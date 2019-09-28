<?php

namespace Fishinglog\GraphQL\Types;

use Fishinglog\Record;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RecordType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Record',
        'description' => 'Record details',
        'model' => Record::class, 
    ];
    
    // define field of type
    public function fields(): array
    {
        return [
            'anglers_id' => [
                'type' => Type::int(),
                'description' => 'Angler who caught the fish.',
            ],
            'lakes_id' => [
                'type' => Type::int(),
                'description' => 'Lake of the catch.',
            ],
            'fish_breeds_id' => [
                'type' => Type::int(),
                'description' => 'Defines the breed of the fish.',
            ],
            'lures_id' => [
                'type' => Type::int(),
                'description' => 'Lure used.',
            ],
            'weight' => [
                'type' => Type::float(),
                'description' => 'Weight of the catch',
            ],
            'length' => [
                'type' => Type::float(),
                'description' => 'Length of the catch.',
            ],
            #'temperature' => 'numeric',
            'released' => [
                'type' => Type::boolean(),
                'description' => 'Was the fish released',
            ],
            'caught' => [
                'type' => Type::string(),
                'description' => 'Date of the catch.',
            ]
        ];
    }
}