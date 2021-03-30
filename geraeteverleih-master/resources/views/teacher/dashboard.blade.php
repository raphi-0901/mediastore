@extends('layouts.app')
@section('body-bottom')
    <script src="{{asset("/js/vendor/jquery.dataTables.min.js")}}"></script>
    <script src="{{asset("/js/vendor/dataTables.bootstrap4.js")}}"></script>
@endsection
@section('content')
    <div class="container-fluid">
        <h1>Hallo, {{\Illuminate\Support\Facades\Auth::user()->displayName()}}</h1>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Bestellungen, die nächste Woche anfangen.</h4>
                        @if($ordersGoingOutNextWeek->count() == 0)
                            <div class="text-white">Keine Bestellungen verfügbar.</div>
                        @else
                        <table id="sort-datatable-out" class="table table-centered table-striped mt-4 mobile-table">
                            <thead>
                            <tr>
                                <th>Von</th>
                                <th>Bis</th>
                                <th>Schüler</th>
                                <th>Status</th>
                                <th class="no-sort-symbol"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($ordersGoingOutNextWeek as $order)
                                <tr id="order-{{$order->id}}">
                                    <td data-title="Von">{{$order->from->isoFormat("Do MMM. YYYY")}}</td>
                                    <td data-title="Bis">{{$order->to->isoFormat("Do MMM. YYYY")}}</td>
                                    <td data-title="Schüler">{{$order->user->lastName . ' ' . $order->user->firstName}}</td>
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
                            @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Bestellungen, die nächste Woche zurückkommen.</h4>
                        @if($ordersComingBackNextWeek->count() == 0)
                            <div class="text-white">Keine Bestellungen verfügbar.</div>
                        @else
                            <table id="sort-datatable-back"
                                   class="table table-centered table-striped mt-4 mobile-table">
                                <thead>
                                <tr>
                                    <th>Von</th>
                                    <th>Bis</th>
                                    <th>Schüler</th>
                                    <th>Status</th>
                                    <th class="no-sort-symbol"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($ordersComingBackNextWeek as $order)
                                    <tr id="order-{{$order->id}}">
                                        <td data-title="Von">{{$order->from->isoFormat("Do MMM. YYYY")}}</td>
                                        <td data-title="Bis">{{$order->to->isoFormat("Do MMM. YYYY")}}</td>
                                        <td data-title="Schüler">{{$order->user->lastName . ' ' . $order->user->firstName}}</td>
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
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>
        $(document).ready(function () {
            $('#sort-datatable-out').DataTable({
                keys: !0,
                paging: false,
                info: false,
                language: {
                    "zeroRecords": "Keine passenden Einträge gefunden",
                    "infoEmpty": "Keine Einträge vorhanden",
                    "search": "Suchen"
                },
            })

            $('#sort-datatable-back').DataTable({
                keys: !0,
                paging: false,
                info: false,
                language: {
                    "zeroRecords": "Keine passenden Einträge gefunden",
                    "infoEmpty": "Keine Einträge vorhanden",
                    "search": "Suchen"
                },
            })
            //ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
        })
    </script>
@endsection

