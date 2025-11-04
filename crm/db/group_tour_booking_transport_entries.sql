-- Create table for group tour booking transport entries
CREATE TABLE IF NOT EXISTS `group_tour_booking_transport_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
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

