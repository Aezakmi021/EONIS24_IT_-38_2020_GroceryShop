<x-layout>
    <div class="container py-md-5 container--narrow">
        @foreach ($orders as $order)
            @if ($order->user_id === auth()->user()->id)
                <div class="mb-4">
                    <h2>Order ID: {{ $order->id }}</h2>
                    <p>Status: {{ $order->status }}</p>
                    <ul>
                        @foreach (json_decode($order->items) as $item)
                            <li>{{ $item->name }} | Quantitiy:{{$item->quantity}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </div>
</x-layout>
