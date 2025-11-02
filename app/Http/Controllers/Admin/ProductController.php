<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Untuk mengelola file foto
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Menampilkan halaman index produk dengan data paginasi.
     */
    public function index()
    {
        $products = Product::latest()->paginate(10); // Ambil 10 produk terbaru per halaman
        return view('admin.products.index', compact('products'));
    }

    /**
     * Menyimpan produk baru dari modal AJAX.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'owner' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi foto
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $photoPath = null;

        // Handle upload foto jika ada
        if ($request->hasFile('photo_path')) {
            try {
                // Simpan di storage/app/public/product_photos
                $photoPath = $request->file('photo_path')->store('product_photos', 'public');
                $validated['photo_path'] = $photoPath; // Masukkan path ke data yang divalidasi
            } catch (\Exception $e) {
                Log::error('Gagal upload foto produk: ' . $e->getMessage());
                return response()->json(['error_message' => 'Gagal mengunggah foto.'], 500);
            }
        }

        try {
            Product::create($validated);
            return response()->json(['success' => 'Produk berhasil ditambahkan.']);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan produk: ' . $e->getMessage());
            // Jika foto sudah terupload tapi DB gagal, hapus fotonya lagi
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            return response()->json(['error_message' => 'Gagal menyimpan data produk.'], 500);
        }
    }

    /**
     * Mengambil data produk untuk modal edit.
     */
    public function edit(Product $product)
    {
        // Route Model Binding otomatis mengambil produk berdasarkan product_id
        return response()->json($product);
    }

    /**
     * Memperbarui produk dari modal AJAX.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'owner' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $oldPhotoPath = $product->photo_path; // Simpan path foto lama

        // Handle upload foto baru jika ada
        if ($request->hasFile('photo_path')) {
            try {
                $photoPath = $request->file('photo_path')->store('product_photos', 'public');
                $validated['photo_path'] = $photoPath;

                // Hapus foto lama jika ada dan berhasil upload foto baru
                if ($oldPhotoPath && Storage::disk('public')->exists($oldPhotoPath)) {
                    Storage::disk('public')->delete($oldPhotoPath);
                }
            } catch (\Exception $e) {
                Log::error('Gagal upload foto produk (update): ' . $e->getMessage());
                return response()->json(['error_message' => 'Gagal mengunggah foto baru.'], 500);
            }
        } else {
            // Jika tidak ada foto baru diupload, JANGAN hapus photo_path lama dari $validated
            unset($validated['photo_path']);
        }


        try {
            $product->update($validated);
            return response()->json(['success' => 'Produk berhasil diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui produk: ' . $e->getMessage());
            // Jika foto baru sudah terupload tapi DB gagal, hapus foto baru
            if (isset($validated['photo_path']) && Storage::disk('public')->exists($validated['photo_path'])) {
                Storage::disk('public')->delete($validated['photo_path']);
            }
            return response()->json(['error_message' => 'Gagal memperbarui data produk.'], 500);
        }
    }

    /**
     * Menghapus produk.
     */
    public function destroy(Product $product)
    {
        $photoPath = $product->photo_path;

        try {
            $product->delete();

            // Hapus foto terkait jika ada
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return response()->json(['success' => 'Produk berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus produk: ' . $e->getMessage());
            return response()->json(['error_message' => 'Gagal menghapus data produk.'], 500);
        }
    }

    /**
     * Menampilkan halaman detail produk (belum dibuat).
     */
    public function show(Product $product)
    {
        // Anda akan membuat view 'show' ini nanti
        return view('admin.products.show', compact('product'));
    }

    /**
     * Menampilkan halaman index berita.
     */
    public function newsIndex()
    {
        $newsItems = News::latest()->paginate(10); // Ambil 10 berita terbaru
        return view('admin.news.index', compact('newsItems')); // Arahkan ke view news
    }

    /**
     * Menyimpan berita baru dari modal AJAX.
     */
    public function newsStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string', // Deskripsi wajib
            'source_url' => 'required|url|max:255',
            'news_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Foto opsional
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $photoPath = null;

        if ($request->hasFile('news_photo')) {
            try {
                $photoPath = $request->file('news_photo')->store('news_photos', 'public');
                $validated['news_photo'] = $photoPath;
            } catch (\Exception $e) {
                Log::error('Gagal upload foto berita: ' . $e->getMessage());
                return response()->json(['error_message' => 'Gagal mengunggah foto.'], 500);
            }
        }

        try {
            News::create($validated);
            return response()->json(['success' => 'Berita berhasil ditambahkan.']);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan berita: ' . $e->getMessage());
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            return response()->json(['error_message' => 'Gagal menyimpan data berita.'], 500);
        }
    }

    /**
     * Mengambil data berita untuk modal edit.
     * Menggunakan {news} sebagai parameter route model binding
     */
    public function newsEdit(News $news)
    {
        return response()->json($news);
    }

    /**
     * Memperbarui berita dari modal AJAX.
     * Menggunakan {news} sebagai parameter route model binding
     */
    public function newsUpdate(Request $request, News $news)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'source_url' => 'required|url|max:255',
            'news_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $oldPhotoPath = $news->news_photo;
        $newPhotoPath = null; // Untuk rollback

        if ($request->hasFile('news_photo')) {
            try {
                $newPhotoPath = $request->file('news_photo')->store('news_photos', 'public');
                $validated['news_photo'] = $newPhotoPath;

                if ($oldPhotoPath && Storage::disk('public')->exists($oldPhotoPath)) {
                    Storage::disk('public')->delete($oldPhotoPath);
                }
            } catch (\Exception $e) {
                Log::error('Gagal upload foto berita (update): ' . $e->getMessage());
                return response()->json(['error_message' => 'Gagal mengunggah foto baru.'], 500);
            }
        } else {
            unset($validated['news_photo']); // Jangan update path jika tidak ada file baru
        }

        try {
            $news->update($validated);
            return response()->json(['success' => 'Berita berhasil diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui berita: ' . $e->getMessage());
            if ($newPhotoPath && Storage::disk('public')->exists($newPhotoPath)) {
                Storage::disk('public')->delete($newPhotoPath); // Hapus foto baru jika update gagal
            }
            return response()->json(['error_message' => 'Gagal memperbarui data berita.'], 500);
        }
    }

    /**
     * Menghapus berita.
     * Menggunakan {news} sebagai parameter route model binding
     */
    public function newsDestroy(News $news)
    {
        $photoPath = $news->news_photo;

        try {
            $news->delete();
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            return response()->json(['success' => 'Berita berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus berita: ' . $e->getMessage());
            return response()->json(['error_message' => 'Gagal menghapus data berita.'], 500);
        }
    }

    public function newsShow(News $news)
    {

        // Pilihan 2: Buat view show terpisah
        return view('admin.news.show', compact('news'));
    }

}


