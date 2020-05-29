<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            'name' => 'Alfian Dimas',
            'username' => 'fiandimas',
            'password' => app('hash')->make('lizscarlet')
        ];

        User::create($user);
    }
}

