  <script>
        $(document).ready(function() {
            // Select All Roles
            $('#selectAllRoles').on('click', function() {
                $('.role-checkbox').prop('checked', true);
                updateRoleCards();
                updatePermissionsPreview();
            });

            // Deselect All Roles
            $('#deselectAllRoles').on('click', function() {
                $('.role-checkbox').prop('checked', false);
                updateRoleCards();
                updatePermissionsPreview();
            });

            // Role checkbox change
            $('.role-checkbox').on('change', function() {
                updateRoleCards();
                updatePermissionsPreview();
            });

            // Role card click (toggle checkbox)
            $('.role-card').on('click', function(e) {
                if (!$(e.target).is('input, button, .btn')) {
                    const checkbox = $(this).find('.role-checkbox');
                    checkbox.prop('checked', !checkbox.prop('checked'));
                    updateRoleCards();
                    updatePermissionsPreview();
                }
            });

            // Update role card appearance
            function updateRoleCards() {
                $('.role-card').each(function() {
                    const checkbox = $(this).find('.role-checkbox');
                    if (checkbox.is(':checked')) {
                        $(this).addClass('selected');
                    } else {
                        $(this).removeClass('selected');
                    }
                });
            }

            // View role permissions
            $('.view-permissions').on('click', function(e) {
                e.stopPropagation();
                const roleName = $(this).data('role');

                $('#rolePermissionsModalLabel').html(
                    `<i class="material-icons-outlined me-2">security</i>Permissions for "${roleName}" Role`
                );
                $('#rolePermissionsContent').html(
                    '<div class="text-center"><i class="material-icons-outlined loading-icon">hourglass_empty</i><p>Loading permissions...</p></div>'
                );

                $('#rolePermissionsModal').modal('show');

                // Load permissions via AJAX
                $.get(`/dashboard/admin/roles/${roleName}/permissions-preview`)
                    .done(function(data) {
                        // Format the permissions data with improved styling
                        const formattedData = formatPermissionsData(data);
                        $('#rolePermissionsContent').html(formattedData);
                    })
                    .fail(function() {
                        $('#rolePermissionsContent').html(
                            '<div class="alert alert-danger">Failed to load permissions.</div>');
                    });
            });

            // Format permissions data with improved styling
            function formatPermissionsData(data) {
                // Ensure data is a string
                if (typeof data !== 'string') {
                    // If data is an object, try to convert it to a string or extract HTML content
                    if (data && typeof data === 'object') {
                        // If it has an html property, use that
                        if (data.html) {
                            data = data.html;
                        } else {
                            // Try to stringify the object, or provide a default message
                            try {
                                data = JSON.stringify(data);
                            } catch (e) {
                                return '<div class="text-center py-4"><i class="material-icons-outlined" style="font-size: 3rem; color: var(--text-muted);">error_outline</i><p class="mt-3">تعذر عرض بيانات الصلاحيات.</p></div>';
                            }
                        }
                    } else {
                        // For null, undefined or other non-string, non-object values
                        return '<div class="text-center py-4"><i class="material-icons-outlined" style="font-size: 3rem; color: var(--text-muted);">no_encryption</i><p class="mt-3">لم يتم العثور على صلاحيات لهذا الدور.</p></div>';
                    }
                }

                // Now that we've ensured data is a string, we can safely use string methods
                // If the data is already formatted, return it as is
                if (data.includes('permission-group')) {
                    return data;
                }

                // Check if the data is empty or just contains basic HTML
                if (!data || data.trim() === '' || data.includes('No permissions found')) {
                    return '<div class="text-center py-4"><i class="material-icons-outlined" style="font-size: 3rem; color: var(--text-muted);">no_encryption</i><p class="mt-3">لم يتم العثور على صلاحيات لهذا الدور.</p></div>';
                }

                // Enhanced styling for permission groups
                let enhancedData = data;

                // Replace basic headers with styled group headers
                enhancedData = enhancedData.replace(/<h6 class="text-primary"><i class="material-icons-outlined me-1" style="font-size: 16px;">folder<\/i>(.*?)\((\d+)\)<\/h6>/g,
                    '<div class="permission-group-header"><h5 class="permission-group-title"><i class="material-icons-outlined">folder</i>$1<span class="badge bg-primary ms-2">$2</span></h5></div>'
                );

                // Replace basic spans with styled permission items
                enhancedData = enhancedData.replace(/<span class="badge bg-light text-dark me-1 mb-1">(.*?)<\/span>/g,
                    '<div class="permission-item"><i class="material-icons-outlined permission-icon">check_circle</i><div class="permission-name">$1</div></div>'
                );

                // Wrap groups of permissions
                enhancedData = enhancedData.replace(
                    /<div class="mb-3">(.*?)<div class="ms-3">(.*?)<\/div><\/div>/gs,
                    '<div class="permission-group">$1<div class="permission-group-body">$2</div></div>'
                );

                return enhancedData;
            }

            // Update permissions preview
            function updatePermissionsPreview() {
                const selectedRoles = $('.role-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedRoles.length === 0) {
                    $('#currentPermissions').html(
                        '<div class="text-center text-muted py-4"><i class="material-icons-outlined" style="font-size: 3rem;">block</i><p class="mt-3">No roles selected</p></div>'
                    );
                    return;
                }

                $('#currentPermissions').html(
                    '<div class="text-center text-muted py-4"><i class="material-icons-outlined loading-icon">hourglass_empty</i><p class="mt-3">Loading permissions...</p></div>'
                );

                $.post('{{ route('admin.users.roles.preview-permissions') }}', {
                        _token: '{{ csrf_token() }}',
                        roles: selectedRoles
                    })
                    .done(function(data) {
                        // Format the permissions data with improved styling
                        const formattedData = formatPermissionsData(data);
                        $('#currentPermissions').html(formattedData);
                    })
                    .fail(function() {
                        $('#currentPermissions').html(
                            '<div class="alert alert-danger"><i class="material-icons-outlined me-2">error_outline</i>Failed to load permissions preview.</div>'
                            );
                    });
            }

            // Form submission
            $('#userRolesForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Show loading state with improved animation
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                // Create a loading button with pulse animation
                submitBtn.html(
                    '<div class="d-flex align-items-center"><i class="material-icons-outlined me-2 loading-icon">autorenew</i><span>Saving...</span></div>'
                    ).addClass('pulse-animation').prop('disabled', true);

                $.ajax({
                    url: '{{ route('admin.users.roles.update', $user->id) }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Success handling
                        showToast('success', response.message);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        showToast('error', xhr.responseJSON?.message || 'حدث خطأ أثناء حفظ الأدوار');
                    },
                    complete: function() {
                            submitBtn.html(originalText).removeClass('pulse-animation').prop(
                            'disabled', false);
                    }
                });
            });

            // Initialize
            updateRoleCards();
            updatePermissionsPreview();
        });
    </script>
