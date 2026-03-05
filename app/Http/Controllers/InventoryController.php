<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'low':
                    $query->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0);
                    break;
                case 'out':
                    $query->where('stock', 0);
                    break;
                case 'normal':
                    $query->whereColumn('stock', '>', 'min_stock');
                    break;
            }
        }

        $products = $query->orderBy('stock', 'asc')->paginate(20);

        $totalProducts = Product::where('is_active', true)->count();
        $lowStock = Product::where('is_active', true)->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0)->count();
        $outOfStock = Product::where('is_active', true)->where('stock', 0)->count();
        $totalValue = Product::where('is_active', true)->selectRaw('SUM(stock * cost_price) as total')->value('total') ?? 0;

        return view('inventory.index', compact('products', 'totalProducts', 'lowStock', 'outOfStock', 'totalValue'));
    }

    public function adjustStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $stockBefore = $product->stock;

        if ($validated['type'] === 'in') {
            $product->increment('stock', $validated['quantity']);
            $stockAfter = $stockBefore + $validated['quantity'];
        } elseif ($validated['type'] === 'out') {
            if ($product->stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi!'
                ], 422);
            }
            $product->decrement('stock', $validated['quantity']);
            $stockAfter = $stockBefore - $validated['quantity'];
        } else {
            $product->update(['stock' => $validated['quantity']]);
            $stockAfter = $validated['quantity'];
        }

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'reference' => 'Manual Adjustment',
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil diperbarui!',
            'new_stock' => $product->fresh()->stock,
        ]);
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with(['product', 'user']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $movements = $query->latest()->paginate(25);
        $products = Product::orderBy('name')->get();

        return view('inventory.movements', compact('movements', 'products'));
    }
}
