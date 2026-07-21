<?php
require_once('../../config.php');

// if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
//     $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
//   header('Location: ../../index.php');
// }

// List of country calling codes
$mobileCodes = [
    "+1" => "United States/Canada",
    "+7" => "Russia/Kazakhstan",
    "+20" => "Egypt",
    "+27" => "South Africa",
    "+30" => "Greece",
    "+31" => "Netherlands",
    "+32" => "Belgium",
    "+33" => "France",
    "+34" => "Spain",
    "+39" => "Italy",
    "+40" => "Romania",
    "+44" => "United Kingdom",
    "+49" => "Germany",
    "+52" => "Mexico",
    "+55" => "Brazil",
    "+61" => "Australia",
    "+64" => "New Zealand",
    "+81" => "Japan",
    "+82" => "South Korea",
    "+86" => "China",
    "+91" => "India",
    "+92" => "Pakistan",
    "+93" => "Afghanistan",
    "+94" => "Sri Lanka",
    "+95" => "Myanmar",
    "+98" => "Iran",
    "+211" => "South Sudan",
    "+212" => "Morocco",
    "+213" => "Algeria",
    "+234" => "Nigeria",
    "+251" => "Ethiopia",
    "+254" => "Kenya",
    "+255" => "Tanzania",
    "+256" => "Uganda",
    "+260" => "Zambia",
    "+263" => "Zimbabwe"
    // Add more codes as needed
];

?>
<?php
        $_SESSION['search_data'] = $_GET;
        // Extract incoming POST data (assumed to be sent via AJAX)
        $flight_id=isset($_GET['flightid']) ? $_GET['flightid'] : header("Location: /flightapi");
        $searchType = isset($_GET['searchType']) ? $_GET['searchType'] : 'oneway';
        $order = isset($_GET['order']) ? $_GET['order'] : 'price-asc';
        $travelClass = isset($_GET['travelClass']) ? $_GET['travelClass'] : 'Economy';
        $adult = isset($_GET['adult']) ? $_GET['adult'] : 1;
        $child = isset($_GET['child']) ? $_GET['child'] : 0;
        $infant = isset($_GET['infant']) ? $_GET['infant'] : 0;
        $from = isset($_GET['from']) ? $_GET['from'] : null;
        $to = isset($_GET['to']) ? $_GET['to'] : null;
        $departureDate = isset($_GET['departureDate']) ? $_GET['departureDate'] : null;
        $returnDate = isset($_GET['returnDate']) ? $_GET['returnDate'] : null;
        
        $datareview=[];
        $flight_ids=explode(",",$flight_id);
        foreach ($flight_ids as $group => $value) 
        {
            $datareview['priceIds'][]=$value;
        }
       
        $resultreview = callAPI('POST', 'https://apitest.tripjack.com/fms/v1/review', json_encode($datareview));
        $resultreviewArr = json_decode($resultreview, true);
        
        $_SESSION['flight_review_data'] = $resultreview;
        // print_r($resultreview);
        // die;
        if( isset($resultreviewArr['status']['success'] ) && $resultreviewArr['status']['httpStatus']=200 && $resultreviewArr['status']['success']==true) 
        {
            
            foreach($resultreviewArr['searchQuery']['routeInfos'] as $routeInfos)
            {
                $fromCityOrAirport=$routeInfos['fromCityOrAirport'];
                $toCityOrAirport=$routeInfos['toCityOrAirport'];
                $travelDate=$routeInfos['travelDate'];
            }
            
        }
        else
        {
            
            // Get the current URL
            $currentUrl = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            
            // Parse the URL into components
            $parsedUrl = parse_url($currentUrl);
            
            // Parse the query string into an associative array
            parse_str($parsedUrl['query'], $queryParams);
            
            // Remove the 'flight_id' parameter
            unset($queryParams['flight_id']);
            
            // Replace 'guest-details.php' with 'list.php' in the path
            $parsedUrl['path'] = str_replace('guest-details.php', 'list.php', $parsedUrl['path']);
            
            // Build the new query string without 'flight_id'
            $newQuery = http_build_query($queryParams);
            
            // Reconstruct the URL with the updated path and query
            $newUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'] . '?' . $newQuery;
            
            // Redirect to the new URL
            header("Location: $newUrl");
            exit;


        }
        //echo json_encode($resultreviewArr, JSON_PRETTY_PRINT);
        //print_r($resultreviewArr);
include '../../layouts/header2.php';
?>
 <style>
        .selector .error-message
        {
            position: relative;
            top: 42px;
        }
        .selector.st-clean span.c-custom-select{
            display:none;
        }
        
        
    </style>
<!-- ***** Banner Section ***** -->
<!--<section class="c-bannerAndFilter with-image" style="background-image: url('./images/banner.png');padding: 35px 0 140px 0;">-->
<!--  <div class="container-lg">-->
<!--    <div class="row align-items-center">-->
<!--      <div class="col-12">-->
<!--        <div class="banner_one_text text-center">-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!--</section>-->
<!-- ***** Banner Section End ***** -->
			</div>
			<!-- ********** Component :: Header End ********** -->

      <!-- ********** Component :: Page Title End ********** -->

      <!-- ********** Component :: Hotel Listing  ********** -->
      <div class="c-containerDark">
        <div class="container">
          <div class="row">
            <!-- ***** Hotel Listing ***** -->
            <div class="col-md-8 col-sm-12">

              <!-- **** Shopping Cart Detail ****  -->
              <div class="c-cartContainer">
                <div class="cartHeading">Flight Details</div>
                <div class="cartBody">
                  <!-- **** Shopping Cart Item ****  -->
                  <div class="cartItem" style="padding-left: 40px;">
                    <div class="row">

                    <?php
                    //print_r($resultreviewArr);
                    
                    foreach ($resultreviewArr['tripInfos'] as $tripInfo) {
                        // Check if 'sI' exists in the current 'tripInfo' array
                        if (isset($tripInfo['sI']) && is_array($tripInfo['sI'])) {
                            foreach ($tripInfo['sI'] as $tripInfossI) {
                            
                        ?>
                            <!-- **** Cart Data ****  -->
                            <div class="col-12 p0-left" style="padding-top: 20px;">
                                <div class="cartInfo" style="display: flex;justify-content: space-between;">
                                  <!-- **** Cart Header ****  -->
                                  <div class="cartHeader">
                                    <span class="itemTitle"><?=$tripInfossI['da']['city']?>(<?=$tripInfossI['da']['code']?>) - <?=$tripInfossI['aa']['city']?>(<?=$tripInfossI['aa']['code']?>) </span>
                                    <!--<button class="deleteHotel"></button>-->
                                    
        
                                    <div class="infoSection">
                                      <span class="cardInfoLine cust">
                                        <i class="icon it itours-calendar"></i>
                                        Departure Date:
                                        <strong><?php $date = new DateTime($tripInfossI['dt']); echo $date->format('d-m-Y, h:i A');?></strong>
                                      </span>
                                      <span class="cardInfoLine cust">
                                        <i class="icon it itours-calendar"></i>
                                        Arrival Date:
                                        <strong><?php $date = new DateTime($tripInfossI['at']); echo $date->format('d-m-Y, h:i A');?></strong>
                                      </span>
                                    </div>
                                  </div>
                                  <div>
                                      <?php
                                    //   print_r($tripInfo['totalPriceList'][0]['fd']['ADULT'])
                                    if($tripInfo['totalPriceList'][0]['fd']['ADULT']['cc'])
                                    {
                                      ?>
                                      <span><?=$tripInfo['totalPriceList'][0]['fd']['ADULT']['cc']?></span>
                                     <?php
                                    }
                                    ?>
                                  </div>
                                  <!-- **** Cart Header End ****  -->
                                </div>
                              </div>
                            <!-- **** Cart Data End ****  -->
                        <?php
                    } }}
                    
                    ?>
                     
					  
					 
                    </div>
                  </div>
                  <!-- **** Shopping Cart Item End ****  -->
                </div>
              </div>
              <!-- **** Shopping Cart Detail End ****  -->



              <!-- ***** Traveller Info Section ***** -->
              <div class="c-cartContainer">
                <div class="cartHeading">Travellers Details</div>
                <div class="cartBody">
                  <form id="guestForm">
                    <input type="hidden" value="<?=$resultreviewArr['bookingId']?>" name="bookingId">
                    <input type="hidden" value="<?=$resultreviewArr['totalPriceInfo']['totalFareDetail']['fC']['TF']?>" name="totalFare">
                
                    <div class="c-travellerDtls">
                      <!-- Existing code for travellers -->
                      <!-- ***** Room Row ***** -->
                    <div class="section">
                      <div class="title">Enter Guest Name</div>
                    <?php
                    for($a=1;$a<=$adult;$a++)
                    {
                    ?>
                      <!-- ***** Info Row ***** -->
                      <div class="row c-infoRow">
                        <div class="col-md-1 col-sm-6" style="padding: 0px 5px;">
                          <label>Adult <?=$a?>: </label>
                        </div>
                        <div class="col-md-2 col-sm-6">
                          <div class="selector st-clean">
                           <select class="full-width js-advanceSelect" name="ti[]" required>
                              <option value="">Select</option>
                              <option value="Mr">Mr</option>
                              <option value="Mrs">Mrs</option>
                              <option value="Ms">Ms</option>
                            </select>
                          </div>
                        </div>
                        <input type="hidden" name="pt[]" value="ADULT" class="infoRow_txtbox" required />  
                        <div class="col-md-3 col-sm-6">
                          <input type="text" name="fN[]" class="infoRow_txtbox" placeholder="Enter Name" required />
                        </div>
                        <div class="col-md-3 col-sm-6">
                          <input type="text" name="lN[]" class="infoRow_txtbox" placeholder="Last Name" required />
                        </div>
                        <div class="col-md-3 col-sm-6 ">
                          <input type="date" name="dob[]" class="infoRow_txtbox adultdob dobval" placeholder="Date of Birth"  />
                        </div>
                      </div>
                      <!-- ***** Info Row End ***** -->
					<?php
                    }
                    for($c=1;$c<=$child;$c++)
                    {
                    ?>
                      <!-- ***** Info Row ***** -->
                      <div class="row c-infoRow">
                        <div class="col-md-1 col-sm-6" style="padding: 0px 5px;">
                          <label>Child <?=$c?>: </label>
                        </div>
                        <div class="col-md-2 col-sm-6">
                          <div class="selector st-clean">
                            <select class="full-width js-advanceSelect" name="ti[]" required>
                              <option value="">Select</option>
                              <option value="Ms">Ms</option>
                              <option value="Master">Master</option>
                            </select>
                          </div>
                        </div>
                        <input type="hidden" name="pt[]" value="CHILD" class="infoRow_txtbox" required />  
                        <div class="col-md-3 col-sm-6">
                          <input type="text" name="fN[]" class="infoRow_txtbox" placeholder="Enter Name" required />
                        </div>
                        <div class="col-md-3 col-sm-6">
                          <input type="text" name="lN[]" class="infoRow_txtbox" placeholder="Last Name" required />
                        </div>
                        <div class="col-md-3 col-sm-6">
                          <input type="date" name="dob[]" class="infoRow_txtbox childdob dobval" placeholder="Date of Birth"  />
                        </div>
                      </div>
                      <!-- ***** Info Row End ***** -->
					<?php
                    }
                    for($i=1;$i<=$infant;$i++)
                    {
                    ?>
                      <!-- ***** Info Row ***** -->
                      <div class="row c-infoRow">
                        <div class="col-md-1 col-sm-6" style="padding: 0px 5px;">
                          <label>Infant <?=$i?>: </label>
                        </div>
                        <div class="col-md-2 col-sm-6">
                          <div class="selector st-clean">
                            <select class="full-width js-advanceSelect" name="ti[]" required>
                              <option value="">Select</option>
                              <option value="Ms">Ms</option>
                              <option value="Master">Master</option>
                            </select>
                          </div>
                        </div>
                        <input type="hidden" name="pt[]" value="INFANT" class="infoRow_txtbox" required />  
                        <div class="col-md-3 col-sm-6">
                          <input type="text" name="fN[]" class="infoRow_txtbox" placeholder="Enter Name" required />
                        </div>
                        <div class="col-md-3 col-sm-6">
                          <input type="text" name="lN[]" class="infoRow_txtbox" placeholder="Last Name" required />
                        </div>
                        <div class="col-md-3 col-sm-6">
                          <input type="date" name="dob[]" class="infoRow_txtbox infantdob dobval" placeholder="Date of Birth" required />
                        </div>
                      </div>
                      <!-- ***** Info Row End ***** -->
					<?php
                    }            
                    ?>
                    </div>
                    <!-- ***** Room Row End ***** -->
                      <!-- Contact Person Section -->
                      <div class="section">
                        <div class="title">Contact Person</div>
                        <div class="row c-infoRow">
                          <div class="col-md-2 col-sm-6">
                            <div class="selector st-clean">
                              <select class="full-width js-advanceSelect" name="contact_country_code">
                                <option value="">Country Code</option>
                                <?php
                                foreach ($mobileCodes as $code => $country) {
                                    if($code=='+91'){
                                        $select='selected';
                                    }else{
                                         $select='';
                                    }
                                  echo '<option value="' . htmlspecialchars($code) . '" '.$select.'>' . $code . ' - ' . htmlspecialchars($country) . '</option>';
                                }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-3 col-sm-6">
                            <input type="text" class="infoRow_txtbox" id="contact_mobile" name="contact_mobile" placeholder="Enter Contact Number" />
                          </div>
                          <div class="col-md-3 col-sm-6">
                            <input type="text" class="infoRow_txtbox" id="contact_email" name="contact_email" placeholder="Enter Email ID" />
                          </div>
                          <div class="col-md-4 col-sm-6">
                            <input type="text" class="infoRow_txtbox" id="contact_name" name="contact_name" placeholder="Enter Name" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>

              </div>
              <!-- ***** Traveller Info Section End ***** -->

            </div>
            <!-- ***** Hotel Listing End ***** -->

            <!-- ***** Pricing ***** -->
            <div class="col-md-4 col-sm-12">
              <div class="c-cartContainer cartPricing">
                <div class="sTitle">Final Pricing</div>
                <?php
                $totalPayAmount=0;
                foreach ($resultreviewArr['tripInfos'] as $tripInfo) {
                
                // Loop through the 'totalPriceList' for each 'tripInfo'
                foreach ($tripInfo['totalPriceList'] as $tripInfostotalPriceList) {
                
                    $fairDetails=$tripInfostotalPriceList['fd'];
                   // print_r($fairDetails);
                    
                    foreach($fairDetails as $fdindex => $fd)
                    {
                        
                        if($fdindex=="CHILD")
                        {
                            $prcount=$child;
                        }
                        elseif($fdindex=="INFANT")
                        {
                            $prcount=$infant;
                        }
                        else
                        {
                            $prcount=$adult;
                        }
                        $totalPayAmount+=$fd['fC']['TF']*$prcount;
                        ?>
                        <!-- **** Pricing Block **** -->
                        <div class="sBlock">
                          <span class="sBlock_title"><?=$prcount?> <?=$fdindex?></span>
                          <div class="row sBlock_price">
                            <div class="col-4">
                              <span class="pLabel">Price: </span>
                            </div>
                            <div class="col-8">
                              <span class="pLabel cost"><span class="p_currency">₹</span> <?=$fd['fC']['BF']*$prcount ?? 0;?></span>
                            </div>
                          </div>
                          <div class="row sBlock_price">
                            <div class="col-4">
                              <span class="pLabel">Tax: </span>
                            </div>
                            <div class="col-8">
                              <span class="pLabel cost"><span class="p_currency">₹</span> <?=$fd['fC']['TAF']*$prcount?? 0;?></span>
                            </div>
                          </div>
                          <div class="row sBlock_price cDivider">
                            <div class="col-4">
                              <span class="pLabel">Total: </span>
                            </div>
                            <div class="col-8">
                              <span class="pLabel cost colWhite"><span class="p_currency">₹</span> <?=$fd['fC']['TF'] ?? 0;?></span>
                            </div>
                          </div>
                        </div>
                        <!-- **** Pricing Block End **** -->
                        <?php
                    }
                }
                }
                ?>
                <!-- **** Pricing Block **** -->
                <div class="sBlock gTotal">
                  <div class="row sBlock_price">
                    <div class="col-6">
                      <span class="pLabel">Grand Total: </span>
                    </div>
                    <div class="col-6">
                      <span class="pLabel cost colWhite"><span class="p_currency">₹</span> <?=$totalPayAmount?></span>
                    </div>
                  </div>
                </div>
                <!-- **** Pricing Block End **** -->

                <!-- **** Promo code Notifications **** -->
               <!-- <div class="c-promoNotify st-success">
                  <span>Promocode Applied Successfully</span>
                  <span class="p_code">Promocode</span>
                  <button class="removePromo">Remove</button>
                </div>-->
                <!-- **** Promo code Notifications End **** -->

                <!-- **** Promo code Notifications **** -->
                <!--<div class="c-promoNotify st-fail">
                  <span>Invalid Promocode</span>
                  <button class="removePromo">Close</button>
                </div>-->
                <!-- **** Promo code Notifications End **** -->


                <!-- **** Promo Code Block **** -->
                <!--<div class="sPromocode">
                  <span class="sLabel">Have promo code.?</span>
                  <div class="row">
                    <div class="col-8">
                      <input
                        type="text"
                        class="promoTxt"
                        placeholder="Enter Promocode"
                      />
                    </div>
                    <div class="col-4">
                      <button class="promoBtn">Apply</button>
                    </div>
                  </div>
                </div>-->
                <!-- **** Promo Code Block End **** -->

                <button class="btnCheckout">Proceed to Checkout</button>
              </div>
            </div>
            <!-- ***** Pricing End ***** -->
          </div>





        </div>
      </div>
      <!-- ********** Component :: Hotel Listing End ********** -->

      <!-- ********** Component :: Footer ********** -->
      	<?php include '../../layouts/footer2.php'; ?>
      <!-- ********** Component :: Footer End ********** -->
    </div>

   
    <!-- Preloader HTML -->
    <div id="preloader" class="preloader">
        <!--<div class="spinner"></div>-->
        <p>Processing your booking, please wait...</p>
    </div>

    <!-- Javascript -->
    <!-- Javascript -->
    <script type="text/javascript" src="../../js2/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="../../js2/jquery-ui.1.10.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript" src="../../js2/popper.min.js"></script>
    <script type="text/javascript" src="../../js2/bootstrap-4.min.js"></script>
    <script type="text/javascript" src="../../js2/theme-scripts.js"></script>
       <script src="./js/bootstrap-datepicker.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
     <script type="text/javascript" src="../../js2/select2.min.js"></script>
      <!--<script type="text/javascript" src="../../js2/scripts.js"></script>-->
    <script>
    
    $(document).ready(function () {
    
    $(document).on('click','.btnCheckout', function (e) {
        e.preventDefault(); // Prevent default button behavior
        // Custom validation
        const isValid = validateGuestForm();
        if (isValid) {
            // Disable the button and show loader
            $('.btnCheckout').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
            // Gather form data
            let formData = $('#guestForm').serialize();
            // Submit form via AJAX
            $.ajax({
                url: 'api/air_book.php', // Replace with your server endpoint
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if(response.status == 'error') 
                    {
                        alert(response.message);
                        
                        $('.btnCheckout').prop('disabled', false).html('Proceed to Checkout');
                        
                    } 
                    else if(response.status == 'success')
                    {
                        submitRazorpay(response);
                        //alert(response);
                        console.log(response);
                        $('.btnCheckout').prop('disabled', false).html('Proceed to Checkout');
                    }
                    else
                    {
                        alert("An error occurred. Please try again.");
                        console.error(xhr.responseText);
                        $('.btnCheckout').prop('disabled', false).html('Proceed to Checkout');
                    }
                },
                error: function (xhr, status, error) {
                    alert("An error occurred. Please try again.");
                    console.error(xhr.responseText);
                    $('.btnCheckout').prop('disabled', false).html('Proceed to Checkout');
                },
                complete: function () {
                    // Re-enable the button and reset the text
                    $('.btnCheckout').prop('disabled', false).html('Proceed to Checkout');
                }
            });
        }
    });

    function submitRazorpay(GetResponse) {
            var options = {
                "key": "<?=$apiKey?>", // Razorpay key ID
                "amount": GetResponse.amount * 100, // Amount in paise
                "currency": "INR",
                "name": "Flight Booking",
                "description": "Flight Booking Payment",
                "order_id": GetResponse.orderId, // Order ID from Razorpay
                "handler": function (response) {
                    // Send payment details to the server for verification
                    fetch("api/verify_payment.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({
                            paymentId: response.razorpay_payment_id,
                            orderId: response.razorpay_order_id,
                            signature: response.razorpay_signature,
                            bookingId: GetResponse.bookingId,
                            amount: GetResponse.amount
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                // Call bookingConfirm() and pass necessary data
                                bookingConfirm({
                                    paymentId: response.razorpay_payment_id,
                                    bookingId: GetResponse.bookingId,
                                    orderId: response.razorpay_order_id,
                                    amount: GetResponse.amount,
                                });
                            } else {
                                alert("Payment Verification Failed!");
                            }
                        })
                        .catch(error => {
                            console.error("Error verifying payment:", error);
                            alert("An error occurred during payment verification.");
                        });
                },
                "prefill": {
                    "name": GetResponse.contact_name,
                    "email": GetResponse.contact_email,
                    "contact": GetResponse.contact_country_code + GetResponse.contact_mobile
                },
                "theme": {
                    "color": "#d67f76"
                }
            };
            var rzp1 = new Razorpay(options);
        
            // Open Razorpay payment popup
            rzp1.open();
        
            // Prevent default action (if used in a form submission)
            event.preventDefault();
        }
        //document.getElementById('preloader').style.display = 'block';
    function bookingConfirm(data) {
        document.getElementById('preloader').style.display = 'block';
            // Send the booking confirmation details to the server
            fetch("api/confirm_booking.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data) // Send the data received from Razorpay
            })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('preloader').style.display = 'none';
                    if (data.status === "success") {
                        alert("Booking Confirmed!");
                        // Redirect or perform any post-confirmation actions
                        window.location.href = "booking_success.php?bookingId=" + data.bookingId;
                    } else {
                        alert("Booking Confirmation Failed: " + data.message);
                    }
                })
                .catch(error => {
                    document.getElementById('preloader').style.display = 'none';
                    console.error("Error confirming booking:", error);
                    alert("An error occurred during booking confirmation.");
                });
        }

    // Custom validation function
    function validateGuestForm() {
        let isValid = true;

        // Reset previous error messages
        $('.error-message').remove();

        // Validate titles
        $('select[name="ti[]"]').each(function () {
            if (!$(this).val()) {
                showError($(this), "Please select a title.");
                isValid = false;
            }
        });

        // Validate first names
        $('input[name="fN[]"]').each(function () {
            if (!$(this).val() || $(this).val().length < 1) {
                showError($(this), "First name is required.");
                isValid = false;
            }
        });

        // Validate last names
        $('input[name="lN[]"]').each(function () {
            if (!$(this).val() || $(this).val().length < 1) {
                showError($(this), "Last name is required.");
                isValid = false;
            }
        });
   /* $('.infantdob').each(function () {
            if (!$(this).val()) {
                showError($(this), "Date of birth is required.");
                isValid = false;
            }
        });*/
        // Validate dates of birth
          $('.childdob').each(function () {
    var dobValue = $(this).val();
    
    // Check if the date is empty
    if (!dobValue) {
        showError($(this), "Date of birth is required.");
        isValid = false;
    } else {
        // Split the date in dd-mm-yyyy format
        var dateParts = dobValue.split('-');
        var year = dateParts[0];
        var month = dateParts[1] - 1; // JavaScript months are zero-indexed
        var day = dateParts[2];
          console.log(dateParts);
        // Create a new date object in yyyy-mm-dd format
        var dob = new Date(year, month, day);
          
        var today = new Date();
        var age = today.getFullYear() - dob.getFullYear();
        var monthDiff = today.getMonth() - dob.getMonth();
        
        // If the birthday hasn't occurred yet this year, subtract 1 year from the age
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        if (age < 2 || age > 12) {
            showError($(this), "The child should be between 2 and 12 years old.");
            isValid = false;
        }
    }
});
    
         // Validate dates of birth
          $('.adultdob').each(function () {
    var dobValue = $(this).val();
    
    // Check if the date is empty
    if (!dobValue) {
        showError($(this), "Date of birth is required.");
        isValid = false;
    } else {
        // Split the date in dd-mm-yyyy format
        var dateParts = dobValue.split('-');
        var year = dateParts[0];
        var month = dateParts[1] - 1; // JavaScript months are zero-indexed
        var day = dateParts[2];
          console.log(dateParts);
        // Create a new date object in yyyy-mm-dd format
      
        var dob = new Date(year, month, day);
          
        var today = new Date();
        var age = today.getFullYear() - dob.getFullYear();
        var monthDiff = today.getMonth() - dob.getMonth();
        
        // If the birthday hasn't occurred yet this year, subtract 1 year from the age
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

         if(age <= 11){
            showError($(this), "Adult age should be above 12 years old.");
            isValid = false;
        }
    }
});

  // Validate dates of birth
          $('.infantdob').each(function () {
    var dobValue = $(this).val();
    
    // Check if the date is empty
    if (!dobValue) {
        showError($(this), "Date of birth is required.");
        isValid = false;
    } else {
        // Split the date in dd-mm-yyyy format
        var dateParts = dobValue.split('-');
        var year = dateParts[0];
        var month = dateParts[1] - 1; // JavaScript months are zero-indexed
        var day = dateParts[2];
          console.log(dateParts);
        // Create a new date object in yyyy-mm-dd format
        var dob = new Date(year, month, day);
          
        var today = new Date();
        var age = today.getFullYear() - dob.getFullYear();
        var monthDiff = today.getMonth() - dob.getMonth();
        
        // If the birthday hasn't occurred yet this year, subtract 1 year from the age
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        // If age is 2 or more years old, show error
        if (age >= 2) {
            showError($(this), "The child should be under 2 years old to be considered an infant.");
            isValid = false;
        }
    }
});
        // Validate contact country code
        const countryCode = $('select[name="contact_country_code"]');
        if (!countryCode.val()) {
            showError(countryCode, "Select code.");
            isValid = false;
        }

        // Validate contact mobile
        const mobile = $('input[name="contact_mobile"]');
        const mobileValue = mobile.val();
        if (!mobileValue || !/^\d{10,15}$/.test(mobileValue)) {
            showError(mobile, "Mobile number must be 10 digits.");
            isValid = false;
        }

        // Validate contact email
        const email = $('input[name="contact_email"]');
        const emailValue = email.val();
        if (!emailValue || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
            showError(email, "Please enter a valid email address.");
            isValid = false;
        }

        // Validate contact name
        const contactName = $('input[name="contact_name"]');
        if (!contactName.val()) {
            showError(contactName, "Contact name is required.");
            isValid = false;
        }

        return isValid;
    }

    // Helper function to display error messages
    function showError(element, message) {
        const errorMessage = $('<span class="error-message" style="color:red; font-size:12px;">' + message + '</span>');
        element.after(errorMessage);
    }

    // Remove error message dynamically on input
    $(document).on('input change', 'input, select', function () {
        const element = $(this);
        if (element.next('.error-message').length) {
            element.next('.error-message').remove();
        }
    });
    
    $(".js-advanceSelect").select2();
});

// Set the max attribute of the date input to the current date
document.querySelectorAll('.dobval').forEach(function(input) {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); // Month is 0-based
    var yyyy = today.getFullYear();

    // Format the date to yyyy-mm-dd
    var currentDate = yyyy + '-' + mm + '-' + dd;

    // Set the max attribute for the input field
    input.setAttribute('max', currentDate);
});

   
    </script>
  </body>
</html>
