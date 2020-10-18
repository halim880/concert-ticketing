<?php

namespace App\Http\Controllers;

use App\Billing\NotEnoughTicketsExeption;
use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Models\Consert;
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
            
            $order = $consert->orderTickets(request('email'), request('ticket_quantity'));
            $this->paymentGateway->charge(request('ticket_quantity')* $consert->ticket_price, request('payment_token'));
            return response()->json([], 201);
        }

        catch (PaymentFailedException $th) {
            $order->cancel();
            return response()->json([], 422);
        }
        
        catch (NotEnoughTicketsExeption $th) {
            return response()->json([], 422);
        }

    }
}
