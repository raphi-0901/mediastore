@extends('layouts.app')
@section('head-bottom')
    <link rel="stylesheet" href="{{asset('css/datepicker.css')}}"/>
@endsection
@section('body-bottom')
    <script src="{{asset("/js/vendor/jquery.dataTables.min.js")}}"></script>
    <script src="{{asset("/js/vendor/dataTables.bootstrap4.js")}}"></script>
@endsection
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h2 class="page-title">{{$device->name}}</h2>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            @if($device->description)
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title">Beschreibung:</h4>
                                            <p class="card-text">{{$device->description}}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($device->serial)
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title">Seriennummer:</h4>
                                            <p class="card-text">{{$device->serial}}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($device->qr_id)
                                @if(!Auth::user()->isStudent())
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="card-title">QR-ID:</h4>
                                                <a href="{{route("devices.downloadQRCode", $device->id)}}"
                                                   class="btn btn-primary float-right"><i class="fa fa-download"></i></a>
                                                <p class="card-text">{{$device->qr_id}}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endif
                            @if($device->note)
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title">Notizen:</h4>
                                            <p class="card-text">{{$device->note}}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Kategorie:</h4>
                                        <p class="card-text">{{$type->name}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-9">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-group mt-3">
                                            <input type="hidden" id="date" name="date">
                                            <div id="datepicker" data-date="today()"></div>
                                        </div>
                                        <h5 class="text-muted px-1 p-2 px-sm-3 mt-2">
                                            <i class="far fa-circle text-danger"></i> Nicht verfügbar
                                            <i class="far fa-circle text-info ml-2"></i> Reserviert
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 d-flex justify-content-center align-items-center">
                                <div class="card">
                                    <div class="card-body">
                                        {{QrCode::size(250)->margin(2)->encoding('UTF-8')->errorCorrection('H')->generate($device->qr_id)}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(!Auth::user()->isStudent())
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3>Bestellhistorie</h3>
                            <div class="col-lg-12">
                                @if($device->orders->count() != 0)
                                    <table id="sort-datatable"
                                           class="table table-centered table-striped mt-4 mobile-table">
                                        <thead>
                                        <tr>
                                            <th>Schüler</th>
                                            <th>Von</th>
                                            <th>Bis</th>
                                            <th>Status</th>
                                            <th class="no-sort-symbol"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($device->orders as $order)
                                            <tr>
                                                <td data-title="Schüler">{{$order->user->displayName()}}</td>
                                                <td data-title="Von"
                                                    data-sort="{{$order->from}}">{{$order->from->isoFormat("Do MMM. YYYY")}}</td>
                                                <td data-title="Bis"
                                                    data-sort="{{$order->to}}">{{$order->to->isoFormat("Do MMM. YYYY")}}</td>
                                                <td data-title="Status">{!! $order->status()[2] !!}</td>
                                                <td>
                                                    <a href="{{route('orders.show', $order->id)}}"
                                                       class="btn btn-secondary mt-1 mr-1">
                                                        <i class="fas fa-eye"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    Gerät war in noch keiner Bestellung!
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <script type="text/javascript">
            let unavailableDays = {!! json_encode($unavailableDays) !!};
            let shoppingCart = {!! json_encode($shoppingCart) !!};

            $(function () {
                $(document).ready(function () {
                    function constructDatepicker() {
                        $('#datepicker').datepicker({
                            inline: true,
                            weekStart: 1,
                            language: 'de',
                            //Highlight booked Days
                            beforeShowDay: function (date) {
                                let formattedDate = moment(date).format("Y-MM-DD")
                                if ($.inArray(formattedDate, unavailableDays) != -1) {
                                    return {classes: "disabled unavailable"}
                                }

                                if ($.inArray(formattedDate, shoppingCart) != -1) {
                                    return {classes: "disabled shoppingCart"}
                                }

                                return;
                            },
                        })
                    }

                    let dataTable = $('#sort-datatable').DataTable({
                        keys: !0,
                        paging: false,
                        info: false,
                        language: {
                            "zeroRecords": "Keine passenden Einträge gefunden",
                            "infoEmpty": "Keine Einträge vorhanden",
                            "search": "Suchen"
                        },
                    })

                    constructDatepicker()
                })
            })
        </script>
@endsection
