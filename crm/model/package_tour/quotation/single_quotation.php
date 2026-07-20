<?php
/**
 * Public single-quotation preview (email / WhatsApp / share links).
 * Validates the quotation dynamically, then opens the Option-1 viewer
 * without triggering the browser print dialog.
 */
include_once('../../model.php');
include_once(__DIR__ . '/../../app_settings/print_html/quotation_html/generic_quotation_data.php');

$quotation_param = '';
if (isset($_GET['quotation'])) {
    $quotation_param = trim($_GET['quotation']);
} elseif (isset($_GET['quotation_id'])) {
    $quotation_param = trim($_GET['quotation_id']);
}

$decoded_quotation = base64_decode($quotation_param, true);
if ($decoded_quotation !== false && ctype_digit($decoded_quotation)) {
    $quotation_id = (int) $decoded_quotation;
} elseif (ctype_digit($quotation_param)) {
    $quotation_id = (int) $quotation_param;
} else {
    $quotation_id = 0;
}

if ($quotation_id <= 0) {
    echo 'Invalid quotation.';
    exit;
}

$quotation_data = get_generic_quotation_data($quotation_id);
if (empty($quotation_data['found'])) {
    echo 'Quotation not found.';
    exit;
}

$preview_url = BASE_URL
    . 'model/app_settings/print_html/quotation_html/quotation_html_1/fit_single_quotation_html.php'
    . '?quotation_id=' . urlencode((string) $quotation_id)
    . '&preview=1';

header('Location: ' . $preview_url);
exit;