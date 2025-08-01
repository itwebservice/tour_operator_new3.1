<?php
global $currency, $app_name, $app_contact_no, $app_email_id_send, $app_address;
include "get_cache_currencies.php";


$city_data = mysqli_fetch_all(mysqlQuery("SELECT city_id,city_name,active_flag FROM `city_master` WHERE active_flag='active'"), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Theme Store:: Theme 1.2</title>

  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="./css/bootstrap.min.css" />
  <link rel="stylesheet" href="./css/select2.min.css" />
  <link rel="stylesheet" href="./css/owl.carousel.min.css" />
  <link id="main-style" rel="stylesheet" href="<?php echo BASE_URL_B2C; ?>css2/vi.alert.css" />
  <!-- <link rel="stylesheet" href="./css/bootstrap-datepicker.min.css" /> -->
  <link rel="stylesheet" href="./css/jquery.datetimepicker.css" />
  <link rel="stylesheet" href="./css/theme.css" />
</head>

<body>
  <!-- ***** Header ***** -->
  <header class="c-header" id="top-header">

    <input type="hidden" id="base_url" name="base_url" value="<?= BASE_URL_B2C ?>">
    <input type="hidden" id="crm_base_url" name="crm_base_url" value="<?= BASE_URL ?>">
    <input type="hidden" id="global_currency" value="<?= $currency ?>" />

    <?php
    $socialIcons = $themeData->getSocialIcons();
    ?>
    <!-- ***** Primary header section ***** -->
    <div class="header-primary">
      <div class="container-lg">
        <div class="row align-items-center">
          <!-- ***** Social media and contact section ***** -->
          <div class="col-lg-6 col-md-6">
            <ul class="sectionList">
              <li class="social-icons">
                <?php foreach ($socialIcons as $icon): ?>
                  <?php if ($icon['fb']) { ?>
                    <a href="<?php echo $icon['fb']; ?>" class="link" target="_blank">
                      <i class="fab fa-facebook-square"></i>
                    </a>
                  <?php } ?>
                  <?php if ($icon['tw']) { ?>
                    <a href="<?php echo $icon['tw']; ?>" class="link" target="_blank">
                      <i class="fab fa-twitter-square"></i>
                    </a>
                  <?php } ?>
                  <?php if ($icon['inst']) { ?>
                    <a href="<?php echo $icon['inst']; ?>" class="link" target="_blank">
                      <i class="fab fa-instagram"></i>
                    </a>
                  <?php } ?>
                  <?php if ($icon['li']) { ?>
                    <a href="<?php echo $icon['li']; ?>" class="link" target="_blank">
                      <i class="fab fa-linkedin"></i>
                    </a>
                  <?php } ?>
                  <?php if ($icon['wa']) { ?>
                    <a href="<?php echo $icon['wa']; ?>" class="link" target="_blank">
                      <i class="fa-brands fa-whatsapp"></i>
                    </a>
                  <?php } ?>
                  <?php if ($icon['yu']) { ?>
                    <a href="<?php echo $icon['yu']; ?>" class="link" target="_blank">
                      <i class="fa-brands fa-youtube"></i>
                    </a>
                  <?php } ?>
                <?php endforeach; ?>
              </li>
              <li>
                <a href="tel:<?php echo $app_contact_no; ?>" class="link"><span id="appContact"><i class="fa-solid fa-phone me-1"></i> <?php echo $app_contact_no; ?></span></a>
              </li>
              <li>
                <a href="mailto:<?php echo urlencode($app_email_id_send); ?>?subject=Hi" class="link"><span id="appEmail"><i class="fa-solid fa-envelope me-1"></i> <?php echo $app_email_id_send; ?></span></a>
              </li>
            </ul>
          </div>
          <!-- ***** Social media and contact section End ***** -->

          <!-- ***** Language change and login section ***** -->
          <div class="col-lg-6 col-md-6">
            <ul
              class="sectionList text-end d-flex align-items-center justify-content-end">
              <li><a href="<?php echo BASE_URL . "view/customer/index.php"; ?>" class="link" target="_blank">Login</a></li>
              <li class="inHeader">
                <?php include_once('translate.php'); ?>
              </li>
              <li>
                <div class="c-advanceSelect transparent">
                  <select class="js-advanceSelect" id="currency" name="currency" onchange="get_selected_currency()">
                    <?php
                    $currencies = $themeData->getCurrencyDropDownData();
                    foreach ($currencies as $item) {
                    ?>
                      <option value='<?= $item['id'] ?>' <?= $currency == $item['id'] ? "selected" : ""; ?>><?= $item['currency_code'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </li>
            </ul>
          </div>
          <!-- ***** Language change and login section End ***** -->
        </div>
      </div>
    </div>
    <!-- ***** Primary header section End ***** -->

    <!-- ***** secondary header section ***** -->
    <div class="header-secondary">
      <div class="container-lg">
        <nav class="navbar navbar-expand-md navbar-light">
          <a class="navbar-brand" href="<?= BASE_URL_B2C ?>">
            <img src="<?php echo $admin_logo_url; ?>" alt="logo" />
          </a>

          <div
            class="collapse navbar-collapse justify-content-end"
            id="navbarSupportedContent"
            style="display: block">
            <?php
            /**
             * @var 
             * Get headers menu from app_settings
             */
            $menuOptions = $headerMenu ? json_decode($headerMenu, true) : [];
            $groupTours = $themeData->getGroupTourDropDownData();
            ?>
            <ul class="menuBar">
              <?php
              foreach ($menuOptions as $menu) {
                $menuKey = $menu;
                if (preg_match("#_#", $menu)) {
                  $menu = str_replace('_', ' ', $menu);
                }
              ?>
                <li class="menuBar-item">
                  <?php if ($menuKey !== 'group_tours' && $menuKey !== 'holiday'):
                    $menuLink = "#";
                    $clickEvent = null;
                    if ($menuKey === 'home') {
                      $menuLink = "index.php";
                    } else if ($menuKey === 'activities') {
                      $menuLink = "view/activities/activities-listing.php";
                      $clickEvent = "get_tours_data('','4')";
                    } else if ($menuKey === 'visa') {
                      $menuLink = "view/visa/visa-listing.php";
                      $clickEvent = "get_tours_data('','6')";
                    } else if ($menuKey === 'hotels') {
                      $menuLink = "view/hotel/hotel-listing.php";
                      $clickEvent = "get_tours_data('','3')";
                    } else if ($menuKey === 'transfer') {
                      $menuLink = "view/transfer/transfer-listing.php";
                      $clickEvent = "get_tours_data('','5')";
                    } else if ($menuKey === 'cruise') {
                      $menuLink = "view/ferry/ferry-listing.php";
                      $clickEvent = "get_tours_data('','7')";
                    } else if ($menuKey === 'services') {
                      $menuLink = "services.php";
                    } else if (preg_match('#contact_us#', $menuKey)) {
                      $menuLink = "contact.php";
                    } else if ($menuKey === 'offers') {
                      $menuLink = "offers.php";
                    }


                  ?>
                    <a href="<?php echo $menuLink; ?>" class="link <?= $menu === 'home' ? 'active' : '' ?>" <?php if ($clickEvent): ?>onclick=" <?= $clickEvent; ?>" <?php endif; ?>><?php echo ucwords($menu); ?></a>
                  <?php else: ?>

                    <?php if ($menuKey == 'group_tours') {

                      list(
                        $domesticGroupTours,
                        $internationalGroupTours
                      ) = $themeData->hydrateGroupTourDropDownData($groupTours);

                    ?>
                      <div class="dropdown subMenus">
                        <button
                          class="dropdown-toggle link"
                          type="button"
                          data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <?php echo ucwords($menu); ?>
                        </button>
                        <div class="subMenu-wrapper">
                          <ul class="dropdown-menu">
                            <?php if (count($domesticGroupTours) > 0): ?>
                              <li class="has-submenu">
                                <a
                                  href="javascript:void(0)"
                                  class="link d-block p-0 pb-2 pt-2 text-secondary">Domestic</a>

                                <ul class="submenu">
                                  <?php foreach ($domesticGroupTours as $tour) { ?>
                                    <li>
                                      <a class="link d-block p-0 pb-2 pt-2 text-secondary" onclick="get_tours_data('<?= $tour['dest_id'] ?>','2')">
                                        <?= htmlspecialchars($tour['dest_name']) ?>
                                      </a>
                                    </li>
                                  <?php } ?>
                                </ul>
                              </li>
                            <?php endif; ?>
                            <?php if (count($internationalGroupTours) > 0): ?>
                              <li class="has-submenu">
                                <a
                                  href="javascript:void(0)"
                                  class="link d-block p-0 pb-2 pt-2 text-secondary">International</a>
                                <ul class="submenu">
                                  <?php foreach ($internationalGroupTours as $tour) { ?>
                                    <li>
                                      <a class="link d-block p-0 pb-2 pt-2 text-secondary" onclick="get_tours_data('<?= $tour['dest_id'] ?>','2')">
                                        <?= htmlspecialchars($tour['dest_name']) ?>
                                      </a>
                                    </li>
                                  <?php } ?>
                                </ul>
                              </li>
                            <?php endif; ?>
                          </ul>
                        </div>
                      </div>
                    <?php } ?>

                    <?php if ($menuKey == 'holiday') {

                      $packageTours = $themeData->getHolidayPackagesDropDownData();
                      list($domesticPackageTours, $internationalPackageTours) = $themeData->hydratePackageTourDropDownData($packageTours);
                    ?>
                      <div class="dropdown subMenus">
                        <button
                          class="dropdown-toggle link"
                          type="button"
                          data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <?php echo ucwords($menu); ?>
                        </button>
                        <div class="subMenu-wrapper">
                          <ul class="dropdown-menu">
                            <?php if (count($domesticPackageTours) > 0): ?>
                              <li class="has-submenu">
                                <a
                                  href="javascript:void(0)"
                                  class="link d-block p-0 pb-2 pt-2 text-secondary">Domestic</a>

                                <ul class="submenu">
                                  <?php foreach ($domesticPackageTours as $tour) { ?>
                                    <li>
                                      <a class="link d-block p-0 pb-2 pt-2 text-secondary" onclick="get_tours_data('<?= $tour['dest_id'] ?>','1')">
                                        <?= htmlspecialchars($tour['dest_name']) ?>
                                      </a>
                                    </li>
                                  <?php } ?>
                                </ul>
                              </li>
                            <?php endif; ?>
                            <?php if (count($internationalPackageTours) > 0): ?>
                              <li class="has-submenu">
                                <a
                                  href="javascript:void(0)"
                                  class="link d-block p-0 pb-2 pt-2 text-secondary">International</a>

                                <ul class="submenu">
                                  <?php foreach ($internationalPackageTours as $tour) { ?>
                                    <li>
                                      <a class="link d-block p-0 pb-2 pt-2 text-secondary" onclick="get_tours_data('<?= $tour['dest_id'] ?>','1')">
                                        <?= htmlspecialchars($tour['dest_name']) ?>
                                      </a>
                                    </li>
                                  <?php } ?>
                                </ul>
                              </li>
                            <?php endif; ?>
                          </ul>
                        </div>
                      </div>
                    <?php } ?>
                  <?php endif; ?>
                </li>
              <?php } ?>
            </ul>
          </div>
        </nav>
      </div>
    </div>
    <!-- ***** secondary header section ***** -->
  </header>
  <!-- ***** Header End ***** -->

  <!-- ***** Mobile Header ***** -->
  <header class="c-headerMobile">
    <button
      class="hambMenu"
      type="button"
      data-bs-toggle="offcanvas"
      data-bs-target="#mobileSidebar"
      aria-controls="mobileSidebar">
      <i class="fa-sharp-duotone fa-solid fa-list"></i>
    </button>
    <div class="logo">
      <a href="<?php echo BASE_URL_B2C; ?>">
        <img src="<?php echo $admin_logo_url; ?>" alt="logo" />
      </a>
    </div>
    <div class="setting text-end">
      <a
        href="<?php echo BASE_URL . "view/customer/index.php"; ?>"
        class="fs-6 text-white link-underline link-underline-opacity-0 fw-medium">Login</a>
    </div>

    <!-- Mobile sideMenu -->
    <div
      class="offcanvas offcanvas-start mobileSidebarMenu"
      tabindex="-1"
      id="mobileSidebar"
      aria-labelledby="mobileSidebarLabel">
      <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="mobileSidebarLabel">Menu</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="offcanvas"
          aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <div class="d-flex gap-3">
          <div class="flex-grow-1 ps-3">
            <div class="c-advanceSelect transparent">
              <select class="js-advanceSelect notranslate select2 " translate="no" name="state" id="lang-select2">
              </select>
            </div>
          </div>
          <div class="flex-grow-1 pe-3">
            <div class="c-advanceSelect transparent">
              <select class="js-advanceSelect" name="state">
                <?php foreach ($currencies as $item) { ?>
                  <option value='<?= $item['id'] ?>' <?= $currency == $item['id'] ? "selected" : ""; ?>><?= $item['currency_code'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>

        <hr />

        <ul class="list-group list-group-flush">
          <?php
          // $menuOptions = $headerMenu ? json_decode($headerMenu, true) : [];
          // $groupTours = $themeData->getGroupTourDropDownData();
          foreach ($menuOptions as $menu) {
            $menuKey = $menu;
            if (preg_match("#_#", $menu)) {
              $menu = str_replace('_', ' ', $menu);
            }
            $menu = ucwords($menu);
            if ($menuKey !== 'group_tours' && $menuKey !== 'holiday') {
              $menuLink = "#";
              $clickEvent = null;
              if ($menuKey === 'home') {
                $menuLink = "index.php";
                $icon = 'fa-house';
              } else if ($menuKey === 'activities') {
                $menuLink = "view/activities/activities-listing.php";
                $icon = 'fa-briefcase';
                $clickEvent = "get_tours_data('','4')";
              } else if ($menuKey === 'visa') {
                $menuLink = "view/visa/visa-listing.php";
                $icon = 'fa-passport';
                $clickEvent = "get_tours_data('','6')";
              } else if ($menuKey === 'hotels') {
                $menuLink = "view/hotel/hotel-listing.php";
                $icon = 'fa-hotel';
                $clickEvent = "get_tours_data('','3')";
              } else if ($menuKey === 'transfer') {
                $menuLink = "view/transfer/transfer-listing.php";
                $icon = 'fa-car';
                $clickEvent = "get_tours_data('','5')";
              } else if ($menuKey === 'cruise') {
                $menuLink = "view/ferry/ferry-listing.php";
                $icon = 'fa-ship';
                $clickEvent = "get_tours_data('','7')";
              } else if ($menuKey === 'services') {
                $menuLink = "services.php";
                $icon = 'fa-cog';
              } else if (preg_match('#contact_us#', $menuKey)) {
                $menuLink = "contact.php";
                $icon = 'fa-envelope';
              } else if ($menuKey === 'offers') {
                $menuLink = "offers.php";
                $icon = 'fa-tags';
              }
          ?>
              <a href="<?php echo $menuLink; ?>" class="list-group-item list-group-item-action link <?= $menu === 'home' ? 'active' : '' ?>" <?php if ($clickEvent): ?>onclick=" <?= $clickEvent; ?>" <?php endif; ?>><i class="fa-solid <?= $icon ?> me-2 text-body-tertiary fs-8"></i>
                <span class="fs-7 fw-medium"> <?= $menu ?> </span></a>
              <?php } else {
              if ($menuKey == 'group_tours') {

                list(
                  $domesticGroupTours,
                  $internationalGroupTours
                ) = $themeData->hydrateGroupTourDropDownData($groupTours);

                $icon = 'fa-users';
              ?>
                <div class="subMenu">
                  <button
                    class="list-group-item list-group-item-action"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseExample"
                    aria-expanded="false"
                    aria-controls="collapseExample">
                    <i
                      class="fa-solid <?= $icon ?> me-2 text-body-tertiary fs-8"></i>
                    <span class="fs-7 fw-medium"> <?= $menu ?></span>
                    <i class="fa-solid fa-caret-down ms-2 fs-8"></i>
                  </button>
                  <div class="collapse" id="collapseExample">
                    <div class="card card-body">
                      <ul class="menu-list">
                        <?php if (count($domesticGroupTours) > 0) { ?>
                          <li class="menu-item has-mobileSubmenu">
                            <a
                              href="javascript:void(0)"
                              class="menu-link link d-block p-0 pb-2 pt-2 text-secondary">Domestic</a>
                            <ul class="mobileSubmenu">

                              <?php foreach ($domesticGroupTours as $tour) { ?>
                                <li>
                                  <a class="mobileSubmenu-link" onclick="get_tours_data('<?= $tour['dest_id'] ?>','2')">
                                    <span class="fs-7 fw-medium"> <?= htmlspecialchars($tour['dest_name']) ?></span>
                                  </a>
                                </li>
                              <?php } ?>
                            </ul>
                          </li>
                        <?php } ?>
                        <?php if (count($internationalGroupTours) > 0) { ?>
                          <li class="menu-item has-mobileSubmenu">
                            <a
                              href="javascript:void(0)"
                              class="menu-link link d-block p-0 pb-2 pt-2 text-secondary">International</a>
                            <ul class="mobileSubmenu">
                              <?php foreach ($internationalGroupTours as $tour) { ?>
                                <li>
                                  <a class="mobileSubmenu-link" onclick="get_tours_data('<?= $tour['dest_id'] ?>','2')">
                                    <span class="fs-7 fw-medium"> <?= htmlspecialchars($tour['dest_name']) ?></span>
                                  </a>
                                </li>
                              <?php } ?>
                            </ul>
                          </li>
                        <?php } ?>
                      </ul>
                    </div>
                  </div>
                </div>
              <?php }
              ?>
              <?php if ($menuKey == 'holiday') {

                list(
                  $domesticPackageTours,
                  $internationalPackageTours
                ) = $themeData->hydratePackageTourDropDownData($packageTours);

                $icon = 'fa-umbrella-beach';
              ?>

                <div class="subMenu">
                  <button
                    class="list-group-item list-group-item-action"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseExample1"
                    aria-expanded="false"
                    aria-controls="collapseExample1">
                    <i
                      class="fa-solid <?= $icon ?> me-2 text-body-tertiary fs-8"></i>
                    <span class="fs-7 fw-medium"> <?= $menu ?></span>
                    <i class="fa-solid fa-caret-down ms-2 fs-8"></i>
                  </button>
                  <div class="collapse" id="collapseExample1">
                    <div class="card card-body">
                      <ul class="menu-list">
                        <?php if (count($domesticPackageTours) > 0) { ?>
                          <li class="menu-item has-mobileSubmenu">
                            <a
                              href="javascript:void(0)"
                              class="menu-link link d-block p-0 pb-2 pt-2 text-secondary">Domestic</a>
                            <ul class="mobileSubmenu">

                              <?php foreach ($domesticPackageTours as $tour) { ?>
                                <li>
                                  <a class="mobileSubmenu-link" onclick="get_tours_data('<?= $tour['dest_id'] ?>','1')">
                                    <span class="fs-7 fw-medium"> <?= htmlspecialchars($tour['dest_name']) ?></span>
                                  </a>
                                </li>
                              <?php } ?>
                            </ul>
                          </li>
                        <?php } ?>
                        <?php if (count($internationalPackageTours) > 0) { ?>
                          <li class="menu-item has-mobileSubmenu">
                            <a
                              href="javascript:void(0)"
                              class="menu-link link d-block p-0 pb-2 pt-2 text-secondary">International</a>
                            <ul class="mobileSubmenu">
                              <?php foreach ($internationalPackageTours as $tour) { ?>
                                <li>
                                  <a class="mobileSubmenu-link" onclick="get_tours_data('<?= $tour['dest_id'] ?>','1')">
                                    <span class="fs-7 fw-medium"> <?= htmlspecialchars($tour['dest_name']) ?></span>
                                  </a>
                                </li>
                              <?php } ?>
                            </ul>
                          </li>
                        <?php } ?>
                      </ul>
                    </div>
                  </div>
                </div>
          <?php
              }
            }
          }  ?>
          <a href="<?php echo BASE_URL . "view/customer/index.php"; ?>" class="list-group-item list-group-item-action">
            <i class="fa-solid fa-lock me-2 text-body-tertiary fs-8"></i>
            <span class="fs-7 fw-medium"> Login </span>
          </a>
        </ul>

        <hr />
        <div class="d-flex flex-row g-2 p-1">
          <span class="fs-8 fw-medium flex-grow-1 text-center" id="appPhoneMobileMenu"><i class="fa-solid fa-phone me-1"></i> <a href="tel:<?php echo $app_contact_no; ?>" style="color:black;"> <?php echo $app_contact_no; ?></a></span>
          <span class="fs-8 fw-medium flex-grow-1 text-center" id="appEmailMobileMenu"><i class="fa-solid fa-envelope me-1"></i> <a href="mailto:<?= $app_email_id_send ?>" style="color:black;"><?php echo $app_email_id_send; ?></a></span>
        </div>
        <hr />
        <div
          class="d-flex justify-content-center align-items-center gap-3 hstack"
          style="height: 21px">
          <?php foreach ($socialIcons as $icon): ?>
            <?php if ($icon['fb']) { ?>
              <a href="<?php echo $icon['fb']; ?>" class="fs-5 link-underline link-underline-opacity-0 fw-medium text-secondary" target="_blank">
                <i class="fab fa-facebook-square"></i>
              </a>
            <?php } ?>
            <?php if ($icon['tw']) { ?>
              <a href="<?php echo $icon['tw']; ?>" class="fs-5 link-underline link-underline-opacity-0 fw-medium text-secondary" target="_blank">
                <i class="fab fa-twitter-square"></i>
              </a>
            <?php } ?>
            <?php if ($icon['inst']) { ?>
              <a href="<?php echo $icon['inst']; ?>" class="fs-5 link-underline link-underline-opacity-0 fw-medium text-secondary" target="_blank">
                <i class="fab fa-instagram"></i>
              </a>
            <?php } ?>
            <?php if ($icon['li']) { ?>
              <a href="<?php echo $icon['li']; ?>" class="fs-5 link-underline link-underline-opacity-0 fw-medium text-secondary" target="_blank">
                <i class="fab fa-linkedin"></i>
              </a>
            <?php } ?>
            <?php if ($icon['wa']) { ?>
              <a href="<?php echo $icon['wa']; ?>" class="fs-5 link-underline link-underline-opacity-0 fw-medium text-secondary" target="_blank">
                <i class="fa-brands fa-whatsapp"></i>
              </a>
            <?php } ?>
            <?php if ($icon['yu']) { ?>
              <a href="<?php echo $icon['yu']; ?>" class="fs-5 link-underline link-underline-opacity-0 fw-medium text-secondary" target="_blank">
                <i class="fa-brands fa-youtube"></i>
              </a>
            <?php } ?>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </header>
  <input type="hidden" id='cache_currencies' value='<?= $currency_data ?>' />
  <!-- ***** Mobile Header End ***** -->