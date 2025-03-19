<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\OrderStatistics;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class OrganizationsController extends Controller
{
    public function saveLinksSettings(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:organizations,id',
            'map_link_yandex' => 'nullable',
            'map_link_2gis' => 'nullable',
        ]);

        try {
            $organization = Organization::find($validatedData['id']);

            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Организация не найдена',
                ], 404);
            }

            $organization->map_link_yandex = $validatedData['map_link_yandex'] ?? null;
            $organization->map_link_2gis = $validatedData['map_link_2gis'] ?? null;
            $organization->save();

            return response()->json([
                'success' => true,
                'message' => 'Ссылки успешно обновлены'
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка при обновлении ссылок: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при обновлении ссылок',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function submitReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderStatisticId' => 'required|integer',
            'review' => 'nullable|string|max:500',
            'rating' => 'nullable|string|max:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors(),
            ], 422);
        }

        $orderStatistic = OrderStatistics::find($request->input('orderStatisticId'));
        $orderStatistic->comment = $request->input('review');
        $orderStatistic->rating = $request->input('rating');
        $orderStatistic->save();

        $review = $request->input('review');

        return response()->json([
            'success' => true,
            'message' => 'Отзыв успешно сохранен',
            'review' => $review,
        ]);
    }

}
