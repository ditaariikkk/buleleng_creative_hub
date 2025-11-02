@extends('adminlte::page')

@section('title', 'Events - Buleleng Crative Hub')

@section('content_header')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Daftar Event</li>
</ol>
@stop

@section('content')

{{-- Filter Chips (Badges) --}}
<div class="mb-4">
    {{-- Tombol 'Akan Datang' --}}
    <a href="{{ route('user.events.index', ['status' => 'Akan Datang']) }}"
        class="btn btn-md m-1 {{ $currentStatus == 'Akan Datang' ? 'btn-primary' : 'btn-light' }}">
        Akan Datang
    </a>
    {{-- Tombol 'Sedang Berlangsung' --}}
    <a href="{{ route('user.events.index', ['status' => 'Sedang Berlangsung']) }}"
        class="btn btn-md m-1 {{ $currentStatus == 'Sedang Berlangsung' ? 'btn-primary' : 'btn-light' }}">
        Sedang Berlangsung
    </a>
    {{-- Tombol 'Telah Berakhir' --}}
    <a href="{{ route('user.events.index', ['status' => 'Telah Berakhir']) }}"
        class="btn btn-md m-1 {{ $currentStatus == 'Telah Berakhir' ? 'btn-primary' : 'btn-light' }}">
        Telah Berakhir
    </a>
</div>

{{-- Grid Card Konten Event --}}
<div class="row">
    @forelse ($events as $event)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                {{-- (Opsional) Tambahkan gambar event jika ada --}}
                {{-- <img src="{{ $event->image_path ?? asset('img/event_placeholder.jpg') }}" class="card-img-top"
                    alt="{{ $event->event_title }}" style="height: 180px; object-fit: cover;"> --}}

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title font-weight-bold">{{ $event->event_title }}</h5>

                    <p class="card-text text-muted small mt-2 mb-1">
                        {{-- Tampilkan Status Badge --}}
                        @php
                            $status = $event->event_status; // Accessor akan otomatis terpanggil
                            $badgeClass = 'badge-secondary';
                            if ($status == 'Sedang Berlangsung')
                                $badgeClass = 'badge-success';
                            elseif ($status == 'Telah Berakhir')
                                $badgeClass = 'badge-danger';
                            elseif ($status == 'Belum Terlaksana')
                                $badgeClass = 'badge-info';
                        @endphp
                        <span class="badge {{ $badgeClass }} mr-2">{{ $status }}</span>
                    </p>

                    <p class="card-text text-muted small mb-1">
                        <i class="far fa-clock"></i>
                        {{ \Carbon\Carbon::parse($event->start_datetime)->locale('id_ID')->translatedFormat('d F Y, H:i') }}
                    </p>
                    <p class="card-text text-muted small mb-2">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $event->venue->venue_name ?? 'Online' }}
                    </p>

                    <p class="card-text flex-grow-1">
                        {{ Str::limit($event->description ?? 'Deskripsi tidak tersedia.', 120) }}
                    </p>

                    <a href="{{ route('user.events.show', $event->event_id) }}"
                        class="btn btn-primary mt-auto align-self-start">
                        <i class="fas fa-info-circle mr-1"></i> Lihat Detail
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle mr-2"></i>
                Belum ada event
                @if($currentStatus != 'all')
                    dengan status <strong>{{ $currentStatus }}</strong>
                @endif
                yang sesuai dengan sub sektor Anda.
            </div>
        </div>
    @endforelse
</div>

{{-- Link Paginasi --}}
<div class="d-flex justify-content-center">
    {{ $events->appends(['status' => $currentStatus])->links() }}
</div>

@stop

@section('css')
<style>
    /* Style untuk chip filter kustom (putih/abu) */
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