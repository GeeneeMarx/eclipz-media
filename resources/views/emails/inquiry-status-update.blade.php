@extends('emails.layout')

@section('title', 'Update on your enquiry '.$inquiry->reference)

@section('preheader', 'Status of enquiry '.$inquiry->reference.': '.$status->label().'.')

@section('content')
    <p style="margin:0 0 8px;font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#007BFF;">{{ $copy['eyebrow'] }}</p>
    <h1 style="margin:0 0 24px;font-family:Impact,'Arial Narrow',Arial,sans-serif;font-size:34px;line-height:1.15;font-weight:normal;letter-spacing:1px;text-transform:uppercase;color:#0A0A0A;">{{ $copy['headline'] }}</h1>

    <p style="margin:0 0 16px;">Hello {{ $visitorName }},</p>
    <p style="margin:0 0 28px;">{{ $copy['body'] }}</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;border:1px solid #E5E5E5;">
        <tr>
            <td width="4" bgcolor="#D4A017" style="width:4px;background-color:#D4A017;font-size:0;line-height:0;">&nbsp;</td>
            <td style="padding:20px 24px;background-color:#F7F7F7;">
                <p style="margin:0 0 6px;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#777777;">Your Inquiry ID</p>
                <p class="reference" style="margin:0;font-size:30px;line-height:1.2;font-weight:bold;letter-spacing:3px;color:#0A0A0A;">{{ $inquiry->reference }}</p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 24px;">{{ $copy['next'] }}</p>

    @if($whatsappUrl)
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 10px;">
            <tr>
                <td bgcolor="#0A0A0A" style="background-color:#0A0A0A;">
                    <a href="{{ $whatsappUrl }}" style="display:inline-block;padding:14px 26px;font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#FFFFFF;text-decoration:none;">Follow up on WhatsApp</a>
                </td>
            </tr>
        </table>
        <p style="margin:0 0 8px;font-size:12px;color:#777777;">This opens WhatsApp with your Inquiry ID already in the message.</p>
    @endif
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