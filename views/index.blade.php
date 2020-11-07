<!DOCTYPE html>
<html ng-app="myApp">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log Reader</title>

    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js"></script>

    <script>
        var angularUrl = '{{asset('laravel-log-reader/angular.min.js')}}';
        window.angular || document.write('<script src="' + angularUrl + '">\x3C/script>')
    </script>


    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f4f4;
            font-family: sans-serif;
        }

        .btn {
            text-decoration: none;
            background: antiquewhite;
            padding: 5px 12px;
            border-radius: 25px;
        }

        header {
            min-height: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #3F51B5;
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        }
        header .btn_clear_all {
            background: #de4f4f;
            color: #fff;
        }
        header .name {
            font-size: 25px;
            font-weight: 500;
            color: white;
        }

        .content {
            margin-top: 65px;
            padding: 15px;
            background: #fff;
            min-height: 100px;
        }

        .content .date_selector {
            min-height: 26px;
            min-width: 130px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .top_content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top_content .top_content_left {
            display: flex;
        }

        .top_content .top_content_left .log_filter {
            display: flex;
            align-items: center;
            margin-left: 15px;
        }

        .top_content .top_content_left .log_filter .log_type_item {
            margin-right: 4px;
            background: #eae9e9;
            max-height: 20px;
            font-size: 11px;
            box-sizing: border-box;
            padding: 4px 6px;
            cursor: pointer;
        }

        .top_content .top_content_left .log_filter .log_type_item.active {
            background: #2f2e2f;
            color: white;
        }

        .top_content .top_content_left .log_filter .log_type_item.clear {
            background: #607D8B;
            color: white;
        }

        table {
            border: 1px solid #ccc;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        table tr {
            border: 1px solid #e8e8e8;
            padding: 5px;
        }
        table tr:hover {
            background: #f4f4f4;
        }
        thead tr td {
            background: #717171;
            color: #fff;
        }

        table th,
        table td {
            padding: 5px;
            font-size: 14px;
            color: #666;
        }

        table th {
            font-size: 14px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        @media screen and (max-width: 700px) {
            .top_content {
                flex-direction: column;
            }

            .top_content .top_content_left {
                flex-direction: column;
            }

            .top_content .log_filter {
                flex-wrap: wrap;
            }

            .top_content .log_filter .log_type_item {
                margin-bottom: 3px;
            }
        }

        @media screen and (max-width: 600px) {
            header {
                flex-direction: column;
            }

            header .name {
                margin-bottom: 20px;
            }

            .content {
                margin-top: 90px;
            }

            .btn {
                font-size: 13px;
            }

            .dt_box,
            .selected_date {
                text-align: center;
            }

            .responsive_table {
                max-width: 100%;
                overflow-x: auto;
            }

            table {
                border: 0;
            }

            table thead {
                display: none;
            }

            table tr {
                border-bottom: 2px solid #ddd;
                display: block;
                margin-bottom: 10px;
            }

            table td {
                border-bottom: 1px dotted #ccc;
                display: block;
                font-size: 15px;
            }

            table td:last-child {
                border-bottom: 0;
            }

            table td:before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
                text-transform: uppercase;
            }
        }

        .badge {
            padding: 2px 8px;
            -webkit-border-radius: 25px;
            -moz-border-radius: 25px;
            border-radius: 25px;
            font-size: 11px;
        }

        .badge.info {
            background: #6bb5b5;
            color: #fff;
        }

        .badge.warning {
            background: #f7be57;
        }

        .badge.critical {
            background: #de4f4f;
            color: #fff;
        }

        .badge.emergency {
            background: #ff6060;
            color: white;
        }

        .badge.notice {
            background: bisque;
        }

        .badge.debug {
            background: #8e8c8c;
            color: white;
        }

        .badge.alert {
            background: #4ba4ea;
            color: white;
        }

        .badge.error {
            background: #c36a6a;
            color: white;
        }
    </style>


</head>
<body ng-controller="LogCtrl">
<header>
    <div class="name">@{{ title }}</div>
    <div class="actions">
        <a class="btn btn_clear_all" href="#" ng-click="clearAll()">Clear All</a>
        <a class="btn" href="{{url(config('laravel-log-reader.admin_panel_path'))}}">Goto Admin Panel</a>
        <a class="btn" href="https://laravelarticle.com/laravel-log-reader" title="Laravel Log Reader">Doc</a>
    </div>
</header>
<section class="content">
    <div class="top_content">
        <div class="top_content_left">
            <div>
                <p class="selected_date" style="font-size: 14px;"><strong>
                        <span ng-show="response.success">Showing Logs: @{{data.date}}</span>
                        <span ng-hide="response.success">@{{response.message}}</span>
                    </strong></p>
            </div>
            <div class="log_filter">
                <div class="log_type_item" ng-class="selectedType==tp?'active':''"
                     ng-repeat="tp in logTypes track by $index"
                     ng-click="filterByType(tp)">@{{ tp }}
                </div>
                <div class="log_type_item clear" ng-show="selectedType" ng-click="selectedType=undefined">CLEAR FILTER
                </div>
            </div>
        </div>

        <div class="top_content_right">
            <p class="dt_box">Select Date: <select class="date_selector" ng-model="selectedDate"
                                                   ng-change="init(selectedDate)">
                    <option ng-repeat="dt in data.available_log_dates"
                            value="@{{ dt }}">@{{ dt }}
                    </option>
                </select>
            </p>
        </div>
    </div>

    <div>
        <div class="responsive_table">
            <table>
                <thead>
                <tr>
                    <td width="140">Timestamp</td>
                    <td width="120">Env</td>
                    <td width="120">Type</td>
                    <td>Message</td>
                </tr>
                </thead>

                <tr ng-repeat="log in data.logs |filter: selectedType track by $index">
                    <td>@{{ log.timestamp }}</td>
                    <td>@{{log.env}}</td>
                    <td><span class="badge @{{ log.type.toLowerCase() }}">@{{ log.type }}</span></td>
                    <td>@{{ log.message }}</td>
                </tr>
            </table>
        </div>
    </div>


    <script>
        var myApp = angular.module("myApp", []);
        myApp.controller("LogCtrl", function ($scope, $http) {
            $scope.title = "Log Reader";
            $scope.selectedType = undefined;
            $scope.logTypes = ['INFO', 'EMERGENCY', 'CRITICAL', 'ALERT', 'ERROR', 'WARNING', 'NOTICE', 'DEBUG'];
            var originalData = null;

            $scope.init = function (date) {
                var url = '';
                if (date !== '' && date !== undefined) {
                    url = '{{url(config('laravel-log-reader.api_route_path'))}}?date=' + date
                } else {
                    url = '{{url(config('laravel-log-reader.api_route_path'))}}'
                }

                $http.get(url)
                    .success(function (data) {
                        $scope.response = data;
                        $scope.data = data.data;
                        originalData = data.data;
                    })
            };

            $scope.init();

            $scope.filterByType = function (tp) {
                $scope.selectedType = tp
            };

            $scope.clearAll = function () {
                if (confirm("Are you sure?")) {
                    var url = '{{url(config('laravel-log-reader.view_route_path'))}}'
                    $http.post(url, {'clear': true})
                        .success(function (data) {
                            if (data.success) {
                                alert(data.message);
                                $scope.init();
                            }
                        })
                }
            }

        })
    </script>
</section>

</body>
</html>