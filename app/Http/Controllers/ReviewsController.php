<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ReviewsResource;
use App\Models\Reviews;

class ReviewsController extends Controller
{
    public function createReview(Request $request)
    {
        $data = $request->validate([
            'comment' => 'required|string',
            'color' => 'required|string',
        ]);
        $data['user_id'] = auth()->id();
        $review = Reviews::create($data);
        $createdReview = Reviews::with('user')->findOrFail($review->id);

        return $this->sendResponse(ReviewsResource::make($createdReview)
                ->response()
                ->getData(true),'Review added successfully');
    }

    public function getReviews()
    {
        $reviews = Reviews::with('user')->paginate(20);
        return $this->sendResponse(ReviewsResource::collection($reviews)
                ->response()
                ->getData(true), 'Reviews retrieved successfully');
    }

    public function getAReview(Reviews $review)
    {
        $review->load('user');
        return $this->sendResponse(ReviewsResource::make($review)
                ->response()
                ->getData(true), 'Review retrieved successfully');
    }

    public function updateReview(Request $request, Reviews $review)
    {
        $review->is_approved = !$review->is_approved;
        $review->save();

        $updatedReview = Reviews::with('user')->findOrFail($review->id);
        return $this->sendResponse(ReviewsResource::make($updatedReview)
                ->response()
                ->getData(true), 'Review status updated successfully');
    }

    public function deleteReview(Reviews $review)
    {
        $review->delete();
        return $this->sendResponse([], 'Review deleted successfully');
    }
}
