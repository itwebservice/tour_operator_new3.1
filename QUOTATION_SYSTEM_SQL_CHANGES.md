# Quotation System SQL Changes Documentation

## Overview
This document contains all SQL queries and database changes made for the quotation system enhancements, including itinerary image functionality and sub-quotation features.

## Date: September 18, 2025

---

## 1. Database Schema Changes

### 1.1 Add Image Column to Package Quotation Program Table
```sql
-- Add day_image column to package_quotation_program table
ALTER TABLE package_quotation_program 
ADD COLUMN day_image VARCHAR(255) NULL DEFAULT NULL AFTER meal_plan;
```

### 1.2 Add Image Column to Custom Package Program Table
```sql
-- Add day_image column to custom_package_program table
ALTER TABLE custom_package_program 
ADD COLUMN day_image VARCHAR(255) NULL DEFAULT NULL AFTER meal_plan;
```

---

## 2. Data Verification Queries

### 2.1 Check Package Quotation Program Structure
```sql
-- Verify the new day_image column exists
DESCRIBE package_quotation_program;
```

### 2.2 Check Custom Package Program Structure
```sql
-- Verify the new day_image column exists
DESCRIBE custom_package_program;
```

### 2.3 Check Existing Image Data
```sql
-- Check existing day_image data in package_quotation_program
SELECT quotation_id, day_count, attraction, day_image 
FROM package_quotation_program 
WHERE day_image IS NOT NULL 
ORDER BY quotation_id, day_count;
```

---

## 3. Data Cleanup Queries

### 3.1 Remove Duplicate Itinerary Entries (Package Tour)
```sql
-- Find duplicate entries in custom_package_program
SELECT package_id, day_count, COUNT(*) as duplicate_count
FROM custom_package_program 
GROUP BY package_id, day_count 
HAVING COUNT(*) > 1;
```

### 3.2 Remove Duplicate Itinerary Entries (Package Quotation)
```sql
-- Find duplicate entries in package_quotation_program
SELECT quotation_id, day_count, COUNT(*) as duplicate_count
FROM package_quotation_program 
GROUP BY quotation_id, day_count 
HAVING COUNT(*) > 1;
```

### 3.3 Clean Up Specific Duplicate Entries
```sql
-- Remove specific duplicate entries (adjust IDs as needed)
DELETE FROM custom_package_program 
WHERE entry_id IN (140, 141, 142, 143);

DELETE FROM package_quotation_program 
WHERE entry_id IN (specific_duplicate_ids);
```

---

## 4. Sub-Quotation System Queries

### 4.1 Check Sub-Quotation Structure
```sql
-- Verify sub-quotation fields exist
DESCRIBE package_tour_quotation_master;
```

### 4.2 Check Sub-Quotation Data
```sql
-- Find quotations with sub-quotations
SELECT 
    quotation_id,
    quotation_display_id,
    is_sub_quotation,
    parent_quotation_id,
    package_name,
    quotation_date
FROM package_tour_quotation_master 
WHERE is_sub_quotation = '1' 
ORDER BY parent_quotation_id, quotation_display_id;
```

### 4.3 Get Hierarchical Quotation List
```sql
-- Get main quotations and their sub-quotations
SELECT 
    m.quotation_id,
    m.quotation_display_id,
    m.is_sub_quotation,
    m.parent_quotation_id,
    m.package_name,
    m.quotation_date,
    CASE 
        WHEN m.is_sub_quotation = '1' THEN CONCAT('Sub of: ', p.quotation_display_id)
        ELSE 'Main Quotation'
    END as quotation_type
FROM package_tour_quotation_master m
LEFT JOIN package_tour_quotation_master p ON m.parent_quotation_id = p.quotation_id
ORDER BY 
    COALESCE(m.parent_quotation_id, m.quotation_id),
    m.is_sub_quotation,
    m.quotation_display_id;
```

---

## 5. Image Management Queries

### 5.1 Get Quotation with Images
```sql
-- Get quotation details with itinerary images
SELECT 
    q.quotation_id,
    q.package_name,
    q.quotation_date,
    p.day_count,
    p.attraction,
    p.day_image,
    p.stay,
    p.meal_plan
FROM package_tour_quotation_master q
JOIN package_quotation_program p ON q.quotation_id = p.quotation_id
WHERE q.quotation_id = 33
ORDER BY p.day_count;
```

### 5.2 Count Images per Quotation
```sql
-- Count how many days have images for each quotation
SELECT 
    quotation_id,
    COUNT(*) as total_days,
    COUNT(day_image) as days_with_images,
    (COUNT(day_image) / COUNT(*)) * 100 as image_percentage
FROM package_quotation_program 
GROUP BY quotation_id
ORDER BY image_percentage DESC;
```

### 5.3 Find Quotations Missing Images
```sql
-- Find quotations with missing images
SELECT 
    quotation_id,
    COUNT(*) as total_days,
    COUNT(day_image) as days_with_images,
    COUNT(*) - COUNT(day_image) as missing_images
FROM package_quotation_program 
GROUP BY quotation_id
HAVING missing_images > 0
ORDER BY missing_images DESC;
```

---

## 6. Performance Optimization Queries

### 6.1 Add Indexes for Better Performance
```sql
-- Add index on day_image column for faster queries
CREATE INDEX idx_package_quotation_program_day_image 
ON package_quotation_program(day_image);

CREATE INDEX idx_custom_package_program_day_image 
ON custom_package_program(day_image);

-- Add index on sub-quotation fields
CREATE INDEX idx_quotation_sub_quotation 
ON package_tour_quotation_master(is_sub_quotation, parent_quotation_id);
```

### 6.2 Check Index Usage
```sql
-- Check if indexes are being used
SHOW INDEX FROM package_quotation_program;
SHOW INDEX FROM package_tour_quotation_master;
```

---

## 7. Data Migration Queries

### 7.1 Migrate Old Image Data (if needed)
```sql
-- If migrating from old image system to new day_image column
UPDATE package_quotation_program p
JOIN package_tour_quotation_images i ON p.quotation_id = i.quotation_id
SET p.day_image = SUBSTRING_INDEX(SUBSTRING_INDEX(i.image_url, ',', p.day_count), ',', -1)
WHERE p.day_image IS NULL 
AND i.image_url IS NOT NULL;
```

### 7.2 Backup Before Migration
```sql
-- Create backup tables before making changes
CREATE TABLE package_quotation_program_backup AS 
SELECT * FROM package_quotation_program;

CREATE TABLE custom_package_program_backup AS 
SELECT * FROM custom_package_program;
```

---

## 8. Testing Queries

### 8.1 Test Image URLs
```sql
-- Test if image URLs are accessible
SELECT 
    quotation_id,
    day_count,
    day_image,
    CASE 
        WHEN day_image LIKE 'http%' THEN 'Full URL'
        WHEN day_image LIKE 'uploads/%' THEN 'Relative Path'
        ELSE 'Unknown Format'
    END as url_type
FROM package_quotation_program 
WHERE day_image IS NOT NULL
LIMIT 10;
```

### 8.2 Test Sub-Quotation Relationships
```sql
-- Test sub-quotation relationships
SELECT 
    parent.quotation_id as parent_id,
    parent.quotation_display_id as parent_display,
    child.quotation_id as child_id,
    child.quotation_display_id as child_display,
    child.is_sub_quotation
FROM package_tour_quotation_master parent
JOIN package_tour_quotation_master child ON parent.quotation_id = child.parent_quotation_id
WHERE child.is_sub_quotation = '1'
ORDER BY parent.quotation_id, child.quotation_display_id;
```

---

## 9. Rollback Queries

### 9.1 Remove Image Columns (if rollback needed)
```sql
-- Remove day_image columns (use with caution)
ALTER TABLE package_quotation_program DROP COLUMN day_image;
ALTER TABLE custom_package_program DROP COLUMN day_image;
```

### 9.2 Remove Indexes
```sql
-- Remove added indexes
DROP INDEX idx_package_quotation_program_day_image ON package_quotation_program;
DROP INDEX idx_custom_package_program_day_image ON custom_package_program;
DROP INDEX idx_quotation_sub_quotation ON package_tour_quotation_master;
```

---

## 10. Monitoring Queries

### 10.1 Monitor Image Usage
```sql
-- Monitor image usage over time
SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_quotations,
    COUNT(CASE WHEN day_image IS NOT NULL THEN 1 END) as with_images
FROM package_quotation_program 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(created_at)
ORDER BY date DESC;
```

### 10.2 Monitor Sub-Quotation Usage
```sql
-- Monitor sub-quotation creation
SELECT 
    DATE(quotation_date) as date,
    COUNT(*) as total_quotations,
    COUNT(CASE WHEN is_sub_quotation = '1' THEN 1 END) as sub_quotations
FROM package_tour_quotation_master 
WHERE quotation_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(quotation_date)
ORDER BY date DESC;
```

---

## 11. File System Queries

### 11.1 Check Image File Existence
```sql
-- Get image paths for file system verification
SELECT 
    quotation_id,
    day_count,
    day_image,
    CONCAT('/var/www/html/itoursdemo/', day_image) as full_path
FROM package_quotation_program 
WHERE day_image IS NOT NULL
LIMIT 10;
```

---

## Notes

1. **Always backup your database** before running any ALTER TABLE statements
2. **Test queries on a development environment** first
3. **Monitor performance** after adding indexes
4. **Verify image file paths** are correct for your server setup
5. **Check file permissions** for the uploads directory

## Support

For any issues with these queries or the quotation system changes, refer to the implementation files:
- Image upload: `crm/view/package_booking/quotation/home/save/tab2.php`
- Image display: `crm/view/package_booking/quotation/home/update/tab2.php`
- PDF generation: `crm/model/app_settings/print_html/quotation_html/`
- Sub-quotation: `crm/model/package_tour/quotation/quotation_sub_create.php`
