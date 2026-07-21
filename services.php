<?php

include 'config.php';

//Include header

include 'layouts/header2.php';
$_SESSION['page_type'] = 'services';


$servicesData = mysqli_fetch_all(mysqlQuery("SELECT services FROM `b2c_settings`"), MYSQLI_ASSOC);

$services = isset($servicesData[0]['services']) ? json_decode($servicesData[0]['services'], true) : [];
?>


<!-- ********** Component :: Page Title ********** -->

<div class="c-pageTitleSect ts-pageTitleSect">

    <div class="container">

        <div class="row">

            <div class="col-md-7 col-12">



                <!-- *** Search Head **** -->

                <div class="searchHeading">

                    <span class="pageTitle mb-0">Services</span>

                </div>

                <!-- *** Search Head End **** -->

            </div>



            <div class="col-md-5 col-12 c-breadcrumbs">

                <ul>

                    <li>

                        <a href="<?= BASE_URL_B2C ?>">Home</a>

                    </li>

                    <li class="st-active">

                        <a href="javascript:void(0)">Services</a>

                    </li>

                </ul>

            </div>



        </div>

    </div>

</div>

<!-- ********** Component :: Page Title End ********** -->

<!-- Testimonial Section Start -->

<section class="ts-customer-testimonial-section ts-destinations-section">

    <div class="container">

        <div class="ts-section-subtitle-content">

            <h2 class="ts-section-subtitle">OUR SERVICES</h2>

            <span class="ts-section-subtitle-icon"><img src="images/traveler.png" alt="traveler"
                    classimg-fluid=""></span>

        </div>

        <h2 class="ts-section-title">OUR TOP TRAVEL SERVICES</h2>

        <div class="row">

            <?php
            if (!empty($services)) :
                foreach ($services as $service) :
                    if ($service['service_name'] == 'Airport Transfers')
                        $icon = '<i class="fa fa-plane"></i>';
                    else if ($service['service_name'] == 'Adventure Activities')
                        $icon = '<i class="fa fa-hiking"></i>';
                    else if ($service['service_name'] == 'Luxury Cruise Tours')
                        $icon = '<i class="fa fa-anchor"></i>';
                    else if ($service['service_name'] == 'City Sightseeing Tours')
                        $icon = '<i class="fa fa-city"></i>';
                    else if ($service['service_name'] == 'Corporate Travel Services')
                        $icon = '<i class="fa fa-briefcase"></i>';
                    else if ($service['service_name'] == 'Hotel Bookings')
                        $icon = '<i class="fa fa-hotel"></i>';
                    else if ($service['service_name'] == 'Flight Reservations')
                        $icon = '<i class="fa fa-plane"></i>';
                    else if ($service['service_name'] == 'Visa Assistance')
                        $icon = '<i class="fa fa-passport"></i>';
                    else if ($service['service_name'] == 'Cruise Holidays')
                        $icon = '<i class="fa fa-ship"></i>';
                    else if ($service['service_name'] == 'Travel Insurance')
                        $icon = '<i class="fa fa-shield"></i>';
                    else if ($service['service_name'] == 'Adventure Activities')
                        $icon = '<i class="fa fa-mountain"></i>';
                    else
                        $icon = '<i class="fa fa-headphones"></i>';
            ?>
                    <div class="col col-12 col-md-6 col-lg-4">
                        <div class="ts-service-card">
                            <div class="ts-service-icon">
                                <div class="ts-service-icon__inner">
                                    <?php echo $icon; ?>
                                </div>
                            </div>
                            <div class="ts-service-card-body">
                                <h4 class="ts-service-title"><?= htmlspecialchars($service['service_name']) ?></h4>
                                <p class="ts-service-description"><?= htmlspecialchars($service['description']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php
                endforeach;
            else :
                ?>
                <p class="text-center font-weight-bold text-danger">No services available</p>
            <?php
            endif;
            ?>

        </div>

    </div>

</section>

<!-- Testimonial Section End -->

<a href="#" class="scrollup">Scroll</a>



<?php include 'layouts/footer2.php'; ?>

<script type="text/javascript" src="js2/scripts.js"></script>

<script>
    $(document).ready(function() {



        var service = '<?php echo $service; ?>';

        if (service && (service !== '' || service !== undefined)) {

            var checkLink = $('.c-searchContainer .c-search-tabs li');

            var checkTab = $('.c-searchContainer .search-tab-content .tab-pane');

            checkLink.each(function() {

                var child = $(this).children('.nav-link');

                if (child.data('service') === service) {

                    $(this).siblings().children('.nav-link').removeClass('active');

                    child.addClass('active');

                }

            });

            checkTab.each(function() {

                if ($(this).data('service') === service) {

                    $(this).addClass('active show').siblings().removeClass('active show');

                }

            })

        }



    });
</script>