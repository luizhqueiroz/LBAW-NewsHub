@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="container my-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h1 class="display-4 text-center text-primary">Contact Us</h1>
            <p class="lead text-center mt-3">
                Have questions, suggestions, or need assistance? We’re here to help! Reach out to us using the form below.
            </p>

            <form action="{{ route('contact.store') }}" method="POST" class="mt-4 shadow-sm p-4 bg-light rounded">
                @csrf
                @method('POST')
                <div class="mb-3">
                    <label for="name" class="form-label">Your Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter your name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Your Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control" placeholder="Enter the subject" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea id="message" name="message" rows="5" class="form-control" placeholder="Write your message here..." required></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-envelope-fill"></i> Send Message
                    </button>
                </div>
            </form>

            <div class="mt-5 text-center">
                <h5>Alternatively, reach us at:</h5>
                <p class="mt-2">
                    <i class="bi bi-envelope"></i> <a href="mailto:support@newshub.com">support@newshub.com</a><br>
                    <i class="bi bi-telephone"></i> +351-9999-9999
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
