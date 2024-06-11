<!DOCTYPE html>
<html>
<head>
    <title>Success</title>
    <!-- Add Bootstrap CDN -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <div class="row d-flex justify-content-center">
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
