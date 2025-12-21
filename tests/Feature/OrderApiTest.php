<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Configurar para que los logs no llenen la consola durante tests
        Log::spy();
    }

    /** @test */
    public function it_can_create_a_new_order()
    {
        $orderData = [
            'order_id' => 'ORD-2024-001',
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+5491123456789',
            'shipping_address' => '123 Main St',
            'shipping_city' => 'Buenos Aires',
            'shipping_state' => 'CABA',
            'shipping_zip' => 'C1001',
            'shipping_country' => 'Argentina',
            'total_amount' => 99.99,
            'currency' => 'USD',
            'items' => [
                [
                    'product_id' => 'PROD-001',
                    'name' => 'Test Product',
                    'quantity' => 2,
                    'price' => 49.99
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'order_id',
                    'customer_name',
                    'status',
                    'created_at'
                ],
                'links'
            ])
            ->assertJson([
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => 'ORD-2024-001',
                    'customer_name' => 'John Doe',
                    'status' => 'pending'
                ]
            ]);

        $this->assertDatabaseHas('orders', [
            'order_id' => 'ORD-2024-001',
            'customer_email' => 'john@example.com'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_order()
    {
        $response = $this->postJson('/api/v1/orders', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'order_id',
                'customer_name',
                'customer_email',
                'shipping_address',
                'shipping_city',
                'shipping_state',
                'shipping_zip',
                'shipping_country',
                'total_amount',
                'currency',
                'items'
            ]);
    }

    /** @test */
    public function it_validates_unique_order_id()
    {
        $order = Order::factory()->create(['order_id' => 'ORD-2024-001']);

        $orderData = [
            'order_id' => 'ORD-2024-001', // Mismo ID
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'shipping_address' => '123 Main St',
            'shipping_city' => 'Buenos Aires',
            'shipping_state' => 'CABA',
            'shipping_zip' => 'C1001',
            'shipping_country' => 'Argentina',
            'total_amount' => 99.99,
            'currency' => 'USD',
            'items' => [
                [
                    'product_id' => 'PROD-001',
                    'name' => 'Test Product',
                    'quantity' => 1,
                    'price' => 99.99
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_id']);
    }

    /** @test */
    public function it_validates_items_array_structure()
    {
        $orderData = [
            'order_id' => 'ORD-2024-001',
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'shipping_address' => '123 Main St',
            'shipping_city' => 'Buenos Aires',
            'shipping_state' => 'CABA',
            'shipping_zip' => 'C1001',
            'shipping_country' => 'Argentina',
            'total_amount' => 99.99,
            'currency' => 'USD',
            'items' => [
                [
                    // Falta product_id
                    'name' => 'Test Product',
                    'quantity' => -1, // Cantidad inválida
                    'price' => -10 // Precio inválido
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'items.0.product_id',
                'items.0.quantity',
                'items.0.price'
            ]);
    }

    /** @test */
    public function it_can_retrieve_all_orders()
    {
        $orders = Order::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'count'
            ])
            ->assertJsonCount(3, 'data')
            ->assertJson([
                'count' => 3
            ]);
    }

    /** @test */
    public function it_can_retrieve_a_specific_order()
    {
        $order = Order::factory()->create();

        $response = $this->getJson("/api/v1/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'order_id',
                    'customer_name',
                    'customer_email',
                    'items',
                    'created_at'
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $order->id,
                    'order_id' => $order->order_id,
                    'customer_name' => $order->customer_name
                ]
            ]);
    }

    /** @test */
    public function it_returns_404_when_order_not_found()
    {
        $response = $this->getJson('/api/v1/orders/999999');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Order not found'
            ]);
    }

    /** @test */
    public function it_returns_health_check_status()
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'service'
            ])
            ->assertJson([
                'status' => 'ok',
                'service' => 'Urbano Express Integration API'
            ]);
    }

    /** @test */
    public function it_logs_errors_when_creating_order_fails()
    {
        // Forzar un error en la base de datos
        config(['app.simulate_order_error' => true]);

        $orderData = [
            'order_id' => 'ORD-2024-001',
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'shipping_address' => '123 Main St',
            'shipping_city' => 'Buenos Aires',
            'shipping_state' => 'CABA',
            'shipping_zip' => 'C1001',
            'shipping_country' => 'Argentina',
            'total_amount' => 99.99,
            'currency' => 'USD',
            'items' => [
                [
                    'product_id' => 'PROD-001',
                    'name' => 'Test Product',
                    'quantity' => 1,
                    'price' => 99.99
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Internal server error',
                'message' => 'Could not create order'
            ]);
    }

    /** @test */
    public function it_handles_database_errors_when_retrieving_orders()
    {
        // Simular error en la base de datos
        config(['app.simulate_order_error' => true]);

        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Internal server error',
                'message' => 'Could not fetch orders'
            ]);
    }
}