<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function stats()
    {
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalRevenue = (float) Order::sum('total');

        $statuses = ['Livré' => 'delivered', 'En cours' => 'processing', 'Expédié' => 'shipped', 'En attente' => 'pending'];
        $statusChart = [];
        foreach ($statuses as $label => $key) {
            $statusChart[] = [
                'status' => $label,
                'count' => Order::where('status', $key)->count(),
            ];
        }

        $revenueChart = [
            ['date' => 'Lun', 'revenue' => 1450],
            ['date' => 'Mar', 'revenue' => 2100],
            ['date' => 'Mer', 'revenue' => 1800],
            ['date' => 'Jeu', 'revenue' => 2900],
            ['date' => 'Ven', 'revenue' => 3400],
            ['date' => 'Sam', 'revenue' => 4200],
            ['date' => 'Dim', 'revenue' => 3850],
        ];

        return response()->json([
            'total_users' => $totalUsers,
            'total_products' => $totalProducts,
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'revenue_chart' => $revenueChart,
            'status_chart' => $statusChart,
        ]);
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $users = $query->latest()->get();

        return response()->json($users);
    }

    public function updateUserRole(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,user',
        ]);

        $user = User::findOrFail($id);
        $user->role = $validated['role'];
        $user->save();

        return response()->json([
            'message' => 'Rôle mis à jour avec succès',
            'user' => $user,
        ]);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès',
        ]);
    }

    public function orders(Request $request)
    {
        $query = Order::with(['user', 'items']);

        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(function ($q) use ($s) {
                $q->where('id', 'like', "%{$s}%")
                  ->orWhere('status', 'like', "%{$s}%")
                  ->orWhereHas('user', function ($uq) use ($s) {
                      $uq->where('name', 'like', "%{$s}%")
                         ->orWhere('email', 'like', "%{$s}%");
                  });
            });
        }

        $orders = $query->latest()->get();

        return response()->json($orders);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $validated['status'];
        $order->save();

        return response()->json([
            'message' => 'Statut mis à jour avec succès',
            'order' => $order,
        ]);
    }
}
