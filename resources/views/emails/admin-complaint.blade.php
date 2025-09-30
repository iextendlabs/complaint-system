<!DOCTYPE html>
<html>

<head>
    <title>New Complaint Received</title>
</head>

<body>
    <h2>New Complaint Submitted</h2>

    <p><strong>Tracking ID:</strong> {{ $complaint->tracking_id }}</p>
    <p><strong>Name:</strong> {{ $complaint->name }}</p>
    <p><strong>Phone Number:</strong> {{ $complaint->number }}</p>
    <p><strong>Email:</strong> {{ $complaint->email }}</p>
    <p><strong>Complaint Details:</strong></p>
    <p>{{ $complaint->complaint }}</p>

    <p><strong>Status:</strong> {{ $complaint->status }}</p>
    <p><strong>Confidential:</strong> {{ $complaint->isConfidential ? 'Yes' : 'No' }}</p>
    <p><strong>Declaration Accepted:</strong> {{ $complaint->declarationAccepted ? 'Yes' : 'No' }}</p>

    @if ($complaint->file)
        <p><strong>Attached File:</strong> {{ basename($complaint->file) }}</p>
    @endif

    <p><strong>Submitted At:</strong> {{ $complaint->created_at->format('Y-m-d H:i:s') }}</p>

    <hr>
    <p>This is an automated notification. Please review the complaint in the admin panel.</p>
</body>

</html>
