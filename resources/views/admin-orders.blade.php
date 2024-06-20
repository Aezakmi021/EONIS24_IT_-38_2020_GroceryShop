<x-layout>
    <div class="container py-md-5 container--narrow">
        <div class="text-center">
            <a href="{{ route('admin-dashboard') }}" class="btn btn-light-magenta text-white">Users</a>
            <a href="{{ route('categories') }}" class="btn btn-light-magenta text-white">Categories</a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @else
            @foreach ($orders as $order)
                <div class="mb-4">
                    <h2>Order ID: {{ $order->id }}</h2>
                    <p>Status: {{ $order->status }}</p>
                    <p>User: {{ $order->user->username }}</p>
                    <p>Shipping address: {{ $order->shipping_address }} </p>
                    <p>City: {{ $order->city }}</p>
                    <p>Country: {{ $order->country }}</p>
                    <p>Zip Code: {{ $order->zip_code }}</p>
                    <ul>
                        @foreach (json_decode($order->items) as $item)
                            <li>{{ $item->name }} | Quantity: {{ $item->quantity }}</li>
                        @endforeach
                    </ul>

                    @if(auth()->user()->isAdmin === 1)
                        <form action="{{ route('orders.update.status', ['order' => $order->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="status">Update Status:</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option> <!-- Add this option for 'paid' status -->
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </form>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</x-layout>
