<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Storages API",
 *     version="1.0.0",
 *     description="Warehouse management system API"
 * )
 *
 * @OA\Server(url=L5_SWAGGER_CONST_HOST)
 *
 * @OA\Schema(
 *     schema="CategoryChild",
 *     @OA\Property(property="id",        type="integer", example=4),
 *     @OA\Property(property="name",      type="string",  example="Earl Grey"),
 *     @OA\Property(property="parent_id", type="integer", example=3),
 *     @OA\Property(
 *         property="children",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/CategoryChild")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Category",
 *     @OA\Property(property="id",          type="integer", example=1),
 *     @OA\Property(property="name",        type="string",  example="Ahmad Tea"),
 *     @OA\Property(property="parent_id",   type="integer", nullable=true, example=null),
 *     @OA\Property(property="provider_id", type="integer", nullable=true, example=1),
 *     @OA\Property(
 *         property="provider",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id",   type="integer", example=1),
 *         @OA\Property(property="name", type="string",  example="Ahmad Tea Co")
 *     ),
 *     @OA\Property(
 *         property="children",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/CategoryChild")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Provider",
 *     @OA\Property(property="id",         type="integer", example=1),
 *     @OA\Property(property="name",       type="string",  example="Ahmad Tea Co"),
 *     @OA\Property(property="created_at", type="string",  format="date-time", example="2026-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string",  format="date-time", example="2026-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Storage",
 *     @OA\Property(property="id",         type="integer", example=1),
 *     @OA\Property(property="name",       type="string",  example="Main Warehouse"),
 *     @OA\Property(property="created_at", type="string",  format="date-time", example="2026-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string",  format="date-time", example="2026-01-01T00:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Product",
 *     @OA\Property(property="id",          type="integer", example=1),
 *     @OA\Property(property="name",        type="string",  example="Ahmad Earl Grey 500g"),
 *     @OA\Property(property="category_id", type="integer", example=4),
 *     @OA\Property(property="sale_price",       type="number",  example=45000)
 * )
 */
class SwaggerInfo
{
}
