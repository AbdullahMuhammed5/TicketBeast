<?php

/** @var Factory $factory */

use App\Concert;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;


$factory->define(\App\Concert::class, function (Faker $faker) {
    return [
        'title'                  => 'The big concert',
        'subtitle'               => 'The biggest concert in the world!',
        'date'                   => Carbon::parse('+2 weeks'),
        'ticket_price'           => 2500,
        'venue'                  => 'London main st',
        'venue_address'          => '123 Example Lane',
        'city'                   => 'London',
        'state'                  => 'chelsea',
        'zip'                    => '12584',
        'additional_information' => 'For tickets, call (555) 555-5555',
    ];
});
