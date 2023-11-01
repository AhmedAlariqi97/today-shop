<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class SubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,20,22,25,28,30];
        $catRandKey = array_rand($categories);

        return [
            'name' => fake()->name(),
            'slug' => fake()->name(),
            'status' => rand(0,1),
            'category_id' => $categories[$catRandKey]
        ];
    }
}
