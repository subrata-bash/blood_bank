<a href="{{ route('admin.dashboard') }}">Dashboard</a> |
<a href="{{ route('admin.profile') }}">Profile</a> |
<form method="POST" action="{{ route('admin.logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>
