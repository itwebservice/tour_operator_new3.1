<?php

/**
 * OPTION-8 (quotation_html_8) — Package Tour Quotation
 * Layout/CSS from Final-Designs/Option-8-Done/index.html
 * Data from generic quotation engine (no inline DB queries).
 */
include "../../../../model.php";
include_once "../generic_quotation_data.php";
include_once "../generic_builder_config.php";

$quotation_id = isset($_GET['quotation_id']) ? $_GET['quotation_id']
  : (isset($_REQUEST['quotation_id']) ? $_REQUEST['quotation_id'] : 0);

$q = get_generic_quotation_data($quotation_id);
if (empty($q['found'])) {
  echo "Quotation not found.";
  exit;
}

$hero         = $q['hero'];
$ov           = $q['tour_overview'];
$hotels       = $q['hotels'];
$flights      = $q['flights'];
$trains       = isset($q['trains']) ? $q['trains'] : array();
$acts         = isset($q['activities']) ? $q['activities'] : array();
$vehs         = $q['vehicles'];
$itin         = $q['itinerary'];
$incx         = $q['inclusion_exclusion'];
$cost         = $q['costing'];
$bank         = $q['bank_details'];
$terms        = $q['terms_conditions'];
$ty           = $q['thank_you'];
$present      = $q['sections_present'];
$testimonials = array();
$o8_cfg       = array();
if (function_exists('gqb_get_config')) {
  $o8_cfg = gqb_get_config();
  $testimonials = isset($o8_cfg['testimonials']) && is_array($o8_cfg['testimonials'])
    ? $o8_cfg['testimonials'] : array();
}

if (!function_exists('o8e')) {
  function o8e($v)
  {
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
  }
}
if (!function_exists('o8nv')) {
  function o8nv($v, $f = '')
  {
    return ($v !== null && $v !== '') ? $v : $f;
  }
}
if (!function_exists('o8_media_url')) {
  function o8_media_url($url)
  {
    $url = trim((string) $url);
    if ($url === '' || stripos($url, 'dummy') !== false) {
      return '';
    }
    $url = str_replace('\\', '/', $url);
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
      return $url;
    }
    $url = preg_replace('#^(\.\./)+#', '', $url);
    return BASE_URL . ltrim($url, '/');
  }
}
if (!function_exists('o8img')) {
  function o8img($url, $fallback)
  {
    $resolved = o8_media_url($url);
    return $resolved !== '' ? $resolved : $fallback;
  }
}
if (!function_exists('o8_guest_label')) {
  function o8_guest_label($ov)
  {
    $p = isset($ov['pax']) ? $ov['pax'] : array();
    $parts = array();
    $ad = (int) o8nv(isset($p['adult']) ? $p['adult'] : 0, 0);
    $ch = (int) o8nv(isset($p['children_with_bed']) ? $p['children_with_bed'] : 0, 0)
      + (int) o8nv(isset($p['children_without_bed']) ? $p['children_without_bed'] : 0, 0);
    $inf = (int) o8nv(isset($p['infant']) ? $p['infant'] : 0, 0);
    if ($ad) {
      $parts[] = $ad . ' Adult' . ($ad > 1 ? 's' : '');
    }
    if ($ch) {
      $parts[] = $ch . ' Child' . ($ch > 1 ? 'ren' : '');
    }
    if ($inf) {
      $parts[] = $inf . ' Infant' . ($inf > 1 ? 's' : '');
    }
    return $parts ? implode(', ', $parts) : o8nv($ov['guest_count'], '-');
  }
}
if (!function_exists('o8_split_lines')) {
  function o8_split_lines($html, $fallback = array())
  {
    $text = trim(strip_tags(str_replace(array('<br>', '<br/>', '<br />', '</p>', '</li>'), "\n", (string) $html)));
    $items = preg_split('/\r\n|\r|\n|•|\x{2022}/u', $text);
    $items = array_values(array_filter(array_map('trim', (array) $items)));
    return $items ? $items : $fallback;
  }
}
if (!function_exists('o8_air_code')) {
  function o8_air_code($loc)
  {
    $loc = trim((string) $loc);
    if ($loc === '') {
      return '—';
    }
    if (preg_match('/\(([A-Z]{3})\)/', strtoupper($loc), $m)) {
      return $m[1];
    }
    $words = preg_split('/[\s,]+/', $loc);
    if (count($words) >= 2) {
      return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 2));
    }
    return strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $loc), 0, 3));
  }
}
if (!function_exists('o8_first_name')) {
  function o8_first_name($name)
  {
    $name = trim((string) $name);
    if ($name === '') {
      return 'Guest';
    }
    $parts = preg_split('/\s+/', $name);
    return o8nv($parts[0], 'Guest');
  }
}
if (!function_exists('o8_vehicle_end_date')) {
  function o8_vehicle_end_date($v)
  {
    if (!empty($v['end_date_raw']) && function_exists('get_date_user')) {
      return get_date_user($v['end_date_raw']);
    }
    return o8nv(isset($v['end_date_raw']) ? $v['end_date_raw'] : '', o8nv(isset($v['end_date']) ? $v['end_date'] : '', ''));
  }
}
if (!function_exists('o8_is_recommended_pkg')) {
  function o8_is_recommended_pkg($pkg, $idx, $total)
  {
    $pkg = strtolower((string) $pkg);
    if (strpos($pkg, 'premium') !== false || strpos($pkg, 'recommended') !== false) {
      return true;
    }
    return ($idx === 1 && $total > 1);
  }
}

$o8_dest         = o8nv($ov['destination'], o8nv($hero['tour_name'], 'Tour'));
$o8_client       = o8nv($ov['client_name'], o8nv($hero['client_name'], 'Guest'));
$o8_first        = o8_first_name($o8_client);
$o8_company      = o8nv($hero['company_name'], o8nv($ty['company_name'], 'Travel Partner'));
$o8_logo         = o8nv($hero['company_logo'], o8nv($ty['company_logo'], ''));
$o8_tour_id      = o8nv($hero['package_code'], o8nv($ov['tour_id'], ''));
$o8_quot_code    = o8nv($hero['quotation_code'], '');
$o8_duration     = o8nv($ov['duration_label'], o8nv($hero['duration_label'], ''));
$o8_travel_from  = o8nv($ov['travel_from'], '');
$o8_travel_to    = o8nv($ov['travel_to'], '');
$o8_travel_range = trim($o8_travel_from . ($o8_travel_to !== '' ? ' — ' . $o8_travel_to : ''));
$o8_pkg_badge    = '';
if (!empty($cost['computed']['group'][0]['package_type'])) {
  $o8_pkg_badge = $cost['computed']['group'][0]['package_type'];
} elseif (!empty($hotels[0]['package_type'])) {
  $o8_pkg_badge = $hotels[0]['package_type'];
}
$o8_pkg_ov = o8nv($o8_pkg_badge, o8nv($ov['package_type_label'], 'Package'));
$o8_included = o8_split_lines(isset($incx['included']) ? $incx['included'] : '', array('Inclusions as per itinerary.'));
$o8_excluded = o8_split_lines(isset($incx['excluded']) ? $incx['excluded'] : '', array('Exclusions as per company policy.'));
$o8_cost_grp = isset($cost['computed']['group']) ? $cost['computed']['group'] : array();
$o8_cost_pp  = isset($cost['computed']['per_person']) ? $cost['computed']['per_person'] : array();
if (empty($o8_cost_grp) && empty($o8_cost_pp)) {
  $o8_cost_grp = array(array(
    'package_type'      => 'Package',
    'tour_cost_display' => '0',
    'tax_display'       => '0',
    'tcs_display'       => '0',
    'travel_display'    => '0',
    'total_display'     => '0',
  ));
}
$o8_pay_notes = o8_split_lines(
  o8nv(isset($incx['quot_note']) ? $incx['quot_note'] : '', ''),
  array(
    'Transfer 50% advance amount to the above bank account with reference to your quotation ID (' . $o8_quot_code . ').',
    'You can also use UPI to pay via the QR code provided.',
    'Send us the payment screenshot along with your quotation ID.',
    'We will confirm your booking within 24 hours.',
    'Balance amount is due 30 days before your travel date.',
  )
);
$o8_book_policy = o8_split_lines(
  o8nv(isset($incx['note']) ? $incx['note'] : '', ''),
  array(
    '50% advance required at the time of booking confirmation.',
    'Remaining 50% payment due 30 days before travel date.',
    'No cancellation charge if cancelled 45 days prior to travel.',
    'Booking confirmed after receipt of advance payment.',
  )
);
$o8_term_lines = o8_split_lines(isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '', array());
$o8_show_flights = !empty($present['flights']) && !empty($flights);
$o8_show_vehs    = !empty($present['vehicles']) && !empty($vehs);
$o8_is_per_person = (isset($cost['costing_type']) && (string) $cost['costing_type'] === '2')
  || (isset($cost['costing_type_label']) && strtolower(trim($cost['costing_type_label'])) === 'per person');

$o8_consult_name  = o8nv($ty['prepared_by'], o8nv($hero['login_user'], ''));
$o8_consult_email = o8nv($hero['user_email_id'], o8nv($ty['company_email'], ''));
$o8_consult_phone = o8nv($hero['user_contact'], o8nv($ty['user_mobile'], o8nv($ty['company_contact'], '')));

$o8_google_rating = o8nv(isset($o8_cfg['google_rating']) ? $o8_cfg['google_rating'] : '', '4.9/5');
$o8_review_count  = o8nv(isset($o8_cfg['review_count']) ? $o8_cfg['review_count'] : '', '2500+');
$o8_traveller_cnt = o8nv(isset($o8_cfg['traveller_count']) ? $o8_cfg['traveller_count'] : '', '2500+');

$o8_qr_url = '';
if (!empty($bank['qr_code_url'])) {
  $o8_qr_url = o8_media_url($bank['qr_code_url']);
} elseif (!empty($bank['qr_code'])) {
  $o8_qr_url = o8_media_url($bank['qr_code']);
} elseif (!empty($bank['branch_qr_url'])) {
  $o8_qr_url = o8_media_url($bank['branch_qr_url']);
}

$dummy_hotel = BASE_URL . 'images/hotel.png';
$dummy_itin  = BASE_URL . 'images/itinerary.png';
$dummy_vehicle = BASE_URL . 'images/vehicle.png';

$o8_cost_note = o8nv(isset($incx['note']) ? $incx['note'] : '', 'All prices are subject to change based on seasonal demand, hotel availability and flight seat availability. Please confirm booking within 48 hours for guaranteed rates.');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= o8e($o8_dest) ?> — <?= o8e($o8_company) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link rel="stylesheet" href="option8.css">
</head>

<body>

  <!-- PAGE 1 — HERO -->
  <div class="page">
    <section class="hero-section">
      <div class="hero-inner">
        <div class="hero-logo">
          <?php if ($o8_logo !== '') : ?>
            <img src="<?= o8e($o8_logo) ?>" alt="<?= o8e($o8_company) ?>" style="width:56px;height:56px;object-fit:contain;border-radius:50%;background:#fff;padding:6px;">
          <?php else : ?>
            <i class="fa-regular fa-compass"></i>
          <?php endif; ?>
        </div>
        <h1 class="hero-title"><?= o8e(strtoupper(o8nv($hero['tour_name'], $o8_dest))) ?></h1>
        <p class="hero-subtitle">Discover Extraordinary Experiences</p>
        <div class="hero-services">
          <div class="service"><i class="fa-solid fa-hotel"></i><span>Hotels</span></div>
          <?php if ($o8_show_flights) : ?><div class="service"><i class="fa-solid fa-plane"></i><span>Flights</span></div><?php endif; ?>
          <div class="service"><i class="fa-solid fa-car-side"></i><span>Transfer</span></div>
          <div class="service"><i class="fa-solid fa-location-dot"></i><span>Touring</span></div>
          <div class="service"><i class="fa-solid fa-utensils"></i><span>Meals</span></div>
          <div class="service"><i class="fa-regular fa-compass"></i><span>Activity</span></div>
        </div>
        <div class="hero-client">
          <small>Prepared Exclusively For</small>
          <h2><?= o8e($o8_client) ?></h2>
        </div>
        <div class="hero-bottom">
          <span>Explore Your Journey</span>
          <i class="fa-solid fa-angle-down"></i>
        </div>
      </div>
    </section>
  </div>

  <!-- PAGE 2 — WELCOME & OVERVIEW -->
  <div class="page">
    <section class="welcome-section">
      <div class="welcome-banner">
        <div class="container">
          <small>YOUR EXCLUSIVE JOURNEY</small>
          <h1>A Personalized Travel Experience Exclusively Designed for <?= o8e($o8_client) ?></h1>
        </div>
      </div>
      <div class="container">
        <div class="welcome-text">
          <h2>Welcome to Your Journey</h2>
          <p>Dear <?= o8e($o8_first) ?>,</p>
          <p>Thank you for choosing <?= o8e($o8_company) ?> for your upcoming journey. We are delighted to present this carefully crafted proposal designed to provide memorable experiences and seamless arrangements.</p>
        </div>
        <h2 class="overview-heading">Tour Overview</h2>
        <div class="overview-grid">
          <div class="overview-card"><label>Quotation ID</label><strong><?= o8e($o8_quot_code) ?></strong></div>
          <div class="overview-card"><label>Tour ID</label><strong><?= o8e($o8_tour_id) ?></strong></div>
          <div class="overview-card"><label>Quotation Date</label><strong><?= o8e(o8nv($ov['quotation_date'], '')) ?></strong></div>
          <div class="overview-card"><label>Travel Date</label><strong><?= o8e($o8_travel_range) ?></strong></div>
          <div class="overview-card"><label>Duration</label><strong><?= o8e($o8_duration) ?></strong></div>
          <div class="overview-card"><label>Total Guests</label><strong><?= o8e(o8_guest_label($ov)) ?></strong></div>
        </div>
        <div class="package-box">
          <small>PACKAGE TYPE</small>
          <h2><?= o8e(strtoupper($o8_pkg_ov)) ?></h2>
        </div>
        <div class="prepared-wrapper">
          <div class="prepared-col">
            <h3>Prepared For</h3>
            <div class="info-item">
              <i class="fa-regular fa-user"></i>
              <div><span>Client Name</span><strong><?= o8e($o8_client) ?></strong></div>
            </div>
            <div class="info-item">
              <i class="fa-regular fa-envelope"></i>
              <div><span>Email Address</span><strong><?= o8e(o8nv($ov['customer_email'], o8nv($hero['user_email_id'], ''))) ?></strong></div>
            </div>
            <div class="info-item">
              <i class="fa-solid fa-phone"></i>
              <div><span>Mobile Number</span><strong><?= o8e(o8nv($ov['customer_mobile'], o8nv($hero['user_contact'], ''))) ?></strong></div>
            </div>
          </div>
          <div class="prepared-col prepared-by">
            <h3>Prepared By</h3>
            <div class="info-item right">
              <div><span>Username</span><strong><?= o8e($o8_consult_name) ?></strong></div>
              <i class="fa-regular fa-user"></i>
            </div>
            <div class="info-item right">
              <div><span>Email ID</span><strong><?= o8e($o8_consult_email) ?></strong></div>
              <i class="fa-regular fa-envelope"></i>
            </div>
            <div class="info-item right">
              <div><span>Mobile Number</span><strong><?= o8e($o8_consult_phone) ?></strong></div>
              <i class="fa-solid fa-phone"></i>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- ACCOMMODATION -->
  <?php if (!empty($hotels)) : ?>
    <div class="page">
      <section class="accommodation-section">
        <div class="section-gradient-header">
          <div class="container">
            <span class="section-tag">YOUR ACCOMMODATION</span>
            <h2>Accommodation Details</h2>
            <p>PACKAGE TYPE - <?= o8e(strtoupper($o8_pkg_ov)) ?></p>
          </div>
        </div>
        <div class="container">
          <div class="hotel-grid">
            <?php foreach ($hotels as $h) :
              $room_label = o8nv($h['room_category'], o8nv($h['room_type'], 'Standard Room'));
              $hphoto_raw = isset($h['hotel_photo']) ? trim($h['hotel_photo']) : '';
              $hphoto = ($hphoto_raw === '' || stripos($hphoto_raw, 'dummy') !== false)
                ? $dummy_hotel : o8img($hphoto_raw, $dummy_hotel);
            ?>
              <div class="hotel-card">
                <div class="hotel-image">
                  <img src="<?= o8e($hphoto) ?>" alt="<?= o8e(o8nv($h['hotel_name'], 'Hotel')) ?>">
                  <span class="hotel-location"><?= o8e(o8nv($h['hotel_city'], $o8_dest)) ?></span>
                </div>
                <div class="hotel-body">
                  <h3><?= o8e(o8nv($h['hotel_name'], 'Hotel')) ?></h3>
                  <small class="hotel-category">ROOM CATEGORY</small>
                  <div class="room-name"><?= o8e($room_label) ?></div>
                  <div class="hotel-dates">
                    <div><span>CHECK-IN</span><strong><?= o8e(o8nv($h['check_in'], '')) ?></strong></div>
                    <div><span>CHECK-OUT</span><strong><?= o8e(o8nv($h['check_out'], '')) ?></strong></div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </div>
  <?php endif; ?>

  <!-- FLIGHTS -->
  <?php if ($o8_show_flights) : ?>
    <div class="page">
      <section class="flight-section">
        <div class="container">
          <h2 class="flight-title">Flight Details</h2>
          <div class="flight-grid">
            <?php foreach ($flights as $f) :
              $from_code = o8_air_code(o8nv($f['from_city'], ''));
              $to_code   = o8_air_code(o8nv($f['to_city'], ''));
              $baggage   = o8nv($f['baggage'], 'As per airline policy');
              $duration  = o8nv($f['duration'], 'As per schedule');
            ?>
              <div class="flight-card">
                <div class="flight-route">
                  <div class="route-point"><span>From</span><h3><?= o8e($from_code) ?></h3></div>
                  <div class="route-plane"><i class="fa-solid fa-plane"></i></div>
                  <div class="route-point text-right"><span>To</span><h3><?= o8e($to_code) ?></h3></div>
                </div>
                <div class="flight-info">
                  <div class="info-block"><label>AIRLINE</label><strong><?= o8e(o8nv($f['airline_display'], o8nv($f['airline_name'], ''))) ?></strong></div>
                  <div class="info-block"><label>CLASS</label><strong><?= o8e(o8nv($f['class'], 'Economy')) ?></strong></div>
                  <div class="info-block"><label>DEPARTURE</label><strong><?= o8e(o8nv($f['departure_datetime'], '')) ?></strong></div>
                  <div class="info-block"><label>ARRIVAL</label><strong><?= o8e(o8nv($f['arrival_datetime'], '')) ?></strong></div>
                </div>
                <div class="flight-footer">
                  <div class="footer-box">
                    <i class="fa-regular fa-clock"></i>
                    <div><small>Duration</small><strong><?= o8e($duration) ?></strong></div>
                  </div>
                  <div class="footer-box">
                    <i class="fa-solid fa-suitcase"></i>
                    <div><small>Baggage</small><strong><?= o8e($baggage) ?></strong></div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </div>
  <?php endif; ?>

  <!-- TRANSPORTATION & ITINERARY -->
  <div class="page">
    <?php if ($o8_show_vehs) : ?>
      <section class="transport-section">
        <div class="container">
          <h2 class="section-title">Transportation</h2>
          <div class="transport-grid">
            <?php foreach ($vehs as $v) :
              $v_start = o8nv($v['date'], '');
              $v_end   = o8_vehicle_end_date($v);
              $v_dur   = o8nv($v['service_duration'], o8nv($v['description'], 'As per itinerary'));
            ?>
              <div class="transport-card">
                <h3 class="vehicle-title"><?= o8e(o8nv($v['vehicle_name'], 'Vehicle')) ?></h3>
                <div class="transport-meta">
                  <div class="meta-box"><label>START DATE</label><strong><?= o8e($v_start) ?></strong></div>
                  <div class="meta-box"><label>END DATE</label><strong><?= o8e($v_end) ?></strong></div>
                  <div class="meta-box"><label>TOTAL VEHICLES</label><strong><?= o8e(o8nv($v['vehicle_count'], '1')) ?></strong></div>
                </div>
                <div class="pickup-drop-box">
                  <div class="location-row">
                    <i class="fa-solid fa-location-dot"></i>
                    <div><span>PICKUP</span><strong><?= o8e(o8nv($v['pickup'], 'NA')) ?></strong></div>
                  </div>
                  <div class="location-row">
                    <i class="fa-solid fa-location-dot"></i>
                    <div><span>DROP</span><strong><?= o8e(o8nv($v['drop'], 'NA')) ?></strong></div>
                  </div>
                </div>
                <div class="transport-footer">
                  <div class="footer-card">
                    <i class="fa-regular fa-clock"></i>
                    <div><small>Duration</small><strong><?= o8e($v_dur) ?></strong></div>
                  </div>
                  <div class="footer-card">
                    <i class="fa-solid fa-car-side"></i>
                    <div><small>Vehicle</small><strong><?= o8e(o8nv($v['vehicle_count'], '1')) ?></strong></div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <?php if (!empty($itin)) : ?>
      <section class="itinerary-section">
        <div class="section-gradient-header">
          <div class="container">
            <span class="section-tag">YOUR JOURNEY DETAILS</span>
            <h2>Day Wise Itinerary</h2>
          </div>
        </div>
        <div class="container">
          <?php foreach ($itin as $day) :
            $day_img_raw = isset($day['image']) ? trim($day['image']) : '';
            $day_img = ($day_img_raw === '' || stripos($day_img_raw, 'dummy') !== false)
              ? $dummy_itin : o8img($day_img_raw, $dummy_itin);
            $day_title = o8nv($day['special_attraction'], o8nv($day['city'], 'Sightseeing'));
          ?>
            <div class="itinerary-card">
              <div class="day-badge">Day <?= o8e(o8nv($day['day_number'], '')) ?></div>
              <div class="itinerary-image"><img src="<?= o8e($day_img) ?>" alt="Day <?= o8e(o8nv($day['day_number'], '')) ?>"></div>
              <div class="itinerary-header">
                <div>
                  <small><?= o8e(o8nv($day['city'], $o8_dest)) ?></small>
                  <h3><?= o8e($day_title) ?></h3>
                </div>
                <div class="date-box"><span>DATE</span><strong><?= o8e(o8nv($day['date'], '')) ?></strong></div>
              </div>
              <div class="program-row">
                <i class="fa-regular fa-map"></i>
                <div>
                  <label>DETAILED PROGRAM</label>
                  <p><?= o8e(o8nv($day['detailed_programme'], '')) ?></p>
                </div>
              </div>
              <div class="itinerary-footer">
                <div class="footer-item">
                  <i class="fa-regular fa-moon"></i>
                  <div><small>OVERNIGHT STAY</small><strong><?= o8e(o8nv($day['overnight_stay'], '')) ?></strong></div>
                </div>
                <div class="footer-item">
                  <i class="fa-solid fa-utensils"></i>
                  <div><small>MEAL PLAN</small><strong><?= o8e(o8nv($day['meal_plan'], '')) ?></strong></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  </div>

  <!-- INCLUSIONS / EXCLUSIONS -->
  <div class="page">
    <section class="package-section">
      <div class="section-gradient-header">
        <div class="container">
          <span class="section-tag">PACKAGE DETAILS</span>
          <h2>Inclusions, Exclusions &amp; Costing</h2>
        </div>
      </div>
      <div class="container">
        <div class="package-grid">
          <div class="package-card included">
            <h3>What's Included</h3>
            <ul>
              <?php foreach ($o8_included as $item) : ?>
                <li><i class="fa-solid fa-check"></i> <?= o8e($item) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="package-card excluded">
            <h3>What's Excluded</h3>
            <ul>
              <?php foreach ($o8_excluded as $item) : ?>
                <li><i class="fa-solid fa-xmark"></i> <?= o8e($item) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <div class="container">
      <h2 class="section-title">Costing Details</h2>
      <div class="costing-card">
        <table class="costing-table">
          <?php if (!$o8_is_per_person) : ?>
            <thead>
              <tr>
                <th>Package Type</th>
                <th>Tour Cost</th>
                <th>Tax</th>
                <th>TCS</th>
                <th>Grand Total</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $o8_gcnt = count($o8_cost_grp);
              foreach ($o8_cost_grp as $ci => $row) :
                $is_rec = o8_is_recommended_pkg(o8nv($row['package_type'], ''), $ci, $o8_gcnt);
              ?>
                <tr<?= $is_rec ? ' class="recommended-row"' : '' ?>>
                  <td>
                    <strong><?= o8e(o8nv($row['package_type'], 'Package')) ?></strong>
                    <?php if ($is_rec) : ?><span class="recommended-badge">RECOMMENDED</span><?php endif; ?>
                  </td>
                  <td><?= o8e(o8nv($row['tour_cost_display'], '0')) ?></td>
                  <td><?= o8e(o8nv($row['tax_display'], '0')) ?></td>
                  <td><?= o8e(o8nv($row['tcs_display'], '0')) ?></td>
                  <td class="<?= $is_rec ? 'highlight-total' : 'grand-total' ?>"><?= o8e(o8nv($row['total_display'], '0')) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          <?php else : ?>
            <thead>
              <tr>
                <th>Package Type</th>
                <th>Adult</th>
                <th>CWB</th>
                <th>CWNB</th>
                <th>Infant</th>
                <th>Grand Total</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $o8_pcnt = count($o8_cost_pp);
              foreach ($o8_cost_pp as $pi => $row) :
                $is_rec = o8_is_recommended_pkg(o8nv($row['package_type'], ''), $pi, $o8_pcnt);
              ?>
                <tr<?= $is_rec ? ' class="recommended-row"' : '' ?>>
                  <td>
                    <strong><?= o8e(o8nv($row['package_type'], 'Package')) ?></strong>
                    <?php if ($is_rec) : ?><span class="recommended-badge">RECOMMENDED</span><?php endif; ?>
                  </td>
                  <td><?= o8e(o8nv($row['pp_adult_display'], '0')) ?></td>
                  <td><?= o8e(o8nv($row['pp_cwb_display'], '0')) ?></td>
                  <td><?= o8e(o8nv($row['pp_cwnb_display'], '0')) ?></td>
                  <td><?= o8e(o8nv($row['pp_infant_display'], '0')) ?></td>
                  <td class="<?= $is_rec ? 'highlight-total' : 'grand-total' ?>"><?= o8e(o8nv($row['grand_total_display'], '0')) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          <?php endif; ?>
        </table>
      </div>
      <div class="cost-note"><strong>Note:</strong> <?= o8e($o8_cost_note) ?></div>
    </div>
  </div>

  <!-- PAYMENT -->
  <div class="section-gradient-header">
    <div class="container">
      <span class="section-tag">PAYMENT INFORMATION</span>
      <h2>Payment Details</h2>
    </div>
  </div>

  <div class="page">
    <section class="payment-section">
      <div class="container">
        <div class="payment-grid">
          <div class="bank-card">
            <h3>Bank Account Details</h3>
            <div class="bank-row">
              <div><label>ACCOUNT NAME</label><p><?= o8e(o8nv($bank['account_name'], o8nv($ty['company_name'], $o8_company))) ?></p></div>
              <i class="fa-regular fa-copy"></i>
            </div>
            <div class="bank-row">
              <div><label>ACCOUNT NUMBER</label><p><?= o8e(o8nv($bank['account_no'], 'NA')) ?></p></div>
              <i class="fa-regular fa-copy"></i>
            </div>
            <div class="bank-row">
              <div><label>BANK NAME</label><p><?= o8e(o8nv($bank['bank_name'], 'NA')) ?></p></div>
              <i class="fa-regular fa-copy"></i>
            </div>
            <?php if (!empty($bank['ifsc_code'])) : ?>
              <div class="bank-row">
                <div><label>IFSC CODE</label><p><?= o8e($bank['ifsc_code']) ?></p></div>
                <i class="fa-regular fa-copy"></i>
              </div>
            <?php endif; ?>
            <div class="bank-row last">
              <div><label>UPI ID</label><p><?= o8e(o8nv($bank['upi_id'], 'NA')) ?></p></div>
              <i class="fa-regular fa-copy"></i>
            </div>
          </div>
          <div class="qr-card">
            <h4>Scan to Pay</h4>
            <div class="qr-wrapper">
              <?php if (!empty($bank['qr_html'])) : ?>
                <?= $bank['qr_html'] ?>
              <?php elseif ($o8_qr_url !== '') : ?>
                <img src="<?= o8e($o8_qr_url) ?>" alt="QR Code">
              <?php else : ?>
                <img src="<?= o8e(BASE_URL . 'images/qr.png') ?>" alt="QR Code" onerror="this.style.display='none'">
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- BOOKING POLICY -->
  <div class="page">
    <section class="booking-policy-section">
      <div class="container">
        <h2 class="section-title">Booking Policy</h2>
        <div class="policy-grid">
          <?php
          $o8_policy_titles = array('Advance Payment', 'Balance Payment', 'Cancellation Charges', 'Confirmation');
          foreach ($o8_book_policy as $pi => $policy) :
            $ptitle = isset($o8_policy_titles[$pi]) ? $o8_policy_titles[$pi] : 'Policy';
          ?>
            <div class="policy-card">
              <h4><?= o8e($ptitle) ?></h4>
              <p><?= o8e($policy) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="payment-instructions">
          <h3>Payment Instructions</h3>
          <ul>
            <?php foreach ($o8_pay_notes as $ni => $note) : ?>
              <li><span class="step-no"><?= o8e($ni + 1) ?></span><?= o8e($note) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="important-notice">
          <div class="notice-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
          <div class="notice-content">
            <h4>Important Notice</h4>
            <p>Please ensure the amount is transferred from the account holder's name mentioned in the booking form. Any discrepancy may delay confirmation. GST invoice will be provided after payment confirmation.</p>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- TESTIMONIALS -->
  <?php if (!empty($testimonials)) : ?>
    <div class="page">
      <section class="testimonial-section">
        <div class="section-gradient-header">
          <div class="container">
            <span class="section-tag">CLIENT VOICES</span>
            <h2>What Our Travellers Say</h2>
            <p class="section-subtitle">Join thousands of satisfied customers who have experienced our exceptional service.</p>
          </div>
        </div>
        <div class="container">
          <div class="testimonial-grid">
            <?php
            $o8_ti = 0;
            foreach ($testimonials as $t) :
              if ($o8_ti >= 6) {
                break;
              }
              $o8_ti++;
              $tphoto = o8_media_url(isset($t['photo']) ? $t['photo'] : '');
            ?>
              <div class="testimonial-card">
                <div class="testimonial-top"></div>
                <?php if ($tphoto !== '') : ?>
                  <img src="<?= o8e($tphoto) ?>" class="testimonial-avatar" alt="<?= o8e(o8nv($t['name'], '')) ?>">
                <?php else : ?>
                  <img src="<?= o8e(BASE_URL . 'images/person.jpg') ?>" class="testimonial-avatar" alt="<?= o8e(o8nv($t['name'], '')) ?>" onerror="this.style.visibility='hidden'">
                <?php endif; ?>
                <div class="testimonial-content">
                  <h3><?= o8e(o8nv($t['name'], 'Traveller')) ?></h3>
                  <span class="testimonial-destination"><?= o8e(o8nv($t['designation'], $o8_dest)) ?></span>
                  <div class="testimonial-stars">★★★★★</div>
                  <p>&ldquo;<?= o8e(o8nv($t['review'], '')) ?>&rdquo;</p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </div>
  <?php endif; ?>

  <!-- TRUST STATS -->
  <div class="page">
    <section class="stats-section">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-card gold">
            <div class="stat-number"><?= o8e($o8_traveller_cnt) ?></div>
            <div class="stat-title">Happy Travellers</div>
            <div class="stat-text">Have experienced our exceptional service</div>
          </div>
          <div class="stat-card teal">
            <div class="stat-number"><?= o8e($o8_google_rating) ?></div>
            <div class="stat-title">Average Rating</div>
            <div class="stat-text">Based on customer reviews</div>
          </div>
          <div class="stat-card gold">
            <div class="stat-number"><?= o8e($o8_review_count) ?></div>
            <div class="stat-title">5-Star Reviews</div>
            <div class="stat-text">From verified customers</div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- TERMS -->
  <div class="page">
    <section class="terms-section">
      <div class="section-gradient-header">
        <div class="container">
          <span class="section-tag">IMPORTANT INFORMATION</span>
          <h2>Terms &amp; Conditions</h2>
          <p class="section-subtitle">Please read these carefully before finalizing your booking</p>
        </div>
      </div>
      <div class="container">
        <div class="terms-intro">
          <p>By booking with us, you agree to all the terms and conditions mentioned below. These terms are binding and govern your relationship with <strong><?= o8e($o8_company) ?></strong>. Please ensure you understand and accept all conditions before making your final payment.</p>
        </div>
        <div class="terms-card">
          <h3><i class="fa-regular fa-circle-check"></i> <?= o8e(o8nv($terms['title'], 'Booking Policy')) ?></h3>
          <ul>
            <?php if (!empty($o8_term_lines)) :
              foreach ($o8_term_lines as $line) : ?>
                <li><?= o8e($line) ?></li>
              <?php endforeach;
            else : ?>
              <li>Booking confirmation is subject to receipt of advance payment.</li>
              <li>All bookings must be made as per company policy and availability.</li>
              <li>Modifications to bookings are subject to availability and additional charges may apply.</li>
              <li>A completed booking form with valid identification is required at the time of booking.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </section>
  </div>

  <!-- THANK YOU -->
  <div class="page">
    <section class="thankyou-hero">
      <div class="thankyou-container">
        <div class="thankyou-logo">
          <div class="logo-circle">
            <?php if ($o8_logo !== '') : ?>
              <img src="<?= o8e($o8_logo) ?>" alt="<?= o8e($o8_company) ?>" style="width:100%;height:100%;object-fit:contain;padding:6px;background:#fff;border-radius:50%;">
            <?php else : ?>
              <i class="fa-solid fa-location-dot"></i>
            <?php endif; ?>
          </div>
        </div>
        <h1 class="company-name"><?= o8e($o8_company) ?></h1>
        <div class="company-tagline">Your Travel Companion</div>
        <div class="thankyou-card">
          <h2>Thank You!</h2>
          <p>We look forward to creating unforgettable travel memories for you.</p>
          <span>Your journey begins with us!</span>
        </div>
      </div>

      <div class="container">
        <div class="footer-contact-grid">
          <div class="footer-info-card">
            <h3><i class="fa-solid fa-location-dot"></i> Office Address</h3>
            <p><?= o8e($o8_company) ?></p>
            <p><?= o8e(o8nv($ty['company_address'], '')) ?></p>
          </div>
          <div class="footer-info-card">
            <h3>Contact Us</h3>
            <ul>
              <li><i class="fa-solid fa-phone"></i> <?= o8e(o8nv($ty['company_contact'], '')) ?></li>
              <li><i class="fa-solid fa-envelope"></i> <?= o8e(o8nv($ty['company_email'], '')) ?></li>
              <li><i class="fa-solid fa-globe"></i> <?= o8e(o8nv($ty['website'], '')) ?></li>
            </ul>
          </div>
        </div>
        <div class="footer-stats-grid">
          <div class="footer-stat-card">
            <i class="fa-regular fa-star"></i>
            <h4><?= o8e($o8_google_rating) ?></h4>
            <span>Google Rating</span>
            <small>Based on <?= o8e($o8_review_count) ?> reviews</small>
          </div>
          <div class="footer-stat-card">
            <i class="fa-solid fa-users"></i>
            <h4><?= o8e($o8_traveller_cnt) ?></h4>
            <span>Happy Travellers</span>
            <small>Across the globe</small>
          </div>
          <div class="footer-stat-card">
            <i class="fa-solid fa-location-dot"></i>
            <h4>50+</h4>
            <span>Destinations Covered</span>
            <small>Worldwide</small>
          </div>
        </div>
      </div>

      <div class="container">
        <div class="prepared-card">
          <span class="prepared-label">PREPARED BY</span>
          <h3><?= o8e($o8_consult_name) ?></h3>
          <h4>Travel Consultant</h4>
          <p>Your dedicated travel expert for personalized experiences</p>
        </div>
        <div class="social-section">
          <h3>Follow Us On Social Media</h3>
          <div class="social-icons">
            <?php if (!empty($ty['website'])) : ?>
              <a href="<?= o8e($ty['website']) ?>" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
              <a href="<?= o8e($ty['website']) ?>" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
              <a href="<?= o8e($ty['website']) ?>" target="_blank" rel="noopener"><i class="fab fa-linkedin-in"></i></a>
            <?php else : ?>
              <a href="#"><i class="fab fa-facebook-f"></i></a>
              <a href="#"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fab fa-linkedin-in"></i></a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  </div>

</body>

</html>
