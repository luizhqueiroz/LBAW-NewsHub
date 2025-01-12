@extends('layouts.app')

@section('title', 'FAQ')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <h1 class="display-4 text-center text-primary">Frequently Asked Questions</h1>
            <p class="lead text-center mt-3">
                Find answers to the most common questions about <span class="fw-bold">NewsHub</span>.
            </p>
            <div class="accordion mt-5" id="faqAccordion">
                <!-- FAQ 1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq1">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                            What is NewsHub?
                        </button>
                    </h2>
                    <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            NewsHub is a collaborative platform where users can share, read, and discuss the latest news about movies and TV series. It's designed for entertainment enthusiasts to interact and stay updated.
                        </div>
                    </div>
                </div>
                <!-- FAQ 2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                            How can I share news on NewsHub?
                        </button>
                    </h2>
                    <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            To share news, simply create an account and log in. You’ll find an option to post news on your dashboard. 
                        </div>
                    </div>
                </div>
                <!-- FAQ 3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                            Is NewsHub free to use?
                        </button>
                    </h2>
                    <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes! NewsHub is completely free to use. You can browse content, share news, and engage with the community without any cost.
                        </div>
                    </div>
                </div>
                <!-- FAQ 4 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq4">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                            How do I comment on a news post?
                        </button>
                    </h2>
                    <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            You need to have an account and be logged in to comment on posts. Once logged in, navigate to the post and add your comment in the comment section below.
                        </div>
                    </div>
                </div>
                <!-- FAQ 5 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq5">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                            How can I contact support?
                        </button>
                    </h2>
                    <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="faq5" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            You can contact support through the <a href="{{ route('contact') }}" class="text-primary">Contact Us</a> page. Fill out the form or email us directly at support@newshub.com.
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
            </div>
        </div>
    </div>
</div>
@endsection
