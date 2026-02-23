<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request){

        $query = Product::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sortColumn = $request->input('sort', 'created_at'); 
        $sortDirection = $request->input('direction', 'desc'); 

        $allowedSorts = ['id', 'name', 'purchase_price', 'sell_price', 'stock', 'created_at'];
        
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $products = $query->paginate(10)->withQueryString();
        //dd($products);
        return view('products.list', compact('products'));
    }
}
