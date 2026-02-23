<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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


    public function create(){
        return view('products.create');
    }


    public function store(Request $request){
        //validation
        $request->validate([
            'name' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        
        DB::beginTransaction();

        try {
            $product = Product::create($request->all());

            if ($product->stock > 0) {

                $totalValue = $product->stock * $product->purchase_price;

                $inventoryAccount = Account::where('name', 'Inventory')->first();
                $cashAccount = Account::where('name', 'Cash')->first();

                $journalEntry = JournalEntry::create([
                    'date' => now()->toDateString(),
                    'description' => "Opening stock added for product: " . $product->name,
                ]);

                //debit inventory
                JournalItem::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $inventoryAccount->id,
                    'debit' => $totalValue,
                    'credit' => 0,
                ]);

                //credit cash
                JournalItem::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $cashAccount->id,
                    'debit' => 0,
                    'credit' => $totalValue,
                ]);
            }

            DB::commit(); 
            
            return redirect()->route('products.index')->with('success', 'Product added successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); 
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        //validation
        $request->validate([
            'name' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $oldStock = $product->stock; 
            $newStock = $request->stock; 
            
            $product->update($request->all());

            $stockDifference = $newStock - $oldStock;

            if ($stockDifference != 0) {
                $inventoryAccount = Account::where('name', 'Inventory')->first();
                $cashAccount = Account::where('name', 'Cash')->first();
                $cogsAccount = Account::where('name', 'Cost of Goods Sold (COGS)')->first();

                $journalEntry = JournalEntry::create([
                    'date' => now()->toDateString(),
                    'description' => "Stock adjusted manually for product: " . $product->name,
                ]);

                if ($stockDifference > 0) {
                    $totalValue = $stockDifference * $product->purchase_price;

                    JournalItem::create([
                        'journal_entry_id' => $journalEntry->id, 
                        'account_id' => $inventoryAccount->id, 
                        'debit' => $totalValue, 
                        'credit' => 0
                    ]);

                    JournalItem::create(
                        ['journal_entry_id' => $journalEntry->id, 
                        'account_id' => $cashAccount->id, 
                        'debit' => 0, 
                        'credit' => $totalValue
                    ]);
                } else {
                    $lossValue = abs($stockDifference) * $product->purchase_price; 

                    JournalItem::create([
                        'journal_entry_id' => $journalEntry->id, 
                        'account_id' => $cogsAccount->id, 
                        'debit' => $lossValue, 
                        'credit' => 0
                    ]);

                    JournalItem::create([
                        'journal_entry_id' => $journalEntry->id, 
                        'account_id' => $inventoryAccount->id, 
                        'debit' => 0, 
                        'credit' => $lossValue
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Product updated and accounts adjusted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong! ' . $e->getMessage());
        }
    }
}