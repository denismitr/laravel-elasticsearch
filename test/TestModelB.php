<?php

namespace Denismitr\ElasticEngine\Test;

use Illuminate\Database\Eloquent\Model;

class TestModelB extends Model
{
    public function getIdAttribute()
    {
        return 2;
    }

    public function searchableAs()
    {
        return 'test_table_a';
    }

    public function getKey()
    {
        return '2';
    }

    public function toSearchableArray()
    {
        return ['id' => 2];
    }
}
