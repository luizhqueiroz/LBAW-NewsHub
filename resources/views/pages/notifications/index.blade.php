@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Notifications</h1>
    </div>

    @if($notifications->count() > 0)
        <div class="card shadow-sm">
            <ul class="list-group list-group-flush">
                @foreach($notifications as $notification)
                    <li class="list-group-item d-flex justify-content-between align-items-start py-3 {{ $notification->viewed ? '' : 'bg-light' }}">
                        <div class="me-3">
                            <i class="fas fa-bell {{ $notification->viewed ? 'text-muted' : 'text-primary' }} me-2"></i>
                            @if($notification->notification_type === 'Like_notification')
                                @if($notification->like->news)
                                    <a class="text-decoration-none {{ $notification->viewed ? 'text-muted' : 'text-primary' }} fw-bold" href="{{ route('user.show', $notification->sender->id) }}">{{ $notification->sender->name }}</a>
                                    <a class="text-decoration-none text-dark" href="{{ route('news.show', $notification->like->news->id)}}">liked your news.</a>
                                @else
                                    <a class="text-decoration-none {{ $notification->viewed ? 'text-muted' : 'text-primary' }} fw-bold" href="{{ route('user.show', $notification->sender->id) }}">{{ $notification->sender->name }}</a>
                                    <a class="text-decoration-none text-dark" href="{{ route('news.show', $notification->like->comment->news->id) }}">liked your comment in a news.</a>
                                @endif
                            @elseif($notification->notification_type === 'Comment_notification')
                                <a class="text-decoration-none {{ $notification->viewed ? 'text-muted' : 'text-primary' }} fw-bold" href="{{ route('user.show', $notification->sender->id) }}">{{ $notification->sender->name }}</a>
                                <a class="text-decoration-none text-dark" href="{{ route('news.show', $notification->comment->news->id) }}">commented on your news.</a>
                               
                            @elseif($notification->notification_type === 'Follow_notification')
                                <a class="text-decoration-none {{ $notification->viewed ? 'text-muted' : 'text-primary' }} fw-bold" href="{{ route('user.show', $notification->sender->id) }}">{{ $notification->sender->name }}</a> started following you.
                            @endif
                            <br>
                            <small class="text-muted">{{ $notification->notification_date->diffForHumans() }}</small>
                        </div>

                        <div class="d-flex gap-2">
                            @if(!$notification->viewed)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success py-0 px-1">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('notifications.delete', $notification->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $notifications->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i> No notifications yet.
        </div>
    @endif
</div>

@endsection
