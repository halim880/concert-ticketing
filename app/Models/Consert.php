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
        return $this->belongsToMany(Order::class, 'tickets');
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
        return $this->tickets()->available()->count();
    }

    // public function orderTickets($email, $ticketQuantity){
    //     $tickets = $this->findTickets($ticketQuantity);
    //     return $this->createOrder($email, $tickets);
    // }

    public function reserveTicket($quantity){
        return $this->findTickets($quantity)->each(function ($ticket){
            $ticket->reserve();
        });
    }

    public function findTickets($ticketQuantity){

        if($this->remainingTickets() < $ticketQuantity){
            throw new NotEnoughTicketsExeption;
        }

        return $this->tickets()->take($ticketQuantity)->get();
    }

    public function createOrder($email, $tickets){

        $order = Order::forTickets($tickets, $email, $tickets->sum('price'));

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    public function hasOrderFor($email){
        if($this->orders()->where('email', $email)->get()->first()) return true;
        else return false;
    }
}
