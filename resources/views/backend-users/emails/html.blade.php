<html>
<head>
    <title>Welcome to {{ucfirst($project)}}</title>
</head>

<body>
    <h4>Hello {{$user->name}}</h4>
    <p>
        You have been invited to join {{ucfirst($project)}} admin backend
        <br>
        The backend can be accessed <a href="{{$url}}">Here!</a>
        <br>
        You can login with following credentials
        <br>
        Email: {{$user->email}}
        <br>
        Password: {{$password}}
    </p>
    @if($user->should_reset_password)
        <p>
            After login in, you will be asked to change your password
        </p>
    @endif
    <p>Best regards {{ucfirst($project)}}</p>
</body>
</html>



