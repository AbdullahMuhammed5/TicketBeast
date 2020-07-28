<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    /**
     * @test
    */
    public function charges_with_valid_payment_token_are_successful()
    {
        $paymentGateway = new FakePaymentGateway();
        $paymentGateway->charge(2300, $paymentGateway->getValidToken());
        $this->assertEquals(2300, $paymentGateway->totalCharges());
    }
}
