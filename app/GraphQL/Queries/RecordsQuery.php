<?php

namespace Fishinglog\GraphQL\Queries;

use Closure;
use Fishinglog\Record;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class RecordsQuery extends Query
{
    protected $attributes = [
        'name' => 'records',
        'description' => 'Details for all records.'
    ];

    public function type(): Type
    {
        // result of query with pagination laravel
        return Type::listOf(GraphQL::type('Record'));
    }
    
    // arguments to filter query
    public function args(): array
    {
        return [
            'anglers_id' => [
                'type' => Type::int(),
            ],
            'lakes_id' => [
                'type' => Type::int(),
            ],
            'fish_breeds_id' => [
                'type' => Type::int(),
            ],
            'lures_id' => [
                'type' => Type::int(),
            ],
            'weight' => [
                'type' => Type::float(),
            ],
            'length' => [
                'type' => Type::float(),
            ],
            'released' => [
                'type' => Type::boolean(),
            ],
            'caught' => [
                'type' => Type::string(),
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Record::all();
    }
}