<x-layout>
    <div class="container py-md-5 container--narrow">
        <div class="d-flex justify-content-center">
            <h2>Hello <strong class="text-magenta">{{ auth()->user() ? auth()->user()->username : 'Guest' }}</strong>, Welcome to your Cart.</h2>
        </div>
        <div class="list-group shadow">
            @php $totalPrice = 0 @endphp
            @forelse ($cartItems as $cartItem)
                <div class="list-group-item list-group-item-action">
                    <a href="/product/{{ $cartItem->id }}" class="list-group-item-action">{{ $cartItem->title }}</a>
                    <div class="body-content">
                        Price per item: <strong>{{ $cartItem->price }}</strong>
                        <br>
                        <form action="/cart/update/{{ $cartItem->id }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="quantity">Quantity:</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" min="1" max="{{ $cartItem->available_quantity }}" value="{{ $cartItem->pivot->quantity }}" required>
                            </div>
                            <button type="submit" class="btn btn-light-magenta">Update Quantity</button>
                        </form>
                        Total Price: <strong>{{ $cartItem->price * $cartItem->pivot->quantity }}</strong>
                        @php
                            $totalPrice += $cartItem->price * $cartItem->pivot->quantity;
                        @endphp
                        <form class="delete-post-form d-inline" action="/cart/delete/{{ $cartItem->id }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="delete-post-button text-light-magenta" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fas fa-trash"></i></button>
                        </form>
                        @if ($cartItem->images)
                            <br>
                            @foreach ($cartItem->images as $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product Image" style="width: 200px; height: 200px; object-fit: cover;">
                            @endforeach
                        @endif
                    </div>
                </div>
            @empty
                <p>No items in cart</p>
            @endforelse
            <div class="d-flex justify-content-center align-items-center list-group-item total-price">
                Total Price: <strong class="ml-1"> {{ $totalPrice }} </strong>
            </div>
        </div>

        <div class="d-flex justify-content-center m-3">
            <form action="/checkout" method="POST">
                @csrf
                <input type="hidden" name="total_price" id="total_price" value="{{ $totalPrice }}">
                <button type="submit" class="btn btn-magenta text-white">Checkout</button>
            </form>
        </div>
    </div>
</x-layout>
