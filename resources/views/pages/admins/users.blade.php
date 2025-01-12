@extends('layouts.app')

@section('content')
    <div class="container">
        
        @include('partials.user-searchbar')

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary mt-2 mb-2" id="openModalButton">
            <i class="fas fa-plus-circle me-1"></i> Create New Account
        </button>

        <!-- Modal -->
        <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createUserModalLabel">Create New Account</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.user.store') }}" class="register-form">
                            @csrf
                            <div class="form-group mb-2">
                                <label for="name">Username</label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="form-control">
                            </div>
                            <div class="form-group mb-2">
                                <label for="email">Email Address</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-control">
                                @if ($errors->has('email'))
                                    <span class="error">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                            <div class="form-group mb-2">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="error text-danger mb-1">
                                @if ($errors->has('password'))
                                    <span>{{ $errors->first('password') }}</span>
                                @endif
                            </div>
                            <div class="form-group mb-2">
                                <label for="password-confirm">Confirm Password</label>
                                <input id="password-confirm" type="password" name="password_confirmation" required class="form-control">
                            </div>
                            <div class="form-buttons d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary mt-3">Create Account</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(isset($users) && $users->count() > 0)
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th class="col-1">ID</th>
                        <th class="col-2">Name</th>
                        <th class="col-3">Email</th>
                        <th class="col-1">Status</th>
                        <th class="col-5">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->user_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->blocked)
                                    <span class="badge bg-danger">Blocked</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                    {{-- View Button --}}
                                    <a href="{{ route('user.show', $user->id) }}" class="btn btn-info btn-sm ms-4">
                                        <i class="fas fa-eye"></i> View
                                    </a>

                                    {{-- Edit Button --}}
                                    <a href="{{ route('user.edit', $user->id) }}" class="btn btn-warning btn-sm ms-4">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    {{-- Delete Button --}}
                                    <form action="{{ route('admin.user.delete', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm ms-4">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>

                                    {{-- Block/Unblock Button --}}
                                    @if($user->blocked)
                                        <form action="{{ route('admin.user.unblock', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm ms-4">
                                                <i class="fas fa-unlock"></i> Unblock
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.user.block', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm ms-4">
                                                <i class="fas fa-ban"></i> Block
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p class="text-muted">No users found.</p>
        @endif
    </div>
    <script>
        document.getElementById('openModalButton').addEventListener('click', function () {
            var myModal = new bootstrap.Modal(document.getElementById('createUserModal'), {
                keyboard: false
            });
            myModal.show();
        });

        @if ($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('createUserModal'), {
                keyboard: false
            });
            myModal.show();
        @endif
    </script>
@endsection