<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
   public function index(Request $request)
    {
        $query = Sale::with(['saleItems', 'customer']);

        if ($request->filled('search')) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('sale_date', [ 
                $request->start_date . ' 00:00:00', 
                $request->end_date . ' 23:59:59'
            ]);
        }

        $sales = $query->latest()->paginate(5)->withQueryString();

        return view('sales.list', compact('sales'));
    }

    
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('stock', '>', 0)->orderBy('name')->get(); 

        $nextId = Sale::max('id') + 1;
        $invoice_no = 'INV-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('sales.create', compact('customers', 'products', 'invoice_no'));
    }

    public function store(Request $request)
    {
        //validation
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:1',
            'unit_price' => 'required|array',
            'unit_price.*' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'vat' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $gross_amount = 0;
            $total_cogs = 0; 
            $items = [];

            foreach ($request->product_id as $index => $id) {
                $qty = $request->quantity[$index];
                $price = $request->unit_price[$index];
                $subtotal = $qty * $price;
                $gross_amount += $subtotal;

                $product = Product::findOrFail($id);
                
               //stock validation
                if ($product->stock < $qty) {
                    // return back()->with('error', "Not enough stock for product: " . $product->name)->withInput();
                    throw new \Exception("Not enough stock for {$product->name}. Available: {$product->stock}");
                }

                $total_cogs += ($product->purchase_price * $qty);

                $items[] = [
                    'product_model' => $product,
                    'product_id' => $id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'sub_total' => $subtotal,
                ];
            }

            
            $discount = $request->discount ?? 0;
            $vat = $request->vat ?? 0;
            $net_amount = ($gross_amount - $discount) + $vat;
            $due_amount = $net_amount - $request->paid_amount;

            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'invoice_no' => $request->invoice_no,
                'sale_date' => $request->sale_date,
                'gross_amount' => $gross_amount,
                'discount' => $discount,
                'vat' => $vat,
                'net_amount' => $net_amount,
                'paid_amount' => $request->paid_amount,
                'due_amount' => $due_amount,
            ]);

            foreach ($items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['sub_total'],
                ]);

                $item['product_model']->decrement('stock', $item['quantity']);
            }

            $cashAccount = Account::where('name', 'Cash')->first();
            $salesAccount = Account::where('name', 'Sales Revenue')->first();
            $inventoryAccount = Account::where('name', 'Inventory')->first();
            $cogsAccount = Account::where('name', 'Cost of Goods Sold (COGS)')->first();

            $journalEntry = JournalEntry::create([
                'date' => $request->sale_date,
                'description' => "Products sold. Invoice: " . $sale->invoice_no,
            ]);

            // Sales Revenue (Credit)
            JournalItem::create([
                'journal_entry_id' => $journalEntry->id, 
                'account_id' => $salesAccount->id, 
                'debit' => 0, 
                'credit' => $net_amount
            ]);

            // Cash Received (Debit)
            if ($request->paid_amount > 0) {
                JournalItem::create([
                    'journal_entry_id' => $journalEntry->id, 
                    'account_id' => $cashAccount->id, 
                    'debit' => $request->paid_amount, 
                    'credit' => 0
                ]);
            }

            // COGS (Debit) & Inventory (Credit) 
            JournalItem::create([
                'journal_entry_id' => $journalEntry->id, 
                'account_id' => $cogsAccount->id, 
                'debit' => $total_cogs, 
                'credit' => 0
            ]);
            JournalItem::create([
                'journal_entry_id' => $journalEntry->id, 
                'account_id' => $inventoryAccount->id, 
                'debit' => 0, 
                'credit' => $total_cogs
            ]);

            DB::commit();
            
            return redirect()->route('sales.index')->with('success', 'Sale completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'saleItems.product']);
        
        return view('sales.show', compact('sale'));
    }
}
