@extends('nodes.backend::layout')

@section('breadcrumbs')
    @parent
    <li>
        <a href="{{ route('nodes.backend.users') }}">
            <span class="fa fa-street-view"></span>
            Backend users
        </a>
    </li>
    <li class="active">
        <span class="fa fa-graduation-cap"></span>
        Roles
    </li>
@endsection

@section('content')
    <section class="panel panel-default">
        <header class="panel-heading clearfix">
            <h3 class="panel-title">Roles</h3>
            <button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#roleModal">
                <span class="fa fa-plus"></span>
                <span>Create role</span>
            </button>
        </header>
        <div class="panel-body">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th class="col-xs-4">Title</th>
                    <th class="col-xs-4">Slug</th>
                    <th class="col-xs-1 text-center">Default</th>
                    <th class="col-xs-1 text-center">Users</th>
                    <th class="col-xs-2 text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td class="col-xs-4">{{ $role->title }}</td>
                        <td class="col-xs-4">{{ $role->slug }}</td>
                        <td class="col-xs-1 text-center">
                            @if ($role->isDefault())
                            <span class="fa fa-check text-success"></span>
                            @else
                            <span class="fa fa-times text-danger"></span>
                            @endif
                        <td class="col-xs-1 text-center">{{ $role->user_count }}</td>
                        <td class="col-xs-2 text-center">
                            {{-- Edit role --}}
                            <button type="button" data-href="{{ route('nodes.backend.users.roles.update', $role->id) }}" class="btn btn-sm btn-default role-edit" data-toggle="modal" data-target="#roleModal" data-role="{{ $role->title }}" data-role-id="{{ $role->id }}" title="Edit role">
                                <span class="fa fa-pencil"></span>
                                <span class="sr-only">Edit details</span>
                            </button>

                            {{-- Delete role --}}
                            <a href="{{ route('nodes.backend.users.roles.destroy', $role->id) }}" class="btn btn-sm btn-danger" data-delete="true" data-token="{{ csrf_token() }}" title="Delete role">
                                <span class="fa fa-times"></span>
                                <span class="sr-only"> Delete Roll </span>
                            </a>

                            {{--Set default--}}
                            <a href="{{ route('nodes.backend.users.roles.default', $role->id) }}"
                               class="btn btn-sm btn-info" data-toggle="tooltip"
                               data-method="POST" data-confirm="true"
                               data-token="{{ csrf_token() }}" title="Set default"
                                {{$role->isDefault() ? 'disabled' : ''}}>
                                <span class="fa fa-heart"></span>
                                <span class="sr-only">Set default</span>
                            </a>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if ($roles->total() > 1)
                <nav class="paginator text-center">
                    {!! $roles->render() !!}
                </nav>
            @endif
        </div>
    </section>
    <section id="roleModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <header class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="roleModalLabel">Create role</h4>
                </header>
                {!! Form::open(['method' => 'POST', 'route' => 'nodes.backend.users.roles.store']) !!}
                <div class="modal-body">
                    <div class="form-group">
                        <label for="roleName" class="sr-only">Role title</label>
                        <input type="text" id="roleName" class="form-control" name="title" placeholder="Name of role">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create role</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@endsection
