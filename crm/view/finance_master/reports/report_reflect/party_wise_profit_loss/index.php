<?php
include "../../../../../model/model.php";
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$q = "select * from branch_assign where link='finance_master/reports/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
?>

<input type="hidden" name="branch_status" value="<?= $branch_status ?>" id="branch_status">
<input type="hidden" name="role" value="<?= $role ?>" id="role">
<input type="hidden" name="branch_admin_id" value="<?= $branch_admin_id ?>" id="branch_admin_id">

<div class="row text-right mg_bt_10">
	<div class="col-md-12">
		<button class="btn btn-excel btn-sm pull-right" onclick="excel_report()" data-toggle="tooltip" title="Generate Excel"><i class="fa fa-file-excel-o"></i></button>
	</div>
</div>

<div class="app_panel_content Filter-panel">
	<div class="row">
		<div class="col-md-3 col-sm-6 mg_bt_10_xs">
			<select name="customer_id_filter" id="customer_id_filter" title="Select Customer" style="width:100%" class="form-control" required>
				<option value="">Select Customer*</option>
				<?php
				$query = "select * from customer_master where 1 ";
				if($branch_status=='yes' && $role!='Admin'){
					$query .= " and branch_admin_id = '$branch_admin_id'";
				}
				$query .=" and active_flag!='Inactive'";
				$sq_customer = mysqlQuery($query); 
				while($row_cust = mysqli_fetch_assoc($sq_customer)){
					if($row_cust['type'] == 'Corporate'||$row_cust['type']=='B2B'){
						$cust_name = $row_cust['company_name'];
					}else{
						$cust_name = $row_cust['first_name'].' '.$row_cust['last_name'];
					} ?>
					<option value="<?php echo $row_cust['customer_id']; ?>"><?php echo $cust_name; ?></option>
				<?php 
				} ?>
			</select>
		</div>
		<div class="col-md-3 col-sm-6 mg_bt_10_xs">
			<input type="text" id="from_date_filter" name="from_date_filter" placeholder="From Date" title="From Date" class="form-control" onchange="get_to_date(this.id,'to_date_filter');">
		</div>
		<div class="col-md-3 col-sm-6 mg_bt_10_xs">
			<input type="text" id="to_date_filter" name="to_date_filter" placeholder="To Date" title="To Date" class="form-control" onchange="validate_validDate('from_date_filter','to_date_filter');">
		</div>
		<div class="col-md-3 col-sm-6">
			<button class="btn btn-sm btn-info ico_right" onclick="report_reflect()">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
		</div>
	</div>
</div>

<hr>

<div id="div_report" class="main_block loader_parent">
	<div class="row mg_tp_20">
		<div class="col-md-12 no-pad">
			<div class="table-responsive">
				<table id="party_wise_profit_loss" class="table table-hover" style="margin: 20px 0 !important;">
				</table>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$('#customer_id_filter').select2();
$('#from_date_filter, #to_date_filter').datetimepicker({ timepicker:false, format:'d-m-Y' });

var column = [
	{ title: "Sr. No." },
	{ title: "Service Name" },
	{ title: "Booking ID" },
	{ title: "Total Sale (Without Tax)" , className: "info text-right"},
	{ title: "Total Purchase (Without Tax)" , className: "warning text-right"},
	{ title: "Profit/Loss" , className: "text-right"}
];

function report_reflect()
{
	var customer_id = $('#customer_id_filter').val();
	var from_date = $('#from_date_filter').val();
	var to_date = $('#to_date_filter').val();
	var branch_status = $('#branch_status').val();
	var branch_admin_id = $('#branch_admin_id').val();
	var role = $('#role').val();
	
	if(customer_id == ''){
		error_msg_alert('Please select Customer!');
		return false;
	}
	
	$('#div_report').append('<div class="loader"></div>');
	
	$.post('report_reflect/party_wise_profit_loss/report_reflect.php',{
		customer_id : customer_id,
		from_date : from_date,
		to_date : to_date,
		branch_status : branch_status,
		branch_admin_id : branch_admin_id,
		role : role
	}, function(data){
		pagination_load(data, column, true, true, 20, 'party_wise_profit_loss');
		$('.loader').remove();
	});
}

function excel_report()
{
	var customer_id = $('#customer_id_filter').val();
	var from_date = $('#from_date_filter').val();
	var to_date = $('#to_date_filter').val();
	var branch_status = $('#branch_status').val();
	var branch_admin_id = $('#branch_admin_id').val();
	var role = $('#role').val();
	
	if(customer_id == ''){
		error_msg_alert('Please select Customer!');
		return false;
	}
	
	window.location = 'report_reflect/party_wise_profit_loss/excel_report.php?customer_id='+customer_id+'&from_date='+from_date+'&to_date='+to_date+'&branch_status='+branch_status+'&branch_admin_id='+branch_admin_id+'&role='+role;
}
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>

