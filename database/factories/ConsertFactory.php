<?php

namespace Database\Factories;

use App\Models\Consert;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ConsertFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Consert::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'=> $this->faker->sentence(4),
            'subtitle'=> $this->faker->sentence(10),
            'date'=>  Carbon::createFromTimestamp($this->faker->dateTimeBetween($startDate = '+2 days', $endDate = '+1 week')->getTimeStamp()),
            'ticket_price'=> 30,
            'venue'=> 'The mosh pit',
            'venue_address' => $this->faker->address,
            'city'=> $this->faker->city,
            'state'=> $this->faker->state,
            'zip'=> '3030',
            'additional_information'=> 'For ticket call, 01743-920880',
        ];
    }

    public function published(){
        return $this->state(function (array $attributes) {
            return [
                'published_at' => Carbon::parse('-1 week'),
            ];
        });
    }

    public function unpublished(){
        return $this->state(function (array $attributes) {
            return [
                'published_at' => null,
            ];
        });
    }

}
