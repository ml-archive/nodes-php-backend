@extends('nodes.backend::layouts.base')

@section('page-header-top')
    <h1>Dashboard</h1>
@endsection

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
                            <iframe class="iframe" src="{{$dashboard->getUrl()}}" width=100% height="300" scrolling="no" frameborder="no"></iframe>
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
                        <div class="panel-body">
                            @if($dashboard->getChartData())
                                <div class="chart-responsive bar-chart">
                                    <style>
                                        #{{$dashboard->getId()}} {
                                            height: 300px !important;
                                        }
                                    </style>
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
                        <div class="panel-body" >
                            @if($dashboard->getChartData())
                                <div class="chart-responsive line-chart">
                                    <style>
                                        #{{$dashboard->getId()}} {
                                            height: 300px !important;
                                        }
                                    </style>
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

@section('project-js')
    <script>

        $(document).ready(function() {

            // Bar charts
            var arrA = {!! json_encode($dashboardCollection->getBarChartsAsChartData()) !!};

            for (var i = 0; i < arrA.length; i++) {
                if (!arrA[i]) {
                    continue;
                }
                var ctx = $("#" + arrA[i].id).get(0).getContext("2d");
                var myNewChart = new Chart(ctx).Bar({
                    labels: arrA[i]['labels'],
                    datasets: [
                        {
                            label: arrA[i].title,
                            fillColor: "rgba(220,220,220,0.2)",
                            strokeColor: "rgba(220,220,220,1)",
                            pointColor: "rgba(220,220,220,1)",
                            pointStrokeColor: "#fff",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(220,220,220,1)",
                            data: arrA[i]['data']
                        }
                    ]
                });
            }

            // Bar charts
            var arrB = {!! json_encode($dashboardCollection->getLineChartsAsChartData()) !!};
            for (var i = 0; i < arrB.length; i++) {
                if (!arrB[i]) {
                    continue;
                }

                var ctx = $("#" + arrB[i].id).get(0).getContext("2d");
                var myNewChart = new Chart(ctx).Line({
                    labels: arrB[i]['labels'],
                    datasets: [
                        {
                            label: arrB[i].title,
                            fillColor: "rgba(220,220,220,0.2)",
                            strokeColor: "rgba(220,220,220,1)",
                            pointColor: "rgba(220,220,220,1)",
                            pointStrokeColor: "#fff",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(220,220,220,1)",
                            data: arrB[i]['data']
                        }
                    ]
                }, {responsive: true});
            }

        });

    </script>
@endsection