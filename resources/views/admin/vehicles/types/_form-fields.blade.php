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
    <label for="{{ $prefix }}start_fare" class="form-label">Start Fare</label>
    <input type="number" step="0.01" class="form-control" id="{{ $prefix }}start_fare" name="start_fare" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}day_per_km_rate" class="form-label">Fare per KM (Day)</label>
    <input type="number" step="0.01" class="form-control" id="{{ $prefix }}day_per_km_rate" name="day_per_km_rate" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}night_per_km_rate" class="form-label">Fare per KM (Night)</label>
    <input type="number" step="0.01" class="form-control" id="{{ $prefix }}night_per_km_rate" name="night_per_km_rate" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}day_per_minute_rate" class="form-label">Fare per Min (Day)</label>
    <input type="number" step="0.01" class="form-control" id="{{ $prefix }}day_per_minute_rate" name="day_per_minute_rate" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}night_per_minute_rate" class="form-label">Fare per Min (Night)</label>
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
