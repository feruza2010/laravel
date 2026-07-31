<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderCategorySeeder extends Seeder
{
    // Validation rule:
    //   root category  → provider_id required, parent_id must be null
    //   child category → parent_id required,  provider_id must be null

    public function run(): void
    {
        $ahmadTeaCo = Provider::create(['name' => 'Ahmad Tea Co']);
        $unilever = Provider::create(['name' => 'Unilever (Lipton)']);


        // root: provider_id set, no parent_id
        $ahmadTea   = Category::create(['name' => 'Ahmad Tea', 'provider_id' => $ahmadTeaCo->id]);
        $lipton   = Category::create(['name' => 'Lipton', 'provider_id' => $unilever->id]);

        // child: parent_id set, no provider_id
        $blackTea   = Category::create(['name' => 'Black Tea', 'parent_id' => $ahmadTea->id]);
        Category::create(['name' => 'Earl Grey',     'parent_id' => $blackTea->id]);
        Category::create(['name' => 'English Blend', 'parent_id' => $blackTea->id]);

        $greenTea   = Category::create(['name' => 'Green Tea', 'parent_id' => $ahmadTea->id]);
        Category::create(['name' => 'Sencha', 'parent_id' => $greenTea->id]);

        $whiteTea   = Category::create(['name' => 'White Tea', 'parent_id' => $ahmadTea->id]);
        Category::create(['name' => 'Classic White', 'parent_id' => $whiteTea->id]);



        // child: parent_id set, no provider_id
        $teaBags  = Category::create(['name' => 'Tea Bags',   'parent_id' => $lipton->id]);
        Category::create(['name' => 'Yellow Label', 'parent_id' => $teaBags->id]);
        Category::create(['name' => 'Green Label',  'parent_id' => $teaBags->id]);

        $looseTea = Category::create(['name' => 'Loose Leaf', 'parent_id' => $lipton->id]);
        Category::create(['name' => 'Ceylon Black', 'parent_id' => $looseTea->id]);
        Category::create(['name' => 'Darjeeling',   'parent_id' => $looseTea->id]);
    }
}
