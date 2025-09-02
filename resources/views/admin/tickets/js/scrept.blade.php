<script>
    const ticketFirebaseConfig = {
        apiKey: "AIzaSyDwH1kfiDSLvjI4V4UxLqZQnIyGH87MBzw",
        authDomain: "waddini-ccbc7.firebaseapp.com",
        databaseURL: "https://waddini-ccbc7-default-rtdb.asia-southeast1.firebasedatabase.app",
        projectId: "waddini-ccbc7",
        storageBucket: "waddini-ccbc7.firebasestorage.app",
        messagingSenderId: "823593320488",
        appId: "1:823593320488:web:d17d3a8ec271fd6a85b51d",
        measurementId: "G-8KDJ0T6YXF"
    };

    let ticketFirebaseApp;
    if (!firebase.apps.length) {
        ticketFirebaseApp = firebase.initializeApp(ticketFirebaseConfig);
    } else {
        ticketFirebaseApp = firebase.app();
    }

    const ticketDatabase = firebase.database();
    let currentUserId = {{ auth()->id() }};
    let ticketId = {{ $ticket->id }};
    let existingReplies = new Set();
    let isTyping = false;
    let typingTimeout;
    let pageLoadTime = Math.floor(Date.now() / 1000);

    $(document).ready(function() {
        console.log('Page loaded, setting up listeners for ticket:', ticketId);

        $('[data-reply-id]').each(function() {
            const replyId = $(this).data('reply-id');
            if (replyId) {
                existingReplies.add(replyId.toString());
                console.log('Existing reply marked:', replyId);
            }
        });

        setupIncomingMessageListener();
        setupRealtimeListeners();
        setupConnectionStatus();
        setupTypingIndicator();

        $('#replyForm').submit(function(e) {
            e.preventDefault();
            addReply();
        });

        $('#statusSelect').change(function() {
            updateTicketStatus($(this).val());
        });

        $('#assignSelect').change(function() {
            assignTicket($(this).val());
        });

        $('#prioritySelect').change(function() {
            updateTicketPriority($(this).val());
        });

        $('#categorySelect').change(function() {
            updateTicketCategory($(this).val());
        });

        scrollToBottom();

        if (Notification.permission === 'default') {
            Notification.requestPermission().then(function(permission) {});
        }
    });

    function setupRealtimeListeners() {
        const repliesRef = ticketDatabase.ref('tickets/' + ticketId + '/replies');

        repliesRef.on('child_added', function(snapshot) {
            const reply = snapshot.val();
            if (reply && reply.id && !existingReplies.has(reply.id.toString())) {
                existingReplies.add(reply.id.toString());
                const replyTime = reply.timestamp || reply.created_at || 0;
                if (replyTime >= pageLoadTime && reply.replier_id != currentUserId) {
                    addReplyToDOM(reply);
                    if (reply.replier_id != currentUserId) {
                        showNewMessageNotification(reply);
                        playNotificationSound();
                        updateNotificationCount();
                    }
                    $('#lastUpdate').text(new Date().toLocaleTimeString());
                }
            }
        });
    }

    function setupIncomingMessageListener(ticketId) {
        const repliesRef = ticketDatabase.ref('tickets/' + ticketId + '/replies');
        repliesRef.on('child_added', function(snapshot) {
            const reply = snapshot.val();
            if (reply.replier_id !== currentUserId) {
                addReplyToDOM(reply);
                showNewMessageNotification(reply);
                playNotificationSound();
                updateNotificationCount();
            }
        });
    }

    function setupConnectionStatus() {
        const connectedRef = ticketDatabase.ref('.info/connected');
        connectedRef.on('value', function(snapshot) {
            const isConnected = snapshot.val();
            if (isConnected === true) {
                $('#connectionStatus').removeClass('text-danger').addClass('text-success');
                $('#connectionText').text('Yes').removeClass('text-danger').addClass('text-success');
            } else {
                $('#connectionStatus').removeClass('text-success').addClass('text-danger');
                $('#connectionText').text('No').removeClass('text-success').addClass('text-danger');
            }
        });
    }

    function setupTypingIndicator() {
        const typingRef = ticketDatabase.ref('tickets/' + ticketId + '/typing');

        typingRef.on('value', function(snapshot) {
            const typingData = snapshot.val();
            if (typingData) {
                const typingUsers = Object.keys(typingData).filter(userId =>
                    userId != currentUserId && typingData[userId] === true
                );
                if (typingUsers.length > 0) {
                    $('#typingIndicator').show();
                } else {
                    $('#typingIndicator').hide();
                }
            } else {
                $('#typingIndicator').hide();
            }
        });

        $('#replyMessage').on('input', function() {
            if (!isTyping) {
                isTyping = true;
                ticketDatabase.ref('tickets/' + ticketId + '/typing/' + currentUserId).set(true);
            }
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                isTyping = false;
                ticketDatabase.ref('tickets/' + ticketId + '/typing/' + currentUserId).remove();
            }, 2000);
        });
    }

    function addReplyToDOM(reply) {
        const isCurrentUser = reply.replier_id == currentUserId;
        const isInternal = reply.is_internal;
        const replyHtml = `
            <div class="message-wrapper ${isCurrentUser ? 'message-right' : 'message-left'} mb-3 new-message" data-reply-id="${reply.id}">
                <div class="reply-item ${isInternal ? 'internal-reply' : ''} ${isCurrentUser ? 'current-user-reply' : 'other-user-reply'}">
                    <div class="d-flex ${isCurrentUser ? 'justify-content-end' : 'justify-content-start'} align-items-start">
                        ${!isCurrentUser ? `<div class="avatar-circle mr-3">${reply.replier_name.charAt(0)}</div>` : ''}
                        <div class="${isCurrentUser ? 'text-right' : 'text-left'}">
                            <div class="message-header ${isCurrentUser ? 'justify-content-end' : 'justify-content-start'} d-flex align-items-center">
                                <strong class="${isCurrentUser ? 'text-white' : ''}">
                                    ${isCurrentUser ? 'You' : reply.replier_name}
                                </strong>
                                ${isInternal ? '<span class="badge badge-warning badge-sm ml-2">Internal</span>' : ''}
                            </div>
                            <div class="text-muted small ${isCurrentUser ? 'text-light' : ''}">
                                ${reply.created_at_human || 'just now'}
                            </div>
                        </div>
                        ${isCurrentUser ? `<div class="avatar-circle ml-3 current-user-avatar">${reply.replier_name.charAt(0)}</div>` : ''}
                    </div>
                    <div class="reply-content mt-2 ${isCurrentUser ? 'current-user-content' : 'other-user-content'}">
                        ${reply.message.replace(/\n/g, '<br>')}
                    </div>
                </div>
            </div>
        `;

        const $newReply = $(replyHtml);
        $newReply.hide();
        $('#repliesContainer').append($newReply);
        $newReply.fadeIn(300);

        setTimeout(() => {
            scrollToBottom();
            $newReply.removeClass('new-message');
        }, 100);
    }

    function scrollToBottom() {
        const container = $('#repliesContainer');
        if (container.length) {
            container.animate({
                scrollTop: container[0].scrollHeight
            }, 300);
        }
    }

    function showNewMessageNotification(reply) {
        if (Notification.permission === 'granted') {
            const notification = new Notification(`New message from ${reply.replier_name}`, {
                body: reply.message.substring(0, 100) + (reply.message.length > 100 ? '...' : ''),
                icon: '/favicon.ico',
                tag: 'ticket-reply-' + reply.id,
                requireInteraction: false
            });

            setTimeout(() => {
                notification.close();
            }, 5000);
        }

        showToast('info', `New message from ${reply.replier_name}`, 8000);

        const originalTitle = document.title;
        if (!originalTitle.includes('🔔')) {
            document.title = '🔔 New Message - ' + originalTitle;
            let flashCount = 0;
            const flashInterval = setInterval(() => {
                document.title = flashCount % 2 === 0 ? '🔔 NEW MESSAGE!' : '🔔 New Message - ' + originalTitle;
                flashCount++;
                if (flashCount >= 6) {
                    clearInterval(flashInterval);
                    document.title = '🔔 New Message - ' + originalTitle;
                }
            }, 1000);
        }
    }

    function updateNotificationCount() {
        const $countElement = $('#countUnreadMessages');
        if ($countElement.length) {
            const current = parseInt($countElement.text()) || 0;
            $countElement.text(current + 1).addClass('badge-notify');
        }
    }

    function playNotificationSound() {
        try {
            const audio = new Audio(
                'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT'
            );
            audio.volume = 0.3;
            audio.play().catch(e => console.log('Audio play failed:', e));
        } catch (e) {
            console.log('Audio error:', e);
        }
    }

    function updateStatusBadge(status) {
        const statusColors = {
            'open': 'success',
            'pending': 'warning',
            'in_progress': 'info',
            'resolved': 'primary',
            'closed': 'secondary'
        };
        const color = statusColors[status] || 'secondary';
        const statusText = status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        $('#statusBadge').removeClass().addClass(`badge badge-${color} ml-2`).text(statusText);
    }

    function updatePriorityBadge(priority) {
        const priorityColors = {
            'low': 'success',
            'medium': 'warning',
            'high': 'danger',
            'urgent': 'dark'
        };
        const color = priorityColors[priority] || 'secondary';
        $('#priorityBadge').removeClass().addClass(`badge badge-${color} ml-2`).text(priority.charAt(0).toUpperCase() +
            priority.slice(1));
    }

    function addReply() {
        const message = $('#replyMessage').val().trim();
        const isInternal = $('#isInternal').is(':checked') ? 1 : 0;

        if (!message) {
            showToast('error', 'Please enter a message');
            return;
        }

        const submitBtn = $('#replyForm button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');

        ticketDatabase.ref('tickets/' + ticketId + '/typing/' + currentUserId).remove();

        $.ajax({
            url: '{{ route('admin.tickets.reply', $ticket->id) }}',
            method: 'POST',
            data: {
                message: message,
                is_internal: isInternal,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#replyMessage').val('');
                    $('#isInternal').prop('checked', false);

                    if (response.data && response.data.reply_id) {
                        existingReplies.add(response.data.reply_id.toString());
                        const reply = {
                            id: response.data.reply_id,
                            replier_id: currentUserId,
                            replier_name: '{{ auth()->user()->name }}',
                            message: message,
                            is_internal: isInternal,
                            created_at_human: 'just now'
                        };
                        addReplyToDOM(reply);
                        $('#lastUpdate').text(new Date().toLocaleTimeString());
                    }
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr) {
                console.error('Reply send error:', xhr);
                let errorMessage = 'Error sending message';
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                }
                showToast('error', errorMessage);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    }

    function updateTicketStatus(status) {
        $.ajax({
            url: '{{ route('admin.tickets.update-status', $ticket->id) }}',
            method: 'PUT',
            data: {
                status: status,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                }
            },
            error: function() {
                showToast('error', 'Error updating status');
            }
        });
    }

    function assignTicket(userId) {
        $.ajax({
            url: '{{ route('admin.tickets.assign', $ticket->id) }}',
            method: 'PUT',
            data: {
                assigned_to: userId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                }
            },
            error: function() {
                showToast('error', 'Error assigning ticket');
            }
        });
    }

    function updateTicketPriority(priority) {
        $.ajax({
            url: '{{ route('admin.tickets.update-priority', $ticket->id) }}',
            method: 'PUT',
            data: {
                priority: priority,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                }
            },
            error: function() {
                showToast('error', 'Error updating priority');
            }
        });
    }

    function updateTicketCategory(categoryId) {
        $.ajax({
            url: '{{ route('admin.tickets.update-category', $ticket->id) }}',
            method: 'PUT',
            data: {
                category_id: categoryId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                }
            },
            error: function() {
                showToast('error', 'Error updating category');
            }
        });
    }

    $(window).on('beforeunload', function() {
        ticketDatabase.ref('tickets/' + ticketId + '/typing/' + currentUserId).remove();
        ticketDatabase.ref('tickets/' + ticketId).off();
    });

    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            document.title = document.title.replace('🔔 New Message - ', '').replace('🔔 NEW MESSAGE!', '');
        }
    });

    window.showToast = function(type, message, duration = 5000) {
        const toast = $(`
            <div class="alert alert-${type === 'error' ? 'danger' : (type === 'success' ? 'success' : 'info')} alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;">
                <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);
        $('body').append(toast);
        setTimeout(() => {
            toast.fadeOut(() => toast.remove());
        }, duration);
    };

    $(document).ready(function() {
        setTimeout(() => {
            ticketDatabase.ref('.info/connected').once('value').then(snapshot => {}).catch(error => {
                console.error('Firebase connection test failed:', error);
            });
        }, 2000);
    });
</script>
