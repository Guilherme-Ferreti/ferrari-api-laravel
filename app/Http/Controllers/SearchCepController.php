<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class SearchCepController extends Controller
{
    public function __invoke(string $cep)
    {
        $response = Http::get("https://viacep.com.br/ws/$cep/json/");

        if ($response->failed()) {
            $code = $response->clientError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_BAD_GATEWAY; 

            return response()->json([
                'message' => __('The provided CEP :cep could not be found.', ['cep' => $cep]),
            ], $code);
        }

        return $response->json();
    }
}
