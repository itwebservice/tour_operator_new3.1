<?php
include "../../../../../../model/model.php";

$tour_id = $_POST['tour_id'];

if ($tour_id != "") {
    $query = "SELECT group_id, from_date, to_date FROM tour_groups WHERE tour_id = '$tour_id' AND status = 'Active'";
    $result = mysqlQuery($query);
    echo '<option value="">Tour Date</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        $from_date = date('d-m-Y', strtotime($row['from_date']));
        $to_date = date('d-m-Y', strtotime($row['to_date']));
        echo "<option value='{$row['group_id']}'>$from_date to $to_date</option>";
    }
}
?>
