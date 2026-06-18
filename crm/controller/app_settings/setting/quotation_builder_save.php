<?php

/**
 * Persist Generic Quotation Builder settings (Company Profile -> Quotation Builder).
 * Stores: active flag, per-section visibility, customer testimonials, active template.
 */

include_once('../../../model/model.php');
include_once('../../../model/app_settings/print_html/quotation_html/generic_builder_config.php');

$active   = (isset($_POST['active']) && $_POST['active'] == '1');
$template = isset($_POST['active_template']) ? trim($_POST['active_template']) : '';

$sections_in = array();
if (isset($_POST['sections'])) {
    $decoded = json_decode($_POST['sections'], true);
    if (is_array($decoded)) {
        foreach ($decoded as $key => $val) {
            $sections_in[$key] = (bool) $val;
        }
    }
}

$testimonials = array();
if (isset($_POST['testimonials'])) {
    $decoded = json_decode($_POST['testimonials'], true);
    if (is_array($decoded)) {
        foreach ($decoded as $t) {
            $testimonials[] = array(
                'name'        => isset($t['name']) ? trim($t['name']) : '',
                'designation' => isset($t['designation']) ? trim($t['designation']) : '',
                'review'      => isset($t['review']) ? trim($t['review']) : '',
                'photo'       => isset($t['photo']) ? trim($t['photo']) : '',
            );
        }
    }
}

// Merge onto defaults so any newly added section keys keep sane values.
$config = gqb_get_config();
$config['active'] = $active;
$config['active_template'] = $template;
$config['sections'] = array_merge($config['sections'], $sections_in);
$config['testimonials'] = $testimonials;

if (gqb_save_config($config)) {
    echo "Quotation builder settings saved successfully!";
} else {
    echo "error--Sorry, quotation builder settings are not saved!";
}
