<?php

namespace Tests\Unit;

use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
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
       $concert->addTickets(20);

       $order = $concert->orderTickets('abdo@email.com', 2);

       $this->assertEquals('abdo@email.com', $order->email);
       $this->assertEquals(2, $order->tickets()->count());
    }

    /**
     * @test
     */
    public function test_can_add_tickets()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(20);
        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    /**
     * @test
     */
    public function test_tickets_remaining_does_not_includes_tickets_associated_with_order()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(20);
        $concert->orderTickets('abdo@email.com', 15);
        $this->assertEquals(5, $concert->ticketsRemaining());
    }

    /**
     * @test
     */
    public function test_trying_to_purchase_more_tickets_than_remain_throws_an_exception()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(20);
        try{
            $concert->orderTickets('abdo@email.com', 21);
        } catch (NotEnoughTicketsException $e){
            $order = $concert->orders()->whereEmail('abdo@email.com')->first();
            $this->assertNull($order);
            $this->assertEquals(20, $concert->ticketsRemaining());
            return;
        }
        $this->fail("fail");
    }

    /**
     * @test
     */
    public function test_cannot_order_tickets_that_already_purchased()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(10);
        $concert->orderTickets('abdo@email.com', 8);

        try{
            $concert->orderTickets('mazen@email.com', 3);
        } catch (NotEnoughTicketsException $e){
            $order = $concert->orders()->whereEmail('mazen@email.com')->first();
            $this->assertNull($order);
            $this->assertEquals(2, $concert->ticketsRemaining());
            return;
        }
        $this->fail("fail");
    }
}
