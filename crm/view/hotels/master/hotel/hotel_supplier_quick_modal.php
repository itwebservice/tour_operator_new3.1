<?php
include "../../../../model/model.php";
?>
<div class="modal fade" id="Hotelsupplierdetails_modal" role="dialog" aria-labelledby="hsqModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog w-65pr" style="width: 60%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="hsqModalLabel">Hotel Supplier Details</h4>
            </div>
            <div class="modal-body">
                <form id="frm_hotel_supplier_quick">
                    <div class="panel panel-default panel-body app_panel_style feildset-panel">
                        <legend>Basic Information</legend>
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mg_bt_10">
                                <select class="form-control app_select2" id="city_filter_hotel" name="city_filter_hotel" style="width:100%" title="Select City Name" data-add-new-option="true">
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6 mg_bt_10">
                                <input type="text" class="form-control" id="hsq_hotel_name" name="hsq_hotel_name" placeholder="*Hotel Name" title="Hotel Name" onchange="validate_spaces(this.id);">
                            </div>
                            <div class="col-md-3 col-sm-6 mg_bt_10">
                                <select name="state_filter_hotel" id="state_filter_hotel" title="Select State/Country Name" style="width:100%" required class="app_select2">
                                    <?php get_states_dropdown(); ?>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6 mg_bt_10">
                                <select id="hotel_Category" name="hotel_Category" title="*Category" class="app_select2" style="width:100%">
                                    <option value="">Select Hotel Category</option>
                                    <option value="1 Star">1 Star</option>
                                    <option value="2 Star">2 Star</option>
                                    <option value="3 Star">3 Star</option>
                                    <option value="4 Star">4 Star</option>
                                    <option value="5 Star">5 Star</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mg_tp_20 text-center">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-success" id="btn_hsq_save"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
