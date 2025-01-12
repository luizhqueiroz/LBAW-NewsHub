<div class="user-card">
    <div class="user-item-info">
        <a href="{{ route('user.show', $user->id) }}" class="link-no-underline">
            <img src="{{ $user->image ? asset($user->image->image_path) : asset('images/avatars/default-avatar.png') }}" alt="User Avatar" class="user-avatar">
        </a>
        <div class="user-name-item">
            <a href="{{ route('user.show', $user->id) }}" class="link-no-underline">
                <p class="username-item">{{ $user->user_name }}</p>
                <p class="user-item-handle">{{ '@' .  $user->user_name }}</p>
            </a>
        </div>
    </div>
    <div class="follow-action">
        @if(Auth::check())
            @if(auth()->id() == $user->id)
                {{-- Do nothing --}}
            @elseif(!auth()->user()->followings()->where('followed_id', $user->id)->exists())
                <button data-url="{{ route('users.follow', $user->id) }}" class="btn btn-primary follow-btn">Follow</button>
            @else
                <button data-url="{{ route('users.unfollow', $user->id) }}" class="btn btn-danger unfollow-btn">Unfollow</button>
            @endif
        @endif
    </div>
</div>