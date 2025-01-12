@extends('layouts.app')

@section('content')
    <div class="edit-profile">

        <h1>Edit Profile</h1>

        <form id="user-update-form" action="{{ route('user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Profile Picture -->
            <div class="form-group">
                <label for="image">Profile Picture</label>
                <input type="file" class="form-control" name="image" id="image" accept="image/*">
            </div>

            <!-- Username -->
            <div class="form-group">
                <label for="user_name">Username</label>
                <input type="text" class="form-control" name="user_name" id="user_name" value="{{ old('user_name', $user->user_name) }}" required>
                @error('user_name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" class="form-control" name="password" id="password">
                @error('password')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
            </div>
        </form>

        <form class="d-none" id="user-delete-form" action="{{ route('user.delete', $user->id) }}" method="POST">
            @csrf
            @method('DELETE')
        </form>
    
        <div class="d-flex justify-content-center gap-5">
            <!-- Submit Button -->
            <button form="user-update-form" type="submit" class="btn btn-primary me-5 py-2">Save Changes</button>
            <!-- Delete Button -->
            <button form="user-delete-form" type="submit" class="btn btn-danger py-2">Delete Account</button>
        </div>
    </div>
@endsection
