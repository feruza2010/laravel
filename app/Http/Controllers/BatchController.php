<?php

namespace App\Http\Controllers;

use App\Http\Requests\BatchPurchaseRequest;
use App\Http\Requests\BatchRefundRequest;
use App\Models\Batch;

use App\Services\BatchService;

class BatchController extends Controller
{
    public function __construct(private BatchService $service) {}

    /**
     * @OA\Get(
     *     path="/batches",
     *     summary="List all purchase batches",
     *     tags={"Batches"},
     *     @OA\Response(
     *         response=200,
     *         description="List of batches with items",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id",           type="integer", example=1),
     *                 @OA\Property(property="code",         type="string",  example="BATCH-001"),
     *                 @OA\Property(property="provider",     type="object",
     *                     @OA\Property(property="id",   type="integer", example=1),
     *                     @OA\Property(property="name", type="string",  example="Ahmad Tea Co")
     *                 ),
     *                 @OA\Property(property="storage",      type="object",
     *                     @OA\Property(property="id",   type="integer", example=1),
     *                     @OA\Property(property="name", type="string",  example="Main Warehouse")
     *                 ),
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id",             type="integer", example=1),
     *                         @OA\Property(property="product_id",     type="integer", example=1),
     *                         @OA\Property(property="qty",            type="integer", example=200),
     *                         @OA\Property(property="remaining_qty",  type="integer", example=150),
     *                         @OA\Property(property="purchase_price", type="number",  example=38000),
     *                         @OA\Property(property="product",        type="object",
     *                             @OA\Property(property="id",          type="integer", example=1),
     *                             @OA\Property(property="name",        type="string",  example="Ahmad Earl Grey 500g"),
     *                             @OA\Property(property="sale_price",       type="number",  example=45000),
     *                             @OA\Property(property="category_id", type="integer", example=4)
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $batches = Batch::with(['provider', 'storage', 'items.product'])->get();
        return response()->json($batches);
    }

    /**
     * @OA\Post(
     *     path="/batches/purchase",
     *     summary="Register a new purchase batch",
     *     tags={"Batches"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code","provider_id","storage_id","products"},
     *             @OA\Property(property="code",        type="string",  example="BATCH-001"),
     *             @OA\Property(property="provider_id", type="integer", example=1),
     *             @OA\Property(property="storage_id",  type="integer", example=1),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id",             type="integer", example=1),
     *                     @OA\Property(property="qty",            type="integer", example=100),
     *                     @OA\Property(property="purchase_price", type="number",  example=38000)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Purchase recorded",
     *         @OA\JsonContent(
     *             @OA\Property(property="message",  type="string",  example="Purchased successfully"),
     *             @OA\Property(property="batch_id", type="integer", example=1),
     *             @OA\Property(property="code",     type="string",  example="BATCH-001")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function purchase(BatchPurchaseRequest $request)
    {
        $batch = $this->service->purchase($request->validated());

        return response()->json([
            'message'  => 'Purchased successfully',
            'batch_id' => $batch->id,
            'code'     => $batch->code,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/batches/refund",
     *     summary="Refund unsold products back to provider",
     *     tags={"Batches"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items"},
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="batch_item_id", type="integer", example=2),
     *                     @OA\Property(property="qty",           type="integer", example=10)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Refund processed",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Purchase refund processed"))
     *     ),
     *     @OA\Response(response=404, description="Batch not found"),
     *     @OA\Response(response=422, description="Qty exceeds remaining stock or wrong batch")
     * )
     */
    public function refund(BatchRefundRequest $request)
    {
        $this->service->refund($request->validated());

        return response()->json(['message' => 'Purchase refund processed']);
    }

    /**
     * @OA\Get(
     *     path="/batches/profit",
     *     summary="Get profit report for all batches",
     *     tags={"Batches"},
     *     @OA\Response(
     *         response=200,
     *         description="Profit per batch",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="batch_id", type="integer", example=1),
     *                 @OA\Property(property="provider", type="string",  example="Ahmad Tea Co"),
     *                 @OA\Property(property="cost",     type="number",  example=3800000),
     *                 @OA\Property(property="revenue",  type="number",  example=4500000),
     *                 @OA\Property(property="profit",   type="number",  example=700000)
     *             )
     *         )
     *     )
     * )
     */
    public function profit()
    {
        return response()->json($this->service->profit());
    }
}
