@extends('layouts.app')

@section('content')
    <div class="profile">

        <!-- Edit Profile Button (only if it's the current admin) -->
        @if(auth()->guard('admin')->id() == $admin->id)
            <a href="{{ route('admin.edit', $admin->id) }}" class="btn btn-primary profile-button">Edit Profile</a>
        @endif

        <!-- Profile Header with Image and Username -->
        <div class="profile-header">
            <img src="{{ $admin->image ? asset($admin->image->image_path) : asset('images/avatars/default-avatar.png') }}"
                 alt="{{ $admin->adm_name }}"
                 class="profile-picture">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h1 class="profile-name">{{ $admin->adm_name }}</h1> 
                </div>
                <p class="profile-username ms-1">
                    <strong class="admin-contact">Contact:</strong>
                    {{ $admin->email }}
                </p>
            </div>
        </div>
    </div>

    <!-- Separator -->
    <hr class="profile-custom-separator">
@endsection
