@extends('layouts.app')

@section('content')
<div id="content">
    <form method="POST" action="/login" class="register-form">
        {{ csrf_field() }}

        <h2 class="form-title">Login</h2>

        <!-- Email Input -->
        <div class="form-group mb-2">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control">
        </div>

        <!-- Password Input -->
        <div class="form-group mb-2">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required class="form-control">
        </div>

        <!-- Error Messages -->
        @if ($errors->has('login'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                {{ $errors->first('login') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif ($errors->has('blocked'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                {{ $errors->first('blocked') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Buttons -->
        <div class="form-buttons d-flex">
            <button type="submit" class="btn btn-primary mt-3 me-5">Login</button>
            <button class="btn btn-primary mt-3" onclick="window.location='{{ route('register') }}';">Register</button>
        </div>
        <div>
            <button class="btn btn-primary mt-3" onclick="window.location='{{ route('recover') }}';">Recover Password</button>
        </div>
    </form>
</div>
@endsection
