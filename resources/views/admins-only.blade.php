<x-layout>
    <div class="container py-md-5 container--narrow">
        <div class="text-center">
            <a href="/categories" class="btn btn-primary text-white">Categories</a>
            @if(auth()->user()->isAdmin === 1)
                <a href="/orders" class="btn btn-primary text-white">Orders</a>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-bordered mt-4">
                <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Status</th>
                    <th>User</th>
                    <th>Items</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->status }}</td>
                        <td>{{ $order->user->username }}</td>
                        <td>
                            <ul>
                                @foreach (json_decode($order->items) as $item)
                                    <li>{{ $item->name }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
