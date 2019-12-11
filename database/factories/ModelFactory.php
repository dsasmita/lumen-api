<?php

use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'email' => "dangs.work@gmail.com",
        'password' => Hash::make("dangs123"),
        'token' => '123456'
    ];
});

$factory->define(App\Models\ChecklistTemplate::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'checklist_id' => App\Models\Checklist::all()->random()->id,
    ];
});


$factory->define(App\Models\Checklist::class, function (Faker\Generator $faker) {
    return [
        'object_domain' => $faker->jobTitle,
        'object_id' => $faker->randomDigit,
        'description' => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'is_completed' => $faker->boolean,
        'completed_at' => $faker->dateTime(),
        'created_by' => App\User::all()->random()->id,
        'updated_by' => App\User::all()->random()->id,
        'due' => $faker->dateTime(),
        'due_interval' => $faker->randomDigit,
        'due_unit' => $faker->randomElement(['minute','hour','day','week','month']),
        'urgency' => $faker->randomDigit
    ];
});

$factory->define(App\Models\ChecklistItem::class, function (Faker\Generator $faker) {
    return [
        'checklist_id' => App\Models\Checklist::all()->random()->id,
        'assignee_id' => App\User::all()->random()->id,
        'description' => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'is_completed' => $faker->boolean,
        'completed_at' => $faker->dateTime(),
        'created_by' => App\User::all()->random()->id,
        'updated_by' => App\User::all()->random()->id,
        'due' => $faker->dateTime(),
        'urgency' => $faker->randomDigit,
        'assignee_id' => App\User::all()->random()->id
    ];
});