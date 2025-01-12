@extends('layouts.app')

@section('content')
<div id="content">
    <form method="POST" action="{{ route('send.recover') }}" class="register-form">
        {{ csrf_field() }}

        <h2 class="form-title">Recover Password</h2>

        <!-- Email Input -->
        <div class="form-group mb-2">
            <label for="email">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control">
        </div>

        <!-- Error Message -->
        <div class="error text-danger mb-1">
            @if ($errors->has('email'))
                <span class="error">{{ $errors->first('email') }}</span>
            @endif
        </div>

        <!-- Information Message -->
        @if (session('status'))
            <div class="text-success mb-2">
                {{ session('status') }}
            </div>
        @endif

            <!-- error Message -->
            @if (session('error'))
            <div class="mb-2 text-danger">
                {{ session('error') }}
            </div>
            @endif

        <!-- Buttons -->
        <div class="form-buttons">
            <button type="submit" class="btn btn-primary mt-3">Send Password Reset Link</button>
        </div>
    </form>
</div>
@endsection
