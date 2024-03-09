<?php

namespace Tests\Feature\Users;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\CIUnitTestCase;

use CodeIgniter\Test\Fabricator;
use Tests\Support\Models\UserFabricator;
use Tests\Support\Models\BookFabricator;
use Tests\Support\Models\CategoryFabricator;
use Tests\Support\Models\CategoryBookFabricator;
use Tests\Support\Models\UserBookFabricator;

class GetBackBorrowedBookTest extends CIUnitTestCase
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
        $book = $fabricator->setOverrides(['name' => 'Book', 'author' => 'Jose Ortiz', 'is_available' => 0])->create();

        $fabricator = new Fabricator(CategoryBookFabricator::class);
        $fabricator->setOverrides(['category_id' => $category1->id, 'book_id' => $book->id])->create();
        $fabricator->setOverrides(['category_id' => $category2->id, 'book_id' => $book->id])->create();
        
        $fabricator = new Fabricator(UserFabricator::class);
        $user = $fabricator->create();

        $fabricator = new Fabricator(UserBookFabricator::class);
        $fabricator->setOverrides(['user_id' => $user->id, 'book_id' => $book->id])->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/users/{$user->id}/getBackBorrowedBook", [
            'bookId' => $book->id
        ]);

        $response->assertStatus(204);
        $this->dontSeeInDatabase('users_books',[
            'user_id' => $user->id,
            'book_id' => $book->id,
            'deleted_at' => null
        ]);

        $this->seeInDatabase('books',[
            'id' => $book->id,
            'is_available' => 1,
        ]);
    }

    public function test_book_no_exists()
    {
        $fabricator = new Fabricator(UserFabricator::class);
        $user = $fabricator->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/users/{$user->id}/getBackBorrowedBook", [
            'bookId' => 0
        ]);

        $response->assertStatus(409);
        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El libro no existe.', $responseJsonDecode->message->bookId);
    }
}