<div id="deleteOrder" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-danger">
                <h4 class="modal-title">Bestellung löschen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="deleteForm" autocomplete="off" class="form-horizontal">
                <div class="modal-body">
                    <h4 class="modal-title" id="dOrderDates"></h4>
                    <div>Möchten Sie diese Bestellung von <b><span id="dOrderName"></span></b> wirklich löschen?</div>
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Abbruch</button>
                        <div id="btnDeleteOrder" class="btn btn-danger text-white" data-dismiss="modal">Löschen</div>
                    </div>
                </div> <!-- end modal-footer -->
            </form>
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end modal -->
