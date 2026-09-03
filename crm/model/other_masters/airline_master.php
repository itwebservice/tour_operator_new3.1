<?php 
$flag = true;
class airline_master{

	public function airline_save()
	{
		$airline_name_arr = $_POST['airline_name_arr'];
		$airline_code_arr = ($_POST['airline_code_arr']!='')?$_POST['airline_code_arr']:[];
		$airline_status_arr = $_POST['airline_status_arr'];

		begin_t();

		for($i=0; $i<sizeof($airline_code_arr); $i++){
			$airline_name1 = addslashes($airline_name_arr[$i]);
			$airline_code1 = addslashes($airline_code_arr[$i]);

			$sq_count = mysqli_num_rows(mysqlQuery("select airline_id from airline_master where airline_name='$airline_name1' and airline_code='$airline_code1'"));
			if($sq_count>0){
				$GLOBALS['flag'] = false;
				echo "error--".$airline_name1.'('.$airline_code1.')'." already exists!";
				exit;
			}
			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(airline_id) as max from airline_master"));
			$airline_id = $sq_max['max'] + 1;
			$airline_status = ($airline_status_arr[$i] != '') ? $airline_status_arr[$i] : 'Active';

			$sq_airline = mysqlQuery("insert into airline_master (airline_id, airline_code, airline_name, active_flag) values ('$airline_id','$airline_code1', '$airline_name1',  '$airline_status')");
			if(!$sq_airline){
				$GLOBALS['flag'] = false;
				echo "error--Some entries not saved";
				exit;
			}

		}

		if($GLOBALS['flag']){
			commit_t();
			echo "Airline has been successfully saved.";
			exit;
		}
		else{
			rollback_t();
			exit;
		}
	}

	public function airline_quick_save($airline_input, $active_flag = 'Active')
	{
		$airline_input = trim($airline_input);
		if ($airline_input == '') {
			echo json_encode(array('status' => 'error', 'message' => 'Airline name is required.'));
			exit;
		}

		$airline_name = $airline_input;
		$airline_code = '';
		if (preg_match('/^(.+?)\s*\(([^)]+)\)\s*$/', $airline_input, $matches)) {
			$airline_name = trim($matches[1]);
			$airline_code = trim($matches[2]);
		}

		if ($airline_name == '') {
			echo json_encode(array('status' => 'error', 'message' => 'Airline name is required.'));
			exit;
		}

		$airline_name1 = addslashes($airline_name);

		$sq_existing = mysqlQuery("select airline_id, airline_name, airline_code from airline_master where airline_name='$airline_name1' limit 1");
		if (mysqli_num_rows($sq_existing) > 0) {
			$row = mysqli_fetch_assoc($sq_existing);
			echo json_encode(array(
				'status' => 'success',
				'airline_id' => $row['airline_id'],
				'airline_name' => $row['airline_name'],
				'airline_code' => $row['airline_code'],
				'label' => $row['airline_name'] . ' (' . $row['airline_code'] . ')',
				'existing' => true
			));
			exit;
		}

		if ($airline_code == '') {
			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(airline_id) as max from airline_master"));
			$airline_code = (string) ($sq_max['max'] + 1);
		}

		$airline_code1 = addslashes($airline_code);
		$sq_dup = mysqli_num_rows(mysqlQuery("select airline_id from airline_master where airline_name='$airline_name1' and airline_code='$airline_code1'"));
		if ($sq_dup > 0) {
			$row = mysqli_fetch_assoc(mysqlQuery("select airline_id, airline_name, airline_code from airline_master where airline_name='$airline_name1' and airline_code='$airline_code1' limit 1"));
			echo json_encode(array(
				'status' => 'success',
				'airline_id' => $row['airline_id'],
				'airline_name' => $row['airline_name'],
				'airline_code' => $row['airline_code'],
				'label' => $row['airline_name'] . ' (' . $row['airline_code'] . ')',
				'existing' => true
			));
			exit;
		}

		$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(airline_id) as max from airline_master"));
		$airline_id = $sq_max['max'] + 1;

		$sq_airline = mysqlQuery("insert into airline_master (airline_id, airline_code, airline_name, active_flag) values ('$airline_id', '$airline_code1', '$airline_name1', '$active_flag')");
		if (!$sq_airline) {
			echo json_encode(array('status' => 'error', 'message' => $airline_name1 . ' not saved!'));
			exit;
		}

		echo json_encode(array(
			'status' => 'success',
			'airline_id' => $airline_id,
			'airline_name' => $airline_name,
			'airline_code' => $airline_code,
			'label' => $airline_name . ' (' . $airline_code . ')',
			'existing' => false
		));
		exit;
	}

	public function airline_update()
	{
		$airline_id = $_POST['airline_id'];
		$airline_name = $_POST['airline_name'];
		$airline_code = $_POST['airline_code'];
		$airline_status = $_POST['airline_status'];

		$airline_name1 = addslashes($airline_name);
		$airline_code1 = addslashes($airline_code);

		$sq_count = mysqli_num_rows(mysqlQuery("select airline_id from airline_master where airline_name='$airline_name1' and airline_code='$airline_code1' and airline_id!='$airline_id'"));
		if($sq_count>0){
			$GLOBALS['flag'] = false;
			echo "error--".$airline_name1.'('.$airline_code1.')'." already exists!";
			exit;
		}

		$sq_airline = mysqlQuery("update airline_master set airline_name='$airline_name1', airline_code='$airline_code1', active_flag='$airline_status' where airline_id='$airline_id'");
		if($sq_airline){
			echo "Airline has been successfully updated.";
			exit;
		}
		else{
			echo "error--Airline not updated";
			exit;
		}

	}

}
?>