<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'sku' => $faker->unique()->md5,
        'Name' => $faker->word,
        'price' =>  $faker->numberBetween($min = 1, $max = 1000),
        'description' =>  $faker->text,
        'UnitsInStock' =>  $faker->numberBetween($min = 1, $max = 50),
    ];
});
