@extends('adminlte::page')

@section('title', 'Detail Venue: ' . $venue->venue_name)

@section('content_header')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.venues.index') }}">Daftar Venue</a></li>
    <li class="breadcrumb-item active">{{ $venue->venue_name }}</li>
</ol>
@stop

@section('content')
<div class="card card-solid card-primary card-outline">
    <div class="card-header bg-gradient-lightblue">
        <h2 class="card-title font-weight-bold my-1" style="font-size: 1.7rem;">
            <i class="fas fa-store-alt mr-2"></i> {{ $venue->venue_name }}
        </h2>
    </div>
    <div class="card-body">
        <div class="row">
            {{-- Kolom Kiri: Foto dan Info Utama --}}
            <div class="col-12 col-md-5 text-center d-flex flex-column align-items-center">
                <div
                    style="width: 100%; max-width: 400px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: .25rem; padding: 5px; margin-bottom: 1rem;">
                    <img src="{{ $venue->photo_path ? asset('storage/' . $venue->photo_path) : asset('img/placeholder.png') }}"
                        alt="Foto {{ $venue->venue_name }}"
                        style="width: 100%; height: auto; max-height: 400px; object-fit: contain; display: block;"
                        onerror="this.onerror=null;this.src='{{ asset('img/placeholder.png') }}';">
                </div>
                <p class="lead text-muted mt-2">
                    <i class="fas fa-user-tie mr-1"></i> Pemilik: <strong>{{ $venue->owner ?? 'N/A' }}</strong>
                </p>
            </div>

            {{-- Kolom Kanan: Detail Informasi --}}
            <div class="col-12 col-md-7">

                <h4 class="mt-4"><i class="fas fa-map-marker-alt text-danger mr-2"></i>Alamat</h4>
                <p class="text-muted" style="white-space: pre-wrap;">{{ $venue->address ?? 'N/A' }}</p>
                <hr>

                <h4 class="mt-4"><i class="fas fa-phone-alt text-success mr-2"></i>Kontak</h4>
                <p class="text-muted">{{ $venue->contact ?? 'N/A' }}</p>
                <hr>

                <h4 class="mt-4"><i class="fas fa-users text-info mr-2"></i>Kapasitas</h4>
                <p class="text-muted">{{ $venue->capacity ?? 'N/A' }} Orang</p>

            </div>
        </div>
    </div>
    <div class="card-footer">
        <a href="{{ route('admin.venues.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>
            Kembali ke Daftar Venue
        </a>
    </div>
</div>
@stop