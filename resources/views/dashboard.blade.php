@extends('layouts.app')

@section('content')
<div>
    <h2>Dashboard</h2>
    <p>Welcome to the dashboard!</p>
    <a href="{{ route('folders') }}">Manage Folders</a>
</div>
@endsection
