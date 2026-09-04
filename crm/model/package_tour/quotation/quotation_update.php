<?php
class quotation_update{

public function quotation_master_update()
{
	ensure_quotation_refer_id_column();
	ensure_excursion_vehicle_id_column();
	$quotation_id = $_POST['quotation_id'];
	error_log("QUOTATION UPDATE: Starting update for quotation_id = " . $quotation_id);
	
	// Check if this is just saving itinerary data
	if (isset($_POST['action']) && $_POST['action'] === 'save_itinerary_only') {
		$this->save_itinerary_only($quotation_id);
		return;
	}
	$enquiry_id = $_POST['enquiry_id'];
	$package_id = $_POST['package_id'];
	$is_ai_quotation = isset($_POST['is_ai_quotation']) ? $_POST['is_ai_quotation'] : '0';
	$dest_id = isset($_POST['dest_id']) ? intval($_POST['dest_id']) : 0;
	$quotation_refer_id = 0;

	// Always preserve the saved quotation_refer_id on update (never reassign from destination packages)
	$existing_quot = mysqli_fetch_assoc(mysqlQuery("select quotation_refer_id, package_id from package_tour_quotation_master where quotation_id='$quotation_id'"));
	$existing_refer_id = isset($existing_quot['quotation_refer_id']) ? intval($existing_quot['quotation_refer_id']) : 0;

	if ($is_ai_quotation == '1') {
		$package_id = 0;
		$quotation_refer_id = $existing_refer_id;
	} else {
		$quotation_refer_id = 0;
	}
	$tour_name = $_POST['tour_name'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $total_days = $_POST['total_days'];
    $customer_name = $_POST['customer_name'];
	$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
    $email_id = $_POST['email_id'];
    $mobile_no = $_POST['mobile_no'];
	$country_code = $_POST['country_code'];
    $total_adult = $_POST['total_adult'];
    $total_infant = $_POST['total_infant'];
    $total_passangers = $_POST['total_passangers'];
    $children_without_bed = $_POST['children_without_bed'];
    $children_with_bed = $_POST['children_with_bed'];
	$quotation_date = $_POST['quotation_date'];
	$active_flag = $_POST['active_flag'];
	$booking_type = $_POST['booking_type'];
	$train_cost = $_POST['train_cost'];
	$flight_cost = $_POST['flight_cost'];
	$cruise_cost = $_POST['cruise_cost'];
	$visa_cost = $_POST['visa_cost'];
	$guide_cost = $_POST['guide_cost'];
	$misc_cost = $_POST['misc_cost'];

	$adult_cost = isset($_POST['adult_cost']) && is_array($_POST['adult_cost']) ? $_POST['adult_cost'] : [];
	$infant_cost = isset($_POST['infant_cost']) && is_array($_POST['infant_cost']) ? $_POST['infant_cost'] : [];
	$child_with = isset($_POST['child_with']) && is_array($_POST['child_with']) ? $_POST['child_with'] : [];
	$child_without = isset($_POST['child_without']) && is_array($_POST['child_without']) ? $_POST['child_without'] : [];
	$entry_id_arr = isset($_POST['entry_id_arr']) && is_array($_POST['entry_id_arr']) ? $_POST['entry_id_arr'] : [];
	$price_str_url = $_POST['price_str_url'];
	$currency_code = $_POST['currency_code'];

	//Train
	$train_from_location_arr = isset($_POST['train_from_location_arr']) ? $_POST['train_from_location_arr'] : [];
	$train_to_location_arr = isset($_POST['train_to_location_arr']) ? $_POST['train_to_location_arr'] : [];
	$train_class_arr = isset($_POST['train_class_arr']) ? $_POST['train_class_arr'] : [];
	$train_arrival_date_arr = isset($_POST['train_arrival_date_arr']) ? $_POST['train_arrival_date_arr'] : [];
	$train_departure_date_arr = isset($_POST['train_departure_date_arr']) ? $_POST['train_departure_date_arr'] : [];
	$train_id_arr = $_POST['train_id_arr'];
	$train_status_arr = $_POST['train_status_arr'];

	//Plane
	$plane_from_city_arr = isset($_POST['plane_from_city_arr']) ? $_POST['plane_from_city_arr'] : [];
	$plane_to_city_arr = isset($_POST['plane_to_city_arr']) ? $_POST['plane_to_city_arr'] : [];
	$plane_from_location_arr = isset($_POST['plane_from_location_arr']) ? $_POST['plane_from_location_arr'] : [];
	$plane_to_location_arr = isset($_POST['plane_to_location_arr']) ? $_POST['plane_to_location_arr'] : [];
	$airline_name_arr = isset($_POST['airline_name_arr']) ? $_POST['airline_name_arr'] : [];
	$plane_class_arr = isset($_POST['plane_class_arr']) ? $_POST['plane_class_arr'] : [];
	$arraval_arr = isset($_POST['arraval_arr']) ? $_POST['arraval_arr'] : [];
	$dapart_arr = isset($_POST['dapart_arr']) ? $_POST['dapart_arr'] : [];
    $plane_id_arr = $_POST['plane_id_arr'];
	$plane_status_arr = $_POST['plane_status_arr'];

    //Cruise
	$cruise_departure_date_arr = isset($_POST['cruise_departure_date_arr']) ? $_POST['cruise_departure_date_arr'] : [];
	$cruise_arrival_date_arr = isset($_POST['cruise_arrival_date_arr']) ? $_POST['cruise_arrival_date_arr'] : [];
	$route_arr = isset($_POST['route_arr']) ? $_POST['route_arr'] : [];
	$cabin_arr = isset($_POST['cabin_arr']) ? $_POST['cabin_arr'] : [];
	$sharing_arr = isset($_POST['sharing_arr']) ? $_POST['sharing_arr'] : [];
    $c_entry_id_arr = $_POST['c_entry_id_arr'];
    $cruise_status_arr = $_POST['cruise_status_arr'];

    //Hotel
	$package_type_arr = isset($_POST['package_type_arr']) ? $_POST['package_type_arr'] : [];
	$city_name_arr = isset($_POST['city_name_arr']) ? $_POST['city_name_arr'] : [];
	$hotel_name_arr = isset($_POST['hotel_name_arr']) ? $_POST['hotel_name_arr'] : [];
	$hotel_cat_arr = isset($_POST['hotel_cat_arr']) ? $_POST['hotel_cat_arr'] : [];
	$hotel_stay_days_arr = isset($_POST['hotel_stay_days_arr']) ? $_POST['hotel_stay_days_arr'] : [];
    $hotel_id_arr = $_POST['hotel_id_arr'];
	$hotel_meal_plan_arr = isset($_POST['hotel_meal_plan_arr']) ? $_POST['hotel_meal_plan_arr'] : [];
	
	$hotel_type_arr = isset($_POST['hotel_type_arr']) ? $_POST['hotel_type_arr'] : [];
	$extra_bed_arr = isset($_POST['extra_bed_arr']) ? $_POST['extra_bed_arr'] : [];
	$total_rooms_arr = isset($_POST['total_rooms_arr']) ? $_POST['total_rooms_arr'] : [];
	$hotel_cost_arr = isset($_POST['hotel_cost_arr']) ? $_POST['hotel_cost_arr'] : [];
	$extra_bed_cost_arr = isset($_POST['extra_bed_cost_arr']) ? $_POST['extra_bed_cost_arr'] : [];
	$check_in_arr = isset($_POST['check_in_arr']) ? $_POST['check_in_arr'] : [];
	$check_out_arr = isset($_POST['check_out_arr']) ? $_POST['check_out_arr'] : [];
	$hotel_status_arr = isset($_POST['hotel_status_arr']) ? $_POST['hotel_status_arr'] : [];
	
    //Tranport
	
	$vehicle_name_arr = isset($_POST['vehicle_name_arr']) ? $_POST['vehicle_name_arr'] : [];
	$start_date_arr = isset($_POST['start_date_arr']) ? $_POST['start_date_arr'] : [];
	$end_date_arr = isset($_POST['end_date_arr']) ? $_POST['end_date_arr'] : [];
	$pickup_arr = isset($_POST['pickup_arr']) ? $_POST['pickup_arr'] : [];
	$drop_arr = isset($_POST['drop_arr']) ? $_POST['drop_arr'] : [];
	$vehicle_count_arr = isset($_POST['vehicle_count_arr']) ? $_POST['vehicle_count_arr'] : [];
	$transport_cost_arr1 = isset($_POST['transport_cost_arr1']) ? $_POST['transport_cost_arr1'] : [];
	$package_name_arr1 = isset($_POST['package_name_arr1']) ? $_POST['package_name_arr1'] : [];
	$service_duration_arr = isset($_POST['service_duration_arr']) ? $_POST['service_duration_arr'] : [];
	$pickup_type_arr = isset($_POST['pickup_type_arr']) ? $_POST['pickup_type_arr']: [];
	$drop_type_arr = isset($_POST['drop_type_arr']) ? $_POST['drop_type_arr']: [];
	$transport_status_arr = isset($_POST['transport_status_arr']) ? $_POST['transport_status_arr'] : [];
	$transport_id_arr = isset($_POST['transport_id_arr']) ? $_POST['transport_id_arr'] : [];

    //Excursion
	$city_name_arr_e = isset($_POST['city_name_arr_e']) ? (array)$_POST['city_name_arr_e'] : [];
	$excursion_name_arr = isset($_POST['excursion_name_arr']) ? (array)$_POST['excursion_name_arr'] : [];
	$excursion_amt_arr = isset($_POST['excursion_amt_arr']) ? (array)$_POST['excursion_amt_arr'] : [];
	$exc_date_arr_e = isset($_POST['exc_date_arr_e']) ? (array)$_POST['exc_date_arr_e'] : [];
	$transfer_option_arr = isset($_POST['transfer_option_arr']) ? (array)$_POST['transfer_option_arr'] : [];
	$adult_arr = isset($_POST['adult_arr']) ? (array)$_POST['adult_arr'] : [];
	$chwb_arr = isset($_POST['chwb_arr']) ? (array)$_POST['chwb_arr'] : [];
	$chwob_arr = isset($_POST['chwob_arr']) ? (array)$_POST['chwob_arr'] : [];
	$infant_arr = isset($_POST['infant_arr']) ? (array)$_POST['infant_arr'] : [];
	$vehicles_arr = isset($_POST['vehicles_arr']) ? (array)$_POST['vehicles_arr'] : [];
	$vehicle_id_arr_e = isset($_POST['vehicle_id_arr_e']) ? (array)$_POST['vehicle_id_arr_e'] : [];
    $excursion_id_arr = isset($_POST['excursion_id_arr']) ? (array)$_POST['excursion_id_arr'] : [];
	$exc_status_arr = isset($_POST['exc_status_arr']) ? (array)$_POST['exc_status_arr'] : [];
    
    //Costing
	$tour_cost_arr = isset($_POST['tour_cost_arr']) ? $_POST['tour_cost_arr']: [];
	$transport_cost_arr = isset($_POST['transport_cost_arr']) ? $_POST['transport_cost_arr'] : [];
	$excursion_cost_arr = isset($_POST['excursion_cost_arr']) ? $_POST['excursion_cost_arr'] : [];
	$basic_amount_arr = isset($_POST['basic_amount_arr']) ? $_POST['basic_amount_arr'] : [];
	$service_charge_arr = isset($_POST['service_charge_arr']) ? $_POST['service_charge_arr'] : [];
	$service_tax_subtotal_arr = isset($_POST['service_tax_subtotal_arr']) ? $_POST['service_tax_subtotal_arr'] : [];
	$total_tour_cost_arr = isset($_POST['total_tour_cost_arr']) ? $_POST['total_tour_cost_arr'] : [];
    $costing_id_arr = isset($_POST['costing_id_arr']) ? $_POST['costing_id_arr'] : [];

	$bsmValues = json_decode(json_encode($_POST['bsmValues']));
	$discount_in_arr = $_POST['discount_in_arr'];
	$discount_arr  =$_POST['discount_arr'];
    //Package Program
	$checked_programe_arr1 = $_POST['checked_programe_arr'];
	$day_count_arr = $_POST['day_count_arr'];
    $attraction_arr = $_POST['attraction_arr'];
    $program_arr = $_POST['program_arr'];
    $stay_arr = $_POST['stay_arr'];
    $meal_plan_arr = $_POST['meal_plan_arr'];
    $day_image_arr = isset($_POST['day_image_arr']) ? $_POST['day_image_arr'] : [];
    $package_p_id_arr = $_POST['package_p_id_arr'];

    $inclusions = isset($_POST['inclusions']) ? $_POST['inclusions'] : '';
	$exclusions = isset($_POST['exclusions']) ? $_POST['exclusions'] : '';
	// Multiple name="inclusions" fields on the form can arrive as arrays — keep last non-empty string
	if (is_array($inclusions)) {
		$picked = '';
		foreach ($inclusions as $chunk) {
			if (is_string($chunk) && trim(strip_tags($chunk)) !== '') {
				$picked = $chunk;
			}
		}
		$inclusions = $picked !== '' ? $picked : (string)end($inclusions);
	}
	if (is_array($exclusions)) {
		$picked = '';
		foreach ($exclusions as $chunk) {
			if (is_string($chunk) && trim(strip_tags($chunk)) !== '') {
				$picked = $chunk;
			}
		}
		$exclusions = $picked !== '' ? $picked : (string)end($exclusions);
	}
	$inclusions = (string)$inclusions;
	$exclusions = (string)$exclusions;
	$costing_type = isset($_POST['costing_type']) ? intval($_POST['costing_type']) : 1;
	if ($costing_type !== 1 && $costing_type !== 2) {
		$costing_type = 1;
	}
	$discount = $_POST['discount'];
	$flight_acost = $_POST['flight_acost'];
	$flight_ccost = $_POST['flight_ccost'];
	$flight_icost = $_POST['flight_icost'];
	$train_acost = $_POST['train_acost'];
	$train_ccost = $_POST['train_ccost'];
	$train_icost = $_POST['train_icost'];
	$cruise_acost = $_POST['cruise_acost'];
	$cruise_ccost = $_POST['cruise_ccost'];
	$cruise_icost = $_POST['cruise_icost'];
	$other_desc = addslashes($_POST['other_desc']);
	
	$updated_url = $_POST['updated_url'];
	$image_url_id = $_POST['image_url_id'];
	
	$quotation_date = get_date_db($quotation_date);
	$inclusions = addslashes($inclusions);
	$exclusions = addslashes($exclusions);
	
	$enquiry_content = '[{"name":"tour_name","value":"'.$tour_name.'"},{"name":"travel_from_date","value":"'.$from_date.'"},{"name":"travel_to_date","value":"'.$to_date.'"},{"name":"budget","value":"0"},{"name":"total_adult","value":"'.$total_adult.'"},{"name":"total_children","value":"'.'0'.'"},{"name":"total_infant","value":"'.$total_infant.'"},{"name":"total_members","value":"'.$total_passangers.'"},{"name":"hotel_type","value":""},{"name":"children_without_bed","value":"'.$children_without_bed.'"},{"name":"children_with_bed","value":"'.$children_with_bed.'"}]';		
	
	$from_date = get_date_db($from_date);
	$to_date = get_date_db($to_date);
	$whatsapp_no = $country_code.$mobile_no;

	$sq_quotation = mysqlQuery("update package_tour_quotation_master set tour_name = '$tour_name', from_date = '$from_date', to_date = '$to_date', total_days = '$total_days', customer_name = '$customer_name', email_id='$email_id',mobile_no='$whatsapp_no',whatsapp_no='$mobile_no',country_code='$country_code', total_adult = '$total_adult', total_infant = '$total_infant', total_passangers = '$total_passangers', children_without_bed = '$children_without_bed', children_with_bed = '$children_with_bed', quotation_date='$quotation_date', booking_type = '$booking_type', train_cost = '$train_cost', flight_cost = '$flight_cost',cruise_cost='$cruise_cost', visa_cost = '$visa_cost', guide_cost= '$guide_cost',misc_cost='$misc_cost', price_str_url= '$price_str_url', enquiry_id= '$enquiry_id', package_id='$package_id', quotation_refer_id='$quotation_refer_id', inclusions='$inclusions',exclusions='$exclusions',costing_type='$costing_type',currency_code='$currency_code',discount='$discount',status='$active_flag', train_acost='$train_acost',flight_acost='$flight_acost', cruise_acost='$cruise_acost', train_ccost='$train_ccost', flight_ccost='$flight_ccost', cruise_ccost='$cruise_ccost', train_icost='$train_icost', flight_icost='$flight_icost', cruise_icost='$cruise_icost',other_desc='$other_desc',user_id='$user_id',created_at=NOW() where quotation_id = '$quotation_id'");
	
	error_log("QUOTATION UPDATE: UPDATE query executed for quotation_id = " . $quotation_id . ", result = " . ($sq_quotation ? "success" : "failed"));
	
	$sq_info = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id = '$quotation_id'"));

	if($sq_quotation){
		////////////Enquiry Save///////////
		if($enquiry_id == 0){
			$sq_max_id = mysqli_fetch_assoc(mysqlQuery("select max(enquiry_id) as max from enquiry_master"));
			$enquiry_id1 = $sq_max_id['max']+1;	
			
			$sq_enquiry = mysqlQuery("insert into enquiry_master (enquiry_id, login_id,branch_admin_id,financial_year_id, enquiry_type,enquiry, name, mobile_no, landline_no, email_id,location, assigned_emp_id, enquiry_specification, enquiry_date, followup_date, reference_id, enquiry_content ) values ('$enquiry_id1', '$sq_info[login_id]', '$sq_info[branch_admin_id]','$sq_info[financial_year_id]', 'Package Booking','Strong', '$customer_name', '', '$mobile_no', '$email_id','', '$sq_info[emp_id]','', '$quotation_date', '$quotation_date', '', '$enquiry_content')");
			
			$sq_quot_update = mysqlQuery("update package_tour_quotation_master set enquiry_id='$enquiry_id1' where quotation_id='$quotation_id'");

			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from enquiry_master_entries"));
			$entry_id = $sq_max['max'] + 1;
			$sq_followup = mysqlQuery("insert into enquiry_master_entries(entry_id, enquiry_id, followup_reply,  followup_status,  followup_type, followup_date, followup_stage, created_at) values('$entry_id', '$enquiry_id1', '', 'Active','', '$quotation_date','Strong', '$quotation_date')");
			$sq_entryid = mysqlQuery("update enquiry_master set entry_id='$entry_id' where enquiry_id='$enquiry_id1'");
		}

		$sq_image = mysqlQuery("update package_tour_quotation_images set image_url = '$updated_url' where id='$image_url_id'");

		$this->train_entries_update($quotation_id, $train_from_location_arr, $train_to_location_arr, $train_class_arr, $train_arrival_date_arr, $train_departure_date_arr, $train_id_arr,$train_status_arr);
		$this->plane_entries_update($quotation_id,$plane_from_city_arr,$plane_to_city_arr, $plane_from_location_arr, $plane_to_location_arr, $plane_class_arr,$airline_name_arr, $arraval_arr, $dapart_arr, $plane_id_arr,$plane_status_arr);
		$this->cruise_entries_update($quotation_id, $cruise_departure_date_arr, $cruise_arrival_date_arr, $route_arr, $cabin_arr, $sharing_arr,$c_entry_id_arr,$cruise_status_arr);
		$this->hotel_entries_update($quotation_id, $city_name_arr, $hotel_name_arr,$hotel_cat_arr,$hotel_type_arr, $hotel_stay_days_arr,$hotel_id_arr,$hotel_cost_arr,$extra_bed_arr,$extra_bed_cost_arr, $total_rooms_arr, $package_id,$hotel_status_arr,$check_in_arr,$check_out_arr,$package_type_arr,$hotel_meal_plan_arr);
		$this->tranport_entries_update($quotation_id,$vehicle_name_arr,$start_date_arr,$pickup_arr,$drop_arr,$vehicle_count_arr,$transport_cost_arr1,$package_name_arr1,$pickup_type_arr,$drop_type_arr, $package_id,$transport_status_arr,$transport_id_arr,$end_date_arr,$service_duration_arr);	
		$this->excursion_entries_save($quotation_id,$city_name_arr_e, $excursion_name_arr, $excursion_amt_arr,$excursion_id_arr,$exc_status_arr,$exc_date_arr_e,$transfer_option_arr,$adult_arr,$chwb_arr,$chwob_arr,$infant_arr,$vehicles_arr,$vehicle_id_arr_e);
		$this->costing_entries_update($tour_cost_arr,$transport_cost_arr, $basic_amount_arr,$service_charge_arr,$service_tax_subtotal_arr,$total_tour_cost_arr, $costing_id_arr,$excursion_cost_arr,$adult_cost,$infant_cost,$child_with,$child_without,$bsmValues,$quotation_id,$entry_id_arr,$discount_in_arr,$discount_arr);
		$this->program_entries_save($quotation_id,$attraction_arr, $program_arr, $stay_arr,$meal_plan_arr,$day_image_arr,$package_p_id_arr,$checked_programe_arr1,$package_id,$day_count_arr, $quotation_refer_id);	
		
		// Copy image entries from original quotation to new quotation
		$this->copy_image_entries($quotation_id, $package_id);

		$pp_costing_arr = isset($_POST['pp_costing_arr']) ? json_decode($_POST['pp_costing_arr'], true) : [];
		if (!is_array($pp_costing_arr)) {
			$pp_costing_arr = [];
		}

		ensure_pp_costing_package_type_column();

		// Prefer structured pp_costing_arr; fall back to legacy flat POST fields.
		// Only rebuild PP costing for Per Person quotations, and never wipe existing
		// non-zero totals with an all-zero payload (common when hotel tariff refresh fails).
		if (intval($costing_type) === 2 && count($pp_costing_arr) > 0) {
			$is_multi = isset($pp_costing_arr[0]) && is_array($pp_costing_arr[0]) && isset($pp_costing_arr[0]['rows']);
			$packages = $is_multi ? $pp_costing_arr : array(array('package_type' => '', 'rows' => $pp_costing_arr));

			$posted_has_amount = false;
			$posted_has_rows = false;
			foreach ($packages as $pkg_check) {
				if (!is_array($pkg_check) || !isset($pkg_check['rows']) || !is_array($pkg_check['rows'])) {
					continue;
				}
				foreach ($pkg_check['rows'] as $row_check) {
					if (!is_array($row_check) || empty($row_check['type'])) {
						continue;
					}
					$posted_has_rows = true;
					$row_total = isset($row_check['total']) ? (float)$row_check['total'] : 0;
					$row_hotel = isset($row_check['hotel']) ? (float)$row_check['hotel'] : 0;
					$row_land = isset($row_check['land_cost']) ? (float)$row_check['land_cost'] : 0;
					$row_transfer = isset($row_check['transfer']) ? (float)$row_check['transfer'] : 0;
					$row_activity = isset($row_check['activity']) ? (float)$row_check['activity'] : 0;
					if ($row_total > 0 || $row_hotel > 0 || $row_land > 0 || $row_transfer > 0 || $row_activity > 0) {
						$posted_has_amount = true;
						break 2;
					}
				}
			}

			$existing_pp_total = 0;
			$sq_existing_pp = mysqli_fetch_assoc(mysqlQuery(
				"SELECT COALESCE(SUM(total_cost),0) AS total_sum FROM package_quotation_pp_costing WHERE quotation_id='$quotation_id'"
			));
			if ($sq_existing_pp) {
				$existing_pp_total = (float)$sq_existing_pp['total_sum'];
			}

			// Skip destructive rebuild when UI posted empty/zero amounts but DB already has values
			if ($posted_has_rows && ($posted_has_amount || $existing_pp_total <= 0)) {
				mysqlQuery("DELETE FROM package_quotation_pp_costing WHERE quotation_id='$quotation_id'");

				foreach ($packages as $pkg) {
					if (!is_array($pkg) || !isset($pkg['rows']) || !is_array($pkg['rows'])) {
						continue;
					}
					$package_type = isset($pkg['package_type']) ? mysqlREString($pkg['package_type']) : '';
					foreach ($pkg['rows'] as $row) {
						if (!is_array($row) || empty($row['type'])) {
							continue;
						}
						$type = mysqlREString($row['type']);
						$hotel = mysqlREString(isset($row['hotel']) ? $row['hotel'] : 0);
						$transfer = mysqlREString(isset($row['transfer']) ? $row['transfer'] : 0);
						$activity = mysqlREString(isset($row['activity']) ? $row['activity'] : 0);
						$land_cost = mysqlREString(isset($row['land_cost']) ? $row['land_cost'] : 0);
						$service_charge = mysqlREString(isset($row['service_charge']) ? $row['service_charge'] : 0);
						$discount_in = mysqlREString(isset($row['discount_in']) ? $row['discount_in'] : '');
						$discount_amount = mysqlREString(isset($row['discount_amount']) ? $row['discount_amount'] : 0);
						$flight = mysqlREString(isset($row['flight']) ? $row['flight'] : 0);
						$train = mysqlREString(isset($row['train']) ? $row['train'] : 0);
						$cruise = mysqlREString(isset($row['cruise']) ? $row['cruise'] : 0);
						$visa = mysqlREString(isset($row['visa']) ? $row['visa'] : 0);
						$guide = mysqlREString(isset($row['guide']) ? $row['guide'] : 0);
						$misc = mysqlREString(isset($row['misc']) ? $row['misc'] : 0);
						$tax_apply_on = pp_tax_apply_on_to_db(isset($row['tax_apply_on']) ? $row['tax_apply_on'] : '');
						$tax_value = mysqlREString(isset($row['tax_value']) ? $row['tax_value'] : '');
						$tax_amount = mysqlREString(isset($row['tax_amount']) ? $row['tax_amount'] : 0);
						$tcs = mysqlREString(isset($row['tcs']) ? $row['tcs'] : '');
						$tcs_amount = mysqlREString(isset($row['tcs_amount']) ? $row['tcs_amount'] : 0);
						$tcs_percent = 0;
						if ((string)$tcs === '2') { $tcs_percent = 2; }
						if ((string)$tcs === '3') { $tcs_percent = 20; }
						$total = mysqlREString(isset($row['total']) ? $row['total'] : 0);

						mysqlQuery("INSERT INTO package_quotation_pp_costing SET 
							quotation_id='$quotation_id',
							pax_type='$type',
							package_type='$package_type',
							hotel_cost='$hotel',
							transfer_cost='$transfer',
							activity_cost='$activity',
							land_cost='$land_cost',
							service_charge='$service_charge',
							discount_in='$discount_in',
							discount_amount='$discount_amount',
							flight_cost='$flight',
							train_cost='$train',
							cruise_cost='$cruise',
							visa_cost='$visa',
							guide_cost='$guide',
							misc_cost='$misc',
							tax_apply_on='$tax_apply_on',
							tax_value='$tax_value',
							tax_amount='$tax_amount',
							tcs='$tcs',
							tcs_percent='$tcs_percent',
							tcs_amount='$tcs_amount',
							total_cost='$total'");
					}
				}
			}
		} elseif (intval($costing_type) === 2 && isset($_POST['adult_hotel_pp_update'])) {
			$pax_types = ['adult','cweb','cwnb','infant'];
			foreach ($pax_types as $type) {
				$hotel = isset($_POST[$type.'_hotel_pp_update']) ? $_POST[$type.'_hotel_pp_update'] : 0;
				$transfer = isset($_POST[$type.'_transfer_pp_update']) ? $_POST[$type.'_transfer_pp_update'] : 0;
				$activity = isset($_POST[$type.'_activity_pp_update']) ? $_POST[$type.'_activity_pp_update'] : 0;
				$land_cost = isset($_POST[$type.'_land_cost_pp_update']) ? $_POST[$type.'_land_cost_pp_update'] : 0;
				$service_charge = isset($_POST[$type.'_service_charge_pp_update']) ? $_POST[$type.'_service_charge_pp_update'] : 0;
				$discount_amount = isset($_POST[$type.'_discount_amount_pp_update']) ? $_POST[$type.'_discount_amount_pp_update'] : 0;
				$total = isset($_POST[$type.'_total_amount_pp_update']) ? $_POST[$type.'_total_amount_pp_update'] : 0;

				$sq_check = mysqlQuery("SELECT * FROM package_quotation_pp_costing 
					WHERE quotation_id='$quotation_id' AND pax_type='$type'");

				if (mysqli_num_rows($sq_check) > 0) {
					mysqlQuery("UPDATE package_quotation_pp_costing SET 
						hotel_cost='$hotel',
						transfer_cost='$transfer',
						activity_cost='$activity',
						land_cost='$land_cost',
						service_charge='$service_charge',
						discount_amount='$discount_amount',
						total_cost='$total'
					WHERE quotation_id='$quotation_id' AND pax_type='$type'");
				} else {
					mysqlQuery("INSERT INTO package_quotation_pp_costing SET 
						quotation_id='$quotation_id',
						pax_type='$type',
						hotel_cost='$hotel',
						transfer_cost='$transfer',
						activity_cost='$activity',
						land_cost='$land_cost',
						service_charge='$service_charge',
						discount_amount='$discount_amount',
						total_cost='$total'");
				}
			}
		}

		echo "Quotation has been successfully updated.";	
		exit;
	}
	else{
		echo "error--Quotation not updated!";
		exit;
	}

}


public function program_entries_save($quotation_id,$attraction_arr, $program_arr, $stay_arr,$meal_plan_arr,$day_image_arr,$package_p_id_arr,$checked_programe_arr1,$package_id,$day_count_arr, $quotation_refer_id = 0)
{
	// First, delete all existing entries for this quotation to prevent duplicates
	$delete_query = "DELETE FROM package_quotation_program WHERE quotation_id = '$quotation_id'";
	$delete_result = mysqlQuery($delete_query);

	if (intval($package_id) == 0 && intval($quotation_refer_id) > 0) {
		$package_id = intval($quotation_refer_id);
	}
	
	for($i=0; $i<sizeof($program_arr); $i++)
	{
		try {
			$attraction = addslashes($attraction_arr[$i]);
			$program = addslashes($program_arr[$i]);
			$stay = addslashes($stay_arr[$i]);
			$meal_plan = addslashes($meal_plan_arr[$i]);
			$day_image = isset($day_image_arr[$i]) ? addslashes($day_image_arr[$i]) : '';
			$row_package_id = $package_id;
			if (isset($package_p_id_arr[$i]) && $package_p_id_arr[$i] !== '' && $package_p_id_arr[$i] !== null) {
				$row_package_id = $package_p_id_arr[$i];
			}

			if($checked_programe_arr1[$i]=="true")
			{
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_quotation_program"));
				$id = $sq_max['max']+1;

				// Use the row index as day count for new rows, or use the provided day count
				$day_count = isset($day_count_arr[$i]) ? $day_count_arr[$i] : ($i + 1);
				error_log("DEBUG: Using day_count: $day_count for row $i, package_id: $row_package_id, day_image: $day_image");
				
				$sq_plane = mysqlQuery("insert into package_quotation_program (id, quotation_id,package_id, attraction, day_wise_program, stay,meal_plan,day_image,day_count ) values ('$id', '$quotation_id', '$row_package_id','$attraction','$program', '$stay','$meal_plan','$day_image',$day_count)");
				if(!$sq_plane){
					error_log("ERROR: Failed to insert new itinerary entry: " . mysqli_error($GLOBALS['conn']));
					echo "error--Tour Itinerary not saved!";
					exit;
				} else {
					error_log("DEBUG: Successfully inserted new itinerary entry with ID $id");
				}
			}
		} catch (Exception $e) {
			error_log("ERROR: Exception in program_entries_save for row $i: " . $e->getMessage());
			echo "error--Tour Itinerary not saved! Exception: " . $e->getMessage();
			exit;
		}
		// Note: We don't need to handle deletion here since we already deleted all existing entries
	}
}

public function train_entries_update($quotation_id, $train_from_location_arr, $train_to_location_arr, $train_class_arr, $train_arrival_date_arr, $train_departure_date_arr, $train_id_arr,$train_status_arr){
	for($i=0; $i<sizeof($train_from_location_arr); $i++){

		$train_arrival_date_arr[$i] = date('Y-m-d H:i', strtotime($train_arrival_date_arr[$i]));
		$train_departure_date_arr[$i] = date('Y-m-d H:i', strtotime($train_departure_date_arr[$i]));
		if($train_status_arr[$i] == 'true'){
			if($train_id_arr[$i] != ""){
				$sq_train = mysqlQuery("update package_tour_quotation_train_entries set from_location='$train_from_location_arr[$i]', to_location='$train_to_location_arr[$i]', class='$train_class_arr[$i]', arrival_date='$train_arrival_date_arr[$i]', departure_date='$train_departure_date_arr[$i]' where id='$train_id_arr[$i]' ");
				if(!$sq_train){
					echo "error--Train information not updated!";
					exit;
				}
			}
			else{
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_train_entries"));
				$id = $sq_max['max']+1;

				$sq_train = mysqlQuery("insert into package_tour_quotation_train_entries ( id, quotation_id, from_location, to_location, class, arrival_date, departure_date ) values ( '$id', '$quotation_id', '$train_from_location_arr[$i]', '$train_to_location_arr[$i]', '$train_class_arr[$i]', '$train_arrival_date_arr[$i]', '$train_departure_date_arr[$i]' )");
				if(!$sq_train){
					echo "error--Train information not saved!";
					exit;
				}
			}
		}else{
			$sq_train = mysqlQuery("delete from package_tour_quotation_train_entries where id='$train_id_arr[$i]'");
			if(!$sq_train){
				echo "error--Train information not deleted!";
				exit;
			}
		}
	}
}

public function plane_entries_update($quotation_id,$plane_from_city_arr,$plane_to_city_arr, $plane_from_location_arr, $plane_to_location_arr, $plane_class_arr,$airline_name_arr, $arraval_arr, $dapart_arr, $plane_id_arr,$plane_status_arr)
{
	for($i=0; $i<sizeof($plane_from_location_arr); $i++){
			$arraval_arr[$i] = date('Y-m-d H:i', strtotime($arraval_arr[$i]));
			$dapart_arr[$i] = date('Y-m-d H:i', strtotime($dapart_arr[$i]));
			$from_location = array_slice(explode(' - ', $plane_from_location_arr[$i]), 1);
			$from_location = implode(' - ',$from_location);
			$to_location = array_slice(explode(' - ', $plane_to_location_arr[$i]), 1);
			$to_location = implode(' - ',$to_location);
			if($plane_status_arr[$i] == 'true'){
				if($plane_id_arr[$i]=="")
				{
					$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_plane_entries"));
					$id = $sq_max['max']+1;

					$sq_plane = mysqlQuery("insert into package_tour_quotation_plane_entries ( id, quotation_id,from_city,to_city, from_location, to_location,airline_name, class, arraval_time, dapart_time) values ( '$id', '$quotation_id', '$plane_from_city_arr[$i]', '$plane_to_city_arr[$i]', '$from_location', '$to_location','$airline_name_arr[$i]', '$plane_class_arr[$i]', '$arraval_arr[$i]', '$dapart_arr[$i]' )");
					if(!$sq_plane)
					{
						echo "Flight information not saved.";
						exit;
					}
				}else
				{
					$sq_plane=mysqlQuery("UPDATE `package_tour_quotation_plane_entries` SET `from_city`= '$plane_from_city_arr[$i]',`to_city`='$plane_to_city_arr[$i]', `from_location`='$from_location',`to_location`='$to_location',airline_name='$airline_name_arr[$i]',`class`='$plane_class_arr[$i]',`arraval_time`='$arraval_arr[$i]',`dapart_time`='$dapart_arr[$i]' WHERE `id`='$plane_id_arr[$i]'");
					if(!$sq_plane)
					{
						echo "Flight information not updated";
						exit;
					}
				}
			}else{
				$sq_plane = mysqlQuery("delete from package_tour_quotation_plane_entries where id='$plane_id_arr[$i]'");
				if(!$sq_plane){
					echo "error--Flight information not deleted!";
					exit;
				}
			}
	}

}


public function cruise_entries_update($quotation_id, $cruise_departure_date_arr, $cruise_arrival_date_arr, $route_arr, $cabin_arr, $sharing_arr,$c_entry_id_arr,$cruise_status_arr)
{
	for($i=0; $i<sizeof($cruise_departure_date_arr); $i++)
	{
			$cruise_departure_date_arr[$i] = date('Y-m-d H:i', strtotime($cruise_departure_date_arr[$i]));
		    $cruise_arrival_date_arr[$i] = date('Y-m-d H:i', strtotime($cruise_arrival_date_arr[$i]));
			if($cruise_status_arr[$i] == 'true'){
				if($c_entry_id_arr[$i]=="0")
				{
					$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_cruise_entries"));
					$id = $sq_max['max']+1;

					$sq_cruise = mysqlQuery("insert into package_tour_quotation_cruise_entries ( id, quotation_id, dept_datetime, arrival_datetime,route, cabin, sharing) values ( '$id', '$quotation_id', '$cruise_departure_date_arr[$i]', '$cruise_arrival_date_arr[$i]','$route_arr[$i]', '$cabin_arr[$i]', '$sharing_arr[$i]')");
					if(!$sq_cruise){
						echo "error--Cruise information not saved!";
						exit;
					}
				}else
				{
					$sq_update=mysqlQuery("UPDATE `package_tour_quotation_cruise_entries` SET `dept_datetime`='$cruise_departure_date_arr[$i]',`arrival_datetime`='$cruise_arrival_date_arr[$i]',route='$route_arr[$i]',`cabin`='$cabin_arr[$i]',`sharing`='$sharing_arr[$i]' WHERE `id`='$c_entry_id_arr[$i]'");
					if(!$sq_update)
					{
						echo "Cruise information not updated";
						exit;
					}
				}
			}else{
				$sq_cruise = mysqlQuery("delete from package_tour_quotation_cruise_entries where id='$c_entry_id_arr[$i]'");
				if(!$sq_cruise){
					echo "error--Cruise information not deleted!";
					exit;
				}
			}
	}
}

public function hotel_entries_update($quotation_id, $city_name_arr, $hotel_name_arr,$hotel_cat_arr,$hotel_type_arr, $hotel_stay_days_arr,$hotel_id_arr,$hotel_cost_arr,$extra_bed_arr,$extra_bed_cost_arr, $total_rooms_arr, $package_id,$hotel_status_arr,$check_in_arr,$check_out_arr,$package_type_arr,$hotel_meal_plan_arr)
{
   $sq_hotel =true;
	for($i=0; $i<sizeof($city_name_arr); $i++){

		$check_in = get_date_db($check_in_arr[$i]);
		$check_out = get_date_db($check_out_arr[$i]);
		if($hotel_status_arr[$i] == 'true'){
			if($hotel_id_arr[$i]==""){
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_hotel_entries"));
				$id = $sq_max['max']+1;

				$sq_hotel = mysqlQuery("insert into package_tour_quotation_hotel_entries ( id, quotation_id, city_name, hotel_name,room_category, hotel_type, total_days, package_id, total_rooms, hotel_cost, extra_bed, extra_bed_cost,check_in,check_out,package_type,meal_plan) values ( '$id', '$quotation_id', '$city_name_arr[$i]', '$hotel_name_arr[$i]','$hotel_cat_arr[$i]','$hotel_type_arr[$i]', '$hotel_stay_days_arr[$i]', '$package_id', '$total_rooms_arr[$i]', '$hotel_cost_arr[$i]', '$extra_bed_arr[$i]', '$extra_bed_cost_arr[$i]','$check_in','$check_out','$package_type_arr[$i]','$hotel_meal_plan_arr[$i]' )");
				if(!$sq_hotel)
				{
					echo "Hotel information not inserted.";
					exit;
				}
			}
			else
			{
				$query = "update package_tour_quotation_hotel_entries set city_name='$city_name_arr[$i]', hotel_name='$hotel_name_arr[$i]',room_category='$hotel_cat_arr[$i]',hotel_type = '$hotel_type_arr[$i]', total_days='$hotel_stay_days_arr[$i]',total_rooms='$total_rooms_arr[$i]', hotel_cost='$hotel_cost_arr[$i]', extra_bed='$extra_bed_arr[$i]', extra_bed_cost='$extra_bed_cost_arr[$i]',check_in='$check_in',check_out='$check_out',package_type='$package_type_arr[$i]',meal_plan='$hotel_meal_plan_arr[$i]' where id='$hotel_id_arr[$i]'";
				$sq_hotel = mysqlQuery($query);
				if(!$sq_hotel){
					echo "error--Hotel information not updated!".sizeof($city_name_arr);
					exit;
				}
			}
		}else{
			$sq_hotel = mysqlQuery("delete from package_tour_quotation_hotel_entries where id='$hotel_id_arr[$i]'");
			if(!$sq_hotel){
				echo "error--Hotel information not deleted!";
				exit;
			}
		}
	}

	
}

public function tranport_entries_update($quotation_id,$vehicle_name_arr,$start_date_arr,$pickup_arr,$drop_arr,$vehicle_count_arr,$transport_cost_arr1,$package_name_arr1,$pickup_type_arr,$drop_type_arr, $package_id,$transport_status_arr,$transport_id_arr,$end_date_arr,$service_duration_arr)
{
	for($i=0; $i<sizeof($vehicle_name_arr); $i++)
	{
		$is_active = ($transport_status_arr[$i] === true || $transport_status_arr[$i] === 1 || $transport_status_arr[$i] === '1' || $transport_status_arr[$i] === 'true');

		if($is_active){
			$pickup_raw = isset($pickup_arr[$i]) ? $pickup_arr[$i] : '';
			$drop_raw = isset($drop_arr[$i]) ? $drop_arr[$i] : '';
			$pickup_parts = explode("-", $pickup_raw, 2);
			$drop_parts = explode("-", $drop_raw, 2);
			$pickup_type = !empty($pickup_type_arr[$i]) ? $pickup_type_arr[$i] : (isset($pickup_parts[0]) ? $pickup_parts[0] : '');
			$drop_type = !empty($drop_type_arr[$i]) ? $drop_type_arr[$i] : (isset($drop_parts[0]) ? $drop_parts[0] : '');
			$pickup = isset($pickup_parts[1]) ? $pickup_parts[1] : '';
			$drop = isset($drop_parts[1]) ? $drop_parts[1] : '';
			$start_date_arr[$i] = date('Y-m-d H:i', strtotime($start_date_arr[$i]));
			$end_date_arr[$i] = date('Y-m-d H:i', strtotime($end_date_arr[$i]));
			$row1 = mysqli_fetch_assoc(mysqlQuery("select duration from service_duration_master where entry_id='$service_duration_arr[$i]'"));
			$duration = isset($row1['duration']) ? $row1['duration'] : '';
			$vehicle_name = intval($vehicle_name_arr[$i]);
			$vehicle_count = intval($vehicle_count_arr[$i]);
			$transport_cost = floatval($transport_cost_arr1[$i]);
			
			if($transport_id_arr[$i]==""){
					$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_transport_entries2"));
					$id = intval($sq_max['max'] ?? 0) + 1;

					$sq_trans = mysqlQuery("insert into package_tour_quotation_transport_entries2 ( `id`, `quotation_id`, `vehicle_name`, `start_date`, `pickup`, `drop`, `pickup_type`, `drop_type`, `package_id`, `transport_cost`,`vehicle_count`,`end_date`,`service_duration`) values ( '$id', '$quotation_id', '$vehicle_name', '$start_date_arr[$i]','$pickup','$drop','$pickup_type','$drop_type', '$package_id','$transport_cost','$vehicle_count','$end_date_arr[$i]','$duration')");
					if(!$sq_trans)
					{
						echo "error--Transport information not inserted.";
						exit;
					}
			}
			else{
				$transport_id = intval($transport_id_arr[$i]);
				$sq_trans = mysqlQuery("update package_tour_quotation_transport_entries2 set vehicle_name='$vehicle_name', start_date='$start_date_arr[$i]', `pickup`='$pickup', `drop`='$drop', `pickup_type`='$pickup_type', `drop_type`='$drop_type', `transport_cost`='$transport_cost',`vehicle_count`='$vehicle_count',end_date='$end_date_arr[$i]',`service_duration`='$duration' where id='$transport_id'");
				if(!$sq_trans){
					echo "error--Transport information not updated!";
					exit;
				}
			}
		}
		else if(!empty($transport_id_arr[$i])){
			$transport_id = intval($transport_id_arr[$i]);
			$sq_trans = mysqlQuery("delete from package_tour_quotation_transport_entries2 where id='$transport_id'");
			if(!$sq_trans){
				echo "error--Transport information not deleted!";
				exit;
			}
		}
	}

}

public function excursion_entries_save($quotation_id,$city_name_arr_e, $excursion_name_arr, $excursion_amt_arr,$excursion_id_arr,$exc_status_arr,$exc_date_arr_e,$transfer_option_arr,$adult_arr,$chwb_arr,$chwob_arr,$infant_arr,$vehicles_arr,$vehicle_id_arr_e = []){
	if (empty($city_name_arr_e) || !is_array($city_name_arr_e)) {
		return;
	}
	for($i=0; $i<sizeof($city_name_arr_e); $i++){
		$status = $exc_status_arr[$i] ?? 'false';
		$is_active = ($status === true || $status === 1 || $status === '1' || $status === 'true');

		if($is_active){
			$city_name = intval($city_name_arr_e[$i]);
			$excursion_name = trim((string)($excursion_name_arr[$i] ?? ''));
			if ($excursion_name !== '' && !ctype_digit($excursion_name)) {
				$sq_exc_lookup = mysqli_fetch_assoc(mysqlQuery("select entry_id from excursion_master_tariff where excursion_name='" . addslashes($excursion_name) . "' limit 1"));
				if ($sq_exc_lookup) {
					$excursion_name = (string)$sq_exc_lookup['entry_id'];
				}
			}
			$excursion_name = addslashes($excursion_name);
			if ($city_name <= 0 || $excursion_name === '') {
				continue;
			}
			$exc_date = get_datetime_db($exc_date_arr_e[$i] ?? '');
			$excursion_amt = floatval($excursion_amt_arr[$i] ?? 0);
			$transfer_option = addslashes((string)($transfer_option_arr[$i] ?? ''));
			$adult = intval($adult_arr[$i] ?? 0);
			$chwb = intval($chwb_arr[$i] ?? 0);
			$chwob = intval($chwob_arr[$i] ?? 0);
			$infant = intval($infant_arr[$i] ?? 0);
			$vehicles = intval($vehicles_arr[$i] ?? 0);
			$vehicle_id = intval($vehicle_id_arr_e[$i] ?? 0);

			if(!empty($excursion_id_arr[$i]) && $excursion_id_arr[$i] != "undefined"){
				$excursion_id = intval($excursion_id_arr[$i]);
				$sq_exc = mysqlQuery("update package_tour_quotation_excursion_entries set city_name='$city_name', excursion_name='$excursion_name', excursion_amount='$excursion_amt',exc_date='$exc_date',transfer_option='$transfer_option',adult='$adult',chwb='$chwb',chwob='$chwob',infant='$infant',vehicles='$vehicles',vehicle_id='$vehicle_id' where id='$excursion_id' ");
				if(!$sq_exc){
					echo "error--Activity information not updated!";
					exit;
				}
			}
			else{
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_excursion_entries"));
				$id = intval($sq_max['max'] ?? 0) + 1;

				$sq_exc = mysqlQuery("insert into package_tour_quotation_excursion_entries ( id, quotation_id, city_name, excursion_name, excursion_amount,exc_date,transfer_option,adult,chwb,chwob,infant,vehicles,vehicle_id ) values ( '$id', '$quotation_id', '$city_name', '$excursion_name', '$excursion_amt', '$exc_date', '$transfer_option', '$adult', '$chwb', '$chwob', '$infant', '$vehicles', '$vehicle_id')");
				if(!$sq_exc){
					echo "error--Activity information not saved!";
					exit;
				}
			}
		}else if(!empty($excursion_id_arr[$i])){
			$excursion_id = intval($excursion_id_arr[$i]);
			$sq_exc = mysqlQuery("delete from package_tour_quotation_excursion_entries where id='$excursion_id'");
			if(!$sq_exc){
				echo "error--Activity information not deleted!";
				exit;
			}
		}
	}
}


public function costing_entries_update($tour_cost_arr,$transport_cost_arr, $basic_amount_arr,$service_charge_arr,$service_tax_subtotal_arr,$total_tour_cost_arr, $costing_id_arr,$excursion_cost_arr,$adult_cost,$infant_cost,$child_with,$child_without,$bsmValues,$quotation_id,$entry_id_arr,$discount_in_arr,$discount_arr)
{
	for($i=0; $i<sizeof($tour_cost_arr); $i++){

		if (!isset($costing_id_arr[$i]) || $costing_id_arr[$i] === '' || $costing_id_arr[$i] === null) {
			continue;
		}

		$posted_basic = isset($basic_amount_arr[$i]) ? (float)$basic_amount_arr[$i] : 0;
		$posted_total = isset($total_tour_cost_arr[$i]) ? (float)$total_tour_cost_arr[$i] : 0;
		$existing_cost = mysqli_fetch_assoc(mysqlQuery(
			"SELECT basic_amount, total_tour_cost FROM package_tour_quotation_costing_entries WHERE id='".mysqlREString($costing_id_arr[$i])."'"
		));
		$existing_basic = $existing_cost ? (float)$existing_cost['basic_amount'] : 0;
		$existing_total = $existing_cost ? (float)$existing_cost['total_tour_cost'] : 0;
		// Keep previous amounts when UI accidentally posts zeros (tariff/currency sync glitch)
		if ($posted_basic <= 0 && $posted_total <= 0 && ($existing_basic > 0 || $existing_total > 0)) {
			continue;
		}

		$bsmvaluesEach = json_decode(json_encode($bsmValues[$i]));
		foreach($bsmvaluesEach[0] as $key => $value){
			switch($key){
			case 'basic' : $basic_cost = ($value != "") ? $value : $basic_amount_arr[$i];break;
			case 'service' : $service_charge = ($value != "") ? $value : $service_charge_arr[$i];break;
				}
		}
		$bsmvaluesEach = json_encode($bsmValues[$i]);
		if (!function_exists('gqd_applied_tax')) {
			include_once dirname(__FILE__) . '/../../app_settings/print_html/quotation_html/generic_quotation_data.php';
		}
		$tax_subtotal = isset($service_tax_subtotal_arr[$i]) ? $service_tax_subtotal_arr[$i] : '';
		if (function_exists('gqd_applied_tax')) {
			$computed_tax = gqd_applied_tax(array(
				'service_tax_subtotal' => $tax_subtotal,
				'bsmValues' => $bsmvaluesEach,
				'tour_cost' => isset($tour_cost_arr[$i]) ? $tour_cost_arr[$i] : 0,
				'transport_cost' => isset($transport_cost_arr[$i]) ? $transport_cost_arr[$i] : 0,
				'excursion_cost' => isset($excursion_cost_arr[$i]) ? $excursion_cost_arr[$i] : 0,
				'basic_amount' => isset($basic_amount_arr[$i]) ? $basic_amount_arr[$i] : 0,
				'service_charge' => isset($service_charge_arr[$i]) ? $service_charge_arr[$i] : 0,
				'discount_in' => isset($discount_in_arr[$i]) ? $discount_in_arr[$i] : '',
				'discount' => isset($discount_arr[$i]) ? $discount_arr[$i] : 0,
			));
			if (!empty($computed_tax['applied'])) {
				$tax_subtotal = $computed_tax['applied'];
			}
		}
		$tax_subtotal = addslashes($tax_subtotal);
		$row_total = isset($total_tour_cost_arr[$i]) ? $total_tour_cost_arr[$i] : 0;
		if (function_exists('gqd_group_costing_breakdown')) {
			$brk = gqd_group_costing_breakdown(array(
				'tour_cost' => isset($tour_cost_arr[$i]) ? $tour_cost_arr[$i] : 0,
				'transport_cost' => isset($transport_cost_arr[$i]) ? $transport_cost_arr[$i] : 0,
				'excursion_cost' => isset($excursion_cost_arr[$i]) ? $excursion_cost_arr[$i] : 0,
				'basic_amount' => isset($basic_amount_arr[$i]) ? $basic_amount_arr[$i] : 0,
				'service_charge' => isset($service_charge_arr[$i]) ? $service_charge_arr[$i] : 0,
				'discount_in' => isset($discount_in_arr[$i]) ? $discount_in_arr[$i] : '',
				'discount' => isset($discount_arr[$i]) ? $discount_arr[$i] : 0,
				'service_tax_subtotal' => $tax_subtotal,
				'bsmValues' => $bsmvaluesEach,
				'total_tour_cost' => $row_total,
			));
			$row_total = $brk['tour_total'];
		}

		$sq_plane = mysqlQuery("update package_tour_quotation_costing_entries set tour_cost='$tour_cost_arr[$i]',excursion_cost ='$excursion_cost_arr[$i]', basic_amount='$basic_amount_arr[$i]',service_charge = ' $service_charge_arr[$i]',service_tax_subtotal = '$tax_subtotal',total_tour_cost = '$row_total',transport_cost='$transport_cost_arr[$i]', bsmValues='$bsmvaluesEach',discount_in = '$discount_in_arr[$i]' ,discount = '$discount_arr[$i]'  where id='$costing_id_arr[$i]'");

		if(!$sq_plane){
			echo "error--Costing information not updated!";
			exit;
		}
	}
	for($i=0; $i<sizeof($adult_cost); $i++){
		if (!isset($entry_id_arr[$i]) || $entry_id_arr[$i] === '' || $entry_id_arr[$i] === null) {
			continue;
		}
		$adult_v = isset($adult_cost[$i]) ? $adult_cost[$i] : 0;
		$infant_v = isset($infant_cost[$i]) ? $infant_cost[$i] : 0;
		$cwith_v = isset($child_with[$i]) ? $child_with[$i] : 0;
		$cwout_v = isset($child_without[$i]) ? $child_without[$i] : 0;
		$sq_cost = mysqlQuery("update package_tour_quotation_costing_entries set adult_cost = '$adult_v', infant_cost = '$infant_v',child_with = '$cwith_v', child_without = '$cwout_v' where id='$entry_id_arr[$i]'");
		if(!$sq_cost){
			echo "error--Costing information not updated!";
			exit;
		}
	}

}
function quotation_daywiseimages_update(){
	$package_id = $_POST['package_id'];
	$day_id = $_POST['day_id'];
	$url = $_POST['url'];
	$id = $_POST['id'];
	$daywise_url = $package_id.'='.$day_id.'='.$url;
	$daywise_url = ltrim($daywise_url);
	$new_url='';
	
	$sq_day_image = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_images where id='$id' "));
	$day_url1 = explode(',',$sq_day_image['image_url']);
	for($count1 = 0; $count1<sizeof($day_url1);$count1++){
		$db_url = ltrim($day_url1[$count1]);
		if($db_url!= $daywise_url){
			$new_url .= $db_url.',';
		}
	}
	$sq_image = mysqlQuery("update package_tour_quotation_images set image_url='$new_url' where id='$id'");
	if(!$sq_image){
		echo "error--Daywise Image not deleted!";
		exit;
	}else{
		echo $new_url;
		exit;
	}
	}

	// Copy image entries from original quotation to new quotation
	public function copy_image_entries($new_quotation_id, $package_id) {
		// Find the original quotation ID by looking for quotations with the same package_id and tour details
		// We'll look for the most recent quotation with the same package_id that has images
		$original_query = "SELECT DISTINCT quotation_id FROM package_tour_quotation_images 
						   WHERE package_id = '$package_id' 
						   AND quotation_id != '$new_quotation_id' 
						   ORDER BY quotation_id DESC LIMIT 1";
		$original_result = mysqlQuery($original_query);
		
		if (mysqli_num_rows($original_result) > 0) {
			$original_quotation = mysqli_fetch_assoc($original_result);
			$original_quotation_id = $original_quotation['quotation_id'];
			
			error_log("DEBUG: Copying images from quotation $original_quotation_id to $new_quotation_id for package $package_id");
			
			// Get all image entries from the original quotation
			$images_query = "SELECT * FROM package_tour_quotation_images 
							 WHERE quotation_id = '$original_quotation_id' 
							 AND package_id = '$package_id'";
			$images_result = mysqlQuery($images_query);
			
			// Copy each image entry to the new quotation
			while ($image_row = mysqli_fetch_assoc($images_result)) {
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_images"));
				$new_image_id = $sq_max['max'] + 1;
				
				// Update the image URL to reflect the new quotation ID
				$original_image_url = $image_row['image_url'];
				// Use a more specific regex pattern to match quotation_XX_ pattern
				$new_image_url = preg_replace('/quotation_\d+_/', 'quotation_' . $new_quotation_id . '_', $original_image_url);
				
				// If regex didn't work, try a more direct approach
				if ($new_image_url == $original_image_url) {
					// Extract the part after quotation_XX_ and rebuild the URL
					if (preg_match('/quotation_\d+_(.+)/', $original_image_url, $matches)) {
						$new_image_url = 'uploads/quotation_images/day_1_quotation_' . $new_quotation_id . '_' . $matches[1];
					}
				}
				
				// Debug the regex replacement
				error_log("DEBUG: Original URL before regex: $original_image_url");
				error_log("DEBUG: New quotation ID: $new_quotation_id");
				error_log("DEBUG: URL after regex: $new_image_url");
				
				error_log("DEBUG: Original URL: $original_image_url");
				error_log("DEBUG: New URL: $new_image_url");
				
				// Copy the physical image file
				$original_file_path = "../../../" . $original_image_url;
				$new_file_path = "../../../" . $new_image_url;
				
				// Create directory if it doesn't exist
				$new_dir = dirname($new_file_path);
				if (!is_dir($new_dir)) {
					mkdir($new_dir, 0755, true);
				}
				
				// Copy the file
				if (file_exists($original_file_path)) {
					if (copy($original_file_path, $new_file_path)) {
						error_log("SUCCESS: Copied physical file from $original_file_path to $new_file_path");
					} else {
						error_log("ERROR: Failed to copy physical file from $original_file_path to $new_file_path");
					}
				} else {
					error_log("ERROR: Original file does not exist: $original_file_path");
				}
				
				$copy_query = "INSERT INTO package_tour_quotation_images 
							   (id, quotation_id, package_id, image_url) 
							   VALUES ('$new_image_id', '$new_quotation_id', '$package_id', '$new_image_url')";
				
				$copy_result = mysqlQuery($copy_query);
				if (!$copy_result) {
					error_log("ERROR: Failed to copy image entry for quotation $new_quotation_id");
				} else {
					error_log("SUCCESS: Copied image entry from quotation $original_quotation_id to $new_quotation_id with updated URL: $new_image_url");
				}
			}
		} else {
			error_log("DEBUG: No original quotation found for package $package_id");
		}
	}

	// Method to save only itinerary data
	public function save_itinerary_only($quotation_id)
	{
		error_log("QUOTATION UPDATE: Saving itinerary only for quotation_id = " . $quotation_id);
		
		try {
			// Get the itinerary data from POST
			$checked_programe_arr1 = $_POST['checked_programe_arr'];
			$day_count_arr = $_POST['day_count_arr'];
			$attraction_arr = $_POST['attraction_arr'];
			$program_arr = $_POST['program_arr'];
			$stay_arr = $_POST['stay_arr'];
			$meal_plan_arr = $_POST['meal_plan_arr'];
			$day_image_arr = isset($_POST['day_image_arr']) ? $_POST['day_image_arr'] : [];
			$package_p_id_arr = $_POST['package_p_id_arr'];
			$package_id = $_POST['package_id'];
			$is_ai_quotation = isset($_POST['is_ai_quotation']) ? $_POST['is_ai_quotation'] : '0';
			$dest_id = isset($_POST['dest_id']) ? intval($_POST['dest_id']) : 0;
			// Preserve existing quotation_refer_id — do not overwrite with MIN(package_id) for destination
			$existing_quot = mysqli_fetch_assoc(mysqlQuery("select quotation_refer_id from package_tour_quotation_master where quotation_id='$quotation_id'"));
			$quotation_refer_id = isset($existing_quot['quotation_refer_id']) ? intval($existing_quot['quotation_refer_id']) : 0;
			if ($is_ai_quotation == '1') {
				$package_id = 0;
				mysqlQuery("update package_tour_quotation_master set package_id='0' where quotation_id='$quotation_id'");
			}
			
			error_log("QUOTATION UPDATE: Itinerary data - checked_programe_arr1: " . print_r($checked_programe_arr1, true));
			error_log("QUOTATION UPDATE: Itinerary data - attraction_arr: " . print_r($attraction_arr, true));
			error_log("QUOTATION UPDATE: Itinerary data - program_arr: " . print_r($program_arr, true));
			error_log("QUOTATION UPDATE: Itinerary data - day_image_arr: " . print_r($day_image_arr, true));
			
			// Save the itinerary data
			$this->program_entries_save($quotation_id, $attraction_arr, $program_arr, $stay_arr, $meal_plan_arr, $day_image_arr, $package_p_id_arr, $checked_programe_arr1, $package_id, $day_count_arr, $quotation_refer_id);

			$inclusions = isset($_POST['inclusions']) ? $_POST['inclusions'] : null;
			$exclusions = isset($_POST['exclusions']) ? $_POST['exclusions'] : null;
			if (is_array($inclusions)) {
				$picked = '';
				foreach ($inclusions as $chunk) {
					if (is_string($chunk) && trim(strip_tags($chunk)) !== '') {
						$picked = $chunk;
					}
				}
				$inclusions = $picked !== '' ? $picked : (string) end($inclusions);
			}
			if (is_array($exclusions)) {
				$picked = '';
				foreach ($exclusions as $chunk) {
					if (is_string($chunk) && trim(strip_tags($chunk)) !== '') {
						$picked = $chunk;
					}
				}
				$exclusions = $picked !== '' ? $picked : (string) end($exclusions);
			}
			if ($inclusions !== null && $exclusions !== null) {
				$incl_plain = trim(strip_tags((string) $inclusions));
				$excl_plain = trim(strip_tags((string) $exclusions));
				if ($incl_plain !== '' || $excl_plain !== '') {
					$incl_sql = addslashes((string) $inclusions);
					$excl_sql = addslashes((string) $exclusions);
					mysqlQuery("update package_tour_quotation_master set inclusions='$incl_sql', exclusions='$excl_sql' where quotation_id='$quotation_id'");
				}
			}
			
			error_log("QUOTATION UPDATE: Itinerary data saved successfully");
			
			// Set proper headers for JSON response
			header('Content-Type: application/json');
			echo json_encode(['status' => 'success', 'message' => 'Itinerary data saved successfully.']);
			exit;
			
		} catch (Exception $e) {
			error_log("QUOTATION UPDATE: Error saving itinerary data: " . $e->getMessage());
			header('Content-Type: application/json');
			http_response_code(500);
			echo json_encode(['status' => 'error', 'message' => 'Error saving itinerary data: ' . $e->getMessage()]);
			exit;
		}
	}
}
?>