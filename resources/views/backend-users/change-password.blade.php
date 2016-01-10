@extends('nodes.backend::layout')

@section('content')
    <section class="panel panel-default">
        <header class="panel-heading border clearfix">
            <h3 class="panel-title">Update password</h3>
        </header>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    {!! Form::model(backend_user(), ['method' => 'patch', 'route' => ['nodes.backend.users.update-password'], 'class' => 'form-horizontal']) !!}
                    <input type="hidden" name="id" value="{{ backend_user()->id }}">
                    <h4 class="bordered">
                            Change password
                        <small>(min. 6 characters)</small>
                    </h4>
                    {{--Password--}}
                    <div class="form-group">
                        <label for="backendUserFormPassword" class="col-sm-3 control-label">Password</label>
                        <div class="col-sm-9 @if(validation_key_failed('password')) has-error @endif}}">
                            {!! Form::password('password', ['id' => 'backendUserFormPassword', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    {{--Password confirm--}}
                    <div class="form-group">
                        <label for="backendUserFormRepeatPassword" class="col-sm-3 control-label">Repeat password</label>
                        <div class="col-sm-9 @if(validation_key_failed('password')) has-error @endif}}">
                            {!! Form::password('password_confirmation', ['id' => 'backendUserFormRepeatPassword', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <input type="submit" class="btn btn-primary form-control" value="Save">
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
@endsection
