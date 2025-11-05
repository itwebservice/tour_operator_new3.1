<div class="row mg_bt_10">
    <div class="col-md-6">
        <button type="button" class="btn btn-excel btn-sm" title="Note - Please ensure you added transfer tariff"><i class="fa fa-question-circle"></i></button>
        <button type="button" class="btn btn-excel" title="Add Vehicle" onclick="vehicle_save_modal('vehicle_name1')"><i class="fa fa-plus"></i></button>
        <button type="button" class="btn btn-excel btn-sm" title="Add Airport" onclick="airport_airline_save_modal()"><i class="fa fa-plus"></i></button>
    </div>
    <div class="col-md-6 text-right text_center_xs">
        <button type="button" class="btn btn-excel btn-sm" onClick="addRow('tbl_package_tour_transport_update');destinationLoading('select[name^=pickup_from]', 'Pickup Location');destinationLoading('select[name^=drop_to]', 'Drop-off Location');"><i class="fa fa-plus"></i></button>
    </div>
</div>

<div class="row mg_bt_10">
    <div class="col-md-12">
        <div class="table-responsive">
        <table id="tbl_package_tour_transport_update" name="tbl_package_tour_transport_update" class="table table-bordered no-marg pd_bt_51">
			<?php 
			$sq_transport_count = mysqli_num_rows(mysqlQuery("select * from tour_groups_transport where tour_id='$tour_id'"));
			if($sq_transport_count==0){
				?>
                <tr>
                    <td><input class="css-checkbox labelauty" id="chk_transport1" type="checkbox" checked="" autocomplete="off"><label for="chk_transport1"></label></td>
                    <td><input maxlength="15" value="1" type="text" name="username" placeholder="Sr No." class="form-control" disabled="" autocomplete="off"></td>
                    <td class="col-md-3"><select name="vehicle_name1" id="vehicle_name1" title="Select Vehicle" style="width:100%" class="form-control app_select2">
                            <option value="">Select Vehicle</option>
                            <?php
                            $sq_query = mysqlQuery("select * from b2b_transfer_master where status != 'Inactive'");
                            while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>
                                <option value="<?php echo $row_dest['entry_id']; ?>">
                                    <?php echo $row_dest['vehicle_name']; ?></option>
                            <?php } ?>
                        </select></td>
                    <td class="col-md-3"><select name="pickup_from1" id="pickup_from1" style="width:100%;" title="Pickup Location" class="form-control app_minselect2">
                        </select></td>
                    <td class="col-md-3"><select name="drop_to1" id="drop_to1" style="width:100%;" title="Drop-off Location" class="form-control app_minselect2">
                        </select></td>
                </tr>
				<?php
			}
			else{
				$offset = "_d";
				$count = 0;
				$sq_transport = mysqlQuery("select * from tour_groups_transport where tour_id='$tour_id'");
				while($row_transport = mysqli_fetch_assoc($sq_transport)){
					$count++;
					?>
                    <tr>
                        <td><input class="css-checkbox labelauty" id="chk_transport<?= $offset.$count?>_d" type="checkbox" disabled checked autocomplete="off"><label for="chk_transport<?= $offset.$count?>_d"></label></td>
                       
                        <td><input maxlength="15" value="<?= $count ?>" type="text" name="username" placeholder="Sr No." class="form-control" disabled /></td>
                        
                        <td class="col-md-3"><select id="vehicle_name<?= $offset.$count?>_d" name="vehicle_name<?= $offset.$count?>_d" title="Select Vehicle" style="width:100%" class="form-control app_select2">
                            <option value="">Select Vehicle</option>
                            <?php
                            $sq_query = mysqlQuery("select * from b2b_transfer_master where status != 'Inactive'");
                            while ($row_vehicle = mysqli_fetch_assoc($sq_query)) { 
                                $selected = ($row_transport['vehicle_name'] == $row_vehicle['entry_id']) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $row_vehicle['entry_id']; ?>" <?= $selected ?>>
                                    <?php echo $row_vehicle['vehicle_name']; ?></option>
                            <?php } ?>
                        </select></td>
                        
                        <td class="col-md-3"><select name="pickup_from<?= $offset.$count?>_d" id="pickup_from<?= $offset.$count?>_d" data-toggle="tooltip" style="width:100%;" title="Pickup Location" class="form-control app_minselect2 pickup_from_u">
                            <?php
                            // Pickup Location
                            if ($row_transport['pickup_type'] == 'city') {
                                $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_transport[pickup]'"));
                                $html = '<optgroup value="city" label="City Name"><option value="city-' . $row['city_id'] . '">' . $row['city_name'] . '</option></optgroup>';
                            } else if ($row_transport['pickup_type'] == 'hotel') {
                                $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_transport[pickup]'"));
                                $html = '<optgroup value="hotel" label="Hotel Name"><option value="hotel-' . $row['hotel_id'] . '">' . $row['hotel_name'] . '</option></optgroup>';
                            } else if ($row_transport['pickup_type'] == 'airport') {
                                $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_transport[pickup]'"));
                                $airport_nam = $row['airport_name'];
                                $airport_code = $row['airport_code'];
                                $pickup_display = $airport_nam . " (" . $airport_code . ")";
                                $html = '<optgroup value="airport" label="Airport Name"><option value="airport-' . $row['airport_id'] . '">' . $pickup_display . '</option></optgroup>';
                            } else {
                                // Default case - show as is
                                $html = '<optgroup value="' . htmlspecialchars($row_transport['pickup_type']) . '" label="' . ucfirst($row_transport['pickup_type']) . '"><option value="' . htmlspecialchars($row_transport['pickup']) . '">' . htmlspecialchars($row_transport['pickup']) . '</option></optgroup>';
                            }
                            echo $html;
                            ?>
                        </select></td>
                        
                        <td class="col-md-3"><select name="drop_to<?= $offset.$count?>_d" id="drop_to<?= $offset.$count?>_d" style="width:100%;" data-toggle="tooltip" title="Drop-off Location" class="form-control app_minselect2 drop_to_u">
                            <?php
                            // Drop Location
                            if ($row_transport['drop_type'] == 'city') {
                                $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_transport[drop_location]'"));
                                $html = '<optgroup value="city" label="City Name"><option value="city-' . $row['city_id'] . '">' . $row['city_name'] . '</option></optgroup>';
                            } else if ($row_transport['drop_type'] == 'hotel') {
                                $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_transport[drop_location]'"));
                                $html = '<optgroup value="hotel" label="Hotel Name"><option value="hotel-' . $row['hotel_id'] . '">' . $row['hotel_name'] . '</option></optgroup>';
                            } else if ($row_transport['drop_type'] == 'airport') {
                                $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_transport[drop_location]'"));
                                $airport_nam = $row['airport_name'];
                                $airport_code = $row['airport_code'];
                                $drop_display = $airport_nam . " (" . $airport_code . ")";
                                $html = '<optgroup value="airport" label="Airport Name"><option value="airport-' . $row['airport_id'] . '">' . $drop_display . '</option></optgroup>';
                            } else {
                                // Default case - show as is
                                $html = '<optgroup value="' . htmlspecialchars($row_transport['drop_type']) . '" label="' . ucfirst($row_transport['drop_type']) . '"><option value="' . htmlspecialchars($row_transport['drop_location']) . '">' . htmlspecialchars($row_transport['drop_location']) . '</option></optgroup>';
                            }
                            echo $html;
                            ?>
                        </select></td>
                       
                        <td><input type="hidden" id="transport_entry_id<?= $offset.$count?>_d" name="transport_entry_id<?= $offset.$count?>_d" value="<?php echo ($row_transport['entry_id']); ?>"></td>
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
destinationLoading('.pickup_from_u', "Pickup Location");
destinationLoading('.drop_to_u', "Drop-off Location");
</script>

