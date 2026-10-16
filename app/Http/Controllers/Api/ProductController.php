<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->boolean('offers') || $request->query('offers') === 'true') {
            $query->whereNotNull('promo')->where('promo', '!=', '');
        }

        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('category', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category') && $request->query('category') !== 'all') {
            $query->where('category', $request->query('category'));
        }

        $products = $query->latest()->get();

        return response()->json($products);
    }

    public function offers(Request $request)
    {
        // Récupérer STRICTEMENT les produits en offre promotionnelle
        $query = Product::whereNotNull('promo')->where('promo', '!=', '');

        if ($request->filled('category') && $request->query('category') !== 'all') {
            $query->where('category', $request->query('category'));
        }

        return response()->json($query->latest()->get());
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'new_price' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'prev_price' => 'nullable|string',
            'promo' => 'nullable|string',
            'img' => 'nullable|string',
            'rating' => 'nullable|numeric',
            'reviews' => 'nullable|string',
            'in_stock' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        $price = $validated['new_price'] ?? $validated['price'] ?? 150;

        $product = Product::create([
            'title' => $validated['title'],
            'category' => $validated['category'] ?? 'Général',
            'new_price' => $price,
            'prev_price' => $validated['prev_price'] ?? null,
            'promo' => $validated['promo'] ?? null,
            'img' => $validated['img'] ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80',
            'rating' => $validated['rating'] ?? 4.5,
            'reviews' => $validated['reviews'] ?? '1k',
            'in_stock' => $validated['in_stock'] ?? true,
            'description' => $validated['description'] ?? '',
        ]);

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'category' => 'nullable|string|max:100',
            'new_price' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'prev_price' => 'nullable|string',
            'promo' => 'nullable|string',
            'img' => 'nullable|string',
            'rating' => 'nullable|numeric',
            'reviews' => 'nullable|string',
            'in_stock' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        if (isset($validated['price']) && !isset($validated['new_price'])) {
            $validated['new_price'] = $validated['price'];
            unset($validated['price']);
        }

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'Produit supprimé avec succès',
        ]);
    }
}
