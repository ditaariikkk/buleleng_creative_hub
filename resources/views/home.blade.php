@extends('adminlte::page')

@php
    $userRole = Auth::check() ? Auth::user()->role : null;
    $pageTitle = ($userRole === 'admin') ? 'Dashboard Admin' : 'Dashboard'; // Judul dinamis
    $headerTitle = ($userRole === 'admin') ? 'Dashboard Admin' : 'Selamat Datang, ' . (Auth::user()->name ?? 'Peserta') . '!'; // Header dinamis
@endphp

@section('title', $pageTitle . ' - Buleleng Creative Hub')

@section('content_header')
    <h1><b>{{ $headerTitle }}</b></h1>
@stop

@section('content')

    {{-- ================================================= --}}
    {{-- TAMPILAN JIKA ROLE ADALAH ADMIN --}}
    {{-- ================================================= --}}
    @if ($userRole === 'admin')
        
        {{-- BARIS 1: Statistik Cepat (Dari admin.dashboard) --}}
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{$userCount ?? 0}}</h3>
                        <p>Total Peserta</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{$mentorCount ?? 0}}</h3>
                        <p>Total Mentor</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <a href="{{ route('admin.mentors.index') }}" class="small-box-footer">
                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{$eventCount ?? 0}}</h3>
                        <p>Total Event</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <a href="{{ route('admin.events.index') }}" class="small-box-footer">
                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{$lmsCount ?? 0}}</h3>
                        <p>Total Media Pembelajaran</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <a href="{{ route('admin.lms.index') }}" class="small-box-footer">
                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- BARIS 2: Tabel Peserta (Dari admin.dashboard) --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0">
                        <h2 class="card-title" style="font-weight: 800;">Status Mentor Peserta</h2>
                        <div class=" card-tools">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-tool btn-sm">
                                <i class="fas fa-bars"></i> Lihat Semua Peserta
                            </a>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped table-valign-middle">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama Peserta</th>
                                    <th class="text-center">Keterangan</th>
                                    <th class="text-center">Nama Mentor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($usersWithMentorStatus as $user)
                                    <tr>
                                        <td class="text-center">{{ $user->name }}</td>
                                        <td class="text-center">
                                            @if($user->mentors->isNotEmpty())
                                                <span class="badge badge-success">Sudah Memiliki Mentor</span>
                                            @else
                                                <span class="badge badge-warning">Belum Memiliki Mentor</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ $user->mentors->first()->mentor_name ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada peserta (user) yang terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Script Chart.js HANYA untuk Admin --}}
        @push('js')
            <script>
                @if(isset($mentorChartData) && !$mentorChartData['data']->isEmpty() && $mentorChartData['data']->max() > 0)
                    var mentorChartData = {!! json_encode($mentorChartData) !!};
                    var chartLabels = Object.values(mentorChartData.labels);
                    var chartData = Object.values(mentorChartData.data);
                    var chartColors = Object.values(mentorChartData.colors);

                    var doughnutChartCanvas = $('#mentorDoughnutChart').get(0).getContext('2d');
                    var doughnutData = {
                        labels: chartLabels,
                        datasets: [{ data: chartData, backgroundColor: chartColors }]
                    };
                    var doughnutOptions = {
                        maintainAspectRatio: false, responsive: true,
                        legend: { position: 'right', labels: { boxWidth: 12 } },
                        cutoutPercentage: 50
                    };
                    new Chart(doughnutChartCanvas, { type: 'doughnut', data: doughnutData, options: doughnutOptions });
                @endif
            </script>
        @endpush

    {{-- ================================================= --}}
    {{-- TAMPILAN JIKA ROLE ADALAH USER --}}
    {{-- ================================================= --}}
    @elseif ($userRole === 'user')
    
        {{-- Tampilkan pesan flash --}}
        @if(session('success') && !session('profile_update_success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif
        @if(session('error'))
             <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        {{-- BARIS 1: PRODUK (CAROUSEL) & BERITA (LIST) (Dari user.dashboard) --}}
        <div class="row mb-4">
            <div class="col-md-7">
                <div class="card card-info h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-store mr-2"></i>Produk Etalase Terbaru</h3>
                    </div>
                    <div class="card-body p-0">
                        @if($relatedProducts->isEmpty())
                            <div class="p-3"> <p class="text-muted font-italic">Belum ada produk.</p> </div>
                        @else
                            <div id="productCarousel" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    @foreach($relatedProducts as $index => $product)
                                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                            <img src="{{ $product->photo_path ? asset('storage/' . $product->photo_path) : asset('img/placeholder.png') }}"
                                                 class="d-block w-100" alt="{{ $product->product_name }}"
                                                 style="height: 350px; object-fit: cover;"> 
                                            <div class="carousel-caption d-none d-md-block bg-dark p-2" style="opacity: 0.8; border-radius: 5px;">
                                                <h5>{{ $product->product_name }}</h5>
                                                <p>Oleh: {{ $product->owner }}</p>
                                                <a href="{{ route('user.products.show', $product->product_id) }}" class="btn btn-sm btn-secondary">Detail</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="sr-only">Previous</span></a>
                                <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="sr-only">Next</span></a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card card-info h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="far fa-newspaper mr-2"></i>Berita Terbaru</h3>
                    </div>
                    <div class="card-body" style="overflow-y: auto; max-height: 395px;"> 
                        @if($relatedNews->isEmpty())
                            <p class="text-muted font-italic">Belum ada berita terbaru.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($relatedNews as $news)
                                    <li class="list-group-item">
                                        <a href="{{ route('user.news.show', $news->news_id) }}" class="text-dark">
                                            <strong>{{ $news->title }}</strong>
                                        </a>
                                        <p class="text-muted small mb-1">{{ Str::limit($news->description, 100) }}</p>
                                        <small class="text-muted">{{ $news->created_at->locale('id_ID')->diffForHumans() }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('user.news.index') }}">Lihat Semua Berita</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- BARIS 2: MENTOR (Dari user.dashboard) --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-tie mr-2"></i>Mentor Anda</h3>
                    </div>
                    <div class="card-body">
                        @if($currentMentor)
                            <div class="callout callout-success">
                                <h5>Mentor Anda: <strong>{{ $currentMentor->mentor_name }}</strong></h5>
                                <p>Anda sudah terhubung dengan mentor.</p>
                                <div class="mt-3">
                                    <a href="{{ route('user.mentors.show', $currentMentor->mentor_id) }}" class="btn btn-sm btn-outline-success mr-2">Lihat Profil</a>
                                    <form action="{{ route('mentor.remove') }}" method="POST" class="d-inline remove-mentor-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-times-circle mr-1"></i> Ganti Mentor</button>
                                    </form>
                                </div>
                            </div>
                        @elseif($relatedMentors->isEmpty())
                            <div class="callout callout-warning">
                                <h5>Belum Ada Mentor Tersedia</h5>
                                <p>Belum ada mentor aktif sesuai sub sektor Anda.</p>
                            </div>
                        @else
                            <p>Pilih satu mentor yang relevan dengan sub sektor Anda:</p>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="thead-light"><tr class="text-center"><th>Nama</th><th>Keahlian</th><th>Status</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        @foreach($relatedMentors as $mentor)
                                            <tr class="text-center">
                                                <td>{{ $mentor->mentor_name }}</td>
                                                <td>{{ Str::limit($mentor->expertise_summary ?? '-', 50) }}</td>
                                                <td><span class="badge badge-success">Aktif</span></td>
                                                <td>
                                                    <form class="choose-mentor-form" action="{{ route('mentor.choose', $mentor->mentor_id) }}" method="POST" data-mentor-name="{{ $mentor->mentor_name }}"> 
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-check-circle mr-1"></i> Pilih</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- BARIS 3: EVENT & LMS (Dari user.dashboard) --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card card-warning h-100">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Event Mendatang</h3></div>
                    <div class="card-body">
                        @if($relatedEvents->isEmpty())
                            <p class="text-muted font-italic">Belum ada event mendatang.</p>
                        @else
                            <div class="row">
                                @foreach($relatedEvents as $event)
                                    <div class="col-12 mb-2"> 
                                        <div class="card shadow-sm h-100">
                                            <div class="card-body py-2 px-3 d-flex flex-column">
                                                <h6 class="card-title font-weight-bold mb-1">{{ $event->event_title }}</h6>
                                                <p class="card-text text-muted small mb-1">
                                                     <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($event->start_datetime)->locale('id_ID')->translatedFormat('d M Y') }}
                                                    | <i class="fas fa-map-marker-alt"></i> {{ $event->venue->venue_name ?? 'Online' }}
                                                </p>
                                                <a href="{{ route('user.events.show', $event->event_id) }}" class="btn btn-xs btn-warning align-self-start mt-1">Detail</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('user.events.index') }}">Lihat Semua Event</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                 <div class="card card-danger h-100">
                     <div class="card-header"><h3 class="card-title"><i class="fas fa-book-open mr-2"></i>Media Learning</h3></div>
                     <div class="card-body">
                        @if($relatedLms->isEmpty())
                            <p class="text-muted font-italic">Belum ada media pembelajaran.</p>
                        @else
                            @php $groupedLms = $relatedLms->groupBy('type'); @endphp
                            @foreach($groupedLms as $type => $items)
                                <h6 class="mb-2 mt-2 font-weight-bold">
                                    @if($type == 'article') <i class="fas fa-file-alt text-danger mr-1"></i> Artikel
                                    @elseif($type == 'book') <i class="fas fa-book text-danger mr-1"></i> Buku
                                    @elseif($type == 'video') <i class="fab fa-youtube text-danger mr-1"></i> Video 
                                    @endif
                                </h6>
                                <ul class="list-group list-group-flush mb-3">
                                     @foreach($items as $lms)
                                         <li class="list-group-item d-flex justify-content-between align-items-center py-1 px-0">
                                             <span class="text-truncate" style="max-width: 70%;">{{ $lms->content_title }}</span>
                                             <a href="{{ route('user.lms.show', $lms->content_id) }}" target="_blank" class="btn btn-xs btn-danger"><i class="fas fa-external-link-alt"></i> Buka</a>
                                         </li>
                                     @endforeach
                                </ul>
                            @endforeach
                        @endif
                     </div>
                     <div class="card-footer text-center">
                         <a href="{{ route('user.lms.index') }}">Lihat Semua Media</a>
                     </div>
                </div>
            </div>
        </div>

    @else
        {{-- Fallback jika role tidak dikenali --}}
        <p class="text-danger">Error: Peran pengguna tidak dikenali.</p>
    @endif
@stop

@section('css')
   {{-- CSS hanya dimuat jika rolenya user (untuk carousel) --}}
   @if(Auth::check() && Auth::user()->role === 'user')
        <style> .carousel-item img { max-height: 350px; object-fit: cover; } </style>
   @endif
@stop

@section('js')
   <script>
       $(document).ready(function(){
           
           // Script ini akan berjalan untuk kedua dashboard
           
           @if(Auth::check() && Auth::user()->role === 'user')
               {{-- Jalankan script khusus user --}}
               $('#productCarousel').carousel(); 

               @if(session('profile_update_success'))
                    Swal.fire({ title: 'Berhasil!', text: "{{ session('profile_update_success') }}", type: 'success', });
               @endif
               
               if (typeof Swal !== 'undefined') {
                    // Konfirmasi SweetAlert saat memilih mentor
                    $('.choose-mentor-form').on('submit', function(e) { 
                        e.preventDefault(); var form = this; var mentorName = $(form).data('mentor-name'); 
                        Swal.fire({
                           title: 'Konfirmasi Pemilihan Mentor',
                           text: "Pilih " + mentorName + " sebagai mentor Anda?",
                           type: 'question', showCancelButton: true, 
                           confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
                           confirmButtonText: 'Ya, Pilih', cancelButtonText: 'Batal'
                        }).then((result) => { if (result.value) { form.submit(); } });
                    });
                    
                    // Konfirmasi SweetAlert saat mengganti mentor
                    $('.remove-mentor-form').on('submit', function(e) { 
                         e.preventDefault(); var form = this; 
                         Swal.fire({
                           title: 'Konfirmasi Ganti Mentor',
                           text: "Yakin ingin menghapus mentor saat ini?",
                           type: 'warning', showCancelButton: true, 
                           confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
                           confirmButtonText: 'Ya, Ganti', cancelButtonText: 'Batal'
                        }).then((result) => { if (result.value) { form.submit(); } });
                    });
               } else {
                    console.error("SweetAlert is not loaded!"); 
               }
           @endif

           {{-- Script Chart.js dari admin.dashboard akan di-push ke sini jika rolenya admin --}}
       });
   </script>
@stop

