<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            $order = Order::createOrder(
                $request->input('event_id'),
                $request->input('event_date'),
                $request->input('ticket_adult_price'),
                $request->input('ticket_adult_quantity'),
                $request->input('ticket_kid_price'),
                $request->input('ticket_kid_quantity'),
                $request->user()->id
            );

            return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
