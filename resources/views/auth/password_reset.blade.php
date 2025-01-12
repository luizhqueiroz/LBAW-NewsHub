@extends('layouts.app')

@section('content')
<div id="content">
    <form method="POST" action="{{ route('password.reset') }}" class="register-form">
        {{ csrf_field() }}

        <h2 class="form-title">Reset Password</h2>

        <!-- Hidden Inputs -->
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- Password Input -->
        <div class="form-group mb-2">
            <label for="password">New Password</label>
            <input id="password" type="password" name="password" required autofocus class="form-control">
        </div>

        <!-- Confirm Password Input -->
        <div class="form-group mb-2">
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="form-control">
        </div>

        <!-- Error Display -->
        <div class="error text-danger mb-1">
            @if ($errors->has('password'))
                <span class="error">{{ $errors->first('password') }}</span>
            @endif
        </div>

        <!-- Buttons -->
        <div class="form-buttons d-flex">
            <button type="submit" class="btn btn-primary mt-3 me-5">Reset Password</button>
            <button class="btn btn-primary mt-3" onclick="window.location='{{ route('login') }}';">Back to Login</button>
        </div>
    </form>
</div>
@endsection
