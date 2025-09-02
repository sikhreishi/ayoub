<script>
    $(document).ready(function() {
        // Initialize select2 for better UX
        $('#country_id').select2({
            dropdownParent: $('#createCountryCurrencyModal'),
            width: '100%'
        });
        $('#currency_ids').select2({
            dropdownParent: $('#createCountryCurrencyModal'),
            width: '100%'
        });

        // Fetch countries and currencies from server
        $('#createCountryCurrencyModal').on('show.bs.modal', function () {
            $.getJSON("{{ route('admin.countrycurrencies.getFormOptions') }}", function(data) {
                // Populate countries
                var countrySelect = $('#country_id');
                countrySelect.empty();
                $.each(data.countries_en, function(i, country) {
                    countrySelect.append($('<option>', {
                        value: country.id,
                        text: country.name_en
                    }));
                });
                // Populate currencies
                var currencySelect = $('#currency_ids');
                currencySelect.empty();
                $.each(data.currencies, function(i, currency) {
                    currencySelect.append($('<option>', {
                        value: currency.id,
                        text: currency.name
                    }));
                });
            });
        });

        // Handle form submission (AJAX example, adjust endpoint as needed)
        $('#countryCurrencyForm').on('submit', function(e) {
            e.preventDefault();
            var country_id = $('#country_id').val();
            var currency_ids = $('#currency_ids').val();
            // TODO: Send AJAX POST to your backend to save the links
            // Example:
            $.ajax({
                url: '/dashboard/admin/countrycurrencies',
                method: 'POST',
                data: {
                    country_id: country_id,
                    currency_ids: currency_ids,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('createCountryCurrencyModal'));
                    if (modal) modal.hide();
                    $('#country-currencies-table').DataTable().ajax.reload();
                    showToast('success', response.message);

                },
                error: function(xhr) {
                    // handle error
                }
            });

        });

        $('#country_id').on('change', function() {
            var country_id = $(this).val();
            if (!country_id) return;

            $.getJSON('/dashboard/admin/countrycurrencies/currencies/' + country_id, function(data) {
                var usedCurrencies = data.currency_ids.map(String); // IDs as strings
                $('#currency_ids option ').each(function() {
                    if (usedCurrencies.includes($(this).val())) {
                        $(this).prop('disabled', true).hide();
                    } else {
                        $(this).prop('disabled', false).show();
                    }
                });
                $('#currency_ids').val(null).trigger('change'); // reset selection
            });
        });


    });
    </script>
