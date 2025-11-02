<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LmsContent;
use App\Models\CreativeSubSector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // <-- Penting untuk file
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // <-- Penting untuk validasi kondisional

class LmsController extends Controller
{
    public function index()
    {
        $articles = LmsContent::where('type', 'article')->latest()->paginate(10, ['*'], 'article_page');
        $books = LmsContent::where('type', 'book')->latest()->paginate(10, ['*'], 'book_page');
        $videos = LmsContent::where('type', 'video')->latest()->paginate(10, ['*'], 'video_page');
        $creativeSubSectors = CreativeSubSector::orderBy('name')->get();
        return view('admin.lms.index', compact('articles', 'books', 'videos', 'creativeSubSectors'));
    }

    public function store(Request $request)
    {
        $baseRules = [
            'content_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:article,book,video',
            'sub_sector_id' => 'required|exists:creative_sub_sectors,sub_sector_id',
            'source_type' => 'required|in:url,file',
        ];

        $sourceRules = [];
        if ($request->input('source_type') === 'url') {
            $sourceRules['source_url'] = 'required|url|max:255';
        } else { // file
            // PERBAIKAN: Validasi file upload
            $sourceRules['source_file'] = 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:5120'; // max 5MB
        }

        $validator = Validator::make($request->all(), array_merge($baseRules, $sourceRules));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $sourcePathOrUrl = null;
        $filePath = null; // Untuk rollback

        try {
            if ($validated['source_type'] === 'url') {
                $sourcePathOrUrl = $validated['source_url'];
            } else { // file
                // PERBAIKAN: Simpan file dan dapatkan path
                $filePath = $request->file('source_file')->store('lms_files', 'public');
                $sourcePathOrUrl = $filePath;
            }

            LmsContent::create([
                'content_title' => $validated['content_title'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'sub_sector_id' => $validated['sub_sector_id'],
                'source' => $sourcePathOrUrl, // Simpan path atau URL
            ]);

            return response()->json(['success' => 'Konten berhasil ditambahkan.']);

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan konten LMS: ' . $e->getMessage());
            if ($filePath && Storage::disk('public')->exists($filePath)) { // Hapus file jika gagal simpan DB
                Storage::disk('public')->delete($filePath);
            }
            return response()->json(['error_message' => 'Gagal menyimpan data. Error: ' . $e->getMessage()], 500);
        }
    }

    public function edit(LmsContent $lmsContent)
    {
        return response()->json($lmsContent);
    }

    public function update(Request $request, LmsContent $lmsContent)
    {
        $baseRules = [
            'content_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:article,book,video',
            'sub_sector_id' => 'required|exists:creative_sub_sectors,sub_sector_id',
            'source_type' => 'required|in:url,file',
        ];

        $sourceRules = [];
        if ($request->input('source_type') === 'url') {
            $sourceRules['source_url'] = 'required|url|max:255';
            $sourceRules['source_file'] = 'nullable'; // File tidak diperlukan
        } else { // file
            $sourceRules['source_url'] = 'nullable'; // URL tidak diperlukan
            // PERBAIKAN: Validasi file hanya jika file baru diupload
            $sourceRules['source_file'] = [
                'nullable', // Boleh null jika tidak ganti file
                'file',
                'mimes:pdf,doc,docx,ppt,pptx',
                'max:5120'
            ];
        }

        $validator = Validator::make($request->all(), array_merge($baseRules, $sourceRules));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $sourcePathOrUrl = $lmsContent->source; // Default ke source lama
        $oldSourcePath = null;
        $newSourcePath = null;
        $sourceWasFile = !filter_var($lmsContent->source, FILTER_VALIDATE_URL);

        if ($sourceWasFile) {
            $oldSourcePath = $lmsContent->source;
        }

        try {
            if ($validated['source_type'] === 'url') {
                $sourcePathOrUrl = $validated['source_url'];
                // Hapus file lama jika ada
                if ($oldSourcePath && Storage::disk('public')->exists($oldSourcePath)) {
                    Storage::disk('public')->delete($oldSourcePath);
                }
            }
            // Hanya proses file jika ADA file baru di request
            elseif ($request->hasFile('source_file')) {
                // PERBAIKAN: Simpan file baru
                $newSourcePath = $request->file('source_file')->store('lms_files', 'public');
                $sourcePathOrUrl = $newSourcePath;
                // Hapus file lama jika ada
                if ($oldSourcePath && Storage::disk('public')->exists($oldSourcePath)) {
                    Storage::disk('public')->delete($oldSourcePath);
                }
            }
            // Jika tipe 'file' tapi TIDAK ada file baru, $sourcePathOrUrl tetap path file lama

            $lmsContent->update([
                'content_title' => $validated['content_title'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'sub_sector_id' => $validated['sub_sector_id'],
                'source' => $sourcePathOrUrl,
            ]);

            return response()->json(['success' => 'Konten berhasil diperbarui.']);

        } catch (\Exception $e) {
            Log::error('Gagal memperbarui konten LMS: ' . $e->getMessage());
            // Hapus file baru jika error update DB
            if ($newSourcePath && Storage::disk('public')->exists($newSourcePath)) {
                Storage::disk('public')->delete($newSourcePath);
            }
            return response()->json(['error_message' => 'Gagal memperbarui data.'], 500);
        }
    }

    public function destroy(LmsContent $lmsContent)
    {
        $sourcePath = $lmsContent->source;
        $sourceWasFile = !filter_var($sourcePath, FILTER_VALIDATE_URL);

        try {
            $lmsContent->delete();
            // Hapus file jika ada
            if ($sourceWasFile && $sourcePath && Storage::disk('public')->exists($sourcePath)) {
                Storage::disk('public')->delete($sourcePath);
            }
            return response()->json(['success' => 'Konten berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus konten LMS: ' . $e->getMessage());
            return response()->json(['error_message' => 'Gagal menghapus data.'], 500);
        }
    }

    public function show(LmsContent $lmsContent)
    {
        // Redirect ke URL atau tampilkan file
        if (filter_var($lmsContent->source, FILTER_VALIDATE_URL)) {
            return redirect($lmsContent->source);
        } elseif ($lmsContent->source && Storage::disk('public')->exists($lmsContent->source)) {
            return response()->file(storage_path('app/public/' . $lmsContent->source));
            // Atau paksa download: return Storage::disk('public')->download($lmsContent->source);
        } else {
            abort(404, 'File atau URL tidak ditemukan.');
        }
    }
}

