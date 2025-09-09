-- Add quotation_display_id column to package_tour_quotation_master table
-- This column will store the formatted display ID (e.g., QTN/2025/15.1)

ALTER TABLE package_tour_quotation_master 
ADD COLUMN quotation_display_id VARCHAR(50) DEFAULT NULL AFTER quotation_id;

-- Add index for better performance
ALTER TABLE package_tour_quotation_master 
ADD INDEX idx_quotation_display_id (quotation_display_id);

-- Update existing quotations to have their display IDs
-- This will populate the quotation_display_id for existing records
UPDATE package_tour_quotation_master 
SET quotation_display_id = CONCAT('QTN/', YEAR(quotation_date), '/', quotation_id)
WHERE quotation_display_id IS NULL;

-- Optional: Add a unique constraint to prevent duplicate display IDs
-- ALTER TABLE package_tour_quotation_master 
-- ADD UNIQUE KEY unique_quotation_display_id (quotation_display_id);
