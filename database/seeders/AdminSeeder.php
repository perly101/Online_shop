<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create default admin account if it doesn't exist
        Admin::firstOrCreate(
            ['admin_id' => 'ADMIN001'],
            [
                'computer_number' => 0,
                'password' => Hash::make('12345678'), // Default password: 12345678
                'status' => 'active',
            ]
        );
    }
}
