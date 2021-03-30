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
                    <h4 class="page-title">Einstellungen</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form class="form" action="{{ route('settings') }}" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="col-sm-8">
                                    <h4>Bearbeiten</h4>
                                </div>
                                <div class="col-sm-4">
                                    <button class="btn btn-success float-right" type="submit">Speichern</button>
                                </div>
                            </div>
                            <div class="form-row mt-3">
                                <div class="col-sm-8">
                                    <label for="maxSpan">Maximale Dauer:</label> <i class="fas fa-info-circle"
                                                                                         data-toggle="tooltip"
                                                                                         data-placement="top" title=""
                                                                                         data-original-title="Wie lange die Geräte in einer Bestellung ausgeliehen werden können."></i>
                                </div>
                                <div class="col-sm-4">
                                    <input class="form-control d-inline w-50 mr-2" name="maxSpan"
                                           id="maxSpan" type="number" min="0" required
                                           value="{{$settings->find("maxSpan")->value}}"/>..Tage
                                </div>
                            </div>
                            <div class="form-row mt-3">
                                <div class="col-sm-8">
                                    <label for="maxDeviceCount">Maximale Anzahl an Geräten pro Bestellung:</label> <i
                                        class="fas fa-info-circle"
                                        data-toggle="tooltip"
                                        data-placement="top" title=""
                                        data-original-title="Wie viele Geräte pro Bestellung erlaubt sind."></i>
                                </div>
                                <div class="col-sm-4">
                                    <input class="form-control d-inline w-50 mr-2" name="maxDeviceCount"
                                           id="maxDeviceCount" type="number" min="0" required
                                           value="{{$settings->find("maxDeviceCount")->value}}"/>..Geräte
                                </div>
                            </div>
                            <div class="form-row mt-3">
                                <div class="col-sm-8">
                                    <label for="removeFromShoppingCartAfter">Geräte reservieren für:</label> <i
                                        class="fas fa-info-circle"
                                        data-toggle="tooltip"
                                        data-placement="top" title=""
                                        data-original-title="Nach wie vielen Minuten die Geräte aus dem Warenkorb gelöscht werden."></i>
                                </div>
                                <div class="col-sm-4">
                                    <input class="form-control d-inline w-50 mr-2" name="removeFromShoppingCartAfter"
                                           id="removeFromShoppingCartAfter" type="number" min="1" required
                                           value="{{$settings->find("removeFromShoppingCartAfter")->value}}"/>..Minuten
                                </div>
                            </div>
                            <div class="form-row mt-3">
                                <div class="col-sm-8">
                                    <label for="sendEmailBefore">Schüler benachrichtigen:</label> <i
                                        class="fas fa-info-circle"
                                        data-toggle="tooltip"
                                        data-placement="top" title=""
                                        data-original-title="Wie viele Tage vor dem Ende der Ausleihung der Schüler eine E-Mail bekommt."></i>
                                </div>
                                <div class="col-sm-4">
                                    <input class="form-control d-inline w-50 mr-2" name="sendEmailBefore"
                                           id="sendEmailBefore" type="number" min="1" required
                                           value="{{$settings->find("sendEmailBefore")->value}}"/>..Tage davor
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
