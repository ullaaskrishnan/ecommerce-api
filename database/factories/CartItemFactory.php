<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Cart;
use App\CartItem;
use App\Product;
use Faker\Generator as Faker;

$factory->define(CartItem::class, function (Faker $faker) {
    return [
        'cart_id' => function(){
        	return Cart::all()->random();
        },
        'product_id' =>function(){
        	return Product::all()->random();
        },
        'quantity' => $faker->numberBetween(1, 10),
    ];
});
