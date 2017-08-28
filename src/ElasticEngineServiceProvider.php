<?php

namespace Denismitr\ElasticEngine;

use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;
use Elasticsearch\ClientBuilder as Builder;

class ElasticEngineServiceProvider extends ServiceProvider
{
    public function boot()
    {
        app()->make(EngineManager::class)->extend('elasticsearch', function($app) {
            $builder = Builder::create()
                ->setHosts(config('scout.elasticsearch.hosts'))
                ->build();

            return new ElasticEngine($builder, config('scout.elasticsearch.index'));
        });
    }
}
