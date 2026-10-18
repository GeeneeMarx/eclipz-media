@extends('emails.layout')

@section('title', 'New enquiry '.$inquiry->reference)

@section('preheader', $visitorName.' sent a new enquiry. Reference '.$inquiry->reference.'.')

@section('content')
    <p style="margin:0 0 8px;font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#007BFF;">New enquiry</p>
    <h1 class="reference" style="margin:0 0 6px;font-family:Impact,'Arial Narrow',Arial,sans-serif;font-size:34px;line-height:1.15;font-weight:normal;letter-spacing:2px;color:#0A0A0A;">{{ $inquiry->reference }}</h1>
    <p style="margin:0 0 28px;font-size:13px;color:#777777;">Submitted {{ $inquiry->submittedAtLabel() }}</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;font-size:14px;">
        <tr>
            <td class="label-cell" width="150" valign="top" style="padding:10px 16px 10px 0;border-top:1px solid #EEEEEE;color:#777777;">Name</td>
            <td class="value-cell" valign="top" style="padding:10px 0;border-top:1px solid #EEEEEE;">{{ $visitorName }}</td>
        </tr>
        @if(filled($inquiry->company))
            <tr>
                <td class="label-cell" width="150" valign="top" style="padding:10px 16px 10px 0;border-top:1px solid #EEEEEE;color:#777777;">Company</td>
                <td class="value-cell" valign="top" style="padding:10px 0;border-top:1px solid #EEEEEE;">{{ $inquiry->company }}</td>
            </tr>
        @endif
        <tr>
            <td class="label-cell" width="150" valign="top" style="padding:10px 16px 10px 0;border-top:1px solid #EEEEEE;color:#777777;">Email</td>
            <td class="value-cell" valign="top" style="padding:10px 0;border-top:1px solid #EEEEEE;"><a href="mailto:{{ $inquiry->email }}" style="color:#007BFF;">{{ $inquiry->email }}</a></td>
        </tr>
        @if(filled($inquiry->phone))
            <tr>
                <td class="label-cell" width="150" valign="top" style="padding:10px 16px 10px 0;border-top:1px solid #EEEEEE;color:#777777;">Phone / WhatsApp</td>
                <td class="value-cell" valign="top" style="padding:10px 0;border-top:1px solid #EEEEEE;">{{ $inquiry->phone }}</td>
            </tr>
        @endif
        <tr>
            <td class="label-cell" width="150" valign="top" style="padding:10px 16px 10px 0;border-top:1px solid #EEEEEE;color:#777777;">Service</td>
            <td class="value-cell" valign="top" style="padding:10px 0;border-top:1px solid #EEEEEE;">{{ $inquiry->service_requested }}</td>
        </tr>
        @if(filled($inquiry->budgetLabel()))
            <tr>
                <td class="label-cell" width="150" valign="top" style="padding:10px 16px 10px 0;border-top:1px solid #EEEEEE;color:#777777;">Budget</td>
                <td class="value-cell" valign="top" style="padding:10px 0;border-top:1px solid #EEEEEE;">{{ $inquiry->budgetLabel() }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="2" style="border-top:1px solid #EEEEEE;font-size:0;line-height:0;">&nbsp;</td>
        </tr>
    </table>

    <p style="margin:0 0 8px;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#777777;">Message</p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 32px;">
        <tr>
            <td width="3" bgcolor="#D4A017" style="width:3px;background-color:#D4A017;font-size:0;line-height:0;">&nbsp;</td>
            <td style="padding:6px 0 6px 16px;font-size:15px;line-height:1.65;">{!! nl2br(e($inquiry->message)) !!}</td>
        </tr>
    </table>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 12px;">
        <tr>
            <td bgcolor="#0A0A0A" style="background-color:#0A0A0A;">
                <a href="{{ $adminUrl }}" style="display:inline-block;padding:14px 26px;font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#FFFFFF;text-decoration:none;">Open in admin panel</a>
            </td>
        </tr>
    </table>
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;">
        <tr>
            <td style="border:1px solid #0A0A0A;">
                <a href="mailto:{{ $inquiry->email }}?subject={{ rawurlencode('Your enquiry '.$inquiry->reference) }}" style="display:inline-block;padding:13px 26px;font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#0A0A0A;text-decoration:none;">Reply to visitor</a>
            </td>
        </tr>
    </table>
    <p style="margin:0;font-size:12px;color:#777777;">You need to be signed in to the admin panel to open the first link. Replying to this email also replies to the visitor.</p>
@endsection

@section('footer')
    Automatic notification from the Eclipz website. This message was sent to the private inquiry notification address.
@endsection