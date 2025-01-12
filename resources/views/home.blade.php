@extends('layouts.app')

@section('content')
    <!-- Search Bar -->
    @include('partials.search_bar')

    <!-- Feed -->
    <div class="feed" id="news-feed">

        <!-- Feed Controls -->
        <div class="btn-group feed-controls w-100" role="group">
            <input type="radio" class="btn-check" name="topnewsbtn" id="top-news-button" autocomplete="off" checked>
            <label class="btn btn-outline-primary w-50" for="top-news-button">Top News</label>
        
            <input type="radio" class="btn-check" name="recentnewsbtn" id="recent-news-button" autocomplete="off">
            <label class="btn btn-outline-primary w-50" for="recent-news-button">Recent News</label>
        </div>

        <!-- Post Box (Only for Logged-in Users) -->
        @if(Auth::check())
            @include('partials.post_form', [
                'route' => route('news.store'),
                'placeholder' => 'Share the news',
                'button_name' => 'Post'
            ])
        @endif
        
        @if(session('error'))       
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <!-- Posts -->
        <div id="news-list">
            @foreach($news as $post)
                @include('partials.news', ['news' => $post, 'isNewsItem' => false])
            @endforeach
        </div>

        <div id="pagination" class="text-center mt-4">
            {{ $news->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const topNewsButton = document.getElementById('top-news-button');
            const recentNewsButton = document.getElementById('recent-news-button');
            const newsFeed = document.getElementById('news-list');
            const movieTitleInput = document.getElementById('movie-title');
            const movieSearchResults = document.getElementById('movie-search-results');
            const postForm = document.getElementById('post-form');
            const tmdbIdInput = document.getElementById('tmdb_id');

            // Feed Controls: Switch between Top News and Recent News
            topNewsButton.addEventListener('click', function() {
                recentNewsButton.checked = false;
                fetch('/news/top')
                    .then(response => response.text())
                    .then(html => {
                        newsFeed.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error fetching feed:', error);
                        alert('Failed to load news. Please try again.');
                    });
            });

            recentNewsButton.addEventListener('click', function() {
                topNewsButton.checked = false;
                fetch('/news/recent')
                    .then(response => response.text())
                    .then(html => {
                        newsFeed.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error fetching feed:', error);
                        alert('Failed to load news. Please try again.');
                    });
            });

            // Handle movie title input changes
            movieTitleInput.addEventListener('input', function() {
            const query = movieTitleInput.value.trim();

            if (query.length >= 3) {
                fetch('{{ route('movies.search') }}?query=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        if (data.results && data.results.length > 0) {
                            movieSearchResults.innerHTML = '';
                            movieSearchResults.style.display = 'block';

                            data.results.forEach(movie => {
                                const listItem = document.createElement('li');
                                listItem.classList.add('list-group-item', 'movie-result');
                                listItem.dataset.id = movie.id;
                                listItem.dataset.title = movie.title;
                                
                                // Create poster image if available
                                const img = document.createElement('img');
                                img.src = 'https://image.tmdb.org/t/p/w500/' + movie.poster_path;
                                img.alt = movie.title;
                                img.style.width = '50px';
                                img.style.height = '75px';
                                img.style.marginRight = '10px';
                                
                                const titleText = document.createTextNode(movie.title);

                                listItem.appendChild(img);
                                listItem.appendChild(titleText);

                                movieSearchResults.appendChild(listItem);
                            });
                        } else {
                            movieSearchResults.style.display = 'none';
                        }
                    })
                    .catch(() => {
                        movieSearchResults.style.display = 'none';
                    });
            } else {
                movieSearchResults.style.display = 'none';
            }
        });

            movieSearchResults.addEventListener('click', function(e) {
                if (e.target.classList.contains('movie-result')) {
                    const movieId = e.target.dataset.id;
                    const movieTitle = e.target.dataset.title;

                    movieTitleInput.value = movieTitle;
                    tmdbIdInput.value = movieId;

                    movieSearchResults.style.display = 'none';
                }
            });
        });
    </script>
@endsection
