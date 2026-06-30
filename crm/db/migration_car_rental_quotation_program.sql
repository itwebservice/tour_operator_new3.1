-- Migration: car rental quotation itinerary program
-- Run once on existing databases where car_rental_quotation_program is missing.
-- Required for CRM >> Car Rental quotation save, update, and view.

CREATE TABLE IF NOT EXISTS `car_rental_quotation_program` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `attraction` varchar(255) NOT NULL,
  `day_wise_program` text NOT NULL,
  `stay` varchar(80) NOT NULL,
  `meal_plan` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_quotation_id` (`quotation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
