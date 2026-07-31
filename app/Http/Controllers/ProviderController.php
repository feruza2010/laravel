<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProviderRequest;
use App\Models\Provider;

class ProviderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/providers",
     *     summary="List all providers",
     *     tags={"Providers"},
     *     @OA\Response(
     *         response=200,
     *         description="List of providers",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Provider")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Provider::all());
    }

    /**
     * @OA\Post(
     *     path="/providers",
     *     summary="Create a new provider",
     *     tags={"Providers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Ahmad Tea Co")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Provider created",
     *         @OA\JsonContent(ref="#/components/schemas/Provider")
     *     ),
     *     @OA\Response(response=422, description="Validation error or name not unique")
     * )
     */
    public function store(StoreProviderRequest $request)
    {
        return response()->json(Provider::create($request->validated()), 201);
    }

}
