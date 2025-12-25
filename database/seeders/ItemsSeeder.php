<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'slug' => 'laptop-pro-15',
                'image_url' => 'https://picsum.photos/800/600?random=1',
                'image_preview_url' => 'https://picsum.photos/200/150?random=1',
                'name' => 'Laptop Pro 15"',
                'description' => 'High-performance laptop with 15-inch display, perfect for professionals and creators.',
                'price' => 1299.99,
                'sale_price' => 1099.99,
                'currency' => 'USD',
                'stock' => 25,
                'tags' => ['electronics', 'computers', 'featured'],
                'sales_count' => 45,
            ],
            [
                'slug' => 'wireless-mouse-ergonomic',
                'image_url' => 'https://picsum.photos/800/600?random=2',
                'image_preview_url' => 'https://picsum.photos/200/150?random=2',
                'name' => 'Wireless Ergonomic Mouse',
                'description' => 'Comfortable wireless mouse designed for long hours of use.',
                'price' => 49.99,
                'sale_price' => null,
                'currency' => 'USD',
                'stock' => 150,
                'tags' => ['electronics', 'accessories'],
                'sales_count' => 230,
            ],
            [
                'slug' => 'mechanical-keyboard-rgb',
                'image_url' => 'https://picsum.photos/800/600?random=3',
                'image_preview_url' => 'https://picsum.photos/200/150?random=3',
                'name' => 'Mechanical RGB Keyboard',
                'description' => 'Gaming keyboard with RGB backlighting and mechanical switches.',
                'price' => 129.99,
                'sale_price' => 99.99,
                'currency' => 'USD',
                'stock' => 80,
                'tags' => ['electronics', 'gaming', 'featured'],
                'sales_count' => 156,
            ],
            [
                'slug' => 'usb-c-hub-adapter',
                'image_url' => 'https://picsum.photos/800/600?random=4',
                'image_preview_url' => 'https://picsum.photos/200/150?random=4',
                'name' => 'USB-C Hub 7-in-1 Adapter',
                'description' => 'Versatile USB-C hub with multiple ports for all your connectivity needs.',
                'price' => 39.99,
                'sale_price' => null,
                'currency' => 'USD',
                'stock' => 200,
                'tags' => ['electronics', 'accessories'],
                'sales_count' => 89,
            ],
            [
                'slug' => 'wireless-headphones-noise-cancelling',
                'image_url' => 'https://picsum.photos/800/600?random=5',
                'image_preview_url' => 'https://picsum.photos/200/150?random=5',
                'name' => 'Wireless Noise-Cancelling Headphones',
                'description' => 'Premium headphones with active noise cancellation and superior sound quality.',
                'price' => 299.99,
                'sale_price' => 249.99,
                'currency' => 'USD',
                'stock' => 50,
                'tags' => ['electronics', 'audio', 'featured'],
                'sales_count' => 112,
            ],
            [
                'slug' => 'portable-ssd-1tb',
                'image_url' => 'https://picsum.photos/800/600?random=6',
                'image_preview_url' => 'https://picsum.photos/200/150?random=6',
                'name' => 'Portable SSD 1TB',
                'description' => 'Ultra-fast portable storage with 1TB capacity and USB 3.2 Gen 2 interface.',
                'price' => 149.99,
                'sale_price' => null,
                'currency' => 'USD',
                'stock' => 120,
                'tags' => ['electronics', 'storage'],
                'sales_count' => 78,
            ],
            [
                'slug' => 'webcam-4k-streaming',
                'image_url' => 'https://picsum.photos/800/600?random=7',
                'image_preview_url' => 'https://picsum.photos/200/150?random=7',
                'name' => '4K Streaming Webcam',
                'description' => 'Professional-grade 4K webcam perfect for streaming and video conferencing.',
                'price' => 179.99,
                'sale_price' => 149.99,
                'currency' => 'USD',
                'stock' => 65,
                'tags' => ['electronics', 'video', 'streaming'],
                'sales_count' => 93,
            ],
            [
                'slug' => 'gaming-monitor-27-144hz',
                'image_url' => 'https://picsum.photos/800/600?random=8',
                'image_preview_url' => 'https://picsum.photos/200/150?random=8',
                'name' => '27" Gaming Monitor 144Hz',
                'description' => 'High refresh rate gaming monitor with stunning colors and fast response time.',
                'price' => 399.99,
                'sale_price' => 349.99,
                'currency' => 'USD',
                'stock' => 35,
                'tags' => ['electronics', 'monitors', 'gaming', 'featured'],
                'sales_count' => 67,
            ],
            [
                'slug' => 'smartphone-stand-adjustable',
                'image_url' => 'https://picsum.photos/800/600?random=9',
                'image_preview_url' => 'https://picsum.photos/200/150?random=9',
                'name' => 'Adjustable Smartphone Stand',
                'description' => 'Sturdy and adjustable phone stand compatible with all smartphone sizes.',
                'price' => 19.99,
                'sale_price' => null,
                'currency' => 'USD',
                'stock' => 300,
                'tags' => ['accessories', 'mobile'],
                'sales_count' => 445,
            ],
            [
                'slug' => 'laptop-backpack-waterproof',
                'image_url' => 'https://picsum.photos/800/600?random=10',
                'image_preview_url' => 'https://picsum.photos/200/150?random=10',
                'name' => 'Waterproof Laptop Backpack',
                'description' => 'Durable waterproof backpack with dedicated laptop compartment and multiple pockets.',
                'price' => 79.99,
                'sale_price' => 59.99,
                'currency' => 'USD',
                'stock' => 95,
                'tags' => ['accessories', 'bags'],
                'sales_count' => 187,
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
