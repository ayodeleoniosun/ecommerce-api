<?php

namespace Database\Seeders;

use App\Infrastructure\Models\Inventory\Category;
use App\Infrastructure\Models\Inventory\SubCategory;
use Kdabrow\SeederOnce\SeederOnce;

class CategorySeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('seeders/data/categories.json'));
        $categories = json_decode($json, true);

        foreach ($categories as $category) {
            $parentCategory = Category::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'slug' => $category['slug'],
                ],
            );

            if (isset($category['subcategories'])) {
                foreach ($category['subcategories'] as $subCategory) {
                    SubCategory::updateOrCreate(
                        ['slug' => $subCategory['slug']],
                        [
                            'slug' => $subCategory['slug'],
                            'name' => $subCategory['name'],
                            'category_id' => $parentCategory->id,
                        ],
                    );
                }
            }
        }
    }
}
