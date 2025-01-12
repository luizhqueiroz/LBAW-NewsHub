<div class="mb-4">
    <form id="post-form" method="POST" action="{{ $route }}">
        @csrf
        <textarea name="content" class="form-control" placeholder="{{ $placeholder }}" rows="3" required>{{ old('content') }}</textarea>
        
        @if($button_name === 'Post')
            <!-- Movie Title Field -->
            <div class="mt-2">
                <input type="text" id="movie-title" class="form-control" name="movie_title" placeholder="Find the movie for your news" value="{{ old('movie_title') }}">
                <ul id="movie-search-results" class="list-group mt-2" style="display:none;"></ul>
                
                <!-- Hidden input to store selected movie_id -->
                <input type="hidden" name="tmdb_id" id="tmdb_id" value="{{ old('tmdb_id') }}">
            </div>
        @endif
        
        <button type="submit" class="btn btn-primary mt-2">{{ $button_name }}</button>
        <div id="error-alert"></div>
    </form>
</div>
