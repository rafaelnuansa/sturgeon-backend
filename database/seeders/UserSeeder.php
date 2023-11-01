<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'name' => 'Baby Shark',
            'username' => 'babyshark',
            'email' => 'babyshark@unida.ac.id',
            'password' => Hash::make('password'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        User::create([
            'name' => 'Baby Shark 2',
            'username' => 'babyshark2',
            'email' => 'babyshark2@unida.ac.id',
            'password' => Hash::make('password'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $faker = FakerFactory::create();

        for ($i = 1; $i <= 20; $i++) {
            User::create([
                'name' => $faker->name,
                'username' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'), // Anda dapat mengganti kata sandi sesuai kebutuhan.
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
