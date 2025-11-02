@extends('adminlte::page')

@section('title', 'Daftar Admin - Buleleng Creative Hub')

@section('meta_tags')
{{-- Token CSRF untuk AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Daftar Admin</li>
        </ol>
    </div>
    <div>
        <button type="button" class="btn btn-primary" id="btn-add-admin">
            <i class="fas fa-plus-circle"></i> Tambah Admin
        </button>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-end">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" id="adminSearchInput" class="form-control" placeholder="Cari admin...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap" id="adminTable">
            <thead>
                <tr>
                    <th style="width: 50px" class="text-center">No</th>
                    <th class="text-center">Nama Admin</th>
                    <th class="text-center">Email</th>
                    <th style="width: 100px" class="text-center">Status</th>
                    <th style="width: 150px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($admins as $admin)
                    {{-- PERBAIKAN: Gunakan user_id --}}
                    <tr class="text-center" id="row-admin-{{ $admin->user_id }}">
                        <td>{{ $loop->iteration + $admins->firstItem() - 1 }}</td>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>
                            <span class="badge badge-danger">Admin</span>
                        </td>
                        <td class="text-center">
                            {{-- PERBAIKAN: Gunakan user_id --}}
                            <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="{{ $admin->user_id }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            @if($admin->user_id !== auth()->id()) {{-- PERBAIKAN: Gunakan user_id --}}
                                {{-- PERBAIKAN: Gunakan user_id --}}
                                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $admin->user_id }}">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Data admin belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($admins->hasPages())
        <div class="card-footer">
            {{ $admins->links() }}
        </div>
    @endif
</div>

{{-- Modal Tambah/Edit Admin --}}
<div class="modal fade" id="adminModal" tabindex="-1" role="dialog" aria-labelledby="adminModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminModalLabel">Form Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="adminForm" name="adminForm">
                <div class="modal-body">
                    {{-- PERBAIKAN: Ubah id dan name ke user_id --}}
                    <input type="hidden" id="user_id" name="user_id">

                    <div class="form-group">
                        <label for="name">Nama Admin</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <small id="passwordHelpBlock" class="form-text text-muted">
                            Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-admin">Simpan</button>
                </div>
            </form>
        </div>
    </div>
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
        $('#adminSearchInput').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $("#adminTable tbody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // --- Logika Modal ---

        // Tampilkan modal untuk Tambah Admin
        $('#btn-add-admin').on('click', function () {
            $('#adminForm').trigger('reset');
            $('#user_id').val(''); // PERBAIKAN: Gunakan #user_id
            $('#adminModalLabel').text('Tambah Admin Baru');
            $('#password').prop('required', true);
            $('#password_confirmation').prop('required', true);
            $('#passwordHelpBlock').hide();
            $('#togglePassword i').removeClass('fa-eye-slash').addClass('fa-eye');
            $('#password').attr('type', 'password');
            $('#togglePasswordConfirmation i').removeClass('fa-eye-slash').addClass('fa-eye');
            $('#password_confirmation').attr('type', 'password');
            $('#adminModal').modal('show');
        });

        // Kirim data (Simpan atau Update)
        $('#adminForm').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            var adminId = $('#user_id').val(); // PERBAIKAN: Gunakan #user_id
            var url;
            var methodType;

            if (adminId) {
                url = "{{ url('admin') }}/" + adminId; // URL route /admin/{admin}
                formData.append('_method', 'PUT');
                methodType = "POST";
            } else {
                url = "{{ route('admin.store') }}"; // URL route /admin (POST)
                methodType = "POST";
            }

            $.ajax({
                url: url,
                type: methodType,
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    $('#adminModal').modal('hide');
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.success,
                        type: 'success'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    let errorString = '<ul>';
                    if (xhr.status == 422) {
                        $.each(xhr.responseJSON.errors, function (key, value) {
                            if (key === 'password' && value[0].includes('confirmation')) {
                                errorString += '<li>Konfirmasi password tidak cocok.</li>';
                            } else {
                                errorString += '<li>' + value[0] + '</li>';
                            }
                        });
                    } else if (xhr.responseJSON && xhr.responseJSON.error_message) {
                        errorString += '<li>' + xhr.responseJSON.error_message + '</li>';
                    } else {
                        errorString += '<li>Terjadi error di server (' + xhr.status + '). Silakan coba lagi.</li>';
                        console.error('Error:', xhr.responseText);
                    }
                    errorString += '</ul>';
                    Swal.fire({
                        title: 'Data Tidak Valid!',
                        html: errorString,
                        type: 'error'
                    });
                }
            });
        });

        // --- Logika untuk Edit ---
        $('body').on('click', '.edit-btn', function () {
            var adminId = $(this).attr('data-id'); // data-id sekarang berisi user_id

            if (!adminId) {
                Swal.fire('Error!', 'Tidak dapat menemukan ID Admin.', 'error');
                return;
            }

            var url = "{{ url('admin') }}/" + adminId + "/edit"; // URL route /admin/{admin}/edit

            $.get(url, function (data) {
                $('#adminModalLabel').text('Edit Admin');
                $('#user_id').val(data.user_id); // PERBAIKAN: Isi #user_id dengan data.user_id
                $('#name').val(data.name);
                $('#email').val(data.email);

                $('#password').val('');
                $('#password_confirmation').val('');
                $('#password').prop('required', false);
                $('#password_confirmation').prop('required', false);
                $('#passwordHelpBlock').show();

                $('#togglePassword i').removeClass('fa-eye-slash').addClass('fa-eye');
                $('#password').attr('type', 'password');
                $('#togglePasswordConfirmation i').removeClass('fa-eye-slash').addClass('fa-eye');
                $('#password_confirmation').attr('type', 'password');

                $('#adminModal').modal('show');
            }).fail(function (xhr) { // Tambahkan parameter xhr
                console.error("Gagal mengambil data admin:", xhr.responseText); // Log error detail
                Swal.fire('Error!', 'Gagal mengambil data admin. Status: ' + xhr.status, 'error');
            });
        });

        // --- Logika untuk Hapus ---
        $('body').on('click', '.delete-btn', function () {
            var adminId = $(this).attr('data-id'); // data-id sekarang berisi user_id

            if (!adminId) {
                Swal.fire('Error!', 'Tidak dapat menemukan ID Admin.', 'error');
                return;
            }
            var currentUserId = {{ auth()->id() }};
            // PERBAIKAN: Pastikan perbandingan tipe data benar (misal string vs number)
            if (String(adminId) === String(currentUserId)) {
                Swal.fire('Info', 'Anda tidak dapat menghapus akun Anda sendiri.', 'info');
                return;
            }

            var url = "{{ url('admin') }}/" + adminId; // URL route /admin/{admin} (DELETE)

            Swal.fire({
                title: 'Anda Yakin?',
                text: "Admin ini akan dihapus!",
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
                            // PERBAIKAN: Gunakan user_id
                            $('#row-admin-' + adminId).remove();
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

        // --- Logika Toggle Show/Hide Password ---
        $('#togglePassword').on('click', function () {
            const passwordInput = $('#password');
            const icon = $(this).find('i');
            const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
            passwordInput.attr('type', type);
            icon.toggleClass('fa-eye fa-eye-slash');
        });

        $('#togglePasswordConfirmation').on('click', function () {
            const passwordInput = $('#password_confirmation');
            const icon = $(this).find('i');
            const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
            passwordInput.attr('type', type);
            icon.toggleClass('fa-eye fa-eye-slash');
        });

    });
</script>
@stop