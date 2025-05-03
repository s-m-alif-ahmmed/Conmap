<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    <tr>
        <td style="padding: 30px;">
            <h2 style="color: #333333; border-bottom: 1px solid #ddd; padding-bottom: 10px;">Contact Message</h2>

            <p style="margin: 15px 0;"><strong>Name:</strong> {{ $name }}</p>
            <p style="margin: 15px 0;"><strong>Email:</strong> {{ $email }}</p>

            <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">

            <p style="margin: 15px 0;"><strong>Message:</strong></p>
            <p style="background-color: #f1f1f1; padding: 15px; border-radius: 5px;">{{ $userMessage }}</p>
        </td>
    </tr>
</table>

</body>
</html>
