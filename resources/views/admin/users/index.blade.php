@extends('adminlte::page')

@section('title', 'Daftar Peserta - Buleleng Creative Hub')

@section('meta_tags')
{{-- Token CSRF untuk AJAX (jika ada aksi hapus) --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Daftar Peserta</li>
        </ol>
    </div>
    {{-- TIDAK ADA TOMBOL TAMBAH USER --}}
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-end">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" id="userSearchInput" class="form-control" placeholder="Cari user...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap" id="userTable">
            <thead>
                <tr>
                    <th style="width: 50px" class="text-center">No</th>
                    <th class="text-center">Nama User</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Nomor Telepon</th>
                    <th style="width: 100px" class="text-center">Status</th>
                    <th class="text-center">Mentor</th>
                    {{-- PERBAIKAN: Lebarkan kolom Aksi --}}
                    <th style="width: 150px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="text-center" id="row-user-{{ $user->user_id }}">
                        <td>{{ $loop->iteration + $users->firstItem() - 1 }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            {{ $user->profile->phone_number ?? 'N/A' }}
                        </td>
                        <td>
                            <span class="badge badge-info">User</span>
                        </td>
                        <td>
                            @if($user->mentors->isNotEmpty())
                                {{ $user->mentors->first()->mentor_name ?? 'N/A' }}
                            @else
                                <span class="text-muted font-italic">Belum memiliki mentor</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- PERBAIKAN: Tombol Lihat ditambahkan --}}
                            <a href="{{ route('admin.users.show', $user->user_id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $user->user_id }}">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        {{-- Sesuaikan colspan karena ada kolom baru --}}
                        <td colspan="7" class="text-center">Data user belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    @endif
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // --- Setup CSRF Token ---
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // --- Logika Pencarian ---
        $('#userSearchInput').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $("#userTable tbody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // --- Logika untuk Hapus ---
        $('body').on('click', '.delete-btn', function () {
            var userId = $(this).attr('data-id');

            if (!userId) {
                Swal.fire('Error!', 'Tidak dapat menemukan ID User.', 'error');
                return;
            }

            var url = "{{ url('admin/users') }}/" + userId;

            Swal.fire({
                title: 'Anda Yakin?',
                text: "User ini akan dihapus permanen!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: url,
                        type: "DELETE",
                        success: function (response) {
                            Swal.fire('Berhasil!', response.success, 'success');
                            $('#row-user-' + userId).remove();
                        },
                        error: function (xhr) {
                            let errorMsg = 'Gagal menghapus data. Coba lagi.';
                            if (xhr.responseJSON && xhr.responseJSON.error_message) {
                                errorMsg = xhr.responseJSON.error_message;
                            }
                            Swal.fire('Error!', errorMsg, 'error');
                        }
                    });
                }
            });
        });

    });
</script>
@stop