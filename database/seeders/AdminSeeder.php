<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'nomor_hp' => '081234567890',
            'alamat' => 'Alamat Admin',
            'role' => 'Super Admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'verif_wa' => true,
        ]);

        $this->command->info('Admin user created successfully!');
    }
}
