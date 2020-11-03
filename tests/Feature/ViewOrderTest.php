<?php

namespace Tests\Feature;

use App\Models\Consert;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewOrderTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function user_can_view_their_order_confirmation()
    {
        $this->withoutExceptionHandling();
        $consert = Consert::factory()->create();
        $order = Order::factory()->create([
            'confirmation_number'=> 'ORDERCONFIRMATION123',
            'card_last_four'=> '1234'
        ]);
        $ticketA = Ticket::factory()->create([
            'consert_id'=> $consert->id,
            'order_id'=> $order->id,
            'code'=> 'TICKETCODE123',
        ]);

        $ticketB = Ticket::factory()->create([
            'consert_id'=> $consert->id,
            'order_id'=> $order->id,
            'code'=> 'TICKETCODE456',
        ]);

        $response = $this->get('/orders/ORDERCONFIRMATION123');

        $response->assertStatus(200);

        $response->assertViewHas('order', $order);

        $response->assertSee($order->id);
        $response->assertSee($order->amount);
        $response->assertSee($order->confirmation_number);
        $response->assertSee('**** **** **** 1234');
        $response->assertSee('TICKETCODE123');
        $response->assertSee('TICKETCODE456');

    }
}
