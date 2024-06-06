<!doctype html>
<html lang="en">

<head>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div class="container">
    <form action="{{ route('form.store') }}" method="post">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Имя</label>
            <input type="text" name="name" class="form-control" id="name">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp">
        </div>
        <div class="mb-3">
            <label for="phone_number" class="form-label">Телефон</label>
            <input type="number" name="phone_number" class="form-control" id="phone_number">
        </div>
        <div class="mb-3">
            <label for="sum" class="form-label">Сумма</label>
            <input type="number" name="sum" class="form-control" id="sum">
        </div>
        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>
</div>
<script src="{{ asset( 'js/bootstrap.min.js' ) }}"></script>
</body>
</html>
