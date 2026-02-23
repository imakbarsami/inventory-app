<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            ['name' => 'Walk-in Customer', 'phone' => '00000000000',  'address' => 'N/A'],
            ['name' => 'Md. Rahim', 'phone' => '01711223344','address' => 'Dhaka'],
            ['name' => 'Karim Rahman', 'phone' => '01822334455', 'address' => 'Chattogram'],
            ['name' => 'Sonia Akter', 'phone' => '01933445566', 'address' => 'Sylhet'],
            ['name' => 'Jamal Hossain', 'phone' => '01544556677', 'address' => 'Rajshahi'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
