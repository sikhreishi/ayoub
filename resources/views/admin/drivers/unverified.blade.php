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



    <div class="table-responsive">

        <x-data-table
    title="{{ __('dashboard.users.drivers') }}" 
            table-id="drivers-table"
            fetch-url="{{ route('admin.drivers.unverified.data') }}"
            :columns="[__('dashboard.users.name'), __('dashboard.users.email'), __('dashboard.users.phone'), __('dashboard.users.avatar'), __('dashboard.users.actions')]"
            :columns-config="[
                ['data' => 'name', 'name' => 'name'],
                ['data' => 'email', 'name' => 'email'],
                ['data' => 'phone', 'name' => 'phone'],
                ['data' => 'avatar', 'name' => 'avatar', 'orderable' => false, 'searchable' => false],
                ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
            ]"
        />

    </div>



    <!-- Driver Verification Modal -->
    <div class="modal fade" id="DriverInfoModal" tabindex="-1" aria-labelledby="DriverInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-sm border-0">
        <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold" id="DriverInfoModalLabel">Driver Profile Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="driver-info-content">
            <p class="text-muted">Loading...</p>
        </div>
        <div class="modal-footer bg-light">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
    </div>


    <!-- Fullscreen Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark border-0">
        <div class="modal-body p-0">
            <img id="fullImage" src="" class="img-fluid w-100 rounded-0" alt="Full Image">
        </div>
        <div class="modal-footer bg-dark border-0">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
    </div>
    </div>

@endsection



@push('custom-scripts')


<script>



    $(document).on('TableReady', function () {

        function safeReload() {
            if (window.driversTable && typeof window.driversTable.ajax !== 'undefined') {
                window.driversTable.ajax.reload();
            } else {
                console.warn("driversTable is not ready yet.");
            }
        }


        $(document).on('click', '.previewable-image', function () {
            const fullImageUrl = $(this).data('full') || $(this).attr('src');
            $('#fullImage').attr('src', fullImageUrl);
            $('#imagePreviewModal').modal('show');
        });


        $(document).on('click', '.verify-driver-btn', function () {
            const url = $(this).data('url');

            $.get(url, function (response) {
                if (response.success) {
                    const user = response.data;
                    const profile = user.driver_profile;

                    const html = `
                        <form action="/profile/drivers/${user.id}/verify" method="POST">
                            <input type="hidden" name="_method" value="PUT" />
                            <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">

                            <div class="container-fluid">
                            <div class="row g-3">
                                <div class="col-md-6">
                                <label class="fw-semibold">ID Card (Front):</label>
                                    <img src="/storage/${profile.id_card_front}"
                                        class="img-fluid rounded shadow-sm border previewable-image"
                                        alt="ID Card Front"
                                        data-full="/storage/${profile.id_card_front}">
                                </div>
                                <div class="col-md-6">
                                <label class="fw-semibold">ID Card (Back):</label>
                                <img src="/storage/${profile.id_card_back}" class="img-fluid rounded shadow-sm border previewable-image" data-full="/storage/${profile.id_card_front}" alt="ID Card Back">
                                </div>

                                <div class="col-md-6">
                                <label class="fw-semibold">License (Front):</label>
                                <img src="/storage/${profile.license_front}" class="img-fluid rounded shadow-sm border previewable-image" data-full="/storage/${profile.id_card_front}" alt="License Front">
                                </div>
                                <div class="col-md-6">
                                <label class="fw-semibold">License (Back):</label>
                                <img src="/storage/${profile.license_back}" class="img-fluid rounded shadow-sm border previewable-image" data-full="/storage/${profile.id_card_front}" alt="License Back">
                                </div>

                                <div class="col-md-6">
                                <label class="fw-semibold">Vehicle Seats (Front):</label>
                                <img src="/storage/${profile.interior_front_seats}" class="img-fluid rounded shadow-sm border previewable-image" data-full="/storage/${profile.id_card_front}" alt="Vehicle Seats Front">
                                </div>
                                <div class="col-md-6">
                                <label class="fw-semibold">Vehicle Seats (Back):</label>
                                <img src="/storage/${profile.interior_back_seats}" class="img-fluid rounded shadow-sm border previewable-image" data-full="/storage/${profile.id_card_front}" alt="Vehicle Seats Back">
                                </div>
                                <div class="col-md-6">
                                <label class="fw-semibold">Out Vehicle (Front):</label>
                                <img src="/storage/${profile.exterior_front_side}" class="img-fluid rounded shadow-sm border previewable-image" data-full="/storage/${profile.id_card_front}" alt="Out Vehicle Front">
                                </div>
                                <div class="col-md-6">
                                <label class="fw-semibold">Out Vehicle (Back):</label>
                                <img src="/storage/${profile.exterior_back_side}" class="img-fluid rounded shadow-sm border previewable-image" data-full="/storage/${profile.id_card_front}" alt="Out Vehicle Back">
                                </div>
                                <div class="col-md-6">
                                <label for="is_driver_verified" class="form-label fw-semibold">Driver Verified:</label>
                                <select name="is_driver_verified" id="is_driver_verified" class="form-select">
                                    <option value="1" ${profile.is_driver_verified ? 'selected' : ''}>Yes</option>
                                    <option value="0" ${!profile.is_driver_verified ? 'selected' : ''}>No</option>
                                </select>
                                </div>

                                <div class="col-md-12">
                                <label for="verification_note" class="form-label fw-semibold">Verification Note:</label>
                                <textarea name="verification_note" id="verification_note" rows="3" class="form-control">${profile.verification_note ?? ''}</textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-success">Save Verification</button>
                            </div>
                            </div>
                        </form>
                    `;

                    $('#driver-info-content').html(html);
                    $('#DriverInfoModal').modal('show');
                } else {
                    showToast('error', 'Failed to load driver data.');
                }
            });
        });
    });
</script>


@endpush
