<?php
include "../../../model/model.php";
?>
<div class="modal fade" id="AirportQuickSave_modal" role="dialog" aria-labelledby="aqModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog w-65pr" style="width: 60%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="aqModalLabel">Add Airport</h4>
            </div>
            <div class="modal-body">
                <form id="frm_airport_quick">
                    <div class="panel panel-default panel-body app_panel_style feildset-panel">
                        <div class="row">
                            <div class="col-md-4 col-sm-6 mg_bt_10">
                                <select class="form-control app_select2" id="city_filter_airport" name="city_filter_airport" style="width:100%" title="Select City Name" data-add-new-option="true">
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-6 mg_bt_10">
                                <input type="text" class="form-control" id="aq_airport_name" name="aq_airport_name" placeholder="*Airport Name" title="Airport Name" onchange="validate_spaces(this.id);">
                            </div>
                            <div class="col-md-4 col-sm-6 mg_bt_10">
                                <input type="text" class="form-control" id="aq_airport_code" name="aq_airport_code" placeholder="*Airport Code" title="Airport Code" style="text-transform:uppercase;" onchange="validate_spaces(this.id);">
                            </div>
                        </div>
                    </div>
                    <div class="row mg_tp_20 text-center">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-success" id="btn_aq_save"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
