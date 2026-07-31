<?php

namespace App\Services;

use App\Models\BatchItem;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{

    public function store(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $neededQtys = collect($data['products'])->pluck('qty', 'id');
            $productIds = $neededQtys->keys()->all();

            $sellingPrices       = Product::whereIn('id', $productIds)->pluck('sale_price', 'id');
            $batchItemsByProduct = BatchItem::whereIn('product_id', $productIds)
                ->where('remaining_qty', '>', 0)
                ->orderBy('batch_id')
                ->lockForUpdate()
                ->get()
                ->groupBy('product_id');

            $this->validateStock($neededQtys, $batchItemsByProduct);

            $order     = Order::create(['client_id' => $data['client_id']]);
            $movements = [];

            foreach ($neededQtys as $productId => $needed) {
                $orderItem = OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $productId,
                    'qty'         => $needed,
                    'sale_price'  => $sellingPrices[$productId],
                ]);

                $remaining = $needed;
                foreach ($batchItemsByProduct->get($productId) as $batchItem) {
                    if ($remaining === 0) break;

                    $take = min($remaining, $batchItem->remaining_qty);

                    $movements[] = [
                        'type'          => 'sale',
                        'batch_item_id' => $batchItem->id,
                        'order_item_id' => $orderItem->id,
                        'qty'           => -$take,
                    ];

                    $batchItem->decrement('remaining_qty', $take);
                    $remaining -= $take;
                }
            }

            InventoryMovement::insert($movements);

            return $order;
        });
    }

    public function refund(array $data): void
    {
        DB::transaction(function () use ($data) {

            $refundMovements = [];
            foreach ($data['items'] as $item) {
                $batchItem = BatchItem::whereKey($item['batch_item_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $refundable = InventoryMovement::where('order_item_id', $item['order_item_id'])
                    ->where('batch_item_id', $item['batch_item_id'])
                    ->whereIn('type', ['sale', 'refund'])
                    ->lockForUpdate()
                    ->selectRaw('-SUM(qty) AS refundable')
                    ->value('refundable');

                if ($item['qty'] > $refundable) {
                    throw ValidationException::withMessages([
                        'items' => "Refund quantity exceeds available quantity.",
                    ]);
                }

                $refundMovements[] = [
                    'type'          => 'refund',
                    'batch_item_id' => $item['batch_item_id'],
                    'order_item_id' => $item['order_item_id'],
                    'qty'           => $item['qty'],
                    'created_at'    => now(),
                ];


                $batchItem->increment('remaining_qty', $item['qty']);
            }
            
            InventoryMovement::insert($refundMovements);
        });
    }

    private function validateStock(Collection $neededQtys, Collection $batchItemsByProduct): void
    {
        foreach ($neededQtys as $productId => $needed) {
            $available = $batchItemsByProduct->get($productId, collect())->sum('remaining_qty');

            if ($available < $needed) {
                throw ValidationException::withMessages([
                    'products' => ["Product #{$productId}: requested {$needed}, available {$available}."],
                ]);
            }
        }
    }


}
