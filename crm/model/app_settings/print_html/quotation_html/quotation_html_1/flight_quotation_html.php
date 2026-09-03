<?php
//Generic Files
include "../../../../model.php";
include "printFunction.php";
global $app_quot_img, $currency, $quot_note;

$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];

// Get branch-wise logo and QR code
$admin_logo_url = get_branch_logo_url($branch_admin_id);
$branch_qr_url = get_branch_qr_url($branch_admin_id);
$sq = mysqli_fetch_assoc(mysqlQuery("select * from branch_assign where link='package_booking/quotation/car_flight/flight/index.php'"));
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
$sq_terms_cond = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='Flight Quotation' and active_flag ='Active'"));

$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from flight_quotation_master where quotation_id='$quotation_id'"));
$sq_login = mysqli_fetch_assoc(mysqlQuery("select * from roles where id='$sq_quotation[login_id]'"));
$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_login[emp_id]'"));
$sq_plane = mysqli_fetch_assoc(mysqlQuery("select * from flight_quotation_plane_entries where quotation_id='$quotation_id'"));
$sq_airline1 = mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$sq_plane[airline_name]'"));
$quotation_date = $sq_quotation['quotation_date'];
$yr = explode("-", $quotation_date);
$year = $yr[0];

if ($sq_emp_info['first_name'] == '') {
  $emp_name = 'Admin';
} else {
  $emp_name = $sq_emp_info['first_name'] . ' ' . $sq_emp_info['last_name'];
}

$tax_show = '';
$newBasic = $basic_cost1 = $sq_quotation['subtotal'];
$service_charge = $sq_quotation['service_charge'];
$bsmValues = json_decode($sq_quotation['bsm_values']);
//////////////////Service Charge Rules
$service_tax_amount = 0;
$percent = '';
if ($sq_quotation['service_tax'] !== 0.00 && ($sq_quotation['service_tax']) !== '') {
  $service_tax_subtotal1 = explode(',', $sq_quotation['service_tax']);
  for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
    $service_tax = explode(':', $service_tax_subtotal1[$i]);
    $service_tax_amount +=  $service_tax[2];
    $percent .= $service_tax[0]  . $service_tax[1] . ', ';
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
// $total_tax_amount_show = currency_conversion($currency, $currency, (float)($service_tax_amount) + (float)($markupservice_tax_amount) + $sq_quotation['roundoff']);

$tax_cost =  (float)($service_tax_amount) + (float)($markupservice_tax_amount) + $sq_quotation['roundoff'];

            $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $tax_cost);

		if ($sq_quotation['currency_code'] != '0' && $currency != $sq_quotation['currency_code'] && $tax_cost != $currency_amount1) {
	 $total_tax_amount_show =  $currency_amount1 ;
	} else {
	$total_tax_amount_show = $tax_cost;
	}



if (($bsmValues[0]->service != '' || $bsmValues[0]->basic != '')  && $bsmValues[0]->markup != '') {
  $tax_show = '';
  $newBasic = $basic_cost1 + $sq_quotation['markup_cost'] + $markupservice_tax_amount + $service_charge + $service_tax_amount;
} elseif (($bsmValues[0]->service == '' || $bsmValues[0]->basic == '')  && $bsmValues[0]->markup == '') {
  $tax_show = $percent . ' ' . ($total_tax_amount_show);
  $newBasic = $basic_cost1 + $sq_quotation['markup_cost'] + $service_charge;
} elseif (($bsmValues[0]->service != '' || $bsmValues[0]->basic != '') && $bsmValues[0]->markup == '') {
  $tax_show = $percent . ' ' . ($markupservice_tax_amount);
  $newBasic = $basic_cost1 + $sq_quotation['markup_cost'] + $service_charge + $service_tax_amount;
} else {
  $tax_show = $percent . ' ' . ($service_tax_amount);
  $newBasic = $basic_cost1 + $sq_quotation['markup_cost'] + $service_charge + $markupservice_tax_amount;
}
// $quotation_cost = currency_conversion($currency, $currency, $sq_quotation['quotation_cost']);

$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['quotation_cost']);

		if ($sq_quotation['currency_code'] != '0' && $currency != $sq_quotation['currency_code'] && $sq_quotation['quotation_cost'] != $currency_amount1) {
	$quotation_cost = $currency_amount1 ;
	} else {
	$quotation_cost = $sq_quotation['quotation_cost'];
	}
?>

<section class="headerPanel main_block">
  <div class="headerImage">
    <img src="<?= $app_quot_img ?>" class="img-responsive">
    <div class="headerImageOverLay"></div>
  </div>

  <!-- Header -->
  <section class="print_header main_block side_pad mg_tp_30">
    <div class="col-md-4 no-pad">
      <div class="print_header_logo">
        <img src="<?= $admin_logo_url ?>" class="img-responsive mg_tp_10">
      </div>
    </div>
    <div class="col-md-4 no-pad text-center mg_tp_30">
      <span class="title"><svg xmlns="http://www.w3.org/2000/svg" height="18" width="18" viewBox="0 0 512 512" style="margin-right: 5px;"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L368 46.1 465.9 144 490.3 119.6c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L432 177.9 334.1 80 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z"/></svg> FLIGHT QUOTATION</span>
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
                  <span>QUOTATION ID</span><br>
                  <?= get_quotation_id($quotation_id, $year) ?><br>
                </div>
              </li>
              <li class="col-md-3 mg_tp_10 mg_bt_10">
                <div class="print_quo_detail_block">
                <svg xmlns="http://www.w3.org/2000/svg" height="14" width="17.5" viewBox="0 0 640 512"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M320 16a104 104 0 1 1 0 208 104 104 0 1 1 0-208zM96 88a72 72 0 1 1 0 144 72 72 0 1 1 0-144zM0 416c0-70.7 57.3-128 128-128 12.8 0 25.2 1.9 36.9 5.4-32.9 36.8-52.9 85.4-52.9 138.6l0 16c0 11.4 2.4 22.2 6.7 32L32 480c-17.7 0-32-14.3-32-32l0-32zm521.3 64c4.3-9.8 6.7-20.6 6.7-32l0-16c0-53.2-20-101.8-52.9-138.6 11.7-3.5 24.1-5.4 36.9-5.4 70.7 0 128 57.3 128 128l0 32c0 17.7-14.3 32-32 32l-86.7 0zM472 160a72 72 0 1 1 144 0 72 72 0 1 1 -144 0zM160 432c0-88.4 71.6-160 160-160s160 71.6 160 160l0 16c0 17.7-14.3 32-32 32l-256 0c-17.7 0-32-14.3-32-32l0-16z"/></svg><br>
                  <span>TOTAL SEATS</span><br>
                  <?= $sq_plane['total_adult'] + $sq_plane['total_child'] + $sq_plane['total_infant'] ?><br>
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
      <h2>CUSTOMER DETAILS</h2>
      <div class="section_heding_img">
        <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
      </div>
    </div>
    <div class="row">
      <div class="col-md-7 mg_bt_20">
      </div>
      <div class="col-md-5 mg_bt_20">
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="print_info_block">
          <ul class="print_info_list">
            <li class="col-md-6 mg_tp_10 mg_bt_10">
              <svg xmlns="http://www.w3.org/2000/svg" height="14" width="7.5" viewBox="0 0 256 512" style="margin-right: 5px;"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M249.3 235.8c10.2 12.6 9.5 31.1-2.2 42.8l-128 128c-9.2 9.2-22.9 11.9-34.9 6.9S64.5 396.9 64.5 384l0-256c0-12.9 7.8-24.6 19.8-29.6s25.7-2.2 34.9 6.9l128 128 2.2 2.4z"/></svg><span>CUSTOMER NAME :</span><?= $sq_quotation['customer_name'] ?></li>
          </ul>
          <ul class="print_info_list">
            <li class="col-md-6 mg_tp_10 mg_bt_10"><svg xmlns="http://www.w3.org/2000/svg" height="14" width="7.5" viewBox="0 0 256 512" style="margin-right: 5px;"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M249.3 235.8c10.2 12.6 9.5 31.1-2.2 42.8l-128 128c-9.2 9.2-22.9 11.9-34.9 6.9S64.5 396.9 64.5 384l0-256c0-12.9 7.8-24.6 19.8-29.6s25.7-2.2 34.9 6.9l128 128 2.2 2.4z"/></svg><span>CONTACT NUMBER :</span> <?= $sq_quotation['mobile_no'] ?></li>
            <li class="col-md-6 mg_tp_10 mg_bt_10"><svg xmlns="http://www.w3.org/2000/svg" height="14" width="7.5" viewBox="0 0 256 512" style="margin-right: 5px;"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M249.3 235.8c10.2 12.6 9.5 31.1-2.2 42.8l-128 128c-9.2 9.2-22.9 11.9-34.9 6.9S64.5 396.9 64.5 384l0-256c0-12.9 7.8-24.6 19.8-29.6s25.7-2.2 34.9 6.9l128 128 2.2 2.4z"/></svg><span>E-MAIL ID :</span> <?= $sq_quotation['email_id'] ?></li>
          </ul>
        </div>
      </div>
    </div>
  </section>
  <!-- Flight -->
  <?php
  $sq_plane_count = mysqli_num_rows(mysqlQuery("select * from flight_quotation_plane_entries where quotation_id='$quotation_id'"));
  if ($sq_plane_count > 0) {
  ?>
    <section class="print_sec main_block side_pad mg_tp_30">
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
                  <th>From_Sector</th>
                  <th>To_Sector</th>
                  <th>Airline</th>
                  <th>Class</th>
                  <th>Departure_D/T</th>
                  <th>Arrival_D/T</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sq_plane = mysqlQuery("select * from flight_quotation_plane_entries where quotation_id='$quotation_id'");
                while ($row_plane = mysqli_fetch_assoc($sq_plane)) {
                  $sq_airline = mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$row_plane[airline_name]'")); ?>
                  <tr>
                    <td><?= $row_plane['from_location'] ?></td>
                    <td><?= $row_plane['to_location'] ?></td>
                    <td><?= ($sq_airline['airline_name'] != '') ? $sq_airline['airline_name'] . ' (' . $sq_airline['airline_code'] . ')' : 'NA' ?></td>
                    <td><?= ($row_plane['class'] != '') ? $row_plane['class'] : 'NA' ?></td>
                    <td><?= get_datetime_user($row_plane['dapart_time']) ?></td>
                    <td><?= get_datetime_user($row_plane['arraval_time']) ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  <?php } ?>

  <!-- Costing -->
  <section class="print_sec main_block side_pad mg_tp_30">
    <div class="row">
      <div class="col-md-6">
        <div class="section_heding">
          <h2>COSTING DETAILS</h2>
          <div class="section_heding_img">
            <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
          </div>
        </div>
        <div class="print_info_block">
          <ul class="main_block">
            <?php
            $fare_cost = currency_conversion($currency, $currency, ((float)($newBasic)));
            
            $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], (float)($newBasic));

		if ($sq_quotation['currency_code'] != '0' && $currency != $sq_quotation['currency_code'] && (float)($newBasic) != $currency_amount1) {
	 $fare_cost = $currency_amount1 ;
	} else {
	 $fare_cost = (float)($newBasic);
	}
            ?>
            <li class="col-md-12 mg_tp_10 mg_bt_10"><span>TOTAL FARE : </span><?= $fare_cost ?></li>
            <li class="col-md-12 mg_tp_10 mg_bt_10"><span>TAX : </span><?= 
            
            $tax_show ?></li>
            <li class="col-md-12 mg_tp_10 mg_bt_10"><span>QUOTATION COST : </span><?= $quotation_cost ?></li>
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
            <div class="<?= check_qr($branch_admin_id) ? 'col-md-7' : 'col-md-12' ?>">
              <table style="width:100%; border-collapse:collapse; margin:0;">
                <tr>
                  <td style="font-weight:500; white-space:nowrap; padding:6px 12px 6px 0; width:42%; vertical-align:top;">BANK NAME :</td>
                  <td style="padding:6px 0; vertical-align:top;"><?= ($sq_bank_count > 0 || $sq_bank_branch['bank_name'] != '') ? $sq_bank_branch['bank_name'] : $bank_name_setting ?></td>
                </tr>
                <tr>
                  <td style="font-weight:500; white-space:nowrap; padding:6px 12px 6px 0; vertical-align:top;">A/C TYPE :</td>
                  <td style="padding:6px 0; vertical-align:top;"><?= ($sq_bank_count > 0 || $sq_bank_branch['account_type'] != '') ? $sq_bank_branch['account_type'] : $acc_name ?></td>
                </tr>
                <tr>
                  <td style="font-weight:500; white-space:nowrap; padding:6px 12px 6px 0; vertical-align:top;">BRANCH :</td>
                  <td style="padding:6px 0; vertical-align:top;"><?= ($sq_bank_count > 0 || $sq_bank_branch['branch_name'] != '') ? $sq_bank_branch['branch_name'] : $bank_branch_name ?></td>
                </tr>
                <tr>
                  <td style="font-weight:500; white-space:nowrap; padding:6px 12px 6px 0; vertical-align:top;">A/C NO :</td>
                  <td style="padding:6px 0; vertical-align:top;"><?= ($sq_bank_count > 0 || $sq_bank_branch['account_no'] != '') ? $sq_bank_branch['account_no'] : $bank_acc_no ?></td>
                </tr>
                <tr>
                  <td style="font-weight:500; white-space:nowrap; padding:6px 12px 6px 0; vertical-align:top;">BANK ACCOUNT NAME :</td>
                  <td style="padding:6px 0; vertical-align:top;"><?= ($sq_bank_count > 0 || $sq_bank_branch['account_name'] != '') ? $sq_bank_branch['account_name'] : $bank_account_name ?></td>
                </tr>
                <tr>
                  <td style="font-weight:500; white-space:nowrap; padding:6px 12px 6px 0; vertical-align:top;">SWIFT CODE :</td>
                  <td style="padding:6px 0; vertical-align:top;"><?= ($sq_bank_count > 0 || $sq_bank_branch['swift_code'] != '') ? strtoupper($sq_bank_branch['swift_code']) : strtoupper($bank_swift_code) ?></td>
                </tr>
              </table>
            </div>
            <?php
            if (check_qr($branch_admin_id)) { ?>
              <div class="col-md-5 text-center">
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

  <!-- Terms and Conditions -->
  <?php if (isset($sq_terms_cond['terms_and_conditions']) || isset($quot_note)) { ?>
    <section class="print_sec main_block side_pad mg_tp_30">
      <?php if (isset($sq_terms_cond['terms_and_conditions'])) { ?>
        <div class="row">
          <div class="col-md-12">
            <div class="section_heding">
              <h2>TERMS AND CONDITIONS</h2>
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
      <?php
      if (isset($quot_note)) { ?>
        <div class="row mg_tp_10">
          <div class="col-md-12">
            <?php echo $quot_note; ?>
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
  <?php } ?>

  </body>

  </html>