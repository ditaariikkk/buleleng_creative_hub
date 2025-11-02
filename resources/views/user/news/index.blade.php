@extends('adminlte::page')

@section('title', 'News - Buleleng Creative Hub')

@section('content_header')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Berita</li>
</ol>
@stop

@section('content')

<div class="row">
    @forelse ($newsItems as $news)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <img src="{{ $news->news_photo ? asset('storage/' . $news->news_photo) : asset('img/placeholder-news.jpg') }}"
                    class="card-img-top" alt="{{ $news->title }}" style="height: 200px; object-fit: cover;"
                    onerror="this.onerror=null;this.src='{{ asset('img/placeholder-news.jpg') }}';">

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title font-weight-bold">{{ $news->title }}</h5>

                    <p class="card-text text-muted small mt-2 mb-2">
                        <i class="far fa-clock"></i>
                        {{ $news->created_at->locale('id_ID')->translatedFormat('d F Y') }}
                    </p>

                    <p class="card-text flex-grow-1">
                        {{ Str::limit($news->description ?? 'Deskripsi tidak tersedia.', 120) }}
                    </p>

                    <a href="{{ route('user.news.show', $news->news_id) }}"
                        class="btn btn-primary mt-auto align-self-start">
                        <i class="fas fa-info-circle mr-1"></i> Baca Selengkapnya
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle mr-2"></i>
                Saat ini belum ada berita yang dipublikasikan.
            </div>
        </div>
    @endforelse
</div>

{{-- Link Paginasi --}}
<div class="d-flex justify-content-center">
    {{ $newsItems->links() }}
</div>

@stop