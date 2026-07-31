<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $name
 * @property int    $category_id
 * @property float  $sale_price
 */
class Product extends Model
{
    protected $fillable = ['name', 'category_id', 'sale_price'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function batchItems()
    {
        return $this->hasMany(BatchItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
