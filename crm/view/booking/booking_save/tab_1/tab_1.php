<form id="frm_tab_1">
    <?php include_once('tour_info_sec.php') ?>
    <div class="container-fluid mg_tp_10">
        <div class="">
            <div class="app_panel_content no-pad">
                <div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                    <legend>Tour Details</legend>

                    <div class="row">
                        <input type="hidden" id="whatsapp_switch" value="<?= $whatsapp_switch ?>">
                        <input type="hidden" id="quot_adult_rate" value="0">
                        <input type="hidden" id="quot_with_bed_rate" value="0">
                        <input type="hidden" id="quot_without_bed_rate" value="0">
                        <input type="hidden" id="quot_infant_rate" value="0">
                        <input type="hidden" id="quot_single_person_rate" value="0">
                        <input type="hidden" id="group_quot_transport_loaded" value="0">
                        <div class="col-sm-4 mg_bt_10_sm_xs">
                            <select name="quotation_id" id="quotation_id" title="Select Quotation" style="width:100%;" class="form-control app_select2"
                                onchange="group_quotation_info_load()">
                                <option value="">*Select Quotation</option>
                                <option value="0">Sale Without Quotation</option>
                                <?php
                                $quot_query = "select * from group_tour_quotation_master where status='1' order by quotation_id desc";
                                if ($branch_status == 'yes') {
                                    if ($role == 'Branch Admin' || $role == 'Accountant') {
                                        $quot_query = "select * from group_tour_quotation_master where status='1' and branch_admin_id='$branch_admin_id' order by quotation_id desc";
                                    } elseif ($role != 'Admin' && $role != 'Branch Admin') {
                                        $quot_query = "select * from group_tour_quotation_master where status='1' and emp_id='$emp_id' and branch_admin_id='$branch_admin_id' order by quotation_id desc";
                                    }
                                } elseif ($role != 'Admin' && $role != 'Branch Admin') {
                                    $quot_query = "select * from group_tour_quotation_master where status='1' and emp_id='$emp_id' order by quotation_id desc";
                                }
                                $sq_quotation = mysqlQuery($quot_query);
                                while ($row_quotation = mysqli_fetch_assoc($sq_quotation)) {
                                    $yr = explode("-", $row_quotation['quotation_date']);
                                    $quotation_cost = $row_quotation['quotation_cost'];
                                    $currency_amount = '';
                                    if (isset($currency) && $row_quotation['currency_code'] != '0' && $currency != $row_quotation['currency_code']) {
                                        $currency_amount1 = currency_conversion($currency, $row_quotation['currency_code'], $quotation_cost);
                                        $currency_amount = ' (' . $currency_amount1 . ')';
                                    }
                                ?>
                                <option value="<?= $row_quotation['quotation_id'] ?>">
                                    <?= get_quotation_id($row_quotation['quotation_id'], $yr[0]) . ' : ' . $row_quotation['customer_name'] . ' : ' . $quotation_cost . ' /-' . $currency_amount ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-4 mg_bt_10_sm_xs">
                            <select class="form-control" style="width:100%" id="cmb_tour_name" name="cmb_tour_name"
                                title="Tour Name"
                                onchange="tour_group_reflect(this.id, false); payment_details_reflected_data('tbl_member_dynamic_row');  seats_availability_reflect(); tour_type_reflect(this.id); "
                                title="Tour Name">
                                <option value="">*Tour Name</option>
                                <?php
                                $sq = mysqlQuery("select tour_id,tour_name from tour_master where active_flag = 'Active' order by tour_name asc");
                                while ($row = mysqli_fetch_assoc($sq)) {
                                    echo "<option value='$row[tour_id]'>" . $row['tour_name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-sm-4 mg_bt_10_sm_xs">
                            <select class="form-control" id="cmb_tour_group" Title="Tour Date" name="cmb_tour_group"
                                onchange="seats_availability_reflect(); seats_availability_check();due_date_reflect(); tour_details_reflect(this.id);">
                                <option value="">*Tour Date</option>
                            </select>
                        </div>
                        <div id="div_seats_availability" class="reflect-seats"></div>
                        <div class="col-sm-4 mg_bt_10_sm_xs hidden">
                            <select name="tour_type" id="tour_type" title="Tour Type">

                            </select>
                        </div>
                    </div>

                    <input type="hidden" id="txt_available_seats" name="txt_available_seats">
                    <input type="hidden" id="txt_total_seats1" name="txt_total_seats">
                    <input type="hidden" id="seats_booked" name="seats_booked">
                    <input type="hidden" id="tour_type_r" name="tour_type_r">
                    <input type="hidden" id="operation" name="operation" value='save'>
                </div>
                <div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                    <legend>Customer Details</legend>
                    <?php include_once('personal_info_sec.php') ?>
                </div>

                <div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                    <legend>Passenger Details</legend>
                    <?php include_once('member_info_sec.php') ?>
                </div>

                <div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                    <?php include_once('emergency_contact_info.php') ?>
                </div>

                <div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                    <?php include_once('hoteling_facility_info.php') ?>
                </div>


            </div>
        </div>
        <div class="panel panel-default main_block bg_light pad_8 text-center mg_bt_0"
            style="background-color: #fff; border: none;">
            <button id="proceed_btn" class="btn btn-sm btn-info ico_right">Next&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
        </div>
    </div>
    </div>
</form>
<script src="../js/tab_1.js"></script>
<script src="../js/tab_1_tour_info_sec.js"></script>

<script>
$(document).ready(function() {
    $("#cmb_tour_name").select2();
    $("#quotation_id").select2();
});
tour_type_reflect('cmb_tour_name');
</script>