@push('plugin-scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.responsive.min.js"></script>
@endpush

@push('plugin-styles')
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.5/css/responsive.dataTables.min.css" rel="stylesheet" />
@endpush

@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">User Role Management</h3>
            <p class="page-subtitle">Assign and manage user roles</p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <button class="btn btn-refresh" id="refreshData">
                    <i class="material-icons-outlined">refresh</i>
                    <span>Refresh</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card primary-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper primary-icon">
                        <i class="material-icons-outlined">people</i>
                    </div>
                    <div class="stat-content">
                        <h6 class="stat-title">Total Users</h6>
                        <h3 class="stat-value" id="totalUsersCount">-</h3>
                        <small class="stat-desc">All registered users</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card success-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper success-icon">
                        <i class="material-icons-outlined">assignment_ind</i>
                    </div>
                    <div class="stat-content">
                        <h6 class="stat-title">With Roles</h6>
                        <h3 class="stat-value" id="usersWithRolesCount">-</h3>
                        <small class="stat-desc">Users with assigned roles</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card warning-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper warning-icon">
                        <i class="material-icons-outlined">person_outline</i>
                    </div>
                    <div class="stat-content">
                        <h6 class="stat-title">Without Roles</h6>
                        <h3 class="stat-value" id="usersWithoutRolesCount">-</h3>
                        <small class="stat-desc">Users without any role</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card info-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper info-icon">
                        <i class="material-icons-outlined">admin_panel_settings</i>
                    </div>
                    <div class="stat-content">
                        <h6 class="stat-title">Available Roles</h6>
                        <h3 class="stat-value" id="availableRolesCount">-</h3>
                        <small class="stat-desc">Total system roles</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter and Action Buttons -->
<div class="control-panel mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="d-flex gap-3">
                <div class="dropdown">
                    <button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="material-icons-outlined">filter_list</i>
                        <span>Filter by Role</span>
                    </button>
                    <ul class="dropdown-menu custom-dropdown" id="roleFilterDropdown">
                        <li><a class="dropdown-item role-filter" href="#" data-role="">All Users</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <!-- Roles will be populated here -->
                    </ul>
                </div>
                <div class="search-box">
                    <i class="material-icons-outlined search-icon">search</i>
                    <input type="text" class="form-control" placeholder="Search users..." id="userSearch">
                </div>
            </div>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-manage">
                <i class="material-icons-outlined">admin_panel_settings</i>
                <span>Manage Roles</span>
            </a>
        </div>
    </div>
</div>

<!-- Table with Bootstrap and custom styles -->
<div class="table-container">
    <x-data-table
        title="User Role Assignments"
        table-id="user-roles-table"
        fetch-url="{{ route('admin.users.roles.data') }}"
        :columns="['ID', 'User', 'Email', 'Current Roles', 'Last Login', 'Actions']"
        :columns-config="[
            ['data' => 'id', 'name' => 'id'],
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'email', 'name' => 'email'],
            ['data' => 'roles', 'name' => 'roles', 'orderable' => false, 'searchable' => false],
            ['data' => 'updated_at', 'name' => 'updated_at' ],
            ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false]
        ]"
    />
</div>
@endsection

@push('data-table-styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        --success-gradient: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        --info-gradient: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
        --warning-gradient: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        --danger-gradient: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
        --card-bg: rgba(6, 11, 40, 0.94);
        --card-border: rgba(255, 255, 255, 0.1);
        --text-primary: #e6ecf0;
        --text-secondary: #b1b7c1;
        --text-muted: #858796;
    }

    /* Page Header */
    .page-header {
        padding: 1.5rem 0;
        border-bottom: 1px solid var(--card-border);
    }

    .page-title {
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .page-subtitle {
        color: var(--text-secondary);
        margin-bottom: 0;
    }

    /* Statistical Cards */
    .stat-card {
        border-radius: 0.75rem;
        border: 1px solid var(--card-border);
        box-shadow: 0 0.15rem 1.75rem 0 rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 2rem 0 rgba(0, 0, 0, 0.25);
    }

    .primary-card {
        background: var(--card-bg);
        border-left: 4px solid #4e73df;
    }

    .success-card {
        background: var(--card-bg);
        border-left: 4px solid #1cc88a;
    }

    .info-card {
        background: var(--card-bg);
        border-left: 4px solid #36b9cc;
    }

    .warning-card {
        background: var(--card-bg);
        border-left: 4px solid #f6c23e;
    }

    .icon-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        margin-right: 1rem;
    }

    .primary-icon {
        background: var(--primary-gradient);
        color: white;
    }

    .success-icon {
        background: var(--success-gradient);
        color: white;
    }

    .info-icon {
        background: var(--info-gradient);
        color: white;
    }

    .warning-icon {
        background: var(--warning-gradient);
        color: white;
    }

    .icon-wrapper i {
        font-size: 1.5rem;
    }

    .stat-content {
        flex: 1;
    }

    .stat-title {
        color: var(--text-secondary);
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        color: var(--text-primary);
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .stat-desc {
        color: var(--text-muted);
        font-size: 0.75rem;
    }

    /* Control Panel */
    .control-panel {
        background: var(--card-bg);
        border-radius: 0.75rem;
        padding: 1rem;
        border: 1px solid var(--card-border);
    }

    /* Buttons */
    .btn {
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn i {
        font-size: 1.25rem;
    }

    .btn-filter {
        background: rgba(78, 115, 223, 0.1);
        color: #4e73df;
        border: 1px solid rgba(78, 115, 223, 0.2);
    }

    .btn-filter:hover {
        background: rgba(78, 115, 223, 0.2);
        color: #4e73df;
    }

    .btn-refresh {
        background: rgba(28, 200, 138, 0.1);
        color: #1cc88a;
        border: 1px solid rgba(28, 200, 138, 0.2);
    }

    .btn-refresh:hover {
        background: rgba(28, 200, 138, 0.2);
        color: #1cc88a;
    }

    .btn-export {
        background: rgba(54, 185, 204, 0.1);
        color: #36b9cc;
        border: 1px solid rgba(54, 185, 204, 0.2);
    }

    .btn-export:hover {
        background: rgba(54, 185, 204, 0.2);
        color: #36b9cc;
    }

    .btn-manage {
        background: var(--info-gradient);
        color: white;
        border: none;
    }

    .btn-manage:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        color: white;
    }

    /* Search Box */
    .search-box {
        position: relative;
        flex: 1;
        max-width: 300px;
    }

    .search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 1.25rem;
    }

    .search-box .form-control {
        padding-left: 2.5rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--card-border);
        color: var(--text-primary);
        border-radius: 0.5rem;
    }

    .search-box .form-control:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(78, 115, 223, 0.5);
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }

    /* Dropdowns */
    .custom-dropdown {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        padding: 0.5rem 0;
    }

    .dropdown-item {
        color: var(--text-secondary);
        padding: 0.5rem 1rem;
    }

    .dropdown-item:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary);
    }

    .dropdown-divider {
        border-color: var(--card-border);
        margin: 0.25rem 0;
    }

    /* Table Container */
    .table-container {
        background: var(--card-bg);
        border-radius: 0.75rem;
        padding: 1rem;
        border: 1px solid var(--card-border);
    }

    /* DataTable Styling */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_processing,
    .dataTables_wrapper .dataTables_paginate {
        color: var(--text-secondary) !important;
    }

    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid var(--card-border) !important;
        color: var(--text-primary) !important;
        border-radius: 0.5rem !important;
    }

    table.dataTable thead th, table.dataTable thead td {
        border-bottom: 1px solid var(--card-border) !important;
        color: var(--text-primary) !important;
    }

    table.dataTable tbody tr {
        background-color: transparent !important;
    }

    table.dataTable.stripe tbody tr.odd {
        background-color: rgba(255, 255, 255, 0.02) !important;
    }

    table.dataTable.hover tbody tr:hover, table.dataTable.hover tbody tr.odd:hover {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }

    table.dataTable tbody td {
        color: var(--text-secondary) !important;
        border-bottom: 1px solid var(--card-border) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: var(--text-secondary) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: rgba(78, 115, 223, 0.1) !important;
        color: #4e73df !important;
        border: 1px solid rgba(78, 115, 223, 0.2) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(255, 255, 255, 0.05) !important;
        color: var(--text-primary) !important;
        border: 1px solid var(--card-border) !important;
    }

    /* User Avatar */
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--card-border);
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    /* Role Badges */
    .role-badge {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .control-panel .row {
            flex-direction: column;
            gap: 1rem;
        }

        .control-panel .col-md-6 {
            width: 100%;
            text-align: left !important;
        }

        .search-box {
            max-width: 100%;
        }
    }

    /* Print Styles */
    @media print {
        .btn, .control-panel, .page-header {
            display: none !important;
        }

        .table-container {
            border: none !important;
            padding: 0 !important;
        }

        .stat-card {
            break-inside: avoid;
        }
    }
</style>
@endpush

@push('data-table-scripts')
<script>
$(document).ready(function() {
    let currentRoleFilter = '';

    // Wait for the DataTable to be ready
    $(document).on('TableReady', function() {
        const userRolesTable = window['userRolesTable'];

        // Update summary cards and filter dropdown when data loads
        userRolesTable.on('xhr.dt', function(e, settings, json, xhr) {
            updateSummaryCards(json);
            updateRoleFilterDropdown(json);
        });

        // Load initial data
        userRolesTable.ajax.reload();

        // Role filter functionality
        $(document).on('click', '.role-filter', function(e) {
            e.preventDefault();
            currentRoleFilter = $(this).data('role');

            // Update button text
            const filterText = currentRoleFilter ? `Filter: ${currentRoleFilter}` : 'Filter by Role';
            $('.btn-filter').html(`<i class="material-icons-outlined">filter_list</i><span>${filterText}</span>`);

            // Update DataTable settings to include filter
            userRolesTable.settings()[0].ajax.data = function(d) {
                d.role_filter = currentRoleFilter;
                return d;
            };

            // Reload table
            userRolesTable.ajax.reload();
        });

        // Refresh button functionality
        $('#refreshData').on('click', function() {
            userRolesTable.ajax.reload();
        });

        // Search functionality
        $('#userSearch').on('keyup', function() {
            userRolesTable.search(this.value).draw();
        });
    });

    // Get role badge class based on role name
    function getRoleBadgeClass(role) {
        const roleClasses = {
            'admin': 'bg-danger',
            'manager': 'bg-warning',
            'editor': 'bg-info',
            'user': 'bg-secondary',
            'driver': 'bg-success',
            'customer': 'bg-primary'
        };

        return roleClasses[role.toLowerCase()] || 'bg-secondary';
    }

    // Update summary cards
    function updateSummaryCards(data) {
        if (data.summary) {
            $('#totalUsersCount').text(data.summary.total_users || '-');
            $('#usersWithRolesCount').text(data.summary.users_with_roles || '-');
            $('#usersWithoutRolesCount').text(data.summary.users_without_roles || '-');
            $('#availableRolesCount').text(data.summary.available_roles || '-');
        }
    }

    // Update role filter dropdown
    function updateRoleFilterDropdown(data) {
        if (data.roles) {
            const dropdown = $('#roleFilterDropdown');
            // Keep the "All Users" option and divider
            const staticItems = dropdown.find('li').slice(0, 2);
            dropdown.empty().append(staticItems);

            // Add role options
            data.roles.forEach(function(role) {
                dropdown.append(`<li><a class="dropdown-item role-filter" href="#" data-role="${role.name}">${role.name} (${role.users_count})</a></li>`);
            });
        }
    }
});
</script>
@endpush
