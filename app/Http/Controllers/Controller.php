<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getExchangeRate()
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; LaravelBot/1.0)',
            ])->timeout(4)->withoutVerifying()->get('https://data.mef.gov.kh/api/v1/realtime-api/exchange-rate?currency_id=USD');
            if ($response->successful() && isset($response->json()['data']['average'])) {
                $exchangeRate = $response->json()['data']['average'];

                DB::table('rates')
                    ->orderBy('id', 'desc')
                    ->limit(1)
                    ->update(['rate' => $exchangeRate]);

                // Session::flash('success', 'Exchange rate today (1 USD = ' . $exchangeRate . ' KHR)');
            } else {
                Log::warning('Exchange rate API returned an invalid response.', [
                    'response' => $response->body()
                ]);
                // Session::flash('error', "Exchange rate update failed");
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch exchange rate.', [
                'message' => $e->getMessage()
            ]);
            // Session::flash('error', "Exchange rate API error: " . $e->getMessage());
        }
    }


    public function jsonArray($data)
    {
        if (!is_array($data)) return null;
        return json_encode(collect($data));
    }
}
