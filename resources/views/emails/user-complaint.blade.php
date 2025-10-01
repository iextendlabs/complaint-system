<!DOCTYPE html>
<html>

<head>
    <title>Complaint Received</title>
</head>

<body>
    <h2>Thank you for submitting your complaint.</h2>
    <p>Your concern has been received and will be processed according to our organizational policies.</p>
    <a href="{{ url('/?tracking_id=' . $complaint->tracking_id) }}"><strong>Track Your Complaint</strong></a>
    <p>Please keep this tracking ID for your reference. You can use it to follow up on your complaint.</p>
</body>

</html>
