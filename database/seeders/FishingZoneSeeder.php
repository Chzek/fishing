<?php

namespace Database\Seeders;

use Fishinglog\Models\FishingRule;
use Fishinglog\Models\FishingZone;
use Illuminate\Database\Seeder;

class FishingZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = [
            [
                'code' => 'FMZ 7',
                'name' => 'FMZ 7 - Wawa, Algoma & Highway 17 Corridor',
                'province_state' => 'Ontario',
                'country' => 'Canada',
                'description' => 'Fisheries Management Zone 7 covers the Algoma District, Wawa, Hawk Junction, Dubreuilville, White River, and Missinaibi provincial waters. Primary targets: Walleye, Northern Pike, Lake Trout, Brook Trout, Pacific Salmon, Smallmouth Bass.',
                'regulations_url' => 'https://www.ontario.ca/page/fisheries-management-zone-7-fmz-7',
                'bounds' => [
                    'min_lat' => 47.5,
                    'max_lat' => 49.0,
                    'min_lng' => -86.0,
                    'max_lng' => -83.5,
                ],
            ],
            [
                'code' => 'FMZ 9',
                'name' => 'FMZ 9 - Lake Superior North Shore',
                'province_state' => 'Ontario',
                'country' => 'Canada',
                'description' => 'Fisheries Management Zone 9 includes the open waters of Lake Superior and offshore islands north of Sault Ste. Marie up to Thunder Bay. Primary targets: Chinook Salmon, Coho Salmon, Pink Salmon, Lake Trout, Rainbow Trout (Steelhead).',
                'regulations_url' => 'https://www.ontario.ca/page/fisheries-management-zone-9-fmz-9',
                'bounds' => [
                    'min_lat' => 46.5,
                    'max_lat' => 48.8,
                    'min_lng' => -89.5,
                    'max_lng' => -84.3,
                ],
            ],
            [
                'code' => 'FMZ 5',
                'name' => 'FMZ 5 - Kenora, Rainy River & Sunset Country',
                'province_state' => 'Ontario',
                'country' => 'Canada',
                'description' => 'Fisheries Management Zone 5 covers Lake of the Woods, Rainy Lake, and Northwestern Ontario border waters.',
                'regulations_url' => 'https://www.ontario.ca/page/fisheries-management-zone-5-fmz-5',
            ],
            [
                'code' => 'FMZ 6',
                'name' => 'FMZ 6 - Thunder Bay & Nipigon Boundary Waters',
                'province_state' => 'Ontario',
                'country' => 'Canada',
                'description' => 'Fisheries Management Zone 6 includes Lake Nipigon and Inland Thunder Bay District waters.',
                'regulations_url' => 'https://www.ontario.ca/page/fisheries-management-zone-6-fmz-6',
            ],
            [
                'code' => 'FMZ 10',
                'name' => 'FMZ 10 - Sudbury, Sault Ste. Marie Inland & North Shore',
                'province_state' => 'Ontario',
                'country' => 'Canada',
                'description' => 'Fisheries Management Zone 10 covers Inland Sault Ste. Marie, Elliot Lake, and Sudbury inland waters.',
                'regulations_url' => 'https://www.ontario.ca/page/fisheries-management-zone-10-fmz-10',
            ],
        ];

        // Add standard FMZ 1..4, 8, 11..20
        for ($i = 1; $i <= 20; $i++) {
            $code = "FMZ {$i}";
            if (!collect($zones)->pluck('code')->contains($code)) {
                $zones[] = [
                    'code' => $code,
                    'name' => "FMZ {$i} - Ontario Fisheries Management Zone {$i}",
                    'province_state' => 'Ontario',
                    'country' => 'Canada',
                    'description' => "Ontario Fisheries Management Zone {$i} fishing regulations and limits.",
                    'regulations_url' => "https://www.ontario.ca/page/fisheries-management-zone-{$i}-fmz-{$i}",
                    'bounds' => null,
                ];
            }
        }

        foreach ($zones as $zData) {
            $zone = FishingZone::updateOrCreate(
                ['code' => $zData['code']],
                $zData
            );

            // Seed regulations for FMZ 7
            if ($zone->code === 'FMZ 7') {
                $rules = [
                    [
                        'species_name' => 'Trout & Salmon (Aggregate Limit)',
                        'is_aggregate' => true,
                        'aggregate_group' => 'Trout & Salmon',
                        'season' => 'Open all year (check individual species dates)',
                        'sport_limit' => 'S - 5 aggregate',
                        'conservation_limit' => 'C - 2 aggregate',
                        'size_restriction' => 'Combined total of all trout and salmon species cannot exceed 5 (S) or 2 (C)',
                        'notes' => 'Provincial Aggregate Rule: Total combined catch & possession for Lake Trout, Brook Trout, Rainbow Trout, Chinook, Coho, Pink Salmon, and Splake.',
                    ],
                    [
                        'species_name' => 'Pacific Salmon (Chinook, Coho, Pink)',
                        'is_aggregate' => false,
                        'aggregate_group' => 'Trout & Salmon',
                        'season' => 'Open all year',
                        'sport_limit' => 'S - 5',
                        'conservation_limit' => 'C - 2',
                        'size_restriction' => 'None; counts toward aggregate Trout & Salmon limit of 5 (S) / 2 (C)',
                        'notes' => 'Applies to Chinook Salmon, Coho Salmon, and Pink Salmon in inland rivers & tributary streams.',
                    ],
                    [
                        'species_name' => 'Walleye & Sauger (Aggregate Limit)',
                        'is_aggregate' => true,
                        'aggregate_group' => 'Walleye & Sauger',
                        'season' => '3rd Saturday in May to December 31',
                        'sport_limit' => 'S - 4 aggregate',
                        'conservation_limit' => 'C - 2 aggregate',
                        'size_restriction' => 'Must be under 46 cm (18.1 in); only 1 over 46 cm allowed',
                        'notes' => 'Combined limit for Walleye and Sauger. Check waterbody specific exceptions.',
                    ],
                    [
                        'species_name' => 'Northern Pike',
                        'is_aggregate' => false,
                        'aggregate_group' => null,
                        'season' => 'Open all year',
                        'sport_limit' => 'S - 4',
                        'conservation_limit' => 'C - 2',
                        'size_restriction' => 'None over 75 cm (29.5 in) allowed without special tag',
                        'notes' => 'General FMZ 7 regulations apply.',
                    ],
                    [
                        'species_name' => 'Lake Trout',
                        'is_aggregate' => false,
                        'aggregate_group' => 'Trout & Salmon',
                        'season' => 'January 1 to September 30',
                        'sport_limit' => 'S - 2',
                        'conservation_limit' => 'C - 1',
                        'size_restriction' => 'Not more than 1 greater than 56 cm (22 in) if taken from inland lakes',
                        'notes' => 'Counts toward 5 (S) / 2 (C) aggregate Trout & Salmon limit.',
                    ],
                    [
                        'species_name' => 'Smallmouth & Largemouth Bass (Aggregate Limit)',
                        'is_aggregate' => true,
                        'aggregate_group' => 'Bass',
                        'season' => 'Open all year',
                        'sport_limit' => 'S - 4 aggregate',
                        'conservation_limit' => 'C - 2 aggregate',
                        'size_restriction' => 'None',
                        'notes' => 'Combined total of Smallmouth and Largemouth Bass.',
                    ],
                    [
                        'species_name' => 'Brook Trout',
                        'is_aggregate' => false,
                        'aggregate_group' => 'Trout & Salmon',
                        'season' => '4th Saturday in April to Labour Day',
                        'sport_limit' => 'S - 5',
                        'conservation_limit' => 'C - 2',
                        'size_restriction' => 'Only 2 greater than 30 cm (11.8 in), 1 greater than 40 cm (15.7 in)',
                        'notes' => 'Includes rivers and inland streams in Algoma.',
                    ],
                ];

                foreach ($rules as $r) {
                    FishingRule::updateOrCreate(
                        ['fishing_zone_id' => $zone->id, 'species_name' => $r['species_name']],
                        $r
                    );
                }
            }

            // Seed regulations for FMZ 9 (Lake Superior)
            if ($zone->code === 'FMZ 9') {
                $rules9 = [
                    [
                        'species_name' => 'Pacific Salmon (Chinook, Coho, Pink)',
                        'is_aggregate' => false,
                        'aggregate_group' => 'Trout & Salmon',
                        'season' => 'Open all year',
                        'sport_limit' => 'S - 5',
                        'conservation_limit' => 'C - 2',
                        'size_restriction' => 'No minimum length limit on Lake Superior offshore waters',
                        'notes' => 'Combined total of Chinook, Coho, and Pink Salmon on Lake Superior. Counts toward aggregate Trout & Salmon limit.',
                    ],
                    [
                        'species_name' => 'Trout & Salmon (Lake Superior Aggregate Limit)',
                        'is_aggregate' => true,
                        'aggregate_group' => 'Trout & Salmon',
                        'season' => 'Open all year',
                        'sport_limit' => 'S - 5 aggregate',
                        'conservation_limit' => 'C - 2 aggregate',
                        'size_restriction' => 'Combined total of all salmon and trout species on Lake Superior cannot exceed 5 (S) or 2 (C)',
                        'notes' => 'Covers Chinook, Coho, Pink, Rainbow Trout (Steelhead), Brown Trout, and Lake Trout on Lake Superior.',
                    ],
                    [
                        'species_name' => 'Rainbow Trout (Steelhead)',
                        'is_aggregate' => false,
                        'aggregate_group' => 'Trout & Salmon',
                        'season' => 'Open all year',
                        'sport_limit' => 'S - 1',
                        'conservation_limit' => 'C - 0',
                        'size_restriction' => 'Must be greater than 50 cm (19.7 in)',
                        'notes' => 'Counts toward 5 (S) aggregate Trout & Salmon limit.',
                    ],
                    [
                        'species_name' => 'Lake Trout',
                        'is_aggregate' => false,
                        'aggregate_group' => 'Trout & Salmon',
                        'season' => 'January 1 to September 30',
                        'sport_limit' => 'S - 3',
                        'conservation_limit' => 'C - 1',
                        'size_restriction' => 'Only 1 greater than 56 cm (22 in)',
                        'notes' => 'Counts toward 5 (S) aggregate Trout & Salmon limit on Lake Superior.',
                    ],
                ];

                foreach ($rules9 as $r9) {
                    FishingRule::updateOrCreate(
                        ['fishing_zone_id' => $zone->id, 'species_name' => $r9['species_name']],
                        $r9
                    );
                }
            }
        }
    }
}
