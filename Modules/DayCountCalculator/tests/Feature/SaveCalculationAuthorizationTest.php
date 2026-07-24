<?php

namespace Modules\DayCountCalculator\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\DayCountCalculator\Entities\Calculation;
use Tests\TestCase;

/**
 * Regression tests for the save-calculation ownership check (IDOR fix).
 */
class SaveCalculationAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function makeCalculation(array $attributes = []): Calculation
    {
        return Calculation::create(array_merge([
            'convention_type' => '30/360 US',
            'start_date' => '2024-01-01',
            'end_date' => '2024-07-01',
            'days_calculated' => 180,
            'day_count_factor' => 0.5,
            'calculation_steps' => [],
        ], $attributes));
    }

    public function test_guest_cannot_save_a_calculation(): void
    {
        $calculation = $this->makeCalculation();

        $this->postJson(route('calculator.save', $calculation->id), ['name' => 'Mine'])
            ->assertStatus(401);
    }

    public function test_user_can_save_own_calculation(): void
    {
        $user = User::factory()->create();
        $calculation = $this->makeCalculation(['user_id' => $user->id]);

        $this->actingAs($user)
            ->postJson(route('calculator.save', $calculation->id), ['name' => 'Mine'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('saved_calculations', [
            'user_id' => $user->id,
            'calculation_id' => $calculation->id,
        ]);
    }

    public function test_user_cannot_save_another_users_calculation(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $calculation = $this->makeCalculation(['user_id' => $owner->id]);

        $this->actingAs($attacker)
            ->postJson(route('calculator.save', $calculation->id), ['name' => 'Stolen'])
            ->assertStatus(404);

        $this->assertDatabaseCount('saved_calculations', 0);
    }

    public function test_user_can_save_calculation_from_own_guest_session(): void
    {
        $user = User::factory()->create();
        $calculation = $this->makeCalculation(['user_id' => null, 'session_id' => 'guest-session-1']);

        $this->actingAs($user)
            ->withSession(['calculator_session_id' => 'guest-session-1'])
            ->postJson(route('calculator.save', $calculation->id), ['name' => 'Pre-login calc'])
            ->assertOk();
    }

    public function test_user_cannot_save_calculation_from_foreign_guest_session(): void
    {
        $user = User::factory()->create();
        $calculation = $this->makeCalculation(['user_id' => null, 'session_id' => 'someone-elses-session']);

        $this->actingAs($user)
            ->withSession(['calculator_session_id' => 'guest-session-2'])
            ->postJson(route('calculator.save', $calculation->id), ['name' => 'Enumerated'])
            ->assertStatus(404);

        $this->assertDatabaseCount('saved_calculations', 0);
    }
}
