<?php
/**
 * Group Quotation Update — Flight rows.
 * Prefer saved quotation plane entries; if none, fall back to tour-master flights
 * (same source used when creating the quotation).
 */
if (!function_exists('gq_update_build_plane_sector')) {
	function gq_update_build_plane_sector($city_name, $location)
	{
		$city_name = trim((string) $city_name);
		$location = trim((string) $location);
		if ($location === '' && $city_name === '') {
			return '';
		}
		if ($location === '') {
			return $city_name;
		}
		if ($city_name === '') {
			return $location;
		}
		// from_location may already be stored as "City - Airport"
		if (stripos($location, $city_name) === 0) {
			return $location;
		}
		return $city_name . ' - ' . $location;
	}
}

$gq_plane_rows = array();
$sq_q_plane = mysqlQuery("select * from group_tour_quotation_plane_entries where quotation_id='$quotation_id'");
while ($row_q_plane = mysqli_fetch_assoc($sq_q_plane)) {
	$row_q_plane['_source'] = 'quotation';
	$gq_plane_rows[] = $row_q_plane;
}

// Fallback: load flights from tour master when quotation has no saved plane rows
if (count($gq_plane_rows) === 0) {
	$tour_id_for_plane = isset($sq_quotation['tour_group_id']) ? intval($sq_quotation['tour_group_id']) : 0;
	if ($tour_id_for_plane <= 0 && !empty($sq_quotation['tour_group'])) {
		$sq_tg = mysqli_fetch_assoc(mysqlQuery("select tour_id from tour_groups where group_id='" . intval($sq_quotation['tour_group']) . "'"));
		$tour_id_for_plane = isset($sq_tg['tour_id']) ? intval($sq_tg['tour_id']) : 0;
	}
	if ($tour_id_for_plane > 0) {
		$sq_m_plane = mysqlQuery("select * from group_tour_plane_entries where tour_id='$tour_id_for_plane'");
		while ($row_m_plane = mysqli_fetch_assoc($sq_m_plane)) {
			$row_m_plane['_source'] = 'master';
			$row_m_plane['id'] = '';
			$gq_plane_rows[] = $row_m_plane;
		}
	}
}

$travel_from_dt = (!empty($sq_quotation['from_date']) && $sq_quotation['from_date'] != '0000-00-00')
	? (date('d-m-Y', strtotime($sq_quotation['from_date'])) . ' 00:00')
	: date('d-m-Y H:i');
$travel_to_dt = (!empty($sq_quotation['to_date']) && $sq_quotation['to_date'] != '0000-00-00')
	? (date('d-m-Y', strtotime($sq_quotation['to_date'])) . ' 00:00')
	: $travel_from_dt;
?>
<div class="row">
    <div class="col-xs-12 text-right mg_bt_10_sm_xs">
        <button type="button" class="btn btn-excel btn-sm" title="Add Row" onClick="addRow('tbl_package_tour_quotation_dynamic_plane_update');event_airport('tbl_package_tour_quotation_dynamic_plane_update');if(typeof initAllAirlineSelectAddNew==='function'){initAllAirlineSelectAddNew('#tbl_package_tour_quotation_dynamic_plane_update');}"><i class="fa fa-plus"></i></button>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
        <table id="tbl_package_tour_quotation_dynamic_plane_update" name="tbl_package_tour_quotation_dynamic_plane_update" class="table table-bordered pd_bt_51">
			<?php
			if (count($gq_plane_rows) === 0) {
				?>
				<tr>
	                <td><input class="css-checkbox" id="chk_plan-1" type="checkbox"><label class="css-label" for="chk_plan-1"> </label></td>
	                <td><input maxlength="15" value="1" type="text" name="username" placeholder="Sr. No." class="form-control" disabled /></td>
					<td class="sector-select">
						<select name="from_sector-1" id="from_sector-1" class="form-control app_select2 plane-airport-select" data-sector-type="from" title="From Sector" style="width: 100%;" data-add-new-option="true">
							<option value="">*From Sector</option>
						</select>
					</td>
					<td class="sector-select">
						<select name="to_sector-1" id="to_sector-1" class="form-control app_select2 plane-airport-select" data-sector-type="to" title="To Sector" style="width: 100%;" data-add-new-option="true">
							<option value="">*To Sector</option>
						</select>
					</td>
		            <td><select id="airline_name-1" class="app_select2 form-control" title="Airline Name" name="airline_name-1" style="width: 100%;">
			                <option value="">Airline Name</option>
			                <?php get_airline_name_dropdown(); ?>
			            </select>
	                </td>
		            <td><select name="plane_class-1" id="plane_class-1" title="Class" style="width: 180px!important;">
                        <?php get_flight_class_dropdown(); ?>
						</select></td>
		            <td><input type="text" id="txt_dapart-1" name="txt_dapart-1" class="app_datetimepicker" placeholder="Departure Date and Time" title="Departure Date and Time" style="width: 100%;" value="<?= htmlspecialchars($travel_from_dt) ?>" onchange="get_to_datetime(this.id,'txt_arrval-1')" /></td>
		            <td><input type="text" id="txt_arrval-1" name="txt_arrval-1" style="width: 100%;" class="app_datetimepicker" placeholder="Arrival Date Time" title="Arrival Date Time" value="<?= htmlspecialchars($travel_to_dt) ?>" onchange="validate_validDatetime('txt_dapart-1',this.id)"/></td>
					<td><input type="hidden" id="from_city-1"> </td>
					<td><input type="hidden" id="to_city-1"></td>
		        </tr>
				<?php
			} else {
				$count = 0;
				foreach ($gq_plane_rows as $row_q_plane) {
					$count++;
					$from_city_id = intval($row_q_plane['from_city']);
					$to_city_id = intval($row_q_plane['to_city']);
					$sq_city = ($from_city_id > 0) ? mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$from_city_id'")) : array('city_name' => '');
					$sq_city2 = ($to_city_id > 0) ? mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$to_city_id'")) : array('city_name' => '');
					$from_sector_val = gq_update_build_plane_sector(isset($sq_city['city_name']) ? $sq_city['city_name'] : '', $row_q_plane['from_location']);
					$to_sector_val = gq_update_build_plane_sector(isset($sq_city2['city_name']) ? $sq_city2['city_name'] : '', $row_q_plane['to_location']);

					$dept_val = $travel_from_dt;
					$arr_val = $travel_to_dt;
					if (!empty($row_q_plane['dapart_time']) && $row_q_plane['dapart_time'] != '0000-00-00 00:00:00' && $row_q_plane['_source'] === 'quotation') {
						$dept_val = date('d-m-Y H:i', strtotime($row_q_plane['dapart_time']));
					}
					if (!empty($row_q_plane['arraval_time']) && $row_q_plane['arraval_time'] != '0000-00-00 00:00:00' && $row_q_plane['_source'] === 'quotation') {
						$arr_val = date('d-m-Y H:i', strtotime($row_q_plane['arraval_time']));
					}

					$airline_id = isset($row_q_plane['airline_name']) ? $row_q_plane['airline_name'] : '';
					$sq_airline = ($airline_id !== '' && $airline_id !== null)
						? mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$airline_id'"))
						: null;
					$plane_entry_id = ($row_q_plane['_source'] === 'quotation' && !empty($row_q_plane['id'])) ? $row_q_plane['id'] : '';
					?>
					<tr>
						<td><input class="css-checkbox" id="chk_plan-<?= $count ?>_d" type="checkbox" checked><label class="css-label" for="chk_plan-<?= $count ?>_d"> </label></td>
		                <td><input maxlength="15" value="<?= $count ?>" type="text" name="username" placeholder="Sr. No." class="form-control" disabled /></td>
		                <td class="sector-select">
							<select name="from_sector-<?= $count ?>_d" id="from_sector-<?= $count ?>_d" class="form-control app_select2 plane-airport-select" data-sector-type="from" title="From Sector" style="width: 220px;" data-add-new-option="true">
								<?php if ($from_sector_val != '') { ?>
									<option value="<?= htmlspecialchars($from_sector_val, ENT_QUOTES) ?>" selected><?= htmlspecialchars($from_sector_val) ?></option>
								<?php } ?>
								<option value="">*From Sector</option>
							</select>
						</td>
						<td class="sector-select">
							<select name="to_sector-<?= $count ?>_d" id="to_sector-<?= $count ?>_d" class="form-control app_select2 plane-airport-select" data-sector-type="to" title="To Sector" style="width: 220px;" data-add-new-option="true">
								<?php if ($to_sector_val != '') { ?>
									<option value="<?= htmlspecialchars($to_sector_val, ENT_QUOTES) ?>" selected><?= htmlspecialchars($to_sector_val) ?></option>
								<?php } ?>
								<option value="">*To Sector</option>
							</select>
						</td>
		                 <td><select id="airline_name-<?= $count ?>_d" class="app_select2 form-control" name="airline_name-<?= $count ?>_d" style="width: 160px!important;">
		                 	<?php if ($sq_airline && !empty($sq_airline['airline_id'])) { ?>
			                <option value="<?= htmlspecialchars($sq_airline['airline_id']) ?>" selected><?= htmlspecialchars($sq_airline['airline_name'] . ' (' . $sq_airline['airline_code'] . ')') ?></option>
			                      <?php } ?>
			                      <option value="">Airline Name</option>
			                      <?php get_airline_name_dropdown(); ?>
			            </select></td>
			            <td><select name="plane_class-<?= $count ?>_d" id="plane_class-<?= $count ?>_d" title="Class" style="width: 180px!important;">
			            		<?php if (!empty($row_q_plane['class'])) { ?>
			            		<option value="<?= htmlspecialchars($row_q_plane['class']) ?>" selected><?= htmlspecialchars($row_q_plane['class']) ?></option>
			            		<?php } ?>
				            	<option value="">Class</option>
				            	<option value="Economy">Economy</option>
			                    <option value="Premium Economy">Premium Economy</option>
			                    <option value="Business">Business</option>
			                    <option value="First Class">First Class</option>
				            </select></td>
			            <td><input type="text" id="txt_dapart-<?= $count ?>_d" name="txt_dapart-<?= $count ?>_d" class="app_datetimepicker" placeholder="Departure Date and Time" title="Departure Date and Time" value="<?= htmlspecialchars($dept_val) ?>" onchange="get_to_datetime(this.id,'txt_arrval-<?= $count ?>_d')" style="width: 180px;" /></td>
			            <td><input type="text" id="txt_arrval-<?= $count ?>_d" name="txt_arrval-<?= $count ?>_d" class="app_datetimepicker" placeholder="Arrival Date and Time" title="Arrival Date and Time" value="<?= htmlspecialchars($arr_val) ?>" style="width: 180px;" onchange="validate_validDatetime('txt_dapart-<?= $count ?>_d',this.id)"/></td>
						<td><input type="hidden" id="from_city-<?= $count ?>_d" value="<?= $from_city_id ?>"></td>
						<td><input type="hidden" id="to_city-<?= $count ?>_d" value="<?= $to_city_id ?>"></td>
			            <td><input type="hidden" value="<?= htmlspecialchars($plane_entry_id) ?>"></td>
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
	(function () {
		function initGroupQuotationUpdatePlanes() {
			var tableId = 'tbl_package_tour_quotation_dynamic_plane_update';
			if (!document.getElementById(tableId)) {
				return;
			}
			$('#tbl_package_tour_quotation_dynamic_plane_update .app_datetimepicker').datetimepicker({ format: 'd-m-Y H:i' });
			if (typeof event_airport === 'function') {
				event_airport(tableId);
			}
			if (typeof initAllAirlineSelectAddNew === 'function') {
				initAllAirlineSelectAddNew('#' + tableId);
			} else {
				$('#tbl_package_tour_quotation_dynamic_plane_update select[id^="airline_name-"]').select2();
			}
		}
		if (window.jQuery) {
			$(document).ready(function () {
				setTimeout(initGroupQuotationUpdatePlanes, 50);
			});
		} else {
			setTimeout(initGroupQuotationUpdatePlanes, 100);
		}
	})();
	$.fn.modal.Constructor.prototype.enforceFocus = function () {};
</script>
