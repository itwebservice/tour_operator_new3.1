<?php
include "../../../model/model.php";

$booking_id = $_POST['booking_id'];
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_POST['branch_status'];
$sq_enq_info = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$booking_id' and delete_status='0'"));
$reflections = json_decode($sq_enq_info['reflections']);
if($reflections[0]->tax_apply_on == '1') { 
    $tax_apply_on = 'Basic Amount';
}
else if($reflections[0]->tax_apply_on == '2') { 
    $tax_apply_on = 'Service Charge';
}
else if($reflections[0]->tax_apply_on == '3') { 
    $tax_apply_on = 'Total';
}else{
    $tax_apply_on = '';
}
?>

<form id="frm_booking_update">
    <input type="hidden" id="booking_id" name="booking_id" value="<?= $booking_id ?>">
    <input type="hidden" id="car_sc" name="car_sc" value="<?php echo $reflections[0]->car_sc ?>">
    <input type="hidden" id="car_markup" name="car_markup" value="<?php echo $reflections[0]->car_markup ?>">
    <input type="hidden" id="car_taxes" name="car_taxes" value="<?php echo $reflections[0]->car_taxes ?>">
    <input type="hidden" id="car_markup_taxes" name="car_markup_taxes"
        value="<?php echo $reflections[0]->car_markup_taxes ?>">
    <input type="hidden" id="tax_apply_on" name="tax_apply_on" value="<?php echo $tax_apply_on ?>">
    <input type="hidden" id="atax_apply_on" name="atax_apply_on" value="<?php echo $reflections[0]->tax_apply_on ?>">
    <input type="hidden" id="tax_value1" name="tax_value1" value="<?php echo $reflections[0]->tax_value ?>">
    <input type="hidden" id="markup_tax_value1" name="markup_tax_value1" value="<?php echo $reflections[0]->markup_tax_value ?>">

    <div class="modal fade" id="booking_update_modal" role="dialog" aria-labelledby="myModalLabel"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document" style="width: 95% !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Car Rental Booking</h4>
                </div>
                <div class="modal-body">


                    <div class="panel panel-default panel-body app_panel_style feildset-panel">
                        <legend>Customer Details</legend>
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                <select name="customer_id1" id="customer_id1" style="width: 100%"
                                    onchange="customer_info_load('1')" disabled>
                                    <?php
                                    $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_enq_info[customer_id]'"));
                                    if ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') {
                                    ?>
                                    <option value="<?= $sq_customer['customer_id'] ?>">
                                        <?= $sq_customer['company_name'] ?></option>
                                    <?php } else { ?>
                                    <option value="<?= $sq_customer['customer_id'] ?>">
                                        <?= $sq_customer['first_name'] . ' ' . $sq_customer['last_name'] ?></option>
                                    <?php } ?>
                                    <?php get_customer_dropdown($role, $branch_admin_id, $branch_status); ?>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="mobile_no1" name="mobile_no1" title="Mobile Number"
                                    placeholder="Mobile No" title="Mobile No" readonly>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="email_id1" name="email_id1" title="Email Id"
                                    placeholder="Email ID" title="Email ID" readonly>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="company_name1" class="hidden" name="company_name1"
                                    title="Company Name" placeholder="Company Name" title="Company Name" readonly>
                            </div>
                            <script>
                            customer_info_load('1');
                            </script>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-sm-4 col-xs-12">
                                <input type="text" id="pass_name1" name="pass_name1" title="Guest Name"
                                    placeholder="Guest Name" value="<?= $sq_enq_info['pass_name'] ?>">
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default panel-body app_panel_style feildset-panel">
                        <legend>Quotation Details</legend>
                        <div class="row">
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <?php
                                $quo_id = "Without Quotation";
                                if($sq_enq_info['quotation_id'] != 0){
                                    $sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_quotation_master where quotation_id='$sq_enq_info[quotation_id]'"));
                                    $quotation_date = $sq_quotation['quotation_date'];
                                    $yr = explode("-", $quotation_date);
                                    $year = $yr[0];
                                    $quo_id = get_quotation_id($sq_enq_info['quotation_id'], $year). ': ' . $sq_quotation['customer_name'];
                                }
                                    ?>
                                    <input type="text" name="quotation_id" id="quotation_id" value="<?php echo $quo_id; ?>"
                                    readonly>

                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <select name="vehicle_name1" id="vehicle_name1" title="Vehicle Name"
                                    class="form-control" onchange="get_capacity(this.id,'1')">
                                    <option value="<?= $sq_enq_info['vehicle_name'] ?>">
                                        <?= $sq_enq_info['vehicle_name'] ?></option>
                                    <option value="">*Select Vehicle</option>
                                    <?php
                                    $sql = mysqlQuery("select * from b2b_transfer_master where status!='Inactive'");
                                    while ($row = mysqli_fetch_assoc($sql)) {
                                    ?>
                                    <option value="<?= $row['vehicle_name'] ?>"><?= $row['vehicle_name'] ?></option>
                                    <?php }  ?>
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <select name="travel_type1" id="travel_type1" title="Travel Type" class="form-control"
                                    onchange="reflect_feilds();" disabled>

                                    <option value="<?= $sq_enq_info['travel_type'] ?>">
                                        <?= $sq_enq_info['travel_type'] ?></option>
                                    <option value="Local">Local</option>
                                    <option value="Outstation">Outstation</option>

                                </select>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="capacity1" name="capacity1"
                                    onchange="validate_balance(this.id);calculate_total_fees(this.id, '1');"
                                    placeholder="Capacity" title="Capacity" class="form-control"
                                    value="<?= $sq_enq_info['capacity'] ?>">
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="total_pax1" name="total_pax1"
                                    onchange="validate_balance(this.id);calculate_total_fees(this.id, '1')"
                                    placeholder="No Of Pax" title="No Of Pax" value="<?= $sq_enq_info['total_pax'] ?>">
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <textarea type="text" name="local_places_to_visit1" onchange="validate_spaces(this.id)"
                                    id="local_places_to_visit1" placeholder="Route" title="Route"
                                    rows="1"><?= $sq_enq_info['local_places_to_visit'] ?></textarea>
                                <select name="places_to_visit1" id="places_to_visit1" title="Places To visit"
                                    class="form-control" onchange="get_car_cost();">
                                    <option value="<?= $sq_enq_info['places_to_visit'] ?>">
                                        <?= $sq_enq_info['places_to_visit'] ?></option>
                                    <option value="">*Select Route</option>
                                    <?php
                                    $sql = mysqlQuery("select * from car_rental_tariff_entries where tour_type='Outstation'");
                                    while ($row = mysqli_fetch_assoc($sql)) {
                                    ?>
                                    <option value="<?= $row['route'] ?>"><?= $row['route'] ?></option>
                                    <?php }  ?>
                                </select>
                            </div>


                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="from_date1" name="from_date1" placeholder="From Date"
                                    title="From Date" class="form-control"
                                    value="<?= date('d-m-Y', strtotime($sq_enq_info['from_date'])) ?>"
                                    onchange="get_to_date(this.id,'to_date1');total_days_reflect('1');;calculate_total_fees(this.id, '1')">
                            </div>

                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="to_date1" name="to_date1" placeholder="To Date" title="To Date"
                                    class="form-control"
                                    value="<?= date('d-m-Y', strtotime($sq_enq_info['to_date'])) ?>"
                                    onchange="validate_validDate('from_date1','to_date1');total_days_reflect('1');calculate_total_fees(this.id, '1')">
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="total_hrs1" name="total_hrs1" placeholder="Total Hrs"
                                    title="Total Hrs" class="form-control" value="<?= $sq_enq_info['total_hrs'] ?>">
                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="days_of_traveling1" name="days_of_traveling1"
                                    onchange="validate_balance(this.id);calculate_total_fees(this.id, '1');"
                                    placeholder="Days Of Travelling" title="Days Of Travelling"
                                    value="<?= $sq_enq_info['days_of_traveling'] ?>"
                                    value="<?= $sq_enq_info['markup_cost_subtotal'] ?>">
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="rate1" name="rate1" class="text-right form-control"
                                    placeholder="Daily Rate" title="Daily Rate" onchange="calculate_total_fees(this.id,'1');get_auto_values('booking_date1','basic_amount1','payment_mode','service_charge1','markup_cost1','update','true','service_charge');"
                                    class="form-control" value="<?= $sq_enq_info['rate'] ?>">
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="extra_km1" name="extra_km1" placeholder="Extra Km Rate"
                                    title="Extra Km Rate" class="text-right form-control"
                                    onchange="validate_balance(this.id)" value="<?= $sq_enq_info['extra_km'] ?>">
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="extra_hr_cost1" name="extra_hr_cost1"
                                    class="text-right form-control" placeholder="Extra Hr Rate" title="Extra Hr Rate"
                                    onchange="calculate_total_fees(this.id, '1');validate_balance(this.id);"
                                    value="<?= $sq_enq_info['extra_hr_cost'] ?>">
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="total_max_km1" name="total_max_km1" placeholder="Total Max Km"
                                    title="Total Max Km" class="form-control"
                                    value="<?= $sq_enq_info['total_max_km'] ?>">
                                <input type="text" id="total_km1" name="total_km1" class="text-right form-control"
                                    placeholder="Total Km" title="Total Km" class="form-control"
                                    value="<?= $sq_enq_info['total_km'] ?>">
                            </div>
                            <?php
                            if($sq_enq_info['travel_type'] == 'Outstation'){
                                ?>
                                <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                    <input type="text" id="traveling_date1" name="traveling_date1"
                                        placeholder="Travelling Date" title="Travelling Date"
                                        value="<?= (($sq_enq_info['traveling_date'])!='1970-01-01') ? date('d-m-Y', strtotime($sq_enq_info['traveling_date'])) : ''  ?>">
                                </div>
                                <?php
                            }else{ ?>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="traveling_date1" name="traveling_date1"
                                    placeholder="Travelling Date" title="Travelling Date">
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Tour Itinerary Section -->
                    <div class="panel panel-default panel-body app_panel_style feildset-panel">
                        <legend>Tour Itinerary Details</legend>
                        <div class="app_panel_content Filter-panel">
                            <div class="row mg_bt_10">
                                <div class="col-xs-12 text-right text_center_xs">
                                    <button type="button" class="btn btn-excel btn-sm" onClick="addRowUpdate('package_program_list')" title="Add row"><i class="fa fa-plus"></i></button>
                                    <button type="button" class="btn btn-pdf btn-sm" onClick="deleteRow('package_program_list');renumber_itinerary_rows_update();" title="Delete row"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-6 col-xs-12 mg_bt_10">
                                    <div style="max-height: 400px; overflow-y: auto;">
                                        <table style="width:100%" id="package_program_list" name="package_program_list"
                                            class="table mg_bt_0 table-bordered">
                                            <tbody>
                                                <?php
                                                // Fetch existing itinerary for this booking
                                                $sq_program = mysqlQuery("SELECT * FROM car_rental_booking_program WHERE booking_id='$booking_id' ORDER BY entry_id ASC");
                                                $program_count = mysqli_num_rows($sq_program);
                                                
                                                if($program_count > 0){
                                                    $count = 0;
                                                    while($row_program = mysqli_fetch_assoc($sq_program)){
                                                        $count++;
                                                ?>
                                                <tr>
                                                    <td width="27px;" style="padding-right: 10px !important;">
                                                        <input class="css-checkbox mg_bt_10 labelauty" id="chk_program<?= $count ?>" type="checkbox" checked style="display: none;">
                                                        <label for="chk_program<?= $count ?>" style="margin-top: 55px;">
                                                            <span class="labelauty-unchecked-image"></span>
                                                            <span class="labelauty-checked-image"></span>
                                                        </label>
                                                    </td>
                                                    <td width="50px;">
                                                        <input maxlength="15" value="<?= $count ?>" type="text" name="username" placeholder="Sr. No." style="margin-top: 35px;" class="form-control" disabled="">
                                                    </td>
                                                    <td class="col-md-3 no-pad" style="padding-left: 5px !important;">
                                                        <input type="text" id="special_attaraction<?= $count ?>" style="margin-top: 35px;" onchange="validate_spaces(this.id);" name="special_attaraction" class="form-control mg_bt_10" placeholder="Special Attraction" title="Special Attraction" value="<?= $row_program['attraction'] ?>">
                                                    </td>
                                                    <td class='col-md-5 no-pad' style="padding-left: 5px !important;position: relative;">
                                                        <textarea id="day_program<?= $count ?>" name="day_program" style="height:90px;" class="form-control mg_bt_10 day_program" placeholder="*Day Program" title="Day-wise Program" onchange="validate_spaces(this.id);" rows="3"><?= $row_program['day_wise_program'] ?></textarea>
                                                        <span class="style_text">
                                                            <span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span>
                                                            <span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span>
                                                        </span>
                                                    </td>
                                                    <td class="col-md-1/2 no-pad" style="padding-left: 5px !important;">
                                                        <input type="text" id="overnight_stay<?= $count ?>" style="margin-top: 35px;" name="overnight_stay" onchange="validate_spaces(this.id);" class="form-control mg_bt_10" placeholder="Overnight Stay" title="Overnight Stay" value="<?= $row_program['stay'] ?>">
                                                    </td>
                                                    <td class="col-md-1/2 no-pad" style="padding-left: 5px !important;">
                                                        <select id="meal_plan<?= $count ?>" title="meal plan" style="margin-top: 35px;" name="meal_plan" class="form-control mg_bt_10" data-original-title="Meal Plan">
                                                            <option value="<?= $row_program['meal_plan'] ?>"><?= $row_program['meal_plan'] ?></option>
                                                            <?php get_mealplan_dropdown(); ?>
                                                        </select>
                                                    </td>
                                                    <td class='col-md-1 pad_8'>
                                                        <button type="button" class="btn btn-info btn-iti btn-sm itinerary-btn" style="margin-top: 35px; border:none;" data-row="<?= $count ?>" id="itinerary<?= $count ?>" title="Add Itinerary" onClick="add_itinerary_booking_update(0,'special_attaraction<?= $count ?>','day_program<?= $count ?>','overnight_stay<?= $count ?>','meal_plan<?= $count ?>','Day-<?= $count ?>')">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </td>
                                                    <td style="display:none">
                                                        <input type="text" name="package_id_n" value="" autocomplete="off" class="form-control" data-original-title="" title="">
                                                    </td>
                                                </tr>
                                                <?php
                                                    }
                                                } else {
                                                    // If no itinerary exists, show one blank row
                                                ?>
                                                <tr>
                                                    <td width="27px;" style="padding-right: 10px !important;">
                                                        <input class="css-checkbox mg_bt_10 labelauty" id="chk_program1" type="checkbox" checked style="display: none;">
                                                        <label for="chk_program1" style="margin-top: 55px;">
                                                            <span class="labelauty-unchecked-image"></span>
                                                            <span class="labelauty-checked-image"></span>
                                                        </label>
                                                    </td>
                                                    <td width="50px;">
                                                        <input maxlength="15" value="1" type="text" name="username" placeholder="Sr. No." style="margin-top: 35px;" class="form-control" disabled="">
                                                    </td>
                                                    <td class="col-md-3 no-pad" style="padding-left: 5px !important;">
                                                        <input type="text" id="special_attaraction1" style="margin-top: 35px;" onchange="validate_spaces(this.id);" name="special_attaraction" class="form-control mg_bt_10" placeholder="Special Attraction" title="Special Attraction">
                                                    </td>
                                                    <td class='col-md-5 no-pad' style="padding-left: 5px !important;position: relative;">
                                                        <textarea id="day_program1" name="day_program" style="height:90px;" class="form-control mg_bt_10 day_program" placeholder="*Day Program" title="Day-wise Program" onchange="validate_spaces(this.id);" rows="3"></textarea>
                                                        <span class="style_text">
                                                            <span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span>
                                                            <span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span>
                                                        </span>
                                                    </td>
                                                    <td class="col-md-1/2 no-pad" style="padding-left: 5px !important;">
                                                        <input type="text" id="overnight_stay1" style="margin-top: 35px;" name="overnight_stay" onchange="validate_spaces(this.id);" class="form-control mg_bt_10" placeholder="Overnight Stay" title="Overnight Stay">
                                                    </td>
                                                    <td class="col-md-1/2 no-pad" style="padding-left: 5px !important;">
                                                        <select id="meal_plan1" title="meal plan" style="margin-top: 35px;" name="meal_plan" class="form-control mg_bt_10" data-original-title="Meal Plan">
                                                            <?php get_mealplan_dropdown(); ?>
                                                        </select>
                                                    </td>
                                                    <td class='col-md-1 pad_8'>
                                                        <button type="button" class="btn btn-info btn-iti btn-sm itinerary-btn" style="margin-top: 35px; border:none;" data-row="1" id="itinerary1" title="Add Itinerary" onClick="add_itinerary_booking_update(0,'special_attaraction1','day_program1','overnight_stay1','meal_plan1','Day-1')">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </td>
                                                    <td style="display:none">
                                                        <input type="text" name="package_id_n" value="" autocomplete="off" class="form-control" data-original-title="" title="">
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="panel panel-default panel-body app_panel_style feildset-panel">
                                <legend>Costing Details</legend>
                                <div class="row">

                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="driver_allowance1" name="driver_allowance1"
                                            placeholder="Driver Allowance" class="text-right form-control"
                                            title="Driver Allowance"
                                            onchange="calculate_total_fees(this.id, '1');validate_balance(this.id)"
                                            value="<?= $sq_enq_info['driver_allowance'] ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="permit_charges1" name="permit_charges1"
                                            class="text-right form-control" placeholder="Permit Charges"
                                            title="Permit Charges"
                                            onchange="calculate_total_fees(this.id, '1');validate_balance(this.id)"
                                            value="<?= $sq_enq_info['permit_charges'] ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="toll_and_parking1" name="toll_and_parking1"
                                            class="text-right form-control" placeholder="Toll & Parking"
                                            title="Toll & Parking"
                                            onchange="calculate_total_fees(this.id, '1');validate_balance(this.id)"
                                            value="<?= $sq_enq_info['toll_and_parking'] ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="state_entry_tax1" name="state_entry_tax1"
                                            class="text-right form-control" placeholder="State Entry"
                                            title="State Entry"
                                            onchange="calculate_total_fees(this.id, '1');validate_balance(this.id)"
                                            value="<?= $sq_enq_info['state_entry_tax'] ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="other_charges1" name="other_charges1"
                                            class="text-right form-control" placeholder="Other Charges"
                                            title="Other Charges"
                                            onchange="calculate_total_fees(this.id, '1');validate_balance(this.id)"
                                            value="<?= $sq_enq_info['other_charges'] ?>">
                                    </div>
                                </div>
                                <?php
                $basic_cost1 = $sq_enq_info['basic_amount'];
                $service_charge = $sq_enq_info['service_charge'];
                $markup = $sq_enq_info['markup_cost'];

                $bsmValues = json_decode($sq_enq_info['bsm_values']);
                $service_tax_amount = 0;
                if ($sq_enq_info['service_tax_subtotal'] !== 0.00 && ($sq_enq_info['service_tax_subtotal']) !== '') {
                    $service_tax_subtotal1 = explode(',', $sq_enq_info['service_tax_subtotal']);
                    for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
                        $service_tax = explode(':', $service_tax_subtotal1[$i]);
                        $service_tax_amount = $service_tax_amount + $service_tax[2];
                    }
                }
                $markupservice_tax_amount = 0;
                if ($sq_enq_info['markup_cost_subtotal'] !== 0.00 && $sq_enq_info['markup_cost_subtotal'] !== "") {
                    $service_tax_markup1 = explode(',', $sq_enq_info['markup_cost_subtotal']);
                    for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
                        $service_tax = explode(':', $service_tax_markup1[$i]);
                        $markupservice_tax_amount = $markupservice_tax_amount + $service_tax[2];
                    }
                }
                foreach ($bsmValues[0] as $key => $value) {
                    switch ($key) {
                        case 'basic':
                        $basic_cost = ($value != "") ? $basic_cost1 + $service_tax_amount : $basic_cost1;
                        $inclusive_b = $value;
                        break;
                        case 'service':
                        $service_charge = ($value != "") ? $service_charge + $service_tax_amount : $service_charge;
                        $inclusive_s = $value;
                        break;
                        case 'markup':
                        $markup = ($value != "") ? $markup + $markupservice_tax_amount : $markup;
                        $inclusive_m = $value;
                        break;
                    }
                }
                ?>
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <small
                                            id="basic_show1"><?= ($inclusive_b == '') ? '&nbsp;' : 'Inclusive Amount : <span>' . $inclusive_b ?></span></small>
                                        <input type="text" id="basic_amount1" name="basic_amount1"
                                            class="text-right form-control" placeholder="*Basic Amount"
                                            title="Basic Amount"
                                            onchange="calculate_total_fees(this.id, '1');validate_balance(this.id);get_auto_values('booking_date1','basic_amount1','payment_mode','service_charge1','markup_cost1','update','true','service_charge');" value="<?= $basic_cost ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <small
                                            id="service_show1"><?= ($inclusive_s == '') ? '&nbsp;' : 'Inclusive Amount : <span>' . $inclusive_s ?></span></small>
                                        <input type="text" id="service_charge1" name="service_charge1"
                                            class="text-right form-control" placeholder="Service Charge"
                                            title="Service Charge"
                                            onchange="validate_balance(this.id);get_auto_values('booking_date1','basic_amount1','payment_mode','service_charge1','markup_cost1','update','true','service_charge');" value="<?= $service_charge ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <small>&nbsp;</small>
                                        <input type="text" id="service_tax_subtotal1" name="service_tax_subtotal1"
                                            class="text-right form-control"
                                            value="<?= $sq_enq_info['service_tax_subtotal'] ?>" placeholder="Tax Amount"
                                            title="Tax Amount" readonly>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <small
                                            id="markup_show1"><?= ($inclusive_m == '') ? '&nbsp;' : 'Inclusive Amount : <span>' . $inclusive_m ?></span></small>
                                        <input type="text" id="markup_cost1" name="markup_cost1"
                                            placeholder="Markup Amount" title="Markup Amount"
                                            class="text-right form-control"
                                            onchange="validate_balance(this.id);get_auto_values('booking_date1','basic_amount1','payment_mode','service_charge1','markup_cost1','update','false','markup');"
                                            value="<?= $markup ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="service_tax_markup1" name="service_tax_markup1"
                                            class="text-right form-control" placeholder="Markup Tax" title="Markup Tax"
                                            value="<?= $sq_enq_info['markup_cost_subtotal'] ?>" readonly>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="total_cost1" name="total_cost1"
                                            class="text-right form-control" placeholder="Total" title="Total"
                                            onchange="calculate_total_fees(this.id, '1');validate_balance(this.id)"
                                            readonly value="<?= $sq_enq_info['total_cost'] ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="roundoff1" class="text-right form-control"
                                            name="roundoff1" placeholder="Round Off" title="Round Off"
                                            value="<?= $sq_enq_info['roundoff'] ?>" readonly>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="total_fees1" class="amount_feild_highlight text-right"
                                            name="total_fees1" placeholder="Net Total" title="Net Total" readonly
                                            value="<?= $sq_enq_info['total_fees'] ?>">
                                            <input type="hidden" id="old_total" value="<?= $sq_enq_info['total_fees'] ?>"/>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" name="due_date1" id="due_date1" placeholder="Due Date"
                                            title="Due Date" value="<?= get_date_user($sq_enq_info['due_date']) ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                        <input type="text" name="booking_date1" id="booking_date1"
                                            value="<?= get_date_user($sq_enq_info['created_at']) ?>"
                                            placeholder="Booking Date" title="Booking Date"
                                            onchange="check_valid_date(this.id);get_auto_values('booking_date1','basic_amount1','payment_mode','service_charge1','markup_cost1','update','false','markup');">
                                    </div>

                                     <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
                                <select name="currency_code" id="acurrency_code1" title="Currency" style="width:100%"  data-toggle="tooltip" required>
                                    <?php
                                    
                                
                                    if($sq_enq_info['currency_code'] ==''){
                                      $currency_code1 = $currency_code;
                                    }
                                    else{
                                      $currency_code1= $sq_enq_info['currency_code'];
                                    }
                        
                                    $sq_currencyd = mysqli_fetch_assoc(mysqlQuery("SELECT `id`,`currency_code` FROM `currency_name_master` WHERE id=" . $currency_code1));
                                    ?>
                                    <option value="<?=$sq_currencyd['id']?>"><?=$sq_currencyd['currency_code']?>
                                    </option>
                                    <?php
                                    $sq_currency = mysqlQuery("select * from currency_name_master order by currency_code");
                                    while ($row_currency = mysqli_fetch_assoc($sq_currency)) 
                                    {
                                    ?>
                                    <option value="<?= $row_currency['id'] ?>"><?= $row_currency['currency_code'] ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                                </div>

                            </div>

                        </div>
                    </div>


                    <div class="row text-center">
                        <div class="col-xs-12">
                            <button class="btn btn-sm btn-success" id="car_update"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Update</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

<div id="div_itinerary_modal"></div>

<style>
.style_text {
    position: absolute;
    right: 15px;
    display: flex;
    gap: 15px;
    background: #f5f5f5;
    padding: 0px 14px;
    top: 0px;
}

#booking_update_modal .modal-body {
    max-height: 120vh;
    overflow-y: auto;
    overflow-x: hidden;
}
</style>

<script>
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

$('#vendor_id1, #customer_id1,#acurrency_code1').select2();
$('#from_date1,#to_date1').datetimepicker({
    timepicker: false,
    format: 'd-m-Y'
});
$('#booking_update_modal').modal('show');

$('#from_date1,#to_date1,#total_hrs1,#total_km1,#total_max_km1,#driver_allowance1,#permit_charges1,#toll_and_parking1,#state_entry_tax1,#other_charges1,#local_places_to_visit1,#places_to_visit1,#traveling_date1')
    .hide();

$('#from_date1,#to_date,#due_date1,#booking_date1,#traveling_date1').datetimepicker({
    timepicker: false,
    format: 'd-m-Y'
});

function reflect_feilds() {
    var type = $('#travel_type1').val();

    if (type == 'Local') {
        $('#from_date1,#to_date1,#total_hrs1,#total_km1,#local_places_to_visit1').show();
        $('#total_max_km1,#driver_allowance1,#permit_charges1,#toll_and_parking1,#state_entry_tax1,#other_charges1,#places_to_visit1,#traveling_date1')
            .hide();
    }
    if (type == 'Outstation') {
        $('#total_hrs1,#total_km1,#local_places_to_visit1').hide();
        $('#from_date1,#to_date1,#total_max_km1,#driver_allowance1,#permit_charges1,#toll_and_parking1,#state_entry_tax1,#other_charges1,#places_to_visit1,#traveling_date1')
            .show();
    }
}
reflect_feilds();
$(function() {
    $('#frm_booking_update').validate({
        rules: {
            booking_date1: {
                required: true
            },
        },
        submitHandler: function(form) {

            $('#car_update').prop('disabled',true);
            var booking_id = $('#booking_id').val();
            var travel_type = $('#travel_type1').val();
            var capacity = $('#capacity1').val();
            var total_pax = $('#total_pax1').val();
            var places_to_visit = $('#places_to_visit1').val();
            var local_places_to_visit = $('#local_places_to_visit1').val();
            var from_date = $('#from_date1').val();
            var to_date = $('#to_date1').val();
            var total_hrs = $('#total_hrs1').val();
            var total_km = $('#total_km1').val();
            var rate = $('#rate1').val();
            var days_of_traveling = $('#days_of_traveling1').val();
            var customer_id = $('#customer_id1').val();
            var vehicle_name = $('#vehicle_name1').val();
            var traveling_date = $('#traveling_date1').val();
            var pass_name = $('#pass_name1').val();
            var extra_km = $('#extra_km1').val();
            var total_max_km = $('#total_max_km1').val();
            var extra_hr_cost = $('#extra_hr_cost1').val();
            var driver_allowance = $('#driver_allowance1').val();
            var permit_charges = $('#permit_charges1').val();
            var toll_and_parking = $('#toll_and_parking1').val();
            var state_entry_tax = $('#state_entry_tax1').val();
            var other_charges = $('#other_charges1').val();
            var basic_amount = $('#basic_amount1').val();
            var markup_cost = $('#markup_cost1').val();
            var markup_cost_subtotal = $('#service_tax_markup1').val();
            var service_charge = $('#service_charge1').val();
            var service_tax_subtotal = $('#service_tax_subtotal1').val();
            var total_cost = $('#total_cost1').val();
            var old_total = $('#old_total').val();
            var total_fees = $('#total_fees1').val();
            var due_date1 = $('#due_date1').val();
            var booking_date1 = $('#booking_date1').val();

            var base_url = $('#base_url').val();

            var currency_code = $('#acurrency_code1').val();

            var car_sc = $('#car_sc').val();
            var car_markup = $('#car_markup').val();
            var car_taxes = $('#car_taxes').val();
            var car_markup_taxes = $('#car_markup_taxes').val();
            var tax_apply_on = $('#atax_apply_on').val();
            var tax_value = $('#tax_value1').val();
            var markup_tax_value = $('#markup_tax_value1').val();
            var reflections = [];
            reflections.push({
                'car_sc': car_sc,
                'car_markup': car_markup,
                'car_taxes': car_taxes,
                'car_markup_taxes': car_markup_taxes,
                'tax_apply_on':tax_apply_on,
                'tax_value':tax_value,
                'markup_tax_value':markup_tax_value
            });
            var roundoff = $('#roundoff1').val();
            var bsmValues = [];
            bsmValues.push({
                "basic": $('#basic_show1').find('span').text(),
                "service": $('#service_show1').find('span').text(),
                "markup": $('#markup_show1').find('span').text()
            });

            // Collect itinerary data from all rows
            var table = document.getElementById("package_program_list");
            if(table) {
                var rowCount = table.rows.length;
                var special_attraction_arr = [];
                var day_program_arr = [];
                var stay_arr = [];
                var meal_plan_arr = [];
                var checked_programe_arr = [];

                for (var i = 0; i < rowCount; i++) {
                    var row = table.rows[i];
                    
                    console.log('Row '+i+' cells:', row.cells.length);
                    
                    // Get checkbox value
                    var checkbox = row.cells[0].querySelector('input[type="checkbox"]');
                    var isChecked = checkbox ? checkbox.checked : false;
                    checked_programe_arr.push(isChecked);
                    
                    // Get attraction value - try multiple methods
                    var attractionInput = row.cells[2].querySelector('input[name="special_attaraction"]') || 
                                         row.cells[2].querySelector('input');
                    var attraction = attractionInput ? attractionInput.value : '';
                    
                    // Get day program value
                    var dayProgramTextarea = row.cells[3].querySelector('textarea[name="day_program"]') || 
                                             row.cells[3].querySelector('textarea');
                    var dayProgram = dayProgramTextarea ? dayProgramTextarea.value : '';
                    
                    // Get stay value
                    var stayInput = row.cells[4].querySelector('input[name="overnight_stay"]') || 
                                   row.cells[4].querySelector('input');
                    var stay = stayInput ? stayInput.value : '';
                    
                    // Get meal plan value
                    var mealSelect = row.cells[5].querySelector('select[name="meal_plan"]') || 
                                    row.cells[5].querySelector('select');
                    var mealPlan = mealSelect ? mealSelect.value : '';
                    
                    console.log('Row '+i+' data:', {
                        checked: isChecked,
                        attraction: attraction,
                        dayProgram: dayProgram,
                        stay: stay,
                        mealPlan: mealPlan
                    });
                    
                    special_attraction_arr.push(attraction);
                    day_program_arr.push(dayProgram);
                    stay_arr.push(stay);
                    meal_plan_arr.push(mealPlan);
                }
                
                console.log('Update - Collecting itinerary data:', {
                    rowCount: rowCount,
                    special_attraction_arr: special_attraction_arr,
                    day_program_arr: day_program_arr,
                    stay_arr: stay_arr,
                    meal_plan_arr: meal_plan_arr,
                    checked_programe_arr: checked_programe_arr
                });
            } else {
                var special_attraction_arr = [];
                var day_program_arr = [];
                var stay_arr = [];
                var meal_plan_arr = [];
                var checked_programe_arr = [];
            }

            //Validation for booking and payment date in login financial year
            var check_date1 = $('#booking_date1').val();
            $.post(base_url + 'view/load_data/finance_date_validation.php', {
                check_date: check_date1
            }, function(data) {
                if (data !== 'valid') {
                    error_msg_alert("The Booking date does not match between selected Financial year.");
                    $('#car_update').prop('disabled',false);
                    return false;
                } else {
                    $('#car_update').button('loading');
                    $('#car_update').prop('disabled',true);

                    $.ajax({
                        type: 'post',
                        url: base_url +
                            'controller/car_rental/booking/booking_update.php',
                        data: {
                            booking_id: booking_id,
                            customer_id: customer_id,
                            total_pax: total_pax,
                            pass_name: pass_name,
                            days_of_traveling: days_of_traveling,
                            travel_type: travel_type,
                            places_to_visit: places_to_visit,
                            extra_km: extra_km,
                            service_charge: service_charge,
                            service_tax_subtotal: service_tax_subtotal,
                            total_cost: total_cost,
                            driver_allowance: driver_allowance,
                            permit_charges: permit_charges,
                            toll_and_parking: toll_and_parking,
                            state_entry_tax: state_entry_tax,
                            total_fees: total_fees,
                            due_date1: due_date1,
                            booking_date1: booking_date1,
                            capacity: capacity,
                            from_date: from_date,
                            to_date: to_date,
                            total_hrs: total_hrs,
                            total_km: total_km,
                            rate: rate,
                            total_max_km: total_max_km,
                            extra_hr_cost: extra_hr_cost,
                            other_charges: other_charges,
                            basic_amount: basic_amount,
                            markup_cost: markup_cost,
                            markup_cost_subtotal: markup_cost_subtotal,
                            vehicle_name: vehicle_name,
                            local_places_to_visit: local_places_to_visit,
                            traveling_date: traveling_date,
                            reflections: reflections,
                            roundoff: roundoff,
                            bsmValues: bsmValues,old_total:old_total,
                            currency_code:currency_code,
                            special_attraction_arr: special_attraction_arr,
                            day_program_arr: day_program_arr,
                            stay_arr: stay_arr,
                            meal_plan_arr: meal_plan_arr,
                            checked_programe_arr: checked_programe_arr
                        },
                        success: function(result) {
                            msg_popup_reload(result);
                            $('#car_update').prop('disabled',true);
                        },
                        error: function(result) {
                            console.log(result.responseText);
                        }
                    });
                }
            });


        }
    });
});

// Itinerary management functions for update modal
var count_itinerary_update = <?= ($program_count > 0) ? $program_count : 1 ?>;

// Function to add new row at the end of table (from top Add button)
function addRowUpdate(table_id) {
    count_itinerary_update++;
    var table = document.getElementById(table_id);
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
    
    // Clone the meal plan options from the first row
    var mealPlanOptions = $('#meal_plan1').html();
    
    row.innerHTML = '<td style="padding-right: 10px !important;"><input class="css-checkbox mg_bt_10 labelauty" id="chk_program'+count_itinerary_update+'" type="checkbox" checked style="display: none;"><label for="chk_program'+count_itinerary_update+'" style="margin-top: 55px;"><span class="labelauty-unchecked-image"></span><span class="labelauty-checked-image"></span></label></td>'+
        '<td><input maxlength="15" value="'+(rowCount + 1)+'" type="text" name="username" placeholder="Sr. No." style="margin-top: 35px;" class="form-control" disabled=""></td>'+
        '<td class="col-md-3 no-pad" style="padding-left: 5px !important;"><input type="text" id="special_attaraction'+count_itinerary_update+'" style="margin-top: 35px;" onchange="validate_spaces(this.id);" name="special_attaraction" class="form-control mg_bt_10" placeholder="Special Attraction" title="Special Attraction"></td>'+
        '<td class="col-md-5 no-pad" style="padding-left: 5px !important;position: relative;"><textarea id="day_program'+count_itinerary_update+'" name="day_program" style="height:90px;" class="form-control mg_bt_10 day_program" placeholder="*Day Program" title="Day-wise Program" onchange="validate_spaces(this.id);" rows="3"></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span></td>'+
        '<td class="col-md-1/2 no-pad" style="padding-left: 5px !important;"><input type="text" id="overnight_stay'+count_itinerary_update+'" style="margin-top: 35px;" name="overnight_stay" onchange="validate_spaces(this.id);" class="form-control mg_bt_10" placeholder="Overnight Stay" title="Overnight Stay"></td>'+
        '<td class="col-md-1/2 no-pad" style="padding-left: 5px !important;"><select id="meal_plan'+count_itinerary_update+'" title="meal plan" style="margin-top: 35px;" name="meal_plan" class="form-control mg_bt_10">'+mealPlanOptions+'</select></td>'+
        '<td class="col-md-1 pad_8"><button type="button" class="btn btn-info btn-iti btn-sm itinerary-btn" style="margin-top: 35px; border:none;" title="Add Itinerary" onClick="add_itinerary_booking_update(0,\'special_attaraction'+count_itinerary_update+'\',\'day_program'+count_itinerary_update+'\',\'overnight_stay'+count_itinerary_update+'\',\'meal_plan'+count_itinerary_update+'\',\'Day-'+count_itinerary_update+'\')"><i class="fa fa-plus"></i></button></td>'+
        '<td style="display:none"><input type="text" name="package_id_n" value="" autocomplete="off" class="form-control"></td>';
    
    // Reinitialize labelauty for the new checkbox
    $("input[type='checkbox']").labelauty({ label: false, maximum_width: '20px' });
    
    renumber_itinerary_rows_update();
}

// Function to renumber all itinerary rows
function renumber_itinerary_rows_update(){
    var table = document.getElementById('package_program_list');
    for(var i = 0; i < table.rows.length; i++){
        table.rows[i].cells[1].childNodes[0].value = i + 1;
    }
}

// Bold and Underline text formatting for day program
$(document).on("click", ".style_text_b, .style_text_u", function() {
    var wrapper = $(this).data("wrapper");
    
    // Get the textarea element
    var textarea = $(this).parents('.style_text').siblings('.day_program')[0];
    
    // Ensure textarea exists and selectionStart/selectionEnd are supported
    var start = textarea.selectionStart;
    var end = textarea.selectionEnd;
    var selectedText = textarea.value.substring(start, end);
    
    if (selectedText) {
        // Wrap the selected text with the wrapper
        var newText = textarea.value.substring(0, start) + wrapper + selectedText + wrapper + textarea.value.substring(end);
        textarea.value = newText;
        
        // Set cursor position after the wrapped text
        textarea.selectionStart = textarea.selectionEnd = end + (wrapper.length * 2);
        textarea.focus();
    } else {
        error_msg_alert("Please select text to format!");
    }
});

// Function to open itinerary modal for booking update
function add_itinerary_booking_update(dest_id1, spa, dwp, ovs, meal, dayp) {
    var day_id = dayp.split('-');
    $('#itinerary'+day_id[1]).prop('disabled',true);
    var base_url = $('#base_url').val();
    var dest_id = $('#' + dest_id1).val();
    if (dest_id == '') {
        dest_id = 0; // Allow opening modal even without destination
    }
    $('#itinerary'+day_id[1]).button('loading');
    $.post(base_url + 'view/car_rental/booking/itinerary_modal.php', { dest_id: dest_id, spa: spa, dwp: dwp, ovs: ovs, meal: meal, dayp: dayp }, function (data) {
        $('#itinerary'+day_id[1]).button('reset');
        $('#itinerary'+day_id[1]).prop('disabled',false);
        $('#div_itinerary_modal').html(data);
    });
}

// Function to get itinerary data for booking update (also used by modal)
function get_dest_itinerary_booking(dest_id1) {
    var base_url = $('#base_url').val();
    var dest_id = $('#' + dest_id1).val();
    if (dest_id == '' || dest_id == 0) {
        error_msg_alert('Please select destination!');
        $('#itinerary_data').html('');
        return false;
    }
    $.post(base_url + 'view/car_rental/booking/get_itinerary_data.php', { dest_id: dest_id }, function (data) {
        $('#itinerary_data').html(data);
    });
}

// Alias for consistency
function get_dest_itinerary_booking_update(dest_id1) {
    return get_dest_itinerary_booking(dest_id1);
}
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>