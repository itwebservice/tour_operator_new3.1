<?php
/**
 * Convert an amount from company default currency into selected quotation currency (ROE).
 * POST: amount, to_currency (id)
 * Returns JSON: { amount, formatted, from_currency, to_currency, factor }
 */
include_once(__DIR__ . '/../../../../../model/model.php');

header('Content-Type: application/json');

global $currency;

$amount = isset($_POST['amount']) ? $_POST['amount'] : 0;
$to_currency = isset($_POST['to_currency']) ? trim((string)$_POST['to_currency']) : '';
$from_currency = isset($_POST['from_currency']) && $_POST['from_currency'] !== ''
	? trim((string)$_POST['from_currency'])
	: (string)$currency;

if (is_string($amount)) {
	$amount = str_replace(',', '', $amount);
}
$amount = floatval($amount);

$converted = currency_conversion_amount($from_currency, $to_currency, $amount);
$formatted = currency_conversion($from_currency, $to_currency, $amount);

$factor = 1.0;
if ($amount != 0.0) {
	$factor = $converted / $amount;
}

echo json_encode(array(
	'amount' => round($converted, 2),
	'formatted' => $formatted,
	'from_currency' => $from_currency,
	'to_currency' => $to_currency,
	'factor' => $factor,
	'base_amount' => $amount
));
