<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    protected $fillable = ['name'];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

}
