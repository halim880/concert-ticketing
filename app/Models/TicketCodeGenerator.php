<?php

namespace App\Models;


interface TicketCodeGenerator
{
    public function generateFor($ticket);
}
