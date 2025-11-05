<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Collaboration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CollaborationController extends Controller
{
    /**
     * Menampilkan halaman utama kolaborasi.
     */
    public function index()
    {
        $currentUserId = Auth::id();

        // 1. Ambil user lain (TAB 1: DISCOVER)
        $usersToDiscover = User::where('user_id', '!=', $currentUserId)
            ->where('role', 'user')
            ->with(['profile.creativeSubSectors'])
            ->whereDoesntHave('collaborationsAsRequester', function ($query) use ($currentUserId) {
                $query->where('recipient_id', $currentUserId);
            })
            ->whereDoesntHave('collaborationsAsRecipient', function ($query) use ($currentUserId) {
                $query->where('requester_id', $currentUserId);
            })
            ->paginate(12);

        // [PERBAIKAN] 2. Ambil kolaborasi yang SUDAH DITERIMA (TAB 2: KOLABORASI SAYA)
        // Ini adalah query yang hilang.
        // Ambil di mana status = 'accepted' DAN saya adalah requester ATAU recipient
        $acceptedCollaborations = Collaboration::where('status', 'accepted')
            ->where(function ($query) use ($currentUserId) {
                $query->where('requester_id', $currentUserId)
                    ->orWhere('recipient_id', $currentUserId);
            })
            ->with(['requester.profile.creativeSubSectors', 'recipient.profile.creativeSubSectors'])
            ->get();

        // [PERBAIKAN] 3. Ambil kolaborasi yang SAYA AJUKAN (TAB 3: PERMINTAAN TERKIRIM)
        // Tambahkan filter ->where('status', 'pending')
        $myCollaborations = Collaboration::where('requester_id', $currentUserId)
            ->where('status', 'pending') // Hanya tampilkan yang masih pending
            ->with(['recipient.profile.creativeSubSectors'])
            ->get();

        // 4. Ambil permintaan yang MASUK KE SAYA (TAB 3: PERMINTAAN MASUK)
        // Query ini sudah benar
        $incomingRequests = Collaboration::where('recipient_id', $currentUserId)
            ->where('status', 'pending')
            ->with(['requester.profile.creativeSubSectors'])
            ->get();

        // Kirim semua data ini ke View
        return view('user.collaboration.index', [
            'usersToDiscover' => $usersToDiscover,
            'acceptedCollaborations' => $acceptedCollaborations, // [BARU] Kirim data Tab 2
            'myCollaborations' => $myCollaborations, // [TERBARU] Data Tab 3 (Terkirim)
            'incomingRequests' => $incomingRequests, // Data Tab 3 (Masuk)
        ]);
    }

    /**
     * Mengambil detail user untuk modal AJAX.
     */
    public function showUser(User $user)
    {
        // [PERBAIKAN] Load relasi 'creativeSubSectors'
        $user->load(['profile.creativeSubSectors']);



        // Kembalikan data sebagai JSON yang rapi
        return response()->json([
            'id' => $user->user_id,
            'name' => $user->name,
            // [PERBAIKAN] Ganti 'photo_path' ke 'user_photo'
            'photo_url' => $user->profile->user_photo ?? null,
            'business_name' => $user->profile->business_name ?? 'Nama Usaha Belum Diisi',
            // [PERBAIKAN] Ambil sub sektor pertama dari koleksi (karena ManyToMany)
            'sub_sector' => $user->profile->creativeSubSectors->first()->name ?? 'Sub Sektor Belum Diisi',
            'description' => $user->profile->bio ?? 'Deskripsi pengguna tidak tersedia.',
        ]);
    }

    /**
     * Menyimpan permintaan kolaborasi baru.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'recipient_id' => 'required|exists:users,user_id',
        ]);

        $requesterId = Auth::id();
        $recipientId = $request->recipient_id;

        // 2. Cek role
        $recipientUser = User::find($recipientId);
        if ($recipientUser && $recipientUser->role == 'admin') {
            return redirect()->route('collaboration.index')->withErrors('Anda tidak dapat berkolaborasi dengan Admin.');
        }

        // 3. Cek diri sendiri
        if ($requesterId == $recipientId) {
            return redirect()->route('collaboration.index')->withErrors('Anda tidak dapat berkolaborasi dengan diri sendiri.');
        }

        // 4. Cek duplikasi
        $existing = Collaboration::where(function ($query) use ($requesterId, $recipientId) {
            $query->where('requester_id', $requesterId)
                ->where('recipient_id', $recipientId);
        })->orWhere(function ($query) use ($requesterId, $recipientId) {
            $query->where('requester_id', $recipientId)
                ->where('recipient_id', $requesterId);
        })->first();

        if ($existing) {
            return redirect()->route('collaboration.index')->withErrors('Anda sudah memiliki relasi kolaborasi dengan pengguna ini.');
        }

        // 5. Buat kolaborasi
        Collaboration::create([
            'requester_id' => $requesterId,
            'recipient_id' => $recipientId,
            'status' => 'pending',
        ]);

        return redirect()->route('collaboration.index')->with('success', 'Permintaan kolaborasi berhasil dikirim!');
    }

    /**
     * Menerima permintaan kolaborasi.
     */
    public function accept(Collaboration $collaboration)
    {
        // Kode ini sudah benar
        if ($collaboration->recipient_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }
        $collaboration->update(['status' => 'accepted']);
        return redirect()->route('collaboration.index')->with('success', 'Kolaborasi diterima!');
    }

    /**
     * Menolak permintaan kolaborasi.
     */
    public function reject(Collaboration $collaboration)
    {
        // Kode ini sudah benar
        if ($collaboration->recipient_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }
        $collaboration->update(['status' => 'rejected']);
        return redirect()->route('collaboration.index')->with('success', 'Kolaborasi ditolak.');
    }
}