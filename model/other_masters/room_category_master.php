<?php
class room_category_master{

public function category_save()
{
	$room_category = $_POST['room_category'];
	$status = $_POST['status'];

	$room_category1 = trim(addslashes($room_category));
	$room_category1 = str_replace("-","/",$room_category1);
	$sq_count = mysqli_num_rows(mysqlQuery("select entry_id from room_category_master where room_category='$room_category1'"));
	if($sq_count>0){
		echo "error--".$room_category1." already exists!";
		exit;
	}

	$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from room_category_master"));
	$entry_id = $sq_max['max'] + 1;
	$sq_insert = mysqlQuery("insert into room_category_master ( entry_id, room_category, active_status ) values ( '$entry_id', '$room_category1', '$status' )");
	if($sq_insert){
		echo "Room Category has been successfully saved.";
		exit;
	}
	else{
		echo "error--Room Category not saved!";
		exit;
	}
}

public function category_quick_save($room_category, $active_status = 'Active')
{
	$room_category1 = trim(addslashes($room_category));
	$room_category1 = str_replace("-", "/", $room_category1);

	if ($room_category1 == '') {
		echo json_encode(array('status' => 'error', 'message' => 'Room category is required.'));
		exit;
	}

	$sq_existing = mysqlQuery("select entry_id, room_category from room_category_master where room_category='$room_category1' limit 1");
	if (mysqli_num_rows($sq_existing) > 0) {
		$row = mysqli_fetch_assoc($sq_existing);
		echo json_encode(array(
			'status' => 'success',
			'entry_id' => $row['entry_id'],
			'room_category' => $row['room_category'],
			'existing' => true
		));
		exit;
	}

	$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from room_category_master"));
	$entry_id = $sq_max['max'] + 1;
	$sq_insert = mysqlQuery("insert into room_category_master ( entry_id, room_category, active_status ) values ( '$entry_id', '$room_category1', '$active_status' )");
	if (!$sq_insert) {
		echo json_encode(array('status' => 'error', 'message' => $room_category1 . ' not saved!'));
		exit;
	}

	echo json_encode(array(
		'status' => 'success',
		'entry_id' => $entry_id,
		'room_category' => $room_category1,
		'existing' => false
	));
	exit;
}

public function category_update()
{
	$entry_id = $_POST['entry_id'];
	$room_category = $_POST['room_category'];
	$active_flag = $_POST['status'];

	$room_category1 = trim(addslashes($room_category));
	$room_category1 = str_replace("-","/",$room_category1);
	$sq_count = mysqli_num_rows(mysqlQuery("select * from room_category_master where room_category='$room_category1' and entry_id!='$entry_id'"));

	if($sq_count>0){
		echo "error--".$room_category1." already exists!";
		exit;
	}

	$sq_insert = mysqlQuery("update room_category_master set room_category='$room_category1' , active_status='$active_flag'  where entry_id='$entry_id'");
	if($sq_insert){
		echo "Room Category has been successfully updated.";
		exit;
	}
	else{
		echo "error--Room Category not updated!";
		exit;
	}
}

}
?>