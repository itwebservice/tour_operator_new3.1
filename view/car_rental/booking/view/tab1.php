<div class="row">
	<div class="col-md-4 col-sm-12 col-xs-12">
		<div class="profile_box main_block">
        	 	<h3>Customer Details</h3>
				<?php $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_booking[customer_id]'")); 
				$contact_no = $encrypt_decrypt->fnDecrypt($sq_customer['contact_no'], $secret_key);
				$email_id = $encrypt_decrypt->fnDecrypt($sq_customer['email_id'], $secret_key);
				?>
				<span class="main_block"> 
				    <i class="fa fa-user-o" aria-hidden="true"></i>
				    <?php echo $sq_customer['first_name'].' '.$sq_customer['middle_name'].' '.$sq_customer['last_name'].'&nbsp'.'('.get_car_rental_booking_id($booking_id,$year).')'; ?>
				</span>
				<?php if($sq_customer['type'] == 'Corporate'||$sq_customer['type'] == 'B2B'){?>
				<span class="main_block">
						<i class="fa fa-building-o" aria-hidden="true"></i>
						<?php echo $sq_customer['company_name'] ?>
					</span>
				<?php  } ?>
				<span class="main_block">
				    <i class="fa fa-envelope-o" aria-hidden="true"></i>
				    <?php echo $email_id; ?>
				</span>	
				<span class="main_block">
				    <i class="fa fa-phone" aria-hidden="true"></i>
				    <?php echo $contact_no; ?> 
				</span>
				<span class="main_block">
				    <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
				    <?php echo "<label>Guest Name <em>:</em></label> ".$sq_booking['pass_name']; ?>
				</span>
	    </div>
	</div>
	<div class="col-md-8 col-sm-12 col-xs-12">
		<div class="profile_box main_block">
			<h3>Costing Details</h3>
			<div class="col-sm-6 col-xs-12">
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>Total Amount <em>:</em></label> ".$sq_booking['basic_amount'];?> 
				</span>       
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>Service Charge<em>:</em></label> ".$sq_booking['service_charge'];?> 
				</span>  
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>".$tax_name." <em>:</em></label> ".$sq_booking['service_tax_subtotal'];?> 
				</span> 	 	
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>Markup Amount<em>:</em></label> ".$sq_booking['markup_cost'];?> 
				</span> 
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>Markup Tax<em>:</em></label> ".$sq_booking['markup_cost_subtotal'];?> 
				</span> 
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>Extra Km Rate <em>:</em></label> ".$sq_booking['extra_km'];?> 
				</span>
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>Extra Hr Rate <em>:</em></label> ".$sq_booking['extra_hr_cost'];?> 
				</span>
				<?php
				if($sq_booking['local_places_to_visit'] != ''){ ?>
					<span class="main_block">
						<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
						<?php echo "<label>Route <em>:</em></label> ".$sq_booking['local_places_to_visit'];?> 
					</span> 
				<?php } ?>
				<?php
				if($sq_booking['total_km'] != 0){ ?>
					<span class="main_block">
						<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
						<?php echo "<label>Total Km <em>:</em></label> ".$sq_booking['total_km'];?> 
					</span> 
				<?php } ?>
			</div>    	 	
			<div class="col-sm-6 col-xs-12">    
			<?php if($sq_booking['travel_type']=="Outstation"){?>
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>Driver Allowance <em>:</em></label> ".$sq_booking['driver_allowance'];?> 
				</span>
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>Permit Charges <em>:</em></label> ".$sq_booking['permit_charges'];?> 
				</span>
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>Toll & Parking <em>:</em></label> ".$sq_booking['toll_and_parking'];?> 
				</span>
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>State Entry Tax <em>:</em></label> ".$sq_booking['state_entry_tax'];?> 
				</span> 
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>Other Charge <em>:</em></label> ".$sq_booking['other_charges'];?> 
				</span>  
			<?php } ?>   
			<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>Round Off <em>:</em></label> ".$sq_booking['roundoff'];?> 
				</span>           	 	
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>Net Total <em>:</em></label> ".$sq_booking['total_fees'];?> 
				</span> 
				<span class="main_block">
						<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
						<?php
						$sq_credit = mysqli_fetch_assoc(mysqlQuery("SELECT sum(`credit_charges`) as sumc FROM `car_rental_payment` WHERE `booking_id`='$sq_booking[booking_id]' and `clearance_status`!='Cancelled' and clearance_status != 'Pending'"));
						$charge = ($sq_credit['sumc'] != '')?$sq_credit['sumc']:0;
						echo "<label>Credit card charges <em>:</em></label> ".number_format($charge,2)?> 
					</span>
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>Due Date <em>:</em></label> ".get_date_user($sq_booking['due_date']);?> 
				</span>
				<span class="main_block">
					<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
					<?php echo "<label>Booking Date <em>:</em></label> ".get_date_user($sq_booking['created_at']);?> 
				</span>
			</div>	        		

        </div>
    </div>
</div>

<?php 
// Fetch itinerary for this booking
$sq_package_program = mysqlQuery("select * from car_rental_booking_program where booking_id='$booking_id' ORDER BY entry_id ASC");
$sq_package_count = mysqli_num_rows($sq_package_program);
if($sq_package_count > 0){ ?>
<div class="row mg_tp_20">
	<div class="col-md-12">
		<div class="profile_box main_block">
			<h3>Itinerary Details</h3>
			<div class="table-responsive">
				<table class="table table-bordered no-marg">
					<thead>
						<tr class="table-heading-row">
							<th>S_No.</th>
							<th>Special_Attraction</th>
							<th>Day-wise_Program</th>
							<th>Overnight_Stay</th>
							<th>Meal_Plan</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$count = 0;
						while($row_itinarary = mysqli_fetch_assoc($sq_package_program)){
							?>
							<tr>
								<td><?= ++$count ?></td>
								<td><?= $row_itinarary['attraction'] ?></td>
								<td><pre class="real_text"><?= $row_itinarary['day_wise_program'] ?></pre></td>
								<td><?= $row_itinarary['stay'] ?></td>
								<td><?= ($row_itinarary['meal_plan']!='')?$row_itinarary['meal_plan']:'NA' ?></td>
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