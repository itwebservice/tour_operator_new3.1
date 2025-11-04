<!-- Hotel  -->
<?php
$sq_t_count = mysqli_num_rows(mysqlQuery("Select * from group_tour_hotel_entries where tour_id = '$tour_id'"));
if($sq_t_count != '0'){
?>
<div class="row mg_bt_30">
	<div class="col-md-12">
	        <h3 class="editor_title">Hotel Details</h3>
             <div class="table-responsive">
		        <table class="table no-marg table-bordered">
		            <thead>
		                <tr class="table-heading-row">
							<th>S_No.</th>
	         				<th>City</th>
					        <th>Hotel_name</th>
					        <th>Hotel_Category</th>
					        <th>Total_Nights</th>
					    </tr>
					</thead>
					<tbody>
						<?php 
							$count = 1; 
							$query1 = "select * from group_tour_hotel_entries where tour_id = '$tour_id'";
							$sq_tourgrp1 = mysqlQuery($query1);
							while($sq_tourgrp2 = mysqli_fetch_assoc($sq_tourgrp1)) 
							{				
								$sq_hotel = mysqli_fetch_assoc(mysqlQuery("select hotel_name from hotel_master where hotel_id = ".$sq_tourgrp2['hotel_id']));
								$sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id = ".$sq_tourgrp2['city_id']));	  
							?>
						<tr>
							<td><?php echo $count++; ?></td>
							<td><?php echo $sq_city['city_name']; ?></td>
							<td><?php echo $sq_hotel['hotel_name']; ?></td>
							<td><?php echo $sq_tourgrp2['hotel_type']; ?></td>
							<td><?php echo $sq_tourgrp2['total_nights']; ?></td>						
						</tr>
						<?php } ?>
					</tbody>
				</table>
				
    	 	</div>
	</div>
</div>
<?php } ?>

<!-- Transport  -->
<?php
$sq_transport_count = mysqli_num_rows(mysqlQuery("Select * from tour_groups_transport where tour_id = '$tour_id'"));
if($sq_transport_count != '0'){
?>
<div class="row mg_bt_30">
	<div class="col-md-12">
	        <h3 class="editor_title">Transport Details</h3>
             <div class="table-responsive">
		        <table class="table no-marg table-bordered">
		            <thead>
		                <tr class="table-heading-row">
							<th>S_No.</th>
	         				<th>Vehicle_Name</th>
					        <th>Pickup_Location</th>
					        <th>Drop_Location</th>
					    </tr>
					</thead>
					<tbody>
						<?php 
							$count = 1; 
							$query1 = "select * from tour_groups_transport where tour_id = '$tour_id'";
							$sq_transport = mysqlQuery($query1);
							while($row_transport = mysqli_fetch_assoc($sq_transport)) 
							{				
								// Get Vehicle Name
								$sq_vehicle = mysqli_fetch_assoc(mysqlQuery("select vehicle_name from b2b_transfer_master where entry_id = '".$row_transport['vehicle_name']."'"));
								$vehicle_name = $sq_vehicle['vehicle_name'] ? $sq_vehicle['vehicle_name'] : 'N/A';
								
								// Get Pickup Location based on type
								$pickup_location = '';
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
									} else {
										$pickup_location = 'N/A';
									}
								}
								else {
									$pickup_location = $row_transport['pickup'];
								}
								
								// Get Drop Location based on type
								$drop_location = '';
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
									} else {
										$drop_location = 'N/A';
									}
								}
								else {
									$drop_location = $row_transport['drop_location'];
								}
							?>
						<tr>
							<td><?php echo $count++; ?></td>
							<td><?php echo $vehicle_name; ?></td>
							<td><?php echo $pickup_location; ?></td>
							<td><?php echo $drop_location; ?></td>						
						</tr>
						<?php } ?>
					</tbody>
				</table>
				
    	 	</div>
	</div>
</div>
<?php } ?>

<!-- Train  -->
<?php 
$sq_t_count = mysqli_num_rows(mysqlQuery("Select * from group_train_entries where tour_id = '$tour_id'"));
if($sq_t_count != '0'){
?>
<div class="row">
	<div class="col-md-12">
	        <h3 class="editor_title">Train Details</h3>
             <div class="table-responsive">
		        <table class="table no-marg table-bordered">
		            <thead>
		                <tr class="table-heading-row">
							<th>S_No.</th>
	         				<th>From_Location</th>
					        <th>To_Location</th>
					        <th>Class</th>
					    </tr>
					</thead>
					<tbody>
						<?php 
							$count = 1; 
							$query1 = "select * from group_train_entries where tour_id = '$tour_id'";
							$sq_tourgrp1 = mysqlQuery($query1);
							while($sq_tourgrp2 = mysqli_fetch_assoc($sq_tourgrp1)) 
							{						  
							?>
						<tr>
							<td><?php echo $count++; ?></td>
							<td><?php echo $sq_tourgrp2['from_location']; ?></td>
							<td><?php echo $sq_tourgrp2['to_location']; ?></td>
							<td><?php echo $sq_tourgrp2['class']; ?></td>						
						</tr>
						<?php } ?>
					</tbody>
				</table>
				
    	 	</div>
	</div>
</div>
<?php } ?>

<!-- Flight -->
<?php 
$sq_f_count = mysqli_num_rows(mysqlQuery("Select * from group_tour_plane_entries where tour_id = '$tour_id'"));
if($sq_f_count != '0'){
?>
<div class="row">
	<div class="col-md-12 mg_tp_30">
	        <h3 class="editor_title">Flight Details</h3>
             <div class="table-responsive">
		        <table class="table no-marg table-bordered">
		            <thead>
		                <tr class="table-heading-row">
							<th>S_No.</th>
							<th>From_City</th>
	         				<th>Sector_From</th>
	         				<th>To_City</th>
					        <th>Sector_To</th>
					        <th>Airline_Name</th>
					        <th>Class</th>
					    </tr>
					</thead>
					<tbody>
						<?php 
							$count = 1; 
							$query = "Select * from group_tour_plane_entries where tour_id = '$tour_id'";
							$sq_tourgrp1 = mysqlQuery($query);
							while($sq_tourgrp = mysqli_fetch_assoc($sq_tourgrp1)) {
								$sq_airline = mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$sq_tourgrp[airline_name]'"));
								$sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$sq_tourgrp[from_city]'"));
					            $sq_city1 = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$sq_tourgrp[to_city]'"));
							?>
						<tr>
							<td><?php echo $count++; ?></td>
							<td><?php echo $sq_city['city_name']; ?></td>
							<td><?php echo $sq_tourgrp['from_location']; ?></td>
							<td><?php echo $sq_city1['city_name']; ?></td>
							<td><?php echo $sq_tourgrp['to_location']; ?></td>
							<td><?php echo $sq_airline['airline_name'].' ('.$sq_airline['airline_code'].')'; ?></td>
							<td><?php echo $sq_tourgrp['class']; ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				
    	 	</div>
	</div>
</div>
<?php } ?>

<!-- Cruise -->
<?php 
$sq_c_count = mysqli_num_rows(mysqlQuery("Select * from group_cruise_entries where tour_id = '$tour_id'"));
if($sq_c_count != '0'){
?>
<div class="row">
	<div class="col-md-12 mg_tp_30">
	        <h3 class="editor_title">Cruise Details</h3>
             <div class="table-responsive">
		        <table class="table no-marg table-bordered">
		            <thead>
		                <tr class="table-heading-row">
							<th>S_No.</th>
					        <th>Route</th>
					        <th>Cabin</th>
					    </tr>
					</thead>
					<tbody>
						<?php 
							$count = 1; 
							$query = "select * from group_cruise_entries where tour_id = '$tour_id'";
							$sq_tourgrp_c1 = mysqlQuery($query);
							while($sq_tourgrp_c = mysqli_fetch_assoc($sq_tourgrp_c1)) {
						  
							?>
						<tr>
							<td><?php echo $count++; ?></td>
							<td><?php echo $sq_tourgrp_c['route']; ?></td>
							<td><?php echo $sq_tourgrp_c['cabin']; ?></td>
						</tr>
						<?php 
						} ?>
					</tbody>
				</table>
				
    	 	</div>
	</div>
</div>
<?php } ?>