<?php

/**
 * OPTION-3 (quotation_html_3) — Package Tour Quotation
 * Layout/CSS from Final-Designs/Option-3-Done/option-3.html
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
$assets       = "assets/";
$testimonials = array();
// if (function_exists('gqb_get_config')) {
//   $o3_cfg = gqb_get_config();
//   $testimonials = isset($o3_cfg['testimonials']) && is_array($o3_cfg['testimonials'])
//     ? $o3_cfg['testimonials'] : array();
// }
$testimonials = isset($q['testimonials']) && is_array($q['testimonials'])
  ? $q['testimonials'] : array();

if (!function_exists('o3e')) {
  function o3e($v)
  {
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
  }
}
if (!function_exists('o3nv')) {
  function o3nv($v, $f = '')
  {
    return ($v !== null && $v !== '') ? $v : $f;
  }
}
if (!function_exists('o3img')) {
  function o3img($url, $fallback)
  {
    return (is_string($url) && trim($url) !== '' && stripos($url, 'dummy') === false) ? $url : $fallback;
  }
}
if (!function_exists('o3_guest_label')) {
  function o3_guest_label($ov)
  {
    $p = isset($ov['pax']) ? $ov['pax'] : array();
    $parts = array();
    $ad = (int) o3nv(isset($p['adult']) ? $p['adult'] : 0, 0);
    $ch = (int) o3nv(isset($p['children_with_bed']) ? $p['children_with_bed'] : 0, 0)
      + (int) o3nv(isset($p['children_without_bed']) ? $p['children_without_bed'] : 0, 0);
    $inf = (int) o3nv(isset($p['infant']) ? $p['infant'] : 0, 0);
    if ($ad) {
      $parts[] = $ad . ' Adult' . ($ad > 1 ? 's' : '');
    }
    if ($ch) {
      $parts[] = $ch . ' Child' . ($ch > 1 ? 'ren' : '');
    }
    if ($inf) {
      $parts[] = $inf . ' Infant' . ($inf > 1 ? 's' : '');
    }
    return $parts ? implode(', ', $parts) : o3nv($ov['guest_count'], '-');
  }
}
if (!function_exists('o3_split_lines')) {
  function o3_split_lines($html, $fallback = array())
  {
    $text = trim(strip_tags(str_replace(array('<br>', '<br/>', '<br />', '</p>', '</li>'), "\n", (string) $html)));
    $items = preg_split('/\r\n|\r|\n|•|\x{2022}/u', $text);
    $items = array_values(array_filter(array_map('trim', (array) $items)));
    return $items ? $items : $fallback;
  }
}
if (!function_exists('o3_air_code')) {
  function o3_air_code($loc)
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
if (!function_exists('o3_stars')) {
  function o3_stars($rating)
  {
    $n = (int) preg_replace('/\D/', '', (string) $rating);
    if ($n < 1) {
      $n = 5;
    }
    return str_repeat('★', min($n, 5));
  }
}
if (!function_exists('o3_brand_lines')) {
  function o3_brand_lines($name)
  {
    $name = trim((string) $name);
    if ($name === '') {
      return array('TRAVEL', 'PARTNER');
    }
    $parts = preg_split('/\s+/', $name, 2);
    return array(strtoupper($parts[0]), isset($parts[1]) ? strtoupper($parts[1]) : 'TOURS');
  }
}
if (!function_exists('o3_cover_strip')) {
  function o3_cover_strip($q, $hotels, $itin, $assets)
  {
    $imgs = array();
    if (!empty($q['gallery_images']) && is_array($q['gallery_images'])) {
      $imgs = $q['gallery_images'];
    }
    foreach ($hotels as $h) {
      if (count($imgs) >= 4) {
        break;
      }
      $p = o3img(isset($h['hotel_photo']) ? $h['hotel_photo'] : '', '');
      if ($p !== '') {
        $imgs[] = $p;
      }
    }
    foreach ($itin as $d) {
      if (count($imgs) >= 4) {
        break;
      }
      $p = o3img(isset($d['image']) ? $d['image'] : '', '');
      if ($p !== '') {
        $imgs[] = $p;
      }
    }
    $fallback = array(
      $assets . 'strip-1.jpg',
      $assets . 'strip-2.jpg',
      $assets . 'strip-3.jpg',
      $assets . 'strip-4.jpg',
      // $assets . 'strip-5.jpg',
    );
    while (count($imgs) < 4) {
      $imgs[] = $fallback[count($imgs) % 4];
    }
    return array_slice($imgs, 0, 4);
  }
}
if (!function_exists('o3_vehicle_end_date')) {
  function o3_vehicle_end_date($v)
  {
    if (!empty($v['end_date_raw']) && function_exists('get_date_user')) {
      return get_date_user($v['end_date_raw']);
    }
    return o3nv(isset($v['end_date_raw']) ? $v['end_date_raw'] : '', '');
  }
}
if (!function_exists('o3_render_page_header')) {
  function o3_render_page_header($hero, $ov, $assets)
  {
    $dest = strtoupper(o3nv($ov['destination'], o3nv($hero['tour_name'], 'TOUR')));
    $thumb = o3img(o3nv($hero['cover_image'], ''), $assets . 'cover-thumb.jpg');
    list($b1, $b2) = o3_brand_lines(o3nv($hero['company_name'], 'Travel Partner'));
    $logo = o3nv($hero['company_logo'], '');
?>
    <div class="page-header">
      <div class="ph-logo">
        <?php if ($logo !== '') : ?>
          <img src="<?= o3e($logo) ?>" alt="<?= o3e($hero['company_name']) ?>" class="company-logo-img" />
        <?php else : ?>
          <svg class="pl-icon" viewBox="0 0 48 48">
            <path d="M8 28 L42 14 L36 30 L22 28 L14 40 L12 30 Z" fill="#c8973a" />
          </svg>
        <?php endif; ?>
        <div class="pl-text">
          <div class="freeze"><?= o3e($b1) ?></div>
          <div class="mtrip"><?= o3e($b2) ?></div>
          <div class="pl-tag">—Journey Beyond Dreams—</div>
        </div>
      </div>
      <div class="ph-right">
        <div class="ph-right-text">
          <div class="sing"><?= o3e($dest) ?></div>
          <div class="tour-pkg">TOUR PACKAGE</div>
        </div>
        <img src="<?= o3e($thumb) ?>" alt="<?= o3e($dest) ?>" />
      </div>
    </div>
<?php
  }
}

$o3_dest      = o3nv($ov['destination'], o3nv($hero['tour_name'], 'Tour'));
$o3_dest_up   = strtoupper($o3_dest);
$o3_client    = o3nv($ov['client_name'], o3nv($hero['client_name'], 'Guest'));
$o3_cover_img = o3img(o3nv($hero['cover_image'], ''), $assets . 'cover.jpg');
list($o3_b1, $o3_b2) = o3_brand_lines(o3nv($hero['company_name'], 'Travel Partner'));
$o3_logo      = o3nv($hero['company_logo'], '');
$o3_strip     = o3_cover_strip($q, $hotels, $itin, $assets);
$o3_tour_id   = o3nv($hero['package_code'], o3nv($ov['tour_id'], ''));
$o3_pkg_badge = '';
if (!empty($cost['computed']['group'][0]['package_type'])) {
  $o3_pkg_badge = $cost['computed']['group'][0]['package_type'];
} elseif (!empty($hotels[0]['package_type'])) {
  $o3_pkg_badge = $hotels[0]['package_type'];
}
$o3_pkg_ov = o3nv($o3_pkg_badge, o3nv($ov['package_type_label'], 'Package'));
$o3_included = o3_split_lines(isset($incx['included']) ? $incx['included'] : '', array('Inclusions as per itinerary.'));
$o3_excluded = o3_split_lines(isset($incx['excluded']) ? $incx['excluded'] : '', array('Exclusions as per company policy.'));
$o3_cost_grp = isset($cost['computed']['group']) ? $cost['computed']['group'] : array();
if (empty($o3_cost_grp)) {
  $o3_cost_grp = array(array(
    'package_type' => 'Package',
    'tour_cost_display' => '0',
    'tax_display' => '0',
    'tcs_display' => '0',
    'travel_display' => '0',
    'total_display' => '0',
  ));
}
$o3_pay_notes = o3_split_lines(
  o3nv(isset($incx['quot_note']) ? $incx['quot_note'] : '', isset($incx['note']) ? $incx['note'] : ''),
  array(
    '50% advance at the time of booking.',
    'Balance before departure as per company policy.',
    'All payments to be made in INR only.',
  )
);
$o3_book_policy = o3_split_lines(
  isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '',
  array(
    'Booking is confirmed only after receipt of advance payment.',
    'Package cost is valid from the quotation date.',
  )
);
$o3_term_lines = o3_split_lines(isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '', array());
$o3_term_icons = array('🗓️', '🚫', '🔄', '🪪', '🛎️', '✈️', '🌪️', '🛡️');
$o3_term_classes = array('ti-gold', 'ti-red', 'ti-blue', 'ti-navy', 'ti-teal', 'ti-sky', 'ti-amber', 'ti-green');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= o3e($o3_dest) ?> Tour Package – <?= o3e(o3nv($hero['company_name'], '')) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Lato:wght@300;400;700;900&family=Cinzel:wght@600;700&display=swap" rel="stylesheet" />
  <link href="option3.css" rel="stylesheet" />
</head>

<body>
  <div class="page">

    <!-- COVER -->
    <div class="cover" style="background: linear-gradient(to bottom, rgba(8,18,40,.5) 0%, rgba(8,18,40,.68) 50%, rgba(8,18,40,.96) 100%), url('<?= o3e($o3_cover_img) ?>') center/cover no-repeat;">
      <div class="cover-logo">
        <div class="brand">
          <?php if ($o3_logo !== '') : ?>
            <!-- <img src="<? //= o3e($o3_logo) 
                            ?>" alt="<? //= o3e($hero['company_name']) 
                                      ?>" class="company-logo-img" /> -->
            <img src="<?= BASE_URL ?>images/Admin-Area-Logo.png"
              alt="Logo"
              class="company-logo-img" />
          <?php else : ?>
            <svg class="plane-icon" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
              <path d="M8 28 L42 14 L36 30 L22 28 L14 40 L12 30 Z" fill="#c8973a" />
            </svg>
          <?php endif; ?>
          <!-- <div class="brand-text">
            <div class="freeze"><? //= o3e($o3_b1) 
                                ?></div>
            <div class="my-trip"><? //= o3e($o3_b2) 
                                  ?></div>
          </div> -->
        </div>
        <div class="tagline">Journey Beyond Dreams</div>
      </div>
      <div class="cover-hero-frame">
        <div class="cover-hero-inner">
          <img src="<?= o3e($o3_cover_img) ?>" alt="<?= o3e($o3_dest) ?>" />
        </div>
      </div>
      <div class="cover-title-block">
        <div class="main-title"><?= o3e($o3_dest_up) ?></div>
        <div class="sub-title">TOUR PACKAGE</div>
        <div class="discover">Discover Extraordinary Experiences</div>
      </div>
      <div class="cover-icons">
        <div class="cover-icon-item">
          <div class="cover-icon-circle">🏨</div>
          <div class="cover-icon-label">Hotels</div>
        </div>
        <div class="cover-icon-item">
          <div class="cover-icon-circle">✈️</div>
          <div class="cover-icon-label">Flights</div>
        </div>
        <div class="cover-icon-item">
          <div class="cover-icon-circle">🎯</div>
          <div class="cover-icon-label">Activities</div>
        </div>
        <div class="cover-icon-item">
          <div class="cover-icon-circle">🚗</div>
          <div class="cover-icon-label">Transfers</div>
        </div>
        <div class="cover-icon-item">
          <div class="cover-icon-circle">🏛️</div>
          <div class="cover-icon-label">Sightseeing</div>
        </div>
        <div class="cover-icon-item">
          <div class="cover-icon-circle">🍽️</div>
          <div class="cover-icon-label">Meals</div>
        </div>
      </div>
      <div class="cover-strip">
        <?php foreach ($o3_strip as $si => $strip_img) : ?>
          <div class="cover-strip-item">
            <img src="<?= o3e($strip_img) ?>" alt="Gallery Image <?= $si + 1 ?>">
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <hr class="page-divider" />

    <!-- TOUR OVERVIEW -->
    <?php o3_render_page_header($hero, $ov, $assets); ?>

    <div class="personalized-title">
      <div class="pt-subtitle">A Personalized Travel Experience<br />Exclusively Designed for</div>
      <div class="pt-name"><?= o3e($o3_client) ?></div>
      <div class="pt-name-underline"></div>
      <div class="pt-divider">
        <div class="pt-divider-line"></div>
        <span class="pt-divider-diamond">✦</span>
        <div class="pt-divider-line"></div>
      </div>
      <div class="pt-body">
        <span class="dear">Dear <?= o3e($o3_client) ?>,</span><br />
        Thank you for choosing <?= o3e(o3nv($hero['company_name'], 'us')) ?> for your upcoming journey.<br />
        We are delighted to present this carefully crafted travel<br />
        proposal designed to provide memorable experiences,<br />
        seamless arrangements, and exceptional hospitality<br />
        throughout your trip.
      </div>
    </div>

    <div class="section-body page-flow-section">
      <div class="section-heading">
        <div class="line"></div>
        <span class="star">✦</span>
        <h2>TOUR OVERVIEW</h2>
        <span class="star">✦</span>
        <div class="line"></div>
      </div>
      <div class="overview-grid">
        <div class="ov-card">
          <div class="ov-icon">📋</div>
          <div>
            <div class="ov-label">Quotation ID</div>
            <div class="ov-value"><?= o3e(o3nv($hero['quotation_code'], '')) ?></div>
          </div>
        </div>
        <div class="ov-card">
          <div class="ov-icon" style="background:#2a5298;">🎯</div>
          <div>
            <div class="ov-label">Tour ID</div>
            <div class="ov-value"><?= o3e($o3_tour_id) ?></div>
          </div>
        </div>
        <div class="ov-card">
          <div class="ov-icon" style="background:#1a5276;">📅</div>
          <div>
            <div class="ov-label">Quotation Date</div>
            <div class="ov-value"><?= o3e(o3nv($ov['quotation_date'], '')) ?></div>
          </div>
        </div>
      </div>
      <div class="overview-grid">
        <div class="ov-card">
          <div class="ov-icon" style="background:#154360;">🗓️</div>
          <div>
            <div class="ov-label">Travel Date</div>
            <div class="ov-value"><?= o3e(o3nv($ov['travel_from'], '')) ?> to<br /><?= o3e(o3nv($ov['travel_to'], '')) ?></div>
          </div>
        </div>
        <div class="ov-card">
          <div class="ov-icon" style="background:#0e6655;">⏱️</div>
          <div>
            <div class="ov-label">Duration</div>
            <div class="ov-value"><?= o3e(o3nv($ov['duration_label'], o3nv($hero['duration_label'], ''))) ?></div>
          </div>
        </div>
        <div class="ov-card">
          <div class="ov-icon" style="background:#6c3483;">👥</div>
          <div>
            <div class="ov-label">Guests</div>
            <div class="ov-value"><?= o3e(o3_guest_label($ov)) ?></div>
          </div>
        </div>
      </div>
      <div style="display:flex;justify-content:center;margin-top:14px;">
        <div class="ov-card" style="width:280px;">
          <div class="ov-icon" style="background:#784212;">🏷️</div>
          <div>
            <div class="ov-label">Package Type</div>
            <div class="ov-value"><?= o3e($o3_pkg_ov) ?></div>
          </div>
        </div>
      </div>
      <div class="prepared-for" style="margin-top:36px;">
        <div class="pf-heading">PREPARED FOR ←</div>
        <div class="pf-rows">
          <div class="pf-row"><span class="pf-row-icon">👤</span> <?= o3e($o3_client) ?></div>
          <!-- <div class="pf-row"><span class="pf-row-icon">✉️</span> <?= o3e(o3nv($ov['customer_email'], o3nv($hero['user_email_id'], ''))) ?></div>
          <div class="pf-row"><span class="pf-row-icon">📞</span> <?= o3e(o3nv($ov['customer_mobile'], o3nv($hero['user_contact'], ''))) ?></div> -->
          <div class="pf-row">
            <span class="pf-row-icon">✉️</span>
            <a href="mailto:<?= o3e(o3nv($ov['customer_email'], o3nv($hero['user_email_id'], ''))) ?>">
              <?= o3e(o3nv($ov['customer_email'], o3nv($hero['user_email_id'], ''))) ?>
            </a>
          </div>

          <div class="pf-row">
            <span class="pf-row-icon">📞</span>
            <a href="tel:<?= o3e(preg_replace('/\D+/', '', o3nv($ov['customer_mobile'], o3nv($hero['user_contact'], '')))) ?>">
              <?= o3e(o3nv($ov['customer_mobile'], o3nv($hero['user_contact'], ''))) ?>
            </a>
          </div>
        </div>
      </div>
    </div>

    <hr class="page-divider" />

    <!-- ACCOMMODATION -->
    <?php o3_render_page_header($hero, $ov, $assets); ?>

    <div class="accom-section page-flow-section">
      <h2>Accommodation Details</h2>
      <div class="accom-subtitle">Your handpicked stays for an unforgettable <?= o3e($o3_dest) ?> experience</div>
      <?php if ($o3_pkg_badge !== '') : ?>
        <div class="pkg-badge">⭐ Package Type: <?= o3e($o3_pkg_badge) ?></div>
      <?php endif; ?>

      <?php
      $o3_hi = 0;
      if (!empty($hotels)) :
        foreach ($hotels as $h) :
          $o3_hi++;
          // $hphoto = o3img(isset($h['hotel_photo']) ? $h['hotel_photo'] : '', $assets . 'hotel-' . (($o3_hi - 1) % 3 + 1) . '.jpg');
          $dummy_hotel_img = BASE_URL . 'images/hotel.png';

          $o3_hotel_photo = isset($h['hotel_photo']) ? trim($h['hotel_photo']) : '';

          if ($o3_hotel_photo == '' || stripos($o3_hotel_photo, 'dummy') !== false) {
            $hphoto = $dummy_hotel_img;
          } else {
            $hphoto = o3img($o3_hotel_photo, $dummy_hotel_img);
          }
      ?>
          <div class="hotel-card">
            <div class="hotel-card-img-wrap">
              <img src="<?= o3e($hphoto) ?>" alt="<?= o3e(o3nv($h['hotel_name'], 'Hotel')) ?>" />
              <div class="hotel-star-badge"><?= o3e(o3_stars(o3nv($h['rating'], ''))) ?></div>
            </div>
            <div class="hotel-divider"></div>
            <div class="hotel-info">
              <div class="hotel-info-top">
                <div class="hotel-loc"><span class="pin">📍</span> <?= o3e(o3nv($h['hotel_city'], '')) ?></div>
                <div class="hotel-name"><?= o3e(o3nv($h['hotel_name'], 'Hotel')) ?></div>
                <div class="hotel-meta-row">
                  <div class="hotel-meta-item">
                    <div class="hotel-meta-label">Room Category</div>
                    <div class="hotel-meta-value"><?= o3e(o3nv($h['room_category'], o3nv($h['room_type'], ''))) ?></div>
                  </div>
                  <?php if (!empty($h['meal_plan'])) : ?>
                    <div class="hotel-meta-item">
                      <div class="hotel-meta-label">Meal Plan</div>
                      <div class="hotel-meta-value"><?= o3e($h['meal_plan']) ?></div>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="hotel-dates-row">
                  <div class="hotel-date-chip">📅 Check-in: <strong><?= o3e(o3nv($h['check_in'], '')) ?></strong></div>
                  <div class="hotel-date-chip">📅 Check-Out: <strong><?= o3e(o3nv($h['check_out'], '')) ?></strong></div>
                </div>
              </div>
              <?php if (!empty($h['meal_plan'])) : ?>
                <div class="hotel-amenities">
                  <div class="amenity-item">
                    <div class="amenity-circle">🍳</div>
                    <div class="amenity-label"><?= o3e($h['meal_plan']) ?></div>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php
        endforeach;
      else :
        ?>
        <div class="hotel-card">
          <div class="hotel-info" style="padding:24px;">
            <div class="hotel-name">Hotel details will be confirmed with your booking.</div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <hr class="page-divider" />

    <!-- FLIGHTS & TRANSPORT -->
    <!-- <div class="prep-exclusively">
      <div class="prep-label">PREPARED EXCLUSIVELY FOR</div>
      <div class="prep-name"><?= o3e($o3_client) ?></div>
      <div class="prep-deco">✦ ❁ ✦</div>
    </div> -->
    <?php o3_render_page_header($hero, $ov, $assets); ?>

    <div class="flight-section page-flow-section" style="padding-top:28px;">
      <?php if (!empty($flights)) : ?>
        <div class="section-sub-heading">
          <div class="badge-letter">A</div>FLIGHT DETAILS
        </div>
        <?php foreach ($flights as $f) :
          $air_name = o3nv($f['airline_name'], o3nv($f['airline_display'], 'Flight'));
          $air_html = str_replace(' ', '<br/>', o3e($air_name));
          $from_code = o3_air_code(o3nv($f['from_city'], ''));
          $to_code = o3_air_code(o3nv($f['to_city'], ''));
          $flight_lbl = o3nv($f['airline_code'], o3nv($f['airline_display'], ''));
        ?>
          <div class="flight-card" style="padding-right:40px;">
            <div>
              <?php if (!empty($f['airline_logo'])) : ?>
                <img src="<?= o3e($f['airline_logo']) ?>" alt="<?= o3e($air_name) ?>" class="airline-logo" style="max-height:40px;object-fit:contain;background:transparent;padding:0;" />
              <?php else : ?>
                <div class="airline-logo"><?= $air_html ?></div>
              <?php endif; ?>
              <div class="flight-route">
                <div class="route-airport">
                  <div class="code"><?= o3e($from_code) ?></div>
                  <div class="city"><?= o3e(o3nv($f['from_city'], '')) ?></div>
                </div>
                <div class="route-arrow">→</div>
                <div class="route-airport">
                  <div class="code"><?= o3e($to_code) ?></div>
                  <div class="city"><?= o3e(o3nv($f['to_city'], '')) ?></div>
                </div>
              </div>
            </div>
            <div></div>
            <div class="flight-num-class" style="text-align:right;">
              <div class="flight-num"><?= o3e($flight_lbl) ?></div>
              <div class="flight-class-label">Class</div>
              <div class="flight-class"><?= o3e(o3nv($f['class'], 'Economy')) ?></div>
            </div>
            <div class="flight-footer">
              <div class="ff-item">
                <div class="ff-label">Depart</div>
                <div class="ff-value"><?= o3e(o3nv($f['departure_datetime'], 'NA')) ?></div>
              </div>
              <div class="ff-item">
                <div class="ff-label">Arrive</div>
                <div class="ff-value"><?= o3e(o3nv($f['arrival_datetime'], 'NA')) ?></div>
              </div>
              <div class="ff-item">
                <div class="ff-label">Route</div>
                <div class="ff-value"><?= o3e(o3nv($f['from_city'], '')) ?> → <?= o3e(o3nv($f['to_city'], '')) ?></div>
              </div>
              <div class="ff-item">
                <div class="ff-label">Airline</div>
                <div class="ff-value"><?= o3e($air_name) ?></div>
              </div>
            </div>
            <div class="ticket-stripe"></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <!-- ============= Train Details -->
      <?php if (!empty($trains)) : ?>
        <div class="section-sub-heading" style="margin-top:28px;">
          <div class="badge-letter">B</div>TRAIN DETAILS
        </div>

        <?php foreach ($trains as $tr) :
          $from_loc = isset($tr['from_location']) ? $tr['from_location'] : '';
          $to_loc = isset($tr['to_location']) ? $tr['to_location'] : '';
          $train_class = isset($tr['class']) ? $tr['class'] : 'NA';
          $from_date = isset($tr['from_date']) ? $tr['from_date'] : '';

          $total_pax = 0;
          if (isset($ov['pax']) && is_array($ov['pax'])) {
            $total_pax =
              (int)o3nv(isset($ov['pax']['adult']) ? $ov['pax']['adult'] : 0, 0) +
              (int)o3nv(isset($ov['pax']['children_with_bed']) ? $ov['pax']['children_with_bed'] : 0, 0) +
              (int)o3nv(isset($ov['pax']['children_without_bed']) ? $ov['pax']['children_without_bed'] : 0, 0) +
              (int)o3nv(isset($ov['pax']['infant']) ? $ov['pax']['infant'] : 0, 0);
          }
        ?>
          <div class="flight-card" style="padding-right:40px;">
            <div>
              <div class="airline-logo">TRAIN<br>JOURNEY</div>

              <div class="flight-route">
                <div class="route-airport">
                  <div class="code"><?= o3e(o3_air_code($from_loc)) ?></div>
                  <div class="city"><?= o3e(o3nv($from_loc, 'NA')) ?></div>
                </div>

                <div class="route-arrow">→</div>

                <div class="route-airport">
                  <div class="code"><?= o3e(o3_air_code($to_loc)) ?></div>
                  <div class="city"><?= o3e(o3nv($to_loc, 'NA')) ?></div>
                </div>
              </div>
            </div>

            <div></div>

            <div class="flight-num-class" style="text-align:right;">
              <div class="flight-num">TRAIN</div>
              <div class="flight-class-label">Class</div>
              <div class="flight-class"><?= o3e($train_class) ?></div>
            </div>

            <div class="flight-footer">
              <div class="ff-item">
                <div class="ff-label">Date & Time</div>
                <div class="ff-value"><?= o3e(o3nv($from_date, 'NA')) ?></div>
              </div>

              <div class="ff-item">
                <div class="ff-label">From</div>
                <div class="ff-value"><?= o3e(o3nv($from_loc, 'NA')) ?></div>
              </div>

              <div class="ff-item">
                <div class="ff-label">To</div>
                <div class="ff-value"><?= o3e(o3nv($to_loc, 'NA')) ?></div>
              </div>

              <div class="ff-item">
                <div class="ff-label">Total Pax</div>
                <div class="ff-value"><?= o3e($total_pax) ?></div>
              </div>
            </div>

            <div class="ticket-stripe"></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <!-- ================== Activity Details -->
      <?php if (!empty($acts)) : ?>
        <div class="section-sub-heading" style="margin-top:28px;">
          <div class="badge-letter">C</div>ACTIVITY DETAILS
        </div>

        <?php foreach ($acts as $a) :
          $activity_img = BASE_URL . 'images/activity.jpg';

          $activity_name = isset($a['activity_name']) ? $a['activity_name'] : '';
          $city_name = isset($a['city_name']) ? $a['city_name'] : '';
          $activity_date = isset($a['date']) ? $a['date'] : '';
          $transfer_type = isset($a['transfer_type']) ? $a['transfer_type'] : '';

          $total_pax = 0;
          if (isset($a['pax']) && is_array($a['pax'])) {
            $total_pax =
              (int)o3nv(isset($a['pax']['adult']) ? $a['pax']['adult'] : 0, 0) +
              (int)o3nv(isset($a['pax']['chwb']) ? $a['pax']['chwb'] : 0, 0) +
              (int)o3nv(isset($a['pax']['chwob']) ? $a['pax']['chwob'] : 0, 0) +
              (int)o3nv(isset($a['pax']['infant']) ? $a['pax']['infant'] : 0, 0);
          }
        ?>
          <div class="transport-card" style="margin-bottom:14px;">
            <img src="<?= o3e($activity_img) ?>" alt="<?= o3e(o3nv($activity_name, 'Activity')) ?>" />

            <div style="display:flex;gap:16px;flex:1;">
              <div class="transport-timeline" style="padding-top:8px;">
                <div class="tl-dot"></div>
                <div class="tl-line"></div>
                <div class="tl-dot"></div>
                <div class="tl-line"></div>
                <div class="tl-person"></div>
              </div>

              <div class="transport-details">
                <div class="td-row">
                  <div class="td-label">Activity Name</div>
                  <div class="td-value"><?= o3e(o3nv($activity_name, 'Activity')) ?></div>
                </div>

                <div class="td-row">
                  <div class="td-label">City</div>
                  <div class="td-value"><?= o3e(o3nv($city_name, 'NA')) ?></div>
                </div>

                <div class="td-row">
                  <div class="td-label">Date</div>
                  <div class="td-value"><?= o3e(o3nv($activity_date, 'NA')) ?></div>
                </div>

                <div class="td-row">
                  <div class="td-label">Transfer Type</div>
                  <div class="td-value"><?= o3e(o3nv($transfer_type, 'NA')) ?></div>
                </div>

                <div class="td-row">
                  <div class="td-label">Total Pax</div>
                  <div class="td-value"><?= o3e($total_pax) ?> Pax</div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
      <!-- ========================== -->
      <?php if (!empty($vehs)) : ?>
        <div class="section-sub-heading" style="margin-top:28px;">
          <div class="badge-letter">D</div>TRANSPORTATION
        </div>
        <?php foreach ($vehs as $v) : ?>
          <div class="transport-card" style="margin-bottom:14px;">
            <!-- <img src="<? //= o3e(o3img(isset($v['vehicle_image']) ? $v['vehicle_image'] : '', $assets . 'vehicle.jpg')) 
                            ?>" alt="<? //= o3e(o3nv($v['vehicle_name'], 'Vehicle')) 
                                      ?>" /> -->
            <?php $vehicle_img = BASE_URL . 'images/vehicle.png'; ?>

            <img src="<?= o3e($vehicle_img) ?>"
              alt="<?= o3e(o3nv($v['vehicle_name'], 'Vehicle')) ?>" />

            <div style="display:flex;gap:16px;flex:1;">
              <div class="transport-timeline" style="padding-top:8px;">
                <div class="tl-dot"></div>
                <div class="tl-line"></div>
                <div class="tl-dot"></div>
                <div class="tl-line"></div>
                <div class="tl-person"></div>
              </div>
              <div class="transport-details">
                <div class="td-row">
                  <div class="td-label">Vehicle</div>
                  <div class="td-value"><?= o3e(o3nv($v['vehicle_name'], '')) ?><?php if (!empty($v['vehicle_count'])) : ?> (<?= o3e($v['vehicle_count']) ?> Unit<?= ((int)$v['vehicle_count'] > 1 ? 's' : '') ?>)<?php endif; ?></div>
                </div>
                <div class="td-row">
                  <div class="td-label">Pickup Location</div>
                  <div class="td-value"><?= o3e(o3nv($v['pickup'], 'NA')) ?></div>
                </div>
                <div class="td-row">
                  <div class="td-label">Drop Location</div>
                  <div class="td-value"><?= o3e(o3nv($v['drop'], 'NA')) ?></div>
                </div>
                <div class="td-row">
                  <div class="td-label">Start Date</div>
                  <div class="td-value"><?= o3e(o3nv($v['date'], '')) ?></div>
                </div>
                <div class="td-row">
                  <div class="td-label">End Date</div>
                  <div class="td-value"><?= o3e(o3_vehicle_end_date($v)) ?></div>
                </div>
                <div class="td-row">
                  <div class="td-label">Service Duration</div>
                  <div class="td-value"><?= o3e(o3nv($v['service_duration'], o3nv($v['description'], ''))) ?></div>
                </div>
                <div class="td-row">
                  <div class="td-label">Vehicle Category</div>
                  <div class="td-value"><?= o3e(o3nv($v['vehicle_type'], 'Private Transfer')) ?></div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <hr class="page-divider" />

    <div class="page-break"></div>
    <!-- ITINERARY -->

    <div class="itinerary-page">
      <?php o3_render_page_header($hero, $ov, $assets); ?>
      <div class="itinerary-section page-flow-section ">
        <!-- <div class="section-sub-heading"> -->
        <div class="section-sub-heading keep-with-next">
          <div class="badge-letter">C</div>DAY WISE ITINERARY
        </div>

        <?php if (!empty($itin)) :
          foreach ($itin as $day) :
            // $day_img = o3img(isset($day['image']) ? $day['image'] : '', $assets . 'day-' . ((($day['day_number'] - 1) % 6) + 1) . '.jpg');
            $dummy_day_img = BASE_URL . 'images/itinerary.png';

            $o3_day_photo = isset($day['image']) ? trim($day['image']) : '';

            if ($o3_day_photo == '' || stripos($o3_day_photo, 'dummy') !== false) {
              $day_img = $dummy_day_img;
            } else {
              $day_img = o3img($o3_day_photo, $dummy_day_img);
            }
            $day_date = o3nv($day['date'], '');
            $day_date_html = str_replace(' ', '<br/>', o3e($day_date));
        ?>
            <div class="day-card">
              <div class="day-badge">
                <div class="day-word">DAY</div>
                <div class="day-num"><?= o3e(o3nv($day['day_number'], '')) ?></div>
                <div class="day-date"><?= $day_date_html ?></div>
              </div>
              <div class="day-img-wrap">
                <img src="<?= o3e($day_img) ?>" alt="<?= o3e(o3nv($day['special_attraction'], 'Day')) ?>" />
                <div class="day-img-overlay"></div>
              </div>
              <div class="day-info">
                <div class="day-attr-label">Special Attractions:</div>
                <div class="day-attr"><?= o3e(o3nv($day['special_attraction'], o3nv($day['city'], 'Sightseeing'))) ?></div>
                <div class="day-prog-label">Detailed Program:</div>
                <div class="day-prog"><?= o3e(o3nv($day['detailed_programme'], '')) ?></div>
                <div class="day-chips">
                  <?php if (!empty($day['meal_plan'])) : ?>
                    <span class="day-chip"><span class="chip-icon">🍽️</span> Meal Plan: <strong><?= o3e($day['meal_plan']) ?></strong></span>
                  <?php endif; ?>
                  <?php if (!empty($day['overnight_stay'])) : ?>
                    <span class="day-chip"><span class="chip-icon">🏨</span> Stay: <strong><?= o3e($day['overnight_stay']) ?></strong></span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php
          endforeach;
        else :
          ?>
          <div class="day-card">
            <div class="day-info" style="padding:20px;">
              <div class="day-prog">Itinerary details will be shared upon confirmation.</div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <hr class="page-divider" />
    <div class="page-break"></div>

    <!-- INCLUSIONS / COSTING -->
    <div class="print-section">
      <?php o3_render_page_header($hero, $ov, $assets); ?>
      <div class="inc-exc-row page-flow-section ">
        <div class="inc-card">
          <div class="inc-exc-title">
            <div class="inc-exc-badge inc-badge">A</div>WHAT'S INCLUDED
          </div>
          <ul class="inc-list">
            <?php foreach ($o3_included as $item) : ?>
              <li><span class="chk-icon">✅</span> <?= o3e($item) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="exc-card">
          <div class="inc-exc-title">
            <div class="inc-exc-badge exc-badge">B</div>WHAT'S EXCLUDED
          </div>
          <ul class="exc-list">
            <?php foreach ($o3_excluded as $item) : ?>
              <li><span class="x-icon">✗</span> <?= o3e($item) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      </br>

      <!-- ======================= Costing -->
      <?php
      $o3_costing_type = isset($cost['costing_type_label']) ? strtolower(trim($cost['costing_type_label'])) : '';
      $o3_is_per_person = ($o3_costing_type == 'per person');
      $o3_pp = isset($cost['computed']['per_person']) ? $cost['computed']['per_person'] : array();
      ?>
      <div class="costing-section page-flow-section">
        <div class="section-sub-heading" style="margin-bottom:20px;">
          <div class="badge-letter">C</div>COSTING DETAILS
        </div>
        <?php if (!$o3_is_per_person) { ?>

          <table class="costing-table">
            <thead>
              <tr>
                <th>Package Type</th>
                <th>Tour Cost (INR)</th>
                <th>Tax (INR)</th>
                <th>TCS (INR)</th>
                <th>Travel Cost (INR)</th>
                <th>Grand Total (INR)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($o3_cost_grp as $ci => $row) :
                $is_royal = (stripos(o3nv($row['package_type'], ''), 'royal') !== false);
              ?>
                <tr<?= $is_royal ? ' class="royal-row"' : '' ?>>
                  <?php
                  $tax_amount = '0.00';

                  if (!empty($row['tax_display'])) {
                    preg_match('/INR\s*([\d,\.]+)/i', $row['tax_display'], $m);
                    if (!empty($m[1])) {
                      $tax_amount = $m[1];
                    }
                  }
                  ?>
                  <td class="pkg-name"><?php if ($is_royal) : ?><span class="royal-star">★</span> <?php endif; ?><?= o3e(o3nv($row['package_type'], 'Package')) ?></td>
                  <td>&#8377;<?= o3e(o3nv($row['tour_cost_display'], '0')) ?></td>
                  <td>&#8377; INR<?= o3e($tax_amount) ?></td>
                  <td>&#8377;<?= o3e(o3nv($row['tcs_display'], '0')) ?></td>
                  <td>&#8377;<?= o3e(o3nv($row['travel_display'], '0')) ?></td>
                  <td class="grand-total">&#8377;<?= o3e(o3nv($row['total_display'], '0')) ?></td>
                  </tr>
                <?php endforeach; ?>
            </tbody>
          </table>
        <?php } else { ?>

          <?php if (!empty($o3_pp)) { ?>
            <table class="costing-table">
              <thead>
                <tr>
                  <th>Package</th>
                  <th>Adult</th>
                  <th>CWB</th>
                  <th>CWOB</th>
                  <th>Infant</th>
                  <th>Tax</th>
                  <th>TCS</th>
                  <th>Visa</th>
                  <th>Guide</th>
                  <th>Misc</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($o3_pp as $pp) { ?>
                  <?php
                  $tax_amount = '0.00';
                  if (!empty($pp['tax_display'])) {
                    preg_match('/INR\s*([\d,\.]+)/i', $pp['tax_display'], $m);
                    if (!empty($m[1])) {
                      $tax_amount = $m[1];
                    }
                  }
                  ?>

                  <tr>
                    <td class="pkg-name"><?= o3e(o3nv($pp['package_type'], 'Package')) ?></td>
                    <td>&#8377; <?= o3e(o3nv($pp['pp_adult_display'], '0')) ?></td>
                    <td>&#8377; <?= o3e(o3nv($pp['pp_cwb_display'], '0')) ?></td>
                    <td>&#8377; <?= o3e(o3nv($pp['pp_cwnb_display'], '0')) ?></td>
                    <td>&#8377; <?= o3e(o3nv($pp['pp_infant_display'], '0')) ?></td>
                    <td>&#8377; INR</br><?= o3e($tax_amount) ?></td>
                    <td>&#8377; <?= o3e(o3nv($pp['tcs_display'], '0')) ?></td>
                    <td>&#8377; <?= o3e(o3nv($pp['visa_display'], '0')) ?></td>
                    <td>&#8377; <?= o3e(o3nv($pp['guide_display'], '0')) ?></td>
                    <td>&#8377; <?= o3e(o3nv($pp['misc_display'], '0')) ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          <?php } ?>

        <?php } ?>

        <div class="costing-note">
          <?= o3e(o3nv(isset($incx['note']) ? $incx['note'] : '', 'Note: Rates are subject to availability at the time of confirmation.')) ?>
        </div>
      </div>
    </div>

    <hr class="page-divider" />
    <div class="page-break"></div>

    <!-- PAYMENT -->
    <div class="print-section">
      <?php o3_render_page_header($hero, $ov, $assets); ?>

      <div class="payment-section page-flow-section">
        <h2>Payment Information</h2>
        <div class="payment-grid">
          <div class="payment-info-card">
            <div class="pay-row">
              <div class="pay-icon">👤</div>
              <div>
                <div class="pay-detail-label">Account Name</div>
                <div class="pay-detail-value"><?= o3e(o3nv($bank['account_name'], 'NA')) ?></div>
              </div>
            </div>
            <div class="pay-row">
              <div class="pay-icon">🏦</div>
              <div>
                <div class="pay-detail-label">Account Number</div>
                <div class="pay-detail-value"><?= o3e(o3nv($bank['account_no'], 'NA')) ?></div>
              </div>
            </div>
            <div class="pay-row">
              <div class="pay-icon">🏛️</div>
              <div>
                <div class="pay-detail-label">Bank Name</div>
                <div class="pay-detail-value"><?= o3e(o3nv($bank['bank_name'], 'NA')) ?></div>
              </div>
            </div>
            <div class="pay-row">
              <div class="pay-icon">📍</div>
              <div>
                <div class="pay-detail-label">Branch</div>
                <div class="pay-detail-value"><?= o3e(o3nv($bank['branch_name'], 'NA')) ?></div>
              </div>
            </div>
            <div class="pay-row">
              <div class="pay-icon">🔢</div>
              <div>
                <div class="pay-detail-label">IFSC Code</div>
                <div class="pay-detail-value"><?= o3e(o3nv($bank['ifsc_code'], o3nv($bank['swift_code'], 'NA'))) ?></div>
              </div>
            </div>
            <?php if (!empty($bank['upi_id'])) : ?>
              <div class="pay-row">
                <div class="pay-icon">💳</div>
                <div>
                  <div class="pay-detail-label">UPI ID</div>
                  <div class="pay-detail-value"><?= o3e($bank['upi_id']) ?></div>
                </div>
              </div>
            <?php endif; ?>
          </div>
          <div class="qr-card">
            <div class="qr-title">SCAN &amp; PAY</div>
            <div class="qr-placeholder">
              <?php if (!empty($bank['qr_html'])) : ?>
                <?= $bank['qr_html'] ?>
              <?php elseif (!empty($bank['qr_code']) || !empty($bank['branch_qr_url'])) : ?>
                <img src="<?= o3e(o3nv($bank['branch_qr_url'], $bank['qr_code'])) ?>" alt="Payment QR" style="width:110px;height:110px;object-fit:contain;" />
              <?php else : ?>
                <span style="color:#fff;font-size:11px;">QR not configured</span>
              <?php endif; ?>
            </div>
            <?php if (!empty($bank['upi_id'])) : ?>
              <div class="upi-logo">UPI▶ <?= o3e($bank['upi_id']) ?></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="pay-instructions-grid">
          <div class="pay-inst-card">
            <div class="pay-inst-title">PAYMENT INSTRUCTIONS</div>
            <ul class="pay-inst-list">
              <?php foreach ($o3_pay_notes as $note) : ?>
                <li><?= o3e($note) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="pay-inst-card">
            <div class="pay-inst-title">BOOKING POLICY</div>
            <ul class="pay-inst-list">
              <?php foreach (array_slice($o3_book_policy, 0, 4) as $pol) : ?>
                <li><?= o3e($pol) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <hr class="page-divider" />
    <div class="page-break"></div>

    <!-- TESTIMONIALS -->
    <div class="testimonial-page print-section">
      <?php o3_render_page_header($hero, $ov, $assets); ?>

      <div class="testimonials-section page-flow-section">
        <h2>What Our Travellers Say</h2>
        <?php if (!empty($testimonials)) :
          foreach ($testimonials as $t) :
            $photo = isset($t['photo']) ? trim($t['photo']) : '';
            if ($photo !== '' && strpos($photo, 'http') !== 0) {
              $photo = BASE_URL . ltrim(str_replace('\\', '/', $photo), '/');
            }
        ?>
            <div class="testi-card">
              <?php if ($photo !== '') : ?>
                <img class="testi-avatar" src="<?= o3e($photo) ?>" alt="<?= o3e(o3nv($t['name'], '')) ?>" />
              <?php else : ?>
                <div class="testi-avatar" style="background:var(--navy);color:var(--gold-lt);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:24px;font-weight:700;"><?= o3e(strtoupper(substr(o3nv($t['name'], 'T'), 0, 1))) ?></div>
              <?php endif; ?>
              <div class="testi-body">
                <div class="testi-name-row">
                  <div class="testi-name"><?= o3e(o3nv($t['name'], 'Traveller')) ?></div>
                  <div class="testi-stars">★★★★★</div>
                </div>
                <div class="testi-dest"><?= o3e(o3nv($t['designation'], '')) ?></div>
                <div class="testi-quote"><?= o3e(o3nv($t['review'], '')) ?></div>
              </div>
            </div>
          <?php
          endforeach;
        else :
          ?>
          <div class="testi-card">
            <div class="testi-body">
              <div class="testi-quote">Customer testimonials can be managed from Quotation Builder settings.</div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <hr class="page-divider" />
    <div class="page-break"></div>

    <div class="terms-page print-section">
      <!-- TERMS -->
      <?php o3_render_page_header($hero, $ov, $assets); ?>

      <div class="terms-section page-flow-section">
        <h2><?= o3e(o3nv($terms['title'], 'Terms & Conditions')) ?></h2>

        <div class="terms-content terms-content-icons">
          <?= isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '' ?>
        </div>
      </div>

      <hr class="page-divider" />

      <!-- THANK YOU -->
      <div class="thankyou-page print-section">
        <div class="ty-circle-1"></div>
        <div class="ty-circle-2"></div>
        <div class="ty-circle-3"></div>
        <div class="ty-stripes"></div>
        <div class="ty-content">
          <div class="ty-top-line">
            <div class="ty-gold-line"></div>
            <div class="ty-gold-line rev"></div>
          </div>
          <div class="ty-heading">THANK YOU</div>
          <div class="ty-subtext">for choosing us as your travel partner</div>
          <div class="ty-diamond-row">
            <div class="ty-diamond-line"></div>
            <span class="ty-diamond">◆</span>
            <div class="ty-diamond-line"></div>
          </div>
          <div class="ty-message">
            Dear <?= o3e($o3_client) ?>, we truly appreciate your trust in <?= o3e(o3nv($ty['company_name'], $hero['company_name'])) ?>.
            Our team is committed to crafting an unforgettable travel experience for you.
            Should you have any questions, feel free to reach out anytime.
          </div>

          <div class="ty-contacts">
            <?php
            $call_no = preg_replace('/\D+/', '', o3nv($ty['company_contact'], o3nv($ty['user_mobile'], '')));
            $wa_no   = preg_replace('/\D+/', '', o3nv($ty['user_mobile'], o3nv($ty['company_contact'], '')));
            ?>
            <a class="ty-contact-card" href="tel:<?= o3e($call_no) ?>">
              <div class="ty-contact-icon-wrap ty-icon-blue">📞</div>
              <div>
                <div class="ty-contact-label">Call Us</div>
                <div class="ty-contact-value"><?= o3e(o3nv($ty['company_contact'], o3nv($ty['user_mobile'], ''))) ?></div>
              </div>
            </a>

            <a class="ty-contact-card" href="https://wa.me/<?= o3e($wa_no) ?>" target="_blank">
              <div class="ty-contact-icon-wrap ty-icon-green">💬</div>
              <div>
                <div class="ty-contact-label">WhatsApp</div>
                <div class="ty-contact-value"><?= o3e(o3nv($ty['user_mobile'], o3nv($ty['company_contact'], ''))) ?></div>
              </div>
            </a>
            <div class="ty-contact-card">
              <div class="ty-contact-icon-wrap ty-icon-red">✉️</div>
              <div>
                <div class="ty-contact-label">Email Us</div>
                <div class="ty-contact-value"><?= o3e(o3nv($ty['company_email'], '')) ?></div>
              </div>
            </div>
            <div class="ty-contact-card">
              <div class="ty-contact-icon-wrap ty-icon-purple">🌐</div>
              <div>
                <div class="ty-contact-label">Visit Us</div>
                <div class="ty-contact-value"><?= o3e(o3nv($ty['website'], '')) ?></div>
              </div>
            </div>
          </div>

          <div class="ty-address-row">
            <span class="ty-address-diamond">◆</span>
            <span class="ty-address-text"><?= o3e(o3nv($ty['company_address'], '')) ?></span>
          </div>

          <div class="ty-stats">
            <div class="ty-stat">
              <div class="ty-stat-num"><?= o3e(o3nv($ty['quotation_code'], $hero['quotation_code'])) ?></div>
              <div class="ty-stat-label">Quotation Ref</div>
            </div>
            <div class="ty-stat-divider"></div>
            <div class="ty-stat">
              <div class="ty-stat-num"><?= o3e(o3nv($ty['issue_date'], o3nv($ov['quotation_date'], ''))) ?></div>
              <div class="ty-stat-label">Issue Date</div>
            </div>
            <div class="ty-stat-divider"></div>
            <div class="ty-stat">
              <div class="ty-stat-num"><?= o3e(o3nv($ty['prepared_by'], o3nv($hero['login_user'], 'Team'))) ?></div>
              <div class="ty-stat-label">Prepared By</div>
            </div>
          </div>

          <div class="ty-brand-footer">
            <div class="ty-brand-name"><?= o3e(strtoupper(o3nv($ty['company_name'], $hero['company_name']))) ?></div>
            <div class="ty-brand-tagline">YOUR TRUSTED TRAVEL PARTNER</div>
          </div>
        </div>
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