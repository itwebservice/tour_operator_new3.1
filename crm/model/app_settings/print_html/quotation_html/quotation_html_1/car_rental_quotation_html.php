<?php
//Generic Files
include "../../../../model.php";
include "printFunction.php";
global $app_quot_img, $currency;

$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];

// Get branch-wise logo and QR code
$admin_logo_url = get_branch_logo_url($branch_admin_id);
$branch_qr_url = get_branch_qr_url($branch_admin_id);
$sq = mysqli_fetch_assoc(mysqlQuery("select * from branch_assign where link='package_booking/quotation/car_flight/car_rental/index.php'"));
$branch_status = $sq['branch_status'];

if ($branch_admin_id != 0) {
    $branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_admin_id'"));
    $sq_bank_count = mysqli_num_rows(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id' and active_flag='Active'"));
    $sq_bank_branch = mysqli_fetch_assoc(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id' and active_flag='Active'"));
} else {
    $branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='1'"));
    $sq_bank_count = mysqli_num_rows(mysqlQuery("select * from bank_master where branch_id='1' and active_flag='Active'"));
    $sq_bank_branch = mysqli_fetch_assoc(mysqlQuery("select * from bank_master where branch_id='1' and active_flag='Active'"));
}

$quotation_id = $_GET['quotation_id'];

$sq_terms_cond = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='Car Rental Quotation' and active_flag ='Active'"));

$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_quotation_master where quotation_id='$quotation_id'"));
$sq_login = mysqli_fetch_assoc(mysqlQuery("select * from roles where id='$sq_quotation[login_id]'"));
$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_login[emp_id]'"));
$quotation_date = $sq_quotation['quotation_date'];
$yr = explode("-", $quotation_date);
$year = $yr[0];

if ($sq_emp_info['first_name'] == '') {
    $emp_name = 'Admin';
} else {
    $emp_name = $sq_emp_info['first_name'] . ' ' . $sq_emp_info['last_name'];
}
$tax_show = '';
$service_charge = $sq_quotation['service_charge'];
$newBasic = $basic_cost1 = $sq_quotation['subtotal'] + $sq_quotation['other_charge'] + $sq_quotation['state_entry'] + $service_charge + $sq_quotation['markup_cost'];
$bsmValues = json_decode($sq_quotation['bsm_values']);
//////////////////Service Charge Rules
$service_tax_amount = 0;
$percent = '';
if ($sq_quotation['service_tax_subtotal'] !== 0.00 && ($sq_quotation['service_tax_subtotal']) !== '') {
    $service_tax_subtotal1 = explode(',', $sq_quotation['service_tax_subtotal']);
    for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
        $service_tax = explode(':', $service_tax_subtotal1[$i]);
        $service_tax_amount +=  $service_tax[2];
        $percent .= $service_tax[0]  . $service_tax[1] .', ';
    }
}
////////////////////Markup Rules
$markupservice_tax_amount = 0;
if ($sq_quotation['markup_cost_subtotal'] !== 0.00 && $sq_quotation['markup_cost_subtotal'] !== "") {
    $service_tax_markup1 = explode(',', $sq_quotation['markup_cost_subtotal']);
    for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
        $service_tax = explode(':', $service_tax_markup1[$i]);
        $markupservice_tax_amount += $service_tax[2];
    }
}

// $total_tax = currency_conversion($currency, $currency, ($markupservice_tax_amount + $service_tax_amount));
$tax_cost =  $markupservice_tax_amount + $service_tax_amount;

            $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $tax_cost);

		if ($sq_quotation['currency_code'] != '0' && $currency != $sq_quotation['currency_code'] && $tax_cost != $currency_amount1) {
	 $total_tax =  $currency_amount1 ;
	} else {
	$total_tax = $tax_cost;
	}


$tax_show = $percent . ' ' .$total_tax;
// $quotation_cost = currency_conversion($currency, $currency, $sq_quotation['total_tour_cost']);

$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['total_tour_cost']);

		if ($sq_quotation['currency_code'] != '0' && $currency != $sq_quotation['currency_code'] && $sq_quotation['total_tour_cost'] != $currency_amount1) {
	$quotation_cost = $currency_amount1 ;
	} else {
	$quotation_cost = $sq_quotation['total_tour_cost'];
	}

$sq_package_program = mysqlQuery("select * from car_rental_quotation_program where quotation_id='$quotation_id'");
$sq_package_count = mysqli_num_rows($sq_package_program);
?>

<section class="headerPanel main_block">
    <div class="headerImage">
        <img src="<?= $app_quot_img ?>" class="img-responsive">
        <div class="headerImageOverLay"></div>
    </div>

    <!-- header -->
    <section class="print_header main_block side_pad mg_tp_30">
        <div class="col-md-4 no-pad">
            <div class="print_header_logo">
                <img src="<?= $admin_logo_url ?>" class="img-responsive mg_tp_10">
            </div>
        </div>
        <div class="col-md-4 no-pad text-center mg_tp_30">
            <span class="title"><svg xmlns="http://www.w3.org/2000/svg" height="18" width="18" viewBox="0 0 512 512"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L368 46.1 465.9 144 490.3 119.6c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L432 177.9 334.1 80 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z"/></svg> CAR RENTAL QUOTATION</span>
        </div>

        <?php
    include "standard_header_html.php";
    ?>

        <!-- print-detail -->
        <section class="print_sec main_block side_pad">
            <div class="row">
                <div class="col-md-12">
                    <div class="print_info_block">
                        <ul class="main_block">
                            <li class="col-md-3 mg_tp_10 mg_bt_10">
                                <div class="print_quo_detail_block">
                                <svg xmlns="http://www.w3.org/2000/svg" height="14" width="12.25" viewBox="0 0 448 512"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M128 0c17.7 0 32 14.3 32 32l0 32 128 0 0-32c0-17.7 14.3-32 32-32s32 14.3 32 32l0 32 32 0c35.3 0 64 28.7 64 64l0 288c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 128C0 92.7 28.7 64 64 64l32 0 0-32c0-17.7 14.3-32 32-32zM64 240l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm128 0l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM64 368l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zm112 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16z"/></svg><br>
                                    <span>QUOTATION DATE</span><br>
                                    <?= get_date_user($sq_quotation['quotation_date']) ?><br>
                                </div>
                            </li>
                            <li class="col-md-3 mg_tp_10 mg_bt_10">
                                <div class="print_quo_detail_block">
                                <svg xmlns="http://www.w3.org/2000/svg" height="14" width="14" viewBox="0 0 512 512"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M214.7 .7c17.3 3.7 28.3 20.7 24.6 38l-19.1 89.3 126.5 0 22-102.7C372.4 8 389.4-3 406.7 .7s28.3 20.7 24.6 38L412.2 128 480 128c17.7 0 32 14.3 32 32s-14.3 32-32 32l-81.6 0-27.4 128 67.8 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-81.6 0-22 102.7c-3.7 17.3-20.7 28.3-38 24.6s-28.3-20.7-24.6-38l19.1-89.3-126.5 0-22 102.7c-3.7 17.3-20.7 28.3-38 24.6s-28.3-20.7-24.6-38L99.8 384 32 384c-17.7 0-32-14.3-32-32s14.3-32 32-32l81.6 0 27.4-128-67.8 0c-17.7 0-32-14.3-32-32s14.3-32 32-32l81.6 0 22-102.7C180.4 8 197.4-3 214.7 .7zM206.4 192l-27.4 128 126.5 0 27.4-128-126.5 0z"/></svg><br>
                                    <span>DURATION</span><br>
                                    <?php echo $sq_quotation['days_of_traveling'] . ' Days'; ?><br>
                                </div>
                            </li>
                            <li class="col-md-3 mg_tp_10 mg_bt_10">
                                <div class="print_quo_detail_block">
                                <svg xmlns="http://www.w3.org/2000/svg" height="14" width="17.5" viewBox="0 0 640 512"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M320 16a104 104 0 1 1 0 208 104 104 0 1 1 0-208zM96 88a72 72 0 1 1 0 144 72 72 0 1 1 0-144zM0 416c0-70.7 57.3-128 128-128 12.8 0 25.2 1.9 36.9 5.4-32.9 36.8-52.9 85.4-52.9 138.6l0 16c0 11.4 2.4 22.2 6.7 32L32 480c-17.7 0-32-14.3-32-32l0-32zm521.3 64c4.3-9.8 6.7-20.6 6.7-32l0-16c0-53.2-20-101.8-52.9-138.6 11.7-3.5 24.1-5.4 36.9-5.4 70.7 0 128 57.3 128 128l0 32c0 17.7-14.3 32-32 32l-86.7 0zM472 160a72 72 0 1 1 144 0 72 72 0 1 1 -144 0zM160 432c0-88.4 71.6-160 160-160s160 71.6 160 160l0 16c0 17.7-14.3 32-32 32l-256 0c-17.7 0-32-14.3-32-32l0-16z"/></svg><br>
                                    <span>TOTAL GUEST</span><br>
                                    <?= $sq_quotation['total_pax'] ?><br>
                                </div>
                            </li>
                            <li class="col-md-3 mg_tp_10 mg_bt_10">
                                <div class="print_quo_detail_block">
                                <svg xmlns="http://www.w3.org/2000/svg" height="14" width="15.75" viewBox="0 0 576 512"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M401.2 39.1L549.4 189.4c27.7 28.1 27.7 73.1 0 101.2L393 448.9c-9.3 9.4-24.5 9.5-33.9 .2s-9.5-24.5-.2-33.9L515.3 256.8c9.2-9.3 9.2-24.4 0-33.7L367 72.9c-9.3-9.4-9.2-24.6 .2-33.9s24.6-9.2 33.9 .2zM32.1 229.5L32.1 96c0-35.3 28.7-64 64-64l133.5 0c17 0 33.3 6.7 45.3 18.7l144 144c25 25 25 65.5 0 90.5L285.4 418.7c-25 25-65.5 25-90.5 0l-144-144c-12-12-18.7-28.3-18.7-45.3zm144-85.5a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/></svg><br>
                                    <span>PRICE</span><br>
                                    <?= $quotation_cost ?><br>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

    </section>

    <!-- Package -->
    <section class="print_sec main_block side_pad mg_tp_30">
        <div class="section_heding">
            <h2>BOOKING DETAILS</h2>
            <div class="section_heding_img">
                <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
            </div>
        </div>
        <div class="row mg_tp_30">
            <div class="col-md-12">
                <div class="print_info_block">
                    <ul class="print_info_list">
                        <li class="col-md-6 mg_tp_10 mg_bt_10"><span>ROUTE
                                :</span><?= ($sq_quotation['travel_type'] == 'Outstation') ? $sq_quotation['places_to_visit'] : $sq_quotation['local_places_to_visit'] ?>
                        </li>
                        <li class="col-md-6 mg_tp_10 mg_bt_10"><span>CUSTOMER NAME :</span>
                            <?= $sq_quotation['customer_name'] ?></li>
                    </ul>
                    <ul class="print_info_list">
                        <li class="col-md-6 mg_tp_10 mg_bt_10"><span>QUOTATION ID :</span>
                            <?= get_quotation_id($quotation_id, $year) ?></li>
                        <li class="col-md-6 mg_tp_10 mg_bt_10"><span>E-MAIL ID :</span> <?= $sq_quotation['email_id'] ?>
                        </li>
                        <?php if ($sq_quotation['mobile_no'] != '') { ?><li class="col-md-6 mg_tp_10 mg_bt_10">
                            <span>MOBILE NO :</span> <?= $sq_quotation['mobile_no'] ?>
                        </li><?php } ?>
                    </ul>
                    <hr class="main_block">
                    <?php if ($sq_quotation['travel_type'] == 'Local') { ?>
                    <ul class="main_block">
                        <li class="col-md-6 mg_tp_10 mg_bt_10"><span>FROM DATE :
                            </span><?= get_date_user($sq_quotation['from_date']) ?></li>
                        <li class="col-md-6 mg_tp_10 mg_bt_10"><span>TO DATE :
                            </span><?= get_date_user($sq_quotation['to_date']) ?></li>
                    </ul>
                    <?php } else { ?>
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>FROM DATE :
                        </span><?= get_date_user($sq_quotation['from_date']) ?></li>
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>TO DATE :
                        </span><?= get_date_user($sq_quotation['to_date']) ?></li>
                    <?php } ?>
                    <?php $no_of_car = ceil($sq_quotation['total_pax'] / $sq_quotation['capacity']); ?>
                    <ul class="main_block">
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Transport -->
    <section class="print_sec main_block side_pad mg_tp_30">
        <div class="section_heding">
            <h2>VEHICLE DETAILS</h2>
            <div class="section_heding_img">
                <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="print_info_block">
                    <ul class="main_block no-pad">
                        <li class="col-md-4 mg_tp_10 mg_bt_10"><span>VEHICLE NAME :
                            </span><?= $sq_quotation['vehicle_name'] ?></li>
                        <li class="col-md-6 mg_tp_10 mg_bt_10"><span>NO OF VEHICLE : </span><?= $no_of_car ?></li>
                    </ul>
                    <ul class="main_block no-pad">
                        <li class="col-md-4 mg_tp_10 mg_bt_10"><span>EXTRA KM COST :
                            </span><?= $sq_quotation['extra_km_cost'] ?></li>
                        <li class="col-md-4 mg_tp_10 mg_bt_10"><span>EXTRA HR COST :
                            </span><?= $sq_quotation['extra_hr_cost'] ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Costing -->
    <section class="print_sec main_block side_pad mg_tp_30">
        <div class="row">
            <div class="col-md-6">
                <div class="section_heding">
                    <h2>COSTING</h2>
                    <div class="section_heding_img">
                        <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                    </div>
                </div>
                <div class="print_info_block">
                    <ul class="main_block">
                        <li class="col-md-12 mg_tp_10 mg_bt_10"><span>TOTAL FARE :
                            </span><?= 
                            
                            // currency_conversion($currency, $currency, $newBasic) 

                            $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], (float)($newBasic));

		if ($sq_quotation['currency_code'] != '0' && $currency != $sq_quotation['currency_code'] && (float)($newBasic) != $currency_amount1) {
	 $fare_cost = $currency_amount1 ;
	} else {
	 $fare_cost = (float)($newBasic);
	}
                            
                            ?></li>
                        <li class="col-md-12 mg_tp_10 mg_bt_10"><span>TAX : </span><?= $tax_show ?></li>
                        <?php if ($sq_quotation['travel_type'] == "Outstation") { ?>
                        <li class="col-md-12 mg_tp_10 mg_bt_10"><span>PERMIT :
                            </span><?= 
                            // currency_conversion($currency, $currency, $sq_quotation['permit']) 

                            $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['permit']);

		if ($sq_quotation['currency_code'] != '0' && $currency != $sq_quotation['currency_code'] && $sq_quotation['permit'] != $currency_amount1) {
	 $permit_cost = $currency_amount1 ;
	} else {
	 $permit_cost = $sq_quotation['permit'];
	}
                            
                            
                            ?></li>
                        <li class="col-md-12 mg_tp_10 mg_bt_10"><span>TOLL/PARKING :
                            </span><?= 
                            // currency_conversion($currency, $currency, $sq_quotation['toll_parking'])
                            
                             $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['toll_parking']);

		if ($sq_quotation['currency_code'] != '0' && $currency != $sq_quotation['currency_code'] && $sq_quotation['toll_parking'] != $currency_amount1) {
	 $toll_cost = $currency_amount1 ;
	} else {
	 $toll_cost = $sq_quotation['toll_parking'];
	}
                            ?></li>
                        <li class="col-md-12 mg_tp_10 mg_bt_10"><span>DRIVER ALLOWANCE :
                            </span><?= 
                            // currency_conversion($currency, $currency, $sq_quotation['driver_allowance'])

                             $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['driver_allowance']);

		if ($sq_quotation['currency_code'] != '0' && $currency != $sq_quotation['currency_code'] && $sq_quotation['driver_allowance'] != $currency_amount1) {
	 $driver_allowance_cost = $currency_amount1 ;
	} else {
	 $driver_allowance_cost = $sq_quotation['driver_allowance'];
	}
                            
                            
                            ?></li>
                        <?php } ?>
                        <li class="col-md-12 mg_tp_10 mg_bt_10"><span>ROUND OFF :
                            </span><?= currency_conversion($currency, $currency, $sq_quotation['roundoff']) ?></li>
                        <li class="col-md-12 mg_tp_10 mg_bt_10"><span>QUOTATION COST :
                                <?= 
                                // currency_conversion($currency, $currency, $sq_quotation['total_tour_cost'])
                                
                                 $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['total_tour_cost']);

		if ($sq_quotation['currency_code'] != '0' && $currency != $sq_quotation['currency_code'] && $sq_quotation['total_tour_cost'] != $currency_amount1) {
	 $qtn_cost = $currency_amount1 ;
	} else {
	 $qtn_cost = $sq_quotation['total_tour_cost'];
	}
                                ?></span></li>
                    </ul>
                </div>
            </div>

            <!-- Bank Detail -->
            <div class="col-md-6">
                <div class="section_heding">
                    <h2>BANK DETAILS</h2>
                    <div class="section_heding_img">
                        <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                    </div>
                </div>
                <div class="print_info_block">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="main_block">
                                <li class="col-md-12 mg_tp_10 mg_bt_10"><span>BANK NAME :
                                    </span><?=  ($sq_bank_count>0 || $sq_bank_branch['bank_name'] != '') ? $sq_bank_branch['bank_name'] : $bank_name_setting ?></li>
                                <li class="col-md-12 mg_tp_10 mg_bt_10"><span>A/C TYPE :
                                    </span><?= ($sq_bank_count>0 || $sq_bank_branch['account_type'] != '') ? $sq_bank_branch['account_type'] : $acc_name ?></li>
                                <li class="col-md-12 mg_tp_10 mg_bt_10"><span>BRANCH :
                                    </span><?= ($sq_bank_count>0 || $sq_bank_branch['branch_name'] != '') ? $sq_bank_branch['branch_name'] : $bank_branch_name ?>
                                </li>
                                <li class="col-md-12 mg_tp_10 mg_bt_10"><span>A/C NO :
                                    </span><?= ($sq_bank_count>0 || $sq_bank_branch['account_no'] != '') ? $sq_bank_branch['account_no'] : $bank_acc_no  ?></li>
                                <li class="col-md-12 mg_tp_10 mg_bt_10"><span>BANK ACCOUNT NAME :
                                    </span><?= ($sq_bank_count>0 || $sq_bank_branch['account_name'] != '') ? $sq_bank_branch['account_name'] : $bank_account_name ?></li>
                                <li class="col-md-12 mg_tp_10 mg_bt_10"><span>SWIFT CODE :
                                    </span><?= ($sq_bank_count>0 || $sq_bank_branch['swift_code'] != '') ? strtoupper($sq_bank_branch['swift_code']) :  strtoupper($bank_swift_code) ?></li>
                            </ul>
                        </div>
                        <?php if (check_qr($branch_admin_id)) {
            ?>
                        <div class="col-md-6 text-center">
                            <?= get_qr('Protrait Standard', $branch_admin_id) ?>
                            <br>
                            <h4 class="no-marg">Scan & Pay </h4>

                        </div>
                        <?php } ?>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Tour Itinerary -->
    <?php 
    $sq_package_program = mysqlQuery("select * from car_rental_quotation_program where quotation_id='$quotation_id'");
    $sq_package_count = mysqli_num_rows($sq_package_program);
    if($sq_package_count > 0){ ?>
    <section class="print_sec main_block side_pad mg_tp_30">
      <div class="section_heding mg_tp_20">
        <h2>TOUR ITINERARY</h2>
        <div class="section_heding_img">
          <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
        </div>
      </div>
      <div class="">
        <div class="col-md-12">
          <div class="print_itinenary main_block no-pad no-marg">

            <?php
            $count = 1;
            while ($row_itinarary = mysqli_fetch_assoc($sq_package_program)) {
              $last_child = ($sq_package_count == $count) ? 'last-child' : '';
            ?>
              <section class="print_single_itinenary main_block <?= $last_child ?>">
                <div class="print_itinenary_count print_info_block" style="width:200px;">DAY - <?= $count ?></div>
                <div class="print_itinenary_desciption print_info_block">
                  <div class="print_itinenary_attraction">
                    <span class="print_itinenary_attraction_icon"><svg xmlns="http://www.w3.org/2000/svg" height="12" width="9" viewBox="0 0 384 512"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M0 188.6C0 84.4 86 0 192 0S384 84.4 384 188.6c0 119.3-120.2 262.3-170.4 316.8-11.8 12.8-31.5 12.8-43.3 0-50.2-54.5-170.4-197.5-170.4-316.8zM192 256a64 64 0 1 0 0-128 64 64 0 1 0 0 128z"/></svg></span>
                    <samp class="print_itinenary_attraction_location"><?= $row_itinarary['attraction'] ?></samp>
                  </div>
                  <p><?= $row_itinarary['day_wise_program'] ?></p>
                </div>
                <div class="print_itinenary_details">
                  <div class="print_info_block">
                    <ul class="main_block no-pad">
                      <li class="col-md-12 mg_tp_10 mg_bt_10"><span><svg xmlns="http://www.w3.org/2000/svg" height="12" width="13.5" viewBox="0 0 576 512"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M32 32c17.7 0 32 14.3 32 32l0 224 224 0 0-128c0-17.7 14.3-32 32-32l160 0c53 0 96 43 96 96l0 224c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-64-448 0 0 64c0 17.7-14.3 32-32 32S0 465.7 0 448L0 64C0 46.3 14.3 32 32 32zm80 160a64 64 0 1 1 128 0 64 64 0 1 1 -128 0z"/></svg> : </span><?= $row_itinarary['stay'] ?></li>
                      <li class="col-md-12 mg_tp_10 mg_bt_10"><span><svg xmlns="http://www.w3.org/2000/svg" height="12" width="12" viewBox="0 0 512 512"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M63.9 14.4C63.1 6.2 56.2 0 48 0s-15.1 6.2-16 14.3L17.9 149.7c-1.3 6-1.9 12.1-1.9 18.2 0 45.9 35.1 83.6 80 87.7L96 480c0 17.7 14.3 32 32 32s32-14.3 32-32l0-224.4c44.9-4.1 80-41.8 80-87.7 0-6.1-.6-12.2-1.9-18.2L223.9 14.3C223.1 6.2 216.2 0 208 0s-15.1 6.2-15.9 14.4L178.5 149.9c-.6 5.7-5.4 10.1-11.1 10.1-5.8 0-10.6-4.4-11.2-10.2L143.9 14.6C143.2 6.3 136.3 0 128 0s-15.2 6.3-15.9 14.6L99.8 149.8c-.5 5.8-5.4 10.2-11.2 10.2-5.8 0-10.6-4.4-11.1-10.1L63.9 14.4zM448 0C432 0 320 32 320 176l0 112c0 35.3 28.7 64 64 64l32 0 0 128c0 17.7 14.3 32 32 32s32-14.3 32-32l0-448c0-17.7-14.3-32-32-32z"/></svg> : </span><?= ($row_itinarary['meal_plan'] != '') ? $row_itinarary['meal_plan'] : 'NA' ?></li>
                    </ul>
                  </div>
                </div>
              </section>
            <?php $count++;
            } ?>
          </div>
        </div>
      </div>
    </section>
    <?php } ?>

    <!-- Tour Itinenary -->
   

    <!-- Terms and Conditions -->
    <section class="print_sec main_block side_pad mg_tp_30">
        <?php if (isset($sq_terms_cond['terms_and_conditions'])) { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="section_heding">
                    <h2>Terms and Conditions</h2>
                    <div class="section_heding_img">
                        <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                    </div>
                </div>
                <div class="print_text_bolck">
                    <?= $sq_terms_cond['terms_and_conditions'] ?>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="row mg_tp_30">
            <div class="col-md-7"></div>
            <div class="col-md-5 mg_tp_30">
                <div class="print_quotation_creator text-center">
                    <span>PREPARED BY </span><br><?= $emp_name ?>
                </div>
            </div>
        </div>
    </section>

    </body>

    </html>