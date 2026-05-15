<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    // Test Controller for API routes
    public function hello(): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "message" => "Hello from the API!"
        ]);
    }

}
