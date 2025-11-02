@extends('adminlte::page')

@section('title', 'Daftar Events - Buleleng Creative Hub')

@section('meta_tags')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Daftar Acara</li>
        </ol>
    </div>
    <div>
        <button type="button" class="btn btn-primary" id="btn-add-event">
            <i class="fas fa-plus-circle"></i> Tambah Acara
        </button>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header p-0">
        <ul class="nav nav-tabs" id="eventTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="offline-tab" data-toggle="tab" href="#offline" role="tab"
                    aria-controls="offline" aria-selected="true">
                    <i class="fas fa-map-marked-alt mr-2"></i>Luring (Offline)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="online-tab" data-toggle="tab" href="#online" role="tab" aria-controls="online"
                    aria-selected="false">
                    <i class="fas fa-globe mr-2"></i> Daring (Online)
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="eventTabsContent">
            {{-- Tab Offline --}}
            <div class="tab-pane fade show active" id="offline" role="tabpanel" aria-labelledby="offline-tab">
                <div class="d-flex justify-content-end mb-3">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" id="offlineSearchInput" class="form-control"
                            placeholder="Cari acara offline...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                @include('admin.events.partials._event-table', ['events' => $offlineEvents, 'tableId' => 'offlineEventTable'])
            </div>
            {{-- Tab Online --}}
            <div class="tab-pane fade" id="online" role="tabpanel" aria-labelledby="online-tab">
                <div class="d-flex justify-content-end mb-3">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" id="onlineSearchInput" class="form-control"
                            placeholder="Cari acara online...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                @include('admin.events.partials._event-table', ['events' => $onlineEvents, 'tableId' => 'onlineEventTable'])
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah/Edit Acara Multi-Langkah --}}
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Form Acara</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="eventForm" name="eventForm">
                <div class="modal-body">
                    <input type="hidden" id="event_id" name="event_id">

                    {{-- Langkah 1: Detail Acara --}}
                    <div id="step-1">
                        <h5>Langkah 1: Detail Acara</h5>
                        <hr>
                        <div class="form-group">
                            <label for="event_title">Nama Kegiatan</label>
                            <input type="text" class="form-control" id="event_title" name="event_title" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_datetime">Tanggal Mulai</label>
                                    <input type="datetime-local" class="form-control" id="start_datetime"
                                        name="start_datetime" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_datetime">Tanggal Berakhir</label>
                                    <input type="datetime-local" class="form-control" id="end_datetime"
                                        name="end_datetime" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Jenis Acara</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input event-type-radio" type="radio" name="event_type"
                                        id="eventTypeOnline" value="online" required>
                                    <label class="form-check-label" for="eventTypeOnline">Online</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input event-type-radio" type="radio" name="event_type"
                                        id="eventTypeOffline" value="offline" required>
                                    <label class="form-check-label" for="eventTypeOffline">Offline</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="venueField" style="display: none;">
                            <label for="venue_id">Pilih Venue</label>
                            <select class="form-control" id="venue_id" name="venue_id">
                                <option value="" disabled selected>-- Pilih Venue --</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->venue_id }}">{{ $venue->venue_name }}</option>
                                @endforeach
                                <option value="other">Lainnya...</option>
                            </select>
                        </div>
                        <div id="newVenueFieldGroup"
                            style="display: none; border: 1px solid #ddd; padding: 15px; border-radius: 5px; background: #f9f9f9;">
                            <h5>Detail Venue Baru</h5>
                            <div class="form-group">
                                <label for="new_venue_name">Nama Venue Baru</label>
                                <input type="text" class="form-control" id="new_venue_name" name="new_venue_name">
                            </div>
                            <div class="form-group">
                                <label for="new_address">Alamat Venue</label>
                                <textarea class="form-control" id="new_address" name="new_address" rows="2"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group"><label for="new_capacity">Kapasitas</label><input
                                            type="number" class="form-control" id="new_capacity" name="new_capacity"
                                            min="1"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group"><label for="new_contact">Narahubung Venue</label><input
                                            type="text" class="form-control" id="new_contact" name="new_contact"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Langkah 2: Pilih Sub Sektor --}}
                    <div id="step-2" style="display: none;">
                        <h5>Langkah 2: Pilih Sub Sektor Relevan</h5>
                        <hr>
                        <div class="form-group">
                            <label>Sub Sektor Kreatif</label>
                            <div class="row">
                                @foreach ($creativeSubSectors as $sub)
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
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-light" id="btn-prev" style="display: none;">Previous</button>
                    <button type="button" class="btn btn-info" id="btn-next">Next</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-event"
                        style="display: none;">Simpan</button>
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

        var currentStep = 1;
        var totalSteps = 2;
        var editedEventSubSectors = [];

        function goToStep(step) {
            currentStep = step;
            $('#step-1').toggle(step === 1);
            $('#step-2').toggle(step === 2);
            $('#btn-prev').toggle(step > 1);
            $('#btn-next').toggle(step < totalSteps);
            $('#btn-save-event').toggle(step === totalSteps);
        }

        $('#btn-next').on('click', function () {
            if (!$('input[name="event_type"]:checked').val()) {
                Swal.fire('Info', 'Silakan pilih Jenis Acara terlebih dahulu.', 'info'); return;
            }
            if ($('input[name="event_type"]:checked').val() === 'offline' && !$('#venue_id').val()) {
                Swal.fire('Info', 'Silakan pilih Venue untuk acara offline.', 'info'); return;
            }
            if ($('#venue_id').val() === 'other' && !$('#new_venue_name').val()) {
                Swal.fire('Info', 'Silakan isi Nama Venue Baru.', 'info'); return;
            }
            goToStep(currentStep + 1);
        });

        $('#btn-prev').on('click', function () {
            goToStep(currentStep - 1);
        });

        function resetModal() {
            $('#eventForm').trigger('reset');
            $('#event_id').val('');
            $('#eventModalLabel').text('Tambah Acara Baru');
            $('#venueField').hide();
            resetNewVenueFields();
            $('input[name="sub_sectors[]"]').prop('checked', false);
            editedEventSubSectors = [];
            goToStep(1);
        }

        $('#offlineSearchInput').on('keyup', function () { var value = $(this).val().toLowerCase(); $("#offlineEventTable tbody tr").filter(function () { $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1) }); });
        $('#onlineSearchInput').on('keyup', function () { var value = $(this).val().toLowerCase(); $("#onlineEventTable tbody tr").filter(function () { $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1) }); });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) { localStorage.setItem('lastEventTab', $(e.target).attr('href')); });
        var lastTab = localStorage.getItem('lastEventTab'); if (lastTab) { $('#eventTabs a[href="' + lastTab + '"]').tab('show'); }

        function resetNewVenueFields() { $('#newVenueFieldGroup').hide(); $('#new_venue_name').val(''); $('#new_address').val(''); $('#new_capacity').val(''); $('#new_contact').val(''); $('#new_venue_name, #new_address, #new_capacity, #new_contact').prop('required', false); }
        $('input[type=radio][name=event_type]').on('change', function () { if (this.value == 'offline') { $('#venueField').show(); } else { $('#venueField').hide(); $('#venue_id').val(''); resetNewVenueFields(); } });
        $('#venue_id').on('change', function () { if (this.value == 'other') { $('#newVenueFieldGroup').show(); $('#new_venue_name, #new_address, #new_capacity, #new_contact').prop('required', true); } else { resetNewVenueFields(); } });

        $('#btn-add-event').on('click', function () { resetModal(); $('#eventModal').modal('show'); });

        $('#eventForm').on('submit', function (e) {
            e.preventDefault();
            if ($('input[name="sub_sectors[]"]:checked').length === 0) { Swal.fire('Info', 'Pilih setidaknya satu Sub Sektor yang relevan.', 'warning'); return; }

            var formData = new FormData(this);
            var eventId = $('#event_id').val();
            var url = eventId ? "{{ url('admin/events') }}/" + eventId : "{{ route('admin.events.store') }}";
            var methodType = eventId ? "POST" : "POST"; // Always POST for FormData with potential PUT spoofing
            if (eventId) formData.append('_method', 'PUT');

            $.ajax({
                url: url, type: methodType, data: formData, contentType: false, processData: false,
                success: function (response) {
                    $('#eventModal').modal('hide');
                    Swal.fire({ title: 'Berhasil!', text: response.success, type: 'success' }).then(() => { location.reload(); });
                },
                error: function (xhr) {
                    let errorString = '<ul>';
                    if (xhr.status == 422) { $.each(xhr.responseJSON.errors, function (key, value) { errorString += '<li>' + value[0] + '</li>'; }); }
                    else if (xhr.responseJSON && xhr.responseJSON.error_message) { errorString += '<li>' + xhr.responseJSON.error_message + '</li>'; }
                    else { errorString += '<li>Terjadi error (' + xhr.status + '). Silakan coba lagi.</li>'; console.error('Error:', xhr.responseText); }
                    errorString += '</ul>';
                    Swal.fire({ title: 'Data Tidak Valid!', html: errorString, type: 'error' });
                }
            });
        });

        $('body').on('click', '.edit-btn', function () {
            var eventId = $(this).attr('data-id');
            if (!eventId) { Swal.fire('Error!', 'ID Acara tidak ditemukan.', 'error'); return; }
            var url = "{{ url('admin/events') }}/" + eventId + "/edit";

            $.get(url, function (data) {
                resetModal();
                $('#eventModalLabel').text('Edit Acara');
                $('#event_id').val(data.event_id);
                $('#event_title').val(data.event_title);
                $('#description').val(data.description);
                $('#start_datetime').val(data.start_datetime ? data.start_datetime.slice(0, 16) : '');
                $('#end_datetime').val(data.end_datetime ? data.end_datetime.slice(0, 16) : '');
                if (data.event_type == 'offline') { $('#eventTypeOffline').prop('checked', true); $('#venueField').show(); $('#venue_id').val(data.venue_id); }
                else { $('#eventTypeOnline').prop('checked', true); $('#venueField').hide(); }

                // Centang sub sektor yang sudah ada
                if (data.creative_sub_sectors && data.creative_sub_sectors.length > 0) {
                    data.creative_sub_sectors.forEach(function (sub) {
                        $('#sub_sector_' + sub.sub_sector_id).prop('checked', true);
                    });
                }

                goToStep(1);
                $('#eventModal').modal('show');
            }).fail(function (xhr) { Swal.fire('Error!', 'Gagal mengambil data acara. Status: ' + xhr.status, 'error'); console.error(xhr.responseText); });
        });

        $('body').on('click', '.delete-btn', function () {
            var eventId = $(this).attr('data-id');
            if (!eventId) { Swal.fire('Error!', 'ID Acara tidak ditemukan.', 'error'); return; }
            var url = "{{ url('admin/events') }}/" + eventId;
            Swal.fire({ title: 'Anda Yakin?', text: "Data akan dihapus permanen!", type: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal' })
                .then((result) => { if (result.value) { $.ajax({ url: url, type: "DELETE", success: function (response) { Swal.fire('Berhasil!', response.success, 'success'); location.reload(); }, error: function (xhr) { Swal.fire('Error!', 'Gagal menghapus.', 'error'); } }); } });
        });

    });
</script>
@stop