<?php
namespace App\Facades;

use App\Models\TicketCodeGenerator;
use Illuminate\Support\Facades\Facade;

class TicketCode extends Facade {
    protected static function getFacadeAccessor()
    {
        return TicketCodeGenerator::class;
    }
}