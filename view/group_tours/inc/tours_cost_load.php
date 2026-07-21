<?php
include '../../../config.php';

$adult_count = (int)$_POST['adult_count'];
$child_wobed = (int)$_POST['child_wobed'];
$child_wibed = (int)$_POST['child_wibed'];
$extra_bed_count = (int)$_POST['extra_bed_c'];
$infant_count1 = (int)$_POST['infant_c'];
$package_id = (int)$_POST['package_id'];

$all_costs_array = array();
global $currency;
$s_currency = $_SESSION['session_currency_id'];

$sq_to = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$s_currency'"));
$to_currency_rate = $sq_to['currency_rate'];

$sq_from = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$currency_id'"));
$from_currency_rate = $sq_from['currency_rate'];

$sq_curr_symbol = mysqli_fetch_assoc(mysqlQuery("select default_currency from currency_name_master where id='$s_currency'"));
$curr_symbol = $sq_curr_symbol['default_currency'];

$sq_tariff = mysqlQuery("SELECT * FROM tour_master WHERE `tour_id` = '$package_id'");

if (mysqli_num_rows($sq_tariff) > 0) {
    while ($row_tariff = mysqli_fetch_assoc($sq_tariff)) {

        // Calculate costs per person
        $adult_cost_per_person = ($adult_count > 0 && $adult_count == 1) ? $row_tariff['single_person_cost'] : (float)($row_tariff['adult_cost']);
        $child_without_bed_per_person = $child_wobed > 0 ? (float)($row_tariff['child_without_cost']) : 0;
        $child_with_bed_per_person = $child_wibed > 0 ? (float)($row_tariff['child_with_cost']) : 0;
        $extra_bed_per_person = $extra_bed_count > 0 ? (float)($row_tariff['with_bed_cost']) : 0;
        $infant_per_person = $infant_count1 > 0 ? (float)($row_tariff['infant_cost']) : 0;
        //Currency conversions

        $adult_cost_per_person = ($to_currency_rate != '') ? $to_currency_rate * $adult_cost_per_person : 0;
        $child_without_bed_per_person = ($to_currency_rate != '') ? $to_currency_rate * $child_without_bed_per_person : 0;
        $child_with_bed_per_person = ($to_currency_rate != '') ? $to_currency_rate * $child_with_bed_per_person : 0;
        $infant_per_person = ($to_currency_rate != '') ? $to_currency_rate * $infant_per_person : 0;
        $extra_bed_per_person = ($to_currency_rate != '') ? $to_currency_rate * $extra_bed_per_person : 0;

        // Add Per Adult cost
        if ($adult_count > 0) {
            $adult_cost = $adult_count * $adult_cost_per_person;
            array_push($all_costs_array, array(
                'type' => 'Adult(PP)',
                // 'cost' => $adult_cost,
                'per_person' => $curr_symbol . ' ' . number_format($adult_cost_per_person, 2)
            ));
        }
        // Add other costs
        if ($child_wobed > 0) {
            array_push($all_costs_array, array(
                'type' => 'Child Without Bed(PP)',
                // 'cost' => $child_wobed_cost,
                'per_person' => $curr_symbol . ' ' . number_format($child_without_bed_per_person, 2)
            ));
        }
        if ($child_wibed > 0) {
            array_push($all_costs_array, array(
                'type' => 'Child With Bed(PP)',
                // 'cost' => $child_wibed_cost,
                'per_person' => $curr_symbol . ' ' . number_format($child_with_bed_per_person, 2)
            ));
        }
        if ($infant_count1 > 0) {
            array_push($all_costs_array, array(
                'type' => 'Infant(PP)',
                // 'cost' => $infant_cost,
                'per_person' => $curr_symbol . ' ' . number_format($infant_per_person, 2)
            ));
        }
        if ($extra_bed_count > 0) {
            array_push($all_costs_array, array(
                'type' => 'Extra Bed',
                // 'cost' => $extra_bed_cost,
                'per_person' => $curr_symbol . ' ' . number_format($extra_bed_per_person, 2)
            ));
        }

        // Calculate Gross Salary and include per person
        $total_cost1 = ($adult_count * $adult_cost_per_person) +
            ($child_wobed * $child_without_bed_per_person) +
            ($child_wibed * $child_with_bed_per_person) +
            ($extra_bed_count * $extra_bed_per_person) +
            ($infant_count1 * $infant_per_person);

        // $total_cost = ($to_currency_rate != '') ? $to_currency_rate * $total_cost1 : 0;
        array_push($all_costs_array, array(
            'type' => 'Total Cost',
            // 'cost' => $total_cost1,
            'per_person' => $curr_symbol . ' ' . number_format($total_cost1, 2)
        ));
    }
} else {
    // No package found
    $all_costs_array[] = array(
        'type' => 'Error',
        'cost' => 'Tour not found.',
        'per_person' => 0
    );
}

// Output as JSON
echo json_encode($all_costs_array);
