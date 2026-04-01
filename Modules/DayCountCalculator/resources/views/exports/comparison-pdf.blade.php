<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Day Count Convention Comparison</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 5px;
        }
        .subtitle {
            color: #64748b;
            font-size: 14px;
        }
        .info-section {
            background-color: #f8fafc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #475569;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #3b82f6;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .monospace {
            font-family: 'Courier New', monospace;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 15px;
            background-color: #f1f5f9;
            text-align: center;
            border-right: 2px solid white;
        }
        .stat-box:last-child {
            border-right: none;
        }
        .stat-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            padding: 10px 0;
            border-top: 1px solid #e2e8f0;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Day Count Calculator</div>
        <div class="subtitle">Convention Comparison Report</div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Start Date:</span>
            {{ $comparison->startDate->format('F j, Y') }}
        </div>
        <div class="info-row">
            <span class="info-label">End Date:</span>
            {{ $comparison->endDate->format('F j, Y') }}
        </div>
        @if($comparison->principal)
            <div class="info-row">
                <span class="info-label">Principal:</span>
                ${{ number_format($comparison->principal, 2) }}
            </div>
        @endif
        @if($comparison->interestRate)
            <div class="info-row">
                <span class="info-label">Interest Rate:</span>
                {{ number_format($comparison->interestRate * 100, 3) }}%
            </div>
        @endif
        <div class="info-row">
            <span class="info-label">Generated:</span>
            {{ now()->format('F j, Y \a\t g:i A') }}
        </div>
    </div>

    <h3>Statistics</h3>
    <div class="stats-grid">
        @php $stats = $comparison->getStatistics(); @endphp
        <div class="stat-box">
            <div class="stat-label">Min Days</div>
            <div class="stat-value">{{ $stats['min_days'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Max Days</div>
            <div class="stat-value">{{ $stats['max_days'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Days Range</div>
            <div class="stat-value">{{ $stats['days_range'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Conventions</div>
            <div class="stat-value">{{ count($comparison->results) }}</div>
        </div>
    </div>

    <h3>Comparison Results</h3>
    <table>
        <thead>
            <tr>
                <th>Convention</th>
                <th class="text-right">Days</th>
                <th class="text-right">Day Count Factor</th>
                @if($comparison->principal && $comparison->interestRate)
                    <th class="text-right">Interest Amount</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($comparison->results as $conventionType => $result)
                <tr>
                    <td><strong>{{ $conventionType }}</strong></td>
                    <td class="text-right monospace">{{ $result->days }}</td>
                    <td class="text-right monospace">{{ $result->getFormattedFactor(10) }}</td>
                    @if($result->interestAmount !== null)
                        <td class="text-right monospace">${{ number_format($result->interestAmount, 2) }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(!empty($comparison->getSignificantDifferences()))
        <h3>Significant Differences</h3>
        <p style="margin-bottom: 10px;">Conventions with 5+ day differences:</p>
        <ul>
            @foreach($comparison->getSignificantDifferences() as $conv => $days)
                <li><strong>{{ $conv }}</strong>: {{ $days }} days difference from minimum</li>
            @endforeach
        </ul>
    @endif

    <div class="footer">
        <p>
            Generated by Day Count Calculator | {{ url('/') }}<br>
            Professional Financial Calculation Tools
        </p>
    </div>
</body>
</html>
