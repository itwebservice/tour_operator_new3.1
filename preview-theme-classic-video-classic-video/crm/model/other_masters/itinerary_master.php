<?php
class itinerary_master
{

  private function ensure_utf8($text)
  {
    if ($text === null) {
      return '';
    }
    $text = (string)$text;
    // If already valid UTF-8, keep as is.
    if (preg_match('//u', $text)) {
      return $text;
    }
    // Try mbstring conversion first (if available).
    if (function_exists('mb_convert_encoding')) {
      $converted = @mb_convert_encoding($text, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
      if ($converted !== false && preg_match('//u', $converted)) {
        return $converted;
      }
    }
    // Fallback for CP1252/Latin1-like input.
    if (function_exists('iconv')) {
      $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $text);
      if ($converted !== false) {
        return $converted;
      }
    }
    return utf8_encode($text);
  }

  public function csv_save()
  {
    $itinerary_csv_dir = $_POST['itinerary_csv_dir'];
    $itinerary_arr = array();
    $flag = true;

    $itinerary_csv_dir = trim($itinerary_csv_dir);
    $csv_file_path = '';

    // Resolve uploaded CSV path robustly (relative paths differ by caller context).
    if ($itinerary_csv_dir != '') {
      $candidates = array();

      // 1) Direct (absolute or already-resolved relative path)
      $candidates[] = $itinerary_csv_dir;

      // 2) Path relative to this model file
      $candidates[] = __DIR__ . '/' . $itinerary_csv_dir;

      // 3) Path relative to itinerary view folder (where upload script runs)
      $candidates[] = dirname(__DIR__, 2) . '/view/other_masters/itinerary/' . $itinerary_csv_dir;

      // 4) If it contains 'uploads', map to crm/uploads and project root uploads
      if (strpos($itinerary_csv_dir, 'uploads') !== false) {
        $uploads_split = explode('uploads', $itinerary_csv_dir, 2);
        if (isset($uploads_split[1])) {
          $uploads_suffix = $uploads_split[1];
          $candidates[] = dirname(__DIR__, 2) . '/uploads' . $uploads_suffix;     // crm/uploads
          $candidates[] = dirname(__DIR__, 3) . '/uploads' . $uploads_suffix;     // project-root/uploads
        }
      }

      foreach ($candidates as $candidate) {
        $resolved = realpath($candidate);
        if ($resolved !== false && is_file($resolved) && is_readable($resolved)) {
          $csv_file_path = $resolved;
          break;
        }
      }
    }

    begin_t();
    $count = 1;
    $arrResult  = array();
    $handle = ($csv_file_path != '' && file_exists($csv_file_path)) ? fopen($csv_file_path, "r") : false;

    // Direct absolute/relative path fallback (upload script now returns absolute path).
    if ($handle === false && $itinerary_csv_dir != '' && file_exists($itinerary_csv_dir)) {
      $handle = fopen($itinerary_csv_dir, "r");
    }

    // Last fallback: try URL fopen if enabled.
    if ($handle === false && strpos($itinerary_csv_dir, 'uploads') !== false) {
      $uploads_split = explode('uploads', $itinerary_csv_dir, 2);
      if (isset($uploads_split[1])) {
        $csv_url = BASE_URL . 'uploads' . $uploads_split[1];
        $handle = @fopen($csv_url, "r");
      }
    }

    if (empty($handle) === false) {

      while (($data = fgetcsv($handle, 10000, ",", "\"", "\\")) !== FALSE) {
        if ($count == 1) {
          $count++;
          continue;
        }
        if ($count > 0) {
          if (count($data) < 4) {
            $count++;
            continue;
          }
          $spa = str_replace('"', "'", $data[1]);
          $dwp = str_replace('"', "'", $data[2]);
          $os = str_replace('"', "'", $data[3]);
          $spa = $this->ensure_utf8($spa);
          $dwp = $this->ensure_utf8($dwp);
          $os = $this->ensure_utf8($os);
          $arr = array(
            "spa" => addslashes($spa),
            "dwp" => addslashes($dwp),
            "os" => addslashes($os)
          );
          array_push($itinerary_arr, $arr);
        }
        $count++;
      }
      fclose($handle);
    }
    $json_flags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE;
    if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
      $json_flags = $json_flags | JSON_INVALID_UTF8_SUBSTITUTE;
    }
    $itinerary_arr = json_encode($itinerary_arr, $json_flags);
    $itinerary_arr = ($itinerary_arr !== false && $itinerary_arr != '') ? $itinerary_arr : json_encode(array());
    echo "<input type='hidden' value='$itinerary_arr' id='itinerary_arr' name='itinerary_arr'/>";
  }
  function itinerary_save()
  {

    $dest_id = $_POST['dest_id'];
    $sp_arr = $_POST['sp_arr'];
    $dwp_arr = $_POST['dwp_arr'];
    $os_arr = $_POST['os_arr'];

    $sq_repc = mysqli_num_rows(mysqlQuery("select dest_id from itinerary_master where dest_id='$dest_id'"));
    if ($sq_repc > 0) {
      echo "error--Itinerary already added for this destination.Please update the same!";
    } else {
      for ($i = 0; $i < sizeof($dwp_arr); $i++) {

        $sp_arr1 = addslashes($sp_arr[$i]);
        $dwp_arr1 = addslashes($dwp_arr[$i]);
        $os_arr1 = addslashes($os_arr[$i]);
        $sq = mysqlQuery("select max(entry_id) as max from itinerary_master");
        $value = mysqli_fetch_assoc($sq);
        $entry_id = $value['max'] + 1;

        $sq1 = mysqlQuery("insert into itinerary_master(`entry_id`, `dest_id`, `special_attraction`, `daywise_program`, `overnight_stay`)values('$entry_id','$dest_id','$sp_arr1', '$dwp_arr1', '$os_arr1')");
        if (!$sq1) {
          $GLOBALS['flag'] = false;
          echo "error--Error in Itinerary at row " . $i + 1;
        }
      }
      echo "Itinerary saved successfully!";
    }
  }
  function itinerary_update()
  {

    $dest_id = $_POST['dest_id'];
    $sp_arr = $_POST['sp_arr'];
    $dwp_arr = $_POST['dwp_arr'];
    $os_arr = $_POST['os_arr'];
    $checked_arr = $_POST['checked_arr'];
    $entry_id_arr = $_POST['entry_id_arr'];

    for ($i = 0; $i < sizeof($dwp_arr); $i++) {

      if ($checked_arr[$i] != 'true') {
        $sq_exc = mysqlQuery("delete from itinerary_master where entry_id='$entry_id_arr[$i]'");
        if (!$sq_exc) {
          echo "error--Itinerary information not deleted!";
          exit;
        }
      } else {
        $sp_arr1 = addslashes($sp_arr[$i]);
        $dwp_arr1 = addslashes($dwp_arr[$i]);
        $os_arr1 = addslashes($os_arr[$i]);
        if ($entry_id_arr[$i] == "") {

          $sq = mysqlQuery("select max(entry_id) as max from itinerary_master");
          $value = mysqli_fetch_assoc($sq);
          $entry_id = $value['max'] + 1;
          $sq1 = mysqlQuery("insert into itinerary_master(`entry_id`, `dest_id`, `special_attraction`, `daywise_program`, `overnight_stay`)values('$entry_id','$dest_id','$sp_arr1', '$dwp_arr1', '$os_arr1')");
        } else {

          $sq1 = mysqlQuery("update itinerary_master set `special_attraction`='$sp_arr1', `daywise_program`='$dwp_arr1', `overnight_stay`='$os_arr1' where entry_id='$entry_id_arr[$i]'");
        }
        if (!$sq1) {
          $GLOBALS['flag'] = false;
          echo "error--Error in Itinerary at row " . $i + 1;
        }
      }
    }
    echo "Itinerary updated successfully!";
  }
}
