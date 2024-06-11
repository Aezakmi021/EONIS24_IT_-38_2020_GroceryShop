<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Http\Services\WishlistServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    protected $wishlistServices;

    public function __construct(WishlistServices $wishlistService)
    {
        $this->wishlistServices = $wishlistService;
    }
    public function viewPage(Request $request)
    {
        $userId = auth()->user()->id;

        // Retrieve the user's cart with its associated products
        $cart = Cart::where('user_id', $userId)->with('products')->first();

        // If user's cart exists, get the products
        $cartItems = $cart ? $cart->products : [];

        // Update the session to include the cart items if the cart exists
        if ($cart) {
            $cartData = [];
            foreach ($cartItems as $item) {
                $cartData[] = [
                    'product_id' => $item->id,
                    'name' => $item->title,
                    'price' => $item->price,
                    'quantity' => $item->pivot->quantity,
                ];
            }
            $request->session()->put('cart', $cartData);
        }

        $products = $this->wishlistServices->userResults();

        return view('cart', ['cartItems' => $cartItems, 'wishlistItems' => $products ]);
    }




    public function addProduct(Request $request, $productId)
    {
        // Step 1: Check if the user is authenticated
        if (Auth::check()) {
            // Step 2: Retrieve the user ID
            $userId = Auth::id();

            // Fetch product details
            $product = Product::findOrFail($productId);

            // Check if the product status is 'Unavailable'
            if ($product->status === 'Unavailable') {
                return redirect()->back()->with('error', 'The product is currently unavailable.')->setStatusCode(422);
            }

            // Retrieve the user's cart or create a new one if it doesn't exist
            $userCart = Cart::firstOrCreate(['user_id' => $userId]);

            // Get the requested quantity
            $requestedQuantity = $request->input('quantity', 1); // Default quantity is 1

            // Check if the same product is already in the cart
            $existingCartItem = $userCart->products()->where('product_id', $productId)->first();

            if ($existingCartItem) {
                // If the product is already in the cart, update the quantity
                $existingQuantityInCart = $existingCartItem->pivot->quantity;
                $newQuantity = $existingQuantityInCart + $requestedQuantity;
                $availableQuantity = $product->available_quantity - $existingQuantityInCart;
                $validatedQuantity = min($newQuantity, $availableQuantity);

                if ($requestedQuantity > $availableQuantity) {
                    if ($availableQuantity === 0) {
                        return redirect()->back()->with('error', 'Cannot add more products, you already reached the limit.')->setStatusCode(422);
                    } else {
                        return redirect()->back()->with('error', 'Cannot add more than ' . $availableQuantity . ' products.')->setStatusCode(422);
                    }
                }

                // Update the quantity of the existing product in the cart
                $userCart->products()->updateExistingPivot($productId, ['quantity' => $validatedQuantity]);
            } else {
                // Otherwise, attach the product to the user's cart with the specified quantity
                $userCart->products()->attach($productId, ['quantity' => $requestedQuantity]);
            }

            // Update the session to include the newly added item with product details
            $cart = $request->session()->get('cart', []);
            $existingCartItemIndex = array_search($productId, array_column($cart, 'product_id'));
            if ($existingCartItemIndex !== false) {
                // If the product already exists in the session, update its quantity
                $cart[$existingCartItemIndex]['quantity'] += $requestedQuantity;
            } else {
                // Otherwise, add the product to the session
                $cart[] = [
                    'product_id' => $productId,
                    'name' => $product->title,
                    'price' => $product->price,
                    'quantity' => $requestedQuantity
                ];
            }
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
    public function updateQuantity(Request $request, $productId)
    {
        // Retrieve the user's cart
        $userId = auth()->user()->id;
        $userCart = Cart::where('user_id', $userId)->firstOrFail();

        // Fetch the product
        $product = Product::findOrFail($productId);

        // Calculate the available quantity for the product
        $existingQuantityInCart = $userCart->products()->where('product_id', $productId)->sum('quantity');
        $availableQuantity = $product->available_quantity;

        // Validate the requested quantity
        $request->validate([
            'quantity' => [
                'required',
                'numeric',
                'min:1',
                'max:' . $availableQuantity, // Limit the quantity to the available quantity
            ],
        ]);

        // Get the requested quantity
        $requestedQuantity = $request->input('quantity');

        // Update the quantity of the specified product in the cart
        $userCart->products()->updateExistingPivot($productId, ['quantity' => $requestedQuantity]);

        // Update the session to match the updated quantity
        $cart = $request->session()->get('cart', []);
        foreach ($cart as &$item) {
            if ($item['product_id'] == $productId) {
                $item['quantity'] = $requestedQuantity;
                break; // Stop the loop once the item is found and updated
            }
        }
        $request->session()->put('cart', $cart);

        // Redirect back to the cart page with a success message
        return redirect()->back()->with('success', 'Quantity updated successfully.');
    }



}
