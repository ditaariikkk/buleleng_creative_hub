@extends('adminlte::page')

@section('title', 'Daftar Produk Etalase - Buleleng Creative Hub')

@section('meta_tags')
{{-- Token CSRF untuk AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Daftar Produk Etalase</li>
        </ol>
    </div>
    <div>
        <button type="button" class="btn btn-primary" id="btn-add-product">
            <i class="fas fa-plus-circle"></i> Tambah Produk
        </button>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-end">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" id="productSearchInput" class="form-control" placeholder="Cari produk...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap" id="productTable">
            <thead>
                <tr>
                    <th style="width: 50px" class="text-center">No</th>
                    <th style="width: 100px" class="text-center">Foto</th>
                    <th class="text-center">Nama Produk</th>
                    <th class="text-center">Owner</th>
                    <th style="width: 200px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="text-center" id="row-product-{{ $product->product_id }}">
                        <td>{{ $loop->iteration + $products->firstItem() - 1 }}</td>
                        <td>
                            {{-- Tampilkan foto jika ada, jika tidak, tampilkan placeholder --}}
                            <img src="{{ $product->photo_path ? asset('storage/' . $product->photo_path) : asset('img/placeholder.png') }}"
                                alt="Foto {{ $product->product_name }}"
                                style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px;"
                                onerror="this.onerror=null;this.src='{{ asset('img/placeholder.png') }}';"> {{-- Fallback
                            jika link foto rusak --}}
                        </td>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->owner }}</td>
                        <td class="text-center">
                            {{-- Link ke halaman show (jika sudah dibuat) --}}
                            <a href="{{ route('admin.products.show', $product->product_id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                            <button type="button" class="btn btn-sm btn-warning edit-btn"
                                data-id="{{ $product->product_id }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-btn"
                                data-id="{{ $product->product_id }}">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Data produk belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Tampilkan link paginasi jika ada --}}
    @if($products->hasPages())
        <div class="card-footer">
            {{ $products->links() }}
        </div>
    @endif
</div>

{{-- Modal Tambah/Edit Produk --}}
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Form Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- Penting: enctype="multipart/form-data" untuk upload file --}}
            <form id="productForm" name="productForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="product_id" name="product_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_name">Nama Produk</label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="owner">Owner</label>
                                <input type="text" class="form-control" id="owner" name="owner" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact">Kontak (Email/Telepon)</label>
                                <input type="text" class="form-control" id="contact" name="contact" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">Alamat</label>
                                <input type="text" class="form-control" id="address" name="address" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="photo_path">Unggah Foto Produk</label>
                        <input type="file" class="form-control-file" id="photo_path" name="photo_path" accept="image/*">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto. Maksimal
                            2MB.</small>
                        {{-- Placeholder untuk menampilkan foto saat edit --}}
                        <img id="currentPhoto" src="#" alt="Foto Saat Ini"
                            style="max-width: 150px; margin-top: 10px; display: none;" />
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-product">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // --- Setup CSRF Token ---
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // --- Logika Pencarian ---
        $('#productSearchInput').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $("#productTable tbody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // --- Logika Modal ---

        // Tampilkan modal untuk Tambah Produk
        $('#btn-add-product').on('click', function () {
            $('#productForm').trigger('reset');
            $('#product_id').val('');
            $('#productModalLabel').text('Tambah Produk Baru');
            $('#currentPhoto').hide().attr('src', '#'); // Sembunyikan placeholder foto
            $('#productModal').modal('show');
        });

        // Kirim data (Simpan atau Update)
        $('#productForm').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this); // Gunakan FormData untuk file
            var productId = $('#product_id').val();
            var url;
            var methodType;

            // Tambahkan _method PUT secara manual karena FormData AJAX tidak otomatis
            if (productId) {
                url = "{{ url('admin/products') }}/" + productId;
                formData.append('_method', 'PUT'); // Method spoofing
                methodType = "POST"; // Tetap POST
            } else {
                url = "{{ route('admin.products.store') }}";
                methodType = "POST";
            }

            $.ajax({
                url: url,
                type: methodType,
                data: formData,
                contentType: false, // Penting untuk FormData
                processData: false, // Penting untuk FormData
                success: function (response) {
                    $('#productModal').modal('hide');
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.success,
                        type: 'success'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    let errorString = '<ul>';
                    if (xhr.status == 422) {
                        $.each(xhr.responseJSON.errors, function (key, value) {
                            errorString += '<li>' + value + '</li>';
                        });
                    } else if (xhr.responseJSON && xhr.responseJSON.error_message) {
                        errorString += '<li>' + xhr.responseJSON.error_message + '</li>';
                    } else {
                        errorString += '<li>Terjadi error di server (' + xhr.status + '). Silakan coba lagi.</li>';
                        console.error('Error:', xhr.responseText);
                    }
                    errorString += '</ul>';
                    Swal.fire({
                        title: 'Data Tidak Valid!',
                        html: errorString,
                        type: 'error'
                    });
                }
            });
        });

        // --- Logika untuk Edit ---
        $('body').on('click', '.edit-btn', function () {
            var productId = $(this).attr('data-id');

            if (!productId) {
                Swal.fire('Error!', 'Tidak dapat menemukan ID Produk.', 'error');
                return;
            }

            var url = "{{ url('admin/products') }}/" + productId + "/edit";

            $.get(url, function (data) {
                $('#productModalLabel').text('Edit Produk');
                $('#product_id').val(data.product_id);
                $('#product_name').val(data.product_name);
                $('#owner').val(data.owner);
                $('#description').val(data.description);
                $('#contact').val(data.contact);
                $('#address').val(data.address);

                // Tampilkan foto saat ini jika ada
                if (data.photo_path) {
                    $('#currentPhoto').attr('src', "{{ asset('storage') }}/" + data.photo_path).show();
                } else {
                    $('#currentPhoto').hide().attr('src', '#');
                }

                // Kosongkan input file
                $('#photo_path').val('');

                $('#productModal').modal('show');
            }).fail(function () {
                Swal.fire('Error!', 'Gagal mengambil data produk.', 'error');
            });
        });

        // --- Logika untuk Hapus ---
        $('body').on('click', '.delete-btn', function () {
            var productId = $(this).attr('data-id');

            if (!productId) {
                Swal.fire('Error!', 'Tidak dapat menemukan ID Produk.', 'error');
                return;
            }

            var url = "{{ url('admin/products') }}/" + productId;

            Swal.fire({
                title: 'Anda Yakin?',
                text: "Data produk ini akan dihapus permanen!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: url,
                        type: "DELETE",
                        success: function (response) {
                            Swal.fire('Berhasil!', response.success, 'success');
                            $('#row-product-' + productId).remove(); // Hapus baris dari tabel
                            // location.reload(); // Atau reload jika perlu update paginasi
                        },
                        error: function (xhr) {
                            Swal.fire('Error!', 'Gagal menghapus data. Coba lagi.', 'error');
                        }
                    });
                }
            });
        });

        // --- Preview Foto ---
        $('#photo_path').on('change', function (event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('currentPhoto');
                output.src = reader.result;
                output.style.display = 'block'; // Tampilkan preview
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            } else {
                // Sembunyikan preview jika tidak ada file dipilih
                $('#currentPhoto').hide().attr('src', '#');
            }
        });

    });
</script>
@stop