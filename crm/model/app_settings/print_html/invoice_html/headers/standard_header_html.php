<?php
global $service_charge_switch;
$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
$sq_settings = mysqli_fetch_assoc(mysqlQuery("select * from app_settings"));
$sq_state = mysqli_fetch_assoc(mysqlQuery("select * from state_master where id='$sq_settings[state_id]'"));
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$branch_status = $_GET['branch_status'];
$branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_admin_id'"));

// Get branch-wise logo
$admin_logo_url = get_branch_logo_url($branch_admin_id);

$sq_terms_cond = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='Invoice' and active_flag ='Active'")); 
$emp_id = isset($_SESSION['emp_id']) ? $_SESSION['emp_id'] : 1;
$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$emp_id'"));
if($emp_id == '0'){ $emp_name = 'Admin';}
else { $emp_name = $sq_emp['first_name'].' ' .$sq_emp['last_name']; }
?>
<section class="print_sec_tp_s main_block ">
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
          <h2 class="inv_rec_no_title font_5 font_s_21 no-marg no-pad">INVOICE</h2>
          <h4 class="inv_rec_no font_5 font_s_14 no-marg no-pad"><?php echo $invoice_no; ?></h4>
        </div>
      </div>
    </div>
    <div class="col-md-4 last_h_sep_border_lt">
      <div class="inv_rece_header_right text-right">
        <ul class="no-pad no-marg font_s_12 noType">
          <li><h3 class=" font_5 font_s_16 no-marg no-pad caps_text"><?php echo $app_name; ?></h3></li>
          <li><p><?php echo ($branch_status=='yes') ? $branch_details['address1'].','.$branch_details['address2'].','.$branch_details['city'] : $app_address ?></p></li>
          <li><svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" height="12" width="12" viewBox="0 0 512 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M160.2 25C152.3 6.1 131.7-3.9 112.1 1.4l-5.5 1.5c-64.6 17.6-119.8 80.2-103.7 156.4 37.1 175 174.8 312.7 349.8 349.8 76.3 16.2 138.8-39.1 156.4-103.7l1.5-5.5c5.4-19.7-4.7-40.3-23.5-48.1l-97.3-40.5c-16.5-6.9-35.6-2.1-47 11.8l-38.6 47.2C233.9 335.4 177.3 277 144.8 205.3L189 169.3c13.9-11.3 18.6-30.4 11.8-47L160.2 25z"/></svg> <?php echo ($branch_status=='yes') ? 
          $branch_details['contact_no'] : $app_contact_no ?></li>
          <li><svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" height="12" width="12" viewBox="0 0 512 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M48 64c-26.5 0-48 21.5-48 48 0 15.1 7.1 29.3 19.2 38.4l208 156c17.1 12.8 40.5 12.8 57.6 0l208-156c12.1-9.1 19.2-23.3 19.2-38.4 0-26.5-21.5-48-48-48L48 64zM0 196L0 384c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-188-198.4 148.8c-34.1 25.6-81.1 25.6-115.2 0L0 196z"/></svg><?php echo ($branch_status=='yes' && $branch_details['email_id'] != '') ? $branch_details['email_id'] : $app_email_id; ?></li>
          <li><span class="font_5">TAX NO : </span><?php echo ($service_tax_no=='') ? 'NA' : strtoupper($service_tax_no); ?></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<hr class="no-marg">
<!-- invloice_receipt_bottom-->
<div class="main_block inv_rece_header_bottom mg_tp_10">
  <div class="row">
    <div class="col-md-7">
      <?php
      if($customer_id != '' && $customer_id != 0){
      ?>
      <div class="inv_rece_header_left mg_bt_10">
        <ul class="no-marg no-pad noType">
          <li><h3 class="title font_5 font_s_16 no-marg no-pad">TO,</h3></li>
          <li><h3 class=" font_5 font_s_14 no-marg no-pad"><?php echo  $sq_customer['company_name'] ?></h3></li>
          <li><svg xmlns="http://www.w3.org/2000/svg" height="14" width="12.25" viewBox="0 0 448 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M224 248a120 120 0 1 0 0-240 120 120 0 1 0 0 240zm-29.7 56C95.8 304 16 383.8 16 482.3 16 498.7 29.3 512 45.7 512l356.6 0c16.4 0 29.7-13.3 29.7-29.7 0-98.5-79.8-178.3-178.3-178.3l-59.4 0z"/></svg> : <?php echo $sq_customer['first_name'].' '.$sq_customer['last_name']; ?></li>
          <li><svg xmlns="http://www.w3.org/2000/svg" height="14" width="10.5" viewBox="0 0 384 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M0 188.6C0 84.4 86 0 192 0S384 84.4 384 188.6c0 119.3-120.2 262.3-170.4 316.8-11.8 12.8-31.5 12.8-43.3 0-50.2-54.5-170.4-197.5-170.4-316.8zM192 256a64 64 0 1 0 0-128 64 64 0 1 0 0 128z"/></svg> : <?php echo $sq_customer['address']; ?></li>
          <li><svg xmlns="http://www.w3.org/2000/svg" height="14" width="10.5" viewBox="0 0 384 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M32 32C32 14.3 46.3 0 64 0L320 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-29.5 0 10.3 134.1c37.1 21.2 65.8 56.4 78.2 99.7l3.8 13.4c2.8 9.7 .8 20-5.2 28.1S362 352 352 352L32 352c-10 0-19.5-4.7-25.5-12.7s-8-18.4-5.2-28.1L5 297.8c12.4-43.3 41-78.5 78.2-99.7L93.5 64 64 64C46.3 64 32 49.7 32 32zM160 400l64 0 0 112c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-112z"/></svg> : <?php echo $sq_customer['address2']; ?></li>
          <li><svg xmlns="http://www.w3.org/2000/svg" height="14" width="14" viewBox="0 0 512 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M55.7 199.7l30.9 30.9c6 6 14.1 9.4 22.6 9.4l21.5 0c8.5 0 16.6 3.4 22.6 9.4l29.3 29.3c6 6 9.4 14.1 9.4 22.6l0 37.5c0 8.5 3.4 16.6 9.4 22.6l13.3 13.3c6 6 9.4 14.1 9.4 22.6l0 18.7c0 17.7 14.3 32 32 32s32-14.3 32-32l0-2.7c0-8.5 3.4-16.6 9.4-22.6l45.3-45.3c6-6 9.4-14.1 9.4-22.6l0-34.7c0-17.7-14.3-32-32-32l-82.7 0c-8.5 0-16.6-3.4-22.6-9.4l-16-16c-4.2-4.2-6.6-10-6.6-16 0-12.5 10.1-22.6 22.6-22.6l34.7 0c12.5 0 22.6-10.1 22.6-22.6 0-6-2.4-11.8-6.6-16l-19.7-19.7C242 130 240 125.1 240 120s2-10 5.7-13.7l17.3-17.3c5.8-5.8 9.1-13.7 9.1-21.9 0-7.2-2.4-13.7-6.4-18.9-3.2-.1-6.4-.2-9.6-.2-95.4 0-175.7 64.2-200.3 151.7zM464 256c0-34.6-8.4-67.2-23.4-95.8-6.4 .9-12.7 3.9-17.9 9.1l-13.4 13.4c-6 6-9.4 14.1-9.4 22.6l0 34.7c0 17.7 14.3 32 32 32l24.1 0c2.5 0 5-.3 7.3-.8 .4-5 .5-10.1 .5-15.2zM0 256a256 256 0 1 1 512 0 256 256 0 1 1 -512 0z"/></svg> : <?php echo $sq_customer['city']; ?></li>
          <li><svg xmlns="http://www.w3.org/2000/svg" height="14" width="14" viewBox="0 0 512 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M160.2 25C152.3 6.1 131.7-3.9 112.1 1.4l-5.5 1.5c-64.6 17.6-119.8 80.2-103.7 156.4 37.1 175 174.8 312.7 349.8 349.8 76.3 16.2 138.8-39.1 156.4-103.7l1.5-5.5c5.4-19.7-4.7-40.3-23.5-48.1l-97.3-40.5c-16.5-6.9-35.6-2.1-47 11.8l-38.6 47.2C233.9 335.4 177.3 277 144.8 205.3L189 169.3c13.9-11.3 18.6-30.4 11.8-47L160.2 25z"/></svg> : <?php echo $contact_no = $encrypt_decrypt->fnDecrypt($sq_customer['contact_no'], $secret_key);; ?></li>
          <li><span class="font_5">TAX NO : </span> <?php echo $sq_customer['service_tax_no']; ?></li>
        </ul>
      </div>
      <?php } ?>
    </div>
    <div class="col-md-5">
      <div class="inv_rece_header_right">
        <ul class="no-marg no-pad noType">
          <li><span class="font_5">INVOICE FOR </span>: <?php echo $service_name; ?></li>
          <li><span class="font_5">INVOICE DATE </span>: <?php echo $invoice_date; ?></li>
          <li><span class="font_5">PLACE OF SUPPLY </span>: <?php echo $sq_state['state_name']; ?></li>
          <li><span class="font_5">SAC Code </span>: <?php echo $sac_code; ?></li>
          <?php    if ($service_name =='Flight Invoice'){ 
            if($ticket_type=='1'){

              $ticket_stus="Reissue Ticket";
            }else{
              $ticket_stus="";
            }
            
            ?>

          <li><span class="font_5" style="font-size:15px !important;"><?= $ticket_stus ?></span></li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>
</div>
</section>