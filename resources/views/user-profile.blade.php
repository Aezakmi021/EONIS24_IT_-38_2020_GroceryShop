<x-layout>
    <div class="container py-md-5 container--narrow">
        <h2>
            {{ auth()->user()->username }}
        </h2>

        <p>Registered on: {{ auth()->user()->created_at->format('F j, Y') }}</p>

        <form action="{{ route('update-profile', ['user' => auth()->user()->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" disabled class="form-control" id="email" name="email" value="{{ auth()->user()->email }}">
            </div>

            <div class="form-group">
                <label for="password">New Password:</label>
                <input type="password" class="form-control" id="password" name="password">
                @error('password')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password:</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
            </div>

            <button type="submit" class="btn btn-light-magenta">Update</button>
        </form>

        <!-- Add a button to view user orders -->
        <a href="{{ route('my-orders') }}" class="btn btn-dark mt-3">View My Orders</a>
    </div>
</x-layout>
