<?php

namespace Tests\Support\Models;

use App\Models\CategoryModel;
use Faker\Generator;

class CategoryFabricator extends CategoryModel
{
    public function fake(Generator &$faker)
    {
        return [
            'name'  => $faker->name(),
            'description'  => $faker->text()
        ];
    }
}
