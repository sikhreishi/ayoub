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

<button class="btn btn-primary mb-3" id="createVehicleTypeBtn">Add New Vehicle Type</button>

<div class="table-responsive">
    <x-data-table
        title="Vehicle Types"
        table-id="vehicle-types-table"
        fetch-url="{{ route('admin.vehicle_types.data') }}"
        :columns="['Name', 'Description', 'Start Fare', 'Day KM Rate', 'Night KM Rate', 'Actions']"
        :columns-config="[
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'description', 'name' => 'description'],
            ['data' => 'start_fare', 'name' => 'start_fare'],
            ['data' => 'day_per_km_rate', 'name' => 'day_per_km_rate'],
            ['data' => 'night_per_km_rate', 'name' => 'night_per_km_rate'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
    />
</div>

<!-- Modal to Create a New Vehicle Type -->
<div class="modal fade" id="createVehicleTypeModal" tabindex="-1" aria-labelledby="createVehicleTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createVehicleTypeModalLabel">Create New Vehicle Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createVehicleTypeForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" required>
                    </div>
                    <div class="mb-3">
                        <label for="start_fare" class="form-label">Start Fare</label>
                        <input type="number" step="0.01" class="form-control" id="start_fare" required>
                    </div>
                    <div class="mb-3">
                        <label for="day_per_km_rate" class="form-label">Day Per KM Rate</label>
                        <input type="number" step="0.01" class="form-control" id="day_per_km_rate" required>
                    </div>
                    <div class="mb-3">
                        <label for="night_per_km_rate" class="form-label">Night Per KM Rate</label>
                        <input type="number" step="0.01" class="form-control" id="night_per_km_rate" required>
                    </div>
                    <div class="mb-3">
                        <label for="day_per_minute_rate" class="form-label">Day Per Minute Rate</label>
                        <input type="number" step="0.01" class="form-control" id="day_per_minute_rate" required>
                    </div>
                    <div class="mb-3">
                        <label for="night_per_minute_rate" class="form-label">Night Per Minute Rate</label>
                        <input type="number" step="0.01" class="form-control" id="night_per_minute_rate" required>
                    </div>
                    <div class="mb-3">
                        <label for="is_active" class="form-label">Active</label>
                        <input type="checkbox" class="form-check-input" id="is_active">
                    </div>
                    <div class="mb-3">
                        <label for="icon_url" class="form-label">Icon</label>
                        <input type="file" class="form-control" id="icon_url" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Create Vehicle Type</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal to Update Vehicle Type -->
<div class="modal fade" id="updateVehicleTypeModal" tabindex="-1" aria-labelledby="updateVehicleTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateVehicleTypeModalLabel">Update Vehicle Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateVehicleTypeForm">
                    <input type="hidden" id="update_vehicle_type_id">
                    <div class="mb-3">
                        <label for="update_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="update_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="update_description" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_start_fare" class="form-label">Start Fare</label>
                        <input type="number" step="0.01" class="form-control" id="update_start_fare" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_day_per_km_rate" class="form-label">Day Per KM Rate</label>
                        <input type="number" step="0.01" class="form-control" id="update_day_per_km_rate" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_night_per_km_rate" class="form-label">Night Per KM Rate</label>
                        <input type="number" step="0.01" class="form-control" id="update_night_per_km_rate" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_day_per_minute_rate" class="form-label">Day Per Minute Rate</label>
                        <input type="number" step="0.01" class="form-control" id="update_day_per_minute_rate" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_night_per_minute_rate" class="form-label">Night Per Minute Rate</label>
                        <input type="number" step="0.01" class="form-control" id="update_night_per_minute_rate" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_is_active" class="form-label">Active</label>
                        <input type="checkbox" class="form-check-input" id="update_is_active">
                    </div>
                    <!-- Add an input field for the icon file upload in the Update Modal -->
                    <div class="mb-3">
                        <label for="update_icon_url" class="form-label">Update Icon</label>
                        <input type="file" class="form-control" id="update_icon_url" accept="image/*">
                    </div>
                    <!-- Optional: Display a preview of the current image -->
                    <img id="current_icon_preview" src="" alt="Current Icon" width="50" />

                    <button type="submit" class="btn btn-primary">Update Vehicle Type</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('custom-scripts')
<script>
  $(document).on('TableReady', function () {

    function safeReload() {
        if (window.vehicleTypesTable && typeof window.vehicleTypesTable.ajax !== 'undefined') {
            window.vehicleTypesTable.ajax.reload();
        } else {
            console.warn("vehicleTypesTable is not ready yet.");
        }
    }

    $('#createVehicleTypeBtn').click(function() {
        $('#createVehicleTypeModal').modal('show');
    });

    // Handle form submission for creating a vehicle type
    $('#createVehicleTypeForm').submit(function(e) {
        e.preventDefault();
        const formData = new FormData();
        formData.append('name', $('#name').val());
        formData.append('description', $('#description').val());
        formData.append('start_fare', $('#start_fare').val());
        formData.append('day_per_km_rate', $('#day_per_km_rate').val());
        formData.append('night_per_km_rate', $('#night_per_km_rate').val());
        formData.append('day_per_minute_rate', $('#day_per_minute_rate').val());
        formData.append('night_per_minute_rate', $('#night_per_minute_rate').val());
        formData.append('is_active', $('#is_active').prop('checked') ? 1 : 0);

        // Handling file upload
        if ($('#icon_url')[0].files[0]) {
            formData.append('icon_url', $('#icon_url')[0].files[0]);
        }

        $.ajax({
            url: '{{ route('admin.vehicle_types.store') }}',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', 'Vehicle type created successfully!');
                    $('#createVehicleTypeModal').modal('hide');
                    $('#createVehicleTypeForm')[0].reset();  // Clear form fields
                    safeReload();
                } else {
                    showToast('error', response.message || 'Failed to create vehicle type');
                }
            },
            error: function(xhr, status, error) {
                showToast('error', error + ' An error occurred while creating the vehicle type.');
            }
        });
    });

    // Handle form submission for updating a vehicle type
    $('#updateVehicleTypeForm').submit(function(e) {
        e.preventDefault();
        const vehicleTypeId = $('#update_vehicle_type_id').val();
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('name', $('#update_name').val());
        formData.append('description', $('#update_description').val());
        formData.append('start_fare', $('#update_start_fare').val());
        formData.append('day_per_km_rate', $('#update_day_per_km_rate').val());
        formData.append('night_per_km_rate', $('#update_night_per_km_rate').val());
        formData.append('day_per_minute_rate', $('#update_day_per_minute_rate').val());
        formData.append('night_per_minute_rate', $('#update_night_per_minute_rate').val());
        formData.append('is_active', $('#update_is_active').prop('checked') ? 1 : 0);

        // Handle file upload if a new image is provided
        if ($('#update_icon_url')[0].files[0]) {
            formData.append('icon_url', $('#update_icon_url')[0].files[0]);
        }
        $.ajax({
            url: '{{ route('admin.vehicle_types.update', ':id') }}'.replace(':id', vehicleTypeId),
            method: 'PUT',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', 'Vehicle type updated successfully!');
                    $('#updateVehicleTypeModal').modal('hide');
                    safeReload();
                } else {
                    showToast('error', response.message || 'Failed to update vehicle type');
                }
            },
            error: function(xhr, status, error) {
                showToast('error', error + ' An error occurred while updating the vehicle type.');
            }
        });
    });

    // Handle edit button click to populate update modal
    $('#vehicle-types-table').on('click', '.edit-item', function() {
        const vehicleTypeId = $(this).data('id'); // Get the vehicle type ID

        $.ajax({
            url: '{{ route('admin.vehicle_types.edit', ':id') }}'.replace(':id', vehicleTypeId),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const vehicleType = response.data;
                    $('#update_vehicle_type_id').val(vehicleType.id);
                    $('#update_name').val(vehicleType.name);
                    $('#update_description').val(vehicleType.description);
                    $('#update_start_fare').val(vehicleType.start_fare);
                    $('#update_day_per_km_rate').val(vehicleType.day_per_km_rate);
                    $('#update_night_per_km_rate').val(vehicleType.night_per_km_rate);
                    $('#update_day_per_minute_rate').val(vehicleType.day_per_minute_rate);
                    $('#update_night_per_minute_rate').val(vehicleType.night_per_minute_rate);
                    $('#update_is_active').prop('checked', vehicleType.is_active);

                    if (vehicleType.icon_url) {
                        $('#current_icon_preview').attr('src', '{{ asset('storage/') }}/' + vehicleType.icon_url);
                    }
                    $('#updateVehicleTypeModal').modal('show');
                } else {
                    showToast('error', 'Failed to fetch vehicle type data');
                }
            },
            error: function(xhr, status, error) {
                showToast('error', 'An error occurred while fetching the vehicle type data');
            }
        });
    });
});

</script>
@endpush
