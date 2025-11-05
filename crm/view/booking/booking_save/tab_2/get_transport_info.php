<?php
include "../../../../model/model.php";

$tour_id = $_POST['tour_id'];  // Changed from tour_group_id to tour_id

$array_s = array();
$sq_transport = mysqlQuery("select * from tour_groups_transport where tour_id='$tour_id'");
while($row_transport = mysqli_fetch_assoc($sq_transport)){
    
    // Get Vehicle Name
    $sq_vehicle = mysqli_fetch_assoc(mysqlQuery("select vehicle_name from b2b_transfer_master where entry_id = '".$row_transport['vehicle_name']."'"));
    $vehicle_name = $sq_vehicle['vehicle_name'] ? $sq_vehicle['vehicle_name'] : 'N/A';
    
    // Get Pickup Location
    $pickup_location = '';
    $pickup_value = '';
    if($row_transport['pickup_type'] == 'city'){
        $row = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='".$row_transport['pickup']."'"));
        $pickup_location = $row['city_name'] ? $row['city_name'] : 'N/A';
        $pickup_value = 'city-'.$row_transport['pickup'];
    }
    else if($row_transport['pickup_type'] == 'hotel'){
        $row = mysqli_fetch_assoc(mysqlQuery("select hotel_name from hotel_master where hotel_id='".$row_transport['pickup']."'"));
        $pickup_location = $row['hotel_name'] ? $row['hotel_name'] : 'N/A';
        $pickup_value = 'hotel-'.$row_transport['pickup'];
    }
    else if($row_transport['pickup_type'] == 'airport'){
        $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code from airport_master where airport_id='".$row_transport['pickup']."'"));
        if($row){
            $pickup_location = $row['airport_name']." (".$row['airport_code'].")";
        }
        $pickup_value = 'airport-'.$row_transport['pickup'];
    }
    
    // Get Drop Location
    $drop_location = '';
    $drop_value = '';
    if($row_transport['drop_type'] == 'city'){
        $row = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='".$row_transport['drop_location']."'"));
        $drop_location = $row['city_name'] ? $row['city_name'] : 'N/A';
        $drop_value = 'city-'.$row_transport['drop_location'];
    }
    else if($row_transport['drop_type'] == 'hotel'){
        $row = mysqli_fetch_assoc(mysqlQuery("select hotel_name from hotel_master where hotel_id='".$row_transport['drop_location']."'"));
        $drop_location = $row['hotel_name'] ? $row['hotel_name'] : 'N/A';
        $drop_value = 'hotel-'.$row_transport['drop_location'];
    }
    else if($row_transport['drop_type'] == 'airport'){
        $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code from airport_master where airport_id='".$row_transport['drop_location']."'"));
        if($row){
            $drop_location = $row['airport_name']." (".$row['airport_code'].")";
        }
        $drop_value = 'airport-'.$row_transport['drop_location'];
    }
    
    $temp_arr = array(
        'vehicle_id' => $row_transport['vehicle_name'],
        'vehicle_name' => $vehicle_name,
        'pickup_location' => $pickup_location,
        'pickup_value' => $pickup_value,
        'pickup_type' => $row_transport['pickup_type'],
        'drop_location' => $drop_location,
        'drop_value' => $drop_value,
        'drop_type' => $row_transport['drop_type'],
        'service_duration' => $row_transport['service_duration'],
        'vehicle_count' => $row_transport['vehicle_count']
    );
    array_push($array_s, $temp_arr);
}
echo json_encode($array_s);
?>

