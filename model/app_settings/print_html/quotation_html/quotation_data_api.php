<?php

/**
 * GENERIC QUOTATION DATA API (JSON endpoint)
 * -------------------------------------------------------------------------
 * Returns the complete Package Tour quotation data-set as JSON so that any
 * front-end / template can be built on top of it.
 *
 *   GET|POST  quotation_id   (required)  package_tour_quotation_master.quotation_id
 *   GET|POST  force          (optional)  "1" to return data even when the
 *                                        builder is deactivated in Company Profile.
 *
 * Response shape:
 *   {
 *     "status": "ok" | "error",
 *     "builder_active": true|false,
 *     "config": { ...activation + section toggles... },
 *     "data": { ...full quotation data-set... }
 *   }
 */

include "../../../model.php";
include_once "generic_quotation_data.php";
include_once "generic_builder_config.php";

header('Content-Type: application/json; charset=utf-8');

$quotation_id = isset($_REQUEST['quotation_id']) ? trim($_REQUEST['quotation_id']) : '';
$force        = isset($_REQUEST['force']) && $_REQUEST['force'] == '1';

if ($quotation_id === '') {
    echo json_encode(array(
        'status'  => 'error',
        'message' => 'quotation_id is required.',
    ));
    exit;
}

$config = gqb_get_config();

// echo '<pre>';
// print_r($config['social_links']);
// exit;

// Respect the activate/deactivate toggle unless explicitly forced.
if (empty($config['active']) && !$force) {
    echo json_encode(array(
        'status'         => 'ok',
        'builder_active' => false,
        'message'        => 'Generic quotation builder is deactivated in Company Profile.',
        'config'         => $config,
        'data'           => null,
    ));
    exit;
}

$data = get_generic_quotation_data($quotation_id);

if (empty($data['found'])) {
    echo json_encode(array(
        'status'         => 'error',
        'builder_active' => (bool) $config['active'],
        'message'        => 'Quotation not found.',
        'quotation_id'   => $quotation_id,
    ));
    exit;
}

// Surface admin-managed testimonials from config alongside the quotation data.
// $data['testimonials'] = isset($config['testimonials']) ? $config['testimonials'] : array();

// Section visibility from config (data is always present; this tells the
// consumer which sections the company has chosen to render).
$data['section_visibility'] = isset($config['sections']) ? $config['sections'] : array();
$data['active_template']    = isset($config['active_template']) ? $config['active_template'] : '';

echo json_encode(array(
    'status'         => 'ok',
    'builder_active' => (bool) $config['active'],
    'config'         => $config,
    'data'           => $data,
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
