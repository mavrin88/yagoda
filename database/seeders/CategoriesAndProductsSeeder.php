<?php

namespace Database\Seeders;


use App\Models\Category;
use App\Models\Organization;
use App\Models\Product;
use App\Models\Role;
use Illuminate\Database\Seeder;

class CategoriesAndProductsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $organizations = Organization::all();

        foreach ($organizations as $organization) {
            $categories = $organization->categories()->where('hide', '=', true)->get();
            if ($categories->isEmpty()) {
                $category = Category::create([
                    'name' => 'Скрытая категория',
                    'organization_id' => $organization->id,
                    'bill_id' => 1,
                    'hide' => true
                ]);

                Product::create([
                    'name' => 'Товар/Услуга',
                    'category_id' => $category->id,
                    'price' => 0,
                    'hide' => true
                ]);
            }
        }
    }
}
