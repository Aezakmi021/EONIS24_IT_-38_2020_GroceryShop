<x-layout>
    <div class="container py-md-5 container--narrow">
        <div class="text-center">
            <a href="{{ route('categories') }}" class="btn btn-light-magenta text-white">Categories</a>
                <a href="{{route('orders.index') }}" class="btn btn-light-magenta  text-white">Orders</a>
        </div>
        @foreach ($users as $user)
                <div class="d-flex justify-content-between">
                    <h2><a  class="dm-mono-regular text-light-magenta">{{$user->username}}</a></h2>
                    <span class="pt-2">
                        <a href="{{ route('edit-user.edit', ['user' => $user->id])  }}" class="text-primary mr-2" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit text-dark"></i></a>
                        <form class="delete-post-form d-inline" action="{{route('delete-user', ['user' => $user->id])}}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="delete-post-button text-danger" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fas fa-trash text-light-magenta"></i></button>
                        </form>
                    </span>
                </div>
        @endforeach
    </div>
</x-layout>
