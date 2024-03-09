<?php

namespace Tests\Feature\Categories;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\CIUnitTestCase;

use CodeIgniter\Test\Fabricator;
use Tests\Support\Models\CategoryFabricator;

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
        $fabricator = new Fabricator(CategoryFabricator::class);
        $fabricator->create(10);

        $response = $this->get('/api/v1/categories');
        $responseJsonDecode = json_decode($response->getJSON());
        $this->assertEquals(10, count($responseJsonDecode));
    }
}