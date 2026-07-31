<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $type
 * @property int    $batch_item_id
 * @property int|null $order_item_id
 * @property int    $qty
 */
class InventoryMovement extends Model
{
    public $timestamps = true;

    const UPDATED_AT = null;

    protected $fillable = ['type', 'batch_item_id', 'order_item_id', 'qty'];

    public function batchItem()
    {
        return $this->belongsTo(BatchItem::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
