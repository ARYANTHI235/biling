<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $bills = Bill::orderBy('id', 'desc')->get();

        return view('bills.index', compact('bills'));
    }

    public function create()
    {
        $products = Product::where('stock','>',0)->get();

        // Get last bill number and increment
        $lastBill = \App\Models\Bill::orderBy('id', 'desc')->first();
        $nextNumber = $lastBill ? str_pad($lastBill->id + 1, 3, '0', STR_PAD_LEFT) : '001';

        return view('bills.create', compact('products', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bill_no' => 'required|unique:bills,bill_no',
            'customer_name' => 'required',
            'items' => 'required|array|min:1'
        ]);

        // Combine items by product_id
        $combined = [];
        foreach ($request->items as $it) {
            $pid = $it['product_id'];
            if (isset($combined[$pid])) {
                $combined[$pid]['quantity'] += $it['quantity'];
                $combined[$pid]['total'] += $it['price'] * $it['quantity'];
            } else {
                $combined[$pid] = [
                    'product_id' => $pid,
                    'quantity' => $it['quantity'],
                    'price' => $it['price'],
                    'total' => $it['price'] * $it['quantity'],
                ];
            }
        }
        $items = array_values($combined);

        DB::beginTransaction();
        try {
            $total = array_sum(array_column($items, 'total'));

            $bill = Bill::create([
                'bill_no' => $request->bill_no,
                'customer_name' => $request->customer_name,
                'total_amount' => $total,
            ]);

            foreach ($items as $it) {
                BillItem::create([
                    'bill_id' => $bill->id,
                    'product_id' => $it['product_id'],
                    'quantity' => $it['quantity'],
                    'price' => $it['price'],
                    'total' => $it['total'],
                ]);

                // reduce stock
                $p = Product::find($it['product_id']);
                if ($p) {
                    $p->decrement('stock', $it['quantity']);
                }
            }

            DB::commit();
            return redirect()->route('bills.index')->with('success','Bill created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('Error creating bill: ' . $e->getMessage());
        }
    }

    public function show(Bill $bill)
    {
        $bill->load('items.product');
        return view('bills.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        $bill->load('items.product');
        $products = Product::where('stock','>',0)
            ->orWhereIn('id', $bill->items->pluck('product_id'))->get();

        // Prepare items array for JS
        $jsItems = $bill->items->map(function($it){
            return [
                'product_id' => $it->product_id,
                'name' => optional($it->product)->name,
                'price' => $it->price,
                'quantity' => $it->quantity
            ];
        });

        return view('bills.edit', compact('bill', 'products', 'jsItems'));
    }

    public function update(Request $request, Bill $bill)
    {
        $request->validate([
            'customer_name' => 'required',
            'items' => 'required|array|min:1'
        ]);

        // Combine items by product_id
        $combined = [];
        foreach ($request->items as $it) {
            $pid = $it['product_id'];
            if (isset($combined[$pid])) {
                $combined[$pid]['quantity'] += $it['quantity'];
                $combined[$pid]['total'] += $it['price'] * $it['quantity'];
            } else {
                $combined[$pid] = [
                    'product_id' => $pid,
                    'quantity' => $it['quantity'],
                    'price' => $it['price'],
                    'total' => $it['price'] * $it['quantity'],
                ];
            }
        }
        $items = array_values($combined);

        DB::beginTransaction();
        try {
            // Restore stock for old items
            foreach ($bill->items as $oldItem) {
                $product = Product::find($oldItem->product_id);
                if ($product) {
                    $product->increment('stock', $oldItem->quantity);
                }
            }

            $total = array_sum(array_column($items, 'total'));

            $bill->update([
                'customer_name' => $request->customer_name,
                'total_amount' => $total,
            ]);

            // Delete old items
            $bill->items()->delete();

            // Add new items and reduce stock
            foreach ($items as $it) {
                BillItem::create([
                    'bill_id' => $bill->id,
                    'product_id' => $it['product_id'],
                    'quantity' => $it['quantity'],
                    'price' => $it['price'],
                    'total' => $it['total'],
                ]);
                $p = Product::find($it['product_id']);
                if ($p) {
                    $p->decrement('stock', $it['quantity']);
                }
            }

            DB::commit();
            return redirect()->route('bills.index')->with('success','Bill updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('Error updating bill: ' . $e->getMessage());
        }
    }
}
