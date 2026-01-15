@include('user.top')
<h2>Forget Password</h2>

@if ($errors->any())
    @foreach ($errors->all() as $error)
        {{ $error }}
    @endforeach
@endif

@if (session('success'))
    {{ session('success') }}
@endif

@if (session('error'))
    {{ session('error') }}
@endif

<form action="{{ route('forget.password.submit') }}" method="post">
    @csrf
    <table>
        <tr>
            <td>Emial:</td>
            <td>
                <input type="text" name="email" placeholder="Email">
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <button type="submit">Reset Password</button>
                <div>
                    <a href="{{ route('login') }}">Back to Login</a>
                </div>
            </td>
        </tr>
    </table>
</form>
