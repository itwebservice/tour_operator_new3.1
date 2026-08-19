<?php
/**
 * Shared quotation format labels and option → format_image_master.type mapping.
 * Used by upload modal; main format screen keeps its inline array unchanged.
 */
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
