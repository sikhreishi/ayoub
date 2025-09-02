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
<div class="row mb-4">
    <div class="col-md-12">
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createCurrencyModal">
            <i class="material-icons-outlined">add</i> Create Currency
        </button>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="createCurrencyModal" tabindex="-1" aria-labelledby="createCurrencyModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createCurrencyModalLabel">Add New Currency</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="add-currency-form">
            <div class="mb-3">
                <label for="currency-select" class="form-label">Select Currency</label>
                <select id="currency-select" class="form-select">
                    <option value="">-- Select Currency --</option>
                    <option value="USD" data-name="USD">USD (USD)</option>
                    <option value="AED" data-name="AED">AED (AED)</option>
                    <option value="AFN" data-name="AFN">AFN (AFN)</option>
                    <option value="ALL" data-name="ALL">ALL (ALL)</option>
                    <option value="AMD" data-name="AMD">AMD (AMD)</option>
                    <option value="ANG" data-name="ANG">ANG (ANG)</option>
                    <option value="AOA" data-name="AOA">AOA (AOA)</option>
                    <option value="ARS" data-name="ARS">ARS (ARS)</option>
                    <option value="AUD" data-name="AUD">AUD (AUD)</option>
                    <option value="AWG" data-name="AWG">AWG (AWG)</option>
                    <option value="AZN" data-name="AZN">AZN (AZN)</option>
                    <option value="BAM" data-name="BAM">BAM (BAM)</option>
                    <option value="BBD" data-name="BBD">BBD (BBD)</option>
                    <option value="BDT" data-name="BDT">BDT (BDT)</option>
                    <option value="BGN" data-name="BGN">BGN (BGN)</option>
                    <option value="BHD" data-name="BHD">BHD (BHD)</option>
                    <option value="BIF" data-name="BIF">BIF (BIF)</option>
                    <option value="BMD" data-name="BMD">BMD (BMD)</option>
                    <option value="BND" data-name="BND">BND (BND)</option>
                    <option value="BOB" data-name="BOB">BOB (BOB)</option>
                    <option value="BRL" data-name="BRL">BRL (BRL)</option>
                    <option value="BSD" data-name="BSD">BSD (BSD)</option>
                    <option value="BTN" data-name="BTN">BTN (BTN)</option>
                    <option value="BWP" data-name="BWP">BWP (BWP)</option>
                    <option value="BYN" data-name="BYN">BYN (BYN)</option>
                    <option value="BZD" data-name="BZD">BZD (BZD)</option>
                    <option value="CAD" data-name="CAD">CAD (CAD)</option>
                    <option value="CDF" data-name="CDF">CDF (CDF)</option>
                    <option value="CHF" data-name="CHF">CHF (CHF)</option>
                    <option value="CLP" data-name="CLP">CLP (CLP)</option>
                    <option value="CNY" data-name="CNY">CNY (CNY)</option>
                    <option value="COP" data-name="COP">COP (COP)</option>
                    <option value="CRC" data-name="CRC">CRC (CRC)</option>
                    <option value="CUP" data-name="CUP">CUP (CUP)</option>
                    <option value="CVE" data-name="CVE">CVE (CVE)</option>
                    <option value="CZK" data-name="CZK">CZK (CZK)</option>
                    <option value="DJF" data-name="DJF">DJF (DJF)</option>
                    <option value="DKK" data-name="DKK">DKK (DKK)</option>
                    <option value="DOP" data-name="DOP">DOP (DOP)</option>
                    <option value="DZD" data-name="DZD">DZD (DZD)</option>
                    <option value="EGP" data-name="EGP">EGP (EGP)</option>
                    <option value="ERN" data-name="ERN">ERN (ERN)</option>
                    <option value="ETB" data-name="ETB">ETB (ETB)</option>
                    <option value="EUR" data-name="EUR">EUR (EUR)</option>
                    <option value="FJD" data-name="FJD">FJD (FJD)</option>
                    <option value="FKP" data-name="FKP">FKP (FKP)</option>
                    <option value="FOK" data-name="FOK">FOK (FOK)</option>
                    <option value="GBP" data-name="GBP">GBP (GBP)</option>
                    <option value="GEL" data-name="GEL">GEL (GEL)</option>
                    <option value="GGP" data-name="GGP">GGP (GGP)</option>
                    <option value="GHS" data-name="GHS">GHS (GHS)</option>
                    <option value="GIP" data-name="GIP">GIP (GIP)</option>
                    <option value="GMD" data-name="GMD">GMD (GMD)</option>
                    <option value="GNF" data-name="GNF">GNF (GNF)</option>
                    <option value="GTQ" data-name="GTQ">GTQ (GTQ)</option>
                    <option value="GYD" data-name="GYD">GYD (GYD)</option>
                    <option value="HKD" data-name="HKD">HKD (HKD)</option>
                    <option value="HNL" data-name="HNL">HNL (HNL)</option>
                    <option value="HRK" data-name="HRK">HRK (HRK)</option>
                    <option value="HTG" data-name="HTG">HTG (HTG)</option>
                    <option value="HUF" data-name="HUF">HUF (HUF)</option>
                    <option value="IDR" data-name="IDR">IDR (IDR)</option>
                    <option value="ILS" data-name="ILS">ILS (ILS)</option>
                    <option value="IMP" data-name="IMP">IMP (IMP)</option>
                    <option value="INR" data-name="INR">INR (INR)</option>
                    <option value="IQD" data-name="IQD">IQD (IQD)</option>
                    <option value="IRR" data-name="IRR">IRR (IRR)</option>
                    <option value="ISK" data-name="ISK">ISK (ISK)</option>
                    <option value="JEP" data-name="JEP">JEP (JEP)</option>
                    <option value="JMD" data-name="JMD">JMD (JMD)</option>
                    <option value="JOD" data-name="JOD">JOD (JOD)</option>
                    <option value="JPY" data-name="JPY">JPY (JPY)</option>
                    <option value="KES" data-name="KES">KES (KES)</option>
                    <option value="KGS" data-name="KGS">KGS (KGS)</option>
                    <option value="KHR" data-name="KHR">KHR (KHR)</option>
                    <option value="KID" data-name="KID">KID (KID)</option>
                    <option value="KMF" data-name="KMF">KMF (KMF)</option>
                    <option value="KRW" data-name="KRW">KRW (KRW)</option>
                    <option value="KWD" data-name="KWD">KWD (KWD)</option>
                    <option value="KYD" data-name="KYD">KYD (KYD)</option>
                    <option value="KZT" data-name="KZT">KZT (KZT)</option>
                    <option value="LAK" data-name="LAK">LAK (LAK)</option>
                    <option value="LBP" data-name="LBP">LBP (LBP)</option>
                    <option value="LKR" data-name="LKR">LKR (LKR)</option>
                    <option value="LRD" data-name="LRD">LRD (LRD)</option>
                    <option value="LSL" data-name="LSL">LSL (LSL)</option>
                    <option value="LYD" data-name="LYD">LYD (LYD)</option>
                    <option value="MAD" data-name="MAD">MAD (MAD)</option>
                    <option value="MDL" data-name="MDL">MDL (MDL)</option>
                    <option value="MGA" data-name="MGA">MGA (MGA)</option>
                    <option value="MKD" data-name="MKD">MKD (MKD)</option>
                    <option value="MMK" data-name="MMK">MMK (MMK)</option>
                    <option value="MNT" data-name="MNT">MNT (MNT)</option>
                    <option value="MOP" data-name="MOP">MOP (MOP)</option>
                    <option value="MRU" data-name="MRU">MRU (MRU)</option>
                    <option value="MUR" data-name="MUR">MUR (MUR)</option>
                    <option value="MVR" data-name="MVR">MVR (MVR)</option>
                    <option value="MWK" data-name="MWK">MWK (MWK)</option>
                    <option value="MXN" data-name="MXN">MXN (MXN)</option>
                    <option value="MYR" data-name="MYR">MYR (MYR)</option>
                    <option value="MZN" data-name="MZN">MZN (MZN)</option>
                    <option value="NAD" data-name="NAD">NAD (NAD)</option>
                    <option value="NGN" data-name="NGN">NGN (NGN)</option>
                    <option value="NIO" data-name="NIO">NIO (NIO)</option>
                    <option value="NOK" data-name="NOK">NOK (NOK)</option>
                    <option value="NPR" data-name="NPR">NPR (NPR)</option>
                    <option value="NZD" data-name="NZD">NZD (NZD)</option>
                    <option value="OMR" data-name="OMR">OMR (OMR)</option>
                    <option value="PAB" data-name="PAB">PAB (PAB)</option>
                    <option value="PEN" data-name="PEN">PEN (PEN)</option>
                    <option value="PGK" data-name="PGK">PGK (PGK)</option>
                    <option value="PHP" data-name="PHP">PHP (PHP)</option>
                    <option value="PKR" data-name="PKR">PKR (PKR)</option>
                    <option value="PLN" data-name="PLN">PLN (PLN)</option>
                    <option value="PYG" data-name="PYG">PYG (PYG)</option>
                    <option value="QAR" data-name="QAR">QAR (QAR)</option>
                    <option value="RON" data-name="RON">RON (RON)</option>
                    <option value="RSD" data-name="RSD">RSD (RSD)</option>
                    <option value="RUB" data-name="RUB">RUB (RUB)</option>
                    <option value="RWF" data-name="RWF">RWF (RWF)</option>
                    <option value="SAR" data-name="SAR">SAR (SAR)</option>
                    <option value="SBD" data-name="SBD">SBD (SBD)</option>
                    <option value="SCR" data-name="SCR">SCR (SCR)</option>
                    <option value="SDG" data-name="SDG">SDG (SDG)</option>
                    <option value="SEK" data-name="SEK">SEK (SEK)</option>
                    <option value="SGD" data-name="SGD">SGD (SGD)</option>
                    <option value="SHP" data-name="SHP">SHP (SHP)</option>
                    <option value="SLE" data-name="SLE">SLE (SLE)</option>
                    <option value="SLL" data-name="SLL">SLL (SLL)</option>
                    <option value="SOS" data-name="SOS">SOS (SOS)</option>
                    <option value="SRD" data-name="SRD">SRD (SRD)</option>
                    <option value="SSP" data-name="SSP">SSP (SSP)</option>
                    <option value="STN" data-name="STN">STN (STN)</option>
                    <option value="SYP" data-name="SYP">SYP (SYP)</option>
                    <option value="SZL" data-name="SZL">SZL (SZL)</option>
                    <option value="THB" data-name="THB">THB (THB)</option>
                    <option value="TJS" data-name="TJS">TJS (TJS)</option>
                    <option value="TMT" data-name="TMT">TMT (TMT)</option>
                    <option value="TND" data-name="TND">TND (TND)</option>
                    <option value="TOP" data-name="TOP">TOP (TOP)</option>
                    <option value="TRY" data-name="TRY">TRY (TRY)</option>
                    <option value="TTD" data-name="TTD">TTD (TTD)</option>
                    <option value="TVD" data-name="TVD">TVD (TVD)</option>
                    <option value="TWD" data-name="TWD">TWD (TWD)</option>
                    <option value="TZS" data-name="TZS">TZS (TZS)</option>
                    <option value="UAH" data-name="UAH">UAH (UAH)</option>
                    <option value="UGX" data-name="UGX">UGX (UGX)</option>
                    <option value="UYU" data-name="UYU">UYU (UYU)</option>
                    <option value="UZS" data-name="UZS">UZS (UZS)</option>
                    <option value="VES" data-name="VES">VES (VES)</option>
                    <option value="VND" data-name="VND">VND (VND)</option>
                    <option value="VUV" data-name="VUV">VUV (VUV)</option>
                    <option value="WST" data-name="WST">WST (WST)</option>
                    <option value="XAF" data-name="XAF">XAF (XAF)</option>
                    <option value="XCD" data-name="XCD">XCD (XCD)</option>
                    <option value="XCG" data-name="XCG">XCG (XCG)</option>
                    <option value="XDR" data-name="XDR">XDR (XDR)</option>
                    <option value="XOF" data-name="XOF">XOF (XOF)</option>
                    <option value="XPF" data-name="XPF">XPF (XPF)</option>
                    <option value="YER" data-name="YER">YER (YER)</option>
                    <option value="ZAR" data-name="ZAR">ZAR (ZAR)</option>
                    <option value="ZMW" data-name="ZMW">ZMW (ZMW)</option>
                    <option value="ZWL" data-name="ZWL">ZWL (ZWL)</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="currency-name" class="form-label">Currency Name</label>
                <input type="text" id="currency-name" name="name" class="form-control" readonly>
            </div>
            <div class="mb-3">
                <label for="currency-code" class="form-label">Currency Code</label>
                <input type="text" id="currency-code" name="code" class="form-control" readonly>
            </div>
            <button type="submit" class="btn btn-primary">Add Currency</button>
        </form>
      </div>
    </div>
  </div>
</div>
<x-data-table
    title="Currencies"
    table-id="currencies-table"
    fetch-url="{{ route('admin.currencies.data') }}"
    :columns="['Name', 'Code' ,'Actions']"
    :columns-config="[
        ['data' => 'name', 'name' => 'name'],
        ['data' => 'code', 'code' => 'code'],
        ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
    ]"
/>

@endsection

@push('styles')

@endpush

@push('plugin-scripts')
@include('admin.currency.script.currencies')
@endpush
