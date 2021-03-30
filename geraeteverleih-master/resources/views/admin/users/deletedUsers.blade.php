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
                    <h4 class="page-title">Gelöschte Benutzer</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        @include('admin.users.restoreUser')
        @include('admin.users.forceDeleteUser')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Gelöschte Benutzer</h4>
                        <div class="mb-md-n4">
                            <a href="{{route("users.index")}}"
                               class="btn btn-secondary position-relative z-index-2">Benutzer</a>
                        </div>

                        <table id="sort-datatable"
                               class="table table-striped table-centered mt-4 mobile-table dt-responsive">
                            <thead>
                            <tr>
                                <th>Nachname</th>
                                <th>Vorname</th>
                                <th>Klasse</th>
                                <th>E-Mail</th>
                                <th class="no-sort-symbol"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $u)
                                <tr>
                                    <td>{{ ($u->lastName)}}</td>
                                    <td>{{ ($u->firstName)}}</td>
                                    <td>{{$u->class}}</td>
                                    <td><a href="mailto:{{$u->email}}">{{$u->email}}</a></td>
                                    <td data-title="" data-sort="">
                                        <a href="{{route('users.show', $u->id)}}" class="btn btn-secondary  mt-1 mr-1">
                                            <i class="fas fa-eye"></i></a>
                                        <div data-toggle="modal" data-target="#restoreUser"
                                             class="btn btn-primary mt-1 mr-1"
                                             data-name="{{$u->displayName()}}"
                                             data-id="{{$u->id}}">
                                            <i class="fas fa-trash-restore"></i></div>
                                        <div data-toggle="modal" data-target="#forceDeleteUser"
                                             class="btn btn-danger mt-1 mr-1"
                                             data-name="{{$u->displayName()}}"
                                             data-id="{{$u->id}}">
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
            let userID = null

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
                    "rowCallback": function( row, data ) {
                        $('td:eq(0)', row).attr('data-title', 'Nachname')
                        $('td:eq(1)', row).attr('data-title', 'Vorname')
                        $('td:eq(2)', row).attr('data-title', 'Klasse')
                        $('td:eq(3)', row).attr('data-title', 'E-Mail')
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

                // One modal for every "edit foodDay" - Set data
                $('#restoreUser').on('show.bs.modal', function (event) {
                    let button = $(event.relatedTarget) // Button that triggered the modal
                    userID = button.data('id')

                    let userName = button.data('name')
                    $('#rsUserName').html(userName)
                })

                $("#btnRestoreUser").click(function (e) {
                    let route = '{{route('users.restore', ':id')}}'.replace(':id', userID)
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
                $('#forceDeleteUser').on('show.bs.modal', function (event) {
                    let button = $(event.relatedTarget) // Button that triggered the modal
                    // Extract info from data-* attributes
                    userID = button.data('id')

                    let userName = button.data('name')
                    $('#fdUserName').html(userName)
                })

                $('#btnForceDeleteUser').on('click', function () {
                    let route = '{{route('users.forceDelete', ':id')}}'.replace(':id', userID)

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
                    })
                })
            })
        })
    </script>
@endsection
