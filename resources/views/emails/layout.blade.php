@php
    // One place to change how every email looks.
    // Header band colour: brand Jet Black, which suits the white logo.
    $headerBackground = '#0A0A0A';

    // The logo is the same transparent white PNG the website footer uses
    // (public/images/eclipz-logo-white.png). Email apps cannot show SVG, so it is a PNG,
    // embedded in the email itself. It is drawn small: a fixed height, with the width
    // following the picture's shape, so the header stays a slim band.
    $logoPath = public_path('images/eclipz-logo-white.png');

    if (! is_file($logoPath)) {
        $logoPath = public_path('images/eclipz-logo-email.png');
    }

    $logoHeight = 32;     // change this one number to make the logo bigger or smaller
    $logoMaxWidth = 180;
    $logoWidth = $logoHeight;
    $showLogo = isset($message) && is_file($logoPath);

    if ($showLogo) {
        $size = @getimagesize($logoPath);

        if ($size && $size[0] > 0 && $size[1] > 0) {
            $logoWidth = (int) round($size[0] / $size[1] * $logoHeight);

            if ($logoWidth > $logoMaxWidth) {
                $logoWidth = $logoMaxWidth;
                $logoHeight = (int) round($size[1] / $size[0] * $logoWidth);
            }
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>@yield('title')</title>
    <style>
        @media only screen and (max-width: 640px) {
            .container { width: 100% !important; }
            .pad { padding-left: 22px !important; padding-right: 22px !important; }
            .reference { font-size: 24px !important; letter-spacing: 2px !important; }
            .label-cell { display: block !important; width: 100% !important; padding-bottom: 0 !important; }
            .value-cell { display: block !important; width: 100% !important; padding-top: 2px !important; padding-bottom: 12px !important; border-top: 0 !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#EDEDED;">
    <div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">@yield('preheader')</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#EDEDED;">
        <tr>
            <td align="center" style="padding:28px 12px;">
                <table role="presentation" class="container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;max-width:600px;background-color:#FFFFFF;">
                    <tr>
                        <td class="pad" bgcolor="{{ $headerBackground }}" style="background-color:{{ $headerBackground }};padding:14px 40px;">
                            @if($showLogo)
                                <img src="{{ $message->embed($logoPath) }}" width="{{ $logoWidth }}" height="{{ $logoHeight }}" alt="Eclipz Media &amp; Marketing" style="display:block;width:{{ $logoWidth }}px;height:{{ $logoHeight }}px;border:0;outline:none;">
                            @else
                                <span style="font-family:Impact,'Arial Narrow',Arial,sans-serif;font-size:22px;letter-spacing:4px;color:#FFFFFF;">ECLIPZ</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td height="3" bgcolor="#D4A017" style="height:3px;line-height:3px;font-size:3px;background-color:#D4A017;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="pad" style="padding:40px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.65;color:#0A0A0A;">
                            @yield('content')
                        </td>
                    </tr>
                    <tr>
                        <td class="pad" style="padding:24px 40px 30px;border-top:1px solid #E5E5E5;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.7;color:#777777;">
                            @hasSection('footer')
                                @yield('footer')
                            @else
                                Eclipz Media &amp; Marketing Limited, Lagos, Nigeria
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>