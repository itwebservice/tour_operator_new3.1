<?php 
error_reporting(E_ALL);
include_once 'config.php';


// Fetch settings and decode popular destinations
$setData = mysqli_fetch_object(mysqlQuery("SELECT * FROM b2c_settings LIMIT 1"));
$popularDest = json_decode($setData->popular_dest);

if (empty($popularDest)) {
    echo json_encode(array());
    exit;
}

// Fetch all active packages with destination names
$resultPackage = mysqlQuery("
    SELECT 
        cpm.package_id, 
        cpm.dest_id, 
        cpm.package_name, 
        cpm.tour_type, 
        dm.dest_name 
    FROM custom_package_master cpm
    LEFT JOIN destination_master dm ON cpm.dest_id = dm.dest_id
    WHERE cpm.status = 'Active'
");

$packageData = array();	
while ($data = mysqli_fetch_array($resultPackage)) {
    $packageData[$data['package_id']] = array(
        'package_id'   => $data['package_id'],
        'dest_id'      => $data['dest_id'],
        'dest_name'    => $data['dest_name'],
        'package_name' => $data['package_name'],
        'tour_type'    => $data['tour_type']
    );
}

// Filter only popular destinations
$popularDestDataHeader = $packageData;
/*
foreach ($popularDest as $destination) {
    if (isset($packageData[$destination->package_id])) {
        $popularDestDataHeader[] = $packageData[$destination->package_id];
    }
}*/


//print_r($popularDestDataHeader); exit;


?>
