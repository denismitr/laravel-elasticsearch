<?php

namespace Denismitr\ElasticEngine\Test;

use Illuminate\Database\Eloquent\Model;

class TestModelA extends Model
{
    public function getIdAttribute()
    {
        return 1;
    }

    public function searchableAs()
    {
        return 'test_table_a';
    }

    public function getKey()
    {
        return '1';
    }

    public function toSearchableArray()
    {
        return ['id' => 1];
    }
}
