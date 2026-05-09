<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderIndexRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;
use OpenApi\Annotations as OA;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orders
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="List orders",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="min_total", in="query", @OA\Schema(type="number", format="float")),
     *     @OA\Parameter(name="max_total", in="query", @OA\Schema(type="number", format="float")),
     *     @OA\Parameter(name="from_date", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to_date", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function index(OrderIndexRequest $request)
    {
        $validated = $request->validated();
        $perPage = (int)($validated['per_page'] ?? 15);
        $filters = Arr::except($validated, ['per_page']);
        $filters['user_id'] = $request->user()->getAuthIdentifier();

        $orders = $this->orders->paginate($filters, $perPage);

        return $this->orders->respond([
            'data' => OrderResource::collection($orders),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="Create order",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"total","status"},
     *             @OA\Property(property="total", type="number", format="float", example=99.99),
     *             @OA\Property(property="status", type="string", example="pending")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orders->create([
            ...$request->validated(),
            'user_id' => $request->user()->getAuthIdentifier(),
        ]);

        return $this->orders->respond([
            'message' => 'Order created successfully.',
            'data' => new OrderResource($order),
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/orders/{order}",
     *     tags={"Orders"},
     *     summary="Delete order",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="order", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function destroy(Order $order): JsonResponse
    {
        Gate::authorize('delete', $order);

        $this->orders->delete($order);

        return $this->orders->respond([
            'message' => 'Order deleted successfully.',
        ]);
    }
}
