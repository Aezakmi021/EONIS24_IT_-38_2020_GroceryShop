<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session; // Import the Session class from Stripe
use App\Models\Cart;
use App\Models\Order;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        // Get the cart items from the session
        $cartItems = $request->session()->get('cart', []);

        // If the cart is empty, redirect back with an error message
        if (empty($cartItems)) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.')->setStatusCode(400);
        }

        // Prepare line items array
        $lineItems = [];

        foreach ($cartItems as $item) {
            // Provide a default product name if it's missing
            $productName = isset($item['name']) ? $item['name'] : 'Unnamed Product';

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => ['name' => $productName],
                    'unit_amount' => $item['price'] * 100, // Convert to cents
                ],
                'quantity' => $item['quantity'],
            ];
        }

        // Create Stripe checkout session
        Stripe::setApiKey(env('STRIPE_SK'));
        $session = Session::create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('success'),
            'cancel_url' => route('cart'),
        ]);

        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        // Get the cart items from the session
        $cartItems = $request->session()->get('cart', []);

        // If the cart is empty, redirect back with an error message
        if (empty($cartItems)) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        // Delete the user's cart
        $userId = auth()->user()->id;
        Cart::where('user_id', $userId)->delete();

        // Clear the cart items from the session
        $request->session()->forget('cart');

        // Reduce available_quantity for bought products
        foreach ($cartItems as $cartItem) {
            $product = Product::findOrFail($cartItem['product_id']);
            $product->available_quantity -= $cartItem['quantity'];
            $product->save();
        }

        // Create a new order
        $order = new Order();
        $order->user_id = $userId;
        $order->items = json_encode($cartItems); // Store cart items as JSON
        // Add other order details as needed
        $order->save();

        // Redirect to success view
        return view('success')->with('success', 'Order placed successfully.');
    }

}
