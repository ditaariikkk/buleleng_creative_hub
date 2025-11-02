@extends('adminlte::page')

@php use \Carbon\Carbon; @endphp

@section('title', 'Detail Acara: ' . $event->event_title)

@section('content_header')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Daftar Acara</a></li>
    <li class="breadcrumb-item active">{{ $event->event_title }}</li>
</ol>
@stop

@section('content')
<div class="card card-solid">
    {{-- Judul Utama Acara --}}
    <div class="card-header bg-gradient-lightblue">
        <h2 class="card-title font-weight-bold my-1" style="font-size: 1.7rem;">
            <i class="fas fa-calendar-check mr-2"></i> {{ $event->event_title }}
        </h2>
    </div>
    <div class="card-body">

        {{-- BARIS 1: Informasi Detail (Menyamping) --}}
        <div class="row border-bottom pb-3 mb-3">
            {{-- Status --}}
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

            {{-- Waktu Mulai --}}
            <div class="col-md-3 col-sm-6 mb-2">
                <strong><i class="fas fa-calendar-alt mr-1"></i> Mulai</strong><br>
                <span class="text-muted">
                    {{ Carbon::parse($event->start_datetime)->locale('id_ID')->translatedFormat('d F Y, H:i') }}
                </span>
            </div>

            {{-- Waktu Selesai --}}
            <div class="col-md-3 col-sm-6 mb-2">
                <strong><i class="fas fa-calendar-check mr-1"></i> Selesai</strong><br>
                <span class="text-muted">
                    {{ Carbon::parse($event->end_datetime)->locale('id_ID')->translatedFormat('d F Y, H:i') }}
                </span>
            </div>

            {{-- Lokasi / Venue --}}
            <div class="col-md-3 col-sm-6 mb-2">
                <strong><i class="fas fa-map-marker-alt mr-1"></i> Lokasi</strong><br>
                @if($event->event_type == 'offline' && $event->venue)
                    <span class="text-muted font-weight-bold">{{ $event->venue->venue_name }}</span>
                    <small class="d-block text-muted">{{ $event->venue->address ?? '' }}</small>
                @else
                    <span class="text-muted font-weight-bold"><i class="fas fa-globe mr-1"></i> Daring (Online)</span>
                @endif
            </div>

            {{-- Kapasitas & Kontak (Hanya jika Offline) --}}
            @if($event->event_type == 'offline' && $event->venue)
                <div class="col-md-3 col-sm-6 mb-2 mt-md-3"> {{-- mt-md-3 untuk baris baru di layar besar --}}
                    <strong><i class="fas fa-users mr-1"></i> Kapasitas</strong><br>
                    <span class="text-muted">{{ $event->venue->capacity ?? 'N/A' }} Orang</span>
                </div>
                <div class="col-md-3 col-sm-6 mb-2 mt-md-3">
                    <strong><i class="fas fa-phone mr-1"></i> Narahubung Venue</strong><br>
                    <span class="text-muted">{{ $event->venue->contact ?? 'N/A' }}</span>
                </div>
            @endif
        </div>

        {{-- BARIS 2: Deskripsi dan Sub Sektor (Menyamping) --}}
        <div class="row mt-3">
            {{-- Deskripsi --}}
            <div class="col-md-7">
                <h4 class="text-primary"><i class="fas fa-file-alt mr-2"></i>Deskripsi Kegiatan</h4>
                <p class="text-muted" style="font-size: 1.1rem;">
                    {{ $event->description ?? 'Deskripsi kegiatan belum tersedia.' }}
                </p>
            </div>

            {{-- Sub Sektor --}}
            <div class="col-md-5">
                <h4 class="text-info"><i class="fas fa-tags mr-1"></i> Sub Sektor Terkait</h4>
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
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>
            Kembali ke Daftar Acara
        </a>
    </div>
</div>
@stop

@section('css')
<style>
    .list-group-item b {
        width: 100px;
        display: inline-block;
    }

    .profile-user-img {
        border: 3px solid #adb5bd;
        margin: 0 auto;
        padding: 3px;
        width: 150px;
        height: 150px;
        object-fit: cover;
    }
</style>
@stop