<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

class UserCategoryThreadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Seeder untuk Pengguna
        $userIds = [];

        for ($i = 1; $i <= 10; $i++) {
            $userId = Uuid::uuid4()->toString();
            $userIds[] = $userId;

            DB::table('users')->insert([
                'id' => $userId,
                'name' => 'User ' . $i,
                'username' => 'username' . $i,
                'email' => 'user' . $i . '@example.com',
                'password' => Hash::make('password'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // Seeder untuk Kategori
        $categories = [];

        for ($i = 1; $i <= 10; $i++) {
            $categories[] = [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Category ' . $i,
                'slug' => 'category-' . $i,
                'image' => 'category-' . $i . '.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('thread_categories')->insert($categories);

        // Seeder untuk Thread dalam setiap kategori
        $threads = [];

        foreach ($categories as $category) {
            for ($j = 1; $j <= 2; $j++) {
                $threads[] = [
                    'id' => Uuid::uuid4()->toString(),
                    'title' => 'Thread ' . $j . ' in ' . $category['name'],
                    'slug' => Str::slug('Thread ' . $j . ' in ' . $category['name']),
                    'content' => 'Content of Thread ' . $j . ' in ' . $category['name'] . '.',
                    'image' => 'thread-' . $j . '.jpg',
                    'enable_comment' => true,
                    'user_id' => $userIds[$j - 1], // Menggunakan ID pengguna yang sesuai
                    'thread_category_id' => $category['id'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        
        DB::table('threads')->insert($threads);


        $scientificworks = [];
        for ($j = 1; $j <= 2; $j++) {
            $scientificworks[] = [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'karya ' . $j,
                'slug' => Str::slug('karya ' . $j),
                'content' => 'Content of karya ' . $j,
                'attachment' => 'karya-' . $j . '.pdf',
                'user_id' => $userIds[$j - 1],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        DB::table('scientific_works')->insert($scientificworks);
    }
}
