@extends('adminlte::page')

@section('title', 'Media Learning - Buleleng Creative Hub')

@section('content_header')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Media Pembelajaran</li>
    </ol>
@stop

@section('content')

    {{-- Pesan error jika user tidak punya akses (dari controller lmsShow) --}}
    @if(session('error'))
         <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    {{-- PERBAIKAN: Filter Chips (Badges) tanpa ikon, warna primer/abu --}}
    <div class="mb-4">
        {{-- Tombol 'Semua' --}}
        <a href="{{ route('user.lms.index') }}" 
           class="btn btn-md m-1 {{ $currentType == 'all' ? 'btn-primary' : 'btn-light' }}">
           Semua
        </a>
        {{-- Tombol 'Artikel' --}}
        <a href="{{ route('user.lms.index', ['type' => 'article']) }}" 
           class="btn btn-md m-1 {{ $currentType == 'article' ? 'btn-primary' : 'btn-light' }}">
           Artikel
        </a>
        {{-- Tombol 'Buku' --}}
        <a href="{{ route('user.lms.index', ['type' => 'book']) }}" 
           class="btn btn-md m-1 {{ $currentType == 'book' ? 'btn-primary' : 'btn-light' }}">
           Buku
        </a>
         {{-- Tombol 'Video' --}}
         <a href="{{ route('user.lms.index', ['type' => 'video']) }}" 
           class="btn btn-md m-1 {{ $currentType == 'video' ? 'btn-primary' : 'btn-light' }}">
           Video
        </a>
    </div>

    {{-- Grid Card Konten LMS --}}
    <div class="row">
        @forelse ($lmsItems as $lms)
            <div class="col-md-4 col-lg-3 mb-4"> {{-- 4 card per baris di layar large --}}
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title font-weight-bold mb-2">{{ $lms->content_title }}</h5>
                        
                        {{-- Badge Tipe Konten (tanpa ikon) --}}
                        <p class="mb-2">
                            @if($lms->type == 'article')
                                <span class="badge badge-sucess">Artikel</span>
                            @elseif($lms->type == 'book')
                                <span class="badge badge-success">Buku</span>
                            @elseif($lms->type == 'video')
                                <span class="badge badge-success">Video</span>
                            @endif
                        </p>

                        <p class="card-text text-muted flex-grow-1">
                            {{ Str::limit($lms->description ?? 'Tidak ada deskripsi.', 100) }}
                        </p>

                        {{-- Tombol Buka Sumber --}}
                        @php
                            $isUrl = filter_var($lms->source, FILTER_VALIDATE_URL);
                            $url = $isUrl ? $lms->source : ($lms->source ? asset('storage/' . $lms->source) : '#');
                            $icon = $isUrl ? 'fa-external-link-alt' : 'fa-download';
                        @endphp
                        <a href="{{ $url }}" 
                           target="_blank" 
                           class="btn btn-primary mt-auto align-self-start {{ $url == '#' ? 'disabled' : '' }}"
                           {{ !$isUrl ? 'download' : '' }}>
                           <i class="fas {{ $icon }} mr-1"></i> Buka Konten
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Belum ada media pembelajaran yang ditambahkan
                    @if($currentType != 'all')
                        untuk tipe <strong>{{ $currentType }}</strong>
                    @endif
                    yang sesuai dengan sub sektor Anda.
                </div>
            </div>
        @endforelse
    </div>

    {{-- Link Paginasi --}}
    <div class="d-flex justify-content-center">
        {{-- Tambahkan parameter query 'type' ke link paginasi agar filter tetap aktif --}}
        {{ $lmsItems->appends(['type' => $currentType])->links() }}
    </div>
@stop

@section('css')
<style>
    /* Style untuk chip filter kustom (opsional, btn-light sudah cukup 'pudar') */
    .btn-light {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #495057;
    }
    .btn-light:hover {
        background-color: #e2e6ea;
    }
</style>
@stop

