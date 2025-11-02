<?php

namespace App\Http\Controllers;

// Import Model yang dibutuhkan
use App\Models\Event;
use App\Models\LmsContent;
use App\Models\Mentor;
use App\Models\Product;
use App\Models\User;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan user yang login
use Illuminate\Support\Facades\Log;   // Untuk logging error
use Carbon\Carbon;                   // Untuk memfilter event berdasarkan waktu

class HomeController extends Controller
{
    /**
     * Middleware untuk memastikan hanya user terotentikasi yang bisa akses.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan dashboard yang sesuai berdasarkan peran user.
     */
    public function index()
    {
        $user = Auth::user();
        $viewData = ['user' => $user];
        if ($user->role === 'admin') {
            // Hitung data agregat untuk dashboard admin
            $viewData['userCount'] = User::where('role', 'user')->count();
            $viewData['mentorCount'] = Mentor::count();
            $viewData['eventCount'] = Event::count();
            $viewData['lmsCount'] = LmsContent::count();
            $viewData['usersWithMentorStatus'] = User::where('role', 'user')
                ->with('mentors') // Eager load relasi mentor
                ->latest('user_id') // Ambil yang terbaru
                ->take(5) // Batasi 5
                ->get();
        }
        // Logika jika user adalah User Biasa
        elseif ($user->role === 'user') {
            $user->loadMissing(['profile.creativeSubSectors', 'mentors']);
            $userSubSectorIds = $user->profile?->creativeSubSectors->pluck('sub_sector_id')->toArray() ?? [];
            $viewData['relatedEvents'] = Event::whereHas('creativeSubSectors', function ($query) use ($userSubSectorIds) {
                if (!empty($userSubSectorIds))
                    $query->whereIn('creative_sub_sectors.sub_sector_id', $userSubSectorIds);
                else
                    $query->whereRaw('1 = 0');
            })
                ->where('start_datetime', '>', Carbon::now())
                ->orderBy('start_datetime', 'asc')
                ->take(5)
                ->get();

            // Ambil LMS terkait
            $viewData['relatedLms'] = LmsContent::whereIn('sub_sector_id', $userSubSectorIds)
                ->latest()->take(5)->get();

            // Ambil Produk terbaru (asumsi tidak difilter)
            $viewData['relatedProducts'] = Product::latest()->take(15)->get();

            $viewData['relatedNews'] = News::latest()->take(5)->get();

            // Ambil Mentor aktif terkait
            $viewData['relatedMentors'] = Mentor::where('status', 'Aktif')
                ->whereHas('creativeSubSectors', function ($query) use ($userSubSectorIds) {
                    if (!empty($userSubSectorIds))
                        $query->whereIn('creative_sub_sectors.sub_sector_id', $userSubSectorIds);
                    else
                        $query->whereRaw('1 = 0');
                })->withCount('users')
                ->orderBy('users_count', 'asc')
                ->get();

            // Ambil mentor user saat ini
            $viewData['currentMentor'] = $user->mentors()->first();
        }
        // Jika role tidak 'admin' atau 'user' (seharusnya tidak terjadi)
        else {
            Log::warning('User role tidak dikenali atau null untuk user ID: ' . $user->user_id);
            // Siapkan data minimal untuk view home fallback
            $viewData += [
                'relatedEvents' => collect(),
                'relatedLms' => collect(),
                'relatedProducts' => collect(),
                'relatedMentors' => collect(),
                'relatedNews' => collect(),
                'currentMentor' => null
            ];
        }

        // Selalu kembalikan view 'home', kirim semua data yang terkumpul
        return view('home', $viewData);
    }

    /**
     * Method untuk menangani pemilihan mentor oleh user.
     */
    public function chooseMentor(Request $request, Mentor $mentor)
    {
        $user = Auth::user();
        if ($user->role !== 'user' || $mentor->status !== 'Aktif' || $user->mentors()->exists()) {
            // PERBAIKAN: Redirect kembali ke halaman sebelumnya
            return redirect()->back()->with('error', 'Gagal memilih mentor. Pastikan Anda belum memiliki mentor.');
        }
        try {
            $user->mentors()->attach($mentor->mentor_id);
            // PERBAIKAN: Redirect kembali ke halaman sebelumnya
            return redirect()->back()->with('success', 'Anda berhasil memilih ' . $mentor->mentor_name . ' sebagai mentor.');
        } catch (\Exception $e) {
            Log::error('Gagal memilih mentor: ' . $e->getMessage());
            // PERBAIKAN: Redirect kembali ke halaman sebelumnya
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memilih mentor.');
        }
    }

    /**
     * Menghapus relasi mentor saat ini dari user.
     */
    public function removeMentor()
    {
        $user = Auth::user();
        if ($user->role !== 'user') {
            // PERBAIKAN: Redirect kembali ke halaman sebelumnya
            return redirect()->back()->with('error', 'Aksi tidak valid.');
        }
        try {
            $user->mentors()->detach();
            // PERBAIKAN: Redirect kembali ke halaman sebelumnya
            return redirect()->back()->with('success', 'Mentor berhasil dihapus. Anda sekarang dapat memilih mentor baru.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus mentor: ' . $e->getMessage());
            // PERBAIKAN: Redirect kembali ke halaman sebelumnya
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus mentor.');
        }
    }
}

