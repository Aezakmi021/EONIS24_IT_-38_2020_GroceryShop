<x-layout>
    <div class="d-flex justify-content-center flex-row my-3 my-md-0">
        <form action="/search" method="GET" class="form-inline">
            <div class="form-group">
                <input type="text" class="form-control form-control-sm" name="term" placeholder="Search" />
            </div>
            <button type="submit" class="btn btn-primary btn-sm ml-2">Search</button>
        </form>
    </div>

    <div class="container py-md-5 container--narrow ">
        <div class="d-flex justify-content-center">
            <h2>Hello <strong>{{ auth()->user()  ? auth()->user()->username : 'Guest' }}</strong>, Welcome to Grocery Store.</h2>
        </div>
        <div class="list-group">
            @foreach ($products as $product)
                <a href="/product/{{$product->id}}" class="list-group-item list-group-item-action">
                    <div class="d-flex align-items-center">
                        @if($product->images->first()?->image_path)
                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->title }}" style="width: 100px; height: 100px;" class="mr-3">
                        @else
                            <div class="mr-3" style="width: 100px; height: 100px; background-color: #eee;"></div>
                        @endif
                        <span>{{ $product->title }}</span>
                    </div>
                </a>
            @endforeach
        </div>
        {{ $products->links() }}
    </div>
</x-layout>
