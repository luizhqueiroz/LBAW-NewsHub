@extends('layouts.app')

@section('content')
    <div class="container py-4">

        <!-- Create Tags Section -->
        <div class="mb-4">
            <button type="button" class="btn btn-primary" id="openCreateTagModal">
                <i class="fas fa-plus-circle me-1"></i> Create New Tag
            </button>
        </div>

        <!-- Create Tag Modal -->
        <div class="modal fade" id="createTagModal" tabindex="-1" role="dialog" aria-labelledby="createTagModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="createTagModalLabel">Create New Tag</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('tags.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="tag_name" class="form-label">Tag Name</label>
                                <input id="tag_name" type="text" name="tag_name" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3 d-block mx-auto">Add Tag</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success and Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tags Table -->
        @if((isset($tags) && $tags->count() > 0) || (isset($requests) && $requests->count() > 0))
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th class="col-2">Tag ID</th>
                            <th class="col-4">Tag Name</th>
                            <th class="col-2">Status</th>
                            <th class="col-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Requested Tags --}}
                        @if(isset($requests) && $requests->count() > 0)
                            @foreach($requests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->tag_name }}</td>
                                    <td><span class="badge bg-warning">Requested</span></td>
                                    <td>
                                        <form action="{{ route('tags.accept', $request->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm me-2">
                                                <i class="fas fa-check"></i> Accept
                                            </button>
                                        </form>
                                        <form action="{{ route('tags.reject', $request->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        
                        {{-- Active Tags --}}
                        @if(isset($tags) && $tags->count() > 0)
                            @foreach($tags as $tag)
                                <tr>
                                    <td>{{ $tag->id }}</td>
                                    <td>{{ $tag->name }}</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>
                                        <form action="{{ route('tags.delete', $tag->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted">No tags available for management.</p>
        @endif
        

    <script>
        document.getElementById('openCreateTagModal').addEventListener('click', function () {
            var createTagModal = new bootstrap.Modal(document.getElementById('createTagModal'), {
                keyboard: false
            });
            createTagModal.show();
        });

        @if ($errors->any())
        var createTagModal = new bootstrap.Modal(document.getElementById('createTagModal'), {
            keyboard: false
        });
        createTagModal.show();
        @endif
    </script>
@endsection
