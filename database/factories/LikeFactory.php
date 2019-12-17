<?php

use App\User;
use App\Models\Status;
use Faker\Generator as Faker;

$factory->define(App\Models\Like::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create();
        }
    ];
});
