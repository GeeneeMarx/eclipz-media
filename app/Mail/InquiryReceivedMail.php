<?php

namespace App\Mail;

use App\Models\ContactSetting;
use App\Models\Inquiry;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Confirmation sent to the visitor. It makes no promise about response times.
 */
class InquiryReceivedMail extends Mailable
{
    public function __construct(public Inquiry $inquiry)
    {
    }

    public function envelope(): Envelope
    {
        $replyTo = ContactSetting::current()->email;

        return new Envelope(
            subject: "We received your enquiry ({$this->inquiry->reference})",
            replyTo: filled($replyTo) ? [new Address($replyTo)] : [],
        );
    }

    public function content(): Content
    {
        $contact = ContactSetting::current();

        return new Content(
            view: 'emails.inquiry-received',
            text: 'emails.inquiry-received-text',
            with: [
                'inquiry' => $this->inquiry,
                'visitorName' => trim((string) preg_replace('/\s+/', ' ', (string) $this->inquiry->name)),
                'contact' => $contact,
                'whatsappUrl' => $contact->whatsappUrl(Inquiry::followUpMessage($this->inquiry->reference)),
            ],
        );
    }
}