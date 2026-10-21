@php
    // The WhatsApp glyph is a small white PNG (email apps cannot show SVG). Without the file,
    // or when the email is only being previewed, the button simply shows its text.
    $whatsappIcon = public_path('images/email-whatsapp.png');
    $showWhatsappIcon = isset($message) && is_file($whatsappIcon);
@endphp
@if($whatsappUrl)
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 10px;">
        <tr>
            <td bgcolor="#0A0A0A" style="background-color:#0A0A0A;">
                <a href="{{ $whatsappUrl }}" style="display:inline-block;padding:13px 26px;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:20px;color:#FFFFFF;text-decoration:none;">
                    @if($showWhatsappIcon)
                        <img src="{{ $message->embed($whatsappIcon) }}" width="20" height="20" alt="" style="display:inline-block;width:20px;height:20px;border:0;vertical-align:middle;margin:0 10px 2px 0;">
                    @endif
                    <span style="vertical-align:middle;">Follow up on WhatsApp</span>
                </a>
            </td>
        </tr>
    </table>
    <p style="margin:0 0 8px;font-size:12px;color:#777777;">This opens WhatsApp with your Inquiry ID already in the message.</p>
@endif