<html>
<head>
    <title>Reset password</title>
</head>

<body>
    <h4>Hello,</h4>
    <p>
        We have received a request to reset the password of the user with this e-mail.<br>
        If you did not request this, simply ignore and delete this e-mail.
    </p>
    <p>
        To reset your password, click the following link:<br>
        <a href="{{$domain}}/admin/login/reset/{{$token}}">{{$domain}}/admin/login/reset/{{$token}}</a>
    </p>
    <p><em>This reset password request will expire in {{ $expire }} minutes.</em></p>
</body>
</html>