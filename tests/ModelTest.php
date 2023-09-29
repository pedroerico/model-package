<?php

use Model\Model;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Model::setPath(__DIR__ . '/data/');
        Model::setTable('model_mock');
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
    public function testAllMethodReturnsArrayOfModels()
    {
        $models = Model::all();
        $this->assertIsArray($models);
        $this->assertNotEmpty($models);

        foreach ($models as $model) {
            $this->assertInstanceOf(Model::class, $model);
        }
    }

    public function testFindMethodReturnsModelById()
    {
        $model = Model::find(1);

        $this->assertInstanceOf(Model::class, $model);
        $this->assertEquals(1, $model->id);
    }

    public function testFindMethodReturnsNullForNonexistentModel()
    {
        $model = Model::find(999);

        $this->assertNull($model);
    }
}
