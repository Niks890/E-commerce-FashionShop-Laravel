<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueInventoryController extends Controller
{
public function revenueInventory(Request $request)
{
    $query = DB::table('product_variants as pv')
        ->join('products as p', 'pv.product_id', '=', 'p.id')
        ->select('pv.id as variant_id', 'p.product_name', 'p.id as product_id', 'pv.color', 'pv.size', 'pv.stock')
        ->where('pv.stock', '>=', 0);

    // Lọc theo các trường
    if ($request->filled('product_name')) {
        $query->where('p.product_name', 'like', '%' . $request->product_name . '%');
    }

    if ($request->filled('color')) {
        $query->where('pv.color', 'like', '%' . $request->color . '%');
    }

    if ($request->filled('size')) {
        $query->where('pv.size', $request->size);
    }

   if ($request->filled('stock_status')) {
        switch ($request->stock_status) {
            case 'low':
                $query->where('stock', '<', 5)->where('stock', '>', 0);
                break;
            case 'out':
                $query->where('stock', '<=', 0);
                break;
            case 'available':
                $query->where('stock', '>', 0)->orderBy('stock', 'desc');
                break;
        }
    }

    $inventoryRevenueCurrent = $query->orderBy('p.product_name')
        ->paginate(10)
        ->appends($request->query());

    return view('admin.inventory.revenue.revenue-inventory', compact('inventoryRevenueCurrent'));
}


}
