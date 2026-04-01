<?php

namespace Modules\DayCountCalculator\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\DayCountCalculator\DTOs\CalculationRequest as CalculationRequestDTO;
use Modules\DayCountCalculator\DTOs\ComparisonResult;
use Modules\DayCountCalculator\Features\DayCount\Calculate30360USFeature;
use Modules\DayCountCalculator\Features\DayCount\Calculate30E360Feature;
use Modules\DayCountCalculator\Features\DayCount\Calculate30E360ISDAFeature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActual360Feature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActual364Feature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActual365Feature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActualActualFeature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActualActualISDAFeature;
use Modules\DayCountCalculator\Features\DayCount\Calculate30360BondBasisFeature;
use Modules\DayCountCalculator\Http\Requests\CompareConventionsRequest;
use Carbon\Carbon;

/**
 * ComparisonController
 *
 * Handles day count convention comparison
 */
class ComparisonController extends Controller
{
    /**
     * Display comparison tool interface
     */
    public function index()
    {
        return view('daycountcalculator::comparison.index', [
            'conventions' => $this->getAvailableConventions(),
        ]);
    }

    /**
     * Compare multiple conventions
     */
    public function compare(CompareConventionsRequest $request)
    {
        // Create DTO from validated request
        $calculationRequest = CalculationRequestDTO::fromArray([
            'convention_type' => '', // Not used in comparison
            'start_date' => Carbon::parse($request->input('start_date')),
            'end_date' => Carbon::parse($request->input('end_date')),
            'principal' => $request->input('principal'),
            'interest_rate' => $request->input('interest_rate') ? $request->input('interest_rate') / 100 : null,
        ]);

        // Get selected conventions or all if none selected
        $selectedConventions = $request->getSelectedConventions();

        // Calculate results for each convention
        $results = [];
        foreach ($selectedConventions as $conventionType) {
            $feature = $this->getCalculatorFeature($conventionType);
            $requestWithConvention = CalculationRequestDTO::fromArray([
                'convention_type' => $conventionType,
                'start_date' => $calculationRequest->startDate,
                'end_date' => $calculationRequest->endDate,
                'principal' => $calculationRequest->principal,
                'interest_rate' => $calculationRequest->interestRate,
            ]);

            $results[$conventionType] = $feature->execute($requestWithConvention);
        }

        // Create comparison result
        $comparisonResult = new ComparisonResult(
            startDate: $calculationRequest->startDate,
            endDate: $calculationRequest->endDate,
            results: $results,
            principal: $calculationRequest->principal,
            interestRate: $calculationRequest->interestRate
        );

        // Return response based on request type
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'comparison' => [
                    'start_date' => $comparisonResult->startDate->format('Y-m-d'),
                    'end_date' => $comparisonResult->endDate->format('Y-m-d'),
                    'principal' => $comparisonResult->principal,
                    'interest_rate' => $comparisonResult->interestRate,
                    'results' => array_map(function ($result) {
                        return [
                            'convention_type' => $result->conventionType,
                            'days' => $result->days,
                            'day_count_factor' => $result->dayCountFactor,
                            'day_count_factor_formatted' => $result->getFormattedFactor(),
                            'interest_amount' => $result->interestAmount,
                            'interest_amount_formatted' => $result->interestAmount ? '$' . number_format($result->interestAmount, 2) : null,
                        ];
                    }, $comparisonResult->results),
                    'statistics' => $comparisonResult->getStatistics(),
                    'significant_differences' => $comparisonResult->getSignificantDifferences(),
                ],
            ]);
        }

        return view('daycountcalculator::comparison.results', [
            'comparison' => $comparisonResult,
            'conventions' => $this->getAvailableConventions(),
        ]);
    }

    /**
     * Export comparison results
     */
    public function export(Request $request, string $format)
    {
        $request->validate([
            'comparison_data' => 'required|json',
        ]);

        $comparisonData = json_decode($request->input('comparison_data'), true);

        // Recreate comparison from JSON data
        $results = [];
        foreach ($comparisonData['results'] as $conventionType => $resultData) {
            $results[$conventionType] = new \Modules\DayCountCalculator\DTOs\CalculationResult(
                days: $resultData['days'],
                dayCountFactor: $resultData['day_count_factor'],
                interestAmount: $resultData['interest_amount'],
                steps: [],
                conventionType: $conventionType
            );
        }

        $comparison = new ComparisonResult(
            startDate: Carbon::parse($comparisonData['start_date']),
            endDate: Carbon::parse($comparisonData['end_date']),
            results: $results,
            principal: $comparisonData['principal'] ?? null,
            interestRate: $comparisonData['interest_rate'] ?? null
        );

        return match (strtolower($format)) {
            'pdf' => $this->exportPdf($comparison),
            'csv' => $this->exportCsv($comparison),
            'excel' => $this->exportExcel($comparison),
            default => abort(400, 'Invalid export format'),
        };
    }

    /**
     * Export comparison to PDF
     */
    private function exportPdf(ComparisonResult $comparison)
    {
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('daycountcalculator::exports.comparison-pdf', [
            'comparison' => $comparison,
        ]);

        $filename = sprintf(
            'comparison_%s_to_%s.pdf',
            $comparison->startDate->format('Y-m-d'),
            $comparison->endDate->format('Y-m-d')
        );

        return $pdf->download($filename);
    }

    /**
     * Export comparison to CSV
     */
    private function exportCsv(ComparisonResult $comparison)
    {
        $filename = sprintf(
            'comparison_%s_to_%s.csv',
            $comparison->startDate->format('Y-m-d'),
            $comparison->endDate->format('Y-m-d')
        );

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($comparison) {
            $file = fopen('php://output', 'w');

            // Header row
            $headerRow = ['Convention', 'Days', 'Day Count Factor'];
            if ($comparison->principal && $comparison->interestRate) {
                $headerRow[] = 'Interest Amount';
            }
            fputcsv($file, $headerRow);

            // Data rows
            foreach ($comparison->results as $conventionType => $result) {
                $row = [
                    $conventionType,
                    $result->days,
                    number_format($result->dayCountFactor, 10),
                ];
                if ($result->interestAmount !== null) {
                    $row[] = number_format($result->interestAmount, 2);
                }
                fputcsv($file, $row);
            }

            // Statistics
            fputcsv($file, []);
            fputcsv($file, ['Statistics']);
            $stats = $comparison->getStatistics();
            fputcsv($file, ['Min Days', $stats['min_days']]);
            fputcsv($file, ['Max Days', $stats['max_days']]);
            fputcsv($file, ['Days Range', $stats['days_range']]);
            fputcsv($file, ['Min Factor', number_format($stats['min_factor'], 10)]);
            fputcsv($file, ['Max Factor', number_format($stats['max_factor'], 10)]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export comparison to Excel
     */
    private function exportExcel(ComparisonResult $comparison)
    {
        // This will be implemented with maatwebsite/excel package
        // For now, redirect to CSV export
        return $this->exportCsv($comparison);
    }

    /**
     * Get calculator feature instance based on convention type
     */
    private function getCalculatorFeature(string $conventionType): object
    {
        return match ($conventionType) {
            '30/360 US' => new Calculate30360USFeature(),
            '30/360 Bond Basis' => new Calculate30360BondBasisFeature(),
            '30E/360' => new Calculate30E360Feature(),
            '30E/360 ISDA' => new Calculate30E360ISDAFeature(),
            'Actual/365 Fixed' => new CalculateActual365Feature(),
            'Actual/360' => new CalculateActual360Feature(),
            'Actual/364' => new CalculateActual364Feature(),
            'Actual/Actual' => new CalculateActualActualFeature(),
            'Actual/Actual ISDA' => new CalculateActualActualISDAFeature(),
            default => throw new \InvalidArgumentException("Unknown convention type: {$conventionType}"),
        };
    }

    /**
     * Get list of available conventions
     */
    private function getAvailableConventions(): array
    {
        return [
            '30/360 US',
            '30/360 Bond Basis',
            '30E/360',
            '30E/360 ISDA',
            'Actual/365 Fixed',
            'Actual/360',
            'Actual/364',
            'Actual/Actual',
            'Actual/Actual ISDA',
        ];
    }
}
