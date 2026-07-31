<?php

namespace Database\Seeders;

use App\Models\Storage;
use Illuminate\Database\Seeder;

class StorageSeeder extends Seeder
{
    public function run(): void
    {
        $storages = [
            ['name' => 'Main Warehouse'],
            ['name' => 'Chilanzar Warehouse'],
            ['name' => 'Yunusabad Warehouse'],
            ['name' => 'Sergeli Warehouse'],
        ];

        foreach ($storages as $storage) {
            Storage::create($storage);
        }
    }
}
