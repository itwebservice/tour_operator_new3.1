# Sub-Quotation System - Complete SQL Query List

## Overview
This document provides a comprehensive step-by-step list of all SQL queries required to implement the sub-quotation functionality in the iTours system. The sub-quotation system allows creating versioned copies of existing quotations with hierarchical relationships.

## Date: September 18, 2025

---

## Step 1: Database Schema Setup

### 1.1 Add Sub-Quotation Fields to Main Table
```sql
-- Add sub-quotation tracking fields to package_tour_quotation_master
ALTER TABLE package_tour_quotation_master 
ADD COLUMN is_sub_quotation ENUM('0','1') DEFAULT '0' AFTER quotation_id,
ADD COLUMN parent_quotation_id INT(11) DEFAULT NULL AFTER is_sub_quotation,
ADD COLUMN quotation_id_display VARCHAR(50) DEFAULT NULL AFTER parent_quotation_id;
```

### 1.2 Add Performance Indexes
```sql
-- Add indexes for better performance on sub-quotation queries
ALTER TABLE package_tour_quotation_master 
ADD INDEX idx_is_sub_quotation (is_sub_quotation),
ADD INDEX idx_parent_quotation_id (parent_quotation_id),
ADD INDEX idx_quotation_sub_quotation (is_sub_quotation, parent_quotation_id);
```

---

## Step 2: Verification Queries

### 2.1 Verify Schema Changes
```sql
-- Check if sub-quotation fields exist in main table
DESCRIBE package_tour_quotation_master;
```

### 2.2 Check Index Creation
```sql
-- Verify indexes were created successfully
SHOW INDEX FROM package_tour_quotation_master;
```

---

## Step 3: Core Sub-Quotation Operations

### 3.1 Create Sub-Quotation (Main Table)
```sql
-- Insert new sub-quotation into main table
-- Note: This query is dynamically generated in PHP based on table structure
-- The following shows the key fields that need to be set:

INSERT INTO package_tour_quotation_master (
    quotation_id,
    is_sub_quotation,
    parent_quotation_id,
    quotation_id_display,
    quotation_display_id,
    -- ... all other fields from original quotation
) VALUES (
    [new_quotation_id],
    '1',
    [parent_quotation_id],
    [versioned_display_id],
    [versioned_display_id],
    -- ... copied values from original quotation
);
```

### 3.2 Clone Transport Entries
```sql
-- Clone transport entries for sub-quotation
INSERT INTO package_tour_quotation_transport_entries2 (
    id,
    quotation_id,
    -- ... all other fields
) 
SELECT 
    (SELECT MAX(id) + 1 FROM package_tour_quotation_transport_entries2),
    [new_quotation_id],
    -- ... all other fields from original
FROM package_tour_quotation_transport_entries2 
WHERE quotation_id = [original_quotation_id];
```

### 3.3 Clone Train Entries
```sql
-- Clone train entries for sub-quotation
INSERT INTO package_tour_quotation_train_entries (
    id,
    quotation_id,
    -- ... all other fields
) 
SELECT 
    (SELECT MAX(id) + 1 FROM package_tour_quotation_train_entries),
    [new_quotation_id],
    -- ... all other fields from original
FROM package_tour_quotation_train_entries 
WHERE quotation_id = [original_quotation_id];
```

### 3.4 Clone Plane Entries
```sql
-- Clone plane entries for sub-quotation
INSERT INTO package_tour_quotation_plane_entries (
    id,
    quotation_id,
    -- ... all other fields
) 
SELECT 
    (SELECT MAX(id) + 1 FROM package_tour_quotation_plane_entries),
    [new_quotation_id],
    -- ... all other fields from original
FROM package_tour_quotation_plane_entries 
WHERE quotation_id = [original_quotation_id];
```

### 3.5 Clone Cruise Entries
```sql
-- Clone cruise entries for sub-quotation
INSERT INTO package_tour_quotation_cruise_entries (
    id,
    quotation_id,
    -- ... all other fields
) 
SELECT 
    (SELECT MAX(id) + 1 FROM package_tour_quotation_cruise_entries),
    [new_quotation_id],
    -- ... all other fields from original
FROM package_tour_quotation_cruise_entries 
WHERE quotation_id = [original_quotation_id];
```

### 3.6 Clone Hotel Entries
```sql
-- Clone hotel entries for sub-quotation
INSERT INTO package_tour_quotation_hotel_entries (
    id,
    quotation_id,
    -- ... all other fields
) 
SELECT 
    (SELECT MAX(id) + 1 FROM package_tour_quotation_hotel_entries),
    [new_quotation_id],
    -- ... all other fields from original
FROM package_tour_quotation_hotel_entries 
WHERE quotation_id = [original_quotation_id];
```

### 3.7 Clone Excursion Entries
```sql
-- Clone excursion entries for sub-quotation
INSERT INTO package_tour_quotation_excursion_entries (
    id,
    quotation_id,
    -- ... all other fields
) 
SELECT 
    (SELECT MAX(id) + 1 FROM package_tour_quotation_excursion_entries),
    [new_quotation_id],
    -- ... all other fields from original
FROM package_tour_quotation_excursion_entries 
WHERE quotation_id = [original_quotation_id];
```

### 3.8 Clone Program Entries
```sql
-- Clone program entries for sub-quotation
INSERT INTO package_quotation_program (
    id,
    quotation_id,
    -- ... all other fields
) 
SELECT 
    (SELECT MAX(id) + 1 FROM package_quotation_program),
    [new_quotation_id],
    -- ... all other fields from original
FROM package_quotation_program 
WHERE quotation_id = [original_quotation_id];
```

### 3.9 Clone Image Entries
```sql
-- Clone image entries for sub-quotation
INSERT INTO package_tour_quotation_images (
    id,
    quotation_id,
    -- ... all other fields
) 
SELECT 
    (SELECT MAX(id) + 1 FROM package_tour_quotation_images),
    [new_quotation_id],
    -- ... all other fields from original
FROM package_tour_quotation_images 
WHERE quotation_id = [original_quotation_id];
```

### 3.10 Clone Costing Entries
```sql
-- Clone costing entries for sub-quotation
INSERT INTO package_tour_quotation_costing_entries (
    id,
    quotation_id,
    -- ... all other fields
) 
SELECT 
    (SELECT MAX(id) + 1 FROM package_tour_quotation_costing_entries),
    [new_quotation_id],
    -- ... all other fields from original
FROM package_tour_quotation_costing_entries 
WHERE quotation_id = [original_quotation_id];
```

---

## Step 4: Query Operations

### 4.1 Get Next Quotation ID
```sql
-- Get the next available quotation ID
SELECT MAX(quotation_id) + 1 as next_quotation_id 
FROM package_tour_quotation_master;
```

### 4.2 Count Existing Sub-Quotations
```sql
-- Count existing sub-quotations for a parent
SELECT COUNT(*) as sub_quotation_count 
FROM package_tour_quotation_master 
WHERE parent_quotation_id = [parent_quotation_id];
```

### 4.3 Get Original Quotation Details
```sql
-- Get original quotation details for cloning
SELECT * FROM package_tour_quotation_master 
WHERE quotation_id = [original_quotation_id];
```

### 4.4 Get Parent Quotation Details (for nested sub-quotations)
```sql
-- Get parent quotation details when creating nested sub-quotations
SELECT quotation_date, quotation_display_id 
FROM package_tour_quotation_master 
WHERE quotation_id = [parent_quotation_id];
```

---

## Step 5: Data Retrieval Queries

### 5.1 Get All Sub-Quotations for a Parent
```sql
-- Get all sub-quotations for a specific parent quotation
SELECT 
    quotation_id,
    quotation_display_id,
    quotation_id_display,
    is_sub_quotation,
    parent_quotation_id,
    package_name,
    quotation_date,
    created_at
FROM package_tour_quotation_master 
WHERE parent_quotation_id = [parent_quotation_id]
ORDER BY quotation_display_id;
```

### 5.2 Get Hierarchical Quotation List
```sql
-- Get main quotations and their sub-quotations in hierarchical order
SELECT 
    m.quotation_id,
    m.quotation_display_id,
    m.quotation_id_display,
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

### 5.3 Get Sub-Quotation with All Related Data
```sql
-- Get complete sub-quotation data with all related entries
SELECT 
    q.quotation_id,
    q.quotation_display_id,
    q.is_sub_quotation,
    q.parent_quotation_id,
    q.package_name,
    q.quotation_date,
    p.day_count,
    p.attraction,
    p.day_image,
    p.stay,
    p.meal_plan
FROM package_tour_quotation_master q
LEFT JOIN package_quotation_program p ON q.quotation_id = p.quotation_id
WHERE q.quotation_id = [sub_quotation_id]
ORDER BY p.day_count;
```

---

## Step 6: Update Operations

### 6.1 Update Sub-Quotation Status
```sql
-- Update sub-quotation status or details
UPDATE package_tour_quotation_master 
SET 
    quotation_display_id = [new_display_id],
    quotation_id_display = [new_display_id],
    updated_at = NOW()
WHERE quotation_id = [sub_quotation_id];
```

### 6.2 Update Parent Reference
```sql
-- Update parent quotation reference
UPDATE package_tour_quotation_master 
SET parent_quotation_id = [new_parent_id]
WHERE quotation_id = [sub_quotation_id];
```

---

## Step 7: Delete Operations

### 7.1 Delete Sub-Quotation (Cascade Delete)
```sql
-- Delete sub-quotation and all related entries
-- Note: This should be done in reverse order of creation

-- Delete costing entries
DELETE FROM package_tour_quotation_costing_entries 
WHERE quotation_id = [sub_quotation_id];

-- Delete image entries
DELETE FROM package_tour_quotation_images 
WHERE quotation_id = [sub_quotation_id];

-- Delete program entries
DELETE FROM package_quotation_program 
WHERE quotation_id = [sub_quotation_id];

-- Delete excursion entries
DELETE FROM package_tour_quotation_excursion_entries 
WHERE quotation_id = [sub_quotation_id];

-- Delete hotel entries
DELETE FROM package_tour_quotation_hotel_entries 
WHERE quotation_id = [sub_quotation_id];

-- Delete cruise entries
DELETE FROM package_tour_quotation_cruise_entries 
WHERE quotation_id = [sub_quotation_id];

-- Delete plane entries
DELETE FROM package_tour_quotation_plane_entries 
WHERE quotation_id = [sub_quotation_id];

-- Delete train entries
DELETE FROM package_tour_quotation_train_entries 
WHERE quotation_id = [sub_quotation_id];

-- Delete transport entries
DELETE FROM package_tour_quotation_transport_entries2 
WHERE quotation_id = [sub_quotation_id];

-- Finally delete main quotation record
DELETE FROM package_tour_quotation_master 
WHERE quotation_id = [sub_quotation_id];
```

---

## Step 8: Validation Queries

### 8.1 Check for Orphaned Sub-Quotations
```sql
-- Find sub-quotations with invalid parent references
SELECT 
    quotation_id,
    quotation_display_id,
    parent_quotation_id
FROM package_tour_quotation_master 
WHERE is_sub_quotation = '1' 
AND parent_quotation_id NOT IN (
    SELECT quotation_id FROM package_tour_quotation_master 
    WHERE is_sub_quotation = '0'
);
```

### 8.2 Check Data Integrity
```sql
-- Check if all sub-quotations have proper parent relationships
SELECT 
    COUNT(*) as total_sub_quotations,
    COUNT(CASE WHEN parent_quotation_id IS NOT NULL THEN 1 END) as with_parent,
    COUNT(CASE WHEN parent_quotation_id IS NULL THEN 1 END) as orphaned
FROM package_tour_quotation_master 
WHERE is_sub_quotation = '1';
```

---

## Step 9: Performance Monitoring Queries

### 9.1 Monitor Sub-Quotation Usage
```sql
-- Monitor sub-quotation creation over time
SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_quotations,
    COUNT(CASE WHEN is_sub_quotation = '1' THEN 1 END) as sub_quotations,
    COUNT(CASE WHEN is_sub_quotation = '0' THEN 1 END) as main_quotations
FROM package_tour_quotation_master 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(created_at)
ORDER BY date DESC;
```

### 9.2 Check Index Usage
```sql
-- Check if indexes are being used effectively
SHOW INDEX FROM package_tour_quotation_master;
```

---

## Step 10: Rollback Queries (if needed)

### 10.1 Remove Sub-Quotation Fields
```sql
-- Remove sub-quotation fields (use with caution)
ALTER TABLE package_tour_quotation_master 
DROP COLUMN is_sub_quotation,
DROP COLUMN parent_quotation_id,
DROP COLUMN quotation_id_display;
```

### 10.2 Remove Indexes
```sql
-- Remove sub-quotation indexes
DROP INDEX idx_is_sub_quotation ON package_tour_quotation_master;
DROP INDEX idx_parent_quotation_id ON package_tour_quotation_master;
DROP INDEX idx_quotation_sub_quotation ON package_tour_quotation_master;
```

---

## Implementation Notes

1. **Dynamic Query Generation**: The main quotation cloning query is dynamically generated in PHP based on the actual table structure using `SHOW COLUMNS`.

2. **ID Management**: All related tables use auto-increment IDs that are managed by getting the MAX value and adding 1.

3. **Version Numbering**: Sub-quotations use a versioned display ID format (e.g., "QT2025001.1", "QT2025001.2").

4. **Data Integrity**: All related entries are cloned to maintain data consistency across the quotation system.

5. **Performance**: Indexes are added to improve query performance for sub-quotation operations.

6. **Error Handling**: The PHP implementation includes try-catch blocks to handle cases where fields might not exist.

---

## Tables Involved in Sub-Quotation System

1. **Main Table**: `package_tour_quotation_master`
2. **Related Tables**:
   - `package_tour_quotation_transport_entries2`
   - `package_tour_quotation_train_entries`
   - `package_tour_quotation_plane_entries`
   - `package_tour_quotation_cruise_entries`
   - `package_tour_quotation_hotel_entries`
   - `package_tour_quotation_excursion_entries`
   - `package_quotation_program`
   - `package_tour_quotation_images`
   - `package_tour_quotation_costing_entries`

---

## Support Files

- **Controller**: `/crm/controller/package_tour/quotation/quotation_sub_create.php`
- **Model**: `/crm/model/package_tour/quotation/quotation_sub_create.php`
- **Related Documentation**: `QUOTATION_SYSTEM_SQL_CHANGES.md`
