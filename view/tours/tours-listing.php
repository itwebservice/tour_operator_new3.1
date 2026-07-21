<?php
include '../../config.php';
include BASE_URL . 'model/model.php';
include '../../layouts/header2.php';

$_SESSION['page_type'] = 'package';
$currency = $_SESSION['session_currency_id'];
$b2b_agent_code = $_SESSION['b2b_agent_code'];

$sq_to = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$currency'"));
$to_currency_rate = $sq_to['currency_rate'];

$tours_array = json_decode($_SESSION['tours_array']);
$checkDate = date('d M Y', strtotime($tours_array[0]->tour_date));
$checkDate1 = date('d-m-Y', strtotime($tours_array[0]->tour_date));
$date1 = date("Y-m-d", strtotime($tours_array[0]->tour_date));

$pax = intval($tours_array[0]->adult) + intval($tours_array[0]->child_wobed) + intval($tours_array[0]->child_wibed) + intval($tours_array[0]->extra_bed) + intval($tours_array[0]->infant);
$costing_pax = intval($pax) - intval($tours_array[0]->infant);
$dest_id = $tours_array[0]->dest_id;
$tour_id = $tours_array[0]->tour_id;

//City Search
if ($dest_id != '') {
  $sq_dest = mysqli_fetch_assoc(mysqlQuery("select dest_name,dest_id from destination_master where dest_id = '$dest_id' and status!='Inactive'"));
  $query = "select * from custom_package_master where dest_id = '$dest_id' and status!='Inactive'";
}
//Hotel Search
if ($tour_id != '') {
  $sq_tour = mysqli_fetch_assoc(mysqlQuery("select package_id, package_name,total_nights,total_days from custom_package_master where package_id='$tour_id' and status!='Inactive'"));
  $query = "select * from custom_package_master where package_id='$tour_id' and status!='Inactive'";
}
?>

<!-- ********** Component :: Page Title ********** -->
<div class="c-pageTitleSect listingPageTitleSection">

  <div class="container">

    <div class="row">

      <div class="col-md-7 col-12">

        <!-- *** Search Head **** -->

        <div class="searchHeading">

          <span class="pageTitle">Holiday</span>

          <div class="clearfix for-transfer">

            <?php if ($dest_id != '') { ?>

              <div class="sortSection">

                <span class="sortTitle st-search">

                  <i class="icon it itours-pin-alt"></i>

                  Destination: <strong><?= $sq_dest['dest_name'] ?></strong>

                </span>

              </div>

            <?php } ?>

            <?php if ($tour_id != '') { ?>

              <div class="sortSection">

                <span class="sortTitle st-search">

                  <i class="icon it itours-pin-alt"></i>

                  Tour Name: <strong><?= $sq_tour['package_name'] ?></strong>

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

                <?php echo $tours_array[0]->adult; ?> Adult(s), <?php echo $tours_array[0]->child_wobed + $tours_array[0]->child_wibed; ?> Child(ren), <?php echo $tours_array[0]->extra_bed; ?> Extra Bed(s), <?php echo $tours_array[0]->infant; ?> Infant(s)

                <input type="hidden" id="total_passengers" value="<?= $tours_array[0]->adult . '-' . $tours_array[0]->child_wobed . '-' . $tours_array[0]->child_wibed . '-' . $tours_array[0]->extra_bed . '-' . $tours_array[0]->infant ?>" />

              </span><span class="results_count d-none"></span>

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
            <a href="#">Holidays Search Result</a>
          </li>

        </ul>

      </div>



    </div>

  </div>

</div>

<!-- ********** Component :: Page Title End ********** -->

<!-- ********** Component :: Tours Listing  ********** -->

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

                <form id="frm_tours_search">

                  <div class="row">

                    <input type='hidden' id='page_type' value='search_page' name='search_page' />

                    <!-- *** Destination Name *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>Select Destination</label>

                        <div class="c-select2DD">

                          <select id='tours_dest_filter' class="full-width js-roomCount" onchange="package_dynamic_reflect(this.id);">

                            <?php if ($dest_id != '') { ?>

                              <option value="<?php echo $sq_dest['dest_id'] ?>"><?php echo $sq_dest['dest_name'] ?></option>

                            <?php  } ?>

                            <option value="">Destination</option>

                            <?php

                            $sq_query = mysqlQuery("select * from destination_master where status != 'Inactive'");

                            while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>

                              <option value="<?php echo $row_dest['dest_id']; ?>"><?php echo $row_dest['dest_name']; ?></option>

                            <?php } ?>

                          </select>

                        </div>

                      </div>

                    </div>

                    <!-- *** Destination Name End *** -->

                    <!-- *** tours Name *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>Select Tour</label>

                        <div class="c-select2DD">

                          <select id='tours_name_filter' class="full-width js-roomCount">

                            <?php if ($tour_id != '') { ?>

                              <option value="<?php echo $sq_tour['package_id'] ?>"><?php echo $sq_tour['package_name'] . " (" . $sq_tour['total_nights'] . "N /" . $sq_tour['total_days'] . "D)" ?></option>

                              <option value=''>Tour Name</option>

                            <?php } else {

                              if ($dest_id != '') {

                                $querys = "select * from custom_package_master where dest_id = '$dest_id' and status!='Inactive'";
                              } else {

                                $querys = "select * from custom_package_master where 1 and status!='Inactive'";
                              } ?>

                              <option value="">Tour Name</option>

                              <?php

                              $sq_act = mysqlQuery($querys);

                              while ($row_act = mysqli_fetch_assoc($sq_act)) {

                              ?>

                                <option value="<?php echo $row_act['package_id'] ?>"><?php echo $row_act['package_name'] . " (" . $row_act['total_nights'] . "N /" . $row_act['total_days'] . "D)" ?></option>

                            <?php }
                            } ?>

                          </select>

                        </div>

                      </div>

                    </div>

                    <!-- *** tours Name End *** -->

                    <!-- *** Date *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>*Select Travel Date</label>

                        <div class="datepicker-wrap">

                          <input type="text" name="travelDate" class="input-text full-width" placeholder="mm/dd/yy" value="<?= $tours_array[0]->tour_date ?>" id="travelDate" required />

                        </div>

                      </div>

                    </div>

                    <!-- *** Date End *** -->

                    <!-- *** Adult *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>*Adults</label>

                        <div class="selector">

                          <select name="tadult" id='tadult' class="full-width" required>

                            <option value='<?= $tours_array[0]->adult ?>'><?= $tours_array[0]->adult ?></option>

                            <?php for ($m = 0; $m <= 10; $m++) {

                              if ($m != $tours_array[0]->adult) { ?>

                                <option value="<?= $m ?>"><?= $m ?></option>

                            <?php }
                            } ?>

                          </select>

                        </div>

                      </div>

                    </div>

                    <!-- *** Adult End *** -->

                    <!-- *** Child W/o Bed *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>Child Without Bed(2-5 Yrs)</label>

                        <div class="selector">

                          <select name="child_wobed" id='child_wobed' class="full-width">

                            <option value='<?= $tours_array[0]->child_wobed ?>'><?= $tours_array[0]->child_wobed ?></option>

                            <?php for ($m = 0; $m <= 10; $m++) {

                              if ($m != $tours_array[0]->child_wobed) { ?>

                                <option value="<?= $m ?>"><?= $m ?></option>

                            <?php }
                            } ?>

                          </select>

                        </div>

                      </div>

                    </div>

                    <!-- *** Child W/o Bed End *** -->

                    <!-- *** Child With Bed *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>Child With Bed(5-12 Yrs)</label>

                        <div class="selector">

                          <select name="child_wibed" id='child_wibed' class="full-width">

                            <option value='<?= $tours_array[0]->child_wibed ?>'><?= $tours_array[0]->child_wibed ?></option>

                            <?php for ($m = 0; $m <= 10; $m++) {

                              if ($m != $tours_array[0]->child_wibed) { ?>

                                <option value="<?= $m ?>"><?= $m ?></option>

                            <?php }
                            } ?>

                          </select>

                        </div>

                      </div>

                    </div>

                    <!-- *** Child With Bed End *** -->

                    <!-- *** Extra Bed *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>Extra Bed</label>

                        <div class="selector">

                          <select name="extra_bed" id='extra_bed' class="full-width">

                            <option value='<?= $tours_array[0]->extra_bed ?>'><?= $tours_array[0]->extra_bed ?></option>

                            <?php for ($m = 0; $m <= 10; $m++) {

                              if ($m != $tours_array[0]->extra_bed) { ?>

                                <option value="<?= $m ?>"><?= $m ?></option>

                            <?php }
                            } ?>

                          </select>

                        </div>

                      </div>

                    </div>

                    <!-- *** Extra Bed End *** -->

                    <!-- *** Infant *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <div class="form-group">

                        <label>Infants(0-2 Yrs)</label>

                        <div class="selector">

                          <select name="tinfant" id='tinfant' class="full-width">

                            <option value='<?= $tours_array[0]->infant ?>'><?= $tours_array[0]->infant ?></option>

                            <?php for ($m = 0; $m <= 10; $m++) {

                              if ($m != $tours_array[0]->infant) { ?>

                                <option value="<?= $m ?>"><?= $m ?></option>

                            <?php }
                            } ?>

                          </select>

                        </div>

                      </div>

                    </div>

                    <!-- *** Infant End *** -->

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">

                      <button class="c-button lg colGrn m20-top">

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

      <!-- ***** Tours Listing Filter ***** -->

      <div class="col-md-3 col-sm-12">

        <!-- ***** Price Filter ***** -->

        <div class="accordion c-accordion filterPriceSidebar" id="filterPrice">

          <div class="filterItem">
            <div class="heading">
              <h5 class="filterTitle">
                Sort Tours By
              </h5>

            </div>
            <div class="dropdown selectable">
              <select id="price_filter_id" name="price_filter_id" title="Select Destination" class="form-control" style="width:100%" onchange="get_price_filter_data('tours_result_array',this.id,'0','0');">
                <option value="2">Price- Low to High</option>
                <option value="1">Price- High to Low</option>
              </select>
            </div>
          </div>
          <hr />
          <div class="filterItem">
            <div class="heading">
              <h5 class="filterTitle">
                Price Range ( <span class="currency-icon"></span>)
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
          <div class="filterItem">
            <div class="heading">
              <h5 class="filterTitle">
                Recommended For
              </h5>

            </div>

            <div class="checkboxLists" id="total_themes">
            </div>
          </div>
          <hr />

        </div>

        <!-- ***** Price Filter End ***** -->

      </div>

      <!-- ***** Tours Listing Filter End ***** -->

      <!-- ***** Tours Listing ***** -->
      <?php
      $adult_count = $tours_array[0]->adult;
      $child_wocount = $tours_array[0]->child_wobed;
      $child_wicount = $tours_array[0]->child_wibed;
      $extra_bed = $tours_array[0]->extra_bed;
      $infant_count = $tours_array[0]->infant;
      $tours_result_array = array();
      $final_arr = array();
      $actual_ccosts_array = array();
      $all_costs_array = array();
      $total_nights_array = array();
      $total_theme_array = array();

      $sq_query = mysqlQuery($query);
      while (($row_query  = mysqli_fetch_assoc($sq_query))) {

        $hotels_array = array();
        $transport_array = array();
        $program_array = array();
        $package_id = $row_query['package_id'];
        $currency_id = $row_query['currency_id'];
        $taxation = json_decode($row_query['taxation']);

        $sq_dest = mysqli_fetch_assoc(mysqlQuery("select dest_name from destination_master where dest_id = '$row_query[dest_id]' and status!='Inactive'"));
        $sq_terms = mysqli_fetch_assoc(mysqlQuery("select terms_and_conditions from terms_and_conditions where type = 'Package Quotation' and active_flag!='Inactive' and dest_id = '$row_query[dest_id]'"));

        $sq_from = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$currency_id'"));
        $from_currency_rate = $sq_from['currency_rate'];

        if ($row_query['dest_image'] != '0') {

          $row_gallary = mysqli_fetch_assoc(mysqlQuery("select * from gallary_master where entry_id = '$row_query[dest_image]'"));
          $url = $row_gallary['image_url'];
          $pos = strstr($url, 'uploads');
          $entry_id = $row_gallary['entry_id'];
          if ($pos != false) {

            $newUrl1 = preg_replace('/(\/+)/', '/', $row_gallary['image_url']);

            $newUrl = BASE_URL . str_replace('../', '', $newUrl1);
          } else {

            $newUrl =  $row_gallary['image_url'];
          }
        } else {

          $sq_singleImage = mysqli_fetch_assoc(mysqlQuery("select * from default_package_images where dest_id='$row_query[dest_id]'"));

          if ($sq_singleImage['image_url'] != '') {

            $image = $sq_singleImage['image_url'];

            $pos = strstr($url, 'uploads');

            if ($pos != false) {

              $newUrl1 = preg_replace('/(\/+)/', '/', $image);

              $newUrl = BASE_URL . str_replace('../', '', $newUrl1);
            } else {

              $newUrl =  $image;
            }
          } else {

            $newUrl = BASE_URL . 'images/dummy-image.jpg';
          }
        }

        $sq_hotel = mysqlQuery("select * from custom_package_hotels where package_id = '$row_query[package_id]'");  //Package Hotels

        while ($row_hotel = mysqli_fetch_assoc($sq_hotel)) {

          $sq_hcity = mysqli_fetch_assoc(mysqlQuery("select city_name,city_id from city_master where city_id = '$row_hotel[city_name]'"));

          $sq_hhotel = mysqli_fetch_assoc(mysqlQuery("select hotel_name,hotel_id from hotel_master where hotel_id = '$row_hotel[hotel_name]'"));

          array_push($hotels_array, array(

            'city' => $sq_hcity['city_name'],

            'hotel' => $sq_hhotel['hotel_name'],

            'hotel_type' => $row_hotel['hotel_type'],

            'nights' => $row_hotel['total_days'],

          ));
        }

        $sq_trans = mysqlQuery("select * from custom_package_transport where package_id = '$row_query[package_id]'"); //Package Transports
        while ($row_trans = mysqli_fetch_assoc($sq_trans)) {

          $sq_vehicle = mysqli_fetch_assoc(mysqlQuery("select entry_id,vehicle_name from b2b_transfer_master where entry_id = '$row_trans[vehicle_name]'"));

          if ($row_trans['pickup_type'] == 'city') {   //pickup

            $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_trans[pickup]'"));

            $pickup = $row['city_name'];
          } else if ($row_trans['pickup_type'] == 'hotel') {

            $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_trans[pickup]'"));

            $pickup = $row['hotel_name'];
          } else {

            $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_trans[pickup]'"));

            $airport_nam = clean($row['airport_name']);

            $airport_code = clean($row['airport_code']);

            $pickup = $airport_nam . " (" . $airport_code . ")";

            $html = '<optgroup value="airport" label="Airport Name"><option value="' . $row['airport_id'] . '">' . $pickup . '</option></optgroup>';
          }

          if ($row_trans['drop_type'] == 'city') { // Drop

            $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_trans[drop]'"));

            $drop = $row['city_name'];
          } else if ($row_trans['drop_type'] == 'hotel') {

            $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_trans[drop]'"));

            $drop = $row['hotel_name'];
          } else {

            $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_trans[drop]'"));

            $airport_nam = clean($row['airport_name']);

            $airport_code = clean($row['airport_code']);

            $drop = $airport_nam . " (" . $airport_code . ")";
          }

          array_push($transport_array, array(

            'vehicle' => $sq_vehicle['vehicle_name'],

            'pickup' => $pickup,

            'drop' => $drop

          ));
        }

        $sq_prg = mysqlQuery("select * from custom_package_program where package_id = '$row_query[package_id]'");  //Package Program

        while ($row_prg = mysqli_fetch_assoc($sq_prg)) {

          array_push($program_array, array(

            'attraction' => $row_prg['attraction'],

            'day_wise_program' => $row_prg['day_wise_program'],

            'stay' => $row_prg['stay'],

            'meal_plan' => $row_prg['meal_plan'],

          ));
        }

        $costing_array = array();  // Costing from tarif
        $offer_options_array = array();
        $all_orgcosts_array = array();
        $total_cost1 = 0;

        // Fetch offers from custom_package_offers table
        $sq_offers_count = mysqli_num_rows(mysqlQuery("select * from custom_package_offers where (from_date <='$date1' and to_date>='$date1') and package_id='$row_query[package_id]'"));
        if ($sq_offers_count > 0) {
          $sq_offers = mysqlQuery("select * from custom_package_offers where (from_date <='$date1' and to_date>='$date1') and package_id='$row_query[package_id]'");
          while ($row_offers = mysqli_fetch_assoc($sq_offers)) {
            array_push($offer_options_array, array(
              'offer_type' => $row_offers['type'],
              'offer_in' => $row_offers['offer_in'],
              'offer_amount' => (float)($row_offers['offer_amount']),
              'coupon_code' => $row_offers['coupon_code'],
              'agent_type' => $row_offers['agent_type'],
              'from_date' => $row_offers['from_date'],
              'to_date' => $row_offers['to_date'],
              'currency_id' => $currency_id
            ));
          }
        }

        $q = "select * from custom_package_tariff where (`from_date` <= '$date1' and `to_date` >= '$date1') and (`min_pax` <= '$costing_pax' and `max_pax` >= '$costing_pax') and `package_id`='$row_query[package_id]'";
        $sq_tariff = mysqlQuery($q);
        while ($row_tariff = mysqli_fetch_assoc($sq_tariff)) {

          $total_cost1 = ($adult_count * (float)($row_tariff['cadult'])) + ($child_wocount * (float)($row_tariff['ccwob'])) + ($child_wicount * (float)($row_tariff['ccwb'])) + ($extra_bed * (float)($row_tariff['cextra'])) + ($infant_count * (float)($row_tariff['cinfant']));

          $org_cost = $total_cost1;
          $total_cost1 = ceil($total_cost1);
          $c_amount1 = ($to_currency_rate != '') ? $from_currency_rate / $to_currency_rate * $total_cost1 : 0;
          $c_amount1 = ceil($c_amount1);

          array_push($costing_array, array(

            'type' => $row_tariff['hotel_type'],

            'min_pax' => $row_tariff['min_pax'],

            'max_pax' => $row_tariff['max_pax'],

            'badult' => (float)($row_tariff['badult']),

            'bcwb' => (float)($row_tariff['bcwb']),

            'bcwob' => (float)($row_tariff['bcwob']),

            'binfant' => (float)($row_tariff['binfant']),

            'total_cost' => (float)($total_cost1),

            'org_cost' => (float)($org_cost),

            'id' => $currency_id

          ));

          array_push($all_orgcosts_array, array('orgamount' => $org_cost, 'id' => $currency_id));
          array_push($all_costs_array, array('amount' => $c_amount1, 'id' => $currency));
        }

        if (!in_array($row_query['total_nights'], $total_nights_array)) {
          array_push($total_nights_array, $row_query['total_nights']);
        }
        sort($total_nights_array);

        $found = null;
        if ($row_query['tour_theme'] != 0) {
          $sq_theme = mysqli_fetch_assoc(mysqlQuery("select name,id from tour_theme where id = '$row_query[tour_theme]'"));
          foreach ($total_theme_array as $theme) {
            if (intval($theme['theme_id']) === intval($row_query['tour_theme'])) {
              $found = 'yes';
              break;
            }
          }
          if ($found != 'yes') {
            array_push($total_theme_array, array('theme_id' => intval($sq_theme['id']), 'theme_name' => $sq_theme['name']));
          }
        }

        $prices = (sizeof($costing_array)) ? $array_master->array_column($costing_array, 'total_cost') : [];
        $min_array = (sizeof($prices)) ? $costing_array[array_search(min($prices), $prices)] : [];
        $prices1 = $array_master->array_column($all_orgcosts_array, 'orgamount');
        $orgmin_array = (sizeof($prices1)) ? $all_orgcosts_array[array_search(min($prices1), $prices1)] : [];

        $c_amount = ceil($min_array['total_cost']);
        array_push($actual_ccosts_array, $c_amount);

        array_push($tours_result_array, array(

          'image' => $newUrl,
          "package_id" => $row_query['package_id'],
          "package_name" => $row_query['package_name'],
          "seo_slug" => $row_query['seo_slug'],
          "tour_theme" => $row_query['tour_theme'],
          "package_code" => $row_query['package_code'],

          "dest_name" => $sq_dest['dest_name'],

          'adult_cost' => $row_query['adult_cost'],

          'child_without' => $row_query['child_without'],

          'child_with' => $row_query['child_with'],

          'extra_bed' => $row_query['extra_bed'],

          'infant_cost' => $row_query['infant_cost'],

          "total_cost" => $total_cost1,

          'taxation' => $taxation,

          'total_nights' => $row_query['total_nights'],

          'total_days' => $row_query['total_days'],

          'inclusions' => $row_query['inclusions'],

          'exclusions' => $row_query['exclusions'],

          'terms_condition' => $sq_terms['terms_and_conditions'],

          'note' => $row_query['note'],

          "currency_id" => $currency_id,

          "best_lowest_cost" => array(
            'id' => $min_array['id'],

            'cost' => $min_array['total_cost']
          ),

          "best_org_cost" => array(
            "id" => $orgmin_array['id'],

            "org_cost" => $orgmin_array['orgamount']
          ),

          "hotels_array" => $hotels_array,

          "transport_array" => $transport_array,

          "program_array" => $program_array,

          "costing_array" => $costing_array,

          "offer_options_array" => $offer_options_array,

          'adult_count' => $adult_count,

          'child_wocount' => $child_wocount,

          'child_wicount' => $child_wicount,

          'extra_bed_count' => $extra_bed,

          'infant_count' => $infant_count,

          'travel_date' => $checkDate1

        ));
      }

      $all_costs_array = ($all_costs_array == NULL) ? [] : $all_costs_array;
      $min_array = (sizeof($all_costs_array)) ? $all_costs_array[array_search(min($all_costs_array), $all_costs_array)] : [];
      $max_array = (sizeof($all_costs_array)) ? $all_costs_array[array_search(max($all_costs_array), $all_costs_array)] : [];

      $actual_ccosts_array = ($actual_ccosts_array != '') ? $actual_ccosts_array : [];
      $min_value = (sizeof($actual_ccosts_array) != 0) ? min($actual_ccosts_array) : 0;
      $max_value = (sizeof($actual_ccosts_array) != 0) ? max($actual_ccosts_array) : 0;
      $tours_result_array = ($tours_result_array != '') ? $tours_result_array : [];
      ?>

      <input type='hidden' value='<?= json_encode($tours_result_array, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>' id='tours_result_array' name='tours_result_array' />
      <input type='hidden' class='best-cost-currency' id='bestlow_cost' value='<?= $min_value ?>' />
      <input type='hidden' class='best-cost-currency' id='besthigh_cost' value='<?= $max_value ?>' />
      <input type='hidden' class='best-cost-id' id='bestlow_cost' value='<?= ($min_array['id'] != '') ? $min_array['id'] : '0' ?>' />
      <input type='hidden' class='best-cost-id' id='besthigh_cost' value='<?= ($max_array['id'] != '') ? $max_array['id'] : '0'  ?>' />
      <input type="hidden" id='price_rangevalues' />
      <input type="hidden" value='<?= json_encode($total_nights_array, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>' id="total_nights_array" />
      <input type="hidden" id="selected_total_nights_array" />
      <input type="hidden" value='<?= json_encode($total_theme_array, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>' id="total_themes_array" />
      <input type="hidden" id="selected_theme_array" />

      <div class="col-md-9 col-sm-12">
        <div id="tours_result_block">
        </div>
      </div>
      <!-- ***** Tours Listing End ***** -->
    </div>

  </div>

</div>
<style>
  .customizedTour-item:hover {
    color: blue;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
  }


  button.close {
    outline: none;
    box-shadow: none;
  }

  button.close:focus,
  button.close:hover {
    outline: none;
    box-shadow: none;
  }

  .modal-content .modal-title {
    text-align: center !important;
    width: 100%;
  }

  .itours-calendar {
    color: red !important;
  }

  .cardInfoLine::before {
    color: red !important;
  }

  /* Fix for View Details button clickability on mobile only */
  @media (max-width: 768px) {
    .priceTag .expandSect {
      position: relative !important;
      z-index: 999 !important;
      pointer-events: auto !important;
      display: inline-block !important;
      min-height: 44px !important;
      min-width: 44px !important;
      touch-action: manipulation !important;
      -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1) !important;
      margin-top: 15px !important;
    }
    
    .divider.s2 {
      position: relative;
      z-index: 1;
    }
  }
</style>
<!-- ********** Component :: Tours Listing End ********** -->
<?php include '../../layouts/footer2.php'; ?>

<script>
  /////// Next 10th day onwards date display

  var tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 10);

  var day = tomorrow.getDate();
  var month = tomorrow.getMonth() + 1
  var year = tomorrow.getFullYear();

  $('#travelDate').datetimepicker({
    timepicker: false,
    format: 'm/d/Y',
    minDate: tomorrow
  });
  $(document).ready(function() {

    $('body').delegate('.nights_label', 'click', function() {
      get_price_filter_data('tours_result_array', 'price_filter_id', '0', '0');
    })
    $('body').delegate('.themes_label', 'click', function() {
      get_price_filter_data('tours_result_array', 'price_filter_id', '0', '0');
    })

  });

  // Get currency changed values in hotel result

  function get_price_filter_data(tours_result_array, type1, fromRange_cost, toRange_cost, flag = true) { // Get tours results data

    var type = $('#' + type1).val();
    setTimeout(() => {

      var selected_value = document.getElementById(tours_result_array).value;
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

          if (a.best_lowest_cost.id == null) {

            a.best_lowest_cost.id = '0';

            a.best_lowest_cost.cost = 0;

          }

          if (b.best_lowest_cost.id == null) {

            b.best_lowest_cost.id = '0';

            b.best_lowest_cost.cost = 0;

          }

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

      var final_arr1 = [];
      var total_nights_array = [];

      var checkboxes = document.getElementsByName('nights');
      for (var checkbox of checkboxes) {

        if (checkbox.checked)

          total_nights_array.push(checkbox.value);
      }
      if (total_nights_array.length !== 0) {
        final_arr1 = (final_arr).filter(function(a) {
          return total_nights_array.includes(a.total_nights);
        });
      } else {
        final_arr1 = final_arr;
      }
      $('#selected_total_nights_array').val(total_nights_array);

      var final_arr2 = [];
      var selected_theme_array = $('#selected_theme_array').val();
      var total_themes_array = [];
      var checkboxes = document.getElementsByName('themes');
      for (var checkbox of checkboxes) {

        if (checkbox.checked)

          total_themes_array.push(checkbox.value);

      }
      if (total_themes_array.length !== 0) {
        final_arr2 = (final_arr1).filter(function(a) {

          return total_themes_array.includes(a.tour_theme);
        });
      } else {
        final_arr2 = final_arr1;
      }

      $('#selected_theme_array').val(total_themes_array);

      if (flag === true) {

        const valueLow = document.querySelector('.pointer-label.low').textContent;
        const valueHigh = document.querySelector('.pointer-label.high').textContent;
        fromRange_cost = (parseFloat(valueLow));
        toRange_cost = (parseFloat(valueHigh));

        var final_arr3 = [];
        final_arr2.forEach(function(item) {
          var currency_rates = get_currency_rates(item.best_lowest_cost.id, currency_id).split('-');
          var to_currency_rate = currency_rates[0];
          var from_currency_rate = currency_rates[1];
          var amount = parseFloat(from_currency_rate * item.best_lowest_cost.cost).toFixed(2);
          if (compare(amount, fromRange_cost, toRange_cost)) {
            final_arr3.push(item);
          }
        });
        get_price_filter_data_result(final_arr3);
      } else {
        get_price_filter_data_result(final_arr2);
      }

    }, 1000);

  }

  function get_price_filter_data_result(final_arr) { //Display tours results data 

    var base_url = $('#base_url').val();

    $.post('tours_results.php', {
      final_arr: final_arr
    }, function(data) {

      $('#tours_result_block').html(data);

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

      get_price_filter_data('tours_result_array', 'price_filter_id', ranges[0], ranges[1], true);

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


  clearTimeout(a); //Make session for best hotel costs

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

    sessionStorage.setItem('tours_best_price', JSON.stringify(bestAmount_arr));

  }, 100);
  get_price_filter_data('tours_result_array', 'price_filter_id', '0', '0', false);
</script>
<script type="text/javascript" src="js/index.js"></script>
<script type="text/javascript" src="../../js2/scripts.js"></script>
<script type="text/javascript" src="../../js2/jquery.range.min.js"></script>
<script type="text/javascript" src="../../js2/pagination.min.js"></script>