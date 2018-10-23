<ei_progress_modal>
<div id="modal-progress" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" if={!getState().success && !getState().error}>{getState().translate.text_please_wait}</h4>
                <h4 class="modal-title" if={getState().success}>{getState().translate.text_success_title}</h4>
                <h4 class="modal-title" if={getState().error}>{getState().translate.text_error_title}</h4>
            </div>
            <div class="modal-body">
                <ei_progress if={!getState().success && !getState().error}></ei_progress>
                <div if={getState().success}>{getState().success}</div>
                <div if={getState().error}>{getState().error}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{getState().translate.button_close}</button>
            </div>
        </div>

    </div>
</div>
</ei_progress_modal>