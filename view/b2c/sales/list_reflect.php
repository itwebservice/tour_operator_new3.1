<?php
include "../../../model/model.php";
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$cust_id = $_POST['cust_id'];

$query = "select * from b2c_sale where 1 ";
if ($from_date != '' && $to_date != "") {

	$from_date = date('Y-m-d', strtotime($from_date));
	$to_date = date('Y-m-d', strtotime($to_date));

	$query .= " and DATE(created_at) between '$from_date' and '$to_date' ";
}
if ($cust_id != '') {
	$query .= " and customer_id='$cust_id'";
}
$query .= " order by booking_id desc ";

$count = 0;
$quotation_cost = 0;
$row_sale1 = mysqlQuery($query);
$array_s = array();
$temp_arr = array();
$f_net_total = 0;
$f_cancel_amount = 0;
$f_total_cost = 0;
$f_paid_amount = 0;
$f_balance_amount = 0;
while ($row_sale = mysqli_fetch_assoc($row_sale1)) {

	$bg = ($row_sale['status'] == 'Cancel') ? 'danger' : '';
	$entry_id = $row_sale['entry_id'];
	$customer_id = $row_sale['customer_id'];
	if ($row_sale['service'] == 'Holiday') {
		$service_name = "Package Invoice";
	} else if ($row_sale['service'] == 'Group Tour') {
		$service_name = "Group Invoice";
	} else if ($row_sale['service'] == 'Activity') {
		$service_name = "Activity Invoice";
	} else if ($row_sale['service'] == 'Transfer') {
		$service_name = "Transfer Invoice";
	} else if ($row_sale['service'] == 'Hotel') {
		$service_name = "Hotel Invoice";
	}




	$booking_date = get_date_user($row_sale['created_at']);
	$yr = explode("-", $row_sale['created_at']);
	$year = $yr[0];
	$booking_id = get_b2c_booking_id($row_sale['booking_id'], $year);

	$sq_payment_info = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum,sum(`credit_charges`) as sumc from b2c_payment_master where booking_id='$row_sale[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
	$credit_card_charges = $sq_payment_info['sumc'];
	$paid_amount = $sq_payment_info['sum'];

	$costing_data = json_decode($row_sale['costing_data']);
	$enq_data = json_decode($row_sale['enq_data']);
	$net_total = $costing_data[0]->net_total;


	$cancel_amount = $row_sale['cancel_amount'];
	$total_cost1 = $net_total - $cancel_amount;

	if ($row_sale['status'] == 'Cancel') {
		if ($cancel_amount <= $paid_amount) {
			$balance_amount = 0;
		} else {
			$balance_amount =  $cancel_amount - $paid_amount;
		}
	} else {
		$cancel_amount = ($cancel_amount == '') ? '0' : $cancel_amount;
		$balance_amount = $net_total - $paid_amount;
	}

	$f_net_total += $net_total;
	$f_cancel_amount += $cancel_amount;
	$f_total_cost += $total_cost1;
	$f_paid_amount += $paid_amount;
	$f_balance_amount += $balance_amount;

	$sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
	$cust_name = $sq_cust['first_name'] . ' ' . $sq_cust['last_name'];
	// Invoice

	if ($service_name == 'Package Invoice' || $service_name == 'Group Invoice') {
		$tour_name = $enq_data[0]->package_name;
	} else if ($service_name == 'Activity Invoice') {
		$tour_name = $enq_data[0]->act_name;
	} else if ($service_name == 'Transfer Invoice') {
		$tour_name = $enq_data[0]->trans_name;
	} else if ($service_name == 'Hotel Invoice') {
		$tour_name = $enq_data[0]->hotel_name;
	}

	if ($row_sale['service'] == 'Holiday') {

		$service_sac = "Package Tour";
	} else if ($row_sale['service'] == 'Group Tour') {
		$service_sac =  "Group Tour";
	} else if ($row_sale['service'] == 'Activity') {
		$service_sac =  "Excursion";
	} else if ($row_sale['service'] == 'Transfer') {
		$service_sac =  "Car Rental";
	} else if ($row_sale['service'] == 'Transfer') {
		$service_sac =  "Hotel / Accommodation";
	}

	if ($row_sale['service'] == 'Transfer') {
		$total_pax = $enq_data[0]->pass;
	} else if ($row_sale['service'] == 'Hotel') {
		$room_cat_string = $enq_data[0]->room_cat;
		$room_count = substr_count($room_cat_string, ',') + 1;
		$total_pax = $room_count;
	} else {
		$total_pax = intval($enq_data[0]->adults) + intval($enq_data[0]->chwob) + intval($enq_data[0]->chwb) + intval($enq_data[0]->infant) + intval($enq_data[0]->extra_bed) + intval($enq_data[0]->child);
		$sq_sac = mysqli_fetch_assoc(mysqlQuery("select * from sac_master where service_name='$service_sac'"));
	}
	$sac_code = $sq_sac['hsn_sac_code'];
	// Costing on invoice
	$total_cost = $costing_data[0]->total_cost;
	$total_tax = $costing_data[0]->total_tax;
	$taxes = explode(',', $total_tax);
	$tax_amount = 0;
	$tax_string = '';
	for ($i = 0; $i < sizeof($taxes); $i++) {
		$single_tax = explode(':', $taxes[$i]);
		$tax_amount += (float)($single_tax[1]);
		$temp_tax = explode(' ', $single_tax[1]);
		$tax_string .= $single_tax[0] . $temp_tax[1];
	}
	$grand_total = $costing_data[0]->grand_total;
	$coupon_amount = $costing_data[0]->coupon_amount;

	$tax = $tax_string;
	$sq = mysqli_fetch_assoc(mysqlQuery("select branch_status from branch_assign where link='b2b_sale/index.php'"));
	$branch_status = $sq['branch_status'];
	// Receipt
	if ($row_sale['service'] == 'Holiday') {

		$receipt_type = "Package Tour Receipt";
	} else if ($row_sale['service'] == 'Group Tour') {

		$receipt_type = "Group Tour Receipt";
	} else if ($row_sale['service'] == 'Activity') {
		$receipt_type = "Activity  Receipt";
	} else if ($row_sale['service'] == 'Transfer') {
		$receipt_type = "Transfer  Receipt";
	} else if ($row_sale['service'] == 'Hotel') {
		$receipt_type = "Hotel  Receipt";
	}
	// ${params.toString()}
	$url = BASE_URL . "model/app_settings/print_html/receipt_html/b2c_receipt_html.php?booking_id=$row_sale[booking_id]&customer_id=$customer_id&confirm_by=$app_name&receipt_type=$receipt_type&branch_status=$branch_status";

	$conf_btn = '';
	$voucher_modal = '';
	if ($row_sale['status'] != 'Cancel') {
		// Booking Form
		if ($row_sale['service'] == 'Holiday' || $row_sale['service'] == 'Group Tour') {
			$b_url = BASE_URL . "model/app_settings/print_html/booking_form_html/b2c_package_tour.php?booking_id=$row_sale[booking_id]&credit_card_charges=$credit_card_charges&branch_status=$branch_status";
			$conf_btn = '<a data-toggle="tooltip" style="display:inline-block" onclick="loadOtherPage(\'' . $b_url . '\')" class="btn btn-info btn-sm" title="Download Confirmation Form" ><i class="fa fa-print"></i></a>';
		} else {
			$conf_btn = '';
		}
		// Voucher
		if ($row_sale['service'] == 'Holiday' || $row_sale['service'] == 'Hotel') {
			$voucher_modal = '<button data-toggle="tooltip" title="Download Service Voucher" class="btn btn-info btn-sm" onclick="voucher_modal(' . $row_sale['booking_id'] . ',\'' . $row_sale['service'] . '\')" ><i class="fa fa-print" data-toggle="tooltip"></i></button>';
		} else {
			$voucher_modal = '';
		}
	}
	$cancel_amt = $row_sale['cancel_amount'];
	$tour_name = urlencode($tour_name);
	$tax = urlencode($tax);
	// Invoice PDF
	$url1 = BASE_URL . "model/app_settings/print_html/invoice_html/body/b2c_invoice.php?invoice_no=$booking_id&invoice_date=$booking_date&customer_id=$customer_id&service_name=$service_name&sac_code=$sac_code&tour_name=$tour_name&booking_id=$row_sale[booking_id]&credit_card_charges=$credit_card_charges&total_cost=$total_cost&tax_amount=$tax_amount&grand_total=$grand_total&coupon_amount=$coupon_amount&net_total=$net_total&tax=$tax&paid_amount=$paid_amount&total_pax=$total_pax&branch_status=$branch_status&bal_amount=$balance_amount&canc_amount=$cancel_amt&bg=$bg";

	$temp_arr = array("data" => array(
		(int)(++$count),
		$booking_id,
		$row_sale['service'],
		$cust_name,
		get_date_user($row_sale['created_at']),
		number_format($net_total, 2),
		number_format($cancel_amount, 2),
		number_format($total_cost1, 2),
		number_format($paid_amount, 2),
		number_format($balance_amount, 2),
		$conf_btn . '<a data-toggle="tooltip" onclick="loadOtherPage(\'' . $url1 . '\')" class="btn btn-info btn-sm" title="Download Invoice"><i class="fa fa-print" data-toggle="tooltip"></i></a>' . $voucher_modal . '<a data-toggle="tooltip" onclick="loadOtherPage(\'' . $url . '\')" class="btn btn-info btn-sm" title="Download Receipt"><i class="fa fa-print"></i></a>		

		<button style="display:inline-block" class="btn btn-info btn-sm" onclick="package_view_modal(' . $row_sale['booking_id'] . ')" title="View Details" id="view_btn-' . $row_sale['booking_id'] . '" data-toggle="tooltip"><i class="fa fa-eye" aria-hidden="true"></i></button>
		'

	), "bg" => $bg);
	array_push($array_s, $temp_arr);
}
$footer_data = array(
	"footer_data" => array(
		'total_footers' => 7,
		'foot0' => "Total",
		'col0' => 5,
		'class0' => "",
		'foot1' => number_format($f_net_total, 2),
		'col1' => 0,
		'class1' => "warning",
		'foot2' =>  number_format($f_cancel_amount, 2),
		'col2' => 0,
		'class2' => "danger",
		'foot3' => number_format($f_total_cost, 2),
		'col3' => 0,
		'class3' => "info",
		'foot4' =>  number_format($f_paid_amount, 2),
		'col4' => 0,
		'class4' => "success",
		'foot5' => number_format($f_balance_amount, 2),
		'col5' => 1,
		'class5' => "info",
		'foot6' => "",
		'col6' => 1,
		'class6' => "",
	)
);
array_push($array_s, $footer_data);
echo json_encode($array_s);
