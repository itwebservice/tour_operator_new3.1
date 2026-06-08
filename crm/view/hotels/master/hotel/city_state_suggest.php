<?php
include "../../../../model/model.php";

header('Content-Type: application/json');

$city_id = isset($_REQUEST['city_id']) ? trim($_REQUEST['city_id']) : '';

if ($city_id === '' || !preg_match('/^[0-9]+$/', $city_id)) {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid city.'));
    exit;
}

$state_id = '';

$sq_hotel_state = mysqlQuery(
    "select state_id from hotel_master where city_id='$city_id' and state_id!='' and state_id!='0' group by state_id order by count(*) desc limit 1"
);
if ($row = mysqli_fetch_assoc($sq_hotel_state)) {
    $state_id = $row['state_id'];
}

if ($state_id === '') {
    $sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$city_id' limit 1"));
    if ($sq_city && !empty($sq_city['city_name'])) {
        $city_name = $sq_city['city_name'];
        $city_token = preg_split('/[\s,]+/', trim($city_name));
        $city_token = isset($city_token[0]) ? $city_token[0] : $city_name;

        $sq_sac = mysqlQuery(
            "select city_state from state_and_cities where city_name like '" . mysqlREString($city_token) . "%' limit 1"
        );
        if ($row_sac = mysqli_fetch_assoc($sq_sac)) {
            $state_name = $row_sac['city_state'];
            $sq_state = mysqli_fetch_assoc(
                mysqlQuery("select id from state_master where state_name='" . mysqlREString($state_name) . "' and active_flag='Active' limit 1")
            );
            if ($sq_state) {
                $state_id = $sq_state['id'];
            }
        }
    }
}

echo json_encode(array(
    'status' => 'success',
    'state_id' => $state_id
));
?>
