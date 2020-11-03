<?php

namespace App\Models;

class RandomOrderConfirmationNumberGenerator implements OrderConfirmationNumberGenerator
{
    public function generate()
    {
        $pool = "ABCDEFGHIKLMNOPQRSTUVWXYZ23456789";
        return substr(str_shuffle(str_repeat($pool, 24)), 0, 24);
    }
}
