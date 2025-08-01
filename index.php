<?php
include "config.php";
global $app_contact_no;
$b = 'base6' . '4_decode';
$service = $_GET['service'];
include 'layouts/header.php'; //Include header
?>

<!-- *** Banner slider *** -->
<section class="c-bannerAndFilter with-slider">
  <div class="c-banner type-01">
    <?php $banners = $themeData->getBanners(); ?>
    <!-- *** Slider *** -->
    <div class="owl-carousel pageSlider">
      <?php foreach ($banners as $banner): ?>
        <div class="item sliderItem">
          <img src="<?php echo $banner; ?>" alt="travel" />
          <!-- *** Info Section *** -->
          <div class="info-section banner_one_text text-center">
            <h1>Explore the world together</h1>
          </div>
          <!-- *** Info Section End *** -->
        </div>
      <?php endforeach; ?>
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
            <ul class="nav nav-tabs" id="myTab" role="tablist">

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
                  <i class="fa-solid fa-umbrella-beach me-2"></i>
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
                  <i class="fa-solid fa-users me-2"></i>
                  <span class="fw-medium">Group Tour</span>
                </button>
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
                  <i class="fa-solid fa-hotel me-2"></i>
                  <span class="fw-medium">Hotel</span>
                </button>
              </li>
              </li>
              <li class="nav-item" role="presentation">
                <button
                  class="nav-link filterButton fs-7"
                  id="flight-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#flight-tab-pane"
                  type="button"
                  role="tab"
                  aria-controls="flight-tab-pane"
                  aria-selected="false">
                  <i class="fa-sharp fa-solid fa-plane-departure me-2"></i>
                  <span class="fw-medium">Flight</span>
                </button>
              </li>
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
                  <i class="fa-solid fa-sailboat me-2"></i>
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
                  <i class="fa-solid fa-car me-2"></i>
                  <span class="fw-medium">Transfer</span>
                </button>
              </li>
            </ul>
            <div class="tab-content" id="myTabContent">

              <!-- ***** Holiday Tour ***** -->
              <div
                class="tab-pane fade show active"
                id="holiday-tab-pane"
                role="tabpanel"
                aria-labelledby="holiday-tab"
                tabindex="0">
                <?php include 'view/tours/tours-search.php'; ?>

              </div>
              <!-- ***** Holiday Tour End ***** -->
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

              <!-- ***** Hotel ***** -->
              <div
                class="tab-pane fade"
                id="hotel-tab-pane"
                role="tabpanel"
                tabindex="0">
                <?php include 'view/hotel/hotel-search.php'; ?>
              </div>
              <!-- ***** Hotel End ***** -->

              <!-- ***** Flight ***** -->
              <div
                class="tab-pane fade"
                id="flight-tab-pane"
                role="tabpanel"
                aria-labelledby="flight-tab"
                tabindex="0">
                <?php include 'view/flight/flight-search.php'; ?>
              </div>
              <!-- ***** Flight End ***** -->
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
              <!-- ***** Transfer Tour End ***** -->
            </div>
          </div>
          <!-- ***** Filter Tabs End ***** -->
        </div>
      </div>
    </div>
  </div>
</section>
<!-- ***** Filter Section End ***** -->

<!-- ***** Popular Packages Section ***** -->
<?php
$popularPackages = $themeData->getPopularPackages();
if ($popularPackages && count($popularPackages) > 0) {
?>
  <section class="c-section type-2">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-12">
          <h3 class="heading">Explore Popular Packages</h3>
          <!-- Set up your HTML -->
          <div class="owl-carousel cardSlider c-slider">
            <?php foreach ($popularPackages as $package) :
              $pricing = ($package['tariff']['cadult'])
                ?  $themeData->convertCurrency($package['tariff']['cadult'], $currency)  : '0.00';
            ?>
              <!-- Card -->
              <div class="card c-card" title="<?php echo $package['package_name'];  ?>">
                <div class="card-image">
                  <img
                    src="<?php echo $package['main_img_url']; ?>"
                    class="card-img-top"
                    alt="<?php echo $package['package_name']; ?>" />
                  <span class="title fw-medium"> <?php echo $package['tour_type']; ?> </span>
                  <div class="discount">
                    <i class="fa-solid fa-tags fs-5 text-success"></i>
                  </div>
                </div>
                <div class="card-body">
                  <h6 class="fw-medium mb-2 color-primary fs-6 height-38">
                    <?php
                    if ((strlen($package['package_name']) > 30))
                      echo substr($package['package_name'], 0, length: 30) . "...";
                    else
                      echo $package['package_name'];
                    // echo (strlen($package['package_name']) > 30) ? substr($package['package_name'], 0, length: 30) . "..." : $package['package_name'];
                    ?>
                  </h6>
                  <div class="d-flex flex-row mb-3 gap-2">
                    <div class="flex-grow-1">
                      <span class="fs-6 fw-bold text-secondary">
                        <i class="fa-solid fa-location-dot me-1"></i> <?php echo $package['destination']['dest_name']; ?> </span>
                    </div>
                    <div class="flex-grow-1 text-end">
                      <span class="fs-6 fw-medium text-secondary">
                        <i class="fa-regular fa-clock me-1"></i> <?php echo $package['total_nights']; ?> N / <?php echo $package['total_days']; ?> D
                      </span>
                    </div>
                  </div>

                  <div class="d-block mb-2">
                    <span class="fs-6 text-secondary d-block">
                      Price Per Person
                    </span>
                    <span class="card-title d-inline fs-4 fw-bold">
                      <?php echo $pricing; ?>
                      <sup class="fs-6 text-secondary">*</sup>
                    </span>
                  </div>
                  <a class="c-button btn small sm fw-medium fs-8" href="<?php echo BASE_URL_B2C; ?><?php echo $package['seo_slug']; ?>">
                    View More
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
            <!-- Card End -->
          </div>
        </div>
      </div>
    </div>
  </section>
<?php } ?>
<!-- ***** Popular Packages Section End ***** -->

<!-- ***** Recommended HotelsSection ***** -->
<?php
$recommendedHotels = $themeData->getPopularHotels();
if ($recommendedHotels && count($recommendedHotels) > 0) {
?>
  <section class="c-section type-1">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-12">
          <h3 class="heading">Recommended Hotels</h3>
          <!-- Set up your HTML -->
          <div class="owl-carousel cardSlider c-slider">
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

              // $pricing = $hotel['double_bed'] ? $hotel['double_bed'] : '0.00';
              $pricing =  $hotel['double_bed'] ? $themeData->convertCurrency($hotel['double_bed'], $currency) : '0.00';
            ?>
              <div class="card c-card">
                <div class="card-image">
                  <img
                    src="<?= $hotel['main_img']; ?>"
                    class="card-img-top"
                    alt="<?php echo $hotel['hotel_type']; ?>" />
                  <div class="overlayRating">
                    <div class="flex-grow-1 text-end"><?php echo $ratingStars; ?></div>
                  </div>
                  <span class="title fs-8 fw-medium"> <?php echo $hotel['hotel_type']; ?> </span>
                  <div class="discount">
                    <i class="fa-solid fa-tags fs-5 text-success"></i>
                  </div>
                </div>
                <div class="card-body">
                  <h6 class="fw-medium mb-2 color-primary fs-6 height-38"><?php echo $hotel['hotel_name']; ?>
                  </h6>
                  <span class="fs-8 fw-medium text-secondary mb-3 d-block height-38">
                    <i class="fa-solid fa-location-dot me-1"></i>
                    <?php echo $hotel['hotel_address']; ?>
                  </span>

                  <div class="d-block mb-2">
                    <span class="fs-8 text-secondary d-block">
                      Room Cost
                    </span>
                    <span class="card-title d-inline fs-4 fw-bold hotel-mrp-homepage">
                      <?php echo $pricing; ?>
                      <sup class="fs-6 text-secondary">*</sup>
                    </span>
                  </div>
                  <a class="c-button btn small sm fw-medium fs-8" target="_blank" onclick="get_hotel_listing_page('<?= $hotel['hotel_id']; ?>')">
                    View More
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
            <!-- Card End -->
          </div>
        </div>
      </div>
    </div>
  </section>
<?php } ?>
<!-- ***** Recommended Hotels Section End ***** -->

<!-- ***** CTA Section ***** -->
<?php
$testimonials = $themeData->getCustomerTestimonials(5);
?>
<section class="c-section">
  <div class="container-lg">
    <div class="row align-items-center">
      <div class="col-12">
        <div class="c-cta">
          <img src="./images/CTA.png" alt="cta" />
          <div class="infoSection">
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <div class="info p-5 align-middle">
                  <span
                    class="fs-4 fw-medium d-block text-white font-family-secondary">
                    At <?php echo $app_name; ?>, we take pride in crafting unforgettable travel experiences. Our customer's testimonials reflect the seamless journeys, personalized service, and incredible destinations we offer.
                  </span>
                  <span class="fs-7 d-block text-white mb-1">

                  </span>
                  <span class="fs-3 d-block text-white">
                    <i class="fa-solid fa-phone me-2 fs-5"></i><?= $app_contact_no; ?></span>
                </div>
              </div>
              <div class="col-md-6 col-sm-12 text-center">
                <div class="ctaSlider">
                  <span
                    class="fs-6 d-block text-secondary text-center mb-1 font-family-secondary fw-bolder">
                    Our Testimonials
                  </span>
                  <span
                    class="fs-4 d-block fw-medium text-center mb-1 font-family-secondary">
                    What our clients say about us
                  </span>

                  <div class="owl-carousel cta-slider c-slider mt-4">
                    <?php if ($testimonials && count($testimonials) > 0):
                      foreach ($testimonials as $testimonial) { ?>
                        <div class="item">
                          <span
                            class="fs-5 fw-medium d-block color-primary text-center mb-3">
                            Awesome Service
                          </span>
                          <span
                            class="fs-6 d-block text-center mb-3 text-secondary mh-100">
                            <?= $testimonial['testm']; ?>
                          </span>
                          <span class="fs-6 fw-medium d-block text-center">
                            <?= $testimonial['name']; ?>
                          </span>
                          <span
                            class="fs-7 fw-medium d-block color-primary text-center mb-1">
                            <?= $testimonial['designation']; ?>
                          </span>
                        </div>
                    <?php }
                    endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- ***** CTA Section End ***** -->

<?php
$excitingGroupTours = $themeData->getPopularGroupTours();
if ($excitingGroupTours && count($excitingGroupTours) > 0) {
?>
  <!-- ***** Exciting Group Tours Section ***** -->
  <section class="c-section type-2">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-12">
          <h3 class="heading">Exciting Group Tours</h3>
          <!-- Set up your HTML -->
          <div class="owl-carousel cardSlider c-slider">
            <?php foreach ($excitingGroupTours as $tour) {
              $pricing =  $tour['adult_cost'] ? $themeData->convertCurrency($tour['adult_cost'], $currency) : '0.00';
            ?>
              <!-- Card -->
              <div class="card c-card" title="<?php echo $tour['tour_name']; ?>">
                <div class="card-image">
                  <img
                    src="<?= $tour['image_url']; ?>"
                    class="card-img-top"
                    alt="<?= $tour['tour_name']; ?>" />
                  <span class="title fs-8 fw-bold"> <?= $tour['tour_type']; ?> </span>
                  <div class="discount">
                    <i class="fa-solid fa-tags fs-5 text-success"></i>
                  </div>
                </div>
                <div class="card-body">
                  <h6
                    class="card-title fw-5 fw-medium mb-2 color-primary height-38">
                    <?php
                    echo (strlen($tour['tour_name']) > 30)
                      ? substr($tour['tour_name'], 0, 30) . "..."
                      : $tour['tour_name'];
                    ?>
                  </h6>

                  <div class="d-flex flex-row mb-3 gap-2">
                    <div class="flex-grow-1">
                      <span class="fs-8 fw-medium text-secondary d-block group-tour-date-homepage">
                        <?= $tour['tour_dates']; ?>
                      </span>
                    </div>
                  </div>
                  <div class="d-block mb-2">
                    <span class="fs-8 fw-bold text-secondary d-block">
                      Price Per Person
                    </span>
                    <span class="card-title d-inline fs-5 fw-bold">
                      <?= $pricing; ?>
                      <sup class="fs-6 text-secondary">*</sup>
                    </span>
                  </div>
                  <a class="c-button btn small sm fw-medium fs-8" onclick='<?php echo BASE_URL_B2C; ?><?php echo $tour['seo_slug']; ?>'>
                    View More
                  </a>
                </div>
              </div>
              <!-- Card End -->
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php } ?>
<!-- ***** Exciting Group Tours Section End ***** -->

<!-- ***** Popular Destinations Section ***** -->
<section class="c-section type-1">
  <div class="container-lg">
    <div class="row align-items-center">
      <div class="col-12">
        <h3 class="heading">Popular Destinations</h3>
      </div>
    </div>
    <div class="row alterNateCards">
      <!-- Alternate image card -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="imageCard mb-3 mb-md-0">
          <img src="./images/destination-card-2.png" alt="'offer" />
          <div class="rating">
            <span class="fs-7 text-secondary fw-semibold">Rating</span>
            <i
              class="fa-sharp fa-solid fa-star color-primary fs-7 me-1"></i>
            <span class="fs-7 text-secondary fw-semibold">4.8</span>
          </div>
          <div class="info">
            <h3
              class="fs-4 fs-md-3 mb-3 text-white fw-semibold font-family-secondary">
              Dubai
            </h3>
            <span class="fs-7 fs-md-6 text-white">
              Experience luxury, adventure, and culture in vibrant, iconic
              Dubai
            </span>
          </div>
        </div>
      </div>
      <!-- Alternate image card End -->

      <!-- Alternate image card -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="imageCard mt-0 mt-md-5 mb-3 mb-md-0">
          <img src="./images/destination-card-1.png" alt="'offer" />
          <div class="rating">
            <span class="fs-7 text-secondary fw-semibold">Rating</span>
            <i
              class="fa-sharp fa-solid fa-star color-primary fs-7 me-1"></i>
            <span class="fs-7 text-secondary fw-semibold">4.8</span>
          </div>
          <div class="info">
            <h3
              class="fs-4 fs-md-3 mb-3 text-white fw-semibold font-family-secondary">
              Singapore
            </h3>
            <span class="fs-7 fs-md-6 text-white">
              Explore Singapore's vibrant culture, stunning landmarks, and
              world-class attractions.
            </span>
          </div>
        </div>
      </div>
      <!-- Alternate image card End -->

      <!-- Alternate image card -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="imageCard mb-3 mb-md-0">
          <img src="./images/destination-card-2.png" alt="'offer" />
          <div class="rating">
            <span class="fs-7 text-secondary fw-semibold">Rating</span>
            <i
              class="fa-sharp fa-solid fa-star color-primary fs-7 me-1"></i>
            <span class="fs-7 text-secondary fw-semibold">4.8</span>
          </div>
          <div class="info">
            <h3
              class="fs-4 fs-md-3 mb-3 text-white fw-semibold font-family-secondary">
              Europe
            </h3>
            <span class="fs-7 fs-md-6 text-white">
              Experience Europe's rich history, diverse cultures, stunning
              landmarks, and landscapes.
            </span>
          </div>
        </div>
      </div>
      <!-- Alternate image card End -->

      <!-- Alternate image card -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="imageCard mt-0 mt-md-5 mb-3 mb-md-0">
          <img src="./images/destination-card-1.png" alt="'offer" />
          <div class="rating">
            <span class="fs-7 text-secondary fw-semibold">Rating</span>
            <i
              class="fa-sharp fa-solid fa-star color-primary fs-7 me-1"></i>
            <span class="fs-7 text-secondary fw-semibold">4.8</span>
          </div>
          <div class="info">
            <h3
              class="fs-4 fs-md-3 mb-3 text-white fw-semibold font-family-secondary">
              Thailand
            </h3>
            <span class="fs-7 fs-md-6 text-white">
              Discover Thailand's rich culture, beautiful beaches, and
              vibrant city life.
            </span>
          </div>
        </div>
      </div>
      <!-- Alternate image card End -->
    </div>
  </div>
</section>
<!-- ***** Popular Destinations Section End ***** -->

<!-- ***** Memorable  Activities  Section ***** -->
<?php
$activities = $themeData->getPopularActivities();
if ($activities && count($activities) > 0) {
?>
  <section class="c-section type-2">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-12">
          <h3 class="heading">Memorable Activities</h3>
          <!-- Set up your HTML -->
          <div class="owl-carousel cardSlider c-slider">
            <!-- Card -->
            <?php foreach ($activities as $activity) {
            ?>
              <div class="card c-card" title="<?php echo $activity['excursion_name'];  ?>">
                <div class="card-image">
                  <img
                    src="<?= $activity['main_img_url']; ?>"
                    class="card-img-top"
                    alt="<?= $activity['excursion_name']; ?>" />
                  <span class="title fs-8 fw-medium"> <?php echo $activity['city_details']['city_name'] ?? "Unknown"; ?> </span>
                </div>
                <div class="card-body">
                  <h6
                    class="card-title fw-5 fw-medium mb-1 color-primary height-38">
                    <?= $activity['excursion_name']; ?>
                  </h6>
                  <span class="fs-8 fw-medium text-secondary d-block mb-3 height-38">
                    <!-- <i class="icon itours-align-left" aria-hidden="true"></i> -->
                    <i class="fa-solid fa-circle-info"></i>
                    <?php echo substr($activity['description'], 0, 100) . '...'; ?>
                  </span>

                  <div class="d-block mb-2">
                    <span class="fs-8 text-secondary d-block">
                      Price Per Person
                    </span>
                    <span class="card-title d-inline fs-4 fw-bold">
                      <?php
                      echo $activity['basics']->adult_cost ? $themeData->convertCurrency($activity['basics']->adult_cost, $currency) : "";
                      ?>
                      <sup class="fs-6 text-secondary">*</sup>
                    </span>
                  </div>
                  <a class="c-button btn small sm fw-medium fs-8" target="!#" onclick="get_act_listing_page('<?= $activity['entry_id'] ?>')">
                    View More
                  </a>
                </div>
              </div>
            <?php } ?>
            <!-- Card End -->

          </div>
        </div>
      </div>
    </div>
  </section>
<?php } ?>
<!-- ***** Memorable  Activities  Section End ***** -->

<!-- ***** Latest News and Blogs Section ***** -->
<?php
$blogs = $themeData->getBlogsData(3);
?>
<section class="c-section">
  <div class="container-lg">
    <div class="row align-items-center">
      <div class="col-12">
        <h3 class="heading">Latest News and Blogs</h3>
      </div>
    </div>
    <div class="row align-items-center">
      <!-- Card -->
      <?php foreach ($blogs as $blog) { ?>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="card c-card mb-3 mb-md-0 blog-home-page-list">
            <div class="card-image">
              <img src="<?php echo BASE_URL . $blog['image_path']; ?>" class="card-img-top" alt="..." />
            </div>
            <div class="card-body">
              <div class="mb-3 d-flex">
                <h6
                  class="card-title fw-5 fw-medium mb-1 color-primary height-38">
                  <?php
                  echo substr($blog['title'], 0, 20) . "....";
                  ?>
                  </span>
                </h6>
                <span
                  class="d-inline fs-8 text-body-tertiary fw-medium text-end flex-grow-1">
                  <!-- 26 October, 2024 -->
                </span>
              </div>
              <span class="fs-8 fw-medium text-secondary d-block mb-3 height-38">
                <i class="icon itours-align-left" aria-hidden="true"></i>
                <?php echo substr($blog['description'], 0, 100) . "..."; ?>
              </span>
              <!-- <h5 class="card-title mb-3">
                <?php echo substr($blog['description'], 0, 100) . "..."; ?>
              </h5> -->

              <a href="<?= BASE_URL_B2C ?>single-blog.php?blog_id=<?= $blog['id'] ?>" class="c-button btn small sm fw-medium fs-8">
                Read More
              </a>
            </div>
          </div>
        </div>
      <?php } ?>
      <!-- Card End -->
    </div>
  </div>
</section>
<!-- ***** Latest News and Blogs Section End ***** -->

<!-- ***** Partner Slider Section ***** -->
<?php
$partners = $themeData->getPartners();
if (count($partners) > 0) {
?>
  <section class="c-section type-1">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-12">
          <h3 class="heading">Our Partners</h3>
        </div>
      </div>
      <div class="row align-items-center">
        <div class="col-12">
          <div class="owl-carousel partnerSlider">
            <?php
            foreach ($partners as $partner) {
            ?>
              <div class="partner">
                <img src="<?php echo $partner; ?>" alt="'partner" />
              </div>
            <?php }
            ?>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php
}
?>
<!-- ***** Partner Slider Section ***** -->

<!-- ***** Flight :: Traveller information Modal ***** -->
<div
  class="modal fade"
  id="attendantModal"
  tabindex="-1"
  aria-labelledby="attendantModalLabel"
  aria-hidden="true">
  <div class="modal-dialog">
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
<script type="text/javascript" src="./js/header-api-async.js"></script>
<script type="text/javascript" src="js/scripts.js"></script>
<script type="text/javascript" src="view/transfer/js/index.js"></script>
<script type="text/javascript" src="view/activities/js/index.js"></script>
<script type="text/javascript" src="view/tours/js/index.js"></script>
<script type="text/javascript" src="view/group_tours/js/index.js"></script>
<script type="text/javascript" src="view/hotel/js/index.js"></script>
<script type="text/javascript" src="view/flight/js/index.js"></script>
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
</script>