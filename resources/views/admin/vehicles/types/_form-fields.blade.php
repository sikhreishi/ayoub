@php $prefix = $prefix ?? '' @endphp

<div class="mb-3">
    <label for="{{ $prefix }}name" class="form-label">Name</label>
    <input type="text" class="form-control" id="{{ $prefix }}name" name="name" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}description" class="form-label">Description</label>
    <textarea class="form-control" id="{{ $prefix }}description" name="description"></textarea>
</div>

<div class="mb-3">
<label for="{{ $prefix }}start_fare" class="form-label d-block">Start Fare<small class="d-block mt-1 text-danger">💵 Please enter the amount in <strong>USD $</strong></small></label>
    <input type="number" step="0.01" class="form-control" id="{{ $prefix }}start_fare" name="start_fare" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}day_per_km_rate" class="form-label">Fare per KM (Day) Fare<small class="d-block mt-1 text-danger">💵 Please enter the amount in <strong>USD $</strong></small></label>
    <input type="number" step="0.01" class="form-control" id="{{ $prefix }}day_per_km_rate" name="day_per_km_rate" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}night_per_km_rate" class="form-label">Fare per KM (Night) Fare<small class="d-block mt-1 text-danger">💵 Please enter the amount in <strong>USD $</strong></small></label>
    <input type="number" step="0.01" class="form-control" id="{{ $prefix }}night_per_km_rate" name="night_per_km_rate" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}day_per_minute_rate" class="form-label">Fare per Min (Day) <small class="d-block mt-1 text-danger">💵 Please enter the amount in <strong>USD $</strong></small></label>
    <input type="number" step="0.01" class="form-control" id="{{ $prefix }}day_per_minute_rate" name="day_per_minute_rate" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}night_per_minute_rate" class="form-label">Fare per Min (Night) <small class="d-block mt-1 text-danger">💵 Please enter the amount in <strong>USD $</strong></small></label>
    <input type="number" step="0.01" class="form-control" id="{{ $prefix }}night_per_minute_rate" name="night_per_minute_rate" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}icon_url" class="form-label">Icon File</label>
    <input type="file" class="form-control" id="{{ $prefix }}icon_url" name="icon_url" accept="image/*">
</div>

<div class="mb-3">
    <label for="{{ $prefix }}is_active" class="form-label">Status</label>
    <select class="form-control" id="{{ $prefix }}is_active" name="is_active">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
    </select>
</div>
