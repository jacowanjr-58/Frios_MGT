<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('customers')->insert([
            [
                'franchisee_id' => 9,
                'name' => 'Jane Doe',
                'address1' => '123 Main St',
                'address2' => 'Apt 4B',
                'zip_code' => '90210',
                'state' => 'CA',
                'email' => 'jane.doe@example.com',
                'phone' => '555-1234',
                'notes' => 'VIP customer',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'franchisee_id' => 9,
                'name' => 'John Smith',
                'address1' => '456 Oak Ave',
                'address2' => null,
                'zip_code' => '10001',
                'state' => 'NY',
                'email' => 'john.smith@example.com',
                'phone' => '555-5678',
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'franchisee_id' => 9,
                'name' => 'Ava Patel',
                'address1' => '789 Pine Blvd',
                'address2' => 'Suite 101',
                'zip_code' => '60601',
                'state' => 'IL',
                'email' => 'ava.patel@example.com',
                'phone' => '555-8765',
                'notes' => 'Loyal member',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
