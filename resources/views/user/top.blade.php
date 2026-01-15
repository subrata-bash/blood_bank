<a href="{{ route('home') }}">Home</a> |
<a href="{{ route('about') }}">About</a> |
@if (Auth::guard('web')->check())
    <a href="{{ route('dashboard') }}">Dashboard</a> |
    <a href="{{ route('profile') }}">Profile</a> |
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
@else
    <a href="{{ route('login') }}">Login</a> |
    <a href="{{ route('register') }}">Register</a>
@endif
