<?php

namespace Tests\Feature\Books;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\CIUnitTestCase;

use CodeIgniter\Test\Fabricator;
use Tests\Support\Models\BookFabricator;
use Tests\Support\Models\CategoryFabricator;
use Tests\Support\Models\CategoryBookFabricator;

class GetTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $refresh     = true;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_only_is_available_success()
    {
        $fabricator = new Fabricator(CategoryFabricator::class);
        $category1 = $fabricator->setOverrides(['name' => 'Horror'])->create();
        $category2 = $fabricator->setOverrides(['name' => 'Fantasia'])->create();

        $fabricator = new Fabricator(BookFabricator::class);
        $book1 = $fabricator->setOverrides(['name' => 'Book 1'])->create();
        $book2 = $fabricator->setOverrides(['name' => 'Book 2', 'is_available' => 0])->create();
        $book3 = $fabricator->setOverrides(['name' => 'Book 3', 'published_at' => '2024-03-01 15:16:16'])->create();

        $fabricator = new Fabricator(CategoryBookFabricator::class);
        $fabricator->setOverrides(['category_id' => $category1->id, 'book_id' => $book1->id])->create();
        $fabricator->setOverrides(['category_id' => $category2->id, 'book_id' => $book1->id])->create();
        $fabricator->setOverrides(['category_id' => $category1->id, 'book_id' => $book2->id])->create();
        $fabricator->setOverrides(['category_id' => $category2->id, 'book_id' => $book2->id])->create();
        $fabricator->setOverrides(['category_id' => $category1->id, 'book_id' => $book3->id])->create();
        $fabricator->setOverrides(['category_id' => $category2->id, 'book_id' => $book3->id])->create();

        $response = $this->get('/api/v1/books');
        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals(2, count($responseJsonDecode));
        $this->assertEquals(2, count($responseJsonDecode[0]->categories));
        $this->assertEquals(2, count($responseJsonDecode[1]->categories));
    }
}