<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;
class HashIdTicketCodeGenerator implements TicketCodeGenerator
{


    public function generateFor($ticket)
    {
        return Hashids::encode($ticket->id);
        
    }
}
