<form id="frm_tab_2">
    <div class="app_panel">

        <div class="app_panel_content no-pad">

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 app_accordion">
                        <div class="panel-group main_block" id="accordion" role="tablist" aria-multiselectable="true">

                            <!-- Train Information -->
                            <div class="accordion_content main_block mg_bt_10">
                                <div class="panel panel-default main_block">
                                    <div class="panel-heading main_block" role="tab" id="heading_train">
                                        <div class="Normal main_block" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_train" aria-expanded="true" aria-controls="collapse_train">
                                            <div class="col-md-12"><span>Train Information</span></div>
                                        </div>
                                    </div>
                                    <div id="collapse_train" class="panel-collapse collapse in main_block" role="tabpanel" aria-labelledby="heading_train">
                                        <div class="panel-body">
                                            <?php include_once('train_info.php'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hotel Information -->
                            <div class="accordion_content main_block mg_bt_10">
                                <div class="panel panel-default main_block">
                                    <div class="panel-heading main_block" role="tab" id="heading_hotel">
                                        <div class="Normal collapsed main_block" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_hotel" aria-expanded="false" aria-controls="collapse_hotel">
                                            <div class="col-md-12"><span>Hotel Information</span></div>
                                        </div>
                                    </div>
                                    <div id="collapse_hotel" class="panel-collapse collapse main_block" role="tabpanel" aria-labelledby="heading_hotel">
                                        <div class="panel-body">
                                            <?php include_once('hotel_info.php'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Transport Information -->
                            <div class="accordion_content main_block mg_bt_10">
                                <div class="panel panel-default main_block">
                                    <div class="panel-heading main_block" role="tab" id="heading_transport">
                                        <div class="Normal collapsed main_block" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_transport" aria-expanded="false" aria-controls="collapse_transport">
                                            <div class="col-md-12"><span>Transport Information</span></div>
                                        </div>
                                    </div>
                                    <div id="collapse_transport" class="panel-collapse collapse main_block" role="tabpanel" aria-labelledby="heading_transport">
                                        <div class="panel-body">
                                            <?php include_once('transport_info.php'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Flight Information -->
                            <div class="accordion_content main_block mg_bt_10">
                                <div class="panel panel-default main_block">
                                    <div class="panel-heading main_block" role="tab" id="heading_flight">
                                        <div class="Normal collapsed main_block" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_flight" aria-expanded="false" aria-controls="collapse_flight">
                                            <div class="col-md-12"><span>Flight Information</span></div>
                                        </div>
                                    </div>
                                    <div id="collapse_flight" class="panel-collapse collapse main_block" role="tabpanel" aria-labelledby="heading_flight">
                                        <div class="panel-body">
                                            <?php include_once('plane_info.php'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cruise Information -->
                            <div class="accordion_content main_block mg_bt_10">
                                <div class="panel panel-default main_block">
                                    <div class="panel-heading main_block" role="tab" id="heading_cruise">
                                        <div class="Normal collapsed main_block" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_cruise" aria-expanded="false" aria-controls="collapse_cruise">
                                            <div class="col-md-12"><span>Cruise Information</span></div>
                                        </div>
                                    </div>
                                    <div id="collapse_cruise" class="panel-collapse collapse main_block" role="tabpanel" aria-labelledby="heading_cruise">
                                        <div class="panel-body">
                                            <?php include_once('cruise_info.php'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="panel panel-default panel-body main_block bg_light pad_8 text-center">
                    <div class="row">
                        <div class="col-md-5 col-sm-6 col-xs-12 text-right text_center_xs"><label>Total Travel
                                Expense</label></div>
                        <div class="col-md-2 col-sm-4 col-xs-12"><input type="text" id="txt_travel_total_expense"
                                class="amount_feild_highlight text-right" name="txt_travel_total_expense"
                                title="Total Travel Expense"
                                value="<?php echo $tourwise_details['total_travel_expense'] ?>" readonly /></div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default main_block bg_light pad_8 text-center mg_bt_0">
                <button type="button" onclick="switch_to_tab_1()" class="btn btn-sm btn-info ico_left"><i
                        class="fa fa-arrow-left"></i>&nbsp;&nbsp;Previous</button> &nbsp;&nbsp;
                <button class="btn btn-sm btn-info ico_right"
                    onclick="calculate_cruise_expense('tbl_dynamic_cruise_package_booking')">Next&nbsp;&nbsp;<i
                        class="fa fa-arrow-right"></i></button>
            </div>

            <?= end_panel() ?>
</form>

<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>
<script src="../js/tab_2.js"></script>
<script src="../js/tab_2_calculations.js"></script>

<script>
// Function to add transport row with proper initialization
function addTransportRowUpdate(){
    addRow('tbl_booking_transport_u');
    setTimeout(function(){ 
        destinationLoading('select[name^=transport_pickup_from]', 'Pickup Location');
        destinationLoading('select[name^=transport_drop_to]', 'Drop-off Location');
        $('#tbl_booking_transport_u').find('.app_select2').select2();
        // Reinitialize datepicker for all rows (including new one)
        $('#tbl_booking_transport_u').find('.app_datepicker').datetimepicker({ 
            timepicker: false, 
            format: 'd-m-Y' 
        });
    }, 100);
}

// App_accordion
jQuery(document).ready(function() {			
    jQuery(".panel-heading").click(function(){ 
        jQuery('#accordion .panel-heading').not(this).removeClass('isOpen');
        jQuery(this).toggleClass('isOpen');
        jQuery(this).next(".panel-collapse").addClass('thePanel');
        jQuery('#accordion .panel-collapse').not('.thePanel').slideUp("slow"); 
        jQuery(".thePanel").slideToggle("slow").removeClass('thePanel'); 
    });
});
</script>