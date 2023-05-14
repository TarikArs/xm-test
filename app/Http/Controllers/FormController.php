<?php

namespace App\Http\Controllers;

use App\Helpers\DataFilterHelper;
use App\Http\Requests\FetchDataRequest;
use App\Services\NasdaqApi;
use App\Services\RapidApi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class FormController extends Controller
{
    //
    public function index()
    {
        return view('index');
    }

    public function fetchApi(FetchDataRequest $request, NasdaqApi $nasdaqApi, RapidApi $rapidApi)
    {

        try {
            $companySymbol = $request->input('company_symbol');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $email = $request->input('email');

            ///////////////////////////////////////////////////
            $companyData = $nasdaqApi->fetchCompanySymbolData($companySymbol);

            if (empty($companyData))
                throw new \Exception('Company symbol not found');
            $companyName = $companyData[0]['Company Name'] ?? '';
            ///////////////////////////////////////////////////
            $companyData = $rapidApi->fetchCompanyData($companySymbol);
            $filteredData = DataFilterHelper::filterData($companyData, $startDate, $endDate);

            $chartData = $dates = $openPrices = $closePrices = $highPrices = $lowPrices = [];
            // dd($filteredData);
            foreach ($filteredData as $price) {
                if (empty($price['open']) || empty($price['close']))
                    continue;
                $dates[] = Carbon::parse($price['date'])->format('Y-m-d');
                $openPrices[] = $price['open'];
                $closePrices[] = $price['close'];
                $highPrices[] = $price['high'];
                $lowPrices[] = $price['low'];
            }

            $chartData = [
                'labels' => $dates,
                'openPrices' => $openPrices,
                'closePrices' => $closePrices,
                'highPrices' => $highPrices,
                'lowPrices' => $lowPrices,
            ];

            $result = [
                'api_data' => $filteredData,
                'company_name' => $companyName,
                'chart_data' => $chartData,
            ];
            //////////////////////////////////////////////
            $this->sendEmail($email, $companyName, $startDate, $endDate);
            ////////////////////////////////////////////

            return view('result', ['result' => $result]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
            // dd($e->getMessage(), $e->getTrace());
        }
    }


    public function sendEmail($email, $company_name, $start_date, $end_date)
    {
        Notification::route('mail', $email)->notify(new \App\Notifications\XmNotification($company_name, $start_date, $end_date));
    }
}
