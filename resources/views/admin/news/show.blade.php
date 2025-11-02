@extends('adminlte::page')

@section('title', 'Detail Berita: ' . Str::limit($news->title, 50))

@section('content_header')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">Daftar Berita</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($news->title, 30) }}</li>
</ol>
@stop

@section('content')
<div class="card card-solid card-info card-outline">
    <div class="card-header bg-gradient-info">
        <h2 class="card-title font-weight-bold my-1" style="font-size: 1.7rem;">
            <i class="far fa-newspaper mr-2"></i> {{ $news->title }}
        </h2>
    </div>
    <div class="card-body">
        {{-- BARIS 1: Foto Berita --}}
        <div class="row mb-4 justify-content-center">
            <div class="col-12 col-md-8 text-center">
                @if($news->news_photo)
                    <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: .25rem; padding: 5px;">
                        <img src="{{ asset('storage/' . $news->news_photo) }}" alt="Foto Berita: {{ $news->title }}"
                            style="width: 100%; height: auto; max-height: 300px; object-fit: contain; display: block;"
                            onerror="this.onerror=null;this.src='{{ asset('img/placeholder.png') }}';">
                    </div>
                @else
                    <div class="text-muted border rounded p-5 text-center"
                        style="height: 250px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                        <span><i class="fas fa-image fa-3x"></i><br>Tidak ada foto</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- BARIS 2: Deskripsi Berita --}}
        <div class="row mb-4">
            <div class="col-12">
                {{-- PERBAIKAN: Style ditambahkan via @section('css') --}}
                <div class="news-description text-muted">
                    @php echo nl2br(e($news->description ?? 'Deskripsi berita belum tersedia.')); @endphp
                </div>
            </div>
        </div>

        {{-- BARIS 3: Sumber Berita --}}
        <div class="row border-top pt-3 mt-3">
            <div class="col-12">
                @if($news->source_url)
                    <p>
                        <a href="{{ $news->source_url }}" target="_blank" class="btn btn-outline-success">
                            Kunjungi Sumber <i class="fas fa-external-link-alt fa-xs ml-1"></i>
                        </a>
                    </p>
                @else
                    <p class="text-muted font-italic">Sumber URL tidak tersedia.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer">
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>
            Kembali ke Daftar Berita
        </a>
    </div>
</div>
@stop

{{-- PERBAIKAN: CSS ditambahkan di sini --}}
@section('css')
<style>
    .news-description {
        text-align: justify;
        /* Rata kanan-kiri */
        text-indent: 2em;
        /* Inden paragraf pertama (sesuaikan nilainya jika perlu, misal 30px) */
        font-size: 1.1rem;
        /* Ukuran font yang sudah ada */
        line-height: 1.6;
        /* Tambahkan jarak antar baris agar lebih mudah dibaca */
    }

    /* Hapus inden jika deskripsi hanya 1 baris pendek (opsional) */
    .news-description:first-line {
        /* text-indent: 0; */
        /* Komentari jika ingin inden tetap ada walau pendek */
    }

    /* Styling untuk <br> agar inden berfungsi per paragraf visual */
    .news-description br+* {
        /* Target elemen setelah <br> */
        text-indent: 2em;
        display: inline-block;
        /* Perlu agar text-indent bekerja setelah br */
        margin-top: 0.5em;
        /* Beri sedikit jarak antar paragraf visual */
    }
</style>
@stop