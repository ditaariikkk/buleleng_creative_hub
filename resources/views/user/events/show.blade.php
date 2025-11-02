@extends('adminlte::page')

@php use \Carbon\Carbon; @endphp

@section('title', 'Detail Event: ' . $event->event_title)

@section('content_header')
<h1>Detail Event</h1>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('user.events.index') }}">Daftar Event</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($event->event_title, 30) }}</li>
</ol>
@stop

@section('content')
<div class="card card-solid card-info card-outline">
    <div class="card-header bg-gradient-info">
        <h2 class="card-title font-weight-bold my-1" style="font-size: 1.7rem;">
            <i class="fas fa-calendar-check mr-2"></i> {{ $event->event_title }}
        </h2>
    </div>
    <div class="card-body">

        {{-- BARIS 1: Informasi Detail (Menyamping) --}}
        <div class="row border-bottom pb-3 mb-3">
            <div class="col-md-3 col-sm-6 mb-2">
                <strong><i class="fas fa-flag mr-1"></i> Status</strong><br>
                @php
                    $status = $event->event_status;
                    $badgeClass = 'badge-secondary';
                    if ($status == 'Sedang Berlangsung')
                        $badgeClass = 'badge-success';
                    elseif ($status == 'Telah Berakhir')
                        $badgeClass = 'badge-danger';
                    elseif ($status == 'Belum Terlaksana')
                        $badgeClass = 'badge-info';
                @endphp
                <span class="badge {{ $badgeClass }} p-2" style="font-size: 1rem;">{{ $status }}</span>
            </div>

            <div class="col-md-3 col-sm-6 mb-2">
                <strong><i class="fas fa-calendar-alt mr-1"></i> Mulai</strong><br>
                <span class="text-muted">
                    {{ Carbon::parse($event->start_datetime)->locale('id_ID')->translatedFormat('d F Y, H:i') }} WITA
                </span>
            </div>

            <div class="col-md-3 col-sm-6 mb-2">
                <strong><i class="fas fa-calendar-check mr-1"></i> Selesai</strong><br>
                <span class="text-muted">
                    {{ Carbon::parse($event->end_datetime)->locale('id_ID')->translatedFormat('d F Y, H:i') }} WITA
                </span>
            </div>

            <div class="col-md-3 col-sm-6 mb-2">
                <strong><i class="fas fa-map-marker-alt mr-1"></i> Lokasi</strong><br>
                @if($event->event_type == 'offline' && $event->venue)
                    <span class="text-muted font-weight-bold">{{ $event->venue->venue_name }}</span>
                    <small class="d-block text-muted">{{ $event->venue->address ?? '' }}</small>
                @else
                    <span class="text-muted font-weight-bold"><i class="fas fa-globe mr-1"></i> Daring (Online)</span>
                @endif
            </div>

            @if($event->event_type == 'Offline' && $event->venue)
                <div class="col-md-3 col-sm-6 mb-2 mt-md-3">
                    <strong><i class="fas fa-users mr-1"></i> Kapasitas</strong><br>
                    <span class="text-muted">{{ $event->venue->capacity ?? 'N/A' }} Orang</span>
                </div>
                <div class="col-md-3 col-sm-6 mb-2 mt-md-3">
                    <strong><i class="fas fa-phone mr-1"></i> Narahubung Venue</strong><br>
                    <span class="text-muted">{{ $event->venue->contact ?? 'N/A' }}</span>
                </div>
            @endif
        </div>

        {{-- BARIS 2: Deskripsi dan Sub Sektor --}}
        <div class="row mt-3">
            <div class="col-md-7">
                <h4 class="text-primary"><i class="fas fa-file-alt mr-2"></i>Deskripsi Kegiatan</h4>
                <p class="text-muted" style="font-size: 1.1rem; white-space: pre-wrap;">
                    @php echo nl2br(e($event->description ?? 'Deskripsi berita belum tersedia.')); @endphp
                </p>
            </div>
            <div class="col-md-5">
                <h4 class="text-warning"><i class="fas fa-tags mr-1"></i> Sub Sektor Terkait</h4>
                <div class="mt-2">
                    @forelse($event->creativeSubSectors as $subSector)
                        <span class="badge badge-primary mr-1 mb-1 p-2">{{ $subSector->name }}</span>
                    @empty
                        <p class="text-muted font-italic">Tidak ada sub sektor terkait.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <a href="{{ route('user.events.index') }}" class="btn btn-secondary"> {{-- Link kembali ke user index --}}
            <i class="fas fa-arrow-left mr-1"></i>
            Kembali ke Daftar Event
        </a>
    </div>
</div>
@stop