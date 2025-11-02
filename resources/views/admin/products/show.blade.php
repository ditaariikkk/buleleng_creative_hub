@extends('adminlte::page')

@section('title', 'Detail Produk: ' . $product->product_name)

@section('content_header')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Daftar Produk</a></li>
    <li class="breadcrumb-item active">{{ $product->product_name }}</li>
</ol>
@stop

@section('content')
{{-- PERBAIKAN: Tambahkan card-primary card-outline --}}
<div class="card card-solid card-primary card-outline">
    {{-- PERBAIKAN: Header ditambahkan --}}
    <div class="card-header bg-gradient-lightblue">
        <h2 class="card-title font-weight-bold my-1" style="font-size: 1.7rem;">
            {{-- Ganti ikon sesuai produk --}}
            {{ $product->product_name }}
        </h2>
    </div>
    <div class="card-body">
        <div class="row">
            {{-- Kolom Kiri: Foto dan Owner --}}
            <div class="col-12 col-md-5 text-center d-flex flex-column align-items-center">
                <div
                    style="width: 100%; max-width: 400px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: .25rem; padding: 5px; margin-bottom: 1rem;">
                    <img src="{{ $product->photo_path ? asset('storage/' . $product->photo_path) : asset('img/placeholder.png') }}"
                        alt="Foto {{ $product->product_name }}"
                        style="width: 100%; height: auto; max-height: 500px; object-fit: contain; display: block;"
                        onerror="this.onerror=null;this.src='{{ asset('img/placeholder.png') }}';">
                </div>
                <p class="lead text-muted mt-2">
                    <i class="fas fa-user-tie mr-1"></i> Oleh: <strong>{{ $product->owner }}</strong>
                </p>
            </div>

            {{-- Kolom Kanan: Detail Produk --}}
            <div class="col-12 col-md-7">
                {{-- PERBAIKAN: Hapus H2 nama produk dari sini --}}

                <h4 class="mt-4"><i class="fas fa-info-circle text-primary mr-2"></i>Deskripsi</h4>
                <div class="product-description text-muted" style="white-space: pre-wrap;">
                    {{ $product->description ?? 'Deskripsi produk belum tersedia.' }}
                </div>
                <hr>

                <h4 class="mt-4"><i class="fas fa-phone-alt text-success mr-2"></i>Kontak</h4>
                <p class="text-muted">{{ $product->contact }}</p>
                <hr>

                <h4 class="mt-4"><i class="fas fa-map-marker-alt text-danger mr-2"></i>Alamat</h4>
                <p class="text-muted">{{ $product->address }}</p>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>
            Kembali ke Daftar Produk
        </a>
    </div>
</div>
@stop