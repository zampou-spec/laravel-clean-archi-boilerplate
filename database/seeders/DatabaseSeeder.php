<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\News;
use App\Models\Product;
use App\Models\User;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Subscribe;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Toure',
            'last_name' => 'Hamed',
            'email' => 'admin@vamosavacilar.com',
            'country' => "Côte d'ivoire",
            'mobile_number' => '+2250709605762',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ]);

        for ($i = 0; $i < 10; $i++) {
            User::create([
                'first_name' => 'Test'.$i,
                'last_name' => 'User'.$i,
                'email' => "test$i@example.com",
                'country' => "Côte d'ivoir".$i,
                'mobile_number' => '+225077841439'.$i,
                'role' => 'user',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ]);
        }

        Course::create([
            'name' => 'Salsa',
            'image' => 'https://placehold.co/200/webp',
            'price_online' => 100,
            'price_classroom' => 102,
            'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
        ]);

        Course::create([
            'name' => 'Sun',
            'image' => 'https://placehold.co/200/webp',
            'price_online' => 101,
            'price_classroom' => 103,
            'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
        ]);

        Subscribe::create([
            'subscribe_type' => 'online',
            'user_id' => 2,
            'course_id' => 1,
        ]);

        Subscribe::create([
            'sold' => 4,
            'subscribe_type' => 'classroom',
            'user_id' => 2,
            'course_id' => 1,
        ]);

        Subscribe::create([
            'sold' => 4,
            'subscribe_type' => 'classroom',
            'user_id' => 2,
            'course_id' => 2,
        ]);

        Chapter::create([
            'name' => 'Deux temps',
            'playlist_id' => 'PLMQTL7J6dPKyyVXski2S8A6mLS7XsMdf-',
            'course_id' => 1
        ]);

        Chapter::create([
            'name' => 'Trois temps',
            'playlist_id' => 'PLMQTL7J6dPKyyVXski2S8A6mLS7XsMdf-',
            'course_id' => 1
        ]);

        for ($i = 0; $i < 10; $i++) {
            News::create([
                'title' => 'Passion and Rythm: Exploring the Allure of Tango '.$i,
                'image' => 'https://placehold.co/600.webp',
                'author' => 'Selected for you '.$i,
                'category' => 'tango, dance, music',
                'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
            ]);
        }

        for ($i = 0; $i < 10; $i++) {
            Product::create([
                'name' => 'Gourde '.$i,
                'image' => 'https://placehold.co/600.webp',
                'price' => $i + 1000,
                'quantity' => $i,
                'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
            ]);
        }

    }
}
