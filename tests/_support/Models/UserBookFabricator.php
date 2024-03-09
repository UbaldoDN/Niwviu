<?php

namespace Tests\Support\Models;

use App\Models\UserBookModel;
use Faker\Generator;

class UserBookFabricator extends UserBookModel
{
    public function fake(Generator &$faker)
    {
        return [
            'user_id'  => 1,
            'book_id'  => 1
        ];
    }
}
