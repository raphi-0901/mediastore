<div id="createDevice" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-success">
                <h4 class="modal-title">Neues Gerät</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form autocomplete="off" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cDname">Name:</label>
                        <input class="form-control" id="cDname" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="cDdescription">Beschreibung:</label>
                        <input class="form-control" id="cDdescription" name="description" required>
                    </div>
                    <div class="form-group">
                        <label for="cDserial">Seriennummer:</label>
                        <input class="form-control" id="cDserial" name="serial" required>
                    </div>
                    <div class="form-group">
                        <label for="cDqr">QR-ID:</label>
                        <input class="form-control" id="cDqr" name="qr" required>
                    </div>
                    <div class="form-group">
                        <label for="cDnote">Notiz:</label>
                        <input class="form-control" id="cDnote" name="note" required>
                    </div>
                    <div class="form-group">
                        <label>Kategorie:</label>
                        <div id="cD-types"></div>
                        {{--<label>Kategorie:</label>
                        @foreach($types as $type)
                            @include('admin.devices.recursive', ['type' => $type, 'name' => 'cDtype', 'prefix' => 'ct-'])
                        @endforeach--}}
                    </div>
                    <div class="form-group">
                        <label for="serial">Anzahl:</label>
                        <input class="form-control" id="cCount" name="count" type="number" min="1" placeholder="1">
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="form-group">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Abbruch</button>
                        <div id="btnCreateDevice" class="btn btn-success text-white" data-dismiss="modal">Speichern
                        </div>
                    </div>
                </div> <!-- end modal-footer -->
            </form>
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end modal -->

