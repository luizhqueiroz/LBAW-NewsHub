<div class="comment-wrapper d-flex flex-col justify-content-center">
    <div class="post col-11 mb-4 {{ $comment->author && $comment->author->influencer ? 'influencer-post' : '' }}">
        <!-- User Details -->
        @if(Auth::anyCheck())
            <div class="d-flex justify-content-between mb-2 me-3">
                @if($comment->author)
                    <div class="d-flex align-items-center">
                        <a href="{{ route('user.show', $comment->author->id) }}" class="link-no-underline">
                            <img src="{{ $comment->author->image ? asset($comment->author->image->image_path) : asset('images/avatars/default-avatar.png') }}" alt="User Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; display: block;">
                        </a>
                        <a href="{{ route('user.show', $comment->author->id) }}" class="link-no-underline d-flex align-items-center gap-1">
                            <span class="user-comment-handle">{{ '@' . $comment->author->user_name }}</span>
                            @if ($comment->author->influencer)
                                <span style="color: gold; font-size: 0.9rem;">⭐</span>      
                            @endif
                        </a>
                    </div>
                @else
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('images/avatars/default-avatar.png') }}" alt="User Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; display: block;">
                        <span class="username">{{ '@' . 'Anonymous' }}</span>
                    </div>
                @endif
                
                @if(Auth::anyCheck() && (Auth::currentUser()->id === $comment->author_id || Auth::guard('admin')->check()))
                    <div class="dropdown d-flex align-content-start flex-wrap">
                        <button class="btn-dropdown" type="button" id="dropdownCommentButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="options-menu">
                                <div class="dots"></div>
                                <div class="dots"></div>
                                <div class="dots"></div>
                            </div>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end mt-2">
                            <li><button type="button" class="dropdown-item" onclick="enableEditMode({{ $comment->id }})">Edit</button></li>
                            <li><button type="button" class="dropdown-delete-btn rounded-bottom" onclick="deleteComment({{ $comment->id }})">Delete</button></li>   
                        </ul>
                    </div>
                @endif
            </div>
        @endif
    
        <!-- Comment Content -->
        <div class="ms-5" id="comment-container-{{ $comment->id }}">
            <p id="comment-content-{{ $comment->id }}">{{ $comment->content }}</p>
        </div>
    
        <!-- Footer -->
        @if(Auth::anyCheck())
            <div class="post-footer d-flex justify-content-between">
                @if(Auth::check())
                    <button class="btn btn-link p-0 text-decoration-none text-danger" onclick="toggleCommentLike({{ $comment->id }})">
                        <i id="comment-heart-icon-{{ $comment->id }}" class="fa-heart {{ $comment->isLikedByUser(auth()->id()) ? "fa-solid" : "fa-regular"}}"></i> 
                        <span id="comment-likes-count-{{ $comment->id }}">{{ $comment->likes_count }} Likes</span>
                    </button>
                @else
                    <button class="btn btn-link p-0 text-decoration-none text-danger pointer-events-none" style="cursor: default;">
                        <i id="comment-heart-icon-{{ $comment->id }}" class="fa-heart {{ $comment->isLikedByUser(auth()->id()) ? "fa-solid" : "fa-regular"}}"></i> 
                        <span id="comment-likes-count-{{ $comment->id }}">{{ $comment->likes_count }} Likes</span>
                    </button>
                @endif
                <span>{{ $comment->published_date->format('g:i A • M j, Y') }}</span>
            </div>
        @endif
    </div>
</div>
