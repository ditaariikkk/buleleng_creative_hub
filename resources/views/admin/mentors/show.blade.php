@extends('adminlte::page')

{{-- Judul halaman akan dinamis sesuai nama mentor --}}
@section('title', 'Detail Mentor: ' . $mentor->mentor_name)

@section('content_header')
{{-- Header juga akan dinamis --}}
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.mentors.index') }}">Daftar Mentor</a></li>
    <li class="breadcrumb-item active">{{ $mentor->mentor_name }}</li>
</ol>
@stop

@section('content')
{{-- PERBAIKAN: Card diganti menjadi card-solid untuk efek visual --}}
<div class="card card-solid card-primary card-outline">
    {{-- PERBAIKAN: Header ditambahkan dengan gaya baru --}}
    <div class="card-header bg-gradient-lightblue">
        <h2 class="card-title font-weight-bold my-1" style="font-size: 1.7rem;">
            <i class="fas fa-user-tie mr-2"></i> {{ $mentor->mentor_name }}
        </h2>
    </div>
    <div class="card-body">
        <div class="row">
            {{-- Kolom Kiri: Foto dan Info Utama --}}
            <div class="col-md-4 text-center">
                <img src="{{ $mentor->photo_path ? asset('storage/' . $mentor->photo_path) : asset('img/avatar.jpg') }}"
                    alt="Foto Mentor" style="width: 100%; 
                            max-width: 300px; /* Batasi lebar max */
                            height: auto; /* Biarkan tinggi menyesuaikan */
                            max-height: 450px; 
                            object-fit: contain; 
                            background-color: #f8f9fa; 
                            border: 1px solid #eee; 
                            border-radius: 0.25rem;
                            margin-bottom: 1rem;"> {{-- Tambah margin bawah --}}

                {{-- PERBAIKAN: Hapus H3 nama mentor dari sini --}}

                {{-- Tampilkan Status di bawah foto --}}
                @if($mentor->status == 'Aktif')
                    <p class="text-muted"><span class="badge badge-success p-2" style="font-size: 1rem;">Aktif</span></p>
                @else
                    <p class="text-muted"><span class="badge badge-danger p-2" style="font-size: 1rem;">Tidak Aktif</span>
                    </p>
                @endif
            </div>

            {{-- Kolom Kanan: Detail Informasi --}}
            <div class="col-md-8">
                <strong><i class="fas fa-book mr-1"></i> Bio</strong>
                <p class="text-muted" style="white-space: pre-wrap;"> {{-- pre-wrap agar baris baru terbaca --}}
                    {{ $mentor->bio ?? 'Bio belum diisi.' }}
                </p>
                <hr>
                <strong><i class="fas fa-lightbulb mr-1"></i> Ringkasan Keahlian</strong>
                <p class="text-muted" style="white-space: pre-wrap;">
                    {{ $mentor->expertise_summary ?? 'Keahlian belum diisi.' }}
                </p>
                <hr>
                <strong><i class="fas fa-tags mr-1"></i> Sub-Sektor Keahlian</strong>
                <p class="text-muted">
                    @forelse ($mentor->creativeSubSectors as $subSector)
                        <span class="badge badge-primary mr-1 mb-1 p-2">{{ $subSector->name }}</span>
                    @empty
                        <span>Belum ada sub-sektor yang terhubung.</span>
                    @endforelse
                </p>
                <hr>
                <strong><i class="fas fa-concierge-bell mr-1"></i> Layanan yang Diberikan</strong>
                <p class="text-muted">
                    @forelse ($mentor->userNeeds as $need)
                        <span class="badge badge-info mr-1 mb-1 p-2">{{ $need->need_name }}</span> {{-- Asumsi nama kolom
                        need_name --}}
                    @empty
                        <span>Belum ada layanan yang terhubung.</span>
                    @endforelse
                </p>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <a href="{{ route('admin.mentors.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Mentor
        </a>
    </div>
</div>
@stop