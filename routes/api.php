<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::apiResource('orders', OrderController::class)->only(['store', 'index', 'show']);
    
    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'service' => 'Urbano Express Integration API'
        ]);
    });

    // Ruta para generar datos de prueba
    Route::post('/test-data/generate', function () {
        try {
            if (config('app.simulate_factory_error')) {
                throw new \Exception('Factory error');
            }

            // Crear 5 órdenes de prueba
            Order::factory()
            ->count(5)
            ->testOrder()
            ->create();
            
            return response()->json([
                'message' => '5 órdenes de prueba generadas exitosamente',
                'total_orders' => Order::count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating test data', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Error generando datos de prueba',
                'message' => $e->getMessage()
            ], 500);
        }
    });

    // Ruta para limpiar datos de prueba
    Route::delete('/test-data/clean', function () {
        try {
            if (config('app.simulate_delete_error')) {
                throw new \Exception('Simulated error for testing');
            }

            $count = Order::where('order_id', 'like', 'TEST-%')->count();
            Order::where('order_id', 'like', 'TEST-%')->delete();
            
            return response()->json([
                'message' => 'Datos de prueba eliminados',
                'deleted_count' => $count,
                'remaining_orders' => Order::count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error cleaning test data', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Error limpiando datos de prueba'
            ], 500);
        }
    });
});