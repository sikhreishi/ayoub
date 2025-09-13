<?php

namespace App\Services\Currency;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    protected $baseCurrency = 'USD';
    protected $exchangeRates = [];

    public function __construct()
    {
        $this->loadExchangeRates();
    }

    protected function loadExchangeRates()
    {

    $this->exchangeRates = Cache::remember('exchange_rates', 900, function () {
        try {
            // Use a more reliable free exchange rate API
            $response = Http::timeout(10)->get('https://open.er-api.com/v6/latest/USD');
            
            if ($response->successful() && $response->json()['result'] === 'success') {
                return $response->json()['rates'];
            }
            
            // Fallback to another API if first one fails
            $response = Http::timeout(10)->get('https://api.exchangerate.host/latest?base=USD');
            
            if ($response->successful() && isset($response->json()['rates'])) {
                return $response->json()['rates'];
            }
            
            throw new \Exception('All exchange rate APIs failed');
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch exchange rates: ' . $e->getMessage());
            
            return [];
        }
    });
    
    // If API failed, try to get rates from database cache
    if (empty($this->exchangeRates)) {
        $this->exchangeRates = Cache::remember('fallback_exchange_rates', 3600, function () {

            return [
                'USD' => 1,
                'JOD' => 0.709,
                'SYP' => 13000,
            ];
        });
    }
}

public function convertFromUSD(float $amount, string $targetCurrency): float
{
    $targetCurrency = strtoupper($targetCurrency);
    
    if (!isset($this->exchangeRates[$targetCurrency])) {
        Log::warning("Unknown target currency: {$targetCurrency}");
        return $amount; // Return original amount if currency not found
    }
    
    $rate = $this->exchangeRates[$targetCurrency];
    return $amount * $rate;
}

public function convertToUSD(float $amount, string $sourceCurrency): float
{
    $sourceCurrency = strtoupper($sourceCurrency);
    
    if (!isset($this->exchangeRates[$sourceCurrency])) {
        Log::warning("Unknown source currency: {$sourceCurrency}");
        return $amount; 
    }
    
    $rate = $this->exchangeRates[$sourceCurrency];
    return $amount / $rate;
}

public function convert(float $amount, string $fromCurrency, string $toCurrency): float
{
    $fromCurrency = strtoupper($fromCurrency);
    $toCurrency = strtoupper($toCurrency);
    
    if ($fromCurrency === $toCurrency) {
        return $amount;
    }
    
    // First convert to USD, then to target currency
    $amountInUSD = $fromCurrency === 'USD' 
        ? $amount 
        : $this->convertToUSD($amount, $fromCurrency);
        
    return $toCurrency === 'USD' 
        ? $amountInUSD 
        : $this->convertFromUSD($amountInUSD, $toCurrency);
}

public function formatMoney(float $amount, string $currencyCode): string
{
    $currencyCode = strtoupper($currencyCode);
    
    $symbols = [
        'USD' => '$',
        'JOD' => 'د.أ',
        'SYP' => '£S',
    ];
    
    $symbol = $symbols[$currencyCode] ?? $currencyCode . ' ';
    
    // Format based on currency
    switch ($currencyCode) {
        case 'JOD':
            return $symbol . number_format($amount, 3, '.', ',');
        case 'SYP':
            return $symbol . number_format($amount, 0, '.', ',');
        default:
            return $symbol . number_format($amount, 2, '.', ',');
    }
}

public function getExchangeRate(string $fromCurrency, string $toCurrency): float
{
    $fromCurrency = strtoupper($fromCurrency);
    $toCurrency = strtoupper($toCurrency);
    
    if ($fromCurrency === $toCurrency) {
        return 1.0;
    }
    
    if ($fromCurrency === 'USD') {
        return $this->exchangeRates[$toCurrency] ?? 1.0;
    }
    
    if ($toCurrency === 'USD') {
        return 1 / ($this->exchangeRates[$fromCurrency] ?? 1.0);
    }
    
    $usdRate = 1 / ($this->exchangeRates[$fromCurrency] ?? 1.0);
    return $usdRate * ($this->exchangeRates[$toCurrency] ?? 1.0);
}

public function getExchangeRates(): array
{
    return $this->exchangeRates;
}

public function isValidCurrency(string $currencyCode): bool
{
    $currencyCode = strtoupper($currencyCode);
    return isset($this->exchangeRates[$currencyCode]) || $currencyCode === 'USD';
}


public function refreshRates(): bool
{
    Cache::forget('exchange_rates');
    $this->loadExchangeRates();
    return !empty($this->exchangeRates);
}
}