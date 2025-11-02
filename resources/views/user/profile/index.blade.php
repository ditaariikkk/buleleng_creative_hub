@extends('adminlte::page')

{{-- $profile didapat dari controller --}}

@section('title', 'Profil - Buleleng Creative Hub')

@section('content_header')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Profil</li>
</ol>
@stop

@section('content')

{{-- Tampilkan notifikasi sukses/error --}}
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
{{-- Tampilkan error validasi (jika ada) --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Oops! Ada kesalahan:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
    </div>
@endif


<div class="row">
    {{-- Kolom Kiri: Profil Utama (Foto, Nama, Kontak, Mentor) --}}
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                        src="{{ $profile->user_photo ? asset('storage/' . $profile->user_photo) : asset('img/avatar.jpg') }}"
                        alt="Foto profil {{ $user->name }}" style="width: 150px; height: 150px; object-fit: cover;"
                        onerror="this.onerror=null;this.src='{{ asset('img/avatar.jpg') }}';">
                </div>

                <h3 class="profile-username text-center">{{ $user->name }}</h3>

                <p class="text-muted text-center">{{ $user->email }}</p>

                {{-- Tombol Edit Profil --}}
                <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#editProfileModal">
                    <b><i class="fas fa-edit mr-1"></i> Edit Profil</b>
                </button>
                <hr>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b><i class="fas fa-phone mr-1"></i> Telepon</b> <a
                            class="float-right">{{ $profile->phone_number ?? 'N/A' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-link mr-1"></i> Portofolio</b>
                        @if($profile->portofolio_url ?? null) {{-- Menggunakan 'portofolio_url' --}}
                            <a href="{{ $profile->portofolio_url }}" target="_blank" class="float-right">Lihat <i
                                    class="fas fa-external-link-alt fa-xs"></i></a>
                        @else
                            <a class="float-right text-muted">N/A</a>
                        @endif
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-user-tie mr-1"></i> Mentor</b>
                        @if($user->mentors->isNotEmpty())
                            {{-- Ganti rute ke user.mentors.show --}}
                            <a href="{{ route('user.mentors.show', $user->mentors->first()->mentor_id) }}"
                                class="float-right">{{ $user->mentors->first()->mentor_name }}</a>
                        @else
                            <span class="float-right text-muted font-italic">Belum memiliki mentor</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Bio, Sub Sektor, Kebutuhan --}}
    <div class="col-md-8">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-id-card mr-1"></i> Bio</h3>
            </div>
            <div class="card-body">
                <p class="text-muted" style="white-space: pre-wrap;">{{ $profile->bio ?? 'Bio belum diisi.' }}</p>
            </div>
        </div>
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tags mr-1"></i> Sub Sektor Diminati</h3>
            </div>
            <div class="card-body">
                @if($profile && $profile->creativeSubSectors->isNotEmpty())
                    @foreach($profile->creativeSubSectors as $subSector)
                        <span class="badge badge-primary mr-1 mb-1 p-2">{{ $subSector->name }}</span>
                    @endforeach
                @else
                    <p class="text-muted font-italic">Belum ada sub sektor yang dipilih.</p>
                @endif
            </div>
        </div>
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tasks mr-1"></i> Layanan yang Dibutuhkan</h3>
            </div>
            <div class="card-body">
                @if($profile && $profile->userNeeds->isNotEmpty())
                    @foreach($profile->userNeeds as $need)
                        <span class="badge badge-info mr-1 mb-1 p-2">{{ $need->need_name }}</span>
                    @endforeach
                @else
                    <p class="text-muted font-italic">Belum ada layanan yang dibutuhkan.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ============================================= --}}
{{-- MODAL EDIT PROFIL 3-TAB --}}
{{-- ============================================= --}}
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- Navigasi Tab --}}
                <ul class="nav nav-tabs" id="profileTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="edit-akun-tab" data-toggle="tab" href="#edit-akun" role="tab"
                            aria-controls="edit-akun" aria-selected="true">Akun</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="edit-profil-tab" data-toggle="tab" href="#edit-profil" role="tab"
                            aria-controls="edit-profil" aria-selected="false">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="edit-minat-tab" data-toggle="tab" href="#edit-minat" role="tab"
                            aria-controls="edit-minat" aria-selected="false">Minat</a>
                    </li>
                </ul>

                {{-- Konten Tab --}}
                <div class="tab-content" id="profileTabContent">

                    {{-- TAB 1: EDIT AKUN --}}
                    <div class="tab-pane fade show active" id="edit-akun" role="tabpanel"
                        aria-labelledby="edit-akun-tab">
                        <form action="{{ route('user.profile.updateAccount') }}" method="POST" id="form-akun"
                            class="mt-3">
                            @csrf
                            @method('PATCH')
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name', 'updateAccount') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name', 'updateAccount') <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email', 'updateAccount') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email', 'updateAccount') <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <hr>
                            <p class="text-muted">Kosongkan password jika tidak ingin mengubahnya.</p>
                            <div class="form-group">
                                <label for="password">Password Baru</label>
                                <input type="password" name="password" id="password"
                                    class="form-control password-input @error('password', 'updateAccount') is-invalid @enderror">
                                @error('password', 'updateAccount') <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control password-input">
                            </div>
                            <button type="submit" class="btn btn-primary float-right">Simpan Perubahan Akun</button>
                        </form>
                    </div>

                    {{-- TAB 2: EDIT PROFIL --}}
                    <div class="tab-pane fade" id="edit-profil" role="tabpanel" aria-labelledby="edit-profil-tab">
                        <form action="{{ route('user.profile.updateDetails') }}" method="POST" id="form-profil"
                            class="mt-3" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <div class="form-group">
                                <label for="bio_edit">Bio Singkat</label>
                                <textarea class="form-control @error('bio', 'updateDetails') is-invalid @enderror"
                                    id="bio_edit" name="bio" rows="3">{{ old('bio', $profile->bio ?? '') }}</textarea>
                                @error('bio', 'updateDetails') <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="phone_number_edit">Nomor HP</label>
                                <input type="text"
                                    class="form-control @error('phone_number', 'updateDetails') is-invalid @enderror"
                                    id="phone_number_edit" name="phone_number"
                                    value="{{ old('phone_number', $profile->phone_number ?? '') }}" required>
                                @error('phone_number', 'updateDetails') <span
                                class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="portofolio_url_edit">Link Portofolio</label>
                                <input type="url"
                                    class="form-control @error('portofolio_url', 'updateDetails') is-invalid @enderror"
                                    id="portofolio_url_edit" name="portofolio_url"
                                    value="{{ old('portofolio_url', $profile->portofolio_url ?? '') }}">
                                @error('portofolio_url', 'updateDetails') <span
                                class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="user_photo_edit">Ganti Foto Profil</label>
                                <input type="file"
                                    class="form-control-file @error('user_photo', 'updateDetails') is-invalid @enderror"
                                    id="user_photo_edit" name="user_photo" accept="image/*">
                                @error('user_photo', 'updateDetails') <span
                                class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="btn btn-primary float-right">Simpan Detail Profil</button>
                        </form>
                    </div>

                    {{-- TAB 3: EDIT MINAT --}}
                    <div class="tab-pane fade" id="edit-minat" role="tabpanel" aria-labelledby="edit-minat-tab">
                        <form action="{{ route('user.profile.updateInterests') }}" method="POST" id="form-minat"
                            class="mt-3">
                            @csrf
                            @method('PATCH')
                            <div class="form-group">
                                <label>Sub Sektor Kreatif <span class="text-danger">*</span></label>
                                <div class="row p-2 border rounded @error('sub_sectors', 'updateInterests') border-danger @enderror"
                                    style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($subSectors as $sub)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sub_sectors[]"
                                                    value="{{ $sub->sub_sector_id }}"
                                                    id="edit_sub_{{ $sub->sub_sector_id }}" {{ in_array($sub->sub_sector_id, old('sub_sectors', $profile->creativeSubSectors->pluck('sub_sector_id')->toArray() ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="edit_sub_{{ $sub->sub_sector_id }}">{{ $sub->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('sub_sectors', 'updateInterests') <span class="text-danger small"><strong>Pilih
                                minimal satu.</strong></span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Kebutuhan Layanan <span class="text-danger">*</span></label>
                                <div class="row p-2 border rounded @error('user_needs', 'updateInterests') border-danger @enderror"
                                    style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($needs as $need)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="user_needs[]"
                                                    value="{{ $need->need_id }}" id="edit_need_{{ $need->need_id }}" {{ in_array($need->need_id, old('user_needs', $profile->userNeeds->pluck('need_id')->toArray() ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="edit_need_{{ $need->need_id }}">{{ $need->need_name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('user_needs', 'updateInterests') <span class="text-danger small"><strong>Pilih
                                minimal satu.</strong></span> @enderror
                            </div>
                            <button type="submit" class="btn btn-primary float-right">Simpan Minat (Perbarui
                                Rekomendasi)</button>
                        </form>
                    </div>
                </div>
            </div>
            {{-- Footer modal tidak diperlukan karena tiap form punya tombol submit sendiri --}}
        </div>
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

    .border.rounded {
        max-height: 200px;
        overflow-y: auto;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // Logika untuk menampilkan tab yang benar jika ada error validasi
        @if ($errors->hasBag('updateAccount'))
            $('#editProfileModal').modal('show');
            $('#edit-akun-tab').tab('show');
        @elseif ($errors->hasBag('updateDetails'))
            $('#editProfileModal').modal('show');
            $('#edit-profil-tab').tab('show');
        @elseif ($errors->hasBag('updateInterests'))
            $('#editProfileModal').modal('show');
            $('#edit-minat-tab').tab('show');
        @endif

        // Logika untuk mengingat tab terakhir yang dibuka (opsional, tapi bagus)
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('lastProfileTab', $(this).attr('href'));
        });
        var lastTab = localStorage.getItem('lastProfileTab');
        if (lastTab) {
            $('#profileTab a[href="' + lastTab + '"]').tab('show');
        }
    });
</script>
@stop