-- Migration script for group_tour_booking_transport_entries table
-- Run this ONLY if you already created the table with booking_id column

-- Option 1: If table exists with booking_id, rename the column
ALTER TABLE `group_tour_booking_transport_entries` 
CHANGE COLUMN `booking_id` `traveler_group_id` int(11) NOT NULL;

-- Option 2: If table doesn't exist yet, create it with correct column name
CREATE TABLE IF NOT EXISTS `group_tour_booking_transport_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `traveler_group_id` int(11) NOT NULL,
  `vehicle_name` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `pickup` varchar(500) NOT NULL,
  `pickup_type` varchar(50) NOT NULL,
  `drop_location` varchar(500) NOT NULL,
  `drop_type` varchar(50) NOT NULL,
  `service_duration` varchar(50) DEFAULT NULL,
  `vehicle_count` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

