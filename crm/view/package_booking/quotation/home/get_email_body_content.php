<?php
include "../../../../model/model.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$quotation_id = $_POST['quotation_id'];
$email_option = $_POST['email_option'];
$options = isset($_POST['options']) && !empty($_POST['options']) ? $_POST['options'] : array();

// Debug information
error_log("Quotation ID: " . $quotation_id);
error_log("Email Option: " . $email_option);
error_log("Selected Options: " . print_r($options, true));
error_log("Inclusions/Exclusions selected: " . (in_array('inclusion_exclusion', $options) ? 'YES' : 'NO'));

// Get quotation details
$sq_quotation = mysqli_fetch_assoc(mysqlQuery("SELECT *, 
	COALESCE(is_sub_quotation, '0') as is_sub_quotation,
	COALESCE(parent_quotation_id, '0') as parent_quotation_id,
	COALESCE(quotation_display_id, '') as quotation_display_id
	FROM package_tour_quotation_master WHERE quotation_id = '$quotation_id'"));
$sq_package = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM custom_package_master WHERE package_id = '{$sq_quotation['package_id']}'"));

// Get costing details
$sq_cost = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM package_tour_quotation_costing_entries WHERE quotation_id = '$quotation_id' ORDER BY sort_order LIMIT 1"));

// Calculate costs
$basic_cost = $sq_cost['basic_amount'];
$service_charge = $sq_cost['service_charge'];
$service_tax_amount = 0;

$bsmValues = json_decode($sq_cost['bsmValues'], true);
$discount_in = $sq_cost['discount_in'];
$discount = $sq_cost['discount'];

if ($discount_in == 'Percentage') {
    $act_discount = (float)($service_charge) * (float)($discount) / 100;
} else {
    $act_discount = ($service_charge != 0) ? $discount : 0;
}

$service_charge = $service_charge - (float)($act_discount);

// Calculate service tax
$name = '';
if ($sq_cost['service_tax_subtotal'] !== 0.00 && ($sq_cost['service_tax_subtotal']) !== '') {
    $service_tax_subtotal1 = explode(',', $sq_cost['service_tax_subtotal']);
    for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
        $service_tax = explode(':', $service_tax_subtotal1[$i]);
        $service_tax_amount = (float)($service_tax_amount) + (float)($service_tax[2]);
        $name .= $service_tax[0] . ' ';
        $percent = $service_tax[1];
    }
}

// Calculate TCS
if (isset($bsmValues[0]['tcsper']) && $bsmValues[0]['tcsper'] != 'NaN') {
    $tcsper = $bsmValues[0]['tcsper'];
    $tcsvalue = $bsmValues[0]['tcsvalue'];
} else {
    $tcsper = 0;
    $tcsvalue = 0;
}

// Calculate total costs
$quotation_cost = $basic_cost + $service_charge + $service_tax_amount + $sq_quotation['train_cost'] + $sq_quotation['cruise_cost'] + $sq_quotation['flight_cost'] + $sq_quotation['visa_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'] + (float)($tcsvalue) - $act_discount;

$travel_cost = $sq_quotation['train_cost'] + $sq_quotation['flight_cost'] + $sq_quotation['cruise_cost'] + $sq_quotation['visa_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'];

// Format dates
$quotation_date = $sq_quotation['quotation_date'];
$yr = explode("-", $quotation_date);
$year = $yr[0];

// Get quotation display ID (prefer quotation_display_id if available)
$quotation_id_display = '';
if (isset($sq_quotation['quotation_display_id']) && !empty($sq_quotation['quotation_display_id'])) {
    $quotation_id_display = $sq_quotation['quotation_display_id'];
} else {
    // Fallback to generating from quotation_id
    $quotation_id_display = get_quotation_id($sq_quotation['quotation_id'], $year);
}

// Check if this is a sub-quotation and format accordingly
$is_sub_quotation = isset($sq_quotation['is_sub_quotation']) && $sq_quotation['is_sub_quotation'] == '1';
if ($is_sub_quotation) {
    // For sub-quotations, ensure proper version numbering
    if (strpos($quotation_id_display, '.') === false) {
        // If no version number exists, add it
        $parent_quotation_id = isset($sq_quotation['parent_quotation_id']) ? $sq_quotation['parent_quotation_id'] : null;
        if ($parent_quotation_id && $parent_quotation_id != '0') {
            // Get parent quotation details
            $parent_quotation = mysqli_fetch_assoc(mysqlQuery("SELECT quotation_date FROM package_tour_quotation_master WHERE quotation_id='$parent_quotation_id'"));
            if ($parent_quotation) {
                $parent_year = explode("-", $parent_quotation['quotation_date'])[0];
                $parent_id_display = get_quotation_id($parent_quotation_id, $parent_year);
                
                // Count existing sub-quotations for this parent
                $sub_count = mysqli_num_rows(mysqlQuery("SELECT quotation_id FROM package_tour_quotation_master WHERE parent_quotation_id='$parent_quotation_id' AND quotation_id <= '{$sq_quotation['quotation_id']}'"));
                $quotation_id_display = $parent_id_display . '.' . $sub_count;
            }
        }
    }
}

$from_date = get_date_user($sq_quotation['from_date']);
$to_date = get_date_user($sq_quotation['to_date']);

// Calculate duration
$from_date_obj = new DateTime($sq_quotation['from_date']);
$to_date_obj = new DateTime($sq_quotation['to_date']);
$duration = $from_date_obj->diff($to_date_obj)->days;

// Get hotel details (resolve IDs to names)
$hotel_details = '';
$sq_hotel = mysqlQuery("SELECT * FROM package_tour_quotation_hotel_entries WHERE quotation_id = '$quotation_id'");
$hotel_count = 0;
while ($row_hotel = mysqli_fetch_assoc($sq_hotel)) {
    // Handle possible column name differences (city_name vs city_id, hotel_name vs hotel_id storing IDs)
    $city_id_for_lookup = isset($row_hotel['city_name']) && $row_hotel['city_name'] !== '' ? $row_hotel['city_name'] : (isset($row_hotel['city_id']) ? $row_hotel['city_id'] : '');
    $hotel_id_for_lookup = isset($row_hotel['hotel_name']) && $row_hotel['hotel_name'] !== '' ? $row_hotel['hotel_name'] : (isset($row_hotel['hotel_id']) ? $row_hotel['hotel_id'] : '');

    $sq_city = $city_id_for_lookup !== '' ? mysqli_fetch_assoc(mysqlQuery("SELECT city_name FROM city_master WHERE city_id='".$city_id_for_lookup."'")) : null;
    $sq_hotel_name = $hotel_id_for_lookup !== '' ? mysqli_fetch_assoc(mysqlQuery("SELECT hotel_name FROM hotel_master WHERE hotel_id='".$hotel_id_for_lookup."'")) : null;

    $city_display = $sq_city && isset($sq_city['city_name']) ? $sq_city['city_name'] : (isset($row_hotel['city_name']) ? $row_hotel['city_name'] : (isset($row_hotel['city_id']) ? $row_hotel['city_id'] : ''));
    $hotel_display = $sq_hotel_name && isset($sq_hotel_name['hotel_name']) ? $sq_hotel_name['hotel_name'] : (isset($row_hotel['hotel_name']) ? $row_hotel['hotel_name'] : (isset($row_hotel['hotel_id']) ? $row_hotel['hotel_id'] : ''));

    $room_category_display = isset($row_hotel['room_category']) ? $row_hotel['room_category'] : (isset($row_hotel['hotel_type']) ? $row_hotel['hotel_type'] : '');
    $meal_plan_display = isset($row_hotel['meal_plan']) ? $row_hotel['meal_plan'] : '';

    $hotel_details .= "*{$city_display}*  -*{$hotel_display}* - *{$room_category_display}*  -*{$meal_plan_display}*\n";
    $hotel_count++;
}

error_log("Hotel count: " . $hotel_count);

// Get itinerary details
$itinerary_details = '';
$sq_package_program = mysqlQuery("SELECT * FROM package_quotation_program WHERE quotation_id = '$quotation_id'");
$count = 1;
$j = 0;
$itinerary_count = 0;

if (mysqli_num_rows($sq_package_program) > 0) {
    $itinerary_details = "\n 📅 *Itinerary*\n-----------\n";
    while ($row_itinerary = mysqli_fetch_assoc($sq_package_program)) {
        $itinerary_details .= "*Day - {$count}*   " .
                              "*" . htmlspecialchars($row_itinerary['attraction']) . "*      " .
                              "*(" . htmlspecialchars($row_itinerary['stay']) . ")*     " .
                              "*(" . htmlspecialchars($row_itinerary['meal_plan']) . ")*\n";

        $count++;
        $j++;
        $itinerary_count++;
    }
}

error_log("Itinerary count: " . $itinerary_count);

// Get transportation details
$transport_details = '';
$sq_transport = mysqlQuery("SELECT t.*, v.vehicle_name as actual_vehicle_name FROM package_tour_quotation_transport_entries2 t 
                           LEFT JOIN b2b_transfer_master v ON t.vehicle_name = v.entry_id 
                           WHERE t.quotation_id = '$quotation_id'");
$transport_count = 0;
while ($row_transport = mysqli_fetch_assoc($sq_transport)) {
    $from_date_trans = get_date_user($row_transport['start_date']);
    $to_date_trans = get_date_user($row_transport['end_date']);
    $vehicle_name = !empty($row_transport['actual_vehicle_name']) ? $row_transport['actual_vehicle_name'] : 'Vehicle ID: ' . $row_transport['vehicle_name'];
    
    // Get pickup location
    $pickup = '';
    if($row_transport['pickup_type'] == 'city'){
        $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_transport[pickup]'"));
        $pickup = $row['city_name'];
    }
    else if($row_transport['pickup_type'] == 'hotel'){
        $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_transport[pickup]'"));
        $pickup = $row['hotel_name'];
    }
    else{
        $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_transport[pickup]'"));
        $airport_nam = clean($row['airport_name']);
        $airport_code = clean($row['airport_code']);
        $pickup = $airport_nam." (".$airport_code.")";
    }
    
    // Get drop location
    $drop = '';
    if($row_transport['drop_type'] == 'city'){
        $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_transport[drop]'"));
        $drop = $row['city_name'];
    }
    else if($row_transport['drop_type'] == 'hotel'){
        $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_transport[drop]'"));
        $drop = $row['hotel_name'];
    }
    else{
        $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_transport[drop]'"));
        $airport_nam = clean($row['airport_name']);
        $airport_code = clean($row['airport_code']);
        $drop = $airport_nam." (".$airport_code.")";
    }
    
    // Get service duration
    $service_duration = '';
    if(!empty($row_transport['service_duration'])){
        $row = mysqli_fetch_assoc(mysqlQuery("select duration from service_duration_master where entry_id='$row_transport[service_duration]'"));
        $service_duration = $row['duration'];
    }
    
    $transport_details .= "*{$vehicle_name}* *{$from_date_trans}*    *{$to_date_trans}*    *{$pickup} to {$drop}*    *{$service_duration}*    *({$row_transport['vehicle_count']})*\n";
    $transport_count++;
}



$terms_and_conditions_details = '';

$sq_terms_and_conditions = mysqlQuery("SELECT * FROM terms_and_conditions WHERE type='Package Quotation' AND active_flag='Active' LIMIT 1");

if ($sq_terms_and_conditions && mysqli_num_rows($sq_terms_and_conditions) > 0) {
    $row_terms = mysqli_fetch_assoc($sq_terms_and_conditions);
    $terms_and_conditions_details = $row_terms['terms_and_conditions'] ?? '';
}

// Generate email body content
$email_content = "Hi Guest,\n\n";
$email_content .= "Greetings from ITOURS LLP PVT LTDS\n\n";
$email_content .= "Thank you for your query with us. As per your requirements, following are the package details.\n";
$email_content .= "*Quotation ID :* {$quotation_id_display} \n\n";
$email_content .= "*{$sq_package['package_name']}*\n";
$email_content .= "* {$from_date} for {$duration} Nights, " . ($duration + 1) . " Days\n";
$email_content .= "* {$sq_quotation['total_adult']} Adults\n";
$email_content .= "* " . ($sq_quotation['children_with_bed'] + $sq_quotation['children_without_bed']) . " Child\n";
$email_content .= "* {$sq_quotation['total_infant']} Infant\n";
$email_content .= "               \n";

// Price Structure - only show if selected
if (in_array('price_structure', $options)) {
    $email_content .= "*Tour Amount :* INR " . number_format($quotation_cost - $travel_cost, 2) . "\n";
    $email_content .= "*Travel Amount :* INR " . number_format($travel_cost, 2) . "\n";
    $email_content .= "*Tax :* INR " . number_format($service_tax_amount, 2) . "\n";
    $email_content .= "*Tcs :* INR " . number_format($tcsvalue, 2) . "\n";
    $email_content .= "*Total Price :*  INR " . number_format($quotation_cost, 2) . " \n\n";
}




if (!empty($hotel_details)) {
    if ($hotel_count > 0) {
        $email_content .= "🏨  *Hotels*\n";
        $email_content .= "-----------\n";
        $email_content .= $hotel_details . "\n";
    } else {
        $email_content .= "🏨  *Hotels*\n";
        $email_content .= "-----------\n";
        $email_content .= "Hotel details will be provided upon confirmation.\n\n";
    }
}


// Itinerary - only show if selected
if (in_array('itinerary', $options)) {
    if ($itinerary_count > 0) {
        $email_content .= "-----------\n";
        $email_content .= $itinerary_details . "\n";
    } else {
        $email_content .= "-----------\n";
        $email_content .= "📅 *Itinerary*\n";
        $email_content .= "-----------\n";
        $email_content .= "Detailed itinerary will be provided upon confirmation.\n\n";
    }
}

// transport_details - only show if selected
if ($transport_count > 0) {
    $email_content .= "🚖  *Transportation*\n";
    $email_content .= "-----------\n";
    $email_content .= $transport_details . "\n";
}

// Inclusion/Exclusion - only show if selected
if (in_array('inclusion_exclusion', $options)) {
    error_log("Processing inclusions/exclusions...");
    error_log("Inclusions data: " . (isset($sq_quotation['inclusions']) ? $sq_quotation['inclusions'] : 'NOT SET'));
    error_log("Exclusions data: " . (isset($sq_quotation['exclusions']) ? $sq_quotation['exclusions'] : 'NOT SET'));
    
    // Inclusions
    $email_content .= "✅  *Inclusions*\n";
    $email_content .= "-----------\n";
    if (!empty($sq_quotation['inclusions'])) {
        // Remove HTML tags and clean up the text
        $inclusions = $sq_quotation['inclusions'];
        $inclusions = strip_tags($inclusions);
        $inclusions = html_entity_decode($inclusions, ENT_QUOTES, 'UTF-8');
        $inclusions = preg_replace('/\s+/', ' ', $inclusions); // Replace multiple spaces with single space
        $inclusions = trim($inclusions);
        
        // Split by common separators and format as bullet points
        $inclusions_list = preg_split('/(\.|;|,)/', $inclusions);
        foreach ($inclusions_list as $inclusion) {
            $inclusion = trim($inclusion);
            // Only include meaningful items (at least 10 characters)
            if (!empty($inclusion) && strlen($inclusion) > 10) {
                $email_content .= "• " . $inclusion . "\n";
            }
        }
        $email_content .= "\n";
    } else {
        $email_content .= "Inclusions will be provided upon confirmation.\n\n";
    }

    // Exclusions
    $email_content .= "❌  *Exclusions*\n";
    $email_content .= "-----------\n";
    if (!empty($sq_quotation['exclusions'])) {
        // Remove HTML tags and clean up the text
        $exclusions = $sq_quotation['exclusions'];
        $exclusions = strip_tags($exclusions);
        $exclusions = html_entity_decode($exclusions, ENT_QUOTES, 'UTF-8');
        $exclusions = preg_replace('/\s+/', ' ', $exclusions); // Replace multiple spaces with single space
        $exclusions = trim($exclusions);
        
        // Split by common separators and format as bullet points
        $exclusions_list = preg_split('/(\.|;|,)/', $exclusions);
        foreach ($exclusions_list as $exclusion) {
            $exclusion = trim($exclusion);
            // Only include meaningful items (at least 10 characters)
            if (!empty($exclusion) && strlen($exclusion) > 10) {
                $email_content .= "• " . $exclusion . "\n";
            }
        }
        $email_content .= "\n";
    } else {
        $email_content .= "Exclusions will be provided upon confirmation.\n\n";
    }
} else {
    error_log("Inclusions/Exclusions NOT selected in options");
}



// Terms & Conditions - only show if selected
if (in_array('terms_conditions', $options)) {
    error_log("Processing terms and conditions...");
    error_log("Terms data: " . (isset($terms_and_conditions_details) ? substr($terms_and_conditions_details, 0, 200) . '...' : 'NOT SET'));
    
    $email_content .= "📌 *TERMS AND CONDITIONS*\n";
    $email_content .= "-----------\n";
    if (!empty($terms_and_conditions_details)) {
        // Format terms and conditions as a proper list
        $terms = $terms_and_conditions_details;
        
        // Remove all HTML tags and clean up the text
        $terms = strip_tags($terms);
        $terms = html_entity_decode($terms, ENT_QUOTES, 'UTF-8');
        $terms = preg_replace('/\s+/', ' ', $terms); // Replace multiple spaces with single space
        $terms = trim($terms);
        
        // Split by common separators and format as bullet points
        $terms_list = preg_split('/(\.|;|,)/', $terms);
        foreach ($terms_list as $term) {
            $term = trim($term);
            if (!empty($term) && strlen($term) > 10) { // Only include meaningful terms
                $email_content .= "• " . $term . "\n";
            }
        }
        $email_content .= "\n";
    } else {
        $email_content .= "Standard terms and conditions apply. Details will be provided upon confirmation.\n";
    }
} else {
    error_log("Terms and conditions NOT selected in options");
}



// Generate quotation link
$quotation_encoded = base64_encode($quotation_id);
$quotation_link = BASE_URL . "model/package_tour/quotation/single_quotation.php?quotation={$quotation_encoded}";

$email_content .= "\n*Link* : {$quotation_link}\n\n";
$email_content .= "Please contact for more details : ITOURS LLP PVT LTDS +919168425999\n";
$email_content .= "Thank you.";

// If WhatsApp is requested, format content for WhatsApp
if ($email_option == 'WhatsApp') {
    // Format content for WhatsApp (remove HTML tags, use * for bold)
    $email_content = str_replace(['<br>', '<br/>', '<br />'], "\n", $email_content);
    $email_content = strip_tags($email_content);
    $email_content = str_replace(['<b>', '</b>', '<strong>', '</strong>'], '*', $email_content);
    $email_content = str_replace(['<i>', '</i>', '<em>', '</em>'], '_', $email_content);
    $email_content = preg_replace('/\*+/', '*', $email_content); // Remove multiple asterisks
    $email_content = preg_replace('/\n\s*\n/', "\n\n", $email_content); // Clean up multiple newlines
}

echo $email_content;
?>
