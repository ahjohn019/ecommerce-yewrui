<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiResponseResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'success' => $this->resource['success'] ?? true,
            'code' => $this->resource['code'] ?? 200,
            'message' => $this->resource['message'] ?? null,
            'data' => $this->resource['data'] ?? null,
        ];
    }
}
