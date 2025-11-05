<div class="row mg_tp_10 mg_bt_10">
    <div class="col-xs-6 mg_bt_20_sm_xs">
        <button type="button" class="btn btn-excel btn-sm" title="Add Vehicle" onclick="vehicle_save_modal('transport_vehicle_name1')"><i class="fa fa-plus"></i></button>
    </div>
    <div class="col-xs-6 text-right mg_bt_20_sm_xs">
        <button type="button" class="btn btn-excel btn-sm" onClick="addTransportRowBooking();" title="Add Row"><i class="fa fa-plus"></i></button>
        <button type="button" class="btn btn-pdf btn-sm" onClick="deleteRow('tbl_booking_transport')" title="Delete Row"><i class="fa fa-trash"></i></button>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
            <table id="tbl_booking_transport" name="tbl_booking_transport" class="table table-bordered no-marg pd_bt_51">
                <tr>
                    <td><input class="css-checkbox" id="chk_transport1" type="checkbox" checked><label class="css-label" for="chk_transport1"> </label></td>
                    <td><input maxlength="15" value="1" type="text" name="username" placeholder="Sr No." class="form-control" disabled autocomplete="off"></td>
                    <td class="col-md-2">
                        <select name="transport_vehicle_name1" id="transport_vehicle_name1" title="Select Vehicle" style="width:200px" class="form-control app_select2">
                            <option value="">Select Vehicle</option>
                            <?php
                            $sq_query = mysqlQuery("select * from b2b_transfer_master where status != 'Inactive'");
                            while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>
                                <option value="<?php echo $row_dest['entry_id']; ?>">
                                    <?php echo $row_dest['vehicle_name']; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td><input type="text" id="transport_start_date1" name="transport_start_date1" placeholder="Start Date" title="Start Date" class="app_datepicker form-control" style="width:150px" value="<?= date('d-m-Y') ?>" onchange="get_to_date(this.id,'transport_end_date1');"></td>
                    <td><input type="text" id="transport_end_date1" name="transport_end_date1" placeholder="End Date" title="End Date" class="app_datepicker form-control" style="width:150px" value="<?= date('d-m-Y') ?>" onchange="validate_validDate('transport_start_date1','transport_end_date1');"></td>
                    <td class="col-md-2">
                        <select name="transport_pickup_from1" id="transport_pickup_from1" style="width:250px;" title="Pickup Location" class="form-control app_minselect2">
                        </select>
                    </td>
                    <td class="col-md-2">
                        <select name="transport_drop_to1" id="transport_drop_to1" style="width:250px;" title="Drop-off Location" class="form-control app_minselect2">
                        </select>
                    </td>
                    <td>
                        <select name="transport_service_duration1" id="transport_service_duration1" style="width:170px;" title="Service Duration" class="form-control app_select2">
                            <option value="">Service Duration</option>
                            <?php echo get_service_duration_dropdown(); ?>
                        </select>
                    </td>
                    <td><input type="text" id="transport_no_vehicles1" name="transport_no_vehicles1" placeholder="No.Of vehicles" title="No.Of vehicles" class="form-control" style="width:150px"></td>
                </tr>
            </table>
        </div>
    </div>
</div>

