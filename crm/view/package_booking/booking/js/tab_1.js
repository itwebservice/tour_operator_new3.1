$(
	'#txt_package_from_date,#txt_package_to_date,#txt_m_passport_issue_date1,#txt_m_passport_expiry_date1'
).datetimepicker({
	timepicker: false,
	format: 'd-m-Y'
});
var date = new Date();
var yest = date.setDate(date.getDate() - 1);

$('#m_birthdate1').datetimepicker({ timepicker: false, maxDate: yest, format: 'd-m-Y' });

function formatQuotationOption(data) {
	if (!data.id) {
		return data.text;
	}
	var $option = $(data.element);
	var bg = $option.data('bg') || $option.attr('data-bg');
	if (bg) {
		return $('<span style="display:block;padding:3px 6px;background-color:' + bg + ';">' + data.text + '</span>');
	}
	return data.text;
}

$('#customer_id_p, #country_name,#currency_code').select2();
$('#quotation_id').select2({
	templateResult: formatQuotationOption,
	templateSelection: formatQuotationOption
});

function total_days_reflect(offset = '') {
	var from_date = $('#txt_package_from_date' + offset).val();
	var to_date = $('#txt_package_to_date' + offset).val();
	if(from_date != '' && to_date != ''){
		var edate = from_date.split('-');
		e_date = new Date(edate[2], edate[1] - 1, edate[0]).getTime();
		var edate1 = to_date.split('-');
		e_date1 = new Date(edate1[2], edate1[1] - 1, edate1[0]).getTime();

		var one_day = 1000 * 60 * 60 * 24;

		var from_date_ms = new Date(e_date).getTime();
		var to_date_ms = new Date(e_date1).getTime();

		var difference_ms = to_date_ms - from_date_ms;
		var total_days = Math.round(Math.abs(difference_ms) / one_day);

		total_days = parseFloat(total_days) + 1;

		$('#txt_tour_total_days' + offset).val(total_days);
	}else{
		$('#txt_tour_total_days' + offset).val(0);
	}
}

//////////////////Due date reflect start/////////////////////////////
function due_date_reflect() {
	var text = $('#txt_package_from_date').val();
	var date_arr = text.split('-');

	var d = new Date();
	d.setDate(date_arr[0]);
	d.setMonth(date_arr[1] - 1);
	d.setFullYear(date_arr[2]);
	var yesterdayMs = d.getTime() - 1000 * 60 * 60 * 24; // Offset by one day;
	d.setTime(yesterdayMs);
	var month = d.getMonth() + 1;

	var date1 = d.getDate();
	if (date1 <= 9) {
		date1 = '0' + date1;
	}
	if (month <= 9) {
		month = '0' + month;
	}
	var due_date = date1 + '-' + month + '-' + d.getFullYear();
	$('#txt_balance_due_date').val(due_date);
}

//////////////////Due date reflect end////////////////////////
function customer_info_load(div_id, offset = '') {
	var customer_id = $('#' + div_id).val();
	if (customer_id == 'ncust') {
		customer_save_modal();
		return false;
	}
	$.ajax({
		type: 'post',
		url: '../inc/customer_info_load.php',
		dataType: 'json',
		data: { customer_id: customer_id },
		success: function (result) {
			$('#txt_m_mobile_no' + offset).val(result.contact_no);
			$('#txt_m_email_id' + offset).val(result.email_id);
			$('#txt_m_address' + offset).val(result.address);
			$('#txt_contact_person_name' + offset).val(
				result.first_name + ' ' + result.middle_name + ' ' + result.last_name
			);
			$('#txt_m_city' + offset).val(result.city);
			$('#txt_m_state' + offset).val(result.state_name);

			if (result.payment_amount != '' || result.payment_amount != '0') {
				$('#credit_amount' + offset).removeClass('hidden');
				$('#credit_amount' + offset).val(result.payment_amount);
			}
			else {
				$('#credit_amount' + offset).addClass('hidden');
				$('#credit_amount' + offset).val(0);
			}
			if ($('#copy_details1').is(':checked')) {
				copy_details();
			}
		}
	});
}


function customer_users_reflect(type) {

    if(type=='save') {
        var cust_type_id = 'cust_type';
        var cust_type_div = 'users_div';
    }else{
        var cust_type_id = 'cust_type1';
        var cust_type_div = 'users_div_update';
    }
	var base_url = $('#base_url').val();
	var cust_type = $('#'+cust_type_id).val();
	var customer_id = $('#customer_id_u').val();
	$.post(
		base_url + 'view/customer_master/customer_users_reflect.php',
		{ cust_type: cust_type, customer_id: customer_id,type:type },
		function (data) {
			$('#'+cust_type_div).html(data);
		}
	);
}


function state_dropdown_load(country_name, offset = '') {
	var country_name = $('#' + country_name).val();
	$.post('../inc/state_dropdown_load.php', { country_name: country_name }, function (data) {
		$('#txt_m_state' + offset).html(data);
	});
}

function calculate_age_member(id) {
	var dateString1 = $('#' + id).val();
	console.log(dateString1);
	if(dateString1 != '' && dateString1 != undefined){
		var get_new = dateString1.split('-');
		var day = get_new[0];
		var month = get_new[1];
		var year = get_new[2];

		var fromdate = month + '/' + day + '/' + year;

		var todate = new Date();

		var age = [],
			fromdate = new Date(fromdate),
			y = [
				todate.getFullYear(),
				fromdate.getFullYear()
			],
			ydiff = y[0] - y[1],
			m = [
				todate.getMonth(),
				fromdate.getMonth()
			],
			mdiff = m[0] - m[1],
			d = [
				todate.getDate(),
				fromdate.getDate()
			],
			ddiff = d[0] - d[1];

		if (mdiff < 0 || (mdiff === 0 && ddiff < 0)) --ydiff;

		if (ddiff < 0) {
			fromdate.setMonth(m[1] + 1, 0);
			ddiff = fromdate.getDate() - d[1] + d[0];
			--mdiff;
		}
		if (mdiff < 0) mdiff += 12;

		if (ydiff >= 0) {
			age.push(ydiff + 'Y' + ': ');
		}
		if (mdiff >= 0) {
			age.push(mdiff + 'M' + ': ');
		}
		if (ddiff >= 0) {
			age.push(ddiff + 'D');
		}

		if (age.length > 1) age.splice(age.length - 1, 0, ':');
		var age1 = age.join('');
		var count = id.substr(11);
		var id1 = 'txt_m_age' + count;

		document.getElementById(id1).value = age1;

		var dateString2 = $('#' + id).val();
		var today = new Date();
		var birthDate = php_to_js_date_converter(dateString2);
		var millisecondsPerDay = 1000 * 60 * 60 * 24;
		var millisBetween = today.getTime() - birthDate.getTime();
		var days = millisBetween / millisecondsPerDay;

		var count = id.substr(11);
		var adl = '';
		var no_days = Math.floor(days);

		if (no_days <= 730 && no_days > 0) {
			adl = 'Infant';
		}
		if (no_days > 730 && no_days <= 4383) {
			adl = 'Children';
		}
		if (no_days > 4383) {
			adl = 'Adult';
		}
		if (adl === '' && ydiff >= 12) {
			adl = 'Adult';
		}

		$('#txt_m_adolescence' + count).val(adl);
	}
}
function adolescence_reflect(id) {
	var age = $('#' + id).val();
	var count = id.substr(9);
	if (age <= 2 && age > 0) {
		document.getElementById('txt_m_adolescence' + count).value = 'Infant';
	}
	if (age > 2 && age <= 12) {
		document.getElementById('txt_m_adolescence' + count).value = 'Children';
	}
	if (age > 12) {
		document.getElementById('txt_m_adolescence' + count).value = 'Adult';
	}
}

/////////////////////////////////////Site seeing related info start/////////////////////////////////////
$(function () {
	$('#frm_tab_1').validate({
		rules: {
			quotation_id: { required: true },
			txt_package_tour_name: { required: true },
			tour_type: { required: true },
			txt_package_from_date: { required: true },
			txt_package_to_date: { required: true },
			txt_package_to_date: { required: true },
			txt_tour_total_days: { required: true },
			taxation_type: { required: true },
			customer_id_p: { required: true },
			txt_total_required_rooms: { number: true },
			txt_child_with_bed: { number: true },
			txt_child_without_bed: { number: true },
			txt_contact_person_name: { required: true }
		},
		submitHandler: function (form) {
			var quotation_id = $('#quotation_id').val();
			var package_id = $('#package1').val();
			var customer_id = $('#customer_id_p').val();

			var from_date = $('#txt_package_from_date').val();
			var to_date = $('#txt_package_to_date').val();
			// var res = validate_issueDate('txt_package_from_date','txt_package_to_date');
			// if(!res){
			// 	return false;
			// }

			var valid_state = package_tour_booking_tab1_validate();
			if (valid_state == false) {
				return false;
			}
			if (customer_id == '' || customer_id == 0 || customer_id == 'ncust') {
				error_msg_alert('Select Customer!');
				return false;
			}

			if (quotation_id == 0) {
				var table = document.getElementById('package_program_list');
				var rowCount = table.rows.length;
				let checkedRowCount = 0;
				for (var i = 0; i < rowCount; i++) {
					var row = table.rows[i];
					if (row.cells[0].childNodes[0].checked) {
						if (row.cells[3].childNodes[0].value == '') {
							error_msg_alert('Daywise Program is mandatory in row-' + (i + 1) + '<br>');
							return false;
						}
					}
				}
				//Hotel info
				if (package_id == 0) {
					var table = document.getElementById('tbl_package_hotel_infomration');
					for (var i = 0; i < table.rows.length; i++) {
						var row = table.rows[i];
						row.cells[4].childNodes[0].value = from_date;
						row.cells[5].childNodes[0].value = to_date;
					}
				}else{
					///Package hotel info load
					$.ajax({
						type: 'post',
						url: '../inc/package_hotel_info_load.php',
						data: { package_id: package_id,from_date:from_date },
			
						success: function (result) {
							var result1 = JSON.parse(result);
							var hotel_info_arr = result1.hotel_info_arr;
							var table = document.getElementById('tbl_package_hotel_infomration');
							for (var i = 0; i < hotel_info_arr.length; i++) {
								var row = table.rows[i];
								row.cells[4].childNodes[0].value = hotel_info_arr[i]['check_in_date'];
								row.cells[5].childNodes[0].value = hotel_info_arr[i]['check_out_date'];
							}
						}
					});
				}
				//Transport info
				var table = document.getElementById('tbl_package_transport_infomration');
				for (var i = 0; i < table.rows.length; i++) {
					var row = table.rows[i];
					row.cells[3].childNodes[0].value = from_date;
					row.cells[4].childNodes[0].value = to_date;
				}
				//Train info
				var table = document.getElementById('tbl_train_travel_details_dynamic_row');
				for (var i = 0; i < table.rows.length; i++) {
					var row = table.rows[i];
					row.cells[2].childNodes[0].value = from_date + " 00:00";
				}
				//Flight info
				var table = document.getElementById('tbl_plane_travel_details_dynamic_row');
				for (var i = 0; i < table.rows.length; i++) {
					var row = table.rows[i];
					row.cells[2].childNodes[0].value = from_date + " 00:00";
					row.cells[3].childNodes[0].value = from_date + " 00:00";
				}
				//Cruise info
				var table = document.getElementById('tbl_dynamic_cruise_package_booking');
				for (var i = 0; i < table.rows.length; i++) {
					var row = table.rows[i];
					row.cells[2].childNodes[0].value = from_date + " 00:00";
					row.cells[3].childNodes[0].value = from_date + " 00:00";
				}
			}

			//Passenger count for total seats
			var table = document.getElementById('tbl_package_tour_member');
			var rowCount = table.rows.length;
			var pass_count = 0;
			for (var i = 0; i < rowCount; i++) {
				var row = table.rows[i];
				if (row.cells[0].childNodes[0].checked) {
					pass_count++;
				}
			}
			//Train info
			var table = document.getElementById('tbl_train_travel_details_dynamic_row');
			for (var i = 0; i < table.rows.length; i++) {
				var row = table.rows[i];
				row.cells[6].childNodes[0].value = pass_count;
			}
			//Flight info
			var table = document.getElementById('tbl_plane_travel_details_dynamic_row');
			for (var i = 0; i < table.rows.length; i++) {
				var row = table.rows[i];
				row.cells[8].childNodes[0].value = pass_count;
			}
			//Cruise info
			var table = document.getElementById('tbl_dynamic_cruise_package_booking');
			for (var i = 0; i < table.rows.length; i++) {
				var row = table.rows[i];
				row.cells[7].childNodes[0].value = pass_count;
			}
			due_date_reflect();
			get_auto_values('txt_booking_date','total_basic_amt','payment_mode','service_charge','markup','save','true','service_charge','discount_amt');

			$('#tab_1_head').addClass('done');
			$('#tab_2_head').addClass('active');
			$('.bk_tab').removeClass('active');
			$('#tab_2').addClass('active');
			$('html, body').animate({ scrollTop: $('.bk_tab_head').offset().top }, 200);

			return false;
		}
	});
});

/////////////////////////////////////Package Tour Master Tab1 validate start/////////////////////////////////////
function package_tour_booking_tab1_validate() {
	g_validate_status = true;
	var validate_message = '';
	var tour_type = $('#tour_type').val();

	var table = document.getElementById('tbl_package_tour_member');
	var rowCount = table.rows.length;
	var checked_count = 0;
	for (var i = 0; i < rowCount; i++) {
		var row = table.rows[i];
		if (row.cells[0].childNodes[0].checked) {
			checked_count++;
		}
	}
	if (checked_count == 0) {
		error_msg_alert("Atleast one passenger is required!");
		return false;
	}
	for (var i = 0; i < rowCount; i++) {
		var row = table.rows[i];
		if (row.cells[0].childNodes[0].checked) {
			validate_dynamic_empty_fields(row.cells[3].childNodes[0]);
			validate_dynamic_empty_fields(row.cells[8].childNodes[0]);

			if (row.cells[3].childNodes[0].value == '') {
				validate_message += 'Enter traveller first name in row-' + (i + 1) + '<br>';
			}
			if (row.cells[4].childNodes[0].value != '' && !row.cells[4].childNodes[0].value.match(/^[a-zA-Z\s\.]+$/)) {
				validate_message += 'Enter valid middle name in row-' + (i + 1) + '<br>';
			}

			if (
				!row.cells[7].childNodes[0].value.match(/^([0-9]{2})\-([0-9]{2})\-([0-9]{4})$/) &&
				!row.cells[7].childNodes[0].value.match(/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/)
			) {
				validate_message += 'Enter valid birth date in row-' + (i + 1) + '<br>';
			}

			if (row.cells[11].childNodes[0].value != '') {
				if (
					!row.cells[11].childNodes[0].value.match(/^([0-9]{2})\-([0-9]{2})\-([0-9]{4})$/) &&
					!row.cells[11].childNodes[0].value.match(/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/)
				) {
					validate_message += 'Enter valid Issue date in row-' + (i + 1) + '<br>';
				}
			}
			if (row.cells[12].childNodes[0].value != '') {
				if (
					!row.cells[12].childNodes[0].value.match(/^([0-9]{2})\-([0-9]{2})\-([0-9]{4})$/) &&
					!row.cells[12].childNodes[0].value.match(/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/)
				) {
					validate_message += 'Enter valid Expiry date in row-' + (i + 1) + '<br>';
				}
			}

			if (row.cells[8].childNodes[0].value == '') {
				validate_message += 'Enter traveller age in row-' + (i + 1) + '<br>';
			}
		}
	}

	if (validate_message != '') {
		error_msg_alert(validate_message, 10000);
		return false;
	}

	if (g_validate_status == false) {
		return false;
	}
}
/////////////////////////////////////Package Tour Master Tab1 validate end/////////////////////////////////////
function taxes_reflect(tax_id, tax) {
	var base_url = $('#base_url').val();
	$.post(base_url + 'model/app_settings/tax_reflect.php', { tax_id: tax_id, tax: tax }, function (data) {
		console.log(data);
		$('#tour_taxation_id').html(data);
	});
}

function format_booking_datetime(value) {
	if (!value) {
		return '';
	}
	value = String(value).trim();
	if (/^\d{2}-\d{2}-\d{4}$/.test(value)) {
		return value + ' 00:00';
	}
	if (/^\d{2}-\d{2}-\d{4}\s+\d{1,2}:\d{2}$/.test(value)) {
		var parts = value.split(/\s+/);
		var timeParts = parts[1].split(':');
		return parts[0] + ' ' + (timeParts[0].length === 1 ? '0' + timeParts[0] : timeParts[0]) + ':' + timeParts[1];
	}
	return value;
}

function set_booking_field_value(el, value) {
	if (!el) {
		return;
	}
	var savedOnchange = el.getAttribute('onchange');
	if (savedOnchange) {
		el.removeAttribute('onchange');
	}
	el.value = value;
	if (savedOnchange) {
		el.setAttribute('onchange', savedOnchange);
	}
}

function set_booking_city_select(cityEl, cityId, cityName) {
	if (!cityEl || !cityId) {
		return;
	}
	var $city = $(cityEl);
	if ($city.data('select2')) {
		$city.select2('destroy');
	}
	city_lzloading(cityEl);
	var savedOnchange = cityEl.getAttribute('onchange');
	if (savedOnchange) {
		cityEl.removeAttribute('onchange');
	}
	if (typeof selectCityInLazyDropdown === 'function') {
		selectCityInLazyDropdown($city, cityId, cityName, { triggerChange: false });
	} else {
		$city.append(new Option(cityName, cityId, true, true));
		$city.val(String(cityId)).trigger('change.select2');
	}
	if (savedOnchange) {
		cityEl.setAttribute('onchange', savedOnchange);
	}
}

function set_booking_meal_plan_select(mealPlanEl, mealPlan) {
	if (!mealPlanEl || mealPlan === undefined || mealPlan === null || mealPlan === '') {
		return;
	}
	var $mealPlan = $(mealPlanEl);
	if ($mealPlan.data('select2')) {
		$mealPlan.select2('destroy');
	}
	$mealPlan.removeClass('app_select2');
	var mealValue = String(mealPlan).trim();
	if (!$mealPlan.find('option').filter(function () { return String(this.value) === mealValue; }).length) {
		$mealPlan.append($('<option></option>').attr('value', mealValue).text(mealValue));
	}
	$mealPlan.val(mealValue).trigger('change');
}

function apply_booking_hotel_row_selection(hotelEl, hotelId, hotelName, roomCatEl, roomCategory) {
	if (!hotelEl || !hotelId) {
		return;
	}
	var $hotel = $(hotelEl);
	var hid = String(hotelId);
	var savedOnchange = hotelEl.getAttribute('onchange');
	if (savedOnchange) {
		hotelEl.removeAttribute('onchange');
	}
	if (!$hotel.find('option').filter(function () { return String(this.value) === hid; }).length) {
		$hotel.append(new Option(hotelName || ('Hotel ' + hid), hid, true, true));
	}
	if ($hotel.data('select2')) {
		$hotel.select2('destroy');
	}
	$hotel.select2({ width: '170px', minimumResultsForSearch: 0 });
	$hotel.val(hid).trigger('change');
	if (typeof captureHotelSelect2Config === 'function') {
		captureHotelSelect2Config($hotel);
	}
	if (typeof initHotelSelectAddNew === 'function') {
		initHotelSelectAddNew($hotel);
	}
	if (savedOnchange) {
		hotelEl.setAttribute('onchange', savedOnchange);
	}
	set_booking_hotel_room_category(roomCatEl, hotelId, roomCategory || '');
}

function refresh_booking_hotel_select2_display() {
	var $table = $('#tbl_package_hotel_infomration');
	if (!$table.length) {
		return;
	}
	$table.find('select[id^="hotel_name"], select[id^="txt_catagory"]').each(function () {
		var $sel = $(this);
		var val = $sel.val();
		if (!val) {
			return;
		}
		if ($sel.data('select2')) {
			$sel.val(val).trigger('change');
		}
	});
}

function booking_table_cell_control(cell) {
	if (!cell) {
		return null;
	}
	if (typeof getCellFormControl === 'function') {
		return getCellFormControl(cell);
	}
	var $el = $(cell).find('select, input, textarea').first();
	return $el.length ? $el[0] : cell.childNodes[0];
}

function populate_booking_flight_sector($select, sectorLabel, cityId) {
	if (!$select || !$select.length || !sectorLabel) {
		return;
	}
	if ($select.data('select2')) {
		$select.select2('destroy');
	}
	$select.empty().append(new Option(sectorLabel, sectorLabel, true, true));
	$select.val(sectorLabel);
	// Re-initialize with the airport ajax config so the pre-selected sector
	// stays visible and the field remains searchable. initPlaneAirportSelect2
	// searches for descendant selects, so scope it to the parent cell.
	var $scope = $select.closest('td');
	if (typeof initPlaneAirportSelect2 === 'function' && $scope.length) {
		$select.removeData('pa-select2-config');
		initPlaneAirportSelect2($scope);
	} else if (!$select.data('select2')) {
		$select.select2({ width: '100%' });
	}
	$select.trigger('change.select2');
	if (cityId && typeof syncSectorCityHidden === 'function') {
		syncSectorCityHidden($select.attr('id') || '', cityId);
	} else if (cityId) {
		var sectorId = $select.attr('id') || '';
		var suffix = sectorId.replace(/^from_sector-/, '').replace(/^to_sector-/, '');
		if (sectorId.indexOf('from_sector') === 0) {
			$('#from_city-' + suffix).val(cityId);
		} else {
			$('#to_city-' + suffix).val(cityId);
		}
	}
}

function set_booking_hotel_room_category(roomCatEl, hotelId, roomCategory) {
	if (!roomCatEl || !hotelId) {
		return;
	}
	var $roomCat = $(roomCatEl);
	var quotationId = $('#quotation_id').val() || 0;
	var base_url = $('#base_url').val();
	$.get(base_url + 'view/package_booking/booking/inc/hotel_category.php', { hotel_id: hotelId, quotation_id: quotationId }, function (data) {
		$roomCat.html(data);
		if (roomCategory) {
			if (!$roomCat.find('option').filter(function () { return String(this.value) === String(roomCategory); }).length) {
				$roomCat.append($('<option></option>').attr('value', roomCategory).text(roomCategory));
			}
		}
		if (typeof refreshRoomCategorySelectAfterLoad === 'function') {
			refreshRoomCategorySelectAfterLoad(roomCatEl, { width: '140px' });
		} else if (!$roomCat.data('select2')) {
			$roomCat.select2({ width: '140px' });
		}
		if (roomCategory) {
			$roomCat.val(roomCategory).trigger('change');
		}
		if (typeof get_booking_hotel_cost === 'function') {
			get_booking_hotel_cost();
		}
	});
}

function load_booking_hotel_table(hotel_info_arr) {
	var table = document.getElementById('tbl_package_hotel_infomration');
	if (!table) {
		return;
	}

	while (table.rows.length > 1) {
		table.deleteRow(1);
	}
	while (table.rows.length < (hotel_info_arr || []).length) {
		addRow('tbl_package_hotel_infomration');
	}

	if (!hotel_info_arr || !hotel_info_arr.length) {
		if (table.rows.length > 0) {
			var emptyChk = booking_table_cell_control(table.rows[0].cells[0]);
			if (emptyChk) {
				emptyChk.checked = false;
			}
		}
		return;
	}

	var rowIndex = 0;

	function load_next_booking_hotel_row() {
		rowIndex++;
		if (rowIndex < hotel_info_arr.length) {
			populate_booking_hotel_row(rowIndex);
		} else {
			setTimeout(function () {
				if (typeof initAllHotelSelectAddNew === 'function') {
					initAllHotelSelectAddNew('#tbl_package_hotel_infomration');
				}
				if (typeof initAllRoomCategorySelectAddNew === 'function') {
					initAllRoomCategorySelectAddNew('#tbl_package_hotel_infomration');
				}
				refresh_booking_hotel_select2_display();
				for (var mp = 0; mp < hotel_info_arr.length; mp++) {
					var mpRow = table.rows[mp];
					var mpData = hotel_info_arr[mp] || {};
					if (mpRow && mpData.meal_plan) {
						set_booking_meal_plan_select(booking_table_cell_control(mpRow.cells[8]), mpData.meal_plan);
					}
				}
				if (typeof get_booking_hotel_cost === 'function') {
					get_booking_hotel_cost();
				}
			}, 120);
		}
	}

	function populate_booking_hotel_row(i) {
		var row = table.rows[i];
		var data = hotel_info_arr[i] || {};
		var chkEl = booking_table_cell_control(row.cells[0]);
		var cityEl = booking_table_cell_control(row.cells[2]);
		var hotelEl = booking_table_cell_control(row.cells[3]);
		var checkInEl = booking_table_cell_control(row.cells[4]);
		var checkOutEl = booking_table_cell_control(row.cells[5]);
		var roomsEl = booking_table_cell_control(row.cells[6]);
		var roomCatEl = booking_table_cell_control(row.cells[7]);
		var mealPlanEl = booking_table_cell_control(row.cells[8]);
		var extraBedEl = booking_table_cell_control(row.cells[9]);
		var hotelId = data.hotel_id1 || data.hotel_id || '';
		var hotelName = data.hotel_name1 || data.hotel_name || '';
		var roomCategory = data.room_category || '';

		if (chkEl) {
			chkEl.checked = true;
		}

		set_booking_field_value(checkInEl, format_booking_datetime(data.check_in || data.check_in_date || ''));
		set_booking_field_value(checkOutEl, format_booking_datetime(data.check_out || data.check_out_date || ''));
		if (roomsEl) {
			roomsEl.value = data.total_rooms || '';
		}
		if (extraBedEl) {
			extraBedEl.value = data.extra_bed || '0';
		}

		function finishHotelRowPopulation() {
			set_booking_meal_plan_select(mealPlanEl, data.meal_plan || '');
		}

		if (cityEl && data.city_id && data.city_name) {
			set_booking_city_select(cityEl, data.city_id, data.city_name);
		}

		if (hotelEl && hotelId) {
			var $hotel = $(hotelEl);
			function finishHotelRow() {
				apply_booking_hotel_row_selection(hotelEl, hotelId, hotelName, roomCatEl, roomCategory);
				finishHotelRowPopulation();
				load_next_booking_hotel_row();
			}
			if (data.city_id && typeof hotelDropdownLoadByCity === 'function') {
				hotelDropdownLoadByCity(data.city_id, $hotel, function () {
					finishHotelRow();
				});
			} else {
				if ($hotel.data('select2')) {
					$hotel.select2('destroy');
				}
				$hotel.html('<option value="">*Hotel Name</option>');
				finishHotelRow();
			}
		} else {
			if (roomCatEl && roomCategory) {
				var $roomCat = $(roomCatEl);
				if (!$roomCat.find('option').filter(function () { return String(this.value) === String(roomCategory); }).length) {
					$roomCat.append($('<option></option>').attr('value', roomCategory).text(roomCategory));
				}
				if (typeof refreshRoomCategorySelectAfterLoad === 'function') {
					refreshRoomCategorySelectAfterLoad(roomCatEl, { width: '140px' });
				}
				$roomCat.val(roomCategory).trigger('change');
			}
			finishHotelRowPopulation();
			load_next_booking_hotel_row();
		}
	}

	populate_booking_hotel_row(0);
}

function store_booking_quotation_activity_pax(pax_counts) {
	pax_counts = pax_counts || {};
	window.bookingQuotationActivityPax = {
		total_adult: pax_counts.total_adult != null ? pax_counts.total_adult : '',
		children_with_bed: pax_counts.children_with_bed != null ? pax_counts.children_with_bed : '',
		children_without_bed: pax_counts.children_without_bed != null ? pax_counts.children_without_bed : '',
		total_infant: pax_counts.total_infant != null ? pax_counts.total_infant : ''
	};
	$('#quot_total_adult').val(window.bookingQuotationActivityPax.total_adult);
	$('#quot_children_with_bed').val(window.bookingQuotationActivityPax.children_with_bed);
	$('#quot_children_without_bed').val(window.bookingQuotationActivityPax.children_without_bed);
	$('#quot_total_infant').val(window.bookingQuotationActivityPax.total_infant);
}

function get_booking_quotation_activity_pax() {
	if (window.bookingQuotationActivityPax) {
		return window.bookingQuotationActivityPax;
	}
	return {
		total_adult: $('#quot_total_adult').val() || '',
		children_with_bed: $('#quot_children_with_bed').val() || '',
		children_without_bed: $('#quot_children_without_bed').val() || '',
		total_infant: $('#quot_total_infant').val() || ''
	};
}

// Same as CRM Package Quotation tab2: push traveler counts into every Activity row
function apply_booking_activity_pax_from_quotation(pax_counts) {
	var table = document.getElementById('tbl_package_exc_infomration');
	if (!table) {
		return;
	}
	pax_counts = pax_counts || get_booking_quotation_activity_pax();
	var total_adult = pax_counts.total_adult;
	var children_with_bed = pax_counts.children_with_bed;
	var children_without_bed = pax_counts.children_without_bed;
	var total_infant = pax_counts.total_infant;
	if (
		(total_adult === undefined || total_adult === null || String(total_adult).trim() === '') &&
		(children_with_bed === undefined || children_with_bed === null || String(children_with_bed).trim() === '') &&
		(children_without_bed === undefined || children_without_bed === null || String(children_without_bed).trim() === '') &&
		(total_infant === undefined || total_infant === null || String(total_infant).trim() === '')
	) {
		return;
	}
	for (var i = 0; i < table.rows.length; i++) {
		var row = table.rows[i];
		var adultEl = booking_table_cell_control(row.cells[7]);
		var chwbEl = booking_table_cell_control(row.cells[8]);
		var chwobEl = booking_table_cell_control(row.cells[9]);
		var infantEl = booking_table_cell_control(row.cells[10]);
		if (adultEl && total_adult !== undefined && total_adult !== null && String(total_adult).trim() !== '') {
			adultEl.value = total_adult;
		}
		if (chwbEl && children_with_bed !== undefined && children_with_bed !== null && String(children_with_bed).trim() !== '') {
			chwbEl.value = children_with_bed;
		}
		if (chwobEl && children_without_bed !== undefined && children_without_bed !== null && String(children_without_bed).trim() !== '') {
			chwobEl.value = children_without_bed;
		}
		if (infantEl && total_infant !== undefined && total_infant !== null && String(total_infant).trim() !== '') {
			infantEl.value = total_infant;
		}
	}
}

function load_booking_excursion_table(exc_info_arr, pax_counts) {
	var table = document.getElementById('tbl_package_exc_infomration');
	if (!table) {
		return;
	}
	pax_counts = pax_counts || {};
	store_booking_quotation_activity_pax(pax_counts);

	while (table.rows.length > 1) {
		table.deleteRow(1);
	}
	while (table.rows.length < (exc_info_arr || []).length) {
		addRow('tbl_package_exc_infomration');
		var newRow = table.rows[table.rows.length - 1];
		var newCityEl = booking_table_cell_control(newRow.cells[3]);
		if (newCityEl) {
			city_lzloading(newCityEl);
		}
	}

	if (!exc_info_arr || !exc_info_arr.length) {
		if (table.rows.length > 0) {
			var emptyChk = booking_table_cell_control(table.rows[0].cells[0]);
			if (emptyChk) {
				emptyChk.checked = false;
			}
		}
		// Quotation may have no activities — still fill traveler counts on the blank Activity row (CRM behavior)
		apply_booking_activity_pax_from_quotation(pax_counts);
		return;
	}

	for (var i = 0; i < exc_info_arr.length; i++) {
		var row = table.rows[i];
		var data = exc_info_arr[i] || {};
		var chkEl = booking_table_cell_control(row.cells[0]);
		var dateEl = booking_table_cell_control(row.cells[2]);
		var cityEl = booking_table_cell_control(row.cells[3]);
		var excEl = booking_table_cell_control(row.cells[4]);
		var transferEl = booking_table_cell_control(row.cells[5]);
		var vehicleEl = booking_table_cell_control(row.cells[6]);

		if (chkEl) {
			chkEl.checked = true;
		}
		if (dateEl) {
			set_booking_field_value(dateEl, format_booking_datetime(data.exc_date || ''));
		}

		if (cityEl && data.city_id && data.city_name) {
			set_booking_city_select(cityEl, data.city_id, data.city_name);
		}

		if (excEl && data.exc_id) {
			$(excEl).html(
				'<option value="' + data.exc_id + '" selected="selected">' + (data.exc_name || '') + '</option>'
			);
			excEl.value = data.exc_id;
		}

		if (transferEl && data.transfer_option) {
			var $transfer = $(transferEl);
			if (!$transfer.find('option[value="' + data.transfer_option + '"]').length) {
				$transfer.prepend(
					$('<option></option>').attr('value', data.transfer_option).text(data.transfer_option)
				);
			}
			transferEl.value = data.transfer_option;
			if (!$transfer.data('select2')) {
				$transfer.select2({ width: '200px' });
			}
			$transfer.trigger('change');
		}

		if (vehicleEl && data.vehicle_id) {
			var $vehicle = $(vehicleEl);
			if (typeof selectVehicleInDropdown === 'function') {
				selectVehicleInDropdown($vehicle, data.vehicle_id, data.vehicle_name || '');
			} else {
				if (!$vehicle.find('option[value="' + data.vehicle_id + '"]').length) {
					$vehicle.prepend(
						$('<option></option>').attr('value', data.vehicle_id).text(data.vehicle_name || '').prop('selected', true)
					);
				}
				vehicleEl.value = data.vehicle_id;
				$vehicle.trigger('change');
			}
		}
	}

	// Always apply quotation traveler counts to Activity (adult / CWB / CWoB / infant)
	apply_booking_activity_pax_from_quotation(pax_counts);

	if (typeof initAllVehicleSelectAddNew === 'function') {
		initAllVehicleSelectAddNew('#tbl_package_exc_infomration');
	}
}

function populate_booking_transport_pickup_drop($select, type, locId, label, placeholder) {
	if (!$select || !$select.length || !type || !locId) {
		return;
	}
	var val = type + '-' + locId;
	var groupLabel = type.charAt(0).toUpperCase() + type.slice(1) + ' Name';
	if ($select.data('select2')) {
		$select.select2('destroy');
	}
	$select.empty().append(
		$('<optgroup></optgroup>').attr('value', type).attr('label', groupLabel).append(
			$('<option></option>').attr('value', val).text(label || val).prop('selected', true)
		)
	);
	$select.val(val);
	if (!$select.data('select2')) {
		$select.select2({ width: '250px' });
	} else {
		$select.trigger('change.select2');
	}
}

function load_booking_flight_table(flight_info_arr, flightCost) {
	var table = document.getElementById('tbl_plane_travel_details_dynamic_row');
	if (!table) {
		return;
	}

	while (table.rows.length > 1) {
		table.deleteRow(1);
	}
	while (table.rows.length < (flight_info_arr || []).length) {
		addRow('tbl_plane_travel_details_dynamic_row');
		if (typeof event_airport === 'function') {
			event_airport('tbl_plane_travel_details_dynamic_row', 4, 5);
		}
	}

	if (!flight_info_arr || !flight_info_arr.length) {
		if (table.rows.length > 0) {
			var emptyChk = booking_table_cell_control(table.rows[0].cells[0]);
			if (emptyChk) {
				emptyChk.checked = false;
			}
		}
		return;
	}

	for (var i = 0; i < flight_info_arr.length; i++) {
		var row = table.rows[i];
		var data = flight_info_arr[i] || {};
		var chkEl = booking_table_cell_control(row.cells[0]);
		var depEl = booking_table_cell_control(row.cells[2]);
		var arrEl = booking_table_cell_control(row.cells[3]);
		var fromEl = booking_table_cell_control(row.cells[4]);
		var toEl = booking_table_cell_control(row.cells[5]);
		var airlineEl = booking_table_cell_control(row.cells[6]);
		var classEl = booking_table_cell_control(row.cells[7]);
		var amountEl = booking_table_cell_control(row.cells[9]);
		var fromCityEl = booking_table_cell_control(row.cells[10]);
		var toCityEl = booking_table_cell_control(row.cells[11]);

		if (chkEl) {
			chkEl.checked = true;
		}

		set_booking_field_value(depEl, format_booking_datetime(data.departure_date || ''));
		set_booking_field_value(arrEl, format_booking_datetime(data.arrival_date || ''));

		var from_sector = '';
		if (data.from_location) {
			from_sector = data.from_city ? data.from_city + ' - ' + data.from_location : data.from_location;
		}
		var to_sector = '';
		if (data.to_location) {
			to_sector = data.to_city ? data.to_city + ' - ' + data.to_location : data.to_location;
		}

		populate_booking_flight_sector($(fromEl), from_sector, data.from_city_id);
		populate_booking_flight_sector($(toEl), to_sector, data.to_city_id);

		if (fromCityEl) {
			fromCityEl.value = data.from_city_id || '';
		}
		if (toCityEl) {
			toCityEl.value = data.to_city_id || '';
		}

		if (airlineEl && data.airline_id) {
			var $airline = $(airlineEl);
			if ($airline.data('select2')) {
				$airline.select2('destroy');
			}
			if (!$airline.find('option[value="' + data.airline_id + '"]').length) {
				$airline.prepend(
					$('<option></option>').attr('value', data.airline_id).text(data.airline_name || '').prop('selected', true)
				);
			}
			airlineEl.value = data.airline_id;
			$airline.select2({ width: '150px' });
		}

		if (classEl && data.class) {
			var $class = $(classEl);
			if ($class.data('select2')) {
				$class.select2('destroy');
			}
			if (!$class.find('option[value="' + data.class + '"]').length) {
				$class.append($('<option></option>').attr('value', data.class).text(data.class));
			}
			$class.val(data.class);
		}

		if (amountEl) {
			amountEl.value = '';
		}

		if (depEl && depEl.id && typeof dynamic_datetime === 'function') {
			dynamic_datetime(depEl.id);
		}
		if (arrEl && arrEl.id && typeof dynamic_datetime === 'function') {
			dynamic_datetime(arrEl.id);
		}
	}

	$('#tbl_plane_travel_details_dynamic_row select[id^="plane_class-"]').each(function () {
		if ($(this).data('select2')) {
			$(this).select2('destroy');
		}
	});

	if (typeof initAllAirlineSelectAddNew === 'function') {
		initAllAirlineSelectAddNew('#tbl_plane_travel_details_dynamic_row');
	}
	if (typeof calculate_plane_expense === 'function') {
		calculate_plane_expense('tbl_plane_travel_details_dynamic_row', true);
	}
}

function load_booking_transport_table(transport_info_arr) {
	var table = document.getElementById('tbl_package_transport_infomration');
	if (!table) {
		return;
	}

	while (table.rows.length > 1) {
		table.deleteRow(1);
	}
	while (table.rows.length < transport_info_arr.length) {
		addRow('tbl_package_transport_infomration');
	}

	if (!transport_info_arr.length) {
		if (table.rows.length > 0) {
			var emptyChk = booking_table_cell_control(table.rows[0].cells[0]);
			if (emptyChk) {
				emptyChk.checked = false;
			}
		}
		return;
	}

	for (var i = 0; i < transport_info_arr.length; i++) {
		var row = table.rows[i];
		var data = transport_info_arr[i];
		var chkEl = booking_table_cell_control(row.cells[0]);
		var vehicleEl = booking_table_cell_control(row.cells[2]);
		var fromDateEl = booking_table_cell_control(row.cells[3]);
		var toDateEl = booking_table_cell_control(row.cells[4]);
		var pickupEl = booking_table_cell_control(row.cells[5]);
		var dropEl = booking_table_cell_control(row.cells[6]);
		var durationEl = booking_table_cell_control(row.cells[7]);
		var countEl = booking_table_cell_control(row.cells[8]);

		if (chkEl) {
			chkEl.checked = true;
		}
		if (fromDateEl) {
			set_booking_field_value(fromDateEl, format_booking_datetime(data['start_date'] || ''));
		}
		if (toDateEl) {
			set_booking_field_value(toDateEl, format_booking_datetime(data['end_date'] || ''));
		}

		if (vehicleEl && data['vehicle_id']) {
			var $vehicle = $(vehicleEl);
			var vehicleLabel = data['vehicle_name'] || data['bus_name'] || '';
			if (typeof selectVehicleInDropdown === 'function') {
				selectVehicleInDropdown($vehicle, data['vehicle_id'], vehicleLabel);
			} else {
				if (!$vehicle.find('option[value="' + data['vehicle_id'] + '"]').length) {
					$vehicle.prepend(
						'<option value="' + data['vehicle_id'] + '" selected="selected">' + vehicleLabel + '</option>'
					);
				}
				vehicleEl.value = data['vehicle_id'];
				$vehicle.trigger('change');
			}
			if (!$vehicle.data('select2')) {
				$vehicle.select2({ width: '250px' });
			}
		}

		if (pickupEl) {
			populate_booking_transport_pickup_drop(
				$(pickupEl),
				data['pickup_type'],
				data['pickup_id'],
				data['pickup'],
				'Pickup Location'
			);
		}
		if (dropEl) {
			populate_booking_transport_pickup_drop(
				$(dropEl),
				data['drop_type'],
				data['drop_id'],
				data['drop'],
				'Drop-off Location'
			);
		}

		if (durationEl) {
			var $duration = $(durationEl);
			if (data['s_duration_id'] && !$duration.find('option[value="' + data['s_duration_id'] + '"]').length) {
				$duration.prepend(
					'<option value="' + data['s_duration_id'] + '">' + data['service_duration'] + '</option>'
				);
			}
			if (data['s_duration_id']) {
				durationEl.value = data['s_duration_id'];
			} else if (data['service_duration']) {
				durationEl.value = data['service_duration'];
			}
			if (!$duration.data('select2')) {
				$duration.select2({ width: '170px' });
			}
			$duration.trigger('change');
		}

		if (countEl) {
			countEl.value = data['vehicle_count'] || '';
		}

		if (fromDateEl && fromDateEl.id && typeof dynamic_datetime === 'function') {
			dynamic_datetime(fromDateEl.id);
		}
		if (toDateEl && toDateEl.id && typeof dynamic_datetime === 'function') {
			dynamic_datetime(toDateEl.id);
		}
	}

	if (typeof initAllVehicleSelectAddNew === 'function') {
		initAllVehicleSelectAddNew('#tbl_package_transport_infomration');
	}
	if (typeof initBookingServiceDurationSelects === 'function') {
		initBookingServiceDurationSelects();
	}
	if (typeof initBookingTransportPickupDrop === 'function') {
		initBookingTransportPickupDrop();
	}
}

/////////////////////////////////////Quotation information load start/////////////////////////////////////
function quotation_info_load() {

	var quotation_id = $('#quotation_id').val();
	//Quotation is selected
	if (quotation_id != 0) {
		$('#dest_div').html('');
		$('#package_div').html('');
		$('#package_program').html('');
		var base_url = $('#base_url').val() || '';
		$.ajax({
			type: 'post',
			url: base_url + 'view/package_booking/booking/inc/quotation_info_load.php',
			data: { quotation_id: quotation_id },
			success: function (result) {
				var response;
				try {
					response = (typeof result === 'object') ? result : JSON.parse(result);
				} catch (e) {
					console.error('quotation_info_load: invalid JSON', e, result);
					return;
				}
				$('#txt_package_tour_name').val(response.tour_name);
				$('#txt_package_package_id').val(response.package_id);
				$('#txt_package_from_date').val(response.from_date);
				$('#txt_package_to_date').val(response.to_date);
				$('#tour_type').val(response.booking_type);
				$('#txt_tour_total_days').val(response.total_days);
				$('#txt_child_without_bed').val(response.children_without_bed);
				$('#tax_apply_on').val(response.tax_apply_on);
				$('#tax_value').val(response.tax_value);
				setBookingDiscountIn(response.discount_in);
				$('#discount_amt').val(response.discount);
				$('#txt_special_request').html(response.enquiry_spec);
				//Passenger Rows
				var table = document.getElementById('tbl_package_tour_member');
				if (table.rows.length == 1) {
					for (var k = 1; k < table.rows.length; k++) {
						document.getElementById('tbl_package_tour_member').deleteRow(k);
					}
				}
				else {
					while (table.rows.length > 1) {
						document.getElementById('tbl_package_tour_member').deleteRow(1);
					}
				}
				if (table.rows.length != response.total_passangers) {
					for (var j = 0; j < response.total_passangers - 1; j++) {
						addRow('tbl_package_tour_member');
					}
				}
				//Transport Info
				load_booking_transport_table(response.transport_info_arr || []);
				//Excursion/Activity Info — fill pax from quotation traveler counts when activity rows lack them
				load_booking_excursion_table(response.exc_info_arr || [], {
					total_adult: response.total_adult,
					children_with_bed: response.children_with_bed,
					children_without_bed: response.children_without_bed,
					total_infant: response.total_infant
				});

				//Train Info
				var train_info_arr = response.train_info_arr;
				var table = document.getElementById('tbl_train_travel_details_dynamic_row');
				for (var i = 1; i < table.rows.length; i++) {
					document.getElementById('tbl_train_travel_details_dynamic_row').deleteRow(i);
				}
				if (table.rows.length != train_info_arr.length) {
					for (var i = 1; i < train_info_arr.length; i++) {
						addRow('tbl_train_travel_details_dynamic_row');
					}
				}
				for (var i = 0; i < train_info_arr.length; i++) {
					var row = table.rows[i];
					row.cells[0].childNodes[0].setAttribute('checked', 'true');

					set_booking_field_value(row.cells[2].childNodes[0], format_booking_datetime(train_info_arr[i]['departure_date']));
					$(row.cells[3].childNodes[0]).html(
						'<option value="' +
						train_info_arr[i]['from_location'] +
						'" selected="selected">' +
						train_info_arr[i]['from_location'] +
						'</option>'
					);
					$(row.cells[4].childNodes[0]).html(
						'<option value="' +
						train_info_arr[i]['to_location'] +
						'" selected="selected">' +
						train_info_arr[i]['to_location'] +
						'</option>'
					);
					row.cells[8].childNodes[0].value = train_info_arr[i]['class'];
				}

				load_booking_flight_table(response.flight_info_arr || [], response.flight_cost);
				//Cruise Info
				var cruise_info_arr = response.cruise_info_arr;
				var table = document.getElementById('tbl_dynamic_cruise_package_booking');

				for (var i = 1; i < table.rows.length; i++) {
					document.getElementById('tbl_dynamic_cruise_package_booking').deleteRow(i);
				}
				//add rows for that length
				if (table.rows.length != cruise_info_arr.length) {
					for (var i = 1; i < cruise_info_arr.length; i++) {
						addRow('tbl_dynamic_cruise_package_booking');
					}
				}
				for (var i = 0; i < cruise_info_arr.length; i++) {
					var row = table.rows[i];
					row.cells[0].childNodes[0].setAttribute('checked', 'true');

					set_booking_field_value(row.cells[2].childNodes[0], format_booking_datetime(cruise_info_arr[i]['departure_date']));
					set_booking_field_value(row.cells[3].childNodes[0], format_booking_datetime(cruise_info_arr[i]['arrival_date']));
					row.cells[4].childNodes[0].value = cruise_info_arr[i]['route'];
					row.cells[5].childNodes[0].value = cruise_info_arr[i]['cabin'];
					row.cells[6].childNodes[0].value = cruise_info_arr[i]['sharing'];
				}

				var tour_type = $('#tour_type').val();
				passport_fields_toggle(tour_type);
				// Package Types
				var hotel_package_type_arr = response.hotel_package_type_arr;
				var package_html = '<select id="package_type" class="form-control" style="width:100%" onchange="get_package_type_costing()">';
				for (var i = 0; i < hotel_package_type_arr.length; i++) {
				
					package_html += '<option value="' +
					hotel_package_type_arr[i]['package_type'] +
					'">' +
					hotel_package_type_arr[i]['package_type'] +
					'</option>';
				}
				package_html += '</select>';
				$('#package_types_html').html(package_html);
				if (hotel_package_type_arr.length > 0) {
					get_package_type_costing();
				} else {
					load_booking_hotel_table(response.hotel_info_arr || []);
					get_package_type_costing();
				}
			}
		});
	}
	else {
		window._quotation_tcsper = '';
		window._quotation_tcsvalue = 0;
		store_booking_quotation_activity_pax({
			total_adult: '',
			children_with_bed: '',
			children_without_bed: '',
			total_infant: ''
		});
		$('#tcs_tax').val('');
		$('#tcs1').val('0.00');
		$('#txt_package_tour_name').val('');
		$('#tour_type').val('');
		$('#txt_package_from_date').val('');
		$('#txt_package_to_date').val('');
		$('#txt_tour_total_days').val('');
		$('#txt_special_request').html('');
		if(quotation_id != ''){
			var package_html = '<select class="form-control" style="width:100%">'+'<option value="NA">NA</option>';
			$('#package_types_html').html(package_html);
		}else{
			$('#package_types_html').html('');
		}
		// Passenger Info
		var table = document.getElementById("tbl_package_tour_member");
		if (table.rows.length == 1) {
			for (var k = 1; k < table.rows.length; k++) {
				document.getElementById("tbl_package_tour_member").deleteRow(k);
			}
		} else {
			while (table.rows.length > 1) {
				document.getElementById("tbl_package_tour_member").deleteRow(k);
				table.rows.length--;
			}
		}
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			row.cells[3].childNodes[0].value = '';
			row.cells[4].childNodes[0].value = '';
			row.cells[5].childNodes[0].value = '';
		}
		//Train Info
		var table = document.getElementById('tbl_train_travel_details_dynamic_row');
		if (table.rows.length == 1) {
			for (var k = 1; k < table.rows.length; k++) {
				document.getElementById("tbl_train_travel_details_dynamic_row").deleteRow(k);
			}
		} else {
			while (table.rows.length > 1) {
				document.getElementById("tbl_train_travel_details_dynamic_row").deleteRow(k);
				table.rows.length--;
			}
		}
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			row.cells[2].childNodes[0].value = '';
		}
		//Flight Info
		var table = document.getElementById('tbl_plane_travel_details_dynamic_row');
		if (table.rows.length == 1) {
			for (var k = 1; k < table.rows.length; k++) {
				document.getElementById("tbl_plane_travel_details_dynamic_row").deleteRow(k);
			}
		} else {
			while (table.rows.length > 1) {
				document.getElementById("tbl_plane_travel_details_dynamic_row").deleteRow(k);
				table.rows.length--;
			}
		}
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			row.cells[2].childNodes[0].value = '';
			row.cells[10].childNodes[0].value = '';
		}
		//Cruise Info
		var table = document.getElementById('tbl_dynamic_cruise_package_booking');
		if (table.rows.length == 1) {
			for (var k = 1; k < table.rows.length; k++) {
				document.getElementById("tbl_dynamic_cruise_package_booking").deleteRow(k);
			}
		} else {
			while (table.rows.length > 1) {
				document.getElementById("tbl_dynamic_cruise_package_booking").deleteRow(k);
				table.rows.length--;
			}
		}
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			row.cells[2].childNodes[0].value = '';
			row.cells[3].childNodes[0].value = '';
			row.cells[4].childNodes[0].value = '';
			row.cells[5].childNodes[0].value = '';
		}
		//Hotel Info
		var table = document.getElementById('tbl_package_hotel_infomration');
		if (table.rows.length == 1) {
			for (var k = 1; k < table.rows.length; k++) {
				document.getElementById("tbl_package_hotel_infomration").deleteRow(k);
			}
		} else {
			while (table.rows.length > 1) {
				document.getElementById("tbl_package_hotel_infomration").deleteRow(k);
				table.rows.length--;
			}
		}
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			row.cells[4].childNodes[0].value = '';
			row.cells[5].childNodes[0].value = '';
			row.cells[6].childNodes[0].value = '';
			row.cells[2].childNodes[0].value = '';
			row.cells[3].childNodes[0].value = '';
			$('#' + row.cells[2].childNodes[0].id).select2().trigger("change");
			city_lzloading(row.cells[2].childNodes[0]);
			$('#' + row.cells[3].childNodes[0].id).trigger("change");
		}
		//Transport Info
		var table = document.getElementById('tbl_package_transport_infomration');
		if (table.rows.length == 1) {
			for (var k = 1; k < table.rows.length; k++) {
				document.getElementById("tbl_package_transport_infomration").deleteRow(k);
			}
		} else {
			while (table.rows.length > 1) {
				document.getElementById("tbl_package_transport_infomration").deleteRow(k);
				table.rows.length--;
			}
		}
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			row.cells[2].childNodes[0].value = '';
			row.cells[5].childNodes[0].value = '';
			row.cells[6].childNodes[0].value = '';
			row.cells[7].childNodes[0].value = '';
			row.cells[8].childNodes[0].value = '';
			$('#' + row.cells[2].childNodes[0].id).trigger("change");
			$('#' + row.cells[5].childNodes[0].id).trigger("change");
			$('#' + row.cells[6].childNodes[0].id).trigger("change");
			$('#' + row.cells[7].childNodes[0].id).select2().trigger("change");
		}
		//Excursion Info
		var table = document.getElementById('tbl_package_exc_infomration');
		if (table.rows.length == 1) {
			for (var k = 1; k < table.rows.length; k++) {
				document.getElementById("tbl_package_exc_infomration").deleteRow(k);
			}
		} else {
			while (table.rows.length > 1) {
				document.getElementById("tbl_package_exc_infomration").deleteRow(k);
				table.rows.length--;
			}
		}
		$('#txt_hotel_expenses').val('');
		$('#service_charge').val('');
		$('#total_basic_amt').val('');
		$('#discount_amt').val('');
		$('#discount_in').html('<option value="Percentage">Percentage</option><option value="Flat">Flat</option>');
		$('#txt_special_request').html('');
		$('#tax_apply_on').val('');
		$('#tax_value').val('');

		//Destination dropdown load
		$.get('../inc/get_destin_dropdown.php', {}, function (data) {
			$('#dest_div').html(data);
		});

		// taxes_reflect('', '');

		//Packages load
		var dest_id = $('#dest_name2').val();
		if (dest_id != 0) {
			$.ajax({
				type: 'post',
				url: '../inc/get_packages.php',
				data: { dest_id: dest_id },
				success: function (result) {
					$('#package_program').html(result);
				},
				error: function (result) {
					console.log(result.responseText);
				}
			});
		}
	}
	//currency dropdown load
	$.get('../inc/get_currency_dropdown.php', {quotation_id:quotation_id}, function (data) {
		$('#currency_div').html(data);
	});
}

function setBookingDiscountIn(discountIn) {
	var normalized = String(discountIn || '').trim();
	if (normalized === '1') {
		normalized = 'Percentage';
	} else if (normalized === '2') {
		normalized = 'Flat';
	} else if (normalized !== 'Percentage' && normalized !== 'Flat') {
		normalized = 'Percentage';
	}
	$('#discount_in').html('<option value="Percentage">Percentage</option><option value="Flat">Flat</option>');
	$('#discount_in').val(normalized);
}

function mapQuotationTcsForSale(tcsper) {
	tcsper = String(tcsper || '').trim();
	if (tcsper === '' || tcsper === '0' || tcsper === '1' || tcsper === 'NaN') {
		return '';
	}
	if (tcsper === '3') {
		return '20';
	}
	if (tcsper === '2' || tcsper === '20') {
		return (tcsper === '20') ? '20' : '2';
	}
	return '';
}

function applyQuotationTcs(tcsper, tcsvalue) {
	var mappedTcs = mapQuotationTcsForSale(tcsper);
	window._quotation_tcsper = mappedTcs;
	window._quotation_tcsvalue = parseFloat(tcsvalue) || 0;
	$('#tcs_tax').val(mappedTcs);
	if (typeof customTcsTax === 'function') {
		customTcsTax();
	} else {
		$('#tcs_tax').trigger('change');
	}
}

function refreshQuotationTcsOnCostingTab() {
	var quotation_id = $('#quotation_id').val();
	if (!quotation_id || quotation_id === '0') {
		return;
	}
	if (window._quotation_tcsper) {
		$('#tcs_tax').val(window._quotation_tcsper);
		if (typeof customTcsTax === 'function') {
			customTcsTax();
		}
	}
}

function get_package_type_costing() {

	var quotation_id = $('#quotation_id').val();
	var package_type = $('#package_type').val();
	$.ajax({
		type: 'post',
		url: '../inc/quotation_cost_load.php',
		data: { quotation_id: quotation_id,package_type:package_type },
		success: function (result) {
			var response = JSON.parse(result);
			load_booking_hotel_table(response.hotel_info_arr || []);
			$('#txt_hotel_expenses').val(response.tour_cost || 0);
			$('#service_charge').val(response.service_charge);
			$('#total_basic_amt').val(response.tour_cost);
			setBookingDiscountIn(response.discount_in);
			$('#discount_amt').val(response.discount);
			$('#tax_apply_on').val(response.tax_apply_on);
			$('#tax_value').val(response.tax_value);
			if (response.tour_cost !== undefined && response.tour_cost !== null) {
				response.tour_cost = parseFloat(response.tour_cost).toFixed(2);
			}
			get_auto_values('txt_booking_date','total_basic_amt','payment_mode','service_charge','markup','save','true','service_charge','discount_amt');
			applyQuotationTcs(response.tcsper, response.tcsvalue);
		}
});
}
function get_package_program(package) {
	var package_id = $('#' + package).val();
	if (package_id != 0) {
		///Package hotel info load
		$.ajax({
			type: 'post',
			url: '../inc/package_hotel_info_load.php',
			data: {
				package_id: package_id,
				from_date: $('#txt_package_from_date').val()
			},

			success: function (result) {
				var result1 = JSON.parse(result);
				load_booking_hotel_table(result1.hotel_info_arr || []);

				//Transport Info
				load_booking_transport_table(result1.transport_info_arr || []);

				$('#txt_package_tour_name').val(result1.package_name);
				$('#tour_type').val(result1.tour_type);
				$('#tour_type').trigger('change');
			}
		});

		//Itinerary Reflection
		$.ajax({
			type: 'post',
			url: '../inc/get_package_program.php',
			data: { package_id: package_id },
			success: function (result) {
				if (package_id != 0) {
					$('#package_program').html(result);
				}
				else {
					$('#package_program').html('');
				}
			},
			error: function (result) {
				console.log(result.responseText);
			}
		});
	}
	else {
		$('#txt_package_tour_name').val('');
		$('#tour_type').val('');
		$('#txt_package_from_date').val('');
		$('#txt_package_to_date').val('');
		$('#txt_tour_total_days').val('');
	}
}

function package_dynamic_reflect(dest_name) {

	var dest_id = $('#' + dest_name).val();
	$.ajax({
		type: 'post',
		url: '../inc/get_packages.php',
		data: { dest_id: dest_id },
		success: function (result) {
			if (dest_id != 0) {
				$('#package_div').html(result);
			}
			else {
				$('#package_program').html(result);
			}
		},
		error: function (result) {
			console.log(result.responseText);
		}
	});

	if (dest_id == 0) {
		$('#package_div').html('');
		$('#package_program').html('');
		$('#txt_package_tour_name').val('');
		$('#tour_type').val('');
		$('#txt_package_from_date').val('');
		$('#txt_package_to_date').val('');
		$('#txt_tour_total_days').val('');
	}
}

/**Excursion Name load**/
function get_excursion_list(id) {
	var city_id = $('#' + id).val();
	var base_url = $('#base_url').val();
	var count = id.replace(/^city_name-/, '');
	var $excursion = $('#excursion-' + count);

	$.post(base_url + 'view/package_booking/quotation/home/excursion_name_load.php', { city_id: city_id }, function (
		data
	) {
		if ($excursion.data('select2')) {
			$excursion.select2('destroy');
		}
		$excursion.empty().html(data);
		if ($excursion.hasClass('app_select2') || $excursion.data('select2')) {
			$excursion.select2({ width: '200px' });
		}
	});
}
/////////////////////////////////////Quotation information load end/////////////////////////////////////

/////////////////////////////////////Passport fields toggle start/////////////////////////////////////
function passport_fields_toggle(tour_type) {
	if (tour_type == 'International') {
		$(
			'input[name="txt_m_passport_no1"],input[name="txt_m_passport_issue_date1"], input[name="txt_m_passport_expiry_date1"]'
		).prop('disabled', false);
	}
	if (tour_type == 'Domestic') {
		$(
			'input[name="txt_m_passport_no1"], input[name="txt_m_passport_issue_date1"], input[name="txt_m_passport_expiry_date1"]'
		).prop('disabled', true);
	}
}
/////////////////////////////////////Passport fields toggle end/////////////////////////////////////
