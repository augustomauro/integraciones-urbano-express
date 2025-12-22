<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $citiesArgentina = [
            'Buenos Aires', 'Córdoba', 'Rosario', 'Mendoza', 'La Plata',
            'Mar del Plata', 'San Miguel de Tucumán', 'Salta', 'Santa Fe',
            'San Juan', 'Resistencia', 'Neuquén', 'Corrientes'
        ];

        $statesArgentina = [
            'Buenos Aires', 'CABA', 'Córdoba', 'Santa Fe', 'Mendoza',
            'Tucumán', 'Salta', 'Entre Ríos', 'Corrientes'
        ];

        $customerFirstNames = ['Ana', 'Carlos', 'María', 'Juan', 'Laura', 'Diego', 'Sofía', 'Luis', 'Valeria', 'Pedro'];
        $customerLastNames = ['Gómez', 'Rodríguez', 'Fernández', 'López', 'Martínez', 'Pérez', 'García', 'Sánchez'];
        
        $firstName = $this->faker->randomElement($customerFirstNames);
        $lastName = $this->faker->randomElement($customerLastNames);
        $customerName = "{$firstName} {$lastName}";
        $customerEmail = strtolower("{$firstName}.{$lastName}@example.com");
        
        // Productos comunes de e-commerce
        $products = [
            ['name' => 'Zapatillas Deportivas', 'price_range' => [50, 150]],
            ['name' => 'Notebook 14"', 'price_range' => [500, 1200]],
            ['name' => 'Smartphone', 'price_range' => [300, 800]],
            ['name' => 'Auriculares Inalámbricos', 'price_range' => [50, 200]],
            ['name' => 'Tablet 10"', 'price_range' => [200, 500]],
            ['name' => 'Monitor 24"', 'price_range' => [150, 400]],
            ['name' => 'Teclado Mecánico', 'price_range' => [60, 150]],
            ['name' => 'Mouse Gaming', 'price_range' => [30, 100]],
            ['name' => 'Mochila para Notebook', 'price_range' => [40, 120]],
            ['name' => 'Camiseta Deportiva', 'price_range' => [20, 60]]
        ];

        // Generar 1-3 items por pedido
        $numItems = $this->faker->numberBetween(1, 3);
        $items = [];
        $totalAmount = 0;
        
        for ($i = 0; $i < $numItems; $i++) {
            $product = $this->faker->randomElement($products);
            $quantity = $this->faker->numberBetween(1, 3);
            $price = $this->faker->randomFloat(2, $product['price_range'][0], $product['price_range'][1]);
            $itemTotal = $price * $quantity;
            $totalAmount += $itemTotal;
            
            $items[] = [
                'product_id' => 'PROD-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
                'name' => $product['name'] . ' ' . $this->faker->word(),
                'quantity' => $quantity,
                'price' => $price
            ];
        }

        $statuses = ['pending', 'processing', 'shipped', 'delivered'];

        return [
            'order_id' => 'ORD-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => '+54911' . $this->faker->numberBetween(30000000, 99999999),
            
            'shipping_address' => $this->faker->streetAddress(),
            'shipping_city' => $this->faker->randomElement($citiesArgentina),
            'shipping_state' => $this->faker->randomElement($statesArgentina),
            'shipping_zip' => $this->faker->numerify('####'),
            'shipping_country' => 'Argentina',
            
            'total_amount' => round($totalAmount, 2),
            'currency' => $this->faker->randomElement(['USD', 'ARS', 'EUR']),
            'status' => $this->faker->randomElement($statuses),
            'items' => $items,
            
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            // Lógica adicional después de crear cada orden
        });
    }

    /**
     * Método para generar órdenes con prefijo TEST
     */
    public function testOrder()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_id' => 'TEST-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            ];
        });
    }

    /**
     * Método para generar órdenes con prefijo específico
     */
    public function withPrefix(string $prefix)
    {
        return $this->state(function (array $attributes) use ($prefix) {
            return [
                'order_id' => $prefix . '-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            ];
        });
    }
}
