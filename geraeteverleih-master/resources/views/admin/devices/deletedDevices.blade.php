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
                    <h4 class="page-title">Gelöschte Geräte</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        @include('admin.devices.restoreDevice')
        @include('admin.devices.forceDeleteDevice')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Gelöschte Geräte</h4>
                        <div class="mb-md-n4">
                            <a href="{{route("devices.index")}}"
                               class="btn btn-secondary position-relative z-index-2">Geräte</a>
                        </div>

                        <table id="sort-datatable" class="table table-centered table-striped mt-4 mobile-table">
                            <thead>
                            <tr>
                                <th>QR-ID</th>
                                <th>Name</th>
                                <th>Kategorie</th>
                                <th class="no-sort-symbol"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($devices as $device)
                                <tr>
                                    <td>{{$device->qr_id}}</td>
                                    <td>{{$device->name}}</td>
                                    <td>{{$device->type->name}}</td>
                                    <td>
                                        <a href="{{route('devices.show', $device->id)}}"
                                           class="btn btn-secondary mt-1 mr-1">
                                            <i class="fas fa-eye"></i></a>
                                        <div data-toggle="modal" data-target="#restoreDevice"
                                             class="btn btn-primary mt-1 mr-1"
                                             data-id="{{$device->id}}"
                                             data-name="{{$device->name}}">
                                            <i class="fas fa-trash-restore"></i></div>
                                        <div data-toggle="modal" data-target="#forceDeleteDevice"
                                             class="btn btn-danger mt-1 mr-1"
                                             data-id="{{$device->id}}"
                                             data-name="{{$device->name}}">
                                            <i class="fas fa-trash"></i></div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript">
        $(function () {
            let row = null
            let deviceID = null

            $(document).ready(function () {
                let dataTable = $('#sort-datatable').DataTable({
                    keys: !0,
                    paging: false,
                    info: false,
                    language: {
                        "zeroRecords": "Keine passenden Einträge gefunden",
                        "infoEmpty": "Keine Einträge vorhanden",
                        "search": "Suchen"
                    },
                    "rowCallback": function (row, data) {
                        $('td:eq(0)', row).attr('data-title', 'QR-ID')
                        $('td:eq(1)', row).attr('data-title', 'Name')
                        $('td:eq(2)', row).attr('data-title', 'Kategorie')
                    }
                })

                //ajax
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })

                $('#sort-datatable tbody').on('click', 'tr', function () {
                    row = this;
                });

                $('#restoreDevice').on('show.bs.modal', function (event) {
                    let button = $(event.relatedTarget) // Button that triggered the modal
                    deviceID = button.data('id')
                    let deviceName = button.data('name')
                    $('#rsDeviceName').html(deviceName)
                })

                $("#btnRestoreDevice").click(function (e) {
                    let route = '{{route('devices.restore', ':id')}}'.replace(':id', deviceID)
                    $.ajax({
                        type: 'PATCH',
                        url: route,
                        success: function (data) {
                            dataTable.row(row).remove().draw();
                            $.toast({
                                heading: "Erfolgreich",
                                text: data.success,
                                icon: 'success',
                                position: 'top-right',
                                stack: stack,
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
                                stack: stack,
                                loaderBg: 'rgba(0,0,0,0.2)',
                                hideAfter: 5000   // in milli seconds
                            })
                        }
                    })
                })

                // One modal for every "edit user" - Set data
                $('#forceDeleteDevice').on('show.bs.modal', function (event) {
                    let button = $(event.relatedTarget) // Button that triggered the modal
                    // Extract info from data-* attributes
                    deviceID = button.data('id')
                    let deviceName = button.data('name')
                    $('#fdDeviceName').html(deviceName)
                })

                $('#btnForceDeleteDevice').on('click', function () {
                    let route = '{{route('devices.forceDelete', ':id')}}'.replace(':id', deviceID)

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
                                stack: stack,
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
                                stack: stack,
                                loaderBg: 'rgba(0,0,0,0.2)',
                                hideAfter: 5000   // in milli seconds
                            })
                        }
                    });
                })
            })
        })
    </script>
@endsection
