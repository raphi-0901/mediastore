@extends('layouts.app')
@section('body-bottom')
    <script src="{{asset("/js/vendor/jquery.dataTables.min.js")}}"></script>
    <script src="{{asset("/js/vendor/dataTables.bootstrap4.js")}}"></script>
@endsection
@section('content')
    @include('teacher.orders.qrcodefound')
    @include('teacher.orders.deleteDevice')
    @include('teacher.orders.undoScan')
    @include('teacher.orders.denyOrder')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        {{--<div class="form-group mt-2">
                            <input type="text" class="form-control date" id="ordersDateRange"
                                   data-toggle="date-picker"
                                   data-cancel-class="btn-warning">
                        </div>--}}

                        @include('orders.information')
                        {{--only show qr code scanner when order is accepted--}}
                        @if($order->answer)
                            @if($order->picked_at === null || $order->returned_at === null)
                                <div class="col-12 col-md-8"></div>
                                <div class="d-flex justify-content-center mb-3">
                                    <div id="qrWidth">
                                        <div id="qr-reader" class="w-100"></div>
                                        <div id="qr-reader-results"></div>
                                    </div>
                                </div>
                            @endif
                        @endif
                        <div class="d-flex justify-content-around align-items-center">
                            @if($order->answer === null)
                                <a href="{{route('orders.accept', $order->id)}}"
                                   class="btn btn-success btn-block mx-2 my-0 align-self-stretch"><i
                                        class="fas fa-check"></i> Bestätigen</a>
                                <div data-toggle="modal" data-target="#denyOrder"
                                     class="btn btn-danger btn-block mx-2 my-0 align-self-stretch"
                                     data-id="{{$order->id}}"><i
                                        class="fas fa-times"></i> Ablehnen
                                </div>
                            @endif

                            @if($order->answer == true)
                                @if($order->picked_at === null)
                                    <a href="{{route('orders.pick', $order->id)}}" id="out"
                                       class="btn btn-success btn-block disabled mx-2 my-0 align-self-stretch"><i
                                            class="fas fa-check"></i> Ausgabe bestätigen</a>
                                @elseif($order->returned_at === null)
                                    <a href="{{route('orders.return', $order->id)}}" id="back"
                                       class="btn btn-success btn-block disabled mx-2 my-0 align-self-stretch"><i
                                            class="fas fa-check"></i> Zurücknahme bestätigen</a>
                                @endif
                            @endif

                           {{-- @if($order->answer === false)
                                <a href="{{route('orders.accept', $order->id)}}"
                                   class="btn btn-success btn-block mx-2 my-0 align-self-stretch"><i
                                        class="fas fa-check"></i> Bestätigen</a>
                            @endif--}}

                            @if($order->answer == true && $order->picked_at === null)
                                <div data-toggle="modal" data-target="#denyOrder"
                                     class="btn btn-danger btn-block mx-2 my-0 align-self-stretch"
                                     data-id="{{$order->id}}"><i
                                        class="fas fa-times"></i> Ablehnen
                                </div>
                            @endif
                            <a href="{{route('orders.index')}}"
                               class="btn btn-secondary btn-block mx-2 my-0 align-self-stretch"><i
                                    class="fas fa-arrow-left"></i> Zurück</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/qr-code.js') }}" defer></script>
    <script>
        let order = {!! json_encode($order) !!};
        let devices = {!! json_encode($order->devices) !!};

        //disable all buttons in advance
        for (let device of devices) {
            $(`#device-${device.id} > td:last-child`).children().eq(1).hide()
            $(`#device-${device.id} > td:last-child`).children().eq(2).hide()
            $(`#device-${device.id} > td:last-child`).children().eq(3).hide()
        }

        let foundDevice = null;
        let deviceID = null;
        let row = null;
        let isOutScan = false;

        $(document).ready(function () {
            //ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            if (!order.picked_at)
                isOutScan = true

            if (order.returned_at)
                isOutScan = null

            $('#sort-datatable tbody').on('click', 'tr', function () {
                row = this;
            });

            $("#btnAcceptQRCode").click(function (e) {
                let note_before = $('#note-before').val()
                let note_after = $('#note-after').val()

                $.ajax({
                    type: 'POST',
                    url: '{{route('orders.handleQRCodeScan')}}',
                    data: {
                        order_id: order.id,
                        device_id: foundDevice.id,
                        note_before: note_before,
                        note_after: note_after,
                    },
                    success: function (data) {
                        devices = data.devices;
                        handleActionButtons();
                        toggleButton()

                        $.toast({
                            heading: 'Erfolgreich',
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
            $('#deleteDevice').on('show.bs.modal', function (event) {
                let button = $(event.relatedTarget) // Button that triggered the modal
                // Extract info from data-* attributes
                deviceID = button.data('id')
                let deviceName = button.data('name')
                $('#rmDeviceName').html(deviceName)

                if (devices.length === 1)
                    $('#deleteDeviceModalInfo').show();
                else
                    $('#deleteDeviceModalInfo').hide();
            })

            $('#btnDeleteDevice').on('click', function () {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('orders.removeDevice') }}",
                    data: {
                        order_id: order.id,
                        device_id: deviceID,
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
                        devices = data.devices;
                        //leave site when no device is there anymore
                        if (devices.length == 0)
                            location.href = "{{route('orders.index')}}"

                        dataTable.row(row).remove().draw();
                        toggleButton()
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


            $('#undoScan').on('show.bs.modal', function (event) {
                let button = $(event.relatedTarget) // Button that triggered the modal
                // Extract info from data-* attributes
                deviceID = button.data('id')
            })

            $('#btnUndoScan').on('click', function () {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('orders.undoScan') }}",
                    data: {
                        order_id: order.id,
                        device_id: deviceID,
                        isOutScan: isOutScan,
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
                        devices = data.devices;
                        handleActionButtons()
                        toggleButton()
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

            function handleActionButtons() {
                for (let device of devices) {
                    if (isOutScan === true) {
                        //action button order:
                        //0:    show
                        //1:    info
                        //2:    undo
                        //3:    delete

                        if (device.pivot.out_scan) {
                            //$(`#device-${device.id} > td:not(:last-child)`).css('text-decoration', 'line-through');
                            $(`#device-${device.id} > td:last-child`).children().eq(1).show()
                            $(`#device-${device.id} > td:last-child`).children().eq(2).show()
                            $(`#device-${device.id} > td:last-child`).children().eq(3).show()
                        } else {
                            //$(`#device-${device.id} > td:not(:last-child)`).css('text-decoration', '');
                            $(`#device-${device.id} > td:last-child`).children().eq(1).hide()
                            $(`#device-${device.id} > td:last-child`).children().eq(2).hide()
                            $(`#device-${device.id} > td:last-child`).children().eq(3).show()
                        }
                    } else if (isOutScan === false) {
                        if (device.pivot.back_scan) {
                            //$(`#device-${device.id} > td:not(:last-child)`).css('text-decoration', 'line-through');
                            $(`#device-${device.id} > td:last-child`).children().eq(1).show()
                            $(`#device-${device.id} > td:last-child`).children().eq(2).show()
                            $(`#device-${device.id} > td:last-child`).children().eq(3).hide()
                        } else {
                            //$(`#device-${device.id} > td:not(:last-child)`).css('text-decoration', '');
                            $(`#device-${device.id} > td:last-child`).children().eq(1).show()
                            $(`#device-${device.id} > td:last-child`).children().eq(2).hide()
                            $(`#device-${device.id} > td:last-child`).children().eq(3).hide()
                        }
                    } else if (isOutScan === null) {
                        //$(`#device-${device.id} > td:not(:last-child)`).css('text-decoration', '');
                        $(`#device-${device.id} > td:last-child`).children().eq(1).show()
                        $(`#device-${device.id} > td:last-child`).children().eq(2).hide()
                        $(`#device-${device.id} > td:last-child`).children().eq(3).hide()
                    }
                }
            }

            function toggleButton() {
                //if all device
                if (!devices.find(device => device.pivot.out_scan == null))
                    $("#out").removeClass('disabled')

                if (!devices.find(device => device.pivot.back_scan == null))
                    $("#back").removeClass('disabled')
            }

            toggleButton();
            handleActionButtons();

            if ($('#qr-reader').length) {
                function docReady(fn) {
                    // see if DOM is already available
                    if (document.readyState === "complete" || document.readyState === "interactive") {
                        // call on next available tick
                        setTimeout(fn, 1);
                    } else {
                        document.addEventListener("DOMContentLoaded", fn);
                    }
                }

                docReady(function () {
                    var resultContainer = document.getElementById('qr-reader-results');
                    //var lastResult, countResults = 0;

                    //set qr code width
                    let qrbox = null
                    if (screen.width > 768) {
                        $("#qrWidth").addClass('w-50')
                        qrbox = $("#qrWidth").width() / 2
                    } else {
                        qrbox = 175
                        $("#qrWidth").addClass('w-100')
                    }
                    var html5QrcodeScanner = new Html5QrcodeScanner(
                        "qr-reader", {
                            fps: 10,
                            qrbox: qrbox
                        });

                    function onScanSuccess(qrCodeMessage) {
                        //foundDevice = devices.find(d => d.qr_id == qrCodeMessage);

                        let foundDevices = devices.filter(function (device) {
                            if (device.qr_id != qrCodeMessage)
                                return false;

                            if (!device.out_scan)
                                return true

                            if (!device.back_scan)
                                return true

                            return false;
                        });

                        if (isOutScan)
                            foundDevice = foundDevices.find(d => !d.pivot.out_scan)
                        else
                            foundDevice = foundDevices.find(d => !d.pivot.back_scan)

                        if (foundDevice) {
                            $('#qrcodefound').modal('show')
                            $('#note-after').val(foundDevice.note)
                            $('#note-before').val(foundDevice.note)

                            $('#dName').text(foundDevice.name)
                            if (!foundDevice.pivot.out_scan)
                                $('#note-after').parent().hide()
                            else {
                                $('#note-before').val(foundDevice.pivot.note_before)
                                $('#note-before').prop('disabled', 'disabled')
                            }
                        } else {
                            $.toast({
                                heading: "Fehler",
                                text: 'QR-Code ist mit keinem Gerät in dieser Bestellung verbunden!',
                                icon: 'error',
                                position: 'top-right',
                                stack: 1,
                                preventDuplicates: true,
                                loaderBg: 'rgba(0,0,0,0.2)',
                                hideAfter: 5000   // in milli seconds
                            })
                            //setTimeout(console.log('timing'), 1000)
                        }
                        // Optional: To close the QR code scannign after the result is found
                        //html5QrcodeScanner.clear();
                    }

                    // Optional callback for error, can be ignored.
                    function onScanError(qrCodeError) {
                        //console.log(qrCodeError);

                        // This callback would be called in case of qr code scan error or setup error.
                        // You can avoid this callback completely, as it can be very verbose in nature.
                    }

                    html5QrcodeScanner.render(onScanSuccess, onScanError);
                });
            }

            $('#comment').on('input', function () {
                if ($(this).val())
                    $('#btnDeny').prop('disabled', '')
                else
                    $('#btnDeny').prop('disabled', 'disabled')
            });
        })
    </script>
@endsection
