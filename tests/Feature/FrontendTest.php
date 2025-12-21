<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FrontendTest extends TestCase
{
    /** @test */
    public function it_serves_frontend_html_correctly()
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
            ->assertSee('Urbano Express')
            ->assertSee('Simulador de Integración E-commerce')
            ->assertSee('Crear Nuevo Pedido')
            ->assertSee('Respuesta del Sistema');
    }

    /** @test */
    public function frontend_contains_all_necessary_elements()
    {
        $response = $this->get('/');

        $content = $response->getContent();

        // Verificar que contiene elementos clave del formulario
        $this->assertStringContainsString('id="orderForm"', $content);
        $this->assertStringContainsString('name="customer_name"', $content);
        $this->assertStringContainsString('name="customer_email"', $content);
        $this->assertStringContainsString('name="shipping_address"', $content);
        $this->assertStringContainsString('name="items[0][name]"', $content);
        
        // Verificar botones importantes
        $this->assertStringContainsString('Enviar Pedido a Urbano Express', $content);
        $this->assertStringContainsString('Ver Todos los Pedidos', $content);
        $this->assertStringContainsString('Probar Conexión API', $content);
        
        // Verificar elementos de respuesta
        $this->assertStringContainsString('id="responseContainer"', $content);
        $this->assertStringContainsString('id="alert"', $content);
    }

    /** @test */
    public function frontend_has_correct_api_endpoints_referenced()
    {
        $response = $this->get('/');

        $content = $response->getContent();

        // Verificar que la URL base de la API está referenciada
        $this->assertStringContainsString('http://localhost:8000/api/v1', $content);
        
        // Verificar que se hacen referencias a los endpoints
        $this->assertStringContainsString('/orders', $content);
        $this->assertStringContainsString('/health', $content);
        $this->assertStringContainsString('/test-data/generate', $content);
        $this->assertStringContainsString('/test-data/clean', $content);
    }

    /** @test */
    public function frontend_has_proper_meta_tags()
    {
        $response = $this->get('/');

        $content = $response->getContent();

        $this->assertStringContainsString('<meta charset="UTF-8">', $content);
        $this->assertStringContainsString('<meta name="viewport"', $content);
        $this->assertStringContainsString('<title>Urbano Express', $content);
    }

    /** @test */
    public function frontend_file_exists_and_is_readable()
    {
        $filePath = public_path('frontend/index.html');
        
        $this->assertFileExists($filePath);
        $this->assertFileIsReadable($filePath);
        
        $content = file_get_contents($filePath);
        $this->assertNotEmpty($content);
        $this->assertGreaterThan(1000, strlen($content)); // Debe tener cierto tamaño mínimo
    }
}