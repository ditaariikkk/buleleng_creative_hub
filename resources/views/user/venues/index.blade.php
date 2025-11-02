@extends('adminlte::page')

@section('title', 'Venue - Buleleng Creative Hub')

@section('content_header')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Venues</li>
</ol>
@stop

@section('content')

{{-- Container untuk Grid Card --}}
<div class="row">
    @forelse ($venues as $venue)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            {{-- Card dibuat clickable untuk memicu modal --}}
            <div class="card h-100 shadow-sm venue-card" data-id="{{ $venue->venue_id }}" data-toggle="modal"
                data-target="#venueDetailModal" style="cursor: pointer;">

                <img src="{{ $venue->photo_path ? asset('storage/' . $venue->photo_path) : asset('img/placeholder.png') }}"
                    class="card-img-top" alt="{{ $venue->venue_name }}" style="height: 200px; object-fit: cover;"
                    onerror="this.onerror=null;this.src='{{ asset('img/placeholder.png') }}';">

                <div class="card-body">
                    <h5 class="card-title font-weight-bold">{{ $venue->venue_name }}</h5>
                    <p class="card-text text-muted small mt-2">
                        <i class="fas fa-map-marker-alt fa-xs"></i>
                        {{ Str::limit($venue->address, 50) }}
                    </p>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                Belum ada venue yang ditambahkan.
            </div>
        </div>
    @endforelse
</div>

{{-- Link Paginasi --}}
<div class="d-flex justify-content-center">
    {{ $venues->links() }}
</div>

{{-- Modal untuk Detail Venue --}}
<div class="modal fade" id="venueDetailModal" tabindex="-1" role="dialog" aria-labelledby="venueDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="modalVenueName">Memuat...</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modalVenueBody" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .venue-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .venue-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#venueDetailModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var venueId = button.data('id');
            var modal = $(this);

            modal.find('.modal-title').text('Memuat...');
            modal.find('#modalVenueBody').html('<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>');

            var url = '{{ route("user.venues.show", ":id") }}';
            url = url.replace(':id', venueId);

            $.get(url, function (data) {
                modal.find('.modal-title').text(data.venue_name);

                var photoUrl = data.photo_path
                    ? '{{ asset('storage') }}/' + data.photo_path
                    : '{{ asset('img/placeholder.png') }}';

                // HTML Grid mirip admin.venues.show
                var html = `
                    <div class="row">
                        <div class="col-md-6">
                             <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: .25rem; padding: 5px;">
                                <img src="${photoUrl}" class="img-fluid rounded shadow-sm" alt="${data.venue_name}" onerror="this.onerror=null;this.src='{{ asset('img/placeholder.png') }}';">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 class="font-weight-bold">Oleh: ${data.owner || 'N/A'}</h4>
                            <hr>
                            <strong><i class="fas fa-map-marker-alt mr-1 text-danger"></i> Alamat:</strong>
                            <p class="text-muted" style="white-space: pre-wrap;">${data.address || 'N/A'}</p>
                            
                            <strong><i class="fas fa-phone-alt mr-1 text-success"></i> Kontak:</strong>
                            <p class="text-muted">${data.contact || 'N/A'}</p>
                            
                            <strong><i class="fas fa-users mr-1 text-info"></i> Kapasitas:</strong>
                            <p class="text-muted">${data.capacity || 'N/A'} Orang</p>
                        </div>
                    </div>
                `;

                modal.find('#modalVenueBody').html(html);

            }).fail(function () {
                modal.find('.modal-title').text('Error');
                modal.find('#modalVenueBody').html('<p class="text-danger">Gagal memuat detail venue. Silakan coba lagi.</p>');
            });
        });
    });
</script>
@stop