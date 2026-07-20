<?php
/**
 * Normalize quotation/booking discount type values for package tour sales.
 * Supports legacy values: Percentage, Flat, and per-person codes 1/2.
 */
function booking_normalize_discount_in($discount_in) {
	$discount_in = trim((string) $discount_in);
	if ($discount_in === '1') {
		return 'Percentage';
	}
	if ($discount_in === '2') {
		return 'Flat';
	}
	if ($discount_in === 'Percentage' || $discount_in === 'Flat') {
		return $discount_in;
	}
	return 'Percentage';
}

function booking_is_percentage_discount($discount_in) {
	return booking_normalize_discount_in($discount_in) === 'Percentage';
}

function booking_discount_display_suffix($discount_in) {
	return booking_is_percentage_discount($discount_in) ? '%' : '';
}

function booking_get_pp_costing_discount($quotation_id) {
	$result = array('discount_in' => 'Percentage', 'discount' => 0);
	$sq_pp = mysqlQuery("SELECT discount_in, discount_amount FROM package_quotation_pp_costing WHERE quotation_id='$quotation_id' AND pax_type='adult' LIMIT 1");
	if ($row_pp = mysqli_fetch_assoc($sq_pp)) {
		$result['discount_in'] = booking_normalize_discount_in($row_pp['discount_in']);
		$result['discount'] = $row_pp['discount_amount'];
		return $result;
	}
	$sq_pp = mysqlQuery("SELECT discount_in, discount_amount FROM package_quotation_pp_costing WHERE quotation_id='$quotation_id' LIMIT 1");
	if ($row_pp = mysqli_fetch_assoc($sq_pp)) {
		$result['discount_in'] = booking_normalize_discount_in($row_pp['discount_in']);
		$result['discount'] = $row_pp['discount_amount'];
	}
	return $result;
}

function booking_resolve_quotation_discount($quotation_id, $sq_costing, $sq_quotation = null) {
	$discount_in = 'Percentage';
	$discount = 0;

	if (is_array($sq_costing) && !empty($sq_costing)) {
		$discount_in = booking_normalize_discount_in(isset($sq_costing['discount_in']) ? $sq_costing['discount_in'] : '');
		$discount = isset($sq_costing['discount']) ? $sq_costing['discount'] : 0;
	}

	$use_pp_fallback = ((float) $discount == 0);
	if (!$use_pp_fallback && $sq_quotation && intval($sq_quotation['costing_type']) == 2) {
		$normalized_group = booking_normalize_discount_in(isset($sq_costing['discount_in']) ? $sq_costing['discount_in'] : '');
		if ($normalized_group === 'Percentage' && (float) $discount == 0) {
			$use_pp_fallback = true;
		}
	}

	if ($use_pp_fallback && $sq_quotation && intval($sq_quotation['costing_type']) == 2) {
		$pp_discount = booking_get_pp_costing_discount($quotation_id);
		if ((float) $pp_discount['discount'] > 0 || $pp_discount['discount_in'] === 'Flat') {
			$discount_in = $pp_discount['discount_in'];
			$discount = $pp_discount['discount'];
		}
	}

	return array(
		'discount_in' => $discount_in,
		'discount' => $discount
	);
}
