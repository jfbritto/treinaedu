<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
{!! file_get_contents(resource_path('views/vendor/mail/html/themes/default.css')) !!}
</style>
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center" style="padding: 24px 0;">

<!-- Container fixo de 570px para alinhar header + body -->
<table align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto; width: 570px;">

{{ $header ?? '' }}

<!-- Spacer -->
<tr><td height="24" style="line-height: 24px; font-size: 24px;">&nbsp;</td></tr>

<!-- Body -->
<tr>
<td class="inner-body" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); padding: 32px;">
{{ Illuminate\Mail\Markdown::parse($slot) }}

{{ $subcopy ?? '' }}
</td>
</tr>

{{ $footer ?? '' }}
</table>

</td>
</tr>
</table>

</body>
</html>
