<div id="denyOrder" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-danger">
                <h4 class="modal-title">Bestellung ablehnen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form action="{{route('orders.deny',$order->id)}}" autocomplete="off" class="form-horizontal" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="comment">Kommentar:</label>
                        <textarea required class="form-control mb-1" id="comment" name="comment" rows="3"></textarea>
                        <i class="fas fa-info-circle"></i> Dieser Vorgang kann nicht rückgängig gemacht werden!
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Abbruch</button>
                        <button type="submit" id="btnDeny" class="btn btn-danger text-white" disabled="disabled">Ablehnen</button>
                    </div>
                </div> <!-- end modal-footer -->
            </form>
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end modal -->
