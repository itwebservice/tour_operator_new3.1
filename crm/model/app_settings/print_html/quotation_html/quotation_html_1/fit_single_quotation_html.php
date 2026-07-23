<?php

/**
 * ============================================================================
 * OPTION-1  (quotation_html_1)  —  Package Tour Quotation
 * ----------------------------------------------------------------------------
 * Faithful render of Final-Designs/Option-1/index.html, but every value comes
 * from the generic JSON data engine (get_generic_quotation_data) instead of
 * inline DB queries.
 *   .../quotation_html/quotation_html_1/fit_quotation_html.php?quotation_id=ID
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

// ---- section aliases -------------------------------------------------------
$hero    = $q['hero'];
$ov      = $q['tour_overview'];
$hotels  = $q['hotels'];
$flights = $q['flights'];
$trains  = $q['trains'];
$cruises = $q['cruises'];
$acts    = $q['activities'];
$vehs    = $q['vehicles'];
$itin    = $q['itinerary'];
$incx    = $q['inclusion_exclusion'];
$cost    = $q['costing'];
$bank    = $q['bank_details'];
$terms   = $q['terms_conditions'];
$ty      = $q['thank_you'];
$present = $q['sections_present'];
$assets  = "assets/"; // decorative/fallback images shipped with this design

// $testimonials = array();

$testimonials = isset($q['testimonials']) && is_array($q['testimonials'])
  ? $q['testimonials']
  : array();

$social_links = array();

if (function_exists('gqb_get_config')) {
  $o1_cfg = gqb_get_config();
  // $testimonials = isset($o1_cfg['testimonials']) && is_array($o1_cfg['testimonials']) ? $o1_cfg['testimonials'] : array();
  // $social_links = isset($o1_cfg['social_links']) && is_array($o1_cfg['social_links']) ? $o1_cfg['social_links'] : array();
  $social_links = isset($o1_cfg['social_links']) && is_array($o1_cfg['social_links'])
    ? $o1_cfg['social_links']
    : array();
}

// ---- helpers ---------------------------------------------------------------
if (!function_exists('o1e')) {
  function o1e($v)
  {
    return htmlspecialchars((string)$v, ENT_QUOTES);
  }
}
if (!function_exists('o1nv')) {
  function o1nv($v, $f = '')
  {
    return ($v !== null && $v !== '') ? $v : $f;
  }
}

$o1_company = o1nv($hero['company_name'], o1nv($ty['company_name'], ''));

if (!function_exists('o1_list_item_text')) {
  function o1_list_item_text($html)
  {
    $html = preg_replace('/<br\s*\/?>/i', ' ', (string)$html);
    $text = strip_tags($html);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = str_replace("\xC2\xA0", ' ', $text);
    $text = preg_replace('/\s+/u', ' ', $text);
    return trim($text);
  }
}
if (!function_exists('o1_list_items')) {
  function o1_list_items($html, $default = '')
  {
    $html = (string)$html;
    $items = array();

    if (trim($html) === '') {
      return $default !== '' ? array($default) : array();
    }

    if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $html, $matches)) {
      foreach ($matches[1] as $chunk) {
        $text = o1_list_item_text($chunk);
        if ($text !== '') {
          $items[] = $text;
        }
      }
    }

    if (empty($items) && preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $html, $matches)) {
      foreach ($matches[1] as $chunk) {
        $text = o1_list_item_text($chunk);
        if ($text !== '') {
          $items[] = $text;
        }
      }
    }

    if (empty($items)) {
      $plain = o1_list_item_text($html);
      $parts = preg_split('/\r\n|\r|\n|•|\x{2022}/u', $plain);
      foreach ((array)$parts as $part) {
        $text = trim($part);
        if ($text !== '') {
          $items[] = $text;
        }
      }
    }

    if (empty($items) && $default !== '') {
      $items[] = $default;
    }

    return $items;
  }
}

// Image url with local-asset fallback when the data has no image.
if (!function_exists('o1img')) {
  function o1img($url, $fallback)
  {
    return (is_string($url) && trim($url) !== '' && stripos($url, 'dummy') === false) ? $url : $fallback;
  }
}

// =========================== Dipti 
if (!function_exists('o1_terms_sections')) {
  function o1_terms_sections($html)
  {
    $sections = array();

    $html = str_replace(array("\r", "\n"), '', $html);
    $html = preg_replace('/<br\s*\/?>/i', '', $html);

    $parts = preg_split('/<b[^>]*>/i', $html);

    foreach ($parts as $part) {
      $part = trim($part);
      if ($part == '') continue;

      $bclose = stripos($part, '</b>');
      if ($bclose === false) continue;

      $title_html = substr($part, 0, $bclose);
      $content_html = substr($part, $bclose + 4);

      $title = trim(strip_tags($title_html));
      $title = trim($title, " :\t\n\r\0\x0B");

      // keep only bullet list content, avoid unclosed wrapper nesting
      if (preg_match('/<ul[^>]*>(.*?)<\/ul>/is', $content_html, $ul_match)) {
        $content_html = '<ul>' . $ul_match[1] . '</ul>';
      } else {
        $plain = trim(strip_tags($content_html));
        $content_html = '<p>' . htmlspecialchars($plain) . '</p>';
      }

      if ($title != '' && $content_html != '') {
        $sections[] = array(
          'title' => $title,
          'content' => $content_html
        );
      }
    }

    return $sections;
  }
}
// ===========================================

// ==================== Dipti
function o1_airport_code($v)
{
  if (preg_match('/\((.*?)\)/', $v, $m)) {
    return $m[1];
  }
  return strtoupper(substr(trim($v), 0, 3));
}

function o1_airport_name($v)
{
  return trim(preg_replace('/\s*\(.*?\)/', '', $v));
}

function o1_flight_date($v)
{
  if ($v == '' || strtotime($v) == false) {
    return 'NA';
  }
  return date('d M · H:i', strtotime($v));
}
// ===================================


$o1_pkg_type = '';
if (!empty($cost['computed']['group'][0]['package_type'])) {
  $o1_pkg_type = $cost['computed']['group'][0]['package_type'];
} elseif (!empty($hotels[0]['package_type'])) {
  $o1_pkg_type = $hotels[0]['package_type'];
} else {
  $o1_pkg_type = 'Package';
}

$o1_pkg_label = stripos($o1_pkg_type, 'package') !== false
  ? $o1_pkg_type
  : $o1_pkg_type . ' Package';

// Web preview: continuous scroll layout (no per-page headers, footers, or A4 gaps).
$o1_continuous_view = true;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= o1e(o1nv($ov['destination'], $hero['tour_name'])) ?> Tour Package Quotation &mdash; <?= o1e(o1nv($hero['company_name'], '')) ?></title>
  <meta name="description" content="Tour quotation <?= o1e($hero['quotation_code']) ?> &mdash; itinerary, hotels, flights, costing and more.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;0,900;1,500&family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" type="text/css" href="option1.css">
  <?php if ($o1_continuous_view) : ?>
  <style>
    /* Single-quotation web preview: hide page chrome and flow content continuously. */
    body.single-quotation-flow {
      background: var(--cream, #faf8f4);
    }

    body.single-quotation-flow .header-strip,
    body.single-quotation-flow .page-foot,
    body.single-quotation-flow .page > div.absolute.bottom-0.border-t {
      display: none !important;
    }

    body.single-quotation-flow main {
      min-height: auto !important;
      padding: 0 !important;
    }

    body.single-quotation-flow .page {
      width: 100% !important;
      max-width: 100%;
      min-height: auto !important;
      height: auto !important;
      overflow: visible !important;
      page-break-after: auto !important;
      break-after: auto !important;
      margin: 0 auto !important;
      box-shadow: none !important;
    }

    body.single-quotation-flow .page-hero {
      min-height: 100vh !important;
      height: auto !important;
      page-break-after: auto !important;
      break-after: auto !important;
    }

    body.single-quotation-flow .itinerary-page,
    body.single-quotation-flow .inclusion-page,
    body.single-quotation-flow .thankyou-page,
    body.single-quotation-flow .print-section {
      page-break-before: auto !important;
      break-before: auto !important;
      min-height: auto !important;
      height: auto !important;
    }

    body.single-quotation-flow .page .relative.px-10 {
      padding-bottom: 1.5rem !important;
    }
    body.single-quotation-flow .itinerary-card {
      height: 350px;
    }
  </style>
  <?php endif; ?>
</head>

<body<?= $o1_continuous_view ? ' class="single-quotation-flow "' : '' ?>>
  <main class="<?= $o1_continuous_view ? '' : 'min-h-screen py-6' ?>">
    <section class="page page-hero relative">
      <img src="<?= o1e(o1img($hero['cover_image'], $assets . 'hero-singapore.jpg')) ?>"
        alt="<?= o1e(o1nv($ov['destination'], 'Tour')) ?>"
        class="absolute inset-0 w-full h-full object-cover hero-bg" />
      <!-- <div class="absolute inset-0" style="background:var(--gradient-hero-overlay)">
      </div> -->
      <div class="hero-blue-overlay"></div>
      <div class="relative z-10 px-20 pt-8 flex items-start justify-between">
        <div class="flex items-center gap-2.5">
          <div class="relative w-10 h-10 rounded-full grid place-items-center" style="background:var(--gradient-gold)">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-compass w-5 h-5 text-[color:var(--navy)]" aria-hidden="true">
              <circle cx="12" cy="12" r="10">
              </circle>
              <path d="m16.24 7.76-1.804 5.411a2 2 0 0 1-1.265 1.265L7.76 16.24l1.804-5.411a2 2 0 0 1 1.265-1.265z">
              </path>
            </svg>
          </div>
          <div class="leading-tight text-cream">
            <div class="font-display text-lg font-bold tracking-wide"><?= o1e($o1_company) ?></div>
            <div class="text-[9px] uppercase tracking-[0.25em] opacity-80">Luxury Voyages</div>
          </div>
        </div>
        <div class="text-right text-cream/90">
          <div class="text-[10px] uppercase tracking-[0.3em] opacity-80">Quotation</div>
          <div class="font-display text-base text-[color:var(--gold)]"><?= o1e(o1nv($hero['quotation_code'], '')) ?></div>
        </div>
      </div>
      <div class="relative z-10 px-20 pt-10 text-cream">
        <div class="flex items-center gap-3 mb-5">
          <span class="h-px w-12 bg-[color:var(--gold)]">
          </span>
          <span class="text-[11px] tracking-[0.4em] uppercase text-[color:var(--gold)]">Exclusive Travel Proposal</span>
        </div>
        <h1 class="font-display font-black leading-[0.92] text-cream" style="font-size:84px"><?= o1e(o1nv($ov['destination'], o1nv($hero['tour_name'], 'Tour'))) ?></h1>
        <h2 class="font-display italic text-3xl mt-1 gold-text inline-block">a curated escape</h2>
        <p class="font-serif-soft text-2xl mt-6 max-w-lg text-cream/85">Discover unforgettable experiences — breathtaking landscapes, vibrant cultures, and moments that stay with you forever.</p>
      </div>
      <div class="relative z-10 mt-12 px-20">
        <div class="grid grid-cols-6 gap-3">
          <div class="rounded-xl border border-[color:var(--gold)]/40 bg-white/8 backdrop-blur-md px-3 py-3 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hotel w-5 h-5 mx-auto text-[color:var(--gold)]" aria-hidden="true">
              <path d="M10 22v-6.57">
              </path>
              <path d="M12 11h.01">
              </path>
              <path d="M12 7h.01">
              </path>
              <path d="M14 15.43V22">
              </path>
              <path d="M15 16a5 5 0 0 0-6 0">
              </path>
              <path d="M16 11h.01">
              </path>
              <path d="M16 7h.01">
              </path>
              <path d="M8 11h.01">
              </path>
              <path d="M8 7h.01">
              </path>
              <rect x="4" y="2" width="16" height="20" rx="2">
              </rect>
            </svg>
            <div class="mt-1.5 text-[10px] uppercase tracking-[0.18em] text-cream/85">Hotels</div>
          </div>
          <div class="rounded-xl border border-[color:var(--gold)]/40 bg-white/8 backdrop-blur-md px-3 py-3 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plane w-5 h-5 mx-auto text-[color:var(--gold)]" aria-hidden="true">
              <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z">
              </path>
            </svg>
            <div class="mt-1.5 text-[10px] uppercase tracking-[0.18em] text-cream/85">Flights</div>
          </div>
          <div class="rounded-xl border border-[color:var(--gold)]/40 bg-white/8 backdrop-blur-md px-3 py-3 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-camera w-5 h-5 mx-auto text-[color:var(--gold)]" aria-hidden="true">
              <path d="M13.997 4a2 2 0 0 1 1.76 1.05l.486.9A2 2 0 0 0 18.003 7H20a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.997a2 2 0 0 0 1.759-1.048l.489-.904A2 2 0 0 1 10.004 4z">
              </path>
              <circle cx="12" cy="13" r="3">
              </circle>
            </svg>
            <div class="mt-1.5 text-[10px] uppercase tracking-[0.18em] text-cream/85">Activities</div>
          </div>
          <div class="rounded-xl border border-[color:var(--gold)]/40 bg-white/8 backdrop-blur-md px-3 py-3 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-car w-5 h-5 mx-auto text-[color:var(--gold)]" aria-hidden="true">
              <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2">
              </path>
              <circle cx="7" cy="17" r="2">
              </circle>
              <path d="M9 17h6">
              </path>
              <circle cx="17" cy="17" r="2">
              </circle>
            </svg>
            <div class="mt-1.5 text-[10px] uppercase tracking-[0.18em] text-cream/85">Transfers</div>
          </div>
          <div class="rounded-xl border border-[color:var(--gold)]/40 bg-white/8 backdrop-blur-md px-3 py-3 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-5 h-5 mx-auto text-[color:var(--gold)]" aria-hidden="true">
              <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
              </path>
              <circle cx="12" cy="10" r="3">
              </circle>
            </svg>
            <div class="mt-1.5 text-[10px] uppercase tracking-[0.18em] text-cream/85">Sightseeing</div>
          </div>
          <div class="rounded-xl border border-[color:var(--gold)]/40 bg-white/8 backdrop-blur-md px-3 py-3 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-utensils w-5 h-5 mx-auto text-[color:var(--gold)]" aria-hidden="true">
              <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2">
              </path>
              <path d="M7 2v20">
              </path>
              <path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7">
              </path>
            </svg>
            <div class="mt-1.5 text-[10px] uppercase tracking-[0.18em] text-cream/85">Meals</div>
          </div>
        </div>
      </div>

      <!-- ====================================  Dipti -->
      <div class="relative z-10 mt-7 px-12">
        <div class="grid grid-cols-4 gap-2 h-32" style="margin-top:50px;">
          <?php
          $first_page_images = array();

          if (!empty($q['gallery_images']) && is_array($q['gallery_images'])) {
            $first_page_images = $q['gallery_images'];
          }

          if (empty($first_page_images)) {
            $first_page_images = array(
              $assets . 'hotel-1.jpg',
              $assets . 'day-1.jpg',
              $assets . 'day-2.jpg',
              $assets . 'day-3.jpg'
            );
          }

          while (count($first_page_images) < 4) {
            $first_page_images[] = $first_page_images[0];
          }

          for ($gi = 0; $gi < 4; $gi++) {
          ?>
            <img src="<?= o1e($first_page_images[$gi]) ?>" alt="" class="w-full h-full object-cover rounded-md ring-1 ring-[color:var(--gold)]/40" />
          <?php } ?>
        </div>
      </div>
      <!-- ========================================= -->
      <!-- <div class="relative z-10 mt-7 px-12">
        <div class="grid grid-cols-4 gap-2 h-32">
          <?php
          // Build a small strip from real images (hotels + itinerary day photos) with graceful fallback.
          // $strip = array();
          // if (!empty($hotels[0]['hotel_photo'])) {
          //   $strip[] = $hotels[0]['hotel_photo'];
          // }
          // foreach ($itin as $__d) {
          //   if (count($strip) >= 4) break;
          //   $img = o1img(isset($__d['image']) ? $__d['image'] : '', '');
          //   if ($img !== '') {
          //     $strip[] = $img;
          //   }
          // }
          // $strip_fallback = array($assets . 'hotel-1.jpg', $assets . 'day-1.jpg', $assets . 'day-2.jpg', $assets . 'day-3.jpg');
          // for ($si = 0; $si < 4; $si++) {
          //   $src = isset($strip[$si]) ? $strip[$si] : $strip_fallback[$si];
          //   echo '<img src="' . o1e($src) . '" alt="" class="w-full h-full object-cover rounded-md ring-1 ring-[color:var(--gold)]/40"/>';
          // }
          // 
          ?>
        </div>
      </div> -->
      <div class="absolute bottom-0 left-0 right-0 z-10 px-20 pb-10 pt-24" style="background:linear-gradient(180deg, transparent, oklch(0.10 0.05 260 / 0.95))">
        <div class="flex items-end justify-between">
          <div>
            <div class="text-[10px] uppercase tracking-[0.35em] text-[color:var(--gold)] mb-1.5">Prepared Exclusively For</div>
            <div class="font-display text-4xl text-cream"><?= o1e(o1nv($ov['client_name'], $hero['client_name'])) ?></div>
            <div class="text-cream/70 text-sm mt-1 font-serif-soft italic"><?php if (o1nv($ov['guest_count'], '') !== '') { ?><?= o1e($ov['guest_count']) ?> Travellers &middot; <?php } ?><?= o1e($ov['travel_from']) ?> &ndash; <?= o1e($ov['travel_to']) ?></div>
          </div>
          <div class="text-right text-cream/80">
            <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]"><?= o1e(o1nv($hero['duration_label'], '')) ?></div>
            <div class="font-display text-2xl mt-1"><span>Package Tour</span></div>
          </div>
        </div>
      </div>
    </section>
    <section class="page" style="--wm-url:url(assets/globe-watermark.png)">
      <div class="watermark" style="background-image:url(assets/globe-watermark.png)">
      </div>
      
      <div class="relative px-20 py-6">
        <div class="rounded-xl px-6 py-4 flex items-center gap-4" style="background:var(--gradient-navy)">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles w-5 h-5 text-[color:var(--gold)]" aria-hidden="true">
            <path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z">
            </path>
            <path d="M20 2v4">
            </path>
            <path d="M22 4h-4">
            </path>
            <circle cx="4" cy="20" r="2">
            </circle>
          </svg>
          <p class="font-serif-soft italic text-cream text-[15px]">A personalized travel experience exclusively designed for <span class="gold-text font-semibold not-italic"><?= o1e(o1nv($ov['client_name'], $hero['client_name'])) ?></span>
          </p>
        </div>
        <div class="mt-7">
          <div class="divider-fancy">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart w-4 h-4" aria-hidden="true">
              <path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5">
              </path>
            </svg>
          </div>
          <h2 class="font-display text-4xl text-[color:var(--navy)] mt-3"><?php $o1cn = o1nv($ov['client_name'], $hero['client_name']);
                                                                          $o1fn = trim(strtok((string)$o1cn, ' '));
                                                                          echo o1e('Dear ' . ($o1fn !== '' ? $o1fn : $o1cn) . ','); ?></h2>
          <p class="mt-3 text-[15.5px] leading-relaxed text-[color:var(--ink)]/85 font-serif-soft text-lg">Thank you for choosing <?= o1e(o1nv($o1_company, 'us')) ?> for your upcoming journey to <?= o1e(o1nv($ov['destination'], 'your destination')) ?>. We are delighted to present this carefully crafted travel proposal &mdash; thoughtfully designed to deliver memorable experiences, seamless arrangements and exceptional hospitality at every step.</p>
        </div>
        <div class="mt-8">
          <div class="flex items-center justify-between">
            <h3 class="font-display text-2xl text-[color:var(--navy)]">Tour Overview</h3>
            <span class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">At a Glance</span>
          </div>
          <hr class="gold-rule mt-2" />
          <div class="grid grid-cols-3 gap-3 mt-5">
            <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                    <path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z">
                    </path>
                    <path d="M14 2v5a1 1 0 0 0 1 1h5">
                    </path>
                    <path d="M10 9H8">
                    </path>
                    <path d="M16 13H8">
                    </path>
                    <path d="M16 17H8">
                    </path>
                  </svg>
                </div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Quotation ID</div>
              </div>
              <div class="mt-2.5 font-display text-lg text-[color:var(--navy)]"><?= o1e($hero['quotation_code']) ?></div>
            </div>
            <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                    <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z">
                    </path>
                    <path d="m9 12 2 2 4-4">
                    </path>
                  </svg>
                </div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Tour ID</div>
              </div>
              <div class="mt-2.5 font-display text-lg text-[color:var(--navy)]"><?= o1e(o1nv($ov['tour_id'], '-')) ?></div>
            </div>
            <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                    <path d="M8 2v4">
                    </path>
                    <path d="M16 2v4">
                    </path>
                    <rect width="18" height="18" x="3" y="4" rx="2">
                    </rect>
                    <path d="M3 10h18">
                    </path>
                  </svg>
                </div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Quotation Date</div>
              </div>
              <div class="mt-2.5 font-display text-lg text-[color:var(--navy)]"><?= o1e(o1nv($ov['quotation_date'], '-')) ?></div>
            </div>
            <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                    <path d="M8 2v4">
                    </path>
                    <path d="M16 2v4">
                    </path>
                    <rect width="18" height="18" x="3" y="4" rx="2">
                    </rect>
                    <path d="M3 10h18">
                    </path>
                  </svg>
                </div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Travel Dates</div>
              </div>
              <div class="mt-2.5 font-display text-lg text-[color:var(--navy)]"><?= o1e($ov['travel_from']) ?> &ndash; <?= o1e($ov['travel_to']) ?></div>
            </div>
            <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                    <circle cx="12" cy="12" r="10">
                    </circle>
                    <path d="M12 6v6l4 2">
                    </path>
                  </svg>
                </div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Duration</div>
              </div>
              <div class="mt-2.5 font-display text-lg text-[color:var(--navy)]"><?php $o1n = (int) o1nv($hero['total_nights'], (int)o1nv($hero['total_days'], 0));
                                                                                echo o1e($o1n . ' Nights / ' . ($o1n + 1) . ' Days'); ?></div>
            </div>
            <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2">
                    </path>
                    <path d="M16 3.128a4 4 0 0 1 0 7.744">
                    </path>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87">
                    </path>
                    <circle cx="9" cy="7" r="4">
                    </circle>
                  </svg>
                </div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Guests</div>
              </div>
              <div class="mt-2.5 font-display text-lg text-[color:var(--navy)]"><?php
                                                                                $o1p = isset($ov['pax']) ? $ov['pax'] : array();
                                                                                $o1parts = array();
                                                                                $o1ad = (int) o1nv(isset($o1p['adult']) ? $o1p['adult'] : 0, 0);
                                                                                $o1ch = (int) o1nv(isset($o1p['children_with_bed']) ? $o1p['children_with_bed'] : 0, 0) + (int) o1nv(isset($o1p['children_without_bed']) ? $o1p['children_without_bed'] : 0, 0);
                                                                                $o1in = (int) o1nv(isset($o1p['infant']) ? $o1p['infant'] : 0, 0);
                                                                                if ($o1ad) {
                                                                                  $o1parts[] = $o1ad . ' Adult' . ($o1ad > 1 ? 's' : '');
                                                                                }
                                                                                if ($o1ch) {
                                                                                  $o1parts[] = $o1ch . ' Child' . ($o1ch > 1 ? 'ren' : '');
                                                                                }
                                                                                if ($o1in) {
                                                                                  $o1parts[] = $o1in . ' Infant' . ($o1in > 1 ? 's' : '');
                                                                                }
                                                                                echo o1e($o1parts ? implode(', ', $o1parts) : o1nv($ov['guest_count'], '-'));
                                                                                ?></div>
            </div>
          </div>
        </div>
        <div class="grid grid-cols-5 gap-4 mt-6">
          <div class="col-span-2 rounded-xl p-5 text-cream relative overflow-hidden" style="background:var(--gradient-navy)">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-briefcase w-5 h-5 text-[color:var(--gold)]" aria-hidden="true">
              <path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16">
              </path>
              <rect width="20" height="14" x="2" y="6" rx="2">
              </rect>
            </svg>
            <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)] mt-2">Package Type</div>
            <div class="font-display text-3xl mt-1"><span><?= o1e(o1nv($o1_row['package_type'], 'Package')) ?></span></div>
            <div class="text-cream/70 text-xs mt-1.5 font-serif-soft italic">Hand-picked value with luxury touchpoints.</div>
            <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full" style="background:oklch(0.78 0.13 78 / 0.2)">
            </div>
          </div>
          <div class="col-span-3 rounded-xl bg-white p-5 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
            <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Prepared For</div>
            <div class="font-display text-2xl text-[color:var(--navy)] mt-1"><?= o1e(o1nv($ov['client_name'], $hero['client_name'])) ?></div>
            <div class="mt-3 space-y-1.5 text-[13px] text-[color:var(--ink)]/85">
              <!-- ================== Dipti -->
              <?php
              $customer_email = o1nv($ov['customer_email'], o1nv($hero['user_email_id'], ''));
              ?>
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail w-3.5 h-3.5 text-[color:var(--teal)]" aria-hidden="true">
                  <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7">
                  </path>
                  <rect x="2" y="4" width="20" height="16" rx="2">
                  </rect>
                </svg> <?php if ($customer_email != '') { ?>
                  <a href="mailto:<?= o1e($customer_email) ?>" style="color:inherit;text-decoration:none;position:relative;z-index:9999;pointer-events:auto;cursor:pointer;">
                    <?= o1e($customer_email) ?>
                  </a>
                <?php } ?>
              </div>

              <?php
              $customer_mobile = o1nv($ov['customer_mobile'], o1nv($hero['user_contact'], ''));
              $customer_mobile_href = preg_replace('/[^0-9+]/', '', $customer_mobile);
              ?>
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone w-3.5 h-3.5 text-[color:var(--teal)]" aria-hidden="true">
                  <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384">
                  </path>
                </svg> <? //= o1e(o1nv($ov['customer_mobile'], o1nv($hero['user_contact'], ''))) 
                        ?>
                <?php if ($customer_mobile != '') { ?>
                  <a href="tel:<?= o1e($customer_mobile_href) ?>" style="color:inherit;text-decoration:none;position:relative;z-index:9999;pointer-events:auto;cursor:pointer;">
                    <?= o1e($customer_mobile) ?>
                  </a>
                <?php } ?>
              </div>
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-3.5 h-3.5 text-[color:var(--teal)]" aria-hidden="true">
                  <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                  </path>
                  <circle cx="12" cy="10" r="3">
                  </circle>
                </svg> <?= o1e(o1nv($ov['destination'], 'Destination')) ?>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-6 rounded-xl overflow-hidden relative h-36">
          <!-- <img src="<?= o1e(o1img($hero['cover_image'], (isset($itin[0]['image']) ? o1img($itin[0]['image'], $assets . 'day-1.jpg') : $assets . 'day-1.jpg'))) ?>" alt="<?= o1e(o1nv($ov['destination'], '')) ?>" class="absolute inset-0 w-full h-full object-cover" loading="lazy" /> -->
          <img
            src="<?= o1e(o1img(isset($hero['destination_5th_gallery_image']) ? $hero['destination_5th_gallery_image'] : '', (isset($itin[0]['image']) ? o1img($itin[0]['image'], $assets . 'day-1.jpg') : $assets . 'day-1.jpg'))) ?>"
            alt="<?= o1e(o1nv($ov['destination'], '')) ?>"
            class="absolute inset-0 w-full h-full object-cover" />
          <div class="absolute inset-0" style="background:linear-gradient(90deg, oklch(0.20 0.06 260 / 0.85), oklch(0.20 0.06 260 / 0.2))">
          </div>

          <div class="relative p-5 text-cream h-full flex flex-col justify-between">
            <div class="flex items-center gap-2 text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-compass w-3.5 h-3.5" aria-hidden="true">
                <circle cx="12" cy="12" r="10">
                </circle>
                <path d="m16.24 7.76-1.804 5.411a2 2 0 0 1-1.265 1.265L7.76 16.24l1.804-5.411a2 2 0 0 1 1.265-1.265z">
                </path>
              </svg> Destination
            </div>
            <div>
              <div class="font-display text-3xl"><?= o1e(o1nv($ov['destination'], $hero['tour_name'])) ?></div>
              <div class="dest-mb text-cream/80 text-xs font-serif-soft italic">Where futuristic skylines meet tropical serenity.</div>
            </div>
          </div>
        </div>
      </div>
    
    </section>

    <section class="page" style="--wm-url:url(assets/globe-watermark.png)">
      <div class="watermark" style="background-image:url(assets/globe-watermark.png)">
      </div>
      
      <div class="relative px-20 py-8">
        <div class="flex items-end justify-between">
          <div>
            <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Where You&#x27;ll Stay</div>
            <h2 class="font-display text-4xl text-[color:var(--navy)] mt-1">Accommodation Details</h2>
          </div>
          <div class="rounded-full px-4 py-1.5 text-cream text-xs uppercase tracking-[0.2em]" style="background:var(--gradient-navy)">
            <span class="text-[color:var(--gold)] mr-2">✦</span>
            
            <?= o1e($o1_pkg_label) ?>
          </div>
        </div>
        <hr class="gold-rule mt-3" />
        <div class="mt-6 space-y-4">
          <?php
          $o1_hotels = is_array($hotels) ? $hotels : array();
          if (empty($o1_hotels)) {
            $o1_hotels = array();
          }
          $o1_hi = 0;
          foreach ($o1_hotels as $h):
            $o1_hi++;
            $o1_hnights = (int) o1nv(isset($h['total_nights']) ? $h['total_nights'] : '', 0);
            // $o1_hphoto  = o1img(isset($h['hotel_photo']) ? $h['hotel_photo'] : '', $assets . 'hotel-' . ((($o1_hi - 1) % 3) + 1) . '.jpg');
            $dummy_hotel_img = BASE_URL . 'images/hotel.png';

            $o1_hphoto = '';
            if (!empty($h['hotel_photo'])) {
              $o1_hphoto = $h['hotel_photo'];
            } else {
              $o1_hphoto = $dummy_hotel_img;
            }
          ?>
            <div class="rounded-xl overflow-hidden bg-white border border-[color:var(--gold)]/25 grid grid-cols-5" style="box-shadow:var(--shadow-card)">
              <!-- <img src="<? //= o1e($o1_hphoto) 
                              ?>" alt="<? //= o1e(o1nv(isset($h['hotel_name']) ? $h['hotel_name'] : '', 'Hotel')) 
                                        ?>" class="col-span-2 h-56 w-full object-cover" loading="lazy" /> -->
              <div class="col-span-1 relative bg-[#001c41]">
                <img src="<?= o1e($o1_hphoto) ?>" alt="<?= o1e(o1nv(isset($h['hotel_name']) ? $h['hotel_name'] : '', 'Hotel')) ?>" class="col-span-2 h-56 w-full object-contain" />
              </div>
              <div class="col-span-4 p-5">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-1.5 text-[12px] uppercase tracking-[0.22em] text-[color:var(--teal)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-3 h-3" aria-hidden="true">
                      <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                      </path>
                      <circle cx="12" cy="10" r="3">
                      </circle>
                    </svg> <!-- --><?= o1e(o1nv(isset($h['hotel_city']) ? $h['hotel_city'] : '', '')) ?>
                  </div>
                  <?php
                  $o1_rating = isset($h['rating']) ? $h['rating'] : '';
                  $o1_star_count = (int) preg_replace('/[^0-9]/', '', $o1_rating);

                  if ($o1_star_count <= 0) {
                    $o1_star_count = 0;
                  }
                  if ($o1_star_count > 5) {
                    $o1_star_count = 5;
                  }
                  ?>
                  <!-- ================= Hotels rating Dipti -->

                  <div class="flex gap-0.5 text-[color:var(--gold)]">
                    <?php for ($s = 1; $s <= $o1_star_count; $s++) { ?>
                      <svg xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="lucide lucide-star w-3 h-3 fill-current"
                        aria-hidden="true">
                        <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path>
                      </svg>
                    <?php } ?>

                    <?php if ($o1_star_count == 0) { ?>
                      <span class="text-[10px] text-[color:var(--ink)]/50">No Rating</span>
                    <?php } ?>
                  </div>
                  <!-- ====================== -->
                </div>
                <h3 class="font-display text-2xl text-[color:var(--navy)] mt-1.5"><?= o1e(o1nv(isset($h['hotel_name']) ? $h['hotel_name'] : '', 'Hotel')) ?></h3>
                <div class="text-[13px] font-serif-soft italic text-[color:var(--ink)]/70 mt-0.5"><?= o1e(o1nv(isset($h['room_category']) ? $h['room_category'] : '', o1nv(isset($h['room_type']) ? $h['room_type'] : '', ''))) ?></div>
                <div class="grid grid-cols-3 gap-3 mt-4">
                  <div class="rounded-lg bg-[color:var(--cream)] p-2.5 border border-[color:var(--gold)]/20">
                    <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Check-in</div>
                    <div class="font-display text-sm text-[color:var(--navy)] mt-0.5"><?= o1e(o1nv(isset($h['check_in']) ? $h['check_in'] : '', 'NA')) ?></div>
                  </div>
                  <div class="rounded-lg bg-[color:var(--cream)] p-2.5 border border-[color:var(--gold)]/20">
                    <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Check-out</div>
                    <div class="font-display text-sm text-[color:var(--navy)] mt-0.5"><?= o1e(o1nv(isset($h['check_out']) ? $h['check_out'] : '', 'NA')) ?></div>
                  </div>
                  <div class="rounded-lg p-2.5 text-cream" style="background:var(--gradient-navy)">
                    <div class="text-[9px] uppercase tracking-[0.22em] text-[color:var(--gold)]">Nights</div>
                    <div class="font-display text-sm mt-0.5"><?= o1e($o1_hnights) ?> Night<?= ($o1_hnights == 1 ? '' : 's') ?></div>
                  </div>
                </div>
                <div class="flex flex-wrap gap-2 mt-3">
                  <span class="text-[10px] px-2.5 py-1 rounded-full bg-[color:var(--gold-soft)]/30 text-[color:var(--navy)] border border-[color:var(--gold)]/30">TV Screen</span>
                  <span class="text-[10px] px-2.5 py-1 rounded-full bg-[color:var(--gold-soft)]/30 text-[color:var(--navy)] border border-[color:var(--gold)]/30">Coffee Maker</span>
                  <span class="text-[10px] px-2.5 py-1 rounded-full bg-[color:var(--gold-soft)]/30 text-[color:var(--navy)] border border-[color:var(--gold)]/30">Wardrobe</span>
                  <span class="text-[10px] px-2.5 py-1 rounded-full bg-[color:var(--gold-soft)]/30 text-[color:var(--navy)] border border-[color:var(--gold)]/30">Room service</span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
          <?php if (empty($o1_hotels)): ?>
            <div class="rounded-xl bg-white border border-[color:var(--gold)]/25 p-6 text-center text-[color:var(--ink)]/60 text-sm">No accommodation details available.</div>
          <?php endif; ?>
        </div>
      </div>
    
    </section>
    <section class="page print-section" style="--wm-url:url(assets/globe-watermark.png)">
      <div class="watermark" style="background-image:url(assets/globe-watermark.png)">
      </div>
      <div class="header-strip">
        <div class="px-10 py-4 flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <div class="relative w-10 h-10 rounded-full grid place-items-center" style="background:var(--gradient-gold)">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-compass w-5 h-5 text-[color:var(--navy)]" aria-hidden="true">
                <circle cx="12" cy="12" r="10">
                </circle>
                <path d="m16.24 7.76-1.804 5.411a2 2 0 0 1-1.265 1.265L7.76 16.24l1.804-5.411a2 2 0 0 1 1.265-1.265z">
                </path>
              </svg>
            </div>
            <div class="leading-tight text-cream">
              <div class="font-display text-lg font-bold tracking-wide"><?= o1e($o1_company) ?></div>
              <div class="text-[9px] uppercase tracking-[0.25em] opacity-80">Luxury Voyages</div>
            </div>
          </div>
          <div class="text-cream text-right">
            <div class="text-[10px] uppercase tracking-[0.3em] opacity-70">Flights Â· Transfers Â· Itinerary</div>
            <div class="font-display text-sm"><?= o1e($hero['quotation_code']) ?> &middot; <?= o1e(o1nv($ov['destination'], $hero['tour_name'])) ?></div>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-[10px] uppercase tracking-[0.25em] opacity-70 text-cream">Page</span>
            <span class="font-display text-2xl text-[color:var(--gold)]">04</span>
          </div>
        </div>
      </div>
      <div class="relative px-20 py-7 space-y-6">
        <div class="print-section">
          <div class="flex items-center justify-between">
            <h2 class="font-display text-2xl text-[color:var(--navy)] flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plane w-5 h-5 text-[color:var(--gold)]" aria-hidden="true">
                <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z">
                </path>
              </svg> Flight Details
            </h2>
            <span class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Boarding Passes</span>
          </div>
          <hr class="gold-rule mt-2" />

          <!-- ======================== Dipti -->
          <div class="grid grid-cols-2 gap-3 mt-4">
            <?php foreach ((array)$flights as $f): ?>

              <?php
              $from = isset($f['from_city']) ? $f['from_city'] : '';
              $to   = isset($f['to_city']) ? $f['to_city'] : '';
              // echo '<pre>';
              // print_r($f);
              // echo '</pre>';
              $from_code = o1_airport_code($from);
              $to_code   = o1_airport_code($to);

              $from_name = o1_airport_name($from);
              $to_name   = o1_airport_name($to);

              $airline_name = o1nv(isset($f['airline_display']) ? $f['airline_display'] : '', o1nv(isset($f['airline_name']) ? $f['airline_name'] : '', 'Flight'));
              $airline_code = o1nv(isset($f['airline_code']) ? $f['airline_code'] : '', 'FL');
              ?>

              <div class="rounded-xl bg-white overflow-hidden border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
                <div class="px-4 py-2 flex items-center justify-between text-cream" style="background:var(--gradient-navy)">
                  <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center overflow-hidden">
                      <?php if (!empty($f['airline_logo'])): ?>
                        <img src="<?= o1e($f['airline_logo']) ?>"
                          alt="<?= o1e($airline_name) ?>"
                          class="w-full h-full object-contain">
                      <?php else: ?>
                        <span class="font-display font-bold text-[color:var(--navy)]">
                          <?= o1e(substr($airline_code, 0, 2)) ?>
                        </span>
                      <?php endif; ?>
                    </div>
                    <div class="text-xs"><?= o1e($airline_name) ?></div>
                  </div>
                  <div class="text-[10px] uppercase tracking-[0.2em] text-[color:var(--gold)]">
                    <?= o1e(o1nv(isset($f['class']) ? $f['class'] : '', 'Economy')) ?>
                  </div>
                </div>

                <div class="p-4">
                  <div class="flex items-center justify-between">
                    <div style="max-width:110px;">
                      <div class="font-display text-2xl text-[color:var(--navy)]"><?= o1e($from_code) ?></div>
                      <div class="text-[10px] text-[color:var(--ink)]/60 uppercase tracking-wide"><?= o1e($from_name) ?></div>
                    </div>

                    <div class="flex-1 px-4 flex flex-col items-center">
                      <div class="text-[10px] uppercase tracking-[0.25em] text-[color:var(--gold)]">Flight</div>
                      <div class="relative w-full h-px bg-[color:var(--gold)]/40 my-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plane w-3 h-3 text-[color:var(--gold)] absolute -top-1.5 left-1/2 -translate-x-1/2" aria-hidden="true">
                          <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"></path>
                        </svg>
                      </div>
                      <div class="text-[9px] text-[color:var(--ink)]/60">As per itinerary</div>
                    </div>

                    <div class="text-right" style="max-width:110px;">
                      <div class="font-display text-2xl text-[color:var(--navy)]"><?= o1e($to_code) ?></div>
                      <div class="text-[10px] text-[color:var(--ink)]/60 uppercase tracking-wide"><?= o1e($to_name) ?></div>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-dashed border-[color:var(--gold)]/40 text-[10px]">
                    <div>
                      <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60">Departure</div>
                      <div class="font-display text-[13px] text-[color:var(--navy)]">
                        <?= o1e(o1_flight_date(isset($f['departure_raw']) ? $f['departure_raw'] : '')) ?>
                      </div>
                    </div>
                    <div>
                      <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60">Arrival</div>
                      <div class="font-display text-[13px] text-[color:var(--navy)]">
                        <?= o1e(o1_flight_date(isset($f['arrival_raw']) ? $f['arrival_raw'] : '')) ?>
                      </div>
                    </div>
                    <!-- <div>
                      <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60">Baggage</div>
                      <div class="font-display text-[13px] text-[color:var(--navy)]">
                        <? //= o1e(o1nv(isset($f['baggage']) ? $f['baggage'] : '', 'NA')) 
                        ?>
                      </div>
                    </div> -->
                  </div>
                </div>
              </div>

            <?php endforeach; ?>

            <?php if (empty($flights)): ?>
              <div class="col-span-2 rounded-xl bg-white border border-[color:var(--gold)]/25 p-5 text-center text-[color:var(--ink)]/60 text-sm">No flight details available.</div>
            <?php endif; ?>
          </div>
        </div>
        <!-- ======================== Train Details  -->
        <?php if (!empty($trains)) { ?>
          <div class="mt-6">
            <div class="flex items-center justify-between">
              <h2 class="font-display text-2xl text-[color:var(--navy)] flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-train w-5 h-5 text-[color:var(--gold)]" aria-hidden="true">
                  <path d="M4 15.5A3.5 3.5 0 0 0 7.5 19h9a3.5 3.5 0 0 0 3.5-3.5V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2z"></path>
                  <path d="M8 19l-2 3"></path>
                  <path d="M16 19l2 3"></path>
                  <path d="M8 7h8"></path>
                  <path d="M8 11h8"></path>
                </svg>
                Train Details
              </h2>
              <span class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Rail Tickets</span>
            </div>

            <hr class="gold-rule mt-2" />

            <div class="grid grid-cols-2 gap-3 mt-4">
              <?php foreach ((array)$trains as $tr) { ?>

                <?php
                $train_img = BASE_URL . 'images/train.jpg';

                $from_loc = isset($tr['from_location']) ? $tr['from_location'] : '';
                $to_loc   = isset($tr['to_location']) ? $tr['to_location'] : '';

                $from_date = isset($tr['from_date']) ? $tr['from_date'] : '';
                $to_date   = isset($tr['to_date']) ? $tr['to_date'] : '';

                $train_class = isset($tr['class']) ? $tr['class'] : 'NA';

                $total_pax = 0;
                if (isset($ov['pax']) && is_array($ov['pax'])) {
                  $total_pax =
                    (int)o1nv(isset($ov['pax']['adult']) ? $ov['pax']['adult'] : 0, 0) +
                    (int)o1nv(isset($ov['pax']['children_with_bed']) ? $ov['pax']['children_with_bed'] : 0, 0) +
                    (int)o1nv(isset($ov['pax']['children_without_bed']) ? $ov['pax']['children_without_bed'] : 0, 0) +
                    (int)o1nv(isset($ov['pax']['infant']) ? $ov['pax']['infant'] : 0, 0);
                }
                ?>

                <div class="rounded-xl bg-white overflow-hidden border border-[color:var(--gold)]/25"
                  style="box-shadow:var(--shadow-card)">

                  <div class="px-4 py-2 flex items-center justify-between text-cream"
                    style="background:var(--gradient-navy)">
                    <div class="flex items-center gap-2">
                      <!-- <div class="w-7 h-7 rounded-full grid place-items-center font-display font-bold text-[color:var(--navy)]"
                        style="background:var(--gradient-gold)">TR</div> -->
                      <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center overflow-hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                          viewBox="0 0 24 24" fill="none" stroke="currentColor"
                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          class="w-5 h-5 text-[color:var(--navy)]">
                          <path d="M4 15.5A3.5 3.5 0 0 0 7.5 19h9a3.5 3.5 0 0 0 3.5-3.5V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2z"></path>
                          <path d="M8 19l-2 3"></path>
                          <path d="M16 19l2 3"></path>
                          <path d="M8 7h8"></path>
                          <path d="M8 11h8"></path>
                        </svg>
                      </div>
                      <div class="text-xs">Train Journey</div>
                    </div>
                    <div class="text-[10px] uppercase tracking-[0.2em] text-[color:var(--gold)]">
                      <?= o1e($train_class) ?>
                    </div>
                  </div>

                  <div class="p-4">
                    <div class="flex items-center justify-between">
                      <div style="max-width:130px;">
                        <div class="font-display text-2xl text-[color:var(--navy)]">
                          <?= o1e(strtoupper(substr($from_loc, 0, 3))) ?>
                        </div>
                        <div class="text-[10px] text-[color:var(--ink)]/60 uppercase tracking-wide">
                          <?= o1e($from_loc) ?>
                        </div>
                      </div>

                      <div class="flex-1 px-4 flex flex-col items-center">
                        <div class="text-[10px] uppercase tracking-[0.25em] text-[color:var(--gold)]">Train</div>
                        <div class="relative w-full h-px bg-[color:var(--gold)]/40 my-1">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="w-3 h-3 text-[color:var(--gold)] absolute -top-1.5 left-1/2 -translate-x-1/2">
                            <path d="M4 15.5A3.5 3.5 0 0 0 7.5 19h9a3.5 3.5 0 0 0 3.5-3.5V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2z"></path>
                            <path d="M8 19l-2 3"></path>
                            <path d="M16 19l2 3"></path>
                            <path d="M8 7h8"></path>
                            <path d="M8 11h8"></path>
                          </svg>
                        </div>
                        <div class="text-[9px] text-[color:var(--ink)]/60">Rail journey</div>
                      </div>

                      <div class="text-right" style="max-width:130px;">
                        <div class="font-display text-2xl text-[color:var(--navy)]">
                          <?= o1e(strtoupper(substr($to_loc, 0, 3))) ?>
                        </div>
                        <div class="text-[10px] text-[color:var(--ink)]/60 uppercase tracking-wide">
                          <?= o1e($to_loc) ?>
                        </div>
                      </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-dashed border-[color:var(--gold)]/40 text-[10px]">
                      <div>
                        <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60">Date & Time</div>
                        <div class="font-display text-[13px] text-[color:var(--navy)]">
                          <?= o1e(o1nv($from_date, 'NA')) ?>
                        </div>
                      </div>

                      <div>
                        <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60">Total Pax</div>
                        <div class="font-display text-[13px] text-[color:var(--navy)]">
                          <?= o1e($total_pax) ?>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>

              <?php } ?>
            </div>
          </div>
        <?php } ?>
        <!-- ================================= -->

        <!-- ======================== Activity Details -->
        <?php if (!empty($acts)) { ?>
          <div class="mt-6">
            <h2 class="font-display text-2xl text-[color:var(--navy)] flex items-center gap-2">
              Activity Details
            </h2>
            <hr class="gold-rule mt-2" />

            <?php foreach ((array)$acts as $a) { ?>

              <?php
              // $activity_img = BASE_URL . 'uploads/quotation_images/activity.jpg';
              $dummy_activity_img = BASE_URL . 'images/activity.jpg';

              $activity_img = '';
              if (!empty($a['activity_image'])) {
                $activity_img = $a['activity_image'];
              } else {
                $activity_img = $dummy_activity_img;
              }

              $activity_name = isset($a['activity_name']) ? $a['activity_name'] : '';
              $city_name     = isset($a['city_name']) ? $a['city_name'] : '';
              $activity_date = isset($a['date']) ? $a['date'] : '';
              $transfer_type = isset($a['transfer_type']) ? $a['transfer_type'] : '';

              $total_pax = 0;
              if (isset($a['pax']) && is_array($a['pax'])) {
                $total_pax =
                  (int)o1nv(isset($a['pax']['adult']) ? $a['pax']['adult'] : 0, 0) +
                  (int)o1nv(isset($a['pax']['chwb']) ? $a['pax']['chwb'] : 0, 0) +
                  (int)o1nv(isset($a['pax']['chwob']) ? $a['pax']['chwob'] : 0, 0) +
                  (int)o1nv(isset($a['pax']['infant']) ? $a['pax']['infant'] : 0, 0);
              }
              ?>

              <div class="rounded-xl bg-white border border-[color:var(--gold)]/25 grid grid-cols-5 mt-3 overflow-hidden"
                style="box-shadow:var(--shadow-card)">

                <div class="col-span-2 bg-[color:var(--navy)] grid place-items-center p-4">
                  <img src="<?= o1e($activity_img) ?>"
                    alt="Activity"
                    class="w-full object-contain" />
                </div>

                <div class="col-span-3 p-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--teal)]">
                        <?= o1e(o1nv($city_name, 'City')) ?>
                      </div>

                      <h3 class="font-display text-xl text-[color:var(--navy)]">
                        <?= o1e(o1nv($activity_name, 'Activity')) ?>
                      </h3>
                    </div>

                    <span class="text-[10px] px-2.5 py-1 rounded-full bg-[color:var(--gold-soft)]/40 text-[color:var(--navy)]">
                      <?= o1e($total_pax) ?> Pax
                    </span>
                  </div>

                  <div class="grid grid-cols-3 gap-2 mt-3 text-[11px]">
                    <div class="rounded-lg bg-[color:var(--cream)] p-2 border border-[color:var(--gold)]/20">
                      <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60 text-[9px]">City</div>
                      <div class="font-display text-[13px] text-[color:var(--navy)]">
                        <?= o1e(o1nv($city_name, 'NA')) ?>
                      </div>
                    </div>

                    <div class="rounded-lg bg-[color:var(--cream)] p-2 border border-[color:var(--gold)]/20">
                      <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60 text-[9px]">Date</div>
                      <div class="font-display text-[13px] text-[color:var(--navy)]">
                        <?= o1e(o1nv($activity_date, 'NA')) ?>
                      </div>
                    </div>

                    <div class="rounded-lg bg-[color:var(--cream)] p-2 border border-[color:var(--gold)]/20">
                      <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60 text-[9px]">Transfer</div>
                      <div class="font-display text-[13px] text-[color:var(--navy)]">
                        <?= o1e(o1nv($transfer_type, 'NA')) ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            <?php } ?>
          </div>
        <?php } ?>
        <!-- =================================== -->
        <div>
          <h2 class="font-display text-2xl text-[color:var(--navy)] flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-car w-5 h-5 text-[color:var(--gold)]" aria-hidden="true">
              <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2">
              </path>
              <circle cx="7" cy="17" r="2">
              </circle>
              <path d="M9 17h6">
              </path>
              <circle cx="17" cy="17" r="2">
              </circle>
            </svg> Transportation
          </h2>
          <hr class="gold-rule mt-2" />
          <?php foreach ((array)$vehs as $o1_vehicle): ?>

            <div class="rounded-xl bg-white border border-[color:var(--gold)]/25 grid grid-cols-5 mt-3 overflow-hidden"
              style="box-shadow:var(--shadow-card)">

              <div class="col-span-2 bg-[color:var(--navy)] grid place-items-center p-4">
                <?php $vehicle_img = BASE_URL . 'images/vehicle.png'; ?>
                <img src="<?= o1e($vehicle_img) ?>"
                  alt="Vehicle"
                  style="width:100%;height:auto;object-fit:contain;" />
              </div>

              <div class="col-span-3 p-4">
                <div class="flex items-center justify-between">
                  <div>
                    <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--teal)]">
                      Vehicle
                    </div>
                    <h3 class="font-display text-xl text-[color:var(--navy)]">
                      <?= o1e(o1nv($o1_vehicle['vehicle_name'], 'Vehicle details')) ?>
                    </h3>
                  </div>

                  <span class="text-[10px] px-2.5 py-1 rounded-full bg-[color:var(--gold-soft)]/40 text-[color:var(--navy)]">
                    <?= o1e($o1_vehicle['vehicle_count']) ?> Vehicle<?= ($o1_vehicle['vehicle_count'] == 1 ? '' : 's') ?>
                  </span>
                </div>

                <div class="grid grid-cols-3 gap-2 mt-3 text-[11px]">
                  <div class="rounded-lg bg-[color:var(--cream)] p-2 border border-[color:var(--gold)]/20">
                    <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60 text-[9px]">Pickup</div>
                    <div class="font-display text-[13px] text-[color:var(--navy)]">
                      <?= o1e($o1_vehicle['pickup']) ?>
                    </div>
                  </div>

                  <div class="rounded-lg bg-[color:var(--cream)] p-2 border border-[color:var(--gold)]/20">
                    <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60 text-[9px]">Drop</div>
                    <div class="font-display text-[13px] text-[color:var(--navy)]">
                      <?= o1e($o1_vehicle['drop']) ?>
                    </div>
                  </div>

                  <div class="rounded-lg bg-[color:var(--cream)] p-2 border border-[color:var(--gold)]/20">
                    <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60 text-[9px]">Duration</div>
                    <div class="font-display text-[13px] text-[color:var(--navy)]">
                      <?= o1e($o1_vehicle['service_duration']) ?>
                    </div>
                  </div>
                </div>

                <div class="text-[11px] text-[color:var(--ink)]/70 mt-2 font-serif-soft italic">
                  <?= o1e($o1_vehicle['date']) ?>
                  <?php if (!empty($o1_vehicle['description'])): ?>
                    &mdash; <?= o1e($o1_vehicle['description']) ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>

          <?php endforeach; ?>
        </div>
        <div>
    </section>

    <section class="page itinerary-page ">
      <div class="watermark"></div>
      
      <div class="relative px-20 py-7">
        <div class="flex items-end justify-between">
          <h2 class="font-display text-2xl text-[color:var(--navy)] flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-camera w-5 h-5 text-[color:var(--gold)]" aria-hidden="true">
              <path d="M13.997 4a2 2 0 0 1 1.76 1.05l.486.9A2 2 0 0 0 18.003 7H20a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.997a2 2 0 0 0 1.759-1.048l.489-.904A2 2 0 0 1 10.004 4z">
              </path>
              <circle cx="12" cy="13" r="3">
              </circle>
            </svg> Day-Wise Itinerary
          </h2>
          <span class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Your Journey · Day by Day</span>
        </div>

        <hr class="gold-rule mt-2" />
        <div class="mt-4 space-y-4">
          <?php
          $o1_itin = is_array($itin) ? $itin : array();
          foreach ($o1_itin as $d):
            $o1_dno    = str_pad((string) (int) o1nv(isset($d['day_number']) ? $d['day_number'] : '', 0), 2, '0', STR_PAD_LEFT);
            // $o1_dimg   = o1img(isset($d['image']) ? $d['image'] : '', $assets . 'day-1.jpg');
            $dummy_day_img = BASE_URL . 'images/itinerary.png';

            $o1_day_photo = isset($d['image']) ? trim($d['image']) : '';

            if ($o1_day_photo == '' || stripos($o1_day_photo, 'dummy') !== false) {
              $o1_dimg = $dummy_day_img;
            } else {
              $o1_dimg = o1img($o1_day_photo, $dummy_day_img);
            }
            $o1_dtitle = o1nv(isset($d['special_attraction']) ? $d['special_attraction'] : '', o1nv(isset($d['city']) ? $d['city'] : '', 'Day ' . $o1_dno));
            $o1_dprog  = isset($d['detailed_programme']) ? trim($d['detailed_programme']) : '';
          ?>
            <!-- <div class="relative rounded-2xl overflow-hidden bg-white border border-[color:var(--gold)]/30 grid grid-cols-12" style="box-shadow:var(--shadow-card)"> -->
            <!-- ========================  Dipti add just itinerary-card  -->
            <div class="itinerary-card relative rounded-2xl overflow-hidden bg-white border border-[color:var(--gold)]/30 grid grid-cols-12" style="box-shadow:var(--shadow-card)">
              <div class="col-span-4 relative">
                <img src="<?= o1e($o1_dimg) ?>" alt="<?= o1e($o1_dtitle) ?>" class="w-full h-full object-cover absolute inset-0" />
                <div class="absolute inset-0" style="background:linear-gradient(135deg, oklch(0.20 0.06 260 / 0.55), oklch(0.20 0.06 260 / 0.05) 60%)">
                </div>
                <div class="absolute top-3 left-3 rounded-xl px-3 py-2 text-cream backdrop-blur-md" style="background:oklch(0.20 0.06 260 / 0.75)">
                  <div class="text-[8px] uppercase tracking-[0.3em] text-[color:var(--gold)] leading-none">Day</div>
                  <div class="font-display text-3xl leading-none mt-0.5"><?= o1e($o1_dno) ?></div>
                </div>
                <div class="absolute bottom-3 left-3 right-3 text-cream">
                  <!-- <div class="text-[10px] uppercase tracking-[0.25em] text-[color:var(--gold)]"><? //= o1e(o1nv(isset($d['date']) ? $d['date'] : '', '')) 
                                                                                                      ?></div> -->
                  <!-- <div class="font-display text-lg leading-tight drop-shadow"><? //= o1e($o1_dtitle) 
                                                                                    ?></div> -->
                </div>
              </div>
              <div class="col-span-8 p-4 relative">

                <!-- ============== Dipti -->
                <?php $o1_day_date = isset($d['date']) ? trim($d['date']) : ''; ?>

                <?php if ($o1_day_date != '') { ?>
                  <div class="absolute top-0 right-0 px-3 py-1 text-[9px] uppercase tracking-[0.22em] text-[color:var(--navy)] rounded-bl-xl"
                    style="background:var(--gradient-gold); z-index:50;">
                    <?= o1e($o1_day_date) ?>
                  </div>
                <?php } ?>
                <!-- ================================= -->


                <?php if ($o1_dtitle !== ''): ?>
                  <div class="flex items-center gap-2 text-[14px] uppercase tracking-[0.22em] text-[color:var(--teal)] mt-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-3 h-3" aria-hidden="true">
                      <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                      </path>
                      <circle cx="12" cy="10" r="3">
                      </circle>
                    </svg> <!-- --><?= o1e(o1nv($d['special_attraction'] ?? '', $o1_dtitle)) ?>
                  </div>
                <?php endif; ?>
                <p class="text-[13px] text-[color:var(--ink)]/85 mt-2 leading-relaxed font-serif-soft" style="font-size:13px"><?= ($o1_dprog !== '' ? $o1_dprog : 'Detailed programme will be shared.') ?></p>
                <!-- ============================== Dipti -->
                <div class="mt-3 pt-3 border-t border-dashed border-[color:var(--gold)]/40">
                  <div class="flex flex-wrap items-center gap-3">

                    <?php if (!empty($d['overnight_stay'])): ?>
                      <span class="inline-flex items-center gap-1.5 text-[10px] text-[color:var(--ink)]/75">

                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                          viewBox="0 0 24 24" fill="none" stroke="currentColor"
                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          class="lucide lucide-hotel w-3 h-3 text-[color:var(--gold)]">

                          <path d="M10 22v-6.57"></path>
                          <path d="M12 11h.01"></path>
                          <path d="M12 7h.01"></path>
                          <path d="M14 15.43V22"></path>
                          <path d="M15 16a5 5 0 0 0-6 0"></path>
                          <path d="M16 11h.01"></path>
                          <path d="M16 7h.01"></path>
                          <path d="M8 11h.01"></path>
                          <path d="M8 7h.01"></path>
                          <rect x="4" y="2" width="16" height="20" rx="2"></rect>

                        </svg>

                        <strong>Overnight Stay:</strong>
                        <?= o1e($d['overnight_stay']) ?>

                      </span>
                    <?php endif; ?>

                    <?php if (!empty($d['meal_plan'])): ?>
                      <span class="inline-flex items-center gap-1.5 text-[10px] text-[color:var(--ink)]/75">

                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                          viewBox="0 0 24 24" fill="none" stroke="currentColor"
                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          class="lucide lucide-utensils w-3 h-3 text-[color:var(--gold)]">

                          <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path>
                          <path d="M7 2v20"></path>
                          <path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path>

                        </svg>

                        <strong>Meal Plan:</strong>
                        <?= o1e($d['meal_plan']) ?>

                      </span>
                    <?php endif; ?>

                  </div>
                </div>
                <!-- ================================== -->
              </div>
            </div>
          <?php endforeach; ?>
          <?php if (empty($o1_itin)): ?>
            <div class="rounded-2xl bg-white border border-[color:var(--gold)]/30 p-6 text-center text-[color:var(--ink)]/60 text-sm">Day-wise itinerary will be shared.</div>
          <?php endif; ?>
          <div class="relative rounded-2xl overflow-hidden bg-white border border-[color:var(--gold)]/30 grid grid-cols-12" style="box-shadow:var(--shadow-card)" hidden>
            <div class="col-span-4 relative">
              <img src="assets/day-3.jpg" alt="Gardens by the Bay" class="w-full h-full object-cover absolute inset-0" loading="lazy" />
              <div class="absolute inset-0" style="background:linear-gradient(135deg, oklch(0.20 0.06 260 / 0.55), oklch(0.20 0.06 260 / 0.05) 60%)">
              </div>
              <div class="absolute top-3 left-3 rounded-xl px-3 py-2 text-cream backdrop-blur-md" style="background:oklch(0.20 0.06 260 / 0.75)">
                <div class="text-[8px] uppercase tracking-[0.3em] text-[color:var(--gold)] leading-none">Day</div>
                <div class="font-display text-3xl leading-none mt-0.5">03</div>
              </div>
              <div class="absolute bottom-3 left-3 right-3 text-cream">
                <div class="text-[10px] uppercase tracking-[0.25em] text-[color:var(--gold)]">12 Jul 2026</div>
                <div class="font-display text-lg leading-tight drop-shadow">Gardens by the Bay</div>
              </div>
            </div>
            <!-- <div class="col-span-8 p-4 relative"> -->
            <div class="col-span-8 p-4 relative" style="position:relative;">
              <div class="absolute top-0 right-0 px-3 py-1 text-[9px] uppercase tracking-[0.22em] text-[color:var(--navy)] rounded-bl-xl" style="background:var(--gradient-gold)">âœ¦ <!-- -->30Â°C<!-- --> · <!-- -->Highlight</div>
              <div class="flex items-center gap-2 text-[10px] uppercase tracking-[0.22em] text-[color:var(--teal)] mt-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-3 h-3" aria-hidden="true">
                  <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                  </path>
                  <circle cx="12" cy="10" r="3">
                  </circle>
                </svg> <!-- -->Supertree Grove &amp; Cloud Forest
              </div>
              <p class="text-[12px] text-[color:var(--ink)]/85 mt-2 leading-relaxed font-serif-soft" style="font-size:13px">Morning at Gardens by the Bay conservatories. Evening Garden Rhapsody show under the illuminated Supertrees.</p>
              <div class="grid grid-cols-3 gap-2 mt-3">
                <div class="rounded-lg bg-[color:var(--cream)] border border-[color:var(--gold)]/25 p-2 flex items-center gap-2">
                  <div class="w-7 h-7 rounded-md grid place-items-center shrink-0" style="background:var(--gradient-navy)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock w-3.5 h-3.5 text-[color:var(--gold)]" aria-hidden="true">
                      <circle cx="12" cy="12" r="10">
                      </circle>
                      <path d="M12 6v6l4 2">
                      </path>
                    </svg>
                  </div>
                  <div class="min-w-0">
                    <div class="font-display text-[12px] text-[color:var(--navy)] leading-none">10:00</div>
                    <div class="text-[9.5px] uppercase tracking-[0.12em] text-[color:var(--ink)]/65 truncate">Cloud Forest</div>
                  </div>
                </div>
                <div class="rounded-lg bg-[color:var(--cream)] border border-[color:var(--gold)]/25 p-2 flex items-center gap-2">
                  <div class="w-7 h-7 rounded-md grid place-items-center shrink-0" style="background:var(--gradient-navy)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock w-3.5 h-3.5 text-[color:var(--gold)]" aria-hidden="true">
                      <circle cx="12" cy="12" r="10">
                      </circle>
                      <path d="M12 6v6l4 2">
                      </path>
                    </svg>
                  </div>
                  <div class="min-w-0">
                    <div class="font-display text-[12px] text-[color:var(--navy)] leading-none">13:00</div>
                    <div class="text-[9.5px] uppercase tracking-[0.12em] text-[color:var(--ink)]/65 truncate">Flower Dome</div>
                  </div>
                </div>
                <div class="rounded-lg bg-[color:var(--cream)] border border-[color:var(--gold)]/25 p-2 flex items-center gap-2">
                  <div class="w-7 h-7 rounded-md grid place-items-center shrink-0" style="background:var(--gradient-navy)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock w-3.5 h-3.5 text-[color:var(--gold)]" aria-hidden="true">
                      <circle cx="12" cy="12" r="10">
                      </circle>
                      <path d="M12 6v6l4 2">
                      </path>
                    </svg>
                  </div>
                  <div class="min-w-0">
                    <div class="font-display text-[12px] text-[color:var(--navy)] leading-none">19:45</div>
                    <div class="text-[9.5px] uppercase tracking-[0.12em] text-[color:var(--ink)]/65 truncate">Garden Rhapsody</div>
                  </div>
                </div>
              </div>
              <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-dashed border-[color:var(--gold)]/40">
                <span class="text-[10px] px-2.5 py-1 rounded-full bg-[color:var(--gold-soft)]/40 text-[color:var(--navy)] border border-[color:var(--gold)]/30">âœ¦ <!-- -->Cloud Forest</span>
                <span class="text-[10px] px-2.5 py-1 rounded-full bg-[color:var(--gold-soft)]/40 text-[color:var(--navy)] border border-[color:var(--gold)]/30">âœ¦ <!-- -->Flower Dome</span>
                <span class="text-[10px] px-2.5 py-1 rounded-full bg-[color:var(--gold-soft)]/40 text-[color:var(--navy)] border border-[color:var(--gold)]/30">âœ¦ <!-- -->Garden Rhapsody</span>
                <span class="ml-auto inline-flex items-center gap-1.5 text-[10px] text-[color:var(--ink)]/75">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-utensils w-3 h-3 text-[color:var(--gold)]" aria-hidden="true">
                    <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2">
                    </path>
                    <path d="M7 2v20">
                    </path>
                    <path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7">
                    </path>
                  </svg> <!-- -->Breakfast</span>
                <span class="inline-flex items-center gap-1.5 text-[10px] text-[color:var(--ink)]/75">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hotel w-3 h-3 text-[color:var(--gold)]" aria-hidden="true">
                    <path d="M10 22v-6.57">
                    </path>
                    <path d="M12 11h.01">
                    </path>
                    <path d="M12 7h.01">
                    </path>
                    <path d="M14 15.43V22">
                    </path>
                    <path d="M15 16a5 5 0 0 0-6 0">
                    </path>
                    <path d="M16 11h.01">
                    </path>
                    <path d="M16 7h.01">
                    </path>
                    <path d="M8 11h.01">
                    </path>
                    <path d="M8 7h.01">
                    </path>
                    <rect x="4" y="2" width="16" height="20" rx="2">
                    </rect>
                  </svg> <!-- -->The Fullerton Heritage</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      </div>
      </div>
      <div class="page-foot  absolute bottom-0 left-0 right-0 px-10 py-3 flex items-center justify-between text-[10px] uppercase tracking-[0.25em] text-[color:var(--navy)]/60 border-t border-[color:var(--gold)]/30 bg-cream">
        <span><?= o1e($o1_company) ?> · Luxury Voyages</span>
        <span class="text-[color:var(--gold)]">✦ ✦ ✦</span>
        <span>04<!-- --> / 09</span>
      </div>
    </section>
    <section class="page page-flow  inclusion-page" style="--wm-url:url(assets/globe-watermark.png)">
      <div class="watermark" style="background-image:url(assets/globe-watermark.png)">
      </div>
      
      <div class="relative px-20 py-7">
        <div class="grid grid-cols-2 gap-4">
          <div class="rounded-xl bg-white p-5 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5">
                  </path>
                </svg>
              </div>
              <h3 class="font-display text-xl text-[color:var(--navy)]">What&#x27;s Included</h3>
            </div>
            <hr class="gold-rule mt-2" />
            <ul class="mt-3 space-y-2">
              <?php
              $o1_included_items = o1_list_items(
                isset($incx['included']) ? $incx['included'] : '',
                'Inclusions will be shared as per final quotation.'
              );
              foreach ($o1_included_items as $o1_item):
              ?>
                <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-3.5 h-3.5 text-[color:var(--teal)] mt-0.5 shrink-0" aria-hidden="true">
                    <path d="M20 6 9 17l-5-5">
                    </path>
                  </svg>
                  <span><?= o1e($o1_item) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="rounded-xl bg-white p-5 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg grid place-items-center bg-[color:var(--navy)]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-4 h-4 text-[color:var(--gold)]" aria-hidden="true">
                  <path d="M18 6 6 18">
                  </path>
                  <path d="m6 6 12 12">
                  </path>
                </svg>
              </div>
              <h3 class="font-display text-xl text-[color:var(--navy)]">What&#x27;s Excluded</h3>
            </div>
            <hr class="gold-rule mt-2" />
            <ul class="mt-3 space-y-2">
              <?php
              $o1_excluded_items = o1_list_items(
                isset($incx['excluded']) ? $incx['excluded'] : '',
                'Exclusions will be shared as per final quotation.'
              );
              foreach ($o1_excluded_items as $o1_item):
              ?>
                <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-3.5 h-3.5 text-[color:var(--navy)]/70 mt-0.5 shrink-0" aria-hidden="true">
                    <path d="M18 6 6 18">
                    </path>
                    <path d="m6 6 12 12">
                    </path>
                  </svg>
                  <span><?= o1e($o1_item) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <div class="mt-4 payment-page>
          <?php
          $o1_costing_type = isset($cost['costing_type_label']) ? strtolower(trim($cost['costing_type_label'])) : '';
          $is_per_person = ($o1_costing_type == 'per person');
          ?>
          <div class=" flex items-end justify-between">
          <h3 class="font-display text-2xl text-[color:var(--navy)]">Costing Details</h3>
          <!-- <span class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">All values in INR · per package</span> -->
          <span class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">
            All values in INR · <?= $is_per_person ? 'Per Person' : 'Per Package' ?>
          </span>
        </div>
        <hr class="gold-rule mt-2" />
        <div class="mt-4 rounded-xl overflow-hidden border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
          <?php if (!$is_per_person) { ?>
            <div class="grid grid-cols-6 text-[10px] uppercase tracking-[0.2em] text-cream px-4 py-3" style="background:var(--gradient-navy)">
              <div class="col-span-1">Package</div>
              <div class="text-right">Tour Cost</div>
              <div class="text-right">Tax</div>
              <!-- <div class="text-right">Total Amount</div> -->
              <div class="text-right">TCS</div>
              <div class="text-right">Travel</div>
              <div class="text-right text-[color:var(--gold)]">Grand Total</div>
            </div>
            <?php
            $o1_grp = isset($cost['computed']['group']) ? $cost['computed']['group'] : array();
            if (empty($o1_grp)) {
              $o1_grp = array(array('package_type' => 'Package', 'tour_cost_display' => '0', 'tax_display' => '0', 'tcs_display' => '0', 'travel_display' => '0', 'total_display' => '0'));
            }
            $o1_ci = 0;
            foreach ($o1_grp as $o1_row):
              $o1_bg = ($o1_ci % 2 == 1) ? 'bg-[color:var(--gold-soft)]/30' : 'bg-white';
            ?>
              <div class="grid grid-cols-6 items-center px-4 py-3.5 text-[12.5px] <?= $o1_bg ?> border-t border-[color:var(--gold)]/20">
                <div class="col-span-1 flex items-center gap-2">
                  <span class="font-display text-base text-[color:var(--navy)]"><?= o1e(o1nv($o1_row['package_type'], 'Package')) ?></span>
                </div>
                <div class="text-right text-[color:var(--ink)]/85">&#8377; <?= o1e($o1_row['tour_cost_display']) ?></div>
                <!-- <div class="text-right text-[color:var(--ink)]/85"><? //= o1e(o1nv($o1_row['tax_display'], '&#8377; 0')) 
                                                                        ?></div> -->
                <!-- ================== Dipti -->
                <?php
                $tax_amount = '0.00';

                if (!empty($o1_row['tax_display'])) {
                  preg_match('/INR\s*([\d,\.]+)/i', $o1_row['tax_display'], $matches);
                  $tax_amount = isset($matches[1]) ? $matches[1] : '0.00';
                }
                ?>

                <div class="text-right text-[color:var(--ink)]/85">
                  INR <?= o1e($tax_amount) ?>
                </div>
                <!-- <div class="text-right text-[color:var(--ink)]/85">&#8377; <? //= o1e(o1nv($o1_row['total_display'], '0')) 
                                                                                ?></div> -->
                <div class="text-right text-[color:var(--ink)]/85">&#8377; <?= o1e($o1_row['tcs_display']) ?></div>
                <div class="text-right text-[color:var(--ink)]/85">&#8377; <?= o1e($o1_row['travel_display']) ?></div>
                <div class="text-right font-display text-lg text-[color:var(--navy)]">&#8377; <?= o1e($o1_row['total_display']) ?></div>
              </div>
            <?php $o1_ci++;
            endforeach; ?>
          <?php } ?>
        </div>
        <!-- <div class="text-[10px] text-[color:var(--ink)]/55 mt-2 italic">* Prices indicative and subject to availability at the time of booking confirmation.</div> -->


        <?php
        $o1_pp = isset($cost['computed']['per_person']) ? $cost['computed']['per_person'] : array();
        ?>
        <?php if ($is_per_person && !empty($o1_pp)) { ?>
          <div class="mt-4 rounded-xl overflow-hidden border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
            <div style="margin-top:15px;border:1px solid #e4d3b3;border-radius:18px;overflow:hidden;">
              <table style="width:100%; border-collapse:separate; border-spacing:0; font-family:Arial, Helvetica, sans-serif;">
                <thead>
                  <tr style="background:#022b5b; color:#fff;">
                    <th style="padding:12px 18px;font-size:10px;letter-spacing:3px;font-weight:normal;">
                      PACKAGE
                    </th>

                    <th style="padding:14px 8px;font-size:10px;letter-spacing:3px;text-align:center;">
                      ADULT
                    </th>

                    <th style="padding:14px 8px;font-size:10px;letter-spacing:3px;text-align:center;">
                      CWB
                    </th>

                    <th style="padding:14px 8px;font-size:10px;letter-spacing:3px;text-align:center;">
                      CWOB
                    </th>

                    <th style="padding:14px 8px;font-size:10px;letter-spacing:3px;text-align:center;">
                      INFANT
                    </th>

                    <th style="padding:14px 8px;font-size:10px;letter-spacing:3px;text-align:center;">
                      TAX
                    </th>

                    <th style="padding:14px 8px;font-size:10px;letter-spacing:3px;text-align:center;">
                      TCS
                    </th>

                    <th style="padding:14px 8px;font-size:10px;letter-spacing:3px;text-align:center;">
                      VISA
                    </th>

                    <th style="padding:14px 8px;font-size:10px;letter-spacing:3px;text-align:center;">
                      GUIDE
                    </th>

                    <th style="padding:14px 8px;font-size:10px;letter-spacing:3px;text-align:center;">
                      MISC
                    </th>

                  </tr>
                </thead>

                <tbody>
                  <?php foreach ($o1_pp as $i => $pp) { ?>

                    <?php
                    $row_bg = ($i % 2 == 0) ? '#ffffff' : '#f6efdf';

                    $tax_amount = 'INR 0.00';

                    if (!empty($pp['tax_display'])) {
                      preg_match('/INR\s*([\d,\.]+)/i', $pp['tax_display'], $m);
                      if (!empty($m[1])) {
                        $tax_amount = 'INR ' . $m[1];
                      }
                    }
                    ?>

                    <tr style="background:<?= $row_bg ?>;">

                      <td style="padding:32px 18px;font-size:16px;color:#0b2343;font-family:Arial, Helvetica, sans-serif;">
                        <?= o1e($pp['package_type']) ?>
                      </td>

                      <td style="padding:32px 8px;text-align:center;font-size:13px;font-family:Arial, Helvetica, sans-serif;">
                        &#8377; <?= o1e($pp['pp_adult_display']) ?>
                      </td>

                      <td style="padding:32px 8px;text-align:center;font-size:13px;font-family:Arial, Helvetica, sans-serif;">
                        &#8377; <?= o1e($pp['pp_cwb_display']) ?>
                      </td>

                      <td style="padding:32px 8px;text-align:center;font-size:13px;font-family:Arial, Helvetica, sans-serif;">
                        &#8377; <?= o1e($pp['pp_cwnb_display']) ?>
                      </td>

                      <td style="padding:32px 8px;text-align:center;font-size:13px;font-family:Arial, Helvetica, sans-serif;">
                        &#8377; <?= o1e($pp['pp_infant_display']) ?>
                      </td>

                      <td style="padding:32px 8px;text-align:center;font-size:13px;font-family:Arial, Helvetica, sans-serif;">
                        &#8377; <?= o1e($tax_amount) ?>
                      </td>

                      <td style="padding:32px 8px;text-align:center;font-size:13px;font-family:Arial, Helvetica, sans-serif;">
                        &#8377; <?= o1e($pp['tcs_display']) ?>
                      </td>

                      <td style="padding:32px 8px;text-align:center;font-size:13px;font-family:Arial, Helvetica, sans-serif;">
                        &#8377; <?= o1e($pp['visa_display']) ?>
                      </td>

                      <td style="padding:32px 8px;text-align:center;font-size:13px;font-family:Arial, Helvetica, sans-serif;">
                        &#8377; <?= o1e($pp['guide_display']) ?>
                      </td>

                      <td style="padding:32px 8px;text-align:center;font-size:13px;font-family:Arial, Helvetica, sans-serif;">
                        &#8377; <?= o1e($pp['misc_display']) ?>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            <?php } ?>
            </div>
          </div>
          <div class="px-20">
          <div class="text-[10px] text-[color:var(--ink)]/55 mt-2 italic">* Prices indicative and subject to availability at the time of booking confirmation.</div>
          </div>
         
    </section>
    <section class="page print-section" style="--wm-url:url(assets/globe-watermark.png)">
      <div class="watermark" style="background-image:url(assets/globe-watermark.png)">
      </div>
      
      <div class="relative px-20 py-8">
        <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Secure Booking</div>
        <h2 class="font-display text-4xl text-[color:var(--navy)] mt-1">Payment Information</h2>
        <hr class="gold-rule mt-3" />
        <div class="grid grid-cols-5 gap-4 mt-6">
          <div class="col-span-3 rounded-2xl p-6 text-cream relative overflow-hidden" style="background:var(--gradient-navy);box-shadow:var(--shadow-card)">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-credit-card w-5 h-5 text-[color:var(--gold)]" aria-hidden="true">
                  <rect width="20" height="14" x="2" y="5" rx="2">
                  </rect>
                  <line x1="2" x2="22" y1="10" y2="10">
                  </line>
                </svg>
                <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Bank Transfer</div>
              </div>
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 lucide-building-2 w-5 h-5 text-cream/60" aria-hidden="true">
                <path d="M10 12h4">
                </path>
                <path d="M10 8h4">
                </path>
                <path d="M14 21v-3a2 2 0 0 0-4 0v3">
                </path>
                <path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2">
                </path>
                <path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16">
                </path>
              </svg>
            </div>
            <div class="font-display text-xl mt-4"><?= o1e(o1nv($bank['bank_name'], 'Bank Account')) ?><?php if (!empty($bank['account_type'])): ?> &middot; <?= o1e($bank['account_type']) ?><?php endif; ?></div>
            <div class="grid grid-cols-2 gap-4 mt-5">
              <div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-cream/60">Account Name</div>
                <div class="font-display text-base"><?= o1e(o1nv($bank['account_name'], 'NA')) ?></div>
              </div>
              <div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-cream/60">Account Number</div>
                <div class="font-display text-base tracking-widest"><?= o1e(o1nv($bank['account_no'], 'NA')) ?></div>
              </div>
              <div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-cream/60">Branch</div>
                <div class="font-display text-base"><?= o1e(o1nv($bank['branch_name'], 'NA')) ?></div>
              </div>
              <div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-cream/60">IFSC Code</div>
                <div class="font-display text-base"><?= o1e(o1nv($bank['ifsc_code'], o1nv($bank['swift_code'], 'NA'))) ?></div>
              </div>
              <?php if (!empty($bank['upi_id'])): ?>
                <div class="col-span-2">
                  <div class="text-[10px] uppercase tracking-[0.22em] text-cream/60">UPI ID</div>
                  <div class="font-display text-base text-[color:var(--gold)]"><?= o1e($bank['upi_id']) ?></div>
                </div>
              <?php endif; ?>
            </div>
            <div class="absolute -right-10 -bottom-10 w-44 h-44 rounded-full" style="background:oklch(0.78 0.13 78 / 0.18)">
            </div>
          </div>
          <div class="col-span-2 rounded-2xl bg-white p-5 border border-[color:var(--gold)]/25 flex flex-col items-center justify-center text-center" style="box-shadow:var(--shadow-card)">
            <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Scan to Pay</div>
            <div class="mt-3 p-3 rounded-xl border-2 border-[color:var(--gold)]/40 bg-white">
              <div class="w-36 h-36 grid place-items-center" style="background:white">
                <?php if (!empty($bank['qr_html'])): ?>
                  <?= $bank['qr_html'] ?>
                <?php elseif (!empty($bank['branch_qr_url']) || !empty($bank['qr_code'])): ?>
                  <img src="<?= o1e(o1nv($bank['branch_qr_url'], $bank['qr_code'])) ?>" alt="Payment QR" class="w-32 h-32 object-contain" />
                <?php else: ?>
                  <svg viewBox="0 0 21 21" class="w-32 h-32">
                    <rect x="0" y="0" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="2" y="0" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="3" y="0" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="4" y="0" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="6" y="0" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="7" y="0" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="20" y="0" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="2" y="1" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="6" y="1" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="9" y="1" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="11" y="1" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="12" y="1" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="14" y="1" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="15" y="1" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="16" y="1" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="18" y="1" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="19" y="1" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="11" y="2" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="14" y="2" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="18" y="2" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="0" y="3" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="2" y="3" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="3" y="3" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="5" y="3" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="6" y="3" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="7" y="3" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="9" y="3" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="10" y="3" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="2" y="4" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="5" y="4" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="9" y="4" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="12" y="4" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="14" y="4" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="15" y="4" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="17" y="4" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="18" y="4" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="19" y="4" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="0" y="5" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="1" y="5" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="14" y="5" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="17" y="5" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="0" y="6" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="3" y="6" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="5" y="6" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="6" y="6" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="8" y="6" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="9" y="6" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="10" y="6" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="12" y="6" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="13" y="6" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="5" y="7" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="8" y="7" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="12" y="7" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="15" y="7" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="17" y="7" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="18" y="7" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="20" y="7" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="0" y="8" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="1" y="8" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="3" y="8" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="4" y="8" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="17" y="8" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="20" y="8" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="3" y="9" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="6" y="9" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="8" y="9" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="9" y="9" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="11" y="9" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="12" y="9" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="13" y="9" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="15" y="9" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="16" y="9" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="8" y="10" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="11" y="10" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="15" y="10" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="18" y="10" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="20" y="10" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="0" y="11" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="2" y="11" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="3" y="11" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="4" y="11" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="6" y="11" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="7" y="11" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="20" y="11" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="2" y="12" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="6" y="12" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="9" y="12" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="11" y="12" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="12" y="12" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="14" y="12" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="15" y="12" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="16" y="12" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="18" y="12" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="19" y="12" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="11" y="13" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="14" y="13" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="18" y="13" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="0" y="14" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="2" y="14" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="3" y="14" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="5" y="14" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="6" y="14" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="7" y="14" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="9" y="14" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="10" y="14" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="2" y="15" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="5" y="15" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="9" y="15" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="12" y="15" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="14" y="15" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="15" y="15" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="17" y="15" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="18" y="15" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="19" y="15" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="0" y="16" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="1" y="16" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="14" y="16" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="17" y="16" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="0" y="17" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="3" y="17" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="5" y="17" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="6" y="17" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="8" y="17" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="9" y="17" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="10" y="17" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="12" y="17" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="13" y="17" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="5" y="18" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="8" y="18" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="12" y="18" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="15" y="18" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="17" y="18" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="18" y="18" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="20" y="18" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="0" y="19" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="1" y="19" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="3" y="19" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="4" y="19" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="17" y="19" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="20" y="19" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="3" y="20" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="6" y="20" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="8" y="20" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="9" y="20" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="11" y="20" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="12" y="20" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="13" y="20" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="15" y="20" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <rect x="16" y="20" width="1" height="1" fill="var(--navy)">
                    </rect>
                    <g>
                      <rect x="0" y="0" width="7" height="7" fill="var(--navy)">
                      </rect>
                      <rect x="1" y="1" width="5" height="5" fill="white">
                      </rect>
                      <rect x="2" y="2" width="3" height="3" fill="var(--navy)">
                      </rect>
                    </g>
                    <g>
                      <rect x="14" y="0" width="7" height="7" fill="var(--navy)">
                      </rect>
                      <rect x="15" y="1" width="5" height="5" fill="white">
                      </rect>
                      <rect x="16" y="2" width="3" height="3" fill="var(--navy)">
                      </rect>
                    </g>
                    <g>
                      <rect x="0" y="14" width="7" height="7" fill="var(--navy)">
                      </rect>
                      <rect x="1" y="15" width="5" height="5" fill="white">
                      </rect>
                      <rect x="2" y="16" width="3" height="3" fill="var(--navy)">
                      </rect>
                    </g>
                  </svg>
                <?php endif; ?>
              </div>
            </div>
            <div class="font-display text-lg text-[color:var(--navy)] mt-3">UPI · GPay · PhonePe</div>
            <div class="text-[11px] text-[color:var(--ink)]/60 font-serif-soft italic"><?= o1e(o1nv($bank['upi_id'], '')) ?></div>
          </div>
        </div>
        <div class="payment-policy-row grid grid-cols-2 gap-4 mt-5">
          <div class="rounded-xl bg-white p-5 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
            <div class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check w-4 h-4 text-[color:var(--teal)]" aria-hidden="true">
                <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                </path>
                <path d="m9 12 2 2 4-4">
                </path>
              </svg>
              <h3 class="font-display text-lg text-[color:var(--navy)]">Payment Instructions</h3>
            </div>
            <ul class="mt-3 space-y-1.5 text-[12px] text-[color:var(--ink)]/80">
              <li>&bull; 25% advance to confirm the booking.</li>
              <li>&bull; 50% payable 30 days prior to travel.</li>
              <li>&bull; Balance 25% to be cleared 15 days prior.</li>
              <li>&bull; Share the payment receipt at the email below.</li>
            </ul>
          </div>
          <div class="rounded-xl bg-white p-5 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
            <div class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-4 h-4 text-[color:var(--teal)]" aria-hidden="true">
                <path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z">
                </path>
                <path d="M14 2v5a1 1 0 0 0 1 1h5">
                </path>
                <path d="M10 9H8">
                </path>
                <path d="M16 13H8">
                </path>
                <path d="M16 17H8">
                </path>
              </svg>
              <h3 class="font-display text-lg text-[color:var(--navy)]">Booking Policy</h3>
            </div>
            <ul class="mt-3 space-y-1.5 text-[12px] text-[color:var(--ink)]/80">
              <li>&bull; Quotation valid for 7 days from issue date.</li>
              <li>&bull; Rates subject to currency &amp; availability.</li>
              <li>&bull; Passport must be valid for 6+ months.</li>
              <li>&bull; Visa fee subject to consulate revision.</li>
            </ul>
          </div>
        </div>
      </div>
  
    </section>
    <section class="page  print-section" style="--wm-url:url(assets/globe-watermark.png)">
      <div class="watermark" style="background-image:url(assets/globe-watermark.png)">
      </div>
      
      <div class="relative px-20 py-8">
        <?php
        // echo '<pre>';
        // print_r($testimonials);
        // exit;
        ?>
        <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Trusted by Travellers</div>
        <h2 class="font-display text-4xl text-[color:var(--navy)] mt-1">What Our Travellers Say</h2>
        <hr class="gold-rule mt-3" />
        <div class="mt-8 space-y-5">
          <?php if (!empty($testimonials)): foreach ($testimonials as $t): ?>
              <div class="rounded-2xl bg-white p-5 border border-[color:var(--gold)]/25 grid grid-cols-6 gap-4 items-center" style="box-shadow:var(--shadow-card)">
                <!-- ========================== Dipti -->
                <div class="col-span-1 flex flex-col items-center">
                  <?php
                  $photo = isset($t['photo']) ? trim($t['photo']) : '';

                  if ($photo != '') {
                    $photo = str_replace('\\', '/', $photo);

                    if (strpos($photo, 'http://') !== 0 && strpos($photo, 'https://') !== 0) {
                      $photo = BASE_URL . ltrim($photo, '/');
                    }
                  }
                  ?>

                  <?php if ($photo != '') { ?>
                    <img src="<?= o1e($photo) ?>"
                      alt="<?= o1e($t['name']) ?>"
                      class="w-20 h-20 rounded-full object-cover">
                  <?php } else { ?>
                    <div class="w-20 h-20 rounded-full grid place-items-center text-cream font-display text-2xl" style="background:var(--gradient-navy)">
                      <?= o1e(strtoupper(substr(o1nv(isset($t['name']) ? $t['name'] : 'T', 'T'), 0, 1))) ?>
                    </div>
                  <?php } ?>

                  <div class="font-display text-sm text-[color:var(--navy)] mt-2 text-center">
                    <?= o1e(o1nv($t['name'] ?? '', 'Traveller')) ?>
                  </div>

                  <div class="text-[10px] uppercase tracking-[0.18em] text-[color:var(--teal)]">
                    <?= o1e(o1nv($t['designation'] ?? '', 'Customer')) ?>
                  </div>
                </div>

                <div class="col-span-5 relative">
                  <div class="font-display text-6xl text-[color:var(--gold)]/40 absolute -top-4 -left-1 leading-none">&quot;</div>
                  <p class="font-serif-soft italic text-[15px] text-[color:var(--ink)]/85 pl-6">
                    <?= o1e(o1nv($t['review'] ?? '', '')) ?>
                  </p>
                  <div class="flex gap-0.5 mt-2 pl-6 text-[color:var(--gold)]">&#9733; &#9733; &#9733; &#9733; &#9733;</div>
                </div>
                <!-- =================================== -->
              </div>
            <?php endforeach;
          else: ?>
            <div class="rounded-2xl bg-white p-5 border border-[color:var(--gold)]/25 text-center text-[color:var(--ink)]/60 text-sm" style="box-shadow:var(--shadow-card)">Customer testimonials can be managed from Quotation Builder settings.</div>
          <?php endif; ?>
        </div>
        <div class="mt-8 space-y-5 hidden">
          <div class="rounded-2xl bg-white p-5 border border-[color:var(--gold)]/25 grid grid-cols-6 gap-4 items-center" style="box-shadow:var(--shadow-card)">
            <div class="col-span-1 flex flex-col items-center">
              <div class="relative">
                <img src="assets/client-1.jpg" alt="Anjali Mehta" class="w-20 h-20 rounded-full object-cover ring-4 ring-[color:var(--gold)]/40" loading="lazy" />
                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full grid place-items-center" style="background:var(--gradient-gold)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check w-3.5 h-3.5 text-[color:var(--navy)]" aria-hidden="true">
                    <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z">
                    </path>
                    <path d="m9 12 2 2 4-4">
                    </path>
                  </svg>
                </div>
              </div>
              <div class="font-display text-sm text-[color:var(--navy)] mt-2 text-center">Anjali Mehta</div>
              <div class="text-[10px] uppercase tracking-[0.18em] text-[color:var(--teal)]">Bali · 2025</div>
            </div>
            <div class="col-span-5 relative">
              <div class="font-display text-6xl text-[color:var(--gold)]/40 absolute -top-4 -left-1 leading-none">&quot;</div>
              <p class="font-serif-soft italic text-[15px] text-[color:var(--ink)]/85 pl-6"><?= o1e($o1_company) ?> turned our anniversary into a dream. Every transfer, every meal, every sunset â€” perfectly arranged.</p>
              <div class="flex gap-0.5 mt-2 pl-6 text-[color:var(--gold)]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
              </div>
            </div>
          </div>
          <div class="rounded-2xl bg-white p-5 border border-[color:var(--gold)]/25 grid grid-cols-6 gap-4 items-center" style="box-shadow:var(--shadow-card)">
            <div class="col-span-1 flex flex-col items-center">
              <div class="relative">
                <img src="assets/client-2.jpg" alt="Rohan Kapoor" class="w-20 h-20 rounded-full object-cover ring-4 ring-[color:var(--gold)]/40" loading="lazy" />
                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full grid place-items-center" style="background:var(--gradient-gold)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check w-3.5 h-3.5 text-[color:var(--navy)]" aria-hidden="true">
                    <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z">
                    </path>
                    <path d="m9 12 2 2 4-4">
                    </path>
                  </svg>
                </div>
              </div>
              <div class="font-display text-sm text-[color:var(--navy)] mt-2 text-center">Rohan Kapoor</div>
              <div class="text-[10px] uppercase tracking-[0.18em] text-[color:var(--teal)]">Dubai · 2025</div>
            </div>
            <div class="col-span-5 relative">
              <div class="font-display text-6xl text-[color:var(--gold)]/40 absolute -top-4 -left-1 leading-none">&quot;</div>
              <p class="font-serif-soft italic text-[15px] text-[color:var(--ink)]/85 pl-6">The attention to detail is exceptional. Premium hotels, on-time pickups, and a guide who felt like a friend.</p>
              <div class="flex gap-0.5 mt-2 pl-6 text-[color:var(--gold)]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
              </div>
            </div>
          </div>
          <div class="rounded-2xl bg-white p-5 border border-[color:var(--gold)]/25 grid grid-cols-6 gap-4 items-center" style="box-shadow:var(--shadow-card)">
            <div class="col-span-1 flex flex-col items-center">
              <div class="relative">
                <img src="assets/client-3.jpg" alt="Priya &amp; Vikram" class="w-20 h-20 rounded-full object-cover ring-4 ring-[color:var(--gold)]/40" loading="lazy" />
                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full grid place-items-center" style="background:var(--gradient-gold)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check w-3.5 h-3.5 text-[color:var(--navy)]" aria-hidden="true">
                    <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z">
                    </path>
                    <path d="m9 12 2 2 4-4">
                    </path>
                  </svg>
                </div>
              </div>
              <div class="font-display text-sm text-[color:var(--navy)] mt-2 text-center">Priya &amp; Vikram</div>
              <div class="text-[10px] uppercase tracking-[0.18em] text-[color:var(--teal)]">Maldives · 2024</div>
            </div>
            <div class="col-span-5 relative">
              <div class="font-display text-6xl text-[color:var(--gold)]/40 absolute -top-4 -left-1 leading-none">&quot;</div>
              <p class="font-serif-soft italic text-[15px] text-[color:var(--ink)]/85 pl-6">Hands down the best travel experience we&#x27;ve had. Highly recommend their luxury packages.</p>
              <div class="flex gap-0.5 mt-2 pl-6 text-[color:var(--gold)]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-current" aria-hidden="true">
                  <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                  </path>
                </svg>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-8 grid grid-cols-3 gap-3 text-center">
          <div class="rounded-xl p-4 text-cream" style="background:var(--gradient-navy)">
            <div class="font-display text-2xl gold-text">4.9 ★</div>
            <div class="text-[10px] uppercase tracking-[0.25em] text-cream/70 mt-1">Google Rating</div>
          </div>
          <div class="rounded-xl p-4 text-cream" style="background:var(--gradient-navy)">
            <div class="font-display text-2xl gold-text">2,400+</div>
            <div class="text-[10px] uppercase tracking-[0.25em] text-cream/70 mt-1">Verified Reviews</div>
          </div>
          <div class="rounded-xl p-4 text-cream" style="background:var(--gradient-navy)">
            <div class="font-display text-2xl gold-text">18,500+</div>
            <div class="text-[10px] uppercase tracking-[0.25em] text-cream/70 mt-1">Happy Travellers</div>
          </div>
        </div>
      </div>
   
      
    </section>
    <section class="page print-section" style="--wm-url:url(assets/globe-watermark.png)">
      <div class="watermark" style="background-image:url(assets/globe-watermark.png)">
      </div>
      
      <div class="relative px-20 py-8">
        <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">The Fine Print</div>
        <h2 class="font-display text-4xl text-[color:var(--navy)] mt-1"><?= o1e(o1nv(isset($terms['title']) ? $terms['title'] : '', 'Terms & Conditions')) ?></h2>
        <hr class="gold-rule mt-3" />

        <!-- ==================================== Dipti -->
        <?php
        $o1_terms_html = trim(isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '');
        $o1_tc_sections = o1_terms_sections($o1_terms_html);
        ?>

        <?php if (!empty($o1_tc_sections)) { ?>
          <!-- <div class="grid grid-cols-2 gap-3 mt-6"> -->
          <div class="space-y-4 mt-6">
            <?php foreach ($o1_tc_sections as $i => $tc) {

              $svg_icons = array(
                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-4 h-4 text-[color:var(--navy)]"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"></path><path d="M14 2v5a1 1 0 0 0 1 1h5"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>',

                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-4 h-4 text-[color:var(--navy)]"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>',

                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-credit-card w-4 h-4 text-[color:var(--navy)]"><rect width="20" height="14" x="2" y="5" rx="2"></rect><line x1="2" x2="22" y1="10" y2="10"></line></svg>',

                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-globe w-4 h-4 text-[color:var(--navy)]"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><path d="M2 12h20"></path></svg>'
              );

              $icon_svg = $svg_icons[$i % count($svg_icons)];
            ?>
              <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                    <?= $icon_svg ?>
                  </div>

                  <h3 class="font-display text-base text-[color:var(--navy)]">
                    <?= o1e($tc['title']) ?>
                  </h3>
                </div>

                <div class="mt-3 text-[13px] text-[color:var(--ink)]/85 leading-relaxed tc-content">
                  <?= $tc['content'] ?>
                </div>
              </div>
            <?php } ?>
          </div>
        <?php } else { ?>
          <div class="rounded-xl bg-white p-5 border border-[color:var(--gold)]/25 mt-6" style="box-shadow:var(--shadow-card)">
            Terms and conditions will be shared as per company policy.
          </div>
        <?php } ?>

      </div>
      <!-- ======================================== -->
      <!-- <div class="rounded-xl bg-white p-5 border border-[color:var(--gold)]/25 mt-6" style="box-shadow:var(--shadow-card)">
          <div class="font-serif-soft text-[12px] text-[color:var(--ink)]/85 leading-relaxed">
            <? //php
            //$o1_terms_html = trim(isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '');
            //echo $o1_terms_html !== '' ? $o1_terms_html : 'Terms and conditions will be shared as per company policy.';
            ?>
          </div>
        </div> -->
      <div class="grid grid-cols-2 gap-3 mt-6 hidden">
        <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                <path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z">
                </path>
                <path d="M14 2v5a1 1 0 0 0 1 1h5">
                </path>
                <path d="M10 9H8">
                </path>
                <path d="M16 13H8">
                </path>
                <path d="M16 17H8">
                </path>
              </svg>
            </div>
            <h3 class="font-display text-base text-[color:var(--navy)]">Booking Policy</h3>
          </div>
          <!-- ================= Dipti -->
          <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">
            <? //= o1e($tc['content']) 
            ?>
          </p>
          <!-- ==================== -->
          <!-- <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">25% advance required to confirm the booking. The balance must be cleared 15 days prior to the travel date.</p> -->
        </div>
        <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                <path d="M18 6 6 18">
                </path>
                <path d="m6 6 12 12">
                </path>
              </svg>
            </div>
            <h3 class="font-display text-base text-[color:var(--navy)]">Cancellation Policy</h3>
          </div>
          <!-- ================= Dipti -->
          <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">
            <? //= o1e($tc['content']) 
            ?>
          </p>
          <!-- ==================== -->
          <!-- <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">30+ days: 25% retention. 15â€“29 days: 50%. 7â€“14 days: 75%. Under 7 days: 100% retention.</p> -->
        </div>
        <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-credit-card w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                <rect width="20" height="14" x="2" y="5" rx="2">
                </rect>
                <line x1="2" x2="22" y1="10" y2="10">
                </line>
              </svg>
            </div>
            <h3 class="font-display text-base text-[color:var(--navy)]">Refund Policy</h3>
          </div>
          <!-- ================= Dipti -->
          <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">
            <? //= o1e($tc['content']) 
            ?>
          </p>
          <!-- ==================== -->
          <!-- <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">Refunds will be processed within 10â€“15 working days from the date of cancellation approval.</p> -->
        </div>
        <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-globe w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                <circle cx="12" cy="12" r="10">
                </circle>
                <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20">
                </path>
                <path d="M2 12h20">
                </path>
              </svg>
            </div>
            <h3 class="font-display text-base text-[color:var(--navy)]">Visa Disclaimer</h3>
          </div>
          <!-- ================= Dipti -->
          <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">
            <? //= o1e($tc['content']) 
            ?>
          </p>
          <!-- ==================== -->
          <!-- <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">Visa approval is at the sole discretion of the embassy. Fees once paid are non-refundable in any case.</p> -->
        </div>
        <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hotel w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                <path d="M10 22v-6.57">
                </path>
                <path d="M12 11h.01">
                </path>
                <path d="M12 7h.01">
                </path>
                <path d="M14 15.43V22">
                </path>
                <path d="M15 16a5 5 0 0 0-6 0">
                </path>
                <path d="M16 11h.01">
                </path>
                <path d="M16 7h.01">
                </path>
                <path d="M8 11h.01">
                </path>
                <path d="M8 7h.01">
                </path>
                <rect x="4" y="2" width="16" height="20" rx="2">
                </rect>
              </svg>
            </div>
            <h3 class="font-display text-base text-[color:var(--navy)]">Hotel Policies</h3>
          </div>
          <!-- ================= Dipti -->
          <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">
            <? //= o1e($tc['content']) 
            ?>
          </p>
          <!-- ==================== -->
          <!-- <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">Standard check-in 14:00, check-out 12:00. Early check-in or late check-out is subject to availability.</p> -->
        </div>
        <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plane w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z">
                </path>
              </svg>
            </div>
            <h3 class="font-display text-base text-[color:var(--navy)]">Flight Policies</h3>
          </div>
          <!-- ================= Dipti -->
          <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">
            <? //= o1e($tc['content']) 
            ?>
          </p>
          <!-- ==================== -->
          <!-- <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">Flight schedules and fares are subject to change by the airline. Re-booking charges are payable as applicable.</p> -->
        </div>
        <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                </path>
                <path d="m9 12 2 2 4-4">
                </path>
              </svg>
            </div>
            <h3 class="font-display text-base text-[color:var(--navy)]">Force Majeure</h3>
          </div>
          <!-- ================= Dipti -->
          <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">
            <? //= o1e($tc['content']) 
            ?>
          </p>
          <!-- ==================== -->
          <!-- <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">We shall not be liable for changes or cancellations due to natural calamities, political unrest, or pandemics.</p> -->
        </div>
        <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                <path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526">
                </path>
                <circle cx="12" cy="8" r="6">
                </circle>
              </svg>
            </div>
            <h3 class="font-display text-base text-[color:var(--navy)]">Travel Insurance</h3>
          </div>
          <!-- ================= Dipti -->
          <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">
            <? //= o1e($tc['content']) 
            ?>
          </p>
          <!-- ==================== -->
          <!-- <p class="mt-2 text-[11.5px] text-[color:var(--ink)]/80 leading-relaxed">Travel insurance is strongly recommended and not included in the package unless specified explicitly.</p> -->
        </div>
      </div>
      <div class="px-20">
      <div class="terms-spacing mt-2 rounded-xl p-5 text-cream relative overflow-hidden" style="background:var(--gradient-navy)">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles w-5 h-5 text-[color:var(--gold)]" aria-hidden="true">
          <path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z">
          </path>
          <path d="M20 2v4">
          </path>
          <path d="M22 4h-4">
          </path>
          <circle cx="4" cy="20" r="2">
          </circle>
        </svg>
        <p class="mt-2 font-serif-soft italic text-[13px] text-cream/90 max-w-3xl">By confirming this booking, the guest acknowledges that they have read, understood and accepted the terms and conditions above. <?= o1e($o1_company) ?> reserves the right to amend these terms with prior notice.</p>
      </div>
      </div>
      </div>
    
    </section>
    <section class="page relative thankyou-page" style="--wm-url:url(assets/globe-watermark.png)">
      <div class="watermark" style="background-image:url(assets/globe-watermark.png)">
      </div>
      
      <div class="relative px-20 pt-10 pb-20">
        <div class="text-center">
          <div class="flex items-center gap-2.5">
            <div class="relative w-10 h-10 rounded-full grid place-items-center" style="background:var(--gradient-gold)">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-compass w-5 h-5 text-[color:var(--navy)]" aria-hidden="true">
                <circle cx="12" cy="12" r="10">
                </circle>
                <path d="m16.24 7.76-1.804 5.411a2 2 0 0 1-1.265 1.265L7.76 16.24l1.804-5.411a2 2 0 0 1 1.265-1.265z">
                </path>
              </svg>
            </div>
            <div class="leading-tight text-[color:var(--navy)]">
              <div class="font-display text-lg font-bold tracking-wide"><?= o1e($o1_company) ?></div>
              <div class="text-[9px] uppercase tracking-[0.25em] opacity-80">Luxury Voyages</div>
            </div>
          </div>
        </div>
        <div class="text-center mt-10">
          <div class="text-[11px] uppercase tracking-[0.4em] text-[color:var(--gold)]">With Gratitude</div>
          <h1 class="font-display text-7xl text-[color:var(--navy)] mt-2 leading-none">Thank You</h1>
          <div class="divider-fancy mt-4 max-w-xs mx-auto">✦</div>
          <p class="font-serif-soft italic text-xl text-[color:var(--ink)]/75 mt-4 max-w-lg mx-auto">We look forward to creating unforgettable travel memories for you and your family.</p>
        </div>
        <div class="grid grid-cols-3 gap-3 mt-10">
          <div class="rounded-xl p-4 text-cream" style="background:var(--gradient-navy)">
            <div class="font-display text-2xl gold-text">4.9 ★</div>
            <div class="text-[10px] uppercase tracking-[0.25em] text-cream/70 mt-1">Google Rating</div>
          </div>
          <div class="rounded-xl p-4 text-cream" style="background:var(--gradient-navy)">
            <div class="font-display text-2xl gold-text">2,400+</div>
            <div class="text-[10px] uppercase tracking-[0.25em] text-cream/70 mt-1">Verified Reviews</div>
          </div>
          <div class="rounded-xl p-4 text-cream" style="background:var(--gradient-navy)">
            <div class="font-display text-2xl gold-text">18,500+</div>
            <div class="text-[10px] uppercase tracking-[0.25em] text-cream/70 mt-1">Happy Travellers</div>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-7">
          <div class="rounded-xl bg-white p-5 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
            <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Office Address</div>
            <h3 class="font-display text-xl text-[color:var(--navy)] mt-1"><?= o1e($o1_company) ?></h3>
            <p class="text-[12px] text-[color:var(--ink)]/80 mt-2 leading-relaxed"><?= nl2br(o1e(o1nv($ty['company_address'], ''))) ?></p>
            <div class="mt-3 space-y-1.5 text-[12px] text-[color:var(--ink)]/85">
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone w-3.5 h-3.5 text-[color:var(--teal)]" aria-hidden="true">
                  <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384">
                  </path>
                </svg> <? //= o1e(o1nv($ty['company_contact'], '')) 
                        ?>
                <a href="tel:<?= o1e(o1nv($ty['company_contact'], '')) ?>">
                  <?= o1e(o1nv($ty['company_contact'], '')) ?>
                </a>
              </div>
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail w-3.5 h-3.5 text-[color:var(--teal)]" aria-hidden="true">
                  <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7">
                  </path>
                  <rect x="2" y="4" width="20" height="16" rx="2">
                  </rect>
                </svg> <a href="mailto:<?= o1e(o1nv($ty['company_email'], '')) ?>">
                  <?= o1e(o1nv($ty['company_email'], '')) ?>
                </a>
              </div>
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-globe w-3.5 h-3.5 text-[color:var(--teal)]" aria-hidden="true">
                  <circle cx="12" cy="12" r="10">
                  </circle>
                  <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20">
                  </path>
                  <path d="M2 12h20">
                  </path>
                </svg> <?= o1e(o1nv($ty['website'], '')) ?>
              </div>
            </div>

            <!-- =========================== Dipti -->
            <div class="flex gap-2 mt-4">

              <?php if (!empty($social_links['instagram'])) { ?>
                <a href="<?= o1e($social_links['instagram']) ?>" style="text-decoration:none; display:inline-block;">
                  <span class="w-8 h-8 rounded-full grid place-items-center border border-[color:var(--gold)]/40 text-[color:var(--navy)]">

                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram w-3.5 h-3.5" aria-hidden="true">
                      <rect width="20" height="20" x="2" y="2" rx="5" ry="5">
                      </rect>
                      <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z">
                      </path>
                      <line x1="17.5" x2="17.51" y1="6.5" y2="6.5">
                      </line>
                    </svg>
                  </span>
                </a>
              <?php } ?>

              <?php if (!empty($social_links['facebook'])) { ?>
                <a href="<?= o1e($social_links['facebook']) ?>" style="text-decoration:none; display:inline-block;">
                  <span class="w-8 h-8 rounded-full grid place-items-center border border-[color:var(--gold)]/40 text-[color:var(--navy)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-facebook w-3.5 h-3.5" aria-hidden="true">
                      <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z">
                      </path>
                    </svg>
                  </span>
                </a>
              <?php } ?>

              <?php if (!empty($social_links['linkedin'])) { ?>
                <a href="<?= o1e($social_links['linkedin']) ?>" style="text-decoration:none; display:inline-block;">
                  <span class="w-8 h-8 rounded-full grid place-items-center border border-[color:var(--gold)]/40 text-[color:var(--navy)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-linkedin w-3.5 h-3.5" aria-hidden="true">
                      <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z">
                      </path>
                      <rect width="4" height="12" x="2" y="9">
                      </rect>
                      <circle cx="4" cy="4" r="2">
                      </circle>
                    </svg>
                  </span>
                </a>
              <?php } ?>

              <?php if (!empty($social_links['youtube'])) { ?>
                <a href="<?= o1e($social_links['youtube']) ?>" style="text-decoration:none; display:inline-block;">
                  <span class="w-8 h-8 rounded-full grid place-items-center border border-[color:var(--gold)]/40 text-[color:var(--navy)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-youtube w-3.5 h-3.5" aria-hidden="true">
                      <path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17">
                      </path>
                      <path d="m10 15 5-3-5-3z">
                      </path>
                    </svg>
                  </span>
                </a>
              <?php } ?>

            </div>
            <!-- ============================= -->
          </div>
          <div class="rounded-xl p-5 text-cream relative overflow-hidden" style="background:var(--gradient-navy);box-shadow:var(--shadow-card)">
            <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Prepared By</div>
            <div class="flex items-center gap-3 mt-3">
              <div class="w-14 h-14 rounded-full grid place-items-center font-display text-2xl text-[color:var(--navy)]" style="background:var(--gradient-gold)"><?= o1e(strtoupper(substr(o1nv($ty['prepared_by'], 'A'), 0, 2))) ?></div>
              <div>
                <div class="font-display text-2xl"><?= o1e(o1nv($ty['prepared_by'], 'Admin')) ?></div>
                <div class="text-[11px] uppercase tracking-[0.22em] text-[color:var(--gold)]">Travel Consultant</div>
              </div>
            </div>
            <div class="mt-4 space-y-1.5 text-[12px] text-cream/85">
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone w-3.5 h-3.5 text-[color:var(--gold)]" aria-hidden="true">
                  <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384">
                  </path>
                </svg>
                <a href="tel:<?= o1e(o1nv($ty['user_mobile'], o1nv($ty['company_contact'], ''))) ?>">
                  <?= o1e(o1nv($ty['user_mobile'], o1nv($ty['company_contact'], ''))) ?>
                </a>
              </div>
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail w-3.5 h-3.5 text-[color:var(--gold)]" aria-hidden="true">
                  <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7">
                  </path>
                  <rect x="2" y="4" width="20" height="16" rx="2">
                  </rect>
                </svg> <a href="mailto:<?= o1e(o1nv($ty['company_email'], '')) ?>">
                  <?= o1e(o1nv($ty['company_email'], '')) ?>
                </a>
              </div>
            </div>
            <p class="mt-5 font-serif-soft italic text-cream/80 text-[12px]">&quot;Travel is the only thing you buy that makes you richer. Let&#x27;s make yours unforgettable.&quot;</p>
            <div class="absolute -right-8 -bottom-8 w-40 h-40 rounded-full" style="background:oklch(0.78 0.13 78 / 0.18)">
            </div>
          </div>
        </div>
        <div class="mt-10 text-center">
          <!-- <div class="font-display text-2xl gold-text inline-block">Bon Voyage</div> -->
          <div class="text-[10px] uppercase tracking-[0.35em] text-[color:var(--navy)]/60 mt-1"><?= o1e($o1_company) ?> Â· Luxury Voyages Â· Est. 2014</div>
        </div>
      </div>
      
    </section>
  </main>
  <?php
  // Preview links (single_quotation.php) pass preview=1 to show the page only.
  // PDF download uses fit_quotation_html.php in a hidden iframe and still auto-prints.
  $o1_auto_print = empty($_GET['preview']) || $_GET['preview'] !== '1';
  if ($o1_auto_print) :
  ?>
  <script type="text/javascript">
    // ---------------------------------------------------------------------------
    // Auto-print trigger (skipped when preview=1).
    // Images sometimes appeared and sometimes did not because printing fired
    // before every image / web font had finished decoding. To make it reliable we
    // explicitly wait for: (1) all <img> to finish (load OR error), and
    // (2) document.fonts.ready -- then print. A hard safety timeout guarantees the
    // dialog always opens even if one asset stalls.
    // ---------------------------------------------------------------------------
    (function() {
      var printed = false;

      function doPrint() {
        if (printed) {
          return;
        }
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
        if (pending.length === 0) {
          return Promise.resolve();
        }
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
        // Hard cap: never wait longer than 4s for stragglers.
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
  <?php endif; ?>
</body>

</html>