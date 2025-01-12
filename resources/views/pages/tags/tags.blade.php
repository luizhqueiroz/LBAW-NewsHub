@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <!-- Success Message Section -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Request Tags Section -->
        @if($user->influencer)
            <div class="mb-4">
                <h2>Request a New Tag</h2>
                <form action="{{ route('tags.ask') }}" method="POST" class="form-group">
                    @csrf
                    <input type="text" name="tag_name" placeholder="Enter tag name..." class="form-control mb-2" required>
                    <button type="submit" class="btn btn-primary">Request Tag</button>
                </form>
            </div>
        @endif

        <!-- List of Tags Section -->
        <div class="mb-4">
            <h2>List of Tags</h2>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                @foreach($tags as $tag)
                    <div class="col">
                        <div class="card {{ $user->followedTags->contains($tag->id) ? 'bg-primary text-white' : 'bg-light' }} text-center">
                            <div class="card-body">
                                <a href="#"
                                   class="tag-link text-decoration-none {{ $user->followedTags->contains($tag->id) ? 'text-white' : 'text-primary' }}"
                                   data-id="{{ $tag->id }}">
                                    {{ $tag->name }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- News with Followed Tags Section -->
        <div>
            <h2>News with Followed Tags</h2>
            <div id="news-list">
                @foreach($news as $post)
                    @include('partials.news', ['news' => $post, 'isNewsItem' => false])
                @endforeach
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tags = document.querySelectorAll('.tag-link');

            tags.forEach(tag => {
                tag.addEventListener('click', function(event) {
                    event.preventDefault();
                    const tagId = event.target.dataset.id;
                    const card = event.target.closest('.card');

                    fetch(`{{ url('tags/toggle-follow') }}/${tagId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ tag_id: tagId })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (card.classList.contains('bg-primary')) {
                                    card.classList.remove('bg-primary', 'text-white');
                                    card.classList.add('bg-light');
                                    event.target.classList.remove('text-white');
                                    event.target.classList.add('text-primary');
                                } else {
                                    card.classList.remove('bg-light');
                                    card.classList.add('bg-primary', 'text-white');
                                    event.target.classList.remove('text-primary');
                                    event.target.classList.add('text-white');
                                }
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
@endsection
