<?php
include '../../config.php';
$ferry_results_array = ($_POST['data'] != '') ? $_POST['data'] : [];
?>
<!-- ***** Ferry Listing ***** -->
<?php
if (sizeof($ferry_results_array) > 0) {

    for ($ferry_i = 0; $ferry_i < sizeof($ferry_results_array); $ferry_i++) {

        $cruise_enq_data = array();
        $adults = $ferry_results_array[$ferry_i]['adults'];
        $children = $ferry_results_array[$ferry_i]['children'];
        $infant = $ferry_results_array[$ferry_i]['infant'];
        $travel_date = $ferry_results_array[$ferry_i]['travel_date'];
        $ferry_type = $ferry_results_array[$ferry_i]['ferry_type'];
        $ferry_category = $ferry_results_array[$ferry_i]['ferry_category'];
        $ferry_name = $ferry_results_array[$ferry_i]['ferry_name'];
        $from_location = $ferry_results_array[$ferry_i]['from_location'];
        $to_location = $ferry_results_array[$ferry_i]['to_location'];

        array_push($cruise_enq_data, array('ferry_type' => $ferry_type, 'ferry_category' => $ferry_category, 'ferry_name' => $ferry_name, 'from_location' => $from_location, 'to_location' => $to_location, 'travel_date' => $travel_date, 'adult_count' => $adults, 'child_count' => $children, 'infant_count' => $infant));

        $newUrl = '';
        $images = $ferry_results_array[$ferry_i]['image'];
        $image = explode(',', $images);
        if ($images == '') {
            $newUrl = BASE_URL . 'images/dummy-image.jpg';
        } else {

            for ($image_i = 0; $image_i < sizeof($image); $image_i++) {
                if ($image[$image_i] != '') {
                    $image = $image[$image_i];
                    $newUrl1 = preg_replace('/(\/+)/', '/', $image);
                    $newUrl1 = explode('uploads', $newUrl1);
                    $newUrl = BASE_URL . 'uploads' . $newUrl1[1];
                    break;
                }
            }
        }
        $link = 'https://web.whatsapp.com/send?phone=' . $app_contact_no . '&text=Hello,
    %20I%20am%20interested%20in%20' . $ferry_results_array[$ferry_i]['ferry_name'] . '(' . $ferry_results_array[$ferry_i]['ferry_type'] . ') ' . $ferry_results_array[$ferry_i]['ferry_category'] . '%20. Kindly%20provide%20more%20details.%20Thanks!';
?>
        <!-- ***** Car Card ***** -->
        <!--<div class="c-cardList type-transfer">-->
        <div class="c-cardList ">
            <div class="c-cardListTable tours-cardListTable">
                <!-- *** Car Card image *** -->
                <div class="cardList-image">
                    <img src="<?= $newUrl ?>" loading="lazy" alt="iTours" class="d-block mb-2" />
                    <a target="_blank" href="<?= $link ?>" class="btn btn-outline-success d-block mb-2"><i class="fa fa-whatsapp"></i> Whatsapp</a>
                    <input type="hidden" value='<?= $newUrl ?>' id="image-<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>" />
                    <div class="typeOverlay">
                        <span class="transferType c-hide">
                            AC
                        </span>
                    </div>
                </div>
                <!-- *** Car Card image End *** -->

                <!-- *** Car Card Info *** -->
                <div class="cardList-info">
                    <div class="dividerSection type-1 noborder">
                        <div class="divider s1">
                            <h4 class="cardTitle"><span id="ferry_name-<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>"><?= $ferry_results_array[$ferry_i]['ferry_name'] . ' (' . $ferry_results_array[$ferry_i]['ferry_type'] . ')' ?></span>
                            </h4>
                            <div class="infoSection">
                                <span class="cardInfoLine cust">
                                    <i class="fa fa-ship"></i>
                                    Cruise Class: <strong id="ferry_type-<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>"><?= $ferry_results_array[$ferry_i]['ferry_category'] ?></strong>
                                </span>
                                <!-- <span class="c-tag" id="ferry_type-<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>"><?= $ferry_results_array[$ferry_i]['ferry_category'] ?></span> -->
                            </div>
                            <div class="infoSection">
                                <span class="cardInfoLine cust">
                                    <i class="fa fa-users"></i>
                                    Seating Capacity: <strong><?= $ferry_results_array[$ferry_i]['seating_capacity'] ?></strong>
                                </span>
                            </div>

                            <input type="hidden" id="total_pax<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>" value="<?= $ferry_results_array[$ferry_i]['adults'] . '-' . $ferry_results_array[$ferry_i]['children'] . '-' . $ferry_results_array[$ferry_i]['infant'] ?>" />
                            <input type="hidden" id="travel_date<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>" value="<?= $ferry_results_array[$ferry_i]['travel_date'] ?>" />
                            <input type="hidden" id="dep_date<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>" value="<?= $ferry_results_array[$ferry_i]['dep_date'] ?>" />
                            <input type="hidden" id="arr_date<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>" value="<?= $ferry_results_array[$ferry_i]['arr_date'] ?>" />
                        </div>

                        <div class="divider s2">
                            <div class="priceTag">
                                <div class="p-old">
                                    <span class="o_lbl">Total Price</span>
                                    <span class="price_main">
                                        <span class="p_currency currency-icon"></span>
                                        <span class="p_cost ferry-currency-price"><?= $ferry_results_array[$ferry_i]['total_cost'] ?></span>
                                        <span class="c-hide ferry-currency-id"><?= $ferry_results_array[$ferry_i]['currency_id'] ?></span>
                                    </span> <small>(Excl of all taxes)</small>
                                </div>
                                <button class="expandSect" role="button" data-toggle="collapse" href="#collapseExample<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>" aria-expanded="false" aria-controls="collapseExample">View Details</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- *** Car Card Info End *** -->
            </div>

            <!-- *** Activity Details Accordian *** -->
            <div class="collapse" id="collapseExample<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>">
                <div class="cardList-accordian">
                    <!-- ***** Activity Info Tabs ***** -->
                    <div class="c-compTabs">
                        <ul class="nav nav-pills" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="costing-tab" data-toggle="tab" href="#costing<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>" role="tab"
                                    aria-controls="costing" aria-selected="true">Costing</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="incl-tab" data-toggle="tab" href="#incl<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>" role="tab"
                                    aria-controls="incl" aria-selected="true">Inclusions & Exclusions</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link js-gallery" id="termsTab" data-toggle="tab" href="#terms<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>"
                                    role="tab" aria-controls="terms" aria-selected="true">Terms & Conditions</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="gallery-tab" data-toggle="tab" href="#gallery-<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>" role="tab"
                                    aria-controls="gallery" aria-selected="true">Image Gallery</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade active show" id="costing<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>" role="tabpanel" aria-labelledby="incl-tab">
                                <!-- **** Costing **** -->
                                <div class="clearfix m20-btm">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="c-flexCards">

                                                <?php
                                                if ($ferry_results_array[$ferry_i]['adults'] > 0) { ?>
                                                    <div class="f_card">
                                                        <span class="p_currency currency_icon currency-icon"></span>
                                                        <span class="currency_amount ferry-currency-adult_price"><?= $ferry_results_array[$ferry_i]['adult_cost'] ?></span>
                                                        <span class="c-hide ferry-currency-adult_id"><?= $ferry_results_array[$ferry_i]['currency_id'] ?></span>
                                                        <span class="currency_for">For Adult (PP)</span>
                                                    </div>
                                                <?php } ?>
                                                <?php
                                                if ($ferry_results_array[$ferry_i]['children'] > 0) { ?>
                                                    <div class="f_card">
                                                        <span class="p_currency currency_icon currency-icon"></span>
                                                        <span class="currency_amount ferry-currency-child_price"><?= $ferry_results_array[$ferry_i]['child_cost'] ?></span>
                                                        <span class="c-hide ferry-currency-child_id"><?= $ferry_results_array[$ferry_i]['currency_id'] ?></span>
                                                        <span class="currency_for">For Child (PP)</span>
                                                    </div>
                                                <?php } ?>
                                                <?php
                                                if ($ferry_results_array[$ferry_i]['infant'] > 0) { ?>
                                                    <div class="f_card">
                                                        <span class="p_currency currency_icon currency-icon"></span>
                                                        <span class="currency_amount ferry-currency-infant_price"><?= $ferry_results_array[$ferry_i]['infant_cost'] ?></span>
                                                        <span class="c-hide ferry-currency-infant_id"><?= $ferry_results_array[$ferry_i]['currency_id'] ?></span>
                                                        <span class="currency_for">For Infant (PP)</span>
                                                    </div>
                                                <?php } ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="incl<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>" role="tabpanel" aria-labelledby="incl-tab">
                                <!-- **** Incl/Excl **** -->
                                <div class="clearfix margin20-bottom">

                                    <h3 class="c-heading">
                                        Inclusions
                                    </h3>
                                    <div class="custom_texteditor">
                                        <?= $ferry_results_array[$ferry_i]['inclusions'] ?>
                                    </div>
                                    <h3 class="c-heading">
                                        Exclusions
                                    </h3>
                                    <div class="custom_texteditor">
                                        <?= $ferry_results_array[$ferry_i]['exclusions'] ?>
                                    </div>
                                </div>
                                <!-- **** Incl/Excl End **** -->
                            </div>
                            <!-- **** Tab Incl End **** -->

                            <!-- **** Tab Terms **** -->
                            <div class="tab-pane fade" id="terms<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>" role="tabpanel" aria-labelledby="terms-tab">
                                <!-- **** Terms **** -->
                                <div class="clearfix margin20-bottom">

                                    <h3 class="c-heading">
                                        Terms & Conditions
                                    </h3>
                                    <div class="custom_texteditor">
                                        <?= $ferry_results_array[$ferry_i]['terms_condition'] ?>
                                    </div>
                                </div>
                                <!-- **** Terms End **** -->
                            </div>
                            <!-- **** Tab Terms End **** -->

                            <!-- **** Tab Gallery **** -->
                            <div class="tab-pane fade" id="gallery-<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>" role="tabpanel" aria-labelledby="gallery-tab<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>">
                                <!-- **** photo List **** -->
                                <div class="clearfix">
                                    <div class="c-photoGallery">
                                        <div class="js-photoGallery owl-carousel">
                                            <?php
                                            $images = $ferry_results_array[$ferry_i]['image'];
                                            $image = explode(',', $images);
                                            if ($images != '') {
                                                for ($image_i = 0; $image_i <= sizeof($image); $image_i++) {
                                                    if ($image[$image_i] != '') {
                                                        $image1 = $image[$image_i];
                                                        $newUrl1 = preg_replace('/(\/+)/', '/', $image1);
                                                        $newUrl1 = explode('uploads', $newUrl1);
                                                        $newUrl = BASE_URL . 'uploads' . $newUrl1[1];
                                            ?>
                                                        <div class="item">
                                                            <img src="<?= $newUrl ?>" alt="" />
                                                        </div>
                                            <?php
                                                    }
                                                }
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- **** photo List End **** -->
                            </div>
                            <!-- **** Tab Gallery End **** -->

                        </div>
                    </div>
                    <!-- ***** ferry Info Tabs End***** -->
                    <div class="clearfix text-right">
                        <button type="button" class="c-button md" id='<?= $ferry_results_array[$ferry_i]['tariff_id'] ?>' onclick='enq_to_action_page("7",this.id,<?= json_encode($cruise_enq_data) ?>)'><i class="fa fa-phone-square" aria-hidden="true"></i> Enquiry</button>
                        <!-- <button type="button" class="c-button g-button md md"><i class="fa fa-contact-book" aria-hidden="true"></i> Book</button> -->
                    </div>
                </div>
            </div>
            <!-- *** ferry Details Accordian End *** -->
        </div>
        <!-- ***** Car Card End ***** -->

<?php }
} ?>
<!-- ***** ferry Listing End ***** -->
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
                slideBy: 1,
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
        setTimeout(() => {
            var amount_list = document.querySelectorAll(".ferry-currency-price");
            var amount_id = document.querySelectorAll(".ferry-currency-id");

            var adult_list = document.querySelectorAll(".ferry-currency-adult_price");
            var adult_id = document.querySelectorAll(".ferry-currency-adult_id");

            var child_list = document.querySelectorAll(".ferry-currency-child_price");
            var child_id = document.querySelectorAll(".ferry-currency-child_id");

            var infant_list = document.querySelectorAll(".ferry-currency-infant_price");
            var infant_id = document.querySelectorAll(".ferry-currency-infant_id");
            //ferry Best Cost
            var amount_arr = [];
            for (var i = 0; i < amount_list.length; i++) {
                amount_arr.push({
                    'amount': amount_list[i].innerHTML,
                    'id': amount_id[i].innerHTML
                });
            }
            sessionStorage.setItem('ferry_amount_list', JSON.stringify(amount_arr));

            //ferry adult Cost
            var amount_arr = [];
            for (var i = 0; i < adult_list.length; i++) {
                amount_arr.push({
                    'amount': adult_list[i].innerHTML,
                    'id': adult_id[i].innerHTML
                });
            }
            sessionStorage.setItem('ferry_adult_amount_list', JSON.stringify(amount_arr));

            //ferry child Cost
            var amount_arr = [];
            for (var i = 0; i < child_list.length; i++) {
                amount_arr.push({
                    'amount': child_list[i].innerHTML,
                    'id': child_id[i].innerHTML
                });
            }
            sessionStorage.setItem('ferry_child_amount_list', JSON.stringify(amount_arr));

            //ferry infant Cost
            var amount_arr = [];
            for (var i = 0; i < infant_list.length; i++) {
                amount_arr.push({
                    'amount': infant_list[i].innerHTML,
                    'id': infant_id[i].innerHTML
                });
            }
            sessionStorage.setItem('ferry_infant_amount_list', JSON.stringify(amount_arr));

            ferry_page_currencies();
            // Ferry class
            var ferry_class_array = JSON.parse(document.getElementById('ferry_class_array').value);
            var selected_ferry_class_array = (document.getElementById('selected_ferry_class_array').value).split(',');
            var html = '';
            for (var i = 0; i < ferry_class_array.length; i++) {
                var checked_status = (selected_ferry_class_array.includes(ferry_class_array[i])) ? 'checked' : '';
                html += '<div class="form-check"><input type="checkbox" name="ferry_class" class="form-check-input lblfilterChk" id="' + (i + 1) + '" value="' + ferry_class_array[i] + '" ' + checked_status + '/><label class="form-check-label lblfilterChk" for="' + (i + 1) + '">' + ferry_class_array[i] + '</label></div>';
            }
            $('#cruise_classes').html(html);
            // Ferry type
            var ferry_type_array = JSON.parse(document.getElementById('ferry_type_array').value);
            var selected_ferry_type_array = (document.getElementById('selected_ferry_type_array').value).split(',');
            var html = '';
            for (var i = 0; i < ferry_type_array.length; i++) {
                var checked_status = (selected_ferry_type_array.includes(ferry_type_array[i])) ? 'checked' : '';
                html += '<div class="form-check"><input type="checkbox" name="ferry_type" class="form-check-input lblftfilterChk" id="ct-' + (i + 1) + '" value="' + ferry_type_array[i] + '" ' + checked_status + '/><label class="form-check-label lblftfilterChk" for="ct-' + (i + 1) + '">' + ferry_type_array[i] + '</label></div>';
            }
            $('#cruise_types').html(html);

        }, 500);
    });
</script>