<?php
$sq_terms_cond = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='Receipt' and active_flag ='Active'"));
$branch_status = $_GET['branch_status'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$branch_admin_id = isset($_SESSION['branch_admin_id']) ? $_SESSION['branch_admin_id'] : 1;
$emp_id = isset($_SESSION['emp_id']) ? $_SESSION['emp_id'] : 1;

if($branch_admin_id != 0){
  $branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_admin_id'"));
  $sq_bank_count = mysqli_num_rows(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id' and active_flag='Active'"));
  $sq_bank_branch = mysqli_fetch_assoc(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id' and active_flag='Active'"));
}
else{
  if($branch_admin_id == ''){
    $branch_admin_id1 = $branch_admin_id;
  }else{
    $branch_admin_id1 = 1;
  }

  $branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_admin_id1'"));
  $sq_bank_count = mysqli_num_rows(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id1' and active_flag='Active'"));
  $sq_bank_branch = mysqli_fetch_assoc(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id1' and active_flag='Active'"));
}

// Get branch-wise logo
$admin_logo_url = get_branch_logo_url($branch_admin_id);

$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$emp_id'"));
if ($emp_id == '0' || $emp_id == '') {
  $emp_name = 'Admin';
} else{
  $emp_name = $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];
}
?>

<div class="repeat_section main_block">

  <section class="print_sec_tp_s main_block">

    <!-- invloice_receipt_hedaer_top-->

    <div class="main_block inv_rece_header_top header_seprator header_seprator_4 mg_bt_10">

      <div class="row">

        <div class="col-md-4">

          <div class="inv_rece_header_left">

            <div class="inv_rece_header_logo">

              <img src="<?php echo $admin_logo_url ?>" class="img-responsive">

            </div>

          </div>

        </div>

        <div class="col-md-4 text-center pd_tp_5">

          <div class="inv_rece_header_left">

            <div class="inv_rec_no_detail">

              <h2 class="inv_rec_no_title font_5 font_s_21 no-marg no-pad">RECEIPT</h2>

              <h4 class="inv_rec_no font_5 font_s_14 no-marg no-pad"><?php echo $payment_id; ?></h4>

            </div>

          </div>

        </div>

        <div class="col-md-4 last_h_sep_border_lt">

          <div class="inv_rece_header_right text-right">

            <ul class="no-pad no-marg font_s_12 noType">

              <li>
                <h3 class=" font_5 font_s_16 no-marg no-pad caps_text"><?php echo $app_name; ?></h3>
              </li>

              <li>
                <p><?php echo ($branch_status == 'yes' ) ? $branch_details['address1'] . ',' . $branch_details['address2'] . ',' . $branch_details['city'] : $app_address ?></p>
              </li>

              <li><svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" height="12" width="12" viewBox="0 0 512 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M160.2 25C152.3 6.1 131.7-3.9 112.1 1.4l-5.5 1.5c-64.6 17.6-119.8 80.2-103.7 156.4 37.1 175 174.8 312.7 349.8 349.8 76.3 16.2 138.8-39.1 156.4-103.7l1.5-5.5c5.4-19.7-4.7-40.3-23.5-48.1l-97.3-40.5c-16.5-6.9-35.6-2.1-47 11.8l-38.6 47.2C233.9 335.4 177.3 277 144.8 205.3L189 169.3c13.9-11.3 18.6-30.4 11.8-47L160.2 25z"/></svg> <?php echo ($branch_status == 'yes' )  ? $branch_details['contact_no'] : $app_contact_no ?></li>

              <li><svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" height="12" width="12" viewBox="0 0 512 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M48 64c-26.5 0-48 21.5-48 48 0 15.1 7.1 29.3 19.2 38.4l208 156c17.1 12.8 40.5 12.8 57.6 0l208-156c12.1-9.1 19.2-23.3 19.2-38.4 0-26.5-21.5-48-48-48L48 64zM0 196L0 384c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-188-198.4 148.8c-34.1 25.6-81.1 25.6-115.2 0L0 196z"/></svg><?php echo ($branch_status == 'yes'  && $branch_details['email_id'] != '') ? $branch_details['email_id'] : $app_email_id; ?></li>

              <li><span class="font_5">TAX NO : </span><?php echo ($service_tax_no=='') ? 'NA' : strtoupper($service_tax_no); ?></li>

            </ul>

          </div>

        </div>

      </div>

    </div>







    <!-- invloice_receipt_bottom-->

    <div class="main_block inv_rece_header_bottom mg_tp_10">

      <div class="row">
        <div class="col-md-7">

        <?php if ($customer_id != '' && $customer_id != 0) { ?>
          <div class="inv_rece_header_left mg_bt_10">

            <ul class="no-marg no-pad noType">

              <li>
                <h3 class="title font_5 font_s_16 no-marg no-pad">TO,</h3>
              </li>

              <li>
                <h3 class=" font_5 font_s_14 no-marg no-pad"><?php echo  $sq_customer['company_name']; ?></h3>
              </li>

              <li><svg xmlns="http://www.w3.org/2000/svg" height="14" width="12.25" viewBox="0 0 448 512"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M224 248a120 120 0 1 0 0-240 120 120 0 1 0 0 240zm-29.7 56C95.8 304 16 383.8 16 482.3 16 498.7 29.3 512 45.7 512l356.6 0c16.4 0 29.7-13.3 29.7-29.7 0-98.5-79.8-178.3-178.3-178.3l-59.4 0z"/></svg> : <?php if ($customer_id != '' && $customer_id != 0) {
                                                  echo  $sq_customer['first_name'] . ' ' . $sq_customer['last_name'].$pass_name;
                                                } else {
                                                  echo $booking_id;
                                                } ?></li>



            </ul>

          </div>
        <?php } ?>

        </div>

        <div class="col-md-5">

          <div class="inv_rece_header_right mg_bt_10">

            <ul class="no-marg no-pad noType">

              <li><span class="font_5">RECEIPT FOR </span>: <?php echo $receipt_type; ?></li>

              <?php if ($payment_date != '') { ?><li><span class="font_5">RECEIPT DATE </span>: <?php echo date('d-m-Y', strtotime($receipt_date)); ?></li><?php } ?>

              <li><span class="font_5">TAX NO </span> : <?php echo ($customer_id != '' && $customer_id != 0 && isset($sq_customer['service_tax_no'])) ? $sq_customer['service_tax_no'] : 'NA'; ?></li>

            </ul>

          </div>

        </div>

      </div>

    </div>

  </section>