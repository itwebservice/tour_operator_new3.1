<?php

/**
 * Renders package-quotation email HTML using Option-1 design always
 * (independent of Company Profile quotation format).
 * Section toggles: header, price_structure, itinerary, inclusion_exclusion, terms_conditions, footer.
 */
if (!function_exists('render_quotation_email_pdf_style')) {

	function qes_esc($v)
	{
		return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
	}

	function qes_nv($v, $f = '')
	{
		return ($v !== null && $v !== '') ? $v : $f;
	}

	function qes_wrap_section($key, $html, $sectioned)
	{
		if (trim(strip_tags($html)) === '') {
			return '';
		}
		if (!$sectioned) {
			return $html;
		}
		return '<div class="preview-section-block" data-section="' . qes_esc($key) . '">' . $html . '</div>';
	}

	function qes_list_items($html, $default = '')
	{
		$html = (string) $html;
		if (trim(strip_tags($html)) === '') {
			return $default !== '' ? array($default) : array();
		}
		$items = array();
		if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $html, $m)) {
			foreach ($m[1] as $chunk) {
				$t = trim(strip_tags(html_entity_decode($chunk, ENT_QUOTES, 'UTF-8')));
				if ($t !== '') {
					$items[] = $t;
				}
			}
		}
		if (empty($items)) {
			$plain = trim(strip_tags(html_entity_decode($html, ENT_QUOTES, 'UTF-8')));
			$parts = preg_split('/\r\n|\r|\n|•/', $plain);
			foreach ((array) $parts as $p) {
				$p = trim($p);
				if ($p !== '' && strlen($p) > 3) {
					$items[] = $p;
				}
			}
		}
		if (empty($items) && $default !== '') {
			$items[] = $default;
		}
		return $items;
	}

	function qes_email_fonts_block()
	{
		return '<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">';
	}

	function qes_render_hero_block($q)
	{
		$hero = $q['hero'];
		$ov = $q['tour_overview'];
		$navy = '#0d1e3b';
		$gold = '#e5ac4c';
		$cream = '#f5f0e8';
		$company = qes_nv($hero['company_name'], 'iTours');
		$dest = qes_nv($ov['destination'], qes_nv($hero['tour_name'], 'Tour'));
		$code = qes_nv($hero['quotation_code'], '');
		$client = qes_nv($ov['client_name'], qes_nv($hero['client_name'], 'Guest'));
		$guests = qes_nv($ov['guest_count'], '');
		$from = qes_nv($ov['travel_from'], '');
		$to = qes_nv($ov['travel_to'], '');
		$duration = qes_nv($hero['duration_label'], qes_nv($ov['duration_label'], ''));

		$gallery = '';
		$imgs = isset($q['gallery_images']) && is_array($q['gallery_images']) ? $q['gallery_images'] : array();
		if (!empty($imgs)) {
			$gallery = '<table width="100%" cellpadding="0" cellspacing="6" style="margin-top:20px;"><tr>';
			for ($i = 0; $i < 4; $i++) {
				$src = isset($imgs[$i]) ? $imgs[$i] : $imgs[0];
				if (strpos($src, 'http') !== 0) {
					$src = BASE_URL . ltrim(str_replace('\\', '/', $src), '/');
				}
				$gallery .= '<td width="25%" style="padding:0;"><img src="' . qes_esc($src) . '" alt="" width="100%" height="80" style="display:block;border-radius:6px;border:1px solid ' . $gold . ';object-fit:cover;" /></td>';
			}
			$gallery .= '</tr></table>';
		}

		$travellers = ($guests !== '') ? qes_esc($guests) . ' Travellers &middot; ' : '';
		$dates = qes_esc($from) . ' &ndash; ' . qes_esc($to);

		return '<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:linear-gradient(180deg,#0d1e3b 0%,#051028 70%,#000418 100%);border-radius:8px;overflow:hidden;margin-bottom:8px;">'
			. '<tr><td style="padding:28px 24px 24px;font-family:Inter,Arial,sans-serif;">'
			. '<table width="100%" cellpadding="0" cellspacing="0"><tr>'
			. '<td style="color:' . $cream . ';font-family:\'Playfair Display\',Georgia,serif;font-size:18px;font-weight:700;">' . qes_esc($company) . '<div style="font-size:9px;letter-spacing:0.25em;text-transform:uppercase;opacity:0.8;margin-top:2px;">Luxury Voyages</div></td>'
			. '<td align="right" style="color:' . $cream . ';font-size:10px;letter-spacing:0.3em;text-transform:uppercase;">QUOTATION<div style="font-family:\'Playfair Display\',Georgia,serif;font-size:16px;color:' . $gold . ';letter-spacing:0;margin-top:4px;">' . qes_esc($code) . '</div></td>'
			. '</tr></table>'
			. '<div style="margin:24px 0 12px;"><span style="display:inline-block;width:48px;height:1px;background:' . $gold . ';vertical-align:middle;"></span> <span style="color:' . $gold . ';font-size:11px;letter-spacing:0.4em;text-transform:uppercase;margin-left:8px;">Exclusive Travel Proposal</span></div>'
			. '<div style="font-family:\'Playfair Display\',Georgia,serif;font-size:42px;line-height:1;color:' . $cream . ';font-weight:800;">' . qes_esc($dest) . '</div>'
			. '<div style="font-family:\'Playfair Display\',Georgia,serif;font-size:22px;font-style:italic;color:' . $gold . ';margin-top:4px;">a curated escape</div>'
			. '<p style="color:rgba(245,240,232,0.85);font-size:15px;line-height:1.5;margin:16px 0 0;max-width:520px;">Discover unforgettable experiences — breathtaking landscapes, vibrant cultures, and moments that stay with you forever.</p>'
			. $gallery
			. '<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;border-top:1px solid rgba(229,172,76,0.3);padding-top:20px;"><tr>'
			. '<td style="vertical-align:bottom;">'
			. '<div style="font-size:10px;letter-spacing:0.35em;text-transform:uppercase;color:' . $gold . ';">Prepared Exclusively For</div>'
			. '<div style="font-family:\'Playfair Display\',Georgia,serif;font-size:28px;color:' . $cream . ';margin-top:6px;">' . qes_esc($client) . '</div>'
			. '<div style="color:rgba(245,240,232,0.7);font-size:13px;margin-top:6px;font-style:italic;">' . $travellers . $dates . '</div>'
			. '</td><td align="right" style="vertical-align:bottom;color:rgba(245,240,232,0.8);">'
			. '<div style="font-size:10px;letter-spacing:0.3em;text-transform:uppercase;color:' . $gold . ';">' . qes_esc($duration) . '</div>'
			. '<div style="font-family:\'Playfair Display\',Georgia,serif;font-size:22px;margin-top:6px;color:' . $cream . ';">Package Tour</div>'
			. '</td></tr></table>'
			. '</td></tr></table>'
			. '<p style="font-family:Inter,Arial,sans-serif;color:#444;font-size:14px;line-height:1.6;margin:16px 0 8px;">Hi ' . qes_esc($client) . ',<br>Greetings from <strong>' . qes_esc($company) . '</strong>.<br>Thank you for your query. Below are your package details.</p>';
	}

	function qes_section_bar($title, $subtitle = '')
	{
		$gold = '#e5ac4c';
		$navy = '#0d1e3b';
		$sub = $subtitle !== '' ? '<div style="font-size:11px;color:rgba(245,240,232,0.7);margin-top:4px;">' . qes_esc($subtitle) . '</div>' : '';
		return '<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top:20px;background:' . $navy . ';border-radius:8px 8px 0 0;"><tr><td style="padding:14px 18px;">'
			. '<div style="font-family:\'Playfair Display\',Georgia,serif;font-size:16px;color:#f5f0e8;font-weight:600;">' . qes_esc($title) . '</div>' . $sub
			. '</td></tr></table>';
	}

	function qes_card_open()
	{
		return '<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border:1px solid rgba(229,172,76,0.35);border-top:0;border-radius:0 0 8px 8px;background:#fff;margin-bottom:4px;"><tr><td style="padding:16px 18px;">';
	}

	function qes_card_close()
	{
		return '</td></tr></table>';
	}

	function qes_render_costing_block($cost)
	{
		$navy = '#0d1e3b';
		$gold = '#e5ac4c';
		$type = isset($cost['costing_type_label']) ? strtolower(trim($cost['costing_type_label'])) : '';
		$is_pp = ($type === 'per person');

		$html = qes_section_bar('Costing Details', 'All values in INR · ' . ($is_pp ? 'Per Person' : 'Per Package'));
		$html .= '<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border:1px solid rgba(229,172,76,0.35);border-top:0;border-radius:0 0 8px 8px;overflow:hidden;margin-bottom:4px;">';

		if (!$is_pp) {
			$rows = isset($cost['computed']['group']) ? $cost['computed']['group'] : array();
			if (empty($rows)) {
				$rows = array(array('package_type' => 'Package', 'tour_cost_display' => '0', 'tax_display' => '0', 'tcs_display' => '0', 'travel_display' => '0', 'total_display' => '0'));
			}
			$html .= '<tr style="background:' . $navy . ';color:#fff;">'
				. '<td style="padding:10px 12px;font-size:10px;letter-spacing:0.15em;text-transform:uppercase;">Package</td>'
				. '<td align="right" style="padding:10px 8px;font-size:10px;letter-spacing:0.15em;text-transform:uppercase;">Tour Cost</td>'
				. '<td align="right" style="padding:10px 8px;font-size:10px;letter-spacing:0.15em;text-transform:uppercase;">Tax</td>'
				. '<td align="right" style="padding:10px 8px;font-size:10px;letter-spacing:0.15em;text-transform:uppercase;">TCS</td>'
				. '<td align="right" style="padding:10px 8px;font-size:10px;letter-spacing:0.15em;text-transform:uppercase;">Travel</td>'
				. '<td align="right" style="padding:10px 12px;font-size:10px;letter-spacing:0.15em;text-transform:uppercase;color:' . $gold . ';">Grand Total</td></tr>';
			$i = 0;
			foreach ($rows as $row) {
				$bg = ($i % 2 === 1) ? '#faf6ee' : '#ffffff';
				$tax = '0.00';
				if (!empty($row['tax_display']) && preg_match('/INR\s*([\d,\.]+)/i', $row['tax_display'], $m)) {
					$tax = $m[1];
				}
				$html .= '<tr style="background:' . $bg . ';border-top:1px solid rgba(229,172,76,0.2);">'
					. '<td style="padding:12px;font-family:\'Playfair Display\',Georgia,serif;color:' . $navy . ';font-size:14px;">' . qes_esc(qes_nv($row['package_type'], 'Package')) . '</td>'
					. '<td align="right" style="padding:12px 8px;font-size:13px;">&#8377; ' . qes_esc(qes_nv($row['tour_cost_display'], '0')) . '</td>'
					. '<td align="right" style="padding:12px 8px;font-size:13px;">INR ' . qes_esc($tax) . '</td>'
					. '<td align="right" style="padding:12px 8px;font-size:13px;">&#8377; ' . qes_esc(qes_nv($row['tcs_display'], '0')) . '</td>'
					. '<td align="right" style="padding:12px 8px;font-size:13px;">&#8377; ' . qes_esc(qes_nv($row['travel_display'], '0')) . '</td>'
					. '<td align="right" style="padding:12px;font-family:\'Playfair Display\',Georgia,serif;font-size:16px;color:' . $navy . ';font-weight:700;">&#8377; ' . qes_esc(qes_nv($row['total_display'], '0')) . '</td></tr>';
				$i++;
			}
		} else {
			$gt = isset($cost['computed']['grand_total_display']) ? $cost['computed']['grand_total_display'] : '0';
			$html .= '<tr><td style="padding:16px;font-family:\'Playfair Display\',Georgia,serif;font-size:18px;color:' . $navy . ';">Grand Total: <span style="color:' . $gold . ';">' . qes_esc($gt) . '</span></td></tr>';
		}
		$html .= '</table>';
		$html .= '<p style="font-size:11px;color:#888;font-style:italic;margin:6px 0 0;">* Prices indicative and subject to availability at confirmation.</p>';
		return $html;
	}

	function qes_render_itinerary_block($q)
	{
		$html = '';
		$navy = '#0d1e3b';
		$gold = '#e5ac4c';

		if (!empty($q['hotels'])) {
			$html .= qes_section_bar('Accommodation Details');
			$html .= qes_card_open();
			foreach ($q['hotels'] as $h) {
				$html .= '<div style="border-bottom:1px solid #eee;padding:12px 0;">'
					. '<div style="font-family:\'Playfair Display\',Georgia,serif;font-size:16px;color:' . $navy . ';font-weight:600;">' . qes_esc(qes_nv($h['hotel_name'], 'Hotel')) . '</div>'
					. '<div style="font-size:13px;color:#666;margin-top:4px;">' . qes_esc(qes_nv($h['hotel_city'], '')) . ' &middot; ' . qes_esc(qes_nv($h['check_in'], '')) . ' to ' . qes_esc(qes_nv($h['check_out'], '')) . '</div>'
					. '<div style="font-size:12px;color:#888;margin-top:4px;">' . qes_esc(qes_nv($h['room_category'], '')) . ' &middot; ' . qes_esc(qes_nv($h['meal_plan'], '')) . '</div>'
					. '</div>';
			}
			$html .= qes_card_close();
		}

		if (!empty($q['itinerary'])) {
			$html .= qes_section_bar('Tour Itinerary');
			$html .= qes_card_open();
			foreach ($q['itinerary'] as $idx => $day) {
				$dn = isset($day['day_number']) ? $day['day_number'] : ($idx + 1);
				$attr = qes_nv($day['special_attraction'], qes_nv($day['attraction'], ''));
				$program = qes_nv($day['detailed_programme'], qes_nv($day['day_wise_program'], ''));
				$stay = qes_nv($day['overnight_stay'], qes_nv($day['stay'], ''));
				$html .= '<div style="border-bottom:1px solid #eee;padding:14px 0;">'
					. '<div style="font-size:11px;letter-spacing:0.2em;text-transform:uppercase;color:' . $gold . ';">Day ' . qes_esc($dn) . '</div>'
					. '<div style="font-family:\'Playfair Display\',Georgia,serif;font-size:15px;color:' . $navy . ';margin-top:4px;font-weight:600;">' . qes_esc($attr) . '</div>';
				if ($program !== '') {
					$html .= '<div style="font-size:13px;color:#555;margin-top:8px;line-height:1.5;">' . $program . '</div>';
				}
				$html .= '<div style="font-size:12px;color:#777;margin-top:8px;"><strong>Stay:</strong> ' . qes_esc($stay) . ' &nbsp;|&nbsp; <strong>Meals:</strong> ' . qes_esc(qes_nv($day['meal_plan'], '')) . '</div></div>';
			}
			$html .= qes_card_close();
		}

		if (!empty($q['vehicles'])) {
			$html .= qes_section_bar('Transportation');
			$html .= qes_card_open() . '<ul style="margin:0;padding-left:18px;color:#555;">';
			foreach ($q['vehicles'] as $v) {
				$start = qes_nv($v['date'], '');
				$end = !empty($v['end_date_raw']) && function_exists('get_date_user') ? get_date_user($v['end_date_raw']) : qes_nv($v['end_date_raw'], '');
				$line = qes_nv($v['vehicle_name'], '') . ' | ' . $start . ' - ' . $end . ' | ' . qes_nv($v['pickup'], '') . ' to ' . qes_nv($v['drop'], '');
				$html .= '<li style="margin-bottom:8px;font-size:13px;">' . qes_esc($line) . '</li>';
			}
			$html .= '</ul>' . qes_card_close();
		}

		return $html;
	}

	function qes_render_inclusion_block($incx)
	{
		$navy = '#0d1e3b';
		$gold = '#e5ac4c';
		$inc_items = qes_list_items(isset($incx['included']) ? $incx['included'] : '', 'Inclusions will be shared as per final quotation.');
		$exc_items = qes_list_items(isset($incx['excluded']) ? $incx['excluded'] : '', 'Standard exclusions apply.');

		$html = '<table width="100%" cellpadding="0" cellspacing="8" role="presentation"><tr>';
		$html .= '<td width="50%" valign="top" style="border:1px solid rgba(229,172,76,0.35);border-radius:8px;background:#fff;padding:16px;">'
			. '<div style="font-family:\'Playfair Display\',Georgia,serif;font-size:18px;color:' . $navy . ';margin-bottom:8px;">What\'s Included</div>'
			. '<div style="height:2px;background:' . $gold . ';width:40px;margin-bottom:12px;"></div><ul style="margin:0;padding-left:16px;font-size:12px;color:#444;">';
		foreach ($inc_items as $item) {
			$html .= '<li style="margin-bottom:6px;">' . qes_esc($item) . '</li>';
		}
		$html .= '</ul></td>';
		$html .= '<td width="50%" valign="top" style="border:1px solid rgba(229,172,76,0.35);border-radius:8px;background:#fff;padding:16px;">'
			. '<div style="font-family:\'Playfair Display\',Georgia,serif;font-size:18px;color:' . $navy . ';margin-bottom:8px;">What\'s Excluded</div>'
			. '<div style="height:2px;background:' . $navy . ';width:40px;margin-bottom:12px;"></div><ul style="margin:0;padding-left:16px;font-size:12px;color:#444;">';
		foreach ($exc_items as $item) {
			$html .= '<li style="margin-bottom:6px;">' . qes_esc($item) . '</li>';
		}
		$html .= '</ul></td></tr></table>';
		return $html;
	}

	function qes_render_terms_block($terms)
	{
		$body = qes_nv($terms['terms_and_conditions'], 'Standard terms and conditions apply.');
		return qes_section_bar('Terms &amp; Conditions') . qes_card_open()
			. '<div style="font-size:13px;color:#555;line-height:1.6;">' . $body . '</div>' . qes_card_close();
	}

	function qes_render_footer_block($ty, $quotation_link)
	{
		$gold = '#e5ac4c';
		$navy = '#0d1e3b';
		$name = qes_nv($ty['company_name'], '');
		$contact = qes_nv($ty['company_contact'], '');
		return qes_section_bar('View Quotation Online') . qes_card_open()
			. '<p style="margin:0 0 12px;"><a href="' . qes_esc($quotation_link) . '" style="color:' . $gold . ';font-weight:600;text-decoration:none;font-size:15px;">Open Full Quotation &rarr;</a></p>'
			. '<p style="margin:0;font-size:13px;color:#555;">Please contact for more details: <strong style="color:' . $navy . ';">' . qes_esc($name) . '</strong> ' . qes_esc($contact) . '</p>'
			. '<p style="margin:12px 0 0;font-size:13px;color:#666;">Thank you.</p>'
			. qes_card_close();
	}

	function render_quotation_email_pdf_style($quotation_id, $options = array(), $sectioned = true, $quotation_link = '')
	{
		// Package quotation emails always use Option-1 design,
		// regardless of Company Profile quotation format setting.
		$data_path = dirname(__FILE__) . '/../../app_settings/print_html/quotation_html/generic_quotation_data.php';
		if (!file_exists($data_path)) {
			return '';
		}
		include_once $data_path;

		if (!function_exists('get_generic_quotation_data')) {
			return '';
		}

		$q = get_generic_quotation_data($quotation_id);
		if (empty($q['found'])) {
			return '';
		}

		if ($quotation_link === '') {
			$quotation_link = BASE_URL . 'model/package_tour/quotation/single_quotation.php?quotation=' . base64_encode($quotation_id);
		}

		$hero = qes_render_hero_block($q);
		$price = qes_render_costing_block($q['costing']);
		$itin = qes_render_itinerary_block($q);
		$inc = qes_render_inclusion_block($q['inclusion_exclusion']);
		$terms = qes_render_terms_block($q['terms_conditions']);
		$footer = qes_render_footer_block($q['thank_you'], $quotation_link);

		if ($sectioned) {
			$body = qes_wrap_section('header', $hero, true)
				. qes_wrap_section('price_structure', $price, true)
				. qes_wrap_section('itinerary', $itin, true)
				. qes_wrap_section('inclusion_exclusion', $inc, true)
				. qes_wrap_section('terms_conditions', $terms, true)
				. qes_wrap_section('footer', $footer, true);
		} else {
			$body = $hero;
			if (in_array('price_structure', $options)) {
				$body .= $price;
			}
			if (in_array('itinerary', $options)) {
				$body .= $itin;
			}
			if (in_array('inclusion_exclusion', $options)) {
				$body .= $inc;
			}
			if (in_array('terms_conditions', $options)) {
				$body .= $terms;
			}
			$body .= $footer;
		}

		return '<div class="quotation-email-pdf-style">' . qes_email_fonts_block() . $body . '</div>';
	}
}
