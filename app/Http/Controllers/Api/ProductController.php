<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use OpenApi\Annotations as OA;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $products
    ) {
    }

    /**
     * @OA\Get(
     *     path="/products",
     *     tags={"Products"},
     *     summary="List products",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="min_price", in="query", @OA\Schema(type="number", format="float")),
     *     @OA\Parameter(name="max_price", in="query", @OA\Schema(type="number", format="float")),
     *     @OA\Parameter(name="stock_level", in="query", @OA\Schema(type="string", enum={"in_stock","out_of_stock","low_stock"})),
     *     @OA\Parameter(name="low_stock_threshold", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function index(ProductIndexRequest $request)
    {
        $validated = $request->validated();

        $perPage = (int) ($validated['per_page'] ?? 15);
        $filters = Arr::except($validated, ['per_page']);

        $products = $this->products->paginate($filters, $perPage);

        return $this->products->respond([
            'data' => ProductResource::collection($products),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     tags={"Products"},
     *     summary="Create product",
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->products->create($request->validated());

        return $this->products->respond([
            'message' => 'Product created successfully.',
            'data' => new ProductResource($product),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/products/{product}",
     *     tags={"Products"},
     *     summary="Show product",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function show(Product $product): JsonResponse
    {
        $product = $this->products->find($product->id);

        return $this->products->respond([
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/products/{product}",
     *     tags={"Products"},
     *     summary="Update product",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->products->update($product, $request->validated());

        return $this->products->respond([
            'message' => 'Product updated successfully.',
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/products/{product}",
     *     tags={"Products"},
     *     summary="Delete product",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->products->delete($product);

        return $this->products->respond([
            'message' => 'Product deleted successfully.',
        ]);
    }
}
