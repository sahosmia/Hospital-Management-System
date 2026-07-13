<?php

namespace App\Http\Controllers;

use App\Models\DoctorReview;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService) {}

    /**
     * List reviews.
     */
    public function index(): JsonResponse
    {
        try {
            $reviews = DoctorReview::with(['doctor', 'patient'])
                ->latest()
                ->get();

            return $this->jsonSuccess('Reviews retrieved successfully.', $reviews);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch reviews: '.$e->getMessage(), 500);
        }
    }

    /**
     * Submit review.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'rating' => 'required|integer|between:1,5',
            'communication_rating' => 'nullable|integer|between:1,5',
            'expertise_rating' => 'nullable|integer|between:1,5',
            'behavior_rating' => 'nullable|integer|between:1,5',
            'cleanliness_rating' => 'nullable|integer|between:1,5',
            'waiting_time_rating' => 'nullable|integer|between:1,5',
            'review' => 'nullable|string',
            'positive_points' => 'nullable|string',
            'negative_points' => 'nullable|string',
            'tags' => 'nullable|array',
            'media' => 'nullable|array',
            'is_anonymous' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $data = $request->all();
            $data['patient_id'] = Auth::id();

            $review = $this->reviewService->submit($data);
            $this->logAudit('SUBMIT_REVIEW', 'doctor_reviews', $review->id, null, $review->toArray());

            return $this->jsonSuccess('Review submitted and pending admin approval.', $review, 201);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to submit review: '.$e->getMessage(), 500);
        }
    }

    /**
     * Approve doctor review.
     */
    public function approve(int $id): JsonResponse
    {
        try {
            $review = DoctorReview::findOrFail($id);
            $oldValue = $review->toArray();

            $approvedReview = $this->reviewService->approve($id, Auth::id());
            $this->logAudit('APPROVE_REVIEW', 'doctor_reviews', $id, $oldValue, $approvedReview->toArray());

            return $this->jsonSuccess('Review approved and ratings updated.', $approvedReview);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to approve review: '.$e->getMessage(), 500);
        }
    }

    /**
     * Doctor replies to a review.
     */
    public function reply(Request $request, int $id): JsonResponse
    {
        try {
            $review = DoctorReview::findOrFail($id);
            $oldValue = $review->toArray();

            if ($review->doctor_id !== Auth::id()) {
                return $this->jsonError('You can only reply to reviews written for you.', 403);
            }
        } catch (\Exception $e) {
            return $this->jsonError('Review not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'reply' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $review->update([
                'doctor_reply' => $request->reply,
                'doctor_replied_at' => now(),
            ]);

            $this->logAudit('REPLY_REVIEW', 'doctor_reviews', $id, $oldValue, $review->toArray());

            return $this->jsonSuccess('Reply added successfully.', $review);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to add reply: '.$e->getMessage(), 500);
        }
    }

    /**
     * Flag review as inappropriate.
     */
    public function flag(Request $request, int $id): JsonResponse
    {
        try {
            $review = DoctorReview::findOrFail($id);
            $oldValue = $review->toArray();
        } catch (\Exception $e) {
            return $this->jsonError('Review not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Validation errors', 422, $validator->errors());
        }

        try {
            $review->update([
                'status' => 'flagged',
                'flagged_reason' => $request->reason,
            ]);

            $this->logAudit('FLAG_REVIEW', 'doctor_reviews', $id, $oldValue, $review->toArray());

            return $this->jsonSuccess('Review flagged and under moderation.', $review);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to flag review: '.$e->getMessage(), 500);
        }
    }

    /**
     * List pending reviews.
     */
    public function pending(): JsonResponse
    {
        try {
            $pending = DoctorReview::where('status', 'pending')
                ->with(['doctor', 'patient'])
                ->latest()
                ->get();

            return $this->jsonSuccess('Pending reviews retrieved successfully.', $pending);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to fetch pending reviews: '.$e->getMessage(), 500);
        }
    }
}
