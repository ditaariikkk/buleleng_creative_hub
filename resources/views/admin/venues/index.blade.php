@extends('adminlte::page')

@section('title', 'Daftar Venue - Buleleng Creative Hub')

@section('meta_tags')
{{-- Token CSRF untuk AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Daftar Venue</li>
        </ol>
    </div>
    <div>
        <button type="button" class="btn btn-primary" id="btn-add-venue">
            <i class="fas fa-plus-circle"></i> Tambah Venue
        </button>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-end">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" id="venueSearchInput" class="form-control" placeholder="Cari venue...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap" id="venueTable">
            <thead>
                <tr>
                    <th style="width: 50px" class="text-center">No</th>
                    <th class="text-center">Nama Tempat</th>
                    <th class="text-center">Alamat</th>
                    <th class="text-center">Kontak</th>
                    <th style="width: 200px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {{-- Asumsi controller mengirim variabel $venues --}}
                @forelse ($venues as $venue)
                    <tr class="text-center" id="row-venue-{{ $venue->venue_id }}">
                        <td>{{ $loop->iteration + $venues->firstItem() - 1 }}</td>
                        <td>{{ $venue->venue_name }}</td>
                        <td>{{ Str::limit($venue->address, 50) }}</td>
                        <td>{{ $venue->contact }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.venues.show', $venue->venue_id) }}" class="btn btn-sm btn-info"
                                title="Lihat">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="{{ $venue->venue_id }}"
                                title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $venue->venue_id }}"
                                title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Data venue belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($venues->hasPages())
        <div class="card-footer">
            {{ $venues->links() }}
        </div>
    @endif
</div>

{{-- Modal Tambah/Edit Venue --}}
<div class="modal fade" id="venueModal" tabindex="-1" role="dialog" aria-labelledby="venueModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="venueModalLabel">Form Venue</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="venueForm" name="venueForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="venue_id" name="venue_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="venue_name">Nama Lokasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="venue_name" name="venue_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="owner">Pemilik</label>
                                <input type="text" class="form-control" id="owner" name="owner">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact">Kontak (Email/Nomor HP) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contact" name="contact" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="capacity">Kapasitas</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" min="1">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="photo_path">Unggah Foto Lokasi</label>
                        <input type="file" class="form-control-file" id="photo_path" name="photo_path" accept="image/*">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto. Maks 2MB.</small>
                        <img id="currentPhoto" src="#" alt="Foto Saat Ini"
                            style="max-width: 200px; margin-top: 10px; display: none;" />
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-venue">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        $('#venueSearchInput').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $("#venueTable tbody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        function resetVenueModal() {
            $('#venueForm').trigger('reset');
            $('#venue_id').val('');
            $('#venueModalLabel').text('Tambah Venue Baru');
            $('#currentPhoto').hide().attr('src', '#');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }

        $('#btn-add-venue').on('click', function () {
            resetVenueModal();
            $('#venueModal').modal('show');
        });

        $('#venueForm').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            var venueId = $('#venue_id').val();
            var url = venueId ? "{{ url('admin/venues') }}/" + venueId : "{{ route('admin.venues.store') }}";
            var methodType = "POST";
            if (venueId) formData.append('_method', 'PUT');

            $.ajax({
                url: url, type: methodType, data: formData, contentType: false, processData: false,
                success: function (response) {
                    $('#venueModal').modal('hide');
                    Swal.fire({ title: 'Berhasil!', text: response.success, type: 'success' }).then(() => location.reload());
                },
                error: function (xhr) {
                    let errorString = '<ul>';
                    if (xhr.status == 422) { $.each(xhr.responseJSON.errors, (k, v) => errorString += '<li>' + v[0] + '</li>'); }
                    else if (xhr.responseJSON?.error_message) { errorString += '<li>' + xhr.responseJSON.error_message + '</li>'; }
                    else { errorString += '<li>Error (' + xhr.status + '). Coba lagi.</li>'; console.error(xhr.responseText); }
                    errorString += '</ul>';
                    Swal.fire({ title: 'Data Tidak Valid!', html: errorString, type: 'error' });
                }
            });
        });

        $('body').on('click', '.edit-btn', function () {
            var venueId = $(this).attr('data-id');
            if (!venueId) { Swal.fire('Error!', 'ID Venue tidak ditemukan.', 'error'); return; }
            var url = "{{ url('admin/venues') }}/" + venueId + "/edit";

            $.get(url, function (data) {
                resetVenueModal();
                $('#venueModalLabel').text('Edit Venue');
                $('#venue_id').val(data.venue_id);
                $('#venue_name').val(data.venue_name);
                $('#owner').val(data.owner);
                $('#address').val(data.address);
                $('#contact').val(data.contact);
                $('#capacity').val(data.capacity);

                if (data.photo_path) {
                    $('#currentPhoto').attr('src', "{{ asset('storage') }}/" + data.photo_path).show();
                } else {
                    $('#currentPhoto').hide().attr('src', '#');
                }
                $('#photo_path').val('');

                $('#venueModal').modal('show');
            }).fail(function (xhr) { Swal.fire('Error!', 'Gagal mengambil data. Status: ' + xhr.status, 'error'); console.error(xhr.responseText); });
        });

        $('body').on('click', '.delete-btn', function () {
            var venueId = $(this).attr('data-id');
            if (!venueId) { Swal.fire('Error!', 'ID Venue tidak ditemukan.', 'error'); return; }
            var url = "{{ url('admin/venues') }}/" + venueId;
            Swal.fire({ title: 'Anda Yakin?', text: "Data akan dihapus permanen!", type: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal' })
                .then((result) => { if (result.value) { $.ajax({ url: url, type: "DELETE", success: (res) => { Swal.fire('Berhasil!', res.success, 'success'); $('#row-venue-' + venueId).remove(); }, error: (xhr) => Swal.fire('Error!', 'Gagal menghapus.', 'error') }); } });
        });

        $('#photo_path').on('change', function (event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('currentPhoto');
                output.src = reader.result;
                output.style.display = 'block';
            };
            if (event.target.files[0]) { reader.readAsDataURL(event.target.files[0]); }
            else { $('#currentPhoto').hide().attr('src', '#'); }
        });

    });
</script>
@stop