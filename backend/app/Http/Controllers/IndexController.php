<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Usar configuración dinámica
        if (! config('frontend.serve_integrated')) {
            return redirect()->away(config('frontend.external_url', 'http://localhost:8080'));
        }
        
        // DESARROLLO LOCAL: Servir frontend
        return $this->serveLocalFrontend();
    }

    private function serveLocalFrontend()
    {
        try {
            $htmlContent = Storage::disk('external_html')->get('index.html');
            
            return response($htmlContent)
                ->header('Content-Type', 'text/html; charset=UTF-8');
                
        } catch (\Exception $e) {
            return response()->view('frontend-error', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
