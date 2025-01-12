@extends('layouts.app')

@section('content')
<div id="content">
    <form method="POST" action="{{ route('register') }}" class="register-form">
        {{ csrf_field() }}

        <h2 class="form-title">Register</h2>

        <!-- Name Input -->
        <div class="form-group mb-2">
            <label for="name">Username</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="form-control">
            @if ($errors->has('name'))
                <span class="error">{{ $errors->first('name') }}</span>
            @endif
        </div>

        <!-- Email Input -->
        <div class="form-group mb-2">
            <label for="email">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-control">
            @if ($errors->has('email'))
                <span class="error">{{ $errors->first('email') }}</span>
            @endif
        </div>

        <!-- Password Input -->
        <div class="form-group mb-2">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required class="form-control">
            @if ($errors->has('password'))
                <span class="error">{{ $errors->first('password') }}</span>
            @endif
        </div>

        <!-- Password Confirmation -->
        <div class="form-group mb-2">
            <label for="password-confirm">Confirm Password</label>
            <input id="password-confirm" type="password" name="password_confirmation" required class="form-control">
        </div>

        <!-- Buttons -->
        <div class="form-buttons d-flex">
            <button type="submit" class="btn btn-primary mt-3 me-5">Register</button>
            <button class="btn btn-primary mt-3" onclick="window.location='{{ route('login') }}';">Login</button>
        </div>
    </form>
</div>
@endsection
