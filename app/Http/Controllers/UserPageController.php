<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\LmsContent;
use App\Models\Mentor;
use App\Models\News;
use App\Models\Product;
use App\Models\Venue;
use App\Models\User;
use App\Models\CreativeSubSector;
use App\Models\UserNeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class UserPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // --- PRODUCT ---

    public function productsIndex()
    {
        $products = Product::latest('product_id')->paginate(12);
        return view('user.products.index', compact('products'));
    }

    public function productShow(Product $product)
    {
        return response()->json($product);
    }

    // --- MENTOR ---

    public function mentorsIndex()
    {
        $user = Auth::user();
        $user->loadMissing(['profile.creativeSubSectors', 'mentors']);

        // 1. Cek mentor saat ini
        $currentMentor = $user->mentors()->first();

        // 2. Siapkan data rekomendasi (hanya jika user belum punya mentor)
        $relatedMentors = collect(); // Default koleksi kosong
        if (!$currentMentor) {
            $userSubSectorIds = $user->profile?->creativeSubSectors->pluck('sub_sector_id')->toArray() ?? [];
            $relatedMentors = Mentor::where('status', 'Aktif')
                ->whereHas('creativeSubSectors', function ($query) use ($userSubSectorIds) {
                    if (!empty($userSubSectorIds))
                        $query->whereIn('creative_sub_sectors.sub_sector_id', $userSubSectorIds);
                    else
                        $query->whereRaw('1 = 0');
                })->withCount('users')
                ->orderBy('users_count', 'asc')
                ->get();
        }

        return view('user.mentors.index', compact('user', 'currentMentor', 'relatedMentors'));
    }

    public function mentorShow(Mentor $mentor)
    {
        // Tampilkan profil mentor publik
        return view('user.mentors.show', compact('mentor'));
    }

    // --- EVENT ---

    /**
     * Menampilkan halaman Daftar Event (user.events.index) dengan filter status.
     */
    public function eventsIndex(Request $request) // <-- Tambahkan Request
    {
        $user = Auth::user();
        $user->loadMissing(['profile.creativeSubSectors']);
        $userSubSectorIds = $user->profile?->creativeSubSectors->pluck('sub_sector_id')->toArray() ?? [];

        // Tentukan status filter
        $validStatuses = ['Akan Datang', 'Sedang Berlangsung', 'Telah Berakhir'];
        $currentStatus = $request->query('status', 'Akan Datang'); // Default ke 'upcoming'
        if (!in_array($currentStatus, $validStatuses)) {
            $currentStatus = 'Akan Datang';
        }

        // Mulai query dasar (hanya event yang relevan dengan sub sektor user)
        $eventsQuery = Event::whereHas('creativeSubSectors', function ($query) use ($userSubSectorIds) {
            if (!empty($userSubSectorIds)) {
                $query->whereIn('creative_sub_sectors.sub_sector_id', $userSubSectorIds);
            } else {
                $query->whereRaw('1 = 0'); // Tidak ada sub sektor, tidak ada event
            }
        });

        // Terapkan filter status
        $now = Carbon::now();
        if ($currentStatus == 'Akan Datang') {
            $eventsQuery->where('start_datetime', '>', $now)
                ->orderBy('start_datetime', 'asc'); // Akan datang, urutkan dari terdekat
        } elseif ($currentStatus == 'Sedang Berlangsung') {
            $eventsQuery->where('start_datetime', '<=', $now)
                ->where('end_datetime', '>=', $now)
                ->orderBy('start_datetime', 'desc'); // Sedang berlangsung, urutkan dari yang terbaru mulai
        } elseif ($currentStatus == 'Telah Berakhir') {
            $eventsQuery->where('end_datetime', '<', $now)
                ->orderBy('end_datetime', 'desc'); // Selesai, urutkan dari yang terbaru selesai
        }

        $events = $eventsQuery->paginate(9); // Paginasi 9 untuk grid 3 kolom

        return view('user.events.index', compact('events', 'currentStatus'));
    }

    /**
     * Menampilkan halaman detail event (user.events.show).
     */
    public function eventShow(Event $event)
    {
        $event->load(['venue', 'creativeSubSectors']);
        return view('user.events.show', compact('event'));
    }

    // --- LMS ---

    /**
     * Menampilkan halaman Media Pembelajaran (user.lms.index) dengan filter.
     */
    public function lmsIndex(Request $request) // <-- Tambahkan Request
    {
        $user = Auth::user();
        $user->loadMissing(['profile.creativeSubSectors']);
        $userSubSectorIds = $user->profile?->creativeSubSectors->pluck('sub_sector_id')->toArray() ?? [];

        // Tipe yang valid (sesuai ENUM di database Anda)
        $validTypes = ['article', 'book', 'video'];
        $currentType = $request->query('type'); // Ambil ?type= dari URL

        // Mulai query
        $lmsQuery = LmsContent::whereIn('sub_sector_id', $userSubSectorIds);

        // Terapkan filter tipe jika valid
        if ($currentType && in_array($currentType, $validTypes)) {
            $lmsQuery->where('type', $currentType);
        } else {
            $currentType = 'all'; // Default jika tidak ada filter atau filter tidak valid
        }

        $lmsItems = $lmsQuery->latest()->paginate(12);

        // Kirim item dan tipe saat ini ke view
        return view('user.lms.index', compact('lmsItems', 'currentType'));
    }

    /**
     * Menampilkan/mengarahkan ke detail LMS (user.lms.show).
     */
    public function lmsShow(LmsContent $lmsContent)
    {
        // Cek apakah user punya akses (berdasarkan sub sektor) - Opsional
        $user = Auth::user();
        $userSubSectorIds = $user->profile?->creativeSubSectors->pluck('sub_sector_id')->toArray() ?? [];

        if (!in_array($lmsContent->sub_sector_id, $userSubSectorIds) && $user->role !== 'admin') {
            // Jika tidak punya akses, kembalikan ke index
            return redirect()->route('user.lms.index')->with('error', 'Anda tidak memiliki akses ke konten tersebut.');
        }

        // Logika redirect/tampil file (seperti di LmsController)
        if (filter_var($lmsContent->source, FILTER_VALIDATE_URL)) {
            return redirect($lmsContent->source);
        } elseif ($lmsContent->source && Storage::disk('public')->exists($lmsContent->source)) {
            return response()->file(storage_path('app/public/' . $lmsContent->source));
        }

        abort(404, 'File atau URL tidak ditemukan.');
    }

    // --- NEWS ---
    public function newsIndex()
    {
        $newsItems = News::latest()->paginate(9); // Paginasi 9 untuk grid 3 kolom
        return view('user.news.index', compact('newsItems'));
    }
    public function newsShow(News $news)
    {
        return view('user.news.show', compact('news'));
    }


    // --- VENUE ---
    public function venuesIndex()
    {
        $venues = Venue::latest()->paginate(12);
        return view('user.venues.index', compact('venues'));
    }

    public function venueShow(Venue $venue)
    {
        return response()->json($venue); // Mengembalikan JSON untuk modal
    }

    // --- USER PROFILE ---

    public function profileIndex()
    {
        $user = Auth::user();
        // Muat semua relasi yang diperlukan untuk tampilan
        $user->loadMissing(['profile.creativeSubSectors', 'profile.userNeeds', 'mentors']);

        // Ambil profile, buat jika tidak ada (meskipun seharusnya sudah ada dari 'complete')
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->user_id]);

        // Ambil data master untuk modal edit
        $subSectors = CreativeSubSector::orderBy('name')->get();
        $needs = UserNeed::orderBy('need_name')->get();

        return view('user.profile.index', compact('user', 'profile', 'subSectors', 'needs'));
    }

    /**
     * Update Akun User (Nama, Email, Password)
     */
    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->user_id, 'user_id')],
            'password' => ['nullable', 'string', Password::min(8), 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'updateAccount')->withInput();
        }

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }
            $user->update($updateData);
            return redirect()->route('user.profile.index')->with('success', 'Akun berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal update akun: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui akun.');
        }
    }

    /**
     * Update Detail Profil (Bio, Telepon, Foto, Portofolio)
     */
    public function updateDetails(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->user_id]);

        $validator = Validator::make($request->all(), [
            'bio' => 'nullable|string|max:1000',
            'phone_number' => 'required|string|max:20',
            'portofolio_url' => 'nullable|url|max:255',
            'user_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'updateDetails')->withInput();
        }

        $validated = $validator->validated();
        $photoPath = $profile->user_photo;

        if ($request->hasFile('user_photo')) {
            try {
                $newPhotoPath = $request->file('user_photo')->store('user_photos', 'public');
                if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $newPhotoPath;
            } catch (\Exception $e) {
                Log::error('Gagal upload foto profil: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal mengunggah foto profil.');
            }
        }

        try {
            $profile->update([
                'bio' => $validated['bio'],
                'phone_number' => $validated['phone_number'],
                'portofolio_url' => $validated['portofolio_url'] ?? null,
                'user_photo' => $photoPath,
            ]);
            return redirect()->route('user.profile.index')->with('success', 'Detail profil berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal update detail profil: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui detail profil.');
        }
    }

    /**
     * Update Minat (Sub Sektor & Kebutuhan)
     */
    public function updateInterests(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->user_id]);

        $validator = Validator::make($request->all(), [
            'sub_sectors' => 'required|array|min:1',
            'sub_sectors.*' => 'exists:creative_sub_sectors,sub_sector_id',
            'user_needs' => 'required|array|min:1',
            'user_needs.*' => 'exists:user_needs,need_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'updateInterests')->withInput();
        }

        try {
            $profile->creativeSubSectors()->sync($request->sub_sectors);
            $profile->userNeeds()->sync($request->user_needs);
            return redirect()->route('user.profile.index')->with('success', 'Minat & Kebutuhan berhasil diperbarui. Rekomendasi Anda akan disesuaikan.');
        } catch (\Exception $e) {
            Log::error('Gagal update minat: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui minat.');
        }
    }
}

