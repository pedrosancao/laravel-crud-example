<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [[
            'name'        => 'Admin User',
            'email'       => 'admin@sample.com',
            'description' => 'Sample admin',
            'password'    => Hash::make('admin123'),
        ]];
        foreach ($users as $user) {
            if (User::where('email', $user['email'])->count() === 0) {
                User::create($user);
            }
        }
    }
}
