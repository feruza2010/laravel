<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function availableAtDate(string $date): Collection
    {
        return DB::table('inventory_movements as im')
            ->join('batch_items as bi', 'bi.id', '=', 'im.batch_item_id')
            ->join('products as p', 'p.id', '=', 'bi.product_id')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->whereDate('im.created_at', '<=', $date)
            ->groupBy('p.id', 'p.name', 'c.name', 'p.sale_price')
            ->select('p.id', 'p.name', 'c.name as category_name', 'p.sale_price')
            ->selectRaw('SUM(im.qty) as remaining_qty')
            ->having('remaining_qty', '>', 0)
            ->get();
    }

    public function available(): Collection
    {
        return Product::query()
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('batch_items', function ($join) {
                $join->on('batch_items.product_id', '=', 'products.id')
                    ->where('batch_items.remaining_qty', '>', 0);
            })
            ->select('products.id', 'products.name', 'categories.name as category_name', 'products.sale_price')
            ->selectRaw('SUM(batch_items.remaining_qty) as available_qty')
            ->groupBy('products.id', 'products.name', 'categories.name', 'products.sale_price')
            ->get();
    }
}
