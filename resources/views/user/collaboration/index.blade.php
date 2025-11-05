{{--
File ini di-refactor untuk menggunakan AdminLTE, Bootstrap, dan jQuery
berdasarkan contoh etalase Anda.
--}}

@extends('adminlte::page')

@section('title', 'Kolaborasi Pengguna - Buleleng Creative Hub')

@section('content_header')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Kolaborasi Pengguna</li>
</ol>
@stop

@section('content')

{{-- Menampilkan notifikasi sukses --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- Menampilkan notifikasi error --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif


<div class="card card-primary card-outline card-tabs">
    <div class="card-header p-0 pt-1 border-bottom-0">
        <ul class="nav nav-tabs" id="collaborationTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="discover-tab" data-toggle="pill" href="#discover" role="tab"
                    aria-controls="discover" aria-selected="true">
                    <i class="fas fa-search mr-1"></i> Cari Partner
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="my-collaborations-tab" data-toggle="pill" href="#my-collaborations" role="tab"
                    aria-controls="my-collaborations" aria-selected="false">
                    <i class="fas fa-briefcase mr-1"></i> Kolaborasi Saya
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="requests-tab" data-toggle="pill" href="#requests" role="tab"
                    aria-controls="requests" aria-selected="false">
                    <i class="fas fa-bell mr-1"></i> Permintaan Masuk
                    @if($incomingRequests->count() > 0)
                        <span class="badge badge-danger right">{{ $incomingRequests->count() }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="collaborationTabContent">

            {{-- TAB 1: CARI PARTNER --}}
            <div class="tab-pane fade show active" id="discover" role="tabpanel" aria-labelledby="discover-tab">
                <div class="row">
                    @forelse ($usersToDiscover as $user)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            {{-- [PERBAIKAN] Gunakan data-id="$user->user_id" --}}
                            <div class="card h-100 shadow-sm user-card" data-id="{{ $user->user_id }}" data-toggle="modal"
                                data-target="#userDetailModal" style="cursor: pointer;">

                                @php
                                    // [PERBAIKAN] Ambil dari profile->user_photo
                                    $photoPath = $user->profile->user_photo ?? null;
                                    // [PERBAIKAN] Perbaiki placeholder string
                                    $photoUrl = $photoPath ? asset('storage/' . $photoPath) : 'https://placehold.co/400x400/E2E8F0/333?text=' . substr($user->name, 0, 1);
                                @endphp
                                <img src="{{ $photoUrl }}" class="card-img-top" alt="{{ $user->name }}"
                                    style="height: 200px; object-fit: cover;"
                                    onerror="this.onerror=null;this.src='https://placehold.co/400x400/E2E8F0/333?text=User';">

                                <div class="card-body">
                                    <h5 class="card-title font-weight-bold">{{ $user->name }}</h5>
                                    <p class="card-text text-muted small mt-2">
                                        {{-- [PERBAIKAN] Ambil dari profile->business_name --}}
                                        {{ $user->profile->business_name ?? 'Nama Usaha Belum Diisi' }}
                                    </p>
                                    <p class="card-text text-muted small" style="margin-top: -10px;">
                                        {{-- [PERBAIKAN] Ambil dari profile->creativeSubSectors (Banyak) --}}
                                        {{ $user->profile->creativeSubSectors->pluck('name')->implode(', ') ?: 'Sub Sektor Belum Diisi' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                Belum ada pengguna lain yang dapat diajak berkolaborasi.
                            </div>
                        </div>
                    @endforelse
                </div>
                {{-- Link Paginasi --}}
                <div class="d-flex justify-content-center">
                    {{ $usersToDiscover->links() }}
                </div>
            </div>


            <div class="tab-pane fade" id="my-collaborations" role="tabpanel" aria-labelledby="my-collaborations-tab">
                <div class="row">
                    @forelse ($acceptedCollaborations as $collaboration)
                        {{-- Karena ini kolaborasi yg diterima, partner bisa jadi requester ATAU recipient --}}
                        @php
                            $partner = ($collaboration->requester_id == Auth::id())
                                ? $collaboration->recipient
                                : $collaboration->requester;

                            $partnerProfile = $partner->profile ?? null;
                            $photoPath = $partnerProfile->user_photo ?? null;
                            $photoUrl = $photoPath
                                ? asset('storage/' . $photoPath)
                                : 'https://placehold.co/400x400/E2E8F0/333?text=' . substr($partner->name, 0, 1);
                        @endphp

                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            {{-- Kita HAPUS atribut modal agar tidak membingungkan --}}
                            <div class="card h-100 shadow-sm">
                                <img src="{{ $photoUrl }}" class="card-img-top" alt="{{ $partner->name }}"
                                    style="height: 200px; object-fit: cover;"
                                    onerror="this.onerror=null;this.src='https://placehold.co/400x400/E2E8F0/333?text=User';">

                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title font-weight-bold">{{ $partner->name }}</h5>
                                    <p class="card-text text-muted small mt-2">
                                        {{ $partnerProfile->business_name ?? 'Nama Usaha Belum Diisi' }}
                                    </p>
                                    <p class="card-text text-muted small" style="margin-top: -10px;">
                                        {{ $partnerProfile->creativeSubSectors->pluck('name')->implode(', ') ?: 'Sub Sektor Belum Diisi' }}
                                    </p>

                                    {{-- Bagian bawah card --}}
                                    <div class="mt-auto text-center">
                                        <x-bs-status-badge :status="$collaboration->status" />

                                        {{-- LOGIKA TOMBOL WHATSAPP --}}
                                        @php
                                            // Asumsi kolom di tabel profile adalah 'phone'
                                            $rawPhone = $partnerProfile->phone_number ?? null;
                                            $waPhone = null;
                                            if ($rawPhone) {
                                                // Ganti 0 di depan jadi 62
                                                if (substr($rawPhone, 0, 1) === '0') {
                                                    $waPhone = '62' . substr($rawPhone, 1);
                                                }
                                                // Jika sudah 62, biarkan
                                                elseif (substr($rawPhone, 0, 2) === '62') {
                                                    $waPhone = $rawPhone;
                                                }
                                                // Tambahkan 62 jika formatnya aneh (misal 81...)
                                                elseif (is_numeric(substr($rawPhone, 0, 1)) && substr($rawPhone, 0, 1) !== '0') {
                                                    $waPhone = '62' . $rawPhone;
                                                }
                                            }
                                        @endphp

                                        {{-- Tampilkan tombol HANYA jika nomor WA valid --}}
                                        @if ($waPhone)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $waPhone) }}" target="_blank"
                                                class="btn btn-success btn-block btn-sm mt-2">
                                                <i class="fab fa-whatsapp"></i> Hubungi Partner
                                            </a>
                                        @else
                                            <small class="text-muted d-block mt-2">(Partner tidak mencantumkan No. WA)</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                Anda belum memiliki kolaborasi aktif.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- TAB 3: PERMINTAAN (MASUK & TERKIRIM) --}}
            <div class="tab-pane fade" id="requests" role="tabpanel" aria-labelledby="requests-tab">

                {{-- BAGIAN 1: PERMINTAAN MASUK (Kode Anda sudah benar) --}}
                <div class="mb-5">
                    <h4 class="mb-3">Permintaan Masuk</h4>
                    <div class="row">
                        @forelse ($incomingRequests as $request)
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                                <div class="card h-100 shadow-sm">
                                    @php
                                        $requester = $request->requester;
                                        $photoPath = $requester->profile->user_photo ?? null;
                                        $photoUrl = $photoPath ? asset('storage/' . $photoPath) : 'https://placehold.co/400x400/E2E8F0/333?text=' . substr($requester->name, 0, 1);
                                    @endphp
                                    <img src="{{ $photoUrl }}" class="card-img-top" alt="{{ $requester->name }}"
                                        style="height: 200px; object-fit: cover;"
                                        onerror="this.onerror=null;this.src='https://placehold.co/400x400/E2E8F0/333?text=User';">

                                    <div class="card-body">
                                        <h5 class="card-title font-weight-bold">{{ $requester->name }}</h5>
                                        <p class="card-text text-muted small mt-2">
                                            {{ $requester->profile->business_name ?? 'Nama Usaha Belum Diisi' }}
                                        </p>
                                        <p class="card-text text-muted small" style="margin-top: -10px;">
                                            {{ $requester->profile->creativeSubSectors->pluck('name')->implode(', ') ?: 'Sub Sektor Belum Diisi' }}
                                        </p>
                                        <div class="d-flex gap-2 mt-3">
                                            <form
                                                action="{{ route('collaboration.accept', ['collaboration' => $request]) }}"
                                                method="POST" class="w-100">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-block btn-sm">
                                                    <i class="fas fa-check"></i> Terima
                                                </button>
                                            </form>
                                            <form
                                                action="{{ route('collaboration.reject', ['collaboration' => $request]) }}"
                                                method="POST" class="w-100">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-danger btn-block btn-sm">
                                                    <i class="fas fa-times"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    Tidak ada permintaan kolaborasi yang masuk.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <hr>

            </div>
        </div>
    </div>

    {{-- Modal untuk Detail Pengguna --}}
    <div class="modal fade" id="userDetailModal" tabindex="-1" role="dialog" aria-labelledby="userDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="modalUserName">Memuat...</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- Konten detail akan dimuat di sini oleh AJAX --}}
                    <div id="modalUserBody" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>

                    {{-- Form untuk "Ajak Kolaborasi" --}}
                    <form action="{{ route('collaboration.store') }}" method="POST">
                        @csrf
                        {{-- ID user akan diisi oleh jQuery --}}
                        <input type="hidden" name="recipient_id" id="modalRecipientId">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus mr-1"></i> Ajak Kolaborasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @stop

    @section('css')
    <style>
        /* Tambahkan efek hover sederhana pada card */
        .user-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        /* Fix untuk form di dalam flex container */
        .d-flex .btn-block {
            width: 100%;
        }

        .gap-2 {
            gap: 0.5rem;
        }
    </style>
    @stop

    @section('js')
    <script>
        $(document).ready(function () {
            // Event listener saat modal akan ditampilkan
            $('#userDetailModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Card yang diklik
                var userId = button.data('id'); // Ambil ID dari data-id (sekarang user_id)
                var modal = $(this);

                // Reset modal body
                modal.find('.modal-title').text('Memuat...');
                modal.find('#modalUserBody').html('<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>');
                modal.find('#modalRecipientId').val(''); // Kosongkan hidden input

                // Definisikan URL untuk AJAX 
                var url = '{{ route("collaboration.user.show", ":id") }}';
                url = url.replace(':id', userId);

                // Panggil AJAX
                $.get(url, function (data) {
                    // Handle success
                    modal.find('.modal-title').text(data.name);

                    // Isi hidden input dengan ID (user_id)
                    modal.find('#modalRecipientId').val(data.id);

                    // [PERBAIKAN JS] Pastikan kutipan '{{ asset("storage") }}/' benar
                    var photoUrl = data.photo_url
                        ? '{{ asset("storage") }}/' + data.photo_url
                        : 'https://placehold.co/400x400/E2E8F0/333?text=' + data.name.substring(0, 1);

                    // Buat HTML untuk grid di dalam modal
                    // [PERBAIKAN JS] Pastikan kutipan 'https://placehold.co/...' di onerror benar
                    var html = `
                    <div class="row">
                        <div class="col-md-5 text-center">
                            <img src="${photoUrl}" class="img-fluid rounded-circle shadow-sm" alt="${data.name}" style="width: 150px; height: 150px; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/400x400/E2E8F0/333?text=User';">
                        </div>
                        <div class="col-md-7">
                            <h4 class="font-weight-bold">${data.business_name}</h4>
                            <hr>
                            <strong><i class="fas fa-building mr-1 text-primary"></i> Sub Sektor:</strong>
                            <p class="text-muted">${data.sub_sector}</p>
                            
                            <strong><i class="fas fa-info-circle mr-1 text-info"></i> Deskripsi:</strong>
                            <p class="text-muted" style="white-space: pre-wrap;">${data.description}</p>
                        </div>
                    </div>
                `;

                    modal.find('#modalUserBody').html(html);
                }).fail(function () {
                    // Handle error
                    modal.find('.modal-title').text('Error');
                    modal.find('#modalUserBody').html('<p class="text-danger">Gagal memuat detail pengguna. Silakan coba lagi.</p>');
                });
            });
        });
    </script>
    @stop