<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
    <h5 class="mb-0">{{ $title }}</h5>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
      <table id="{{ $tableId }}" class="table table-striped table-hover w-100 mb-0">
        <thead class="thead-dark">
          <tr>
            @foreach ($columns as $column)
              <th class="align-middle">{{ $column }}</th>
            @endforeach
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@push('data-table-scripts')
<script>
$(document).ready(function () {
    const tableId = '#{{ $tableId }}';
    const isRtl = "{{ app()->getLocale() === 'ar' }}" === "1";

    window['{{ \Illuminate\Support\Str::camel($tableId) }}'] = $(tableId).DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ $fetchUrl }}',
        columns: {!! json_encode($columnsConfig) !!},
        responsive: true,
        rtl: isRtl, // DataTables RTL support
        dom: isRtl
            ? "<'row'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'l>>" +
              "<'row'<'col-sm-12'tr>>" +
              "<'row'<'col-sm-12 col-md-7'i><'col-sm-12 col-md-5'p>>"
            : "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
              "<'row'<'col-sm-12'tr>>" +
              "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        initComplete: function() {
            $(document).trigger('TableReady');
        },
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
            search: "_INPUT_",
            searchPlaceholder: isRtl ? "بحث..." : "Search...",
            lengthMenu: isRtl ? "أظهر _MENU_ مدخلات" : "Show _MENU_ entries",
            info: isRtl ? "عرض _START_ إلى _END_ من _TOTAL_ مدخلات" : "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: isRtl ? "لا توجد مدخلات" : "No entries found",
            infoFiltered: isRtl ? "(مصفاة من _MAX_ إجمالي المدخلات)" : "(filtered from _MAX_ total entries)",
            paginate: {
                first: isRtl ? "الأول" : "First",
                last: isRtl ? "الأخير" : "Last",
                next: isRtl ? "التالي" : "Next",
                previous: isRtl ? "السابق" : "Previous"
            }
        },
        drawCallback: function() {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
        }
    });
});
</script>
@endpush


@push('data-table-styles')
<style>
/* Card styling */
.card {
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding: 1rem 1.25rem;
}

/* Table styling */
.table {
    margin-bottom: 0 !important;
}

.table thead th {
    vertical-align: middle;
    padding: 0.75rem 1rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    border-bottom-width: 1px;
}

.table tbody td {
    padding: 0.75rem 1rem;
    vertical-align: middle;
    border-top: 1px solid #f0f0f0;
}

/* Striped rows with softer color */
.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 123, 255, 0.02);
}
.table-striped tbody tr:nth-of-type(even) {
    background-color: rgba(0, 123, 255, 0.02);
}

/* Hover effect */
.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

/* Responsive container */
.table-responsive {
    border-radius: 0.5rem;
    overflow-x: auto; /* Make the table scrollable horizontally */
}

/* DataTables controls */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    padding: 0.75rem 1.25rem;
}

.dataTables_wrapper .dataTables_length select {
    border-radius: 0.25rem;
    padding: 0.25rem 0.5rem;
    margin: 0 0.25rem;
    border: 1px solid #dee2e6;
}

.dataTables_wrapper .dataTables_filter input {
    border-radius: 0.25rem;
    padding: 0.25rem 0.5rem;
    border: 1px solid #dee2e6;
    margin-left: 0.5rem;
}

/* Pagination styling */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.25rem 0.75rem;
    margin: 0 0.1rem;
    border-radius: 0.25rem;
    border: 1px solid transparent;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #007bff !important;
    color: white !important;
    border: 1px solid #007bff;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #e9ecef;
    border: 1px solid #dee2e6;
    color: #0056b3 !important;
}

/* Info text */
.dataTables_info {
    color: #6c757d;
    font-size: 0.875rem;
}

/* Processing indicator */
.dataTables_processing {
    background: rgba(255, 255, 255, 0.9) !important;
    color: #007bff !important;
    box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
    padding: 1.5rem !important;
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 767px) {
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        text-align: left;
    }

    .dataTables_wrapper .dataTables_filter {
        margin-top: 0.5rem;
    }

    .dataTables_wrapper .dataTables_paginate {
        text-align: center;
    }
}

</style>
@endpush
