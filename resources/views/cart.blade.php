<x-layout>
    <div class="container py-md-5 container--narrow">
        <div class="d-flex justify-content-center">
            <h2>Hello <strong>{{ auth()->user() ? auth()->user()->username : 'Guest' }}</strong>, Welcome to your Cart.</h2>
        </div>
        <div class="list-group">
            @php $totalPrice = 0 @endphp
            @forelse ($cartItems as $cartItem)
                <div class="list-group-item list-group-item-action">
                    <a href="/product/{{ $cartItem->id }}" class="list-group-item-action">{{ $cartItem->title }}</a>
                    <div class="body-content">
                        Price: {{ $cartItem->price * $cartItem->pivot->quantity }}
                        @php
                            $totalPrice += $cartItem->price * $cartItem->pivot->quantity;
                        @endphp
                        <form class="delete-post-form d-inline" action="/cart/delete/{{ $cartItem->id }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="delete-post-button text-danger" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fas fa-trash"></i></button>
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
        </div>
        <div class="total-price">
            Total Price: {{ $totalPrice }}
        </div>
        <div class="d-flex justify-content-center">
            <form action="/checkout" method="POST">
                @csrf
                <input type="hidden" name="total_price" id="total_price" value="{{ $totalPrice }}">
                <button type="submit" class="btn btn-primary">Checkout</button>
            </form>
        </div>
    </div>
</x-layout>
