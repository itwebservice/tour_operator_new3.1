<?php

/**
 * GENERIC QUOTATION DATA PROVIDER (API-style)
 * -------------------------------------------------------------------------
 * Single source of truth that returns the COMPLETE data-set of a Package Tour
 * quotation as one structured array. It is intentionally presentation-free:
 * any number of quotation template designs can be built on top of this data
 * without re-querying the database or duplicating business logic.
 *
 * Usage (PHP):
 *     include_once '.../model/model.php';                  // bootstraps $conn + helpers
 *     include_once '.../quotation_html/generic_quotation_data.php';
 *     $data = get_generic_quotation_data($quotation_id);
 *
 * Usage (HTTP / JSON):  see quotation_data_api.php in this folder.
 *
 * This file defines functions only and performs no output, so it is safe to
 * include from any controller/view that has already loaded model.php.
 *
 * Field mapping reference: Package Tour quotation schema
 * (package_tour_quotation_master + child *_entries tables, package_quotation_program,
 *  custom_package_master, bank_master, terms_and_conditions, app_settings, etc.)
 */

if (!function_exists('gqd_query_safe')) {
  /**
   * Run a query without ever fatally crashing the caller. mysqli runs in
   * exception mode in this app, so missing tables/columns would otherwise
   * throw. We swallow such errors and return false, keeping the data-set
   * resilient across installs whose schema differs slightly.
   */
  function gqd_query_safe($query)
  {
    try {
      return mysqlQuery($query);
    } catch (\Throwable $e) {
      return false;
    }
  }
}

if (!function_exists('gqd_row')) {
  /** Fetch a single associative row (or [] when nothing found / query failed). */
  function gqd_row($query)
  {
    $res = gqd_query_safe($query);
    if ($res === false) {
      return array();
    }
    $row = mysqli_fetch_assoc($res);
    return is_array($row) ? $row : array();
  }
}

if (!function_exists('gqd_rows')) {
  /** Fetch all associative rows as a numerically indexed array. */
  function gqd_rows($query)
  {
    $out = array();
    $res = gqd_query_safe($query);
    if ($res === false) {
      return $out;
    }
    while ($r = mysqli_fetch_assoc($res)) {
      $out[] = $r;
    }
    return $out;
  }
}

if (!function_exists('gqd_count')) {
  /** Number of rows returned by a query. */
  function gqd_count($query)
  {
    $res = gqd_query_safe($query);
    return ($res === false) ? 0 : mysqli_num_rows($res);
  }
}

if (!function_exists('gqd_esc')) {
  /** Escape a value for safe inline interpolation into a query. */
  function gqd_esc($value)
  {
    $value = (string) $value;
    if (function_exists('mysqlREString')) {
      $escaped = mysqlREString($value);
      if ($escaped !== false) {
        return $escaped;
      }
    }
    return addslashes($value);
  }
}

if (!function_exists('gqd_pickup_drop')) {
  /**
   * Resolve a transport/excursion pickup or drop location label from its type + id.
   * Mirrors the logic used by the existing fit_quotation_html templates.
   */
  function gqd_pickup_drop($type, $id)
  {
    $id = gqd_esc($id);
    if ($type == 'city') {
      $r = gqd_row("select city_name from city_master where city_id='$id'");
      return isset($r['city_name']) ? $r['city_name'] : '';
    }
    if ($type == 'hotel') {
      $r = gqd_row("select hotel_name from hotel_master where hotel_id='$id'");
      return isset($r['hotel_name']) ? $r['hotel_name'] : '';
    }
    // default => airport
    $r = gqd_row("select airport_name, airport_code from airport_master where airport_id='$id'");
    if (empty($r)) {
      return '';
    }
    $name = function_exists('clean') ? clean($r['airport_name']) : $r['airport_name'];
    $code = function_exists('clean') ? clean($r['airport_code']) : $r['airport_code'];
    return trim($name . ' (' . $code . ')');
  }
}

if (!function_exists('gqd_parse_service_tax')) {
  /**
   * Parse the costing service_tax_subtotal string ("Name:Pct:Amount,Name:Pct:Amount,...")
   * exactly the way the existing quotation templates do.
   *
   * @return array [ (float) total_tax_amount, (string) label ]
   */
  function gqd_parse_service_tax($subtotal)
  {
    $amount = 0;
    $label  = '';
    if ($subtotal !== null && $subtotal !== '' && $subtotal !== 0.00 && $subtotal !== '0.00') {
      $parts = explode(',', $subtotal);
      foreach ($parts as $part) {
        if (trim($part) === '') {
          continue;
        }
        $seg = explode(':', $part);
        $amount += isset($seg[2]) ? (float) $seg[2] : 0;
        $label  .= (isset($seg[0]) ? $seg[0] : '') . (isset($seg[1]) ? $seg[1] : '') . ', ';
      }
    }
    return array($amount, trim(rtrim(trim($label), ',')));
  }
}

if (!function_exists('gqd_tcs')) {
  /**
   * Extract [tcs_percent, tcs_value] from the costing bsmValues JSON,
   * mirroring the templates' guard against the literal string "NaN".
   */
  function gqd_tcs($bsm_json)
  {
    $d = json_decode($bsm_json, true);
    if (isset($d[0]['tcsper']) && $d[0]['tcsper'] != 'NaN') {
      return array($d[0]['tcsper'], isset($d[0]['tcsvalue']) ? $d[0]['tcsvalue'] : 0);
    }
    return array(0, 0);
  }
}

if (!function_exists('gqd_clean_html_text')) {
  /**
   * Treat the "empty rich-text" markers the templates ignore (' ', '<div><br></div>')
   * as truly empty so consumers never render junk.
   */
  function gqd_clean_html_text($value)
  {
    $v = isset($value) ? trim($value) : '';
    if ($v === '' || $v === ' ' || $v === '<div><br></div>' || $v === '<br>' || $v === '<p></p>') {
      return '';
    }
    return $value;
  }
}

if (!function_exists('gqd_media_url')) {
  /**
   * Resolve itinerary / gallery image paths to a browser-ready absolute URL.
   * Handles full external URLs, crm/uploads paths, and project-root uploads.
   */
  function gqd_media_url($url)
  {
    $url = trim((string) $url);
    if ($url === '' || stripos($url, 'dummy') !== false) {
      return '';
    }

    $url = str_replace('\\', '/', $url);
    if (preg_match('#^https?://#i', $url)) {
      return str_replace(' ', '%20', $url);
    }

    $url = preg_replace('#^(\.\./)+#', '', $url);
    $url = ltrim($url, '/');

    if (!defined('BASE_URL')) {
      return $url;
    }

    $base = rtrim(BASE_URL, '/');
    $project_base = rtrim(str_replace('/crm/', '/', BASE_URL), '/');

    if (strpos($url, 'crm/') === 0) {
      return $base . '/' . substr($url, 4);
    }
    if (strpos($url, 'uploads/quotation_images/') === 0) {
      return $base . '/' . $url;
    }
    if (strpos($url, 'uploads/') === 0) {
      return $project_base . '/' . $url;
    }

    return $base . '/' . $url;
  }
}

if (!function_exists('gqd_itinerary_package_ids')) {
  /** Package ids that may appear in daywise image map entries for a program row. */
  function gqd_itinerary_package_ids($master, $program_row, $effective_package_id)
  {
    $ids = array();
    $program_pkg_id = intval(isset($program_row['package_id']) ? $program_row['package_id'] : 0);
    $effective_package_id = intval($effective_package_id);
    $refer_id = intval(isset($master['quotation_refer_id']) ? $master['quotation_refer_id'] : 0);

    if ($program_pkg_id > 0) {
      $ids[] = $program_pkg_id;
    }
    if ($effective_package_id > 0) {
      $ids[] = $effective_package_id;
    }
    if ($refer_id > 0) {
      $ids[] = $refer_id;
    }

    return array_values(array_unique($ids));
  }
}

if (!function_exists('get_generic_quotation_data')) {
  /**
   * Build the full structured data-set for a Package Tour quotation.
   *
   * @param int|string $quotation_id  package_tour_quotation_master.quotation_id
   * @return array  Associative, section-keyed array. 'found' === false when the
   *                quotation id does not exist.
   */
  function get_generic_quotation_data($quotation_id)
  {
    global $currency, $app_quot_format, $app_name, $app_address, $app_website,
      $app_email_id, $app_contact_no, $quot_note,
      $bank_name_setting, $bank_branch_name, $bank_acc_no, $acc_name,
      $bank_account_name, $bank_swift_code, $bank_ifsc_code;

    $quotation_id = gqd_esc($quotation_id);

    $master = gqd_row("select * from package_tour_quotation_master where quotation_id='$quotation_id'");
    if (empty($master)) {
      return array(
        'found'        => false,
        'quotation_id' => $quotation_id,
        'message'      => 'Quotation not found.',
      );
    }


    // AI-built quotations store package_id=0 and keep the real package in quotation_refer_id.
    $effective_package_id = function_exists('get_quotation_package_lookup_id')
      ? intval(get_quotation_package_lookup_id($master))
      : intval(isset($master['package_id']) ? $master['package_id'] : 0);
    $package_id      = gqd_esc($effective_package_id);
    $branch_admin_id = isset($master['branch_admin_id']) ? gqd_esc($master['branch_admin_id']) : '0';
    $currency_code   = isset($master['currency_code']) ? $master['currency_code'] : '0';
    $session_role    = isset($_SESSION['role']) ? $_SESSION['role'] : '';

    // ---- Related master records -------------------------------------------------
    $package = gqd_row("select * from custom_package_master where package_id='$package_id'");
    $dest_id = isset($package['dest_id']) ? gqd_esc($package['dest_id']) : '0';
    $dest    = gqd_row("select * from destination_master where dest_id='$dest_id'");

    // Company / branch info
    $branch_admin = ($branch_admin_id != '0')
      ? gqd_row("select * from branch_admin_master where branch_admin_id='$branch_admin_id'")
      : array();
    $branch_details = ($branch_admin_id != '0')
      ? gqd_row("select * from branches where branch_id='$branch_admin_id'")
      : gqd_row("select * from branches where branch_id='1'");

    $branch_link    = gqd_row("select branch_status from branch_assign where link='package_booking/quotation/home/index.php'");
    $branch_status  = isset($branch_link['branch_status']) ? $branch_link['branch_status'] : 'no';
    $branch_qr_url  = function_exists('get_branch_qr_url') ? get_branch_qr_url($branch_admin_id) : '';
    $branch_logo_url = function_exists('get_branch_logo_url') ? get_branch_logo_url($branch_admin_id) : '';

    // Prepared-by employee (login_id -> roles.emp_id -> emp_master)
    $login_id = isset($master['login_id']) ? gqd_esc($master['login_id']) : '';
    $login    = gqd_row("select * from roles where id='$login_id'");
    $emp_id   = isset($login['emp_id']) ? gqd_esc($login['emp_id']) : gqd_esc($master['emp_id']);
    $emp      = gqd_row("select * from emp_master where emp_id='$emp_id'");
    $emp_name = (!empty($emp['first_name'])) ? trim($emp['first_name'] . ' ' . $emp['last_name']) : 'Admin';

    // App settings (logo, qr, name, address, etc.)
    $app = gqd_row("select * from app_settings where setting_id='1'");
    if (empty($app)) {
      $app = gqd_row("select * from app_settings");
    }

    // Bank (branch active bank, fallback to settings)
    $bank_branch_id = ($branch_admin_id != '0') ? $branch_admin_id : '1';
    $bank = gqd_row("select * from bank_master where branch_id='$bank_branch_id' and active_flag='Active'");

    // Terms & conditions (Package Quotation, destination specific else global)


    // ================================== Dipti
    $terms = gqd_row("select * from terms_and_conditions 
    where type='Package Quotation' 
    and dest_id='$dest_id' 
    and active_flag='Active' 
    order by terms_and_conditions_id desc limit 1");

    if (empty($terms)) {
      $terms = gqd_row("select * from terms_and_conditions 
      where type='Package Quotation' 
      and dest_id='0' 
      and active_flag='Active' 
      order by terms_and_conditions_id desc limit 1");
      }
    // =============================================================
    // $tc_dest_count = gqd_count("select dest_id from terms_and_conditions where type='Package Quotation' and dest_id='$dest_id' and active_flag='Active'");
    // $tc_dest_id    = ($tc_dest_count != 0) ? $dest_id : '0';
    // $terms         = gqd_row("select * from terms_and_conditions where type='Package Quotation' and dest_id='$tc_dest_id' and active_flag='Active'");

    // Destination guide video
    $video = gqd_row("select link from video_itinerary_master where dest_id='$dest_id'");

    // ---- Year + display id -----------------------------------------------------
    $quotation_date = isset($master['quotation_date']) ? $master['quotation_date'] : '';
    $yr             = explode('-', $quotation_date);
    $year           = isset($yr[0]) ? $yr[0] : date('Y');
    $display_id     = function_exists('get_quotation_id') ? get_quotation_id($master['quotation_id'], $year) : $master['quotation_id'];

    // ---- Section counts --------------------------------------------------------
    $count_hotel     = gqd_count("select id from package_tour_quotation_hotel_entries where quotation_id='$quotation_id'");
    $count_plane     = gqd_count("select id from package_tour_quotation_plane_entries where quotation_id='$quotation_id'");
    $count_train     = gqd_count("select id from package_tour_quotation_train_entries where quotation_id='$quotation_id'");
    $count_cruise    = gqd_count("select id from package_tour_quotation_cruise_entries where quotation_id='$quotation_id'");
    $count_exc       = gqd_count("select id from package_tour_quotation_excursion_entries where quotation_id='$quotation_id'");
    $count_transport = gqd_count("select id from package_tour_quotation_transport_entries2 where quotation_id='$quotation_id'");
    $count_program   = gqd_count("select id from package_quotation_program where quotation_id='$quotation_id'");

    $total_days = isset($master['total_days']) ? $master['total_days'] : '';

    // =====================================================================
    // 1. HERO
    // =====================================================================
    // Company name always comes from App Settings (Basic Info → Company Name).
    // Do not use branch_admin_master / branch_name here — that caused hero and
    // thank_you sections to show different names in Option-1 PDF/HTML.
    $company_name = isset($app['app_name']) && trim($app['app_name']) !== ''
      ? trim($app['app_name'])
      : (isset($app_name) && trim($app_name) !== '' ? trim($app_name) : '');

    //=============================== Dipti
    // Gallery images for first page - destination wise last 4 images
    $gallery_images = array();

    $dest_id_for_gallery = 0;

    // First try from package master (uses quotation_refer_id for AI quotations)
    if ($effective_package_id > 0) {
      if (!empty($dest_id) && $dest_id != '0') {
        $dest_id_for_gallery = $dest_id;
      } else {
        $pkg = gqd_row("SELECT dest_id FROM custom_package_master WHERE package_id='$package_id'");
        if (!empty($pkg['dest_id'])) {
          $dest_id_for_gallery = $pkg['dest_id'];
        }
      }
    }

    // Fallback: match destination name
    if (empty($dest_id_for_gallery) && !empty($master['tour_name'])) {
      $tour_name = gqd_esc($master['tour_name']);
      $dest = gqd_row("SELECT dest_id FROM destination_master WHERE dest_name='$tour_name' LIMIT 1");
      if (!empty($dest['dest_id'])) {
        $dest_id_for_gallery = $dest['dest_id'];
      }
    }

    if (!empty($dest_id_for_gallery)) {
      $gallery_rows = gqd_rows("SELECT image_url FROM gallary_master WHERE dest_id='$dest_id_for_gallery' ORDER BY entry_id DESC LIMIT 4");

      foreach ($gallery_rows as $gr) {
        $img = isset($gr['image_url']) ? trim($gr['image_url']) : '';

        if ($img != '') {
          $img = str_replace('\\', '/', $img);

          if (strpos($img, 'http://') !== 0 && strpos($img, 'https://') !== 0) {
            $img = str_replace('../../../../', '', $img);
            $img = str_replace('../../../', '', $img);
            $img = str_replace('../../', '', $img);
            $img = str_replace('../', '', $img);
            $img = preg_replace('/\/+/', '/', $img);
            $img = BASE_URL . $img;
          }

          $gallery_images[] = $img;
        }
      }
    }

    // ======================= Dipti: Destination 5th Gallery Image
    $destination_5th_gallery_image = '';

    if (!empty($dest_id_for_gallery)) {
      $gallery_img = gqd_row("SELECT image_url FROM gallary_master WHERE dest_id='$dest_id_for_gallery' ORDER BY entry_id ASC LIMIT 4,1");

      if (!empty($gallery_img['image_url'])) {
        $img = trim($gallery_img['image_url']);
        $img = str_replace('\\', '/', $img);

        if (strpos($img, 'http://') !== 0 && strpos($img, 'https://') !== 0) {
          $img = str_replace('../../../../', '', $img);
          $img = str_replace('../../../', '', $img);
          $img = str_replace('../../', '', $img);
          $img = str_replace('../', '', $img);
          $img = preg_replace('/\/+/', '/', $img);
          $img = BASE_URL . $img;
        }

        $destination_5th_gallery_image = $img;
      }
    }


    // ==========================================================
    //=====================================

    $hero = array(
      'company_logo'   => function_exists('get_branch_logo_url') ? get_branch_logo_url($branch_admin_id) : '',
      'cover_image'    => function_exists('getFormatImg') ? getFormatImg($app_quot_format, $dest_id) : '',
      'tour_name'      => isset($master['tour_name']) ? $master['tour_name'] : (isset($package['package_name']) ? $package['package_name'] : ''),
      'package_name'   => isset($package['package_name']) ? $package['package_name'] : '',
      'package_code'   => isset($package['package_code']) ? $package['package_code'] : '',
      'quotation_id'   => $master['quotation_id'],
      'quotation_code' => $display_id,
      'total_days'     => $total_days,
      'total_nights'   => ($total_days !== '' ? ($total_days) : ''),
      'duration_label' => ($total_days !== '' ? ($total_days . 'N/' . ((int) $total_days + 1) . 'D') : ''),
      'client_name'    => isset($master['customer_name']) ? $master['customer_name'] : '',
      'company_name'   => $company_name,
      'login_user'     => $emp_name,
      'user_email_id'  => isset($master['email_id']) ? $master['email_id'] : '',
      'user_contact'   => isset($master['mobile_no']) ? $master['mobile_no'] : '',
      'destination_5th_gallery_image' => $destination_5th_gallery_image,
    );

    // =====================================================================
    // 2. TOUR OVERVIEW
    // =====================================================================
    $overview = array(
      'quotation_id'    => $master['quotation_id'],
      'quotation_code'  => $display_id,
      'client_name'     => isset($master['customer_name']) ? $master['customer_name'] : '',
      'destination'     => isset($dest['dest_name']) ? $dest['dest_name'] : (isset($master['tour_name']) ? $master['tour_name'] : ''),
      'company_name'    => $company_name,
      'tour_id'         => $package_id,
      'quotation_date'  => function_exists('get_date_user') ? get_date_user($quotation_date) : $quotation_date,
      'quotation_date_raw' => $quotation_date,
      'travel_from'     => function_exists('get_date_user') ? get_date_user($master['from_date']) : $master['from_date'],
      'travel_to'       => function_exists('get_date_user') ? get_date_user($master['to_date']) : $master['to_date'],
      'customer_email'  => isset($master['email_id']) ? $master['email_id'] : '',
      'customer_mobile' => isset($master['mobile_no']) ? $master['mobile_no'] : '',
      'duration'        => $total_days,
      'duration_label'  => $hero['duration_label'],
      'guest_count'     => isset($master['total_passangers']) ? $master['total_passangers'] : '',
      'package_type'    => isset($master['costing_type']) ? $master['costing_type'] : '',
      'package_type_label' => (isset($master['costing_type']) && $master['costing_type'] == 1) ? 'Group' : 'Per Person',
      'pax'             => array(
        'adult'              => isset($master['total_adult']) ? $master['total_adult'] : 0,
        'children_with_bed'  => isset($master['children_with_bed']) ? $master['children_with_bed'] : 0,
        'children_without_bed' => isset($master['children_without_bed']) ? $master['children_without_bed'] : 0,
        'infant'             => isset($master['total_infant']) ? $master['total_infant'] : 0,
      ),
    );

    // =====================================================================
    // 3. HOTELS (loop)
    // =====================================================================
    $hotels = array();
    foreach (gqd_rows("select * from package_tour_quotation_hotel_entries where quotation_id='$quotation_id' order by package_type") as $h) {
      $hid  = gqd_esc($h['hotel_name']);
      $cid  = gqd_esc($h['city_name']);
      $hm   = gqd_row("select * from hotel_master where hotel_id='$hid'");
      $cm   = gqd_row("select city_name from city_master where city_id='$cid'");
      $img  = gqd_row("select hotel_pic_url from hotel_vendor_images_entries where hotel_id='$hid'");
      $photo = isset($img['hotel_pic_url']) ? preg_replace('/(\/+)/', '/', $img['hotel_pic_url']) : '';

      //==============================Dipti
      if (!empty($photo)) {
        $photo = str_replace('\\', '/', $photo);

        $photo = str_replace('../../../../', '', $photo);
        $photo = str_replace('../../../', '', $photo);
        $photo = str_replace('../../', '', $photo);
        $photo = str_replace('../', '', $photo);

        $photo = BASE_URL . $photo;
      }
      //-===================================
      $hotels[] = array(
        'hotel_name'    => isset($hm['hotel_name']) ? $hm['hotel_name'] : '',
        'hotel_city'    => isset($cm['city_name']) ? $cm['city_name'] : '',
        'room_type'     => isset($h['meal_plan']) ? $h['meal_plan'] : '',
        'meal_plan'     => isset($h['meal_plan']) ? $h['meal_plan'] : '',
        'check_in'      => function_exists('get_date_user') ? get_date_user($h['check_in']) : $h['check_in'],
        'check_out'     => function_exists('get_date_user') ? get_date_user($h['check_out']) : $h['check_out'],
        'check_in_raw'  => isset($h['check_in']) ? $h['check_in'] : '',
        'check_out_raw' => isset($h['check_out']) ? $h['check_out'] : '',
        'amenities'     => isset($h['package_type']) ? $h['package_type'] : '',
        'hotel_photo'   => $photo,
        'room_category' => isset($h['room_category']) ? $h['room_category'] : '',
        'rating'        => isset($h['hotel_type']) ? $h['hotel_type'] : '',
        'package_type'  => isset($h['package_type']) ? $h['package_type'] : '',
        'total_nights'  => isset($h['total_days']) ? $h['total_days'] : '',
      );
    }

    // =====================================================================
    // 4. FLIGHTS (loop)
    // =====================================================================
    // =============================== Dipti 
    $flights = array();

    foreach (gqd_rows("select * from package_tour_quotation_plane_entries where quotation_id='$quotation_id'") as $f) {
      $aid = gqd_esc($f['airline_name']);

      // $airline = gqd_row("select airline_name, airline_code, airline_logo from airline_master where airline_id='$aid'");
      $airline = mysqli_fetch_assoc(
        mysqlQuery("SELECT * FROM airline_master WHERE airline_id='$aid'")
      );

      $airline_name = !empty($airline['airline_name']) ? $airline['airline_name'] : 'NA';
      $airline_code = !empty($airline['airline_code']) ? $airline['airline_code'] : 'FL';

      $dep_time = !empty($f['dapart_time']) ? strtotime($f['dapart_time']) : false;
      $arr_time = !empty($f['arraval_time']) ? strtotime($f['arraval_time']) : false;

      $duration = 'Flight';
      if ($dep_time && $arr_time) {
        $diff = abs($arr_time - $dep_time);
        $h = floor($diff / 3600);
        $m = floor(($diff % 3600) / 60);
        $duration = $h . 'H ' . $m . 'M';
      }

      $flights[] = array(
        'airline_name'   => $airline_name,
        'airline_display' => $airline_name,
        'airline_code'   => $airline_code,
        // 'airline_logo'   => !empty($airline['airline_logo']) ? $airline['airline_logo'] : '',
        'airline_logo'   => !empty($airline['image']) ? $airline['image'] : '',
        'class'          => !empty($f['class']) ? $f['class'] : 'Economy',

        'departure_datetime' => function_exists('get_datetime_user') ? get_datetime_user($f['dapart_time']) : $f['dapart_time'],
        'arrival_datetime'   => function_exists('get_datetime_user') ? get_datetime_user($f['arraval_time']) : $f['arraval_time'],
        'departure_raw'      => !empty($f['dapart_time']) ? $f['dapart_time'] : '',
        'arrival_raw'        => !empty($f['arraval_time']) ? $f['arraval_time'] : '',

        'from_city'      => !empty($f['from_location']) ? $f['from_location'] : '',
        'to_city'        => !empty($f['to_location']) ? $f['to_location'] : '',

        // 'duration'       => $duration,
        'duration' => ($duration == '0H 0M') ? 'As Per Itinerary' : $duration,
        'stop_type'      => 'Non-stop',

        'baggage'        => !empty($f['baggage']) ? $f['baggage'] : '30 KG',

      );
    }
    // ============================
    // $flights = array();
    // foreach (gqd_rows("select * from package_tour_quotation_plane_entries where quotation_id='$quotation_id'") as $f) {
    //     $aid     = gqd_esc($f['airline_name']);
    //     $airline = gqd_row("select airline_name, airline_code, airline_logo from airline_master where airline_id='$aid'");
    //     $airline_display = (!empty($f['airline_name']) && !empty($airline['airline_name']))
    //         ? $airline['airline_name'] . ' (' . $airline['airline_code'] . ')'
    //         : 'NA';
    //     $flights[] = array(
    //         'airline_name'   => isset($airline['airline_name']) ? $airline['airline_name'] : '',
    //         'airline_display' => $airline_display,
    //         'airline_code'   => isset($airline['airline_code']) ? $airline['airline_code'] : '',
    //         'airline_logo'   => isset($airline['airline_logo']) ? $airline['airline_logo'] : '',
    //         'class'          => isset($f['class']) ? $f['class'] : '',
    //         'departure_datetime' => function_exists('get_datetime_user') ? get_datetime_user($f['dapart_time']) : $f['dapart_time'],
    //         'arrival_datetime'   => function_exists('get_datetime_user') ? get_datetime_user($f['arraval_time']) : $f['arraval_time'],
    //         'departure_raw'  => isset($f['dapart_time']) ? $f['dapart_time'] : '',
    //         'arrival_raw'    => isset($f['arraval_time']) ? $f['arraval_time'] : '',
    //         'from_city'      => isset($f['from_location']) ? $f['from_location'] : '',
    //         'to_city'        => isset($f['to_location']) ? $f['to_location'] : '',
    //     );
    // }

    // =====================================================================
    // 5. TRAINS (loop)
    // =====================================================================
    $trains = array();
    foreach (gqd_rows("select * from package_tour_quotation_train_entries where quotation_id='$quotation_id'") as $t) {
      $trains[] = array(
        'from_location'  => isset($t['from_location']) ? $t['from_location'] : '',
        'to_location'    => isset($t['to_location']) ? $t['to_location'] : '',
        'class'          => (isset($t['class']) && $t['class'] != '') ? $t['class'] : 'NA',
        'from_date'      => function_exists('get_datetime_user') ? get_datetime_user($t['departure_date']) : $t['departure_date'],
        'to_date'        => function_exists('get_datetime_user') ? get_datetime_user($t['arrival_date']) : $t['arrival_date'],
        'from_date_raw'  => isset($t['departure_date']) ? $t['departure_date'] : '',
        'to_date_raw'    => isset($t['arrival_date']) ? $t['arrival_date'] : '',
      );
    }

    // =====================================================================
    // 6. CRUISES (loop)
    // =====================================================================
    $cruises = array();
    foreach (gqd_rows("select * from package_tour_quotation_cruise_entries where quotation_id='$quotation_id'") as $c) {
      $cruises[] = array(
        'from_date'     => function_exists('get_datetime_user') ? get_datetime_user($c['dept_datetime']) : $c['dept_datetime'],
        'to_date'       => function_exists('get_datetime_user') ? get_datetime_user($c['arrival_datetime']) : $c['arrival_datetime'],
        'from_date_raw' => isset($c['dept_datetime']) ? $c['dept_datetime'] : '',
        'to_date_raw'   => isset($c['arrival_datetime']) ? $c['arrival_datetime'] : '',
        'route'         => isset($c['route']) ? $c['route'] : '',
        'cabin'         => isset($c['cabin']) ? $c['cabin'] : '',
        'sharing_type'  => isset($c['sharing']) ? $c['sharing'] : '',
      );
    }

    // =====================================================================
    // 7. ACTIVITIES / EXCURSIONS (loop)
    // =====================================================================
    $activities = array();
    foreach (gqd_rows("select * from package_tour_quotation_excursion_entries where quotation_id='$quotation_id'") as $e) {
      $cid     = gqd_esc($e['city_name']);
      $excid   = gqd_esc($e['excursion_name']);
      $cm      = gqd_row("select city_name from city_master where city_id='$cid'");
      $exc     = gqd_row("select excursion_name from excursion_master_tariff where entry_id='$excid'");
      $vehicle = '';
      if (!empty($e['vehicle_id']) && $e['vehicle_id'] != '0') {
        $vid = gqd_esc($e['vehicle_id']);
        $v   = gqd_row("select vehicle_name from b2b_transfer_master where entry_id='$vid'");
        $vehicle = isset($v['vehicle_name']) ? $v['vehicle_name'] : '';
      }
      $activities[] = array(
        'date'          => function_exists('get_datetime_user') ? get_datetime_user($e['exc_date']) : $e['exc_date'],
        'date_raw'      => isset($e['exc_date']) ? $e['exc_date'] : '',
        'city_name'     => isset($cm['city_name']) ? $cm['city_name'] : '',
        'activity_name' => isset($exc['excursion_name']) ? $exc['excursion_name'] : '',
        'transfer_type' => isset($e['transfer_option']) ? $e['transfer_option'] : '',
        'vehicle_name'  => $vehicle,
        'pax'           => array(
          'adult'  => isset($e['adult']) ? $e['adult'] : 0,
          'chwb'   => isset($e['chwb']) ? $e['chwb'] : 0,
          'chwob'  => isset($e['chwob']) ? $e['chwob'] : 0,
          'infant' => isset($e['infant']) ? $e['infant'] : 0,
        ),
      );
    }

    // =====================================================================
    // 8. VEHICLES / TRANSPORTATION (loop)
    // =====================================================================
    $vehicles = array();
    foreach (gqd_rows("select * from package_tour_quotation_transport_entries2 where quotation_id='$quotation_id'") as $tr) {
      $vid = gqd_esc($tr['vehicle_name']);
      $v   = gqd_row("select vehicle_name from b2b_transfer_master where entry_id='$vid'");
      $vehicles[] = array(
        'vehicle_name'    => isset($v['vehicle_name']) ? $v['vehicle_name'] : '',
        'description'     => isset($tr['service_duration']) ? $tr['service_duration'] : '',
        'service_duration' => isset($tr['service_duration']) ? $tr['service_duration'] : '',
        'date'            => function_exists('get_date_user') ? get_date_user($tr['start_date']) : $tr['start_date'],
        'start_date_raw'  => isset($tr['start_date']) ? $tr['start_date'] : '',
        'end_date_raw'    => isset($tr['end_date']) ? $tr['end_date'] : '',
        'vehicle_type'    => isset($tr['pickup_type']) ? $tr['pickup_type'] : '',
        'vehicle_image'   => isset($v['vehicle_name']) ? $v['vehicle_name'] : '',
        'pickup'          => isset($tr['pickup_type']) ? gqd_pickup_drop($tr['pickup_type'], $tr['pickup']) : '',
        'drop'            => isset($tr['drop_type']) ? gqd_pickup_drop($tr['drop_type'], $tr['drop']) : '',
        'vehicle_count'   => isset($tr['vehicle_count']) ? $tr['vehicle_count'] : '',
      );
    }

    // =====================================================================
    // 9. ITINERARY (loop)
    // =====================================================================
    $itinerary  = array();
    $dates_arr  = (array) (function_exists('get_dates_for_package_itineary') ? get_dates_for_package_itineary($quotation_id) : array());
    $dummy_day_image = 'http://itourscloud.com/quotation_format_images/dummy-image.jpg';
    $imgrow = gqd_row("select image_url from package_tour_quotation_images where quotation_id='$quotation_id'");
    $daywise_image_chunks = array();
    if (!empty($imgrow['image_url'])) {
      $daywise_image_chunks = explode(',', $imgrow['image_url']);
    }
    $i = 0;
    $day_no = 1;
    foreach (gqd_rows("select * from package_quotation_program where quotation_id='$quotation_id'") as $p) {
      $day_image = $dummy_day_image;
      $resolved_day_image = '';

      if (!empty($p['day_image'])) {
        $resolved_day_image = gqd_media_url($p['day_image']);
      }

      if ($resolved_day_image === '' && !empty($daywise_image_chunks)) {
        $lookup_pkg_ids = gqd_itinerary_package_ids($master, $p, $effective_package_id);
        $day_count = intval(isset($p['day_count']) ? $p['day_count'] : $day_no);
        foreach ($daywise_image_chunks as $chunk) {
          if (trim($chunk) === '') {
            continue;
          }
          $parts = explode('=', $chunk);
          if (!isset($parts[1], $parts[2]) || trim($parts[2]) === '') {
            continue;
          }
          if (intval($parts[1]) !== $day_count) {
            continue;
          }
          $map_pkg_id = intval(isset($parts[0]) ? $parts[0] : 0);
          if (!empty($lookup_pkg_ids) && !in_array($map_pkg_id, $lookup_pkg_ids, true) && $map_pkg_id !== 0) {
            continue;
          }
          $resolved_day_image = gqd_media_url($parts[2]);
          if ($resolved_day_image !== '') {
            break;
          }
        }
      }

      if ($resolved_day_image !== '') {
        $day_image = $resolved_day_image;
      }

      $itinerary[] = array(
        'day_number'        => $day_no,
        'day_count'         => isset($p['day_count']) ? $p['day_count'] : $day_no,
        'date'              => isset($dates_arr[$i]) ? $dates_arr[$i] : 'NA',
        'image'             => $day_image,
        'city'              => isset($p['stay']) ? $p['stay'] : '',
        'special_attraction' => isset($p['attraction']) ? $p['attraction'] : '',
        'detailed_programme' => isset($p['day_wise_program']) ? $p['day_wise_program'] : '',
        'meal_plan'         => isset($p['meal_plan']) ? $p['meal_plan'] : '',
        'overnight_stay'    => isset($p['stay']) ? $p['stay'] : '',
      );
      $i++;
      $day_no++;
    }

    // =====================================================================
    // 10. INCLUSIONS / EXCLUSIONS
    // =====================================================================
    $inclusion_exclusion = array(
      'included' => gqd_clean_html_text(isset($master['inclusions']) ? $master['inclusions'] : ''),
      'excluded' => gqd_clean_html_text(isset($master['exclusions']) ? $master['exclusions'] : ''),
      'note'     => gqd_clean_html_text(isset($package['note']) ? $package['note'] : ''),
      'quot_note' => gqd_clean_html_text(isset($quot_note) ? $quot_note : ''),
    );

    // =====================================================================
    // 11. COSTING (group + per person + travel + other)
    // =====================================================================
    $costing_entries = gqd_rows("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id' order by sort_order");

    $group_cost = array();
    $per_person_cost = array();
    foreach ($costing_entries as $ce) {
      // Group cost view (mapping: Group Cost)
      $group_cost[] = array(
        'package_type'        => isset($ce['package_type']) ? $ce['package_type'] : '',
        'tour_cost'           => isset($ce['tour_cost']) ? $ce['tour_cost'] : 0,
        'tax_gst'             => isset($ce['service_tax_subtotal']) ? $ce['service_tax_subtotal'] : '',
        'tcs'                 => isset($ce['service_charge']) ? $ce['service_charge'] : 0,
        'travel_cost'         => isset($ce['transport_cost']) ? $ce['transport_cost'] : 0,
        'grand_total'         => isset($ce['total_tour_cost']) ? $ce['total_tour_cost'] : 0,
      );
      // Per person cost view (mapping: Per Person)
      $per_person_cost[] = array(
        'package_type'  => isset($ce['package_type']) ? $ce['package_type'] : '',
        'pp_adult'      => isset($ce['adult_cost']) ? $ce['adult_cost'] : 0,
        'pp_cwb'        => isset($ce['child_with']) ? $ce['child_with'] : 0,
        'pp_cwnb'       => isset($ce['child_without']) ? $ce['child_without'] : 0,
        'pp_infant'     => isset($ce['infant_cost']) ? $ce['infant_cost'] : 0,
      );
    }

    // ---- Computed costing (mirrors fit_quotation_html business logic) ----------
    // These are the final, display-ready amounts so consumers never have to
    // re-implement the discount / service-charge / tax / TCS / currency math.
    $cur_from = isset($currency) ? $currency : 0;
    $cur_to   = $currency_code;
    $conv = function ($amt) use ($cur_from, $cur_to) {
      return function_exists('currency_conversion')
        ? currency_conversion($cur_from, $cur_to, (float) $amt)
        : number_format((float) $amt, 2);
    };

    $adults   = (float) (isset($master['total_adult']) ? $master['total_adult'] : 0);
    $cwb      = (float) (isset($master['children_with_bed']) ? $master['children_with_bed'] : 0);
    $cwob     = (float) (isset($master['children_without_bed']) ? $master['children_without_bed'] : 0);
    $infants  = (float) (isset($master['total_infant']) ? $master['total_infant'] : 0);

    $visa_cost  = (float) (isset($master['visa_cost']) ? $master['visa_cost'] : 0);
    $guide_cost = (float) (isset($master['guide_cost']) ? $master['guide_cost'] : 0);
    $misc_cost  = (float) (isset($master['misc_cost']) ? $master['misc_cost'] : 0);
    $train_cost_total  = (float) (isset($master['train_cost']) ? $master['train_cost'] : 0);
    $flight_cost_total = (float) (isset($master['flight_cost']) ? $master['flight_cost'] : 0);
    $cruise_cost_total = (float) (isset($master['cruise_cost']) ? $master['cruise_cost'] : 0);

    $group_computed = array();
    $per_person_computed = array();
    $grand_total_numeric = 0;

    foreach ($costing_entries as $ce) {
      $basic       = (float) (isset($ce['basic_amount']) ? $ce['basic_amount'] : 0);
      $service     = (float) (isset($ce['service_charge']) ? $ce['service_charge'] : 0);
      $discount_in = isset($ce['discount_in']) ? $ce['discount_in'] : '';
      $discount    = (float) (isset($ce['discount']) ? $ce['discount'] : 0);
      $act_discount = ($discount_in == 'Percentage')
        ? ($service * $discount / 100)
        : (($service != 0) ? $discount : 0);
      $service_after = $service - $act_discount;

      list($tax_amount, $tax_label) = gqd_parse_service_tax(isset($ce['service_tax_subtotal']) ? $ce['service_tax_subtotal'] : '');
      list($tcs_per, $tcs_value)     = gqd_tcs(isset($ce['bsmValues']) ? $ce['bsmValues'] : '');
      $tcs_value = (float) $tcs_value;

      // ---- Group view ----
      $tour_cost  = $basic + $service_after;
      $g_travel   = $train_cost_total + $flight_cost_total + $cruise_cost_total + $visa_cost + $guide_cost + $misc_cost;
      $g_total    = $basic + $service_after + $tax_amount + $train_cost_total + $cruise_cost_total + $flight_cost_total + $visa_cost + $guide_cost + $misc_cost + $tcs_value;

      $group_computed[] = array(
        'package_type'       => isset($ce['package_type']) ? $ce['package_type'] : '',
        'tour_cost'          => $tour_cost,
        'tour_cost_display'  => $conv($tour_cost),
        'tax_amount'         => $tax_amount,
        'tax_label'          => $tax_label,
        'tax_display'        => trim($tax_label . ' ' . $conv($tax_amount)),
        'tcs_percent'        => $tcs_per,
        'tcs_value'          => $tcs_value,
        'tcs_display'        => $conv($tcs_value),
        'travel_cost'        => $g_travel,
        'travel_display'     => $conv($g_travel),
        'total_cost'         => $g_total,
        'total_display'      => $conv($g_total),
      );

      // ---- Per-person view ----
      $total_pax = ($adults + $cwb + $cwob + $infants);
      $total_pax = ($total_pax != 0) ? $total_pax : 1;
      $per_service = $service_after / $total_pax;

      $adult_final  = ($adults != 0)  ? ((float) (isset($ce['adult_cost']) ? $ce['adult_cost'] : 0) + $per_service) : 0;
      $cwb_final    = ($cwb != 0)     ? ((float) (isset($ce['child_with']) ? $ce['child_with'] : 0) + $per_service) : 0;
      $cwob_final   = ($cwob != 0)    ? ((float) (isset($ce['child_without']) ? $ce['child_without'] : 0) + $per_service) : 0;
      $infant_final = ($infants != 0) ? ((float) (isset($ce['infant_cost']) ? $ce['infant_cost'] : 0) + $per_service) : 0;

      // Per-pax travel components (only when that travel type exists on the quotation)
      $flight_a = ($count_plane > 0)  ? (float) (isset($master['flight_acost']) ? $master['flight_acost'] : 0) : 0;
      $flight_c = ($count_plane > 0)  ? (float) (isset($master['flight_ccost']) ? $master['flight_ccost'] : 0) : 0;
      $flight_i = ($count_plane > 0)  ? (float) (isset($master['flight_icost']) ? $master['flight_icost'] : 0) : 0;
      $train_a  = ($count_train > 0)  ? (float) (isset($master['train_acost']) ? $master['train_acost'] : 0) : 0;
      $train_c  = ($count_train > 0)  ? (float) (isset($master['train_ccost']) ? $master['train_ccost'] : 0) : 0;
      $train_i  = ($count_train > 0)  ? (float) (isset($master['train_icost']) ? $master['train_icost'] : 0) : 0;
      $cruise_a = ($count_cruise > 0) ? (float) (isset($master['cruise_acost']) ? $master['cruise_acost'] : 0) : 0;
      $cruise_c = ($count_cruise > 0) ? (float) (isset($master['cruise_ccost']) ? $master['cruise_ccost'] : 0) : 0;
      $cruise_i = ($count_cruise > 0) ? (float) (isset($master['cruise_icost']) ? $master['cruise_icost'] : 0) : 0;

      $flight_total = $flight_a * $adults + $flight_c * ($cwb + $cwob) + $flight_i * $infants;
      $train_total  = $train_a * $adults + $train_c * ($cwb + $cwob) + $train_i * $infants;
      $cruise_total = $cruise_a * $adults + $cruise_c * ($cwb + $cwob) + $cruise_i * $infants;

      $pax_total = $adult_final * $adults + $cwb_final * $cwb + $cwob_final * $cwob + $infant_final * $infants;
      $other     = $tax_amount + $visa_cost + $guide_cost + $misc_cost;
      $pp_grand  = $pax_total + $flight_total + $train_total + $cruise_total + $other + $tcs_value;

      $per_person_computed[] = array(
        'package_type'       => isset($ce['package_type']) ? $ce['package_type'] : '',
        'pp_adult'           => $adult_final,
        'pp_adult_display'   => $conv($adult_final),
        'pp_cwb'             => $cwb_final,
        'pp_cwb_display'     => $conv($cwb_final),
        'pp_cwnb'            => $cwob_final,
        'pp_cwnb_display'    => $conv($cwob_final),
        'pp_infant'          => $infant_final,
        'pp_infant_display'  => $conv($infant_final),
        'tax_amount'         => $tax_amount,
        'tax_display'        => trim($tax_label . ' ' . $conv($tax_amount)),
        'tcs_percent'        => $tcs_per,
        'tcs_value'          => $tcs_value,
        'tcs_display'        => $conv($tcs_value),
        'visa_display'       => $conv($visa_cost),
        'guide_display'      => $conv($guide_cost),
        'misc_display'       => $conv($misc_cost),
        'travel_per_person'  => array(
          'flight_adult'  => $conv($flight_a),
          'flight_child'  => $conv($flight_c),
          'flight_infant' => $conv($flight_i),
          'train_adult'   => $conv($train_a),
          'train_child'   => $conv($train_c),
          'train_infant'  => $conv($train_i),
          'cruise_adult'  => $conv($cruise_a),
          'cruise_child'  => $conv($cruise_c),
          'cruise_infant' => $conv($cruise_i),
        ),
        'grand_total'        => $pp_grand,
        'grand_total_display' => $conv($pp_grand),
      );

      // Quotation grand total uses the relevant view for its costing type.
      $grand_total_numeric += ((isset($master['costing_type']) && $master['costing_type'] == 1) ? $g_total : $pp_grand);
    }

    $costing = array(
      'costing_type'       => isset($master['costing_type']) ? $master['costing_type'] : '',
      'costing_type_label' => (isset($master['costing_type']) && $master['costing_type'] == 1) ? 'Group' : 'Per Person',
      'currency'           => isset($currency) ? $currency : '',
      'currency_code'      => $currency_code,
      'entries_raw'        => $costing_entries,
      'group'              => $group_cost,
      'per_person'         => $per_person_cost,
      'computed'           => array(
        'group'        => $group_computed,
        'per_person'   => $per_person_computed,
        'grand_total'  => $grand_total_numeric,
        'grand_total_display' => $conv($grand_total_numeric),
      ),
      'travel'             => array(
        'flight_adult'  => isset($master['flight_acost']) ? $master['flight_acost'] : 0,
        'flight_child'  => isset($master['flight_ccost']) ? $master['flight_ccost'] : 0,
        'flight_infant' => isset($master['flight_icost']) ? $master['flight_icost'] : 0,
        'train_adult'   => isset($master['train_acost']) ? $master['train_acost'] : 0,
        'train_child'   => isset($master['train_ccost']) ? $master['train_ccost'] : 0,
        'train_infant'  => isset($master['train_icost']) ? $master['train_icost'] : 0,
        'cruise_adult'  => isset($master['cruise_acost']) ? $master['cruise_acost'] : 0,
        'cruise_child'  => isset($master['cruise_ccost']) ? $master['cruise_ccost'] : 0,
        'cruise_infant' => isset($master['cruise_icost']) ? $master['cruise_icost'] : 0,
      ),
      'other'              => array(
        'visa_cost'             => isset($master['visa_cost']) ? $master['visa_cost'] : 0,
        'guide_cost'            => isset($master['guide_cost']) ? $master['guide_cost'] : 0,
        'misc_cost'             => isset($master['misc_cost']) ? $master['misc_cost'] : 0,
        'misc_description'      => isset($master['other_desc']) ? $master['other_desc'] : '',
        'discount'              => isset($master['discount']) ? $master['discount'] : 0,
      ),
    );

    // =====================================================================
    // 12. BANK DETAILS
    // =====================================================================
    //========================== Dipti
    $qr_code = isset($app['qr_url']) ? trim($app['qr_url']) : '';

    if (!empty($qr_code)) {
      $qr_code = str_replace('\\', '/', $qr_code);

      $qr_code = str_replace('../../../../', '', $qr_code);
      $qr_code = str_replace('../../../', '', $qr_code);
      $qr_code = str_replace('../../', '', $qr_code);
      $qr_code = str_replace('../', '', $qr_code);

      $qr_code = preg_replace('/\/+/', '/', $qr_code);
      $qr_code = BASE_URL . $qr_code;
    }

    //=============================
    $bank_details = array(
      'bank_name'      => !empty($bank['bank_name']) ? $bank['bank_name'] : (isset($bank_name_setting) ? $bank_name_setting : ''),
      'account_name'   => !empty($bank['account_name']) ? $bank['account_name'] : (isset($bank_account_name) ? $bank_account_name : ''),
      'account_no'     => !empty($bank['account_no']) ? $bank['account_no'] : (isset($bank_acc_no) ? $bank_acc_no : ''),
      'account_type'   => !empty($bank['account_type']) ? $bank['account_type'] : (isset($acc_name) ? $acc_name : ''),
      'branch_name'    => !empty($bank['branch_name']) ? $bank['branch_name'] : (isset($bank_branch_name) ? $bank_branch_name : ''),
      'ifsc_code'      => !empty($bank['ifsc_code']) ? $bank['ifsc_code'] : (isset($bank_ifsc_code) ? $bank_ifsc_code : ''),
      'swift_code'     => !empty($bank['swift_code']) ? $bank['swift_code'] : (isset($bank_swift_code) ? $bank_swift_code : ''),
      // 'upi_id'         => isset($bank['upi_id']) ? $bank['upi_id'] : '',
      'upi_id' => isset($bank['upi_id']) ? $bank['upi_id'] : '', //======= Dipti
      // 'qr_code'        => isset($app['qr_url']) ? $app['qr_url'] : '',
      'qr_code' => $qr_code,
      'branch_qr_url'  => $branch_qr_url,
      'qr_available'   => (function_exists('check_qr') && check_qr($branch_admin_id) === true) || !empty($branch_qr_url) || !empty($app['qr_url']),
      'qr_html'        => function_exists('get_qr') ? get_qr('Protrait Standard', $branch_admin_id) : '',
    );

    // =====================================================================
    // 13. TERMS & CONDITIONS
    // =====================================================================
    $terms_conditions = array(
      'title'                => isset($terms['title']) ? $terms['title'] : '',
      'terms_and_conditions' => isset($terms['terms_and_conditions']) ? $terms['terms_and_conditions'] : '',
      'dest_id'              => $dest_id,
    );

    // =====================================================================
    // 14. THANK YOU / COMPANY CONTACT
    // =====================================================================
    // Old templates use the logged-in session branch, but this API should
    // stay tied to the quotation itself. Use the quotation branch when
    // branch-wise filtering is enabled and the quotation has a branch.
    $use_branch = ($branch_status == 'yes' && $branch_admin_id != '0');
    $thank_you = array(
      'company_logo'   => $branch_logo_url,
      'company_name'   => $company_name,
      'company_address' => ($use_branch && !empty($branch_details['address1']))
        ? trim($branch_details['address1'] . ',' . $branch_details['address2'] . ',' . $branch_details['city'], ',')
        : (isset($app['app_address']) ? $app['app_address'] : ''),
      'company_email'  => ($use_branch && !empty($branch_details['email_id'])) ? $branch_details['email_id'] : (isset($app['app_email_id']) ? $app['app_email_id'] : ''),
      'company_contact' => ($use_branch && !empty($branch_details['contact_no'])) ? $branch_details['contact_no'] : (isset($app_contact_no) ? $app_contact_no : ''),
      'website'        => isset($app['app_website']) ? $app['app_website'] : '',
      'user_mobile'    => isset($emp['mobile_no']) ? $emp['mobile_no'] : '',
      'prepared_by'    => $emp_name,
      'quotation_id'   => $master['quotation_id'],
      'quotation_code' => $display_id,
      'issue_date'     => function_exists('get_date_user') ? get_date_user($quotation_date) : $quotation_date,
    );

    $branch = array(
      'branch_admin_id' => $branch_admin_id,
      'branch_status'   => $branch_status,
      'session_role'    => $session_role,
      'branch_name'     => isset($branch_details['branch_name']) ? $branch_details['branch_name'] : '',
      'company_name'    => $company_name,
      'logo_url'        => $branch_logo_url,
      'qr_url'          => $branch_qr_url,
      'address'         => trim(
        (isset($branch_details['address1']) ? $branch_details['address1'] : '') . ',' .
          (isset($branch_details['address2']) ? $branch_details['address2'] : '') . ',' .
          (isset($branch_details['city']) ? $branch_details['city'] : ''),
        ','
      ),
      'email_id'        => isset($branch_details['email_id']) ? $branch_details['email_id'] : '',
      'contact_no'      => isset($branch_details['contact_no']) ? $branch_details['contact_no'] : '',
    );

    // ========= Testimonial
    $testimonials = array();

    $sq_testm = gqd_rows("SELECT * FROM quotation_testimonial WHERE active_flag='Active' ORDER BY testimonial_id ASC");

    foreach ($sq_testm as $t) {
      $testimonials[] = array(
        'testimonial_id' => $t['testimonial_id'],
        'name' => $t['name'],
        'designation' => $t['designation'],
        'review' => $t['review'],
        'photo' => $t['photo']
      );
    }
    // =====================
    // =====================================================================
    // ASSEMBLE
    // =====================================================================
    return array(
      'gallery_images' => $gallery_images,
      'found'        => true,
      'quotation_id' => $master['quotation_id'],
      'quotation_code' => $display_id,
      'currency'     => isset($currency) ? $currency : '',
      'currency_code' => $currency_code,
      'testimonials' => $testimonials,
      'sections_present' => array(
        'hotels'     => $count_hotel > 0,
        'flights'    => $count_plane > 0,
        'trains'     => $count_train > 0,
        'cruises'    => $count_cruise > 0,
        'activities' => $count_exc > 0,
        'vehicles'   => $count_transport > 0,
        'itinerary'  => $count_program > 0,
      ),
      // Explicit row counts so consumers can confirm EVERY record is
      // returned (each section below is a full loop, never a single row).
      'counts' => array(
        'hotels'     => count($hotels),
        'flights'    => count($flights),
        'trains'     => count($trains),
        'cruises'    => count($cruises),
        'activities' => count($activities),
        'vehicles'   => count($vehicles),
        'itinerary'  => count($itinerary),
        'costing'    => count($costing_entries),
      ),
      'hero'                 => $hero,
      'tour_overview'        => $overview,
      'hotels'               => $hotels,
      'flights'              => $flights,
      'trains'               => $trains,
      'cruises'              => $cruises,
      'activities'           => $activities,
      'vehicles'             => $vehicles,
      'itinerary'            => $itinerary,
      'inclusion_exclusion'  => $inclusion_exclusion,
      'costing'              => $costing,
      'bank_details'         => $bank_details,
      'terms_conditions'     => $terms_conditions,
      'thank_you'            => $thank_you,
      'branch'               => $branch,
      // raw master row for any field not explicitly surfaced above
      'raw_master'           => $master,
    );
  }
}
