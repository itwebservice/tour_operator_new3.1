<?php

class sub_quotation_setup
{
    public function __construct()
    {
        // Constructor - can be used for initialization if needed
    }

    /**
     * Check if sub-quotation fields exist in the database
     * @return bool
     */
    public function check_sub_quotation_fields_exist()
    {
        try {
            $result = mysqlQuery("SHOW COLUMNS FROM package_tour_quotation_master LIKE 'is_sub_quotation'");
            return mysqli_num_rows($result) > 0;
        } catch (Exception $e) {
            error_log("Error checking sub-quotation fields: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if itinerary image fields exist in the database
     * @return bool
     */
    public function check_itinerary_image_fields_exist()
    {
        try {
            $tables_to_check = [
                'itinerary_master' => 'itinerary_image',
                'custom_package_program' => 'day_image',
                'package_quotation_program' => 'day_image'
            ];
            
            foreach ($tables_to_check as $table => $column) {
                $result = mysqlQuery("SHOW COLUMNS FROM $table LIKE '$column'");
                if (mysqli_num_rows($result) == 0) {
                    return false;
                }
            }
            return true;
        } catch (Exception $e) {
            error_log("Error checking itinerary image fields: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Setup sub-quotation database structure
     * This function runs all necessary ALTER TABLE commands
     * @return bool
     */
    public function setup_sub_quotation_database()
    {
        try {
            $setup_completed = true;

            // Check if sub-quotation fields already exist
            if (!$this->check_sub_quotation_fields_exist()) {
                // Step 1: Add sub-quotation fields to main table
                $this->add_sub_quotation_fields();
                
                // Step 2: Add performance indexes
                $this->add_performance_indexes();
                
                error_log("Sub-quotation fields setup completed");
            } else {
                error_log("Sub-quotation fields already exist, skipping setup");
            }

            // Check if itinerary image fields already exist
            if (!$this->check_itinerary_image_fields_exist()) {
                // Step 3: Add itinerary image fields
                $this->add_itinerary_image_fields();
                
                // Step 4: Add itinerary image indexes
                $this->add_itinerary_image_indexes();
                
                error_log("Itinerary image fields setup completed");
            } else {
                error_log("Itinerary image fields already exist, skipping setup");
            }
            
            // Step 5: Verify complete setup
            $this->verify_complete_setup();
            
            error_log("Complete database setup completed successfully");
            return true;
            
        } catch (Exception $e) {
            error_log("Error in database setup: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add sub-quotation fields to package_tour_quotation_master table
     */
    private function add_sub_quotation_fields()
    {
        $queries = [
            // Add sub-quotation tracking fields
            "ALTER TABLE package_tour_quotation_master 
             ADD COLUMN is_sub_quotation ENUM('0','1') DEFAULT '0' AFTER quotation_id",
            
            "ALTER TABLE package_tour_quotation_master 
             ADD COLUMN parent_quotation_id INT(11) DEFAULT NULL AFTER is_sub_quotation",
            
            "ALTER TABLE package_tour_quotation_master 
             ADD COLUMN quotation_id_display VARCHAR(50) DEFAULT NULL AFTER parent_quotation_id"
        ];

        foreach ($queries as $query) {
            $result = mysqlQuery($query);
            if (!$result) {
                throw new Exception("Failed to execute query: " . $query . " - " . mysqli_error($GLOBALS['connection']));
            }
        }
    }

    /**
     * Add performance indexes for sub-quotation queries
     */
    private function add_performance_indexes()
    {
        $queries = [
            // Add indexes for better performance
            "ALTER TABLE package_tour_quotation_master 
             ADD INDEX idx_is_sub_quotation (is_sub_quotation)",
            
            "ALTER TABLE package_tour_quotation_master 
             ADD INDEX idx_parent_quotation_id (parent_quotation_id)",
            
            "ALTER TABLE package_tour_quotation_master 
             ADD INDEX idx_quotation_sub_quotation (is_sub_quotation, parent_quotation_id)"
        ];

        foreach ($queries as $query) {
            $result = mysqlQuery($query);
            if (!$result) {
                // Index might already exist, log warning but continue
                error_log("Warning: Index creation failed (may already exist): " . $query);
            }
        }
    }

    /**
     * Add itinerary image fields to relevant tables
     */
    private function add_itinerary_image_fields()
    {
        $queries = [
            // Add itinerary_image column to itinerary_master
            "ALTER TABLE itinerary_master 
             ADD COLUMN itinerary_image VARCHAR(500) NULL DEFAULT NULL AFTER overnight_stay",
            
            // Add day_image column to custom_package_program
            "ALTER TABLE custom_package_program 
             ADD COLUMN day_image VARCHAR(255) NULL DEFAULT NULL AFTER meal_plan",
            
            // Add day_image column to package_quotation_program
            "ALTER TABLE package_quotation_program 
             ADD COLUMN day_image VARCHAR(255) NULL DEFAULT NULL AFTER meal_plan"
        ];

        foreach ($queries as $query) {
            $result = mysqlQuery($query);
            if (!$result) {
                // Check if column already exists (MySQL error 1060)
                $error = mysqli_error($GLOBALS['connection']);
                if (strpos($error, 'Duplicate column name') === false) {
                    throw new Exception("Failed to execute query: " . $query . " - " . $error);
                } else {
                    error_log("Column already exists, skipping: " . $query);
                }
            }
        }
    }

    /**
     * Add performance indexes for itinerary image queries
     */
    private function add_itinerary_image_indexes()
    {
        $queries = [
            // Add index on itinerary_master itinerary_image
            "ALTER TABLE itinerary_master 
             ADD INDEX idx_itinerary_image (itinerary_image)",
            
            // Add index on custom_package_program day_image
            "ALTER TABLE custom_package_program 
             ADD INDEX idx_day_image (day_image)",
            
            // Add index on package_quotation_program day_image
            "ALTER TABLE package_quotation_program 
             ADD INDEX idx_day_image (day_image)"
        ];

        foreach ($queries as $query) {
            $result = mysqlQuery($query);
            if (!$result) {
                // Index might already exist, log warning but continue
                error_log("Warning: Index creation failed (may already exist): " . $query);
            }
        }
    }

    /**
     * Verify that the sub-quotation setup was successful
     */
    private function verify_sub_quotation_setup()
    {
        $required_fields = ['is_sub_quotation', 'parent_quotation_id', 'quotation_id_display'];
        
        foreach ($required_fields as $field) {
            $result = mysqlQuery("SHOW COLUMNS FROM package_tour_quotation_master LIKE '$field'");
            if (mysqli_num_rows($result) == 0) {
                throw new Exception("Required sub-quotation field '$field' was not created successfully");
            }
        }
    }

    /**
     * Verify that the itinerary image setup was successful
     */
    private function verify_itinerary_image_setup()
    {
        $tables_to_check = [
            'itinerary_master' => 'itinerary_image',
            'custom_package_program' => 'day_image',
            'package_quotation_program' => 'day_image'
        ];
        
        foreach ($tables_to_check as $table => $column) {
            $result = mysqlQuery("SHOW COLUMNS FROM $table LIKE '$column'");
            if (mysqli_num_rows($result) == 0) {
                throw new Exception("Required itinerary image field '$column' in table '$table' was not created successfully");
            }
        }
    }

    /**
     * Verify that the complete setup was successful
     */
    private function verify_complete_setup()
    {
        $this->verify_sub_quotation_setup();
        $this->verify_itinerary_image_setup();
    }

    /**
     * Check if sub-quotation system is ready to use
     * @return array
     */
    public function get_system_status()
    {
        $status = [
            'sub_quotation_fields_exist' => false,
            'sub_quotation_indexes_exist' => false,
            'itinerary_image_fields_exist' => false,
            'itinerary_image_indexes_exist' => false,
            'ready' => false,
            'errors' => []
        ];

        try {
            // Check if sub-quotation fields exist
            $status['sub_quotation_fields_exist'] = $this->check_sub_quotation_fields_exist();
            
            // Check if sub-quotation indexes exist
            $result = mysqlQuery("SHOW INDEX FROM package_tour_quotation_master WHERE Key_name = 'idx_is_sub_quotation'");
            $status['sub_quotation_indexes_exist'] = mysqli_num_rows($result) > 0;
            
            // Check if itinerary image fields exist
            $status['itinerary_image_fields_exist'] = $this->check_itinerary_image_fields_exist();
            
            // Check if itinerary image indexes exist
            $result = mysqlQuery("SHOW INDEX FROM itinerary_master WHERE Key_name = 'idx_itinerary_image'");
            $status['itinerary_image_indexes_exist'] = mysqli_num_rows($result) > 0;
            
            // Overall status - both systems must be ready
            $status['ready'] = $status['sub_quotation_fields_exist'] && 
                             $status['sub_quotation_indexes_exist'] && 
                             $status['itinerary_image_fields_exist'] && 
                             $status['itinerary_image_indexes_exist'];
            
        } catch (Exception $e) {
            $status['errors'][] = $e->getMessage();
        }

        return $status;
    }

    /**
     * Run database setup if needed
     * This is the main function to call during login
     * @return bool
     */
    public function ensure_sub_quotation_ready()
    {
        try {
            // Check current status
            $status = $this->get_system_status();
            
            if ($status['ready']) {
                // System is already ready
                return true;
            }
            
            // Setup is needed
            error_log("Sub-quotation system not ready, running setup...");
            return $this->setup_sub_quotation_database();
            
        } catch (Exception $e) {
            error_log("Error ensuring sub-quotation system is ready: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get setup statistics
     * @return array
     */
    public function get_setup_stats()
    {
        try {
            $stats = [
                'total_quotations' => 0,
                'main_quotations' => 0,
                'sub_quotations' => 0,
                'setup_date' => null
            ];

            // Get quotation counts
            $result = mysqlQuery("SELECT COUNT(*) as total FROM package_tour_quotation_master");
            if ($row = mysqli_fetch_assoc($result)) {
                $stats['total_quotations'] = $row['total'];
            }

            // Get main quotations count
            $result = mysqlQuery("SELECT COUNT(*) as main FROM package_tour_quotation_master WHERE is_sub_quotation = '0' OR is_sub_quotation IS NULL");
            if ($row = mysqli_fetch_assoc($result)) {
                $stats['main_quotations'] = $row['main'];
            }

            // Get sub-quotations count
            $result = mysqlQuery("SELECT COUNT(*) as sub FROM package_tour_quotation_master WHERE is_sub_quotation = '1'");
            if ($row = mysqli_fetch_assoc($result)) {
                $stats['sub_quotations'] = $row['sub'];
            }

            // Get setup date (approximate - when first sub-quotation was created)
            $result = mysqlQuery("SELECT MIN(created_at) as setup_date FROM package_tour_quotation_master WHERE is_sub_quotation = '1'");
            if ($row = mysqli_fetch_assoc($result) && $row['setup_date']) {
                $stats['setup_date'] = $row['setup_date'];
            }

            return $stats;
            
        } catch (Exception $e) {
            error_log("Error getting setup stats: " . $e->getMessage());
            return [];
        }
    }
}

?>
