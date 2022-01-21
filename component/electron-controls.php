<div id="electron-shutdown-modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div class="modal-body p-5">
                <div class="card-panel p-4 border-0 mb-0 rounded-0 text-center" data-dismiss="modal">
                    <div class="icon-lg-pop mb-4 text-center">
                        <i class="material-icons text-muted">warning</i>
                    </div>
                    <p class="m-0 font-weight-bold text-center">Proceed to shutdown your work station?</p>
                    <div class="pt-4 text-center">
                        <button class="btn-danger btn" id="app-exit">Shutdown</button>
                        <button class="btn-secondary btn">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="electron-reboot-modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div class="modal-body p-5">
                <div class="card-panel p-4 border-0 mb-0 rounded-0 text-center" data-dismiss="modal">
                    <div class="icon-lg-pop mb-4 text-center">
                        <i class="material-icons text-muted">warning</i>
                    </div>
                    <p class="m-0 font-weight-bold text-center">Proceed to reboot your work station?</p>
                    <div class="pt-4 text-center">
                        <button class="btn-danger btn" id="app-reboot">Reboot</button>
                        <button class="btn-secondary btn">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ios" id="electron-minimize-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Minimize app</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Minimizing this application requires an administration code. Please enter it on the field below</p>
                <br>
                <input type="password" id="electron-minimize-code" class="form-control" placeholder="Authentication code" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="material-icons myicon-lg">close</i> Cancel</button>
                <button type="button" id="app-minimize" class="btn btn-success"><i class="material-icons myicon-lg">save_alt</i> Submit code</button>
            </div>
        </div>
    </div>
</div>