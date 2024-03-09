<?php

namespace Tests\Support\Models;

use App\Models\BookModel;
use Faker\Generator;

class BookFabricator extends BookModel
{
    public function fake(Generator &$faker)
    {
        return [
            'name'  => $faker->name(),
            'author'  => $faker->name(),
            'published_at'  => $faker->dateTime()->format('Y-m-d H:i:s'),
            'is_available' => 1
        ];
    }
}