<?php

namespace App\Models;

use App\Billing\NotEnoughTicketsExeption;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consert extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopePublished($query){
        return $query->whereNotNull('published_at');
    }


    public function getPathAttribute(){
        return '/consert/'.$this->id;
    }

    public function getFormattedDateAttribute(){
        return $this->date->format('F j, Y');
    }

    public function getFormattedStartTimeAttribute(){
        return $this->date->format('g:ia');
    }

    public function getFormattedTicketPriceAttribute(){
        return number_format($this->ticket_price/100, 2);
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function tickets(){
        return $this->hasMany(Ticket::class);
    }

    public function addTickets($count){

        foreach (range(1, $count) as $i) {
            $this->tickets()->create([]);
        }
    }

    public function remainingTickets(){
        return $this->tickets()->whereNull('order_id')->count();
    }

    public function orderTickets($email, $count){


        if($this->remainingTickets() < $count){
            throw new NotEnoughTicketsExeption;
        }

        $tickets = $this->tickets()->take($count)->get();
        $order = $this->orders()->create(['email'=> $email]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }
}
