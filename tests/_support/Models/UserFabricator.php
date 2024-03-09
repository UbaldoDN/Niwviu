<?php

namespace Tests\Support\Models;

use App\Models\UserModel;
use Faker\Generator;

class UserFabricator extends UserModel
{
    public function fake(Generator &$faker)
    {
        return [
            'name'  => $faker->name(),
            'email'  => $faker->email()
        ];
    }
}