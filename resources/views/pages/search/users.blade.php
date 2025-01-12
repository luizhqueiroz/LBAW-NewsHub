@extends('layouts.app')

@section('content')
    @if(isset($searchQuery))
        <h2 class="search-tittle">Search results for "{{ $searchQuery }}":</h2>
    @endif

    @if($users->isEmpty())
        <p class="search-not-found">No users found.</p>
    @else
        <ul class="search-list">
            @foreach($users as $user)
                <li class="search-item">@include('partials.user', ['user' => $user])</li>
            @endforeach
        </ul>
    @endif
@endsection