<?php

namespace App\Http\Controllers;


use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\RefundOrderRequest;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    /**
     * @OA\Get(
     *     path="/orders",
     *     summary="List all orders with items and movements",
     *     tags={"Orders"},
     *     @OA\Response(response=200, description="Orders list")
     * )
     */
    public function index()
    {
        $orders = Order::with('items.inventoryMovements')->get(['id', 'client_id', 'created_at']);

        return response()->json($orders);
    }

    /**
     * @OA\Post(
     *     path="/orders",
     *     summary="Create a new order (FIFO stock allocation)",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"client_id","products"},
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id",  type="integer", example=1),
     *                     @OA\Property(property="qty", type="integer", example=5)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message",  type="string",  example="Order created"),
     *             @OA\Property(property="order_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=422, description="Insufficient stock or validation error")
     * )
     */
    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderService->store($request->validated());
        return response()->json(['message' => 'Order created', 'order_id' => $order->id], 201);
    }

    /**
     * @OA\Post(
     *     path="/orders/refund",
     *     summary="Refund items from a client order",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items"},
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="order_item_id", type="integer", example=5),
     *                     @OA\Property(property="batch_item_id", type="integer", example=3),
     *                     @OA\Property(property="qty",           type="integer", example=10)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Refund processed",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Order refund processed"))
     *     ),
     *     @OA\Response(response=422, description="Refund qty exceeds refundable qty")
     * )
     */
    public function refund(RefundOrderRequest $request)
    {
        $this->orderService->refund($request->validated());

        return response()->json(['message' => 'Order refund processed']);
    }
}
