<?php

/**
 * OPTION-7 (quotation_html_7) — Package Tour Quotation
 * Layout/CSS from Final-Designs/Option-7/Option-7.html
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
$trains = isset($q['trains']) ? $q['trains'] : array();
$acts   = isset($q['activities']) ? $q['activities'] : array();
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
$o7_cfg       = array();
if (function_exists('gqb_get_config')) {
  $o7_cfg = gqb_get_config();
  $testimonials = isset($o7_cfg['testimonials']) && is_array($o7_cfg['testimonials'])
    ? $o7_cfg['testimonials'] : array();
}

if (!function_exists('o7e')) {
  function o7e($v)
  {
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
  }
}
if (!function_exists('o7nv')) {
  function o7nv($v, $f = '')
  {
    return ($v !== null && $v !== '') ? $v : $f;
  }
}
if (!function_exists('o7_media_url')) {
  function o7_media_url($url)
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
if (!function_exists('o7img')) {
  function o7img($url, $fallback)
  {
    $resolved = o7_media_url($url);
    return $resolved !== '' ? $resolved : $fallback;
  }
}
if (!function_exists('o7_guest_label')) {
  function o7_guest_label($ov)
  {
    $p = isset($ov['pax']) ? $ov['pax'] : array();
    $parts = array();
    $ad = (int) o7nv(isset($p['adult']) ? $p['adult'] : 0, 0);
    $ch = (int) o7nv(isset($p['children_with_bed']) ? $p['children_with_bed'] : 0, 0)
      + (int) o7nv(isset($p['children_without_bed']) ? $p['children_without_bed'] : 0, 0);
    $inf = (int) o7nv(isset($p['infant']) ? $p['infant'] : 0, 0);
    if ($ad) {
      $parts[] = $ad . ' Adult' . ($ad > 1 ? 's' : '');
    }
    if ($ch) {
      $parts[] = $ch . ' Child' . ($ch > 1 ? 'ren' : '');
    }
    if ($inf) {
      $parts[] = $inf . ' Infant' . ($inf > 1 ? 's' : '');
    }
    return $parts ? implode(', ', $parts) : o7nv($ov['guest_count'], '-');
  }
}
if (!function_exists('o7_split_lines')) {
  function o7_split_lines($html, $fallback = array())
  {
    $text = trim(strip_tags(str_replace(array('<br>', '<br/>', '<br />', '</p>', '</li>'), "\n", (string) $html)));
    $items = preg_split('/\r\n|\r|\n|•|\x{2022}/u', $text);
    $items = array_values(array_filter(array_map('trim', (array) $items)));
    return $items ? $items : $fallback;
  }
}
if (!function_exists('o7_air_code')) {
  function o7_air_code($loc)
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
if (!function_exists('o7_flight_parts')) {
  function o7_flight_parts($dt)
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
if (!function_exists('o7_vehicle_end_date')) {
  function o7_vehicle_end_date($v)
  {
    if (!empty($v['end_date_raw']) && function_exists('get_date_user')) {
      return get_date_user($v['end_date_raw']);
    }
    return o7nv(isset($v['end_date_raw']) ? $v['end_date_raw'] : '', o7nv(isset($v['end_date']) ? $v['end_date'] : '', ''));
  }
}
if (!function_exists('o7_initials')) {
  function o7_initials($name)
  {
    $name = trim((string) $name);
    if ($name === '') {
      return 'G';
    }
    $words = preg_split('/\s+/', $name);
    if (count($words) >= 2) {
      return strtoupper(substr($words[0], 0, 1) . substr($words[count($words) - 1], 0, 1));
    }
    return strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 1));
  }
}
if (!function_exists('o7_company_initials')) {
  function o7_company_initials($name)
  {
    $name = trim((string) $name);
    if ($name === '') {
      return 'TP';
    }
    $words = preg_split('/\s+/', $name);
    if (count($words) >= 2) {
      return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    }
    return strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 2));
  }
}
if (!function_exists('o7_cover_images')) {
  function o7_cover_images($hero, $gallery, $hotels, $itin, $assets)
  {
    $imgs = array();
    $cover = o7_media_url(o7nv(isset($hero['cover_image']) ? $hero['cover_image'] : '', ''));
    if ($cover !== '') {
      $imgs[] = $cover;
    }
    if (!empty($hero['destination_5th_gallery_image'])) {
      $g5 = o7_media_url($hero['destination_5th_gallery_image']);
      if ($g5 !== '' && !in_array($g5, $imgs, true)) {
        $imgs[] = $g5;
      }
    }
    foreach ($gallery as $g) {
      if (count($imgs) >= 5) {
        break;
      }
      $p = o7_media_url($g);
      if ($p !== '' && !in_array($p, $imgs, true)) {
        $imgs[] = $p;
      }
    }
    foreach ($hotels as $h) {
      if (count($imgs) >= 5) {
        break;
      }
      $p = o7_media_url(isset($h['hotel_photo']) ? $h['hotel_photo'] : '');
      if ($p !== '' && !in_array($p, $imgs, true)) {
        $imgs[] = $p;
      }
    }
    foreach ($itin as $d) {
      if (count($imgs) >= 5) {
        break;
      }
      $p = o7_media_url(isset($d['image']) ? $d['image'] : '');
      if ($p !== '' && !in_array($p, $imgs, true)) {
        $imgs[] = $p;
      }
    }
    $fallback = array(
      $assets . 'cover-1.jpg',
      $assets . 'cover-2.jpg',
      $assets . 'cover-3.jpg',
      $assets . 'cover-4.jpg',
      $assets . 'cover-5.jpg',
    );
    $fi = 0;
    while (count($imgs) < 5) {
      $imgs[] = $fallback[$fi % 5];
      $fi++;
    }
    return array_slice($imgs, 0, 5);
  }
}
if (!function_exists('o7_render_page_header')) {
  function o7_render_page_header($hero, $section_label, $page_num)
  {
    $logo = o7nv($hero['company_logo'], '');
    $name = o7nv($hero['company_name'], 'Travel Partner');
    $tagline = 'Your Journey, Our Passion';
    $initials = o7_company_initials($name);
    $page_str = sprintf('%02d', (int) $page_num);
?>
    <div class="page-header">
      <div class="ph-logo">
        <?php if ($logo !== '') : ?>
          <img src="<?= o7e($logo) ?>" alt="<?= o7e($name) ?>" class="ph-nh" style="width:44px;height:44px;border-radius:50%;object-fit:contain;background:#fff;padding:4px;border:2px solid rgba(255,255,255,.2);" />
        <?php else : ?>
          <div class="ph-nh"><?= o7e($initials) ?></div>
        <?php endif; ?>
        <div class="ph-brand">
          <div class="ph-name"><?= o7e(strtoupper($name)) ?></div>
          <div class="ph-tag"><?= o7e($tagline) ?></div>
        </div>
      </div>
      <div class="ph-section-label"><?= o7e($section_label) ?></div>
      <div class="page-num" style="position:relative;top:auto;right:auto;"><?= o7e($page_str) ?></div>
    </div>
<?php
  }
}

$o7_dest        = o7nv($ov['destination'], o7nv($hero['tour_name'], 'Tour'));
$o7_client      = o7nv($ov['client_name'], o7nv($hero['client_name'], 'Guest'));
$o7_company     = o7nv($hero['company_name'], 'Travel Partner');
$o7_logo        = o7nv($hero['company_logo'], '');
$o7_initials    = o7_company_initials($o7_company);
$o7_cover_imgs  = o7_cover_images($hero, $gallery, $hotels, $itin, $assets);
$o7_cover_bg    = o7img(o7nv($hero['cover_image'], ''), !empty($o7_cover_imgs[0]) ? $o7_cover_imgs[0] : $assets . 'cover-1.jpg');
$o7_banner_img  = o7img(o7nv($hero['cover_image'], ''), !empty($gallery[0]) ? o7_media_url($gallery[0]) : $o7_cover_bg);
$o7_tour_id     = o7nv($hero['package_code'], o7nv($ov['tour_id'], ''));
$o7_duration    = o7nv($ov['duration_label'], o7nv($hero['duration_label'], ''));
$o7_travel_range = trim(o7nv($ov['travel_from'], '') . (o7nv($ov['travel_to'], '') !== '' ? ' to ' . o7nv($ov['travel_to'], '') : ''));
$o7_pkg_badge   = '';
if (!empty($cost['computed']['group'][0]['package_type'])) {
  $o7_pkg_badge = $cost['computed']['group'][0]['package_type'];
} elseif (!empty($hotels[0]['package_type'])) {
  $o7_pkg_badge = $hotels[0]['package_type'];
}
$o7_pkg_ov = o7nv($o7_pkg_badge, o7nv($ov['package_type_label'], 'Package'));
$o7_included = o7_split_lines(isset($incx['included']) ? $incx['included'] : '', array('Inclusions as per itinerary.'));
$o7_excluded = o7_split_lines(isset($incx['excluded']) ? $incx['excluded'] : '', array('Exclusions as per company policy.'));
$o7_cost_grp = isset($cost['computed']['group']) ? $cost['computed']['group'] : array();
if (empty($o7_cost_grp)) {
  $o7_cost_grp = array(array(
    'package_type'      => 'Package',
    'tour_cost_display' => '0',
    'tax_display'       => '0',
    'tcs_display'       => '0',
    'travel_display'    => '0',
    'total_display'     => '0',
  ));
}
$o7_pay_notes = o7_split_lines(
  o7nv(isset($incx['quot_note']) ? $incx['quot_note'] : '', ''),
  array(
    '30% advance payment required to confirm booking.',
    'Remaining balance before departure as per company policy.',
    'Payment can be made via NEFT/RTGS/IMPS/UPI.',
    'Kindly share payment screenshot after transfer.',
  )
);
$o7_book_policy = o7_split_lines(
  o7nv(isset($incx['note']) ? $incx['note'] : '', ''),
  array(
    'Mention Quotation ID in payment reference.',
    'Booking is confirmed only after payment realization.',
    'Rates are subject to availability at the time of confirmation.',
    'Valid passport with 6 months validity required where applicable.',
  )
);
$o7_term_lines = o7_split_lines(isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '', array());
$o7_tnc_meta = array(
  array('icon' => '📋', 'title' => 'Booking Policy'),
  array('icon' => '🏨', 'title' => 'Hotel Policy'),
  array('icon' => '❌', 'title' => 'Cancellation Policy'),
  array('icon' => '✈️', 'title' => 'Flight Policy'),
  array('icon' => '💰', 'title' => 'Refund Policy'),
  array('icon' => '🌪️', 'title' => 'Force Majeure'),
  array('icon' => '🪪', 'title' => 'Visa Disclaimer'),
  array('icon' => '🛡️', 'title' => 'Travel Insurance'),
);
$o7_tnc_cards = array();
if (!empty($o7_term_lines)) {
  foreach ($o7_term_lines as $ti => $line) {
    $meta = $o7_tnc_meta[$ti % count($o7_tnc_meta)];
    $o7_tnc_cards[] = array(
      'icon'  => $meta['icon'],
      'title' => $meta['title'],
      'text'  => $line,
    );
  }
} else {
  $o7_tnc_cards = array(
    array('icon' => '📋', 'title' => 'Booking Policy', 'text' => 'Booking is confirmed only after receipt of advance payment.'),
    array('icon' => '❌', 'title' => 'Cancellation Policy', 'text' => 'Cancellation charges will be applicable as per airline and hotel policy.'),
    array('icon' => '🛡️', 'title' => 'Travel Insurance', 'text' => 'We strongly recommend comprehensive travel insurance for unforeseen circumstances.'),
  );
}
$o7_google_rating = o7nv(isset($o7_cfg['google_rating']) ? $o7_cfg['google_rating'] : '', '4.9');
$o7_review_count  = o7nv(isset($o7_cfg['review_count']) ? $o7_cfg['review_count'] : '', '2,500+');
$o7_traveller_cnt = o7nv(isset($o7_cfg['traveller_count']) ? $o7_cfg['traveller_count'] : '', '50,000+');
$o7_salutation = $o7_client;
if (preg_match('/^(\S+)/', $o7_client, $o7_sal_m)) {
  $o7_salutation = $o7_sal_m[1];
}
$o7_brand_parts = preg_split('/\s+/', trim($o7_company), 2);
$o7_brand_line1 = isset($o7_brand_parts[0]) ? strtoupper($o7_brand_parts[0]) : 'TRAVEL';
$o7_brand_line2 = isset($o7_brand_parts[1]) ? strtoupper($o7_brand_parts[1]) : 'PARTNER';
$o7_show_flights = !empty($present['flights']) && !empty($flights);
$o7_show_vehs = !empty($present['vehicles']) && !empty($vehs);
$o7_transport_img = o7img('', !empty($o7_cover_imgs[2]) ? $o7_cover_imgs[2] : $o7_cover_bg);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= o7e($o7_dest) ?> Tour Package – <?= o7e($o7_company) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400;1,700&family=Cinzel:wght@400;600;700&family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet" />
  <link href="option7.css" rel="stylesheet" />
  <style>
    .cover::before {
      background-image: url('<?= o7e($o7_cover_bg) ?>');
    }

    .thankyou-section::before {
      background-image: url('<?= o7e($o7_cover_bg) ?>');
    }

    .company-logo-img {
      max-height: 56px;
      max-width: 56px;
      object-fit: contain;
    }
  </style>
</head>

<body>
  <div class="doc">

    <!-- PAGE 1 – COVER -->
    <div class="cover">
      <div class="cover-inner">
        <div class="cover-topbar">
          <div class="cover-logo">
            <?php if ($o7_logo !== '') : ?>
              <img src="<?= o7e($o7_logo) ?>" alt="<?= o7e($o7_company) ?>" class="company-logo-img cover-nh" style="border-radius:50%;border:2px solid rgba(255,255,255,.3);background:#fff;padding:4px;" />
            <?php else : ?>
              <div class="cover-nh"><?= o7e($o7_initials) ?></div>
            <?php endif; ?>
            <div class="cover-brand">
              <div class="brand-name"><?= o7e($o7_brand_line1) ?><?php if ($o7_brand_line2 !== '') : ?><br /><?= o7e($o7_brand_line2) ?><?php endif; ?></div>
              <div class="brand-tag">Your Journey, Our Passion</div>
            </div>
          </div>
          <div class="cover-page-num">01</div>
        </div>
      </div>

      <div class="cover-sidebar">
        <div class="cover-side-item">
          <div class="cover-side-icon">🏨</div>
          <div class="cover-side-text">Luxury<br />Hotels</div>
        </div>
        <div class="cover-side-item">
          <div class="cover-side-icon">🎯</div>
          <div class="cover-side-text">Exciting<br />Activities</div>
        </div>
        <?php if ($o7_show_flights) : ?>
          <div class="cover-side-item">
            <div class="cover-side-icon">✈️</div>
            <div class="cover-side-text">Flights</div>
          </div>
        <?php endif; ?>
        <?php if ($o7_show_vehs) : ?>
          <div class="cover-side-item">
            <div class="cover-side-icon">🚗</div>
            <div class="cover-side-text">Private<br />Transfers</div>
          </div>
        <?php endif; ?>
        <div class="cover-side-item">
          <div class="cover-side-icon">📸</div>
          <div class="cover-side-text">Sightseeing</div>
        </div>
        <div class="cover-side-item">
          <div class="cover-side-icon">🍽️</div>
          <div class="cover-side-text">Delicious<br />Meals</div>
        </div>
      </div>

      <div class="cover-center">
        <span class="cover-dubai-icon">🏙️</span>
        <div class="cover-dubai"><?= o7e($o7_dest) ?></div>
        <div class="cover-pkg">TOUR PACKAGE</div>
        <div class="cover-discover">DISCOVER EXTRAORDINARY EXPERIENCES</div>
      </div>

      <div class="cover-grid" style="height:260px; margin-top: 130px;">
        <?php foreach ($o7_cover_imgs as $ci => $cimg) : ?>
          <div class="cover-grid-item"><img src="<?= o7e($cimg) ?>" alt="<?= o7e($o7_dest) ?> <?= o7e($ci + 1) ?>" /></div>
        <?php endforeach; ?>
      </div>

      <div class="cover-prep" style="margin-top: 20px;">
        <div class="cover-prep-label">Prepared Exclusively For:</div>
        <div class="cover-prep-name"><?= o7e($o7_client) ?></div>
      </div>
    </div>

    <hr class="pg-sep" />

    <!-- PAGE 2 – OVERVIEW -->
    <?php o7_render_page_header($hero, 'Tour Overview', 2); ?>

    <div class="overview-page">
      <div class="overview-inner">
        <div class="personal-banner">
          <p>A Personalized Travel Experience<br />Exclusively Designed for <?= o7e($o7_client) ?></p>
        </div>

        <div class="overview-top">
          <div class="salutation">
            <div class="dear">Dear <?= o7e($o7_salutation) ?>,</div>
            <p>Thank you for choosing <strong><?= o7e($o7_company) ?></strong> for your upcoming journey to <?= o7e($o7_dest) ?>. We are delighted to present this carefully crafted travel proposal designed to offer memorable experiences and exceptional hospitality.</p>
            <p>We look forward to creating unforgettable memories for you!</p>
            <div class="deco">〜 ❧ 〜</div>
          </div>
          <div class="watercolor-img">
            <img src="<?= o7e($o7_banner_img) ?>" alt="<?= o7e($o7_dest) ?>" style="border:2px solid var(--border);" />
          </div>
        </div>

        <div class="ov-title-row">
          <div class="ov-title-line"></div>
          <div class="ov-title"><span class="ornament">❧</span> TOUR OVERVIEW <span class="ornament">❧</span></div>
          <div class="ov-title-line"></div>
        </div>

        <div class="ov-grid">
          <div class="ov-item">
            <div class="ov-icon">📋</div>
            <div class="ov-text">
              <div class="ov-lbl">Quotation ID</div>
              <div class="ov-val"><?= o7e(o7nv($hero['quotation_code'], '')) ?></div>
            </div>
          </div>
          <div class="ov-item">
            <div class="ov-icon">📅</div>
            <div class="ov-text">
              <div class="ov-lbl">Travel Date</div>
              <div class="ov-val"><?= o7e($o7_travel_range) ?></div>
            </div>
          </div>
          <div class="ov-item">
            <div class="ov-icon">🏷️</div>
            <div class="ov-text">
              <div class="ov-lbl">Tour ID</div>
              <div class="ov-val"><?= o7e($o7_tour_id) ?></div>
            </div>
          </div>
          <div class="ov-item">
            <div class="ov-icon">🌙</div>
            <div class="ov-text">
              <div class="ov-lbl">Duration</div>
              <div class="ov-val"><?= o7e(strtoupper($o7_duration)) ?></div>
            </div>
          </div>
          <div class="ov-item">
            <div class="ov-icon">📆</div>
            <div class="ov-text">
              <div class="ov-lbl">Quotation Date</div>
              <div class="ov-val"><?= o7e(o7nv($ov['quotation_date'], '')) ?></div>
            </div>
          </div>
          <div class="ov-item">
            <div class="ov-icon">👥</div>
            <div class="ov-text">
              <div class="ov-lbl">Total Guests</div>
              <div class="ov-val"><?= o7e(o7_guest_label($ov)) ?></div>
            </div>
          </div>
        </div>

        <div class="ov-grid" style="grid-template-columns:1fr;">
          <div class="ov-item">
            <div class="ov-icon">👑</div>
            <div class="ov-text">
              <div class="ov-lbl">Package Type</div>
              <div class="ov-val"><?= o7e(strtoupper($o7_pkg_ov)) ?> PACKAGE</div>
            </div>
          </div>
        </div>

        <div class="prep-card" style="margin-top:8px;">
          <div class="prep-card-label">Prepared For</div>
          <div class="prep-row"><span class="prep-row-icon">👤</span> <?= o7e($o7_client) ?></div>
          <div class="prep-row">
            <span class="prep-row-icon">✉️</span>
            <a href="mailto:<?= o7e(o7nv($ov['customer_email'], o7nv($hero['user_email_id'], ''))) ?>">
              <?= o7e(o7nv($ov['customer_email'], o7nv($hero['user_email_id'], ''))) ?>
            </a>
          </div>

          <div class="prep-row">
            <span class="prep-row-icon">📞</span>
            <a href="tel:<?= preg_replace('/\s+/', '', o7nv($ov['customer_mobile'], o7nv($hero['user_contact'], ''))) ?>">
              <?= o7e(o7nv($ov['customer_mobile'], o7nv($hero['user_contact'], ''))) ?>
            </a>
          </div>
        </div>
      </div>

      <div class="exp-strip">
        <div class="exp-strip-text">EXPERIENCE. COMFORT. MEMORIES.</div>
      </div>
    </div>

    <hr class="pg-sep" />

    <!-- PAGE 3 – ACCOMMODATION -->
    <?php o7_render_page_header($hero, 'Accommodation Details', 3); ?>

    <div class="gold-section-header">
      <h2>Accommodation Details</h2>
      <div class="gold-ornament">
        <div class="go-line"></div>
        <span class="go-icon">👑</span>
        <span style="font-family:'Cinzel',serif;font-size:11px;color:var(--gold2);letter-spacing:3px;">PACKAGE TYPE – <?= o7e(strtoupper($o7_pkg_ov)) ?></span>
        <span class="go-icon">👑</span>
        <div class="go-line"></div>
      </div>
    </div>

    <div class="accom-page">
      <div class="accom-inner" style="padding-top:20px;">
        <?php
        $o7_hi = 0;
        if (!empty($hotels)) :
          foreach ($hotels as $h) :
            $o7_hi++;
            // $hphoto = o7img(isset($h['hotel_photo']) ? $h['hotel_photo'] : '', !empty($o7_cover_imgs[($o7_hi - 1) % 5]) ? $o7_cover_imgs[($o7_hi - 1) % 5] : $assets . 'hotel.jpg');
            $dummy_hotel_img = BASE_URL . 'uploads/quotation_images/hotel.png';

            $o7_hotel_photo = isset($h['hotel_photo']) ? trim($h['hotel_photo']) : '';

            if ($o7_hotel_photo == '' || stripos($o7_hotel_photo, 'dummy') !== false) {
              $hphoto = $dummy_hotel_img;
            } else {
              $hphoto = o7img($o7_hotel_photo, $dummy_hotel_img);
            }
            $room_label = o7nv($h['room_category'], o7nv($h['room_type'], 'Standard Room'));
        ?>
            <div class="hotel-card">
              <div class="hotel-inner">
                <div class="hotel-img"><img src="<?= o7e($hphoto) ?>" alt="<?= o7e(o7nv($h['hotel_name'], 'Hotel')) ?>" /></div>
                <div class="hotel-body">
                  <div class="hotel-head">
                    <div class="hotel-crown">♛</div>
                    <div>
                      <div class="hotel-name"><?= o7e(strtoupper(o7nv($h['hotel_name'], 'Hotel'))) ?></div>
                      <div class="hotel-loc"><?= o7e(o7nv($h['hotel_city'], '')) ?></div>
                    </div>
                  </div>
                  <div class="hotel-detail-row">
                    <span class="hotel-detail-icon">🏛️</span>
                    <div>
                      <div class="hotel-detail-lbl">Room Category</div>
                      <div class="hotel-detail-val"><?= o7e($room_label) ?></div>
                    </div>
                  </div>
                  <div class="hotel-dates">
                    <div class="hotel-date-col">
                      <div class="hotel-date-lbl">Check-In</div>
                      <div class="hotel-date-val"><?= o7e(o7nv($h['check_in'], '')) ?></div>
                    </div>
                    <div class="hotel-date-col">
                      <div class="hotel-date-lbl">Check-Out</div>
                      <div class="hotel-date-val"><?= o7e(o7nv($h['check_out'], '')) ?></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="hotel-amenities">
                <?php if (!empty($h['meal_plan'])) : ?>
                  <div class="hotel-amenity"><span class="ha-icon">🍳</span> <?= o7e($h['meal_plan']) ?></div>
                <?php endif; ?>
                <?php if (!empty($h['rating'])) : ?>
                  <div class="hotel-amenity"><span class="ha-icon">⭐</span> <?= o7e($h['rating']) ?> Star</div>
                <?php endif; ?>
              </div>
            </div>
          <?php
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

        <div class="hotel-note">
          <div class="hotel-note-icon">🛎️</div>
          All hotels are handpicked to ensure comfort, luxury &amp; memorable stay.
        </div>
      </div>
    </div>

    <hr class="pg-sep" />

    <!-- PAGE 4 – JOURNEY -->
    <?php o7_render_page_header($hero, 'Journey Details', 4); ?>

    <div class="gold-section-header">
      <h2>Journey Details</h2>
    </div>

    <div class="journey-page">
      <div class="journey-inner">

        <!-- ========== Flight Details -->
        <?php if ($o7_show_flights) : ?>
          <div class="journey-section-head">
            <span class="jsh-icon">✈️</span>
            <span class="jsh-title">Flight Details</span>
          </div>
          <?php foreach ($flights as $f) :
            $air_name = o7nv($f['airline_name'], o7nv($f['airline_display'], 'Flight'));
            $air_words = preg_split('/\s+/', trim($air_name));
            $air_html = o7e(isset($air_words[0]) ? $air_words[0] : $air_name);
            if (isset($air_words[1])) {
              $air_html .= '<br/>' . o7e(implode(' ', array_slice($air_words, 1)));
            }
            $flight_lbl = o7nv($f['airline_code'], o7nv($f['airline_display'], ''));
            list($dep_time, $dep_date) = o7_flight_parts(o7nv($f['departure_datetime'], ''));
            list($arr_time, $arr_date) = o7_flight_parts(o7nv($f['arrival_datetime'], ''));
            $from_code = o7_air_code(o7nv($f['from_city'], ''));
            $to_code = o7_air_code(o7nv($f['to_city'], ''));
          ?>
            <div class="flight-ticket">
              <div class="ft-inner">
                <div class="ft-left-strip"></div>
                <div class="ft-airline-col">
                  <div class="ft-airline-logo"><?= $air_html ?></div>
                  <div class="ft-airline-lines"></div>
                  <div class="ft-class-badge"><?= o7e(o7nv($f['class'], 'Economy')) ?></div>
                </div>
                <div class="ft-route">
                  <div class="ft-route-row">
                    <div>
                      <div class="ft-code"><?= o7e($from_code) ?></div>
                      <div class="ft-city"><?= o7e(o7nv($f['from_city'], '')) ?></div>
                    </div>
                    <div class="ft-mid">
                      <div class="ft-mid-line"></div>
                      <div class="ft-mid-arrow">✈</div>
                      <div class="ft-mid-line"></div>
                    </div>
                    <div style="text-align:right;">
                      <div class="ft-code"><?= o7e($to_code) ?></div>
                      <div class="ft-city"><?= o7e(o7nv($f['to_city'], '')) ?></div>
                    </div>
                  </div>
                  <div class="ft-details-row">
                    <div class="ft-detail">
                      <div class="fdl">Duration</div>
                      <div class="fdv"><?= o7e(o7nv($f['duration'], 'Direct')) ?></div>
                    </div>
                    <div class="ft-detail">
                      <div class="fdl">Baggage</div>
                      <div class="fdv"><?= o7e(o7nv($f['baggage'], '30 KG')) ?></div>
                    </div>
                  </div>
                </div>
                <div class="ft-right-col">
                  <?php if ($flight_lbl !== '') : ?>
                    <div class="ft-right-item">
                      <div class="ft-right-lbl">Flight</div>
                      <div class="ft-right-val"><?= o7e($flight_lbl) ?></div>
                    </div>
                  <?php endif; ?>
                  <div class="ft-right-item">
                    <div class="ft-right-lbl">Departure</div>
                    <div class="ft-right-val"><?= o7e($dep_date) ?><br /><span class="ft-right-sub"><?= o7e($dep_time) ?></span></div>
                  </div>
                  <div class="ft-right-item">
                    <div class="ft-right-lbl">Arrival</div>
                    <div class="ft-right-val"><?= o7e($arr_date) ?><br /><span class="ft-right-sub"><?= o7e($arr_time) ?></span></div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
        <!-- ============= -->
        <!-- ============= Train Details -->
        <?php if (!empty($trains)) : ?>
          <div class="journey-section-head" style="margin-top:26px;">
            <span class="jsh-icon">🚆</span>
            <span class="jsh-title">Train Details</span>
          </div>

          <?php foreach ($trains as $tr) :
            $from_loc = isset($tr['from_location']) ? $tr['from_location'] : '';
            $to_loc = isset($tr['to_location']) ? $tr['to_location'] : '';
            $train_class = isset($tr['class']) ? $tr['class'] : 'NA';
            $from_date = isset($tr['from_date']) ? $tr['from_date'] : '';

            $total_pax = 0;
            if (isset($ov['pax']) && is_array($ov['pax'])) {
              $total_pax =
                (int)o7nv(isset($ov['pax']['adult']) ? $ov['pax']['adult'] : 0, 0) +
                (int)o7nv(isset($ov['pax']['children_with_bed']) ? $ov['pax']['children_with_bed'] : 0, 0) +
                (int)o7nv(isset($ov['pax']['children_without_bed']) ? $ov['pax']['children_without_bed'] : 0, 0) +
                (int)o7nv(isset($ov['pax']['infant']) ? $ov['pax']['infant'] : 0, 0);
            }
          ?>
            <div class="flight-ticket">
              <div class="ft-inner">
                <div class="ft-left-strip"></div>

                <div class="ft-airline-col">
                  <div class="ft-airline-logo">TRAIN<br />JOURNEY</div>
                  <div class="ft-airline-lines"></div>
                  <div class="ft-class-badge"><?= o7e($train_class) ?></div>
                </div>

                <div class="ft-route">
                  <div class="ft-route-row">
                    <div>
                      <div class="ft-code"><?= o7e(o7_air_code($from_loc)) ?></div>
                      <div class="ft-city"><?= o7e(o7nv($from_loc, 'NA')) ?></div>
                    </div>

                    <div class="ft-mid">
                      <div class="ft-mid-line"></div>
                      <div class="ft-mid-arrow">🚆</div>
                      <div class="ft-mid-line"></div>
                    </div>

                    <div style="text-align:right;">
                      <div class="ft-code"><?= o7e(o7_air_code($to_loc)) ?></div>
                      <div class="ft-city"><?= o7e(o7nv($to_loc, 'NA')) ?></div>
                    </div>
                  </div>

                  <div class="ft-details-row">
                    <div class="ft-detail">
                      <div class="fdl">Date & Time</div>
                      <div class="fdv"><?= o7e(o7nv($from_date, 'NA')) ?></div>
                    </div>
                    <div class="ft-detail">
                      <div class="fdl">Total Pax</div>
                      <div class="fdv"><?= o7e($total_pax) ?></div>
                    </div>
                  </div>
                </div>

                <div class="ft-right-col">
                  <div class="ft-right-item">
                    <div class="ft-right-lbl">Type</div>
                    <div class="ft-right-val">Train</div>
                  </div>
                  <div class="ft-right-item">
                    <div class="ft-right-lbl">Class</div>
                    <div class="ft-right-val"><?= o7e($train_class) ?></div>
                  </div>
                  <div class="ft-right-item">
                    <div class="ft-right-lbl">Pax</div>
                    <div class="ft-right-val"><?= o7e($total_pax) ?></div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
        <!-- ============ -->

        <!-- ============= Activity Details -->
        <?php if (!empty($acts)) : ?>
          <div class="journey-section-head" style="margin-top:26px;">
            <span class="jsh-icon">📸</span>
            <span class="jsh-title">Activity Details</span>
          </div>

          <?php foreach ($acts as $a) :
            $activity_img = BASE_URL . 'uploads/quotation_images/activity.jpg';

            $activity_name = isset($a['activity_name']) ? $a['activity_name'] : '';
            $city_name = isset($a['city_name']) ? $a['city_name'] : '';
            $activity_date = isset($a['date']) ? $a['date'] : '';
            $transfer_type = isset($a['transfer_type']) ? $a['transfer_type'] : '';

            $total_pax = 0;
            if (isset($a['pax']) && is_array($a['pax'])) {
              $total_pax =
                (int)o7nv(isset($a['pax']['adult']) ? $a['pax']['adult'] : 0, 0) +
                (int)o7nv(isset($a['pax']['chwb']) ? $a['pax']['chwb'] : 0, 0) +
                (int)o7nv(isset($a['pax']['chwob']) ? $a['pax']['chwob'] : 0, 0) +
                (int)o7nv(isset($a['pax']['infant']) ? $a['pax']['infant'] : 0, 0);
            }
          ?>
            <div class="flight-ticket">
              <div class="ft-inner">
                <div class="ft-left-strip"></div>

                <div class="ft-airline-col">
                  <img src="<?= o7e($activity_img) ?>" alt="Activity" style="width:90px;height:90px;object-fit:contain;">
                  <div class="ft-airline-lines"></div>
                  <div class="ft-class-badge"><?= o7e(o7nv($transfer_type, 'Activity')) ?></div>
                </div>

                <div class="ft-route">
                  <div class="ft-route-row">
                    <div>
                      <div class="ft-code">ACT</div>
                      <div class="ft-city"><?= o7e(o7nv($activity_name, 'Activity')) ?></div>
                    </div>

                    <div class="ft-mid">
                      <div class="ft-mid-line"></div>
                      <div class="ft-mid-arrow">📸</div>
                      <div class="ft-mid-line"></div>
                    </div>

                    <div style="text-align:right;">
                      <div class="ft-code"><?= o7e(o7_air_code($city_name)) ?></div>
                      <div class="ft-city"><?= o7e(o7nv($city_name, 'NA')) ?></div>
                    </div>
                  </div>

                  <div class="ft-details-row">
                    <div class="ft-detail">
                      <div class="fdl">Date</div>
                      <div class="fdv"><?= o7e(o7nv($activity_date, 'NA')) ?></div>
                    </div>
                    <div class="ft-detail">
                      <div class="fdl">Transfer</div>
                      <div class="fdv"><?= o7e(o7nv($transfer_type, 'NA')) ?></div>
                    </div>
                  </div>
                </div>

                <div class="ft-right-col">
                  <div class="ft-right-item">
                    <div class="ft-right-lbl">Type</div>
                    <div class="ft-right-val">Activity</div>
                  </div>
                  <div class="ft-right-item">
                    <div class="ft-right-lbl">City</div>
                    <div class="ft-right-val"><?= o7e(o7nv($city_name, 'NA')) ?></div>
                  </div>
                  <div class="ft-right-item">
                    <div class="ft-right-lbl">Pax</div>
                    <div class="ft-right-val"><?= o7e($total_pax) ?></div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
        <!-- ============ -->

        <?php if ($o7_show_vehs) : ?>
          <div class="journey-section-head" style="margin-top:20px;">
            <span class="jsh-icon">🚗</span>
            <span class="jsh-title">Transportation Details</span>
          </div>
          <?php foreach ($vehs as $v) :
            $v_start = o7nv($v['date'], '');
            $v_end = o7_vehicle_end_date($v);
            $v_dur = o7nv($v['service_duration'], o7nv($v['description'], 'As per itinerary'));
            $v_name = o7nv($v['vehicle_name'], 'Vehicle');
            if (!empty($v['vehicle_count'])) {
              $v_name .= ' (' . $v['vehicle_count'] . ')';
            }
          ?>
            <div class="transport-card" style="margin-bottom:14px;">
              <div class="transport-inner">
                <div class="transport-img"><img src="<?= o7e(BASE_URL . 'uploads/quotation_images/vehicle.png') ?>"
                    alt="<?= o7e($v_name) ?>" /></div>
                <div class="transport-body">
                  <div class="transport-name"><?= o7e(strtoupper($v_name)) ?></div>
                  <div class="transport-grid">
                    <div class="tg-cell">
                      <div class="tg-lbl">Start Date</div>
                      <div class="tg-val"><?= o7e($v_start !== '' ? $v_start : '—') ?></div>
                    </div>
                    <div class="tg-cell">
                      <div class="tg-lbl">End Date</div>
                      <div class="tg-val"><?= o7e($v_end !== '' ? $v_end : '—') ?></div>
                    </div>
                    <div class="tg-cell">
                      <div class="tg-lbl">Duration</div>
                      <div class="tg-val"><?= o7e($v_dur) ?></div>
                    </div>
                  </div>
                  <div class="transport-row2">
                    <div class="tg-cell" style="border-bottom:none;">
                      <div class="tg-lbl">Pickup</div>
                      <div class="tg-val"><?= o7e(o7nv($v['pickup'], 'NA')) ?></div>
                    </div>
                    <div class="tg-cell" style="border-bottom:none;">
                      <div class="tg-lbl">Drop</div>
                      <div class="tg-val"><?= o7e(o7nv($v['drop'], 'NA')) ?></div>
                    </div>
                    <div class="tg-cell" style="border-bottom:none;">
                      <div class="tg-lbl">Category</div>
                      <div class="tg-val"><?= o7e(o7nv($v['vehicle_type'], 'Private Transfer')) ?></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <div class="journey-section-head" style="margin-top:20px;">
          <span class="jsh-icon">📋</span>
          <span class="jsh-title">Itinerary Overview</span>
        </div>

        <div class="itin-overview">
          <?php if (!empty($itin)) :
            foreach ($itin as $day) :
              // $day_img = o7img(isset($day['image']) ? $day['image'] : '', $o7_cover_bg);
              $dummy_day_img = BASE_URL . 'uploads/quotation_images/day.jpg';

              $o7_day_photo = isset($day['image']) ? trim($day['image']) : '';

              if ($o7_day_photo == '' || stripos($o7_day_photo, 'dummy') !== false) {
                $day_img = $dummy_day_img;
              } else {
                $day_img = o7img($o7_day_photo, $dummy_day_img);
              }
              $day_num = o7nv($day['day_number'], '');
              $day_date = o7nv($day['date'], '');
              $day_place = o7nv($day['special_attraction'], o7nv($day['city'], 'Sightseeing'));
              $day_desc = o7nv($day['detailed_programme'], '');
              if (strlen($day_desc) > 120) {
                $day_desc = substr($day_desc, 0, 117) . '...';
              }
              $day_label = 'Day ' . $day_num;
              if ($day_date !== '' && $day_date !== 'NA') {
                $day_label .= ' | ' . $day_date;
              }
          ?>
              <div class="itin-ov-row">
                <div class="itin-ov-dot"></div>
                <div class="itin-ov-day"><?= o7e($day_label) ?></div>
                <img class="itin-ov-img" src="<?= o7e($day_img) ?>" alt="<?= o7e($day_place) ?>" />
                <div class="itin-ov-place"><?= o7e($day_place) ?></div>
                <div class="itin-ov-desc"><?= o7e($day_desc) ?></div>
              </div>
            <?php
            endforeach;
          else :
            ?>
            <div class="itin-ov-row">
              <div class="itin-ov-dot"></div>
              <div class="itin-ov-day">Itinerary</div>
              <div class="itin-ov-desc">Detailed itinerary will be shared upon confirmation.</div>
            </div>
          <?php endif; ?>
          <div class="itin-note">⭐ Detailed day wise plan with meals &amp; stay included.</div>
        </div>

      </div>
    </div>

    <hr class="pg-sep" />

    <!-- PAGE 5 – INCLUSIONS -->
    <?php o7_render_page_header($hero, 'Inclusions & Exclusions', 5); ?>

    <div class="gold-section-header">
      <h2>Inclusions &amp; Exclusions</h2>
    </div>

    <div class="inc-page">
      <div class="inc-inner">
        <div class="inc-grid">
          <div class="inc-card">
            <div class="inc-card-head">
              <div class="ie-icon-circle green">✓</div>
              <div class="ie-head-title green">What's Included</div>
            </div>
            <div class="ie-body">
              <?php foreach ($o7_included as $item) : ?>
                <div class="ie-item">
                  <div class="ie-bullet green">✓</div>
                  <div class="ie-text"><?= o7e($item) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="exc-card">
            <div class="exc-card-head">
              <div class="ie-icon-circle red">✗</div>
              <div class="ie-head-title red">What's Excluded</div>
            </div>
            <div class="ie-body">
              <?php foreach ($o7_excluded as $item) : ?>
                <div class="ie-item">
                  <div class="ie-bullet red">✗</div>
                  <div class="ie-text"><?= o7e($item) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="costing-title-row">
          <div class="ct-line"></div>
          <div class="ct-text"><span class="ct-icon">→</span> COSTING DETAILS <span class="ct-icon">⊖</span></div>
          <div class="ct-line"></div>
        </div>
        <?php
        $o7_costing_type = isset($cost['costing_type_label']) ? strtolower(trim($cost['costing_type_label'])) : '';
        $o7_is_per_person = ($o7_costing_type == 'per person');
        $o7_pp = isset($cost['computed']['per_person']) ? $cost['computed']['per_person'] : array();
        ?>
        <?php if (!$o7_is_per_person) { ?>

          <table class="cost-table">
            <thead>
              <tr>
                <th>Package Type</th>
                <th>Tour Cost</th>
                <th>Taxes</th>
                <th>TCS</th>
                <th>Travel Cost</th>
                <th>Grand Total</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($o7_cost_grp as $ci => $row) :

                $tax_amount = '0.00';
                if (!empty($row['tax_display'])) {
                  preg_match('/INR\s*([\d,\.]+)/i', $row['tax_display'], $m);
                  if (!empty($m[1])) $tax_amount = $m[1];
                }

                $is_rec = (stripos(o7nv($row['package_type'], ''), 'premium') !== false)
                  || (stripos(o7nv($row['package_type'], ''), 'royal') !== false)
                  || (stripos(o7nv($row['package_type'], ''), 'recommended') !== false)
                  || ($ci === 1 && count($o7_cost_grp) > 1);
              ?>
                <tr<?= $is_rec ? ' class="recommended"' : '' ?>>
                  <td>
                    <strong><?= o7e(o7nv($row['package_type'], 'Package')) ?></strong>
                    <?php if ($is_rec) : ?><span class="rec-badge">RECOMMENDED</span><?php endif; ?>
                  </td>
                  <td><?= o7e(o7nv($row['tour_cost_display'], '0')) ?></td>
                  <td><?= o7e(o7nv($row['tax_display'], '0')) ?></td>
                  <td><?= o7e(o7nv($row['tcs_display'], '0')) ?></td>
                  <td><?= o7e(o7nv($row['travel_display'], '0')) ?></td>
                  <td><strong><?= o7e(o7nv($row['total_display'], '0')) ?></strong></td>
                  </tr>
                <?php endforeach; ?>
            </tbody>
          </table>
        <?php } else { ?>
          <table class="cost-table">
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
              <?php foreach ($o7_pp as $pp) :
                $tax_amount = '0.00';
                if (!empty($pp['tax_display'])) {
                  preg_match('/INR\s*([\d,\.]+)/i', $pp['tax_display'], $m);
                  if (!empty($m[1])) $tax_amount = $m[1];
                }
              ?>
                <tr>
                  <td><strong><?= o7e(o7nv($pp['package_type'], 'Package')) ?></strong></td>
                  <td><?= o7e(o7nv($pp['pp_adult_display'], '0')) ?></td>
                  <td><?= o7e(o7nv($pp['pp_cwb_display'], '0')) ?></td>
                  <td><?= o7e(o7nv($pp['pp_cwnb_display'], '0')) ?></td>
                  <td><?= o7e(o7nv($pp['pp_infant_display'], '0')) ?></td>
                  <td>INR <?= o7e($tax_amount) ?></td>
                  <td><?= o7e(o7nv($pp['tcs_display'], '0')) ?></td>
                  <td><?= o7e(o7nv($pp['visa_display'], '0')) ?></td>
                  <td><?= o7e(o7nv($pp['guide_display'], '0')) ?></td>
                  <td><?= o7e(o7nv($pp['misc_display'], '0')) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

        <?php } ?>
        <div class="cost-note">
          <div class="cost-note-icon">⭐</div>
          <div><?= o7e(o7nv(isset($incx['note']) ? $incx['note'] : '', 'Prices are subject to availability at the time of booking. Rates are subject to change without prior notice.')) ?></div>
        </div>
      </div>
    </div>

    <hr class="pg-sep" />

    <!-- PAGE 6 – PAYMENT -->
    <?php o7_render_page_header($hero, 'Payment Details', 6); ?>

    <div class="gold-section-header">
      <h2>Payment Details</h2>
    </div>

    <div class="pay-page">
      <div class="pay-inner">
        <div class="pay-grid">
          <div class="bank-card">
            <div class="bank-card-head">
              <div class="bank-card-head-title"><span>Bank Account Details</span></div>
            </div>
            <div class="bank-body">
              <div class="bank-field">
                <div class="bank-lbl">Account Name</div>
                <div class="bank-val"><?= o7e(o7nv($bank['account_name'], 'NA')) ?></div>
              </div>
              <div class="bank-field">
                <div class="bank-lbl">Account Number</div>
                <div class="bank-val"><?= o7e(o7nv($bank['account_no'], 'NA')) ?></div>
              </div>
              <div class="bank-field">
                <div class="bank-lbl">Bank Name</div>
                <div class="bank-val"><?= o7e(o7nv($bank['bank_name'], 'NA')) ?></div>
              </div>
              <div class="bank-field">
                <div class="bank-lbl">Branch</div>
                <div class="bank-val"><?= o7e(o7nv($bank['branch_name'], 'NA')) ?></div>
              </div>
              <div class="bank-field">
                <div class="bank-lbl">IFSC Code</div>
                <div class="bank-val"><?= o7e(o7nv($bank['ifsc_code'], o7nv($bank['swift_code'], 'NA'))) ?></div>
              </div>
              <?php if (!empty($bank['upi_id'])) : ?>
                <div class="bank-field">
                  <div class="bank-lbl">UPI ID</div>
                  <div class="bank-val"><?= o7e($bank['upi_id']) ?></div>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <div class="qr-card">
            <div class="qr-head"><span class="arrow">→</span> SCAN &amp; PAY <span class="arrow">·</span></div>
            <div class="qr-body">
              <div class="qr-img-box">
                <?php if (!empty($bank['qr_html'])) : ?>
                  <?= $bank['qr_html'] ?>
                <?php elseif (!empty($bank['qr_code']) || !empty($bank['branch_qr_url'])) : ?>
                  <img src="<?= o7e(o7nv($bank['branch_qr_url'], $bank['qr_code'])) ?>" alt="Payment QR" style="width:130px;height:130px;object-fit:contain;" />
                <?php else : ?>
                  <span style="color:var(--muted);font-size:11px;">QR not configured</span>
                <?php endif; ?>
              </div>
              <div class="qr-pay-methods">
                <span class="qr-pm" style="color:#00a0e9;">BHIM</span>
                <span class="qr-pm" style="color:#097939;">UPI</span>
                <span class="qr-pm" style="color:#4285f4;">G Pay</span>
                <span class="qr-pm" style="color:#00baf2;">Paytm</span>
                <span class="qr-pm" style="color:#5f259f;">PhonePe</span>
              </div>
            </div>
          </div>
        </div>

        <div class="pay-info-grid">
          <div class="pay-info-card">
            <div class="pay-info-head">
              <div class="pay-info-head-badge"><span>Payment Policy</span></div>
            </div>
            <div class="pay-info-body">
              <?php foreach ($o7_pay_notes as $note) : ?>
                <div class="pay-info-item"><span class="pay-bullet">•</span><span class="pay-text"><?= o7e($note) ?></span></div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="pay-info-card">
            <div class="pay-info-head">
              <div class="pay-info-head-badge"><span>Important</span></div>
            </div>
            <div class="pay-info-body">
              <?php foreach ($o7_book_policy as $pol) : ?>
                <div class="pay-info-item"><span class="pay-bullet">•</span><span class="pay-text"><?= o7e($pol) ?></span></div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <hr class="pg-sep" />

    <!-- PAGE 7 – REVIEWS -->
    <?php o7_render_page_header($hero, 'What Our Travellers Say', 7); ?>

    <div class="gold-section-header">
      <h2>What Our Travellers Say</h2>
    </div>

    <div class="reviews-page">
      <div class="reviews-inner">
        <div class="review-cards-grid">
          <?php if (!empty($testimonials)) :
            $o7_ti = 0;
            foreach ($testimonials as $t) :
              if ($o7_ti >= 3) {
                break;
              }
              $o7_ti++;
              $photo = o7_media_url(isset($t['photo']) ? $t['photo'] : '');
          ?>
              <div class="review-card">
                <?php if ($photo !== '') : ?>
                  <img class="review-avatar" src="<?= o7e($photo) ?>" alt="<?= o7e(o7nv($t['name'], '')) ?>" />
                <?php else : ?>
                  <div class="review-avatar" style="display:flex;align-items:center;justify-content:center;background:var(--navy);color:var(--gold2);font-weight:700;font-size:22px;"><?= o7e(o7_initials(o7nv($t['name'], 'T'))) ?></div>
                <?php endif; ?>
                <div class="review-name"><?= o7e(o7nv($t['name'], 'Traveller')) ?></div>
                <div class="review-tour"><?= o7e(o7nv($t['designation'], $o7_dest . ' Tour')) ?></div>
                <div class="review-stars">★★★★★</div>
                <div class="review-text">"<?= o7e(o7nv($t['review'], '')) ?>"</div>
              </div>
            <?php
            endforeach;
          else :
            ?>
            <div class="review-card">
              <div class="review-avatar" style="display:flex;align-items:center;justify-content:center;background:var(--navy);color:var(--gold2);font-weight:700;">★</div>
              <div class="review-name">Our Travellers</div>
              <div class="review-tour"><?= o7e($o7_dest) ?> Tour</div>
              <div class="review-stars">★★★★★</div>
              <div class="review-text">Customer testimonials can be managed from Quotation Builder settings.</div>
            </div>
          <?php endif; ?>
        </div>

        <div class="stats-bar">
          <div class="stat-item">
            <div class="stat-icon"><span style="font-size:28px;">G</span></div>
            <div class="stat-num"><?= o7e($o7_google_rating) ?><span style="font-size:16px;">/5</span></div>
            <div class="stat-lbl">Google Rating</div>
          </div>
          <div class="stats-div"></div>
          <div class="stat-item">
            <div class="stat-icon">🔔</div>
            <div class="stat-num"><?= o7e($o7_review_count) ?></div>
            <div class="stat-lbl">Happy Reviews</div>
          </div>
          <div class="stats-div"></div>
          <div class="stat-item">
            <div class="stat-icon">👥</div>
            <div class="stat-num"><?= o7e($o7_traveller_cnt) ?></div>
            <div class="stat-lbl">Happy Travellers</div>
          </div>
        </div>
      </div>
    </div>

    <hr class="pg-sep" />

    <!-- PAGE 8 – T&C -->
    <?php o7_render_page_header($hero, 'Terms & Conditions', 8); ?>

    <div class="gold-section-header">
      <h2>Terms &amp; Conditions</h2>
    </div>

    <div class="tnc-page">
      <div class="tnc-inner">
        <div class="tnc-grid">
          <?php foreach ($o7_tnc_cards as $card) : ?>
            <div class="tnc-item">
              <div class="tnc-icon"><?= o7e($card['icon']) ?></div>
              <div class="tnc-content">
                <div class="tnc-title"><?= o7e($card['title']) ?></div>
                <div class="tnc-text"><?= o7e($card['text']) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="tnc-agree">
          By proceeding with the booking, you agree to the above terms &amp; conditions and authorize <strong><?= o7e($o7_company) ?></strong> to process payments and make necessary arrangements for the tour.
        </div>
      </div>
    </div>

    <hr class="pg-sep" />

    <!-- PAGE 9 – THANK YOU -->
    <div class="thankyou-section">
      <div class="ty-banner">
        <div class="ty-banner-logo">
          <?php if ($o7_logo !== '') : ?>
            <img src="<?= o7e($o7_logo) ?>" alt="<?= o7e($o7_company) ?>" class="ty-nh" style="width:44px;height:44px;border-radius:50%;object-fit:contain;background:#fff;padding:4px;" />
          <?php else : ?>
            <div class="ty-nh"><?= o7e($o7_initials) ?></div>
          <?php endif; ?>
          <div>
            <div class="ty-brand-name"><?= o7e(strtoupper($o7_company)) ?></div>
            <div class="ty-brand-tag">Your Journey, Our Passion</div>
          </div>
        </div>
        <div class="ty-sep-line"></div>
        <div class="ty-script">Thank You</div>
      </div>

      <div style="display:flex;justify-content:flex-end;padding:10px 20px 0;position:relative;z-index:2;">
        <div class="page-num" style="position:relative;top:auto;right:auto;">09</div>
      </div>

      <div class="ty-bottom-grid">
        <div class="ty-contact-card">
          <div class="ty-contact-head">
            <div class="ch-line"></div>
            <div class="ch-text">GET IN TOUCH</div>
            <div class="ch-line"></div>
          </div>
          <?php if (!empty($ty['company_address'])) : ?>
            <div class="ty-contact-row"><span class="ty-ci">📍</span><span class="ty-ct"><?= o7e(o7nv($ty['company_name'], $o7_company)) ?><br /><?= o7e($ty['company_address']) ?></span></div>
          <?php endif; ?>
          <?php
          $o7_phone = o7nv($ty['company_contact'], o7nv($ty['user_mobile'], o7nv($hero['user_contact'], '')));
          $o7_email = o7nv($ty['company_email'], o7nv($hero['user_email_id'], ''));
          $o7_web   = o7nv($ty['website'], '');
          ?>

          <div class="ty-contact-row">
            <span class="ty-ci">📞</span>
            <span class="ty-ct">
              <a href="tel:<?= preg_replace('/\s+/', '', $o7_phone) ?>">
                <?= o7e($o7_phone) ?>
              </a>
            </span>
          </div>

          <div class="ty-contact-row">
            <span class="ty-ci">✉️</span>
            <span class="ty-ct">
              <a href="mailto:<?= o7e($o7_email) ?>">
                <?= o7e($o7_email) ?>
              </a>
            </span>
          </div>

          <?php if ($o7_web !== '') : ?>
            <div class="ty-contact-row">
              <span class="ty-ci">🌐</span>
              <span class="ty-ct">
                <a href="<?= o7e($o7_web) ?>">
                  <?= o7e($o7_web) ?>
                </a>
              </span>
            </div>
          <?php endif; ?>
        </div>

        <div class="ty-stats-card">
          <div class="ty-stat-row">
            <div class="ty-stat-icon">⭐</div>
            <div>
              <div class="ty-stat-num"><?= o7e($o7_google_rating) ?>/5</div>
              <div style="color:var(--gold2);font-size:14px;margin:2px 0;">★★★★★</div>
              <div class="ty-stat-lbl">Google Rating<br />(<?= o7e($o7_review_count) ?> Reviews)</div>
            </div>
          </div>
          <div class="ty-stat-row">
            <div class="ty-stat-icon">👥</div>
            <div>
              <div class="ty-stat-num"><?= o7e($o7_traveller_cnt) ?></div>
              <div class="ty-stat-lbl">Happy Travellers</div>
            </div>
          </div>
          <div class="ty-stat-row">
            <div class="ty-stat-icon">✈️</div>
            <div>
              <div class="ty-stat-num"><?= o7e($o7_duration) ?></div>
              <div class="ty-stat-lbl">Your Journey Duration</div>
            </div>
          </div>
        </div>
      </div>

      <div class="ty-prepared-bar">
        <div class="ty-prep-label">Prepared By</div>
        <?php
        $o7_prep_name = o7nv($ty['prepared_by'], o7nv($hero['login_user'], 'Team'));
        $o7_prep_photo = '';
        if (!empty($testimonials[0]['photo'])) {
          $o7_prep_photo = o7_media_url($testimonials[0]['photo']);
        }
        ?>
        <?php if ($o7_prep_photo !== '') : ?>
          <img class="ty-prep-avatar" src="<?= o7e($o7_prep_photo) ?>" alt="<?= o7e($o7_prep_name) ?>" />
        <?php else : ?>
          <div class="ty-prep-avatar" style="display:flex;align-items:center;justify-content:center;background:var(--navy);color:var(--gold2);font-family:'Cinzel',serif;font-weight:700;"><?= o7e(o7_initials($o7_prep_name)) ?></div>
        <?php endif; ?>
        <div>
          <div class="ty-prep-name"><?= o7e($o7_prep_name) ?></div>
          <div class="ty-prep-role">Travel Consultant</div>
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