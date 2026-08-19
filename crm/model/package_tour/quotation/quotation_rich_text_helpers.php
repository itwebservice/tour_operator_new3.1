<?php
/**
 * Helpers for quotation inclusions/exclusions rich HTML from WYSIWYG editors.
 * Preserves line breaks and list structure for email, WhatsApp, Word, and PDF.
 */

if (!function_exists('quotation_rich_text_clean_mso_html')) {
	/**
	 * Strip Microsoft Word paste noise while keeping paragraph/list structure.
	 */
	function quotation_rich_text_clean_mso_html($html)
	{
		$html = (string) $html;
		if ($html === '') {
			return '';
		}

		$html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$html = preg_replace('/<!--\[if[^\]]*\]>.*?<!\[endif\]-->/is', '', $html);
		$html = preg_replace('/<!--.*?-->/s', '', $html);
		$html = preg_replace('/<\/?o:p[^>]*>/i', '', $html);
		$html = preg_replace('/<\/?w:[^>]+>/i', '', $html);
		$html = preg_replace('/<\/?m:[^>]+>/i', '', $html);
		$html = preg_replace('/<img[^>]*>/i', '', $html);
		$html = str_replace(array("\xC2\xA0", '&nbsp;'), ' ', $html);

		return trim($html);
	}
}

if (!function_exists('quotation_rich_text_is_empty')) {
	function quotation_rich_text_is_empty($html)
	{
		$v = trim((string) $html);
		if ($v === '' || $v === ' ' || $v === '<div><br></div>' || $v === '<br>' || $v === '<p></p>') {
			return true;
		}
		return trim(strip_tags(html_entity_decode($v, ENT_QUOTES | ENT_HTML5, 'UTF-8'))) === '';
	}
}

if (!function_exists('quotation_rich_text_sanitize_html')) {
	function quotation_rich_text_sanitize_html($html)
	{
		$html = (string) $html;
		$allowed = '<p><br><ul><ol><li><div><span><b><strong><i><em><u><h1><h2><h3><h4>';
		return strip_tags($html, $allowed);
	}
}

if (!function_exists('quotation_rich_text_chunk_to_text')) {
	function quotation_rich_text_chunk_to_text($html)
	{
		$html = preg_replace('/<br\s*\/?>/i', ' ', (string) $html);
		$text = strip_tags($html);
		$text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$text = str_replace("\xC2\xA0", ' ', $text);
		$text = preg_replace('/\s+/u', ' ', $text);
		return trim($text);
	}
}

if (!function_exists('quotation_rich_text_to_plain')) {
	function quotation_rich_text_to_plain($html)
	{
		$html = (string) $html;
		$html = preg_replace('/<\/li>/i', "\n", $html);
		$html = preg_replace('/<\/p>/i', "\n", $html);
		$html = preg_replace('/<\/div>/i', "\n", $html);
		$html = preg_replace('/<br\s*\/?>/i', "\n", $html);
		$text = strip_tags($html);
		$text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$text = str_replace("\xC2\xA0", ' ', $text);
		$lines = array();
		foreach (preg_split('/\r\n|\r|\n/', $text) as $line) {
			$line = trim(preg_replace('/[ \t]+/', ' ', $line));
			if ($line !== '') {
				$lines[] = $line;
			}
		}
		return implode("\n", $lines);
	}
}

if (!function_exists('quotation_rich_text_to_list_items')) {
	/**
	 * Extract one logical bullet per li/p/line — never split on periods or commas.
	 */
	function quotation_rich_text_to_list_items($html, $default = '')
	{
		$html = quotation_rich_text_clean_mso_html($html);
		$html = trim((string) $html);
		if (quotation_rich_text_is_empty($html)) {
			return $default !== '' ? array($default) : array();
		}

		$items = array();

		if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $html, $m)) {
			foreach ($m[1] as $chunk) {
				$t = quotation_rich_text_chunk_to_text($chunk);
				if ($t !== '') {
					$items[] = $t;
				}
			}
		}

		if (empty($items) && preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $html, $m)) {
			foreach ($m[1] as $chunk) {
				$t = quotation_rich_text_chunk_to_text($chunk);
				if ($t !== '') {
					$items[] = $t;
				}
			}
		}

		// Word paste sometimes puts the last point in a standalone span/div.
		if (preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $html, $pm)) {
			$remaining = preg_replace('/<p[^>]*>.*?<\/p>/is', '', $html);
			if (preg_match_all('/<(?:span|div)[^>]*>(.*?)<\/(?:span|div)>/is', $remaining, $rm)) {
				foreach ($rm[1] as $chunk) {
					$t = quotation_rich_text_chunk_to_text($chunk);
					if ($t !== '' && !in_array($t, $items, true)) {
						$items[] = $t;
					}
				}
			}
		}

		if (empty($items)) {
			$plain = quotation_rich_text_to_plain($html);
			foreach (preg_split('/\n+/', $plain) as $line) {
				$line = trim($line);
				if ($line !== '') {
					$items[] = $line;
				}
			}
		}

		if (empty($items) && $default !== '') {
			$items[] = $default;
		}

		return $items;
	}
}

if (!function_exists('quotation_rich_text_list_html')) {
	function quotation_rich_text_list_html($items)
	{
		if (empty($items)) {
			return '';
		}
		$html = '<ul style="margin:0 0 0 18px;padding:0;list-style:disc;">';
		foreach ($items as $item) {
			$html .= '<li style="margin-bottom:8px;">' . htmlspecialchars((string) $item, ENT_QUOTES, 'UTF-8') . '</li>';
		}
		return $html . '</ul>';
	}
}

if (!function_exists('quotation_rich_text_for_email_html')) {
	/**
	 * Clean WYSIWYG / Word HTML and render readable bullets for email/PDF/Word.
	 */
	function quotation_rich_text_for_email_html($html, $default = '')
	{
		if (quotation_rich_text_is_empty($html)) {
			if ($default === '') {
				return '';
			}
			return '<p style="margin:0;color:#666;">' . htmlspecialchars($default, ENT_QUOTES, 'UTF-8') . '</p>';
		}

		$items = quotation_rich_text_to_list_items($html, '');
		if (!empty($items)) {
			return quotation_rich_text_list_html($items);
		}

		$clean = quotation_rich_text_clean_mso_html($html);
		$plain = quotation_rich_text_to_plain($clean);
		if ($plain !== '') {
			return '<p style="margin:0 0 8px;">' . nl2br(htmlspecialchars($plain, ENT_QUOTES, 'UTF-8')) . '</p>';
		}

		return quotation_rich_text_sanitize_html($clean);
	}
}

if (!function_exists('quotation_rich_text_to_whatsapp')) {
	function quotation_rich_text_to_whatsapp($html, $default = '')
	{
		if (quotation_rich_text_is_empty($html)) {
			return $default;
		}
		$items = quotation_rich_text_to_list_items($html, '');
		if (!empty($items)) {
			$lines = array();
			foreach ($items as $item) {
				$lines[] = '• ' . $item;
			}
			return implode("\n", $lines);
		}
		return quotation_rich_text_to_plain($html);
	}
}

if (!function_exists('quotation_rich_text_word_wrapper_style')) {
	function quotation_rich_text_word_wrapper_style()
	{
		return 'line-height:1.65;font-size:14px;word-wrap:break-word;overflow-wrap:break-word;';
	}
}
