<?php

/**
 * GENERIC QUOTATION BUILDER - CONFIGURATION STORE
 * -------------------------------------------------------------------------
 * The generic builder is activated/deactivated from Company Profile and its
 * settings (active flag, per-section visibility, registered templates and
 * customer testimonials) are persisted as a single JSON document in
 * app_settings.generic_builder_config.
 *
 * This file is self-installing: it creates the column on first use so no
 * manual migration is required. It defines functions only (no output) and is
 * safe to include wherever model.php has already been loaded.
 */

if (!function_exists('gqb_default_config')) {
    /** The default configuration used when nothing is stored yet. */
    function gqb_default_config()
    {
        return array(
            'active'   => false,
            'sections' => array(
                'hero'                => true,
                'tour_overview'       => true,
                'hotels'              => true,
                'flights'             => true,
                'trains'              => true,
                'cruises'             => true,
                'activities'          => true,
                'vehicles'            => true,
                'itinerary'           => true,
                'inclusion_exclusion' => true,
                'costing'             => true,
                'bank_details'        => true,
                'terms_conditions'    => true,
                'testimonials'        => true,
                'thank_you'           => true,
            ),
            'active_template' => '',
            'testimonials'    => array(),
        );
    }
}

if (!function_exists('gqb_ensure_column')) {
    /** Add app_settings.generic_builder_config (LONGTEXT) when it is missing. */
    function gqb_ensure_column()
    {
        try {
            $check = mysqlQuery("SHOW COLUMNS FROM `app_settings` LIKE 'generic_builder_config'");
            if ($check !== false && mysqli_num_rows($check) > 0) {
                return true;
            }
            return mysqlQuery("ALTER TABLE `app_settings` ADD COLUMN `generic_builder_config` LONGTEXT NULL") !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('gqb_get_config')) {
    /**
     * Read the stored configuration, merged on top of the defaults so newly
     * introduced keys always resolve to a sane value.
     */
    function gqb_get_config()
    {
        gqb_ensure_column();
        $defaults = gqb_default_config();

        $row = array();
        try {
            $res = mysqlQuery("SELECT generic_builder_config FROM app_settings WHERE setting_id='1'");
            if ($res === false) {
                $res = mysqlQuery("SELECT generic_builder_config FROM app_settings LIMIT 1");
            }
            $row = ($res !== false) ? mysqli_fetch_assoc($res) : array();
        } catch (\Throwable $e) {
            $row = array();
        }

        $stored = array();
        if (!empty($row['generic_builder_config'])) {
            $decoded = json_decode($row['generic_builder_config'], true);
            if (is_array($decoded)) {
                $stored = $decoded;
            }
        }

        $config = array_merge($defaults, $stored);
        $config['sections'] = array_merge($defaults['sections'], isset($stored['sections']) && is_array($stored['sections']) ? $stored['sections'] : array());
        if (!isset($config['testimonials']) || !is_array($config['testimonials'])) {
            $config['testimonials'] = array();
        }
        $config['active'] = !empty($config['active']);
        return $config;
    }
}

if (!function_exists('gqb_save_config')) {
    /** Persist a configuration array as JSON. Returns true on success. */
    function gqb_save_config($config)
    {
        gqb_ensure_column();
        $json = json_encode($config);
        $json = function_exists('mysqlREString') ? mysqlREString($json) : addslashes($json);

        try {
            $count = mysqlQuery("SELECT setting_id FROM app_settings WHERE setting_id='1'");
            if ($count !== false && mysqli_num_rows($count) > 0) {
                return mysqlQuery("UPDATE app_settings SET generic_builder_config='$json' WHERE setting_id='1'") !== false;
            }
            return mysqlQuery("UPDATE app_settings SET generic_builder_config='$json' LIMIT 1") !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
