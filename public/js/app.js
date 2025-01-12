  function addEventListeners() {
    let newsContainers = document.querySelectorAll('#resultsContainer, #news-feed, #news-item-page');
    let followButtons = document.querySelectorAll('.follow-btn, .unfollow-btn, #resultsContainer');

    [].forEach.call(newsContainers, function(container) {
      container.addEventListener('click', function(event) {
        if (event.target.classList.contains('edit-news-button')) {
          editNews.call(event.target, event);
        }

        if (event.target.classList.contains('update-news-button')) {
          sendUpdateNewsRequest.call(event.target);
        }

        if (event.target.classList.contains('cancel-news-button')) {
          cancelEditNews.call(event.target, event);
        }

      });
    });

   [].forEach.call(followButtons, function(button) {
     button.addEventListener('click', function(event) {
      if (event.target.classList.contains('follow-btn') || event.target.classList.contains('unfollow-btn')) {
        sendFollowRequest.call(event.target);
      }
     });
   });
  }
  
  function encodeForAjax(data) {
    if (data == null) return null;
    return Object.keys(data).map(function(k){
      return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
    }).join('&');
  }
  
  function sendAjaxRequest(method, url, data, handler) {
    let request = new XMLHttpRequest();
  
    request.open(method, url, true);
    request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.addEventListener('load', handler);
    request.send(encodeForAjax(data));
  }

  /*
  =============================
  -- NEWS
  =============================
  */

  function toggleLike(newsId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/news/${newsId}/like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            const likesCount = document.getElementById(`likes-count-${newsId}`);
            likesCount.textContent = data.likes_count + ' Likes';
            const heartIcon = document.getElementById(`heart-icon-${newsId}`);

            if (data.message === 'Liked') {
                heartIcon.classList.remove('fa-regular');
                heartIcon.classList.add('fa-solid');
            } else {
                heartIcon.classList.remove('fa-solid');
                heartIcon.classList.add('fa-regular');
            }
        }
    })
    .catch(error => console.error('Error:', error));
  } 
  
  function editNews(event) {
    event.preventDefault();
    let newsContainer = this.closest('.news-item');
    let newsContentAll = newsContainer.querySelector('.news-content');
    let anchor = newsContentAll.querySelector('a');
    let newsContent = newsContainer.querySelector('.news-content .news-content-container');
    let currentContent = newsContent.querySelector('p').textContent;

    if (anchor !== null) {
      newsContentAll.dataset.href = anchor.getAttribute('href');
      anchor.style.cursor = 'default';
      anchor.removeAttribute('href');
    }
    newsContent.dataset.originalContent = currentContent;

    newsContent.innerHTML = `
      <textarea class="form-control ms-2 mb-2" rows="4">${currentContent}</textarea>
      <button type="submit" class="btn btn-secondary btn-sm update-news-button">Save</button>
      <button type="button" class="btn btn-secondary btn-sm ms-1 cancel-news-button">Cancel</button>
        `;
  }

  function cancelEditNews(event) {
    event.preventDefault();
    let newsContainer = this.closest('.news-item');
    let newsContentAll = newsContainer.querySelector('.news-content');
    let anchor = newsContentAll.querySelector('a');
    let newsContent = newsContainer.querySelector('.news-content .news-content-container');

    if (anchor !== null) {
      anchor.setAttribute('href', newsContentAll.dataset.href);
      anchor.style.cursor = 'pointer';
    }
    let originalContent = newsContent.dataset.originalContent;
    
    newsContent.innerHTML = `
      <p>${originalContent}</p>
        `;
}
  
  function sendUpdateNewsRequest() {
    let newsBody = this.closest('div.news-body');
    let newsContent = newsBody.querySelector('.news-content');
    let newsText = newsContent.querySelector('.news-content .news-content-container .form-control');

    let content = newsText.value;
    let newsId = newsContent.getAttribute('data-id');

    if (!content) {
      alert('Content cannot be empty.');
      return;
    }

    sendAjaxRequest('put', '/news/' + newsId, { content: content }, newsUpdateHandler);
  }

  function newsUpdateHandler() {
    let news = JSON.parse(this.responseText);
    let newsContent = document.querySelector('div.news-content[data-id="' + news.id + '"]');
    let anchor = newsContent.querySelector('a');
    let newsContainer = newsContent.querySelector('.news-content-container');

    if (anchor !== null) {
      anchor.setAttribute('href', newsContent.dataset.href);
      anchor.style.cursor = 'pointer';
    }

    newsContainer.innerHTML = `
    <p>${news.content}</p>
      `;
  }

  /*
  =============================
  - COMMENTS
  =============================
  */

  function toggleCommentLike(commentId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/comments/${commentId}/like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            const likesCount = document.getElementById(`comment-likes-count-${commentId}`);
            likesCount.textContent = data.likes_count + ' Likes';
            const heartIcon = document.getElementById(`comment-heart-icon-${commentId}`);

            if (data.message === 'Liked') {
                heartIcon.classList.remove('fa-regular');
                heartIcon.classList.add('fa-solid');
            } else {
                heartIcon.classList.remove('fa-solid');
                heartIcon.classList.add('fa-regular');
            }
        }
    })
    .catch(error => console.error('Error:', error));
  }

function enableEditMode(commentId) {
    const commentContent = document.getElementById(`comment-content-${commentId}`);
    const currentContent = commentContent.textContent;

    const container = document.getElementById(`comment-container-${commentId}`);
    container.innerHTML = `
        <textarea id="edit-comment-${commentId}" class="form-control mb-2">${currentContent}</textarea>
        <button class="btn btn-primary btn-sm" onclick="saveComment(${commentId})">Save</button>
        <button class="btn btn-secondary btn-sm" onclick="cancelEdit(${commentId}, '${currentContent}')">Cancel</button>
    `;
}

function saveComment(commentId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const newContent = document.getElementById(`edit-comment-${commentId}`).value;

    fetch(`/comments/${commentId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ content: newContent })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById(`comment-container-${commentId}`);
            container.innerHTML = `<p id="comment-content-${commentId}">${newContent}</p>`;
        } else {
            alert("Error updating comment");
        }
    })
    .catch(error => console.error('Error:', error));
}

function cancelEdit(commentId, originalContent) {
    const container = document.getElementById(`comment-container-${commentId}`);
    container.innerHTML = `<p id="comment-content-${commentId}">${originalContent}</p>`;
}

function deleteComment(commentId) {
    if (!confirm("Are you sure you want to delete this comment?")) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/comments/${commentId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`comment-container-${commentId}`).closest('.comment-wrapper').remove();
        } else {
            alert("Error deleting comment");
        }
    })
    .catch(error => console.error('Error:', error));
  }

  /*
  =============================
  -- FOLLOW
  =============================
  */


  function sendFollowRequest() {
    let followButton = this;
    let url = followButton.getAttribute('data-url');
    let action = followButton.classList.contains('follow-btn') ? 'follow' : 'unfollow';

    sendAjaxRequest('post', url, { action: action }, function () {
      followHandler.call(followButton, this);
  });
  }

  function followHandler(request) {
    let button = this;
    let data = JSON.parse(request.responseText);

    if (data.success) {
      if (button.classList.contains('follow-btn')) {
            button.classList.remove('btn-primary', 'follow-btn');
            button.classList.add('btn-danger', 'unfollow-btn');
            button.textContent = 'Unfollow';
            button.setAttribute('data-url', data.unfollow_url);
        } else if (button.classList.contains('unfollow-btn')) {
            button.classList.remove('btn-danger', 'unfollow-btn');
            button.classList.add('btn-primary', 'follow-btn');
            button.textContent = 'Follow';
            button.setAttribute('data-url', data.follow_url);
        }
    } else {
      showErrorModal(data.message);
    }
  }

  /*
  =============================
  -- ERROR
  =============================
  */

  function showErrorModal(message) {
    document.getElementById('errorModalMessage').textContent = message;

    let errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    errorModal.show();
  }

  /*
  =============================
  -- MOVIE
  =============================
  */

  document.addEventListener('DOMContentLoaded', function () {
    const newsItem = document.querySelector('.news-item');
    const movieId = newsItem.getAttribute('data-movie-id');
    const newsId = newsItem.getAttribute('data-id');
    const movieContainer = document.getElementById(`movie-details-${newsId}`);

    // Only fetch movie details if movie_id exists
    if (movieId && movieContainer) {
        // Fetch movie details from your backend route
        fetch(`/movies/${movieId}`)
            .then(response => response.json())
            .then(movie => {
                if (movie) {
                    const movieHTML = `
                    <div class="movie-details-card card shadow-sm mb-4">
                            <div class="card-header">
                                <h5 class="m-0">Movie Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Poster and Movie Info Section -->
                                    <div class="col-md-4">
                                        <img 
                                            src="${movie.poster_path ? 'https://image.tmdb.org/t/p/w500/' + movie.poster_path : '/images/default-movie.png'}" 
                                            alt="${movie.title}" 
                                            class="img-fluid rounded shadow-sm">
                                    </div>
                                    <div class="col-md-8">
                                        <h2 class="movie-title mb-2">${movie.title}</h2>
                                        <p class="text-muted movie-release-date mb-3">Released: ${movie.release_date}</p>
                                        
                                        <div class="movie-genres mb-3">
                                            <strong>Genres:</strong>
                                            ${movie.genres ? movie.genres.map(genre => `<span class="badge bg-primary">${genre.name}</span>`).join(' ') : 'Not available'}
                                        </div>

                                        <div class="movie-runtime mb-3">
                                            <strong>Runtime:</strong> ${movie.runtime ? movie.runtime + ' minutes' : 'Not available'}
                                        </div>

                                        <div class="movie-overview mb-3">
                                            <strong>Overview:</strong>
                                            <p>${movie.overview || 'No overview available.'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                        
                    movieContainer.innerHTML = movieHTML;
                }
            })
            .catch(error => console.error(`Error fetching details for movie ${movieId}:`, error));
    }
});

/*
=============================
-- PUSHER
=============================
*/

  fetch('/notifications/data', {
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json',
    },
  })
    .then(response => response.json())
    .then(ids => {
        const { newsIds, userId } = ids;

        const pusher = new Pusher(pusherAppKey, {
            cluster: pusherCluster,
            encrypted: true
        });

        newsIds.forEach(newsId => {
            const newsChannel = pusher.subscribe(`news.${newsId}`);
            newsChannel.bind('Notification-comment', function (data) {
              const message = `New comment on your <a href="/news/${newsId}">news</a>: ${data.comment.content}`;
              showToast(message);
            });

            newsChannel.bind('Notification-like', function (data) {
              if (data.like.comment_id) {
                const message = `Your comment on a <a href="/news/${newsId}">news</a> was liked by <a href="/users/${data.like.sender.id}">${data.like.sender.user_name}</a>`;
                showToast(message);
              } else if (data.like.news_id) {
                const message = `Your <a href="/news/${newsId}">news</a> was liked by <a href="/users/${data.like.sender.id}">${data.like.sender.user_name}</a>`;
                showToast(message);
              }
            });
        });
        
        const followChannel = pusher.subscribe(`user.${userId}`);
        followChannel.bind('Notification-follow', function (data) {
          const message = `<a href="/users/${data.follower.id}">${data.follower.user_name}</a> is now following you`;
          showToast(message);
        });
        
    })
    .catch(error => {
        console.error('Error fetching dashboard data:', error);
    });

function showToast(message) {
  const toastContainer = document.getElementById('toastContainer');
  const toastHTML = `
      <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
          <div class="toast-header bg-primary text-white">
              <strong class="me-auto">Notification</strong>
              <small class="text-muted">Just now</small>
              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
          <div class="toast-body">
              ${message}
          </div>
      </div>
  `;

  toastContainer.innerHTML = toastHTML;

  const toastElement = new bootstrap.Toast(toastContainer.lastElementChild);
  toastElement.show();
}


  addEventListeners();