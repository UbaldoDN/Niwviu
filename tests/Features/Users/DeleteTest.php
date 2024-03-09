<?php

namespace Tests\Feature\Users;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\CIUnitTestCase;

use CodeIgniter\Test\Fabricator;
use Tests\Support\Models\UserFabricator;

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
        $fabricator = new Fabricator(UserFabricator::class);
        $test = $fabricator->create();

        $response = $this->delete("/api/v1/users/{$test->id}");

        $response->assertStatus(204);
        $this->dontSeeInDatabase('users',[
            'name' => $test->name,
            'email' => $test->email,
            'deleted_at' => null
        ]);
    }

    public function test_user_error()
    {
        $response = $this->delete("/api/v1/users/0");
        $responseJsonDecode = json_decode($response->getJSON());
        $response->assertStatus(409);
        $this->assertEquals('El campo identificador del usuario debe ser válido.', $responseJsonDecode->message->id);
    }
}