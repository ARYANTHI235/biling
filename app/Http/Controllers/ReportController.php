<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\BillItem;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Summary report: date range filter (from, to) and per-product in-stock, out-stock, sell amount, remaining amount
     */
    public function summary(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        // default: today
        if (!$from) {
            $from = now()->startOfMonth()->toDateString();
        }
        if (!$to) {
            $to = now()->endOfDay()->toDateString();
        }

        // Load all products
        $products = Product::orderBy('id')->get();

        $sold = BillItem::select('bill_items.product_id', DB::raw('SUM(bill_items.quantity) as sold_qty'))
            ->join('bills', 'bills.id', '=', 'bill_items.bill_id')
            ->whereDate('bills.created_at', '>=', $from)
            ->whereDate('bills.created_at', '<=', $to)
            ->groupBy('bill_items.product_id')
            ->pluck('sold_qty', 'product_id')
            ->toArray();

        // Prepare rows
        $rows = $products->map(function($p) use ($sold) {
            $outStock = isset($sold[$p->id]) ? (int)$sold[$p->id] : 0;
            $inStock = (int)$p->stock; 
            $sellAmount = round($outStock * (float)$p->price, 2);
            $remainingAmount = round($inStock * (float)$p->price, 2);

            return (object)[
                'id' => $p->id,
                'code' => $p->code ?? null,
                'name' => $p->name,
                'rate' => (float)$p->price,
                'in_stock' => $inStock,
                'out_stock' => $outStock,
                'sell_amount' => $sellAmount,
                'remaining_amount' => $remainingAmount,
            ];
        });

        // Totals
        $totals = [
            'in_stock' => $rows->sum('in_stock'),
            'out_stock' => $rows->sum('out_stock'),
            'sell_amount' => $rows->sum('sell_amount'),
            'remaining_amount' => $rows->sum('remaining_amount'),
        ];

        return view('reports.summary', compact('rows', 'totals', 'from', 'to'));
    }
}
