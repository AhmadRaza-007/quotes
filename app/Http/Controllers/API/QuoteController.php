<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteCategory;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function quotes(Request $request, $user_id = 0)
    {
        try {
            $userId = $user_id;

            // Retrieve and paginate quotes
            $quotes = Quote::with('category')
                ->withCount('likes')
                ->withCount('comments')
                ->paginate($request->count ?? 10);

            // Use transform to add is_liked_by_user attribute
            $quotes->getCollection()->transform(function ($quote) use ($userId) {
                $quote->is_liked_by_user = $quote->userLikes($userId)->exists();
                $quote->is_favourite_by_user = $quote->userFavourites($userId)->exists();
                return $quote;
            });

            return response()->json([
                'status' => 'success',
                'data', $quotes
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage() . " on line number: " . $exception->getLine(),
            ], 500);
        }
    }

    public function categories(Request $request)
    {
        try {
            $categories = QuoteCategory::get();
            return response()->json([
                'status' => 'success',
                'data' => $categories,
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage() . " on line number: " . $exception->getLine(),
            ], 500);
        }
    }

    public function categoriesWithQuotes($id = null)
    {
        try {
            if (isset($id)) {
                $categories = QuoteCategory::with('quote')->whereId($id)->get();
            } else {
                $categories = QuoteCategory::with('quote')->get();
            }
            return response()->json([
                'status' => 'success',
                'data' => $categories,
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage() . " on line number: " . $exception->getLine(),
            ], 500);
        }
    }
}
