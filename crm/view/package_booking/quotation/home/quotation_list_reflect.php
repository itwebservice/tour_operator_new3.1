<?php
include "../../../../model/model.php";
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$booking_type = isset($_POST['booking_type']) ? $_POST['booking_type'] : '';
$package_id = isset($_POST['package_id']) ? $_POST['package_id'] : '';
$quotation_id = isset($_POST['quotation_id']) ? $_POST['quotation_id'] : '';
$branch_status = isset($_POST['branch_status']) ? $_POST['branch_status'] : '';
$financial_year_id = isset($_POST['financial_year_id']) ? $_POST['financial_year_id'] : '';
$branch_id = isset($_POST['branch_id']) ? $_POST['branch_id'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';

global $app_quot_format, $currency, $modify_entries_switch;
if ($status != '') {

	$query = "select * from package_tour_quotation_master where status='$status'";
} else {

	$query = "select * from package_tour_quotation_master where status='1' ";
}
if ($financial_year_id != "") {
	$query .= " and financial_year_id='$financial_year_id'";
}

if ($from_date != '' && $to_date != "") {

	$from_date = date('Y-m-d', strtotime($from_date));
	$to_date = date('Y-m-d', strtotime($to_date));

	$query .= " and quotation_date between '$from_date' and '$to_date' ";
}
if ($booking_type != '') {
	$query .= " and booking_type='$booking_type'";
}
if ($package_id != '') {
	$query .= " and package_id in(select package_id from custom_package_master where package_id = '$package_id')";
}
if ($quotation_id != '') {
	$query .= " and quotation_id='$quotation_id'";
}
if ($branch_id != "") {
	$query .= " and branch_admin_id = '$branch_id'";
}
include "../../../../model/app_settings/branchwise_filteration.php";
$query .= " order by quotation_id desc ";

$count = 0;
$quotation_cost = 0;
$row_quotation1 = mysqlQuery($query);
$array_s = array();
$temp_arr = array();
while ($row_quotation = mysqli_fetch_assoc($row_quotation1)) {

	$cust_user_name = '';
	if ($row_quotation['user_id'] != 0) {
		$row_user = mysqli_fetch_assoc(mysqlQuery("Select name from customer_users where user_id ='$row_quotation[user_id]'"));
		$cust_user_name = ' (' . $row_user['name'] . ')';
	}
	$sq_emp =  mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id = '$row_quotation[emp_id]'"));
	$emp_name = ($row_quotation['emp_id'] != 0) ? $sq_emp['first_name'] . ' ' . $sq_emp['last_name'] : 'Admin';
	$quotation_date = $row_quotation['quotation_date'];
	$yr = explode("-", $quotation_date);
	$year = $yr[0];
	$sq_package_program = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id ='$row_quotation[package_id]'"));



	$adults = $row_quotation['total_adult'];
	$childs = $row_quotation['children_with_bed'] + $row_quotation['children_without_bed'];
	$infants = $row_quotation['total_infant'];
	$child_wb = $row_quotation['children_with_bed'];
	$child_wob = $row_quotation['children_without_bed'];




	if ($row_quotation['costing_type'] == 2) {

		$sq_costing1 = mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$row_quotation[quotation_id]'  order by sort_order limit 1");


		while ($sq_costing = mysqli_fetch_assoc($sq_costing1)) {




			// new


			$service_charge = $sq_costing['service_charge'];
			$discount_in = $sq_costing['discount_in'];
			$discount = $sq_costing['discount'];
			if ($discount_in == 'Percentage') {
				$act_discount = (float)($service_charge) * (float)($discount) / 100;
			} else {
				$act_discount = ($service_charge != 0) ? $discount : 0;
			}
			$service_charge = $service_charge - (float)($act_discount);
			$total_pax = (float)($sq_quotation['total_adult']) + (float)($sq_quotation['children_with_bed']) + (float)($sq_quotation['children_without_bed']) + (float)($sq_quotation['total_infant']);
			$total_pax = $adults + $child_wb + $child_wob + $infants;

			$total_pax = !empty($total_pax) ? (float)($total_pax) : 1;
			$per_service_charge = (float)($service_charge) / (float)($total_pax);
			$o_per_service_charge = (float)($sq_costing['service_charge']) / (float)($total_pax);







			$adult_cost1 = ((float)($sq_costing['adult_cost'] + (float)($per_service_charge)));


			$child_with1 = ((float)($sq_costing['child_with'] + (float)($per_service_charge)));


			$child_without1 = ((float)($sq_costing['child_without'] + (float)($per_service_charge)));



			$infant_cost1 = ((float)($sq_costing['infant_cost'] + (float)($per_service_charge)));



			$service_tax_amount = 0;
			$tax_show = '';
			$bsmValues = json_decode($sq_costing['bsmValues'], true);
			$name = '';
			if ($sq_costing['service_tax_subtotal'] !== 0.00 && ($sq_costing['service_tax_subtotal']) !== '') {
				$service_tax_subtotal1 = explode(',', $sq_costing['service_tax_subtotal']);
				for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
					$service_tax = explode(':', $service_tax_subtotal1[$i]);
					$service_tax_amount = (float)($service_tax_amount) + (float)($service_tax[2]);
					$name .= $service_tax[0] . $service_tax[1] . ', ';
				}
			}

			if (isset($bsmValues[0]['tcsper']) && $bsmValues[0]['tcsper'] != 'NaN') {
				$tcsper = $bsmValues[0]['tcsper'];
				$tcsvalue = $bsmValues[0]['tcsvalue'];
			} else {
				$tcsper = 0;
				$tcsvalue = 0;
			}
			$service_tax_amount_show = currency_conversion($currency, $row_quotation['currency_code'], $service_tax_amount);

			$tax1 = $service_tax_amount;

			$total_child = (float)($row_quotation['children_with_bed']) + (float)($row_quotation['children_without_bed']);


			$tax = str_replace(',', '', $name) . $service_tax_amount_show;
			$tcs_cost = '(' . $tcsper . '%) ' . $tcs_show1;
			//   $visa=currency_conversion($currency, $row_quotation['currency_code'], $row_quotation['visa_cost']);
			$visa1 = (float)($row_quotation['visa_cost']);
			//   $guide= currency_conversion($currency, $row_quotation['currency_code'], $row_quotation['guide_cost']) ;

			$guide1 = (float)($row_quotation['guide_cost']);
			//   $misc=currency_conversion($currency, $row_quotation['currency_code'], $row_quotation['misc_cost']) ;
			$misc1 = (float)($row_quotation['misc_cost']);

			$flight_a1 = $row_quotation['flight_acost'];


			$flight_cwb1 = $row_quotation['flight_ccost'];


			$flight_i1 = $row_quotation['flight_icost'];


			$train_a1 = (float)($row_quotation['train_acost']);



			$train_cwb1 = (float)($row_quotation['train_ccost']);



			$train_i1 = (float)($row_quotation['train_icost']);


			$cruise_a1 = (float)($row_quotation['cruise_acost']);



			$cruise_cwb1 = (float)($row_quotation['cruise_ccost']);


			$cruise_i1 = (float)($row_quotation['cruise_icost']);
			// // *Train Cost:* 
			// *Cruise Cost:*



			$total_pkg_cost = ((float)($adult_cost1) * (float)($adults)) +
				((float)($child_with1) * (float)($child_wb)) + ((float)($child_without1) * (float)($child_wob)) +
				((float)($infants) * (float)($infant_cost1)) +
				(float)($tax1) +
				(float)($tcsvalue) +
				(float)($visa1) +
				(float)($guide1) +
				(float)($misc1) +
				(float)($flight_a1) * (float)($adults) +
				((float)($child_wb) * (float)($flight_cwb1)) + ((float)($child_wob) * (float)($flight_cwb1)) +
				((float)($infants) * (float)($flight_i1)) +
				((float)($train_a1) * (float)($adults)) +
				((float)($child_wb) * (float)($train_cwb1)) +  ((float)($child_wob) * (float)($train_cwb1)) +
				((float)($infants) * (float)($train_i1)) +
				((float)($cruise_a1) * (float)($adults)) +
				((float)($child_wb) * (float)($cruise_cwb1)) + ((float)($child_wob) * (float)($cruise_cwb1)) +
				((float)($infants) * (float)($cruise_i1));








			// echo($adult_cost1)."<br>";
			// echo($child_with1)."<br>";
			// echo($child_without1)."<br>";
			// echo($infant_cost1)."<br>";
			// echo($tax1)."<br>";
			// echo($tcsvalue)."<br>";
			// 		  echo "Visa Cost: " . $visa1 . "<br>";
			// 		  echo "Guide Cost: " . $guide1 . "<br>";
			// 		  echo "Miscellaneous Cost: " . $misc1 . "<br>";
			// 		  echo "Flight Adult Cost: " . $flight_a1 . "<br>";
			// 		  echo "Flight Child Cost (WB): " . $flight_cwb1 . "<br>";

			// 		  echo "Flight Infant Cost: " . $flight_i1 . "<br>";

			// 		  echo "train Adult Cost: " . $train_a1 . "<br>";
			// 		  echo "train Child Cost (WB): " . $train_cwb1 . "<br>";

			// 		  echo "train Infant Cost: " . $train_i1 . "<br>";

			// 		  echo "Flight Adult Cost: " . $cruise_a1 . "<br>";
			// 		  echo "Flight Child Cost (WB): " . $cruise_cwb1 . "<br>";

			// 		  echo "Flight Infant Cost: " . $cruise_i1 . "<br>";

			$quotation_cost_p = $total_pkg_cost;
			//   $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $total_pkg_cost);

		}
	}


	// else{

	$sq_cost =  mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id = '$row_quotation[quotation_id]' order by sort_order limit 1"));
	$sq_enq_count =  mysqli_num_rows(mysqlQuery("select entry_id from enquiry_master_entries where quotation_id = '$row_quotation[quotation_id]' and followup_status = 'Converted'"));

	$basic_cost = $sq_cost['basic_amount'];


	$sq_costing_service_charge = !empty($sq_cost['service_charge']) ? (float)($sq_cost['service_charge']) : 0;
	$service_charge = $sq_costing_service_charge;


	$total_tour_cost = $sq_cost['total_tour_cost'];

	$tour_cost = $basic_cost + $service_charge;
	$service_tax_amount = 0;
	$tax_show = '';

	$bsmValues = json_decode($sq_cost['bsmValues'], true);
	$discount_in = $sq_cost['discount_in'];
	$discount = $sq_cost['discount'];
	if ($discount_in == 'Percentage') {
		$act_discount = (float)($service_charge) * (float)($discount) / 100;
	} else {
		$act_discount = $discount;
	}



	$name = '';
	if ($sq_cost['service_tax_subtotal'] !== 0.00 && ($sq_cost['service_tax_subtotal']) !== '') {
		$service_tax_subtotal1 = explode(',', $sq_cost['service_tax_subtotal']);
		for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
			$service_tax = explode(':', $service_tax_subtotal1[$i]);
			$service_tax_amount = (float)($service_tax_amount) + (float)($service_tax[2]);
			$name .= $service_tax[0] . ' ';
			$percent = $service_tax[1];
		}
	}
	if ($bsmValues[0]->service != '') {   //inclusive service charge
		$newBasic = $tour_cost + $service_tax_amount;
		$tax_show = '';
	} else {
		// $tax_show = $service_tax_amount;
		$tax_show =  $name . $percent . ($service_tax_amount);
		$newBasic = $tour_cost;
	}

	////////////Basic Amount Rules
	if ($bsmValues[0]->basic != '') { //inclusive markup
		$newBasic = $tour_cost + $service_tax_amount;
		$tax_show = '';
	}

	// $quotation_cost = $basic_cost +$service_charge+ $service_tax_amount+ $row_quotation['train_cost'] + $row_quotation['cruise_cost']+ $row_quotation['flight_cost'] + $row_quotation['visa_cost'] + $row_quotation['guide_cost'] + $row_quotation['misc_cost'];
	// $quotation_cost=$total_tour_cost;
	// $quotation_cost = ceil($quotation_cost);



	if (isset($bsmValues[0]['tcsper']) && $bsmValues[0]['tcsper'] != 'NaN') {
		$tcsper = $bsmValues[0]['tcsper'];
		$tcsvalue = $bsmValues[0]['tcsvalue'];
	} else {
		$tcsper = 0;
		$tcsvalue = 0;
	}


	$quotation_cost = $basic_cost + $service_charge + $service_tax_amount + $row_quotation['train_cost'] + $row_quotation['cruise_cost'] + $row_quotation['flight_cost'] + $row_quotation['visa_cost'] + $row_quotation['guide_cost'] + $row_quotation['misc_cost'] + (float)($tcsvalue) - $act_discount;
	// $quotation_cost=$total_tour_cost;


	//Currency conversion
	// $currency_amount1 = currency_conversion($currency, $row_quotation['currency_code'], $quotation_cost);
	// if($row_quotation['currency_code'] !='0' && $currency != $row_quotation['currency_code']){
	// 	$currency_amount = ' ('.$currency_amount1.')';
	// }else{
	// 	$currency_amount = '';
	// }


	// if ($row_quotation['currency_code'] != '0' && $currency != $row_quotation['currency_code'] && $quotation_cost != $currency_amount1) {
	// 	$currency_amount = ' (' . $currency_amount1 . ')';
	// } else {
	// 	$currency_amount = '';
	// }



		if($row_quotation['costing_type'] == 2) {

		$currency_amount1 = currency_conversion($currency, $row_quotation['currency_code'], $quotation_cost_p);

		if ($row_quotation['currency_code'] != '0' && $currency != $row_quotation['currency_code'] && $quotation_cost_p != $currency_amount1) {
		$currency_amount = ' (' . $currency_amount1 . ')';
	} else {
		$currency_amount = '';
	}


	}else{

		$currency_amount1 = currency_conversion($currency, $row_quotation['currency_code'], $quotation_cost);

		if ($row_quotation['currency_code'] != '0' && $currency != $row_quotation['currency_code'] && $quotation_cost != $currency_amount1) {
		$currency_amount = ' (' . $currency_amount1 . ')';
	} else {
		$currency_amount = '';
	}

	}


	// // converted currency
	// $currency_amount1 = currency_conversion($currency,$row_quotation['currency_code'],$quotation_cost);


	// }
	//Proforma Invoice
	$for = 'Package Tour';
	$invoice_no = get_quotation_id($row_quotation['quotation_id'], $year);
	$invoice_date = get_date_user($row_quotation['created_at']);
	$customer_id = $row_quotation['customer_name'];
	$customer_email = $row_quotation['email_id'];
	$service_name = "Proforma Invoice";

	//**Basic Cost
	$basic_cost = $sq_cost['tour_cost'] + $sq_cost['transport_cost'] + $sq_cost['excursion_cost'];
	//GST
	$service_tax =  $sq_cost['service_tax_subtotal'];
	// Travel + visa
	$travel_cost = $row_quotation['train_cost'] + $row_quotation['flight_cost'] + $row_quotation['cruise_cost'] + $row_quotation['visa_cost'] + $row_quotation['guide_cost'] + $row_quotation['misc_cost'];
	//Net cost
	$net_amount = $sq_cost['total_tour_cost'] + $row_quotation['train_cost'] + $row_quotation['flight_cost'] + $row_quotation['visa_cost'] + $row_quotation['guide_cost'] + $row_quotation['misc_cost'] + $row_quotation['cruise_cost'];

	$quotation_id = $row_quotation['quotation_id'];
	$p_url = BASE_URL . "model/app_settings/print_html/invoice_html/body/proforma_invoice_html.php?invoice_no=$invoice_no&invoice_date=$invoice_date&customer_id=$customer_id&customer_email=$customer_email&service_name=$service_name&basic_cost=$basic_cost&service_tax=$service_tax&net_amount=$net_amount&travel_cost=$travel_cost&for=$for";



	if ($app_quot_format == 2) {
		$url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_2/fit_quotation_html.php?quotation_id=$quotation_id";

		$urldoc = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_2/fit_quotation_html_doc.php?quotation_id=$quotation_id";
	} else if ($app_quot_format == 3) {
		$url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_3/fit_quotation_html.php?quotation_id=$quotation_id";

		$urldoc = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_3/fit_quotation_html_doc.php?quotation_id=$quotation_id";
	} else if ($app_quot_format == 4) {
		$url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_4/fit_quotation_html.php?quotation_id=$quotation_id";

		$urldoc = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_4/fit_quotation_html_doc.php?quotation_id=$quotation_id";
	} else if ($app_quot_format == 5) {
		$url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_5/fit_quotation_html.php?quotation_id=$quotation_id";

		$urldoc = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_5/fit_quotation_html_doc.php?quotation_id=$quotation_id";
	} else if ($app_quot_format == 6) {
		$url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_6/fit_quotation_html.php?quotation_id=$quotation_id";

		$urldoc = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_6/fit_quotation_html_doc.php?quotation_id=$quotation_id";
	} else {
		$url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_1/fit_quotation_html.php?quotation_id=$quotation_id";

		$urldoc = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_1/fit_quotation_html_doc.php?quotation_id=$quotation_id";
	}
	$whatsapp_tooltip_change = ($whatsapp_switch == "on") ? 'Email and What\'sApp Quotation to Customer' : "Email Quotation to Customer";

	$sq_h_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_hotel_entries where quotation_id='$row_quotation[quotation_id]'"));
	$avail_count = 0;
	$not_avail_count = 0;
	$req_count = 0;
	$sq_hotel = mysqlQuery("select * from package_tour_quotation_hotel_entries where quotation_id='$row_quotation[quotation_id]'");
	while ($row_hotel = mysqli_fetch_assoc($sq_hotel)) {
		if ($row_hotel['request_sent'] == '0') {
			$req_count++;
		} else {
			$avail = isset($row_hotel['availability']) ? json_decode($row_hotel['availability']) : [];
			if (isset($avail) && ($avail->availability == 'Available' || $avail->availability == 'NA')) {
				$avail_count++;
			} else {
				$hotel_options = !empty($avail->option_hotel_arr) && $avail->option_hotel_arr != "null" ? $avail->option_hotel_arr : [];
				if (!empty($hotel_options) && $hotel_options != "null") {
					for ($j = 0; $j < sizeof($hotel_options); $j++) {
						if ($hotel_options[$j]->availability == 'Available' || $hotel_options[$j]->availability == 'NA') {
							$avail_count++;
						} else {
							$not_avail_count++;
						}
					}
				} else {
					$not_avail_count++;
				}
			}
		}
	}
	if ($req_count > 0) {
		$req_btn_class = 'btn-info';
		$title = "Send Hotel Availability Request";
	} else if ($sq_h_count == $avail_count || $sq_h_count <= $avail_count) {
		$req_btn_class = 'btn-warning';
		$title = "Hotel Availability Request(All hotels are available)";
	} else {
		$req_btn_class = 'btn-danger';
		$title = "Hotel Availability Request(Request is in process)";
	}

	if ($row_quotation['status'] == '0') {
		$bg = 'danger';
		$pdf_show = '';
		$whatsapp_show = '';
		$email_show = '';
		$email_show1 = '';
		$hotel_request1 = '';
		$copy_btn = '';
	} else {
		if ($row_quotation['clone'] == 'yes') {
			$bg = 'warning';
		} else {
			$bg = '';
		}
		$pdf_show = '<a data-toggle="tooltip" onclick="loadOtherPage(\'' . $url1 . '\')" class="btn btn-info btn-sm" title="Download Quotation PDF"><i class="fa fa-print"></i></a><a data-toggle="tooltip" onclick="exportHTML(\'' . $urldoc . '\')" class="btn btn-info btn-sm" title="Download Quotation Word"><i class="fa fa-file-word-o"></i></a>';
		$whatsapp_show = '<button class="btn btn-info btn-sm" onclick="quotation_whatsapp(' . $row_quotation['quotation_id'] . ')" title="What\'sApp Quotation to customer" data-toggle="tooltip"><i class="fa fa-whatsapp"></i></button>';
		$email_show = '<a data-toggle="tooltip"  href="javascript:void(0)" id="btn_email_' . $count . '" class="btn btn-info btn-sm" onclick="quotation_email_send(this.id, ' . $row_quotation['quotation_id'] . ',\'' . $row_quotation['email_id'] . '\',\'' . $row_quotation['mobile_no'] . '\')" title="' . $whatsapp_tooltip_change . '"><i class="fa fa-envelope-o"></i></a>';
		$email_show1 = '<a href="javascript:void(0)" id="btn_email1_' . $count . '" title="Email Quotation to Backoffice" class="btn btn-info btn-sm" onclick="quotation_email_send_backoffice_modal(' . $row_quotation['quotation_id'] . ');btnDisableEnable(this.id)" id="email_backoffice_btn-' . $row_quotation['quotation_id'] . '"><i class="fa fa-paper-plane-o"></i></a>';
		$hotel_request1 = '<button data-toggle="tooltip" style="display:inline-block" class="btn ' . $req_btn_class . ' btn-sm" onclick="view_request(' . $row_quotation['quotation_id'] . ')" id="view_req' . $row_quotation['quotation_id'] . '" title="' . $title . '"><i class="fa fa-paper-plane-o"></i></button>';
		$copy_btn = '<button data-toggle="tooltip" style="display:inline-block" class="btn btn-warning btn-sm" onclick="quotation_clone(' . $row_quotation['quotation_id'] . ')" title="Create Copy of this Quotation"><i class="fa fa-files-o"></i></button>';
	}
	if ($sq_enq_count > 0)
		$bg = 'success';

	$update_btn = '<button data-toggle="tooltip"  style="display:inline-block" class="btn btn-info btn-sm" title="Update Details"><i class="fa fa-pencil-square-o"></i></button>';
	$to_date = $row_quotation['to_date'];
	$today = date('Y-m-d');
	if ($to_date < $today && $modify_entries_switch == 'No' && $role != 'Admin' && $role != 'Branch Admin') {
		$update_btn = '';
	}





	$temp_arr = array("data" => array(
		(int)(++$count),
		get_quotation_id($row_quotation['quotation_id'], $year),
		$sq_package_program['package_name'],
		$row_quotation['customer_name'] . $cust_user_name,
		get_date_user($row_quotation['quotation_date']),
		($row_quotation['costing_type'] == 2) ?
			 $quotation_cost_p . '<br>' . $currency_amount : $quotation_cost . '<br>' . $currency_amount,
		$emp_name,
		$pdf_show . $email_show . '
		<form  style="display:inline-block" action="update/index.php" id="frm_booking_' . $count . '" method="POST">
		<input  style="display:inline-block" type="hidden" id="quotation_id" name="quotation_id" value="' . $row_quotation['quotation_id'] . '">
		<input data-toggle="tooltip" style="display:inline-block" type="hidden" id="package_id" name="package_id" value="' . $row_quotation['package_id'] . '">' . $update_btn . '
		</form>' . $copy_btn . $hotel_request1 . $email_show1 . '
		<a data-toggle="tooltip" style="display:inline-block" href="quotation_view.php?quotation_id=' . $row_quotation['quotation_id'] . '" target="_BLANK" class="btn btn-info btn-sm" title="View Details"><i class="fa fa-eye"></i></a>'
	), "bg" => $bg);
	array_push($array_s, $temp_arr);
}
echo json_encode($array_s);
