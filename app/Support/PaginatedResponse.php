<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaginatedResponse
{
    public static function make(
        AnonymousResourceCollection $resource,
        LengthAwarePaginator $paginator
    ): JsonResponse {
        $raw = $resource->response()->getData(true);

        return response()->json([
            'data' => $raw['data'] ?? [],
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'has_more' => $paginator->hasMorePages(),
            ],
        ]);
    }
}
