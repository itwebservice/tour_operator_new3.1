-- Migration: group tour quotation transport details
-- Run once on existing databases where group_tour_quotation_transport_entries is missing.
-- Required for CRM >> Group Quotation save, update, and view.

CREATE TABLE IF NOT EXISTS `group_tour_quotation_transport_entries` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `vehicle_name` varchar(150) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `pickup` varchar(500) NOT NULL,
  `pickup_type` varchar(50) NOT NULL,
  `drop_location` varchar(500) NOT NULL,
  `drop_type` varchar(50) NOT NULL,
  `service_duration` varchar(50) NOT NULL,
  `vehicle_count` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_quotation_id` (`quotation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
