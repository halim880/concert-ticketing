<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;

    protected $guarded = [];

    private $data;

    public function __construct($data){
        $this->data = $data;
    }

    public function getLastFour(){
        return $this->data['card_last_four'];
    }

    public function amount(){
        return $this->data['amount'];
    }
}
