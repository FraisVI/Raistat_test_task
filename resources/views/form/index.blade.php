@php($startTime = time())

@extends('layouts.main')
@section('content')
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
                <label for="price" class="form-label">Сумма</label>
                <input type="number" name="price" class="form-control" id="price">
            </div>
            <button type="submit" name="startTime" value="<?=$startTime?>" class="btn btn-primary">Отправить</button>
        </form>
    </div>
    <script src="{{ asset( 'js/bootstrap.min.js' ) }}"></script>
@endsection
