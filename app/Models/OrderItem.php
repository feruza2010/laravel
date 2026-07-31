<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int   $id
 * @property int   $order_id
 * @property int   $product_id
 * @property int   $batch_item_id
 * @property int   $quantity
 * @property float $selling_price
 */
class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'qty', 'sale_price'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batchItem()
    {
        return $this->belongsTo(BatchItem::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
