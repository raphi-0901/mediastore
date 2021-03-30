@include('orders.deviceInfo')
@include('orders.showQRCode')
<h4>Bestellung von {{$order->user->displayName()}}, {{$order->user->class}}</h4>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Geräte - Bestellung #{{$order->id}}</h4>
                <table id="sort-datatable" class="table table-centered table-striped mt-4 mobile-table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th class="no-sort-symbol"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(Auth::user()->isStudent() || Auth::user()->isTeacher() && !Auth::user()->belongingOrders()->pluck('id')->contains($order->id))
                        @foreach($order->devices as $device)
                            <tr id="device-{{$device->id}}">
                                <td>{{$device->name}}</td>
                                <td data-sort="">
                                    <a href="{{route('devices.show', $device->id)}}"
                                       class="btn btn-secondary mt-1 mr-1">
                                        <i class="fas fa-eye"></i></a>
                                    <div data-toggle="modal" data-target="#deviceQRCode"
                                         class="btn btn-secondary mt-1 mr-1"
                                         data-id="{{$device->id}}">
                                        <i class="fas fa-qrcode"></i></div>
                                    <div data-toggle="modal" data-target="#deviceInfo"
                                         class="btn btn-secondary mt-1 mr-1"
                                         @if($device->pivot->out_scan === null)style="display: none" @endif
                                         data-id="{{$device->id}}">
                                        <i class="fas fa-info"></i></div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    @if(Auth::user()->isAdmin() || Auth::user()->isTeacher() && Auth::user()->belongingOrders()->pluck('id')->contains($order->id))
                        @foreach($order->devices as $device)
                            <tr id="device-{{$device->id}}">
                                <td>{{$device->name}}</td>
                                <td data-sort="">
                                    <a href="{{route('devices.show', $device->id)}}"
                                       class="btn btn-secondary mt-1 mr-1">
                                        <i class="fas fa-eye"></i></a>

                                    <div data-toggle="modal" data-target="#deviceInfo"
                                         class="btn btn-secondary mt-1 mr-1"
                                         @if($device->pivot->out_scan === null)style="display: none" @endif
                                         data-id="{{$device->id}}">
                                        <i class="fas fa-info"></i></div>

                                    <div data-toggle="modal" data-target="#undoScan"
                                         class="btn btn-primary mt-1 mr-1"
                                         data-id="{{$device->id}}">
                                        <i class="fas fa-redo"></i></div>

                                    @if($order->picked_at === null && $order->answer !== false)
                                        <div data-toggle="modal" data-target="#deleteDevice"
                                             class="btn btn-danger mt-1 mr-1"
                                             data-name="{{$device->name}}"
                                             data-id="{{$device->id}}">
                                            <i class="fas fa-trash"></i></div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div> <!-- end col -->
    <div class="col-lg-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Details</h4>
                        <p class="my-0">{{$order->user->displayName()}}, {{$order->user->class}}</p>
                        <p class="my-0">{{$order->from->isoFormat("Do MMM. YYYY")}}
                            bis {{$order->to->isoFormat("Do MMM. YYYY")}}</p>
                        @isset($order->answer)<span
                            class="badge @if($order->answer) badge-success @else  badge-danger @endif text-white">{{$order->answer ? 'Bestätigt' : 'Abgelehnt'}} von {{$order->answeredBy->displayName()}}</span>
                        @if($order->answer === false)
                            <p class="my-0"><b>Grund:</b> {{$order->comment }}</p>
                        @endif
                        @endif
                    </div>
                </div>
            </div> <!-- end col -->
            @if($order->answer !== false)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Ausgabe</h4>
                            <p class="my-0">{{$order->givenBy ? $order->givenBy->displayName() : ''}}</p>
                            <p class="my-0">{{$order->picked_at ? $order->picked_at->isoFormat("Do MMM. YYYY") . ' um ' . $order->picked_at->format("H:i") : ''}}</p>
                        </div>
                    </div>
                </div> <!-- end col -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Zurücknahme</h4>
                            <p class="my-0">{{$order->returnedBy ? $order->returnedBy->displayName() : ''}}</p>
                            <p class="my-0">{{$order->returned_at ? $order->returned_at->isoFormat("Do MMM. YYYY") . ' um ' . $order->returned_at->format("H:i") : ''}}</p>
                        </div>
                    </div>
                </div> <!-- end col -->
            @endif
        </div>
    </div>
</div>

<script type="text/javascript">
    let dataTable;

    $(document).ready(function () {
        let i_Devices = {!! json_encode($order->devices) !!};
        let i_Order = {!! json_encode($order) !!};
        let i_IsOutScan = {!! json_encode($order) !!};

        let qrCodes = {!! json_encode($qrCodes->toArray()) !!};

        dataTable = $('#sort-datatable').DataTable({
            keys: !0,
            paging: false,
            info: false,
            language: {
                "zeroRecords": "Keine passenden Einträge gefunden",
                "infoEmpty": "Keine Einträge vorhanden",
                "search": "Suchen"
            },
            "rowCallback": function (row, data) {
                $('td:eq(0)', row).attr('data-title', 'Name')
            }
        })

        // One modal for every "edit user" - Set data
        $('#deviceQRCode').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            let id = button.data('id')

            let qrCode = qrCodes.find(qr => qr[0] == id);
            $('#qr_placeholder').attr("src", "data:image/svg+xml;base64," + qrCode[1]);
            $('#qrName').text(i_Devices.find(d => d.id == id).name);
        })

        if (!i_Order.picked_at)
            i_IsOutScan = true

        // One modal for every "edit user" - Set data
        $('#deviceInfo').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget) // Button that triggered the modal
            // Extract info from data-* attributes
            let infoDevice = null;

            if (typeof devices != "undefined")
                infoDevice = devices.find(d => d.id == button.data('id'));
            else
                infoDevice = i_Devices.find(d => d.id == button.data('id'));

            $('#iName').text(infoDevice.name)
            $('#iNote-before').val("")
            $('#iNote-after').val("")

            if (infoDevice.pivot.out_scan) {
                $('#out_scan').text(moment(infoDevice.pivot.out_scan).format('DD. MMM YYYY') + " um " + moment(infoDevice.pivot.out_scan).format('HH:mm'))
                $('#iNote-before').val(infoDevice.pivot.note_before)
                $('#out_scan_container').show()
            } else
                $('#out_scan_container').hide()

            if (infoDevice.pivot.back_scan) {
                $('#back_scan').text(moment(infoDevice.pivot.back_scan).format('DD. MMM YYYY') + " um " + moment(infoDevice.pivot.back_scan).format('HH:mm'))
                $('#iNote-after').val(infoDevice.pivot.note_after)
                $('#back_scan_container').show()
            } else
                $('#back_scan_container').hide()

        })

        /*for (let device of i_Devices) {
            if (i_IsOutScan) {
                if (device.pivot.out_scan)
                    $(`#device-${device.id} > td:not(:last-child)`).css('text-decoration', 'line-through');
            } else {
                if (device.pivot.back_scan)
                    $(`#device-${device.id} > td:not(:last-child)`).css('text-decoration', 'line-through');
            }
        }*/
    })
</script>

