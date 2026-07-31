<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $code
 * @property int    $provider_id
 * @property int    $storage_id
 */
class Batch extends Model
{
    protected $fillable = ['code', 'provider_id', 'storage_id'];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

    public function items()
    {
        return $this->hasMany(BatchItem::class);
    }
}
