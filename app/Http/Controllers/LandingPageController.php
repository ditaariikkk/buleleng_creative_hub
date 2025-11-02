<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mentor;
use App\Models\Event;
use App\Models\LmsContent;
use App\Models\Product;
use App\Models\Venue;
use App\Models\News; // Pastikan Anda memiliki model News

class LandingPageController extends Controller
{
    /**
     * Menampilkan landing page dengan data dinamis.
     */
    public function index()
    {
        // 1. Ambil Statistik (Jumlah-jumlah)
        $userCount = User::where('role', 'user')->count();
        $mentorCount = Mentor::count();
        $eventCount = Event::count();
        $productCount = Product::count();
        $venueCount = Venue::count();
        $lmsCount = LmsContent::count();

        // 2. Ambil Item untuk Etalase (Carousel Produk)
        // Mengambil 5 produk terbaru
        $featuredProducts = Product::latest('product_id')->take(5)->get();

        // 3. Ambil Mentor Unggulan
        $featuredMentors = Mentor::where('status', 'Aktif')
            ->inRandomOrder() // Ambil acak
            ->take(4) // Ambil 4 mentor
            ->get();

        // 4. Ambil Venue Unggulan (Galeri)
        $featuredVenues = Venue::inRandomOrder()
            ->take(6) // Ambil 6 venue
            ->get();

        return view('welcome', compact(
            'userCount',
            'mentorCount',
            'eventCount',
            'productCount',
            'venueCount',
            'lmsCount',
            'featuredProducts',
            'featuredMentors',
            'featuredVenues'
        ));
    }
}

