@include('user.top')
<h2>Dashboard</h2>
<p>
    Welcome {{ Auth::guard('web')->user()->name }} to your Dashboard.
</p>
