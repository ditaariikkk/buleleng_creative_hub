<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // <-- Import Log
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator; // <-- Import Validator

class VenueController extends Controller
{
    /**
     * Menampilkan halaman index (Tampilan Blade Penuh)
     */
    public function index()
    {
        $venues = Venue::latest()->paginate(10);
        return view('admin.venues.index', compact('venues'));
    }

    /**
     * Menyimpan venue baru dari MODAL AJAX
     */
    public function store(Request $request)
    {
        // Validasi disesuaikan dengan form modal
        $validator = Validator::make($request->all(), [
            'venue_name' => 'required|string|max:255|unique:venues,venue_name',
            'address' => 'required|string',
            'capacity' => 'nullable|integer|min:1',
            'contact' => 'required|string|max:255', // Ganti dari contact_email
            'owner' => 'nullable|string|max:255',
            'photo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            // PERBAIKAN: Kembalikan error validasi sebagai JSON
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $photoPath = null;

        if ($request->hasFile('photo_path')) {
            try {
                $photoPath = $request->file('photo_path')->store('venue_photos', 'public');
                $validated['photo_path'] = $photoPath;
            } catch (\Exception $e) {
                Log::error('Gagal upload foto venue: ' . $e->getMessage());
                // PERBAIKAN: Kembalikan error sebagai JSON
                return response()->json(['error_message' => 'Gagal mengunggah foto.'], 500);
            }
        }

        try {
            Venue::create($validated);
            // PERBAIKAN: Kembalikan sukses sebagai JSON
            return response()->json(['success' => 'Venue berhasil ditambahkan.']);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan venue: ' . $e->getMessage());
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            // PERBAIKAN: Kembalikan error sebagai JSON
            return response()->json(['error_message' => 'Gagal menyimpan data venue.'], 500);
        }
    }

    /**
     * Menampilkan halaman detail (Tampilan Blade Penuh)
     */
    public function show(Venue $venue)
    {
        return view('admin.venues.show', compact('venue'));
    }

    /**
     * Mengambil data venue untuk MODAL AJAX
     */
    public function edit(Venue $venue)
    {
        // PERBAIKAN: Kembalikan data venue sebagai JSON
        return response()->json($venue);
    }

    /**
     * Memperbarui venue dari MODAL AJAX
     */
    public function update(Request $request, Venue $venue)
    {
        $validator = Validator::make($request->all(), [
            'venue_name' => 'required|string|max:255|unique:venues,venue_name,' . $venue->venue_id . ',venue_id', // Abaikan ID saat ini
            'address' => 'required|string',
            'capacity' => 'nullable|integer|min:1',
            'contact' => 'required|string|max:255', // Ganti dari contact_email
            'owner' => 'nullable|string|max:255',
            'photo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            // PERBAIKAN: Kembalikan error validasi sebagai JSON
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $oldPhotoPath = $venue->photo_path;
        $newPhotoPath = null; // Untuk rollback

        if ($request->hasFile('photo_path')) {
            try {
                $newPhotoPath = $request->file('photo_path')->store('venue_photos', 'public');
                $validated['photo_path'] = $newPhotoPath;
                if ($oldPhotoPath && Storage::disk('public')->exists($oldPhotoPath)) {
                    Storage::disk('public')->delete($oldPhotoPath);
                }
            } catch (\Exception $e) {
                Log::error('Gagal upload foto venue (update): ' . $e->getMessage());
                // PERBAIKAN: Kembalikan error sebagai JSON
                return response()->json(['error_message' => 'Gagal mengunggah foto baru.'], 500);
            }
        } else {
            unset($validated['photo_path']); // Jangan update path jika tidak ada file baru
        }

        try {
            $venue->update($validated);
            // PERBAIKAN: Kembalikan sukses sebagai JSON
            return response()->json(['success' => 'Data venue berhasil diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui venue: ' . $e->getMessage());
            if ($newPhotoPath && Storage::disk('public')->exists($newPhotoPath)) {
                Storage::disk('public')->delete($newPhotoPath);
            }
            // PERBAIKAN: Kembalikan error sebagai JSON
            return response()->json(['error_message' => 'Gagal memperbarui data venue.'], 500);
        }
    }

    /**
     * Menghapus venue dari MODAL AJAX
     */
    public function destroy(Venue $venue)
    {
        $photoPath = $venue->photo_path;

        try {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            $venue->delete();
            // PERBAIKAN: Kembalikan sukses sebagai JSON
            return response()->json(['success' => 'Venue berhasil dihapus.']);

        } catch (\Illuminate\Database\QueryException $e) {
            // Error jika venue masih dipakai (on delete restrict)
            Log::error('Gagal hapus venue (Constraint): ' . $e->getMessage());
            // PERBAIKAN: Kembalikan error sebagai JSON
            return response()->json(['error_message' => 'Venue tidak dapat dihapus karena masih digunakan oleh event.'], 500);
        } catch (\Exception $e) {
            Log::error('Gagal hapus venue (Lainnya): ' . $e->getMessage());
            return response()->json(['error_message' => 'Gagal menghapus data venue.'], 500);
        }
    }
}

