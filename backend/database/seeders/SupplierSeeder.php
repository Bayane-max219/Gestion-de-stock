<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        $suppliers = [
            [
                'name' => 'Tech Supplies Inc.',
                'email' => 'tech@supplies.com',
                'phone' => '+1234567893',
                'address' => '789 Tech Street',
                'contact_person' => 'John Tech',
                'tax_number' => 'TAX123456',
                'is_active' => true
            ],
            [
                'name' => 'Office Solutions Ltd.',
                'email' => 'office@solutions.com',
                'phone' => '+1234567894',
                'address' => '101 Office Road',
                'contact_person' => 'Jane Office',
                'tax_number' => 'TAX789012',
                'is_active' => true
            ],
            [
                'name' => 'General Goods Co.',
                'email' => 'info@generalgoods.com',
                'phone' => '+1234567895',
                'address' => '202 Goods Avenue',
                'contact_person' => 'Mike General',
                'tax_number' => 'TAX345678',
                'is_active' => true
            ]
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}