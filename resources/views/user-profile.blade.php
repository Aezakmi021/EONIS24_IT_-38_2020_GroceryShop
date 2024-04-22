<x-layout>
    <div class="container py-md-5 container--narrow">
        <h2>
            {{$username}}
        </h2>

        <!-- Display user registration date -->
        <p>Registered on: {{ auth()->user()->created_at->format('F j, Y') }}</p>

        <!-- Check for success message and display it -->
        <!-- No need to handle success message here, as it's handled in layout.blade.php -->

        <!-- Display validation errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form for email and password editing -->
        <form action="/update-profile" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email }}">
                @error('email')
                <span class="text-danger">{{ $message }}</span>
                @enderror
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

            <button type="submit" class="btn btn-primary">Update</button>
        </form>

        <!-- Add a button to view user orders -->
        <a href="{{ route('my-orders') }}" class="btn btn-success mt-3">View My Orders</a>
    </div>
</x-layout>
