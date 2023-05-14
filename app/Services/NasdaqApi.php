<?php

namespace App\Services;

use GuzzleHttp\Client;

class NasdaqApi
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function fetchCompanySymbolData($companySymbol)
    {
        $endpoint =  config('services.xm.nasdaq_api_endpoint');

        $response = $this->client->request('GET', $endpoint);
        $responseBody = json_decode($response->getBody(), true);

        $companyData = array_filter($responseBody, function ($value) use ($companySymbol) {
            return $value['Symbol'] === $companySymbol;
        });

        return array_values($companyData);
    }
}
