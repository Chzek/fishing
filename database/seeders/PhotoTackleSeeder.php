<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Fishinglog\Models\Lure;

class PhotoTackleSeeder extends Seeder
{
    /**
     * Seed photo-identified tackle inventory into the lures database.
     */
    public function run(): void
    {
        $lures = [
            // TRAY 1: Crankbaits, Jerkbaits & Swimbaits
            ['brand' => 'Yo-Zuri', 'name' => 'Crystal Minnow', 'category' => 'Jerkbait', 'color' => 'Translucent Emerald Perch', 'size' => '3.5"', 'depth_range' => '4-6 ft.'],
            ['brand' => 'Strike King', 'name' => 'Series 5 Deep Diver', 'category' => 'Crankbait', 'color' => 'Blue Lime Chartreuse', 'size' => '3"', 'depth_range' => '10-14 ft.'],
            ['brand' => 'Cotton Cordell', 'name' => 'Redfin Minnow', 'category' => 'Jerkbait', 'color' => 'Spotted Firetiger', 'size' => '3.5"', 'depth_range' => '3-5 ft.'],
            ['brand' => 'LiveTarget', 'name' => 'Jointed Perch', 'category' => 'Swimbait', 'color' => 'Yellow Perch', 'size' => '3"', 'depth_range' => '3-6 ft.'],
            ['brand' => 'Bandit', 'name' => 'Bandit 200 Deep Diver', 'category' => 'Crankbait', 'color' => 'Rainbow Trout Purple Back', 'size' => '3.5"', 'depth_range' => '8-12 ft.'],
            ['brand' => 'Savage Gear', 'name' => 'Jointed Sunfish', 'category' => 'Swimbait', 'color' => 'Green Sunfish', 'size' => '3.5"', 'depth_range' => 'Sinking'],
            ['brand' => 'Berkley', 'name' => 'Dredger Deep Diver', 'category' => 'Crankbait', 'color' => 'Chartreuse Red Head', 'size' => '3.5"', 'depth_range' => '12-16 ft.'],

            // TRAY 2: Classic Spoons
            ['brand' => 'Eppinger', 'name' => 'Daredevle Spoon', 'category' => 'Spoon', 'color' => 'Classic Red & White Stripe', 'size' => '3.5"', 'weight' => '1/2 oz.'],
            ['brand' => 'Eppinger', 'name' => 'Five-Diamond Spoon', 'category' => 'Spoon', 'color' => 'Cream w/ Green Diamonds', 'size' => '3.5"', 'weight' => '1/2 oz.'],

            // TRAY 3: Inline Spinners & Spoons
            ['brand' => 'Mepps', 'name' => 'Aglia #3', 'category' => 'Inline Spinner', 'color' => 'Chartreuse / Yellow Blade', 'size' => '#3'],
            ['brand' => 'Mepps', 'name' => 'Black Fury', 'category' => 'Inline Spinner', 'color' => 'Black w/ Orange Dots', 'size' => '#2'],
            ['brand' => 'Mepps', 'name' => 'Comet Scale', 'category' => 'Inline Spinner', 'color' => 'Gold Scale Blade', 'size' => '#2'],
            ['brand' => 'Acme', 'name' => 'Little Cleo Spoon', 'category' => 'Spoon', 'color' => 'Silver & Neon Orange', 'size' => '2.5"', 'weight' => '1/4 oz.'],
            ['brand' => 'Tandem', 'name' => 'Silver French Blade Spinner', 'category' => 'Inline Spinner', 'color' => 'Double Chrome Silver', 'size' => '#3'],
            ['brand' => 'Mepps', 'name' => 'Black Fury #5 Dressed', 'category' => 'Inline Spinner', 'color' => 'Black w/ Chartreuse Dots & Green Bucktail', 'size' => '#5'],

            // TRAY 4: Jerkbaits, Minnows & Topwater
            ['brand' => 'Panfish', 'name' => 'Perch Inline Spinner', 'category' => 'Inline Spinner', 'color' => 'Yellow Perch', 'size' => '#2'],
            ['brand' => 'Luhr-Jensen', 'name' => 'Swedish Pimple Ice Spoon', 'category' => 'Spoon', 'color' => 'Chrome Blue & Silver', 'size' => '2"', 'weight' => '1/4 oz.'],
            ['brand' => 'Rapala', 'name' => 'Rattlin Rap', 'category' => 'Crankbait', 'color' => 'Silver w/ Neon Orange Belly', 'size' => '2.5"', 'depth_range' => 'Sinking'],
            ['brand' => 'Rapala', 'name' => 'Husky Jerk HJ14', 'category' => 'Jerkbait', 'color' => 'Chartreuse Firetiger', 'size' => '5.5"', 'depth_range' => '4-8 ft.'],
            ['brand' => 'Rebel', 'name' => 'Jointed Perch Minnow', 'category' => 'Crankbait', 'color' => 'Gold Perch', 'size' => '2.5"', 'depth_range' => '4-7 ft.'],
            ['brand' => 'Heddon', 'name' => 'Pop-R Topwater Chugger', 'category' => 'Topwater', 'color' => 'Spotted Frog w/ Feathered Hook', 'size' => '2.5"', 'depth_range' => 'Surface'],
            ['brand' => 'Rapala', 'name' => 'Original Floating Minnow', 'category' => 'Jerkbait', 'color' => 'Gold / Chartreuse Perch', 'size' => '3"', 'depth_range' => '2-4 ft.'],
            ['brand' => 'Rapala', 'name' => 'Jointed Shad Rap', 'category' => 'Crankbait', 'color' => 'Blue & Pearl White', 'size' => '2.5"', 'depth_range' => '5-8 ft.'],
            ['brand' => 'Rapala', 'name' => 'Original Floating Minnow', 'category' => 'Jerkbait', 'color' => 'Rainbow Trout', 'size' => '4"', 'depth_range' => '3-5 ft.'],
            ['brand' => 'Rapala', 'name' => 'Husky Jerk Gold', 'category' => 'Jerkbait', 'color' => 'Gold Firetiger w/ Orange Feather', 'size' => '4.5"', 'depth_range' => '4-6 ft.'],
            ['brand' => 'Rapala', 'name' => 'Husky Jerk Purple Pearl', 'category' => 'Jerkbait', 'color' => 'Purple Back & Pearl White', 'size' => '4.5"', 'depth_range' => '4-6 ft.'],

            // TRAY 5: Spoons & Casting Tackle
            ['brand' => 'Wonderbread', 'name' => 'Spotted Casting Spoon', 'category' => 'Spoon', 'color' => 'Blue/Pink Dots & Silver', 'size' => '2"', 'weight' => '1/4 oz.'],
            ['brand' => 'Eppinger', 'name' => 'Weedless Silver Minnow', 'category' => 'Spoon', 'color' => 'Red & White Weathered', 'size' => '3"', 'weight' => '1/2 oz.'],
            ['brand' => 'Phoebe', 'name' => 'Iridescent Trout Spoon', 'category' => 'Spoon', 'color' => 'Iridescent Green & Gold', 'size' => '2"', 'weight' => '1/6 oz.'],
            ['brand' => 'Acme', 'name' => 'Kastmaster Firetiger', 'category' => 'Spoon', 'color' => 'Firetiger Stripes', 'size' => '2"', 'weight' => '1/4 oz.'],
            ['brand' => 'Luhr-Jensen', 'name' => 'Oval Silver Casting Spoon', 'category' => 'Spoon', 'color' => 'Chrome Silver', 'size' => '2.5"', 'weight' => '1/4 oz.'],
            ['brand' => 'Diamond', 'name' => 'Scale Casting Spoon', 'category' => 'Spoon', 'color' => 'Green Scale & Diamond Pattern', 'size' => '4"', 'weight' => '3/4 oz.'],
            ['brand' => 'Eppinger', 'name' => 'Daredevle Tiger Stripe', 'category' => 'Spoon', 'color' => 'Firetiger Tiger Stripe', 'size' => '3.5"', 'weight' => '1/2 oz.'],

            // TRAY 6: Small Spoons & Marabou Jigs
            ['brand' => 'Panfish', 'name' => 'Marabou Feather Jig', 'category' => 'Jig', 'color' => 'Hot Pink & Magenta', 'size' => '1/16 oz.'],
            ['brand' => 'Crappie', 'name' => 'Feather Jig', 'category' => 'Jig', 'color' => 'Pearl White', 'size' => '1/16 oz.'],
            ['brand' => 'Acme', 'name' => 'Scale Casting Spoon', 'category' => 'Spoon', 'color' => 'Silver & Neon Orange Scale', 'size' => '2"', 'weight' => '1/4 oz.'],
            ['brand' => 'Thomas', 'name' => 'Buoyant Curved Spoon', 'category' => 'Spoon', 'color' => 'Chrome Silver', 'size' => '2"', 'weight' => '1/6 oz.'],
            ['brand' => 'Acme', 'name' => 'Kastmaster Silver', 'category' => 'Spoon', 'color' => 'Chrome Silver', 'size' => '2"', 'weight' => '1/4 oz.'],
            ['brand' => 'Panfish', 'name' => 'Hair Streamer Jig', 'category' => 'Jig', 'color' => 'White Hair & Yellow Head', 'size' => '1/8 oz.'],
            ['brand' => 'Acme', 'name' => 'Little Cleo Hammered', 'category' => 'Spoon', 'color' => 'Metallic Blue & Chartreuse', 'size' => '2.5"', 'weight' => '1/4 oz.'],

            // TRAY 7: Inline Spinners & Z-Ray
            ['brand' => 'Micro', 'name' => 'Gold Casting Spoon', 'category' => 'Spoon', 'color' => 'Gold Chrome', 'size' => '1.5"', 'weight' => '1/8 oz.'],
            ['brand' => 'Mepps', 'name' => 'Aglia Purple Dot', 'category' => 'Inline Spinner', 'color' => 'Chartreuse / Purple Dots', 'size' => '#2'],
            ['brand' => 'Z-Ray', 'name' => 'Copper Spoon', 'category' => 'Spoon', 'color' => 'Stamped Copper w/ Red Dots', 'size' => '2.5"', 'weight' => '1/4 oz.'],
            ['brand' => 'Mepps', 'name' => 'Aglia Dressed', 'category' => 'Inline Spinner', 'color' => 'Gold Scale w/ Black Bucktail', 'size' => '#3'],
            ['brand' => 'Yakima Bait', 'name' => 'Rooster Tail White', 'category' => 'Inline Spinner', 'color' => 'White Blade & Feather', 'size' => '#2'],
            ['brand' => 'Mepps', 'name' => 'Aglia Gold', 'category' => 'Inline Spinner', 'color' => 'Gold Scale Blade', 'size' => '#2'],
            ['brand' => 'Mepps', 'name' => 'Comet Rainbow Scale', 'category' => 'Inline Spinner', 'color' => 'Rainbow Scale Blade', 'size' => '#2'],
            ['brand' => 'Panfish', 'name' => 'Silver Trout Spinner', 'category' => 'Inline Spinner', 'color' => 'Silver Blade', 'size' => '#1'],
            ['brand' => 'Yakima Bait', 'name' => 'Rooster Tail', 'category' => 'Inline Spinner', 'color' => 'White w/ Black Dots', 'size' => '1/6 oz.'],

            // TRAY 8: Z-Man ElaZtech Finesse TRD Soft Plastics
            ['brand' => 'Z-Man', 'name' => 'Finesse TRD 2.75"', 'category' => 'Soft Plastic', 'color' => 'Twilight (Purple/Pink)', 'size' => '2.75"'],
            ['brand' => 'Z-Man', 'name' => 'Finesse TRD 2.75"', 'category' => 'Soft Plastic', 'color' => 'Pro Yellow Perch', 'size' => '2.75"'],
            ['brand' => 'Z-Man', 'name' => 'Finesse TRD 2.75"', 'category' => 'Soft Plastic', 'color' => 'California Craw', 'size' => '2.75"'],
            ['brand' => 'Z-Man', 'name' => 'Finesse TRD 2.75"', 'category' => 'Soft Plastic', 'color' => 'Deal / Mud Minnow', 'size' => '2.75"'],
            ['brand' => 'Z-Man', 'name' => 'Finesse TRD 2.75"', 'category' => 'Soft Plastic', 'color' => 'Coppertreuse', 'size' => '2.75"'],
        ];

        foreach ($lures as $lureData) {
            Lure::firstOrCreate(
                [
                    'brand' => $lureData['brand'],
                    'name' => $lureData['name'],
                    'color' => $lureData['color'],
                ],
                [
                    'category' => $lureData['category'],
                    'size' => $lureData['size'] ?? null,
                    'weight' => $lureData['weight'] ?? null,
                    'depth_range' => $lureData['depth_range'] ?? null,
                ]
            );
        }
    }
}
