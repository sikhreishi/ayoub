<script>
    $(document).ready(function() {
        // Pagination variables
        let currentPage = 1;
        let isLoading = false;
        let hasMorePages = true;
        let totalNotifications = {{ auth()->user()->notifications->count() }};

        // Handle notification click to mark as read
        $(document).on('click', '.notification-item', function() {
            var $notification = $(this);
            var notificationId = $notification.data('id');

            // Immediately update UI to show as read
            $notification.removeClass('unread-notification firebase-notification');
            $notification.find('.notification-dot').remove();
            $notification.find('.notification-badge').remove();
            $notification.find('.notify-title').removeClass('fw-bold');
            $notification.find('.notify-desc').removeClass('text-dark fw-medium').addClass('text-muted');

            // Send AJAX request to mark as read on server
            $.ajax({
                type: "PUT",
                url: "/reade-notification",
                data: {
                    id: notificationId,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    var count = response.countUnreadMessages;
                    var $badge = $('#countUnreadMessages');
                    if (count > 0) {
                        $badge.text(count).addClass('badge-notify');
                    } else {
                        $badge.text('').removeClass('badge-notify');
                    }
                },
                error: function() {
                    // Revert UI changes if request failed
                    $notification.addClass('unread-notification');
                    $notification.find('.notify-title').addClass('fw-bold');
                    $notification.find('.notify-desc').removeClass('text-muted').addClass('text-dark fw-medium');
                }
            });
        });

        // Handle notification close button
        // $(document).on('click', '.notify-close', function(e) {
        //     e.preventDefault();
        //     e.stopPropagation();
        //     var $notification = $(this).closest('.notification-item').parent();
        //     $notification.fadeOut(300, function() {
        //         $(this).remove();
        //     });
        // });

        // Mark all as read functionality
        $(document).on('click', '[href="javascript:;"]', function(e) {
            var $link = $(this);
            if ($link.find('i').text() === 'done_all') {
                e.preventDefault();
                $('.unread-notification').each(function() {
                    $(this).trigger('click');
                });
            }
        });

        // Infinite scroll functionality
        $('.loder-notify').on('scroll', function() {
            const $container = $(this);
            const scrollTop = $container.scrollTop();
            const scrollHeight = $container[0].scrollHeight;
            const containerHeight = $container.height();

            // Check if user scrolled to bottom (with 50px threshold)
            if (scrollTop + containerHeight >= scrollHeight - 50 && !isLoading && hasMorePages) {
                loadMoreNotifications();
            }
        });

        // Function to load more notifications
        function loadMoreNotifications() {
            if (isLoading || !hasMorePages) return;

            isLoading = true;
            currentPage++;

            // Show loading indicator
            $('.notification-loading').removeClass('d-none');

            $.ajax({
                url: '/notifications/paginated',
                method: 'GET',
                data: {
                    page: currentPage,
                    per_page: 10
                },
                success: function(response) {
                    if (response.success && response.notifications.length > 0) {
                        // Append new notifications
                        response.notifications.forEach(function(notification) {
                            const notificationHtml = createNotificationHtml(notification);
                            $('#notificationsList').append(notificationHtml);
                        });

                        // Update pagination state
                        hasMorePages = response.has_more;

                        // Show "no more notifications" message if at end
                        if (!hasMorePages) {
                            $('.no-more-notifications').removeClass('d-none');
                        }
                    } else {
                        hasMorePages = false;
                        $('.no-more-notifications').removeClass('d-none');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading notifications:', xhr);
                    // Revert page counter on error
                    currentPage--;
                },
                complete: function() {
                    isLoading = false;
                    $('.notification-loading').addClass('d-none');
                }
            });
        }

        // Function to create notification HTML
        function createNotificationHtml(notification) {
            const isUnread = notification.read == 0;
            const unreadClass = isUnread ? 'unread-notification' : '';
            const titleClass = isUnread ? 'fw-bold' : '';
            const descClass = isUnread ? 'text-dark fw-medium' : 'text-muted';
            const notificationDot = isUnread ? `
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle notification-dot">
                    <span class="visually-hidden">New alerts</span>
                </span>
            ` : '';
            const notificationBadge = isUnread ? `
                <span class="badge bg-primary rounded-pill notification-badge">جديد</span>
            ` : '';

            return `
                <div>
                    <a class="dropdown-item border-bottom py-2 notification-item ${unreadClass}" href="javascript:;" data-id="${notification.id}">
                        <div class="d-flex align-items-center gap-3 position-relative">
                            <div class="position-relative">
                                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(notification.sender_name)}&background=random" width="45" height="45" alt="${notification.sender_name}" class="rounded-circle">
                                ${notificationDot}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="notify-title mb-1 ${titleClass}">${notification.title}</h5>
                                    ${notificationBadge}
                                </div>
                                <p class="mb-1 notify-desc ${descClass}">${notification.body}</p>
                                <p class="mb-0 notify-time text-muted small">
                                    <i class="material-icons-outlined fs-6 me-1">access_time</i>
                                    ${notification.created_at_human}
                                </p>
                            </div>
                        \
                        </div>
                    </a>
                </div>
            `;
        }

        // Reset pagination when dropdown is opened
        $('.dropdown-toggle[data-bs-toggle="dropdown"]').on('show.bs.dropdown', function() {
            // Reset state if needed
            if (currentPage === 1 && $('#notificationsList .notification-item').length <= 10) {
                hasMorePages = totalNotifications > 10;
                $('.no-more-notifications').addClass('d-none');
            }
        });
    });
</script>

<style>
/* Enhanced notification styles */
.notification-dot {
    width: 12px;
    height: 12px;
    animation: pulse 2s infinite;
}

.pulse-animation {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
}

.notification-badge {
    font-size: 10px;
    padding: 2px 6px;
    animation: fadeInBounce 0.5s ease-out;
}

.firebase-badge {
    background: linear-gradient(45deg, #28a745, #20c997) !important;
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
}

@keyframes fadeInBounce {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 1;
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.unread-notification {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.05), rgba(13, 202, 240, 0.05));
    border-left: 3px solid #0d6efd;
    transition: all 0.3s ease;
}

.unread-notification:hover {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(13, 202, 240, 0.1));
    transform: translateX(2px);
}

.firebase-notification {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.05), rgba(32, 201, 151, 0.05));
    border-left: 3px solid #28a745;
}

.new-notification-highlight {
    animation: highlightNew 3s ease-out;
}

@keyframes highlightNew {
    0% {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.2), rgba(32, 201, 151, 0.2));
        transform: scale(1.02);
    }
    100% {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.05), rgba(32, 201, 151, 0.05));
        transform: scale(1);
    }
}

.notify-close {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.notification-item:hover .notify-close {
    opacity: 1;
}

.notify-close:hover {
    background: rgba(220, 53, 69, 0.1);
    border-radius: 50%;
    color: #dc3545 !important;
}

.dropdown-notify {
    width: 380px;
    max-height: 500px;
    overflow-y: auto;
}

.badge-notify {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 11px;
    min-width: 18px;
    text-align: center;
    animation: bounceIn 0.5s ease-out;
}

@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 1;
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
