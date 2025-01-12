<form id="searchForm" class="d-flex mb-4 pt-2">
    <select id="searchType" class="form-control-1 rounded-5 me-2 text-center" style="border-color:rgb(212, 213, 213); color: #505050;">
        <option value="news">News</option>
        <option value="comments">Comments</option>
        @if(Auth::anyCheck())
            <option value="user">User</option>
        @endif
    </select>
    <input type="text" name="query" id="searchInput" class="form-control me-2" placeholder="Search">
    <button type="submit" class="btn btn-primary">Search</button>
</form>

<div id="resultsContainer"></div>

<script>
    document.getElementById('searchForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const query = document.getElementById('searchInput').value;
        const type = document.getElementById('searchType').value;

        if (!query) {
            alert('Please enter a search query');
            return;
        }

        let apiUrl = '';
        if (type === 'news') {
            apiUrl = '/api/news';
        } else if (type === 'comments') {
            apiUrl = '/api/comments';
        } else if (type === 'user') {
            apiUrl = '/search/users';
        }

        fetch(`${apiUrl}?query=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
            })
            .then(response => response.json())
            .then(data => {
                toggleFeedVisibility(false);
                displayResults(data, type);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request.');
            });
    });

    function displayResults(data, type) {
        const resultsContainer = document.getElementById('resultsContainer');
        resultsContainer.className = '';
        resultsContainer.innerHTML = '';

        if (data.html) {
            if (type === 'user') {
                resultsContainer.classList.add('d-flex', 'flex-column', 'justify-content-center', 'align-items-center');
            } else if (type === 'comments') {
                resultsContainer.classList.add('news-item-page', 'comments-list');
            }

            resultsContainer.innerHTML = data.html;
        } else {
            resultsContainer.innerHTML = '<p>No results found.</p>';
        }
    }

    function toggleFeedVisibility(visible) {
        const feedContainer = document.getElementById('news-feed');
        feedContainer.style.display = visible ? 'block' : 'none';
    }
</script>