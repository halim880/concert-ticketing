<?php

namespace Database\Factories;

use App\Models\Consert;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'consert_id' => function(){
                return Consert::factory()->create()->id;
            }
            
        ];
    }
}
