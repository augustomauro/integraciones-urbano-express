<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TestDataRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Log::spy();
    }

    /** @test */
    public function it_can_generate_test_orders()
    {
        // Verificar que no hay órdenes inicialmente
        $this->assertEquals(0, Order::count());

        $response = $this->postJson('/api/v1/test-data/generate');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'total_orders'
            ])
            ->assertJson([
                'message' => '5 órdenes de prueba generadas exitosamente',
                'total_orders' => 5
            ]);

        // Verificar que se crearon 5 órdenes
        $this->assertEquals(5, Order::count());

        // Verificar que todas las órdenes tienen prefijo TEST
        $testOrders = Order::where('order_id', 'like', 'TEST-%')->count();
        $this->assertEquals(5, $testOrders);
    }

    /** @test */
    public function it_generates_test_orders_with_correct_structure()
    {
        $response = $this->postJson('/api/v1/test-data/generate');

        $response->assertStatus(200);

        // Obtener la primera orden creada
        $order = Order::first();

        // Verificar estructura básica
        $this->assertNotNull($order);
        $this->assertStringStartsWith('TEST-', $order->order_id);
        $this->assertContains($order->status, ['pending', 'processing', 'shipped', 'delivered']);
        $this->assertIsArray($order->items);
        $this->assertGreaterThan(0, count($order->items));
        
        // Verificar items
        $firstItem = $order->items[0];
        $this->assertArrayHasKey('product_id', $firstItem);
        $this->assertArrayHasKey('name', $firstItem);
        $this->assertArrayHasKey('quantity', $firstItem);
        $this->assertArrayHasKey('price', $firstItem);
        
        $this->assertStringStartsWith('PROD-', $firstItem['product_id']);
        $this->assertIsInt($firstItem['quantity']);
        $this->assertIsFloat($firstItem['price']);
        $this->assertGreaterThan(0, $firstItem['quantity']);
        $this->assertGreaterThan(0, $firstItem['price']);
    }

    /** @test */
    public function it_can_clean_test_orders()
    {
        // Primero crear algunas órdenes de prueba
        Order::factory()->count(3)->testOrder()->create();
        Order::factory()->count(2)->withPrefix('ORD')->create(); // Órdenes no TEST

        $this->assertEquals(5, Order::count());
        $this->assertEquals(3, Order::where('order_id', 'like', 'TEST-%')->count());

        $response = $this->deleteJson('/api/v1/test-data/clean');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'deleted_count',
                'remaining_orders'
            ])
            ->assertJson([
                'deleted_count' => 3,
                'remaining_orders' => 2
            ]);

        // Verificar que solo se eliminaron órdenes TEST
        $this->assertEquals(2, Order::count());
        $this->assertEquals(0, Order::where('order_id', 'like', 'TEST-%')->count());
        $this->assertEquals(2, Order::where('order_id', 'not like', 'TEST-%')->count());
    }

    /** @test */
    public function it_handles_clean_when_no_test_orders_exist()
    {
        // Crear solo órdenes no TEST
        Order::factory()->count(2)->withPrefix('ORD')->create();

        $response = $this->deleteJson('/api/v1/test-data/clean');

        $response->assertStatus(200)
            ->assertJson([
                'deleted_count' => 0,
                'remaining_orders' => 2
            ]);
    }

    /** @test */
    public function it_logs_errors_when_generating_test_data_fails()
    {
        // Simular error en el factory
        config(['app.simulate_factory_error' => true]);

        $response = $this->postJson('/api/v1/test-data/generate');

        $response->assertStatus(500)
            ->assertJsonStructure([
                'error',
                'message'
            ]);
    }

    /** @test */
    public function it_logs_errors_when_cleaning_test_data_fails()
    {
        // Crear algunas órdenes
        Order::factory()->count(2)->testOrder()->create();

        // Simular error en la eliminación
        config(['app.simulate_delete_error' => true]);

        $response = $this->deleteJson('/api/v1/test-data/clean');

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Error limpiando datos de prueba'
            ]);
    }

    /** @test */
    public function it_preserves_non_test_orders_when_cleaning()
    {
        // Crear una mezcla de órdenes
        $testOrders = Order::factory()->count(3)->testOrder()->create();
        $realOrders = Order::factory()->count(2)->create([
            'order_id' => function() {
                return 'ORD-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        ]);

        $response = $this->deleteJson('/api/v1/test-data/clean');

        $response->assertStatus(200);

        // Verificar que las órdenes REALES aún existen
        foreach ($realOrders as $order) {
            $this->assertDatabaseHas('orders', [
                'id' => $order->id,
                'order_id' => $order->order_id
            ]);
        }

        // Verificar que las órdenes TEST fueron eliminadas
        foreach ($testOrders as $order) {
            $this->assertDatabaseMissing('orders', [
                'id' => $order->id
            ]);
        }
    }

    /** @test */
    public function test_orders_have_consistent_data_structure()
    {
        $response = $this->postJson('/api/v1/test-data/generate');

        $response->assertStatus(200);

        $orders = Order::all();

        foreach ($orders as $order) {
            // Verificar campos obligatorios
            $this->assertNotEmpty($order->order_id);
            $this->assertNotEmpty($order->customer_name);
            $this->assertNotEmpty($order->customer_email);
            $this->assertNotEmpty($order->shipping_address);
            $this->assertNotEmpty($order->shipping_city);
            $this->assertNotEmpty($order->shipping_state);
            $this->assertNotEmpty($order->shipping_zip);
            $this->assertNotEmpty($order->shipping_country);
            $this->assertNotNull($order->total_amount);
            $this->assertNotEmpty($order->currency);
            $this->assertNotEmpty($order->status);

            // Verificar tipos de datos
            $this->assertIsFloat((float)$order->total_amount);
            $this->assertIsArray($order->items);
            $this->assertTrue($order->total_amount > 0);
        }
    }

    /** @test */
    public function it_generates_different_order_ids()
    {
        $response = $this->postJson('/api/v1/test-data/generate');

        $response->assertStatus(200);

        $orderIds = Order::pluck('order_id')->toArray();

        // Verificar que todos los IDs son únicos
        $this->assertCount(5, array_unique($orderIds));

        // Verificar formato de los IDs
        foreach ($orderIds as $orderId) {
            $this->assertMatchesRegularExpression('/^TEST-\d{4}-\d{4}$/', $orderId);
        }
    }
}