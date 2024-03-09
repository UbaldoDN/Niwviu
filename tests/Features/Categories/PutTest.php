<?php

namespace Tests\Feature\Categories;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\CIUnitTestCase;

use CodeIgniter\Test\Fabricator;
use Tests\Support\Models\CategoryFabricator;

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
        $test = $fabricator->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/categories/{$test->id}", [
            'name' => 'Horror',
            'description' => 'Es un texto para las categorias'
        ]);

        $response->assertStatus(204);
        $this->seeInDatabase('categories',[
            'name' => 'Horror',
            'description' => 'Es un texto para las categorias'
        ]);
    }

    public function test_error_name_text()
    {
        $fabricator = new Fabricator(CategoryFabricator::class);
        $test = $fabricator->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/categories/{$test->id}", [
            'name' => 'Horror12345',
            'description' => $test->description
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El nombre debe ser texto.', $responseJsonDecode->message->name);
    }

    public function test_error_name_empty()
    {
        $fabricator = new Fabricator(CategoryFabricator::class);
        $test = $fabricator->create();

        $response = $this->withHeaders(['Content-Type' => 'application/json'])->withBodyFormat('json')->put("/api/v1/categories/{$test->id}", [
            'description' => $test->description
        ]);

        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals('El nombre es obligatorio.', $responseJsonDecode->message->name);
    }
}