<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Pusher -->
        <script>
            const pusherAppKey = "{{ env('PUSHER_APP_KEY') }}";
            const pusherCluster = "{{ env('PUSHER_APP_CLUSTER') }}";
        </script>  
        <script src="https://js.pusher.com/7.0/pusher.min.js" defer></script>
        <script type="text/javascript" src="{{ url('js/app.js') }}" defer></script>
    </head>
    <body>
        <main>
            <div class="layout-container">

                <!-- Sidebar -->
                <div class="sidebar">
                    <div class="logo">
                        <a href="{{ route('home') }}">NewsHub</a>
                    </div>
                    <nav class="sidebar-nav">
                        <a href="{{ route('home') }}" class="sidebar-nav-link">
                            <i class="fas fa-home"></i> Home
                        </a>
                        @if(Auth::guard('web')->check())
                            <a href="{{ route('user.show', Auth::user()->id) }}" class="sidebar-nav-link">
                                <i class="fas fa-user"></i> Profile
                            </a>                            
                            <a href="/tags" class="sidebar-nav-link">
                                <i class="fas fa-tags"></i> Tags
                            </a>
                            <a href="{{ route('users.follows', Auth::user()->id) }}" class="sidebar-nav-link">
                                <i class="fas fa-user-friends"></i> Follows
                            </a>

                            <a href="{{ route('movies.trending') }}" class="sidebar-nav-link">
                                <i class="fas fa-fire"></i> Trending
                            </a>
                            
                            <div class="btn-group dropend sidebar-nav-link p-0">
                                <!-- Notifications Dropdown -->
                                <a class="sidebar-nav-link" href="{{ route('notifications.index') }}">
                                    <i class="fas fa-bell"></i>
                                    Notifications
                                    @if(Auth::user()->unreadNotifications->count() > 0)
                                        <span class="badge bg-danger rounded-pill">{{ Auth::user()->unreadNotifications->count() }}</span>
                                    @endif
                                </a>
                                <button type="button" class="btn dropdown-toggle dropdown-toggle-split p-0" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>                               
                                
                                <div class="notification-dropdown dropdown-menu py-3 px-2" aria-labelledby="navbarDropdown">
                                    <!-- Dropdown Header -->
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong>Notifications</strong>
                                        <span class="text-muted small">Unread: {{ Auth::user()->unreadNotifications->count() }}</span>
                                    </div>

                                    <!-- Notifications -->
                                    @forelse(Auth::user()->unreadNotifications->take(5) as $notification)
                                        <div class="dropdown-item border-bottom">
                                            <p class="mb-0 text-muted small text-start">New notification</p>
                                            <div class="d-flex justify-content-between">
                                                <strong>{{ $notification->sender->name }}</strong>
                                                <small class="text-muted">{{ $notification->notification_date->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="dropdown-item text-muted m-0">No new notifications</p>
                                    @endforelse
                                </div>
                            </div>

                        @elseif(Auth::guard('admin')->check())
                            <a href="{{ route('admin.show', Auth::currentUser()->id) }}" class="sidebar-nav-link">
                                <i class="fas fa-user"></i> Profile
                            </a>
                            <a href="{{ route('admin.users.show') }}" class="sidebar-nav-link">
                                <i class="fas fa-users-cog"></i> Manage Users
                            </a>
                            <a href="{{ route('tags.show') }}" class="sidebar-nav-link">
                                <i class="fas fa-tags"></i> Manage Tags
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="sidebar-nav-link">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        @endif
                    </nav>

                    <!-- Separator Line -->
                    <hr class="sidebar-custom-separator">

                    <!-- User Information or Guest User -->
                    <div class="sidebar-user-info">
                        @if(Auth::anyCheck())
                            <img src="{{ Auth::currentUser()->image ? asset(Auth::currentUser()->image->image_path) : asset('images/avatars/default-avatar.png') }}" alt="User Avatar" class="user-avatar">
                            
                            <div>
                                <span>{{ '@' . Auth::currentUser()->name }}</span>
                            </div>

                            <!-- Three Dots Dropdown for Logout -->
                            <div class="dropdown d-flex">
                                <!-- Button Trigger -->
                                <button class="btn-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="d-flex flex-column gap-1">
                                        <div class="dots"></div>
                                        <div class="dots"></div>
                                        <div class="dots"></div>
                                    </div>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <ul class="dropdown-menu rounded-4">
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="dropdown-item rounded-4 p-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item rounded-4 p-2">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>                            
                        @else
                            <img src="{{ asset('images/avatars/default-avatar.png') }}" alt="Guest Avatar" class="sidebar-photo-rounded">
                            
                            <div>
                                <span>@@GuestUser</span>
                            </div>
                        @endif
                    </div>

                    <!-- Separator Line -->
                    <hr class="sidebar-custom-separator">

                    <!-- Footer Links -->
                    <div class="footer-links">
                        <a href="{{ route('about') }}">About</a>
                        <a href="{{ route('contact') }}">Contact Us</a>
                        <a href="{{ route('faq') }}">FAQ</a>
                    </div>
                </div>

                <div class="layout-content">
                    <section id="content">
                        @yield('content')
                    </section>
                </div>
            </div>
        </main>

        <!-- Error Modal -->
        @include('partials.error_modal')

        <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer">
            <!-- Toasts will be dynamically appended here -->
        </div>
    </body>
</html>