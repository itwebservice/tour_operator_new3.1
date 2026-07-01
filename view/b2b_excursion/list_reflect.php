<?php
include "../../model/model.php";
header('Content-Type: application/json; charset=UTF-8');

$city_id = isset($_POST['city_id']) ? trim($_POST['city_id']) : '';
$active_flag = isset($_POST['active_flag']) ? trim($_POST['active_flag']) : '';

$query = "select entry_id,city_id,currency_code,excursion_name,departure_point,duration,active_flag from excursion_master_tariff where 1";
if ($active_flag != '') {
	$query .= " and active_flag='$active_flag'";
} else {
	$query .= " and active_flag='Active'";
}
if ($city_id != '') {
	$query .= " and city_id='$city_id'";
}
$query .= ' order by entry_id desc';

$array_s = array();
$count = 0;
$sq_serv = mysqlQuery($query);

if ($sq_serv && mysqli_num_rows($sq_serv) > 0) {
	while ($row_ser = mysqli_fetch_assoc($sq_serv)) {
		$sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$row_ser[city_id]'"));
		$sq_currency = mysqli_fetch_assoc(mysqlQuery("select * from currency_name_master where id='$row_ser[currency_code]'"));
		$city_name = !empty($sq_city['city_name']) ? $sq_city['city_name'] : '';
		$currency_code = !empty($sq_currency['currency_code']) ? $sq_currency['currency_code'] : '';

		if ($row_ser['active_flag'] == "Inactive") {
			$bg = "danger";
			$update_btn = '';
		} else {
			$bg = "";
			$update_btn = '<button class="btn btn-info btn-sm" onclick="time_slotupdate_modal(' . $row_ser['entry_id'] . ')" data-toggle="tooltip" id="tedit-' . $row_ser['entry_id'] . '" title="Update Timing Slot(s)"><i class="fa fa-pencil-square-o"></i></button>';
		}

		$temp_arr = array(
			"data" => array(
				(int)(++$count),
				$city_name,
				$row_ser['excursion_name'],
				$row_ser['departure_point'],
				$row_ser['duration'],
				$currency_code,
				'<button class="btn btn-info btn-sm" id="edit-' . $row_ser['entry_id'] . '" onclick="update_modal(' . $row_ser['entry_id'] . ')" data-toggle="tooltip" title="Update Details"><i class="fa fa-pencil-square-o"></i></button>' . $update_btn
			),
			"bg" => $bg
		);
		array_push($array_s, $temp_arr);
	}
}

$json = json_encode($array_s, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
echo ($json !== false) ? $json : '[]';
