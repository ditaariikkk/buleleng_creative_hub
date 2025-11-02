<?php

namespace App\Http\Controllers; // Sesuaikan namespace jika berbeda

use App\Models\CreativeSubSector; // Import model SubSektor
use App\Models\UserNeed;         // Import model Kebutuhan
use App\Models\UserProfile;      // Import model UserProfile
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // Untuk validasi unique


class UserProfileController extends Controller
{
    /**
     * Menampilkan form untuk mengedit/melengkapi profil pengguna.
     */
    public function edit()
    {
        $user = Auth::user();

        $profile = $user->profile()->firstOrCreate(
            ['user_id' => $user->user_id],
            []
        );

        $profile->load(['creativeSubSectors', 'userNeeds']);

        $subSectors = CreativeSubSector::orderBy('name')->get();
        // Asumsi Model UserNeed punya kolom 'need_name'
        $needs = UserNeed::orderBy('need_name')->get();

        return view('user.profile.complete', compact('user', 'profile', 'subSectors', 'needs'));
    }

    /**
     * Memperbarui profil pengguna.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->user_id]);

        $validator = Validator::make($request->all(), [
            'bio' => 'nullable|string|max:1000',
            'phone_number' => ['required', 'string', 'max:20'], // Buat required
            'portofolio_url' => 'nullable|url|max:255',
            'user_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Ganti nama field jika perlu
            'sub_sectors' => 'required|array|min:1',
            // Asumsi PK CreativeSubSector adalah 'sub_sector_id'
            'sub_sectors.*' => 'exists:creative_sub_sectors,sub_sector_id',
            'user_needs' => 'required|array|min:1',
            // Asumsi PK UserNeed adalah 'need_id'
            'user_needs.*' => 'exists:user_needs,need_id',
        ]);

        if ($validator->fails()) {
            // Redirect kembali ke form DENGAN error validasi dan input lama
            return redirect()->back()
                ->withErrors($validator)
                ->withInput(); // Bawa input lama
        }

        $validated = $validator->validated();
        $photoPath = $profile->user_photo; // Simpan path lama

        // Handle upload foto baru
        if ($request->hasFile('user_photo')) {
            try {
                // Simpan foto baru
                $newPhotoPath = $request->file('user_photo')->store('user_photos', 'public');
                // Hapus foto lama jika ada
                if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $newPhotoPath; // Update path foto
            } catch (\Exception $e) {
                Log::error('Gagal upload foto profil: ' . $e->getMessage());
                // PERBAIKAN: Redirect back dengan pesan error spesifik
                return redirect()->back()
                    ->with('profile_update_error', 'Gagal mengunggah foto profil.') // Session key error
                    ->withInput();
            }
        }

        // Hapus data relasi dari array sebelum update profile
        $profileData = $validated;
        unset($profileData['sub_sectors'], $profileData['user_needs']);
        // Pastikan path foto dimasukkan (baik lama atau baru)
        $profileData['user_photo'] = $photoPath;

        try {
            // Update tabel user_profiles
            $profile->update($profileData);

            // Sync relasi pivot
            $profile->creativeSubSectors()->sync($validated['sub_sectors']);
            $profile->userNeeds()->sync($validated['user_needs']);

            // PERBAIKAN: Redirect ke home dengan pesan sukses spesifik
            return redirect()->route('home')->with('profile_update_success', 'Profil berhasil diperbarui!'); // Session key sukses

        } catch (\Exception $e) {
            Log::error('Gagal memperbarui profil: ' . $e->getMessage());
            // PERBAIKAN: Redirect back dengan pesan error spesifik
            return redirect()->back()
                ->with('profile_update_error', 'Terjadi kesalahan saat menyimpan profil.') // Session key error
                ->withInput();
        }
    }
}

