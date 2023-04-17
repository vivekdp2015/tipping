<?php

use Illuminate\Database\Seeder;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        User::create([
            'first_name' => 'admin',
            'last_name' => 'admin',
            'email' => 'admin@tippingJar.com',
            'about' => 'Admin User',
            'type' => 'admin',
            'password' => bcrypt('12345678'),
        ]);
    }
}
