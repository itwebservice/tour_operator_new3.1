<?php
//Generic Files
include "../../../model.php";
include "../print_functions.php";
$hotel_accomodation_id = $_GET['booking_id'];

$sq_service_voucher1 = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='Hotel Service Voucher' and active_flag ='Active'"));
$sq_accomodation1 =  mysqlQuery("select * from b2c_sale where booking_id='$hotel_accomodation_id'");
while ($sq_accomodation = mysqli_fetch_assoc($sq_accomodation1)) {

    $enq_data = json_decode($sq_accomodation['enq_data']);
    $guest_data = json_decode($sq_accomodation['guest_data'], true);
    $total_pax = intval($enq_data[0]->adults) + intval($enq_data[0]->chwob) + intval($enq_data[0]->chwb) + intval($enq_data[0]->infant) + intval($enq_data[0]->extra_bed) + intval($enq_data[0]->child);
    $room_cat_string = $enq_data[0]->room_cat;
    $room_count = substr_count($room_cat_string, ',') + 1;

    $hotel_id = $enq_data[0]->hotel_id;

    $sq_hotel = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$hotel_id'"));
    $mobile_no = $encrypt_decrypt->fnDecrypt($sq_hotel['mobile_no'], $secret_key);
    $email_id1 = $encrypt_decrypt->fnDecrypt($sq_hotel['email_id'], $secret_key);

    // Convert to DateTime objects
    $checkinDate = DateTime::createFromFormat('d-m-Y', $enq_data[0]->check_in);
    $checkoutDate = DateTime::createFromFormat('d-m-Y', $enq_data[0]->check_out);
    // Calculate difference
    $interval = $checkinDate->diff($checkoutDate);
    // Output number of nights
    $total_days = $interval->days;

    $sq_customer_name = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_accomodation[customer_id]'"));
    if ($sq_customer_name['type'] == 'Corporate' || $sq_customer_name['type'] == 'B2B') {
        $name = $sq_customer_name['company_name'];
    } else {
        $name = $sq_customer_name['first_name'] . ' ' . $sq_customer_name['last_name'];
    }

    $contact_no = $sq_customer_name['contact_no'];
    $email_id = $sq_customer_name['email_id'];

    $emp_id = $_SESSION['emp_id'];
    $sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$emp_id'"));
    if ($emp_id == '0') {
        $emp_name = 'Admin';
    } else {
        $emp_name = $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];
    }
?>
    <div class="repeat_section main_block">
        <!-- header -->
        <img src="<?= BASE_URL ?>images/vouchers/hotel-service-voucher.jpg" class="watermark">
        <section class="print_header main_block" style="margin-bottom: 200px;">
            <div class="col-md-6 no-pad">
                <div class="print_header_logo">
                    <img src="<?= $admin_logo_url ?>" class="img-responsive mg_tp_10 set_logo_on_img">
                </div>
            </div>
            <div class=" col-md-6 no-pad" style="color: #ddd !important; filter: brightness(1000) !important;">
                <div class="c-w-main print_header_contact text-right"
                    style="color: #ddd !important; filter: brightness(1000) !important;">
                    <span class="c-w-main title"><?php echo $enq_data[0]->hotel_name; ?></span><br>
                    <div class="c-w-main" style="color: #ddd !important; filter: brightness(1000) !important;">
                        <?php echo $sq_hotel['hotel_address']; ?></div>
                    <p class="c-w-main no-marg"><img src="<?= BASE_URL ?>/images/icons/phone-icon.png" alt="" width="15">
                        <?php echo $mobile_no; ?></p>
                    <?php if ($email_id1 != '') { ?><p class="c-w-main"><img src="<?= BASE_URL ?>/images/icons/email-icon.png" alt="" width="15">
                            <?php echo $email_id1; ?></p> <?php } ?>
                </div>
            </div>
        </section>
        <!-- print-detail -->
        <section class="print_sec main_block">
            <div class="section_heding">
                <h2>BOOKING DETAILS</h2>
                <div class="section_heding_img">
                    <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="print_info_block">
                        <ul class="main_block noType">
                            <li class="col-md-3 mg_tp_10 mg_bt_10">
                                <div class="print_quo_detail_block">
                                    <i class="fa fa-hourglass-half" aria-hidden="true"></i><br>
                                    <span>DURATION</span><br>
                                    <?= ($total_days) . ' N' ?><br>
                                </div>
                            </li>
                            <li class="col-md-3 mg_tp_10 mg_bt_10">
                                <div class="print_quo_detail_block">
                                    <i class="fa fa-users" aria-hidden="true"></i><br>
                                    <span>TOTAL GUEST(s)</span><br>
                                    <?= $enq_data[0]->adults ?> Adult(s), <?= $enq_data[0]->chwob ?> Child(wo), <?= $enq_data[0]->chwb ?> Child(wi),
                                    <?= $enq_data[0]->infant ?> Infant(s)<br>
                                </div>
                            </li>
                            <li class="col-md-3 mg_tp_10 mg_bt_10">
                                <div class="print_quo_detail_block">
                                    <i class="fa fa-home" aria-hidden="true"></i><br>
                                    <span>TOTAL ROOM(s)</span><br>
                                    <?php echo $room_count;
                                    echo ($enq_data[0]->extra_bed != '0') ? ' + ' . $enq_data[0]->extra_bed . ' Extra Bed(s)' : ''; ?><br>
                                </div>
                            </li>
                            <li class="col-md-3 mg_tp_10 mg_bt_10">
                                <div class="print_quo_detail_block">
                                    <i class="fa fa-university" aria-hidden="true"></i><br>
                                    <span>ROOM CATEGORY</span><br>
                                    <?= $enq_data[0]->room_cat ?><br>
                                </div>
                            </li>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- BOOKING -->
        <section class="print_sec main_block">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered no-marg" id="tbl_emp_list">
                            <thead>
                                <tr class="table-heading-row">
                                    <th>CUSTOMER_NAME</th>
                                    <th>CHECK-IN </th>
                                    <th>CHECK-OUT</th>
                                    <th>MEAL PLAN</th>
                                    <th>CONTACT</th>
                                    <th>CONFIRMATION ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?= $name . $pass_name ?></td>
                                    <td><?= date('d-m-Y', strtotime($enq_data[0]->check_in)) ?></td>
                                    <td><?= date('d-m-Y', strtotime($enq_data[0]->check_out)) ?></td>
                                    <td><?= $sq_accomodation['meal_plan'] ?></td>
                                    <td><?= $sq_hotel['immergency_contact_no'] ?></td>
                                    <td><?= $sq_accomodation['conf_no'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <?php
        if (isset($sq_service_voucher1['terms_and_conditions'])) { ?>

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
                            <?= $sq_service_voucher1['terms_and_conditions']; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php } ?>

        <p style="float: left;width: 100%;"><b>Note: Please present this service voucher to service provider
                (Hotel/Transport) upon arrival</b></p>

        <!-- Payment Detail -->
        <section class="print_sec main_block">
            <div class="row">
                <div class="col-md-7">
                </div>
                <div class="col-md-5">
                    <div class="print_quotation_creator text-center">
                        <span>Generated BY </span><br><?= $emp_name ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
    </body>

    </html>
<?php } ?>