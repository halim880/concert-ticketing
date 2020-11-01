<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    private $tickets;
    private $email;

    public function __construct($tickets, $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    public function getTickets(){
        return $this->tickets;
    }

    public function getEmailAttribute(){
        return $this->email;
    }

    public function totalCost(){
        return $this->tickets->sum('price');
    }


    public function complete($paymentGateway, $token){
        $paymentGateway->charge($this->totalCost(), $token);
        return  Order::fromReservation($this);
    }

    public function cancel(){
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
    }
}
