<?php
include_once(__DIR__ . '/../../../../../model/model.php');
//Get selected currency rate
global $currency,$mealplan_tariff_switch;
$sq_to = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$currency'"));
$to_currency_rate = $sq_to['currency_rate'] ?: 1;  //1 is need to stop Uncaught DivisionByZeroError

$hotel_id_arr = $_POST['hotel_id_arr'] ?: [];
$meal_plan_arr = isset($_POST['meal_plan_arr']) ? $_POST['meal_plan_arr']: [];
$room_cat_arr = $_POST['room_cat_arr'];
$check_in_arr = $_POST['check_in_arr'];
$check_out_arr = $_POST['check_out_arr'];
$total_rooms_arr = $_POST['total_rooms_arr'];
$extra_bed_arr = $_POST['extra_bed_arr'];
$child_with_bed = isset($_POST['child_with_bed']) ? $_POST['child_with_bed'] : 0;
$child_without_bed = isset($_POST['child_without_bed']) ? $_POST['child_without_bed'] : 0;
$adult_count = isset($_POST['adult_count']) ? $_POST['adult_count'] : 0;
$package_id_arr = $_POST['package_id_arr'];
$checked_arr = isset($_POST['checked_arr']) ? $_POST['checked_arr'] : [];

$child_with_bed = ($child_with_bed === '' || $child_with_bed === null) ? 0 : (float)$child_with_bed;
$child_without_bed = ($child_without_bed === '' || $child_without_bed === null) ? 0 : (float)$child_without_bed;
$adult_count = ($adult_count === '' || $adult_count === null) ? 0 : (float)$adult_count;

/**
 * Fetch tariff row for one stay night (valid from/to date, or weekend day).
 * Priority: blackdated → weekend → contracted seasonal.
 */
function quotation_hotel_tariff_for_night($pricing_id, $room_category, $stay_date, $mean_plan_query) {
	$pricing_id = addslashes($pricing_id);
	$room_category = addslashes($room_category);
	$stay_date = addslashes($stay_date);

	$blackdated_count = mysqli_num_rows(mysqlQuery("select * from hotel_blackdated_tarrif where pricing_id='$pricing_id' and room_category = '$room_category' and (from_date <='$stay_date' and to_date>='$stay_date') $mean_plan_query"));
	if ($blackdated_count > 0) {
		return mysqli_fetch_assoc(mysqlQuery("select * from hotel_blackdated_tarrif where pricing_id='$pricing_id' and room_category = '$room_category' and (from_date <='$stay_date' and to_date>='$stay_date') $mean_plan_query"));
	}

	$day = date("l", strtotime($stay_date));
	$day_esc = addslashes($day);
	$weekenddated_count = mysqli_num_rows(mysqlQuery("select * from hotel_weekend_tarrif where pricing_id='$pricing_id' and room_category = '$room_category' and day='$day_esc' $mean_plan_query"));
	if ($weekenddated_count > 0) {
		return mysqli_fetch_assoc(mysqlQuery("select * from hotel_weekend_tarrif where pricing_id='$pricing_id' and room_category = '$room_category' and day='$day_esc' $mean_plan_query"));
	}

	$contracted_count = mysqli_num_rows(mysqlQuery("select * from hotel_contracted_tarrif where pricing_id='$pricing_id' and room_category = '$room_category' and (from_date <='$stay_date' and to_date>='$stay_date') $mean_plan_query"));
	if ($contracted_count > 0) {
		return mysqli_fetch_assoc(mysqlQuery("select * from hotel_contracted_tarrif where pricing_id='$pricing_id' and room_category = '$room_category' and (from_date <='$stay_date' and to_date>='$stay_date') $mean_plan_query"));
	}

	return null;
}

$hotel_arr = array();
for($i=0;$i<sizeof($hotel_id_arr);$i++){

	$hotel_cost = 0;
	$adult_cost = 0;
	$cwb_cost = 0;
	$cwob_cost = 0;
	$infant_cost = 0;
	$flag = 'false';

	$checked = isset($checked_arr[$i]) ? $checked_arr[$i] : false;
	$is_checked = ($checked === true || $checked === 'true' || $checked === 1 || $checked === '1');

	if($is_checked){

		$checkDate_array = array(); // nights only (exclude checkout day)
		$check_in = strtotime($check_in_arr[$i]);
		$check_out = strtotime($check_out_arr[$i]);
		if ($check_in && $check_out && $check_out > $check_in) {
			for ($i_date=$check_in; $i_date<$check_out; $i_date+=86400){
				array_push($checkDate_array, date("Y-m-d", $i_date));
			}
		}

		$total_rooms = (isset($total_rooms_arr[$i]) && $total_rooms_arr[$i] !== '') ? (float)$total_rooms_arr[$i] : 0;
		$extra_beds = (isset($extra_bed_arr[$i]) && $extra_bed_arr[$i] !== '') ? (float)$extra_bed_arr[$i] : 0;

		$row_tariff_count = mysqli_num_rows(mysqlQuery("select * from hotel_vendor_price_master where hotel_id='$hotel_id_arr[$i]' order by pricing_id desc"));
		if($row_tariff_count > 0 && sizeof($checkDate_array) > 0){
			$mean_plan_query = '';
			if($mealplan_tariff_switch == 'Yes'){
				$meal_plan_val = isset($meal_plan_arr[$i]) ? addslashes($meal_plan_arr[$i]) : '';
				$mean_plan_query = "and meal_plan='$meal_plan_val'";
			}
			$row_tariff_master1 = mysqlQuery("select * from hotel_vendor_price_master where hotel_id='$hotel_id_arr[$i]' order by pricing_id desc");
			while($row_tariff_master = mysqli_fetch_assoc($row_tariff_master1)){

				$currency_id = $row_tariff_master['currency_id'];
				$sq_from = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$currency_id'"));
				$from_currency_rate = $sq_from['currency_rate'] ?: 1;

				$hotel_cost_arr = array();
				$all_nights_matched = true;

				for($i_date=0; $i_date<sizeof($checkDate_array); $i_date++){
					$sq_tariff = quotation_hotel_tariff_for_night(
						$row_tariff_master['pricing_id'],
						$room_cat_arr[$i],
						$checkDate_array[$i_date],
						$mean_plan_query
					);
					if (!$sq_tariff) {
						$all_nights_matched = false;
						break;
					}

					$max_occupancy = isset($sq_tariff['max_occupancy']) ? (float)$sq_tariff['max_occupancy'] : 0;
					if ($max_occupancy <= 0) {
						$max_occupancy = 2;
					}

					$arr = array(
						'room_cost' => ($from_currency_rate / $to_currency_rate) * (float)$sq_tariff['double_bed'],
						'child_with_bed' => ($from_currency_rate / $to_currency_rate) * (float)$sq_tariff['child_with_bed'],
						'child_without_bed' => ($from_currency_rate / $to_currency_rate) * (float)$sq_tariff['child_without_bed'],
						'extra_bed' => ($from_currency_rate / $to_currency_rate) * (float)$sq_tariff['extra_bed'],
						'max_occupancy' => $max_occupancy,
					);
					array_push($hotel_cost_arr, $arr);
				}

				// Accept only when every stay night has a valid tariff date/rate
				if ($all_nights_matched && sizeof($hotel_cost_arr) > 0) {
					$room_cost = 0;
					$cwb_tariff = 0;
					$cwob_tariff = 0;
					$extra_bed_cost = 0;

					for($j=0;$j<sizeof($hotel_cost_arr);$j++){
						$room_cost += $hotel_cost_arr[$j]['room_cost'];
						$cwb_tariff += $hotel_cost_arr[$j]['child_with_bed'];
						$cwob_tariff += $hotel_cost_arr[$j]['child_without_bed'];
						$extra_bed_cost += $hotel_cost_arr[$j]['extra_bed'];
					}

					// Group Costing (total stay) — unchanged
					$hotel_cost = ($total_rooms * $room_cost)
						+ ($child_without_bed * $cwob_tariff)
						+ ($child_with_bed * $cwb_tariff)
						+ ($extra_beds * $extra_bed_cost);

					// Per Person Costing ONLY (unit rates; quote total = amount × pax count):
					// Adult PP = ((roomcost × rooms) + (extra_beds × extra_bed_cost)) / adults
					// CWEB PP = cweb tariff for stay (per child)
					// CWNB PP = cwnb tariff for stay (per child)
					// Infant  = 0 (not in hotel tariff)
					$adult_cost = 0;
					if ($adult_count > 0) {
						$adult_cost = (($room_cost * $total_rooms) + ($extra_beds * $extra_bed_cost)) / $adult_count;
					}
					$cwb_cost = ($child_with_bed > 0) ? $cwb_tariff : 0;
					$cwob_cost = ($child_without_bed > 0) ? $cwob_tariff : 0;
					$infant_cost = 0;
					$flag = 'true';
					break; // use first fully matching price master
				}
			}
		}
	}

	array_push($hotel_arr, array(
		'hotel_id' => $hotel_id_arr[$i],
		'hotel_cost' => $hotel_cost,
		'adult_cost' => $adult_cost,
		'child_with_bed' => $cwb_cost,
		'child_without_bed' => $cwob_cost,
		'infant_cost' => $infant_cost,
		'package_id' => $package_id_arr[$i],
		'flag' => $flag
	));
}

echo json_encode($hotel_arr);
exit;
?>
