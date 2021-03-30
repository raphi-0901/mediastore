<div id="createType" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-success">
                <h4 class="modal-title">Neue Kategorie</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form autocomplete="off" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input class="form-control" id="cTname" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Überkategorie:</label>
                        <div id="cT-types"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Abbruch</button>
                        <div id="btnCreateType" class="btn btn-success text-white" data-dismiss="modal">Speichern
                        </div>
                    </div>
                </div> <!-- end modal-footer -->
            </form>
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end modal -->

