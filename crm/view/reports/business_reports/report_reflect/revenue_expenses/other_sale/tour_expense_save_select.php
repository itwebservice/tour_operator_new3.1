
<?php 
include "../../../../../../model/model.php"; 
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$emp_id = $_SESSION['emp_id'];
$financial_year_id = $_SESSION['financial_year_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];


?>

<div class="app_panel_content Filter-panel">
	<div class="row">
		<div class="col-md-3 col-sm-6">
			<select style="width:100%" id="sale_type" name="sale_type" class="form-control" title="Select Sale" onchange="tour_expense_save_reflect();fetch_booking_ids();get_widget();"> 
				<option value="Select Sale">Select Sale</option>
				<option value="Package Tour">Package Tour</option>
				<option value="Group Tour">Group Tour</option>
				<option value="Hotel">Hotel</option>    
		    	<option value="Flight Ticket">Flight</option> 
		    	<option value="Visa">Visa</option>     
		    	<option value="Car Rental">Car Rental</option>
		    	<option value="Excursion">Activity</option>
		    	<option value="Train Ticket">Train</option>
		    	<option value="Bus">Bus</option>
		    	<option value="Miscellaneous">Miscellaneous</option>		    	
		    </select>
		</div>
		<div class="col-md-9 col-sm-12 text-right">
			<button class="btn btn-excel btn-sm mg_bt_10_sm_xs" onclick="excel_report()" data-toggle="tooltip" title="Generate Excel"><i class="fa fa-file-excel-o"></i></button>
		</div>
	</div>

<!-- </div> -->

<div class="row" style="margin-top:10px;">
<!-- Tour Name Filter (Visible only for Group Tour) -->
<div class="col-md-3 col-sm-6 mg_bt_10_xs" id="tour_name_filter" style="display:none;">
	        <select class="form-control" style="width:100%" id="cmb_tour_name" name="cmb_tour_name" onchange="tour_group_reflect(this.id)" title="Tour Name"> 
	            <option value="">Tour Name</option>
	            <?php
				$sq = mysqlQuery("select tour_id, tour_name from tour_master where active_flag='Active' order by tour_name asc");
				while($row = mysqli_fetch_assoc($sq)){
					echo "<option value='$row[tour_id]'>".$row['tour_name']."</option>";
				}
	            ?>
	        </select>
	    </div>

	    <!-- Tour Date Filter (Visible only for Group Tour) -->
	    <div class="col-md-3 col-sm-6 mg_bt_10_xs" id="tour_date_filter" style="display:none;">
	        <select class="form-control" id="cmb_tour_group" name="cmb_tour_group" title="Tour Date"> 
	            <option value="">Tour Date</option>        
	        </select>
	    </div>

		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10" id="select_booking">
            <select id="booking_id_filter" name="booking_id_filter" style="width:100%" title="Booking ID">
                <option value="">*Select Booking</option>
               
        
            </select>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
            <input type="text" id="from_date_filter" name="from_date_filter"
                onchange="get_to_date(this.id,'to_date_filter');" placeholder="From Date" title="From Date" class="form-control" >
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
            <input type="text" id="to_date_filter" onchange="validate_validDate('from_date_filter','to_date_filter');"
                name="to_date_filter" placeholder="To Date" title="To Date" class="form-control">
        </div>
		<!-- Proceed and Excel Buttons -->
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10 ">
			<button class="btn btn-sm btn-info ico_right" id="group_tour_btn" onclick="tour_expense_save_reflect()">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
			
		</div>
	</div>
<!-- </div> -->


<!-- <div class="app_panel_content Filter-panel">
    <div class="row"> -->
      
        <!-- <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
            <select id="booking_id_filter" name="booking_id_filter" style="width:100%" title="Booking ID">
                <option value="">*Select Booking</option>
               
        
            </select>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
            <input type="text" id="from_date_filter" name="from_date_filter"
                onchange="get_to_date(this.id,'to_date_filter');" placeholder="From Date" title="From Date">
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
            <input type="text" id="to_date_filter" onchange="validate_validDate('from_date_filter','to_date_filter');"
                name="to_date_filter" placeholder="To Date" title="To Date">
        </div> -->
     
        <!-- <div class="col-md-3 col-sm-12 col-xs-12 mg_bt_10">
            <button class="btn btn-sm btn-info ico_right" id='tour_btn' onclick="tour_expense_save_reflect()">Proceed&nbsp;&nbsp;<i
                    class="fa fa-arrow-right"></i></button>
        </div> -->
    <!-- </div>
</div> -->
<div id="div_other_tour_reflect" class="main_block mg_tp_10"></div>
<div id="purchases_display"></div>
<div class="row mg_tp_20"> <div class="col-md-12 no-pad"> <div class="table-responsive">
<table id="other_report" class="table table-hover" style="width:100%;margin: 20px 0 !important;">         
</table>
</div></div></div>
<!-- </div> -->

<div id="other_expnse_display"></div>
<div id="other_package_expnse_display"></div>
<script>

$('#from_date_filter, #to_date_filter').datetimepicker({
    timepicker: false,
    format: 'd-m-Y'
});
$('#booking_id_filter,#customer_id_filter,#cust_type_filter').select2();
// dynamic_customer_load('', '');






// fetch booking id

function fetch_booking_ids() {
    var sale_type = $('#sale_type').val();
    var base_url = $('#base_url').val();

    if (!sale_type) {
        $('#booking_id_filter').html('<option value="">*Select Booking</option>');
        return;
    }

    $.post(base_url + 'view/reports/business_reports/report_reflect/revenue_expenses/other_sale/tour_booking_id.php', { sale_type: sale_type }, function(data) {
        $('#booking_id_filter').html(data);
    });
}



$('#sale_type').select2();
	function excel_report(){
		var sale_type = $('#sale_type').val();
		var base_url = $('#base_url').val();

		var booking_id = $('#booking_id_filter').val();
		var from_date = $('#from_date_filter').val();
    var to_date = $('#to_date_filter').val();
	var tour_id = $('#cmb_tour_name').val();
	var group_id=$('#cmb_tour_group').val();

		if(sale_type==""){
			error_msg_alert("Select Sale Type");
			return false;
		// 	booking_id: booking_id,
        // from_date: from_date,
        // to_date: to_date,tour_id:tour_id,group_id:group_id
		}
		window.location = base_url+'view/reports/business_reports/report_reflect/revenue_expenses/other_sale/excel_report.php?sale_type='+sale_type+'&booking_id=' + booking_id + '&from_date=' + from_date + '&to_date=' + to_date+'&tour_id='
		+tour_id+'&group_id='+group_id;
	}
	var column = [
	{ title : "S_No."},
	{ title : "Booking_ID"},
	{ title : "Booking_date"},
	{ title : "Customer_Name"},
	{ title : "Sale_amount"},
	// { title : "Supplier_type"},
	// { title : "Supplier_name"},
	{ title : "Purchases"},
	{ title : "Purchase_amount"},
	{title:"Other_Expenses"},
	{ title : "Profit/Loss(%)"},
	{ title : "User_Name"}
];






// 
function other_expnse_modal(tour_id,group_id,btn_d)
{
	$('#suppliere_btn-'+btn_d+tour_id+group_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#suppliere_btn-'+btn_d+tour_id+group_id).button('loading');
	$.post(base_url+'view/reports/business_reports/report_reflect/revenue_expenses/other_sale/other_expnse_modal.php', { tour_id : tour_id, group_id : group_id }, function(data){
		$('#other_expnse_display').html(data);
		$('#suppliere_btn-'+btn_d+tour_id+group_id).prop('disabled',false);
    	$('#suppliere_btn-'+btn_d+tour_id+group_id).button('reset');
		
	});
}
// package tour other expense

function package_other_expnse_modal(booking_id)
{
	$('#suppliere_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#suppliere_btn-'+booking_id).button('loading');
	$.post(base_url+'view/reports/business_reports/report_reflect/revenue_expenses/package_tour/other_expnse_modal.php', { booking_id : booking_id }, function(data){
		$('#other_package_expnse_display').html(data);
		$('#suppliere_btn-'+booking_id).prop('disabled',false);
    	$('#suppliere_btn-'+booking_id).button('reset');
	});
}

	function tour_expense_save_reflect(){
		var sale_type = $('#sale_type').val();
		var base_url = $('#base_url').val();

		var booking_id = $('#booking_id_filter').val();
    var from_date = $('#from_date_filter').val();
    var to_date = $('#to_date_filter').val();
	var tour_id = $('#cmb_tour_name').val();
	var group_id=$('#cmb_tour_group').val();

	// var branch_status = $('#branch_status').val();
		if(sale_type==""){
			error_msg_alert("Select Sale");
			return false;
		}

		$.post(base_url+'view/reports/business_reports/report_reflect/revenue_expenses/other_sale/get_sale_purchase_summary.php', { sale_type : sale_type , booking_id: booking_id,
        from_date: from_date,
        to_date: to_date,tour_id:tour_id,group_id:group_id}, function(data){
			pagination_load(data, column, true, false, 20, 'other_report');
		});
	}
	tour_expense_save_reflect('Visa');
	


	$('#sale_type').on('change', function () {
    var sale_type = $(this).val();
    if (sale_type === "Group Tour") {
        $('#tour_name_filter').show();
        $('#tour_date_filter').show();
		// $('#tour_btn').hide();
		$('#booking_id_filter').hide();
		$('#from_date_filter').hide();
		$('#to_date_filter').hide();
		// $('#group_tour_btn').show();
		$('#select_booking').hide();
    } else {
        $('#tour_name_filter').hide();
        $('#tour_date_filter').hide();
		// $('#group_tour_btn').hide();
		$('#select_booking').show();

		// $('#tour_btn').show();

		$('#booking_id_filter').show();
		$('#from_date_filter').show();
		$('#to_date_filter').show();
    }

	
});

function tour_group_reflect(tour_id) {
    var tour_id = $('#' + tour_id).val();
    var base_url = $('#base_url').val();

    $.post(base_url + 'view/reports/business_reports/report_reflect/revenue_expenses/other_sale/get_tour_groups.php', { tour_id: tour_id }, function (data) {
        $('#cmb_tour_group').html(data);
    });
}






	function get_widget(){
		var sale_type = $('#sale_type').val();
		var base_url = $('#base_url').val();

		if(sale_type==""){
			error_msg_alert("Select Sale");
			return false;
		}
		$.post(base_url+'view/reports/business_reports/report_reflect/revenue_expenses/other_sale/tour_expense_save_reflect.php', { sale_type : sale_type }, function(data){
			$('#div_other_tour_reflect').html(data);
		});
	}
	get_widget('Visa');
	
	function purchases_display_modal(estimate_type,estimate_type_id,tour_id){

		$('#supplierv_btn-'+estimate_type_id).prop('disabled',true);
		var base_url = $('#base_url').val();
		$('#supplierv_btn-'+estimate_type_id).button('loading');
		$.post(base_url+'view/reports/business_reports/report_reflect/revenue_expenses/other_sale/purchases_display.php', { estimate_type : estimate_type,estimate_type_id:estimate_type_id,tour_id:tour_id }, function(data){
			$('#purchases_display').html(data);
			$('#supplierv_btn-'+estimate_type_id).prop('disabled',false);
			$('#supplierv_btn-'+estimate_type_id).button('reset');
		});
	}





function purchases_display_modal_pkg(estimate_type,booking_id)
{
	$('#supplierv_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#supplierv_btn-'+booking_id).button('loading');
	var base_url = $('#base_url').val();
	$.post(base_url+'view/reports/business_reports/report_reflect/revenue_expenses/package_tour/view_purchase_modal.php', { estimate_type:estimate_type,booking_id: booking_id }, function(data){
		$('#other_package_expnse_display').html(data);
		$('#supplierv_btn-'+booking_id).prop('disabled',false);
    	$('#supplierv_btn-'+booking_id).button('reset');
	});
}
</script>

