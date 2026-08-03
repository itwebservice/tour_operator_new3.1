<?php

/**
 * ============================================================================
 * OPTION-2  (quotation_html_2)  —  Package Tour Quotation
 * ----------------------------------------------------------------------------
 * Faithful render of Final-Designs/Option-2-Done/index.html using the generic
 * JSON data engine (get_generic_quotation_data).
 *   .../quotation_html/quotation_html_2/fit_quotation_html.php?quotation_id=ID
 * ============================================================================
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

$hero    = $q['hero'];
$ov      = $q['tour_overview'];
$hotels  = $q['hotels'];
$flights = $q['flights'];
// ========= Dipti
$trains  = isset($q['trains']) ? $q['trains'] : array();
$acts    = isset($q['activities']) ? $q['activities'] : array();
// ================
$vehs    = $q['vehicles'];
$itin    = $q['itinerary'];
$incx    = $q['inclusion_exclusion'];
$cost    = $q['costing'];
$bank    = $q['bank_details'];
$terms   = $q['terms_conditions'];
$ty      = $q['thank_you'];
$assets  = "assets/";

// $testimonials = array();
// $o2_cfg = function_exists('gqb_get_config') ? gqb_get_config() : array();
// if (!empty($o2_cfg['testimonials']) && is_array($o2_cfg['testimonials'])) {
//   $testimonials = $o2_cfg['testimonials'];
// }

// ============== Dipti
$testimonials = array();
$testimonials = isset($q['testimonials']) && is_array($q['testimonials'])
  ? $q['testimonials'] : array();

$social_links = array();

$o2_cfg = function_exists('gqb_get_config') ? gqb_get_config() : array();

// if (!empty($o2_cfg['testimonials']) && is_array($o2_cfg['testimonials'])) {
//   $testimonials = $o2_cfg['testimonials'];
// }
if (!empty($o2_cfg['social_links']) && is_array($o2_cfg['social_links'])) {
  $social_links = $o2_cfg['social_links'];
}
// ===================

$o2_page = 0;

if (!function_exists('o2e')) {
  function o2e($v)
  {
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
  }
}
if (!function_exists('o2nv')) {
  function o2nv($v, $f = '')
  {
    return ($v !== null && $v !== '') ? $v : $f;
  }
}
if (!function_exists('o2img')) {
  function o2img($url, $fallback)
  {
    return (is_string($url) && trim($url) !== '' && stripos($url, 'dummy') === false) ? $url : $fallback;
  }
}
// if (!function_exists('o2_list_items')) {
//   function o2_list_items($html, $fallback)
//   {
//     $text = trim(strip_tags((string) $html));
//     $items = preg_split('/\r\n|\r|\n|•|\x{2022}/u', $text);
//     $items = array_values(array_filter(array_map('trim', (array) $items)));
//     return !empty($items) ? $items : array($fallback);
//   }
// }

if (!function_exists('o2_list_items')) {
  function o2_list_items($html, $fallback)
  {
    $html = (string)$html;

    $html = preg_replace('/<\/li\s*>/i', "\n", $html);
    $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
    $html = preg_replace('/<\/p\s*>/i', "\n", $html);

    $text = trim(strip_tags($html));
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

    $items = preg_split('/\r\n|\r|\n|•|\x{2022}/u', $text);
    $items = array_values(array_filter(array_map('trim', $items)));

    return !empty($items) ? $items : array($fallback);
  }
}


if (!function_exists('o2_stars')) {
  function o2_stars($rating)
  {
    $n = 5;
    if (preg_match('/(\d)/', (string) $rating, $m)) {
      $n = min(5, max(1, (int) $m[1]));
    }
    return str_repeat('★', $n);
  }
}
if (!function_exists('o2_air_code')) {
  function o2_air_code($city)
  {
    $city = trim((string) $city);
    if (preg_match('/\(([A-Z]{3})\)/', $city, $m)) {
      return $m[1];
    }
    $clean = preg_replace('/[^A-Za-z]/', '', $city);
    return $clean !== '' ? strtoupper(substr($clean, 0, 3)) : '—';
  }
}
if (!function_exists('o2_flight_label')) {
  function o2_flight_label($index)
  {
    $labels = array('Outbound', 'Return');
    return isset($labels[$index]) ? $labels[$index] : ('Sector ' . ($index + 1));
  }
}
if (!function_exists('o2_testi_photo')) {
  function o2_testi_photo($photo)
  {
    $photo = isset($photo) ? trim((string) $photo) : '';
    if ($photo === '') {
      return '';
    }
    $photo = str_replace('\\', '/', $photo);
    if (strpos($photo, 'http://') !== 0 && strpos($photo, 'https://') !== 0) {
      $photo = BASE_URL . ltrim($photo, '/');
    }
    return $photo;
  }
}
if (!function_exists('o2_next_page')) {
  function o2_next_page()
  {
    global $o2_page;
    return ++$o2_page;
  }
}
if (!function_exists('o2_foot')) {
  function o2_foot()
  {
    global $o2_company, $o2_dest, $o2_page, $o2_total_pages;
    $pg = o2_next_page();
    echo '<footer class="foot">';
    echo '<span class="foot__mark"><span class="dot"></span> ' . o2e($o2_company) . ' · ' . o2e($o2_dest) . ' Package</span>';
    echo '<span><b>' . str_pad((string) $pg, 2, '0', STR_PAD_LEFT) . '</b> &nbsp;/&nbsp; ' . str_pad((string) $o2_total_pages, 2, '0', STR_PAD_LEFT) . '</span>';
    echo '</footer>';
  }
}
if (!function_exists('o2_strip')) {
  function o2_strip($eyebrow, $title, $no_html, $banner = '')
  {
    global $assets, $o2_banner;
    $banner_src = o2e($banner !== '' ? $banner : o2nv($o2_banner, $assets . 'banner.jpg'));
    echo '<header class="strip">';
    echo '<img class="strip__img" src="' . $banner_src . '" alt="Banner">';
    echo '<div class="strip__shade"></div>';
    echo '<div class="strip__content">';
    echo '<div><div class="strip__eyebrow">' . $eyebrow . '</div><h2 class="strip__title">' . $title . '</h2></div>';
    echo '<div class="strip__no">' . $no_html . '</div>';
    echo '</div><div class="strip__torn"></div></header>';
  }
}

$o2_dest          = o2nv($ov['destination'], o2nv($hero['tour_name'], 'Tour'));
// $o2_pkg           = o2nv($ov['package_type_label'], o2nv(!empty($hotels[0]['package_type']) ? $hotels[0]['package_type'] : '', 'Package'));
$o2_pkg = o2nv(
  !empty($q['package_types_label']) ? $q['package_types_label'] : '',
  o2nv(
    !empty($hotels[0]['package_type']) ? $hotels[0]['package_type'] : '',
    o2nv($ov['package_type_label'], 'Package')
  )
);
$o2_client        = o2nv($ov['client_name'], o2nv($hero['client_name'], ''));
$o2_client_first  = trim((string) preg_replace('/\s+.*/', '', $o2_client));
$o2_travel_dates  = trim(o2nv($ov['travel_from'], '') . (o2nv($ov['travel_to'], '') !== '' ? ' – ' . o2nv($ov['travel_to'], '') : ''));
$o2_guests        = o2nv($ov['guest_count'], '');
$o2_company       = o2nv($hero['company_name'], 'Travel Company');
$o2_tagline       = 'Luxury Travel';
$o2_google_rating = '4.9';
$o2_review_count  = '2,400+';
$o2_traveller_cnt = '18,500+';

$o2_itin_list   = is_array($itin) ? $itin : array();
// $o2_itin_pages  = !empty($o2_itin_list) ? array_chunk($o2_itin_list, 3) : array();
$o2_itin_pages  = !empty($o2_itin_list) ? array($o2_itin_list) : array();
$o2_total_pages = 4 + count($o2_itin_pages) + 5;

$o2_banner = o2img(isset($hero['cover_image']) ? $hero['cover_image'] : '', $assets . 'banner.jpg');
$o2_hero   = o2img(isset($hero['cover_image']) ? $hero['cover_image'] : '', $assets . 'hero.jpg');


// $o2_logo   = o2img(isset($hero['company_logo']) ? $hero['company_logo'] : '', $assets . 'logo.png');
$o2_logo = BASE_URL . 'images/logo-circle.png?v=' . time();
// $o2_round  = o2img((!empty($o2_itin_list[0]['image']) ? $o2_itin_list[0]['image'] : ''), $assets . 'day.jpg');
// =========== Dipti
$o2_round = o2img(
  isset($hero['destination_5th_gallery_image']) ? $hero['destination_5th_gallery_image'] : '',
  $assets . 'day.jpg'
);
// =================
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= o2e($o2_dest) ?> Tour Package Quotation — <?= o2e($o2_company) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="option2.css">
</head>

<body class="cover-a">
  <div class="doc">

    <!-- PAGE 1 · COVER -->
    <section class="page cover">
      <div class="cv cv-a">
        <img class="cv-a__photo" src="<?= o2e($o2_hero) ?>" alt="<?= o2e($o2_dest) ?>">
        <div class="cv-a__veil"></div>
        <div class="cv-a__inner">
          <div class="logo">
            <img class="logo__slot" src="<?= o2e($o2_logo) ?>" alt="Company logo">
            <!-- <div>
              <div class="logo__name"><//?= o2e($o2_company) ?></div>
              <div class="logo__tag"><//?= o2e($o2_tagline) ?></div>
            </div> -->
          </div>
          <div class="cv-a__eyebrow"><?= o2e(o2nv($hero['package_name'], o2nv($hero['tour_name'], 'Exclusive Tour'))) ?></div>
          <h1 class="cv-a__dest"><?= o2e($o2_dest) ?></h1>
          <div class="cv-a__badge"><?= o2e(o2nv($hero['duration_label'], o2nv($ov['duration_label'], ''))) ?></div>

          <img class="cv-a__round" src="<?= o2e($o2_round) ?>" alt="Highlight">
        </div>
        <div class="cv-a__for">
          <div class="l">Prepared Exclusively For</div>
          <div class="n"><?= o2e($o2_client) ?></div>
        </div>
        <div class="incstrip">
          <div class="incstrip__row">

            <!-- Flight -->
            <div class="incitem">
              <span class="ico">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"></path>
                </svg>
              </span>
              <span>DOMESTIC &amp;<br>INTERNATIONAL FLIGHTS</span>
            </div>

            <!-- Hotel -->
            <div class="incitem">
              <span class="ico">
                <svg xmlns="http://www.w3.org/2000/svg"
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round">
                  <path d="M10 22v-6.57" />
                  <path d="M12 11h.01" />
                  <path d="M12 7h.01" />
                  <path d="M14 15.43h.01" />
                  <path d="M15 22v-6.57" />
                  <path d="M15 16a5 5 0 0 0-6 0" />
                  <path d="M16 11h.01" />
                  <path d="M16 7h.01" />
                  <path d="M8 11h.01" />
                  <path d="M8 7h.01" />
                  <rect x="4" y="2" width="16" height="20" rx="2" />
                </svg>
              </span>
              <span>Premium<br>Hotels</span>
            </div>

            <!-- Breakfast -->
            <div class="incitem">
              <span class="ico">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M3 2v7c0 2.2 1.8 4 4 4h1v9"></path>
                  <path d="M7 2v11"></path>
                  <path d="M11 2v11"></path>
                  <path d="M18 2v20"></path>
                  <path d="M21 2c0 4-1 7-3 7s-3-3-3-7"></path>
                </svg>
              </span>
              <span>QUALITY<br>MEALS</span>
            </div>

            <!-- Guide -->
            <div class="incitem">
              <span class="ico">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <path d="m16.24 7.76-1.8 5.4a2 2 0 0 1-1.27 1.27l-5.4 1.8 1.8-5.4a2 2 0 0 1 1.27-1.27z"></path>
                </svg>
              </span>
              <span>TOUR<br>SIGHTSEEING</span>
            </div>

            <!-- Transfer -->
            <div class="incitem">
              <span class="ico">
                <svg xmlns="http://www.w3.org/2000/svg"
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round">
                  <path d="M14 16H9m10 0h2m-2 0a2 2 0 1 1-4 0m4 0H9m0 0a2 2 0 1 1-4 0m4 0H5m0 0H3v-4l2-5h11l3 5v4h-2" />
                  <circle cx="7" cy="16" r="2" />
                  <circle cx="17" cy="16" r="2" />
                </svg>
              </span>
              <span>Private<br>Transfers</span>
            </div>

            <!-- Visa -->
            <div class="incitem">
              <span class="ico">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M8 2h8l4 4v16H4V2z"></path>
                  <path d="M8 2v6h8"></path>
                  <path d="M8 13h8"></path>
                  <path d="M8 17h5"></path>
                </svg>
              </span>
              <span>Visa &amp;<br>Insurance</span>
            </div>

          </div>
          <div class="incstrip__foot"><b>INCLUSION:</b>&nbsp; <?= o2e(trim(strip_tags(o2nv(isset($incx['included']) ? $incx['included'] : '', 'Flights, Hotels, Transfers &amp; Sightseeing'))) !== '' ? 'See detailed inclusions inside' : 'As per itinerary') ?></div>
        </div>
      </div>
    </section>

    <!-- PAGE 2 · OVERVIEW -->
    <section class="page">
      <?php o2_strip(o2e($o2_company) . ' · Travel Proposal', 'Your Journey', 'Quotation&nbsp; <b>' . o2e(o2nv($hero['quotation_code'], '')) . '</b>'); ?>
      <div class="page__wm"></div>
      <div class="page__body">
        <div class="banner">
          <div class="k">A Personalized Travel Experience</div>
          <h2 style="font-size: 20px;">Exclusively designed for <em><?= o2e($o2_client) ?></em> — an unforgettable journey through <?= o2e($o2_dest) ?>.</h2>
        </div>
        <p class="greet" style="margin:7mm 0 0; font-size: 15px;">
          Dear <b><?= o2e($o2_client_first !== '' ? $o2_client_first : $o2_client) ?></b>,<br>
          Thank you for choosing <?= o2e($o2_company) ?> for your upcoming journey. We are delighted to present this carefully
          crafted travel proposal, designed to deliver memorable experiences, seamless arrangements and exceptional
          hospitality at every step of your trip.
        </p>
        <div style="margin-top:7mm">
          <p class="kicker">Tour Overview</p>
          <div class="ov-grid" style="margin-top:11px">
            <div class="card ov"><span class="ic">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <path d="M14 2v6h6"></path>
                  <path d="M16 13H8"></path>
                  <path d="M16 17H8"></path>
                  <path d="M10 9H8"></path>
                </svg>
              </span>
              <div>
                <div class="t">Quotation ID</div>
                <div class="v sm"><?= o2e(o2nv($hero['quotation_code'], '')) ?></div>
              </div>
            </div>
            <div class="card ov"><span class="ic">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <path d="M2 12h20"></path>
                  <path d="M12 2a15 15 0 0 1 0 20"></path>
                  <path d="M12 2a15 15 0 0 0 0 20"></path>
                </svg>
              </span>
              <div>
                <div class="t">Enquiry ID</div>
                <div class="v sm"><?= o2e(o2nv($ov['enquiry_code'], o2nv($ov['enquiry_id'], ''))) ?></div>
              </div>
            </div>
            <div class="card ov"><span class="ic">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
              </span>
              <div>
                <div class="t">Quotation Date</div>
                <div class="v sm"><?= o2e(o2nv($ov['quotation_date'], '')) ?></div>
              </div>
            </div>
            <div class="card ov"><span class="ic">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"></path>
                </svg>
              </span>
              <div>
                <div class="t">Travel Dates</div>
                <div class="v sm"><?= o2nv($ov['travel_from'], '') ?> &ndash; <br/><?= o2nv($ov['travel_to'], '') ?></div>
              </div>
            </div>
            <div class="card ov"><span class="ic">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 3a6 6 0 1 0 9 9A9 9 0 1 1 12 3z"></path>
                </svg>
              </span>
              <div>
                <div class="t">Duration</div>
                <div class="v sm"><?= o2e(o2nv($ov['duration_label'], o2nv($hero['duration_label'], ''))) ?></div>
              </div>
            </div>
            <div class="card ov"><span class="ic">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </span>
              <div>
                <div class="t">Guests</div>
                <div class="v sm"><?= o2e($o2_guests) ?></div>
              </div>
            </div>
          </div>
        </div>
        <div style="margin-top:7mm">
          <p class="kicker">Prepared For</p>
          <div class="prep" style="margin-top:11px">
            <div class="card prep__card prep__contact-card">
              <img class="prep__photo" src="<?= o2e($assets . 'person.jpg') ?>" alt="Client">
              <div class="meta">

                <?php if (o2nv($ov['customer_email'], '') !== ''): ?>
                  <span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                      <path d="m3 7 9 6 9-6"></path>
                    </svg>
                    <a href="mailto:<?= o2e($ov['customer_email']) ?>">
                      <?= o2e($ov['customer_email']) ?>
                    </a>
                  </span>
                <?php endif; ?>

                <?php if (o2nv($ov['customer_mobile'], '') !== ''): ?>
                  <span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.9.34 1.78.65 2.62a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.46-1.17a2 2 0 0 1 2.11-.45c.84.31 1.72.53 2.62.65A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    <a href="tel:<?= o2e(preg_replace('/\D+/', '', $ov['customer_mobile'])) ?>">
                      <?= o2e($ov['customer_mobile']) ?>
                    </a>
                  </span>
                <?php endif; ?>

              </div>
            </div>
            <div class="card prep__card" style="flex:.85">
              <!-- <span class="ic ic--navy" style="width:46px;height:46px"><i class="fa-solid fa-star" style="font-size:20px"></i></span> -->
              <span class="ic ic--navy" style="width:46px;height:46px;display:flex;align-items:center;justify-content:center">
                <svg xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                  stroke="currentColor"
                  stroke-width="1">
                  <path d="M11.48 3.5a.56.56 0 0 1 1.04 0l2.1 5.34a1 1 0 0 0 .84.63l5.76.42a.56.56 0 0 1 .31.98l-4.4 3.76a1 1 0 0 0-.33.95l1.38 5.62a.56.56 0 0 1-.84.61L12 18.54l-4.95 3.27a.56.56 0 0 1-.84-.61l1.38-5.62a1 1 0 0 0-.33-.95l-4.4-3.76a.56.56 0 0 1 .31-.98l5.76-.42a1 1 0 0 0 .84-.63z" />
                </svg>
              </span>
              <div>
                <div class="t" style="font-size:11px;letter-spacing:.16em;text-transform:uppercase;color:var(--muted)">Package Type</div>
                 <div class="nm" style="font-size:16px;margin-top:2px"><?= o2e(o2nv($o2_pkg, 'Package')) ?></div>
                <div class="meta" style="margin-top:3px"><span style="color:var(--gold-deep);font-weight:500; font-size:14px;">Recommended for your group</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php o2_foot(); ?>
    </section>

    <!-- PAGE 3 · HOTELS -->
    <section class="page print-section<?= count((array) $hotels) > 4 ? ' page-flow' : '' ?>">
      <?php o2_strip(
        'Where You\'ll Stay',
        'Accommodation',
        o2e(strtoupper($o2_pkg)) . ' <b>PACKAGE</b>'
      ); ?>
      <div class="page__wm"></div>
      <div class="page__body">
        <div class="sec-h">
          <div>
            <span class="gold-rule" style="display:block;margin-bottom:7px"></span>
            <span class="t">Handpicked Stays Across <?= o2e($o2_dest) ?></span>
          </div>
          <span class="sec-tag"><?= o2e($o2_pkg) ?> Package</span>
        </div>
        <div class="hotel-grid">
          <?php
          $o2_hi = 0;
          foreach ((array) $hotels as $h):
            $o2_hi++;
            // $o2_himg = o2img(isset($h['hotel_photo']) ? $h['hotel_photo'] : '', $assets . 'hotel-' . ((($o2_hi - 1) % 3) + 1) . '.jpg');
            $dummy_hotel_img = BASE_URL . 'images/hotel.png';

            $o2_hotel_photo = isset($h['hotel_photo']) ? trim($h['hotel_photo']) : '';

            if ($o2_hotel_photo == '' || stripos($o2_hotel_photo, 'dummy') !== false) {
              $o2_himg = $dummy_hotel_img;
            } else {
              $o2_himg = o2img($o2_hotel_photo, $dummy_hotel_img);
            }
          ?>
            <div class="card hotel">
              <img class="hotel__img" src="<?= o2e($o2_himg) ?>" alt="Hotel">
              <div class="hotel__b">
                <div class="hotel__city"><i class="fa-solid fa-location-dot"></i> <?= o2e(o2nv($h['hotel_city'], '')) ?></div>
                <div class="hotel__name"><?= o2e(o2nv($h['hotel_name'], 'Hotel')) ?></div>
                <div class="hotel__cat"><?= o2e(o2nv($h['room_category'], o2nv($h['room_type'], o2nv($h['meal_plan'], '')))) ?></div>
                <div class="hotel__stars"><?= o2_stars(isset($h['rating']) ? $h['rating'] : 5) ?></div>
                <div class="hotel__dates">
                  <div class="hotel__dt">
                    <div class="l">Check-In</div>
                    <div class="d"><?= o2e(o2nv($h['check_in'], 'NA')) ?></div>
                  </div>
                  <div class="hotel__dt">
                    <div class="l">Check-Out</div>
                    <div class="d"><?= o2e(o2nv($h['check_out'], 'NA')) ?></div>
                  </div>
                </div>
                <?php if (!empty($h['meal_plan'])): ?>
                  <div class="amen">
                    <span><i class="fa-solid fa-mug-saucer"></i> <?= o2e($h['meal_plan']) ?></span>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
          <?php if (empty($hotels)): ?>
            <div class="card" style="padding:14px 15px;grid-column:1/-1;text-align:center;color:var(--muted)">No accommodation details available.</div>
          <?php else: ?>
            <div class="card" style="padding:14px 15px;display:flex;flex-direction:column;justify-content:center">
              <div class="hotel__city" style="color:var(--gold-deep)"><i class="fa-solid fa-circle-info"></i> Common Amenities</div>
              <div class="hotel__name" style="font-size:15px;margin-bottom:8px">Standard in Every Hotel</div>

              <p class="muted" style="font-size:11px;line-height:1.2;margin-top:10px;font-style:italic;">
                Enjoy a comfortable stay at carefully selected accommodations that offer a perfect blend of convenience, comfort, and hospitality.
              </p>

              <p class="muted" style="font-size:11px;line-height:1.2;margin-top:8px;font-style:italic;">
                Each property is chosen to provide a relaxing environment and essential amenities, ensuring a pleasant experience throughout your journey.
              </p>
              <p class="muted" style="font-size:11px;margin:10px 0 0;line-height:1.5">All stays are on twin-sharing basis with daily breakfast unless stated otherwise. Room categories may be upgraded subject to availability.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <?php o2_foot(); ?>
    </section>

    <!-- PAGE 4 · FLIGHTS & TRANSPORT -->
    <section class="page page-flow print-section">
      <?php o2_strip('Getting There &amp; Around', 'Journey Plan', 'Flights · <b>Transfers</b>'); ?>
      <div class="page__wm"></div>
      <div class="page__body">
        <p class="kicker">A · Flight Details</p>
        <div style="display:flex;flex-direction:column;gap:11px;margin-top:12px">
          <?php if (!empty($flights)): $o2_fi = 0;
            foreach ((array) $flights as $f): ?>
              <div class="boarding">
                <div class="boarding__main">
                  <div class="boarding__top">
                    <div class="airline">
                      <img class="airline__logo" src="<?= o2e(o2img(isset($f['airline_logo']) ? $f['airline_logo'] : '', $assets . 'airline.png')) ?>" alt="Airline">
                      <div>
                        <div class="airline__name"><?= o2e(o2nv($f['airline_display'], o2nv($f['airline_name'], 'Flight'))) ?></div>
                        <div class="airline__cls"><?= o2e(o2nv($f['class'], '')) ?></div>
                      </div>
                    </div>
                    <div class="boarding__pnr"><?= o2e(o2_flight_label($o2_fi)) ?></div>
                  </div>
                  <div class="sector">
                    <div class="pt">
                      <div class="code"><?= o2e(o2_air_code(isset($f['from_city']) ? $f['from_city'] : '')) ?></div>
                      <div class="city"><?= o2e(o2nv($f['from_city'], '')) ?></div>
                    </div>
                    <div class="mid"><i class="fa-solid fa-plane"></i>
                      <div class="line"></div>
                      <div class="dur">As per schedule</div>
                    </div>
                    <div class="pt">
                      <div class="code"><?= o2e(o2_air_code(isset($f['to_city']) ? $f['to_city'] : '')) ?></div>
                      <div class="city"><?= o2e(o2nv($f['to_city'], '')) ?></div>
                    </div>
                  </div>
                  <div class="boarding__times">
                    <div class="x">
                      <div class="l">Departure</div>
                      <div class="v"><?= o2e(o2nv($f['departure_datetime'], 'NA')) ?></div>
                    </div>
                    <div class="x">
                      <div class="l">Arrival</div>
                      <div class="v"><?= o2e(o2nv($f['arrival_datetime'], 'NA')) ?></div>
                    </div>
                    <!-- <div class="x">
                      <div class="l">Flight</div>
                      <div class="v"><//?= o2e(o2nv($f['airline_code'], '—')) ?></div>
                    </div> -->
                  </div>
                </div>
                <div class="boarding__stub">
                  <div class="bl">Baggage</div>
                  <div class="bv">As per airline</div>
                  <div class="bl" style="margin-top:5px">Class</div>
                  <div class="bv"><?= o2e(o2nv($f['class'], '—')) ?></div>
                </div>
              </div>
            <?php $o2_fi++;
            endforeach;
          else: ?>
            <div class="card" style="padding:14px;text-align:center;color:var(--muted)">No flight details available.</div>
          <?php endif; ?>
        </div>
        <?php if (count((array) $flights) > 2): ?>
          <p class="muted" style="font-size:9px;margin:7px 0 0">Additional internal sectors are included as per the itinerary on the respective travel dates.</p>
        <?php endif; ?>

        <?php if (!empty($trains)): ?>
          <p class="kicker kicker-heading" style="margin-top:6mm">B · Train Details</p>

          <div style="display:flex;flex-direction:column;gap:11px;margin-top:12px">
            <?php foreach ((array)$trains as $tr): ?>
              <?php
              $from_loc = isset($tr['from_location']) ? $tr['from_location'] : '';
              $to_loc   = isset($tr['to_location']) ? $tr['to_location'] : '';
              $from_date = isset($tr['from_date']) ? $tr['from_date'] : '';
              $train_class = isset($tr['class']) ? $tr['class'] : 'NA';

              $total_pax = 0;
              if (isset($ov['pax']) && is_array($ov['pax'])) {
                $total_pax =
                  (int)o2nv(isset($ov['pax']['adult']) ? $ov['pax']['adult'] : 0, 0) +
                  (int)o2nv(isset($ov['pax']['children_with_bed']) ? $ov['pax']['children_with_bed'] : 0, 0) +
                  (int)o2nv(isset($ov['pax']['children_without_bed']) ? $ov['pax']['children_without_bed'] : 0, 0) +
                  (int)o2nv(isset($ov['pax']['infant']) ? $ov['pax']['infant'] : 0, 0);
              }
              ?>

              <div class="boarding">
                <div class="boarding__main">
                  <div class="boarding__top">
                    <div class="airline">
                      <span class="airline__logo" style="display:flex;align-items:center;justify-content:center;background:#fff;color:var(--navy);font-size:18px">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="16" height="16" fill="currentColor">
                          <path d="M96 0C43 0 0 43 0 96v192c0 35.3 28.7 64 64 64l-32 64H80l16-32H352l16 32h48l-32-64c35.3 0 64-28.7 64-64V96c0-53-43-96-96-96H96zm0 64H352c17.7 0 32 14.3 32 32V224H64V96c0-17.7 14.3-32 32-32zm-16 224a32 32 0 1 1 64 0 32 32 0 1 1-64 0zm224 0a32 32 0 1 1 64 0 32 32 0 1 1-64 0z" />
                        </svg>
                      </span>
                      <div>
                        <div class="airline__name">Train Journey</div>
                        <div class="airline__cls"><?= o2e($train_class) ?></div>
                      </div>
                    </div>
                    <div class="boarding__pnr">Rail Ticket</div>
                  </div>

                  <div class="sector">
                    <div class="pt">
                      <div class="code"><?= o2e(strtoupper(substr($from_loc, 0, 3))) ?></div>
                      <div class="city"><?= o2e(o2nv($from_loc, 'NA')) ?></div>
                    </div>
                    <div class="mid">
                      <i class="fa-solid fa-train"></i>
                      <div class="line"></div>
                      <div class="dur">Rail journey</div>
                    </div>
                    <div class="pt">
                      <div class="code"><?= o2e(strtoupper(substr($to_loc, 0, 3))) ?></div>
                      <div class="city"><?= o2e(o2nv($to_loc, 'NA')) ?></div>
                    </div>
                  </div>

                  <div class="boarding__times">
                    <div class="x">
                      <div class="l">Date & Time</div>
                      <div class="v"><?= o2e(o2nv($from_date, 'NA')) ?></div>
                    </div>
                    <div class="x">
                      <div class="l">Total Pax</div>
                      <div class="v"><?= o2e($total_pax) ?></div>
                    </div>
                    <div class="x">
                      <div class="l">Class</div>
                      <div class="v"><?= o2e($train_class) ?></div>
                    </div>
                  </div>
                </div>

                <div class="boarding__stub">
                  <div class="bl">Train</div>
                  <div class="bv">As per schedule</div>
                  <div class="bl" style="margin-top:5px">Class</div>
                  <div class="bv"><?= o2e($train_class) ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- =============== Activity Details -->
        <?php if (!empty($acts)): ?>
          <p class="kicker kicker-heading" style="margin-top:6mm">C · Activity Details</p>

          <div class="tline" style="margin-top:11px">
            <?php foreach ((array)$acts as $a): ?>
              <?php
              $activity_img = BASE_URL . 'images/activity.jpg';

              $activity_name = isset($a['activity_name']) ? $a['activity_name'] : '';
              $city_name     = isset($a['city_name']) ? $a['city_name'] : '';
              $activity_date = isset($a['date']) ? $a['date'] : '';
              $transfer_type = isset($a['transfer_type']) ? $a['transfer_type'] : '';

              $total_pax = 0;
              if (isset($a['pax']) && is_array($a['pax'])) {
                $total_pax =
                  (int)o2nv(isset($a['pax']['adult']) ? $a['pax']['adult'] : 0, 0) +
                  (int)o2nv(isset($a['pax']['chwb']) ? $a['pax']['chwb'] : 0, 0) +
                  (int)o2nv(isset($a['pax']['chwob']) ? $a['pax']['chwob'] : 0, 0) +
                  (int)o2nv(isset($a['pax']['infant']) ? $a['pax']['infant'] : 0, 0);
              }
              ?>

              <div class="tnode">
                <div class="card">
                  <img class="tveh" src="<?= o2e($activity_img) ?>" alt="Activity">

                  <div>
                    <div class="nm"><?= o2e(o2nv($activity_name, 'Activity')) ?></div>
                    <div class="rt">
                      <i class="fa-solid fa-location-dot"></i>
                      <?= o2e(o2nv($city_name, 'NA')) ?>
                    </div>
                  </div>

                  <div class="meta">
                    <?= o2e(o2nv($activity_date, 'NA')) ?><br>
                    <span class="cat"><?= o2e(o2nv($transfer_type, 'Transfer')) ?></span><br>
                    <span class="cat"><?= o2e($total_pax) ?> Pax</span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <!-- ====================== -->
        <p class="kicker kicker-heading" style="margin-top:6mm">D · Transportation</p>
        <div class="tline" style="margin-top:11px">
          <?php if (!empty($vehs)): foreach ((array) $vehs as $v): ?>
              <div class="tnode">
                <div class="card">
                  <!-- <img class="tveh" src="<? //= o2e($assets . 'vehicle.png') 
                                              ?>" alt="Vehicle"> -->
                  <img class="tveh" src="<?= o2e(BASE_URL . 'images/vehicle.png') ?>" alt="Vehicle">
                  <div>
                    <div class="nm"><?= o2e(o2nv($v['vehicle_name'], 'Transfer')) ?></div>
                    <div class="rt"><i class="fa-solid fa-location-dot"></i> <?= o2e(o2nv($v['pickup'], '')) ?><?php if (o2nv($v['drop'], '') !== ''): ?> → <?= o2e($v['drop']) ?><?php endif; ?></div>
                  </div>
                  <div class="meta"><?= o2e(o2nv($v['date'], '')) ?><?php if (!empty($v['description'])): ?><br><?= o2e($v['description']) ?><?php endif; ?><br><span class="cat"><?= o2e(o2nv($v['vehicle_type'], 'Private Transfer')) ?></span></div>
                </div>
              </div>
            <?php endforeach;
          else: ?>
            <div class="card" style="padding:12px;color:var(--muted)">No transportation details available.</div>
          <?php endif; ?>
        </div>
      </div>
      <?php o2_foot(); ?>
    </section>

    <!-- ITINERARY PAGES -->
    <?php
    if (!empty($o2_itin_pages)):
      foreach ($o2_itin_pages as $o2_chunk_idx => $o2_chunk):
        $o2_first_day = isset($o2_chunk[0]['day_number']) ? (int) $o2_chunk[0]['day_number'] : 1;
        $o2_last_day  = isset($o2_chunk[count($o2_chunk) - 1]['day_number']) ? (int) $o2_chunk[count($o2_chunk) - 1]['day_number'] : $o2_first_day;
        $o2_day_label = 'Days ' . $o2_first_day . ($o2_last_day > $o2_first_day ? ' – ' . $o2_last_day : '');
    ?>
        <section class="page itinerary-page page-flow  <?= count($o2_chunk) > 2 ? ' page-flow' : '' ?>">
          <?php o2_strip('Day by Day', 'Itinerary', o2e($o2_day_label)); ?>
          <div class="page__wm"></div>
          <div class="page__body">
            <div class="itin">
              <?php foreach ($o2_chunk as $d):
                $o2_dno   = (int) o2nv(isset($d['day_number']) ? $d['day_number'] : '', 0);
                // $o2_dimg  = o2img(isset($d['image']) ? $d['image'] : '', $assets . 'day.jpg');
                $dummy_day_img = BASE_URL . 'images/itinerary.png';

                $o2_day_photo = isset($d['image']) ? trim($d['image']) : '';

                if ($o2_day_photo == '' || stripos($o2_day_photo, 'dummy') !== false) {
                  $o2_dimg = $dummy_day_img;
                } else {
                  $o2_dimg = o2img($o2_day_photo, $dummy_day_img);
                }
                $o2_attr  = o2nv($d['special_attraction'], o2nv($d['city'], 'Day ' . $o2_dno));
                $o2_prog  = trim(isset($d['detailed_programme']) ? $d['detailed_programme'] : '');
              ?>
                <div class="card iday">
                  <div class="iday__media">
                    <img src="<?= o2e($o2_dimg) ?>" alt="Day <?= o2e($o2_dno) ?>">
                    <div class="iday__tag"><span class="iday__no">DAY <?= o2e($o2_dno) ?></span><span class="iday__date"><?= o2e(o2nv($d['date'], '')) ?></span></div>
                  </div>
                  <div class="iday__b">
                    <div class="iday__attr"><i class="fa-solid fa-compass"></i> Special Attraction · <?= o2e($o2_attr) ?></div>
                    <div class="iday__field">
                      <div class="iday__lbl">Detailed Programme</div>
                      <div class="iday__val"><?= $o2_prog !== '' ? $o2_prog : 'Detailed programme will be shared.' ?></div>
                    </div>
                    <div class="iday__chips">
                      <div class="ichip ichip--meal"><span class="b">
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 2v7c0 2.2 1.8 4 4 4V22"></path>
                            <path d="M7 2v20"></path>
                            <path d="M11 2v7c0 2.2-1.8 4-4 4"></path>
                            <path d="M18 2v20"></path>
                            <path d="M18 2c2.2 0 4 2.7 4 6v4h-4"></path>
                          </svg>
                        </span>
                        <div>
                          <div class="l">Meal Plan</div>
                          <div class="v"><?= o2e(o2nv($d['meal_plan'], '—')) ?></div>
                        </div>
                      </div>
                      <div class="ichip ichip--stay"><span class="b">
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 3a6 6 0 1 0 9 9A9 9 0 1 1 12 3z"></path>
                          </svg>
                        </span>
                        <div>
                          <div class="l">Overnight Stay</div>
                          <div class="v"><?= o2e(o2nv($d['overnight_stay'], '—')) ?></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php o2_foot(); ?>
        </section>
    <?php endforeach;
    endif; ?>

    <!-- INCLUSIONS / EXCLUSIONS / COSTING -->
    <section class="page page-flow inclusion-page">
      <?php o2_strip('The Fine Detail', 'Inclusions', '&amp; <b>Investment</b>'); ?>
      <div class="page__wm"></div>
      <div class="page__body">
        <div class="ie-grid option1-ie-grid">
          <div class="card ie ie--in">
            <h3><span class="b rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5">
                  </path>
                </svg>
              </span> What's Included</h3>
            <hr class="gold-rule mt-2">
            <ul>
              <?php
              if (!empty($incx['included'])) {

                $html = preg_replace('/<\/span>\s*<span[^>]*>/i', '', $incx['included']);

                preg_match_all('/<p[^>]*>(.*?)<\/p>|<span[^>]*>(.*?)<\/span>/is', $html, $matches);

                $items = array_merge($matches[1], $matches[2]);

                foreach ($items as $item) {
                  if (trim($item) == '') continue;

                  $item = preg_replace('/<img[^>]*>/i', '', $item);
                  $item = preg_replace('/<!--.*?-->/s', '', $item);
                  $item = preg_replace('/<o:p>.*?<\/o:p>/i', '', $item);

                  $item = strip_tags($item);
                  $item = html_entity_decode(trim($item));

                  if ($item == '') continue;
              ?>
                  <li>
                    <svg width="16" height="16" viewBox="0 0 24 24"
                      fill="none" stroke="currentColor" stroke-width="2"
                      stroke-linecap="round" stroke-linejoin="round">
                      <path d="M20 6 9 17l-5-5"></path>
                    </svg>
                    <?= o2e($item) ?>
                  </li>
                <?php
                }
              } else {
                ?>
                <li>Inclusions will be shared as per final quotation.</li>
              <?php } ?>
            </ul>
          </div>
          <div class="card ie ie--ex">
            <h3><span class="b rounded-xl">
                <svg width="16" height="16" viewBox="0 0 24 24"
                  fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M18 6 6 18"></path>
                  <path d="M6 6 18 18"></path>
                </svg>
              </span> What's Excluded</h3>
            <hr class="gold-rule mt-2">

            <ul>
              <?php
              if (!empty($incx['excluded'])) {

                $html = preg_replace('/<\/span>\s*<span[^>]*>/i', '', $incx['excluded']);

                preg_match_all('/<p[^>]*>(.*?)<\/p>|<span[^>]*>(.*?)<\/span>/is', $html, $matches);

                $items = array_merge($matches[1], $matches[2]);

                foreach ($items as $item) {
                  if (trim($item) == '') continue;

                  $item = preg_replace('/<img[^>]*>/i', '', $item);
                  $item = preg_replace('/<!--.*?-->/s', '', $item);
                  $item = preg_replace('/<o:p>.*?<\/o:p>/i', '', $item);

                  $item = strip_tags($item);
                  $item = html_entity_decode(trim($item));

                  if ($item == '') continue;
              ?>
                  <li>
                    <svg width="16" height="16" viewBox="0 0 24 24"
                      fill="none"
                      stroke="#dc2626"
                      stroke-width="2.5"
                      stroke-linecap="round"
                      stroke-linejoin="round">
                      <path d="M18 6L6 18"></path>
                      <path d="M6 6L18 18"></path>
                    </svg>
                    <?= o2e($item) ?>
                  </li>
                <?php
                }
              } else {
                ?>
                <li>Exclusions will be shared as per final quotation.</li>
              <?php } ?>
            </ul>
          </div>
        </div>


        <div style="margin-top:6mm">
          <?php
          $o2_costing_type = isset($cost['costing_type_label']) ? strtolower(trim($cost['costing_type_label'])) : '';
          $o2_is_per_person = ($o2_costing_type == 'per person');
          ?>

          <div class="sec-h">
            <div><span class="gold-rule" style="display:block;margin-bottom:7px"></span><span class="t">Costing Details</span></div>
            <span class="s">
              All amounts in <?= o2e(o2nv($q['currency'], 'INR')) ?>
              · <?= $o2_is_per_person ? 'Per Person' : 'For ' . o2e($o2_guests) ?>
            </span>
          </div>

          <?php if (!$o2_is_per_person) { ?>

            <table class="cost">
              <thead>
                <tr>
                  <th>Package</th>
                  <th>Tour Cost</th>
                  <th>Tax (GST)</th>
                  <th>TCS</th>
                  <th>Travel Cost</th>
                  <th>Grand Total</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $o2_grp = isset($cost['computed']['group']) ? $cost['computed']['group'] : array();

                if (empty($o2_grp)) {
                  $o2_grp = array(array(
                    'package_type' => 'Package',
                    'tour_cost_display' => '0',
                    'tax_display' => '0',
                    'tcs_display' => '0',
                    'travel_display' => '0',
                    'total_display' => '0'
                  ));
                }

                $o2_ci = 0;
                foreach ($o2_grp as $o2_row):
                  $o2_rec = ($o2_ci === 1 || (count($o2_grp) === 1));

                  $tax_amount = function_exists('gqd_tax_display_amount') ? gqd_tax_display_amount($o2_row) : (isset($o2_row['tax_amount_display']) ? $o2_row['tax_amount_display'] : '0.00');
                ?>
                  <tr<?= $o2_rec ? ' class="rec"' : '' ?>>
                    <td class="pk">
                      <?php if ($o2_rec): ?>
                        <span class="recbar">
                          <?= o2e(o2nv($o2_row['package_type'], 'Package')) ?>
                          <span class="tag">Recommended</span>
                        </span>
                      <?php else: ?>
                        <?= o2e(o2nv($o2_row['package_type'], 'Package')) ?>
                      <?php endif; ?>
                    </td>

                    <td><?= o2e(o2nv($o2_row['tour_cost_display'], 'INR 0.00')) ?></td>
                    <td><?= o2e($tax_amount) ?></td>
                    <td><?= o2e(o2nv($o2_row['tcs_display'], 'INR 0.00')) ?></td>
                    <td><?= o2e(o2nv($o2_row['travel_display'], 'INR 0.00')) ?></td>
                    <td class="gt"><?= function_exists('gqd_total_with_before_discount') ? gqd_total_with_before_discount($o2_row, 'total_display', 'before_discount_display', 'o2e') : o2e(o2nv($o2_row['total_display'], 'INR 0.00')) ?></td>
                    </tr>
                  <?php
                  $o2_ci++;
                endforeach;
                  ?>
              </tbody>
            </table>

          <?php } else { ?>

            <?php
            $o2_pp_entries = isset($cost['computed']['pp_entries']) ? $cost['computed']['pp_entries'] : array();
            $o2_pp = isset($cost['computed']['per_person']) ? $cost['computed']['per_person'] : array();
            ?>

            <?php if (!empty($o2_pp_entries)) { ?>
              <?php gqd_render_pp_entries_table($o2_pp_entries, array('escape' => 'o2e', 'table_class' => 'cost')); ?>
            <?php } elseif (!empty($o2_pp)) { ?>
              <table class="cost">
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
                  <?php foreach ($o2_pp as $i => $pp): ?>
                    <?php
                    $o2_rec = ($i === 1 || (count($o2_pp) === 1));

                    $tax_amount = function_exists('gqd_tax_display_amount') ? gqd_tax_display_amount($pp) : (isset($pp['tax_amount_display']) ? $pp['tax_amount_display'] : '0.00');
                    ?>

                    <tr<?= $o2_rec ? ' class="rec"' : '' ?>>
                      <td class="pk">
                        <?php if ($o2_rec): ?>
                          <span class="recbar">
                            <?= o2e(o2nv($pp['package_type'], 'Package')) ?>
                            <span class="tag">Recommended</span>
                          </span>
                        <?php else: ?>
                          <?= o2e(o2nv($pp['package_type'], 'Package')) ?>
                        <?php endif; ?>
                      </td>

                      <td>&#8377;<?= o2e(o2nv($pp['pp_adult_display'], 'INR 0.00')) ?></td>
                      <td>&#8377;<?= o2e(o2nv($pp['pp_cwb_display'], 'INR 0.00')) ?></td>
                      <td>&#8377;<?= o2e(o2nv($pp['pp_cwnb_display'], 'INR 0.00')) ?></td>
                      <td>&#8377;<?= o2e(o2nv($pp['pp_infant_display'], 'INR 0.00')) ?></td>
                      <td>&#8377;<?= o2e($tax_amount) ?></td>
                      <td>&#8377;<?= o2e(o2nv($pp['tcs_display'], 'INR 0.00')) ?></td>
                      <td>&#8377;<?= o2e(o2nv($pp['visa_display'], 'INR 0.00')) ?></td>
                      <td>&#8377;<?= o2e(o2nv($pp['guide_display'], 'INR 0.00')) ?></td>
                      <td>&#8377;<?= o2e(o2nv($pp['misc_display'], 'INR 0.00')) ?></td>
                      </tr>
                    <?php endforeach; ?>
                </tbody>
              </table>
            <?php } ?>

          <?php } ?>

          <p class="cost-note">
            Prices are indicative and subject to availability and currency fluctuation at the time of confirmation.
          </p>
        </div>
      </div>
      <?php o2_foot(); ?>
    </section>

    <!-- PAYMENT -->
    <section class="page payment-page">
      <?php o2_strip('Securing Your Booking', 'Payment', 'Bank &amp; <b>UPI</b>'); ?>
      <div class="page__wm"></div>
      <div class="page__body">
        <div class="pay-grid">
          <div class="bankcard">
            <div class="bankcard__top">
              <div class="l">Bank Transfer Details</div>
              <div class="bankcard__chip"></div>
            </div>
            <div class="bankrow solo">
              <div class="f">
                <div class="l">Account Name</div>
                <div class="v"><?= o2e(o2nv($bank['account_name'], 'NA')) ?></div>
              </div>
            </div>
            <div class="bankcard__div"></div>
            <div class="bankrow">
              <div class="f">
                <div class="l">Account Number</div>
                <div class="v mono"><?= o2e(o2nv($bank['account_no'], 'NA')) ?></div>
              </div>
              <div class="f">
                <div class="l">IFSC Code</div>
                <div class="v mono"><?= o2e(o2nv($bank['ifsc_code'], o2nv($bank['swift_code'], 'NA'))) ?></div>
              </div>
              <div class="f">
                <div class="l">Bank Name</div>
                <div class="v"><?= o2e(o2nv($bank['bank_name'], 'NA')) ?></div>
              </div>
              <div class="f">
                <div class="l">Branch</div>
                <div class="v"><?= o2e(o2nv($bank['branch_name'], 'NA')) ?></div>
              </div>
            </div>
            <div class="bankcard__div"></div>
            <div class="bankrow solo">
              <div class="f">
                <div class="l">UPI ID</div>
                <div class="v mono"><?= o2e(o2nv($bank['upi_id'], 'NA')) ?></div>
              </div>
            </div>
          </div>
          <div class="card qrcard">
            <div class="scan">Scan &amp; Pay</div>
            <?php if (!empty($bank['qr_html'])): ?>
              <div class="qr"><?= $bank['qr_html'] ?></div>
            <?php elseif (!empty($bank['qr_code']) || !empty($bank['branch_qr_url'])): ?>
              <img class="qr" src="<?= o2e(o2img(o2nv($bank['branch_qr_url'], o2nv($bank['qr_code'], '')), $assets . 'qr.png')) ?>" alt="UPI QR code">
            <?php else: ?>
              <img class="qr" src="<?= o2e($assets . 'qr.png') ?>" alt="UPI QR code">
            <?php endif; ?>
            <div class="upi"><?= o2e(o2nv($bank['upi_id'], '')) ?></div>
            <p class="muted" style="font-size:9px;margin:0;line-height:1.5">Pay instantly via any UPI app — Google Pay, PhonePe, Paytm or BHIM.</p>
          </div>
        </div>
        <div class="pay-panels" style="margin-top:6mm">
          <div class="card panel">
            <h4><i class="fa-solid fa-circle-info"></i> Payment Instructions</h4>
            <ul>
              <li>A 30% advance confirms your booking; the balance is due 21 days before travel.</li>
              <li>Share the payment screenshot or UTR with your travel consultant.</li>
              <li>All payments are accepted in <?= o2e(o2nv($q['currency'], 'Indian Rupees')) ?> only.</li>
              <li>A GST invoice is issued against every confirmed payment.</li>
            </ul>
          </div>
          <div class="card panel">
            <h4><i class="fa-solid fa-shield-halved"></i> Booking Policy</h4>
            <ul>
              <li>Booking is confirmed only on receipt of the advance amount.</li>
              <li>Rates are held for 7 days from the quotation date.</li>
              <li>Visa, flights &amp; hotels are confirmed after full payment.</li>
              <li>Names must match passports exactly to avoid charges.</li>
            </ul>
          </div>
        </div>
      </div>
      <?php o2_foot(); ?>
    </section>

    <!-- TESTIMONIALS -->
    <section class="page testimonial-page<?= count($testimonials) > 2 ? ' page-flow' : '' ?>">
      <?php o2_strip('Loved by Travellers', 'Their Words', o2e($o2_google_rating) . ' ★ <b>Rated</b>'); ?>
      <div class="page__wm"></div>
      <div class="page__body">
        <div class="sec-h">
          <div><span class="gold-rule" style="display:block;margin-bottom:7px"></span><span class="t">What Our Travellers Say</span></div>
          <span class="s">Verified Google reviews</span>
        </div>
        <div class="testi-grid">
          <?php if (!empty($testimonials)): foreach ($testimonials as $t):
              $tphoto = o2_testi_photo(isset($t['photo']) ? $t['photo'] : '');
          ?>
              <div class="card testi">
                <?php if ($tphoto !== ''): ?>
                  <img class="testi__ph" src="<?= o2e($tphoto) ?>" alt="Reviewer">
                <?php else: ?>
                  <img class="testi__ph" src="<?= o2e($assets . 'person.jpg') ?>" alt="Reviewer">
                <?php endif; ?>
                <div>
                  <div class="testi__stars">★★★★★</div>
                  <div class="testi__quote"><span class="testi__mark">“</span> <?= o2e(o2nv(isset($t['review']) ? $t['review'] : '', o2nv(isset($t['message']) ? $t['message'] : '', ''))) ?></div>
                  <div class="testi__by"><span class="n"><?= o2e(o2nv(isset($t['name']) ? $t['name'] : '', 'Traveller')) ?></span><span class="sep"></span><span class="d"><?= o2e(o2nv(isset($t['designation']) ? $t['designation'] : '', o2nv(isset($t['title']) ? $t['title'] : '', 'Customer'))) ?></span></div>
                </div>
              </div>
            <?php endforeach;
          else: ?>
            <div class="card testi" style="grid-template-columns:1fr;padding:20px;text-align:center;color:var(--muted)">Customer testimonials can be managed from Quotation Builder settings.</div>
          <?php endif; ?>
        </div>
        <div class="statbar">
          <div class="st">
            <div class="n"><?= o2e($o2_google_rating) ?>★</div>
            <div class="l">Google Rating</div>
          </div>
          <div class="st">
            <div class="n"><?= o2e($o2_review_count) ?></div>
            <div class="l">Verified Reviews</div>
          </div>
          <div class="st">
            <div class="n"><?= o2e($o2_traveller_cnt) ?></div>
            <div class="l">Happy Travellers</div>
          </div>
        </div>
      </div>
      <?php o2_foot(); ?>
    </section>

    <!-- TERMS -->
    <section class="page page-flow terms-page">
      <?php o2_strip('Please Read Carefully', 'Terms', '&amp; <b>Conditions</b>'); ?>
      <div class="page__wm"></div>
      <div class="page__body">
        <?php
        $o2_terms_html = trim(isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '');
        if ($o2_terms_html !== ''):
        ?>
          <div class="terms-content" style="font-size:12px;line-height:1.55;color:var(--ink-soft);margin-top:2mm">
            <?= $o2_terms_html ?>
          </div>
        <?php endif; ?>
        <div class="card" style="margin-top:6mm;padding:12px 15px;display:flex;gap:11px;align-items:center;background:var(--cream)">
          <!-- <span class="ic ic--navy"><i class="fa-solid fa-circle-info"></i></span> -->
          <span class="ic ic--navy">
            <svg xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M12 16v-4"></path>
              <path d="M12 8h.01"></path>
            </svg>
          </span>
          <p class="muted" style="font-size:11px;margin:0;line-height:1.5">By proceeding with payment, the traveller acknowledges and accepts all terms and conditions outlined above. This quotation is valid for 7 days from the date of issue.</p>
        </div>
      </div>
      <?php o2_foot(); ?>
    </section>

    <!-- THANK YOU -->
    <section class="page thanks thanks-page">
      <div class="page__wm thanks__wm"></div>
      <div class="thanks__wrap">
        <div class="logo">
          <img class="logo__slot" src="<?= o2e($o2_logo) ?>" alt="Company logo">
        </div>
        <div class="thanks__k">With Heartfelt Gratitude</div>
        <h2 class="thanks__big">Thank You</h2>
        <p class="thanks__msg">We look forward to creating unforgettable travel memories for you. Your journey through <?= o2e($o2_dest) ?> is just the beginning of many more to come.</p>
        <div class="thanks__stats">
          <div class="s">
            <div class="n"><?= o2e($o2_google_rating) ?>★</div>
            <div class="l">Google Rating</div>
          </div>
          <div class="s">
            <div class="n"><?= o2e($o2_review_count) ?></div>
            <div class="l">Reviews</div>
          </div>
          <div class="s">
            <div class="n"><?= o2e($o2_traveller_cnt) ?></div>
            <div class="l">Happy Travellers</div>
          </div>
        </div>

        <!-- <div class="thanks__social">
          <?php $o2_web = o2nv($ty['website'], ''); ?>
          
          <a href="<//?= o2e($o2_web !== '' ? $o2_web : '#') ?>" aria-label="Website">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M2 12h20"></path>
              <path d="M12 2a15.3 15.3 0 0 1 0 20"></path>
              <path d="M12 2a15.3 15.3 0 0 0 0 20"></path>
            </svg>
          </a>

          
          <a href="mailto:<//?= o2e(o2nv($ty['company_email'], '')) ?>" aria-label="Email">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="5" width="18" height="14" rx="2"></rect>
              <path d="m3 7 9 6 9-6"></path>
            </svg>
          </a>

          
          <a href="tel:<//?= o2e(preg_replace('/\s+/', '', o2nv($ty['company_contact'], o2nv($ty['user_mobile'], '')))) ?>" aria-label="Phone">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.9.34 1.78.65 2.62a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.46-1.17a2 2 0 0 1 2.11-.45c.84.31 1.72.53 2.62.65A2 2 0 0 1 22 16.92z"></path>
            </svg>
          </a>

          <a href="<//?= !empty($social_links['facebook']) ? o2e($social_links['facebook']) : '#' ?>" aria-label="Facebook">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
              <path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.88 3.77-3.88 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.77l-.44 2.89h-2.33v6.99A10 10 0 0 0 22 12z" />
            </svg>
          </a>
        </div> -->
        <div class="thanks__contact">

          <!-- <span class="c">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-5 h-5 mx-auto text-[color:var(--gold)]" aria-hidden="true">
              <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
              </path>
              <circle cx="12" cy="10" r="3">
              </circle>
            </svg>
            <//?= o2e(o2nv($ty['company_address'], '')) ?>
          </span> -->

          <span class="c">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.9.34 1.78.65 2.62a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.46-1.17a2 2 0 0 1 2.11-.45c.84.31 1.72.53 2.62.65A2 2 0 0 1 22 16.92z"></path>
            </svg>
            <a href="tel:<?= o2e(preg_replace('/\D+/', '', o2nv($ty['company_contact'], o2nv($ty['user_mobile'], '')))) ?>">
              <?= o2e(o2nv($ty['company_contact'], o2nv($ty['user_mobile'], ''))) ?>
            </a>
          </span>

          <span class="c">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="5" width="18" height="14" rx="2"></rect>
              <path d="m3 7 9 6 9-6"></path>
            </svg>
            <a href="mailto:<?= o2e(o2nv($ty['company_email'], '')) ?>">
              <?= o2e(o2nv($ty['company_email'], '')) ?>
            </a>
          </span>

          <span class="c">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M2 12h20"></path>
              <path d="M12 2a15.3 15.3 0 0 1 0 20"></path>
              <path d="M12 2a15.3 15.3 0 0 0 0 20"></path>
            </svg>
            <a href="<?= o2e($o2_web) ?>" target="_blank">
              <?= o2e($o2_web) ?>
            </a>
          </span>

        </div>
      </div>
      <div class="thanks__bar">
        <div>
          <div class="l">Prepared By</div>
          <div class="n"><?= o2e(o2nv($ty['prepared_by'], o2nv($hero['login_user'], ''))) ?></div>
          <div class="sub">Travel Consultant · <?= o2e($o2_company) ?></div>
        </div>
        <div style="text-align:right">
          <div class="l">Quotation</div>
          <div class="n"><?= o2e(o2nv($hero['quotation_code'], '')) ?></div>
          <div class="sub">Issued <?= o2e(o2nv($ty['issue_date'], o2nv($ov['quotation_date'], ''))) ?></div>
        </div>
      </div>
    </section>

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
          setTimeout(doPrint, 2500);
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