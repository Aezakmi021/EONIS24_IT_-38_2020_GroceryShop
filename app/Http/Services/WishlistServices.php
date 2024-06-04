<?php

namespace App\Http\Services;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistServices
{
    public function userResults() {
        $userId = auth()->id();

        $wishlists = Wishlist::with('product')->where('user_id', $userId)->get();

        $products = $wishlists->map(function ($wishlist) {
            return $wishlist->product;
        });

        return $products;
    }


}
