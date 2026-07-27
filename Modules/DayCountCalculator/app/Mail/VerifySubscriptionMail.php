<?php

namespace Modules\DayCountCalculator\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\DayCountCalculator\Entities\Subscriber;

/**
 * VerifySubscriptionMail
 *
 * Email sent to verify subscriber's email address
 */
class VerifySubscriptionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Subscriber $subscriber
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your Email - Day Count Calculator',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'daycountcalculator::emails.verify-subscription',
            with: [
                'verificationUrl' => route('subscribe.verify', ['token' => $this->subscriber->verification_token]),
                'subscriber' => $this->subscriber,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
