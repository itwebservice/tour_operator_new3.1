<?php
include "../../../model/model.php";
$selectedDate = !empty($_POST['date']) ? get_date_db($_POST['date']) : null;
?>
<input type="hidden" id="selectedDate" value="<?= $selectedDate ?>"/>
<div id="div_list" class="main_block mg_tp_20">
	<div class="dashboard_table dashboard_table_panel main_block">
	<div class="row text-left mg_tp_10">
		<div class="col-md-12">
			<div class="col-md-12 no-pad table_verflow"> 
				<div class="row mg_tp_20"> <div class="col-md-12"> <div class="table-responsive">
					<table class="table table-hover" style="margin: 20px 0 !important;width: 100%;" id="itinerary_report">    
					</table>
				</div></div></div>
			</div>
	</div></div></div>
	<div id="other_des_wise_display">
</div>

<script>
function report_reflect(){
	
	var fromdate = $('#selectedDate').val();
	var column = [
	{ title : "S_No"},
	{ title : "Booking Id"},
	{ title : "Customer Name"},
	{ title : "Special Attraction"},
	{ title : "Day Wise Program"},
	{ title : "Overnight Stay"},
	{ title : "Meal Plan"},
	{ title : "Actions"}
];
	$.post('itinerary/list_reflect.php', { date : fromdate}, function(data){
		pagination_load(data, column, true, true, 20, 'itinerary_report');
	});
}
report_reflect();

function send_itinerary_whatsapp(contact_no, cust_name, attraction, day_wise_program, stay, meal_plan) {
    var app_name = $('#app_name').val();
    var app_contact_no = $('#app_contact_no').val();
    
    var msg = "Hello Sir/Ma'am\n\nGreetings from " + app_name + ",\n\nTodays program:\n\n";
    msg += "*Special Attraction*: " + attraction + "\n";
    msg += "*Day Wise Program*: " + day_wise_program + "\n";
    msg += "*Overnight Stay*: " + stay + "\n";
    msg += "*Meal Plan*: " + meal_plan + "\n\n";
    msg += "May this trip turn out to be a wonderful treat for you and may you create beautiful memories throughout this trip to cherish forever. Wish you a very happy and safe journey!!";
    msg += "\nThank you.\n";
	msg += app_name +'('+app_contact_no+ ')';
    
    window.open('https://web.whatsapp.com/send?phone=' + contact_no + '&text=' + encodeURIComponent(msg));
}
</script>