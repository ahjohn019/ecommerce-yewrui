<?php

namespace App\Services;

use App\Http\Resources\ApiResponseResource;
use Illuminate\Http\JsonResponse;

abstract class ResponseService
{
    public function respond(array $payload, int $status = 200): JsonResponse
    {
        $response = ApiResponseResource::make([
            'success' => true,
            'code' => $status,
            ...$payload,
        ])->response();

        $response->setStatusCode($status);

        return $response;
    }
}
