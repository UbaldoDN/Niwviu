<?php

namespace Tests\Feature\Books;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\CIUnitTestCase;

use CodeIgniter\Test\Fabricator;
use Tests\Support\Models\BookFabricator;
use Tests\Support\Models\CategoryFabricator;
use Tests\Support\Models\CategoryBookFabricator;

class DeleteTest extends CIUnitTestCase
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

    public function test_success()
    {
        $fabricator = new Fabricator(CategoryFabricator::class);
        $category1 = $fabricator->setOverrides(['name' => 'Horror'])->create();
        $category2 = $fabricator->setOverrides(['name' => 'Fantasia'])->create();

        $fabricator = new Fabricator(BookFabricator::class);
        $book = $fabricator->setOverrides(['name' => 'Book', 'author' => 'JoseFa Ortiz'])->create();

        $fabricator = new Fabricator(CategoryBookFabricator::class);
        $fabricator->setOverrides(['category_id' => $category1->id, 'book_id' => $book->id])->create();
        $fabricator->setOverrides(['category_id' => $category2->id, 'book_id' => $book->id])->create();

        $response = $this->delete("/api/v1/books/{$book->id}");

        $response->assertStatus(204);
        $this->dontSeeInDatabase('books',[
            'name' => 'Book',
            'author' => 'JoseFa Ortiz',
            'deleted_at' => null
        ]);
    }

    public function test_no_delete_is_no_available()
    {
        $fabricator = new Fabricator(CategoryFabricator::class);
        $category1 = $fabricator->setOverrides(['name' => 'Horror'])->create();
        $category2 = $fabricator->setOverrides(['name' => 'Fantasia'])->create();

        $fabricator = new Fabricator(BookFabricator::class);
        $book = $fabricator->setOverrides(['is_available' => 0])->create();

        $fabricator = new Fabricator(CategoryBookFabricator::class);
        $fabricator->setOverrides(['category_id' => $category1->id, 'book_id' => $book->id])->create();
        $fabricator->setOverrides(['category_id' => $category2->id, 'book_id' => $book->id])->create();

        $response = $this->delete("/api/v1/books/{$book->id}");

        $response->assertStatus(409);
        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('No puedes eliminar el libro, no se encuentra disponible.', $responseJsonDecode->message->book);
        $this->seeInDatabase('categories_books',[
            'category_id' => $category1->id,
            'book_id' => $book->id
        ]);

        $this->seeInDatabase('categories_books',[
            'category_id' => $category2->id,
            'book_id' => $book->id
        ]);
    }

    public function test_error_id_no_exist()
    {
        $response = $this->delete("/api/v1/books/0");
        $responseJsonDecode = json_decode($response->getJSON());
        
        $response->assertStatus(409);
        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El campo identificador del libro debe ser válido.', $responseJsonDecode->message->book);
    }
}