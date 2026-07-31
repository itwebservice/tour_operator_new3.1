<style>
    #ui-datepicker-div {
        display: none !important;
    }
</style>
<form id="frm_tab3">
    <div class="app_panel" style="overflow:hidden;">

        <div class="container" style="width:100% !important;">
            <input type="hidden" name="pckg_id_arr" id="pckg_id_arr" />
            <input type="hidden" name="pckg_day_id_arr" id="pckg_day_id_arr" />
            <input type="hidden" name="pckg_img_arr" id="pckg_img_arr" />
            <div class="row">
                <div class="col-md-12 app_accordion">
                    <div class="panel-group main_block" id="accordion" role="tablist" aria-multiselectable="true">

                        <!-- Hotel Information -->
                        <div class="accordion_content main_block mg_bt_10">

                            <div class="panel panel-default main_block">
                                <div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
                                    <div class="Normal main_block" role="button" data-toggle="collapse"
                                        data-parent="#accordion" href="#collapse4" aria-expanded="true"
                                        aria-controls="collapse4" id="collapsed4">
                                        <div class="col-md-12"><span>Hotel Information</span></div>
                                    </div>
                                </div>
                                <div id="collapse4" class="panel-collapse in collapse main_block" role="tabpanel"
                                    aria-labelledby="heading4">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-6 mg_bt_20_sm_xs">
                                                <div style="display:flex; align-items:center; gap:8px;">
                                                    <button type="button" class="btn btn-excel btn-sm hidden" title="Add Hotel" onclick="hotel_save_modal()">
                                                    <i class="fa fa-plus"></i>
                                                </button> 
                                                <select id="package_type" name="package_type"
                                                    class="form-control"
                                                    style="width:160px; text-align-last:center; -moz-text-align-last:center; -ms-text-align-last:center;"
                                                    title="Select Package Type" onchange="syncPackageType(this)">
                                                    <?php echo get_package_type_dropdown(); ?>
                                                </select>

                                                <button type="button" id="addHotelInfobtnsubmit" class="btn btn-excel btn-sm" title="Add Hotel Row"
                                                    onClick="addHotelInfo('tbl_package_tour_quotation_dynamic_hotel');city_lzloading('.city_name1');">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                                </div>
                                            </div>

                                            <div class="col-xs-6 mg_bt_20_sm_xs"
                                                style="display:flex; justify-content:flex-end; align-items:center; gap:8px;">

                                                <button type="button" id="addHotelInfoSingleRowbtnsubmit" class="btn btn-excel btn-sm"
                                                    title="Add Single Hotel Row">
                                                    <i class="fa fa-plus"></i>
                                                </button>

                                            
                                         

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="table-responsive">
                                                    <table id="tbl_package_tour_quotation_dynamic_hotel"
                                                        name="tbl_package_tour_quotation_dynamic_hotel"
                                                        class="table mg_bt_0 table-bordered mg_bt_10">
                                                        <tr>
                                                            <td><input class="css-checkbox" id="chk_hotel1" type="checkbox" checked><label class="css-label" for="chk_hotel1"></label></td>

                                                            <td><input maxlength="15" value="1" type="text"
                                                                    name="username" placeholder="Sr. No."
                                                                    class="form-control" disabled /></td>
                                                            <td class="package_type_td"><select id="package_type-1" name="package_type-1"
                                                                    class="form-control app_select2 package_type_select" style="width:160px"
                                                                    title="Select Package Type">
                                                                    <?php echo get_package_type_dropdown(); ?>
                                                                </select></td>
                                                            <td><select id="city_name1" name="city_name1"
                                                                    class="city_master_dropdown city_name1 form-control app_select2"
                                                                    style="width:160px" title="Select City Name" data-add-new-option="true"
                                                                    onchange="hotel_name_list_load(this.id);">
                                                                </select></td>
                                                            <td><select id="hotel_name-1" name="hotel_name-1"
                                                                    onchange="hotel_type_load(this.id);get_hotel_cost();"
                                                                    class="form-control app_select2" style="width:160px"
                                                                    title="Select Hotel Name" data-add-new-option="true">
                                                                    <option value="">*Hotel Name</option>
                                                                </select></td>
                                                            <td><select name="room_cat-1" id="room_cat-1"
                                                                    style="width:145px;" title="Room Category"
                                                                    class="form-control app_select2"
                                                                    onchange="get_hotel_cost();" data-add-new-option="true">    
                                                                    <option value="">Room Category</option>
                                                                </select>
                                                            </td>
                                                            <td><input type="text" style="width:150px;"
                                                                    class="app_datepicker" id="check_in-1"
                                                                    name="check_in-1" placeholder="*Check-In Date"
                                                                    title="Check-In Date"
                                                                    onchange="get_auto_to_date(this.id);get_hotel_cost();">
                                                            </td>
                                                            <td><input type="text" style="width:150px;"
                                                                    class="app_datepicker" id="check_out-1"
                                                                    name="check_out-1" placeholder="*Check-Out Date"
                                                                    title="Check-Out Date"
                                                                    onchange="calculate_total_nights(this.id);validate_validDates(this.id);get_hotel_cost();">
                                                            </td>
                                                            <td><input type="text" id="hotel_type-1" name="hotel_type-1"
                                                                    placeholder="Hotel Category" title="Hotel Category"
                                                                    style="width:150px" readonly></td>
                                                            <td class="hidden"><input type="text" id="hotel_stay_days-1"
                                                                    title="Total Nights" name="hotel_stay_days-1"
                                                                    placeholder="Total Nights"
                                                                    onchange="validate_balance(this.id);"
                                                                    style="display:none;"></td>
                                                            <td><input type="text" id="no_of_rooms-1"
                                                                    title="Total Rooms" name="no_of_rooms-1"
                                                                    placeholder="*Total Rooms"
                                                                    onchange="validate_balance(this.id);get_hotel_cost();"
                                                                    style="width:110px"></td>
                                                            <td><input type="text" id="extra_bed-1" name="extra_bed-1"
                                                                    title="Extra Bed" placeholder="Extra Bed"
                                                                    onchange="validate_balance(this.id);get_hotel_cost();"
                                                                    style="width:100px"></td>
                                                            <td class="hidden"><input type="text" id="package_name1"
                                                                    name="package_name1" placeholder="Package Name"
                                                                    title="Package Name" style="width:200px;display:none;" readonly></td>
                                                            <td class="hidden"><input type="text" id="hotel_cost1"
                                                                    name="hotel_cost1" placeholder="Hotel Cost"
                                                                    title="Hotel Cost" style="display: none"
                                                                    onchange="validate_balance(this.id)"></td>
                                                            <td class="hidden"><input type="text" id="package_id1"
                                                                    name="package_id1" placeholder="Package ID"
                                                                    title="Package ID" style="display:none;"></td>
                                                            <td class="hidden"><input type="text" id="extra_bed_cost-1"
                                                                    name="extra_bed_cost-1" placeholder="Extra bed cost"
                                                                    title="Extra bed cost" style="display: none"
                                                                    onchange="validate_balance(this.id)"></td>
                                                            <td><select name="meal_plan1" id="meal_plan1"
                                                                    style="width:145px;" title="Meal Plan"
                                                                    class="form-control app_select2" onchange="get_hotel_cost();"><?php get_mealplan_dropdown(); ?></select></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Transport Information -->
                        <div class="accordion_content main_block mg_bt_10">

                            <div class="panel panel-default main_block">
                                <div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
                                    <div class="Normal main_block" role="button" data-toggle="collapse"
                                        data-parent="#accordion" href="#collapse5" aria-expanded="true"
                                        aria-controls="collapse5" id="collapsed5">
                                        <div class="col-md-12"><span>Transport Information</span></div>
                                    </div>
                                </div>
                                <div id="collapse5" class="panel-collapse in main_block" role="tabpanel"
                                    aria-labelledby="heading5" aria-expanded="true">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-6 mg_bt_20_sm_xs">
                                                <button type="button" class="btn btn-excel hidden" title="Add Vehicle" onclick="vehicle_save_modal('transport_vehicle-')"><i class="fa fa-plus"></i></button>
                                                <button type="button" class="btn btn-excel btn-sm" title="Add Airport" onclick="airport_airline_save_modal()"><i class="fa fa-plus"></i></button>
                                            </div>
                                            <div class="col-xs-6 text-right mg_bt_20_sm_xs">
                                                <button type="button" class="btn btn-excel btn-sm"
                                                    onClick="addRow('tbl_package_tour_quotation_dynamic_transport');destinationLoading('.pickup_from', 'Pickup Location');destinationLoading('.drop_to', 'Drop-off Location');"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="table-responsive">
                                                    <table id="tbl_package_tour_quotation_dynamic_transport"
                                                        name="tbl_package_tour_quotation_dynamic_transport"
                                                        class="table mg_bt_0 table-bordered mg_bt_10">
                                                        <tr>
                                                            <td><input class="css-checkbox" id="chk_transport-" type="checkbox" onchange="get_transport_cost();" readonly><label class="css-label" for="chk_transport-"> </label></td>
                                                            <td><input maxlength="15" value="1" type="text"
                                                                    name="username" placeholder="Sr. No."
                                                                    class="form-control" disabled /></td>
                                                            <td><select id="transport_vehicle-"
                                                                    name="transport_vehicle-" title="Select Transport"
                                                                    onchange="get_transport_cost();"
                                                                    class="form-control app_select2"
                                                                    style="width:200px" data-add-new-option="true">
                                                                    <option value="">Select Vehicle</option>
                                                                    <?php
                                                                    $sq_query = mysqlQuery("select * from b2b_transfer_master where status != 'Inactive' order by vehicle_name asc");
                                                                    while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>
                                                                        <option
                                                                            value="<?php echo $row_dest['entry_id']; ?>">
                                                                            <?php echo $row_dest['vehicle_name']; ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select></td>
                                                            <td><input type="text" id="transport_start_date-"
                                                                    name="transport_start_date-"
                                                                    placeholder="Start Date" title="Start Date"
                                                                    class="app_datepicker" style="width:150px"
                                                                    onchange="get_to_date(this.id,'transport_end_date-');get_transport_cost();">
                                                            </td>
                                                            <td><input type="text" id="transport_end_date-"
                                                                    name="transport_end_date-" placeholder="End Date"
                                                                    title="End Date" class="app_datepicker"
                                                                    style="width:150px"
                                                                    onchange="validate_validDate('transport_start_date-','transport_end_date-');">
                                                            </td>
                                                            <td><select name="pickup_from-" id="pickup_from-"
                                                                    data-toggle="tooltip" style="width:250px;"
                                                                    title="Pickup Location"
                                                                    class="form-control app_select2 pickup_from"
                                                                    onchange="get_transport_cost();">
                                                                </select></td>
                                                            <td><select name="drop_to-" id="drop_to-"
                                                                    style="width:250px;" data-toggle="tooltip"
                                                                    title="Drop-off Location"
                                                                    class="form-control app_select2 drop_to"
                                                                    onchange="get_transport_cost();">
                                                                </select></td>
                                                            <td><select name="duration-" id="duration-" style="width:170px;" title="*Service Duration" data-toggle="tooltip" class="form-control app_select2" onchange="get_transport_cost();">
                                                                    <option value="">*Service Duration</option>
                                                                    <?php echo get_service_duration_dropdown(); ?>
                                                                </select></td>
                                                            <td><input type="text" id="no_vehicles-" name="no_vehicles-"
                                                                    placeholder="*No.Of vehicles" title="No.Of vehicles"
                                                                    style="width:150px"
                                                                    onchange="get_transport_cost();"></td>
                                                            <td style="display:none;"><input type="text"
                                                                    id="transport_cost-" name="transport_cost-"
                                                                    placeholder="Cost" title="Cost" style="width:150"
                                                                    style="display:none;"></td>
                                                            <td style="display:none;"><input type="text"
                                                                    id="package_name-" name="package_name-"
                                                                    placeholder="Package Name" title="Package Name"
                                                                    style="width:200px" style="display:none;" readonly>
                                                            </td>
                                                            <td><input type="text" id="package_id-" name="package_id-"
                                                                    placeholder="Package ID" title="Package ID"
                                                                    style="display:none;"></td>
                                                            <td><input type="hidden" id="pickup_type-"
                                                                    name="pickup_type-" style="display:none;"></td>
                                                            <td><input type="hidden" id="drop_type" name="drop_type"
                                                                    style="display:none;"></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Flight Information -->
                        <div class="accordion_content main_block mg_bt_10 <?= $hide_flight ?>">

                            <div class="panel panel-default main_block">
                                <div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
                                    <div class="Normal main_block" role="button" data-toggle="collapse"
                                        data-parent="#accordion" href="#collapse2" aria-expanded="true"
                                        aria-controls="collapse2" id="collapsed2">
                                        <div class="col-md-12"><span>Flight Information</span></div>
                                    </div>
                                </div>
                                <div id="collapse2" class="panel-collapse in main_block" role="tabpanel"
                                    aria-labelledby="heading2" aria-expanded="true">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-6 mg_bt_20_sm_xs">
                                                <button type="button" class="btn btn-excel btn-sm hidden" title="Add Airport/Airline" onclick="airport_airline_save_modal()"><i class="fa fa-plus"></i></button>
                                            </div>
                                            <div class="col-xs-6 text-right mg_bt_20_sm_xs">
                                                <button type="button" class="btn btn-excel btn-sm"
                                                    onClick="addRow('tbl_package_tour_quotation_dynamic_plane')"><i
                                                        class="fa fa-plus"></i></button>
                                                <button type="button" class="btn btn-pdf btn-sm"
                                                    onClick="deleteRow('tbl_package_tour_quotation_dynamic_plane')"><i
                                                        class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="table-responsive">
                                                    <table id="tbl_package_tour_quotation_dynamic_plane"
                                                        name="tbl_package_tour_quotation_dynamic_plane"
                                                        class="table mg_bt_0 table-bordered pd_bt_51">
                                                        <tr>
                                                            <td><input class="css-checkbox" id="chk_plan1"
                                                                    type="checkbox"><label class="css-label"
                                                                    for="chk_plan1"> <label></td>
                                                            <td><input maxlength="15" value="1" type="text"
                                                                    name="username" placeholder="Sr. No."
                                                                    class="form-control" disabled /></td>
                                                            <td style="width: 300px;" class="sector-select"><select name="from_sector-1" id="from_sector-1"
                                                                    class="form-control app_select2 plane-airport-select"
                                                                    data-sector-type="from" title="From Sector"
                                                                     data-add-new-option="true">
                                                                    <option value="">*From Sector</option>
                                                                </select>
                                                            </td>
                                                            <td style="width: 300px;" class="sector-select"><select name="to_sector-1" id="to_sector-1"
                                                                    class="form-control app_select2 plane-airport-select"
                                                                    data-sector-type="to" title="To Sector"
                                                                     data-add-new-option="true">
                                                                    <option value="">*To Sector</option>
                                                                </select>
                                                            </td>
                                                            <td><select id="airline_name1"
                                                                    class="app_select2 form-control"
                                                                    name="airline_name1" title="Airline Name"
                                                                    style="width: 120px;" data-add-new-option="true">
                                                                    <option value="">Airline Name</option>
                                                                    <?php get_airline_name_dropdown(); ?>
                                                                </select></td>
                                                            <td><select name="plane_class" id="plane_class1"
                                                                    title="Class" style="width: 170px !important;">
                                                                    <?php get_flight_class_dropdown(); ?>
                                                                </select></td>
                                                            <td><input type="text" id="txt_dapart1" name="txt_dapart"
                                                                    class="app_datetimepicker"
                                                                    placeholder="*Departure Date and time"
                                                                    title="Departure Date and time"
                                                                    onchange="get_to_datetime(this.id,'txt_arrval1')"
                                                                    value="<?= date('d-m-Y H:i') ?>"
                                                                    style="width: 150px;" /></td>
                                                            <td><input type="text" id="txt_arrval1" name="txt_arrval"
                                                                    class="app_datetimepicker"
                                                                    placeholder="*Arrival Date and time"
                                                                    title="Arrival Date and time"
                                                                    value="<?= date('d-m-Y H:i') ?>"
                                                                    style="width: 150px;"
                                                                    onchange="validate_validDatetime('txt_dapart1',this.id)" />
                                                            </td>
                                                            <td><input type="hidden" id="from_city-1"></td>
                                                            <td><input type="hidden" id="to_city-1"></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Information -->
                        <div class="accordion_content main_block mg_bt_10">

                            <div class="panel panel-default main_block">
                                <div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
                                    <div class="Normal main_block" role="button" data-toggle="collapse"
                                        data-parent="#accordion" href="#collapse6" aria-expanded="true"
                                        aria-controls="collapse6" id="collapsed6">
                                        <div class="col-md-12"><span>Activity Information</span></div>
                                    </div>
                                </div>
                                <div id="collapse6" class="panel-collapse in main_block" role="tabpanel"
                                    aria-labelledby="heading6" aria-expanded="true">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-6 mg_bt_20_sm_xs">
                                                <button type="button" class="btn btn-excel btn-sm" title="Add Activity" onclick="activity_save_modal()"><i class="fa fa-plus"></i></button>
                                            </div>
                                            <div class="col-xs-6 text-right mg_bt_20_sm_xs">
                                                <button type="button" class="btn btn-excel btn-sm"
                                                    onClick="addRow('tbl_package_tour_quotation_dynamic_excursion');city_lzloading('.exc_city')"><i class="fa fa-plus"></i></button>
                                                <button type="button" class="btn btn-pdf btn-sm"
                                                    onClick="deleteRow('tbl_package_tour_quotation_dynamic_excursion')"><i
                                                        class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="table-responsive">
                                                    <table id="tbl_package_tour_quotation_dynamic_excursion"
                                                        name="tbl_package_tour_quotation_dynamic_excursion"
                                                        class="table mg_bt_0 table-bordered pd_bt_51">
                                                        <tr>
                                                            <td><input class="css-checkbox" id="chk_tour_group-1"
                                                                    type="checkbox"
                                                                    onchange="get_excursion_amount();"><label
                                                                    class="css-label" for="chk_tour_group-1"> <label>
                                                            </td>
                                                            <td style="width:10%"><input maxlength="15" value="1"
                                                                    type="text" name="username1" placeholder="Sr. No."
                                                                    class="form-control" disabled /></td>
                                                            <td><input type="text" id="exc_date-1" name="exc_date-1"
                                                                    placeholder="Activity Date & Time"
                                                                    title="Activity Date & Time"
                                                                    class="app_datetimepicker"
                                                                    value="<?= date('d-m-Y H:i') ?>" style="width:150px"
                                                                    onchange="get_excursion_amount();"></td>
                                                            <td><select id="city_name-1" class="form-control exc_city"
                                                                    name="city_name-1" title="City Name"
                                                                    style="width:150px"
                                                                    onchange="get_excursion_list(this.id);" data-add-new-option="true">
                                                                </select>
                                                            </td>
                                                            <td><select id="excursion-1"
                                                                    class="app_select2 form-control"
                                                                    title="Activity Name" name="excursion-1"
                                                                    style="width:150px"
                                                                    onchange="get_excursion_amount(this.id);">
                                                                    <option value="">*Activity Name</option>
                                                                </select></td>
                                                            <td><select name="transfer_option-1" id="transfer_option-1"
                                                                    data-toggle="tooltip"
                                                                    class="form-contrl app_select2"
                                                                    title="Transfer Option" style="width:150px"
                                                                    onchange="get_excursion_amount();">
                                                                    <option value="Private Transfer">Private Transfer
                                                                    </option>
                                                                    <option value="Without Transfer">Without Transfer
                                                                    </option>
                                                                    <option value="Sharing Transfer">Sharing Transfer
                                                                    </option>
                                                                    <option value="SIC">SIC</option>
                                                                </select></td>
                                                            <td><input type="number" id="adult-1" name="adult-1"
                                                                    placeholder="Adult(s)" title="Adult(s)"
                                                                    style="width:100px"
                                                                    onchange="get_excursion_amount();validate_balance(this.id);validate_pax_count(this.id,'Adult');">
                                                            </td>
                                                            <td><input type="number" id="child-1" name="child-1"
                                                                    placeholder="Child With-Bed" title="Child With-Bed"
                                                                    style="width:150px"
                                                                    onchange="get_excursion_amount();validate_balance(this.id);validate_pax_count(this.id,'ChildWithBed');">
                                                            </td>
                                                            <td><input type="number" id="childwo-1" name="childwo-1"
                                                                    placeholder="Child Without-Bed"
                                                                    title="Child Without-Bed" style="width:150px"
                                                                    onchange="get_excursion_amount();validate_balance(this.id);validate_pax_count(this.id,'ChildWithoutBed');">
                                                            </td>
                                                            <td><input type="number" id="infant-1" name="infant-1"
                                                                    placeholder="Infant(s)" title="Infant(s)"
                                                                    style="width:100px"
                                                                    onchange="get_excursion_amount();validate_balance(this.id);validate_pax_count(this.id,'Infant');">
                                                            </td>
                                                            <td style="display:none"><input type="text"
                                                                    id="excursion_amount-1" name="excursion_amount-1"
                                                                    placeholder="Activity Amount"
                                                                    title="Activity Amount"
                                                                    style="width:100px;display:none;"
                                                                    onchange="validate_balance(this.id);"></td>
                                                            <td style="display:none"><input type="number"
                                                                    id="adult_total-1" name="adult_total-1"
                                                                    style="width:100px;display:none;"></td>
                                                            <td style="display:none"><input type="number"
                                                                    id="child_total-1" name="child_total-1"
                                                                    style="width:100px;display:none;"></td>
                                                            <td style="display:none"><input type="number"
                                                                    id="childwo_total-1" name="childwo_total-1"
                                                                    style="width:100px;display:none;"></td>
                                                            <td style="display:none"><input type="number"
                                                                    id="infant_total-1" name="infant_total-1"
                                                                    style="width:100px;display:none;"></td>
                                                            <td><select name="vehicle_id-1" id="vehicle_id-1"
                                                                    style="width: 155px"
                                                                    class="form-control app_select2"
                                                                    title="Select Vehicle"
                                                                    onchange="get_excursion_amount();">
                                                                    <option value=''>Select Vehicle</option>
                                                                    <?php
                                                                    $sq_vehicle = mysqlQuery("select * from b2b_transfer_master where status='Active' order by vehicle_name");
                                                                    while ($row_vehicle = mysqli_fetch_assoc($sq_vehicle)) {
                                                                    ?>
                                                                        <option value="<?= $row_vehicle['entry_id'] ?>">
                                                                            <?= $row_vehicle['vehicle_name'] ?></option>
                                                                    <?php } ?>
                                                                </select></td>
                                                            <td><input type="number" id="no_vehicles-1" name="no_vehicles-1" placeholder="No.Of Vehicles" title="No.Of Vehicles" style="width:150px" onchange="get_excursion_amount();"></td>
                                                            <td style="display:none"><input type="number" id="transfer_total-1" name="transfer_total-1" style="width:100px;display:none;"></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Train Information -->
                        <div class="accordion_content main_block mg_bt_10 <?= $hide_train ?>">

                            <div class="panel panel-default main_block">
                                <div class="panel-heading main_block" role="tab" id="heading1">
                                    <div class="Normal main_block" role="button" data-toggle="collapse"
                                        data-parent="#accordion" href="#collapse1" aria-expanded="true"
                                        aria-controls="collapse1" id="collapsed1">
                                        <div class="col-md-12"><span>Train Information</span></div>
                                    </div>
                                </div>
                                <div id="collapse1" class="panel-collapse collapse main_block" role="tabpanel"
                                    aria-labelledby="heading1">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-6 mg_bt_20_sm_xs">
                                                <button type="button" class="btn btn-excel btn-sm" title="Add City" onclick="city_ssave_modal()"><i class="fa fa-plus"></i></button>
                                            </div>
                                            <div class="col-xs-6 text-right mg_bt_20_sm_xs">
                                                <button type="button" class="btn btn-excel btn-sm"
                                                    onClick="addRow('tbl_package_tour_quotation_dynamic_train');city_lzloading('.train_from','*From', true);city_lzloading('.train_to','*To', true)"><i
                                                        class="fa fa-plus"></i></button>
                                                <button type="button" class="btn btn-pdf btn-sm"
                                                    onClick="deleteRow('tbl_package_tour_quotation_dynamic_train')"><i
                                                        class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="table-responsive">
                                                    <table id="tbl_package_tour_quotation_dynamic_train"
                                                        name="tbl_package_tour_quotation_dynamic_train"
                                                        class="table mg_bt_0 table-bordered pd_bt_51 no-marg ">
                                                        <tr>
                                                            <td><input class="css-checkbox" id="chk_tour_group1"
                                                                    type="checkbox"><label class="css-label"
                                                                    for="chk_tour_group1"> <label></td>
                                                            <td><input maxlength="15" value="1" type="text"
                                                                    name="username" placeholder="Sr. No."
                                                                    class="form-control" disabled /></td>
                                                            <td class="col-md-3 no-pad"><select id="train_from_location1"
                                                                    onchange="validate_location('train_to_location1','train_from_location1');"
                                                                    class="app_select2 form-control train_from"
                                                                    name="train_from_location1" title="From Location"
                                                                    style="width:100%">
                                                                    <option value="" selected="selected">*From</option>
                                                                </select></td>
                                                            <td class="col-md-3 no-pad"><select id="train_to_location1"
                                                                    onchange="validate_location('train_from_location1','train_to_location1');"
                                                                    class="app_select2 form-control train_to"
                                                                    title="To Location" name="train_to_location1"
                                                                    style="width:100%">
                                                                    <option value="" selected="selected">*To</option>
                                                                </select></td>
                                                            <td class="col-md-2 no-pad"><select name="train_class"
                                                                    id="train_class1" title="Class" style="width:100%">
                                                                    <option value="">Class</option>
                                                                    <option value="1A">1A</option>
                                                                    <option value="2A">2A</option>
                                                                    <option value="3A">3A</option>
                                                                    <option value="FC">FC</option>
                                                                    <option value="CC">CC</option>
                                                                    <option value="SL">SL</option>
                                                                    <option value="2S">2S</option>
                                                                </select></td>
                                                            <td class="col-md-2 no-pad"><input type="text"
                                                                    id="train_departure_date"
                                                                    name="train_departure_date"
                                                                    placeholder="Departure Date and time"
                                                                    title="Departure Date and time"
                                                                    class="app_datetimepicker" style="width:100% !important;"
                                                                    onchange="get_to_datetime(this.id,'train_arrival_date')"
                                                                    value="<?= date('d-m-Y H:i') ?>">
                                                            </td>
                                                            <td class="col-md-2 no-pad"><input type="text"
                                                                    id="train_arrival_date" name="train_arrival_date"
                                                                    placeholder="Arrival Date and time"
                                                                    title="Arrival Date and time"
                                                                    class="app_datetimepicker"
                                                                    value="<?= date('d-m-Y H:i') ?>" style="width:100% !important;"
                                                                    onchange="validate_validDatetime('train_departure_date',this.id)">
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Cruise Information -->
                        <div class="accordion_content main_block mg_bt_10 <?= $hide_cruise ?>">

                            <div class="panel panel-default main_block">
                                <div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
                                    <div class="Normal main_block" role="button" data-toggle="collapse"
                                        data-parent="#accordion" href="#collapse3" aria-expanded="true"
                                        aria-controls="collapse3" id="collapsed3">
                                        <div class="col-md-12"><span>Cruise Information</span></div>
                                    </div>
                                </div>
                                <div id="collapse3" class="panel-collapse collapse main_block" role="tabpanel"
                                    aria-labelledby="heading3">
                                    <div class="panel-body">
                                        <div class="row mg_bt_10">
                                            <div class="col-md-12 text-right text_center_xs">
                                                <button type="button" class="btn btn-excel btn-sm"
                                                    onClick="addRow('tbl_dynamic_cruise_quotation')"><i
                                                        class="fa fa-plus"></i></button>
                                                <button type="button" class="btn btn-pdf btn-sm"
                                                    onClick="deleteRow('tbl_dynamic_cruise_quotation')"><i
                                                        class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                        <div class="row mg_bt_10">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table id="tbl_dynamic_cruise_quotation"
                                                        name="tbl_dynamic_cruise_quotation"
                                                        class="table table-bordered no-marg">
                                                        <tr>
                                                            <td><input class="css-checkbox" id="chk_cruise1"
                                                                    type="checkbox"><label class="css-label"
                                                                    for="chk_cruise1"><label></td>
                                                            <td><input maxlength="15" value="1" type="text"
                                                                    name="username" placeholder="Sr. No."
                                                                    class="form-control" disabled /></td>
                                                            <td><input type="text" id="cruise_departure_date"
                                                                    name="cruise_departure_date"
                                                                    placeholder="Departure Date and time"
                                                                    title="Departure Date and time"
                                                                    class="app_datetimepicker"
                                                                    onchange="get_to_datetime(this.id,'cruise_arrival_date')"
                                                                    value="<?= date('d-m-Y H:i') ?>"></td>
                                                            <td><input type="text" id="cruise_arrival_date"
                                                                    name="cruise_arrival_date"
                                                                    placeholder="Arrival Date and time"
                                                                    title="Arrival Date and time"
                                                                    class="app_datetimepicker"
                                                                    value="<?= date('d-m-Y H:i') ?>"
                                                                    onchange="validate_validDatetime('cruise_departure_date',this.id)">
                                                            </td>
                                                            <td><input type="text" id="route"
                                                                    onchange="validate_spaces(this.id);validate_decimal(this.id);"
                                                                    name="route" placeholder="*Route" title="Route">
                                                            </td>
                                                            <td><input type="text" id="cabin"
                                                                    onchange="validate_spaces(this.id);validate_decimal(this.id);"
                                                                    name="cabin" placeholder="*Cabin" title="Cabin">
                                                            </td>
                                                            <td><select id="sharing" name="sharing" style="width:100%;"
                                                                    title="Sharing">
                                                                    <option value="">Sharing</option>
                                                                    <option value="Single">Single</option>
                                                                    <option value="Double">Double</option>
                                                                    <option value="Triple Quad">Triple Quad</option>
                                                                </select></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <input type="hidden" id='exc_adult_cost' />
            <input type="hidden" id='exc_child_cost' />
            <input type="hidden" id='exc_childwo_cost' />
            <input type="hidden" id='exc_infant_cost' />
            <div class="row text-center mg_tp_30 mg_bt_30">
                <div class="col-xs-12">
                    <button class="btn btn-info btn-sm ico_left" type="button" onclick="switch_to_tab2()"><i
                            class="fa fa-arrow-left"></i>&nbsp;&nbsp;Previous</button>
                    &nbsp;&nbsp;
                    <button class="btn btn-info btn-sm ico_right">Next&nbsp;&nbsp;<i
                            class="fa fa-arrow-right"></i></button>
                </div>
            </div>
            <input type="hidden" id="hotel_pp_costing" name="hotel_pp_costing" />
            <input type="hidden" id="travel_pp_costing" name="travel_pp_costing" />
</form>
<?= end_panel() ?>

<script>
    if (typeof hotelSupplierQuickLoadUrl === 'undefined') {
        hotelSupplierQuickLoadUrl = $('#base_url').val() + 'view/package_booking/quotation/home/hotel/hotel_name_load.php';
    }
    $('#airline_name1,#room_cat1,#pickup_from-,#drop_to-,#transport_vehicle-,#excursion-1,#vehicle_id-1,#plane_class1').select2();
    if (typeof initAllAirlineSelectAddNew === 'function') {
        initAllAirlineSelectAddNew('#tbl_package_tour_quotation_dynamic_plane');
    }
    $('#cruise_departure_date,#cruise_arrival_date,#exc_date-1').datetimepicker({
        format: "d-m-Y H:i"
    });
    $('#check_in-1, #check_out-1,#transport_start_date-,#transport_end_date-').datetimepicker({
        format: 'd-m-Y',
        timepicker: false
    });
    destinationLoading("#pickup_from-", 'Pickup Location');
    destinationLoading("#drop_to-", 'Drop-off Location');
    city_lzloading('#city_name1,#city_name-1');
    city_lzloading('#train_to_location1', "*To", true);
    city_lzloading('#train_from_location1', "*From", true);
    $('#excursion-1, #transfer_option-1').select2({ width: '150px' });

    $('#hotel_name-1').select2({ width: '160px', minimumResultsForSearch: 0 });
    function initPackageQuotationMealPlanSelect(scope) {
        var $scope = scope ? $(scope) : $('#tbl_package_tour_quotation_dynamic_hotel');
        $scope.find('select[id^="meal_plan"]').each(function () {
            var $select = $(this);
            if ($select.data('select2')) {
                $select.select2('destroy');
            }
            $select.select2({ width: '145px' });
        });
    }
    window.initPackageQuotationMealPlanSelect = initPackageQuotationMealPlanSelect;

    $(document).on('keydown', '#tbl_package_tour_quotation_dynamic_hotel input[id^="extra_bed"]', function (e) {
        if (e.key !== 'Tab' || e.shiftKey) {
            return;
        }
        var $mealSelect = $(this).closest('tr').find('select[id^="meal_plan"]');
        if (!$mealSelect.length || !$mealSelect.data('select2')) {
            return;
        }
        e.preventDefault();
        $mealSelect.next('.select2-container').find('.select2-selection').focus();
    });

    $(document).on('keydown', '#tbl_package_tour_quotation_dynamic_hotel select[id^="meal_plan"] + .select2-container .select2-selection', function (e) {
        if (e.key !== 'Tab' || e.shiftKey) {
            return;
        }
        if ($(this).closest('.select2-container').hasClass('select2-container--open')) {
            return;
        }
        e.preventDefault();
        var $row = $(this).closest('tr');
        var $nextRow = $row.next('tr');
        if ($nextRow.length) {
            var $nextField = $nextRow.find('.select2-container .select2-selection').first();
            if ($nextField.length) {
                $nextField.focus();
                return;
            }
        }
        var $nextFocusable = $row.closest('table').find('input, select, textarea, button, .select2-selection')
            .filter(':visible:enabled')
            .not('[type="hidden"]')
            .not('[disabled]');
        var currentIndex = $nextFocusable.index(this);
        if (currentIndex > -1 && currentIndex + 1 < $nextFocusable.length) {
            $nextFocusable.eq(currentIndex + 1).focus();
        }
    });

    function initPackageQuotationHotelAddNew() {
        if (typeof initHotelSelectAddNew !== 'function') {
            return;
        }
        initHotelSelectAddNew('#hotel_name-1');
        initAllHotelSelectAddNew('#tbl_package_tour_quotation_dynamic_hotel');
    }
    $(function () {
        initPackageQuotationHotelAddNew();
        initPackageQuotationMealPlanSelect();
        setTimeout(initPackageQuotationHotelAddNew, 400);
        setTimeout(initPackageQuotationMealPlanSelect, 400);
    });
    if (typeof initAllRoomCategorySelectAddNew === 'function') {
        initAllRoomCategorySelectAddNew('#tbl_package_tour_quotation_dynamic_hotel');
        setTimeout(function () {
            initAllRoomCategorySelectAddNew('#tbl_package_tour_quotation_dynamic_hotel');
        }, 400);
    }
    if (typeof initAllVehicleSelectAddNew === 'function') {
        initAllVehicleSelectAddNew('#tbl_package_tour_quotation_dynamic_transport');
        initAllVehicleSelectAddNew('#tbl_package_tour_quotation_dynamic_excursion');
        setTimeout(function () {
            initAllVehicleSelectAddNew('#tbl_package_tour_quotation_dynamic_transport');
            initAllVehicleSelectAddNew('#tbl_package_tour_quotation_dynamic_excursion');
        }, 400);
    }

    // Event handler removed - using inline onchange attributes instead

    function initPackageQuotationPlaneAirports() {
        if (typeof initPlaneAirportSelect2 === 'function') {
            initPlaneAirportSelect2('#tbl_package_tour_quotation_dynamic_plane');
        } else {
            setTimeout(initPackageQuotationPlaneAirports, 200);
        }
    }
    jQuery(initPackageQuotationPlaneAirports);

    // App_accordion
    jQuery(document).ready(function() {
        jQuery(".panel-heading").click(function() {
            jQuery('#accordion .panel-heading').not(this).removeClass('isOpen');
            jQuery('#accordionl .panel-heading').not(this).removeClass('isOpen');
            jQuery(this).toggleClass('isOpen');
            jQuery(this).next(".panel-collapse").addClass('thePanel');
            jQuery('#accordion .panel-collapse').not('.thePanel').slideUp("slow");
            jQuery('#accordionl .panel-collapse').not('.thePanel').slideUp("slow");
            jQuery(".thePanel").slideToggle("slow").removeClass('thePanel');
        });
    });

    //Get Hotel Cost
    function get_hotel_cost() {

        var hotel_id_arr = [];
        var room_cat_arr = [];
        var check_in_arr = [];
        var check_out_arr = [];
        var total_nights_arr = [];
        var total_rooms_arr = [];
        var extra_bed_arr = [];
        var meal_plan_arr = [];
        var checked_arr = [];
        var package_id_arr = [];
        var child_with_bed = $('#children_with_bed').val();
        var child_without_bed = $('#children_without_bed').val();
        var adult_count = $('#total_adult').val();
        adult_count = (adult_count == '') ? 0 : adult_count;
        child_without_bed = (child_without_bed == '') ? 0 : child_without_bed;
        child_with_bed = (child_with_bed == '') ? 0 : child_with_bed;

        var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
        var rowCount = table.rows.length;
        for (var i = 0; i < rowCount; i++) {

            var row = table.rows[i];
            var hotel_id = row.cells[4].childNodes[0].value;
            var room_category = row.cells[5].childNodes[0].value;
            var check_in = row.cells[6].childNodes[0].value;
            var check_out = row.cells[7].childNodes[0].value;
            var total_nights = row.cells[9].childNodes[0].value;
            var total_rooms = row.cells[10].childNodes[0].value;
            var extra_bed = row.cells[11].childNodes[0].value;
            var package_id = row.cells[14].childNodes[0].value;
            var meal_plan = row.cells[16].childNodes[0].value;

            hotel_id_arr.push(hotel_id);
            room_cat_arr.push(room_category);
            check_in_arr.push(check_in);
            check_out_arr.push(check_out);
            total_nights_arr.push(total_nights);
            total_rooms_arr.push(total_rooms);
            extra_bed_arr.push(extra_bed);
            meal_plan_arr.push(meal_plan);
            package_id_arr.push(package_id);
            checked_arr.push(row.cells[0].childNodes[0].checked);
        }
        var base_url = $('#base_url').val();
        $.ajax({
            type: 'post',
            url: base_url + 'view/package_booking/quotation/home/hotel/get_hotel_cost.php',
            data: {
                hotel_id_arr: hotel_id_arr,
                check_in_arr: check_in_arr,
                check_out_arr: check_out_arr,
                room_cat_arr: room_cat_arr,
                total_nights_arr: total_nights_arr,
                total_rooms_arr: total_rooms_arr,
                extra_bed_arr: extra_bed_arr,
                child_with_bed: child_with_bed,
                child_without_bed: child_without_bed,
                adult_count: adult_count,
                package_id_arr: package_id_arr,
                checked_arr: checked_arr,
                meal_plan_arr: meal_plan_arr
            },
            success: function(result) {

                var hotel_arr = JSON.parse(result);
                var pp_arr = [];
                if (hotel_arr.length === 0) {

                    for (var i = 0; i < rowCount; i++) {

                        var row = table.rows[i];
                        row.cells[13].childNodes[0].value = 0;
                    }
                } else {

                    for (var i = 0; i < hotel_arr.length; i++) {

                        var row = table.rows[i];
                        if (row.cells[0].childNodes[0].checked) {

                            row.cells[13].childNodes[0].value = hotel_arr[i]['hotel_cost'];
                            pp_arr.push({
                                'hotel_cost': hotel_arr[i]['hotel_cost'],
                                'adult_cost': hotel_arr[i]['adult_cost'],
                                'child_with_bed': hotel_arr[i]['child_with_bed'],
                                'child_without_bed': hotel_arr[i]['child_without_bed'],
                                'infant_cost': hotel_arr[i]['infant_cost'] || 0,
                                'package_id': hotel_arr[i]['package_id'],
                                'flag': hotel_arr[i]['flag'],
                                'package_type': (typeof quotationGetHotelRowPackageType === 'function'
                                    ? quotationGetHotelRowPackageType(row)
                                    : (row.cells[2].childNodes[0].value)),
                                'checked': true
                            });
                        } else {
                            row.cells[13].childNodes[0].value = 0;
                            pp_arr.push({
                                'hotel_cost':0,
                                'adult_cost': 0,
                                'child_with_bed': 0,
                                'child_without_bed': 0,
                                'infant_cost': 0,
                                'package_id': hotel_arr[i]['package_id'],
                                'package_type': (typeof quotationGetHotelRowPackageType === 'function'
                                    ? quotationGetHotelRowPackageType(row)
                                    : (row.cells[2].childNodes[0].value)),
                                'checked': false
                            });
                        }
                        // Don't force checkboxes to be checked - let user control them
                        // $(row.cells[0].childNodes[0]).prop('checked', true) /* .trigger('change') */ ;
                        var $pkgSelect = $(row.cells[2].childNodes[0]);
                        if (typeof quotationIsEditablePackageTypeSelect === 'function') {
                            if (!quotationIsEditablePackageTypeSelect($pkgSelect)) {
                                $pkgSelect.prop('disabled', true);
                            }
                        } else if (!$pkgSelect.attr('data-editable-package-type')) {
                            $pkgSelect.prop('disabled', true);
                        }
                    }
                }
                if (typeof quotationEnsureEditablePackageTypeRows === 'function') {
                    quotationEnsureEditablePackageTypeRows(table);
                }
                console.log("hotel_cost"+row.cells[13].childNodes[0].value);
                //Tab-4 Per person costing
                $('#hotel_pp_costing').val(JSON.stringify(pp_arr));

                calculateCostingCardsTab3();
            }
        });
    }
    // hotel_name_list_load is provided by footer_scripts.js

    // Hotel type load function
    function hotel_type_load(id) {
        var base_url = $("#base_url").val();
        var hotel_id = $("#" + id).val();
        var count = typeof parseQuotationHotelRowSuffix === 'function'
            ? parseQuotationHotelRowSuffix(id)
            : id.substring(10);
        $.get(base_url + "view/package_booking/quotation/home/hotel/hotel_type_load.php", {
            hotel_id: hotel_id
        }, function(data) {
            $("#hotel_type-" + count).val(data);
        });
        if (typeof hotel_type_load_cate === 'function') {
            hotel_type_load_cate(id);
        }
    }

    // Event handlers for city and hotel dropdowns
    $('#tbl_package_tour_quotation_dynamic_hotel').on('change select2:select', 'select[id^="city_name"], select[name^="city_name"]', function() {
        console.log("City dropdown changed:", this.id, "Value:", $(this).val());
        hotel_name_list_load(this.id);
    });

    // Additional event handler for manual trigger
    $(document).on('change', 'select[name^="city_name"]', function() {
        if ($(this).closest('#tbl_package_tour_quotation_dynamic_hotel').length > 0) {
            console.log("Manual city dropdown change detected:", this.id, "Value:", $(this).val());
            hotel_name_list_load(this.id);
        }
    });

    $('#tbl_package_tour_quotation_dynamic_hotel').on('change', 'select[name^="hotel_name"]', function() {
        hotel_type_load(this.id);
    });
    //Get Transport Cost
    function get_transport_cost() {

        var transport_id_arr = [];
        var travel_date_arr = [];
        var pickup_arr = [];
        var drop_arr = [];
        var pickup_id_arr = [];
        var drop_id_arr = [];
        var vehicle_count_arr = [];
        var ppackage_id_arr = [];
        var ppackage_name_arr = [];
        var service_duration_arr = [];
        var table = document.getElementById("tbl_package_tour_quotation_dynamic_transport");

        var rowCount = table.rows.length;
        for (var i = 0; i < rowCount; i++) {

            var row = table.rows[i];
            var transport_id = row.cells[2].childNodes[0].value;
            var travel_date = row.cells[3].childNodes[0].value;
            var pickup = row.cells[5].childNodes[0].value;
            var drop = row.cells[6].childNodes[0].value;
            var pickup1 = pickup.split("-")[1];
            var drop1 = drop.split("-")[1];

            var pickup_type = pickup.split("-")[0];
            var drop_type = drop.split("-")[0];
            var service_duration = row.cells[7].childNodes[0].value;
            var vehicle_count = row.cells[8].childNodes[0].value;
            var pname = row.cells[10].childNodes[0].value;
            var pid = row.cells[11].childNodes[0].value;

            transport_id_arr.push(transport_id);
            travel_date_arr.push(travel_date);
            pickup_arr.push(pickup1);
            drop_arr.push(drop1);
            pickup_id_arr.push(pickup_type);
            drop_id_arr.push(drop_type);
            vehicle_count_arr.push(vehicle_count);
            ppackage_id_arr.push(pid);
            ppackage_name_arr.push(pname);
            service_duration_arr.push(service_duration);
        }
        $.ajax({
            type: 'post',
            url: '../hotel/get_transport_cost.php',
            data: {
                transport_id_arr: transport_id_arr,
                travel_date_arr: travel_date_arr,
                pickup_arr: pickup_arr,
                drop_arr: drop_arr,
                vehicle_count_arr: vehicle_count_arr,
                pickup_id_arr: pickup_id_arr,
                drop_id_arr: drop_id_arr,
                ppackage_id_arr: ppackage_id_arr,
                ppackage_name_arr: ppackage_name_arr,
                service_duration_arr: service_duration_arr
            },
            success: function(result) {
                var transport_arr = JSON.parse(result);
                var pp_arr = [];
                for (var i = 0; i < transport_arr.length; i++) {

                    var row = table.rows[i];
                    if (row.cells[0].childNodes[0].checked) {
                        row.cells[9].childNodes[0].value = transport_arr[i]['total_cost'];
                        pp_arr.push({
                            'total_cost': transport_arr[i]['total_cost'],
                            'package_id': transport_arr[i]['package_id'],
                            'checked': true
                        });
                    } else {
                        row.cells[9].childNodes[0].value = 0;
                        pp_arr.push({
                            'total_cost': 0,
                            'package_id': transport_arr[i]['package_id'],
                            'checked': false
                        });
                    }

                }
                console.log("transport_cost"+row.cells[9].childNodes[0].value);
                //Tab-4 Per person costing
                $('#travel_pp_costing').val(JSON.stringify(pp_arr));

                calculateCostingCardsTab3();
            }
        });
    }

    function isQuotationGroupCostingDiv() {
        var el = document.getElementById('tbl_package_tour_quotation_dynamic_costing');
        return !!(el && el.tagName === 'DIV');
    }

    function populateGroupCostingFromHotels(hotel_main_arr, hotel_per_person_arr, costingOptions) {
        costingOptions = costingOptions || {};
        if (typeof quotationPopulateGroupCostingFromHotels === 'function' &&
            quotationPopulateGroupCostingFromHotels(hotel_main_arr, costingOptions)) {
            return;
        }

        var table = document.getElementById('tbl_package_tour_quotation_dynamic_costing');
        if (!table || !table.rows) {
            return;
        }

        if (table.rows.length == 1) {
            for (var k = 1; k < table.rows.length; k++) {
                document.getElementById("tbl_package_tour_quotation_dynamic_costing").deleteRow(k);
            }
        } else {
            while (table.rows.length > 1) {
                document.getElementById("tbl_package_tour_quotation_dynamic_costing").deleteRow(
                    (table.rows.length - 1));
                table.rows.length--;
            }
        }
        if (table.rows.length != hotel_main_arr.length) {
            for (var i = 1; i < hotel_main_arr.length; i++) {
                addRow('tbl_package_tour_quotation_dynamic_costing');
            }
        }
        if (!hotel_per_person_arr || hotel_per_person_arr.length === 0) {
            var row = table.rows[0];
            row.cells[2].childNodes[1].value = 'NA';
            row.cells[3].childNodes[1].value = 0;
        }
        for (var k = 0; k < hotel_main_arr.length; k++) {
            var row = table.rows[k];
            row.cells[2].childNodes[1].value = hotel_main_arr[k]['type'];
            row.cells[3].childNodes[1].value = hotel_main_arr[k]['cost'];
        }
    }

    function setGroupExcursionCost(total_amount, rowCount) {
        if (typeof quotationSetGroupExcursionCost === 'function' &&
            quotationSetGroupExcursionCost(total_amount, rowCount)) {
            return;
        }
        var table = document.getElementById("tbl_package_tour_quotation_dynamic_costing");
        if (!table || !table.rows) {
            return;
        }
        for (var j = 0; j < rowCount; j++) {
            var row = table.rows[j];
            row.cells[5].childNodes[1].value = total_amount;
        }
    }

    function applyGroupCostingTransportTotals(unique_package_id_arr, package_type_count) {
        var transport_cost = 0;
        if (unique_package_id_arr.length && unique_package_id_arr[0]) {
            transport_cost = unique_package_id_arr[0]['transport_cost'] || 0;
        }

        if (typeof quotationApplyGroupCostingTransportTotals === 'function' &&
            quotationApplyGroupCostingTransportTotals(unique_package_id_arr, package_type_count)) {
            return;
        }

        var table = document.getElementById("tbl_package_tour_quotation_dynamic_costing");
        if (!table || !table.rows) {
            return;
        }
        for (var j = 0; j < package_type_count; j++) {
            var row = table.rows[j];
            var hotel_cost = row.cells[3].childNodes[1].value;
            row.cells[4].childNodes[1].value = transport_cost;
            row.cells[13].childNodes[1].value = parseFloat(transport_cost) + parseFloat(hotel_cost);

            var total_cost = (row.cells[11].childNodes[1].value == '') ? row.cells[11].childNodes[1].value : 0;
            var exc_cost = (row.cells[5].childNodes[1].value == '') ? row.cells[5].childNodes[1].value : 0;
            row.cells[6].childNodes[1].value = parseFloat(total_cost) + parseFloat(exc_cost);
            row.cells[13].childNodes[1].value = parseFloat(total_cost) + parseFloat(exc_cost);
            $(row.cells[6].childNodes[1]).trigger('change');
            if (typeof get_business === 'function') {
                get_business(row.cells[3].childNodes[1].id, 'true');
            }
        }
    }

    function collectGroupCostingEntries() {
        if (typeof quotationCollectGroupCostingEntries === 'function') {
            var divEntries = quotationCollectGroupCostingEntries();
            if (divEntries !== null) {
                return divEntries;
            }
        }

        var entries = [];
        var table = document.getElementById("tbl_package_tour_quotation_dynamic_costing");
        if (!table || !table.rows) {
            return entries;
        }

        for (var i = 0; i < table.rows.length; i++) {
            var row = table.rows[i];
            if (row.cells[0].childNodes[0].checked) {
                entries.push({
                    package_type_c: row.cells[2].childNodes[1].value,
                    tour_cost: row.cells[3].childNodes[1].value,
                    transport_cost: row.cells[4].childNodes[1].value,
                    excursion_cost: row.cells[5].childNodes[1].value,
                    basic_cost: row.cells[6].childNodes[1].value,
                    service_tax: row.cells[7].childNodes[1].value,
                    discount_in: row.cells[8].childNodes[3].value,
                    discount: row.cells[9].childNodes[1].value,
                    tax_apply_on: row.cells[10].childNodes[3].value,
                    tax_value: row.cells[11].childNodes[3].value,
                    service_tax_subtotal: row.cells[12].childNodes[1].value,
                    tcs: row.cells[13].childNodes[3].value,
                    tcsvalue: row.cells[14].childNodes[3].value,
                    tdsvalue: row.cells[15].childNodes[3].value,
                    total_tour_cost: row.cells[16].childNodes[3].value,
                    package_name3: row.cells[17].childNodes[1].value,
                    pkg_id: row.cells[18].childNodes[1].value
                });
            }
        }
        return entries;
    }

    function collectGroupCostingBsmValues() {
        if (typeof quotationCollectGroupCostingBsmValues === 'function') {
            var divBsm = quotationCollectGroupCostingBsmValues();
            if (divBsm !== null) {
                return divBsm;
            }
        }

        var bsmValues = [];
        var table = document.getElementById("tbl_package_tour_quotation_dynamic_costing");
        if (!table || !table.rows) {
            return bsmValues;
        }

        for (var i = 0; i < table.rows.length; i++) {
            var row = table.rows[i];
            if (row.cells[0].childNodes[0].checked) {
                bsmValues.push([{
                    "basic": 'basic',
                    "service": 'service',
                    'tax_apply_on': row.cells[10].childNodes[3].value,
                    'tax_value': row.cells[11].childNodes[3].value,
                    'tcsper': row.cells[13].childNodes[3].value,
                    'tcsvalue': row.cells[14].childNodes[3].value
                }]);
            }
        }
        return bsmValues;
    }

    $(function() {
        $('#frm_tab3').validate({
            rules: {},
            submitHandler: function(form, e) {
                if (e && typeof e.preventDefault === 'function') {
                    e.preventDefault();
                }

                // Before validation, ensure all hotel dropdowns have proper values
                ensureHotelSelections();

                var child_with_bed = $('#children_with_bed').val();
                var child_without_bed = $('#children_without_bed').val();
                var adult_count = $('#total_adult').val();
                var total_infant = $('#total_infant').val();

                //Train Info
                var table = document.getElementById("tbl_package_tour_quotation_dynamic_train");
                var rowCount = table.rows.length;

                for (var i = 0; i < rowCount; i++) {
                    var row = table.rows[i];

                    if (row.cells[0].childNodes[0].checked) {
                        var train_from_location1 = row.cells[2].childNodes[0].value;
                        var train_to_location1 = row.cells[3].childNodes[0].value;
                        var train_class = row.cells[4].childNodes[0].value;
                        var train_arrival_date = row.cells[5].childNodes[0].value;
                        var train_departure_date = row.cells[6].childNodes[0].value;

                        if (train_from_location1 == "") {
                            error_msg_alert('Enter train from location in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_train').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }

                        if (train_to_location1 == "") {
                            error_msg_alert('Enter train to location in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_train').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }

                    }
                }

                // Flight Info
                var table = document.getElementById("tbl_package_tour_quotation_dynamic_plane");
                var rowCount = table.rows.length;

                for (var i = 0; i < rowCount; i++) {
                    var row = table.rows[i];

                    if (row.cells[0].childNodes[0].checked) {

                        var plane_from_location1 = row.cells[2].childNodes[0].value;
                        var plane_to_location1 = row.cells[3].childNodes[0].value;
                        var airline_name = row.cells[4].childNodes[0].value;
                        var plane_class = row.cells[5].childNodes[0].value;
                        var dapart1 = row.cells[6].childNodes[0].value;
                        var arraval1 = row.cells[7].childNodes[0].value;
                        var plane_from_city = row.cells[8].childNodes[0].value;
                        var plane_to_city = row.cells[9].childNodes[0].value;

                        if (plane_from_location1 == "") {
                            error_msg_alert('Enter from sector in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_plane').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }

                        if (plane_to_location1 == "") {
                            error_msg_alert('Enter to sector in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_plane').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }

                        if (dapart1 == "") {
                            error_msg_alert("Departure Datetime is required in row:" + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_plane').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }

                        if (arraval1 == "") {
                            error_msg_alert('Arrival Datetime is required in row:' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_plane').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                    }
                }


                //Cruise Information
                var table = document.getElementById("tbl_dynamic_cruise_quotation");
                var rowCount = table.rows.length;

                for (var i = 0; i < rowCount; i++) {
                    var row = table.rows[i];
                    if (row.cells[0].childNodes[0].checked) {
                        var cruise_from_date = row.cells[2].childNodes[0].value;
                        var cruise_to_date = row.cells[3].childNodes[0].value;
                        var route = row.cells[4].childNodes[0].value;
                        var cabin = row.cells[5].childNodes[0].value;
                        var sharing = row.cells[6].childNodes[0].value;

                        if (cruise_from_date == "") {
                            error_msg_alert('Enter cruise Departure datetime in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_dynamic_cruise_quotation').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }

                        if (cruise_to_date == "") {
                            error_msg_alert('Enter cruise Arrival datetime  in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_dynamic_cruise_quotation').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (route == "") {
                            error_msg_alert('Enter route in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_dynamic_cruise_quotation').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (cabin == "") {
                            error_msg_alert('Enter cabin in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_dynamic_cruise_quotation').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }

                    }
                }

                //Hotel Information 
                var package_id_arr = [];
                var package_type_arr = [];
                var hotel_cost_arr = [];
                var extra_bed_cost_arr = [];
                var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
                var rowCount = table.rows.length;

                for (var i = 0; i < rowCount; i++) {

                    var row = table.rows[i];
                    if (row.cells[0].childNodes[0].checked) {

                        var package_type = typeof quotationGetHotelRowPackageType === 'function'
                            ? quotationGetHotelRowPackageType(row)
                            : ($(row.cells[2].childNodes[0]).val() || row.cells[2].childNodes[0].value);
                        var city_name = row.cells[3].childNodes[0].value;
                        // Force refresh the hotel value from Select2
                        var $hotelSelect = $(row.cells[4].childNodes[0]);
                        if ($hotelSelect.data('select2')) {
                            row.cells[4].childNodes[0].value = $hotelSelect.val();
                        }
                        var hotel_id = row.cells[4].childNodes[0].value;
                        var hotel_cat = row.cells[5].childNodes[0].value;

                        // Debug logging
                        console.log('Row ' + (i + 1) + ' - Package Type:', package_type, 'City:', city_name, 'Hotel ID:', hotel_id, 'Hotel Cat:', hotel_cat);
                        console.log('Row ' + (i + 1) + ' - Hotel dropdown options:', $(row.cells[4].childNodes[0]).html());
                        console.log('Row ' + (i + 1) + ' - Selected hotel value:', $(row.cells[4].childNodes[0]).val());
                        console.log('Row ' + (i + 1) + ' - City dropdown options:', $(row.cells[3].childNodes[0]).html());
                        console.log('Row ' + (i + 1) + ' - Selected city value:', $(row.cells[3].childNodes[0]).val());
                        var check_in = row.cells[6].childNodes[0].value;
                        var checkout = row.cells[7].childNodes[0].value;
                        var hotel_stay_days1 = row.cells[9].childNodes[0].value;
                        var total_rooms = row.cells[10].childNodes[0].value;
                        var package_name1 = row.cells[12].childNodes[0].value;
                        var hotel_cost = row.cells[13].childNodes[0].value;
                        var package_id1 = row.cells[14].childNodes[0].value;
                        var extra_bed_cost = row.cells[15].childNodes[0].value;

                        if (package_type == "" || package_type == "*Package Type") {
                            error_msg_alert('Select Package Type in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (city_name == "" || city_name == "*City Name") {
                            error_msg_alert('Select Hotel city in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (hotel_id == "" || hotel_id == "*Hotel Name") {
                            error_msg_alert('Select Hotel in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (hotel_cat == "" || hotel_cat == "*Room Category") {
                            error_msg_alert('Select Room Category in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (check_in == "") {
                            error_msg_alert('Select Check-In date in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (checkout == "") {
                            error_msg_alert('Select Check-Out date in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (hotel_stay_days1 == "") {
                            error_msg_alert('Enter Hotel total days in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (total_rooms == "") {
                            error_msg_alert('Enter Hotel total rooms in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        package_id_arr.push(package_id1);
                        package_type_arr.push(package_type);
                        extra_bed_cost_arr.push(extra_bed_cost);
                        hotel_cost_arr.push(hotel_cost);
                    }
                }
                var unique_package_type_arr = [];
                for (var ptype_i = 0; ptype_i < package_type_arr.length; ptype_i++) {
                    var cost = 0;
                    if (ptype_i == 0) {
                        unique_package_type_arr.push(package_type_arr[ptype_i]);
                    } else {
                        if (unique_package_type_arr.indexOf(package_type_arr[ptype_i]) == -1) {
                            unique_package_type_arr.push(package_type_arr[ptype_i]);
                        }
                    }
                }

                var uniquepackages = [];
                $('input[name="custom_package"]:checked').each(function() {
                    uniquepackages.push($(this).val());
                });

                var unique_package_id_arr = [];
                var hotel_main_arr = [];
                var hotel_per_person_arr = [];
                var per_person_costing = [];
                try {
                    var hotel_pp_val = $('#hotel_pp_costing').val();
                    if (hotel_pp_val) {
                        per_person_costing = JSON.parse(hotel_pp_val);
                    }
                } catch (err) {
                    per_person_costing = [];
                }
                if (!Array.isArray(per_person_costing)) {
                    per_person_costing = [];
                }

                //Creating unique package id wise array
                for (var i = 0; i < unique_package_type_arr.length; i++) {

                    var hotel_cost_total = 0;
                    var hotel_data_arr = [];
                    var checked_arr = [];
                    var hotel_cost1 = 0;
                    // if(!added){
                    for (var k = 0; k < rowCount; k++) {

                        var row = table.rows[k];
                        var hotel_cost = row.cells[13].childNodes[0].value;
                        var package_type = typeof quotationGetHotelRowPackageType === 'function'
                            ? quotationGetHotelRowPackageType(row)
                            : ($(row.cells[2].childNodes[0]).val() || row.cells[2].childNodes[0].value);
                        if (hotel_cost == '') {
                            hotel_cost = 0;
                        }

                        if (row.cells[0].childNodes[0].checked) {

                            if (package_type === unique_package_type_arr[i]) {
                                if (hotel_cost == 0) {
                                    hotel_cost1 = 0;
                                    break;
                                } else {
                                    hotel_cost1 += parseFloat(hotel_cost);
                                }
                            }
                        }
                    }
                    var adult_cost_total = 0;
                    var cwb_cost_total = 0;
                    var cwob_cost_total = 0;
                    var adult_cost_total1 = 0;
                    var cwb_cost_total1 = 0;
                    var cwob_cost_total1 = 0;
                    var hotel_perperson_data_arr = [];
                    for (var k = 0; k < per_person_costing.length; k++) {

                        if (per_person_costing[k]['checked'] === true) {
                            adult_cost_total = (parseInt(adult_count) > 0) ? parseFloat(per_person_costing[k]['adult_cost']) : 0;
                            cwb_cost_total = (parseInt(child_with_bed) > 0) ? parseFloat(per_person_costing[k]['child_with_bed']) : 0;
                            cwob_cost_total = (parseInt(child_without_bed) > 0) ? parseFloat(per_person_costing[k]['child_without_bed']) : 0;

                            adult_cost_total = (isNaN(adult_cost_total)) ? 0 : adult_cost_total;
                            cwb_cost_total = (isNaN(cwb_cost_total)) ? 0 : cwb_cost_total;
                            cwob_cost_total = (isNaN(cwob_cost_total)) ? 0 : cwob_cost_total;
                            if (per_person_costing[k]['package_type'] == unique_package_type_arr[i]) {
                                if (adult_cost_total == 0) {
                                    adult_cost_total1 = 0;
                                    cwb_cost_total1 = 0;
                                    cwob_cost_total1 = 0;
                                    // break;
                                } else {
                                    adult_cost_total1 += parseFloat(adult_cost_total);
                                    cwb_cost_total1 += parseFloat(cwb_cost_total);
                                    cwob_cost_total1 += parseFloat(cwob_cost_total);
                                }
                            }
                        }
                    }
                    hotel_per_person_arr.push({
                        'package_id': package_id1,
                        'adult_cost': adult_cost_total1,
                        'cwb_cost': cwb_cost_total1,
                        'cwob_cost': cwob_cost_total1,
                        'infant_cost': 0,
                        'type': unique_package_type_arr[i],
                        'checked': false
                    });
                    hotel_main_arr.push({
                        'id': package_id1,
                        'type': unique_package_type_arr[i],
                        'cost': parseFloat(hotel_cost1),
                        'checked': true
                    });
                }
                if (hotel_per_person_arr.length === 0) {
                    var package_type_arr = 1;
                } else {
                    var package_type_arr = hotel_per_person_arr.length;
                }
                //Group Costing
                populateGroupCostingFromHotels(hotel_main_arr, hotel_per_person_arr);
                var per_adult = [];
                var per_cwb = [];
                var per_cwob = [];
                var per_infant = [];

                // Per-person hotel totals (arrays only — UI blocks are built later, one full design per package)
                if (hotel_per_person_arr.length === 0) {
                    per_adult.push(0);
                    per_cwb.push(0);
                    per_cwob.push(0);
                    per_infant.push(0);
                } else {
                    for (var k = 0; k < hotel_per_person_arr.length; k++) {
                        per_adult.push(hotel_per_person_arr[k]['adult_cost']);
                        per_cwb.push(hotel_per_person_arr[k]['cwb_cost']);
                        per_cwob.push(hotel_per_person_arr[k]['cwob_cost']);
                        per_infant.push(hotel_per_person_arr[k]['infant_cost'] || 0);
                    }
                }
                ////////////////////Hotel End//////////////////////////
                //Transport Information
                var package_id_arr1 = [];

                var table = document.getElementById("tbl_package_tour_quotation_dynamic_transport");
                var rowCount = table.rows.length;
                for (var i = 0; i < rowCount; i++) {

                    var row = table.rows[i];
                    if (row.cells[0].childNodes[0].checked) {

                        var transport_id = row.cells[2].childNodes[0].value;
                        var travel_date = row.cells[3].childNodes[0].value;
                        var end_date = row.cells[4].childNodes[0].value;
                        var pickup = row.cells[5].childNodes[0].value;
                        var drop = row.cells[6].childNodes[0].value;
                        var service_duration = row.cells[7].childNodes[0].value;
                        var vehicle_count = row.cells[8].childNodes[0].value;
                        var vehicle_cost = row.cells[9].childNodes[0].value;
                        var pname = row.cells[10].childNodes[0].value;
                        var pid = row.cells[11].childNodes[0].value;

                        if (transport_id == "") {
                            error_msg_alert('Select Transport Vehicle in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (travel_date == "") {
                            error_msg_alert('Enter Start date in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (end_date == "") {
                            error_msg_alert('Enter End date in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (pickup == "") {
                            error_msg_alert('Select pickup location in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (drop == "") {
                            error_msg_alert('Select drop location in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (service_duration == "") {
                            error_msg_alert('Select service duration in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (vehicle_count == "") {
                            error_msg_alert('Enter vehicle count in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (vehicle_cost == "") {
                            error_msg_alert('Enter vehicle cost in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        package_id_arr1.push(pid);
                    }
                }

                var unique_package_id_arr = [];
                var transport_cost_total = 0;
                for (var k = 0; k < rowCount; k++) {

                    var row = table.rows[k];
                    var package_id1 = row.cells[11].childNodes[0].value;
                    if (row.cells[0].childNodes[0].checked) {

                        var transport_cost1 = row.cells[9].childNodes[0].value;
                        transport_cost_total = parseFloat(transport_cost_total) + parseFloat(
                            transport_cost1);
                    }
                }
                unique_package_id_arr.push({
                    package_id: uniquepackages[i],
                    transport_cost: (isNaN(transport_cost_total) ? 0 : transport_cost_total)
                });
                var total_passangers = $('#total_passangers').val();
                var per_person_tr_arr = [];
                for (var t = 0; t < unique_package_id_arr.length; t++) {
                    per_person_tr_arr.push(parseFloat(unique_package_id_arr[t]['transport_cost']) /
                        parseInt(total_passangers));
                }
                var table = document.getElementById("tbl_package_tour_quotation_adult_child");
                var rowCount = (table && table.rows) ? table.rows.length : hotel_per_person_arr.length;
                if (per_adult.length == 0) {

                    for (var j = 0; j < Math.max(rowCount, hotel_per_person_arr.length, 1); j++) {
                        per_adult.push(0);
                        per_cwb.push(0);
                        per_cwob.push(0);
                        per_infant.push(0);
                    }
                }
                for (var j = 0; j < package_type_arr; j++) {

                    var adult_cost_total1 = (per_adult[j]) ? per_adult[j] : 0;
                    var cwb_cost_total1 = (per_cwb[j]) ? per_cwb[j] : 0;
                    var cwob_cost_total1 = (per_cwob[j]) ? per_cwob[j] : 0;
                    var infant_cost_total1 = (per_infant[j]) ? per_infant[j] : 0;

                    var hadult_cost = (parseInt(adult_count) !== 0) ? per_person_tr_arr[0] : 0;
                    var child_with_bed_coste = (parseInt(child_with_bed) !== 0) ? per_person_tr_arr[0] :
                        0;
                    var child_without_bede = (parseInt(child_without_bed) !== 0) ? per_person_tr_arr[
                        0] : 0;
                    var exc_infant_coste = (parseInt(total_infant) !== 0) ? per_person_tr_arr[0] : 0;
                    // Keep hotel-only in per_* ; transfer stored on package object for PP fields
                    if (hotel_per_person_arr[j]) {
                        hotel_per_person_arr[j].transfer_adult = hadult_cost;
                        hotel_per_person_arr[j].transfer_cweb = child_with_bed_coste;
                        hotel_per_person_arr[j].transfer_cwnb = child_without_bede;
                        hotel_per_person_arr[j].transfer_infant = exc_infant_coste;
                    }
                }

                if (unique_package_id_arr.length !== 0) {
                    // per_* remain hotel-only for package-wise PP hotel fields
                }
                ////////////////// Transport End ///////////////////////
                var children_with_bed = $('#children_with_bed').val();
                var children_without_bed = $('#children_without_bed').val();
                var total_infant = $('#total_infant').val();
                var table = document.getElementById("tbl_package_tour_quotation_dynamic_excursion");
                var rowCount = table.rows.length;
                var total_amount = 0;
                var exc_adult_cost = 0;
                var exc_child_cot = 0;
                var exc_childwo_cot = 0;
                var exc_infant_cost = 0;
                var exc_transfer_cost = 0;
                for (var e = 0; e < rowCount; e++) {
                    var row = table.rows[e];
                    if (row.cells[0].childNodes[0].checked) {

                        var exc_date = row.cells[2].childNodes[0].value;
                        var city_name = row.cells[3].childNodes[0].value;
                        var excursion_name = row.cells[4].childNodes[0].value;
                        var transfer_option = row.cells[5].childNodes[0].value;

                        if (exc_date == "") {
                            error_msg_alert('Select Activity date in row' + (e + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_excursion').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (city_name == "") {
                            error_msg_alert('Select Activity city in row' + (e + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_excursion').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (excursion_name == "") {
                            error_msg_alert('Select Activity name in row' + (e + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_excursion').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (transfer_option == "") {
                            error_msg_alert('Select Transfer option in row' + (e + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_excursion').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        var e_amount = row.cells[10].childNodes[0].value;
                        total_amount = parseFloat(total_amount) + parseFloat(e_amount);
                        //For per person costing
                        exc_adult_cost = parseFloat(exc_adult_cost) + parseFloat(row.cells[11]
                            .childNodes[0].value);
                         
                        exc_child_cot = parseFloat(exc_child_cot) + parseFloat(row.cells[12].childNodes[
                            0].value);
                          
                        exc_childwo_cot = parseFloat(exc_childwo_cot) + parseFloat(row.cells[13]
                            .childNodes[0].value);
                          
                        exc_infant_cost = parseFloat(exc_infant_cost) + parseFloat(row.cells[14]
                            .childNodes[0].value);
                          
                        exc_transfer_cost = parseFloat(exc_transfer_cost) + parseFloat(row.cells[16]
                            .childNodes[0].value);
                            
                    }
                }
                //Group costing
                setGroupExcursionCost(total_amount, package_type_arr);
                //Per person costing//Adult/Child costing
                var adult_cost_total = 0;
                var cwb_cost_total = 0;
                var cwob_cost_total = 0;
                var infant_cost_total = 0;

                var hadult_cost = parseFloat(exc_adult_cost) / parseInt(adult_count);
                var child_with_bed_coste = (parseInt(child_with_bed) !== 0) ? parseFloat(
                    exc_child_cot) / parseInt(child_with_bed) : 0;
                var child_without_bede = (parseInt(child_without_bed) !== 0) ? parseFloat(
                    exc_childwo_cot) / parseInt(child_without_bed) : 0;
                var exc_infant_coste = (parseInt(total_infant) !== 0) ? parseFloat(exc_infant_cost) /
                    parseInt(total_infant) : 0;
                var exc_ftransfer_cost = (parseInt(adult_count) !== 0) ? parseFloat(exc_transfer_cost) /
                    (parseInt(adult_count) + parseInt(child_with_bed)) : 0;

                var exc_atransfer_cost = (parseInt(adult_count) !== 0) ? exc_ftransfer_cost : 0;
                var exc_cwtransfer_cost = (parseInt(child_with_bed) !== 0) ? exc_ftransfer_cost : 0;

              // ===== TOTAL COUNT =====
var final_count = 
    parseInt(adult_count) + 
    parseInt(child_with_bed) + 
    parseInt(child_without_bed) + 
    parseInt(total_infant);

if (final_count === 0) final_count = 1;

// ===== PER PERSON TRANSFER =====
var final_tranfer_cost = parseFloat(exc_ftransfer_cost) / final_count;

// ===== FINAL VALUES (applied to every package PP block below) =====
var pp_activity_adult = (hadult_cost + (parseInt(adult_count) !== 0 ? final_tranfer_cost : 0));
var pp_activity_cweb = (child_with_bed_coste + (parseInt(child_with_bed) !== 0 ? final_tranfer_cost : 0));
var pp_activity_cwnb = (child_without_bede + (parseInt(child_without_bed) !== 0 ? final_tranfer_cost : 0));
var pp_activity_infant = (exc_infant_coste + (parseInt(total_infant) !== 0 ? final_tranfer_cost : 0));

                var table = document.getElementById("tbl_package_tour_quotation_adult_child");
                var rowCount = (table && table.rows) ? table.rows.length : hotel_per_person_arr.length;
                if (per_adult.length == 0) {

                    for (var j = 0; j < Math.max(rowCount, hotel_per_person_arr.length, 1); j++) {
                        per_adult.push(0);
                        per_cwb.push(0);
                        per_cwob.push(0);
                        per_infant.push(0);
                    }
                }
                for (var j = 0; j < package_type_arr; j++) {

                    var adult_cost_total1 = (per_adult[j]) ? per_adult[j] : 0;
                    var cwb_cost_total1 = (per_cwb[j]) ? per_cwb[j] : 0;
                    var cwob_cost_total1 = (per_cwob[j]) ? per_cwob[j] : 0;
                    var infant_cost_total1 = (per_infant[j]) ? per_infant[j] : 0;
                    // Keep hotel-only costs for PP hotel fields; activity/transfer applied separately
                    if (hotel_per_person_arr[j]) {
                        hotel_per_person_arr[j].activity_adult = pp_activity_adult;
                        hotel_per_person_arr[j].activity_cweb = pp_activity_cweb;
                        hotel_per_person_arr[j].activity_cwnb = pp_activity_cwnb;
                        hotel_per_person_arr[j].activity_infant = pp_activity_infant;
                    }
                }

                applyGroupCostingTransportTotals(unique_package_id_arr, package_type_arr);

                // Populate group costing table first
                populateTab4CostingTable();

                // Then build Per Person UI last (so it is not overwritten):
                // Package → Adult/CWEB/CWNB/Infant tables → next package
                if (typeof quotationPopulatePpCostingFromHotels === 'function') {
                    var adult_count_pp = parseInt($('#total_adult').val(), 10) || 0;
                    var cwnb_count_pp = parseInt($('#children_without_bed').val(), 10) || 0;
                    var cweb_count_pp = parseInt($('#children_with_bed').val(), 10) || 0;
                    var infant_count_pp = parseInt($('#total_infant').val(), 10) || 0;
                    var total_pax_pp = adult_count_pp + cwnb_count_pp + cweb_count_pp + infant_count_pp;
                    if (total_pax_pp === 0) total_pax_pp = 1;
                    var transport_pp_opt = (typeof per_person_tr_arr !== 'undefined' && per_person_tr_arr && per_person_tr_arr[0])
                        ? (parseFloat(per_person_tr_arr[0]) || 0)
                        : 0;
                    quotationPopulatePpCostingFromHotels(hotel_per_person_arr, {
                        transport_pp: transport_pp_opt,
                        activity_adult: pp_activity_adult,
                        activity_cweb: pp_activity_cweb,
                        activity_cwnb: pp_activity_cwnb,
                        activity_infant: pp_activity_infant,
                        force: true
                    });
                } else {
                    $("#adult_activity_pp").val(pp_activity_adult.toFixed(2));
                    $("#cweb_activity_pp").val(pp_activity_cweb.toFixed(2));
                    $("#cwnb_activity_pp").val(pp_activity_cwnb.toFixed(2));
                    $("#infant_activity_pp").val(pp_activity_infant.toFixed(2));
                }

                $('.accordion_content').removeClass("indicator");
                $('#tab3_head').addClass('done');
                $('#tab4_head').addClass('active');
                $('.bk_tab').removeClass('active');
                $('#tab4').addClass('active');
                $('html, body').animate({
                    scrollTop: $('.bk_tab_head').offset().top
                }, 200);
            }
        });
    });

    function switch_to_tab2() {
        // Discard saved Tab 3/4 state so Tab 2 Next reloads fresh package data.
        sessionStorage.removeItem('hotel_table_state_tab3');
        sessionStorage.removeItem('quotation_tab4_costing_state');
        sessionStorage.removeItem('quotation_tab4_travel_cost_state');
        sessionStorage.removeItem('quotation_tab4_costing_visited');
        if (typeof quotationRestorePackageTypeDropdown === 'function') {
            quotationRestorePackageTypeDropdown();
        }

        $('#tab3_head').removeClass('active');
        $('#tab2_head').addClass('active');
        $('.bk_tab').removeClass('active');
        $('#tab2').addClass('active');
        $('html, body').animate({
            scrollTop: $('.bk_tab_head').offset().top
        }, 200);
    }

    // Function to switch to tab3 and restore packages
    function switch_to_tab3() {
        $('#tab2_head').removeClass('active');
        $('#tab3_head').addClass('active');
        $('.bk_tab').removeClass('active');
        $('#tab3').addClass('active');
        $('html, body').animate({
            scrollTop: $('.bk_tab_head').offset().top
        }, 200);

        // Restore saved packages after a short delay to ensure tab is loaded
        setTimeout(function() {
            restoreSavedPackages();
        }, 300);
    }

    // Function to restore saved packages when tab3 is loaded
    function restoreSavedPackages() {
        var savedPackages = sessionStorage.getItem('selected_packages_tab3');
        if (savedPackages) {
            try {
                var packageIds = JSON.parse(savedPackages);
                console.log('Restoring saved packages:', packageIds);

                // Check if packages are already loaded in the hotel table
                var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
                if (table && table.rows.length > 1) {
                    console.log('Hotel table already has data, skipping restoration');
                    return;
                }

                // If no data in hotel table, restore the packages
                if (packageIds.length > 0) {
                    // Simulate the addHotelInfo function call
                    addHotelInfo('tbl_package_tour_quotation_dynamic_hotel');
                }
            } catch (e) {
                console.log('Error restoring saved packages:', e);
            }
        }
    }

    // Function to save hotel table state
    function saveHotelTableState() {
        var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
        if (table && table.rows.length > 1) {
            var tableData = [];
            for (var i = 1; i < table.rows.length; i++) {
                var row = table.rows[i];
                var rowData = {
                    checked: row.cells[0].childNodes[0].checked,
                    package_type: row.cells[2].childNodes[0].value,
                    hotel_name: row.cells[4].childNodes[0].value,
                    room_category: row.cells[5].childNodes[0].value,
                    check_in: row.cells[6].childNodes[0].value,
                    check_out: row.cells[7].childNodes[0].value,
                    hotel_type: row.cells[8].childNodes[0].value,
                    total_rooms: row.cells[9].childNodes[0].value,
                    extra_bed: row.cells[10].childNodes[0].value,
                    meal_plan: row.cells[11].childNodes[0].value,
                    package_id: row.cells[14].childNodes[0].value
                };
                tableData.push(rowData);
            }
            sessionStorage.setItem('hotel_table_state_tab3', JSON.stringify(tableData));
            console.log('Hotel table state saved:', tableData.length, 'rows');
        }
    }

    // Function to restore hotel table state
    function restoreHotelTableState() {
        var savedState = sessionStorage.getItem('hotel_table_state_tab3');
        if (savedState) {
            try {
                var tableData = JSON.parse(savedState);
                var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");

                if (table && tableData.length > 0) {
                    // Clear existing rows except header
                    while (table.rows.length > 1) {
                        table.deleteRow(table.rows.length - 1);
                    }

                    // Restore each row with a single timeout to ensure all rows are created first
                    setTimeout(function() {
                        for (var i = 0; i < tableData.length; i++) {
                            addRow('tbl_package_tour_quotation_dynamic_hotel');
                        }

                        // Then restore data after all rows are created
                        setTimeout(function() {
                            for (var i = 0; i < tableData.length; i++) {
                                var row = table.rows[i + 1]; // +1 because first row is header
                                var data = tableData[i];

                                if (row && row.cells && row.cells.length > 14) {
                                    try {
                                        // Restore row data with safety checks
                                        if (row.cells[0] && row.cells[0].childNodes && row.cells[0].childNodes[0]) {
                                            row.cells[0].childNodes[0].checked = data.checked;
                                        }
                                        if (row.cells[2] && row.cells[2].childNodes && row.cells[2].childNodes[0]) {
                                            row.cells[2].childNodes[0].value = data.package_type;
                                        }
                                        if (row.cells[4] && row.cells[4].childNodes && row.cells[4].childNodes[0]) {
                                            row.cells[4].childNodes[0].value = data.hotel_name;
                                        }
                                        if (row.cells[5] && row.cells[5].childNodes && row.cells[5].childNodes[0]) {
                                            row.cells[5].childNodes[0].value = data.room_category;
                                        }
                                        if (row.cells[6] && row.cells[6].childNodes && row.cells[6].childNodes[0]) {
                                            row.cells[6].childNodes[0].value = data.check_in;
                                        }
                                        if (row.cells[7] && row.cells[7].childNodes && row.cells[7].childNodes[0]) {
                                            row.cells[7].childNodes[0].value = data.check_out;
                                        }
                                        if (row.cells[8] && row.cells[8].childNodes && row.cells[8].childNodes[0]) {
                                            row.cells[8].childNodes[0].value = data.hotel_type;
                                        }
                                        if (row.cells[9] && row.cells[9].childNodes && row.cells[9].childNodes[0]) {
                                            row.cells[9].childNodes[0].value = data.total_rooms;
                                        }
                                        if (row.cells[10] && row.cells[10].childNodes && row.cells[10].childNodes[0]) {
                                            row.cells[10].childNodes[0].value = data.extra_bed;
                                        }
                                        if (row.cells[11] && row.cells[11].childNodes && row.cells[11].childNodes[0]) {
                                            row.cells[11].childNodes[0].value = data.meal_plan;
                                        }
                                        if (row.cells[14] && row.cells[14].childNodes && row.cells[14].childNodes[0]) {
                                            row.cells[14].childNodes[0].value = data.package_id;
                                        }
                                    } catch (cellError) {
                                        console.log('Error setting cell values for row', i, ':', cellError);
                                    }
                                }
                            }
                            console.log('Hotel table state restored:', tableData.length, 'rows');
                        }, 200); // Wait for rows to be fully created

                    }, 100);
                }
            } catch (e) {
                console.log('Error restoring hotel table state:', e);
            }
        }
    }

    // Restore packages when tab3 is initially loaded
    $(document).ready(function() {
        // Check if we're on tab3 and restore packages
        if ($('#tab3').hasClass('active')) {
            setTimeout(function() {
                restoreHotelTableState();
            }, 500);
        }
    });

    function populateTab4CostingTable() {
        // Get the costing data from tab3
        var hotel_main_arr = [];
        var unique_package_type_arr = [];

        // Collect package data from hotel table
        var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
        var rowCount = table.rows.length;

        for (var i = 0; i < rowCount; i++) {
            var row = table.rows[i];
            if (row.cells[0].childNodes[0].checked) {
                var package_type = typeof quotationGetHotelRowPackageType === 'function'
                    ? quotationGetHotelRowPackageType(row)
                    : ($(row.cells[2].childNodes[0]).val() || row.cells[2].childNodes[0].value);
                var hotel_cost = parseFloat(row.cells[13].childNodes[0].value) || 0;
                var package_id = row.cells[14].childNodes[0].value;

                if (package_type && package_type !== "*Package Type") {
                    // Add to unique package types
                    if (unique_package_type_arr.indexOf(package_type) === -1) {
                        unique_package_type_arr.push(package_type);
                    }

                    // Add to hotel main array
                    hotel_main_arr.push({
                        'id': package_id,
                        'type': package_type,
                        'cost': hotel_cost,
                        'checked': true
                    });
                }
            }
        }

        // Get the tab4 costing table
        var costingTable = document.getElementById("tbl_package_tour_quotation_dynamic_costing");
        if (!costingTable) {
            console.log("Costing table not found in tab4");
            return;
        }

        if (isQuotationGroupCostingDiv()) {
            var aggregated_hotel_arr = [];
            for (var u = 0; u < unique_package_type_arr.length; u++) {
                var packageType = unique_package_type_arr[u];
                var totalCost = 0;
                var packageId = '';
                for (var h = 0; h < hotel_main_arr.length; h++) {
                    if (hotel_main_arr[h]['type'] === packageType) {
                        totalCost += parseFloat(hotel_main_arr[h]['cost']) || 0;
                        if (!packageId && hotel_main_arr[h]['id']) {
                            packageId = hotel_main_arr[h]['id'];
                        }
                    }
                }
                aggregated_hotel_arr.push({
                    id: packageId,
                    type: packageType,
                    cost: totalCost,
                    checked: true
                });
            }
            var costingOptions = {
                transport_cost: typeof quotationGetTab3TransportTotal === 'function'
                    ? quotationGetTab3TransportTotal()
                    : 0,
                excursion_cost: typeof quotationGetTab3ExcursionTotal === 'function'
                    ? quotationGetTab3ExcursionTotal()
                    : 0
            };
            populateGroupCostingFromHotels(aggregated_hotel_arr, aggregated_hotel_arr, costingOptions);
            // PP multi-package blocks are built by the Tab3 Next handler after this call
            console.log("Tab4 costing populated with", aggregated_hotel_arr.length, "package types");
            if (typeof quotationRestoreTab4CostingState === 'function') {
                quotationRestoreTab4CostingState({
                    refreshHotelCost: true,
                    refreshActivityCost: true,
                    refreshTransportCost: false
                });
            }
            return;
        }

        // Clear existing rows (except header)
        while (costingTable.rows.length > 1) {
            costingTable.deleteRow(costingTable.rows.length - 1);
        }

        // Add rows for each unique package type
        for (var i = 0; i < unique_package_type_arr.length; i++) {
            if (i > 0) { // Add row for each package type after the first one
                addRow('tbl_package_tour_quotation_dynamic_costing');
            }

            var row = costingTable.rows[i];
            var packageType = unique_package_type_arr[i];

            // Find the total cost for this package type
            var totalCost = 0;
            for (var j = 0; j < hotel_main_arr.length; j++) {
                if (hotel_main_arr[j]['type'] === packageType) {
                    totalCost += hotel_main_arr[j]['cost'];
                }
            }

            // Populate the row
            if (row.cells[2].childNodes[1]) {
                row.cells[2].childNodes[1].value = packageType; // Package Type
            }
            if (row.cells[3].childNodes[1]) {
                row.cells[3].childNodes[1].value = totalCost; // Hotel Cost
            }
            if (row.cells[5].childNodes[1]) {
                row.cells[5].childNodes[1].value = 0; // Activity Cost (default)
            }
            if (row.cells[6].childNodes[1]) {
                row.cells[6].childNodes[1].value = totalCost; // Basic Amount
            }
            if (row.cells[7].childNodes[1]) {
                row.cells[7].childNodes[1].value = 0; // Service Charge (default)
            }
            // Find the last cell in the row which should be the Total Cost
            var lastCellIndex = row.cells.length - 1;
            if (row.cells[lastCellIndex] && row.cells[lastCellIndex].childNodes[1]) {
                row.cells[lastCellIndex].childNodes[1].value = totalCost; // Total Tour Cost
            }

            // Also try setting it in cell 16 if it exists
            if (row.cells[16] && row.cells[16].childNodes[1]) {
                row.cells[16].childNodes[1].value = totalCost; // Total Tour Cost
            }

            // Trigger change events to calculate totals
            if (row.cells[3].childNodes[1]) {
                $(row.cells[3].childNodes[1]).trigger('change');
            }

            // Force calculation of total cost
            setTimeout(function() {
                quotation_cost_calculate(row.cells[3].childNodes[1].id);

                // Also manually set the total cost in the correct field
                // Find the total_tour_cost input by name pattern
                var $totalCostInput = $(row).find('input[name^="total_tour_cost"]');
                if ($totalCostInput.length > 0) {
                    $totalCostInput.val(totalCost);
                }

                // Debug: log all inputs in the row to find the correct one
                console.log('Row inputs:', $(row).find('input').map(function() {
                    return this.name + ' = ' + this.value;
                }).get());
            }, 100);
        }

        console.log("Tab4 costing table populated with", unique_package_type_arr.length, "package types");
        if (typeof quotationRestoreTab4CostingState === 'function') {
            quotationRestoreTab4CostingState({
                refreshHotelCost: true,
                refreshActivityCost: true,
                refreshTransportCost: false
            });
        }
    }


    // function addHotelInfo(tableID, quot_table = "", itinerary = "") {
    //     var base_url = $('#base_url').val();

    //     var incl_arr = new Array();
    //     var excl_arr = new Array();
    //     var package_id_arr = new Array();
    //     var rowLenth = $('#tbl_package_tour_quotation_dynamic_hotel tbody tr').length;

    //     console.log("rowLenth", rowLenth);
    //     $('input[name="custom_package"]:checked').each(function() {

    //         package_id_arr.push($(this).val());
    //         var package_id = $(this).val();
    //         //Incl & Excl
    //         var table = document.getElementById("dynamic_table_incl" + package_id);
    //         var rowCount = table.rows.length;
    //         for (var i = 0; i < rowCount; i++) {
    //             var row = table.rows[i];
    //             var inclusion = $('#inclusions' + package_id).val();
    //             var exclusion = $('#exclusions' + package_id).val();

    //             incl_arr.push(inclusion);
    //             excl_arr.push(exclusion);
    //         }

    //     });
    //     if (package_id_arr.length == 0) {
    //         error_msg_alert('Please select at least one Package!');
    //         return false;
    //     }

    //     // Save selected packages to sessionStorage for persistence
    //     sessionStorage.setItem('selected_packages_tab3', JSON.stringify(package_id_arr));

    //     // Also save the current hotel table state
    //     saveHotelTableState();

    //     var attraction_arr = new Array();
    //     var program_arr = new Array();
    //     var stay_arr = new Array();
    //     var meal_plan_arr = new Array();
    //     var package_p_id_arr = new Array();
    //     var day_count_arr = new Array();
    //     var count = 0;


    //     for (var j = 0; j < package_id_arr.length; j++) {
    //         var table = document.getElementById("dynamic_table_list_p_" + package_id_arr[j]);
    //         var rowCount = table.rows.length;
    //         for (var i = 0; i < rowCount; i++) {
    //             var row = table.rows[i];
    //             if (row.cells[0].childNodes[0].checked) {

    //                 count++;
    //                 var attraction = row.cells[2].childNodes[0].value;
    //                 var program = row.cells[3].childNodes[0].value;
    //                 var stay = row.cells[4].childNodes[0].value;
    //                 var meal_plan = row.cells[5].childNodes[0].value;
    //                 var package_id1 = row.cells[7].childNodes[0].value;

    //                 if (attraction == "") {
    //                     error_msg_alert('Special Attraction is mandatory in row' + (i + 1));
    //                     return false;
    //                 }
    //                 if (program == "") {
    //                     error_msg_alert('Daywise program is mandatory in row' + (i + 1));
    //                     return false;
    //                 }
    //                 if (stay == "") {
    //                     error_msg_alert('Overnight Stay is mandatory in row' + (i + 1));
    //                     return false;
    //                 }

    //                 var flag1 = validate_spattration(row.cells[2].childNodes[0].id);
    //                 var flag2 = validate_dayprogram(row.cells[3].childNodes[0].id);
    //                 var flag3 = validate_onstay(row.cells[4].childNodes[0].id);
    //                 if (!flag1 || !flag2 || !flag3) {
    //                     return false;
    //                 }
    //                 attraction_arr.push(attraction);
    //                 program_arr.push(program);
    //                 stay_arr.push(stay);
    //                 meal_plan_arr.push(meal_plan);
    //                 package_p_id_arr.push(package_id1);
    //             }
    //         }
    //         day_count_arr.push(count);
    //         count = 0;
    //     }

    //     var total_adult = $('#total_adult').val();
    //     var total_children = $('#total_children').val();
    //     var from_date = $('#from_date').val();
    //     var to_date = $('#to_date').val();
    //     var total_days = $('#total_days').val();


    //     $.ajax({

    //         type: 'post',

    //         url: '../save/package_hotel_info.php',

    //         data: {
    //             package_id_arr: package_id_arr,
    //             from_date: from_date
    //         },
    //         success: function(result) {
    //             var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
    //             var hotel_arr = JSON.parse(result);

    //             // Find current max row number (column 1 of existing rows) with safety checks
    //             var lastIndex = 0;
    //             for (var r = 1; r < table.rows.length; r++) {
    //                 var row = table.rows[r];
    //                 if (row && row.cells && row.cells[1] && row.cells[1].childNodes && row.cells[1].childNodes[0]) {
    //                     var val = parseInt(row.cells[1].childNodes[0].value);
    //                     if (!isNaN(val) && val > lastIndex) lastIndex = val;
    //                 }
    //             }

    //             var rowCopyFrom = table.rows;
    //             var startingRowIndex = table.rows.length; // remember table length before adding new rows

    //             // 1️⃣ Append all new rows
    //             for (var i = 0; i < hotel_arr.length; i++) {
    //                 addRow('tbl_package_tour_quotation_dynamic_hotel'); // Add new row
    //                 var rowIndex = table.rows.length - 1; // Get the new row index
    //                 var row = table.rows[rowIndex];

    //                 // Safety check for row and cells
    //                 if (!row || !row.cells || row.cells.length < 17) {
    //                     console.log('Row not properly created, skipping row', i);
    //                     continue;
    //                 }

    //                 // Continuous numbering
    //                 if (row.cells[1] && row.cells[1].childNodes && row.cells[1].childNodes[0]) {
    //                     row.cells[1].childNodes[0].value = ++lastIndex;
    //                 }

    //                 // Populate hotel dropdown with available options
    //                 if (row.cells[4] && row.cells[4].childNodes && row.cells[4].childNodes[0]) {
    //                     $(row.cells[4].childNodes[0]).html(
    //                         '<option value="">*Hotel Name</option>' +
    //                         hotel_arr.map(function(hotel) {
    //                             return `<option value="${hotel.hotel_id1}">${hotel.hotel_name}</option>`;
    //                         }).join('')
    //                     );

    //                     // Set the selected hotel based on the previous row's selection (if any)
    //                     var previousHotelId = hotel_arr[i] ? hotel_arr[i].hotel_id1 : null;
    //                     if (previousHotelId) {
    //                         $(row.cells[4].childNodes[0]).val(previousHotelId).trigger('change'); // Select the hotel
    //                     }
    //                 }

    //                 // Set room category if available
    //                 if (row.cells[5] && row.cells[5].childNodes && row.cells[5].childNodes[0]) {
    //                     var roomCatValue = '';
    //                     if (table.rows[i] && table.rows[i].cells && table.rows[i].cells[5] && table.rows[i].cells[5].childNodes && table.rows[i].cells[5].childNodes[0]) {
    //                         roomCatValue = $('#' + table.rows[i].cells[5].childNodes[0].id).val().trim();
    //                     }

    //                     if (roomCatValue && roomCatValue !== "") {
    //                         $('#' + row.cells[5].childNodes[0].id).val(roomCatValue); // Set room category
    //                     } else {
    //                         if ($('#' + row.cells[5].childNodes[0].id).find('option[value=""]').length === 0) {
    //                             $('#' + row.cells[5].childNodes[0].id).prepend('<option value="">Room Category</option>');
    //                         }
    //                     }
    //                 }

    //                 // Fill other fields (dates, package details, etc.) with safety checks
    //                 if (row.cells[6] && row.cells[6].childNodes && row.cells[6].childNodes[0]) {
    //                     row.cells[6].childNodes[0].value = hotel_arr[i]['check_in_date'];
    //                 }
    //                 if (row.cells[7] && row.cells[7].childNodes && row.cells[7].childNodes[0]) {
    //                     row.cells[7].childNodes[0].value = hotel_arr[i]['check_out_date'];
    //                 }
    //                 if (row.cells[8] && row.cells[8].childNodes && row.cells[8].childNodes[0]) {
    //                     row.cells[8].childNodes[0].value = hotel_arr[i]['hotel_type'];
    //                 }
    //                 if (row.cells[9] && row.cells[9].childNodes && row.cells[9].childNodes[0] && table.rows[i] && table.rows[i].cells && table.rows[i].cells[9] && table.rows[i].cells[9].childNodes && table.rows[i].cells[9].childNodes[0]) {
    //                     row.cells[9].childNodes[0].value = table.rows[i].cells[9].childNodes[0].value;
    //                 }
    //                 if (row.cells[10] && row.cells[10].childNodes && row.cells[10].childNodes[0] && table.rows[i] && table.rows[i].cells && table.rows[i].cells[10] && table.rows[i].cells[10].childNodes && table.rows[i].cells[10].childNodes[0]) {
    //                     row.cells[10].childNodes[0].value = table.rows[i].cells[10].childNodes[0].value;
    //                 }
    //                 if (row.cells[11] && row.cells[11].childNodes && row.cells[11].childNodes[0] && table.rows[i] && table.rows[i].cells && table.rows[i].cells[11] && table.rows[i].cells[11].childNodes && table.rows[i].cells[11].childNodes[0]) {
    //                     row.cells[11].childNodes[0].value = table.rows[i].cells[11].childNodes[0].value;
    //                 }
    //                 if (row.cells[16] && row.cells[16].childNodes && row.cells[16].childNodes[0] && rowCopyFrom[i] && rowCopyFrom[i].cells && rowCopyFrom[i].cells[16] && rowCopyFrom[i].cells[16].childNodes && rowCopyFrom[i].cells[16].childNodes[0]) {
    //                     $('#' + row.cells[16].childNodes[0].id).val(rowCopyFrom[i].cells[16].childNodes[0].value);
    //                 }
    //                 if (row.cells[12] && row.cells[12].childNodes && row.cells[12].childNodes[0]) {
    //                     row.cells[12].childNodes[0].value = hotel_arr[i]['package_name'];
    //                 }
    //                 if (row.cells[14] && row.cells[14].childNodes && row.cells[14].childNodes[0]) {
    //                     row.cells[14].childNodes[0].value = hotel_arr[i]['package_id'];
    //                 }

    //                 if (row.cells[2] && row.cells[2].childNodes && row.cells[2].childNodes[0]) {
    //                     if ($('#package_type').val() != '') {
    //                         $('#' + row.cells[2].childNodes[0].id).val($('#package_type').val());
    //                     } else {
    //                         document.getElementById(row.cells[2].childNodes[0].id).selectedIndex = 0;
    //                     }

    //                     // Init select2s for other dropdowns (except city)
    //                     $('#' + row.cells[2].childNodes[0].id).select2().trigger("change");
    //                 }

    //                 if (row.cells[4] && row.cells[4].childNodes && row.cells[4].childNodes[0]) {
    //                     $('#' + row.cells[4].childNodes[0].id).select2().trigger("change");

    //                     // After hotel selection, load hotel type
    //                     setTimeout(function() {
    //                         hotel_type_load($('#' + row.cells[4].childNodes[0].id).attr('id'));
    //                     }, 100); // Adjust timeout as needed for your setup
    //                 }

    //                 if (row.cells[5] && row.cells[5].childNodes && row.cells[5].childNodes[0]) {
    //                     $('#' + row.cells[5].childNodes[0].id).select2().trigger("change");
    //                 }

    //                 if (row.cells[16] && row.cells[16].childNodes && row.cells[16].childNodes[0]) {
    //                     $('#' + row.cells[16].childNodes[0].id).select2().trigger("change");
    //                 }

    //                 if (row.cells[7] && row.cells[7].childNodes && row.cells[7].childNodes[0]) {
    //                     calculate_total_nights(row.cells[7].childNodes[0].id);
    //                 }
    //             }

    //             // 2️⃣ Initialize city dropdowns ONLY for newly added rows (starting from startingRowIndex)
    //             for (var i = startingRowIndex; i < table.rows.length; i++) {
    //                 var row = table.rows[i];

    //                 // Safety check for row and cells
    //                 if (!row || !row.cells || row.cells.length < 4) {
    //                     console.log('Row not properly created for city initialization, skipping row', i);
    //                     continue;
    //                 }

    //                 var $citySelect = $(row.cells[3].childNodes[0]);

    //                 // Initialize city dropdown for new row
    //                 city_lzloading($citySelect);

    //                 // Get city from hotel_arr data
    //                 var cityId = hotel_arr[i - startingRowIndex] ? hotel_arr[i - startingRowIndex]['city_id'] : null;
    //                 var cityName = hotel_arr[i - startingRowIndex] ? hotel_arr[i - startingRowIndex]['city_name'] : null;

    //                 // If no city data from server, copy from previous row
    //                 if (!cityId && !cityName && i > 0) {
    //                     var prevRow = table.rows[i - 1];
    //                     var prevCitySelect = $(prevRow.cells[3].childNodes[0]);
    //                     cityId = prevCitySelect.val();
    //                     cityName = prevCitySelect.find('option:selected').text();
    //                 }

    //                 if (cityId && cityName) {
    //                     var newOption = new Option(cityName, cityId, true, true);
    //                     $citySelect.append(newOption);

    //                     // Set the value and trigger change after Select2 is initialized
    //                     $citySelect.val(cityId).trigger('change');

    //                     // Load hotel dropdown for the new row
    //                     setTimeout(function() {
    //                         var currentRow = row;
    //                         var currentCitySelect = $citySelect;

    //                         hotel_name_list_load(currentCitySelect.attr('id'));

    //                         // If we need to copy hotel from previous row
    //                         if (i > 0 && !hotel_arr[i - startingRowIndex]) {
    //                             setTimeout(function() {
    //                                 var prevRow = table.rows[i - 1];
    //                                 var prevHotelSelect = $(prevRow.cells[4].childNodes[0]);
    //                                 var prevHotelId = prevHotelSelect.val();
    //                                 var prevHotelName = prevHotelSelect.find('option:selected').text();

    //                                 if (prevHotelId && prevHotelName && prevHotelName !== "Select Hotel" && prevHotelName !== "*Hotel Name") {
    //                                     var $hotelSelect = $(currentRow.cells[4].childNodes[0]);

    //                                     // Wait a bit for hotels to load, then select
    //                                     setTimeout(function() {
    //                                         if ($hotelSelect.find('option[value="' + prevHotelId + '"]').length > 0) {
    //                                             $hotelSelect.val(prevHotelId).trigger('change');
    //                                             hotel_type_load($hotelSelect.attr('id'));
    //                                         }
    //                                     }, 300);
    //                                 }
    //                             }, 200);
    //                         }
    //                     }, 200);
    //                 }
    //             }

    //             // 3️⃣ Additional logic to copy hotels to rows that don't have hotels selected
    //             setTimeout(function() {
    //                 $('#tbl_package_tour_quotation_dynamic_hotel tbody tr').each(function(index, row) {
    //                     if (index) { // Skip first row
    //                         var $hotelSelect = $(row.cells[4].childNodes[0]);
    //                         var currentHotelId = $hotelSelect.val();
    //                         var prevRowIndex = index - rowLenth;

    //                         // If current row doesn't have a hotel selected, copy from previous row
    //                         if (!currentHotelId || currentHotelId === '') {
    //                             var prevRow = table.rows[prevRowIndex];
    //                             var prevHotelSelect = $(prevRow.cells[4].childNodes[0]);
    //                             var prevHotelId = prevHotelSelect.val();
    //                             var prevHotelName = prevHotelSelect.find('option:selected').text();


    //                             if (prevHotelId && prevHotelName) {
    //                                 // Check if the hotel option exists in the current row's options
    //                                 var hotelExists = $hotelSelect.find('option[value="' + prevHotelId + '"]').length > 0;

    //                                 if (hotelExists) {
    //                                     // Hotel exists in options, just select it
    //                                     console.log('DEBUG: Selecting existing hotel in row', index, ':', prevHotelId);
    //                                     $hotelSelect.val(prevHotelId).trigger('change');

    //                                     // Trigger hotel type load
    //                                     setTimeout(function() {
    //                                         hotel_type_load($hotelSelect.attr('id'));
    //                                     }, 100);
    //                                 } else {
    //                                     // Hotel doesn't exist in options, add it manually
    //                                     console.log('DEBUG: Adding hotel manually in row', index, ':', prevHotelId, prevHotelName);
    //                                     var hotelOption = new Option(prevHotelName, prevHotelId, true, true);
    //                                     $hotelSelect.append(hotelOption);
    //                                     $hotelSelect.val(prevHotelId).trigger('change');

    //                                     // Trigger hotel type load
    //                                     setTimeout(function() {
    //                                         hotel_type_load($hotelSelect.attr('id'));
    //                                     }, 100);
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 });
    //             }, 1000); // Wait 1 second to ensure all dropdowns are loaded

    //             // Hide hotel package if needed
    //             var selectedPackagevalue = table.rows[1] ? table.rows[1].cells[2].childNodes[0].value : '';
    //             hideHotelPackage(selectedPackagevalue);
    //         }





    //     });
    // }



    function hideHotelPackage(pkgValue) {
        if (window.quotationBatchPopulatingHotels) {
            return;
        }
        $('#package_type').find('option[value="' + pkgValue + '"]').remove();
        $('#package_type').trigger('change');
        if ($('#package_type').val() === null) {
            $('#addHotelInfobtnsubmit').prop('disabled', true);
        }
    }

    function syncPackageType(mainSelect) {
        var selectedVal = $(mainSelect).val();
        // $(mainSelect).find('option[value="' + selectedVal + '"]').prop('disabled', true);
        // $(mainSelect).find('option[value="' + selectedValue + '"]').css('display', 'none');
        // return false;

        // Update all row selects (but skip the main one itself)
        //$(".package_type_select").not(mainSelect).val(selectedVal).trigger("change.select2");
    }

    // Function to ensure all hotel dropdowns have selected values
    function ensureHotelSelections() {
        var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
        var rowCount = table.rows.length;

        for (var i = 0; i < rowCount; i++) {
            var row = table.rows[i];
            var chk = row.cells[0].childNodes[0].checked;

            if (chk) {
                var $hotelSelect = $(row.cells[4].childNodes[0]);
                var hotelValue = $hotelSelect.val();

                console.log('Ensuring hotel selection for row', i + 1, '- Current value:', hotelValue);

                // If no hotel selected but there are options available
                if ((!hotelValue || hotelValue === '') && i > 0) {
                    // Try to copy from previous row
                    var prevRow = table.rows[i - 1];
                    var $prevHotelSelect = $(prevRow.cells[4].childNodes[0]);
                    var prevHotelId = $prevHotelSelect.val();

                    if (prevHotelId && $hotelSelect.find('option[value="' + prevHotelId + '"]').length > 0) {
                        console.log('Setting hotel value to:', prevHotelId);
                        $hotelSelect.val(prevHotelId);
                        // Force the value to stick
                        row.cells[4].childNodes[0].value = prevHotelId;
                    }
                }
            }
        }
    }

    // Add exactly one hotel row dynamically
    function addHotelInfoSingleRow(tableID) {
        tableID = tableID || 'tbl_package_tour_quotation_dynamic_hotel';

        if (window.quotationAddingSingleHotelRow) {
            return false;
        }

        var selectedPackageType = $('#package_type').val();
        if (!selectedPackageType || selectedPackageType === '*Package Type') {
            error_msg_alert('Please select Package Type!');
            return false;
        }

        var table = document.getElementById(tableID);
        if (!table) {
            return false;
        }

        var rowCountBefore = table.rows.length;
        var prevRow = rowCountBefore > 0 ? table.rows[rowCountBefore - 1] : null;
        var $btn = $('#addHotelInfoSingleRowbtnsubmit');

        window.quotationAddingSingleHotelRow = true;
        $btn.prop('disabled', true);

        window.quotationFreshPackageLoad = true;
        addRow(tableID);
        window.quotationFreshPackageLoad = false;

        if (table.rows.length > rowCountBefore + 1) {
            while (table.rows.length > rowCountBefore + 1) {
                table.deleteRow(table.rows.length - 1);
            }
        }

        var newRow = table.rows[table.rows.length - 1];
        if (!newRow || !newRow.cells || newRow.cells.length < 17) {
            window.quotationAddingSingleHotelRow = false;
            $btn.prop('disabled', false);
            return false;
        }

        if (typeof quotationResetHotelRowFields === 'function') {
            quotationResetHotelRowFields(newRow, { packageType: selectedPackageType });
        }
        if (typeof quotationInitEditablePackageTypeSelect === 'function') {
            quotationInitEditablePackageTypeSelect(newRow, selectedPackageType);
        } else {
            var $pkgSelect = $(newRow.cells[2].childNodes[0]);
            $pkgSelect.prop('disabled', false)
                .attr('data-editable-package-type', '1')
                .val(selectedPackageType)
                .trigger('change.select2');
        }

        var newSrNo = 1;
        if (prevRow && prevRow.cells[1] && prevRow.cells[1].childNodes[0]) {
            newSrNo = (parseInt(prevRow.cells[1].childNodes[0].value, 10) || rowCountBefore) + 1;
        }
        if (newRow.cells[1] && newRow.cells[1].childNodes[0]) {
            newRow.cells[1].childNodes[0].value = newSrNo;
        }

        if (prevRow && typeof quotationGetHotelRowReference === 'function') {
            var ref = quotationGetHotelRowReference(prevRow);
            if (ref && ref.check_out && newRow.cells[6] && newRow.cells[6].childNodes[0]) {
                newRow.cells[6].childNodes[0].value = ref.check_out;
            }
        }

        if (typeof initAllHotelSelectAddNew === 'function') {
            initAllHotelSelectAddNew(newRow);
        }
        if (typeof initAllRoomCategorySelectAddNew === 'function') {
            initAllRoomCategorySelectAddNew(newRow);
        }
        if (typeof initPackageQuotationMealPlanSelect === 'function') {
            initPackageQuotationMealPlanSelect(newRow);
        }

        if (typeof saveHotelTableState === 'function') {
            saveHotelTableState();
        }

        window.quotationAddingSingleHotelRow = false;
        $btn.prop('disabled', false);
        return false;
    }
    window.addHotelInfoSingleRow = addHotelInfoSingleRow;

    $(document).off('click', '#addHotelInfoSingleRowbtnsubmit').on('click', '#addHotelInfoSingleRowbtnsubmit', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        addHotelInfoSingleRow('tbl_package_tour_quotation_dynamic_hotel');
        return false;
    });


function isAiQuotationActive() {
    return sessionStorage.getItem('is_ai_quotation') === '1'
        || ($('#is_ai_quotation').length && $('#is_ai_quotation').val() === '1')
        || $('#aiBuilder').is(':checked');
}

function resolveAiQuotationPackageIds() {
    var referId = sessionStorage.getItem('quotation_refer_id') || $('#quotation_refer_id').val() || '';
    if (referId && String(referId) !== '0') {
        return [String(referId)];
    }
    try {
        var itineraryData = JSON.parse(sessionStorage.getItem('itinerary_data') || '{}');
        if (itineraryData.package_id_arr && itineraryData.package_id_arr.length) {
            return itineraryData.package_id_arr.map(String);
        }
    } catch (e) {}
    var savedPackages = sessionStorage.getItem('selected_packages_tab3');
    if (savedPackages) {
        try {
            var packageIds = JSON.parse(savedPackages);
            if (packageIds.length) {
                return packageIds.map(String);
            }
        } catch (e2) {}
    }
    return [];
}

function addHotelInfo(tableID, quot_table = "", itinerary = "") {
    const base_url = $('#base_url').val();
    let incl_arr = [], excl_arr = [], package_id_arr = [];
    const rowLenth = $('#tbl_package_tour_quotation_dynamic_hotel tbody tr').length;

    console.log("rowLenth", rowLenth);

    // Collect selected package IDs and inclusions/exclusions
    $('input[name="custom_package"]:checked').each(function() {
        const package_id = $(this).val();
        package_id_arr.push(package_id);
        const table = document.getElementById(`dynamic_table_incl${package_id}`);
        const rowCount = table.rows.length;
        const inclusion = $(`#inclusions${package_id}`).val();
        const exclusion = $(`#exclusions${package_id}`).val();

        // Push inclusion/exclusion for each row (although only one value will be stored)
        incl_arr.push(inclusion);
        excl_arr.push(exclusion);
    });

    if (package_id_arr.length === 0 && isAiQuotationActive()) {
        package_id_arr = resolveAiQuotationPackageIds();
    }

    if (package_id_arr.length === 0 && isAiQuotationActive()) {
        error_msg_alert('Please select destination for AI quotation.');
        return false;
    }

    if (package_id_arr.length === 0) {
        error_msg_alert('Please select at least one Package!');
        return false;
    }

    const selectedPackageTypeToAdd = $('#package_type').val();
    if (!selectedPackageTypeToAdd || selectedPackageTypeToAdd === '*Package Type') {
        error_msg_alert('Please select Package Type!');
        return false;
    }

    const hotelTableBeforeAdd = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
    const firstTierRowCount = (hotelTableBeforeAdd && typeof quotationGetFirstPackageHotelRowCount === 'function')
        ? quotationGetFirstPackageHotelRowCount(hotelTableBeforeAdd)
        : 0;
    const firstTierType = (hotelTableBeforeAdd && hotelTableBeforeAdd.rows.length && typeof quotationGetHotelRowPackageType === 'function')
        ? quotationGetHotelRowPackageType(hotelTableBeforeAdd.rows[0])
        : '';
    const isAddingNewPackageTier = firstTierRowCount > 0
        && selectedPackageTypeToAdd
        && firstTierType
        && selectedPackageTypeToAdd !== firstTierType;

    // Save selected packages to sessionStorage
    sessionStorage.setItem('selected_packages_tab3', JSON.stringify(package_id_arr));

    // Save hotel table state
    saveHotelTableState();

    // Collect data arrays for each row in selected packages
    let attraction_arr = [], program_arr = [], stay_arr = [], meal_plan_arr = [], package_p_id_arr = [], day_count_arr = [];
    let count = 0;
    var usedAiItineraryData = false;

    if (isAiQuotationActive() && !$('input[name="custom_package"]:checked').length) {
        try {
            var storedItinerary = JSON.parse(sessionStorage.getItem('itinerary_data') || '{}');
            attraction_arr = storedItinerary.attraction_arr || [];
            program_arr = storedItinerary.program_arr || [];
            stay_arr = storedItinerary.stay_arr || [];
            meal_plan_arr = storedItinerary.meal_plan_arr || [];
            package_p_id_arr = storedItinerary.package_p_id_arr || [];
            if (program_arr.length > 0) {
                day_count_arr = [program_arr.length];
                usedAiItineraryData = true;
            }
        } catch (e) {}
    }

    if (!usedAiItineraryData) {
    package_id_arr.forEach(package_id => {
        const table = document.getElementById(`dynamic_table_list_p_${package_id}`);
        if (!table) {
            return;
        }
        const rowCount = table.rows.length;

        for (let i = 0; i < rowCount; i++) {
            const row = table.rows[i];
            if (row.cells[0].childNodes[0].checked) {
                count++;
                const attraction = row.cells[2].childNodes[0].value;
                const program = row.cells[3].childNodes[0].value;
                const stay = row.cells[4].childNodes[0].value;
                const meal_plan = row.cells[5].childNodes[0].value;
                const package_id1 = row.cells[7].childNodes[0].value;

                // Validation
                if (!attraction || !program || !stay) {
                    error_msg_alert(`Special Attraction, Daywise Program, and Overnight Stay are mandatory in row ${i + 1}`);
                    return false;
                }

                // Validate inputs
                if (!validate_spattration(row.cells[2].childNodes[0].id) || 
                    !validate_dayprogram(row.cells[3].childNodes[0].id) ||
                    !validate_onstay(row.cells[4].childNodes[0].id)) {
                    return false;
                }

                // Push valid values into arrays
                attraction_arr.push(attraction);
                program_arr.push(program);
                stay_arr.push(stay);
                meal_plan_arr.push(meal_plan);
                package_p_id_arr.push(package_id1);
            }
        }
        day_count_arr.push(count);
        count = 0;
    });
    }

    const hotelPackageIds = package_id_arr.filter(function(packageId) {
        return packageId && String(packageId) !== '0';
    });

    const total_adult = $('#total_adult').val();
    const total_children = $('#total_children').val();
    const to_date = $('#to_date').val();
    const total_days = $('#total_days').val();
    const packagesToLoad = isAddingNewPackageTier
        ? hotelPackageIds
        : (typeof quotationFilterNewPackageIds === 'function'
            ? quotationFilterNewPackageIds(hotelPackageIds)
            : hotelPackageIds);
    const from_date = typeof quotationGetReferenceTravelStartDate === 'function'
        ? quotationGetReferenceTravelStartDate()
        : $('#from_date').val();

    if (!packagesToLoad.length && isAiQuotationActive() && program_arr.length > 0) {
        const table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
        let lastRowNo = 0;
        for (let r = 0; r < table.rows.length; r++) {
            const rowNo = parseInt(table.rows[r].cells[1].childNodes[0].value, 10);
            if (!isNaN(rowNo) && rowNo > lastRowNo) {
                lastRowNo = rowNo;
            }
        }
        window.quotationBatchPopulatingHotels = true;
        for (let i = 0; i < program_arr.length; i++) {
            addRow('tbl_package_tour_quotation_dynamic_hotel');
            const row = table.rows[table.rows.length - 1];
            if (row && row.cells[1] && row.cells[1].childNodes[0]) {
                row.cells[1].childNodes[0].value = lastRowNo + i + 1;
            }
            if (selectedPackageTypeToAdd && row.cells[2] && row.cells[2].childNodes[0]) {
                $(row.cells[2].childNodes[0]).val(selectedPackageTypeToAdd).trigger('change.select2');
            }
        }
        window.quotationBatchPopulatingHotels = false;
        if (selectedPackageTypeToAdd && typeof hideHotelPackage === 'function') {
            hideHotelPackage(selectedPackageTypeToAdd);
        }
        if (typeof get_hotel_cost === 'function') {
            get_hotel_cost();
        }
        sessionStorage.setItem('selected_packages_tab3', JSON.stringify(package_id_arr));
        saveHotelTableState();
        return false;
    }

    // Ajax to save data and update the table
    $.ajax({
        type: 'post',
        url: '../save/package_hotel_info.php',
        data: { package_id_arr: packagesToLoad, from_date },
        success: function(result) {
            let hotel_arr = JSON.parse(result);
            const table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
            let lastRowNo = 0;
            for (let r = 0; r < table.rows.length; r++) {
                const rowNo = parseInt(table.rows[r].cells[1].childNodes[0].value, 10);
                if (!isNaN(rowNo) && rowNo > lastRowNo) {
                    lastRowNo = rowNo;
                }
            }

            const templateRowCount = isAddingNewPackageTier ? firstTierRowCount : hotel_arr.length;
            const rowsToAdd = templateRowCount > 0 ? templateRowCount : hotel_arr.length;
            hotel_arr = typeof quotationNormalizeHotelListForCount === 'function'
                ? quotationNormalizeHotelListForCount(hotel_arr, rowsToAdd)
                : hotel_arr;

            const templateReferenceRows = isAddingNewPackageTier && typeof quotationGetTemplateHotelRowReferences === 'function'
                ? quotationGetTemplateHotelRowReferences(table, rowsToAdd)
                : [];
            const copyFromTemplate = isAddingNewPackageTier && templateReferenceRows.length > 0;

            const startRowIndex = table.rows.length;
            window.quotationBatchPopulatingHotels = true;

            hotel_arr.forEach(function(hotel, i) {
                addRow('tbl_package_tour_quotation_dynamic_hotel');
                const row = table.rows[table.rows.length - 1];
                if (!row || !row.cells || row.cells.length < 17) return;
                row.cells[1].childNodes[0].value = lastRowNo + i + 1;
                const templateRef = templateReferenceRows[i]
                    || templateReferenceRows[templateReferenceRows.length - 1];
                if (templateRef && typeof quotationApplyHotelRowReference === 'function') {
                    quotationApplyHotelRowReference(row, templateRef, { skipHotelLoad: true });
                }
                if (selectedPackageTypeToAdd && row.cells[2] && row.cells[2].childNodes[0]) {
                    $(row.cells[2].childNodes[0]).val(selectedPackageTypeToAdd).trigger('change.select2');
                }
            });

            const finishHotelPopulation = function() {
                window.quotationBatchPopulatingHotels = false;

                if (selectedPackageTypeToAdd) {
                    hideHotelPackage(selectedPackageTypeToAdd);
                }

                if (typeof get_hotel_cost === 'function') {
                    get_hotel_cost();
                }

                const transportPackagesToLoad = typeof quotationFilterNewPackageIdsForTransport === 'function'
                    ? quotationFilterNewPackageIdsForTransport(package_id_arr)
                    : [];

                const afterTransportOrSkip = function() {
                    if (typeof quotationSyncTravelStaySectionsFromHotels === 'function') {
                        quotationSyncTravelStaySectionsFromHotels();
                    } else if (typeof syncQuotationTravelStayDates === 'function') {
                        syncQuotationTravelStayDates({ preserveHotelDates: true });
                    }
                    if (typeof get_excursion_amount === 'function') {
                        get_excursion_amount();
                    }
                };

                if (!transportPackagesToLoad.length) {
                    afterTransportOrSkip();
                    return;
                }

                $.ajax({
                    type: 'post',
                    url: '../save/package_transport_info.php',
                    data: {
                        package_id_arr: transportPackagesToLoad,
                        from_date: from_date,
                        total_adult: total_adult
                    },
                    success: function(transportResult) {
                        const transport_arr = JSON.parse(transportResult);
                        if (typeof quotationPopulateTransportRows === 'function') {
                            quotationPopulateTransportRows(transport_arr, {
                                append: true,
                                from_date: from_date,
                                to_date: to_date
                            });
                        }

                        afterTransportOrSkip();
                        if (typeof get_transport_cost === 'function') {
                            get_transport_cost();
                        }
                    }
                });
            };

            if (typeof populateQuotationHotelRowsSequential === 'function') {
                populateQuotationHotelRowsSequential(table, hotel_arr, {
                    startRowIndex: startRowIndex,
                    templateReferenceRows: templateReferenceRows,
                    copyFromTemplate: copyFromTemplate,
                    packageTypeToAdd: selectedPackageTypeToAdd,
                    onComplete: finishHotelPopulation
                });
            } else {
                hotel_arr.forEach(function(hotel, i) {
                    const row = table.rows[startRowIndex + i];
                    if (!row) return;
                    const ref = templateReferenceRows[i]
                        || templateReferenceRows[templateReferenceRows.length - 1]
                        || null;
                    populateHotelRow(row, hotel, i, hotel_arr, {
                        referenceRow: ref,
                        copyFromTemplate: copyFromTemplate,
                        packageTypeToAdd: selectedPackageTypeToAdd
                    });
                });
                finishHotelPopulation();
            }
        }
    });
}

// Helper function to populate hotel row
function populateHotelRow(row, hotel, i, hotel_arr, options, onComplete) {
    options = options || {};
    const freshPackageLoad = !!options.freshPackageLoad;
    const ref = freshPackageLoad ? null : (options.referenceRow || null);
    const copyFromTemplate = !!options.copyFromTemplate && !!ref;
    const hotelSelect = $(row.cells[4].childNodes[0]);
    const roomCatSelect = $(row.cells[5].childNodes[0]);
    const cityEl = row.cells[3].childNodes[0];
    const applyTemplateExtras = function() {
        if (ref && ref.room_cat_id && roomCatSelect.length) {
            if (roomCatSelect.find('option').filter(function() { return String(this.value) === String(ref.room_cat_id); }).length === 0) {
                roomCatSelect.append(new Option(ref.room_cat_name || '', ref.room_cat_id, true, true));
            }
            roomCatSelect.val(ref.room_cat_id).trigger('change.select2');
        }
        if (ref && ref.hotel_type && row.cells[8] && row.cells[8].childNodes[0]) {
            row.cells[8].childNodes[0].value = ref.hotel_type;
        }
        if (typeof onComplete === 'function') {
            onComplete();
        }
    };
    const done = function() {
        applyTemplateExtras();
    };

    const packageTypeSelect = $(row.cells[2].childNodes[0]);
    const packageTypeValue = options.packageTypeToAdd || $('#package_type').val();
    if (packageTypeValue) {
        packageTypeSelect.val(packageTypeValue).trigger('change.select2');
    }

    const cityId = (ref && ref.city_id) ? ref.city_id : (hotel.city_id || '');
    const cityName = (ref && ref.city_name) ? ref.city_name : (hotel.city_name || '');

    if (typeof setQuotationCitySelect === 'function') {
        setQuotationCitySelect(cityEl, cityId, cityName);
    } else {
        city_lzloading(cityEl);
        if (cityId && cityName) {
            const citySelect = $(cityEl);
            const cityOption = new Option(cityName, cityId, true, true);
            citySelect.append(cityOption).trigger('change.select2');
        }
    }

    const hotelLoadData = Object.assign({}, hotel, {
        city_id: cityId,
        city_name: cityName
    });
    if (copyFromTemplate && ref && ref.hotel_id) {
        hotelLoadData.hotel_id1 = ref.hotel_id;
        hotelLoadData.hotel_name = ref.hotel_name || '';
    } else if (!hotelLoadData.hotel_id1 && ref && ref.hotel_id) {
        hotelLoadData.hotel_id1 = ref.hotel_id;
        hotelLoadData.hotel_name = ref.hotel_name || '';
    }

    if (typeof loadQuotationHotelFromPackage === 'function') {
        loadQuotationHotelFromPackage(hotelLoadData, hotelSelect, done);
    } else if (cityId && typeof hotelDropdownLoadByCity === 'function') {
        hotelDropdownLoadByCity(cityId, hotelSelect, function(success) {
            if (success && hotelLoadData.hotel_id1) {
                if (typeof selectHotelInDropdown === 'function') {
                    selectHotelInDropdown(hotelSelect, hotelLoadData.hotel_id1, hotelLoadData.hotel_name);
                } else {
                    hotelSelect.val(hotelLoadData.hotel_id1).trigger('change');
                }
            }
            done();
        });
    } else {
        hotelSelect.html('<option value="">*Hotel Name</option>' +
            (hotelLoadData.hotel_id1 ? '<option value="' + hotelLoadData.hotel_id1 + '" selected>' + (hotelLoadData.hotel_name || '') + '</option>' : ''));
        hotelSelect.select2({ width: '160px', minimumResultsForSearch: 0 });
        done();
    }

    hotelSelect.attr('data-add-new-option', 'true');
    if (typeof initHotelSelectAddNew === 'function') {
        initHotelSelectAddNew(hotelSelect);
    }

    let checkIn = hotel.check_in_date || '';
    let checkOut = hotel.check_out_date || '';
    if (ref) {
        if (ref.check_in) checkIn = ref.check_in;
        if (ref.check_out) checkOut = ref.check_out;
    } else if (options.useReferenceDates && typeof quotationGetReferenceHotelDateRange === 'function') {
        const refDates = quotationGetReferenceHotelDateRange();
        if (refDates) {
            checkIn = refDates.check_in;
            checkOut = refDates.check_out;
        }
    }

    // Set other fields (dates, package details, etc.)
    if (row.cells[1] && row.cells[1].childNodes[0]) {
        const existingSrNo = parseInt(row.cells[1].childNodes[0].value, 10);
        if (isNaN(existingSrNo) || existingSrNo <= 0) {
            row.cells[1].childNodes[0].value = i + 1;
        }
    }
    row.cells[6].childNodes[0].value = checkIn;
    row.cells[7].childNodes[0].value = checkOut;
    if (!(copyFromTemplate && ref && ref.hotel_type)) {
        row.cells[8].childNodes[0].value = hotel.hotel_type || '';
    } else if (row.cells[8] && row.cells[8].childNodes[0]) {
        row.cells[8].childNodes[0].value = ref.hotel_type;
    }
    if (row.cells[9] && row.cells[9].childNodes[0]) {
        var nights = 0;
        if (typeof quotationComputeNightsFromDates === 'function') {
            nights = quotationComputeNightsFromDates(checkIn, checkOut);
        }
        row.cells[9].childNodes[0].value = nights > 0 ? nights : (hotel.total_days || '');
    }
    if (row.cells[7] && row.cells[7].childNodes[0] && row.cells[7].childNodes[0].id && typeof calculate_total_nights === 'function') {
        calculate_total_nights(row.cells[7].childNodes[0].id);
    }
    if (row.cells[12] && row.cells[12].childNodes[0]) {
        row.cells[12].childNodes[0].value = hotel.package_name || '';
    }
    if (row.cells[14] && row.cells[14].childNodes[0]) {
        row.cells[14].childNodes[0].value = hotel.package_id || '';
    }

    if (ref) {
        if (ref.total_rooms && row.cells[10] && row.cells[10].childNodes[0]) {
            row.cells[10].childNodes[0].value = ref.total_rooms;
        }
        if (ref.extra_bed !== undefined && ref.extra_bed !== null && row.cells[11] && row.cells[11].childNodes[0]) {
            row.cells[11].childNodes[0].value = ref.extra_bed;
        }
        if (ref.meal_plan && row.cells[16] && row.cells[16].childNodes[0]) {
            $(row.cells[16].childNodes[0]).val(ref.meal_plan).trigger('change');
        }
    }

    $('#' + row.cells[2].childNodes[0].id).select2().trigger("change");
    if (!copyFromTemplate || !ref || !ref.room_cat_id) {
        roomCatSelect.select2().trigger("change");
    }
    initPackageQuotationMealPlanSelect(row);
}

// Helper function to initialize city dropdowns
function initializeCityDropdowns(table, hotel_arr, startRowIndex) {
    if (!hotel_arr || !hotel_arr.length) {
        return;
    }
    const firstRow = typeof startRowIndex === 'number'
        ? startRowIndex
        : Math.max(1, table.rows.length - hotel_arr.length);

    for (let i = 0; i < hotel_arr.length; i++) {
        const rowIndex = firstRow + i;
        if (rowIndex >= table.rows.length) {
            break;
        }
        const row = table.rows[rowIndex];
        const citySelect = $(row.cells[3].childNodes[0]);
        const hotel = hotel_arr[i] || {};

        city_lzloading(citySelect);

        if (hotel.city_id && hotel.city_name) {
            const newOption = new Option(hotel.city_name, hotel.city_id, true, true);
            citySelect.append(newOption).trigger('change');
        }
    }
}









function calculateCostingCardsTab3(type = '') {

    console.log('Run:', type);

    // =========================
    // LAND COST
    // =========================

    let hotel = 0;

    // 👉 Infant: take manual value (no override logic)
    if (type === 'infant') {
        hotel = parseFloat($('#hotel_cost').val()) || 0;
    } else {
        // 👉 Others: normal (can be auto or manual as per your system)
        hotel = parseFloat($('#hotel_cost').val()) || 0;
    }

    let transfer = parseFloat($('#transfer_cost').val()) || 0;
    let activity = parseFloat($('#activity_cost').val()) || 0;

    let land_cost = hotel + transfer + activity;

    // =========================
    // SERVICE CHARGE
    // =========================
    let service = parseFloat($('#service_charge').val()) || 0;
    let discount_percent = parseFloat($('#discount_percent').val()) || 0;

    // 👉 Discount calculation (internal only)
    let discount = (service * discount_percent) / 100;

    if (discount > service) discount = service;

    let service_used = service - discount;

    if (service_used < 0) service_used = 0;

    // =========================
    // TRAVEL COST
    // =========================
    let flight = parseFloat($('#flight_cost').val()) || 0;
    let train = parseFloat($('#train_cost').val()) || 0;
    let cruise = parseFloat($('#cruise_cost').val()) || 0;

    let travel_cost = flight + train + cruise;

    // =========================
    // OTHER COST
    // =========================
    let visa = parseFloat($('#visa_cost').val()) || 0;
    let guide = parseFloat($('#guide_cost').val()) || 0;
    let misc = parseFloat($('#misc_cost').val()) || 0;

    let total_pax = parseInt($('#total_pax').val()) || 1;

    if (total_pax <= 0) total_pax = 1;

    let guide_pp = guide / total_pax;
    let misc_pp = misc / total_pax;

    let other_cost = visa + guide_pp + misc_pp;

    // =========================
    // BASE + SUBTOTAL
    // =========================
    let base = land_cost + service_used;

    let subtotal = base + travel_cost + other_cost;

    // =========================
    // TAX
    // =========================
    let tax_percent = parseFloat($('#tax_percent').val()) || 0;
    let tax_apply = $('#tax_apply').val();

    let tax = 0;

    if (tax_apply == "service") {
        tax = (service_used * tax_percent) / 100;
    } 
    else if (tax_apply == "basic") {
        tax = (land_cost * tax_percent) / 100;
    } 
    else if (tax_apply == "total") {
        tax = (subtotal * tax_percent) / 100;
    }

    let subtotal_with_tax = subtotal + tax;

    // =========================
    // TCS
    // =========================
    let tcs_percent = parseFloat($('#tcs_percent').val()) || 0;

    let tcs = (subtotal_with_tax * tcs_percent) / 100;

    // =========================
    // FINAL AMOUNT
    // =========================
    let final = subtotal_with_tax + tcs;

    // =========================
    // RETURN VALUES
    // =========================
    return {
        type: type,
        hotel: hotel,
        transfer: transfer,
        activity: activity,
        land: land_cost,
        service: service,            // UI value
        discount: discount,
        service_used: service_used,  // internal value
        travel: travel_cost,
        other: other_cost,
        tax: tax,
        tcs: tcs,
        final: final
    };
}

// =========================
// UPDATE TABLE FUNCTION
// =========================
function updateCard(data) {

    let table = $('.table'); // or give ID if multiple tables

    // Individual components
    table.find('tr[data-type="hotel"] .price').text('₹ ' + (data.hotel || 0).toFixed(2));
    table.find('tr[data-type="transfer"] .price').text('₹ ' + (data.transfer || 0).toFixed(2));
    table.find('tr[data-type="activity"] .price').text('₹ ' + (data.activity || 0).toFixed(2));

    // Land total
    table.find('tr[data-type="land"] .price').text('₹ ' + data.land.toFixed(2));

    // Other
    table.find('tr[data-type="misc"] .price').text('₹ ' + data.other.toFixed(2));

    // Service / Markup
    table.find('tr[data-type="markup"] .price').text('₹ ' + data.service.toFixed(2));

    // Tax
    table.find('tr[data-type="tax"] .price').text('₹ ' + data.tax.toFixed(2));

    // Final
    table.find('tr[data-type="total"] .price').text('₹ ' + data.final.toFixed(2));
}







</script>