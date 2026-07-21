<?php
class quotation_email_send_backoffice
{


	public function quotation_email_backoffice()
	{
		global $app_cancel_pdf, $theme_color, $currency, $model;
		$quotation_id = $_POST['quotation_id'];
		$email_id = $_POST['email_id'];

		$quotation_no = base64_encode($quotation_id);


		$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id'"));

		$adults = $sq_quotation['total_adult'];
		$childs = $sq_quotation['children_with_bed'] + $sq_quotation['children_without_bed'];
		$infants = $sq_quotation['total_infant'];
		$child_wb = $sq_quotation['children_with_bed'];
		$child_wob = $sq_quotation['children_without_bed'];
		/* tour costing  start*/

		//    $quotation_no = base64_encode($quotation_id_arr[$i]);
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
				// $quotation_cost = ceil($quotation_cost);
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



			$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id'"));
			$date = $sq_quotation['created_at'];
			$yr = explode("-", $date);
			$year = $yr[0];
			$sq_cost =  mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id = '$quotation_id'"));

			$service_tax_amount = 0;
			$basic_cost = $sq_cost['basic_amount'];
			$service_charge = $sq_cost['service_charge'];
			$discount_in = $sq_cost['discount_in'];
			$discount = $sq_cost['discount'];

			$bsmValues = json_decode($sq_cost['bsmValues']);
			if ($bsmValues[0]->tcsvalue != '') {
				$tcsvalue = $bsmValues[0]->tcsvalue;
			} else {
				$tcsvalue = '0';
			}

			if ($discount_in == 'Percentage') {
				$act_discount = (float)($service_charge) * (float)($discount) / 100;
			} else {
				$act_discount = ($service_charge != 0) ? $discount : 0;
			}
			$service_charge = $service_charge - (float)($act_discount);
			$name = '';
			if ($sq_cost['service_tax_subtotal'] !== 0.00 && ($sq_cost['service_tax_subtotal']) !== '') {
				$service_tax_subtotal1 = explode(',', $sq_cost['service_tax_subtotal']);
				for ($i1 = 0; $i1 < sizeof($service_tax_subtotal1); $i1++) {
					$service_tax = explode(':', $service_tax_subtotal1[$i1]);
					$service_tax_amount = (float)($service_tax_amount) + (float)($service_tax[2]);
					$name .= $service_tax[0] . $service_tax[1] . ', ';
				}
			}
			$quotation_cost = $basic_cost + $service_charge + $service_tax_amount + $sq_quotation['train_cost'] + $sq_quotation['cruise_cost'] + $sq_quotation['flight_cost'] + $sq_quotation['visa_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'] +  $tcsvalue;
			// $quotation_cost = ceil($quotation_cost);
			////////////////Currency conversion ////////////
			$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);
		}

		$sq_login = mysqli_fetch_assoc(mysqlQuery("select * from roles where id='$sq_quotation[login_id]'"));
		$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_login[emp_id]'"));

		if ($sq_emp_info['first_name'] == '') {
			$emp_name = 'Admin';
		} else {
			$emp_name = $sq_emp_info['first_name'] . ' ' . $sq_emp_info['last_name'];
		}

		$sq_package_program = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id ='$sq_quotation[package_id]'"));

		if ($app_cancel_pdf == '') {
			$url =  BASE_URL . 'view/package_booking/quotation/cancellaion_policy_msg.php';
		} else {
			$url = explode('uploads', $app_cancel_pdf);
			$url = BASE_URL . 'uploads' . $url[1];
		}

		$content = '
		<tr>
			<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
				<tr><td style="text-align:left;border: 1px solid #888888;">Name</td>   <td style="text-align:left;border: 1px solid #888888;">' . $sq_quotation['customer_name'] . '</td></tr>
				<tr><td style="text-align:left;border: 1px solid #888888;">Package Name</td>   <td style="text-align:left;border: 1px solid #888888;" >' . $sq_package_program['package_name'] . '(Package Tour)' . '</td></tr>
				<tr><td style="text-align:left;border: 1px solid #888888;">Tour Date</td>   <td style="text-align:left;border: 1px solid #888888;">' . date('d-m-Y', strtotime($sq_quotation['from_date'])) . ' to ' . date('d-m-Y', strtotime($sq_quotation['to_date'])) . '</td></tr>
				<tr><td style="text-align:left;border: 1px solid #888888;">Quotation Cost</td>   <td style="text-align:left;border: 1px solid #888888;">' . $currency_amount1 . '</td></tr>
				<tr><td style="text-align:left;border: 1px solid #888888;">Created By</td>   <td style="text-align:left;border: 1px solid #888888;">' . $emp_name . '</td></tr>
			</table>
		</tr>
		<tr>
			<td>
				<a style="font-weight:500;font-size:12px;display:block;color:#ffffff;background:' . $theme_color . ';text-decoration:none;padding:5px 10px;border-radius:25px;width: 90px;text-align: center;margin:0px auto;margin-top:10px;" href="' . BASE_URL . 'model/package_tour/quotation/quotation_email_template.php?quotation_id=' . $quotation_no . '" >Booking Details</a>
			</td> 
			
		</tr>';
		$subject = 'Confirmed Quotation Details : ( Quotation ID : ' . get_quotation_id($quotation_id, $year) . ', Name : ' . $sq_quotation['customer_name'] . ' )';
		$model->app_email_send('7', 'Team', $email_id, $content, $subject, '1');
		echo "Quotation sent successfully!";
		exit;
	}
}
