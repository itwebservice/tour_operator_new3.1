<?php

include '../../config.php';

include BASE_URL . 'model/model.php';

include '../../layouts/header2.php';

$_SESSION['page_type'] = 'hotels-list';
global $currency;
$b2b_currency = $_SESSION['session_currency_id'];

//Object created to get room types result
include 'inc/get_final_rooms_result.php';
$get_result = new result_master;


$hotel_array = json_decode($_SESSION['hotel_array']);

$city_id = ($hotel_array[0]->city_id);

$hotel_id = ($hotel_array[0]->hotel_id);

$check_indate = $hotel_array[0]->check_indate;

$check_outdate = $hotel_array[0]->check_outdate;

$star_category_arr = $hotel_array[0]->star_category_arr;

$star_category_arr = ($star_category_arr != '') ? $star_category_arr : [];
$star_category_arr = implode(',', $star_category_arr);
$final_rooms_arr = (isset($hotel_array[0]->final_arr)) ? $hotel_array[0]->final_arr : [];
$final_rooms_arr = is_array($final_rooms_arr) ? $final_rooms_arr : json_decode($final_rooms_arr, true);
$dynamic_room_count = ($hotel_array[0]->dynamic_room_count == '') ? 1 : $hotel_array[0]->dynamic_room_count;
if ($city_id == '' && $hotel_id == '') {

  $query = "select * from hotel_master where 1 and active_flag='Active'";
  $check_indate = date("m/d/Y");
  $check_outdate = date("m/d/Y", strtotime("+1 day"));
}
//City Search

else if ($city_id != '') {

  $sq_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$city_id'"));

  $city_name = $sq_city['city_name'];

  $query = "select * from hotel_master where city_id='$city_id' and active_flag='Active'";
}
//Hotel Search

if ($hotel_id != '') {

  $sq_hotel = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$hotel_id' and active_flag='Active'"));

  $hotel_name = $sq_hotel['hotel_name'];

  $query = "select * from hotel_master where hotel_id='$hotel_id' and active_flag='Active'";
}

//Star Category filter

if ($star_category_arr != '') {

  $query .= " and rating_star in($star_category_arr) ";
}

//Page Title

if ($hotel_id != '') {

  $page_title = 'Results for ' . $hotel_name;
} else if ($city_id != '') {

  $page_title = 'Hotels in ' . $city_name;
} else {

  $page_title = 'All Hotels..';
}


$check_indate1 = date('d M Y', strtotime($check_indate));
$check_outdate1 = date('d M Y', strtotime($check_outdate));

$adults_count = 0;
$child_count = 0;
$chwb_count = 0;
$chwob_count = 0;
for ($n = 0; $n < sizeof($final_rooms_arr); $n++) {
  $adults_count = ($adults_count) + ($final_rooms_arr[$n]['rooms']['adults']);
  $child_count = ($child_count) + ($final_rooms_arr[$n]['rooms']['child']);
}
//Array of Check-in and Check-out date
$checkDate_array = array();
$check_in = strtotime($check_indate);
$check_out = strtotime($check_outdate);

for ($i_date = $check_in; $i_date <= $check_out; $i_date += 86400) {

  array_push($checkDate_array, date("Y-m-d", $i_date));
}
?>
<input type='hidden' id='check_indate' value='<?= $check_indate ?>' />
<input type='hidden' id='check_outdate' value='<?= $check_outdate ?>' />
<input type='hidden' id='rooms' value='<?= $dynamic_room_count ?>' />

<!-- ********** Component :: Page Title ********** -->

<div class="c-pageTitleSect">

  <div class="container">

    <div class="row">

      <div class="col-md-7 col-12">



        <!-- *** Search Head **** -->

        <div class="searchHeading">

          <span class="pageTitle"><?= $page_title ?></span>



          <div class="clearfix">



            <div class="sortSection">

              <span class="sortTitle st-search">

                <i class="icon it itours-timetable"></i>

                Check In: <strong><?= $check_indate1 ?></strong>

              </span>

            </div>



            <div class="sortSection">

              <span class="sortTitle st-search">

                <i class="icon it itours-timetable"></i>

                Check Out: <strong><?= $check_outdate1 ?></strong>

              </span>

            </div>



          </div>



          <div class="clearfix">





            <div class="sortSection">

              <span class="sortTitle st-search">

                <i class="icon it itours-search"></i>

                <span>Showing <span class="results_count"></span></span>

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

          <li>

            <a href="#">Hotel Search Result</a>

          </li>

        </ul>

      </div>



    </div>

  </div>

</div>

<!-- ********** Component :: Page Title End ********** -->



<!-- ********** Component :: Hotel Listing  ********** -->

<div class="c-containerDark">

  <div class="container">

    <!-- ********** Component :: Modify Filter  ********** -->

    <div class="row c-modifyFilter">

      <div class="col">

        <!-- Modified Search Filter -->

        <div class="accordion c-accordion ts-hotel-listing-accordion" id="modifySearch_filter">

          <div class="card">



            <div class="card-header" id="headingThree">

              <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#jsModifySearch_filter" aria-expanded="false" aria-controls="jsModifySearch_filter">

                Modify Search >><?php $room_word = (sizeof($final_rooms_arr) <= 1) ? ' Room' : ' Rooms'; ?><span class="results_count"></span><?php echo ' available for ' . ($adults_count + $child_count) . ' Pax ' . ' in ' . sizeof($final_rooms_arr) . $room_word; ?>

              </button>

            </div>

            <div id="jsModifySearch_filter" class="collapse" aria-labelledby="jsModifySearch_filter" data-parent="#modifySearch_filter">

              <div class="card-body">

                <form id="frm_hotel_search">

                  <input type='hidden' id='page_type' value='hotel_listing_page' name='hotel_listing_page' />

                  <div class="row">

                    <!-- *** City Name *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>Enter City</label>

                        <div class="c-select2DD">

                          <select id='hotel_city_filter' class="full-width js-roomCount" onchange="hotel_names_load(this.id);">

                            <?php if ($city_id != '') {

                              $sq_city_name = mysqli_fetch_assoc(mysqlQuery("select city_id, city_name from city_master where city_id='$city_id'")); ?>

                              <option value="<?php echo $sq_city_name['city_id'] ?>"><?php echo $sq_city_name['city_name'] ?></option>

                            <?php  } else {
                            ?>
                              <option value="">City Name</option>
                            <?php
                              $sq_city_name = mysqli_fetch_all(mysqlQuery("select city_id, city_name from city_master where 1"), MYSQLI_ASSOC);
                              foreach ($sq_city_name as $city) {
                                echo '<option value="' . $city['city_id'] . '">' . $city['city_name'] . '</option>';
                              }
                            } ?>

                          </select>

                        </div>

                      </div>

                    </div>

                    <!-- *** City Name End *** -->

                    <!-- *** Hotel Name *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>Enter Hotel Name</label>

                        <div class="c-select2DD">

                          <select id='hotel_name_filter' class="full-width js-roomCount">

                            <?php if ($hotel_id != '') {

                              $sq_filter = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$hotel_id'")); ?>

                              <option value="<?php echo $sq_filter['hotel_id'] ?>"><?php echo $sq_filter['hotel_name'] ?></option>

                              <option value="">Hotel Name</option>

                            <?php } else {

                              if ($city_id != '') {

                                $querys = "select hotel_id, hotel_name from hotel_master where city_id='$city_id' and active_flag='Active'";
                              } else {

                                $querys = "select hotel_id, hotel_name from hotel_master where 1 and active_flag='Active'";
                              } ?>

                              <option value="">Hotel Name</option>

                              <?php

                              $sq_hotels = mysqlQuery($querys);

                              while ($row_hotels = mysqli_fetch_assoc($sq_hotels)) { ?>

                                <option value="<?php echo $row_hotels['hotel_id'] ?>"><?php echo $row_hotels['hotel_name'] ?></option>

                            <?php }
                            }  ?>

                          </select>

                        </div>

                      </div>

                    </div>

                    <!-- *** Hotel Name End *** -->



                    <!-- *** Check in Date *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>*Check In</label>

                        <div class="datepicker-wrap">

                          <input type="text" name="date_from" class="input-text full-width" value="<?= $check_indate ?>" placeholder="mm/dd/yy" id="checkInDate" onchange='get_to_date("checkInDate","checkOutDate")' required />

                        </div>

                      </div>

                    </div>

                    <!-- *** Check in Date End *** -->



                    <!-- *** Check Out Date *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>*Check Out

                          <span class="nytCount" id='total_stay'></span>

                        </label>

                        <div class="datepicker-wrap">

                          <input type="text" name="date_from" class="input-text full-width" value="<?= $check_outdate ?>" placeholder="mm/dd/yy" id="checkOutDate" onchange='total_nights_reflect();' required />

                        </div>

                      </div>

                    </div>

                    <!-- *** Check Out Date End *** -->

                    <div class='block blue' id='display_addRooms_modal'></div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                      <div class="form-group">
                        <label>Total Rooms</label>
                        <div class="c-addRoom">
                          <a class="roomInfo js-roomCount" onclick='display_addRooms_modal()'>
                            <strong id='total_pax'><?php echo ($adults_count + $child_count) ?></strong> Person in
                            <strong id='room_count'><?php echo sizeof($final_rooms_arr) ?> <?= $room_word ?></strong>
                          </a>
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group clearfix">

                        <label>Hotel Star Catagory</label>

                        <div class="form-check form-check-inline c-checkGroup">

                          <?php $checked_status1 = (strpos($star_category_arr, "1 Star")) ? 'checked' : ''; ?>

                          <input class="form-check-input" type="checkbox" id="c1" value="1" name='star_category' <?= $checked_status1 ?>>

                          <label class="form-check-label" role="button" for="c1">

                            1 <i class="icon-star"></i>

                          </label>

                        </div>

                        <div class="form-check form-check-inline c-checkGroup">

                          <?php

                          $checked_status2 = (strpos($star_category_arr, "2 Star")) ? 'checked' : ''; ?>

                          <input class="form-check-input" type="checkbox" id="c2" value="2" name='star_category' <?= $checked_status2 ?>>

                          <label class="form-check-label" role="button" for="c2">

                            2 <i class="icon-star"></i>

                          </label>

                        </div>

                        <div class="form-check form-check-inline c-checkGroup">

                          <?php

                          $checked_status3 = (strpos($star_category_arr, "3 Star")) ? 'checked' : ''; ?>

                          <input class="form-check-input" type="checkbox" id="c3" value="3" name='star_category' <?= $checked_status3 ?>>

                          <label class="form-check-label" role="button" for="c3">

                            3<i class="icon-star"></i>

                          </label>

                        </div>

                        <div class="form-check form-check-inline c-checkGroup">

                          <?php

                          $checked_status4 = (strpos($star_category_arr, "4 Star")) ? 'checked' : ''; ?>

                          <input class="form-check-input" type="checkbox" id="c4" value="4" name='star_category' <?= $checked_status4 ?>>

                          <label class="form-check-label" role="button" for="c4">

                            4 <i class="icon-star"></i>

                          </label>

                        </div>

                        <div class="form-check form-check-inline c-checkGroup">

                          <?php

                          $checked_status5 = (strpos($star_category_arr, "5 Star")) ? 'checked' : ''; ?>

                          <input class="form-check-input" type="checkbox" id="c5" value="5" name='star_category' <?= $checked_status5 ?>>

                          <label class="form-check-label" role="button" for="c5">

                            5<i class="icon-star"></i>

                          </label>

                        </div>

                      </div>

                    </div>



                    <!-- *** Search Rooms *** -->
                    <input type='hidden' id='adult_count' name='adult_count' />
                    <input type='hidden' id='child_count' name='child_count' />
                    <input type='hidden' value='<?= $dynamic_room_count ?>' id='dynamic_room_count' name='dynamic_room_count' />

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <button class="c-button lg colGrn m26-top">

                        <i class="icon itours-search"></i> SEARCH NOW

                      </button>

                    </div>

                    <!-- *** Search Rooms End *** -->

                  </div>

                </form>

              </div>

            </div>

          </div>

        </div>

        <!-- Modified Search Filter End -->

      </div>

    </div>

    <div class="row">
      <div class="col-md-3 col-sm-12">

        <!-- ***** Price Filter ***** -->

        <div class="accordion c-accordion filterPriceSidebar" id="filterPrice">

          <div class="filterItem">
            <div class="heading">
              <h5 class="filterTitle">
                Sort Hotels By
              </h5>

            </div>
            <div class="dropdown selectable">

              <select id="price_filter_id" name="price_filter_id" title="Sort Hotels By Price" class="form-control" style="width:100%" onchange="get_price_filter_data('hotel_results_array',this.id,0,0);">
                <option value="2">Price- Low to High</option>
                <option value="1">Price- High to Low</option>
              </select>
            </div>
          </div>
          <hr />
          <div class="filterItem">
            <div class="heading">
              <h5 class="filterTitle">
                Price Range
              </h5>

            </div>
            <div id="jsFilterPrice">
              <div class="c-priceRange">

                <input type="hidden" class="slider-input" data-step="1" />

              </div>

            </div>
          </div>
          <hr />
          <div class="filterItem">
            <div class="heading">
              <h5 class="filterTitle">
                Property Category
              </h5>

            </div>
            <div class="checkboxLists" id="hotel_categories">
            </div>
          </div>
          <hr />
          <div class="filterItem">
            <div class="heading">
              <h5 class="filterTitle">
                Property Type
              </h5>

            </div>
            <div class="checkboxLists" id="hotel_types">
            </div>
          </div>
          <hr />
          <div class="filterItem">
            <div class="heading">
              <h5 class="filterTitle">
                Property Amenities
              </h5>

            </div>
            <div class="checkboxLists" id="hotel_amenities">
            </div>
          </div>
          <hr />

        </div>

        <!-- ***** Price Filter End ***** -->

      </div>


      <!-- ***** Hotel Listing ***** -->

      <div class="col-md-9 col-sm-12">

        <?php
        $actual_ccosts_array = array();
        $hotel_results_array = array();
        $min_array = array();
        $max_array = array();
        $hotel_category_array = array();
        $hotel_type_array = array();
        $hotel_amenity_array = array();

        $sq_query = mysqlQuery($query);
        while (($row_query  = mysqli_fetch_assoc($sq_query))) {

          //Single Hotel Image

          $sq_singleImage = mysqli_fetch_assoc(mysqlQuery("select * from hotel_vendor_images_entries where hotel_id='$row_query[hotel_id]'"));

          if ($sq_singleImage['hotel_pic_url'] != '') {

            $image = $sq_singleImage['hotel_pic_url'];

            $newUrl1 = preg_replace('/(\/+)/', '/', $image);

            $newUrl1 = explode('uploads', $newUrl1);

            $newUrl = BASE_URL . 'uploads' . $newUrl1[1];
          } else {

            $newUrl = BASE_URL_B2C . 'images/hotel_general.png';
          }



          $sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$row_query[city_id]'"));

          //Category

          $star_category = explode(' ', $row_query['rating_star']);

          $star_category = (sizeof($star_category) > 1) ? $star_category[0] : '';

          //Amenities

          $amenity = explode(',', $row_query['amenities']);
          $cost_arr = array();
          //Fetch Hotel Tariff for Room Categories(Room Types)
          $sq_tariff_master = mysqlQuery("select * from hotel_vendor_price_master where 1 and hotel_id='$row_query[hotel_id]'");
          while ($row_tariff_master = mysqli_fetch_assoc($sq_tariff_master)) {
            $currency_id = $row_tariff_master['currency_id'];
            for ($i_date = 0; $i_date < sizeof($checkDate_array) - 1; $i_date++) {

              $day = date("l", strtotime($checkDate_array[$i_date]));
              $blackdated_count = mysqli_num_rows(mysqlQuery("select * from hotel_blackdated_tarrif where pricing_id='$row_tariff_master[pricing_id]' and (from_date <='$checkDate_array[$i_date]' and to_date>='$checkDate_array[$i_date]')"));
              $weekenddated_count = mysqli_num_rows(mysqlQuery("select * from hotel_weekend_tarrif where pricing_id='$row_tariff_master[pricing_id]' and day='$day'"));
              $contracted_count = mysqli_num_rows(mysqlQuery("select * from hotel_contracted_tarrif where pricing_id='$row_tariff_master[pricing_id]' and (from_date <='$checkDate_array[$i_date]' and to_date>='$checkDate_array[$i_date]')"));
              //################### Black-dated rates ########################//
              if ($blackdated_count > 0) {

                $sq_tariff = mysqlQuery("select * from hotel_blackdated_tarrif where pricing_id='$row_tariff_master[pricing_id]' and (from_date <='$checkDate_array[$i_date]' and to_date>='$checkDate_array[$i_date]') ");
                while ($row_tariff = mysqli_fetch_assoc($sq_tariff)) {

                  $chwb_count = 0;
                  $chwob_count = 0;
                  for ($i_room = 0; $i_room < sizeof($final_rooms_arr); $i_room++) {   //No.of rooms loop
                    $roomChild_arr = array();
                    $total_pax = $final_rooms_arr[$i_room]['rooms']['adults'] + $final_rooms_arr[$i_room]['rooms']['child'];
                    if ($row_tariff['max_occupancy'] >= $total_pax) {

                      //For Extra bed for more than 2 adults
                      $extra_bed_cost = ($final_rooms_arr[$i_room]['rooms']['adults'] > 2) ? $row_tariff['extra_bed'] : '0';
                      //Child Age-wise costing
                      for ($k = 0; $k < sizeof($final_rooms_arr[$i_room]['rooms']); $k++) {
                        if ($final_rooms_arr[$i_room]['rooms']['childAge'][$k] >= $row_query['cwb_from'] && $final_rooms_arr[$i_room]['rooms']['childAge'][$k] <= $row_query['cwb_to']) {
                          array_push($roomChild_arr, (float)($row_tariff['child_with_bed']));
                          $chwb_count++;
                        } elseif ($final_rooms_arr[$i_room]['rooms']['childAge'][$k] >= $row_query['cwob_from'] && $final_rooms_arr[$i_room]['rooms']['childAge'][$k] <= $row_query['cwob_to']) {
                          array_push($roomChild_arr, (float)($row_tariff['child_without_bed']));
                          $chwob_count++;
                        } else {
                          array_push($roomChild_arr, (float)(0));
                        }
                      }
                      if ($row_tariff['markup_per'] != '0') {
                        $markup_type = 'Percentage';
                        $markup_amount = $row_tariff['markup_per'];
                      } else {
                        $markup_type = 'flat';
                        $markup_amount = $row_tariff['markup'];
                      }
                      //Checking discount applied or not
                      $sq_offers_count = mysqli_num_rows(mysqlQuery("select * from hotel_offers_tarrif where (from_date <='$checkDate_array[$i_date]' and to_date>='$checkDate_array[$i_date]' and hotel_id='$row_query[hotel_id]')"));
                      if ($sq_offers_count > 0) {
                        $row_offers = mysqli_fetch_assoc(mysqlQuery("select * from hotel_offers_tarrif where (from_date <='$checkDate_array[$i_date]' and to_date>='$checkDate_array[$i_date]' and hotel_id='$row_query[hotel_id]')"));
                        $offer_type = $row_offers['type'];
                        $offer_in = $row_offers['offer'];
                        $offer_amount = $row_offers['offer_amount'];
                        $coupon_code = $row_offers['coupon_code'];
                        $agent_type = $row_offers['agent_type'];
                      } else {
                        $offer_type = '';
                        $offer_in = '';
                        $offer_amount = 0;
                        $coupon_code = '';
                        $agent_type = '';
                      }

                      $cost_arr1 = array(
                        'rooms' => array(
                          "room_count" => $final_rooms_arr[$i_room]['rooms']['room'],
                          "check_date" => $checkDate_array[$i_date],
                          "category" => $row_tariff['room_category'],
                          "meal_plan" => $row_tariff['meal_plan'],
                          "room_cost" => (float)($row_tariff['double_bed']),
                          "child_cost" => $roomChild_arr,
                          "extra_bed_cost" => (float)($extra_bed_cost),
                          "max_occupancy" => $row_tariff['max_occupancy'],
                          "markup_type" => $markup_type,
                          "markup_amount" => (float)($markup_amount),
                          "offer_type" => $offer_type,
                          "offer_amount" => (float)($offer_amount),
                          "offer_in" => $offer_in,
                          "coupon_code" => $coupon_code,
                          "agent_type" => $agent_type,
                          "currency_id" => $currency_id
                        )
                      );
                      array_push($cost_arr, $cost_arr1);
                    }
                  }
                } // Rates while loop End
              } //Black dated rates If loop End
              //################# Black-dated rates End #####################//

              //################### Weekend-dated rates ########################//
              elseif ($weekenddated_count > 0) {

                $sq_tariff = mysqlQuery("select * from hotel_weekend_tarrif where pricing_id='$row_tariff_master[pricing_id]' and day='$day'");
                while ($row_tariff = mysqli_fetch_assoc($sq_tariff)) {

                  $chwb_count = 0;
                  $chwob_count = 0;
                  for ($i_room = 0; $i_room < sizeof($final_rooms_arr); $i_room++) { //No.of rooms loop

                    $roomChild_arr = array();
                    $total_pax = $final_rooms_arr[$i_room]['rooms']['adults'] + $final_rooms_arr[$i_room]['rooms']['child'];
                    if ($row_tariff['max_occupancy'] >= $total_pax) {

                      //For Extra bed for more than 2 adults
                      $extra_bed_cost = ($final_rooms_arr[$i_room]['rooms']['adults'] > 2) ? $row_tariff['extra_bed'] : '0';

                      //Child Age-wise costing
                      for ($k = 0; $k < sizeof($final_rooms_arr[$i_room]['rooms']); $k++) {
                        if ($final_rooms_arr[$i_room]['rooms']['childAge'][$k] >= $row_query['cwb_from'] && $final_rooms_arr[$i_room]['rooms']['childAge'][$k] <= $row_query['cwb_to']) {
                          array_push($roomChild_arr, (float)($row_tariff['child_with_bed']));
                          $chwb_count++;
                        } elseif ($final_rooms_arr[$i_room]['rooms']['childAge'][$k] >= $row_query['cwob_from'] && $final_rooms_arr[$i_room]['rooms']['childAge'][$k] <= $row_query['cwob_to']) {
                          array_push($roomChild_arr, (float)($row_tariff['child_without_bed']));
                          $chwob_count++;
                        } else {
                          array_push($roomChild_arr, (float)(0));
                        }
                      }
                      if ($row_tariff['markup_per'] != 0) {
                        $markup_type = 'Percentage';
                        $markup_amount = $row_tariff['markup_per'];
                      } else {
                        $markup_type = 'flat';
                        $markup_amount = $row_tariff['markup'];
                      }
                      //Checking discount applied or not
                      $sq_offers_count = mysqli_num_rows(mysqlQuery("select * from hotel_offers_tarrif where (from_date <='$checkDate_array[$i_date]' and to_date>='$checkDate_array[$i_date]') and hotel_id='$row_query[hotel_id]'"));
                      if ($sq_offers_count > 0) {
                        $row_offers = mysqli_fetch_assoc(mysqlQuery("select * from hotel_offers_tarrif where (from_date <='$checkDate_array[$i_date]' and to_date>='$checkDate_array[$i_date]') and hotel_id='$row_query[hotel_id]'"));
                        $offer_type = $row_offers['type'];
                        $offer_in = $row_offers['offer'];
                        $offer_amount = $row_offers['offer_amount'];
                        $coupon_code = $row_offers['coupon_code'];
                        $agent_type = $row_offers['agent_type'];
                      } else {
                        $offer_type = '';
                        $offer_in = '';
                        $offer_amount = 0;
                        $coupon_code = '';
                        $agent_type = '';
                      }
                      $cost_arr1 = array(
                        'rooms' => array(
                          "room_count" => $final_rooms_arr[$i_room]['rooms']['room'],
                          "check_date" => $checkDate_array[$i_date],
                          "category" => $row_tariff['room_category'],
                          "meal_plan" => $row_tariff['meal_plan'],
                          "room_cost" => (float)($row_tariff['double_bed']),
                          "child_cost" => $roomChild_arr,
                          "extra_bed_cost" => (float)($extra_bed_cost),
                          "max_occupancy" => $row_tariff['max_occupancy'],
                          "markup_type" => $markup_type,
                          "markup_amount" => (float)($markup_amount),
                          "offer_type" => $offer_type,
                          "offer_amount" => (float)($offer_amount),
                          "offer_in" => $offer_in,
                          "coupon_code" => $coupon_code,
                          "agent_type" => $agent_type,
                          "currency_id" => $currency_id
                        )
                      );
                      array_push($cost_arr, $cost_arr1);
                    }
                  }
                } // Rates while loop End
              } //Weekend dated rates If loop End
              //################# Weekend-dated rates End #####################//

              //################### Contracted-dated rates ########################//
              elseif ($contracted_count > 0) {
                $sq_tariff = mysqlQuery("select * from hotel_contracted_tarrif where pricing_id='$row_tariff_master[pricing_id]' and (from_date <='$checkDate_array[$i_date]' and to_date>='$checkDate_array[$i_date]') ");
                while ($row_tariff = mysqli_fetch_assoc($sq_tariff)) {

                  $chwb_count = 0;
                  $chwob_count = 0;
                  for ($i_room = 0; $i_room < sizeof($final_rooms_arr); $i_room++) { //No.of rooms loop
                    $roomChild_arr = array();
                    $total_pax = $final_rooms_arr[$i_room]['rooms']['adults'] + $final_rooms_arr[$i_room]['rooms']['child'];
                    if ($row_tariff['max_occupancy'] >= $total_pax) {

                      //For Extra bed for more than 2 adults
                      $extra_bed_cost = ($final_rooms_arr[$i_room]['rooms']['adults'] > 2) ? $row_tariff['extra_bed'] : '0';

                      //Child Age-wise costing
                      for ($k = 0; $k < sizeof($final_rooms_arr[$i_room]['rooms']); $k++) {
                        if (isset($final_rooms_arr[$i_room]['rooms']['childAge'][$k]) && $final_rooms_arr[$i_room]['rooms']['childAge'][$k] >= $row_query['cwb_from'] && $final_rooms_arr[$i_room]['rooms']['childAge'][$k] <= $row_query['cwb_to']) {
                          array_push($roomChild_arr, (float)($row_tariff['child_with_bed']));
                          $chwb_count++;
                        } elseif (isset($final_rooms_arr[$i_room]['rooms']['childAge'][$k]) && $final_rooms_arr[$i_room]['rooms']['childAge'][$k] >= $row_query['cwob_from'] && $final_rooms_arr[$i_room]['rooms']['childAge'][$k] <= $row_query['cwob_to']) {
                          array_push($roomChild_arr, (float)($row_tariff['child_without_bed']));
                          $chwob_count++;
                        } else {
                          array_push($roomChild_arr, (float)(0));
                        }
                      }
                      if ($row_tariff['markup_per'] != 0) {
                        $markup_type = 'Percentage';
                        $markup_amount = $row_tariff['markup_per'];
                      } else {
                        $markup_type = 'flat';
                        $markup_amount = $row_tariff['markup'];
                      }
                      //Checking discount applied or not
                      $sq_offers_count = mysqli_num_rows(mysqlQuery("select * from hotel_offers_tarrif where (from_date <='$checkDate_array[$i_date]' and to_date>='$checkDate_array[$i_date]') and hotel_id='$row_query[hotel_id]'"));
                      if ($sq_offers_count > 0) {
                        $row_offers = mysqli_fetch_assoc(mysqlQuery("select * from hotel_offers_tarrif where (from_date <='$checkDate_array[$i_date]' and to_date>='$checkDate_array[$i_date]') and hotel_id='$row_query[hotel_id]'"));
                        $offer_type = $row_offers['type'];
                        $offer_in = $row_offers['offer'];
                        $offer_amount = $row_offers['offer_amount'];
                        $coupon_code = $row_offers['coupon_code'];
                        $agent_type = $row_offers['agent_type'];
                      } else {
                        $offer_type = '';
                        $offer_in = '';
                        $offer_amount = 0;
                        $coupon_code = '';
                        $agent_type = '';
                      }

                      $cost_arr1 = array(
                        'rooms' => array(
                          "room_count" => $final_rooms_arr[$i_room]['rooms']['room'],
                          "check_date" => $checkDate_array[$i_date],
                          "category" => $row_tariff['room_category'],
                          "meal_plan" => $row_tariff['meal_plan'],
                          "room_cost" => (float)($row_tariff['double_bed']),
                          "child_cost" => $roomChild_arr,
                          "extra_bed_cost" => (float)($extra_bed_cost),
                          "max_occupancy" => $row_tariff['max_occupancy'],
                          "markup_type" => $markup_type,
                          "markup_amount" => (float)($markup_amount),
                          "offer_type" => $offer_type,
                          "offer_amount" => (float)($offer_amount),
                          "offer_in" => $offer_in,
                          "coupon_code" => $coupon_code,
                          "agent_type" => $agent_type,
                          "currency_id" => $currency_id
                        )
                      );
                      array_push($cost_arr, $cost_arr1);
                    }
                  }
                } // Rates while loop End
              } //Contracted dated rates If loop End
              //################# Contracted-dated rates End #####################//
            } //checkin Datewise for loop End
          }  //Hotel Master Rate while loop End
          //Method Call to get room types result
          $final_room_type_array = [];
          $final_room_type_array = $get_result->get_result_array($cost_arr, sizeof($checkDate_array));
          //Get selected currency rate
          $sq_to = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$b2b_currency'"));
          $to_currency_rate = $sq_to['currency_rate'];
          $all_costs_array = array();
          $extra_bed_count = 0;
          if (sizeof($final_room_type_array) > 0) {

            $last_date = sizeof($checkDate_array) - 2;
            //Main for loop
            for ($i = 0; $i < sizeof($final_room_type_array); $i++) {

              $room_cost = 0;
              $room_cost_array = ($final_room_type_array[$i]['room_cost']);
              $child_cost_array = ($final_room_type_array[$i]['child_cost']);
              $daywise_exbcost_array = ($final_room_type_array[$i]['daywise_exbcost']);
              $markup_type_array = ($final_room_type_array[$i]['markup_type']);
              $markup_amount_array = ($final_room_type_array[$i]['markup_amount']);
              // Roomcost For loop
              $room_cost_temp = 0;
              $child_cost_temp = 0;
              $exbed_cost_temp = 0;
              $markup = 0;
              for ($m = 0; $m < sizeof($room_cost_array); $m++) {

                if ((int)($daywise_exbcost_array[$m]) !== 0) {
                  $extra_bed_count++;
                }
                $room_cost_temp = $room_cost_array[$m] + $child_cost_array[$m] + $daywise_exbcost_array[$m];
                if ($markup_type_array[$m] == 'Percentage') {
                  $markup = ($room_cost_temp * ($markup_amount_array[$m] / 100));
                } else {
                  $markup = (float)($markup_amount_array[$m]);
                }
                $room_cost = $room_cost + $room_cost_temp + $markup;
              }
              $room_cost = ceil($room_cost);
              $h_currency_id = $final_room_type_array[$i]['currency_id'];
              $sq_from = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$h_currency_id'"));
              $from_currency_rate = $sq_from['currency_rate'];
              $c_amount = ($to_currency_rate != '') ? 1 / $from_currency_rate * $room_cost : 0;

              //Offers
              if ($final_room_type_array[$i]['offer_type'] != '') {

                if ($final_room_type_array[$i]['offer_type'] == 'Offer') {

                  $offer_amount = $final_room_type_array[$i]['offer_amount'];
                  $coupon_offer = 0;
                  if ($final_room_type_array[$i]['offer_in'] == 'Percentage') {
                    $coupon_offer = ($c_amount * ($offer_amount / 100));
                  } else {
                    if ($currency != $b2b_currency) {

                      $sq_to = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$b2b_currency'"));
                      $to_currency_rate = $sq_to['currency_rate'];
                      $coupon_offer = ($to_currency_rate != '') ? 1 / $from_currency_rate  * $offer_amount : 0;
                    } else {
                      $sq_to = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$h_currency_id'"));
                      $to_currency_rate = $sq_to['currency_rate'];
                      $coupon_offer = ($to_currency_rate != '') ? 1 / $from_currency_rate * $offer_amount : 0;
                    }
                  }
                  $c_amount = $c_amount - $coupon_offer;
                }
              }
              //Final cost push into array
              $c_amount = ceil($c_amount);
              array_push($actual_ccosts_array, $c_amount);
              array_push($all_costs_array, array('amount' => $c_amount, 'id' => $h_currency_id));
            }
            $prices = (sizeof($all_costs_array)) ? $array_master->array_column($all_costs_array, 'amount') : [];
            $min_array = (sizeof($prices)) ? $all_costs_array[array_search(min($prices), $prices)] : [];

            $hotel_result_array = array(

              "hotel_id" => $row_query['hotel_id'],

              "hotel_name" => $row_query['hotel_name'],

              "city_name" => $sq_city['city_name'],

              "hotel_image" => $newUrl,

              "star_category" => $star_category,
              "adult_count" => $adults_count,
              "chwb_count" => $chwb_count,
              "chwob_count" => $chwob_count,
              "extra_bed_count" => $extra_bed_count,
              "total_rooms" => sizeof($final_rooms_arr),

              "hotel_address" => addslashes($row_query['hotel_address']),

              "hotel_type" => $row_query['hotel_type'],

              "meal_plan" => $row_query['meal_plan'],

              "amenity" => $amenity,

              "checkDate_array" => $checkDate_array,
              "final_room_type_array" => $final_room_type_array,

              "description" => addslashes($row_query['description']),

              "policies" => addslashes($row_query['policies']),

              "check_in" => $check_indate,

              "check_out" => $check_outdate,

              "best_lowest_cost" => array(
                'id' => $min_array['id'],
                'cost' => $min_array['amount']
              )

            );

            array_push($hotel_results_array, $hotel_result_array);
          }
          for ($i = 0; $i < sizeof($hotel_results_array); $i++) {

            if (!empty($star_category) && !in_array($star_category, $hotel_category_array)) {
              array_push($hotel_category_array, $star_category);
            }
            if (!empty($hotel_results_array[$i]['hotel_type']) && !in_array($hotel_results_array[$i]['hotel_type'], $hotel_type_array)) {
              array_push($hotel_type_array, $hotel_results_array[$i]['hotel_type']);
            }
          }
          for ($i = 0; $i < sizeof($amenity); $i++) {
            if (!empty($amenity[$i]) && !in_array($amenity[$i], $hotel_amenity_array)) {
              array_push($hotel_amenity_array, $amenity[$i]);
            }
          }
        }
        sort($hotel_category_array);
        sort($hotel_type_array);
        sort($hotel_amenity_array);
        $min_array = (sizeof($all_costs_array)) ? $all_costs_array[array_search(min($prices), $prices)] : [];
        $max_array = (sizeof($all_costs_array)) ? $all_costs_array[array_search(max($prices), $prices)] : [];
        $actual_ccosts_array = ($actual_ccosts_array != '') ? $actual_ccosts_array : [];
        $min_value = (sizeof($actual_ccosts_array) != 0) ? min($actual_ccosts_array) : 0;
        $max_value = (sizeof($actual_ccosts_array) != 0) ? max($actual_ccosts_array) : 0;
        $hotel_results_array = ($hotel_results_array != '') ? $hotel_results_array : [];
        ?>

        <input type='hidden' value='<?= json_encode($hotel_results_array, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>' id='hotel_results_array' name='hotel_results_array' />

        <input type='hidden' class='best-cost-currency' id='bestlow_cost' value='<?= ($min_value != '') ? $min_value : 0 ?>' />
        <input type='hidden' class='best-cost-currency' id='besthigh_cost' value='<?= ($max_value != '') ? $max_value : 0 ?>' />
        <input type='hidden' class='best-cost-id' id='bestlow_cost_id' value='<?= ($min_array['id'] != '') ? $min_array['id'] : '' ?>' />
        <input type='hidden' class='best-cost-id' id='besthigh_cost_id' value='<?= ($max_array['id'] != '') ? $max_array['id'] : ''  ?>' />
        <input type="hidden" id='price_rangevalues' />
        <input type="hidden" value='<?= json_encode($hotel_category_array, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>' id="hotel_category_array" />
        <input type="hidden" id="selected_hotel_category_array" />
        <input type="hidden" value='<?= json_encode($hotel_type_array, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>' id="hotel_type_array" />
        <input type="hidden" id="selected_hotel_type_array" />
        <input type="hidden" value='<?= json_encode($hotel_amenity_array, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>' id="hotel_amenity_array" />
        <input type="hidden" id="selected_hotel_amenity_array" />

        <div id='hotel_result_block'></div>

      </div>

    </div>

  </div>

</div>

<!-- ********** Component :: Hotel Listing End ********** -->

<?php include '../../layouts/footer2.php'; ?>
<script type="text/javascript" src="js/index.js"></script>
<script type="text/javascript" src="js/amenities.js"></script>
<script type="text/javascript" src="../../js2/scripts.js"></script>
<script type="text/javascript" src="../../js2/jquery.range.min.js"></script>
<script type="text/javascript" src="../../js2/pagination.min.js"></script>

<script>
  $('#checkInDate, #checkOutDate').datetimepicker({
    timepicker: false,
    format: 'm/d/Y',
    minDate: new Date()
  });
  city_lzloading('#hotel_city_filter');
  total_nights_reflect();
  $(document).ready(function() {
    $('body').delegate('.lblhtfilterChk', 'click', function() {
      get_price_filter_data('hotel_results_array', 'price_filter_id', 0, 0);
    })
    $('body').delegate('.lblhcfilterChk', 'click', function() {
      get_price_filter_data('hotel_results_array', 'price_filter_id', 0, 0);
    })
    $('body').delegate('.lblpafilterChk', 'click', function() {
      get_price_filter_data('hotel_results_array', 'price_filter_id', 0, 0);
    })
  });

  // Get Hotel results data

  function get_price_filter_data(hotel_results_array, type1, fromRange_cost, toRange_cost, flag = true) {
    var base_url = $('#base_url').val();

    var type = $('#' + type1).val();
    setTimeout(() => {
      var selected_value = document.getElementById(hotel_results_array).value;
      var JSONItems = JSON.parse(selected_value);
      var final_arr = [];

      if (typeof Storage !== 'undefined') {
        if (localStorage) {
          var currency_id = localStorage.getItem('global_currency');
        } else {
          var currency_id = window.sessionStorage.getItem('global_currency');
        }
      }
      if (type == 1) {
        final_arr = (JSONItems).sort(function(a, b) {

          if (a.best_lowest_cost.id == null) {

            a.best_lowest_cost.id = '0';
            a.best_lowest_cost.cost = 0;

          }

          if (b.best_lowest_cost.id == null) {

            b.best_lowest_cost.id = '0';
            b.best_lowest_cost.cost = 0;

          }

          var currency_rates = get_currency_rates(a.best_lowest_cost.id, currency_id).split('-');

          var to_currency_rate = currency_rates[0];

          var from_currency_rate = currency_rates[1];

          var aamount = parseFloat(from_currency_rate * a.best_lowest_cost.cost).toFixed(2);

          //Second value      

          var currency_rates = get_currency_rates(b.best_lowest_cost.id, currency_id).split('-');

          var to_currency_rate = currency_rates[0];

          var from_currency_rate = currency_rates[1];

          var bamount = parseFloat(from_currency_rate * b.best_lowest_cost.cost).toFixed(2);

          return bamount - aamount;
        });


      } else if (type == 2) {

        final_arr = (JSONItems).sort(function(a, b) {

          if (a.best_lowest_cost.id == null) {

            a.best_lowest_cost.id = '0';
            a.best_lowest_cost.cost = 0;

          }

          if (b.best_lowest_cost.id == null) {

            b.best_lowest_cost.id = '0';
            b.best_lowest_cost.cost = 0;

          }

          var currency_rates = get_currency_rates(a.best_lowest_cost.id, currency_id).split('-');

          var to_currency_rate = currency_rates[0];

          var from_currency_rate = currency_rates[1];

          var aamount = parseFloat(from_currency_rate * a.best_lowest_cost.cost).toFixed(2);

          //Second value      

          var currency_rates = get_currency_rates(b.best_lowest_cost.id, currency_id).split('-');

          var to_currency_rate = currency_rates[0];

          var from_currency_rate = currency_rates[1];

          var bamount = parseFloat(from_currency_rate * b.best_lowest_cost.cost).toFixed(2);
          return aamount - bamount;

        });

      }

      //hotel category  filter
      var final_arr1 = [];
      var hotel_category_array = [];
      var checkboxes = document.getElementsByName('hotel_category');
      for (var checkbox of checkboxes) {
        if (checkbox.checked)
          hotel_category_array.push(checkbox.value);
      }
      if (hotel_category_array.length != 0) {
        final_arr1 = (final_arr).filter(function(a) {
          return hotel_category_array.includes(a.star_category);
        });
      } else {
        final_arr1 = final_arr;
      }
      $('#selected_hotel_category_array').val(hotel_category_array);
      //hotel type filter
      var final_arr2 = [];
      var hotel_type_array = [];
      var checkboxes = document.getElementsByName('hotel_type');
      for (var checkbox of checkboxes) {
        if (checkbox.checked)
          hotel_type_array.push(checkbox.value);
      }
      if (hotel_type_array.length != 0) {
        final_arr2 = (final_arr1).filter(function(a) {
          return hotel_type_array.includes(a.hotel_type);
        });
      } else {
        final_arr2 = final_arr1;
      }
      $('#selected_hotel_type_array').val(hotel_type_array);
      //hotel amenities filter
      var final_arr3 = [];
      var hotel_amenity_array = [];
      var checkboxes = document.getElementsByName('hotel_amenity');
      for (var checkbox of checkboxes) {
        if (checkbox.checked)
          hotel_amenity_array.push(checkbox.value);
      }
      if (hotel_amenity_array.length !== 0) {

        let outputArray = [];
        const addedHotelIds = new Set();
        hotel_amenity_array.forEach(item => {
          final_arr2.forEach(hotel => {
            if (hotel.amenity.includes(item) && !addedHotelIds.has(hotel?.hotel_id)) {
              outputArray.push(hotel);
              addedHotelIds.add(hotel?.hotel_id); // track the ID to avoid duplicates
            }
          });
        });

        final_arr3 = outputArray;
      } else {
        final_arr3 = final_arr2;
      }
      $('#selected_hotel_amenity_array').val(hotel_amenity_array);
      if (flag === true) {

        const valueLow = document.querySelector('.pointer-label.low').textContent;
        const valueHigh = document.querySelector('.pointer-label.high').textContent;
        fromRange_cost = (parseFloat(valueLow));
        toRange_cost = (parseFloat(valueHigh));

        var final_arr4 = [];
        final_arr3.forEach(function(item) {
          var currency_rates = get_currency_rates(item.best_lowest_cost.id, currency_id).split('-');
          var to_currency_rate = currency_rates[0];
          var from_currency_rate = currency_rates[1];
          var amount = parseFloat(from_currency_rate * item.best_lowest_cost.cost).toFixed(2);
          if (compare(amount, fromRange_cost, toRange_cost)) {
            final_arr4.push(item);
          }
        });
        get_price_filter_data_result(final_arr4);
      } else {
        get_price_filter_data_result(final_arr3);
      }
    }, 1000);

  }

  //Display Hotel results data 

  function get_price_filter_data_result(final_arr) {

    var base_url = $('#base_url').val();

    $.post(base_url + 'view/hotel/hotel_results.php', {
      final_arr: final_arr
    }, function(data) {

      $('#hotel_result_block').html(data);

    });

  }

  function getSliderValue() {

    var ranges = $('.slider-input').val().split(',');
    $('.slider-input').attr({

      min: parseFloat(ranges[0]).toFixed(2),

      max: parseFloat(ranges[1]).toFixed(2)

    });
    if (ranges[0] != '' && ranges[1] != '' && ranges[0] !== 'NaN' && ranges[1] !== 'NaN') {

      get_price_filter_data('hotel_results_array', 'price_filter_id', ranges[0], ranges[1], true);

    }

  }

  const setSliderValue = function(fun) {

    let timer;

    return function() {

      let context = this;

      args = arguments;

      clearTimeout(timer);

      timer = setTimeout(() => {

        fun.apply(context, args);

      }, 1000);

    };

  };

  const passSliderValue = setSliderValue(getSliderValue);

  clearTimeout(a); //Make session for best costs
  var a = setTimeout(function() {

    var best_price_list = document.querySelectorAll(".best-cost-currency");
    var best_price_id = document.querySelectorAll(".best-cost-id");
    var bestAmount_arr = [];

    for (var i = 0; i < best_price_list.length; i++) {
      bestAmount_arr.push({
        'amount': best_price_list[i].value,
        'id': best_price_id[i].value
      });
    }
    sessionStorage.setItem('hotel_best_price', JSON.stringify(bestAmount_arr));
  }, 100);

  get_price_filter_data('hotel_results_array', 'price_filter_id', '0', '0', false);
</script>