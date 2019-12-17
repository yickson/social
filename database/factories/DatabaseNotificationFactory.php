<?php

use App\User;
use Faker\Generator as Faker;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

$factory->define(DatabaseNotification::class, function (Faker $faker) {
    return [
        'id' => Str::uuid()->toString(),
        'type' => 'App\\Notifications\\ExampleNotification',
        'notifiable_type' => 'App\\User',
        'notifiable_id' => factory(User::class)->create(),
        'data' => [
            'link' => url('/'),
            'message' => 'Mensaje de la notificación'
        ],
        'read_at' => null,
    ];
});
