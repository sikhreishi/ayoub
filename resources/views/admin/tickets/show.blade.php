@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Ticket #{{ $ticket->ticket_number }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">Tickets</a></li>
                        <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Tickets
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ $ticket->title }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Status:</strong>
                                <span id="statusBadge"
                                    class="badge badge-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'pending' ? 'warning' : ($ticket->status === 'in_progress' ? 'info' : ($ticket->status === 'resolved' ? 'primary' : 'secondary'))) }} ml-2">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Priority:</strong>
                                <span id="priorityBadge"
                                    class="badge badge-{{ $ticket->priority === 'low' ? 'success' : ($ticket->priority === 'medium' ? 'warning' : ($ticket->priority === 'high' ? 'danger' : 'dark')) }} ml-2">{{ ucfirst($ticket->priority) }}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Category:</strong>
                                @if ($ticket->ticketCategory)
                                    <span class="badge"
                                        style="background-color: {{ $ticket->ticketCategory->color }}; color: white;">
                                        {{ $ticket->ticketCategory->name }}
                                    </span>
                                @else
                                    <span class="text-muted">Uncategorized</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <strong>Created:</strong> {{ $ticket->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <strong>Description:</strong>
                            <div class="mt-2 p-3 bg-light rounded">
                                {!! nl2br(e($ticket->description)) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Conversation</h5>
                        <div id="typingIndicator" class="text-muted small" style="display: none;">
                            <i class="fas fa-circle-notch fa-spin"></i> Someone is typing...
                        </div>
                    </div>
                    <div class="card-body" id="repliesContainer" style="max-height: 500px; overflow-y: auto;">
                        @foreach ($ticket->replies as $reply)
                            @php
                                $isCurrentUser =
                                    $reply->replier_type === 'App\Models\User' && $reply->replier_id === auth()->id();
                            @endphp
                            <div class="message-wrapper {{ $isCurrentUser ? 'message-right' : 'message-left' }} mb-3"
                                data-reply-id="{{ $reply->id }}">
                                <div
                                    class="reply-item {{ $reply->is_internal ? 'internal-reply' : '' }} {{ $isCurrentUser ? 'current-user-reply' : 'other-user-reply' }}">
                                    <div
                                        class="d-flex {{ $isCurrentUser ? 'justify-content-end' : 'justify-content-start' }} align-items-start">
                                        @if (!$isCurrentUser)
                                            <div class="avatar-circle mr-3">
                                                {{ substr($reply->replier_name ?? 'U', 0, 1) }}
                                            </div>
                                        @endif
                                        <div class="{{ $isCurrentUser ? 'text-right' : 'text-left' }}">
                                            <div
                                                class="message-header {{ $isCurrentUser ? 'justify-content-end' : 'justify-content-start' }} d-flex align-items-center">
                                                <strong class="{{ $isCurrentUser ? 'text-white' : '' }}">
                                                    {{ $isCurrentUser ? 'You' : $reply->replier_name ?? 'Unknown' }}
                                                </strong>
                                                @if ($reply->is_internal)
                                                    <span class="badge badge-warning badge-sm ml-2">Internal</span>
                                                @endif
                                            </div>
                                            <div class="text-muted small {{ $isCurrentUser ? 'text-light' : '' }}">
                                                {{ $reply->created_at->format('M d, Y H:i') }}
                                            </div>
                                        </div>
                                        @if ($isCurrentUser)
                                            <div class="avatar-circle ml-3 current-user-avatar">
                                                {{ substr(auth()->user()->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div
                                        class="reply-content mt-2 {{ $isCurrentUser ? 'current-user-content' : 'other-user-content' }}">
                                        {!! nl2br(e($reply->message)) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="card-footer">
                        <form id="replyForm">
                            @csrf
                            <div class="form-group">
                                <label for="replyMessage">Add Reply</label>
                                <textarea id="replyMessage" name="message" class="form-control" rows="4" required
                                    placeholder="Type your message here..."></textarea>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="isInternal" name="is_internal">
                                <label class="form-check-label" for="isInternal">
                                    Internal note (not visible to customer)
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-reply"></i> Send Reply
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Sender Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Name:</strong> {{ $ticket->sender_name }}
                        </div>
                        @if ($ticket->sender_email)
                            <div class="mb-2">
                                <strong>Email:</strong>
                                <a href="mailto:{{ $ticket->sender_email }}">{{ $ticket->sender_email }}</a>
                            </div>
                        @endif
                        @if ($ticket->sender_phone)
                            <div class="mb-2">
                                <strong>Phone:</strong> {{ $ticket->sender_phone }}
                            </div>
                        @endif
                        <div class="mb-2">
                            <strong>Type:</strong>
                            {{ $ticket->sender_type ? class_basename($ticket->sender_type) : 'Guest' }}
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="statusSelect">Update Status</label>
                            <select id="statusSelect" class="form-control">
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In
                                    Progress</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved
                                </option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="assignSelect">Assign To</label>
                            <select id="assignSelect" class="form-control">
                                <option value="">Unassigned</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="prioritySelect">Priority</label>
                            <select id="prioritySelect" class="form-control">
                                <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium
                                </option>
                                <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="categorySelect">Category</label>
                            <select id="categorySelect" class="form-control">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $ticket->ticket_category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-circle text-success" id="connectionStatus"></i>
                            Real-time Status
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            <div>Connected: <span id="connectionText" class="text-success">Yes</span></div>
                            <div>Last Update: <span id="lastUpdate">-</span></div>
                            <div>Active Users: <span id="activeUsers">1</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('plugin-scripts')
        <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
        <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
        @include('admin.tickets.js.scrept')
    @endpush

    @push('styles')
        @include('admin.tickets.css.style')
    @endpush
@endsection
