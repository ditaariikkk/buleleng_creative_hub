@extends('adminlte::page')

@section('title', 'Mentor Saya')

@section('content_header')

<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Mentor Saya</li>
</ol>
@stop

@section('content')

{{-- Tampilkan pesan sukses/error jika ada --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
    </div>
@endif

{{-- Logika Utama: Cek apakah user SUDAH punya mentor --}}
@if($currentMentor)
    {{-- ============================================= --}}
    {{-- TAMPILAN JIKA SUDAH PUNYA MENTOR (DETAIL) --}}
    {{-- ============================================= --}}
    <div class="card card-solid card-primary card-outline">
        <div class="card-header bg-gradient-lightblue">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="card-title font-weight-bold my-1" style="font-size: 1.7rem;">
                    <i class="fas fa-user-tie mr-2"></i> {{ $currentMentor->mentor_name }}
                </h2>
                {{-- Tombol Ganti Mentor --}}
                <form action="{{ route('mentor.remove') }}" method="POST" class="d-inline remove-mentor-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-times-circle mr-1"></i> Ganti Mentor
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Kolom Kiri: Foto dan Status --}}
                <div class="col-md-4 text-center">
                    <img src="{{ $currentMentor->photo_path ? asset('storage/' . $currentMentor->photo_path) : asset('img/avatar.jpg') }}"
                        alt="Foto Mentor"
                        style="width: 100%; max-width: 300px; height: auto; max-height: 450px; object-fit: contain; background-color: #f8f9fa; border: 1px solid #eee; border-radius: 0.25rem; margin-bottom: 1rem;">

                    @if($currentMentor->status == 'Aktif')
                        <p class="text-muted"><span class="badge badge-success p-2" style="font-size: 1rem;">Aktif</span></p>
                    @else
                        <p class="text-muted"><span class="badge badge-danger p-2" style="font-size: 1rem;">Tidak Aktif</span>
                        </p>
                    @endif
                </div>

                {{-- Kolom Kanan: Detail Informasi --}}
                <div class="col-md-8">
                    <strong><i class="fas fa-book mr-1"></i> Bio</strong>
                    <p class="text-muted" style="white-space: pre-wrap;">
                        {{ $currentMentor->bio ?? 'Bio belum diisi.' }}
                    </p>
                    <hr>
                    <strong><i class="fas fa-lightbulb mr-1"></i> Ringkasan Keahlian</strong>
                    <p class="text-muted" style="white-space: pre-wrap;">
                        {{ $currentMentor->expertise_summary ?? 'Keahlian belum diisi.' }}
                    </p>
                    <hr>
                    <strong><i class="fas fa-tags mr-1"></i> Sub-Sektor Keahlian</strong>
                    <p class="text-muted">
                        @forelse ($currentMentor->creativeSubSectors as $subSector)
                            <span class="badge badge-primary mr-1 mb-1 p-2">{{ $subSector->name }}</span>
                        @empty
                            <span>Belum ada sub-sektor yang terhubung.</span>
                        @endforelse
                    </p>
                    <hr>
                    <strong><i class="fas fa-concierge-bell mr-1"></i> Layanan yang Diberikan</strong>
                    <p class="text-muted">
                        @forelse ($currentMentor->userNeeds as $need)
                            <span class="badge badge-info mr-1 mb-1 p-2">{{ $need->need_name }}</span>
                        @empty
                            <span>Belum ada layanan yang terhubung.</span>
                        @endforelse
                    </p>
                </div>
            </div>
        </div>
    </div>

@elseif($relatedMentors->isEmpty())
    {{-- ======================================================= --}}
    {{-- TAMPILAN JIKA BELUM PUNYA & TIDAK ADA REKOMENDASI --}}
    {{-- ======================================================= --}}
    <div class="card card-outline card-warning">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-exclamation-triangle mr-2"></i>Belum Ada Mentor Tersedia</h3>
        </div>
        <div class="card-body">
            <p>Saat ini belum ada mentor yang aktif sesuai dengan sub sektor Anda.</p>
            <p>Silakan perbarui sub sektor Anda di halaman <a href="{{ route('user.profile.index') }}">Profil</a> atau
                periksa kembali nanti.</p>
        </div>
    </div>

@else
    {{-- ================================================= --}}
    {{-- TAMPILAN JIKA BELUM PUNYA & ADA REKOMENDASI --}}
    {{-- ================================================= --}}
    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-users mr-2"></i>Rekomendasi Mentor</h3>
        </div>
        <div class="card-body">
            <p>Berikut adalah daftar mentor yang relevan dengan sub sektor Anda. Silakan pilih satu:</p>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>Nama Mentor</th>
                            <th>Ringkasan Keahlian</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($relatedMentors as $mentor)
                            <tr class="text-center">
                                <td>{{ $mentor->mentor_name }}</td>
                                <td>{{ Str::limit($mentor->expertise_summary ?? '-', 50) }}</td>
                                <td><span class="badge badge-success">Aktif</span></td>
                                <td>
                                    <form class="choose-mentor-form" action="{{ route('mentor.choose', $mentor->mentor_id) }}"
                                        method="POST" data-mentor-name="{{ $mentor->mentor_name }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-check-circle mr-1"></i> Pilih Mentor
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@stop

@section('js')
<script>
    $(document).ready(function () {
        // Script SweetAlert (Sama seperti di home.blade.php)
        if (typeof Swal !== 'undefined') {
            // Konfirmasi SweetAlert saat memilih mentor
            $('.choose-mentor-form').on('submit', function (e) {
                e.preventDefault(); var form = this; var mentorName = $(form).data('mentor-name');
                Swal.fire({
                    title: 'Konfirmasi Pemilihan Mentor',
                    text: "Pilih " + mentorName + " sebagai mentor Anda?",
                    type: 'question', showCancelButton: true,
                    confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Pilih', cancelButtonText: 'Batal'
                }).then((result) => { if (result.value) { form.submit(); } });
            });

            // Konfirmasi SweetAlert saat mengganti mentor
            $('.remove-mentor-form').on('submit', function (e) {
                e.preventDefault(); var form = this;
                Swal.fire({
                    title: 'Konfirmasi Ganti Mentor',
                    text: "Yakin ingin menghapus mentor saat ini dan memilih yang baru?",
                    type: 'warning', showCancelButton: true,
                    confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Ganti', cancelButtonText: 'Batal'
                }).then((result) => { if (result.value) { form.submit(); } });
            });
        } else {
            console.error("SweetAlert is not loaded!");
        }
    });
</script>
@stop