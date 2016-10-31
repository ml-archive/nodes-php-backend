@extends('nodes.backend::layouts.base')

@section('breadcrumbs')
    <li class="active">Backend users</li>
@endsection

@section('page-header-top')
        <div>
            <h3>Backend users</h3>
        </div>
        <div>
            <div class="layout horizontal center justified padding-vertical-sm">
                <form class="form-inline search-form margin-right margin-left-md-auto">
                    <div class="form-group action-wrapper no-margin-bottom">
                        <button type="submit" class="btn btn-transparent action-wrap-action action-wrap-right action-wrap-sm">
                            <i class="fa fa-search"></i>
                        </button>
                        <input type="text" id="search" class="form-control input-sm action-wrap-item action-wrap-right" name="search" placeholder="Search" value="{{ Request::get('search') }}">
                    </div>
                </form>

                @can('backend-admin')
                <a href="{{ route('nodes.backend.users.create') }}" class="btn btn-primary btn-sm pull-right">
                    <span class="fa fa-user-plus"></span>
                    <span class="hidden-xs">Create backend user</span>
                </a>
                @endcan
            </div>
        </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <br>
            <table class="table table-striped">
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
                            <td class="col-xs-1 text-center va-middle">{{$user->id}}</td>
                            <td class="col-xs-4 va-middle">
                                <div class="user__profile--inline">
                                    <img src="{{ $user->getImageUrl(35, 35) }}" width="35" class="user__info-avatar img-responsive img-circle pull-left">
                                    <span class="user__info-name pull-left">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="col-xs-3 va-middle">
                                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                            </td>
                            <td class="col-xs-2 text-center va-middle">{{ $user->role->title }}</td>
                            <td class="col-xs-2 text-center va-middle">
                                <a href="{{ route('nodes.backend.users.edit', $user->id) }}" class="btn btn-sm btn-default @cannot('backend-edit-backend-user', $user) disabled @endcan" data-tooltip="true" title="Edit details">
                                    <span class="fa fa-pencil"></span>
                                    <span class="sr-only">Edit details</span>
                                </a>
                                <a href="{{ route('nodes.backend.users.destroy', $user->id) }}" class="btn btn-sm btn-danger @cannot('backend-edit-backend-user', $user) disabled @endcan" data-delete="true" data-token="{{ csrf_token() }}" data-tooltip="true" title="Delete user">
                                    <span class="fa fa-times"></span>
                                    <span class="sr-only">Delete user</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if ($users->total() > 1)
        <div class="row">
            <div class="col-xs-12">
                <nav class="paginator text-center">
                    {!! $users->render() !!}
                </nav>
            </div>
        </div>
    @endif
@endsection
