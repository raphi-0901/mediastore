@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <h4>Zeitraum wählen</h4>
        <div class="form-group mt-2">
            <input type="text" class="form-control date" id="singledaterange" data-toggle="date-picker"
                   data-cancel-class="btn-warning">
            <i class="fas fa-info-circle"></i> Datum kann nur geändert werden, wenn der Warenkorb noch leer ist.
        </div>
        <h4 class="mt-3">Kategorien</h4>
        <div class="row">
            <div class="col-9">
                <select class="form-control select2" id="pTypes" data-toggle="select2">
                    @foreach($parentTypes as $pType)
                        <option value="{{$pType->id}}">{{$pType->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3">
                <select class="form-control select2" id="sortBy" data-toggle="select2">
                    <option value="A-Z">A-Z</option>
                    <option value="Z-A">Z-A</option>
                </select>
            </div>
        </div>
        {{-- subTypes come here--}}
        <div class="row ml-0" id="sTypes"></div>
        <div class="d-flex justify-content-center">
            <div class="spinner-grow text-primary" role="status" id="loading"></div>
        </div>
        {{--        devices come here--}}
        <h3 class="mt-4">Verfügbare Geräte</h3>
        <div class="row mt-3" id="devices"></div>
    </div>
    <script>
        $(document).ready(function () {
            $('#loading').show()
            let isSearching = false;
            //set value from cookie if available
            let pTCookie = document.cookie.split(';').find(c => c.indexOf('pType') !== -1)
            if (pTCookie)
                $('#pTypes').val(pTCookie.split('=')[1]).change()

            let shoppingCart = {!! \Illuminate\Support\Facades\Auth::user()->shoppingCart()->pluck('devices.id') !!};
            let parentTypes = convertToArray({!! json_encode($parentTypes->toArray()) !!});
            let subTypes = convertToArray({!! json_encode($subTypes->toArray()) !!});

            function convertToArray(array) {
                let newArray = [];
                if (!Array.isArray(array)) {
                    for (let i in array) {
                        newArray.push(array[i]);
                    }
                    return newArray;
                }
                return array;
            }

            let selectedParent = parentTypes.find(pT => pT.id == $('#pTypes').val());

            //hole Benutzereinstellungen
            let userFrom = "{{!isset(\Illuminate\Support\Facades\Auth::user()->from) ? '' : \Illuminate\Support\Facades\Auth::user()->from->format('Y-m-d')}}";
            let userTo = "{{!isset(\Illuminate\Support\Facades\Auth::user()->to) ? '' : \Illuminate\Support\Facades\Auth::user()->to->format('Y-m-d')}}";

            //wenn user länger nicht da war und seine alten daten nicht mehr passen.
            if (moment(userFrom, 'YYYY-MM-DD') < moment())
                userFrom = null

            //wenn user länger nicht da war und seine alten daten nicht mehr passen.
            if (moment(userTo, 'YYYY-MM-DD') < moment())
                userTo = null

            let from = null;
            let to = null;
            userFrom
                ? from = moment(userFrom, 'YYYY-MM-DD')
                : from = moment(moment().add(2, 'days'));

            userTo
                ? to = moment(userTo, 'YYYY-MM-DD')
                : to = moment(moment().add(9, 'days'));

            //ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            //change subTypes
            $('#pTypes').on('change', function (e) {
                //ändere parent kategorie und speicher es als cookie
                selectedParent = parentTypes.find(pT => pT.id == $(this).val());
                document.cookie = 'pType=' + selectedParent.id + ";max-age=" + 60 * 60 * 24;

                //neue Subtypes erstellen
                buildSubTypes()
                filterDevices()
            })

            //change sort
            $('#sortBy').on('change', function (e) {
                filterDevices()
            })

            //filter on checkbox click
            $(document).on('click', 'input:checkbox', function (e) {
                filterDevices()
            })

            //different daterangepicker for small devices
            if (screen.width >= 564)
                $('#singledaterange').daterangepicker({
                    ranges: {
                        'Bis Ende der Woche': [moment(), moment().endOf('week')],
                        'Bis Ende des Monats': [moment(), moment().endOf('month')],
                        'Nächste Woche': [moment().add(7, 'days').startOf('week'), moment().add(7, 'days').endOf('week')],
                        'Nächsten zwei Wochen': [moment().add(7, 'days').startOf('week'), moment().add(14, 'days').endOf('week')],
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
                    "minDate": moment(),
                    "startDate": from,
                    "endDate": to,
                    "maxSpan": {
                        "days": {{\App\Setting::all()->find('maxSpan')->value}}
                    },
                });
            else
                $('#singledaterange').daterangepicker({
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
                    "opens": "center",
                    "minDate": moment(),
                    "startDate": from,
                    "endDate": to,
                    "maxSpan": {
                        "days": {{\App\Setting::all()->find('maxSpan')->value}}
                    },
                });

            //for mobile devices. keyboard should not appear.
            $('#singledaterange').on('focus', function (e) {
                $(this).blur()
            })

            $('#singledaterange').on('showCalendar.daterangepicker', function (ev, picker) {
                if (screen.width >= 564)
                    $('.ranges').next().attr('style', '');
                else
                    $('.ranges').next().attr('style', 'border: none');
            });

            $('#singledaterange').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('D. MMM YYYY') + ' - ' + picker.endDate.format('D. MMM YYYY'));
                from = picker.startDate;
                to = picker.endDate;
                setDates();
                filterDevices();
            });

            function setDates() {
                $.ajax({
                    type: 'POST',
                    url: "{{route('users.setDates')}}",
                    data: {
                        from: from.format('Y-MM-DD'),
                        to: to.format('Y-MM-DD'),
                    },
                    success: function (data) {
                        //console.log(data)
                    },
                    error: function (data) {
                        console.log(data)
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

            function filterDevices() {
                $('#loading').show()

                isSearching = true;

                //wenn keine from und to daten vorhanden sind, werden die standard ausgewählten genommen und in die DB geschrieben.
                if (!userFrom && !userTo)
                    setDates();

                let filterOptions = [];
                $('input:checked').each(function () {
                    filterOptions.push($(this).attr('name'));
                });

                //if anything is selected, give parentType
                if (filterOptions.length == 0)
                    filterOptions.push(selectedParent.id)

                $.ajax({
                    url: '{{route('devices.filter')}}',
                    type: 'get',
                    data: {
                        filter: filterOptions,
                        sortBy: $('#sortBy').val(),
                    },
                    success: function (data) {
                        $('#devices').empty()
                        let devices = data.devices;
                        devices.forEach(device => buildDevice(device));


                        isSearching = false;
                        $('#loading').hide()
                    },
                    error: function (data) {
                        console.log(data)
                        $.toast({
                            heading: "Fehler",
                            text: data.responseJSON.error,
                            icon: 'error',
                            position: 'top-right',
                            stack: stack,
                            loaderBg: 'rgba(0,0,0,0.2)',
                            hideAfter: 5000   // in milli seconds
                        })
                        isSearching = false;
                        $('#loading').hide()
                    }
                })
            }

            $("#devices").on("click", ".shoppingCart", function (event) {
                let button = $(this)

                if (!isSearching) {
                    let id = button.data('id')
                    let available = button.data('available')

                    let route = "";
                    if (available == true)
                        route = "{{ route('shoppingCart.add') }}";
                    else
                        route = "{{ route('shoppingCart.remove') }}";

                    $.ajax({
                        type: 'POST',
                        url: route,
                        data: {
                            id: id,
                        },
                        success: function (data) {
                            if (available == true) {
                                shoppingCart.push(id)
                                button.data('available', 0)
                                button.removeClass('btn-primary').addClass('btn-success')
                                button.find('i').addClass('fa-check').removeClass('fa-plus')
                                $.toast({
                                    heading: 'Erfolgreich',
                                    text: data.success,
                                    icon: 'success',
                                    position: 'top-right',
                                    stack: stack,
                                    loaderBg: 'rgba(0,0,0,0.2)',
                                    hideAfter: 5000   // in milli seconds
                                })
                            } else {
                                shoppingCart.splice(shoppingCart.indexOf(id), 1);
                                button.data('available', 1)
                                button.removeClass('btn-success').addClass('btn-primary')
                                button.find('i').removeClass('fa-check').addClass('fa-plus')
                                $.toast({
                                    heading: 'Erfolgreich',
                                    text: data.success,
                                    icon: 'info',
                                    position: 'top-right',
                                    stack: stack,
                                    loaderBg: 'rgba(0,0,0,0.2)',
                                    hideAfter: 5000   // in milli seconds
                                })
                            }

                            $("#shoppingCartInfo span").text(data.sCount)
                            toggleDateRangePicker()
                        },
                        error: function (data) {
                            console.log(data)
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
            })

            function toggleDateRangePicker() {
                if (shoppingCart.length !== 0)
                    $('#singledaterange').prop('disabled', true)
                else
                    $('#singledaterange').prop('disabled', false)
            }

            function buildDevice(device) {
                let route = '{{route('devices.show', ':id')}}'.replace(':id', device.id)
                let type = parentTypes.find(t => t.id == device.type_id)
                if (!type)
                    type = subTypes.find(t => t.id == device.type_id)


                let description = device.description;
                if (!description)
                    description = "Keine Beschreibung vorhanden"
                else if (description.length > 25)
                    description = device.description.substr(0, 25) + "..."

                let html = `<div class="col-sm-6 col-md-4 col-lg-3 device">
                <div class="card">
                <h6 class="card-header">${type.name}</h6>
                <div class="card-body">
                <h5 class="card-title">${device.name}</h5>
                <p class="card-text" style="height: 25px">${description}</p>
                <a href="${route}" class="btn btn-secondary mt-1 mr-1">
                <i class="fas fa-eye"></i></a>`;

                if (shoppingCart.find(d => d == device.id)) {
                    html += `<div class="btn btn-success mt-1 mr-1 shoppingCart float-right" data-id="${device.id}" data-available="0">
                    <i class="fas fa-check"></i></div>
                </div>`;
                } else {
                    html += `<div class="btn btn-primary mt-1 mr-1 shoppingCart float-right" data-id="${device.id}" data-available="1">
                    <i class="fas fa-plus"></i></div>
                </div>`;
                }
                $('#devices').append(html);
            }

            function buildSubTypes() {
                let html = '';
                $.each(subTypes, function (index, subType) {
                    if (subType.parent_id == selectedParent.id) {
                        html += `<div class="col-6 col-sm-4 col-md-3 mt-1">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" name="${subType.id}" id="${subType.id}" value="1">
                                    <label class="custom-control-label" for="${subType.id}">${subType.name}</label>
                                </div>
                            </div>`;
                    }
                });
                $('#sTypes').html(html)
            }

            toggleDateRangePicker()
            buildSubTypes()
            filterDevices()
        })
    </script>
@endsection

