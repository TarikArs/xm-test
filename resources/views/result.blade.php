<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Result</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/css/bootstrap.min.css" integrity="sha512-Ez0cGzNzHR1tYAv56860NLspgUGuQw16GiOOp/I2LuTmpSK9xDXlgJz3XN4cnpXWDmkNBKXR/VDMTCnAaEooxA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-core.min.js"></script>
    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-stock.min.js"></script>
    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-data-adapter.min.js"></script>

    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-ui.min.js"></script>
    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-exports.min.js"></script>

    <link href="https://cdn.anychart.com/releases/8.11.0/css/anychart-ui.min.css" type="text/css" rel="stylesheet">
    <link href="https://cdn.anychart.com/releases/8.11.0/fonts/css/anychart-font.min.css" type="text/css" rel="stylesheet">




    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        table {
            font-size: 12px;
        }

        .table-container {
            width: 100%;
            max-height: 500px;
            overflow: auto;
        }
    </style>
</head>
@php

$chartData = $result['chart_data'];
$companyName = $result['company_name'];
$api_data = $result['api_data'];

@endphp

<body>
    <div class="form-container">
        <h3>{{$companyName}}</h3>
        <div class="result d-flex">
            <div class="table-container">
                <table class="table table-responsive table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Open</th>
                            <th>Heigh</th>
                            <th>Low</th>
                            <th>Close</th>
                            <th>Volume</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($api_data as $data)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($data['date'])->format('Y-m-d')  }}</td>
                            <td>{{number_format($data['open'] ?? 0,2)}}</td>
                            <td>{{number_format($data['high'] ?? 0,2)}}</td>
                            <td>{{number_format($data['low'] ?? 0,2)}}</td>
                            <td>{{number_format($data['close'] ?? 0,2)}}</td>
                            <td>{{number_format($data['volume'] ?? 0,2)}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div id="container" style="width: 100%; height: 500px;"></div>

        </div>
    </div>
</body>

<script>
    
    anychart.onDocumentReady(function() {
        var chartData = @json($chartData); // Pass the formatted data to JavaScript

        var dataLength = chartData.labels.length;
        var openPrices = chartData.openPrices;
        var closePrices = chartData.closePrices;
        var highPrices = chartData.highPrices;
        var lowPrices = chartData.lowPrices;

        var dataTable = anychart.data.table();

        // Create data table on loaded data
        var rows = [];
        for (var i = 0; i < dataLength; i++) {
            rows.push([chartData.labels[i], openPrices[i], highPrices[i], lowPrices[i], closePrices[i]]);
        }
        dataTable.addData(rows);

        // Map loaded data for the candlestick series
        var mapping = dataTable.mapAs({
            open: 1,
            high: 2,
            low: 3,
            close: 4

        });

        // Create stock chart
        var chart = anychart.stock();

        // Create first plot on the chart
        var plot = chart.plot(0);

        // Set grid settings
        plot.yGrid(true).xGrid(true).yMinorGrid(true).xMinorGrid(true);

        var series = plot.candlestick(mapping)
            .name('Stock');
        series.legendItem().iconType('rising-falling');

        // Create scroller series with mapped data
        chart.scroller().candlestick(mapping);

        // Set chart title
        chart.title('Stock Chart');

        // Set container id for the chart
        chart.container('container');

        // Initiate chart drawing
        chart.draw();
    });
    
</script>


</html>