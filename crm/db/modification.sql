ALTER TABLE custom_package_master
ADD COLUMN seo_slug VARCHAR(150) NOT NULL AFTER package_name,
ADD COLUMN tour_theme INT(11) NOT NULL;


ALTER TABLE tour_master
ADD COLUMN seo_slug VARCHAR(255) NOT NULL AFTER tour_name;

ALTER TABLE tour_master
ADD COLUMN tour_note TEXT NOT NULL AFTER tour_type;



alter table enquiry_master add column tour_name text not null;



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
