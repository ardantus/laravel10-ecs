@extends('layouts.app')

@section('content')
<div>
    <h2>Login</h2>
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <input type="text" name="username" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>
@endsection
