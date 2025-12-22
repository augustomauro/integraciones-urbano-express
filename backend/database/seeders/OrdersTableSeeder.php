<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding orders table...');

        // Crear 5 órdenes de ejemplo específicas
        $sampleOrders = [
            [
                'order_id' => 'ORD-' . date('Y') . '-00001',
                'customer_name' => 'Martín López',
                'customer_email' => 'martin.lopez@example.com',
                'customer_phone' => '+5491134567890',
                'shipping_address' => 'Av. Corrientes 1234',
                'shipping_city' => 'Buenos Aires',
                'shipping_state' => 'CABA',
                'shipping_zip' => 'C1043',
                'shipping_country' => 'Argentina',
                'total_amount' => 299.97,
                'currency' => 'USD',
                'status' => 'pending',
                'items' => [
                    [
                        'product_id' => 'PROD-001',
                        'name' => 'Zapatillas Deportivas Nike Air Max',
                        'quantity' => 1,
                        'price' => 129.99
                    ],
                    [
                        'product_id' => 'PROD-002',
                        'name' => 'Medias Técnicas Running',
                        'quantity' => 3,
                        'price' => 9.99
                    ],
                    [
                        'product_id' => 'PROD-003',
                        'name' => 'Bolso Deportivo',
                        'quantity' => 1,
                        'price' => 39.99
                    ]
                ]
            ],
            [
                'order_id' => 'ORD-' . date('Y') . '-00002',
                'customer_name' => 'Ana García',
                'customer_email' => 'ana.garcia@example.com',
                'customer_phone' => '+5491145678901',
                'shipping_address' => 'Calle Florida 567',
                'shipping_city' => 'Buenos Aires',
                'shipping_state' => 'CABA',
                'shipping_zip' => 'C1005',
                'shipping_country' => 'Argentina',
                'total_amount' => 799.98,
                'currency' => 'ARS',
                'status' => 'processing',
                'items' => [
                    [
                        'product_id' => 'PROD-004',
                        'name' => 'Notebook Dell XPS 13',
                        'quantity' => 1,
                        'price' => 799.98
                    ]
                ]
            ],
            [
                'order_id' => 'ORD-' . date('Y') . '-00003',
                'customer_name' => 'Carlos Rodríguez',
                'customer_email' => 'carlos.rodriguez@example.com',
                'customer_phone' => '+5491156789012',
                'shipping_address' => 'Av. Cabildo 2345',
                'shipping_city' => 'Buenos Aires',
                'shipping_state' => 'CABA',
                'shipping_zip' => 'C1428',
                'shipping_country' => 'Argentina',
                'total_amount' => 349.99,
                'currency' => 'USD',
                'status' => 'shipped',
                'items' => [
                    [
                        'product_id' => 'PROD-005',
                        'name' => 'Auriculares Sony WH-1000XM5',
                        'quantity' => 1,
                        'price' => 349.99
                    ]
                ]
            ],
            [
                'order_id' => 'ORD-' . date('Y') . '-00004',
                'customer_name' => 'María Fernández',
                'customer_email' => 'maria.fernandez@example.com',
                'customer_phone' => '+5491167890123',
                'shipping_address' => 'Av. Rivadavia 3456',
                'shipping_city' => 'Buenos Aires',
                'shipping_state' => 'CABA',
                'shipping_zip' => 'C1406',
                'shipping_country' => 'Argentina',
                'total_amount' => 129.99,
                'currency' => 'USD',
                'status' => 'delivered',
                'items' => [
                    [
                        'product_id' => 'PROD-006',
                        'name' => 'Silla Gamer Ergonómica',
                        'quantity' => 1,
                        'price' => 129.99
                    ]
                ]
            ],
            [
                'order_id' => 'ORD-' . date('Y') . '-00005',
                'customer_name' => 'Diego Martínez',
                'customer_email' => 'diego.martinez@example.com',
                'customer_phone' => '+5491178901234',
                'shipping_address' => 'Calle Paraguay 876',
                'shipping_city' => 'Córdoba',
                'shipping_state' => 'Córdoba',
                'shipping_zip' => '5000',
                'shipping_country' => 'Argentina',
                'total_amount' => 89.97,
                'currency' => 'USD',
                'status' => 'pending',
                'items' => [
                    [
                        'product_id' => 'PROD-007',
                        'name' => 'Teclado Mecánico RGB',
                        'quantity' => 1,
                        'price' => 69.99
                    ],
                    [
                        'product_id' => 'PROD-008',
                        'name' => 'Mouse Gaming',
                        'quantity' => 1,
                        'price' => 19.98
                    ]
                ]
            ]
        ];

        // Insertar órdenes de ejemplo
        foreach ($sampleOrders as $orderData) {
            Order::create($orderData);
            $this->command->info("Created order: {$orderData['order_id']} - {$orderData['customer_name']}");
        }

        // Crear 10 órdenes aleatorias adicionales
        Order::factory()->count(10)->create();
        
        $this->command->info('✅ Orders table seeded successfully!');
        $this->command->info("📊 Total orders created: " . Order::count());
    }
}
