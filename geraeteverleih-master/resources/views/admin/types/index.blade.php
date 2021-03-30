@extends('layouts.app')
@section('body-bottom')
    <script src="{{asset("js/vendor/dropzone.min.js")}}"></script>
    <script src="{{asset("/js/vendor/jquery.dataTables.min.js")}}"></script>
    <script src="{{asset("/js/vendor/dataTables.bootstrap4.js")}}"></script>
@endsection
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Kategorien</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @include('admin.types.editType')
        @include('admin.types.createType')
        @include('admin.types.deleteType')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Kategorien</h4>
                        <div class="mb-md-n4 mr-1 mt-1">
                            <div data-toggle="modal" data-target="#createType"
                                 class="btn btn-success position-relative z-index-2">Erstellen
                            </div>
                        </div>
                        <table id="sort-datatable" class="table table-centered table-striped mt-4 mobile-table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th class="no-sort-symbol"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($types as $type)
                                <tr>
                                    <td>{{$type->name}}</td>
                                    <td>
                                        <div data-toggle="modal" data-target="#editType"
                                             class="btn btn-secondary mt-1 mr-1"
                                             data-id="{{$type->id}}"
                                             data-name="{{$type->name}}"
                                             data-parent_id="{{$type->parent_id}}">
                                            <i class="fas fa-pen"></i></div>
                                        <div data-toggle="modal" data-target="#deleteType"
                                             class="btn btn-danger mt-1 mr-1"
                                             data-id="{{$type->id}}"
                                             data-name="{{$type->name}}">
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
            let typeID = null
            let types = {!! json_encode($types) !!};
            let row = null;

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
                        $('td:eq(0)', row).attr('data-title', 'Name')
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

                // One modal for every "edit Type" - Set data
                $('#createType').on('show.bs.modal', function (event) {
                    buildTypes('cT');
                })

                $("#btnCreateType").click(function (e) {
                    let name = $('#cTname').val()
                    let parentID = $('input:radio[name="cT-type"]:checked').val()

                    if(parentID == 'none')
                        parentID = null;

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('types.store') }}",
                        data: {
                            name: name,
                            parent_id: parentID,
                        },
                        success: function (data) {
                            let type = data.type
                            types = data.types
                            dataTable.row.add([type.name, buildActionButtons(type)]).draw(false);

                            $.toast({
                                heading: 'Erfolgreich',
                                text: data.success,
                                icon: 'success',
                                position: 'top-right',
                                stack: stack,
                                loaderBg: 'rgba(0,0,0,0.2)',
                                hideAfter: 5000   // in milli seconds
                            })

                            //clear modal values
                            $('#cTname').val('')
                            $('#cTParentType').val('')
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

                // One modal for every "edit Type" - Set data
                $('#editType').on('show.bs.modal', function (event) {
                    let button = $(event.relatedTarget) // Button that triggered the modal

                    moment.locale("de")

                    // Extract info from data-* attributes
                    typeID = button.data('id')
                    let name = button.data('name')
                    let parent_id = button.data('parent_id')


                    let prefix = 'eT';
                    buildTypes(prefix);

                    // Update the modal's content for edit.
                    $('#eTname').val(name)
                    if (parent_id)
                        $(`input:radio[name="${prefix}-type"]`).filter(`[value='${parent_id}']`).attr('checked', true);

                    $(`input:radio[name="${prefix}-type"]`).each(function () {
                        $(this).attr("disabled", false);
                    });

                    $(`#${prefix}-${typeID}-sub input:radio`).each(function () {
                        $(this).attr("disabled", true);
                    });

                    $(`#${prefix}-${typeID}`).attr("disabled", true);
                })

                $("#btnEditType").click(function (e) {
                    let name = $('#eTname').val()
                    // let parent_id = $('#eTParentType').val()
                    let parentID = $('input:radio[name="eT-type"]:checked').val()

                    if(parentID == 'none')
                        parentID = null;

                    $.ajax({
                        type: 'PATCH',
                        url: '{{route('types.update', ':id')}}'.replace(':id', typeID),
                        data: {
                            name: name,
                            parent_id: parentID,
                        },
                        success: function (data) {
                            //update content
                            let type = data.type
                            types = data.types
                            dataTable.row(row).data([type.name, buildActionButtons(type)]).draw();

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
                $('#deleteType').on('show.bs.modal', function (event) {
                    let button = $(event.relatedTarget) // Button that triggered the modal
                    // Extract info from data-* attributes
                    typeID = button.data('id')
                    let typeName = button.data('name')
                    $('#dTypeName').html(typeName)
                })

                $('#btnDeleteType').on('click', function () {
                    let route = '{{route('types.destroy', ':id')}}'.replace(':id', typeID)

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
                            types = data.types;
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


                function buildActionButtons(type)
                {
                    return `<div data-toggle="modal" data-target="#editType"
                                             class="btn btn-secondary mt-1 mr-1"
                                             data-id="${type.id}"
                                            data-name="${type.name}"
                                            data-parent_id="${type.parent_id}">
                                <i class="fas fa-pen"></i></div>
                                            <div data-toggle="modal" data-target="#deleteType"
                                             class="btn btn-danger mt-1 mr-1"
                                             data-id="${type.id}"
                                             data-name="${type.name}">
                                            <i class="fas fa-trash"></i></div>`;
                }

                //type building
                let html = "";
                function buildTypes(prefix) {
                    html = `<div class="custom-control custom-radio mb-2">
                <input type="radio" class="custom-control-input"
            id="${prefix}-none"
            name="${prefix}-type" value="none">
                <label class="custom-control-label"
            for="${prefix}-none">neue Überkategorie</label>
                </div>`;

                    for (let type of types.filter(t => t.parent_id === null))
                        buildSubTypes(type, prefix);

                    $(`#${prefix}-types`).html(html);
                }

                function buildSubTypes(type, prefix) {
                    html += `<div class="custom-control custom-radio mb-2">
                <input type="radio" class="custom-control-input"
            id="${prefix}-${type.id}"
            name="${prefix}-type" value="${type.id}">
                <label class="custom-control-label"
            for="${prefix}-${type.id}">${type.name}</label>
                </div>`;

                    html += `<div id="${prefix}-${type.id}-sub" class="ml-4">`;

                    for (let child of types.filter(t => t.parent_id == type.id)) {
                        if (types.filter(t => t.parent_id == child.id).length === 0) {
                            html += `<div class="custom-control custom-radio mb-2">
                <input type="radio" class="custom-control-input"
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
            })
        })
    </script>

@endsection
