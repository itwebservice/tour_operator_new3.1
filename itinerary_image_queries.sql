-- =====================================================
-- ITINERARY IMAGE UPLOAD FUNCTIONALITY - SQL QUERIES
-- =====================================================
-- Purpose: Add image upload functionality to itinerary forms
-- Date: Generated for iTours Demo
-- 
-- IMPORTANT: Run these queries manually in your database
-- =====================================================

-- =====================================================
-- 1. ADD IMAGE COLUMN TO ITINERARY_MASTER TABLE
-- =====================================================
-- Purpose: Add a column to store image file path for each itinerary entry
-- This allows each day's itinerary to have an associated image

ALTER TABLE `itinerary_master` 
ADD COLUMN `itinerary_image` VARCHAR(500) NULL DEFAULT NULL 
AFTER `overnight_stay`;

-- =====================================================
-- 2. ADD INDEX FOR BETTER PERFORMANCE
-- =====================================================
-- Purpose: Add index on the new image column for faster queries
-- This is optional but recommended for better performance

ALTER TABLE `itinerary_master` 
ADD INDEX `idx_itinerary_image` (`itinerary_image`);

-- =====================================================
-- 3. VERIFY TABLE STRUCTURE
-- =====================================================
-- Purpose: Check that the column was added successfully
-- Run this query to verify the table structure

DESCRIBE `itinerary_master`;

-- =====================================================
-- 4. SAMPLE DATA VERIFICATION
-- =====================================================
-- Purpose: Check existing data and verify the new column is working
-- This will show all itinerary entries with their new image column

SELECT 
    entry_id,
    dest_id,
    special_attraction,
    daywise_program,
    overnight_stay,
    itinerary_image
FROM `itinerary_master` 
LIMIT 10;

-- =====================================================
-- 5. UPDATE EXISTING RECORDS (OPTIONAL)
-- =====================================================
-- Purpose: Set default image path for existing records if needed
-- Uncomment and modify the path as per your requirements

-- UPDATE `itinerary_master` 
-- SET `itinerary_image` = 'default_itinerary_image.jpg' 
-- WHERE `itinerary_image` IS NULL;

-- =====================================================
-- 6. ROLLBACK QUERY (IF NEEDED)
-- =====================================================
-- Purpose: Remove the image column if you need to rollback
-- Uncomment and run if you need to remove the changes

-- ALTER TABLE `itinerary_master` DROP COLUMN `itinerary_image`;

-- =====================================================
-- NOTES FOR IMPLEMENTATION:
-- =====================================================
-- 1. The image column stores the file path/name of uploaded images
-- 2. Images should be uploaded to a directory like: uploads/itinerary_images/
-- 3. The column accepts NULL values, so existing records won't break
-- 4. Maximum path length is 500 characters (adjust if needed)
-- 5. Consider adding image validation in your PHP code
-- 6. Don't forget to update your PHP forms to handle image uploads
-- 7. Update the save and update functions in itinerary_master.php
-- 8. Add image display functionality in the list and edit forms

-- =====================================================
-- EXPECTED TABLE STRUCTURE AFTER RUNNING QUERIES:
-- =====================================================
-- entry_id (int) - Primary key
-- dest_id (int) - Foreign key to destination_master
-- special_attraction (text) - Special attraction text
-- daywise_program (text) - Day-wise program text  
-- overnight_stay (text) - Overnight stay text
-- itinerary_image (varchar) - NEW: Image file path
-- =====================================================

-- =====================================================
-- 7. ADD IMAGE COLUMN TO CUSTOM_PACKAGE_PROGRAM TABLE
-- =====================================================
-- Purpose: Add image functionality to custom package programs
-- This allows each day's program in custom packages to have an image

ALTER TABLE `custom_package_program` 
ADD COLUMN `day_image` VARCHAR(255) NULL DEFAULT NULL 
AFTER `meal_plan`;

-- =====================================================
-- 8. ADD IMAGE COLUMN TO PACKAGE_QUOTATION_PROGRAM TABLE
-- =====================================================
-- Purpose: Add image functionality to package quotation programs
-- This stores image paths when quotations are saved from custom packages

ALTER TABLE `package_quotation_program` 
ADD COLUMN `day_image` VARCHAR(255) NULL DEFAULT NULL 
AFTER `meal_plan`;

-- =====================================================
-- 9. VERIFY ALL TABLE STRUCTURES
-- =====================================================
-- Purpose: Check that all image columns were added successfully

-- Check itinerary_master table
DESCRIBE `itinerary_master`;

-- Check custom_package_program table
DESCRIBE `custom_package_program`;

-- Check package_quotation_program table
DESCRIBE `package_quotation_program`;

-- =====================================================
-- 10. CHECK EXISTING DATA IN ALL TABLES
-- =====================================================
-- Purpose: Verify that existing data is intact and new columns are working

-- Check itinerary_master data
SELECT entry_id, dest_id, special_attraction, itinerary_image 
FROM `itinerary_master` 
ORDER BY entry_id DESC 
LIMIT 5;

-- Check custom_package_program data
SELECT entry_id, package_id, attraction, day_image 
FROM `custom_package_program` 
ORDER BY entry_id DESC 
LIMIT 5;

-- Check package_quotation_program data
SELECT id, quotation_id, package_id, attraction, day_image 
FROM `package_quotation_program` 
ORDER BY id DESC 
LIMIT 5;

-- =====================================================
-- 11. SAMPLE DATA UPDATE (OPTIONAL)
-- =====================================================
-- Purpose: Add sample image paths for testing
-- Uncomment and modify as needed for testing

-- Add sample images to custom packages
-- UPDATE `custom_package_program` 
-- SET `day_image` = 'uploads/itinerary_images/sample_day1.jpg' 
-- WHERE entry_id = 1;

-- UPDATE `custom_package_program` 
-- SET `day_image` = 'uploads/itinerary_images/sample_day2.jpg' 
-- WHERE entry_id = 2;

-- =====================================================
-- 12. CLEAN UP QUERIES (IF NEEDED)
-- =====================================================
-- Purpose: Remove empty or invalid image entries
-- Run these if you need to clean up data

-- Remove empty image paths
-- UPDATE `itinerary_master` SET `itinerary_image` = NULL WHERE `itinerary_image` = '';
-- UPDATE `custom_package_program` SET `day_image` = NULL WHERE `day_image` = '';
-- UPDATE `package_quotation_program` SET `day_image` = NULL WHERE `day_image` = '';

-- =====================================================
-- 13. PERFORMANCE OPTIMIZATION (OPTIONAL)
-- =====================================================
-- Purpose: Add indexes for better query performance

-- Add index on custom_package_program day_image
ALTER TABLE `custom_package_program` 
ADD INDEX `idx_day_image` (`day_image`);

-- Add index on package_quotation_program day_image
ALTER TABLE `package_quotation_program` 
ADD INDEX `idx_day_image` (`day_image`);

-- =====================================================
-- 14. BACKUP QUERIES (BEFORE RUNNING)
-- =====================================================
-- Purpose: Create backup of tables before making changes
-- Uncomment and run these before making any changes

-- CREATE TABLE `itinerary_master_backup` AS SELECT * FROM `itinerary_master`;
-- CREATE TABLE `custom_package_program_backup` AS SELECT * FROM `custom_package_program`;
-- CREATE TABLE `package_quotation_program_backup` AS SELECT * FROM `package_quotation_program`;

-- =====================================================
-- 15. ROLLBACK QUERIES (IF NEEDED)
-- =====================================================
-- Purpose: Remove all image columns if you need to rollback
-- DANGER: This will remove all image data permanently

-- ALTER TABLE `itinerary_master` DROP COLUMN `itinerary_image`;
-- ALTER TABLE `custom_package_program` DROP COLUMN `day_image`;
-- ALTER TABLE `package_quotation_program` DROP COLUMN `day_image`;

-- =====================================================
-- 16. DATA MIGRATION QUERIES (IF NEEDED)
-- =====================================================
-- Purpose: Migrate image data between tables if needed

-- Copy images from custom_package_program to package_quotation_program
-- UPDATE package_quotation_program pqp
-- INNER JOIN custom_package_program cpp ON pqp.package_id = cpp.package_id 
-- SET pqp.day_image = cpp.day_image
-- WHERE pqp.day_image IS NULL AND cpp.day_image IS NOT NULL;

-- =====================================================
-- 17. VERIFICATION QUERIES
-- =====================================================
-- Purpose: Verify that image functionality is working correctly

-- Count records with images in each table
SELECT 'itinerary_master' as table_name, 
       COUNT(*) as total_records,
       COUNT(itinerary_image) as records_with_images
FROM itinerary_master
UNION ALL
SELECT 'custom_package_program' as table_name,
       COUNT(*) as total_records, 
       COUNT(day_image) as records_with_images
FROM custom_package_program
UNION ALL
SELECT 'package_quotation_program' as table_name,
       COUNT(*) as total_records,
       COUNT(day_image) as records_with_images  
FROM package_quotation_program;

-- Check for specific quotation images
-- SELECT * FROM package_tour_quotation_images 
-- WHERE quotation_id = YOUR_QUOTATION_ID;

-- =====================================================
-- IMPLEMENTATION NOTES:
-- =====================================================
-- 1. These queries add image functionality to three main tables:
--    - itinerary_master: For standalone itinerary management
--    - custom_package_program: For package creation with images
--    - package_quotation_program: For quotation itinerary with images
--
-- 2. The package_tour_quotation_images table already exists and handles
--    uploaded image files separately from the day_image columns
--
-- 3. Image upload directory structure:
--    - uploads/itinerary_images/ (for itinerary master images)
--    - uploads/quotation_images/ (for quotation-specific images)
--
-- 4. Frontend files that were updated:
--    - crm/js/app/validation.js (row addition handling)
--    - crm/view/other_masters/itinerary/update_modal.php (itinerary update)
--    - crm/view/custom_packages/master/update_modal.php (package update)
--    - crm/view/package_booking/quotation/inc/get_packages.php (quotation display)
--    - crm/view/package_booking/quotation/home/update/tab2.php (quotation update)
--    - Multiple save and update files for complete flow
--
-- 5. Backend files that were updated:
--    - crm/model/other_masters/itinerary_master.php (itinerary model)
--    - crm/model/custom_packages/package_master.php (package model)
--    - crm/model/package_tour/quotation/quotation_save.php (quotation save)
--    - crm/model/package_tour/quotation/quotation_update.php (quotation update)
--    - Multiple controller files for image upload handling
--
-- 6. Key Features Implemented:
--    - Image preview functionality
--    - File type and size validation
--    - Proper error handling
--    - Unique row index generation
--    - Cross-table data synchronization
--    - Update and delete image capabilities
--
-- =====================================================
-- 18. SPECIFIC TESTING QUERIES
-- =====================================================
-- Purpose: Specific queries for testing the image functionality

-- Test if columns exist (returns 1 if exists, 0 if not)
SELECT COUNT(*) as itinerary_image_column_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'tour_operator' 
  AND TABLE_NAME = 'itinerary_master' 
  AND COLUMN_NAME = 'itinerary_image';

SELECT COUNT(*) as custom_package_day_image_column_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'tour_operator' 
  AND TABLE_NAME = 'custom_package_program' 
  AND COLUMN_NAME = 'day_image';

SELECT COUNT(*) as quotation_day_image_column_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'tour_operator' 
  AND TABLE_NAME = 'package_quotation_program' 
  AND COLUMN_NAME = 'day_image';

-- =====================================================
-- 19. TROUBLESHOOTING QUERIES
-- =====================================================
-- Purpose: Debug queries for troubleshooting image issues

-- Find records with image data
SELECT 'itinerary_master' as source, entry_id as id, itinerary_image as image_path
FROM itinerary_master 
WHERE itinerary_image IS NOT NULL AND itinerary_image != ''
UNION ALL
SELECT 'custom_package_program' as source, entry_id as id, day_image as image_path
FROM custom_package_program 
WHERE day_image IS NOT NULL AND day_image != ''
UNION ALL
SELECT 'package_quotation_program' as source, id, day_image as image_path
FROM package_quotation_program 
WHERE day_image IS NOT NULL AND day_image != ''
ORDER BY source, id;

-- Check for duplicate or orphaned image entries
SELECT package_id, COUNT(*) as duplicate_count
FROM custom_package_program 
WHERE day_image IS NOT NULL 
GROUP BY package_id, day_image 
HAVING COUNT(*) > 1;

-- =====================================================
-- 20. MAINTENANCE QUERIES
-- =====================================================
-- Purpose: Regular maintenance queries for image data

-- Find and remove broken image paths (files that don't exist)
-- Note: This is a placeholder - you'll need to implement file existence check in PHP

-- Remove duplicate image entries (keep the latest one)
-- DELETE cpp1 FROM custom_package_program cpp1
-- INNER JOIN custom_package_program cpp2 
-- WHERE cpp1.id < cpp2.id 
--   AND cpp1.package_id = cpp2.package_id 
--   AND cpp1.day_image = cpp2.day_image 
--   AND cpp1.day_image IS NOT NULL;

-- =====================================================
-- 21. QUICK SETUP SCRIPT (RUN ALL ESSENTIAL QUERIES)
-- =====================================================
-- Purpose: Run this section to quickly set up image functionality
-- Copy and paste this entire section into your SQL client

/*
-- Essential setup queries (run these in order):

-- 1. Add image column to itinerary_master
ALTER TABLE `itinerary_master` 
ADD COLUMN IF NOT EXISTS `itinerary_image` VARCHAR(500) NULL DEFAULT NULL 
AFTER `overnight_stay`;

-- 2. Add image column to custom_package_program  
ALTER TABLE `custom_package_program` 
ADD COLUMN IF NOT EXISTS `day_image` VARCHAR(255) NULL DEFAULT NULL 
AFTER `meal_plan`;

-- 3. Add image column to package_quotation_program
ALTER TABLE `package_quotation_program` 
ADD COLUMN IF NOT EXISTS `day_image` VARCHAR(255) NULL DEFAULT NULL 
AFTER `meal_plan`;

-- 4. Verify all tables
DESCRIBE `itinerary_master`;
DESCRIBE `custom_package_program`;  
DESCRIBE `package_quotation_program`;

-- 5. Check current data
SELECT COUNT(*) as total_itinerary_records FROM itinerary_master;
SELECT COUNT(*) as total_package_records FROM custom_package_program;
SELECT COUNT(*) as total_quotation_records FROM package_quotation_program;

-- Setup complete!
*/

-- =====================================================
-- 22. SERVER DEPLOYMENT CHECKLIST
-- =====================================================
-- Purpose: Checklist for deploying image functionality on production server

/*
SERVER DEPLOYMENT STEPS:

1. DATABASE SETUP:
   - Run the Quick Setup Script (Section 21) above
   - Verify all columns exist using Section 18 queries
   - Check existing data using Section 10 queries

2. DIRECTORY PERMISSIONS:
   - Ensure uploads directory exists: mkdir -p uploads/itinerary_images/
   - Set proper permissions: chmod 755 uploads/itinerary_images/
   - Ensure web server can write: chown www-data:www-data uploads/itinerary_images/

3. PHP CONFIGURATION:
   - Check upload_max_filesize in php.ini (recommend 10M)
   - Check post_max_size in php.ini (recommend 20M)  
   - Check max_execution_time (recommend 300 seconds)
   - Restart web server after php.ini changes

4. FILE STRUCTURE VERIFICATION:
   - Verify all frontend files are updated (see Section 4 in Implementation Notes)
   - Verify all backend files are updated (see Section 5 in Implementation Notes)
   - Check that upload handlers exist in view/other_masters/itinerary/

5. TESTING CHECKLIST:
   - Test itinerary master image upload/update
   - Test custom package image upload/update  
   - Test quotation creation with images
   - Test quotation update with image changes
   - Verify images display correctly in all sections
*/

-- =====================================================
-- 23. PRODUCTION MONITORING QUERIES
-- =====================================================
-- Purpose: Monitor image functionality in production

-- Monitor image upload activity
SELECT 
    DATE(created_at) as upload_date,
    COUNT(*) as images_uploaded
FROM package_tour_quotation_images 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(created_at)
ORDER BY upload_date DESC;

-- Check for failed uploads (empty paths)
SELECT 'itinerary_master' as table_name, COUNT(*) as empty_paths
FROM itinerary_master WHERE itinerary_image = ''
UNION ALL
SELECT 'custom_package_program', COUNT(*) 
FROM custom_package_program WHERE day_image = ''
UNION ALL  
SELECT 'package_quotation_program', COUNT(*)
FROM package_quotation_program WHERE day_image = '';

-- Find large image files (if file size tracking is implemented)
-- SELECT * FROM package_tour_quotation_images 
-- WHERE image_url LIKE '%.jpg' OR image_url LIKE '%.png'
-- ORDER BY id DESC LIMIT 10;

-- =====================================================
