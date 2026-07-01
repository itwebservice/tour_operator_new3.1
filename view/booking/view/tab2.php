<?php
$sq_train_count = mysqli_num_rows(mysqlQuery("select * from train_master where tourwise_traveler_id='$id'"));
if($sq_train_count!='0'){ 
?>
<div class="row">
	<div class="col-md-12 mg_bt_20">
		<div class="profile_box main_block">
    	 	<h3 class="editor_title">Train Details</h3>
				<div class="table-responsive">
                	<table class="table table-bordered no-marg">
                    	<thead>
                       		<tr class="table-heading-row">
		                       	<th>S_No.</th>
		                       	<th>Departure_Date/Time</th>
		                       	<th>Location_From</th>
		                       	<th>Location_To</th>
		                       	<th>Train_Name_No</th>
		                       	<th>Total_Seats</th>
		                       	<th>Class</th>
		                       	<th>Priority</th>
                       		</tr>
                    	</thead>
                   		<tbody>
                       <?php 
                       		$count = 0;
                       		$sq_entry = mysqlQuery("select * from train_master where tourwise_traveler_id='$id'");
                       		while($row_entry = mysqli_fetch_assoc($sq_entry)){
                       			$count++;
                       	?>
							<tr class="<?php echo $bg; ?>">
							    <td><?php echo $count; ?></td>
							    <td><?php echo date("d-m-Y H:i", strtotime($row_entry['date'])) ?></td>
							    <td><?php echo $row_entry['from_location'] ?></td>
								<td><?php echo $row_entry['to_location']; ?></td>
							    <td><?php echo $row_entry['train_no']; ?></td>
							    <td><?php echo $row_entry['seats']; ?> </td>
							    <td><?php echo $row_entry['train_class']; ?> </td>
							    <td><?php echo $row_entry['train_priority']; ?></td>
							</tr>       
	               			<?php
	               				}
	               			?>
	                    </tbody>
           			 </table>
            	</div>
	    </div> 
	</div>
</div>
<?php } ?>

<?php
$sq_air_count = mysqli_num_rows(mysqlQuery("select * from plane_master where tourwise_traveler_id='$id'")); 
if($sq_air_count!='0'){ 
?>
<div class="row mg_bt_20">
	<div class="col-md-12">
		<div class="profile_box main_block">
				<h3 class="editor_title">Flight Details</h3>
				<div class="table-responsive">
					<table class="table table-bordered no-marg">
						<thead>
							<tr class="table-heading-row">
								<th>S_No.</th>
								<th>Departure_D/T</th>
								<th>Arrival_D/T</th>
								<th>From_City</th>
								<th>Sector_From</th>
								<th>To_City</th>
								<th>Sector_To</th>
								<th>Airline_Name</th>
								<th>Class</th>
								<th>Total_Seats</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$count = 0;
							$sq_air_count = mysqli_num_rows(mysqlQuery("select * from plane_master where tourwise_traveler_id='$id'"));
							if($sq_air_count!='0'){
							$sq_entry = mysqlQuery("select * from plane_master where tourwise_traveler_id='$id'");
							while($row_entry = mysqli_fetch_assoc($sq_entry)){
								$count++;
								$sq_airline = mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$row_entry[company]'"));

								$sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$row_entry[from_city]'"));
								$sq_city1 = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$row_entry[to_city]'"));
								?>
								<tr class="<?php echo $bg; ?>">
									<td><?php echo $count; ?></td>
									<td><?php echo date("d-m-Y H:i", strtotime($row_entry['date'])) ?></td>
									<td><?php echo date("d-m-Y H:i", strtotime($row_entry['arraval_time'])); ?> </td>
									<td><?php echo $sq_city['city_name']; ?></td>
									<td><?php echo $row_entry['from_location']; ?></td>
									<td><?php echo $sq_city1['city_name']; ?></td>
									<td><?php echo $row_entry['to_location']; ?></td>
									<td><?php echo $sq_airline['airline_name'].' ('.$sq_airline['airline_code'].')'; ?></td>
									<td><?php echo $row_entry['class']; ?></td>
									<td><?php echo $row_entry['seats']; ?> </td>
								</tr>      
							<?php
							}
						}
						?>
	                    </tbody>
                	</table>
            	</div>
	    	</div> 
		</div>
	</div>
<?php } ?>
<?php 
$sq_air_count = mysqli_num_rows(mysqlQuery("select * from group_tour_hotel_entries where tour_id='$tour_id'")); 
if($sq_air_count!='0'){ 
?>
<div class="row mg_bt_20">
	<div class="col-md-12">
		<div class="profile_box main_block">
        	 	<h3 class="editor_title">Hotel Details</h3>
				<div class="table-responsive">
                    <table class="table table-bordered no-marg">
	                    <thead>
	                       	<tr class="table-heading-row">
								<th>S_No.</th>
								<th>City_name</th>
								<th>Hotel_name</th>
								<th>Hotel Category</th>
								<th>Total Night(s)</th>
	                       </tr>
	                    </thead>
	                    <tbody>
	                       <?php 
	                       		$count = 0;
									$sq_entry = mysqlQuery("select * from group_tour_hotel_entries where tour_id='$tour_id'");
									while($row_entry = mysqli_fetch_assoc($sq_entry)){
										$count++;
										$sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$row_entry[city_id]'")); 
										$sq_hotel = mysqli_fetch_assoc(mysqlQuery("select hotel_name from hotel_master where hotel_id='$row_entry[hotel_id]'"));
								?>
							<tr class="<?php echo $bg; ?>">
							    <td><?php echo $count; ?></td>
							    <td><?php echo $sq_city['city_name']; ?></td>
								<td><?php echo $sq_hotel['hotel_name']; ?></td>
								<td><?php echo $row_entry['hotel_type']; ?></td>
								<td><?php echo $row_entry['total_nights']; ?></td>
							</tr>     
	               			<?php
	               				}
	               			?>
	                    </tbody>
                	</table>
            	</div>
	    	</div> 
		</div>
	</div>
<?php } ?>

<!-- Transport Details -->
<?php
$traveler_group_id = $sq_group_info['traveler_group_id'];
$sq_transport_count = mysqli_num_rows(mysqlQuery("select * from group_tour_booking_transport_entries where traveler_group_id='$traveler_group_id'"));
if($sq_transport_count > 0){ 
?>
<div class="row mg_bt_20">
	<div class="col-md-12">
		<div class="profile_box main_block">
        	 	<h3 class="editor_title">Transport Details</h3>
				<div class="table-responsive">
                    <table class="table table-bordered no-marg">
	                    <thead>
	                       	<tr class="table-heading-row">
								<th>S_No.</th>
								<th>Vehicle</th>
								<th>Start Date</th>
								<th>End Date</th>
								<th>Pickup Location</th>
								<th>Drop Location</th>
								<th>Service Duration</th>
								<th>No. of Vehicles</th>
	                       </tr>
	                    </thead>
	                    <tbody>
	                       <?php 
	                       		$count = 0;
									$sq_transport = mysqlQuery("select * from group_tour_booking_transport_entries where traveler_group_id='$traveler_group_id'");
									while($row_transport = mysqli_fetch_assoc($sq_transport)){
										$count++;
										
										// Get Vehicle Name
										$sq_vehicle = mysqli_fetch_assoc(mysqlQuery("select vehicle_name from b2b_transfer_master where entry_id = '".$row_transport['vehicle_name']."'"));
										$vehicle_name = $sq_vehicle['vehicle_name'] ? $sq_vehicle['vehicle_name'] : 'N/A';
										
										// Get Pickup Location
										$pickup_location = 'N/A';
										if($row_transport['pickup_type'] == 'city'){
											$row = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='".$row_transport['pickup']."'"));
											$pickup_location = $row['city_name'] ? $row['city_name'] : 'N/A';
										}
										else if($row_transport['pickup_type'] == 'hotel'){
											$row = mysqli_fetch_assoc(mysqlQuery("select hotel_name from hotel_master where hotel_id='".$row_transport['pickup']."'"));
											$pickup_location = $row['hotel_name'] ? $row['hotel_name'] : 'N/A';
										}
										else if($row_transport['pickup_type'] == 'airport'){
											$row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code from airport_master where airport_id='".$row_transport['pickup']."'"));
											if($row){
												$pickup_location = $row['airport_name']." (".$row['airport_code'].")";
											}
										}
										
										// Get Drop Location
										$drop_location = 'N/A';
										if($row_transport['drop_type'] == 'city'){
											$row = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='".$row_transport['drop_location']."'"));
											$drop_location = $row['city_name'] ? $row['city_name'] : 'N/A';
										}
										else if($row_transport['drop_type'] == 'hotel'){
											$row = mysqli_fetch_assoc(mysqlQuery("select hotel_name from hotel_master where hotel_id='".$row_transport['drop_location']."'"));
											$drop_location = $row['hotel_name'] ? $row['hotel_name'] : 'N/A';
										}
										else if($row_transport['drop_type'] == 'airport'){
											$row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code from airport_master where airport_id='".$row_transport['drop_location']."'"));
											if($row){
												$drop_location = $row['airport_name']." (".$row['airport_code'].")";
											}
										}
								?>
							<tr>
							    <td><?php echo $count; ?></td>
							    <td><?php echo $vehicle_name; ?></td>
								<td><?php echo get_date_user($row_transport['start_date']); ?></td>
								<td><?php echo get_date_user($row_transport['end_date']); ?></td>
								<td><?php echo $pickup_location; ?></td>
								<td><?php echo $drop_location; ?></td>
								<td><?php echo $row_transport['service_duration']; ?></td>
								<td><?php echo $row_transport['vehicle_count']; ?></td>
							</tr>     
	               			<?php
	               				}
	               			?>
	                    </tbody>
                	</table>
            	</div>
	    	</div> 
		</div>
	</div>
<?php } ?>

<?php
$sq_cruise_count = mysqli_num_rows(mysqlQuery("select * from group_cruise_master where booking_id='$id'"));
if($sq_cruise_count!='0'){ 
?>
<div class="row">
	<div class="col-md-12 mg_bt_20">
		<div class="profile_box main_block">
    	 	<h3 class="editor_title">Cruise Details</h3>
				<div class="table-responsive">
                	<table class="table table-bordered no-marg">
                    	<thead>
                       		<tr class="table-heading-row">
		                       	<th>S_No.</th>
		                       	<th>Departure_Date/Time</th>
		                       	<th>Arrival_Date/Time</th>
		                       	<th>Route</th>
		                       	<th>Cabin</th>
		                       	<th>Sharing</th>
		                       	<th>Total_Seats</th>
                       		</tr>
                    	</thead>
                   		<tbody>
                       <?php 
                       		$count = 0;
                       		$sq_entry = mysqlQuery("select * from group_cruise_master where booking_id='$id'");
                       		while($row_entry = mysqli_fetch_assoc($sq_entry)){
                       			$count++;
                       	?>
							<tr class="<?php echo $bg; ?>">
							    <td><?php echo $count; ?></td>
							    <td><?php echo date("d-m-Y H:i", strtotime($row_entry['dept_datetime'])) ?></td>
							    <td><?php echo date("d-m-Y H:i", strtotime($row_entry['arrival_datetime'])) ?></td>
								<td><?php echo $row_entry['route']; ?></td>
							    <td><?php echo $row_entry['cabin']; ?></td>
							    <td><?php echo $row_entry['sharing']; ?> </td>
							    <td><?php echo $row_entry['seats']; ?> </td>
							</tr>        
	               			<?php
	               				}
	               			?>
	                    </tbody>
           			 </table>
            	</div>
	    </div> 
	</div>
</div>
<?php } ?>