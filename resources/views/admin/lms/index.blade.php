@extends('adminlte::page')

@section('title', 'Media Learning - Buleleng Creative Hub')

@section('meta_tags')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Media Pembelajaran</li>
        </ol>
    </div>
    <div>
        <button type="button" class="btn btn-primary" id="btn-add-content">
            <i class="fas fa-plus-circle"></i> Tambah Konten
        </button>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header p-0">
        <ul class="nav nav-tabs" id="lmsTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="article-tab" data-toggle="tab" href="#article" role="tab"
                    aria-controls="article" aria-selected="true">
                    {{-- Ganti ikon jika perlu --}}
                    <i class="fas fa-fw fa-file-alt mr-2"></i> Artikel
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="book-tab" data-toggle="tab" href="#book" role="tab" aria-controls="book"
                    aria-selected="false">
                    <i class="fas fa-fw fa-book mr-2"></i> Buku
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="video-tab" data-toggle="tab" href="#video" role="tab" aria-controls="video"
                    aria-selected="false">
                    <i class="fas fa-fw fa-play mr-2"></i> Video
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="lmsTabsContent">
            {{-- Tab Artikel --}}
            <div class="tab-pane fade show active" id="article" role="tabpanel" aria-labelledby="article-tab">
                <div class="d-flex justify-content-end mb-3">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" id="articleSearchInput" class="form-control" placeholder="Cari artikel...">
                        <div class="input-group-append"><span class="input-group-text"><i
                                    class="fas fa-search"></i></span></div>
                    </div>
                </div>
                @include('admin.lms.partials._lms-table', ['contents' => $articles, 'tableId' => 'articleLmsTable'])
            </div>
            {{-- Tab Buku --}}
            <div class="tab-pane fade" id="book" role="tabpanel" aria-labelledby="book-tab">
                <div class="d-flex justify-content-end mb-3">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" id="bookSearchInput" class="form-control" placeholder="Cari buku...">
                        <div class="input-group-append"><span class="input-group-text"><i
                                    class="fas fa-search"></i></span></div>
                    </div>
                </div>
                @include('admin.lms.partials._lms-table', ['contents' => $books, 'tableId' => 'bookLmsTable'])
            </div>
            {{-- Tab Video --}}
            <div class="tab-pane fade" id="video" role="tabpanel" aria-labelledby="video-tab">
                <div class="d-flex justify-content-end mb-3">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" id="videoSearchInput" class="form-control" placeholder="Cari video...">
                        <div class="input-group-append"><span class="input-group-text"><i
                                    class="fas fa-search"></i></span></div>
                    </div>
                </div>
                @include('admin.lms.partials._lms-table', ['contents' => $videos, 'tableId' => 'videoLmsTable'])
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah/Edit Konten --}}
<div class="modal fade" id="contentModal" tabindex="-1" role="dialog" aria-labelledby="contentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contentModalLabel">Form Konten</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="contentForm" name="contentForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="content_id" name="content_id">

                    <div class="form-group">
                        <label for="content_title">Judul Konten <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="content_title" name="content_title" required>
                    </div>

                    <div class="form-group">
                        <label for="sub_sector_id">Sub Sektor <span class="text-danger">*</span></label>
                        <select class="form-control" id="sub_sector_id" name="sub_sector_id" required>
                            <option value="" disabled selected>-- Pilih Sub Sektor --</option>
                            @foreach($creativeSubSectors as $subSector)
                                <option value="{{ $subSector->sub_sector_id }}">{{ $subSector->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tipe Konten <span class="text-danger">*</span></label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="" disabled selected>-- Pilih Tipe --</option>
                            <option value="article">Artikel</option>
                            <option value="book">Buku</option>
                            <option value="video">Video</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Sumber Konten <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input source-type-radio" type="radio" name="source_type"
                                    id="sourceTypeUrl" value="url" checked>
                                <label class="form-check-label" for="sourceTypeUrl">Gunakan URL</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input source-type-radio" type="radio" name="source_type"
                                    id="sourceTypeFile" value="file">
                                <label class="form-check-label" for="sourceTypeFile">Unggah File</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="sourceUrlField">
                        <label for="source_url">Link URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="source_url" name="source_url"
                            placeholder="https://contoh.com/...">
                        <small class="form-text text-muted">Contoh: Link YouTube, Google Drive, artikel blog,
                            dll.</small>
                    </div>

                    <div class="form-group" id="sourceFileField" style="display: none;">
                        <label for="source_file">Pilih File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="source_file" name="source_file"
                            accept=".pdf,.doc,.docx,.ppt,.pptx">
                        <small class="form-text text-muted">Disarankan: PDF, DOCX, PPTX. Maksimal: 5MB.</small>
                        <div id="currentFileName" class="mt-2 text-muted small"></div>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi Singkat</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-content">Simpan</button>
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

        $('#articleSearchInput').on('keyup', function () { var value = $(this).val().toLowerCase(); $("#articleLmsTable tbody tr").filter(function () { $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1) }); });
        $('#bookSearchInput').on('keyup', function () { var value = $(this).val().toLowerCase(); $("#bookLmsTable tbody tr").filter(function () { $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1) }); });
        $('#videoSearchInput').on('keyup', function () { var value = $(this).val().toLowerCase(); $("#videoLmsTable tbody tr").filter(function () { $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1) }); });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) { localStorage.setItem('lastLmsTab', $(e.target).attr('href')); });
        var lastTab = localStorage.getItem('lastLmsTab'); if (lastTab) { $('#lmsTabs a[href="' + lastTab + '"]').tab('show'); }

        function resetLmsModal() {
            $('#contentForm').trigger('reset');
            $('#content_id').val('');
            $('#contentModalLabel').text('Tambah Konten Baru');
            $('#sourceTypeUrl').prop('checked', true);
            $('#sourceUrlField').show();
            $('#source_url').prop('required', true).val('');
            $('#sourceFileField').hide();
            $('#source_file').prop('required', false).val('');
            $('#currentFileName').text('').data('original-path', ''); // Reset data path juga
        }

        $('#btn-add-content').on('click', function () {
            resetLmsModal();
            $('#contentModal').modal('show');
        });

        $('.source-type-radio').on('change', function () {
            let isUrl = this.value === 'url';
            $('#sourceUrlField').toggle(isUrl);
            $('#source_url').prop('required', isUrl);
            $('#sourceFileField').toggle(!isUrl);
            $('#source_file').prop('required', !isUrl && (!$('#content_id').val() || !$('#currentFileName').data('original-path'))); // Required if file & (add new OR edit without existing file)
            if (isUrl) $('#source_file').val(''); else $('#source_url').val('');
            if (isUrl) $('#currentFileName').text('').data('original-path', ''); // Clear file name if URL selected
        });

        $('#source_file').on('change', function () {
            let hasFile = this.files.length > 0;
            let isEdit = !!$('#content_id').val();
            let hadOldFile = !!$('#currentFileName').data('original-path');

            $('#currentFileName').text(hasFile ? '' : (hadOldFile ? 'File saat ini: ' + $('#currentFileName').data('original-path').split('/').pop() : ''));
            // File becomes required if: adding new OR editing and choosing a new file
            $('#source_file').prop('required', hasFile || (!isEdit && !hadOldFile));
        });

        $('#contentForm').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            var contentId = $('#content_id').val();
            var url, methodType;

            let sourceType = $('input[name="source_type"]:checked').val();
            let sourceUrl = $('#source_url').val();
            let sourceFile = $('#source_file')[0].files[0]; // Get the actual file object
            let hasOldFile = !!$('#currentFileName').data('original-path');

            // Frontend validation
            if (sourceType === 'url' && !sourceUrl) { Swal.fire('Error', 'Link URL wajib diisi.', 'error'); return; }
            if (sourceType === 'file' && !sourceFile && !hasOldFile) { Swal.fire('Error', 'Silakan pilih file untuk diunggah.', 'error'); return; }

            if (contentId) {
                url = "{{ url('admin/lms') }}/" + contentId;
                formData.append('_method', 'PUT');
                methodType = "POST";
            } else {
                url = "{{ route('admin.lms.store') }}";
                methodType = "POST";
            }

            $.ajax({
                url: url, type: methodType, data: formData, contentType: false, processData: false,
                success: function (response) {
                    $('#contentModal').modal('hide');
                    Swal.fire({ title: 'Berhasil!', text: response.success, type: 'success' }).then(() => location.reload());
                },
                error: function (xhr) {
                    let errorString = '<ul>';
                    if (xhr.status == 422) { $.each(xhr.responseJSON.errors, (k, v) => errorString += '<li>' + v[0] + '</li>'); } // Ambil pesan pertama
                    else if (xhr.responseJSON?.error_message) { errorString += '<li>' + xhr.responseJSON.error_message + '</li>'; }
                    else { errorString += '<li>Error (' + xhr.status + '). Coba lagi.</li>'; console.error(xhr.responseText); }
                    errorString += '</ul>';
                    Swal.fire({ title: 'Data Tidak Valid!', html: errorString, type: 'error' });
                }
            });
        });

        $('body').on('click', '.edit-btn', function () {
            var contentId = $(this).attr('data-id');
            if (!contentId) { Swal.fire('Error!', 'ID Konten tidak ditemukan.', 'error'); return; }
            var url = "{{ url('admin/lms') }}/" + contentId + "/edit";

            $.get(url, function (data) {
                resetLmsModal();
                $('#contentModalLabel').text('Edit Konten');
                $('#content_id').val(data.content_id);
                $('#content_title').val(data.content_title);
                $('#description').val(data.description);
                $('#type').val(data.type);
                $('#sub_sector_id').val(data.sub_sector_id);

                let isSourceUrl = data.source && (data.source.startsWith('http://') || data.source.startsWith('https://'));

                if (isSourceUrl) {
                    $('#sourceTypeUrl').prop('checked', true).trigger('change');
                    $('#source_url').val(data.source);
                } else if (data.source) { // Check if source (file path) exists
                    $('#sourceTypeFile').prop('checked', true).trigger('change');
                    $('#currentFileName').text('File saat ini: ' + data.source.split('/').pop());
                    $('#currentFileName').data('original-path', data.source);
                    $('#source_file').prop('required', false); // Upload baru jadi opsional
                } else {
                    // Jika source kosong/null, default ke URL
                    $('#sourceTypeUrl').prop('checked', true).trigger('change');
                }

                $('#contentModal').modal('show');
            }).fail(function (xhr) { Swal.fire('Error!', 'Gagal mengambil data. Status: ' + xhr.status, 'error'); console.error(xhr.responseText); });
        });

        $('body').on('click', '.delete-btn', function () {
            var contentId = $(this).attr('data-id');
            if (!contentId) { Swal.fire('Error!', 'ID Konten tidak ditemukan.', 'error'); return; }
            var url = "{{ url('admin/lms') }}/" + contentId;
            Swal.fire({ title: 'Anda Yakin?', text: "Data akan dihapus permanen!", type: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal' })
                .then((result) => { if (result.value) { $.ajax({ url: url, type: "DELETE", success: (res) => { Swal.fire('Berhasil!', res.success, 'success'); location.reload(); }, error: (xhr) => Swal.fire('Error!', 'Gagal menghapus.', 'error') }); } });
        });
    });
</script>
@stop