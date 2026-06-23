<?php

/**
 * OPTION-4 (quotation_html_4) — Package Tour Quotation
 * Layout/CSS from Final-Designs/Option-4-Done/option-4.html
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
$vehs         = $q['vehicles'];
$trains       = isset($q['trains']) ? $q['trains'] : array();
$acts         = isset($q['activities']) ? $q['activities'] : array();
$itin         = $q['itinerary'];
$incx         = $q['inclusion_exclusion'];
$cost         = $q['costing'];
$bank         = $q['bank_details'];
$terms        = $q['terms_conditions'];
$ty           = $q['thank_you'];
$present      = $q['sections_present'];
$gallery      = isset($q['gallery_images']) && is_array($q['gallery_images']) ? $q['gallery_images'] : array();
$assets       = "assets/";
$testimonials = array();
$o4_cfg       = array();
if (function_exists('gqb_get_config')) {
  $o4_cfg = gqb_get_config();
  $testimonials = isset($o4_cfg['testimonials']) && is_array($o4_cfg['testimonials'])
    ? $o4_cfg['testimonials'] : array();
}

if (!function_exists('o4e')) {
  function o4e($v)
  {
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
  }
}
if (!function_exists('o4nv')) {
  function o4nv($v, $f = '')
  {
    return ($v !== null && $v !== '') ? $v : $f;
  }
}
if (!function_exists('o4_media_url')) {
  function o4_media_url($url)
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
if (!function_exists('o4img')) {
  function o4img($url, $fallback)
  {
    $resolved = o4_media_url($url);
    return $resolved !== '' ? $resolved : $fallback;
  }
}
if (!function_exists('o4_guest_label')) {
  function o4_guest_label($ov)
  {
    $p = isset($ov['pax']) ? $ov['pax'] : array();
    $parts = array();
    $ad = (int) o4nv(isset($p['adult']) ? $p['adult'] : 0, 0);
    $ch = (int) o4nv(isset($p['children_with_bed']) ? $p['children_with_bed'] : 0, 0)
      + (int) o4nv(isset($p['children_without_bed']) ? $p['children_without_bed'] : 0, 0);
    $inf = (int) o4nv(isset($p['infant']) ? $p['infant'] : 0, 0);
    if ($ad) {
      $parts[] = $ad . ' Adult' . ($ad > 1 ? 's' : '');
    }
    if ($ch) {
      $parts[] = $ch . ' Child' . ($ch > 1 ? 'ren' : '');
    }
    if ($inf) {
      $parts[] = $inf . ' Infant' . ($inf > 1 ? 's' : '');
    }
    return $parts ? implode(', ', $parts) : o4nv($ov['guest_count'], '-');
  }
}
if (!function_exists('o4_split_lines')) {
  function o4_split_lines($html, $fallback = array())
  {
    $text = trim(strip_tags(str_replace(array('<br>', '<br/>', '<br />', '</p>', '</li>'), "\n", (string) $html)));
    $items = preg_split('/\r\n|\r|\n|•|\x{2022}/u', $text);
    $items = array_values(array_filter(array_map('trim', (array) $items)));
    return $items ? $items : $fallback;
  }
}
if (!function_exists('o4_air_code')) {
  function o4_air_code($loc)
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
if (!function_exists('o4_vehicle_end_date')) {
  function o4_vehicle_end_date($v)
  {
    if (!empty($v['end_date_raw']) && function_exists('get_date_user')) {
      return get_date_user($v['end_date_raw']);
    }
    return o4nv(isset($v['end_date_raw']) ? $v['end_date_raw'] : '', o4nv(isset($v['end_date']) ? $v['end_date'] : '', ''));
  }
}
if (!function_exists('o4_render_vl_logo')) {
  function o4_render_vl_logo($hero, $dark = false)
  {
    $logo = o4nv($hero['company_logo'], '');
    $name = o4nv($hero['company_name'], 'Travel Partner');
    $icon_style = $dark ? '' : ' style="border-color:var(--navy);color:var(--navy);"';
    $name_style = $dark ? '' : ' style="color:var(--text);"';
?>
    <div class="vl-logo">
      <?php if ($logo !== '') : ?>
        <img src="<?= o4e($logo) ?>" alt="<?= o4e($name) ?>" class="company-logo-img" />
      <?php else : ?>
        <div class="vl-logo-icon" <?= $icon_style ?>>◎</div>
      <?php endif; ?>
      <div class="vl-logo-text">
        <div class="name" <?= $name_style ?>><?= o4e($name) ?></div>
        <div class="tagline">Curated Luxury Journeys</div>
      </div>
    </div>
  <?php
  }
}
if (!function_exists('o4_render_page_header')) {
  function o4_render_page_header($hero, $right_label)
  {
  ?>
    <div class="page-header">
      <?php o4_render_vl_logo($hero, false); ?>
      <div class="page-header-right"><?= o4e($right_label) ?></div>
    </div>
<?php
  }
}

$o4_dest       = o4nv($ov['destination'], o4nv($hero['tour_name'], 'Tour'));
$o4_client     = o4nv($ov['client_name'], o4nv($hero['client_name'], 'Guest'));
$o4_tour_id    = o4nv($hero['package_code'], o4nv($ov['tour_id'], ''));
$o4_duration   = o4nv($ov['duration_label'], o4nv($hero['duration_label'], ''));
$o4_travel_from = o4nv($ov['travel_from'], '');
$o4_travel_to   = o4nv($ov['travel_to'], '');
$o4_travel_range = trim($o4_travel_from . ($o4_travel_to !== '' ? ' — ' . $o4_travel_to : ''));
$o4_pkg_badge  = '';
if (!empty($cost['computed']['group'][0]['package_type'])) {
  $o4_pkg_badge = $cost['computed']['group'][0]['package_type'];
} elseif (!empty($hotels[0]['package_type'])) {
  $o4_pkg_badge = $hotels[0]['package_type'];
}
$o4_pkg_ov = o4nv($o4_pkg_badge, o4nv($ov['package_type_label'], 'Package'));
$o4_included = o4_split_lines(isset($incx['included']) ? $incx['included'] : '', array('Inclusions as per itinerary.'));
$o4_excluded = o4_split_lines(isset($incx['excluded']) ? $incx['excluded'] : '', array('Exclusions as per company policy.'));
$o4_cost_grp = isset($cost['computed']['group']) ? $cost['computed']['group'] : array();
if (empty($o4_cost_grp)) {
  $o4_cost_grp = array(array(
    'package_type' => 'Package',
    'tour_cost_display' => '0',
    'tax_display' => '0',
    'tcs_display' => '0',
    'travel_display' => '0',
    'total_display' => '0',
  ));
}
$o4_featured_idx = count($o4_cost_grp) >= 3 ? 1 : 0;
$o4_pay_notes = o4_split_lines(
  o4nv(isset($incx['quot_note']) ? $incx['quot_note'] : '', ''),
  array(
    'Mention your Quotation ID in the payment reference.',
    'Share the payment screenshot with your consultant.',
    'An official receipt will be issued within 24 hours.',
    'Cheque payments to be drawn in the company name.',
  )
);
$o4_book_policy = o4_split_lines(
  o4nv(isset($incx['note']) ? $incx['note'] : '', ''),
  array(
    '50% advance required to confirm the booking.',
    'Balance payment due 21 days before departure.',
    'Rates are subject to availability and currency changes.',
    'All payments are protected and securely processed.',
  )
);
$o4_term_lines = o4_split_lines(isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '', array());
$o4_company    = o4nv($hero['company_name'], 'Travel Partner');
$o4_salutation = o4nv($ov['client_name'], $o4_client);
$o4_cover_img  = o4img(o4nv($hero['cover_image'], ''), !empty($gallery[0]) ? o4_media_url($gallery[0]) : $assets . 'cover.jpg');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= o4e($o4_dest) ?> Tour Package – <?= o4e($o4_company) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link href="option4.css" rel="stylesheet" />
</head>

<body>
  <div class="doc">

    <!-- COVER -->
    <div class="cover" style="background: linear-gradient(to bottom,rgba(10,22,50,.35) 0%,rgba(10,22,50,.55) 50%,rgba(10,22,50,.92) 100%), url('<?= o4e($o4_cover_img) ?>') center/cover no-repeat;">
      <img class="cover-bg-img"
        src="<?= o4e($o4_cover_img) ?>"
        alt="<?= o4e($o4_dest) ?>">

      <div class="cover-overlay"></div>
      <div class="cover-top-bar">
        <?php o4_render_vl_logo($hero, true); ?>
        <div class="cover-badge">Travel Proposal</div>
      </div>
      <div class="cover-center">
        <div class="cover-presents">Presents</div>
        <div class="cover-title"><?= o4e($o4_dest) ?></div>
        <div class="cover-subtitle">Tour Package</div>
        <div class="cover-tagline">Discover Extraordinary Experiences</div>
        <div class="cover-icons">
          <div class="cover-icon-pill">
            <div class="icon">🏨</div>
            <div class="label">Hotels</div>
          </div>
          <div class="cover-icon-pill">
            <div class="icon">✈️</div>
            <div class="label">Flights</div>
          </div>
          <div class="cover-icon-pill">
            <div class="icon">📸</div>
            <div class="label">Activities</div>
          </div>
          <div class="cover-icon-pill">
            <div class="icon">🚐</div>
            <div class="label">Transfers</div>
          </div>
          <div class="cover-icon-pill">
            <div class="icon">📍</div>
            <div class="label">Sightseeing</div>
          </div>
          <div class="cover-icon-pill">
            <div class="icon">🍽️</div>
            <div class="label">Meals</div>
          </div>
        </div>
      </div>
      <div class="cover-bottom">
        <div class="cover-bottom-bar">
          <div>
            <div class="cover-client-label">Prepared Exclusively For</div>
            <div class="cover-client-name"><?= o4e($o4_client) ?></div>
          </div>
          <div class="cover-trip-info">
            <?php if ($o4_duration !== '') : ?>
              <div class="cover-trip-nights"><?= o4e($o4_duration) ?></div>
            <?php endif; ?>
            <?php if ($o4_travel_range !== '') : ?>
              <div class="cover-trip-dates"><?= o4e($o4_travel_range) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- PAGE 2 – OVERVIEW -->
    <?php o4_render_page_header($hero, $o4_pkg_ov); ?>

    <div class="page-section">
      <div class="personal-banner">
        <div class="eyebrow">Personalized For You</div>
        <h2>A Personalized Travel Experience<br />Exclusively Designed for <span><?= o4e($o4_client) ?></span></h2>
      </div>

      <div class="salutation-card">
        <div class="dear">Dear <?= o4e($o4_salutation) ?>,</div>
        <p>Thank you for choosing <?= o4e($o4_company) ?> for your upcoming journey. We are delighted to present this carefully crafted travel proposal designed to provide memorable experiences, seamless arrangements, and exceptional hospitality throughout your trip. Every detail has been thoughtfully curated to ensure your <?= o4e($o4_dest) ?> escape is nothing short of extraordinary.</p>
      </div>

      <div class="sec-eyebrow">Your Journey at a Glance</div>
      <div class="sec-heading">Tour Overview</div>

      <div class="ov-grid">
        <div class="ov-card">
          <div class="icon">#</div>
          <div class="lbl">Quotation ID</div>
          <div class="val"><?= o4e(o4nv($hero['quotation_code'], '')) ?></div>
        </div>
        <div class="ov-card">
          <div class="icon">🏷️</div>
          <div class="lbl">Tour ID</div>
          <div class="val"><?= o4e($o4_tour_id) ?></div>
        </div>
        <div class="ov-card">
          <div class="icon">📅</div>
          <div class="lbl">Quotation Date</div>
          <div class="val"><?= o4e(o4nv($ov['quotation_date'], '')) ?></div>
        </div>
      </div>
      <div class="ov-grid" style="margin-bottom:20px;">
        <div class="ov-card">
          <div class="icon">✈️</div>
          <div class="lbl">Travel Date</div>
          <div class="val"><?= o4e($o4_travel_range !== '' ? $o4_travel_range : ($o4_travel_from . ($o4_travel_to !== '' ? ' – ' . $o4_travel_to : ''))) ?></div>
        </div>
        <div class="ov-card">
          <div class="icon">⏱️</div>
          <div class="lbl">Duration</div>
          <div class="val"><?= o4e($o4_duration) ?></div>
        </div>
        <div class="ov-card">
          <div class="icon">👥</div>
          <div class="lbl">Guests</div>
          <div class="val"><?= o4e(o4_guest_label($ov)) ?></div>
        </div>
      </div>

      <div class="prep-bar">
        <div class="prep-lbl">Prepared For</div>
        <div class="prep-row">
          <div class="prep-item"><span class="pi">👤</span> <?= o4e($o4_client) ?></div>
          <div class="prep-item">
            <span class="pi">✉️</span>
            <a href="mailto:<?= o4e(o4nv($ov['customer_email'], o4nv($hero['user_email_id'], ''))) ?>">
              <?= o4e(o4nv($ov['customer_email'], o4nv($hero['user_email_id'], ''))) ?>
            </a>
          </div>

          <div class="prep-item">
            <span class="pi">📞</span>
            <a href="tel:<?= o4e(preg_replace('/\D+/', '', o4nv($ov['customer_mobile'], o4nv($hero['user_contact'], '')))) ?>">
              <?= o4e(o4nv($ov['customer_mobile'], o4nv($hero['user_contact'], ''))) ?>
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="page-num">PAGE 02 / 09</div>

    <!-- PAGE 3 – ACCOMMODATION -->
    <div class="page-header">
      <?php o4_render_vl_logo($hero, false); ?>
      <?php if ($o4_pkg_badge !== '') : ?>
        <div class="pkg-pill"><?= o4e($o4_pkg_badge) ?></div>
      <?php else : ?>
        <div class="page-header-right"><?= o4e($o4_pkg_ov) ?></div>
      <?php endif; ?>
    </div>

    <div class="page-section">
      <div class="sec-eyebrow">Where You'll Stay</div>
      <div class="sec-heading">Accommodation Details</div>

      <?php
      $o4_hi = 0;
      if (!empty($hotels)) :
        foreach ($hotels as $h) :
          $o4_hi++;
          // $hphoto = o4img(isset($h['hotel_photo']) ? $h['hotel_photo'] : '', $assets . 'hotel-' . (($o4_hi - 1) % 3 + 1) . '.jpg');
          $hphoto = !empty($h['hotel_photo'])
            ? o4img($h['hotel_photo'], '')
            : BASE_URL . 'images/hotel.png';
          $room_label = o4nv($h['room_category'], o4nv($h['room_type'], ''));
          $nights = o4nv($h['total_nights'], '');
      ?>
          <div class="hotel-card">
            <div class="hotel-card-inner">
              <div class="hotel-img">
                <img src="<?= o4e($hphoto) ?>" alt="<?= o4e(o4nv($h['hotel_name'], 'Hotel')) ?>" />
                <div class="hotel-img-badge">📍 <?= o4e(o4nv($h['hotel_city'], '')) ?></div>
              </div>
              <div class="hotel-body">
                <div class="hotel-head">
                  <div class="hotel-name"><?= o4e(o4nv($h['hotel_name'], 'Hotel')) ?></div>
                  <?php if ($nights !== '') : ?>
                    <div class="nights-badge">⏱ <?= o4e($nights) ?> Night<?= ((int) $nights > 1 ? 's' : '') ?></div>
                  <?php endif; ?>
                </div>
                <?php if ($room_label !== '') : ?>
                  <div class="hotel-room">🛏️ <?= o4e($room_label) ?></div>
                <?php endif; ?>
                <div class="hotel-dates">
                  <div class="date-chip">
                    <div class="dlbl">Check-In</div>
                    <div class="dval"><span class="dc-icon">→</span><?= o4e(o4nv($h['check_in'], '')) ?></div>
                  </div>
                  <div class="date-chip">
                    <div class="dlbl">Check-Out</div>
                    <div class="dval"><span class="dc-icon">↩</span><?= o4e(o4nv($h['check_out'], '')) ?></div>
                  </div>
                </div>
                <div class="amenities">
                  <?php if (!empty($h['meal_plan'])) : ?>
                    <div class="amenity"><span class="a-icon">🍳</span> <?= o4e($h['meal_plan']) ?></div>
                  <?php endif; ?>
                  <?php if (!empty($h['rating'])) : ?>
                    <div class="amenity"><span class="a-icon">⭐</span> <?= o4e($h['rating']) ?> Star</div>
                  <?php endif; ?>
                  <?php if (!empty($h['package_type'])) : ?>
                    <div class="amenity"><span class="a-icon">🏷️</span> <?= o4e($h['package_type']) ?></div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php
        endforeach;
      else :
        ?>
        <div class="hotel-card">
          <div class="hotel-card-inner">
            <div class="hotel-body" style="padding:24px;">
              <div class="hotel-name">Hotel details will be confirmed with your booking.</div>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <div class="hotel-note">All hotels are subject to availability at the time of confirmation. Similar category alternatives may be offered.</div>
    </div>
    <div class="page-num">PAGE 03 / 09</div>

    <!-- PAGE 4 – FLIGHTS & TRANSFERS -->
    <?php
    $o4_show_flights = !empty($present['flights']) && !empty($flights);
    $o4_show_vehs = !empty($present['vehicles']) && !empty($vehs);
    if ($o4_show_flights || $o4_show_vehs) :
      o4_render_page_header($hero, 'Flights & Transfers');
    ?>
      <div class="page-section">
        <?php if ($o4_show_flights) : ?>
          <div class="sec-eyebrow">Your Journey by Air</div>
          <div class="sec-heading">Flight Details</div>

          <?php foreach ($flights as $f) :
            $air_name = o4nv($f['airline_name'], o4nv($f['airline_display'], 'Flight'));
            $flight_lbl = o4nv($f['airline_code'], o4nv($f['airline_display'], ''));
            $from_code = o4_air_code(o4nv($f['from_city'], ''));
            $to_code = o4_air_code(o4nv($f['to_city'], ''));
            $duration = o4nv($f['duration'], '');
            $stop_type = o4nv($f['stop_type'], 'Non-Stop');
            $baggage = o4nv($f['baggage'], 'As per airline policy');
          ?>
            <div class="flight-card">
              <div class="flight-card-inner">
                <div class="flight-left">
                  <div class="flight-boarding">✈ Boarding</div>
                  <div>
                    <div class="flight-num"><?= o4e($flight_lbl) ?></div>
                    <div class="flight-class"><?= o4e(o4nv($f['class'], 'Economy')) ?></div>
                  </div>
                </div>
                <div class="flight-right">
                  <div class="flight-airline-row">
                    <div class="flight-airline"><?= o4e($air_name) ?></div>
                    <div class="flight-num-badge"><?= o4e($flight_lbl) ?></div>
                  </div>
                  <div class="flight-route">
                    <div>
                      <div class="flight-code"><?= o4e($from_code) ?></div>
                      <div class="flight-city"><?= o4e(o4nv($f['from_city'], '')) ?></div>
                    </div>
                    <div class="flight-middle">
                      <?php if ($duration !== '') : ?><div class="flight-duration"><?= o4e($duration) ?></div><?php endif; ?>
                      <div class="flight-arrow-line">
                        <div class="fal"></div>
                        <div class="fa-arrow">→</div>
                        <div class="fal"></div>
                      </div>
                      <div class="flight-nonstop"><?= o4e($stop_type) ?></div>
                    </div>
                    <div style="text-align:right;">
                      <div class="flight-code"><?= o4e($to_code) ?></div>
                      <div class="flight-city"><?= o4e(o4nv($f['to_city'], '')) ?></div>
                    </div>
                  </div>
                  <div class="flight-footer">
                    <div class="ff-item"><span class="ff-icon">⏰</span>
                      <div>
                        <div class="ff-lbl">Dep:</div>
                        <div class="ff-val"><?= o4e(o4nv($f['departure_datetime'], 'NA')) ?></div>
                      </div>
                    </div>
                    <div class="ff-div"></div>
                    <div class="ff-item"><span class="ff-icon">⏰</span>
                      <div>
                        <div class="ff-lbl">Arr:</div>
                        <div class="ff-val"><?= o4e(o4nv($f['arrival_datetime'], 'NA')) ?></div>
                      </div>
                    </div>
                    <div class="ff-div"></div>
                    <div class="ff-item"><span class="ff-icon">🧳</span>
                      <div>
                        <div class="ff-lbl">Baggage</div>
                        <div class="ff-val"><?= o4e($baggage) ?></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <!-- ============ Train Details -->
        <?php if (!empty($trains)) : ?>
          <div class="sec-eyebrow" style="margin-top:24px;">Your Journey by Rail</div>
          <div class="sec-heading">Train Details</div>

          <?php foreach ($trains as $tr) :
            $from_loc = isset($tr['from_location']) ? $tr['from_location'] : '';
            $to_loc = isset($tr['to_location']) ? $tr['to_location'] : '';
            $train_class = isset($tr['class']) ? $tr['class'] : 'NA';
            $from_date = isset($tr['from_date']) ? $tr['from_date'] : '';

            $total_pax = 0;
            if (isset($ov['pax']) && is_array($ov['pax'])) {
              $total_pax =
                (int)o4nv(isset($ov['pax']['adult']) ? $ov['pax']['adult'] : 0, 0) +
                (int)o4nv(isset($ov['pax']['children_with_bed']) ? $ov['pax']['children_with_bed'] : 0, 0) +
                (int)o4nv(isset($ov['pax']['children_without_bed']) ? $ov['pax']['children_without_bed'] : 0, 0) +
                (int)o4nv(isset($ov['pax']['infant']) ? $ov['pax']['infant'] : 0, 0);
            }
          ?>
            <div class="flight-card">
              <div class="flight-card-inner">
                <div class="flight-left">
                  <div class="flight-boarding">🚆 Boarding</div>
                  <div>
                    <div class="flight-num">TRAIN</div>
                    <div class="flight-class"><?= o4e($train_class) ?></div>
                  </div>
                </div>

                <div class="flight-right">
                  <div class="flight-airline-row">
                    <div class="flight-airline">Train Journey</div>
                    <div class="flight-num-badge"><?= o4e($train_class) ?></div>
                  </div>

                  <div class="flight-route">
                    <div>
                      <div class="flight-code"><?= o4e(o4_air_code($from_loc)) ?></div>
                      <div class="flight-city"><?= o4e(o4nv($from_loc, 'NA')) ?></div>
                    </div>

                    <div class="flight-middle">
                      <div class="flight-arrow-line">
                        <div class="fal"></div>
                        <div class="fa-arrow">→</div>
                        <div class="fal"></div>
                      </div>
                      <div class="flight-nonstop">Rail Journey</div>
                    </div>

                    <div style="text-align:right;">
                      <div class="flight-code"><?= o4e(o4_air_code($to_loc)) ?></div>
                      <div class="flight-city"><?= o4e(o4nv($to_loc, 'NA')) ?></div>
                    </div>
                  </div>

                  <div class="flight-footer">
                    <div class="ff-item">
                      <span class="ff-icon">📅</span>
                      <div>
                        <div class="ff-lbl">Date & Time</div>
                        <div class="ff-val"><?= o4e(o4nv($from_date, 'NA')) ?></div>
                      </div>
                    </div>

                    <div class="ff-div"></div>

                    <div class="ff-item">
                      <span class="ff-icon">👥</span>
                      <div>
                        <div class="ff-lbl">Total Pax</div>
                        <div class="ff-val"><?= o4e($total_pax) ?></div>
                      </div>
                    </div>

                    <div class="ff-div"></div>

                    <div class="ff-item">
                      <span class="ff-icon">🎫</span>
                      <div>
                        <div class="ff-lbl">Class</div>
                        <div class="ff-val"><?= o4e($train_class) ?></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <!-- ====== Activity Details -->
        <?php if (!empty($acts)) : ?>
          <div class="sec-eyebrow" style="margin-top:24px;">Experiences Included</div>
          <div class="sec-heading">Activity Details</div>

          <?php foreach ($acts as $a) :
            $activity_img = BASE_URL . 'images/activity.jpg';

            $activity_name = isset($a['activity_name']) ? $a['activity_name'] : '';
            $city_name = isset($a['city_name']) ? $a['city_name'] : '';
            $activity_date = isset($a['date']) ? $a['date'] : '';
            $transfer_type = isset($a['transfer_type']) ? $a['transfer_type'] : '';

            $total_pax = 0;
            if (isset($a['pax']) && is_array($a['pax'])) {
              $total_pax =
                (int)o4nv(isset($a['pax']['adult']) ? $a['pax']['adult'] : 0, 0) +
                (int)o4nv(isset($a['pax']['chwb']) ? $a['pax']['chwb'] : 0, 0) +
                (int)o4nv(isset($a['pax']['chwob']) ? $a['pax']['chwob'] : 0, 0) +
                (int)o4nv(isset($a['pax']['infant']) ? $a['pax']['infant'] : 0, 0);
            }
          ?>
            <div class="transfer-card" style="margin-bottom:14px;">
              <div class="transfer-inner">
                <div class="transfer-img">
                  <img src="<?= o4e($activity_img) ?>" alt="<?= o4e(o4nv($activity_name, 'Activity')) ?>" />
                </div>

                <div class="transfer-body">
                  <div class="transfer-head">
                    <div class="transfer-name">📸 <?= o4e(o4nv($activity_name, 'Activity')) ?></div>
                    <div class="transfer-type-badge"><?= o4e(o4nv($transfer_type, 'Activity')) ?></div>
                  </div>

                  <div class="transfer-grid">
                    <div class="transfer-item">
                      <div class="tlbl">📍 City</div>
                      <div class="tval"><?= o4e(o4nv($city_name, 'NA')) ?></div>
                    </div>

                    <div class="transfer-item">
                      <div class="tlbl">📅 Date</div>
                      <div class="tval"><?= o4e(o4nv($activity_date, 'NA')) ?></div>
                    </div>

                    <div class="transfer-item">
                      <div class="tlbl">🚐 Transfer Type</div>
                      <div class="tval"><?= o4e(o4nv($transfer_type, 'NA')) ?></div>
                    </div>

                    <div class="transfer-item">
                      <div class="tlbl">👥 Total Pax</div>
                      <div class="tval"><?= o4e($total_pax) ?> Pax</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
        <!-- ============= -->

        <?php if ($o4_show_vehs) : ?>
          <div class="sec-eyebrow" style="margin-top:24px;">Ground Transportation</div>
          <div class="sec-heading">Private Transfers</div>

          <?php foreach ($vehs as $v) :
            $vimg = BASE_URL . 'images/vehicle.png';
            $vtype = o4nv($v['vehicle_type'], 'Private Transfer');
            if (!empty($v['vehicle_count'])) {
              $vtype .= ' (' . $v['vehicle_count'] . ' Unit' . ((int) $v['vehicle_count'] > 1 ? 's' : '') . ')';
            }
          ?>
            <div class="transfer-card" style="margin-bottom:14px;">
              <div class="transfer-inner">
                <div class="transfer-img">
                  <img src="<?= o4e($vimg) ?>" alt="<?= o4e(o4nv($v['vehicle_name'], 'Vehicle')) ?>" />
                </div>
                <div class="transfer-body">
                  <div class="transfer-head">
                    <div class="transfer-name">🚐 <?= o4e(o4nv($v['vehicle_name'], 'Vehicle')) ?></div>
                    <div class="transfer-type-badge"><?= o4e($vtype) ?></div>
                  </div>
                  <div class="transfer-grid">
                    <div class="transfer-item">
                      <div class="tlbl">📍 Pickup</div>
                      <div class="tval"><?= o4e(o4nv($v['pickup'], 'NA')) ?></div>
                    </div>
                    <div class="transfer-item">
                      <div class="tlbl">📍 Drop</div>
                      <div class="tval"><?= o4e(o4nv($v['drop'], 'NA')) ?></div>
                    </div>
                    <div class="transfer-item">
                      <div class="tlbl">📅 Start Date</div>
                      <div class="tval"><?= o4e(o4nv($v['date'], '')) ?></div>
                    </div>
                    <div class="transfer-item">
                      <div class="tlbl">📅 End Date</div>
                      <div class="tval"><?= o4e(o4_vehicle_end_date($v)) ?></div>
                    </div>
                  </div>
                  <div class="transfer-duration"><span class="td-icon">⏱</span> Service Duration: <strong><?= o4e(o4nv($v['service_duration'], o4nv($v['description'], 'As per itinerary'))) ?></strong></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <div class="page-num">PAGE 04 / 09</div>
    <?php endif; ?>

    <!-- PAGE 5 – ITINERARY -->
    <?php o4_render_page_header($hero, 'Day-Wise Itinerary'); ?>

    <div class="page-section">
      <div class="sec-eyebrow">Your Day-by-Day Journey</div>
      <div class="sec-heading">Itinerary</div>

      <?php if (!empty($itin)) :
        foreach ($itin as $day) :
          $day_num = str_pad((string) o4nv($day['day_number'], ''), 2, '0', STR_PAD_LEFT);
      ?>
          <div class="itin-day">
            <div class="itin-day-header">
              <div class="itin-day-num"><span class="day-label">Day</span><span class="day-n"><?= o4e($day_num) ?></span></div>
              <div class="itin-day-date"><?= o4e(o4nv($day['date'], '')) ?></div>
            </div>
            <div class="itin-day-body">
              <div class="itin-attraction">🌐 Special Attraction · <?= o4e(o4nv($day['special_attraction'], o4nv($day['city'], 'Sightseeing'))) ?></div>
              <div class="itin-prog-lbl">Detailed Programme</div>
              <div class="itin-prog"><?= o4e(o4nv($day['detailed_programme'], '')) ?></div>
              <div class="itin-chips">
                <?php if (!empty($day['meal_plan'])) : ?>
                  <div class="itin-chip meal">
                    <div class="itin-chip-icon">🍽️</div>
                    <div>
                      <div class="itin-chip-lbl">Meal Plan</div>
                      <div class="itin-chip-val"><?= o4e($day['meal_plan']) ?></div>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if (!empty($day['overnight_stay'])) : ?>
                  <div class="itin-chip stay">
                    <div class="itin-chip-icon">🌙</div>
                    <div>
                      <div class="itin-chip-lbl">Overnight Stay</div>
                      <div class="itin-chip-val"><?= o4e($day['overnight_stay']) ?></div>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php
        endforeach;
      else :
        ?>
        <div class="itin-day">
          <div class="itin-day-body">
            <div class="itin-prog">Itinerary details will be shared upon confirmation.</div>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <div class="page-num">PAGE 05 / 09</div>

    <!-- PAGE 6 – PRICING -->
    <?php o4_render_page_header($hero, 'Pricing & Coverage'); ?>

    <div class="page-section">
      <div class="inc-exc-grid">
        <div class="inc-card">
          <div class="ie-eyebrow">Included</div>
          <div class="ie-heading">What's Included</div>
          <ul class="ie-list">
            <?php foreach ($o4_included as $item) : ?>
              <li><span class="ie-icon-chk">✓</span> <?= o4e($item) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="exc-card">
          <div class="ie-eyebrow" style="color:#e53e3e;">Excluded</div>
          <div class="ie-heading">What's Excluded</div>
          <ul class="ie-list">
            <?php foreach ($o4_excluded as $item) : ?>
              <li><span class="ie-icon-x">✗</span> <?= o4e($item) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>

      <div class="sec-eyebrow">Transparent Pricing</div>

      <?php
      $o4_costing_type = isset($cost['costing_type_label']) ? strtolower(trim($cost['costing_type_label'])) : '';
      $o4_is_per_person = ($o4_costing_type == 'per person');
      $o4_pp = isset($cost['computed']['per_person']) ? $cost['computed']['per_person'] : array();
      ?>

      <div class="sec-heading" style="margin-bottom:4px;">Costing Details</div>
      <div class="pricing-note" style="margin-bottom:18px;">
        <?= o4e(o4nv(isset($incx['note']) ? $incx['note'] : '', 'All figures are per package. Taxes & TCS shown separately.')) ?>
      </div>

      <?php if (!$o4_is_per_person) { ?>

        <div class="pricing-grid">
          <?php foreach ($o4_cost_grp as $ci => $row) :
            $is_featured = ($ci === $o4_featured_idx);

            $tax_amount = '0.00';
            if (!empty($row['tax_display'])) {
              preg_match('/INR\s*([\d,\.]+)/i', $row['tax_display'], $m);
              if (!empty($m[1])) {
                $tax_amount = $m[1];
              }
            }
          ?>

            <div class="pricing-card<?= $is_featured ? ' featured' : '' ?>">
              <?php if ($is_featured) : ?>
                <div class="rec-badge">⭐ Recommended</div>
              <?php endif; ?>

              <div class="pricing-name"><?= o4e(o4nv($row['package_type'], 'Package')) ?></div>

              <div class="pricing-row">
                <span>Tour Cost</span>
                <span><?= o4e(o4nv($row['tour_cost_display'], '0')) ?></span>
              </div>

              <div class="pricing-row">
                <span>Tax</span>
                <span>INR <?= o4e($tax_amount) ?></span>
              </div>

              <div class="pricing-row">
                <span>TCS</span>
                <span><?= o4e(o4nv($row['tcs_display'], '0')) ?></span>
              </div>

              <div class="pricing-row">
                <span>Travel Cost</span>
                <span><?= o4e(o4nv($row['travel_display'], '0')) ?></span>
              </div>

              <div class="pricing-total-lbl">Grand Total</div>
              <div class="pricing-total-val"><?= o4e(o4nv($row['total_display'], '0')) ?></div>
            </div>

          <?php endforeach; ?>
        </div>

      <?php } else { ?>

        <?php if (!empty($o4_pp)) { ?>

          <div class="pricing-grid">
            <?php foreach ($o4_pp as $ci => $pp) :
              $is_featured = ($ci === 0);

              $tax_amount = '0.00';
              if (!empty($pp['tax_display'])) {
                preg_match('/INR\s*([\d,\.]+)/i', $pp['tax_display'], $m);
                if (!empty($m[1])) {
                  $tax_amount = $m[1];
                }
              }
            ?>

              <div class="pricing-card<?= $is_featured ? ' featured' : '' ?>">
                <?php if ($is_featured) : ?>
                  <div class="rec-badge">⭐ Recommended</div>
                <?php endif; ?>

                <div class="pricing-name"><?= o4e(o4nv($pp['package_type'], 'Package')) ?></div>

                <div class="pricing-row">
                  <span>Adult</span>
                  <span><?= o4e(o4nv($pp['pp_adult_display'], 'INR 0.00')) ?></span>
                </div>

                <div class="pricing-row">
                  <span>CWB</span>
                  <span><?= o4e(o4nv($pp['pp_cwb_display'], 'INR 0.00')) ?></span>
                </div>

                <div class="pricing-row">
                  <span>CWOB</span>
                  <span><?= o4e(o4nv($pp['pp_cwnb_display'], 'INR 0.00')) ?></span>
                </div>

                <div class="pricing-row">
                  <span>Infant</span>
                  <span><?= o4e(o4nv($pp['pp_infant_display'], 'INR 0.00')) ?></span>
                </div>

                <div class="pricing-row">
                  <span>Tax</span>
                  <span>INR <?= o4e($tax_amount) ?></span>
                </div>

                <div class="pricing-row">
                  <span>TCS</span>
                  <span><?= o4e(o4nv($pp['tcs_display'], 'INR 0.00')) ?></span>
                </div>

                <div class="pricing-row">
                  <span>Visa</span>
                  <span><?= o4e(o4nv($pp['visa_display'], 'INR 0.00')) ?></span>
                </div>

                <div class="pricing-row">
                  <span>Guide</span>
                  <span><?= o4e(o4nv($pp['guide_display'], 'INR 0.00')) ?></span>
                </div>

                <div class="pricing-row">
                  <span>Misc</span>
                  <span><?= o4e(o4nv($pp['misc_display'], 'INR 0.00')) ?></span>
                </div>
              </div>

            <?php endforeach; ?>
          </div>

        <?php } ?>

      <?php } ?></br>
      <div class="page-num">PAGE 06 / 09</div>

      <!-- PAGE 7 – PAYMENT -->
      <?php o4_render_page_header($hero, 'Secure Payments'); ?>

      <div class="page-section">
        <div class="sec-eyebrow">How to Pay</div>
        <div class="sec-heading">Payment Information</div>

        <div class="pay-grid">
          <div class="bank-card">
            <div class="bank-header">Bank Transfer Details</div>
            <div class="bank-body">
              <div class="bank-item">
                <div class="bi-lbl">🏛️ Account Name</div>
                <div class="bi-val"><?= o4e(o4nv($bank['account_name'], 'NA')) ?></div>
              </div>
              <div class="bank-item">
                <div class="bi-lbl"># Account Number</div>
                <div class="bi-val"><?= o4e(o4nv($bank['account_no'], 'NA')) ?></div>
              </div>
              <div class="bank-item">
                <div class="bi-lbl">🏦 Bank Name</div>
                <div class="bi-val"><?= o4e(o4nv($bank['bank_name'], 'NA')) ?></div>
              </div>
              <div class="bank-item">
                <div class="bi-lbl">📍 Branch</div>
                <div class="bi-val"><?= o4e(o4nv($bank['branch_name'], 'NA')) ?></div>
              </div>
              <div class="bank-item">
                <div class="bi-lbl">🔑 IFSC Code</div>
                <div class="bi-val"><?= o4e(o4nv($bank['ifsc_code'], o4nv($bank['swift_code'], 'NA'))) ?></div>
              </div>
              <?php if (!empty($bank['upi_id'])) : ?>
                <div class="bank-item">
                  <div class="bi-lbl">📱 UPI ID</div>
                  <div class="bi-val"><?= o4e($bank['upi_id']) ?></div>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <div class="qr-card">
            <div class="qr-title">Scan to Pay</div>
            <div class="qr-box">
              <?php if (!empty($bank['qr_html'])) : ?>
                <?= $bank['qr_html'] ?>
              <?php elseif (!empty($bank['qr_code']) || !empty($bank['branch_qr_url'])) : ?>
                <img src="<?= o4e(o4img(o4nv($bank['branch_qr_url'], $bank['qr_code']), $assets . 'qr-placeholder.png')) ?>" alt="Payment QR" style="width:90px;height:90px;object-fit:contain;" />
              <?php else : ?>
                <span style="color:#fff;font-size:11px;">QR not configured</span>
              <?php endif; ?>
            </div>
            <?php if (!empty($bank['upi_id'])) : ?>
              <div class="qr-upi"><?= o4e($bank['upi_id']) ?></div>
              <div class="qr-accepts">UPI · Google Pay · PhonePe · Paytm</div>
            <?php endif; ?>
          </div>
        </div>

        <div class="pay-cards-grid">
          <div class="pay-info-card">
            <div class="pay-info-title"><span class="pay-info-icon">💡</span> Payment Instructions</div>
            <ul class="pay-info-list">
              <?php foreach ($o4_pay_notes as $note) : ?>
                <li><?= o4e($note) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="pay-info-card">
            <div class="pay-info-title"><span class="pay-info-icon">✅</span> Booking Policy</div>
            <ul class="pay-info-list">
              <?php foreach (array_slice($o4_book_policy, 0, 4) as $pol) : ?>
                <li><?= o4e($pol) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
      <div class="page-num">PAGE 07 / 09</div>

      <!-- PAGE 8 – REVIEWS -->
      <?php o4_render_page_header($hero, 'Reviews'); ?>

      <div class="page-section">
        <div class="sec-eyebrow">Loved by Travellers</div>
        <div class="sec-heading">What Our Travellers Say</div>

        <?php if (!empty($testimonials)) :
          foreach ($testimonials as $t) :
            $photo = isset($t['photo']) ? trim($t['photo']) : '';
            if ($photo !== '' && strpos($photo, 'http') !== 0) {
              $photo = BASE_URL . ltrim(str_replace('\\', '/', $photo), '/');
            }
        ?>
            <div class="review-card">
              <div class="review-stars">★★★★★</div>
              <div class="review-quote">"</div>
              <div class="review-text"><?= o4e(o4nv($t['review'], '')) ?></div>
              <div class="review-footer">
                <?php if ($photo !== '') : ?>
                  <img class="review-avatar" src="<?= o4e($photo) ?>" alt="<?= o4e(o4nv($t['name'], '')) ?>" />
                <?php else : ?>
                  <div class="review-avatar" style="background:var(--navy);color:var(--gold-lt);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:16px;font-weight:700;"><?= o4e(strtoupper(substr(o4nv($t['name'], 'T'), 0, 1))) ?></div>
                <?php endif; ?>
                <div>
                  <div class="review-name"><?= o4e(o4nv($t['name'], 'Traveller')) ?></div>
                  <div class="review-meta"><?= o4e(o4nv($t['designation'], '')) ?></div>
                </div>
              </div>
            </div>
          <?php
          endforeach;
        else :
          ?>
          <div class="review-card">
            <div class="review-text">Customer testimonials can be managed from Quotation Builder settings.</div>
          </div>
        <?php endif; ?>

        <div class="rating-bar">
          <span class="star">★</span>
          <span class="num">4.9</span>
          <span class="txt">rated by happy travellers across the globe</span>
        </div>
      </div>
      <div class="page-num">PAGE 08 / 09</div>

      <!-- PAGE 9 – T&C -->
      <?php o4_render_page_header($hero, 'Legal'); ?>

      <div class="page-section">
        <div class="sec-eyebrow">Please Read Carefully</div>
        <div class="sec-heading"><?= o4e(o4nv($terms['title'], 'Terms & Conditions')) ?></div>

        <div class="tnc-grid">
          <?php if (!empty($o4_term_lines)) :
            foreach ($o4_term_lines as $ti => $line) :
              $num = str_pad((string) ($ti + 1), 2, '0', STR_PAD_LEFT);
              $title = 'Terms';
              $body = $line;
              if (strpos($line, ':') !== false) {
                list($title, $body) = array_map('trim', explode(':', $line, 2));
              }
          ?>
              <div class="tnc-card">
                <div class="tnc-num"><?= o4e($num) ?></div>
                <div class="tnc-title"><?= o4e($title) ?></div>
                <div class="tnc-body"><?= o4e($body) ?></div>
              </div>
            <?php
            endforeach;
          else :
            ?>
            <div class="tnc-card">
              <div class="tnc-num">01</div>
              <div class="tnc-title">Terms &amp; Conditions</div>
              <div class="tnc-body">Terms and conditions will be shared as per company policy.</div>
            </div>
          <?php endif; ?>
        </div>

        <div class="tnc-footer">
          <span class="tnc-footer-icon">📄</span>
          By confirming this booking, the traveller acknowledges and agrees to all terms and conditions stated above.
        </div>
      </div>
      <div class="page-num">PAGE 09 / 09</div>

      <!-- THANK YOU PAGE -->
      <div class="ty-page" style="background: linear-gradient(to bottom,rgba(10,22,50,.7) 0%,rgba(10,22,50,.78) 60%,rgba(10,22,50,.95) 100%), url('<?= o4e($o4_cover_img) ?>') center/cover no-repeat;">
        <div class="ty-top">
          <?php o4_render_vl_logo($hero, true); ?>
        </div>

        <div class="ty-main">
          <div class="ty-until">
            <div class="ty-line"></div>
            <div class="ty-until-text">Until We Meet Again</div>
            <div class="ty-line"></div>
          </div>
          <div class="ty-heading">Thank You</div>
          <div class="ty-sub">We look forward to creating unforgettable travel memories for you. Dear <?= o4e($o4_client) ?>, we truly appreciate your trust in <?= o4e(o4nv($ty['company_name'], $o4_company)) ?>.</div>
        </div>

        <div class="ty-bottom">
          <div class="ty-contact-card">
            <div class="ty-contact-grid">
              <div>
                <div class="ty-contact-item" style="margin-bottom:10px;">
                  <div class="tc-lbl">📍 Office Address</div>
                  <div class="tc-val"><?= o4e(o4nv($ty['company_address'], '')) ?></div>
                </div>
              </div>
              <div class="ty-contact-right">
                <div class="ty-contact-item">
                  <div class="tc-lbl">📞 Contact</div>
                  <div class="tc-val">
                    <a href="tel:<?= preg_replace('/\s+/', '', o4nv($ty['company_contact'], o4nv($ty['user_mobile'], ''))) ?>">
                      <?= o4e(o4nv($ty['company_contact'], o4nv($ty['user_mobile'], ''))) ?>
                    </a>
                  </div>
                </div>

                <div class="ty-contact-item">
                  <div class="tc-lbl">🌐 Website</div>
                  <div class="tc-val">
                    <a href="<?= o4e(o4nv($ty['website'], '')) ?>" target="_blank">
                      <?= o4e(o4nv($ty['website'], '')) ?>
                    </a>
                  </div>
                </div>

                <div class="ty-contact-item">
                  <div class="tc-lbl">✉️ Email</div>
                  <div class="tc-val">
                    <a href="mailto:<?= o4e(o4nv($ty['company_email'], '')) ?>">
                      <?= o4e(o4nv($ty['company_email'], '')) ?>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <div class="ty-divider"></div>
            <div class="ty-footer-row">
              <div class="ty-prepared">
                <div class="lbl">Prepared By</div>
                <div class="name"><?= o4e(o4nv($ty['prepared_by'], o4nv($hero['login_user'], 'Team'))) ?></div>
                <div class="role">Travel Consultant</div>
              </div>
              <div class="ty-brand-footer"><?= o4e(o4nv($ty['company_name'], $o4_company)) ?> — Curated Luxury Journeys</div>
            </div>
          </div>
        </div>
        <div style="height:28px;"></div>
      </div>

    </div>

    <script type="text/javascript">
      (function() {
        var printed = false;

        function doPrint() {
          if (printed) return;
          printed = true;
          try {
            window.focus();
          } catch (e) {}
          window.print();
        }

        function waitForImages() {
          var imgs = Array.prototype.slice.call(document.images || []);
          var pending = imgs.filter(function(img) {
            return !img.complete;
          });
          if (pending.length === 0) return Promise.resolve();
          return Promise.all(pending.map(function(img) {
            return new Promise(function(resolve) {
              img.addEventListener('load', resolve, {
                once: true
              });
              img.addEventListener('error', resolve, {
                once: true
              });
            });
          }));
        }

        function waitForFonts() {
          if (document.fonts && document.fonts.ready) {
            return document.fonts.ready.catch(function() {});
          }
          return Promise.resolve();
        }

        function ready() {
          var safety = new Promise(function(resolve) {
            setTimeout(resolve, 4000);
          });
          Promise.race([
            Promise.all([waitForImages(), waitForFonts()]),
            safety
          ]).then(function() {
            setTimeout(doPrint, 150);
          });
        }
        if (document.readyState === 'complete') {
          ready();
        } else {
          window.addEventListener('load', ready);
        }
      })();
    </script>
</body>

</html>