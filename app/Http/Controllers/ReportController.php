<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function financialReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $salesQuery = Sale::whereBetween('sale_date', [$startDate, $endDate]);

        $totalSales = (clone $salesQuery)->sum('net_amount');

        $totalDiscount = (clone $salesQuery)->sum('discount');

        $totalVat = (clone $salesQuery)->sum('vat');

        $totalCogs = SaleItem::whereIn('sale_id', (clone $salesQuery)->pluck('id'))
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->selectRaw('SUM(sale_items.quantity * products.purchase_price) as cogs')
            ->first()->cogs ?? 0;

        $netProfit = ($totalSales - $totalVat) - ($totalCogs + $totalDiscount);

        $dailyReports = Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->selectRaw('sale_date, SUM(gross_amount) as gross, SUM(discount) as disc, SUM(vat) as vt, SUM(net_amount) as net')
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'desc')
            ->get();

        return view('reports.financial', compact(
            'totalSales', 
            'totalDiscount', 
            'totalVat', 
            'totalCogs', 
            'netProfit', 
            'startDate', 
            'endDate',
            'dailyReports'
        ));
    }
}
