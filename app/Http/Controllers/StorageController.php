<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;
use App\Http\Requests\StoreStorageRequest;
use App\Http\Requests\UpdateStorageRequest;
use App\Models\Storage;
use Illuminate\Support\Facades\DB;

class StorageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/storages",
     *     summary="List all storages",
     *     tags={"Storages"},
     *     @OA\Response(
     *         response=200,
     *         description="List of storages",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Storage")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Storage::all());
    }

    /**
     * @OA\Post(
     *     path="/storages",
     *     summary="Create a new storage",
     *     tags={"Storages"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Yunusabad Warehouse")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Storage created",
     *         @OA\JsonContent(ref="#/components/schemas/Storage")
     *     ),
     *     @OA\Response(response=422, description="Validation error or name not unique")
     * )
     */
    public function store(StoreStorageRequest $request)
    {
        return response()->json(Storage::create($request->validated()), 201);
    }

     /**
     * @OA\Put(
     *     path="/storages/{id}",
     *     summary="Update a storage",
     *     tags={"Storages"},
     *     @OA\Parameter(
     *         name="id", in="path", required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Main Warehouse")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Storage updated",
     *         @OA\JsonContent(ref="#/components/schemas/Storage")
     *     ),
     *     @OA\Response(response=404, description="Storage not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateStorageRequest $request, Storage $storage)
    {
        $storage->update($request->validated());

        return response()->json($storage);
    }

    /**
     * @OA\Get(
     *     path="/storages/remaining",
     *     summary="Get current remaining stock per storage",
     *     tags={"Storages"},
     *     @OA\Response(
     *         response=200,
     *         description="Remaining stock per storage and product",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="storage_id",   type="integer", example=1),
     *                 @OA\Property(property="storage_name", type="string",  example="Main Warehouse"),
     *                 @OA\Property(property="product_id",   type="integer", example=1),
     *                 @OA\Property(property="product_name", type="string",  example="Ahmad Earl Grey 500g"),
     *                 @OA\Property(property="qty",          type="integer", example=85)
     *             )
     *         )
     *     )
     * )
     */
    public function remaining()
    {
        $rows = DB::table('batch_items as bi')
            ->join('batches as b', 'b.id', '=', 'bi.batch_id')
            ->join('products as p', 'p.id', '=', 'bi.product_id')
            ->join('storages as s', 's.id', '=', 'b.storage_id')
            ->where('bi.remaining_qty', '>', 0)
            ->groupBy('b.storage_id', 'bi.product_id', 's.name', 'p.name')
            ->select(
                'b.storage_id',
                's.name as storage_name',
                'bi.product_id',
                'p.name as product_name',
                DB::raw('SUM(bi.remaining_qty) as qty')
            )
            ->get();

        return response()->json($rows->map(fn($row) => [
            'storage_id'   => $row->storage_id,
            'storage_name' => $row->storage_name,
            'product_id'   => $row->product_id,
            'product_name' => $row->product_name,
            'qty'          => (int) $row->qty,
        ]));
    }
}
