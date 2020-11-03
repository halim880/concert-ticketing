<?php

namespace App\Models;

use App\Facades\OrderConfirmationNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function forTickets($tickets, $email, $charge){
        $order = self::create([
                'email'=> $email,
                'amount'=> $charge->amount(),
                'card_last_four'=> $charge->getLastFour(),
                'confirmation_number'=> OrderConfirmationNumber::generate(),
            ]);

            $tickets->each->claimFor($order);

        return $order;
    }

    public static function fromReservation($reservation){
        $order = self::create([
                'email'=> $reservation->email,
                'amount'=> $reservation->totalCost(),
                'confirmation_number'=> app(RandomOrderConfirmationNumberGenerator::class)->generate(),
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


    public static function findByConfirmationNumber($confirmationNumber){
        return self::where('confirmation_number', $confirmationNumber)->first();
    }


    public function toArray()
    {
        return [
            'email'=> $this->email,
            'amount'=> $this->amount,
            'confirmation_number'=> $this->confirmation_number,
            'tickets'=> $this->tickets->map(function($ticket){
                return ['code'=> $ticket->code];
            })->all(),
        ];
    }



}
