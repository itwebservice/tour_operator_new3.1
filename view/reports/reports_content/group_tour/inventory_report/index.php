<?php
include "../../../../../model/model.php";
$q = "select * from branch_assign where link='booking/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
?>
<input type="hidden" id="branch_status" name="branch_status" value="<?= $branch_status ?>" >
<div class="row mg_bt_10">
	<div class="col-md-12 text-right">
		<button class="btn btn-excel btn-sm" onclick="excel_report_new()" data-toggle="tooltip" title="Generate Excel"><i class="fa fa-file-excel-o"></i></button>
	</div>
</div>
<div class="app_panel_content Filter-panel mg_bt_10">
	<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
        <select id="tour_id_filter" name="tour_id_filter" onchange="tour_group_dynamic_reflect(this.id,'group_id_filter');" style="width:100%" title="Tour Name" class="form-control"> 
            <option value="">All Tours</option>
            <?php
            $sq=mysqlQuery("select tour_id,tour_name from tour_master where active_flag='Active' order by tour_name");
            while($row=mysqli_fetch_assoc($sq))
            {
                echo "<option value='$row[tour_id]'>".$row['tour_name']."</option>";
            }    
            ?>
        </select>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
        <select class="form-control" id="group_id_filter" name="group_id_filter"  title="Tour Date"> 
            <option value="">All Tour Dates</option>        
        </select>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
        <select class="form-control" id="status_filter" name="status_filter" title="Status"> 
            <option value="">All Status</option>        
            <option value="Active">Active</option>        
            <option value="Inactive">Inactive</option>        
        </select>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12 form-group">
        <button class="btn btn-sm btn-info ico_right" onclick="inventory_reflect()">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
    </div>
</div>
<div id="div_list" class="main_block mg_tp_20">
<div class="row"> <div class="col-md-12 no-pad"> <div class="table-responsive">
<table id="inventory_report" class="table table-hover" style="margin: 20px 0 !important;">         
</table>
</div></div></div>
</div>
<script>
    $('#tour_id_filter').select2();
    var column = [
	{ title:"Sr_No"},
    { title:"Destination"},
    { title:"From_Date"},
    { title:"To_Date"},
    { title:"Total_Capacity", className:"text-right"},
    { title:"Booked_Seats", className:"text-right"},
	{ title:"Available_Seats", className:"text-right"}
];
	function inventory_reflect(){
		var group_id = $('#group_id_filter').val();
		var tour_id = $('#tour_id_filter').val();
		var status = $('#status_filter').val();
		var branch_status = $('#branch_status').val();
		$.post('reports_content/group_tour/inventory_report/inventory_report.php', {group_id : group_id,tour_id : tour_id,status:status,branch_status:branch_status}, function(data){
            pagination_load(data, column, true, false, 20, 'inventory_report',true);
	});
	}
	inventory_reflect();

    function excel_report_new() {
		var tourName = $('#tour_id_filter').val();
		var tourDate = $('#group_id_filter').val();
		var status = $('#status_filter').val();
		var branch_status = $('#branch_status').val();
		
		var base_url = $('#base_url').val();
		
		 window.location = base_url + 'view/reports/reports_content/group_tour/inventory_report/export_excel.php?tourName=' + tourName + '&tourDate=' + tourDate + '&status=' + status + '&branch_status=' + branch_status;
	}

</script>

