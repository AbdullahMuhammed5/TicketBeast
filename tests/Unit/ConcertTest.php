<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;
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

    /***
     * @test
     */
    public function test_concerts_with_published_at_date_are_published(){
        $publishedConcertA = factory(Concert::class)->create([
            "published_at" =>  Carbon::parse('-1 week')
        ]);
        $publishedConcertB = factory(Concert::class)->create([
            "published_at" =>  Carbon::parse('-1 week')
        ]);
        $unpublishedConcert = factory(Concert::class)->create([
            "published_at" => null
        ]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    /**
     * @test
     */
    public function test_user_can_order_concert_tickets()
    {
       $concert = factory(Concert::class)->create();

       $order = $concert->orderTickets('abdo@email.com', 2);

       $this->assertEquals('abdo@email.com', $order->email);
       $this->assertEquals(2, $order->tickets()->count());
    }

}
