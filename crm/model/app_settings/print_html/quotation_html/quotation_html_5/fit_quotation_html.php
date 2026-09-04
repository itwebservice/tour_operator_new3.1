<?php

/**
 * OPTION-5 (quotation_html_5) — Package Tour Quotation
 * Layout/CSS from Final-Designs/Option-5-Done/option-5.html
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
$o5_hotels_by_pkg = (!empty($q['hotels_by_package_type']) && is_array($q['hotels_by_package_type']))
  ? $q['hotels_by_package_type']
  : (function_exists('gqd_hotels_by_package_type') ? gqd_hotels_by_package_type($hotels) : array());
$o5_multi_pkg = count($o5_hotels_by_pkg) > 1;
$flights      = $q['flights'];
$trains  = isset($q['trains']) ? $q['trains'] : array();
$cruises = isset($q['cruises']) ? $q['cruises'] : array();
$acts    = isset($q['activities']) ? $q['activities'] : array();
$vehs         = $q['vehicles'];
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
$o5_cfg       = array();

$testimonials = isset($q['testimonials']) && is_array($q['testimonials'])
  ? $q['testimonials'] : array();

if (!function_exists('o5e')) {
  function o5e($v)
  {
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
  }
}
if (!function_exists('o5nv')) {
  function o5nv($v, $f = '')
  {
    return ($v !== null && $v !== '') ? $v : $f;
  }
}
if (!function_exists('o5_media_url')) {
  function o5_media_url($url)
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
if (!function_exists('o5img')) {
  function o5img($url, $fallback)
  {
    $resolved = o5_media_url($url);
    return $resolved !== '' ? $resolved : $fallback;
  }
}
if (!function_exists('o5_guest_label')) {
  function o5_guest_label($ov)
  {
    $p = isset($ov['pax']) ? $ov['pax'] : array();
    $parts = array();
    $ad = (int) o5nv(isset($p['adult']) ? $p['adult'] : 0, 0);
    $ch = (int) o5nv(isset($p['children_with_bed']) ? $p['children_with_bed'] : 0, 0)
      + (int) o5nv(isset($p['children_without_bed']) ? $p['children_without_bed'] : 0, 0);
    $inf = (int) o5nv(isset($p['infant']) ? $p['infant'] : 0, 0);
    if ($ad) {
      $parts[] = $ad . ' Adult' . ($ad > 1 ? 's' : '');
    }
    if ($ch) {
      $parts[] = $ch . ' Child' . ($ch > 1 ? 'ren' : '');
    }
    if ($inf) {
      $parts[] = $inf . ' Infant' . ($inf > 1 ? 's' : '');
    }
    return $parts ? implode(', ', $parts) : o5nv($ov['guest_count'], '-');
  }
}
if (!function_exists('o5_list_item_text')) {
  function o5_list_item_text($html)
  {
    $html = preg_replace('/<br\s*\/?>/i', ' ', (string) $html);
    $text = strip_tags($html);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = str_replace("\xC2\xA0", ' ', $text);
    $text = preg_replace('/\s+/u', ' ', $text);
    return trim($text);
  }
}
if (!function_exists('o5_split_lines')) {
  function o5_split_lines($html, $fallback = array())
  {
    $html = (string) $html;
    $items = array();

    if (trim($html) === '') {
      return $fallback;
    }

    if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $html, $matches)) {
      foreach ($matches[1] as $chunk) {
        $text = o5_list_item_text($chunk);
        if ($text !== '') {
          $items[] = $text;
        }
      }
    }

    if (empty($items) && preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $html, $matches)) {
      foreach ($matches[1] as $chunk) {
        $text = o5_list_item_text($chunk);
        if ($text !== '') {
          $items[] = $text;
        }
      }
    }

    if (empty($items)) {
      $plain = o5_list_item_text($html);
      $parts = preg_split('/\r\n|\r|\n|•|\x{2022}/u', $plain);
      foreach ((array) $parts as $part) {
        $text = trim($part);
        if ($text !== '') {
          $items[] = $text;
        }
      }
    }

    return $items ? $items : $fallback;
  }
}
if (!function_exists('o5_air_code')) {
  function o5_air_code($loc)
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
if (!function_exists('o5_cover_images')) {
  function o5_cover_images($hero, $gallery, $hotels, $itin, $assets)
  {
    $imgs = array();
    $cover = o5_media_url(o5nv(isset($hero['cover_image']) ? $hero['cover_image'] : '', ''));
    if ($cover !== '') {
      $imgs[] = $cover;
    }
    if (!empty($hero['destination_5th_gallery_image'])) {
      $g5 = o5_media_url($hero['destination_5th_gallery_image']);
      if ($g5 !== '' && !in_array($g5, $imgs, true)) {
        $imgs[] = $g5;
      }
    }
    foreach ($gallery as $g) {
      if (count($imgs) >= 3) {
        break;
      }
      $p = o5_media_url($g);
      if ($p !== '' && !in_array($p, $imgs, true)) {
        $imgs[] = $p;
      }
    }
    foreach ($hotels as $h) {
      if (count($imgs) >= 3) {
        break;
      }
      $p = o5_media_url(isset($h['hotel_photo']) ? $h['hotel_photo'] : '');
      if ($p !== '' && !in_array($p, $imgs, true)) {
        $imgs[] = $p;
      }
    }
    foreach ($itin as $d) {
      if (count($imgs) >= 3) {
        break;
      }
      $p = o5_media_url(isset($d['image']) ? $d['image'] : '');
      if ($p !== '' && !in_array($p, $imgs, true)) {
        $imgs[] = $p;
      }
    }
    $fallback = array(
      $assets . 'cover-1.jpg',
      $assets . 'cover-2.jpg',
      $assets . 'cover-3.jpg',
    );
    while (count($imgs) < 3) {
      $imgs[] = $fallback[count($imgs) % 3];
    }
    return array_slice($imgs, 0, 3);
  }
}
if (!function_exists('o5_render_page_header_strip')) {
  function o5_render_page_header_strip($hero, $right_label, $badge = false)
  {
    $logo = o5nv($hero['company_logo'], '');
    $name = o5nv($hero['company_name'], 'Travel Partner');
?>
    <div class="page-header-strip">
      <div class="phs-logo">
        <?php if ($logo !== '') : ?>
          <img src="<?= o5e($logo) ?>" alt="<?= o5e($name) ?>" class="company-logo-img" />
        <?php else : ?>
          <div class="phs-logo-icon">✈</div>
        <?php endif; ?>
        <!-- <div class="phs-logo-name"><? //= o5e($name) 
                                        ?></div> -->
      </div>
      <?php if ($badge) : ?>
        <span class="phs-pkg-badge"><?= o5e(strtoupper($right_label)) ?></span>
      <?php else : ?>
        <span class="phs-right"><?= o5e($right_label) ?></span>
      <?php endif; ?>
    </div>
<?php
  }
}
if (!function_exists('o5_stars')) {
  function o5_stars($rating)
  {
    $n = (int) preg_replace('/\D/', '', (string) $rating);
    if ($n < 1) {
      $n = 5;
    }
    return str_repeat('☆', min($n, 5));
  }
}
if (!function_exists('o5_vehicle_end_date')) {
  function o5_vehicle_end_date($v)
  {
    if (!empty($v['end_date_raw']) && function_exists('get_date_user')) {
      return get_date_user($v['end_date_raw']);
    }
    return o5nv(isset($v['end_date_raw']) ? $v['end_date_raw'] : '', '');
  }
}
if (!function_exists('o5_flight_parts')) {
  function o5_flight_parts($dt)
  {
    $dt = trim((string) $dt);
    $time = '—';
    $date = '—';
    if (preg_match('/(\d{1,2}:\d{2}(?::\d{2})?\s*(?:AM|PM|am|pm)?)/', $dt, $m)) {
      $time = trim($m[1]);
    }
    if (preg_match('/(\d{2}[-\/]\d{2}[-\/]\d{4})/', $dt, $m)) {
      $date = $m[1];
    } elseif (preg_match('/(\d{4}[-\/]\d{2}[-\/]\d{2})/', $dt, $m)) {
      $date = $m[1];
    }
    return array($time, $date);
  }
}
if (!function_exists('o5_testi_photo')) {
  function o5_testi_photo($photo)
  {
    return o5_media_url($photo);
  }
}
if (!function_exists('o5_term_cards')) {
  function o5_term_cards($lines)
  {
    $titles = array('Booking Policy', 'Cancellation Policy', 'Refund Policy', 'Visa Disclaimer', 'General Terms', 'Important Notes');
    $cards = array();
    if (empty($lines)) {
      return $cards;
    }
    $chunks = array_chunk($lines, 4);
    foreach ($chunks as $ci => $chunk) {
      $cards[] = array(
        'num'   => $ci + 1,
        'title' => isset($titles[$ci]) ? $titles[$ci] : 'Terms & Conditions',
        'items' => $chunk,
      );
    }
    return $cards;
  }
}

$o5_dest        = o5nv($ov['destination'], o5nv($hero['tour_name'], 'Tour'));
$o5_client      = o5nv($ov['client_name'], o5nv($hero['client_name'], 'Guest'));
$o5_company     = o5nv($hero['company_name'], 'Travel Partner');
$o5_logo        = o5nv($hero['company_logo'], '');
$o5_cover_imgs  = o5_cover_images($hero, $gallery, $hotels, $itin, $assets);
$o5_banner_img  = o5img(o5nv($hero['cover_image'], ''), !empty($gallery[0]) ? o5_media_url($gallery[0]) : $assets . 'banner.jpg');
$o5_ty_bg       = o5img(o5nv($hero['cover_image'], ''), $assets . 'thankyou.jpg');
$o5_tour_id     = o5nv($ov['enquiry_code'], o5nv($ov['enquiry_id'], ''));
$o5_duration    = o5nv($ov['duration_label'], o5nv($hero['duration_label'], ''));
$o5_travel_pill = trim(o5nv($ov['travel_from'], '') . (o5nv($ov['travel_to'], '') !== '' ? ' to ' . o5nv($ov['travel_to'], '') : ''));
$o5_pkg_badge   = '';
if (!empty($q['package_types_label'])) {
  $o5_pkg_badge = $q['package_types_label'];
} elseif (!empty($cost['computed']['group'][0]['package_type'])) {
  $o5_pkg_badge = $cost['computed']['group'][0]['package_type'];
} elseif (!empty($hotels[0]['package_type'])) {
  $o5_pkg_badge = $hotels[0]['package_type'];
}

// ============== Dipti
$o5_banner_img = !empty($hero['destination_5th_gallery_image'])
  ? $hero['destination_5th_gallery_image']
  : $assets . 'banner.jpg';
// ================

$o5_pkg_ov = o5nv($o5_pkg_badge, o5nv($ov['package_type_label'], 'Package'));
$o5_included = o5_split_lines(isset($incx['included']) ? $incx['included'] : '', array('Inclusions as per itinerary.'));
$o5_excluded = o5_split_lines(isset($incx['excluded']) ? $incx['excluded'] : '', array('Exclusions as per company policy.'));
$o5_cost_grp = isset($cost['computed']['group']) ? $cost['computed']['group'] : array();
if (empty($o5_cost_grp)) {
  $o5_cost_grp = array(array(
    'package_type'      => 'Package',
    'tour_cost_display' => '0',
    'tax_display'       => '0',
    'tcs_display'       => '0',
    'travel_display'    => '0',
    'total_display'     => '0',
  ));
}
$o5_start_total = o5nv($o5_cost_grp[0]['total_display'], '0');
$o5_pay_notes = o5_split_lines(
  o5nv(isset($incx['quot_note']) ? $incx['quot_note'] : '', ''),
  array(
    '50% advance payment required to confirm booking.',
    'Remaining balance before departure as per company policy.',
    'Payment can be made via NEFT/RTGS/IMPS/UPI.',
    'Booking confirmation within 24 hours of payment.',
  )
);
$o5_book_policy = o5_split_lines(
  o5nv(isset($incx['note']) ? $incx['note'] : '', ''),
  array(
    'All bookings are subject to availability.',
    'Package rates are as quoted in this proposal.',
    'Rates may vary during peak or festive seasons.',
    'Valid passport with 6 months validity required where applicable.',
  )
);
$o5_cancel_notes = o5_split_lines(
  isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '',
  array(
    '30+ days before: 10% cancellation fee',
    '15-30 days before: 25% cancellation fee',
    'Less than 15 days: 50% cancellation fee',
  )
);
$o5_term_lines = o5_split_lines(isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '', array());
$o5_term_cards = o5_term_cards($o5_term_lines);
$o5_google_rating = o5nv(isset($o5_cfg['google_rating']) ? $o5_cfg['google_rating'] : '', '4.9');
$o5_review_count  = o5nv(isset($o5_cfg['review_count']) ? $o5_cfg['review_count'] : '', '2,847+');
$o5_traveller_cnt = o5nv(isset($o5_cfg['traveller_count']) ? $o5_cfg['traveller_count'] : '', '15,000+');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= o5e($o5_dest) ?> Tour Package – <?= o5e($o5_company) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400;1,700&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link href="option5.css" rel="stylesheet" />
</head>

<body>
  <div class="doc">

    <!-- COVER -->
    <div class="cover">
      <div class="cover-nav">
        <div class="cover-logo">
          <?php if ($o5_logo !== '') : ?>
            <img src="<?= o5e($o5_logo) ?>" alt="<?= o5e($o5_company) ?>" class="company-logo-img" />
          <?php else : ?>
            <div class="cover-logo-icon">✈</div>
          <?php endif; ?>
        </div>
        <div class="cover-tagline-right">Discover Extraordinary Experiences</div>
      </div>

      <div class="cover-icons-row">
        <div class="cover-icon-item">
          <div class="cover-icon-circle">🏨</div>
          <div class="cover-icon-label">Hotels</div>
        </div>
        <div class="cover-icon-item">
          <div class="cover-icon-circle">✈️</div>
          <div class="cover-icon-label">Flights</div>
        </div>
        <div class="cover-icon-item">
          <div class="cover-icon-circle">🚐</div>
          <div class="cover-icon-label">Transfers</div>
        </div>
        <div class="cover-icon-item">
          <div class="cover-icon-circle">📸</div>
          <div class="cover-icon-label">Activities</div>
        </div>
        <div class="cover-icon-item">
          <div class="cover-icon-circle">🍽️</div>
          <div class="cover-icon-label">Meals</div>
        </div>
        <div class="cover-icon-item">
          <div class="cover-icon-circle">📍</div>
          <div class="cover-icon-label">Sightseeing</div>
        </div>
      </div>

      <div class="cover-title-block">
        <div class="cover-singapore"><?= o5e($o5_dest) ?></div>
        <div class="cover-package-row">
          <div class="cover-divider-line"></div>
          <div class="cover-package-text">TOUR PACKAGE</div>
          <div class="cover-divider-line"></div>
        </div>
      </div>

      <div class="cover-img-grid">
        <?php foreach ($o5_cover_imgs as $ci => $cimg) : ?>
          <img src="<?= o5e($cimg) ?>" alt="<?= o5e($o5_dest) ?> <?= o5e($ci + 1) ?>" />
        <?php endforeach; ?>
      </div>

      <div class="cover-pills">
        <?php if ($o5_duration !== '') : ?>
          <div class="cover-pill"><span class="cover-pill-icon">📅</span> <?= o5e($o5_duration) ?></div>
        <?php endif; ?>
        <div class="cover-pill"><span class="cover-pill-icon">👥</span> <?= o5e(o5_guest_label($ov)) ?></div>
      </div>

      <div class="cover-for-block">
        <div class="cover-for-label">Prepared Exclusively For</div>
        <div class="cover-for-name"><?= o5e($o5_client) ?></div>
      </div>
    </div>

    <!-- OVERVIEW -->
    <div class="page-section">
      <div class="personal-banner">
        <div class="script">A Personalized Travel Experience Exclusively Designed for <span><?= o5e($o5_client) ?></span></div>
      </div>

      <div class="salutation-card">
        <div class="dear">Dear <?= o5e($o5_client) ?>,</div>
        <p>Thank you for choosing <strong><?= o5e($o5_company) ?></strong> for your upcoming journey to <?= o5e($o5_dest) ?>. We are delighted to present this carefully crafted travel proposal designed to provide memorable experiences, seamless arrangements, and exceptional hospitality throughout your trip. We look forward to creating unforgettable travel memories for you.</p>
      </div>

      <div class="sec-head">
        <h2>Tour Overview</h2>
      </div>

      <div class="overview-grid">
        <div class="ov-card dark">
          <div class="ov-card-head">
            <div class="ov-card-icon blue">🧳</div>
            <div class="ov-card-title">Quotation Details</div>
          </div>
          <div class="ov-row"><span class="lbl">Quotation ID</span><span class="val"><?= o5e(o5nv($hero['quotation_code'], '')) ?></span></div>
          <div class="ov-row"><span class="lbl">Enquiry ID</span><span class="val"><?= o5e($o5_tour_id) ?></span></div>
          <div class="ov-row"><span class="lbl">Quotation Date</span><span class="val"><?= o5e(o5nv($ov['quotation_date'], '')) ?></span></div>
        </div>
        <div class="ov-card">
          <div class="ov-card-head">
            <div class="ov-card-icon gold">📅</div>
            <div class="ov-card-title">Travel Details</div>
          </div>
          <div class="ov-row"><span class="lbl">Travel Date</span><span class="val"><?= o5e(o5nv($ov['travel_from'], '')) ?> To <?= o5e(o5nv($ov['travel_to'], '')) ?></span></div>
          <div class="ov-row"><span class="lbl">Duration</span><span class="val"><?= o5e($o5_duration) ?></span></div>
          <div class="ov-row"><span class="lbl">Total Guests</span><span class="val"><?= o5e(o5_guest_label($ov)) ?></span></div>
        </div>
      </div>

      <div style="text-align:center;margin-bottom:18px;">
        <div class="pkg-type-btn">🏅 PACKAGE TYPE - <?= o5e(strtoupper($o5_pkg_ov)) ?></div>
      </div>
      <div class="prep-for-title">PREPARED FOR</div>
      <div class="prep-bar">
        <div class="prep-item">
          <div class="prep-icon navy">👤</div>
          <div class="prep-text">
            <div class="plbl">Name</div>
            <div class="pval"><?= o5e($o5_client) ?></div>
          </div>
        </div>
        <div class="prep-item">
          <div class="prep-icon teal">✉️</div>
          <div class="prep-text">
            <div class="plbl">Email</div>
            <div class="pval">
              <a href="mailto:<?= o5e(o5nv($ov['customer_email'], o5nv($hero['user_email_id'], ''))) ?>">
                <?= o5e(o5nv($ov['customer_email'], o5nv($hero['user_email_id'], ''))) ?>
              </a>
            </div>
          </div>
        </div>

        <div class="prep-item">
          <div class="prep-icon gold">📞</div>
          <div class="prep-text">
            <div class="plbl">Phone</div>
            <div class="pval">
              <a href="tel:<?= preg_replace('/\s+/', '', o5nv($ov['customer_mobile'], o5nv($hero['user_contact'], ''))) ?>">
                <?= o5e(o5nv($ov['customer_mobile'], o5nv($hero['user_contact'], ''))) ?>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="sing-banner" style="background:linear-gradient(135deg,var(--navy),#1a3a7a),url('<?= o5e($o5_banner_img) ?>') center/cover no-repeat;background-blend-mode:overlay;">
        <div class="sing-banner-title"><?= o5e($o5_dest) ?></div>
        <div class="sing-banner-sub">Your Journey Awaits</div>
      </div>
    </div>

    <hr class="page-divider" />

    <?php o5_render_page_header_strip($hero, $o5_multi_pkg ? 'Hotels by Package Type' : ($o5_pkg_badge !== '' ? $o5_pkg_badge : $o5_pkg_ov), true); ?>

    <!-- ACCOMMODATION -->
    <div class="page-section print-section">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div class="sec-head" style="margin-bottom:0;">
          <h2>Accommodation Details</h2>
        </div>
        <?php if ($o5_multi_pkg) : ?>
          <div class="pkg-badge-gold">HOTELS BY PACKAGE TYPE</div>
        <?php elseif ($o5_pkg_badge !== '') : ?>
          <div class="pkg-badge-gold"><?= o5e(strtoupper($o5_pkg_badge)) ?> PACKAGE</div>
        <?php endif; ?>
      </div>

      <?php
      if (empty($o5_hotels_by_pkg) && !empty($hotels)) {
        $o5_hotels_by_pkg = array('Package' => $hotels);
      }
      $o5_hi = 0;
      if (!empty($o5_hotels_by_pkg)) :
        foreach ($o5_hotels_by_pkg as $o5_pkg_heading => $o5_pkg_hotels) :
          if ($o5_multi_pkg) :
      ?>
          <div class="sec-head" style="margin:18px 0 12px;">
            <h2 style="font-size:18px;">Package Type — <?= o5e($o5_pkg_heading) ?></h2>
          </div>
      <?php
          endif;
          foreach ($o5_pkg_hotels as $h) :
          $o5_hi++;
          $dummy_hotel_img = BASE_URL . 'images/hotel.png';

          $o5_hotel_photo = isset($h['hotel_photo']) ? trim($h['hotel_photo']) : '';

          if ($o5_hotel_photo == '' || stripos($o5_hotel_photo, 'dummy') !== false) {
            $hphoto = $dummy_hotel_img;
          } else {
            $hphoto = o5img($o5_hotel_photo, $dummy_hotel_img);
          }
          $room_label = o5nv($h['room_category'], o5nv($h['room_type'], 'Standard Room'));
      ?>
          <div class="hotel-card">
            <div class="hotel-inner">
              <div class="hotel-img"><img src="<?= o5e($hphoto) ?>" alt="<?= o5e(o5nv($h['hotel_name'], 'Hotel')) ?>" /></div>
              <div class="hotel-body">
                <?php if ($o5_multi_pkg) : ?>
                  <div class="hotel-loc" style="letter-spacing:.12em;text-transform:uppercase;margin-bottom:4px;">🏷️ <?= o5e($o5_pkg_heading) ?></div>
                <?php endif; ?>
                <div class="hotel-loc">📍 <?= o5e(o5nv($h['hotel_city'], '')) ?></div>
                <div class="hotel-name"><?= o5e(o5nv($h['hotel_name'], 'Hotel')) ?></div>
                <div class="hotel-room-badge"><span class="star"><?= o5e(o5_stars(o5nv($h['rating'], ''))) ?></span> <?= o5e($room_label) ?></div>
                <div class="hotel-dates">
                  <div class="hotel-date-item">
                    <div class="dlbl">Check-In</div>
                    <div class="dval"><?= o5e(o5nv($h['check_in'], '')) ?></div>
                  </div>
                  <div class="hotel-date-arrow">→</div>
                  <div class="hotel-date-item">
                    <div class="dlbl">Check-Out</div>
                    <div class="dval"><?= o5e(o5nv($h['check_out'], '')) ?></div>
                  </div>
                </div>
                <div class="hotel-amenities">
                  <?php if (!empty($h['meal_plan'])) : ?>
                    <div class="hotel-amenity"><span class="a-icon">🍽️</span> <?= o5e($h['meal_plan']) ?></div>
                  <?php endif; ?>
                  <?php if (!empty($h['rating'])) : ?>
                    <div class="hotel-amenity"><span class="a-icon">⭐</span> <?= o5e($h['rating']) ?> Star</div>
                  <?php endif; ?>
                  <div class="hotel-amenity"><span class="a-icon">📶</span> WiFi</div>
                  <div class="hotel-amenity"><span class="a-icon">🛎️</span> Room Service</div>
                </div>
              </div>
            </div>
          </div>
        <?php
          endforeach;
        endforeach;
      else :
        ?>
        <div class="hotel-card">
          <div class="hotel-inner">
            <div class="hotel-body" style="padding:24px;">
              <div class="hotel-name">Hotel details will be confirmed with your booking.</div>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <div class="all-hotels-box">
        <div class="ah-title">All Hotels Include</div>
        <div class="all-hotels-grid">
          <div class="all-hotels-item"><span class="ai">📶</span> Free WiFi</div>
          <div class="all-hotels-item"><span class="ai">🌊</span> Swimming Pool</div>
          <div class="all-hotels-item"><span class="ai">☕</span> 24/7 Room Service</div>
          <div class="all-hotels-item"><span class="ai">💪</span> Fitness Center</div>
          <div class="all-hotels-item"><span class="ai">📺</span> Smart TV</div>
          <div class="all-hotels-item"><span class="ai">🏢</span> Concierge</div>
          <div class="all-hotels-item"><span class="ai">🔒</span> In-Room Safe</div>
          <div class="all-hotels-item"><span class="ai">🍽️</span> Multi-Cuisine Restaurant</div>
        </div>
      </div>
    </div>

    <hr class="page-divider" />

    <div class="pdf-page">
      <?php
      $o5_show_flights = !empty($flights);
      $o5_show_vehs = !empty($vehs);
      $o5_show_trains = !empty($trains);
      $o5_show_cruises = !empty($cruises);
      if ($o5_show_flights || $o5_show_vehs || $o5_show_trains || $o5_show_cruises) {
        o5_render_page_header_strip($hero, $o5_show_flights ? 'Flight Details' : ($o5_show_trains ? 'Train Details' : ($o5_show_cruises ? 'Cruise Details' : 'Transportation')));
      }
      ?>
      <!-- FLIGHTS & TRANSPORT -->
      <?php if ($o5_show_flights || $o5_show_vehs || $o5_show_trains || $o5_show_cruises) : ?>
        <div class="page-section">
          <?php if ($o5_show_flights) : ?>
            <div class="sec-head">
              <h2>Flight Details</h2>
            </div>
            <?php foreach ($flights as $f) :
              $air_name = o5nv($f['airline_name'], o5nv($f['airline_display'], 'Flight'));
              $flight_lbl = o5nv($f['airline_code'], o5nv($f['airline_display'], ''));
              list($dep_time, $dep_date) = o5_flight_parts(o5nv($f['departure_datetime'], ''));
              list($arr_time, $arr_date) = o5_flight_parts(o5nv($f['arrival_datetime'], ''));
              $from_code = o5_air_code(o5nv($f['from_city'], ''));
              $to_code = o5_air_code(o5nv($f['to_city'], ''));
            ?>
              <div class="flight-card">
                <div class="flight-header">
                  <div class="flight-header-left">
                    <div class="flight-icon-box">✈</div>
                    <div>
                      <span class="flight-airline"><?= o5e($air_name) ?></span>
                      <?php if ($flight_lbl !== '') : ?><span class="flight-num-text"><?= o5e($flight_lbl) ?></span><?php endif; ?>
                    </div>
                  </div>
                  <div class="flight-class-badge"><?= o5e(o5nv($f['class'], 'Economy')) ?></div>
                </div>
                <div class="flight-body">
                  <div class="flight-route-row">
                    <div>
                      <div class="flight-time"><?= o5e($dep_time) ?></div>
                      <div class="flight-date"><?= o5e($dep_date) ?></div>
                      <div class="flight-city"><?= o5e(o5nv($f['from_city'], '')) ?> (<?= o5e($from_code) ?>)</div>
                    </div>
                    <div class="flight-middle">
                      <div class="flight-dur"><?= o5e(o5nv($f['duration'], 'Direct')) ?></div>
                      <div class="flight-line">
                        <div class="fl"></div>
                        <div class="fc">⏰</div>
                        <div class="fl"></div>
                      </div>
                    </div>
                    <div style="text-align:right;">
                      <div class="flight-time"><?= o5e($arr_time) ?></div>
                      <div class="flight-date"><?= o5e($arr_date) ?></div>
                      <div class="flight-city"><?= o5e(o5nv($f['to_city'], '')) ?> (<?= o5e($to_code) ?>)</div>
                    </div>
                  </div>
                  <div class="flight-footer">
                    <span class="ff-icon">🧳</span>
                    <span class="ff-text">Route:</span>
                    <span class="ff-val"><?= o5e(o5nv($f['from_city'], '')) ?> → <?= o5e(o5nv($f['to_city'], '')) ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php if (!empty($trains)) : ?>
            <div class="sec-head" style="margin-top:24px;">
              <h2>Train Details</h2>
            </div>

            <?php foreach ($trains as $tr) :
              $from_loc = isset($tr['from_location']) ? $tr['from_location'] : '';
              $to_loc = isset($tr['to_location']) ? $tr['to_location'] : '';
              $train_class = isset($tr['class']) ? $tr['class'] : 'NA';
              $from_date = isset($tr['from_date']) ? $tr['from_date'] : '';

              $total_pax = 0;
              if (isset($ov['pax']) && is_array($ov['pax'])) {
                $total_pax =
                  (int)o5nv(isset($ov['pax']['adult']) ? $ov['pax']['adult'] : 0, 0) +
                  (int)o5nv(isset($ov['pax']['children_with_bed']) ? $ov['pax']['children_with_bed'] : 0, 0) +
                  (int)o5nv(isset($ov['pax']['children_without_bed']) ? $ov['pax']['children_without_bed'] : 0, 0) +
                  (int)o5nv(isset($ov['pax']['infant']) ? $ov['pax']['infant'] : 0, 0);
              }
            ?>

              <div class="flight-card">
                <div class="flight-header">
                  <div class="flight-header-left">
                    <div class="flight-icon-box">🚆</div>
                    <div>
                      <span class="flight-airline">Train Journey</span>
                      <span class="flight-num-text"><?= o5e($train_class) ?></span>
                    </div>
                  </div>
                  <div class="flight-class-badge"><?= o5e($train_class) ?></div>
                </div>

                <div class="flight-body">
                  <div class="flight-route-row">
                    <div>
                      <div class="flight-time"><?= o5e(o5_air_code($from_loc)) ?></div>
                      <div class="flight-date"><?= o5e(o5nv($from_date, 'NA')) ?></div>
                      <div class="flight-city"><?= o5e(o5nv($from_loc, 'NA')) ?></div>
                    </div>

                    <div class="flight-middle">
                      <div class="flight-dur">Rail Journey</div>
                      <div class="flight-line">
                        <div class="fl"></div>
                        <div class="fc">🚆</div>
                        <div class="fl"></div>
                      </div>
                    </div>

                    <div style="text-align:right;">
                      <div class="flight-time"><?= o5e(o5_air_code($to_loc)) ?></div>
                      <div class="flight-date"><?= o5e(o5nv($from_date, 'NA')) ?></div>
                      <div class="flight-city"><?= o5e(o5nv($to_loc, 'NA')) ?></div>
                    </div>
                  </div>

                  <div class="flight-footer">
                    <span class="ff-icon">👥</span>
                    <span class="ff-text">Total Pax:</span>
                    <span class="ff-val"><?= o5e($total_pax) ?></span>
                  </div>
                </div>
              </div>

            <?php endforeach; ?>
          <?php endif; ?>

          <?php if (!empty($cruises)) : ?>
            <div class="sec-head" style="margin-top:24px;">
              <h2>Cruise Details</h2>
            </div>
            <?php foreach ($cruises as $cr) :
              $route = isset($cr['route']) ? $cr['route'] : '';
              $cabin = isset($cr['cabin']) ? $cr['cabin'] : '';
              $share = isset($cr['sharing_type']) ? $cr['sharing_type'] : '';
              $from_date = isset($cr['from_date']) ? $cr['from_date'] : '';
              $to_date = isset($cr['to_date']) ? $cr['to_date'] : '';
            ?>
              <div class="flight-card">
                <div class="flight-header">
                  <div class="flight-header-left">
                    <div class="flight-icon-box">🚢</div>
                    <div>
                      <span class="flight-airline"><?= o5e(o5nv($route, 'Cruise')) ?></span>
                    </div>
                  </div>
                  <div class="flight-class-badge"><?= o5e(o5nv($cabin, 'Cabin')) ?></div>
                </div>
                <div class="flight-body">
                  <div class="flight-footer">
                    <span class="ff-text">Dep:</span>
                    <span class="ff-val"><?= o5e(o5nv($from_date, 'NA')) ?></span>
                    <span class="ff-text">Arr:</span>
                    <span class="ff-val"><?= o5e(o5nv($to_date, 'NA')) ?></span>
                    <span class="ff-text">Sharing:</span>
                    <span class="ff-val"><?= o5e(o5nv($share, 'NA')) ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>


          <?php if (!empty($acts)) : ?>
            <div class="sec-head" style="margin-top:24px;">
              <h2>Activity Details</h2>
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
                  (int)o5nv(isset($a['pax']['adult']) ? $a['pax']['adult'] : 0, 0) +
                  (int)o5nv(isset($a['pax']['chwb']) ? $a['pax']['chwb'] : 0, 0) +
                  (int)o5nv(isset($a['pax']['chwob']) ? $a['pax']['chwob'] : 0, 0) +
                  (int)o5nv(isset($a['pax']['infant']) ? $a['pax']['infant'] : 0, 0);
              }
            ?>

              <div class="hotel-card">
                <div class="hotel-inner">
                  <div class="hotel-img">
                    <img src="<?= o5e($activity_img) ?>" alt="<?= o5e(o5nv($activity_name, 'Activity')) ?>" />
                  </div>

                  <div class="hotel-body">
                    <div class="hotel-loc">📍 <?= o5e(o5nv($city_name, 'NA')) ?></div>
                    <div class="hotel-name"><?= o5e(o5nv($activity_name, 'Activity')) ?></div>

                    <div class="hotel-dates">
                      <div class="hotel-date-item">
                        <div class="dlbl">Date</div>
                        <div class="dval"><?= o5e(o5nv($activity_date, 'NA')) ?></div>
                      </div>

                      <div class="hotel-date-arrow">→</div>

                      <div class="hotel-date-item">
                        <div class="dlbl">Transfer Type</div>
                        <div class="dval"><?= o5e(o5nv($transfer_type, 'NA')) ?></div>
                      </div>
                    </div>

                    <div class="hotel-amenities">
                      <div class="hotel-amenity"><span class="a-icon">👥</span> <?= o5e($total_pax) ?> Pax</div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
    <hr class="page-divider" />

    <div class="pdf-page">
      <?php if ($o5_show_vehs) : ?>
        <?php o5_render_page_header_strip($hero, 'Transportation'); ?>

        <div class="page-section  print-section">
          <div class=" sec-head" style="margin-top:8px;">
            <h2>Transportation</h2>
          </div>
          <div class="transport-grid">
            <?php foreach ($vehs as $v) :
              $v_start = o5nv($v['date'], '');
              $v_end = o5_vehicle_end_date($v);
              $v_dur = trim($v_start . ($v_end !== '' ? ' – ' . $v_end : ''));
              $svc = o5nv($v['service_duration'], o5nv($v['description'], 'Private Transfer'));
            ?>
              <div class="transport-card">
                <div class="transport-card-head">
                  <div class="transport-icon">🚗</div>
                  <div>
                    <div class="transport-name"><?= o5e(o5nv($v['vehicle_name'], 'Vehicle')) ?><?php if (!empty($v['vehicle_count'])) : ?> (<?= o5e($v['vehicle_count']) ?>)<?php endif; ?></div>
                    <div class="transport-type"><?= o5e(o5nv($v['vehicle_type'], 'Private Transfer')) ?></div>
                  </div>
                </div>
                <div class="transport-row"><span class="tr-lbl">Pickup</span><span class="tr-val"><?= o5e(o5nv($v['pickup'], 'NA')) ?></span></div>
                <div class="transport-row"><span class="tr-lbl">Drop</span><span class="tr-val"><?= o5e(o5nv($v['drop'], 'NA')) ?></span></div>
                <div class="transport-row"><span class="tr-lbl">Duration</span><span class="tr-val"><?= o5e($v_dur !== '' ? $v_dur : 'As per itinerary') ?></span></div>
                <div class="transport-row"><span class="tr-lbl">Service</span><span class="tr-val"><span class="tr-badge2"><?= o5e($svc) ?></span></span></div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        </div>
        <hr class="page-divider" />

        <div class="pdf-page">
          <?php o5_render_page_header_strip($hero, 'Day Wise Itinerary'); ?>

          <!-- ITINERARY -->
          <div class="page-section print-section">
            <div class="sec-head">
              <h2>Day Wise Itinerary</h2>
            </div>
            <div class="itin-timeline">
              <?php if (!empty($itin)) :
                foreach ($itin as $day) :
                  // $day_img = o5img(isset($day['image']) ? $day['image'] : '', $assets . 'day-' . ((($day['day_number'] - 1) % 6) + 1) . '.jpg');
                  $dummy_day_img = BASE_URL . 'images/itinerary.png';

                  $o5_day_photo = isset($day['image']) ? trim($day['image']) : '';

                  if ($o5_day_photo == '' || stripos($o5_day_photo, 'dummy') !== false) {
                    $day_img = $dummy_day_img;
                  } else {
                    $day_img = o5img($o5_day_photo, $dummy_day_img);
                  }
                  $day_date = o5nv($day['date'], '');
                  $day_title = o5nv($day['special_attraction'], o5nv($day['city'], 'Sightseeing'));
              ?>
                  <div class="itin-item">
                    <div class="itin-dot"></div>
                    <div class="itin-card">
                      <div class="itin-img-top">
                        <img src="<?= o5e($day_img) ?>" alt="<?= o5e($day_title) ?>" />
                        <?php if ($day_date !== '') : ?>
                          <div class="itin-date-badge"><?= o5e($day_date) ?></div>
                        <?php endif; ?>
                      </div>
                      <div class="itin-body">
                        <div class="itin-attract-row">
                          <div class="itin-attract-left">
                            <span style="font-size:16px;">🌐</span>
                            <div class="itin-attract-title">Special Attraction · <?= o5e($day_title) ?></div>
                          </div>
                          <div class="itin-day-num-circle"><?= o5e(o5nv($day['day_number'], '')) ?></div>
                        </div>
                        <div class="itin-prog-lbl">Detailed Programme</div>
                        <div class="itin-prog-text"><?= o5e(o5nv($day['detailed_programme'], '')) ?></div>
                        <div class="itin-chips">
                          <?php if (!empty($day['meal_plan'])) : ?>
                            <div class="itin-chip meal">
                              <div class="itin-chip-icon">🍽️</div>
                              <div>
                                <div class="itin-chip-lbl">Meal Plan</div>
                                <div class="itin-chip-val"><?= o5e($day['meal_plan']) ?></div>
                              </div>
                            </div>
                          <?php endif; ?>
                          <?php if (!empty($day['overnight_stay'])) : ?>
                            <div class="itin-chip stay">
                              <div class="itin-chip-icon">🌙</div>
                              <div>
                                <div class="itin-chip-lbl">Overnight Stay</div>
                                <div class="itin-chip-val"><?= o5e($day['overnight_stay']) ?></div>
                              </div>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php
                endforeach;
              else :
                ?>
                <div class="itin-item">
                  <div class="itin-dot"></div>
                  <div class="itin-card">
                    <div class="itin-body">
                      <div class="itin-prog-text">Itinerary details will be shared upon confirmation.</div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <hr class="page-divider" />

        <div class="pdf-page">
          <?php o5_render_page_header_strip($hero, 'Inclusions & Costing'); ?>

          <!-- INCLUSIONS + COSTING -->
          <div class="page-section">
            <div class="inc-exc-grid">
              <div class="inc-card">
                <div class="inc-header"><span style="font-size:18px;">✓</span><span class="ie-header-text">What's Included</span></div>
                <div class="inc-body">
                  <?php foreach ($o5_included as $item) : ?>
                    <div class="ie-item">
                      <div class="ie-check">✓</div>
                      <div class="ie-text"><?= nl2br(o5e($item)) ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="exc-card">
                <div class="exc-header"><span style="font-size:18px;">✗</span><span class="ie-header-text">What's Excluded</span></div>
                <div class="exc-body">
                  <?php foreach ($o5_excluded as $item) : ?>
                    <div class="ie-item">
                      <div class="ie-x">✗</div>
                      <div class="ie-text"><?= nl2br(o5e($item)) ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <?php
            $o5_costing_type = isset($cost['costing_type_label']) ? strtolower(trim($cost['costing_type_label'])) : '';
            $o5_is_per_person = ($o5_costing_type == 'per person');
            $o5_pp_entries = isset($cost['computed']['pp_entries']) ? $cost['computed']['pp_entries'] : array();
            $o5_pp = isset($cost['computed']['per_person']) ? $cost['computed']['per_person'] : array();
            ?>

            <div class="sec-head">
              <h2>Costing Details</h2>
            </div>

            <?php if (!$o5_is_per_person) { ?>

              <table class="costing-table">
                <thead>
                  <tr>
                    <th>Package Type</th>
                    <th>Tour Cost</th>
                    <th>Tax</th>
                    <th>TCS</th>
                    <th>Travel Cost</th>
                    <th>Grand Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($o5_cost_grp as $ci => $row) :
                    $is_rec = (stripos(o5nv($row['package_type'], ''), 'premium') !== false)
                      || (stripos(o5nv($row['package_type'], ''), 'recommended') !== false)
                      || ($ci === 1 && count($o5_cost_grp) > 1);

                    $tax_amount = function_exists('gqd_tax_display_amount') ? gqd_tax_display_amount($row) : (isset($row['tax_amount_display']) ? $row['tax_amount_display'] : '0.00');
                  ?>
                    <tr<?= $is_rec ? ' class="recommended"' : '' ?>>
                      <td>
                        <strong><?= o5e(o5nv($row['package_type'], 'Package')) ?></strong>
                        <?php if ($is_rec) : ?><span class="rec-inline-badge">RECOMMENDED</span><?php endif; ?>
                      </td>
                      <td><?= o5e(o5nv($row['tour_cost_display'], '0')) ?></td>
                      <td><?= o5e($tax_amount) ?></td>
                      <td><?= o5e(o5nv($row['tcs_display'], '0')) ?></td>
                      <td><?= o5e(o5nv($row['travel_display'], '0')) ?></td>
                      <td><?= function_exists('gqd_total_with_before_discount') ? gqd_total_with_before_discount($row, 'total_display', 'before_discount_display', 'o5e') : o5e(o5nv($row['total_display'], '0')) ?></td>
                      </tr>
                    <?php endforeach; ?>
                </tbody>
              </table>

            <?php } else { ?>

              <?php if (!empty($o5_pp_entries)) { ?>
                <?php gqd_render_pp_entries_table($o5_pp_entries, array('escape' => 'o5e', 'table_class' => 'costing-table')); ?>
              <?php } elseif (!empty($o5_pp)) { ?>
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
                    <?php foreach ($o5_pp as $pp) :
                      $tax_amount = function_exists('gqd_tax_display_amount') ? gqd_tax_display_amount($pp) : (isset($pp['tax_amount_display']) ? $pp['tax_amount_display'] : '0.00');
                    ?>
                      <tr>
                        <td><strong><?= o5e(o5nv($pp['package_type'], 'Package')) ?></strong></td>
                        <td><?= o5e(o5nv($pp['pp_adult_display'], 'INR 0.00')) ?></td>
                        <td><?= o5e(o5nv($pp['pp_cwb_display'], 'INR 0.00')) ?></td>
                        <td><?= o5e(o5nv($pp['pp_cwnb_display'], 'INR 0.00')) ?></td>
                        <td><?= o5e(o5nv($pp['pp_infant_display'], 'INR 0.00')) ?></td>
                        <td><?= o5e($tax_amount) ?></td>
                        <td><?= o5e(o5nv($pp['tcs_display'], 'INR 0.00')) ?></td>
                        <td><?= o5e(o5nv($pp['visa_display'], 'INR 0.00')) ?></td>
                        <td><?= o5e(o5nv($pp['guide_display'], 'INR 0.00')) ?></td>
                        <td><?= o5e(o5nv($pp['misc_display'], 'INR 0.00')) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php } ?>

            <?php } ?>

            <div class="costing-note">
              <strong>Note:</strong>
              <?= o5e(o5nv(isset($incx['note']) ? $incx['note'] : '', 'Rates are subject to availability at the time of confirmation.')) ?>
            </div>
          </div>
        </div>

        <hr class="page-divider" />

        <div class="pdf-page">
          <?php o5_render_page_header_strip($hero, 'Payment Information'); ?>

          <!-- PAYMENT -->
          <div class="page-section">
            <div class="sec-head">
              <h2>Payment Information</h2>
            </div>

            <div class="pay-bank-card">
              <div class="pay-bank-header">
                <div class="pay-bank-icon">🏦</div>
                <div>
                  <div class="pay-bank-title">Bank Details</div>
                  <div class="pay-bank-name"><?= o5e(o5nv($bank['bank_name'], 'NA')) ?></div>
                </div>
              </div>
              <div class="pay-bank-body">
                <div class="pay-bank-left">
                  <div class="pay-field">
                    <div class="pf-lbl">Account Name</div>
                    <div class="pf-val"><?= o5e(o5nv($bank['account_name'], 'NA')) ?></div>
                  </div>
                  <div class="pay-field">
                    <div class="pf-lbl">Account Number</div>
                    <div class="pf-val"><?= o5e(o5nv($bank['account_no'], 'NA')) ?></div>
                  </div>
                  <div class="pay-field">
                    <div class="pf-lbl">Branch</div>
                    <div class="pf-val"><?= o5e(o5nv($bank['branch_name'], 'NA')) ?></div>
                  </div>
                  <div class="pay-field">
                    <div class="pf-lbl">IFSC Code</div>
                    <div class="pf-val"><?= o5e(o5nv($bank['ifsc_code'], o5nv($bank['swift_code'], 'NA'))) ?></div>
                  </div>
                </div>
                <div class="pay-bank-right">
                  <div class="qr-box">
                    <?php if (!empty($bank['qr_html'])) : ?>
                      <?= $bank['qr_html'] ?>
                    <?php elseif (!empty($bank['qr_code']) || !empty($bank['branch_qr_url'])) : ?>
                      <img src="<?= o5e(o5nv($bank['branch_qr_url'], $bank['qr_code'])) ?>" alt="Payment QR" style="width:110px;height:110px;object-fit:contain;" />
                    <?php else : ?>
                      <span style="color:var(--muted);font-size:11px;">QR not configured</span>
                    <?php endif; ?>
                  </div>
                  <div class="qr-title">Scan to Pay</div>
                  <?php if (!empty($bank['upi_id'])) : ?>
                    <div class="qr-upi">UPI: <?= o5e($bank['upi_id']) ?></div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <div class="pay-cards-grid">
              <div class="pay-info-card">
                <div class="pay-info-head"><span class="pay-info-icon">💳</span> Payment Instructions</div>
                <ul class="pay-list">
                  <?php foreach ($o5_pay_notes as $note) : ?>
                    <li><span class="chk">✓</span> <?= o5e($note) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <div class="pay-info-card">
                <div class="pay-info-head"><span class="pay-info-icon">🛡️</span> Booking Policy</div>
                <ul class="pay-list">
                  <?php foreach (array_slice($o5_book_policy, 0, 4) as $pol) : ?>
                    <li><span class="chk">✓</span> <?= o5e($pol) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <hr class="page-divider" />

        <div class="pdf-page">
          <?php o5_render_page_header_strip($hero, 'Traveller Reviews'); ?>

          <!-- REVIEWS -->
          <div class="page-section print-section">
            <div class="sec-head">
              <h2>What Our Travellers Say</h2>
            </div>

            <div class="reviews-grid">
              <?php if (!empty($testimonials)) :
                $o5_ti = 0;
                foreach ($testimonials as $t) :
                  if ($o5_ti >= 3) {
                    break;
                  }
                  $o5_ti++;
                  $photo = o5_testi_photo(isset($t['photo']) ? $t['photo'] : '');
              ?>
                  <div class="review-card">
                    <div class="review-quote-icon">❝</div>
                    <div class="review-text">"<?= o5e(o5nv($t['review'], '')) ?>"</div>
                    <div class="review-footer">
                      <?php if ($photo !== '') : ?>
                        <img class="review-avatar" src="<?= o5e($photo) ?>" alt="<?= o5e(o5nv($t['name'], '')) ?>" />
                      <?php else : ?>
                        <div class="review-avatar" style="background:var(--navy);color:var(--gold2);display:flex;align-items:center;justify-content:center;font-weight:700;"><?= o5e(strtoupper(substr(o5nv($t['name'], 'T'), 0, 1))) ?></div>
                      <?php endif; ?>
                      <div>
                        <div class="review-name"><?= o5e(o5nv($t['name'], 'Traveller')) ?></div>
                        <div class="review-loc"><?= o5e(o5nv($t['designation'], '')) ?></div>
                        <div class="review-stars">★★★★★</div>
                      </div>
                    </div>
                  </div>
                <?php
                endforeach;
              else :
                ?>
                <div class="review-card">
                  <div class="review-quote-icon">❝</div>
                  <div class="review-text">Customer testimonials can be managed from Quotation Builder settings.</div>
                </div>
              <?php endif; ?>
            </div>

            <div class="stats-bar">
              <div class="stat-item">
                <div class="stat-num">⭐ <?= o5e($o5_google_rating) ?></div>
                <div class="stat-lbl">Google Rating</div>
              </div>
              <div class="stats-div"></div>
              <div class="stat-item">
                <div class="stat-num"><?= o5e($o5_review_count) ?></div>
                <div class="stat-lbl">Happy Reviews</div>
              </div>
              <div class="stats-div"></div>
              <div class="stat-item">
                <div class="stat-num"><?= o5e($o5_traveller_cnt) ?></div>
                <div class="stat-lbl">Happy Travellers</div>
              </div>
            </div>

            <div class="trust-grid">
              <div class="trust-card">
                <div class="trust-icon">🛡️</div>
                <div class="trust-label">Secure Booking</div>
              </div>
              <div class="trust-card">
                <div class="trust-icon">🏅</div>
                <div class="trust-label">Best Price Guarantee</div>
              </div>
              <div class="trust-card">
                <div class="trust-icon">❤️</div>
                <div class="trust-label">Personalized Service</div>
              </div>
              <div class="trust-card">
                <div class="trust-icon">✨</div>
                <div class="trust-label">Premium Experience</div>
              </div>
            </div>

            <div class="cta-btn">📞 Ready to Book? Call us: <?= o5e(o5nv($ty['company_contact'], o5nv($hero['user_contact'], ''))) ?></div>
          </div>
        </div>

        <hr class="page-divider" />

        <div class="pdf-page">
          <?php o5_render_page_header_strip($hero, 'Terms & Conditions'); ?>

          <!-- TERMS -->
          <div class="page-section print-section">
            <div class="sec-head">
              <h2><?= o5e(o5nv($terms['title'], 'Terms & Conditions')) ?></h2>
            </div>

            <div class="tnc-grid">
              <?php
              $o5_terms_html = isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '';
              $o5_terms_html = str_replace(array("\r", "\n"), '', $o5_terms_html);

              $o5_term_cards = array();

              preg_match_all('/<b[^>]*>(.*?)<\/b>\s*<br\s*\/?>(.*?)(?=<span[^>]*>\s*<br>\s*<b|<div><span[^>]*>\s*<b|<b[^>]*>|$)/is', $o5_terms_html, $matches, PREG_SET_ORDER);

              foreach ($matches as $i => $m) {
                $title = trim(strip_tags($m[1]));
                $title = trim($title, " :\t\n\r\0\x0B");

                $body = trim($m[2]);

                if (preg_match('/<ul[^>]*>(.*?)<\/ul>/is', $body, $ul_match)) {
                  $body = '<ul class="tnc-list">' . $ul_match[1] . '</ul>';
                } else {
                  $body = '<div class="tnc-list-text">' . strip_tags($body, '<br>') . '</div>';
                }

                if ($title != '') {
                  $o5_term_cards[] = array(
                    'num' => $i + 1,
                    'title' => $title,
                    'body' => $body
                  );
                }
              }
              ?>
              <?php if (!empty($o5_term_cards)) :
                foreach ($o5_term_cards as $card) : ?>
                  <div class="tnc-card">
                    <div class="tnc-head">
                      <div class="tnc-num"><?= o5e($card['num']) ?></div>
                      <div class="tnc-title"><?= o5e($card['title']) ?></div>
                    </div>
                    <?= $card['body'] ?>
                  </div>
                <?php endforeach;
              else : ?>
                <div class="tnc-card">
                  <div class="tnc-head">
                    <div class="tnc-num">1</div>
                    <div class="tnc-title">Terms &amp; Conditions</div>
                  </div>
                  <ul class="tnc-list">
                    <li>Terms and conditions will be shared as per company policy.</li>
                  </ul>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- THANK YOU -->
        <div class="ty-page" style="background:linear-gradient(rgba(15,32,68,.72),rgba(15,32,68,.82)),url('<?= o5e($o5_ty_bg) ?>') center/cover no-repeat;">
          <div class="ty-top">
            <div class="ty-logo">
              <?php if ($o5_logo !== '') : ?>
                <img src="<?= o5e($o5_logo) ?>" alt="<?= o5e($o5_company) ?>" class="company-logo-img" />
              <?php else : ?>
                <div class="ty-logo-icon">✈</div>
              <?php endif; ?>
              <!-- <div class="ty-logo-name"><? //= o5e($o5_company) 
                                              ?></div> -->
            </div>
          </div>

          <div class="ty-main">
            <div class="ty-script">Thank You</div>
            <div class="ty-sub">Dear <?= o5e($o5_client) ?>, we truly appreciate your trust in <?= o5e(o5nv($ty['company_name'], $o5_company)) ?>. Our team is committed to crafting an unforgettable travel experience for you.</div>
            <div class="ty-stats-row">
              <div class="ty-stat">
                <div class="ty-stat-num">⭐ <?= o5e($o5_google_rating) ?></div>
                <div class="ty-stat-lbl">Rating</div>
              </div>
              <div class="ty-stat">
                <div class="ty-stat-num"><?= o5e($o5_review_count) ?></div>
                <div class="ty-stat-lbl">Reviews</div>
              </div>
              <div class="ty-stat">
                <div class="ty-stat-num"><?= o5e($o5_traveller_cnt) ?></div>
                <div class="ty-stat-lbl">Travellers</div>
              </div>
            </div>
          </div>

          <div class="ty-contact-row">
            <div class="ty-contact-item">
              <div class="ty-contact-icon">🌐</div>
              <div class="ty-contact-val">
                <a href="<?= o5e(o5nv($ty['website'], '')) ?>">
                  <?= o5e(o5nv($ty['website'], '')) ?>
                </a>
              </div>
            </div>

            <div class="ty-contact-item">
              <div class="ty-contact-icon">📞</div>
              <div class="ty-contact-val">
                <a href="tel:<?= preg_replace('/\s+/', '', o5nv($ty['company_contact'], o5nv($ty['user_mobile'], ''))) ?>">
                  <?= o5e(o5nv($ty['company_contact'], o5nv($ty['user_mobile'], ''))) ?>
                </a>
              </div>
            </div>

            <div class="ty-contact-item">
              <div class="ty-contact-icon">✉️</div>
              <div class="ty-contact-val">
                <a href="mailto:<?= o5e(o5nv($ty['company_email'], '')) ?>">
                  <?= o5e(o5nv($ty['company_email'], '')) ?>
                </a>
              </div>
            </div>
          </div>

          <div class="ty-divider"></div>
          <div class="ty-bottom">
            <div class="ty-address">
              <div class="addr-title">Corporate Office</div>
              <div class="addr-text"><?= o5e(o5nv($ty['company_address'], '')) ?></div>
            </div>
            <div class="ty-prepared">
              <div class="prep-label">Prepared By</div>
              <div class="prep-name"><?= o5e(o5nv($ty['prepared_by'], o5nv($hero['login_user'], 'Team'))) ?></div>
              <div class="prep-role">Travel Consultant</div>
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