<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12 mg_bt_20_xs">
		<div class="profile_box main_block">
			<h3>Tour Details</h3>
			<div class="row">
				<div class="col-sm-12 col-xs-12 right_border_none_sm_xs">
					<span class="main_block">
						<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
						<?php echo "<label>Booking ID <em>:</em></label>" . get_b2c_booking_id($booking_id, $year) ?>
					</span>

					<?php if ($sq_package_info['service'] == 'Activity') { ?>

						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Activity Name <em>:</em></label>" . $enq_data[0]->act_name; ?>
						</span>

						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Activity Date <em>:</em></label>" . $enq_data[0]->act_date; ?></span>
						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Transfer Option <em>:</em></label>" . $enq_data[0]->transfer_option; ?></span>
						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Timing Slot <em>:</em></label>" . $enq_data[0]->timing_slot; ?></span>
					<?php } else if ($sq_package_info['service'] == 'Transfer') { ?>

						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Transfer Name <em>:</em></label>" . $enq_data[0]->trans_name; ?>
						</span>
						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Trip Type <em>:</em></label>" . $enq_data[0]->trip_type; ?>
						</span>


						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Pickup Date & Time <em>:</em></label>" . $enq_data[0]->pickup_date; ?></span>
					<?php	 } else if ($sq_package_info['service'] == 'Hotel') { ?>

						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Hotel Name <em>:</em></label>" . $enq_data[0]->hotel_name; ?>
						</span>
						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Room Catagory <em>:</em></label>" . $enq_data[0]->room_cat; ?>
						</span>


						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php
							$checkin = $enq_data[0]->check_in;
							$checkout = $enq_data[0]->check_out;
							$checkin_checkout = $checkin . ' To ' . $checkout;

							echo "<label>Check In  & Check Out Date <em>:</em></label>" . $checkin_checkout; ?></span>
					<?php	 } else { ?>
						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Tour Name <em>:</em></label>" . $enq_data[0]->package_name; ?>
						</span>
						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Travel Date <em>:</em></label>" . $enq_data[0]->travel_from . ' To ' . $enq_data[0]->travel_to ?>
						</span>

					<?php } ?>
					<?php if ($sq_package_info['service'] == 'Transfer') { ?>

						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Total Passenger(s) <em>:</em></label>" . $enq_data[0]->pass ?>
						</span>
					<?php }
					if ($sq_package_info['service'] == 'Hotel') { ?>

						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php
							$room_cat_string = $enq_data[0]->room_cat;
							$room_count = substr_count($room_cat_string, ',') + 1;

							echo "<label>Total Room(s) <em>:</em></label>" . $room_count; ?>
						</span>
					<?php } else if ($sq_package_info['service'] != 'Transfer') { ?>
						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Total Guest(s) <em>:</em></label>" . $total_pax ?>
						</span>
					<?php } ?>
				</div>
				<?php
				if ($sq_package_info['service'] == 'Holiday' || $sq_package_info['service'] == 'Activity') {
				?>
					<div class="col-sm-12 col-xs-12 right_border_none_sm_xs">
						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Pickup Location <em>:</em></label>" . $enq_data[0]->pickup_from ?>
						</span>
						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Pickup Date&Time <em>:</em></label>" . $enq_data[0]->pickup_time; ?>
						</span>
						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Dropoff Location <em>:</em></label>" . $enq_data[0]->drop_to ?>
						</span>
					</div>
				<?php } else if ($sq_package_info['service'] == 'Transfer') {
				?>
					<div class="col-sm-12 col-xs-12 right_border_none_sm_xs">
						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Pickup Location <em>:</em></label>" . $enq_data[0]->pickup_from ?>
						</span>
						<?php if ($enq_data[0]->trip_type != 'Oneway') { ?>
							<span class="main_block">
								<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
								<?php echo "<label>Return Date&Time <em>:</em></label>" . $enq_data[0]->pickup_time; ?>
							</span>
						<?php } ?>
						<span class="main_block">
							<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
							<?php echo "<label>Dropoff Location <em>:</em></label>" . $enq_data[0]->drop_to ?>
						</span>
					</div>
				<?php }  ?>


			</div>
		</div>
	</div>
	<hr>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="profile_box main_block">

			<h3>Customer Details</h3>


			<div class="row">

				<div class="col-sm-12 col-xs-12 right_border_none_sm_xs">

					<span class="main_block">

						<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

						<?php echo  "<label>Customer Name <em>:</em></label> " . $sq_package_info['name'] . '&nbsp'; ?>

					</span>

					<span class="main_block">

						<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

						<?php echo "<label>Email <em>:</em></label> " . $sq_package_info['email_id']; ?>

					</span>

					<span class="main_block">

						<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

						<?php echo "<label>Mobile No <em>:</em></label>" . $sq_package_info['phone_no']; ?>

					</span>

				</div>

				<div class="col-sm-6 col-xs-12">


					<span class="main_block">

						<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

						<?php echo "<label>City <em>:</em></label> " . $sq_package_info['city']; ?>

					</span>

					<span class="main_block">

						<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

						<?php echo "<label>State <em>:</em></label> " . $sq_state['state_name']; ?>

					</span>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row mg_bt_20">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="profile_box main_block">
			<h3>Booking Details</h3>
			<div class="row">
				<div class="col-xs-12">
					<span class="main_block">
						<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
						<?php echo "<label>Booking Date&Time <em>:</em></label> " . get_datetime_user($sq_package_info['created_at']); ?>
					</span>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<span class="main_block">
						<i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
						<?php echo "<label>Other Specification <em>:</em></label> " . $enq_data[0]->specification; ?>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
if ($sq_package_info['service'] != 'Transfer' && $sq_package_info['service'] != 'Hotel') {
?>
	<div class="row">
		<div class="col-md-12">
			<div class="profile_box main_block">
				<h3>Guest Details</h3>
				<div class="table-responsive">
					<table class="table table-bordered no-marg" id="tbl_emp_list">
						<thead>
							<tr class="table-heading-row">
								<th>Adolescence</th>
								<th>Full_Name</th>
								<th>DOB</th>
							</tr>
						</thead>
						<tbody>
							<?php

							// for($i=0;$i<sizeof($guest_data[0]->adult);$i++){

							$adults = isset($guest_data[0]['adult']) && is_array($guest_data[0]['adult']) ? $guest_data[0]['adult'] : [];

							for ($i = 0; $i < sizeof($adults); $i++) {
							?>
								<tr class="<?= $bg_clr ?>">
									<td>Adult</td>
									<td><?php echo $adults[$i]['honorific'] . ' ' . $adults[$i]['first_name'] . ' ' . $adults[$i]['last_name']; ?></td>
									<td><?php echo $adults[$i]['birthdate']; ?></td>
								</tr>
							<?php
							} ?>
							<?php
							$guest_data[0]['chwob'] = isset($guest_data[0]['chwob']) && is_array($guest_data[0]['chwob']) ? $guest_data[0]['chwob'] : [];
							// $guest_data[0]->chwob = ($guest_data[0]->chwob != '') ? $guest_data[0]->chwob : [];
							for ($i = 0; $i < sizeof($guest_data[0]['chwob']); $i++) {
							?>
								<tr class="<?= $bg_clr ?>">
									<td>Child w/o bed</td>
									<td><?php echo $guest_data[0]['chwob'][$i]['honorific'] . ' ' . $guest_data[0]['chwob'][$i]['first_name'] . ' ' . $guest_data[0]['chwob'][$i]['last_name']; ?></td>
									<td><?php echo $guest_data[0]['chwob'][$i]['birthdate']; ?></td>
								</tr>
							<?php
							} ?>
							<?php
							$guest_data[0]['chwb'] = isset($guest_data[0]['chwb']) && is_array($guest_data[0]['chwb']) ? $guest_data[0]['chwb'] : [];
							// $guest_data[0]->chwb = ($guest_data[0]->chwb != '') ? $guest_data[0]->chwb : [];
							for ($i = 0; $i < sizeof($guest_data[0]['chwb']); $i++) {
							?>
								<tr class="<?= $bg_clr ?>">
									<td>Child with bed</td>
									<td><?php echo $guest_data[0]['chwb'][$i]['honorific'] . ' ' . $guest_data[0]['chwb'][$i]['first_name'] . ' ' . $guest_data[0]['chwb'][$i]['last_name']; ?></td>
									<td><?php echo $guest_data[0]['chwb'][$i]['birthdate']; ?></td>
								</tr>
							<?php
							} ?>
							<?php
							$guest_data[0]['extra_bed'] = isset($guest_data[0]['extra_bed']) && is_array($guest_data[0]['extra_bed']) ? $guest_data[0]['extra_bed'] : [];
							// $guest_data[0]->extra_bed = ($guest_data[0]->extra_bed != '') ? $guest_data[0]->extra_bed : [];
							for ($i = 0; $i < sizeof($guest_data[0]['extra_bed']); $i++) {
							?>
								<tr class="<?= $bg_clr ?>">
									<td>Extra Bed</td>
									<td><?php echo $guest_data[0]['extra_bed'][$i]['honorific'] . ' ' . $guest_data[0]['extra_bed'][$i]['first_name'] . ' ' . $guest_data[0]['extra_bed'][$i]['last_name']; ?></td>
									<td><?php echo $guest_data[0]['extra_bed'][$i]['birthdate']; ?></td>
								</tr>
							<?php
							} ?>
							<!-- child for activity -->
							<?php
							$guest_data[0]['child'] = isset($guest_data[0]['child']) && is_array($guest_data[0]['child']) ? $guest_data[0]['child'] : [];
							// $guest_data[0]->child = ($guest_data[0]->child != '') ? $guest_data[0]->child : [];
							for ($i = 0; $i < sizeof($guest_data[0]['child']); $i++) {
							?>
								<tr class="<?= $bg_clr ?>">
									<td>Child</td>
									<td><?php echo $guest_data[0]['child'][$i]['honorific'] . ' ' . $guest_data[0]['child'][$i]['first_name'] . ' ' . $guest_data[0]['child'][$i]['last_name']; ?></td>
									<td><?php echo $guest_data[0]['child'][$i]['birthdate']; ?></td>
								</tr>
							<?php
							} ?>
							<?php
							$guest_data[0]['infant'] = isset($guest_data[0]['infant']) && is_array($guest_data[0]['infant']) ? $guest_data[0]['infant'] : [];
							// $guest_data[0]->infant = ($guest_data[0]->infant != '') ? $guest_data[0]->infant : [];
							for ($i = 0; $i < sizeof($guest_data[0]['infant']); $i++) {
							?>
								<tr class="<?= $bg_clr ?>">
									<td>Infant</td>
									<td><?php echo $guest_data[0]['infant'][$i]['honorific'] . ' ' . $guest_data[0]['infant'][$i]['first_name'] . ' ' . $guest_data[0]['infant'][$i]['last_name']; ?></td>
									<td><?php echo $guest_data[0]['infant'][$i]['birthdate']; ?></td>
								</tr>
							<?php
							} ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<?php if ($sq_package_info['service'] == 'Hotel') { ?>
	<div class="row">
		<div class="col-md-12">
			<div class="profile_box main_block">
				<h3>Guest Details</h3>
				<div class="table-responsive">
					<table class="table table-bordered no-marg" id="tbl_emp_list">
						<thead>
							<tr class="table-heading-row">
								<th>Room No</th>
								<th>Adolescence</th>
								<th>Full Name</th>
								<th>DOB</th>
							</tr>
						</thead>
						<tbody>
							<?php

							$data = $guest_data;

							if (!empty($data[0]['rooms_data'])) {
								foreach ($data[0]['rooms_data'] as $room) {
									$room_no = $room['room'];

									// Adults
									if (!empty($room['adults'])) {
										foreach ($room['adults'] as $adult) {
											echo "<tr>
											<td>{$room_no}</td>
											<td>Adult</td>
											<td>{$adult['honorific']} {$adult['first_name']} {$adult['last_name']}</td>
											<td>{$adult['birthdate']}</td>
										</tr>";
										}
									}

									// Children
									if (!empty($room['children'])) {
										foreach ($room['children'] as $child) {
											echo "<tr>
											<td>{$room_no}</td>
											<td>Child</td>
											<td>{$child['honorific']} {$child['first_name']} {$child['last_name']}</td>
											<td>{$child['birthdate']}</td>
										</tr>";
										}
									}
								}
							} else {
								echo "<tr><td colspan='4'>No room data available.</td></tr>";
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

<?php } ?>