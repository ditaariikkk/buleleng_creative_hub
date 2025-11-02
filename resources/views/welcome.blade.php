<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buleleng Creative Hub - Selamat Datang</title>
    <link rel="icon" type="image/png" href="{{ asset('vendor/favicons/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('vendor/favicons/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('vendor/favicons/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('vendor/favicons/apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('vendor/favicons/site.webmanifest') }}" />

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
        }

        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 2.25rem;
            /* 36px */
            font-weight: 800;
            /* extrabold */
            line-height: 1.2;
            letter-spacing: -0.025em;
        }

        /* --- PERBAIKAN CSS CAROUSEL --- */
        .carousel-container {
            overflow: hidden;
            /* Sembunyikan slide di luar container */
            position: relative;
        }

        .carousel-track {
            display: flex;
            transition: transform 0.5s ease-in-out;
            /* Animasi slide */
        }

        .carousel-item {
            min-width: 100%;
            position: relative;
            /* Penting untuk caption 'absolute' */
            /* Hapus 'display: none' dan 'opacity' */
        }

        /* Hapus .carousel-item.active { display: block; } */

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            z-index: 10;
            cursor: pointer;
        }

        .carousel-btn:hover {
            background-color: white;
        }

        .carousel-btn.prev {
            left: 1rem;
        }

        .carousel-btn.next {
            right: 1rem;
        }

        /* --- AKHIR PERBAIKAN CSS --- */
    </style>
</head>

<body class="bg-gray-100 text-gray-800 antialiased">

    <!-- 1. Header & Navbar -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center">
                    <img class="h-12 w-auto" src="{{ asset('vendor/adminlte/dist/img/Logo_Fix.png') }}" alt="BCH Logo">

                </div>
                <div class="hidden md:flex md:items-center md:space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-blue-800 font-medium">Fitur</a>
                    <a href="#stats" class="text-gray-600 hover:text-blue-800 font-medium">Statistik</a>
                    <a href="#mentors" class="text-gray-600 hover:text-blue-800 font-medium">Mentor</a>
                    <a href="#venues" class="text-gray-600 hover:text-blue-800 font-medium">Venue</a>
                </div>
                <div class="hidden md:flex items-center space-x-2">
                    @auth
                        <a href="{{ route('home') }}"
                            class="px-5 py-2 rounded-full text-sm font-medium text-white bg-blue-700 hover:bg-blue-800">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-5 py-2 rounded-full text-sm font-medium text-gray-700 hover:bg-gray-100">Login</a>
                        <a href="{{ route('register') }}"
                            class="px-5 py-2 rounded-full text-sm font-medium text-white bg-blue-700 hover:bg-blue-800">Register</a>
                    @endauth
                </div>
                <div class="md:hidden">
                    @auth
                        <a href="{{ route('home') }}"
                            class="px-4 py-2 rounded-md text-sm font-medium text-white bg-blue-700 hover:bg-blue-800">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-4 py-2 rounded-md text-sm font-medium text-white bg-blue-700 hover:bg-blue-800">Login</a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero-gradient text-white py-24 sm:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight">
                        Wadah Kreativitas & Inovasi di Buleleng
                    </h1>
                    <p class="mt-6 text-lg sm:text-xl text-blue-100 max-w-2xl">
                        Temukan mentor, ikuti event, pelajari hal baru, dan pamerkan produk Anda. Bergabunglah dengan
                        komunitas kreatif terpadu di Buleleng.
                    </p>
                    <div class="mt-10">
                        <a href="{{ route('register') }}"
                            class="px-8 py-3 rounded-lg text-lg font-semibold text-blue-800 bg-white hover:bg-gray-100 shadow-lg transform hover:scale-105 transition-transform duration-200">
                            Mulai Sekarang <i class="fas fa-arrow-right fa-xs ml-2"></i>
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <img src="{{ asset('vendor/adminlte/dist/img/Design thinking-bro.png') }}"
                        alt="Ilustrasi Buleleng Creative Hub">
                </div>
            </div>
        </section>

        <!-- 2. Etalase (Featured Products Carousel) -->
        @if($featuredProducts->isNotEmpty())
            <section id="etalase" class="py-20 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="section-title text-center mb-12">Etalase Produk Unggulan</h2>
                    <div class="carousel-container relative max-w-4xl mx-auto rounded-lg shadow-xl overflow-hidden">
                        <div class="carousel-track">
                            @foreach ($featuredProducts as $product)
                                {{-- PERBAIKAN: Hapus kelas 'active' dari sini --}}
                                <div class="carousel-item">
                                    <img src="{{ $product->photo_path ? asset('storage/' . $product->photo_path) : asset('img/placeholder.png') }}"
                                        alt="{{ $product->product_name }}" class="w-full h-96 object-cover"
                                        onerror="this.onerror=null;this.src='{{ asset('img/placeholder.png') }}';">
                                    <div class="absolute bottom-0 left-0 right-0 p-4 bg-black bg-opacity-60 text-white">
                                        <h3 class="text-xl font-bold">{{ $product->product_name }}</h3>
                                        <p class="text-sm">Oleh: {{ $product->owner }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-btn prev" aria-label="Previous Slide"><i
                                class="fas fa-chevron-left"></i></button>
                        <button class="carousel-btn next" aria-label="Next Slide"><i
                                class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </section>
        @endif

        <!-- 3. Fitur-fitur -->
        <section id="features" class="py-20 bg-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="section-title text-center mb-16">Fitur Utama Kami</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="feature-card bg-white p-6 rounded-lg shadow-md text-center transition-all duration-300">
                        <i class="fas fa-chalkboard-teacher text-5xl text-blue-700 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Mentor</h3>
                        <p class="text-gray-600">Dapatkan bimbingan dari para ahli di bidang Anda.</p>
                    </div>
                    <div class="feature-card bg-white p-6 rounded-lg shadow-md text-center transition-all duration-300">
                        <i class="fas fa-book-open text-5xl text-red-600 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Media Pembelajaran</h3>
                        <p class="text-gray-600">Akses video, artikel, dan buku untuk tingkatkan skill.</p>
                    </div>
                    <div class="feature-card bg-white p-6 rounded-lg shadow-md text-center transition-all duration-300">
                        <i class="fas fa-calendar-alt text-5xl text-yellow-500 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Events</h3>
                        <p class="text-gray-600">Ikuti workshop dan seminar terbaru.</p>
                    </div>
                    <div class="feature-card bg-white p-6 rounded-lg shadow-md text-center transition-all duration-300">
                        <i class="fas fa-store text-5xl text-purple-600 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Etalase Produk</h3>
                        <p class="text-gray-600">Temukan produk-produk kreatif lokal yang berkualitas dan berdaya saing.
                        </p>
                    </div>
                    <div class="feature-card bg-white p-6 rounded-lg shadow-md text-center transition-all duration-300">
                        <i class="fas fa-map-marked-alt text-5xl text-indigo-600 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Venue</h3>
                        <p class="text-gray-600">Temukan lokasi events maupun lokasi ruang kerja untuk berkreasi.</p>
                    </div>
                    <div class="feature-card bg-white p-6 rounded-lg shadow-md text-center transition-all duration-300">
                        <i class="fas fa-newspaper text-5xl text-gray-700 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Berita</h3>
                        <p class="text-gray-600">Dapatkan update terbaru dari ekosistem kreatif.</p>
                    </div>
                    <div class="feature-card bg-white p-6 rounded-lg shadow-md text-center transition-all duration-300">
                        <i class="fas fa-leaf text-5xl text-green-600 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Green Economy</h3>
                        <p class="text-gray-600">Hitung jejak karbon dan dukung ekonomi hijau.</p>
                    </div>
                    <div class="feature-card bg-white p-6 rounded-lg shadow-md text-center transition-all duration-300">
                        <i class="fab fa-whatsapp text-5xl text-green-500 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Channel</h3>
                        <p class="text-gray-600">Terhubung langsung melalui WhatsApp channel kami.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Statistik (Jumlah-jumlah) -->
        <section id="stats" class="py-20 bg-blue-800 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="section-title text-center mb-12">Komunitas Kami dalam Angka</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8 text-center">
                    <div><span class="text-5xl font-extrabold">{{ $userCount ?? 0 }}</span>
                        <p class="mt-2 text-lg text-blue-200">Total Peserta</p>
                    </div>
                    <div><span class="text-5xl font-extrabold">{{ $mentorCount ?? 0 }}</span>
                        <p class="mt-2 text-lg text-blue-200">Total Mentor</p>
                    </div>
                    <div><span class="text-5xl font-extrabold">{{ $eventCount ?? 0 }}</span>
                        <p class="mt-2 text-lg text-blue-200">Total Event</p>
                    </div>
                    <div><span class="text-5xl font-extrabold">{{ $productCount ?? 0 }}</span>
                        <p class="mt-2 text-lg text-blue-200">Total Produk</p>
                    </div>
                    <div><span class="text-5xl font-extrabold">{{ $lmsCount ?? 0 }}</span>
                        <p class="mt-2 text-lg text-blue-200">Media Belajar</p>
                    </div>
                    <div><span class="text-5xl font-extrabold">{{ $venueCount ?? 0 }}</span>
                        <p class="mt-2 text-lg text-blue-200">Total Venue</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. Mentor Unggulan -->
        @if($featuredMentors->isNotEmpty())
            <section id="mentors" class="py-20 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="section-title text-center mb-12">Mentor Unggulan Kami</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        @foreach ($featuredMentors as $mentor)
                            <div class="text-center">
                                <img src="{{ $mentor->photo_path ? asset('storage/' . $mentor->photo_path) : asset('img/avatar.jpg') }}"
                                    alt="Foto {{ $mentor->mentor_name }}"
                                    class="w-40 h-40 rounded-full mx-auto shadow-lg object-cover"
                                    onerror="this.onerror=null;this.src='{{ asset('img/avatar.jpg') }}';">
                                <h4 class="text-xl font-bold mt-4 mb-1">{{ $mentor->mentor_name }}</h4>
                                <p class="text-gray-600">{{ Str::limit($mentor->expertise_summary, 50) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- 6. Venue Unggulan (Galeri) -->
        @if($featuredVenues->isNotEmpty())
            <section id="venues" class="py-20 bg-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="section-title text-center mb-12">Galeri Venue Kreatif</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach ($featuredVenues as $venue)
                            <div class="relative rounded-lg overflow-hidden shadow-lg group">
                                <img src="{{ $venue->photo_path ? asset('storage/' . $venue->photo_path) : asset('img/placeholder.png') }}"
                                    alt="{{ $venue->venue_name }}"
                                    class="w-full h-64 object-cover transform group-hover:scale-110 transition-transform duration-300"
                                    onerror="this.onerror=null;this.src='{{ asset('img/placeholder.png') }}';">
                                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent">
                                    <h4 class="text-white text-lg font-bold">{{ $venue->venue_name }}</h4>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <img class="h-12 w-auto mx-auto mb-4" src="{{ asset('vendor/adminlte/dist/img/Logo.png') }}" alt="BCH Logo">
            <p>&copy; {{ date('Y') }} Buleleng Creative Hub. Dibuat dengan <i class="fas fa-heart text-red-500"></i> di
                Buleleng.</p>
        </div>
    </footer>

    {{-- PERBAIKAN: Script untuk Carousel Sederhana --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const track = document.querySelector('.carousel-track');
            if (track) {
                const items = Array.from(track.children);
                const nextBtn = document.querySelector('.carousel-btn.next');
                const prevBtn = document.querySelector('.carousel-btn.prev');
                let currentIndex = 0;
                const itemsCount = items.length;

                // Pastikan track tidak kosong
                if (itemsCount === 0) return;

                function updateSlide(targetIndex) {
                    // Bungkus indeks
                    if (targetIndex < 0) {
                        targetIndex = itemsCount - 1;
                    } else if (targetIndex >= itemsCount) {
                        targetIndex = 0;
                    }

                    const amountToMove = -targetIndex * 100;
                    track.style.transform = `translateX(${amountToMove}%)`;
                    currentIndex = targetIndex;
                }

                nextBtn.addEventListener('click', e => {
                    updateSlide(currentIndex + 1);
                });

                prevBtn.addEventListener('click', e => {
                    updateSlide(currentIndex - 1);
                });

                // Setel slide pertama
                track.style.transform = 'translateX(0%)';
            }
        });
    </script>
</body>

</html>