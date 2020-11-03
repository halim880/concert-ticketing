<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Details</title>
</head>
<body>

    <p>{{$order->id}}</p>
    <p>{{$order->confirmation_number}}</p>
    <p>{{$order->amount}}</p>
    <p>**** **** **** {{$order->card_last_four}}</p>

    @foreach ($order->tickets as $ticket)
        <p>{{$ticket->code}}</p>
    @endforeach
</body>
</html>