<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css')}}">
</head>
<body>
    <div class="container-fluid d-flex flex-column justify-content-center align-items-center error-container">
        <img src="{{ asset('assets/img/errors/403.png') }}" alt="" width="300">
        <h1 class="">403</h1>
        <h2 class="text-uppercase">Forbidden</h2>
        <p class="error-content" style="font-size: 1.25rem">Bạn không có quyền truy cập trang này!</p>
        <button class="btn btn-secondary mt-3 text-white" onclick="window.history.back()">Quay lại</button>
    </div>
</body>
</html>
