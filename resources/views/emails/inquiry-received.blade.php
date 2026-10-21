@extends('emails.layout')

@section('title', 'We received your enquiry')

@section('preheader', 'Your Inquiry ID is '.$inquiry->reference.'. Keep it handy if you follow up with us.')

@section('content')
    <p style="margin:0 0 8px;font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#007BFF;">Enquiry received</p>
    <h1 style="margin:0 0 24px;font-family:Impact,'Arial Narrow',Arial,sans-serif;font-size:34px;line-height:1.15;font-weight:normal;letter-spacing:1px;text-transform:uppercase;color:#0A0A0A;">Thank you, we have your enquiry</h1>

    <p style="margin:0 0 16px;">Hello {{ $visitorName }},</p>
    <p style="margin:0 0 28px;">Thank you for contacting Eclipz Media &amp; Marketing. Your enquiry has been received and saved under the reference below.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;border:1px solid #E5E5E5;">
        <tr>
            <td width="4" bgcolor="#D4A017" style="width:4px;background-color:#D4A017;font-size:0;line-height:0;">&nbsp;</td>
            <td style="padding:20px 24px;background-color:#F7F7F7;">
                <p style="margin:0 0 6px;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#777777;">Your Inquiry ID</p>
                <p class="reference" style="margin:0;font-size:30px;line-height:1.2;font-weight:bold;letter-spacing:3px;color:#0A0A0A;">{{ $inquiry->reference }}</p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 20px;">The Eclipz team will review your request and get back to you. If you follow up with us, please quote this Inquiry ID so we can find your enquiry quickly.</p>

    @php
        $summary = [
            'Submitted' => $inquiry->submittedAtLabel(),
            'Service of interest' => $inquiry->service_requested,
            'Estimated budget' => $inquiry->budgetLabel(),
        ];
    @endphp
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;font-size:14px;">
        @foreach($summary as $label => $value)
            @if(filled($value))
                <tr>
                    <td class="label-cell" width="150" valign="top" style="padding:10px 16px 10px 0;border-top:1px solid #EEEEEE;color:#777777;">{{ $label }}</td>
                    <td class="value-cell" valign="top" style="padding:10px 0;border-top:1px solid #EEEEEE;color:#0A0A0A;">{{ $value }}</td>
                </tr>
            @endif
        @endforeach
        <tr>
            <td colspan="2" style="border-top:1px solid #EEEEEE;font-size:0;line-height:0;">&nbsp;</td>
        </tr>
    </table>

    @include('emails.partials.whatsapp-button')
@endsection

@section('footer')
    <strong style="color:#0A0A0A;">Eclipz Media &amp; Marketing Limited</strong><br>
    Lagos, Nigeria<br>
    @if($contact->email)
        <a href="mailto:{{ $contact->email }}" style="color:#777777;">{{ $contact->email }}</a><br>
    @endif
    @if($contact->phone)
        {{ $contact->phone }}<br>
    @endif
    <br>
    You are receiving this email because an enquiry was submitted on our website using this email address.
@endsection