@extends('nodes.backend::layout')
@section('content')
    <div class="row">
        @foreach($dashboardCollection as $dashboard)
            @if($dashboard->getType() == 'i-frame')
                <div class="col-sm-12 col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading border">
                            <h3 class="panel-title">{{$dashboard->getTitle()}}</h3>
                        </div>
                        <div class="panel-body">
                            <iframe src="{{$dashboard->getUrl()}}" width=100% height="300" scrolling="no" frameborder="no"></iframe>
                        </div>
                    </div>
                </div>
            @endif
            @if($dashboard->getType() == 'bar-chart')
                <div class="col-sm-12 col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading border">
                            <h3 class="panel-title">{{$dashboard->getTitle()}}</h3>
                        </div>
                        <div class="panel-body" style="height: 300px">
                            @if($dashboard->getChartData())
                                <div class="chart-responsive">
                                    <canvas id="{{$dashboard->getId()}}"></canvas>
                                </div>
                            @else
                                <h4 class="text-center">Failed to generate chart</h4>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            @if($dashboard->getType() == 'line-chart')
                <div class="col-sm-12 col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading border">
                            <h3 class="panel-title">{{$dashboard->getTitle()}}</h3>
                        </div>
                        <div class="panel-body" style="height: 300px">
                            @if($dashboard->getChartData())
                                <div class="chart-responsive">
                                    <canvas id="{{$dashboard->getId()}}"></canvas>
                                </div>
                            @else
                                <h4 class="text-center">Failed to generate chart</h4>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endsection

@section('backend-js')
    <script>
        // Bar charts
        var arr = {!! json_encode($dashboardCollection->getBarChartsAsChartData()) !!};
        for (var i = 0; i < arr.length; i++) {
            if (!arr[i]) {
                continue;
            }
            var ctx = $("#" + arr[i].id).get(0).getContext("2d");
            var myNewChart = new Chart(ctx).Bar({
                labels: arr[i]['labels'],
                datasets: [
                    {
                        label: arr[i].title,
                        fillColor: "rgba(220,220,220,0.2)",
                        strokeColor: "rgba(220,220,220,1)",
                        pointColor: "rgba(220,220,220,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data: arr[i]['data']
                    }
                ]
            });
        }

        // Bar charts
        var arr = {!! json_encode($dashboardCollection->getLineChartsAsChartData()) !!};
        for (var i = 0; i < arr.length; i++) {
            if (!arr[i]) {
                continue;
            }

            var ctx = $("#" + arr[i].id).get(0).getContext("2d");
            var myNewChart = new Chart(ctx).Line({
                labels: arr[i]['labels'],
                datasets: [
                    {
                        label: arr[i].title,
                        fillColor: "rgba(220,220,220,0.2)",
                        strokeColor: "rgba(220,220,220,1)",
                        pointColor: "rgba(220,220,220,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data: arr[i]['data']
                    }
                ]
            });
        }
    </script>
@endsection