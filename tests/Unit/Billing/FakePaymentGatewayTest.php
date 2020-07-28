<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
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

    /**
     * @test
     */
    public function charges_with_invalid_payment_token_fail()
    {
        try{
            $paymentGateway = new FakePaymentGateway();
            $paymentGateway->charge(2300, 'invalid-token');
        } catch (PaymentFailedException $ex){
            return;
        }

        $this->fail();
    }
}
