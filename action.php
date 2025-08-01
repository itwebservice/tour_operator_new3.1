<?php

include 'config.php';

//Include header

include 'layouts/header2.php';

$type = $_POST['type'];

if ($type == '1' || $type == '2') {



    $package_id = $_POST['package_id'];

    $package_type = $_POST['package_type'];

    $adult_count = $_POST['adult_count'];

    $child_wocount = $_POST['child_wocount'];

    $child_wicount = $_POST['child_wicount'];

    $extra_bed_count = $_POST['extra_bed_count'];

    $infant_count = $_POST['infant_count'];

    $travel_date = $_POST['travel_date'];
} else {

    $item_id = $_POST['item_id'];

    $enq_data = json_decode($_POST['enq_data']);
}



if ($type == '1') {

    $type_label = 'Holiday';

    $sq_tour = mysqli_fetch_assoc(mysqlQuery("select package_name,dest_id from custom_package_master where package_id='$package_id'"));

    $sq_dest = mysqli_fetch_assoc(mysqlQuery("select dest_name from destination_master where dest_id='$sq_tour[dest_id]'"));

    $tour_name = $sq_tour['package_name'] . ' (' . $sq_dest['dest_name'] . ')';

    $travel_date1 = $travel_date;

    $readonly = '';
} else if ($type == '2') {

    $type_label = 'Group Tour';

    $query = mysqli_fetch_assoc(mysqlQuery("select tour_name,dest_id from tour_master where tour_id = '$package_id'"));

    $sq_dest = mysqli_fetch_assoc(mysqlQuery("select dest_name from destination_master where dest_id='$query[dest_id]'"));

    $tour_name = $query['tour_name'] . ' (' . $sq_dest['dest_name'] . ')';

    $travel_date1 = explode('to', $travel_date);

    $travel_date = $travel_date1[0];

    $travel_to_date = $travel_date1[1];

    $readonly = 'readonly';

    $group_id = $_POST['group_id'];
} else if ($type == '3') {

    $type_label = 'Hotel';

    $query = mysqli_fetch_assoc(mysqlQuery("select city_id from hotel_master where hotel_id = '$item_id'"));

    $sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id = '$query[city_id]'"));

    $tour_name = $enq_data[0]->hotel_name . ' (' . $sq_city['city_name'] . ')';

    $check_in = date('d-m-Y', strtotime($enq_data[0]->check_in));

    $check_out = date('d-m-Y', strtotime($enq_data[0]->check_out));
    $hotel_id = $item_id;
    $total_rooms = $enq_data[0]->no_of_rooms;
    $adult_count = $enq_data[0]->adult_count;
    $chwb_count = $enq_data[0]->chwb_count;
    $chwob_count = $enq_data[0]->chwob_count;
    $extra_bed_count = $enq_data[0]->extra_bed_count;

    // Initialize total cost
    $total_cost = 0;
    // Loop through all room entries in the second part of the array
    foreach ($enq_data[1] as $room_entry) {
        $parts = explode('-', $room_entry);
        if (isset($parts[2])) {
            $cost = (float)($parts[2]); // Convert to number
            $total_cost += $cost;
        }
    }


    // Room Category
    $room_data_list = $enq_data[1]; // second element
    // room extracted from array
    $output = [];

    foreach ($room_data_list as $room_info) {
        // Decode and split string
        $decoded = urldecode($room_info); // "Room 1-Deluxe Room-5750-68"
        $parts = explode('-', $decoded);

        if (count($parts) >= 2) {
            $room_number = str_replace(' ', '', $parts[0]); // Room1
            $room_type = $parts[1]; // Deluxe Room
            $output[] = "{$room_number}-{$room_type}";
        }
    }
    $room_display = implode(', ', $output);
} else if ($type == '4') {

    $type_label = 'Activity';

    $query = mysqli_fetch_assoc(mysqlQuery("select city_id,timing_slots from excursion_master_tariff where entry_id = '$item_id'"));

    $sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id = '$query[city_id]'"));

    $tour_name = $enq_data[0]->excursion_name . ' (' . $sq_city['city_name'] . ')';

    $act_date = date('d-m-Y', strtotime($enq_data[0]->actDate));
    $timing_slots = ($query['timing_slots'] != '' && $query['timing_slots'] != 'null') ? json_decode($query['timing_slots']) : [];

    // $entry_id = $_POST['item_id'];


    $entry_id = $enq_data[0]->act_id;

    $act_total_cost =  $enq_data[0]->act_total_cost;
} else if ($type == '5') {



    $type_label = 'Transfer';

    $tour_name = $enq_data[0]->vehicle_name . ' (' . $enq_data[0]->vehicle_type . ')';

    $trip_type = $enq_data[0]->trip_type;

    $pickup = $enq_data[0]->pickup;

    $pickup_date = date('d-m-Y H:i', strtotime($enq_data[0]->pickup_date));

    $drop = $enq_data[0]->drop;

    $return_date = ($trip_type == 'roundtrip') ? date('d-m-Y H:i', strtotime($enq_data[0]->return_date)) : 'NA';

    $readonly = ($trip_type == 'roundtrip') ? '' : 'readonly';

    $passengers = $enq_data[0]->passengers;
    $vehicle_count = $enq_data[0]->vehicle_count;

    $tariff_entries_id = $enq_data[0]->tariff_entries_id;
    $total_cost = $enq_data[0]->total_cost;
} else if ($type == '6') {



    $type_label = 'Visa';

    $tour_name = $enq_data[0] . ' (' . $enq_data[1] . ')';
} else if ($type == '7') {



    $type_label = 'Cruise';

    $tour_name = $enq_data[0]->ferry_name . ' (' . $enq_data[0]->ferry_type . ') : ' . $enq_data[0]->ferry_category;

    $travel_date = date('d-m-Y H:i', strtotime($enq_data[0]->travel_date));

    $adult_count = $enq_data[0]->adult_count;

    $child_count = $enq_data[0]->child_count;

    $infant_count = $enq_data[0]->infant_count;
}

?>


<!-- ********** Component :: Page Title ********** -->

<div class="c-pageTitleSect ts-pageTitleSect">

    <div class="container">

        <div class="row">

            <div class="col-md-7 col-12">



                <!-- *** Search Head **** -->

                <div class="searchHeading">

                    <span class="pageTitle">Enquiry for <?= $type_label ?> </span>



                    <div class="clearfix for-transfer">

                        <div class="sortSection">

                            <span class="sortTitle st-search">

                                <i class="icon it itours-pin-alt"></i>

                                <?= $tour_name ?></strong>

                            </span>

                        </div>

                    </div>

                </div>

                <!-- *** Search Head End **** -->

            </div>



            <div class="col-md-5 col-12 c-breadcrumbs">

                <ul>

                    <li>

                        <a href="<?= BASE_URL_B2C ?>">Home</a>

                    </li>

                    <li class="st-active">

                        <a href="javascript:void(0)">Enquiry</a>

                    </li>

                </ul>

            </div>



        </div>

    </div>

</div>

<!-- ********** Component :: Page Title End ********** -->


<!-- Contact Section Start -->

<section class="ts-contact-section" style="padding:30px 0;">

    <div class="container">

        <div class="row">

            <div class="col col-12 col-md-12 col-lg-12">

                <div class="ts-contact-form ts-enquiry-form">

                    <form id="action_form" class="needs-validation" novalidate>

                        <input type="hidden" id="type" name="type" value="<?= $type ?>" />

                        <?php

                        // Holiday and Group Tour

                        if ($type == '1' || $type == '2') {

                        ?>

                            <input type="hidden" id="package_id" name="package_id" value="<?= $package_id ?>" />

                            <input type="hidden" id="package_type" name="package_type" value="<?= $package_type ?>" />

                            <input type="hidden" id="group_id" name="group_id" value="<?= $group_id ?>" />
                            <div class="form-wrapper">
                                <p class="form-heading">Customer Details</p>
                                <div class="form-row">

                                    <div class="form-group col-12 col-md-6 col-lg-4 test">


                                        <input type="text" class="form-control" id="name" name="name" placeholder="*Name" title="*Name" onkeypress="return blockSpecialChar(event)" required>

                                    </div>

                                    <div class="form-group col-12 col-md-6 col-lg-4">


                                        <input type="email" class="form-control" id="email_id" name="email_id" placeholder="*Email ID" title="*Email ID" required>

                                    </div>

                                    <div class="form-group col-12 col-md-6 col-lg-4">


                                        <input type="text" class="form-control" id="city_place" name="city_place" title="*City or Place*" placeholder="*City or Place" required>

                                        <input type="hidden" id="city_data" name="city_data" value='<?= get_cities_dropdown_sugg() ?>'>

                                    </div>

                                    <div class="form-group col-12 col-md-6 col-lg-4">


                                        <select class="form-control" title="*Country Code" id="country_code" name="country_code*" style="width:100%" required>

                                            <?= get_country_code() ?>

                                        </select>

                                    </div>

                                    <div class="form-group col-12 col-md-6 col-lg-4">


                                        <input type="number" class="form-control" id="phone" name="phone" placeholder="*Phone" title="*Phone" required>

                                    </div>
                                </div>
                            </div>

                            <div class="form-wrapper">
                                <p class="form-heading">Booking Details</p>
                                <div class="form-row">
                                    <div class="form-group col-12 col-md-6 col-lg-4">


                                        <input style="border: none !important;" name="package_name" id="package_name" name="package_name" title="Package Name" class="form-control" value="<?= $tour_name ?>" readonly required>

                                    </div>

                                    <div class="form-group col-12 col-md-6 col-lg-4">


                                        <input type="text" class="form-control" id="travel_from" name="travel_from" placeholder="*Travel From Date" title="*Travel From Date" onchange="get_to_date1(this.id,'travel_to')" value="<?= $travel_date ?>" <?= $readonly ?> required>

                                    </div>

                                    <div class="form-group col-12 col-md-6 col-lg-4">


                                        <input type="text" class="form-control" id="travel_to" name="travel_to" title="*Travel To Date" placeholder="*Travel To Date" onchange="validate_validDate1('travel_from','travel_to');" value="<?= $travel_to_date ?>" <?= $readonly ?> required>

                                    </div>

                                    <div class="form-group col-12 col-md-6 col-lg-4">

                                        <!-- <label for="adults" style="text-transform: inherit !important;">Adult(s)*</label> -->

                                        <input type="number" class="form-control" id="adults" name="adults" placeholder="*Adult(s)" title="*Adult(s)" value="<?= $adult_count ?>" required>

                                    </div>

                                    <div class="form-group col-12 col-md-6 col-lg-4">


                                        <input type="number" class="form-control" id="chwob" placeholder="Child Without Bed(s)(2-5 yrs)" value="<?= $child_wocount ?>">

                                    </div>

                                    <div class="form-group col-12 col-md-6 col-lg-4">


                                        <input type="number" class="form-control" id="chwb" placeholder="Child With Bed(s)(6-11 yrs)" value="<?= $child_wicount ?>">

                                    </div>

                                    <div class="form-group col-12 col-md-6 col-lg-4">


                                        <input type="number" class="form-control" id="extra_bed" placeholder="Extra Bed(s)" value="<?= $extra_bed_count ?>">

                                    </div>

                                    <div class="form-group col-12 col-md-6 col-lg-4">


                                        <input type="number" class="form-control" id="infant" placeholder="Infant(s)(Below 2 yrs)" value="<?= $infant_count ?>">

                                    </div>

                                    <div class="form-group col-12 col-md-6 col-lg-4">


                                        <select id="package_typef" class="form-control">

                                            <?php

                                            if ($type == '2') { ?>

                                                <option value="NA"><?= 'NA' ?></option>

                                                <?php

                                            } else if ($package_type != '') {

                                                $package_type_arr = explode(',', $package_type);

                                                for ($i = 0; $i < sizeof($package_type_arr); $i++) {

                                                ?>

                                                    <option value="<?= $package_type_arr[$i] ?>"><?= $package_type_arr[$i] ?></option>

                                            <?php }
                                            } else {

                                                get_package_type_dropdown();
                                            }

                                            ?>

                                        </select>

                                    </div>

                                    <div class="form-group col-12 col-md-12 col-lg-4">

                                        <textarea class="form-control" id="specification" placeholder="Other Specification(If any)"></textarea>

                                    </div>

                                </div>
                            </div>
                </div>


            <?php }

                        // Hotel

                        else if ($type == '3') {

            ?>

                <div class="form-wrapper">
                    <p class="form-heading">Customer Details</p>
                    <div class="form-row">

                        <div class="form-group col-12 col-md-6 col-lg-4">

                            <input type="text" class="form-control" id="name" name="name" placeholder="*Name" title="*Name" onkeypress="return blockSpecialChar(event)" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="email" class="form-control" id="email_id" name="email_id" placeholder="*Email ID" title="*Email ID" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="text" class="form-control" id="city_place" name="city_place" placeholder="*City or Place" title="*City or Place" required>

                            <input type="hidden" id="city_data" name="city_data" value='<?= get_cities_dropdown_sugg() ?>'>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <select class="form-control" id="country_code" title="*Country Code" placeholder="*Country Code" name="country_code" name="country_code" style="width:100%" required>

                                <?= get_country_code() ?>

                            </select>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="number" class="form-control" id="phone" name="phone" placeholder="*Phone" title="*Phone" required>

                        </div>
                    </div>
                </div>
                <div class="form-wrapper">
                    <p class="form-heading">Booking Details</p>
                    <div class="form-row">
                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" name="hotel_name" id="hotel_name" name="hotel_name" class="form-control" title="<?= $tour_name ?>" value="<?= $tour_name ?>" readonly>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" type="text" class="form-control" id="check_in" name="check_in" placeholder="*CheckIn Date" title="*CheckIn Date" onchange="get_to_date1(this.id,'check_out')" value="<?= $check_in ?>" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" type="text" class="form-control" id="check_out" name="check_out" placeholder="*CheckOut Date" title="*CheckOut Date" onchange="validate_validDate1('check_in','check_out');" value="<?= $check_out ?>" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" type="number" class="form-control" id="total_rooms" name="total_rooms" title="*Total Room(s)" placeholder="*Total Room(s)" value="<?= $total_rooms ?>" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">



                            <input style="border: none !important;" type="text" value="<?= $room_display ?>" title="<?= $room_display ?>" id="room_cat" class="form-control" style="width:100%;" readonly>
                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" type="number" class="form-control" id="adults" name="adults" placeholder="*Adult(s)" value="<?= $adult_count ?>" title="*Adult(s)" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" type="number" class="form-control" id="chwob" placeholder="Child Without Bed(s)" value="<?= $chwob_count ?>" title="Child Without Bed(s)(2-5 yrs)" readonly>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">

                            <input style="border: none !important;" type="number" class="form-control" id="chwb" placeholder="Child With Bed(s)" value="<?= $chwb_count ?>" title="Child With Bed(s)(6-11 yrs)" readonly>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" type="number" class="form-control" id="extra_bed" placeholder="Extra Bed(s)" title="Extra Bed(s)" value="<?php echo ($extra_bed_count) ?>" readonly>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="number" class="form-control" id="infant" placeholder="Infant(s)" title="Infant(s)(Below 2 yrs)">

                        </div>

                        <div class="form-group col-md-12 col-lg-4">


                            <textarea class="form-control" id="specification" title="Other Specification(If any)" placeholder="Other Specification"></textarea>

                        </div>
                        <input type='hidden' id="total_cost" value="<?= $total_cost ?>">
                        <input type='hidden' id="hotel_id" value="<?= $hotel_id ?>">

                    </div>
                </div>


            <?php }

                        // Activity

                        else if ($type == '4') {

            ?>
                <div class="form-wrapper">
                    <p class="form-heading">Customer Details</p>
                    <div class="form-row">

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="text" class="form-control" id="name" name="name" placeholder="*Name" title="*Name" onkeypress="return blockSpecialChar(event)" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="email" class="form-control" id="email_id" name="email_id" placeholder="*Email ID" title="*Email ID" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="text" class="form-control" id="city_place" name="city_place" title="*City or Place" placeholder="*City or Place" required>

                            <input type="hidden" id="city_data" name="city_data" value='<?= get_cities_dropdown_sugg() ?>'>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <select class="form-control" id="country_code" name="country_code" title="Country Code" name="country_code" style="width:100%" required>

                                <?= get_country_code() ?>

                            </select>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">

                            <input type="number" class="form-control" id="phone" name="phone" title="*Phone" placeholder="*Phone" required>

                        </div>

                    </div>
                </div>
                <div class="form-wrapper">
                    <p class="form-heading">Booking Details</p>
                    <div class="form-row">
                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" name="act_name" id="act_name" class="form-control" title="<?= $tour_name ?>" value="<?= $tour_name ?>" readonly required>

                            <input type="hidden" id="entry_id" name="entry_id" value="<?= $entry_id  ?>">
                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="text" class="form-control" id="act_date" name="act_date" title="*Activity Date" placeholder="*Activity Date" value="<?= $act_date ?>" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">

                            <input style="border: none !important;" name="transfer_option" id="transfer_option" class="form-control" title="*Transfer Option" value="<?= $enq_data[1] ?>" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-3">


                            <input type="number" class="form-control" id="adults" name="adults" title="*Adult(s)" placeholder="*Adult(s)" value="<?= $enq_data[0]->adult_count ?>" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-3">


                            <input type="number" class="form-control" id="child" title="Child(ren)" placeholder="Child(ren)" value="<?= $enq_data[0]->child_count ?>">

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-3">


                            <input type="number" class="form-control" id="infant" title="Infant(s)" placeholder="Infant(s)" value="<?= $enq_data[0]->infant_count ?>">

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-3">


                            <select class="form-control" id="timing_slot" name="timing_slot" title="*Timing Slot" required>

                                <?php
                                if (sizeof($timing_slots) == 0) {
                                    echo "<option value='NA'>NA</option>";
                                } else {
                                ?>
                                    <option value="">*Timing Slot</option>
                                    <?php
                                    for ($t = 0; $t < sizeof($timing_slots); $t++) {
                                    ?><option value='<?= $timing_slots[$t]->from_time . ' - ' . $timing_slots[$t]->to_time ?>'><?= $timing_slots[$t]->from_time . ' - ' . $timing_slots[$t]->to_time ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>

                        </div>

                        <div class="form-group col-12 col-md-12 col-lg-3">


                            <textarea class="form-control" id="specification" title="Other Specification" placeholder="Other Specification"></textarea>

                        </div>
                        <input type="hidden" id="act_total_cost" name="act_total_cost" value="<?= $act_total_cost ?>">

                    </div>
                </div>

            <?php }

                        // Transfer

                        else if ($type == '5') {

            ?>

                <div class="form-wrapper">
                    <p class="form-heading">Customer Details</p>
                    <div class="form-row">

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="text" class="form-control" id="name" name="name" placeholder="*Name" title="*Name" onkeypress="return blockSpecialChar(event)" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="email" class="form-control" id="email_id" name="email_id" placeholder="*Email ID" title="*Email ID" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="text" class="form-control" id="city_place" name="city_place" placeholder="*City or Place" title="*City or Place" required>

                            <input type="hidden" id="city_data" name="city_data" value='<?= get_cities_dropdown_sugg() ?>'>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">

                            <select class="form-control" id="country_code" name="country_code" title="*Country Code" style="width:100%" required>

                                <?= get_country_code() ?>

                            </select>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="number" class="form-control" id="phone" name="phone" placeholder="*Phone" title="*Phone" required>

                        </div>

                    </div>
                </div>
                <div class="form-wrapper">
                    <p class="form-heading">Booking Details</p>
                    <div class="form-row">
                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" name="trans_name" id="trans_name" class="form-control" title="*Transfer name" value="<?= $tour_name . ' : ' . $vehicle_count . ' Vehicle(s)' ?>" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" name="trip_type" id="trip_type" class="form-control" title="*Trip Type" value="<?= ucfirst($trip_type) ?>" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" name="pickup" id="pickup" class="form-control" title="*Pickup Location" value="<?= $pickup ?>" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" type="text" class="form-control" id="pickup_date" name="pickup_date" title="*Pickup Date&Time" placeholder="*Pickup Date&Time" onchange="get_to_datetime1(this.id,'return_date','<?= $trip_type ?>')" value="<?= $pickup_date ?>" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" name="drop" id="drop" class="form-control" value="<?= $drop ?>" title="*Dropoff Location" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" type="text" class="form-control" id="return_date" name="return_date" title="*Return Date&Time" placeholder="*Return Date&Time" onchange="validate_validDatetime1('pickup_date','return_date')" value="<?= $return_date ?>" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" type="number" class="form-control" id="pass" name="pass" title="*Total Passenger(s)" placeholder="*Total Passengers" value="<?= $passengers ?>" readonly required>

                        </div>

                        <div class="form-group col-md-12 col-lg-4">


                            <textarea class="form-control" id="specification" title="Other Specification(If any)" placeholder="Other Specification"></textarea>

                        </div>
                        <input type='hidden' id="tariff_entries_id" value="<?= $tariff_entries_id ?>">
                        <input type='hidden' id="total_cost" value="<?= $total_cost ?>">

                    </div>
                </div>

            <?php }

                        // Visa

                        else if ($type == '6') {

            ?>

                <div class="form-wrapper">
                    <p class="form-heading">Customer Details</p>
                    <div class="form-row">

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="text" class="form-control" id="name" name="name" placeholder="*Name" title="*Name" onkeypress="return blockSpecialChar(event)" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="email" class="form-control" id="email_id" name="email_id" placeholder="*Email ID" title="*Email ID" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="text" class="form-control" id="city_place" name="city_place" title="*City or Place" placeholder="*City or Place" required>

                            <input type="hidden" id="city_data" name="city_data" value='<?= get_cities_dropdown_sugg() ?>'>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <select class="form-control" id="country_code" name="country_code" name="country_code" title="*Country Code" style="width:100%" required>

                                <?= get_country_code() ?>

                            </select>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="number" class="form-control" id="phone" name="phone" placeholder="*Phone" title="*Phone" required>

                        </div>
                    </div>
                </div>
                <div class="form-wrapper">
                    <p class="form-heading">Booking Details</p>
                    <div class="form-row">

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" name="country_name" id="country_name" class="form-control" title="Country name" value="<?= $tour_name ?>" readonly required>

                        </div>
                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="text" class="form-control" id="travel_date" name="travel_date" placeholder="*Travel Date" title="*Travel Date" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="number" class="form-control" id="adults" name="adults" placeholder="*Adult(s)" title="*Adult(s)" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="number" class="form-control" id="child" placeholder="Child(ren)" title="Child(ren)(2-5 yrs)">

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="number" class="form-control" id="infant" placeholder="Infant(s)" title="Infant(s)(Below 2 yrs)">

                        </div>

                        <div class="form-group col-md-8">


                            <textarea class="form-control" id="specification" placeholder="Other Specification" title="Other Specification(If any)"></textarea>

                        </div>

                    </div>
                </div>

            <?php }

                        // Cruise

                        else if ($type == '7') {

            ?>

                <div class="form-wrapper">
                    <p class="form-heading">Customer Details</p>
                    <div class="form-row">

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="text" class="form-control" id="name" name="name" placeholder="*Name" title="*Name" onkeypress="return blockSpecialChar(event)" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="email" class="form-control" id="email_id" name="email_id" placeholder="*Email ID" title="*Email ID" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="text" class="form-control" id="city_place" name="city_place" title="*City or Place" placeholder="*City or Place" required>

                            <input type="hidden" id="city_data" name="city_data" value='<?= get_cities_dropdown_sugg() ?>'>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <select class="form-control" id="country_code" name="country_code" name="country_code" title="*Country Code" style="width:100%" required>

                                <?= get_country_code() ?>

                            </select>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="number" class="form-control" id="phone" name="phone" placeholder="*Phone" title="*Phone" required>

                        </div>
                    </div>
                </div>
                <div class="form-wrapper">
                    <p class="form-heading">Booking Details</p>
                    <div class="form-row">

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" name="cruise_name" id="cruise_name" class="form-control" title="*Cruise name" value="<?= $tour_name ?>" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" name="from_location" id="from_location" class="form-control" title="*From Location" value="<?= $enq_data[0]->from_location ?>" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" name="to_location" id="to_location" class="form-control" title="*To Location" value="<?= $enq_data[0]->to_location ?>" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input style="border: none !important;" type="text" class="form-control" id="travel_date" name="travel_date" title="*Travel Date" placeholder="Travel Date" value="<?= $travel_date ?>" readonly required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="number" class="form-control" id="adults" name="adults" placeholder="Adult(s)" title="*Adult(s)" value="<?= $adult_count ?>" required>

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="number" class="form-control" id="child" placeholder="Child(ren)" title="Child(ren)(2-5 yrs)" value="<?= $child_count ?>">

                        </div>

                        <div class="form-group col-12 col-md-6 col-lg-4">


                            <input type="number" class="form-control" id="infant" placeholder="Infant(s)" title="Infant(s)(Below 2 yrs)" value="<?= $infant_count ?>">

                        </div>

                        <div class="form-group col-md-8">


                            <textarea class="form-control" id="specification" placeholder="Other Specification" title="Other Specification(If any)"></textarea>

                        </div>

                    </div>
                </div>

            <?php } ?>

            <button type="submit" name="sb" value="btn_enq" id="btn_enq" class="btn btn-primary w-33 width-100" title="Generate Enquiry"><i class="fa fa-phone-square" aria-hidden="true"></i> Enquiry</button>

            <?php

            if ($type == '1' || $type == '2') { ?>

                <button type="submit" name="sb" value="btn_quot" id="btn_quot" class="btn btn-info w-33 width-100 mt-3 mb-3 mt-md-0 mb-md-0" title="Download Quotation"><i class="fa fa-file-text-o" aria-hidden="true"></i> Download Quotation</button>

                <button type="submit" name="sb" value="btn_book" id="btn_book" class="btn btn-success w-33 width-100" title="Book"><i class="fa fa-address-book" aria-hidden="true"></i> Book</button>

            <?php } ?>

            <!-- Activity Book button -->
            <?php

            if ($type == '4') {


                $booking_status = mysqli_fetch_assoc(mysqlQuery("select * from b2c_generic_settings where entry_id='2'"));

                if ($booking_status['answer'] == 'Yes') {


            ?>
                    <button type="submit" name="sb" value="btn_book" id="btn_book" class="btn btn-success w-33" title="Book"><i class="fa fa-address-book" aria-hidden="true"></i> Book</button>

            <?php }
            } ?>

            <!-- Transfer -->

            <?php

            if ($type == '5') {


                $booking_status = mysqli_fetch_assoc(mysqlQuery("select * from b2c_generic_settings where entry_id='3'"));

                if ($booking_status['answer'] == 'Yes') {


            ?>
                    <button type="submit" name="sb" value="btn_book" id="btn_book" class="btn btn-success w-33" title="Book"><i class="fa fa-address-book" aria-hidden="true"></i> Book</button>

            <?php }
            } ?>

            <!-- hotel -->
            <?php

            if ($type == '3') {


                $booking_status = mysqli_fetch_assoc(mysqlQuery("select * from b2c_generic_settings where entry_id='1'"));

                if ($booking_status['answer'] == 'Yes') {


            ?>
                    <button type="submit" name="sb" value="btn_book" id="btn_book" class="btn btn-success w-33" title="Book"><i class="fa fa-address-book" aria-hidden="true"></i> Book</button>

            <?php }
            } ?>

            </form>

            </div>

        </div>

    </div>

    </div>

</section>

<div id="div_data_modal"></div>

<!-- Contact Section End -->

<?php include 'layouts/footer2.php'; ?>
<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields

    (function() {

        'use strict';

        window.addEventListener('load', function() {

            // Fetch all the forms we want to apply custom Bootstrap validation styles to

            var forms = document.getElementsByClassName('needs-validation');

            // Loop over them and prevent submission

            var validation = Array.prototype.filter.call(forms, function(form) {

                form.addEventListener('submit', function(event) {

                    if (form.checkValidity() === false) {

                        event.preventDefault();

                        event.stopPropagation();

                    }

                    form.classList.add('was-validated');

                }, false);

            });

        }, false);

    })();
</script>





<script>
    $('#country_code').select2();

    /////// Next 10th day onwards date display

    var tomorrow = new Date();

    tomorrow.setDate(tomorrow.getDate() + 10);

    var day = tomorrow.getDate();

    var month = tomorrow.getMonth() + 1

    var year = tomorrow.getFullYear();

    var type = '<?php echo $type; ?>';
    if (type == '1') {

        $('#travel_from, #travel_to').datetimepicker({
            timepicker: false,
            format: 'd-m-Y',
            minDate: tomorrow
        });

    }

    if (type == '3') {

        $('#check_in, #check_out').datetimepicker({
            timepicker: false,
            format: 'd-m-Y',
            minDate: new Date()
        });

    }

    if (type == '4') {

        $('#act_date').datetimepicker({
            timepicker: false,
            format: 'd-m-Y',
            minDate: new Date()
        });

    }

    if (type == '5') {

        $('#pickup_date').datetimepicker({
            format: 'd-m-Y H:i',
            minDate: new Date()
        });

        var trip_type = '<?= $trip_type ?>';

        if (trip_type == 'roundtrip') {

            $('#return_date').datetimepicker({
                format: 'd-m-Y H:i',
                minDate: new Date()
            });

        }

    }

    if (type == '6') {

        $('#travel_date').datetimepicker({
            timepicker: false,
            format: 'd-m-Y',
            minDate: tomorrow
        });

    }



    $(document).ready(function() {



        var service = '<?php echo $service; ?>';

        if (service && (service !== '' || service !== undefined)) {

            var checkLink = $('.c-searchContainer .c-search-tabs li');

            var checkTab = $('.c-searchContainer .search-tab-content .tab-pane');

            checkLink.each(function() {

                var child = $(this).children('.nav-link');

                if (child.data('service') === service) {

                    $(this).siblings().children('.nav-link').removeClass('active');

                    child.addClass('active');

                }

            });

            checkTab.each(function() {

                if ($(this).data('service') === service) {

                    $(this).addClass('active show').siblings().removeClass('active show');

                }

            })

        }

    });

    //Get DateTime
    function get_to_datetime1(from_date, to_date, trip_type) {



        if (trip_type == 'roundtrip') {

            var from_date1 = $('#' + from_date).val();

            if (from_date1 != '') {

                var edate = from_date1.split(' ');

                var edate1 = edate[0].split('-');

                var edatetime = edate[1].split(':');

                var e_date_temp = new Date(

                    edate1[2],

                    edate1[1] - 1,

                    edate1[0],

                    edatetime[0],

                    edatetime[1]

                ).getTime();



                var currentDate = new Date(new Date(e_date_temp).getTime() + 24 * 60 * 60 * 1000);

                var day = currentDate.getDate();

                var month = currentDate.getMonth() + 1;

                var year = currentDate.getFullYear();

                var hours = currentDate.getHours();

                var minute = currentDate.getMinutes();

                if (day < 10) {

                    day = '0' + day;

                }

                if (month < 10) {

                    month = '0' + month;

                }

                if (hours < 10) {

                    hours = '0' + hours;

                }

                if (minute < 10) {

                    minute = '0' + minute;

                }

                $('#' + to_date).val(day + '-' + month + '-' + year + ' ' + hours + ':' + minute);

            } else {

                $('#' + to_date).val('');

            }

        }

    }

    //function for valid date tariff

    function validate_validDatetime1(from, to) {



        var base_url = $('#base_url').val();

        var from_date = $('#' + from).val();

        var to_date = $('#' + to).val();



        var edates = from_date.split(' ');

        var edate = edates[0].split('-');

        e_date = new Date(edate[2], edate[1] - 1, edate[0]).getTime();

        var edatet = to_date.split(' ');

        var edate1 = edatet[0].split('-');

        e_date1 = new Date(edate1[2], edate1[1] - 1, edate1[0]).getTime();



        var from_date_ms = new Date(e_date).getTime();

        var to_date_ms = new Date(e_date1).getTime();



        if (from_date_ms > to_date_ms) {

            error_msg_alert('Date should not be greater than valid to date', base_url);

            $('#' + from).css({
                border: '1px solid red'
            });

            document.getElementById(from).value = '';

            $('#' + from).focus();

            g_validate_status = false;

            return false;

        } else {

            $('#' + from).css({
                border: '1px solid #ddd'
            });

            return true;

        }

        return true;

    }



    $(function() {

        $('#action_form').validate({

            rules: {

            },

            submitHandler: function(form) {



                var btn_id = window.event.submitter.id;

                var base_url = $('#base_url').val();

                var crm_base_url = $('#crm_base_url').val();



                var type = $('#type').val();

                var name = $('#name').val();

                var email_id = $('#email_id').val();

                var city_place = $('#city_place').val();

                var country_code = $('#country_code').val();

                var phone = $('#phone').val();



                var package_id = $('#package_id').val();

                var group_id = $('#group_id').val();

                var package_type = $('#package_type').val();

                var package_name = $('#package_name').val();

                var travel_from = $('#travel_from').val();

                var travel_to = $('#travel_to').val();

                var adults = $('#adults').val();

                var chwb = $('#chwb').val();

                var chwob = $('#chwob').val();

                var extra_bed = $('#extra_bed').val();

                var infant = $('#infant').val();

                var package_typef = $('#package_typef').val();

                var specification = $('#specification').val();



                var enq_data_arr = [];

                if (type == '3') {


                    var hotel_id = $('#hotel_id').val();
                    var hotel_name = $('#hotel_name').val();

                    var check_in = $('#check_in').val();

                    var check_out = $('#check_out').val();

                    var total_rooms = $('#total_rooms').val();

                    var room_cat = $('#room_cat').val();

                    var total_cost = $('#total_cost').val();


                    enq_data_arr.push({

                        'total_cost': total_cost,
                        'hotel_id': hotel_id,
                        'hotel_name': hotel_name,

                        'check_in': check_in,

                        'check_out': check_out,

                        'total_rooms': total_rooms,

                        'room_cat': room_cat,

                        'adults': adults,

                        'chwob': chwob,

                        'chwb': chwb,

                        'extra_bed': extra_bed,

                        'infant': infant,

                        'specification': specification

                    });

                }

                if (type == '4') {



                    var act_name = $('#act_name').val();

                    var act_date = $('#act_date').val();
                    var timing_slot = $('#timing_slot').val();

                    var child = $('#child').val();

                    var transfer_option = $('#transfer_option').val();

                    var entry_id = $('#entry_id').val();

                    var act_total_cost = $('#act_total_cost').val();



                    enq_data_arr.push({

                        'act_name': act_name,

                        'act_date': act_date,

                        'adults': adults,

                        'child': child,

                        'infant': infant,

                        'transfer_option': transfer_option,
                        'timing_slot': timing_slot,
                        'specification': specification,
                        'act_total_cost': act_total_cost

                    });

                }

                if (type == '5') {

                    //   "tariff_entries_id" => $row_tariff['tariff_entries_id'],
                    var tariff_entries_id = $('#tariff_entries_id').val();

                    var total_cost = $('#total_cost').val();
                    var trans_name = $('#trans_name').val();

                    var trip_type = $('#trip_type').val();

                    var pickup = $('#pickup').val();

                    var pickup_date = $('#pickup_date').val();

                    var drop = $('#drop').val();

                    var return_date = $('#return_date').val();

                    var pass = $('#pass').val();



                    enq_data_arr.push({

                        'tariff_entries_id': tariff_entries_id,
                        'total_cost': total_cost,
                        'trans_name': trans_name,

                        'trip_type': trip_type,

                        'pickup': pickup,

                        'pickup_date': pickup_date,

                        'drop': drop,

                        'return_date': return_date,

                        'pass': pass,

                        'specification': specification

                    });

                }

                if (type == '6') {



                    var country_name = $('#country_name').val();

                    var travel_date = $('#travel_date').val();

                    var child = $('#child').val();



                    adults = (adults == '') ? 0 : adults;

                    child = (child == '') ? 0 : child;

                    infant = (infant == '') ? 0 : infant;

                    var pass = parseInt(adults) + parseInt(child) + parseInt(infant);



                    enq_data_arr.push({

                        'country_name': country_name,

                        'travel_date': travel_date,

                        'adults': adults,

                        'child': child,

                        'infant': infant,

                        'pass': pass,

                        'specification': specification

                    });

                }

                if (type == '7') {



                    var cruise_name = $('#cruise_name').val();

                    var from_location = $('#from_location').val();

                    var to_location = $('#to_location').val();

                    var travel_date = $('#travel_date').val();

                    var child = $('#child').val();



                    adults = (adults == '') ? 0 : adults;

                    child = (child == '') ? 0 : child;

                    infant = (infant == '') ? 0 : infant;

                    var pass = parseInt(adults) + parseInt(child) + parseInt(infant);



                    enq_data_arr.push({

                        'cruise_name': cruise_name,

                        'travel_date': travel_date,

                        'from_location': from_location,

                        'to_location': to_location,

                        'adults': adults,

                        'child': child,

                        'infant': infant,

                        'pass': pass,

                        'specification': specification

                    });

                }

                $('#' + btn_id).prop('disabled', true);

                $('#' + btn_id).button('loading');

                if (btn_id == 'btn_enq') {

                    var action_url = crm_base_url + 'controller/b2c_settings/b2c/enquiry_form.php';

                } else if (btn_id == 'btn_quot') {

                    var action_url = crm_base_url + 'controller/b2c_settings/b2c/quot_form.php';

                } else if (btn_id == 'btn_book') {

                    var action_url = crm_base_url + 'controller/b2c_settings/b2c/book_form.php';

                }

                $.ajax({

                    type: 'post',

                    url: action_url,

                    data: {

                        type: type,

                        package_id: package_id,

                        group_id: group_id,

                        package_type: package_type,

                        name: name,

                        email_id: email_id,

                        city_place: city_place,

                        country_code: country_code,

                        phone: phone,

                        package_name: package_name,

                        travel_from: travel_from,

                        travel_to: travel_to,

                        adults: adults,

                        chwb: chwb,

                        chwob: chwob,

                        infant: infant,

                        extra_bed: extra_bed,

                        package_typef: package_typef,

                        specification: specification,
                        entry_id: entry_id,
                        child: child,
                        act_total_cost: act_total_cost,



                        hotel_name: hotel_name,

                        check_in: check_in,

                        check_out: check_out,

                        total_rooms: total_rooms,

                        room_cat: room_cat,

                        enq_data_arr: JSON.stringify(enq_data_arr)

                    },

                    success: function(result) {

                        $('#' + btn_id).prop('disabled', false);

                        $('#' + btn_id).button('reset');

                        if (btn_id == 'btn_enq') {

                            var msg = 'Thank you for enquiry with us. Our experts will contact you shortly.';

                            $.alert({

                                title: 'Notification!',

                                content: msg,

                            });

                            setTimeout(() => {

                                window.location.href = base_url;

                            }, 2000);

                        } else if (btn_id == 'btn_quot') {

                            var otp = [];

                            otp.push({

                                otp: result,
                                email_id: email_id,
                                phone: phone,
                                used: 'false'

                            });

                            if (typeof Storage !== 'undefined') {

                                if (localStorage) {

                                    localStorage.setItem('otp_info', JSON.stringify(otp));

                                } else {

                                    window.sessionStorage.setItem('otp_info', JSON.stringify(otp));

                                }

                            }

                            $.post('action_pages/quotation_modal.php', {

                                type: type,

                                package_id: package_id,

                                name: name,

                                email_id: email_id,

                                city_place: city_place,

                                country_code: country_code,

                                phone: phone,

                                travel_from: travel_from,

                                travel_to: travel_to,

                                adults: adults,

                                chwb: chwb,

                                chwob: chwob,

                                infant: infant,

                                extra_bed: extra_bed,

                                package_typef: package_typef,

                                specification: specification,
                                entry_id: entry_id,
                                act_date: act_date,
                                transfer_option: transfer_option,
                                act_name: act_name,
                                child: child,
                                act_total_cost: act_total_cost,


                                otp: JSON.stringify(otp),
                                email_id: email_id,
                                phone: phone
                            }, function(data) {

                                $('#' + btn_id).prop('disabled', false);

                                $('#div_data_modal').html(data);

                            });

                        } else if (btn_id == 'btn_book') {

                            var msg = result.split('--');
                            if (msg[0] == 'error') {

                                error_msg_alert(msg[1], base_url);

                                $('#' + btn_id).prop('disabled', false);

                                return false;

                            } else {


                                var final_arr = sessionStorage.getItem('final_arr');


                                $.post('action_pages/book_modal.php', {

                                    type: type,

                                    package_id: package_id,

                                    package_name: package_name,

                                    name: name,

                                    email_id: email_id,

                                    city_place: city_place,

                                    country_code: country_code,

                                    phone: phone,

                                    travel_from: travel_from,

                                    travel_to: travel_to,

                                    adults: adults,

                                    chwb: chwb,

                                    chwob: chwob,

                                    extra_bed: extra_bed,

                                    infant: infant,

                                    package_typef: package_typef,

                                    specification: specification,

                                    entry_id: entry_id,
                                    act_date: act_date,
                                    timing_slot: timing_slot,
                                    transfer_option: transfer_option,
                                    act_name: act_name,
                                    child: child,
                                    act_total_cost: act_total_cost,

                                    tariff_entries_id: tariff_entries_id,
                                    trans_name: trans_name,
                                    trip_type: trip_type,
                                    pickup: pickup,
                                    pickup_date: pickup_date,
                                    drop: drop,
                                    return_date: return_date,
                                    pass: pass,
                                    specification: specification,

                                    hotel_name: hotel_name,
                                    hotel_id: hotel_id,

                                    check_in: check_in,

                                    check_out: check_out,

                                    total_rooms: total_rooms,

                                    room_cat: room_cat,
                                    final_arr: final_arr,

                                    result: result,
                                    email_id: email_id,
                                    phone: phone
                                }, function(data) {
                                    $('#' + btn_id).prop('disabled', false);

                                    $('#div_data_modal').html(data);

                                });

                            }

                        }

                    }

                });

            }

        });

    });
</script>
<script type="text/javascript" src="js/scripts.js"></script>