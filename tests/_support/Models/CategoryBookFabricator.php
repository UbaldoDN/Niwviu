<?php

namespace Tests\Support\Models;

use App\Models\CategoryBookModel;
use Faker\Generator;

class CategoryBookFabricator extends CategoryBookModel
{
    public function fake(Generator &$faker)
    {
        return [
            'category_id'  => 1,
            'book_id'  => 1
        ];
    }
}
