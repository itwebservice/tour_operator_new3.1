<?php



include 'config.php';

//Include header

include 'layouts/header2.php';
$_SESSION['page_type'] = 'about';


$result = mysqli_fetch_all(mysqlQuery("SELECT about_us FROM `b2c_settings`"), MYSQLI_ASSOC);
$aboutusData = !empty($result) ? json_decode($result[0]['about_us'], true) : [];

?>


<!-- ********** Component :: Page Title ********** -->

<div class="c-pageTitleSect ts-pageTitleSect">

    <div class="container">

        <div class="row">

            <div class="col-md-7 col-12">



                <!-- *** Search Head **** -->

                <div class="earchHeading">

                    <span class="pageTitle mb-0">About Us</span>

                </div>

                <!-- *** Search Head End **** -->

            </div>



            <div class="col-md-5 col-12 c-breadcrumbs">

                <ul>

                    <li>

                        <a href="<?= BASE_URL_B2C ?>">Home</a>

                    </li>

                    <li class="st-active">

                        <a href="javascript:void(0)">About Us</a>

                    </li>

                </ul>

            </div>



        </div>

    </div>

</div>

<!-- ********** Component :: Page Title End ********** -->

<!-- Reason Section End -->

<section class="ts-reason-section ts-font-poppins">

    <div class="container">

        <div class="ts-section-subtitle-content">

            <h2 class="ts-section-subtitle">Know more</h2>

            <span class="ts-section-subtitle-icon"><img src="images/traveler.png" alt="traveler"
                    classimg-fluid=""></span>

        </div>

        <h2 class="ts-section-title">WHY CHOOSE US?</h2>



        <div class="row">

            <?php
            if (!empty($aboutusData)) :
                foreach ($aboutusData as $about) :

                    if ($about['title'] == 'The secret') {
                        $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff" class="bi bi-lock" viewBox="0 0 16 16"><path d="M8 1a3 3 0 0 0-3 3v3h6V4a3 3 0 0 0-3-3zM5 4a3 3 0 0 1 6 0v3H5V4z"/><path d="M4 7V6a4 4 0 0 1 8 0v1h.5A1.5 1.5 0 0 1 14 8.5v5A1.5 1.5 0 0 1 12.5 15h-9A1.5 1.5 0 0 1 2 13.5v-5A1.5 1.5 0 0 1 3.5 7H4zm0 1h8v5.5a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5V8z"/></svg>';
                    } else if ($about['title'] == 'Never give up.') {
                        $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff" class="bi bi-person-dolly" viewBox="0 0 16 16"><path d="M14 8V7a1 1 0 0 0-.58-.894L9.68 3.053a1 1 0 0 0-.91-.056l-3 2a1 1 0 0 0-.18 1.41l.38.475A4 4 0 0 1 8 6v8a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1V8h1.236a1 1 0 0 0 .764-.468L14 7V8h-1z"/></svg>';
                    } else if ($about['title'] == 'With a vision') {
                        $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff" class="bi bi-telescope" viewBox="0 0 16 16"><path d="M11 2a1 1 0 0 1 1 1v3h2a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h2V3a1 1 0 0 1 1-1h5zm1 5H4v4h8V7z"/></svg>';
                    } else if ($about['title'] == 'Indian travelers') {
                        $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff" class="bi bi-mountain" viewBox="0 0 16 16"><path d="M15 9l-3.515-6H4.515L1 9h3l2-3 2 5 2-5 2 3h3z"/></svg>';
                    } else if ($about['title'] == 'Continually') {
                        $icon = '<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"><circle cx="100" cy="100" r="90" fill="transparent" stroke="white" stroke-width="4"/><path d="M100,40 A60,60 0 1,1 40,100 A60,60 0 1,1 100,160 A60,60 0 1 1 160,100 A60,60 0 1,1 100,40" fill="none" stroke="white" stroke-width="8" stroke-linecap="round" stroke-dasharray="20,10"/><polygon points="100,20 115,40 85,40" fill="white"/></svg>';
                    } else if ($about['title'] == 'predict') {
                        $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff" class="bi bi-bar-chart" viewBox="0 0 16 16"><path d="M1 14h12v1H1v-1zm3-5h2v5H4v-5zm3-1h2v6H7v-6zm3-3h2v9h-2V5z"/></svg>';
                    } else {
                        $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M279.6 160.4C282.4 160.1 285.2 160 288 160C341 160 384 202.1 384 256C384 309 341 352 288 352C234.1 352 192 309 192 256C192 253.2 192.1 250.4 192.4 247.6C201.7 252.1 212.5 256 224 256C259.3 256 288 227.3 288 192C288 180.5 284.1 169.7 279.6 160.4zM480.6 112.6C527.4 156 558.7 207.1 573.5 243.7C576.8 251.6 576.8 260.4 573.5 268.3C558.7 304 527.4 355.1 480.6 399.4C433.5 443.2 368.8 480 288 480C207.2 480 142.5 443.2 95.42 399.4C48.62 355.1 17.34 304 2.461 268.3C-.8205 260.4-.8205 251.6 2.461 243.7C17.34 207.1 48.62 156 95.42 112.6C142.5 68.84 207.2 32 288 32C368.8 32 433.5 68.84 480.6 112.6V112.6zM288 112C208.5 112 144 176.5 144 256C144 335.5 208.5 400 288 400C367.5 400 432 335.5 432 256C432 176.5 367.5 112 288 112z" fill="#fff"></path></svg>';
                    }
            ?>
                    <div class="col col-12 col-md-6 col-lg-6">
                        <div class="ts-reason-card">
                            <div class="ts-reason-card-icon">
                                <div class="ts-reason-icon__inner">
                                    <?php echo $icon; ?>
                                </div>
                            </div>
                            <div class="ts-reason-card-body">
                                <h3 class="ts-reason-card-title"><?= htmlspecialchars($about['title']) ?></h3>
                                <p class="ts-reason-card-description"><?= htmlspecialchars($about['description']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php
                endforeach;
            else :
                ?>
                <p class="text-center font-weight-bold text-danger">No data available</p>
            <?php endif; ?>


        </div>

    </div>

</section>

<!-- Reason Section End -->




<!-- Special Section Start -->

<section class="ts-special-section ts-font-poppins">

    <div class="row no-gutters">

        <div class="col col-12 col-md-6 col-lg-6">

            <div class="ts-special-img">

                <img src="images/banner-2.jpg" alt="Special" class="img-fluid">

            </div>

        </div>

        <div class="col col-12 col-md-6 col-lg-6">

            <div class="ts-special-content">

                <div class="ts-special-content__inner">

                    <h2 class="ts-section-title">Special offer on early discount!</h2>

                    <p class="ts-section-description">Our Well Experienced tourism professionals serve tourists better
                        as per their convenience. You Can Contact us any time 24*7. We Provides efficient, reliable &
                        Cost effective Services anywhere in the world.</p>

                    <div class="abt-btn">
                        <a href="contact.php" class="btn btn-primary">Contact Us</a>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- Special Section End -->


<!-- Why choose Us Start -->
<section class="ts-special-section ts-font-poppins container pt-5topb-5 overflow-x-auto">

    <div class="row">

        <div class="col col-12 col-md-12 col-lg-6 col-xl-6 discount-wrapper">

            <div class="why-choose-img top-1 position-absolute">

                <img src="images/discount_1.png" alt="Special" class="img-fluid">

            </div>
            <div class="why-choose-img bottom-1 position-absolute">

                <img src="images/discount_2.png" alt="Special" class="img-fluid d-none d-md-block">

            </div>

        </div>

        <div class="col col-12 col-md-12 col-lg-6 col-xl-6">

            <div class="about-us-wrapper">

                <div class="about-us-inner-content px-3 md:px-0">
                    <p class="get-to-know-txt mb-3">Get to know us</p>
                    <h2 class="plan-your-txt">Plan Your Trip With Us</h2>

                    <p class="ts-section-description why-choose-para">"World's leading tour and travels Booking website, Over 30,000 packages worldwide. Book travel packages and enjoy your holidays with distinctive experience."</p>

                    <div>
                        <ul class="checklist ml-5px">
                            <li><i class="fas fa-check-square"></i> A Simply Perfect Place To Get Lost</li>
                            <li><i class="fas fa-check-square"></i> A place where start new life with place</li>
                            <li><i class="fas fa-check-square"></i> Top 10 destination adventure trips</li>
                        </ul>

                    </div>
                    <div class="d-flex ml-5px pb-3" style="padding-top: 1rem !important;">
                        <a href="contact.php" class="btn btn-primary">Contact Us</a>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
<!-- Why choose Us End -->


<script type="text/javascript" src="js2/scripts.js"></script>

<?php include 'layouts/footer2.php'; ?>