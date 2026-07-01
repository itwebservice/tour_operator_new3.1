<?php

if (!function_exists('sq_e')) {
  function sq_e($value)
  {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
  }
}

if (!function_exists('sq_page_num')) {
  function sq_page_num($page_no)
  {
    return str_pad((int) $page_no, 2, '0', STR_PAD_LEFT);
  }
}

if (!function_exists('sq_company_name')) {
  function sq_company_name()
  {
    global $hero, $app_name;
    if (!empty($hero['company_name'])) {
      return $hero['company_name'];
    }
    return !empty($app_name) ? $app_name : 'iTours Web Services LLP';
  }
}

if (!function_exists('sq_quotation_code')) {
  function sq_quotation_code()
  {
    global $hero;
    return !empty($hero['quotation_code']) ? $hero['quotation_code'] : '';
  }
}

if (!function_exists('sq_destination_name')) {
  function sq_destination_name()
  {
    global $hero, $ov;
    if (!empty($ov['destination'])) {
      return $ov['destination'];
    }
    if (!empty($hero['tour_name'])) {
      return $hero['tour_name'];
    }
    return '';
  }
}

if (!function_exists('sq_render_hero_top_bar')) {
  function sq_render_hero_top_bar()
  {
    $company = sq_e(sq_company_name());
    $quotation = sq_e(sq_quotation_code());
?>
      <div class="relative z-10 px-10 pt-8 flex items-start justify-between">
        <div class="flex items-center gap-2.5">
          <div class="relative w-10 h-10 rounded-full grid place-items-center" style="background:var(--gradient-gold)">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-compass w-5 h-5 text-[color:var(--navy)]" aria-hidden="true">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="m16.24 7.76-1.804 5.411a2 2 0 0 1-1.265 1.265L7.76 16.24l1.804-5.411a2 2 0 0 1 1.265-1.265z"></path>
            </svg>
          </div>
          <div class="leading-tight text-cream">
            <div class="font-display text-lg font-bold tracking-wide"><?= $company ?></div>
            <div class="text-[9px] uppercase tracking-[0.25em] opacity-80">Luxury Voyages</div>
          </div>
        </div>
        <div class="text-right text-cream/90">
          <div class="text-[10px] uppercase tracking-[0.3em] opacity-80">Quotation</div>
          <div class="font-display text-base text-[color:var(--gold)]"><?= $quotation ?></div>
        </div>
      </div>
<?php
  }
}

if (!function_exists('sq_render_page_header')) {
  function sq_render_page_header($section_label, $page_no)
  {
    $company = sq_e(sq_company_name());
    $quotation = sq_e(sq_quotation_code());
    $destination = sq_e(sq_destination_name());
    $section = sq_e($section_label);
    $page = sq_page_num($page_no);
?>
      <div class="header-strip">
        <div class="px-10 py-4 flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <div class="relative w-10 h-10 rounded-full grid place-items-center" style="background:var(--gradient-gold)">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-compass w-5 h-5 text-[color:var(--navy)]" aria-hidden="true">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="m16.24 7.76-1.804 5.411a2 2 0 0 1-1.265 1.265L7.76 16.24l1.804-5.411a2 2 0 0 1 1.265-1.265z"></path>
              </svg>
            </div>
            <div class="leading-tight text-cream">
              <div class="font-display text-lg font-bold tracking-wide"><?= $company ?></div>
              <div class="text-[9px] uppercase tracking-[0.25em] opacity-80">Luxury Voyages</div>
            </div>
          </div>
          <div class="text-cream text-right">
            <div class="text-[10px] uppercase tracking-[0.3em] opacity-70"><?= $section ?></div>
            <div class="font-display text-sm"><?= $quotation ?> &middot; <?= $destination ?></div>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-[10px] uppercase tracking-[0.25em] opacity-70 text-cream">Page</span>
            <span class="font-display text-2xl text-[color:var(--gold)]"><?= sq_e($page) ?></span>
          </div>
        </div>
      </div>
<?php
  }
}

if (!function_exists('sq_render_page_footer')) {
  function sq_render_page_footer($page_no, $total_pages = null, $extra_class = '')
  {
    global $sq_total_pages;
    if ($total_pages === null) {
      $total_pages = isset($sq_total_pages) ? (int) $sq_total_pages : 9;
    }
    $company = sq_e(sq_company_name());
    $page = sq_page_num($page_no);
    $total = sq_page_num($total_pages);
    $class = trim(($extra_class !== '' ? $extra_class . ' ' : '') . 'absolute bottom-0 left-0 right-0 px-10 py-3 flex items-center justify-between text-[10px] uppercase tracking-[0.25em] text-[color:var(--navy)]/60 border-t border-[color:var(--gold)]/30 bg-cream');
?>
      <div class="<?= sq_e($class) ?>">
        <span><?= $company ?> &middot; Luxury Voyages</span>
        <span class="text-[color:var(--gold)]">&#10022; &#10022; &#10022;</span>
        <span><?= sq_e($page) ?> / <?= sq_e($total) ?></span>
      </div>
<?php
  }
}
