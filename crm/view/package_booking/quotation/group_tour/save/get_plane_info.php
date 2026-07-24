<?php 
include_once('../../../../../model/model.php');

$group_id = isset($_POST['group_id']) ? $_POST['group_id'] : '';
$plane_info_arr = array();

if ($group_id === '' || $group_id === null) {
	echo json_encode($plane_info_arr);
	exit;
}

$sq_group = mysqli_fetch_assoc(mysqlQuery("select * from tour_groups where group_id='$group_id'"));
$tour_id = isset($sq_group['tour_id']) ? intval($sq_group['tour_id']) : 0;
if ($tour_id <= 0) {
	echo json_encode($plane_info_arr);
	exit;
}

$query = "select * from group_tour_plane_entries where tour_id='$tour_id'";
$sq_plane = mysqlQuery($query);

while ($row_plane = mysqli_fetch_assoc($sq_plane)) {
	$from_city_id = intval($row_plane['from_city']);
	$to_city_id = intval($row_plane['to_city']);
	$sq_city = ($from_city_id > 0) ? mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$from_city_id'")) : array();
	$sq_city1 = ($to_city_id > 0) ? mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$to_city_id'")) : array();

	$city_name = isset($sq_city['city_name']) ? $sq_city['city_name'] : '';
	$city_name1 = isset($sq_city1['city_name']) ? $sq_city1['city_name'] : '';
	$from_location = isset($row_plane['from_location']) ? $row_plane['from_location'] : '';
	$to_location = isset($row_plane['to_location']) ? $row_plane['to_location'] : '';

	$from_sector = trim($city_name . ($from_location !== '' ? ' - ' . $from_location : ''));
	$to_sector = trim($city_name1 . ($to_location !== '' ? ' - ' . $to_location : ''));

	$airline_id = $row_plane['airline_name'];
	$airline_label = '';
	if ($airline_id !== '' && $airline_id !== null) {
		$sq_airline = mysqli_fetch_assoc(mysqlQuery("select airline_name, airline_code from airline_master where airline_id='$airline_id'"));
		if ($sq_airline) {
			$airline_label = $sq_airline['airline_name'] . (!empty($sq_airline['airline_code']) ? ' (' . $sq_airline['airline_code'] . ')' : '');
		}
	}

	$arr = array(
		'from_location' => $from_location,
		'to_location' => $to_location,
		'from_sector' => $from_sector,
		'to_sector' => $to_sector,
		'airline_name' => $airline_id,
		'airline_label' => $airline_label,
		'class' => $row_plane['class'],
		'from_city' => $from_city_id,
		'to_city' => $to_city_id,
		'city_name' => $city_name,
		'city_name1' => $city_name1
	);
	array_push($plane_info_arr, $arr);
}

header('Content-Type: application/json');
echo json_encode($plane_info_arr);
?>
