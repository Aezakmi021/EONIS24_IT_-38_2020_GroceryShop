@php
    use App\Models\Notification;
    use App\Models\User;
@endphp

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Grocery Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
          crossorigin="anonymous"/>
    <script defer src="https://use.fontawesome.com/releases/v5.5.0/js/all.js"
            integrity="sha384-GqVMZRt5Gn7tB9D9q7ONtcp4gtHIUEW/yG7h98J7IpE3kpi+srfFyyB/04OV6pG0"
            crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,400;0,700;1,400;1,700&display=swap"
          rel="stylesheet"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:ital,wght@0,300;0,400;0,500;1,300;1,400;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/main.css"/>
</head>
<body>
<header class="header-bar mb-3">
    <div class="container d-flex flex-column flex-md-row align-items-center p-3">
        <h4 class="my-0 mr-md-auto  font-weight-normal"><a href="/" class="text-light-magenta dm-mono-medium">Grocery Store</a></h4>

        @auth
            <div class="flex-row  my-3 my-md-0 d-flex align-items-center dm-mono-regular">
                <a href="/profile" class="mr-2 text-white ">{{ auth()->user()->username }}</a>

                <form action="/logout" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-dark btn-light">Sign Out</button>
                </form>
                @if(auth()->user()->isAdmin === 1)
                    <!-- Admin Notification Button -->
                    <div class="dropdown ml-auto">
                        <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="adminNotificationDropdown"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Notifications
                        </button>
                        <div class="dropdown-menu" aria-labelledby="adminNotificationDropdown">
                            <!-- Fetch all notifications from the database -->
                            @php
                                $notifications = Notification::all();
                            @endphp

                                <!-- Check if notifications are available -->
                            @if($notifications->isNotEmpty())
                                <!-- Loop through notifications and display each one -->
                                @foreach($notifications as $notification)
                                    @php
                                        // Retrieve the user who placed the order
                                        $user = User::find($notification->user_id);
                                    @endphp
                                        <!-- Check if the notification has been read -->
                                    @if($notification->read_at)
                                        <!-- Skip displaying this notification if it has been read -->
                                        @continue
                                    @endif
                                    <!-- Check if user exists -->
                                    @if($user)
                                        <!-- Display the notification -->
                                        <a class="dropdown-item" href="{{ route('notifications.markAsRead', $notification->id) }}">
                                            {{ $user->username }} just placed a new order!
                                        </a>
                                    @endif
                                @endforeach
                            @else
                                <a class="dropdown-item" href="#">No notifications</a>
                            @endif

                        </div>
                    </div>
                @endif
            </div>
        @else
            <form class="dm-mono-regular" action="/login" method="POST" class="mb-0 pt-2 pt-md-0 ">
                @csrf
                <div class="row align-items-center">
                    <div class="col-md mr-0 pr-md-0 mb-3 mb-md-0">
                        <input name="loginusername" class="form-control form-control-sm input-dark" type="text"
                               placeholder="Username" autocomplete="off"/>
                        @error('loginusername')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md mr-0 pr-md-0 mb-3 mb-md-0">
                        <input name="loginpassword" class="form-control form-control-sm input-dark" type="password"
                               placeholder="Password"/>
                        @error('loginpassword')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-auto">
                        <button class="btn btn-light btn-sm">Sign In</button>
                    </div>
                    <div class="col-md-auto">
                        <a class="btn btn-light btn-sm" href='/register'>Sign Up</a>
                    </div>
                </div>
            </form>
        @endauth
        @if(auth()->user())
            <div class="d-flex justify-content-center flex-row my-3 py-3 my-md-0 m-3 dm-mono-regular">
                <a href="/cart" class="btn btn-sm btn-light"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart mr-1" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>Cart</a>
            </div>
        @endif
        @if(isset(auth()->user()->isAdmin))
            <!-- Admin Notification Button -->
            <div class="dropdown dm-mono-regular">
                <button class="btn btn-sm btn-light-magenta dropdown-toggle" type="button" id="adminNotificationDropdown"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6"/>
                    </svg> Notifications
                </button>
                <div class="dropdown-menu" aria-labelledby="adminNotificationDropdown">
                    <!-- Check if notifications are available -->
                    @if(isset($notifications) && $notifications->isNotEmpty())
                        <!-- Check if notifications are available -->
                        <!-- Loop through notifications and display each one -->
                        @foreach($notifications as $notification)
                            @php
                                // Retrieve the user who placed the order
                                $user = User::find($notification->user_id);
                            @endphp
                                <!-- Check if user exists -->
                            @if($user)
                                <!-- Display the notification -->
                                <a class="dropdown-item" href="{{ route('notifications.markAsRead', $notification->id) }}">
                                    {{ $user->name }} just placed a new order!
                                </a>
                            @endif
                        @endforeach
                    @else
                        <a class="dropdown-item" href="#">No notifications</a>
                    @endif

                </div>
            </div>
        @endif
    </div>
</header>
<!-- header ends here -->

<!-- Display success, failure, and error messages -->
@if(session()->has('success'))
    <div class='container container--narrow'>
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    </div>
@endif

@if(session()->has('failure'))
    <div class='container container--narrow'>
        <div class="alert alert-danger text-center">
            {{ session('failure') }}
        </div>
    </div>
@endif

@if(session()->has('error'))
    <div class='container container--narrow'>
        <div class="alert alert-danger text-center">
            {{ session('error') }}
        </div>
    </div>
@endif

@include('sidebar')

{{ $slot }}

<!-- footer begins -->
<footer class="border-top text-center small text-muted py-3 dm-mono-regular">
    <p class="m-0">Copyright &copy; {{ date('Y') }}<a href="/" class="text-muted">Grocery Store</a>. All rights
        reserved.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>
<script>
    $('[data-toggle="tooltip"]').tooltip()
</script>
</body>
</html>
