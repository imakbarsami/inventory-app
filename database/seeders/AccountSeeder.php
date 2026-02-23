<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            ['name' => 'Cash', 'type' => 'Asset'],                          
            ['name' => 'Accounts Receivable', 'type' => 'Asset'],           
            ['name' => 'Inventory', 'type' => 'Asset'],                     
            ['name' => 'Sales Revenue', 'type' => 'Revenue'],               
            ['name' => 'Discount Expense', 'type' => 'Expense'],            
            ['name' => 'Cost of Goods Sold (COGS)', 'type' => 'Expense'],   
            ['name' => 'VAT Payable', 'type' => 'Liability'],               
        ];

        foreach ($accounts as $account) {
            Account::firstOrCreate(['name' => $account['name']], $account);
        }
    }
}
