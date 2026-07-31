<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int   $id
 * @property int   $batch_id
 * @property int   $product_id
 * @property int   $qty
 * @property int   $remaining_qty
 * @property float $purchase_price
 */
class BatchItem extends Model
{
    protected $fillable = ['batch_id', 'product_id', 'qty', 'remaining_qty', 'purchase_price'];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
