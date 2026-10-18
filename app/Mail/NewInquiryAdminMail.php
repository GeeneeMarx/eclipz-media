<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Sent to the private notification address when a new enquiry is saved.
 * Replying to it replies to the visitor.
 */
class NewInquiryAdminMail extends Mailable
{
    public function __construct(public Inquiry $inquiry)
    {
    }

    public function envelope(): Envelope
    {
        $name = $this->visitorName();

        return new Envelope(
            subject: "New enquiry {$this->inquiry->reference} from {$name}",
            replyTo: [new Address($this->inquiry->email, $name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-inquiry-admin',
            text: 'emails.new-inquiry-admin-text',
            with: [
                'inquiry' => $this->inquiry,
                'visitorName' => $this->visitorName(),
                'adminUrl' => route('filament.admin.resources.inquiries.edit', ['record' => $this->inquiry->getKey()]),
            ],
        );
    }

    /** Collapses line breaks and tabs so a name can never inject mail headers. */
    private function visitorName(): string
    {
        return trim((string) preg_replace('/\s+/', ' ', (string) $this->inquiry->name));
    }
}