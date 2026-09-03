<?php
/**
 * Shared Per Person costing helpers for Document / View / Backoffice / WhatsApp.
 *
 * - gqd_pp_group_style_table_html(): Package | Tour Cost | Tax | Tcs | Travel | Total
 *   (backoffice / view)
 * - gqd_render_pp_costing_for_doc(): PP breakdown with Adult / CWB / CWNB / Infant
 *   columns always shown, styled like Group costing (Word document)
 * - gqd_render_pp_costing_whatsapp_text(): plain message lines for WhatsApp
 */

if (!function_exists('get_generic_quotation_data')) {
  include_once __DIR__ . '/generic_quotation_data.php';
}

if (!function_exists('gqd_pp_group_style_table_html')) {
  /**
   * Build Group-column PP costing table HTML (Package / Tour / Tax / TCS / Travel / Total).
   *
   * @param int|string $quotation_id
   * @param array $opts
   * @return string
   */
  function gqd_pp_group_style_table_html($quotation_id, $opts = array())
  {
    $quotation_id = (int) $quotation_id;
    if ($quotation_id <= 0) {
      return '';
    }

    $q = get_generic_quotation_data($quotation_id);
    if (!is_array($q) || empty($q['found'])) {
      return '<p style="margin:10px;font-size:13px;">No per person costing entries found for this quotation.</p>';
    }

    $pp_entries = (isset($q['costing']['computed']['pp_entries']) && is_array($q['costing']['computed']['pp_entries']))
      ? $q['costing']['computed']['pp_entries'] : array();

    if (empty($pp_entries)) {
      return '<p style="margin:10px;font-size:13px;">No per person costing entries found for this quotation.</p>';
    }

    $th = isset($opts['th_style'])
      ? $opts['th_style']
      : 'font-size:16px;font-weight:600;padding:8px 5px;border:1px solid #888888;';
    $td = isset($opts['td_style'])
      ? $opts['td_style']
      : 'font-size:14px;padding:8px 5px;border:1px solid #888888;';
    $table_style = isset($opts['table_style'])
      ? $opts['table_style']
      : 'width:100%;border-collapse:collapse;color:#444;';
    $heading_bg = isset($opts['heading_bg']) ? $opts['heading_bg'] : '#0b2a4a';
    $show_title = !isset($opts['show_title']) || $opts['show_title'];
    $title = isset($opts['title']) ? $opts['title'] : 'COSTING DETAILS';

    $html = '';
    if ($show_title) {
      $html .= '<table width="100%" cellspacing="0" cellpadding="5" style="' . $table_style . ' margin-bottom:0;" role="presentation">';
      $html .= '<tr><td style="text-align:center;border:1px solid #888888;color:#fff;background:' . htmlspecialchars($heading_bg, ENT_QUOTES, 'UTF-8') . ';font-weight:600;">'
        . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</td></tr></table>';
    }

    $html .= '<table class="table no-marg tableTrnasp" width="100%" cellspacing="0" cellpadding="5" style="' . $table_style . '" role="presentation">';
    $html .= '<thead><tr class="table-heading-row">';
    $html .= '<th style="' . $th . '">Package_Type</th>';
    $html .= '<th style="' . $th . '">Tour Cost</th>';
    $html .= '<th style="' . $th . '">Tax</th>';
    $html .= '<th style="' . $th . '">Tcs</th>';
    $html .= '<th style="' . $th . '">TRAVEL/OTHER</th>';
    $html .= '<th style="' . $th . '">Total Cost</th>';
    $html .= '</tr></thead><tbody>';

    foreach ($pp_entries as $pkg) {
      $pkg_name = isset($pkg['package_type']) ? $pkg['package_type'] : 'Package';
      $tour_disp = isset($pkg['tour_cost_display']) ? $pkg['tour_cost_display'] : '0.00';
      $tax_disp = isset($pkg['tax_display']) ? $pkg['tax_display'] : '0.00';
      $tcs_disp = isset($pkg['tcs_display']) ? $pkg['tcs_display'] : '0.00';
      $travel_disp = isset($pkg['travel_display']) ? $pkg['travel_display'] : '0.00';
      $total_disp = isset($pkg['total_amount_display']) ? $pkg['total_amount_display'] : '0.00';
      $before = isset($pkg['before_discount_display']) ? (string) $pkg['before_discount_display'] : '';
      if ($before !== '') {
        $total_cell = htmlspecialchars((string) $total_disp, ENT_QUOTES, 'UTF-8')
          . ' <s>' . htmlspecialchars($before, ENT_QUOTES, 'UTF-8') . '</s>';
      } else {
        $total_cell = htmlspecialchars((string) $total_disp, ENT_QUOTES, 'UTF-8');
      }

      $html .= '<tr>';
      $html .= '<td style="' . $td . '">' . htmlspecialchars((string) $pkg_name, ENT_QUOTES, 'UTF-8') . '</td>';
      $html .= '<td style="' . $td . '">' . htmlspecialchars((string) $tour_disp, ENT_QUOTES, 'UTF-8') . '</td>';
      $html .= '<td style="' . $td . '">' . htmlspecialchars((string) $tax_disp, ENT_QUOTES, 'UTF-8') . '</td>';
      $html .= '<td style="' . $td . '">' . htmlspecialchars((string) $tcs_disp, ENT_QUOTES, 'UTF-8') . '</td>';
      $html .= '<td style="' . $td . '">' . htmlspecialchars((string) $travel_disp, ENT_QUOTES, 'UTF-8') . '</td>';
      $html .= '<td style="' . $td . '">' . $total_cell . '</td>';
      $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    return $html;
  }
}

if (!function_exists('gqd_render_pp_costing_whatsapp_text')) {
  /**
   * Plain WhatsApp message lines for Per Person costing (not a table).
   * Example:
   *   *Adult PP (2) :* INR 15000.00
   *   *Discount :* 5%
   *   *Tcs :* INR 500.00
   *   *Total Price :* INR 45000.00
   * Per-person amounts already include tax, so Tax is not listed again.
   *
   * @param int|string $quotation_id
   * @param array $opts optional: first_only (bool, default false)
   * @return string
   */
  function gqd_render_pp_costing_whatsapp_text($quotation_id, $opts = array())
  {
    $quotation_id = (int) $quotation_id;
    if ($quotation_id <= 0) {
      return '';
    }

    $q = get_generic_quotation_data($quotation_id);
    $pp_entries = (is_array($q) && isset($q['costing']['computed']['pp_entries']) && is_array($q['costing']['computed']['pp_entries']))
      ? $q['costing']['computed']['pp_entries'] : array();

    if (empty($pp_entries)) {
      return '';
    }

    $first_only = !empty($opts['first_only']);
    if ($first_only) {
      $pp_entries = array($pp_entries[0]);
    }

    $text = '';
    $multi = (count($pp_entries) > 1);

    foreach ($pp_entries as $pkg) {
      if ($multi) {
        $pkg_name = isset($pkg['package_type']) ? trim((string) $pkg['package_type']) : 'Package';
        if ($pkg_name === '') {
          $pkg_name = 'Package';
        }
        $text .= '*' . $pkg_name . "*\n";
      }

      $counts = isset($pkg['pax_counts']) && is_array($pkg['pax_counts']) ? $pkg['pax_counts'] : array();
      $adult_n = isset($counts['adult']) ? (int) $counts['adult'] : 0;
      $cwb_n = isset($counts['cwb']) ? (int) $counts['cwb'] : 0;
      $cwnb_n = isset($counts['cwnb']) ? (int) $counts['cwnb'] : 0;
      $infant_n = isset($counts['infant']) ? (int) $counts['infant'] : 0;

      $total = isset($pkg['total']) && is_array($pkg['total']) ? $pkg['total'] : array();
      $discount = isset($pkg['discount']) && is_array($pkg['discount']) ? $pkg['discount'] : array();

      if ($adult_n > 0) {
        $amt = isset($total['adult_display']) ? $total['adult_display'] : '0.00';
        $text .= '*Adult PP (' . $adult_n . ') :* ' . $amt . "\n";
      }
      if ($cwb_n > 0) {
        $amt = isset($total['cwb_display']) ? $total['cwb_display'] : '0.00';
        $text .= '*CWB PP (' . $cwb_n . ') :* ' . $amt . "\n";
      }
      if ($cwnb_n > 0) {
        $amt = isset($total['cwnb_display']) ? $total['cwnb_display'] : '0.00';
        $text .= '*CWNB PP (' . $cwnb_n . ') :* ' . $amt . "\n";
      }
      if ($infant_n > 0) {
        $amt = isset($total['infant_display']) ? $total['infant_display'] : '0.00';
        $text .= '*Infant PP (' . $infant_n . ') :* ' . $amt . "\n";
      }

      // Discount: prefer adult, else first pax type with a non-zero value
      $disc_disp = '';
      $disc_val = 0.0;
      $disc_keys = array('adult', 'cwb', 'cwnb', 'infant');
      foreach ($disc_keys as $dk) {
        $v = isset($discount[$dk]) ? (float) $discount[$dk] : 0.0;
        if ($v > 0) {
          $disc_val = $v;
          $disc_disp = isset($discount[$dk . '_display']) ? (string) $discount[$dk . '_display'] : (string) $v;
          break;
        }
      }
      if ($disc_val > 0 && $disc_disp !== '') {
        $text .= '*Discount :* ' . $disc_disp . "\n";
      }

      $tcs_total = isset($pkg['tcs_total']) ? (float) $pkg['tcs_total'] : 0.0;
      if ($tcs_total > 0) {
        $tcs_disp = isset($pkg['tcs_display']) ? $pkg['tcs_display'] : '0.00';
        $text .= '*Tcs :* ' . $tcs_disp . "\n";
      }

      $total_disp = isset($pkg['total_amount_display']) ? $pkg['total_amount_display'] : '0.00';
      $text .= '*Total Price :* ' . $total_disp . "\n\n";
    }

    return $text;
  }
}

if (!function_exists('gqd_render_pp_costing_for_doc')) {
  /**
   * Word/Document PP costing: same visual design as Group costing table,
   * but keeps Adult / CWB / CWNB / Infant columns (never hidden).
   * Cost component rows (Flight/Cruise/Misc/…) still hide when all amounts <= 0.
   */
  function gqd_render_pp_costing_for_doc($quotation_id, $opts = array())
  {
    $quotation_id = (int) $quotation_id;
    if ($quotation_id <= 0) {
      return;
    }

    $q = get_generic_quotation_data($quotation_id);
    $pp_entries = (is_array($q) && isset($q['costing']['computed']['pp_entries']) && is_array($q['costing']['computed']['pp_entries']))
      ? $q['costing']['computed']['pp_entries'] : array();

    if (empty($pp_entries) || !function_exists('gqd_render_pp_entries_table')) {
      echo '<p style="margin:10px;font-size:13px;">No per person costing entries found for this quotation.</p>';
      return;
    }

    // Simple document table — no navy/cream highlights (like Group costing doc)
    echo '<div class="travsportInfoBlock1">';
    echo '<div class="transportDetails_costing package_costing">';
    echo '<div class="">';
    gqd_render_pp_entries_table($pp_entries, array_merge(array(
      'table_class' => 'table no-marg tableTrnasp',
      'table_style' => 'width:100%;border-collapse:collapse;font-size:14px;',
      'force_all_pax_cols' => true,
      'simple' => true,
      'symbol' => '&#8377;',
    ), is_array($opts) ? $opts : array()));
    echo '</div></div></div>';
  }
}
