<?php

namespace Tests\Feature\Categories;

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
        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->post('/api/v1/categories', [
            'name' => 'Rosa Carrillo',
            'description' => 'rosa@gmail.com'
        ]);

        $response->assertStatus(201);
        $this->seeInDatabase('categories',[
            'name' => 'Rosa Carrillo',
            'description' => 'rosa@gmail.com'
        ]);
    }

    public function test_error_name_text()
    {
        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->post('/api/v1/categories', [
            'name' => 'Rosa Carrillo12345',
            'description' => 'rosa@gmail.com'
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El nombre debe ser texto.', $responseJsonDecode->message->name);
    }

    public function test_error_name_empty()
    {
        $fabricator = new Fabricator(CategoryFabricator::class);
        $test = $fabricator->create();
        
        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->post('/api/v1/categories', [
            'description' => $test->description
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El nombre es obligatorio.', $responseJsonDecode->message->name);
    }
}