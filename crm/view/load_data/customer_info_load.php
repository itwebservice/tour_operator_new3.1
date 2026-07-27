<?php
include "../../model/model.php";
$customer_id = $_POST['customer_id'];
$info_arr = array();
$total_amount = 0;

//Customer Info
$sq_cust_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));

$contact_no = $encrypt_decrypt->fnDecrypt($sq_cust_info['contact_no'], $secret_key);
$email_id = $encrypt_decrypt->fnDecrypt($sq_cust_info['email_id'], $secret_key);

$birthdate = get_date_user($sq_cust_info['birth_date']);
$info_arr['first_name'] = $sq_cust_info['first_name'];
$info_arr['last_name'] = $sq_cust_info['last_name'];
$info_arr['middle_name'] = $sq_cust_info['middle_name'];
$info_arr['gender'] = $sq_cust_info['gender'];
$info_arr['birth_date'] = $birthdate;
$info_arr['age'] = $sq_cust_info['age'];
$info_arr['contact_no'] = $contact_no;
$info_arr['email_id'] = $email_id;
$info_arr['company_name'] = $sq_cust_info['company_name'];

/**
 * Original credit note amount from the linked refund entry.
 * Falls back to stored payment_amount when refund row is missing.
 */
function credit_note_original_amount($row_credit)
{
	$module = isset($row_credit['module_name']) ? $row_credit['module_name'] : '';
	$refund_id = isset($row_credit['refund_id']) ? $row_credit['refund_id'] : 0;
	$fallback = isset($row_credit['payment_amount']) ? (float)$row_credit['payment_amount'] : 0;
	if ($refund_id == '' || $refund_id == 0) {
		return $fallback;
	}

	$map = array(
		'Air Ticket Booking' => array('ticket_refund_master', 'refund_id', 'refund_amount'),
		'Visa Booking' => array('visa_refund_master', 'refund_id', 'refund_amount'),
		'Train Ticket Booking' => array('train_ticket_refund_master', 'refund_id', 'refund_amount'),
		'Hotel Booking' => array('hotel_booking_refund_master', 'refund_id', 'refund_amount'),
		'Package Booking' => array('package_refund_traveler_cancelation', 'refund_id', 'total_refund'),
		'Miscellaneous Booking' => array('miscellaneous_refund_master', 'refund_id', 'refund_amount'),
		'Excursion Booking' => array('exc_refund_master', 'refund_id', 'refund_amount'),
		'Car Rental Booking' => array('car_rental_refund_master', 'refund_id', 'refund_amount'),
		'Bus Booking' => array('bus_booking_refund_master', 'refund_id', 'refund_amount'),
		'B2B Booking' => array('b2b_booking_refund_master', 'refund_id', 'refund_amount'),
		'B2C Booking' => array('b2c_booking_refund_master', 'refund_id', 'refund_amount'),
	);

	if ($module == 'Group Booking') {
		$row = mysqli_fetch_assoc(mysqlQuery("select total_refund as amt from refund_traveler_cancelation where refund_id='$refund_id'"));
		if ($row && isset($row['amt'])) {
			return (float)$row['amt'];
		}
		$row = mysqli_fetch_assoc(mysqlQuery("select refund_amount as amt from refund_tour_cancelation where refund_id='$refund_id'"));
		if ($row && isset($row['amt'])) {
			return (float)$row['amt'];
		}
		return $fallback;
	}

	if (!isset($map[$module])) {
		return $fallback;
	}
	$table = $map[$module][0];
	$id_col = $map[$module][1];
	$amt_col = $map[$module][2];
	$row = mysqli_fetch_assoc(mysqlQuery("select `$amt_col` as amt from `$table` where `$id_col`='$refund_id'"));
	if ($row && isset($row['amt']) && $row['amt'] !== '' && $row['amt'] !== null) {
		return (float)$row['amt'];
	}
	return $fallback;
}

/**
 * Total Credit Note payments used for a customer across sale modules.
 */
function credit_note_payments_used($customer_id)
{
	$used = 0;
	$queries = array(
		"select sum(p.payment_amount) as s from ticket_payment_master p join ticket_master m on m.ticket_id=p.ticket_id where m.customer_id='$customer_id' and p.payment_mode='Credit Note' and (p.clearance_status is null or p.clearance_status='' or (p.clearance_status!='Pending' and p.clearance_status!='Cancelled'))",
		"select sum(p.payment_amount) as s from visa_payment_master p join visa_master m on m.visa_id=p.visa_id where m.customer_id='$customer_id' and p.payment_mode='Credit Note' and (p.clearance_status is null or p.clearance_status='' or (p.clearance_status!='Pending' and p.clearance_status!='Cancelled'))",
		"select sum(p.payment_amount) as s from train_ticket_payment_master p join train_ticket_master m on m.train_ticket_id=p.train_ticket_id where m.customer_id='$customer_id' and p.payment_mode='Credit Note' and (p.clearance_status is null or p.clearance_status='' or (p.clearance_status!='Pending' and p.clearance_status!='Cancelled'))",
		"select sum(p.payment_amount) as s from hotel_booking_payment p join hotel_booking_master m on m.booking_id=p.booking_id where m.customer_id='$customer_id' and p.payment_mode='Credit Note' and (p.clearance_status is null or p.clearance_status='' or (p.clearance_status!='Pending' and p.clearance_status!='Cancelled'))",
		"select sum(p.amount) as s from package_payment_master p join package_tour_booking_master m on m.booking_id=p.booking_id where m.customer_id='$customer_id' and p.payment_mode='Credit Note' and (p.clearance_status is null or p.clearance_status='' or (p.clearance_status!='Pending' and p.clearance_status!='Cancelled'))",
		"select sum(p.payment_amount) as s from miscellaneous_payment_master p join miscellaneous_master m on m.misc_id=p.misc_id where m.customer_id='$customer_id' and p.payment_mode='Credit Note' and (p.clearance_status is null or p.clearance_status='' or (p.clearance_status!='Pending' and p.clearance_status!='Cancelled'))",
		"select sum(p.payment_amount) as s from exc_payment_master p join excursion_master m on m.exc_id=p.exc_id where m.customer_id='$customer_id' and p.payment_mode='Credit Note' and (p.clearance_status is null or p.clearance_status='' or (p.clearance_status!='Pending' and p.clearance_status!='Cancelled'))",
		"select sum(p.payment_amount) as s from car_rental_payment p join car_rental_booking m on m.booking_id=p.booking_id where m.customer_id='$customer_id' and p.payment_mode='Credit Note' and (p.clearance_status is null or p.clearance_status='' or (p.clearance_status!='Pending' and p.clearance_status!='Cancelled'))",
		"select sum(p.payment_amount) as s from bus_booking_payment_master p join bus_booking_master m on m.booking_id=p.booking_id where m.customer_id='$customer_id' and p.payment_mode='Credit Note' and (p.clearance_status is null or p.clearance_status='' or (p.clearance_status!='Pending' and p.clearance_status!='Cancelled'))",
		"select sum(p.amount) as s from payment_master p join tourwise_traveler_details m on m.id=p.tourwise_traveler_id where m.customer_id='$customer_id' and p.payment_mode='Credit Note' and (p.clearance_status is null or p.clearance_status='' or (p.clearance_status!='Pending' and p.clearance_status!='Cancelled'))",
	);

	foreach ($queries as $q) {
		$row = @mysqli_fetch_assoc(mysqlQuery($q));
		if ($row && isset($row['s']) && $row['s'] !== null && $row['s'] !== '') {
			$used += (float)$row['s'];
		}
	}
	return $used;
}

// Credit Note Info — rebuild remaining balances from original refunds minus Credit Note payments used
// (repairs balances incorrectly reduced by non-Credit-Note flight payments)
$used_amount = credit_note_payments_used($customer_id);
$remaining_to_apply = $used_amount;
$sq_credit_note = mysqlQuery("select * from credit_note_master where customer_id='$customer_id' order by id asc");
while ($row = mysqli_fetch_assoc($sq_credit_note)) {
	$original = credit_note_original_amount($row);
	if ($remaining_to_apply >= $original) {
		$new_balance = 0;
		$remaining_to_apply -= $original;
	} else {
		$new_balance = $original - $remaining_to_apply;
		$remaining_to_apply = 0;
	}
	if ((float)$row['payment_amount'] != (float)$new_balance) {
		mysqlQuery("update credit_note_master set payment_amount='$new_balance' where id='$row[id]'");
	}
	$total_amount += $new_balance;
}
$info_arr['payment_amount'] = $total_amount;
echo json_encode($info_arr);
?>
