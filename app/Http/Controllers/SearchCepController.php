<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SearchCepController extends Controller
{
    public function __invoke(string $cep)
    {
        $key = "CEP_$cep";

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $response = Http::get("https://viacep.com.br/ws/$cep/json/");

        if ($this->searchHasFailed($response)) {
            $code = $response->clientError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_BAD_GATEWAY; 

            return response()->json([
                'message' => __('The provided CEP :cep could not be found.', ['cep' => $cep]),
            ], $code);
        }

        Cache::put($key, $response->json());

        return $response->json();
    }

    private function searchHasFailed($response): bool
    {
        return $response->failed() || ($response->json()['erro'] ?? false);
    }
}
