<?php
class vehicle_master{

    function vehicle_save(){
        $vehicle_type = $_POST['vehicle_type'];
        $vehicle_name = addslashes($_POST['vehicle_name']);
        $seating_c = $_POST['seating_c'];

        $sq_count = mysqli_num_rows(mysqlQuery("select entry_id from b2b_transfer_master where vehicle_name='$vehicle_name' and vehicle_type='$vehicle_type'"));
        if($sq_count > 0){
            echo 'error--Vehicle name already added';
            exit;
        }
        $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from b2b_transfer_master"));
        $entry_id = $sq_max['max'] + 1;
        $sq_query = mysqlQuery("INSERT INTO `b2b_transfer_master`(`entry_id`,`vehicle_type`,`vehicle_name`, `seating_capacity`,`image_url`, `cancellation_policy`, `status`) VALUES ('$entry_id','$vehicle_type','$vehicle_name','$seating_c','','','Active')");
        if($sq_query){
            echo "<option value=".$entry_id.">$vehicle_name</option>";
            exit;
        }else{
            echo 'error--Vehicle Details not added succesfully';
            exit;
        }

    }

    function vehicle_quick_save($vehicle_name, $vehicle_type = 'Private Car', $seating_capacity = '4', $status = 'Active')
    {
        $vehicle_name1 = trim(addslashes($vehicle_name));
        $vehicle_type1 = addslashes($vehicle_type);
        $seating_capacity1 = addslashes($seating_capacity);
        $status1 = addslashes($status);

        if ($vehicle_name1 == '') {
            echo json_encode(array('status' => 'error', 'message' => 'Vehicle name is required.'));
            exit;
        }

        $sq_existing = mysqlQuery("select entry_id, vehicle_name, vehicle_type, seating_capacity from b2b_transfer_master where vehicle_name='$vehicle_name1' and vehicle_type='$vehicle_type1' limit 1");
        if (mysqli_num_rows($sq_existing) > 0) {
            $row = mysqli_fetch_assoc($sq_existing);
            echo json_encode(array(
                'status' => 'success',
                'entry_id' => $row['entry_id'],
                'vehicle_name' => $row['vehicle_name'],
                'vehicle_type' => $row['vehicle_type'],
                'seating_capacity' => $row['seating_capacity'],
                'existing' => true
            ));
            exit;
        }

        $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from b2b_transfer_master"));
        $entry_id = $sq_max['max'] + 1;

        $sq_query = mysqlQuery("INSERT INTO `b2b_transfer_master`(`entry_id`,`vehicle_type`,`vehicle_name`, `seating_capacity`,`image_url`, `cancellation_policy`, `status`) VALUES ('$entry_id','$vehicle_type1','$vehicle_name1','$seating_capacity1','','','$status1')");
        if (!$sq_query) {
            echo json_encode(array('status' => 'error', 'message' => $vehicle_name1 . ' not saved!'));
            exit;
        }

        echo json_encode(array(
            'status' => 'success',
            'entry_id' => $entry_id,
            'vehicle_name' => $vehicle_name1,
            'vehicle_type' => $vehicle_type,
            'seating_capacity' => $seating_capacity,
            'existing' => false
        ));
        exit;
    }
}
?>