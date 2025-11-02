<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\CreativeSubSector;
use App\Models\UserNeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MentorController extends Controller
{
    /**
     * Menampilkan halaman daftar mentor.
     */
    public function index()
    {
        $mentors = Mentor::latest()->paginate(10);
        $subSectors = CreativeSubSector::all();
        $userNeeds = UserNeed::all();
        return view('admin.mentors.index', compact('mentors', 'subSectors', 'userNeeds'));
    }

    /**
     * Menyimpan mentor baru dari permintaan AJAX.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mentor_name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'expertise_summary' => 'nullable|string',
            'mentor_contact' => 'nullable|string|max:255',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'photo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sub_sectors' => 'nullable|array',
            // Validasi harus ke PK tabel creative_sub_sectors, yaitu sub_sector_id
            'sub_sectors.*' => 'exists:creative_sub_sectors,sub_sector_id',
            'user_needs' => 'nullable|array',
            // Validasi harus ke PK tabel user_needs, yaitu need_id
            'user_needs.*' => 'exists:user_needs,need_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        if ($request->hasFile('photo_path')) {
            $validated['photo_path'] = $request->file('photo_path')->store('mentor_photos', 'public');
        }

        $mentor = Mentor::create($validated);

        if (isset($validated['sub_sectors'])) {
            $mentor->creativeSubSectors()->sync($validated['sub_sectors']);
        }

        if (isset($validated['user_needs'])) {
            $mentor->userNeeds()->sync($validated['user_needs']);
        }

        return response()->json(['success' => 'Mentor berhasil ditambahkan.']);
    }

    /**
     * Mengambil data mentor untuk form edit via AJAX.
     */
    public function edit(Mentor $mentor)
    {
        $mentor->load('creativeSubSectors', 'userNeeds');
        return response()->json($mentor);
    }

    /**
     * Memperbarui data mentor dari permintaan AJAX.
     */
    public function update(Request $request, Mentor $mentor)
    {
        $validator = Validator::make($request->all(), [
            'mentor_name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'expertise_summary' => 'nullable|string',
            'mentor_contact' => 'nullable|string|max:255',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'photo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sub_sectors' => 'nullable|array',
            // Validasi harus ke PK tabel creative_sub_sectors, yaitu sub_sector_id
            'sub_sectors.*' => 'exists:creative_sub_sectors,sub_sector_id',
            'user_needs' => 'nullable|array',
            // Validasi harus ke PK tabel user_needs, yaitu need_id
            'user_needs.*' => 'exists:user_needs,need_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        if ($request->hasFile('photo_path')) {
            if ($mentor->photo_path) {
                Storage::disk('public')->delete($mentor->photo_path);
            }
            $validated['photo_path'] = $request->file('photo_path')->store('mentor_photos', 'public');
        }

        $mentor->update($validated);

        $mentor->creativeSubSectors()->sync($validated['sub_sectors'] ?? []);
        $mentor->userNeeds()->sync($validated['user_needs'] ?? []);

        return response()->json(['success' => 'Mentor berhasil diperbarui.']);
    }

    /**
     * Menampilkan detail spesifik mentor.
     */
    public function show(Mentor $mentor)
    {
        $mentor->load('creativeSubSectors', 'userNeeds');
        return view('admin.mentors.show', compact('mentor'));
    }

    /**
     * Menghapus mentor dari permintaan AJAX.
     */
    public function destroy(Mentor $mentor)
    {
        if ($mentor->photo_path) {
            Storage::disk('public')->delete($mentor->photo_path);
        }
        // Hapus relasi pivot sebelum menghapus mentor
        $mentor->creativeSubSectors()->detach();
        $mentor->userNeeds()->detach();

        $mentor->delete();

        return response()->json(['success' => 'Mentor berhasil dihapus.']);
    }
}

