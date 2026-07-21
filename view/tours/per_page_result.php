<?php
include '../../config.php';
include BASE_URL . 'model/model.php';
$tours_result_array = ($_POST['data'] != '') ? $_POST['data'] : [];
$coupon_list_arr = array();
$coupon_info_arr = array();
global $currency;
$b2b_currency = $_SESSION['session_currency_id'];
//Get selected currency rate
$sq_to = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$b2b_currency'"));
$to_currency_rate = $sq_to['currency_rate'];

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
    
    // Process offers - keep everything in tour's currency, let JavaScript handle conversion
    $offer_text = '';
    $offer_price_display = '';
    $offer_price_flag = '';
    $offer_currency_id_val = $currency;
    $coupon_offer = 0;
    $show_offer = false;
    $offer_amount = 0; // Initialize offer amount
    
    // Keep costs in tour's original currency
    $original_cost = (float)$tours_result_array[$i]['best_lowest_cost']['cost'];
    $discounted_cost = $original_cost;
    
    if (!empty($tours_result_array[$i]['offer_options_array']) && is_array($tours_result_array[$i]['offer_options_array'])) {
      foreach ($tours_result_array[$i]['offer_options_array'] as $offer_data) {
        $offer_in_value = strtolower(trim($offer_data['offer_in'] ?? ''));
        $offer_type_value = strtolower(trim($offer_data['offer_type'] ?? ''));
        
        if ($offer_type_value != '') {
          $offer_amount = (float)$offer_data['offer_amount'];
          
          if ($offer_type_value == 'offer') {
            if ($offer_in_value === 'percentage') {
              $coupon_offer = ($original_cost * ($offer_amount / 100));
            } else {
              // Flat amount offer - keep in tour currency
              $coupon_offer = $offer_amount;
            }
            
            // Calculate discounted cost (in tour currency)
            if ($coupon_offer > 0) {
              $discounted_cost = $original_cost - $coupon_offer;
              $discounted_cost = ($discounted_cost > 0) ? ceil($discounted_cost) : 0;
            }
          } else if ($offer_type_value == 'coupon') {
            $coupon_offer = 0;
          }
          
          if ($offer_in_value === 'percentage') {
            $offer_price_display = rtrim(rtrim(number_format($offer_amount, 2, '.', ''), '0'), '.') . '%';
            $offer_price_flag = 'percentage';
            $offer_currency_id_val = 'PERCENT';
            $offer_text = rtrim(rtrim(number_format($offer_amount, 2, '.', ''), '0'), '.') . '% ' . ucfirst($offer_data['offer_type']);
          } else {
            $offer_text = ucfirst($offer_data['offer_type']);
            // Store offer amount in tour's currency, JavaScript will convert
            $offer_price_display = sprintf("%.2f", $offer_amount);
            $offer_price_flag = 'no';
            $offer_currency_id_val = $h_currency_id; // Store in tour currency
          }
          
          if ($offer_type_value == 'offer' && $coupon_offer > 0) {
            $show_offer = true;
            break; // Use first valid offer
          }
        }
      }
    }
    
    $original_cost = ceil($original_cost);
    $discounted_cost = ceil($discounted_cost);
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
            <div class="c-discount <?= $show_offer ? '' : 'c-hide' ?>" id='discount<?= $tours_result_array[$i]['package_id'] ?>' <?= ($offer_price_flag == 'percentage') ? 'data-offer-type="percentage"' : '' ?>>
              <div class="discount-text">
                <span class="currency-icon percentage-offer-icon" <?= ($offer_price_flag == 'percentage') ? 'style="display:none !important;"' : '' ?>></span>
                <span class='offer-currency-price percentage-offer-price' id="offer-currency-price<?= $tours_result_array[$i]['package_id'] ?>" <?= ($show_offer && $offer_price_flag != 'percentage') ? 'data-amount="' . $offer_amount . '"' : ($offer_price_flag == 'percentage' ? 'style="display:none !important;"' : '') ?>></span>
                <span class="<?= ($offer_price_flag == 'percentage') ? '' : 'ml-5px' ?>" id='discount_text<?= $tours_result_array[$i]['package_id'] ?>'><?= htmlspecialchars($offer_text, ENT_QUOTES, 'UTF-8') ?></span>
                <span class='c-hide offer-currency-id' id="offer-currency-id<?= $tours_result_array[$i]['package_id'] ?>"><?= $offer_currency_id_val ?></span>
                <span class='c-hide offer-currency-flag' id="offer-currency-flag<?= $tours_result_array[$i]['package_id'] ?>"><?= $offer_price_flag ?></span>
              </div>
            </div>
          <?php if ($offer_price_flag == 'percentage') { ?>
          <style>
            #discount<?= $tours_result_array[$i]['package_id'] ?> .currency-icon,
            #discount<?= $tours_result_array[$i]['package_id'] ?> .offer-currency-price {
              display: none !important;
              visibility: hidden !important;
            }
          </style>
          <?php } ?>
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
                <div class="p-old <?= ($original_cost != $discounted_cost) ? '' : 'c-hide' ?>" id="old_price<?= $tours_result_array[$i]['package_id'] ?>">
                  <span class="o_lbl">Old Price</span>
                  <span class="o_price">
                    <span class="p_currency currency-icon"></span>
                    <span class="p_cost original-currency-price" id="price_sub<?= $tours_result_array[$i]['package_id'] ?>"><?= ($original_cost != $discounted_cost) ? sprintf("%.2f", $original_cost) : '' ?></span>
                    <span class="c-hide original-currency-id" id="price_subid<?= $tours_result_array[$i]['package_id'] ?>"><?= $h_currency_id ?></span>
                  </span>
                </div>
                <div class="p-old">
                  <span class="o_lbl">Price Per Person</span>
                  <span class="price_main">
                    <span class="p_currency currency-icon"></span>
                    <span class="p_cost best-currency-price" id="best_cost<?= $tours_result_array[$i]['package_id'] ?>"><?= sprintf("%.2f", $discounted_cost) ?></span>
                    <span class="c-hide best-currency-id" id="best_cost_cid<?= $tours_result_array[$i]['package_id'] ?>"><?= $h_currency_id ?></span>
                  </span>
                  <small class="mb-2 mb-md-0">(Excl of all taxes)</small>
                </div>
                <a target="_blank" href="<?= BASE_URL_B2C . $tours_result_array[$i]['seo_slug'] ?>" class="expandSect" onclick="event.stopPropagation();">View Details</a>
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

      // Set best cost and handle old price display
      setTimeout(function() {
        var best_cost_el = document.getElementById('best_cost<?= $tours_result_array[$i]['package_id'] ?>');
        var best_cost_cid_el = document.getElementById('best_cost_cid<?= $tours_result_array[$i]['package_id'] ?>');
        
        if (best_cost_el && best_cost_cid_el) {
          best_cost_el.innerHTML = '<?php echo sprintf("%.2f", $discounted_cost); ?>';
          best_cost_cid_el.innerHTML = '<?= $h_currency_id ?>';
          
          // Show old price if original cost is different from discounted cost
          var original_cost = parseFloat(<?= $original_cost ?>);
          var discounted_cost_val = parseFloat(<?= $discounted_cost ?>);
          
          if (original_cost != discounted_cost_val) {
            var old_price_el = document.getElementById('old_price<?= $tours_result_array[$i]['package_id'] ?>');
            var price_sub_el = document.getElementById('price_sub<?= $tours_result_array[$i]['package_id'] ?>');
            var price_subid_el = document.getElementById('price_subid<?= $tours_result_array[$i]['package_id'] ?>');
            
            if (old_price_el && price_sub_el && price_subid_el) {
              old_price_el.classList.remove('c-hide');
              price_sub_el.innerHTML = '<?php echo sprintf("%.2f", $original_cost); ?>';
              price_subid_el.innerHTML = '<?= $h_currency_id ?>';
            }
          } else {
            var old_price_el = document.getElementById('old_price<?= $tours_result_array[$i]['package_id'] ?>');
            if (old_price_el) {
              old_price_el.style.display = 'none';
            }
          }
          
          // Offer red strip display - handle immediately for percentage offers
          <?php if ($show_offer && $offer_text != '') { ?>
          <?php if ($offer_price_flag == 'percentage') { ?>
          // Immediately hide currency icon and price for percentage offers
          (function() {
            var discountEl = document.getElementById("discount<?= $tours_result_array[$i]['package_id'] ?>");
            if (discountEl) {
              var discountTextEl = discountEl.querySelector('.discount-text');
              var offerPriceEl = document.getElementById("offer-currency-price<?= $tours_result_array[$i]['package_id'] ?>");
              var offerIcon = discountTextEl ? discountTextEl.querySelector('.currency-icon') : null;
              var discountTextSpan = document.getElementById("discount_text<?= $tours_result_array[$i]['package_id'] ?>");
              
              if (offerPriceEl) {
                offerPriceEl.style.display = 'none';
                offerPriceEl.style.visibility = 'hidden';
                offerPriceEl.innerHTML = '';
              }
              if (offerIcon) {
                offerIcon.style.display = 'none';
                offerIcon.style.visibility = 'hidden';
              }
              if (discountTextSpan) {
                discountTextSpan.className = '';
                discountTextSpan.style.marginLeft = '0';
                discountTextSpan.innerHTML = '<?= $offer_text ?>';
              }
            }
          })();
          <?php } ?>
          
          setTimeout(function() {
            var discountEl = document.getElementById("discount<?= $tours_result_array[$i]['package_id'] ?>");
            if (discountEl) {
              var discountTextEl = discountEl.querySelector('.discount-text');
              discountEl.classList.remove("c-hide");
              discountEl.classList.add("c-show");
              var offerCurrencyIdEl = document.getElementById("offer-currency-id<?= $tours_result_array[$i]['package_id'] ?>");
              var offerCurrencyFlagEl = document.getElementById("offer-currency-flag<?= $tours_result_array[$i]['package_id'] ?>");
              var offerPriceEl = document.getElementById("offer-currency-price<?= $tours_result_array[$i]['package_id'] ?>");
              var offerIcon = discountTextEl ? discountTextEl.querySelector('.currency-icon') : null;
              var discountTextSpan = document.getElementById("discount_text<?= $tours_result_array[$i]['package_id'] ?>");
              
              if (offerCurrencyIdEl) offerCurrencyIdEl.innerHTML = '<?= $offer_currency_id_val ?>';
              if (offerCurrencyFlagEl) offerCurrencyFlagEl.innerHTML = '<?= $offer_price_flag ?>';
              
              // Handle percentage offers like hotels
              <?php if ($offer_price_flag == 'percentage') { ?>
              if (offerPriceEl) {
                offerPriceEl.style.display = 'none';
                offerPriceEl.style.visibility = 'hidden';
                offerPriceEl.innerHTML = ''; // Clear any content
              }
              if (offerIcon) {
                offerIcon.style.display = 'none';
                offerIcon.style.visibility = 'hidden';
              }
              if (discountTextSpan) {
                discountTextSpan.className = '';
                discountTextSpan.style.marginLeft = '0';
                discountTextSpan.style.display = 'inline-block';
                discountTextSpan.innerHTML = '<?= $offer_text ?>';
              }
              <?php } else { ?>
              if (offerPriceEl) {
                offerPriceEl.style.display = 'inline-block';
                // Set initial value from data-amount if available
                var dataAmount = offerPriceEl.getAttribute('data-amount');
                if (dataAmount) {
                  offerPriceEl.innerHTML = parseFloat(dataAmount).toFixed(2);
                }
                // Currency conversion function will update it if currency changes
              }
              if (offerIcon) {
                offerIcon.style.display = 'inline-block';
              }
              if (discountTextSpan) {
                discountTextSpan.className = 'ml-5px';
                discountTextSpan.style.display = 'inline-block';
                discountTextSpan.innerHTML = '<?= addslashes($offer_text) ?>';
              }
              <?php } ?>
            }
          }, 100);
          <?php } ?>
        }
      }, 100);
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

      //Tours Offer Cost
      var offer_price_list = document.querySelectorAll(".offer-currency-price");
      var offer_price_id = document.querySelectorAll(".offer-currency-id");
      var offer_price_flag = document.querySelectorAll(".offer-currency-flag");
      var offerAmount_arr = [];
      for (var i = 0; i < offer_price_id.length; i++) {
        var flag = offer_price_flag[i] ? offer_price_flag[i].innerHTML : 'no';
        var id = offer_price_id[i] ? offer_price_id[i].innerHTML.trim() : '';
        // Get amount from data-amount attribute (original amount in tour currency)
        var amount = 0;
        var packageId = '';
        if (offer_price_list[i]) {
          var dataAmount = offer_price_list[i].getAttribute('data-amount');
          if (dataAmount) {
            amount = parseFloat(dataAmount) || 0;
          }
          // Extract package_id from element ID (format: offer-currency-price{package_id})
          var elementId = offer_price_list[i].id;
          if (elementId) {
            var match = elementId.match(/offer-currency-price(\d+)/);
            if (match && match[1]) {
              packageId = match[1];
            }
          }
        }
        if (amount > 0 && flag != 'percentage' && id != 'PERCENT' && id != '' && packageId != '') {
          offerAmount_arr.push({
            'amount': amount,
            'id': id,
            'flag': flag,
            'package_id': packageId
          });
        }
      }
      if (offerAmount_arr.length > 0) {
        sessionStorage.setItem('tours_offer_price_list', JSON.stringify(offerAmount_arr));
      }

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
  
  // Function to ensure percentage offers stay hidden
  function hidePercentageOffers() {
    var allDiscountEls = document.querySelectorAll('.c-discount[data-offer-type="percentage"]');
    allDiscountEls.forEach(function(discountEl) {
      var currencyIcon = discountEl.querySelector('.currency-icon');
      var offerPrice = discountEl.querySelector('.offer-currency-price');
      if (currencyIcon) {
        currencyIcon.style.display = 'none';
        currencyIcon.style.visibility = 'hidden';
        currencyIcon.innerHTML = '';
      }
      if (offerPrice) {
        offerPrice.style.display = 'none';
        offerPrice.style.visibility = 'hidden';
        offerPrice.innerHTML = '';
      }
    });
    
    // Also check by flag element
    var allOfferFlags = document.querySelectorAll('.offer-currency-flag');
    allOfferFlags.forEach(function(flagEl) {
      if (flagEl.innerHTML.trim() === 'percentage') {
        var discountText = flagEl.closest('.discount-text');
        if (discountText) {
          var currencyIcon = discountText.querySelector('.currency-icon');
          var offerPrice = discountText.querySelector('.offer-currency-price');
          if (currencyIcon) {
            currencyIcon.style.display = 'none';
            currencyIcon.style.visibility = 'hidden';
            currencyIcon.innerHTML = '';
          }
          if (offerPrice) {
            offerPrice.style.display = 'none';
            offerPrice.style.visibility = 'hidden';
            offerPrice.innerHTML = '';
          }
        }
      }
    });
  }
  
  // Run immediately
  hidePercentageOffers();
  
  // Run after a short delay
  setTimeout(hidePercentageOffers, 100);
  setTimeout(hidePercentageOffers, 500);
  setTimeout(hidePercentageOffers, 1000);
  setTimeout(hidePercentageOffers, 2000);
  
  // Run after currency conversion (if tours_page_currencies is called)
  var originalToursPageCurrencies = window.tours_page_currencies;
  if (typeof originalToursPageCurrencies === 'function') {
    window.tours_page_currencies = function() {
      var result = originalToursPageCurrencies.apply(this, arguments);
      setTimeout(hidePercentageOffers, 100);
      return result;
    };
  }
  
  // Also run on currency change events
  document.addEventListener('currencyChanged', function() {
    setTimeout(hidePercentageOffers, 100);
  });
</script>