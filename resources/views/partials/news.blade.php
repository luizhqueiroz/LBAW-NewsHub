<div class="news-item {{ $news->author && $news->author->influencer ? 'influencer-post' : '' }}" 
    data-id="{{ $news->id }}" 
    data-movie-id="{{ $news->movie_id }}">
    @if(Auth::anyCheck())
        <!-- Tags Section -->
        <div class="news-header">
            <!-- Options Section -->
            @if((Auth::currentUser()->id === $news->author_id || Auth::guard('admin')->check()))
                <div class="options-menu ms-2" tabindex="0">
                    <div class="dots"></div>
                    <div class="dots"></div>
                    <div class="dots"></div>

                    <!-- Dropdown menu for Edit and Delete -->
                    <div class="options-dropdown">
                        <a href="#" class="dropdown-item edit-news-button">Edit</a>
                        <form action="{{ route('news.delete', $news->id) }}" method="POST" class="dropdown-item">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-delete-btn">Delete</button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Tags Section -->
            <div class="news-tags">
                @foreach($news->tags as $tag)
                    <span class="tag">{{ $tag->name }}</span>
                @endforeach
            </div>
        </div>

        <!-- Body Section -->
        <div class="news-body">
            <!-- User Info -->
            @if(Auth::anyCheck())
                <div class="user-info">
                    @if($news->author)
                        <a href="{{ route('user.show', $news->author->id) }}" class="link-no-underline">
                            <img src="{{ $news->author->image ? asset($news->author->image->image_path) : asset('images/avatars/default-avatar.png') }}" alt="User Avatar" class="user-avatar">
                        </a>
                        <a href="{{ route('user.show', $news->author->id) }}" class="link-no-underline d-flex align-items-center gap-1">
                            <span class="username">{{ '@' . $news->author->user_name }}</span>
                            @if ($news->author->influencer)
                                <span class="mt-1" style="color: gold; font-size: 0.9rem;">⭐</span>    
                            @endif
                        </a>
                    @else
                        <img src="{{ asset('images/avatars/default-avatar.png') }}" alt="User Avatar" class="user-avatar">
                        <span class="username">{{ '@' . 'Anonymous' }}</span>
                    @endif
                </div>
            @endif

            <!-- News Content -->
            <div class="news-content" data-id="{{ $news->id }}">
                @if(!$isNewsItem)
                    <a href="{{ route('news.show', $news->id) }}" class="link-no-underline">
                @endif
                <div class="news-content-container">
                    <p>{{ $news->content }}</p>
                </div>
                @if(!$isNewsItem)
                    </a>
                @endif
            </div>
        </div>

        <!-- Footer Section -->
        <div class="news-footer">
            <div class="left-group">
                @if(Auth::check())
                    <button class="btn btn-link p-0 text-decoration-none text-danger" onclick="toggleLike({{ $news->id }})">
                        <i id="heart-icon-{{ $news->id }}" class="fa-heart {{ $news->isLikedByUser(auth()->id()) ? "fa-solid" : "fa-regular"}}"></i> 
                        <span id="likes-count-{{ $news->id }}">{{ $news->likes_count }} Likes</span>
                    </button>
                @else
                    <button class="btn btn-link p-0 text-decoration-none text-danger pointer-events-none" style="cursor: default;">                    
                        <i id="heart-icon-{{ $news->id }}" class="fa-heart {{ $news->isLikedByUser(auth()->id()) ? "fa-solid" : "fa-regular"}}"></i> 
                        <span id="likes-count-{{ $news->id }}">{{ $news->likes_count }} Likes</span>
                    </button>
                @endif
                @if(!$isNewsItem)
                    <a href="{{ route('news.show', $news->id) }}" class="btn btn-link p-0 text-decoration-none">
                        <i class="fa fa-comments"></i>
                        <span> {{ $news->comments_count }} Comments</span>
                    </a>
                @else
                    <button class="btn btn-link p-0 text-decoration-none pointer-events-none" style="cursor: default;">
                        <i class="fa fa-comments"></i>
                        <span> {{ $news->comments_count }} Comments</span>
                    </button>
                @endif
            </div>
            <span class="date">{{ $news->published_date->format('g:i A • M j, Y') }}</span>
        </div>

    @else
        <!-- News Content -->
        <div class="news-content">
            @if(!$isNewsItem)
                    <a href="{{ route('news.show', $news->id) }}" class="link-no-underline">
                @endif
                {{ $news->content }}
            @if(!$isNewsItem)
                </a>
            @endif
        </div>

        <div class="news-footer-pub">
            <span>💬 {{ $news->comments_count }} Comments</span>
        </div>
        
    @endif
</div>

<!-- Movie Section -->
@if($news->movie_id && $isNewsItem && Auth::anyCheck())
    <div class="news-movie mt-3 mb-3" id="movie-details-{{ $news->id }}"></div>
@endif
