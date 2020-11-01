<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function forTickets($tickets, $email, $amount){
        $order = self::create([
                'email'=> $email,
                'amount'=> $amount,
            ]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    public static function fromReservation($reservation){
        $order = self::create([
                'email'=> $reservation->email,
                'amount'=> $reservation->totalCost(),
            ]);

        foreach ($reservation->getTickets() as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    public function tickets(){
        return $this->hasMany(Ticket::class);
    }

    public function consert(){
        return $this->belongsTo(Consert::class);
    }

    public function ticketQuantity(){
        return $this->tickets()->count();
    }

    public function toArray()
    {
        return [
            'email'=> $this->email,
            'ticket_quantity'=> $this->ticketQuantity(),
            'amount'=> $this->amount,
        ];
    }


}
