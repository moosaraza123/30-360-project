<?php

namespace Modules\DayCountCalculator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\DayCountCalculator\Entities\Subscriber;
use Tests\TestCase;

/**
 * Regression tests for the subscription verify/unsubscribe flow.
 * Previously the verification token was nulled on verify, which made
 * every unsubscribe link permanently invalid.
 */
class SubscriptionFlowTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubscriber(array $attributes = []): Subscriber
    {
        return Subscriber::create(array_merge([
            'email' => 'subscriber@example.com',
            'verification_token' => str_repeat('a', 60),
            'is_active' => true,
            'source' => 'calculator',
        ], $attributes));
    }

    public function test_verification_marks_verified_and_keeps_token(): void
    {
        $subscriber = $this->makeSubscriber();

        $this->get(route('subscribe.verify', $subscriber->verification_token))
            ->assertOk();

        $subscriber->refresh();
        $this->assertTrue($subscriber->isVerified());
        $this->assertNotNull($subscriber->verification_token, 'Token must survive verification for unsubscribe links');
    }

    public function test_verified_subscriber_can_unsubscribe(): void
    {
        $subscriber = $this->makeSubscriber();

        $this->get(route('subscribe.verify', $subscriber->verification_token));
        $subscriber->refresh();

        $this->get(route('subscribe.unsubscribe', [
            'email' => $subscriber->email,
            'token' => $subscriber->verification_token,
        ]))->assertOk();

        $this->assertFalse($subscriber->refresh()->is_active);
    }

    public function test_unsubscribe_rejects_invalid_token(): void
    {
        $subscriber = $this->makeSubscriber();

        $this->get(route('subscribe.unsubscribe', [
            'email' => $subscriber->email,
            'token' => str_repeat('b', 60),
        ]))->assertOk(); // renders the "failed" view

        $this->assertTrue($subscriber->refresh()->is_active);
    }

    public function test_verify_with_unknown_token_fails_gracefully(): void
    {
        $this->get(route('subscribe.verify', str_repeat('c', 60)))
            ->assertOk(); // renders the "failed" view without exceptions
    }
}
