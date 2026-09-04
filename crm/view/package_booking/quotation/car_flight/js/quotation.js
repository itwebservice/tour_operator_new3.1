if ($('#quotation_date').length) {
	$('#quotation_date').datetimepicker({ timepicker: false, format: 'd-m-Y' });
}

function quotation_cost_calculate() {
	var quotation_cost = 0;
	var subtotal = $('#subtotal').val();
	var markup_cost = $('#markup_cost').val();
	var service_tax_markup = $('#service_tax_markup').val();
	var service_tax_subtotal = $('#service_tax_subtotal').val();
	var service_charge = $('#service_charge').val();
	var permit = $('#permit').val();
	var toll_parking = $('#toll_parking').val();
	var driver_allowance = $('#driver_allowance').val();
	var state_entry = $('#state_entry').val();
	var other_charges = $('#other_charges').val();

	var service_tax_amount = 0;
	if (parseFloat(service_tax_subtotal) !== 0.00 && (service_tax_subtotal) !== '') {

		var service_tax_subtotal1 = service_tax_subtotal.split(",");
		for (var i = 0; i < service_tax_subtotal1.length; i++) {
			var service_tax = service_tax_subtotal1[i].split(':');
			service_tax_amount = parseFloat(service_tax_amount) + parseFloat(service_tax[2]);
		}
	}
	var markupservice_tax_amount = 0;
	if (parseFloat(service_tax_markup) !== 0.00 && (service_tax_markup) !== "") {
		var service_tax_markup1 = service_tax_markup.split(",");
		for (var i = 0; i < service_tax_markup1.length; i++) {
			var service_tax = service_tax_markup1[i].split(':');
			markupservice_tax_amount = parseFloat(markupservice_tax_amount) + parseFloat(service_tax[2]);
		}
	}

	if (subtotal == '') {
		subtotal = 0;
	}
	if (permit == '') {
		permit = 0;
	}
	if (toll_parking == '') {
		toll_parking = 0;
	}
	if (driver_allowance == '') {
		driver_allowance = 0;
	}
	if (state_entry == '') {
		state_entry = 0;
	}
	if (other_charges == '') {
		other_charges = 0;
	}

	if (markup_cost == '') {
		markup_cost = 0;
	}
	subtotal = ($('#basic_show').html() == '&nbsp;') ? subtotal : parseFloat($('#basic_show').text().split(' : ')[1]);
	service_charge = ($('#service_show').html() == '&nbsp;') ? service_charge : parseFloat($('#service_show').text().split(' : ')[1]);
	markup_cost = ($('#markup_show').html() == '&nbsp;') ? markup_cost : parseFloat($('#markup_show').text().split(' : ')[1]);

	
	total_tour_cost =
		parseFloat(subtotal) +
		parseFloat(markupservice_tax_amount) +
		parseFloat(permit) +
		parseFloat(toll_parking) +
		parseFloat(driver_allowance) +
		parseFloat(state_entry) +
		parseFloat(other_charges) +
		parseFloat(service_tax_amount) +
		parseFloat(markup_cost) +
		parseFloat(service_charge);
	// quotation_cost = parseFloat(total_tour_cost.toFixed(2));
	// var roundoff = Math.round(quotation_cost) - quotation_cost;
	// $('#roundoff').val(roundoff.toFixed(2));
	// var total_cost = parseFloat(quotation_cost) + parseFloat(roundoff);
	// $('#total_tour_cost').val(total_cost.toFixed(2));



	var quotation_cost = parseFloat(total_tour_cost.toFixed(2));
var roundoff = 0; // Set roundoff to zero
$('#roundoff').val(roundoff.toFixed(2));

// Since roundoff is zero, total_cost equals quotation_cost
var total_cost = quotation_cost;
$('#total_tour_cost').val(total_cost.toFixed(2));

 calculate_per_person_cost();

}

function calculate_per_person_cost() {

    let adults = parseInt($('#total_adult').val()) || 0;
    let child_wb = parseInt($('#children_with_bed').val()) || 0;
    let child_wob = parseInt($('#children_without_bed').val()) || 0;

    let total_pax = adults + child_wb + child_wob;

    let total_cost = parseFloat($('#total_tour_cost').val()) || 0;

    let per_person = 0;

    if (total_pax > 0) {
        per_person = total_cost / total_pax;
    }

    console.log("TOTAL:", total_cost, "PAX:", total_pax, "PP:", per_person);

    // 👉 Update UI (adjust selector if needed)
    $('#per_person_cost').text('₹ ' + per_person.toFixed(2));
}

function get_enquiry_details(offset = '') {
	var enquiry_id = $('#enquiry_id' + offset).val();
	var base_url = $('#base_url').val();
	$.ajax({
		type: 'post',
		url: base_url + 'view/package_booking/quotation/car_flight/car_rental/get_enquiry_details.php',
		dataType: 'json',
		data: { enquiry_id: enquiry_id },
		success: function (result) {
			if (enquiry_id != '' && enquiry_id != '0') {
				let trav_date = result.traveling_date.split(' ');
				$('#customer_name' + offset).val(result.name);
				$('#email_id' + offset).val(result.email_id);
				$('#mobile_no' + offset).val(result.landline_no);
				$('#country_code' + offset).val(result.country_code);
				$('#country_code'+ offset).trigger('change');
				$('#total_pax' + offset).val(result.total_pax);
				$('#days_of_traveling' + offset).val(result.days_of_traveling);
				$('#from_date' + offset).val(trav_date[0]);
				$('#to_date' + offset).val(trav_date[0]);
				$('#traveling_date' + offset).val(result.traveling_date);
				$('#vehicle_name' + offset).val(result.vehicle_type);
				$('#travel_type' + offset).val(result.travel_type);
				$('#local_places_to_visit' + offset).html(result.places_to_visit);
			}
			else {
				console.log('hii');
				$('#customer_name' + offset).val('');
				$('#email_id' + offset).val('');
				$('#mobile_no' + offset).val('');
				$('#total_pax' + offset).val('');
				$('#days_of_traveling' + offset).val('');
				$('#traveling_date' + offset).val('');
				$('#vehicle_name' + offset).val('');
				$('#travel_type' + offset).val('');
				$('#local_places_to_visit' + offset).html('');
				$('#rate' + offset).val('');
				$('#total_hr' + offset).val('');
				$('#total_km' + offset).val('');
				
				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth() + 1; //January is 0!
			
				var yyyy = today.getFullYear();
				if (dd < 10) {
					dd = "0" + dd;
				}
				if (mm < 10) {
					mm = "0" + mm;
				}
				var today = dd + "-" + mm + "-" + yyyy;
				$('#from_date' + offset).val(today);
				$('#to_date' + offset).val(today);

				if(offset!=''){
					$('#travel_type1').prop('disabled', false);
				}
			}
			if(offset==''){
				reflect_feilds();
			}else{
				reflect_feilds1();
			}
			get_car_cost();
			get_capacity();
		},
		error: function (result) {
			// alert(result);
			console.log(result.responseText);
		}
	});
	get_basic_amount()
}
function get_car_cost(offset='') {
	var travel_type = $('#travel_type'+offset).val();
	var vehicle_name = $('#vehicle_name'+offset).val();
	var places_to_visit = $('#places_to_visit'+offset).val();

	var base_url = $('#base_url').val();
	$.ajax({
		type: 'post',
		url: base_url + 'view/package_booking/quotation/car_flight/car_rental/get_car_cost.php',
		dataType: 'json',
		data: { travel_type: travel_type, vehicle_name: vehicle_name, places_to_visit: places_to_visit },
		success: function (result) {
			if(parseInt(result.length) != 0){
				
			console.log(offset);

				$('#total_hr'+offset).val(result[0].total_hrs);
				$('#total_km'+offset).val(result[0].total_km);
				$('#extra_hr_cost'+offset).val(result[0].extra_hrs_rate);
				$('#extra_km_cost'+offset).val(result[0].extra_km_rate);
				$('#route'+offset).val(result[0].route);
				$('#total_max_km').val(result[0].total_max_km);
				$('#rate'+offset).val(result[0].rate);
				$('#driver_allowance'+offset).val(result[0].driver_allowance);
				$('#permit'+offset).val(result[0].permit_charges);
				$('#toll_parking'+offset).val(result[0].toll_parking);
				$('#state_entry'+offset).val(result[0].state_entry_pass);
				$('#other_charges'+offset).val(result[0].other_charges);
			}else{
				$('#total_hr'+offset).val('');
				$('#total_km'+offset).val('');
				$('#extra_hr_cost'+offset).val('');
				$('#extra_km_cost'+offset).val('');
				$('#route'+offset).val('');
				$('#total_max_km').val('');
				$('#rate'+offset).val('');
				$('#driver_allowance'+offset).val('');
				$('#permit'+offset).val('');
				$('#toll_parking'+offset).val('');
				$('#state_entry'+offset).val('');
				$('#other_charges'+offset).val('');
				$('#traveling_date'+offset).val('');
			}
			console.log($('#total_max_km').val());
		},
		error: function (result) {
		}
	});
}

function reflect_feilds() {

	var type = $('#travel_type').val();
	if (type == 'Local') {
		$('#total_hr,#total_km,#local_places_to_visit').show();
		$('#total_max_km,#driver_allowance,#permit,#toll_parking,#state_entry,#other_charges,#places_to_visit,#traveling_date').hide();
	}
	if (type == 'Outstation') {
		$('#total_hr,#total_km,#local_places_to_visit').hide();
		$('#total_max_km,#driver_allowance,#permit,#toll_parking,#state_entry,#other_charges,#places_to_visit,#traveling_date').show();
	}
}

function reflect_feilds1(){
	
	var type = $('#travel_type1').val();
	if(type=='Local'){
		$('#from_date1,#to_date1,#total_hr1,#total_km1,#local_places_to_visit1').show();
		$('#total_max_km,#driver_allowance1,#permit1,#toll_parking1,#state_entry1,#other_charges1,#places_to_visit1,#traveling_date1').hide();
	}
	if(type=='Outstation'){
		$('.update_field,#local_places_to_visit1').hide();
		$('#from_date1,#to_date1,#total_max_km,#driver_allowance1,#permit1,#toll_parking1,#state_entry1,#other_charges1,#places_to_visit1,#traveling_date1').show();
	}
}

function flightQuotationPlaneTable() {
	return document.getElementById('tbl_flight_quotation_dynamic_plane')
		|| document.getElementById('tbl_flight_quotation_dynamic_plane_update');
}

function flightQuotationResetPlaneRows() {
	var table = flightQuotationPlaneTable();
	if (!table) {
		return;
	}
	while (table.rows.length > 1) {
		table.deleteRow(table.rows.length - 1);
	}
}

function flightQuotationRowSuffix(row) {
	if (!row) {
		return '1';
	}
	var fromSel = row.querySelector('select[id^="from_sector-"], input[id^="from_sector-"]');
	if (fromSel && fromSel.id) {
		return fromSel.id.replace(/^from_sector-/, '');
	}
	var chk = row.querySelector('input[type="checkbox"]');
	if (chk && chk.id && chk.id.indexOf('-') > -1) {
		return chk.id.substring(chk.id.lastIndexOf('-') + 1);
	}
	return '1';
}

function flightQuotationSetSector(selectId, value, cityId) {
	var $sel = $('#' + selectId);
	if (!$sel.length) {
		return;
	}
	value = String(value || '').trim();
	if (!value) {
		return;
	}
	if (typeof selectAirportInSector === 'function') {
		selectAirportInSector($sel, value, cityId);
		return;
	}
	if ($sel.find('option').filter(function () { return String(this.value) === value; }).length === 0) {
		$sel.append(new Option(value, value, true, true));
	}
	$sel.val(value).trigger('change');
	if (cityId && typeof syncSectorCityHidden === 'function') {
		syncSectorCityHidden(selectId, cityId);
	}
}

function flightQuotationSetAirline($air, airlineId, airlineLabel) {
	if (!$air || !$air.length) {
		return;
	}
	airlineId = String(airlineId || '').trim();
	airlineLabel = String(airlineLabel || '').trim();
	if (airlineId && $air.find('option[value="' + airlineId.replace(/"/g, '\\"') + '"]').length) {
		$air.val(airlineId).trigger('change');
		return;
	}
	if (airlineLabel) {
		var matched = '';
		$air.find('option').each(function () {
			if (!matched && $.trim($(this).text()).toLowerCase().indexOf(airlineLabel.toLowerCase()) !== -1) {
				matched = this.value;
			}
		});
		if (matched) {
			$air.val(matched).trigger('change');
			return;
		}
		$air.append(new Option(airlineLabel, airlineId || airlineLabel, true, true)).trigger('change');
	}
}

function flightQuotationFillRow(row, flight) {
	if (!row || !flight) {
		return;
	}
	var suffix = flightQuotationRowSuffix(row);
	var $chk = $(row).find('input[type="checkbox"]').first();
	if ($chk.length) {
		$chk.prop('checked', true);
	}
	flightQuotationSetSector('from_sector-' + suffix, flight.sector_from, flight.from_city_id_flight);
	flightQuotationSetSector('to_sector-' + suffix, flight.sector_to, flight.to_city_id_flight);
	flightQuotationSetAirline($('#airline_name-' + suffix), flight.preffered_airline, flight.airline_label);
	if (flight.class_type) {
		var $cls = $('#plane_class-' + suffix);
		if ($cls.length && !$cls.find('option[value="' + String(flight.class_type).replace(/"/g, '\\"') + '"]').length) {
			$cls.append(new Option(flight.class_type, flight.class_type, true, true));
		}
		$cls.val(flight.class_type).trigger('change');
	}
	$('#txt_dapart-' + suffix).val(flight.travel_datetime || '');
	$('#txt_arrval-' + suffix).val(flight.travel_datetime || '');
	$('#adult-' + suffix).val(flight.total_adults_flight || '');
	$('#child-' + suffix).val(flight.total_child_flight || '');
	$('#infant-' + suffix).val(flight.total_infant_flight || '');
	$('#from_city-' + suffix).val(flight.from_city_id_flight || '');
	$('#to_city-' + suffix).val(flight.to_city_id_flight || '');
}

function flightQuotationParseFlights(result) {
	if (result && $.isArray(result.flights) && result.flights.length) {
		return result.flights;
	}
	var raw = result && result.enquiry_content;
	if (!raw) {
		return [];
	}
	if (typeof raw === 'string') {
		try {
			raw = JSON.parse(raw);
		} catch (e) {
			return [];
		}
	}
	if (!$.isArray(raw)) {
		return [];
	}
	return raw.filter(function (row) {
		return row && (row.sector_from || row.sector_to || row.travel_datetime);
	});
}

function get_flight_enquiry_details(offset) {
	offset = offset || '';
	var enquiry_id = $('#enquiry_id' + offset).val();
	var base_url = $('#base_url').val();
	if (enquiry_id != '' && enquiry_id != 0) {
		$.ajax({
			type: 'GET',
			url: base_url + 'view/package_booking/quotation/car_flight/flight/get_enquiry_details.php',
			dataType: 'json',
			data: { enquiry_id: enquiry_id },
			success: function (result) {
				if (!result) {
					return;
				}
				$('#customer_name' + offset).val(result.name || '');
				$('#email_id' + offset).val(result.email_id || '');
				$('#mobile_no' + offset).val(result.landline_no || result.mobile_no || '');
				if (result.country_code) {
					$('#country_code' + offset).val(result.country_code).trigger('change');
				}
				var flights = flightQuotationParseFlights(result);
				window._flightEnquiryFlights = flights;
				var table = flightQuotationPlaneTable();
				if (!table) {
					return;
				}
				flightQuotationResetPlaneRows();
				if (!flights.length) {
					return;
				}
				function fillNext(index) {
					if (index >= flights.length) {
						if (typeof refreshPlaneAirportSelect2In === 'function') {
							refreshPlaneAirportSelect2In('#' + table.id);
						}
						return;
					}
					if (index > 0) {
						addRow(table.id);
					}
					flightQuotationFillRow(table.rows[table.rows.length - 1], flights[index]);
					fillNext(index + 1);
				}
				fillNext(0);
			},
			error: function (result) {
				console.log(result.responseText);
			}
		});
	}
	else {
		window._flightEnquiryFlights = [];
		$('#customer_name' + offset).val('');
		$('#email_id' + offset).val('');
		$('#mobile_no' + offset).val('');
		flightQuotationResetPlaneRows();
		var table = flightQuotationPlaneTable();
		if (!table || !table.rows.length) {
			return;
		}
		var row = table.rows[0];
		var suffix = flightQuotationRowSuffix(row);
		$('#from_sector-' + suffix).val('').trigger('change');
		$('#to_sector-' + suffix).val('').trigger('change');
		$('#airline_name-' + suffix).val('').trigger('change');
		$('#plane_class-' + suffix).val('');
		$('#txt_dapart-' + suffix).val('');
		$('#txt_arrval-' + suffix).val('');
		$('#adult-' + suffix).val('');
		$('#child-' + suffix).val('');
		$('#infant-' + suffix).val('');
		$('#from_city-' + suffix).val('');
		$('#to_city-' + suffix).val('');
	}
}

$(document).off('shown.bs.tab.flightEnquiry').on('shown.bs.tab.flightEnquiry', 'a[href="#tab2"]', function () {
	var table = flightQuotationPlaneTable();
	if (!table) {
		return;
	}
	var flights = window._flightEnquiryFlights || [];
	if (flights.length) {
		var fromVal = $(table.rows[0]).find('select[id^="from_sector-"]').val();
		if (!fromVal) {
			flightQuotationResetPlaneRows();
			for (var i = 0; i < flights.length; i++) {
				if (i > 0) {
					addRow(table.id);
				}
				flightQuotationFillRow(table.rows[table.rows.length - 1], flights[i]);
			}
		}
	}
	if (typeof refreshPlaneAirportSelect2In === 'function') {
		refreshPlaneAirportSelect2In('#' + table.id);
	}
});

function get_capacity(){
	var vehicle_name = $('#vehicle_name').val();
	var travel_type = $('#travel_type').val();
	var base_url = $('#base_url').val();
	$.ajax({
		type:'post',
		url: base_url+'view/package_booking/quotation/car_flight/car_rental/get_capacity.php',
		data: { travel_type : travel_type, vehicle_name: vehicle_name },
		success: function(result){
			$('#capacity').val(result);
		}
	});
}

function add_itinerary_car_quotation(dest_id1, spa, dwp, ovs, meal, dayp) {
	var day_id = dayp.split('-');
	var $btn = $('#itinerary' + day_id[1]);
	$btn.prop('disabled', true);
	var base_url = $('#base_url').val();
	var dest_id = 0;
	if (dest_id1 && dest_id1 !== 0 && dest_id1 !== '0') {
		dest_id = $('#' + dest_id1).val() || 0;
	}
	$btn.button('loading');
	$.post(base_url + 'view/car_rental/booking/itinerary_modal.php', {
		dest_id: dest_id,
		spa: spa,
		dwp: dwp,
		ovs: ovs,
		meal: meal,
		dayp: dayp
	}, function (data) {
		$btn.button('reset');
		$btn.prop('disabled', false);
		var $container = $('#div_itinerary_modal').last();
		$container.html(data);
		if (typeof init_car_rental_itinerary_modal === 'function') {
			init_car_rental_itinerary_modal();
		}
	});
}

function sync_car_itinerary_count() {
	var count = $('#itinerary_data .sq_itinerary_count').first().val();
	if (count !== undefined && count !== '') {
		$('#sq_itinerary_c1').val(count);
	}
}

function get_dest_itinerary_car_quotation(dest_id1) {
	if (typeof get_dest_itinerary_car_rental === 'function') {
		return get_dest_itinerary_car_rental(dest_id1);
	}
    var base_url = $('#base_url').val();
    var dest_id = $('#' + dest_id1).val();
    if (dest_id == '' || dest_id == 0) {
        $('#itinerary_data').html('');
        $('#sq_itinerary_c1').val(0);
        return false;
    }
    $.post(base_url + 'view/car_rental/booking/get_itinerary_data.php', { dest_id: dest_id }, function (data) {
        $('#itinerary_data').html(data);
        sync_car_itinerary_count();
        if (typeof initItineraryImagePreview === 'function') {
            initItineraryImagePreview($('#itinerary_data'));
        }
        $("input[type='checkbox']", '#itinerary_detail_modal').labelauty({ label: false, maximum_width: '20px' });
    });
}

function restore_car_quotation_parent_modal_scroll() {
	if ($('#quotation_save_modal').is(':visible') || $('#quotation_update_modal').is(':visible')) {
		$('body').addClass('modal-open');
		var $backdrops = $('.modal-backdrop');
		if ($backdrops.length > 1) {
			$backdrops.last().remove();
		}
	}
}

window.add_itinerary_car_quotation = add_itinerary_car_quotation;
window.get_dest_itinerary_car_quotation = get_dest_itinerary_car_quotation;
window.sync_car_itinerary_count = sync_car_itinerary_count;
window.restore_car_quotation_parent_modal_scroll = restore_car_quotation_parent_modal_scroll;

function flightQuotationCellControl(cell) {
	if (typeof getCellFormControl === 'function') {
		return getCellFormControl(cell);
	}
	if (!cell) {
		return null;
	}
	for (var i = 0; i < cell.childNodes.length; i++) {
		if (cell.childNodes[i].nodeType === 1) {
			return cell.childNodes[i];
		}
	}
	return null;
}

function flightQuotationCellValue(cell) {
	var el = flightQuotationCellControl(cell);
	if (!el) {
		return '';
	}
	var $el = $(el);
	if ($el.data('select2')) {
		var v = $el.val();
		return (v === null || v === undefined) ? '' : String(v);
	}
	return el.value || '';
}

function isFlightQuotationPlaneRowChecked(row) {
	var chk = flightQuotationCellControl(row.cells[0]);
	return !!(chk && chk.checked);
}

function getFlightQuotationPlaneRowData(row) {
	return {
		from_sector: flightQuotationCellValue(row.cells[2]),
		to_sector: flightQuotationCellValue(row.cells[3]),
		airline_name: flightQuotationCellValue(row.cells[4]),
		plane_class: flightQuotationCellValue(row.cells[5]),
		total_adult: flightQuotationCellValue(row.cells[6]),
		total_child: flightQuotationCellValue(row.cells[7]),
		total_infant: flightQuotationCellValue(row.cells[8]),
		dapart: flightQuotationCellValue(row.cells[9]),
		arraval: flightQuotationCellValue(row.cells[10]),
		from_city_id: flightQuotationCellValue(row.cells[11]),
		to_city_id: flightQuotationCellValue(row.cells[12]),
		plane_id: (row.cells[13] ? flightQuotationCellValue(row.cells[13]) : '')
	};
}

function flight_quotation_cost_calculate(offset = '') {
	var quotation_cost = 0;
	var subtotal = $('#subtotal' + offset).val();
	var service_charge = $('#service_charge' + offset).val();
	var service_tax_subtotal = $('#service_tax' + offset).val();
	var markup_cost = $('#markup_cost' + offset).val();
	var service_tax_markup = $('#markup_cost_subtotal' + offset).val();

	if (subtotal == '') {
		subtotal = 0;
	}
	if (markup_cost == '') {
		markup_cost = 0;
	}
	if (service_charge == '') {
		service_charge = 0;
	}

	var service_tax_amount = 0;
	if (parseFloat(service_tax_subtotal) !== 0.00 && (service_tax_subtotal) !== '') {

		var service_tax_subtotal1 = service_tax_subtotal.split(",");
		for (var i = 0; i < service_tax_subtotal1.length; i++) {
			var service_tax = service_tax_subtotal1[i].split(':');
			service_tax_amount = parseFloat(service_tax_amount) + parseFloat(service_tax[2]);
		}
	}
	var markupservice_tax_amount = 0;
	if (parseFloat(service_tax_markup) !== 0.00 && (service_tax_markup) !== "") {
		var service_tax_markup1 = service_tax_markup.split(",");
		for (var i = 0; i < service_tax_markup1.length; i++) {
			var service_tax = service_tax_markup1[i].split(':');
			markupservice_tax_amount = parseFloat(markupservice_tax_amount) + parseFloat(service_tax[2]);
		}
	}

	subtotal = ($('#basic_show' + offset).html() == '&nbsp;') ? subtotal : parseFloat($('#basic_show' + offset).text().split(' : ')[1]);
	service_charge = ($('#service_show' + offset).html() == '&nbsp;') ? service_charge : parseFloat($('#service_show' + offset).text().split(' : ')[1]);
	markup_cost = ($('#markup_show' + offset).html() == '&nbsp;') ? markup_cost : parseFloat($('#markup_show' + offset).text().split(' : ')[1]);

	total_tour_cost = parseFloat(subtotal) + parseFloat(service_charge) + parseFloat(service_tax_amount) + parseFloat(markup_cost) + parseFloat(markupservice_tax_amount);

	// var roundoff = Math.round(total_tour_cost) - total_tour_cost;
	// $('#roundoff' + offset).val(roundoff.toFixed(2));

	// quotation_cost = parseFloat(total_tour_cost) + parseFloat(roundoff);

	// $('#total_tour_cost' + offset).val(quotation_cost.toFixed(2));



	// No rounding, roundoff = 0.00 always
var roundoff = 0.00;
$('#roundoff' + offset).val(roundoff.toFixed(2));

// Keep the total cost as it is, without rounding
quotation_cost = parseFloat(total_tour_cost);
$('#total_tour_cost' + offset).val(quotation_cost.toFixed(2));

}




