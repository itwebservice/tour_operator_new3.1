<?php
session_start();
$tours_result_array = ($_POST['data'] != NULL) ? $_POST['data'] : [];
global $app_contact_no;
$coupon_list_arr = array();
$coupon_info_arr = array();
if (sizeof($tours_result_array) > 0) {

  for ($i = 0; $i < sizeof($tours_result_array); $i++) {

    $h_currency_id = $tours_result_array[$i]['currency_id'];
    $adult_count = $tours_result_array[$i]['adult_count'];
    $child_wocount = $tours_result_array[$i]['child_wocount'];
    $child_wicount = $tours_result_array[$i]['child_wicount'];
    $extra_bed_count = $tours_result_array[$i]['extra_bed_count'];
    $infant_count = $tours_result_array[$i]['infant_count'];
    $travel_date = $tours_result_array[$i]['travel_date'];
    $sp_cost = $tours_result_array[$i]['sp_cost_total'];

    $total_pax = intval($adult_count) + intval($child_wocount) + intval($child_wicount) + intval($extra_bed_count) + intval($infant_count);
    $adult_cost = ($total_pax > 1) ? (float)($tours_result_array[$i]['adult_cost']) : $sp_cost;
    $link = 'https://web.whatsapp.com/send?phone=' . $app_contact_no . '&text=Hello,
%20I%20am%20interested%20in%20' . $tours_result_array[$i]['tour_name'] . ' (' . $tours_result_array[$i]['travel_date'] . ') tour' . '%20. Kindly%20provide%20more%20details.%20Thanks!';
?>
    <input type="hidden" id="tour_id-<?= $tours_result_array[$i]['tour_id'] ?>" value="<?= $tours_result_array[$i]['tour_id'] ?>">
    <input type="hidden" id="group_id-<?= $tours_result_array[$i]['tour_id'] ?>" value="<?= $tours_result_array[$i]['group_id'] ?>">
    <input type="hidden" id="adult_count" value="<?= $adult_count ?>" />
    <input type="hidden" id="child_wocount" value="<?= $child_wocount ?>" />
    <input type="hidden" id="child_wicount" value="<?= $child_wicount ?>" />
    <input type="hidden" id="extra_bed_count" value="<?= $extra_bed_count ?>" />
    <input type="hidden" id="infant_count" value="<?= $infant_count ?>" />
    <input type="hidden" id="travel_date" value="<?= $travel_date ?>" />
    <!-- ***** Tours Card ***** -->
    <div class="c-cardList type-hotel">
      <div class="c-cardListTable tours-cardListTable">
        <!-- *** Tours Card image *** -->
        <div class="cardList-image">
          <img src="<?= $tours_result_array[$i]['image'] ?>" alt="iTours" class="d-block mb-2" />
          <input type="hidden" value="<?= $tours_result_array[$i]['image'] ?>" id="image-<?= $tours_result_array[$i]['tour_id'] ?>" />

          <div class="d_flex">
            <a target="_blank" href="<?= $link ?>" class="btn btn-outline-success d-block mb-lg-2"><i class="fa fa-whatsapp"></i> Whatsapp</a>

            <a href="" class="btn btn-outline-danger d-block" data-toggle="modal" data-target="#modal<?= $tours_result_array[$i]['tour_id'] ?>"><i class="fa fa-calculator" aria-hidden="true"></i> Price Calculator</a>

          </div>
          <div class="typeOverlay">
          </div>
        </div>
        <!-- *** Tours Card image End *** -->
        <!-- *** Tours Card Info *** -->
        <div class="cardList-info" role="button">
          <div class="dividerSection type-1 noborder">
            <div class="divider s1" role="button" data-toggle="collapses" href="#collapseExample<?= $tours_result_array[$i]['tour_id'] ?>" aria-expanded="false" aria-controls="collapseExample">
              <a href="#">
                <h4 class="cardTitle"><span id="tour-<?= $tours_result_array[$i]['tour_id'] ?>"><?= $tours_result_array[$i]['tour_name'] ?></span>
                </h4>

              </a>

              <div class="infoSection">
                <span class="cardInfoLine">
                  <?= $tours_result_array[$i]['dest_name'] ?>
                </span>
              </div>

              <div class="infoSection">
                <span class="cardInfoLine cust">
                  <i class="icon it itours-calendar"></i>
                  <?= $tours_result_array[$i]['travel_date'] ?>
                  <input type="hidden" value="<?= $tours_result_array[$i]['travel_date'] ?>" id="tour_date-<?= $tours_result_array[$i]['tour_id'] ?>" />
                </span>
              </div>

            </div>

            <div class="divider s2">
              <div class="priceTag tourPrice">
                <div class="p-old">
                  <span class="o_lbl">Total Cost</span>
                  <span class="price_main">
                    <span class="p_currency currency-icon"></span>
                    <span class="p_cost tours-currency-price" id="total_cost-<?= $tours_result_array[$i]['tour_id'] ?>"><?= $tours_result_array[$i]['total_cost'] ?></span>
                    <span class="c-hide tours-currency-id" id="h_currency_id-<?= $tours_result_array[$i]['tour_id'] ?>"><?= $h_currency_id ?></span>
                  </span>
                  <small class="mb-2 mb-md-0">(Excl of all taxes)</small>
                  <input type="hidden" id="tours-cost-<?= $tours_result_array[$i]['tour_id'] ?>" value='<?php echo $tours_result_array[$i]['total_cost'] . '-' . $h_currency_id ?>'>
                </div>
                <a target="_blank" href="<?php echo $tours_result_array[$i]['seo_slug']; ?>" class="expandSect">View Details</a>
              </div>
            </div>
          </div>
          <div class="customizedTour">
            <h3 class="customizedTour-title">Fixed Departure
              <div class="danger_bg seat_availability main_block_xs text_center_sm_xs mg_bt_10_sm_xs" id="tseats">
                Total Seats : <?php echo $tours_result_array[$i]['total_seats']; ?>
              </div>
              <div class="info_bg seat_availability main_block_xs text_center_sm_xs" id="aseats">
                Available Seats : <?php echo $tours_result_array[$i]['available_seats']; ?>
              </div>
            </h3>

            <ul class="customizedTour-list">
              <li class="customizedTour-item">
                <i class="fa fa-hotel"></i>
                <span>Hotel</span>
              </li>
              <li class="customizedTour-item">
                <i class="fa fa-camera"></i>
                <span>Sightseeing</span>
              </li>
              <li class="customizedTour-item">
                <i class="fa fa-car"></i>
                <span>Transfer</span>
              </li>
              <li class="customizedTour-item">
                <i class="fa-solid fa-utensils"></i>
                <span>Meals</span>
              </li>
            </ul>
          </div>
          <div class="tourOfferText">
            <span class="tourOfferLabel">Highlights</span>
            <p class="tourOfferDec"><?php
                                    echo $tours_result_array[$i]['tour_note'];
                                    ?></p>
          </div>
        </div>
        <!-- *** Tours Card Info End *** -->
      </div>

      <!-- *** Tours Details Accordian *** -->
      <div class="collapse" id="collapseExample<?= $tours_result_array[$i]['tour_id'] ?>">
        <div class="cardList-accordian">
          <!-- ***** Tours Info Tabs ***** -->
          <div class="c-compTabs">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="itinerary-tab" data-toggle="tab" href="#itinerary-tab<?= $tours_result_array[$i]['tour_id'] ?>" role="tab" aria-controls="itinerary" aria-selected="true">Itinerary</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="travel-tab" data-toggle="tab" href="#travel-tab<?= $tours_result_array[$i]['tour_id'] ?>" role="tab" aria-controls="travel" aria-selected="true">Travel & Stay</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="inclusion-tab" data-toggle="tab" href="#inclusion-tab<?= $tours_result_array[$i]['tour_id'] ?>" role="tab" aria-controls="inclusion" aria-selected="true">Inclusion/Exclusion</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="costing-tab" data-toggle="tab" href="#costing-tab<?= $tours_result_array[$i]['tour_id'] ?>" role="tab" aria-controls="costing" aria-selected="true">Costing</a>
              </li>
            </ul>

            <div class="tab-content" id="myTabContent">
              <!-- **** Tab costing **** -->
              <div class="tab-pane fade" id="costing-tab<?= $tours_result_array[$i]['tour_id'] ?>" role="tabpanel" aria-labelledby="costing-tab">

                <!-- **** Policies List **** -->
                <div class="clearfix m20-btm">
                  <div class="row">
                    <div class="col-12">
                      <div class="c-flexCards">

                        <div class="f_card">
                          <span class="currency_icon currency-icon"></span>
                          <span class="currency_amount adult_cost-currency-price"><?= (float)($adult_cost) / intval($adult_count) ?></span>
                          <span class="c-hide adult-currency-id"><?= $h_currency_id ?></span>
                          <span class="currency_for">For Adult (PP)</span>
                        </div>
                        <?php
                        if ($child_wocount > 0) { ?>
                          <div class="f_card">
                            <span class="currency_icon currency-icon"></span>
                            <span class="currency_amount childwio_cost-currency-price"><?= (float)($tours_result_array[$i]['child_wo_cost']) / intval($child_wocount) ?></span>
                            <span class="c-hide childwio-currency-id"><?= $h_currency_id ?></span>
                            <span class="currency_for">For Child Without Bed (PP)</span>
                          </div>
                        <?php } ?>
                        <?php
                        if ($child_wicount > 0) { ?>
                          <div class="f_card">
                            <span class="currency_icon currency-icon"></span>
                            <span class="currency_amount childwi_cost-currency-price"><?= (float)($tours_result_array[$i]['child_wi_cost']) / intval($child_wicount) ?></span>
                            <span class="c-hide childwi-currency-id"><?= $h_currency_id ?></span>
                            <span class="currency_for">For Child With Bed (PP)</span>
                          </div>
                        <?php } ?>
                        <?php
                        if ($extra_bed_count > 0) { ?>
                          <div class="f_card">
                            <span class="currency_icon currency-icon"></span>
                            <span class="currency_amount extrabed-currency-price"><?= (float)($tours_result_array[$i]['with_bed_cost']) / intval($extra_bed_count) ?></span>
                            <span class="c-hide extrabed-currency-id"><?= $h_currency_id ?></span>
                            <span class="currency_for">For Extra Bed (PP)</span>
                          </div>
                        <?php } ?>
                        <?php
                        if ($infant_count > 0) { ?>
                          <div class="f_card">
                            <span class="currency_icon currency-icon"></span>
                            <span class="currency_amount infant_cost-currency-price"><?= (float)($tours_result_array[$i]['infant_cost']) / intval($infant_count) ?></span>
                            <span class="c-hide infant_cost-currency-id"><?= $h_currency_id ?></span>
                            <span class="currency_for">For Infant (PP)</span>
                          </div>
                        <?php } ?>

                      </div>
                    </div>
                  </div>

                </div>
                <!-- **** Policies List End **** -->

              </div>
              <!-- **** Tab costing End **** -->

              <!-- **** Tab itenary **** -->
              <div class="tab-pane show active fade" id="itinerary-tab<?= $tours_result_array[$i]['tour_id'] ?>" role="tabpanel" aria-labelledby="itinerary-tab">

                <!-- **** Day Info List **** -->
                <div class="c-cardListInfo">
                  <div class="cardListInfo-row">
                    <!-- **** List **** -->
                    <?php for ($pi = 0; $pi < sizeof($tours_result_array[$i]['program_array']); $pi++) { ?>
                      <div class="ListInfo-col">

                        <div class="dayCount">
                          <span class="s1">DAY</span>
                          <span class="s2"><?= ($pi + 1) ?></span>
                        </div>

                        <div class="dayInfo">
                          <h5 class="h1"><?= $tours_result_array[$i]['program_array'][$pi]['attraction'] ?></h5>
                          <span class="staticText">
                            <?= $tours_result_array[$i]['program_array'][$pi]['day_wise_program'] ?>
                          </span>
                          <div class="itemList">
                            <span class="item">
                              <i class="icon it itours-bed"></i>
                              <?= $tours_result_array[$i]['program_array'][$pi]['stay'] ?>
                            </span>
                            <?php if ($tours_result_array[$i]['program_array'][$pi]['meal_plan'] != '') { ?>
                              <span class="item">
                                <i class="icon it itours-cutlery"></i>
                                <?= $tours_result_array[$i]['program_array'][$pi]['meal_plan'] ?>
                              </span>
                            <?php } ?>
                          </div>
                        </div>

                      </div>
                    <?php } ?>
                    <!-- **** List End **** -->
                  </div>
                </div>
                <!-- **** Day Info List **** -->
              </div>
              <!-- **** Tab itenary End **** -->

              <!-- **** Tab Tours Car **** -->
              <div class="tab-pane fade" id="travel-tab<?= $tours_result_array[$i]['tour_id'] ?>" role="tabpanel" aria-labelledby="travel-tab">
                <!-- **** Tab Hotel Car **** -->
                <div class="clearfix m20-btm">
                  <div class="row">
                    <div class="col-12 m20-btm">
                      <h3 class="c-heading">
                        Hotel Details
                      </h3>
                      <?php
                      $tours_result_array[$i]['hotels_array'] = ($tours_result_array[$i]['hotels_array']) ? $tours_result_array[$i]['hotels_array'] : [];
                      for ($hotel_i = 0; $hotel_i < sizeof($tours_result_array[$i]['hotels_array']); $hotel_i++) {
                      ?>
                        <!-- *** Hotel Card Info *** -->
                        <div class="c-cardListHolder">
                          <div class="c-cardListTable type-3">

                            <div class="cardList-info">
                              <div class="flexGrid">
                                <div class="gridItem">
                                  <div class="infoCard">
                                    <span class="infoCard_price"><?= $tours_result_array[$i]['hotels_array'][$hotel_i]['hotel'] ?></span>
                                    <span class="infoCard_data"><?= $tours_result_array[$i]['hotels_array'][$hotel_i]['city'] ?></span>
                                  </div>
                                </div>

                                <div class="gridItem">
                                  <div class="infoCard c-halfText m0">
                                    <span class="infoCard_label">Hotel Category</span>
                                    <span class="infoCard_price"><?= $tours_result_array[$i]['hotels_array'][$hotel_i]['hotel_type'] ?></span>
                                  </div>
                                </div>

                                <div class="gridItem styleForMobile M-m0">
                                  <div class="infoCard m5-btm">
                                    <span class="infoCard_label">Stay Duration</span>
                                    <span class="infoCard_price"><?= $tours_result_array[$i]['hotels_array'][$hotel_i]['nights'] ?> Nights</span>
                                  </div>
                                </div>
                              </div>
                            </div>

                          </div>
                        </div>
                        <!-- *** Hotel Card Info End *** -->
                      <?php } ?>

                    </div>

                    <?php
                    $train_array = (isset($tours_result_array[$i]['train_array'])) ? $tours_result_array[$i]['train_array'] : [];
                    if (sizeof($train_array) > 0) { ?>
                      <div class="col-12 m20-btm">
                        <h3 class="c-heading">
                          Train Details
                        </h3>
                        <?php
                        for ($tr_i = 0; $tr_i < sizeof($train_array); $tr_i++) { ?>
                          <!-- *** Train Card Info *** -->
                          <div class="c-cardListHolder">
                            <div class="c-cardListTable type-3">

                              <div class="cardList-info">
                                <div class="flexGrid">
                                  <div class="gridItem">
                                    <div class="infoCard c-halfText m0">
                                      <span class="infoCard_label">From Location</span>
                                      <span class="infoCard_price"><?= $train_array[$tr_i]['from_location'] ?></span>
                                    </div>
                                  </div>

                                  <div class="gridItem styleForMobile M-m0">
                                    <div class="infoCard m5-btm">
                                      <span class="infoCard_label">To Location</span>
                                      <span class="infoCard_price"><?= $train_array[$tr_i]['to_location'] ?></span>
                                    </div>
                                  </div>
                                  <div class="gridItem styleForMobile M-m0">
                                    <div class="infoCard m5-btm">
                                      <span class="infoCard_label">Class</span>
                                      <span class="infoCard_price"><?= $train_array[$tr_i]['class'] ?></span>
                                    </div>
                                  </div>
                                </div>
                              </div>

                            </div>
                          </div>
                          <!-- *** Train Card Info End *** -->
                        <?php } ?>
                      </div>
                    <?php } ?>

                    <?php
                    $tours_result_array[$i]['flight_array'] = (isset($tours_result_array[$i]['flight_array'])) ? $tours_result_array[$i]['flight_array'] : [];
                    if (sizeof($tours_result_array[$i]['flight_array']) > 0) { ?>
                      <div class="col-12 m20-btm">
                        <h3 class="c-heading">
                          Flight Details
                        </h3>
                        <?php
                        for ($tr_i = 0; $tr_i < sizeof($tours_result_array[$i]['flight_array']); $tr_i++) { ?>
                          <!-- *** Flight Card Info *** -->
                          <div class="c-cardListHolder">
                            <div class="c-cardListTable type-3">

                              <div class="cardList-info">
                                <div class="flexGrid">
                                  <div class="gridItem">
                                    <div class="infoCard c-halfText m0">
                                      <span class="infoCard_label">From Location</span>
                                      <span class="infoCard_price"><?= $tours_result_array[$i]['flight_array'][$tr_i]['from_location'] ?></span>
                                    </div>
                                  </div>

                                  <div class="gridItem styleForMobile M-m0">
                                    <div class="infoCard m5-btm">
                                      <span class="infoCard_label">To Location</span>
                                      <span class="infoCard_price"><?= $tours_result_array[$i]['flight_array'][$tr_i]['to_location'] ?></span>
                                    </div>
                                  </div>
                                  <div class="gridItem styleForMobile M-m0">
                                    <div class="infoCard m5-btm">
                                      <span class="infoCard_label">Airline</span>
                                      <span class="infoCard_price"><?= $tours_result_array[$i]['flight_array'][$tr_i]['airline'] ?></span>
                                    </div>
                                  </div>
                                  <div class="gridItem styleForMobile M-m0">
                                    <div class="infoCard m5-btm">
                                      <span class="infoCard_label">Class</span>
                                      <span class="infoCard_price"><?= $tours_result_array[$i]['flight_array'][$tr_i]['class'] ?></span>
                                    </div>
                                  </div>
                                </div>
                              </div>

                            </div>
                          </div>
                          <!-- *** Flight Card Info End *** -->
                        <?php } ?>
                      </div>
                    <?php } ?>
                    <?php
                    $tours_result_array[$i]['cruise_array'] = (isset($tours_result_array[$i]['cruise_array']) != '') ? $tours_result_array[$i]['cruise_array'] : [];
                    if (sizeof($tours_result_array[$i]['cruise_array']) > 0) { ?>
                      <div class="col-12 m20-btm">
                        <h3 class="c-heading">
                          Cruise Details
                        </h3>
                        <?php
                        for ($tr_i = 0; $tr_i < sizeof($tours_result_array[$i]['cruise_array']); $tr_i++) { ?>
                          <!-- *** Cruise Card Info *** -->
                          <div class="c-cardListHolder">
                            <div class="c-cardListTable type-3">

                              <div class="cardList-info">
                                <div class="flexGrid">
                                  <div class="gridItem">
                                    <div class="infoCard c-halfText m0">
                                      <span class="infoCard_label">Route</span>
                                      <span class="infoCard_price"><?= $tours_result_array[$i]['cruise_array'][$tr_i]['route'] ?></span>
                                    </div>
                                  </div>

                                  <div class="gridItem styleForMobile M-m0">
                                    <div class="infoCard m5-btm">
                                      <span class="infoCard_label">Cabin</span>
                                      <span class="infoCard_price"><?= $tours_result_array[$i]['cruise_array'][$tr_i]['cabin'] ?></span>
                                    </div>
                                  </div>

                                </div>
                              </div>

                            </div>
                          </div>
                          <!-- *** Cruise Card Info End *** -->
                        <?php } ?>
                      </div>
                    <?php } ?>
                  </div>
                </div>

                <!-- **** Tab Hotel Car End **** -->
              </div>
              <!-- **** Tab Tours Car End **** -->
              <!-- **** Tab Policies **** -->
              <div class="tab-pane fade" id="inclusion-tab<?= $tours_result_array[$i]['tour_id'] ?>" role="tabpanel" aria-labelledby="inclusion-tab">
                <!-- **** Policies List **** -->
                <div class="clearfix margin20-bottom">
                  <?php if ($tours_result_array[$i]['inclusions'] != '') { ?>
                    <h3 class="c-heading">
                      Inclusions
                    </h3>
                    <div class="custom_texteditor">
                      <?= $tours_result_array[$i]['inclusions'] ?>
                    </div>
                  <?php } ?>
                  <?php if ($tours_result_array[$i]['exclusions'] != '') { ?>
                    <h3 class="c-heading">
                      Exclusions
                    </h3>
                    <div class="custom_texteditor">
                      <?= $tours_result_array[$i]['exclusions'] ?>
                    </div>
                  <?php } ?>
                  <?php if ($tours_result_array[$i]['terms'] != '') { ?>
                    <h3 class="c-heading">
                      Terms & Conditions
                    </h3>
                    <div class="custom_texteditor">
                      <?= $tours_result_array[$i]['terms'] ?>
                    </div>
                  <?php } ?>
                </div>
                <!-- **** Policies List End **** -->
              </div>
              <!-- **** Tab Policies End **** -->

              <div class="clearfix text-right">
                <button class="c-button md" id='<?= $tours_result_array[$i]['tour_id'] ?>' onclick='redirect_to_action_page("<?= $tours_result_array[$i]["tour_id"] ?>","2","",<?= $adult_count ?>,<?= $child_wocount ?>,<?= $child_wicount ?>,<?= $extra_bed_count ?>,<?= $infant_count ?>,"<?= $travel_date ?>","<?= $tours_result_array[$i]["group_id"] ?>")'><i class="fa fa-phone-square" aria-hidden="true"></i> Enquiry</button>
                <button class="c-button g-button md" id='<?= $tours_result_array[$i]['tour_id'] ?>' onclick='redirect_to_action_page("<?= $tours_result_array[$i]["tour_id"] ?>","2","",<?= $adult_count ?>,<?= $child_wocount ?>,<?= $child_wicount ?>,<?= $extra_bed_count ?>,<?= $infant_count ?>,"<?= $travel_date ?>","<?= $tours_result_array[$i]["group_id"] ?>")'><i class="fa fa-address-book" aria-hidden="true"></i> Book</button>
              </div>

            </div>
          </div>
          <!-- ***** Tours Info Tabs End***** -->
        </div>
      </div>
      <!-- *** Tours Details Accordian End *** -->
    </div>
    <!-- ***** Tours Card End ***** -->
    <?php
    $package_id = $tours_result_array[$i]['tour_id'];
    // print_r($tours_result_array);
    ?>
    <div class="modal fade" id="modal<?= $package_id ?>" aria-labelledby="modalLabel<?php echo $package_id; ?>" data-backdrop="static" data-keyboard="false" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel<?php echo $package_id; ?>" style="display: block; text-align: center !important;">
              <?php echo $tours_result_array[$i]['tour_name'] . '(' . $tours_result_array[$i]['travel_date'] . ')'; ?>
            </h5>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span> </button>
          </div>
          <div class="modal-body">

            <div class="bookingArea">
              <div class="bookingHead">
                <h6>Calculate Your Tour Estimated Price</h6>
              </div>
              <div class="bookingBody">
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label class="form-label mb-0" for="tadult<?= $package_id ?>">Adults*</label>
                    <select name="tadult" id='tadult<?= $package_id ?>' class="full-width" onchange="calculate_total_cost(<?= $package_id ?>)">
                      <?php for ($m = 0; $m <= 10; $m++) { ?>
                        <option value="<?= $m ?>"><?= $m ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label class="form-label mb-0" for="child_wobed<?= $package_id ?>">Child Without Bed(2-5 Yrs)</label>
                    <select name="child_wobed" id='child_wobed<?= $package_id ?>' class="full-width" onchange="calculate_total_cost(<?= $package_id ?>)">
                      <?php for ($m = 0; $m <= 10; $m++) { ?>
                        <option value="<?= $m ?>"><?= $m ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label class="form-label mb-0" for="child_wibed<?= $package_id ?>">Child With Bed(5-12 Yrs)</label>
                    <select name="child_wibed" id='child_wibed<?= $package_id ?>' class="full-width" onchange="calculate_total_cost(<?= $package_id ?>)">
                      <?php for ($m = 0; $m <= 10; $m++) { ?>
                        <option value="<?= $m ?>"><?= $m ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label class="form-label mb-0" for="extra_bed<?= $package_id ?>">Extra Bed</label>
                    <select name="extra_bed" id='extra_bed<?= $package_id ?>' class="full-width" onchange="calculate_total_cost(<?= $package_id ?>)">
                      <?php for ($m = 0; $m <= 10; $m++) { ?>
                        <option value="<?= $m ?>"><?= $m ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label class="form-label mb-0" for="infant<?= $package_id ?>">Infant(0-2 Yrs)</label>
                    <select name="infant" id="infant<?= $package_id ?>" class="full-width" onchange="calculate_total_cost(<?= $package_id ?>)">
                      <?php for ($m = 0; $m <= 10; $m++) { ?>
                        <option value="<?= $m ?>"><?= $m ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <!-- <div class="bookingTable">
                  <table class="table">
                    <tbody id="tour_total_cost<?php
                                              // echo $package_id ;
                                              ?>">
                    </tbody>
                  </table>
                </div> -->

                <div class="bookingTable">
                  <table class="table">
                    <tbody id="tour_total_cost<?= $package_id ?>">

                    </tbody>
                  </table>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php
  }
} //Activity arrays for loop
?>
<script>
  $(document).ready(function() {

    clearTimeout(b);
    var b = setTimeout(function() {

      var amount_list = document.querySelectorAll(".tours-currency-price");
      var amount_id = document.querySelectorAll(".tours-currency-id");

      var adult_price_list = document.querySelectorAll(".adult_cost-currency-price");
      var adult_price_cid = document.querySelectorAll(".adult-currency-id");

      var childwo_price_list = document.querySelectorAll(".childwio_cost-currency-price");
      var childwo_price_cid = document.querySelectorAll(".childwio-currency-id");

      var childwi_price_list = document.querySelectorAll(".childwi_cost-currency-price");
      var childwi_price_cid = document.querySelectorAll(".childwi-currency-id");

      var extrabed_price_list = document.querySelectorAll(".extrabed-currency-price");
      var extrabed_price_id = document.querySelectorAll(".extrabed-currency-id");

      var infant_price_list = document.querySelectorAll(".infant_cost-currency-price");
      var infant_price_id = document.querySelectorAll(".infant_cost-currency-id");

      //Tours Best Cost
      var amount_arr = [];
      for (var i = 0; i < amount_list.length; i++) {
        amount_arr.push({
          'amount': amount_list[i].innerHTML,
          'id': amount_id[i].innerHTML
        });
      }
      sessionStorage.setItem('tours_amount_list', JSON.stringify(amount_arr));

      //Adult cost prices
      var roomAmount_arr = [];
      for (var i = 0; i < adult_price_list.length; i++) {
        roomAmount_arr.push({
          'amount': adult_price_list[i].innerHTML,
          'id': adult_price_cid[i].innerHTML
        });
      }
      sessionStorage.setItem('adult_price_list', JSON.stringify(roomAmount_arr));

      //Child Wo cost prices
      var roomAmount_arr = [];
      for (var i = 0; i < childwo_price_list.length; i++) {
        roomAmount_arr.push({
          'amount': childwo_price_list[i].innerHTML,
          'id': childwo_price_cid[i].innerHTML
        });
      }
      sessionStorage.setItem('childwo_price_list', JSON.stringify(roomAmount_arr));

      //Child WI cost prices
      var roomAmount_arr = [];
      for (var i = 0; i < childwi_price_list.length; i++) {
        roomAmount_arr.push({
          'amount': childwi_price_list[i].innerHTML,
          'id': childwi_price_cid[i].innerHTML
        });
      }
      sessionStorage.setItem('childwi_price_list', JSON.stringify(roomAmount_arr));

      //Extra Bed Cost
      var offerAmount_arr = [];
      for (var i = 0; i < extrabed_price_list.length; i++) {
        offerAmount_arr.push({
          'amount': extrabed_price_list[i].innerHTML,
          'id': extrabed_price_id[i].innerHTML
        });
      }
      sessionStorage.setItem('extrabed_price_list', JSON.stringify(offerAmount_arr));

      //Infant Cost
      var offerAmount_arr = [];
      for (var i = 0; i < infant_price_list.length; i++) {
        offerAmount_arr.push({
          'amount': infant_price_list[i].innerHTML,
          'id': infant_price_id[i].innerHTML
        });
      }
      sessionStorage.setItem('infant_price_list', JSON.stringify(offerAmount_arr));
      var current_page_url = document.URL;
      group_tours_page_currencies(current_page_url);

      var total_nights_array = JSON.parse(document.getElementById('total_nights_array').value);
      var selected_total_nights_array = (document.getElementById('selected_total_nights_array').value).split(',');
      var html = '';
      for (var i = 0; i < total_nights_array.length; i++) {
        var checked_status = (selected_total_nights_array.includes(`${total_nights_array[i]}`)) ? 'checked' : '';
        html += '<div class="form-check"><input type="checkbox" name="nights" class="form-check-input nights_label" id="' + (i + 1) + '" value="' + total_nights_array[i] + '" ' + checked_status + '/><label class="form-check-label nights_label" for="' + (i + 1) + '">' + total_nights_array[i] + ' Nights - ' + (parseInt(total_nights_array[i]) + 1) + ' Days</label></div>';
      }
      $('#total_nights').html(html);
    }, 500);
  });

  function calculate_total_cost(package_id) {

    var base_url = $('#base_url').val();
    var adult_count = $("#tadult" + package_id).val();
    var child_wobed = $("#child_wobed" + package_id).val();
    var child_wibed = $("#child_wibed" + package_id).val();
    var extra_bed_c = $("#extra_bed" + package_id).val();
    var infant_c = $("#infant" + package_id).val();

    $.ajax({
      type: 'post',
      url: base_url + 'view/group_tours/inc/tours_cost_load.php',
      data: {
        package_id: package_id,
        adult_count: adult_count,
        child_wobed: child_wobed,
        child_wibed: child_wibed,
        extra_bed_c: extra_bed_c,
        infant_c: infant_c
      },
      success: function(result) {
        var cost_result = JSON.parse(result);
        let html = '';
        if (cost_result.length !== 0) {

          // html = '<tr><td style="color: red;"><b>Refer below price details...</b></tr></td>';
          cost_result.forEach(function(cost_result1, index) {
            var css = '';
            if (index == cost_result.length - 1) {
              css = 'style="color:white;background-color: #7db77d"';
            }
            html += '<tr ' + css + '><td> <b> ' + cost_result1.type + ' </b></td><td class = "text-right">  <b> ' + cost_result1.per_person + ' </b></td></tr>';
          });
        }
        $('#tour_total_cost' + package_id).html(html);
      },
    });
  }
</script>