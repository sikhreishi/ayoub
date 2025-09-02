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
    <div class="container-fluid">
        <div class="row mb-4" id="statsCards">
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label for="statusFilter">Status</label>
                        <select id="statusFilter" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="open">Open</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="priorityFilter">Priority</label>
                        <select id="priorityFilter" class="form-control">
                            <option value="">All Priorities</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="categoryFilter">Category</label>
                        <select id="categoryFilter" class="form-control">
                            <option value="">All Categories</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="assignedFilter">Assigned To</label>
                        <select id="assignedFilter" class="form-control">
                            <option value="">All Assignments</option>
                            <option value="unassigned">Unassigned</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <x-data-table title="Support Tickets" table-id="tickets-table" fetch-url="{{ route('admin.tickets.data') }}"
                :columns="['Ticket', 'Sender', 'Status', 'Priority', 'Category', 'Assigned To', 'Created', 'Actions']" :columns-config="[
                    ['data' => 'ticket_info', 'name' => 'ticket_number', 'orderable' => false],
                    ['data' => 'sender_info', 'name' => 'sender_name', 'orderable' => false],
                    ['data' => 'status_badge', 'name' => 'status'],
                    ['data' => 'priority_badge', 'name' => 'priority'],
                    ['data' => 'category', 'name' => 'category'],
                    ['data' => 'assigned_info', 'name' => 'assigned_to', 'orderable' => false],
                    ['data' => 'created_at', 'name' => 'created_at'],
                    ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
                ]" />
        </div>
    </div>

    @push('plugin-scripts')
        <script>
            $(document).on('TableReady', function() {
                $('#statusFilter, #priorityFilter, #categoryFilter, #assignedFilter').on('change', function() {
                    const status = $('#statusFilter').val();
                    const priority = $('#priorityFilter').val();
                    const category = $('#categoryFilter').val();
                    const assigned_to = $('#assignedFilter').val();

                    let url = '{{ route('admin.tickets.data') }}?';
                    const params = [];

                    if (status) params.push('status=' + status);
                    if (priority) params.push('priority=' + priority);
                    if (category) params.push('category=' + category);
                    if (assigned_to) params.push('assigned_to=' + assigned_to);

                    url += params.join('&');

                    if (window.ticketsTable) {
                        window.ticketsTable.ajax.url(url).load();
                    }
                });
            });

            $(document).ready(function() {
                refreshStats();
                loadCategories();
                loadAssignedUsers();
            });

            function refreshStats() {
                $.get('{{ route('admin.tickets.stats') }}', function(response) {
                    if (response.success) {
                        const stats = response.data;
                        const statsHtml = `
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Tickets</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${stats.total || 0}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Open Tickets</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${stats.open || 0}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-folder-open fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${stats.pending || 0}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Urgent</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${stats.urgent || 0}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                        $('#statsCards').html(statsHtml);
                    }
                });
            }

            function loadCategories() {
                $.get('{{ route('admin.ticket_categories.data') }}', function(response) {
                    if (response.data) {
                        let options = '<option value="">All Categories</option>';
                        response.data.forEach(function(category) {
                            options += `<option value="${category.slug}">${category.name}</option>`;
                        });
                        $('#categoryFilter').html(options);
                    }
                });
            }

            function loadAssignedUsers() {
                $.get('{{ route('admin.users.data') }}', function(response) {
                    if (response.data) {
                        let options =
                            '<option value="">All Assignments</option><option value="unassigned">Unassigned</option>';
                        response.data.forEach(function(user) {
                            options += `<option value="${user.id}">${user.name}</option>`;
                        });
                        $('#assignedFilter').html(options);
                    }
                });
            }

            function deleteTicket(id) {
                if (confirm('Are you sure you want to delete this ticket?')) {
                    $.ajax({
                        url: `{{ route('admin.tickets.destroy', ':id') }}`.replace(':id', id),
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                window['ticketsTable'].ajax.reload();
                                showToast('success', response.message || 'Ticket deleted successfully');
                            } else {
                                showToast('error', response.message || 'Failed to delete ticket');
                            }
                        },
                        error: function() {
                            showToast('error', 'An error occurred while deleting the ticket');
                        }
                    });
                }
            }

            function viewTicket(id) {
                window.location.href = `{{ route('admin.tickets.show', ':id') }}`.replace(':id', id);
            }
        </script>
    @endpush
@endsection
