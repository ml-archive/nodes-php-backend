@extends('nodes.backend::layouts.base')

@section('breadcrumbs')
    <li class="active">Failed jobs</li>
@endsection

@section('page-header-top')
    <div>
        <h3>{{sprintf('Failed jobs (%s)', $failedJobs->total())}}</h3>
    </div>
    <div>
        <a href="{{ route('nodes.backend.failed-jobs.restart-all') }}" class="btn btn btn-primary btn-sm" data-method="POST" data-confirm="true" data-token="{{ csrf_token() }}">
            <span class="fa fa-play-circle"></span>
            <span>Restart all</span>
        </a>
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
                    <th class="col-xs-2 text-center">Connection</th>
                    <th class="col-xs-2 text-center">Queue</th>
                    <th class="col-xs-3 text-center">Payload</th>
                    <th class="col-xs-2 text-center">Date / Time</th>
                    <th class="col-xs-2 text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($failedJobs as $failedJob)
                    <tr>
                        <td class="col-xs-1 text-center">{{ $failedJob->id }}</td>
                        <td class="col-xs-2 text-center">{{ $failedJob->connection }}</td>
                        <td class="col-xs-2 text-center">{{$failedJob->queue }}</td>
                        <td class="col-xs-3 text-center">
                            <button type="button" class="btn btn-sm btn-default" data-toggle="payload-modal" data-changelog-template="#changelogModal" data-resolve="{{ $failedJob->payload }}">
                                <span class="fa fa-rocket"></span>
                                View payload
                            </button>
                        </td>
                        <td class="col-xs-2 text-center">{{ $failedJob->getDateHumanReadable('failed_at') }}</td>
                        <td class="col-xs-2 text-right">
                            <a href="{{ route('nodes.backend.failed-jobs.restart', $failedJob->id) }}"  class="btn btn-sm btn-default" data-method="POST" data-confirm="true" data-token="{{ csrf_token() }}">
                                <span class="fa fa-play"></span>
                                <span class="sr-only">Restart</span>
                            </a>
                            <a href="{{ route('nodes.backend.failed-jobs.forget', $failedJob->id) }}"  class="btn btn-sm btn-danger" data-method="POST" data-confirm="true" data-token="{{ csrf_token() }}">
                                <span class="fa fa-trash"></span>
                                <span class="sr-only">Forget</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No failed jobs <span class="fa fa-thumbs-o-up"></span></td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            @if ($failedJobs->total() > 1)
                <div class="row">
                    <div class="col-xs-12">
                        <nav class="paginator text-center">
                            {!! $failedJobs->render() !!}
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="hidden">
        <div class="row" id="changelogModal">
            <div class="col-xs-12">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Value</th>
                        <th>From</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr data-payload-repeater>
                        <td data-payload-key class="font-bold"></td>
                        <td data-payload-value></td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection

@section('project-js')
    <script>
        // Custom Dialogs
        $('[data-toggle="payload-modal"]').each(function () {
            $(this).on('click', _openModal);

            function _openModal(e) {

                e.preventDefault();
                var data = $(this).data('resolve');
                var template = '<h1 class="text-danger">Please provide a template</h1>';

                if ($(this).data('changelog-template')) {
                    var templateString = '';
                    var input = $($(this).data('changelog-template')).get(0).outerHTML;
                    template = buildModalMarkup(input, data);
                }

                bootbox.dialog({
                    message: template,
                    onEscape: true,
                    backdrop: true,
                    className: 'test',
                    buttons: false
                });

                function buildModalMarkup(templateString, data) {
                    var html = [];
                    var $template = $(templateString);
                    var $repeaterTemplate = $template.find('[data-payload-repeater]');

                    for (var key in data) {
                        if (data.hasOwnProperty(key)) {
                            var $row = $repeaterTemplate.clone();

                            $row.find('[data-payload-key]').text(key).removeAttr('data-payload-key');
                            if (typeof data[key] == 'string') {
                                $row.find('[data-payload-value]').text(data[key]).removeAttr('data-payload-value');
                            } else {
                                var str = ''
                                for (var inKey in data[key]) {
                                    if (data[key].hasOwnProperty(inKey)) {
                                        str += inKey + ' : ' + data[key][inKey] + "\n"
                                    }
                                }

                                var chunks = [];
                                var chunkSize = 50;
                                var newStr = '';
                                while (str) {
                                    if (str.length < chunkSize) {
                                        chunks.push(str);
                                        break;
                                    }
                                    else {
                                        var temp = str.substr(0, chunkSize);
                                        chunks.push(temp);
                                        newStr += temp + "\n";
                                        str = str.substr(chunkSize);
                                    }
                                }

                                $row.find('[data-payload-value]').text(newStr).removeAttr('data-payload-value');
                            }
                            html.push($row);
                        }
                    }

                    $template.find('[data-payload-repeater]').replaceWith(html);

                    return $template;
                }
            }
        });
    </script>
@endsection
