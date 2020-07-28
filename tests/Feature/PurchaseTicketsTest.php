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
    protected $paymentGateway;
    protected function setUp(): void
    {
        parent::setUp();

        $paymentGateway = new FakePaymentGateway();

        $this->app->instance(PaymentGateway::class, $paymentGateway);
        $this->paymentGateway = $paymentGateway;
    }

    private function orderTicketsRequest($concert, $params)
    {
        return $this->json('post', "/concerts/$concert->id/orders", $params);
    }

    private function assertValidationError($response, $field)
    {
        $response->assertStatus(422);
        $this->assertArrayHasKey($field, $response->decodeResponseJson()['errors']);
    }

    /**
     * Test purchasing a tickets.
     *
     * @return void
     */
    public function test_user_can_purchase_tickets()
    {
        $concert = factory(Concert::class)->state('published')->create(['ticket_price' => 2000]);
        $response = $this->orderTicketsRequest($concert, [
            'email' => 'abdo@email.com',
            'ticket_quantity' => 5,
            'payment_token' => $this->paymentGateway->getValidToken()
        ]);

        $response->assertStatus(201);
        $this->assertEquals(10000, $this->paymentGateway->totalCharges());

        $this->assertTrue($concert->orders->contains(function ($order){
            return $order->email == 'abdo@email.com';
        }));

        $order = $concert->orders->where('email', 'abdo@email.com')->first();
        $this->assertEquals(5, $order->tickets()->count());
    }

    public function test_user_cannot_purchase_an_unpublished_concerts()
    {
        $concert = factory(Concert::class)->state('unpublished')->create();
        $response = $this->orderTicketsRequest($concert, [
            'email' => 'abdo@email.com',
            'ticket_quantity' => 5,
            'payment_token' => $this->paymentGateway->getValidToken()
        ]);

        $response->assertStatus(404);
        $this->assertEquals(0, $concert->orders()->count());
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    public function test_order_not_created_if_invalid_token()
    {
        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->state('published')->create(['ticket_price' => 2000]);
        $response = $this->orderTicketsRequest($concert, [
            'email' => 'abdo@email.com',
            'ticket_quantity' => 5,
            'payment_token' => 'invalid-token'
        ]);

        $response->assertStatus(422);
        $order = $concert->orders->where('email', 'abdo@email.com')->first();
        $this->assertNull($order);
    }

    /**
     * Test email is required in purchasing a ticket.
     */
    public function test_email_is_required_in_purchase_tickets()
    {
        $concert = factory(Concert::class)->state('published')->create(['ticket_price' => 2000]);
        $response = $this->orderTicketsRequest($concert, [
            'ticket_quantity' => 5,
            'payment_token' => $this->paymentGateway->getValidToken()
        ]);

        $response->assertStatus(422);
        $this->assertValidationError($response, 'email');
    }

    /**
     * Test email is valid in purchasing a ticket.
     */
    public function test_email_is_valid_in_purchase_tickets()
    {
        $concert = factory(Concert::class)->state('published')->create(['ticket_price' => 2000]);
        $response = $this->orderTicketsRequest($concert, [
            'email' => 'not-valid-email',
            'ticket_quantity' => 5,
            'payment_token' => $this->paymentGateway->getValidToken()
        ]);

        $response->assertStatus(422);
        $this->assertValidationError($response, 'email');
    }

    /**
     * Test ticket quantity is required in purchasing a ticket.
     */
    public function test_ticket_quantity_is_required_in_purchase_tickets()
    {
        $concert = factory(Concert::class)->state('published')->create(['ticket_price' => 2000]);
        $response = $this->orderTicketsRequest($concert, [
            'email' => 'abdo@email.com',
            'payment_token' => $this->paymentGateway->getValidToken()
        ]);

        $response->assertStatus(422);
        $this->assertValidationError($response, 'ticket_quantity');
    }

    /**
     * Test ticket_quantity is at least one in purchasing a ticket.
     */
    public function test_ticket_quantity_is_at_least_one_in_purchase_tickets()
    {
        $concert = factory(Concert::class)->state('published')->create(['ticket_price' => 2000]);
        $response = $this->orderTicketsRequest($concert, [
            'email' => 'abdo@email.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidToken()
        ]);

        $response->assertStatus(422);
        $this->assertValidationError($response, 'ticket_quantity');
    }

    /**
     * Test payment token is required in purchasing a ticket.
     */
    public function test_payment_token_is_required_in_purchase_tickets()
    {
        $concert = factory(Concert::class)->state('published')->create(['ticket_price' => 2000]);
        $response = $this->orderTicketsRequest($concert, [
            'email' => 'abdo@email.com',
            'ticket_quantity' => 1
        ]);

        $this->assertValidationError($response, 'payment_token');
    }
}
