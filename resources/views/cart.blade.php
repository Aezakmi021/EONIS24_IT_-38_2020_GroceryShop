<x-layout>
    <div class="container py-md-5 container--narrow">
        <div class="d-flex justify-content-center">
            <h2>Hello <strong class="text-magenta">{{ auth()->user() ? auth()->user()->username : 'Guest' }}</strong>, Welcome to your Cart.</h2>
        </div>
        <div class="d-flex justify-content-center flex-column ">
            <p>
                <button class="btn btn-light-magenta" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    Wishlist
                </button>
            </p>
            <div class="collapse" id="collapseExample">
                @foreach($wishlistItems as $item)
                    <a href="/product/{{$item->id}}" class="card card-body text-dark">
                        <p> Product : <strong>{{$item->title}}</strong> </p>
                        <p>Price : <strong>{{$item->price}}</strong> </p>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="list-group shadow my-2">
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
                <p class="list-group-item">No items in cart</p>
            @endforelse
            <div class="d-flex justify-content-center align-items-center list-group-item total-price">
                Total Price: <strong class="ml-1"> {{ $totalPrice }} </strong>
            </div>
        </div>

        <div class="d-flex my-3 ">
            <form action="/checkout" method="POST">
                @csrf
                <input type="hidden" name="total_price" id="total_price" value="{{ $totalPrice }}">
                <button type="submit" class="btn btn-light-magenta text-white">Checkout</button>
            </form>
        </div>

    </div>

</x-layout>
