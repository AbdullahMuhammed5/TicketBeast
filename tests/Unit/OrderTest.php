<?php


namespace Tests\Unit;


use App\Concert;
use App\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;
    /***
     * @test
     */
    public function test_tickets_are_released_when_order_canceled()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(6);
        $order = $concert->orderTickets('ahmed@email.com', 3);
        $this->assertEquals(3, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(6, $concert->ticketsRemaining());
        $this->assertNull(Order::find($order->id));
    }
}
