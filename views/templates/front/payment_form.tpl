<form action="{$action}" id="mercury-payment-form">
        <input type="hidden" name="refresh_period" value="{$refresh_period}">
        <input type="hidden" name="url" value="{$url}">
        <input type="hidden" name="status_url" value="{$status_url}">
        <input type="hidden" name="static_url" value="{$modules_dir}">
        <input type="hidden" name="get_settings_url" value="{$get_settings_url}">
        <input type="hidden" name="success_url" value="{$success_url}">
</form>
<div class="loader-modal"></div>
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" style="margin: 0 auto;" id="errorModalLabel"></h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>