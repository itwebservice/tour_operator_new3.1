<div class="row mg_bt_10">
    <div class="col-xs-12 text-right">
        <button type="button" class="btn btn-excel btn-sm" onClick="addRow('tbl_plane_travel_details_dynamic_row');event_airport('tbl_plane_travel_details_dynamic_row',4,5)" title="Add row"><i class="fa fa-plus"></i></button>
        <!--  Code to uploadf button -->
        <div class="div-upload" id="div_upload_button">
            <div id="package_plane_upload" class="upload-button"><span>Ticket</span></div><span id="package_plane_status" ></span>
            <ul id="files" ></ul>
            <input type="hidden" id="txt_plane_upload_dir" name="txt_plane_upload_dir" value="<?= $sq_booking_info['plane_upload_ticket'] ?>">
        </div>
        <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note :Only JPG, PNG or PDF files are allowed"><i class="fa fa-question-circle"></i></button>
    </div>
</div>   

<div class="row mg_bt_30"> <div class="col-xs-12"> <div class="table-responsive">
                        
    <table id="tbl_plane_travel_details_dynamic_row" name="tbl_plane_travel_details_dynamic_row" class="table table-bordered table-hover pd_bt_51 no-marg" style="width: 1400px;">
    <?php
     $sq_plane_info_count = mysqli_num_rows(mysqlQuery("select * from package_plane_master where booking_id='$booking_id'"));
    if($sq_plane_info_count==0)
    { ?>
        <tr>
        <td ><input id="check-btn-plane-1" type="checkbox" onchange="calculate_plane_expense('tbl_plane_travel_details_dynamic_row',true)" checked ></td>
        <td><input maxlength="15" type="text" id="" name="username" value="1" placeholder="Sr.No." disabled/></td>
        <td><input type="text" id="txt_plane_date-1" name="txt_plane_date-1" title="Departure Date & Time" onchange="get_to_datetime(this.id,'txt_arravl-1')" placeholder="Departure Date & Time"/></td>
        <td><input type="text" id="txt_arravl-1" name="txt_arravl-1" class="app_datetimepicker" onchange="validate_arrivalDate('txt_plane_date-1','txt_arravl-1')" placeholder="Arrival Date & Time" title="Arrival Date & Time"></td>
        <!-- <td><input type="text" name="from_sector-1" id="from_sector-1" style="width:300px" placeholder="*From Sector" title="From Sector">
		</td>
		<td><input type="text" name="to_sector-1" id="to_sector-1" style="width:300px" placeholder="*To Sector" title="To Sector">
		</td> -->

        <td style="min-width:300px;"><select name="from_sector-1" id="from_sector-1"
                                                                    class="form-control app_select2 "
                                                                    data-sector-type="from" title="From Sector"
                                                                     data-add-new-option="true" style="width:100%">
                                                                    <option value="">*From Sector</option>
                                                                </select>
                                                            </td>
                                                            <td style="min-width:300px;"><select name="to_sector-1" id="to_sector-1"
                                                                    class="form-control app_select2 "
                                                                    data-sector-type="to" title="To Sector"
                                                                     data-add-new-option="true" style="width:100%">
                                                                    <option value="">*To Sector</option>
                                                                </select>
                                                            </td>

        <td><select id="txt_plane_company-1" name="txt_plane_company-1" class="app_select2" style="width:150px" title="Airline Name" data-add-new-option="true">
            <option value="">*Airline Name</option>
            <?php get_airline_name_dropdown(); ?>
        </select></td>
        <td><select name="plane_class-1" id="plane_class-1" title="Class" style="width: 200px;">
                <?php get_flight_class_dropdown(); ?>
            </select></td>
        <td><input type="text" id="txt_plane_seats-1" name="txt_plane_seats-1" placeholder="Total Seats" title="Total Seats" maxlength="2" style="width: 110px;"  /></td>
        <td><input type="text" id="txt_plane_amount-1" name="txt_plane_amount-1" placeholder="*Amount" onchange="validate_balance(this.id)" title="Amount" onchange=" calculate_plane_expense('tbl_plane_travel_details_dynamic_row',true);"  style="width: 130px;"/></td>
        <td><input type="hidden" id="from_city-1"> </td>
		<td><input type="hidden" id="to_city-1"></td>
        </tr>
        <script type="text/javascript">
        $('#txt_plane_date-1,#txt_arravl-1').datetimepicker({ format:'d-m-Y H:i' });
        </script>
 <?php    }
    else{
    $offset = "_u";
    $count = 0;
    $sq_plane_details = mysqlQuery("select * from package_plane_master where booking_id='$booking_id'");
    while($row_plane_details = mysqli_fetch_assoc($sq_plane_details))
    {                            
        $sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id=".$row_plane_details['from_city']));
        $sq_city2 = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id=".$row_plane_details['to_city']));
        $from_sector = '';
        if ($row_plane_details['from_location']) {
            $from_sector = ($sq_city['city_name']) ? $sq_city['city_name'] . ' - ' . $row_plane_details['from_location'] : $row_plane_details['from_location'];
        }
        $to_sector = '';
        if ($row_plane_details['to_location']) {
            $to_sector = ($sq_city2['city_name']) ? $sq_city2['city_name'] . ' - ' . $row_plane_details['to_location'] : $row_plane_details['to_location'];
        }
        $count++;
    ?>

        <tr>

            <td ><input id="check-btn-plane-<?= $offset.$count ?>_d" type="checkbox" onchange="calculate_plane_expense('tbl_plane_travel_details_dynamic_row',true)" checked disabled ></td>

            <td><input maxlength="15" type="text" id="" name="username" value="<?php echo $count ?>" placeholder="Sr.No." disabled/></td>

            <td><input type="text" id="txt_plane_date-<?= $offset.$count ?>_d" name="txt_plane_date-<?= $offset.$count ?>_d ?>" placeholder="Departure Date" title="Departure Date & Time" onchange="get_to_datetime(this.id,'txt_arravl-<?= $offset.$count ?>_d')" value="<?php echo date("d-m-Y H:i", strtotime($row_plane_details['date'])) ?>" style="width: 152px;/"></td>
            <td><input type="text" id="txt_arravl-<?= $offset.$count ?>_d" name="txt_arravl-<?= $offset.$count ?>_d" placeholder="Arrival date & time" style="width:200px" title="Arrival date & time" onchange="validate_validDatetime('txt_plane_date-<?= $offset.$count ?>_d' , this.id)" class="app_datetimepicker" value="<?php echo date("d-m-Y H:i", strtotime($row_plane_details['arraval_time'])) ?>"/></td>

            <td style="min-width:360px;"><select name="from_sector-<?= $offset.$count ?>_d"
id="from_sector-<?= $offset.$count ?>_d"
                                                                    class="form-control app_select2 plane-airport-select"
                                                                    data-sector-type="from" title="From Sector"
                                                                     data-add-new-option="true" style="width:100%">
                                                                    <option value="">*From Sector</option>
                                                                    <?php if ($from_sector != '') { ?>
                                                                    <option value="<?= htmlspecialchars($from_sector, ENT_QUOTES) ?>" selected><?= htmlspecialchars($from_sector, ENT_QUOTES) ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </td>
                                                            <td style="min-width:360px;"><select name="to_sector-<?= $offset.$count ?>_d"
id="to_sector-<?= $offset.$count ?>_d"
                                                                    class="form-control app_select2 plane-airport-select"
                                                                    data-sector-type="to" title="To Sector"
                                                                     data-add-new-option="true" style="width:100%">
                                                                    <option value="">*To Sector</option>
                                                                    <?php if ($to_sector != '') { ?>
                                                                    <option value="<?= htmlspecialchars($to_sector, ENT_QUOTES) ?>" selected><?= htmlspecialchars($to_sector, ENT_QUOTES) ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </td>

            <td><select id="txt_plane_company-<?= $offset.$count ?>_d" name="txt_plane_company-<?= $offset.$count ?>_d" class="app_select2" style="width:150px" data-add-new-option="true">
                <?php 
                 $sq_airline = mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$row_plane_details[company]'"));?>
                <option value="<?php echo $sq_airline['airline_id'] ?>"><?php echo $sq_airline['airline_name'].' ('.$sq_airline['airline_code'].')' ?></option>
                <?php get_airline_name_dropdown(); ?>
            </select></td>
            <td><select name="plane_class-<?= $offset.$count ?>_d" id="plane_class-<?= $offset.$count ?>_d" title="Class" style="width: 200px;">
                <option value="<?php echo $row_plane_details['class'] ?>"><?php echo $row_plane_details['class'] ?></option>
                <?php get_flight_class_dropdown(); ?>
            </select></td>
            <td><input type="text" id="txt_plane_seats-<?= $offset.$count ?>_d" name="txt_plane_seats-<?= $offset.$count ?>_d" placeholder="Total Seats" title="Total Seats"  maxlength="2" onchange="validate_balance(this.id);"  value="<?php echo $row_plane_details['seats'] ?>" style="width: 110px;"/></td>
            <td><input type="text" id="txt_plane_amount-<?= $offset.$count ?>_d" name="txt_plane_amount-<?= $offset.$count ?>_d" placeholder="Amount" title="Amount" onchange="validate_balance(this.id);calculate_plane_expense('tbl_plane_travel_details_dynamic_row',true);" style="width: 130px;" value="<?php echo $row_plane_details['amount'] ?>"/></td>
            <td><input type="hidden" id="from_city-<?= $offset.$count ?>_d" value="<?= $row_plane_details['from_city'] ?>"></td>
        	<td><input type="hidden" id="to_city-<?= $offset.$count ?>_d" value="<?= $row_plane_details['to_city'] ?>"></td>
            <td><input type="hidden" value="<?php echo $row_plane_details['plane_id'] ?>"></td>
        </tr>
        <script>
            $('#txt_arravl-<?= $offset.$count ?>_d, #txt_plane_date-<?= $offset.$count ?>_d').datetimepicker({ format:'d-m-Y H:i' });
        </script>
    <?php }
    } ?>
    </table>
    <input type = "hidden" id="txt_plane_date_generate" value="<?php echo $count ?>">
</div>  </div> </div>
    <div class="row hidden">
        <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
            <label>Subtotal</label>
            <input type="text" id="txt_plane_expense" name="txt_plane_expense"  class="text-right" value="<?php echo $sq_booking_info['plane_expense'] ?>" placeholder="Subtotal" title="Subtotal" disabled />
        </div>
        <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
            <label>Service Charge</label>
            <input type="text" id="txt_plane_service_charge" name="txt_plane_service_charge"  class="text-right" value="<?php echo $sq_booking_info['plane_service_charge'] ?>"placeholder="Service Charge" title="Service Charge" onchange="validate_balance(this.id); calculate_total_plane_expense()" />            
        </div>
        <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
            <label>Tax</label>
            <select name="plane_taxation_id" id="plane_taxation_id" onchange="generic_tax_reflect(this.id, 'plane_service_tax', 'calculate_total_plane_expense');">
            </select>
            <input type="hidden" id="plane_service_tax" name="plane_service_tax" value="<?= $sq_booking_info['plane_service_tax'] ?>">            
        </div>
        <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
            <label>Tax Amount</label>
            <input type="text" id="plane_service_tax_subtotal" name="plane_service_tax_subtotal" value="<?= $sq_booking_info['plane_service_tax_subtotal'] ?>" title="Tax Amount" disabled>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
            <label>Total</label>
            <input type="text" id="txt_plane_total_expense" name="txt_plane_total_expense" value="<?php echo $sq_booking_info['total_plane_expense'] ?>" placeholder="total expense" title="Total expense" disabled />
        </div>
    </div>    

<script>
function init_package_booking_update_plane_rows() {
    if (typeof initPlaneAirportSelect2 === 'function') {
        initPlaneAirportSelect2('#tbl_plane_travel_details_dynamic_row');
    }
    $('#tbl_plane_travel_details_dynamic_row select[id^="from_sector-"], #tbl_plane_travel_details_dynamic_row select[id^="to_sector-"]').each(function () {
        var $sel = $(this);
        var sectorId = $sel.attr('id') || '';
        var selectedVal = $sel.find('option:selected').val() || $sel.val();
        var selectedText = $sel.find('option:selected').text() || selectedVal;
        if (selectedVal && selectedText) {
            if (!$sel.find('option[value="' + selectedVal.replace(/"/g, '\\"') + '"]').length) {
                $sel.append(new Option(selectedText, selectedVal, true, true));
            }
            $sel.val(selectedVal);
        }
        var suffix = sectorId.replace(/^from_sector-/, '').replace(/^to_sector-/, '');
        var cityId = '';
        if (sectorId.indexOf('from_sector') === 0) {
            cityId = $('#from_city-' + suffix).val();
        } else if (sectorId.indexOf('to_sector') === 0) {
            cityId = $('#to_city-' + suffix).val();
        }
        if (cityId && typeof syncSectorCityHidden === 'function') {
            syncSectorCityHidden(sectorId, cityId);
        }
    });
    if (typeof initAllAirlineSelectAddNew === 'function') {
        initAllAirlineSelectAddNew('#tbl_plane_travel_details_dynamic_row');
    }
    $('[id^="txt_plane_date-"], [id^="txt_arravl-"]').each(function () {
        if (!$(this).data('xdsoft_datetimepicker')) {
            $(this).datetimepicker({ format: 'd-m-Y H:i' });
        }
    });
    if (typeof calculate_plane_expense === 'function') {
        calculate_plane_expense('tbl_plane_travel_details_dynamic_row', true);
    }
}
event_airport('tbl_plane_travel_details_dynamic_row', 4, 5);
$(document).ready(function () {
    init_package_booking_update_plane_rows();
});
</script>