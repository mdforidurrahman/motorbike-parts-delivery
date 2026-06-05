<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Outlet;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{

    public function run(): void
    {
        // প্রথম আউটলেট পণ্য যোগ করুন
        $outlet = Outlet::where('type', 'contracted')->first();
        $engineCategory = Category::where('name', 'Engine Parts')->first();
        $brakeCategory = Category::where('name', 'Brake System')->first();
        
        if ($outlet && $engineCategory) {
            $products = [
                [
                    'name' => 'Honda CBR 150 Radiator',
                    'sku' => 'HON-CBR150-RAD-001',
                    'description' => 'Original radiator for Honda CBR 150R, perfect cooling efficiency',
                    'price' => 3500,
                    'stock_quantity' => 10,
                    'category_id' => $engineCategory->id,
                    'outlet_id' => $outlet->id,
                    'image' => 'products/radiator.jpg',
                    'is_available' => true,
                ],
                [
                    'name' => 'Yamaha FZ Brake Pads',
                    'sku' => 'YAM-FZ-BRK-002',
                    'description' => 'High-quality brake pads for Yamaha FZ series',
                    'price' => 450,
                    'stock_quantity' => 25,
                    'category_id' => $brakeCategory->id,
                    'outlet_id' => $outlet->id,
                    'image' => 'products/brake-pads.jpg',
                    'is_available' => true,
                ],
                [
                    'name' => 'Suzuki Gixxer Chain Sprocket Kit',
                    'sku' => 'SUZ-GIX-CHN-003',
                    'description' => 'Original chain sprocket kit for Suzuki Gixxer',
                    'price' => 1200,
                    'stock_quantity' => 8,
                    'category_id' => $engineCategory->id,
                    'outlet_id' => $outlet->id,
                    'image' => 'products/sprocket.jpg',
                    'is_available' => true,
                ],
                [
                    'name' => 'Bajaj Pulsar Headlight Assembly',
                    'sku' => 'BAJ-PUL-HDL-004',
                    'description' => 'Complete headlight assembly for Bajaj Pulsar 150/180',
                    'price' => 1800,
                    'stock_quantity' => 5,
                    'category_id' => Category::where('name', 'Electrical Parts')->first()->id,
                    'outlet_id' => $outlet->id,
                    'image' => 'products/headlight.jpg',
                    'is_available' => true,
                ],
                [
                    'name' => 'Hero Honda Engine Oil (1L)',
                    'sku' => 'HER-HON-OIL-005',
                    'description' => '20W40 engine oil for Hero Honda bikes',
                    'price' => 350,
                    'stock_quantity' => 50,
                    'category_id' => $engineCategory->id,
                    'outlet_id' => $outlet->id,
                    'image' => 'products/engine-oil.jpg',
                    'is_available' => true,
                ],
                [
                    'name' => 'TVS Apache Rear Shock Absorber',
                    'sku' => 'TVS-APO-SHK-006',
                    'description' => 'Original rear shock absorber for TVS Apache RTR',
                    'price' => 2200,
                    'stock_quantity' => 3,
                    'category_id' => Category::where('name', 'Suspension')->first()->id,
                    'outlet_id' => $outlet->id,
                    'image' => 'products/shock.jpg',
                    'is_available' => true,
                ],
                [
                    'name' => 'KTM Duke 200 Air Filter',
                    'sku' => 'KTM-DUK-AIR-007',
                    'description' => 'Performance air filter for KTM Duke 200',
                    'price' => 650,
                    'stock_quantity' => 12,
                    'category_id' => $engineCategory->id,
                    'outlet_id' => $outlet->id,
                    'image' => 'products/air-filter.jpg',
                    'is_available' => true,
                ],
                [
                    'name' => 'Royal Enfield Bullet Silencer',
                    'sku' => 'RE-BUL-SIL-008',
                    'description' => 'Classic silencer for Royal Enfield Bullet 350',
                    'price' => 4500,
                    'stock_quantity' => 4,
                    'category_id' => Category::where('name', 'Exhaust System')->first()->id,
                    'outlet_id' => $outlet->id,
                    'image' => 'products/silencer.jpg',
                    'is_available' => true,
                ],
            ];
            
            foreach ($products as $product) {
                Product::updateOrCreate(
                    ['sku' => $product['sku']],
                    $product
                );
            }
        }
    }
}