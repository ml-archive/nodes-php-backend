@extends('nodes.backend::layouts.base')

@section('breadcrumbs')
    <li class="active">Roles</li>
@endsection

@section('page-header-top')
    <div>
        <h3>Roles</h3>
    </div>
    <div>
        <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#roleModal">
            <span class="fa fa-plus"></span>
            <span>Create role</span>
        </button>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <br>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="col-xs-4">Title</th>
                        <th class="col-xs-3">Slug</th>
                        <th class="col-xs-2 text-center">Users</th>
                        <th class="col-xs-1 text-center">Default</th>
                        <th class="col-xs-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->title }}</td>
                            <td>{{ $role->slug }}</td>
                            <td class="text-center">{{ $role->user_count }}</td>
                            @if ($role->isDefault())
                                <td class="text-center success">
                                    <span class="fa fa-check"></span>
                                </td>
                            @else
                                <td class="text-center danger">
                                    <span class="fa fa-times"></span>
                                </td>
                            @endif
                            <td class="text-center">
                                {{-- Set default --}}
                                <a href="{{ route('nodes.backend.users.roles.default', $role->id) }}" class="btn btn-sm btn-default @if($role->isDefault()) disabled @endif" data-toggle="tooltip" data-method="POST" data-confirm="true" data-token="{{ csrf_token() }}" title="Set role as default">
                                    <span class="fa fa-heart"></span>
                                    <span class="sr-only">Set role as default</span>
                                </a>

                                {{-- Edit role --}}
                                <button type="button" data-href="{{ route('nodes.backend.users.roles.update', $role->id) }}" data-tooltip="true" class="btn btn-sm btn-default role-edit" data-toggle="modal" data-target="#roleModal" data-role="{{ $role->title }}" data-role-id="{{ $role->id }}" title="Edit role">
                                    <span class="fa fa-pencil"></span>
                                    <span class="sr-only">Edit details</span>
                                </button>

                                {{-- Delete role --}}
                                <a href="{{ route('nodes.backend.users.roles.destroy', $role->id) }}" data-tooltip="true" class="btn btn-sm btn-danger" data-delete="true" data-token="{{ csrf_token() }}" title="Delete role">
                                    <span class="fa fa-times"></span>
                                    <span class="sr-only"> Delete Role</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($roles->total() > 1)
                <div class="row">
                    <div class="col-xs-12">
                        <nav class="paginator text-center">
                            {!! $roles->render() !!}
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div id="roleModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel" aria-hidden="true">
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
    </div>
@endsection
