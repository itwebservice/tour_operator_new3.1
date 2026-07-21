<?php
include '../../config.php';
$hotel_results_array = ($_POST['data'] != '') ? $_POST['data'] : [];
global $currency;
$b2b_currency = $_SESSION['session_currency_id'];
//Get selected currency rate
$sq_to = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$b2b_currency'"));
$to_currency_rate = $sq_to['currency_rate'];
if (sizeof($hotel_results_array) > 0) {
    for ($hotel_i = 0; $hotel_i < sizeof($hotel_results_array); $hotel_i++) {

        $hotel_enq_data = array();
        $check_in = $hotel_results_array[$hotel_i]['check_in'];
        $check_out = $hotel_results_array[$hotel_i]['check_out'];
        $hotel_name = $hotel_results_array[$hotel_i]['hotel_name'];
        $final_room_type_array = $hotel_results_array[$hotel_i]['final_room_type_array'];
        $adult_count = $hotel_results_array[$hotel_i]['adult_count'];
        $chwb_count = $hotel_results_array[$hotel_i]['chwb_count'];
        $chwob_count = $hotel_results_array[$hotel_i]['chwob_count'];
        $extra_bed_count = $hotel_results_array[$hotel_i]['extra_bed_count'];
        $total_rooms = $hotel_results_array[$hotel_i]['total_rooms'];

        array_push($hotel_enq_data, array('hotel_name' => $hotel_name, 'check_in' => $check_in, 'check_out' => $check_out, 'total_rooms' => $final_room_type_array, 'adult_count' => $adult_count, 'chwb_count' => $chwb_count, 'chwob_count' => $chwob_count, 'no_of_rooms' => $total_rooms, 'extra_bed_count' => $extra_bed_count));
        $link = 'https://web.whatsapp.com/send?phone=' . $app_contact_no . '&text=Hello,
    %20I%20am%20interested%20in%20' . $hotel_name . ' (' . $hotel_results_array[$hotel_i]['city_name'] . ')' . '%20 . Kindly%20provide%20more%20details.%20Thanks!';
?>
        <!-- ***** Hotel Card ***** -->
        <div class="c-cardList type-hotel">
            <div class="c-cardListTable tours-cardListTable">
                <!-- *** Hotel Card image *** -->
                <div class="cardList-image">
                    <img src="<?= $hotel_results_array[$hotel_i]['hotel_image'] ?>" loading="lazy" alt="<?php echo $hotel_results_array[$hotel_i]['hotel_name']; ?>" class="d-block mb-2" />
                    <a target="_blank" href="<?= $link ?>" class="btn btn-outline-success d-block mb-2"><i class="fa fa-whatsapp"></i> Whatsapp</a>
                    <?php if ($hotel_results_array[$hotel_i]['hotel_type'] != '') { ?>
                        <div class="typeOverlay">
                            <span class="hotelType">
                                <!-- <i class="icon it itours-building"></i> -->
                                <?php echo $hotel_results_array[$hotel_i]['hotel_type'] ?>
                            </span>
                        </div>
                    <?php } ?>
                    <div class="c-discount c-hide" id='discount<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>'>
                        <div class="discount-text">
                            <span class="currency-icon"></span>
                            <span class='offer-currency-price' id="offer-currency-price<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>"></span>
                            <span id='discount_text<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>'></span>
                            <span class='c-hide offer-currency-id' id="offer-currency-id<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>"></span>
                            <span class='c-hide offer-currency-flag' id="offer-currency-flag<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>"></span>
                        </div>
                    </div>
                </div>
                <!-- *** Hotel Card image End *** -->

                <!-- *** Hotel Card Info *** -->
                <div class="cardList-info" role="button">

                    <!--<button class="expandSect">View Details</button>-->

                    <div class="dividerSection type-1 noborder">
                        <div class="divider s1">
                            <h4 class="cardTitle">
                                <?php echo $hotel_results_array[$hotel_i]['hotel_name']; ?>
                                <?php if ($hotel_results_array[$hotel_i]['star_category'] != '') { ?>
                                    <div class="hotelStar">
                                        <div class="c-starRating cust s<?= $hotel_results_array[$hotel_i]['star_category'] ?>">
                                            <span class="stars"></span>
                                        </div>
                                    </div>
                                <?php } ?>
                            </h4>

                            <div class="infoSection">
                                <?php if ($hotel_results_array[$hotel_i]['hotel_address'] != '') { ?>
                                    <span class="cardInfoLine">
                                        <?php echo $hotel_results_array[$hotel_i]['hotel_address'] ?>
                                    </span>
                                <?php } ?>
                            </div>
                            <div class="c-tagSection">
                                <?php if ($hotel_results_array[$hotel_i]['meal_plan'] != '') { ?>
                                    <span class="tag"><?= $hotel_results_array[$hotel_i]['meal_plan'] ?></span>
                                <?php } ?>
                            </div>

                            <div class="c-aminityListBlock">
                                <ul>
                                    <?php if ($hotel_results_array[$hotel_i]['amenity'][0] != '') { ?>
                                        <script>
                                            var ameities = getObjects(amenities, 'name', '<?php echo $hotel_results_array[$hotel_i]['amenity'][0]; ?>');
                                            document.getElementById("amenity1<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>").src = '../../images/amenities/' + ameities[0]['image'];
                                        </script>
                                        <li>
                                            <div class="amenity">
                                                <img id='amenity1<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>' alt="" />
                                                <span><?= $hotel_results_array[$hotel_i]['amenity'][0] ?></span>
                                            </div>
                                        </li>
                                        <script>
                                            var ameities2 = getObjects(amenities, 'name', '<?php echo $hotel_results_array[$hotel_i]['amenity'][1]; ?>');
                                            document.getElementById("amenity2<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>").src = '../../images/amenities/' + ameities2[0]['image'];
                                        </script>
                                    <?php }
                                    if ($hotel_results_array[$hotel_i]['amenity'][1] != '') { ?>
                                        <li>
                                            <div class="amenity">
                                                <img id='amenity2<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>' alt="" />
                                                <span><?= $hotel_results_array[$hotel_i]['amenity'][1] ?></span>
                                            </div>
                                        </li>
                                        <script>
                                            var ameities3 = getObjects(amenities, 'name', '<?php echo $hotel_results_array[$hotel_i]['amenity'][2]; ?>');
                                            document.getElementById("amenity3<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>").src = '../../images/amenities/' + ameities3[0]['image'];
                                        </script>
                                    <?php }
                                    if ($hotel_results_array[$hotel_i]['amenity'][2] != '') { ?>
                                        <li>
                                            <div class="amenity">
                                                <img id='amenity3<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>' alt="" />
                                                <span><?= $hotel_results_array[$hotel_i]['amenity'][2] ?></span>
                                            </div>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    $hotel_amenity = ($hotel_results_array[$hotel_i]['amenity'] != '') ? $hotel_results_array[$hotel_i]['amenity'] : [];
                                    if (sizeof($hotel_amenity) - 3 > 0) { ?>
                                        <li>
                                            <div class="amenity st-last">
                                                <span class="num">+<?= sizeof($hotel_amenity) - 3 ?></span>
                                                <span class="txt">more</span>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="divider s2">
                            <span class="priceTag">
                                <div class="p-old c-hide" id="old_price<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>">
                                    <span class="o_lbl">Old Price</span>
                                    <span class="o_price">
                                        <span class="p_currency currency-icon"></span>
                                        <span class="p_cost original-currency-price" id="price_sub<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>"></span>
                                        <span class="c-hide original-currency-id" id="price_subid<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>"></span>
                                    </span>
                                </div>
                                <div class="p-old">
                                    <span class="o_lbl">Total Price</span>
                                    <span class="price_main">
                                        <span class="p_currency currency-icon"></span>
                                        <span class="p_cost currency-price" id="best_cost<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>"></span>
                                        <span class="c-hide currency-id" id="best_cost_cid<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>"></span>
                                    </span>
                                </div>
                            </span>
                            <button class="expandSect" role="button" data-toggle="collapse" href="#collapseExample<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>" aria-expanded="false" aria-controls="collapseExample">View Details</button>
                        </div>
                    </div>
                </div>
                <!-- *** Hotel Card Info End *** -->
            </div>

            <!-- *** Hotel Details Accordian *** -->
            <div class="collapse hidden" id="collapseExample<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>">
                <div class="cardList-accordian">
                    <!-- ***** Hotel Info Tabs ***** -->
                    <div class="c-compTabs">
                        <ul class="nav nav-pills" id="myTab" role="tablist">
                            <li class="nav-item active">
                                <a class="nav-link active" id="costingTab-<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>" data-toggle="tab" href="#costing-<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>" role="tab" aria-controls="costing" aria-selected="true">Costing</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="description-tab<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>" data-toggle="tab" href="#description<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>" role="tab" aria-controls="description" aria-selected="true">Description</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="policies-tab<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>" data-toggle="tab" href="#policies<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>" role="tab" aria-controls="policies" aria-selected="true">Policies</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link js-gallery" id="galleryTab-<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>" data-toggle="tab" href="#gallery-<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>" role="tab" aria-controls="gallery" aria-selected="true">Gallery</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <!-- **** Tab Hotel Listing **** -->

                            <!-- **** Tab costing start **** -->
                            <div class="tab-pane show active fade" id="costing-<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>" role="tabpanel" aria-labelledby="costing-tab<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>">

                                <?php
                                //Room Type Result Final  Result Display
                                $check_date_size = (isset($hotel_results_array[$hotel_i]['checkDate_array'])) ? $hotel_results_array[$hotel_i]['checkDate_array'] : [];
                                $last_date = sizeof($check_date_size) - 2;
                                $all_costs_array = array();
                                $original_costs_array = array();
                                for ($i = 0; $i < sizeof($hotel_results_array[$hotel_i]['final_room_type_array']); $i++) {
                                    if ($hotel_results_array[$hotel_i]['final_room_type_array'][$i]['check_date'] == $check_date_size[$last_date]) {

                                        $room_cost_array = (isset($hotel_results_array[$hotel_i]['final_room_type_array'][$i]['room_cost'])) ? ($hotel_results_array[$hotel_i]['final_room_type_array'][$i]['room_cost']) : [];

                                        $child_cost_array = (isset($hotel_results_array[$hotel_i]['final_room_type_array'][$i]['child_cost'])) ? ($hotel_results_array[$hotel_i]['final_room_type_array'][$i]['child_cost']) : [];

                                        $daywise_exbcost_array = (isset($hotel_results_array[$hotel_i]['final_room_type_array'][$i]['daywise_exbcost'])) ? ($hotel_results_array[$hotel_i]['final_room_type_array'][$i]['daywise_exbcost']) : [];

                                        $markup_type_array = (isset($hotel_results_array[$hotel_i]['final_room_type_array'][$i]['markup_type'])) ? ($hotel_results_array[$hotel_i]['final_room_type_array'][$i]['markup_type']) : [];

                                        $markup_amount_array = (isset($hotel_results_array[$hotel_i]['final_room_type_array'][$i]['markup_amount'])) ? ($hotel_results_array[$hotel_i]['final_room_type_array'][$i]['markup_amount']) : [];

                                        $h_currency_id = $hotel_results_array[$hotel_i]['final_room_type_array'][$i]['currency_id'];
                                        // Roomcost For loop
                                        $room_cost_temp = 0;
                                        $child_cost_temp = 0;
                                        $exbed_cost_temp = 0;
                                        $markup = 0;
                                        $room_cost = 0;
                                        for ($m = 0; $m < sizeof($room_cost_array); $m++) {
                                            $room_cost_temp = $room_cost_array[$m] + $child_cost_array[$m] + $daywise_exbcost_array[$m];
                                            if ($markup_type_array[$m] == 'Percentage') {
                                                $markup = ($room_cost_temp * ($markup_amount_array[$m] / 100));
                                            } else {
                                                $markup = ($markup_amount_array[$m]);
                                            }
                                            $room_cost = $room_cost + $room_cost_temp + $markup;
                                        }
                                        $room_cost = ceil($room_cost);
                                        $sq_from = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$h_currency_id'"));
                                        $from_currency_rate = $sq_from['currency_rate'];
                                        $c_amount = ($to_currency_rate != '') ? 1 / $from_currency_rate * $room_cost : 0;

                                        array_push($original_costs_array, $c_amount);
                                        $offer_text = '';
                                        $offer_price_display = '';
                                        $offer_price_flag = '';
                                        $offer_currency_id_val = $currency;
                                        $coupon_offer = 0;
                                        $offer_in_value = strtolower(trim($hotel_results_array[$hotel_i]['final_room_type_array'][$i]['offer_in'] ?? ''));
                                        $offer_type_value = strtolower(trim($hotel_results_array[$hotel_i]['final_room_type_array'][$i]['offer_type'] ?? ''));
                                        if ($offer_type_value != '') {
                                            $offer_amount = (float)$hotel_results_array[$hotel_i]['final_room_type_array'][$i]['offer_amount'];
                                            if ($offer_type_value == 'offer') {
                                                if ($offer_in_value === 'percentage') {
                                                    $coupon_offer = ($c_amount * ($offer_amount / 100));
                                                } else {
                                                    if ($currency != $b2b_currency) {
                                                        $sq_to = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$h_currency_id'"));
                                                        $to_currency_rate = $sq_to['currency_rate'];
                                                        $coupon_offer = ($to_currency_rate != '') ? 1 / $from_currency_rate * $offer_amount : 0;
                                                    } else {
                                                        $sq_to = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$b2b_currency'"));
                                                        $to_currency_rate = $sq_to['currency_rate'];
                                                        $coupon_offer = ($to_currency_rate != '') ? 1 / $from_currency_rate * $offer_amount : 0;
                                                    }
                                                }
                                            } else if ($offer_type_value == 'coupon') {
                                                $coupon_offer = 0;
                                            }

                                            if ($offer_in_value === 'percentage') {
                                                $offer_price_display = rtrim(rtrim(number_format($offer_amount, 2, '.', ''), '0'), '.') . '%';
                                                $offer_price_flag = 'percentage';
                                                $offer_currency_id_val = 'PERCENT';
                                                $offer_text = rtrim(rtrim(number_format($offer_amount, 2, '.', ''), '0'), '.') . '% ' . $hotel_results_array[$hotel_i]['final_room_type_array'][$i]['offer_type'];
                                            } else {
                                                $offer_text = ' ' . $hotel_results_array[$hotel_i]['final_room_type_array'][$i]['offer_type'];
                                                $offer_price_display = sprintf("%.2f", $coupon_offer);
                                                $offer_price_flag = 'no';
                                                $offer_currency_id_val = $currency;
                                            }
                                        }
                                        if ($offer_type_value == 'offer') {
                                            $c_amount = $c_amount - $coupon_offer;
                                        }
                                        $c_amount = ceil($c_amount);
                                        array_push($all_costs_array, $c_amount);
                                ?>
                                        <div class="c-cardListHolder">
                                            <div class="c-cardListTable type-2" role="button">
                                                <input class="btn-radio" type="radio" id="<?= $hotel_results_array[$hotel_i]['hotel_id'] . $i ?>" name="result_day<?= $hotel_results_array[$hotel_i]['hotel_id'] . $hotel_results_array[$hotel_i]['final_room_type_array'][$i]['room_count'] ?>" value='<?php echo "Room " . $hotel_results_array[$hotel_i]['final_room_type_array'][$i]['room_count'] . '-' . $hotel_results_array[$hotel_i]['final_room_type_array'][$i]['category'] . '-' . $c_amount . '-' . $h_currency_id ?>'>
                                                <input type="hidden" id="coupon-<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>" value='<?php echo json_encode($coupon_list_arr); ?>'>
                                                <!-- *** Type Card Info *** -->
                                                <label class="cardList-info" for="<?= $hotel_results_array[$hotel_i]['hotel_id'] . $i ?>" role="button">
                                                    <div class="flexGrid">
                                                        <div class="gridItem">
                                                            <div class="infoCard">
                                                                <span class="infoCard_data">
                                                                    <?php echo "Room-" . $hotel_results_array[$hotel_i]['final_room_type_array'][$i]['room_count'] . ' : ' . $hotel_results_array[$hotel_i]['final_room_type_array'][$i]['category']; ?>
                                                                    <!-- <span class="infoCard_notifi m5-l">Check Availability</span> -->
                                                                </span>
                                                            </div>
                                                            <div class="styleForMobile">
                                                                <div class="infoCard c-halfText m0">
                                                                    <span class="sect">Max Guests:</span>
                                                                    <span class="sect s2"><?= $hotel_results_array[$hotel_i]['final_room_type_array'][$i]['max_occupancy'] ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="gridItem">
                                                            <div class="infoCard m0">
                                                                <div class="M-infoCard">
                                                                    <span class="infoCard_label">Total Price</span>
                                                                    <span class="infoCard_price">
                                                                        <span class="p_currency currency-icon"></span>
                                                                        <span class="p_cost room-currency-price"><?= $c_amount ?></span>
                                                                        <span class="c-hide room-currency-id"><?= $h_currency_id ?></span>
                                                                    </span>
                                                                    <span class="infoCard_priceTax">(Excl of all taxes)</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                                <!-- *** Type Card Info End *** -->
                                            </div>
                                        </div>
                                <?php
                                    }
                                }
                                $best_cost = (sizeof($all_costs_array)) ? min($all_costs_array) : [];
                                $original_cost = (sizeof($original_costs_array)) ? min($original_costs_array) : [];
                                $best_cost = ceil($best_cost);
                                $original_cost = ceil($original_cost);
                                ?>
                                <script>
                                    setTimeout(function() {
                                        document.getElementById('best_cost<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>').innerHTML = '<?php echo sprintf("%.2f", $best_cost); ?>';
                                        document.getElementById('best_cost_cid<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>').innerHTML = '<?= $h_currency_id ?>';

                                        if (parseFloat(<?= $original_cost ?>) != parseFloat(<?= $best_cost ?>)) {
                                            document.getElementById('old_price<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>').classList.remove('c-hide');
                                            document.getElementById('price_sub<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>').innerHTML = '<?php echo sprintf("%.2f", $original_cost); ?>';
                                            document.getElementById('price_subid<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>').innerHTML = '<?= $h_currency_id ?>';
                                        } else {
                                            document.getElementById('old_price<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>').style.display = 'none';
                                        }

                                        //Offer red strip display
                                        if ('<?= $offer_text ?>' != '') {
                                            var discountEl<?= $hotel_results_array[$hotel_i]['hotel_id'] ?> = document.getElementById("discount<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>");
                                            var discountTextEl<?= $hotel_results_array[$hotel_i]['hotel_id'] ?> = discountEl<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.querySelector('.discount-text');
                                            discountEl<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.classList.remove("c-hide");
                                            discountEl<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.classList.add("c-show");
                                            document.getElementById("offer-currency-id<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>").innerHTML = '<?= $offer_currency_id_val ?>';
                                            document.getElementById("offer-currency-flag<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>").innerHTML = '<?= $offer_price_flag ?>';
                                            var offerPriceEl<?= $hotel_results_array[$hotel_i]['hotel_id'] ?> = document.getElementById("offer-currency-price<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>");
                                            var offerIcon<?= $hotel_results_array[$hotel_i]['hotel_id'] ?> = discountTextEl<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.querySelector('.currency-icon');
                                            var discountTextSpan<?= $hotel_results_array[$hotel_i]['hotel_id'] ?> = document.getElementById("discount_text<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>");

                                            if ('<?= $offer_price_flag ?>' === 'percentage') {
                                                if (offerPriceEl<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>) {
                                                    offerPriceEl<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.style.display = 'none';
                                                }
                                                if (offerIcon<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>) {
                                                    offerIcon<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.style.display = 'none';
                                                }
                                                if (discountTextSpan<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>) {
                                                    // discountTextSpan<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.className = '';
                                                    // discountTextSpan<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.style.marginLeft = '0';
                                                    // discountTextSpan<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.style.display = 'inline-block';
                                                    discountTextSpan<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.innerHTML = '<?= $offer_text ?>';
                                                }
                                            } else {
                                                if (offerPriceEl<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>) {
                                                    offerPriceEl<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.innerHTML = '<?= $offer_price_display ?>';
                                                    offerPriceEl<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.style.display = 'inline-block';
                                                }
                                                if (offerIcon<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>) {
                                                    offerIcon<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.style.display = 'inline-block';
                                                }
                                                if (discountTextSpan<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>) {
                                                    // discountTextSpan<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.className = 'ml-5px';
                                                    discountTextSpan<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.style.display = 'inline-block';
                                                    // discountTextSpan<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.style.marginLeft = '3px';
                                                    discountTextSpan<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>.innerHTML = '<?= $offer_text ?>';
                                                }
                                            }
                                        } else {
                                            document.getElementById("discount<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>").classList.add("c-hide");
                                        }
                                    }, 50);
                                </script>
                                <div class="clearfix text-right">
                                    <button type="button" class="c-button md" id='<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>' onclick='enq_to_action_page("3",this.id,<?= json_encode($hotel_enq_data) ?>)'><i class="fa fa-phone-square" aria-hidden="true"></i> Enquiry</button>
                                    <button type="button" class="c-button g-button md md"
                                        id='<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>' onclick='enq_to_action_page("3",this.id,<?= json_encode($hotel_enq_data) ?>)'><i class="fa fa-contact-book" aria-hidden="true"></i> Book</button>
                                </div>
                            </div>
                            <!-- ***** Hotel costing Tabs End***** -->
                            <!-- **** Tab Description **** -->
                            <div class="tab-pane fade" id="description<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>" role="tabpanel" aria-labelledby="description-tab<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>">
                                <!-- **** Description **** -->
                                <div class="clearfix margin20-bottom">
                                    <p class="c-statictext">
                                        <?= $hotel_results_array[$hotel_i]['description'] ?>
                                    </p>
                                </div>
                                <!-- **** Description End **** -->

                                <!-- **** Amenities **** -->
                                <?php
                                $hotel_ame = ($hotel_results_array[$hotel_i]['amenity'] != '') ? $hotel_results_array[$hotel_i]['amenity'] : [];
                                if (sizeof($hotel_ame) > 1) { ?>
                                    <div class="clearfix margin20-bottom">
                                        <h3 class="c-heading">
                                            Amenities
                                        </h3>
                                        <div class="clearfix">
                                            <ul class="row c-amenitiesType2">
                                                <?php
                                                for ($i = 0; $i < sizeof($hotel_ame); $i++) {
                                                ?>
                                                    <script>
                                                        var ameities3 = getObjects(amenities, 'name', '<?php echo $hotel_ame[$i]; ?>');
                                                        document.getElementById("ameities1<?= $i . $hotel_results_array[$hotel_i]['hotel_id'] ?>").className = 'icon ' + ameities3[0]['icon'];
                                                    </script>
                                                    <li class="col-md-4 col-sm-6 col-12">
                                                        <div class="amenitiesList">
                                                            <i id="ameities1<?= $i . $hotel_results_array[$hotel_i]['hotel_id'] ?>"></i><?= $hotel_ame[$i] ?>
                                                        </div>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php } ?>
                                <!-- **** Amenities **** -->
                            </div>
                            <!-- **** Tab Description End **** -->

                            <!-- **** Tab Policies **** -->
                            <div class="tab-pane fade" id="policies<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>" role="tabpanel" aria-labelledby="policies-tab<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>">
                                <!-- **** Policies List **** -->
                                <div class="c-infoDivider">
                                    <div class="custom_texteditor">
                                        <?php echo $hotel_results_array[$hotel_i]['policies']; ?>
                                    </div>
                                </div>
                                <!-- **** Policies List End **** -->
                            </div>
                            <!-- **** Tab Policies End **** -->

                            <!-- **** Tab Gallery **** -->
                            <div class="tab-pane fade" id="gallery-<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>" role="tabpanel" aria-labelledby="gallery-tab<?= $hotel_results_array[$hotel_i]['hotel_id'] ?>">
                                <!-- **** photo List **** -->
                                <div class="clearfix">
                                    <div class="c-photoGallery js-dynamicLoad">
                                        <div class="js-photoGallery owl-carousel">
                                            <?php
                                            $thotel_id = $hotel_results_array[$hotel_i]['hotel_id'];
                                            $sq_hotelImage = mysqlQuery("select * from hotel_vendor_images_entries where hotel_id='$thotel_id'");
                                            while ($sq_singleImage = mysqli_fetch_assoc($sq_hotelImage)) {
                                                if ($sq_singleImage['hotel_pic_url'] != '') {
                                                    $image = $sq_singleImage['hotel_pic_url'];
                                                    $newUrl1 = preg_replace('/(\/+)/', '/', $image);
                                                    $newUrl1 = explode('uploads', $newUrl1);
                                                    $newUrl = BASE_URL . 'uploads' . $newUrl1[1];
                                                } else {
                                                    $newUrl = BASE_URL . 'images/dummy-image.jpg';
                                                }
                                            ?>
                                                <div class="item">
                                                    <img src="<?= $newUrl ?>" alt="" />
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- **** photo List End **** -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- *** Hotel Details Accordian End *** -->
        </div>
        </div>
        <!-- ***** Hotel Card End ***** -->
<?php
    }
} //Hotel arrays for loop
?>
<script>
    $(document).ready(function() {
        if ($('.js-photoGallery').length > 0) {

            $('.js-photoGallery').owlCarousel({
                loop: false,
                margin: 16,
                nav: true,
                dots: false,
                lazyLoad: true,
                checkVisible: true,
                slideBy: 2,
                navText: [
                    '<i class="icon it itours-arrow-left"></i>',
                    '<i class="icon it itours-arrow-right"></i>'
                ],
                responsive: {
                    0: {
                        items: 1
                    },
                    768: {
                        items: 2
                    }
                },
            });
        }

        clearTimeout(b);
        var b = setTimeout(function() {
            var amount_list = document.querySelectorAll(".currency-price");
            var amount_id = document.querySelectorAll(".currency-id");

            var original_amt_list = document.querySelectorAll(".original-currency-price");
            var original_amt_id = document.querySelectorAll(".original-currency-id");

            var room_price_list = document.querySelectorAll(".room-currency-price");
            var room_price_cid = document.querySelectorAll(".room-currency-id");

            var offer_price_list = document.querySelectorAll(".offer-currency-price");
            var offer_price_id = document.querySelectorAll(".offer-currency-id");
            var offer_currency_flag = document.querySelectorAll(".offer-currency-flag");

            //Hotel Best Cost
            var amount_arr = [];
            for (var i = 0; i < amount_list.length; i++) {
                amount_arr.push({
                    'amount': amount_list[i].innerHTML,
                    'id': amount_id[i].innerHTML
                });
            }
            sessionStorage.setItem('amount_list', JSON.stringify(amount_arr));

            //Room categorywise prices
            var roomAmount_arr = [];
            for (var i = 0; i < room_price_list.length; i++) {
                roomAmount_arr.push({
                    'amount': room_price_list[i].innerHTML,
                    'id': room_price_cid[i].innerHTML
                });
            }
            sessionStorage.setItem('room_price_list', JSON.stringify(roomAmount_arr));

            //Hotel Original Cost
            var orgAmount_arr = [];
            for (var i = 0; i < original_amt_list.length; i++) {
                orgAmount_arr.push({
                    'amount': original_amt_list[i].innerHTML,
                    'id': original_amt_id[i].innerHTML
                });
            }
            sessionStorage.setItem('original_amt_list', JSON.stringify(orgAmount_arr));

            //Hotel Offer Cost
            var offerAmount_arr = [];
            for (var i = 0; i < offer_price_list.length; i++) {
                offerAmount_arr.push({
                    'amount': offer_price_list[i].innerHTML,
                    'id': offer_price_id[i].innerHTML,
                    'flag': offer_currency_flag[i].innerHTML
                });
            }
            sessionStorage.setItem('offer_price_list', JSON.stringify(offerAmount_arr));

            hotel_page_currencies();
        }, 500);
    });
</script>
<script>
    setTimeout(() => {

        var hotel_type_array = JSON.parse(document.getElementById('hotel_type_array').value);
        var selected_hotel_type_array = (document.getElementById('selected_hotel_type_array').value).split(',');
        var html = '';
        for (var i = 0; i < hotel_type_array.length; i++) {
            var checked_status = (selected_hotel_type_array.includes(hotel_type_array[i])) ? 'checked' : '';
            html += '<div class="form-check"><input type="checkbox" name="hotel_type" class="form-check-input lblhtfilterChk" id="' + (i + 1) + '" value="' + hotel_type_array[i] + '" ' + checked_status + '/><label class="form-check-label lblhtfilterChk" for="' + (i + 1) + '">' + hotel_type_array[i] + '</label></div>';
        }
        $('#hotel_types').html(html);
        // hotel_category_array
        var hotel_category_array = JSON.parse(document.getElementById('hotel_category_array').value);
        var selected_hotel_category_array = (document.getElementById('selected_hotel_category_array').value).split(',');
        var html = '';
        for (var i = 0; i < hotel_category_array.length; i++) {
            var checked_status = (selected_hotel_category_array.includes(String(hotel_category_array[i]))) ? 'checked' : '';
            html += '<div class="form-check"><input type="checkbox" name="hotel_category" class="form-check-input lblhcfilterChk" id="hc-' + (i + 1) + '" value="' + hotel_category_array[i] + '" ' + checked_status + '/><label class="form-check-label lblhcfilterChk" for="hc-' + (i + 1) + '">' + '<div class="hotelStar"><div class="c-starRating cust s' + hotel_category_array[i] + '"><span class="stars"></span></div></div>' + '</label></div>';
        }
        $('#hotel_categories').html(html);
        // hotel_amenity_array
        var hotel_amenities_array = JSON.parse(document.getElementById('hotel_amenity_array').value);
        var selected_hotel_amenities_array = (document.getElementById('selected_hotel_amenity_array').value).split(',');
        var html = '';
        for (var i = 0; i < hotel_amenities_array.length; i++) {
            var checked_status = (selected_hotel_amenities_array.includes(String(hotel_amenities_array[i]))) ? 'checked' : '';
            html += '<div class="form-check"><input type="checkbox" name="hotel_amenity" class="form-check-input lblpafilterChk" id="pa-' + (i + 1) + '" value="' + hotel_amenities_array[i] + '" ' + checked_status + '/><label class="form-check-label lblpafilterChk" for="pa-' + (i + 1) + '">' + hotel_amenities_array[i] + '</label></div>';
        }
        $('#hotel_amenities').html(html);

    }, 500);
</script>