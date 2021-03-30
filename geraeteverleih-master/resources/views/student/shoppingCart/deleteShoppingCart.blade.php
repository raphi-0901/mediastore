<div id="deleteShoppingCart" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-danger">
                <h4 class="modal-title">Warenkorb leeren?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form  autocomplete="off" class="form-horizontal">
                <div class="modal-body">
                    <div>Möchten Sie wirklich den ganzen Warenkorb leeren?</div>
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Abbruch</button>
                        <a href="{{route('shoppingCart.clear')}}" class="btn btn-danger text-white">Leeren</a>
                    </div>
                </div> <!-- end modal-footer -->
            </form>
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end modal -->
