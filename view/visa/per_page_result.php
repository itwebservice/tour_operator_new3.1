<?php
include '../../config.php';
$visa_results_array = ($_POST['data'] != '') ? $_POST['data'] : [];
$h_currency_id = $_SESSION['session_currency_id'];
global $currency;
if (sizeof($visa_results_array) > 0) {

    for ($visa_i = 0; $visa_i < sizeof($visa_results_array); $visa_i++) {

        $visa_enq_data = array();
        $country_name = $visa_results_array[$visa_i]['country_name'];

        array_push($visa_enq_data, $country_name);
        $link = 'https://web.whatsapp.com/send?phone=' . $app_contact_no . '&text=Hello,
    %20I%20am%20interested%20in%20' . $country_name . '%20 visa. Kindly%20provide%20more%20details.%20Thanks!';
?>
        <!-- ***** Visa Card ***** -->
        <div class="c-cardList type-hotel">
            <div class="c-cardListTable tours-cardListTable" role="button" data-toggle="collapse"
                href="#collapseExample<?= $visa_results_array[$visa_i]['country_id'] ?>" aria-expanded="false"
                aria-controls="collapseExample">
                <!-- *** Visa Card image *** -->
                <div class="cardList-image">
                    <div class="flag mb-2">
                        <span class="fi fi-<?= strtolower($visa_results_array[$visa_i]['country_code']) ?>"></span>
                        <!-- alt="<?php echo $visa_results_array[$visa_i]['visa_name']; ?>" /> -->
                    </div>
                    <a target="_blank" href="<?= $link ?>" class="btn btn-outline-success d-block mb-2"><i class="fa fa-whatsapp"></i> Whatsapp</a>
                </div>
                <!-- *** Visa Card image End *** -->

                <!-- *** Visa Card Info *** -->
                <div class="cardList-info" role="button">
                    <div class="dividerSection type-1 noborder">
                        <div class="divider s1">
                            <h4 class="cardTitle">
                                <?php echo $visa_results_array[$visa_i]['country_name']; ?>
                            </h4>

                            <div class="c-aminityListBlock mb-3 mb-md-0">
                                <ul>
                                    <?php
                                    $visa_types = ($visa_results_array[$visa_i]['visa_info'] != '' && $visa_results_array[$visa_i]['visa_info'] != null) ? ($visa_results_array[$visa_i]['visa_info']) : [];
                                    if (sizeof($visa_types) > 0) { ?>
                                        <li>
                                            <div class="amenity st-last  st-lasts">
                                                <span class="num">+<?= sizeof($visa_types) ?></span>
                                                <span class="txt">more visa types</span>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="divider s2">
                            <div class="priceTag">
                                <div class="p-old">
                                    <span class="o_lbl"></span>
                                    <span class="price_main">
                                        <span class="p_currency currency-icon"></span>
                                        <span class="p_cost visa-currency-total-price"><?= $visa_results_array[$visa_i]['min_cost'] ?></span>
                                        <span class="c-hide visa-currency-total-id"><?= $currency ?></span>
                                    </span>
                                </div>
                                <button class="expandSect">View Details</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- *** Visa Card Info End *** -->
            </div>

            <!-- *** Visa Details Accordian *** -->
            <div class="collapse collapseForMobile" id="collapseExample<?= $visa_results_array[$visa_i]['country_id'] ?>">
                <div class="cardList-accordian">
                    <!-- ***** Visa Info Tabs ***** -->
                    <div class="c-compTabs">
                        <ul class="nav nav-pills" id="myTab" role="tablist">
                            <li class="nav-item active">
                                <a class="nav-link active"
                                    id="visa_type_all-tab<?= $visa_results_array[$visa_i]['country_id'] ?>" data-toggle="tab"
                                    href="#visa_type_all<?= $visa_results_array[$visa_i]['country_id'] ?>" role="tab"
                                    aria-controls="visa_type_all" aria-selected="true">All</a>
                            </li>
                            <?php
                            if (sizeof($visa_types) > 0) {
                                for ($v = 0; $v < sizeof($visa_types); $v++) { ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="type-tab<?= $visa_results_array[$visa_i]['country_id'] . $v ?>"
                                            data-toggle="tab" href="#type<?= $visa_results_array[$visa_i]['country_id'] . $v ?>"
                                            role="tab" aria-controls="type" aria-selected="true"><?= $visa_types[$v]['visa_type'] ?></a>
                                    </li>
                            <?php
                                }
                            } ?>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <!-- **** Tab Visa Listing **** -->

                            <!-- **** Tab Description **** -->
                            <div class="tab-pane fade show active"
                                id="visa_type_all<?= $visa_results_array[$visa_i]['country_id'] ?>" role="tabpanel"
                                aria-labelledby="visa_type_all-tab<?= $visa_results_array[$visa_i]['country_id'] ?>">
                                <!-- **** all **** -->
                                <?php
                                if (sizeof($visa_types) > 0) {
                                    for ($v = 0; $v < sizeof($visa_types); $v++) {
                                ?>
                                        <div class="c-cardListHolder">
                                            <div class="c-cardListTable type-2" role="button">
                                                <input class="btn-radio" type="radio"
                                                    id="<?= $visa_results_array[$visa_i]['country_id'] . $v ?>"
                                                    name="result_day-<?= $visa_results_array[$visa_i]['country_id'] ?>"
                                                    value='<?php echo $visa_types[$v]['visa_type']; ?>'>
                                                <!-- *** Visa Card Info *** -->
                                                <label class="cardList-info"
                                                    for="<?= $visa_results_array[$visa_i]['country_id'] . $v ?>" role="button">
                                                    <div class="flexGrid">
                                                        <div class="gridItem">
                                                            <div class="infoCard">
                                                                <span class="infoCard_data">
                                                                    <?php echo $visa_types[$v]['visa_type']; ?>
                                                                </span>
                                                                <div class="infoCard_price">
                                                                    <span class="p_currency currency-icon"></span>
                                                                    <span class="p_cost visa-currency-price"><?php echo $visa_types[$v]['cost']; ?></span>
                                                                    <span class="c-hide visa-currency-id"><?= $currency ?></span>
                                                                </div>
                                                                <span class="infoCard_priceTax">(exclusive of all taxes)</span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </label>
                                                <!-- *** Visa Card Info End *** -->
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                <?php } ?>
                                <!-- **** all End **** -->
                            </div>
                            <?php
                            if (sizeof($visa_types) > 0) {
                                for ($v = 0; $v < sizeof($visa_types); $v++) { ?>
                                    <div class="tab-pane fade" id="type<?= $visa_results_array[$visa_i]['country_id'] . $v ?>"
                                        role="tabpanel"
                                        aria-labelledby="type-tab<?= $visa_results_array[$visa_i]['country_id'] . $v ?>">
                                        <!-- **** types **** -->
                                        <h3 class="c-heading">
                                            Time Taken
                                        </h3>
                                        <div class="clearfix text-dark" style="font-size:14px;">
                                            <?php echo $visa_types[$v]['time_taken'] ?>
                                        </div>
                                        <h3 class="c-heading">
                                            List Of Documents
                                        </h3>
                                        <div class="custom_texteditor">
                                            <?php echo $visa_types[$v]['documents'] ?>
                                        </div>
                                        <?php
                                        if ($visa_types[$v]['upload_url1'] != '') {
                                            $url = preg_replace('/(\/+)/', '/', $visa_types[$v]['upload_url1']);
                                            $newUrl1 = explode('uploads', $url);
                                            $newUrl = BASE_URL . 'uploads' . $newUrl1[1];  ?>
                                            <h3 class="c-heading">
                                                Form-1
                                                <a href="<?php echo $newUrl; ?>" download title="Download Form-1"><i
                                                        class="fa fa-file-text"></i></a>
                                            </h3>
                                        <?php } ?>
                                        <?php
                                        if ($visa_types[$v]['upload_url2'] != '') {
                                            $url = preg_replace('/(\/+)/', '/', $visa_types[$v]['upload_url2']);
                                            $newUrl1 = explode('uploads', $url);
                                            $newUrl = BASE_URL . 'uploads' . $newUrl1[1];  ?>
                                            <h3 class="c-heading">
                                                Form-2
                                                <a href="<?php echo $newUrl; ?>" download title="Download Form-2"><i
                                                        class="fa fa-file-text"></i></a>
                                            </h3>
                                        <?php } ?>
                                        <!-- **** all End **** -->
                                    </div>
                            <?php }
                            } ?>
                        </div>
                        <div class="clearfix text-right">
                            <button type="button" class="c-button md" id='<?= $visa_results_array[$visa_i]['country_id'] ?>'
                                onclick='enq_to_action_page("6",this.id,<?= json_encode($visa_enq_data) ?>)'><i
                                    class="fa fa-phone-square" aria-hidden="true"></i> Enquiry</button>
                        </div>
                        <!-- ***** Visa Info Tabs End***** -->
                    </div>
                </div>
                <!-- *** Visa Details Accordian End *** -->
            </div>
        </div>
        <!-- ***** Visa Card End ***** -->
<?php
    }
} //Visa arrays for loop
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

    });
    clearTimeout(b);
    var b = setTimeout(function() {

        var amount_list = document.querySelectorAll(".visa-currency-price");
        var amount_id = document.querySelectorAll(".visa-currency-id");
        //Main price
        var amount_total_list = document.querySelectorAll(".visa-currency-total-price");
        var amount_total_id = document.querySelectorAll(".visa-currency-total-id");

        //internal Cost
        var amount_arr = [];
        for (var i = 0; i < amount_list.length; i++) {
            amount_arr.push({
                'amount': amount_list[i].innerHTML,
                'id': amount_id[i].innerHTML
            });
        }
        //total Cost
        var total_amount_arr = [];
        for (var i = 0; i < amount_total_list.length; i++) {
            total_amount_arr.push({
                'amount': amount_total_list[i].innerHTML,
                'id': amount_total_id[i].innerHTML
            });
        }
        sessionStorage.setItem('visa_amount_list', JSON.stringify(amount_arr));
        sessionStorage.setItem('visa_total_amount_list', JSON.stringify(total_amount_arr));
        visa_page_currencies();
    }, 500);
</script>