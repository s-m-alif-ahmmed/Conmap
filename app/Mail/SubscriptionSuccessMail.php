<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $plan;
    public $userSubscription;

    public function __construct($plan, $userSubscription)
    {
        $this->plan = $plan;
        $this->userSubscription = $userSubscription;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Subscription Successful');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.subscription-success',
            with: [
                'plan' => $this->plan,
                'userSubscription' => $this->userSubscription,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
