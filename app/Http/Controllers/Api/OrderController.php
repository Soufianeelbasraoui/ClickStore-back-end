<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user() ?? auth('sanctum')->user();
        if (!$user) {
            return response()->json([], 401);
        }

        $orders = Order::where('user_id', $user->id)
            ->with(['user', 'items'])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'total' => 'required|numeric',
            'payment_method' => 'nullable|string',
            'shipping_name' => 'nullable|string',
            'shipping_email' => 'nullable|email',
            'shipping_phone' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.title' => 'required|string',
            'items.*.price' => 'nullable|numeric',
            'items.*.new_price' => 'nullable|numeric',
            'items.*.quantity' => 'nullable|integer',
            'items.*.selected_size' => 'nullable|string',
            'items.*.img' => 'nullable|string',
            'items.*.product_id' => 'nullable|integer',
            'items.*.id' => 'nullable|integer',
        ]);

        $user = $request->user() ?? auth('sanctum')->user();

        $order = DB::transaction(function () use ($validated, $user) {
            $order = Order::create([
                'user_id' => $user?->id,
                'total' => $validated['total'],
                'status' => 'pending',
                'payment_method' => $validated['payment_method'] ?? 'card',
                'shipping_name' => $validated['shipping_name'] ?? $user?->name,
                'shipping_email' => $validated['shipping_email'] ?? $user?->email,
                'shipping_phone' => $validated['shipping_phone'] ?? null,
                'shipping_address' => $validated['shipping_address'] ?? 'Maroc',
            ]);

            foreach ($validated['items'] as $item) {
                $price = $item['price'] ?? $item['new_price'] ?? 0;
                $productId = $item['product_id'] ?? $item['id'] ?? null;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'title' => $item['title'],
                    'price' => $price,
                    'quantity' => $item['quantity'] ?? 1,
                    'selected_size' => $item['selected_size'] ?? null,
                    'img' => $item['img'] ?? null,
                ]);
            }

            return $order->load(['user', 'items']);
        });

        return response()->json($order, 201);
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items'])->findOrFail($id);
        return response()->json($order);
    }
}
