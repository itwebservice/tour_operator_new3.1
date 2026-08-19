<?php
include "../../../model.php";
include "../print_functions.php";
require("../../../../classes/convert_amount_to_word.php");

$ticket_id = $_GET['ticket_id'];
$invoice_date = $_GET['invoice_date'];
$branch_status = $_GET['branch_status'];
$emp_id = isset($_SESSION['emp_id']) ? $_SESSION['emp_id'] : 0;
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';


$sq_visa_info = mysqli_fetch_assoc(mysqlQuery("select * from ticket_master where ticket_id='$ticket_id'"));
$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_visa_info[customer_id]'"));
$branch_admin_id = isset($_SESSION['branch_admin_id']) ? $_SESSION['branch_admin_id'] : $sq_visa_info['branch_admin_id'];
if ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') {
    $name = $sq_customer['company_name'];
} else {
    $name = $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
}
$branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_admin_id'"));

// Get branch-wise logo
$admin_logo_url = get_branch_logo_url($branch_admin_id);

$sq_terms_cond = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='Flight E-Ticket' and active_flag ='Active'"));

$sq_flight = mysqli_fetch_assoc(mysqlQuery("select first_name,last_name from ticket_master_entries where ticket_id='$ticket_id' "));
$guest_name = (($sq_customer['type']=='Corporate' || $sq_customer['type'] == 'B2B') && $sq_flight['first_name']!='') ? '('.$sq_flight['first_name'].' '.$sq_flight['last_name'].')' : '';
?>
</style>
<?php
///////////////////////////Meal plan and seat nos trip n passengerwise//////////////////////////////
$trip_seat_arr = array();
$trip_meal_arr = array();
$trip_status_arr = array();
$trip_pass_arr = array();
$dep_city1_arr = array();$arr_city1_arr = array();
$sq_fcity_arr = array(); $sq_tcity_arr = array();
$dep_city_arr = array(); $arr_city_arr = array(); $op_by_arr = array(); $ticket_status_arr = array();
$airline_img_arr = array(); $time1_arr = array(); $time2_arr = array(); $day1_arr = array(); $day2_arr = array();
$airlines_name_arr = array(); $aircraft_type_arr = array(); $class_arr = array(); $date1_arr = array(); $date2_arr = array();
$departure_terminal_arr = array(); $arrival_terminal_arr = array(); $flight_duration_arr = array(); $layover_time_arr = array();
$row_passenger = mysqlQuery("select * from ticket_master_entries where ticket_id = '$ticket_id' and status!='Cancel'");
while ($row_passenger1 = mysqli_fetch_assoc($row_passenger)) {
    
    $seat_nos = explode('/',$row_passenger1['seat_no']);
    $meal_plans = explode('/',$row_passenger1['meal_plan']);
    $i = 0;
    $sq_ticket_trip = mysqlQuery("SELECT * FROM ticket_trip_entries WHERE passenger_id='$row_passenger1[entry_id]'");
    while ($row_trip = mysqli_fetch_assoc($sq_ticket_trip)) {

        $tseat_no = isset($seat_nos[$i]) ? $seat_nos[$i] : '';
        $tmeal_plans = isset($meal_plans[$i]) ? $meal_plans[$i] : '';
        array_push($trip_seat_arr,$tseat_no);
        array_push($trip_meal_arr,$tmeal_plans);
        array_push($trip_status_arr,$row_trip['status']);
        array_push($trip_pass_arr,$row_passenger1['entry_id']);

        $date1 = get_datetime_user($row_trip['departure_datetime']);
        $time1 = explode(' ', $date1);
        $date2 = get_datetime_user($row_trip['arrival_datetime']);
        $time2 = explode(' ', $date2);
        array_push($time1_arr,$time1[1]);
        array_push($time2_arr,$time2[1]);
        array_push($date1_arr,$date1);
        array_push($date2_arr,$date2);

        $timestamp2 = strtotime($row_trip['departure_datetime']);
        $day1 = date('D', $timestamp2);
        $timestamp1 = strtotime($row_trip['arrival_datetime']);
        $day2 = date('D', $timestamp1);
        array_push($day1_arr,$day1);
        array_push($day2_arr,$day2);
    
        $sq_fcity = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$row_trip[from_city]'"));
        array_push($sq_fcity_arr,$sq_fcity['city_name']);
        $sq_tcity = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$row_trip[to_city]'"));
        array_push($sq_tcity_arr,$sq_tcity['city_name']);
        $sirline_string = '';
        $flight_no = ($row_trip['flight_no'] != '') ? strtoupper($row_trip['flight_no']) : '';
        $op_by = ($row_trip['operating_carrier'] != '') ? $flight_no . ' (Operated By: ' . $row_trip['operating_carrier'] . ')' : $flight_no;
        $airline_img = '';
        if ($row_trip['airline_id'] != 0) {
            $sq_air_img = mysqli_fetch_assoc(mysqlQuery("select image from airline_master where airline_id='$row_trip[airline_id]'"));
            $airline_img = ($sq_air_img['image'] != '') ? $sq_air_img['image'] : '';
        }
        array_push($dep_city_arr,$row_trip['departure_city']);
        array_push($arr_city_arr,$row_trip['arrival_city']);

        $dep_city = explode('(', $row_trip['departure_city']);
        $arr_city = explode('(', $row_trip['arrival_city']);
        $dep_city1 = explode(')', $dep_city[1]);
        $arr_city1 = explode(')', $arr_city[1]);

        array_push($dep_city1_arr,$dep_city1[0]);
        array_push($arr_city1_arr,$arr_city1[0]);

        array_push($airlines_name_arr,$row_trip['airlines_name']);
        array_push($aircraft_type_arr,$row_trip['aircraft_type']);
        array_push($class_arr,$row_trip['class']);
        array_push($op_by_arr,$op_by);
        array_push($ticket_status_arr,$row_trip['ticket_status']);
        array_push($airline_img_arr,$airline_img);
        array_push($departure_terminal_arr,$row_trip['departure_terminal']);
        array_push($arrival_terminal_arr,$row_trip['arrival_terminal']);
        array_push($flight_duration_arr,$row_trip['flight_duration']);
        array_push($layover_time_arr,$row_trip['layover_time']);

        $i++;
    }
}
///////////////////////////////////////////////////// END ///////////////////////////////////////////////////
$trip_count = 0;
$sq_ticket_trip = mysqlQuery("SELECT * FROM ticket_trip_entries WHERE ticket_id='$ticket_id' ");
while ($row_trip = mysqli_fetch_assoc($sq_ticket_trip)) {

    if($trip_status_arr[$trip_count] != 'Cancel' && $dep_city_arr[$trip_count] != ''){
        
        $seat_counter = isset($trip_seat_arr[$trip_count+1]) ? $trip_seat_arr[$trip_count+1] : '';
        $meal_counter = isset($trip_meal_arr[$trip_count+1]) ? $trip_meal_arr[$trip_count+1] : '';
        $seat_no_test = ($row_trip['status'] != 'Cancel') ? $trip_seat_arr[$trip_count] : $seat_counter;
        $meal_plan_test = ($row_trip['status'] != 'Cancel') ? $trip_meal_arr[$trip_count] : $meal_counter;
    ?>
    <!-- header -->
    <section class="repeat_section main_block">
    <section class="print_header main_block" style="margin-bottom:0;">
        <div class="col-md-4 no-pad">
            <div class="print_header_logo">
                <img src="<?php echo $admin_logo_url; ?>" class="img-responsive mg_tp_10" style="max-width: 210px; max-height: 90px; object-fit: contain;">
            </div>
        </div>
        <div class="col-md-4 no-pad">
            <div class="text-center">
                <h3>E-Ticket</h3>
            </div>
        </div>
        <div class="col-md-4 no-pad">
            <div class="print_header_contact">
                <span class="title"><?php echo $app_name; ?></span><br>
                <p><?php echo ($branch_status == 'yes') ? $branch_details['address1'] . ',' . $branch_details['address2'] . ',' . $branch_details['city'] : $app_address ?>
                </p>
                <p class="no-marg"><svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" height="12" width="12" viewBox="0 0 512 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M160.2 25C152.3 6.1 131.7-3.9 112.1 1.4l-5.5 1.5c-64.6 17.6-119.8 80.2-103.7 156.4 37.1 175 174.8 312.7 349.8 349.8 76.3 16.2 138.8-39.1 156.4-103.7l1.5-5.5c5.4-19.7-4.7-40.3-23.5-48.1l-97.3-40.5c-16.5-6.9-35.6-2.1-47 11.8l-38.6 47.2C233.9 335.4 177.3 277 144.8 205.3L189 169.3c13.9-11.3 18.6-30.4 11.8-47L160.2 25z"/></svg>
                    <?php echo ($branch_status == 'yes') ? $branch_details['contact_no'] : $app_contact_no ?>
                </p>
                <p><svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" height="12" width="12" viewBox="0 0 512 512"><!--!Font Awesome Free v7.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M48 64c-26.5 0-48 21.5-48 48 0 15.1 7.1 29.3 19.2 38.4l208 156c17.1 12.8 40.5 12.8 57.6 0l208-156c12.1-9.1 19.2-23.3 19.2-38.4 0-26.5-21.5-48-48-48L48 64zM0 196L0 384c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-188-198.4 148.8c-34.1 25.6-81.1 25.6-115.2 0L0 196z"/></svg> 
                    <?php echo ($branch_status == 'yes' && $branch_details['email_id'] != '') ? $branch_details['email_id'] : $app_email_id; ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Package -->
    <section class="print_sec main_block">
        <div class="row">
            <div class="col-xs-12 mg_bt_20">
                <ul class="print_info_list no-pad noType">
                    <li><span>CUSTOMER NAME :</span> <?php echo $name . '&nbsp'.$guest_name; ?></li>
                    <?php if ($sq_visa_info['guest_name'] != '') { ?> <li><span>GUEST NAME & CONTACT NO :</span>
                        <?= $sq_visa_info['guest_name'] ?></li> <?php } ?>
                    <li><span>BOOKING DATE :</span> <?= $invoice_date ?></li>
                </ul>
            </div>
        </div>
    </section>
    <!--New ticket design-->
    <div class="container-fluid ticket_info">
        <div class="row">
            <div class="col-md-12 airport">
                <h3>From <?= $dep_city_arr[$trip_count] ?> to <?= $arr_city_arr[$trip_count] ?></h3>
            </div>
        </div>
    </div>
    <hr />
    <div class="flight-info">
        <div class="row">
            <?php
            if($airline_img_arr[$trip_count] != ''){ ?>
            <div class="col-md-1" style="padding-right: 0px;">
                <img src="<?= $airline_img_arr[$trip_count] ?>" class="img-thumbnail" width="40px" height="40px" />
            </div>
            <?php } ?>
            <div class="col-md-7">
                <h4><?= $airlines_name_arr[$trip_count] ?></h4>
                <p><?= $op_by_arr[$trip_count] ?></p>
            </div>
            <?php
                if ($aircraft_type_arr[$trip_count] != '') { ?>
            <div class="col-md-2">
                <h4>Aircraft</h4>
                <p><?= $aircraft_type_arr[$trip_count] ?></p>
            </div>
            <?php }
                if ($class_arr[$trip_count] != '') { ?>
            <div class="col-md-2">
                <h4>Travel class</h4>
                <p><?= $class_arr[$trip_count] ?></p>
            </div>
            <?php } ?>
        </div>
        <div class="row">
            <div class="col-md-5" id="depart">
                <h4><?= $time1_arr[$trip_count] ?></h4>
                <p><?= $day1_arr[$trip_count] . ' : ' . $date1_arr[$trip_count] ?></p>
                <p><?= $sq_fcity_arr[$trip_count] ?></p>
                <p><?= $departure_terminal_arr[$trip_count] ?></p>
            </div>
            <div class="col-md-2" id="travel-time">
                <h4><?= $flight_duration_arr[$trip_count] ?></h4>
                <p>----------<svg xmlns="http://www.w3.org/2000/svg" height="14" width="14" viewBox="0 0 640 640"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M552 264C582.9 264 608 289.1 608 320C608 350.9 582.9 376 552 376L424.7 376L265.5 549.6C259.4 556.2 250.9 560 241.9 560L198.2 560C187.3 560 179.6 549.3 183 538.9L237.3 376L137.6 376L84.8 442C81.8 445.8 77.2 448 72.3 448L52.5 448C42.1 448 34.5 438.2 37 428.1L64 320L37 211.9C34.4 201.8 42.1 192 52.5 192L72.3 192C77.2 192 81.8 194.2 84.8 198L137.6 264L237.3 264L183 101.1C179.6 90.7 187.3 80 198.2 80L241.9 80C250.9 80 259.4 83.8 265.5 90.4L424.7 264L552 264z"/></svg></p>
            </div>
            <div class="col-md-5" id="arrival">
                <h4><?= $time2_arr[$trip_count] ?></h4>
                <p><?= $day2_arr[$trip_count] . ' : ' . $date2_arr[$trip_count] ?></p>
                <p><?= $sq_tcity_arr[$trip_count] ?></p>
                <p><?= $arrival_terminal_arr[$trip_count] ?></p>
            </div>
        </div>
        <?php
        if($layover_time_arr[$trip_count]!=''){ ?>
        <!--layover-->
        <div class="layover_section">
            <span>Change of planes | <?= $layover_time_arr[$trip_count] ?> layover in <?=$arr_city1_arr[$trip_count]?></span>
        </div>
        <!--layover-->
        <?php } ?>
        <section class="print_sec main_block">
            <div class="row">
                <div class="col-md-12">
                    <h4>Traveller Details</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered no-marg" id="tbl_emp_list">
                            <thead>
                                <tr class="table-heading-row">
                                    <th>Passengers</th>
                                    <th>Airline PNR</th>
                                    <th>Ticket Number</th>
                                    <?php if($sq_visa_info['ticket_reissue']==1){ echo '<th>'. 'Main Ticket Number'.'</th>'; } ?>
                                    <th>CHECK_IN & CABIN Baggage</th>
                                    <th>Seat No</th>
                                    <th>Meal Plan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $row_passenger = mysqli_fetch_assoc(mysqlQuery("select * from ticket_master_entries where entry_id = '$trip_pass_arr[$trip_count]' and status!='Cancel'"));
                                $count = 1;
                                $main_ticket = ($row_passenger['main_ticket'] != '') ? strtoupper($row_passenger['main_ticket']):'NA';
                                ?>
                                <tr>
                                    <td><?php echo $row_passenger['first_name'] . ' ' . $row_passenger['middle_name'] . ' ' . $row_passenger['last_name'] . '(' . $row_passenger['adolescence'] . ')'; ?>
                                    </td>
                                    <td><?php echo ($row_passenger['gds_pnr'] != '') ? strtoupper($row_passenger['gds_pnr']) : 'NA'; ?></td>
                                    <td><?php echo ($row_passenger['ticket_no'] != '') ? strtoupper($row_passenger['ticket_no']) : 'NA'; ?></td>
                                    <?php if($sq_visa_info['ticket_reissue']==1){ echo '<td>' . $main_ticket . '</td>'; } ?>
                                    <td><?php echo ($row_passenger['baggage_info'] != '') ? $row_passenger['baggage_info'] : 'NA'; ?></td>
                                    <td><?php echo ($seat_no_test != '') ? $seat_no_test.' (' .$dep_city1_arr[$trip_count].'-'.$arr_city1_arr[$trip_count].')' : 'NA'; ?></td>
                                    <td><?php echo ($meal_plan_test != '') ? $meal_plan_test.' (' .$dep_city1_arr[$trip_count].'-'.$arr_city1_arr[$trip_count].')' : 'NA'; ?></td>
                                    <td style="color: green !important;"><?php echo ($ticket_status_arr[$trip_count] != '') ? $ticket_status_arr[$trip_count] : 'NA'; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!--End New ticket design-->
    <?php
        
        }
        $trip_count++;
} ?>
</section>

<?php
if(isset($sq_visa_info['canc_policy'])){
    ?>
<!-- Cancellation Policy -->
<section class="print_sec main_block">
    <div class="row">
        <div class="col-md-12">
            <div class="section_heding">
                <h2>Cancellation Policy</h2>
                <div class="section_heding_img">
                    <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                </div>
            </div>
            <div class="print_text_bolck">
                <span><?= ($sq_visa_info['canc_policy']) ?><span>
            </div>
        </div>
    </div>
</section>
<?php } ?>
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
                <span><?= ($sq_terms_cond['terms_and_conditions']) ?><span>
            </div>
        </div>
    </div>
</section>
<?php } ?>
</body>

</html>