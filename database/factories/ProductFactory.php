<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->name();
        $slug = Str::slug($title);
        $description = fake()->text();
        $short_description = fake()->text(60);
        $shipping_returns = fake()->text();

        $categories = [7,32,33,37,40];
        $catRandKey = array_rand($categories);

        $subCategories = [3,4,5,6,7,8];
        $subCatRandKey = array_rand($subCategories);

        $brands = [2,3,4,5,6,7];
        $brandRandKey = array_rand($brands);


        return [
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'short_description' => $short_description,
            'shipping_returns' => $shipping_returns,
            'category_id' => $categories[$catRandKey],
            'sub_category_id' => $subCategories[$subCatRandKey],
            'brand_id' => $brands[$brandRandKey],
            'price' => rand(10,1000),
            'sku' => rand(1000,100000),
            'track_qty' => 'Yes',
            'qty' => 10,
            'is_featured' => 'Yes',
            'status' => 1
        ];
    }
}
