@php($sessionStartTime = time())
@php($massage = 2)

@extends('layouts.main')
@section('content')
    <div class="container">
        <form action="{{ route('form.send') }}" method="post">
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
                <label for="phoneNumber" class="form-label">Телефон</label>
                <input type="number" name="phoneNumber" class="form-control" id="phoneNumber">
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Сумма</label>
                <input type="number" name="price" class="form-control" id="price">
            </div>
            <button type="submit" name="sessionStartTime" value="<?=$sessionStartTime?>" class="btn btn-primary">Отправить</button>
        </form>
    </div>
    @if($massage == 1)
        <script>
            alert('Сделка отправлена')
        </script>
    @elseif($massage == 0)
        <script>
            alert('Сделка не отправлена')
        </script>
    @endif
    <script src="{{ asset( 'js/bootstrap.min.js' ) }}"></script>
@endsection
