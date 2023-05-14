<?php

namespace App\Services;

use GuzzleHttp\Client;

class RapidApi
{
    protected $client;


    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function fetchCompanyData($symbol)
    {
        $rapidApiEndpoint = config('services.xm.rapid_api_endpoint');
        $rapidApiHost = config('services.xm.api_host');
        $rapidApiKey = config('services.xm.api_key');
        $headers = [
            'x-rapidapi-key' => $rapidApiKey,
            'x-rapidapi-host' => $rapidApiHost
        ];

        $query = ['symbol' => $symbol];

        $response = $this->client->request('GET', $rapidApiEndpoint, [
            'headers' => $headers,
            'query' => $query
        ]);

        $responseBody = json_decode($response->getBody(), true);

        if (empty($responseBody)) {
            throw new \Exception('No data found');
        }

        return $responseBody['prices'];
    }
}
