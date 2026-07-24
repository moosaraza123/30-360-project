<?php

namespace Modules\DayCountCalculator\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\DayCountCalculator\DTOs\CalculationRequest as CalculationRequestDTO;
use Modules\DayCountCalculator\Features\DayCount\Calculate30360BondBasisFeature;
use Modules\DayCountCalculator\Features\DayCount\Calculate30360USFeature;
use Modules\DayCountCalculator\Features\DayCount\Calculate30E360Feature;
use Modules\DayCountCalculator\Features\DayCount\Calculate30E360ISDAFeature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActual360Feature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActual364Feature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActual365Feature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActualActualFeature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActualActualISDAFeature;
use Modules\DayCountCalculator\Http\Requests\CalculateDayCountRequest;
use Modules\DayCountCalculator\Repositories\CalculationRepository;

/**
 * CalculatorController
 *
 * Handles day count calculation requests
 */
class CalculatorController extends Controller
{
    public function __construct(
        private CalculationRepository $calculationRepository
    ) {}

    /**
     * Display the calculator interface
     */
    public function index()
    {
        $sessionId = $this->getOrCreateSessionId();
        $recentCalculations = $this->calculationRepository->getRecentBySession($sessionId, 5);

        return view('daycountcalculator::calculator.index', [
            'recentCalculations' => $recentCalculations,
            'conventions' => $this->getConventionsInfo(),
        ]);
    }

    /**
     * Process calculation request
     */
    public function calculate(CalculateDayCountRequest $request)
    {
        // Create DTO from validated request
        $calculationRequest = CalculationRequestDTO::fromArray([
            'convention_type' => $request->input('convention_type'),
            'start_date' => Carbon::parse($request->input('start_date')),
            'end_date' => Carbon::parse($request->input('end_date')),
            'principal' => $request->input('principal'),
            'interest_rate' => $request->input('interest_rate') ? $request->input('interest_rate') / 100 : null,
            'apply_eom_adjustment' => $request->boolean('apply_eom_adjustment', false),
            'end_date_is_maturity' => $request->boolean('end_date_is_maturity', false),
        ]);

        // Execute appropriate calculator feature
        $feature = $this->getCalculatorFeature($calculationRequest->conventionType);
        $result = $feature->execute($calculationRequest);

        // Store calculation in database
        $calculation = $this->calculationRepository->store(
            result: $result,
            request: $calculationRequest,
            sessionId: $this->getOrCreateSessionId(),
            userId: auth()->id(),
            ipAddress: $request->ip()
        );

        // Return response based on request type (AJAX vs regular)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'result' => [
                    'id' => $calculation->id,
                    'convention_type' => $result->conventionType,
                    'days' => $result->days,
                    'day_count_factor' => $result->dayCountFactor,
                    'day_count_factor_formatted' => $result->getFormattedFactor(),
                    'interest_amount' => $result->interestAmount,
                    'interest_amount_formatted' => $result->interestAmount ? '$'.number_format($result->interestAmount, 2) : null,
                    'steps' => $result->steps,
                    'applied_steps' => $result->getAppliedSteps(),
                ],
            ]);
        }

        return redirect()
            ->route('calculator.index')
            ->with('result', $result)
            ->with('calculation_id', $calculation->id);
    }

    /**
     * Display calculation history
     */
    public function history(Request $request)
    {
        if (auth()->check()) {
            $calculations = $this->calculationRepository->getRecentByUser(auth()->id(), 50);
        } else {
            $sessionId = $this->getOrCreateSessionId();
            $calculations = $this->calculationRepository->getRecentBySession($sessionId, 50);
        }

        return view('daycountcalculator::calculator.history', [
            'calculations' => $calculations,
        ]);
    }

    /**
     * Display educational content for a convention
     */
    public function educate(string $conventionType)
    {
        $conventionInfo = $this->getConventionInfo($conventionType);

        if (! $conventionInfo) {
            abort(404, 'Convention not found');
        }

        return view('daycountcalculator::educational.convention', [
            'convention' => $conventionInfo,
        ]);
    }

    /**
     * Save a calculation (authenticated users only)
     */
    public function save(Request $request, int $calculationId)
    {
        if (! auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to save calculations.',
            ], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'is_favorite' => 'nullable|boolean',
        ]);

        $calculation = $this->calculationRepository->findById($calculationId);

        if (! $calculation) {
            return response()->json([
                'success' => false,
                'message' => 'Calculation not found.',
            ], 404);
        }

        // Only the owner may save: either the authenticated creator, or the
        // guest session that produced the calculation before logging in.
        $sessionId = Session::get('calculator_session_id');
        $ownsCalculation = ($calculation->user_id !== null && $calculation->user_id === auth()->id())
            || ($calculation->user_id === null && $sessionId !== null && $calculation->session_id === $sessionId);

        if (! $ownsCalculation) {
            return response()->json([
                'success' => false,
                'message' => 'Calculation not found.',
            ], 404);
        }

        $savedCalculation = $calculation->savedCalculations()->create([
            'user_id' => auth()->id(),
            'name' => $request->input('name'),
            'notes' => $request->input('notes'),
            'is_favorite' => $request->boolean('is_favorite', false),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Calculation saved successfully.',
            'saved_calculation' => $savedCalculation,
        ]);
    }

    /**
     * Display saved calculations (authenticated users only)
     */
    public function savedCalculations()
    {
        if (! auth()->check()) {
            return redirect()->route('login')
                ->with('message', 'Please log in to view saved calculations.');
        }

        $savedCalculations = auth()->user()
            ->savedCalculations()
            ->with('calculation')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('daycountcalculator::calculator.saved', [
            'savedCalculations' => $savedCalculations,
        ]);
    }

    /**
     * Get or create session ID for guest users
     */
    private function getOrCreateSessionId(): string
    {
        if (! Session::has('calculator_session_id')) {
            Session::put('calculator_session_id', bin2hex(random_bytes(20)));
        }

        return Session::get('calculator_session_id');
    }

    /**
     * Get calculator feature instance based on convention type
     */
    private function getCalculatorFeature(string $conventionType): object
    {
        return match ($conventionType) {
            '30/360 US' => new Calculate30360USFeature,
            '30/360 Bond Basis' => new Calculate30360BondBasisFeature,
            '30E/360' => new Calculate30E360Feature,
            '30E/360 ISDA' => new Calculate30E360ISDAFeature,
            'Actual/365 Fixed' => new CalculateActual365Feature,
            'Actual/360' => new CalculateActual360Feature,
            'Actual/364' => new CalculateActual364Feature,
            'Actual/Actual' => new CalculateActualActualFeature,
            'Actual/Actual ISDA' => new CalculateActualActualISDAFeature,
            default => throw new \InvalidArgumentException("Unknown convention type: {$conventionType}"),
        };
    }

    /**
     * Get information about all conventions (from module config — single source of truth)
     */
    private function getConventionsInfo(): array
    {
        return config('daycountcalculator.conventions', []);
    }

    /**
     * Get information about a specific convention by canonical type or URL slug
     */
    private function getConventionInfo(string $typeOrSlug): ?array
    {
        foreach ($this->getConventionsInfo() as $convention) {
            if ($convention['type'] === $typeOrSlug || $convention['slug'] === $typeOrSlug) {
                return $convention;
            }
        }

        return null;
    }
}
