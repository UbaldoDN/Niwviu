<?php

namespace Tests\Feature\Books;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\CIUnitTestCase;

use CodeIgniter\Test\Fabricator;
use Tests\Support\Models\CategoryFabricator;

class PostTest extends CIUnitTestCase
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

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->post('/api/v1/books', [
            'name' => 'Book',
            'author' => 'Josefa Ortiz',
            'categories' => [
                $category1->id, $category2->id
            ]
        ]);

        $response->assertStatus(201);
        
        $this->seeInDatabase('books',[
            'name' => 'Book',
            'author' => 'Josefa Ortiz',
            'is_available' => 1
        ]);

        $book = new \App\Models\BookModel();
        $this->seeInDatabase('categories_books',[
            'category_id' => $category1->id,
            'book_id' => $book->findAll()[0]->id
        ]);

        $this->seeInDatabase('categories_books',[
            'category_id' => $category2->id,
            'book_id' => $book->findAll()[0]->id
        ]);
    }

    public function test_error_name_text()
    {
        $fabricator = new Fabricator(CategoryFabricator::class);
        $category1 = $fabricator->setOverrides(['name' => 'Horror'])->create();
        $category2 = $fabricator->setOverrides(['name' => 'Fantasia'])->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->post('/api/v1/books', [
            'name' => 'Book 1234',
            'author' => 'Josefa Ortiz',
            'categories' => [
                $category1->id, $category2->id
            ]
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El nombre debe ser texto.', $responseJsonDecode->message->name);
    }

    public function test_error_author_text()
    {
        $fabricator = new Fabricator(CategoryFabricator::class);
        $category1 = $fabricator->setOverrides(['name' => 'Horror'])->create();
        $category2 = $fabricator->setOverrides(['name' => 'Fantasia'])->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->post('/api/v1/books', [
            'name' => 'Book',
            'author' => 'Josefa Ortiz 1234',
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

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->post('/api/v1/books', [
            'author' => 'Josefa Ortiz 1234',
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

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->post('/api/v1/books', [
            'name' => 'Book',
            'categories' => [
                $category1->id, $category2->id
            ]
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El autor es obligatorio.', $responseJsonDecode->message->author);
    }

    public function test_error_categories_empty()
    {
        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->post('/api/v1/books', [
            'name' => 'Book',
            'author' => 'Josefa Ortiz'
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('La categoría es obligatoría.', $responseJsonDecode->message->categories);
    }

    public function test_error_categories_min_1()
    {
        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->post('/api/v1/books', [
            'name' => 'Book',
            'author' => 'Josefa Ortiz',
            'categories' => []
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('Se debe envíar por lo menos una categoría.', $responseJsonDecode->message->categories);
    }

    public function test_error_categories_no_exist()
    {
        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->post('/api/v1/books', [
            'name' => 'Book',
            'author' => 'Josefa Ortiz',
            'categories' => [1]
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('Una o más categorias no existen.', $responseJsonDecode->message->categories);
    }
}