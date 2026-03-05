<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->withCount('activeProducts')->get();
        $customers = Customer::orderBy('name')->get();

        return view('pos.index', compact('categories', 'customers'));
    }

    public function getProducts(Request $request)
    {
        $query = Product::where('is_active', true)->where('stock', '>', 0);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->with('category')->get();

        return response()->json($products);
    }

    public function processOrder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_id' => 'nullable|exists:customers,id',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => 'in:fixed,percentage',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'paid' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,ewallet,transfer',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$product->name} tidak mencukupi! Tersisa: {$product->stock}"
                    ], 422);
                }

                $itemSubtotal = $product->selling_price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->selling_price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Calculate discount
            $discount = $validated['discount'] ?? 0;
            $discountType = $validated['discount_type'] ?? 'fixed';
            $discountAmount = $discountType === 'percentage'
                ? ($subtotal * $discount / 100)
                : $discount;

            // Calculate tax
            $taxRate = $validated['tax_rate'] ?? 0;
            $tax = ($subtotal - $discountAmount) * $taxRate / 100;

            $total = $subtotal - $discountAmount + $tax;
            $paid = $validated['paid'];
            $change = $paid - $total;

            if ($change < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran kurang! Kurang: Rp ' . number_format(abs($change), 0, ',', '.')
                ], 422);
            }

            // Create order
            $order = Order::create([
                'invoice_number' => Order::generateInvoiceNumber(),
                'user_id' => auth()->id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discountAmount,
                'discount_type' => $discountType,
                'total' => $total,
                'paid' => $paid,
                'change' => $change,
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create order items & update stock
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                $stockBefore = $item['product']->stock;
                $item['product']->decrement('stock', $item['quantity']);

                StockMovement::create([
                    'product_id' => $item['product']->id,
                    'user_id' => auth()->id(),
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockBefore - $item['quantity'],
                    'reference' => $order->invoice_number,
                    'notes' => 'Penjualan POS',
                ]);
            }

            // Update customer total purchases
            if ($order->customer_id) {
                $order->customer->increment('total_purchases', $total);
            }

            DB::commit();

            $order->load(['items', 'customer', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'order' => $order,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function receipt(Order $order)
    {
        $order->load(['items', 'customer', 'user']);
        return view('pos.receipt', compact('order'));
    }
}
