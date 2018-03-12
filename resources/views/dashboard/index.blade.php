@extends('nodes.backend::layouts.base')

@section('page-header-top')
    <h3>Dashboard</h3>
@endsection

@section('content')

    <div class="row">
        <div class="margin-top">
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
    </div>
@endsection

@section('project-js')
    <script>

        $(document).ready(function() {

            // Bar charts
            var barChartData = {!! json_encode($dashboardCollection->getChartDataForType('bar-chart')) !!};

            for (var i = 0; i < barChartData.length; i++) {
                if (!barChartData[i]) {
                    continue;
                }
                var ctx = $("#" + barChartData[i].id).get(0).getContext("2d");
                var myNewChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: barChartData[i]['labels'],
                        datasets: [
                            {
                                backgroundColor: {!! json_encode(\Nodes\Backend\Dashboard\Tiles\Charts\Chart::$colors)!!},
                                data: barChartData[i]['data']
                            }
                        ]
                    },
                    options: {
                        legend: { display: false },
                        title: {
                            display: true,
                            text: barChartData[i]['title']
                        }
                    }
                });
            }

            // Line charts
            var lineChartData = {!! json_encode($dashboardCollection->getChartDataForType('line-chart')) !!};
            for (var i = 0; i < lineChartData.length; i++) {
                if (!lineChartData[i]) {
                    continue;
                }

                var ctx = $("#" + lineChartData[i].id).get(0).getContext("2d");
                var myNewChart = new Chart(ctx, {
                    type:'line',
                    data: {
                        labels: lineChartData[i]['labels'],
                        datasets: [
                            {
                                backgroundColor: {!! json_encode(\Nodes\Backend\Dashboard\Tiles\Charts\Chart::$colors)!!},
                                data: lineChartData[i]['data']
                            }
                        ]
                    },
                    options: {
                        legend: { display: false },
                        title: {
                            display: true,
                            text: lineChartData[i]['title']
                        }
                    }
                }, {responsive: true});
            }

        });

    </script>
@endsection