<x-layout>
    <div class="container py-md-5 container--narrow">
        <div class="d-flex justify-content-between">
            <h2>{{$product->title}}</h2>
            @can('update', $product)
                <span class="pt-2">
                    <a href="/product/{{$product->id}}/edit" class="text-dark mr-2" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>
                    <form class="delete-post-form d-inline" action="/product/{{$product->id}}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="delete-post-button text-light-magenta" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fas fa-trash"></i></button>
                    </form>
                </span>
            @endcan
        </div>

        <p class="text-muted small mb-4">
            Posted by <a class="text-magenta" href="#">{{$product->user->username}}</a> on {{$product->created_at->format('n/j/Y')}}
        </p>

        <div class="body-content">
            {{$product->body}}
        </div>
        <hr>
        @foreach ($product->images as $image)
            <div style="text-align: center;">
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product Image" style="width: 500px; height: 500px;">
            </div>
        @endforeach
        <hr>
        <div class="body-content d-flex flex-column">
            <div>Price: <strong> {{$product->price}} </strong> </div>
            <div> Status: <strong> {{$product->status}} </strong> </div>
            <div>  Available Quantity: <strong> {{$product->available_quantity}} </strong> </div>
        </div>
        <hr>
        <div class="body-content">
            Category: <strong> {{ $product->category ? $product->category->categoryName : 'N/A' }} </strong>
        </div>
        @if(auth()->user())
            <div class="d-flex flex-gap flex-column ">
                <form class="d-flex flex-column flex-gap" action="/cart/add/{{$product->id}}" method="POST">
                    @csrf
                    <div class="">
                        <label for="quantity">Quantity:</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" min="1" max="{{$product->available_quantity}}" value="1" required>
                    </div>
                    <button class="btn btn-light-magenta">Add to Cart</button>
                    @if(session('error'))
                        <span class="text-danger ml-2">{{ session('error') }}</span>
                    @endif
                </form>
                <form action="/wishlist/add/{{$product->id}}" method="POST">
                    @csrf
                    <button class="btn btn-light-magenta">Add to Wishlist</button>
                </form>
            </div>
        @endif
        <div class="mt-5">
            <h3>Comments</h3>

            <ul class="list-group">
                @foreach ($product->comments as $comment)
                    <li class="list-group-item">
                        <strong>{{$comment->user->username}}:</strong> {{$comment->content}}
                    </li>
                @endforeach
            </ul>

            @auth
                <form class="mt-3" action="/product/{{$product->id}}/comments" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="comment">Add a Comment:</label>
                        <textarea class="form-control" id="comment" name="content" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-light-magenta">Submit Comment</button>
                </form>
            @else
                <p class="mt-3">Please <strong>login</strong> to leave a comment.</p>
            @endauth
        </div>
    </div>
</x-layout>
