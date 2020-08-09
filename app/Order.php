<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function tickets()
    {
        return $this->hasMany(Ticket::Class);
    }

    public function cancel()
    {
        foreach ($this->tickets as $ticket){
            $ticket->release();
        }
        $this->delete();
    }
}
