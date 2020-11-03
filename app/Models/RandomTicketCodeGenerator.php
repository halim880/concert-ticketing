<?php

namespace App\Models;

use App\Facades\TicketCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class RandomTicketCodeGenerator implements TicketCodeGenerator
{
    use HasFactory;

    public function generateFor($ticket)
    {
        return HashIds::encode($ticket->id);
    }
}
