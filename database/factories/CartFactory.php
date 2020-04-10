<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Cart;
use App\User;
use Faker\Generator as Faker;

$factory->define(Cart::class, function (Faker $faker) {
    return [
        'id' => md5(uniqid(rand(), true)),
        'key' => md5(uniqid(rand(), true)),
        'userID' => function(){
        	return User::all()->random();
        },
    ];
});
