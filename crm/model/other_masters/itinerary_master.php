<?php
class itinerary_master{

    public function csv_save()
    {
        $itinerary_csv_dir = isset($_POST['itinerary_csv_dir']) ? trim(strip_tags($_POST['itinerary_csv_dir'])) : '';
        $itinerary_arr = array();

        $parts = explode('uploads', $itinerary_csv_dir);
        if ($itinerary_csv_dir !== '' && isset($parts[1]) && $parts[1] !== '') {
            // Read from disk. fopen(BASE_URL) hits Apache over HTTP and often fails on local XAMPP.
            $itinerary_csv_dir = str_replace('\\', '/', CSV_READ_URL.'uploads'.$parts[1]);
            $itinerary_csv_dir = preg_replace('#/+#', '/', $itinerary_csv_dir);

            $handle = @fopen($itinerary_csv_dir, "r");
            if ($handle !== false) {
                $count = 0;
                while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                    $count++;
                    if (!is_array($data) || empty($data)) {
                        continue;
                    }
                    // Strip UTF-8 BOM from the first cell
                    if (isset($data[0])) {
                        $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
                    }
                    // Skip header
                    if ($count === 1) {
                        continue;
                    }
                    $spa = isset($data[1]) ? trim($data[1]) : '';
                    $dwp = isset($data[2]) ? trim($data[2]) : '';
                    $os  = isset($data[3]) ? trim($data[3]) : '';
                    if ($spa === '' && $dwp === '' && $os === '') {
                        continue;
                    }
                    $itinerary_arr[] = array(
                        "spa" => $spa,
                        "dwp" => $dwp,
                        "os"  => $os
                    );
                }
                fclose($handle);
            }
        }

        $json = json_encode($itinerary_arr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        if ($json === false || $json === '') {
            $json = '[]';
        }
        // Textarea keeps JSON out of an HTML attribute so quotes/apostrophes cannot truncate the value
        echo '<textarea id="itinerary_arr" name="itinerary_arr" style="display:none">'.$json.'</textarea>';
    }
    function itinerary_save(){

        $dest_id = $_POST['dest_id'];
        $sp_arr = $_POST['sp_arr'];
        $dwp_arr = $_POST['dwp_arr'];
        $os_arr = $_POST['os_arr'];
        $img_arr = isset($_POST['img_arr']) ? $_POST['img_arr'] : array();
        
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
        $img_arr = isset($_POST['img_arr']) && is_array($_POST['img_arr']) ? $_POST['img_arr'] : array();

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