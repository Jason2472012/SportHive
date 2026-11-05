@extends('layouts.auth', ['title' => 'Masuk'])

@section('content')
    <section class="card" aria-labelledby="page-title">
        <div class="brand"><span class="brand-mark">S</span>{{ config('app.name', 'Sports Booking') }}</div>
        <h1 id="page-title">Selamat datang</h1>
        <p class="intro">Masuk menggunakan email dan password akun Anda.</p>

        @if ($errors->any())
            <div class="alert" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="fields">
            @csrf
            <div>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                @error('email') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
                @error('password') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <button class="button" type="submit">Masuk</button>
        </form>

        <p class="foot">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
    </section>
@endsection
