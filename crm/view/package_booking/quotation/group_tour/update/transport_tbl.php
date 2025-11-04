<div class="row mg_tp_10 mg_bt_10">
	<div class="col-xs-6 mg_bt_20_sm_xs">
		<button type="button" class="btn btn-excel btn-sm" title="Add Vehicle" onclick="vehicle_save_modal('transport_vehicle_name1')"><i class="fa fa-plus"></i></button>
	</div>
	<div class="col-xs-6 text-right mg_bt_20_sm_xs">
		<button type="button" class="btn btn-excel btn-sm" onClick="addTransportRow();" title="Add Row"><i class="fa fa-plus"></i></button>
		<button type="button" class="btn btn-pdf btn-sm" onClick="deleteRow('tbl_group_tour_quotation_transport_u')" title="Delete Row"><i class="fa fa-trash"></i></button>
	</div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
        <table id="tbl_group_tour_quotation_transport_u" name="tbl_group_tour_quotation_transport_u" class="table mg_bt_0 table-bordered mg_bt_10">

        	<?php 
        	$sq_transport_count = mysqli_num_rows(mysqlQuery("select * from group_tour_quotation_transport_entries where quotation_id='$quotation_id'"));
        	if($sq_transport_count==0){
        		?>
				<tr>
					<td><input class="css-checkbox" id="chk_transport1" type="checkbox"><label class="css-label" for="chk_transport1"> </label></td>
					<td><input maxlength="15" value="1" type="text" name="username" placeholder="Sr No." class="form-control" disabled="" autocomplete="off"></td>
					<td class="col-md-2"><select name="transport_vehicle_name1" id="transport_vehicle_name1" title="Select Vehicle" style="width:200px" class="form-control app_select2">
							<option value="">Select Vehicle</option>
							<?php
							$sq_query = mysqlQuery("select * from b2b_transfer_master where status != 'Inactive'");
							while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>
								<option value="<?php echo $row_dest['entry_id']; ?>">
									<?php echo $row_dest['vehicle_name']; ?></option>
							<?php } ?>
						</select></td>
					<td><input type="text" id="transport_start_date1" name="transport_start_date1" placeholder="Start Date" title="Start Date" class="app_datepicker form-control" style="width:150px" onchange="get_to_date(this.id,'transport_end_date1');"></td>
					<td><input type="text" id="transport_end_date1" name="transport_end_date1" placeholder="End Date" title="End Date" class="app_datepicker form-control" style="width:150px" onchange="validate_validDate('transport_start_date1','transport_end_date1');"></td>
					<td class="col-md-2"><select name="transport_pickup_from1" id="transport_pickup_from1" style="width:250px;" title="Pickup Location" class="form-control app_minselect2 transport_pickup_u">
						</select></td>
					<td class="col-md-2"><select name="transport_drop_to1" id="transport_drop_to1" style="width:250px;" title="Drop-off Location" class="form-control app_minselect2 transport_drop_u">
						</select></td>
					<td><select name="transport_service_duration1" id="transport_service_duration1" style="width:170px;" title="Service Duration" class="form-control app_select2">
							<option value="">Service Duration</option>
							<?php echo get_service_duration_dropdown(); ?>
						</select></td>
					<td><input type="text" id="transport_no_vehicles1" name="transport_no_vehicles1" placeholder="No.Of vehicles" title="No.Of vehicles" class="form-control" style="width:150px"></td>
                </tr>           
        		<?php
        	}
        	else{
        		$count = 0;
        		$sq_q_transport = mysqlQuery("select * from group_tour_quotation_transport_entries where quotation_id='$quotation_id'");
        		while($row_q_transport = mysqli_fetch_assoc($sq_q_transport))
        		{
        			$count++;
        			
        			// Get Vehicle Name
					$sq_vehicle = mysqli_fetch_assoc(mysqlQuery("select entry_id, vehicle_name from b2b_transfer_master where entry_id = '".$row_q_transport['vehicle_name']."'"));
					
        			?>
					<tr>
						<td><input class="css-checkbox" id="chk_transport1<?= $count ?>" type="checkbox" checked><label class="css-label" for="chk_transport1<?= $count ?>" checked> <label></td>
						<td><input maxlength="15" value="<?= $count ?>" type="text" name="no" placeholder="Sr. No." class="form-control" disabled /></td>
						
						<td class="col-md-2"><select name="transport_vehicle_name<?= $count ?>_u" id="transport_vehicle_name<?= $count ?>_u" title="Select Vehicle" style="width:200px" class="form-control app_select2">
								<option value="<?php echo $sq_vehicle['entry_id']; ?>"><?php echo $sq_vehicle['vehicle_name']; ?></option>
								<option value="">Select Vehicle</option>
								<?php
								$sq_query = mysqlQuery("select * from b2b_transfer_master where status != 'Inactive'");
								while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>
									<option value="<?php echo $row_dest['entry_id']; ?>">
										<?php echo $row_dest['vehicle_name']; ?></option>
								<?php } ?>
							</select></td>
						
						<td><input type="text" id="transport_start_date<?= $count ?>_u" name="transport_start_date<?= $count ?>_u" placeholder="Start Date" title="Start Date" class="app_datepicker form-control" style="width:150px" value="<?= get_date_user($row_q_transport['start_date']) ?>" onchange="get_to_date(this.id,'transport_end_date<?= $count ?>_u');"></td>
						
						<td><input type="text" id="transport_end_date<?= $count ?>_u" name="transport_end_date<?= $count ?>_u" placeholder="End Date" title="End Date" class="app_datepicker form-control" style="width:150px" value="<?= get_date_user($row_q_transport['end_date']) ?>" onchange="validate_validDate('transport_start_date<?= $count ?>_u','transport_end_date<?= $count ?>_u');"></td>
						
						<td class="col-md-2"><select name="transport_pickup_from<?= $count ?>_u" id="transport_pickup_from<?= $count ?>_u" data-toggle="tooltip" style="width:250px;" title="Pickup Location" class="form-control app_minselect2 transport_pickup_u">
							<?php
							// Pickup Location
							if ($row_q_transport['pickup_type'] == 'city') {
								$row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='".$row_q_transport['pickup']."'"));
								$html = '<optgroup value="city" label="City Name"><option value="city-' . $row['city_id'] . '">' . $row['city_name'] . '</option></optgroup>';
							} else if ($row_q_transport['pickup_type'] == 'hotel') {
								$row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='".$row_q_transport['pickup']."'"));
								$html = '<optgroup value="hotel" label="Hotel Name"><option value="hotel-' . $row['hotel_id'] . '">' . $row['hotel_name'] . '</option></optgroup>';
							} else if ($row_q_transport['pickup_type'] == 'airport') {
								$row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='".$row_q_transport['pickup']."'"));
								$airport_nam = $row['airport_name'];
								$airport_code = $row['airport_code'];
								$pickup_display = $airport_nam . " (" . $airport_code . ")";
								$html = '<optgroup value="airport" label="Airport Name"><option value="airport-' . $row['airport_id'] . '">' . $pickup_display . '</option></optgroup>';
							} else {
								// Default case - show as is
								$html = '<optgroup value="' . htmlspecialchars($row_q_transport['pickup_type']) . '" label="' . ucfirst($row_q_transport['pickup_type']) . '"><option value="' . htmlspecialchars($row_q_transport['pickup']) . '">' . htmlspecialchars($row_q_transport['pickup']) . '</option></optgroup>';
							}
							echo $html;
							?>
						</select></td>
						
						<td class="col-md-2"><select name="transport_drop_to<?= $count ?>_u" id="transport_drop_to<?= $count ?>_u" style="width:250px;" data-toggle="tooltip" title="Drop-off Location" class="form-control app_minselect2 transport_drop_u">
							<?php
							// Drop Location
							if ($row_q_transport['drop_type'] == 'city') {
								$row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='".$row_q_transport['drop_location']."'"));
								$html = '<optgroup value="city" label="City Name"><option value="city-' . $row['city_id'] . '">' . $row['city_name'] . '</option></optgroup>';
							} else if ($row_q_transport['drop_type'] == 'hotel') {
								$row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='".$row_q_transport['drop_location']."'"));
								$html = '<optgroup value="hotel" label="Hotel Name"><option value="hotel-' . $row['hotel_id'] . '">' . $row['hotel_name'] . '</option></optgroup>';
							} else if ($row_q_transport['drop_type'] == 'airport') {
								$row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='".$row_q_transport['drop_location']."'"));
								$airport_nam = $row['airport_name'];
								$airport_code = $row['airport_code'];
								$drop_display = $airport_nam . " (" . $airport_code . ")";
								$html = '<optgroup value="airport" label="Airport Name"><option value="airport-' . $row['airport_id'] . '">' . $drop_display . '</option></optgroup>';
							} else {
								// Default case - show as is
								$html = '<optgroup value="' . htmlspecialchars($row_q_transport['drop_type']) . '" label="' . ucfirst($row_q_transport['drop_type']) . '"><option value="' . htmlspecialchars($row_q_transport['drop_location']) . '">' . htmlspecialchars($row_q_transport['drop_location']) . '</option></optgroup>';
							}
							echo $html;
							?>
						</select></td>
						
						<td><select name="transport_service_duration<?= $count ?>_u" id="transport_service_duration<?= $count ?>_u" style="width:170px;" title="Service Duration" class="form-control app_select2">
								<option value="<?= $row_q_transport['service_duration'] ?>"><?= $row_q_transport['service_duration'] ?></option>
								<?php echo get_service_duration_dropdown(); ?>
							</select></td>
						
						<td><input type="text" id="transport_no_vehicles<?= $count ?>_u" name="transport_no_vehicles<?= $count ?>_u" placeholder="No.Of vehicles" title="No.Of vehicles" class="form-control" style="width:150px" value="<?= $row_q_transport['vehicle_count'] ?>"></td>
						
						<td class="hidden"><input type="text" id="transport_entry_id<?= $count ?>_u" name="transport_entry_id<?= $count ?>_u" value="<?= $row_q_transport['id'] ?>"></td>
                	</tr>          
        			<?php
        		}
        	}
        	?>                                  
        </table>
        </div>
    </div>
</div> 
<script>
// Initialize destination loading for pickup and drop locations
destinationLoading('.transport_pickup_u', "Pickup Location");
destinationLoading('.transport_drop_u', "Drop-off Location");
$('.app_datepicker').datetimepicker({ timepicker:false, format:'d-m-Y' });

// Function to add transport row with proper initialization
function addTransportRow(){
	addRow('tbl_group_tour_quotation_transport_u');
	setTimeout(function(){ 
		destinationLoading('select[name^=transport_pickup_from]', 'Pickup Location');
		destinationLoading('select[name^=transport_drop_to]', 'Drop-off Location');
		$('.app_datepicker').datetimepicker({ timepicker:false, format:'d-m-Y' });
		$('#tbl_group_tour_quotation_transport_u').find('.app_select2').select2();
	}, 100);
}
</script>

