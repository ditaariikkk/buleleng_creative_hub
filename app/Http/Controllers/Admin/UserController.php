<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // Menggunakan model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Menampilkan halaman index daftar user (role 'user').
     */
    public function index()
    {
        // Eager load 'mentors' DAN 'profile'
        $users = User::where('role', 'user')
            ->with(['mentors', 'profile']) // Muat kedua relasi dasar
            ->latest('user_id')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan halaman detail user.
     */
    public function show(User $user) // <-- TAMBAHKAN METHOD INI
    {
        // Pastikan hanya user biasa yang bisa dilihat detailnya di sini
        if ($user->role !== 'user') {
            abort(404);
        }

        // Eager load relasi yang lebih dalam untuk halaman show
        $user->load([
            'mentors',
            'profile.creativeSubSectors', // Muat sub sektor melalui profile
            'profile.userNeeds'           // Muat user needs melalui profile
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Menghapus user (jika diperlukan).
     */
    public function destroy(User $user)
    {
        if ($user->role !== 'user') {
            return response()->json(['error_message' => 'Hanya user yang dapat dihapus melalui endpoint ini.'], 403);
        }

        try {
            $user->mentors()->detach();
            if ($user->profile) {
                // Hapus relasi pivot profile SEBELUM menghapus profile
                $user->profile->creativeSubSectors()->detach();
                $user->profile->userNeeds()->detach();
                $user->profile->delete();
            }

            $user->delete();
            return response()->json(['success' => 'User berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus user: ' . $e->getMessage());
            return response()->json(['error_message' => 'Gagal menghapus data user.'], 500);
        }
    }

}

