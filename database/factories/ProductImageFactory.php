<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $numberOfImages = 3; // Number of fake images to generate
        $images = [];

        for ($i = 0; $i < $numberOfImages; $i++) {
            $images[] = fake()->imageUrl();
        }

        $products = [102,103,104,105,106,107,108,109,110];
        $productRandKey = array_rand($products);


        return [

            'image' => $images,
            'product_id' => $products[$productRandKey]

        ];
    }
}
