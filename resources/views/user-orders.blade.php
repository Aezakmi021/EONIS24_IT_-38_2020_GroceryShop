<x-layout>
    <div class="container d-flex flex-column justify-content-center py-md-5 container--narrow">
        @if( count($orders) == 0)
            <h1>You didn't make any orders.</h1>
        @endif
        @foreach ($orders as $order)
            @if ($order->user_id === auth()->user()->id)
                <div class="mb-4 shadow p-4">
                    <h2>Order ID: {{ $order->id }}</h2>
                    <p>Status: {{ $order->status }}</p>
                    <ul>
                        @foreach (json_decode($order->items) as $item)
                            <li>{{ $item->name }} | Quantity: {{$item->quantity}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </div>
</x-layout>

