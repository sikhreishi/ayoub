<script>
    $(document).ready(function() {
        $('#currency-select').on('change', function() {
            var name = $(this).find(':selected').data('name') || '';
            var code = $(this).val() || '';
            $('#currency-name').val(name);
            $('#currency-code').val(code);
        });

        // DataTable initialization
        var table = $('#currencies-table').DataTable();

        // Add currency via Ajax
        $('#add-currency-form').on('submit', function(e) {
            e.preventDefault();
            var name = $('#currency-name').val();
            var code = $('#currency-code').val();
            if (!name || !code) {
                alert('Please select a currency first');
                return;
            }
            $.ajax({
                url: '{{ route('admin.currencies.store') }}',
                method: 'POST',
                data: {
                    name: name,
                    code: code,
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    $('#createCurrencyModal').modal('hide');
                    if (res.success && res.currency) {
                        // Close modal
                        // Reset form
                        $('#add-currency-form')[0].reset();
                        $('#currency-name').val('');
                        $('#currency-code').val('');
                        // Add new row to DataTable
                        table.row.add({
                            name: res.currency.name,
                            code: res.currency.code,
                            action: '<button class="btn btn-sm btn-danger delete-item" data-id="' + res.currency.id + '">Delete</button>'
                        }).draw(false);
                        showToast('success', res.message);

                    } else {
                        showToast('success', res.message || 'add failed');
                    }
                    table.ajax.reload();
                    $('#add-currency-form')[0].reset();
                },
                error: function(xhr) {
                    var msg = 'An error occurred while adding the currency2';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).join('\n');
                    }
                    $('#createCurrencyModal').modal('hide');

                    showToast('error', msg);
                }
            });
        });
        var currencyToDeleteId = null;
        var currencyToDeleteBtn = null;
        $('#currencies-table').on('click', '.delete-item', function() {
            currencyToDeleteId = $(this).data('id');
            currencyToDeleteBtn = $(this);
            $('#deleteCurrencyModal').modal('show');
        });
        $('#confirmDeleteBtn').on('click', function() {
            if (!currencyToDeleteId) return;
            $.ajax({
                url: '/dashboard/admin/currencies/' + currencyToDeleteId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    if(res.success) {
                        var table = $('#currencies-table').DataTable();
                        table.row(currencyToDeleteBtn.parents('tr')).remove().draw();
                        $('#deleteCurrencyModal').modal('hide');
                        $('#confirmDeleteModal').modal('hide');
                        showToast('success', res.message);
                    } else {
                        showToast('error', res.message);
                    }
                },
                error: function(xhr) {
                    showToast('error', 'An error occurred while deleting the currency');
                }
            });
        });
    });
    </script>
