<?php
include "config.php";
global $app_contact_no;
$b = 'base6' . '4_decode';
$service = $_GET['service'];
$_SESSION['page_type'] = 'index';
include 'layouts/header.php'; //Include header
?>
<!-- *** Banner slider *** -->
<section class="c-bannerAndFilter with-slider">
  <div class="c-banner type-01"><!-- YouTube Video Background -->
    <div class="video-wrapper position-absolute top-0 start-0 w-100 h-100">
      <iframe class="yt-video"
        src="https://www.youtube.com/embed/Wcd6r97fOgo?autoplay=1&mute=1&controls=0&showinfo=0&loop=1&playlist=Wcd6r97fOgo&rel=0&modestbranding=1"
        frameborder="0"
        allow="autoplay; fullscreen"
        allowfullscreen
        title="Holiday Tour Video"></iframe>
    </div>
    <!-- *** Slider End *** -->
  </div>
</section>
<!-- *** Banner slider End *** -->

<!-- ***** Filter Section ***** -->
<section class="c-filter">
  <div class="container-lg">
    <div class="row align-items-center">
      <div class="col-12">
        <div class="filterWrapper">
          <!-- ***** Filter Tabs ***** -->
          <div class="c-filterTabs">
            <ul class="nav nav-tabs parentNav" id="myTab" role="tablist">

              <li class="nav-item" role="presentation">
                <button
                  class="nav-link active filterButton fs-7"
                  id="holiday-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#holiday-tab-pane"
                  type="button"
                  role="tab"
                  aria-controls="holiday-tab-pane"
                  aria-selected="false">
                  <i class="fa-solid fa-umbrella-beach me-2 me-2 fs-8"></i>
                  <span class="fw-medium">Holiday</span>
                </button>
              </li>

              <li class="nav-item" role="presentation">
                <button
                  class="nav-link filterButton fs-7"
                  id="groupTour-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#groupTour-tab-pane"
                  type="button"
                  role="tab"
                  aria-controls="groupTour-tab-pane"
                  aria-selected="false">
                  <i class="fa-solid fa-users me-2 me-2 fs-8"></i>
                  <span class="fw-medium">Group Tour</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button
                  class="nav-link filterButton fs-7"
                  id="hotel-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#hotel-tab-pane"
                  type="button"
                  role="tab"
                  aria-controls="hotel-tab-pane"
                  aria-selected="true">
                  <i class="fa-solid fa-hotel me-2 fs-8"></i>
                  <span class="fw-medium">Hotel</span>
                </button>
              </li>
              <!-- <li class="nav-item" role="presentation">
                <button
                  class="nav-link filterButton fs-7"
                  id="flight-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#flight-tab-pane"
                  type="button"
                  role="tab"
                  aria-controls="flight-tab-pane"
                  aria-selected="false">
                  <i class="fa-solid fa-plane-departure me-2 fs-8"></i>
                  <span class="fw-medium">Flight</span>
                </button>
              </li> -->

              <li class="nav-item" role="presentation">
                <button
                  class="nav-link filterButton fs-7"
                  id="activity-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#activity-tab-pane"
                  type="button"
                  role="tab"
                  aria-controls="activity-tab-pane"
                  aria-selected="false">
                  <i class="fa-solid fa-sailboat me-2 me-2 fs-8"></i>
                  <span class="fw-medium">Activity</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button
                  class="nav-link filterButton fs-7"
                  id="transfer-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#transfer-tab-pane"
                  type="button"
                  role="tab"
                  aria-controls="transfer-tab-pane"
                  aria-selected="false">
                  <i class="fa-solid fa-car me-2 me-2 fs-8"></i>
                  <span class="fw-medium">Transfer</span>
                </button>
              </li>
            </ul>
            <div class="tab-content" id="myTabContent">
              <!-- ***** Flight ***** -->
              <!-- <div
                class="tab-pane fade"
                id="flight-tab-pane"
                role="tabpanel"
                aria-labelledby="flight-tab"
                tabindex="0">
                <?php //include 'view/flight/flight-search.php'; 
                ?>
              </div> -->
              <!-- ***** Flight End ***** -->

              <!-- ***** Hotel ***** -->
              <div
                class="tab-pane fade"
                id="hotel-tab-pane"
                role="tabpanel"
                aria-labelledby="hotel-tab"
                tabindex="0">
                <?php include 'view/hotel/hotel-search.php'; ?>
              </div>
              <!-- ***** Hotel End ***** -->

              <!-- ***** Group Tour ***** -->
              <div
                class="tab-pane fade"
                id="groupTour-tab-pane"
                role="tabpanel"
                aria-labelledby="groupTour-tab"
                tabindex="0">
                <?php include 'view/group_tours/tours-search.php'; ?>
              </div>
              <!-- ***** Group Tour End ***** -->

              <!-- ***** Holiday Tour ***** -->
              <div
                class="tab-pane fade show active"
                id="holiday-tab-pane"
                role="tabpanel"
                aria-labelledby="holiday-tab"
                tabindex="0">
                <?php include 'view/tours/tours-search.php'; ?>
              </div>
              <!-- ***** Holiday Tour ***** -->

              <!-- ***** Activity Tour ***** -->
              <div
                class="tab-pane fade"
                id="activity-tab-pane"
                role="tabpanel"
                aria-labelledby="activity-tab"
                tabindex="0">
                <?php include 'view/activities/activities-search.php'; ?>
              </div>
              <!-- ***** Activity Tour End ***** -->

              <!-- ***** Transfer Tour ***** -->
              <div
                class="tab-pane fade"
                id="transfer-tab-pane"
                role="tabpanel"
                aria-labelledby="transfer-tab"
                tabindex="0">
                <?php include 'view/transfer/transfer-search.php'; ?>
              </div>
              <!-- ***** Activity Tour End ***** -->
            </div>
          </div>
          <!-- ***** Filter Tabs End ***** -->
        </div>
      </div>
    </div>
  </div>
</section>
<!-- ***** Trending Tours Slider Section ***** -->
<?php
$popularPackages = $themeData->getPopularPackages();
if ($popularPackages && count($popularPackages) > 0) {
?>
  <section class="c-section type-1">
    <div class="container-lg" id="season-spcl">
      <div class="row align-items-center">
        <div class="col-12">
          <div class="heading">
            <h2 class="span">Trending Tours</h2>
            <div class="image"></div>
          </div>

          <!-- *** Card Slider *** -->
          <div class="owl-carousel c-slider oddEven js-trendingTours">
            <?php foreach ($popularPackages as $package) :
              // echo '<pre.'; var_dump($package);
              $pricing = ($package['tariff']['cadult'])
                ?  $themeData->convertCurrency($package['tariff']['cadult'], $currency)  : '0.00';
            ?>
              <!-- Card -->
              <div class="card c-card" title="<?php echo $package['package_name'];  ?>">
                <div class="card-image">
                  <img src="<?php echo $package['main_img_url']; ?>" alt="..." />
                  <span class="title"><?php echo $package['tour_type']; ?></span>
                </div>
                <div class="card-body">
                  <h5
                    class="card-title mb-2 fs-6 text-center color-primary font-family-secondary-semibold">
                    <?php
                    if ((strlen($package['package_name']) > 30))
                      echo substr($package['package_name'], 0, length: 30) . "...";
                    else
                      echo $package['package_name'];
                    ?>
                  </h5>
                  <span
                    class="d-block card-title mb-3 fs-7 text-center text-secondary">
                    <?php echo $package['total_nights']; ?> nights & <?php echo $package['total_days']; ?> days
                  </span>
                  <span
                    class="d-block card-title mb-3 fs-7 text-center text-secondary">
                    <i class="fa-solid fa-location-dot me-1"></i> <?php echo $package['destination']['dest_name']; ?>
                  </span>
                  <div
                    class="d-flex justify-content-center align-items-center gap-2 mb-3">
                    <span class="fs-8 text-secondary d-block">
                      Price Per Person
                    </span>
                    <span class="fs-5 font-family-secondary-bold color-primary">
                      <?php echo $pricing; ?>
                    </span>
                  </div>
                  <div class="text-center">
                    <!-- <button class="c-button btn rounded">View Details</button> -->
                    <a class="c-button btn rounded" href="<?php echo BASE_URL_B2C; ?><?php echo $package['seo_slug']; ?>">
                      View Details
                    </a>
                  </div>
                </div>
              </div>
              <!-- Card End -->
            <?php endforeach; ?>
          </div>
          <!-- *** Card Slider End *** -->
        </div>
      </div>
    </div>
  </section>
<?php } ?>
<!-- ***** Trending Tours Slider Section End ***** -->
>

<!-- ***** Popular Activities Slider Section ***** -->
<?php
$popularActivities = $themeData->getPopularActivities();
if ($popularActivities && count($popularActivities) > 0) {
?>
  <section class="c-section type-1 popularActivities">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-12">
          <div class="heading">
            <h2 class="span">Popular Activities</h2>
            <div class="image"></div>
          </div>

          <!-- *** Card Slider *** -->
          <div class="owl-carousel c-slider oddEven js-activities">
            <?php foreach ($popularActivities as $activity) :
              $pricing = ($activity['basics']->adult_cost && $activity['basics']->adult_cost !== 'On Req')
                ? $themeData->convertCurrency($activity['basics']->adult_cost, $currency)
                : $activity['basics']->adult_cost;
              $activityImg = ($activity['main_img_url']) ? htmlspecialchars($activity['main_img_url']) : BASE_URL_B2C . '/images/activity_default.png';
            ?>
              <!-- Card -->
              <div class="card c-card" title="<?php echo htmlspecialchars($activity['excursion_name']); ?>">
                <div class="card-image">
                  <img src="<?php echo $activityImg; ?>" alt="<?php echo htmlspecialchars($activity['excursion_name']); ?>" />
                  <span class="title"><?php echo $activity['duration'] ? $activity['duration'] : 'NA'; ?></span>
                </div>
                <div class="card-body">
                  <h5 class="card-title mb-2 fs-6 text-center color-primary font-family-secondary-semibold">
                    <?php
                    echo (strlen($activity['excursion_name']) > 30)
                      ? substr($activity['excursion_name'], 0, 30) . "..."
                      : $activity['excursion_name'];
                    ?>
                  </h5>
                  <span class="d-block card-title mb-3 fs-7 text-center text-secondary">
                    <i class="fa-solid fa-location-dot me-1"></i>
                    <?php
                    echo (strlen($activity['city_details']['city_name']) > 30)
                      ? substr($activity['city_details']['city_name'], 0, 30) . "..."
                      : $activity['city_details']['city_name'];
                    ?>
                  </span>
                  <div class="d-flex justify-content-center align-items-center gap-2 mb-3">
                    <span class="fs-8 text-secondary d-block">Per Person</span>
                    <span class="fs-5 font-family-secondary-bold color-primary">
                      <?php echo $pricing ? $pricing : 'On Request'; ?>
                    </span>
                  </div>
                  <div class="text-center">
                    <a class="c-button btn rounded" onclick="get_act_listing_page('<?php echo $activity['entry_id']; ?>')">
                      View Details
                    </a>
                  </div>
                </div>
              </div>
              <!-- Card End -->
            <?php endforeach; ?>
          </div>
          <!-- *** Card Slider End *** -->
        </div>
      </div>
    </div>
  </section>
<?php } ?>
<!-- ***** Popular Activities Slider Section End ***** -->
<!-- ***** Top Hotels Slider Section ***** -->
<?php
$recommendedHotels = $themeData->getPopularHotels();
if ($recommendedHotels && count($recommendedHotels) > 0) {
?>
  <section class="c-section topHotels">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-12">
          <div class="heading">
            <h2 class="span">Top Hotels</h2>
            <div class="image"></div>
          </div>
          <!-- *** Hotel Slider single  *** -->
          <div class="container-lg">
            <div class="owl-carousel c-slider js-ctaSlider">
              <!-- Card -->
              <?php foreach ($recommendedHotels as $hotel) :
                $ratingStars = '';
                $starValue = 0;
                if (preg_match("#Star#", $hotel['rating_star'])) {
                  list($starValue, $stringVal) = explode("Star", $hotel['rating_star']);
                  $starValue = trim($starValue);
                }
                for ($i = 0; $i < $starValue; $i++) {
                  $ratingStars .= '<i class="fa-solid fa-star fs-10 text-warning" style=" text-shadow: 0 0 3px #000;"></i>';
                }
                $pricing =  $hotel['double_bed'] ? $themeData->convertCurrency($hotel['double_bed'], $currency) : '0.00';
              ?>
                <div class="row align-items-center">
                  <div class="col-md-6 col-sm-12 order-2 order-md-1">
                    <h5
                      class="card-title mb-2 fs-3 color-primary font-family-secondary-bold">
                      <?php echo $hotel['hotel_name']; ?>
                    </h5>

                    <div class="with-divider d-flex flex-row mb-4">
                      <span
                        class="color-primary fs-7">
                        <i class="fa-solid fa-location-dot me-1"></i> <?php echo $hotel['hotel_address']; ?>
                      </span>
                    </div>
                    <div class="d-flex flex-row gap-3 mb-4">
                      <?php
                      if (!empty($hotel['amenities'])) {
                        $items = explode(",", $hotel['amenities']);
                        if ($items[0] != '') { ?>
                          <script>
                            setTimeout(function() {
                              var ameities = getObjectsData(amenities, 'name', '<?php echo $items[0]; ?>');
                              document.getElementById("amenity1<?= $hotel['hotel_id']; ?>").src = 'crm/Tours_B2B/images/amenities/' + ameities[0]['image'];
                            }, 5000);
                          </script>
                          <div class="itenary text-center align-items-center justify-content-center d-flex flex-column gap-2">
                            <div class="icon mb-2 bg-color-secondary">
                              <img id='amenity1<?= $hotel['hotel_id']; ?>' alt="" width='32' height='32' />
                            </div>
                            <span class="fs-7 fw-medium"> <?php echo $items[0]; ?></span>
                          </div>
                        <?php }
                        if ($items[1] != '') { ?>
                          <script>
                            setTimeout(function() {
                              var ameities = getObjectsData(amenities, 'name', '<?php echo $items[1]; ?>');
                              document.getElementById("amenity2<?= $hotel['hotel_id']; ?>").src = 'crm/Tours_B2B/images/amenities/' + ameities[0]['image'];
                            }, 5000);
                          </script>
                          <div class="itenary text-center align-items-center justify-content-center d-flex flex-column gap-2">
                            <div class="icon mb-2 bg-color-secondary">
                              <img id='amenity2<?= $hotel['hotel_id']; ?>' alt="" width='32' height='32' />
                            </div>
                            <span class="fs-7 fw-medium"> <?php echo $items[1]; ?></span>
                          </div>
                        <?php }
                        if ($items[2] != '') { ?>
                          <script>
                            setTimeout(function() {
                              var ameities = getObjectsData(amenities, 'name', '<?php echo $items[2]; ?>');
                              document.getElementById("amenity3<?= $hotel['hotel_id']; ?>").src = 'crm/Tours_B2B/images/amenities/' + ameities[0]['image'];
                            }, 5000);
                          </script>
                          <div class="itenary text-center align-items-center justify-content-center d-flex flex-column gap-2">
                            <div class="icon mb-2 bg-color-secondary">
                              <img id='amenity3<?= $hotel['hotel_id']; ?>' alt="" width='32' height='32' />
                            </div>
                            <span class="fs-7 fw-medium"> <?php echo $items[2]; ?></span>
                          </div>
                        <?php }
                      } else { ?>
                        <div class="d-flex flex-row gap-3 mb-4">
                          <div
                            class="itenary text-center align-items-center justify-content-center d-flex flex-column gap-2">
                            <div class="icon mb-2 bg-color-secondary">
                              <i class="fa-solid fa-wifi"></i>
                            </div>
                            <span class="fs-7 fw-medium">Free Wifi</span>
                          </div>
                          <div
                            class="itenary text-center align-items-center justify-content-center d-flex flex-column gap-2">
                            <div class="icon mb-2 bg-color-secondary">
                              <i class="fa-solid fa-water-ladder"></i>
                            </div>
                            <span class="fs-7 fw-medium">Swimming Pool</span>
                          </div>
                          <div
                            class="itenary text-center align-items-center justify-content-center d-flex flex-column gap-2">
                            <div class="icon mb-2 bg-color-secondary">
                              <i class="fa-solid fa-utensils"></i>
                            </div>
                            <span class="fs-7 fw-medium">Breakfast</span>
                          </div>
                        </div>
                      <?php } ?>
                    </div>

                    <span
                      class="fs-6 text-secondary font-family-secondary-medium">
                      Room Cost
                    </span>

                    <div class="d-flex align-items-center gap-3 mb-4">
                      <span
                        class="fs-4 font-family-secondary-bold color-primary">
                        <?php echo $pricing; ?>
                        <sup class="fs-6 text-secondary">*</sup>
                      </span>
                    </div>
                    <button class="c-button btn rounded align-items-center d-flex" onclick="get_hotel_listing_page('<?= $hotel['hotel_id']; ?>')">View Details<i class="fa-solid fa-circle-arrow-right ms-2 fs-5"></i>
                    </button>
                  </div>
                  <div
                    class="col-md-6 col-sm-12 order-1 order-md-2 mb-3 mb-md-3">
                    <div class="card-image position-relative">
                      <img
                        src="<?= $hotel['main_img']; ?>"
                        alt="<?php echo $hotel['hotel_type']; ?>"
                        class="c-image sh-oval bordered" />
                      <div class="rating d-flex flex-row align-items-center justify-content-center">
                        <span class="badge text-white position-absolute m-2 shadow-sm hoteltype-badge">
                          <?php echo $hotel['hotel_type']; ?>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Card End -->
              <?php endforeach; ?>
            </div>
          </div>
          <!-- *** Hotel Slider single End  *** -->
        </div>
      </div>
    </div>
  </section>
<?php } ?>
<!-- ***** Top Hotels Slider Section End ***** -->

<!-- ***** Our Story Section ***** -->
<section class=" c-section ourStory">
  <div class="container-lg">
    <div class="row gx-md-5">
      <div class="col-md-6 col-sm-12">
        <div class="cta mb-3 mb-md-0">
          <div class="cta-info">
            <span
              class="fs-3 color-primary font-family-secondary-bold d-block mb-3">Our Story</span>
            <span class="fs-7 mb-3 d-block color-primary">At <?php echo $app_name; ?>, we take pride in crafting unforgettable travel experiences. Our customer's testimonials reflect the seamless journeys, personalized service, and incredible destinations we offer.With a strong focus on customer satisfaction, real-time support, and carefully selected destinations, we continue to raise the bar in the travel industry.</span>
            <button
              class="c-button btn rounded align-items-center d-flex primary" onclick="window.location.href='<?= BASE_URL_B2C ?>about.php'">
              View Details <i class="fa-solid fa-circle-arrow-right ms-2 fs-5"></i>
            </button>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <h3
          class="fs-2 font-family-secondary-bold d-block mb-3 color-primary mb-3">
          Why choose us
        </h3>
        <div class="d-flex flex-row align-items-center mb-4 gap-3">
          <div class="itenary">
            <div class="icon bg-color-secondary">
              <i class="fa-solid fa-plane"></i>
            </div>
          </div>
          <div class="flex-grow-1">
            <span class="fs-5 font-family-secondary-bold d-block">550+ Destinations</span>
          </div>
        </div>
        <div class="d-flex flex-row align-items-center mb-4 gap-3">
          <div class="itenary">
            <div class="icon">
              <i class="fa-solid fa-circle-check"></i>
            </div>
          </div>
          <div class="flex-grow-1">
            <span class="fs-5 font-family-secondary-bold d-block">Best price guaranteed</span>
          </div>
        </div>
        <div class="d-flex flex-row align-items-center mb-4 gap-3">
          <div class="itenary">
            <div class="icon bg-color-secondary">
              <i class="fa-solid fa-compass"></i>
            </div>
          </div>
          <div class="flex-grow-1">
            <span class="fs-5 font-family-secondary-bold d-block">Top quality experience</span>
          </div>
        </div>
        <div class="d-flex flex-row align-items-center mb-4 gap-3">
          <div class="itenary">
            <div class="icon">
              <i class="fa-solid fa-headset"></i>
            </div>
          </div>
          <div class="flex-grow-1">
            <span class="fs-5 font-family-secondary-bold d-block">Customer Support</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- ***** Our Story Section ***** -->
<?php
$testimonials = $themeData->getCustomerTestimonials(10);
?>
<!-- ***** Happy Customers Slider Section ***** -->
<section class="c-section">
  <div class="container-lg">
    <div class="row align-items-center">
      <div class="col-12">
        <div class="heading">
          <h2 class="span">Happy Customers</h2>
          <div class="image"></div>
        </div>
        <!-- *** Card Slider *** -->
        <div
          class="owl-carousel c-slider js-testimonials customerCard-oddEven">
          <!-- Card -->
          <?php if ($testimonials && count($testimonials) > 0):
            foreach ($testimonials as $testimonial) { ?>
              <div class="c-customerCard">
                <div class="card-image">
                  <?php $cleanPath = str_replace('../../../', '/', $testimonial['image']); ?>
                  <img
                    src="<?= 'crm/' . $cleanPath; ?>"
                    alt="photo"
                    height="150"
                    width="150" />
                </div>
                <div class="card-body">
                  <h2 class="fs-5 d-block mb-2 font-family-secondary-bold">
                    <?= $testimonial['name']; ?>
                  </h2>
                  <h3 class="fs-7 d-block mb-3 color-secondary">
                    <?= $testimonial['designation']; ?>
                  </h3>
                  <p class="fs-7 d-block lh-lg mb-0">
                    <a class="text-black text-decoration-none" href="<?= BASE_URL_B2C ?>testimonials.php"><?= substr($testimonial['testm'], 0, length: 100) . "..."; ?></a>
                  </p>
                </div>
              </div>
              <!-- Card End -->
          <?php }
          endif; ?>
        </div>
        <!-- *** Card Slider End *** -->
      </div>
    </div>
  </div>
</section>
<!-- ***** Happy Customers Slider Section End ***** -->

<?php
$excitingGroupTours = $themeData->getPopularGroupTours();
if ($excitingGroupTours && count($excitingGroupTours) > 0) {
?>
  <!-- ***** Seasons special Slider Section ***** -->
  <section class="c-section type-1 overlayRight">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-12">
          <div class="heading">
            <h2 class="span">Group Tours Special</h2>
            <div class="image"></div>
          </div>
          <!-- *** Hotel Slider single  *** -->
          <div class="container-lg">
            <div class="owl-carousel c-slider js-ctaSlider">
              <!-- Card -->
              <?php foreach ($excitingGroupTours as $tour) {
                // echo '<pre>'; print_r($tour);
                $pricing =  $tour['adult_cost'] ? $themeData->convertCurrency($tour['adult_cost'], $currency) : '0.00';
              ?>
                <div class="row align-items-center">
                  <div class="col-md-6 col-sm-12">
                    <div class="image oval mb-3 mb-md-0">
                      <img src="<?= $tour['image_url']; ?>" alt="<?= $tour['tour_name']; ?>" />
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-12 ps-md-4">
                    <h5
                      class="card-title mb-3 fs-4 fs-md-3 color-primary font-family-secondary-bold">
                      <?php
                      echo (strlen($tour['tour_name']) > 30)
                        ? substr($tour['tour_name'], 0, 30) . "..."
                        : $tour['tour_name'];
                      ?>
                    </h5>

                    <div class="with-divider d-flex flex-row mb-4">
                      <?php if (strpos($tour['total_nights'], '|') !== false) {
                        $totalNight = explode('|', $tour['total_nights']);
                        $cityName = explode('|', $tour['city_name']);
                        $hotelName = explode('|', $tour['hotel_name']);
                        foreach ($totalNight as $key => $nt) { ?>
                          <span class="color-primary fs-7"><?= $nt ?> N <?= $cityName[$key]; ?> </span>
                          <?= (++$index < $total) ? ' &bull; ' : '' ?>
                        <?php }
                      } elseif (!empty($tour['total_nights'])) { ?>
                        <span class="color-primary fs-7"><?= $tour['total_nights'] ?> N <?= $tour['city_name'] ?></span>
                      <?php } else {
                      } ?>
                    </div>

                    <span class="fs-7 mb-4 text-ellipsis-3"><?= $tour['tour_note']; ?></span>

                    <div class="d-flex flex-row gap-4 mb-4">
                      <div
                        class="itenary text-center align-items-center justify-content-center d-flex flex-column gap-2">
                        <div class="icon mb-2 bg-color-secondary">
                          <i class="fa-solid fa-hotel"></i>
                        </div>
                        <span class="fs-7 fw-medium">Hotel</span>
                      </div>

                      <div
                        class="itenary text-center align-items-center justify-content-center d-flex flex-column gap-2">
                        <div class="icon mb-2 bg-color-secondary">
                          <i class="fa-solid fa-car"></i>
                        </div>
                        <span class="fs-7 fw-medium">Transfer</span>
                      </div>
                      <div
                        class="itenary text-center align-items-center justify-content-center d-flex flex-column gap-2">
                        <div class="icon mb-2 bg-color-secondary">
                          <i class="fa-solid fa-utensils"></i>
                        </div>
                        <span class="fs-7 fw-medium">Meals</span>
                      </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-4">
                      <span
                        class="fs-4 color-primary font-family-secondary-bold">
                        <?= $pricing; ?>
                      </span>

                      <span
                        class="fs-6 color-primary font-family-secondary-medium">
                        Per Person
                      </span>
                    </div>

                    <button class="c-button btn rounded align-items-center d-flex" onclick="window.location.href='<?php echo BASE_URL_B2C; ?><?php echo 'group-tour/' . $tour['seo_slug']; ?>'">
                      View Details
                      <i class="fa-solid fa-circle-arrow-right ms-2 fs-4"></i>
                    </button>
                  </div>
                </div>
              <?php } ?>
              <!-- Card End -->
            </div>
          </div>
          <!-- *** Hotel Slider single End  *** -->
        </div>
      </div>
    </div>
  </section>
  <!-- ***** Seasons special Slider Section End ***** -->
<?php } ?>
<!-- ***** Gallery ***** -->
<section class="c-section top-destinations">
  <div class="container-lg">
    <div class="row align-items-center">
      <div class="col-12">
        <div class="heading">
          <h2 class="span font-family-secondary type-2">
            Top Destinations
          </h2>
          <div class="image"></div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="imageGrid">
          <div class="imageGrid-item">
            <img src="./images/Singapore.webp" alt="Image 1" />
            <div class="imageGrid-item-info">
              <div class="text-center">
                <span
                  class="d-block mb-3 font-family-secondary-bold fs-5 text-white">Destination</span>
                <span
                  class="d-block mb-1 font-family-secondary fs-3 text-white">
                  Singapore
                </span>
                <span class="d-block font-family-secondary fs-5 text-white">
                  Merlion statue and cityscape
                </span>
              </div>
            </div>
          </div>
          <div class="imageGrid-item">
            <img src="./images/Thailand.webp" alt="Image 2" />
            <div class="imageGrid-item-info">
              <div class="text-center">
                <span
                  class="d-block mb-3 font-family-secondary-bold fs-5 text-white">Destination</span>
                <span
                  class="d-block mb-1 font-family-secondary fs-3 text-white">
                  Thailand
                </span>
                <span class="d-block font-family-secondary fs-5 text-white">
                  Bangkok Temple
                </span>
              </div>
            </div>
          </div>
          <div class="imageGrid-item">
            <img src="./images/Turkey.webp" alt="Image 3" />
            <div class="imageGrid-item-info">
              <div class="text-center">
                <span
                  class="d-block mb-3 font-family-secondary-bold fs-5 text-white">Destination</span>
                <span
                  class="d-block mb-1 font-family-secondary fs-3 text-white">
                  Turkey
                </span>
                <span class="d-block font-family-secondary fs-5 text-white">
                  Cappadocia
                </span>
              </div>
            </div>
          </div>
          <div class="imageGrid-item">
            <img src="./images/Paris.webp" alt="Image 4" />
            <div class="imageGrid-item-info">
              <div class="text-center">
                <span
                  class="d-block mb-3 font-family-secondary-bold fs-5 text-white">Destination</span>
                <span
                  class="d-block mb-1 font-family-secondary fs-3 text-white">
                  Paris
                </span>
                <span class="d-block font-family-secondary fs-5 text-white">
                  Eiffel Tower
                </span>
              </div>
            </div>
          </div>
          <div class="imageGrid-item">
            <img src="./images/Japanese.webp" alt="Image 4" />
            <div class="imageGrid-item-info">
              <div class="text-center">
                <span
                  class="d-block mb-3 font-family-secondary-bold fs-5 text-white">Destination</span>
                <span
                  class="d-block mb-1 font-family-secondary fs-3 text-white">
                  Japan
                </span>
                <span class="d-block font-family-secondary fs-5 text-white">
                  Sannen Zaka street
                </span>
              </div>
            </div>
          </div>
          <div class="imageGrid-item">
            <img src="./images/India.webp" alt="Image 5" />
            <div class="imageGrid-item-info">
              <div class="text-center">
                <span
                  class="d-block mb-3 font-family-secondary-bold fs-5 text-white">Destination</span>
                <span
                  class="d-block mb-1 font-family-secondary fs-3 text-white">
                  India
                </span>
                <span class="d-block font-family-secondary fs-5 text-white">
                  Taj Mahal
                </span>
              </div>
            </div>
          </div>
          <div class="imageGrid-item">
            <img src="./images/Indonesia.webp" alt="Image 3" />
            <div class="imageGrid-item-info">
              <div class="text-center">
                <span
                  class="d-block mb-3 font-family-secondary-bold fs-5 text-white">Destination</span>
                <span
                  class="d-block mb-1 font-family-secondary fs-3 text-white">
                  Indonesia
                </span>
                <span class="d-block font-family-secondary fs-5 text-white">
                  Bali pagoda
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- ***** Gallery End ***** -->

<!-- ***** Our Articles Slider Section ***** -->
<?php
$blogs = $themeData->getBlogsData(3);
?>
<section class="c-section type-2 blogArticle">
  <div class="container-lg">
    <div class="row align-items-center">
      <div class="col-12">
        <div class="heading">
          <h2 class="span">
            Travel
            <span class="fs-4 font-family-secondary-bold"> Blog </span>
          </h2>
          <div class="image"></div>
        </div>
        <!-- *** Blog Slider  *** -->
        <div class="justify-content-center row">
          <div class="col-md-8 col-sm-12">
            <div class="owl-carousel c-slider js-blogSlider">
              <!-- Card -->
              <?php foreach ($blogs as $blog) { ?>
                <div class="card c-card">
                  <div class="card-image md">
                    <img src="<?php echo BASE_URL . $blog['image_path']; ?>" alt="..." />
                  </div>
                  <div class="card-body p-4">
                    <h5
                      class="card-title mb-2 fs-6 font-family-secondary-bold color-primary" title="<?= $blog['title'] ?>">
                      <?php
                      echo (strlen($blog['title']) > 45)
                        ? substr($blog['title'], 0, 45) . "..."
                        : $blog['title'];
                      ?>
                    </h5>
                    <div class="with-divider d-flex flex-row mb-3">

                    </div>
                    <span
                      class="mb-1 fs-7 text-secondary text-ellipsis-3 lh-md">
                      <?php
                      $desc = strip_tags($blog['description']);
                      echo (strlen($desc) > 200)
                        ? substr($desc, 0, 200) . "..."
                        : $desc;
                      ?>
                    </span>

                    <a href="<?= BASE_URL_B2C ?>single-blog.php?blog_id=<?= $blog['id'] ?>" class="c-button btn is-link text-uppercase fw-bolder">
                      Read More
                    </a>
                  </div>
                </div>
                <!-- Card End -->
              <?php } ?>
              <!-- Card End -->
            </div>
          </div>
        </div>
        <!-- *** Blog Slider End  *** -->
      </div>
    </div>
  </div>
</section>
<!-- ***** Our Articles Slider Section End ***** -->

<!-- ***** Our Partners Section ***** -->
<?php
$partners = $themeData->getPartners();
if (count($partners) > 0) {
?>
  <section class="c-section">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-12">
          <div class="heading">
            <h2 class="span">Our Partners</h2>
            <div class="image"></div>
          </div>
        </div>
      </div>
      <div class="row align-items-center">
        <div class="col-12">
          <div
            class="owl-carousel c-slider js-gallerySlider partnerCardSlider">
            <!-- Card -->
            <?php
            foreach ($partners as $partner) {
            ?>
              <div class="c-partnerCard">
                <div class="card-image">
                  <img
                    src="<?php echo $partner; ?>"
                    alt="photo"
                    width="60" />
                </div>
              </div>
            <?php }
            ?>
            <!-- Card End -->
          </div>
        </div>
      </div>
    </div>
  </section>
<?php
}
?>
<!-- ***** Our Partners Section End ***** -->

<!-- ***** Our Team Section ***** -->
<?php $team_array = $themeData->getTeams(5);
if (count($team_array) > 0) {
?>
  <section class="c-section type-2 ourTeam">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-12">
          <div class="heading">
            <h2 class="span">Our Team</h2>
            <div class="image"></div>
          </div>
        </div>
      </div>

      <div class="owl-carousel c-slider js-trendingTours">
        <!-- Card -->
        <?php
        foreach ($team_array as $team) {
          if ($team['image']) {
            $cleanPath = str_replace('../../../', '/', $team['image']);
            $cleanPath = "crm/" . $cleanPath;
          } else {
            $cleanPath = './images/profile.png';
          } ?>
          <div class="c-customerCard type-1">
            <div class="card-image">
              <img
                src="<?php echo $cleanPath; ?>"
                alt="photo"
                height="130"
                width="130" class="profile-pic" />
            </div>
            <div class="card-body">
              <h2 class="fs-5 font-family-secondary-bold d-block mb-2">
                <?php echo $team['tname']; ?>
              </h2>
              <span
                class="fs-7 d-block color-secondary font-family-secondary-bold">
                <?= $team['designation']; ?>
              </span>
            </div>
          </div>
          <!-- Card End -->
        <?php } ?>
      </div>
    </div>
  </section>
  <!-- ***** Our Services Section End ***** -->
<?php } ?>
<!-- ***** Our Gallery Slider Section ***** -->
<?php $gallary_array = $moduleData->getGalleryImages(); ?>
<section class="c-section">
  <div class="container-lg">
    <div class="row align-items-center">
      <div class="col-12">
        <div class="heading">
          <h2 class="span">Our Gallery</h2>
          <div class="image"></div>
        </div>
      </div>
    </div>
    <div class="owl-carousel c-slider js-gallerySlider">
      <?php
      foreach ($gallary_array as $item) {
      ?>
        <div class="card-image">
          <a class="light-gallery-item">
            <img src="<?= $item['image_url'] ?>" alt="photo" class="img-fluid" style="height:242px;border-radius: 30px;max-width:264px;" title="<?= $item['dest_name']; ?>" />
          </a>
        </div>
      <?php } ?>
    </div>
  </div>
</section>
<!-- ***** Our Gallery Slider Section End ***** -->

<!-- ***** Our Services Section ***** -->
<?php
$servicesData = mysqli_fetch_all(mysqlQuery("SELECT services FROM `b2c_settings` "), MYSQLI_ASSOC);
$services = isset($servicesData[0]['services']) ? json_decode($servicesData[0]['services'], true) : [];
?>
<section class="c-section">
  <div class="container-lg">
    <div class="row">
      <div class="col-12">
        <div class="c-ourServices">
          <div class="ourServices">
            <h2
              class="fs-2 font-family-secondary-bold d-block text-center text-white mb-4">
              Our Services
            </h2>
            <div class="row">

              <!-- Service card -->
              <?php
              if (!empty($services)) :
                $limitedServices = array_slice($services, 0, 4);
                foreach ($limitedServices as $service) :
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
                  <div class="col-md-3 col-sm-6 col-xs-12 text-center">
                    <div class="serviceCircle mb-3">
                      <?php echo $icon; ?>
                    </div>
                    <span
                      class="fs-6 font-family-secondary-bold d-block text-white mb-3">
                      <?= htmlspecialchars($service['service_name']) ?>
                    </span>
                    <span class="fs-7 d-block text-white mb-3">
                      <?= htmlspecialchars($service['description']) ?>
                    </span>
                  </div>
                <?php
                endforeach;
              else :
                ?>
                <p class="text-center font-weight-bold text-danger">No services available</p>
              <?php
              endif;
              ?>
              <!-- Service card End -->


            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- ***** Our Services Section End ***** -->

<!-- ***** Our Expertise Section ***** -->
<section class="c-section">
  <div class="container-lg">
    <div class="row align-items-center">
      <div class="col-12">
        <div class="heading">
          <h2 class="span">Our Expertise</h2>
          <div class="image"></div>
        </div>
      </div>
      <!-- Card -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="counter text-center mb-3 mb-md-0">
          <div>
            <h5 class="d-block fs-3 font-family-secondary-bold mb-3">
              2000+
            </h5>
            <span class="d-block fs-5 font-family-secondary-bold">Awesome hikers</span>
          </div>
        </div>
      </div>
      <!-- Card End -->

      <!-- Card -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="counter text-center mb-3 mb-md-0">
          <div>
            <h5 class="d-block fs-3 font-family-secondary-bold mb-3">
              80+
            </h5>
            <span class="d-block fs-5 font-family-secondary-bold">Stunning destinations</span>
          </div>
        </div>
      </div>
      <!-- Card End -->

      <!-- Card -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="counter text-center mb-3 mb-md-0">
          <div>
            <h5 class="d-block fs-3 font-family-secondary-bold mb-3">
              1200+
            </h5>
            <span class="d-block fs-5 font-family-secondary-bold">Miles to hike</span>
          </div>
        </div>
      </div>
      <!-- Card End -->

      <!-- Card -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="counter text-center mb-3 mb-md-0">
          <div>
            <h5 class="d-block fs-3 font-family-secondary-bold mb-3">
              15+
            </h5>
            <span class="d-block fs-5 font-family-secondary-bold">Years in service</span>
          </div>
        </div>
      </div>
      <!-- Card End -->
    </div>
  </div>
</section>
<!-- ***** Our Expertise Section End ***** -->

<!-- ***** Write to us Section ***** -->
<section class="c-section type-1 overlayRight sm">
  <div class="container-lg">
    <div class="row align-items-center">
      <div class="col-12">
        <div class="heading">
          <h2 class="span">Write to us</h2>
          <div class="image"></div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-12 order-md-1 order-2">
        <form id="contactForm">
          <span class="fw-6 d-block mb-3">Feel free to reach out to us and if you have a query drop us a
            message. Our team will get back to you at the earliest.</span>
          <div class="mb-3">
            <input type="text" class="form-control" placeholder="Name *" name="name" required />
          </div>
          <div class="mb-3">
            <input type="email" class="form-control" placeholder="Email *" name="email" required />
          </div>
          <div class="mb-3">
            <input
              type="number"
              class="form-control"
              name="phone"
              placeholder="Phone Number *" required />
          </div>
          <div class="mb-3">
            <textarea
              class="form-control"
              placeholder="Message *"
              name="message"
              rows="3" required></textarea>
          </div>
          <button
            class="c-button btn rounded primary align-items-center d-flex">
            Submit
            <i class="fa-solid fa-circle-arrow-right ms-2 fs-5"></i>
          </button>
          <div id="response" class="mt-3"></div>
        </form>
      </div>
      <div class="col-md-7 col-sm-12 order-md-2 order-1 mb-4 mb-md-0">
        <?php $googleMapScript = $moduleData->getB2cSettings('google_map_script'); ?>
        <div class="gMap">
          <?php if ($googleMapScript != '') { ?>
            <iframe
              src="<?= $googleMapScript ?>"
              style="border: 0"
              class="map"
              allowfullscreen=""
              referrerpolicy="no-referrer-when-downgrade"
              loading="lazy">
            </iframe>
          <?php } ?>
        </div>
      </div>
      </form>
    </div>
  </div>
</section>
<!-- ***** Write to us Section End ***** -->

<!-- ***** Flight :: Traveller information Modal ***** -->
<div
  class="modal fade"
  id="attendantModal"
  tabindex="-1"
  aria-labelledby="attendantModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Travellers Information</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <span class="fs-7 fw-medium d-block text-uppercase">Adults (12y +)</span>
          <span class="fs-7 fw-medium text-secondary d-block mb-2">On the day of travel</span>
          <input
            class="form-control c-input transparent"
            type="number"
            placeholder="1"
            min="1"
            max="10"
            data-x-input="adult" />
        </div>
        <div class="mb-3">
          <span class="fs-7 fw-medium d-block text-uppercase">CHILDREN (2y - 12y)</span>
          <span class="fs-7 fw-medium text-secondary d-block mb-2">On the day of travel</span>
          <input
            class="form-control c-input transparent"
            type="number"
            placeholder="1"
            min="1"
            max="10"
            data-x-input="child" />
        </div>
        <div class="mb-3">
          <span class="fs-7 fw-medium d-block text-uppercase">INFANTS (below 2y)</span>
          <span class="fs-7 fw-medium text-secondary d-block mb-2">On the day of travel</span>
          <input
            class="form-control c-input transparent"
            type="number"
            placeholder="1"
            min="1"
            max="10"
            data-x-input="infant" />
        </div>

        <span class="fs-7 fw-medium d-block text-uppercase">CHOOSE TRAVEL CLASS
        </span>
        <div class="d-flex flex-row mb-3">
          <div class="form-check flex-fill">
            <input
              class="form-check-input"
              type="radio"
              name="travelClass"
              id="economyClass"
              value="Economy"
              checked
              data-x-input="travelClass" />
            <label class="form-check-label fs-7" for="economyClass">
              Economy
            </label>
          </div>
          <div class="form-check flex-fill">
            <input
              class="form-check-input"
              type="radio"
              name="travelClass"
              id="premiumClass"
              value="Premium Economy"
              data-x-input="travelClass" />
            <label class="form-check-label fs-7" for="premiumClass">
              Premium Economy
            </label>
          </div>
        </div>
        <div class="d-flex flex-row mb-3">

          <div class="form-check flex-fill">
            <input
              class="form-check-input"
              type="radio"
              name="travelClass"
              id="businessClass"
              value="Business"
              data-x-input="travelClass" />
            <label class="form-check-label fs-7" for="businessClass">
              Business
            </label>
          </div>

          <div class="form-check flex-fill">
            <input
              class="form-check-input"
              type="radio"
              name="travelClass"
              id="firstClass"
              value="First"
              data-x-input="travelClass" />
            <label class="form-check-label fs-7" for="firstClass">
              First Class
            </label>
          </div>
        </div>
        <div class="text-center">
          <button class="btn c-button btn-lg" onclick="attendantModalUpdater()">
            Add
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- ***** Flight :: Traveller information Modal End ***** -->
</div>


<?php
include 'layouts/footer.php'; // Include footer
?>
<?php
include 'buy_now.php';
?>
<script type="text/javascript" src="js/scripts.js"></script>
<script type="text/javascript" src="view/transfer/js/index.js"></script>
<script type="text/javascript" src="view/activities/js/index.js"></script>
<script type="text/javascript" src="view/tours/js/index.js"></script>
<script type="text/javascript" src="view/group_tours/js/index.js"></script>
<script type="text/javascript" src="view/hotel/js/index.js"></script>
<script type="text/javascript" src="view/flight/js/index.js"></script>
<script type="text/javascript" src="view/hotel/js/amenities.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/lightgallery.min.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/lg-thumbnail.min.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/lg-zoom.min.js"></script>

<script>
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

  function filterSearch() {
    var input, filter, found, table, tr, td, i, j;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td");
      for (j = 0; j < td.length; j++) {
        if (td[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
          found = true;
        }
      }
      if (found) {
        tr[i].style.display = "";
        found = false;
      } else {
        tr[i].style.display = "none";
      }
    }
  }
  $(function() {
    $('#enq_form').validate({
      rules: {},
      submitHandler: function(form) {

        $('#enq_submit').prop('disabled', 'true');
        var base_url = $('#base_url').val();
        var crm_base_url = $('#crm_base_url').val();
        var name = $('#name').val();
        var phone_no = $('#phone_no').val();
        var email = $('#email').val();
        var city = $('#city').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var service_name = $('#service_name').val();
        document.getElementById('enq_submit').textContent = 'Loading';

        $.ajax({
          type: 'post',
          url: crm_base_url + "controller/b2c_settings/b2c/homepage_enq.php",
          data: {
            name: name,
            phone_no: phone_no,
            email: email,
            city: city,
            from_date: from_date,
            to_date: to_date,
            service_name: service_name
          },
          success: function(result) {
            var msg = 'Thank you for enquiry with us. Our experts will contact you shortly.';
            $.alert({
              title: 'Notification!',
              content: msg,
            });

            document.getElementById('enq_submit').textContent = 'Enquire Now';
            setTimeout(() => {
              window.location.href = base_url;
            }, 2000);
          }
        });
      }
    });
  });

  window.addEventListener('scroll', function() {
    const header = document.getElementById('top-header');

    if (window.scrollY > 50) { // Adjust the scroll position where you want it to stick
      header.classList.add('sticky');
    } else {
      header.classList.remove('sticky');
    }
  });

  $(document).ready(function() {
    lightGallery(document.getElementById('lightGalleryImage'), {
      plugins: [lgZoom, lgThumbnail],
      speed: 500,
      download: true,
    });

    setTimeout(function() {
      var width = $(".light-gallery-item img").width();
      console.log(width);
      $(".light-gallery-item img").height(width);
    }, 1000);
  });

  jQuery.validator.addMethod("lettersOnly", function(value, element) {
    return this.optional(element) || /^[a-zA-Z\s]+$/.test(value); // only letters and space
  }, "Please enter letters only.");

  jQuery.validator.addMethod("validMobile", function(value, element) {
    return this.optional(element) || /^[6-9]\d{9}$/.test(value);
  }, "Please enter a valid 10-digit mobile number.");

  $("#contactForm").validate({
    rules: {
      name: {
        required: true,
        lettersOnly: true
      },
      email: {
        required: true,
        email: true
      },
      phone: {
        required: true,
        validMobile: true
      }
    },
    submitHandler: function(form) {
      // This will only run if the form is valid
      $.ajax({
        url: 'layouts/send_mail.php',
        type: 'POST',
        data: $(form).serialize(), // ← this must be used, not JSON
        success: function(response) {
          $('#response').html('<b>Response:</b><br>' + response);
        },
        error: function() {
          $('#response').html('<b style="color:red;">AJAX request failed</b>');
        }
      });
    }
  });

  $('#contactForm').on('submit', function(e) {
    if (!$(this).valid()) {
      e.preventDefault(); // Stop form submission if invalid
    }
  });

  //Get Amenities by mathcing name
  function getObjectsData(obj, key, val) {
    var objects = [];
    for (var i in obj) {
      if (!obj.hasOwnProperty(i)) continue;
      if (typeof obj[i] == 'object') {
        objects = objects.concat(getObjectsData(obj[i], key, val));
      } else if ((i == key && obj[i] == val) || (i == key && val == '')) {
        //if key matches and value matches or if key matches and value is not passed (eliminating the case where key matches but passed value does not)
        objects.push(obj);
      } else if (obj[i] == val && key == '') {
        //only add if the object is not already in the array
        if (objects.lastIndexOf(obj) == -1) {
          objects.push(obj);
        }
      }
    }
    return objects;
  }
</script>