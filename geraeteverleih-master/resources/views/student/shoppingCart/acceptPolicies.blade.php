<div id="acceptPolicies" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-primary">
                <h4 class="modal-title">Bestellen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form autocomplete="off" class="form-horizontal" method="post" action="{{route('orders.finish')}}">
                @csrf
                <div class="modal-body pb-0">
                    Mit Abschluss des Bestellvorganges bestätigen Sie, dass Sie die <a
                        href="{{route('policies')}}" target="_blank">Richtlinien</a> gelesen und bestätigt haben.
                    <div class="form-group mt-3 mb-0 pb-0">
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input"
                                   id="checked"
                                   name="checked" value="1" required>
                            <label class="custom-control-label"
                                   for="checked">Richtlinien hier aktzeptieren</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Abbruch</button>
                        <button id="btnOrder" type="submit" class="btn btn-primary text-white" disabled="disabled">
                            Bestellen
                        </button>
                    </div>
                </div> <!-- end modal-footer -->
            </form>
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end modal -->

