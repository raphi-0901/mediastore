<div id="forceDeleteDevice" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-danger">
                <h4 class="modal-title"><span id="fdDeviceName"></span> komplett löschen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="deleteForm" autocomplete="off" class="form-horizontal">
                <div class="modal-body">
                    <div>Möchten Sie dieses Gerät wirklich komplett löschen?</div><br><br>
                    Es wird aus allen Bestellungen gelöscht. Bitte löschen Sie nur, wenn sie es aus Versehen erstellt haben.
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Abbruch</button>
                        <div id="btnForceDeleteDevice" class="btn btn-danger text-white" data-dismiss="modal">Löschen</div>
                    </div>
                </div> <!-- end modal-footer -->
            </form>
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end modal -->
