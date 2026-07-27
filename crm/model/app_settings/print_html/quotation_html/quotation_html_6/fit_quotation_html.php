<?php

/**
 * OPTION-6 (quotation_html_6) — Package Tour Quotation
 * Layout/CSS from Final-Designs/Option-6-Done/index.html
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
// $testimonials = array();

$testimonials = isset($q['testimonials']) && is_array($q['testimonials'])
  ? $q['testimonials']
  : array();

$o6_cfg       = array();

if (!function_exists('o6e')) {
  function o6e($v)
  {
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
  }
}
if (!function_exists('o6nv')) {
  function o6nv($v, $f = '')
  {
    return ($v !== null && $v !== '') ? $v : $f;
  }
}
if (!function_exists('o6_media_url')) {
  function o6_media_url($url)
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
if (!function_exists('o6img')) {
  function o6img($url, $fallback)
  {
    $resolved = o6_media_url($url);
    return $resolved !== '' ? $resolved : $fallback;
  }
}
if (!function_exists('o6_guest_label')) {
  function o6_guest_label($ov)
  {
    $p = isset($ov['pax']) ? $ov['pax'] : array();
    $parts = array();
    $ad = (int) o6nv(isset($p['adult']) ? $p['adult'] : 0, 0);
    $ch = (int) o6nv(isset($p['children_with_bed']) ? $p['children_with_bed'] : 0, 0)
      + (int) o6nv(isset($p['children_without_bed']) ? $p['children_without_bed'] : 0, 0);
    $inf = (int) o6nv(isset($p['infant']) ? $p['infant'] : 0, 0);
    if ($ad) {
      $parts[] = $ad . ' Adult' . ($ad > 1 ? 's' : '');
    }
    if ($ch) {
      $parts[] = $ch . ' Child' . ($ch > 1 ? 'ren' : '');
    }
    if ($inf) {
      $parts[] = $inf . ' Infant' . ($inf > 1 ? 's' : '');
    }
    return $parts ? implode(', ', $parts) : o6nv($ov['guest_count'], '-');
  }
}
if (!function_exists('o6_split_lines')) {
  function o6_split_lines($html, $fallback = array())
  {
    $text = trim(strip_tags(str_replace(array('<br>', '<br/>', '<br />', '</p>', '</li>'), "\n", (string) $html)));
    $items = preg_split('/\r\n|\r|\n|•|\x{2022}/u', $text);
    $items = array_values(array_filter(array_map('trim', (array) $items)));
    return $items ? $items : $fallback;
  }
}
if (!function_exists('o6_air_code')) {
  function o6_air_code($loc)
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
if (!function_exists('o6_flight_parts')) {
  function o6_flight_parts($dt)
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
if (!function_exists('o6_vehicle_end_date')) {
  function o6_vehicle_end_date($v)
  {
    if (!empty($v['end_date_raw']) && function_exists('get_date_user')) {
      return get_date_user($v['end_date_raw']);
    }
    return o6nv(isset($v['end_date_raw']) ? $v['end_date_raw'] : '', '');
  }
}
if (!function_exists('o6_initials')) {
  function o6_initials($name)
  {
    $name = trim((string) $name);
    if ($name === '') {
      return 'G';
    }
    $parts = preg_split('/\s+/', $name);
    $out = '';
    foreach ($parts as $p) {
      if ($p !== '') {
        $out .= strtoupper(substr($p, 0, 1));
      }
      if (strlen($out) >= 2) {
        break;
      }
    }
    return $out !== '' ? $out : 'G';
  }
}
if (!function_exists('o6_first_name')) {
  function o6_first_name($name)
  {
    $name = trim((string) $name);
    if ($name === '') {
      return 'Guest';
    }
    $parts = preg_split('/\s+/', $name);
    return o6nv($parts[0], 'Guest');
  }
}
if (!function_exists('o6_stars')) {
  function o6_stars($rating)
  {
    $n = (int) preg_replace('/\D/', '', (string) $rating);
    if ($n < 1) {
      return '';
    }
    $n = min($n, 5);
    $html = '';
    for ($i = 0; $i < $n; $i++) {
      $html .= '<i class="fa-solid fa-star"></i>';
    }
    return $html;
  }
}
if (!function_exists('o6_render_page_header')) {
  function o6_render_page_header($hero, $topic)
  {
    $name = o6nv($hero['company_name'], 'Travel Partner');
    $logo = o6nv($hero['company_logo'], '');
?>
    <div class="page-header">
      <?php if ($logo !== '') : ?>
        <span class="header-brand"><img src="<?= o6e($logo) ?>" alt="<?= o6e($name) ?>" class="company-logo-img" /></span>
      <?php else : ?>
        <span class="header-brand"><?= o6e(strtoupper($name)) ?></span>
      <?php endif; ?>
      <span class="header-topic"><?= o6e(strtoupper($topic)) ?></span>
    </div>
  <?php
  }
}
if (!function_exists('o6_render_footer')) {
  function o6_render_footer($ty, $hero, $pg, $total, $meta_override = '')
  {
    if ($meta_override !== '') {
      $meta = $meta_override;
    } else {
      $contact = o6nv($ty['company_contact'], o6nv($hero['user_contact'], ''));
      $website = o6nv($ty['website'], '');
      $meta = trim($contact . ($website !== '' ? ' • ' . strtoupper($website) : ''));
    }
  ?>
    <div class="page-footer">
      <span class="footer-meta-text"><?= o6e($meta) ?></span>
      <span class="page-number"><?= o6e(str_pad((string) $pg, 2, '0', STR_PAD_LEFT) . '/' . str_pad((string) $total, 2, '0', STR_PAD_LEFT)) ?></span>
    </div>
<?php
  }
}

$o6_dest        = o6nv($ov['destination'], o6nv($hero['tour_name'], 'Tour'));
$o6_dest_up     = strtoupper($o6_dest);
$o6_client      = o6nv($ov['client_name'], o6nv($hero['client_name'], 'Guest'));
$o6_first       = o6_first_name($o6_client);
$o6_company     = o6nv($hero['company_name'], 'Travel Partner');
$o6_logo        = o6nv($hero['company_logo'], '');
$o6_tour_id     = o6nv($hero['package_code'], o6nv($ov['tour_id'], ''));
$o6_quot_code   = o6nv($hero['quotation_code'], '');
$o6_duration    = o6nv($ov['duration_label'], o6nv($hero['duration_label'], ''));
$o6_travel_from = o6nv($ov['travel_from'], '');
$o6_travel_to   = o6nv($ov['travel_to'], '');
$o6_travel_range = trim($o6_travel_from . ($o6_travel_to !== '' ? ' to ' . $o6_travel_to : ''));
$o6_pkg_badge   = '';
if (!empty($q['package_types_label'])) {
  $o6_pkg_badge = $q['package_types_label'];
} elseif (!empty($cost['computed']['group'][0]['package_type'])) {
  $o6_pkg_badge = $cost['computed']['group'][0]['package_type'];
} elseif (!empty($hotels[0]['package_type'])) {
  $o6_pkg_badge = $hotels[0]['package_type'];
}
$o6_pkg_ov = o6nv($o6_pkg_badge, o6nv($ov['package_type_label'], 'Package'));
$o6_included = o6_split_lines(isset($incx['included']) ? $incx['included'] : '', array('Inclusions as per itinerary.'));
$o6_excluded = o6_split_lines(isset($incx['excluded']) ? $incx['excluded'] : '', array('Exclusions as per company policy.'));
$o6_cost_grp = isset($cost['computed']['group']) ? $cost['computed']['group'] : array();
if (empty($o6_cost_grp)) {
  $o6_cost_grp = array(array(
    'package_type'      => 'Package',
    'tour_cost_display' => '0',
    'tax_display'       => '0',
    'tcs_display'       => '0',
    'travel_display'    => '0',
    'total_display'     => '0',
  ));
}
$o6_pay_notes = o6_split_lines(
  o6nv(isset($incx['quot_note']) ? $incx['quot_note'] : '', ''),
  array(
    '50% advance payment required to confirm booking.',
    'Remaining balance before departure as per company policy.',
    'Payment can be made via NEFT/RTGS/IMPS/UPI.',
  )
);
$o6_book_policy = o6_split_lines(
  o6nv(isset($incx['note']) ? $incx['note'] : '', ''),
  array(
    'All bookings are subject to availability.',
    'Package rates are as quoted in this proposal.',
    'Rates may vary during peak or festive seasons.',
  )
);
$o6_term_lines = o6_split_lines(isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '', array());
// $o6_show_flights = !empty($present['flights']) && !empty($flights);
// $o6_show_vehs = !empty($present['vehicles']) && !empty($vehs);

// ============ Dipti
$o6_show_flights = !empty($present['flights']) && !empty($flights);
$o6_show_vehs    = !empty($present['vehicles']) && !empty($vehs);
$o6_show_trains  = !empty($present['trains']) && !empty($trains);
$o6_show_acts    = !empty($present['activities']) && !empty($acts);
// =========================

$o6_itin_p5 = array();
$o6_itin_p6 = array();
if (!empty($itin)) {
  foreach ($itin as $idx => $day) {
    if ($idx < 2) {
      $o6_itin_p5[] = $day;
    } else {
      $o6_itin_p6[] = $day;
    }
  }
}

// $o6_total_pages = 11;
// if (!$o6_show_flights) {
//   $o6_total_pages--;
// }
// if (empty($o6_itin_p6)) {
//   $o6_total_pages--;
// }

// ============== Dipti
$o6_total_pages = 11;

if (!$o6_show_flights) $o6_total_pages--;
if (!$o6_show_trains)  $o6_total_pages--;
if (!$o6_show_acts)    $o6_total_pages--;
if (!$o6_show_vehs)    $o6_total_pages--;
if (empty($o6_itin_p6)) $o6_total_pages--;
// ========================


$o6_pg = 2;

$o6_consult_email = o6nv($ty['company_email'], o6nv($hero['user_email_id'], ''));
$o6_consult_phone = o6nv($ty['company_contact'], o6nv($hero['user_contact'], ''));
$o6_google_rating = o6nv(isset($o6_cfg['google_rating']) ? $o6_cfg['google_rating'] : '', '4.9');
$o6_review_count  = o6nv(isset($o6_cfg['review_count']) ? $o6_cfg['review_count'] : '', '245');
$o6_traveller_cnt = o6nv(isset($o6_cfg['traveller_count']) ? $o6_cfg['traveller_count'] : '', '5,000+');
$o6_years_exp     = o6nv(isset($o6_cfg['years_experience']) ? $o6_cfg['years_experience'] : '', '10+');

$o6_qr_url = '';
if (!empty($bank['qr_code_url'])) {
  $o6_qr_url = o6_media_url($bank['qr_code_url']);
} elseif (!empty($bank['qr_code'])) {
  $o6_qr_url = o6_media_url($bank['qr_code']);
} elseif (!empty($bank['branch_qr_url'])) {
  $o6_qr_url = o6_media_url($bank['branch_qr_url']);
}

$o6_term_clauses = array();
if (!empty($o6_book_policy)) {
  $o6_term_clauses[] = array('title' => 'Booking Policy', 'body' => implode(' ', array_slice($o6_book_policy, 0, 4)));
}
if (!empty($o6_pay_notes)) {
  $o6_term_clauses[] = array('title' => 'Payment Instructions', 'body' => implode(' ', array_slice($o6_pay_notes, 0, 4)));
}
foreach ($o6_term_lines as $ti => $line) {
  $title = 'Terms & Conditions';
  $body = $line;
  if (strpos($line, ':') !== false) {
    list($title, $body) = array_map('trim', explode(':', $line, 2));
  } elseif ($ti === 0) {
    $title = o6nv($terms['title'], 'Terms & Conditions');
  }
  $o6_term_clauses[] = array('title' => $title, 'body' => $body);
}
if (empty($o6_term_clauses)) {
  $o6_term_clauses[] = array(
    'title' => 'Terms & Conditions',
    'body'  => 'Terms and conditions will be shared as per company policy.',
  );
}

list($o6_logo_main, $o6_logo_sub) = array(strtoupper($o6_company), 'Journey &amp; Dream');
$brand_parts = preg_split('/\s+/', trim($o6_company), 2);
if (is_array($brand_parts) && count($brand_parts) >= 2) {
  $o6_logo_main = strtoupper($brand_parts[0]);
  $o6_logo_sub = strtoupper($brand_parts[1]);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= o6e($o6_dest) ?> Tour Package – <?= o6e($o6_company) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link href="option6.css" rel="stylesheet" />
</head>

<body>

  <!-- PAGE 1 — COVER -->
  <div class="page cover-page">
    <div class="jp-pattern-overlay"></div>
    <div class="cover-header">
      <div class="logo-block">
        <?php if ($o6_logo !== '') : ?>
          <img src="<?= o6e($o6_logo) ?>" alt="<?= o6e($o6_company) ?>" class="company-logo-img" style="max-height:48px;margin-bottom:6px;" />
        <?php else : ?>
          <span class="logo-main"><?= o6e($o6_logo_main) ?></span>
          <span class="logo-sub"><?= o6e(html_entity_decode(strip_tags($o6_logo_sub), ENT_QUOTES, 'UTF-8')) ?></span>
        <?php endif; ?>
      </div>
      <div class="proposal-badge">
        <span class="badge-code">TOUR PACKAGE</span>
        <span class="badge-id">#<?= o6e($o6_quot_code) ?></span>
      </div>
    </div>

    <div class="cover-hero">
      <div class="jp-accent-line"></div>
      <span class="proposal-title-tag"><?= o6e($o6_dest_up) ?></span>
      <h1 class="main-destination"><?= o6e($o6_dest_up) ?></h1>
      <h2 class="sub-destination">TOUR PACKAGE</h2>
      <p class="hero-description">Discover Extraordinary Experiences</p>
    </div>

    <div class="quick-glance-grid">
      <div class="glance-card">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="28" height="28" fill="#c62828">
          <path d="M64 64h384c17.7 0 32 14.3 32 32v32H32V96c0-17.7 14.3-32 32-32zM64 128h384v288h32v48H32v-48h32V128zm80 64v48h48v-48h-48zm88 0v48h48v-48h-48zm88 0v48h48v-48h-48zM144 280v48h48v-48h-48zm88 0v48h48v-48h-48zm88 0v48h48v-48h-48zM216 384v32h80v-32c0-22.1-17.9-40-40-40s-40 17.9-40 40z" />
        </svg>
        <h4>HOTELS</h4>
        <p>PREMIUM STAYS</p>
      </div>
      <div class="glance-card">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="28" height="28" fill="#c62828">
          <path d="M624 448H16c-8.8 0-16 7.2-16 16s7.2 16 16 16h608c8.8 0 16-7.2 16-16s-7.2-16-16-16zM80 352l159.7 42.7c17.8 4.8 36.8 2.5 53-6.4l267.4-146.8c31.2-17.1 42.7-56.2 25.6-87.4s-56.2-42.7-87.4-25.6L335.6 217.8 176 128l-56 30.7 120 117.7-112 61.5-64-48-48 26.4L80 352z" />
        </svg>
        <h4>FLIGHTS</h4>
        <p><?= o6e($o6_show_flights ? 'RETURN TRANSIT' : 'AS QUOTED') ?></p>
      </div>
      <div class="glance-card">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="28" height="28" fill="#c62828">
          <path d="M408 120c0 54.6-73.1 139.2-92.7 161a18.1 18.1 0 0 1-26.6 0C269.1 259.2 196 174.6 196 120C196 53.7 249.7 0 316 0s92 53.7 92 120zm-92 40a40 40 0 1 0 0-80 40 40 0 0 0 0 80zM144 96l24.5-8.2c-2.9 10.2-4.5 21-4.5 32.2 0 45 26.5 98.1 56.4 140.4L144 236V96zM32 160l80-26.7V256l-80 26.7V160zm368 116.5c29.8-42.3 56-95.1 56-140.5 0-15.6-3-30.5-8.5-44.2L544 64v352l-144 48V276.5zM176 268l144 48v164l-144-48V268z" />
        </svg>
        <h4>ACTIVITIES</h4>
        <p>SIGHTSEEING</p>
      </div>
      <div class="glance-card">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="28" height="28" fill="#c62828">
          <path d="M171.3 96h297.4c28.7 0 55.2 15.3 69.6 40l54.4 95.2C620.9 240.7 640 267.4 640 298.2V384c0 17.7-14.3 32-32 32h-32c0 53-43 96-96 96s-96-43-96-96H256c0 53-43 96-96 96s-96-43-96-96H32c-17.7 0-32-14.3-32-32v-64c0-39.8 30.2-72.6 69-76.8L119.2 136c9.7-24.3 33.3-40 59.1-40zM192 160l-38.4 80H288v-80H192zm144 0v80h170.9l-45.7-80H336zM160 464a48 48 0 1 0 0-96 48 48 0 0 0 0 96zm320 0a48 48 0 1 0 0-96 48 48 0 0 0 0 96z" />
        </svg>
        <h4>TRANSFERS</h4>
        <p><?= o6e($o6_show_vehs ? 'PRIVATE TRANSFER' : 'AS PER ITINERARY') ?></p>
      </div>
    </div>

    <div class="cover-footer">
      <div class="footer-meta">
        <h5>PREPARED EXCLUSIVELY FOR</h5>
        <p class="client-name"><?= o6e($o6_client) ?></p>
      </div>
      <div class="consultant-meta">
        <h5>YOUR TRAVEL CONSULTANT</h5>
        <p class="consultant-name"><?= o6e($o6_company) ?></p>
        <?php if ($o6_consult_email !== '') : ?>
          <p class="consultant-contact">
            <i class="fa-solid fa-envelope"></i>
            <a href="mailto:<?= o6e($o6_consult_email) ?>">
              <?= o6e($o6_consult_email) ?>
            </a>
          </p>
        <?php endif; ?>

        <?php if ($o6_consult_phone !== '') : ?>
          <p class="consultant-contact">
            <i class="fa-solid fa-phone"></i>
            <a href="tel:<?= preg_replace('/\s+/', '', $o6_consult_phone) ?>">
              <?= o6e($o6_consult_phone) ?>
            </a>
          </p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- PAGE 2 — WELCOME & OVERVIEW -->
  <div class="page">
    <?php o6_render_page_header($hero, $o6_dest_up . ' TOUR PACKAGE'); ?>

    <div class="content-container">
      <div class="welcome-section card-container">
        <span class="section-context">A Personalized Travel Experience</span>
        <h3 class="welcome-sub">Exclusively Designed for <?= o6e($o6_client) ?></h3>
        <p class="letter-para">Dear <?= o6e($o6_first) ?>, Thank you for choosing <strong><?= o6e($o6_company) ?></strong> for your upcoming journey to <?= o6e($o6_dest) ?>. We are delighted to present this carefully crafted travel proposal designed to provide memorable experiences, seamless arrangements, and exceptional hospitality throughout your trip.</p>
      </div>

      <div class="overview-section card-container">
        <div class="block-header-strip">
          <h4 class="block-title"><svg class="compass-icon" xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 512 512"
              width="24"
              height="24"
              fill="var(--japan-crimson)">
              <path d="M256 0C114.6 0 0 114.6 0 256S114.6 512 256 512 512 397.4 512 256 397.4 0 256 0zm93.1 162.9-43.4 130.2c-1.7 5.2-5.8 9.3-11 11l-130.2 43.4c-10.6 3.5-20.8-6.7-17.3-17.3l43.4-130.2c1.7-5.2 5.8-9.3 11-11l130.2-43.4c10.6-3.5 20.8 6.7 17.3 17.3z" />
            </svg> TOUR OVERVIEW</h4>
        </div>
        <div class="overview-details-grid">
          <div class="overview-item">
            <span class="label">QUOTATION ID</span>
            <span class="val font-highlight"><?= o6e($o6_quot_code) ?></span>
          </div>
          <div class="overview-item">
            <span class="label">TOUR ID</span>
            <span class="val"><?= o6e($o6_tour_id) ?></span>
          </div>
          <div class="overview-item">
            <span class="label">QUOTATION DATE</span>
            <span class="val"><?= o6e(o6nv($ov['quotation_date'], '')) ?></span>
          </div>
          <div class="overview-item">
            <span class="label">TRAVEL DATE</span>
            <span class="val"><?= o6e($o6_travel_range !== '' ? $o6_travel_range : ($o6_travel_from . ($o6_travel_to !== '' ? ' to ' . $o6_travel_to : ''))) ?></span>
          </div>
          <div class="overview-item">
            <span class="label">DURATION</span>
            <span class="val"><?= o6e($o6_duration) ?></span>
          </div>
          <div class="overview-item">
            <span class="label">PACKAGE TYPE</span>
            <span class="val"><?= o6e($o6_pkg_ov) ?></span>
          </div>
        </div>
      </div>

      <div class="split-profile-investment-grid">
        <div class="profile-card card-container">
          <h5 class="card-mini-title"><svg class="compass-icon" xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 512 512"
              width="24"
              height="24"
              fill="var(--japan-crimson)">
              <path d="M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256 256-114.6 256-256S397.4 0 256 0zm0 128a80 80 0 1 1 0 160 80 80 0 1 1 0-160zm0 320c-53 0-100.9-20.9-136.2-54.9 8.8-46.7 49.8-81.1 98.2-81.1h76c48.4 0 89.4 34.4 98.2 81.1C356.9 427.1 309 448 256 448z" />
            </svg> PREPARED FOR</h5>
          <div class="profile-identity-lock">
            <div class="avatar-badge"><?= o6e(o6_initials($o6_client)) ?></div>
            <div class="identity-text">
              <h4><?= o6e($o6_client) ?></h4>
              <!-- <p><i class="fa-solid fa-envelope"></i> <? //= o6e(o6nv($ov['customer_email'], o6nv($hero['user_email_id'], ''))) 
                                                            ?></p>
              <p><i class="fa-solid fa-phone"></i> <? //= o6e(o6nv($ov['customer_mobile'], o6nv($hero['user_contact'], ''))) 
                                                    ?></p> -->
              <p>
                <svg class="compass-icon-e" xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 512 512"
                  width="24"
                  height="24"
                  fill="var(--text-caption)">
                  <path d="M502.3 190.8 327.4 338c-15.4 12.9-37.5 12.9-52.9 0L9.7 190.8C3.8 185.8 0 178.4 0 170.6V112c0-26.5 21.5-48 48-48h416c26.5 0 48 21.5 48 48v58.6c0 7.8-3.8 15.2-9.7 20.2zM512 224.5V400c0 26.5-21.5 48-48 48H48C21.5 448 0 426.5 0 400V224.5l243.7 204.9c7.1 6 15.7 9 24.3 9s17.2-3 24.3-9L512 224.5z" />
                </svg>
                <a href="mailto:<?= o6e(o6nv($ov['customer_email'], o6nv($hero['user_email_id'], ''))) ?>">
                  <?= o6e(o6nv($ov['customer_email'], o6nv($hero['user_email_id'], ''))) ?>
                </a>
              </p>

              <p>
                <svg class="compass-icon-e" xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 512 512"
                  width="24"
                  height="24"
                  fill="var(--text-caption)">
                  <path d="M497.39 361.8l-112-48c-11.2-4.8-24.3-1.6-32 8l-49.6 60.6c-77.5-37.4-140.8-100.7-178.2-178.2l60.6-49.6c9.6-7.7 12.8-20.8 8-32l-48-112C141.2 3.8 129.8-2.1 118.2 .7l-104 24C5.9 26.6 0 36.4 0 46.6 0 299.1 212.9 512 465.4 512c10.2 0 20-5.9 21.9-14.2l24-104c2.8-11.6-3.1-23-13.9-28z" />
                </svg>
                <a href="tel:<?= preg_replace('/\s+/', '', o6nv($ov['customer_mobile'], o6nv($hero['user_contact'], ''))) ?>">
                  <?= o6e(o6nv($ov['customer_mobile'], o6nv($hero['user_contact'], ''))) ?>
                </a>
              </p>
            </div>
          </div>
        </div>

        <div class="investment-card card-container">
          <h5 class="card-mini-title"><svg class="compass-icon" xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 384 512"
              width="24"
              height="24"
              fill="var(--japan-crimson)">
              <path d="M0 64C0 28.7 28.7 0 64 0H320c35.3 0 64 28.7 64 64V480c0 12.9-7.8 24.6-19.8 29.5s-25.7 2.2-35-6.9L288 464l-41.2 38.6c-12.3 11.5-31.3 11.5-43.6 0L160 464l-43.2 38.6c-12.3 11.5-31.3 11.5-43.6 0L32 464l-12.2 11.5C10.5 484.6 0 478.1 0 464V64zm80 64c0 8.8 7.2 16 16 16H288c8.8 0 16-7.2 16-16s-7.2-16-16-16H96c-8.8 0-16 7.2-16 16zm16 80c-8.8 0-16 7.2-16 16s7.2 16 16 16H288c8.8 0 16-7.2 16-16s-7.2-16-16-16H96zm0 96c-8.8 0-16 7.2-16 16s7.2 16 16 16H224c8.8 0 16-7.2 16-16s-7.2-16-16-16H96z" />
            </svg> PACKAGE CONFIGURATION</h5>
          <?php foreach ($o6_cost_grp as $ci => $row) :
            $tier_cls = ($ci === 1 && count($o6_cost_grp) > 1) ? ' recommended-tier' : '';
            $tier_lbl = ($ci === 0) ? 'Selected Base Tier' : 'Package Tier ' . ($ci + 1);
            if ($ci === 1 && count($o6_cost_grp) > 1) {
              $tier_lbl = 'Premium Upgrade Tier';
            }
          ?>
            <div class="pricing-tier-row<?= $tier_cls ?>">
              <span class="tier-name"><?= o6e($tier_lbl) ?></span>
              <span class="tier-cost"><?= o6e(o6nv($row['package_type'], 'Package')) ?></span>
            </div>
          <?php endforeach; ?>
          <div class="tax-disclaimer-strip">VALIDATED PROPOSAL FRAMEWORK</div>
        </div>
      </div>
    </div>

    <?php o6_render_footer($ty, $hero, $o6_pg, $o6_total_pages); ?>
  </div>
  <?php $o6_pg++; ?>

  <!-- PAGE 3 — HOTELS -->
  <div class="page">
    <?php o6_render_page_header($hero, 'Accommodation Details'); ?>

    <div class="content-container">
      <div class="section-intro"></br>
        <h2 class="main-section-title">Hotel Options &amp; Stay Parameters</h2>
        <p class="section-desc">
          Package Type: <?= o6e($o6_pkg_ov) ?> — Handpicked accommodation options for your <?= o6e($o6_dest) ?> journey.
        </p>
      </div>

      <?php if (!empty($hotels)) : ?>
        <?php foreach ($hotels as $h) :

          $room_label = o6nv($h['room_category'], o6nv($h['room_type'], 'Standard Room'));
          $stars_html = o6_stars(o6nv($h['rating'], ''));

          // Hotel Image
          $dummy_hotel_img = BASE_URL . 'images/hotel.png';

          if (!empty($h['hotel_photo'])) {
            $o6_hphoto = $h['hotel_photo'];
          } else {
            $o6_hphoto = $dummy_hotel_img;
          }

        ?>

          <div class="hotel-striped-card layout-split-card">

            <div class="card-left-stripbg">
              <img src="<?= o6e($o6_hphoto) ?>"
                alt="<?= o6e(o6nv($h['hotel_name'], 'Hotel')) ?>">
            </div>

            <div class="card-right-datacore">

              <div class="property-top-meta-row">

                <div class="title-stars-block">
                  <span class="micro-location-tag">
                    <?= o6e(strtoupper(o6nv($h['hotel_city'], $o6_dest_up))) ?>
                  </span>

                  <h3><?= o6e(o6nv($h['hotel_name'], 'Hotel')) ?></h3>

                  <?php if ($stars_html != '') : ?>
                    <div class="star-rating-row"><?= $stars_html ?></div>
                  <?php endif; ?>

                </div>

                <div class="property-badges-block">

                  <?php if (!empty($h['rating'])) : ?>
                    <div class="score-pill-badge">
                      <i class="fa-solid fa-star"></i>
                      <?= o6e($h['rating']) ?>
                    </div>
                  <?php endif; ?>

                  <?php if (!empty($h['meal_plan'])) : ?>
                    <div class="score-pill-badge" style="background:#f1f5f9;color:#1e293b;">
                      <?= o6e($h['meal_plan']) ?>
                    </div>
                  <?php endif; ?>

                </div>

              </div>

              <table class="property-metrics-table">
                <thead>
                  <tr>
                    <th>CHECK-IN</th>
                    <th>CHECK-OUT</th>
                    <th>ROOM CATEGORY</th>
                  </tr>
                </thead>

                <tbody>
                  <tr>
                    <td><strong><?= o6e(o6nv($h['check_in'], '')) ?></strong></td>
                    <td><strong><?= o6e(o6nv($h['check_out'], '')) ?></strong></td>
                    <td><strong><?= o6e($room_label) ?></strong></td>
                  </tr>
                </tbody>
              </table>

            </div>

          </div>

        <?php endforeach; ?>

      <?php else : ?>

        <div class="hotel-striped-card layout-split-card">

          <div class="card-left-stripbg">
            <img src="<?= o6e(BASE_URL . 'images/hotel.png') ?>" alt="Hotel">
          </div>

          <div class="card-right-datacore">
            <div class="property-top-meta-row">
              <div class="title-stars-block">
                <h3>Hotel details will be confirmed with your booking.</h3>
              </div>
            </div>
          </div>

        </div>

      <?php endif; ?>

    </div>

    <?php o6_render_footer($ty, $hero, $o6_pg, $o6_total_pages); ?>
  </div>
  <?php $o6_pg++; ?>

  <?php if ($o6_show_flights) : ?>
    <!-- PAGE 4 — FLIGHTS -->
    <div class="page">
      <?php o6_render_page_header($hero, $o6_dest_up . ' TOUR PACKAGE'); ?>

      <div class="content-container">
        <div class="section-intro"></br>
          <h2 class="main-section-title"><svg class="flight-icon"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 576 512"
              fill="currentColor">
              <path d="M480 192H365.71L260.61 8.67C255.21-.78 244.94-2.85 236.83 3.78L198.4 35.2c-5.5 4.5-8.3 11.5-7.3 18.6L224 192H112l-43.2-57.6c-4.6-6.2-13.2-7.8-19.7-3.7L7.8 155.2c-7.7 4.8-10.1 14.8-5.4 22.5L64 256 2.4 334.3c-4.7 7.7-2.3 17.7 5.4 22.5l41.3 24.5c6.5 4.1 15.1 2.5 19.7-3.7L112 320h112l-32.9 138.2c-1 7.1 1.8 14.1 7.3 18.6l38.4 31.4c8.1 6.6 18.4 4.6 23.8-4.9L365.7 320H480c53 0 96-28.7 96-64s-43-64-96-64z" />
            </svg> Flight Details</h2>
          <p class="section-desc">Confirmed flight configurations for your outbound and return sectors.</p>
        </div>

        <div class="flight-itinerary-stack">
          <?php
          $o6_fi = 0;
          $o6_fcnt = count($flights);
          foreach ($flights as $f) :
            $o6_fi++;
            $air_name = o6nv($f['airline_name'], o6nv($f['airline_display'], 'Flight'));
            $flight_lbl = o6nv($f['airline_code'], o6nv($f['airline_display'], ''));
            $from_code = o6_air_code(o6nv($f['from_city'], ''));
            $to_code = o6_air_code(o6nv($f['to_city'], ''));
            list($dep_time, $dep_date) = o6_flight_parts(o6nv($f['departure_datetime'], ''));
            list($arr_time, $arr_date) = o6_flight_parts(o6nv($f['arrival_datetime'], ''));
            $sector = ($o6_fi === 1) ? 'OUTBOUND' : (($o6_fi === $o6_fcnt && $o6_fcnt > 1) ? 'RETURN' : 'SECTOR ' . $o6_fi);
            $baggage = o6nv($f['baggage'], 'As per airline policy');
          ?>
            <div class="boarding-pass-container">
              <div class="pass-left-main">
                <div class="pass-header-row">
                  <span class="pass-sector-route"><?= o6e($sector) ?> · <?= o6e(strtoupper(o6nv($f['from_city'], ''))) ?> <i class="fa-solid fa-arrow-right-long"></i> <?= o6e(strtoupper(o6nv($f['to_city'], ''))) ?></span>
                  <span class="pass-cabin-badge"><?= o6e(strtoupper(o6nv($f['class'], 'Economy'))) ?></span>
                </div>
                <div class="pass-carrier-row">
                  <div class="carrier-icon-square"><svg class="flight-icon"
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 576 512"
                      fill="currentColor">
                      <path d="M480 192H365.71L260.61 8.67C255.21-.78 244.94-2.85 236.83 3.78L198.4 35.2c-5.5 4.5-8.3 11.5-7.3 18.6L224 192H112l-43.2-57.6c-4.6-6.2-13.2-7.8-19.7-3.7L7.8 155.2c-7.7 4.8-10.1 14.8-5.4 22.5L64 256 2.4 334.3c-4.7 7.7-2.3 17.7 5.4 22.5l41.3 24.5c6.5 4.1 15.1 2.5 19.7-3.7L112 320h112l-32.9 138.2c-1 7.1 1.8 14.1 7.3 18.6l38.4 31.4c8.1 6.6 18.4 4.6 23.8-4.9L365.7 320H480c53 0 96-28.7 96-64s-43-64-96-64z" />
                    </svg></div>
                  <div class="carrier-text-stack">
                    <h3><?= o6e($air_name) ?></h3>
                    <p>FLIGHT <?= o6e($flight_lbl) ?></p>
                  </div>
                </div>
                <div class="pass-transit-core">
                  <div class="transit-node alignment-left">
                    <h2><?= o6e($from_code) ?></h2>
                    <p class="airport-sub"><?= o6e(o6nv($f['from_city'], '')) ?></p>
                  </div>
                  <div class="transit-vector-track">
                    <span class="duration-lbl">Depart: <?= o6e($dep_date) ?></span>
                    <div class="track-vector-line"><!-- Font Awesome: fa-solid fa-plane track-plane-icon -->
                      <svg class="track-plane-icon"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 576 512"
                        width="24"
                        height="24"
                        fill="var(--japan-crimson)">
                        <path d="M480 192H365.71L260.61 8.67C255.21-.78 244.94-2.85 236.83 3.78L198.4 35.2c-5.5 4.5-8.3 11.5-7.3 18.6L224 192H112l-43.2-57.6c-4.6-6.2-13.2-7.8-19.7-3.7L7.8 155.2c-7.7 4.8-10.1 14.8-5.4 22.5L64 256 2.4 334.3c-4.7 7.7-2.3 17.7 5.4 22.5l41.3 24.5c6.5 4.1 15.1 2.5 19.7-3.7L112 320h112l-32.9 138.2c-1 7.1 1.8 14.1 7.3 18.6l38.4 31.4c8.1 6.6 18.4 4.6 23.8-4.9L365.7 320H480c53 0 96-28.7 96-64s-43-64-96-64z" />
                      </svg>
                    </div>
                    <span class="stops-lbl">Duration: <?= o6e(o6nv($f['duration'], 'Direct')) ?></span>
                  </div>
                  <div class="transit-node alignment-right">
                    <h2><?= o6e($to_code) ?></h2>
                    <p class="airport-sub"><?= o6e(o6nv($f['to_city'], '')) ?></p>
                  </div>
                </div>
                <div class="pass-footer-metrics">
                  <div class="metric-column">
                    <span class="lbl">DEPARTS TIME</span>
                    <span class="val"><strong><?= o6e($dep_time) ?></strong></span>
                  </div>
                  <div class="metric-column">
                    <span class="lbl">ARRIVES TIME</span>
                    <span class="val"><strong><?= o6e($arr_time) ?></strong></span>
                  </div>
                  <div class="metric-column static-allowance">
                    <span><i class="fa-solid fa-briefcase"></i> ALLOWANCE: <?= o6e(strtoupper($baggage)) ?></span>
                  </div>
                </div>
              </div>
              <div class="pass-right-stub">
                <div class="stub-notch-top"></div>
                <div class="stub-notch-bottom"></div>
                <div class="stub-content-wrapper">
                  <div class="stub-data-node"><span class="stub-lbl">FLIGHT</span><span class="stub-val"><?= o6e($flight_lbl) ?></span></div>
                  <div class="stub-data-node"><span class="stub-lbl">CLASS</span><span class="stub-val"><?= o6e(o6nv($f['class'], 'Economy')) ?></span></div>
                  <div class="stub-data-node"><span class="stub-lbl">SEAT</span><span class="stub-val">As Assumed</span></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <?php o6_render_footer($ty, $hero, $o6_pg, $o6_total_pages); ?>
    </div>
    <?php $o6_pg++; ?>
  <?php endif; ?>

  <!-- Train Details -->
  <?php if ($o6_show_trains) : ?>
    <div class="page">
      <?php o6_render_page_header($hero, $o6_dest_up . ' TOUR PACKAGE'); ?>

      <!-- <? //php if (!empty($trains)) : 
            ?> -->
      <div class="section-intro" style="margin-top:28px;">
        <h2 class="main-section-title"><!-- Font Awesome: fa-solid fa-train -->
          <svg class="carrier-icon-square-t" xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 448 512"
            width="24"
            height="24"
            fill="var(--primary-navy)">
            <path d="M96 0C43 0 0 43 0 96V352c0 35.3 28.7 64 64 64L32 480c-8.8 17.7 4 32 23.8 32h336.4c19.8 0 32.6-14.3 23.8-32l-32-64c35.3 0 64-28.7 64-64V96c0-53-43-96-96-96H96zM64 96c0-17.7 14.3-32 32-32H352c17.7 0 32 14.3 32 32V224H64V96zm64 224a32 32 0 1 1-64 0 32 32 0 1 1 64 0zm256 0a32 32 0 1 1-64 0 32 32 0 1 1 64 0zM160 416h128l24 48H136l24-48z" />
          </svg></i> Train Details
        </h2>
        <p class="section-desc">Confirmed train journey details for your tour.</p>
      </div>

      <div class="flight-itinerary-stack">
        <?php foreach ($trains as $tr) :
          $from_loc = isset($tr['from_location']) ? $tr['from_location'] : '';
          $to_loc = isset($tr['to_location']) ? $tr['to_location'] : '';
          $train_class = isset($tr['class']) ? $tr['class'] : 'NA';
          $from_date = isset($tr['from_date']) ? $tr['from_date'] : '';

          $total_pax = 0;
          if (isset($ov['pax']) && is_array($ov['pax'])) {
            $total_pax =
              (int)o6nv(isset($ov['pax']['adult']) ? $ov['pax']['adult'] : 0, 0) +
              (int)o6nv(isset($ov['pax']['children_with_bed']) ? $ov['pax']['children_with_bed'] : 0, 0) +
              (int)o6nv(isset($ov['pax']['children_without_bed']) ? $ov['pax']['children_without_bed'] : 0, 0) +
              (int)o6nv(isset($ov['pax']['infant']) ? $ov['pax']['infant'] : 0, 0);
          }
        ?>
          <div class="boarding-pass-container">
            <div class="pass-left-main">
              <div class="pass-header-row">
                <span class="pass-sector-route">
                  TRAIN · <?= o6e(strtoupper($from_loc)) ?>
                  <i class="fa-solid fa-arrow-right-long"></i>
                  <?= o6e(strtoupper($to_loc)) ?>
                </span>
                <span class="pass-cabin-badge"><?= o6e($train_class) ?></span>
              </div>

              <div class="pass-carrier-row">
                <div class="carrier-icon-square"><svg xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 448 512"
                    width="24"
                    height="24"
                    fill="var(--primary-navy)">
                    <path d="M96 0C43 0 0 43 0 96V352c0 35.3 28.7 64 64 64L32 480c-8.8 17.7 4 32 23.8 32h336.4c19.8 0 32.6-14.3 23.8-32l-32-64c35.3 0 64-28.7 64-64V96c0-53-43-96-96-96H96zM64 96c0-17.7 14.3-32 32-32H352c17.7 0 32 14.3 32 32V224H64V96zm64 224a32 32 0 1 1-64 0 32 32 0 1 1 64 0zm256 0a32 32 0 1 1-64 0 32 32 0 1 1 64 0zM160 416h128l24 48H136l24-48z" />
                  </svg></div>
                <div class="carrier-text-stack">
                  <h3>Train Journey</h3>
                  <p>CLASS <?= o6e($train_class) ?></p>
                </div>
              </div>

              <div class="pass-transit-core">
                <div class="transit-node alignment-left">
                  <h2><?= o6e(o6_air_code($from_loc)) ?></h2>
                  <p class="airport-sub"><?= o6e(o6nv($from_loc, 'NA')) ?></p>
                </div>

                <div class="transit-vector-track">
                  <span class="duration-lbl">Date: <?= o6e(o6nv($from_date, 'NA')) ?></span>
                  <div class="track-vector-line"><svg class="track-vector-line-t" xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 48 48"
                      width="24"
                      height="24"
                      circle cx="24" cy="24" r="24"
                      fill="var(--japan-crimson)">
                      <path fill="#c62828"
                        d="M17 8C12.6 8 9 11.6 9 16v16c0 2.9 2.1 5.3 4.9 5.9L11 43h3l2-4h16l2 4h3l-2.9-5.1c2.8-.6 4.9-3 4.9-5.9V16c0-4.4-3.6-8-8-8H17zm0 3h14c2.8 0 5 2.2 5 5v8H12v-8c0-2.8 2.2-5 5-5zm-2 16a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zm18 0a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5z" />
                    </svg></div>
                  <span class="stops-lbl">Rail Journey</span>
                </div>

                <div class="transit-node alignment-right">
                  <h2><?= o6e(o6_air_code($to_loc)) ?></h2>
                  <p class="airport-sub"><?= o6e(o6nv($to_loc, 'NA')) ?></p>
                </div>
              </div>

              <div class="pass-footer-metrics">
                <div class="metric-column">
                  <span class="lbl">DATE & TIME</span>
                  <span class="val"><strong><?= o6e(o6nv($from_date, 'NA')) ?></strong></span>
                </div>
                <div class="metric-column">
                  <span class="lbl">TOTAL PAX</span>
                  <span class="val"><strong><?= o6e($total_pax) ?></strong></span>
                </div>
                <div class="metric-column static-allowance">
                  <span><i class="fa-solid fa-ticket"></i> CLASS: <?= o6e(strtoupper($train_class)) ?></span>
                </div>
              </div>
            </div>

            <div class="pass-right-stub">
              <div class="stub-notch-top"></div>
              <div class="stub-notch-bottom"></div>
              <div class="stub-content-wrapper">
                <div class="stub-data-node"><span class="stub-lbl">TYPE</span><span class="stub-val">TRAIN</span></div>
                <div class="stub-data-node"><span class="stub-lbl">CLASS</span><span class="stub-val"><?= o6e($train_class) ?></span></div>
                <div class="stub-data-node"><span class="stub-lbl">PAX</span><span class="stub-val"><?= o6e($total_pax) ?></span></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <!-- <? //php endif; 
            ?> -->

      <?php o6_render_footer($ty, $hero, $o6_pg, $o6_total_pages); ?>
    </div>
    <?php $o6_pg++; ?>
  <?php endif; ?>
  <!-- ============== -->

  <!-- Activity Details -->
  <?php if ($o6_show_acts) : ?>
    <div class="page">
      <?php o6_render_page_header($hero, $o6_dest_up . ' TOUR PACKAGE'); ?>
      <!-- <? //php if (!empty($acts)) : 
            ?> -->
      <div class="section-intro" style="margin-top:28px;">
        <h2 class="main-section-title"><svg xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 512 512"
            width="20"
            height="20"
            fill="var(--primary-navy)">
            <path d="M149.1 64.8 109.3 128H64C28.7 128 0 156.7 0 192V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V192c0-35.3-28.7-64-64-64H402.7L362.9 64.8C351 45.9 330.2 32 307.8 32H204.2c-22.4 0-43.2 13.9-55.1 32.8zM256 384a96 96 0 1 1 0-192 96 96 0 1 1 0 192zm0-48a48 48 0 1 0 0-96 48 48 0 1 0 0 96z" />
          </svg> Activity Details</h2>
        <p class="section-desc">Experiences and activities included in this quotation.</p>
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
            (int)o6nv(isset($a['pax']['adult']) ? $a['pax']['adult'] : 0, 0) +
            (int)o6nv(isset($a['pax']['chwb']) ? $a['pax']['chwb'] : 0, 0) +
            (int)o6nv(isset($a['pax']['chwob']) ? $a['pax']['chwob'] : 0, 0) +
            (int)o6nv(isset($a['pax']['infant']) ? $a['pax']['infant'] : 0, 0);
        }
      ?>
        <div class="boarding-pass-container">
          <div class="pass-left-main">
            <div class="pass-header-row">
              <span class="pass-sector-route">ACTIVITY · <?= o6e(strtoupper(o6nv($city_name, 'CITY'))) ?></span>
              <span class="pass-cabin-badge"><?= o6e(o6nv($transfer_type, 'ACTIVITY')) ?></span>
            </div>

            <div class="pass-carrier-row">
              <div class="carrier-icon-square"><svg xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 512 512"
                  width="13"
                  height="13"
                  fill="var(--primary-navy)">
                  <path d="M149.1 64.8 109.3 128H64C28.7 128 0 156.7 0 192V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V192c0-35.3-28.7-64-64-64H402.7L362.9 64.8C351 45.9 330.2 32 307.8 32H204.2c-22.4 0-43.2 13.9-55.1 32.8zM256 384a96 96 0 1 1 0-192 96 96 0 1 1 0 192zm0-48a48 48 0 1 0 0-96 48 48 0 1 0 0 96z" />
                </svg></div>
              <div class="carrier-text-stack">
                <h3><?= o6e(o6nv($activity_name, 'Activity')) ?></h3>
                <p><?= o6e(o6nv($city_name, 'NA')) ?></p>
              </div>
            </div>

            <div class="pass-footer-metrics">
              <div class="metric-column">
                <span class="lbl">DATE</span>
                <span class="val"><strong><?= o6e(o6nv($activity_date, 'NA')) ?></strong></span>
              </div>
              <div class="metric-column">
                <span class="lbl">TOTAL PAX</span>
                <span class="val"><strong><?= o6e($total_pax) ?> Pax</strong></span>
              </div>
              <div class="metric-column static-allowance">
                <span><svg xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 512 512"
                    width="13"
                    height="13"
                    fill="var(--primary-navy)">
                    <path d="M135.2 32c-13.3 0-25.2 8.3-29.8 20.8L64 160H48C21.5 160 0 181.5 0 208v160c0 17.7 14.3 32 32 32h16c0 44.2 35.8 80 80 80s80-35.8 80-80h96c0 44.2 35.8 80 80 80s80-35.8 80-80h16c17.7 0 32-14.3 32-32V208c0-26.5-21.5-48-48-48h-16l-41.4-107.2C402 40.3 390.1 32 376.8 32H135.2zM128 400a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm256 0a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM96.9 160l30.9-80H384l30.9 80H96.9z" />
                  </svg> TRANSFER: <?= o6e(strtoupper(o6nv($transfer_type, 'NA'))) ?></span>
              </div>
            </div>
          </div>

          <div class="pass-right-stub">
            <div class="stub-notch-top"></div>
            <div class="stub-notch-bottom"></div>
            <div class="stub-content-wrapper">
              <div class="stub-data-node"><span class="stub-lbl">TYPE</span><span class="stub-val">ACTIVITY</span></div>
              <div class="stub-data-node"><span class="stub-lbl">CITY</span><span class="stub-val"><?= o6e(o6nv($city_name, 'NA')) ?></span></div>
              <div class="stub-data-node"><span class="stub-lbl">PAX</span><span class="stub-val"><?= o6e($total_pax) ?></span></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <!-- <? //php endif; 
            ?> -->

      <?php o6_render_footer($ty, $hero, $o6_pg, $o6_total_pages); ?>
    </div>
    <?php $o6_pg++; ?>
  <?php endif; ?>
  <!-- =============== -->

  <!-- PAGE 5 — VEHICLES + ITINERARY (DAYS 1–2) -->
  <?php if ($o6_show_vehs) : ?>

    <div class="page">
      <?php o6_render_page_header($hero, 'Transportation Details'); ?>

      <div class="content-container">
        <!-- <//?php if ($o6_show_vehs) : ?> -->
        <div class="section-intro"></br>
          <h2 class="main-section-title"><svg xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 640 512"
              width="20"
              height="20"
              fill="var(--primary-navy)">
              <path d="M171.3 96h297.4c28.7 0 55.2 15.3 69.6 40l54.4 95.2C620.9 240.7 640 267.4 640 298.2V384c0 17.7-14.3 32-32 32h-32c0 53-43 96-96 96s-96-43-96-96H256c0 53-43 96-96 96s-96-43-96-96H32c-17.7 0-32-14.3-32-32v-64c0-39.8 30.2-72.6 69-76.8L119.2 136c9.7-24.3 33.3-40 59.1-40zM192 160l-38.4 80H288v-80H192zm144 0v80h170.9l-45.7-80H336zM160 464a48 48 0 1 0 0-96 48 48 0 0 0 0 96zm320 0a48 48 0 1 0 0-96 48 48 0 0 0 0 96z" />
            </svg> Ground Transportation</h2>
          <p class="section-desc">Private logistics allocations designated for the program duration.</p>
        </div>
        <?php
        $vehicle_img = BASE_URL . 'images/vehicle.png';
        ?>
        <?php foreach ($vehs as $v) :
          $v_start = o6nv($v['date'], '');
          $v_end = o6_vehicle_end_date($v);
          $svc = o6nv($v['service_duration'], o6nv($v['description'], 'As per itinerary'));
        ?>
          <div class="hotel-striped-card layout-split-card" style="height: 160px;">
            <!-- <div class="card-left-stripbg"></div> -->
            <div class="card-left-stripbg">
              <img src="<?= o6e(BASE_URL . 'images/vehicle.png') ?>" alt="Vehicle">
            </div>
            <div class="card-right-datacore" style="padding: 12px 18px;">
              <div class="property-top-meta-row">
                <div class="title-stars-block">
                  <span class="micro-location-tag">VEHICLE CATEGORY: <?= o6e(strtoupper(o6nv($v['vehicle_type'], 'PRIVATE TRANSFER'))) ?></span>
                  <h3><?= o6e(o6nv($v['vehicle_name'], 'Vehicle')) ?><?php if (!empty($v['vehicle_count'])) : ?> (<?= o6e($v['vehicle_count']) ?>)<?php endif; ?></h3>
                </div>
                <div class="score-pill-badge" style="background:#f1f5f9;color:#1e293b;"><?= o6e($svc) ?></div>
              </div>
              <table class="property-metrics-table" style="margin: 4px 0;">
                <thead>
                  <tr>
                    <th>PICKUP LOCATION</th>
                    <th>DROP-OFF ROUTING</th>
                    <th>START DATE</th>
                    <th>RETURN DATE</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><?= o6e(o6nv($v['pickup'], 'NA')) ?></td>
                    <td><?= o6e(o6nv($v['drop'], 'NA')) ?></td>
                    <td><?= o6e($v_start) ?></td>
                    <td><?= o6e($v_end) ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        <?php endforeach; ?>
        <!-- <? ///php endif; 
              ?> -->
        <?php o6_render_footer($ty, $hero, $o6_pg, $o6_total_pages); ?>
      </div>
      <?php $o6_pg++; ?>
    <?php endif; ?>


    <!-- PAGE — DAY WISE ITINERARY -->
    <?php if (!empty($itin)) : ?>
      <div class="page itinerary-page">
        <?php o6_render_page_header($hero, 'Day Wise Itinerary'); ?>

        <div class="content-container">
          <h4 class="block-title" style="margin-top:10px;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="20" height="20" fill="var(--japan-crimson)">
              <path d="M128 0c17.7 0 32 14.3 32 32V64H288V32c0-17.7 14.3 32 32-32s32 14.3 32 32V64H384c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128C0 92.7 28.7 64 64 64H96V32c0-17.7 14.3-32 32-32zM64 192V448H384V192H64zm64 64h64v64H128V256zm128 0h64v64H256V256z" />
            </svg>
            DAY WISE ITINERARY
          </h4>

          <div class="itinerary-timeline-vertical">
            <?php foreach ($itin as $day) :
              $day_title = o6nv($day['special_attraction'], o6nv($day['city'], 'Sightseeing'));
              $dummy_day_img = BASE_URL . 'images/itinerary.png';

              $o6_day_photo = isset($day['image']) ? trim($day['image']) : '';

              if ($o6_day_photo == '' || stripos($o6_day_photo, 'dummy') !== false) {
                $o6_dimg = $dummy_day_img;
              } else {
                $o6_dimg = o6_media_url($o6_day_photo);
              }
            ?>
              <div class="timeline-day-block">
                <div class="day-spine-node-badge">
                  <span class="word">DAY</span>
                  <span class="num"><?= o6e(o6nv($day['day_number'], '')) ?></span>
                </div>

                <div class="itinerary-split-card layout-split-card">
                  <div class="card-left-stripbg">
                    <!-- <img src="<//?= o6e($o6_dimg) ?>" alt="Day <//?= o6e(o6nv($day['day_number'], '')) ?>"> -->
                    <img src="<?= o6e($o6_dimg) ?>"
                      alt="<?= o6e($day_title) ?>"
                      class="itinerary-img">
                  </div>

                  <div class="card-right-datacore">
                    <div class="day-header-meta">
                      <span class="date-string"><?= o6e(o6nv($day['date'], '')) ?></span>
                      <h4><?= o6e($day_title) ?></h4>
                    </div>

                    <p class="day-narrative-text"><?= o6e(o6nv($day['detailed_programme'], '')) ?></p>

                    <?php if (!empty($day['meal_plan']) || !empty($day['overnight_stay'])) : ?>
                      <p class="day-narrative-text" style="margin-top:6px;font-size:0.62rem;">
                        <?php if (!empty($day['meal_plan'])) : ?>
                          Meals: <?= o6e($day['meal_plan']) ?>
                        <?php endif; ?>

                        <?php if (!empty($day['overnight_stay'])) : ?>
                          <?= !empty($day['meal_plan']) ? ' · ' : '' ?>Stay: <?= o6e($day['overnight_stay']) ?>
                        <?php endif; ?>
                      </p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <?php o6_render_footer($ty, $hero, $o6_pg, $o6_total_pages); ?>
      </div>
      <?php $o6_pg++; ?>
    <?php endif; ?>

    <!-- PAGE 7 — INCLUSIONS / EXCLUSIONS -->
    <div class="page">
      <?php o6_render_page_header($hero, "What's Included / What's Excluded"); ?>

      <div class="content-container">
        <div class="scope-matrix-grid">
          <div class="matrix-column-wrapper">
            <div class="matrix-header inclusions-bg">
              <div class="header-badge-circle"><!-- fa-solid fa-check -->
                <svg xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 448 512"
                  width="15"
                  height="15"
                  fill="rgba(255, 255, 255, 0.15)">
                  <path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.2 0z" />
                </svg>
              </div>
              <h3>What's Included</h3>
            </div>
            <div class="matrix-list-body">
              <!-- <span class="matrix-scope-tag">PACKAGE SCOPE</span> -->
              <ul>
                <?php foreach ($o6_included as $item) : ?>
                  <li><i class="fa-regular fa-circle-check item-inc-icon"></i> <?= o6e($item) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>

          <div class="matrix-column-wrapper">
            <div class="matrix-header exclusions-bg">
              <div class="header-badge-circle"><i class="fa-solid fa-xmark"></i></div>
              <h3>What's Excluded</h3>
            </div>
            <div class="matrix-list-body">
              <!-- <span class="matrix-scope-tag text-red">OUT OF SCOPE</span> -->
              <ul>
                <?php foreach ($o6_excluded as $item) : ?>
                  <li><i class="fa-regular fa-circle-xmark item-exc-icon"></i> <?= o6e($item) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <?php o6_render_footer($ty, $hero, $o6_pg, $o6_total_pages); ?>
    </div>
    <?php $o6_pg++; ?>

    <!-- PAGE 8 — COSTING & PAYMENT -->
    <div class="page">
      <?php
      $o6_costing_type = isset($cost['costing_type_label']) ? strtolower(trim($cost['costing_type_label'])) : '';
      $o6_is_per_person = ($o6_costing_type == 'per person');
      $o6_pp = isset($cost['computed']['per_person']) ? $cost['computed']['per_person'] : array();
      ?>
      <?php o6_render_page_header($hero, 'Costing Details'); ?>

      <div class="content-container">
        <div class="section-intro"></br>
          <h2 class="main-section-title">Your Investment Matrix</h2>
          <p class="section-desc">Consolidated cost structure breakdown per package tier definitions.</p>
        </div>
        <?php if (!$o6_is_per_person) { ?>
          <table class="financial-breakdown-table card-container">
            <thead>
              <tr>
                <th>PACKAGE TYPE</th>
                <th>TOUR COST</th>
                <th>TAX</th>
                <th>TCS</th>
                <th>TRAVEL COST</th>
                <th>GRAND TOTAL</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($o6_cost_grp as $ci => $row) :
                $tax_amount = function_exists('gqd_tax_display_amount') ? gqd_tax_display_amount($row) : (isset($row['tax_amount_display']) ? $row['tax_amount_display'] : '0.00');
                $is_rec = (stripos(o6nv($row['package_type'], ''), 'premium') !== false)
                  || (stripos(o6nv($row['package_type'], ''), 'recommended') !== false)
                  || (stripos(o6nv($row['package_type'], ''), 'royal') !== false)
                  || ($ci === 1 && count($o6_cost_grp) > 1);
              ?>
                <tr<?= $is_rec ? ' class="highlighted-row"' : '' ?>>
                  <td>
                    <strong><?= o6e(o6nv($row['package_type'], 'Package')) ?></strong>
                    <?php if ($is_rec) : ?> <span class="table-rec-badge">RECOMMENDED</span><?php endif; ?>
                  </td>
                  <td><?= o6e(o6nv($row['tour_cost_display'], '0')) ?></td>
                  <td><?= o6e($tax_amount) ?></td>
                  <td><?= o6e(o6nv($row['tcs_display'], '0')) ?></td>
                  <td><?= o6e(o6nv($row['travel_display'], '0')) ?></td>
                  <td class="final-total-field<?= $is_rec ? ' focus-color' : '' ?>"><?= function_exists('gqd_total_with_before_discount') ? gqd_total_with_before_discount($row, 'total_display', 'before_discount_display', 'o6e') : o6e(o6nv($row['total_display'], '0')) ?></td>
                  </tr>
                <?php endforeach; ?>
            </tbody>
          </table>
        <?php } else { ?>
          <table class="financial-breakdown-table card-container">
            <thead>
              <tr>
                <th>PACKAGE</th>
                <th>ADULT</th>
                <th>CWB</th>
                <th>CWOB</th>
                <th>INFANT</th>
                <th>TAX</th>
                <th>TCS</th>
                <th>VISA</th>
                <th>GUIDE</th>
                <th>MISC</th>
              </tr>
            </thead>

            <tbody>
              <?php foreach ($o6_pp as $pp) :

                $tax_amount = function_exists('gqd_tax_display_amount') ? gqd_tax_display_amount($pp) : (isset($pp['tax_amount_display']) ? $pp['tax_amount_display'] : '0.00');
              ?>
                <tr>
                  <td><?= o6e(o6nv($pp['package_type'], 'Package')) ?></td>
                  <td><?= o6e(o6nv($pp['pp_adult_display'], '0')) ?></td>
                  <td><?= o6e(o6nv($pp['pp_cwb_display'], '0')) ?></td>
                  <td><?= o6e(o6nv($pp['pp_cwnb_display'], '0')) ?></td>
                  <td><?= o6e(o6nv($pp['pp_infant_display'], '0')) ?></td>

                  <td><?= o6e($tax_amount) ?></td>

                  <td><?= o6e(o6nv($pp['tcs_display'], '0')) ?></td>
                  <td><?= o6e(o6nv($pp['visa_display'], '0')) ?></td>
                  <td><?= o6e(o6nv($pp['guide_display'], '0')) ?></td>
                  <td><?= o6e(o6nv($pp['misc_display'], '0')) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php } ?>

        <p class="table-disclaimer-text"><i class="fa-solid fa-circle-info"></i> Notes: <?= o6e(o6nv(isset($incx['note']) ? $incx['note'] : '', 'Rates are subject to availability at the time of final confirmation.')) ?></p>

        <h4 class="block-title" style="margin-top: 15px;">PAYMENT INFORMATION <span class="sub-label-term">— How to pay</span></h4>

        <div class="payment-infrastructure-grid">
          <div class="payment-solid-dark-box">
            <span class="top-micro-caption">BANK TRANSFER SETUP</span>
            <h2><?= o6e(o6nv($bank['account_name'], o6nv($ty['company_name'], $o6_company))) ?></h2>
            <div class="solid-grid-meta">
              <div class="meta-cell">
                <span class="label">ACCOUNT NUMBER</span>
                <span class="val font-mono"><?= o6e(o6nv($bank['account_no'], 'NA')) ?></span>
              </div>
              <div class="meta-cell">
                <span class="label">BANK ENTITY</span>
                <span class="val"><?= o6e(o6nv($bank['bank_name'], 'NA')) ?></span>
              </div>
              <div class="meta-cell">
                <span class="label">LOCATION BRANCH</span>
                <span class="val"><?= o6e(o6nv($bank['branch_name'], 'NA')) ?></span>
              </div>
              <div class="meta-cell">
                <span class="label">IFSC CODE</span>
                <span class="val font-mono"><?= o6e(o6nv($bank['ifsc_code'], o6nv($bank['swift_code'], 'NA'))) ?></span>
              </div>
            </div>
          </div>

          <div class="payment-right-stack-layout">
            <div class="qr-code-white-card card-container">
              <?php if (!empty($bank['qr_html'])) : ?>
                <?= $bank['qr_html'] ?>
              <?php elseif ($o6_qr_url !== '') : ?>
                <img src="<?= o6e($o6_qr_url) ?>" alt="Payment QR" style="width:90px;height:90px;object-fit:contain;" />
              <?php else : ?>
                <div class="simulated-qr-pattern"></div>
              <?php endif; ?>
              <span class="qr-caption">SCAN &amp; PAY VIA UPI</span>
              <?php if (!empty($bank['upi_id'])) : ?>
                <p class="qr-upi-text"><?= o6e($bank['upi_id']) ?></p>
              <?php endif; ?>
            </div>

            <div class="payment-notice-tint-box">
              <h5>PAYMENT INSTRUCTIONS</h5>
              <p style="font-size: 0.65rem; line-height: 1.3;"><?= o6e(implode(' ', array_slice($o6_pay_notes, 0, 4))) ?></p>
            </div>
          </div>
        </div>
      </div>

      <?php o6_render_footer($ty, $hero, $o6_pg, $o6_total_pages); ?>
    </div>
    <?php $o6_pg++; ?>

    <!-- PAGE 9 — TESTIMONIALS -->
    <div class="page">
      <?php o6_render_page_header($hero, 'What Our Travellers Say'); ?>

      <div class="content-container">
        <div class="section-intro"></br>
          <h2 class="main-section-title">Client Feedback Profiles</h2>
          <p class="section-desc">A consistent record of exceptional holiday delivery across global sectors.</p>
        </div>

        <div class="reviews-masonry-grid">
          <?php if (!empty($testimonials)) :
            $o6_ti = 0;
            foreach ($testimonials as $t) :
              // if ($o6_ti >= 3) {
              //   break;
              // }
              // $o6_ti++;
              $photo = o6_media_url(isset($t['photo']) ? $t['photo'] : '');
          ?>
              <div class="testimonial-card-v2 card-container">
                <div class="quote-signature-mark">&ldquo;</div>
                <div class="star-row-layout">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <p class="review-body-text"><?= o6e(o6nv($t['review'], '')) ?></p>
                <div class="reviewer-footer-row">
                  <?php if ($photo !== '') : ?>
                    <img class="circle-avatar-initials" src="<?= o6e($photo) ?>" alt="<?= o6e(o6nv($t['name'], '')) ?>" style="object-fit:cover;padding:0;" />
                  <?php else : ?>
                    <div class="circle-avatar-initials"><?= o6e(o6_initials(o6nv($t['name'], 'T'))) ?></div>
                  <?php endif; ?>
                  <div class="reviewer-identity">
                    <h4><?= o6e(o6nv($t['name'], 'Traveller')) ?></h4>
                    <span class="destination-tag"><?= o6e(strtoupper(o6nv($t['designation'], ''))) ?></span>
                  </div>
                </div>
              </div>
            <?php
            endforeach;
          else :
            ?>
            <div class="testimonial-card-v2 card-container">
              <p class="review-body-text">Customer testimonials can be managed from Quotation Builder settings.</p>
            </div>
          <?php endif; ?>
        </div>

        <div class="reputation-tracker-bar">
          <i class="fa-brands fa-google"></i>
          <span><strong><?= o6e($o6_review_count) ?> GOOGLE REVIEWS</strong> — <?= o6e(strtoupper($o6_google_rating)) ?> AVERAGE RATING</span>
        </div>
      </div>

      <?php o6_render_footer($ty, $hero, $o6_pg, $o6_total_pages); ?>
    </div>
    <?php $o6_pg++; ?>

    <!-- PAGE 10 — TERMS -->
    <div class="page">
      <?php o6_render_page_header($hero, 'Terms & Conditions'); ?>

      <div class="content-container fine-print-body">
        <div class="section-intro"></br>
          <h2 class="main-section-title">THE FINE PRINT</h2>
          <p class="section-desc"><?= o6e(o6nv($terms['title'], 'Standard terms governing travel packaging arrangements.')) ?></p>
        </div>

        <div class="legal-clauses-two-column">
          <?php foreach ($o6_term_clauses as $clause) : ?>
            <div class="clause-node">
              <h5><?= o6e($clause['title']) ?></h5>
              <p><?= o6e($clause['body']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <?php o6_render_footer($ty, $hero, $o6_pg, $o6_total_pages, o6nv($ty['company_address'], '')); ?>
    </div>
    <?php $o6_pg++; ?>

    <!-- PAGE 11 — THANK YOU -->
    <div class="page thank-you-page">
      <div class="thank-you-main-core">
        <h1 class="thank-you-headline">Thank You</h1>
        <p class="thank-you-subtext">for choosing us as your premium travel partner</p>
        <div class="closing-divider-diamond"></div>
        <p class="closing-personal-note">Dear <?= o6e($o6_first) ?>, we truly appreciate your trust in <?= o6e(o6nv($ty['company_name'], $o6_company)) ?>. Our team is committed to crafting an unforgettable journey for you. Should you have any questions, please reach out anytime.</p>
      </div>

      <div class="corporate-metrics-strip card-container">
        <div class="metric-node">
          <h3><?= o6e($o6_review_count) ?></h3>
          <p>GOOGLE REVIEWS</p>
        </div>
        <div class="metric-node">
          <h3><?= o6e($o6_google_rating) ?></h3>
          <p>AVERAGE RATING</p>
        </div>
        <div class="metric-node">
          <h3><?= o6e($o6_years_exp) ?></h3>
          <p>YEARS EXPERIENCE</p>
        </div>
        <div class="metric-node">
          <h3><?= o6e($o6_traveller_cnt) ?></h3>
          <p>HAPPY TRAVELLERS</p>
        </div>
      </div>

      <div class="closing-contact-card">

        <?php if ($o6_consult_phone !== '') : ?>
          <div class="contact-channel-row">

            <span>
              <i class="fa-solid fa-phone"></i>
              <a href="tel:<?= preg_replace('/\s+/', '', $o6_consult_phone) ?>">
                <?= o6e($o6_consult_phone) ?>
              </a>
            </span>

            <span>
              <i class="fa-brands fa-whatsapp"></i>
              <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $o6_consult_phone) ?>">
                WhatsApp <?= o6e($o6_consult_phone) ?>
              </a>
            </span>

          </div>
        <?php endif; ?>

        <div class="contact-channel-row">

          <?php if ($o6_consult_email !== '') : ?>
            <span>
              <i class="fa-solid fa-envelope"></i>
              <a href="mailto:<?= o6e($o6_consult_email) ?>">
                <?= o6e($o6_consult_email) ?>
              </a>
            </span>
          <?php endif; ?>

          <?php if (o6nv($ty['website'], '') !== '') : ?>
            <span>
              <i class="fa-solid fa-globe"></i>
              <a href="<?= o6e($ty['website']) ?>">
                <?= o6e($ty['website']) ?>
              </a>
            </span>
          <?php endif; ?>

        </div>

      </div>

      <div class="page-footer closing-version">
        <span class="footer-meta-text">PREPARED BY <?= o6e(strtoupper(o6nv($ty['prepared_by'], o6nv($hero['login_user'], 'TEAM')))) ?> TRAVEL CONSULTANT</span>
        <span class="page-number"><?= o6e(str_pad((string) $o6_pg, 2, '0', STR_PAD_LEFT) . '/' . str_pad((string) $o6_total_pages, 2, '0', STR_PAD_LEFT)) ?></span>
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