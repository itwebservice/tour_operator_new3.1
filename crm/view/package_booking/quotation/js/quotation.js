$('#train_arrival_date1, #train_departure_date1').datetimepicker({ format: 'd-m-Y H:i' });
$('#transport_start_date1, #transport_end_date1').datetimepicker({ timepicker: false, format: 'd-m-Y' });

function total_days_reflect(offset = '') {
	var from_date = $('#from_date' + offset).val() || '';
	var to_date = $('#to_date' + offset).val() || '';

	if (from_date && typeof syncQuotationTravelStayDates === 'function') {
		syncQuotationTravelStayDates();
	}

	if (!from_date || !to_date) {
		if (!from_date && !to_date) {
			$('#total_days' + offset).val(0);
		}
		return;
	}

	var edate = from_date.split('-');
	e_date = new Date(edate[2], edate[1] - 1, edate[0]).getTime();
	var edate1 = to_date.split('-');
	e_date1 = new Date(edate1[2], edate1[1] - 1, edate1[0]).getTime();

	var one_day = 1000 * 60 * 60 * 24;

	var from_date_ms = new Date(e_date).getTime();
	var to_date_ms = new Date(e_date1).getTime();

	var difference_ms = to_date_ms - from_date_ms;
	var total_days = Math.round(Math.abs(difference_ms) / one_day);

	total_days = parseFloat(total_days);
	$('#total_days' + offset).val(total_days);
		// Store nights for package filtering (only for main form, not other offsets)
		if (offset === '') {
			sessionStorage.setItem('selected_nights', total_days);
			console.log('Stored nights in sessionStorage:', total_days);
			
			// Reset user modification flag when dates change (allows re-sync)
			sessionStorage.removeItem('user_modified_nights');
			console.log('Reset user_modified_nights flag due to date change');
			
			// Auto-update nights filter on tab2 if it exists
			if ($('#nights_filter').length > 0) {
				$('#nights_filter').val(total_days);
				$('#nights_filter').trigger('change');
				console.log('Auto-updated nights filter to:', total_days);
				
				// Trigger package filtering if destination is already selected
				if ($('#dest_name').val()) {
					console.log('Triggering package filtering with updated nights');
					if (typeof load_packages_with_filter === 'function') {
						load_packages_with_filter();
					} else if (typeof package_dynamic_reflect === 'function') {
						package_dynamic_reflect('dest_name');
					}
				}
			} else {
				// If tab2 is not loaded yet, set up a listener for when it becomes available
				console.log('Tab2 not loaded yet, setting up listener for nights filter');
				$(document).on('DOMNodeInserted', function(e) {
					if ($(e.target).find('#nights_filter').length > 0 || $(e.target).is('#nights_filter')) {
						$('#nights_filter').val(total_days);
						$('#nights_filter').trigger('change');
						console.log('Auto-updated nights filter after tab2 loaded:', total_days);
					}
				});
			}
		}
}

function package_dynamic_reflect(dest_name) {
	var dest_id = $('#' + dest_name).val();
	var base_url = $('#base_url').val();
	
	// Get total_nights from multiple sources
	var total_nights = $('#nights_filter').val() || sessionStorage.getItem('selected_nights') || $('#total_days').val() || $('#total_days12').val();

	// Ensure total_nights is not null or undefined
	if (!total_nights) {
		total_nights = '';
	}

	var ajax_data = { 
		dest_id: dest_id,
		total_nights: total_nights
	};

	$.ajax({
		type: 'post',
		url: base_url + 'view/package_booking/quotation/inc/get_packages.php?v=' + Date.now(),
		data: ajax_data,
		success: function (result) {
			$('#package_name_div').html(result);
		},
		error: function (result) {
			console.log('Package loading error:', result.responseText);
		}
	});
}

/////////////////////////////////////Site seeing related info start/////////////////////////////////////

function citywise_site_seeing_dynamic_reflect(city_id) {
	if (city_id == '') {
		$('#ul_site_seeing_list li').removeClass('hidden');
	}
	else {
		$('#ul_site_seeing_list li').addClass('hidden');
		$('#ul_site_seeing_list li[data-city-id="' + city_id + '"]').removeClass('hidden');
	}
}

/////////////////////////////////////Site seeing related info end/////////////////////////////////////

function total_passangers_calculate(offset = '') {
	var total_adult = $('#total_adult' + offset).val();
	var children_with_bed = $('#children_with_bed' + offset).val();
	var children_without_bed = $('#children_without_bed' + offset).val();
	var total_infant = $('#total_infant' + offset).val();
	if(document.getElementById("single_person"+offset)){
		var single_person = $('#single_person' + offset).val();
	}else{
		var single_person = 0;
	}

	if (total_adult == '') total_adult = 0;
	if (children_with_bed == '') children_with_bed = 0;
	if (children_without_bed == '') children_without_bed = 0;
	if (total_infant == '') total_infant = 0;
	if(single_person == '') single_person = 0;

	var total_passangers = parseFloat(total_adult) + parseFloat(total_infant) + parseFloat(children_with_bed) + parseFloat(children_without_bed) + parseFloat(single_person);
	$('#total_passangers' + offset).val(total_passangers);
}

function group_quotation_cost_calculate(id) {

	var adult_cost = $('#adult_cost').val();
	var children_cost = $('#children_cost').val();
	var infant_cost = $('#infant_cost').val();
	var with_bed_cost = $('#with_bed_cost').val();
	var single_person_cost = $('#single_person_cost').val();

	if (adult_cost == '' || isNaN(adult_cost)) adult_cost = 0;
	if (children_cost == '' || isNaN(children_cost)) children_cost = 0;
	if (infant_cost == '' || isNaN(infant_cost)) infant_cost = 0;
	if (with_bed_cost == '' || isNaN(with_bed_cost)) with_bed_cost = 0;
	if (single_person_cost == '' || isNaN(single_person_cost)) single_person_cost = 0;

	var total = parseFloat(adult_cost) + parseFloat(children_cost) + parseFloat(infant_cost) + parseFloat(with_bed_cost) + parseFloat(single_person_cost);
	$('#tour_cost').val(total.toFixed(2));

	if (id != 'tour_cost') {
		$('#tour_cost').trigger('change');
	}
	var service_charge = $('#service_charge').val();
	if (service_charge == '' || isNaN(service_charge)) service_charge = 0;
	var service_tax_subtotal = $('#service_tax_subtotal').val();
	var service_tax_amount = 0;
	if (parseFloat(service_tax_subtotal) !== 0.0 && service_tax_subtotal !== ''){
		var service_tax_subtotal1 = service_tax_subtotal.split(',');
		for (var i = 0; i < service_tax_subtotal1.length; i++){
			var service_tax = service_tax_subtotal1[i].split(':');
			service_tax_amount = parseFloat(service_tax_amount) + parseFloat(service_tax[2]);
		}
	}
	total = ($('#basic_show').html() == '&nbsp;') ? total : parseFloat($('#basic_show').text().split(' : ')[1]);
	service_charge = ($('#service_show').html() == '&nbsp;') ? service_charge : parseFloat($('#service_show').text().split(' : ')[1]);
	if (isNaN(total)) total = 0;
	if (isNaN(service_charge)) service_charge = 0;
	if (isNaN(service_tax_amount)) service_tax_amount = 0;

	var totalTcs = parseFloat($('#tcs1-').val());
	if (isNaN(totalTcs)) totalTcs = 0;

	var total_tour_cost1 = parseFloat(total) + parseFloat(service_tax_amount) + parseFloat(service_charge) + parseFloat(totalTcs);
	$('#total_tour_cost').val(Math.round(total_tour_cost1));
}
function quotationCostingFieldSuffix(fieldId) {
	if (!fieldId) {
		return '-';
	}
	var prefixes = [
		'transport_cost1', 'excursion_cost', 'basic_amount', 'service_charge',
		'discount_amt', 'discount_in', 'tax_apply_on', 'tax_value',
		'service_tax_subtotal', 'tcs_tax', 'tcs1', 'total_tour_cost',
		'package_type', 'package_id1', 'package_name1', 'tour_cost'
	];
	for (var i = 0; i < prefixes.length; i++) {
		if (fieldId.indexOf(prefixes[i]) === 0) {
			return fieldId.slice(prefixes[i].length);
		}
	}
	var dash = fieldId.indexOf('-');
	return dash >= 0 ? fieldId.slice(dash) : '-';
}

function quotation_cost_calculate(id) {

	if (!id) {
		id = 'tour_cost-';
	}
	var suffix = quotationCostingFieldSuffix(id);
	var tour_cost = $('#tour_cost' + suffix).val();
	var transport_cost = $('#transport_cost1' + suffix).val();
	var excursion_cost = $('#excursion_cost' + suffix).val();
	var discount_in = $('#discount_in' + suffix).val();
	var discount_amt = $('#discount_amt' + suffix).val();


	if (tour_cost == '') {
		tour_cost = 0;
	}
	if (transport_cost == '') {
		transport_cost = 0;
	}
	if (excursion_cost == '') {
		excursion_cost = 0;
	}
	if (discount_amt == '') {
		discount_amt = 0;
	}

	var sub_total = parseFloat(tour_cost) + parseFloat(transport_cost) + parseFloat(excursion_cost);
	$('#basic_amount' + suffix).val(sub_total.toFixed(2));

	if (id != 'basic_amount' + suffix && typeof get_business === 'function') {
		get_business('basic_amount' + suffix, 'true');
	}
	var service_charge = $('#service_charge' + suffix).val();
	var service_tax_subtotal = $('#service_tax_subtotal' + suffix).val();
	if (service_charge == '') {
		service_charge = 0;
	}
	var service_tax_amount = 0;
	if (parseFloat(service_tax_subtotal) !== 0.0 && service_tax_subtotal !== '' && typeof service_tax_subtotal != 'undefined') {
		var service_tax_subtotal1 = service_tax_subtotal.split(',');
		for (var i = 0; i < service_tax_subtotal1.length; i++) {
			var service_tax = service_tax_subtotal1[i].split(':');
			service_tax_amount = parseFloat(service_tax_amount) + parseFloat(service_tax[2]);
		}
	}
	var discountable_amt = parseFloat(service_charge);
	if(discount_in == 'Percentage'){
		var discount = parseFloat(discountable_amt) * parseFloat(discount_amt) / 100;
	}
	else{
		var discount = (service_charge != 0) ? parseFloat(discount_amt) : 0;
	}
	var after_discount_amt = parseFloat(discountable_amt) - parseFloat(discount);
	var basicShowText = $('#basic_show' + suffix).text();
	if ($('#basic_show' + suffix).html() != '&nbsp;' && basicShowText.indexOf(' : ') >= 0) {
		var parsedBasic = parseFloat(basicShowText.split(' : ')[1]);
		if (!isNaN(parsedBasic)) {
			sub_total = parsedBasic;
		}
	}
	var serviceShowText = $('#service_show' + suffix).text();
	if ($('#service_show' + suffix).html() != '&nbsp;' && serviceShowText.indexOf(' : ') >= 0) {
		var parsedService = parseFloat(serviceShowText.split(' : ')[1]);
		if (!isNaN(parsedService)) {
			service_charge = parsedService;
		}
	}
	if (isNaN(sub_total)) {
		sub_total = parseFloat($('#basic_amount' + suffix).val()) || 0;
	}
	if (isNaN(service_charge)) {
		service_charge = 0;
	}
	if (isNaN(after_discount_amt)) {
		after_discount_amt = 0;
	}
	if (isNaN(service_tax_amount)) {
		service_tax_amount = 0;
	}
    customTcsTax('tcs_tax' + suffix);

    var tcs_amt = $('#tcs1' + suffix).val();
    if (tcs_amt == '') {
        tcs_amt = 0;
    }
	
	var total_amt = parseFloat(sub_total) + parseFloat(after_discount_amt) + parseFloat(service_tax_amount) + parseFloat(tcs_amt);
	if (isNaN(total_amt)) {
		total_amt = parseFloat(sub_total) + parseFloat(service_charge) + parseFloat(service_tax_amount);
	}
	$('#total_tour_cost' + suffix).val(Math.round(total_amt).toFixed(2));
}

var quotationLastEnquiryId = null;

function clearQuotationWorkflowState() {
	[
		'selected_destination_id',
		'selected_destination_name',
		'selected_nights',
		'user_modified_nights',
		'selected_packages_tab3',
		'hotel_table_state_tab3',
		'itinerary_data',
		'is_ai_quotation',
		'quotation_dest_id',
		'quotation_refer_id'
	].forEach(function (key) {
		sessionStorage.removeItem(key);
	});
}

function clearQuotationPackageListUi() {
	if ($('#package_name_div').length) {
		$('#package_name_div').empty();
	}
}

function resetPackageSelectorRadios() {
	$('input[name="custom_package"]').each(function() {
		this.checked = false;
		$(this).prop('checked', false).removeAttr('checked');
	});

	$('#accordion .panel-collapse.in').removeClass('in').addClass('collapse').attr('aria-expanded', 'false').css('height', '');
	$('#accordion [data-toggle="collapse"]').addClass('collapsed').attr('aria-expanded', 'false');

	sessionStorage.removeItem('selected_packages_tab3');
}

function blockPackageSelectInAiMode(radioEl) {
	if ($('#aiBuilder').is(':checked') || $('#is_ai_quotation').val() === '1' || sessionStorage.getItem('is_ai_quotation') === '1') {
		if (radioEl) {
			radioEl.checked = false;
			$(radioEl).prop('checked', false).removeAttr('checked');
		} else {
			resetPackageSelectorRadios();
		}
		return false;
	}
	return true;
}

$(document).on('change', 'input[name="custom_package"]', function() {
	blockPackageSelectInAiMode(this);
});

$(document).on('change', 'input[name="quotation_package"]', function() {
	if (typeof package_booking_reflect === 'function') {
		package_booking_reflect();
	}
});

function quotationResetPackageLoadCache() {
	if (typeof window.__quotationResetPackageCache === 'function') {
		window.__quotationResetPackageCache();
	}
}

function quotationClearEnquiryFormFields(offset) {
	offset = offset || '';
	$('#tour_name' + offset).val('');
	$('#total_days' + offset).val(0);
	$('#customer_name' + offset).val('');
	$('#email_id' + offset).val('');
	$('#mobile_no' + offset).val('');
	$('#from_date' + offset).val('');
	$('#to_date' + offset).val('');
	$('#total_adult' + offset).val('');
	$('#total_infant' + offset).val('');
	$('#children_without_bed' + offset).val('');
	$('#children_with_bed' + offset).val('');
	$('#single_person' + offset).val('');
	$('#total_passangers' + offset).val(0);
	$('#user_dropdown').html('').addClass('hidden');
	if ($('#dest_name').length) {
		$('#dest_name').val('').trigger('change.select2');
	}
}

function quotationOnEnquiryChange(enquiry_id) {
	var newId = (enquiry_id === null || enquiry_id === undefined) ? '' : String(enquiry_id);
	if (quotationLastEnquiryId !== null && quotationLastEnquiryId !== newId) {
		clearQuotationWorkflowState();
		clearQuotationPackageListUi();
		quotationResetPackageLoadCache();
	}
	quotationLastEnquiryId = newId;
	sessionStorage.setItem('quotation_active_enquiry_id', newId);
}

function initQuotationSavePageSession() {
	var pageId = $('#unique_timestamp').val();
	if (!pageId) {
		return;
	}
	var storedPageId = sessionStorage.getItem('quotation_page_id');
	if (storedPageId !== pageId) {
		clearQuotationWorkflowState();
		quotationLastEnquiryId = null;
		sessionStorage.setItem('quotation_page_id', pageId);
		clearQuotationPackageListUi();
		quotationResetPackageLoadCache();
	}
	var activeEnquiry = sessionStorage.getItem('quotation_active_enquiry_id');
	if (activeEnquiry) {
		quotationLastEnquiryId = activeEnquiry;
	}
}

function syncDestinationFromTab1(forceReload) {
	var tourName = ($('#tour_name').val() || '').trim();
	if (!tourName) {
		if ($('#dest_name').length) {
			$('#dest_name').val('').trigger('change.select2');
		}
		return;
	}

	var destId = '';
	var destinations = JSON.parse($('#destinations').val() || '[]');
	for (var i = 0; i < destinations.length; i++) {
		if (destinations[i].label === tourName) {
			destId = destinations[i].dest_id;
			sessionStorage.setItem('selected_destination_id', destId);
			sessionStorage.setItem('selected_destination_name', destinations[i].label);
			break;
		}
	}

	if (!destId) {
		destId = sessionStorage.getItem('selected_destination_id') || '';
	}

	if (!$('#dest_name').length || !destId) {
		return;
	}

	var currentDestId = String($('#dest_name').val() || '');
	if (currentDestId !== String(destId) || forceReload) {
		quotationResetPackageLoadCache();
		if (forceReload) {
			clearQuotationPackageListUi();
		}
		$('#dest_name').val(destId).trigger('change');
	} else if (forceReload && typeof load_packages_with_filter === 'function') {
		quotationResetPackageLoadCache();
		clearQuotationPackageListUi();
		load_packages_with_filter(true);
	}
}

function get_enquiry_details(offset = '') {
	var enquiry_id = $('#enquiry_id' + offset).val();
	quotationOnEnquiryChange(enquiry_id);

	if (enquiry_id === '' || enquiry_id === null || typeof enquiry_id === 'undefined') {
		quotationClearEnquiryFormFields(offset);
		return;
	}

	if (String(enquiry_id) === '0') {
		quotationClearEnquiryFormFields(offset);
		clearQuotationWorkflowState();
		clearQuotationPackageListUi();
		quotationResetPackageLoadCache();
		return;
	}

	var base_url = $('#base_url').val();
	$.ajax({
		type: 'post',
		url: base_url + 'view/package_booking/quotation/get_enquiry_details.php',
		dataType: 'json',
		data: { enquiry_id: enquiry_id },
		success: function (result) {
			if (!result) {
				return;
			}

			quotationResetPackageLoadCache();
			clearQuotationPackageListUi();

			$('#tour_name' + offset).val(result.tour_name);
			$('#total_days' + offset).val(result.total_days);
			
			// Update sessionStorage with enquiry data
			if (result.tour_name) {
				console.log('Enquiry loaded - Processing tour_name:', result.tour_name);
				
				// Clear existing destination storage first
				sessionStorage.removeItem('selected_destination_id');
				sessionStorage.removeItem('selected_destination_name');
				
				// Find destination ID from the destinations list
				var destinations = JSON.parse($('#destinations').val() || '[]');
				console.log('Enquiry loaded - Available destinations:', destinations.length);
				console.log('Enquiry loaded - First few destinations:', destinations.slice(0, 3));
				
				var found = false;
				for (var i = 0; i < destinations.length; i++) {
					console.log('Enquiry loaded - Checking destination:', destinations[i].label, 'against:', result.tour_name);
					if (destinations[i].label === result.tour_name) {
						sessionStorage.setItem('selected_destination_id', destinations[i].dest_id);
						sessionStorage.setItem('selected_destination_name', destinations[i].label);
						console.log('Enquiry loaded - Updated sessionStorage with destination:', destinations[i].label, 'ID:', destinations[i].dest_id);
						found = true;
						break;
					}
				}
				
				if (!found) {
					console.log('Enquiry loaded - Destination not found in list:', result.tour_name);
					console.log('Enquiry loaded - Available destinations:', destinations.map(d => d.label));
					
					// Try to add the destination to the dropdown and create a temporary ID
					var tempId = 'temp_' + Date.now();
					var newOption = $("<option selected='selected'></option>").val(tempId).text(result.tour_name);
					$('#dest_name').append(newOption).trigger('change.select2');
					
					// Store with temporary ID
					sessionStorage.setItem('selected_destination_id', tempId);
					sessionStorage.setItem('selected_destination_name', result.tour_name);
					console.log('Enquiry loaded - Added temporary destination with ID:', tempId);
				}
			}
			
			// Update nights in sessionStorage
			if (result.total_days) {
				sessionStorage.setItem('selected_nights', result.total_days);
				sessionStorage.setItem('user_modified_nights', 'true');
				console.log('Enquiry loaded - Updated sessionStorage with nights:', result.total_days);
			}
			
			// Trigger Tab 2 sync if it exists (after sessionStorage is updated)
			setTimeout(function () {
				syncDestinationFromTab1(true);
				console.log('Enquiry loaded - Triggered Tab 2 sync with package reload');
			}, 200);
			$('#customer_name' + offset).val(result.name);
			$('#email_id' + offset).val(result.email_id);
			$('#mobile_no' + offset).val(result.landline_no);
			$('#country_code' + offset).val(result.country_code);
			$('#country_code').trigger('change');

			if (result.total_adult === undefined) result.total_adult = 0;
			if (result.total_infant === undefined) result.total_infant = 0;
			if (result.children_without_bed === undefined) result.children_without_bed = 0;
			if (result.children_with_bed === undefined) result.children_with_bed = 0;
			if (result.total_single_person === undefined || result.total_single_person == '') result.total_single_person = 0;
			$('#total_adult' + offset).val(result.total_adult);
			$('#total_infant' + offset).val(result.total_infant);
			$('#total_adult1' + offset).val(result.total_adult);
			$('#children_without_bed' + offset).val(result.children_without_bed);
			$('#children_with_bed' + offset).val(result.children_with_bed);
			$('#single_person' + offset).val(result.total_single_person);
			if(result.user_id != '0'){
				
                $('#user_dropdown').removeClass('hidden');
                $('#user_dropdown').html('<select id="user_id" name="user_id" title="User" class="form-control"><option value='+result.user_id+'>'+result.user_name+'</option><option value="">Select User</option></select>');
			}else{
				$('#user_dropdown').html('');
				$('#user_dropdown').addClass('hidden');
			}

			var total_pax = parseFloat(result.total_adult) + parseFloat(result.children_without_bed) + parseFloat(result.children_with_bed) + parseFloat(result.total_infant) + parseFloat(result.total_single_person);
			if (total_pax == '') total_pax = 0;
			$('#total_passangers' + offset).val(total_pax);
			if (typeof cost_reflect === 'function') {
				cost_reflect();
			}
			$('#from_date' + offset).val(result.travel_from_date);
			$('#to_date' + offset).val(result.travel_to_date);
			if($('#enquiry_id').val() == '0'){

                $('#user_dropdown').removeClass('hidden');
                $('#user_dropdown').html('<select id="user_id" name="user_id" title="User" class="form-control"><option value="">Select User</option></select>');
				var from_date = $('#from_date' + offset).val();
				$('#train_departure_date').val(from_date + ' 00:00');
				$('#txt_dapart1').val(from_date + ' 00:00');
				$('#cruise_departure_date').val(from_date + ' 00:00');
				
				$('#train_dept_date_hidde').val(from_date + ' 00:00');
				$('#cruise_dept_date_hidde').val(from_date + ' 00:00');
				$('#plane_dept_date_hidde').val(from_date + ' 00:00');
	
				$('#train_arrival_date').val(from_date + ' 00:00');
				$('#txt_arrval1').val(from_date + ' 00:00');
				$('#cruise_arrival_date').val(from_date + ' 00:00');
				$('#exc_date-1').val(from_date + ' 00:00');
				$('#mobile_no' + offset).val('');
			}
			else{

				$('#train_departure_date').val(result.travel_from_date + ' 00:00');
				$('#txt_dapart1').val(result.travel_from_date + ' 00:00');
				$('#cruise_departure_date').val(result.travel_from_date + ' 00:00');
				
				$('#train_dept_date_hidde').val(result.travel_from_date + ' 00:00');
				$('#cruise_dept_date_hidde').val(result.travel_from_date + ' 00:00');
				$('#plane_dept_date_hidde').val(result.travel_from_date + ' 00:00');
	
				$('#train_arrival_date').val(result.travel_from_date + ' 00:00');
				$('#txt_arrval1').val(result.travel_from_date + ' 00:00');
				$('#cruise_arrival_date').val(result.travel_from_date + ' 00:00');
				$('#exc_date-1').val(result.travel_from_date + ' 00:00');
			}
			total_days_reflect(offset);
		},
		error: function (result) {
			//console.log(result.responseText);
		}
	});
}
//Get To Date
function get_auto_to_date(from_date) {

	var from_date1 = $('#' + from_date).val();
	var suffix = String(from_date || '').replace(/^check_in-/, '');
	var checkOutId = 'check_out-' + suffix;
	if (from_date1 != '') {
		var nights = 1;
		var stayEl = document.getElementById('hotel_stay_days-' + suffix);
		if (stayEl && parseInt(stayEl.value, 10) > 0) {
			nights = parseInt(stayEl.value, 10);
		}
		if (typeof quotationAddDays === 'function') {
			$('#' + checkOutId).val(quotationAddDays(from_date1, nights));
		} else {
			var edate = from_date1.split('-');
			var currentDate = new Date(edate[2], edate[1] - 1, edate[0]);
			currentDate.setDate(currentDate.getDate() + nights);
			var day = currentDate.getDate();
			var month = currentDate.getMonth() + 1;
			var year = currentDate.getFullYear();
			if (day < 10) day = '0' + day;
			if (month < 10) month = '0' + month;
			$('#' + checkOutId).val(day + '-' + month + '-' + year);
		}
	}
	else {
		$('#' + checkOutId).val('');
	}
	calculate_total_nights(checkOutId);
}

function calculate_total_nights(to_date1) {

	var offset = to_date1.split('-');
	var from_date = $('#check_in-' + offset[1]).val();
	var to_date = $('#' + to_date1).val();
	if (from_date != '' && to_date != '') {
		var edate = from_date.split('-');
		e_date = new Date(edate[2], edate[1] - 1, edate[0]).getTime();
		var edate1 = to_date.split('-');
		e_date1 = new Date(edate1[2], edate1[1] - 1, edate1[0]).getTime();

		var one_day = 1000 * 60 * 60 * 24;

		var from_date_ms = new Date(e_date).getTime();
		var to_date_ms = new Date(e_date1).getTime();

		var difference_ms = to_date_ms - from_date_ms;
		var total_days = Math.round(Math.abs(difference_ms) / one_day);

		total_days = parseFloat(total_days);
		$('#hotel_stay_days-' + offset[1]).val(total_days);
	}
	else {
		$('#hotel_stay_days-' + offset[1]).val(0);
	}

}

//function for valid to date
function validate_validDates(to) {

	var offset = to.split('-');
	var from_date = $('#check_in-' + offset[1]).val();
	var to_date1 = $('#' + to).val();

	var edate = from_date.split('-');
	e_date = new Date(edate[2], edate[1] - 1, edate[0]).getTime();
	var edate1 = to_date1.split('-');
	e_date1 = new Date(edate1[2], edate1[1] - 1, edate1[0]).getTime();

	var from_date_ms = new Date(e_date).getTime();
	var to_date_ms = new Date(e_date1).getTime();

	if (from_date_ms > to_date_ms) {
		error_msg_alert('Date should not be greater than valid to date');
		$('#check_in-' + offset[1]).css({ border: '1px solid red' });
		$('#check_in-' + offset[1]).focus();
		g_validate_status = false;
		return false;
	}
	else {
		$('#check_in-' + offset[1]).css({ border: '1px solid #ddd' });
		return true;
	}
}
function validate_pax_count(id,type){
	
	var pax_count = $('#'+id).val();
	var total_adult = $('#total_adult').val();
	var children_with_bed = $('#children_with_bed').val();
	var children_without_bed = $('#children_without_bed').val();
	var total_infant = $('#total_infant').val();
	var entered_count = 0;
	if(type=='Adult'){
		entered_count = parseInt(total_adult);
	}else if(type=='ChildWithBed'){
		entered_count = parseInt(children_with_bed);
	}else if(type=='ChildWithoutBed'){
		entered_count = parseInt(children_without_bed);
	}else if(type=='Infant'){
		entered_count = parseInt(total_infant);
	}
	if(entered_count < parseInt(pax_count)){
		error_msg_alert("Invalid "+type+" Count");
		$('#'+id).val(0);
		return false;
	}
}

function quotationParseDMY(dateStr) {
	if (!dateStr) return null;
	var value = String(dateStr).trim();
	var timeIdx = value.indexOf(' ');
	if (timeIdx > -1) {
		value = value.substring(0, timeIdx);
	}
	var parts = value.split('-');
	if (parts.length !== 3) return null;
	return new Date(parseInt(parts[2], 10), parseInt(parts[1], 10) - 1, parseInt(parts[0], 10));
}

function quotationFormatDMY(dateObj) {
	var day = dateObj.getDate();
	var month = dateObj.getMonth() + 1;
	var year = dateObj.getFullYear();
	return (day < 10 ? '0' : '') + day + '-' + (month < 10 ? '0' : '') + month + '-' + year;
}

function quotationDaysBetween(fromStr, toStr) {
	var from = quotationParseDMY(fromStr);
	var to = quotationParseDMY(toStr);
	if (!from || !to) return 0;
	return Math.round(Math.abs(to - from) / (1000 * 60 * 60 * 24));
}

function quotationDateDelta(oldFrom, newFrom) {
	var oldD = quotationParseDMY(oldFrom);
	var newD = quotationParseDMY(newFrom);
	if (!oldD || !newD) return 0;
	return Math.round((newD - oldD) / (1000 * 60 * 60 * 24));
}

function quotationShiftDMY(dateStr, deltaDays) {
	if (!dateStr || deltaDays === 0) return dateStr;
	var value = String(dateStr).trim();
	var timePart = '';
	if (value.indexOf(' ') > -1) {
		timePart = value.substring(value.indexOf(' '));
		value = value.substring(0, value.indexOf(' '));
	}
	var shifted = quotationAddDays(value, deltaDays);
	return shifted + timePart;
}

function quotationAddDays(dateStr, days) {
	var d = quotationParseDMY(dateStr);
	if (!d) return '';
	d.setDate(d.getDate() + days);
	return quotationFormatDMY(d);
}

function quotationGetTravelDates() {
	var isPackageUpdate = $('#from_date12').length > 0;
	if (isPackageUpdate) {
		return {
			isUpdate: true,
			from_date: $('#from_date12').val() || '',
			to_date: $('#to_date12').val() || ''
		};
	}
	// Group Quotation Update uses from_date1 / to_date1 (only while update modal is open)
	var groupUpdateOpen = $('#quotation_update_modal').hasClass('in') ||
		($('#quotation_update_modal').length > 0 && $('#quotation_update_modal').is(':visible'));
	if (groupUpdateOpen && $('#from_date1').length > 0) {
		return {
			isUpdate: true,
			from_date: $('#from_date1').val() || '',
			to_date: $('#to_date1').val() || ''
		};
	}
	return {
		isUpdate: false,
		from_date: $('#from_date').val() || '',
		to_date: $('#to_date').val() || ''
	};
}

function quotationIsRowActive(row) {
	if (!row || !row.cells || !row.cells[0] || !row.cells[0].childNodes[0]) return true;
	var input = row.cells[0].childNodes[0];
	return input.type !== 'checkbox' || input.checked;
}

function quotationShiftTableDateCells(tableId, cellIndexes, delta, options) {
	options = options || {};
	var table = document.getElementById(tableId);
	if (!table || !delta) return;
	for (var i = 0; i < table.rows.length; i++) {
		var row = table.rows[i];
		if (!quotationIsRowActive(row)) continue;
		for (var j = 0; j < cellIndexes.length; j++) {
			var input = quotationGetTableDateInput(row, cellIndexes[j]);
			if (input && input.value) {
				input.value = quotationShiftDMY(input.value, delta);
				// Do not trigger change by default — hotel check-in onchange would force checkout to +1 night
				if (options.triggerChange && window.jQuery) {
					jQuery(input).trigger('change');
				}
			}
		}
	}
}

function quotationSetTableDateCells(tableId, cellIndexes, dateValue) {
	var table = document.getElementById(tableId);
	if (!table || !dateValue) return;
	for (var i = 0; i < table.rows.length; i++) {
		var row = table.rows[i];
		if (!quotationIsRowActive(row)) continue;
		for (var j = 0; j < cellIndexes.length; j++) {
			var input = quotationGetTableDateInput(row, cellIndexes[j]);
			if (input) {
				quotationSetInputDateValue(input, dateValue);
			}
		}
	}
}

function quotationGetTableDateInput(row, cellIndex) {
	if (!row || !row.cells || !row.cells[cellIndex]) return null;
	var cell = row.cells[cellIndex];
	for (var i = 0; i < cell.childNodes.length; i++) {
		var node = cell.childNodes[i];
		if (node.tagName === 'INPUT' || node.tagName === 'SELECT' || node.tagName === 'TEXTAREA') {
			return node;
		}
	}
	return null;
}

function quotationComputeNightsFromDates(checkIn, checkOut) {
	if (!checkIn || !checkOut) return 0;
	var nights = quotationDaysBetween(checkIn, checkOut);
	return nights > 0 ? nights : 0;
}

function quotationRowHasHotelDates(row) {
	var checkInInput = quotationGetTableDateInput(row, 6);
	var checkOutInput = quotationGetTableDateInput(row, 7);
	return !!(checkInInput && checkInInput.value && checkOutInput && checkOutInput.value);
}

function quotationHotelsNeedDateFill() {
	var tableIds = ['tbl_package_tour_quotation_dynamic_hotel', 'tbl_package_tour_quotation_dynamic_hotel_update'];
	for (var t = 0; t < tableIds.length; t++) {
		var table = document.getElementById(tableIds[t]);
		if (!table) continue;
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			if (!quotationIsRowActive(row)) continue;
			if (!quotationRowHasHotelDates(row)) {
				return true;
			}
		}
	}
	return false;
}

function quotationGetRowNights(row) {
	var checkInInput = quotationGetTableDateInput(row, 6);
	var checkOutInput = quotationGetTableDateInput(row, 7);
	if (checkInInput && checkOutInput && checkInInput.value && checkOutInput.value) {
		var nightsFromDates = quotationComputeNightsFromDates(checkInInput.value, checkOutInput.value);
		if (nightsFromDates > 0) {
			return nightsFromDates;
		}
	}

	var stayDaysInput = quotationGetTableDateInput(row, 9);
	if (stayDaysInput && stayDaysInput.value) {
		var nightsFromField = parseInt(stayDaysInput.value, 10);
		if (!isNaN(nightsFromField) && nightsFromField > 0) {
			return nightsFromField;
		}
	}

	return 1;
}

function quotationSetInputDateValue(input, dateValue, triggerChange) {
	if (!input || !dateValue) return;
	input.value = dateValue;
	if (triggerChange && window.jQuery) {
		jQuery(input).trigger('change');
	}
}

function quotationSyncHotelDatesFromTravelStart(from_date, options) {
	options = options || {};
	var onlyMissing = !!options.onlyMissing;
	var tableIds = ['tbl_package_tour_quotation_dynamic_hotel', 'tbl_package_tour_quotation_dynamic_hotel_update'];
	var travelStart = from_date.split(' ')[0];

	tableIds.forEach(function (tableId) {
		var table = document.getElementById(tableId);
		if (!table || !table.rows.length) return;

		// Capture nights first so we can safely rewrite check-in/out without losing stay length
		var nightsByRow = [];
		for (var r = 0; r < table.rows.length; r++) {
			var captureRow = table.rows[r];
			if (!quotationIsRowActive(captureRow)) {
				nightsByRow[r] = 0;
				continue;
			}
			nightsByRow[r] = quotationGetRowNights(captureRow);
		}

		var chainDate = travelStart;
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			if (!quotationIsRowActive(row)) continue;

			var checkInInput = quotationGetTableDateInput(row, 6);
			var checkOutInput = quotationGetTableDateInput(row, 7);
			if (!checkInInput || !checkOutInput) continue;

			if (onlyMissing && quotationRowHasHotelDates(row)) {
				chainDate = checkOutInput.value.split(' ')[0];
				var stayDaysInput = quotationGetTableDateInput(row, 9);
				if (stayDaysInput && !stayDaysInput.value) {
					stayDaysInput.value = nightsByRow[i] || quotationGetRowNights(row);
				}
				continue;
			}

			var nights = nightsByRow[i] || 1;
			var checkOutDate = quotationAddDays(chainDate, nights);

			quotationSetInputDateValue(checkInInput, chainDate);
			quotationSetInputDateValue(checkOutInput, checkOutDate);

			var stayDaysInput = quotationGetTableDateInput(row, 9);
			if (stayDaysInput) {
				stayDaysInput.value = nights;
			}

			chainDate = checkOutDate;
		}
	});
}

function quotationGetReferenceHotelDateRange() {
	var tableIds = ['tbl_package_tour_quotation_dynamic_hotel', 'tbl_package_tour_quotation_dynamic_hotel_update'];
	for (var t = 0; t < tableIds.length; t++) {
		var table = document.getElementById(tableIds[t]);
		if (!table || !table.rows.length) return null;
		var firstRow = table.rows[0];
		if (!quotationIsRowActive(firstRow)) return null;
		var checkInInput = quotationGetTableDateInput(firstRow, 6);
		var checkOutInput = quotationGetTableDateInput(firstRow, 7);
		if (!checkInInput || !checkInInput.value || !checkOutInput || !checkOutInput.value) return null;
		return {
			check_in: checkInInput.value.split(' ')[0],
			check_out: checkOutInput.value.split(' ')[0]
		};
	}
	return null;
}

var quotationPackageTypeOptionsHtml = null;

function quotationCachePackageTypeOptions() {
	var $main = jQuery('#package_type');
	if ($main.length && quotationPackageTypeOptionsHtml === null) {
		quotationPackageTypeOptionsHtml = $main.html();
	}
}

function quotationRestorePackageTypeDropdown() {
	quotationCachePackageTypeOptions();
	if (!quotationPackageTypeOptionsHtml) {
		return;
	}
	var $main = jQuery('#package_type');
	if ($main.length) {
		if ($main.data('select2')) {
			$main.select2('destroy');
		}
		$main.html(quotationPackageTypeOptionsHtml);
	}
	jQuery('#addHotelInfobtnsubmit').prop('disabled', false);
}

function quotationGetDefaultPackageType() {
	quotationCachePackageTypeOptions();
	var $main = jQuery('#package_type');
	if (!$main.length) {
		return 'ECONOMY';
	}
	var val = $main.find('option:first').val();
	return val || 'ECONOMY';
}

function quotationInitEditablePackageTypeSelect(row, selectedValue) {
	if (!row || !row.cells || !row.cells[2] || !row.cells[2].childNodes[0]) {
		return;
	}
	quotationCachePackageTypeOptions();
	var pkgEl = row.cells[2].childNodes[0];
	var $pkg = jQuery(pkgEl);
	if ($pkg.data('select2')) {
		$pkg.select2('destroy');
	}
	if (quotationPackageTypeOptionsHtml) {
		$pkg.html(quotationPackageTypeOptionsHtml);
	}
	$pkg.prop('disabled', false).removeAttr('disabled');
	$pkg.attr('data-editable-package-type', '1').data('editablePackageType', 1);
	$pkg.addClass('app_select2 package_type_select');
	if (selectedValue) {
		if (!$pkg.find('option[value="' + selectedValue + '"]').length) {
			$pkg.append(new Option(selectedValue, selectedValue, true, true));
		}
		$pkg.val(selectedValue);
	}
	$pkg.select2({ width: '160px', minimumResultsForSearch: -1 });
}

function quotationIsEditablePackageTypeSelect($pkg) {
	return !!($pkg && ($pkg.attr('data-editable-package-type') === '1' || $pkg.data('editablePackageType') === 1));
}

function quotationEnsureEditablePackageTypeRows(table) {
	table = table || document.getElementById('tbl_package_tour_quotation_dynamic_hotel')
		|| document.getElementById('tbl_package_tour_quotation_dynamic_hotel_update');
	if (!table || !table.rows.length) {
		return;
	}
	for (var i = 0; i < table.rows.length; i++) {
		var pkgEl = table.rows[i].cells[2] && table.rows[i].cells[2].childNodes[0];
		if (!pkgEl) {
			continue;
		}
		var $pkg = jQuery(pkgEl);
		if (!quotationIsEditablePackageTypeSelect($pkg)) {
			continue;
		}
		var selectedValue = $pkg.val();
		$pkg.prop('disabled', false).removeAttr('disabled');
		if ($pkg.data('select2')) {
			$pkg.select2('destroy');
		}
		if (selectedValue && !$pkg.find('option[value="' + selectedValue + '"]').length) {
			$pkg.append(new Option(selectedValue, selectedValue, true, true));
		}
		$pkg.select2({ width: '160px', minimumResultsForSearch: -1 });
	}
}

function quotationResetHotelRowFields(row, options) {
	options = options || {};
	if (!row || !row.cells) {
		return;
	}
	if (row.cells[0] && row.cells[0].childNodes[0]) {
		row.cells[0].childNodes[0].checked = true;
	}
	var pkgEl = row.cells[2] && row.cells[2].childNodes[0];
	if (pkgEl) {
		var $pkg = jQuery(pkgEl);
		if ($pkg.data('select2')) {
			$pkg.select2('destroy');
		}
		if (quotationPackageTypeOptionsHtml) {
			$pkg.html(quotationPackageTypeOptionsHtml);
		}
		var pkgVal = options.packageType || quotationGetDefaultPackageType();
		$pkg.val(pkgVal);
		$pkg.addClass('app_select2 package_type_select');
		$pkg.select2({ width: '160px', minimumResultsForSearch: -1 });
	}
	var cityEl = row.cells[3] && row.cells[3].childNodes[0];
	if (cityEl) {
		var $city = jQuery(cityEl);
		if ($city.data('select2')) {
			$city.select2('destroy');
		}
		$city.empty();
		if (typeof city_lzloading === 'function') {
			city_lzloading(cityEl);
		}
	}
	var hotelEl = row.cells[4] && row.cells[4].childNodes[0];
	if (hotelEl) {
		var $hotel = jQuery(hotelEl);
		if ($hotel.data('select2')) {
			$hotel.select2('destroy');
		}
		$hotel.empty().append('<option value="">*Hotel Name</option>');
		$hotel.select2({ width: '160px', minimumResultsForSearch: 0 });
	}
	var roomEl = row.cells[5] && row.cells[5].childNodes[0];
	if (roomEl) {
		var $room = jQuery(roomEl);
		if ($room.data('select2')) {
			$room.select2('destroy');
		}
		$room.empty();
		$room.select2({ width: '140px', minimumResultsForSearch: 0 });
	}
	jQuery.each([6, 7, 8, 9, 10, 11, 12, 13, 14, 15], function (_, idx) {
		if (row.cells[idx] && row.cells[idx].childNodes[0]) {
			row.cells[idx].childNodes[0].value = '';
		}
	});
	var mealEl = row.cells[16] && row.cells[16].childNodes[0];
	if (mealEl) {
		var $meal = jQuery(mealEl);
		if ($meal.data('select2')) {
			$meal.select2('destroy');
		}
		$meal.val('');
		if (typeof window.initPackageQuotationMealPlanSelect === 'function') {
			window.initPackageQuotationMealPlanSelect(row);
		}
	}
}

function quotationPrepareHotelTableForTab2Reload(table) {
	table = table || document.getElementById('tbl_package_tour_quotation_dynamic_hotel');
	if (!table) {
		return quotationGetDefaultPackageType();
	}
	quotationRestorePackageTypeDropdown();
	var defaultPkg = quotationGetDefaultPackageType();
	jQuery('#package_type').val(defaultPkg).trigger('change');
	while (table.rows.length > 1) {
		table.deleteRow(table.rows.length - 1);
	}
	if (table.rows.length > 0) {
		quotationResetHotelRowFields(table.rows[0], { packageType: defaultPkg });
	}
	return defaultPkg;
}

function quotationGetHotelRowReference(row) {
	if (!row || !row.cells) return null;
	var cityEl = row.cells[3] && row.cells[3].childNodes[0];
	var $city = cityEl ? jQuery(cityEl) : null;
	var checkInInput = quotationGetTableDateInput(row, 6);
	var checkOutInput = quotationGetTableDateInput(row, 7);
	var mealEl = row.cells[16] && row.cells[16].childNodes[0];
	var hotelEl = row.cells[4] && row.cells[4].childNodes[0];
	var $hotel = hotelEl ? jQuery(hotelEl) : null;
	var roomCatEl = row.cells[5] && row.cells[5].childNodes[0];
	var $roomCat = roomCatEl ? jQuery(roomCatEl) : null;
	return {
		city_id: $city ? $city.val() : '',
		city_name: $city ? $city.find('option:selected').text() : '',
		hotel_id: $hotel ? $hotel.val() : '',
		hotel_name: $hotel ? $hotel.find('option:selected').text() : '',
		room_cat_id: $roomCat ? $roomCat.val() : '',
		room_cat_name: $roomCat ? $roomCat.find('option:selected').text() : '',
		hotel_type: row.cells[8] && row.cells[8].childNodes[0] ? row.cells[8].childNodes[0].value : '',
		check_in: checkInInput && checkInInput.value ? checkInInput.value.split(' ')[0] : '',
		check_out: checkOutInput && checkOutInput.value ? checkOutInput.value.split(' ')[0] : '',
		total_rooms: row.cells[10] && row.cells[10].childNodes[0] ? row.cells[10].childNodes[0].value : '',
		extra_bed: row.cells[11] && row.cells[11].childNodes[0] ? row.cells[11].childNodes[0].value : '',
		meal_plan: mealEl ? jQuery(mealEl).val() : ''
	};
}

function quotationGetLastHotelRowReference(table) {
	table = table || document.getElementById('tbl_package_tour_quotation_dynamic_hotel')
		|| document.getElementById('tbl_package_tour_quotation_dynamic_hotel_update');
	if (!table || !table.rows.length) return null;
	return quotationGetHotelRowReference(table.rows[table.rows.length - 1]);
}

/** Row 1, 2, 3… refs from first package tier (table has no header — row 1 is rows[0]). */
function quotationGetTemplateHotelRowReferences(table, count) {
	table = table || document.getElementById('tbl_package_tour_quotation_dynamic_hotel')
		|| document.getElementById('tbl_package_tour_quotation_dynamic_hotel_update');
	if (!table || !table.rows.length || !count) return [];
	var refs = [];
	for (var i = 0; i < count; i++) {
		if (i >= table.rows.length) {
			if (refs.length) {
				refs.push(refs[refs.length - 1]);
			}
			continue;
		}
		var ref = quotationGetHotelRowReference(table.rows[i]);
		if (ref) {
			refs.push(ref);
		}
	}
	return refs;
}

/** How many hotel rows belong to the first package tier (e.g. 3 Economy rows). */
function quotationGetFirstPackageHotelRowCount(table) {
	table = table || document.getElementById('tbl_package_tour_quotation_dynamic_hotel')
		|| document.getElementById('tbl_package_tour_quotation_dynamic_hotel_update');
	if (!table || !table.rows.length) return 0;
	var firstType = typeof quotationGetHotelRowPackageType === 'function'
		? quotationGetHotelRowPackageType(table.rows[0])
		: (table.rows[0].cells[2] && table.rows[0].cells[2].childNodes[0] ? table.rows[0].cells[2].childNodes[0].value : '');
	if (!firstType || firstType === '*Package Type') {
		return table.rows.length;
	}
	var count = 0;
	for (var i = 0; i < table.rows.length; i++) {
		var rowType = typeof quotationGetHotelRowPackageType === 'function'
			? quotationGetHotelRowPackageType(table.rows[i])
			: (table.rows[i].cells[2] && table.rows[i].cells[2].childNodes[0] ? table.rows[i].cells[2].childNodes[0].value : '');
		if (rowType === firstType) {
			count++;
		} else {
			break;
		}
	}
	return count || table.rows.length;
}

function quotationNormalizeHotelListForCount(hotel_arr, count) {
	if (!count) return hotel_arr || [];
	hotel_arr = hotel_arr || [];
	if (!hotel_arr.length) {
		var emptyRows = [];
		for (var z = 0; z < count; z++) {
			emptyRows.push({});
		}
		return emptyRows;
	}
	var out = [];
	for (var i = 0; i < count; i++) {
		out.push(hotel_arr[i] || hotel_arr[hotel_arr.length - 1]);
	}
	return out;
}

function quotationApplyHotelRowReference(row, ref, options) {
	if (!row || !ref) return;
	options = options || {};
	var cityEl = row.cells[3] && row.cells[3].childNodes[0];
	if (ref.city_id && ref.city_name && cityEl) {
		if (typeof setQuotationCitySelect === 'function') {
			setQuotationCitySelect(cityEl, ref.city_id, ref.city_name);
		} else if (typeof city_lzloading === 'function') {
			city_lzloading(cityEl);
			var cityOption = new Option(ref.city_name, ref.city_id, true, true);
			jQuery(cityEl).append(cityOption).trigger('change.select2');
		}
		if (!options.skipHotelLoad && cityEl.getAttribute('onchange') && typeof hotel_name_list_load === 'function' && cityEl.id) {
			hotel_name_list_load(cityEl.id);
		}
	}
	if (ref.check_in && row.cells[6] && row.cells[6].childNodes[0]) {
		row.cells[6].childNodes[0].value = ref.check_in;
	}
	if (ref.check_out && row.cells[7] && row.cells[7].childNodes[0]) {
		row.cells[7].childNodes[0].value = ref.check_out;
	}
	if (row.cells[9] && row.cells[9].childNodes[0] && ref.check_in && ref.check_out) {
		var nights = typeof quotationComputeNightsFromDates === 'function'
			? quotationComputeNightsFromDates(ref.check_in, ref.check_out) : 0;
		if (nights > 0) {
			row.cells[9].childNodes[0].value = nights;
		}
	}
	if (ref.total_rooms && row.cells[10] && row.cells[10].childNodes[0]) {
		row.cells[10].childNodes[0].value = ref.total_rooms;
	}
	if (ref.extra_bed !== undefined && ref.extra_bed !== null && row.cells[11] && row.cells[11].childNodes[0]) {
		row.cells[11].childNodes[0].value = ref.extra_bed;
	}
	if (ref.meal_plan && row.cells[16] && row.cells[16].childNodes[0]) {
		jQuery(row.cells[16].childNodes[0]).val(ref.meal_plan).trigger('change');
	}
}

function quotationGetReferenceTravelStartDate() {
	var tableIds = ['tbl_package_tour_quotation_dynamic_hotel', 'tbl_package_tour_quotation_dynamic_hotel_update'];
	for (var t = 0; t < tableIds.length; t++) {
		var table = document.getElementById(tableIds[t]);
		if (!table || !table.rows.length) continue;
		var firstRow = table.rows[0];
		if (!quotationIsRowActive(firstRow)) continue;
		var checkInInput = quotationGetTableDateInput(firstRow, 6);
		if (checkInInput && checkInInput.value) {
			return checkInInput.value.split(' ')[0];
		}
	}
	var dates = quotationGetTravelDates();
	return dates.from_date ? dates.from_date.split(' ')[0] : '';
}

function quotationGetChainedPackageStartDate() {
	return quotationGetReferenceTravelStartDate();
}

function quotationGetExistingHotelPackageIds() {
	var ids = {};
	var tableIds = ['tbl_package_tour_quotation_dynamic_hotel', 'tbl_package_tour_quotation_dynamic_hotel_update'];
	for (var t = 0; t < tableIds.length; t++) {
		var table = document.getElementById(tableIds[t]);
		if (!table) continue;
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			if (!row.cells[14] || !row.cells[14].childNodes[0]) continue;
			var packageId = row.cells[14].childNodes[0].value;
			if (packageId) {
				ids[String(packageId)] = true;
			}
		}
	}
	return ids;
}

function quotationFilterNewPackageIds(package_id_arr) {
	var existingIds = quotationGetExistingHotelPackageIds();
	var newIds = [];
	for (var i = 0; i < package_id_arr.length; i++) {
		if (!existingIds[String(package_id_arr[i])]) {
			newIds.push(package_id_arr[i]);
		}
	}
	return newIds.length ? newIds : package_id_arr;
}

function quotationGetExistingTransportPackageIds() {
	var ids = {};
	var tableIds = ['tbl_package_tour_quotation_dynamic_transport', 'tbl_package_tour_quotation_dynamic_transport_u'];
	for (var t = 0; t < tableIds.length; t++) {
		var table = document.getElementById(tableIds[t]);
		if (!table) continue;
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			if (!quotationIsRowActive(row)) continue;
			if (!row.cells[11] || !row.cells[11].childNodes[0]) continue;
			var packageId = row.cells[11].childNodes[0].value;
			if (packageId) {
				ids[String(packageId)] = true;
			}
		}
	}
	return ids;
}

/** Package IDs that do not yet have transport rows (transport is per package, not per hotel tier). */
function quotationFilterNewPackageIdsForTransport(package_id_arr) {
	var existingIds = quotationGetExistingTransportPackageIds();
	var newIds = [];
	for (var i = 0; i < package_id_arr.length; i++) {
		if (!existingIds[String(package_id_arr[i])]) {
			newIds.push(package_id_arr[i]);
		}
	}
	return newIds;
}

function quotationGetHotelDateList() {
	var dates = [];
	var tableIds = ['tbl_package_tour_quotation_dynamic_hotel', 'tbl_package_tour_quotation_dynamic_hotel_update'];
	tableIds.forEach(function (tableId) {
		var table = document.getElementById(tableId);
		if (!table) return;
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			if (!quotationIsRowActive(row)) continue;
			var checkInInput = quotationGetTableDateInput(row, 6);
			var checkOutInput = quotationGetTableDateInput(row, 7);
			if (!checkInInput || !checkInInput.value) continue;
			dates.push({
				check_in: checkInInput.value.split(' ')[0],
				check_out: checkOutInput && checkOutInput.value ? checkOutInput.value.split(' ')[0] : checkInInput.value.split(' ')[0],
				package_id: row.cells[14] ? row.cells[14].childNodes[0].value : ''
			});
		}
	});
	return dates;
}

function quotationSyncTransportDatesFromHotels() {
	var hotelDates = quotationGetHotelDateList();
	if (!hotelDates.length) return;

	var packageRanges = {};
	for (var h = 0; h < hotelDates.length; h++) {
		var entry = hotelDates[h];
		var packageId = entry.package_id ? String(entry.package_id) : '';
		if (!packageId) continue;
		if (!packageRanges[packageId]) {
			packageRanges[packageId] = { start: entry.check_in, end: entry.check_out };
			continue;
		}
		if (quotationParseDMY(entry.check_in) < quotationParseDMY(packageRanges[packageId].start)) {
			packageRanges[packageId].start = entry.check_in;
		}
		if (quotationParseDMY(entry.check_out) > quotationParseDMY(packageRanges[packageId].end)) {
			packageRanges[packageId].end = entry.check_out;
		}
	}

	var tableIds = ['tbl_package_tour_quotation_dynamic_transport', 'tbl_package_tour_quotation_dynamic_transport_u'];
	tableIds.forEach(function (tableId) {
		var table = document.getElementById(tableId);
		if (!table) return;
		var fallbackIdx = 0;
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			if (!quotationIsRowActive(row)) continue;
			var packageId = row.cells[11] ? String(row.cells[11].childNodes[0].value || '') : '';
			var startInput = quotationGetTableDateInput(row, 3);
			var endInput = quotationGetTableDateInput(row, 4);
			var startDate = '';
			var endDate = '';

			if (packageId && packageRanges[packageId]) {
				startDate = packageRanges[packageId].start;
				endDate = packageRanges[packageId].end;
			} else if (hotelDates[fallbackIdx]) {
				startDate = hotelDates[fallbackIdx].check_in;
				endDate = hotelDates[fallbackIdx].check_out;
				fallbackIdx++;
			}

			if (startDate && startInput) {
				quotationSetInputDateValue(startInput, startDate);
			}
			if (endDate && endInput) {
				quotationSetInputDateValue(endInput, endDate);
			}
		}
	});
}

function quotationSyncFlightAndExcursionDatesFromHotels() {
	var hotelDates = quotationGetHotelDateList();
	if (!hotelDates.length) return;

	var excTable = document.getElementById('tbl_package_tour_quotation_dynamic_excursion');
	if (excTable) {
		var excIdx = 0;
		for (var e = 0; e < excTable.rows.length; e++) {
			var excRow = excTable.rows[e];
			if (!quotationIsRowActive(excRow)) continue;
			var excInput = quotationGetTableDateInput(excRow, 2);
			if (!excInput) continue;
			var slot = hotelDates[Math.min(excIdx, hotelDates.length - 1)];
			quotationSetInputDateValue(excInput, slot.check_in);
			excIdx++;
		}
	}

	var planeTable = document.getElementById('tbl_package_tour_quotation_dynamic_plane');
	if (!planeTable || !planeTable.rows.length) return;

	var firstDate = hotelDates[0].check_in;
	var lastDate = hotelDates[hotelDates.length - 1].check_out || hotelDates[hotelDates.length - 1].check_in;
	var activePlaneRows = [];

	for (var p = 0; p < planeTable.rows.length; p++) {
		var planeRow = planeTable.rows[p];
		if (quotationIsRowActive(planeRow)) {
			activePlaneRows.push(planeRow);
		}
	}

	activePlaneRows.forEach(function (planeRow, index) {
		var departInput = quotationGetTableDateInput(planeRow, 6);
		var arriveInput = quotationGetTableDateInput(planeRow, 7);
		if (!departInput) return;

		var dateValue = index === 0 ? firstDate : lastDate;
		var timePart = ' 00:00';
		if (departInput.value && departInput.value.indexOf(' ') > -1) {
			timePart = departInput.value.substring(departInput.value.indexOf(' '));
		}
		quotationSetInputDateValue(departInput, dateValue + timePart);
		if (arriveInput) {
			var arriveTime = ' 00:00';
			if (arriveInput.value && arriveInput.value.indexOf(' ') > -1) {
				arriveTime = arriveInput.value.substring(arriveInput.value.indexOf(' '));
			}
			quotationSetInputDateValue(arriveInput, dateValue + arriveTime);
		}
	});
}

function quotationPopulateTransportRows(transport_arr, options) {
	options = options || {};
	if (!transport_arr || !transport_arr.length) return;

	var table = document.getElementById('tbl_package_tour_quotation_dynamic_transport');
	if (!table) return;

	var append = !!options.append;
	var fallbackFrom = options.from_date || '';
	var fallbackTo = options.to_date || '';

	for (var i = 0; i < transport_arr.length; i++) {
		if (append) {
			addRow('tbl_package_tour_quotation_dynamic_transport');
		}
		var row = table.rows[append ? table.rows.length - 1 : i];
		if (!row) continue;

		row.cells[0].childNodes[0].checked = true;
		row.cells[1].childNodes[0].value = append ? table.rows.length : (i + 1);
		row.cells[2].childNodes[0].value = transport_arr[i]['bus_id'];
		row.cells[3].childNodes[0].value = transport_arr[i]['start_date'] || fallbackFrom;
		row.cells[4].childNodes[0].value = transport_arr[i]['end_date'] || fallbackTo;

		jQuery(row.cells[5].childNodes[0]).prepend('<optgroup value=' + transport_arr[i]['pickup_type'] +
			' label="' + (transport_arr[i]['pickup_type']).charAt(0).toUpperCase() + (transport_arr[i]['pickup_type']).slice(1) +
			' Name"><option value="' + transport_arr[i]['pickup_type'] + '-' + transport_arr[i]['pickup_id'] + '">' + transport_arr[i]['pickup'] +
			'</option></optgroup>');
		document.getElementById(row.cells[5].childNodes[0].id).value =
			transport_arr[i]['pickup_type'] + '-' + transport_arr[i]['pickup_id'];

		jQuery(row.cells[6].childNodes[0]).prepend('<optgroup value=' + transport_arr[i]['drop_type'] +
			' label="' + (transport_arr[i]['drop_type']).charAt(0).toUpperCase() + (transport_arr[i]['drop_type']).slice(1) +
			' Name"><option value="' + transport_arr[i]['drop_type'] + '-' + transport_arr[i]['drop_id'] + '">' + transport_arr[i]['drop'] +
			'</option></optgroup>');
		document.getElementById(row.cells[6].childNodes[0].id).value =
			transport_arr[i]['drop_type'] + '-' + transport_arr[i]['drop_id'];

		row.cells[8].childNodes[0].value = transport_arr[i]['total_vehicles'];
		row.cells[9].childNodes[0].value = transport_arr[i]['total_cost'];
		row.cells[10].childNodes[0].value = transport_arr[i]['package_name'];
		row.cells[11].childNodes[0].value = transport_arr[i]['package_id'];
		row.cells[12].childNodes[0].value = transport_arr[i]['pickup_type'];
		row.cells[13].childNodes[0].value = transport_arr[i]['drop_type'];

		jQuery('#' + row.cells[2].childNodes[0].id).select2().trigger('change');
		jQuery('#' + row.cells[5].childNodes[0].id).select2().trigger('change');
		jQuery('#' + row.cells[6].childNodes[0].id).select2().trigger('change');
		jQuery('#' + row.cells[7].childNodes[0].id).select2().trigger('change');
		if (typeof destinationLoading === 'function') {
			destinationLoading(jQuery(row.cells[5].childNodes[0]), 'Pickup Location');
			destinationLoading(jQuery(row.cells[6].childNodes[0]), 'Drop-off Location');
		}
	}
}

function quotationSyncTransportDates(from_date, to_date) {
	var travelStart = from_date.split(' ')[0];
	var travelEnd = to_date ? to_date.split(' ')[0] : '';

	['tbl_package_tour_quotation_dynamic_transport', 'tbl_package_tour_quotation_dynamic_transport_u'].forEach(function (tableId) {
		var table = document.getElementById(tableId);
		if (!table) return;
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			if (!quotationIsRowActive(row)) continue;
			var startInput = quotationGetTableDateInput(row, 3);
			var endInput = quotationGetTableDateInput(row, 4);
			if (startInput && !startInput.value) {
				quotationSetInputDateValue(startInput, travelStart);
			}
			if (endInput && !endInput.value && travelEnd) {
				quotationSetInputDateValue(endInput, travelEnd);
			}
		}
	});
}

function quotationShiftDatetimeFieldsByDelta(delta) {
	if (!delta) return;

	quotationShiftTableDateCells('tbl_package_tour_quotation_dynamic_plane', [6, 7], delta);
	quotationShiftTableDateCells('tbl_package_tour_quotation_dynamic_plane_update', [6, 7], delta);
	quotationShiftTableDateCells('tbl_package_tour_quotation_dynamic_train', [5, 6], delta);
	quotationShiftTableDateCells('tbl_dynamic_cruise_quotation', [2, 3], delta);
	quotationShiftTableDateCells('tbl_dynamic_cruise_quotation_update', [2, 3], delta);
	quotationShiftTableDateCells('tbl_package_tour_quotation_dynamic_excursion', [2], delta);

	[
		'input[id^="txt_dapart"]',
		'input[id^="txt_arrval"]',
		'input[id^="train_departure_date"]',
		'input[id^="train_arrival_date"]',
		'input[id^="cruise_departure_date"]',
		'input[id^="cruise_arrival_date"]',
		'input[id^="exc_date-"]'
	].forEach(function (selector) {
		jQuery(selector).each(function () {
			if (this.value) {
				this.value = quotationShiftDMY(this.value, delta);
				jQuery(this).trigger('change');
			}
		});
	});
}

function quotationSetDefaultSectionDates(from_date) {
	if (!from_date) return;
	var datePart = from_date.split(' ')[0];
	jQuery('input[id^="txt_dapart"], input[id^="txt_arrval"], input[id^="train_departure_date"], input[id^="train_arrival_date"], input[id^="cruise_departure_date"], input[id^="cruise_arrival_date"], #exc_date-1').each(function () {
		var current = jQuery(this).val();
		var timePart = ' 00:00';
		if (current && current.indexOf(' ') > -1) {
			timePart = current.substring(current.indexOf(' '));
		}
		jQuery(this).val(datePart + timePart);
	});
}
function quotationGetTravelStayBaselineDate() {
	var hotelTableIds = ['tbl_package_tour_quotation_dynamic_hotel', 'tbl_package_tour_quotation_dynamic_hotel_update'];
	for (var h = 0; h < hotelTableIds.length; h++) {
		var hotelTable = document.getElementById(hotelTableIds[h]);
		if (!hotelTable) continue;
		for (var i = 0; i < hotelTable.rows.length; i++) {
			var row = hotelTable.rows[i];
			if (!quotationIsRowActive(row)) continue;
			var checkInInput = quotationGetTableDateInput(row, 6);
			if (checkInInput && checkInInput.value) {
				return checkInInput.value.split(' ')[0];
			}
		}
	}

	var transportTableIds = ['tbl_package_tour_quotation_dynamic_transport', 'tbl_package_tour_quotation_dynamic_transport_u'];
	for (var t = 0; t < transportTableIds.length; t++) {
		var transportTable = document.getElementById(transportTableIds[t]);
		if (!transportTable) continue;
		for (var k = 0; k < transportTable.rows.length; k++) {
			var tRow = transportTable.rows[k];
			if (!quotationIsRowActive(tRow)) continue;
			var startInput = quotationGetTableDateInput(tRow, 3);
			if (startInput && startInput.value) {
				return startInput.value.split(' ')[0];
			}
		}
	}

	return '';
}

function quotationRecalculateTravelStayCosts(isUpdate) {
	if (typeof get_hotel_cost === 'function') {
		get_hotel_cost();
	}
	if (!isUpdate && typeof get_transport_cost === 'function') {
		get_transport_cost();
	}
	if (isUpdate && typeof get_transport_cost_update === 'function') {
		var transportTable = document.getElementById('tbl_package_tour_quotation_dynamic_transport_u');
		if (transportTable && transportTable.rows.length) {
			var firstRow = transportTable.rows[0];
			var startInput = firstRow.cells[3] && firstRow.cells[3].childNodes[0];
			if (startInput && startInput.id) {
				get_transport_cost_update(startInput.id);
			}
		}
	}
	if (typeof get_excursion_amount === 'function') {
		get_excursion_amount();
	}
}

function syncQuotationTravelStayDates(options) {
	options = options || {};
	var dates = quotationGetTravelDates();
	var from_date = dates.from_date;
	var to_date = dates.to_date;
	if (!from_date) return;

	var travelStart = from_date.split(' ')[0];
	var baselineDate = quotationGetTravelStayBaselineDate();
	var delta = baselineDate ? quotationDateDelta(baselineDate, travelStart) : 0;
	var hasHotelRows = !!document.getElementById('tbl_package_tour_quotation_dynamic_hotel') ||
		!!document.getElementById('tbl_package_tour_quotation_dynamic_hotel_update');
	var preserveHotelDates = !!options.preserveHotelDates;
	var isGroupQuotationUpdate = $('#quotation_update_modal').hasClass('in') ||
		($('#quotation_update_modal').length > 0 && $('#quotation_update_modal').is(':visible'));

	// Group Quotation Update has no FIT hotel check-in rows; train/flight/cruise must follow travel from-date
	if (isGroupQuotationUpdate) {
		quotationSetDefaultSectionDates(from_date);
		quotationSyncTransportDates(from_date, to_date);
		quotationSyncGroupTransportDates(from_date, to_date);
		quotationRecalculateTravelStayCosts(dates.isUpdate);
		return;
	}

	if (delta !== 0) {
		// Re-chain hotels from new travel start (preserves nights). Do NOT delta-shift hotel
		// check-in fields — that triggered get_auto_to_date and collapsed every stay to 1 night.
		quotationSyncHotelDatesFromTravelStart(from_date);
		quotationSyncTransportDatesFromHotels();
		quotationSyncFlightAndExcursionDatesFromHotels();
		quotationShiftTableDateCells('tbl_package_tour_quotation_dynamic_train', [5, 6], delta);
		quotationShiftTableDateCells('tbl_dynamic_cruise_quotation', [2, 3], delta);
		quotationShiftTableDateCells('tbl_dynamic_cruise_quotation_update', [2, 3], delta);
	} else if (hasHotelRows) {
		if (!preserveHotelDates && quotationHotelsNeedDateFill()) {
			quotationSyncHotelDatesFromTravelStart(from_date, { onlyMissing: true });
		}
		quotationSyncTransportDatesFromHotels();
		quotationSyncFlightAndExcursionDatesFromHotels();
	} else {
		quotationSyncTransportDates(from_date, to_date);
		if (!baselineDate) {
			quotationSetDefaultSectionDates(from_date);
		}
	}

	quotationRecalculateTravelStayCosts(dates.isUpdate);
}

function quotationSyncGroupTransportDates(from_date, to_date) {
	var travelStart = from_date ? from_date.split(' ')[0] : '';
	var travelEnd = to_date ? to_date.split(' ')[0] : travelStart;
	['tbl_group_tour_quotation_transport', 'tbl_group_tour_quotation_transport_u'].forEach(function (tableId) {
		var table = document.getElementById(tableId);
		if (!table) return;
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			if (!quotationIsRowActive(row)) continue;
			var startInput = quotationGetTableDateInput(row, 3);
			var endInput = quotationGetTableDateInput(row, 4);
			if (startInput && travelStart) {
				quotationSetInputDateValue(startInput, travelStart);
			}
			if (endInput && travelEnd) {
				quotationSetInputDateValue(endInput, travelEnd);
			}
		}
	});
}

function quotationSyncTravelStaySectionsFromHotels() {
	if (typeof quotationSyncTransportDatesFromHotels === 'function') {
		quotationSyncTransportDatesFromHotels();
	}
	if (typeof quotationSyncFlightAndExcursionDatesFromHotels === 'function') {
		quotationSyncFlightAndExcursionDatesFromHotels();
	}
	var dates = quotationGetTravelDates();
	if (typeof quotationRecalculateTravelStayCosts === 'function') {
		quotationRecalculateTravelStayCosts(dates.isUpdate);
	}
}

function quotationWatchTab3Activation() {
	var tab3 = document.getElementById('tab3');
	if (tab3 && window.MutationObserver) {
		var observer = new MutationObserver(function () {
			if (jQuery('#tab3').hasClass('active')) {
				setTimeout(function () {
					if (typeof syncQuotationTravelStayDates === 'function') {
						syncQuotationTravelStayDates();
					}
				}, 150);
			}
		});
		observer.observe(tab3, { attributes: true, attributeFilter: ['class'] });
	}

	var tab3u = document.getElementById('tab3_u');
	if (tab3u && window.MutationObserver) {
		var observerU = new MutationObserver(function () {
			if (jQuery('#tab3_u').hasClass('active')) {
				setTimeout(function () {
					if (typeof syncQuotationTravelStayDates === 'function') {
						syncQuotationTravelStayDates();
					}
				}, 150);
			}
		});
		observerU.observe(tab3u, { attributes: true, attributeFilter: ['class'] });
	}
}

jQuery(document).ready(function () {
	quotationCachePackageTypeOptions();
	quotationWatchTab3Activation();
});

jQuery(document).on('click', '#tab3_head', function () {
	if (jQuery('#tab4').hasClass('active') && typeof quotationSaveTab4CostingState === 'function') {
		quotationSaveTab4CostingState();
	}
	setTimeout(function () {
		if (typeof syncQuotationTravelStayDates === 'function') {
			syncQuotationTravelStayDates();
		}
	}, 200);
});

jQuery(document).on(
	'change',
	'#tbl_package_tour_quotation_dynamic_costing input, #tbl_package_tour_quotation_dynamic_costing select, #flight_cost, #train_cost, #cruise_cost, #visa_cost, #guide_cost, #misc_cost, #currency_code',
	function () {
		if (typeof quotationSaveTab4CostingState === 'function') {
			quotationSaveTab4CostingState();
		}
	}
);

function quotationIsValidImageFile(file) {
	if (!file || !file.name) return false;
	if (file.type && file.type.indexOf('image/') === 0) return true;
	var ext = file.name.split('.').pop().toLowerCase();
	var imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'tiff', 'tif', 'ico', 'heic', 'heif', 'avif'];
	return imageExts.indexOf(ext) !== -1;
}

function quotationGetHotelRowPackageType(row) {
	if (!row || !row.cells || !row.cells[2] || !row.cells[2].childNodes[0]) {
		return '';
	}
	var el = row.cells[2].childNodes[0];
	return $(el).val() || el.value || '';
}

function isQuotationGroupCostingDiv() {
	var el = document.getElementById('tbl_package_tour_quotation_dynamic_costing');
	return !!(el && el.tagName === 'DIV');
}

function quotationGroupCostingSuffix(index, total) {
	if (total <= 1) {
		return '-';
	}
	return '-' + (index + 1);
}

function quotationGroupCostingSuffixNumber(suffix) {
	if (!suffix || suffix === '-') {
		return '1';
	}
	return String(suffix).replace(/^-/, '');
}

function quotationGroupCostingRenumberRow($row, newSuffix) {
	var oldSuffix = '-';
	$row.find('[id]').each(function () {
		var id = this.id;
		if (!id) {
			return;
		}
		if (id === 'chk_costing1') {
			this.id = 'chk_costing' + quotationGroupCostingSuffixNumber(newSuffix);
			return;
		}
		if (id === 'package_name1' || id === 'package_id1') {
			this.id = id + newSuffix;
			return;
		}
		if (id.slice(-oldSuffix.length) === oldSuffix) {
			this.id = id.slice(0, -oldSuffix.length) + newSuffix;
		} else if (/-\d+$/.test(id)) {
			this.id = id.replace(/-\d+$/, '') + newSuffix;
		}
	});
	$row.find('label[for]').each(function () {
		var target = $(this).attr('for');
		if (!target) {
			return;
		}
		if (target === 'chk_costing1') {
			$(this).attr('for', 'chk_costing' + quotationGroupCostingSuffixNumber(newSuffix));
		}
	});
	$row.find('[onchange]').each(function () {
		var handler = $(this).attr('onchange');
		if (!handler) {
			return;
		}
		handler = handler.replace(/([a-zA-Z0-9_]+)-(\d*)(?=['"])/g, function (match, prefix, num) {
			return prefix + newSuffix;
		});
		$(this).attr('onchange', handler);
	});
}

function quotationStripGroupCostingRowSelect2($row) {
	$row.find('select').each(function () {
		var $select = $(this);
		if ($select.data('select2')) {
			$select.select2('destroy');
		}
	});
	$row.find('.select2-container').remove();
}

function quotationGroupCostingSetRowValues($row, suffix, data, options) {
	options = options || {};
	var hotelCost = parseFloat(data && data.cost) || 0;
	var packageType = (data && data.type) ? data.type : 'NA';
	var packageId = (data && data.id) ? data.id : '';
	var transportCost = parseFloat(options.transport_cost) || 0;
	var excursionCost = parseFloat(options.excursion_cost) || 0;
	var basicTotal = hotelCost + transportCost + excursionCost;

	$row.find('#package_type' + suffix).val(packageType);
	$row.find('#tour_cost' + suffix).val(hotelCost);
	$row.find('#excursion_cost' + suffix).val(excursionCost);
	$row.find('#transport_cost1' + suffix).val(transportCost);
	$row.find('#basic_amount' + suffix).val(basicTotal.toFixed(2));
	$row.find('#total_tour_cost' + suffix).val(basicTotal.toFixed(2));
	if (packageId) {
		$row.find('#package_id1' + suffix).val(packageId);
	}
}

function quotationGetTab3TransportTotal() {
	var transport_cost_total = 0;
	var table = document.getElementById('tbl_package_tour_quotation_dynamic_transport');
	if (!table || !table.rows) {
		return 0;
	}
	for (var i = 0; i < table.rows.length; i++) {
		var row = table.rows[i];
		if (row.cells[0] && row.cells[0].childNodes[0] && row.cells[0].childNodes[0].checked) {
			var cost = parseFloat(row.cells[9].childNodes[0].value) || 0;
			transport_cost_total += cost;
		}
	}
	return transport_cost_total;
}

function quotationGetTab3ExcursionTotal() {
	var total_amount = 0;
	var table = document.getElementById('tbl_package_tour_quotation_dynamic_excursion');
	if (!table || !table.rows) {
		return 0;
	}
	for (var i = 0; i < table.rows.length; i++) {
		var row = table.rows[i];
		if (row.cells[0] && row.cells[0].childNodes[0] && row.cells[0].childNodes[0].checked) {
			total_amount += parseFloat(row.cells[10].childNodes[0].value) || 0;
		}
	}
	return total_amount;
}

function quotationPopulateGroupCostingFromHotels(hotel_main_arr, options) {
	if (!isQuotationGroupCostingDiv()) {
		return false;
	}
	options = options || {};

	var $container = $('#tbl_package_tour_quotation_dynamic_costing');
	var $template = $container.find('.quotation-group-costing-row').first();
	if (!$template.length) {
		$container.children().wrapAll('<div class="quotation-group-costing-row mg_bt_20"></div>');
		$template = $container.find('.quotation-group-costing-row').first();
	}

	quotationStripGroupCostingRowSelect2($template);
	var $templateClone = $template.clone(false, false);
	quotationStripGroupCostingRowSelect2($templateClone);
	$container.empty();

	if (!hotel_main_arr || !hotel_main_arr.length) {
		var $emptyRow = $templateClone.clone(false, false);
		quotationStripGroupCostingRowSelect2($emptyRow);
		quotationGroupCostingRenumberRow($emptyRow, '-');
		$container.append($emptyRow);
		quotationGroupCostingSetRowValues($emptyRow, '-', { type: 'NA', cost: 0 }, options);
		if (typeof quotation_cost_calculate === 'function') {
			quotation_cost_calculate('tour_cost-');
		}
		return true;
	}

	var total = hotel_main_arr.length;
	for (var i = 0; i < total; i++) {
		if (i > 0) {
			$container.append('<hr class="quotation-package-costing-separator">');
		}
		var suffix = quotationGroupCostingSuffix(i, total);
		var $row = $templateClone.clone(false, false);
		quotationStripGroupCostingRowSelect2($row);
		quotationGroupCostingRenumberRow($row, suffix);
		$container.append($row);
		quotationGroupCostingSetRowValues($row, suffix, hotel_main_arr[i], options);
		if (typeof quotation_cost_calculate === 'function') {
			quotation_cost_calculate('tour_cost' + suffix);
		}
	}
	return true;
}

function quotationCollectGroupCostingEntries() {
	if (!isQuotationGroupCostingDiv()) {
		return null;
	}

	var entries = [];
	$('#tbl_package_tour_quotation_dynamic_costing').find('[id^="tour_cost-"]').each(function () {
		var suffix = this.id.replace('tour_cost', '');
		if (!suffix) {
			return;
		}
		entries.push({
			package_type_c: $('#package_type' + suffix).val() || '',
			tour_cost: $('#tour_cost' + suffix).val() || '',
			transport_cost: $('#transport_cost1' + suffix).val() || '',
			excursion_cost: $('#excursion_cost' + suffix).val() || '',
			basic_cost: $('#basic_amount' + suffix).val() || '',
			service_tax: $('#service_charge' + suffix).val() || '',
			discount_in: $('#discount_in' + suffix).val() || '',
			discount: $('#discount_amt' + suffix).val() || '',
			tax_apply_on: $('#tax_apply_on' + suffix).val() || '',
			tax_value: $('#tax_value' + suffix).val() || '',
			service_tax_subtotal: $('#service_tax_subtotal' + suffix).val() || '',
			tcs: $('#tcs_tax' + suffix).val() || '',
			tcsvalue: $('#tcs1' + suffix).val() || '',
			tdsvalue: ($('#tds' + suffix).val() || 0),
			total_tour_cost: $('#total_tour_cost' + suffix).val() || '',
			package_name3: $('#package_name1' + suffix).val() || '',
			pkg_id: $('#package_id1' + suffix).val() || ''
		});
	});
	return entries;
}

function quotationCollectGroupCostingBsmValues() {
	if (!isQuotationGroupCostingDiv()) {
		return null;
	}

	var bsmValues = [];
	$('#tbl_package_tour_quotation_dynamic_costing').find('[id^="tour_cost-"]').each(function () {
		var suffix = this.id.replace('tour_cost', '');
		if (!suffix) {
			return;
		}
		bsmValues.push([{
			"basic": 'basic',
			"service": 'service',
			'tax_apply_on': $('#tax_apply_on' + suffix).val() || '',
			'tax_value': $('#tax_value' + suffix).val() || '',
			'tcsper': $('#tcs_tax' + suffix).val() || '',
			'tcsvalue': $('#tcs1' + suffix).val() || ''
		}]);
	});
	return bsmValues;
}

function quotationSetGroupExcursionCost(total_amount, rowCount) {
	if (!isQuotationGroupCostingDiv()) {
		return false;
	}
	$('#tbl_package_tour_quotation_dynamic_costing').find('[id^="excursion_cost-"]').each(function () {
		$(this).val(total_amount);
		if (typeof quotation_cost_calculate === 'function') {
			quotation_cost_calculate(this.id);
		}
	});
	return true;
}

function quotationApplyGroupCostingTransportTotals(unique_package_id_arr, package_type_count) {
	if (!isQuotationGroupCostingDiv()) {
		return false;
	}

	var transport_cost = 0;
	if (unique_package_id_arr.length && unique_package_id_arr[0]) {
		transport_cost = unique_package_id_arr[0]['transport_cost'] || 0;
	}

	$('#tbl_package_tour_quotation_dynamic_costing').find('[id^="tour_cost-"]').each(function () {
		var suffix = this.id.replace('tour_cost', '');
		if (!suffix) {
			return;
		}
		var hotel_cost = parseFloat($('#tour_cost' + suffix).val()) || 0;
		$('#transport_cost1' + suffix).val(transport_cost);
		if (typeof quotation_cost_calculate === 'function') {
			quotation_cost_calculate('tour_cost' + suffix);
		} else {
			$('#total_tour_cost' + suffix).val(parseFloat(transport_cost) + hotel_cost);
			$('#basic_amount' + suffix).val(parseFloat(transport_cost) + hotel_cost);
		}
	});
	return true;
}

/** Persist Tab4 costing so Previous → Next does not wipe manual edits. */
function quotationSaveTab4CostingState() {
	try {
		var entries = typeof quotationCollectGroupCostingEntries === 'function'
			? quotationCollectGroupCostingEntries()
			: null;
		if (entries && entries.length) {
			sessionStorage.setItem('quotation_tab4_costing_state', JSON.stringify(entries));
		}
		sessionStorage.setItem('quotation_tab4_travel_cost_state', JSON.stringify({
			flight_cost: $('#flight_cost').val() || '',
			train_cost: $('#train_cost').val() || '',
			cruise_cost: $('#cruise_cost').val() || '',
			visa_cost: $('#visa_cost').val() || '',
			guide_cost: $('#guide_cost').val() || '',
			misc_cost: $('#misc_cost').val() || '',
			misc_desc: $('#misc_desc').val() || $('#miscellaneous_desc').val() || '',
			currency_code: $('#currency_code').val() || ''
		}));
		sessionStorage.setItem('quotation_tab4_costing_visited', '1');
	} catch (e) {
		console.log('Unable to save tab4 costing state', e);
	}
}

/**
 * After Tab4 costing is rebuilt from Tab3, restore manual fields.
 * options.refreshHotelCost / refreshActivityCost / refreshTransportCost:
 *   true  = keep values just calculated from Tab3
 *   false = restore previous Tab4 manual values (default for transport/service/tax)
 */
function quotationRestoreTab4CostingState(options) {
	options = options || {};
	var refreshHotelCost = options.refreshHotelCost !== false;
	var refreshActivityCost = options.refreshActivityCost !== false;
	var refreshTransportCost = !!options.refreshTransportCost;

	var raw = sessionStorage.getItem('quotation_tab4_costing_state');
	if (!raw) {
		return false;
	}

	var entries;
	try {
		entries = JSON.parse(raw);
	} catch (e) {
		return false;
	}
	if (!entries || !entries.length) {
		return false;
	}

	var byType = {};
	for (var i = 0; i < entries.length; i++) {
		byType[String(entries[i].package_type_c || '')] = entries[i];
	}

	var $costInputs = $('#tbl_package_tour_quotation_dynamic_costing').find('[id^="tour_cost-"]');
	$costInputs.each(function () {
		var suffix = this.id.replace('tour_cost', '');
		if (!suffix) {
			return;
		}
		var pkgType = String($('#package_type' + suffix).val() || '');
		var saved = byType[pkgType];
		if (!saved && entries.length === 1 && $costInputs.length === 1) {
			saved = entries[0];
		}
		if (!saved) {
			return;
		}

		if (!refreshHotelCost && saved.tour_cost !== undefined && saved.tour_cost !== '') {
			$('#tour_cost' + suffix).val(saved.tour_cost);
		}
		if (!refreshTransportCost && saved.transport_cost !== undefined && saved.transport_cost !== '') {
			$('#transport_cost1' + suffix).val(saved.transport_cost);
		}
		if (!refreshActivityCost && saved.excursion_cost !== undefined && saved.excursion_cost !== '') {
			$('#excursion_cost' + suffix).val(saved.excursion_cost);
		}
		if (saved.service_tax !== undefined && saved.service_tax !== '') {
			$('#service_charge' + suffix).val(saved.service_tax);
		}
		if (saved.discount_in) {
			$('#discount_in' + suffix).val(saved.discount_in);
		}
		if (saved.discount !== undefined && saved.discount !== '') {
			$('#discount_amt' + suffix).val(saved.discount);
		}
		if (saved.tax_apply_on) {
			$('#tax_apply_on' + suffix).val(saved.tax_apply_on);
		}
		if (saved.tax_value) {
			$('#tax_value' + suffix).val(saved.tax_value);
		}
		if (saved.service_tax_subtotal !== undefined) {
			$('#service_tax_subtotal' + suffix).val(saved.service_tax_subtotal);
		}
		if (saved.tcs !== undefined) {
			$('#tcs_tax' + suffix).val(saved.tcs);
		}
		if (saved.tcsvalue !== undefined) {
			$('#tcs1' + suffix).val(saved.tcsvalue);
		}
		if (typeof quotation_cost_calculate === 'function') {
			quotation_cost_calculate('tour_cost' + suffix);
		}
	});

	var travelRaw = sessionStorage.getItem('quotation_tab4_travel_cost_state');
	if (travelRaw) {
		try {
			var travel = JSON.parse(travelRaw);
			if (travel.flight_cost !== undefined) $('#flight_cost').val(travel.flight_cost);
			if (travel.train_cost !== undefined) $('#train_cost').val(travel.train_cost);
			if (travel.cruise_cost !== undefined) $('#cruise_cost').val(travel.cruise_cost);
			if (travel.visa_cost !== undefined) $('#visa_cost').val(travel.visa_cost);
			if (travel.guide_cost !== undefined) $('#guide_cost').val(travel.guide_cost);
			if (travel.misc_cost !== undefined) $('#misc_cost').val(travel.misc_cost);
			if (travel.misc_desc !== undefined) {
				if ($('#misc_desc').length) $('#misc_desc').val(travel.misc_desc);
				if ($('#miscellaneous_desc').length) $('#miscellaneous_desc').val(travel.misc_desc);
			}
			if (travel.currency_code && $('#currency_code').length) {
				$('#currency_code').val(travel.currency_code).trigger('change');
			}
		} catch (e2) {}
	}
	return true;
}

