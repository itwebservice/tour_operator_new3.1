<?php

if (!function_exists('qeh_esc')) {
	function qeh_esc($value)
	{
		return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
	}
}

if (!function_exists('qeh_accent_color')) {
	function qeh_accent_color()
	{
		global $theme_color;
		return !empty($theme_color) ? $theme_color : '#009898';
	}
}

if (!function_exists('qeh_section_heading')) {
	function qeh_section_heading($title)
	{
		$accent = qeh_accent_color();
		return '<table width="100%" cellspacing="0" cellpadding="8" style="color:#888888;border:1px solid #888888;margin:16px 0 0;border-collapse:collapse;" role="presentation">'
			. '<tr><td style="text-align:center;color:#fff;background:' . $accent . ';font-weight:600;font-size:13px;letter-spacing:0.4px;text-transform:uppercase;">'
			. qeh_esc($title) . '</td></tr></table>';
	}
}

if (!function_exists('qeh_kv_table')) {
	function qeh_kv_table($rows)
	{
		$html = '<table width="100%" cellspacing="0" cellpadding="6" style="color:#888888;border:1px solid #888888;border-top:0;border-collapse:collapse;margin:0 0 12px;background:#fff;" role="presentation">';
		foreach ($rows as $row) {
			if (isset($row['full'])) {
				$html .= '<tr><td colspan="2" style="border:1px solid #888888;padding:10px;line-height:1.6;">' . $row['full'] . '</td></tr>';
				continue;
			}
			$label = isset($row[0]) ? $row[0] : '';
			$value = isset($row[1]) ? $row[1] : '';
			$html .= '<tr>'
				. '<td style="border:1px solid #888888;width:34%;font-weight:600;background:#f7fbfb;vertical-align:top;">' . qeh_esc($label) . '</td>'
				. '<td style="border:1px solid #888888;vertical-align:top;">' . $value . '</td>'
				. '</tr>';
		}
		$html .= '</table>';
		return $html;
	}
}

if (!function_exists('qeh_rich_block')) {
	function qeh_rich_block($title, $content)
	{
		$content = trim((string) $content);
		if ($content === '') {
			return '';
		}
		if (!function_exists('quotation_rich_text_for_email_html')) {
			include_once __DIR__ . '/../../../../../model/package_tour/quotation/quotation_rich_text_helpers.php';
		}
		if (function_exists('quotation_rich_text_for_email_html') && strpos($content, '<') !== false) {
			$content = quotation_rich_text_for_email_html($content);
		}
		$wrap_style = function_exists('quotation_rich_text_word_wrapper_style')
			? quotation_rich_text_word_wrapper_style()
			: 'line-height:1.65;word-wrap:break-word;overflow-wrap:break-word;';
		return qeh_section_heading($title) . qeh_kv_table(array(array('full' => '<div style="color:#555;' . $wrap_style . '">' . $content . '</div>')));
	}
}

if (!function_exists('qeh_wrap_preview_section')) {
	function qeh_wrap_preview_section($section_key, $html)
	{
		if (trim(strip_tags($html)) === '') {
			return '';
		}
		return '<div class="preview-section-block" data-section="' . qeh_esc($section_key) . '">' . $html . '</div>';
	}
}

if (!function_exists('qeh_greeting_block')) {
	function qeh_greeting_block($app_name, $guest_name = 'Guest')
	{
		$guest_name = trim((string) $guest_name);
		if ($guest_name === '') {
			$guest_name = 'Guest';
		}
		return '<div style="color:#444;line-height:1.65;margin-bottom:4px;font-size:14px;">'
			. '<p style="margin:0 0 8px;">Hi ' . qeh_esc($guest_name) . ',</p>'
			. '<p style="margin:0 0 8px;">Greetings from <strong>' . qeh_esc($app_name) . '</strong></p>'
			. '<p style="margin:0 0 4px;">Thank you for your query with us. As per your requirements, following are the package details.</p>'
			. '</div>';
	}
}
