<?php

namespace App\Services;

use App\Models\User;
use App\Models\Mentor;
use App\Models\Event;
use App\Models\LmsContent;
use Illuminate\Support\Collection;

class RecommendationService
{
    /**
     * Menghasilkan rekomendasi mentor untuk pengguna.
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getMentorRecommendations(User $user, int $limit = 5): Collection
    {
        if (!$user->userProfile) {
            return collect();
        }

        // 1. Ambil ID sub sektor dari profil pengguna
        $userSubSectorIds = $user->userProfile->creativeSubSectors()->pluck('id');

        if ($userSubSectorIds->isEmpty()) {
            return collect(); // Kembalikan koleksi kosong jika user belum set profil
        }

        // 2. Ambil semua mentor yang memiliki setidaknya satu sub sektor yang sama
        $candidateMentors = Mentor::whereHas('creativeSubSectors', function ($query) use ($userSubSectorIds) {
            $query->whereIn('creative_sub_sector_id', $userSubSectorIds);
        })->with('creativeSubSectors')->get();

        // 3. Hitung skor untuk setiap kandidat
        $scoredMentors = $candidateMentors->map(function ($mentor) use ($userSubSectorIds) {
            $mentorSubSectorIds = $mentor->creativeSubSectors->pluck('id');

            // Hitung berapa banyak sub sektor yang cocok
            $matchCount = $userSubSectorIds->intersect($mentorSubSectorIds)->count();

            // Skor sederhana berdasarkan jumlah kecocokan
            $score = $matchCount * 10; // Beri bobot 10 untuk setiap kecocokan

            return ['item' => $mentor, 'score' => $score];
        });

        // 4. Urutkan berdasarkan skor tertinggi dan ambil sesuai limit
        return $scoredMentors->sortByDesc('score')->take($limit)->pluck('item');
    }

    /**
     * Menghasilkan rekomendasi event untuk pengguna.
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getEventRecommendations(User $user, int $limit = 3): Collection
    {
        if (!$user->userProfile) {
            return collect();
        }

        $userSubSectorIds = $user->userProfile->creativeSubSectors()->pluck('id');

        if ($userSubSectorIds->isEmpty()) {
            return collect();
        }

        $candidateEvents = Event::where('start_datetime', '>=', now()) // Hanya event yang akan datang
            ->whereHas('creativeSubSectors', function ($query) use ($userSubSectorIds) {
                $query->whereIn('creative_sub_sector_id', $userSubSectorIds);
            })->with('creativeSubSectors')->get();

        $scoredEvents = $candidateEvents->map(function ($event) use ($userSubSectorIds) {
            $matchCount = $userSubSectorIds->intersect($event->creativeSubSectors->pluck('id'))->count();
            $score = $matchCount * 10;
            return ['item' => $event, 'score' => $score];
        });

        return $scoredEvents->sortByDesc('score')->take($limit)->pluck('item');
    }

    /**
     * Menghasilkan rekomendasi konten LMS untuk pengguna.
     * Skor dihitung berdasarkan kecocokan sub-sektor (bobot lebih tinggi)
     * dan kecocokan kebutuhan pengguna (bobot lebih rendah).
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getLmsContentRecommendations(User $user, int $limit = 5): Collection
    {
        if (!$user->userProfile) {
            return collect();
        }

        $userSubSectorIds = $user->userProfile->creativeSubSectors()->pluck('id');
        $userNeedIds = $user->userProfile->userNeeds()->pluck('id');

        if ($userSubSectorIds->isEmpty() && $userNeedIds->isEmpty()) {
            return collect(); // Kembalikan koleksi kosong jika profil kosong
        }

        // Ambil kandidat yang cocok setidaknya dengan salah satu sub-sektor ATAU salah satu kebutuhan
        $candidates = LmsContent::where(function ($query) use ($userSubSectorIds, $userNeedIds) {
            if ($userSubSectorIds->isNotEmpty()) {
                $query->whereHas('creativeSubSectors', function ($subQuery) use ($userSubSectorIds) {
                    $subQuery->whereIn('creative_sub_sector_id', $userSubSectorIds);
                });
            }
            if ($userNeedIds->isNotEmpty()) {
                $query->orWhereHas('userNeeds', function ($subQuery) use ($userNeedIds) {
                    $subQuery->whereIn('user_need_id', $userNeedIds);
                });
            }
        })->with('creativeSubSectors', 'userNeeds')->get();

        // Hitung skor untuk setiap kandidat
        $scoredContent = $candidates->map(function ($content) use ($userSubSectorIds, $userNeedIds) {
            $score = 0;

            // Bobot untuk kecocokan sub-sektor (lebih tinggi)
            $subSectorMatchCount = $userSubSectorIds->intersect($content->creativeSubSectors->pluck('id'))->count();
            $score += $subSectorMatchCount * 10;

            // Bobot untuk kecocokan kebutuhan (lebih rendah)
            $needMatchCount = $userNeedIds->intersect($content->userNeeds->pluck('id'))->count();
            $score += $needMatchCount * 5;

            return ['item' => $content, 'score' => $score];
        });

        // Urutkan berdasarkan skor tertinggi dan ambil sesuai limit
        return $scoredContent->sortByDesc('score')->take($limit)->pluck('item');
    }
}