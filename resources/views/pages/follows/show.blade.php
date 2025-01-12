@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="profile-name">
        <a href="{{ route('user.show', $user->id) }}" class="text-decoration-none text-reset">{{ $user->user_name }}</a>
    </h1>

    <p class="profile-username">
        <a href="{{ route('user.show', $user->id) }}" class="text-decoration-none text-reset">{{ '@' . $user->user_name }}</a>
    </p>
    
    <ul class="nav nav-tabs" id="followersFollowingTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="followers-tab" data-bs-toggle="tab" data-bs-target="#followers" 
                type="button" role="tab" aria-controls="followers" aria-selected="true">
                Followers
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="following-tab" data-bs-toggle="tab" data-bs-target="#following" 
                type="button" role="tab" aria-controls="following" aria-selected="false">
                Following
            </button>
        </li>
    </ul>

    <div class="tab-content mt-4" id="followersFollowingContent">
        <div class="tab-pane fade show active" id="followers" role="tabpanel" aria-labelledby="followers-tab">
            @forelse($followers as $follower)
                @include('partials.user', ['user' => $follower])
            @empty
                <p class="ms-3">No followers yet.</p>
            @endforelse

            <div class="mt-4">
                {{ $followers->links() }}
            </div>
        </div>

        <div class="tab-pane fade" id="following" role="tabpanel" aria-labelledby="following-tab">
            @forelse($followings as $following)
                @include('partials.user', ['user' => $following])
            @empty
                <p class="ms-3">No following yet.</p>
            @endforelse

            <div class="mt-4">
                {{ $followings->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
