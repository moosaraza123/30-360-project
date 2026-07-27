<?php

namespace Modules\DayCountCalculator\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\DayCountCalculator\Entities\Calculation;
use Modules\DayCountCalculator\Entities\SavedCalculation;

/**
 * Session-authenticated JSON endpoints backing the Saved Calculations page
 * (view details, edit, delete, toggle favorite).
 */
class SavedCalculationController extends Controller
{
    /**
     * Return a calculation's details for the details modal.
     */
    public function showCalculation(int $calculationId): JsonResponse
    {
        $calculation = Calculation::find($calculationId);

        // Only the calculation's creator, or a user who has saved it, may view
        // it. A plain 404 avoids leaking which IDs exist.
        $accessible = $calculation !== null && (
            $calculation->isOwnedBy(auth()->user())
            || $calculation->savedCalculations()->where('user_id', auth()->id())->exists()
        );

        if (! $accessible) {
            return response()->json(['message' => 'Calculation not found.'], 404);
        }

        return response()->json([
            'convention_type' => $calculation->convention_type,
            'start_date' => $calculation->start_date->toDateString(),
            'end_date' => $calculation->end_date->toDateString(),
            'days_calculated' => $calculation->days_calculated,
            'day_count_factor' => $calculation->day_count_factor,
            'interest_amount' => $calculation->interest_amount,
            'calculation_steps' => $calculation->calculation_steps,
        ]);
    }

    /**
     * Update a saved calculation's name, notes and favorite flag.
     */
    public function update(Request $request, int $savedCalculationId): JsonResponse
    {
        $saved = $this->findOwned($savedCalculationId);

        if (! $saved) {
            return response()->json(['success' => false, 'message' => 'Saved calculation not found.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'is_favorite' => 'nullable|boolean',
        ]);

        $saved->update([
            'name' => $validated['name'],
            'notes' => $validated['notes'] ?? null,
            'is_favorite' => $request->boolean('is_favorite'),
        ]);

        return response()->json(['success' => true, 'saved_calculation' => $saved]);
    }

    /**
     * Delete a saved calculation (the underlying calculation is kept).
     */
    public function destroy(int $savedCalculationId): JsonResponse
    {
        $saved = $this->findOwned($savedCalculationId);

        if (! $saved) {
            return response()->json(['success' => false, 'message' => 'Saved calculation not found.'], 404);
        }

        $saved->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Toggle the favorite flag.
     */
    public function toggleFavorite(int $savedCalculationId): JsonResponse
    {
        $saved = $this->findOwned($savedCalculationId);

        if (! $saved) {
            return response()->json(['success' => false, 'message' => 'Saved calculation not found.'], 404);
        }

        $saved->toggleFavorite();

        return response()->json(['success' => true, 'is_favorite' => $saved->is_favorite]);
    }

    private function findOwned(int $id): ?SavedCalculation
    {
        return SavedCalculation::byUser(auth()->id())->find($id);
    }
}
