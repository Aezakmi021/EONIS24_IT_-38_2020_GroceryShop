<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function viewPage()
    {
        $userId = auth()->user()->id;

        // Retrieve the user's cart with its associated products
        $cart = Cart::where('user_id', $userId)->with('products')->first();

        // If user's cart exists, get the products
        $cartItems = $cart ? $cart->products : [];

        return view('cart', ['cartItems' => $cartItems]);
    }



    public function addProduct(Request $request, $productId)
    {
        // Step 1: Check if the user is authenticated
        if (Auth::check()) {
            // Step 2: Retrieve the user ID
            $userId = Auth::id();

            // Retrieve the user's cart or create a new one if it doesn't exist
            $userCart = Cart::firstOrCreate(['user_id' => $userId]);

            // Attach the product to the user's cart
            $quantity = $request->input('quantity', 1); // Default quantity is 1
            $userCart->products()->attach($productId, ['quantity' => $quantity]);

            // Fetch product details
            $product = Product::findOrFail($productId);

            // Update the session to include the newly added item with product details
            $cart = $request->session()->get('cart', []);
            $cart[] = [
                'product_id' => $productId,
                'name' => $product->title,
                'price' => $product->price,
                'quantity' => $quantity
            ];
            $request->session()->put('cart', $cart);

            return redirect()->back()->with('success', 'Product added to cart.');
        } else {
            // Handle the case where the user is not authenticated
            return redirect()->route('login')->with('error', 'Please log in to add products to your cart.')->setStatusCode(401);
        }
    }


    public function deleteProduct(Request $request, $productId)
    {
        $userId = auth()->user()->id;

        // Detach the product from the user's cart
        $userCart = Cart::where('user_id', $userId)->firstOrFail();
        $userCart->products()->detach($productId);

        // Remove the item from the session
        $cart = $request->session()->get('cart', []);
        $updatedCart = array_filter($cart, function ($item) use ($productId) {
            return $item['product_id'] != $productId;
        });
        $request->session()->put('cart', $updatedCart);

        return redirect()->back()->with('success', 'Product removed from cart.')->setStatusCode(200);
    }

}
