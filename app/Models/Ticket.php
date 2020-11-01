<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeAvailable($query){
        return $query->whereNull('order_id')->whereNull('reserved_at');
    }


    public function release(){
        $this->update(['order_id'=>null, 'reserved_at'=>null]);
    }

    public function reserve(){
        $this->update(['reserved_at'=>Carbon::now()]);
    }


    public function consert(){
        return $this->belongsTo(Consert::class);
    }

    public function getPriceAttribute(){
        return $this->consert->ticket_price;
    }
}
