@extends('layouts.app')

@section('title', 'Edit User Roles - ' . $user->name)

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <!-- Page Title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Edit User Roles</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                    {{-- <li class="breadcrumb-item"><a href="{{ route('admin.users.roles') }}">User Roles</a></li> --}}
                                    <li class="breadcrumb-item active">Edit</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- User Information -->
                    <div class="col-md-4">
                        <div class="card user-info-card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="material-icons-outlined me-2">person</i>
                                    User Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    @if ($user->avatar)
                                        <img src="{{ $user->avatar }}" class="rounded-circle avatar-img" width="80"
                                            height="80" alt="Avatar">
                                    @else
                                        <div class="rounded-circle avatar-placeholder d-inline-flex align-items-center justify-content-center text-white"
                                            style="width: 80px; height: 80px; font-size: 2rem;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="user-details">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Name</label>
                                        <p class="mb-0 fw-bold user-info-text">{{ $user->name }}</p>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-muted">Email</label>
                                        <p class="mb-0 user-info-text">{{ $user->email }}</p>
                                    </div>

                                    @if ($user->phone)
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Phone</label>
                                            <p class="mb-0 user-info-text">{{ $user->phone }}</p>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label text-muted">Member Since</label>
                                        <p class="mb-0 user-info-text">{{ $user->created_at->format('M d, Y') }}</p>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-muted">Status</label>
                                        <p class="mb-0">
                                            @if ($user->email_verified_at)
                                                <span class="badge status-badge verified">Verified</span>
                                            @else
                                                <span class="badge status-badge unverified">Unverified</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    <!-- Role Permissions Modal -->
    <div class="modal fade" id="rolePermissionsModal" tabindex="-1" aria-labelledby="rolePermissionsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rolePermissionsModalLabel">
                        <i class="material-icons-outlined me-2">security</i>
                        Role Permissions
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="rolePermissionsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
                    <!-- Role Assignment -->
                    <div class="col-md-8">
                        <div class="card roles-assignment-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">
                                    <i class="material-icons-outlined me-2">admin_panel_settings</i>
                                    Assign Roles to {{ $user->name }}
                                </h6>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm action-btn"
                                        id="selectAllRoles">
                                        <i class="material-icons-outlined me-1">select_all</i>
                                        Select All
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm action-btn"
                                        id="deselectAllRoles">
                                        <i class="material-icons-outlined me-1">deselect</i>
                                        Deselect All
                                    </button>
                                    <a href="{{ route('admin.users.roles.index') }}"
                                        class="btn btn-secondary btn-sm action-btn">
                                        <i class="material-icons-outlined me-1">arrow_back</i>
                                        Back
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="userRolesForm">
                                    @csrf
                                    <div class="row">
                                        @foreach ($roles as $role)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card border role-card" data-role="{{ $role->name }}">
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input role-checkbox" type="checkbox"
                                                                name="roles[]" value="{{ $role->name }}"
                                                                id="role{{ $role->id }}"
                                                                {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold"
                                                                for="role{{ $role->id }}">
                                                                {{ ucfirst($role->name) }}
                                                            </label>
                                                        </div>

                                                        <div class="mt-2 role-meta">
                                                            <small class="text-muted">
                                                                <i class="material-icons-outlined me-1 role-icon"
                                                                    style="font-size: 14px;">people</i>
                                                                {{ $role->users()->count() }} users
                                                            </small>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="material-icons-outlined me-1 role-icon"
                                                                    style="font-size: 14px;">security</i>
                                                                {{ $role->permissions()->count() }} permissions
                                                            </small>
                                                        </div>

                                                        @if ($role->permissions()->count() > 0)
                                                            <div class="mt-2">
                                                                <button type="button"
                                                                    class="btn btn-outline-info btn-sm view-permissions"
                                                                    data-role="{{ $role->name }}">
                                                                    <i class="material-icons-outlined me-1"
                                                                        style="font-size: 14px;">visibility</i>
                                                                    View Permissions
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="text-muted info-text">
                                                    <small>
                                                        <i class="material-icons-outlined me-1 info-icon"
                                                            style="font-size: 16px;">info</i>
                                                        Changes will be applied immediately after saving
                                                    </small>
                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary save-btn">
                                                        <i class="material-icons-outlined me-1">save</i>
                                                        Save Role Changes
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('styles')
    @include('admin.users.css.style')
@endpush

@push('plugin-scripts')
    @include('admin.users.js.script')
@endpush
