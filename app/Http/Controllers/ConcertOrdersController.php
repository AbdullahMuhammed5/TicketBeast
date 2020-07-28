<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Concert;

class ConcertOrdersController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId){
        $concert = Concert::find($concertId);
        $ticket_quantity = request('ticket_quantity');
        $amount = $ticket_quantity * $concert->ticket_price;
        $token = request('payment_token');
        $this->paymentGateway->charge($amount, $token);
        $order = $concert->orders()->create(['email'=>request('email')]);
        foreach (range(1, $ticket_quantity) as $i){
            $order->tickets()->create([]);
        }
        return response()->json([], 201);
    }
}
