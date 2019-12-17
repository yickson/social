<?php

use App\Models\Status;
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
        User::truncate();
        Status::truncate();

        factory(User::class)->create([
            'name' => 'jorge',
            'email' => 'jorge@email.com'
        ]);
        factory(Status::class, 10)->create();
    }
}
