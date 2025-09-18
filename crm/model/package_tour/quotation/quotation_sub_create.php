<?php 

class quotation_sub_create
{
    public function quotation_sub_master_create()
    {
        $branch_admin_id = $_SESSION['branch_admin_id'];
        $quotation_id = $_POST['quotation_id'];
        $cols = array();

        // Get original quotation details
        $result = mysqlQuery("SELECT * FROM package_tour_quotation_master WHERE quotation_id='$quotation_id'");
        $original_quotation = mysqli_fetch_assoc($result);
        
        if (!$original_quotation) {
            echo "error--Original quotation not found.";
            exit;
        }
        
        // Debug: Log original quotation details
        error_log("Original quotation details - ID: " . $quotation_id . ", Email: " . ($original_quotation['email_id'] ?? 'NULL') . ", Mobile: " . ($original_quotation['mobile_no'] ?? 'NULL'));

        // Get quotation ID format for versioning
        $quotation_date = $original_quotation['quotation_date'];
        $yr = explode("-", $quotation_date);
        $year = $yr[0];
        
        // Ensure year is valid, fallback to current year
        if (empty($year) || !is_numeric($year)) {
            $year = date('Y');
        }
        
        // Check if this is already a sub-quotation using database fields
        $is_sub_quotation = isset($original_quotation['is_sub_quotation']) && $original_quotation['is_sub_quotation'] == '1';
        $parent_quotation_id = isset($original_quotation['parent_quotation_id']) ? $original_quotation['parent_quotation_id'] : null;
        
        if ($is_sub_quotation && $parent_quotation_id && $parent_quotation_id != '0') {
            // If it's already a sub-quotation, find the original parent and create next version
            $parent_quotation = mysqli_fetch_assoc(mysqlQuery("SELECT quotation_date FROM package_tour_quotation_master WHERE quotation_id='$parent_quotation_id'"));
            if ($parent_quotation) {
                $parent_year = explode("-", $parent_quotation['quotation_date'])[0];
                $parent_id_display = get_quotation_id($parent_quotation_id, $parent_year);
                
                // Count existing sub-quotations for this parent to get next version number
                $sub_count = mysqli_num_rows(mysqlQuery("SELECT quotation_id FROM package_tour_quotation_master WHERE parent_quotation_id='$parent_quotation_id'"));
                $new_version = $sub_count + 1;
                $new_quotation_id_display = $parent_id_display . '.' . $new_version;
            } else {
                // Fallback: use original quotation ID
                $original_quotation_id_display = get_quotation_id($quotation_id, $year);
                $new_quotation_id_display = $original_quotation_id_display . '.1';
            }
        } else {
            // If it's the original quotation, count existing sub-quotations and create next version
            $original_quotation_id_display = get_quotation_id($quotation_id, $year);
            
            // Count existing sub-quotations for this parent
            $sub_count = mysqli_num_rows(mysqlQuery("SELECT quotation_id FROM package_tour_quotation_master WHERE parent_quotation_id='$quotation_id'"));
            $new_version = $sub_count + 1;
            $new_quotation_id_display = $original_quotation_id_display . '.' . $new_version;
        }
        

        // Get all columns from the table
        $result = mysqlQuery("SHOW COLUMNS FROM package_tour_quotation_master"); 
        while ($r = mysqli_fetch_assoc($result)) {
            $cols[] = $r["Field"];
        }

        // Create new quotation with version number
        $insertSQL = "INSERT INTO package_tour_quotation_master (".implode(", ", $cols).") VALUES (";
        $count = count($cols);
        
        // Get new quotation ID
        $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(quotation_id) as max from package_tour_quotation_master"));
        $quotation_max = $sq_max['max'] + 1;
        
        foreach($cols as $counter => $col) {
            if($col == 'quotation_id') {
                $insertSQL .= "'".$quotation_max."'";
            }
            else if($col == 'quotation_id_display') {
                // Set the versioned quotation ID display
                $insertSQL .= "'".$new_quotation_id_display."'";
            }
            else if($col == 'quotation_display_id') {
                // Set the versioned quotation display ID
                $insertSQL .= "'".$new_quotation_id_display."'";
            }
            else if($col == 'other_desc') {
                $other_desc = addslashes($original_quotation[$col]);
                $insertSQL .= "'".$other_desc."'";
            }
            else if($col == 'inclusions' || $col == 'exclusions') {
                $incl_excl = addslashes($original_quotation[$col]);
                $insertSQL .= "'".$incl_excl."'";
            }
            else if($col == 'created_at' || $col == 'quotation_date') {
                $insertSQL .= "'".date('Y-m-d')."'";
            }
            else if($col == 'parent_quotation_id') {
                // Always set parent quotation ID to the original quotation being copied
                $insertSQL .= "'".$quotation_id."'";
            }
            else if($col == 'is_sub_quotation') {
                // Always mark as sub-quotation
                $insertSQL .= "'1'";
            }
            else {
                $insertSQL .= "'".$original_quotation[$col]."'";
            }

            if ($counter < $count - 1) {
                $insertSQL .= ", ";
            }
        }
        $insertSQL .= ")";
        
        $sq_quot = mysqlQuery($insertSQL);

        if(!$sq_quot) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Sub-quotation has not been created.'
            ]);
            exit;
        }
        else {
            // Clone all related entries
            $this->clone_train_entries($quotation_id, $quotation_max);
            $this->clone_plane_entries($quotation_id, $quotation_max);
            $this->clone_cruise_entries($quotation_id, $quotation_max);
            $this->clone_hotel_entries($quotation_id, $quotation_max);
            $this->clone_transport_entries($quotation_id, $quotation_max);
            $this->clone_excursion_entries($quotation_id, $quotation_max);
            $this->clone_costing_entries($quotation_id, $quotation_max);
            $this->clone_program_entries($quotation_id, $quotation_max);
            $this->clone_images_entries($quotation_id, $quotation_max);
            
            // Debug: Log sub-quotation creation
            error_log("Sub-quotation created successfully: ID=$quotation_max, Display ID=$new_quotation_id_display, Parent=$quotation_id");
            
            // Debug: Log final sub-quotation details
            $final_check = mysqli_fetch_assoc(mysqlQuery("SELECT quotation_id, email_id, mobile_no, is_sub_quotation, parent_quotation_id, quotation_display_id FROM package_tour_quotation_master WHERE quotation_id='$quotation_max'"));
            error_log("Sub-quotation created - ID: " . $quotation_max . ", Display ID: " . ($final_check['quotation_display_id'] ?? 'NULL') . ", Email: " . ($final_check['email_id'] ?? 'NULL') . ", Mobile: " . ($final_check['mobile_no'] ?? 'NULL') . ", Parent: " . ($final_check['parent_quotation_id'] ?? 'NULL') . ", Is Sub: " . ($final_check['is_sub_quotation'] ?? 'NULL'));
            
            // Return JSON response with the new quotation ID
            echo json_encode([
                'status' => 'success',
                'message' => 'Sub-quotation has been successfully created with ID: ' . $new_quotation_id_display,
                'quotation_id' => $quotation_max,
                'quotation_id_display' => $new_quotation_id_display
            ]);
        }
    }

    // Clone all related entries (same as original clone functions)
    public function clone_transport_entries($quotation_id, $quotation_max)
    {
        $cols = array();
        $result = mysqlQuery("SHOW COLUMNS FROM package_tour_quotation_transport_entries2"); 
        while ($r = mysqli_fetch_assoc($result)) {
            $cols[] = '`'.$r["Field"].'`';
        }

        $result = mysqlQuery("SELECT * FROM package_tour_quotation_transport_entries2 WHERE quotation_id='$quotation_id'");
        while($r = mysqli_fetch_array($result)) {
            $insertSQL = "INSERT INTO package_tour_quotation_transport_entries2 (".implode(", ", $cols).") VALUES (";
            $count = count($cols);

            foreach($cols as $counter => $col) {
                if($col == '`id`') {
                    $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_transport_entries2"));
                    $id = $sq_max['max'] + 1;
                    $insertSQL .= "'".$id."'";
                }
                elseif($col == '`quotation_id`') {
                    $insertSQL .= "'".$quotation_max."'";
                }
                else {
                    $col_name = str_replace('`','',$col);
                    $insertSQL .= "'".$r[$col_name]."'";
                }
                if ($counter < $count - 1) {
                    $insertSQL .= ", ";
                }
            }
            $insertSQL .= ")";
            mysqlQuery($insertSQL);
        }
    }

    public function clone_train_entries($quotation_id, $quotation_max) {
        $cols = array();
        $result = mysqlQuery("SHOW COLUMNS FROM package_tour_quotation_train_entries"); 
        while ($r = mysqli_fetch_assoc($result)) {
            $cols[] = $r["Field"];
        }

        $result = mysqlQuery("SELECT * FROM package_tour_quotation_train_entries WHERE quotation_id='$quotation_id'");
        while($r = mysqli_fetch_array($result)) {
            $insertSQL = "INSERT INTO package_tour_quotation_train_entries (".implode(", ", $cols).") VALUES (";
            $count = count($cols);

            foreach($cols as $counter => $col) {
                if($col == 'id') {
                    $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_train_entries"));
                    $id = $sq_max['max'] + 1;
                    $insertSQL .= "'".$id."'";
                }
                elseif($col == 'quotation_id') {
                    $insertSQL .= "'".$quotation_max."'";
                }
                else {
                    $insertSQL .= "'".$r[$col]."'";
                }
                if ($counter < $count - 1) {
                    $insertSQL .= ", ";
                }
            }
            $insertSQL .= ")";
            mysqlQuery($insertSQL);
        }
    }

    public function clone_plane_entries($quotation_id, $quotation_max) {
        $cols = array();
        $result = mysqlQuery("SHOW COLUMNS FROM package_tour_quotation_plane_entries"); 
        while ($r = mysqli_fetch_assoc($result)) {
            $cols[] = $r["Field"];
        }

        $result = mysqlQuery("SELECT * FROM package_tour_quotation_plane_entries WHERE quotation_id='$quotation_id'");
        while($r = mysqli_fetch_array($result)) {
            $insertSQL = "INSERT INTO package_tour_quotation_plane_entries (".implode(", ", $cols).") VALUES (";
            $count = count($cols);

            foreach($cols as $counter => $col) {
                if($col == 'id') {
                    $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_plane_entries"));
                    $id = $sq_max['max'] + 1;
                    $insertSQL .= "'".$id."'";
                }
                elseif($col == 'quotation_id') {
                    $insertSQL .= "'".$quotation_max."'";
                }
                else {
                    $insertSQL .= "'".$r[$col]."'";
                }
                if ($counter < $count - 1) {
                    $insertSQL .= ", ";
                }
            }
            $insertSQL .= ")";
            mysqlQuery($insertSQL);
        }
    }

    public function clone_cruise_entries($quotation_id, $quotation_max) {
        $cols = array();
        $result = mysqlQuery("SHOW COLUMNS FROM package_tour_quotation_cruise_entries"); 
        while ($r = mysqli_fetch_assoc($result)) {
            $cols[] = $r["Field"];
        }

        $result = mysqlQuery("SELECT * FROM package_tour_quotation_cruise_entries WHERE quotation_id='$quotation_id'");
        while($r = mysqli_fetch_array($result)) {
            $insertSQL = "INSERT INTO package_tour_quotation_cruise_entries (".implode(", ", $cols).") VALUES (";
            $count = count($cols);

            foreach($cols as $counter => $col) {
                if($col == 'id') {
                    $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_cruise_entries"));
                    $id = $sq_max['max'] + 1;
                    $insertSQL .= "'".$id."'";
                }
                elseif($col == 'quotation_id') {
                    $insertSQL .= "'".$quotation_max."'";
                }
                else {
                    $insertSQL .= "'".$r[$col]."'";
                }
                if ($counter < $count - 1) {
                    $insertSQL .= ", ";
                }
            }
            $insertSQL .= ")";
            mysqlQuery($insertSQL);
        }
    }

    public function clone_hotel_entries($quotation_id, $quotation_max) {
        $cols = array();
        $result = mysqlQuery("SHOW COLUMNS FROM package_tour_quotation_hotel_entries"); 
        while ($r = mysqli_fetch_assoc($result)) {
            $cols[] = $r["Field"];
        }

        $result = mysqlQuery("SELECT * FROM package_tour_quotation_hotel_entries WHERE quotation_id='$quotation_id'");
        while($r = mysqli_fetch_array($result)) {
            $insertSQL = "INSERT INTO package_tour_quotation_hotel_entries (".implode(", ", $cols).") VALUES (";
            $count = count($cols);

            foreach($cols as $counter => $col) {
                if($col == 'id') {
                    $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_hotel_entries"));
                    $id = $sq_max['max'] + 1;
                    $insertSQL .= "'".$id."'";
                }
                elseif($col == 'quotation_id') {
                    $insertSQL .= "'".$quotation_max."'";
                }
                else {
                    $insertSQL .= "'".$r[$col]."'";
                }
                if ($counter < $count - 1) {
                    $insertSQL .= ", ";
                }
            }
            $insertSQL .= ")";
            mysqlQuery($insertSQL);
        }
    }

    public function clone_excursion_entries($quotation_id, $quotation_max) {
        $cols = array();
        $result = mysqlQuery("SHOW COLUMNS FROM package_tour_quotation_excursion_entries"); 
        while ($r = mysqli_fetch_assoc($result)) {
            $cols[] = $r["Field"];
        }

        $result = mysqlQuery("SELECT * FROM package_tour_quotation_excursion_entries WHERE quotation_id='$quotation_id'");
        while($r = mysqli_fetch_array($result)) {
            $insertSQL = "INSERT INTO package_tour_quotation_excursion_entries (".implode(", ", $cols).") VALUES (";
            $count = count($cols);

            foreach($cols as $counter => $col) {
                if($col == 'id') {
                    $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_excursion_entries"));
                    $id = $sq_max['max'] + 1;
                    $insertSQL .= "'".$id."'";
                }
                elseif($col == 'quotation_id') {
                    $insertSQL .= "'".$quotation_max."'";
                }
                else {
                    $insertSQL .= "'".$r[$col]."'";
                }
                if ($counter < $count - 1) {
                    $insertSQL .= ", ";
                }
            }
            $insertSQL .= ")";
            mysqlQuery($insertSQL);
        }
    }

    public function clone_program_entries($quotation_id, $quotation_max) {	
        $cols = array();
        $result = mysqlQuery("SHOW COLUMNS FROM package_quotation_program"); 
        while ($r = mysqli_fetch_assoc($result)) {
            $cols[] = $r["Field"];
        }

        $result = mysqlQuery("SELECT * FROM package_quotation_program WHERE quotation_id='$quotation_id'");
        while($r = mysqli_fetch_array($result)) {
            $insertSQL = "INSERT INTO package_quotation_program (".implode(", ", $cols).") VALUES (";
            $count = count($cols);

            foreach($cols as $counter => $col) {
                if($col == 'id') {
                    $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_quotation_program"));
                    $id = $sq_max['max'] + 1;
                    $insertSQL .= "'".$id."'";
                }
                elseif($col == 'quotation_id') {
                    $insertSQL .= "'".$quotation_max."'";
                }
                else {
                    $dyn_content = addslashes($r[$col]);
                    $insertSQL .= "'".$dyn_content."'";
                }
                if ($counter < $count - 1) {
                    $insertSQL .= ", ";
                }
            }
            $insertSQL .= ")";
            mysqlQuery($insertSQL);
        }
    }

    public function clone_images_entries($quotation_id, $quotation_max) {	
        $cols = array();
        $result = mysqlQuery("SHOW COLUMNS FROM package_tour_quotation_images"); 
        while ($r = mysqli_fetch_assoc($result)) {
            $cols[] = $r["Field"];
        }

        $result = mysqlQuery("SELECT * FROM package_tour_quotation_images WHERE quotation_id='$quotation_id'");
        while($r = mysqli_fetch_array($result)) {
            $insertSQL = "INSERT INTO package_tour_quotation_images (".implode(", ", $cols).") VALUES (";
            $count = count($cols);

            foreach($cols as $counter => $col) {
                if($col == 'id') {
                    $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_images"));
                    $id = $sq_max['max'] + 1;
                    $insertSQL .= "'".$id."'";
                }
                elseif($col == 'quotation_id') {
                    $insertSQL .= "'".$quotation_max."'";
                }
                else {
                    $insertSQL .= "'".$r[$col]."'";
                }
                if ($counter < $count - 1) {
                    $insertSQL .= ", ";
                }
            }
            $insertSQL .= ")";
            mysqlQuery($insertSQL);
        }
    }

    public function clone_costing_entries($quotation_id, $quotation_max) {
        $cols = array();
        $result = mysqlQuery("SHOW COLUMNS FROM package_tour_quotation_costing_entries"); 
        while ($r = mysqli_fetch_assoc($result)) {
            $cols[] = $r["Field"];
        }

        $result = mysqlQuery("SELECT * FROM package_tour_quotation_costing_entries WHERE quotation_id='$quotation_id'");
        while($r = mysqli_fetch_array($result)) {
            $insertSQL = "INSERT INTO package_tour_quotation_costing_entries (".implode(", ", $cols).") VALUES (";
            $count = count($cols);

            foreach($cols as $counter => $col) {
                if($col == 'id') {
                    $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from package_tour_quotation_costing_entries"));
                    $id = $sq_max['max'] + 1;
                    $insertSQL .= "'".$id."'";
                }
                elseif($col == 'quotation_id') {
                    $insertSQL .= "'".$quotation_max."'";
                }
                else {
                    $insertSQL .= "'".$r[$col]."'";
                }
                if ($counter < $count - 1) {
                    $insertSQL .= ", ";
                }
            }
            $insertSQL .= ")";
            mysqlQuery($insertSQL);
        }
    }
}

?>
