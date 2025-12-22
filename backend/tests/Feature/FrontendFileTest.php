<?php

namespace Tests\Feature;

use Tests\TestCase;

class FrontendFileTest extends TestCase
{
    /** @test */
    public function frontend_html_file_exists_in_project_root()
    {
        // El frontend ahora está en la raíz del proyecto, no en public/
        $filePath = base_path('../frontend/index.html');
        
        $this->assertFileExists($filePath, 
            'El archivo frontend/index.html no existe en la raíz del proyecto');
        
        $this->assertFileIsReadable($filePath,
            'El archivo frontend/index.html no es legible');
    }

    /** @test */
    public function frontend_html_has_minimum_required_content()
    {
        $filePath = base_path('../frontend/index.html');
        
        if (!file_exists($filePath)) {
            $this->markTestSkipped('Archivo frontend/index.html no encontrado');
        }
        
        $content = file_get_contents($filePath);
        
        // Verificar contenido mínimo esperado
        $this->assertNotEmpty($content, 'El archivo frontend está vacío');
        $this->assertGreaterThan(1000, strlen($content), 
            'El archivo frontend es muy pequeño');
        
        // Verificar que contiene elementos esenciales
        $this->assertStringContainsString('Urbano Express', $content,
            'El frontend no contiene el título "Urbano Express"');
            
        $this->assertStringContainsString('<form', $content,
            'El frontend no contiene un formulario');
    }

    /** @test */
    public function frontend_references_correct_api_url()
    {
        $filePath = base_path('../frontend/index.html');
        
        if (!file_exists($filePath)) {
            $this->markTestSkipped('Archivo frontend/index.html no encontrado');
        }
        
        $content = file_get_contents($filePath);
        
        // Verificar que hace referencia a la API en localhost:8000
        $this->assertStringContainsString('localhost:8000', $content,
            'El frontend no referencia localhost:8000 para la API');
            
        $this->assertStringContainsString('/api/v1', $content,
            'El frontend no referencia la ruta /api/v1');
    }
}