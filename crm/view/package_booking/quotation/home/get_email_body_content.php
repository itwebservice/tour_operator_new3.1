<?php
include "../../../../model/model.php";
include_once __DIR__ . '/../../../../model/package_tour/quotation/quotation_rich_text_helpers.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$quotation_id = $_POST['quotation_id'];
$email_option = $_POST['email_option'];
$options = isset($_POST['options']) ? (array)$_POST['options'] : array('price_structure', 'inclusion_exclusion', 'terms_conditions', 'itinerary');
$sectioned = isset($_POST['sectioned']) && $_POST['sectioned'] == '1';

// Debug information
error_log("Quotation ID: " . $quotation_id);
error_log("Email Option: " . $email_option);
error_log("Selected Options: " . print_r($options, true));

// Get quotation details
$sq_quotation = mysqli_fetch_assoc(mysqlQuery("SELECT *, 
	COALESCE(is_sub_quotation, '0') as is_sub_quotation,
	COALESCE(parent_quotation_id, '0') as parent_quotation_id,
	COALESCE(quotation_id_display, '') as quotation_display_id
	FROM package_tour_quotation_master WHERE quotation_id = '$quotation_id'"));
$sq_package = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM custom_package_master WHERE package_id = '".get_quotation_package_lookup_id($sq_quotation)."'"));

// Get costing details
$sq_cost = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM package_tour_quotation_costing_entries WHERE quotation_id = '$quotation_id' ORDER BY sort_order LIMIT 1"));
if (!is_array($sq_cost)) {
    $sq_cost = array();
}

// Calculate costs (cast empty strings to 0 for PHP 8+ number_format)
$basic_cost = (float) (isset($sq_cost['basic_amount']) ? $sq_cost['basic_amount'] : 0);
$service_charge = (float) (isset($sq_cost['service_charge']) ? $sq_cost['service_charge'] : 0);
$service_tax_amount = 0.0;
$act_discount = 0.0;
$tcsper = 0.0;
$tcsvalue = 0.0;

$bsmValues = json_decode(isset($sq_cost['bsmValues']) ? $sq_cost['bsmValues'] : '', true);
$discount_in = isset($sq_cost['discount_in']) ? $sq_cost['discount_in'] : '';
$discount = (float) (isset($sq_cost['discount']) ? $sq_cost['discount'] : 0);

if ($discount_in == 'Percentage') {
    $act_discount = $service_charge * $discount / 100;
} else {
    $act_discount = ($service_charge != 0) ? $discount : 0.0;
}

$service_charge = $service_charge - $act_discount;

// Calculate service tax
$name = '';
$tax_subtotal = isset($sq_cost['service_tax_subtotal']) ? $sq_cost['service_tax_subtotal'] : '';
if ($tax_subtotal !== 0.00 && $tax_subtotal !== '' && $tax_subtotal !== null) {
    $service_tax_subtotal1 = explode(',', (string) $tax_subtotal);
    for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
        $service_tax = explode(':', $service_tax_subtotal1[$i]);
        $service_tax_amount += (float) (isset($service_tax[2]) ? $service_tax[2] : 0);
        $name .= (isset($service_tax[0]) ? $service_tax[0] : '') . ' ';
        $percent = isset($service_tax[1]) ? $service_tax[1] : '';
    }
}

// Calculate TCS
if (is_array($bsmValues) && isset($bsmValues[0]['tcsper']) && $bsmValues[0]['tcsper'] != 'NaN' && $bsmValues[0]['tcsper'] !== '') {
    $tcsper = (float) $bsmValues[0]['tcsper'];
    $tcsvalue = (float) (isset($bsmValues[0]['tcsvalue']) && $bsmValues[0]['tcsvalue'] !== '' && $bsmValues[0]['tcsvalue'] !== 'NaN'
        ? $bsmValues[0]['tcsvalue'] : 0);
}

$train_cost = (float) (isset($sq_quotation['train_cost']) ? $sq_quotation['train_cost'] : 0);
$cruise_cost = (float) (isset($sq_quotation['cruise_cost']) ? $sq_quotation['cruise_cost'] : 0);
$flight_cost = (float) (isset($sq_quotation['flight_cost']) ? $sq_quotation['flight_cost'] : 0);
$visa_cost = (float) (isset($sq_quotation['visa_cost']) ? $sq_quotation['visa_cost'] : 0);
$guide_cost = (float) (isset($sq_quotation['guide_cost']) ? $sq_quotation['guide_cost'] : 0);
$misc_cost = (float) (isset($sq_quotation['misc_cost']) ? $sq_quotation['misc_cost'] : 0);

// Calculate total costs
$quotation_cost = (float) ($basic_cost + $service_charge + $service_tax_amount + $train_cost + $cruise_cost + $flight_cost + $visa_cost + $guide_cost + $misc_cost + $tcsvalue - $act_discount);
$travel_cost = (float) ($train_cost + $flight_cost + $cruise_cost + $visa_cost + $guide_cost + $misc_cost);

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

// Debug information
error_log("Price Structure selected: " . (in_array('price_structure', $options) ? 'YES' : 'NO'));
error_log("Inclusions/Exclusions selected: " . (in_array('inclusion_exclusion', $options) ? 'YES' : 'NO'));
error_log("Terms & Conditions selected: " . (in_array('terms_conditions', $options) ? 'YES' : 'NO'));
error_log("Itinerary selected: " . (in_array('itinerary', $options) ? 'YES' : 'NO'));
error_log("Hotel count: " . $hotel_count);
error_log("Transport count: " . $transport_count);
error_log("Itinerary count: " . $itinerary_count);

// Calculate duration
$from_date_obj = new DateTime($sq_quotation['from_date']);
$to_date_obj = new DateTime($sq_quotation['to_date']);
$duration = $from_date_obj->diff($to_date_obj)->days;

// Get hotel details (resolve IDs to names)
global $similar_text, $app_name, $theme_color, $app_contact_no;
$hotel_details = '';
$hotel_html_lines = array();
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
    $similar_label = isset($similar_text) ? $similar_text : ' / Similar';

    $hotel_details .= trim($city_display) . ' -' . trim($hotel_display) . $similar_label
      . ' - ' . trim($room_category_display) . ' -' . trim($meal_plan_display) . "\n";
    $hotel_html_lines[] = trim($city_display) . ' - ' . trim($hotel_display) . $similar_label
      . ' - ' . trim($room_category_display) . ' - ' . trim($meal_plan_display);
    $hotel_count++;
}

error_log("Hotel count: " . $hotel_count);

// Get itinerary details
$itinerary_details = '';
$itinerary_html_blocks = array();
$sq_package_program = mysqlQuery("SELECT * FROM package_quotation_program WHERE quotation_id = '$quotation_id'");
$count = 1;
$j = 0;
$itinerary_count = 0;

if (mysqli_num_rows($sq_package_program) > 0) {
    $itinerary_details = "\n 📅 *Itinerary*\n-----------\n";
    while ($row_itinerary = mysqli_fetch_assoc($sq_package_program)) {
    	$itinerary_details .= "*Day - {$count}*   \n" .
					"" . htmlspecialchars($row_itinerary['attraction']) . "     \n " .
					"(" . htmlspecialchars($row_itinerary['stay']) . ")     \n" .
					"(" . htmlspecialchars($row_itinerary['meal_plan']) . ")\n";
        $itinerary_html_blocks[] = array(
            'day' => $count,
            'attraction' => $row_itinerary['attraction'],
            'stay' => $row_itinerary['stay'],
            'meal_plan' => $row_itinerary['meal_plan'],
            'program' => $row_itinerary['day_wise_program'],
        );
        $count++;
        $j++;
        $itinerary_count++;
    }
}

error_log("Itinerary count: " . $itinerary_count);

// Get transportation details
$transport_details = '';
$transport_html_lines = array();
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
    $transport_html_lines[] = $vehicle_name . ' | ' . $from_date_trans . ' - ' . $to_date_trans . ' | ' . $pickup . ' to ' . $drop . ' | ' . $service_duration . ' (' . $row_transport['vehicle_count'] . ')';
    $transport_count++;
}



$terms_and_conditions_details = '';

$sq_terms_and_conditions = mysqlQuery("SELECT * FROM terms_and_conditions WHERE type='Package Quotation' AND active_flag='Active' LIMIT 1");

if ($sq_terms_and_conditions && mysqli_num_rows($sq_terms_and_conditions) > 0) {
    $row_terms = mysqli_fetch_assoc($sq_terms_and_conditions);
    $terms_and_conditions_details = $row_terms['terms_and_conditions'] ?? '';
}

// Generate email body content - header (always included)
$header_content = "Hi Guest,\n\n";
$header_content .= "Greetings from ITOURS LLP PVT LTDS\n\n";
$header_content .= "Thank you for your query with us. As per your requirements, following are the package details.\n";
$header_content .= "*Quotation ID :* {$quotation_id_display} \n\n";
$header_content .= "*{$sq_package['package_name']}*\n";
$header_content .= "* {$from_date} for {$duration} Nights, " . ($duration + 1) . " Days\n";
$header_content .= "* {$sq_quotation['total_adult']} Adults\n";
$header_content .= "* " . ($sq_quotation['children_with_bed'] + $sq_quotation['children_without_bed']) . " Child\n";
$header_content .= "* {$sq_quotation['total_infant']} Infant\n";
$header_content .= "               \n";

// Price Structure section (Group = Tour/Travel/Tax/Total; PP = Adult PP / Discount / Tax / Total)
$price_section = '';
if (isset($sq_quotation['costing_type']) && (int) $sq_quotation['costing_type'] === 2) {
    include_once __DIR__ . '/../../../../model/app_settings/print_html/quotation_html/pp_costing_doc_block.php';
    if (function_exists('gqd_render_pp_costing_whatsapp_text')) {
        $price_section = gqd_render_pp_costing_whatsapp_text($quotation_id, array('first_only' => true));
    }
}
if ($price_section === '') {
    $price_section = "*Tour Amount :* INR " . number_format($quotation_cost - $travel_cost, 2) . "\n";
    $price_section .= "*Travel Amount :* INR " . number_format($travel_cost, 2) . "\n";
    $price_section .= "*Tax :* INR " . number_format($service_tax_amount, 2) . "\n";
    $price_section .= "*Tcs :* INR " . number_format($tcsvalue, 2) . "\n";
    $price_section .= "*Total Price :*  INR " . number_format($quotation_cost, 2) . " \n\n";
}

// Hotels + Itinerary + Transportation section
$itinerary_section = '';
if ($hotel_count > 0) {
    $itinerary_section .= "🏨  *Hotels*\n";
    $itinerary_section .= "-----------\n";
    $itinerary_section .= rtrim($hotel_details) . "\n";
    $itinerary_section .= "-----------\n\n";
} elseif (!empty($hotel_details)) {
    $itinerary_section .= "🏨  *Hotels*\n";
    $itinerary_section .= "-----------\n";
    $itinerary_section .= "Hotel details will be provided upon confirmation.\n";
    $itinerary_section .= "-----------\n\n";
}

if ($itinerary_count > 0) {
    $itinerary_section .= "-----------\n";
    $itinerary_section .= $itinerary_details . "\n";
} else {
    $itinerary_section .= "-----------\n";
    $itinerary_section .= "📅 *Itinerary*\n";
    $itinerary_section .= "-----------\n";
    $itinerary_section .= "Detailed itinerary will be provided upon confirmation.\n\n";
}

if ($transport_count > 0) {
    $itinerary_section .= "🚖  *Transportation*\n";
    $itinerary_section .= "-----------\n";
    $itinerary_section .= $transport_details . "\n";
}

// Inclusion/Exclusion section
$inclusion_section = "✅  *Inclusions*\n";
$inclusion_section .= "-----------\n";
if (!empty($sq_quotation['inclusions'])) {
    $inclusion_section .= quotation_rich_text_to_whatsapp($sq_quotation['inclusions']) . "\n\n";
} else {
    $inclusion_section .= "Inclusions will be provided upon confirmation.\n\n";
}

$inclusion_section .= "❌  *Exclusions*\n";
$inclusion_section .= "-----------\n";
if (!empty($sq_quotation['exclusions'])) {
    $inclusion_section .= quotation_rich_text_to_whatsapp($sq_quotation['exclusions']) . "\n\n";
} else {
    $inclusion_section .= "Exclusions will be provided upon confirmation.\n\n";
}

// Terms & Conditions section
$terms_section = "📌 *TERMS AND CONDITIONS*\n";
$terms_section .= "-----------\n";
if (!empty($terms_and_conditions_details)) {
    $terms = $terms_and_conditions_details;
    $terms = strip_tags($terms);
    $terms = html_entity_decode($terms, ENT_QUOTES, 'UTF-8');
    $terms = preg_replace('/\s+/', ' ', $terms);
    $terms = trim($terms);

    $terms_list = preg_split('/(\.|;|,)/', $terms);
    foreach ($terms_list as $term) {
        $term = trim($term);
        if (!empty($term) && strlen($term) > 10) {
            $terms_section .= "• " . $term . "\n";
        }
    }
    $terms_section .= "\n";
} else {
    $terms_section .= "Standard terms and conditions apply. Details will be provided upon confirmation.\n";
}

// Footer (always included)
$quotation_encoded = base64_encode($quotation_id);
$quotation_link = BASE_URL . "model/package_tour/quotation/single_quotation.php?quotation={$quotation_encoded}";

$footer_content = "\n*Link* : {$quotation_link}\n\n";
$footer_content .= "Please contact for more details : " . (!empty($app_name) ? $app_name : 'ITOURS LLP PVT LTDS') . " " . (!empty($app_contact_no) ? $app_contact_no : '+919168425999') . "\n";
$footer_content .= "Thank you.";

$use_styled_html = ($email_option != 'WhatsApp');

if ($use_styled_html) {
    include_once __DIR__ . '/../../../../model/package_tour/quotation/quotation_email_pdf_style_renderer.php';
    $pdf_style_html = render_quotation_email_pdf_style($quotation_id, $options, $sectioned, $quotation_link);
    if ($pdf_style_html !== '') {
        echo $pdf_style_html;
        exit;
    }

    include __DIR__ . '/inc/quotation_email_html_helpers.php';

    $display_app_name = !empty($app_name) ? $app_name : 'ITOURS LLP PVT LTDS';
    $display_contact = !empty($app_contact_no) ? $app_contact_no : '+919168425999';
    $accent = qeh_accent_color();

    $header_html = qeh_greeting_block($display_app_name);
    $header_html .= qeh_section_heading('Package Tour Details');
    $header_html .= qeh_kv_table(array(
        array('Package Name', qeh_esc($sq_package['package_name'])),
        array('Duration', qeh_esc($sq_quotation['total_days'] . 'N/' . ($sq_quotation['total_days'] + 1) . 'D')),
        array('Travel Date', qeh_esc($from_date . ' To ' . $to_date)),
        array('Quotation ID', qeh_esc($quotation_id_display)),
    ));
    $header_html .= qeh_section_heading('Guest Details');
    $header_html .= qeh_kv_table(array(
        array('Adult(s)', qeh_esc($sq_quotation['total_adult'])),
        array('Child With Bed', qeh_esc($sq_quotation['children_with_bed'])),
        array('Child Without Bed', qeh_esc($sq_quotation['children_without_bed'])),
        array('Infant(s)', qeh_esc($sq_quotation['total_infant'])),
        array('Total', qeh_esc($sq_quotation['total_passangers'])),
    ));

    $price_html = qeh_section_heading('Costing Details');
    $price_html .= qeh_kv_table(array(
        array('Tour Amount', 'INR ' . number_format($quotation_cost - $travel_cost, 2)),
        array('Travel Amount', 'INR ' . number_format($travel_cost, 2)),
        array('Tax', 'INR ' . number_format($service_tax_amount, 2)),
        array('TCS', 'INR ' . number_format($tcsvalue, 2)),
        array('Total Price', '<strong style="color:' . $accent . ';">INR ' . number_format($quotation_cost, 2) . '</strong>'),
    ));

    $itinerary_html = '';
    if ($hotel_count > 0) {
        $hotel_list = '<ul style="margin:0;padding:8px 8px 8px 24px;color:#555;">';
        foreach ($hotel_html_lines as $hotel_line) {
            $hotel_list .= '<li style="margin-bottom:6px;">' . qeh_esc($hotel_line) . '</li>';
        }
        $hotel_list .= '</ul>';
        $itinerary_html .= qeh_section_heading('Accommodation Details') . qeh_kv_table(array(array('full' => $hotel_list)));
    }

    if ($itinerary_count > 0) {
        $itinerary_html .= qeh_section_heading('Tour Itinerary');
        foreach ($itinerary_html_blocks as $day) {
            $day_body = '<p style="margin:0 0 6px;"><strong>Day ' . qeh_esc($day['day']) . '</strong> &mdash; ' . qeh_esc($day['attraction']) . '</p>';
            if (!empty($day['program'])) {
                $day_body .= '<div style="margin:0 0 8px;color:#555;">' . $day['program'] . '</div>';
            }
            $day_body .= '<p style="margin:0;font-size:13px;color:#666;"><strong>Overnight:</strong> ' . qeh_esc($day['stay']) . ' &nbsp;|&nbsp; <strong>Meal Plan:</strong> ' . qeh_esc($day['meal_plan']) . '</p>';
            $itinerary_html .= qeh_kv_table(array(array('full' => $day_body)));
        }
    } elseif ($hotel_count === 0) {
        $itinerary_html .= qeh_rich_block('Tour Itinerary', 'Detailed itinerary will be provided upon confirmation.');
    }

    if ($transport_count > 0) {
        $transport_list = '<ul style="margin:0;padding:8px 8px 8px 24px;color:#555;">';
        foreach ($transport_html_lines as $transport_line) {
            $transport_list .= '<li style="margin-bottom:6px;">' . qeh_esc($transport_line) . '</li>';
        }
        $transport_list .= '</ul>';
        $itinerary_html .= qeh_section_heading('Transportation') . qeh_kv_table(array(array('full' => $transport_list)));
    }

    $inclusion_html = qeh_rich_block('Inclusions', !empty($sq_quotation['inclusions']) ? $sq_quotation['inclusions'] : '<span style="color:#777;">Inclusions will be provided upon confirmation.</span>');
    $inclusion_html .= qeh_rich_block('Exclusions', !empty($sq_quotation['exclusions']) ? $sq_quotation['exclusions'] : '<span style="color:#777;">Exclusions will be provided upon confirmation.</span>');

    $terms_html = qeh_rich_block('Terms and Conditions', !empty($terms_and_conditions_details) ? $terms_and_conditions_details : 'Standard terms and conditions apply. Details will be provided upon confirmation.');

    $footer_html = qeh_section_heading('Quotation Link');
    $footer_html .= qeh_kv_table(array(
        array('View Online', '<a href="' . qeh_esc($quotation_link) . '" style="color:' . $accent . ';text-decoration:none;font-weight:600;">View Quotation</a>'),
        array('Contact', qeh_esc($display_app_name . ' ' . $display_contact)),
    ));
    $footer_html .= '<p style="margin:12px 0 0;color:#555;">Thank you.</p>';

    if ($sectioned) {
        $email_content = '<div class="quotation-email-styled-preview">';
        $email_content .= qeh_wrap_preview_section('header', $header_html);
        $email_content .= qeh_wrap_preview_section('price_structure', $price_html);
        $email_content .= qeh_wrap_preview_section('itinerary', $itinerary_html);
        $email_content .= qeh_wrap_preview_section('inclusion_exclusion', $inclusion_html);
        $email_content .= qeh_wrap_preview_section('terms_conditions', $terms_html);
        $email_content .= qeh_wrap_preview_section('footer', $footer_html);
        $email_content .= '</div>';
    } else {
        $email_content = '<div class="quotation-email-styled-preview">' . $header_html;
        if (in_array('price_structure', $options)) {
            $email_content .= $price_html;
        }
        if (in_array('itinerary', $options)) {
            $email_content .= $itinerary_html;
        }
        if (in_array('inclusion_exclusion', $options)) {
            $email_content .= $inclusion_html;
        }
        if (in_array('terms_conditions', $options)) {
            $email_content .= $terms_html;
        }
        $email_content .= $footer_html . '</div>';
    }
} else {
    function format_preview_section_html($section_key, $content) {
        if (trim($content) === '') {
            return '';
        }
        $content = str_replace(array("\r\n", "\r"), "\n", $content);
        $formatted = nl2br($content, false);
        return '<div class="preview-section-block" data-section="' . $section_key . '">' . $formatted . '</div>';
    }

    if ($sectioned) {
        $email_content = '<div class="preview-section-block preview-section-header" data-section="header">' . nl2br($header_content, false) . '</div>';
        $email_content .= format_preview_section_html('price_structure', $price_section);
        $email_content .= format_preview_section_html('itinerary', $itinerary_section);
        $email_content .= format_preview_section_html('inclusion_exclusion', $inclusion_section);
        $email_content .= format_preview_section_html('terms_conditions', $terms_section);
        $email_content .= '<div class="preview-section-block preview-section-footer" data-section="footer">' . nl2br($footer_content, false) . '</div>';
    } else {
        $email_content = $header_content;

        if (in_array('price_structure', $options)) {
            $email_content .= $price_section;
        }
        if (in_array('itinerary', $options)) {
            $email_content .= $itinerary_section;
        }
        if (in_array('inclusion_exclusion', $options)) {
            $email_content .= $inclusion_section;
        }
        if (in_array('terms_conditions', $options)) {
            $email_content .= $terms_section;
        }

        $email_content .= $footer_content;
    }

    if ($email_option == 'WhatsApp' && !$sectioned) {
        error_log("Formatting for WhatsApp");
        $email_content = str_replace(['<br>', '<br/>', '<br />'], "\n", $email_content);
        $email_content = strip_tags($email_content);
        $email_content = str_replace(['<b>', '</b>', '<strong>', '</strong>'], '*', $email_content);
        $email_content = str_replace(['<i>', '</i>', '<em>', '</em>'], '_', $email_content);
        $email_content = preg_replace('/\*+/', '*', $email_content);
        $email_content = preg_replace('/\n\s*\n/', "\n\n", $email_content);
    }
}

echo $email_content;
?>
