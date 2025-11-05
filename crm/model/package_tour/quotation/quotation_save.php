<?php
class quotation_save{

public function quotation_master_save()
{
	$login_id = $_POST['login_id'];
	$emp_id = $_POST['emp_id'];
	$enquiry_id = $_POST['enquiry_id'];
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
	$booking_type = $_POST['booking_type'];
	$train_cost = $_POST['train_cost'];
	$flight_cost = $_POST['flight_cost'];
	$cruise_cost = $_POST['cruise_cost'];
	$visa_cost = $_POST['visa_cost'];
	$guide_cost = $_POST['guide_cost'];
	$misc_cost = $_POST['misc_cost'];
	$branch_admin_id = $_POST['branch_admin_id'];
	$financial_year_id = $_POST['financial_year_id'];
	$price_str_url = $_POST['price_str_url']; 
	$incl_arr = $_POST['incl_arr']; 
	$excl_arr = $_POST['excl_arr'];
	$pckg_daywise_url = $_POST['pckg_daywise_url'];
	$costing_type = $_POST['costing_type'];
	$currency_code = $_POST['currency_code'];

	//Train
	$train_from_location_arr = isset($_POST['train_from_location_arr']) ? $_POST['train_from_location_arr'] : [];
	$train_to_location_arr = isset($_POST['train_to_location_arr']) ? $_POST['train_to_location_arr'] : [];
	$train_class_arr = isset($_POST['train_class_arr']) ? $_POST['train_class_arr'] : [];
	$train_arrival_date_arr = isset($_POST['train_arrival_date_arr']) ? $_POST['train_arrival_date_arr'] : [];
	$train_departure_date_arr = isset($_POST['train_departure_date_arr']) ? $_POST['train_departure_date_arr'] : [];

	//Plane
	$plane_from_city_arr = isset($_POST['plane_from_city_arr']) ? $_POST['plane_from_city_arr'] : [];
	$plane_to_city_arr = isset($_POST['plane_to_city_arr']) ? $_POST['plane_to_city_arr'] : [];
	$plane_from_location_arr = isset($_POST['plane_from_location_arr']) ? $_POST['plane_from_location_arr'] : [];
	$plane_to_location_arr = isset($_POST['plane_to_location_arr']) ? $_POST['plane_to_location_arr'] : [];
	$airline_name_arr = isset($_POST['airline_name_arr']) ? $_POST['airline_name_arr'] : [];
	$plane_class_arr = isset($_POST['plane_class_arr']) ? $_POST['plane_class_arr'] : [];
	$arraval_arr = isset($_POST['arraval_arr']) ? $_POST['arraval_arr'] : [];
	$dapart_arr = isset($_POST['dapart_arr']) ? $_POST['dapart_arr'] : [];

	//Cruise
	$cruise_departure_date_arr = isset($_POST['cruise_departure_date_arr']) ? $_POST['cruise_departure_date_arr'] : [];
	$cruise_arrival_date_arr = isset($_POST['cruise_arrival_date_arr']) ? $_POST['cruise_arrival_date_arr'] : [];
	$route_arr = isset($_POST['route_arr']) ? $_POST['route_arr'] : [];
	$cabin_arr = isset($_POST['cabin_arr']) ? $_POST['cabin_arr'] : [];
	$sharing_arr = isset($_POST['sharing_arr']) ? $_POST['sharing_arr'] : [];

	//Hotel
	$package_type_arr = isset($_POST['package_type_arr']) ? $_POST['package_type_arr'] : [];
	$city_name_arr = isset($_POST['city_name_arr']) ? $_POST['city_name_arr'] : [];
	$hotel_name_arr = isset($_POST['hotel_name_arr']) ? $_POST['hotel_name_arr'] : [];
	$hotel_cat_arr = isset($_POST['hotel_cat_arr']) ? $_POST['hotel_cat_arr'] : [];
	$hotel_stay_days_arr = isset($_POST['hotel_stay_days_arr']) ? $_POST['hotel_stay_days_arr'] : [];
	$package_name_arr = isset($_POST['package_name_arr']) ? $_POST['package_name_arr'] : [];
	$hotel_type_arr = isset($_POST['hotel_type_arr']) ? $_POST['hotel_type_arr'] : [];
	$extra_bed_arr = isset($_POST['extra_bed_arr']) ? $_POST['extra_bed_arr'] : [];
	$total_rooms_arr = isset($_POST['total_rooms_arr']) ? $_POST['total_rooms_arr'] : [];
	$hotel_cost_arr = isset($_POST['hotel_cost_arr']) ? $_POST['hotel_cost_arr'] : [];
	$extra_bed_cost_arr = isset($_POST['extra_bed_cost_arr']) ? $_POST['extra_bed_cost_arr'] : [];
	$check_in_arr = isset($_POST['check_in_arr']) ? $_POST['check_in_arr'] : [];
	$check_out_arr = isset($_POST['check_out_arr']) ? $_POST['check_out_arr'] : [];
	$hotel_meal_plan_arr = isset($_POST['hotel_meal_plan_arr']) ? $_POST['hotel_meal_plan_arr'] : [];

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
	
	//Excursion
	$city_name_arr_e = isset($_POST['city_name_arr_e']) ?$_POST['city_name_arr_e']: [];
	$excursion_name_arr = isset($_POST['excursion_name_arr']) ? $_POST['excursion_name_arr'] : [];
	$excursion_amt_arr = isset($_POST['excursion_amt_arr']) ? $_POST['excursion_amt_arr'] : [];
	$exc_date_arr_e = isset($_POST['exc_date_arr_e']) ? $_POST['exc_date_arr_e'] : [];
	$transfer_option_arr = isset($_POST['transfer_option_arr']) ? $_POST['transfer_option_arr'] : [];
	$adult_arr = isset($_POST['adult_arr']) ? $_POST['adult_arr'] : [];
	$chwb_arr = isset($_POST['chwb_arr']) ? $_POST['chwb_arr'] : [];
	$chwob_arr = isset($_POST['chwob_arr']) ? $_POST['chwob_arr'] : [];
	$infant_arr = isset($_POST['infant_arr']) ? $_POST['infant_arr'] : [];
	$vehicles_arr = isset($_POST['vehicles_arr']) ? $_POST['vehicles_arr'] : [];
	
	//Costing
	$tour_cost_arr = isset($_POST['tour_cost_arr']) ? $_POST['tour_cost_arr']: [];
	$transport_cost_arr = isset($_POST['transport_cost_arr']) ? $_POST['transport_cost_arr'] : [];
	$excursion_cost_arr = isset($_POST['excursion_cost_arr']) ? $_POST['excursion_cost_arr'] : [];
	$basic_amount_arr = isset($_POST['basic_amount_arr']) ? $_POST['basic_amount_arr'] : [];
	$service_charge_arr = isset($_POST['service_charge_arr']) ? $_POST['service_charge_arr'] : [];
	$service_tax_subtotal_arr = isset($_POST['service_tax_subtotal_arr']) ? $_POST['service_tax_subtotal_arr'] : [];
	$tcs_arr = isset($_POST['tcs_arr']) ? $_POST['tcs_arr'] : [];
	$tcsvalue_arr = isset($_POST['tcsvalue_arr']) ? $_POST['tcsvalue_arr'] : [];
	$total_tour_cost_arr = isset($_POST['total_tour_cost_arr']) ? $_POST['total_tour_cost_arr'] : [];
	$package_type_c_arr = isset($_POST['package_type_c_arr']) ? $_POST['package_type_c_arr'] : [];
	$discount_in_arr = isset($_POST['discount_in_arr']) ? $_POST['discount_in_arr'] : [];
	$discount_arr = isset($_POST['discount_arr']) ? $_POST['discount_arr'] : [] ;
    
	//Adult & child cost
	$adult_cost_arr = isset($_POST['adult_cost_arr']) ? $_POST['adult_cost_arr'] : [];
	$infant_cost_arr = isset($_POST['infant_cost_arr']) ? $_POST['infant_cost_arr'] : [];
	$child_with_arr = isset($_POST['child_with_arr']) ? $_POST['child_with_arr'] : [];
	$child_without_arr = isset($_POST['child_without_arr']) ? $_POST['child_without_arr'] : [];

	$package_id_arr = $_POST['package_id_arr'] ?: [];
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
	$temp_quotation_id = isset($_POST['temp_quotation_id']) ? $_POST['temp_quotation_id'] : '';

	// Package Program
	$attraction_arr = isset($_POST['attraction_arr']) ? $_POST['attraction_arr'] : [];
	$program_arr = isset($_POST['program_arr']) ? $_POST['program_arr'] : [];
	$stay_arr = isset($_POST['stay_arr']) ? $_POST['stay_arr'] : [];
	$meal_plan_arr = isset($_POST['meal_plan_arr']) ? $_POST['meal_plan_arr'] : [];
	$day_image_arr = isset($_POST['day_image_arr']) ? $_POST['day_image_arr'] : [];
	$package_p_id_arr = isset($_POST['package_p_id_arr']) ? $_POST['package_p_id_arr'] : [];

	$enquiry_content = '[{"name":"tour_name","value":"'.$tour_name.'"},{"name":"travel_from_date","value":"'.$from_date.'"},{"name":"travel_to_date","value":"'.$to_date.'"},{"name":"budget","value":"0"},{"name":"total_adult","value":"'.$total_adult.'"},{"name":"total_children","value":"0"},{"name":"total_infant","value":"'.$total_infant.'"},{"name":"total_members","value":"'.$total_passangers.'"},{"name":"hotel_type","value":""},{"name":"children_without_bed","value":"'.$children_without_bed.'"},{"name":"children_with_bed","value":"'.$children_with_bed.'"}]';
	
	$quotation_date = get_date_db($quotation_date);	
	$from_date = get_date_db($from_date);
	$to_date = get_date_db($to_date);
	$created_at = date('Y-m-d');
	$quotation_id_arr = array();
	$bsmValues = json_decode(json_encode($_POST['bsmValues']));
	for($i=0; $i<sizeof($package_id_arr); $i++){

		$incl = addslashes($incl_arr[$i]);
		$excl = addslashes($excl_arr[$i]);
		
		$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(quotation_id) as max from package_tour_quotation_master"));
		$quotation_id = $sq_max['max']+1;
	    $quotation_id_arr[$i] = $quotation_id;
		$whatsapp_no = $country_code.$mobile_no;
		$sq_quotation = mysqlQuery("insert into package_tour_quotation_master ( quotation_id,enquiry_id, branch_admin_id,financial_year_id, tour_name, from_date, to_date, total_days, customer_name, email_id,mobile_no,country_code,whatsapp_no, total_adult, total_infant, total_passangers, children_without_bed, children_with_bed, quotation_date, booking_type, train_cost, flight_cost, cruise_cost, visa_cost, guide_cost,misc_cost, price_str_url, package_id, created_at, login_id,emp_id,inclusions,exclusions,costing_type,currency_code,discount,status, train_acost, flight_acost, cruise_acost, train_ccost, flight_ccost, cruise_ccost, train_icost, flight_icost, cruise_icost,other_desc,user_id) values ( '$quotation_id','$enquiry_id', '$branch_admin_id','$financial_year_id', '$tour_name', '$from_date', '$to_date', '$total_days', '$customer_name', '$email_id','$whatsapp_no','$country_code','$mobile_no', '$total_adult', '$total_infant', '$total_passangers', '$children_without_bed', '$children_with_bed', '$quotation_date', '$booking_type', '$train_cost','$flight_cost','$cruise_cost','$visa_cost','$guide_cost','$misc_cost','$price_str_url','$package_id_arr[$i]', '$created_at', '$login_id', '$emp_id','$incl','$excl','$costing_type','$currency_code','$discount','1','$train_acost','$flight_acost','$cruise_acost','$train_ccost','$flight_ccost','$cruise_ccost','$train_icost','$flight_icost','$cruise_icost','$other_desc','$user_id')");

		// Only create image entry if pckg_daywise_url is not empty (gallery images)
		// Uploaded images are already handled by upload_itinerary_image.php
		// Do not create empty image entries
		if (!empty($pckg_daywise_url) && trim($pckg_daywise_url) != '') {
			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_images"));
			$image_id = $sq_max['max']+1;
			$sq_quotation1 = mysqlQuery("insert into package_tour_quotation_images ( id,quotation_id,package_id,image_url) values('$image_id','$quotation_id','$package_id_arr[$i]','$pckg_daywise_url')");
		}
	}

	if($sq_quotation){
		////////////Enquiry Save///////////
		if($enquiry_id == 0){
			$sq_max_id = mysqli_fetch_assoc(mysqlQuery("select max(enquiry_id) as max from enquiry_master"));
			$enquiry_id1 = $sq_max_id['max']+1;
			$sq_enquiry = mysqlQuery("insert into enquiry_master (enquiry_id, login_id,branch_admin_id,financial_year_id, enquiry_type,enquiry, name, mobile_no, landline_no, email_id,location, assigned_emp_id, enquiry_specification, enquiry_date, followup_date, reference_id, enquiry_content,country_code ) values ('$enquiry_id1', '$login_id', '$branch_admin_id','$financial_year_id', 'Package Booking','Strong', '$customer_name', '$mobile_no','$mobile_no', '$email_id','', '$emp_id','', '$quotation_date', '$quotation_date', '', '$enquiry_content','$country_code')");
			if($sq_enquiry){
				for($j=0; $j<sizeof($quotation_id_arr); $j++){
					$sq_quot_update = mysqlQuery("update package_tour_quotation_master set enquiry_id='$enquiry_id1' where quotation_id='$quotation_id_arr[$j]'");
				}
			}

			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from enquiry_master_entries"));
			$entry_id = $sq_max['max'] + 1;
			$sq_followup = mysqlQuery("insert into enquiry_master_entries(entry_id, enquiry_id, followup_reply,  followup_status,  followup_type, followup_date, followup_stage, created_at) values('$entry_id', '$enquiry_id1', '', 'Active','', '$quotation_date','Strong', '$quotation_date')");
			$sq_entryid = mysqlQuery("update enquiry_master set entry_id='$entry_id' where enquiry_id='$enquiry_id1'");
		}

		/////////////Enquiry Save End///////////////
		$this->train_entries_save($quotation_id_arr, $train_from_location_arr, $train_to_location_arr, $train_class_arr, $train_arrival_date_arr, $train_departure_date_arr);
		$this->plane_entries_save($quotation_id_arr,$plane_from_city_arr,$plane_to_city_arr, $plane_from_location_arr, $plane_to_location_arr, $plane_class_arr,$airline_name_arr, $arraval_arr, $dapart_arr);
		$this->cruise_entries_save($quotation_id_arr, $cruise_departure_date_arr, $cruise_arrival_date_arr, $route_arr, $cabin_arr, $sharing_arr);
		$this->hotel_entries_save($quotation_id_arr, $city_name_arr, $hotel_name_arr,$hotel_cat_arr,$hotel_type_arr, $hotel_stay_days_arr, $package_name_arr,$total_rooms_arr,$hotel_cost_arr,$extra_bed_cost_arr,$extra_bed_arr,$check_in_arr,$check_out_arr,$package_type_arr,$hotel_meal_plan_arr);
		$this->tranport_entries_save($quotation_id_arr,$vehicle_name_arr,$start_date_arr,$pickup_arr,$drop_arr,$vehicle_count_arr,$transport_cost_arr1,$package_name_arr1,$end_date_arr,$service_duration_arr);	

		$this->costing_entries_save($tcs_arr,$tcsvalue_arr,$quotation_id,$tour_cost_arr,$basic_amount_arr,$service_charge_arr,$service_tax_subtotal_arr,$total_tour_cost_arr,$transport_cost_arr,$excursion_cost_arr,$adult_cost_arr,$infant_cost_arr,$child_with_arr,$child_without_arr,$bsmValues,$package_type_c_arr,$discount_in_arr,$discount_arr);
		$this->excursion_entries_save($quotation_id_arr,$city_name_arr_e, $excursion_name_arr, $excursion_amt_arr,$exc_date_arr_e,$transfer_option_arr,$adult_arr,$chwb_arr,$chwob_arr,$infant_arr,$vehicles_arr);
		// Update temporary quotation IDs with actual quotation IDs
		$this->update_temporary_quotation_ids($quotation_id_arr, $temp_quotation_id);
		
		// Update temporary quotation IDs in package_tour_quotation_images table
		$this->update_temporary_quotation_ids_in_images($quotation_id_arr, $temp_quotation_id, $package_id_arr);
		
		$this->program_entries_save($quotation_id_arr,$attraction_arr, $program_arr, $stay_arr,$meal_plan_arr,$day_image_arr,$package_p_id_arr,$package_id_arr,$pckg_daywise_url);	

		echo "Quotation has been successfully saved. Quotation ID: " . $quotation_id_arr[0];
		exit;
	}
	else{
		echo "error--Quotation not saved!";
		exit;
	}

}

public function train_entries_save($quotation_id_arr, $train_from_location_arr, $train_to_location_arr, $train_class_arr, $train_arrival_date_arr, $train_departure_date_arr)
{
	for($j=0; $j<sizeof($quotation_id_arr); $j++){
	for($i=0; $i<sizeof($train_from_location_arr); $i++){

		$train_arrival_date_arr[$i] = get_datetime_db($train_arrival_date_arr[$i]);
		$train_departure_date_arr[$i] = get_datetime_db($train_departure_date_arr[$i]);

		$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_train_entries"));
		$id = $sq_max['max']+1;

		$sq_train = mysqlQuery("insert into package_tour_quotation_train_entries ( id, quotation_id, from_location, to_location, class, arrival_date, departure_date ) values ( '$id', '$quotation_id_arr[$j]', '$train_from_location_arr[$i]', '$train_to_location_arr[$i]', '$train_class_arr[$i]', '$train_arrival_date_arr[$i]', '$train_departure_date_arr[$i]' )");
		if(!$sq_train){
			echo "error--Train information not saved!";
			exit;
		}
	}
	}
}

public function plane_entries_save($quotation_id_arr,$plane_from_city_arr,$plane_to_city_arr, $plane_from_location_arr, $plane_to_location_arr, $plane_class_arr,$airline_name_arr, $arraval_arr, $dapart_arr)
{
	for($j=0; $j<sizeof($quotation_id_arr); $j++){

		for($i=0; $i<sizeof($plane_from_location_arr); $i++){
		
			$arraval_arr[$i] = date('Y-m-d H:i', strtotime($arraval_arr[$i]));
			$dapart_arr[$i] = date('Y-m-d H:i', strtotime($dapart_arr[$i]));

			$from_location = array_slice(explode(' - ', $plane_from_location_arr[$i]), 1);
			$from_location = implode(' - ',$from_location);
			$to_location = array_slice(explode(' - ', $plane_to_location_arr[$i]), 1);
			$to_location = implode(' - ',$to_location);

			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_plane_entries"));
			$id = $sq_max['max']+1;

			$sq_plane = mysqlQuery("insert into package_tour_quotation_plane_entries ( id, quotation_id,from_city,to_city, from_location, to_location,airline_name, class, arraval_time, dapart_time) values ( '$id', '$quotation_id_arr[$j]', '$plane_from_city_arr[$i]', '$plane_to_city_arr[$i]', '$from_location', '$to_location','$airline_name_arr[$i]', '$plane_class_arr[$i]', '$arraval_arr[$i]', '$dapart_arr[$i]' )");
			if(!$sq_plane){
				echo "error--Plane information not saved!";
				exit;
			}
		}

	}

}

public function cruise_entries_save($quotation_id_arr, $cruise_departure_date_arr, $cruise_arrival_date_arr, $route_arr, $cabin_arr, $sharing_arr)
{
	for($j=0; $j<sizeof($quotation_id_arr); $j++){
	for($i=0; $i<sizeof($cruise_departure_date_arr); $i++){

		$cruise_departure_date_arr[$i] = date('Y-m-d H:i', strtotime($cruise_departure_date_arr[$i]));
		$cruise_arrival_date_arr[$i] = date('Y-m-d H:i', strtotime($cruise_arrival_date_arr[$i]));

		$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_cruise_entries"));
		$id = $sq_max['max']+1;

		$sq_cruise = mysqlQuery("insert into package_tour_quotation_cruise_entries ( id, quotation_id, dept_datetime, arrival_datetime,route, cabin, sharing) values ( '$id', '$quotation_id_arr[$j]', '$cruise_departure_date_arr[$i]', '$cruise_arrival_date_arr[$i]','$route_arr[$i]', '$cabin_arr[$i]', '$sharing_arr[$i]')");
		if(!$sq_cruise){
			echo "error--Cruise information not saved!";
			exit;
		}
	}

	}
}

public function hotel_entries_save($quotation_id_arr, $city_name_arr, $hotel_name_arr,$hotel_cat_arr,$hotel_type_arr, $hotel_stay_days_arr, $package_name_arr,$total_rooms_arr,$hotel_cost_arr,$extra_bed_cost_arr,$extra_bed_arr,$check_in_arr,$check_out_arr,$package_type_arr,$hotel_meal_plan_arr)
{
	$j=0;
	for($i=0; $i<sizeof($city_name_arr); $i++){

		$check_in = get_date_db($check_in_arr[$i]);
		$check_out = get_date_db($check_out_arr[$i]);

		$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_hotel_entries"));
		$id = $sq_max['max']+1;

		$sq_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_name = '$package_name_arr[$i]'"));
		$package_id = isset($sq_package['package_id']) ? $sq_package['package_id'] : 0;
		$sq_plane = mysqlQuery("insert into package_tour_quotation_hotel_entries ( id, quotation_id, city_name, hotel_name,room_category,hotel_type, total_days,package_id,total_rooms,hotel_cost,extra_bed,extra_bed_cost,check_in,check_out,package_type,meal_plan ) values ( '$id', '$quotation_id_arr[$j]', '$city_name_arr[$i]', '$hotel_name_arr[$i]', '$hotel_cat_arr[$i]','$hotel_type_arr[$i]', '$hotel_stay_days_arr[$i]','$package_id','$total_rooms_arr[$i]','$hotel_cost_arr[$i]','$extra_bed_arr[$i]','$extra_bed_cost_arr[$i]','$check_in','$check_out','$package_type_arr[$i]','$hotel_meal_plan_arr[$i]' )");
		
		if(!$sq_plane){
			echo "error--Hotel information not saved!";
			exit;
		}
	}
}

public function tranport_entries_save($quotation_id_arr,$vehicle_name_arr,$start_date_arr,$pickup_arr,$drop_arr,$vehicle_count_arr,$transport_cost_arr1,$package_name_arr1,$end_date_arr,$service_duration_arr){
	$j=0;
	for($i=0; $i<sizeof($vehicle_name_arr); $i++){
		$start_date_arr[$i] = get_date_db($start_date_arr[$i]);
		$end_date_arr[$i] = get_date_db($end_date_arr[$i]);
		$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_transport_entries2"));
		$id = $sq_max['max']+1;

		$sq_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_name = '$package_name_arr1[$i]'"));
		$package_id = isset($sq_package['package_id']) ? $sq_package['package_id'] : 0;
		$row1 = mysqli_fetch_assoc(mysqlQuery("select duration from service_duration_master where entry_id='$service_duration_arr[$i]'"));
		$duration = isset($row1['duration']) ? $row1['duration'] : '';

		$pickup_type = explode("-",$pickup_arr[$i])[0];
        $drop_type = explode("-",$drop_arr[$i])[0];
        $pickup = explode("-",$pickup_arr[$i])[1];
        $drop = explode("-",$drop_arr[$i])[1];

		$sq_plane = mysqlQuery("INSERT INTO `package_tour_quotation_transport_entries2`(`id`, `quotation_id`, `vehicle_name`, `start_date`, `pickup`, `drop`, `pickup_type`, `drop_type`, `package_id`, `transport_cost`,`vehicle_count`,`end_date`,`service_duration`) values ( '$id', '$quotation_id_arr[$j]', '$vehicle_name_arr[$i]', '$start_date_arr[$i]', '$pickup','$drop','$pickup_type','$drop_type','$package_id','$transport_cost_arr1[$i]','$vehicle_count_arr[$i]','$end_date_arr[$i]','$duration' )");
		if(!$sq_plane){
			echo "error--Transport information not saved!";
			exit;
		}
	}
}

public function excursion_entries_save($quotation_id_arr,$city_name_arr_e, $excursion_name_arr, $excursion_amt_arr,$exc_date_arr_e,$transfer_option_arr,$adult_arr,$chwb_arr,$chwob_arr,$infant_arr,$vehicles_arr)
{
	for($i=0; $i<sizeof($quotation_id_arr); $i++){
		for($j=0; $j<sizeof($city_name_arr_e); $j++){

		$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_excursion_entries"));
		$id = $sq_max['max']+1;
		$exc_date_arr_e[$j] = get_datetime_db($exc_date_arr_e[$j]);
		$sq_plane = mysqlQuery("insert into package_tour_quotation_excursion_entries ( id, quotation_id, city_name, excursion_name, excursion_amount,exc_date,transfer_option,adult,chwb,chwob,infant,vehicles ) values ( '$id', '$quotation_id_arr[$i]', '$city_name_arr_e[$j]','$excursion_name_arr[$j]', '$excursion_amt_arr[$j]','$exc_date_arr_e[$j]','$transfer_option_arr[$j]','$adult_arr[$j]','$chwb_arr[$j]','$chwob_arr[$j]','$infant_arr[$j]','$vehicles_arr[$i]')");
		if(!$sq_plane){
			echo "error--Activity information not saved!";
			exit;
		}
	}
	}
}

public function update_temporary_quotation_ids($quotation_id_arr, $temp_quotation_id = '')
{
	// Update any temporary quotation IDs in package_quotation_program table
	for($i=0; $i<sizeof($quotation_id_arr); $i++)
	{
		$actual_quotation_id = $quotation_id_arr[$i];
		
		// Update specific temporary quotation ID to actual quotation ID
		if (!empty($temp_quotation_id)) {
			$update_query = "UPDATE package_quotation_program SET quotation_id = '$actual_quotation_id' WHERE quotation_id = '$temp_quotation_id'";
			$result = mysqlQuery($update_query);
			error_log("DEBUG: Updated temporary quotation ID '$temp_quotation_id' to actual quotation ID: $actual_quotation_id");
			if ($result) {
				error_log("DEBUG: Update successful");
			} else {
				error_log("DEBUG: Update failed");
			}
		} else {
			// Fallback: update any temporary quotation IDs
			$update_query = "UPDATE package_quotation_program SET quotation_id = '$actual_quotation_id' WHERE quotation_id LIKE 'temp_%'";
			mysqlQuery($update_query);
			error_log("DEBUG: Updated all temporary quotation IDs to actual quotation ID: $actual_quotation_id");
		}
	}
}

public function update_temporary_quotation_ids_in_images($quotation_id_arr, $temp_quotation_id = '', $package_id_arr = [])
{
	// Update any temporary quotation IDs in package_tour_quotation_images table
	for($i=0; $i<sizeof($quotation_id_arr); $i++)
	{
		$actual_quotation_id = $quotation_id_arr[$i];
		
		// First, try to update specific temporary quotation ID if provided
		if (!empty($temp_quotation_id)) {
			$update_query = "UPDATE package_tour_quotation_images SET quotation_id = '$actual_quotation_id' WHERE quotation_id = '$temp_quotation_id'";
			$result = mysqlQuery($update_query);
			error_log("DEBUG: Updated specific temporary quotation ID '$temp_quotation_id' to actual quotation ID in images table: $actual_quotation_id");
			if ($result) {
				error_log("DEBUG: Specific temp ID update successful");
			} else {
				error_log("DEBUG: Specific temp ID update failed");
			}
		}
		
		// Always also update any remaining temporary quotation IDs (fallback)
		$update_query = "UPDATE package_tour_quotation_images SET quotation_id = '$actual_quotation_id' WHERE quotation_id LIKE 'temp_%'";
		$result = mysqlQuery($update_query);
		error_log("DEBUG: Updated all temporary quotation IDs in images table to actual quotation ID: $actual_quotation_id");
		if ($result) {
			error_log("DEBUG: General temp ID update successful");
		} else {
			error_log("DEBUG: General temp ID update failed");
		}
		
		// Also update any images with quotation_id = 0 (failed temp ID updates)
		$update_query_zero = "UPDATE package_tour_quotation_images SET quotation_id = '$actual_quotation_id' WHERE quotation_id = 0 AND package_id = '$package_id_arr[$i]'";
		$result_zero = mysqlQuery($update_query_zero);
		error_log("DEBUG: Updated images with quotation_id = 0 to actual quotation ID: $actual_quotation_id");
		if ($result_zero) {
			error_log("DEBUG: Zero ID update successful");
		} else {
			error_log("DEBUG: Zero ID update failed");
		}
	}
}

public function program_entries_save($quotation_id_arr,$attraction_arr, $program_arr, $stay_arr,$meal_plan_arr,$day_image_arr,$package_p_id_arr,$package_id_arr,$pckg_daywise_url)
{
	error_log("DEBUG: program_entries_save called with:");
	error_log("quotation_id_arr: " . print_r($quotation_id_arr, true));
	error_log("attraction_arr: " . print_r($attraction_arr, true));
	error_log("program_arr: " . print_r($program_arr, true));
	error_log("stay_arr: " . print_r($stay_arr, true));
	error_log("meal_plan_arr: " . print_r($meal_plan_arr, true));
	error_log("day_image_arr: " . print_r($day_image_arr, true));
	error_log("package_p_id_arr: " . print_r($package_p_id_arr, true));
	error_log("package_id_arr: " . print_r($package_id_arr, true));

	// Check if we have itinerary data to save
	error_log("DEBUG: Checking itinerary data - attraction_arr count: " . count($attraction_arr) . ", program_arr count: " . count($program_arr) . ", stay_arr count: " . count($stay_arr));
	
	if(empty($attraction_arr) || empty($program_arr) || empty($stay_arr)) {
		error_log("DEBUG: No itinerary data to save - arrays are empty");
		error_log("DEBUG: attraction_arr: " . print_r($attraction_arr, true));
		error_log("DEBUG: program_arr: " . print_r($program_arr, true));
		error_log("DEBUG: stay_arr: " . print_r($stay_arr, true));
		return;
	}

	// Save itinerary data for each quotation
	for($i=0; $i<sizeof($quotation_id_arr); $i++)
	{
		$quotation_id = $quotation_id_arr[$i];
		$day_count = 0;
		
		// Save all itinerary entries for this quotation
		for($j=0; $j<sizeof($program_arr); $j++)
		{
			// Skip empty entries
			if(empty($attraction_arr[$j]) || empty($program_arr[$j]) || empty($stay_arr[$j])) {
				continue;
			}
			
			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_quotation_program"));
			$id = $sq_max['max']+1;
			$day_count++;

			$attr = addslashes($attraction_arr[$j]);
			$program = addslashes($program_arr[$j]);
			$stay = addslashes($stay_arr[$j]);
			$meal_plan = addslashes($meal_plan_arr[$j]);
			$day_image = isset($day_image_arr[$j]) ? addslashes($day_image_arr[$j]) : '';
			
			// Use the first package ID if available, otherwise use 1 as default
			$package_id = !empty($package_id_arr) ? $package_id_arr[0] : 1;
			
			error_log("DEBUG: Inserting program data - ID: $id, Quotation ID: $quotation_id, Package ID: $package_id, Attraction: $attr, Day Image: $day_image");
			$sq_plane = mysqlQuery("insert into package_quotation_program ( id, quotation_id,package_id, attraction, day_wise_program, stay,meal_plan,day_image,day_count ) values ( '$id', '$quotation_id','$package_id', '$attr','$program', '$stay','$meal_plan','$day_image','$day_count')");
			if(!$sq_plane){
				error_log("ERROR: Program not saved!");
				echo "error--Program not saved!";
				exit;
		    } else {
		    	error_log("DEBUG: Program data saved successfully");
		    }
		}
	}

}
    public function costing_entries_save($tcs_arr,$tcsvalue_arr,$quotation_id,$tour_cost_arr,$basic_amount_arr,$service_charge_arr,$service_tax_subtotal_arr,$total_tour_cost_arr,$transport_cost_arr,$excursion_cost_arr,$adult_cost_arr,$infant_cost_arr,$child_with_arr,$child_without_arr,$bsmValues,$package_type_c_arr,$discount_in_arr,$discount_arr)
    {

		$sq_max_sort_order = mysqli_fetch_assoc(mysqlQuery("SELECT MAX(sort_order) AS max_sort_order FROM package_tour_quotation_costing_entries WHERE quotation_id = '$quotation_id'"));
		$current_sort_order = $sq_max_sort_order['max_sort_order'] + 1;

    	for($i=0; $i<sizeof($package_type_c_arr); $i++)
    	{
    		$bsmvaluesEach = json_decode(json_encode($bsmValues[$i]));
    		$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_costing_entries"));
    		$id = $sq_max['max']+1;
    		$bsmvaluesEach = json_encode($bsmValues[$i]);
    		$sq_plane = mysqlQuery("insert into package_tour_quotation_costing_entries ( id, quotation_id, tour_cost,excursion_cost,basic_amount, service_charge, service_tax_subtotal, total_tour_cost, package_id, transport_cost,adult_cost,infant_cost,child_with,child_without,bsmValues,package_type,discount_in,discount,sort_order) values ( '$id', '$quotation_id', '$tour_cost_arr[$i]','$excursion_cost_arr[$i]', '$basic_amount_arr[$i]', ' $service_charge_arr[$i]', '$service_tax_subtotal_arr[$i]', '$total_tour_cost_arr[$i]','0','$transport_cost_arr[$i]','$adult_cost_arr[$i]','$infant_cost_arr[$i]','$child_with_arr[$i]','$child_without_arr[$i]','$bsmvaluesEach','$package_type_c_arr[$i]','$discount_in_arr[$i]','$discount_arr[$i]','$current_sort_order')");
    		if(!$sq_plane){
    			echo "error--Costing information not saved!";
    			exit;
    		}
	}
    }
}
?>