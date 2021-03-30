<div id="editDevice" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-primary">
                <h4 class="modal-title">Gerät bearbeiten</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="editForm" autocomplete="off" class="form-horizontal">
                <div class="modal-body">
                    <h4 id="eDtitle" class="text-center my-3"></h4>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="eDname">Name:</label>
                            <input class="form-control" id="eDname" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="eDdescription">Beschreibung:</label>
                            <input class="form-control" id="eDdescription" name="description" required>
                        </div>
                        <div class="form-group">
                            <label for="eDserial">Seriennummer:</label>
                            <input class="form-control" id="eDserial" name="serial" required>
                        </div>
                        <div class="form-group">
                            <label for="eDqr">QR-ID:</label>
                            <input class="form-control" id="eDqr" name="qr" required>
                        </div>
                        <div class="form-group">
                            <label for="eDnote">Notiz:</label>
                            <input class="form-control" id="eDnote" name="note" required>
                        </div>
                        <div class="form-group">
                            <label>Kategorie:</label>
                            <div id="eD-types"></div>
                           {{-- <label>Kategorie:</label>
                            @foreach($types as $type)
                                @include('admin.devices.recursive', ['type' => $type, 'name' => 'eDtype', 'prefix' => 'et-'])
                            @endforeach--}}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Abbruch</button>
                        <div id="btnEditDevice" class="btn btn-primary text-white" data-dismiss="modal">Speichern</div>
                    </div>
                </div> <!-- end modal-footer -->
            </form>
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end modal -->
