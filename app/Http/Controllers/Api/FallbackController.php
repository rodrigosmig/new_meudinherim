<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class FallbackController extends Controller
{
    public function fallback()
    {
        return response()->json(['message' => 'No Route Found.'], Response::HTTP_NOT_FOUND);
    }
}
