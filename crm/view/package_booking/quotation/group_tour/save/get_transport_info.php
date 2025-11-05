<?php
include "../../../../../model/model.php";

$tour_id = $_POST['tour_id'];

$array_s = array();
$temp_arr = array();

$count = 0;
$sq_transport = mysqlQuery("select * from tour_groups_transport where tour_id='$tour_id'");
while($row_transport = mysqli_fetch_assoc($sq_transport)){
    $count++;
    
    // Get Vehicle Name
    $sq_vehicle = mysqli_fetch_assoc(mysqlQuery("select vehicle_name from b2b_transfer_master where entry_id = '".$row_transport['vehicle_name']."'"));
    $vehicle_name = $sq_vehicle['vehicle_name'] ? $sq_vehicle['vehicle_name'] : '';
    
    // Get Pickup Location based on type
    $pickup_location = '';
    $pickup_value = '';
    if($row_transport['pickup_type'] == 'city'){
        $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='".$row_transport['pickup']."'"));
        if($row){
            $pickup_location = $row['city_name'];
            $pickup_value = 'city-'.$row['city_id'];
        }
    }
    else if($row_transport['pickup_type'] == 'hotel'){
        $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='".$row_transport['pickup']."'"));
        if($row){
            $pickup_location = $row['hotel_name'];
            $pickup_value = 'hotel-'.$row['hotel_id'];
        }
    }
    else if($row_transport['pickup_type'] == 'airport'){
        $row = mysqli_fetch_assoc(mysqlQuery("select airport_id,airport_name, airport_code from airport_master where airport_id='".$row_transport['pickup']."'"));
        if($row){
            $pickup_location = $row['airport_name']." (".$row['airport_code'].")";
            $pickup_value = 'airport-'.$row['airport_id'];
        }
    }
    
    // Get Drop Location based on type
    $drop_location = '';
    $drop_value = '';
    if($row_transport['drop_type'] == 'city'){
        $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='".$row_transport['drop_location']."'"));
        if($row){
            $drop_location = $row['city_name'];
            $drop_value = 'city-'.$row['city_id'];
        }
    }
    else if($row_transport['drop_type'] == 'hotel'){
        $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='".$row_transport['drop_location']."'"));
        if($row){
            $drop_location = $row['hotel_name'];
            $drop_value = 'hotel-'.$row['hotel_id'];
        }
    }
    else if($row_transport['drop_type'] == 'airport'){
        $row = mysqli_fetch_assoc(mysqlQuery("select airport_id,airport_name, airport_code from airport_master where airport_id='".$row_transport['drop_location']."'"));
        if($row){
            $drop_location = $row['airport_name']." (".$row['airport_code'].")";
            $drop_value = 'airport-'.$row['airport_id'];
        }
    }
    
    $temp_arr = array(
        'vehicle_id' => $row_transport['vehicle_name'],
        'vehicle_name' => $vehicle_name,
        'pickup_location' => $pickup_location,
        'pickup_value' => $pickup_value,
        'pickup_type' => $row_transport['pickup_type'],
        'drop_location' => $drop_location,
        'drop_value' => $drop_value,
        'drop_type' => $row_transport['drop_type']
    );
    array_push($array_s, $temp_arr);
}

echo json_encode($array_s);
?>



