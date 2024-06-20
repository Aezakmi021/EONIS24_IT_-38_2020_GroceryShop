<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session; // Import the Session class from Stripe
use App\Models\Cart;
use App\Models\Order;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

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
            'success_url' => route('success', $request->all()),
            'cancel_url' => route('cart'),
        ]);

        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        $requestParameters = $request->all();
        // Get the cart items from the session
        $cartItems = $request->session()->get('cart', []);
        $shippingAddress = $request->input('shippingAddress');
        $city = $request->input('city');
        $country = $request->input('country');
        $zipCode = $request->input('zipCode');

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
        $order->shipping_address = $shippingAddress;
        $order->city = $city;
        $order->country = $country;
        $order->zip_code = $zipCode;
        $order->items = json_encode($cartItems); // Store cart items as JSON
        // Add other order details as needed
        $order->save();

        // Redirect to success view
        return view('success')->with('success', 'Order placed successfully.');
    }

    public function handleWebhook(Request $request)
    {
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;

        // Log payload and signature header for debugging
        logger()->info('Stripe Webhook Payload:', ['payload' => $payload]);
        logger()->info('Stripe Signature Header:', ['sig_header' => $sig_header]);


        try {
            // Verify webhook signature
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (SignatureVerificationException $e) {
            // Log error for debugging
            logger()->error('Webhook signature verification failed', ['exception' => $e]);

            // Invalid signature
            return response()->json(['error' => 'Webhook signature verification failed.'], 403);
        }

        // Handle the event based on type
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                // Retrieve relevant information from the session object
                $orderId = $order->id; // Assuming you stored order ID as client reference ID
                $order = Order::findOrFail($orderId);

                // Update order status to 'payment made'
                $order->status = 'payment made';
                $order->save();

                // Additional actions if needed

                break;
            // Handle other event types as needed
            default:
                // Unexpected event type
                return response()->json(['error' => 'Unexpected webhook event received.'], 400);
        }

        // Return a response to acknowledge receipt of the event
        return response()->json(['status' => 'success']);
    }

}
