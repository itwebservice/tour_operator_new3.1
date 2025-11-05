<?php
$traveler_group_id = $sq_tourwise_id['traveler_group_id'];
?>
<div class="row mg_tp_10 mg_bt_10">
    <div class="col-xs-6 mg_bt_20_sm_xs">
        <button type="button" class="btn btn-excel btn-sm" title="Add Vehicle" onclick="vehicle_save_modal('transport_vehicle_name1_u')"><i class="fa fa-plus"></i></button>
    </div>
    <div class="col-xs-6 text-right mg_bt_20_sm_xs">
        <button type="button" class="btn btn-excel btn-sm" onClick="addTransportRowUpdate();" title="Add Row"><i class="fa fa-plus"></i></button>
        <button type="button" class="btn btn-pdf btn-sm" onClick="deleteRow('tbl_booking_transport_u')" title="Delete Row"><i class="fa fa-trash"></i></button>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
            <table id="tbl_booking_transport_u" name="tbl_booking_transport_u" class="table table-bordered no-marg pd_bt_51">
                <?php
                $count = 1;
                $sq_transport = mysqlQuery("select * from group_tour_booking_transport_entries where traveler_group_id='$traveler_group_id'");
                while($row_transport = mysqli_fetch_assoc($sq_transport)){
                    
                    // Get Vehicle Name
                    $sq_vehicle = mysqli_fetch_assoc(mysqlQuery("select vehicle_name from b2b_transfer_master where entry_id = '".$row_transport['vehicle_name']."'"));
                    $vehicle_name = $sq_vehicle['vehicle_name'] ? $sq_vehicle['vehicle_name'] : 'N/A';
                    
                    // Get Pickup Location
                    $pickup_html = '';
                    if($row_transport['pickup_type'] == 'city'){
                        $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='".$row_transport['pickup']."'"));
                        $pickup_html = '<optgroup value="city" label="City"><option value="city-'.$row['city_id'].'">'.$row['city_name'].'</option></optgroup>';
                    }
                    else if($row_transport['pickup_type'] == 'hotel'){
                        $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='".$row_transport['pickup']."'"));
                        $pickup_html = '<optgroup value="hotel" label="Hotel"><option value="hotel-'.$row['hotel_id'].'">'.$row['hotel_name'].'</option></optgroup>';
                    }
                    else if($row_transport['pickup_type'] == 'airport'){
                        $row = mysqli_fetch_assoc(mysqlQuery("select airport_id,airport_name,airport_code from airport_master where airport_id='".$row_transport['pickup']."'"));
                        $pickup_html = '<optgroup value="airport" label="Airport"><option value="airport-'.$row['airport_id'].'">'.$row['airport_name'].' ('.$row['airport_code'].')</option></optgroup>';
                    }
                    
                    // Get Drop Location
                    $drop_html = '';
                    if($row_transport['drop_type'] == 'city'){
                        $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='".$row_transport['drop_location']."'"));
                        $drop_html = '<optgroup value="city" label="City"><option value="city-'.$row['city_id'].'">'.$row['city_name'].'</option></optgroup>';
                    }
                    else if($row_transport['drop_type'] == 'hotel'){
                        $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='".$row_transport['drop_location']."'"));
                        $drop_html = '<optgroup value="hotel" label="Hotel"><option value="hotel-'.$row['hotel_id'].'">'.$row['hotel_name'].'</option></optgroup>';
                    }
                    else if($row_transport['drop_type'] == 'airport'){
                        $row = mysqli_fetch_assoc(mysqlQuery("select airport_id,airport_name,airport_code from airport_master where airport_id='".$row_transport['drop_location']."'"));
                        $drop_html = '<optgroup value="airport" label="Airport"><option value="airport-'.$row['airport_id'].'">'.$row['airport_name'].' ('.$row['airport_code'].')</option></optgroup>';
                    }
                ?>
                <tr>
                    <td><input class="css-checkbox" id="chk_transport<?= $count ?>_u" type="checkbox" checked><label class="css-label" for="chk_transport<?= $count ?>_u"> </label></td>
                    <td><input maxlength="15" value="<?= $count ?>" type="text" name="no" placeholder="Sr. No." class="form-control" disabled /></td>
                    <td class="col-md-2">
                        <select name="transport_vehicle_name<?= $count ?>_u" id="transport_vehicle_name<?= $count ?>_u" title="Select Vehicle" style="width:200px" class="form-control app_select2">
                            <option value="<?= $row_transport['vehicle_name'] ?>"><?= $vehicle_name ?></option>
                            <?php
                            $sq_query = mysqlQuery("select * from b2b_transfer_master where status != 'Inactive'");
                            while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>
                                <option value="<?php echo $row_dest['entry_id']; ?>">
                                    <?php echo $row_dest['vehicle_name']; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td><input type="text" id="transport_start_date<?= $count ?>_u" name="transport_start_date<?= $count ?>_u" placeholder="Start Date" title="Start Date" class="app_datepicker form-control" style="width:150px" value="<?= get_date_user($row_transport['start_date']) ?>" onchange="get_to_date(this.id,'transport_end_date<?= $count ?>_u');"></td>
                    <td><input type="text" id="transport_end_date<?= $count ?>_u" name="transport_end_date<?= $count ?>_u" placeholder="End Date" title="End Date" class="app_datepicker form-control" style="width:150px" value="<?= get_date_user($row_transport['end_date']) ?>" onchange="validate_validDate('transport_start_date<?= $count ?>_u','transport_end_date<?= $count ?>_u');"></td>
                    <td class="col-md-2">
                        <select name="transport_pickup_from<?= $count ?>_u" id="transport_pickup_from<?= $count ?>_u" style="width:250px;" title="Pickup Location" class="form-control app_minselect2 transport_pickup_u">
                            <?php echo $pickup_html; ?>
                        </select>
                    </td>
                    <td class="col-md-2">
                        <select name="transport_drop_to<?= $count ?>_u" id="transport_drop_to<?= $count ?>_u" style="width:250px;" title="Drop-off Location" class="form-control app_minselect2 transport_drop_u">
                            <?php echo $drop_html; ?>
                        </select>
                    </td>
                    <td>
                        <select name="transport_service_duration<?= $count ?>_u" id="transport_service_duration<?= $count ?>_u" style="width:170px;" title="Service Duration" class="form-control app_select2">
                            <option value="<?= $row_transport['service_duration'] ?>"><?= $row_transport['service_duration'] ?></option>
                            <?php echo get_service_duration_dropdown(); ?>
                        </select>
                    </td>
                    <td><input type="text" id="transport_no_vehicles<?= $count ?>_u" name="transport_no_vehicles<?= $count ?>_u" placeholder="No.Of vehicles" title="No.Of vehicles" class="form-control" style="width:150px" value="<?= $row_transport['vehicle_count'] ?>"></td>
                    <td class="hidden"><input type="text" id="transport_entry_id<?= $count ?>_u" name="transport_entry_id<?= $count ?>_u" value="<?= $row_transport['id'] ?>"></td>
                </tr>
                <?php
                    $count++;
                }
                
                // If no transport data, show empty row
                if($count == 1){
                ?>
                <tr>
                    <td><input class="css-checkbox" id="chk_transport1_u" type="checkbox" checked><label class="css-label" for="chk_transport1_u"> </label></td>
                    <td><input maxlength="15" value="1" type="text" name="username" placeholder="Sr No." class="form-control" disabled autocomplete="off"></td>
                    <td class="col-md-2">
                        <select name="transport_vehicle_name1_u" id="transport_vehicle_name1_u" title="Select Vehicle" style="width:200px" class="form-control app_select2">
                            <option value="">Select Vehicle</option>
                            <?php
                            $sq_query = mysqlQuery("select * from b2b_transfer_master where status != 'Inactive'");
                            while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>
                                <option value="<?php echo $row_dest['entry_id']; ?>">
                                    <?php echo $row_dest['vehicle_name']; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td><input type="text" id="transport_start_date1_u" name="transport_start_date1_u" placeholder="Start Date" title="Start Date" class="app_datepicker form-control" style="width:150px" value="<?= date('d-m-Y') ?>" onchange="get_to_date(this.id,'transport_end_date1_u');"></td>
                    <td><input type="text" id="transport_end_date1_u" name="transport_end_date1_u" placeholder="End Date" title="End Date" class="app_datepicker form-control" style="width:150px" value="<?= date('d-m-Y') ?>" onchange="validate_validDate('transport_start_date1_u','transport_end_date1_u');"></td>
                    <td class="col-md-2">
                        <select name="transport_pickup_from1_u" id="transport_pickup_from1_u" style="width:250px;" title="Pickup Location" class="form-control app_minselect2">
                        </select>
                    </td>
                    <td class="col-md-2">
                        <select name="transport_drop_to1_u" id="transport_drop_to1_u" style="width:250px;" title="Drop-off Location" class="form-control app_minselect2">
                        </select>
                    </td>
                    <td>
                        <select name="transport_service_duration1_u" id="transport_service_duration1_u" style="width:170px;" title="Service Duration" class="form-control app_select2">
                            <option value="">Service Duration</option>
                            <?php echo get_service_duration_dropdown(); ?>
                        </select>
                    </td>
                    <td><input type="text" id="transport_no_vehicles1_u" name="transport_no_vehicles1_u" placeholder="No.Of vehicles" title="No.Of vehicles" class="form-control" style="width:150px"></td>
                    <td class="hidden"><input type="text" id="transport_entry_id1_u" name="transport_entry_id1_u" value=""></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Initialize ALL dropdowns with AJAX (preserves existing selected options)
    $('select[name^="transport_pickup_from"]').each(function(){
        destinationLoading($(this), 'Pickup Location');
    });
    $('select[name^="transport_drop_to"]').each(function(){
        destinationLoading($(this), 'Drop-off Location');
    });
    
    // Initialize datepicker for transport date fields
    setTimeout(function(){
        $('#tbl_booking_transport_u').find('.app_datepicker').datetimepicker({ 
            timepicker: false, 
            format: 'd-m-Y' 
        });
    }, 200);
});
</script>

