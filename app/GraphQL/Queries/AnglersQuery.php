<?php

namespace Fishinglog\GraphQL\Queries;

use Closure;
use Fishinglog\Angler;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class AnglersQuery extends Query
{
    protected $attributes = [
        'name' => 'anglers',
        'description' => 'Details for all anglers.'
    ];

    public function type(): Type
    {
        // result of query with pagination laravel
        return Type::listOf(GraphQL::type('Angler'));
    }
    
    // arguments to filter query
    public function args(): array
    {
        return [
            'firstName' => [
                'type' => Type::string()
            ],
            'middleName' => [
                'type' => Type::string()
            ],
            'lastname' => [
                'type' => Type::string()
            ],
            'user_id' => [
                'alias' => 'id',
                'type' => Type::int()
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if (isset($args['firstName'])) {
            return Angler::where('firstName' , $args['firstName'])->get();
        }

        return Angler::all();
    }
}