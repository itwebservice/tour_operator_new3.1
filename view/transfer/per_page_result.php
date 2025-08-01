<?php
include '../../config.php';
$transfer_results_array = ($_POST['data'] != '') ? $_POST['data'] : [];

if (sizeof($transfer_results_array) > 0) {
    for ($transfer_i = 0; $transfer_i < sizeof($transfer_results_array); $transfer_i++) {

        $trans_enq_data = array();

        $tariff_entries_id = $transfer_results_array[$transfer_i]['tariff_entries_id'];
        $total_cost = $transfer_results_array[$transfer_i]['total_cost'];
        $vehicle_name = $transfer_results_array[$transfer_i]['vehicle_name'];
        $vehicle_type = $transfer_results_array[$transfer_i]['vehicle_type'];
        $vehicle_count = $transfer_results_array[$transfer_i]['vehicle_count'];
        $trip_type = $transfer_results_array[$transfer_i]['trip_type'];
        $pickup = $transfer_results_array[$transfer_i]['pickup'];
        $pickup_date = $transfer_results_array[$transfer_i]['pickup_date'];
        $drop = $transfer_results_array[$transfer_i]['drop'];
        $return_date = ($trip_type == 'roundtrip') ? $transfer_results_array[$transfer_i]['return_date'] : 'NA';
        $passengers = $transfer_results_array[$transfer_i]['passengers'];
        $service_dutation_data = $transfer_results_array[$transfer_i]['service_duration'];

        array_push($trans_enq_data, array('vehicle_name' => $vehicle_name, 'vehicle_type' => $vehicle_type, 'trip_type' => $trip_type, 'pickup' => $pickup, 'pickup_date' => $pickup_date, 'drop' => $drop, 'return_date' => $return_date, 'passengers' => $passengers, 'tariff_entries_id' => $tariff_entries_id, 'total_cost' => $total_cost, 'vehicle_count' => $vehicle_count));
        $link = 'https://web.whatsapp.com/send?phone=' . $app_contact_no . '&text=Hello,
    %20I%20am%20interested%20in%20' . $vehicle_name . '( ' . $vehicle_type . ')' . '%20. Kindly%20provide%20more%20details.%20Thanks!';
?>
        <!-- ***** Car Card ***** -->
        <div class="c-cardList">
            <!--<div class="c-cardList type-transfer">-->
            <div class="c-cardListTable tours-cardListTable">
                <!-- *** Car Card image *** -->
                <div class="cardList-image">
                    <img src="<?= $transfer_results_array[$transfer_i]['transfer_image'] ?>" loading="lazy" alt="iTours" class="d-block mb-2" />
                    <a target="_blank" href="<?= $link ?>" class="btn btn-outline-success d-block mb-2"><i class="fa fa-whatsapp"></i> Whatsapp</a>
                    <input type="hidden" value="<?= $transfer_results_array[$transfer_i]['transfer_image'] ?>" id="image-<?= $transfer_results_array[$transfer_i]['vehicle_id'] ?>" />
                    <div class="typeOverlay">
                        <span class="transferType c-hide">
                            AC
                        </span>
                    </div>
                </div>
                <!-- *** Car Card image End *** -->

                <!-- *** Car Card Info *** -->
                <div class="cardList-info">
                    <div class="dividerSection type-1 noborder">
                        <div class="divider s1">
                            <h4 class="cardTitle"><span id="vehicle_name-<?= $transfer_results_array[$transfer_i]['vehicle_id'] ?>"><?= $transfer_results_array[$transfer_i]['vehicle_name'] ?></span>
                                <span class="tag" id="vehicle_type-<?= $transfer_results_array[$transfer_i]['vehicle_id'] ?>"><?= $transfer_results_array[$transfer_i]['vehicle_type'] ?></span>
                            </h4>
                            <div class="infoSection">
                                <span class="cardInfoLine cust">
                                    <i class="icon itours-user"></i>
                                    Max Pax(s): <strong><?= $transfer_results_array[$transfer_i]['seating_capacity'] ?></strong>
                                </span>
                            </div>

                            <div class="infoSection">
                                <span class="cardInfoLine cust">
                                    <i class="icon itours-suitcase"></i>
                                    Max Luggage: <strong><?= $transfer_results_array[$transfer_i]['luggage'] ?></strong>
                                </span>
                            </div>

                            <div class="infoSection">
                                <span class="cardInfoLine cust">
                                    <i class="icon itours-clock"></i>
                                    Service Duration: <strong><?= $transfer_results_array[$transfer_i]['service_duration'] ?></strong>
                                </span>
                            </div>

                            <div class="infoSection">
                                <span class="cardInfoLine cust">
                                    <i class="icon itours-taxi"></i>
                                    No. of vehicles: <strong id="vehicle_count-<?= $transfer_results_array[$transfer_i]['vehicle_id'] ?>"><?= $transfer_results_array[$transfer_i]['vehicle_count'] ?></strong>
                                </span>
                            </div>
                        </div>

                        <div class="divider s2 mb-3 mb-md-0">
                            <div class="priceTag">
                                <div class="p-old">
                                    <span class="o_lbl">Total Price</span>
                                    <span class="price_main">
                                        <span class="p_currency currency-icon"></span>
                                        <span class="p_cost transfer-currency-price"><?= $transfer_results_array[$transfer_i]['total_cost'] ?></span>
                                        <span class="c-hide transfer-currency-id"><?= $transfer_results_array[$transfer_i]['currency_id'] ?></span>
                                </div>
                            </div>

                            <div class="flexCustom mt-3 mt-md-0">

                                <div class="item">

                                    <button type="button" class="c-button md w-100" id='<?= $transfer_results_array[$transfer_i]['vehicle_id'] ?>' onclick='enq_to_action_page("5",this.id,<?= json_encode($trans_enq_data) ?>)'><i class="fa fa-phone-square" aria-hidden="true"></i> Enquiry</button>
                                </div>



                                <?php

                                $booking_status = mysqli_fetch_assoc(mysqlQuery("select * from b2c_generic_settings where entry_id='3'"));

                                if ($booking_status['answer'] == 'Yes') {


                                ?>
                                    <div class="item"><button type="button" class="c-button g-button md w-100" id='<?= $transfer_results_array[$transfer_i]['vehicle_id'] ?>' onclick='enq_to_action_page("5",this.id,<?= json_encode($trans_enq_data) ?>)'><i class="fa fa-contact-book" aria-hidden="true"></i> Book</button></div>

                                <?php } ?>
                            </div>

                        </div>
                    </div>

                </div>
                <!-- *** Car Card Info End *** -->
            </div>

        </div>
        <!-- ***** Car Card End ***** -->

<?php }
} ?>
<!-- ***** Transfer Listing End ***** -->
<script>
    $(document).ready(function() {

        var base_url = $('#base_url').val();
        clearTimeout(b);
        var b = setTimeout(function() {

            var amount_list = document.querySelectorAll(".transfer-currency-price");
            var amount_id = document.querySelectorAll(".transfer-currency-id");

            //total cost
            var amount_arr = [];
            for (var i = 0; i < amount_list.length; i++) {
                amount_arr.push({
                    'amount': amount_list[i].innerHTML,
                    'id': amount_id[i].innerHTML
                });
            }
            sessionStorage.setItem('transfer_amount_list', JSON.stringify(amount_arr));
            transfer_page_currencies();
        }, 500);
    });
</script>
<script>
    setTimeout(() => {

        var vehicle_type_array = JSON.parse(document.getElementById('vehicle_type_array').value);
        var selected_vehicle_type_array = (document.getElementById('selected_vehicle_type_array').value).split(',');
        var html = '';
        for (var i = 0; i < vehicle_type_array.length; i++) {
            var checked_status = (selected_vehicle_type_array.includes(vehicle_type_array[i])) ? 'checked' : '';
            html += '<div class="form-check"><input type="checkbox" name="vehicle_type" class="form-check-input lblfilterChk" id="' + (i + 1) + '" value="' + vehicle_type_array[i] + '" ' + checked_status + '/><label class="form-check-label lblfilterChk" for="' + (i + 1) + '">' + vehicle_type_array[i] + '</label></div>';
        }
        $('#vehicle_types').html(html);
        // service_duration
        var service_duration_array = JSON.parse(document.getElementById('service_duration_array').value);
        var selected_service_duration_array = (document.getElementById('selected_service_duration_array').value).split(',');
        var html = '';
        for (var i = 0; i < service_duration_array.length; i++) {
            var checked_status = (selected_service_duration_array.includes(service_duration_array[i])) ? 'checked' : '';
            html += '<div class="form-check"><input type="checkbox" name="service_duration" class="form-check-input lblsdfilterChk" id="sd-' + (i + 1) + '" value="' + service_duration_array[i] + '" ' + checked_status + '/><label class="form-check-label lblsdfilterChk" for="sd-' + (i + 1) + '">' + service_duration_array[i] + '</label></div>';
        }
        $('#total_nights').html(html);

    }, 500);
</script>