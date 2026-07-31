<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['name' => 'Korzinka Market'],
            ['name' => 'Makro Supermarket'],
            ['name' => 'Havas Market'],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
