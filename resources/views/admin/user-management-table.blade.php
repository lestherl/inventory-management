@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Users')

@section('content')
    <div class="inventory-table-card">
        <div class="table-topbar">
            <div class="table-title-wrap">
                <h3 class="table-title">Users</h3>
            </div>
            <a href="{{ route('users.create') }}" class="add-table-btn">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Add
            </a>
        </div>

        @if ($users->isEmpty())
            <div class="empty-state">
                No users available.
            </div>
        @else
            <div class="inventory-table-wrapper">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td>
                                    <span class="badge badge-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="inventory-actions">
                                        <a href="{{ route('users.edit', $user) }}" class="inventory-action" aria-label="Edit user">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M4 20h4l10.5-10.5a2.1 2.1 0 0 0-3-3L5 17v3Z"/>
                                                <path d="m13.5 6.5 4 4"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inventory-action text-danger" aria-label="Delete user" onclick="return confirm('Are you sure you want to delete this user?')">
                                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M4 7h16M8 7V4.5A1.5 1.5 0 0 1 9.5 3h5A1.5 1.5 0 0 1 16 4.5V7M7 7l1 13h8l1-13"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
