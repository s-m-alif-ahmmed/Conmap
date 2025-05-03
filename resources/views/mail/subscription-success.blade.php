<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    <tr>
        <td style="padding: 30px;">
            <h2 style="color: #333333; border-bottom: 1px solid #ddd; padding-bottom: 10px;">Subscription Detail</h2>

            <p style="margin: 15px 0;"><strong>Plan:</strong> {{ $plan->title }}</p>
            <p style="margin: 15px 0;"><strong>Price:</strong> {{ $plan->price }}</p>
            <p style="margin: 15px 0;"><strong>Type:</strong> {{ $plan->type }}</p>
            <p style="margin: 15px 0;"><strong>Duration:</strong> {{ $plan->duration }} Days</p>
            <p style="margin: 15px 0;"><strong>Start Time:</strong> {{ $userSubscription->created_at }}</p>

        </td>
    </tr>
</table>

</body>
</html>
