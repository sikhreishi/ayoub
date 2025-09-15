@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ __('dashboard.ticket_detail.title', ['ticket_number' => $ticket->ticket_number]) }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">{{ __('dashboard.ticket_detail.breadcrumb.tickets') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('dashboard.ticket_detail.breadcrumb.current_ticket') }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('dashboard.ticket_detail.back_to_tickets') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Ticket Info Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ $ticket->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('dashboard.ticket_detail.status') ?? 'Status' }}:</strong>
                            <span id="statusBadge"
                                class="badge badge-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'pending' ? 'warning' : ($ticket->status === 'in_progress' ? 'info' : ($ticket->status === 'resolved' ? 'primary' : 'secondary'))) }} ml-2">
                                {{ __('dashboard.ticket_detail.status_badges.' . $ticket->status) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('dashboard.ticket_detail.priority') }}:</strong>
                            <span id="priorityBadge"
                                class="badge badge-{{ $ticket->priority === 'low' ? 'success' : ($ticket->priority === 'medium' ? 'warning' : ($ticket->priority === 'high' ? 'danger' : 'dark')) }} ml-2">
                                {{ __('dashboard.ticket_detail.priority_badges.' . $ticket->priority) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('dashboard.ticket_detail.category') }}:</strong>
                              @if ($ticket->ticketCategory)
                                <span class="badge" style="background-color: {{ $ticket->ticketCategory->color }}; color: white;">
                                    {{ $ticket->ticketCategory->name }}
                                </span>
                            @else
                                <span class="text-muted">{{ __('dashboard.ticket_detail.uncategorized') }}</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('dashboard.ticket_detail.created_at') ?? 'Created' }}:</strong> {{ $ticket->created_at->format('M d, Y H:i') }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('dashboard.ticket_detail.description') ?? 'Description' }}:</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            {!! nl2br(e($ticket->description)) !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conversation Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('dashboard.ticket_detail.conversation') }}</h5>
                    <div id="typingIndicator" class="text-muted small" style="display: none;">
                        <i class="fas fa-circle-notch fa-spin"></i> {{ __('dashboard.ticket_detail.typing_indicator') }}
                    </div>
                </div>
                <div class="card-body" id="repliesContainer" style="max-height: 500px; overflow-y: auto;">
                    @foreach ($ticket->replies as $reply)
                        @php
                            $isCurrentUser = $reply->replier_type === 'App\Models\User' && $reply->replier_id === auth()->id();
                        @endphp
                        <div class="message-wrapper {{ $isCurrentUser ? 'message-right' : 'message-left' }} mb-3" data-reply-id="{{ $reply->id }}">
                            <div class="reply-item {{ $reply->is_internal ? 'internal-reply' : '' }} {{ $isCurrentUser ? 'current-user-reply' : 'other-user-reply' }}">
                                <div class="d-flex {{ $isCurrentUser ? 'justify-content-end' : 'justify-content-start' }} align-items-start">
                                    @if (!$isCurrentUser)
                                        <div class="avatar-circle mr-3">
                                            {{ substr($reply->replier_name ?? __('dashboard.ticket_detail.unknown'), 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="{{ $isCurrentUser ? 'text-right' : 'text-left' }}">
                                        <div class="message-header {{ $isCurrentUser ? 'justify-content-end' : 'justify-content-start' }} d-flex align-items-center">
                                            <strong class="{{ $isCurrentUser ? 'text-white' : '' }}">
                                                {{ $isCurrentUser ? __('dashboard.ticket_detail.you') : $reply->replier_name ?? __('tickets.ticket_detail.unknown') }}
                                            </strong>
                                            @if ($reply->is_internal)
                                                <span class="badge badge-warning badge-sm ml-2">{{ __('dashboard.ticket_detail.internal_note') }}</span>
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
                                <div class="reply-content mt-2 {{ $isCurrentUser ? 'current-user-content' : 'other-user-content' }}">
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
                            <label for="replyMessage">{{ __('dashboard.ticket_detail.add_reply') }}</label>
                            <textarea id="replyMessage" name="message" class="form-control" rows="4" required placeholder="{{ __('dashboard.ticket_detail.add_reply') }}"></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="isInternal" name="is_internal">
                            <label class="form-check-label" for="isInternal">{{ __('dashboard.ticket_detail.internal_note') }}</label>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-reply"></i> {{ __('dashboard.ticket_detail.send_reply') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Sender Info Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('dashboard.ticket_detail.sender_info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>{{ __('dashboard.tickets.name') ?? 'Name' }}:</strong> {{ $ticket->sender_name }}</div>
                    @if ($ticket->sender_email)
                        <div class="mb-2"><strong>{{ __('dashboard.tickets.email') ?? 'Email' }}:</strong>
                            <a href="mailto:{{ $ticket->sender_email }}">{{ $ticket->sender_email }}</a>
                        </div>
                    @endif
                    @if ($ticket->sender_phone)
                        <div class="mb-2"><strong>{{ __('dashboard.tickets.phone') ?? 'Phone' }}:</strong> {{ $ticket->sender_phone }}</div>
                    @endif
                    <div class="mb-2"><strong>{{ __('dashboard.tickets.role') ?? 'Type' }}:</strong>
                        {{ $ticket->sender_type ? class_basename($ticket->sender_type) : 'Guest' }}
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card mb-4">
                <div class="card-header"><h6 class="mb-0">{{ __('dashboard.ticket_detail.actions') }}</h6></div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="statusSelect">{{ __('dashboard.ticket_detail.update_status') }}</label>
                        <select id="statusSelect" class="form-control">
                            @foreach(['open','pending','in_progress','resolved','closed'] as $status)
                                <option value="{{ $status }}" {{ $ticket->status === $status ? 'selected' : '' }}>
                                    {{ __('dashboard.ticket_detail.status_badges.'.$status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="assignSelect">{{ __('dashboard.ticket_detail.assign_to') }}</label>
                        <select id="assignSelect" class="form-control">
                            <option value="">{{ __('dashboard.ticket_detail.unassigned') ?? 'Unassigned' }}</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="prioritySelect">{{ __('dashboard.ticket_detail.priority') }}</label>
                        <select id="prioritySelect" class="form-control">
                            @foreach(['low','medium','high','urgent'] as $priority)
                                <option value="{{ $priority }}" {{ $ticket->priority === $priority ? 'selected' : '' }}>
                                    {{ __('dashboard.ticket_detail.priority_badges.'.$priority) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="categorySelect">{{ __('dashboard.ticket_detail.category') }}</label>
                        <select id="categorySelect" class="form-control">
                            <option value="">{{ __('dashboard.ticket_detail.select_category') ?? 'Select Category' }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $ticket->ticket_category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Real-time Status Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-circle text-success" id="connectionStatus"></i>
                        {{ __('dashboard.ticket_detail.real_time_status') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div>{{ __('dashboard.ticket_detail.connected') }}: <span id="connectionText" class="text-success">Yes</span></div>
                        <div>{{ __('dashboard.ticket_detail.last_update') }}: <span id="lastUpdate">-</span></div>
                        <div>{{ __('dashboard.ticket_detail.active_users') }}: <span id="activeUsers">1</span></div>
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
