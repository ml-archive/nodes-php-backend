@extends('nodes.backend::layout')

@section('breadcrumbs')
    @parent
    <li class="active">
        <span class="fa fa-street-view"></span>
        Backend users
    </li>
@endsection

@section('content')
    <section class="panel panel-default">
        <header class="panel-heading clearfix">
            <h3 class="panel-title">Backend users</h3>
            @can('admin')
                <a href="{{ route('nodes.backend.users.create') }}" class="btn btn-success btn-sm pull-right">
                    <span class="fa fa-user-plus"></span>
                    <span>Create backend user</span>
                </a>
            @endcan
        </header>
        <div class="panel-body">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th class="col-xs-1 text-center">ID</th>
                    <th class="col-xs-4">Name</th>
                    <th class="col-xs-3">E-mail</th>
                    <th class="col-xs-2 text-center">Role</th>
                    <th class="col-xs-2 text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="col-xs-1 text-center">{{$user->id}}</td>
                        <td class="col-xs-4">
                            <img src="{{ $user->getImageUrl(35, 35) }}" style="width:35px">
                            &nbsp;
                            {{ $user->name }}</td>
                        <td class="col-xs-3">
                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        </td>
                        <td class="col-xs-2 text-center">{{ $user->role->title }}</td>
                        <td class="col-xs-2 text-center">
                            <a href="{{ route('nodes.backend.users.edit', $user->id) }}" class="btn btn-sm btn-default @cannot('edit-user', $user) disabled @endcan"  title="Edit details">
                                <span class="fa fa-pencil"></span>
                                <span class="sr-only">Edit details</span>
                            </a>
                            <a href="{{ route('nodes.backend.users.destroy', $user->id) }}" class="btn btn-sm btn-danger @cannot('edit-user', $user) disabled @endcan" data-delete="true" data-token="{{ csrf_token() }}" title="Delete user">
                                <span class="fa fa-times"></span>
                                <span class="sr-only">Delete user</span>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if ($users->total() > 1)
                <nav class="paginator text-center">
                    {!! $users->render() !!}
                </nav>
            @endif
        </div>
    </section>
@endsection
