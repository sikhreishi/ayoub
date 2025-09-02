@push('plugin-scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
@endpush
@push('plugin-styles')
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.5/css/responsive.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endpush

@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h4 class="mb-0 fw-bold">Country Currencies Management</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createCountryCurrencyModal">
            <i class="material-icons-outlined">add</i> Add Country-Currency Link
        </button>
    </div>
</div>
<div class="table-responsive">
    <x-data-table
        title="Country Currencies"
        table-id="country-currencies-table"
        fetch-url="{{ route('admin.countrycurrencies.data') }}"
        :columns="['Country Ar Name','Country En Name', 'Currency Name', 'action']"
        :columns-config="[
            ['data' => 'country_ar', 'name' => 'country_ar'],
            ['data' => 'country_en', 'name' => 'country_en'],
            ['data' => 'currency', 'name' => 'currency'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
    />
</div>
<!-- Modal for adding new country-currency link -->
<div class="modal fade" id="createCountryCurrencyModal" tabindex="-1" aria-labelledby="createCountryCurrencyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content shadow-lg border-0">
        <div class="modal-header bg-light border-bottom-0">
          <h5 class="modal-title fw-bold" id="createCountryCurrencyModalLabel">Add Country-Currency Link</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="card border-0 shadow-sm mb-0">
            <div class="card-body pb-0">
              <form id="countryCurrencyForm">
                <div class="mb-3">
                  <label for="country_id" class="form-label fw-semibold ">Country</label>
                  <select class="form-select w-100 " id="country_id" name="country_id" required></select>
                </div>
                <div class="mb-3">
                  <label for="currency_ids" class="form-label fw-semibold">Currencies</label>
                  <select class="form-select w-100" id="currency_ids" name="currency_ids[]" multiple required></select>
                  <div class="form-text">You can select more than one currency for the selected country.</div>
                </div>
                <hr class="my-4">
                <div class="d-flex justify-content-end">
                  <button type="submit" class="btn btn-outline-primary px-4">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('custom-scripts')
    @include('admin.countryCurrencies.script.countryCurrencies')
@endpush

@push('plugin-styles')
<style>
.select2-container .select2-selection--single .select2-selection__rendered,
.select2-container .select2-selection--multiple .select2-selection__rendered {
    white-space: normal !important;
    word-break: break-word !important;
 }
.select2-container--default .select2-selection--single {
    height: 38px;
    line-height: 38px;
}

.select2-container--default .select2-selection--multiple {
    min-height: 38px;
}
</style>
@endpush
