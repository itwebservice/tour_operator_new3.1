    <button id="scrollToTopBtn" title="Go to top">
        ↑
    </button>
    <style>
        /* scrolling bar css  */

        #scrollToTopBtn {
            position: fixed;
            bottom: 40px;
            right: 40px;
            z-index: 99;
            background-color: #555;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            display: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        #scrollToTopBtn:hover {
            background-color: #333;
        }
    </style>

    <!-- Footer Start -->
    <footer class="t-footer">
        <div class="container">
            <div class="t-footer-content">
                <div class="t-footer-contact">
                    <div class="row">
                        <div class="col col-12 col-md-6 col-lg-3 col-xl-3">
                            <div class="t-footer-contact-list">
                                <h5 class="t-footer-contact-title">Address & Contact Info</h5>
                                <p class="t-footer-contact-discription mb-2"><?= $app_address ?></p>
                                <h5 class="t-footer-contact-title mb-0">Phone: <?= $app_contact_no ?></h5>
                            </div>
                        </div>
                        <?php
                        if (!empty($Apifooter)) {
                        ?>
                            <div class="col col-12 col-md-6 col-lg-3 col-xl-3">
                                <div class="t-footer-contact-list">
                                    <h5 class="t-footer-contact-title">Holiday Tour & Travels</h5>
                                    <ul class="t-footr-help-list">
                                        <?php foreach ($Apifooter as $footer) { ?>
                                            <li class="t-footer-help-item">
                                                <a target="_blank" onclick="get_tours_data('<?= $footer->dest_id ?>','1')" class="t-footer-help-link t-footer-contact-discription" style="cursor:pointer!important;"><?= $footer->package_name ?></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="col col-12 col-md-6 col-lg-3 col-xl-3">
                            <div class="t-footer-contact-list">
                                <h5 class="t-footer-contact-title">Important Links</h5>
                                <div class="t-footer-help">
                                    <ul class="t-footr-help-list">
                                        <li class="t-footer-help-item">
                                            <a target="_blank" href="<?= BASE_URL_B2C ?>about.php" class="t-footer-help-link t-footer-contact-discription">About Us</a>
                                        </li>
                                        <li class="t-footer-help-item">
                                            <a target="_blank" href="<?= BASE_URL_B2C ?>award.php" class="t-footer-help-link t-footer-contact-discription">Awards</a>
                                        </li>
                                        <li class="t-footer-help-item">
                                            <a target="_blank" href="<?= BASE_URL_B2C ?>blog.php" class="t-footer-help-link t-footer-contact-discription">Travel Blog</a>
                                        </li>
                                        <li class="t-footer-help-item">
                                            <a target="_blank" href="<?= BASE_URL_B2C ?>refund-policy.php" class="t-footer-help-link t-footer-contact-discription">Refund Policy</a>
                                        </li>
                                        <li class="t-footer-help-item">
                                            <a target="_blank" href="<?= BASE_URL_B2C ?>terms-conditions.php" class="t-footer-help-link t-footer-contact-discription">Terms of Use</a>
                                        </li>
                                    </ul>
                                    <ul class="t-footr-help-list">
                                        <li class="t-footer-help-item mr-0">
                                            <a target="_blank" href="<?= BASE_URL_B2C ?>careers.php" class="t-footer-help-link t-footer-contact-discription">Career</a>
                                        </li>
                                        <li class="t-footer-help-item mr-0">
                                            <a target="_blank" href="<?= BASE_URL_B2C ?>gallery.php" class="t-footer-help-link t-footer-contact-discription">Gallery</a>
                                        </li>
                                        <li class="t-footer-help-item mr-0">
                                            <a target="_blank" href="<?= BASE_URL_B2C ?>testimonials.php" class="t-footer-help-link t-footer-contact-discription">Testimonials</a>
                                        </li>
                                        <li class="t-footer-help-item mr-0">
                                            <a target="_blank" href="<?= BASE_URL_B2C ?>privacy-policy.php" class="t-footer-help-link t-footer-contact-discription">Privacy Policy</a>
                                        </li>
                                        <li class="t-footer-help-item mr-0">
                                            <a target="_blank" href="<?= BASE_URL_B2C ?>cancellation-policy.php" class="t-footer-help-link t-footer-contact-discription">Cancellation
                                                Policy</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col col-12 col-md-6 col-lg-3 col-xl-3">
                            <div class="t-footer-contact-list">
                                <h5 class="t-footer-contact-title">Follow with us</h5>
                                <p class="t-footer-contact-discription mb-2">Join our social media's and get more update of latest tours, sightseeing & attractions.</p>
                                <ul class="t-footer-social-list">
                                    <?php if ($social_media[0]['fb'] != '') { ?>
                                        <li class="t-footer-social-item">
                                            <a href="<?= $social_media[0]['fb'] ?>" class="t-footer-social-link" target="_blank">
                                                <i class="fa fa-facebook" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if ($social_media[0]['li'] != '') { ?>
                                        <li class="t-footer-social-item">
                                            <a href="<?= $social_media[0]['li'] ?>" class="t-footer-social-link" target="_blank">
                                                <i class="fa fa-linkedin" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if ($social_media[0]['tw'] != '') { ?>
                                        <li class="t-footer-social-item">
                                            <a href="<?= $social_media[0]['tw'] ?>" class="t-footer-social-link" target="_blank">
                                                <i class="fa fa-twitter" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if ($social_media[0]['wa'] != '') { ?>
                                        <li class="t-footer-social-item">
                                            <a href="<?= $social_media[0]['wa'] ?>" class="t-footer-social-link" target="_blank">
                                                <i class="fa fa-whatsapp" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if ($social_media[0]['inst'] != '') { ?>
                                        <li class="t-footer-social-item">
                                            <a href="<?= $social_media[0]['inst'] ?>" class="t-footer-social-link" target="_blank">
                                                <i class="fa fa-instagram" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if ($social_media[0]['yu'] != '') { ?>
                                        <li class="t-footer-social-item">
                                            <a href="<?= $social_media[0]['yu'] ?>" class="t-footer-social-link" target="_blank">
                                                <i class="fa fa-youtube" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <div class="copiright-content">
        <p class="copiright-discription">Copyrights © 2025 <a href="" style="text-decoration: none; color: inherit;">
                <?= $app_name ?></a> .All Rights Reserved</p>
    </div>
    <!-- Footer End -->

    </div>

    <div id="site_alert"></div>

    <div id='hotel-result'></div>

    <!-- <div id='WhatsAppPanel'></div> -->

    <!-- Javascript -->


    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/jquery-ui.1.10.4.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/popper.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/bootstrap-4.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/owl.carousel.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/select2.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/theme-scripts.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL ?>js/vi.alert.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL ?>js/jquery.validate.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/jquery-confirm.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/pagination.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL ?>js/jquery.datetimepicker.full.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/lightgallery.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/lg-thumbnail.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/lg-zoom.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/scripts.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/custom.js"></script>


    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/ion-rangeslider@2.3.0/js/ion.rangeSlider.min.js"></script>



    </body>

    </html>

    <script>
        $(document).ready(function() {



            var base_url = $('#base_url').val();

            if (typeof Storage !== 'undefined') {



                var currency_id = $('#global_currency').val();

                if (localStorage) {

                    var global_currency = localStorage.getItem('global_currency');



                } else {

                    var global_currency = window.sessionStorage.getItem('global_currency');

                }

            }

            //Get selected Currency Dropdown

            $.post(base_url + 'view/get_currency_dropdown.php', {
                currency_id: global_currency
            }, function(data) {
                $('#currency_dropdown').html(data);

                $('#currency').select2();



                var currency_id1 = $('#currency').val();

                //Set selected currency in php session also

                $.post(base_url + 'view/set_currency_session.php', {
                    currency_id: currency_id1
                }, function(data) {

                });

                if (typeof Storage !== 'undefined') {

                    if (localStorage) {

                        localStorage.setItem(

                            'global_currency', currency_id1

                        );

                    } else {

                        window.sessionStorage.setItem(

                            'global_currency', currency_id1

                        );

                    }

                }

                // get_selected_currency();

            });

        });



        // $('#WhatsAppPanel').load('../whatsContent.html');



        function tours_page_currencies(current_page_url) {



            var base_url = $('#base_url').val();

            var default_currency = $('#global_currency').val();

            if (typeof Storage !== 'undefined') {

                if (localStorage) {

                    var currency_id = localStorage.getItem('global_currency');

                } else {

                    var currency_id = window.sessionStorage.getItem('global_currency');

                }

            }

            // Listing page //Load Currency Icon

            var currency_icon_lisr = document.querySelectorAll(".currency-icon");

            var cache_currencies = JSON.parse($('#cache_currencies').val());

            var to_currency_rate = (cache_currencies.find(el => el.id === currency_id) !== undefined) ? cache_currencies.find(
                el => el.id === currency_id) : '0';

            currency_icon_lisr.forEach(function(item) {

                item.innerHTML = to_currency_rate.icon;

            });
            if (current_page_url != base_url + 'view/tours/tours-listing.php') {



                // Indivisual Package Php page

                var price_list = JSON.parse(sessionStorage.getItem('tours_best_amount_list'));

                var amount_Classlist = document.querySelectorAll(".best-currency-price");

                if (price_list !== null && amount_Classlist[0] !== undefined) {

                    price_list.map((tour, i) => {



                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        var cost = parseFloat((tour.amount * from_currency_rate), 2).toFixed(2);

                        if (parseFloat(cost) != '0.00') {

                            amount_Classlist[i].innerHTML = cost;

                        } else {

                            amount_Classlist[i].innerHTML = 'On Request';

                        }

                    });

                }

            } else {


                //Tour Prices
                var price_list = JSON.parse(sessionStorage.getItem('tours_amount_list'));

                var amount_Classlist = document.querySelectorAll(".tours-currency-price");

                if (price_list !== null && amount_Classlist[0] !== undefined) {

                    price_list.map((tour, i) => {

                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];
                        amount_Classlist[i].innerHTML = parseFloat((tour.amount * from_currency_rate)).toFixed(2);

                    });

                }

                //Tour Org Prices

                var price_list = JSON.parse(sessionStorage.getItem('tours_orgamount_list'));

                var amount_Classlist = document.querySelectorAll(".tours-orgcurrency-price");

                if (price_list !== null && amount_Classlist[0] !== undefined) {

                    price_list.map((tour, i) => {

                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        amount_Classlist[i].innerHTML = parseFloat((tour.amount * from_currency_rate))
                            .toFixed(2);

                    });

                }

                //Tour best Prices

                var price_list = JSON.parse(sessionStorage.getItem('tours_best_amount_list'));

                var amount_Classlist = document.querySelectorAll(".best-currency-price");

                if (price_list !== null && amount_Classlist[0] !== undefined) {

                    price_list.map((tour, i) => {

                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        amount_Classlist[i].innerHTML = parseFloat((tour.amount * from_currency_rate))
                            .toFixed(2);

                    });

                }

                //Tour best Org Prices

                var price_list = JSON.parse(sessionStorage.getItem('tours_best_orgamount_list'));

                var amount_Classlist = document.querySelectorAll(".best-tours-orgamount");

                if (price_list !== null && amount_Classlist[0] !== undefined) {

                    price_list.map((tour, i) => {

                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        amount_Classlist[i].innerHTML = parseFloat((tour.amount * from_currency_rate))
                            .toFixed(2);

                    });

                }

                //Best High-Low cost array(Price Range filter)
                var best_price_list = JSON.parse(sessionStorage.getItem('tours_best_price'));

                if (best_price_list !== null) {

                    var ans_arr3 = [];

                    best_price_list.map((tour, i) => {

                        if (i === 0)

                            tour.amount = Math.floor(tour.amount);

                        else

                            tour.amount = Math.ceil(tour.amount);

                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                        var to_currency_rate = currency_rates[0];
                        var from_currency_rate = currency_rates[1];
                        var final_amount = (parseFloat(tour.amount * from_currency_rate));

                        ans_arr3.push(final_amount);
                        $('#price_rangevalues').val((ans_arr3));
                    });
                    const element = document.querySelector(".c-priceRange");
                    if (element !== null) {
                        clearRange();
                    }
                }
            }

        }

        function group_tours_page_currencies(current_page_url) {



            var base_url = $('#base_url').val();

            var default_currency = $('#global_currency').val();

            if (typeof Storage !== 'undefined') {

                if (localStorage) {

                    var currency_id = localStorage.getItem('global_currency');

                } else {

                    var currency_id = window.sessionStorage.getItem('global_currency');

                }

            }

            // Listing page //Load Currency Icon

            var currency_icon_lisr = document.querySelectorAll(".currency-icon");

            var cache_currencies = JSON.parse($('#cache_currencies').val());

            var to_currency_rate = (cache_currencies.find(el => el.id === currency_id) !== undefined) ? cache_currencies.find(
                el => el.id === currency_id) : '0';

            currency_icon_lisr.forEach(function(item) {

                item.innerHTML = to_currency_rate.icon;

            });

            if (current_page_url != base_url + 'view/group_tours/tours-listing.php') {



                // Indivisual Package Php page

                var price_list = JSON.parse(sessionStorage.getItem('tours_best_amount_list'));

                var amount_Classlist = document.querySelectorAll(".best-currency-price");

                if (price_list !== null && amount_Classlist[0] !== undefined) {

                    price_list.map((tour, i) => {



                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];
                        var cost = parseFloat((tour.amount * from_currency_rate), 2).toFixed(2);
                        if (parseFloat(cost) != '0.00') {

                            amount_Classlist[i].innerHTML = cost;

                        } else {

                            amount_Classlist[i].innerHTML = 'On Request';

                        }

                    });

                }

            } else {



                //Tour Prices

                var price_list = JSON.parse(sessionStorage.getItem('tours_amount_list'));

                var amount_Classlist = document.querySelectorAll(".tours-currency-price");

                if (price_list !== null && amount_Classlist[0] !== undefined) {

                    price_list.map((tour, i) => {

                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        amount_Classlist[i].innerHTML = parseFloat((tour.amount * to_currency_rate))
                            .toFixed(2);

                    });

                }

                //adult Prices

                var price_list = JSON.parse(sessionStorage.getItem('adult_price_list'));

                var amount_Classlist = document.querySelectorAll(".adult_cost-currency-price");

                if (price_list !== null && amount_Classlist[0] !== undefined) {

                    price_list.map((tour, i) => {

                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];
                        amount_Classlist[i].innerHTML = parseFloat((tour.amount * to_currency_rate))
                            .toFixed(2);

                    });

                }

                //child without best Prices

                var price_list = JSON.parse(sessionStorage.getItem('childwo_price_list'));

                var amount_Classlist = document.querySelectorAll(".childwio_cost-currency-price");

                if (price_list !== null && amount_Classlist[0] !== undefined) {

                    price_list.map((tour, i) => {

                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        amount_Classlist[i].innerHTML = parseFloat((tour.amount * to_currency_rate))
                            .toFixed(2);

                    });

                }

                //child with Prices

                var price_list = JSON.parse(sessionStorage.getItem('childwi_price_list'));

                var amount_Classlist = document.querySelectorAll(".childwi_cost-currency-price");

                if (price_list !== null && amount_Classlist[0] !== undefined) {

                    price_list.map((tour, i) => {

                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        amount_Classlist[i].innerHTML = parseFloat((tour.amount * to_currency_rate))
                            .toFixed(2);

                    });

                }

                //extra bed Org Prices

                var price_list = JSON.parse(sessionStorage.getItem('extrabed_price_list'));

                var amount_Classlist = document.querySelectorAll(".extrabed-currency-price");

                if (price_list !== null && amount_Classlist[0] !== undefined) {

                    price_list.map((tour, i) => {

                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        amount_Classlist[i].innerHTML = parseFloat((tour.amount * to_currency_rate))
                            .toFixed(2);

                    });

                }

                //infant Org Prices

                var price_list = JSON.parse(sessionStorage.getItem('infant_price_list'));

                var amount_Classlist = document.querySelectorAll(".infant_cost-currency-price");

                if (price_list !== null && amount_Classlist[0] !== undefined) {

                    price_list.map((tour, i) => {

                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        amount_Classlist[i].innerHTML = parseFloat((tour.amount * to_currency_rate))
                            .toFixed(2);

                    });

                }

                //Best High-Low cost array(Price Range filter)
                var best_price_list = JSON.parse(sessionStorage.getItem('group_tours_best_price'));

                if (best_price_list !== null) {

                    var ans_arr3 = [];

                    best_price_list.map((tour, i) => {

                        if (i === 0)

                            tour.amount = Math.floor(tour.amount);

                        else

                            tour.amount = Math.ceil(tour.amount);

                        //  if (tour.id != currency_id) {

                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        var final_amount = (parseFloat(tour.amount * to_currency_rate));


                        ans_arr3.push(final_amount);

                        //  } else {

                        //      ans_arr3.push(parseFloat(tour.amount).toFixed(2));

                        //  }

                        $('#price_rangevalues').val((ans_arr3));

                    });

                    const element = document.querySelector(".c-priceRange");

                    if (element !== null) {

                        clearRange();

                    }

                }



            }

        }

        function index_page_currencies() {

            var base_url = $('#base_url').val();

            var credit_amount = $("#credit_amount_temp").val();

            var default_currency = $('#global_currency').val();

            if (typeof Storage !== 'undefined') {

                if (localStorage) {

                    var currency_id = localStorage.getItem('global_currency');

                } else {

                    var currency_id = window.sessionStorage.getItem('global_currency');

                }

            }



            final_arr = JSON.parse(sessionStorage.getItem('final_arr'));

            var adult_count = 0;

            var child_count = 0;

            if (final_arr === null) {

                $('#total_pax').html(2);

                $('#room_count').html(1 + ' Room');

                $('#adult_count').val(2);

                $('#child_count').val(0);

                $('#dynamic_room_count').val(1);

            } else {

                for (var n = 0; n < final_arr.length; n++) {

                    adult_count = parseFloat(adult_count) + parseFloat(final_arr[n]['rooms']['adults']);

                    child_count = parseFloat(child_count) + parseFloat(final_arr[n]['rooms']['child']);

                }

                $('#total_pax').html(adult_count + child_count);

                $('#room_count').html(final_arr.length + ' Rooms');

                $('#adult_count').val(adult_count);

                $('#child_count').val(child_count);

                $('#dynamic_room_count').val(final_arr.length);

            }



            setTimeout(() => {

                //Hotels for honeymoon costing

                var amountClasslist = document.querySelectorAll(".currency-hotel-price");

                var amount_list = JSON.parse(sessionStorage.getItem('hotel_price'));

                if (amount_list !== null && amountClasslist[0] !== undefined) {

                    amount_list.map((tour, i) => {

                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        amountClasslist[i].innerHTML = parseFloat(to_currency_rate / from_currency_rate * tour
                            .amount).toFixed(2);

                    });

                }

                //Credit amount conversion

                var currency_rates = get_currency_rates(default_currency, currency_id).split('-');

                var to_currency_rate = currency_rates[0];

                var from_currency_rate = currency_rates[1];

                var result = parseFloat(to_currency_rate / from_currency_rate * credit_amount).toFixed(2);

                if (!isNaN(result))

                    $('#credit_amount').html(result);

                else

                    $('#credit_amount').html((0).toFixed(2));



                //Load Currency Icon

                var currency_icon_lisr = document.querySelectorAll(".currency-icon");

                var cache_currencies = $('#cache_currencies').val();

                cache_currencies = JSON.parse(cache_currencies);

                var to_currency_rate = (cache_currencies.find(el => el.id === currency_id) !== undefined) ?
                    cache_currencies.find(el => el.id === currency_id) : '0';

                currency_icon_lisr.forEach(function(item) {

                    item.innerHTML = to_currency_rate.icon;

                });

            }, 1200);

        }

        function transfer_page_currencies() {

            var base_url = $('#base_url').val();

            var credit_amount = $("#credit_amount_temp").val();

            var default_currency = $('#global_currency').val();

            if (typeof Storage !== 'undefined') {

                if (localStorage) {

                    var currency_id = localStorage.getItem('global_currency');

                } else {

                    var currency_id = window.sessionStorage.getItem('global_currency');

                }

            }

            //Load Currency Icon
            var currency_icon_lisr = document.querySelectorAll(".currency-icon");
            var cache_currencies = JSON.parse($('#cache_currencies').val());
            var to_currency_rate = (cache_currencies.find(el => el.id === currency_id) !== undefined) ? cache_currencies.find(el => el.id === currency_id) : '0';
            currency_icon_lisr.forEach(function(item) {
                item.innerHTML = to_currency_rate.icon;
            });

            var price_list = JSON.parse(sessionStorage.getItem('transfer_amount_list'));
            var amount_Classlist = document.querySelectorAll(".transfer-currency-price");

            if (price_list !== null && amount_Classlist[0] !== undefined) {

                price_list.map((tour, i) => {
                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];
                    var from_currency_rate = currency_rates[1];
                    console.log(parseFloat((tour.amount * from_currency_rate)).toFixed(2));
                    amount_Classlist[i].innerHTML = parseFloat((tour.amount * from_currency_rate)).toFixed(2);

                });

            }
            //Best High-Low cost array(Price Range filter)
            var best_price_list = JSON.parse(sessionStorage.getItem('transfer_best_price'));

            if (best_price_list !== null) {

                var ans_arr3 = [];

                best_price_list.map((tour, i) => {

                    if (i === 0)

                        tour.amount = Math.floor(tour.amount);

                    else

                        tour.amount = Math.ceil(tour.amount);

                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];
                    var from_currency_rate = currency_rates[1];
                    var final_amount = (parseFloat(tour.amount * from_currency_rate));

                    ans_arr3.push(final_amount);

                    $('#price_rangevalues').val((ans_arr3));

                });

                const element = document.querySelector(".c-priceRange");

                if (element !== null) {

                    clearRange();

                }

            }

        }

        function activties_page_currencies() {
            var base_url = $('#base_url').val();
            var credit_amount = $("#credit_amount_temp").val();
            var default_currency = $('#global_currency').val();
            if (typeof Storage !== 'undefined') {
                if (localStorage) {
                    var currency_id = localStorage.getItem('global_currency');
                } else {
                    var currency_id = window.sessionStorage.getItem('global_currency');
                }
            }
            //Load Currency Icon
            var currency_icon_lisr = document.querySelectorAll(".currency-icon");
            var cache_currencies = JSON.parse($('#cache_currencies').val());
            var to_currency_rate = (cache_currencies.find(el => el.id === currency_id) !== undefined) ? cache_currencies.find(el => el.id === currency_id) : '0';
            currency_icon_lisr.forEach(function(item) {
                item.innerHTML = to_currency_rate.icon;
            });
            //Credit amount conversion
            var currency_rates = get_currency_rates(default_currency, currency_id).split('-');
            var to_currency_rate = currency_rates[0];
            var from_currency_rate = currency_rates[1];
            var result = parseFloat(from_currency_rate * credit_amount).toFixed(2);
            if (!isNaN(result))
                $('#credit_amount').html(result);
            else
                $('#credit_amount').html((0).toFixed(2));

            //Activity best original prices
            var price_list = JSON.parse(sessionStorage.getItem('bestorg_activity_price_list'));
            var amount_Classlist = document.querySelectorAll(".best-activity-orgamount");
            if (price_list !== null && amount_Classlist[0] !== undefined) {
                price_list.map((tour, i) => {
                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];
                    var from_currency_rate = currency_rates[1];
                    amount_Classlist[i].innerHTML = parseFloat(from_currency_rate * tour.amount).toFixed(2);
                });
            }
            //Activity best offer prices
            var price_list = JSON.parse(sessionStorage.getItem('act_price_list'));
            var amount_Classlist = document.querySelectorAll(".best-activity-amount");
            if (price_list !== null && amount_Classlist[0] !== undefined) {
                price_list.map((tour, i) => {
                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];
                    var from_currency_rate = currency_rates[1];
                    amount_Classlist[i].innerHTML = parseFloat(from_currency_rate * tour.amount).toFixed(2);
                });
            }
            //Activities prices
            var price_list = JSON.parse(sessionStorage.getItem('activity_amount_list'));
            var amount_Classlist = document.querySelectorAll(".activity-currency-price");
            if (price_list !== null && amount_Classlist[0] !== undefined) {
                price_list.map((tour, i) => {
                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];
                    var from_currency_rate = currency_rates[1];
                    amount_Classlist[i].innerHTML = parseFloat(from_currency_rate * tour.amount).toFixed(2);
                });
            }
            //Activities Original prices
            var price_list = JSON.parse(sessionStorage.getItem('orgactivity_amount_list'));
            var amount_Classlist = document.querySelectorAll(".activity-orgcurrency-price");
            if (price_list !== null && amount_Classlist[0] !== undefined) {
                price_list.map((tour, i) => {
                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];
                    var from_currency_rate = currency_rates[1];
                    amount_Classlist[i].innerHTML = parseFloat(from_currency_rate * tour.amount).toFixed(2);
                });
            }
            //Activities Offer prices
            var price_list = JSON.parse(sessionStorage.getItem('activityoffer_price_list'));
            var amount_Classlist = document.querySelectorAll(".offer-currency-price");
            if (price_list !== null && amount_Classlist[0] !== undefined) {
                price_list.map((tour, i) => {
                    if (tour.flag == 'no') {
                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                        var to_currency_rate = currency_rates[0];
                        var from_currency_rate = currency_rates[1];
                        amount_Classlist[i].innerHTML = parseFloat(from_currency_rate * tour.amount).toFixed(2);
                    }
                });
            }
            //Best High-Low cost array(Price Range filter) 
            var best_price_list = JSON.parse(sessionStorage.getItem('activity_best_price'));
            if (best_price_list !== null) {
                var ans_arr3 = [];
                best_price_list.map((tour, i) => {
                    if (i === 0)
                        tour.amount = Math.floor(tour.amount);
                    else
                        tour.amount = Math.ceil(tour.amount);
                    if (tour.id != currency_id) {
                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                        var to_currency_rate = currency_rates[0];
                        var from_currency_rate = currency_rates[1];
                        ans_arr3.push((parseFloat(from_currency_rate * tour.amount)));
                    } else {
                        ans_arr3.push(parseFloat(tour.amount).toFixed(2));
                    }

                    $('#price_rangevalues').val((ans_arr3));
                });
                const element = document.querySelector(".c-priceRange");
                if (element !== null) {
                    clearRange();
                }
            }
        }

        function ferry_page_currencies() {
            var base_url = $('#base_url').val();
            var credit_amount = $("#credit_amount_temp").val();
            var default_currency = $('#global_currency').val();
            if (typeof Storage !== 'undefined') {
                if (localStorage) {
                    var currency_id = localStorage.getItem('global_currency');
                } else {
                    var currency_id = window.sessionStorage.getItem('global_currency');
                }
            }
            // setTimeout(() => {
            //Load Currency Icon
            var currency_icon_lisr = document.querySelectorAll(".currency-icon");
            var cache_currencies = JSON.parse($('#cache_currencies').val());
            var to_currency_rate = (cache_currencies.find(el => el.id === currency_id) !== undefined) ? cache_currencies.find(el => el.id === currency_id) : '0';
            currency_icon_lisr.forEach(function(item) {
                item.innerHTML = to_currency_rate.icon;
            });
            //Credit amount conversion
            var currency_rates = get_currency_rates(default_currency, currency_id).split('-');
            var to_currency_rate = currency_rates[0];
            var from_currency_rate = currency_rates[1];
            var result = parseFloat(from_currency_rate * credit_amount).toFixed(2);
            if (!isNaN(result))
                $('#credit_amount').html(result);
            else
                $('#credit_amount').html((0).toFixed(2));

            //ferry prices
            var price_list = JSON.parse(sessionStorage.getItem('ferry_amount_list'));
            var amount_Classlist = document.querySelectorAll(".ferry-currency-price");
            if (price_list !== null && amount_Classlist[0] !== undefined) {
                price_list.map((tour, i) => {
                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];
                    var from_currency_rate = currency_rates[1];
                    amount_Classlist[i].innerHTML = parseFloat(from_currency_rate * tour.amount).toFixed(2);
                });
            }

            //ferry adult prices
            var price_list = JSON.parse(sessionStorage.getItem('ferry_adult_amount_list'));
            var amount_Classlist = document.querySelectorAll(".ferry-currency-adult_price");
            if (price_list !== null && amount_Classlist[0] !== undefined) {
                price_list.map((tour, i) => {
                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];
                    var from_currency_rate = currency_rates[1];
                    amount_Classlist[i].innerHTML = parseFloat(from_currency_rate * tour.amount).toFixed(2);
                });
            }
            //ferry child prices
            var price_list = JSON.parse(sessionStorage.getItem('ferry_child_amount_list'));
            var amount_Classlist = document.querySelectorAll(".ferry-currency-child_price");
            if (price_list !== null && amount_Classlist[0] !== undefined) {
                price_list.map((tour, i) => {
                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];
                    var from_currency_rate = currency_rates[1];
                    amount_Classlist[i].innerHTML = parseFloat(from_currency_rate * tour.amount).toFixed(2);
                });
            }
            //ferry infant prices
            var price_list = JSON.parse(sessionStorage.getItem('ferry_infant_amount_list'));
            var amount_Classlist = document.querySelectorAll(".ferry-currency-infant_price");
            if (price_list !== null && amount_Classlist[0] !== undefined) {
                price_list.map((tour, i) => {
                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];
                    var from_currency_rate = currency_rates[1];
                    amount_Classlist[i].innerHTML = parseFloat(from_currency_rate * tour.amount).toFixed(2);
                });
            }
            //Best High-Low cost array(Price Range filter) 
            var best_price_list = JSON.parse(sessionStorage.getItem('ferry_best_price'));
            if (best_price_list !== null) {
                var ans_arr3 = [];
                best_price_list.map((tour, i) => {
                    if (i === 0)
                        tour.amount = Math.floor(tour.amount);
                    else
                        tour.amount = Math.ceil(tour.amount);
                    if (tour.id == currency_id) {
                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                        var to_currency_rate = currency_rates[0];
                        var from_currency_rate = currency_rates[1];
                        ans_arr3.push(parseFloat(from_currency_rate * tour.amount).toFixed(2));
                    } else {
                        ans_arr3.push(parseFloat(tour.amount).toFixed(2));
                    }

                    $('#price_rangevalues').val((ans_arr3));
                });
                const element = document.querySelector(".c-priceRange");
                if (element !== null) {
                    clearRange();
                }
            }
            // },800);
        }

        function visa_page_currencies() {

            var base_url = $('#base_url').val();

            var credit_amount = $("#credit_amount_temp").val();

            var default_currency = $('#global_currency').val();

            if (typeof Storage !== 'undefined') {

                if (localStorage) {

                    var currency_id = localStorage.getItem('global_currency');

                } else {

                    var currency_id = window.sessionStorage.getItem('global_currency');

                }

            }

            //Load Currency Icon

            var currency_icon_lisr = document.querySelectorAll(".currency-icon");

            var cache_currencies = JSON.parse($('#cache_currencies').val());

            var to_currency_rate = (cache_currencies.find(el => el.id === currency_id) !== undefined) ? cache_currencies.find(
                el => el.id === currency_id) : '0';

            currency_icon_lisr.forEach(function(item) {

                item.innerHTML = to_currency_rate.icon;

            });

            //Internal Prices
            var price_list = JSON.parse(sessionStorage.getItem('visa_amount_list'));

            var amount_Classlist = document.querySelectorAll(".visa-currency-price");

            if (price_list !== null && amount_Classlist[0] !== undefined) {

                price_list.map((tour, i) => {

                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];

                    var from_currency_rate = currency_rates[1];
                    amount_Classlist[i].innerHTML = parseFloat((tour.amount * from_currency_rate)).toFixed(2);

                });

            }
            //Total Prices
            var price_list = JSON.parse(sessionStorage.getItem('visa_total_amount_list'));

            var amount_Classlist = document.querySelectorAll(".visa-currency-total-price");

            if (price_list !== null && amount_Classlist[0] !== undefined) {

                price_list.map((tour, i) => {

                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];

                    var from_currency_rate = currency_rates[1];
                    amount_Classlist[i].innerHTML = parseFloat((tour.amount * from_currency_rate)).toFixed(2);

                });

            }


        }

        function hotel_page_currencies() {

            var base_url = $('#base_url').val();

            var credit_amount = $("#credit_amount_temp").val();

            var default_currency = $('#global_currency').val();

            if (typeof Storage !== 'undefined') {

                if (localStorage) {

                    var currency_id = localStorage.getItem('global_currency');

                } else {

                    var currency_id = window.sessionStorage.getItem('global_currency');

                }

            }

            //Load Currency Icon

            var currency_icon_lisr = document.querySelectorAll(".currency-icon");

            var cache_currencies = JSON.parse($('#cache_currencies').val());

            var to_currency_rate = (cache_currencies.find(el => el.id === currency_id) !== undefined) ? cache_currencies.find(
                el => el.id === currency_id) : '0';

            currency_icon_lisr.forEach(function(item) {

                item.innerHTML = to_currency_rate.icon;

            });
            //Get all amounts
            var amount_Classlist = document.querySelectorAll(".currency-price");
            var amount_list = JSON.parse(sessionStorage.getItem('amount_list'));

            var pamount_Classlist = document.querySelectorAll(".room-currency-price");
            var room_price_list = JSON.parse(sessionStorage.getItem('room_price_list'));

            var orgamt_Classlist = document.querySelectorAll(".original-currency-price");
            var original_amt_list = JSON.parse(sessionStorage.getItem('original_amt_list'));

            var offeramt_Classlist = document.querySelectorAll(".offer-currency-price");
            var offer_price_list = JSON.parse(sessionStorage.getItem('offer_price_list'));

            var best_price_list = JSON.parse(sessionStorage.getItem('hotel_best_price'));

            //Hotel room lowest cost array
            if (amount_list !== null && amount_Classlist[0] !== undefined) {
                amount_list.map((tour, i) => {

                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];
                    var from_currency_rate = currency_rates[1];
                    amount_Classlist[i].innerHTML = parseFloat(from_currency_rate * tour.amount).toFixed(2);
                });
            }
            //hotel room cost
            if (room_price_list !== null && pamount_Classlist[0] !== undefined) {
                room_price_list.map((tour, i) => {

                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];
                    var from_currency_rate = currency_rates[1];
                    pamount_Classlist[i].innerHTML = parseFloat(from_currency_rate * tour.amount).toFixed(2);
                });
            }
            //Hotel Original Cost
            if (original_amt_list !== null && orgamt_Classlist[0] !== undefined) {
                original_amt_list.map((tour, i) => {

                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];
                    var from_currency_rate = currency_rates[1];

                    orgamt_Classlist[i].innerHTML = parseFloat(from_currency_rate * tour.amount).toFixed(2);
                });
            }
            //Hotel Offer Cost
            if (offer_price_list !== null && offeramt_Classlist[0] !== undefined) {
                offer_price_list.map((tour, i) => {
                    if (tour.flag == 'no') {
                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                        var to_currency_rate = currency_rates[0];
                        var from_currency_rate = currency_rates[1];
                        console.log(tour.amount, from_currency_rate, to_currency_rate);
                        offeramt_Classlist[i].innerHTML = parseFloat(from_currency_rate * tour.amount).toFixed(2);
                    }
                });
            }
            //Best High-Low cost array(Price Range filter) 
            if (best_price_list !== null) {
                var ans_arr3 = [];
                best_price_list.map((tour, i) => {
                    if (i === 0)
                        tour.amount = Math.floor(tour.amount);
                    else
                        tour.amount = Math.ceil(tour.amount);
                    if (tour.id != currency_id) {
                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                        var to_currency_rate = currency_rates[0];
                        var from_currency_rate = currency_rates[1];
                        ans_arr3.push(parseFloat(from_currency_rate * tour.amount).toFixed(2));
                    } else {
                        ans_arr3.push(parseFloat(tour.amount).toFixed(2));
                    }

                    $('#price_rangevalues').val((ans_arr3));
                });
                const element = document.querySelector(".c-priceRange");
                if (element !== null) {
                    clearRange();
                }
            }

            //Room Category prices
            if (room_price_list !== null && pamount_Classlist[0] !== undefined) {
                room_price_list.map((tour, i) => {

                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');
                    var to_currency_rate = currency_rates[0];
                    var from_currency_rate = currency_rates[1];
                    pamount_Classlist[i].innerHTML = parseFloat(from_currency_rate * tour.amount).toFixed(2);
                });
            }

        }
        // Hotel currencies end
        function currency_converter() {

            var base_url = $('#base_url').val();

            var default_currency = $('#global_currency').val();



            if (typeof Storage !== 'undefined') {

                if (localStorage) {

                    var currency_id = localStorage.getItem('global_currency', credit_amount);

                } else {

                    var currency_id = window.sessionStorage.getItem('global_currency', credit_amount);

                }

            }

            //Load Currency Icon

            var currency_icon_lisr = document.querySelectorAll(".currency-icon");

            var cache_currencies = JSON.parse($('#cache_currencies').val());

            var to_currency_rate = (cache_currencies.find(el => el.id === currency_id) !== undefined) ? cache_currencies.find(
                el => el.id === currency_id) : '0';

            currency_icon_lisr.forEach(function(item) {

                item.innerHTML = to_currency_rate.icon;

            });

            //Get all amounts

            var amount_Classlist = document.querySelectorAll(".currency-price");

            var amount_list = JSON.parse(sessionStorage.getItem('amount_list'));



            var pamount_Classlist = document.querySelectorAll(".room-currency-price");

            var room_price_list = JSON.parse(sessionStorage.getItem('room_price_list'));



            var orgamt_Classlist = document.querySelectorAll(".original-currency-price");

            var original_amt_list = JSON.parse(sessionStorage.getItem('original_amt_list'));



            var offeramt_Classlist = document.querySelectorAll(".offer-currency-price");

            var offer_price_list = JSON.parse(sessionStorage.getItem('offer_price_list'));



            var cartamt_Classlist = document.querySelectorAll(".cart-currency-price");

            var cart_item_list = JSON.parse(localStorage.getItem('cart_item_list'));


            var best_price_list = JSON.parse(sessionStorage.getItem('best_price_list'));



            //Cart Items cost array

            if (cart_item_list !== null && cartamt_Classlist[0] !== undefined) {

                cart_item_list.map((tour, i) => {



                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                    var to_currency_rate = currency_rates[0];

                    var from_currency_rate = currency_rates[1];



                    cartamt_Classlist[i].innerHTML = parseFloat(to_currency_rate / from_currency_rate * tour.amount)
                        .toFixed(2);

                });

            }

            //Hotel Best lowest cost array

            if (amount_list !== null && amount_Classlist[0] !== undefined) {

                amount_list.map((tour, i) => {



                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                    var to_currency_rate = currency_rates[0];

                    var from_currency_rate = currency_rates[1];

                    amount_Classlist[i].innerHTML = parseFloat(to_currency_rate / from_currency_rate * tour.amount)
                        .toFixed(2);

                });

            }

            //Hotel Original Cost

            if (original_amt_list !== null && orgamt_Classlist[0] !== undefined) {

                original_amt_list.map((tour, i) => {



                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                    var to_currency_rate = currency_rates[0];

                    var from_currency_rate = currency_rates[1];



                    orgamt_Classlist[i].innerHTML = parseFloat(to_currency_rate / from_currency_rate * tour.amount)
                        .toFixed(2);

                });

            }

            //Hotel Offer Cost

            if (offer_price_list !== null && offeramt_Classlist[0] !== undefined) {

                offer_price_list.map((tour, i) => {

                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                    var to_currency_rate = currency_rates[0];

                    var from_currency_rate = currency_rates[1];



                    offeramt_Classlist[i].innerHTML = parseFloat(to_currency_rate / from_currency_rate * tour.amount)
                        .toFixed(2);

                });

            }

            //Best High-Low cost array(Price Range filter) 

            if (best_price_list !== null) {

                var ans_arr3 = [];

                best_price_list.map((tour, i) => {

                    if (i === 0)

                        tour.amount = Math.floor(tour.amount);

                    else

                        tour.amount = Math.ceil(tour.amount);

                    if (tour.id == currency_id) {

                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        ans_arr3.push(parseFloat(to_currency_rate / from_currency_rate * tour.amount).toFixed(2));

                    } else {

                        ans_arr3.push(parseFloat(tour.amount).toFixed(2));

                    }



                    $('#price_rangevalues').val((ans_arr3));

                });

                const element = document.querySelector(".c-priceRange");

                if (element !== null) {

                    clearRange();

                }

            }



            //Room Category prices

            if (room_price_list !== null && pamount_Classlist[0] !== undefined) {

                room_price_list.map((tour, i) => {



                    var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                    var to_currency_rate = currency_rates[0];

                    var from_currency_rate = currency_rates[1];

                    pamount_Classlist[i].innerHTML = parseFloat(to_currency_rate / from_currency_rate * tour.amount)
                        .toFixed(2);

                });

            }



        }



        function checkout_currency_converter() {

            var base_url = $('#base_url').val();

            var default_currency = $('#global_currency').val();

            if (typeof Storage !== 'undefined') {

                if (localStorage) {

                    var currency_id = localStorage.getItem('global_currency');

                } else {

                    var currency_id = window.sessionStorage.getItem('global_currency');

                }

            }

            setTimeout(() => {

                //Load Currency Icon

                var currency_icon_lisr = document.querySelectorAll(".currency-icon");

                var cache_currencies = JSON.parse($('#cache_currencies').val());

                var to_currency_rate = (cache_currencies.find(el => el.id === currency_id) !== undefined) ?
                    cache_currencies.find(el => el.id === currency_id) : '0';

                currency_icon_lisr.forEach(function(item) {

                    item.innerHTML = to_currency_rate.icon;

                });

                //Checkout Page amounts

                var cartp_list = document.querySelectorAll(".checkoutp-currency-price");

                var cart_amount_list = JSON.parse(localStorage.getItem('cart_amount_list'));



                var carttax_list = document.querySelectorAll(".checkouttax-currency-price");

                var cart_tax_list = JSON.parse(localStorage.getItem('cart_tax_list'));



                var cartt_list = document.querySelectorAll(".checkoutt-currency-price");

                var cart_total_list = JSON.parse(localStorage.getItem('cart_total_list'));



                //Checkout Page Final Pricing amounts

                var cartsubtotal_list = document.querySelectorAll(".checkouttsubtotal-currency-price");

                var cart_subtotal_list = JSON.parse(localStorage.getItem('cart_subtotal_list'));



                var carttotaltax_list = document.querySelectorAll(".checkoutttaxtotal-currency-price");

                var cart_totaltax_list = JSON.parse(localStorage.getItem('cart_totaltax_list'));



                var carttotal_list = document.querySelectorAll(".checkouttotal-currency-price");

                var cart_maintotal_list = JSON.parse(localStorage.getItem('cart_maintotal_list'));



                var cartgrandt_list = document.querySelectorAll(".checkoutgrandtotal-currency-price");

                var cart_grandtotal_list = localStorage.getItem('cart_grandtotal_list');



                //Checkout Final Pricing Amount cost array

                if (cart_subtotal_list !== null && cartsubtotal_list[0] !== undefined) {

                    cart_subtotal_list.map((tour, i) => {

                        var currency_rates = get_currency_rates(currency_id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        cartsubtotal_list[i].innerHTML = parseFloat(to_currency_rate / from_currency_rate *
                            tour).toFixed(2);

                    })

                }

                //Checkout Tax cost array

                if (cart_totaltax_list !== null && carttotaltax_list[0] !== undefined) {

                    cart_totaltax_list.map((tour, i) => {

                        var currency_rates = get_currency_rates(currency_id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        carttotaltax_list[i].innerHTML = parseFloat(to_currency_rate / from_currency_rate *
                            tour).toFixed(2);

                    })

                }

                //Checkout total cost array

                if (cart_maintotal_list !== null && carttotal_list[0] !== undefined) {

                    cart_maintotal_list.map((tour, i) => {

                        var currency_rates = get_currency_rates(currency_id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        carttotal_list[i].innerHTML = parseFloat(to_currency_rate / from_currency_rate * tour)
                            .toFixed(2);

                    })

                }

                //Checkout grand total cost array

                if (cartgrandt_list !== null) {

                    var currency_rates = get_currency_rates(currency_id, currency_id).split('-');

                    var to_currency_rate = currency_rates[0];

                    var from_currency_rate = currency_rates[1];

                    cartgrandt_list[0].innerHTML = parseFloat(to_currency_rate / from_currency_rate *
                        cart_grandtotal_list).toFixed(2);

                }

                //Checkout Amount cost array

                if (cart_amount_list !== null && cartp_list[0] !== undefined) {

                    cart_amount_list.map((tour, i) => {



                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        cartp_list[i].innerHTML = parseFloat(to_currency_rate / from_currency_rate * tour
                            .amount).toFixed(2);

                    });

                }

                //Checkout Tax cost array

                if (cart_tax_list !== null && carttax_list[0] !== undefined) {

                    cart_tax_list.map((tour, i) => {



                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        carttax_list[i].innerHTML = parseFloat(to_currency_rate / from_currency_rate * tour
                            .amount).toFixed(2);

                    });

                }

                //Checkout total cost array

                if (cart_total_list !== null && cartt_list[0] !== undefined) {

                    cart_total_list.map((tour, i) => {



                        var currency_rates = get_currency_rates(tour.id, currency_id).split('-');

                        var to_currency_rate = currency_rates[0];

                        var from_currency_rate = currency_rates[1];

                        cartt_list[i].innerHTML = parseFloat(to_currency_rate / from_currency_rate * tour
                            .amount).toFixed(2);

                    });

                }

                //Credit amount conversion

                var credit_amount = $("#credit_amount_temp").val();

                var currency_rates = get_currency_rates(default_currency, currency_id).split('-');

                var to_currency_rate = currency_rates[0];

                var from_currency_rate = currency_rates[1];

                var result = parseFloat(to_currency_rate / from_currency_rate * credit_amount).toFixed(2);

                if (!isNaN(result))

                    $('#credit_amount').html(result);

                else

                    $('#credit_amount').html((0).toFixed(2));

            }, 800);

        }


        // scroling feature add js code by vidya 

        // बटन को दिखाने के लिए जब यूज़र नीचे स्क्रॉल करता है
        window.onscroll = function() {
            let btn = document.getElementById("scrollToTopBtn");
            if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                btn.style.display = "block";
            } else {
                btn.style.display = "none";
            }
        };

        // बटन पर क्लिक करने से पेज ऊपर स्क्रॉल हो जाएगा
        document.getElementById("scrollToTopBtn").onclick = function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        };
    </script>

    <!-- <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/scripts.js"></script> -->