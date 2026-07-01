<?php
include_once('../../model.php');
include_once(__DIR__ . '/../../app_settings/print_html/quotation_html/generic_quotation_data.php');
include_once(__DIR__ . '/single_quotation_parts.php');
global $model, $app_name, $app_email_id;

$quotation_id1 = isset($_GET['quotation']) ? $_GET['quotation'] : (isset($_GET['quotation_id']) ? $_GET['quotation_id'] : '');
$quotation_id = base64_decode($quotation_id1, true);
if ($quotation_id === false || $quotation_id === '') {
  $quotation_id = $quotation_id1;
}
$quotation_id = (int) $quotation_id;

$hero = array();
$ov = array();
$sq_total_pages = 9;
$sq_wm_url = BASE_URL . 'model/app_settings/print_html/quotation_html/quotation_html_1/assets/globe-watermark.png';
$sq_assets = BASE_URL . 'model/app_settings/print_html/quotation_html/quotation_html_1/assets/';
$sq_dummy_img = BASE_URL . 'images/dummy-image.jpg';
$sq_gallery_images = array();

if ($quotation_id > 0) {
  $q = get_generic_quotation_data($quotation_id);
  if (!empty($q['found'])) {
    $hero = $q['hero'];
    $ov = $q['tour_overview'];
    if (!empty($q['gallery_images']) && is_array($q['gallery_images'])) {
      foreach ($q['gallery_images'] as $gi) {
        if (is_string($gi) && trim($gi) !== '' && stripos($gi, 'dummy') === false) {
          $sq_gallery_images[] = $gi;
        }
      }
    }
  }
}

if (empty($sq_gallery_images)) {
  $sq_gallery_images = array(
    BASE_URL . 'images/hotel.png',
    BASE_URL . 'images/itinerary.png',
    BASE_URL . 'images/activity.jpg',
    BASE_URL . 'images/dummy-image.jpg',
  );
}
while (count($sq_gallery_images) < 4) {
  $sq_gallery_images[] = $sq_gallery_images[count($sq_gallery_images) - 1];
}
$sq_gallery_images = array_slice($sq_gallery_images, 0, 4);

$sq_quotation = null;
$year = date('Y');
if ($quotation_id > 0) {
  $sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id'"));
  if ($sq_quotation) {
    $yr = explode('-', $sq_quotation['quotation_date']);
    $year = $yr[0];
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= sq_e(sq_destination_name()) ?> Tour Package Quotation &mdash; <?= sq_e(sq_company_name()) ?></title>
  <meta name="description" content="Tour quotation <?= sq_e(sq_quotation_code()) ?> &mdash; itinerary, hotels, flights, costing and more.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;0,900;1,500&family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" type="text/css" href="singlequotation.css">
 
</head>
<body>
  <main class="min-h-screen">
    <section class="page page-hero relative">
      <!-- <img src="" alt="Andaman" class="absolute inset-0 w-full h-full object-cover" /> -->
      <div class="absolute inset-0" style="background:var(--gradient-hero-overlay)">
      </div>
      <?php sq_render_hero_top_bar(); ?>
      <div class="relative z-10 px-12 pt-10 text-cream text-center">
        <div class="flex items-center gap-3 mb-5 justify-center">
          <span class="h-px w-12 bg-[color:var(--gold)]">
          </span>
          <span class="text-[11px] tracking-[0.4em] uppercase text-[color:var(--gold)]">Exclusive Travel Proposal</span>
        </div>
        <h1 class="font-display font-black leading-[0.92] text-cream" style="font-size:84px">Andaman</h1>
        <h2 class="font-display italic text-3xl mt-1 gold-text inline-block">a curated escape</h2>
        <p class="font-serif-soft text-2xl mt-6 max-w-lg text-cream/85 text-center" style="margin: 0 auto;">Discover unforgettable experiences — breathtaking landscapes, vibrant cultures, and moments that stay with you forever.</p>
      </div>
      <div class="relative z-10 mt-12 px-12">
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
        <div class="grid grid-cols-4 gap-4 h-32" style="margin-top:50px;">
          <?php foreach ($sq_gallery_images as $sq_gi) { ?>
            <img src="<?= sq_e($sq_gi) ?>" alt="" class="w-full h-full object-cover rounded-md ring-1 ring-[color:var(--gold)]/40" />
          <?php } ?>
        </div>
      </div>
  
      <div class="absolute bottom-0 left-0 right-0 z-10 px-12 pb-10 pt-24" style="background:linear-gradient(180deg, transparent, oklch(0.10 0.05 260 / 0.95))">
        <div class="flex items-end justify-between">
          <div>
            <div class="text-[10px] uppercase tracking-[0.35em] text-[color:var(--gold)] mb-1.5">Prepared Exclusively For</div>
            <div class="font-display text-4xl text-cream">Harshal Gurav</div>
            <div class="text-cream/70 text-sm mt-1 font-serif-soft italic">4 Travellers &middot; 15-07-2026 &ndash; 20-07-2026</div>
          </div>
          <div class="text-right text-cream/80">
            <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">5 N / 6 D</div>
            <div class="font-display text-2xl mt-1"><span>Package Tour</span></div>
          </div>
        </div>
      </div>
    </section>

    <div class="relative px-20 py-6">
        <div class="rounded-xl px-6 py-4 flex items-center gap-4" style="background:var(--gradient-navy)">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles w-5 h-5 text-[color:var(--gold)]" aria-hidden="true">
            <path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path>
            <path d="M20 2v4"></path>
            <path d="M22 4h-4"></path>
            <circle cx="4" cy="20" r="2"></circle>
          </svg>
          <p class="font-serif-soft italic text-cream text-[15px]">A personalized travel experience exclusively designed for <span class="gold-text font-semibold not-italic">Harshal Gurav</span></p>
        </div>
        <div class="mt-7">
          <div class="divider-fancy">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart w-4 h-4" aria-hidden="true">
              <path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"></path>
            </svg>
          </div>
          <h2 class="font-display text-4xl text-[color:var(--navy)] mt-3">Dear Harshal,</h2>
          <p class="mt-3 text-[15.5px] leading-relaxed text-[color:var(--ink)]/85 font-serif-soft text-lg">Thank you for choosing iTours Web Services LLP for your upcoming journey to Andaman. We are delighted to present this carefully crafted travel proposal &mdash; thoughtfully designed to deliver memorable experiences, seamless arrangements and exceptional hospitality at every step.</p>
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
                    <path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"></path>
                    <path d="M14 2v5a1 1 0 0 0 1 1h5"></path>
                    <path d="M10 9H8"></path>
                    <path d="M16 13H8"></path>
                    <path d="M16 17H8"></path>
                  </svg>
                </div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Quotation ID</div>
              </div>
              <div class="mt-2.5 font-display text-lg text-[color:var(--navy)]">QTN/2026/99</div>
            </div>
            <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                    <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path>
                    <path d="m9 12 2 2 4-4"></path>
                  </svg>
                </div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Tour ID</div>
              </div>
              <div class="mt-2.5 font-display text-lg text-[color:var(--navy)]">53</div>
            </div>
            <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                    <path d="M8 2v4"></path>
                    <path d="M16 2v4"></path>
                    <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                    <path d="M3 10h18"></path>
                  </svg>
                </div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Quotation Date</div>
              </div>
              <div class="mt-2.5 font-display text-lg text-[color:var(--navy)]">23-06-2026</div>
            </div>
            <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                    <path d="M8 2v4"></path>
                    <path d="M16 2v4"></path>
                    <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                    <path d="M3 10h18"></path>
                  </svg>
                </div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Travel Dates</div>
              </div>
              <div class="mt-2.5 font-display text-lg text-[color:var(--navy)]">15-07-2026 &ndash; 20-07-2026</div>
            </div>
            <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 6v6l4 2"></path>
                  </svg>
                </div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Duration</div>
              </div>
              <div class="mt-2.5 font-display text-lg text-[color:var(--navy)]">5 Nights / 6 Days</div>
            </div>
            <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <path d="M16 3.128a4 4 0 0 1 0 7.744"></path>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                  </svg>
                </div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Guests</div>
              </div>
              <div class="mt-2.5 font-display text-lg text-[color:var(--navy)]">4 Adults</div>
            </div>
          </div>
        </div>
        <div class="grid grid-cols-5 gap-4 mt-6">
          <div class="col-span-2 rounded-xl p-5 text-cream relative overflow-hidden" style="background:var(--gradient-navy)">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-briefcase w-5 h-5 text-[color:var(--gold)]" aria-hidden="true">
              <path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
              <rect width="20" height="14" x="2" y="6" rx="2"></rect>
            </svg>
            <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)] mt-2">Package Type</div>
            <div class="font-display text-3xl mt-1"><span>Tailored Package</span></div>
            <div class="text-cream/70 text-xs mt-1.5 font-serif-soft italic">Hand-picked value with luxury touchpoints.</div>
            <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full" style="background:oklch(0.78 0.13 78 / 0.2)"></div>
          </div>
          <div class="col-span-3 rounded-xl bg-white p-5 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
            <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Prepared For</div>
            <div class="font-display text-2xl text-[color:var(--navy)] mt-1">Harshal Gurav</div>
            <div class="mt-3 space-y-1.5 text-[13px] text-[color:var(--ink)]/85">
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail w-3.5 h-3.5 text-[color:var(--teal)]" aria-hidden="true">
                  <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path>
                  <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                </svg>
                <a href="mailto:harshal.toursathi@gmail.com" style="color:inherit;text-decoration:none;">harshal.toursathi@gmail.com</a>
              </div>
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone w-3.5 h-3.5 text-[color:var(--teal)]" aria-hidden="true">
                  <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path>
                </svg>
                <a href="tel:+918888139189" style="color:inherit;text-decoration:none;">+918888139189</a>
              </div>
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-3.5 h-3.5 text-[color:var(--teal)]" aria-hidden="true">
                  <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
                Andaman
              </div>
            </div>
          </div>
        </div>
        <div class="mt-6 rounded-xl overflow-hidden relative h-36">
          <img
            src="../../../images/destination-banner.png"
            alt="Andaman"
            class="absolute inset-0 w-full h-full object-cover"
            loading="lazy"
            onerror="this.onerror=null;this.src='../../../images/dummy-image.jpg';" />
          <div class="absolute inset-0" style="background:linear-gradient(90deg, oklch(0.20 0.06 260 / 0.85), oklch(0.20 0.06 260 / 0.2))"></div>
          <div class="relative p-5 text-cream h-full flex flex-col justify-between">
            <div class="flex items-center gap-2 text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-compass w-3.5 h-3.5" aria-hidden="true">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="m16.24 7.76-1.804 5.411a2 2 0 0 1-1.265 1.265L7.76 16.24l1.804-5.411a2 2 0 0 1 1.265-1.265z"></path>
              </svg>
              Destination
            </div>
            <div>
              <div class="font-display text-3xl">Andaman</div>
              <div class="text-cream/80 text-xs font-serif-soft italic">Where futuristic skylines meet tropical serenity.</div>
            </div>
          </div>
        </div>
    </div>

    <div class="relative px-20 py-8">
        <div class="flex items-end justify-between">
          <div>
            <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Where You&#x27;ll Stay</div>
            <h2 class="font-display text-4xl text-[color:var(--navy)] mt-1">Accommodation Details</h2>
          </div>
          <div class="rounded-full px-4 py-1.5 text-cream text-xs uppercase tracking-[0.2em]" style="background:var(--gradient-navy)">
            <span class="text-[color:var(--gold)] mr-2">✦</span>Royal Package
          </div>
        </div>
        <hr class="gold-rule mt-3" />
        <div class="mt-6 space-y-4">
          <!-- Hotel 1 -->
          <div class="rounded-xl overflow-hidden bg-white border border-[color:var(--gold)]/25 grid grid-cols-5" style="box-shadow:var(--shadow-card)">
            <div class="col-span-1 relative">
              <img src="../../../images/hotel.png" alt="Sinclairs Bayview" class="col-span-2 h-56 w-full object-contain" />
            </div>
            <div class="col-span-4 p-5">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5 text-[12px] uppercase tracking-[0.22em] text-[color:var(--teal)]">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-3 h-3" aria-hidden="true">
                    <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                  </svg>
                  Port Blair Andaman
                </div>
                <div class="flex gap-0.5 text-[color:var(--gold)]">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-3 h-3 fill-current" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-3 h-3 fill-current" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-3 h-3 fill-current" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>
                </div>
              </div>
              <h3 class="font-display text-2xl text-[color:var(--navy)] mt-1.5">Sinclairs Bayview</h3>
              <div class="text-[13px] font-serif-soft italic text-[color:var(--ink)]/70 mt-0.5">Deluxe Room</div>
              <div class="grid grid-cols-3 gap-3 mt-4">
                <div class="rounded-lg bg-[color:var(--cream)] p-2.5 border border-[color:var(--gold)]/20">
                  <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Check-in</div>
                  <div class="font-display text-sm text-[color:var(--navy)] mt-0.5">15-07-2026</div>
                </div>
                <div class="rounded-lg bg-[color:var(--cream)] p-2.5 border border-[color:var(--gold)]/20">
                  <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Check-out</div>
                  <div class="font-display text-sm text-[color:var(--navy)] mt-0.5">16-07-2026</div>
                </div>
                <div class="rounded-lg p-2.5 text-cream" style="background:var(--gradient-navy)">
                  <div class="text-[9px] uppercase tracking-[0.22em] text-[color:var(--gold)]">Nights</div>
                  <div class="font-display text-sm mt-0.5">1 Night</div>
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
          <!-- Hotel 2 -->
          <div class="rounded-xl overflow-hidden bg-white border border-[color:var(--gold)]/25 grid grid-cols-5" style="box-shadow:var(--shadow-card)">
            <div class="col-span-1 relative">
              <img src="../../../images/hotel.png" alt="TSG Blue Resort &amp; Spa" class="col-span-2 h-56 w-full object-contain" />
            </div>
            <div class="col-span-4 p-5">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5 text-[12px] uppercase tracking-[0.22em] text-[color:var(--teal)]">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-3 h-3" aria-hidden="true">
                    <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                  </svg>
                  Havelock Island
                </div>
                <div class="flex gap-0.5 text-[color:var(--gold)]">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-3 h-3 fill-current" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-3 h-3 fill-current" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-3 h-3 fill-current" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>
                </div>
              </div>
              <h3 class="font-display text-2xl text-[color:var(--navy)] mt-1.5">TSG Blue Resort &amp; Spa</h3>
              <div class="text-[13px] font-serif-soft italic text-[color:var(--ink)]/70 mt-0.5">Deluxe Room</div>
              <div class="grid grid-cols-3 gap-3 mt-4">
                <div class="rounded-lg bg-[color:var(--cream)] p-2.5 border border-[color:var(--gold)]/20">
                  <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Check-in</div>
                  <div class="font-display text-sm text-[color:var(--navy)] mt-0.5">16-07-2026</div>
                </div>
                <div class="rounded-lg bg-[color:var(--cream)] p-2.5 border border-[color:var(--gold)]/20">
                  <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Check-out</div>
                  <div class="font-display text-sm text-[color:var(--navy)] mt-0.5">18-07-2026</div>
                </div>
                <div class="rounded-lg p-2.5 text-cream" style="background:var(--gradient-navy)">
                  <div class="text-[9px] uppercase tracking-[0.22em] text-[color:var(--gold)]">Nights</div>
                  <div class="font-display text-sm mt-0.5">2 Nights</div>
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
          <!-- Hotel 3 -->
          <div class="rounded-xl overflow-hidden bg-white border border-[color:var(--gold)]/25 grid grid-cols-5" style="box-shadow:var(--shadow-card)">
            <div class="col-span-1 relative">
              <img src="../../../images/hotel.png" alt="SeaShell Neil Island" class="col-span-2 h-56 w-full object-contain" />
            </div>
            <div class="col-span-4 p-5">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5 text-[12px] uppercase tracking-[0.22em] text-[color:var(--teal)]">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-3 h-3" aria-hidden="true">
                    <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                  </svg>
                  Neil Island
                </div>
                <div class="flex gap-0.5 text-[color:var(--gold)]">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-3 h-3 fill-current" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-3 h-3 fill-current" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-3 h-3 fill-current" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>
                </div>
              </div>
              <h3 class="font-display text-2xl text-[color:var(--navy)] mt-1.5">SeaShell Neil Island</h3>
              <div class="text-[13px] font-serif-soft italic text-[color:var(--ink)]/70 mt-0.5">Deluxe Room</div>
              <div class="grid grid-cols-3 gap-3 mt-4">
                <div class="rounded-lg bg-[color:var(--cream)] p-2.5 border border-[color:var(--gold)]/20">
                  <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Check-in</div>
                  <div class="font-display text-sm text-[color:var(--navy)] mt-0.5">18-07-2026</div>
                </div>
                <div class="rounded-lg bg-[color:var(--cream)] p-2.5 border border-[color:var(--gold)]/20">
                  <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--navy)]/60">Check-out</div>
                  <div class="font-display text-sm text-[color:var(--navy)] mt-0.5">20-07-2026</div>
                </div>
                <div class="rounded-lg p-2.5 text-cream" style="background:var(--gradient-navy)">
                  <div class="text-[9px] uppercase tracking-[0.22em] text-[color:var(--gold)]">Nights</div>
                  <div class="font-display text-sm mt-0.5">2 Nights</div>
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

          <div class="grid grid-cols-2 gap-3 mt-4">
            <!-- Flight 1: Air India -->
            <div class="rounded-xl bg-white overflow-hidden border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
              <div class="px-4 py-2 flex items-center justify-between text-cream" style="background:var(--gradient-navy)">
                <div class="flex items-center gap-2">
                  <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center overflow-hidden">
                    <img src="../../../images/dummy-image.jpg" alt="Air India" class="w-full h-full object-contain" />
                  </div>
                  <div class="text-xs">Air India</div>
                </div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-[color:var(--gold)]">Economy</div>
              </div>
              <div class="p-4">
                <div class="flex items-center justify-between">
                  <div style="max-width:110px;">
                    <div class="font-display text-2xl text-[color:var(--navy)]">PNQ</div>
                    <div class="text-[10px] text-[color:var(--ink)]/60 uppercase tracking-wide">Pune International Airport</div>
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
                    <div class="font-display text-2xl text-[color:var(--navy)]">IXZ</div>
                    <div class="text-[10px] text-[color:var(--ink)]/60 uppercase tracking-wide">Veer Savarkar International Airport</div>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-dashed border-[color:var(--gold)]/40 text-[10px]">
                  <div>
                    <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60">Departure</div>
                    <div class="font-display text-[13px] text-[color:var(--navy)]">15 Jul - 00:00</div>
                  </div>
                  <div>
                    <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60">Arrival</div>
                    <div class="font-display text-[13px] text-[color:var(--navy)]">15 Jul - 00:00</div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Flight 2: IndiGo -->
            <div class="rounded-xl bg-white overflow-hidden border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
              <div class="px-4 py-2 flex items-center justify-between text-cream" style="background:var(--gradient-navy)">
                <div class="flex items-center gap-2">
                  <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center overflow-hidden">
                    <img src="../../../images/dummy-image.jpg" alt="IndiGo" class="w-full h-full object-contain" />
                  </div>
                  <div class="text-xs">IndiGo</div>
                </div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-[color:var(--gold)]">Economy</div>
              </div>
              <div class="p-4">
                <div class="flex items-center justify-between">
                  <div style="max-width:110px;">
                    <div class="font-display text-2xl text-[color:var(--navy)]">IXZ</div>
                    <div class="text-[10px] text-[color:var(--ink)]/60 uppercase tracking-wide">Veer Savarkar International Airport</div>
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
                    <div class="font-display text-2xl text-[color:var(--navy)]">PNQ</div>
                    <div class="text-[10px] text-[color:var(--ink)]/60 uppercase tracking-wide">Pune International Airport</div>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-dashed border-[color:var(--gold)]/40 text-[10px]">
                  <div>
                    <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60">Departure</div>
                    <div class="font-display text-[13px] text-[color:var(--navy)]">20 Jul - 00:00</div>
                  </div>
                  <div>
                    <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60">Arrival</div>
                    <div class="font-display text-[13px] text-[color:var(--navy)]">20 Jul - 00:00</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div>
          <h2 class="font-display text-2xl text-[color:var(--navy)] flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-car w-5 h-5 text-[color:var(--gold)]" aria-hidden="true">
              <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path>
              <circle cx="7" cy="17" r="2"></circle>
              <path d="M9 17h6"></path>
              <circle cx="17" cy="17" r="2"></circle>
            </svg> Transportation
          </h2>
          <hr class="gold-rule mt-2" />
          <div class="rounded-xl bg-white border border-[color:var(--gold)]/25 grid grid-cols-5 mt-3 overflow-hidden" style="box-shadow:var(--shadow-card); ">
            <div class="col-span-2 bg-[color:var(--navy)] grid place-items-center p-4 " >
              <img src="../../../images/vehicle.png" alt="Vehicle" style="width:100%;height:auto;object-fit:contain; height: 250px;" />
            </div>
            <div class="col-span-3 p-4">
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-[10px] uppercase tracking-[0.22em] text-[color:var(--teal)]">Vehicle</div>
                  <h3 class="font-display text-xl text-[color:var(--navy)]">INNOVA</h3>
                </div>
                <span class="text-[10px] px-2.5 py-1 rounded-full bg-[color:var(--gold-soft)]/40 text-[color:var(--navy)]">1 Vehicle</span>
              </div>
              <div class="grid grid-cols-3 gap-2 mt-3 text-[11px]">
                <div class="rounded-lg bg-[color:var(--cream)] p-2 border border-[color:var(--gold)]/20">
                  <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60 text-[9px]">Pickup</div>
                  <div class="font-display text-[13px] text-[color:var(--navy)]">Port Blair Andaman</div>
                </div>
                <div class="rounded-lg bg-[color:var(--cream)] p-2 border border-[color:var(--gold)]/20">
                  <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60 text-[9px]">Drop</div>
                  <div class="font-display text-[13px] text-[color:var(--navy)]">Havelock Island</div>
                </div>
                <div class="rounded-lg bg-[color:var(--cream)] p-2 border border-[color:var(--gold)]/20">
                  <div class="uppercase tracking-[0.2em] text-[color:var(--navy)]/60 text-[9px]">Duration</div>
                  <div class="font-display text-[13px] text-[color:var(--navy)]">6 Days</div>
                </div>
              </div>
              <div class="text-[11px] text-[color:var(--ink)]/70 mt-2 font-serif-soft italic">15-07-2026 &mdash; 6 Days</div>
            </div>
          </div>
        </div>
    </div>

    <section class=" px-20 py-7">
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
          <!-- Day 01 -->
          <div class="itinerary-card relative rounded-2xl overflow-hidden bg-white border border-[color:var(--gold)]/30 grid grid-cols-12" style="box-shadow:var(--shadow-card)">
            <div class="col-span-4 relative">
              <img src="../../../images/itinerary.png" alt="Arrive Port Blair-Cellular Jail-Light &amp; Sound Show" class="w-full h-full object-cover absolute inset-0" />
              <div class="absolute inset-0" style="background:linear-gradient(135deg, oklch(0.20 0.06 260 / 0.55), oklch(0.20 0.06 260 / 0.05) 60%)"></div>
              <div class="absolute top-3 left-3 rounded-xl px-3 py-2 text-cream backdrop-blur-md" style="background:oklch(0.20 0.06 260 / 0.75)">
                <div class="text-[8px] uppercase tracking-[0.3em] text-[color:var(--gold)] leading-none">Day</div>
                <div class="font-display text-3xl leading-none mt-0.5">01</div>
              </div>
              <div class="absolute bottom-3 left-3 right-3 text-cream">
                <div class="text-[10px] uppercase tracking-[0.25em] text-[color:var(--gold)]">15-07-2026</div>
                <div class="font-display text-lg leading-tight drop-shadow">Arrive Port Blair-Cellular Jail-Light &amp; Sound Show</div>
              </div>
            </div>
            <div class="col-span-8 p-4 relative">
              <div class="absolute top-0 right-0 px-3 py-1 text-[9px] uppercase tracking-[0.22em] text-[color:var(--navy)] rounded-bl-xl" style="background:var(--gradient-gold); z-index:50;">15-07-2026</div>
              <div class="flex items-center gap-2 text-[14px] uppercase tracking-[0.22em] text-[color:var(--teal)] mt-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-3 h-3" aria-hidden="true">
                  <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
                Arrive Port Blair-Cellular Jail-Light &amp; Sound Show
              </div>
              <p class="text-18 text-[color:var(--ink)]/85 mt-2 leading-relaxed font-serif-soft" >On arrival at <strong>Port Blair airport</strong>, our representative will receive you and transfer to the hotel. After check-in, visit the historic <strong>Cellular Jail</strong> followed by the Light &amp; Sound Show. Return to hotel for overnight stay.</p>
              <div class="mt-3 pt-3 border-t border-dashed border-[color:var(--gold)]/40">
                <div class="flex flex-wrap items-center gap-3">
                  <span class="inline-flex items-center gap-1.5 text-[12px] text-[color:var(--ink)]/75">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hotel w-3 h-3 text-[color:var(--gold)]"><path d="M10 22v-6.57"></path><path d="M12 11h.01"></path><path d="M12 7h.01"></path><path d="M14 15.43V22"></path><path d="M15 16a5 5 0 0 0-6 0"></path><path d="M16 11h.01"></path><path d="M16 7h.01"></path><path d="M8 11h.01"></path><path d="M8 7h.01"></path><rect x="4" y="2" width="16" height="20" rx="2"></rect></svg>
                    <strong>Overnight Stay:</strong> Port Blair
                  </span>
                  <span class="inline-flex items-center gap-1.5 text-[12px] text-[color:var(--ink)]/75">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-utensils w-3 h-3 text-[color:var(--gold)]"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>
                    <strong>Meal Plan:</strong> Lunch, Dinner
                  </span>
                </div>
              </div>
            </div>
          </div>
          <!-- Day 02 -->
          <div class="itinerary-card relative rounded-2xl overflow-hidden bg-white border border-[color:var(--gold)]/30 grid grid-cols-12" style="box-shadow:var(--shadow-card)">
            <div class="col-span-4 relative">
              <img src="../../../images/itinerary.png" alt="Port Blair to Havelock Island - Radhanagar Beach" class="w-full h-full object-cover absolute inset-0" />
              <div class="absolute inset-0" style="background:linear-gradient(135deg, oklch(0.20 0.06 260 / 0.55), oklch(0.20 0.06 260 / 0.05) 60%)"></div>
              <div class="absolute top-3 left-3 rounded-xl px-3 py-2 text-cream backdrop-blur-md" style="background:oklch(0.20 0.06 260 / 0.75)">
                <div class="text-[8px] uppercase tracking-[0.3em] text-[color:var(--gold)] leading-none">Day</div>
                <div class="font-display text-3xl leading-none mt-0.5">02</div>
              </div>
              <div class="absolute bottom-3 left-3 right-3 text-cream">
                <div class="text-[10px] uppercase tracking-[0.25em] text-[color:var(--gold)]">16-07-2026</div>
                <div class="font-display text-lg leading-tight drop-shadow">Port Blair to Havelock Island - Radhanagar Beach</div>
              </div>
            </div>
            <div class="col-span-8 p-4 relative">
              <div class="absolute top-0 right-0 px-3 py-1 text-[9px] uppercase tracking-[0.22em] text-[color:var(--navy)] rounded-bl-xl" style="background:var(--gradient-gold); z-index:50;">16-07-2026</div>
              <div class="flex items-center gap-2 text-[14px] uppercase tracking-[0.22em] text-[color:var(--teal)] mt-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-3 h-3" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
                Port Blair to Havelock Island - Radhanagar Beach
              </div>
              <p class="text-18 text-[color:var(--ink)]/85 mt-2 leading-relaxed font-serif-soft" >After breakfast, proceed to the jetty for the ferry to <strong>Havelock island</strong>. On arrival, check in at the hotel. In the afternoon, visit the world-famous <strong>Radhanagar Beach</strong> — one of Asia's best beaches.</p>
              <div class="mt-3 pt-3 border-t border-dashed border-[color:var(--gold)]/40">
                <div class="flex flex-wrap items-center gap-3">
                  <span class="inline-flex items-center gap-1.5 text-[12px] text-[color:var(--ink)]/75">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hotel w-3 h-3 text-[color:var(--gold)]"><path d="M10 22v-6.57"></path><path d="M12 11h.01"></path><path d="M12 7h.01"></path><path d="M14 15.43V22"></path><path d="M15 16a5 5 0 0 0-6 0"></path><path d="M16 11h.01"></path><path d="M16 7h.01"></path><path d="M8 11h.01"></path><path d="M8 7h.01"></path><rect x="4" y="2" width="16" height="20" rx="2"></rect></svg>
                    <strong>Overnight Stay:</strong> Havelock Island
                  </span>
                  <span class="inline-flex items-center gap-1.5 text-[12px] text-[color:var(--ink)]/75">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-utensils w-3 h-3 text-[color:var(--gold)]"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>
                    <strong>Meal Plan:</strong> Breakfast, Lunch, Dinner
                  </span>
                </div>
              </div>
            </div>
          </div>
          <!-- Day 03 -->
          <div class="itinerary-card relative rounded-2xl overflow-hidden bg-white border border-[color:var(--gold)]/30 grid grid-cols-12" style="box-shadow:var(--shadow-card)">
            <div class="col-span-4 relative">
              <img src="../../../images/itinerary.png" alt="Havelock Island Sightseeing" class="w-full h-full object-cover absolute inset-0" />
              <div class="absolute inset-0" style="background:linear-gradient(135deg, oklch(0.20 0.06 260 / 0.55), oklch(0.20 0.06 260 / 0.05) 60%)"></div>
              <div class="absolute top-3 left-3 rounded-xl px-3 py-2 text-cream backdrop-blur-md" style="background:oklch(0.20 0.06 260 / 0.75)">
                <div class="text-[8px] uppercase tracking-[0.3em] text-[color:var(--gold)] leading-none">Day</div>
                <div class="font-display text-3xl leading-none mt-0.5">03</div>
              </div>
              <div class="absolute bottom-3 left-3 right-3 text-cream">
                <div class="text-[10px] uppercase tracking-[0.25em] text-[color:var(--gold)]">17-07-2026</div>
                <div class="font-display text-lg leading-tight drop-shadow">Havelock Island Sightseeing</div>
              </div>
            </div>
            <div class="col-span-8 p-4 relative">
              <div class="absolute top-0 right-0 px-3 py-1 text-[9px] uppercase tracking-[0.22em] text-[color:var(--navy)] rounded-bl-xl" style="background:var(--gradient-gold); z-index:50;">17-07-2026</div>
              <div class="flex items-center gap-2 text-[14px] uppercase tracking-[0.22em] text-[color:var(--teal)] mt-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-3 h-3" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
                Havelock Island Sightseeing
              </div>
              <p class="text-18 text-[color:var(--ink)]/85 mt-2 leading-relaxed font-serif-soft">Enjoy a leisure day at <strong>Havelock Island</strong>. Optional activities include <strong>Elephant Beach</strong> snorkelling or water sports (at own cost). Spend the evening at leisure on the beach.</p>
              <div class="mt-3 pt-3 border-t border-dashed border-[color:var(--gold)]/40">
                <div class="flex flex-wrap items-center gap-3">
                  <span class="inline-flex items-center gap-1.5 text-[12px] text-[color:var(--ink)]/75">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hotel w-3 h-3 text-[color:var(--gold)]"><path d="M10 22v-6.57"></path><path d="M12 11h.01"></path><path d="M12 7h.01"></path><path d="M14 15.43V22"></path><path d="M15 16a5 5 0 0 0-6 0"></path><path d="M16 11h.01"></path><path d="M16 7h.01"></path><path d="M8 11h.01"></path><path d="M8 7h.01"></path><rect x="4" y="2" width="16" height="20" rx="2"></rect></svg>
                    <strong>Overnight Stay:</strong> Havelock Island
                  </span>
                  <span class="inline-flex items-center gap-1.5 text-[12px] text-[color:var(--ink)]/75">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-utensils w-3 h-3 text-[color:var(--gold)]"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>
                    <strong>Meal Plan:</strong> Breakfast, Lunch, Dinner
                  </span>
                </div>
              </div>
            </div>
          </div>
          <!-- Day 04 -->
          <div class="itinerary-card relative rounded-2xl overflow-hidden bg-white border border-[color:var(--gold)]/30 grid grid-cols-12" style="box-shadow:var(--shadow-card)">
            <div class="col-span-4 relative">
              <img src="../../../images/itinerary.png" alt="Havelock to Neil Island - Bharatpur &amp; Lakshmanpur" class="w-full h-full object-cover absolute inset-0" />
              <div class="absolute inset-0" style="background:linear-gradient(135deg, oklch(0.20 0.06 260 / 0.55), oklch(0.20 0.06 260 / 0.05) 60%)"></div>
              <div class="absolute top-3 left-3 rounded-xl px-3 py-2 text-cream backdrop-blur-md" style="background:oklch(0.20 0.06 260 / 0.75)">
                <div class="text-[8px] uppercase tracking-[0.3em] text-[color:var(--gold)] leading-none">Day</div>
                <div class="font-display text-3xl leading-none mt-0.5">04</div>
              </div>
              <div class="absolute bottom-3 left-3 right-3 text-cream">
                <div class="text-[10px] uppercase tracking-[0.25em] text-[color:var(--gold)]">18-07-2026</div>
                <div class="font-display text-lg leading-tight drop-shadow">Havelock to Neil Island - Bharatpur &amp; Lakshmanpur</div>
              </div>
            </div>
            <div class="col-span-8 p-4 relative">
              <div class="absolute top-0 right-0 px-3 py-1 text-[9px] uppercase tracking-[0.22em] text-[color:var(--navy)] rounded-bl-xl" style="background:var(--gradient-gold); z-index:50;">18-07-2026</div>
              <div class="flex items-center gap-2 text-[14px] uppercase tracking-[0.22em] text-[color:var(--teal)] mt-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-3 h-3" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
                Havelock to Neil Island - Bharatpur &amp; Lakshmanpur
              </div>
              <p class="text-18  text-[color:var(--ink)]/85 mt-2 leading-relaxed font-serif-soft" >After breakfast, transfer to the jetty for the ferry to <strong>Neil Island</strong>. Check in at the hotel. Visit <strong>Bharatpur Beach</strong> and <strong>Lakshmanpur Beach</strong> — serene coral-fringed shores perfect for sunset views.</p>
              <div class="mt-3 pt-3 border-t border-dashed border-[color:var(--gold)]/40">
                <div class="flex flex-wrap items-center gap-3">
                  <span class="inline-flex items-center gap-1.5 text-[12px] text-[color:var(--ink)]/75">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hotel w-3 h-3 text-[color:var(--gold)]"><path d="M10 22v-6.57"></path><path d="M12 11h.01"></path><path d="M12 7h.01"></path><path d="M14 15.43V22"></path><path d="M15 16a5 5 0 0 0-6 0"></path><path d="M16 11h.01"></path><path d="M16 7h.01"></path><path d="M8 11h.01"></path><path d="M8 7h.01"></path><rect x="4" y="2" width="16" height="20" rx="2"></rect></svg>
                    <strong>Overnight Stay:</strong> Neil Island
                  </span>
                  <span class="inline-flex items-center gap-1.5 text-[12px] text-[color:var(--ink)]/75">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-utensils w-3 h-3 text-[color:var(--gold)]"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>
                    <strong>Meal Plan:</strong> Breakfast, Lunch, Dinner
                  </span>
                </div>
              </div>
            </div>
          </div>
          <!-- Day 05 -->
          <div class="itinerary-card relative rounded-2xl overflow-hidden bg-white border border-[color:var(--gold)]/30 grid grid-cols-12" style="box-shadow:var(--shadow-card)">
            <div class="col-span-4 relative">
              <img src="../../../images/itinerary.png" alt="Neil Island to Port Blair - Chidiyatapu Sunset" class="w-full h-full object-cover absolute inset-0" />
              <div class="absolute inset-0" style="background:linear-gradient(135deg, oklch(0.20 0.06 260 / 0.55), oklch(0.20 0.06 260 / 0.05) 60%)"></div>
              <div class="absolute top-3 left-3 rounded-xl px-3 py-2 text-cream backdrop-blur-md" style="background:oklch(0.20 0.06 260 / 0.75)">
                <div class="text-[8px] uppercase tracking-[0.3em] text-[color:var(--gold)] leading-none">Day</div>
                <div class="font-display text-3xl leading-none mt-0.5">05</div>
              </div>
              <div class="absolute bottom-3 left-3 right-3 text-cream">
                <div class="text-[10px] uppercase tracking-[0.25em] text-[color:var(--gold)]">19-07-2026</div>
                <div class="font-display text-lg leading-tight drop-shadow">Neil Island to Port Blair - Chidiyatapu Sunset</div>
              </div>
            </div>
            <div class="col-span-8 p-4 relative">
              <div class="absolute top-0 right-0 px-3 py-1 text-[9px] uppercase tracking-[0.22em] text-[color:var(--navy)] rounded-bl-xl" style="background:var(--gradient-gold); z-index:50;">19-07-2026</div>
              <div class="flex items-center gap-2 text-[14px] uppercase tracking-[0.22em] text-[color:var(--teal)] mt-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-3 h-3" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
                Neil Island to Port Blair - Chidiyatapu Sunset
              </div>
              <p class="text-18 text-[color:var(--ink)]/85 mt-2 leading-relaxed font-serif-soft" >After breakfast, transfer by ferry back to <strong>Port Blair</strong>. Check in at the hotel. In the evening, visit <strong>Chidiyatapu</strong> for a stunning sunset over the Andaman Sea before returning to the hotel.</p>
              <div class="mt-3 pt-3 border-t border-dashed border-[color:var(--gold)]/40">
                <div class="flex flex-wrap items-center gap-3">
                  <span class="inline-flex items-center gap-1.5 text-[12px] text-[color:var(--ink)]/75">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hotel w-3 h-3 text-[color:var(--gold)]"><path d="M10 22v-6.57"></path><path d="M12 11h.01"></path><path d="M12 7h.01"></path><path d="M14 15.43V22"></path><path d="M15 16a5 5 0 0 0-6 0"></path><path d="M16 11h.01"></path><path d="M16 7h.01"></path><path d="M8 11h.01"></path><path d="M8 7h.01"></path><rect x="4" y="2" width="16" height="20" rx="2"></rect></svg>
                    <strong>Overnight Stay:</strong> Port Blair
                  </span>
                  <span class="inline-flex items-center gap-1.5 text-[12px] text-[color:var(--ink)]/75">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-utensils w-3 h-3 text-[color:var(--gold)]"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>
                    <strong>Meal Plan:</strong> Breakfast, Lunch, Dinner
                  </span>
                </div>
              </div>
            </div>
          </div>
      </div>
    
    </section>

    <div class="relative px-20 py-7">
      <div class="grid grid-cols-2 gap-4">
        <div class="rounded-xl bg-white p-5 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-4 h-4 text-[color:var(--navy)]" aria-hidden="true">
                <path d="M20 6 9 17l-5-5"></path>
              </svg>
            </div>
            <h3 class="font-display text-xl text-[color:var(--navy)]">What's Included</h3>
          </div>
          <hr class="gold-rule mt-2" />
          <ul class="mt-3 space-y-2">
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-3.5 h-3.5 text-[color:var(--teal)] mt-0.5 shrink-0" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg><span>Airport transfer - Airport to above mentioned Hotels on Private basis.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-3.5 h-3.5 text-[color:var(--teal)] mt-0.5 shrink-0" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg><span>Accommodation on twin/Double sharing basis in Deluxe hotels.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-3.5 h-3.5 text-[color:var(--teal)] mt-0.5 shrink-0" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg><span>Accommodation in above mentioned or similar hotels as per availability.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-3.5 h-3.5 text-[color:var(--teal)] mt-0.5 shrink-0" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg><span>Departure Airport transfers from above mentioned Hotels to Airport on Private basis.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-3.5 h-3.5 text-[color:var(--teal)] mt-0.5 shrink-0" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg><span>All Sightseeing &amp; transfers in Private Air-Conditioned Car as per itinerary.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-3.5 h-3.5 text-[color:var(--teal)] mt-0.5 shrink-0" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg><span>Meal plan As per tour itinerary.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-3.5 h-3.5 text-[color:var(--teal)] mt-0.5 shrink-0" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg><span>Baggage Allowance as per the airline policy.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-3.5 h-3.5 text-[color:var(--teal)] mt-0.5 shrink-0" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg><span>Travel assistance &amp; support.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-3.5 h-3.5 text-[color:var(--teal)] mt-0.5 shrink-0" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg><span>Meet &amp; greet at arrival.</span></li>
          </ul>
        </div>
        <div class="rounded-xl bg-white p-5 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg grid place-items-center bg-[color:var(--navy)]">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-4 h-4 text-[color:var(--gold)]" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
            </div>
            <h3 class="font-display text-xl text-[color:var(--navy)]">What's Excluded</h3>
          </div>
          <hr class="gold-rule mt-2" />
          <ul class="mt-3 space-y-2">
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-3.5 h-3.5 text-[color:var(--navy)]/70 mt-0.5 shrink-0" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg><span>Meals other than specified in the inclusions.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-3.5 h-3.5 text-[color:var(--navy)]/70 mt-0.5 shrink-0" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg><span>Any extra cost incurred on behalf of an individual due to illness, accident, or any personal emergency.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-3.5 h-3.5 text-[color:var(--navy)]/70 mt-0.5 shrink-0" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg><span>Anything not mentioned in inclusions.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-3.5 h-3.5 text-[color:var(--navy)]/70 mt-0.5 shrink-0" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg><span>Govt Tax of 5% over and above the Tour Cost mentioned.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-3.5 h-3.5 text-[color:var(--navy)]/70 mt-0.5 shrink-0" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg><span>TCS (5% or 10%) upto 7 lacs and TCS @20% on amounts above 7 lacs is applicable on GST inclusive price w.e.f. 1st October 2023.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-3.5 h-3.5 text-[color:var(--navy)]/70 mt-0.5 shrink-0" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg><span>Any increase in Airfare, Visa fees, Airport taxes, Govt Taxes, Fuel Surcharges and any applicability of new taxes from Govt.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-3.5 h-3.5 text-[color:var(--navy)]/70 mt-0.5 shrink-0" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg><span>Any increase in the rate of exchange leading to an increase in all land arrangements.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-3.5 h-3.5 text-[color:var(--navy)]/70 mt-0.5 shrink-0" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg><span>Cost of insurance for 60 years and above.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-3.5 h-3.5 text-[color:var(--navy)]/70 mt-0.5 shrink-0" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg><span>Porterage, laundry, telephone charges, shopping, wines &amp; alcoholic beverages.</span></li>
            <li class="flex items-start gap-2 text-[12px] text-[color:var(--ink)]/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-3.5 h-3.5 text-[color:var(--navy)]/70 mt-0.5 shrink-0" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg><span>Anything specifically not mentioned in the tour price includes column.</span></li>
          </ul>
        </div>
      </div>
      <div class="mt-4">
        <div class="flex items-end justify-between">
          <h3 class="font-display text-2xl text-[color:var(--navy)]">Costing Details</h3>
          <span class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">All values in INR · Per Package</span>
        </div>
        <hr class="gold-rule mt-2" />
        <div class="mt-4 rounded-xl overflow-hidden border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
          <div class="grid grid-cols-6 text-[10px] uppercase tracking-[0.2em] text-cream px-4 py-3" style="background:var(--gradient-navy)">
            <div class="col-span-1">Package</div>
            <div class="text-right">Tour Cost</div>
            <div class="text-right">Tax</div>
            <div class="text-right">TCS</div>
            <div class="text-right">Travel</div>
            <div class="text-right text-[color:var(--gold)]">Grand Total</div>
          </div>
          <div class="grid grid-cols-6 items-center px-4 py-3.5 text-[12.5px] bg-white border-t border-[color:var(--gold)]/20">
            <div class="col-span-1 flex items-center gap-2">
              <span class="font-display text-base text-[color:var(--navy)]">ECONOMY</span>
            </div>
            <div class="text-right text-[color:var(--ink)]/85">₹ INR 145,000.00</div>
            <div class="text-right text-[color:var(--ink)]/85">INR 6,000.00</div>
            <div class="text-right text-[color:var(--ink)]/85">₹ INR 0.00</div>
            <div class="text-right text-[color:var(--ink)]/85">₹ INR 75,000.00</div>
            <div class="text-right font-display text-lg text-[color:var(--navy)]">₹ INR 226,000.00</div>
          </div>
        </div>
        <div class="text-[10px] text-[color:var(--ink)]/55 mt-2 italic">* Prices indicative and subject to availability at the time of booking confirmation.</div>
      </div>
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
            <div class="font-display text-xl mt-4">ICICI Bank &middot; Current</div>
            <div class="grid grid-cols-2 gap-4 mt-5">
              <div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-cream/60">Account Name</div>
                <div class="font-display text-base">IT Web Services</div>
              </div>
              <div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-cream/60">Account Number</div>
                <div class="font-display text-base tracking-widest">78965412365</div>
              </div>
              <div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-cream/60">Branch</div>
                <div class="font-display text-base">iTours Web Services</div>
              </div>
              <div>
                <div class="text-[10px] uppercase tracking-[0.22em] text-cream/60">IFSC Code</div>
                <div class="font-display text-base">NA</div>
              </div>
              <div class="col-span-2">
                <div class="text-[10px] uppercase tracking-[0.22em] text-cream/60">UPI ID</div>
                <div class="font-display text-base text-[color:var(--gold)]">itwebservices@icici</div>
              </div>
            </div>
            <div class="absolute -right-10 -bottom-10 w-44 h-44 rounded-full" style="background:oklch(0.78 0.13 78 / 0.18)">
            </div>
          </div>
          <div class="col-span-2 rounded-2xl bg-white p-5 border border-[color:var(--gold)]/25 flex flex-col items-center justify-center text-center" style="box-shadow:var(--shadow-card)">
            <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Scan to Pay</div>
            <div class="mt-3 p-3 rounded-xl border-2 border-[color:var(--gold)]/40 bg-white">
              <div class="w-36 h-36 grid place-items-center" style="background:white">
                <img src="../../../images/dummy-image.jpg" alt="Payment QR" class="w-32 h-32 object-contain" />
              </div>
            </div>
            <div class="font-display text-lg text-[color:var(--navy)] mt-3">UPI · GPay · PhonePe</div>
            <div class="text-[11px] text-[color:var(--ink)]/60 font-serif-soft italic">itwebservices@icici</div>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-5">
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

    <div class="relative px-20 py-8">
        <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Trusted by Travellers</div>
        <h2 class="font-display text-4xl text-[color:var(--navy)] mt-1">What Our Travellers Say</h2>
        <hr class="gold-rule mt-3" />
        <div class="mt-8 space-y-5">
          <div class="rounded-2xl bg-white p-5 border border-[color:var(--gold)]/25 grid grid-cols-6 gap-4 items-center" style="box-shadow:var(--shadow-card)">
            <div class="col-span-1 flex flex-col items-center">
              <div class="w-20 h-20 rounded-full grid place-items-center text-cream font-display text-2xl" style="background:var(--gradient-navy)">C</div>
              <div class="font-display text-sm text-[color:var(--navy)] mt-2 text-center">Customer 1</div>
              <div class="text-[10px] uppercase tracking-[0.18em] text-[color:var(--teal)]">Traveller</div>
            </div>
            <div class="col-span-5 relative">
              <div class="font-display text-6xl text-[color:var(--gold)]/40 absolute -top-4 -left-1 leading-none">&quot;</div>
              <p class="font-serif-soft italic text-[15px] text-[color:var(--ink)]/85 pl-6">Excellent travel experience and smooth arrangements.</p>
              <div class="flex gap-0.5 mt-2 pl-6 text-[color:var(--gold)]">&#9733; &#9733; &#9733; &#9733; &#9733;</div>
            </div>
          </div>
          <div class="rounded-2xl bg-white p-5 border border-[color:var(--gold)]/25 grid grid-cols-6 gap-4 items-center" style="box-shadow:var(--shadow-card)">
            <div class="col-span-1 flex flex-col items-center">
              <div class="w-20 h-20 rounded-full grid place-items-center text-cream font-display text-2xl" style="background:var(--gradient-navy)">C</div>
              <div class="font-display text-sm text-[color:var(--navy)] mt-2 text-center">Customer 2</div>
              <div class="text-[10px] uppercase tracking-[0.18em] text-[color:var(--teal)]">Traveller</div>
            </div>
            <div class="col-span-5 relative">
              <div class="font-display text-6xl text-[color:var(--gold)]/40 absolute -top-4 -left-1 leading-none">&quot;</div>
              <p class="font-serif-soft italic text-[15px] text-[color:var(--ink)]/85 pl-6">The trip was well planned and managed professionally.</p>
              <div class="flex gap-0.5 mt-2 pl-6 text-[color:var(--gold)]">&#9733; &#9733; &#9733; &#9733; &#9733;</div>
            </div>
          </div>
          <div class="rounded-2xl bg-white p-5 border border-[color:var(--gold)]/25 grid grid-cols-6 gap-4 items-center" style="box-shadow:var(--shadow-card)">
            <div class="col-span-1 flex flex-col items-center">
              <div class="w-20 h-20 rounded-full grid place-items-center text-cream font-display text-2xl" style="background:var(--gradient-navy)">C</div>
              <div class="font-display text-sm text-[color:var(--navy)] mt-2 text-center">Customer 3</div>
              <div class="text-[10px] uppercase tracking-[0.18em] text-[color:var(--teal)]">Traveller</div>
            </div>
            <div class="col-span-5 relative">
              <div class="font-display text-6xl text-[color:var(--gold)]/40 absolute -top-4 -left-1 leading-none">&quot;</div>
              <p class="font-serif-soft italic text-[15px] text-[color:var(--ink)]/85 pl-6">Hotels, transport and itinerary were very well organized.</p>
              <div class="flex gap-0.5 mt-2 pl-6 text-[color:var(--gold)]">&#9733; &#9733; &#9733; &#9733; &#9733;</div>
            </div>
          </div>
          <div class="rounded-2xl bg-white p-5 border border-[color:var(--gold)]/25 grid grid-cols-6 gap-4 items-center" style="box-shadow:var(--shadow-card)">
            <div class="col-span-1 flex flex-col items-center">
              <div class="w-20 h-20 rounded-full grid place-items-center text-cream font-display text-2xl" style="background:var(--gradient-navy)">C</div>
              <div class="font-display text-sm text-[color:var(--navy)] mt-2 text-center">Customer 4</div>
              <div class="text-[10px] uppercase tracking-[0.18em] text-[color:var(--teal)]">Traveller</div>
            </div>
            <div class="col-span-5 relative">
              <div class="font-display text-6xl text-[color:var(--gold)]/40 absolute -top-4 -left-1 leading-none">&quot;</div>
              <p class="font-serif-soft italic text-[15px] text-[color:var(--ink)]/85 pl-6">Great support from the team throughout the journey.</p>
              <div class="flex gap-0.5 mt-2 pl-6 text-[color:var(--gold)]">&#9733; &#9733; &#9733; &#9733; &#9733;</div>
            </div>
          </div>
          <div class="rounded-2xl bg-white p-5 border border-[color:var(--gold)]/25 grid grid-cols-6 gap-4 items-center" style="box-shadow:var(--shadow-card)">
            <div class="col-span-1 flex flex-col items-center">
              <div class="w-20 h-20 rounded-full grid place-items-center text-cream font-display text-2xl" style="background:var(--gradient-navy)">C</div>
              <div class="font-display text-sm text-[color:var(--navy)] mt-2 text-center">Customer 5</div>
              <div class="text-[10px] uppercase tracking-[0.18em] text-[color:var(--teal)]">Traveller</div>
            </div>
            <div class="col-span-5 relative">
              <div class="font-display text-6xl text-[color:var(--gold)]/40 absolute -top-4 -left-1 leading-none">&quot;</div>
              <p class="font-serif-soft italic text-[15px] text-[color:var(--ink)]/85 pl-6">A memorable holiday with wonderful service and care.</p>
              <div class="flex gap-0.5 mt-2 pl-6 text-[color:var(--gold)]">&#9733; &#9733; &#9733; &#9733; &#9733;</div>
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

    <div class="relative px-20 py-8">
        <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">The Fine Print</div>
        <h2 class="font-display text-4xl text-[color:var(--navy)] mt-1">Terms &amp; Conditions</h2>
        <hr class="gold-rule mt-3" />
        <div class="space-y-4 mt-6">
          <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-4 h-4 text-[color:var(--navy)]" aria-hidden="true"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"></path><path d="M14 2v5a1 1 0 0 0 1 1h5"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
              </div>
              <h3 class="font-display text-base text-[color:var(--navy)]">Booking Amount And Final Payment</h3>
            </div>
            <div class="mt-3 text-[13px] text-[color:var(--ink)]/85 leading-relaxed tc-content">
              <ul>
                <li>We require a minimum deposit as booking amount as per the below chart per person at the time of booking.</li>
                <li>Your service provider will require amounts towards the bookings which amounts are nonrefundable, non-transferable and interest free amounts.</li>
                <li>Final payment for the relevant booking is required no later than 6 weeks prior to departure unless otherwise stated on your invoice.</li>
                <li>Some airfares or services must be paid in full at the time of booking.</li>
              </ul>
            </div>
          </div>
          <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-4 h-4 text-[color:var(--navy)]" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
              </div>
              <h3 class="font-display text-base text-[color:var(--navy)]">Cancellation Policy</h3>
            </div>
            <div class="mt-3 text-[13px] text-[color:var(--ink)]/85 leading-relaxed tc-content">
              <ul>
                <li>In the event of cancellation of tour / travel services due to any avoidable / unavoidable reason/s we must be notified of the same in writing.</li>
                <li>Cancellation charges will be effective from the date we receive advice in writing, and cancellation charges would be as follows:</li>
                <li>45 days prior to arrival: 10% of the Tour / service cost</li>
                <li>15 days prior to arrival: 25% of the Tour / service cost</li>
                <li>07 days prior to arrival: 50% of the Tour / service cost</li>
                <li>48 hours prior to arrival OR No Show: No Refund</li>
              </ul>
            </div>
          </div>
          <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-globe w-4 h-4 text-[color:var(--navy)]" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><path d="M2 12h20"></path></svg>
              </div>
              <h3 class="font-display text-base text-[color:var(--navy)]">Foreign Exchange</h3>
            </div>
            <div class="mt-3 text-[13px] text-[color:var(--ink)]/85 leading-relaxed tc-content">
              <ul>
                <li>Foreign Exchange utilization for the purpose of Land arrangements/ self use will be done from the individual BTQ Quota only.</li>
                <li>Payments will be accepted in accordance with the rules and regulations laid down by Reserve Bank of India.</li>
                <li>You shall be required to provide such KYC documents as may be requested by Travel Tours at the time of booking/ receiving the booking amounts.</li>
              </ul>
            </div>
          </div>
          <div class="rounded-xl bg-white p-4 border border-[color:var(--gold)]/25" style="box-shadow:var(--shadow-card)">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg grid place-items-center" style="background:var(--gradient-gold)">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-pen-line w-4 h-4 text-[color:var(--navy)]" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"></path></svg>
              </div>
              <h3 class="font-display text-base text-[color:var(--navy)]">Booking Amendments</h3>
            </div>
            <div class="mt-3 text-[13px] text-[color:var(--ink)]/85 leading-relaxed tc-content">
              <ul>
                <li>If you wish to transfer from one trip to another or transfer your booking to a third party you must notify us at least 40 days prior to the departure date.</li>
                <li>No charges are applied.</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="mt-6 rounded-xl p-5 text-cream relative overflow-hidden" style="background:var(--gradient-navy)">
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
        <p class="mt-2 font-serif-soft italic text-[13px] text-cream/90 max-w-3xl">By confirming this booking, the guest acknowledges that they have read, understood and accepted the terms and conditions above. FreezeMyTrip reserves the right to amend these terms with prior notice.</p>
      </div>
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
              <div class="font-display text-lg font-bold tracking-wide">iTours Web Services LLP</div>
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
            <h3 class="font-display text-xl text-[color:var(--navy)] mt-1">iTours Web Services LLP</h3>
            <p class="text-[12px] text-[color:var(--ink)]/80 mt-2 leading-relaxed">B-Wings, Teerth Technospace, Mumbai Bangalore Highway, Baner, Pune</p>
            <div class="mt-3 space-y-1.5 text-[12px] text-[color:var(--ink)]/85">
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone w-3.5 h-3.5 text-[color:var(--teal)]" aria-hidden="true">
                  <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384">
                  </path>
                </svg>
                <a href="tel:+919096685012">
                  +91 9096685012
                </a>
              </div>
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail w-3.5 h-3.5 text-[color:var(--teal)]" aria-hidden="true">
                  <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7">
                  </path>
                  <rect x="2" y="4" width="20" height="16" rx="2">
                  </rect>
                </svg> <a href="mailto:pramodvkotkar@gmail.com">
                  pramodvkotkar@gmail.com
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
                </svg> www.itoursdemo.co.in
              </div>
            </div>

            <div class="flex gap-2 mt-4">
              <a class="w-8 h-8 rounded-full grid place-items-center border border-[color:var(--gold)]/40 text-[color:var(--navy)]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram w-3.5 h-3.5" aria-hidden="true"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line></svg>
          </a>
              <a class="w-8 h-8 rounded-full grid place-items-center border border-[color:var(--gold)]/40 text-[color:var(--navy)]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-facebook w-3.5 h-3.5" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
          </a>
              <a class="w-8 h-8 rounded-full grid place-items-center border border-[color:var(--gold)]/40 text-[color:var(--navy)]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-linkedin w-3.5 h-3.5" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle></svg>
          </a>
              <a class="w-8 h-8 rounded-full grid place-items-center border border-[color:var(--gold)]/40 text-[color:var(--navy)]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-youtube w-3.5 h-3.5" aria-hidden="true"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"></path><path d="m10 15 5-3-5-3z"></path></svg>
          </a>
            </div>

           
          </div>
          <div class="rounded-xl p-5 text-cream relative overflow-hidden" style="background:var(--gradient-navy);box-shadow:var(--shadow-card)">
            <div class="text-[10px] uppercase tracking-[0.3em] text-[color:var(--gold)]">Prepared By</div>
            <div class="flex items-center gap-3 mt-3">
              <div class="w-14 h-14 rounded-full grid place-items-center font-display text-2xl text-[color:var(--navy)]" style="background:var(--gradient-gold)">IT</div>
              <div>
                <div class="font-display text-2xl">IT WEB SERVICES</div>
                <div class="text-[11px] uppercase tracking-[0.22em] text-[color:var(--gold)]">Travel Consultant</div>
              </div>
            </div>
            <div class="mt-4 space-y-1.5 text-[12px] text-cream/85">
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone w-3.5 h-3.5 text-[color:var(--gold)]" aria-hidden="true">
                  <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384">
                  </path>
                </svg>
                <a href="tel:+919096685012">
                  +91 90966 85012
                </a>
              </div>
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail w-3.5 h-3.5 text-[color:var(--gold)]" aria-hidden="true">
                  <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7">
                  </path>
                  <rect x="2" y="4" width="20" height="16" rx="2">
                  </rect>
                </svg> <a href="mailto:pramodvkotkar@gmail.com">
                  pramodvkotkar@gmail.com
                </a>
              </div>
            </div>
            <p class="mt-5 font-serif-soft italic text-cream/80 text-[12px]">&quot;Travel is the only thing you buy that makes you richer. Let&#x27;s make yours unforgettable.&quot;</p>
            <div class="absolute -right-8 -bottom-8 w-40 h-40 rounded-full" style="background:oklch(0.78 0.13 78 / 0.18)">
            </div>
          </div>
        </div>
        <div class="mt-10 text-center">
          <div class="font-display text-2xl gold-text inline-block">Bon Voyage</div>
          <div class="text-[10px] uppercase tracking-[0.35em] text-[color:var(--navy)]/60 mt-1">iTours Web Services LLP &middot; Luxury Voyages &middot; Est. 2014</div>
        </div>
      </div>




  </main>
  </body>

</html>
