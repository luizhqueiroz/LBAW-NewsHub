@extends('layouts.app')

@section('content')
    @if(session('error'))       
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="news-item-page" id="news-item-page">
        @include('partials.news', ['news' => $news, 'isNewsItem' => true])

        @if(Auth::check())
            @include('partials.post_form', [
                'route' => route('comment.store', $news->id),
                'placeholder' => 'Post your reply',
                'button_name' => 'Reply'
            ])
        @endif

        <div class="comments-list">
            @foreach ($news->comments as $comment)
                @include('partials.comment', ['comment' => $comment])
            @endforeach
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('post-form');
        const commentList = document.querySelector('.comments-list');
        const alert = document.getElementById('error-alert');
        const wrapper = document.createElement('div');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw data;
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    form.reset();
            
                    commentList.insertAdjacentHTML('afterbegin', data.commentHtml);
                } else {
                    wrapper.innerHTML = [
                        `<div class="alert alert-danger alert-dismissible" role="alert">`,
                        `   <div>Failed to post the comment</div>`,
                        '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
                        '</div>'
                    ].join('');

                    alert.append(wrapper);
                }
            })
            .catch(error => {
                console.error('Error:', error);

                wrapper.innerHTML = [
                        `<div class="alert alert-danger alert-dismissible mt-2" role="alert">`,
                        `   <div>${ Object.values(error.errors)[0] || 'Something went wrong.' }</div>`,
                        '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
                        '</div>'
                    ].join('');

                alert.append(wrapper);
            });
        });
    });
</script>