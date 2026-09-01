<?php
class itinerary_master{

    public function csv_save()
    {
        $itinerary_csv_dir = isset($_POST['itinerary_csv_dir']) ? trim(strip_tags($_POST['itinerary_csv_dir'])) : '';
        $itinerary_arr = array();
        $file_path = $this->resolve_itinerary_csv_path($itinerary_csv_dir);

        if ($file_path !== '') {
            $handle = @fopen($file_path, 'r');
            if ($handle !== false) {
                $first_line = fgets($handle);
                if ($first_line !== false) {
                    $first_line = preg_replace('/^\xEF\xBB\xBF/', '', $first_line);
                    $comma = count(str_getcsv($first_line, ','));
                    $semi  = count(str_getcsv($first_line, ';'));
                    $tab   = count(str_getcsv($first_line, "\t"));
                    $delim = ',';
                    if ($semi > $comma && $semi >= $tab) {
                        $delim = ';';
                    } elseif ($tab > $comma && $tab >= $semi) {
                        $delim = "\t";
                    }

                    $header = str_getcsv($first_line, $delim);
                    $idx_spa = 1;
                    $idx_dwp = 2;
                    $idx_os  = 3;
                    $is_header = false;
                    if (is_array($header)) {
                        foreach ($header as $i => $col) {
                            $n = strtolower(trim(str_replace(array('-', '_'), ' ', (string)$col)));
                            $n = preg_replace('/\s+/', ' ', $n);
                            if (strpos($n, 'special') !== false) {
                                $idx_spa = $i;
                                $is_header = true;
                            }
                            if (strpos($n, 'daywise') !== false || strpos($n, 'day wise') !== false || (strpos($n, 'program') !== false && strpos($n, 'special') === false)) {
                                $idx_dwp = $i;
                                $is_header = true;
                            }
                            if (strpos($n, 'overnight') !== false) {
                                $idx_os = $i;
                                $is_header = true;
                            }
                            if ($n === 'sr.no' || $n === 'sr no' || $n === 'srno' || $n === 's.no') {
                                $is_header = true;
                            }
                        }
                    }

                    if (!$is_header) {
                        $this->push_itinerary_csv_row($itinerary_arr, $header, $idx_spa, $idx_dwp, $idx_os);
                    }

                    while (($data = fgetcsv($handle, 0, $delim)) !== false) {
                        $this->push_itinerary_csv_row($itinerary_arr, $data, $idx_spa, $idx_dwp, $idx_os);
                    }
                }
                fclose($handle);
            }
        }

        $json = json_encode($itinerary_arr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        if ($json === false || $json === '') {
            $json = '[]';
        }
        echo '<textarea id="itinerary_arr" name="itinerary_arr" style="display:none">'.$json.'</textarea>';
    }

    private function resolve_itinerary_csv_path($posted)
    {
        if ($posted === '') {
            return '';
        }
        $posted = str_replace('\\', '/', trim($posted));
        $crm_root = realpath(__DIR__ . '/..');
        $crm_root = $crm_root ? str_replace('\\', '/', $crm_root) : str_replace('\\', '/', rtrim(CSV_READ_URL, '/\\'));

        $candidates = array();
        $uploads_pos = stripos($posted, 'uploads/');
        if ($uploads_pos === false) {
            $uploads_pos = stripos($posted, 'uploads');
        }
        if ($uploads_pos !== false) {
            $from_uploads = substr($posted, $uploads_pos);
            $from_uploads = preg_replace('#^uploads(?!/)#i', 'uploads/', $from_uploads);
            $candidates[] = $crm_root . '/' . ltrim($from_uploads, '/');
            $candidates[] = str_replace('\\', '/', rtrim(CSV_READ_URL, '/\\')) . '/' . ltrim($from_uploads, '/');
        }
        $candidates[] = $posted;
        $candidates[] = $crm_root . '/' . ltrim($posted, '/');

        foreach ($candidates as $path) {
            $path = preg_replace('#/+#', '/', $path);
            $real = @realpath($path);
            if ($real !== false && is_file($real) && is_readable($real)) {
                return $real;
            }
            if (is_file($path) && is_readable($path)) {
                return $path;
            }
        }
        return '';
    }

    private function push_itinerary_csv_row(&$itinerary_arr, $data, $idx_spa, $idx_dwp, $idx_os)
    {
        if (!is_array($data) || empty($data)) {
            return;
        }
        if (isset($data[0])) {
            $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
        }
        $spa = isset($data[$idx_spa]) ? trim($data[$idx_spa]) : '';
        $dwp = isset($data[$idx_dwp]) ? trim($data[$idx_dwp]) : '';
        $os  = isset($data[$idx_os]) ? trim($data[$idx_os]) : '';
        if ($spa === '' && $dwp === '' && $os === '') {
            return;
        }
        $itinerary_arr[] = array(
            'spa' => $spa,
            'dwp' => $dwp,
            'os'  => $os
        );
    }
    function itinerary_save(){

        $dest_id = $_POST['dest_id'];
        $sp_arr = $_POST['sp_arr'];
        $dwp_arr = $_POST['dwp_arr'];
        $os_arr = $_POST['os_arr'];
        $img_arr = isset($_POST['img_arr']) ? $_POST['img_arr'] : array();
        if (is_string($img_arr)) {
            $decoded = json_decode($img_arr, true);
            $img_arr = is_array($decoded) ? $decoded : array();
        }
        if (!is_array($img_arr)) {
            $img_arr = array();
        }
        
        $sq_repc = mysqli_num_rows(mysqlQuery("select dest_id from itinerary_master where dest_id='$dest_id'"));
        if($sq_repc > 0 ){
            echo "error--Itinerary already added for this destination.Please update the same!";
        }
        else{
            for($i=0; $i<sizeof($dwp_arr); $i++){

                $sp_arr1 = addslashes($sp_arr[$i]);
                $dwp_arr1 = addslashes($dwp_arr[$i]);
                $os_arr1 = addslashes($os_arr[$i]);
                $sq = mysqlQuery("select max(entry_id) as max from itinerary_master");
                $value = mysqli_fetch_assoc($sq);
                $entry_id = $value['max'] + 1;

                $img_val = isset($img_arr[$i]) ? addslashes($img_arr[$i]) : '';
                $columns = "`entry_id`, `dest_id`, `special_attraction`, `daywise_program`, `overnight_stay`";
                $values = "('$entry_id','$dest_id','$sp_arr1', '$dwp_arr1', '$os_arr1')";
                $sq_cols = mysqlQuery("SHOW COLUMNS FROM itinerary_master LIKE 'itinerary_image'");
                if(mysqli_num_rows($sq_cols) > 0){
                    $columns .= ", `itinerary_image`";
                    $values = "('$entry_id','$dest_id','$sp_arr1', '$dwp_arr1', '$os_arr1', '$img_val')";
                }
                $sq1 = mysqlQuery("insert into itinerary_master($columns) values $values");
                if(!$sq1){
                    $GLOBALS['flag'] = false;
                    echo "error--Error in Itinerary at row ".$i+1;
                }
            }
            echo "Itinerary saved successfully!";
        }
    }
    function itinerary_update(){

        $dest_id = $_POST['dest_id'];
        $sp_arr = $_POST['sp_arr'];
        $dwp_arr = $_POST['dwp_arr'];
        $os_arr = $_POST['os_arr'];
        $checked_arr = $_POST['checked_arr'];
        $entry_id_arr = $_POST['entry_id_arr'];
        $img_arr = isset($_POST['img_arr']) ? $_POST['img_arr'] : array();
        if (is_string($img_arr)) {
            $decoded = json_decode($img_arr, true);
            $img_arr = is_array($decoded) ? $decoded : array();
        }
        if (!is_array($img_arr)) {
            $img_arr = array();
        }

        for($i=0; $i<sizeof($dwp_arr); $i++){

            if($checked_arr[$i] != 'true'){
                $sq_exc = mysqlQuery("delete from itinerary_master where entry_id='$entry_id_arr[$i]'");
				if(!$sq_exc){
					echo "error--Itinerary information not deleted!";
					exit;
				}
            }
            else{
                $sp_arr1 = addslashes($sp_arr[$i]);
                $dwp_arr1 = addslashes($dwp_arr[$i]);
                $os_arr1 = addslashes($os_arr[$i]);
                $img_path1 = isset($img_arr[$i]) ? addslashes($img_arr[$i]) : '';
                
                if($entry_id_arr[$i]==""){

                    $sq = mysqlQuery("select max(entry_id) as max from itinerary_master");
                    $value = mysqli_fetch_assoc($sq);
                    $entry_id = $value['max'] + 1;
                    
                    // Check if itinerary_image column exists before inserting
                    $column_exists_query = mysqlQuery("SHOW COLUMNS FROM `itinerary_master` LIKE 'itinerary_image'");
                    if (mysqli_num_rows($column_exists_query) > 0) {
                        $sq1 = mysqlQuery("insert into itinerary_master(`entry_id`, `dest_id`, `special_attraction`, `daywise_program`, `overnight_stay`, `itinerary_image`)values('$entry_id','$dest_id','$sp_arr1', '$dwp_arr1', '$os_arr1', '$img_path1')");
                    } else {
                        $sq1 = mysqlQuery("insert into itinerary_master(`entry_id`, `dest_id`, `special_attraction`, `daywise_program`, `overnight_stay`)values('$entry_id','$dest_id','$sp_arr1', '$dwp_arr1', '$os_arr1')");
                    }
                }
                else{
                    // Check if itinerary_image column exists before updating
                    $column_exists_query = mysqlQuery("SHOW COLUMNS FROM `itinerary_master` LIKE 'itinerary_image'");
                    if (mysqli_num_rows($column_exists_query) > 0) {
                        $sq1 = mysqlQuery("update itinerary_master set `special_attraction`='$sp_arr1', `daywise_program`='$dwp_arr1', `overnight_stay`='$os_arr1', `itinerary_image`='$img_path1' where entry_id='$entry_id_arr[$i]'");
                    } else {
                        $sq1 = mysqlQuery("update itinerary_master set `special_attraction`='$sp_arr1', `daywise_program`='$dwp_arr1', `overnight_stay`='$os_arr1' where entry_id='$entry_id_arr[$i]'");
                    }
                }
                if(!$sq1){
                    $GLOBALS['flag'] = false;
                    echo "error--Error in Itinerary at row ".$i+1;
                }
            }
        }
        echo "Itinerary updated successfully!";
        
    }
}
?>