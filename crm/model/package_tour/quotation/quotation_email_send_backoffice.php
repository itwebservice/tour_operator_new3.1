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
		$date = $sq_quotation['created_at'];
		$yr = explode("-", $date);
		$year = $yr[0];

		$sq_login = mysqli_fetch_assoc(mysqlQuery("select * from roles where id='$sq_quotation[login_id]'"));
		$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_login[emp_id]'"));

		if ($sq_emp_info['first_name'] == '') {
			$emp_name = 'Admin';
		} else {
			$emp_name = $sq_emp_info['first_name'] . ' ' . $sq_emp_info['last_name'];
		}

		$sq_package_program = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id ='$sq_quotation[package_id]'"));

		// Load structured costing (same source as Option-1 PDF / email)
		include_once dirname(__FILE__) . '/../../app_settings/print_html/quotation_html/generic_quotation_data.php';
		$q_data = function_exists('get_generic_quotation_data') ? get_generic_quotation_data($quotation_id) : array();
		$cost = (is_array($q_data) && isset($q_data['costing'])) ? $q_data['costing'] : array();
		$is_pp = (isset($sq_quotation['costing_type']) && (string) $sq_quotation['costing_type'] !== '1');

		$currency_amount1 = '';
		if (isset($cost['computed']['grand_total_display']) && $cost['computed']['grand_total_display'] !== '') {
			$currency_amount1 = $cost['computed']['grand_total_display'];
		} else {
			// Fallback total from costing entries (legacy)
			$sq_cost = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id = '$quotation_id' order by sort_order limit 1"));
			if (!is_array($sq_cost)) {
				$sq_cost = array();
			}
			$basic_cost = (float) (isset($sq_cost['basic_amount']) ? $sq_cost['basic_amount'] : 0);
			$service_charge = (float) (isset($sq_cost['service_charge']) ? $sq_cost['service_charge'] : 0);
			$service_tax_amount = 0.0;
			$tcsvalue = 0.0;
			$discount_in = isset($sq_cost['discount_in']) ? $sq_cost['discount_in'] : '';
			$discount = (float) (isset($sq_cost['discount']) ? $sq_cost['discount'] : 0);
			if ($discount_in == 'Percentage') {
				$act_discount = $service_charge * $discount / 100;
			} else {
				$act_discount = ($service_charge != 0) ? $discount : 0.0;
			}
			$service_charge = $service_charge - $act_discount;
			$tax_sub = isset($sq_cost['service_tax_subtotal']) ? $sq_cost['service_tax_subtotal'] : '';
			if ($tax_sub !== '' && $tax_sub !== null && $tax_sub !== 0.00) {
				foreach (explode(',', (string) $tax_sub) as $chunk) {
					$parts = explode(':', $chunk);
					$service_tax_amount += (float) (isset($parts[2]) ? $parts[2] : 0);
				}
			}
			$bsmValues = json_decode(isset($sq_cost['bsmValues']) ? $sq_cost['bsmValues'] : '', true);
			if (is_array($bsmValues) && isset($bsmValues[0]['tcsvalue']) && $bsmValues[0]['tcsvalue'] !== '' && $bsmValues[0]['tcsvalue'] !== 'NaN') {
				$tcsvalue = (float) $bsmValues[0]['tcsvalue'];
			}
			$quotation_cost = $basic_cost + $service_charge + $service_tax_amount
				+ (float) $sq_quotation['train_cost'] + (float) $sq_quotation['cruise_cost'] + (float) $sq_quotation['flight_cost']
				+ (float) $sq_quotation['visa_cost'] + (float) $sq_quotation['guide_cost'] + (float) $sq_quotation['misc_cost']
				+ $tcsvalue;
			$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);
		}

		// Build Per Person / Group costing HTML for email body
		$costing_html = '';
		if ($is_pp) {
			include_once dirname(__FILE__) . '/../../app_settings/print_html/quotation_html/generic_quotation_data.php';
			$pp_entries = (isset($cost['computed']['pp_entries']) && is_array($cost['computed']['pp_entries']))
				? $cost['computed']['pp_entries'] : array();
			$pp_table = '';
			if (!empty($pp_entries) && function_exists('gqd_render_pp_entries_table')) {
				ob_start();
				gqd_render_pp_entries_table($pp_entries, array(
					'table_class' => '',
					'table_style' => 'width:100%;border-collapse:collapse;font-size:13px;color:#444;',
					'force_all_pax_cols' => true,
					'simple' => true,
					'symbol' => '&#8377;',
				));
				$pp_table = ob_get_clean();
			}
			if ($pp_table !== '') {
				$costing_html = '
		<tr>
			<table width="85%" cellspacing="0" cellpadding="5" style="color:#888888;border:1px solid #888888;margin:0px auto;margin-top:20px;min-width:100%;" role="presentation">
				<tr><td style="text-align:center;border:1px solid #888888;font-weight:600;padding:8px;">COSTING DETAILS</td></tr>
				<tr><td style="padding:8px;border:1px solid #888888;">' . $pp_table . '</td></tr>
			</table>
		</tr>';
			}
		} else {
			$group_rows = (isset($cost['computed']['group']) && is_array($cost['computed']['group'])) ? $cost['computed']['group'] : array();
			if (!empty($group_rows)) {
				$costing_html = '
		<tr>
			<table width="85%" cellspacing="0" cellpadding="5" style="color:#888888;border:1px solid #888888;margin:0px auto;margin-top:20px;min-width:100%;" role="presentation">
				<tr><td colspan="6" style="text-align:center;border:1px solid #888888;font-weight:600;">COSTING DETAILS</td></tr>
				<tr>
					<td style="border:1px solid #888888;font-weight:600;">Package</td>
					<td style="border:1px solid #888888;font-weight:600;">Tour Cost</td>
					<td style="border:1px solid #888888;font-weight:600;">Tax</td>
					<td style="border:1px solid #888888;font-weight:600;">TCS</td>
					<td style="border:1px solid #888888;font-weight:600;">Travel</td>
					<td style="border:1px solid #888888;font-weight:600;">Total</td>
				</tr>';
				foreach ($group_rows as $row) {
					$total_cell = htmlspecialchars((string) (isset($row['total_display']) ? $row['total_display'] : ''), ENT_QUOTES, 'UTF-8');
					if (!empty($row['before_discount_display'])) {
						$total_cell .= ' <s>' . htmlspecialchars((string) $row['before_discount_display'], ENT_QUOTES, 'UTF-8') . '</s>';
					}
					$costing_html .= '
				<tr>
					<td style="border:1px solid #888888;">' . htmlspecialchars((string) (isset($row['package_type']) ? $row['package_type'] : ''), ENT_QUOTES, 'UTF-8') . '</td>
					<td style="border:1px solid #888888;">' . htmlspecialchars((string) (isset($row['tour_cost_display']) ? $row['tour_cost_display'] : ''), ENT_QUOTES, 'UTF-8') . '</td>
					<td style="border:1px solid #888888;">' . htmlspecialchars((string) (isset($row['tax_display']) ? $row['tax_display'] : ''), ENT_QUOTES, 'UTF-8') . '</td>
					<td style="border:1px solid #888888;">' . htmlspecialchars((string) (isset($row['tcs_display']) ? $row['tcs_display'] : ''), ENT_QUOTES, 'UTF-8') . '</td>
					<td style="border:1px solid #888888;">' . htmlspecialchars((string) (isset($row['travel_display']) ? $row['travel_display'] : ''), ENT_QUOTES, 'UTF-8') . '</td>
					<td style="border:1px solid #888888;">' . $total_cell . '</td>
				</tr>';
				}
				$costing_html .= '
			</table>
		</tr>';
			}
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
		</tr>'
			. $costing_html
			. '
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
