New enquiry {!! $inquiry->reference !!}

Submitted: {!! $inquiry->submittedAtLabel() !!}
Name: {!! $visitorName !!}
@if(filled($inquiry->company))
Company: {!! $inquiry->company !!}
@endif
Email: {!! $inquiry->email !!}
@if(filled($inquiry->phone))
Phone / WhatsApp: {!! $inquiry->phone !!}
@endif
Service: {!! $inquiry->service_requested !!}
@if(filled($inquiry->budgetLabel()))
Budget: {!! $inquiry->budgetLabel() !!}
@endif

Message:
{!! $inquiry->message !!}

Open in the admin panel (sign-in required):
{!! $adminUrl !!}

Replying to this email replies to the visitor.