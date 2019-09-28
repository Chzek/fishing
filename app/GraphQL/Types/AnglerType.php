<?php

namespace Fishinglog\GraphQL\Types;

use Fishinglog\Angler;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AnglerType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Angler',
        'description' => 'Angler details',
        'model' => Angler::class, 
    ];
    
    // define field of type
    public function fields(): array
    {
        return [
            'firstName' => [
                'type' => Type::string(),
                'description' => 'The first name of user'
            ],
            'middleName' => [
                'type' => Type::string(),
                'description' => 'The middle name of the user'
            ],
            'lastName' => [
                'type' => Type::string(),
                'description' => 'The last name of the user'
            ],
            'id' => [
                'type' => Type::int(),
                'description' => 'Users identification.'
            ]
        ];
    }
}