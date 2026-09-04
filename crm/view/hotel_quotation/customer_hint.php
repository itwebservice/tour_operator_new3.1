<?php
include "../../model/model.php";
header('Content-Type: application/json; charset=utf-8');

$branch_status = isset($_REQUEST['branch_status']) ? $_REQUEST['branch_status'] : 'no';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$branch_admin_id = isset($_SESSION['branch_admin_id']) ? $_SESSION['branch_admin_id'] : '';
$final_array = array();

if ($branch_status == 'yes' && $role != 'Admin') {
	$sq_query = mysqlQuery("select * from customer_master where active_flag!='Inactive' and branch_admin_id='$branch_admin_id' order by customer_id desc");
} else {
	$sq_query = mysqlQuery("select * from customer_master where active_flag!='Inactive' order by customer_id desc");
}
while ($row_cust = mysqli_fetch_assoc($sq_query)) {
	$final_array[] = build_customer_hint_row($row_cust, 'name');
}
echo json_encode($final_array);
?>
