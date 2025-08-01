<?php
include "../../../../../../model/model.php";

$sale_type = $_POST['sale_type'];
$response = '<option value="">*Select Booking</option>';

if ($sale_type == "Hotel") {
    // Query for Hotel bookings
    $query = "SELECT booking_id, customer_id, created_at AS booking_date FROM hotel_booking_master WHERE delete_status = '0' ORDER BY booking_id DESC";
    $sq_booking = mysqlQuery($query);

    while ($row_booking = mysqli_fetch_assoc($sq_booking)) {
        $date = $row_booking['booking_date'];
        $yr = explode("-", $date);
        $year = $yr[0];

        $sq_customer = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM customer_master WHERE customer_id = '{$row_booking['customer_id']}'"));
        $customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ?
            $sq_customer['company_name'] :
            $sq_customer['first_name'] . " " . $sq_customer['last_name'];

        $response .= "<option value='{$row_booking['booking_id']}'>" . get_hotel_booking_id($row_booking['booking_id'], $year) . " - " . $customer_name . "</option>";
    }
} elseif ($sale_type == "Package Tour") {
    // Query for Package Tour bookings
    $query = "SELECT booking_id, customer_id, booking_date FROM package_tour_booking_master WHERE delete_status = '0' ORDER BY booking_id DESC";
    $sq_booking = mysqlQuery($query);

    while ($row_booking = mysqli_fetch_assoc($sq_booking)) {
        $date = $row_booking['booking_date'];
        $yr = explode("-", $date);
        $year = $yr[0];

        $sq_customer = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM customer_master WHERE customer_id = '{$row_booking['customer_id']}'"));
        $customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ?
            $sq_customer['company_name'] :
            $sq_customer['first_name'] . " " . $sq_customer['last_name'];

        $response .= "<option value='{$row_booking['booking_id']}'>" . get_package_booking_id($row_booking['booking_id'], $year) . " - " . $customer_name . "</option>";
    }
} elseif ($sale_type == "Group Tour") {
    // Query for Group Tour bookings
    $query = "SELECT booking_id, customer_id, booking_date FROM group_tour_booking_master WHERE delete_status = '0' ORDER BY booking_id DESC";
    $sq_booking = mysqlQuery($query);

    while ($row_booking = mysqli_fetch_assoc($sq_booking)) {
        $date = $row_booking['booking_date'];
        $yr = explode("-", $date);
        $year = $yr[0];

        $sq_customer = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM customer_master WHERE customer_id = '{$row_booking['customer_id']}'"));
        $customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ?
            $sq_customer['company_name'] :
            $sq_customer['first_name'] . " " . $sq_customer['last_name'];

        $response .= "<option value='{$row_booking['booking_id']}'>" . get_package_booking_id($row_booking['booking_id'], $year) . " - " . $customer_name . "</option>";
    }
}elseif($sale_type == 'Flight Ticket'){

      // Query for Flight bookings
      $query = "SELECT  ticket_id, customer_id, created_at AS booking_date FROM ticket_master WHERE delete_status = '0' ORDER BY ticket_id DESC";
      $sq_booking = mysqlQuery($query);
  
      while ($row_booking = mysqli_fetch_assoc($sq_booking)) {
          $date = $row_booking['booking_date'];
          $yr = explode("-", $date);
          $year = $yr[0];
  
          $sq_customer = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM customer_master WHERE customer_id = '{$row_booking['customer_id']}'"));
          $customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ?
              $sq_customer['company_name'] :
              $sq_customer['first_name'] . " " . $sq_customer['last_name'];
  
          $response .= "<option value='{$row_booking['ticket_id']}'>" . get_ticket_booking_id($row_booking['ticket_id'], $year) . " - " . $customer_name . "</option>";
      }

}
elseif($sale_type == 'Visa'){

    // Query for Flight bookings
    $query = "SELECT  visa_id, customer_id, created_at AS booking_date FROM visa_master WHERE delete_status = '0' ORDER BY visa_id DESC";
    $sq_booking = mysqlQuery($query);

    while ($row_booking = mysqli_fetch_assoc($sq_booking)) {
        $date = $row_booking['booking_date'];
        $yr = explode("-", $date);
        $year = $yr[0];

        $sq_customer = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM customer_master WHERE customer_id = '{$row_booking['customer_id']}'"));
        $customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ?
            $sq_customer['company_name'] :
            $sq_customer['first_name'] . " " . $sq_customer['last_name'];

        $response .= "<option value='{$row_booking['visa_id']}'>" . get_visa_booking_id($row_booking['visa_id'], $year) . " - " . $customer_name . "</option>";
    }

}

elseif($sale_type == 'Car Rental'){

    // Query for Flight bookings
    $query = "SELECT  booking_id, customer_id, created_at AS booking_date FROM car_rental_booking WHERE delete_status = '0' ORDER BY booking_id DESC";
    $sq_booking = mysqlQuery($query);

    while ($row_booking = mysqli_fetch_assoc($sq_booking)) {
        $date = $row_booking['booking_date'];
        $yr = explode("-", $date);
        $year = $yr[0];

        $sq_customer = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM customer_master WHERE customer_id = '{$row_booking['customer_id']}'"));
        $customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ?
            $sq_customer['company_name'] :
            $sq_customer['first_name'] . " " . $sq_customer['last_name'];

        $response .= "<option value='{$row_booking['booking_id']}'>" . get_car_rental_booking_id($row_booking['booking_id'], $year) . " - " . $customer_name . "</option>";
    }

}


elseif($sale_type == 'Excursion'){

    // Query for Flight bookings
    $query = "SELECT  exc_id, customer_id, created_at AS booking_date FROM excursion_master WHERE delete_status = '0' ORDER BY exc_id DESC";
    $sq_booking = mysqlQuery($query);

    while ($row_booking = mysqli_fetch_assoc($sq_booking)) {
        $date = $row_booking['booking_date'];
        $yr = explode("-", $date);
        $year = $yr[0];

        $sq_customer = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM customer_master WHERE customer_id = '{$row_booking['customer_id']}'"));
        $customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ?
            $sq_customer['company_name'] :
            $sq_customer['first_name'] . " " . $sq_customer['last_name'];

        $response .= "<option value='{$row_booking['exc_id']}'>" . get_exc_booking_id($row_booking['exc_id'], $year) . " - " . $customer_name . "</option>";
    }

}
elseif($sale_type == 'Train Ticket'){

    // Query for Flight bookings
    $query = "SELECT  train_ticket_id, customer_id, created_at AS booking_date FROM train_ticket_master WHERE delete_status = '0' ORDER BY train_ticket_id DESC";
    $sq_booking = mysqlQuery($query);

    while ($row_booking = mysqli_fetch_assoc($sq_booking)) {
        $date = $row_booking['booking_date'];
        $yr = explode("-", $date);
        $year = $yr[0];

        $sq_customer = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM customer_master WHERE customer_id = '{$row_booking['customer_id']}'"));
        $customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ?
            $sq_customer['company_name'] :
            $sq_customer['first_name'] . " " . $sq_customer['last_name'];

        $response .= "<option value='{$row_booking['train_ticket_id']}'>" . get_train_ticket_booking_id($row_booking['train_ticket_id'], $year) . " - " . $customer_name . "</option>";
    }

}

elseif($sale_type == 'Bus'){

    // Query for Flight bookings
    $query = "SELECT  booking_id, customer_id, created_at AS booking_date FROM bus_booking_master WHERE delete_status = '0' ORDER BY booking_id DESC";
    $sq_booking = mysqlQuery($query);

    while ($row_booking = mysqli_fetch_assoc($sq_booking)) {
        $date = $row_booking['booking_date'];
        $yr = explode("-", $date);
        $year = $yr[0];

        $sq_customer = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM customer_master WHERE customer_id = '{$row_booking['customer_id']}'"));
        $customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ?
            $sq_customer['company_name'] :
            $sq_customer['first_name'] . " " . $sq_customer['last_name'];

        $response .= "<option value='{$row_booking['booking_id']}'>" . get_bus_booking_id($row_booking['booking_id'], $year) . " - " . $customer_name . "</option>";
    }

}

elseif($sale_type == 'Miscellaneous'){

    // Query for Flight bookings
    $query = "SELECT  misc_id, customer_id, created_at AS booking_date FROM miscellaneous_master WHERE delete_status = '0' ORDER BY misc_id DESC";
    $sq_booking = mysqlQuery($query);

    while ($row_booking = mysqli_fetch_assoc($sq_booking)) {
        $date = $row_booking['booking_date'];
        $yr = explode("-", $date);
        $year = $yr[0];

        $sq_customer = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM customer_master WHERE customer_id = '{$row_booking['customer_id']}'"));
        $customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ?
            $sq_customer['company_name'] :
            $sq_customer['first_name'] . " " . $sq_customer['last_name'];

        $response .= "<option value='{$row_booking['misc_id']}'>" . get_misc_booking_id($row_booking['misc_id'], $year) . " - " . $customer_name . "</option>";
    }

}


 else {
    $response = '<option value="">Invalid Sale Type</option>';
}

// Output the response
echo $response;
?>
