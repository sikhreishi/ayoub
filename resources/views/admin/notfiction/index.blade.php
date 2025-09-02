
@push('plugin-scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.responsive.min.js"></script>
@endpush
@push('plugin-styles')
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.5/css/responsive.dataTables.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #e3f0ff 0%, #f8fafd 100%);
            min-height: 100vh;
        }
        .notification-container {
            background: #181f4a;
            border-radius: 1.5rem;
            box-shadow: 0 4px 32px rgba(80,120,200,0.10);
            padding: 2.5rem 2.5rem 1.5rem 2.5rem;
            margin-top: 2.5rem;
            max-width: 900px;
        }
        .user-checkbox {
            transition: background 0.2s, border 0.2s;
            border-radius: 0.7rem;
            padding: 0.4rem 0.7rem;
            margin-bottom: 0.3rem;
            border: 2px solid transparent;
            cursor: pointer;
        }
        .user-checkbox.selected {
            background: #181f4a;
            border-color: #0d6efd;
        }
        .form-check-input:checked {
            border-color: #0d6efd;
            background-color: #0d6efd;
            box-shadow: 0 0 0 0.15rem #0d6efd33;
        }
        .modal-content {
            border-radius: 1.2rem;
        }
        .modal-header {
            border-top-left-radius: 1.2rem;
            border-top-right-radius: 1.2rem;
            background: linear-gradient(90deg, #0d6efd 60%, #39c0ed 100%);
        }
        .modal-title i {
            margin-right: 8px;
        }
        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            border: 1.5px solid #b6d4fe;
            background: #fff;
        }
        .btn-success, .btn-primary {
            min-width: 110px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn-success:hover, .btn-primary:hover {
            box-shadow: 0 2px 8px #0d6efd33;
            filter: brightness(1.08);
        }
        .gap-2 > * { margin-right: 0.5rem; }
        #userSearch {
            background-image: url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/icons/search.svg');
            background-repeat: no-repeat;
            background-position: 98% 50%;
            background-size: 18px 18px;
            padding-right: 2.2rem;
        }
        .user-count-badge {
            background: #0d6efd;
            color: #fff;
            border-radius: 1rem;
            padding: 0.2rem 0.7rem;
            font-size: 0.95rem;
            margin-left: 0.7rem;
            vertical-align: middle;
        }
    </style>
@endpush
@extends('layouts.app')
@section('content')
<div class="container notification-container">
    <h2 class="mb-4 text-primary"><i class="bi bi-megaphone"></i> Send Notification to Users</h2>
    <form id="notificationForm" method="POST" action="#" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="fw-bold mb-2">
                Select Users
                <span class="text-muted" style="font-weight:normal;">(leave empty to send to all)</span>
                <span class="user-count-badge" id="selectedCount">0 Selected</span>
            </label>
            <div class="mb-2 d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllBtn">Select All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn">Deselect All</button>
                <button type="button" class="btn btn-sm btn-outline-info" id="selectAdminsBtn">Select Admins</button>
                <button type="button" class="btn btn-sm btn-outline-info" id="selectUsersBtn">Select Users</button>
                <button type="button" class="btn btn-sm btn-outline-info" id="selectDriversBtn">Select Drivers</button>
                <input type="text" class="form-control form-control-sm ms-auto" id="userSearch" placeholder="Search user..." style="max-width: 200px;">
            </div>
            <div class="form-control p-2 bg-light" style="height:auto; max-height:300px; overflow-y:auto; border: 1px solid #b6d4fe;">
                <div class="row">
                    @foreach($users as $user)
                        <div class="col-12 col-md-6">
                            <div class="form-check user-checkbox d-flex align-items-center" data-name="{{ strtolower($user->name) }}" data-phone="{{ $user->phone }}" data-role="{{ $user->roles->first()?->name ?? '' }}">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/'.$user->avatar) }}" class="avatar" alt="avatar">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff&size=32" class="avatar" alt="avatar">
                                @endif
                                <input class="form-check-input ms-2" type="checkbox" name="users[]" value="{{ $user->id }}" id="user_{{ $user->id }}">
                                <label class="form-check-label ms-2" for="user_{{ $user->id }}">
                                    {{ $user->name }} ({{ $user->phone }})
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="noResults" class="text-center text-muted py-3" style="display:none;">
                    No users found.
                </div>
            </div>
            <small class="text-muted">If you don't select any user, the notification will be sent to all users.</small>
            <div class="mt-3 text-end">
                <button type="button" class="btn btn-success" id="openMessageModal">Write Message</button>
            </div>
        </div>

    </form>
    <div id="result" class="mt-3"></div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius: 1rem;">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="messageModalLabel">
            <i class="bi bi-send"></i> Send Notification
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Send Method -->
        <div class="mb-3">
            <label class="form-label">Send Method:</label>
            <select class="form-select" name="method" id="modalMethod" required>
                <option value="firebase">Firebase (Push Notification)</option>
                <option value="email">Email</option>
            </select>
        </div>
        <!-- Firebase Fields -->
        <div id="firebaseFields">
            <div class="mb-3">
                <label>Title (Arabic):</label>
                <input type="text" class="form-control" name="title_ar" id="modalTitleAr">
            </div>
            <div class="mb-3">
                <label>Body (Arabic):</label>
                <textarea class="form-control" name="body_ar" id="modalBodyAr"></textarea>
            </div>
            <div class="mb-3">
                <label>Title (English):</label>
                <input type="text" class="form-control" name="title_en" id="modalTitleEn">
            </div>
            <div class="mb-3">
                <label>Body (English):</label>
                <textarea class="form-control" name="body_en" id="modalBodyEn"></textarea>
            </div>
        </div>
        <!-- Email Fields -->
        <div id="emailFields" style="display:none;">
            <div class="mb-3">
                <label>Title:</label>
                <input type="text" class="form-control" name="email_title" id="modalEmailTitle">
            </div>
            <div class="mb-3">
                <label>Message:</label>
                <textarea class="form-control" name="email_body" id="modalEmailBody"></textarea>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="sendNotificationBtn">Send</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('plugin-scripts')
<script>
    // Highlight selected users and update count
    function updateSelectedCount() {
        let count = $('.user-checkbox .form-check-input:checked').length;
        $('#selectedCount').text(count + ' Selected');
        $('.user-checkbox').removeClass('selected');
        $('.user-checkbox .form-check-input:checked').each(function() {
            $(this).closest('.user-checkbox').addClass('selected');
        });
    }
    $(document).on('change', '.user-checkbox .form-check-input', updateSelectedCount);
    $(document).ready(updateSelectedCount);

    // Select All
    $('#selectAllBtn').on('click', function() {
        $('.user-checkbox:visible .form-check-input').prop('checked', true).trigger('change');
    });
    // Deselect All
    $('#deselectAllBtn').on('click', function() {
        $('.user-checkbox:visible .form-check-input').prop('checked', false).trigger('change');
    });
    // Select by Role (TOGGLE)
    function toggleRoleSelection(role) {
        var anyUnchecked = false;
        $('.user-checkbox:visible').each(function() {
            if ($(this).data('role') === role && !$(this).find('.form-check-input').prop('checked')) {
                anyUnchecked = true;
            }
        });
        $('.user-checkbox:visible').each(function() {
            if ($(this).data('role') === role) {
                $(this).find('.form-check-input').prop('checked', anyUnchecked).trigger('change');
            }
        });
    }
    $('#selectAdminsBtn').on('click', function() {
        toggleRoleSelection('admin');
    });
    $('#selectUsersBtn').on('click', function() {
        toggleRoleSelection('user');
    });
    $('#selectDriversBtn').on('click', function() {
        toggleRoleSelection('driver');
    });
    // Search
    $('#userSearch').on('keyup', function() {
        let val = $(this).val().toLowerCase();
        let found = false;
        $('.user-checkbox').each(function() {
            let name = $(this).data('name');
            let phone = String($(this).data('phone'));
            if (name.includes(val) || phone.includes(val)) {
                $(this).closest('.col-12, .col-md-6').show();
                found = true;
            } else {
                $(this).closest('.col-12, .col-md-6').hide();
            }
        });
        if (found) {
            $('#noResults').hide();
        } else {
            $('#noResults').show();
        }
    });

    // Modal show/hide logic
    $('#openMessageModal').on('click', function() {
        $('#messageModal').modal('show');
    });

    // Toggle fields based on method
    $('#modalMethod').on('change', function() {
        if($(this).val() === 'firebase') {
            $('#firebaseFields').show();
            $('#emailFields').hide();
        } else {
            $('#firebaseFields').hide();
            $('#emailFields').show();
        }
    });

    // Send notification
    $('#sendNotificationBtn').on('click', function() {
        let $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Sending...');
        let method = $('#modalMethod').val();
        let users = [];
        $('.user-checkbox .form-check-input:checked').each(function() {
            users.push($(this).val());
        });

        let data = { users: users, method: method, _token: '{{ csrf_token() }}' };

        if(method === 'firebase') {
            data.title_ar = $('#modalTitleAr').val();
            data.body_ar = $('#modalBodyAr').val();
            data.title_en = $('#modalTitleEn').val();
            data.body_en = $('#modalBodyEn').val();
            var url = '{{ route("admin.notfiction.send.firebase") }}';
        } else {
            data.title = $('#modalEmailTitle').val();
            data.body = $('#modalEmailBody').val();
            var url = '{{ route("admin.notfiction.send.email") }}';
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(response) {
                $('#messageModal').modal('hide');
                $('#result').html('<div class="alert alert-success mt-3">Sent successfully!</div>');
                setTimeout(function() {
                    $('.alert').fadeOut(500, function() { $(this).remove(); });
                }, 4000);
            },
            error: function(xhr) {
                let errorHtml = '<div class="alert alert-danger mt-3">';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorHtml += '<ul class="mb-0">';
                    $.each(xhr.responseJSON.errors, function(key, errors) {
                        $.each(errors, function(i, err) {
                            errorHtml += `<li>${err}</li>`;
                        });
                    });
                    errorHtml += '</ul>';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorHtml += xhr.responseJSON.message;
                } else {
                    errorHtml += 'An unknown error occurred!';
                }
                errorHtml += '</div>';
                $('#result').html(errorHtml);
                setTimeout(function() {
                    $('.alert').fadeOut(500, function() { $(this).remove(); });
                }, 4000);
            },
            complete: function() {
                $btn.prop('disabled', false).html('Send');
            }
        });
    });
</script>
@endpush


