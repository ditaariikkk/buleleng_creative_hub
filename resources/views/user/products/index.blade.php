@extends('adminlte::page')

@section('title', 'Etalase Produk - Buleleng Creative Hub')

@section('content_header')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Etalase Produk</li>
</ol>
@stop

@section('content')

{{-- Container untuk Grid Card --}}
<div class="row">
    @forelse ($products as $product)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            {{-- Card dibuat clickable dengan data-attributes untuk memicu modal --}}
            <div class="card h-100 shadow-sm product-card" data-id="{{ $product->product_id }}" data-toggle="modal"
                data-target="#productDetailModal" style="cursor: pointer;">

                <img src="{{ $product->photo_path ? asset('storage/' . $product->photo_path) : asset('img/placeholder.png') }}"
                    class="card-img-top" alt="{{ $product->product_name }}" style="height: 200px; object-fit: cover;"
                    onerror="this.onerror=null;this.src='{{ asset('img/placeholder.png') }}';">

                <div class="card-body">
                    <h5 class="card-title font-weight-bold">{{ $product->product_name }}</h5>
                    <p class="card-text text-muted small mt-2">Oleh: {{ $product->owner }}</p>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                Belum ada produk yang ditambahkan di etalase.
            </div>
        </div>
    @endforelse
</div>

{{-- Link Paginasi --}}
<div class="d-flex justify-content-center">
    {{ $products->links() }}
</div>

{{-- Modal untuk Detail Produk --}}
<div class="modal fade" id="productDetailModal" tabindex="-1" role="dialog" aria-labelledby="productDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> {{-- modal-lg untuk grid --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="modalProductName">Memuat...</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- Konten detail akan dimuat di sini oleh AJAX --}}
                <div id="modalProductBody" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    /* Tambahkan efek hover sederhana pada card */
    .product-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // Event listener saat modal akan ditampilkan
        $('#productDetailModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Tombol/Card yang diklik
            var productId = button.data('id'); // Ambil ID dari data-id
            var modal = $(this);

            // Reset modal body
            modal.find('.modal-title').text('Memuat...');
            modal.find('#modalProductBody').html('<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>');

            // Definisikan URL untuk AJAX
            var url = '{{ route("user.products.show", ":id") }}';
            url = url.replace(':id', productId);

            // Panggil AJAX
            $.get(url, function (data) {
                // Handle success
                modal.find('.modal-title').text(data.product_name);

                // Tentukan URL foto
                var photoUrl = data.photo_path
                    ? '{{ asset('storage') }}/' + data.photo_path
                    : '{{ asset('img/placeholder.png') }}';

                // Buat HTML untuk grid di dalam modal
                var html = `
                    <div class="row">
                        <div class="col-md-6">
                            <img src="${photoUrl}" class="img-fluid rounded shadow-sm" alt="${data.product_name}" onerror="this.onerror=null;this.src='{{ asset('img/placeholder.png') }}';">
                        </div>
                        <div class="col-md-6">
                            <h4 class="font-weight-bold">Oleh: ${data.owner}</h4>
                            <hr>
                            <strong><i class="fas fa-info-circle mr-1 text-primary"></i> Deskripsi:</strong>
                            <p class="text-muted" style="white-space: pre-wrap;">${data.description || 'Deskripsi tidak tersedia.'}</p>
                            
                            <strong><i class="fas fa-phone-alt mr-1 text-success"></i> Kontak:</strong>
                            <p class="text-muted">${data.contact}</p>
                            
                            <strong><i class="fas fa-map-marker-alt mr-1 text-danger"></i> Alamat:</strong>
                            <p class="text-muted">${data.address}</p>
                        </div>
                    </div>
                `;

                modal.find('#modalProductBody').html(html);

            }).fail(function () {
                // Handle error
                modal.find('.modal-title').text('Error');
                modal.find('#modalProductBody').html('<p class="text-danger">Gagal memuat detail produk. Silakan coba lagi.</p>');
            });
        });
    });
</script>
@stop