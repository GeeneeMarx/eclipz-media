Hello {!! $visitorName !!},

{!! $copy['headline'] !!}

{!! $copy['body'] !!}

Your Inquiry ID: {!! $inquiry->reference !!}

{!! $copy['next'] !!}
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