@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-10 mx-auto text-center">
            <h1 class="display-4 fw-bold text-primary">About</h1>
            <p class="lead mt-4">
                Welcome to <span class="fw-bold">NewsHub</span>, your collaborative hub for the latest news about movies and TV series. We bring entertainment enthusiasts together to share, discover, and engage with content they love.
            </p>
            <img src="{{ asset('images/styles/about-banner.jpg') }}" alt="About NewsHub" class="img-fluid rounded shadow-sm mt-4" />
            
            <div class="text-start mt-5">
                <h3 class="fw-bold">Why Choose NewsHub?</h3>
                <p class="mt-3">
                    NewsHub offers a centralized space for movie and TV enthusiasts to come together. Here’s what makes us stand out:
                </p>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <i class="bi bi-check-circle-fill text-success"></i> Up-to-date news on movies and TV series.
                    </li>
                    <li class="list-group-item">
                        <i class="bi bi-check-circle-fill text-success"></i> A platform for users to share news and discuss trending topics.
                    </li>
                    <li class="list-group-item">
                        <i class="bi bi-check-circle-fill text-success"></i> A user-friendly design to enhance interaction and engagement.
                    </li>
                </ul>
            </div>

            <div class="text-start mt-5">
                <h3 class="fw-bold">Our Mission</h3>
                <p>
                    Our mission is to become the go-to platform for movie and TV series lovers by fostering a vibrant community and providing a space for meaningful discussions and updates about the entertainment world.
                </p>
            </div>

            <div class="text-start mt-5">
                <h3 class="fw-bold">Meet the Creators</h3>
                <p class="mt-3">
                    NewsHub was created by a passionate team of movie and TV enthusiasts who wanted to provide a platform for fans to connect, share, and engage. With a shared love for storytelling and a vision for fostering an interactive community, the creators have worked tirelessly to bring NewsHub to life.
                </p>
                <p>
                    Our team is made up of:
                </p>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <i class="bi bi-person-circle"></i> <strong>Luiz Queiroz</strong>
                    </li>
                    <li class="list-group-item">
                        <i class="bi bi-person-circle"></i> <strong>Raphael Moragas</strong>
                    <li class="list-group-item">
                        <i class="bi bi-person-circle"></i> <strong>João Victor Botelho</strong>
                    </li>
                    <li class="list-group-item">
                        <i class="bi bi-person-circle"></i> <strong>Victor Duarte</strong>
                    </li>
                </ul>
                <p class="mt-3">
                    Together, we’re committed to making NewsHub the best platform for entertainment enthusiasts. Thank you for being a part of our journey!
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
