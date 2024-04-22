<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        // Retrieve all orders
        $orders = Order::all();

        // Pass orders data to the view
        return response()->view('admin-orders', compact('orders'), 200);
    }

    public function updateStatus(Request $request, Order $order)
    {
        // Validate the incoming request data
        $request->validate([
            'status' => 'required|string|in:shipped,processing',
        ]);

        // Update the order status
        $order->status = $request->status;
        $order->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Order status updated successfully.')->setStatusCode(200);
    }
}
