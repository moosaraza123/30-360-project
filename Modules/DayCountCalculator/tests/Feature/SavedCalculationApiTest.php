<?php

namespace Modules\DayCountCalculator\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\DayCountCalculator\Entities\Calculation;
use Modules\DayCountCalculator\Entities\SavedCalculation;
use Tests\TestCase;

/**
 * Covers the session-authenticated JSON endpoints behind the Saved
 * Calculations page (view details, edit, delete, toggle favorite).
 */
class SavedCalculationApiTest extends TestCase
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
            'calculation_steps' => [
                ['title' => 'Step 1', 'description' => 'Adjust day counts', 'formula' => 'D1 = 30', 'applied' => true],
            ],
        ], $attributes));
    }

    private function makeSaved(User $user, ?Calculation $calculation = null, array $attributes = []): SavedCalculation
    {
        $calculation ??= $this->makeCalculation(['user_id' => $user->id]);

        return SavedCalculation::create(array_merge([
            'user_id' => $user->id,
            'calculation_id' => $calculation->id,
            'name' => 'My calc',
            'is_favorite' => false,
        ], $attributes));
    }

    public function test_guest_is_rejected(): void
    {
        $this->getJson('/api/calculations/1')->assertStatus(401);
        $this->putJson('/api/saved-calculations/1', ['name' => 'x'])->assertStatus(401);
        $this->deleteJson('/api/saved-calculations/1')->assertStatus(401);
        $this->postJson('/api/saved-calculations/1/toggle-favorite')->assertStatus(401);
    }

    public function test_owner_can_view_calculation_details(): void
    {
        $user = User::factory()->create();
        $calculation = $this->makeCalculation(['user_id' => $user->id]);

        $this->actingAs($user)
            ->getJson("/api/calculations/{$calculation->id}")
            ->assertOk()
            ->assertJson([
                'convention_type' => '30/360 US',
                'start_date' => '2024-01-01',
                'end_date' => '2024-07-01',
                'days_calculated' => 180,
            ])
            ->assertJsonStructure(['calculation_steps' => [['title', 'description', 'formula', 'applied']]]);
    }

    public function test_saver_can_view_guest_calculation_they_saved(): void
    {
        $user = User::factory()->create();
        $calculation = $this->makeCalculation(['user_id' => null, 'session_id' => 'guest-1']);
        $this->makeSaved($user, $calculation);

        $this->actingAs($user)
            ->getJson("/api/calculations/{$calculation->id}")
            ->assertOk();
    }

    public function test_user_cannot_view_another_users_calculation(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $calculation = $this->makeCalculation(['user_id' => $owner->id]);

        $this->actingAs($attacker)
            ->getJson("/api/calculations/{$calculation->id}")
            ->assertStatus(404);
    }

    public function test_owner_can_update_saved_calculation(): void
    {
        $user = User::factory()->create();
        $saved = $this->makeSaved($user);

        $this->actingAs($user)
            ->putJson("/api/saved-calculations/{$saved->id}", [
                'name' => 'Renamed',
                'notes' => 'Q1 accrual check',
                'is_favorite' => true,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('saved_calculations', [
            'id' => $saved->id,
            'name' => 'Renamed',
            'notes' => 'Q1 accrual check',
            'is_favorite' => true,
        ]);
    }

    public function test_user_cannot_update_another_users_saved_calculation(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $saved = $this->makeSaved($owner);

        $this->actingAs($attacker)
            ->putJson("/api/saved-calculations/{$saved->id}", ['name' => 'Hijacked'])
            ->assertStatus(404);

        $this->assertDatabaseHas('saved_calculations', ['id' => $saved->id, 'name' => 'My calc']);
    }

    public function test_owner_can_delete_saved_calculation_but_calculation_is_kept(): void
    {
        $user = User::factory()->create();
        $saved = $this->makeSaved($user);
        $calculationId = $saved->calculation_id;

        $this->actingAs($user)
            ->deleteJson("/api/saved-calculations/{$saved->id}")
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('saved_calculations', ['id' => $saved->id]);
        $this->assertDatabaseHas('calculations', ['id' => $calculationId]);
    }

    public function test_user_cannot_delete_another_users_saved_calculation(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $saved = $this->makeSaved($owner);

        $this->actingAs($attacker)
            ->deleteJson("/api/saved-calculations/{$saved->id}")
            ->assertStatus(404);

        $this->assertDatabaseHas('saved_calculations', ['id' => $saved->id]);
    }

    public function test_toggle_favorite_flips_state_both_ways(): void
    {
        $user = User::factory()->create();
        $saved = $this->makeSaved($user);

        $this->actingAs($user)
            ->postJson("/api/saved-calculations/{$saved->id}/toggle-favorite")
            ->assertOk()
            ->assertJson(['success' => true, 'is_favorite' => true]);

        $this->actingAs($user)
            ->postJson("/api/saved-calculations/{$saved->id}/toggle-favorite")
            ->assertOk()
            ->assertJson(['success' => true, 'is_favorite' => false]);
    }

    public function test_user_cannot_toggle_another_users_favorite(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $saved = $this->makeSaved($owner);

        $this->actingAs($attacker)
            ->postJson("/api/saved-calculations/{$saved->id}/toggle-favorite")
            ->assertStatus(404);
    }
}
