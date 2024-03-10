<?php

namespace Tests\Feature\Books;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\CIUnitTestCase;

use CodeIgniter\Test\Fabricator;
use Tests\Support\Models\BookFabricator;
use Tests\Support\Models\CategoryFabricator;
use Tests\Support\Models\CategoryBookFabricator;

class PutTest extends CIUnitTestCase
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
        $book = $fabricator->setOverrides(['name' => 'Koob', 'author' => 'Ortiz Josefa'])->create();

        $fabricator = new Fabricator(CategoryBookFabricator::class);
        $fabricator->setOverrides(['category_id' => $category1->id, 'book_id' => $book->id])->create();
        $fabricator->setOverrides(['category_id' => $category2->id, 'book_id' => $book->id])->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/books/{$book->id}", [
            'name' => 'Book',
            'author' => 'Josefa Ortiz',
            'publishedAt' => '2024-03-01 00:00:00',
            'categories' => [
                $category1->id, $category2->id
            ]
        ]);

        $response->assertStatus(204);
        $this->dontSeeInDatabase('books',[
            'name' => $book->name,
            'author' => $book->author
        ]);

        $this->seeInDatabase('books',[
            'name' => 'Book',
            'author' => 'Josefa Ortiz',
            'published_at' => '2024-03-01 00:00:00',
            'is_available' => 1
        ]);

        $this->seeInDatabase('categories_books',[
            'category_id' => $category1->id,
            'book_id' => $book->id
        ]);

        $this->seeInDatabase('categories_books',[
            'category_id' => $category2->id,
            'book_id' => $book->id
        ]);
    }

    public function test_success_categories()
    {
        $fabricator = new Fabricator(CategoryFabricator::class);
        $category1 = $fabricator->setOverrides(['name' => 'Horror'])->create();
        $category2 = $fabricator->setOverrides(['name' => 'Fantasia'])->create();
        $category3 = $fabricator->setOverrides(['name' => 'Acción'])->create();
        $category4 = $fabricator->setOverrides(['name' => 'Aventura'])->create();

        $fabricator = new Fabricator(BookFabricator::class);
        $book = $fabricator->setOverrides(['name' => 'Book', 'author' => 'Josefa Ortiz'])->create();

        $fabricator = new Fabricator(CategoryBookFabricator::class);
        $fabricator->setOverrides(['category_id' => $category1->id, 'book_id' => $book->id])->create();
        $fabricator->setOverrides(['category_id' => $category2->id, 'book_id' => $book->id])->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/books/{$book->id}", [
            'name' => $book->name,
            'author' => $book->author,
            'publishedAt' => '2024-03-01 00:00:00',
            'categories' => [
                $category3->id, $category4->id
            ]
        ]);

        $response->assertStatus(204);
        $this->seeInDatabase('categories_books',[
            'category_id' => $category3->id,
            'book_id' => $book->id
        ]);

        $this->seeInDatabase('categories_books',[
            'category_id' => $category4->id,
            'book_id' => $book->id
        ]);

        $this->dontSeeInDatabase('categories_books',[
            'category_id' => $category1->id,
            'book_id' => $book->id,
            'deleted_at' => null
        ]);

        $this->dontSeeInDatabase('categories_books',[
            'category_id' => $category2->id,
            'book_id' => $book->id,
            'deleted_at' => null
        ]);
    }

    public function test_error_author()
    {
        $fabricator = new Fabricator(CategoryFabricator::class);
        $category1 = $fabricator->setOverrides(['name' => 'Horror'])->create();
        $category2 = $fabricator->setOverrides(['name' => 'Fantasia'])->create();

        $fabricator = new Fabricator(BookFabricator::class);
        $book = $fabricator->setOverrides(['name' => 'Book', 'author' => 'Josefa Ortiz'])->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/books/{$book->id}", [
            'name' => 'Book 12345',
            'author' => 'Josefa Ortiz 1234',
            'publishedAt' => '2024-03-01 00:00:00',
            'categories' => [
                $category1->id, $category2->id
            ]
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El autor debe ser texto.', $responseJsonDecode->message->author);
    }

    public function test_error_published_at()
    {
        $fabricator = new Fabricator(CategoryFabricator::class);
        $category1 = $fabricator->setOverrides(['name' => 'Horror'])->create();
        $category2 = $fabricator->setOverrides(['name' => 'Fantasia'])->create();

        $fabricator = new Fabricator(BookFabricator::class);
        $book = $fabricator->setOverrides(['name' => 'Book', 'author' => 'Josefa Ortiz'])->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/books/{$book->id}", [
            'name' => 'Book 12345',
            'author' => 'Josefa Ortiz 1234',
            'publishedAt' => '01-03-2024 30:50:10',
            'categories' => [
                $category1->id, $category2->id
            ]
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El autor debe ser texto.', $responseJsonDecode->message->author);
    }

    public function test_error_name_empty()
    {
        $fabricator = new Fabricator(CategoryFabricator::class);
        $category1 = $fabricator->setOverrides(['name' => 'Horror'])->create();
        $category2 = $fabricator->setOverrides(['name' => 'Fantasia'])->create();

        $fabricator = new Fabricator(BookFabricator::class);
        $book = $fabricator->setOverrides(['name' => 'Book', 'author' => 'Josefa Ortiz'])->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/books/{$book->id}", [
            'author' => 'Josefa Ortiz 1234',
            'publishedAt' => '2024-03-01 00:00:00',
            'categories' => [
                $category1->id, $category2->id
            ]
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El nombre es obligatorio.', $responseJsonDecode->message->name);
    }

    public function test_error_author_empty()
    {
        $fabricator = new Fabricator(CategoryFabricator::class);
        $category1 = $fabricator->setOverrides(['name' => 'Horror'])->create();
        $category2 = $fabricator->setOverrides(['name' => 'Fantasia'])->create();

        $fabricator = new Fabricator(BookFabricator::class);
        $book = $fabricator->setOverrides(['name' => 'Book', 'author' => 'Josefa Ortiz'])->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/books/{$book->id}", [
            'name' => 'Book',
            'publishedAt' => '2024-03-01 00:00:00',
            'categories' => [
                $category1->id, $category2->id
            ]
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El autor es obligatorio.', $responseJsonDecode->message->author);
    }

    public function test_error_published_at_empty()
    {
        $fabricator = new Fabricator(CategoryFabricator::class);
        $category1 = $fabricator->setOverrides(['name' => 'Horror'])->create();
        $category2 = $fabricator->setOverrides(['name' => 'Fantasia'])->create();

        $fabricator = new Fabricator(BookFabricator::class);
        $book = $fabricator->setOverrides(['name' => 'Book', 'author' => 'Josefa Ortiz'])->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/books/{$book->id}", [
            'name' => 'Book',
            'author' => 'Josefa Ortiz',
            'categories' => [
                $category1->id, $category2->id
            ]
        ]);

        $responseJsonDecode = json_decode($response->getJSON());

        $this->assertEquals('El fecha de publicación es obligatorio.', $responseJsonDecode->message->publishedAt);
    }

    public function test_error_categories_empty()
    {
        $fabricator = new Fabricator(BookFabricator::class);
        $book = $fabricator->setOverrides(['name' => 'Book', 'author' => 'Josefa Ortiz'])->create();
        
        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/books/{$book->id}", [
            'name' => 'Book',
            'author' => 'Josefa Ortiz',
            'publishedAt' => '2024-03-01 00:00:00',
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('La categoría es obligatoría.', $responseJsonDecode->message->categories);
    }

    public function test_error_categories_min_1()
    {
        $fabricator = new Fabricator(BookFabricator::class);
        $book = $fabricator->setOverrides(['name' => 'Book', 'author' => 'Josefa Ortiz'])->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/books/{$book->id}", [
            'name' => 'Book',
            'author' => 'Josefa Ortiz',
            'publishedAt' => '2024-03-01 00:00:00',
            'categories' => []
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('Se debe envíar por lo menos una categoría.', $responseJsonDecode->message->categories);
    }

    public function test_error_categories_no_exist()
    {
        $fabricator = new Fabricator(BookFabricator::class);
        $book = $fabricator->setOverrides(['name' => 'Book', 'author' => 'Josefa Ortiz'])->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/books/{$book->id}", [
            'name' => 'Book',
            'author' => 'Josefa Ortiz',
            'publishedAt' => '2024-03-01 00:00:00',
            'categories' => [1]
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('Una o más categorias no existen.', $responseJsonDecode->message->categories);
    }
}