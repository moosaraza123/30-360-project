<?php

namespace Modules\DayCountCalculator\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Modules\DayCountCalculator\Http\Requests\SubscribeEmailRequest;
use Modules\DayCountCalculator\Mail\VerifySubscriptionMail;
use Modules\DayCountCalculator\Repositories\SubscriberRepository;

/**
 * SubscriberController
 *
 * Handles email subscription and verification
 */
class SubscriberController extends Controller
{
    public function __construct(
        private SubscriberRepository $subscriberRepository
    ) {}

    /**
     * Subscribe email
     */
    public function subscribe(SubscribeEmailRequest $request)
    {
        // The response is identical whether or not the email was already
        // subscribed, so the endpoint cannot be used to enumerate subscribers.
        $existing = $this->subscriberRepository->findByEmail($request->input('email'));

        if ($existing) {
            $subscriber = $existing->isVerified() ? null : $existing;
        } else {
            $subscriber = $this->subscriberRepository->create(
                email: $request->input('email'),
                source: $request->input('source', 'calculator')
            );
        }

        // Send (or re-send) the verification email for unverified subscribers
        if ($subscriber) {
            try {
                Mail::to($subscriber->email)->send(new VerifySubscriptionMail($subscriber));
            } catch (\Exception $e) {
                // Log error but don't fail the request
                logger()->error('Failed to send verification email', [
                    'subscriber_id' => $subscriber->id,
                    'email' => $subscriber->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Return response based on request type
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing! Please check your email to verify your subscription.',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Thank you for subscribing! Please check your email to verify your subscription.');
    }

    /**
     * Verify email subscription
     */
    public function verify(string $token)
    {
        $subscriber = $this->subscriberRepository->findByToken($token);

        if (! $subscriber) {
            return view('daycountcalculator::subscription.verify-failed', [
                'message' => 'Invalid verification token. The link may have expired or been used already.',
            ]);
        }

        if ($subscriber->isVerified()) {
            return view('daycountcalculator::subscription.already-verified', [
                'subscriber' => $subscriber,
            ]);
        }

        // Mark as verified
        $this->subscriberRepository->markAsVerified($subscriber);

        return view('daycountcalculator::subscription.verify-success', [
            'subscriber' => $subscriber,
        ]);
    }

    /**
     * Unsubscribe email
     */
    public function unsubscribe(string $email, string $token)
    {
        $subscriber = $this->subscriberRepository->findByEmail($email);

        if (! $subscriber || ! is_string($subscriber->verification_token)
            || ! hash_equals($subscriber->verification_token, $token)) {
            return view('daycountcalculator::subscription.unsubscribe-failed', [
                'message' => 'Invalid unsubscribe link.',
            ]);
        }

        $this->subscriberRepository->deactivate($subscriber);

        return view('daycountcalculator::subscription.unsubscribe-success', [
            'subscriber' => $subscriber,
        ]);
    }

    /**
     * Resubscribe email
     */
    public function resubscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:subscribers,email',
        ]);

        $subscriber = $this->subscriberRepository->findByEmail($request->input('email'));

        if (! $subscriber) {
            return redirect()
                ->back()
                ->withErrors(['email' => 'Email not found.']);
        }

        if ($subscriber->is_active) {
            return redirect()
                ->back()
                ->with('info', 'This email is already subscribed.');
        }

        $this->subscriberRepository->activate($subscriber);

        return redirect()
            ->back()
            ->with('success', 'Successfully resubscribed! Welcome back.');
    }
}
