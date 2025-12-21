<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            if (config('app.simulate_order_error')) {
                throw new \Exception('Simulated error for testing');
            }

            $orders = Order::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'data' => $orders,
                'count' => $orders->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching orders', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => 'Could not fetch orders'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log de la solicitud recibida
        Log::info('POST /orders request received', [
            'ip' => $request->ip(),
            'data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'order_id' => 'required|string|unique:orders',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable|string',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_zip' => 'required|string',
            'shipping_country' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if (config('app.simulate_order_error')) {
                throw new \Exception('Simulated error for testing');
            }

            $order = Order::create(array_merge(
                $validator->validated(),
                ['status' => 'pending']
            ));

            Log::info('Order created successfully', ['order_id' => $order->id]);

            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order,
                'links' => [
                    'self' => url("/api/orders/{$order->id}"),
                    'all' => url('/api/orders')
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => 'Could not create order'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'error' => 'Order not found'
                ], 404);
            }

            return response()->json([
                'data' => $order
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching order', [
                'error' => $e->getMessage(),
                'order_id' => $id
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => 'Could not fetch order'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
