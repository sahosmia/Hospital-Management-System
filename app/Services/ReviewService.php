<?php

namespace App\Services;

use App\Models\DoctorRatingStats;
use App\Models\DoctorReview;
use Carbon\Carbon;

class ReviewService
{
    /**
     * Submit a doctor review from a patient.
     */
    public function submit(array $data): DoctorReview
    {
        return DoctorReview::create([
            'doctor_id' => $data['doctor_id'],
            'patient_id' => $data['patient_id'],
            'appointment_id' => $data['appointment_id'] ?? null,
            'rating' => $data['rating'],
            'communication_rating' => $data['communication_rating'] ?? null,
            'expertise_rating' => $data['expertise_rating'] ?? null,
            'behavior_rating' => $data['behavior_rating'] ?? null,
            'cleanliness_rating' => $data['cleanliness_rating'] ?? null,
            'waiting_time_rating' => $data['waiting_time_rating'] ?? null,
            'review' => $data['review'] ?? null,
            'positive_points' => $data['positive_points'] ?? null,
            'negative_points' => $data['negative_points'] ?? null,
            'tags' => $data['tags'] ?? null,
            'media' => $data['media'] ?? null,
            'is_anonymous' => $data['is_anonymous'] ?? false,
            'status' => 'pending',
        ]);
    }

    /**
     * Approve a review and update stats.
     */
    public function approve(int $reviewId, int $adminId): DoctorReview
    {
        $review = DoctorReview::findOrFail($reviewId);
        $review->update([
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => Carbon::now(),
        ]);

        $this->updateStats($review->doctor_id);

        return $review;
    }

    /**
     * Re-calculate and update doctor rating stats.
     */
    public function updateStats(int $doctorId): void
    {
        $reviews = DoctorReview::where('doctor_id', $doctorId)
            ->where('status', 'approved')
            ->get();

        $totalReviews = $reviews->count();
        if ($totalReviews === 0) {
            return;
        }

        $averageRating = $reviews->avg('rating') ?? 0;
        $rating5 = $reviews->where('rating', 5)->count();
        $rating4 = $reviews->where('rating', 4)->count();
        $rating3 = $reviews->where('rating', 3)->count();
        $rating2 = $reviews->where('rating', 2)->count();
        $rating1 = $reviews->where('rating', 1)->count();

        $avgComm = $reviews->whereNotNull('communication_rating')->avg('communication_rating') ?? 0;
        $avgExp = $reviews->whereNotNull('expertise_rating')->avg('expertise_rating') ?? 0;
        $avgBehav = $reviews->whereNotNull('behavior_rating')->avg('behavior_rating') ?? 0;
        $avgClean = $reviews->whereNotNull('cleanliness_rating')->avg('cleanliness_rating') ?? 0;
        $avgWaiting = $reviews->whereNotNull('waiting_time_rating')->avg('waiting_time_rating') ?? 0;

        DoctorRatingStats::updateOrCreate(
            ['doctor_id' => $doctorId],
            [
                'total_reviews' => $totalReviews,
                'average_rating' => $averageRating,
                'rating_5_count' => $rating5,
                'rating_4_count' => $rating4,
                'rating_3_count' => $rating3,
                'rating_2_count' => $rating2,
                'rating_1_count' => $rating1,
                'avg_communication' => $avgComm,
                'avg_expertise' => $avgExp,
                'avg_behavior' => $avgBehav,
                'avg_cleanliness' => $avgClean,
                'avg_waiting_time' => $avgWaiting,
                'last_updated' => Carbon::now(),
            ]
        );
    }
}
