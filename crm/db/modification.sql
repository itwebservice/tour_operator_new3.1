ALTER TABLE custom_package_master
ADD COLUMN seo_slug VARCHAR(150) NOT NULL AFTER package_name,
ADD COLUMN tour_theme INT(11) NOT NULL;


ALTER TABLE tour_master
ADD COLUMN seo_slug VARCHAR(255) NOT NULL AFTER tour_name;

ALTER TABLE tour_master
ADD COLUMN tour_note TEXT NOT NULL AFTER tour_type;



alter table enquiry_master add column tour_name text not null;



alter table b2c_settings add column services text not null;


alter table b2c_settings add column about_us text not null;


INSERT INTO b2c_generic_settings (question, answer, status) VALUES
('Do you want to activate live booking for Hotel service?', 'Yes', '1'),
('Do you want to activate live booking for Activity service?', 'Yes', '1'),
('Do you want to activate live booking for Transfer service?', 'Yes', '1'),
('Do you want to activate live booking for Cruse service?', 'Yes', '1');



ALTER TABLE vendor_payment_master
ADD COLUMN currency_code varchar(255);

ALTER TABLE exc_payment_master 
ADD COLUMN currency_code varchar(255);


ALTER TABLE visa_payment_master 
ADD COLUMN currency_code varchar(255);


ALTER TABLE car_rental_booking 
ADD COLUMN currency_code varchar(255);


ALTER TABLE bus_booking_payment_master
ADD COLUMN currency_code varchar(255);


  ALTER TABLE miscellaneous_master 
ADD COLUMN currency_code varchar(255); 




ALTER TABLE payment_master ADD COLUMN currency_code VARCHAR(255) NOT NULL;

ALTER TABLE ticket_master ADD COLUMN currency_code VARCHAR(255) NOT NULL;

ALTER TABLE ticket_payment_master ADD COLUMN currency_code VARCHAR(255) NOT NULL;


ALTER TABLE  car_rental_payment ADD COLUMN currency_code VARCHAR(255) NOT NULL;
 
ALTER TABLE bus_booking_master ADD COLUMN currency_code VARCHAR(255) NOT NULL;

ALTER TABLE miscellaneous_payment_master ADD COLUMN currency_code VARCHAR(255) NOT NULL;

  ALTER TABLE flight_quotation_master 
ADD COLUMN currency_code VARCHAR(255) NOT NULL;


  ALTER TABLE car_rental_quotation_master 
ADD COLUMN currency_code VARCHAR(255) NOT NULL;

 ALTER TABLE package_payment_master ADD COLUMN currency_code VARCHAR(255) NOT NULL;


 ALTER TABLE package_train_master 
ADD COLUMN train_travel_date2 DATETIME;


ALTER TABLE travelers_details 
ADD COLUMN driving_license TEXT NOT NULL;


ALTER TABLE package_travelers_details 
ADD COLUMN driving_license TEXT NOT NULL;


ALTER TABLE ticket_master_entries 
ADD COLUMN driving_license TEXT NOT NULL;


ALTER TABLE visa_master_entries 
ADD COLUMN driving_license TEXT NOT NULL;



   ALTER TABLE `excursion_master_tariff_basics` 
   ADD COLUMN `vehicle_id` VARCHAR(50) NULL AFTER `transfer_cost`;


ALTER TABLE `branches` 
ADD COLUMN `qr_url` TEXT NULL AFTER `state`;


-- Add Logo URL column
ALTER TABLE `branches` 
ADD COLUMN `logo_url` TEXT NULL AFTER `qr_url`;


-- For Package Quotation Excursion
ALTER TABLE `package_tour_quotation_excursion_entries` 
ADD COLUMN `vehicle_id` VARCHAR(50) NULL AFTER `vehicles`;


-- for car rental qtn
CREATE TABLE car_rental_quotation_program (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    quotation_id INT(11) NOT NULL,
    package_id INT(11) NOT NULL,
    attraction VARCHAR(255) NOT NULL,
    day_wise_program TEXT NOT NULL,
    stay VARCHAR(255) NOT NULL,
    meal_plan VARCHAR(20) NOT NULL,
    day_count INT(11) NOT NULL
);


CREATE TABLE car_rental_booking_program (
    entry_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
   
    booking_id INT(11) NOT NULL,
    attraction VARCHAR(255) NOT NULL,
    day_wise_program TEXT NOT NULL,
    stay VARCHAR(255) NOT NULL,
    meal_plan VARCHAR(20) NOT NULL,
    day_count INT(11) NOT NULL
);

ALTER TABLE excursion_master
ADD COLUMN guest_name VARCHAR(255) NOT NULL,
ADD COLUMN pick_point VARCHAR(255) NOT NULL;

ALTER TABLE excursion_master_entries
ADD COLUMN vehicle_name VARCHAR(255) NOT NULL;

ALTER TABLE package_tour_excursion_master
ADD COLUMN vehicle_name VARCHAR(255) NOT NULL;


Alter table vendor_payment_master add column credit_charges varchar(255) not null,

add column credit_charge_amount varchar(255) not null,
add column credit_charge_tax varchar(255) not null,
add column credit_card_details varchar(255) not null,
add column credit_charge_tax_amount varchar(255) not null;


CREATE TABLE tour_groups_transport (
    entry_id INT(11) NOT NULL AUTO_INCREMENT,
    tour_id INT(11) NOT NULL,
    vehicle_name VARCHAR(255) NOT NULL,
    pickup VARCHAR(500) NOT NULL,
    pickup_type VARCHAR(50) NOT NULL,
    drop_location VARCHAR(500) NOT NULL,
    drop_type VARCHAR(50) NOT NULL,
    PRIMARY KEY (entry_id)
);

CREATE TABLE group_tour_quotation_transport_entries (
    id INT(11) NOT NULL AUTO_INCREMENT,
    quotation_id INT(11) NOT NULL,
    vehicle_name VARCHAR(255) NOT NULL,
    start_date DATE,
    end_date DATE,
    pickup VARCHAR(500) NOT NULL,
    pickup_type VARCHAR(50) NOT NULL,
    drop_location VARCHAR(500) NOT NULL,
    drop_type VARCHAR(50) NOT NULL,
    service_duration VARCHAR(50),
    vehicle_count VARCHAR(50),
    PRIMARY KEY (id)
);
