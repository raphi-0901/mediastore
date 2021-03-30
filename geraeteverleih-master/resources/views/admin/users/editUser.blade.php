<div id="editUser" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-primary">
                <h4 class="modal-title">Benutzer bearbeiten</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="editForm" autocomplete="off" class="form-horizontal">
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="eUfirstName">Vorname:</label>
                                <input class="form-control" id="eUfirstName" readonly>
                            </div>
                            <div class="form-group col-6">
                                <label for="eUlastName">Nachname:</label>
                                <input class="form-control" id="eUlastName" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="eUclass">Klasse:</label>
                            <input class="form-control" id="eUclass" readonly>
                        </div>
                        <div class="form-group" id="typeSelection">
                            <label>Berechtigungen:</label>
                            <div id="eU-types"></div>
                        </div>
                       {{-- <div class="form-group" id="typeSelection">
                            @foreach($types as $type)
                                @include('admin.users.recursive', ['type' => $type])
                            @endforeach
                        </div>--}}
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Abbruch</button>
                        <div id="btnEditUser" class="btn btn-primary text-white" data-dismiss="modal">Speichern</div>
                    </div>
                </div> <!-- end modal-footer -->
            </form>
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end modal -->
