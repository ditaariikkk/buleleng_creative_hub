<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // Menggunakan model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Untuk hashing password
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password; // Aturan validasi password

class AdminUserController extends Controller
{
    /**
     * Menampilkan halaman index daftar admin.
     */
    public function index()
    {
        // Ambil semua user (atau filter berdasarkan role jika ada)
        // PERBAIKAN: Filter hanya user dengan role 'admin'
        $admins = User::where('role', 'admin')->latest()->paginate(10);
        // PERBAIKAN: Gunakan compact('admins') bukan compact('admin')
        return view('admin.index', compact('admins')); // Arahkan ke view admin.index 
    }

    /**
     * Menyimpan admin baru dari modal AJAX.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email', // Pastikan email unik di tabel users
            'password' => ['required', 'confirmed', Password::min(8)], // Password wajib, harus cocok dengan konfirmasi, minimal 8 karakter
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password, // Model User akan otomatis hash karena $casts
                'role' => 'admin', // <-- PERBAIKAN: Set role 'admin' secara default
            ]);
            return response()->json(['success' => 'Admin berhasil ditambahkan.']);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan admin: ' . $e->getMessage());
            // Berikan pesan error lebih detail jika mungkin MassAssignmentException
            if (str_contains($e->getMessage(), 'must be fillable')) {
                return response()->json(['error_message' => 'Gagal menyimpan data admin. Pastikan field `role` ada di $fillable model User.'], 500);
            }
            return response()->json(['error_message' => 'Gagal menyimpan data admin.'], 500);
        }
    }

    /**
     * Mengambil data admin untuk modal edit.
     */
    public function edit(User $admin) // Menggunakan Route Model Binding dengan parameter 'admin'
    {
        // Pastikan hanya bisa mengedit admin
        if ($admin->role !== 'admin') {
            abort(404); // Atau kirim response error
        }
        return response()->json($admin);
    }

    /**
     * Memperbarui admin dari modal AJAX.
     */
    public function update(Request $request, User $admin) // Menggunakan Route Model Binding dengan parameter 'admin'
    {
        // Pastikan hanya bisa mengupdate admin
        if ($admin->role !== 'admin') {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            // PERBAIKAN: Tambahkan parameter ke-4 ('user_id') pada aturan unique
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->user_id . ',user_id',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = $request->password;
            }

            $admin->update($updateData);

            return response()->json(['success' => 'Data admin berhasil diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui admin: ' . $e->getMessage());
            return response()->json(['error_message' => 'Gagal memperbarui data admin.'], 500);
        }
    }

    /**
     * Menghapus admin.
     */
    public function destroy(User $admin) // Menggunakan Route Model Binding dengan parameter 'admin'
    {
        // Pastikan hanya bisa menghapus admin
        if ($admin->role !== 'admin') {
            abort(404);
        }

        if ($admin->id === auth()->id()) { // Perhatikan: auth()->id() biasanya mengembalikan PK default ('id')
            // Solusi Cepat: Ambil user_id dari user yang terotentikasi
            $currentUserId = auth()->user()->user_id; // Asumsi model User sudah benar
            if ($admin->user_id === $currentUserId) {
                return response()->json(['error_message' => 'Anda tidak dapat menghapus akun Anda sendiri.'], 403);
            }
        }

        try {
            $admin->delete();
            return response()->json(['success' => 'Admin berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus admin: ' . $e->getMessage());
            return response()->json(['error_message' => 'Gagal menghapus data admin.'], 500);
        }
    }
}

