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
                    <h4 class="page-title">Warenkorb</h4>
                </div>
            </div>
        </div>

    @include('student.shoppingCart.deleteShoppingCart')
    @include('student.shoppingCart.deleteDevice')
    @include('student.shoppingCart.acceptPolicies')
    <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if($user->shoppingCart()->count() == 0)
                            <h5>Warenkorb ist noch leer.</h5>
                            <a class="btn btn-primary" href="{{route('index')}}">Weiter anschauen</a>
                        @else
                            <h4>{{$user->from->isoFormat("Do MMM. YYYY") . ' - ' . $user->to->isoFormat("Do MMM. YYYY")}}</h4>
                            <table id="sort-datatable" class="table table-centered table-striped mt-4 mobile-table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="no-sort-symbol"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($devices as $d)
                                    <tr>
                                        <td>{{$d->name}}</td>
                                        <td data-title="" data-sort="">
                                            <a href="{{route('devices.show', $d->id)}}"
                                               class="btn btn-secondary mt-1 mr-1">
                                                <i class="fas fa-eye"></i></a>
                                            <div data-toggle="modal" data-target="#deleteDevice"
                                                 class="btn btn-danger mt-1 mr-1"
                                                 data-name="{{$d->name}}"
                                                 data-id="{{$d->id}}">
                                                <i class="fas fa-trash"></i></div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div data-toggle="modal" data-target="#acceptPolicies"
                                 class="btn btn-primary mt-1 ml-1">Bestellen
                            </div>

                            <div data-toggle="modal" data-target="#deleteShoppingCart"
                                 class="btn btn-danger mt-1 mr-1 float-right">Warenkorb löschen
                            </div>
                            <a class="btn btn-primary mt-1 mr-1 float-right" href="{{route('index')}}">Weiter anschauen</a>

                        @endif
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div>
    </div>
    <!-- end row -->
    <script type="text/javascript">
        $(function () {
            let deviceID = null
            let row = null
            let shoppingCart = {!! \Illuminate\Support\Facades\Auth::user()->shoppingCart()->pluck('devices.id') !!};

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

                $('#checked').on('click', function () {
                    if ($(this).prop('checked'))
                        $('#btnOrder').prop('disabled', '')
                    else
                        $('#btnOrder').prop('disabled', 'disabled')
                });

                // One modal for every "edit user" - Set data
                $('#deleteDevice').on('show.bs.modal', function (event) {
                    let button = $(event.relatedTarget) // Button that triggered the modal
                    // Extract info from data-* attributes
                    deviceID = button.data('id')
                    let deviceName = button.data('name')

                    $('#rmDeviceName').html(deviceName)
                })

                $('#btnDeleteDevice').on('click', function () {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('shoppingCart.remove') }}",
                        data: {
                            id: deviceID,
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
                            shoppingCart.splice(shoppingCart.indexOf(deviceID), 1);
                            $("#shoppingCartInfo span").text(data.sCount)
                            dataTable.row(row).remove().draw();

                            if (shoppingCart.length === 0) {
                                $(".card-body > h4").hide()
                                $(".card-body > table").hide()
                                $(".card-body").html('<h5>Warenkorb ist noch leer.</h5><a class="btn btn-primary" href="{{route("index")}}">Weiter anschauen</a>')
                            }
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
                    })
                })
            })
        })
    </script>
@endsection
