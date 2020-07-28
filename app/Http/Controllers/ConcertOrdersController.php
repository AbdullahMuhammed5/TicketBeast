<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Concert;

class ConcertOrdersController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $concert = Concert::published()->findOrFail($concertId);

        $this->validate(request(), [
            'email' => 'required|email',
            'ticket_quantity' => 'required|numeric|min:1',
            'payment_token' => 'required',
        ]);

        try{
            // Charging the customer
            $ticket_quantity = request('ticket_quantity');
            $amount = $ticket_quantity * $concert->ticket_price;
            $this->paymentGateway->charge($amount, request('payment_token'));

            // creating the order
            $concert->orderTickets(request('email'), $ticket_quantity);
            return response()->json([], 201);
        } catch (PaymentFailedException $e){
            return response()->json([], 422);
        }
    }
}
