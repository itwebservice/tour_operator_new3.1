-- Fix column name from 'no_vehicles' to 'vehicle_count'
-- Run this if you already created the table with 'no_vehicles' column

-- For group_tour_quotation_transport_entries table
-- Check if column 'no_vehicles' exists and rename it to 'vehicle_count'

-- Option 1: If column 'no_vehicles' exists, rename it
ALTER TABLE group_tour_quotation_transport_entries 
CHANGE COLUMN no_vehicles vehicle_count VARCHAR(50);

-- Option 2: If table needs to be recreated completely
-- DROP TABLE IF EXISTS group_tour_quotation_transport_entries;

-- CREATE TABLE group_tour_quotation_transport_entries (
--     id INT(11) NOT NULL AUTO_INCREMENT,
--     quotation_id INT(11) NOT NULL,
--     vehicle_name VARCHAR(255) NOT NULL,
--     start_date DATE,
--     end_date DATE,
--     pickup VARCHAR(500) NOT NULL,
--     pickup_type VARCHAR(50) NOT NULL,
--     drop_location VARCHAR(500) NOT NULL,
--     drop_type VARCHAR(50) NOT NULL,
--     service_duration VARCHAR(50),
--     vehicle_count VARCHAR(50),
--     PRIMARY KEY (id)
-- );



