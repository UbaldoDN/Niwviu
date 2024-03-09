<?php

namespace Tests\Feature\Users;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\CIUnitTestCase;

use CodeIgniter\Test\Fabricator;
use Tests\Support\Models\UserFabricator;

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
        $fabricator = new Fabricator(UserFabricator::class);
        $test = $fabricator->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/users/{$test->id}", [
            'name' => 'Rosa Carrillo',
            'email' => 'rosa@gmail.com'
        ]);

        $response->assertStatus(204);
        $this->seeInDatabase('users',[
            'name' => 'Rosa Carrillo',
            'email' => 'rosa@gmail.com'
        ]);
    }

    public function test_error_name_text()
    {
        $fabricator = new Fabricator(UserFabricator::class);
        $test = $fabricator->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/users/{$test->id}", [
            'name' => 'Rosa Carrillo12345',
            'email' => 'rosa@gmail.com'
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El nombre debe ser texto.', $responseJsonDecode->message->name);
    }

    public function test_error_email_no_valid()
    {
        $fabricator = new Fabricator(UserFabricator::class);
        $test = $fabricator->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/users/{$test->id}", [
            'name' => 'Rosa Carrillo',
            'email' => 'rosa'
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El correo electrónico debe ser válido.', $responseJsonDecode->message->email);
    }

    public function test_error_email_unique()
    {
        $fabricator = new Fabricator(UserFabricator::class);
        $test = $fabricator->create();
        $test1 = $fabricator->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/users/{$test->id}", [
            'name' => 'Rosa Carrillo',
            'email' => $test1->email
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El correo electrónico ya existe.', $responseJsonDecode->message->email);
    }

    public function test_error_name_empty()
    {
        $fabricator = new Fabricator(UserFabricator::class);
        $test = $fabricator->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/users/{$test->id}", [
            'email' => $test->email
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El nombre es obligatorio.', $responseJsonDecode->message->name);
    }

    public function test_error_email_empty()
    {
        $fabricator = new Fabricator(UserFabricator::class);
        $test = $fabricator->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/users/{$test->id}", [
            'name' => $test->name
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El correo electrónico es obligatorio.', $responseJsonDecode->message->email);
    }
}