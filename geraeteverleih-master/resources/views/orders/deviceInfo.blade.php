<div id="deviceInfo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="info-header-modalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-info">
                <h4 class="modal-title" id="iName"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group" id="out_scan_container">
                    <div>Gerät gescannt am <b><span id="out_scan"></span></b></div>
                    <label for="note-before">Notiz davor:</label>
                    <textarea class="form-control" id="iNote-before" rows="3" disabled></textarea>
                </div>

                <div class="form-group mt-3" id="back_scan_container">
                    <div>Gerät gescannt am <b><span id="back_scan"></span></b></div>
                    <label for="note-after">Notiz danach:</label>
                    <textarea class="form-control" id="iNote-after" rows="3" disabled></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-group">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Schließen</button>
                    <div class="btn btn-info text-white" data-dismiss="modal">OK</div>
                </div>
            </div> <!-- end modal-footer -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
