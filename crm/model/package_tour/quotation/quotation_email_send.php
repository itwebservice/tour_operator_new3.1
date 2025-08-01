<?php
error_reporting(E_ALL);
// include_once('../../model.php');
class quotation_email_send
{
	public function quotation_email()
	{

		$quotation_id_arr = $_POST['quotation_id_arr'];
		global $currency, $app_cancel_pdf, $model, $quot_note, $theme_color;
		$i = 0;

		if ($app_cancel_pdf == '') {
			$url =  BASE_URL . 'view/package_booking/quotation/cancellaion_policy_msg.php';
		} else {
			$url = explode('uploads', $app_cancel_pdf);
			$url = BASE_URL . 'uploads' . $url[1];
		}
		$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id_arr[0]'"));
		$sq_cost =  mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id = '$quotation_id_arr[0]'"));

		for ($i = 0; $i < sizeof($quotation_id_arr); $i++) {
			$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id_arr[$i]'"));
			$sq_cost =  mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id = '$quotation_id_arr[$i]'"));
			$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_quotation[emp_id]'"));

			if ($sq_emp_info['first_name'] == '') {
				$emp_name = 'Admin';
			} else {
				$emp_name = $sq_emp_info['first_name'] . ' ' . $sq_emp_info['last_name'];
			}


			$adults = $sq_quotation['total_adult'];
			$childs = $sq_quotation['children_with_bed'] + $sq_quotation['children_without_bed'];
			$infants = $sq_quotation['total_infant'];
			$child_wb = $sq_quotation['children_with_bed'];
			$child_wob = $sq_quotation['children_without_bed'];
			/* tour costing  start*/

			$quotation_no = base64_encode($quotation_id_arr[$i]);
			if ($sq_quotation['costing_type'] == 2) {

				$sq_costing1 = mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id_arr[$i]'  order by sort_order limit 1");
				while ($sq_costing = mysqli_fetch_assoc($sq_costing1)) {

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
					$per_service_charge = (float)($service_charge) / (float)($total_pax);
					$o_per_service_charge = (float)($sq_costing['service_charge']) / (float)($total_pax);

					$adult_cost = ($sq_quotation['total_adult'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['adult_cost'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);

					$adult_cost1 = ((float)($sq_costing['adult_cost'] + (float)($per_service_charge)));

					$child_with = ($sq_quotation['children_with_bed'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['child_with'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);
					$child_with1 = ((float)($sq_costing['child_with'] + (float)($per_service_charge)));

					$child_without = ($sq_quotation['children_without_bed'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['child_without'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);

					$child_without1 = ((float)($sq_costing['child_without'] + (float)($per_service_charge)));

					$infant_cost = ($sq_quotation['total_infant'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['infant_cost'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);

					$infant_cost1 = ((float)($sq_costing['infant_cost'] + (float)($per_service_charge)));

					// Without currency
					$adult_costw = ($sq_quotation['total_adult'] != '0') ? ((float)($sq_costing['adult_cost'] + (float)($per_service_charge)) * intval($sq_quotation['total_adult'])) : 0;
					$child_withw = ($sq_quotation['children_with_bed'] != '0') ? ((float)($sq_costing['child_with'] + (float)($per_service_charge)) * intval($sq_quotation['children_with_bed'])) : 0;
					$child_withoutw = ($sq_quotation['children_without_bed'] != '0') ? ((float)($sq_costing['child_without'] + (float)($per_service_charge)) * intval($sq_quotation['children_without_bed'])) : 0;
					$infant_costw = ($sq_quotation['total_infant'] != '0') ? ((float)($sq_costing['infant_cost'] + (float)($per_service_charge)) * intval($sq_quotation['total_infant'])) : 0;
					$o_adult_costw = ($sq_quotation['total_adult'] != '0') ? ((float)($sq_costing['adult_cost'] + (float)($o_per_service_charge)) * intval($sq_quotation['total_adult'])) : 0;
					$o_child_withw = ($sq_quotation['children_with_bed'] != '0') ? ((float)($sq_costing['child_with'] + (float)($o_per_service_charge)) * intval($sq_quotation['children_with_bed'])) : 0;
					$o_child_withoutw = ($sq_quotation['children_without_bed'] != '0') ? ((float)($sq_costing['child_without'] + (float)($o_per_service_charge)) * intval($sq_quotation['children_without_bed'])) : 0;
					$o_infant_costw = ($sq_quotation['total_infant'] != '0') ? ((float)($sq_costing['infant_cost'] + (float)($o_per_service_charge)) * intval($sq_quotation['total_infant'])) : 0;

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
					$service_tax_amount_show = currency_conversion($currency, $sq_quotation['currency_code'], $service_tax_amount);

					$tax1 = $service_tax_amount;

					$total_child = (float)($sq_quotation['children_with_bed']) + (float)($sq_quotation['children_without_bed']);

					$quotation_cost = (float)($adult_costw) + (float)($child_withw) + (float)($child_withoutw) + (float)($infant_costw);
					$o_quotation_cost = (float)($o_adult_costw) + (float)($o_child_withw) + (float)($o_child_withoutw) + (float)($o_infant_costw);

					$other_cost = $service_tax_amount + $sq_quotation['visa_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'];
					$travel_cost = ($sq_plane_count > 0) ? $sq_quotation['flight_ccost'] + $sq_quotation['flight_icost'] + $sq_quotation['flight_acost'] : 0;
					$travel_cost += ($sq_train_count > 0) ? $sq_quotation['train_ccost'] + $sq_quotation['train_icost'] + $sq_quotation['train_acost'] : 0;
					$travel_cost += ($sq_cruise_count > 0) ?  $sq_quotation['cruise_acost'] + $sq_quotation['cruise_icost'] + $sq_quotation['cruise_ccost'] : 0;

					$train_cost_a = $sq_quotation['train_acost'] * intval($sq_quotation['total_adult']);
					$train_cost_cw = $sq_quotation['train_ccost'] * intval($sq_quotation['children_with_bed']);
					$train_cost_cwo =  $sq_quotation['train_ccost'] * intval($sq_quotation['children_without_bed']);
					$train_cost_i = $sq_quotation['train_icost'] * intval($sq_quotation['total_infant']);

					$train_total_cost = ($sq_train_count > 0) ? $train_cost_a + $train_cost_cw + $train_cost_cwo + $train_cost_i : 0;
					// flight cost
					$flight_cost_a = $sq_quotation['flight_acost'] * intval($sq_quotation['total_adult']);
					$flight_cost_cw = $sq_quotation['flight_ccost'] * intval($sq_quotation['children_with_bed']);
					$flight_cost_cwo =  $sq_quotation['flight_ccost'] * intval($sq_quotation['children_without_bed']);
					$flight_cost_i = $sq_quotation['flight_icost'] * intval($sq_quotation['total_infant']);

					$flight_total_cost = ($sq_plane_count > 0) ? $flight_cost_a + $flight_cost_cw + $flight_cost_cwo + $flight_cost_i : 0;
					// Cruise cost


					$cruise_cost_a = $sq_quotation['cruise_acost'] * intval($sq_quotation['total_adult']);
					$cruise_cost_cw = $sq_quotation['cruise_ccost'] * intval($sq_quotation['children_with_bed']);
					$cruise_cost_cwo =  $sq_quotation['cruise_ccost'] * intval($sq_quotation['children_without_bed']);
					$cruise_cost_i = $sq_quotation['cruise_icost'] * intval($sq_quotation['total_infant']);

					$cruise_total_cost = ($sq_cruise_count > 0) ? $cruise_cost_a + $cruise_cost_cw + $cruise_cost_cwo + $cruise_cost_i : 0;




					$quotation_cost = (float)($quotation_cost) + (float)($train_total_cost) + (float)($flight_total_cost) + (float)($cruise_total_cost) + (float)($other_cost) + (float)($tcsvalue);
					$quotation_cost = ceil($quotation_cost);
					$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);
					$tcs_show1 = currency_conversion($currency, $sq_quotation['currency_code'], $tcsvalue);
					$o_quotation_cost = (float)($o_quotation_cost) + (float)($train_total_cost) + (float)($flight_total_cost) + (float)($cruise_total_cost) + (float)($other_cost) + (float)($tcsvalue);
					// $o_quotation_cost = ceil($o_quotation_cost);
					$act_tour_cost_camount = ($discount != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], $o_quotation_cost) : '';








					$tax = str_replace(',', '', $name) . $service_tax_amount_show;
					$tcs_cost = '(' . $tcsper . '%) ' . $tcs_show1;
					$visa = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['visa_cost']);
					$visa1 = $sq_quotation['visa_cost'];
					$guide = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['guide_cost']);

					$guide1 = $sq_quotation['guide_cost'];
					$misc = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['misc_cost']);
					$misc1 = $sq_quotation['misc_cost'];
					$flight_a = $sq_quotation['total_adult'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['flight_acost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));
					$flight_a1 = $sq_quotation['flight_acost'];
					$flight_cwb = ($sq_quotation['children_with_bed'] != 0 || $sq_quotation['children_without_bed'] != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['flight_ccost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));

					$flight_cwb1 = $sq_quotation['flight_ccost'];

					$flight_i = $sq_quotation['total_infant'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['flight_icost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));

					$flight_i1 = $sq_quotation['flight_icost'];

					$train_a = $sq_quotation['total_adult'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['train_acost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));

					$train_a1 = (float)($sq_quotation['train_acost']);

					$train_cwb = ($sq_quotation['children_with_bed'] != 0 || $sq_quotation['children_without_bed'] != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['train_ccost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));


					$train_cwb1 = (float)($sq_quotation['train_ccost']);

					$train_i = $sq_quotation['total_infant'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['train_icost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));

					$train_i1 = (float)($sq_quotation['train_icost']);

					$cruise_a = $sq_quotation['total_adult'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['cruise_acost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));
					$cruise_a1 = (float)($sq_quotation['cruise_acost']);

					$cruise_cwb = ($sq_quotation['children_with_bed'] != 0  || $sq_quotation['children_without_bed'] != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['cruise_ccost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));

					$cruise_cwb1 = (float)($sq_quotation['cruise_ccost']);

					$cruise_i = $sq_quotation['total_infant'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['cruise_icost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));

					$cruise_i1 = (float)($sq_quotation['cruise_icost']);
					// // *Train Cost:* 
					// *Cruise Cost:*



					$total_pkg_cost = (float)($adult_cost1) * (float)($adults) +
						(float)($child_with1) * (float)($child_wb) + (float)($child_without1) * (float)($child_wob) +
						(float)($infants) * (float)($infant_cost1) +
						(float)($tax1) +
						(float)($tcsvalue) +
						(float)($visa1) +
						(float)($guide1) +
						(float)($misc1) +
						(float)($flight_a1) * (float)($adults) +
						(float)($child_wb) * (float)($flight_cwb1) + (float)($child_wob) * (float)($flight_cwb1) +
						(float)($infants) * (float)($flight_i1) +
						(float)($train_a1) * (float)($adults) +
						(float)($child_wb) * (float)($train_cwb1) +  (float)($child_wob) * (float)($train_cwb1) +
						(float)($infants) * (float)($train_i1) +
						(float)($cruise_a1) * (float)($adults) +
						(float)($child_wb) * (float)($cruise_cwb1) + (float)($child_wob) * (float)($cruise_cwb1) +
						(float)($infants) * (float)($cruise_i1);



					$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $total_pkg_cost);
				}
			} else {

				$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id_arr[0]'"));
				$sq_cost =  mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id = '$quotation_id_arr[0]'"));

				$quotation_cost = $sq_cost['total_tour_cost'] + $sq_quotation['train_cost'] + $sq_quotation['flight_cost'] + $sq_quotation['cruise_cost'] + $sq_quotation['visa_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'];
				////////////////Currency conversion ////////////
				$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);
			}

			$sq_tours_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '$sq_quotation[package_id]'"));

			// $quotation_no = base64_encode($quotation_id_arr[$i]);

			$content = '   
			
			<tr>
				<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
					<tr><td style="text-align:left;border: 1px solid #888888;width:30%">Package Name</td>   <td style="text-align:left;border: 1px solid #888888;">' . $sq_tours_package['package_name'] . '</td></tr>
					<tr><td style="text-align:left;border: 1px solid #888888;width:30%">Travel Date</td>   <td style="text-align:left;border: 1px solid #888888;">' . get_date_user($sq_quotation['from_date']) . ' To ' . get_date_user($sq_quotation['to_date']) . '</td></tr>
					<tr><td style="text-align:left;border: 1px solid #888888;width:30%">Duration</td>   <td style="text-align:left;border: 1px solid #888888;">' . $sq_quotation['total_days'] . 'N/' . ($sq_quotation['total_days'] + 1) . 'D' . '</td></tr>
					<tr><td style="text-align:left;border: 1px solid #888888;width:30%">Quotation Cost</td><td style="text-align:left;border: 1px solid #888888;">' . $currency_amount1 . '</td></tr>
					<tr><td style="text-align:left;border: 1px solid #888888;width:30%">View Quotation</td><td style="text-align:left;border: 1px solid #888888;width:30%"><a style="color: ' . $theme_color . ';text-decoration: none;" href="' . BASE_URL . 'model/package_tour/quotation/single_quotation.php?quotation=' . $quotation_no . '">View</a></td></tr>
					<tr><td style="text-align:left;border: 1px solid #888888;width:30%">Created By</td>   <td style="text-align:left;border: 1px solid #888888;">' . $emp_name . '</td></tr>
				</table>
			</tr>	';
		}
		$content .= '
		<tr>
			<table style="width:100%;margin-top:20px">
				<tr>
					<td style="padding-left: 10px;border-bottom: 1px solid #eee;"><span style="font-weight: 600; color: ' . $theme_color . '">' . $quot_note . '</span></td>
				</tr>
			</table>	
		<tr>';

		$subject = 'New Quotation : (' . $sq_tours_package['package_name'] . ' )';
		$model->app_email_send('8', $sq_quotation['customer_name'], $sq_quotation['email_id'], $content, $subject, '1');

		echo "Quotation successfully sent.";
		exit;
	}

	public function quotation_email_body()
	{

		$quotation_id_arr = $_POST['quotation_id_arr'];
		global $currency, $bank_name_setting, $bank_branch_name, $acc_name, $bank_acc_no, $bank_swift_code, $bank_account_name, $app_cancel_pdf, $model, $quot_note, $tcs_note, $theme_color;
		$i = 0;

		if ($app_cancel_pdf == '') {
			$url =  BASE_URL . 'view/package_booking/quotation/cancellaion_policy_msg.php';
		} else {

			$url = explode('uploads', $app_cancel_pdf);

			$url = BASE_URL . 'uploads' . $url[1];
		}
		$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id_arr[0]'"));
		$sq_cost =  mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id = '$quotation_id_arr[0]'"));

		for ($i = 0; $i < sizeof($quotation_id_arr); $i++) {
			$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id_arr[$i]'"));
			$tcs_note_show = ($sq_quotation['booking_type'] != 'Domestic') ? $tcs_note : '';
			$sq_cost =  mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id = '$quotation_id_arr[$i]'"));
			$sq_login = mysqli_fetch_assoc(mysqlQuery("select * from roles where id='$sq_quotation[login_id]'"));
			$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_quotation[emp_id]'"));

			if ($sq_emp_info['first_name'] == '') {
				$emp_name = 'Admin';
			} else {
				$emp_name = $sq_emp_info['first_name'] . ' ' . $sq_emp_info['last_name'];
			}

			$quotation_cost = $sq_cost['total_tour_cost'] + $sq_quotation['train_cost'] + $sq_quotation['flight_cost'] + $sq_quotation['cruise_cost'] + $sq_quotation['visa_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'];
			////////////////Currency conversion ////////////
			$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);

			$sq_tours_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '$sq_quotation[package_id]'"));

			$sq_cost =  mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id = '$quotation_id_arr[$i]' order by sort_order"));
			$sq_tours_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '$sq_quotation[package_id]'"));


			$sq_login = mysqli_fetch_assoc(mysqlQuery("select * from roles where id='$sq_quotation[login_id]'"));
			$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_login[emp_id]'"));

			if ($sq_emp_info['first_name'] == '') {
				$emp_name = 'Admin';
			} else {
				$emp_name = $sq_emp_info['first_name'] . ' ' . $sq_emp_info['last_name'];
			}

			$quotation_id = $quotation_id_arr[$i];
			$sq_package_program = mysqlQuery("select * from  package_quotation_program where quotation_id='$quotation_id_arr[$i]'");

			$sq_trans_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_transport_entries2 where quotation_id='$quotation_id_arr[$i]'"));
			$sq_hotel = mysqlQuery("select * from package_tour_quotation_transport_entries2 where quotation_id='$quotation_id_arr[$i]'");

			$sq_hotel_count1 = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_hotel_entries where quotation_id='$quotation_id_arr[$i]'"));

			$sq_train_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_train_entries where quotation_id='$quotation_id_arr[$i]'"));
			$sq_train = mysqlQuery("select * from package_tour_quotation_train_entries where quotation_id='$quotation_id_arr[$i]'");

			$sq_plane_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_plane_entries where quotation_id='$quotation_id_arr[$i]'"));
			$sq_plane = mysqlQuery("select * from package_tour_quotation_plane_entries where quotation_id='$quotation_id_arr[$i]'");

			$sq_cruise_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_cruise_entries where quotation_id='$quotation_id_arr[$i]'"));
			$sq_train_cruise = mysqlQuery("select * from package_tour_quotation_cruise_entries where quotation_id='$quotation_id_arr[$i]'");
			$sq_ex_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_excursion_entries where quotation_id='$quotation_id_arr[$i]'"));
			$sq_ex = mysqlQuery("select * from package_tour_quotation_excursion_entries where quotation_id='$quotation_id_arr[$i]'");
			$sq_query = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='Package Quotation' and active_flag='Active'"));
			$sq_tours_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '$sq_quotation[package_id]'"));
			////////////////Currency conversion ////////////
			$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);
			$quotation_date = $sq_quotation['quotation_date'];
			$yr = explode("-", $quotation_date);
			$year = $yr[0];

			$content = '   
			<tr>
				<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
					<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
					background: #009898;">PACKAGE TOUR DETAILS</td></tr>
				</table>
			</tr>
			<tr>
				<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
					<tr><td style="text-align:left;border: 1px solid #888888;width:30%">Package Name</td>   <td style="text-align:left;border: 1px solid #888888;">' . $sq_tours_package['package_name'] . '</td></tr>
					<tr><td style="text-align:left;border: 1px solid #888888;width:30%">Duration</td>   <td style="text-align:left;border: 1px solid #888888;">' . $sq_quotation['total_days'] . 'N/' . ($sq_quotation['total_days'] + 1) . 'D' . '</td></tr>
					<tr><td style="text-align:left;border: 1px solid #888888;width:30%">Travel Date</td> <td style="text-align:left;border: 1px solid #888888;">' . date('d-m-Y', strtotime($sq_quotation['from_date'])) . ' To ' . date('d-m-Y', strtotime($sq_quotation['to_date'])) . '</td></tr>
					<tr><td style="text-align:left;border: 1px solid #888888;width:30%">Quotation ID</td>   <td style="text-align:left;border: 1px solid #888888;">' . get_quotation_id($sq_quotation['quotation_id'], $year) . '</td></tr>
					<tr><td style="text-align:left;border: 1px solid #888888;width:30%">Created By</td>   <td style="text-align:left;border: 1px solid #888888;">' . $emp_name . '</td></tr>
				</table>
			</tr>	
			<tr>
				<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
					<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
					background: #009898;">GUEST DETAILS</td></tr>
				</table>
			</tr>
			
			<tr>
				<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
					<tr>
						<td style="text-align:left;border: 1px solid #888888;width:30%">Adult(s)</td>   <td style="text-align:left;border: 1px solid #888888;">' . $sq_quotation['total_adult'] . '</td>
						<td style="text-align:left;border: 1px solid #888888;width:30%">Child With Bed</td><td style="text-align:left;border: 1px solid #888888;">' . $sq_quotation['children_with_bed'] . '</td>
					</tr>
					<tr>
						<td style="text-align:left;border: 1px solid #888888;width:30%">Child Without Bed</td> <td style="text-align:left;border: 1px solid #888888;">' . $sq_quotation['children_without_bed'] . '</td>
						<td style="text-align:left;border: 1px solid #888888;width:30%">Infant(s) </td>   <td style="text-align:left;border: 1px solid #888888;">' . $sq_quotation['total_infant'] . '</td>
					</tr>
					<tr><td style="text-align:left;border: 1px solid #888888;width:30%">Total</td>   <td style="text-align:left;border: 1px solid #888888;">' . $sq_quotation['total_passangers'] . '</td></tr>
				</table>
			</tr>';
			$content .= '
			<tr>
				<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
					<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
					background: #009898;">TOUR ITINERARY</td></tr>
				</table>
			</tr>';

			$count = 0;
			$i = 0;

			while ($row_itinarary = mysqli_fetch_assoc($sq_package_program)) {

				$dates = (array) get_dates_for_package_itineary($row_itinarary["quotation_id"]);
				$date_format = isset($dates[$i]) ? $dates[$i] : 'NA';

				$sq_day_image = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_images where quotation_id='$row_itinarary[quotation_id]' and package_id='$sq_quotation[package_id]'"));
				$day_url1 = explode(',', $sq_day_image['image_url']);
				$daywise_image = 'http://itourscloud.com/quotation_format_images/dummy-image.jpg';
				for ($count1 = 0; $count1 < sizeof($day_url1); $count1++) {
					$day_url2 = explode('=', $day_url1[$count1]);
					if ($day_url2[0] == $sq_quotation['package_id'] && $day_url2[1] == $row_itinarary['day_count']) {
						$daywise_image = $day_url2[2];
					}
				}

				$count++;
				$content .= '<tr>
					<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
						<tr>
							<td style="text-align:left;border: 1px solid #888888;width:20%"><b>Day : </b> ' . $count . ' (' . $date_format . ')' . '</td> 
							<td style="text-align:left;border: 1px solid #888888;width:60%"><b>Attraction : </b>' . $row_itinarary['attraction'] . '</td>
						</tr>
						
						<tr>
							<td style="text-align:left;border: 1px solid #888888;width:20%"><img src="' . $daywise_image . '" class="img-responsive" style="width:200px;height:200px"></td>
							<td style="text-align:left;border: 1px solid #888888;width:60%">' . $row_itinarary['day_wise_program'] . '</td> 
						</tr>
						
						<tr>
							<td style="text-align:left;border: 1px solid #888888;width:20%"><b>Overnight stay : </b>' . $row_itinarary['stay'] . '</td>
							<td style="text-align:left;border: 1px solid #888888;width:60%"><b>Meal Plan : </b>' . $row_itinarary['meal_plan'] . '</td>
						</tr>
					</table>
				</tr>
			';
			}

			if ($sq_hotel_count1 > 0) {
				$content .= '   
				
				<tr>
					<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
						<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
						background: #009898;">ACCOMMODATION DETAILS</td></tr>
					</table>
				</tr>';
				$sq_package_type = mysqlQuery("select DISTINCT(package_type) from package_tour_quotation_hotel_entries where quotation_id='$quotation_id' order by 
'$sq_cost[sort_order]'");

				while ($row_hotel1 = mysqli_fetch_assoc($sq_package_type)) {
					$content .= '
				<tr>
					<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
					<thead>
						<tr class="table-heading-row">
							<th colspan="6" style="text-align:left;border: 1px solid #888888;width:30%">Package Type - ' . $row_hotel1['package_type'] . '</th>
						</tr>
						<tr class="table-heading-row">
							<th style="text-align:left;border: 1px solid #888888;width:30%">City</th>
							<th style="text-align:left;border: 1px solid #888888;width:30%">Hotel Name</th>
							<th style="text-align:left;border: 1px solid #888888;width:30%">Hotel Category</th>
							<th style="text-align:left;border: 1px solid #888888;width:30%">Check-In</th>
							<th style="text-align:left;border: 1px solid #888888;width:30%">Check-Out</th>
						</tr>
					</thead>
					<tbody> 
					';
					$sq_package_type1 = mysqlQuery("select * from package_tour_quotation_hotel_entries where quotation_id='$quotation_id' and package_type='$row_hotel1[package_type]'");
					while ($row_hotels = mysqli_fetch_assoc($sq_package_type1)) {

						$hotel_name = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$row_hotels[hotel_name]'"));
						$city_name = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_hotels[city_name]'"));
						$content .= '	
						<tr>
							<td style="text-align:left;border: 1px solid #888888;width:30%">' . $city_name['city_name'] . '</td>
							<td style="text-align:left;border: 1px solid #888888;">' . $hotel_name['hotel_name'] . '</td>
							<td style="text-align:left;border: 1px solid #888888;width:30%">' . $row_hotels['hotel_type'] . '</td>   
							<td style="text-align:left;border: 1px solid #888888;">' . get_date_user($row_hotels['check_in']) . '</td>
							<td style="text-align:left;border: 1px solid #888888;">' . get_date_user($row_hotels['check_out']) . '</td>
						</tr>';
					}
					$content .= '
						<tbody> 
					</table>
				</tr>';
				}
			}

			if ($sq_trans_count > 0) {
				$content .= '   
			
			<tr>
				<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
					<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
					background: #009898;">TRANSPORT DETAILS</td></tr>
				</table>
			</tr>
			<tr>
				<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
				<thead>
					<tr class="table-heading-row">
						<th style="text-align:left;border: 1px solid #888888;width:30%">Vehicle</th>
						<th style="text-align:left;border: 1px solid #888888;width:30%">Start Date</th>
						<th style="text-align:left;border: 1px solid #888888;width:30%">End Date</th>
						<th style="text-align:left;border: 1px solid #888888;width:30%">Pickup Location</th>
						<th style="text-align:left;border: 1px solid #888888;width:30%">Drop Location</th>
						<th style="text-align:left;border: 1px solid #888888;width:30%">Service Duration</th>
						<th style="text-align:left;border: 1px solid #888888;width:30%">Total Vehicles</th>
					</tr>
				</thead>
				<tbody> 
				';
				while ($row_hotel = mysqli_fetch_assoc($sq_hotel)) {

					$transport_name = mysqli_fetch_assoc(mysqlQuery("select * from b2b_transfer_master where entry_id='$row_hotel[vehicle_name]'"));
					// Pickup
					if ($row_hotel['pickup_type'] == 'city') {
						$row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_hotel[pickup]'"));
						$pickup = $row['city_name'];
					} else if ($row_hotel['pickup_type'] == 'hotel') {
						$row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_hotel[pickup]'"));
						$pickup = $row['hotel_name'];
					} else {
						$row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_hotel[pickup]'"));
						$airport_nam = clean($row['airport_name']);
						$airport_code = clean($row['airport_code']);
						$pickup = $airport_nam . " (" . $airport_code . ")";
					}
					//Drop-off
					if ($row_hotel['drop_type'] == 'city') {
						$row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_hotel[drop]'"));
						$drop = $row['city_name'];
					} else if ($row_hotel['drop_type'] == 'hotel') {
						$row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_hotel[drop]'"));
						$drop = $row['hotel_name'];
					} else {
						$row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_hotel[drop]'"));
						$airport_nam = clean($row['airport_name']);
						$airport_code = clean($row['airport_code']);
						$drop = $airport_nam . " (" . $airport_code . ")";
					}
					$content .= '	
					<tr>
						<td style="text-align:left;border: 1px solid #888888;width:30%">' . $transport_name['vehicle_name'] . '</td>
						<td style="text-align:left;border: 1px solid #888888;">' . get_date_user($row_hotel['start_date']) . '</td>
						<td style="text-align:left;border: 1px solid #888888;">' . get_date_user($row_hotel['end_date']) . '</td>
						<td style="text-align:left;border: 1px solid #888888;width:30%">' . $pickup . '</td>   <td style="text-align:left;border: 1px solid #888888;">' . $drop . '</td>
						<td style="text-align:left;border: 1px solid #888888;">' . $row_hotel['service_duration'] . '</td>
						<td style="text-align:left;border: 1px solid #888888;">' . $row_hotel['vehicle_count'] . '</td>
					</tr>';
				}
				$content .= '
					<tbody> 
				</table>
			</tr>';
			}
			if ($sq_plane_count > 0) {
				$content .= '   
				
				<tr>
					<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
						<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
						background: #009898;">FLIGHT DETAILS</td></tr>
					</table>
				</tr>
				<tr>
					<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
					<thead>
						<tr class="table-heading-row">
							<th style="text-align:left;border: 1px solid #888888;width:30%">From Sector</th>
							<th style="text-align:left;border: 1px solid #888888;width:30%">To Sector</th>
							<th style="text-align:left;border: 1px solid #888888;width:30%">Airline</th>
							<th style="text-align:left;border: 1px solid #888888;width:30%">Class</th>
							<th style="text-align:left;border: 1px solid #888888;width:30%">Departure</th>
							<th style="text-align:left;border: 1px solid #888888;width:30%">Arrival</th>
						</tr>
					</thead>
					<tbody> 
					';
				while ($row_plane = mysqli_fetch_assoc($sq_plane)) {

					$sq_airline = mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$row_plane[airline_name]'"));
					$airline = ($row_plane['airline_name'] != '') ? $sq_airline['airline_name'] . ' (' . $sq_airline['airline_code'] . ')' : 'NA';
					$class = ($row_plane['class'] != '') ? $row_plane['class'] : 'NA';

					$content .= '	
						<tr>
							<td style="text-align:left;border: 1px solid #888888;width:30%">' . $row_plane['from_location'] . '</td>
							<td style="text-align:left;border: 1px solid #888888;">' . $row_plane['to_location'] . '</td>
							<td style="text-align:left;border: 1px solid #888888;width:30%">' . $airline . '</td> 
							<td style="text-align:left;border: 1px solid #888888;width:30%">' . $class . '</td>   
							<td style="text-align:left;border: 1px solid #888888;">' . date('d-m-Y H:i', strtotime($row_plane['dapart_time'])) . '</td>
							<td style="text-align:left;border: 1px solid #888888;">' . date('d-m-Y H:i', strtotime($row_plane['arraval_time'])) . '</td>
						</tr>';
				}
				$content .= '
						<tbody> 
					</table>
				</tr>';
			}
			if ($sq_train_count > 0) {
				$content .= '   
					<tr>
						<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
							<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
							background: #009898;">TRAIN DETAILS</td></tr>
						</table>
					</tr>
					<tr>
						<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
						<thead>
							<tr class="table-heading-row">
								<th style="text-align:left;border: 1px solid #888888;width:30%">From Location</th>
								<th style="text-align:left;border: 1px solid #888888;width:30%">To Location</th>
								<th style="text-align:left;border: 1px solid #888888;width:30%">Class Type</th>
								<th style="text-align:left;border: 1px solid #888888;width:30%">Departure</th>
								<th style="text-align:left;border: 1px solid #888888;width:30%">Arrival</th>
							</tr>
						</thead>
						<tbody> 
						';
				while ($row_train = mysqli_fetch_assoc($sq_train)) {

					$class = ($row_train['class'] != '') ? $row_train['class'] : 'NA';
					$content .= '
								<tr>
									<td style="text-align:left;border: 1px solid #888888;width:30%">' . $row_train['from_location'] . '</td>
									<td style="text-align:left;border: 1px solid #888888;">' . $row_train['to_location'] . '</td>
									<td style="text-align:left;border: 1px solid #888888;width:30%">' . $class . '</td>   
									<td style="text-align:left;border: 1px solid #888888;">' . date('d-m-Y H:i', strtotime($row_train['departure_date'])) . '</td>
									<td style="text-align:left;border: 1px solid #888888;">' . date('d-m-Y H:i', strtotime($row_train['arrival_date'])) . '</td>
								</tr>';
				}
				$content .= '
								<tbody> 
							</table>
						</tr>';
			}

			if ($sq_cruise_count > 0) {
				$content .= '   
							
							<tr>
								<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
									<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
									background: #009898;">CRUISE DETAILS</td></tr>
								</table>
							</tr>
							<tr>
								<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
								<thead>
									<tr class="table-heading-row">
										<th style="text-align:left;border: 1px solid #888888;width:30%">Departure D/T</th>
										<th style="text-align:left;border: 1px solid #888888;width:30%">Arrival D/T</th>
										<th style="text-align:left;border: 1px solid #888888;width:30%">Route</th>
										<th style="text-align:left;border: 1px solid #888888;width:30%">Cabin</th>
										<th style="text-align:left;border: 1px solid #888888;width:30%">Sharing</th>
									</tr>
								</thead>
								<tbody> 
								';
				while ($row_train = mysqli_fetch_assoc($sq_train_cruise)) {

					$content .= '	
									<tr>
										<td style="text-align:left;border: 1px solid #888888;width:30%">' . get_datetime_user($row_train['dept_datetime']) . '</td>
										<td style="text-align:left;border: 1px solid #888888;">' . get_datetime_user($row_train['arrival_datetime']) . '</td>
										<td style="text-align:left;border: 1px solid #888888;width:30%">' . $row_train['route'] . '</td> 
										<td style="text-align:left;border: 1px solid #888888;width:30%">' . $row_train['cabin'] . '</td>   
										<td style="text-align:left;border: 1px solid #888888;">' . $row_train['sharing'] . '</td>
									</tr>';
				}
				$content .= '
									<tbody> 
								</table>
							</tr>';
			}

			if ($sq_ex_count > 0) {
				$content .= '   
								
								<tr>
									<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
										<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
										background: #009898;">ACTIVITY DETAILS</td></tr>
									</table>
								</tr>
								<tr>
									<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
									<thead>
										<tr class="table-heading-row">
											<th style="text-align:left;border: 1px solid #888888;width:30%">Activity Date</th>
											<th style="text-align:left;border: 1px solid #888888;width:30%">City Name</th>
											<th style="text-align:left;border: 1px solid #888888;width:30%">Activity Name</th>
											<th style="text-align:left;border: 1px solid #888888;width:30%">Transfer option</th>
											<th style="text-align:left;border: 1px solid #888888;width:30%">Adult</th>
											<th style="text-align:left;border: 1px solid #888888;width:30%">CWB</th>
											<th style="text-align:left;border: 1px solid #888888;width:30%">CWOB</th>
											<th style="text-align:left;border: 1px solid #888888;width:30%">Infant</th>
											<th style="text-align:left;border: 1px solid #888888;width:30%">Vehicle</th>
										</tr>
									</thead>
									<tbody> 
									';
				while ($row_ex = mysqli_fetch_assoc($sq_ex)) {
					$sq_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_ex[city_name]'"));
					$sq_ex_name = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master_tariff where entry_id='$row_ex[excursion_name]'"));

					$content .= '	
											<tr>
												<td style="text-align:left;border: 1px solid #888888;width:30%">' . get_datetime_user($row_ex['exc_date']) . '</td>
												<td style="text-align:left;border: 1px solid #888888;">' . $sq_city['city_name'] . '</td>
												<td style="text-align:left;border: 1px solid #888888;width:30%">' . $sq_ex_name['excursion_name'] . '</td> 
												<td style="text-align:left;border: 1px solid #888888;">' . $row_ex['transfer_option'] . '</td>
												<td style="text-align:left;border: 1px solid #888888;">' . $row_ex['adult'] . '</td>
												<td style="text-align:left;border: 1px solid #888888;">' . $row_ex['chwb'] . '</td>
												<td style="text-align:left;border: 1px solid #888888;">' . $row_ex['chwob'] . '</td>
												<td style="text-align:left;border: 1px solid #888888;">' . $row_ex['infant'] . '</td>
												<td style="text-align:left;border: 1px solid #888888;">' . $row_ex['vehicles'] . '</td>
											</tr>';
				}
				$content .= '
										<tbody> 
									</table>
								</tr>';
			}
			if (isset($sq_query['terms_and_conditions'])) {
				$content .= '<tr>
								<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
									<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
									background: #009898;">TERMS AND CONDITIONS</td></tr>
								</table>
							</tr>
							<tr>
								<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
									<tr>
										<td style="text-align:left;border: 1px solid #888888;width:100%"><pre>' . $sq_query['terms_and_conditions'] . '</pre></td></tr>
								</table>
							</tr>';
			}
			$content .= '<tr>
						<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
							<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
							background: #009898;">INCLUSIONS</td></tr>
						</table>
					</tr>
					<tr>
						<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
							<tr>
								<td style="text-align:left;border: 1px solid #888888;width:100%"><pre>' . $sq_quotation['inclusions'] . '</pre></td></tr>
						</table>
					</tr>';
			$content .= '<tr>
						<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
							<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
							background: #009898;">EXCLUSIONS</td></tr>
						</table>
					</tr>
					<tr>
						<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
							<tr>
								<td style="text-align:left;border: 1px solid #888888;width:100%"><pre>' . $sq_quotation['exclusions'] . '</pre></td></tr>
						</table>
					</tr>';
			if ($sq_tours_package['note'] != '') {
				$content .= '<tr>
							<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
								<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
								background: #009898;">NOTE</td></tr>
							</table>
						</tr>
						<tr>
							<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
								<tr>
									<td style="text-align:left;border: 1px solid #888888;width:100%"><pre>' . $sq_tours_package['note'] . '</pre></td></tr>
							</table>
						</tr>';
			}
			if ($sq_quotation['other_desc'] != '') {
				$content .= '<tr>
							<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
								<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
								background: #009898;">MISCELLANEOUS DESCRIPTION</td></tr>
							</table>
						</tr>
						<tr>
							<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
								<tr>
									<td style="text-align:left;border: 1px solid #888888;width:100%"><pre>' . $sq_quotation['other_desc'] . '</pre></td></tr>
							</table>
						</tr>';
			}

			$content .= '
						<tr>
							<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
								<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
								background: #009898;">BANK DETAILS</td></tr>
							</table>
						</tr>
						<tr>
							<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
							<thead>
								<tr class="table-heading-row">
									<th style="text-align:left;border: 1px solid #888888;width:30%">Bank Name</th>
									<th style="text-align:left;border: 1px solid #888888;width:30%">Branch</th>
									<th style="text-align:left;border: 1px solid #888888;width:30%">A/C Type</th>
									<th style="text-align:left;border: 1px solid #888888;width:30%">Bank Account Name</th>
									<th style="text-align:left;border: 1px solid #888888;width:30%">A/C No</th>
									
									<th style="text-align:left;border: 1px solid #888888;width:30%">SWIFT Code</th>
								</tr>
							</thead>
							<tbody> 
							';
			$branch_admin_id = $_SESSION['branch_admin_id'];
			global $bank_name_setting, $bank_branch_name, $acc_name, $bank_acc_no, $bank_account_name, $bank_swift_code;
			if ($branch_admin_id != 0) {
				$sq_bank_count = mysqli_num_rows(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id' and active_flag='Active'"));
				$sq_bank_branch = mysqli_fetch_assoc(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id' and active_flag='Active'"));
			} else {
				$sq_bank_count = mysqli_num_rows(mysqlQuery("select * from bank_master where branch_id='1' and active_flag='Active'"));
				$sq_bank_branch = mysqli_fetch_assoc(mysqlQuery("select * from bank_master where branch_id='1' and active_flag='Active'"));
			}
			$bank_name = ($sq_bank_count > 0 || $sq_bank_branch['bank_name'] != '') ? $sq_bank_branch['bank_name'] : $bank_name_setting;
			$branch_name = ($sq_bank_count > 0 || $sq_bank_branch['branch_name'] != '') ? $sq_bank_branch['branch_name'] : $bank_branch_name;
			$acc_type = ($sq_bank_count > 0 || $sq_bank_branch['account_type'] != '') ? $sq_bank_branch['account_type'] : $acc_name;
			$acc_no = ($sq_bank_count > 0 || $sq_bank_branch['account_no'] != '') ? $sq_bank_branch['account_no'] : $bank_acc_no;
			$acc_name1 = ($sq_bank_count > 0 || $sq_bank_branch['account_name'] != '') ? $sq_bank_branch['account_name'] : $bank_account_name;
			$swift_code = ($sq_bank_count > 0 || $sq_bank_branch['swift_code'] != '') ? strtoupper($sq_bank_branch['swift_code']) :  strtoupper($bank_swift_code);
			$content .= '	
									<tr>
										<td style="text-align:left;border: 1px solid #888888;width:30%">' . $bank_name . '</td>
										<td style="text-align:left;border: 1px solid #888888;">' . $branch_name . '</td>
										<td style="text-align:left;border: 1px solid #888888;width:30%">' . $acc_type . '</td> 
										<td style="text-align:left;border: 1px solid #888888;">' . $acc_name1 . '</td>
										<td style="text-align:left;border: 1px solid #888888;">' . $acc_no . '</td>
										<td style="text-align:left;border: 1px solid #888888;">' . $swift_code . '</td>
									</tr>';
			if (check_qr()) {

				$content .= '<tr> 
									<td style="text-align:left;width:30%" colspan=2>QR Code</td>
									<td style="text-align:left;width:30%" colspan=4>' . get_qr('general') . ' </td>
									
									</tr>';
			}
			$content .= '
								<tbody> 
							</table>
						</tr>';

			$content .= '
						<tr>
							<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
								<tr><td style="text-align:center;border: 1px solid #888888;width:1000%;color: #fff;
								background: #009898;">COSTING DETAILS</td></tr>
							</table>
						</tr>';
			if ($sq_quotation['costing_type'] == 2) {

				$sq_costing1 = mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id' order by sort_order");
				while ($sq_costing = mysqli_fetch_assoc($sq_costing1)) {

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
					$per_service_charge = (float)($service_charge) / (float)($total_pax);
					$o_per_service_charge = (float)($sq_costing['service_charge']) / (float)($total_pax);

					$adult_cost = ($sq_quotation['total_adult'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['adult_cost'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);
					$child_with = ($sq_quotation['children_with_bed'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['child_with'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);
					$child_without = ($sq_quotation['children_without_bed'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['child_without'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);
					$infant_cost = ($sq_quotation['total_infant'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['infant_cost'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);

					// Without currency
					$adult_costw = ($sq_quotation['total_adult'] != '0') ? ((float)($sq_costing['adult_cost'] + (float)($per_service_charge)) * intval($sq_quotation['total_adult'])) : 0;
					$child_withw = ($sq_quotation['children_with_bed'] != '0') ? ((float)($sq_costing['child_with'] + (float)($per_service_charge)) * intval($sq_quotation['children_with_bed'])) : 0;
					$child_withoutw = ($sq_quotation['children_without_bed'] != '0') ? ((float)($sq_costing['child_without'] + (float)($per_service_charge)) * intval($sq_quotation['children_without_bed'])) : 0;
					$infant_costw = ($sq_quotation['total_infant'] != '0') ? ((float)($sq_costing['infant_cost'] + (float)($per_service_charge)) * intval($sq_quotation['total_infant'])) : 0;
					$o_adult_costw = ($sq_quotation['total_adult'] != '0') ? ((float)($sq_costing['adult_cost'] + (float)($o_per_service_charge)) * intval($sq_quotation['total_adult'])) : 0;
					$o_child_withw = ($sq_quotation['children_with_bed'] != '0') ? ((float)($sq_costing['child_with'] + (float)($o_per_service_charge)) * intval($sq_quotation['children_with_bed'])) : 0;
					$o_child_withoutw = ($sq_quotation['children_without_bed'] != '0') ? ((float)($sq_costing['child_without'] + (float)($o_per_service_charge)) * intval($sq_quotation['children_without_bed'])) : 0;
					$o_infant_costw = ($sq_quotation['total_infant'] != '0') ? ((float)($sq_costing['infant_cost'] + (float)($o_per_service_charge)) * intval($sq_quotation['total_infant'])) : 0;

					$service_tax_amount = 0;
					$tax_show = '';
					$bsmValues = json_decode($sq_costing['bsmValues']);
					$name = '';
					if ($sq_costing['service_tax_subtotal'] !== 0.00 && ($sq_costing['service_tax_subtotal']) !== '') {
						$service_tax_subtotal1 = explode(',', $sq_costing['service_tax_subtotal']);
						for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
							$service_tax = explode(':', $service_tax_subtotal1[$i]);
							$service_tax_amount = (float)($service_tax_amount) + (float)($service_tax[2]);
							$name .= $service_tax[0] . $service_tax[1] . ', ';
						}
					}

					if ($bsmValues[0]->tcsvalue != '') {
						$tcsvalue = $bsmValues[0]->tcsvalue;
					} else {
						$tcsvalue = '0';
					}
					$tcs_amount_show  = currency_conversion($currency, $sq_quotation['currency_code'], $tcsvalue);


					$service_tax_amount_show = currency_conversion($currency, $sq_quotation['currency_code'], $service_tax_amount);

					$total_child = (float)($sq_quotation['children_with_bed']) + (float)($sq_quotation['children_without_bed']);

					$quotation_cost = (float)($adult_costw) + (float)($child_withw) + (float)($child_withoutw) + (float)($infant_costw);
					$o_quotation_cost = (float)($o_adult_costw) + (float)($o_child_withw) + (float)($o_child_withoutw) + (float)($o_infant_costw);

					$other_cost = $service_tax_amount + $sq_quotation['visa_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'];
					$travel_cost = ($sq_plane_count > 0) ? $sq_quotation['flight_ccost'] + $sq_quotation['flight_icost'] + $sq_quotation['flight_acost'] : 0;
					$travel_cost += ($sq_train_count > 0) ? $sq_quotation['train_ccost'] + $sq_quotation['train_icost'] + $sq_quotation['train_acost'] : 0;
					$travel_cost += ($sq_cruise_count > 0) ?  $sq_quotation['cruise_acost'] + $sq_quotation['cruise_icost'] + $sq_quotation['cruise_ccost'] : 0;


					$train_cost_a = $sq_quotation['train_acost'] * intval($sq_quotation['total_adult']);
					$train_cost_cw = $sq_quotation['train_ccost'] * intval($sq_quotation['children_with_bed']);
					$train_cost_cwo =  $sq_quotation['train_ccost'] * intval($sq_quotation['children_without_bed']);
					$train_cost_i = $sq_quotation['train_icost'] * intval($sq_quotation['total_infant']);

					$train_total_cost = ($sq_train_count > 0) ? $train_cost_a + $train_cost_cw + $train_cost_cwo + $train_cost_i : 0;
					// flight cost
					$flight_cost_a = $sq_quotation['flight_acost'] * intval($sq_quotation['total_adult']);
					$flight_cost_cw = $sq_quotation['flight_ccost'] * intval($sq_quotation['children_with_bed']);
					$flight_cost_cwo =  $sq_quotation['flight_ccost'] * intval($sq_quotation['children_without_bed']);
					$flight_cost_i = $sq_quotation['flight_icost'] * intval($sq_quotation['total_infant']);

					$flight_total_cost = ($sq_plane_count > 0) ? $flight_cost_a + $flight_cost_cw + $flight_cost_cwo + $flight_cost_i : 0;
					// Cruise cost


					$cruise_cost_a = $sq_quotation['cruise_acost'] * intval($sq_quotation['total_adult']);
					$cruise_cost_cw = $sq_quotation['cruise_ccost'] * intval($sq_quotation['children_with_bed']);
					$cruise_cost_cwo =  $sq_quotation['cruise_ccost'] * intval($sq_quotation['children_without_bed']);
					$cruise_cost_i = $sq_quotation['cruise_icost'] * intval($sq_quotation['total_infant']);

					$cruise_total_cost = ($sq_cruise_count > 0) ? $cruise_cost_a + $cruise_cost_cw + $cruise_cost_cwo + $cruise_cost_i : 0;


					$quotation_cost = (float)($quotation_cost) +  (float)($train_total_cost) + (float)($flight_total_cost) + (float)($cruise_total_cost) + (float)($other_cost) + (float)($tcsvalue);
					// $quotation_cost = ceil($quotation_cost);
					$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);
					$o_quotation_cost = (float)($o_quotation_cost) + (float)($train_total_cost) + (float)($flight_total_cost) + (float)($cruise_total_cost) + (float)($other_cost);

					$act_tour_cost_camount = ($discount != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], $o_quotation_cost + (float)($tcsvalue)) : '';

					$content .= '
							<tr>
								<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
									<tr>
										<td colspan="4" style="text-align:left;border: 1px solid #888888;width:30%">Package Type: ' . $sq_costing['package_type'] . ' (' . $currency_amount1 . ' <s>' . $act_tour_cost_camount . '</s>)' . '</td>
									</tr>
									<tr>
										<td style="text-align:left;border: 1px solid #888888;width:30%">Adult Cost</td>  <td style="text-align:left;border: 1px solid #888888;">' . $adult_cost . '</td>
										<td style="text-align:left;border: 1px solid #888888;width:30%">Child with Bed Cost </td>   <td style="text-align:left;border: 1px solid #888888;">' . $child_with . '</td>
									</tr>
									<tr>
										<td style="text-align:left;border: 1px solid #888888;width:30%">Child w/o Bed Cost</td><td style="text-align:left;border: 1px solid #888888;">' . $child_without . '</td>
										<td style="text-align:left;border: 1px solid #888888;width:30%">Infant Cost</td> <td style="text-align:left;border: 1px solid #888888;">' . $infant_cost . '</td>
									</tr>
									<tr>
										<td style="text-align:left;border: 1px solid #888888;width:30%">Tax</td><td style="text-align:left;border: 1px solid #888888;">' . $service_tax_amount_show . '</td>
										<td style="text-align:left;border: 1px solid #888888;width:30%">Visa Cost </td> <td style="text-align:left;border: 1px solid #888888;">' . currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['visa_cost']) . '</td>
									</tr>
									<tr>
										<td style="text-align:left;border: 1px solid #888888;width:30%">Guide Cost </td> <td style="text-align:left;border: 1px solid #888888;">' . currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['guide_cost']) . '</td>
										<td style="text-align:left;border: 1px solid #888888;width:30%">Miscellaneous Cost </td> <td style="text-align:left;border: 1px solid #888888;">' . currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['misc_cost']) . '</td>
										
									</tr>
									<tr><td style="text-align:left;border: 1px solid #888888;width:30%">Tcs </td> <td style="text-align:left;border: 1px solid #888888;">' . $tcs_amount_show . '</td>
									<td style="text-align:left;border: 1px solid #888888;width:30%"> </td> <td style="text-align:left;border: 1px solid #888888;"></td>
									</tr>
								</table>
							</tr>';
					if ($sq_plane_count > 0 || $sq_train_count > 0 || $sq_cruise_count > 0) {
						$content .= '<tr>
									<table width="85%" cellspacing="0" cellpadding="4" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
									<thead>
										<tr class="table-heading-row">
											<th style="text-align:left;border: 1px solid #888888;width:30%">Travel_Type</th>
											<th style="text-align:left;border: 1px solid #888888;width:30%">Adult(PP)</th>
											<th style="text-align:left;border: 1px solid #888888;width:30%">Child(PP)</th>
											<th style="text-align:left;border: 1px solid #888888;width:30%">Infant(PP)</th>
										</tr>
									</thead>
									<tbody>';
						if ($sq_plane_count > 0) {

							$content .= '	
										<tr>
											<td style="text-align:left;border: 1px solid #888888;width:30%">Flight</td>
											<td style="text-align:left;border: 1px solid #888888;">' . ($sq_quotation['total_adult'] != 0
								? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['flight_acost']))
								: currency_conversion($currency, $sq_quotation['currency_code'], (float)(0))) . '</td>
											<td style="text-align:left;border: 1px solid #888888;width:30%">' . (($sq_quotation['children_with_bed'] != 0 || $sq_quotation['children_without_bed'] != 0)
								? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['flight_ccost']))
								: currency_conversion($currency, $sq_quotation['currency_code'], (float)(0))) . '</td> 
											<td style="text-align:left;border: 1px solid #888888;width:30%">' . ($sq_quotation['total_infant'] != 0
								? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['flight_icost']))
								: currency_conversion($currency, $sq_quotation['currency_code'], (float)(0))) . '</td>
										</tr>';
						}
						if ($sq_train_count > 0) {

							$content .= '
    <tr>
        <td style="text-align:left;border: 1px solid #888888;width:30%">Train</td>
        <td style="text-align:left;border: 1px solid #888888;">' .
								($sq_quotation['total_adult'] != 0
									? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['train_acost']))
									: currency_conversion($currency, $sq_quotation['currency_code'], (float)(0))) .
								'</td>
        <td style="text-align:left;border: 1px solid #888888;width:30%">' .
								(($sq_quotation['children_with_bed'] != 0 || $sq_quotation['children_without_bed'] != 0)
									? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['train_ccost']))
									: currency_conversion($currency, $sq_quotation['currency_code'], (float)(0))) .
								'</td> 
        <td style="text-align:left;border: 1px solid #888888;width:30%">' .
								($sq_quotation['total_infant'] != 0
									? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['train_icost']))
									: currency_conversion($currency, $sq_quotation['currency_code'], (float)(0))) .
								'</td>
    </tr>';
						}
						if ($sq_cruise_count > 0) {

							$content .= '	
										<tr>
											<td style="text-align:left;border: 1px solid #888888;width:30%">Cruise</td>
											<td style="text-align:left;border: 1px solid #888888;">' . ($sq_quotation['total_adult'] != 0
								? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['cruise_acost']))
								: currency_conversion($currency, $sq_quotation['currency_code'], (float)(0))) . '</td>
											<td style="text-align:left;border: 1px solid #888888;width:30%">' . (($sq_quotation['children_with_bed'] != 0 || $sq_quotation['children_without_bed'] != 0)
								? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['cruise_ccost']))
								: currency_conversion($currency, $sq_quotation['currency_code'], (float)(0))) . '</td> 
											<td style="text-align:left;border: 1px solid #888888;width:30%">' . ($sq_quotation['total_infant'] != 0
								? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['cruise_icost']))
								: currency_conversion($currency, $sq_quotation['currency_code'], (float)(0))) . '</td>
										</tr>';
						}
						// if($tcs_note_show != ''){
						// 	$content .= '
						// 	<tr>
						// 		<table style="width:100%;margin-top:10px">
						// 			<tr>
						// 				<td style="padding-left: 10px;border-bottom: 1px solid #eee;"><span style="font-weight: 600; color: '.$theme_color.'">'.$tcs_note_show.'</span></td>
						// 			</tr>
						// 		</table>	
						// 	<tr>';
						// }
						$content .= '
									<tbody> 
								</table>
							</tr>';
					}
				}
			} else {
				$sq_costing1 = mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id' order by sort_order limit 1");
				while ($sq_costing = mysqli_fetch_assoc($sq_costing1)) {

					$basic_cost = $sq_costing['basic_amount'];
					$service_charge = $sq_costing['service_charge'];
					$service_tax_amount = 0;
					$tax_show = '';
					$bsmValues = json_decode($sq_costing['bsmValues']);
					$discount_in = $sq_costing['discount_in'];
					$discount = $sq_costing['discount'];
					if ($discount_in == 'Percentage') {
						$act_discount = (float)($service_charge) * (float)($discount) / 100;
					} else {
						$act_discount = ($service_charge != 0) ? $discount : 0;
					}
					$service_charge = $service_charge - (float)($act_discount);
					$tour_cost = $basic_cost + $service_charge;

					$name = '';
					if ($sq_costing['service_tax_subtotal'] !== 0.00 && ($sq_costing['service_tax_subtotal']) !== '') {
						$service_tax_subtotal1 = explode(',', $sq_costing['service_tax_subtotal']);
						for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
							$service_tax = explode(':', $service_tax_subtotal1[$i]);
							$service_tax_amount = (float)($service_tax_amount) + (float)($service_tax[2]);
							$name .= $service_tax[0] . $service_tax[1] . ', ';
						}
					}

					if ($bsmValues[0]->tcsvalue != '') {
						$tcsvalue = $bsmValues[0]->tcsvalue;
					} else {
						$tcsvalue = '0';
					}



					$service_tax_amount_show = currency_conversion($currency, $sq_quotation['currency_code'], $service_tax_amount);
					$quotation_cost = (float)($basic_cost) + (float)($service_charge) + (float)($service_tax_amount) + (float)($sq_quotation['train_cost']) + (float)($sq_quotation['cruise_cost']) + (float)($sq_quotation['flight_cost']) + (float)($sq_quotation['visa_cost']) + (float)($sq_quotation['guide_cost']) + (float)($sq_quotation['misc_cost']) + (float)($tcsvalue);
					// $quotation_cost = ceil($quotation_cost);

					$quotation_cost = floor($quotation_cost);
					$tcsvalue = currency_conversion($currency, $sq_quotation['currency_code'], $tcsvalue);
					////////////////Currency conversion ////////////
					$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);
					$act_tour_cost = (float)($quotation_cost) - (float)($service_charge) + (float)($sq_costing['service_charge']);
					$act_tour_cost = ceil($act_tour_cost);
					$act_tour_cost_camount = ($discount != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], $act_tour_cost) : '';

					$newBasic = currency_conversion($currency, $sq_quotation['currency_code'], $tour_cost);
					$travel_cost = (float)($sq_quotation['train_cost']) + (float)($sq_quotation['flight_cost']) + (float)($sq_quotation['cruise_cost']) + (float)($sq_quotation['visa_cost']) + (float)($sq_quotation['guide_cost']) + (float)($sq_quotation['misc_cost']);
					$travel_cost = currency_conversion($currency, $sq_quotation['currency_code'], $travel_cost);
					$content .= '
								<tr>
									<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
										<tr><td colspan="6" style="text-align:left;border: 1px solid #888888;width:30%">' . $sq_costing['package_type'] . '</td></tr>
										<tr><td style="text-align:left;border: 1px solid #888888;width:30%">Tour Cost</td>   <td style="text-align:left;border: 1px solid #888888;">' . $newBasic . '</td><td colspan="1" style="text-align:left;border: 1px solid #888888;width:30%">Tax </td>   <td style="text-align:left;border: 1px solid #888888;">' . $service_tax_amount_show . '</td><td style="text-align:left;border: 1px solid #888888;width:30%">Tcs</td><td colspan="4" style="text-align:left;border: 1px solid #888888;width:30%">' . $tcsvalue . '</td></tr>
										<tr><td style="text-align:left;border: 1px solid #888888;width:30%">Travel and other cost</td><td style="text-align:left;border: 1px solid #888888;">' . $travel_cost . '</td><td style="text-align:left;border: 1px solid #888888;width:30%">Quotation Cost</td> <td style="text-align:left;border: 1px solid #888888;">' . $currency_amount1 . ' <s>' . $act_tour_cost_camount . '</s>' . '</td><td style="text-align:left;border: 1px solid #888888;width:30%"> </td><td style="text-align:left;border: 1px solid #888888;width:30%"></td></tr>
									</table>
								</tr>';
					// for cond end
					// if($tcs_note_show != ''){
					// 	$content .= '<tr> 
					// 	<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;margin: 0px auto;margin-top:10px; min-width: 100%;" role="presentation">
					// 	<tr><td style="text-align:left;width:30%;margin-left:30px!important;" colspan=6>'.$tcs_note_show.' </td></tr>
					// 	</table>
					// 	</tr>';
					// }
				}
			}
		}
		$content .= '
		<tr>
			<table style="width:100%;margin-top:20px">
				<tr>
					<td style="padding-left: 10px;border-bottom: 1px solid #eee;"><span style="font-weight: 600; color: ' . $theme_color . '">' . $quot_note . '</span></td>
				</tr>
			</table>	
		<tr>';

		$subject = 'New Quotation : (' . $sq_tours_package['package_name'] . ' )';
		$model->app_email_send('8', $sq_quotation['customer_name'], $sq_quotation['email_id'], $content, $subject, '1');

		echo "Quotation successfully sent.";
		exit;
	}

	public function quotation_whatsapp()
	{

		$quotation_id_arr = $_POST['quotation_id_arr'];
		global $app_contact_no, $app_name, $currency;
		$all_message = "";
		$planedata = "";
		$hoteldata = "";
		$itninarydata = "";
		$transportationdata = "";
		$actvitydata = "";
		$traindata = "";
		$cruisedata = "";
		// Function to extract numeric values
		function extract_numeric($value)
		{
			return (float) preg_replace('/[^0-9.]/', '', $value);
		}
		for ($i = 0; $i < sizeof($quotation_id_arr); $i++) {
			$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id_arr[$i]'"));
			$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_quotation[emp_id]'"));
			if ($sq_quotation['emp_id'] == 0) {
				$contact = $app_contact_no;
			} else {
				$contact = $sq_emp_info['mobile_no'];
			}


			$sq_tours_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '$sq_quotation[package_id]'"));

			$quotation_date = $sq_quotation['quotation_date'];
			$quotation_id = $sq_quotation['quotation_id'];
			$yr = explode("-", $quotation_date);
			$year = $yr[0];
			$quoatationid = get_quotation_id($sq_quotation['quotation_id'], $year);
			$app_name = !empty($app_name) ? $app_name : "ITOURS LLP PVT LTDS"; // Set your default value here
			$tourname = $sq_tours_package['package_name'];
			$quotationdate = get_date_user($sq_quotation['quotation_date']);
			$from_date = get_date_user($sq_quotation['from_date']);
			$duration = $sq_quotation['total_days'] . ' Nights, ' . ($sq_quotation['total_days'] + 1) . ' Days';
			$adults = $sq_quotation['total_adult'];
			$childs = $sq_quotation['children_with_bed'] + $sq_quotation['children_without_bed'];
			$infants = $sq_quotation['total_infant'];
			$child_wb = $sq_quotation['children_with_bed'];
			$child_wob = $sq_quotation['children_without_bed'];
			/* tour costing  start*/


			if ($sq_quotation['costing_type'] == 2) {

				$sq_costing1 = mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id'  order by sort_order limit 1");
				while ($sq_costing = mysqli_fetch_assoc($sq_costing1)) {

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
					$per_service_charge = (float)($service_charge) / (float)($total_pax);
					$o_per_service_charge = (float)($sq_costing['service_charge']) / (float)($total_pax);

					$adult_cost = ($sq_quotation['total_adult'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['adult_cost'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);

					$adult_cost1 = ((float)($sq_costing['adult_cost'] + (float)($per_service_charge)));

					$child_with = ($sq_quotation['children_with_bed'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['child_with'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);
					$child_with1 = ((float)($sq_costing['child_with'] + (float)($per_service_charge)));

					$child_without = ($sq_quotation['children_without_bed'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['child_without'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);

					$child_without1 = ((float)($sq_costing['child_without'] + (float)($per_service_charge)));

					$infant_cost = ($sq_quotation['total_infant'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['infant_cost'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);

					$infant_cost1 = ((float)($sq_costing['infant_cost'] + (float)($per_service_charge)));

					// Without currency
					$adult_costw = ($sq_quotation['total_adult'] != '0') ? ((float)($sq_costing['adult_cost'] + (float)($per_service_charge)) * intval($sq_quotation['total_adult'])) : 0;
					$child_withw = ($sq_quotation['children_with_bed'] != '0') ? ((float)($sq_costing['child_with'] + (float)($per_service_charge)) * intval($sq_quotation['children_with_bed'])) : 0;
					$child_withoutw = ($sq_quotation['children_without_bed'] != '0') ? ((float)($sq_costing['child_without'] + (float)($per_service_charge)) * intval($sq_quotation['children_without_bed'])) : 0;
					$infant_costw = ($sq_quotation['total_infant'] != '0') ? ((float)($sq_costing['infant_cost'] + (float)($per_service_charge)) * intval($sq_quotation['total_infant'])) : 0;
					$o_adult_costw = ($sq_quotation['total_adult'] != '0') ? ((float)($sq_costing['adult_cost'] + (float)($o_per_service_charge)) * intval($sq_quotation['total_adult'])) : 0;
					$o_child_withw = ($sq_quotation['children_with_bed'] != '0') ? ((float)($sq_costing['child_with'] + (float)($o_per_service_charge)) * intval($sq_quotation['children_with_bed'])) : 0;
					$o_child_withoutw = ($sq_quotation['children_without_bed'] != '0') ? ((float)($sq_costing['child_without'] + (float)($o_per_service_charge)) * intval($sq_quotation['children_without_bed'])) : 0;
					$o_infant_costw = ($sq_quotation['total_infant'] != '0') ? ((float)($sq_costing['infant_cost'] + (float)($o_per_service_charge)) * intval($sq_quotation['total_infant'])) : 0;

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
					$service_tax_amount_show = currency_conversion($currency, $sq_quotation['currency_code'], $service_tax_amount);

					$tax1 = $service_tax_amount;

					$total_child = (float)($sq_quotation['children_with_bed']) + (float)($sq_quotation['children_without_bed']);

					$quotation_cost = (float)($adult_costw) + (float)($child_withw) + (float)($child_withoutw) + (float)($infant_costw);
					$o_quotation_cost = (float)($o_adult_costw) + (float)($o_child_withw) + (float)($o_child_withoutw) + (float)($o_infant_costw);

					$other_cost = $service_tax_amount + $sq_quotation['visa_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'];
					$travel_cost = ($sq_plane_count > 0) ? $sq_quotation['flight_ccost'] + $sq_quotation['flight_icost'] + $sq_quotation['flight_acost'] : 0;
					$travel_cost += ($sq_train_count > 0) ? $sq_quotation['train_ccost'] + $sq_quotation['train_icost'] + $sq_quotation['train_acost'] : 0;
					$travel_cost += ($sq_cruise_count > 0) ?  $sq_quotation['cruise_acost'] + $sq_quotation['cruise_icost'] + $sq_quotation['cruise_ccost'] : 0;

					$train_cost_a = $sq_quotation['train_acost'] * intval($sq_quotation['total_adult']);
					$train_cost_cw = $sq_quotation['train_ccost'] * intval($sq_quotation['children_with_bed']);
					$train_cost_cwo =  $sq_quotation['train_ccost'] * intval($sq_quotation['children_without_bed']);
					$train_cost_i = $sq_quotation['train_icost'] * intval($sq_quotation['total_infant']);

					$train_total_cost = ($sq_train_count > 0) ? $train_cost_a + $train_cost_cw + $train_cost_cwo + $train_cost_i : 0;
					// flight cost
					$flight_cost_a = $sq_quotation['flight_acost'] * intval($sq_quotation['total_adult']);
					$flight_cost_cw = $sq_quotation['flight_ccost'] * intval($sq_quotation['children_with_bed']);
					$flight_cost_cwo =  $sq_quotation['flight_ccost'] * intval($sq_quotation['children_without_bed']);
					$flight_cost_i = $sq_quotation['flight_icost'] * intval($sq_quotation['total_infant']);

					$flight_total_cost = ($sq_plane_count > 0) ? $flight_cost_a + $flight_cost_cw + $flight_cost_cwo + $flight_cost_i : 0;
					// Cruise cost


					$cruise_cost_a = $sq_quotation['cruise_acost'] * intval($sq_quotation['total_adult']);
					$cruise_cost_cw = $sq_quotation['cruise_ccost'] * intval($sq_quotation['children_with_bed']);
					$cruise_cost_cwo =  $sq_quotation['cruise_ccost'] * intval($sq_quotation['children_without_bed']);
					$cruise_cost_i = $sq_quotation['cruise_icost'] * intval($sq_quotation['total_infant']);

					$cruise_total_cost = ($sq_cruise_count > 0) ? $cruise_cost_a + $cruise_cost_cw + $cruise_cost_cwo + $cruise_cost_i : 0;




					$quotation_cost = (float)($quotation_cost) + (float)($train_total_cost) + (float)($flight_total_cost) + (float)($cruise_total_cost) + (float)($other_cost) + (float)($tcsvalue);
					//   $quotation_cost = ceil($quotation_cost);
					$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);
					$tcs_show1 = currency_conversion($currency, $sq_quotation['currency_code'], $tcsvalue);
					$o_quotation_cost = (float)($o_quotation_cost) + (float)($train_total_cost) + (float)($flight_total_cost) + (float)($cruise_total_cost) + (float)($other_cost) + (float)($tcsvalue);
					//   $o_quotation_cost = ceil($o_quotation_cost);
					//   $act_tour_cost_camount = ($discount!=0) ? currency_conversion($currency, $sq_quotation['currency_code'], $o_quotation_cost) : ''; 








					$tax = str_replace(',', '', $name) . $service_tax_amount_show;
					$tcs_cost = '(' . $tcsper . '%) ' . $tcs_show1;
					$visa = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['visa_cost']);
					$visa1 = $sq_quotation['visa_cost'];
					$guide = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['guide_cost']);

					$guide1 = $sq_quotation['guide_cost'];
					$misc = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['misc_cost']);
					$misc1 = $sq_quotation['misc_cost'];
					$flight_a = $sq_quotation['total_adult'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['flight_acost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));
					$flight_a1 = $sq_quotation['flight_acost'];
					$flight_cwb = ($sq_quotation['children_with_bed'] != 0 || $sq_quotation['children_without_bed'] != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['flight_ccost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));

					$flight_cwb1 = $sq_quotation['flight_ccost'];

					$flight_i = $sq_quotation['total_infant'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['flight_icost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));

					$flight_i1 = $sq_quotation['flight_icost'];

					$train_a = $sq_quotation['total_adult'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['train_acost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));

					$train_a1 = (float)($sq_quotation['train_acost']);

					$train_cwb = ($sq_quotation['children_with_bed'] != 0 || $sq_quotation['children_without_bed'] != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['train_ccost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));


					$train_cwb1 = (float)($sq_quotation['train_ccost']);

					$train_i = $sq_quotation['total_infant'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['train_icost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));

					$train_i1 = (float)($sq_quotation['train_icost']);

					$cruise_a = $sq_quotation['total_adult'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['cruise_acost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));
					$cruise_a1 = (float)($sq_quotation['cruise_acost']);

					$cruise_cwb = ($sq_quotation['children_with_bed'] != 0  || $sq_quotation['children_without_bed'] != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['cruise_ccost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));

					$cruise_cwb1 = (float)($sq_quotation['cruise_ccost']);

					$cruise_i = $sq_quotation['total_infant'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['cruise_icost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0));

					$cruise_i1 = (float)($sq_quotation['cruise_icost']);
					// // *Train Cost:* 
					// *Cruise Cost:*



					$total_pkg_cost = (float)($adult_cost1) * (float)($adults) +
						(float)($child_with1) * (float)($child_wb) + (float)($child_without1) * (float)($child_wob) +
						(float)($infants) * (float)($infant_cost1) +
						(float)($tax1) +
						(float)($tcsvalue) +
						(float)($visa1) +
						(float)($guide1) +
						(float)($misc1) +
						(float)($flight_a1) * (float)($adults) +
						(float)($child_wb) * (float)($flight_cwb1) + (float)($child_wob) * (float)($flight_cwb1) +
						(float)($infants) * (float)($flight_i1) +
						(float)($train_a1) * (float)($adults) +
						(float)($child_wb) * (float)($train_cwb1) +  (float)($child_wob) * (float)($train_cwb1) +
						(float)($infants) * (float)($train_i1) +
						(float)($cruise_a1) * (float)($adults) +
						(float)($child_wb) * (float)($cruise_cwb1) + (float)($child_wob) * (float)($cruise_cwb1) +
						(float)($infants) * (float)($cruise_i1);


					$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $total_pkg_cost);




					$maindetail = "*Quotation ID :* $quoatationid 

 *$tourname*
 * $from_date for $duration
 * $adults Adults
 * $childs Child
 * $infants Infant

*Land Cost:*

*Adult Amount:* $adult_cost.\n
*Child with Bed Amount:* $child_with.\n
*Child without Bed Amount:* $child_without.\n
*Infant Amount:* $infant_cost.\n

*Taxes:*

*Tax:* $tax.\n
*TCS:*$tcs_cost\n";

					$maindetail .= "\n";

					// Other Costs section
					if (
						extract_numeric($visa) > 0 ||
						extract_numeric($guide) > 0 ||
						extract_numeric($misc) > 0
					) {
						$maindetail .= "*Other Cost:*\n\n";
						if (extract_numeric($visa) > 0) {
							$maindetail .= "*Visa Amount:* " . $visa . "\n\n";
						}
						if (extract_numeric($guide) > 0) {
							$maindetail .= "*Guide Amount:* " . $guide . "\n\n";
						}
						if (extract_numeric($misc) > 0) {
							$maindetail .= "*Miscellaneous Amount:* " . $misc . "\n";
						}
						$maindetail .= "\n"; // Add space after Other Costs
					}

					// Travel Costs section
					if (
						extract_numeric($flight_a) > 0 ||
						extract_numeric($flight_cwb) > 0 ||
						extract_numeric($flight_i) > 0 ||
						extract_numeric($train_a) > 0 ||
						extract_numeric($train_cwb) > 0 ||
						extract_numeric($train_i) > 0 ||
						extract_numeric($cruise_a) > 0 ||
						extract_numeric($cruise_cwb) > 0 ||
						extract_numeric($cruise_i) > 0
					) {
						$maindetail .= "*Travel Cost:*\n\n";

						// Flight Section
						if (extract_numeric($flight_a) > 0) {
							$maindetail .= "*Flight Adult Amount:* " . $flight_a . "\n\n";
						}
						if (extract_numeric($flight_cwb) > 0) {
							$maindetail .= "*Flight Child Amount:* " . $flight_cwb . "\n\n";
						}
						if (extract_numeric($flight_i) > 0) {
							$maindetail .= "*Flight Infant Amount:* " . $flight_i . "\n\n";
						}
						$maindetail .= "";

						// Train Section
						if (extract_numeric($train_a) > 0) {
							$maindetail .= "*Train Adult Amount:* " . $train_a . "\n\n";
						}
						if (extract_numeric($train_cwb) > 0) {
							$maindetail .= "*Train Child Amount:* " . $train_cwb . "\n\n";
						}
						if (extract_numeric($train_i) > 0) {
							$maindetail .= "*Train Infant Amount:* " . $train_i . "\n\n";
						}
						$maindetail .= "";

						// Cruise Section
						if (extract_numeric($cruise_a) > 0) {
							$maindetail .= "*Cruise Adult Amount:* " . $cruise_a . "\n\n";
						}
						if (extract_numeric($cruise_cwb) > 0) {
							$maindetail .= "*Cruise Child Amount:* " . $cruise_cwb . "\n\n";
						}
						if (extract_numeric($cruise_i) > 0) {
							$maindetail .= "*Cruise Infant Amount:* " . $cruise_i . "\n\n";
						}
						$maindetail .= ""; // Final space
					}




					// $maindetail .= '';


					// // Check if each amount is non-zero before adding text
					// if (extract_numeric($visa) > 0) {
					//     $maindetail .= "*Visa Amount*: " . $visa . "\n";
					// }
					// if (extract_numeric($guide) > 0) {
					//     $maindetail .= "*Guide Amount:* " .$guide. "\n";
					// }
					// if (extract_numeric($misc) > 0) {
					//     $maindetail .= "*Miscellaneous Amount:* " .$misc. "\n";
					// }
					// if (extract_numeric($flight_a) >  0) {
					//     $maindetail .= "*Flight Adult Amount:* " .$flight_a . "\n";
					// }
					// if (extract_numeric($flight_cwb) > 0) {
					//     $maindetail .= "*Flight Child Amount:* " .$flight_cwb . "\n";
					// }
					// if (extract_numeric($flight_i) > 0) {
					//     $maindetail .= "*Flight  Infant Amount:* " .$flight_i . "\n";
					// }


					// if (extract_numeric($train_a ) > 0) {
					//     $maindetail .= "*Train Adult Amount:* " .$train_a . "\n";
					// }
					// if (extract_numeric($train_cwb) > 0) {
					//     $maindetail .= "*Train Child Amount:* " .$train_cwb . "\n";
					// }
					// if (extract_numeric($train_i) > 0) {
					//     $maindetail .= "*Train  Infant Amount:* " .$train_i . "\n";
					// }

					// if (extract_numeric($cruise_a) > 0) {
					//     $maindetail .= "*Cruise Adult Amount:* " .$cruise_a . "\n";
					// }
					// if (extract_numeric($cruise_cwb) > 0) {
					//     $maindetail .= "*Cruise Child Amount:* " .$cruise_cwb . "\n";
					// }
					// if (extract_numeric($cruise_i) > 0) {
					//     $maindetail .= "*Cruise  Infant Amount:* " .$cruise_i . "\n";
					// }






					$maindetail .= "*Total Price :*  $currency_amount1 " . "\n";



					// $currency_amount1 =$newBasic;
				}
			} else {


				$sq_costing1 = mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id' order by sort_order limit 1");
				$tour_cost = 0;
				while ($sq_costing = mysqli_fetch_assoc($sq_costing1)) {
					$basic_cost = $sq_costing['basic_amount'];
					$service_charge = $sq_costing['service_charge'];
					$service_tax_amount = 0;
					$tax_show = '';
					$bsmValues = json_decode($sq_costing['bsmValues']);
					$discount_in = $sq_costing['discount_in'];
					$discount = $sq_costing['discount'];
					if ($discount_in == 'Percentage') {
						$act_discount = (float)($service_charge) * (float)($discount) / 100;
					} else {
						$act_discount = ($service_charge != 0) ? $discount : 0;
					}
					$service_charge = $service_charge - (float)($act_discount);
					$tour_cost = $basic_cost + $service_charge;
					$name = '';
					if ($sq_costing['service_tax_subtotal'] !== 0.00 && ($sq_costing['service_tax_subtotal']) !== '') {
						$service_tax_subtotal1 = explode(',', $sq_costing['service_tax_subtotal']);
						for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
							$service_tax = explode(':', $service_tax_subtotal1[$i]);
							$service_tax_amount = (float)($service_tax_amount) + (float)($service_tax[2]);
							$name .= $service_tax[0] . $service_tax[1] . ', ';
						}
					}

					$bsmValues = json_decode($sq_costing['bsmValues'], true);
					if (isset($bsmValues[0]['tcsper']) && $bsmValues[0]['tcsper'] != 'NaN') {
						$tcsper = $bsmValues[0]['tcsper'];
						$tcsvalue = $bsmValues[0]['tcsvalue'];
					} else {
						$tcsper = 0;
						$tcsvalue = 0;
					}
					$tcs_amount_show  = currency_conversion($currency, $sq_quotation['currency_code'], $tcsvalue);
					$service_tax_amount_show = currency_conversion($currency, $sq_quotation['currency_code'], $service_tax_amount);
					$quotation_cost = $basic_cost + $service_charge + $service_tax_amount + $sq_quotation['train_cost'] + $sq_quotation['cruise_cost'] + $sq_quotation['flight_cost'] + $sq_quotation['visa_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'] + $tcsvalue;
					// $quotation_cost = ceil($quotation_cost);
					////////////////Currency conversion ////////////
					$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);
					$act_tour_cost = (float)($quotation_cost) - (float)($service_charge) + (float)($sq_costing['service_charge']);
					$act_tour_cost = ceil($act_tour_cost);
					$act_tour_cost_camount = ($discount != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], $act_tour_cost) : '';

					$newBasic = currency_conversion($currency, $sq_quotation['currency_code'], $tour_cost);
					$travel_cost = (float)($sq_quotation['train_cost']) + (float)($sq_quotation['flight_cost']) + (float)($sq_quotation['cruise_cost']) + (float)($sq_quotation['visa_cost']) + (float)($sq_quotation['guide_cost']) + (float)($sq_quotation['misc_cost']);
					$travel_cost = currency_conversion($currency, $sq_quotation['currency_code'], $travel_cost);






					$maindetail = "*Quotation ID :* $quoatationid 

*$tourname*
* $from_date for $duration
* $adults Adults
* $childs Child
* $infants Infant
				   
*Tour Amount :* $newBasic
*Travel Amount :* $travel_cost
*Tax :* $service_tax_amount_show
*Tcs :* $tcs_amount_show
*Total Price :*  $currency_amount1 " . "\n";
				}
			}




			/*end tour costing */



			/*start hotel detail */
			$sq_package_type = mysqlQuery("select DISTINCT(package_type) from package_tour_quotation_hotel_entries where quotation_id='$quotation_id'");
			while ($row_hotel1 = mysqli_fetch_assoc($sq_package_type)) {
				$sq_package_type1 = mysqlQuery("
    SELECT 
        h.hotel_name as hotelname, 
        c.city_name as cityname, 
        p.*
    FROM 
        package_tour_quotation_hotel_entries p
    JOIN 
        hotel_master h ON h.hotel_id = p.hotel_name
    JOIN 
        city_master c ON c.city_id = p.city_name
    WHERE 
        p.quotation_id = '$quotation_id' 
        AND p.package_type = '$row_hotel1[package_type]' 
    ORDER BY 
        p.package_type
");
				if (mysqli_num_rows($sq_package_type1) > 0) {
					$hoteldata = "\n" . '🏨  *Hotels*
-----------' . "\n";
					while ($row_hotel = mysqli_fetch_assoc($sq_package_type1)) {

						// Concatenate the values for the current row
						$hoteldata .= '*' . htmlspecialchars($row_hotel['cityname']) . '*  -' .
							'*' . htmlspecialchars($row_hotel['hotelname']) . '* - ' .
							'*' . htmlspecialchars($row_hotel['room_category']) . '*  -' .
							'*' . htmlspecialchars($row_hotel['meal_plan']) . "*\n"; // Add a newline for separation

					}
				}
			}
			/*end hotel detail */


			/*start itninary detail */
			$sq_package_program = mysqlQuery("select * from  package_quotation_program where quotation_id='$quotation_id'");
			$count = 1;
			$j = 0;
			$dates = (array) get_dates_for_package_itineary($quotation_id);
			if (mysqli_num_rows($sq_package_program) > 0) {
				$itninarydata = "\n" . ' 📅 *Itinerary*
-----------' . "\n";
				while ($row_itinerary = mysqli_fetch_assoc($sq_package_program)) {

					$date_format = isset($dates[$j]) ? $dates[$j] : 'NA';

					// Concatenate the values for the current row
					// Concatenate the values for the current row
					$itninarydata .= '*Day - ' . $count . '*   ' .
						'*' . htmlspecialchars($row_itinerary['attraction']) . '*      ' .
						'*(' . htmlspecialchars($row_itinerary['stay']) . '*)     ' .
						'*(' . htmlspecialchars($row_itinerary['meal_plan']) . ")*\n"; // Add a newline for separation
					$in = '';
					$count++;
					$j++;
				}
			}

			/*end itninary detail */

			/*start Transportation detail */
			// Query to get transport entries
			$sq_hotel = mysqlQuery("SELECT * FROM package_tour_quotation_transport_entries2 WHERE quotation_id='$quotation_id'");
			if (mysqli_num_rows($sq_hotel) > 0) {
				$transportationdata = "\n" . '🚖  *Transportation*
-----------' . "\n";
				while ($row_hotel = mysqli_fetch_assoc($sq_hotel)) {
					// Get transport name
					$transport_name = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM b2b_transfer_master WHERE entry_id='$row_hotel[vehicle_name]'"));
					$transportationdata .=    '*' . htmlspecialchars($transport_name['vehicle_name']) . '* ' .
						'*' . get_date_user($row_hotel['start_date']) . '*    ' .
						'*' . get_date_user($row_hotel['end_date']) . '*    ' .
						'*' . '(' . htmlspecialchars($row_hotel['vehicle_count']) . ')' . "*\n";
				}
			}

			/*end Transportation detail */


			/*start Activity detail */
			// Query to get excursion entries along with city and excursion details using JOINs
			$sq_ex = mysqlQuery("
    SELECT 
        ee.*, 
        cm.city_name, 
        em.excursion_name 
    FROM 
        package_tour_quotation_excursion_entries ee
    LEFT JOIN 
        city_master cm ON cm.city_id = ee.city_name
    LEFT JOIN 
        excursion_master_tariff em ON em.entry_id = ee.excursion_name 
    WHERE 
        ee.quotation_id = '$quotation_id'
");

			$count = 0;
			if (mysqli_num_rows($sq_ex) > 0) {
				$actvitydata = "\n" . '🚖 *Activity*
-----------' . "\n";
				while ($row_ex = mysqli_fetch_assoc($sq_ex)) {
					$count++;
					// You can now access $row_ex['city_name'] and $row_ex['excursion_name'] directly
					$cityName = $row_ex['city_name'];
					$excursionName = $row_ex['excursion_name'];

					$actvitydata .=    '*' . htmlspecialchars($excursionName) . get_datetime_user($row_ex['exc_date']) . "*\n";
					// Additional processing can be done here
				}
			}


			/*end Activity detail */

			/*start Train detail */

			$sq_train = mysqlQuery("select * from package_tour_quotation_train_entries where quotation_id='$quotation_id'");
			if (mysqli_num_rows($sq_train) > 0) {


				$traindata = "\n" . '🚆 *Train*
-----------' . "\n";
				while ($row_train = mysqli_fetch_assoc($sq_train)) {

					$traindata .=    '*' . date('d-m-Y H:i', strtotime($row_train['departure_date'])) . '* ' .
						'*' . htmlspecialchars($row_train['from_location']) . '*  ' .
						'*' . htmlspecialchars($row_train['to_location']) . "*\n";
				}
			}
			/*end Train detail */

			/*start Cruies detail */

			$sq_cruise = mysqlQuery("select * from package_tour_quotation_cruise_entries where quotation_id='$quotation_id'");

			if (mysqli_num_rows($sq_cruise) > 0) {


				$cruisedata = "\n" . '🚢  *Cruise*
-----------' . "\n";
				while ($row_cruise = mysqli_fetch_assoc($sq_cruise)) {

					$cruisedata .=    '*' . get_datetime_user($row_cruise['dept_datetime']) . '*  ' .
						'*' . htmlspecialchars($row_cruise['route']) . "*\n";
				}
			}
			/*end Cruies detail */

			/*start Plane detail */

			$sq_plane = mysqlQuery("
    SELECT 
        pe.*, 
        am.airline_name, 
        am.airline_code 
    FROM 
        package_tour_quotation_plane_entries pe
    LEFT JOIN 
        airline_master am ON am.airline_id = pe.airline_name 
    WHERE 
        pe.quotation_id = '$quotation_id'
");

			if (mysqli_num_rows($sq_plane) > 0) {


				$planedata = "\n" . '✈️ *Flight*
-----------' . "\n";
				while ($row_plane = mysqli_fetch_assoc($sq_plane)) {
					$airline = !empty($row_plane['airline_name']) ?
						$row_plane['airline_name'] . ' (' . $row_plane['airline_code'] . ')' :
						'NA';
					$planedata .=    '*' . $row_plane['from_location'] . '*  ' .
						'*' . $row_plane['to_location'] . '*  ' .
						'*' . htmlspecialchars($airline) . "*\n";
				}
			}
			/*end Plane detail */

			$quotation_no = base64_encode($quotation_id);


			$whatsapp_msg = rawurlencode('Hi Guest,

Greetings from ' . $app_name . '

Thank you for your query with us. As per your requirements, following are the package details.
' . $maindetail . $hoteldata . $itninarydata . $transportationdata . $actvitydata . $traindata . $cruisedata . $planedata . '
*Link* : ' . BASE_URL . 'model/package_tour/quotation/single_quotation.php?quotation=' . $quotation_no . '

Please contact for more details : ' . $app_name . ' ' . $contact . '
Thank you.');
			$all_message .= $whatsapp_msg;
		}


		$link = 'https://web.whatsapp.com/send?phone=' . $sq_quotation['mobile_no'] . '&text=' . $all_message;
		echo $link;
	}
}
