<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySales = Sale::whereDate('sale_date', Carbon::today())->sum('net_amount');

        $inventoryValue = Product::selectRaw('SUM(stock * purchase_price) as total_value')->first()->total_value ?? 0;

        $cashBalance = Sale::sum('paid_amount');
        //dd($cashBalance);

        $recentSales = Sale::with('customer')->latest()->take(5)->get();

        $lastSevenDays = [];
        $salesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $lastSevenDays[] = $date->format('d M');
            $salesData[] = Sale::whereDate('sale_date', $date)->sum('net_amount');
        }

        return view('dashboard', compact(
            'todaySales', 
            'inventoryValue', 
            'cashBalance', 
            'recentSales',
            'lastSevenDays',
            'salesData'
        ));
    }
}
