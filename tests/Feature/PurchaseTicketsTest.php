<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_user_can_purchase_tickets()
    {
        $paymentGateway = new FakePaymentGateway();

        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $concert = factory(Concert::class)->create(['ticket_price' => 2000]);
        $response = $this->json('post', "/concerts/$concert->id/orders", [
            'email' => 'abdo@email.com',
            'ticket_quantity' => 5,
            'payment_token' => $paymentGateway->getValidToken()
        ]);

        $response->assertStatus(201);
        $this->assertEquals(10000, $paymentGateway->totalCharges());

        $this->assertTrue($concert->orders->contains(function ($order){
            return $order->email == 'abdo@email.com';
        }));

        $order = $concert->orders->where('email', 'abdo@email.com')->first();
        $this->assertEquals(5, $order->tickets()->count());
    }
}
