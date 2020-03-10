<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    /***
     * @test
     */
    public function test_can_get_formatted_date()
    {
        $concert = factory('App\Concert')->make([
           'date' => Carbon::parse('2020-8-12 9:00pm')
        ]);

        $date = $concert->formatted_date;

        $this->assertEquals('August 12, 2020', $date);
    }

    /***
     * @test
     */
    public function test_can_get_start_date()
    {
        $concert = factory('App\Concert')->make([
            'date' => Carbon::parse('2020-8-12 21:00:00')
        ]);

        $start_date = $concert->start_date;

        $this->assertEquals('9:00pm', $start_date);
    }

    /***
     * @test
     */
    public function test_can_get_price_ticket_in_dollars()
    {
        $concert = factory('App\Concert')->make([
            'ticket_price' => 2500
        ]);

        $price = $concert->ticket_price_in_dollars;

        $this->assertEquals('25.00', $price);
    }

}
