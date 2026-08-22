<?php

namespace Database\Seeders;

use Fishinglog\Models\Lure;
use Illuminate\Database\Seeder;

class LureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $catalog = [
            // CRANKBAITS
            [
                'brand' => 'Rapala',
                'name' => 'Shad Rap',
                'category' => 'Crankbait',
                'color' => 'Firetiger',
                'size' => '2"',
                'weight' => '5/16 oz.',
                'depth_range' => '4-9 ft',
            ],
            [
                'brand' => 'Rapala',
                'name' => 'Shad Rap',
                'category' => 'Crankbait',
                'color' => 'Silver',
                'size' => '2"',
                'weight' => '5/16 oz.',
                'depth_range' => '4-9 ft',
            ],
            [
                'brand' => 'Rapala',
                'name' => 'Shad Rap',
                'category' => 'Crankbait',
                'color' => 'Perch',
                'size' => '2.75"',
                'weight' => '3/8 oz.',
                'depth_range' => '7-11 ft',
            ],
            [
                'brand' => 'Strike King',
                'name' => 'KVD 1.5 Squarebill',
                'category' => 'Crankbait',
                'color' => 'Sexy Shad',
                'size' => '2.25"',
                'weight' => '7/16 oz.',
                'depth_range' => '3-6 ft',
            ],
            [
                'brand' => 'Strike King',
                'name' => 'KVD 1.5 Squarebill',
                'category' => 'Crankbait',
                'color' => 'Chartreuse Sexy Shad',
                'size' => '2.25"',
                'weight' => '7/16 oz.',
                'depth_range' => '3-6 ft',
            ],
            [
                'brand' => 'Bandit',
                'name' => '200 Series Crankbait',
                'category' => 'Crankbait',
                'color' => 'Rootbeer',
                'size' => '2"',
                'weight' => '1/4 oz.',
                'depth_range' => '4-8 ft',
            ],

            // SOFT PLASTICS & SWIMBAITS
            [
                'brand' => 'Gary Yamamoto',
                'name' => '5" Senko Worm',
                'category' => 'Soft Plastic',
                'color' => 'Green Pumpkin',
                'size' => '5"',
                'weight' => '3/8 oz.',
                'depth_range' => 'Finesse / Bottom',
            ],
            [
                'brand' => 'Gary Yamamoto',
                'name' => '5" Senko Worm',
                'category' => 'Soft Plastic',
                'color' => 'Watermelon Red Flake',
                'size' => '5"',
                'weight' => '3/8 oz.',
                'depth_range' => 'Finesse / Bottom',
            ],
            [
                'brand' => 'Gary Yamamoto',
                'name' => '5" Senko Worm',
                'category' => 'Soft Plastic',
                'color' => 'Black w/ Blue Flake',
                'size' => '5"',
                'weight' => '3/8 oz.',
                'depth_range' => 'Finesse / Bottom',
            ],
            [
                'brand' => 'Keitech',
                'name' => 'Fat Swing Impact',
                'category' => 'Swimbait',
                'color' => 'Electric Shad',
                'size' => '3.8"',
                'weight' => '1/4 oz.',
                'depth_range' => 'All Depths',
            ],
            [
                'brand' => 'Keitech',
                'name' => 'Fat Swing Impact',
                'category' => 'Swimbait',
                'color' => 'Pro Blue / Red Pearl',
                'size' => '3.8"',
                'weight' => '1/4 oz.',
                'depth_range' => 'All Depths',
            ],
            [
                'brand' => 'Z-Man',
                'name' => 'MinnowZ Swimbait',
                'category' => 'Swimbait',
                'color' => 'Opening Night',
                'size' => '3"',
                'weight' => '1/4 oz.',
                'depth_range' => 'Midwater',
            ],
            [
                'brand' => 'Berkley',
                'name' => '7" Power Worm',
                'category' => 'Soft Plastic',
                'color' => 'Blue Fleck',
                'size' => '7"',
                'weight' => '3/8 oz.',
                'depth_range' => 'Bottom Cover',
            ],

            // INLINE SPINNERS & SPINNERBAITS
            [
                'brand' => 'Mepps',
                'name' => 'Aglia Inline Spinner',
                'category' => 'Inline Spinner',
                'color' => 'Gold',
                'size' => '#3',
                'weight' => '1/4 oz.',
                'depth_range' => 'All Depths',
            ],
            [
                'brand' => 'Mepps',
                'name' => 'Aglia Inline Spinner',
                'category' => 'Inline Spinner',
                'color' => 'Silver',
                'size' => '#3',
                'weight' => '1/4 oz.',
                'depth_range' => 'All Depths',
            ],
            [
                'brand' => 'Mepps',
                'name' => 'Black Fury Spinner',
                'category' => 'Inline Spinner',
                'color' => 'Black / Yellow Dot',
                'size' => '#3',
                'weight' => '1/4 oz.',
                'depth_range' => 'All Depths',
            ],
            [
                'brand' => 'Panther Martin',
                'name' => 'Regular Spinner',
                'category' => 'Inline Spinner',
                'color' => 'Gold Blade / Black Body',
                'size' => '1/4 oz.',
                'weight' => '1/4 oz.',
                'depth_range' => 'Midwater',
            ],
            [
                'brand' => 'Terminator',
                'name' => 'T1 Titanium Spinnerbait',
                'category' => 'Spinnerbait',
                'color' => 'White / Chartreuse',
                'size' => '1/2 oz.',
                'weight' => '1/2 oz.',
                'depth_range' => '2-8 ft',
            ],

            // JIGS & TERMINAL TACKLE
            [
                'brand' => 'Round Ball Jig',
                'name' => 'Finesse Jig Head',
                'category' => 'Jig',
                'color' => 'Chartreuse',
                'size' => '1/4 oz.',
                'weight' => '1/4 oz.',
                'depth_range' => 'Bottom',
            ],
            [
                'brand' => 'Dirty Jigs',
                'name' => "Pitchin' Jig",
                'category' => 'Jig',
                'color' => 'Black & Blue',
                'size' => '3/8 oz.',
                'weight' => '3/8 oz.',
                'depth_range' => 'Heavy Cover',
            ],

            // SPOONS & TOPWATER
            [
                'brand' => 'Eppinger',
                'name' => 'Dardevle Classic Spoon',
                'category' => 'Spoon',
                'color' => 'Red & White',
                'size' => '3/4 oz.',
                'weight' => '3/4 oz.',
                'depth_range' => 'Variable',
            ],
            [
                'brand' => 'Acme',
                'name' => 'Little Cleo',
                'category' => 'Spoon',
                'color' => 'Nickel / Blue',
                'size' => '1/3 oz.',
                'weight' => '1/3 oz.',
                'depth_range' => '3-10 ft',
            ],
            [
                'brand' => 'Heddon',
                'name' => 'Super Spook Jr.',
                'category' => 'Topwater',
                'color' => 'Bone',
                'size' => '3.5"',
                'weight' => '1/2 oz.',
                'depth_range' => 'Topwater',
            ],
        ];

        foreach ($catalog as $item) {
            Lure::firstOrCreate(
                [
                    'name' => $item['name'],
                    'color' => $item['color'],
                    'size' => $item['size'],
                ],
                [
                    'brand' => $item['brand'],
                    'category' => $item['category'],
                    'weight' => $item['weight'],
                    'depth_range' => $item['depth_range'],
                ]
            );
        }
    }
}
