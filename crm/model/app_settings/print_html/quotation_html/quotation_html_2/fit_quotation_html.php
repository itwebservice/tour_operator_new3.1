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
$vehs    = $q['vehicles'];
$itin    = $q['itinerary'];
$incx    = $q['inclusion_exclusion'];
$cost    = $q['costing'];
$bank    = $q['bank_details'];
$terms   = $q['terms_conditions'];
$ty      = $q['thank_you'];
$assets  = "assets/";

$testimonials = array();
$o2_cfg = function_exists('gqb_get_config') ? gqb_get_config() : array();
if (!empty($o2_cfg['testimonials']) && is_array($o2_cfg['testimonials'])) {
  $testimonials = $o2_cfg['testimonials'];
}

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
if (!function_exists('o2_list_items')) {
  function o2_list_items($html, $fallback)
  {
    $text = trim(strip_tags((string) $html));
    $items = preg_split('/\r\n|\r|\n|•|\x{2022}/u', $text);
    $items = array_values(array_filter(array_map('trim', (array) $items)));
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
$o2_pkg           = o2nv($ov['package_type_label'], o2nv(!empty($hotels[0]['package_type']) ? $hotels[0]['package_type'] : '', 'Package'));
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
$o2_itin_pages  = !empty($o2_itin_list) ? array_chunk($o2_itin_list, 3) : array();
$o2_total_pages = 4 + count($o2_itin_pages) + 5;

$o2_banner = o2img(isset($hero['cover_image']) ? $hero['cover_image'] : '', $assets . 'banner.jpg');
$o2_hero   = o2img(isset($hero['cover_image']) ? $hero['cover_image'] : '', $assets . 'hero.jpg');
$o2_logo   = o2img(isset($hero['company_logo']) ? $hero['company_logo'] : '', $assets . 'logo.png');
$o2_round  = o2img((!empty($o2_itin_list[0]['image']) ? $o2_itin_list[0]['image'] : ''), $assets . 'day.jpg');
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
          <div>
            <div class="logo__name"><?= o2e($o2_company) ?></div>
            <div class="logo__tag"><?= o2e($o2_tagline) ?></div>
          </div>
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
          <div class="incitem"><span class="ico"><i class="fa-solid fa-plane"></i></span><span>International &amp;<br>Internal Flights</span></div>
          <div class="incitem"><span class="ico"><i class="fa-solid fa-hotel"></i></span><span>Premium<br>Hotels</span></div>
          <div class="incitem"><span class="ico"><i class="fa-solid fa-utensils"></i></span><span>Daily<br>Breakfast</span></div>
          <div class="incitem"><span class="ico"><i class="fa-solid fa-compass"></i></span><span>Sightseeing With<br>Tour Guide</span></div>
          <div class="incitem"><span class="ico"><i class="fa-solid fa-car"></i></span><span>Private<br>Transfers</span></div>
          <div class="incitem"><span class="ico"><i class="fa-solid fa-passport"></i></span><span>Visa &amp;<br>Insurance</span></div>
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
        <h2>Exclusively designed for <em><?= o2e($o2_client) ?></em> — an unforgettable journey through <?= o2e($o2_dest) ?>.</h2>
      </div>
      <p class="greet" style="margin:7mm 0 0">
        Dear <b><?= o2e($o2_client_first !== '' ? $o2_client_first : $o2_client) ?></b>,<br>
        Thank you for choosing <?= o2e($o2_company) ?> for your upcoming journey. We are delighted to present this carefully
        crafted travel proposal, designed to deliver memorable experiences, seamless arrangements and exceptional
        hospitality at every step of your trip.
      </p>
      <div style="margin-top:7mm">
        <p class="kicker">Tour Overview</p>
        <div class="ov-grid" style="margin-top:11px">
          <div class="card ov"><span class="ic"><i class="fa-solid fa-file-lines"></i></span><div><div class="t">Quotation ID</div><div class="v sm"><?= o2e(o2nv($hero['quotation_code'], '')) ?></div></div></div>
          <div class="card ov"><span class="ic"><i class="fa-solid fa-earth-asia"></i></span><div><div class="t">Tour ID</div><div class="v sm"><?= o2e(o2nv($ov['tour_id'], '')) ?></div></div></div>
          <div class="card ov"><span class="ic"><i class="fa-solid fa-calendar-day"></i></span><div><div class="t">Quotation Date</div><div class="v sm"><?= o2e(o2nv($ov['quotation_date'], '')) ?></div></div></div>
          <div class="card ov"><span class="ic"><i class="fa-solid fa-plane"></i></span><div><div class="t">Travel Dates</div><div class="v sm"><?= o2e($o2_travel_dates) ?></div></div></div>
          <div class="card ov"><span class="ic"><i class="fa-solid fa-moon"></i></span><div><div class="t">Duration</div><div class="v sm"><?= o2e(o2nv($ov['duration_label'], o2nv($hero['duration_label'], ''))) ?></div></div></div>
          <div class="card ov"><span class="ic"><i class="fa-solid fa-users"></i></span><div><div class="t">Guests</div><div class="v sm"><?= o2e($o2_guests) ?></div></div></div>
        </div>
      </div>
      <div style="margin-top:7mm">
        <p class="kicker">Prepared For</p>
        <div class="prep" style="margin-top:11px">
          <div class="card prep__card">
            <img class="prep__photo" src="<?= o2e($assets . 'person.jpg') ?>" alt="Client">
            <div>
              <div class="nm"><?= o2e($o2_client) ?></div>
              <div class="meta">
                <?php if (o2nv($ov['customer_email'], '') !== ''): ?><span><i class="fa-solid fa-envelope"></i> <?= o2e($ov['customer_email']) ?></span><?php endif; ?>
                <?php if (o2nv($ov['customer_mobile'], '') !== ''): ?><span><i class="fa-solid fa-phone"></i> <?= o2e($ov['customer_mobile']) ?></span><?php endif; ?>
              </div>
            </div>
          </div>
          <div class="card prep__card" style="flex:.85">
            <span class="ic ic--navy" style="width:46px;height:46px"><i class="fa-solid fa-star" style="font-size:20px"></i></span>
            <div>
              <div class="t" style="font-size:8px;letter-spacing:.16em;text-transform:uppercase;color:var(--muted)">Package Type</div>
              <div class="nm" style="font-size:16px;margin-top:2px"><?= o2e($o2_pkg) ?></div>
              <div class="meta" style="margin-top:3px"><span style="color:var(--gold-deep);font-weight:500">Recommended for your group</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php o2_foot(); ?>
  </section>

  <!-- PAGE 3 · HOTELS -->
  <section class="page<?= count((array) $hotels) > 4 ? ' page-flow' : '' ?>">
    <?php o2_strip('Where You\'ll Stay', 'Accommodation', o2e($o2_pkg) . '&nbsp; <b>Package</b>'); ?>
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
          $o2_himg = o2img(isset($h['hotel_photo']) ? $h['hotel_photo'] : '', $assets . 'hotel-' . ((($o2_hi - 1) % 3) + 1) . '.jpg');
        ?>
        <div class="card hotel">
          <img class="hotel__img" src="<?= o2e($o2_himg) ?>" alt="Hotel">
          <div class="hotel__b">
            <div class="hotel__city"><i class="fa-solid fa-location-dot"></i> <?= o2e(o2nv($h['hotel_city'], '')) ?></div>
            <div class="hotel__name"><?= o2e(o2nv($h['hotel_name'], 'Hotel')) ?></div>
            <div class="hotel__cat"><?= o2e(o2nv($h['room_category'], o2nv($h['room_type'], o2nv($h['meal_plan'], '')))) ?></div>
            <div class="hotel__stars"><?= o2_stars(isset($h['rating']) ? $h['rating'] : 5) ?></div>
            <div class="hotel__dates">
              <div class="hotel__dt"><div class="l">Check-In</div><div class="d"><?= o2e(o2nv($h['check_in'], 'NA')) ?></div></div>
              <div class="hotel__dt"><div class="l">Check-Out</div><div class="d"><?= o2e(o2nv($h['check_out'], 'NA')) ?></div></div>
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
          <div class="amen" style="border:0;padding:0;gap:9px 12px">
            <span><i class="fa-solid fa-wifi"></i> Free Wi-Fi</span>
            <span><i class="fa-solid fa-mug-saucer"></i> Daily Breakfast</span>
            <span><i class="fa-solid fa-person-swimming"></i> Swimming Pool</span>
            <span><i class="fa-solid fa-snowflake"></i> Air Conditioning</span>
          </div>
          <p class="muted" style="font-size:9px;margin:10px 0 0;line-height:1.5">All stays are on twin-sharing basis with daily breakfast unless stated otherwise. Room categories may be upgraded subject to availability.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php o2_foot(); ?>
  </section>

  <!-- PAGE 4 · FLIGHTS & TRANSPORT -->
  <section class="page page-flow">
    <?php o2_strip('Getting There &amp; Around', 'Journey Plan', 'Flights · <b>Transfers</b>'); ?>
    <div class="page__wm"></div>
    <div class="page__body">
      <p class="kicker">A · Flight Details</p>
      <div style="display:flex;flex-direction:column;gap:11px;margin-top:12px">
        <?php if (!empty($flights)): $o2_fi = 0; foreach ((array) $flights as $f): ?>
        <div class="boarding">
          <div class="boarding__main">
            <div class="boarding__top">
              <div class="airline">
                <img class="airline__logo" src="<?= o2e(o2img(isset($f['airline_logo']) ? $f['airline_logo'] : '', $assets . 'airline.png')) ?>" alt="Airline">
                <div><div class="airline__name"><?= o2e(o2nv($f['airline_display'], o2nv($f['airline_name'], 'Flight'))) ?></div><div class="airline__cls"><?= o2e(o2nv($f['class'], '')) ?></div></div>
              </div>
              <div class="boarding__pnr"><?= o2e(o2_flight_label($o2_fi)) ?></div>
            </div>
            <div class="sector">
              <div class="pt"><div class="code"><?= o2e(o2_air_code(isset($f['from_city']) ? $f['from_city'] : '')) ?></div><div class="city"><?= o2e(o2nv($f['from_city'], '')) ?></div></div>
              <div class="mid"><i class="fa-solid fa-plane"></i><div class="line"></div><div class="dur">As per schedule</div></div>
              <div class="pt"><div class="code"><?= o2e(o2_air_code(isset($f['to_city']) ? $f['to_city'] : '')) ?></div><div class="city"><?= o2e(o2nv($f['to_city'], '')) ?></div></div>
            </div>
            <div class="boarding__times">
              <div class="x"><div class="l">Departure</div><div class="v"><?= o2e(o2nv($f['departure_datetime'], 'NA')) ?></div></div>
              <div class="x"><div class="l">Arrival</div><div class="v"><?= o2e(o2nv($f['arrival_datetime'], 'NA')) ?></div></div>
              <div class="x"><div class="l">Flight</div><div class="v"><?= o2e(o2nv($f['airline_code'], '—')) ?></div></div>
            </div>
          </div>
          <div class="boarding__stub">
            <div class="bl">Baggage</div><div class="bv">As per airline</div>
            <div class="bl" style="margin-top:5px">Class</div><div class="bv"><?= o2e(o2nv($f['class'], '—')) ?></div>
          </div>
        </div>
        <?php $o2_fi++; endforeach; else: ?>
        <div class="card" style="padding:14px;text-align:center;color:var(--muted)">No flight details available.</div>
        <?php endif; ?>
      </div>
      <?php if (count((array) $flights) > 2): ?>
      <p class="muted" style="font-size:9px;margin:7px 0 0">Additional internal sectors are included as per the itinerary on the respective travel dates.</p>
      <?php endif; ?>

      <p class="kicker" style="margin-top:6mm">B · Transportation</p>
      <div class="tline" style="margin-top:11px">
        <?php if (!empty($vehs)): foreach ((array) $vehs as $v): ?>
        <div class="tnode">
          <div class="card">
            <img class="tveh" src="<?= o2e($assets . 'vehicle.jpg') ?>" alt="Vehicle">
            <div>
              <div class="nm"><?= o2e(o2nv($v['vehicle_name'], 'Transfer')) ?></div>
              <div class="rt"><i class="fa-solid fa-location-dot"></i> <?= o2e(o2nv($v['pickup'], '')) ?><?php if (o2nv($v['drop'], '') !== ''): ?> → <?= o2e($v['drop']) ?><?php endif; ?></div>
            </div>
            <div class="meta"><?= o2e(o2nv($v['date'], '')) ?><?php if (!empty($v['description'])): ?><br><?= o2e($v['description']) ?><?php endif; ?><br><span class="cat"><?= o2e(o2nv($v['vehicle_type'], 'Private Transfer')) ?></span></div>
          </div>
        </div>
        <?php endforeach; else: ?>
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
  <section class="page<?= count($o2_chunk) > 2 ? ' page-flow' : '' ?>">
    <?php o2_strip('Day by Day', 'Itinerary', o2e($o2_day_label)); ?>
    <div class="page__wm"></div>
    <div class="page__body">
      <div class="itin">
        <?php foreach ($o2_chunk as $d):
          $o2_dno   = (int) o2nv(isset($d['day_number']) ? $d['day_number'] : '', 0);
          $o2_dimg  = o2img(isset($d['image']) ? $d['image'] : '', $assets . 'day.jpg');
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
              <div class="ichip ichip--meal"><span class="b"><i class="fa-solid fa-utensils"></i></span><div><div class="l">Meal Plan</div><div class="v"><?= o2e(o2nv($d['meal_plan'], '—')) ?></div></div></div>
              <div class="ichip ichip--stay"><span class="b"><i class="fa-solid fa-moon"></i></span><div><div class="l">Overnight Stay</div><div class="v"><?= o2e(o2nv($d['overnight_stay'], '—')) ?></div></div></div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php o2_foot(); ?>
  </section>
  <?php endforeach; endif; ?>

  <!-- INCLUSIONS / EXCLUSIONS / COSTING -->
  <section class="page page-flow">
    <?php o2_strip('The Fine Detail', 'Inclusions', '&amp; <b>Investment</b>'); ?>
    <div class="page__wm"></div>
    <div class="page__body">
      <div class="ie-grid">
        <div class="card ie ie--in">
          <h3><span class="b"><i class="fa-solid fa-check"></i></span> What's Included</h3>
          <ul>
            <?php foreach (o2_list_items(isset($incx['included']) ? $incx['included'] : '', 'Inclusions will be shared as per final quotation.') as $item): ?>
            <li><i class="fa-solid fa-check"></i> <?= o2e($item) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="card ie ie--ex">
          <h3><span class="b"><i class="fa-solid fa-xmark"></i></span> What's Excluded</h3>
          <ul>
            <?php foreach (o2_list_items(isset($incx['excluded']) ? $incx['excluded'] : '', 'Exclusions will be shared as per final quotation.') as $item): ?>
            <li><i class="fa-solid fa-xmark"></i> <?= o2e($item) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <div style="margin-top:6mm">
        <div class="sec-h">
          <div><span class="gold-rule" style="display:block;margin-bottom:7px"></span><span class="t">Costing Details</span></div>
          <span class="s">All amounts in <?= o2e(o2nv($q['currency'], 'INR')) ?> · for <?= o2e($o2_guests) ?></span>
        </div>
        <table class="cost">
          <thead>
            <tr><th>Package</th><th>Tour Cost</th><th>Tax (GST)</th><th>TCS</th><th>Travel Cost</th><th>Grand Total</th></tr>
          </thead>
          <tbody>
            <?php
            $o2_grp = isset($cost['computed']['group']) ? $cost['computed']['group'] : array();
            if (empty($o2_grp)) {
              $o2_grp = array(array('package_type' => 'Package', 'tour_cost_display' => '0', 'tax_display' => '0', 'tcs_display' => '0', 'travel_display' => '0', 'total_display' => '0'));
            }
            $o2_ci = 0;
            foreach ($o2_grp as $o2_row):
              $o2_rec = ($o2_ci === 1 || (count($o2_grp) === 1));
            ?>
            <tr<?= $o2_rec ? ' class="rec"' : '' ?>>
              <td class="pk"><?php if ($o2_rec): ?><span class="recbar"><?= o2e(o2nv($o2_row['package_type'], 'Package')) ?> <span class="tag">Recommended</span></span><?php else: ?><?= o2e(o2nv($o2_row['package_type'], 'Package')) ?><?php endif; ?></td>
              <td><?= o2e($o2_row['tour_cost_display']) ?></td>
              <td><?= o2e(o2nv($o2_row['tax_display'], '0')) ?></td>
              <td><?= o2e($o2_row['tcs_display']) ?></td>
              <td><?= o2e($o2_row['travel_display']) ?></td>
              <td class="gt"><?= o2e($o2_row['total_display']) ?></td>
            </tr>
            <?php $o2_ci++; endforeach; ?>
          </tbody>
        </table>
        <p class="cost-note"><i class="fa-solid fa-circle-info"></i> Prices are indicative and subject to availability and currency fluctuation at the time of confirmation.</p>
      </div>
    </div>
    <?php o2_foot(); ?>
  </section>

  <!-- PAYMENT -->
  <section class="page">
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
            <div class="f"><div class="l">Account Name</div><div class="v"><?= o2e(o2nv($bank['account_name'], 'NA')) ?></div></div>
          </div>
          <div class="bankcard__div"></div>
          <div class="bankrow">
            <div class="f"><div class="l">Account Number</div><div class="v mono"><?= o2e(o2nv($bank['account_no'], 'NA')) ?></div></div>
            <div class="f"><div class="l">IFSC Code</div><div class="v mono"><?= o2e(o2nv($bank['ifsc_code'], o2nv($bank['swift_code'], 'NA'))) ?></div></div>
            <div class="f"><div class="l">Bank Name</div><div class="v"><?= o2e(o2nv($bank['bank_name'], 'NA')) ?></div></div>
            <div class="f"><div class="l">Branch</div><div class="v"><?= o2e(o2nv($bank['branch_name'], 'NA')) ?></div></div>
          </div>
          <div class="bankcard__div"></div>
          <div class="bankrow solo">
            <div class="f"><div class="l">UPI ID</div><div class="v mono"><?= o2e(o2nv($bank['upi_id'], 'NA')) ?></div></div>
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
  <section class="page<?= count($testimonials) > 2 ? ' page-flow' : '' ?>">
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
        <?php endforeach; else: ?>
        <div class="card testi" style="grid-template-columns:1fr;padding:20px;text-align:center;color:var(--muted)">Customer testimonials can be managed from Quotation Builder settings.</div>
        <?php endif; ?>
      </div>
      <div class="statbar">
        <div class="st"><div class="n"><?= o2e($o2_google_rating) ?>★</div><div class="l">Google Rating</div></div>
        <div class="st"><div class="n"><?= o2e($o2_review_count) ?></div><div class="l">Verified Reviews</div></div>
        <div class="st"><div class="n"><?= o2e($o2_traveller_cnt) ?></div><div class="l">Happy Travellers</div></div>
      </div>
    </div>
    <?php o2_foot(); ?>
  </section>

  <!-- TERMS -->
  <section class="page page-flow">
    <?php o2_strip('Please Read Carefully', 'Terms', '&amp; <b>Conditions</b>'); ?>
    <div class="page__wm"></div>
    <div class="page__body">
      <?php
      $o2_terms_html = trim(isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '');
      if ($o2_terms_html !== ''):
      ?>
      <div class="terms-content" style="font-size:10px;line-height:1.55;color:var(--ink-soft);margin-top:2mm">
        <?= $o2_terms_html ?>
      </div>
      <?php else: ?>
      <div class="terms-grid" style="margin-top:2mm">
        <div class="term"><span class="ic term__b"><i class="fa-solid fa-file-lines"></i></span><div><h4>Booking Policy</h4><p>A 30% advance confirms the booking; the balance is payable 21 days prior to departure. Confirmation is subject to availability at the time of payment.</p></div></div>
        <div class="term"><span class="ic term__b"><i class="fa-solid fa-ban"></i></span><div><h4>Cancellation Policy</h4><p>Cancellations 30+ days before travel: 25% charge. 15–29 days: 50%. Within 14 days: 100%. Charges apply on the total tour value.</p></div></div>
        <div class="term"><span class="ic term__b"><i class="fa-solid fa-rotate-left"></i></span><div><h4>Refund Policy</h4><p>Eligible refunds are processed within 15–21 working days to the original payment method, after deduction of applicable supplier charges.</p></div></div>
        <div class="term"><span class="ic term__b"><i class="fa-solid fa-passport"></i></span><div><h4>Visa Disclaimer</h4><p>Visa approval is at the sole discretion of the issuing authority. Rejections are not the company's liability; fees are non-refundable.</p></div></div>
        <div class="term"><span class="ic term__b"><i class="fa-solid fa-hotel"></i></span><div><h4>Hotel Policies</h4><p>Standard check-in is 14:00 and check-out 12:00. Room categories are confirmed on availability; early check-in is chargeable.</p></div></div>
        <div class="term"><span class="ic term__b"><i class="fa-solid fa-plane"></i></span><div><h4>Flight Policies</h4><p>Flight timings and fares are subject to airline confirmation. Schedule changes and baggage rules are governed by airline terms.</p></div></div>
        <div class="term"><span class="ic term__b"><i class="fa-solid fa-earth-americas"></i></span><div><h4>Force Majeure</h4><p>The company is not liable for disruptions caused by weather, natural events, political unrest or any circumstances beyond reasonable control.</p></div></div>
        <div class="term"><span class="ic term__b"><i class="fa-solid fa-shield-halved"></i></span><div><h4>Travel Insurance</h4><p>Complimentary basic insurance is included where stated. Travellers are advised to review coverage and opt for enhanced protection if required.</p></div></div>
      </div>
      <?php endif; ?>
      <div class="card" style="margin-top:6mm;padding:12px 15px;display:flex;gap:11px;align-items:center;background:var(--cream)">
        <span class="ic ic--navy"><i class="fa-solid fa-circle-info"></i></span>
        <p class="muted" style="font-size:10px;margin:0;line-height:1.5">By proceeding with payment, the traveller acknowledges and accepts all terms and conditions outlined above. This quotation is valid for 7 days from the date of issue.</p>
      </div>
    </div>
    <?php o2_foot(); ?>
  </section>

  <!-- THANK YOU -->
  <section class="page thanks">
    <div class="page__wm thanks__wm"></div>
    <div class="thanks__wrap">
      <div class="logo">
        <img class="logo__slot" src="<?= o2e($o2_logo) ?>" alt="Company logo">
        <div>
          <div class="logo__name"><?= o2e($o2_company) ?></div>
          <div class="logo__tag"><?= o2e($o2_tagline) ?></div>
        </div>
      </div>
      <div class="thanks__k">With Heartfelt Gratitude</div>
      <h2 class="thanks__big">Thank You</h2>
      <p class="thanks__msg">We look forward to creating unforgettable travel memories for you. Your journey through <?= o2e($o2_dest) ?> is just the beginning of many more to come.</p>
      <div class="thanks__stats">
        <div class="s"><div class="n"><?= o2e($o2_google_rating) ?>★</div><div class="l">Google Rating</div></div>
        <div class="s"><div class="n"><?= o2e($o2_review_count) ?></div><div class="l">Reviews</div></div>
        <div class="s"><div class="n"><?= o2e($o2_traveller_cnt) ?></div><div class="l">Happy Travellers</div></div>
      </div>
      <div class="thanks__social">
        <?php $o2_web = o2nv($ty['website'], ''); ?>
        <a href="<?= o2e($o2_web !== '' ? $o2_web : '#') ?>" aria-label="Website"><i class="fa-solid fa-earth-asia"></i></a>
        <a href="mailto:<?= o2e(o2nv($ty['company_email'], '')) ?>" aria-label="Email"><i class="fa-solid fa-envelope"></i></a>
        <a href="tel:<?= o2e(preg_replace('/\s+/', '', o2nv($ty['company_contact'], o2nv($ty['user_mobile'], '')))) ?>" aria-label="Phone"><i class="fa-solid fa-phone"></i></a>
        <a href="<?= o2e($o2_web !== '' ? $o2_web : '#') ?>" aria-label="Company"><i class="fa-brands fa-facebook-f"></i></a>
      </div>
      <div class="thanks__contact">
        <span class="c"><i class="fa-solid fa-location-dot"></i> <?= o2e(o2nv($ty['company_address'], '')) ?></span>
        <span class="c"><i class="fa-solid fa-phone"></i> <?= o2e(o2nv($ty['company_contact'], o2nv($ty['user_mobile'], ''))) ?></span>
        <span class="c"><i class="fa-solid fa-envelope"></i> <?= o2e(o2nv($ty['company_email'], '')) ?></span>
        <span class="c"><i class="fa-solid fa-earth-asia"></i> <?= o2e($o2_web) ?></span>
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
    try { window.focus(); } catch (e) {}
    window.print();
  }
  function waitForImages() {
    var imgs = Array.prototype.slice.call(document.images || []);
    var pending = imgs.filter(function(img) { return !img.complete; });
    if (pending.length === 0) return Promise.resolve();
    return Promise.all(pending.map(function(img) {
      return new Promise(function(resolve) {
        img.addEventListener('load', resolve, { once: true });
        img.addEventListener('error', resolve, { once: true });
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
    var safety = new Promise(function(resolve) { setTimeout(resolve, 4000); });
    Promise.race([
      Promise.all([waitForImages(), waitForFonts()]),
      safety
    ]).then(function() { setTimeout(doPrint, 150); });
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
