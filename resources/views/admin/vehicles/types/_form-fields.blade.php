@php $prefix = $prefix ?? '' @endphp

<div class="mb-3">
    <label for="{{ $prefix }}name" class="form-label">{{ __('dashboard.vehicles.name') }}</label>
    <input type="text" class="form-control" id="{{ $prefix }}name" name="name" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}description" class="form-label">{{ __('dashboard.vehicles.description') }}</label>
    <textarea class="form-control" id="{{ $prefix }}description" name="description"></textarea>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}start_fare" class="form-label d-block">
        {{ __('dashboard.vehicles.start_fare') }}
        <small class="d-block mt-1 text-danger">{{ __('dashboard.vehicles.usd_note') }}</small>
    </label>
    <input type="number" step="0.01" class="form-control" id="{{ $prefix }}start_fare" name="start_fare" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}day_per_km_rate" class="form-label">
        {{ __('dashboard.vehicles.day_per_km_rate') }}
        <small class="d-block mt-1 text-danger">{{ __('dashboard.vehicles.usd_note') }}</small>
    </label>
    <input type="number" step="0.01" class="form-control" id="{{ $prefix }}day_per_km_rate" name="day_per_km_rate" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}night_per_km_rate" class="form-label">
        {{ __('dashboard.vehicles.night_per_km_rate') }}
        <small class="d-block mt-1 text-danger">{{ __('dashboard.vehicles.usd_note') }}</small>
    </label>
    <input type="number" step="0.01" class="form-control" id="{{ $prefix }}night_per_km_rate" name="night_per_km_rate" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}day_per_minute_rate" class="form-label">
        {{ __('dashboard.vehicles.day_per_minute_rate') }}
        <small class="d-block mt-1 text-danger">{{ __('dashboard.vehicles.usd_note') }}</small>
    </label>
    <input type="number" step="0.01" class="form-control" id="{{ $prefix }}day_per_minute_rate" name="day_per_minute_rate" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}night_per_minute_rate" class="form-label">
        {{ __('dashboard.vehicles.night_per_minute_rate') }}
        <small class="d-block mt-1 text-danger">{{ __('dashboard.vehicles.usd_note') }}</small>
    </label>
    <input type="number" step="0.01" class="form-control" id="{{ $prefix }}night_per_minute_rate" name="night_per_minute_rate" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}icon_url" class="form-label">{{ __('dashboard.vehicles.icon') }}</label>
    <input type="file" class="form-control" id="{{ $prefix }}icon_url" name="icon_url" accept="image/*">
</div>

<div class="mb-3">
    <label for="{{ $prefix }}is_active" class="form-label">{{ __('dashboard.vehicles.status') }}</label>
    <select class="form-control" id="{{ $prefix }}is_active" name="is_active">
        <option value="1">{{ __('dashboard.vehicles.active') }}</option>
        <option value="0">{{ __('dashboard.vehicles.inactive') }}</option>
    </select>
</div>
