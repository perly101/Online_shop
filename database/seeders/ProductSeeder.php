<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jsonPath = public_path('data/products.json');
        $products = json_decode(file_get_contents($jsonPath), true);

        foreach ($products as $productData) {
            $product = \App\Models\Product::create([
                'name' => $productData['name'],
                'price' => $productData['price'],
                'image' => $productData['image'],
                'description' => $productData['description'],
                'stock' => 100, // Default stock
            ]);

            // Create variants if they exist
            if (isset($productData['variants'])) {
                foreach ($productData['variants'] as $variant) {
                    \App\Models\ProductVariant::create([
                        'product_id' => $product->id,
                        'flavor' => $variant['flavor'],
                        'price' => $variant['price'] ?? $productData['price'],
                        'stock' => $variant['stock'] ?? 100,
                        'image' => $variant['image'],
                    ]);
                }
            }
        }
    }
}
