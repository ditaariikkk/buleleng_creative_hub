@extends('adminlte::page')

@section('title', 'Daftar Mentor - Buleleng Creative Hub')

@section('meta_tags')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Daftar Mentor</li>
        </ol>
    </div>
    <div class="d-flex align-items-center">
        <div class="input-group input-group-sm mr-2" style="width: 250px;">
            <input type="text" id="customSearchInput" class="form-control" placeholder="Cari mentor...">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-primary" id="btn-add-mentor">
                <i class="fas fa-plus-circle"></i> Tambah Mentor
            </button>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap" id="mentorTable">
            <thead>
                <tr>
                    <th style="width: 50px" class="text-center">No</th>
                    <th class="text-center">Nama Mentor</th>
                    <th style="width: 150px" class="text-center">Status</th>
                    <th style="width: 200px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mentors as $mentor)
                    <tr class="text-center" id="row-mentor-{{ $mentor->mentor_id }}">
                        <td>{{ $loop->iteration + $mentors->firstItem() - 1 }}</td>
                        <td>{{ $mentor->mentor_name }}</td>
                        <td>
                            @if($mentor->status == 'Aktif')
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.mentors.show', $mentor->mentor_id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                            <button type="button" class="btn btn-sm btn-warning edit-btn"
                                data-id="{{ $mentor->mentor_id }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-btn"
                                data-id="{{ $mentor->mentor_id }}">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Data mentor belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($mentors->hasPages())
        <div class="card-footer">
            {{ $mentors->links() }}
        </div>
    @endif
</div>

<div class="modal fade" id="mentorModal" tabindex="-1" role="dialog" aria-labelledby="mentorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mentorModalLabel">Form Mentor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="mentorForm" name="mentorForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="mentor_id" name="mentor_id">

                    <div class="form-group">
                        <label for="mentor_name">Nama Mentor</label>
                        <input type="text" class="form-control" id="mentor_name" name="mentor_name">
                    </div>

                    <div class="form-group">
                        <label>Sub Sektor Keahlian</label>
                        <div class="row">
                            @foreach ($subSectors as $sub)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="sub_sectors[]"
                                            value="{{ $sub->sub_sector_id }}" id="sub_sector_{{ $sub->sub_sector_id }}">
                                        <label class="form-check-label" for="sub_sector_{{ $sub->sub_sector_id }}">
                                            {{ $sub->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Layanan yang Diberikan</label>
                        <div class="row">
                            @foreach ($userNeeds as $need)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="user_needs[]"
                                            value="{{ $need->need_id }}" id="user_need_{{ $need->need_id }}">
                                        <label class="form-check-label" for="user_need_{{ $need->need_id }}">
                                            {{ $need->need_name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="expertise_summary">Ringkasan Keahlian</label>
                        <textarea class="form-control" id="expertise_summary" name="expertise_summary"
                            rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="photo_path">Unggah Foto</label>
                        <input type="file" class="form-control-file" id="photo_path" name="photo_path">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('#customSearchInput').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $("#mentorTable tbody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $('#btn-add-mentor').on('click', function () {
            $('#mentorForm').trigger('reset');
            $('#mentor_id').val('');
            $('#mentorForm input[name="sub_sectors[]"]').prop('checked', false);
            $('#mentorForm input[name="user_needs[]"]').prop('checked', false);
            $('#mentorModalLabel').text('Tambah Mentor Baru');
            $('#mentorModal').modal('show');
        });

        $('body').on('click', '.edit-btn', function () {
            var mentorId = $(this).data('id');
            var url = "{{ url('admin/mentors') }}/" + mentorId + "/edit";

            $.get(url, function (data) {
                $('#mentorModalLabel').text('Edit Data Mentor');
                $('#mentorModal').modal('show');

                $('#mentor_id').val(data.mentor_id);
                $('#mentor_name').val(data.mentor_name);
                $('#bio').val(data.bio);
                $('#expertise_summary').val(data.expertise_summary);
                $('#status').val(data.status);

                $('#mentorForm input[name="sub_sectors[]"]').prop('checked', false);

                if (data.creative_sub_sectors) {
                    var subSectorIds = data.creative_sub_sectors.map(function (sub) {
                        return sub.sub_sector_id;
                    });
                    subSectorIds.forEach(function (id) {
                        $('#mentorForm input[name="sub_sectors[]"][value="' + id + '"]').prop('checked', true);
                    });
                }

                $('#mentorForm input[name="user_needs[]"]').prop('checked', false);

                if (data.user_needs) {
                    var userNeedIds = data.user_needs.map(function (need) {
                        return need.need_id;
                    });
                    userNeedIds.forEach(function (id) {
                        $('#mentorForm input[name="user_needs[]"][value="' + id + '"]').prop('checked', true);
                    });
                }
            });
        });

        $('#mentorForm').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            var mentorId = $('#mentor_id').val();
            var url;

            if (mentorId) {
                url = "{{ url('admin/mentors') }}/" + mentorId;
                formData.append('_method', 'PUT');
            } else {
                url = "{{ route('admin.mentors.store') }}";
            }

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    $('#mentorModal').modal('hide');
                    // PERBAIKAN: Menggunakan sintaks objek dengan 'type'
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.success ?? 'Data berhasil disimpan.',
                        type: 'success'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    let errorTitle = 'Error!';
                    let errorHtml = 'Terjadi kesalahan. Silakan coba lagi.';

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            errorTitle = 'Data Tidak Valid!';
                            let errorString = '<ul>';
                            $.each(xhr.responseJSON.errors, function (key, value) {
                                errorString += '<li>' + value + '</li>';
                            });
                            errorString += '</ul>';
                            errorHtml = errorString;
                        }
                        else if (xhr.responseJSON.message) {
                            errorTitle = 'Error Server (500)';
                            errorHtml = '<p><strong>' + xhr.responseJSON.message + '</strong></p>';
                            if (xhr.responseJSON.file) {
                                let fileName = xhr.responseJSON.file.split('\\').pop().split('/').pop();
                                errorHtml += '<small>File: ' + fileName + ' (Baris ' + xhr.responseJSON.line + ')</small>';
                            }
                        }
                    }
                    else if (xhr.status === 500) {
                        errorTitle = 'Error Server (500)';
                        errorHtml = 'Terjadi kesalahan internal pada server. Hubungi administrator.';
                    }

                    // PERBAIKAN: Menggunakan sintaks objek dengan 'type'
                    Swal.fire({
                        title: errorTitle,
                        html: errorHtml,
                        type: 'error'
                    });
                }
            });
        });

        $('body').on('click', '.delete-btn', function (e) {
            e.preventDefault();
            // console.log("Delete button clicked!"); // Baris debug, bisa dihapus
            var mentorId = $(this).data('id');
            var url = "{{ url('admin/mentors') }}/" + mentorId;

            Swal.fire({
                title: 'Anda Yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                type: 'warning', // PERBAIKAN: Mengganti 'icon' menjadi 'type'
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) { // Menggunakan result.value untuk v8
                    $.ajax({
                        url: url,
                        type: "DELETE",
                        success: function (response) {
                            // PERBAIKAN: Menggunakan sintaks objek dengan 'type'
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.success,
                                type: 'success'
                            });
                            $('#row-mentor-' + mentorId).remove();
                        },
                        error: function (xhr) {
                            // PERBAIKAN: Menggunakan sintaks objek dengan 'type'
                            Swal.fire({
                                title: 'Error!',
                                text: 'Gagal menghapus data. Coba lagi.',
                                type: 'error'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@stop