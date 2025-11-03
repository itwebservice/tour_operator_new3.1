<?php
include "../../../../../model/model.php";
global $encrypt_decrypt, $secret_key;
$array_s = array();
$temp_arr = array();
$tour_id= $_POST['tour_id'];
$group_id= $_POST['group_id'];
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_POST['branch_status'];

$count=0;
$total_selling = 0;
$total_paid = 0;
$total_balance = 0;

$query = "select * from tourwise_traveler_details where 1 and delete_status='0'";
if($tour_id!="")
{
	$query .= " and tour_id = '$tour_id'";
}
if($group_id!="")
{
	$query .= " and tour_group_id = '$group_id'";
}
if($branch_status=='yes' && $role=='Branch Admin'){
    $query .= " and  branch_admin_id = '$branch_admin_id'";
}
$query .= " order by id desc";
$sq_tourwise_det = mysqlQuery($query);
while($row_tourwise_det = mysqli_fetch_assoc($sq_tourwise_det))
{
	$pass_count = mysqli_num_rows(mysqlQuery("select * from  travelers_details where traveler_group_id='$row_tourwise_det[id]'"));
	$cancelpass_count = mysqli_num_rows(mysqlQuery("select * from  travelers_details where traveler_group_id='$row_tourwise_det[id]' and status='Cancel'"));
	$bg="";
	if($row_tourwise_det['tour_group_status']=="Cancel"){
		$bg="danger";
	}
	else{
		if($pass_count==$cancelpass_count){
			$bg="danger";
		}
	}

	$count++;
	$date = $row_tourwise_det['form_date'];
	$yr = explode("-", $date);
	$year =$yr[0];

	// Get customer details
	$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_tourwise_det[customer_id]'"));
	if ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') {
		$customer_name = $sq_customer['company_name'];
	} else {
		$customer_name = $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
	}

	// Get booking ID
	$booking_id = get_group_booking_id($row_tourwise_det['id'],$year);

	// Calculate amounts
	$selling_amount = $row_tourwise_det['net_total'];
	
	// Get total paid amount
	$sq_paid = mysqli_fetch_assoc(mysqlQuery("select sum(amount) as sum, sum(credit_charges) as sumc from payment_master where tourwise_traveler_id='$row_tourwise_det[id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
	$payment_sum = ($sq_paid['sum'] != '' && $sq_paid['sum'] != null) ? $sq_paid['sum'] : 0;
	$credit_sum = ($sq_paid['sumc'] != '' && $sq_paid['sumc'] != null) ? $sq_paid['sumc'] : 0;
	$paid_amount = $payment_sum + $credit_sum;
	
	// Calculate balance (including credit charges in selling amount)
	$selling_amount_total = $selling_amount + $credit_sum;
	$balance_amount = $selling_amount_total - $paid_amount;

	// Get customer contact number for WhatsApp
	$contact_no_encrypted = $sq_customer['contact_no'];
	$contact_no = $encrypt_decrypt->fnDecrypt($contact_no_encrypted, $secret_key);
	$mobile_no = $row_tourwise_det['mobile_no'];
	
	// Create WhatsApp button - use mobile_no or contact_no
	if($mobile_no == '' || $mobile_no == null){
		$mobile_no = $contact_no;
	}
	
	$whatsapp_btn = '';
	if($balance_amount > 0 && $mobile_no != '' && $mobile_no != null){
		// Escape customer name for JavaScript - replace quotes
		$customer_name_escaped = str_replace("'", "&#39;", $customer_name);
		$whatsapp_btn = '<button class="btn btn-info btn-sm" onclick="whatsapp_reminder(\'group\',\''.$customer_name_escaped.'\',\''.$selling_amount_total.'\',\''.$paid_amount.'\',\''.$balance_amount.'\',\''.$mobile_no.'\',\''.$booking_id.'\')" data-toggle="tooltip" title="Send WhatsApp Reminder"><i class="fa fa-whatsapp"></i></button>';
	}

	// Add to totals
	$total_selling += $selling_amount_total;
	$total_paid += $paid_amount;
	$total_balance += $balance_amount;

	$temp_arr = array( "data" => array(
		(int)($count),
		$booking_id,
		$customer_name,
		number_format($selling_amount_total, 2),
		number_format($paid_amount, 2),
		number_format($balance_amount, 2),
		$whatsapp_btn
		), "bg" =>$bg);
		array_push($array_s,$temp_arr);
	}

	// Add footer data with totals
	$footer_data = array("footer_data" => array(
		'total_footers' => 3,
		
		'foot0' => "Total Selling : ".number_format($total_selling, 2),
		'col0' => 3,
		'class0' =>"text-right info",

		'foot1' => "Total Paid : ".number_format($total_paid, 2),
		'col1' => 2,
		'class1' =>"text-right success",

		'foot2' => "Total Balance : ".number_format($total_balance, 2),
		'col2' => 2,
		'class2' =>"text-right ".($total_balance > 0 ? 'danger' : 'success')
		)
	);
	array_push($array_s, $footer_data);

	echo json_encode($array_s);
?>

