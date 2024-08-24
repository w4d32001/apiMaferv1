<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            'name' => 'wade',
            'surname' => 'wade',
            'dni' => 'wade',
            'ruc' => 'awade',
            'image' => "http://127.0.0.1:8000/storage/images/logo_perfil.jpeg",
            'customer_type_id' => 1,
            'reason' => '',
            'address' => '',
            'email' => 'wade@gmail.com',
            'password' => Hash::make('12345678'),
            'phone' => '1234566789'
        ]);
    }
}
