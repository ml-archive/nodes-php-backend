<html>
<head>
    <title>Invitation to {{ ucfirst($project) }} admin backend</title>
</head>

<body>
    <h4><strong>Hello</strong> {{ $user->name }},</h4>
    <p>You have been invited to join {{ ucfirst($project) }} admin backend.</p>
    <p>The backend can be accessed here:<br><a href="{{ $url }}">{{ $url }}</a></p>
    <p>
        You can login with following credentials:<br>
        <br>
        <strong>E-mail</strong>: {{ $user->email }}
        <br>
        <strong>Password</strong>: {{ $password }}
    </p>
    @if($user->change_password)
        <p><em>Note: Password was randomly generated. You will be asked to change your password at your first login.</em></p>
    @endif
    <p><strong>Best regards</strong><br>{{ ucfirst($project) }}</p>
</body>
</html>



