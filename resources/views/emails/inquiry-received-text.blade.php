Hello {!! $visitorName !!},

Thank you for contacting Eclipz Media & Marketing. We have received your enquiry and saved it under the reference below.

Your Inquiry ID: {!! $inquiry->reference !!}

The Eclipz team will review your request and get back to you. If you follow up with us, please quote this Inquiry ID so we can find your enquiry quickly.
@if(filled($inquiry->service_requested))

Service of interest: {!! $inquiry->service_requested !!}
@endif
@if($contact->email || $contact->phone || $whatsappUrl)

Contact us:
@if($contact->email)
Email: {!! $contact->email !!}
@endif
@if($contact->phone)
Phone: {!! $contact->phone !!}
@endif
@if($whatsappUrl)
WhatsApp: {!! $whatsappUrl !!}
@endif
@endif

Eclipz Media & Marketing Limited, Lagos, Nigeria