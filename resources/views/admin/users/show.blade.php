@extends('adminlte::page')

{{-- Asumsi Anda memiliki model UserProfile, CreativeSubSector, UserNeed --}}
@php
    $profile = $user->profile; // Ambil profile sekali untuk kemudahan
@endphp

@section('title', 'Detail User: ' . $user->name)

@section('content_header')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Daftar User</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
</ol>
@stop

@section('content')
<div class="row">
    {{-- Kolom Kiri: Profil Utama (Foto, Nama, Kontak, Mentor) --}}
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                        src="{{ $profile->user_photo ? asset('storage/' . $profile->user_photo) : asset('img/avatar.jpg') }}"
                        --}} alt="Foto profil {{ $user->name }}" style="width: 150px; height: 150px; object-fit: cover;"
                        onerror="this.onerror=null;this.src='{{ asset('img/avatar.jpg') }}';">
                </div>

                <h3 class="profile-username text-center">{{ $user->name }}</h3>

                <p class="text-muted text-center">{{ $user->email }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b><i class="fas fa-phone mr-1"></i> Telepon</b> <a
                            class="float-right">{{ $profile->phone_number ?? 'N/A' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-link mr-1"></i> Portofolio</b>
                        @if($profile->portfolio_url ?? null)
                            <a href="{{ $profile->portfolio_url }}" target="_blank" class="float-right">Lihat Portofolio <i
                                    class="fas fa-external-link-alt fa-xs"></i></a>
                        @else
                            <a class="float-right text-muted">N/A</a>
                        @endif
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-user-tie mr-1"></i> Mentor</b>
                        @if($user->mentors->isNotEmpty())
                            {{-- Tampilkan nama mentor pertama --}}
                            <a href="{{ route('admin.mentors.show', $user->mentors->first()->mentor_id) }}"
                                class="float-right">{{ $user->mentors->first()->mentor_name }}</a>
                        @else
                            <span class="float-right text-muted font-italic">Belum memiliki mentor</span>
                        @endif
                    </li>
                </ul>
            </div>
            <!-- /.card-body -->
        </div>
    </div>

    {{-- Kolom Kanan: Bio, Sub Sektor, Kebutuhan --}}
    <div class="col-md-8">
        {{-- Card Bio --}}
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-id-card mr-1"></i> Bio</h3>
            </div>
            <div class="card-body">
                {{-- Gunakan nl2br untuk menghormati baris baru, atau pre-wrap --}}
                <p class="text-muted" style="white-space: pre-wrap;">{{ $profile->bio ?? 'Bio belum diisi.' }}</p>
            </div>
        </div>

        {{-- Card Sub Sektor --}}
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tags mr-1"></i> Sub Sektor Diminati</h3>
            </div>
            <div class="card-body">
                @if($profile && $profile->creativeSubSectors->isNotEmpty())
                    @foreach($profile->creativeSubSectors as $subSector)
                        <span class="badge badge-primary mr-1 mb-1 p-2">{{ $subSector->name }}</span> {{-- Asumsi nama kolom
                        'name' --}}
                    @endforeach
                @else
                    <p class="text-muted font-italic">Belum ada sub sektor yang dipilih.</p>
                @endif
            </div>
        </div>

        {{-- Card Layanan Dibutuhkan --}}
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tasks mr-1"></i> Layanan yang Dibutuhkan</h3>
            </div>
            <div class="card-body">
                {{-- Pastikan relasi userNeeds sudah dimuat di controller --}}
                @if($profile && $profile->userNeeds->isNotEmpty())
                    @foreach($profile->userNeeds as $need)
                        <span class="badge badge-info mr-1 mb-1 p-2">{{ $need->need_name }}</span> {{-- Asumsi nama kolom 'name'
                        --}}
                    @endforeach
                @else
                    <p class="text-muted font-italic">Belum ada layanan yang dibutuhkan.</p>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>
            Kembali ke Daftar User
        </a>
    </div>
</div>
@stop

@section('css')
{{-- Tambahkan sedikit style jika perlu --}}
<style>
    .list-group-item b {
        width: 100px;
        /* Atur lebar label agar rata */
        display: inline-block;
    }

    .profile-user-img {
        border: 3px solid #adb5bd;
        margin: 0 auto;
        padding: 3px;
        width: 150px;
        height: 150px;
    }
</style>
@stop