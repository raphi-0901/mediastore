@extends('layouts.app')
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
                    <h4 class="page-title"><a title="Sende E-Mail"
                                              href="mailto:{{$user->email}}">{{$user->displayName()}}, {{$user->class}}</a></h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @include('orders.deleteOrder')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Bestellungen</h4>
                        <table id="sort-datatable"
                               class="table table-centered table-striped mt-4 mobile-table">
                            <thead>
                            <tr>
                                <th>Von</th>
                                <th>Bis</th>
                                <th>Status</th>
                                <th class="no-sort-symbol"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($user->orders as $order)
                                <tr id="order-{{$order->id}}">
                                    <td data-title="Von"
                                        data-sort="{{$order->from}}">{{$order->from->isoFormat("Do MMM. YYYY")}}</td>
                                    <td data-title="Bis"
                                        data-sort="{{$order->to}}">{{$order->to->isoFormat("Do MMM. YYYY")}}</td>
                                    <td data-title="Status">{!! $order->status()[2] !!}</td>
                                    <td>
                                        <a href="{{route('orders.show', $order->id)}}"
                                           class="btn btn-secondary mt-1 mr-1">
                                            <i class="fas fa-eye"></i></a>
                                        <div data-toggle="modal" data-target="#deleteOrder"
                                             class="btn btn-danger mt-1 mr-1"
                                             data-id="{{$order->id}}">
                                            <i class="fas fa-trash"></i></div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($user->answerings->count() != 0)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4>Antworten</h4>
                            <table id="sort-datatable-answerings"
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
                                @foreach($user->answerings as $order)
                                    <tr id="order-{{$order->id}}">
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
                        </div>
                    </div>
                </div>
            @endif
            @if($user->givens->count() != 0)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4>Ausgaben</h4>
                            <table id="sort-datatable-givens"
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
                                @foreach($user->givens as $order)
                                    <tr id="order-{{$order->id}}">
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
                        </div>
                    </div>
                </div>
            @endif
            @if($user->returnings->count() != 0)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4>Zurücknahmen</h4>
                            <table id="sort-datatable-returnings"
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
                                @foreach($user->returnings as $order)
                                    <tr id="order-{{$order->id}}">
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
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            let orderID = null;
            let row = null

            const dtOption = {
                keys: !0,
                paging: false,
                info: false,
                language: {
                    "zeroRecords": "Keine passenden Einträge gefunden",
                    "infoEmpty": "Keine Einträge vorhanden",
                    "search": "Suchen"
                },
            };

            let dataTable = $('#sort-datatable').DataTable(dtOption)
            $('#sort-datatable-answerings').DataTable(dtOption)
            $('#sort-datatable-givens').DataTable(dtOption)
            $('#sort-datatable-returnings').DataTable(dtOption)

            //ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $('#sort-datatable tbody').on('click', 'tr', function () {
                row = this;
            });

            // One modal for every "edit user" - Set data
            $('#deleteOrder').on('show.bs.modal', function (event) {
                let button = $(event.relatedTarget) // Button that triggered the modal
                // Extract info from data-* attributes
                orderID = button.data('id')
            })

            $('#btnDeleteOrder').on('click', function () {
                let route = '{{route('orders.destroy', ':id')}}'.replace(':id', orderID)

                $.ajax({
                    type: 'DELETE',
                    url: route,
                    success: function (data) {
                        dataTable.row(row).remove().draw();
                        $.toast({
                            heading: "Erfolgreich",
                            text: data.success,
                            icon: 'success',
                            position: 'top-right',
                            stack: 4,
                            loaderBg: 'rgba(0,0,0,0.2)',
                            hideAfter: 5000   // in milli seconds
                        })
                    },
                    error: function (data) {
                        $.toast({
                            heading: "Fehler",
                            text: data.responseJSON.error,
                            icon: 'error',
                            position: 'top-right',
                            stack: 4,
                            loaderBg: 'rgba(0,0,0,0.2)',
                            hideAfter: 5000   // in milli seconds
                        })
                    }
                });
            })
        })
    </script>
@endsection
