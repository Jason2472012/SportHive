@extends('layouts.auth', ['title' => 'Daftar'])

@section('content')
    <section class="card" aria-labelledby="page-title">
        <div class="brand"><span class="brand-mark">S</span>{{ config('app.name', 'Sports Booking') }}</div>
        <h1>Buat akun</h1>
        <p class="intro">Daftar untuk mulai menggunakan layanan. Akun baru dibuat sebagai pengguna biasa.</p>

        @if ($errors->any())
            <div class="alert" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="fields">
            @csrf
            <div>
                <label for="name">Nama</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" maxlength="100" required autofocus>
                @error('name') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="150" required>
                @error('email') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
                @error('password') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation">Ulangi password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
            </div>
            <button class="button" type="submit">Daftar</button>
        </form>

        <p class="foot">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
    </section>
@endsection
