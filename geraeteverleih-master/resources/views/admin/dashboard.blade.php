@extends('layouts.app')
@section('body-bottom')
    <script src="{{asset("js/vendor/dropzone.min.js")}}"></script>
    <script src="{{asset("/js/ui/component.fileupload.js")}}"></script>
    <script src="{{asset("/js/vendor/jquery.dataTables.min.js")}}"></script>
    <script src="{{asset("/js/vendor/dataTables.bootstrap4.js")}}"></script>
    <script src="{{asset("js/vendor/apexcharts.min.js")}}"></script>
@endsection
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Dashboard</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row d-flex flex-wrap">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100 tilebox-one">
                    <div class="card-body">
                        <i class="fas fa-users float-right"></i>
                        <h6 class="text-uppercase mt-0">Benutzer</h6>
                        <h2 class="my-2 pt-1">{{$userCount}}</h2>
                        <a href="{{route("users.index")}}" class="btn btn-warning">Benutzer</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100 tilebox-one">
                    <div class="card-body">
                        <i class="fas fa-list-alt float-right"></i>
                        <h6 class="text-uppercase mt-0">Bestellungen</h6>
                        <h2 class="my-2 pt-1">{{$orderCount}}</h2>
                        <a href="{{route("orders.index")}}" class="btn btn-warning">Übersicht</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100 tilebox-one">
                    <div class="card-body">
                        <i class="fas fa-tags float-right"></i>
                        <h6 class="text-uppercase mt-0">Geräte</h6>
                        <h2 class="my-2 pt-1">{{$deviceCount}}</h2>
                        <a href="{{route("devices.index")}}" class="btn btn-warning">Liste</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Geräte-Auslastung</h4>
                        <div id="workLoad"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card tilebox-one">
                    <div class="card-body">
                        <i class="fas fa-tools float-right"></i>
                        <h6 class="text-uppercase mt-0">Einstellungen</h6>
                        <div class="form-row mt-4">
                            <div class="col-md-6">Maximale Dauer: <h4
                                    class="my-2 pt-1 d-inline">{{$settings->find("maxSpan")->value}} Tage</h4></div>
                        </div>
                        <div class="form-row mt-4">
                            <div class="col-12">Maximale Anzahl an Geräten pro Bestellungen: <h4
                                    class="my-2 pt-1 d-inline">{{$settings->find("maxDeviceCount")->value}}
                                    Geräte</h4>
                            </div>
                        </div>
                        <div class="form-row mt-4">
                            <div class="col-12">Geräte reservieren für: <h4
                                    class="my-2 pt-1 d-inline">{{$settings->find("removeFromShoppingCartAfter")->value}}
                                    Minuten</h4></div>
                        </div>
                        <div class="form-row mt-4">
                            <div class="col-12">Schüler benachrichtigen: <h4
                                    class="my-2 pt-1 d-inline">{{$settings->find("sendEmailBefore")->value}} Tage
                                    davor</h4></div>
                        </div>
                        <div class="form-row mt-4">
                            <a href="{{route("settings")}}" class="btn btn-dark">Einstellungen</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card tilebox-one">
                    <div class="card-body">
                        <i class="fas fa-archive float-right"></i>
                        <h6 class="text-uppercase mt-0 mb-4">Kategorien</h6>
                        <div id="types"></div>
                        <div class="form-row mt-4">
                            <a href="{{route("types.index")}}" class="btn btn-dark">Bearbeiten</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        let types = {!! json_encode($types) !!};
        let workLoad = {!! json_encode($workLoad) !!};
        let deviceCount = {!! json_encode($deviceCount) !!};

        $(document).ready(function () {
            let presenceColors = ['#fa6767', '#f9bc0d', '#42d29d', '#8C9EFF', '#44badc']
            //let colors = ['#44badc', '#42d29d', '#f9bc0d', '#8C9EFF', '#fa6767']
            let gradientColors = ['#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff']

            // Andere Farben bei Darkmode
            if (window.matchMedia('(prefers-color-scheme: dark)').matches)
                gradientColors = ['#474d56', '#474d56', '#474d56', '#474d56', '#474d56']

            let locale = {
                "name": "de",
                "options": {
                    "months": ["Jänner", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
                    "shortMonths": ["Jän", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
                    "days": ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"],
                    "shortDays": ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
                    "toolbar": {
                        "exportToSVG": "Download SVG",
                        "exportToPNG": "Download PNG",
                        "menu": "Menü",
                        "selection": "Auswahl",
                        "selectionZoom": "Auswahl zoomen",
                        "zoomIn": "Vergrößern",
                        "zoomOut": "Verkleinern",
                        "pan": "Frei bewegen",
                        "reset": "Zoom zurücksetzen"
                    }
                }
            }

            let dates = []
            workLoad[0].data.forEach(function (item) {
                dates.push(item.date)
            })

            let options = {
                colors: presenceColors,
                fill: {
                    type: "gradient",
                    gradient: {
                        gradientToColors: gradientColors,
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.9,
                        stops: [0, 90, 100]
                    }
                },
                series: workLoad,
                chart: {
                    height: 350,
                    type: 'area',
                    locales: [locale],
                    defaultLocale: 'de',
                },
                annotations: {
                    xaxis: [{
                        x: new Date(new Date().setHours(0, 0, 0, 0)).getTime(),
                        borderColor: '#999',
                        yAxisIndex: 0,
                        label: {
                            show: true,
                            text: 'Heute',
                            style: {
                                color: "#fff",
                                background: '#775DD0'
                            }
                        }
                    }]
                },
                markers: {
                    size: 4,
                },
                dataLabels: {enabled: false,},
                stroke: {
                    curve: 'smooth',
                    width: 5,
                },
                xaxis: {
                    labels: {
                        datetimeFormatter: {
                            year: 'yyyy',
                            month: 'MMM. yy',
                            day: 'dd. MMM.',
                            hour: 'HH:mm'
                        }
                    },
                    type: 'datetime',
                    categories: dates
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return value + "%";
                        }
                    },
                    min: 0,
                    max: 100,
                },
                tooltip: {
                    x: {format: 'dd. MMM. yyyy'},
                },
            }
            let customersHistoryChart = new ApexCharts(document.querySelector("#workLoad"), options)
            customersHistoryChart.render()

            let html = '';

            function buildSubTypes(type) {
                if (type.parent_id == null) {
                    html += `
       <div class="card-header pl-0" id="hd-${type.id}" data-toggle="collapse" data-target="#collapse-${type.id}" aria-expanded="true" aria-controls="collapse-${type.id}">
      <h5 class="mb-0 btn">
          ${type.name}
      </h5>
    </div>`;
                } else {
                    html += `
       <div class="mb-2 font-weight-bold ml-1 mt-2" id="hd-${type.id}" data-toggle="collapse" data-target="#collapse-${type.id}" aria-expanded="true" aria-controls="collapse-${type.id}">
          ${type.name}
    </div>`
                }
                html += `<div id="collapse-${type.id}" class="collapse" aria-labelledby="hd-${type.id}" data-parent="#ac-${type.id}">
      <div class="card-body pt-0 pb-1">`;

                for (let child of types.filter(t => t.parent_id == type.id)) {
                    if (types.filter(t => t.parent_id == child.id).length === 0) {
                        html += `<div class="mb-2 ml-1 mt-2">
                ${child.name}
            </div>`;
                    } else
                        buildSubTypes(child);

                }
                html += "</div></div>";
            }

            for (let type of types.filter(t => t.parent_id === null))
                buildSubTypes(type);

            $('#types').html(html);
        })

    </script>
@endsection

