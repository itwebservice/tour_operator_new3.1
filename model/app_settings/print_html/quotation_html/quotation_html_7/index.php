<?php
/**
 * ============================================================================
 * EXAMPLE TEMPLATE (quotation_html_7) - built on the generic data engine.
 * ----------------------------------------------------------------------------
 * This is a REFERENCE for the frontend developer. Notice there is NOT a single
 * database query here: the whole quotation is fetched with ONE function call.
 * To create another design (quotation_html_8, _9 ...) just copy this folder and
 * restyle the HTML below - the data layer never changes.
 *
 * Open in browser:
 *   .../crm/model/app_settings/print_html/quotation_html/quotation_html_7/index.php?quotation_id=1
 * ============================================================================
 */

// 1) Bootstrap the app (DB connection + helper functions).
include "../../../../model.php";

// 2) Load the generic data engine (lives one folder up).
include_once "../generic_quotation_data.php";

// 3) Get everything for this quotation in a single call.
$quotation_id = isset($_GET['quotation_id']) ? $_GET['quotation_id'] : 0;
$q = get_generic_quotation_data($quotation_id);

if (empty($q['found'])) {
    echo "Quotation not found.";
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($q['hero']['tour_name']) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/bootstrap.min.css">
    <script src="<?= BASE_URL ?>js/jquery-3.1.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .page { padding: 30px; }
        h2.section { border-bottom: 2px solid #16a085; padding-bottom: 6px; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; font-size: 13px; text-align: left; }
        th { background: #16a085; color: #fff; }
        .hero { text-align: center; }
        .hero img { max-width: 100%; }
    </style>
</head>

<body>
    <div class="page">

        <!-- ============ HERO ============ -->
        <div class="hero">
            <?php if ($q['hero']['cover_image']) { ?><img src="<?= $q['hero']['cover_image'] ?>"><?php } ?>
            <h1><?= htmlspecialchars($q['hero']['tour_name']) ?></h1>
            <p>
                Quotation: <strong><?= htmlspecialchars($q['hero']['quotation_code']) ?></strong> |
                <?= htmlspecialchars($q['hero']['duration_label']) ?> |
                Prepared for <strong><?= htmlspecialchars($q['hero']['client_name']) ?></strong>
            </p>
            <p><?= htmlspecialchars($q['hero']['company_name']) ?></p>
        </div>

        <!-- ============ TOUR OVERVIEW ============ -->
        <h2 class="section">Tour Overview</h2>
        <table>
            <tr><th>Destination</th><td><?= htmlspecialchars($q['tour_overview']['destination']) ?></td>
                <th>Quotation Date</th><td><?= htmlspecialchars($q['tour_overview']['quotation_date']) ?></td></tr>
            <tr><th>Customer Email</th><td><?= htmlspecialchars($q['tour_overview']['customer_email']) ?></td>
                <th>Customer Mobile</th><td><?= htmlspecialchars($q['tour_overview']['customer_mobile']) ?></td></tr>
            <tr><th>Duration</th><td><?= htmlspecialchars($q['tour_overview']['duration_label']) ?></td>
                <th>Guests</th><td><?= htmlspecialchars($q['tour_overview']['guest_count']) ?></td></tr>
        </table>

        <!-- ============ HOTELS (LOOP) ============ -->
        <?php if ($q['sections_present']['hotels']) { ?>
            <h2 class="section">Hotels (<?= $q['counts']['hotels'] ?>)</h2>
            <table>
                <thead>
                    <tr><th>City</th><th>Hotel</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Rating</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($q['hotels'] as $h) { ?>
                        <tr>
                            <td><?= htmlspecialchars($h['hotel_city']) ?></td>
                            <td><?= htmlspecialchars($h['hotel_name']) ?></td>
                            <td><?= htmlspecialchars($h['room_category']) ?></td>
                            <td><?= htmlspecialchars($h['check_in']) ?></td>
                            <td><?= htmlspecialchars($h['check_out']) ?></td>
                            <td><?= htmlspecialchars($h['rating']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>

        <!-- ============ FLIGHTS (LOOP) ============ -->
        <?php if ($q['sections_present']['flights']) { ?>
            <h2 class="section">Flights (<?= $q['counts']['flights'] ?>)</h2>
            <table>
                <thead><tr><th>Airline</th><th>Class</th><th>From</th><th>To</th><th>Departure</th><th>Arrival</th></tr></thead>
                <tbody>
                    <?php foreach ($q['flights'] as $f) { ?>
                        <tr>
                            <td><?= htmlspecialchars($f['airline_display']) ?></td>
                            <td><?= htmlspecialchars($f['class']) ?></td>
                            <td><?= htmlspecialchars($f['from_city']) ?></td>
                            <td><?= htmlspecialchars($f['to_city']) ?></td>
                            <td><?= htmlspecialchars($f['departure_datetime']) ?></td>
                            <td><?= htmlspecialchars($f['arrival_datetime']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>

        <!-- ============ ITINERARY (LOOP) ============ -->
        <?php if ($q['sections_present']['itinerary']) { ?>
            <h2 class="section">Itinerary (<?= $q['counts']['itinerary'] ?> days)</h2>
            <?php foreach ($q['itinerary'] as $day) { ?>
                <div style="border:1px solid #eee; padding:10px; margin-bottom:10px;">
                    <h4>Day <?= htmlspecialchars($day['day_number']) ?> (<?= htmlspecialchars($day['date']) ?>) - <?= htmlspecialchars($day['special_attraction']) ?></h4>
                    <?php if ($day['image']) { ?><img src="<?= $day['image'] ?>" style="max-width:200px;"><?php } ?>
                    <p><?= $day['detailed_programme'] ?></p>
                    <small>Stay: <?= htmlspecialchars($day['overnight_stay']) ?> | Meals: <?= htmlspecialchars($day['meal_plan']) ?></small>
                </div>
            <?php } ?>
        <?php } ?>

        <!-- ============ INCLUSIONS / EXCLUSIONS ============ -->
        <?php if ($q['inclusion_exclusion']['included']) { ?>
            <h2 class="section">Inclusions</h2>
            <div><?= $q['inclusion_exclusion']['included'] ?></div>
        <?php } ?>
        <?php if ($q['inclusion_exclusion']['excluded']) { ?>
            <h2 class="section">Exclusions</h2>
            <div><?= $q['inclusion_exclusion']['excluded'] ?></div>
        <?php } ?>

        <!-- ============ COSTING (use the COMPUTED, ready-to-print values) ============ -->
        <h2 class="section">Costing (<?= htmlspecialchars($q['costing']['costing_type_label']) ?>)</h2>
        <?php if ($q['costing']['costing_type'] == 1) { // Group ?>
            <table>
                <thead><tr><th>Package</th><th>Tour Cost</th><th>Tax</th><th>TCS</th><th>Travel</th><th>Total</th></tr></thead>
                <tbody>
                    <?php foreach ($q['costing']['computed']['group'] as $g) { ?>
                        <tr>
                            <td><?= htmlspecialchars($g['package_type']) ?></td>
                            <td><?= htmlspecialchars($g['tour_cost_display']) ?></td>
                            <td><?= htmlspecialchars($g['tax_display']) ?></td>
                            <td><?= htmlspecialchars($g['tcs_display']) ?></td>
                            <td><?= htmlspecialchars($g['travel_display']) ?></td>
                            <td><?= htmlspecialchars($g['total_display']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { // Per person ?>
            <table>
                <thead><tr><th>Package</th><th>Adult</th><th>CWB</th><th>CWNB</th><th>Infant</th><th>Grand Total</th></tr></thead>
                <tbody>
                    <?php foreach ($q['costing']['computed']['per_person'] as $p) { ?>
                        <tr>
                            <td><?= htmlspecialchars($p['package_type']) ?></td>
                            <td><?= htmlspecialchars($p['pp_adult_display']) ?></td>
                            <td><?= htmlspecialchars($p['pp_cwb_display']) ?></td>
                            <td><?= htmlspecialchars($p['pp_cwnb_display']) ?></td>
                            <td><?= htmlspecialchars($p['pp_infant_display']) ?></td>
                            <td><?= htmlspecialchars($p['grand_total_display']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>

        <!-- ============ BANK ============ -->
        <h2 class="section">Bank Details</h2>
        <table>
            <tr><th>Bank</th><td><?= htmlspecialchars($q['bank_details']['bank_name']) ?></td>
                <th>A/C No</th><td><?= htmlspecialchars($q['bank_details']['account_no']) ?></td></tr>
            <tr><th>IFSC</th><td><?= htmlspecialchars($q['bank_details']['ifsc_code']) ?></td>
                <th>Branch</th><td><?= htmlspecialchars($q['bank_details']['branch_name']) ?></td></tr>
        </table>

        <!-- ============ TERMS ============ -->
        <?php if ($q['terms_conditions']['terms_and_conditions']) { ?>
            <h2 class="section">Terms &amp; Conditions</h2>
            <pre style="white-space:pre-wrap;"><?= $q['terms_conditions']['terms_and_conditions'] ?></pre>
        <?php } ?>

        <!-- ============ THANK YOU ============ -->
        <h2 class="section">Thank You</h2>
        <p>
            <?= htmlspecialchars($q['thank_you']['company_name']) ?><br>
            <?= htmlspecialchars($q['thank_you']['company_address']) ?><br>
            <?= htmlspecialchars($q['thank_you']['company_email']) ?> | <?= htmlspecialchars($q['thank_you']['company_contact']) ?><br>
            Prepared by: <?= htmlspecialchars($q['thank_you']['prepared_by']) ?>
        </p>

    </div>
</body>

</html>
