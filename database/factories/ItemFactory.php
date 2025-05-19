<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    protected $products = [
        'Sunset Glow Lamp',
        'EcoFriendly Water Bottle',
        'PowerPlus Battery Pack',
        'Wilderness Hiking Boots',
        'Sleek Aluminum Laptop Stand',
        'Vitality Protein Powder',
        'CloudDream Memory Foam Pillow',
        'Precision Digital Kitchen Scale',
        'TechPro Wireless Earbuds',
        'Momentum Fitness Tracker',
        'Harmony Noise-Cancelling Headphones',
        'Evergreen Plant Food',
        'Sparkle Glass Cleaner',
        'RapidCharge Power Bank',
        'Serene Essential Oil Diffuser',
        'FlexiGrip Phone Mount',
        'OceanWave Shower Head',
        'Clarity Blue Light Glasses',
        'QuickDry Travel Towel',
        'PureStream Water Filter',
        'Vibrance Hair Conditioner',
        'SoftStep Yoga Mat',
        'DuraTech Phone Case',
        'MorningBliss Coffee Maker',
        'CozyTouch Electric Blanket',
        'FreshSlice Food Chopper',
        'EasyGlide Mouse Pad',
        'NatureSense Air Purifier',
        'FocusPro Study Lamp',
        'CleanSweep Robot Vacuum',
        'GlimmerShine Jewelry Cleaner',
        'SilkSoft Facial Tissues',
        'AlpineBreeze Car Freshener',
        'ProGrip Exercise Resistance Bands',
        'DazzleWhite Teeth Whitening Kit',
        'SteadyGrip Tripod',
        'TasteMaster Spice Set',
        'BrightBeam Flashlight',
        "SwiftSlice Chef's Knife",
        'GentleWash Laundry Detergent',
        'WarmHug Heated Vest',
        'StormShield Umbrella',
        'LuxuryTouch Bath Towels',
        'SoundScape Bluetooth Speaker',
        'FlavorFusion Tea Sampler',
        'PrecisionPoint Stylus Pen',
        'DeepRelief Massage Gun',
        'EcoShine Car Wax',
        'WinterComfort Heated Socks',
        'AquaFresh Skin Moisturizer',
        'BreezeFlow Portable Fan',
        'MindfulMoment Meditation Cushion',
        'UrbanChic Backpack',
        'GlowUp Selfie Ring Light',
        'PurrfectPlay Cat Toy Set',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement($this->products),
            'price' => random_int(1500, 30000),
        ];
    }
}
