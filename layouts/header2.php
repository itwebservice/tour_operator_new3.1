<?php

global $currency, $app_name, $app_contact_no, $app_email_id_send, $app_address;

include "get_cache_currencies.php";
include "package_header_data.php";
include "array_column.php";


// LIVE Cache file reading

//$cached_array = json_decode(file_get_contents('https://' . $_SERVER['SERVER_NAME'] . '/crm/view/b2c_cache.php'));

// LOCAL Cache file readingc

//$cached_array = json_decode(file_get_contents('http://localhost/themestore-theme-1-php/crm/view/b2c_cache.php'));



$array_master = new array_master();

$to_currency_rate = $currency;




$social_media = json_decode($moduleData->getB2cSettings(settingsName: 'social_media'), true);;
$google_analytics = $moduleData->getB2cSettings('google_analytics');

$tidio_chat = $moduleData->getB2cSettings('tidio_chat');

?>

<!DOCTYPE html>

<html>

<head>
    <?php
    header("Cache-Control: max-age=31536000, public");
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + 31536000) . " GMT");
    ?>
    <!-- Page Title -->

    <title><?= $app_name ?></title>



    <!-- Meta Tags -->

    <meta charset="utf-8" />

    <meta name="keywords" content="HTML5 Template" />

    <meta name="description" content="iTours - Travel, Tour Booking HTML5 Template" />

    <meta name="author" content="SoapTheme" />



    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="<?php echo BASE_URL_B2C; ?>images/favicon.png" type="image/x-icon" />



    <!-- Theme Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/font-awesome-4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="<?php echo BASE_URL_B2C; ?>css2/bootstrap-4.min.css" />

    <link rel="stylesheet" href="<?php echo BASE_URL_B2C; ?>css2/owl.carousel.min.css" />

    <link id="main-style" rel="stylesheet" href="<?php echo BASE_URL; ?>css/vi.alert.css" />

    <link rel="stylesheet" href="<?php echo BASE_URL_B2C; ?>css2/pagination.css" />

    <link rel="stylesheet" href="<?php echo BASE_URL_B2C; ?>css2/jquery-confirm.css">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/jquery.datetimepicker.css">

    <link rel="stylesheet" href="<?php echo BASE_URL_B2C; ?>css2/lightgallery.css">

    <link rel="stylesheet" href="<?php echo BASE_URL_B2C; ?>css2/lightgallery-bundle.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/ion-rangeslider@2.3.0/css/ion.rangeSlider.min.css">

    <link id="main-style" rel="stylesheet" href="<?php echo BASE_URL_B2C; ?>css2/itours-styles.css" />
    <link id="main-style" rel="stylesheet" href="<?php echo BASE_URL_B2C; ?>css2/itours-components.css" />

    <?php
    $colorData = $moduleData->getB2cColorScheme(); //$cached_array[0]->cms_data[1];
    if (!empty($colorData->text_primary_color) && !empty($colorData->button_color)) {
        $btnColor = $colorData->button_color;
        $primaryColor = $colorData->text_primary_color;
    } else {
        $btnColor = '#93d42e';
        $primaryColor = '#f68c34';
    }
    ?>

    <Style>
        * {
            --main-bg-color: <?= $btnColor ?>;
            --main-primary-color: <?= $primaryColor ?>;
        }
    </Style>

    <link id="main-style" rel="stylesheet/less" type="text/css" href="<?php echo BASE_URL_B2C; ?>css/LESS/itours-styles.php" />

    <script src="<?php echo BASE_URL_B2C; ?>js2/less.js"></script>
    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/jquery-3.4.1.min.js"></script>

    <!-- <script>
       
    </script> -->

    <script src="<?= $tidio_chat ?>" async></script>

    <!-- Javascript Page Loader -->
 <?= $google_analytics ?>
</head>

<body onload="myLoader()">
    <div id="loading"></div>

    <input type="hidden" id="base_url" name="base_url" value="<?= BASE_URL_B2C ?>">

    <input type="hidden" id="crm_base_url" name="crm_base_url" value="<?= BASE_URL ?>">

    <input type="hidden" id="global_currency" value="<?= $currency ?>" />

    <div class="c-pageWrapper">

        <!-- ********** Component :: Header ********** -->

        <div class="clearfix">

            <!-- **** Top Header ***** -->

            <div class="c-pageHeaderTop">

                <div class="pageHeader_top mobileSidebar">



                    <!-- Menubar close btn for Mobile -->

                    <button class="closeSidebar forMobile"></button>

                    <!-- Menubar close btn for Mobile End -->



                    <div class="container">
                        <div class="row align-items-center">



                            <div class="col-md-6 col-12 section-1">

                                <span class="staticText d-inline"><i class="fa-solid fa-phone me-1 mr-2"></i><?= $app_contact_no ?></span>
                                <a href="mailto:<?= $app_email_id_send ?>" class="header-mail-link d-inline mr-2"><i class="fa-solid fa-envelope me-1"></i> <?= $app_email_id_send ?></a>
                            </div>



                            <div class="col-md-1 col-12 section-2 text-center">

                                <!-- <a href="mailto:gauri@goldfinchholidays.com" class="header-mail-link"><? //= $cached_array[0]->company_profile_data[0]->email_id 
                                                                                                            ?></a> -->

                            </div>



                            <div class="col-md-5 col-12 section-3">

                                <div class="topListing">

                                    <ul>
                                        <li>

                                            <div class="c-select2DD st-clear">

                                                <div id='currency_dropdown'></div>

                                            </div>

                                        </li>
                                        <li class="inHeader">
                                            <?php include_once('translate.php') ?>
                                        </li>
                                        <li>

                                            <a class="login_button" target="_blank" href="<?= BASE_URL . 'view/customer/index.php' ?>">Login</a>

                                        </li>


                                    </ul>

                                </div>

                            </div>



                        </div>
                    </div>


                </div>

            </div>

            <!-- **** Top Header End ***** -->

            <!-- New Header -->

            <div class="container">
                <div class="top-header">
                    <div class="row">

                        <div class="col-sm-3 col-7">

                            <div id="logo_home" class="header-logo">

                                <a href="<?= BASE_URL_B2C ?>" title="B2C Home Page">

                                    <img src="<?php echo $admin_logo_url; ?>" alt="<?php echo $app_name; ?>" />

                                </a>

                            </div>

                        </div>

                        <nav class="col-sm-9 col-5 text-right pad-top">

                            <a class="cmn-toggle-switch cmn-toggle-switch__htx open_close" href="javascript:void(0);"><span>Menu mobile</span></a>

                            <div class="main-menu">

                                <div id="header_menu">
                                    <h5>Menu</h5>
                                    <!-- <img src="<?php echo $admin_logo_url; ?>" width="160" height="34" alt="<?php echo $app_name; ?>" /> -->

                                </div>

                                <a href="#" class="open_close close_in" id="close_in">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </a>

                                <!-- TOP NAVIGATION MENUS -->
                                <?php
                                /**
                                 * @var 
                                 * Get headers menu from app_settings
                                 */
                                $menuOptions = $headerMenu ? json_decode($headerMenu, true) : [];
                                $groupTours = $themeData->getGroupTourDropDownData();
                                ?>
                                <ul>
                                    <?php
                                    foreach ($menuOptions as $menu) {
                                        $menuKey = $menu;
                                        if (preg_match("#_#", $menu)) {
                                            $menu = str_replace('_', ' ', $menu);
                                        }
                                    ?>


                                        <?php if ($menuKey !== 'group_tours' && $menuKey !== 'holiday'):
                                            $menuLink = null;
                                            $clickEvent = null;
                                            $btnClass = null;
                                            if ($menuKey === 'home') {
                                                $menuLink = BASE_URL_B2C . "index.php";
                                            } else if ($menuKey === 'activities') {
                                                $clickEvent = "get_tours_data('','4')";
                                            } else if ($menuKey === 'visa') {
                                                $clickEvent = "get_tours_data('','6')";
                                            } else if ($menuKey === 'hotels') {
                                                $clickEvent = "get_tours_data('','3')";
                                            } else if ($menuKey === 'transfer') {
                                                $clickEvent = "get_tours_data('','5')";
                                            } else if ($menuKey === 'cruise') {
                                                $clickEvent = "get_tours_data('','7')";
                                            } else if ($menuKey === 'services') {
                                                $menuLink = BASE_URL_B2C . "services.php";
                                            } else if (preg_match('#contact_us#', $menuKey)) {
                                                $menuLink = BASE_URL_B2C . "contact.php";
                                            } else if ($menuKey === 'offers') {
                                                $menuLink = BASE_URL_B2C . "offers.php";
                                                $btnClass = "btn header-offer-btn";
                                            }

                                        ?>
                                            <li>
                                                <a <?php if ($btnClass): ?> class="<?= $btnClass ?>" <?php endif; ?> <?php if ($menuLink) : ?> href="<?php echo $menuLink; ?>" <?php endif; ?> <?php if ($clickEvent): ?>onclick="<?= $clickEvent; ?>" <?php endif; ?>><?php echo ucwords($menu); ?></a>
                                            </li>
                                        <?php else: ?>

                                            <?php if ($menuKey == 'group_tours') {

                                                list(
                                                    $domesticGroupTours,
                                                    $internationalGroupTours
                                                ) = $themeData->hydrateGroupTourDropDownData($groupTours);

                                            ?>

                                                <li class="submenu">
                                                    <a class="show-submenu"><?php echo ucwords($menu); ?><i class="icon itours-b2b-angle-down"></i></a>

                                                    <ul>
                                                        <?php if (count($domesticGroupTours) > 0): ?>
                                                            <li class="third-level">
                                                                <a href="javascript:void(0)">Domestic</a>
                                                                <ul>
                                                                    <?php foreach ($domesticGroupTours as $tour) { ?>
                                                                        <li>
                                                                            <a onclick="get_tours_data('<?= $tour['dest_id'] ?>','2')">
                                                                                <?= htmlspecialchars($tour['dest_name']) ?>
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if (count($internationalGroupTours) > 0): ?>
                                                            <li class="third-level">
                                                                <a href="javascript:void(0)">International</a>
                                                                <ul>
                                                                    <?php foreach ($internationalGroupTours as $tour) { ?>
                                                                        <li>
                                                                            <a onclick="get_tours_data('<?= $tour['dest_id'] ?>','2')">
                                                                                <?= htmlspecialchars($tour['dest_name']) ?>
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </li>
                                            <?php } ?>

                                            <?php if ($menuKey == 'holiday') {

                                                $packageTours = $themeData->getHolidayPackagesDropDownData();
                                                list($domesticPackageTours, $internationalPackageTours) = $themeData->hydratePackageTourDropDownData($packageTours);
                                            ?>
                                                <li class="submenu">
                                                    <a class="show-submenu"><?php echo ucwords($menu); ?><i class="icon itours-b2b-angle-down"></i></a>

                                                    <ul>
                                                        <?php if (count($domesticPackageTours) > 0): ?>
                                                            <li class="third-level">
                                                                <a href="javascript:void(0)">Domestic</a>
                                                                <ul>
                                                                    <?php foreach ($domesticPackageTours as $tour) { ?>
                                                                        <li>
                                                                            <a onclick="get_tours_data('<?= $tour['dest_id'] ?>','1')">
                                                                                <?= htmlspecialchars($tour['dest_name']) ?>
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if (count($internationalPackageTours) > 0): ?>
                                                            <li class="third-level">
                                                                <a href="javascript:void(0)">International</a>
                                                                <ul>
                                                                    <?php foreach ($internationalPackageTours as $tour) { ?>
                                                                        <li>
                                                                            <a onclick="get_tours_data('<?= $tour['dest_id'] ?>','1')">
                                                                                <?= htmlspecialchars($tour['dest_name']) ?>
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </li>
                                            <?php } ?>


                                        <?php endif; ?>


                                    <?php } ?>

                                </ul>

                            </div>

                            <!-- End main-menu -->



                    </div>
                </div>
                <!-- End dropdown-cart-->

                </li>

                </ul>

                </nav>

            </div>

        </div>

        <!-- container -->

        <!-- New Header End -->

    </div>

    <!--preloader script-->
    <script>
        var preloader = document.getElementById('loading');

        function myLoader() {
            preloader.style.display = 'none';
        }
    </script>

    <!-- ********** Component :: Header End ********** -->

    <?php

    // include "get_cache_tax_rules.php";

    ?>

    <input type="hidden" id='cache_currencies' value='<?= $currency_data ?>' />