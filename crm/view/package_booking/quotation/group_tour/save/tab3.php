<style>
    .quotation-datetime-td {
        min-width: 165px;
        max-width: 180px;
        vertical-align: middle !important;
    }
    .quotation-datetime-td .form-control {
        width: 100% !important;
        min-width: 0;
    }
    #tbl_package_tour_quotation_dynamic_plane .quotation-datetime-td,
    #tbl_package_tour_quotation_dynamic_train .quotation-datetime-td,
    #tbl_dynamic_cruise_quotation .quotation-datetime-td {
        white-space: nowrap;
    }
    #tbl_group_tour_quotation_transport .transport-date-td {
        width: 150px;
        min-width: 150px;
    }
    #tbl_group_tour_quotation_transport .transport-date-td .form-control {
        width: 100% !important;
        min-width: 0;
    }
    #tbl_group_tour_quotation_transport .transport-vehicle-td {
        width: 200px;
        min-width: 200px;
    }
    #tbl_group_tour_quotation_transport .transport-location-td {
        width: 250px;
        min-width: 250px;
    }
    #tbl_group_tour_quotation_transport .transport-duration-td {
        width: 170px;
        min-width: 170px;
    }
    #tbl_group_tour_quotation_transport .transport-count-td {
        width: 150px;
        min-width: 150px;
    }
    #tbl_group_tour_quotation_transport .transport-vehicle-td .form-control,
    #tbl_group_tour_quotation_transport .transport-vehicle-td .select2-container,
    #tbl_group_tour_quotation_transport .transport-location-td .form-control,
    #tbl_group_tour_quotation_transport .transport-location-td .select2-container,
    #tbl_group_tour_quotation_transport .transport-duration-td .form-control,
    #tbl_group_tour_quotation_transport .transport-duration-td .select2-container {
        width: 100% !important;
        min-width: 0;
    }
</style>
<form id="frm_tab3">
	<div class="row">
		<div class="col-md-12 app_accordion">
			<input type="hidden" value="" id="tour_group_id"/>
			<div class="panel-group main_block" id="accordion" role="tablist" aria-multiselectable="true">

				<!-- Train Information -->
				<div class="accordion_content main_block mg_bt_10">
					<div class="panel panel-default main_block">
						<div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
					        <div class="Normal main_block" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="true" aria-controls="collapse1" id="collapsed1">       
					        	<div class="col-md-12"><span>Train Information</span></div>
					        </div>
					    </div>
					    <div id="collapse1" class="panel-collapse collapse in main_block" role="tabpanel" aria-labelledby="heading1">
					        <div class="panel-body">
					            <div class="row mg_tp_10 mg_bt_10">
									<div class="col-xs-6 mg_bt_20_sm_xs">
										<button type="button" class="btn btn-excel btn-sm" title="Add City" onclick="city_ssave_modal()"><i class="fa fa-plus"></i></button>
									</div>
									<div class="col-xs-6 text-right mg_bt_20_sm_xs">
								        <button type="button" class="btn btn-excel btn-sm" onClick="addRow('tbl_package_tour_quotation_dynamic_train');city_lzloading('.train_from', '*From', true);city_lzloading('.train_to', '*To', true);" title="Add Row"><i class="fa fa-plus"></i></button>
										<button type="button" class="btn btn-pdf btn-sm" onClick="deleteRow('tbl_package_tour_quotation_dynamic_train')" title="Delete Row"><i class="fa fa-trash"></i></button>
								    </div>
								</div>								
					            <div class="row">
									<div class="col-xs-12">
									    <div class="table-responsive">
										    <table id="tbl_package_tour_quotation_dynamic_train" name="tbl_package_tour_quotation_dynamic_train" class="table table-bordered no-marg pd_bt_51">
											<input type="hidden" id="train_dept_date_hidde">
										        <tr>
									                <td><input class="css-checkbox" id="chk_tour_group1" type="checkbox" checked><label class="css-label" for="chk_tour_group1" checked> <label></td>
									                <td><input maxlength="15" value="1" type="text" name="username" placeholder="Sr. No." class="form-control" disabled /></td>
									                <td class="col-md-3 no-pad"><select id="train_from_location1" onchange="validate_location('train_to_location1','train_from_location1')" class="app_select2 form-control train_from" name="train_from_location1" title="From Location" style="width:100%;">
											            </select>
									                </td>
									                <td class="col-md-3 no-pad"><select id="train_to_location1"  onchange="validate_location('train_from_location1','train_to_location1')" class="app_select2 form-control train_to" title="To Location" name="train_to_location1" style="width:100%;">
										            </select></td>
										            <td class="col-md-2 no-pad"><select name="train_class" id="train_class1" title="Class" class="form-control" style="width:100%;">
										            	<option value="">Class</option>
										            	<option value="1A">1A</option>
													    <option value="2A">2A</option>
													    <option value="3A">3A</option>
													    <option value="FC">FC</option>
													    <option value="CC">CC</option>
													    <option value="SL">SL</option>
													    <option value="2S">2S</option>
										            </select></td>
										            <td class="col-md-2 no-pad quotation-datetime-td"><input type="text" id="train_departure_date" name="train_departure_date" placeholder="Departure Date and time" title="Departure Date and time" class="form-control app_datetimepicker" onchange="get_to_datetime(this.id,'train_arrival_date')" value="<?= date('d-m-Y H:i') ?>"></td>
										            <td class="col-md-2 no-pad quotation-datetime-td"><input type="text" id="train_arrival_date" name="train_arrival_date" placeholder="Arrival Date and time" title="Arrival Date and time" class="form-control app_datetimepicker" value="<?= date('d-m-Y H:i') ?>" onchange="validate_validDatetime('train_departure_date',this.id);"></td>
									            </tr>                   
									        </table>
										</div>
									</div>
								</div> 
						    </div>
						</div>
					</div>
				</div>
				
				<div class="accordion_content main_block mg_bt_10">
					<div class="panel panel-default main_block">
						<div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
							<div class="Normal collapsed main_block" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse2" aria-expanded="false" aria-controls="collapse2" id="collapsed2">                  
							<div class="col-md-12"><span>Hotel Information</span></div>
							</div>
						</div>
						<div id="collapse2" class="panel-collapse collapse main_block" role="tabpanel" aria-labelledby="heading2">
							<div class="panel-body">
								<div class="row">
								<div class="col-xs-12">
									<div class="table-responsive">
										<table id="tbl_package_hotel_master" name="tbl_package_hotel_master" class="table table-bordered no-marg pd_bt_51">
										
										<tr>
												<td><input class="css-checkbox" id="chk_dest1" type="checkbox" disabled checked><label class="css-label" for="chk_dest1" checked> <label></td>
												<td><input maxlength="15" value="1" type="text" name="no" placeholder="Sr. No." class="form-control" disabled />
												</td>
												<td><input id="city_name" name="city_name1" class="form-control" style="width:100%" title="City Name" readonly> 
												</td>
												<td><input id="hotel_name" name="hotel_name1" style="width:100%" title="Hotel Name" class="form-control" readonly>
												</td>
												<td><input type="text" id="hotel_type" name="hotel_type1" placeholder="*Hotel Category" class="form-control" title="Hotel Category" readonly></td>
												<td><input type="text" id="hotel_tota_days1"  name="hotel_tota_days1" placeholder="*Total Night" class="form-control" title="Total Night" readonly></td></td>
											</tr> 
										</table>
									</div>
								</div>
							</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Transport Information -->
				<div class="accordion_content main_block mg_bt_10">
					<div class="panel panel-default main_block">
						<div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
							<div class="Normal collapsed main_block" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse2_5" aria-expanded="false" aria-controls="collapse2_5" id="collapsed2_5">                  
							<div class="col-md-12"><span>Transport Information</span></div>
							</div>
						</div>
						<div id="collapse2_5" class="panel-collapse collapse main_block" role="tabpanel" aria-labelledby="heading2_5">
							<div class="panel-body">
								<div class="row mg_tp_10 mg_bt_10">
									<div class="col-xs-6 mg_bt_20_sm_xs">
										<button type="button" class="btn btn-excel btn-sm hidden" title="Add Vehicle" onclick="vehicle_save_modal('vehicle_name1')"><i class="fa fa-plus"></i></button>
									</div>
									<div class="col-xs-6 text-right mg_bt_20_sm_xs">
										<button type="button" class="btn btn-excel btn-sm" onClick="addTransportRowSave();" title="Add Row"><i class="fa fa-plus"></i></button>
										<button type="button" class="btn btn-pdf btn-sm" onClick="deleteRow('tbl_group_tour_quotation_transport')" title="Delete Row"><i class="fa fa-trash"></i></button>
									</div>
								</div>
								<div class="row">
								<div class="col-xs-12">
									<div class="table-responsive">
										<table id="tbl_group_tour_quotation_transport" name="tbl_group_tour_quotation_transport" class="table table-bordered no-marg pd_bt_51">
										
										<tr>
												<td style="vertical-align: middle; "><input class="css-checkbox" id="chk_transport1" type="checkbox" checked><label class="css-label" for="chk_transport1"> </label></td>
												<td><input maxlength="15" value="1" type="text" name="username" placeholder="Sr No." class="form-control" disabled="" autocomplete="off"></td>
												<td class="col-md-2 transport-vehicle-td"><select name="transport_vehicle_name1" id="transport_vehicle_name1" title="Select Vehicle" class="form-control app_select2" data-add-new-option="true">
														<option value="">Select Vehicle</option>
														<?php
														$sq_query = mysqlQuery("select * from b2b_transfer_master where status != 'Inactive'");
														while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>
															<option value="<?php echo $row_dest['entry_id']; ?>">
																<?php echo $row_dest['vehicle_name']; ?></option>
														<?php } ?>
													</select></td>
												<td class="transport-date-td"><input type="text" id="transport_start_date1" name="transport_start_date1" placeholder="Start Date" title="Start Date" class="app_datepicker form-control" value="<?= date('d-m-Y') ?>" onchange="get_to_date(this.id,'transport_end_date1');"></td>
												<td class="transport-date-td"><input type="text" id="transport_end_date1" name="transport_end_date1" placeholder="End Date" title="End Date" class="app_datepicker form-control" value="<?= date('d-m-Y') ?>" onchange="validate_validDate('transport_start_date1','transport_end_date1');"></td>
												<td class="col-md-2 transport-location-td"><select name="transport_pickup_from1" id="transport_pickup_from1" title="Pickup Location" class="form-control app_minselect2">
													</select></td>
												<td class="col-md-2 transport-location-td"><select name="transport_drop_to1" id="transport_drop_to1" title="Drop-off Location" class="form-control app_minselect2">
													</select></td>
												<td class="transport-duration-td"><select name="transport_service_duration1" id="transport_service_duration1" title="Service Duration" class="form-control app_select2">
														<option value="">Service Duration</option>
														<?php echo get_service_duration_dropdown(); ?>
													</select></td>
												<td class="transport-count-td"><input type="text" id="transport_no_vehicles1" name="transport_no_vehicles1" placeholder="No.Of vehicles" title="No.Of vehicles" class="form-control"></td>
											</tr> 
										</table>
									</div>
								</div>
							</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Flight Information -->
				<div class="accordion_content main_block mg_bt_10">
					<div class="panel panel-default main_block">
						<div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
				        	<div class="Normal collapsed main_block" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="false" aria-controls="collapse3" id="collapsed3">
				        	<div class="col-md-12"><span>Flight Information</span></div>
				        </div>
				    </div>
				    <div id="collapse3" class="panel-collapse collapse main_block" role="tabpanel" aria-labelledby="heading3">
				        <div class="panel-body">
				        	<div class="row mg_tp_10 mg_bt_10">
								<div class="col-xs-6 mg_bt_20_sm_xs">
									<button type="button" class="btn btn-excel btn-sm hidden" title="Add Airport/Airline" onclick="airport_airline_save_modal()"><i class="fa fa-plus"></i></button>
								</div>
								<div class="col-xs-6 text-right mg_bt_20_sm_xs">
									<button type="button" class="btn btn-excel btn-sm" onClick="addRow('tbl_package_tour_quotation_dynamic_plane');event_airport('tbl_package_tour_quotation_dynamic_plane')" title="Add Row"><i class="fa fa-plus"></i></button>
									<button type="button" class="btn btn-pdf btn-sm" onClick="deleteRow('tbl_package_tour_quotation_dynamic_plane')" title="Delete Row"><i class="fa fa-trash"></i></button>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="table-responsive">
										<table id="tbl_package_tour_quotation_dynamic_plane" name="tbl_package_tour_quotation_dynamic_plane" class="table table-bordered no-marg pd_bt_51">
										<input type="hidden" id="plane_dept_date_hidde">
											<tr>
												<td><input class="css-checkbox" id="chk_plan1" checked type="checkbox"><label class="css-label" for="chk_plan1"> <label></td>
												<td><input maxlength="15" value="1" type="text" name="username" placeholder="Sr. No." class="form-control" disabled /></td>
												<td style="width: 300px;" class="sector-select"><select name="from_sector-1" id="from_sector-1"
                                                                    class="form-control app_select2 plane-airport-select"
                                                                    data-sector-type="from" title="From Sector"
                                                                     data-add-new-option="true">
                                                                    <option value="">*From Sector</option>
                                                                </select>
                                                </td>
                                                <td style="width: 300px;" class="sector-select"><select name="to_sector-1" id="to_sector-1"
                                                          class="form-control app_select2 plane-airport-select"
                                                          data-sector-type="to" title="To Sector"
                                                           data-add-new-option="true">
                                                          <option value="">*To Sector</option>
                                                      </select>
                                                </td>
												<td><select id="airline_name1" class="app_select2 form-control"  title="Airline Name" name="airline_name1" style="width: 120px;" data-add-new-option="true">
														<option value="">Airline Name</option>
														<?php get_airline_name_dropdown(); ?>
												</select></td>
												<td><select name="plane_class" id="plane_class1" title="Class" class="form-control" style="width: 170px !important;">
													<?php get_flight_class_dropdown(); ?>
												</select></td>	            
												<td class="quotation-datetime-td"><input type="text" id="txt_dapart1" name="txt_dapart" class="form-control app_datetimepicker" placeholder="Departure Date and time" title="Departure Date and time" onchange="get_to_datetime(this.id,'txt_arrval1')" value="<?= date('d-m-Y H:i') ?>" /></td>
												<td class="quotation-datetime-td"><input type="text" id="txt_arrval1" name="txt_arrval" class="form-control app_datetimepicker" placeholder="Arrival Date and time" title="Arrival Date and time" value="<?= date('d-m-Y H:i') ?>" onchange="validate_validDatetime('txt_dapart1',this.id)" /></td>
												<td><input type="hidden" id="from_city-1"></td>								
												<td><input type="hidden" id="to_city-1"></td>
											</tr>
										</table>
									</div>
								</div>
							</div>
							</div>
				    </div>
				  </div>
				</div>

				<!-- Cruise Information -->
				<div class="accordion_content main_block">
				  <div class="panel panel-default main_block">
				  	<div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
				        <div class="Normal collapsed main_block" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse4" aria-expanded="false" aria-controls="collapse4" id="collapsed4">                  
				          <div class="col-md-12"><span>Cruise Information</span></div>
				        </div>
				    </div>
				    <div id="collapse3" class="panel-collapse collapse main_block" role="tabpanel" aria-labelledby="heading4">
				        <div class="panel-body">
				            <div class="row mg_bt_10">
							    <div class="col-md-12 text-right text_center_xs">
							        <button type="button" class="btn btn-excel btn-sm" onClick="addRow('tbl_dynamic_cruise_quotation')" title="Add Row"><i class="fa fa-plus"></i></button>
									<button type="button" class="btn btn-pdf btn-sm" onClick="deleteRow('tbl_dynamic_cruise_quotation')" title="Delete Row"><i class="fa fa-trash"></i></button>
							    </div>
							</div>
					          <div class="row">
							    <div class="col-md-12">
							        <div class="table-responsive">
								        <table id="tbl_dynamic_cruise_quotation" name="tbl_dynamic_cruise_quotation" class="table table-bordered no-marg">
										<input type="hidden" id="cruise_dept_date_hidde">
								            <tr>
								                <td><input class="css-checkbox" id="chk_cruise1" type="checkbox" checked><label class="css-label" for="chk_cruise1"><label></td>
								                <td><input maxlength="15" value="1" type="text" name="username" placeholder="Sr. No." class="form-control" disabled /></td>
									            <td><input type="text" id="cruise_departure_date" name="cruise_departure_date" placeholder="Departure Date and Time" title="Departure Date and Time" onchange="get_to_datetime(this.id,'cruise_arrival_date')" class="app_datetimepicker" value="<?= date('d-m-Y H:i') ?>"></td>
									            <td><input type="text" id="cruise_arrival_date" name="cruise_arrival_date" placeholder="Arrival Date and Time" title="Arrival Date and Time" class="app_datetimepicker" value="<?= date('d-m-Y H:i') ?>"></td>
									            <td><input type="text" id="route" name="route" placeholder="Route" title="Route"></td>
									            <td><input type="text" id="cabin" name="cabin" placeholder="Cabin" title="Cabin"></td>
									            <td><select id="sharing" name="sharing" style="width:100%;" title="Sharing">
									            		<option value="">Sharing</option>
									            		<option value="Single">Single</option>
									            		<option value="Double">Double</option>
									            		<option value="Triple Quad">Triple Quad</option>
									                </select></td>
								            </tr>                                
								        </table>
							        </div>
							    </div>
							</div>
				        </div>
				    </div>
				  </div>
				</div>
  			</div>
  		</div>
  	</div>
			
			
	<div class="row text-center mg_tp_20">
		<div class="col-xs-12">
			<button class="btn btn-info btn-sm ico_left" type="button" onclick="switch_to_tab2()"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Previous</button>
			&nbsp;&nbsp;
			<button class="btn btn-info btn-sm ico_right">Next&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
		</div>
	</div>

</form>



<script>

	 $('#airline_name1').select2({
 
        });
        if (typeof initAllAirlineSelectAddNew === 'function') {
            initAllAirlineSelectAddNew('#tbl_package_tour_quotation_dynamic_plane');
        }
        function initGroupQuotationPlaneAirports() {
            if (typeof initPlaneAirportSelect2 === 'function') {
                initPlaneAirportSelect2('#tbl_package_tour_quotation_dynamic_plane');
                event_airport('tbl_package_tour_quotation_dynamic_plane');
            } else {
                setTimeout(initGroupQuotationPlaneAirports, 200);
            }
        }
        initGroupQuotationPlaneAirports();
$('#transport_vehicle_name1').select2({});
if (typeof initAllVehicleSelectAddNew === 'function') {
    initAllVehicleSelectAddNew('#tbl_group_tour_quotation_transport');
}
$('#plane_from_location1,#plane_to_location1,#train_from_location1,#train_to_location1').select2({
	dropdownParent: $("#quotation_save_modal")});
city_lzloading('.train_from', '*From', true);
city_lzloading('.train_to', '*To', true);
destinationLoading('select[name^="transport_pickup_from"]', 'Pickup Location');
destinationLoading('select[name^="transport_drop_to"]', 'Drop-off Location');
if (typeof gqInitDatepicker === 'function') {
	gqInitDatepicker($('#tbl_group_tour_quotation_transport .app_datepicker'), false);
} else {
	$('.app_datepicker').datetimepicker({ timepicker:false, format:'d-m-Y' });
}

function initGroupQuotationDateTimePicker(scope) {
	var $scope = scope ? $(scope) : $('#tbl_package_tour_quotation_dynamic_plane, #tbl_package_tour_quotation_dynamic_train, #tbl_dynamic_cruise_quotation');
	if (typeof gqInitDatepicker === 'function') {
		gqInitDatepicker($scope.find('.app_datetimepicker'), true);
		return;
	}
	var dateTimeOpts = {
		format: 'd-m-Y H:i',
		parentID: 'body',
		fixed: true,
		scrollInput: false
	};
	$scope.find('.app_datetimepicker').each(function () {
		var $el = $(this);
		if ($el.data('xdsoft_datetimepicker')) {
			$el.datetimepicker('destroy');
		}
		$el.datetimepicker(dateTimeOpts);
		if (!$el.hasClass('form-control')) {
			$el.addClass('form-control');
		}
	});
}
initGroupQuotationDateTimePicker();

(function () {
	var tab3DateTimeTableIds = [
		'tbl_package_tour_quotation_dynamic_plane',
		'tbl_package_tour_quotation_dynamic_train',
		'tbl_dynamic_cruise_quotation'
	];
	var nativeAddRow = window.addRow;
	if (typeof nativeAddRow !== 'function') {
		return;
	}
	window.addRow = function (tableID, quot_table, itinerary) {
		nativeAddRow.apply(this, arguments);
		if (tab3DateTimeTableIds.indexOf(tableID) === -1) {
			return;
		}
		setTimeout(function () {
			var table = document.getElementById(tableID);
			if (!table || !table.rows.length) {
				return;
			}
			initGroupQuotationDateTimePicker(table.rows[table.rows.length - 1]);
		}, 50);
	};
})();

// Function to add transport row with proper initialization
function addTransportRowSave(){
	if (typeof gqAddTransportRow === 'function') {
		gqAddTransportRow('tbl_group_tour_quotation_transport');
		return;
	}
	addRow('tbl_group_tour_quotation_transport');
}

$.fn.modal.Constructor.prototype.enforceFocus = function() {};
// App_accordion
jQuery(document).ready(function() {			
			jQuery(".panel-heading").click(function(){ 
				jQuery('#accordion .panel-heading').not(this).removeClass('isOpen');
				jQuery(this).toggleClass('isOpen');
				jQuery(this).next(".panel-collapse").addClass('thePanel');
				jQuery('#accordion .panel-collapse').not('.thePanel').slideUp("slow"); 
		    	jQuery(".thePanel").slideToggle("slow").removeClass('thePanel'); 
			});
			
		});

$(function(){

	$('#frm_tab3').validate({

		rules:{ 

		},

		submitHandler:function(form,e){
			e.preventDefault();
		

		var train_from_location_arr = new Array();

		var train_to_location_arr = new Array();

		var train_class_arr = new Array();

		var train_arrival_date_arr = new Array();

		var train_departure_date_arr = new Array();




		//Train Info
		var table = document.getElementById("tbl_package_tour_quotation_dynamic_train");

		  var rowCount = table.rows.length;

		  

		  for(var i=0; i<rowCount; i++)

		  {

		    var row = table.rows[i];

		     

		    if(typeof gqRowChecked === 'function' ? gqRowChecked(row) : (row.cells[0].childNodes[0] && row.cells[0].childNodes[0].checked))

		    {

		       var train_from_location1 = (typeof gqCellValue === 'function') ? gqCellValue(row.cells[2]) : row.cells[2].childNodes[0].value;         

		       var train_to_location1 = (typeof gqCellValue === 'function') ? gqCellValue(row.cells[3]) : row.cells[3].childNodes[0].value;         

			   var train_class = (typeof gqCellValue === 'function') ? gqCellValue(row.cells[4]) : row.cells[4].childNodes[0].value;         

			   var train_arrival_date = (typeof gqCellValue === 'function') ? gqCellValue(row.cells[5]) : row.cells[5].childNodes[0].value;         

			   var train_departure_date = (typeof gqCellValue === 'function') ? gqCellValue(row.cells[6]) : row.cells[6].childNodes[0].value;         



		       

		       if(train_from_location1=="")

		       {

		          error_msg_alert('Enter train from location in row'+(i+1));
	  			  $('.accordion_content').removeClass("indicator");
	          	  $('#tbl_package_tour_quotation_dynamic_train').parent('div').closest('.accordion_content').addClass("indicator");

		          return false;

		       }



		       if(train_to_location1=="")

		       {

		          error_msg_alert('Enter train to location in row'+(i+1));
	  			  $('.accordion_content').removeClass("indicator");
	          	  $('#tbl_package_tour_quotation_dynamic_train').parent('div').closest('.accordion_content').addClass("indicator");

		          return false;

		       }

		      

		   

		       train_from_location_arr.push(train_from_location1);

		       train_to_location_arr.push(train_to_location1);

			   train_class_arr.push(train_class);

			   train_arrival_date_arr.push(train_arrival_date);

			   train_departure_date_arr.push(train_departure_date);



		    }      

		  }


		//Flight Info
		var from_city_id_arr = new Array();
		var plane_from_location_arr = new Array();
		var to_city_id_arr = new Array();
		var plane_to_location_arr = new Array();
		var airline_name_arr = new Array();

		var plane_class_arr = new Array();

		var arraval_arr = new Array();

		var dapart_arr = new Array();



		var table = document.getElementById("tbl_package_tour_quotation_dynamic_plane");

		  var rowCount = table.rows.length;

		  

		  for(var i=0; i<rowCount; i++)

		  {

		    var row = table.rows[i];

		     

		    if(typeof gqRowChecked === 'function' ? gqRowChecked(row) : (row.cells[0].childNodes[0] && row.cells[0].childNodes[0].checked))

		    {
		       var planeRow = (typeof gqCollectPlaneFromRow === 'function') ? gqCollectPlaneFromRow(row) : null;
		       var plane_from_location1 = planeRow ? planeRow.from_sector : row.cells[2].childNodes[0].value;          
		       var plane_to_location1 = planeRow ? planeRow.to_sector : row.cells[3].childNodes[0].value;
		       var airline_name = planeRow ? planeRow.airline : row.cells[4].childNodes[0].value;  
		       var plane_class = planeRow ? planeRow.plane_class : row.cells[5].childNodes[0].value;  
		       var dapart1 = planeRow ? planeRow.depart : row.cells[6].childNodes[0].value;       
		       var arraval1 = planeRow ? planeRow.arrival : row.cells[7].childNodes[0].value;
			   var from_city_id1 = planeRow ? planeRow.from_city : row.cells[8].childNodes[0].value;
		       var to_city_id1 = planeRow ? planeRow.to_city : row.cells[9].childNodes[0].value; 

		    if(plane_from_location1=="")

		    {

		          error_msg_alert('Enter from sector in row'+(i+1));
	  			  $('.accordion_content').removeClass("indicator");
	          	  $('#tbl_package_tour_quotation_dynamic_plane').parent('div').closest('.accordion_content').addClass("indicator");

		          return false;

		    }

	       if(plane_to_location1=="")

	       {

	          		error_msg_alert('Enter to sector in row'+(i+1));
					$('.accordion_content').removeClass("indicator");
					$('#tbl_package_tour_quotation_dynamic_plane').parent('div').closest('.accordion_content').addClass("indicator");

	          return false;

	       }
		   if(plane_class==""){

				error_msg_alert('Select class in row'+(i+1));
				$('.accordion_content').removeClass("indicator");
				$('#tbl_package_tour_quotation_dynamic_plane').parent('div').closest('.accordion_content').addClass("indicator");

				return false;
		   }


				if(dapart1=="")

				{ 

				error_msg_alert("Departure Date time is required in row:"+(i+1)); 
	  			  $('.accordion_content').removeClass("indicator");
	          	$('#tbl_package_tour_quotation_dynamic_plane').parent('div').closest('.accordion_content').addClass("indicator");

				return false;

				}


				if(arraval1=="")

				{ 

					error_msg_alert('Arrival Date time is required in row:'+(i+1));
	  			  $('.accordion_content').removeClass("indicator");
	          	  $('#tbl_package_tour_quotation_dynamic_plane').parent('div').closest('.accordion_content').addClass("indicator"); 

					return false;

				}

		       plane_from_location_arr.push(plane_from_location1);

		       plane_to_location_arr.push(plane_to_location1);

		       airline_name_arr.push(airline_name);

		       plane_class_arr.push(plane_class);

		       arraval_arr.push(arraval1);

		       dapart_arr.push(dapart1);



		    }      

		  }

		  /* Cruise Info*/
		  var dept_datetime_arr = new Array();
		  var arrival_datetime_arr = new Array();
		  var route_arr = new Array();
		  var cabin_arr = new Array();
		  var sharing_arr = new Array();

		  var table = document.getElementById("tbl_dynamic_cruise_quotation");
		  var rowCount = table.rows.length;
		  
		  for(var i=0; i<rowCount; i++)
		  {
		    var row = table.rows[i];
		    
		    if(row.cells[0].childNodes[0].checked)
		    {
		       var dept_datetime = row.cells[2].childNodes[0].value;         
		       var arrival_datetime = row.cells[3].childNodes[0].value;         
			   var route = row.cells[4].childNodes[0].value;         
			   var cabin = row.cells[5].childNodes[0].value;         
			   var sharing = row.cells[6].childNodes[0].value;         
		       
		       if(dept_datetime=="")
		       {
		          error_msg_alert('Enter cruise departure datetime in row'+(i+1));
	  			  $('.accordion_content').removeClass("indicator");
	          	  $('#tbl_dynamic_cruise_quotation').parent('div').closest('.accordion_content').addClass("indicator");
		          return false;
		       }
		       if(arrival_datetime=="")
		       {
		          error_msg_alert('Enter cruise arrival datetime  in row'+(i+1));
	  			  $('.accordion_content').removeClass("indicator");
	          	  $('#tbl_dynamic_cruise_quotation').parent('div').closest('.accordion_content').addClass("indicator");
		          return false;
		       }
		       if(route=="")
		       {
		          error_msg_alert('Enter cruise route in row'+(i+1));
	  			  $('.accordion_content').removeClass("indicator");
	          	  $('#tbl_dynamic_cruise_quotation').parent('div').closest('.accordion_content').addClass("indicator");
		          return false;
		       }
		       if(cabin=="")
		       {
		          error_msg_alert('Enter cruise cabin in row'+(i+1));
	  			  $('.accordion_content').removeClass("indicator");
	          	  $('#tbl_dynamic_cruise_quotation').parent('div').closest('.accordion_content').addClass("indicator");
		          return false;
		       }
		      		  
		       dept_datetime_arr.push(dept_datetime);
		       arrival_datetime_arr.push(arrival_datetime);
			   route_arr.push(route);
			   cabin_arr.push(cabin);
			   sharing_arr.push(sharing);
		    }      
		  }

		group_quotation_cost_calculate('');
		if (typeof cost_reflect === 'function') {
			cost_reflect();
		}


	  $('.accordion_content').removeClass("indicator");


		$('a[href="#tab4"]').tab('show');		
		}
	});

});

function switch_to_tab2(){ $('a[href="#tab2"]').tab('show'); }

</script>

