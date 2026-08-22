<?php

namespace Database\Seeders;

use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lure;
use Illuminate\Database\Seeder;

class LureSeeder extends Seeder
{
    /**
     * Run the database seeds tailored to species existing in the database.
     *
     * @return void
     */
    public function run()
    {
        // Fetch existing species names in DB for intelligent targeted tackle seeding
        $existingSpecies = FishBreed::pluck('name')->map(fn ($n) => strtolower((string) $n))->toArray();

        $catalog = [
            // WALLEYE TARGETED TACKLE
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
                'brand' => 'Cotton Cordell',
                'name' => 'Wally Diver',
                'category' => 'Crankbait',
                'color' => 'Gold / Black Back',
                'size' => '2.5"',
                'weight' => '1/4 oz.',
                'depth_range' => '6-11 ft',
            ],
            [
                'brand' => 'Berkley',
                'name' => 'Flicker Shad',
                'category' => 'Crankbait',
                'color' => 'Slick Bluegill',
                'size' => '2.75"',
                'weight' => '5/16 oz.',
                'depth_range' => '9-13 ft',
            ],

            // SMALLMOUTH & LARGEMOUTH BASS TACKLE
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
                'name' => 'TRD Finesse Ned Rig',
                'category' => 'Soft Plastic',
                'color' => 'Green Pumpkin',
                'size' => '2.75"',
                'weight' => '1/10 oz.',
                'depth_range' => 'Bottom Finesse',
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
            [
                'brand' => 'Booyah',
                'name' => 'Covert Spinnerbait',
                'category' => 'Spinnerbait',
                'color' => 'White / Chartreuse',
                'size' => '3/8 oz.',
                'weight' => '3/8 oz.',
                'depth_range' => '2-6 ft',
            ],

            // TROUT & SALMON TACKLE (Lake Trout, Steelhead, Brown, Brook, Coho, Chinook)
            [
                'brand' => 'Acme',
                'name' => 'Little Cleo Spoon',
                'category' => 'Spoon',
                'color' => 'Nickel / Blue',
                'size' => '1/3 oz.',
                'weight' => '1/3 oz.',
                'depth_range' => '3-10 ft',
            ],
            [
                'brand' => 'Acme',
                'name' => 'Little Cleo Spoon',
                'category' => 'Spoon',
                'color' => 'Nickel / Red',
                'size' => '2/5 oz.',
                'weight' => '2/5 oz.',
                'depth_range' => '5-15 ft',
            ],
            [
                'brand' => 'Luhr Jensen',
                'name' => 'Krocodile Spoon',
                'category' => 'Spoon',
                'color' => 'Chrome / Blue Prism',
                'size' => '1/2 oz.',
                'weight' => '1/2 oz.',
                'depth_range' => 'Trolling / Deep',
            ],
            [
                'brand' => 'Blue Fox',
                'name' => 'Super Vibrax',
                'category' => 'Inline Spinner',
                'color' => 'Silver / Blue Bell',
                'size' => '#3',
                'weight' => '1/4 oz.',
                'depth_range' => 'Midwater',
            ],
            [
                'brand' => 'Panther Martin',
                'name' => 'Regular Spinner',
                'category' => 'Inline Spinner',
                'color' => 'Gold Blade / Black Body',
                'size' => '1/4 oz.',
                'weight' => '1/4 oz.',
                'depth_range' => 'Stream / River',
            ],
            [
                'brand' => 'Yo-Zuri',
                'name' => "Pin's Minnow",
                'category' => 'Jerkbait',
                'color' => 'Rainbow Trout',
                'size' => '2.75"',
                'weight' => '1/8 oz.',
                'depth_range' => '1-3 ft',
            ],

            // NORTHERN PIKE & MUSKY TACKLE
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
                'brand' => 'Mepps',
                'name' => 'Musky Killer',
                'category' => 'Inline Spinner',
                'color' => 'Black Bucktail / Gold Blade',
                'size' => '#5',
                'weight' => '3/4 oz.',
                'depth_range' => 'Shallow / Weed Edges',
            ],
            [
                'brand' => 'Rapala',
                'name' => 'Super Shad Rap',
                'category' => 'Crankbait',
                'color' => 'Perch',
                'size' => '5.5"',
                'weight' => '1.6 oz.',
                'depth_range' => '5-9 ft',
            ],

            // BLUEGILL & PANFISH / PERCH TACKLE
            [
                'brand' => 'Mepps',
                'name' => 'Aglia Ultra Lite',
                'category' => 'Inline Spinner',
                'color' => 'Gold',
                'size' => '#0',
                'weight' => '1/12 oz.',
                'depth_range' => 'Shallow',
            ],
            [
                'brand' => 'Round Ball Jig',
                'name' => 'Micro Finesse Jig Head',
                'category' => 'Jig',
                'color' => 'Chartreuse',
                'size' => '1/16 oz.',
                'weight' => '1/16 oz.',
                'depth_range' => 'Panfish Cover',
            ],
            [
                'brand' => 'Leland Lures',
                'name' => 'Trout Magnet',
                'category' => 'Soft Plastic',
                'color' => 'Bison / Black',
                'size' => '1/64 oz.',
                'weight' => '1/64 oz.',
                'depth_range' => 'Finesse Float',
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
