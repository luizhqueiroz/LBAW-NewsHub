@extends('layouts.app')

@section('content')
    <div class="edit-profile">

        <h1>Edit Profile</h1>

        <form id="user-update-form" action="{{ route('admin.update', $admin->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Profile Picture -->
            <div class="form-group">
                <label for="image">Profile Picture</label>
                <input type="file" class="form-control" name="image" id="image" accept="image/*">
            </div>

            <!-- Username -->
            <div class="form-group">
                <label for="adm_name">Username</label>
                <input type="text" class="form-control" name="adm_name" id="adm_name" value="{{ old('adm_name', $admin->adm_name) }}" required>
                @error('adm_name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $admin->email) }}" required>
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
    
        <div class="d-flex justify-content-center gap-5">
            <!-- Submit Button -->
            <button form="user-update-form" type="submit" class="btn btn-primary py-2">Save Changes</button>
        </div>
    </div>
@endsection
