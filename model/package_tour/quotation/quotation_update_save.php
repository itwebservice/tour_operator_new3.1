<?php 

class quotation_update_save
{
    public function quotation_update_save()
    {
        $branch_admin_id = $_SESSION['branch_admin_id'];
        $original_quotation_id = $_POST['original_quotation_id'];
        
        // Get quotation data from POST parameters directly
        $quotation_data = array();
        foreach ($_POST as $key => $value) {
            if ($key != 'original_quotation_id' && $key != 'quotation_data') {
                $quotation_data[$key] = $value;
            }
        }
        
        // Also get data from quotation_data if it exists
        if (isset($_POST['quotation_data']) && is_array($_POST['quotation_data'])) {
            $quotation_data = array_merge($quotation_data, $_POST['quotation_data']);
        }
        
        // Debug: Log the received data
        error_log("Quotation update data received: " . json_encode($quotation_data));
        error_log("Original quotation ID: " . $original_quotation_id);
        error_log("Package ID from data: " . (isset($quotation_data['package_id']) ? $quotation_data['package_id'] : 'NOT SET'));
        
        // Get original quotation details
        $result = mysqlQuery("SELECT * FROM package_tour_quotation_master WHERE quotation_id='$original_quotation_id'");
        $original_quotation = mysqli_fetch_assoc($result);
        
        if (!$original_quotation) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Original quotation not found.'
            ]);
            exit;
        }
        
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
                $original_quotation_id_display = get_quotation_id($original_quotation_id, $year);
                $new_quotation_id_display = $original_quotation_id_display . '.1';
            }
        } else {
            // If it's the original quotation, count existing sub-quotations and create next version
            $original_quotation_id_display = get_quotation_id($original_quotation_id, $year);
            
            // Count existing sub-quotations for this parent
            $sub_count = mysqli_num_rows(mysqlQuery("SELECT quotation_id FROM package_tour_quotation_master WHERE parent_quotation_id='$original_quotation_id'"));
            $new_version = $sub_count + 1;
            $new_quotation_id_display = $original_quotation_id_display . '.' . $new_version;
        }
        
        // Get all columns from the table
        $result = mysqlQuery("SHOW COLUMNS FROM package_tour_quotation_master"); 
        $cols = array();
        while ($r = mysqli_fetch_assoc($result)) {
            $cols[] = $r["Field"];
        }

        // Create new quotation with version number
        $insertSQL = "INSERT INTO package_tour_quotation_master (".implode(", ", $cols).") VALUES (";
        $count = count($cols);
        
        foreach($cols as $counter => $col) {
            if($col == 'quotation_id') {
                // Get new quotation ID
                $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(quotation_id) as max from package_tour_quotation_master"));
                $quotation_max = $sq_max['max'] + 1;
                $insertSQL .= "'".$quotation_max."'";
            }
            else if($col == 'quotation_id_display') {
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
                // Set parent quotation ID for tracking (only if field exists)
                if ($is_sub_quotation && $parent_quotation_id && $parent_quotation_id != '0') {
                    // If editing a sub-quotation, use the same parent
                    $insertSQL .= "'".$parent_quotation_id."'";
                } else {
                    // If editing a parent quotation, use the quotation_id as parent
                    $insertSQL .= "'".$original_quotation_id."'";
                }
            }
            else if($col == 'is_sub_quotation') {
                // Mark as sub-quotation (only if field exists)
                $insertSQL .= "'1'";
            }
            else {
                // Use updated data if provided, otherwise use original data
                if (isset($quotation_data[$col]) && $quotation_data[$col] !== '' && $quotation_data[$col] !== '0') {
                    $insertSQL .= "'".addslashes($quotation_data[$col])."'";
                } else {
                    // Check for alternative field names that might contain the updated data
                    $alternative_fields = array(
                        'customer_name' => array('customer_name12', 'customer_name'),
                        'email_id' => array('email_id12', 'email_id'),
                        'mobile_no' => array('mobile_no12', 'mobile_no'),
                        'total_adult' => array('total_adult12', 'total_adult'),
                        'children_without_bed' => array('children_without_bed12', 'children_without_bed'),
                        'children_with_bed' => array('children_with_bed12', 'children_with_bed'),
                        'total_infant' => array('total_infant12', 'total_infant'),
                        'quotation_date' => array('quotation_date', 'quotation_date'),
                        'from_date' => array('from_date12', 'from_date'),
                        'to_date' => array('to_date12', 'to_date'),
                        'tour_name' => array('tour_name12', 'tour_name'),
                        'total_days' => array('total_days12', 'total_days'),
                        'total_passangers' => array('total_passangers12', 'total_passangers'),
                        'flight_cost' => array('flight_cost1', 'flight_cost'),
                        'train_cost' => array('train_cost1', 'train_cost'),
                        'cruise_cost' => array('cruise_cost1', 'cruise_cost'),
                        'visa_cost' => array('visa_cost1', 'visa_cost'),
                        'guide_cost' => array('guide_cost1', 'guide_cost'),
                        'misc_cost' => array('misc_cost1', 'misc_cost'),
                        'other_desc' => array('other_desc1', 'other_desc'),
                        'costing_type' => array('costing_type1', 'costing_type'),
                        'currency_code' => array('currency_code1', 'currency_code'),
                        'package_id' => array('package_id1', 'package_id')
                    );
                    
                    $found_value = false;
                    if (isset($alternative_fields[$col])) {
                        foreach ($alternative_fields[$col] as $alt_field) {
                            if (isset($quotation_data[$alt_field]) && $quotation_data[$alt_field] !== '' && $quotation_data[$alt_field] !== '0') {
                                $insertSQL .= "'".addslashes($quotation_data[$alt_field])."'";
                                $found_value = true;
                                break;
                            }
                        }
                    }
                    
                    if (!$found_value) {
                        $insertSQL .= "'".$original_quotation[$col]."'";
                    }
                }
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
                'message' => 'Updated quotation has not been created.'
            ]);
            exit;
        }
        else {
            // Clone all related entries
            $this->clone_train_entries($original_quotation_id, $quotation_max);
            $this->clone_plane_entries($original_quotation_id, $quotation_max);
            $this->clone_cruise_entries($original_quotation_id, $quotation_max);
            $this->clone_hotel_entries($original_quotation_id, $quotation_max);
            $this->clone_transport_entries($original_quotation_id, $quotation_max);
            $this->clone_excursion_entries($original_quotation_id, $quotation_max);
            $this->clone_costing_entries($original_quotation_id, $quotation_max);
            $this->clone_program_entries($original_quotation_id, $quotation_max);
            $this->clone_images_entries($original_quotation_id, $quotation_max);
            
            // Try to mark as sub-quotation and update quotation_id_display (only if fields exist)
            try {
                $sq_update = mysqlQuery("UPDATE package_tour_quotation_master SET is_sub_quotation='1', parent_quotation_id='$original_quotation_id', quotation_id_display='$new_quotation_id_display' WHERE quotation_id='$quotation_max'");
                
                // Debug: Log the update result
                if ($sq_update) {
                    error_log("Updated quotation created successfully: ID=$quotation_max, Display ID=$new_quotation_id_display, Parent=$original_quotation_id");
                } else {
                    error_log("Failed to update quotation fields for ID=$quotation_max");
                }
            } catch (Exception $e) {
                // If fields don't exist, just continue without error
                error_log("Error updating quotation fields: " . $e->getMessage());
            }
            
            // Return JSON response with the new quotation ID
            echo json_encode([
                'status' => 'success',
                'message' => 'Quotation has been successfully updated with ID: ' . $new_quotation_id_display,
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
