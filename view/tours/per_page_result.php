<?php
include '../../config.php';
include BASE_URL . 'model/model.php';
$tours_result_array = ($_POST['data'] != '') ? $_POST['data'] : [];
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
    $taxation = $tours_result_array[$i]['taxation'] ?? []; // Ensure it's not null

    if (is_string($taxation)) {
      $taxation = json_decode($taxation, true); // Convert JSON string to array
    }

    if (is_array($taxation) && isset($taxation[0]['taxation_type'], $taxation[0]['service_tax'])) {
      $taxation_type = $taxation[0]['taxation_type'];
      $service_tax = $taxation[0]['service_tax'];
    } else {
      $taxation_type = "N/A"; // Default value
      $service_tax = "0.00";  // Default value
    }
    $package_name = $tours_result_array[$i]['package_name'];
    $link = 'https://web.whatsapp.com/send?phone=' . $app_contact_no . '&text=Hello,
%20I%20am%20interested%20in%20' . $package_name . '%20 tour. Kindly%20provide%20more%20details.%20Thanks!';
?>
    <input type="hidden" id="adult_count" value="<?= $adult_count ?>" />
    <input type="hidden" id="child_wocount" value="<?= $child_wocount ?>" />
    <input type="hidden" id="child_wicount" value="<?= $child_wicount ?>" />
    <input type="hidden" id="extra_bed_count" value="<?= $extra_bed_count ?>" />
    <input type="hidden" id="infant_count" value="<?= $infant_count ?>" />
    <input type="hidden" id="travel_date" value="<?= $travel_date ?>" />
    <input type="hidden" id="taxation-<?= $tours_result_array[$i]['package_id'] ?>" value='<?= $taxation_type . '-' . $ser_type ?>'>
    <!-- ***** Tours Card ***** -->
    <div class="c-cardList type-hotel">
      <div class="c-cardListTable tours-cardListTable">
        <!-- *** Tours Card image *** -->
        <div class="cardList-image">
          <img src="<?= $tours_result_array[$i]['image'] ?>" alt="iTours" class="d-block mb-2" />
          <a target="_blank" href="<?= $link ?>" class="btn btn-outline-success d-block mb-2"><i class="fa fa-whatsapp"></i> Whatsapp</a>
          <a href="" class="btn btn-outline-danger d-block" data-toggle="modal" data-target="#modal<?php echo $tours_result_array[$i]['package_id']; ?>"><i class="fa fa-calculator" aria-hidden="true"></i> Price Calculator</a>
          <input type="hidden" value="<?= $tours_result_array[$i]['image'] ?>" id="image-<?= $tours_result_array[$i]['package_id'] ?>" />
          <div class="typeOverlay">
            <div class="c-discount c-hide" id='discount<?= $tours_result_array[$i]['package_id'] ?>'>
              <div class="discount-text">
                <span class="currency-icon"></span>
                <span class='offer-currency-price' id="offer-currency-price<?= $tours_result_array[$i]['package_id'] ?>"></span>&nbsp;&nbsp;<span id='discount_text<?= $tours_result_array[$i]['package_id'] ?>'></span>
                <span class='c-hide offer-currency-id' id="offer-currency-id<?= $tours_result_array[$i]['package_id'] ?>"></span>
              </div>
            </div>
          </div>
        </div>
        <!-- *** Tours Card image End *** -->

        <!-- *** Tours Card Info *** -->
        <div class="cardList-info" role="button">
          <div class="dividerSection type-1 noborder">
            <div class="divider s1" role="button">
              <a href="#">
                <h4 class="cardTitle"><span id="package-<?= $tours_result_array[$i]['package_id'] ?>"><?= $tours_result_array[$i]['package_name'] ?></span>
                </h4>
              </a>

              <div class="infoSection">
                <span class="cardInfoLine cust">
                  <i class="icon it itours-calendar"></i>
                  <?= $tours_result_array[$i]['total_nights'] ?> Nights <?= $tours_result_array[$i]['total_days'] ?> Days
                  <input type="hidden" value="<?= $tours_result_array[$i]['total_nights'] . '-' . $tours_result_array[$i]['total_days'] ?>" id="days-<?= $tours_result_array[$i]['package_id'] ?>" />
                </span>
              </div>
              <div class="infoSection">
                <span class="cardInfoLine">
                  <?= $tours_result_array[$i]['dest_name'] ?>
                </span>
              </div>
            </div>

            <div class="divider s2">
              <div class="priceTag">
                <?php
                if ($tours_result_array[$i]['best_org_cost']['org_cost'] != '' && $tours_result_array[$i]['best_org_cost']['org_cost'] != $tours_result_array[$i]['best_lowest_cost']['cost']) {
                ?>
                  <div class="p-old">
                    <span class="o_lbl">Old Price</span>
                    <span class="o_price">
                      <span class="p_currency currency-icon"></span>
                      <span class="p_cost best-tours-orgamount"><?= $tours_result_array[$i]['best_org_cost']['org_cost'] ?></span>
                      <span class="c-hide best-tours-orgcurrency-id"><?= $h_currency_id ?></span>
                    </span>
                  </div>
                <?php } ?>
                <div class="p-old">
                  <span class="o_lbl">Price Per Person</span>
                  <span class="price_main">
                    <span class="p_currency currency-icon"></span>
                    <span class="p_cost best-currency-price"><?= $tours_result_array[$i]['best_lowest_cost']['cost'] ?></span>
                    <span class="c-hide best-currency-id"><?= $h_currency_id ?></span>
                  </span>
                  <small class="mb-2 mb-md-0">(Excl of all taxes)</small>
                </div>
                <a target="_blank" href="<?= BASE_URL_B2C . $tours_result_array[$i]['seo_slug'] ?>" class="expandSect">View Details</a>
              </div>
            </div>
          </div>
          <div class="customizedTour">
            <h3 class="customizedTour-title">Customized Holidays</h3>
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
            <?php if ($tours_result_array[$i]['note'] != '') { ?>
              <p class="tourOfferDec"><?= $tours_result_array[$i]['note'] ?></p>
            <?php } ?>
          </div>

        </div>
        <!-- *** Tours Card Info End *** -->
      </div>

    </div>
    <!-- ***** Tours Card End ***** -->
    <?php
    $package_id = $tours_result_array[$i]['package_id'];
    ?>
    <div class="modal fade" id="modal<?php echo $package_id; ?>" aria-labelledby="modalLabel<?php echo $package_id; ?>" data-backdrop="static" data-keyboard="false" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel<?php echo $package_id; ?>"><?php echo $tours_result_array[$i]['package_name']; ?></h5>
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
                    <label class="form-label mb-0" for="date">Travel Date*</label>
                    <div class="datepicker-wrap">
                      <input type="text" name="travelDate" class="input-text full-width" placeholder="mm/dd/yy" id="travelDate<?= $package_id ?>" onchange="calculate_total_cost(<?= $package_id ?>)" required />
                    </div>
                  </div>
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

    <script>
      var tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 10);

      var day = tomorrow.getDate();
      var month = tomorrow.getMonth() + 1
      var year = tomorrow.getFullYear();

      $('#travelDate<?= $package_id ?>').datetimepicker({
        timepicker: false,
        format: 'm/d/Y',
        minDate: tomorrow
      });
    </script>
<?php
  }
} //Activity arrays for loop
?>
<script>
  $(document).ready(function() {

    var base_url = $('#base_url').val();
    clearTimeout(b);
    var b = setTimeout(function() {

      var amount_list = document.querySelectorAll(".tours-currency-price");
      var amount_id = document.querySelectorAll(".tours-currency-id");

      var orgamount_list = document.querySelectorAll(".tours-orgcurrency-price");
      var orgamount_id = document.querySelectorAll(".tours-orgcurrency-id");

      var amount_list1 = document.querySelectorAll(".best-currency-price");
      var amount_id1 = document.querySelectorAll(".best-currency-id");

      var orgamount_list1 = document.querySelectorAll(".best-tours-orgamount");
      var orgamount_id1 = document.querySelectorAll(".best-tours-orgcurrency-id");

      //Tours Cost
      var amount_arr = [];
      for (var i = 0; i < amount_list.length; i++) {
        amount_arr.push({
          'amount': amount_list[i].innerHTML,
          'id': amount_id[i].innerHTML
        });
      }
      sessionStorage.setItem('tours_amount_list', JSON.stringify(amount_arr));

      //Tours Org Cost
      var org_amount_arr = [];
      for (var i = 0; i < orgamount_list.length; i++) {
        org_amount_arr.push({
          'amount': orgamount_list[i].innerHTML,
          'id': orgamount_id[i].innerHTML
        });
      }
      sessionStorage.setItem('tours_orgamount_list', JSON.stringify(org_amount_arr));

      //Tours Best Cost
      var amount_arr1 = [];
      for (var i = 0; i < amount_list1.length; i++) {
        amount_arr1.push({
          'amount': amount_list1[i].innerHTML,
          'id': amount_id1[i].innerHTML
        });
      }
      sessionStorage.setItem('tours_best_amount_list', JSON.stringify(amount_arr1));

      //Tours best Org Cost
      var org_amount_arr1 = [];
      for (var i = 0; i < orgamount_list1.length; i++) {
        org_amount_arr1.push({
          'amount': orgamount_list1[i].innerHTML,
          'id': orgamount_id1[i].innerHTML
        });
      }
      sessionStorage.setItem('tours_best_orgamount_list', JSON.stringify(org_amount_arr1));
      var current_page_url = document.URL;
      tours_page_currencies(current_page_url);
    }, 500);

  });

  function calculate_total_cost(package_id) {

    var base_url = $('#base_url').val();
    var travel_date = $("#travelDate" + package_id).val();
    var adult_count = $("#tadult" + package_id).val();
    var child_wobed = $("#child_wobed" + package_id).val();
    var child_wibed = $("#child_wibed" + package_id).val();
    var extra_bed_c = $("#extra_bed" + package_id).val();
    var infant_c = $("#infant" + package_id).val();

    $.ajax({
      type: 'post',
      url: base_url + 'view/tours/inc/tours_cost_load.php',
      data: {
        package_id: package_id,
        travel_date: travel_date,
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
            html += '<tr' + css + '><td> <b> ' + cost_result1.type + ' </b></td><td class = "text-right">  <b> ' + cost_result1.cost + ' </b></td></tr>';
          });
        }
        $('#tour_total_cost' + package_id).html(html);
      },
    });
  }
</script>
<script>
  setTimeout(() => {

    var total_nights_array = JSON.parse(document.getElementById('total_nights_array').value);
    var selected_total_nights_array = (document.getElementById('selected_total_nights_array').value).split(',');
    var html = '';
    for (var i = 0; i < total_nights_array.length; i++) {

      var checked_status = (selected_total_nights_array.includes(total_nights_array[i])) ? 'checked' : '';
      html += '<div class="form-check"><input type="checkbox" name="nights" class="form-check-input nights_label" id="' + (i + 1) + '" value="' + total_nights_array[i] + '" ' + checked_status + '/><label class="form-check-label nights_label" for="' + (i + 1) + '">' + total_nights_array[i] + ' Nights - ' + (parseInt(total_nights_array[i]) + 1) + ' Days</label></div>';
    }
    $('#total_nights').html(html);

    var total_themes_array = JSON.parse(document.getElementById('total_themes_array').value);
    var selected_theme_array = (document.getElementById('selected_theme_array').value).split(',');
    var html = '';
    total_themes_array.forEach(function(theme) {

      var checked_status = (selected_theme_array.includes(String(theme.theme_id))) ? 'checked' : '';
      html += '<div class="form-check"><input type="checkbox" name="themes" class="form-check-input themes_label" id="t-' + (theme.theme_id) + '" value="' + theme.theme_id + '" ' + checked_status + '/><label class="form-check-label themes_label" for="t-' + (theme.theme_id) + '">' + theme.theme_name + '</label></div>';
    });
    $('#total_themes').html(html);
  }, 500);
</script>