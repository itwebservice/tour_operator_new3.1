<?php
include "../../../model.php";
include "../print_functions.php";
require("../../../../classes/convert_amount_to_word.php");
global $currency_code, $currency;

$booking_id = $_GET['booking_id'];
$quotation_id = $_GET['quotation_id'];
$branch_status = $_GET['branch_status'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$year = $_GET['year'];

$credit_card_charges = $_GET['credit_card_charges'];
$charge = ($credit_card_charges != '') ? $credit_card_charges : 0;

$package_booking_info = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_booking_master where booking_id='$booking_id' "));
$branch_admin_id = ($_SESSION['branch_admin_id'] != '') ? $_SESSION['branch_admin_id'] : $package_booking_info['branch_admin_id'];
$branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_admin_id'"));

// Get branch-wise logo
$admin_logo_url = get_branch_logo_url($branch_admin_id);

$sq_terms_cond = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='Package Sale' and active_flag ='Active'"));
$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id'"));

$inclusions = ($package_booking_info['quotation_id'] == 0) ? $package_booking_info['inclusions'] : $sq_quotation['inclusions'];
$exclusions = ($package_booking_info['quotation_id'] == 0) ? $package_booking_info['exclusions'] : $sq_quotation['exclusions'];

$tour_name = $package_booking_info['tour_name'];
$from_date = date("d-m-Y", strtotime($package_booking_info['tour_from_date']));
$to_date = date("d-m-Y", strtotime($package_booking_info['tour_to_date']));
//Total days
$total_days1 = strtotime($package_booking_info['tour_to_date']) - strtotime($package_booking_info['tour_from_date']);
$total_days = round($total_days1 / 86400);

$_SESSION['generated_by'] = $app_name;
$booking_date = get_datetime_user($package_booking_info['booking_date']);

$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id = '$package_booking_info[customer_id]'"));
if ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') {
    $customer_name = $sq_customer['company_name'];
} else {
    $customer_name = $sq_customer['first_name'] . ' ' . $sq_customer['middle_name'] . ' ' . $sq_customer['last_name'];
}

$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id= '$package_booking_info[emp_id]'"));
$booker_name1 = $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];
if ($package_booking_info['emp_id'] == '0') {
    $booker_name = 'Admin';
} else {
    $booker_name = $booker_name1;
}
if($package_booking_info['quotation_id'] == 0){
    $sq_total_members = mysqli_num_rows(mysqlQuery("select traveler_id from package_travelers_details where booking_id='$booking_id'"));
}else{
    $sq_total_members = $sq_quotation['total_passangers'];
}

$roundoff = $package_booking_info['roundoff'];
$basic_cost1 = $package_booking_info['basic_amount'];
$service_charge = $package_booking_info['service_charge'];
$net_amount = $package_booking_info['net_total'];
$bsmValues = json_decode($package_booking_info['bsm_values']);

$tax_show = '';
$newBasic = $basic_cost1;
$name = '';
//////////////////Service Charge Rules
$service_tax_amount = 0;
if ($package_booking_info['tour_service_tax_subtotal'] !== 0.00 && ($package_booking_info['tour_service_tax_subtotal']) !== '') {
    $service_tax_subtotal1 = explode(',', $package_booking_info['tour_service_tax_subtotal']);
    for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
        $service_tax = explode(':', $service_tax_subtotal1[$i]);
        $service_tax_amount +=  $service_tax[2];
        $name .= $service_tax[0]  . $service_tax[1] . ', ';
    }
}
$service_tax_amount_show = currency_conversion($currency, $package_booking_info['currency_code'], $service_tax_amount);

if ($bsmValues[0]->service != '') {   //inclusive service charge
    $newBasic = $basic_cost1;
    $newSC = $service_tax_amount + $service_charge;
} else {
    // $tax_show = $service_tax_amount;
    $tax_show =  rtrim($name, ', ') . ' : ' . $currency_code . ' ' . ($service_tax_amount);
    $newSC = $service_charge;
}

////////////Basic Amount Rules
if ($bsmValues[0]->basic != '') { //inclusive basic

    $tax_show = '';
} else {
}
$net_amount1 = currency_conversion($currency, $package_booking_info['currency_code'], $net_amount);
?>

<!-- header -->
<section class="print_header main_block" style="margin-bottom: 0 !important;">
    <div class="col-md-6 no-pad">
        <span class="title"><svg xmlns="http://www.w3.org/2000/svg" height="20" width="15" viewBox="0 0 384 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M0 64C0 28.7 28.7 0 64 0L213.5 0c17 0 33.3 6.7 45.3 18.7L365.3 125.3c12 12 18.7 28.3 18.7 45.3L384 448c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 64zm208-5.5l0 93.5c0 13.3 10.7 24 24 24L325.5 176 208 58.5zM120 256c-13.3 0-24 10.7-24 24s10.7 24 24 24l144 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-144 0zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24l144 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-144 0z"/></svg> CONFIRMATION FORM</span>
        <div class="print_header_logo">
            <img src="<?php echo $admin_logo_url; ?>" class="img-responsive mg_tp_10" style="max-width: 210px; max-height: 90px; object-fit: contain;">
        </div>
    </div>
    <div class="col-md-6 no-pad">
        <div class="print_header_contact text-right">
            <span class="title"><?php echo $app_name; ?></span><br>
            <p><?php echo ($branch_status == 'yes') ? $branch_details['address1'] . ',' . $branch_details['address2'] . ',' . $branch_details['city'] : $app_address ?>
            </p>
            <p class="no-marg"><svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" height="12" width="12" viewBox="0 0 512 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M160.2 25C152.3 6.1 131.7-3.9 112.1 1.4l-5.5 1.5c-64.6 17.6-119.8 80.2-103.7 156.4 37.1 175 174.8 312.7 349.8 349.8 76.3 16.2 138.8-39.1 156.4-103.7l1.5-5.5c5.4-19.7-4.7-40.3-23.5-48.1l-97.3-40.5c-16.5-6.9-35.6-2.1-47 11.8l-38.6 47.2C233.9 335.4 177.3 277 144.8 205.3L189 169.3c13.9-11.3 18.6-30.4 11.8-47L160.2 25z"/></svg>
                <?php echo ($branch_status == 'yes') ?
                    $branch_details['contact_no'] : $app_contact_no ?></p>
            <p><svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" height="12" width="12" viewBox="0 0 512 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M48 64c-26.5 0-48 21.5-48 48 0 15.1 7.1 29.3 19.2 38.4l208 156c17.1 12.8 40.5 12.8 57.6 0l208-156c12.1-9.1 19.2-23.3 19.2-38.4 0-26.5-21.5-48-48-48L48 64zM0 196L0 384c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-188-198.4 148.8c-34.1 25.6-81.1 25.6-115.2 0L0 196z"/></svg>
                <?php echo ($branch_status == 'yes' && $branch_details['email_id'] != '') ? $branch_details['email_id'] : $app_email_id;; ?>
            </p>
        </div>
    </div>
</section>

<!-- print-detail -->
<section class="print_sec main_block">
    <div class="row">
        <div class="col-md-12">
            <div class="print_info_block">
                <ul class="main_block noType">
                    <li class="col-md-3 mg_tp_10 mg_bt_10">
                        <div class="print_quo_detail_block">
                        <svg xmlns="http://www.w3.org/2000/svg" height="14" width="12.25" viewBox="0 0 448 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M128 0c17.7 0 32 14.3 32 32l0 32 128 0 0-32c0-17.7 14.3-32 32-32s32 14.3 32 32l0 32 32 0c35.3 0 64 28.7 64 64l0 288c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 128C0 92.7 28.7 64 64 64l32 0 0-32c0-17.7 14.3-32 32-32zM64 240l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm128 0l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM64 368l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zm112 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16z"/></svg><br>
                            <span>BOOKING DATE</span><br>
                            <?= $booking_date ?><br>
                        </div>
                    </li>
                    <li class="col-md-3 mg_tp_10 mg_bt_10">
                        <div class="print_quo_detail_block">
                        <svg xmlns="http://www.w3.org/2000/svg" height="14" width="10.5" viewBox="0 0 384 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M32 0C14.3 0 0 14.3 0 32S14.3 64 32 64l0 11c0 42.4 16.9 83.1 46.9 113.1l67.9 67.9-67.9 67.9C48.9 353.9 32 394.6 32 437l0 11c-17.7 0-32 14.3-32 32s14.3 32 32 32l320 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-11c0-42.4-16.9-83.1-46.9-113.1l-67.9-67.9 67.9-67.9c30-30 46.9-70.7 46.9-113.1l0-11c17.7 0 32-14.3 32-32S369.7 0 352 0L32 0zM288 437l0 11-192 0 0-11c0-25.5 10.1-49.9 28.1-67.9l67.9-67.9 67.9 67.9c18 18 28.1 42.4 28.1 67.9z"/></svg><br>
                            <span>DURATION</span><br>
                            <?php echo ($total_days) . 'N/' . ($total_days + 1) . 'D'; ?><br>
                        </div>
                    </li>
                    <li class="col-md-3 mg_tp_10 mg_bt_10">
                        <div class="print_quo_detail_block">
                        <svg xmlns="http://www.w3.org/2000/svg" height="14" width="17.5" viewBox="0 0 640 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M320 16a104 104 0 1 1 0 208 104 104 0 1 1 0-208zM96 88a72 72 0 1 1 0 144 72 72 0 1 1 0-144zM0 416c0-70.7 57.3-128 128-128 12.8 0 25.2 1.9 36.9 5.4-32.9 36.8-52.9 85.4-52.9 138.6l0 16c0 11.4 2.4 22.2 6.7 32L32 480c-17.7 0-32-14.3-32-32l0-32zm521.3 64c4.3-9.8 6.7-20.6 6.7-32l0-16c0-53.2-20-101.8-52.9-138.6 11.7-3.5 24.1-5.4 36.9-5.4 70.7 0 128 57.3 128 128l0 32c0 17.7-14.3 32-32 32l-86.7 0zM472 160a72 72 0 1 1 144 0 72 72 0 1 1 -144 0zM160 432c0-88.4 71.6-160 160-160s160 71.6 160 160l0 16c0 17.7-14.3 32-32 32l-256 0c-17.7 0-32-14.3-32-32l0-16z"/></svg><br>
                            <span>TOTAL GUEST (s)</span><br>
                            <?php echo $sq_total_members; ?><br>
                        </div>
                    </li>
                    <li class="col-md-3 mg_tp_10 mg_bt_10">
                        <div class="print_quo_detail_block">
                        <svg xmlns="http://www.w3.org/2000/svg" height="14" width="15.75" viewBox="0 0 576 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M401.2 39.1L549.4 189.4c27.7 28.1 27.7 73.1 0 101.2L393 448.9c-9.3 9.4-24.5 9.5-33.9 .2s-9.5-24.5-.2-33.9L515.3 256.8c9.2-9.3 9.2-24.4 0-33.7L367 72.9c-9.3-9.4-9.2-24.6 .2-33.9s24.6-9.2 33.9 .2zM32.1 229.5L32.1 96c0-35.3 28.7-64 64-64l133.5 0c17 0 33.3 6.7 45.3 18.7l144 144c25 25 25 65.5 0 90.5L285.4 418.7c-25 25-65.5 25-90.5 0l-144-144c-12-12-18.7-28.3-18.7-45.3zm144-85.5a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/></svg><br>
                            <span>PRICE</span><br>
                            <?= $net_amount1 ?><br>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Package -->
<section class="print_sec main_block">
    <div class="section_heding">
        <h2>BOOKING DETAILS</h2>
        <div class="section_heding_img">
            <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mg_bt_20">
            <ul class="print_info_list no-pad noType">
                <li><svg xmlns="http://www.w3.org/2000/svg" height="14" width="7" viewBox="0 0 256 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M249.3 235.8c10.2 12.6 9.5 31.1-2.2 42.8l-128 128c-9.2 9.2-22.9 11.9-34.9 6.9S64.5 396.9 64.5 384l0-256c0-12.9 7.8-24.6 19.8-29.6s25.7-2.2 34.9 6.9l128 128 2.2 2.4z"/></svg><span>TOUR :</span> <?= $tour_name ?> </li>
                <li><svg xmlns="http://www.w3.org/2000/svg" height="14" width="7" viewBox="0 0 256 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M249.3 235.8c10.2 12.6 9.5 31.1-2.2 42.8l-128 128c-9.2 9.2-22.9 11.9-34.9 6.9S64.5 396.9 64.5 384l0-256c0-12.9 7.8-24.6 19.8-29.6s25.7-2.2 34.9 6.9l128 128 2.2 2.4z"/></svg><span>CUSTOMER :</span> <?= $customer_name ?></li>
                <li><svg xmlns="http://www.w3.org/2000/svg" height="14" width="7" viewBox="0 0 256 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M249.3 235.8c10.2 12.6 9.5 31.1-2.2 42.8l-128 128c-9.2 9.2-22.9 11.9-34.9 6.9S64.5 396.9 64.5 384l0-256c0-12.9 7.8-24.6 19.8-29.6s25.7-2.2 34.9 6.9l128 128 2.2 2.4z"/></svg><span>CONTACT :</span> <?= $package_booking_info['mobile_no'] ?></li>
            </ul>
        </div>
        <div class="col-md-12 mg_bt_20">
            <ul class="print_info_list no-pad noType">
                <li><svg xmlns="http://www.w3.org/2000/svg" height="14" width="7" viewBox="0 0 256 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M249.3 235.8c10.2 12.6 9.5 31.1-2.2 42.8l-128 128c-9.2 9.2-22.9 11.9-34.9 6.9S64.5 396.9 64.5 384l0-256c0-12.9 7.8-24.6 19.8-29.6s25.7-2.2 34.9 6.9l128 128 2.2 2.4z"/></svg><span>TOUR DATE :</span> <?= $from_date . ' To ' . $to_date ?></li>
                <li><svg xmlns="http://www.w3.org/2000/svg" height="14" width="7" viewBox="0 0 256 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M249.3 235.8c10.2 12.6 9.5 31.1-2.2 42.8l-128 128c-9.2 9.2-22.9 11.9-34.9 6.9S64.5 396.9 64.5 384l0-256c0-12.9 7.8-24.6 19.8-29.6s25.7-2.2 34.9 6.9l128 128 2.2 2.4z"/></svg><span>BOOKING ID :</span> <?= get_package_booking_id($booking_id, $year) ?></li>
                <?php if ($package_booking_info['package_type'] != '') { ?>
                    <li><svg xmlns="http://www.w3.org/2000/svg" height="14" width="7" viewBox="0 0 256 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M249.3 235.8c10.2 12.6 9.5 31.1-2.2 42.8l-128 128c-9.2 9.2-22.9 11.9-34.9 6.9S64.5 396.9 64.5 384l0-256c0-12.9 7.8-24.6 19.8-29.6s25.7-2.2 34.9 6.9l128 128 2.2 2.4z"/></svg><span>PACKAGE TYPE :</span> <?= $package_booking_info['package_type'] ?></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</section>


<!-- Passenger -->
<section class="print_sec main_block">
    <div class="section_heding">
        <h2>PASSENGERS</h2>
        <div class="section_heding_img">
            <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered no-marg" id="tbl_emp_list">
                    <thead>
                        <tr class="table-heading-row">
                            <th>Full_Name</th>
                            <th>Gender</th>
                            <th>DOB</th>
                            <th>Age</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sq_members1 = mysqlQuery("select * from package_travelers_details where booking_id = '$booking_id'");
                        while ($row_members1 = mysqli_fetch_assoc($sq_members1)) { ?>
                            <tr>
                                <td><?php echo $row_members1['first_name'] . ' ' . $row_members1['middle_name'] . ' ' . $row_members1['last_name']; ?>
                                </td>
                                <td><?php echo $row_members1['gender']; ?></td>
                                <td><?php echo date("d-m-Y", strtotime($row_members1['birth_date'])); ?></td>
                                <td><?php echo $row_members1['age']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Accommodation -->
<?php
$sq_count = mysqli_num_rows(mysqlQuery("select * from package_hotel_accomodation_master where booking_id='$booking_id'"));
if ($sq_count != 0) {
?>
    <section class="print_sec main_block">
        <div class="section_heding">
            <h2>ACCOMMODATION</h2>
            <div class="section_heding_img">
                <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered no-marg" id="tbl_emp_list">
                        <thead>
                            <tr class="table-heading-row">
                                <th>City</th>
                                <th>Hotel_NAME</th>
                                <th>Check_In</th>
                                <th>Check_Out</th>
                                <th>Rooms</th>
                                <th>Category</th>
                                <th>Extra_Bed</th>
                                <th>Meal_plan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sq_entry = mysqlQuery("select * from package_hotel_accomodation_master where booking_id='$booking_id'");
                            while ($row_entry = mysqli_fetch_assoc($sq_entry)) {
                                $city_id = $row_entry['city_id'];
                                $hotel_id = $row_entry['hotel_id'];
                                $sq_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$city_id'"));
                                $sq_hotel_name = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$hotel_id'"));
                            ?>
                                <tr>
                                    <td><?php echo $sq_city['city_name']; ?></td>
                                    <td><?php echo $sq_hotel_name['hotel_name']; ?></td>
                                    <td><?php echo get_datetime_user($row_entry['from_date']); ?></td>
                                    <td><?php echo get_datetime_user($row_entry['to_date']); ?></td>
                                    <td><?php echo $row_entry['rooms']; ?></td>
                                    <td><?php echo $row_entry['catagory']; ?></td>
                                    <td><?php echo $row_entry['room_type']; ?></td>
                                    <td><?php echo $row_entry['meal_plan']; ?></td>
                                </tr>
                            <?php
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php } ?>


<!-- transport -->
<?php
$sq_count = mysqli_num_rows(mysqlQuery("select * from package_tour_transport_master where booking_id='$booking_id'"));
if ($sq_count != 0) {
?>
    <section class="print_sec main_block">
        <div class="section_heding">
            <h2>TRANSPORT</h2>
            <div class="section_heding_img">
                <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered no-marg" id="tbl_emp_list">
                        <thead>
                            <tr class="table-heading-row">
                                <th>Vehicle</th>
                                <th>Start_Date</th>
                                <th>End_Date</th>
                                <th>Pickup</th>
                                <th>Drop</th>
                                <th>S_Duration</th>
                                <th>Vehicles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sq_entry = mysqlQuery("select * from package_tour_transport_master where booking_id='$booking_id'");
                            while ($row_entry = mysqli_fetch_assoc($sq_entry)) {
                                $q_transport = mysqli_fetch_assoc(mysqlQuery("select * from b2b_transfer_master where entry_id='$row_entry[transport_bus_id]'"));
                                // Pickup
                                if ($row_entry['pickup_type'] == 'city') {
                                    $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_entry[pickup]'"));
                                    $pickup = $row['city_name'];
                                } else if ($row_entry['pickup_type'] == 'hotel') {
                                    $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_entry[pickup]'"));
                                    $pickup = $row['hotel_name'];
                                } else {
                                    $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_entry[pickup]'"));
                                    $airport_nam = clean($row['airport_name']);
                                    $airport_code = clean($row['airport_code']);
                                    $pickup = $airport_nam . " (" . $airport_code . ")";
                                }
                                //Drop-off
                                if ($row_entry['drop_type'] == 'city') {
                                    $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_entry[drop]'"));
                                    $drop = $row['city_name'];
                                } else if ($row_entry['drop_type'] == 'hotel') {
                                    $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_entry[drop]'"));
                                    $drop = $row['hotel_name'];
                                } else {
                                    $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_entry[drop]'"));
                                    $airport_nam = clean($row['airport_name']);
                                    $airport_code = clean($row['airport_code']);
                                    $drop = $airport_nam . " (" . $airport_code . ")";
                                }
                            ?>
                                <tr>
                                    <td><?= $q_transport['vehicle_name'] ?></td>
                                    <td><?= get_datetime_user($row_entry['transport_from_date']) ?></td>
                                    <td><?= get_datetime_user($row_entry['transport_end_date']) ?></td>
                                    <td><?= $pickup ?></td>
                                    <td><?= $drop ?></td>
                                    <td><?= $row_entry['service_duration'] ?></td>
                                    <td><?= $row_entry['vehicle_count'] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<!-- Activity -->
<?php
$sq_count = mysqli_num_rows(mysqlQuery("select * from package_tour_excursion_master where booking_id='$booking_id'"));
if ($sq_count != 0) {
?>
    <section class="print_sec main_block">
        <div class="section_heding">
            <h2>Activity</h2>
            <div class="section_heding_img">
                <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered no-marg" id="tbl_emp_list">
                        <thead>
                            <tr class="table-heading-row">
                                <th>Activity Date</th>
                                <th>City_Name</th>
                                <th>Activity Name</th>
                                <th>Transfer Option</th>
                                <th>Vehicle Name</th>
                                <th>Adult(s)</th>
                                <th>CWB</th>
                                <th>CWOB</th>
                                <th>Infant(s)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sq_entry = mysqlQuery("select * from package_tour_excursion_master where booking_id='$booking_id'");
                            while ($row_entry = mysqli_fetch_assoc($sq_entry)) {
                                $q_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_entry[city_id]'"));
                                $sq_ex = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master_tariff where entry_id='$row_entry[exc_id]'"));
                                $vehicle_name = get_excursion_vehicle_display_name($row_entry);
                            ?>
                                <tr>
                                    <td><?php echo get_datetime_user($row_entry['exc_date']) ?></td>
                                    <td><?= $q_city['city_name'] ?></td>
                                    <td><?= $sq_ex['excursion_name'] ?></td>
                                    <td><?= $row_entry['transfer_option'] ?> </td>
                                    <td><?= $vehicle_name ?></td>
                                    <td><?= $row_entry['adult'] ?> </td>
                                    <td><?= $row_entry['chwb'] ?> </td>
                                    <td><?= $row_entry['chwob'] ?> </td>
                                    <td><?= $row_entry['infant'] ?> </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php } ?>


<?php
//Train
$sq_train = mysqli_num_rows(mysqlQuery("select booking_id from package_train_master where booking_id='$booking_id'"));
$sq_air = mysqli_num_rows(mysqlQuery("select booking_id from package_plane_master where booking_id='$booking_id'"));
if ($sq_train > 0 || $sq_air > 0) {
    $sq_train = mysqli_num_rows(mysqlQuery("select booking_id from package_train_master where booking_id='$booking_id'"));
    $train_count = 0;

    if ($sq_train > 0) { ?>
        <section class="print_sec main_block">
            <div class="section_heding">
                <h2>Train</h2>
                <div class="section_heding_img">
                    <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered no-marg" id="tbl_emp_list">
                            <thead>
                                <tr class="table-heading-row">
                                    <th>From</th>
                                    <th>To</th>
                                    <th>TRAIN</th>
                                    <th>SEATS</th>
                                    <th>CLASS</th>
                                    <th>PRIORITY</th>
                                    <th>DEPARTURE D/T</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sq_train_details = mysqlQuery("select * from package_train_master where booking_id='$booking_id'");
                                while ($row_train_details = mysqli_fetch_assoc($sq_train_details)) { ?>
                                    <tr>
                                        <td><?php echo $row_train_details['from_location']; ?></td>
                                        <td><?php echo $row_train_details['to_location']; ?></td>
                                        <td><?php echo $row_train_details['train_no']; ?></td>
                                        <td><?php echo $row_train_details['seats']; ?></td>
                                        <td><?php echo $row_train_details['train_class']; ?></td>
                                        <td><?php echo $row_train_details['train_priority']; ?></td>
                                        <td><?php echo get_datetime_user($row_train_details['date']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
<?php }
} ?>

<?php
//Flight
$sq_air = mysqli_num_rows(mysqlQuery("select booking_id from package_plane_master where booking_id='$booking_id'"));
$air_count = 0;

if ($sq_air > 0) { ?>
    <section class="print_sec main_block">
        <div class="section_heding">
            <h2>Flight</h2>
            <div class="section_heding_img">
                <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered no-marg" id="tbl_emp_list">
                        <thead>
                            <tr class="table-heading-row">
                                <th>DEPARTURE D/T</th>
                                <th>ARRIVAL D/T</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Airline</th>
                                <th>Class</th>
                                <th>SEATS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sq_air_details = mysqlQuery("select * from package_plane_master where booking_id='$booking_id'");
                            while ($row_air_details = mysqli_fetch_assoc($sq_air_details)) {
                                $sq_airline = mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$row_air_details[company]'")); ?>
                                <tr>
                                    <td><?php echo get_datetime_user($row_air_details['date']); ?></td>
                                    <td><?php echo get_datetime_user($row_air_details['arraval_time']); ?></td>
                                    <td><?php echo $row_air_details['from_location']; ?></td>
                                    <td><?php echo $row_air_details['to_location']; ?></td>
                                    <td><?php echo $sq_airline['airline_name'] . ' (' . $sq_airline['airline_code'] . ')'; ?>
                                    </td>
                                    <td><?php echo $row_air_details['class']; ?></td>
                                    <td><?php echo $row_air_details['seats']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<?php
//Cruise
$sq_cruise = mysqli_num_rows(mysqlQuery("select booking_id from package_cruise_master where booking_id='$booking_id'"));
if ($sq_cruise > 0) { ?>
    <section class="print_sec main_block">
        <div class="section_heding">
            <h2>Cruise</h2>
            <div class="section_heding_img">
                <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered no-marg" id="tbl_emp_list">
                        <thead>
                            <tr class="table-heading-row">
                                <th>DEPARTURE D/T</th>
                                <th>ARRIVAL D/T</th>
                                <th>ROUTE</th>
                                <th>CABIN</th>
                                <th>SHARING</th>
                                <th>SEATS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sq_cruise_details = mysqlQuery("select * from package_cruise_master where booking_id='$booking_id'");
                            while ($row_cruise_details = mysqli_fetch_assoc($sq_cruise_details)) { ?>
                                <tr>
                                    <td><?php echo get_datetime_user($row_cruise_details['dept_datetime']); ?></td>
                                    <td><?php echo get_datetime_user($row_cruise_details['arrival_datetime']); ?></td>
                                    <td><?php echo $row_cruise_details['route']; ?></td>
                                    <td><?php echo $row_cruise_details['cabin']; ?></td>
                                    <td><?php echo $row_cruise_details['sharing']; ?></td>
                                    <td><?php echo $row_cruise_details['seats']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<!-- Inclusion -->
<section class="print_sec main_block">
    <div class="row">
        <div class="col-md-12">
            <div class="section_heding">
                <h2>Inclusions</h2>
                <div class="section_heding_img">
                    <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                </div>
            </div>
            <div class="print_text_bolck">
                <?php echo $inclusions; ?>
            </div>
        </div>
    </div>
</section>


<!-- Exclusion -->
<section class="print_sec main_block">
    <div class="row">
        <div class="col-md-12">
            <div class="section_heding">
                <h2>Exclusions</h2>
                <div class="section_heding_img">
                    <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                </div>
            </div>
            <div class="print_text_bolck">
                <?php echo $exclusions; ?>
            </div>
        </div>
    </div>
</section>

<?php
if (isset($sq_terms_cond['terms_and_conditions'])) { ?>
    <!-- Terms and Conditions -->
    <section class="print_sec main_block">
        <div class="row">
            <div class="col-md-12">
                <div class="section_heding">
                    <h2>Terms and Conditions</h2>
                    <div class="section_heding_img">
                        <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                    </div>
                </div>
                <div class="print_text_bolck">
                    <span><?= $sq_terms_cond['terms_and_conditions'] ?></span>
                </div>
            </div>
        </div>
    </section>
<?php } ?>


<!-- Booking Summary -->
<section class="print_sec main_block">
    <div class="row">
        <div class="col-md-12">
            <div class="section_heding">
                <h2>Booking Summary</h2>
                <div class="section_heding_img">
                    <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                </div>
            </div>
            <div class="row">
                <div class="col-md-7 mg_bt_20">
                    <ul class="print_info_list no-pad noType">
                        <li><span>BOOKING DATE :</span> <?= get_datetime_user($package_booking_info['booking_date']) ?>
                        </li>
                    </ul>
                </div>
                <div class="col-md-5 mg_bt_20">
                    <ul class="print_info_list no-pad noType">
                        <li><span>DUE DATE :</span>
                            <?php echo ($package_booking_info['due_date'] != '1970-01-01') ? get_date_user($package_booking_info['due_date']) : get_date_user($package_booking_info['tour_to_date']); ?>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="print_text_bolck">
                        <span><?= $package_booking_info['special_request'] ?></span>
                    </div>
                </div>
            </div>
</section>

<!-- Payment Detail -->
<?php

$total_hotel_expense = ($package_booking_info['total_hotel_expense'] != "") ? $package_booking_info['total_hotel_expense'] : 0;
$total_travel_expense = ($package_booking_info['total_travel_expense'] != "") ? $package_booking_info['total_travel_expense'] : 0;

$newBasic1 = currency_conversion($currency, $package_booking_info['currency_code'], ($package_booking_info['subtotal']));
$charge1 = currency_conversion($currency, $package_booking_info['currency_code'], $charge);
// $tcs_tax = currency_conversion($currency, $package_booking_info['currency_code'], $package_booking_info['tcs_tax']);
$tcs_tax =$package_booking_info['tcs_tax'];
$roundoff = currency_conversion($currency, $package_booking_info['currency_code'], $roundoff);
$tds = currency_conversion($currency, $package_booking_info['currency_code'], $package_booking_info['tds']);

$tcs_show=currency_conversion($currency, $package_booking_info['currency_code'],$package_booking_info['tcs_per']);
?>
<section class="print_sec main_block">
    <div class="section_heding">
        <h2>PAYMENT DETAILS</h2>
        <div class="section_heding_img">
            <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="print_amount_block">
                <ul class="main_block no-pad text-right noType">
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>BASIC AMOUNT : </span><?php echo $newBasic1; ?></li>
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>ROUNDOFF : </span><?= $roundoff ?></li>
                </ul>
                <ul class="main_block no-pad text-right noType">
                </ul>
                <ul class="main_block no-pad text-right noType">
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>TAX :
                        </span><?php echo str_replace(',', '', $name) . $service_tax_amount_show; ?></li>
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>NET AMOUNT : </span><?php echo $net_amount1; ?></li>
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>TCS
                            : </span><?= '(' .$tcs_tax  . '%)' ?><?= $tcs_show ?></li>
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>CREDIT CARD CHARGES : </span><?= $charge1 ?></li>
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>TDS : </span><?= $tds ?></li>
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>DUE DATE :
                        </span><?php echo ($package_booking_info['due_date'] != '1970-01-01') ? get_date_user($package_booking_info['due_date']) : get_date_user($package_booking_info['tour_to_date']); ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-6">
            <div class="print_amount_block">
                <ul class="main_block no-pad text-right noType">
                    <li class="col-md-12 mg_tp_10 mg_bt_10 font_5"><span>TOTAL AMOUNT :
                        </span><?php echo $net_amount1; ?></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 text-center">
            <div class="print_quotation_creator">
                <span>CUSTOMER'S SIGNATURE</span><br>
            </div>
        </div>
        <div class="col-md-7 text-right">
            <div class="print_quotation_creator text-center">
                <span>BOOKED BY </span><br><?php echo $booker_name; ?>
            </div>
        </div>
    </div>
</section>
</body>

</html>