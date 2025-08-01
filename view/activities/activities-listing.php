<?php
include '../../config.php';
include BASE_URL . 'model/model.php';
include '../../layouts/header2.php';

global $currency;
$_SESSION['page_type'] = 'activities';
$b2b_currency = $_SESSION['session_currency_id'];
$activity_array = isset($_SESSION['activity_array']) ? json_decode($_SESSION['activity_array']) : [];

$sq_to = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$currency'"));
$to_currency_rate = $sq_to['currency_rate'];

$checkDate = date('d M Y', strtotime($activity_array[0]->checkDate));
$date1 = date("Y-m-d", strtotime($activity_array[0]->checkDate));
$child_count = isset($activity_array[0]->child) ? $activity_array[0]->child : 0;
$infant_count = isset($activity_array[0]->infant) ? $activity_array[0]->infant : 0;
$pax = (int)$activity_array[0]->adult + (int)$child_count + (int)$infant_count;

$city_id = isset($activity_array[0]->activity_city_id) ? $activity_array[0]->activity_city_id : '';
$activities_id = $activity_array[0]->activities_id;
$day = date("l", strtotime($date1));

if ($city_id == '' && $activities_id == '') {

  $query = "select * from excursion_master_tariff where 1 and active_flag='Active'";
}

//City Search

else if ($city_id != '') {

  $sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name,city_id from city_master where city_id='$city_id'"));

  $query = "select * from excursion_master_tariff where city_id='$city_id'";
}

//Hotel Search

if ($activities_id != '') {

  $sq_exc = mysqli_fetch_assoc(mysqlQuery("select entry_id,excursion_name from excursion_master_tariff where entry_id='$activities_id'"));

  $query = "select * from excursion_master_tariff where entry_id='$activities_id'";
}
?>

<!-- ********** Component :: Page Title ********** -->

<div class="c-pageTitleSect">

  <div class="container">

    <div class="row">

      <div class="col-md-7 col-12">



        <!-- *** Search Head **** -->

        <div class="searchHeading">

          <span class="pageTitle">Activity</span>



          <div class="clearfix">

            <?php if ($city_id != '') { ?>

              <div class="sortSection">

                <span class="sortTitle st-search">

                  <i class="icon it itours-pin-alt"></i>

                  City: <strong><?= $sq_city['city_name'] ?></strong>

                </span>

              </div>

            <?php } ?>

            <?php if ($activities_id != '') { ?>

              <div class="sortSection">

                <span class="sortTitle st-search" style="padding-left:0px!important;">

                  Activity: <strong><?= $sq_exc['excursion_name'] ?></strong>

                </span>

              </div>

            <?php } ?>



          </div>



          <div class="clearfix">



            <div class="sortSection">

              <span class="sortTitle st-search">

                <i class="icon it itours-timetable"></i>

                Date: <strong><?= $checkDate ?></strong>

              </span>

            </div>



            <div class="sortSection">

              <span class="sortTitle st-search">

                <i class="icon it itours-person"></i>

                <?php

                $adult_label = 'Adult(s)';

                $child_label = 'Child(ren)';

                $infant_label = 'Infant(s)';

                echo $activity_array[0]->adult . ' ' . $adult_label; ?> , <?php echo $child_count . ' ' . $child_label; ?>, <?php echo $infant_count . ' ' . $infant_label; ?>

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

            <a href="#">Activity Search Result</a>

          </li>

        </ul>

      </div>



    </div>

  </div>

</div>

<!-- ********** Component :: Page Title End ********** -->
<!-- ********** Component :: Activity Listing  ********** -->

<div class="c-containerDark">

  <div class="container">

    <!-- ********** Component :: Modify Filter  ********** -->

    <div class="row c-modifyFilter">

      <div class="col">

        <!-- Modified Search Filter -->

        <div class="accordion c-accordion" id="modifySearch_filter">

          <div class="card">

            <div class="card-header" id="headingThree">

              <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#jsModifySearch_filter" aria-expanded="false" aria-controls="jsModifySearch_filter">

                Modify Search >> <span class="results_count"></span><?php echo ' available for ' . $pax . ' Pax'; ?>

              </button>

              <input type="hidden" value="<?= $pax ?>" id="total_pax" />

            </div>

            <div id="jsModifySearch_filter" class="collapse" aria-labelledby="jsModifySearch_filter" data-parent="#modifySearch_filter">

              <div class="card-body">

                <form id="frm_activities_search">

                  <div class="row">

                    <input type='hidden' id='page_type' value='search_page' name='search_page' />

                    <!-- *** City Name *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>Enter City</label>

                        <div class="c-select2DD">

                          <select id='activities_city_filter' class="full-width js-roomCount" onchange="activities_names_load(this.id);">

                            <?php if ($city_id != '') { ?>

                              <option value="<?php echo $sq_city['city_id'] ?>" selected="selected"><?php echo $sq_city['city_name'] ?></option>

                            <?php  } ?>
                            <option value="">City Name</option>
                            <?php
                            $sq_city_name = mysqli_fetch_all(mysqlQuery("select city_id, city_name from city_master where 1"), MYSQLI_ASSOC);
                            foreach ($sq_city_name as $city) {
                              echo '<option value="' . $city['city_id'] . '">' . $city['city_name'] . '</option>';
                            } ?>

                          </select>

                        </div>

                      </div>

                    </div>

                    <!-- *** City Name End *** -->

                    <!-- *** Activities Name *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>Enter Activity</label>

                        <div class="c-select2DD">

                          <select id='activities_name_filter' class="full-width js-roomCount">

                            <?php if ($activities_id != '') { ?>

                              <option value="<?php echo $sq_exc['entry_id'] ?>"><?php echo $sq_exc['excursion_name'] ?></option>

                              <option value=''>Activity Name</option>

                            <?php } else {

                              if ($city_id != '') {

                                $querys = "select entry_id,excursion_name from excursion_master_tariff where entry_id='$activities_id'";
                              } else {

                                $querys = "select entry_id, excursion_name from excursion_master_tariff where 1";
                              } ?>

                              <option value=''>Activity Name</option>

                              <?php

                              $sq_act = mysqlQuery($query);

                              while ($row_act = mysqli_fetch_assoc($sq_act)) {

                              ?>

                                <option value="<?php echo $row_act['entry_id'] ?>"><?php echo $row_act['excursion_name'] ?></option>

                            <?php }
                            } ?>

                          </select>

                        </div>

                      </div>

                    </div>

                    <!-- *** Activities Name End *** -->



                    <!-- *** Date *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>*Select Date</label>

                        <div class="datepicker-wrap">

                          <input type="text" name="checkDate" class="input-text full-width" placeholder="mm/dd/yy" id="checkDate" value="<?= $activity_array[0]->checkDate ?>" required />

                        </div>

                      </div>

                    </div>

                    <!-- *** Date End *** -->
                    <!-- *** Adult *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>*Adults</label>

                        <div class="selector">

                          <select name="adult" id='adult' class="full-width" required>

                            <option value='<?= $activity_array[0]->adult ?>'><?= $activity_array[0]->adult ?></option>

                            <?php

                            for ($m = 0; $m <= 20; $m++) {

                              if ($m != $activity_array[0]->adult) {

                            ?>

                                <option value="<?= $m ?>"><?= $m ?></option>

                            <?php }
                            } ?>

                          </select>

                        </div>

                      </div>

                    </div>

                    <!-- *** Adult End *** -->
                    <!-- *** Child *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>Children(2-12 Yrs)</label>

                        <div class="selector">

                          <select name="child" id='child' class="full-width">

                            <option value='<?= $activity_array[0]->child ?>'><?= $activity_array[0]->child ?></option>

                            <?php

                            for ($m = 0; $m <= 20; $m++) {

                              if ($m != $activity_array[0]->child) {

                            ?>

                                <option value="<?= $m ?>"><?= $m ?></option>

                            <?php }
                            } ?>

                          </select>

                        </div>

                      </div>

                    </div>

                    <!-- *** Child End *** -->

                    <!-- *** Infant *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>Infants(0-2 Yrs)</label>

                        <div class="selector">

                          <select name="infant" id='infant' class="full-width">

                            <option value='<?= $activity_array[0]->infant ?>'><?= $activity_array[0]->infant ?></option>

                            <?php

                            for ($m = 0; $m <= 20; $m++) {

                              if ($m != $activity_array[0]->infant) {

                            ?>

                                <option value="<?= $m ?>"><?= $m ?></option>

                            <?php }
                            } ?>

                          </select>

                        </div>

                      </div>

                    </div>

                    <!-- *** Infant End *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <button class="c-button lg colGrn m26-top">

                        <i class="icon itours-search"></i> SEARCH NOW

                      </button>

                    </div>

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
                Sort Activities By
              </h5>

            </div>
            <div class="dropdown selectable">

              <div class="dropdown selectable">

                <select id="price_filter_id" name="price_filter_id" title="Select Destination" class="form-control" style="width:100%" onchange="get_price_filter_data('activity_result_array',this.id,'0','0');">
                  <option value="2">Price- Low to High</option>
                  <option value="1">Price- High to Low</option>
                </select>
              </div>
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
                Duration
              </h5>

            </div>
            <div class="checkboxLists" id="total_nights">
            </div>
          </div>
          <hr />

        </div>

        <!-- ***** Price Filter End ***** -->
      </div>

      <!-- ***** Activity Listing ***** -->
      <?php
      $adult_count = $activity_array[0]->adult;
      $activity_result_array = array();
      $actual_ccosts_array = array();
      $actual_orgcosts_array = array();
      $service_duration_array = array();

      $sq_query = mysqlQuery($query);
      while (($row_query  = mysqli_fetch_assoc($sq_query))) {

        $all_costs_array = array();
        $all_orgcosts_array = array();
        $sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$row_query[city_id]'"));

        if ($row_query['off_days'] != '') {

          $off_days = $row_query['off_days'];
          $off_day = (strpos($off_days, $day) === false) ? true : false;
        } else {

          $off_day = true;
        }

        if ($off_day) {

          $transfer_options_array = array();

          $exc_id = $row_query['entry_id'];
          $currency_id = $row_query['currency_code'];

          $timing_slots = json_decode($row_query['timing_slots']);

          $sq_from = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$currency_id'"));
          $from_currency_rate = $sq_from['currency_rate'];

          //Single Hotel Image

          $sq_singleImage = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master_images where exc_id='$exc_id'"));

          if ($sq_singleImage['image_url'] != '') {

            $image = $sq_singleImage['image_url'];

            $newUrl1 = preg_replace('/(\/+)/', '/', $image);

            $newUrl1 = explode('uploads', $newUrl1);

            $newUrl = BASE_URL . 'uploads' . $newUrl1[1];
          } else {

            $newUrl = BASE_URL_B2C . 'images/activity_default.png';
          }


          //Tariff Master 
          $sq_count = mysqli_num_rows(mysqlQuery("select * from excursion_master_tariff_basics where exc_id='$exc_id' and (from_date <='$date1' and to_date>='$date1')"));
          if ($sq_count > 0) {
            $sq_tariff_master = mysqlQuery("select * from excursion_master_tariff_basics where exc_id='$exc_id' and (from_date <='$date1' and to_date>='$date1')");
            while (($row_tariff_master  = mysqli_fetch_assoc($sq_tariff_master))) {

              $adult_count = isset($adult_count) ? (int)$adult_count : 0;
              $child_count = isset($child_count) ? (int)$child_count : 0;
              $infant_count = isset($infant_count) ? (int)$infant_count : 0;
              if ($row_tariff_master['markup_in'] == 'Flat') {
                $adult_markup_cost = $adult_count * $row_tariff_master['markup_cost'];
                $child_markup_cost = $child_count * $row_tariff_master['markup_cost'];
                $infant_markup_cost = $infant_count * $row_tariff_master['markup_cost'];
              } else {

                $adult_markup_cost = $adult_count * ($row_tariff_master['adult_cost'] * $row_tariff_master['markup_cost'] / 100);
                $child_markup_cost = $child_count * ($row_tariff_master['child_cost'] * $row_tariff_master['markup_cost'] / 100);
                $infant_markup_cost = $infant_count * ($row_tariff_master['infant_cost'] * $row_tariff_master['markup_cost'] / 100);
              }

              $total_cost1 = (
                ((int)$adult_count * (float)$row_tariff_master['adult_cost']) + $adult_markup_cost +
                ((int)$child_count * (float)$row_tariff_master['child_cost']) + $child_markup_cost +
                ((int)$infant_count * (float)$row_tariff_master['infant_cost']) + $infant_markup_cost +
                (float)$row_tariff_master['transfer_cost']
              );

              $c_amount1 = ($to_currency_rate != '') ? 1 / $from_currency_rate * $total_cost1 : 0;
              array_push($all_orgcosts_array, array('orgamount' => (float)($c_amount1), 'id' => $currency_id));
              array_push($actual_orgcosts_array, $c_amount1);
              $total_cost = ($c_amount1);

              //Checking discount applied or not
              $sq_offers_count = mysqli_num_rows(mysqlQuery("select * from excursion_master_offers where (from_date <='$date1' and to_date>='$date1') and exc_id='$exc_id'"));
              if ($sq_offers_count > 0) {
                $row_offers = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master_offers where (from_date <='$date1' and to_date>='$date1') and exc_id='$exc_id'"));
                $offer_type = $row_offers['type'];
                $offer_in = $row_offers['offer_in'];
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
              //Offers
              $offer_applied = '';
              if ($offer_type != '') {
                if ($offer_type == 'Offer') {
                  if ($offer_in == 'Percentage') {
                    $offer_applied = $total_cost1 * ($offer_amount / 100);
                  } else {

                    if ($currency != $b2b_currency) {

                      $sq_to = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$currency_id'"));
                      $to_currency_rate = $sq_to['currency_rate'];
                      $offer_applied = ($to_currency_rate != '') ? 1 / $from_currency_rate * $offer_amount : 0;
                    } else {
                      $sq_to = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$b2b_currency'"));
                      $to_currency_rate = $sq_to['currency_rate'];
                      $offer_applied = ($to_currency_rate != '') ? 1 / $from_currency_rate * $offer_amount : 0;
                    }
                  }
                  $c_amount1 = $c_amount1 - $offer_applied;
                }
              }
              // echo $row_query['excursion_name'] . '=' . $offer_applied . '<br/>';
              //Final cost push into array
              array_push($transfer_options_array, array(
                "transfer_option" => $row_tariff_master['transfer_option'],
                "total_cost" => $c_amount1,
                "org_cost" => $total_cost,
                'offer_type' => $offer_type,
                'offer_in' => $offer_in,
                'offer_amount' => $offer_amount,
                'coupon_code' => $coupon_code,
                'agent_type' => $agent_type,
                'offer_applied' => $offer_applied
              ));
              array_push($all_costs_array, array('amount' => (float)($c_amount1), 'id' => $currency_id));
              array_push($actual_ccosts_array, (float)($c_amount1));
            }
            $prices = $array_master->array_column($all_costs_array, 'amount');
            $min_array = $all_costs_array[array_search(min($prices), $prices)];
            $prices1 = $array_master->array_column($all_orgcosts_array, 'orgamount');
            $orgmin_array = $all_orgcosts_array[array_search(min($prices1), $prices1)];

            array_push($activity_result_array, array(

              "exc_id" => (int)($exc_id),
              "excursion_name" => $row_query['excursion_name'],
              "city_name" => $sq_city['city_name'],
              "image" => $newUrl,
              "currency_id" => (int)($currency_id),
              "duration" => $row_query['duration'],
              "departure_point" => $row_query['departure_point'],
              "rep_time" => $row_query['rep_time'],
              "description" => $row_query['description'],
              'note' => $row_query['note'],
              'inclusions' => $row_query['inclusions'],
              'exclusions' => $row_query['exclusions'],
              'terms_condition' => $row_query['terms_condition'],
              'useful_info' => $row_query['useful_info'],
              'booking_policy' => $row_query['booking_policy'],
              'canc_policy' => $row_query['canc_policy'],
              'adult_count' => $adult_count,
              'child_count' => $child_count,
              'infant_count' => $infant_count,
              'actDate' => $date1,
              'timing_slots' => $row_query['timing_slots'],
              'transfer_options' => $transfer_options_array,
              'best_lowest_cost' => array(
                'id' => $min_array['id'],
                'cost' => $min_array['amount']
              ),
              'best_org_cost' => array(
                "id" => $orgmin_array['id'],
                "org_cost" => $orgmin_array['orgamount']
              )
            ));
          }
        }

        for ($i = 0; $i < sizeof($activity_result_array); $i++) {

          $duration = trim($activity_result_array[$i]['duration']);
          $service_duration_array = array_map('trim', $service_duration_array);
          if (!empty($activity_result_array[$i]['duration']) && !in_array($duration, $service_duration_array)) {
            array_push($service_duration_array, $activity_result_array[$i]['duration']);
          }
        }
        sort($service_duration_array);
      }
      $all_costs_array = ($all_costs_array == NULL) ? [] : $all_costs_array;
      $all_costs_array1 = $array_master->array_column($all_costs_array, 'amount');
      $min_array = (sizeof($all_costs_array)) ? $all_costs_array[array_search(min($all_costs_array), $all_costs_array)] : [];
      $max_array = (sizeof($all_costs_array)) ? $all_costs_array[array_search(max($all_costs_array), $all_costs_array)] : [];

      $actual_ccosts_array = ($actual_ccosts_array != '') ? $actual_ccosts_array : [];
      $activity_result_array = ($activity_result_array != '') ? $activity_result_array : [];
      $min_value = (sizeof($actual_ccosts_array) != 0) ? min($actual_ccosts_array) : 0;
      $max_value = (sizeof($actual_ccosts_array) != 0) ? max($actual_ccosts_array) : 0;
      ?>

      <input type='hidden' value='<?= json_encode($activity_result_array, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>' id='activity_result_array' name='activity_result_array' />
      <input type='hidden' class='best-cost-currency' id='bestlow_cost' value='<?= ($min_value != '') ? $min_value : '0'  ?>' />
      <input type='hidden' class='best-cost-currency' id='besthigh_cost' value='<?= ($max_value != '') ? $max_value : '0' ?>' />
      <input type='hidden' class='best-cost-id' id='bestlow_cost' value='<?= ($min_array['id'] != '') ? $min_array['id'] : '0' ?>' />
      <input type='hidden' class='best-cost-id' id='besthigh_cost' value='<?= ($max_array['id'] != '') ? $max_array['id'] : '0'  ?>' />
      <input type="hidden" id='price_rangevalues' />
      <input type="hidden" value='<?= json_encode($service_duration_array, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>' id="service_duration_array" />
      <input type="hidden" id="selected_service_duration_array" />

      <div class="col-md-9 col-sm-12">

        <div id="activity_result_block">

        </div>

      </div>

      <!-- ***** Activity Listing End ***** -->

    </div>

  </div>

</div>

<!-- ********** Component :: Activity Listing End ********** -->
<?php include '../../layouts/footer2.php'; ?>
<script type="text/javascript" src="js/index.js"></script>
<script type="text/javascript" src="../../js2/jquery.range.min.js"></script>
<script type="text/javascript" src="../../js2/pagination.min.js"></script>

<script>
  $('#checkDate').datetimepicker({
    timepicker: false,
    format: 'm/d/Y',
    minDate: new Date()
  });
  $('body').delegate('.lblsdfilterChk', 'click', function() {
    get_price_filter_data('activity_result_array', 'price_filter_id', '0', '0');
  })

  function get_price_filter_data(activity_result_array, type1, fromRange_cost, toRange_cost, flag = true) {

    var base_url = $('#base_url').val();
    var type = $('#' + type1).val();
    setTimeout(() => {
      var selected_value = document.getElementById(activity_result_array).value;
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
          //First Value
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
          //First Value
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
      //service duration filter
      var final_arr1 = [];
      var service_duration_array = [];
      var checkboxes = document.getElementsByName('service_duration');
      for (var checkbox of checkboxes) {
        if (checkbox.checked)
          service_duration_array.push(checkbox.value);
      }
      if (service_duration_array.length != 0) {
        final_arr1 = (final_arr).filter(function(a) {
          return service_duration_array.includes(a.duration);
        });
      } else {
        final_arr1 = final_arr;
      }
      $('#selected_service_duration_array').val(service_duration_array);

      if (flag === true) {

        const valueLow = document.querySelector('.pointer-label.low').textContent;
        const valueHigh = document.querySelector('.pointer-label.high').textContent;
        fromRange_cost = (parseFloat(valueLow));
        toRange_cost = (parseFloat(valueHigh));

        var final_arr2 = [];
        final_arr1.forEach(function(item) {
          var currency_rates = get_currency_rates(item.best_lowest_cost.id, currency_id).split('-');
          var to_currency_rate = currency_rates[0];
          var from_currency_rate = currency_rates[1];
          var amount = parseFloat(from_currency_rate * item.best_lowest_cost.cost).toFixed(2);
          if (compare(amount, fromRange_cost, toRange_cost)) {
            final_arr2.push(item);
          }
        });
        get_price_filter_data_result(final_arr2);
      } else {
        get_price_filter_data_result(final_arr1);
      }
    }, 1000);

  }

  //Display Hotel results data 

  function get_price_filter_data_result(final_arr) {

    var base_url = $('#base_url').val();
    $.post(base_url + 'view/activities/activity_results.php', {
      final_arr: final_arr
    }, function(data) {

      $('#activity_result_block').html(data);

    });

  }


  ///////////Debounce function for range slider////////////
  function getSliderValue() {
    var ranges = $('.slider-input').val().split(',');

    $('.slider-input').attr({
      min: parseFloat(ranges[0]).toFixed(2),
      max: parseFloat(ranges[1]).toFixed(2)
    });
    if (ranges[0] != '' && ranges[1] != '' && ranges[0] !== 'NaN' && ranges[1] !== 'NaN') {
      get_price_filter_data('activity_result_array', 'price_filter_id', ranges[0], ranges[1], true);
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
      }, 800);
    };
  };
  const passSliderValue = setSliderValue(getSliderValue);
  //Make session for best hotel costs
  clearTimeout(a);
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
    sessionStorage.setItem('activity_best_price', JSON.stringify(bestAmount_arr));
  });
  get_price_filter_data('activity_result_array', 'price_filter_id', '0', '0', false);
</script>