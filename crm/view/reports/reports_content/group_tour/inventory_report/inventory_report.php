<?php
include "../../../../../model/model.php";
require_once('../../../../../classes/tour_booked_seats.php');

$array_s = array();
$temp_arr = array();
$tour_id= $_POST['tour_id'];
$group_id= $_POST['group_id'];
$status= $_POST['status'];
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_POST['branch_status'];

$count=0;
$query = "select tg.*, tm.tour_name, tm.dest_id from tour_groups tg
          INNER JOIN tour_master tm ON tg.tour_id = tm.tour_id 
          WHERE 1";

if($tour_id!="")
{
	$query .= " and tg.tour_id = '$tour_id'";
}
if($group_id!="")
{
	$query .= " and tg.group_id = '$group_id'";
}
if($status!="")
{
	$query .= " and tg.status = '$status'";
}

$query .= " ORDER BY tg.from_date ASC";

$sq_groups = mysqlQuery($query);
while($row_groups = mysqli_fetch_assoc($sq_groups))
{
	$count++;
	
	$tour_id = $row_groups['tour_id'];
	$tour_group_id = $row_groups['group_id'];
	$tour_name = $row_groups['tour_name'];
	
	// Get destination name
	$destination = $tour_name; // Default to tour name
	if($row_groups['dest_id'] != '' && $row_groups['dest_id'] != '0'){
		$sq_dest = mysqli_fetch_assoc(mysqlQuery("select dest_name from destination_master where dest_id='".$row_groups['dest_id']."'"));
		if($sq_dest){
			$destination = $sq_dest['dest_name'];
		}
	}
	
	$from_date = date("d-m-Y", strtotime($row_groups['from_date']));
	$to_date = date("d-m-Y", strtotime($row_groups['to_date']));
	$capacity = $row_groups['capacity'];
	
	// Calculate booked seats using the existing class
	$booked_seats = $bk_seats->booked_seats($tour_id, $tour_group_id);
	
	// Calculate available seats
	$available_seats = $capacity - $booked_seats;
	
	// Color coding based on availability
	$bg = "";
	if($available_seats <= 0){
		$bg = "danger"; // Fully booked
	}
	else if($available_seats <= 5){
		$bg = "warning"; // Almost full
	}
	
	$temp_arr = array( "data" => array(
		(int)($count),
		$destination,
		$from_date,
		$to_date,
		(int)$capacity,
		(int)$booked_seats,
		(int)$available_seats
		), "bg" =>$bg);
	array_push($array_s,$temp_arr);
}
echo json_encode($array_s);
?>

