@include('admin.top')
<h2>ADmin Profile Page</h2>

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

<form action="{{ route('admin.profile.submit') }}" method="post" enctype="multipart/form-data">
    @csrf
    <table>
        <tr>
            <td>Name:</td>
            <td>
                <input type="text" name="name" value="{{ Auth::guard('admin')->user()->name }}">
            </td>
        </tr>
        <tr>
            <td>Existing Photo:</td>
            <td>
                @if (Auth::guard('admin')->user()->photo == null)
                    <p>No Photo Found</p>
                @else
                    <img src="{{ asset('uploads/' . Auth::guard('admin')->user()->photo) }}"
                        style="width: 100px; height: auto;" alt="">
                @endif
            </td>
        </tr>
        <tr>
            <td>Change Photo:</td>
            <td>
                <input type="file" name="photo" value="{{ Auth::guard('admin')->user()->photo }}">
            </td>
        </tr>
        <tr>
            <td>Emial:</td>
            <td>
                <input type="text" name="email" value="{{ Auth::guard('admin')->user()->email }}">
            </td>
        </tr>
        <tr>
            <td>Password:</td>
            <td>
                <input type="password" name="password">
            </td>
        </tr>
        <tr>
            <td>Confirm Password:</td>
            <td>
                <input type="password" name="confirm_password">
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <button type="submit">Update</button>
            </td>
        </tr>
    </table>
</form>
