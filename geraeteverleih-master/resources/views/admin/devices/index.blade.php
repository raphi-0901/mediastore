@extends('layouts.app')
@section('body-bottom')
    <script src="{{asset("js/vendor/dropzone.min.js")}}"></script>
    <script src="{{asset("/js/ui/component.fileupload.js")}}"></script>
    <script src="{{asset("/js/vendor/jquery.dataTables.min.js")}}"></script>
    <script src="{{asset("/js/vendor/dataTables.bootstrap4.js")}}"></script>
@endsection
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Geräte</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @include('admin.devices.editDevice')
        @include('admin.devices.createDevice')
        @include('admin.devices.deleteDevice')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Geräteliste hochladen</h4>
                        <form action="{{route("devices.import")}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input id="devicesUpload" name="devices[]" placeholder="Files"
                                   type="file" required="required" multiple class="file-upload"
                                   accept=".xlsx">
                            <label for="devicesUpload" class="file-upload-label">Datei auswählen</label>
                            <button name="submit" type="submit" class="btn btn-success">Importieren
                            </button>
                        </form>
                        @if (session('failures'))
                            <ul>
                                @foreach(session('failures') as $failure)
                                    <li>{!! 'Zeile ' . $failure->row() . ": " . $failure->errors()[0] !!}</li>
                                @endforeach</ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Geräte</h4>
                        <div class="mb-md-n4">
                            <div data-toggle="modal" data-target="#createDevice"
                                 class="btn btn-success position-relative z-index-2 mt-1 mr-1">Erstellen
                            </div>
                            <a href="{{route("devices.deletedDevices")}}" id="deletedUsers"
                               class="btn btn-secondary position-relative z-index-2 mt-1 mr-1">Gelöschte Geräte</a>
                            <a href="{{route("devices.downloadQRCodes")}}"
                               class="btn btn-primary position-relative z-index-2 mt-1 mr-1"><i class="fa fa-download"></i> QR-Codes downloaden</a>
                        </div>
                        <table id="sort-datatable" class="table table-centered table-striped mt-4 mobile-table">
                            <thead>
                            <tr>
                                <th>QR-ID</th>
                                <th>Name</th>
                                <th>Kategorie</th>
                                <th>Status</th>
                                <th class="no-sort-symbol"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($devices as $device)
                                <tr>
                                    <td>{{$device->qr_id}}</td>
                                    <td>{{$device->name}}</td>
                                    <td>{{$device->type->name}}</td>
                                    <td>{!!  $device->isAvailable() ? '<span class="badge badge-success text-white">Verfügbar</span>' : '<span class="badge badge-warning text-white">Nicht verfügbar</span>'!!}</td>
                                    <td>
                                        <a href="{{route('devices.show', $device->id)}}"
                                           class="btn btn-secondary mt-1 mr-1">
                                            <i class="fas fa-eye"></i></a>
                                        <div data-toggle="modal" data-target="#editDevice"
                                             class="btn btn-secondary mt-1 mr-1"
                                             data-id="{{$device->id}}"
                                             data-name="{{$device->name}}"
                                             data-serial="{{$device->serial}}"
                                             data-description="{{$device->description}}"
                                             data-qr="{{$device->qr_id}}"
                                             data-note="{{$device->note}}"
                                             data-type="{{$device->type_id}}">
                                            <i class="fas fa-pen"></i></div>
                                        <div data-toggle="modal" data-target="#createDevice"
                                             class="btn btn-secondary mt-1 mr-1"
                                             data-id="{{$device->id}}"
                                             data-name="{{$device->name}}"
                                             data-serial="{{$device->serial}}"
                                             data-description="{{$device->description}}"
                                             data-qr="{{$device->qr_id}}"
                                             data-note="{{$device->note}}"
                                             data-type="{{$device->type_id}}">
                                            <i class="far fa-clone"></i></div>
                                        <div data-toggle="modal" data-target="#deleteDevice"
                                             class="btn btn-danger mt-1 mr-1"
                                             data-id="{{$device->id}}"
                                             data-name="{{$device->name}}">
                                            <i class="fas fa-trash"></i></div>
                                        <div
                                            class="d-none">{{$device->id}}</div>
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
            let deviceID = null
            let row = null
            let types = {!! json_encode($types) !!};
            let allTypes = {!! json_encode($allTypes) !!};

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
                       $('td:eq(0)', row).attr('data-title', 'QR-ID')
                       $('td:eq(1)', row).attr('data-title', 'Name')
                       $('td:eq(2)', row).attr('data-title', 'Kategorie')
                       $('td:eq(3)', row).attr('data-title', 'Status')
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

                // One modal for every "edit Device" - Set data
                $('#createDevice').on('show.bs.modal', function (event) {
                    let button = $(event.relatedTarget) // Button that triggered the modal

                    moment.locale("de")

                    // Extract info from data-* attributes
                    deviceID = button.data('id')
                    let name = button.data('name')
                    let description = button.data('description')
                    let serial = button.data('serial')
                    let qrID = button.data('qr')
                    let note = button.data('note')
                    let typeID = button.data('type')

                    //wenn deviceID leer ist, dann ist der Button der erstellen button und nicht der duplicate button
                    if(deviceID)
                    {
                        // Update the modal's content for edit.
                        $('#cDname').val(name)
                        $('#cDserial').val(serial)
                        $(`input:radio[name=cD-type][value=${typeID}]`).click();
                        $('#cDdescription').val(description)
                        $('#cDqr').val(qrID)
                        $('#cdNote').val(note)
                    }
                })


                $("#btnCreateDevice").click(function (e) {
                    let name = $('#cDname').val()
                    let serial = $('#cDserial').val()
                    let description = $('#cDdescription').val()
                    let qrID = $('#cDqr').val()
                    let note = $('#cDnote').val()
                    let typeID = $(`input:radio[name=cD-type]:checked`).val()
                    let count = $('#cCount').val() == '' ? 1 : $('#cCount').val();

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('devices.store') }}",
                        data: {
                            name: name,
                            serial: serial,
                            description: description,
                            qr_id: qrID,
                            note: note,
                            type_id: typeID,
                            count: count,
                        },
                        success: function (data) {
                            let devices = data.devices
                            let type = data.type
                            devices.forEach(function (device) {
                                dataTable.row.add([device.qr_id, device.name, type.name, '<span class="badge badge-success text-white">Verfügbar</span>', buildActionButtons(device, type)]);
                            })
                            dataTable.draw(false);

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
                            $('#cDname').val('')
                            $('#cDserial').val('')
                            $('#cDdescription').val('')
                            $('#cDtype').val('')
                            $('#cCount').val('')
                            $('#cDqr').val('')
                            $('#cDnote').val('')
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

                // One modal for every "edit Device" - Set data
                $('#editDevice').on('show.bs.modal', function (event) {
                    let button = $(event.relatedTarget) // Button that triggered the modal

                    moment.locale("de")

                    // Extract info from data-* attributes
                    deviceID = button.data('id')
                    let name = button.data('name')
                    let description = button.data('description')
                    let serial = button.data('serial')
                    let qrID = button.data('qr')
                    let note = button.data('note')
                    let typeID = button.data('type')

                    // Update the modal's content for edit.
                    $('#eDname').val(name)
                    $('#eDserial').val(serial)
                    $(`input:radio[name=eD-type][value=${typeID}]`).click();
                    $('#eDdescription').val(description)
                    $('#eDqr').val(qrID)
                    $('#eDnote').val(note)
                })

                $("#btnEditDevice").click(function (e) {
                    let name = $('#eDname').val()
                    let serial = $('#eDserial').val()
                    let qrID = $('#eDqr').val()
                    let note = $('#eDnote').val()
                    let typeID = $(`input:radio[name=eD-type]:checked`).val()
                    let description = $('#eDdescription').val()

                    $.ajax({
                        type: 'PATCH',
                        url: '{{route('devices.update', ':id')}}'.replace(':id', deviceID),
                        data: {
                            id: deviceID,
                            name: name,
                            serial: serial,
                            description: description,
                            qr_id: qrID,
                            note: note,
                            type_id: typeID,
                        },
                        success: function (data) {
                            $.toast({
                                heading: "Erfolgreich",
                                text: data.success,
                                icon: 'success',
                                position: 'top-right',
                                stack: stack,
                                loaderBg: 'rgba(0,0,0,0.2)',
                                hideAfter: 5000   // in milli seconds
                            })
                            let device = data.device
                            let type = data.type
                            dataTable.row(row).data([device.qr_id, device.name, type.name, device.status, buildActionButtons(device, type)]).draw();
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
                $('#deleteDevice').on('show.bs.modal', function (event) {
                    let button = $(event.relatedTarget) // Button that triggered the modal
                    // Extract info from data-* attributes
                    deviceID = button.data('id')
                    let deviceName = button.data('name')

                    $('#dDeviceName').html(deviceName)
                })

                $('#btnDeleteDevice').on('click', function () {
                    let route = '{{route('devices.destroy', ':id')}}'.replace(':id', deviceID)

                    $.ajax({
                        type: 'DELETE',
                        url: route,
                        success: function (data) {
                            dataTable.row(row).remove().draw();
                            $('#deletedDevice').show()

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

                // File-Upload Design changes
                $('.file-upload').each(function () {
                    let $input = $(this),
                        $label = $input.next('.file-upload-label'),
                        labelVal = $label.html();

                    $input.on('change', function (element) {
                        var fileName = '';
                        if (element.target.value) fileName = element.target.value.split('\\').pop();
                        fileName ? $label.addClass('has-file').html(fileName) : $label.removeClass('has-file').html(labelVal);
                    });
                });


                function buildActionButtons(device, type) {
                    let route = '{{route('devices.show', ':id')}}'.replace(':id', device.id)
                    return `<a href="${route}"
                                           class="btn btn-secondary mt-1 mr-1">
                                            <i class="fas fa-eye"></i></a>
                                        <div data-toggle="modal" data-target="#editDevice"
                                             class="btn btn-secondary mt-1 mr-1"
                                             data-id="${device.id}"
                                             data-name="${device.name}"
                                             data-serial="${device.serial}"
                                             data-description="${device.description}"
                                             data-qr="${device.qr_id}"
                                             data-note="${device.note}"
                                             data-type="${type.id}">
                                            <i class="fas fa-pen"></i></div>
                                        <div data-toggle="modal" data-target="#createDevice"
                                             class="btn btn-secondary mt-1 mr-1"
                                             data-id="${device.id}"
                                             data-name="${device.name}"
                                             data-serial="${device.serial}"
                                             data-description="${device.description}"
                                             data-qr="${device.qr_id}"
                                             data-note="${device.note}"
                                             data-type="${type.id}">
                                           <i class="far fa-clone"></i></div>
                                        <div data-toggle="modal" data-target="#deleteDevice"
                                             class="btn btn-danger mt-1 mr-1"
                                             data-id="${device.id}"
                                             data-name="${device.name}">
                                            <i class="fas fa-trash"></i></div>`;
                }

                //type building
                let html = "";
                function buildTypes(prefix) {
                    html = "";

                    for (let type of allTypes.filter(t => t.parent_id === null))
                        buildSubTypes(type, prefix);

                    $(`#${prefix}-types`).html(html);

                    $(`input:radio[name="${prefix}-type"]`).each(function () {
                        if(!types.find(t => t.id == $(this).val() || t.parent_id == $(this).val()))
                            $(this).parent().hide()
                    });

                    $(`input:radio[name="${prefix}-type"]`).each(function () {
                        if(!types.find(t => t.id == $(this).val()))
                            $(this).attr('disabled', 'disabled')
                    });
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

                    for (let child of allTypes.filter(t => t.parent_id == type.id)) {
                        if (allTypes.filter(t => t.parent_id == child.id).length === 0) {
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

                buildTypes('cD')
                buildTypes('eD')
            })
        })
    </script>
@endsection
