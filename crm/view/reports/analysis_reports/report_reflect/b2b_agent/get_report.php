<?php
include "../../../../../model/model.php";
include __DIR__ . '/../inc/analysis_report_helpers.php';
$fromdate = !empty($_POST['fromdate']) ? get_date_db($_POST['fromdate']) : null;
$todate = !empty($_POST['todate']) ? get_date_db($_POST['todate']) : null;
$branch_admin_id = $_SESSION['branch_admin_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$emp_id = $_SESSION['emp_id'];

$q = "select * from branch_assign where link='customer_master/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';

$array_s = array();
$query = "SELECT * FROM customer_master where active_flag='Active' and type='B2B'";
if($branch_status=='yes' && $role!='Admin'){
	$query .= " and branch_admin_id = '$branch_admin_id'";
}
$sq_enq1 = mysqlQuery($query);
$count = 0;
while ($row_cust = mysqli_fetch_assoc($sq_enq1)) {
    $sq_quot_count = analysis_report_b2b_quotation_count($row_cust['customer_id'], $fromdate, $todate);
    $sq_booking_count = analysis_report_b2b_booking_count($row_cust['customer_id'], $fromdate, $todate);

    $temparr = array("data" => array(
        (int)(++$count),
        $row_cust['company_name'],
        $row_cust['first_name'].' '.$row_cust['middle_name'].' '.$row_cust['last_name'],
        $sq_quot_count,
        $sq_booking_count,
        '<button class="btn btn-info btn-sm" id="view_data_btn-'. $row_cust['customer_id'] .'" onclick="view_data_modal('.$row_cust['customer_id'] .')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye" aria-hidden="true"></i></button>',
    ), "bg" => '');
    array_push($array_s, $temparr);
}
echo json_encode($array_s);
?>
