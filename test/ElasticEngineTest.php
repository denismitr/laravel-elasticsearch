<?php

namespace Denismitr\ElasticEngine\Test;

use Denismitr\ElasticEngine\ElasticEngine;
use Illuminate\Support\Collection;
use Laravel\Scout\Builder;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ElasticEngineTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function setUp()
    {
        parent::setUp();

        $this->client = m::mock('Elasticsearch\Client');
    }

    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_adds_object_to_index_on_update()
    {
        $this->client->shouldReceive('bulk')->with([
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

        $engine = new ElasticEngine($this->client, 'scout');
        $engine->update(Collection::make([new TestModelA]));
    }

    /** @test */
    public function it_removes_object_from_index_on_delete()
    {
        $this->client->shouldReceive('bulk')->with([
            'body' => [
                [
                    'delete' => [
                        '_id' => 1,
                        '_index' => 'scout',
                        '_type' => 'table'
                    ]
                ]
            ]
        ])->once();

        $engine = new ElasticEngine($this->client, 'scout');
        $engine->delete(Collection::make([new TestModelA]));
    }

    /** @test */
    public function it_sends_correct_parameters_to_elasticsearch_with_search()
    {
        $this->client->shouldReceive('search')->with([
            'index' => 'scout',
            'type' => 'table',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['query_string' => ['query' => '*zonda*']],
                            ['match_phrase' => ['foo' => 1]],
                            ['terms' => ['bar' => [1, 3]]],
                        ]
                    ]
                ],
                'sort' => [
                    ['id' => 'desc']
                ]
            ]
        ])->once();

        $engine = new ElasticEngine($this->client, 'scout');

        $builder = new Builder(new TestModelA, 'zonda');

        $builder->where('foo', 1);
        $builder->where('bar', [1, 3]);
        $builder->orderBy('id', 'desc');
        $engine->search($builder);
    }

    /** @test */
    public function builder_can_manipulate_the_search_with_a_callback()
    {
        $this->client->shouldReceive('search')->with('modified_by_callback')->once();

        $engine = new ElasticEngine($this->client, 'scout');
        $builder = new Builder(
            new TestModelA,
            'test query',
            function(\Elasticsearch\Client $client, $query, $params) {
                $this->assertNotEmpty($params);
                $this->assertEquals('test query', $query);
                $params = 'modified_by_callback';

                return $client->search($params);
            }
        );

        $engine->search($builder);
    }
}
