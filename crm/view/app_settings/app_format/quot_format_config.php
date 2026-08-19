<?php
/**
 * Shared quotation format labels and option → format_image_master.type mapping.
 * Used by upload modal; main format screen keeps its inline array unchanged.
 */
if (!function_exists('quot_format_get_preview_base_url')) {
	function quot_format_get_preview_base_url()
	{
		return 'https://itoursdemo.co.in/quotation-format/';
	}
}

if (!function_exists('quot_format_get_preview_urls')) {
	/**
	 * Sample PDF opened from Administration → Format → View (per dropdown option).
	 */
	function quot_format_get_preview_urls()
	{
		$base = quot_format_get_preview_base_url();
		return array(
			'1'  => $base . 'Quotation-Option-1.pdf',
			'2'  => $base . 'Quotation-Option-2.pdf',
			'3'  => $base . 'Quotation-Option-3.pdf',
			'4'  => $base . 'Quotation-Option-4.pdf',
			'5'  => $base . 'Quotation-Option-5.pdf',
			'6'  => $base . 'Quotation-Option-6.pdf',
			'7'  => $base . 'Quotation-Option-7.pdf',
			'9'  => $base . 'Quotation-Option-6.pdf',
			'10' => $base . 'Quotation-Option-7.pdf',
		);
	}
}

if (!function_exists('quot_format_get_preview_pdf_url')) {
	function quot_format_get_preview_pdf_url($quot_format)
	{
		$urls = quot_format_get_preview_urls();
		$key = (string) (int) $quot_format;
		if ($key !== '0' && isset($urls[$key])) {
			return $urls[$key];
		}
		return $urls['1'];
	}
}

if (!function_exists('quot_format_get_labels')) {
	function quot_format_get_labels()
	{
		return array(
			1  => 'Option-1',
			2  => 'Option-2',
			3  => 'Option-3',
			4  => 'Option-4',
			5  => 'Option-5',
			9  => 'Portrait Standard',
			10 => 'Portrait Advanced',
		);
	}
}

if (!function_exists('quot_format_option_to_type')) {
	/**
	 * Map quot_format option (numeric) to format_image_master.type string.
	 * Same mapping as display_images.php and app_settings_master.php.
	 */
	function quot_format_option_to_type($quot_format)
	{
		switch ((int) $quot_format) {
			case 1:
			case 9:
				return 'Portrait-Standard';
			case 2:
				return 'Landscape-Standard';
			case 3:
				return 'Landscape-Creative';
			case 4:
				return 'Portrait-Creative';
			case 5:
			case 10:
				return 'Portrait-Advanced';
			case 6:
				return 'Landscape-Advanced';
			default:
				return 'Portrait-Standard';
		}
	}
}

if (!function_exists('quot_format_resolve_type')) {
	/**
	 * Accept numeric option id or legacy type string (Portrait-Standard, etc.).
	 */
	function quot_format_resolve_type($format)
	{
		$format = trim((string) $format);
		if ($format === '') {
			return 'Portrait-Standard';
		}
		if (ctype_digit($format) || (is_numeric($format) && (int) $format > 0)) {
			return quot_format_option_to_type((int) $format);
		}
		return $format;
	}
}
