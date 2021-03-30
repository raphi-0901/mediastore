<div id="deleteDevice" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-danger">
                <h4 class="modal-title"><span id="rmDeviceName"></span> entfernen?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="deleteForm" autocomplete="off" class="form-horizontal">
                <div class="modal-body">
                    Möchten Sie dieses Gerät wirklich aus der Bestellung entfernen?
                    <div id="deleteDeviceModalInfo">
                        <br/>
                        <h4>Hinweis!</h4>
                        Wenn Sie dieses Gerät löschen, wird auch die Bestellung gelöscht, da sonst kein Gerät mehr in der Bestellung vorhanden ist!
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Abbruch</button>
                        <div id="btnDeleteDevice" class="btn btn-danger text-white" data-dismiss="modal">Löschen</div>
                    </div>
                </div> <!-- end modal-footer -->
            </form>
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end modal -->
