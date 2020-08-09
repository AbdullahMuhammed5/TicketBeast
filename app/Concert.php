<?php

namespace App;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

    public function getFormattedDateAttribute(){
        return $this->date->format('F j, Y');
    }

    public function getStartDateAttribute(){
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollarsAttribute(){
        return number_format($this->ticket_price / 100 , 2);
    }

    public function scopePublished($query){
        return $query->whereNotNull('published_at');
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::Class);
    }

    public function orderTickets($email, $ticket_quantity)
    {
        $tickets = $this->tickets()->available()->take($ticket_quantity)->get();

        if ($tickets->count() < $ticket_quantity){
            throw new NotEnoughTicketsException();
        }

        $order = $this->orders()->create(['email'=>$email]);

        foreach ($tickets as $ticket){
            $order->tickets()->save($ticket);
        }
        return $order;
    }

    public function addTickets($amount)
    {
        foreach (range(1, $amount) as $i){
            $this->tickets()->create([]);
        }
    }

    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }
}
