<!DOCTYPE html>
<html>
<head>
    <title>Success</title>
    <!-- Add Bootstrap CDN -->
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-body">
                    <h1 class="card-title text-center">Success!</h1>
                    <p class="card-text text-center">{{ $success }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add JavaScript for delayed redirection -->
<script>
    setTimeout(function() {
        window.location.href = "{{ route('login') }}";
    }, 4000); // 4000 milliseconds = 4 seconds
</script>
</body>
</html>
