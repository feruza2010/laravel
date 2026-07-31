<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\BatchItem;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BatchService
{

    public function purchase(array $data): Batch
    {
        return DB::transaction(function () use ($data) {
            $batch = Batch::create([
                'code'        => $data['code'],
                'provider_id' => $data['provider_id'],
                'storage_id'  => $data['storage_id'],
            ]);

            $movements = [];

            foreach ($data['products'] as $item) {
                $quantity = $item['qty'];
                $batchItem = BatchItem::create([
                    'batch_id'       => $batch->id,
                    'product_id'     => $item['id'],
                    'qty'            => $quantity,
                    'remaining_qty'  => $quantity,
                    'purchase_price' => $item['purchase_price'],
                ]);

                $movements[] = [
                    'type'          => 'purchase',
                    'batch_item_id' => $batchItem->id,
                    'qty'           => $quantity,
                ];
            }

            InventoryMovement::insert($movements);

            return $batch;
        });
    }

    public function refund(array $data): void
    {
        DB::transaction(function () use ($data) {
            $itemsByBatchItemId = collect($data['items'])->keyBy('batch_item_id');
            $batchItemIds = $itemsByBatchItemId->keys()->all();

            $batchItems = BatchItem::whereIn('id', $batchItemIds)
                ->lockForUpdate()
                ->get();

            if ($batchItems->count() !== \count($batchItemIds)) {
                throw ValidationException::withMessages([
                    'items' => 'Batch item not found.',
                ]);
            }

            $movements = [];

            foreach ($batchItems as $batchItem) {
                $item = $itemsByBatchItemId[$batchItem->id];

                if ($item['qty'] > $batchItem->remaining_qty) {
                    throw ValidationException::withMessages([
                        'items' => "Only {$batchItem->remaining_qty} items are available.",
                    ]);
                }

                $movements[] = [
                    'type'          => 'purchase_refund',
                    'batch_item_id' => $batchItem->id,
                    'order_item_id' => null,
                    'qty'           => -$item['qty'],
                    'created_at'    => now(),
                ];

                $batchItem->decrement('remaining_qty', $item['qty']);
            }

            InventoryMovement::insert($movements);
        });
    }

    public function profit()
    {
        return DB::table('inventory_movements as im')
            ->join('batch_items as bi', 'bi.id', '=', 'im.batch_item_id')
            ->leftJoin('order_items as oi', 'oi.id', '=', 'im.order_item_id')
            ->groupBy('bi.batch_id')
            ->select([
                'bi.batch_id',
                DB::raw("
                    -SUM(
                        CASE
                            WHEN im.type IN ('sale', 'refund')
                                THEN im.qty * oi.sale_price
                            WHEN im.type IN ('purchase', 'purchase_refund')
                                THEN im.qty * bi.purchase_price
                            ELSE 0
                        END
                    ) AS profit
                ")
            ])
            ->get();
    }
}
