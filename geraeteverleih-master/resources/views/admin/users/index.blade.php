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
                    <h4 class="page-title">Benutzer</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        @include('admin.users.deleteUser')
        @include('admin.users.editUser')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Lehrer</h4>
                        <div class="mb-md-n4">
                            <a href="{{route("users.deletedUsers")}}" id="deletedUsers"
                               class="btn btn-secondary position-relative z-index-2">Gelöschte Benutzer</a>
                        </div>

                        <table id="sort-datatable-teachers"
                               class="table table-striped table-centered mt-4 mobile-table dt-responsive">
                            <thead>
                            <tr>
                                <th>Nachname</th>
                                <th>Vorname</th>
                                <th>E-Mail</th>
                                <th class="no-sort-symbol"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($teachers as $u)
                                <tr id="user-{{$u->id}}">
                                    <td>{{ ($u->lastName)}}</td>
                                    <td>{{ ($u->firstName)}}</td>
                                    <td><a href="mailto:{{$u->email}}">{{$u->email}}</a></td>
                                    <td data-title="" data-sort="">
                                        <a href="{{route('users.show', $u->id)}}" class="btn btn-secondary  mt-1 mr-1">
                                            <i class="fas fa-eye"></i></a>
                                        <div data-toggle="modal" data-target="#editUser"
                                             class="btn btn-secondary mt-1 mr-1"
                                             data-id="{{$u->id}}"
                                             data-firstName="{{$u->firstName}}"
                                             data-lastName="{{$u->lastName}}"
                                             data-isTeacher="{{$u->isTeacher() ? '1' : '0'}}"
                                             data-class="{{$u->class}}"
                                             data-types="@foreach($u->types as $type){{$type->id}};@endforeach">
                                            <i class="fas fa-pen"></i></div>
                                        <div data-toggle="modal" data-target="#deleteUser"
                                             class="btn btn-danger mt-1 mr-1"
                                             data-id="{{$u->id}}"
                                             data-name="{{$u->displayName()}}">
                                            <i class="fas fa-trash"></i></div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Schüler</h4>

                        <table id="sort-datatable-students"
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
                            @foreach($students as $u)
                                <tr>
                                    <td>{{ ($u->lastName)}}</td>
                                    <td>{{ ($u->firstName)}}</td>
                                    <td>{{$u->class}}</td>
                                    <td><a href="mailto:{{$u->email}}">{{$u->email}}</a></td>
                                    <td data-title="" data-sort="">
                                        <a href="{{route('users.show', $u->id)}}" class="btn btn-secondary  mt-1 mr-1">
                                            <i class="fas fa-eye"></i></a>
                                        <div data-toggle="modal" data-target="#deleteUser"
                                             class="btn btn-danger mt-1 mr-1"
                                             data-id="{{$u->id}}"
                                             data-name="{{$u->displayName()}}">
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
            let userID = null
            let row = null
            let types = {!! json_encode($types) !!};

            $(document).ready(function () {
                let dataTableTeachers = $('#sort-datatable-teachers').DataTable({
                    keys: !0,
                    paging: false,
                    info: false,
                    language: {
                        "zeroRecords": "Keine passenden Einträge gefunden",
                        "infoEmpty": "Keine Einträge vorhanden",
                        "search": "Suchen"
                    },
                    "rowCallback": function (row, data) {
                        $('td:eq(0)', row).attr('data-title', 'Nachname')
                        $('td:eq(1)', row).attr('data-title', 'Vorname')
                        $('td:eq(2)', row).attr('data-title', 'E-Mail')
                    }
                })

                let dataTableStudents = $('#sort-datatable-students').DataTable({
                    keys: !0,
                    paging: false,
                    info: false,
                    language: {
                        "zeroRecords": "Keine passenden Einträge gefunden",
                        "infoEmpty": "Keine Einträge vorhanden",
                        "search": "Suchen"
                    },
                    "rowCallback": function (row, data) {
                        $('td:eq(0)', row).attr('data-title', 'Nachname')
                        $('td:eq(1)', row).attr('data-title', 'Vorname')
                        $('td:eq(2)', row).attr('data-title', 'Klasse')
                        $('td:eq(3)', row).attr('data-title', 'E-Mail')
                    }
                })

                $('#sort-datatable-teachers tbody').on('click', 'tr', function () {
                    row = this;
                });

                $('#sort-datatable-students tbody').on('click', 'tr', function () {
                    row = this;
                });

                //ajax
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })

                $(document).on('click', 'input:checkbox', function (e) {
                    let id = $(this).val();
                    if ($(this).prop('checked'))
                        $(`#eU-${id}-sub`).find(':checkbox').each(function () {
                            $(this).attr("disabled", true);
                        });
                    else
                        $(`#eU-${id}-sub`).find(':checkbox').each(function () {
                            let parentCheckbox = $(this).parent().parent().prev().children('input:checkbox')
                            //wenn checked, bleibt es disabled
                            if (!parentCheckbox.prop('checked'))
                                $(this).attr("disabled", false);
                        });
                })

                // One modal for every "edit Device" - Set data
                $('#editUser').on('show.bs.modal', function (event) {
                    let button = $(event.relatedTarget) // Button that triggered the modal
                    moment.locale("de")

                    // Extract info from data-* attributes
                    userID = button.data('id')
                    let firstName = button.data('firstname')
                    let lastName = button.data('lastname')
                    let uClass = button.data('class')
                    let isTeacher = button.data('isteacher')
                    let types = button.data('types').split(';')

                    $('#eUfirstName').val(firstName)
                    $('#eUlastName').val(lastName)
                    $('#eUclass').val(uClass)

                    if (isTeacher) {
                        //unselect all
                        $("input:checkbox").prop('checked', false)
                        $("input:checkbox").prop('disabled', false)

                        $('#typeSelection').show()
                        //select new
                        types.forEach(function (type) {
                            $(`#eU-${type}`).prop('checked', true)
                            $(`#eU-${type}-sub`).find(':checkbox').each(function () {
                                $(this).attr("disabled", true);
                            });
                        })
                    } else
                        $('#typeSelection').hide()
                })

                $("#btnEditUser").click(function (e) {
                    let types = [];
                    //only get all checked enabled checkboxes
                    $("input:checkbox:enabled:checked").each(function () {
                        types.push($(this).val());
                    });

                    $.ajax({
                        type: 'PATCH',
                        url: '{{route('users.update', ':id')}}'.replace(':id', userID),
                        data: {
                            types: types,
                        },
                        success: function (data) {
                            let user = data.user
                            let dataModal = $("#user-" + user.id + ' > td div').eq(0)
                            let nTypes = ''
                            types.forEach(type => nTypes += type + ";")
                            dataModal.data('types', nTypes)

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
                $('#deleteUser').on('show.bs.modal', function (event) {
                    let button = $(event.relatedTarget) // Button that triggered the modal
                    // Extract info from data-* attributes
                    userID = button.data('id')

                    let userName = button.data('name')

                    $('#dUserName').html(userName)
                })

                $('#btnDeleteUser').on('click', function () {
                    let route = '{{route('users.destroy', ':id')}}'.replace(':id', userID)

                    $.ajax({
                        type: 'DELETE',
                        url: route,
                        success: function (data) {
                            dataTableStudents.row(row).remove().draw();
                            dataTableTeachers.row(row).remove().draw();
                            $('#deletedUsers').show()

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

                //type building
                let html = "";

                function buildTypes(prefix) {
                    html = "";

                    for (let type of types.filter(t => t.parent_id === null))
                        buildSubTypes(type, prefix);

                    $(`#${prefix}-types`).html(html);
                }

                function buildSubTypes(type, prefix) {
                    html += `<div class="custom-control custom-checkbox mb-2">
                <input type="checkbox" class="custom-control-input"
            id="${prefix}-${type.id}"
            name="${prefix}-type" value="${type.id}">
                <label class="custom-control-label"
            for="${prefix}-${type.id}">${type.name}</label>
                </div>`;

                    html += `<div id="${prefix}-${type.id}-sub" class="ml-4">`;

                    for (let child of types.filter(t => t.parent_id == type.id)) {
                        if (types.filter(t => t.parent_id == child.id).length === 0) {
                            html += `<div class="custom-control custom-checkbox mb-2">
                <input type="checkbox" class="custom-control-input"
            id="${prefix}-${child.id}"
            name="${prefix}-type" value="${child.id}">
                <label class="custom-control-label"
            for="${prefix}-${child.id}">${child.name}</label>
                </div>`;
                        } else
                            buildSubTypes(child, prefix);
                    }
                    html += `</div>`;
                }

                buildTypes('eU')
            })
        })
    </script>
@endsection
