<?php

namespace Tests\Feature;

use App\Services\NasdaqApi;
use App\Services\RapidApi;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class FetchDataControllerTest extends TestCase
{
    /**
     * 
     *
     * @return void
     */
    public function test_home()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
    public function test_view()
    {
        $response = $this->get('/');
        $response->assertViewIs('index');
    }
    public function test_nasdaq_api()
    {

        $nasdaqApi = new NasdaqApi(new Client());

        // Call the method under test
        $companySymbol = 'AAPL';
        $companyData = $nasdaqApi->fetchCompanySymbolData($companySymbol);

        // Assert that the company data returned matches the expected result
        $expectedCompanyData = [
            ['Symbol' => 'AAPL', 'Company Name' => 'Apple Inc.'],
        ];
        $this->assertNotEmpty($companyData);
        $this->assertCount(1, $companyData);
        $this->assertArrayHasKey('Symbol', $companyData[0]);
        $this->assertArrayHasKey('Company Name', $companyData[0]);
        $this->assertEquals($expectedCompanyData[0]['Symbol'], $companyData[0]['Symbol']);
        $this->assertEquals($expectedCompanyData[0]['Company Name'], $companyData[0]['Company Name']);
    }
    public function testFetchCompanyDataThrowsExceptionWhenNoDataFound()
    {
        // Mock the Guzzle client with a predefined response of empty body
        $mockResponse = new Response(200, [], '');
        $mockHandler = new MockHandler([$mockResponse]);
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        // Create an instance of the RapidApi class
        $rapidApi = new RapidApi($client);

        // Call the method under test
        $symbol = 'AAPLXXX';

        // Assert that an exception is thrown when no data is found
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No data found');

        $rapidApi->fetchCompanyData($symbol);
    }
    public function testFetchCompanyDataReturnsPrices()
    {
        // Mock the Guzzle client with a predefined response
        $mockResponse = new Response(200, [], '{"prices": [{"date": "2023-05-01", "open": 100, "close": 105, "high": 110, "low": 95}]}');
        $mockHandler = new MockHandler([$mockResponse]);
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        // Create an instance of the RapidApi class
        $rapidApi = new RapidApi($client);

        // Call the method under test
        $symbol = 'AAPL';
        $companyData = $rapidApi->fetchCompanyData($symbol);

        // Assert that the company data contains the expected prices
        $expectedPrices = [
            ['date' => '2023-05-01', 'open' => 100, 'close' => 105, 'high' => 110, 'low' => 95],
        ];

        $this->assertNotEmpty($companyData);
        $this->assertCount(1, $companyData);
        $this->assertEquals($expectedPrices, $companyData);
    }
    public function testFetchApi()
    {
        // Mock the dependencies
        $nasdaqApi = $this->mock(NasdaqApi::class);
        $rapidApi = $this->mock(RapidApi::class);

        // Set up the test data
        $companySymbol = 'AAPL';
        $startDate = '2023-01-01';
        $endDate = '2023-02-01';
        $email = 'test@example.com';

        // Mock the fetchCompanySymbolData method of NasdaqApi
        $nasdaqApi->shouldReceive('fetchCompanySymbolData')
            ->with($companySymbol)
            ->andReturn([
                ['Symbol' => $companySymbol, 'Company Name' => 'Apple Inc.']
            ]);

        // Mock the fetchCompanyData method of RapidApi
        $rapidApi->shouldReceive('fetchCompanyData')
            ->with($companySymbol)
            ->andReturn([
                ['date' => '2023-01-01', 'open' => 100, 'close' => 120, 'high' => 130, 'low' => 90,'volume'=> 12 ],
                ['date' => '2023-01-02', 'open' => 110, 'close' => 115, 'high' => 120, 'low' => 105 ,'volume'=> 12]
            ]);



        // Make a request to the fetchApi endpoint
        $response = $this->post('/fetch', [
            'company_symbol' => $companySymbol,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'email' => $email
        ]);

        // Assert that the response has a successful status code
        $response->assertStatus(200);

        // Assert that the response contains the expected view
        $response->assertViewIs('result');

        // Assert that the response contains the expected data
        $response->assertViewHas('result', [
            'api_data' => [
                ['date' => '2023-01-01', 'open' => 100, 'close' => 120, 'high' => 130, 'low' => 90,'volume'=> 12],
                ['date' => '2023-01-02', 'open' => 110, 'close' => 115, 'high' => 120, 'low' => 105,'volume'=> 12]
            ],
            'company_name' => 'Apple Inc.',
            'chart_data' => [
                'labels' => ['2023-01-01', '2023-01-02'],
                'openPrices' => [100, 110],
                'closePrices' => [120, 115],
                'highPrices' => [130, 120],
                'lowPrices' => [90, 105]
            ]
        ]);
    }

}
