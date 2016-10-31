Hello {{$user->name}},

You have been invited to join {{ucfirst($project)}} admin backend

The backend can be accessed here:
{{$url}}

You can login with following credentials:

E-mail: {{$user->email}}
Password: {{$password}}

@if($user->should_reset_password)
    After login in, you will be asked to change your password.
@endif

Best regards,
{{ucfirst($project)}}
