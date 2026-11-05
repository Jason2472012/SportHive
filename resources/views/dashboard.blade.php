@extends('layouts.auth', ['title' => 'Dashboard'])

@section('content')
    <section class="card dashboard" aria-labelledby="page-title">
        <div class="dashboard-head">
            <div>
                <div class="brand"><span class="brand-mark">S</span>{{ config('app.name', 'Sports Booking') }}</div>
                <h1 id="page-title">Halo, {{ auth()->user()->name }}</h1>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout" type="submit">Keluar</button>
            </form>
        </div>
        <p>Anda berhasil masuk ke akun.</p>
        <span class="role">{{ str_replace('_', ' ', strtolower(auth()->user()->role)) }}</span>
    </section>
@endsection
