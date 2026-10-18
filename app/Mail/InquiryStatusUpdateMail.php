<?php

namespace App\Mail;

use App\Enums\InquiryStatus;
use App\Models\ContactSetting;
use App\Models\Inquiry;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Tells the visitor their enquiry has moved to a new stage. The wording only
 * states what the status means. It never claims a call, proposal or outcome.
 */
class InquiryStatusUpdateMail extends Mailable
{
    public function __construct(public Inquiry $inquiry, public InquiryStatus $status)
    {
    }

    public function envelope(): Envelope
    {
        $replyTo = ContactSetting::current()->email;
        $reference = $this->inquiry->reference;

        $subject = match ($this->status) {
            InquiryStatus::UnderReview => "Your enquiry {$reference} is under review",
            InquiryStatus::Closed => "Your enquiry {$reference} has been closed",
            default => "Update on your enquiry {$reference}",
        };

        return new Envelope(
            subject: $subject,
            replyTo: filled($replyTo) ? [new Address($replyTo)] : [],
        );
    }

    public function content(): Content
    {
        $contact = ContactSetting::current();

        return new Content(
            view: 'emails.inquiry-status-update',
            text: 'emails.inquiry-status-update-text',
            with: [
                'inquiry' => $this->inquiry,
                'status' => $this->status,
                'copy' => $this->copy(),
                'visitorName' => trim((string) preg_replace('/\s+/', ' ', (string) $this->inquiry->name)),
                'contact' => $contact,
                'whatsappUrl' => $contact->whatsappUrl(Inquiry::followUpMessage($this->inquiry->reference)),
            ],
        );
    }

    /** One source of wording for the HTML and the plain-text version. */
    private function copy(): array
    {
        return match ($this->status) {
            InquiryStatus::UnderReview => [
                'eyebrow' => 'Under review',
                'headline' => 'Your enquiry is under review',
                'body' => 'The Eclipz team is now reviewing your enquiry. No action is needed from you at this stage.',
                'next' => 'We will get back to you. If you would like to add anything in the meantime, please contact us and quote your Inquiry ID.',
            ],
            InquiryStatus::Responded => [
                'eyebrow' => 'Update',
                'headline' => 'Update on your enquiry',
                'body' => 'The Eclipz team has marked your enquiry as responded.',
                'next' => 'If you have not yet received our reply, please check your spam folder, or contact us and quote your Inquiry ID.',
            ],
            InquiryStatus::Closed => [
                'eyebrow' => 'Enquiry closed',
                'headline' => 'Your enquiry has been closed',
                'body' => 'Your enquiry has been closed on our side.',
                'next' => 'If you would like to continue, or have more questions, please contact us and quote your Inquiry ID.',
            ],
            default => [
                'eyebrow' => 'Update',
                'headline' => 'Update on your enquiry',
                'body' => 'There is an update on your enquiry.',
                'next' => 'Please quote your Inquiry ID if you contact us.',
            ],
        };
    }
}