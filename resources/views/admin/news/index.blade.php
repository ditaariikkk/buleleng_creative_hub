@extends('adminlte::page')

@section('title', 'Daftar Berita - Buleleng Creative Hub')

@section('meta_tags')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Daftar Berita</li>
        </ol>
    </div>
    <div>
        <button type="button" class="btn btn-primary" id="btn-add-news">
            <i class="fas fa-plus-circle"></i> Tambah Berita
        </button>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-end">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" id="newsSearchInput" class="form-control" placeholder="Cari berita...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap" id="newsTable">
            <thead>
                <tr>
                    <th style="width: 50px" class="text-center">No</th>
                    <th class="text-center">Judul Berita</th>
                    <th class="text-center">Deskripsi Singkat</th>
                    <th style="width: 200px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($newsItems as $news)
                    <tr class="text-center" id="row-news-{{ $news->news_id }}">
                        <td>{{ $loop->iteration + $newsItems->firstItem() - 1 }}</td>
                        <td>{{ Str::limit($news->title, 50) }}</td>
                        <td>{{ Str::limit($news->description, 40) }}</td>
                        <td class="text-center">
                            {{-- Target _blank agar link sumber terbuka di tab baru --}}
                            <a href="{{ route('admin.news.show', $news->news_id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                            <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="{{ $news->news_id }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $news->news_id }}">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Data berita belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($newsItems->hasPages())
        <div class="card-footer">
            {{ $newsItems->links() }}
        </div>
    @endif
</div>

{{-- Modal Tambah/Edit Berita --}}
<div class="modal fade" id="newsModal" tabindex="-1" role="dialog" aria-labelledby="newsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newsModalLabel">Form Berita</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="newsForm" name="newsForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="news_id" name="news_id">

                    <div class="form-group">
                        <label for="title">Judul Berita <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="source_url">Link Sumber Berita <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="source_url" name="source_url" required
                            placeholder="https://contoh.com/berita/... ">
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="news_photo">Unggah Foto Berita (Opsional)</label>
                        <input type="file" class="form-control-file" id="news_photo" name="news_photo" accept="image/*">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto. Maksimal
                            2MB.</small>
                        <img id="currentNewsPhoto" src="#" alt="Foto Saat Ini"
                            style="max-width: 200px; margin-top: 10px; display: none;" />
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-news">Simpan</button>
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

        $('#newsSearchInput').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $("#newsTable tbody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        function resetNewsModal() {
            $('#newsForm').trigger('reset');
            $('#news_id').val('');
            $('#newsModalLabel').text('Tambah Berita Baru');
            $('#currentNewsPhoto').hide().attr('src', '#');
            // Hapus kelas error jika ada
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }

        $('#btn-add-news').on('click', function () {
            resetNewsModal();
            $('#newsModal').modal('show');
        });

        $('#newsForm').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            var newsId = $('#news_id').val();
            var url = newsId ? "{{ url('admin/news') }}/" + newsId : "{{ route('admin.news.store') }}";
            var methodType = "POST"; // Selalu POST untuk FormData
            if (newsId) formData.append('_method', 'PUT');

            $.ajax({
                url: url, type: methodType, data: formData, contentType: false, processData: false,
                success: function (response) {
                    $('#newsModal').modal('hide');
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
            var newsId = $(this).attr('data-id');
            if (!newsId) { Swal.fire('Error!', 'ID Berita tidak ditemukan.', 'error'); return; }
            var url = "{{ url('admin/news') }}/" + newsId + "/edit";

            $.get(url, function (data) {
                resetNewsModal(); // Reset dulu
                $('#newsModalLabel').text('Edit Berita');
                $('#news_id').val(data.news_id);
                $('#title').val(data.title);
                $('#description').val(data.description);
                $('#source_url').val(data.source_url);

                if (data.news_photo) {
                    $('#currentNewsPhoto').attr('src', "{{ asset('storage') }}/" + data.news_photo).show();
                } else {
                    $('#currentNewsPhoto').hide().attr('src', '#');
                }
                $('#news_photo').val(''); // Kosongkan input file

                $('#newsModal').modal('show');
            }).fail(function (xhr) { Swal.fire('Error!', 'Gagal mengambil data. Status: ' + xhr.status, 'error'); console.error(xhr.responseText); });
        });

        $('body').on('click', '.delete-btn', function () {
            var newsId = $(this).attr('data-id');
            if (!newsId) { Swal.fire('Error!', 'ID Berita tidak ditemukan.', 'error'); return; }
            var url = "{{ url('admin/news') }}/" + newsId;
            Swal.fire({ title: 'Anda Yakin?', text: "Berita ini akan dihapus!", type: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal' })
                .then((result) => { if (result.value) { $.ajax({ url: url, type: "DELETE", success: (res) => { Swal.fire('Berhasil!', res.success, 'success'); $('#row-news-' + newsId).remove(); }, error: (xhr) => Swal.fire('Error!', 'Gagal menghapus.', 'error') }); } });
        });

        // Preview Foto
        $('#news_photo').on('change', function (event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('currentNewsPhoto');
                output.src = reader.result;
                output.style.display = 'block';
            };
            if (event.target.files[0]) { reader.readAsDataURL(event.target.files[0]); }
            else { $('#currentNewsPhoto').hide().attr('src', '#'); }
        });

    });
</script>
@stop