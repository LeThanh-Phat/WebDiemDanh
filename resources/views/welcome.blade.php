<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ</title>
</head>

<body>
    <h2>Chào mừng, {{ $name }}!</h2>
    <p>Chúc bạn một ngày tốt lành.</p>

    <a href="{{ route('dangxuat') }}">Đăng Xuất</a>
</body>

</html>