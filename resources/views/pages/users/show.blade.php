@extends('layouts.app')

@section('content')
    <div class="profile {{ $user->influencer ? 'influencer-post' : '' }}">

        <!-- Edit Profile Button (only if it's the current user) -->
        @if(auth()->id() == $user->id || auth()->guard('admin')->check())
            <a href="{{ route('user.edit', $user->id) }}" class="btn btn-primary profile-button">Edit Profile</a>
        @else
            @if(!$user->isBeingFollowed)
                <button data-url="{{ route('users.follow', $user->id) }}" class="btn btn-primary follow-btn profile-button">Follow</button>
            @else
                <button data-url="{{ route('users.unfollow', $user->id) }}" class="btn btn-danger unfollow-btn profile-button">Unfollow</button>
            @endif
        @endif

        <!-- Profile Header with Image and Username -->
        <div class="profile-header">
            <img src="{{ $user->image ? asset($user->image->image_path) : asset('images/avatars/default-avatar.png') }}"
                 alt="{{ $user->user_name }}"
                 class="profile-picture">

            <div>
                <div class="d-flex align-items-center gap-2">
                    <h1 class="profile-name">{{ $user->user_name }}</h1> 
                    @if ($user->influencer)
                        <span class="mb-2" style="color: gold; font-size: 2.5rem;">⭐</span>                    
                    @endif
                </div>
                <p class="profile-username">{{ '@' . $user->user_name }}</p>
            </div>
        </div>

        <!-- Profile Status Section -->
        <div class="profile-stats">
            <div class="reputation">
                <span>{{ $user->reputation }}</span>
                <p>Reputation</p>
            </div>
            <div class="following">
                <span>
                    <a href="{{ route('users.follows', $user->id) }}" class="text-decoration-none text-reset"> {{ $user->followings_count }} </a>
                    </span>
                <p>
                    <a href="{{ route('users.follows', $user->id) }}" class="text-decoration-none text-reset">Following</a>
                </p>
            </div>
            <div class="followers">
                <span>
                    <a href="{{ route('users.follows', $user->id) }}" class="text-decoration-none text-reset">{{ $user->followers_count }} </a>
                    </span>
                <p>
                    <a href="{{ route('users.follows', $user->id) }}" class="text-decoration-none text-reset">Followers</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Separator -->
    <hr class="profile-custom-separator">

    <!-- News Feed Section -->
    <div class="user-news-feed">
        <h2>News Feed</h2>

        <!-- Display all posts from the user -->
        @foreach($user->news as $post)
            @include('partials.news', ['news' => $post, 'isNewsItem' => false])
        @endforeach
    </div>
@endsection
