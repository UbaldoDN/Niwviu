<?php

namespace Tests\Feature\Users;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\CIUnitTestCase;

use CodeIgniter\Test\Fabricator;
use Tests\Support\Models\UserBookFabricator;
use Tests\Support\Models\UserFabricator;
use Tests\Support\Models\BookFabricator;

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

    public function test_success()
    {
        $fabricator = new Fabricator(UserFabricator::class);
        $user1 = $fabricator->create();
        $user2 = $fabricator->create();

        $fabricator = new Fabricator(BookFabricator::class);
        $book1 = $fabricator->setOverrides(['name' => 'Book', 'author' => 'Author'])->create();
        $book2 = $fabricator->setOverrides(['name' => 'Book One', 'author' => 'Author One'])->create();

        $fabricator = new Fabricator(UserBookFabricator::class);
        $fabricator->setOverrides(['user_id' => $user1->id, 'book_id' => $book1->id])->create();
        $fabricator->setOverrides(['user_id' => $user1->id, 'book_id' => $book2->id])->create();

        $response = $this->get('/api/v1/users');
        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals(2, count($responseJsonDecode));
        $this->assertEquals(2, count($responseJsonDecode[0]->books));
        $this->assertEquals(0, count($responseJsonDecode[1]->books));
    }
}