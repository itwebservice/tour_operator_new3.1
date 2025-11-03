<?php 
class quotation_update{

public function quotation_master_update()
{
	$quotation_id = $_POST['quotation_id'];
	$enquiry_id = $_POST['enquiry_id'];
	$customer_name = $_POST['customer_name'];
	$email_id = $_POST['email_id'];
    $mobile_no = $_POST['mobile_no'];
	$country_code = $_POST['country_code'];
    $total_pax = $_POST['total_pax'];
    $days_of_traveling = $_POST['days_of_traveling'];
	$traveling_date =  $_POST['traveling_date'];
	$travel_type =  $_POST['travel_type'];
	$vehicle_name =  $_POST['vehicle_name'];
	$from_date =  $_POST['from_date'];
	$to_date = $_POST['to_date'];
	$route =  $_POST['route'];
	$extra_km_cost =  $_POST['extra_km_cost'];
	$extra_hr_cost =  $_POST['extra_hr_cost'];
	$subtotal =  $_POST['subtotal'];	
	$markup_cost =  $_POST['markup_cost'];
	$markup_cost_subtotal =  $_POST['markup_cost_subtotal'];
	$taxation_id = isset($_POST['taxation_id']) ? $_POST['taxation_id'] : '';
	$service_charge =  $_POST['service_charge'];
	$service_tax_subtotal =  $_POST['service_tax_subtotal'];
	$permit =  $_POST['permit'];
	$toll_parking =  $_POST['toll_parking'];
	$driver_allowance =  $_POST['driver_allowance'];
	$total_tour_cost =  $_POST['total_tour_cost'];
	$quotation_date  = $_POST['quotation_date'];
	$local_places_to_visit = $_POST['local_places_to_visit'];
	$active_flag = $_POST['active_flag'];

	$traveling_date = get_datetime_db($traveling_date);	
	$quotation_date = get_date_db($quotation_date);
	$from_date = get_datetime_db($from_date);
	$to_date = get_datetime_db($to_date);

	$capacity = $_POST['capacity'];
	$total_hrs = $_POST['total_hrs'];
	$total_km = $_POST['total_km'];
	$rate = $_POST['rate'];
	$total_max_km = $_POST['total_max_km'];
	$other_charges= $_POST['other_charges'];
	$state_entry= $_POST['state_entry'];
	
	$roundoff = $_POST['roundoff'];

		$currency_code = $_POST['currency_code'];

	$bsmValues = json_decode(json_encode($_POST['bsmValues']));
	foreach($bsmValues[0] as $key => $value){
		switch($key){
			case 'basic' : $subtotal = ($value != "") ? $value : $subtotal;break;
			case 'service' : $service_charge = ($value != "") ? $value : $service_charge;break;
			case 'markup' : $markup_cost = ($value != "") ? $value : $markup_cost;break;
		}
    }
	$bsmValues = json_encode($bsmValues);
	$customer_name = addslashes($customer_name);
	$route = addslashes($route);
	$local_places_to_visit = addslashes($local_places_to_visit);
	$whatsapp_no = $country_code.$mobile_no;

	$sq_quotation = mysqlQuery("update car_rental_quotation_master set enquiry_id = '$enquiry_id',customer_name='$customer_name', total_pax = '$total_pax', days_of_traveling ='$days_of_traveling', traveling_date = '$traveling_date', travel_type='$travel_type', places_to_visit = '$route', vehicle_name = '$vehicle_name', from_date = '$from_date', to_date = '$to_date', route = '$route', extra_km_cost='$extra_km_cost', extra_hr_cost = '$extra_hr_cost', subtotal = '$subtotal',markup_cost ='$markup_cost',markup_cost_subtotal='$markup_cost_subtotal', taxation_id = '$taxation_id', service_charge = '$service_charge', service_tax_subtotal = '$service_tax_subtotal', permit='$permit', toll_parking='$toll_parking',driver_allowance='$driver_allowance',email_id='$email_id',mobile_no='$whatsapp_no',country_code='$country_code',whatsapp_no='$mobile_no', total_tour_cost = '$total_tour_cost', quotation_date='$quotation_date',total_hrs='$total_hrs',total_km	='$total_km',rate='$rate',total_max_km='$total_max_km',state_entry='$state_entry',other_charge='$other_charges',capacity='$capacity',local_places_to_visit='$local_places_to_visit',roundoff = '$roundoff', bsm_values = '$bsmValues',status='$active_flag',currency_code ='$currency_code' where quotation_id = '$quotation_id'");

	if($sq_quotation){
		/////////////Itinerary Update///////////////
		if(isset($_POST['special_attraction_arr']) && !empty($_POST['special_attraction_arr'])){
			$this->itinerary_update($quotation_id, $_POST['special_attraction_arr'], $_POST['day_program_arr'], $_POST['stay_arr'], $_POST['meal_plan_arr'], $_POST['checked_programe_arr'], $_POST['iti_entry_id_arr']);
		}

		echo "Quotation has been successfully updated.";	
		exit;
	}
	else{
		echo "error--Quotation not updated!";
		exit;
	}

}

public function itinerary_update($quotation_id, $special_attraction_arr, $day_program_arr, $stay_arr, $meal_plan_arr, $checked_programe_arr, $iti_entry_id_arr)
{
	for ($i = 0; $i < sizeof($day_program_arr); $i++) {

		$special_attraction_arr1 = addslashes($special_attraction_arr[$i]);
		$day_program_arr1 = addslashes($day_program_arr[$i]);
		$stay_arr1 = addslashes($stay_arr[$i]);
		$meal_plan1 = mysqlREString($meal_plan_arr[$i]);

		if ($checked_programe_arr[$i] == 'true') {
			if ($iti_entry_id_arr[$i] != '') {
				$sq = mysqlQuery("update car_rental_quotation_program set attraction = '$special_attraction_arr1', day_wise_program = '$day_program_arr1',stay='$stay_arr1',meal_plan='$meal_plan1' where id='$iti_entry_id_arr[$i]'");

				if (!$sq) {
					echo "error--Error at row " . ($i + 1) . " for Tour Itinerary information.";
					exit;
				}
			} else {
				$sq = mysqlQuery("select max(id) as max from car_rental_quotation_program");
				$value = mysqli_fetch_assoc($sq);
				$max_id = $value['max'] + 1;

				$sq = mysqlQuery("insert into car_rental_quotation_program (id, quotation_id, attraction, day_wise_program, stay, meal_plan) values ('$max_id', '$quotation_id', '$special_attraction_arr1', '$day_program_arr1', '$stay_arr1', '$meal_plan1')");

				if (!$sq) {
					echo "error--Error at row " . ($i + 1) . " for Tour Itinerary information.";
					exit;
				}
			}
		} else {
			if($iti_entry_id_arr[$i] != ''){
				$sq_iti = mysqlQuery("Delete from car_rental_quotation_program where id='$iti_entry_id_arr[$i]'");
				if (!$sq_iti) {
					echo "error--Itinerary not deleted!";
					exit;
				}
			}
		}
	}
}

}
?>