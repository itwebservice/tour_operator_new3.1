<?php
include "../../../model/model.php";
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$sq = mysqli_fetch_assoc(mysqlQuery("select branch_status from branch_assign where link='reports/reports_homepage.php'"));
$branch_status_r = $sq['branch_status'];
?>
<input type="hidden" id="branch_status_r" name="branch_status_r" value="<?= $branch_status_r ?>" >
<div class="row mg_bt_10">
	<div class="col-md-12 text-right">
		<button class="btn btn-excel btn-sm" onclick="excel_report()" data-toggle="tooltip" title="Generate Excel"><i class="fa fa-file-excel-o"></i></button>
	</div>
</div>
<div class="app_panel_content Filter-panel">
<div class="row">
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<select name="cust_type_filter" id="cust_type_filter" style="width: 100%" onchange="dynamic_customer_load(this.value,'company_filter'); company_name_reflect();" title="Customer Type">
				<?php get_customer_type_dropdown(); ?>
			</select>
	    </div>
	    <div id="company_div" class="hidden mg_bt_10">
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10" id="customer_div">
	    </div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<select name="travel_type_filter" id="travel_type_filter" title="Travel Type" style="width: 100%">
				<option value="">Travel Type</option>
				<option value="Package Tour">Package Tour</option>
				<option value="Group Tour">Group Tour</option>
				<option value="Hotel">Hotel</option>
				<option value="Flight">Flight</option>
				<option value="Visa">Visa</option>
				<option value="Car Rental">Car Rental</option>
				<option value="Activity">Activity</option>
				<option value="Bus">Bus</option>
				<option value="Train">Train</option>
				<option value="Miscellaneous">Miscellaneous</option>
			</select>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<input type="text" id="from_date" name="from_date" class="form-control" placeholder="From Date" title="From Date" onchange="get_to_date(this.id,'to_date');"  value="<?php echo date('d-m-Y'); ?>">
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<input type="text" id="to_date" name="to_date" class="form-control" placeholder="To Date" title="To Date" onchange="validate_validDate('from_date','to_date');"  value="<?php echo date('d-m-Y', strtotime('+1 day')); ?>">
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<select name="booker_id_filter" id="booker_id_filter" title="User Name" style="width: 100%">
		        <?php  get_user_dropdown($role, $branch_admin_id, $branch_status_r,$emp_id) ?>
		    </select>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<select name="branch_id_filter" id="branch_id_filter" title="Branch Name" style="width: 100%">
		        <?php get_branch_dropdown($role, $branch_admin_id, $branch_status_r)  ?>
		    </select>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12">
			<button class="btn btn-sm btn-info ico_right" onclick="list_reflect()">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
		</div>
	</div>
 </div>

<div id="div_list" class="main_block">
<div class="row mg_tp_20"> <div class="col-md-12 no-pad"> <div class="table-responsive">
<table id="consolidated_sales_report" class="table table-hover" style="margin: 20px 0 !important;">
</table>
</div></div></div>
</div>
<div id="div_consolidated_content_display"></div>

<script>
$('#customer_id_filter, #travel_type_filter, #cust_type_filter,#booker_id_filter,#branch_id_filter').select2();
$('#from_date,#to_date').datetimepicker({ timepicker:false, format:'d-m-Y' });
dynamic_customer_load('','');

var column = [
	{ title : "S_no"},
	{ title : "Booking_id"},
	{ title : "Customer_name"},
	{ title : "Mobile"},
	{ title : "Email_id"},
	{ title : "No_of_pax"},
	{ title : "Booking_date"},
	{ title : "View"},
	{ title : "Travel_type"},
	{ title : "Tour_name"},
	{ title : "Tour_date"},
	{ title : "Basic_amount"},
	{ title : "Service_charge"},
	{ title : "Tax"},
	{ title : "Tcs"},
	{ title : "Tds"},
	{ title : "Credit_card_charges"},
	{ title : "Sale"},
	{ title : "Cancel"},
	{ title : "Total"},
	{ title : "Paid"},
	{ title : "View"},
	{ title : "Outstanding_balance"},
	{ title : "Due_date"},
	{ title : "Purchase"},
	{ title : "Purchase_form"},
	{ title : "Branch"},
	{ title : "Booked_by"},
	{ title : "Incentive"}
];

function list_reflect()
{
	var customer_id = $('#customer_id_filter').val();
	var travel_type = $('#travel_type_filter').val();
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	var cust_type = $('#cust_type_filter').val();
	var company_name = $('#company_filter').val();
	var booker_id = $('#booker_id_filter').val();
	var branch_id = $('#branch_id_filter').val();
	var base_url = $('#base_url').val();
	var branch_status_r = $('#branch_status_r').val();
	$.post(base_url+'view/reports/filters/consolidated_sales_report/list_reflect.php', { customer_id : customer_id, travel_type : travel_type, from_date : from_date, to_date : to_date, cust_type : cust_type, company_name : company_name,booker_id:booker_id,branch_id : branch_id , branch_status : branch_status_r}, function(data){
		pagination_load(data, column, true, true, 20, 'consolidated_sales_report',true);
	});
}
list_reflect();

function excel_report()
{
	var customer_id = $('#customer_id_filter').val()
	var travel_type = $('#travel_type_filter').val()
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	var cust_type = $('#cust_type_filter').val();
	var company_name = $('#company_filter').val();
	var booker_id = $('#booker_id_filter').val();
	var branch_id = $('#branch_id_filter').val();
	var base_url = $('#base_url').val();
	var branch_status_r = $('#branch_status_r').val();
	window.location = base_url+'view/reports/filters/consolidated_sales_report/excel_report.php?customer_id='+customer_id+'&travel_type='+travel_type+'&from_date='+from_date+'&to_date='+to_date+'&cust_type='+cust_type+'&company_name='+company_name+'&booker_id='+booker_id+'&branch_id='+branch_id+'&branch_status='+branch_status_r;
}
//*******************Get Dynamic Customer Name Dropdown**********************//
function dynamic_customer_load(cust_type, company_name)
{
  var cust_type = $('#cust_type_filter').val();
  var company_name = $('#company_filter').val();
  var branch_status_r = $('#branch_status_r').val();
  var base_url = $('#base_url').val();
    $.get(base_url+"view/package_booking/booking/inc/get_customer_dropdown.php", { cust_type : cust_type , company_name : company_name, branch_status : branch_status_r}, function(data){
    $('#customer_div').html(data);
  });
}
function company_name_reflect()
{
	var cust_type = $('#cust_type_filter').val();
    var base_url = $('#base_url').val();
     var branch_status_r = $('#branch_status_r').val();
  	$.post(base_url+'view/package_booking/booking/company_name_load.php', { cust_type : cust_type, branch_status : branch_status_r }, function(data){
  		if(cust_type=='Corporate'||cust_type=='B2B'){
	  		$('#company_div').addClass('company_class');
	    }
	    else
	    {
	    	$('#company_div').removeClass('company_class');
	    }
	    $('#company_div').html(data);
    });
}

function customer_booking_dropdown_load()
{
	var customer_id = $('#customer_id_filter').val();
	var base_url = $('#base_url').val();
	$.post(base_url+'view/package_booking/booking/inc/customer_booking_dropdown_load.php', { customer_id : customer_id }, function(data){
        $('#booking_id_filter').html(data);
    });
}
function package_view_modal(booking_id)
{
	$('#packagev_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#packagev_btn-'+booking_id).button('loading');
	$.post(base_url+'view/package_booking/summary/view/index.php', { booking_id : booking_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#packagev_btn-'+booking_id).prop('disabled',false);
    	$('#packagev_btn-'+booking_id).button('reset');
	});
}
function package_payment_view_modal(booking_id)
{
	$('#paymentv_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#paymentv_btn-'+booking_id).button('loading');
	$.post(base_url+'view/package_booking/summary/view/payment_view_modal.php', { booking_id : booking_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#paymentv_btn-'+booking_id).prop('disabled',false);
    	$('#paymentv_btn-'+booking_id).button('reset');
	});
}
function package_supplier_view_modal(booking_id)
{
	$('#supplierv_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#supplierv_btn-'+booking_id).button('loading');
	$.post(base_url+'view/package_booking/summary/view/supplier_view_modal.php', { booking_id : booking_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#supplierv_btn-'+booking_id).prop('disabled',false);
    	$('#supplierv_btn-'+booking_id).button('reset');
	});
}
// ===== GROUP TOUR VIEW FUNCTIONS =====
function group_view_modal(id)
{
	$('#packagev_btn-'+id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#packagev_btn-'+id).button('loading');
	$.post(base_url+'view/booking/summary/view/index.php', { id : id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#packagev_btn-'+id).prop('disabled',false);
    	$('#packagev_btn-'+id).button('reset');
	});
}
function group_supplier_view_modal(id)
{
	$('#supplierv_btn-'+id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#supplierv_btn-'+id).button('loading');
	$.post(base_url+'view/booking/summary/view/supplier_view_modal.php', { id : id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#supplierv_btn-'+id).prop('disabled',false);
    	$('#supplierv_btn-'+id).button('reset');
	});
}
function group_payment_view_modal(id)
{
	$('#paymentv_btn-'+id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#paymentv_btn-'+id).button('loading');
	$.post(base_url+'view/booking/summary/view/payment_view_modal.php', { id : id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#paymentv_btn-'+id).prop('disabled',false);
    	$('#paymentv_btn-'+id).button('reset');
	});
}

// ===== HOTEL VIEW FUNCTIONS =====
function hotel_view_modal(booking_id)
{
	$('#packagev_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#packagev_btn-'+booking_id).button('loading');
	$.post(base_url+'view/hotels/booking/payment_status/view/index.php', { booking_id : booking_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#packagev_btn-'+booking_id).prop('disabled',false);
    	$('#packagev_btn-'+booking_id).button('reset');
	});
}
function hotel_supplier_view_modal(booking_id)
{
	$('#supplierv_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#supplierv_btn-'+booking_id).button('loading');
	$.post(base_url+'view/hotels/booking/payment_status/view/supplier_view_modal.php', { booking_id : booking_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#supplierv_btn-'+booking_id).prop('disabled',false);
    	$('#supplierv_btn-'+booking_id).button('reset');
	});
}
function hotel_payment_view_modal(booking_id)
{
	$('#paymentv_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#paymentv_btn-'+booking_id).button('loading');
	$.post(base_url+'view/hotels/booking/payment_status/view/payment_view_modal.php', { booking_id : booking_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#paymentv_btn-'+booking_id).prop('disabled',false);
    	$('#paymentv_btn-'+booking_id).button('reset');
	});
}

// ===== VISA VIEW FUNCTIONS =====
function visa_view_modal(visa_id)
{
	$('#packagev_btn-'+visa_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#packagev_btn-'+visa_id).button('loading');
	$.post(base_url+'view/visa_passport_ticket/visa/payment_status/view/index.php', { visa_id : visa_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#packagev_btn-'+visa_id).prop('disabled',false);
    	$('#packagev_btn-'+visa_id).button('reset');
	});
}
function visa_supplier_view_modal(visa_id)
{
	$('#supplierv_btn-'+visa_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#supplierv_btn-'+visa_id).button('loading');
	$.post(base_url+'view/visa_passport_ticket/visa/payment_status/view/supplier_view_modal.php', { visa_id : visa_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#supplierv_btn-'+visa_id).prop('disabled',false);
    	$('#supplierv_btn-'+visa_id).button('reset');
	});
}
function visa_payment_view_modal(visa_id)
{
	$('#paymentv_btn-'+visa_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#paymentv_btn-'+visa_id).button('loading');
	$.post(base_url+'view/visa_passport_ticket/visa/payment_status/view/payment_view_modal.php', { visa_id : visa_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#paymentv_btn-'+visa_id).prop('disabled',false);
    	$('#paymentv_btn-'+visa_id).button('reset');
	});
}

// ===== FLIGHT VIEW FUNCTIONS =====
function ticket_view_modal(ticket_id)
{
	$('#packagev_btn-'+ticket_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#packagev_btn-'+ticket_id).button('loading');
	$.post(base_url+'view/visa_passport_ticket/ticket/payment_status/view/index.php', { ticket_id : ticket_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#packagev_btn-'+ticket_id).prop('disabled',false);
    	$('#packagev_btn-'+ticket_id).button('reset');
	});
}
function ticket_supplier_view_modal(ticket_id)
{
	$('#supplierv_btn-'+ticket_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#supplierv_btn-'+ticket_id).button('loading');
	$.post(base_url+'view/visa_passport_ticket/ticket/payment_status/view/supplier_view_modal.php', { ticket_id : ticket_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#supplierv_btn-'+ticket_id).prop('disabled',false);
    	$('#supplierv_btn-'+ticket_id).button('reset');
	});
}
function ticket_payment_view_modal(ticket_id)
{
	$('#paymentv_btn-'+ticket_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#paymentv_btn-'+ticket_id).button('loading');
	$.post(base_url+'view/visa_passport_ticket/ticket/payment_status/view/payment_view_modal.php', { ticket_id : ticket_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#paymentv_btn-'+ticket_id).prop('disabled',false);
    	$('#paymentv_btn-'+ticket_id).button('reset');
	});
}

// ===== CAR RENTAL VIEW FUNCTIONS =====
function car_view_modal(booking_id)
{
	$('#packagev_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#packagev_btn-'+booking_id).button('loading');
	$.post(base_url+'view/car_rental/summary/view/index.php', { booking_id : booking_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#packagev_btn-'+booking_id).prop('disabled',false);
    	$('#packagev_btn-'+booking_id).button('reset');
	});
}
function car_supplier_view_modal(booking_id)
{
	$('#supplierv_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#supplierv_btn-'+booking_id).button('loading');
	$.post(base_url+'view/car_rental/summary/view/supplier_view_modal.php', { booking_id : booking_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#supplierv_btn-'+booking_id).prop('disabled',false);
    	$('#supplierv_btn-'+booking_id).button('reset');
	});
}
function car_payment_view_modal(booking_id)
{
	$('#paymentv_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#paymentv_btn-'+booking_id).button('loading');
	$.post(base_url+'view/car_rental/summary/view/payment_view_modal.php', { booking_id : booking_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#paymentv_btn-'+booking_id).prop('disabled',false);
    	$('#paymentv_btn-'+booking_id).button('reset');
	});
}

// ===== ACTIVITY VIEW FUNCTIONS =====
function exc_view_modal(exc_id)
{
	$('#packagev_btn-'+exc_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#packagev_btn-'+exc_id).button('loading');
	$.post(base_url+'view/excursion/payment_status/view/index.php', { exc_id : exc_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#packagev_btn-'+exc_id).prop('disabled',false);
    	$('#packagev_btn-'+exc_id).button('reset');
	});
}
function exc_supplier_view_modal(exc_id)
{
	$('#supplierv_btn-'+exc_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#supplierv_btn-'+exc_id).button('loading');
	$.post(base_url+'view/excursion/payment_status/view/supplier_view_modal.php', { exc_id : exc_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#supplierv_btn-'+exc_id).prop('disabled',false);
    	$('#supplierv_btn-'+exc_id).button('reset');
	});
}
function exc_payment_view_modal(exc_id)
{
	$('#paymentv_btn-'+exc_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#paymentv_btn-'+exc_id).button('loading');
	$.post(base_url+'view/excursion/payment_status/view/payment_view_modal.php', { exc_id : exc_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#paymentv_btn-'+exc_id).prop('disabled',false);
    	$('#paymentv_btn-'+exc_id).button('reset');
	});
}

// ===== BUS VIEW FUNCTIONS =====
function bus_view_modal(booking_id)
{
	$('#packagev_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#packagev_btn-'+booking_id).button('loading');
	$.post(base_url+'view/bus_booking/booking/payment_status/view/index.php', { booking_id : booking_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#packagev_btn-'+booking_id).prop('disabled',false);
    	$('#packagev_btn-'+booking_id).button('reset');
	});
}
function bus_supplier_view_modal(booking_id)
{
	$('#supplierv_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#supplierv_btn-'+booking_id).button('loading');
	$.post(base_url+'view/bus_booking/booking/payment_status/view/supplier_view_modal.php', { booking_id : booking_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#supplierv_btn-'+booking_id).prop('disabled',false);
    	$('#supplierv_btn-'+booking_id).button('reset');
	});
}
function bus_payment_view_modal(booking_id)
{
	$('#paymentv_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#paymentv_btn-'+booking_id).button('loading');
	$.post(base_url+'view/bus_booking/booking/payment_status/view/payment_view_modal.php', { booking_id : booking_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#paymentv_btn-'+booking_id).prop('disabled',false);
    	$('#paymentv_btn-'+booking_id).button('reset');
	});
}

// ===== TRAIN VIEW FUNCTIONS =====
function train_view_modal(ticket_id)
{
	$('#packagev_btn-'+ticket_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#packagev_btn-'+ticket_id).button('loading');
	$.post(base_url+'view/visa_passport_ticket/train_ticket/payment_status/view/index.php', { ticket_id : ticket_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#packagev_btn-'+ticket_id).prop('disabled',false);
    	$('#packagev_btn-'+ticket_id).button('reset');
	});
}
function train_supplier_view_modal(ticket_id)
{
	$('#supplierv_btn-'+ticket_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#supplierv_btn-'+ticket_id).button('loading');
	$.post(base_url+'view/visa_passport_ticket/train_ticket/payment_status/view/supplier_view_modal.php', { train_ticket_id : ticket_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#supplierv_btn-'+ticket_id).prop('disabled',false);
    	$('#supplierv_btn-'+ticket_id).button('reset');
	});
}
function train_payment_view_modal(ticket_id)
{
	$('#paymentv_btn-'+ticket_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#paymentv_btn-'+ticket_id).button('loading');
	$.post(base_url+'view/visa_passport_ticket/train_ticket/payment_status/view/payment_view_modal.php', { train_ticket_id : ticket_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#paymentv_btn-'+ticket_id).prop('disabled',false);
    	$('#paymentv_btn-'+ticket_id).button('reset');
	});
}

// ===== MISCELLANEOUS VIEW FUNCTIONS =====
function misc_view_modal(misc_id)
{
	$('#packagev_btn-'+misc_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#packagev_btn-'+misc_id).button('loading');
	$.post(base_url+'view/miscellaneous/payment_status/view/index.php', { misc_id : misc_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#packagev_btn-'+misc_id).prop('disabled',false);
    	$('#packagev_btn-'+misc_id).button('reset');
	});
}
function misc_supplier_view_modal(misc_id)
{
	$('#supplierv_btn-'+misc_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#supplierv_btn-'+misc_id).button('loading');
	$.post(base_url+'view/miscellaneous/payment_status/view/supplier_view_modal.php', { misc_id : misc_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#supplierv_btn-'+misc_id).prop('disabled',false);
    	$('#supplierv_btn-'+misc_id).button('reset');
	});
}
function misc_payment_view_modal(misc_id)
{
	$('#paymentv_btn-'+misc_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#paymentv_btn-'+misc_id).button('loading');
	$.post(base_url+'view/miscellaneous/payment_status/view/payment_view_modal.php', { misc_id : misc_id }, function(data){
		$('#div_consolidated_content_display').html(data);
		$('#paymentv_btn-'+misc_id).prop('disabled',false);
    	$('#paymentv_btn-'+misc_id).button('reset');
	});
}

$(function () {
    $("[data-toggle='tooltip']").tooltip({placement: 'bottom'});
});
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
