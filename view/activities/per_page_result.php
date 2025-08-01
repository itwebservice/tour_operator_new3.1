<?php
include '../../config.php';
$activity_result_array = ($_POST['data'] != '') ? $_POST['data'] : [];
global $currency;
$b2b_currency = $_SESSION['session_currency_id'];
$coupon_list_arr = array();
$coupon_info_arr = array();
if (sizeof($activity_result_array) > 0) {
  for ($i = 0; $i < sizeof($activity_result_array); $i++) {

    $h_currency_id = $activity_result_array[$i]['currency_id'];

    $act_enq_data = array();
    $adult_count = $activity_result_array[$i]['adult_count'];
    $child_count = $activity_result_array[$i]['child_count'];
    $infant_count = $activity_result_array[$i]['infant_count'];
    $actDate = $activity_result_array[$i]['actDate'];
    $excursion_name = $activity_result_array[$i]['excursion_name'];
    $act_total_cost = $activity_result_array[$i]['best_lowest_cost']['cost'];

    $act_id = $activity_result_array[$i]['exc_id'];

    array_push($act_enq_data, array('excursion_name' => $excursion_name, 'actDate' => $actDate, 'adult_count' => $adult_count, 'child_count' => $child_count, 'infant_count' => $infant_count, 'act_total_cost' => $act_total_cost, 'act_id' => $act_id));
    $link = 'https://web.whatsapp.com/send?phone=' . $app_contact_no . '&text=Hello,
%20I%20am%20interested%20in%20' . $activity_result_array[$i]['excursion_name'] . ' (' . $activity_result_array[$i]['city_name'] . ')' . '%20. Kindly%20provide%20more%20details.%20Thanks!';

?>
    <!-- ***** Activity Card ***** -->
    <div class="c-cardList type-hotel">
      <div class="c-cardListTable tours-cardListTable">
        <!-- *** Activity Card image *** -->
        <div class="cardList-image">
          <img src="<?php echo $activity_result_array[$i]['image'] ?>" alt="iTours" />
          <a target="_blank" href="<?= $link ?>" class="btn btn-outline-success d-block mb-2"><i class="fa fa-whatsapp"></i> Whatsapp</a>
          <input type="hidden" value="<?= $activity_result_array[$i]['image'] ?>" id="image-<?= $activity_result_array[$i]['exc_id'] ?>" />
          <div class="typeOverlay"></div>
          <div class="c-discount c-hide" id='discount<?= $activity_result_array[$i]['exc_id'] ?>'>
            <div class="discount-text">
              <span class="currency-icon"></span>
              <span class='offer-currency-price' id="offer-currency-price<?= $activity_result_array[$i]['exc_id'] ?>"></span>&nbsp;&nbsp;<span id='discount_text<?= $activity_result_array[$i]['exc_id'] ?>'></span>
              <span class='c-hide offer-currency-id' id="offer-currency-id<?= $activity_result_array[$i]['exc_id'] ?>"></span>
              <span class='c-hide offer-currency-flag' id="offer-currency-flag<?= $activity_result_array[$i]['exc_id'] ?>"></span>
            </div>
          </div>
        </div>
        <!-- *** Activity Card image End *** -->

        <!-- *** Activity Card Info *** -->
        <div class="cardList-info" role="button">
          <!--<button class="expandSect">View Details...</button>-->
          <div class="dividerSection type-1 noborder">
            <div class="divider s1">
              <h4 class="cardTitle" id="act_name-<?= $activity_result_array[$i]['exc_id'] ?>"><?php echo $activity_result_array[$i]['excursion_name'] ?></h4>

              <div class="infoSection">
                <span class="cardInfoLine cust">
                  <i class="icon itours-clock"></i>
                  Reporting Time: <strong id="rep_time-<?= $activity_result_array[$i]['exc_id'] ?>"><?php echo $activity_result_array[$i]['rep_time'] ?></strong>
                </span>
                <span class="cardInfoLine cust">
                  <i class="icon itours-hour-glass"></i>
                  Duration: <strong><?php echo $activity_result_array[$i]['duration'] ?></strong>
                </span>
              </div>

              <div class="infoSection">
                <span class="cardInfoLine cust">
                  <i class="icon itours-pin-alt"></i>
                  Pickup Point: <strong id="pick_point-<?= $activity_result_array[$i]['exc_id'] ?>"><?php echo $activity_result_array[$i]['departure_point'] ?></strong>
                </span>
              </div>

              <div class="infoSection">
                <span class="cardInfoLine cust">
                  <i class="icon itours-align-left"></i>
                  <span class="cardDescription">
                    <?php echo substr($activity_result_array[$i]['description'], 0, 250) . ' ...' ?>
                  </span>
                </span>
              </div>
            </div>

            <div class="divider s2">
              <div class="priceTag mb-1 mb-md-0">
                <?php if ($activity_result_array[$i]['best_lowest_cost']['cost'] != '' && $activity_result_array[$i]['best_org_cost']['org_cost'] != $activity_result_array[$i]['best_lowest_cost']['cost']) { ?>
                  <div class="p-old" id="p-old-div<?= $activity_result_array[$i]['exc_id'] ?>">
                    <span class="o_lbl">Old Price</span>
                    <span class="o_price">
                      <span class="p_currency currency-icon"></span>
                      <span class="p_cost best-activity-orgamount"><?= $activity_result_array[$i]['best_org_cost']['org_cost'] ?></span>
                      <span class="c-hide best-activity-orgcurrency-id"><?= $h_currency_id ?></span>
                    </span>
                  </div>
                <?php } ?>
                <div class="p-old mb-2 mb-md-0">
                  <span class="o_lbl">Total Price</span>
                  <span class="price_main">
                    <span class="p_currency currency-icon"></span>
                    <span class="p_cost best-activity-amount"><?= $activity_result_array[$i]['best_lowest_cost']['cost'] ?></span>
                    <span class="c-hide best-activity-currency-id"><?= $h_currency_id ?></span>
                  </span>
                  <small>(Excl of all taxes)</small>
                </div>
                <button class="expandSect" role="button" data-toggle="collapse" href="#collapseExample<?= $activity_result_array[$i]['exc_id'] ?>"
                  aria-expanded="false" aria-controls="collapseExample">View Details</button>

              </div>
            </div>
          </div>
        </div>
        <!-- *** Activity Card Info End *** -->
      </div>

      <!-- *** Activity Details Accordian *** -->
      <div class="collapse" id="collapseExample<?= $activity_result_array[$i]['exc_id'] ?>">
        <div class="cardList-accordian">
          <!-- ***** Activity Info Tabs ***** -->
          <div class="c-compTabs">
            <ul class="nav nav-pills" id="myTab" role="tablist">
              <li class="nav-item active">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $activity_result_array[$i]['exc_id'] ?>" role="tab"
                  aria-controls="home" aria-selected="true">Transfer Types</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="description-tab" data-toggle="tab" href="#description<?= $activity_result_array[$i]['exc_id'] ?>" role="tab"
                  aria-controls="description" aria-selected="true">Description</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="incl-tab" data-toggle="tab" href="#incl<?= $activity_result_array[$i]['exc_id'] ?>" role="tab"
                  aria-controls="incl" aria-selected="true">Inclusions & Exclusions</a>
              </li>
              <li class="nav-item">
                <a class="nav-link js-gallery" id="termsTab" data-toggle="tab" href="#terms<?= $activity_result_array[$i]['exc_id'] ?>"
                  role="tab" aria-controls="terms" aria-selected="true">Terms & Conditions</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="timing-tab" data-toggle="tab" href="#timing<?= $activity_result_array[$i]['exc_id'] ?>" role="tab"
                  aria-controls="timing" aria-selected="true">Timing Slots</a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link js-gallery"
                  id="galleryTab-<?= $activity_result_array[$i]['exc_id'] ?>"
                  data-toggle="tab"
                  href="#gallery-<?= $activity_result_array[$i]['exc_id'] ?>"
                  role="tab"
                  aria-controls="gallery"
                  aria-selected="true">Gallery</a>
              </li>
            </ul>

            <div class="tab-content" id="myTabContent">
              <!-- **** Tab Activity Listing **** -->
              <div class="tab-pane fade show active" id="home<?= $activity_result_array[$i]['exc_id'] ?>" role="tabpanel" aria-labelledby="home-tab">
                <?php
                $original_costs_array = array();
                $text = '';
                for ($ti = 0; $ti < sizeof($activity_result_array[$i]['transfer_options']); $ti++) {

                  $room_cost = $activity_result_array[$i]['transfer_options'][$ti]['total_cost'];
                  $org_cost = $activity_result_array[$i]['transfer_options'][$ti]['org_cost'];
                  $coupon_offer = 0;

                  if ($activity_result_array[$i]['transfer_options'][$ti]['offer_type'] != '') {

                    $offer_amount = $activity_result_array[$i]['transfer_options'][$ti]['offer_amount'];
                    if ($activity_result_array[$i]['transfer_options'][$ti]['offer_type'] == 'Offer') {

                      if ($activity_result_array[$i]['transfer_options'][$ti]['offer_in'] == 'Percentage') {
                        $text = '%';
                        $coupon_offer = ($room_cost * ($offer_amount / 100));
                      } else {
                        $sq_from = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$h_currency_id'"));
                        $from_currency_rate = $sq_from['currency_rate'];
                        $text = '';
                        if ($currency != $b2b_currency) {
                          $sq_to = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$b2b_currency'"));
                          $to_currency_rate = $sq_to['currency_rate'];
                          $coupon_offer = ($to_currency_rate != '') ? 1 / $from_currency_rate * $offer_amount : 0;
                        } else {
                          $sq_to = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$h_currency_id'"));
                          $to_currency_rate = $sq_to['currency_rate'];
                          $coupon_offer = ($to_currency_rate != '') ? 1 / $from_currency_rate * $offer_amount : 0;
                        }
                        $offer_amount = $coupon_offer;
                      }
                    } else if ($activity_result_array[$i]['transfer_options'][$ti]['offer_type'] == 'Coupon') {

                      if ($activity_result_array[$i]['transfer_options'][$ti]['offer_in'] == 'Percentage') {
                        $text = '%';
                      } else {
                        $text = '';
                        $sq_from = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$currency'"));
                        $from_currency_rate = $sq_from['currency_rate'];
                        $sq_to = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$b2b_currency'"));
                        $to_currency_rate = $sq_to['currency_rate'];
                        $coupon_offer = ($to_currency_rate != '') ? 1 / $from_currency_rate * $offer_amount : 0;
                        $offer_amount = $coupon_offer;
                      }
                    }
                    $offer_text = $text . ' ' . $activity_result_array[$i]['transfer_options'][$ti]['offer_type'];
                  } else {
                    $offer_text = '';
                  }
                  array_push($original_costs_array, $org_cost);
                  if ($activity_result_array[$i]['transfer_options'][$ti]['offer_type'] == 'Coupon' && $activity_result_array[$i]['transfer_options'][$ti]['coupon_code'] != "") {
                    $arr = array(
                      'offer_in' => $activity_result_array[$i]['transfer_options'][$ti]['offer_in'],
                      'offer_amount' => $offer_amount,
                      'coupon_code' => $activity_result_array[$i]['transfer_options'][$ti]['coupon_code'],
                      'agent_type' => $activity_result_array[$i]['transfer_options'][$ti]['agent_type'],
                      'currency_id' => $currency
                    );
                    array_push($coupon_info_arr, $arr);
                  }
                  $coupon_list_arr['coupon_info_arr'] = $coupon_info_arr;
                ?>
                  <script>
                    setTimeout(function() {
                      //Offer red strip display
                      if ('<?= $offer_text ?>' != '') {

                        document.getElementById("discount<?= $activity_result_array[$i]['exc_id'] ?>").classList.remove("c-hide");
                        document.getElementById("discount<?= $activity_result_array[$i]['exc_id'] ?>").classList.add("c-show");

                        document.getElementById("offer-currency-price<?= $activity_result_array[$i]['exc_id'] ?>").innerHTML = '<?= ($offer_amount) ?>';
                        document.getElementById("offer-currency-id<?= $activity_result_array[$i]['exc_id'] ?>").innerHTML = '<?= $currency ?>';
                        document.getElementById("offer-currency-flag<?= $activity_result_array[$i]['exc_id'] ?>").innerHTML = '<?= 'no' ?>';
                        document.getElementById("discount_text<?= $activity_result_array[$i]['exc_id'] ?>").innerHTML = '<?= $offer_text ?>';
                      } else {
                        document.getElementById("discount<?= $activity_result_array[$i]['exc_id'] ?>").classList.add("c-hide");
                      }

                      if ('<?= $activity_result_array[$i]['best_org_cost']['org_cost'] ?>' != '<?= $activity_result_array[$i]['best_lowest_cost']['cost'] ?>') {
                        if ('<?= $org_cost ?>' == '<?= $room_cost ?>') {
                          document.getElementById("p-old-div<?= $activity_result_array[$i]['exc_id'] ?>").classList.add("c-hide");
                        }
                      }
                    }, 50);
                  </script>
                  <!-- ***** Activity List Card ***** -->
                  <div class="c-cardListHolder">
                    <div class="c-cardListTable type-2" role="button">
                      <input class="btn-radio" type="radio" id="<?= $activity_result_array[$i]['exc_id'] . $ti ?>" name="result<?= $activity_result_array[$i]['exc_id'] ?>" value='<?php echo $activity_result_array[$i]['transfer_options'][$ti]['transfer_option'] . '-' . $room_cost . '-' . $h_currency_id ?>'>
                      <input type="hidden" id="coupon-<?= $activity_result_array[$i]['exc_id'] ?>" value='<?php echo json_encode($coupon_list_arr); ?>'>
                      <!-- *** Activity Card Info *** -->
                      <label class="cardList-info" for="<?= $activity_result_array[$i]['exc_id'] . $ti ?>" role="button">
                        <div class="flexGrid">
                          <div class="gridItem">
                            <div class="infoCard">
                              <span class="infoCard_data"><?php echo $activity_result_array[$i]['transfer_options'][$ti]['transfer_option'] ?></span>
                            </div>
                          </div>
                          <?php
                          if ($org_cost != 0 && $org_cost != $room_cost) { ?>
                            <div class="gridItem">
                              <div class="infoCard m0">
                                <div class="M-infoCard">
                                  <span class="infoCard_label">OLD PRICE</span>
                                  <div class="infoCard_price strike">
                                    <span class="p_currency currency-icon"></span>
                                    <span class="p_cost activity-orgcurrency-price"><?= $org_cost ?></span>
                                    <span class="c-hide activity-orgcurrency-id"><?= $h_currency_id ?></span>
                                  </div>
                                  <span class="infoCard_priceTax">(Excl of all taxes)</span>
                                </div>
                              </div>
                            </div>
                          <?php } ?>

                          <div class="gridItem">
                            <div class="infoCard m0">
                              <div class="M-infoCard">
                                <span class="infoCard_label">Total Price</span>
                                <div class="infoCard_price">
                                  <span class="p_currency currency-icon"></span>
                                  <span class="p_cost activity-currency-price"><?= $room_cost ?></span>
                                  <span class="c-hide activity-currency-id"><?= $h_currency_id ?></span>
                                </div>
                                <span class="infoCard_priceTax">(Excl of all taxes)</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </label>
                      <!-- *** Activity Card Info End *** -->
                    </div>
                  </div>
                  <!-- ***** Activity List Card End ***** -->
                <?php } ?>

              </div>
              <!-- **** Tab Activity Listing End **** -->
              <!-- **** Tab Description **** -->
              <div class="tab-pane fade" id="description<?= $activity_result_array[$i]['exc_id'] ?>" role="tabpanel" aria-labelledby="description-tab">
                <!-- **** Description **** -->
                <div class="clearfix margin20-bottom">

                  <h3 class="c-heading">
                    Description
                  </h3>
                  <p class="custom_texteditor">
                    <?= $activity_result_array[$i]['description']; ?>
                  </p>
                  <h3 class="c-heading">
                    Note
                  </h3>
                  <p class="custom_texteditor">
                    <?= $activity_result_array[$i]['note'] ?>
                  </p>
                </div>
                <!-- **** Description End **** -->
              </div>
              <!-- **** Tab Description End **** -->

              <!-- **** Tab Incl **** -->
              <div class="tab-pane fade" id="incl<?= $activity_result_array[$i]['exc_id'] ?>" role="tabpanel" aria-labelledby="incl-tab">
                <!-- **** Incl/Excl **** -->
                <div class="clearfix margin20-bottom">

                  <h3 class="c-heading">
                    Inclusions
                  </h3>
                  <div class="custom_texteditor">
                    <?= $activity_result_array[$i]['inclusions'] ?>
                  </div>
                  <h3 class="c-heading">
                    Exclusions
                  </h3>
                  <div class="custom_texteditor">
                    <?= $activity_result_array[$i]['exclusions'] ?>
                  </div>
                </div>
                <!-- **** Incl/Excl End **** -->
              </div>
              <!-- **** Tab Incl End **** -->

              <!-- **** Tab Terms **** -->
              <div class="tab-pane fade" id="terms<?= $activity_result_array[$i]['exc_id'] ?>" role="tabpanel" aria-labelledby="terms-tab">
                <!-- **** Terms **** -->
                <div class="clearfix margin20-bottom">

                  <h3 class="c-heading">
                    Terms & Conditions
                  </h3>
                  <div class="custom_texteditor">
                    <?= $activity_result_array[$i]['terms_condition'] ?>
                  </div> <!-- **** Policies **** -->
                  <?php
                  if ($activity_result_array[$i]['booking_policy'] != '') { ?>
                    <h3 class="c-heading">
                      Booking Policy
                    </h3>
                    <div class="custom_texteditor">
                      <?= $activity_result_array[$i]['booking_policy'] ?>
                    </div>
                  <?php } ?>
                  <?php
                  if ($activity_result_array[$i]['canc_policy'] != '') { ?>
                    <h3 class="c-heading">
                      Cancellation Policy
                    </h3>
                    <div class="custom_texteditor">
                      <?= $activity_result_array[$i]['canc_policy'] ?>
                    </div>
                  <?php } ?>
                  <!-- **** Policies End **** -->
                  <?php
                  if ($activity_result_array[$i]['useful_info'] != '') { ?>
                    <h3 class="c-heading">
                      Useful Information
                    </h3>
                    <div class="custom_texteditor">
                      <?= $activity_result_array[$i]['useful_info'] ?>
                    </div>
                  <?php } ?>
                </div>
                <!-- **** Terms End **** -->
              </div>
              <!-- **** Tab Terms End **** -->

              <!-- **** Tab Timing **** -->
              <div class="tab-pane fade" id="timing<?= $activity_result_array[$i]['exc_id'] ?>" role="tabpanel" aria-labelledby="timing-tab">
                <!-- **** Policies **** -->
                <div class="clearfix margin20-bottom">

                  <div class="custom_texteditor">
                    <?php
                    $timing_slots = ($activity_result_array[$i]['timing_slots'] != '' && $activity_result_array[$i]['timing_slots'] != 'null') ? json_decode($activity_result_array[$i]['timing_slots']) : [];
                    if (sizeof($timing_slots) == 0) {
                      echo 'NA';
                    }
                    for ($t = 0; $t < sizeof($timing_slots); $t++) {
                    ?>
                      <!-- *** timing slots Info *** -->
                      <div class="c-cardListHolder">
                        <div class="c-cardListTable type-3">

                          <div class="cardList-info">
                            <div class="flexGrid">
                              <div class="gridItem">
                                <div class="infoCard c-halfText m0">
                                  <span class="infoCard_label">SR.No</span>
                                  <span class="infoCard_price"><?= ($t + 1) ?></span>
                                </div>
                              </div>
                              <div class="gridItem">
                                <div class="infoCard c-halfText m0">
                                  <span class="infoCard_label">From Time</span>
                                  <span class="infoCard_price"><?= $timing_slots[$t]->from_time ?></span>
                                </div>
                              </div>

                              <div class="gridItem styleForMobile M-m0">
                                <div class="infoCard m5-btm">
                                  <span class="infoCard_label">To Time</span>
                                  <span class="infoCard_price"><?= $timing_slots[$t]->to_time ?></span>
                                </div>
                              </div>
                            </div>
                          </div>

                        </div>
                      </div>
                      <!-- *** timing slots End *** -->
                    <?php } ?>
                  </div>
                </div>
                <!-- **** Policies End **** -->
              </div>

              <!-- **** Tab Gallery **** -->
              <div class="tab-pane fade" id="gallery-<?= $activity_result_array[$i]['exc_id'] ?>" role="tabpanel" aria-labelledby="galleryTab-">
                <!-- **** photo List **** -->
                <div class="clearfix">
                  <div class="c-photoGallery js-dynamicLoad">
                    <div class="js-photoGallery owl-carousel">
                      <?php
                      $exc_id = $activity_result_array[$i]['exc_id'];
                      $sq_singleImage1 = mysqlQuery("select * from excursion_master_images where exc_id='$exc_id'");
                      while ($sq_singleImage = mysqli_fetch_assoc($sq_singleImage1)) {
                        if ($sq_singleImage['image_url'] != '') {
                          $image = $sq_singleImage['image_url'];
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
              <!-- **** Tab Gallery End **** -->
            </div>
            <div class="clearfix text-right">
              <button type="button" class="c-button md" id='<?= $activity_result_array[$i]['exc_id'] ?>' onclick='enq_to_action_page("4",this.id,<?= json_encode($act_enq_data) ?>)'><i class="fa fa-phone-square" aria-hidden="true"></i> Enquiry</button>

              <?php
              $booking_status = mysqli_fetch_assoc(mysqlQuery("select * from b2c_generic_settings where entry_id='2'"));

              if ($booking_status['answer'] == 'Yes') {


              ?>
                <button type="button" class="c-button g-button md md" id='<?= $activity_result_array[$i]['exc_id'] ?>' onclick='enq_to_action_page("4",this.id,<?= json_encode($act_enq_data) ?>)'><i class="fa fa-contact-book" aria-hidden="true"></i> Book</button>

              <?php } ?>
            </div>
            <!-- ***** Activity Info Tabs End***** -->
          </div>
        </div>
      </div>
      <!-- *** Activity Details Accordian End *** -->
      <input type="hidden" id="taxation-<?= $activity_result_array[$i]['exc_id'] ?>" value="<?php echo ($activity_result_array[$i]['taxation'][0]['taxation_type']) . '-' . ($activity_result_array[$i]['taxation'][0]['service_tax']) ?>" />
    </div>
    <!-- ***** Activity Card End ***** -->
<?php
  }
} //Activity arrays for loop
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

      var bestorg_price_list = document.querySelectorAll(".best-activity-orgamount");
      var bestorg_price_cid = document.querySelectorAll(".best-activity-orgcurrency-id");

      var act_price_list = document.querySelectorAll(".best-activity-amount");
      var trans_price_cid = document.querySelectorAll(".best-activity-currency-id");

      var amount_list = document.querySelectorAll(".activity-currency-price");
      var amount_id = document.querySelectorAll(".activity-currency-id");

      var original_amt_list = document.querySelectorAll(".activity-orgcurrency-price");
      var original_amt_id = document.querySelectorAll(".activity-orgcurrency-id");

      var offer_price_list = document.querySelectorAll(".offer-currency-price");
      var offer_price_id = document.querySelectorAll(".offer-currency-id");
      var offer_currency_flag = document.querySelectorAll(".offer-currency-flag");

      //Best low org cost prices
      var roomAmount_arr = [];
      for (var i = 0; i < bestorg_price_list.length; i++) {
        roomAmount_arr.push({
          'amount': bestorg_price_list[i].innerHTML,
          'id': bestorg_price_cid[i].innerHTML
        });
      }
      sessionStorage.setItem('bestorg_activity_price_list', JSON.stringify(roomAmount_arr));

      //Best low cost prices
      var roomAmount_arr = [];
      for (var i = 0; i < act_price_list.length; i++) {
        roomAmount_arr.push({
          'amount': act_price_list[i].innerHTML,
          'id': trans_price_cid[i].innerHTML
        });
      }
      sessionStorage.setItem('act_price_list', JSON.stringify(roomAmount_arr));

      //Activity Best Cost
      var amount_arr = [];
      for (var i = 0; i < amount_list.length; i++) {
        amount_arr.push({
          'amount': amount_list[i].innerHTML,
          'id': amount_id[i].innerHTML
        });
      }
      sessionStorage.setItem('activity_amount_list', JSON.stringify(amount_arr));

      //Activity Original Cost
      var orgAmount_arr = [];
      for (var i = 0; i < original_amt_list.length; i++) {
        orgAmount_arr.push({
          'amount': original_amt_list[i].innerHTML,
          'id': original_amt_id[i].innerHTML
        });
      }
      sessionStorage.setItem('orgactivity_amount_list', JSON.stringify(orgAmount_arr));

      //Activity Offer Cost
      var offerAmount_arr = [];
      for (var i = 0; i < offer_price_list.length; i++) {
        offerAmount_arr.push({
          'amount': offer_price_list[i].innerHTML,
          'id': offer_price_id[i].innerHTML,
          'flag': offer_currency_flag[i].innerHTML
        });
      }
      sessionStorage.setItem('activityoffer_price_list', JSON.stringify(offerAmount_arr));

      activties_page_currencies();
    }, 500);
  });
</script>
<script>
  setTimeout(() => {
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