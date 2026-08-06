<?php
$package_lookup_id = get_quotation_package_lookup_id($sq_quotation);
$is_ai_quotation = is_ai_package_quotation($sq_quotation) ? 1 : 0;
$sq_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '$package_lookup_id'"));
$package_name = isset($sq_package['package_name']) ? $sq_package['package_name'] : '';

if (!function_exists('quotation_resolve_excursion_tariff')) {
    function quotation_resolve_excursion_tariff($stored_excursion_value)
    {
        $stored_excursion_value = trim((string)$stored_excursion_value);
        if ($stored_excursion_value === '') {
            return null;
        }
        if (ctype_digit($stored_excursion_value)) {
            $sq_ex = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master_tariff where entry_id='$stored_excursion_value'"));
            if ($sq_ex) {
                return $sq_ex;
            }
        }
        return mysqli_fetch_assoc(mysqlQuery("select * from excursion_master_tariff where excursion_name='" . addslashes($stored_excursion_value) . "' limit 1"));
    }
}

if (!function_exists('quotation_excursion_options_html')) {
    function quotation_excursion_options_html($city_id, $selected_entry_id = '')
    {
        $html = '<option value="">*Activity Name</option>';
        $city_id = intval($city_id);
        $selected_entry_id = trim((string)$selected_entry_id);
        $found_selected = false;

        if ($city_id > 0) {
            $sq_exc_list = mysqlQuery("select entry_id, excursion_name from excursion_master_tariff where city_id='$city_id' and active_flag!='Inactive' order by excursion_name");
            while ($row = mysqli_fetch_assoc($sq_exc_list)) {
                $selected = ($selected_entry_id !== '' && (string)$row['entry_id'] === (string)$selected_entry_id) ? ' selected' : '';
                if ($selected) {
                    $found_selected = true;
                }
                $html .= '<option value="' . htmlspecialchars($row['entry_id']) . '"' . $selected . '>' . htmlspecialchars($row['excursion_name']) . '</option>';
            }
        }

        if ($selected_entry_id !== '' && !$found_selected) {
            $sq_orphan = mysqli_fetch_assoc(mysqlQuery("select entry_id, excursion_name from excursion_master_tariff where entry_id='" . addslashes($selected_entry_id) . "' limit 1"));
            if ($sq_orphan) {
                $html .= '<option value="' . htmlspecialchars($sq_orphan['entry_id']) . '" selected>' . htmlspecialchars($sq_orphan['excursion_name']) . '</option>';
            }
        }

        return $html;
    }
}
?>
<form id="frm_tab3">

    <div class="app_panel" style="overflow-x: hidden;">



        <div class="container" style="width:100% !important;">


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
                                <div id="collapse4" class="panel-collapse collapse in main_block" role="tabpanel"
                                    aria-labelledby="heading4">
                                    <div class="panel-body">
                                        <?php include_once('hotel_tbl.php'); ?>
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
                                <div id="collapse5" class="panel-collapse collapse in main_block" role="tabpanel"
                                    aria-labelledby="heading5">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-12 text-right mg_bt_20_sm_xs">
                                                <button type="button" class="btn btn-excel btn-sm"
                                                    onClick="addRow('tbl_package_tour_quotation_dynamic_transport_u');destinationLoading('.pickup_from', 'Pickup Location');destinationLoading('.drop_to', 'Drop-off Location');"><i
                                                        class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-xs-12">

                                                <div class="table-responsive">

                                                    <table id="tbl_package_tour_quotation_dynamic_transport_u"
                                                        name="tbl_package_tour_quotation_dynamic_transport_u"
                                                        class="table mg_bt_0 table-bordered mg_bt_10 pd_bt_51">

                                                        <?php
                                                        $sq_transport_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_transport_entries2 where quotation_id='$quotation_id'"));

                                                        if ($sq_transport_count == 0) {
                                                        ?>
                                                            <tr>
                                                                <td><input class="css-checkbox" id="chk_transport-"
                                                                        type="checkbox"
                                                                        onchange="get_transport_cost_update(this.id);" readonly><label class="css-label"
                                                                        for="chk_transport-"> </label></td>
                                                                <td><input maxlength="15" value="1" type="text"
                                                                        name="username" placeholder="Sr. No."
                                                                        class="form-control" disabled /></td>
                                                                <td><select id="transport_vehicle-"
                                                                        name="transport_vehicle-" title="Select Transport"
                                                                        onchange="get_transport_cost_update(this.id);"
                                                                        class="form-control app_select2"
                                                                        style="width:200px" data-add-new-option="true">
                                                                        <option value="">Transport Vehicle</option>
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
                                                                        onchange="get_to_date(this.id,'transport_end_date-');get_transport_cost_update(this.id);">
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
                                                                        onchange="get_transport_cost_update(this.id);">
                                                                    </select></td>
                                                                <td><select name="drop_to-" id="drop_to-"
                                                                        style="width:250px;" data-toggle="tooltip"
                                                                        title="Drop-off Location"
                                                                        class="form-control app_select2 drop_to"
                                                                        onchange="get_transport_cost_update(this.id);">
                                                                    </select></td>
                                                                <td><select name="duration-" id="duration-" style="width:170px;" title="*Service Duration" data-toggle="tooltip" class="form-control app_select2" onchange="get_transport_cost_update(this.id)">
                                                                        <option value="">*Service Duration</option>
                                                                        <?php echo get_service_duration_dropdown(); ?>
                                                                    </select></td>
                                                                <td><input type="text" id="no_vehicles-" name="no_vehicles-"
                                                                        placeholder="*No.Of vehicles" title="No.Of vehicles"
                                                                        style="width:150px"
                                                                        onchange="get_transport_cost_update(this.id);"></td>
                                                                <td class="hidden"><input type="text" id="transport_cost-"
                                                                        name="transport_cost-" placeholder="Cost"
                                                                        title="Cost" style="width:150"
                                                                        style="display:none;"></td>
                                                                <td class="hidden"><input type="text" id="package_name-"
                                                                        name="package_name-" placeholder="Package Name"
                                                                        title="Package Name" style="width:200px"
                                                                        style="display:none;" readonly></td>
                                                                <td><input type="text" id="package_id-" name="package_id-"
                                                                        placeholder="Package ID" title="Package ID"
                                                                        style="display:none;"></td>
                                                                <td><input type="hidden" id="pickup_type-"
                                                                        name="pickup_type-" style="display:none;"></td>
                                                                <td><input type="hidden" id="drop_type" name="drop_type"
                                                                        style="display:none;"></td>
                                                                <td class="hidden"><input type="hidden" id="transport_id-" value=""></td>
                                                            </tr>
                                                            <script type="text/javascript">
                                                                $('#transport_vehicle-,#duration-').select2();
                                                                $('#transport_start_date-,#transport_end_date-')
                                                                    .datetimepicker({
                                                                        format: 'd-m-Y',
                                                                        timepicker: false
                                                                    });
                                                            </script>
                                                            <?php
                                                        } else {
                                                            $count = 0;
                                                            $sq_q_tr = mysqlQuery("select * from package_tour_quotation_transport_entries2 where quotation_id='$quotation_id'");
                                                            while ($row_q_tr = mysqli_fetch_assoc($sq_q_tr)) {

                                                                $count++;
                                                                $sq_transport_bus_agency1 = mysqli_fetch_assoc(mysqlQuery("select * from b2b_transfer_master where entry_id='$row_q_tr[vehicle_name]'"));
                                                                // Pickup
                                                                if ($row_q_tr['pickup_type'] == 'city') {
                                                                    $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_q_tr[pickup]'"));
                                                                    $pickup = $row['city_name'];
                                                                    $plabel = 'City Name';
                                                                } else if ($row_q_tr['pickup_type'] == 'hotel') {
                                                                    $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_q_tr[pickup]'"));
                                                                    $pickup = $row['hotel_name'];
                                                                    $plabel = 'Hotel Name';
                                                                } else {
                                                                    $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_q_tr[pickup]'"));
                                                                    $airport_nam = clean($row['airport_name']);
                                                                    $airport_code = clean($row['airport_code']);
                                                                    $pickup = $airport_nam . " (" . $airport_code . ")";
                                                                    $plabel = 'Airport Name';
                                                                }
                                                                //Drop-off
                                                                if ($row_q_tr['drop_type'] == 'city') {
                                                                    $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_q_tr[drop]'"));
                                                                    $drop = $row['city_name'];
                                                                    $dlabel = 'City Name';
                                                                } else if ($row_q_tr['drop_type'] == 'hotel') {
                                                                    $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_q_tr[drop]'"));
                                                                    $drop = $row['hotel_name'];
                                                                    $dlabel = 'Hotel Name';
                                                                } else {
                                                                    $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_q_tr[drop]'"));
                                                                    $airport_nam = clean($row['airport_name']);
                                                                    $airport_code = clean($row['airport_code']);
                                                                    $drop = $airport_nam . " (" . $airport_code . ")";
                                                                    $dlabel = 'Airport Name';
                                                                }
                                                            ?>
                                                                <tr>
                                                                    <td><input class="css-checkbox"
                                                                            id="chk_transport-<?= $count ?>_u"
                                                                            name="chk_transport-<?= $count ?>_u" type="checkbox"
                                                                            onchange="get_transport_cost_update(this.id);"
                                                                            checked><label class="css-label"
                                                                            for="chk_transport-<?= $count ?>_u"> </label></td>
                                                                    <td><input maxlength="15" value="<?= $count ?>" type="text"
                                                                            name="username" placeholder="Sr. No."
                                                                            class="form-control" disabled /></td>
                                                                    <td class="col-md-3"><select
                                                                            name="transport_vehicle-<?= $count ?>_u"
                                                                            id="transport_vehicle-<?= $count ?>_u"
                                                                            style="width:150px" class="app_select2 form-control"
                                                                            onchange="get_transport_cost_update(this.id)" data-add-new-option="true">
                                                                            <option
                                                                                value="<?= $sq_transport_bus_agency1['entry_id'] ?>">
                                                                                <?= $sq_transport_bus_agency1['vehicle_name'] ?>
                                                                            </option>
                                                                            <option value="">Select Vehicle</option>
                                                                            <?php
                                                                            $sq_transport_bus_agency = mysqlQuery("select * from b2b_transfer_master where status!='Inactive' order by vehicle_name asc");
                                                                            while ($row_transport_bus_agency = mysqli_fetch_assoc($sq_transport_bus_agency)) { ?>
                                                                                <option value="<?= $row_transport_bus_agency['entry_id'] ?>">
                                                                                    <?= $row_transport_bus_agency['vehicle_name'] ?>
                                                                                </option>
                                                                            <?php } ?>
                                                                        </select></td>
                                                                    <td><input type="text"
                                                                            id="transport_start_date-<?= $count ?>_u"
                                                                            name="transport_start_date-<?= $count ?>_u"
                                                                            style="width:150px" placeholder="Start Date"
                                                                            title="Start Date" class="app_datepicker"
                                                                            onchange="get_to_date(this.id,'transport_end_date-<?= $count ?>_u');get_transport_cost_update(this.id);"
                                                                            value="<?= date('d-m-Y', strtotime($row_q_tr['start_date'])) ?>">
                                                                    </td>
                                                                    <td><input type="text"
                                                                            id="transport_end_date-<?= $count ?>_u"
                                                                            name="transport_end_date-<?= $count ?>_u"
                                                                            placeholder="End Date" title="End Date"
                                                                            class="app_datepicker" style="width:150px"
                                                                            onchange="validate_validDate('transport_start_date-<?= $count ?>_u','transport_end_date-<?= $count ?>_u');"
                                                                            value="<?= date('d-m-Y', strtotime($row_q_tr['end_date'])) ?>">
                                                                    </td>
                                                                    <td><select name="pickup_from-<?= $count ?>_u"
                                                                            id="pickup_from-<?= $count ?>_u"
                                                                            data-toggle="tooltip" style="width:250px;"
                                                                            title="Pickup Location"
                                                                            class="form-control app_select2 pickup_from"
                                                                            onchange="get_transport_cost_update(this.id);">
                                                                            <optgroup value='<?= $row_q_tr['pickup_type'] ?>'
                                                                                label="<?= $plabel ?>">
                                                                                <option
                                                                                    value="<?= $row_q_tr['pickup_type'] . '-' . $row_q_tr['pickup'] ?>">
                                                                                    <?= $pickup ?></option>
                                                                        </select></td>
                                                                    <td><select name="drop_to-<?= $count ?>_u"
                                                                            id="drop_to-<?= $count ?>_u" style="width:250px;"
                                                                            data-toggle="tooltip" title="Drop-off Location"
                                                                            class="form-control app_select2 drop_to"
                                                                            onchange="get_transport_cost_update(this.id);">
                                                                            <optgroup value='<?= $row_q_tr['drop_type'] ?>'
                                                                                label="<?= $dlabel ?>">
                                                                                <option
                                                                                    value="<?= $row_q_tr['drop_type'] . '-' . $row_q_tr['drop'] ?>">
                                                                                    <?= $drop ?></option>
                                                                        </select></td>
                                                                    <td><select name="duration-<?= $count ?>_u" id="duration-<?= $count ?>_u" style="width:170px;" title="*Service Duration" data-toggle="tooltip" class="form-control app_select2" onchange="get_transport_cost_update(this.id)">
                                                                            <?php
                                                                            $row = mysqli_fetch_assoc(mysqlQuery("select entry_id,duration from service_duration_master where duration='$row_q_tr[service_duration]'"));
                                                                            ?>
                                                                            <option value="<?= $row['entry_id'] ?>"><?= $row['duration'] ?></option>
                                                                            <option value="">*Service Duration</option>
                                                                            <?php echo get_service_duration_dropdown(); ?>
                                                                        </select></td>
                                                                    <td><input type="text" id="no_vehicles-<?= $count ?>_u"
                                                                            name="no_vehicles-<?= $count ?>_u"
                                                                            placeholder="*No.Of vehicles" title="No.Of vehicles"
                                                                            style="width:150px"
                                                                            value="<?= $row_q_tr['vehicle_count'] ?>"
                                                                            onchange="get_transport_cost_update(this.id);"></td>
                                                                    <td class="hidden"><input type="text"
                                                                            id="transport_cost-<?= $count ?>_u"
                                                                            name="transport_cost-<?= $count ?>_u"
                                                                            placeholder="Cost" title="Cost" style="width:170px"
                                                                            value="<?= $row_q_tr['transport_cost'] ?>"></td>
                                                                    <td class="hidden"><input type="hidden"
                                                                            id="package_name-<?= $count ?>_u"
                                                                            name="package_name-<?= $count ?>_u"
                                                                            placeholder="Package Name" title="Package Name"
                                                                            style="display:none;" readonly></td>
                                                                    <td class="hidden"><input type="text"
                                                                            id="package_id-<?= $count ?>_u"
                                                                            name="package_id-<?= $count ?>_u"
                                                                            placeholder="Package ID" title="Package ID"
                                                                            style="display:none;"
                                                                            value="<?= $row_q_tr['package_id'] ?>"></td>
                                                                    <td class="hidden"><input type="hidden"
                                                                            id="pickup_type-<?= $count ?>_u"
                                                                            name="pickup_type-<?= $count ?>_u"
                                                                            style="display:none;"
                                                                            value="<?= $row_q_tr['pickup_type'] ?>"></td>
                                                                    <td class="hidden"><input type="hidden"
                                                                            id="drop_type-<?= $count ?>_u"
                                                                            name="drop_type-<?= $count ?>_u"
                                                                            style="display:none;"
                                                                            value="<?= $row_q_tr['drop_type'] ?>"></td>
                                                                    <td class="hidden"><input type="hidden" id="transport_id-<?= $count ?>_u" value="<?= $row_q_tr['id'] ?>"></td>
                                                                </tr>
                                                                <script type="text/javascript">
                                                                    $('#transport_vehicle-<?= $count ?>_u,#duration-<?= $count ?>_u').select2();
                                                                    $('#transport_start_date-<?= $count ?>_u,#transport_end_date-<?= $count ?>_u')
                                                                        .datetimepicker({
                                                                            format: 'd-m-Y',
                                                                            timepicker: false
                                                                        });
                                                                </script>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
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
                                <div id="collapse2" class="panel-collapse collapse in main_block" role="tabpanel"
                                    aria-labelledby="heading2">
                                    <div class="panel-body">
                                        <?php include_once('plane_tbl.php'); ?>
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
                                <div id="collapse6" class="panel-collapse collapse in  main_block" role="tabpanel"
                                    aria-labelledby="heading1">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-12 text-right mg_bt_20_sm_xs">
                                                <button type="button" class="btn btn-excel btn-sm"
                                                    onClick="addRow('tbl_package_tour_quotation_dynamic_excursion');city_lzloading('.act_city', '*City')"><i
                                                        class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="table-responsive">
                                                    <table id="tbl_package_tour_quotation_dynamic_excursion"
                                                        name="tbl_package_tour_quotation_dynamic_excursion"
                                                        class="table mg_bt_0 table-bordered mg_bt_10 pd_bt_51">
                                                        <?php
                                                        $sq_ex_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_excursion_entries where quotation_id='$quotation_id'"));
                                                        if ($sq_ex_count == 0) {
                                                        ?>
                                                            <tr>
                                                                <td><input class="css-checkbox" id="chk_tour_group-1_u"
                                                                        type="checkbox"
                                                                        onchange="get_excursion_amount_update(this.id);"><label
                                                                        class="css-label" for="chk_tour_group-1_u"> <label>
                                                                </td>
                                                                <td style="width:10%"><input maxlength="15" value="1"
                                                                        type="text" name="username1" placeholder="Sr. No."
                                                                        class="form-control" disabled /></td>
                                                                <td><input type="text"
                                                                        class="form-control app_datetimepicker"
                                                                        id="exc_date-1_u" name="exc_date-1_u"
                                                                        placeholder="Activity Date/Time"
                                                                        title="Activity Date/Time"
                                                                        value="<?= date('d-m-Y H:i') ?>" style="width:130px"
                                                                        onchange="get_excursion_amount_update(this.id);">
                                                                </td>
                                                                <td><select id="city_name-1_u"
                                                                        class="form-control app_select2 act_city"
                                                                        name="city_name-1_u" title="City Name"
                                                                        style="width:150px"
                                                                        onchange="get_excursion_list(this.id);" data-add-new-option="true">
                                                                        <option value="">*City</option>
                                                                    </select></td>
                                                                <td><select id="excursion-1_u"
                                                                        class="form-control app_select2"
                                                                        title="Activity Name" name="excursion-1_u"
                                                                        style="width:150px"
                                                                        onchange="get_excursion_amount_update(this.id);">
                                                                        <option value="">*Activity Name</option>
                                                                    </select></td>
                                                                <td><select name="transfer_option-1_u" id="transfer_option-1_u"
                                                                        data-toggle="tooltip"
                                                                        class="form-contrl app_select2"
                                                                        title="Transfer Option" style="width:150px"
                                                                        onchange="get_excursion_amount_update(this.id);">
                                                                        <option value="Private Transfer">Private Transfer
                                                                        </option>
                                                                        <option value="Without Transfer">Without Transfer
                                                                        </option>
                                                                        <option value="Sharing Transfer">Sharing Transfer
                                                                        </option>
                                                                        <option value="SIC">SIC</option>
                                                                    </select></td>
                                                                <td><input type="number" id="adult-1_u" name="adult-1_u"
                                                                        placeholder="Adult(s)" title="Adult(s)"
                                                                        style="width:150px"
                                                                        onchange="get_excursion_amount_update(this.id);validate_balance(this.id);validate_pax_count(this.id,'Adult');">
                                                                </td>
                                                                <td><input type="number" id="child-1_u" name="child-1_u"
                                                                        placeholder="Child With-Bed" title="Child With-Bed"
                                                                        style="width:150px"
                                                                        onchange="get_excursion_amount_update(this.id);validate_balance(this.id);validate_pax_count(this.id,'ChildWithBed');">
                                                                </td>
                                                                <td><input type="number" id="childwo-1_u" name="childwo-1_u"
                                                                        placeholder="Child Without-Bed"
                                                                        title="Child Without-Bed" style="width:150px"
                                                                        onchange="get_excursion_amount_update(this.id);validate_balance(this.id);validate_pax_count(this.id,'ChildWithoutBed');">
                                                                </td>
                                                                <td><input type="number" id="infant-1_u" name="infant-1_u"
                                                                        placeholder="Infant(s)" title="Infant(s)"
                                                                        style="width:150px"
                                                                        onchange="get_excursion_amount_update(this.id);validate_balance(this.id);validate_pax_count(this.id,'Infant');">
                                                                </td>
                                                                <td style="display:none"><input type="text"
                                                                        id="excursion_amount-1_u" name="excursion_amount-1_u"
                                                                        placeholder="Activity Amount"
                                                                        title="Activity Amount"
                                                                        style="width:150px;display:none;"
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
                                                                <td><select name="vehicle_id-1_u" id="vehicle_id-1_u"
                                                                        style="width: 155px"
                                                                        class="form-control app_select2"
                                                                        title="Select Vehicle"
                                                                        onchange="get_excursion_amount_update(this.id);">
                                                                        <option value=''>Select Vehicle</option>
                                                                        <?php
                                                                        $sq_vehicle = mysqlQuery("select * from b2b_transfer_master where status='Active' order by vehicle_name");
                                                                        while ($row_vehicle = mysqli_fetch_assoc($sq_vehicle)) {
                                                                        ?>
                                                                            <option value="<?= $row_vehicle['entry_id'] ?>">
                                                                                <?= $row_vehicle['vehicle_name'] ?></option>
                                                                        <?php } ?>
                                                                    </select></td>
                                                                <td><input type="number" id="no_vehicles-1_u" name="no_vehicles-1_u"
                                                                        placeholder="No.Of Vehicles" title="No.Of Vehicles"
                                                                        style="width:150px" onchange="get_excursion_amount_update(this.id);">
                                                                </td>
                                                                <td class="hidden"><input type="hidden" id="excursion_id-1_u" value=""></td>
                                                            </tr>
                                                            <script>
                                                                $("#exc_date-1_u").datetimepicker({
                                                                    format: 'd-m-Y H:i'
                                                                });
                                                            </script>
                                                            <?php
                                                        } else {
                                                            $count = 0;
                                                            $sq_q_ex = mysqlQuery("select * from package_tour_quotation_excursion_entries where quotation_id='$quotation_id'");
                                                            while ($row_q_ex = mysqli_fetch_assoc($sq_q_ex)) {

                                                                $count++;
                                                                $sq_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_q_ex[city_name]'"));
                                                                $sq_ex = quotation_resolve_excursion_tariff($row_q_ex['excursion_name']);
                                                                $saved_exc_entry_id = $sq_ex ? $sq_ex['entry_id'] : $row_q_ex['excursion_name'];
                                                            ?>
                                                                <tr>
                                                                    <td><input class="css-checkbox"
                                                                            id="chk_tour_group-<?= $count ?>_u" type="checkbox"
                                                                            checked
                                                                            onchange="get_excursion_amount_update(this.id);"><label class="css-label"
                                                                            for="chk_tour_group-<?= $count ?>_u">
                                                                            <label></td>
                                                                    <td><input maxlength="15" value="<?= $count ?>" type="text"
                                                                            name="username1" placeholder="Sr. No."
                                                                            class="form-control" disabled /></td>
                                                                    <td><input type="text" id="exc_date-<?= $count ?>_u"
                                                                            name="exc_date-<?= $count ?>_u"
                                                                            placeholder="Activity Date & Time"
                                                                            title="Activity Date & Time"
                                                                            class="app_datetimepicker"
                                                                            value="<?= get_datetime_user($row_q_ex['exc_date']) ?>"
                                                                            style="width:150px"
                                                                            onchange="get_excursion_amount_update(this.id);">
                                                                    </td>
                                                                    <td><select id="city_name-<?= $count ?>_u"
                                                                            class="app_select2 form-control act_city"
                                                                            name="city_name-<?= $count ?>_u" title="City Name"
                                                                            style="width:150px"
                                                                            onchange="get_excursion_list(this.id);">
                                                                            <option value="<?php echo $sq_city['city_id'] ?>">
                                                                                <?php echo $sq_city['city_name'] ?></option>
                                                                            <option value="">*City</option>
                                                                        </select>
                                                                    </td>
                                                                    <td><select id="excursion-<?= $count ?>_u"
                                                                            class="app_select2 form-control"
                                                                            title="Activity Name"
                                                                            name="excursion-<?= $count ?>_u" style="width:150px"
                                                                            onchange="get_excursion_amount_update(this.id);">
                                                                            <?= quotation_excursion_options_html($row_q_ex['city_name'], $saved_exc_entry_id) ?>
                                                                        </select></td>
                                                                    <td><select name="transfer_option-<?= $count ?>_u"
                                                                            id="transfer_option-<?= $count ?>_u"
                                                                            data-toggle="tooltip"
                                                                            class="form-contrl app_select2"
                                                                            title="Transfer Option" style="width:150px"
                                                                            onchange="get_excursion_amount_update(this.id);">
                                                                            <option
                                                                                value="<?php echo $row_q_ex['transfer_option'] ?>">
                                                                                <?php echo $row_q_ex['transfer_option'] ?>
                                                                            </option>
                                                                            <option value="Private Transfer">Private Transfer
                                                                            </option>
                                                                            <option value="Without Transfer">Without Transfer
                                                                            </option>
                                                                            <option value="Sharing Transfer">Sharing Transfer
                                                                            </option>
                                                                            <option value="SIC">SIC</option>
                                                                        </select></td>
                                                                    <td><input type="number" id="adult-<?= $count ?>_u" name="adult-<?= $count ?>_u"
                                                                            placeholder="Adult(s)" title="Adult(s)"
                                                                            style="width:150px"
                                                                            onchange="get_excursion_amount_update('chk_tour_group-<?= $count ?>_u');validate_balance(this.id);validate_pax_count(this.id,'Adult');"
                                                                            value="<?= $row_q_ex['adult'] ?>"></td>
                                                                    <td><input type="number" id="child-<?= $count ?>_u" name="child-<?= $count ?>_u"
                                                                            placeholder="Child With-Bed" title="Child With-Bed"
                                                                            style="width:150px"
                                                                            onchange="get_excursion_amount_update('chk_tour_group-<?= $count ?>_u');validate_balance(this.id);validate_pax_count(this.id,'ChildWithBed');"
                                                                            value="<?= $row_q_ex['chwb'] ?>"></td>
                                                                    <td><input type="number" id="childwo-<?= $count ?>_u" name="childwo-<?= $count ?>_u"
                                                                            placeholder="Child Without-Bed"
                                                                            title="Child Without-Bed" style="width:150px"
                                                                            onchange="get_excursion_amount_update('chk_tour_group-<?= $count ?>_u');validate_balance(this.id);validate_pax_count(this.id,'ChildWithoutBed');"
                                                                            value="<?= $row_q_ex['chwob'] ?>"></td>
                                                                    <td><input type="number" id="infant-<?= $count ?>_u" name="infant-<?= $count ?>_u"
                                                                            placeholder="Infant(s)" title="Infant(s)"
                                                                            style="width:150px"
                                                                            onchange="get_excursion_amount_update('chk_tour_group-<?= $count ?>_u');validate_balance(this.id);validate_pax_count(this.id,'Infant');"
                                                                            value="<?= $row_q_ex['infant'] ?>"></td>
                                                                    <td style="display:none"><input type="text"
                                                                            id="excursion_amount-<?= $count ?>_u"
                                                                            name="excursion_amount-<?= $count ?>_u"
                                                                            onchange="validate_balance(this.id)"
                                                                            placeholder="Activity Amount"
                                                                            title="Activity Amount" style="width:150px"
                                                                            value="<?php echo $row_q_ex['excursion_amount'] ?>">
                                                                    </td>
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
                                                                    <td><select name="vehicle_id-<?= $count ?>_u" id="vehicle_id-<?= $count ?>_u"
                                                                            style="width: 155px"
                                                                            class="form-control app_select2"
                                                                            title="Select Vehicle"
                                                                            onchange="get_excursion_amount_update(this.id);">
                                                                        <?php
                                                                        $saved_vehicle_id = isset($row_q_ex['vehicle_id']) ? $row_q_ex['vehicle_id'] : '';
                                                                        if ($saved_vehicle_id != '' && $saved_vehicle_id != '0') {
                                                                            $sq_vehicle_sel = mysqli_fetch_assoc(mysqlQuery("select * from b2b_transfer_master where entry_id='$saved_vehicle_id'"));
                                                                            if ($sq_vehicle_sel) {
                                                                        ?>
                                                                                <option value="<?= $sq_vehicle_sel['entry_id'] ?>" selected>
                                                                                    <?= $sq_vehicle_sel['vehicle_name'] ?></option>
                                                                        <?php }
                                                                        } ?>
                                                                        <option value=''>Select Vehicle</option>
                                                                        <?php
                                                                        $sq_vehicle = mysqlQuery("select * from b2b_transfer_master where status='Active' order by vehicle_name");
                                                                        while ($row_vehicle = mysqli_fetch_assoc($sq_vehicle)) {
                                                                        ?>
                                                                            <option value="<?= $row_vehicle['entry_id'] ?>">
                                                                                <?= $row_vehicle['vehicle_name'] ?></option>
                                                                        <?php } ?>
                                                                    </select></td>
                                                                    <td><input type="number" id="no_vehicles-<?= $count ?>_u" name="no_vehicles-<?= $count ?>_u" placeholder="No.Of Vehicles" title="No.Of Vehicles" style="width:150px" onchange="get_excursion_amount_update(this.id);" value="<?php echo $row_q_ex['vehicles'] ?>"></td>
                                                                    <td class="hidden"><input type="hidden" id="excursion_id-<?= $count ?>_u" value="<?= $row_q_ex['id'] ?>"></td>
                                                                </tr>
                                                                <script>
                                                                    $('#exc_date-<?= $count ?>_u').datetimepicker({
                                                                        format: "d-m-Y H:i"
                                                                    });
                                                                </script>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                        <script>
                                                            $(function () {
                                                                $('#tbl_package_tour_quotation_dynamic_excursion .act_city').each(function () {
                                                                    if (!$(this).data('select2')) {
                                                                        city_lzloading(this, '*City');
                                                                    }
                                                                });
                                                                $('#tbl_package_tour_quotation_dynamic_excursion select[id^="excursion-"], #tbl_package_tour_quotation_dynamic_excursion select[id^="transfer_option-"]').each(function () {
                                                                    if (!$(this).data('select2')) {
                                                                        $(this).select2({ width: '150px' });
                                                                    }
                                                                });
                                                                $('#tbl_package_tour_quotation_dynamic_excursion select[id^="vehicle_id-"]').each(function () {
                                                                    if (!$(this).data('select2')) {
                                                                        $(this).select2({ width: '155px' });
                                                                    }
                                                                });
                                                            });
                                                        </script>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Activity End Information -->
                        <!-- Train Information -->
                        <div class="accordion_content main_block mg_bt_10 <?= $hide_train ?>">
                            <div class="panel panel-default main_block">
                                <div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
                                    <div class="Normal main_block" role="button" data-toggle="collapse"
                                        data-parent="#accordion" href="#collapse1" aria-expanded="true"
                                        aria-controls="collapse1" id="collapsed1">
                                        <div class="col-md-12"><span>Train Information</span></div>
                                    </div>
                                </div>
                                <div id="collapse1" class="panel-collapse collapse main_block" role="tabpanel"
                                    aria-labelledby="heading1">
                                    <div class="panel-body">
                                        <?php include_once('train_tbl.php'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Cruise Information -->
                        <div class="accordion_content main_block <?= $hide_cruise ?>">
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
                                        <?php include_once('cruise_tbl.php'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


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

<?= end_panel(); ?>


<script>
    $(document).ready(function () {
        destinationLoading(".pickup_from", 'Pickup Location');
        destinationLoading(".drop_to", 'Drop-off Location');
    });
    city_lzloading('.city_name');

    $('#tbl_package_tour_quotation_dynamic_hotel_update').on('change select2:select', 'select[id^="city_name"], select[name^="city_name"]', function() {
        if (typeof hotel_name_list_load === 'function') {
            hotel_name_list_load(this.id);
        }
    });

    // Deselecting hotels must drop that package type from costing
    $('#tbl_package_tour_quotation_dynamic_hotel_update').on('change', 'input[type="checkbox"][id^="chk_hotel"]', function() {
        if (typeof get_hotel_cost === 'function') {
            get_hotel_cost();
        } else if (typeof quotationRefreshPpCostingFromTravelStaySelections === 'function') {
            quotationRefreshPpCostingFromTravelStaySelections({ force: true, preserveIfEmpty: true });
        } else if (typeof quotationSyncCostingUiToHotelSelection === 'function') {
            quotationSyncCostingUiToHotelSelection();
        }
    });

    // Deselect transport / activity / flight → remove those costs from PP costing
    // Transport cost AJAX already refreshes PP in get_transport_cost_update success.
    $(document).on('change', '#tbl_package_tour_quotation_dynamic_excursion input[type="checkbox"]', function () {
        // Prefer full-table recalc so unchecked rows are zeroed consistently
        if (typeof get_excursion_amount === 'function') {
            get_excursion_amount();
        } else if (typeof get_excursion_amount_update === 'function' && this.id) {
            get_excursion_amount_update(this.id);
        }
    });
    $(document).on('change', '#tbl_package_tour_quotation_dynamic_plane input[type="checkbox"]', function () {
        if (typeof quotationRefreshPpCostingFromTravelStaySelections === 'function') {
            quotationRefreshPpCostingFromTravelStaySelections({ force: true, preserveIfEmpty: true });
        }
    });
    
    // Preserve tab2 itinerary data for the final update step
    $(document).ready(function() {
        var storedData = sessionStorage.getItem('tab2_form_data');
        if (storedData && !window.tab2FormData) {
            try {
                var formData = JSON.parse(storedData);
                console.log("Loading stored form data:", formData);
                window.tab2FormData = formData;
            } catch (e) {
                console.error("Error parsing stored form data:", e);
            }
        }
    });
    // App_accordion
    jQuery(document).ready(function() {
        jQuery(".panel-heading").click(function() {
            jQuery('#accordion .panel-heading').not(this).removeClass('isOpen');
            jQuery(this).toggleClass('isOpen');
            jQuery(this).next(".panel-collapse").addClass('thePanel');
            jQuery('#accordion .panel-collapse').not('.thePanel').slideUp("slow");
            jQuery(".thePanel").slideToggle("slow").removeClass('thePanel');
        });

    });
    //Get Hotel Cost
    function get_hotel_cost(hotel_id1) {

        var hotel_id_arr = [];
        var room_cat_arr = [];
        var check_in_arr = [];
        var check_out_arr = [];
        var total_nights_arr = [];
        var total_rooms_arr = [];
        var extra_bed_arr = [];
        var package_id_arr = [];
        var meal_plan_arr = [];
        var checked_arr = [];
        var child_with_bed = $('#children_with_bed12').val() || $('#children_with_bed').val();
        var child_without_bed = $('#children_without_bed12').val() || $('#children_without_bed').val();
        var adult_count = $('#total_adult12').val() || $('#total_adult').val();
        adult_count = (adult_count == '') ? 0 : adult_count;
        child_without_bed = (child_without_bed == '') ? 0 : child_without_bed;
        child_with_bed = (child_with_bed == '') ? 0 : child_with_bed;

        var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel_update");
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
            package_id_arr.push(package_id);
            meal_plan_arr.push(meal_plan);
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
                        if (row.cells[13] && row.cells[13].childNodes[0]) {
                            row.cells[13].childNodes[0].value = 0;
                        }
                    }
                } else {
                    for (var i = 0; i < hotel_arr.length; i++) {
                        var row = table.rows[i];
                        var packageType = (typeof quotationGetHotelRowPackageType === 'function'
                            ? quotationGetHotelRowPackageType(row)
                            : (row.cells[2].childNodes[0].value));
                        if (row.cells[0].childNodes[0].checked) {
                            if (row.cells[13] && row.cells[13].childNodes[0]) {
                                row.cells[13].childNodes[0].value = hotel_arr[i]['hotel_cost'];
                            }
                            pp_arr.push({
                                'hotel_cost': hotel_arr[i]['hotel_cost'],
                                'adult_cost': hotel_arr[i]['adult_cost'],
                                'child_with_bed': hotel_arr[i]['child_with_bed'],
                                'child_without_bed': hotel_arr[i]['child_without_bed'],
                                'infant_cost': hotel_arr[i]['infant_cost'] || 0,
                                'package_id': hotel_arr[i]['package_id'],
                                'flag': hotel_arr[i]['flag'],
                                'package_type': packageType,
                                'checked': true
                            });
                        } else {
                            if (row.cells[13] && row.cells[13].childNodes[0]) {
                                row.cells[13].childNodes[0].value = 0;
                            }
                            pp_arr.push({
                                'hotel_cost': 0,
                                'adult_cost': 0,
                                'child_with_bed': 0,
                                'child_without_bed': 0,
                                'infant_cost': 0,
                                'package_id': hotel_arr[i]['package_id'],
                                'package_type': packageType,
                                'checked': false
                            });
                        }
                    }
                }
                // Tab-4 Per person costing — only checked package types are used downstream
                $('#hotel_pp_costing').val(JSON.stringify(pp_arr));
                if (typeof quotationSyncCostingUiToHotelSelection === 'function') {
                    quotationSyncCostingUiToHotelSelection();
                }
                if (typeof quotationBuildHotelPerPersonArrFromPpCosting === 'function'
                    && typeof quotationApplyHotelTariffToPpFields === 'function') {
                    quotationApplyHotelTariffToPpFields(quotationBuildHotelPerPersonArrFromPpCosting(), { force: true });
                }
                if (typeof quotationRefreshPpCostingFromTravelStaySelections === 'function') {
                    quotationRefreshPpCostingFromTravelStaySelections({ force: true, recalcServiceCharge: true });
                } else {
                    if (typeof quotationRefreshPpActivityFromExcursion === 'function') {
                        quotationRefreshPpActivityFromExcursion({ force: true });
                    }
                    if (typeof calculateCostingCardsUpdate === 'function') {
                        calculateCostingCardsUpdate({ recalcServiceCharge: true });
                    }
                }
            }
        });
    }
    //Get Transport Cost (single-row trigger from update form fields)
    function get_transport_cost_update(id) {

        var trans_id = id.split('-');
        var transport_id_arr = [];
        var travel_date_arr = [];
        var pickup_arr = [];
        var drop_arr = [];
        var pickup_id_arr = [];
        var drop_id_arr = [];
        var vehicle_count_arr = [];
        var ppackage_id_arr = [];
        var ppackage_name_arr = [];

        var transport_id = $('#transport_vehicle-' + trans_id[1]).val();
        var travel_date = $('#transport_start_date-' + trans_id[1]).val();

        try {
            var pickup = $('#pickup_from-' + trans_id[1]).val();
            var drop = $('#drop_to-' + trans_id[1]).val();
            var pickup_type = pickup.split('-')[0];
            var drop_type = drop.split('-')[0];
            var pickup1 = pickup.split("-")[1];
            var drop1 = drop.split("-")[1];
        } catch (e) {
            console.log(e);
        }
        var vehicle_count = $('#no_vehicles-' + trans_id[1]).val();
        var pname = $('#package_name-' + trans_id[1]).val();
        var pid = $('#package_id-' + trans_id[1]).val();

        // Unchecked → clear this row cost and refresh PP (allow wipe)
        var chk = document.getElementById("chk_transport-" + trans_id[1]);
        if (chk && chk.checked !== true) {
            $('#transport_cost-' + trans_id[1]).val(0);
            if (typeof quotationRefreshPpCostingFromTravelStaySelections === 'function') {
                quotationRefreshPpCostingFromTravelStaySelections({ force: true, preserveIfEmpty: false });
            }
            return;
        }

        // Incomplete pickup/drop → keep existing cost; do not wipe PP transfer
        if (!transport_id || !pickup || !drop || typeof pickup1 === 'undefined' || typeof drop1 === 'undefined') {
            if (typeof quotationRefreshPpCostingFromTravelStaySelections === 'function') {
                quotationRefreshPpCostingFromTravelStaySelections({ force: true, preserveIfEmpty: true });
            }
            return;
        }

        transport_id_arr.push(transport_id);
        travel_date_arr.push(travel_date);
        pickup_arr.push(pickup1);
        drop_arr.push(drop1);
        pickup_id_arr.push(pickup_type);
        drop_id_arr.push(drop_type);
        vehicle_count_arr.push(vehicle_count);
        ppackage_id_arr.push(pid);
        ppackage_name_arr.push(pname);
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
                ppackage_name_arr: ppackage_name_arr
            },
            success: function(result) {
                var transport_arr = [];
                try {
                    transport_arr = JSON.parse(result);
                } catch (e) {
                    transport_arr = [];
                }
                var $cost = $('#transport_cost-' + trans_id[1]);
                var existingCost = parseFloat($cost.val()) || 0;
                if (transport_arr.length && transport_arr[0] && transport_arr[0]['total_cost'] != null) {
                    $cost.val(transport_arr[0]['total_cost']);
                    // Fresh tariff → force into PP transfer
                    if (typeof quotationRefreshPpCostingFromTravelStaySelections === 'function') {
                        quotationRefreshPpCostingFromTravelStaySelections({ force: true, preserveIfEmpty: false });
                    }
                } else {
                    // No matching tariff — keep existing saved/tariff cost; do not zero PP
                    if (existingCost <= 0 && typeof quotationRefreshPpCostingFromTravelStaySelections === 'function') {
                        quotationRefreshPpCostingFromTravelStaySelections({ force: true, preserveIfEmpty: true });
                    }
                }
            }
        });
    }

    /**
     * Bulk reload transport costs for all update rows (mirrors save get_transport_cost).
     * Used when tab3 initializes / travel dates sync.
     */
    function get_transport_cost() {
        var table = document.getElementById('tbl_package_tour_quotation_dynamic_transport_u');
        if (!table || !table.rows || !table.rows.length) {
            if (typeof quotationRefreshPpCostingFromTravelStaySelections === 'function') {
                quotationRefreshPpCostingFromTravelStaySelections({ force: true, preserveIfEmpty: true });
            }
            return;
        }

        var transport_id_arr = [];
        var travel_date_arr = [];
        var pickup_arr = [];
        var drop_arr = [];
        var pickup_id_arr = [];
        var drop_id_arr = [];
        var vehicle_count_arr = [];
        var ppackage_id_arr = [];
        var ppackage_name_arr = [];
        var rowMeta = [];

        for (var i = 0; i < table.rows.length; i++) {
            var row = table.rows[i];
            var checkbox = row.cells[0] && row.cells[0].querySelector
                ? row.cells[0].querySelector('input[type="checkbox"]')
                : (row.cells[0] && row.cells[0].childNodes[0]);
            var vehicleEl = row.querySelector('select[id^="transport_vehicle"]');
            var dateEl = row.querySelector('input[id^="transport_start_date"]');
            var pickupEl = row.querySelector('select[id^="pickup_from"]');
            var dropEl = row.querySelector('select[id^="drop_to"]');
            var vehicleCountEl = row.querySelector('input[id^="no_vehicles"]');
            var pkgIdEl = row.querySelector('input[id^="package_id"]');
            var pkgNameEl = row.querySelector('input[id^="package_name"]');
            var costEl = row.querySelector('input[id^="transport_cost"]');

            var transport_id = vehicleEl ? vehicleEl.value : '';
            var travel_date = dateEl ? dateEl.value : '';
            var pickup = pickupEl ? pickupEl.value : '';
            var drop = dropEl ? dropEl.value : '';
            var pickupParts = pickup ? String(pickup).split('-') : [];
            var dropParts = drop ? String(drop).split('-') : [];
            var pickup_type = pickupParts[0] || '';
            var drop_type = dropParts[0] || '';
            var pickup1 = pickupParts.length > 1 ? pickupParts.slice(1).join('-') : '';
            var drop1 = dropParts.length > 1 ? dropParts.slice(1).join('-') : '';

            rowMeta.push({
                checked: !!(checkbox && checkbox.checked),
                costEl: costEl,
                hasRoute: !!(transport_id && pickup1 && drop1)
            });

            transport_id_arr.push(transport_id);
            travel_date_arr.push(travel_date);
            pickup_arr.push(pickup1);
            drop_arr.push(drop1);
            pickup_id_arr.push(pickup_type);
            drop_id_arr.push(drop_type);
            vehicle_count_arr.push(vehicleCountEl ? vehicleCountEl.value : '');
            ppackage_id_arr.push(pkgIdEl ? pkgIdEl.value : '');
            ppackage_name_arr.push(pkgNameEl ? pkgNameEl.value : '');
        }

        var anyRoute = rowMeta.some(function (m) { return m.checked && m.hasRoute; });
        if (!anyRoute) {
            // Still clear unchecked rows; preserve PP if costs already present
            for (var u = 0; u < rowMeta.length; u++) {
                if (!rowMeta[u].checked && rowMeta[u].costEl) {
                    rowMeta[u].costEl.value = 0;
                }
            }
            if (typeof quotationRefreshPpCostingFromTravelStaySelections === 'function') {
                quotationRefreshPpCostingFromTravelStaySelections({ force: true, preserveIfEmpty: true });
            }
            return;
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
                ppackage_name_arr: ppackage_name_arr
            },
            success: function (result) {
                var transport_arr = [];
                try {
                    transport_arr = JSON.parse(result);
                } catch (e) {
                    transport_arr = [];
                }
                var appliedTariff = false;
                for (var i = 0; i < rowMeta.length; i++) {
                    var meta = rowMeta[i];
                    if (!meta.costEl) continue;
                    if (!meta.checked) {
                        meta.costEl.value = 0;
                        continue;
                    }
                    var rowResult = transport_arr[i];
                    if (rowResult && rowResult['total_cost'] != null && meta.hasRoute) {
                        meta.costEl.value = rowResult['total_cost'];
                        appliedTariff = true;
                    }
                    // else keep existing saved cost
                }
                try {
                    var pp_arr = [];
                    for (var p = 0; p < rowMeta.length; p++) {
                        pp_arr.push({
                            total_cost: metaCost(rowMeta[p]),
                            checked: rowMeta[p].checked
                        });
                    }
                    $('#travel_pp_costing').val(JSON.stringify(pp_arr));
                } catch (e2) {}
                if (typeof quotationRefreshPpCostingFromTravelStaySelections === 'function') {
                    quotationRefreshPpCostingFromTravelStaySelections({
                        force: true,
                        preserveIfEmpty: !appliedTariff
                    });
                }
            }
        });

        function metaCost(meta) {
            return meta.costEl ? (parseFloat(meta.costEl.value) || 0) : 0;
        }
    }
    $(function() {

        $('#frm_tab3').validate({

            rules: {},
            submitHandler: function(form) {


                //Train Info
                var table = document.getElementById("tbl_package_tour_quotation_dynamic_train");

                var rowCount = table.rows.length;



                for (var i = 0; i < rowCount; i++)

                {

                    var row = table.rows[i];



                    if (row.cells[0].childNodes[0].checked)

                    {

                        var train_from_location1 = row.cells[2].childNodes[0].value;

                        var train_to_location1 = row.cells[3].childNodes[0].value;

                        var train_class = row.cells[4].childNodes[0].value;

                        var train_arrival_date = row.cells[5].childNodes[0].value;

                        var train_departure_date = row.cells[6].childNodes[0].value;



                        if (row.cells[7] && row.cells[7].childNodes[0]) {

                            var train_id = row.cells[7].childNodes[0].value;

                        } else {

                            var train_id = "";

                        }

                        if (train_from_location1 == "")

                        {

                            error_msg_alert('Enter train from location in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_train').parent('div').closest(
                                '.accordion_content').addClass("indicator");

                            return false;

                        }



                        if (train_to_location1 == "")

                        {

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
                        var plane_from_city = row.cells[2].childNodes[0].value;
                        var plane_from_location1 = row.cells[3].childNodes[0].value;
                        var airline_name = row.cells[4].childNodes[0].value;
                        var plane_class = row.cells[5].childNodes[0].value;
                        var dapart1 = row.cells[6].childNodes[0].value;
                        var arraval1 = row.cells[7].childNodes[0].value;
                        var plane_to_city = row.cells[8].childNodes[0].value;
                        var plane_to_location1 = row.cells[9].childNodes[0].value;

                        if (row.cells[10] && row.cells[10].childNodes[0]) {
                            var plane_id = row.cells[10].childNodes[0].value;
                        } else {
                            var plane_id = "";
                        }

                        if (plane_from_location1 == "") {
                            error_msg_alert('Enter flight from sector in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_plane').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (plane_to_location1 == "") {
                            error_msg_alert('Enter flight to sector in row' + (i + 1));
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
                var cruise_departure_date_arr = [];
                var cruise_arrival_date_arr = [];
                var route_arr = [];
                var cabin_arr = [];
                var sharing_arr = [];
                var c_entry_id_arr = [];

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


                        if (row.cells[7] && row.cells[7].childNodes[0]) {
                            var c_entry_id = row.cells[7].childNodes[0].value;
                        } else {
                            var c_entry_id = "";
                        }

                        if (cruise_from_date == "") {
                            error_msg_alert('Enter Cruise Departure datetime in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_dynamic_cruise_quotation').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }

                        if (cruise_to_date == "") {
                            error_msg_alert('Enter Cruise Arrival datetime  in row' + (i + 1));
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
                            error_msg_alert('Enter Cabin in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_dynamic_cruise_quotation').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        cruise_departure_date_arr.push(cruise_from_date);
                        cruise_arrival_date_arr.push(cruise_to_date);
                        route_arr.push(route);
                        cabin_arr.push(cabin);
                        sharing_arr.push(sharing);
                        c_entry_id_arr.push(c_entry_id);

                    }
                }

                //Hotel Information
                var package_id_arr = [];
                var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel_update");
                var rowCount = table.rows.length;

                for (var i = 0; i < rowCount; i++) {

                    var row = table.rows[i];
                    if (row.cells[0].childNodes[0].checked) {

                        var package_type = row.cells[2].childNodes[0].value;
                        var city_name = row.cells[3].childNodes[0].value;
                        var hotel_id = row.cells[4].childNodes[0].value;
                        var hotel_cat = row.cells[5].childNodes[0].value;
                        var check_in = row.cells[6].childNodes[0].value;
                        var checkout = row.cells[7].childNodes[0].value;
                        var hotel_stay_days1 = row.cells[9].childNodes[0].value;
                        var total_rooms = row.cells[10].childNodes[0].value;
                        var package_name1 = row.cells[12].childNodes[0].value;
                        var hotel_cost = row.cells[13].childNodes[0].value;
                        var package_id1 = row.cells[14].childNodes[0].value;

                        if (package_type == "") {
                            error_msg_alert('Select package type in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel_update').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (city_name == "") {
                            error_msg_alert('Select hotel city in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel_update').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }

                        if (hotel_id == "") {
                            error_msg_alert('Enter hotel in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel_update').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (hotel_cat == "") {
                            error_msg_alert('Enter Room Category in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel_update').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (check_in == "") {
                            error_msg_alert('Select Check-In date in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel_update').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }

                        if (checkout == "") {
                            error_msg_alert('Select Check-Out date in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel_update').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }

                        if (hotel_stay_days1 == "") {
                            error_msg_alert('Enter hotel total days in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel_update').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (total_rooms == "") {
                            error_msg_alert('Enter hotel total rooms in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_hotel_update').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        package_id_arr.push(package_id1);
                    }

                }

                //Transport Information 
                var package_id_arr1 = [];
                var table = document.getElementById("tbl_package_tour_quotation_dynamic_transport_u");

                var rowCount = table.rows.length;
                for (var i = 0; i < rowCount; i++) {

                    var row = table.rows[i];
                    if (row.cells[0].childNodes[0].checked) {

                        var transport_id = row.cells[2].childNodes[0].value;
                        var travel_date = row.cells[3].childNodes[0].value;
                        var end_date = row.cells[4].childNodes[0].value;
                        var service_duration = row.cells[7].childNodes[0].value;
                        var vehicle_count = row.cells[8].childNodes[0].value;
                        var vehicle_cost = row.cells[9].childNodes[0].value;
                        var pname = row.cells[10].childNodes[0].value;
                        var package_id1 = row.cells[11].childNodes[0].value;
                        var pickup = row.cells[5].childNodes[0].value;
                        var drop = row.cells[6].childNodes[0].value;

                        if (transport_id == "") {
                            error_msg_alert('Select Transport Vehicle in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport_u').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (travel_date == "") {
                            error_msg_alert('Enter start date in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport_u').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (end_date == "") {
                            error_msg_alert('Enter end date in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport_u').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (pickup == "") {
                            error_msg_alert('Select pickup location in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport_u').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (drop == "") {
                            error_msg_alert('Select drop location in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport_u').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (service_duration == "") {
                            error_msg_alert('Select service duration in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport_u').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (vehicle_count == "") {
                            error_msg_alert('Enter vehicle count in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport_u').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (vehicle_cost == "") {
                            error_msg_alert('Enter vehicle cost in row' + (i + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_transport_u').parent('div').closest(
                                '.accordion_content').addClass("indicator");
                            return false;
                        }
                        package_id_arr1.push(package_id1);
                    }
                }


                var table = document.getElementById("tbl_package_tour_quotation_dynamic_excursion");
                var rowCount = table.rows.length;
                var total_amount = 0;
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
                            $('#tbl_package_tour_quotation_dynamic_excursion').parent('div').closest('.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (city_name == "") {
                            error_msg_alert('Select Activity city in row' + (e + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_excursion').parent('div').closest('.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (excursion_name == "") {
                            error_msg_alert('Select Activity name in row' + (e + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_excursion').parent('div').closest('.accordion_content').addClass("indicator");
                            return false;
                        }
                        if (transfer_option == "") {
                            error_msg_alert('Select Transfer option in row' + (e + 1));
                            $('.accordion_content').removeClass("indicator");
                            $('#tbl_package_tour_quotation_dynamic_excursion').parent('div').closest('.accordion_content').addClass("indicator");
                            return false;
                        }

                        var e_amount = row.cells[4].childNodes[0].value;
                        total_amount = parseFloat(total_amount) + parseFloat(e_amount);
                    }
                }

                $('.accordion_content').removeClass("indicator");
                $('#tab3_head').addClass('done');
                $('#tab4_head').addClass('active');
                $('.bk_tab').removeClass('active');
                $('#tab4').addClass('active');
                if (typeof quotationSyncCostingUiToHotelSelection === 'function') {
                    quotationSyncCostingUiToHotelSelection();
                }
                if (typeof costing_reflect === 'function') {
                    costing_reflect();
                }
                $('html, body').animate({
                    scrollTop: $('.bk_tab_head').offset().top
                }, 200);
            }
        });
    });

    function switch_to_tab2() {
        $('#tab3_head').removeClass('active');
        $('#tab2_head').addClass('active');
        $('.bk_tab').removeClass('active');
        $('#tab2').addClass('active');
        $('html, body').animate({
            scrollTop: $('.bk_tab_head').offset().top
        }, 200);
    }

    // Initialize tab3 when it becomes active
    $(document).ready(function() {
        // Check if tab3 is active and initialize data
        if ($('#tab3').hasClass('active')) {
            // Load hotel and transport data
            setTimeout(function() {
                if (typeof quotationSyncExcursionDefaultsFromTravel === 'function') {
                    quotationSyncExcursionDefaultsFromTravel({ forcePax: false, onlyMissing: true });
                }
                get_hotel_cost();
                get_excursion_amount();
                get_transport_cost();
            }, 500);
        }
    });

    // Initialize when tab3 becomes active
    $('#tab3_head').on('click', function() {
        setTimeout(function() {
            if (typeof syncQuotationTravelStayDates === 'function') {
                syncQuotationTravelStayDates();
            }
            if (typeof quotationSyncExcursionDefaultsFromTravel === 'function') {
                quotationSyncExcursionDefaultsFromTravel({ forcePax: false, onlyMissing: true });
            }
            get_hotel_cost();
            get_excursion_amount();
            get_transport_cost();
        }, 300);
    });
</script>