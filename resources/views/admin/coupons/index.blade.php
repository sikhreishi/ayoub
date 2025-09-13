@extends('layouts.app')

@push('plugin-scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.responsive.min.js"></script>
@endpush
@push('plugin-styles')
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.5/css/responsive.dataTables.min.css" rel="stylesheet" />
@endpush
@section('content')
<button class="btn btn-primary mb-3" id="createCouponBtn">Add New Coupon</button>

<!-- Coupon Create Modal -->
<div class="modal fade" id="createCouponModal" tabindex="-1" aria-labelledby="createCouponModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCouponModalLabel">Create New Coupon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createCouponForm">
                    <div class="mb-3">
                        <label for="coupon_code" class="form-label">Code</label>
                        <input type="text" class="form-control" id="coupon_code" name="code" pattern="\S+" title="No spaces allowed" required>
                    </div>
                    <div class="mb-3">
                        <label for="coupon_type" class="form-label">Type</label>
                        <select class="form-control" id="coupon_type" name="type" required>
                            <option value="fixed">Fixed Amount</option>
                            <option value="percent">Percent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-end">
                            <div class="btn-group mb-3" role="group" aria-label="Currency selection">
                            @foreach($currencies as $currency)
                                <input type="radio" class="btn-check" name="currency"
                                    id="currency{{ $currency->id }}"
                                    value="{{ $currency->code }}"
                                    autocomplete="off"
                                    {{ $currency->code === 'USD' ? 'checked' : '' }} required>
                                <label class="btn btn-outline-primary" for="currency{{ $currency->id }}">
                                {{ $currency->code }}
                                </label>
                            @endforeach
                            </div>
                        </div>
                        <label for="coupon_value" class="form-label">Value</label>
                        <input type="number" class="form-control" id="coupon_value" name="value" required step="0.01" min="0.01">
                    </div>
                    <div class="mb-3">
                        <label for="coupon_max_uses" class="form-label">Max Uses</label>
                        <input type="number" class="form-control" id="coupon_max_uses" name="max_uses" min="1">
                    </div>
                    <div class="mb-3">
                        <label for="coupon_max_uses_per_user" class="form-label">Max Uses Per User</label>
                        <input type="number" class="form-control" id="coupon_max_uses_per_user" name="max_uses_per_user" min="1">
                    </div>
                    <div class="mb-3">
                        <label for="coupon_min_trip_amount" class="form-label">Min Trip Amount</label>
                        <input type="number" class="form-control" id="coupon_min_trip_amount" name="min_trip_amount" min="0">
                    </div>
                    <div class="mb-3">
                        <label for="coupon_starts_at" class="form-label">Starts At</label>
                        <input type="datetime-local" class="form-control" id="coupon_starts_at" name="starts_at">
                    </div>
                    <div class="mb-3">
                        <label for="coupon_expires_at" class="form-label">Expires At</label>
                        <input type="datetime-local" class="form-control" id="coupon_expires_at" name="expires_at">
                    </div>
                    <div class="mb-3">
                        <label for="coupon_is_active" class="form-label">Status</label>
                        <select class="form-control" id="coupon_is_active" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Coupon</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Coupon Update Modal -->
<div class="modal fade" id="updateCouponModal" tabindex="-1" aria-labelledby="updateCouponModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateCouponModalLabel">Update Coupon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateCouponForm">
                    <input type="hidden" id="update_coupon_id" name="id">
                    <div class="mb-3">
                        <label for="update_coupon_code" class="form-label">Code</label>
                        <input type="text" class="form-control" id="update_coupon_code" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_coupon_type" class="form-label">Type</label>
                        <select class="form-control" id="update_coupon_type" name="type" required>
                            <option value="fixed">Fixed Amount</option>
                            <option value="percent">Percent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="update_coupon_value" class="form-label">Value</label>
                        <input type="number" class="form-control" id="update_coupon_value" name="value" required min="0">
                    </div>
                    <div class="mb-3">
                        <label for="update_coupon_max_uses" class="form-label">Max Uses</label>
                        <input type="number" class="form-control" id="update_coupon_max_uses" name="max_uses" min="1">
                    </div>
                    <div class="mb-3">
                        <label for="update_coupon_max_uses_per_user" class="form-label">Max Uses Per User</label>
                        <input type="number" class="form-control" id="update_coupon_max_uses_per_user" name="max_uses_per_user" min="1">
                    </div>
                    <div class="mb-3">
                        <label for="update_coupon_min_trip_amount" class="form-label">Min Trip Amount</label>
                        <input type="number" class="form-control" id="update_coupon_min_trip_amount" name="min_trip_amount" min="0">
                    </div>
                    <div class="mb-3">
                        <label for="update_coupon_starts_at" class="form-label">Starts At</label>
                        <input type="datetime-local" class="form-control" id="update_coupon_starts_at" name="starts_at">
                    </div>
                    <div class="mb-3">
                        <label for="update_coupon_expires_at" class="form-label">Expires At</label>
                        <input type="datetime-local" class="form-control" id="update_coupon_expires_at" name="expires_at">
                    </div>
                    <div class="mb-3">
                        <label for="update_coupon_is_active" class="form-label">Status</label>
                        <select class="form-control" id="update_coupon_is_active" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Coupon</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">
    <x-data-table
        title="Coupons"
        table-id="coupons-table"
        fetch-url="{{ route('admin.coupons.data') }}"
        :columns="['Code', 'Type', 'Value', 'Max Uses', 'Max Uses Per User','Used By',  'Min Trip Amount', 'Starts At', 'Expires At', 'Status', 'Actions']"
        :columns-config="[
            ['data' => 'code', 'name' => 'code'],
            ['data' => 'type', 'name' => 'type'],
            ['data' => 'value', 'name' => 'value'],
            ['data' => 'max_uses', 'name' => 'max_uses'],
            ['data' => 'max_uses_per_user', 'name' => 'max_uses_per_user'],
            ['data' => 'used_by', 'name' => 'used_by', 'orderable' => false, 'searchable' => false],
            ['data' => 'min_trip_amount', 'name' => 'min_trip_amount'],
            ['data' => 'starts_at', 'name' => 'starts_at'],
            ['data' => 'expires_at', 'name' => 'expires_at'],
            ['data' => 'is_active', 'name' => 'is_active'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
    />
</div>
@endsection

@push('custom-scripts')
<script>
$(document).ready(function () {
    const table = window['couponsTable'] || $('#coupons-table').DataTable();

    // Open modal for create
    $('#createCouponBtn').on('click', function () {
        clearCreateCouponForm();
        $('#createCouponModal').modal('show');
    });

    // Handle create form submit
    $('#createCouponForm').on('submit', function (e) {
        e.preventDefault();

        const currency = $('input[name="currency"]:checked').val() || 'USD';
        
        const formData = $(this).serialize() + '&currency=' + encodeURIComponent(currency);
        $.ajax({
            url: "{{ route('admin.coupons.store') }}",
            method: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                table.ajax.reload();
                $('#createCouponModal').modal('hide');
                showToast('success', res.message);
            },
            error: function (xhr) {
                showToast('error', xhr.responseJSON?.message || 'An error occurred.');
            }
        });
    });

    // Edit coupon (delegated)
    $(document).on('click', '.edit-coupon', function () {
        const id = $(this).data('id');
        $.get(`{{ route('admin.coupons.edit', ['coupon' => ':id']) }}`.replace(':id', id), function (data) {
            fillUpdateCouponForm(data);
            $('#updateCouponModal').modal('show');
        });
    });

    // Handle update form submit
    $('#updateCouponForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#update_coupon_id').val();
        const formData = $(this).serialize();
        $.ajax({
            url: `{{ route('admin.coupons.update', ['coupon' => ':id']) }}`.replace(':id', id),
            method: 'PUT',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                table.ajax.reload();
                $('#updateCouponModal').modal('hide');
                showToast('success', res.message);
            },
            error: function (xhr) {
                showToast('error', xhr.responseJSON?.message || 'An error occurred.');
            }
        });
    });

    // Delete coupon (delegated)
    $(document).on('click', '.delete-coupon', function () {
        if (!confirm('Are you sure you want to delete this coupon?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: `{{ route('admin.coupons.destroy', ['coupon' => ':id']) }}`.replace(':id', id),
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (data) {
                table.ajax.reload();
                showToast('success', data.message);
            },
            error: function (xhr) {
                showToast('error', xhr.responseJSON?.message || 'Delete failed.');
            }
        });
    });

    function clearCreateCouponForm() {
        $('#createCouponForm')[0].reset();
    }
    function fillUpdateCouponForm(data) {
        $('#update_coupon_id').val(data.id);
        $('#update_coupon_code').val(data.code);
        $('#update_coupon_type').val(data.type);
        $('#update_coupon_value').val(data.value);
        $('#update_coupon_max_uses').val(data.max_uses);
        $('#update_coupon_max_uses_per_user').val(data.max_uses_per_user);
        $('#update_coupon_min_trip_amount').val(data.min_trip_amount);
        $('#update_coupon_starts_at').val(data.starts_at ? data.starts_at.replace(' ', 'T') : '');
        $('#update_coupon_expires_at').val(data.expires_at ? data.expires_at.replace(' ', 'T') : '');
        $('#update_coupon_is_active').val(data.is_active ? '1' : '0');
    }

});

</script>
@endpush
