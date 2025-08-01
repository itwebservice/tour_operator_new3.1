<?php
include "../../../model/model.php";
global $show_entries_switch;
$branch_admin_id = $_SESSION['branch_admin_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$login_id = $_SESSION['login_id'];
$emp_id = $_SESSION['emp_id'];
$array_s = array();
$temp_arr = array();
$financial_year_id = $_POST['financial_year_id'];
$enquiry_type = $_POST['enquiry_type'];
$enquiry = $_POST['enquiry'];
$enquiry_status_filter = $_POST['enquiry_status'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$emp_id_filter = isset($_POST['emp_id_filter']) ? $_POST['emp_id_filter'] : '';;
$branch_status = $_POST['branch_status'];
$reference_id_filter=$_POST['reference_id_filter'];
$destination_filter = $_POST['destination_filter'];

$landline_no_filter = $_POST['landline_no_filter'];

$cust_name_filter = $_POST['cust_name_filter'];

$followup_status_type_filter = $_POST['followup_status_type_filter'];

$financial_year_id_filter  = $_POST['financial_year_id_filter'];

$draw = !empty($_POST['draw'])?intval($_POST['draw']):1;
$start = !empty($_POST['start'])?intval($_POST['start']):0;
$length = !empty($_POST['length'])?intval($_POST['length']):10;

//////////Calculate no.of .enquiries Start///////////////////
$enq_count = "SELECT * FROM `enquiry_master` left join enquiry_master_entries as ef on enquiry_master.entry_id = ef.entry_id where enquiry_master.status!='Disabled'";

// if($financial_year_id!=""){
// 	$enq_count .=" and financial_year_id='$financial_year_id'";
// }

if($financial_year_id_filter!=""){
	$enq_count .=" and enquiry_master.financial_year_id='$financial_year_id_filter'";
}


if($emp_id_filter!=""){
	$enq_count .=" and assigned_emp_id='$emp_id_filter'";
}
elseif($branch_status=='yes' && $role=='Branch Admin'){
	$enq_count .= " and branch_admin_id='$branch_admin_id'";
}
if($enquiry!="" && $enquiry!=='undefined'){
	$enq_count .=" and enquiry='$enquiry' ";
}
if($enquiry_type!=""){
	$enq_count .=" and enquiry_type='$enquiry_type' ";
}
if($reference_id_filter!=""){
	$enq_count .=" and reference_id='$reference_id_filter' ";
}
if($from_date!='' && $from_date!='undefined' && $to_date!="" && $to_date!='undefined'){
	$from_date = get_date_db($from_date);
	$to_date = get_date_db($to_date);
	$enq_count .=" and (enquiry_date between '$from_date' and '$to_date')";
}


if($branch_status=='yes' && $role!='Admin'){
	$enq_count .= " and branch_admin_id = '$branch_admin_id'";
}
if($role!='Admin' && $role!='Branch Admin' && $role_id!='7' && $role_id<'7'){
	if($role !='Admin' && $role!='Branch Admin')
	{
		if($show_entries_switch == 'No'){
			$enq_count .= " and assigned_emp_id='$emp_id' and enquiry_master.status!='Disabled' ";  
		}
		else{
			if($role == 'Backoffice'){
				$enq_count .=" and assigned_emp_id in(select emp_id from emp_master where branch_id='$branch_admin_id')";
			}else{
				$enq_count .= " and assigned_emp_id='$emp_id' and enquiry_master.status!='Disabled' ";
			}
		}
		if($enquiry_type!=""){
			$enq_count .=" and enquiry_type='$enquiry_type' ";
		}
		if($reference_id_filter!=""){
			$enq_count .=" and reference_id='$reference_id_filter' ";
		}
		if($from_date!='' && $from_date!='undefined' && $to_date!="" && $to_date!='undefined'){
			$from_date = get_date_db($from_date);
			$to_date = get_date_db($to_date);
			$enq_count .=" and (enquiry_date between '$from_date' and '$to_date')";
		}
		if($enquiry!=""){
			$enq_count .=" and enquiry='$enquiry' ";
		}
	}   
}
if($enquiry_status_filter!='')
{
	$enq_count .= " and ef.followup_status='$enquiry_status_filter'";
}

if($cust_name_filter !=''){

$enq_count .= " and enquiry_master.name ='$cust_name_filter'";
}

if($landline_no_filter !=""){
	$enq_count .="and enquiry_master.landline_no ='$landline_no_filter'";
}


if($followup_status_type_filter !=""){
$enq_count .= "and ef.followup_type ='$followup_status_type_filter'";

}

$enq_count .= " ORDER BY enquiry_master.enquiry_id DESC ";
$enquiry_count = mysqli_num_rows(mysqlQuery($enq_count));
//////////Calculate no.of .enquiries End///////////////////

///////////////////Enquiry table data start///////////////
$query = "SELECT * FROM `enquiry_master` left join enquiry_master_entries as ef on enquiry_master.entry_id=ef.entry_id where enquiry_master.status!='Disabled'";

// if($financial_year_id!=""){
// 	$query .=" and financial_year_id='$financial_year_id'";
// }

if($financial_year_id_filter!=""){
	$query  .=" and enquiry_master.financial_year_id='$financial_year_id_filter'";
}

if($emp_id_filter!=""){
	$query .=" and assigned_emp_id='$emp_id_filter'";
}
if($branch_status=='yes' && $role=='Branch Admin'){
	$query .=" and branch_admin_id = '$branch_admin_id'";
}	
if($enquiry!="" && $enquiry!=='undefined'){
    $query .=" and enquiry='$enquiry' ";
}		
if($enquiry_type!=""){
	$query .=" and enquiry_type='$enquiry_type' ";
}
if($reference_id_filter!=""){
	$query .=" and reference_id='$reference_id_filter' ";
}
if($from_date!='' && $to_date!=""){
	$from_date = get_date_db($from_date);
	$to_date = get_date_db($to_date);
	$query .=" and (enquiry_date between '$from_date' and '$to_date')";
}

if ($destination_filter != '' && strtolower($destination_filter) != strtolower($tour_name)) {
	$query .= " and tour_name ='$destination_filter'";
}


if($branch_status=='yes' && $role!='Admin'){
	$query .= " and branch_admin_id = '$branch_admin_id'";
}
if($role!='Admin' && $role!='Branch Admin' && $role_id!='7' && $role_id<'7'){
	
	if($show_entries_switch == 'No'){
		$query .= " and assigned_emp_id='$emp_id'";
	}
	else{
		if($role == 'Backoffice'){
			
			$query .=" and assigned_emp_id in(select emp_id from emp_master where branch_id='$branch_admin_id')";
		}else{
			$query .= " and assigned_emp_id='$emp_id'";
		}
	}
	if($enquiry_type!=""){
		$query .=" and enquiry_type='$enquiry_type' ";
	}
	if($reference_id_filter!=""){
		$query .=" and reference_id='$reference_id_filter' ";
	}
	if($from_date!='' && $from_date!='undefined' && $to_date!="" && $to_date!='undefined'){
		$from_date = get_date_db($from_date);
		$to_date = get_date_db($to_date);
		$query .=" and (enquiry_date between '$from_date' and '$to_date')";
	}
	if($enquiry!=""){
		$query .=" and enquiry='$enquiry' ";
	}
}
if($enquiry_status_filter!=''){
	$query .= " and ef.followup_status='$enquiry_status_filter'";
}


if($cust_name_filter !=''){

	$query  .= " and enquiry_master.name ='$cust_name_filter'";
	}



	if($landline_no_filter !=""){
		$query .="and enquiry_master.landline_no ='$landline_no_filter'";
	}

	if($followup_status_type_filter !=""){
		$query.= "and ef.followup_type ='$followup_status_type_filter'";
		
		}
// echo $query;
$query .= " ORDER BY enquiry_master.enquiry_id DESC";





// 1. Total filtered records count (before LIMIT, after all filters applied)
$recordsFiltered = mysqli_num_rows(mysqlQuery($query));

// 2. Apply LIMIT and OFFSET for pagination
$query .= " LIMIT $length OFFSET $start";

// 3. Execute paginated query
$sq_enquiries = mysqlQuery($query);

// 4. Prepare response data
$data = array();
$count = $start; 
//////////Enquiry table data End//////////


$count = $start + 1;



$sq_enquiries=mysqlQuery($query);

$recordsTotal = mysqli_num_rows($sq_enquiries);
// 5. Total records before filtering (for recordsTotal)
// $recordsTotal = mysqli_num_rows(mysqlQuery("SELECT * FROM enquiry_master WHERE status!='Disabled'"));

while($row = mysqli_fetch_assoc($sq_enquiries)){
	
	$cust_user_name = '';
	if($row['user_id'] != 0){ 
		$row_user = mysqli_fetch_assoc(mysqlQuery("Select name from customer_users where user_id ='$row[user_id]'"));
		$cust_user_name = ' ('.$row_user['name'].')';
	}

	$actions_string = "";
	$enquiry_id = $row['enquiry_id'];
	$assigned_emp_id = $row['assigned_emp_id'];
	$sq_emp = mysqli_fetch_assoc(mysqlQuery("select first_name,last_name from emp_master where emp_id='$assigned_emp_id'"));
	$allocated_to = ($assigned_emp_id != 0)?$sq_emp['first_name'].' '.$sq_emp['last_name'] : 'Admin';

	$enquiry_content = $row['enquiry_content'];
	$enquiry_content_arr1 = json_decode($enquiry_content, true);
	// pkg and group tour interested tour name

$tour_name = "";
foreach ($enquiry_content_arr1 as $item) {
    if ($item['name'] == 'tour_name') {
        $tour_name = trim($item['value']);
        break;
    }
}
// destination filter


// if ($destination_filter != '' && strtolower($destination_filter) != strtolower($tour_name)) {
//     continue; // Skip this entry
// }


	$enquiry_status1 = mysqli_fetch_assoc(mysqlQuery("select followup_date,followup_reply,followup_status from enquiry_master_entries where enquiry_id='$row[enquiry_id]' order by entry_id DESC"));
	$followup_date1 = $enquiry_status1['followup_date'];
	$followup_status=$enquiry_status1['followup_status'];

	if($followup_status == 'Converted'){
		$bg = 'success';
	}
	elseif($followup_status == 'Dropped'){
		$bg = 'danger';
	}
	else{
		$bg = '';
	}
	$date = $row['enquiry_date'];
	$yr = explode("-", $date);
	$year =$yr[0];



		

$temp_arr = array();
$temp_arr["s_no"] = $count++;
$temp_arr["enquiry_no"] = get_enquiry_id($row['enquiry_id'],$year);
$temp_arr["customer"] = $row['name'].$cust_user_name;
$temp_arr["mobile_no"] = $row['mobile_no'];
$temp_arr["tour_type"] = $row['enquiry_type'];
$temp_arr["destination"] = $tour_name;
$temp_arr["enquiry_date"] = get_date_user($row['enquiry_date']);
$temp_arr["followup_datetime"] = get_datetime_user($followup_date1);
$temp_arr["follow_up_type"] = $row['followup_type'];
$temp_arr["allocate_to"] = $allocated_to;



	if($followup_status != 'Dropped'){
		if($row['enquiry_type'] == "Package Booking" || $row['enquiry_type'] == "Group Booking" || $row['enquiry_type'] == "Hotel" || $row['enquiry_type'] == "Car Rental" || $row['enquiry_type'] == "Flight Ticket"){
			
			if($row['enquiry_type'] == "Hotel"){
				$link = "hotel_quotation/save";
			}
			else if($row['enquiry_type'] == "Car Rental" || $row['enquiry_type'] == "Flight Ticket"){

				$link1 = ($row['enquiry_type'] == "Car Rental") ? "car_rental" : "flight";
				$link = "package_booking/quotation/car_flight/".$link1;
			}else{
				$link1 = ($row['enquiry_type'] == "Package Booking") ? "home/save" : "group_tour";
				$link = "package_booking/quotation/".$link1;
			}
			$form_add = '<form style="display:inline-block" action="'. BASE_URL.'view/'.$link.'/index.php" target="_blank" id="frm_booking_1" method="GET">
				<input type="hidden" id="enquiry_id" name="enquiry_id" value="'.$row['enquiry_id'].'">
				<button style="display:inline-block" data-toggle="tooltip" class="btn btn-info btn-sm" title="Create Quick Quotation"><i class="fa fa-plus"></i></button>
			</form>';
			$actions_string .= $form_add;
		}
	}
	


// Action buttons
// $actions_string = '';
$actions_string .= '<button style="display:inline-block" data-toggle="tooltip" class="btn btn-info btn-sm" onclick="followup_modal('.$row['enquiry_id'].');btnDisableEnable(this.id)" id="followup_modal_add-'.$row['enquiry_id'].'" title="Add New Followup Details"><i class="fa fa-reply-all"></i></button>';

$actions_string .= '<button data-toggle="tooltip" style="display:inline-block" class="btn btn-info btn-sm" onclick="update_modal('.$row['enquiry_id'].');btnDisableEnable(this.id)" id="enq_modal_update-'.$row['enquiry_id'].'" title="Update Details"><i class="fa fa-pencil-square-o"></i></button>';

$actions_string .= '<button data-toggle="tooltip" style="display:inline-block" class="btn btn-info btn-sm" onclick="view_modal('.$row['enquiry_id'] .');btnDisableEnable(this.id)" id="enq_modal_view-'.$row['enquiry_id'].'" title="View Details"><i class="fa fa-eye"></i></button>';

if($role=="Admin" || $role=='Branch Admin'){
    $actions_string .= '<button data-toggle="tooltip" style="display:inline-block" class="btn btn-danger btn-sm" onclick="enquiry_status_disable('.$row['enquiry_id'] .')" title="Delete Enquiry"><i class="fa fa-trash"></i></button>';
}

$temp_arr["actions"] = $actions_string;
$temp_arr['bg'] = $bg;

$data[] = $temp_arr;

}




$response = array(
	  "draw" => intval($draw),
	"recordsTotal" => $recordsTotal,
	"recordsFiltered" => $recordsFiltered,
	"data" => $data
);

// echo json_encode($response);

$array_s = mb_convert_encoding($response, 'UTF-8', 'UTF-8');


echo json_encode($array_s);
// echo(json_last_error_msg());
// die;

?>