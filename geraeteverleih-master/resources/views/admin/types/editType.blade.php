<div id="editType" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-primary">
                <h4 class="modal-title">Kategorie bearbeiten</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="editForm" autocomplete="off" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input class="form-control" id="eTname" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Überkategorie:</label>
                        <div id="eT-types"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Abbruch</button>
                        <div id="btnEditType" class="btn btn-primary text-white" data-dismiss="modal">Speichern</div>
                    </div>
                </div> <!-- end modal-footer -->
            </form>
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end modal -->
