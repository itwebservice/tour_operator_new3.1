<?php
include '../../../../model/model.php';

function _get_departure($departure_or_arrival) {
	$str = $departure_or_arrival;

	$entries = strpos($str, ',') !== false ? explode(',', $str) : [$str];

	foreach ($entries as $entry) {
		$entry = trim($entry);

		if (preg_match('/^([A-Z0-9\-]+)\s+([A-Z]{3}-[A-Z]{3})\s+(.+)$/i', $entry, $matches)) {
			$flight_no = $matches[1]; // e.g. 6E-6764
			$route = $matches[2];     // e.g. GAU-MAA
			$date = $matches[3];      // e.g. 2 Apr

			return explode('-', $route)[0];
		} else {
			return "-";
		}
	}
}

function _get_arrival($departure_or_arrival) {
	$str = $departure_or_arrival;

	$entries = strpos($str, ',') !== false ? explode(',', $str) : [$str];

	foreach ($entries as $entry) {
		$entry = trim($entry);

		if (preg_match('/^([A-Z0-9\-]+)\s+([A-Z]{3}-[A-Z]{3})\s+(.+)$/i', $entry, $matches)) {
			$flight_no = $matches[1]; // e.g. 6E-6764
			$route = $matches[2];     // e.g. GAU-MAA
			$date = $matches[3];      // e.g. 2 Apr

			return explode('-', $route)[1];
		} else {
			return "-";
		}
	}
}

function _get_arrival_date($departure_or_arrival) {
	$str = $departure_or_arrival;

	$entries = strpos($str, ',') !== false ? explode(',', $str) : [$str];

	foreach ($entries as $entry) {
		$entry = trim($entry);

		if (preg_match('/^([A-Z0-9\-]+)\s+([A-Z]{3}-[A-Z]{3})\s+(.+)$/i', $entry, $matches)) {
			$flight_no = $matches[1]; // e.g. 6E-6764
			$route = $matches[2];     // e.g. GAU-MAA
			$date = $matches[3];      // e.g. 2 Apr

			return $date;
		} else {
			return "-";
		}
	}
}

function _get_operated_by($departure_or_arrival) {
	$str = $departure_or_arrival;

	$entries = strpos($str, ',') !== false ? explode(',', $str) : [$str];

	foreach ($entries as $entry) {
		$entry = trim($entry);

		if (preg_match('/^([A-Z0-9\-]+)\s+([A-Z]{3}-[A-Z]{3})\s+(.+)$/i', $entry, $matches)) {
			$flight_no = $matches[1]; // e.g. 6E-6764
			$route = $matches[2];     // e.g. GAU-MAA
			$date = $matches[3];      // e.g. 2 Apr

			return explode('-', $flight_no)[0];
		} else {
			return "-";
		}
	}
}

function _get_flight_no($departure_or_arrival) {
    $str = $departure_or_arrival;
    $entries = strpos($str, ',') !== false ? explode(',', $str) : [$str];
    foreach ($entries as $entry) {
        $entry = trim($entry);
        if (preg_match('/^([A-Z0-9]+)-([0-9]+)\s+[A-Z]{3}-[A-Z]{3}\s+.+$/i', $entry, $matches)) {
            // $matches[1] = Airline code (e.g. 6E)
            // $matches[2] = Flight number digits (e.g. 6764)
            return $matches[2]; // Return only the number part
        } else {
            return "-";
        }
    }
}

function _get_arrival_for_round_trip($data) {
    $result = '';
    if (!empty($data)) {
        $entries = explode(',', $data);
        if (isset($entries[1])) {
            $parts = explode(' ', trim($entries[1]));
            if (isset($parts[1])) {
                $route = $parts[1]; // e.g., DEL-BLR
                $route_parts = explode('-', $route);
                if (isset($route_parts[0])) {
                    $result = $route_parts[0]; // DEL
                }
            }
        }
    }
    return $result;
}

function _get_arrival_date_for_round_trip($data) {
    $result = '';
    if (!empty($data)) {
        $entries = explode(',', $data);
        if (isset($entries[1])) {
            $parts = explode(' ', trim($entries[1]));
            if (isset($parts[2]) && isset($parts[3])) {
                $result = $parts[2] . ' ' . $parts[3]; // 10 Jun
            }
        }
    }
    return $result;
}

function _get_arrival_airport_date_for_round_trip($data) {
    $result = '';
    if (!empty($data)) {
        $entries = explode(',', $data);
        if (isset($entries[1])) {
            $parts = explode(' ', trim($entries[1]));
            if (isset($parts[1])) {
                $routeParts = explode('-', $parts[1]);
                if (isset($routeParts[1])) {
                    $result = $routeParts[1]; // Extracts BLR
                }
            }
        }
    }
    return $result;
}

function _get_flight_no_for_round_trip($data) {
    $flight_number_digits = '';
    if (!empty($data)) {
        $entries = explode(',', $data);
        if (isset($entries[1])) {
            $parts = explode(' ', trim($entries[1]));
            if (isset($parts[0])) {
                // $parts[0] is something like "6E-1234"
                // Extract digits after the dash
                if (preg_match('/-(\d+)/', $parts[0], $matches)) {
                    $flight_number_digits = $matches[1]; // 1234
                }
            }
        }
    }
    return $flight_number_digits;
}

function _get_operated_for_round_trip($data) {
    $operator_code = '';
    if (!empty($data)) {
        $entries = explode(',', $data);
        if (isset($entries[1])) {
            $parts = explode(' ', trim($entries[1]));
            if (isset($parts[0])) {
                // $parts[0] is something like "6E-1234"
                // Extract airline code before the dash
                if (preg_match('/^([A-Z0-9]+)-/', $parts[0], $matches)) {
                    $operator_code = $matches[1]; // 6E
                }
            }
        }
    }
    return $operator_code;
}

function _get_arrival_for_multi_city_trip($departure_or_arrival) {
    $legs = explode(',', $departure_or_arrival);
    $last_leg = trim(end($legs)); 
    if (preg_match('/\s([A-Z]{3})-[A-Z]{3}\s/', $last_leg, $matches)) {
        return $matches[1]; 
    }
    return null;
}






function _get_arrival_date_for_multi_city_trip($departure_or_arrival) {
    $legs = explode(',', $departure_or_arrival);
    $last_leg = trim(end($legs)); 
    if (preg_match('/\b(\d{1,2}\s+[A-Za-z]{3})$/', $last_leg, $matches)) {
        return $matches[1]; 
    }
    return null;
}

function _get_arrival__for_multi_city_trip($departure_or_arrival) {
    $legs = explode(',', $departure_or_arrival);
    $last_leg = trim(end($legs)); 
    if (preg_match('/-([A-Z]{3})\s/', $last_leg, $matches)) {
        return $matches[1];
    }
    return null;
}

function _get_flight_no_for_multi_city_trip($trip_string) {
    $legs = explode(',', $trip_string);
    $last_leg = trim(end($legs)); 
    if (preg_match('/\b[A-Z0-9]+-([0-9]+)\b/', $last_leg, $matches)) {
        return $matches[1]; 
    }
    return null;
}

function _get_operated_for_multi_city_trip($departure_or_arrival) {
    $legs = explode(',', $departure_or_arrival);
    $last_leg = trim(end($legs)); 
    if (preg_match('/^([A-Z0-9]+)-[0-9]+/', $last_leg, $matches)) {
        return $matches[1];
    }
    return null;
}

function _get_flight_section($sector, $type){
	if($type==1){
		return explode('-', $sector)[0];
	} else {
		return explode('-', $sector)[1];
	}
}

function _get_operator_no_($flight_no_with_operator) {
    return substr($flight_no_with_operator, 0, 2);
}

function _get_flight_no_($flight_no_with_operator) {
    return substr($flight_no_with_operator, 2);
}

function _get_airline_name($airline_code) {
    $result = mysqlQuery("SELECT * FROM airline_master WHERE airport_code = '$airline_code'");
    $row = mysqli_fetch_assoc($result);
    return $row['airline_name'] ? $row['airline_name'] : '';
}



$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$emp_id = $_SESSION['emp_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$financial_year_id = $_SESSION['financial_year_id'];
$flight_details = json_decode($_POST['flight_details']);
$branch_status = isset($_POST['branch_status']) ? $_POST['branch_status'] : '';


// Harshit Code Start 03-06-2025
$first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
$middle_name = isset($_POST['middle_name']) ? $_POST['middle_name'] : '';
$last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
$journey_type = isset($_POST['journey_type']) ? $_POST['journey_type'] : '';
$flight_fair_amount = isset($_POST['flight_fair_amount']) ? $_POST['flight_fair_amount'] : '';
$flight_travel_date = isset($_POST['flight_travel_date']) ? $_POST['flight_travel_date'] : '';
$flight_carrier = isset($_POST['flight_carrier']) ? $_POST['flight_carrier'] : '';

$gds_pnr = isset($_POST['gds_pnr']) ? $_POST['gds_pnr'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : 'save';
$departure = _get_departure(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');
$arrival = _get_arrival(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');
$arrival_date = _get_arrival_date(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');
$operated_by = _get_operated_by(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');
$current_flight_no = _get_flight_no(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');
$current_flight_duration = isset($_POST['flight_duration']) ? $_POST['flight_duration'] : '';

$arrival_for_round_trip =  _get_arrival_for_round_trip(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');
$arrival_date_for_round_trip =  _get_arrival_date_for_round_trip(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');
$arrival_airport_for_round_trip =  _get_arrival_airport_date_for_round_trip(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');
$flight_no_for_round_trip =  _get_flight_no_for_round_trip(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');
$operated_for_round_trip =  _get_operated_for_round_trip(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');

$arrival_for_multi_city_trip =  _get_arrival_for_multi_city_trip(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');
$arrival_date_for_multi_city_trip =  _get_arrival_date_for_multi_city_trip(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');
$arrival_airport_for_multi_city_trip =  _get_arrival__for_multi_city_trip(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');
$flight_no_for_multi_city_trip =  _get_flight_no_for_multi_city_trip(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');
$operated_for_multi_city_trip =  _get_operated_for_multi_city_trip(isset($_POST['departure_or_arrival']) ? $_POST['departure_or_arrival'] : '');
$flight_first_section = _get_flight_section(isset($_POST['flight_sector']) ? $_POST['flight_sector'] : '', 1);
$flight_second_section = _get_flight_section(isset($_POST['flight_sector']) ? $_POST['flight_sector'] : '', 2);
$selected_portal = isset($_POST['selected_portal']) ? $_POST['selected_portal'] : '';
$_flight_operator_no = _get_operator_no_(isset($_POST['flight_no_with_operator']) ? $_POST['flight_no_with_operator'] : '');
$_flight_no = _get_flight_no_(isset($_POST['flight_no_with_operator']) ? $_POST['flight_no_with_operator'] : '');
$fair_amount = isset($_POST['fair_amount']) ? $_POST['fair_amount'] : '';
$airline_code = isset($_POST['airline_code']) ? $_POST['airline_code'] : '';
$_flight_from = isset($_POST['flight_from']) ? $_POST['flight_from'] : '';
$_flight_to = isset($_POST['flight_to']) ? $_POST['flight_to'] : '';
$_flight_d_date = isset($_POST['flight_d_date']) ? $_POST['flight_d_date'] : '';
$_flight_d_time = isset($_POST['flight_d_time']) ? $_POST['flight_d_time'] : '';
$_flight_a_date = isset($_POST['flight_d_date']) ? $_POST['flight_a_date'] : '';
$_flight_a_time = isset($_POST['flight_d_time']) ? $_POST['flight_a_time'] : '';
$_flight_status = isset($_POST['flight_status']) ? $_POST['flight_status'] : '';
$_flight_class = isset($_POST['flight_class']) ? $_POST['flight_class'] : '';
$__flight_no = isset($_POST['flight_no_with_operator']) ? $_POST['flight_no_with_operator'] : '';

// Harshit Code End 03-06-2025


$count = isset($_POST['count']) ? $_POST['count'] : '';
$pass_entry_id = isset($_POST['entry_id']) ? $_POST['entry_id'] : '';

$type_of_tour = (isset($flight_details[0])) ? $flight_details[0]->type_of_tour : '';
$departure_datetime_arr = (isset($flight_details[0])) ? $flight_details[0]->departure_datetime_arr : [];
$loop_items = (isset($flight_details[0]->departure_datetime_arr)) ? sizeof($flight_details[0]->departure_datetime_arr) : '1';
$button_name = ($type == 'update') ? 'Update' : 'Save';
$status = '';
if($pass_entry_id!=''){
	$sq_pass = mysqli_fetch_assoc(mysqlQuery("select * from ticket_master_entries where entry_id='$pass_entry_id'"));
	$status = $sq_pass['status'];
}
?>
<div class="modal fade" id="flight_details_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document" style="min-width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">
							Add Flight Ticket Details for <?= htmlspecialchars($first_name . ' ' . $middle_name . ' ' . $last_name) ?>
						</h4>	
			</div>
			<div class="modal-body">
                <form id="frm_flight_details">
					<input type="hidden" id="count" name="count" value="<?= $count ?>" />
					<input type="hidden" id="type" name="type" value="<?= $type ?>" />
					<?php
					if($count=='1' || $count=='1u'){
						?>
						<div class="row mg_bt_20">
							<div class="col-md-3 hidden">
								<select name="quotation_id" id="quotation_id" style="width:100%" onchange="get_quotation_details(this)" class="form-control">
								<option value="">Select Quotation</option>
								<?php
								if($role == 'Admin'){
									$query = "SELECT * FROM `flight_quotation_master` where status='1' order by quotation_id desc";
								}
								else{
									if($branch_status=='yes'){
										if($role=='Branch Admin' || $role=='Accountant' || $role_id>'7'){
										$query = "select * from flight_quotation_master where status='1' and branch_admin_id='$branch_admin_id' order by quotation_id desc";
										}
										elseif($role!='Admin' && $role!='Branch Admin' && $role_id!='7' && $role_id<'7'){
										$query = "select * from flight_quotation_master where status='1' and emp_id='$emp_id' and branch_admin_id='$branch_admin_id' order by quotation_id desc";
										}
									}
									elseif($role!='Admin' && $role!='Branch Admin' && $role_id!='7' && $role_id<'7'){
									$query = "select * from flight_quotation_master where status='1' and emp_id='$emp_id' order by quotation_id desc";
									}  
									elseif($role=='Branch Admin' || $role=='Accountant' || $role_id>'7'){
										$query = "select * from flight_quotation_master where status='1' order by quotation_id desc";
									}
								}  
								$sq_enq = mysqlQuery($query);   
								while($row_enq = mysqli_fetch_assoc($sq_enq)){
									$quotation_date = $row_enq['quotation_date'];
									$yr = explode("-", $quotation_date);
									$year =$yr[0];
									?>
									<option value="<?= $row_enq['quotation_id'] ?>"><?= get_quotation_id($row_enq['quotation_id'],$year ).": ".$row_enq['customer_name'] ?></option>
									<?php
								}
								?>
								</select>
							</div>
						</div>
					<?php } ?>
                    <div class="row mg_bt_20">
                        <div class="col-md-8 col-sm-12 col-xs-12">
							<strong>Type Of Trip :</strong>&nbsp;&nbsp;&nbsp;
							<?php $chk = ($journey_type=="One Way") ? "checked" : "" ?>
							<input  type="radio" name="type_of_tour" id="type_of_tour-one_way" value="One Way"  <?= $chk ?>>&nbsp;&nbsp;<label for="type_of_tour-one_way">One Way</label>
							&nbsp;&nbsp;&nbsp;
							
							<?php $chk = ($journey_type=="Round Trip") ? "checked" : "" ?>
							<input type="radio" name="type_of_tour" id="type_of_tour-round_trip" value="Round Trip"  <?= $chk ?>>&nbsp;&nbsp;<label for="type_of_tour-round_trip " >Round Trip</label>
							&nbsp;&nbsp;&nbsp;

							<?php $chk = ($journey_type=="Multi City") ? "checked" : "" ?>
							<input  type="radio" name="type_of_tour" id="type_of_tour-multi_city" value="Multi City"  <?= $chk ?>>&nbsp;&nbsp;<label for="type_of_tour-multi_city ">Multi City</label>
							&nbsp;&nbsp;&nbsp;
        					<button	button type="button" class="btn btn-excel btn-sm" title="Add Airport/Airline" onclick="airport_airline_save_modal()"><i class="fa fa-plus"></i></button>
                        </div>
						<?php
						if($status == ''){ ?>
							<div class="col-md-4 col-sm-12 col-xs-12 text-right">
								<button type="button" class="btn btn-info btn-sm ico_left" onclick="addDyn('div_dynamic_ticket_info'); event_airport_s();copy_values()"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Section</button>
							</div>
						<?php } ?>
                    </div>
                    <div class="dynform-wrap" id="div_dynamic_ticket_info" data-counter="<?= $loop_items ?>">
						<?php


if ($type == 'save') {
	if ($journey_type == 'One Way') {
		$loop_items = 1;
	} elseif ($journey_type == 'Round Trip') {
		$loop_items = 2;
	} elseif ($journey_type == 'Multi City') {
		$loop_items = 3;
	}
}

						for($i = 0; $i < $loop_items; $i++){
							$sq_trip_entries_count = $i+1;
							$entry_id = isset($flight_details[0]) ? $flight_details[0]->trip_entry_id_arr[$i] : 0;
							$css = '';
							if($type == 'update'){
								$button_name = 'Update';
							}
							$date = isset($departure_datetime_arr[$i]) ? $departure_datetime_arr[$i] : date('d-m-Y H:i');
							$sq_entry = mysqli_fetch_assoc(mysqlQuery("select * from ticket_trip_entries where entry_id='$entry_id'"));
							$bg = (isset($sq_entry['status']) && $sq_entry['status'] == 'Cancel') ? 'background-color:#f2dede!important;' : '';
							$disbaled = (isset($sq_entry['status']) && $sq_entry['status'] == 'Cancel') ? 'disabled' : '';
							$css =  'style="margin-top :0px !important;'.$bg.'"';
							$check_status = (isset($flight_details[0]) && $flight_details[0]->trip_data_check_arr[$i]) ? 'checked' : '';
							if($type == 'save') { $check_status = 'checked'; }
							if(isset($flight_details[0])){
								$arrival_datetime = $flight_details[0]->arrival_datetime_arr[$i];
								$departure_city = $flight_details[0]->departure_city_arr[$i];
								$from_city = $flight_details[0]->from_city_id_arr[$i];
								$dep_terminal = $flight_details[0]->departure_terminal_arr[$i];
								$to_city = $flight_details[0]->to_city_id_arr[$i];
								$arrival_city = $flight_details[0]->arrival_city_arr[$i];
								$arr_terminal = $flight_details[0]->arrival_terminal_arr[$i];
								$airline_name = $flight_details[0]->airlines_name_arr[$i];
								$classa = $flight_details[0]->class_arr[$i];
								$flight_duration = $flight_details[0]->flight_duration_arr[$i];
								$layover_time = $flight_details[0]->layover_time_arr[$i];
								$sub_category = $flight_details[0]->sub_category_arr[$i];
								$flight_no = $flight_details[0]->flight_no_arr[$i];
								$airlin_pnr = $flight_details[0]->airlin_pnr_arr[$i];
								$no_of_pieces = $flight_details[0]->no_of_pieces_arr[$i];
								$special_note = $flight_details[0]->special_note_arr[$i];
								$aircraft_type = $flight_details[0]->aircraft_type_arr[$i];
								$operating_carrier = $flight_details[0]->operating_carrier_arr[$i];
								$ticket_status = $flight_details[0]->ticket_status_arr[$i];
								$frequent_flyer = $flight_details[0]->frequent_flyer_arr[$i];
								$basic_fare = floatval($flight_details[0]->basic_fare_arr[$i]);
								$refund_type = $flight_details[0]->refund_type_arr[$i];
								$trip_entry_id = $flight_details[0]->trip_entry_id_arr[$i];
							}
							
							else{
								$arrival_datetime = '';
								$departure_city = '';
								$from_city = '';
								$dep_terminal = '';
								$to_city = '';
								$arrival_city = '';
								$arr_terminal = '';
								$airline_name = '';
								$classa = '';
								$flight_duration = '';
								$layover_time = '';
								$sub_category = '';
								$flight_no = '';
								$airlin_pnr = '';
								$no_of_pieces = '';
								$special_note = '';
								$aircraft_type = '';
								$operating_carrier = '';
								$ticket_status = '';
								$frequent_flyer = '';
								$basic_fare = '';
								$refund_type = '';
								$trip_entry_id = '';
							}
							?>
							<input type="checkbox" class="form-control css-checkbox trip_data_check" id="chk_tickett<?= $sq_trip_entries_count?>" <?= $check_status.' '.$disbaled ?>>
							<?php //} ?>

							<!-- loop start -->

							<?php 
								$j = 1;
								if ($journey_type == 'One Way') {
									$j = 1;
								}elseif ($journey_type == 'Round Trip') {
									$j = 2;
								}elseif($journey_type == 'Multi City'){
									$j = 3;
								}
								// for ($i = 0; $i < $j; $i++) {
								

								// for ($k = 0; $k < $j; $k++) {
							?>
								<div class="dynform-item" for="chk_tickett<?= $sq_trip_entries_count ?>" <?=$css?>>
									<div class="row">
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<input type="text" id="departure_datetime-<?= $i ?>" name="departure_datetime" class="app_datetimepicker departure_datetime" placeholder="*Departure Date-Time" title="Departure Date-Time"  data-dyn-valid="required">
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<input type="text"  name="arrival_datetime" class="app_datetimepicker arrival_datetime" placeholder="*Arrival Date-Time" id ="arrival_datetime-<?= $i ?>" title="Arrival Date-Time" data-dyn-valid="required">
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<?php
											$city_id = $from_city;
											$sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$city_id'"));
											?>
											<input id="airpf-<?= $i ?>" name="airpf" title="Enter Departure Airport" data-toggle="tooltip" class="form-control autocomplete airpf" placeholder="*Enter Departure Airport" data-dyn-valid="required" >
											<input type="hidden" name="from_city" id="from_city-<?= $i  ?>" data-dyn-valid="required" />
											<input type="hidden" name="departure_city" id="departure_city-<?= $i ?>" data-dyn-valid="required">
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<input type="text" id="dterm-<?= $sq_trip_entries_count ?>" name="dterm" onchange="validate_specialChar(this.id)" placeholder="Departure Terminal" title="Departure Terminal" data-dyn-valid="" value="<?= $dep_terminal ?>">
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10 ">
											<?php
											$city_id = $to_city;
											$sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$city_id'"));
											?>
											<input id="airpt-<?= $i ?>" name="airpt" class="form-control autocomplete airpt" title="Enter Arrival Airport" data-toggle="tooltip" placeholder="*Enter Arrival Airport" data-dyn-valid="required" >
											<input type="hidden" name="to_city" id="to_city-<?= $i  ?>" data-dyn-valid="required" />
											<input type="hidden" name="arrival_city" id="arrival_city-<?= $i ?>" data-dyn-valid="required"  >
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<input type="text" id="aterm-<?= $sq_trip_entries_count ?>" name="aterm" onchange="validate_specialChar(this.id)" placeholder="Arrival Terminal" title="Arrival Terminal" data-dyn-valid="" value="<?= $arr_terminal ?>">
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<select id="airlines_name-<?= $sq_trip_entries_count ?>" name="airlines_name" title="Airlines Name" style="width:100%" data-dyn-valid="required" class="airlines_names app_select">
												<?php if($airline_name!=''){?><option value="<?= $airline_name ?>"><?= $airline_name ?></option><?php } ?>
													<option value="">*Airline Name</option>
												<?php $sq_airline = mysqlQuery("SELECT airline_name,airline_code FROM airline_master WHERE active_flag!='Inactive' ORDER BY airline_name ASC");
													while($row_airline = mysqli_fetch_assoc($sq_airline)){
													?>
													<option value="<?= $row_airline['airline_name'].' ('.$row_airline['airline_code'].')' ?>" <?php if(isset($airline_code)){if($row_airline['airline_code']==$airline_code){echo 'selected';}} ?>><?= $row_airline['airline_name'].' ('.$row_airline['airline_code'].')' ?></option>
												<?php
												}
												?>
											</select>
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<select name="class" id="class-<?= $sq_trip_entries_count ?>" title="Class" data-dyn-valid="required" class="flight_class">
											<?php if($classa!=''){?><option value="<?= $classa ?>"><?= $classa ?></option><?php } ?>
												<?php get_flight_class_dropdown($_flight_class); ?>
											</select>
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<input type="text" id="flight_duration-<?= $sq_trip_entries_count ?>" name="flight_duration" placeholder="Flight Duration" title="Flight Duration" value="<?= $current_flight_duration ?>" >
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<input type="text" id="layover_time-<?= $sq_trip_entries_count ?>" name="layover_time" placeholder="Layover Time" title="Layover Time" value="<?= $layover_time ?>" >
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<input type="text" id="sub_category-<?= $sq_trip_entries_count ?>" name="sub_category"  placeholder="Sub Category" title="Sub Category" value="<?= $sub_category ?>" >
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<input type="text" id="flight_no-<?= $i ?>" style="text-transform: uppercase;" name="flight_no" onchange="validate_specialChar(this.id)" placeholder="Flight No" title="Flight No" data-dyn-valid="" >
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<input type="text" id="airlin_pnr-<?= $sq_trip_entries_count ?>" style="text-transform: uppercase;" onchange=" validate_specialChar(this.id)" name="airlin_pnr" placeholder="GDS PNR" title="GDS PNR" data-dyn-valid="" value="<?= $gds_pnr ?>">
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 hidden mg_bt_10">
											<input type="hidden" id="cancel_status-<?= $sq_trip_entries_count ?>" name="cancel_status" value="<?= $sq_entry['status'] ?>" data-dyn-valid="" >
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<input type="text" id="no_of_pieces-<?= $sq_trip_entries_count ?>" name="no_of_pieces"  placeholder="No of pieces" title="No of pieces" value="<?= $no_of_pieces ?>" >
										</div>
										<div class="col-md-3 col-sm-12 col-xs-12 mg_bt_10">
											<textarea name="special_note" id="special_note-<?= $sq_trip_entries_count ?>" onchange="validate_address(this.id)" rows="1" placeholder="Special Note" title="Special Note" data-dyn-valid=""><?= $special_note ?></textarea>
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<input type="text" id="aircraft_type-<?= $sq_trip_entries_count ?>" name="aircraft_type"  placeholder="Aircraft Type" title="Aircraft Type" value="<?= $flight_carrier ?>" >
										</div>
									</div>	
									<div class="row">
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<input type="text" id="operating_carrier-<?= $i ?>" name="operating_carrier" placeholder="Operated By" title="Operated By" >
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<input type="text" id="frequent_flyer-<?= $sq_trip_entries_count ?>" name="frequent_flyer" placeholder="Frequent Flyer" title="Frequent Flyer" value="<?= $frequent_flyer ?>" >
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<select name="ticket_status" id="ticket_status-<?= $sq_trip_entries_count ?>" title="Status of ticket" >
												<?php if($ticket_status!=''){?><option value="<?= $ticket_status ?>"><?= $flight_details[0]->ticket_status_arr[$i] ?></option><?php } ?>
												<option value="">Status of ticket</option>
												<option value="Hold" <?php if(isset($_flight_status)){if($_flight_status=='Hold'){echo 'selected';}} ?>>Hold</option>
												<option value="Confirmed" <?php if(isset($_flight_status)){if($_flight_status=='Confirmed'){echo 'selected';}} ?>>Confirmed</option>
											</select>
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<input type="number" id="basic_fare-<?= $sq_trip_entries_count ?>-<?= $i ?>" name="basic_fare" placeholder="Basic Fare" class="basic_fare" title="Basic Fare" required >
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
											<select name="refund_type" id="refund_type-<?= $sq_trip_entries_count ?>" title="Refund Type" data-dyn-valid="required" >
											<?php if($refund_type=''){?><option value="<?= $refund_type ?>"><?= $refund_type ?></option><?php } ?>
												<option value="">Refund Type</option>
												<option value="Refundable">Refundable</option>
												<option value="Non Refundable">Non Refundable</option>
											</select>
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10 hidden">
											<input type="hidden" name="entry_id" id="entry_id-<?= $sq_trip_entries_count ?>"
											value="<?= $trip_entry_id ?>"/>
										</div>
										<div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10 hidden">
											<input type="text" id="cabin_baggage-<?= $sq_trip_entries_count ?>" name="cabin_baggage" onchange="validate_specialChar(this.id)" placeholder="Cabin Baggage" title="Cabin Baggage" data-dyn-valid="">
										</div>

									</div>
								</div>

								<script>
							$('#departure_datetime-<?= $i ?>, #arrival_datetime-<?= $i ?>').datetimepicker({ format:'d-m-Y H:i' });
							$('#airlines_name-<?= $i ?>,#plane_from_location-<?= $i ?>,#plane_to_location-<?= $i ?>').select2();
							</script>
							<?php } ?>
							<!-- loop end -->



							
						<?php //} ?>
                    </div>
                    <div class="row text-center mg_tp_20">
                        <div class="col-xs-12">
                        	<button id="update_btn" class="btn btn-sm btn-success" id="btn_ticket_details"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<?= $button_name ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<script>
$('#flight_details_modal').modal('show');
$('#quotation_id').select2();
$('#frm_flight_details').validate({
	submitHandler:function(form, e){
		$('#update_btn').prop('disabled',true);
		e.preventDefault();
		var base_url = $('#base_url').val();
		var count = $('#count').val();
		var type = $('#type').val();

		var type_of_tour = $('input[name="type_of_tour"]:checked').val();
		var msg = "Type of trip is required"; 
		var airpf = $('.airpf');
		var airpt = $('.airpt');
		var departure_datetime = $('.departure_datetime');
		var arrival_datetime = $('.arrival_datetime');
		var airlines_name = $('.airlines_names');
		var trip_data_check = $('.trip_data_check');
		var flight_class = $('.flight_class');

		var basic_fare=$('.basic_fare');


// 		function check_updated_amount() {
//     // Get the value from the input field with the class 'basic_fare'
//     var basic_fare = parseFloat($('.basic_fare').val()); // Parse as float
    
//     // Check if basic_fare is NaN (Not-a-Number)
//     if (isNaN(basic_fare)) {
//         error_msg_alert("Invalid amount entered!"); // Handle invalid values
//         return false;
//     }

//     // Now check if basic_fare equals 0
//     if (basic_fare === 0) {
//         error_msg_alert("You can update payment to 0 only!");
//         return false;
//     } else {
//         return true; // Allow if it's a valid value and not zero
//     }
// }


		var err_msg = "";
		var airpf_flag    = false;
		var airpt_flag    = false;
		var dd_flag       = false;
		var ad_flag       = false;
		var date_mismatch = false;
		var airline_flag  = false;
		var class_flag  = false;

		var basic_fare_flag=false;
		var status = '';
		for(let i = 0; i < airpf.length ; i++){
			if(!trip_data_check[i]){
				status = true;
			}else{
				status = trip_data_check[i].checked;
			}
			if(status){
                     
				if(basic_fare[i].value ==0)basic_fare_flag=true;
				if(airpf[i].value == "")	airpf_flag = true;
				if(airpt[i].value == "")	airpt_flag = true;
				if(departure_datetime[i].value == "")	dd_flag = true;
				if(arrival_datetime[i].value == "")	ad_flag = true;
				if(airlines_name[i].value == "") airline_flag = true;
				if(flight_class[i].value == "") class_flag = true;
				// if(departure_datetime[i].value != "" && arrival_datetime[i].value != "" && Date.parse(departure_datetime[i].value) >= Date.parse(arrival_datetime[i].value)){
				// 	date_mismatch = true;
				// }
			}
			if(airpf_flag){
				err_msg += "-Please Enter Departure Airport at section "+(i+1)+"<br>";
			}
			if(airpt_flag){
				err_msg += "-Please Enter Arrival Airport at section "+(i+1)+"<br>";
			}
			if(dd_flag){
				err_msg += "-Please Enter Departure Date and Time at section "+(i+1)+"<br>";
			}
			if(ad_flag){
				err_msg += "-Please Enter Arrival Date and Time at section "+(i+1)+"<br>";
			}
			// if(date_mismatch){
			// 	err_msg += "-Please Enter Valid Departure and Arrival Date and Time at section "+(i+1)+"<br>";
			// }
			if(airline_flag){
				err_msg += "-Please Select Airline Name at section "+(i+1)+"<br>";
			}
			if(class_flag){
				err_msg += "-Please Select Class at section "+(i+1)+"<br>";
			}
			if(basic_fare_flag){
				err_msg += "-Basic Fare is required and its should be greater than 0 "+"section"+(i+1)+"<br>";
			}
			
		}
		if(airpf_flag || airpt_flag || dd_flag || ad_flag || airline_flag || date_mismatch || basic_fare_flag || class_flag){
			error_msg_alert(err_msg);
			$('#update_btn').prop('disabled',false);
			return false;
		}
		if(type_of_tour == undefined){
			error_msg_alert(msg);
			$('#update_btn').prop('disabled',false);
			return false;
		}
		var airlin_pnr_arr = getDynFields('airlin_pnr');
		$('#update_btn').button('loading');
		$.ajax({
			type: 'post',
			url: base_url+'controller/visa_passport_ticket/ticket/ticket_pnr_check.php',
			data:{ airlin_pnr_arr : airlin_pnr_arr,type:'save',entry_id:'' },
			success: function(result){
				if(result==''){
					$('a[href="#tab3"]').tab('show');
				}
				else{
					var msg = result.split('--');
					error_msg_alert(msg[1]);
					$('#update_btn').prop('disabled',false);
					$('#update_btn').button('reset');
					return false;
				}
			}
		});

        var departure_datetime_arr = getDynFields('departure_datetime');
		var arrival_datetime_arr = getDynFields('arrival_datetime');
		var airlines_name_arr = getDynFields('airlines_name');
		var class_arr = getDynFields('class');
		var sub_category_arr = getDynFields('sub_category');
		var flight_no_arr = getDynFields('flight_no');
		var airlin_pnr_arr = getDynFields('airlin_pnr');
        var from_city_id_arr = getDynFields('from_city');
		var departure_city_arr = getDynFields('departure_city');
		var departure_terminal_arr = getDynFields('dterm');
        var to_city_id_arr = getDynFields('to_city');
		var arrival_city_arr = getDynFields('arrival_city');
		var arrival_terminal_arr = getDynFields('aterm');
		var luggage_arr = getDynFields('cabin_baggage');
		var no_of_pieces_arr = getDynFields('no_of_pieces');
		var special_note_arr = getDynFields('special_note');
		var aircraft_type_arr = getDynFields('aircraft_type');
		var operating_carrier_arr = getDynFields('operating_carrier');
		var frequent_flyer_arr = getDynFields('frequent_flyer');
		var ticket_status_arr = getDynFields('ticket_status');
		var basic_fare_arr = getDynFields('basic_fare');
		var flight_duration_arr = getDynFields('flight_duration');
		var layover_time_arr = getDynFields('layover_time');
		var refund_type_arr = getDynFields('refund_type');
		var trip_entry_id_arr = getDynFields('entry_id');
		var cancel_status_arr = getDynFields('cancel_status');

		var trip_data_check_arr = [];
		var valid_count = 0;
		for(let i = 0; i < departure_datetime_arr.length ; i++){
			var status = '';
			if(!trip_data_check[i]){
				status = true;
			}else{
				status = trip_data_check[i].checked;
			}
			if(status)
			valid_count++;

			trip_data_check_arr.push(status);
		}
		if(valid_count == 0){
			error_msg_alert('Atleast one flight details required!');
			$('#update_btn').prop('disabled',false);
			$('#update_btn').button('reset');
			return false;
		}
		var dynamic_section_arr = [];
		dynamic_section_arr.push({
			'type_of_tour' : type_of_tour,
			'departure_datetime_arr' : departure_datetime_arr,
			'arrival_datetime_arr' : arrival_datetime_arr,
			'airlines_name_arr' : airlines_name_arr,
			'class_arr' : class_arr,
			'sub_category_arr' : sub_category_arr,
			'flight_no_arr' : flight_no_arr,
			'airlin_pnr_arr' : airlin_pnr_arr,
			'from_city_id_arr' : from_city_id_arr,
			'departure_city_arr' : departure_city_arr,
			'departure_terminal_arr' : departure_terminal_arr,
			'to_city_id_arr' : to_city_id_arr,
			'arrival_city_arr' : arrival_city_arr,
			'arrival_terminal_arr' : arrival_terminal_arr,
			'luggage_arr' : luggage_arr,
			'no_of_pieces_arr' : no_of_pieces_arr,
			'special_note_arr' : special_note_arr,
			'aircraft_type_arr' : aircraft_type_arr,
			'operating_carrier_arr' : operating_carrier_arr,
			'frequent_flyer_arr' : frequent_flyer_arr,
			'ticket_status_arr' : ticket_status_arr,
			'basic_fare_arr' : basic_fare_arr,
			'flight_duration_arr' : flight_duration_arr,
			'layover_time_arr' : layover_time_arr,
			'refund_type_arr' : refund_type_arr,
			'trip_entry_id_arr':trip_entry_id_arr,
			'trip_data_check_arr':trip_data_check_arr,
			'cancel_status_arr':cancel_status_arr
		});
		dynamic_section_arr = JSON.stringify(dynamic_section_arr);
		$('#flight_details'+count).html(dynamic_section_arr);
		if(type == 'update'){ var msg = 'Flight ticket details updated!'; }else{ var msg = 'Flight ticket details saved!'; }
		success_msg_alert(msg);
		$('#flight_details_modal').modal('hide');

        var table = document.getElementById("tbl_dynamic_ticket_master");
        var rowCount = table.rows.length;
        for(var i=0; i<rowCount; i++){
			var row = table.rows[i];
			var trip_details = $('#flight_details'+(i+1)).html();
			if(trip_details == ''||trip_details==null){
				$('#flight_details'+(0)).html(dynamic_section_arr);
			}
		}
	}
});

function event_airport_s(count = 2){
	if(count == 1)	{id1 = "airpf-1"; id2 = "airpt-1"}
	else{
		id1 = "airpf-"+$('#div_dynamic_ticket_info').attr('data-counter');
		id2 = "airpt-"+$('#div_dynamic_ticket_info').attr('data-counter');
	}
	ids = [{"dep" : id1}, {"arr" : id2}];
	airport_load_main_sale(ids);
}
for(var num_airp = 1; num_airp <= parseInt($('#div_dynamic_ticket_info').attr('data-counter')); num_airp++)
event_airport_s(num_airp);

function copy_values(){
	var count = $('#div_dynamic_ticket_info').attr('data-counter');
	var currentdate = new Date(); 
	var day = currentdate.getDate();
	var month = currentdate.getMonth() + 1;
	if (day < 10) {
		day = '0' + day;
	}
	if (month < 10) {
		month = '0' + month;
	}
	var datetime = day + "-"
                + month + "-" 
                + currentdate.getFullYear() + " "  
                + currentdate.getHours() + ":"  
                + currentdate.getMinutes();
	$('#departure_datetime-'+count).val(datetime);
	$('#luggage-'+count).val($('#luggage-1').val());
	$('#airpf-'+count).val($('#airpt-1').val());
	$('#from_city-'+count).val($('#to_city-1').val());
	$('#departure_city-'+count).val($('#arrival_city-1').val());
	$('#airpt-'+count).val($('#airpf-1').val());
	$('#to_city-'+count).val($('#from_city-1').val());
	$('#arrival_city-'+count).val($('#departure_city-1').val());
	// $('#cancel_status-'+count).val($('#cancel_status-1').val());
	$('#cancel_status-'+count).val();
}
function addSection(id){
	if($('#div_dynamic_ticket_info').attr('data-counter') == 1){
		addDyn('div_dynamic_ticket_info');
		if(id == 'type_of_tour-round_trip'){
			copy_values();
		}
		event_airport_s();
	}
}
function airport_load_main_sale(ids){
	// alert('dev');
	// return false;
	var base_url = $('#base_url').val();
	ids.forEach(function (id){
		var object_id = Object.values(id)[0];
		$("#"+object_id).autocomplete({
			source: function(request, response){
				$.ajax({
					method:'get',
					url : base_url+'view/visa_passport_ticket/ticket/home/airport_list.php',
					dataType : 'json',
					data : {request : request.term},
					success : function(data){
						response(data);
					}
				});
			},
			select: function (event, ui) {
				var substr_id =  object_id.substr(6);
				if(Object.keys(id)[0] == 'dep'){
					$('#from_city-'+substr_id).val(ui.item.city_id);
					$('#departure_city-'+substr_id).val(ui.item.value.split(" - ")[1]);
				}
				else{
					$('#to_city-'+substr_id).val(ui.item.city_id);
					$('#arrival_city-'+substr_id).val(ui.item.value.split(" - ")[1]);
				}
			},
			open: function(event, ui) {
				$(this).autocomplete("widget").css({
					"width": document.getElementById(object_id).offsetWidth
				});
			},
			minLength: 2,
			change: function(event,ui){
				var substr_id =  object_id.substr(6);
				if(!ui.item) {
					$(this).val('');
					$('#from_city-'+substr_id).val("");
					$('#departure_city-'+substr_id).val("");
					error_msg_alert('Please select Airport from the list!!');
					$(this).css('border','1px solid red;');
					return;
				}
				if($('#'+ids[0].dep).val() == $("#"+ids[1].arr).val()){
					$(this).val('');
					$(this).css('border','1px solid red;');
					$('#from_city-'+substr_id).val("");
					$('#departure_city-'+substr_id).val("");
					error_msg_alert('Same Arrival and Boarding Airport Not Allowed!!');
				}
			}
		}).data("ui-autocomplete")._renderItem = function(ul, item) {
				return $("<li disabled>")
				.append("<a>" + item.value.split(" -")[1] + "<br><b>" + item.value.split(" -")[0] + "<b></a>")
				.appendTo(ul);
			}
	});	
}
</script>

<script>
	
	<?php if($selected_portal=='Tripjack'){ 
		
		$from_dep = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$departure'"));

		$to_arr = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$arrival'"));
		
		?>
		$('#airpf-0').val('<?= $from_dep['airport_name'];?>');
		$('#airpt-0').val('<?= $to_arr['airport_name'];?>');


		$('#from_city-0').val('<?= $from_dep['city_id'];?>');

		$('#to_city-0').val('<?= $to_arr['city_id']; ?>');


		$('#departure_city-0').val('<?= $from_dep['airport_name'];?>');
		$('#arrival_city-0').val('<?= $to_arr['airport_name'];?>');
		



	<?php }elseif($selected_portal=='TBO-Train'){ 
		
		$from_dep = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$flight_first_section'"));

		$to_arr = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$flight_second_section'"));
		
		?>
		$('#airpf-0').val('<?= $from_dep['airport_name'];?>');
		$('#airpt-0').val('<?= $to_arr['airport_name'];?>');




	$('#from_city-0').val('<?= $from_dep['city_id'];?>');

		$('#to_city-0').val('<?= $to_arr['city_id']; ?>');


		$('#departure_city-0').val('<?= $from_dep['airport_name'];?>');
		$('#arrival_city-0').val('<?= $to_arr['airport_name'];?>');

	<?php }elseif($selected_portal=='Amadeus'){ 
		
		$from_dep = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$_flight_from'"));

		$to_arr = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$_flight_to'"));
		
		?>
		$('#airpf-0').val('<?= mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$flight_first_section'"))['airport_name'];?>');
		$('#airpt-0').val('<?= mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$flight_second_section'"))['airport_name'];?>');
		// $('#operating_carrier-0').val('<?= $_flight_operator_no; ?>');
		$('#flight_no-0').val('<?= $__flight_no; ?>');
		
		$('.basic_fare').val('<?= $fair_amount; ?>');
		$('#airpf-0').val('<?= $from_dep['airport_name'];?>');
		$('#airpt-0').val('<?= $to_arr['airport_name'];?>');


		$('#from_city-0').val('<?= $from_dep['city_id'];?>');

		$('#to_city-0').val('<?= $to_arr['city_id']; ?>');


		$('#departure_city-0').val('<?= $from_dep['airport_name'];?>');
		$('#arrival_city-0').val('<?= $to_arr['airport_name'];?>');
		
		
		$('input[name="departure_datetime"]').val('<?= $_flight_d_date.' '.$_flight_d_time; ?>');
		$('input[name="arrival_datetime"]').val('<?= $_flight_a_date . ' ' . $_flight_a_time; ?>');
	<?php }elseif($selected_portal=='Galileo'){ 
		
		$from_dep = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$_flight_from'"));

		$to_arr = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$_flight_to'"));
		
		?>
		// $('#operating_carrier-0').val('<?= $_flight_operator_no; ?>');
		$('#flight_no-0').val('<?= $__flight_no; ?>');
		$('input[name="departure_datetime"]').val('<?=$_flight_d_date.' '.$_flight_d_time;?>');
		$('input[name="arrival_datetime"]').val('<?= $_flight_a_date . ' ' . $_flight_a_time; ?>');
		$('#airpf-0').val('<?= $from_dep['airport_name'];?>');
		$('#airpt-0').val('<?= $to_arr['airport_name'];?>');



			$('#from_city-0').val('<?= $from_dep['city_id'];?>');

		$('#to_city-0').val('<?= $to_arr['city_id']; ?>');


		$('#departure_city-0').val('<?= $from_dep['airport_name'];?>');
		$('#arrival_city-0').val('<?= $to_arr['airport_name'];?>');


		$('.basic_fare').val('<?= $fair_amount; ?>');
	<?php } ?>

	<?php if($selected_portal=='Tripjack'){ ?>
		$('#operating_carrier-0').val('<?= $operated_by; ?>');
		<?php $operated_for_round_trip = _get_operated_for_round_trip($_POST['departure_or_arrival'] ?? ''); ?>
   	    $('#operating_carrier-1').val(<?= json_encode($operated_for_round_trip) ?>);
		<?php $operated_for_multi_city_trip = _get_operated_for_multi_city_trip($_POST['departure_or_arrival'] ?? ''); ?>
   	    $('#operating_carrier-2').val(<?= json_encode($operated_for_multi_city_trip) ?>);
	<?php }elseif($selected_portal=='TBO-Train'){ ?>
		$('#operating_carrier-0').val('<?= $_flight_operator_no; ?>');
	<?php } ?>

	<?php if($selected_portal=='Tripjack'){ ?>
		$('#flight_no-0').val('<?= $current_flight_no; ?>');
		<?php $flight_no_for_round_trip = _get_flight_no_for_round_trip($_POST['departure_or_arrival'] ?? ''); ?>
        $('#flight_no-1').val(<?= json_encode($flight_no_for_round_trip) ?>);
		<?php $flight_no_for_multi_city_trip = _get_flight_no_for_multi_city_trip($_POST['departure_or_arrival'] ?? ''); ?>
   		$('#flight_no-2').val(<?= json_encode($flight_no_for_multi_city_trip) ?>);
		$('.basic_fare').val('<?= $flight_fair_amount; ?>');

		<?php }elseif($selected_portal=='TBO-Train'){ ?>
			$('#flight_no-0').val('<?= $_flight_no; ?>');
			$('.basic_fare').val('<?= $fair_amount; ?>');
	<?php } ?>

	

    <?php $arrival_for_round_trip = _get_arrival_for_round_trip($_POST['departure_or_arrival'] ?? ''); ?>
    $('#airpf-1').val('<?= mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$arrival_for_round_trip'"))['airport_name'];?>');


		$('#from_city-1').val('<?= mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$arrival_for_round_trip'"))['city_id']; ?>');

		   $('#departure_city-1').val('<?= mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$arrival_for_round_trip'"))['airport_name'];?>');

	<?php $arrival_date_for_round_trip = _get_arrival_date_for_round_trip($_POST['departure_or_arrival'] ?? ''); ?>
    // $('#arrival_datetime-1').val(<?= json_encode($arrival_date_for_round_trip) ?>);

	<?php $arrival_airport_for_round_trip = _get_arrival_airport_date_for_round_trip($_POST['departure_or_arrival'] ?? ''); ?>
    $('#airpt-1').val('<?= mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$arrival_airport_for_round_trip'"))['airport_name'];?>');

	
	$('#to_city-1').val('<?= mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$arrival_airport_for_round_trip'"))['city_id']; ?>');

	  $('#arrival_city-1').val('<?= mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$arrival_airport_for_round_trip'"))['airport_name'];?>');

	

	<?php $arrival_for_multi_city_trip = _get_arrival_for_multi_city_trip($_POST['departure_or_arrival'] ?? ''); ?>
    $('#airpf-2').val('<?= mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$arrival_for_multi_city_trip'"))['airport_name'];?>');


	$('#arrival_city-2').val('<?= mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$arrival_for_multi_city_trip'"))['airport_name'];?>');

	$('#from_city-2').val('<?= mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$arrival_for_multi_city_trip'"))['city_id']; ?>');
	
	<?php $arrival_date_for_multi_city_trip = _get_arrival_date_for_multi_city_trip($_POST['departure_or_arrival'] ?? ''); ?>
    // $('#arrival_datetime-2').val(<?= json_encode($arrival_date_for_multi_city_trip) ?>);

	<?php $arrival_airport_for_multi_city_trip = _get_arrival__for_multi_city_trip($_POST['departure_or_arrival'] ?? ''); ?>
    $('#airpt-2').val('<?= mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$arrival_airport_for_multi_city_trip'"))['airport_name'];?>');

	$('#to_city-2').val('<?= mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$arrival_airport_for_multi_city_trip'"))['city_id']; ?>');


	$('#departure_city-2').val('<?= mysqli_fetch_assoc(mysqlQuery("SELECT * FROM airport_master WHERE airport_code = '$arrival_airport_for_multi_city_trip'"))['airport_name'];?>');

</script>
<script>
	$('.airpf').keyup( function(){
		var id = $(this).attr('id');
		var ids = [{"dep" : id}];
		airport_load_main_sale(ids);
	});
</script>
<script>
	$('.airpt').keyup( function(){
		var id = $(this).attr('id');
		var ids = [{"arr" : id}];
		airport_load_main_sale(ids);
	});
</script>

