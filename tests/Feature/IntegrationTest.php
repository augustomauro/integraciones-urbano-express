<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function complete_workflow_from_frontend_to_backend()
    {
        // 1. Acceder al frontend
        $frontendResponse = $this->get('/');
        $frontendResponse->assertStatus(200);

        // 2. Probar conexión con API
        $healthResponse = $this->getJson('/api/v1/health');
        $healthResponse->assertStatus(200)
            ->assertJson(['status' => 'ok']);

        // 3. Verificar que no hay órdenes inicialmente
        $ordersResponse = $this->getJson('/api/v1/orders');
        $ordersResponse->assertStatus(200)
            ->assertJson(['count' => 0]);

        // 4. Generar datos de prueba
        $generateResponse = $this->postJson('/api/v1/test-data/generate');
        $generateResponse->assertStatus(200)
            ->assertJson(['total_orders' => 5]);

        // 5. Verificar que se crearon las órdenes
        $ordersResponse = $this->getJson('/api/v1/orders');
        $ordersResponse->assertStatus(200)
            ->assertJson(['count' => 5]);

        // 6. Crear una nueva orden manualmente
        $newOrderData = [
            'order_id' => 'ORD-2024-100',
            'customer_name' => 'Integration Test User',
            'customer_email' => 'integration@test.com',
            'customer_phone' => '+5491100000000',
            'shipping_address' => 'Integration Test Address 123',
            'shipping_city' => 'Integration City',
            'shipping_state' => 'Integration State',
            'shipping_zip' => '12345',
            'shipping_country' => 'Test Country',
            'total_amount' => 199.99,
            'currency' => 'USD',
            'items' => [
                [
                    'product_id' => 'INT-PROD-001',
                    'name' => 'Integration Test Product',
                    'quantity' => 2,
                    'price' => 99.99
                ]
            ]
        ];

        $createResponse = $this->postJson('/api/v1/orders', $newOrderData);
        $createResponse->assertStatus(201);

        // 7. Verificar que ahora hay 6 órdenes
        $ordersResponse = $this->getJson('/api/v1/orders');
        $ordersResponse->assertStatus(200)
            ->assertJson(['count' => 6]);

        // 8. Obtener la orden creada específicamente
        $order = Order::where('order_id', 'ORD-2024-100')->first();
        $this->assertNotNull($order);

        $showResponse = $this->getJson("/api/v1/orders/{$order->id}");
        $showResponse->assertStatus(200)
            ->assertJson([
                'data' => [
                    'order_id' => 'ORD-2024-100',
                    'customer_name' => 'Integration Test User'
                ]
            ]);

        // 9. Limpiar datos de prueba
        $cleanResponse = $this->deleteJson('/api/v1/test-data/clean');
        $cleanResponse->assertStatus(200);

        // 10. Verificar que solo queda la orden manual
        $ordersResponse = $this->getJson('/api/v1/orders');
        $ordersResponse->assertStatus(200)
            ->assertJson(['count' => 1]);

        // Verificar que la orden que queda es la manual
        $remainingOrder = Order::first();
        $this->assertEquals('ORD-2024-100', $remainingOrder->order_id);
    }

    /** @test */
    public function error_handling_in_complete_workflow()
    {
        // 1. Intentar crear orden con datos inválidos
        $invalidOrderData = [
            'order_id' => 'ORD-2024-001',
            // Falta customer_name
            'customer_email' => 'not-an-email',
            'shipping_address' => 'Test',
            'shipping_city' => 'Test',
            'shipping_state' => 'Test',
            'shipping_zip' => 'Test',
            'shipping_country' => 'Test',
            'total_amount' => 'not-a-number',
            'currency' => 'USD',
            'items' => [] // Array vacío
        ];

        $response = $this->postJson('/api/v1/orders', $invalidOrderData);
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'customer_name',
                'customer_email',
                'total_amount',
                'items'
            ]);

        // 2. Intentar obtener orden que no existe
        $response = $this->getJson('/api/v1/orders/999999');
        $response->assertStatus(404);

        // 3. Limpiar cuando no hay datos de prueba
        $response = $this->deleteJson('/api/v1/test-data/clean');
        $response->assertStatus(200)
            ->assertJson(['deleted_count' => 0]);
    }
}