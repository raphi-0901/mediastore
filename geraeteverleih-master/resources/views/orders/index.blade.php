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
                    <h4 class="page-title">Bestellungen</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @include('orders.deleteOrder')
        <div class="row">
            @if(Auth::user()->isAdmin() || Auth::user()->isTeacher() && Auth::user()->types->count() != 0)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4>Bestellungen</h4>
                            <div class="form-group mt-2">
                                <input type="text" class="form-control date" id="singledaterange"
                                       data-toggle="date-picker"
                                       data-cancel-class="btn-warning">
                            </div>
                            <table id="sort-datatable-all" class="table table-centered table-striped mt-4 mobile-table">
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
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if(!Auth::user()->isAdmin())
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4>Ihre Bestellungen</h4>
                            <div class="form-group mt-2">
                                <input type="text" class="form-control date" id="yourOrdersDateRange"
                                       data-toggle="date-picker"
                                       data-cancel-class="btn-warning">
                            </div>
                            <table id="sort-datatable-yourOrders"
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
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <script type="text/javascript">
        let orders = null;

        let from = null;
        let to = null;

        $(document).ready(function () {
            let orderID = null;
            let rowAll = null
            let rowYours = null

            let OFfrom = document.cookie.split(';').find(c => c.indexOf('OFfrom') !== -1)
            let OFto = document.cookie.split(';').find(c => c.indexOf('OFto') !== -1)

            if (OFfrom && OFto) {
                from = moment(OFfrom.split('=')[1], 'YYYY-MM-DD')
                to = moment(OFto.split('=')[1], 'YYYY-MM-DD')
            } else if (OFfrom && !OFto) {
                from = moment(OFfrom.split('=')[1], 'YYYY-MM-DD')
                to = moment(from).add(1, "months")
            } else if (!OFfrom && OFto) {
                to = moment(OFto.split('=')[1], 'YYYY-MM-DD')
                from = moment(to).subtract(1, "months")
            } else if (!OFfrom && !OFto) {
                from = moment()
                to = moment().add(1, "months")
            }

            let dataTableAll = $('#sort-datatable-all').DataTable({
                keys: !0,
                paging: false,
                info: false,
                language: {
                    "zeroRecords": "Keine passenden Einträge gefunden",
                    "infoEmpty": "Keine Einträge vorhanden",
                    "search": "Suchen"
                },
                "rowCallback": function (row, data) {
                    $('td:eq(0)', row).attr('data-title', 'Schüler')
                    $('td:eq(1)', row).attr('data-title', 'Von')
                    $('td:eq(2)', row).attr('data-title', 'Bis')
                    $('td:eq(3)', row).attr('data-title', 'Status')
                }
            })

            let dataTableYours = $('#sort-datatable-yourOrders').DataTable({
                keys: !0,
                paging: false,
                info: false,
                language: {
                    "zeroRecords": "Keine passenden Einträge gefunden",
                    "infoEmpty": "Keine Einträge vorhanden",
                    "search": "Suchen"
                },
                "rowCallback": function (row, data) {
                    $('td:eq(0)', row).attr('data-title', 'Von')
                    $('td:eq(1)', row).attr('data-title', 'Bis')
                    $('td:eq(2)', row).attr('data-title', 'Status')
                }
            })

            //ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $('#sort-datatable-all tbody').on('click', 'tr', function () {
                rowAll = this;
            });

            $('#sort-datatable-yourOrders tbody').on('click', 'tr', function () {
                rowYours = this;
            });

            // One modal for every "edit user" - Set data
            $('#deleteOrder').on('show.bs.modal', function (event) {
                let button = $(event.relatedTarget) // Button that triggered the modal
                // Extract info from data-* attributes
                orderID = button.data('id')
                $('#dOrderDates').html(button.data('dates'))
                $('#dOrderName').html(button.data('displayname'))
            })

            $('#btnDeleteOrder').on('click', function () {
                let route = '{{route('orders.destroy', ':id')}}'.replace(':id', orderID)

                $.ajax({
                    type: 'DELETE',
                    url: route,
                    success: function (data) {
                        if (rowAll) {
                            dataTableAll.row(rowAll).remove().draw();
                            rowAll = null;
                        } else if (rowYours) {
                            dataTableYours.row(rowYours).remove().draw();
                            rowYours = null;
                        }

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

            const drOptionsMobile = {
                "locale": {
                    "customRangeLabel": "Eigenen Zeitraum auswählen",
                    "format": "D. MMM YY",
                    "separator": " - ",
                    "applyLabel": "OK",
                    "cancelLabel": "Abbr.",
                    "fromLabel": "Von",
                    "toLabel": "Bis",
                    "weekLabel": "W",
                    "daysOfWeek": [
                        "So",
                        "Mo",
                        "Di",
                        "Mi",
                        "Do",
                        "Fr",
                        "Sa",
                    ],
                    "monthNames": [
                        "Januar",
                        "Februar",
                        "März",
                        "April",
                        "Mai",
                        "Juni",
                        "Juli",
                        "August",
                        "September",
                        "Oktober",
                        "November",
                        "Dezember"
                    ],
                },
                'autoApply': true,
                "startDate": from,
                "endDate": to,
                "opens": "center",
            };
            const drOptionsDesktop = {
                ranges: {
                    'Nächste Woche': [moment().add(7, 'days').startOf('week'), moment().add(7, 'days').endOf('week')],
                    'Nächsten zwei Wochen': [moment().add(1, 'weeks').startOf('week'), moment().add(2, 'weeks').endOf('week')],
                    'Dieser Monat': [moment().startOf('month'), moment().endOf('month')],
                    'Nächster Monat': [moment().add(1, 'months').startOf('month'), moment().add(1, 'months').endOf('month')],
                },
                "locale": {
                    "customRangeLabel": "Eigenen Zeitraum auswählen",
                    "format": "D. MMM YYYY",
                    "separator": " - ",
                    "applyLabel": "OK",
                    "cancelLabel": "Abbrechen",
                    "fromLabel": "Von",
                    "toLabel": "Bis",
                    "weekLabel": "W",
                    "daysOfWeek": [
                        "So",
                        "Mo",
                        "Di",
                        "Mi",
                        "Do",
                        "Fr",
                        "Sa",
                    ],
                    "monthNames": [
                        "Januar",
                        "Februar",
                        "März",
                        "April",
                        "Mai",
                        "Juni",
                        "Juli",
                        "August",
                        "September",
                        "Oktober",
                        "November",
                        "Dezember"
                    ],
                },
                'autoApply': true,
                "startDate": from,
                "endDate": to,
            };

            //different daterangepicker for small devices
            if (screen.width >= 564) {
                $('#singledaterange').daterangepicker(drOptionsDesktop);
                $('#yourOrdersDateRange').daterangepicker(drOptionsDesktop);
            } else {
                $('#singledaterange').daterangepicker(drOptionsMobile);
                $('#yourOrdersDateRange').daterangepicker(drOptionsMobile);
            }


            //for mobile devices. keyboard should not appear.
            $('#singledaterange').on('focus', function (e) {
                $(this).blur()
            })

            //for mobile devices. keyboard should not appear.
            $('#yourOrdersDateRange').on('focus', function (e) {
                $(this).blur()
            })

            $('#singledaterange').on('showCalendar.daterangepicker', function (ev, picker) {
                if (screen.width >= 564)
                    $('.ranges').next().attr('style', '');
                else
                    $('.ranges').next().attr('style', 'border: none');
            });

            $('#yourOrdersDateRange').on('showCalendar.daterangepicker', function (ev, picker) {
                if (screen.width >= 564)
                    $('.ranges').next().attr('style', '');
                else
                    $('.ranges').next().attr('style', 'border: none');
            });

            $('#singledaterange').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('D. MMM YYYY') + ' - ' + picker.endDate.format('D. MMM YYYY'));
                from = picker.startDate;
                to = picker.endDate;
                document.cookie = 'OFfrom=' + from.format('YYYY-MM-DD') + ";max-age=" + 60 * 10;
                document.cookie = 'OFto=' + to.format('YYYY-MM-DD') + ";max-age=" + 60 * 10;
                filterOrders(false);
            });

            $('#yourOrdersDateRange').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('D. MMM YYYY') + ' - ' + picker.endDate.format('D. MMM YYYY'));
                from = picker.startDate;
                to = picker.endDate;
                document.cookie = 'OFfrom=' + from.format('YYYY-MM-DD') + ";max-age=" + 60 * 10;
                document.cookie = 'OFto=' + to.format('YYYY-MM-DD') + ";max-age=" + 60 * 10;
                filterOrders(true);
            });

            function filterOrders(isYours) {
                $.ajax({
                    url: '{{route('orders.filter')}}',
                    type: 'get',
                    data: {
                        from: from.format("YYYY-MM-DD"),
                        to: to.format("YYYY-MM-DD"),
                        onlyYours: isYours ? 1 : 0,
                    },
                    success: function (data) {
                        orders = data.orders

                        if (isYours)
                            dataTableYours.clear();
                        else
                            dataTableAll.clear();

                        orders.forEach(function (order) {
                            if (isYours)
                                dataTableYours.row.add([moment(order.from).format('D. MMM YYYY'), moment(order.to).format('D. MMM YYYY'), order.status, buildActionButtons(order, true)]);
                            else
                                dataTableAll.row.add([order.userDisplayName, moment(order.from).format('D. MMM YYYY'), moment(order.to).format('D. MMM YYYY'), order.status, buildActionButtons(order, false)]);

                        })
                        if (isYours)
                            dataTableYours.draw(false);
                        else
                            dataTableAll.draw(false);
                    },
                    error: function (data) {
                        $.toast({
                            heading: "Fehler",
                            text: data.responseJSON.error,
                            icon: 'error',
                            position: 'top-right',
                            stack: stack,
                            loaderBg: 'rgba(0,0,0,0.2)',
                            hideAfter: 5000   // in milli seconds
                        })
                    }
                })
            }

            function buildActionButtons(order, isYours) {
                let route = '{{route('orders.show', ':id')}}'.replace(':id', order.id)
                if (isYours)
                    return `<a href="${route}"
                                           class="btn btn-secondary mt-1 mr-1">
                                            <i class="fas fa-eye"></i></a>`;
                else
                    return `<div class="d-flex"> <a href="${route}"
                                           class="btn btn-secondary mt-1 mr-1">
                                            <i class="fas fa-eye"></i></a>
                <div data-toggle="modal" data-target="#deleteOrder"
            class="btn btn-danger mt-1 mr-1"
data-id="${order.id}"
                data-displayName="${order.userDisplayName}"
                data-dates="${moment(order.from).format('D. MMM YYYY') + " - " + moment(order.to).format('D. MMM YYYY')}">
                    <i class="fas fa-trash"></i></div></div>`;
            }

            filterOrders(true);
            filterOrders(false);
        })
    </script>
@endsection
