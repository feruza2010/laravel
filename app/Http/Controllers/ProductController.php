<?php

namespace App\Http\Controllers;

use App\Http\Requests\RemainingAtDateRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/products",
     *     summary="List all products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="List of products",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Product::with('category')->get());
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","category_id","sale_price"},
     *             @OA\Property(property="name",        type="string",  example="Ahmad Earl Grey 500g"),
     *             @OA\Property(property="category_id", type="integer", example=4),
     *             @OA\Property(property="sale_price",       type="number",  example=45000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=422, description="Validation error or name not unique")
     * )
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return response()->json($product->load('category'), 201);
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Get a product",
     *     tags={"Products"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Product detail",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function show(Product $product)
    {
        return response()->json($product->load('category'));
    }

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     summary="Update a product",
     *     tags={"Products"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name",        type="string",  example="Ahmad Earl Grey 500g"),
     *             @OA\Property(property="category_id", type="integer", example=4),
     *             @OA\Property(property="sale_price",       type="number",  example=45000)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product updated", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response=404, description="Product not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return response()->json($product->load('category'));
    }


    /**
     * @OA\Get(
     *     path="/products/available",
     *     summary="List all products that have stock",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Products with available quantity",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id",            type="integer", example=1),
     *                 @OA\Property(property="name",          type="string",  example="Ahmad Earl Grey 500g"),
     *                 @OA\Property(property="category_name", type="string",  example="Black Tea"),
     *                 @OA\Property(property="sale_price",         type="number",  example=45000),
     *                 @OA\Property(property="qty",           type="integer", example=120)
     *             )
     *         )
     *     )
     * )
     */
    public function available(ProductService $service)
    {
        return response()->json($service->available());
    }

    /**
     * @OA\Get(
     *     path="/products/remaining",
     *     summary="Remaining product quantities as of a given date",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="date", in="query", required=true,
     *         @OA\Schema(type="string", format="date", example="2025-06-01")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Remaining quantities per product at the given date",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id",            type="integer", example=1),
     *                 @OA\Property(property="name",          type="string",  example="Ahmad Earl Grey 500g"),
     *                 @OA\Property(property="category_name", type="string",  example="Black Tea"),
     *                 @OA\Property(property="sale_price",    type="number",  example=45000),
     *                 @OA\Property(property="remaining_qty", type="integer", example=80)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function remainingAtDate(RemainingAtDateRequest $request, ProductService $service)
    {
        return response()->json($service->availableAtDate($request->input('date')));
    }
}
