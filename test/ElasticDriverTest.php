<?php

namespace Denismitr\ElsticEngine\Test;

use Denismitr\ElasticEngine\ElasticEngine;
use Illuminate\Support\Collection;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ElasticEngineTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_adds_objects_to_index_on_update()
    {
        $client = m::mock('Elasticsearch\Client');
        $client->shouldReceive('bulk')->with([
            'body' => [
                [
                    'update' => [
                        '_id' => 1,
                        '_index' => 'scout',
                        '_type' => 'table',
                    ]
                ],
                [
                    'doc' => ['id' => 1 ],
                    'doc_as_upsert' => true
                ]
            ]
        ])->once();

        $driver = new ElasticEngine($client, 'scout');
        $driver->update(Collection::make([new TestModel]));
    }
}
