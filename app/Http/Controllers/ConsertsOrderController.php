<?php

namespace App\Http\Controllers;

use App\Billing\NotEnoughTicketsExeption;
use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Models\Consert;
use App\Models\Order;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ConsertsOrderController extends Controller
{
    private $paymentGateway;
    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }
    public function store($id){

        $consert = Consert::published()->findOrFail($id);

        request()->validate([
            'email'=> 'required',
            'payment_token'=> 'required',
            'ticket_quantity'=> 'required',
        ]);

    
        try {
            
            $reservation = new Reservation($consert->reserveTicket(request('ticket_quantity')), request('email'));

            $order = $reservation->complete($this->paymentGateway, request('payment_token'));

            return response()->json(json_encode($order), 201);
        }

        catch (PaymentFailedException $th) {
            $reservation->cancel();
            return response()->json([], 422);
        }
        
        catch (NotEnoughTicketsExeption $th) {
 
            return response()->json([], 422);
        }

    }
}
