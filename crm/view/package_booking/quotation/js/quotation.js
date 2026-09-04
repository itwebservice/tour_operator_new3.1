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
                if (typeof quotationInitFeatureEditors === 'function') {
                    quotationInitFeatureEditors('#package_name_div');
                } else if (typeof $().wysiwyg === 'function') {
                    $('#package_name_div textarea.feature_editor').each(function () {
                        var $el = $(this);
                        if ($el.data('wysiwyg')) {
                            return;
                        }
                        var existing = $el.val() || '';
                        $el.wysiwyg({
                            controls: 'bold,italic,|,undo,redo,image|h1,h2,h3,decreaseFontSize,highlight',
                            initialContent: ''
                        });
                        if (existing && $el.data('wysiwyg')) {
                            try { $el.wysiwyg('setContent', existing); } catch (e) {}
                        }
                    });
                }
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

	// Pax change affects hotel PP split (adult_share ÷ adults) and child tariff inclusion — refresh costs
	if (typeof quotationRefreshHotelCostingAfterPaxChange === 'function') {
		quotationRefreshHotelCostingAfterPaxChange();
	}
}

function group_quotation_cost_calculate(id) {

	var adult_cost = $('#adult_cost').val();
	var children_cost = $('#children_cost').val();
	var infant_cost = $('#infant_cost').val();
	var with_bed_cost = $('#with_bed_cost').val();
	var extra_bed_cost = $('#extra_bed_cost').val();
	var single_person_cost = $('#single_person_cost').val();

	if (adult_cost == '' || isNaN(adult_cost)) adult_cost = 0;
	if (children_cost == '' || isNaN(children_cost)) children_cost = 0;
	if (infant_cost == '' || isNaN(infant_cost)) infant_cost = 0;
	if (with_bed_cost == '' || isNaN(with_bed_cost)) with_bed_cost = 0;
	if (extra_bed_cost == '' || isNaN(extra_bed_cost)) extra_bed_cost = 0;
	if (single_person_cost == '' || isNaN(single_person_cost)) single_person_cost = 0;

	var total = parseFloat(adult_cost) + parseFloat(children_cost) + parseFloat(infant_cost) + parseFloat(with_bed_cost) + parseFloat(single_person_cost) + parseFloat(extra_bed_cost);
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
		'transport_cost1', 'atax_apply_on', 'tax_value1', 'tax_apply_on', 'tax_value',
		'excursion_cost', 'basic_amount', 'service_charge',
		'discount_amt', 'discount_in',
		'service_tax_subtotal', 'tcs_tax', 'tcs1', 'total_tour_cost',
		'package_type', 'package_id1', 'package_name1', 'tour_cost'
	];
	for (var i = 0; i < prefixes.length; i++) {
		if (fieldId.indexOf(prefixes[i]) === 0) {
			var rest = fieldId.slice(prefixes[i].length);
			return rest === '' ? '-' : rest;
		}
	}
	var dash = fieldId.indexOf('-');
	return dash >= 0 ? fieldId.slice(dash) : '-';
}

function quotationSyncDerivedCostingAmount($el, amount) {
	if (!$el || !$el.length) {
		return;
	}
	var n = parseFloat(amount);
	if (isNaN(n)) {
		n = 0;
	}
	var formatted = n.toFixed(2);
	$el.val(formatted);
	$el.attr('data-default-amount', formatted);
}

function quotationGroupInclusiveShowAmount(suffix) {
	var basicShow = ($('#basic_show' + suffix).find('span').text() || '').trim();
	var serviceShow = ($('#service_show' + suffix).find('span').text() || '').trim();
	return { basic: basicShow, service: serviceShow };
}

function quotation_cost_calculate(id) {

	if (!id) {
		id = 'tour_cost-';
	}
	var suffix = quotationCostingFieldSuffix(id);
	if (!suffix) {
		suffix = '-';
	}
	var tour_cost = $('#tour_cost' + suffix).val();
	var transport_cost = $('#transport_cost1' + suffix).val();
	if (transport_cost === undefined || transport_cost === null || transport_cost === '') {
		transport_cost = $('#transport_cost' + suffix).val();
	}
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
	quotationSyncDerivedCostingAmount($('#basic_amount' + suffix), sub_total);

	if (id != 'basic_amount' + suffix && typeof get_business === 'function' && !window.quotationSkipGetBusiness) {
		try {
			get_business('basic_amount' + suffix, 'true');
		} catch (err) {
			if (typeof console !== 'undefined' && console.warn) {
				console.warn('get_business skipped during costing calculate', err);
			}
		}
	}
	var service_charge = $('#service_charge' + suffix).val();
	var service_tax_subtotal = $('#service_tax_subtotal' + suffix).val();
	if (service_charge == '') {
		service_charge = 0;
	}
	var service_tax_amount = 0;
	if (typeof service_tax_subtotal === 'string' && service_tax_subtotal !== '' && parseFloat(service_tax_subtotal) !== 0.0) {
		var service_tax_subtotal1 = service_tax_subtotal.split(',');
		for (var i = 0; i < service_tax_subtotal1.length; i++) {
			var service_tax = String(service_tax_subtotal1[i] || '').split(':');
			service_tax_amount = parseFloat(service_tax_amount) + (parseFloat(service_tax[2]) || 0);
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
    if (typeof customTcsTax === 'function') {
        customTcsTax('tcs_tax' + suffix);
    }

    var tcs_amt = $('#tcs1' + suffix).val();
    if (tcs_amt == '') {
        tcs_amt = 0;
    }
	
	var total_amt = parseFloat(sub_total) + parseFloat(after_discount_amt) + parseFloat(service_tax_amount) + parseFloat(tcs_amt);
	if (isNaN(total_amt)) {
		total_amt = parseFloat(sub_total) + parseFloat(service_charge) + parseFloat(service_tax_amount);
	}
	quotationSyncDerivedCostingAmount($('#total_tour_cost' + suffix), total_amt);
	quotationSyncDerivedCostingAmount($('#tcs1' + suffix), tcs_amt);
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

			var tourName = String(result.tour_name || '').replace(/\s+/g, ' ').trim();
			$('#tour_name' + offset).val(tourName);
			$('#total_days' + offset).val(result.total_days);
			
			// Update sessionStorage with enquiry data
			if (tourName) {
				console.log('Enquiry loaded - Processing tour_name:', tourName);
				
				// Clear existing destination storage first
				sessionStorage.removeItem('selected_destination_id');
				sessionStorage.removeItem('selected_destination_name');
				
				// Find destination ID from the destinations list
				var destinations = JSON.parse($('#destinations').val() || '[]');
				console.log('Enquiry loaded - Available destinations:', destinations.length);
				console.log('Enquiry loaded - First few destinations:', destinations.slice(0, 3));
				
				var found = false;
				var tourNameCmp = tourName.toLowerCase();
				for (var i = 0; i < destinations.length; i++) {
					var destLabel = String(destinations[i].label || '').replace(/\s+/g, ' ').trim();
					console.log('Enquiry loaded - Checking destination:', destLabel, 'against:', tourName);
					if (destLabel.toLowerCase() === tourNameCmp) {
						sessionStorage.setItem('selected_destination_id', destinations[i].dest_id);
						sessionStorage.setItem('selected_destination_name', destLabel);
						$('#tour_name' + offset).val(destLabel);
						console.log('Enquiry loaded - Updated sessionStorage with destination:', destLabel, 'ID:', destinations[i].dest_id);
						found = true;
						break;
					}
				}
				
				if (!found) {
					console.log('Enquiry loaded - Destination not found in list:', tourName);
					
					// Try to add the destination to the dropdown and create a temporary ID
					var tempId = 'temp_' + Date.now();
					var newOption = $("<option selected='selected'></option>").val(tempId).text(tourName);
					$('#dest_name').append(newOption).trigger('change.select2');
					
					// Store with temporary ID
					sessionStorage.setItem('selected_destination_id', tempId);
					sessionStorage.setItem('selected_destination_name', tourName);
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
			if (typeof quotationSyncExcursionDefaultsFromTravel === 'function') {
				quotationSyncExcursionDefaultsFromTravel({ forcePax: true });
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
	if ($main.length) {
		var html = $main.html();
		if (html && html.indexOf('<option') !== -1) {
			quotationPackageTypeOptionsHtml = html;
		}
	}
	if (!quotationPackageTypeOptionsHtml) {
		var $rowSelect = jQuery('#tbl_package_tour_quotation_dynamic_hotel_update select[id*="package_type"], #tbl_package_tour_quotation_dynamic_hotel select[id*="package_type"]').filter(function () {
			return jQuery(this).find('option').length > 0;
		}).first();
		if ($rowSelect.length) {
			quotationPackageTypeOptionsHtml = $rowSelect.html();
		}
	}
	if (!quotationPackageTypeOptionsHtml) {
		quotationPackageTypeOptionsHtml = '<option value="ECONOMY">ECONOMY</option><option value="LUXURY">LUXURY</option><option value="PREMIUM">PREMIUM</option><option value="ROYAL PACKAGE">ROYAL PACKAGE</option><option value="STANDARD">STANDARD</option>';
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
	if ($main.length) {
		var val = $main.find('option:first').val();
		if (val) {
			return val;
		}
	}
	var table = document.getElementById('tbl_package_tour_quotation_dynamic_hotel_update')
		|| document.getElementById('tbl_package_tour_quotation_dynamic_hotel');
	if (table && table.rows.length && typeof quotationGetHotelRowPackageType === 'function') {
		var existing = quotationGetHotelRowPackageType(table.rows[0]);
		if (existing) {
			return existing;
		}
	}
	return 'ECONOMY';
}

function quotationInitEditablePackageTypeSelect(row, selectedValue) {
	var pkgEl = typeof quotationGetHotelRowPackageSelect === 'function'
		? quotationGetHotelRowPackageSelect(row)
		: (row && row.cells && row.cells[2] ? row.cells[2].querySelector('select') : null);
	if (!pkgEl) {
		return;
	}
	quotationCachePackageTypeOptions();
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
	var pkgEl = typeof quotationGetHotelRowPackageSelect === 'function'
		? quotationGetHotelRowPackageSelect(row)
		: (row.cells[2] && row.cells[2].querySelector('select'));
	if (pkgEl) {
		var $pkg = jQuery(pkgEl);
		if ($pkg.data('select2')) {
			$pkg.select2('destroy');
		}
		if (quotationPackageTypeOptionsHtml) {
			$pkg.html(quotationPackageTypeOptionsHtml);
		}
		var pkgVal = options.packageType || quotationGetDefaultPackageType();
		if (pkgVal && !$pkg.find('option[value="' + pkgVal + '"]').length) {
			$pkg.append(new Option(pkgVal, pkgVal, true, true));
		}
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

function quotationGetHotelRowCellControl(row, cellIndex) {
	if (!row || !row.cells || !row.cells[cellIndex]) {
		return null;
	}
	return row.cells[cellIndex].querySelector('select, input, textarea') || row.cells[cellIndex].childNodes[0] || null;
}

function quotationGetHotelRowReference(row) {
	if (!row || !row.cells) return null;
	var cityEl = quotationGetHotelRowCellControl(row, 3);
	var $city = cityEl ? jQuery(cityEl) : null;
	var checkInInput = quotationGetTableDateInput(row, 6);
	var checkOutInput = quotationGetTableDateInput(row, 7);
	var mealEl = quotationGetHotelRowCellControl(row, 16);
	var hotelEl = quotationGetHotelRowCellControl(row, 4);
	var $hotel = hotelEl ? jQuery(hotelEl) : null;
	var roomCatEl = quotationGetHotelRowCellControl(row, 5);
	var $roomCat = roomCatEl ? jQuery(roomCatEl) : null;
	var packageNameEl = quotationGetHotelRowCellControl(row, 12);
	var packageIdEl = quotationGetHotelRowCellControl(row, 14);
	return {
		package_type: typeof quotationGetHotelRowPackageType === 'function' ? quotationGetHotelRowPackageType(row) : (row.cells[2] && row.cells[2].childNodes[0] ? (jQuery(row.cells[2].childNodes[0]).val() || '') : ''),
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
		package_name: packageNameEl ? packageNameEl.value : '',
		package_id: packageIdEl ? packageIdEl.value : '',
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
	if (ref.package_type) {
		if (typeof quotationSetHotelRowPackageType === 'function') {
			quotationSetHotelRowPackageType(row, ref.package_type);
		} else {
			var pkgEl = typeof quotationGetHotelRowPackageSelect === 'function'
				? quotationGetHotelRowPackageSelect(row)
				: (row.cells[2] ? row.cells[2].querySelector('select') : null);
			if (pkgEl) {
				var $pkg = jQuery(pkgEl);
				if (!$pkg.find('option[value="' + ref.package_type + '"]').length) {
					$pkg.append(new Option(ref.package_type, ref.package_type, true, true));
				}
				$pkg.val(ref.package_type);
				if ($pkg.data('select2')) {
					$pkg.trigger('change.select2');
				}
			}
		}
	}
	var cityEl = quotationGetHotelRowCellControl(row, 3);
	var hotelEl = quotationGetHotelRowCellControl(row, 4);
	var $hotel = hotelEl ? jQuery(hotelEl) : null;
	var applyStayDetails = function () {
		var roomCatEl = quotationGetHotelRowCellControl(row, 5);
		if (ref.room_cat_id && roomCatEl) {
			var $roomCat = jQuery(roomCatEl);
			if ($roomCat.find('option').filter(function () {
				return String(this.value) === String(ref.room_cat_id);
			}).length === 0) {
				$roomCat.append(new Option(ref.room_cat_name || '', ref.room_cat_id, true, true));
			}
			$roomCat.val(String(ref.room_cat_id));
			if ($roomCat.data('select2')) {
				$roomCat.trigger('change.select2');
			}
		}
		if (ref.hotel_type && row.cells[8] && row.cells[8].childNodes[0]) {
			row.cells[8].childNodes[0].value = ref.hotel_type;
		}
	};
	var applyHotelSelection = function () {
		if (!ref.hotel_id || !$hotel || !$hotel.length) {
			applyStayDetails();
			return;
		}
		var hotelNode = $hotel[0];
		var savedOnchange = hotelNode ? hotelNode.getAttribute('onchange') : '';
		if (savedOnchange) {
			hotelNode.removeAttribute('onchange');
		}
		window.quotationSkipHotelTypeAutoLoad = true;
		if ($hotel.find('option').filter(function () {
			return String(this.value) === String(ref.hotel_id);
		}).length === 0) {
			$hotel.append(new Option(ref.hotel_name || '', ref.hotel_id, true, true));
		}
		$hotel.val(String(ref.hotel_id));
		if ($hotel.data('select2')) {
			$hotel.trigger('change.select2');
		}
		window.quotationSkipHotelTypeAutoLoad = false;
		if (savedOnchange && hotelNode) {
			hotelNode.setAttribute('onchange', savedOnchange);
		}
		applyStayDetails();
	};
	if (ref.city_id && ref.city_name && cityEl) {
		window.quotationSkipCityHotelAutoLoad = true;
		if (typeof setQuotationCitySelect === 'function') {
			setQuotationCitySelect(cityEl, ref.city_id, ref.city_name);
		} else if (typeof city_lzloading === 'function') {
			city_lzloading(cityEl);
			var cityOption = new Option(ref.city_name, ref.city_id, true, true);
			jQuery(cityEl).append(cityOption).trigger('change.select2');
		}
		window.quotationSkipCityHotelAutoLoad = false;
		if (!options.skipHotelLoad && $hotel && $hotel.length && typeof hotelDropdownLoadByCity === 'function') {
			hotelDropdownLoadByCity(ref.city_id, $hotel, function () {
				applyHotelSelection();
			});
		} else if (!options.skipHotelLoad && cityEl.id && typeof hotel_name_list_load === 'function') {
			hotel_name_list_load(cityEl.id);
			applyHotelSelection();
		} else {
			applyHotelSelection();
		}
	} else {
		applyHotelSelection();
	}
	if (options.chainDates) {
		if (ref.check_out && row.cells[6] && row.cells[6].childNodes[0]) {
			row.cells[6].childNodes[0].value = ref.check_out;
		}
		if (row.cells[7] && row.cells[7].childNodes[0]) {
			row.cells[7].childNodes[0].value = '';
		}
		if (row.cells[9] && row.cells[9].childNodes[0]) {
			row.cells[9].childNodes[0].value = '';
		}
	} else {
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
	}
	if (ref.total_rooms && row.cells[10] && row.cells[10].childNodes[0]) {
		row.cells[10].childNodes[0].value = ref.total_rooms;
	}
	if (ref.extra_bed !== undefined && ref.extra_bed !== null && row.cells[11] && row.cells[11].childNodes[0]) {
		row.cells[11].childNodes[0].value = ref.extra_bed;
	}
	if (ref.package_name && row.cells[12] && row.cells[12].childNodes[0]) {
		row.cells[12].childNodes[0].value = ref.package_name;
	}
	if (ref.package_id && row.cells[14] && row.cells[14].childNodes[0]) {
		row.cells[14].childNodes[0].value = ref.package_id;
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

function quotationNormalizeIdList(arr) {
	return (arr || []).map(function (id) {
		return String(id);
	}).filter(function (id) {
		return id !== '' && id !== 'undefined';
	}).sort().join(',');
}

function quotationHotelTableHasStayData() {
	var table = document.getElementById('tbl_package_tour_quotation_dynamic_hotel');
	if (!table) return false;
	for (var i = 0; i < table.rows.length; i++) {
		var row = table.rows[i];
		var hotelEl = typeof quotationGetHotelRowCellControl === 'function'
			? quotationGetHotelRowCellControl(row, 4)
			: (row.cells[4] && row.cells[4].childNodes[0]);
		var cityEl = typeof quotationGetHotelRowCellControl === 'function'
			? quotationGetHotelRowCellControl(row, 3)
			: (row.cells[3] && row.cells[3].childNodes[0]);
		var hotelVal = hotelEl ? (jQuery(hotelEl).val() || hotelEl.value || '') : '';
		var cityVal = cityEl ? (jQuery(cityEl).val() || cityEl.value || '') : '';
		if (hotelVal || cityVal) {
			return true;
		}
	}
	return false;
}

function quotationTransportTableHasStayData() {
	var table = document.getElementById('tbl_package_tour_quotation_dynamic_transport');
	if (!table) return false;
	for (var i = 0; i < table.rows.length; i++) {
		var row = table.rows[i];
		if (!row.cells || !row.cells[2]) continue;
		var vehicleEl = row.cells[2].childNodes[0];
		var pickupEl = row.cells[5] && row.cells[5].childNodes[0];
		var vehicleVal = vehicleEl ? (jQuery(vehicleEl).val() || vehicleEl.value || '') : '';
		var pickupVal = pickupEl ? (jQuery(pickupEl).val() || pickupEl.value || '') : '';
		if (vehicleVal || pickupVal) {
			return true;
		}
	}
	return false;
}

function quotationGetPreviouslyLoadedPackageIds() {
	if (window.quotationTab3LoadedPackages && window.quotationTab3LoadedPackages.length) {
		return window.quotationTab3LoadedPackages.slice();
	}
	var saved = sessionStorage.getItem('selected_packages_tab3');
	if (saved) {
		try {
			var parsed = JSON.parse(saved);
			if (parsed && parsed.length) {
				return parsed;
			}
		} catch (e) {}
	}
	if (typeof quotationGetExistingHotelPackageIds === 'function') {
		return Object.keys(quotationGetExistingHotelPackageIds());
	}
	return [];
}

function quotationShouldKeepExistingStayData(package_id_arr) {
	if (!quotationHotelTableHasStayData() && !quotationTransportTableHasStayData()) {
		return false;
	}
	var prev = quotationGetPreviouslyLoadedPackageIds();
	if (!prev.length) {
		return true;
	}
	return quotationNormalizeIdList(prev) === quotationNormalizeIdList(package_id_arr);
}

function quotationRememberLoadedPackages(package_id_arr) {
	window.quotationTab3LoadedPackages = (package_id_arr || []).slice();
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

function quotationGetRowField(row, prefixes) {
	if (!row) return null;
	prefixes = prefixes instanceof Array ? prefixes : [prefixes];
	for (var i = 0; i < prefixes.length; i++) {
		var el = row.querySelector('[id^="' + prefixes[i] + '"]');
		if (el) return el;
	}
	return null;
}

function quotationSyncFlightAndExcursionDatesFromHotels() {
	var hotelDates = quotationGetHotelDateList();
	var travelDates = quotationGetTravelDates();
	var fallbackDate = '';
	if (hotelDates.length) {
		fallbackDate = hotelDates[0].check_in;
	} else if (travelDates.from_date) {
		fallbackDate = travelDates.from_date.split(' ')[0];
	}

	var excTable = document.getElementById('tbl_package_tour_quotation_dynamic_excursion');
	if (excTable) {
		var excIdx = 0;
		for (var e = 0; e < excTable.rows.length; e++) {
			var excRow = excTable.rows[e];
			var excInput = quotationGetTableDateInput(excRow, 2);
			if (!excInput) continue;
			var slotDate = fallbackDate;
			if (hotelDates.length) {
				var slot = hotelDates[Math.min(excIdx, hotelDates.length - 1)];
				slotDate = slot.check_in || fallbackDate;
			}
			if (slotDate) {
				var timePart = ' 00:00';
				if (excInput.value && excInput.value.indexOf(' ') > -1) {
					timePart = excInput.value.substring(excInput.value.indexOf(' '));
				} else if (String(slotDate).indexOf(' ') > -1) {
					timePart = '';
					slotDate = String(slotDate);
				}
				quotationSetInputDateValue(
					excInput,
					timePart ? (String(slotDate).split(' ')[0] + timePart) : slotDate
				);
			}
			excIdx++;
		}
	}

	var planeTable = document.getElementById('tbl_package_tour_quotation_dynamic_plane')
		|| document.getElementById('tbl_package_tour_quotation_dynamic_plane_update');
	if (planeTable && planeTable.rows.length && fallbackDate) {
		var firstDate = hotelDates.length ? hotelDates[0].check_in : fallbackDate;
		var lastDate = hotelDates.length
			? (hotelDates[hotelDates.length - 1].check_out || hotelDates[hotelDates.length - 1].check_in)
			: fallbackDate;
		var planeIndex = 0;
		for (var p = 0; p < planeTable.rows.length; p++) {
			var planeRow = planeTable.rows[p];
			var departInput = quotationGetTableDateInput(planeRow, 6) || quotationGetRowField(planeRow, ['txt_dapart']);
			var arriveInput = quotationGetTableDateInput(planeRow, 7) || quotationGetRowField(planeRow, ['txt_arrval']);
			if (!departInput) continue;
			var dateValue = planeIndex === 0 ? firstDate : lastDate;
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
			planeIndex++;
		}
	}

	if (typeof quotationSyncExcursionPaxFromTab1 === 'function') {
		quotationSyncExcursionPaxFromTab1();
	}
}

/**
 * Copy Tab1 / dashboard pax counts into every Activity (excursion) row.
 * adult / CWEB / CWNB / infant fields on the activity table.
 */
function quotationSyncExcursionPaxFromTab1(options) {
	options = options || {};
	var force = options.force !== false;
	var adult = $('#total_adult12').val();
	if (adult === undefined || adult === null || adult === '') {
		adult = $('#total_adult').val();
	}
	var cweb = $('#children_with_bed12').val();
	if (cweb === undefined || cweb === null || cweb === '') {
		cweb = $('#children_with_bed').val();
	}
	var cwnb = $('#children_without_bed12').val();
	if (cwnb === undefined || cwnb === null || cwnb === '') {
		cwnb = $('#children_without_bed').val();
	}
	var infant = $('#total_infant12').val();
	if (infant === undefined || infant === null || infant === '') {
		infant = $('#total_infant').val();
	}

	if (adult === undefined || adult === null || adult === '') adult = 0;
	if (cweb === undefined || cweb === null || cweb === '') cweb = 0;
	if (cwnb === undefined || cwnb === null || cwnb === '') cwnb = 0;
	if (infant === undefined || infant === null || infant === '') infant = 0;

	var table = document.getElementById('tbl_package_tour_quotation_dynamic_excursion');
	if (!table || !table.rows) return;

	for (var i = 0; i < table.rows.length; i++) {
		var row = table.rows[i];
		if (!row || !row.cells) continue;
		function setField(prefixes, value) {
			var el = quotationGetRowField(row, prefixes);
			if (!el) {
				return;
			}
			if (!force) {
				var cur = el.value;
				if (cur !== '' && cur !== null && typeof cur !== 'undefined' && String(cur) !== '0') {
					return;
				}
			}
			el.value = value;
		}
		setField(['adult-'], adult);
		setField(['child-', 'chwb-'], cweb);
		setField(['childwo-', 'chwob-'], cwnb);
		setField(['infant-'], infant);
	}
}

/**
 * Ensure activity date follows travel from-date (datetime) and pax follow Tab1/dashboard.
 */
function quotationSyncExcursionDefaultsFromTravel(options) {
	options = options || {};
	var dates = quotationGetTravelDates();
	var from_date = dates.from_date || $('#from_date12').val() || $('#from_date').val() || '';
	var datePart = from_date ? String(from_date).split(' ')[0] : '';

	var hotelDates = typeof quotationGetHotelDateList === 'function' ? quotationGetHotelDateList() : [];
	if (hotelDates.length && hotelDates[0].check_in) {
		datePart = String(hotelDates[0].check_in).split(' ')[0];
	}

	var table = document.getElementById('tbl_package_tour_quotation_dynamic_excursion');
	if (table && datePart) {
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			var excInput = quotationGetTableDateInput(row, 2);
			if (!excInput) continue;
			var timePart = ' 00:00';
			if (excInput.value && excInput.value.indexOf(' ') > -1) {
				timePart = excInput.value.substring(excInput.value.indexOf(' '));
			}
			if (options.onlyMissing && excInput.value) {
				continue;
			}
			quotationSetInputDateValue(excInput, datePart + timePart);
		}
	} else if (datePart) {
		var $exc = $('#exc_date-1, #exc_date-1_u');
		$exc.each(function () {
			var timePart = ' 00:00';
			if (this.value && this.value.indexOf(' ') > -1) {
				timePart = this.value.substring(this.value.indexOf(' '));
			}
			if (options.onlyMissing && this.value) return;
			$(this).val(datePart + timePart);
		});
	}

	quotationSyncExcursionPaxFromTab1({ force: options.forcePax !== false });
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
	jQuery('input[id^="txt_dapart"], input[id^="txt_arrval"], input[id^="train_departure_date"], input[id^="train_arrival_date"], input[id^="cruise_departure_date"], input[id^="cruise_arrival_date"], input[id^="exc_date-"]').each(function () {
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

function quotationIsPackageQuotationUpdate() {
	var id = $('#quotation_id1').val() || $('#quotation_id').val() || '';
	return !!(id && String(id) !== '0');
}

function quotationGroupCostingLandInputs() {
	return $('#tbl_package_tour_quotation_dynamic_costing')
		.find('input[id^="transport_cost"], input[id^="excursion_cost"]')
		.filter(function () {
			return !/_u$/.test(this.id);
		});
}

function quotationProtectUpdateGroupLandField($el) {
	if (!$el || !$el.length) {
		return false;
	}
	var id = String($el.attr('id') || '');
	if (!/^(transport_cost|excursion_cost)/.test(id) || /_u$/.test(id)) {
		return false;
	}
	if (typeof quotationIsPackageQuotationUpdate === 'function' && !quotationIsPackageQuotationUpdate()) {
		return false;
	}
	var savedRaw = $el.attr('data-saved-amount');
	var saved = parseFloat(String(savedRaw != null && savedRaw !== '' ? savedRaw : '').replace(/,/g, '')) || 0;
	var cur = parseFloat(String($el.val() || '').replace(/,/g, '')) || 0;
	var isManual = typeof quotationIsManualAmountField === 'function' && quotationIsManualAmountField($el);
	if (!isManual && saved <= 0 && cur <= 0) {
		return false;
	}
	if (saved > 0 && (cur === 0 || (typeof quotationIsTariffAmountField === 'function' && quotationIsTariffAmountField($el)))) {
		$el.val(Number(saved).toFixed(2));
		$el.attr('data-default-amount', saved);
	} else if (cur > 0 && (savedRaw == null || savedRaw === '')) {
		$el.attr('data-saved-amount', cur);
	}
	if (typeof quotationMarkFieldAsManualAmount === 'function') {
		quotationMarkFieldAsManualAmount($el);
	}
	return true;
}

function quotationMarkSavedGroupLandAsManual() {
	quotationGroupCostingLandInputs().each(function () {
		var $el = $(this);
		var savedRaw = $el.attr('data-saved-amount');
		var saved = parseFloat(String(savedRaw != null && savedRaw !== '' ? savedRaw : $el.val() || '').replace(/,/g, '')) || 0;
		if (saved <= 0) {
			return;
		}
		if (savedRaw == null || savedRaw === '') {
			$el.attr('data-saved-amount', saved);
		}
		$el.attr('data-default-amount', saved);
		if (typeof quotationMarkFieldAsManualAmount === 'function') {
			quotationMarkFieldAsManualAmount($el);
		}
	});
}

function quotationRestoreSavedGroupLandAmounts() {
	var restored = false;
	quotationGroupCostingLandInputs().each(function () {
		var $el = $(this);
		var saved = parseFloat(String($el.attr('data-saved-amount') || '').replace(/,/g, ''));
		if (isNaN(saved) || saved === 0) {
			return;
		}
		var overwrittenByTariff = typeof quotationIsTariffAmountField === 'function' && quotationIsTariffAmountField($el);
		var cur = parseFloat(String($el.val() || '').replace(/,/g, '')) || 0;
		if (overwrittenByTariff || cur === 0) {
			$el.val(Number(saved).toFixed(2));
			if (typeof quotationMarkFieldAsManualAmount === 'function') {
				quotationMarkFieldAsManualAmount($el);
			}
			restored = true;
			if (typeof quotation_cost_calculate1 === 'function') {
				quotation_cost_calculate1(this.id);
			} else if (typeof quotation_cost_calculate === 'function') {
				quotation_cost_calculate(this.id);
			}
		}
	});
	return restored;
}

function quotationRecalculateTravelStayCosts(isUpdate) {
	if (typeof get_hotel_cost === 'function') {
		if (isUpdate) {
			get_hotel_cost(null, { forceHotel: false });
		} else {
			get_hotel_cost();
		}
	}
	if (typeof get_transport_cost === 'function') {
		get_transport_cost(isUpdate ? { preserveSaved: true } : {});
	} else if (isUpdate && typeof get_transport_cost_update === 'function') {
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
		get_excursion_amount(isUpdate ? { preserveSaved: true } : {});
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
		if (typeof quotationSyncExcursionDefaultsFromTravel === 'function') {
			quotationSyncExcursionDefaultsFromTravel({
				forcePax: !dates.isUpdate,
				onlyMissing: !!dates.isUpdate
			});
		}
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

	if (typeof quotationSyncExcursionDefaultsFromTravel === 'function') {
		quotationSyncExcursionDefaultsFromTravel({
			forcePax: !dates.isUpdate,
			onlyMissing: !!dates.isUpdate
		});
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

function quotationGetHotelRowPackageSelect(row) {
	if (!row || !row.cells || !row.cells[2]) {
		return null;
	}
	return row.cells[2].querySelector('select');
}

function quotationGetHotelRowPackageType(row) {
	var el = quotationGetHotelRowPackageSelect(row);
	if (!el) {
		return '';
	}
	var val = jQuery(el).val() || el.value || '';
	return String(val).trim();
}

function quotationSetHotelRowPackageType(row, packageType) {
	if (!row || !packageType || packageType === '*Package Type') {
		return;
	}
	if (typeof quotationInitEditablePackageTypeSelect === 'function') {
		quotationInitEditablePackageTypeSelect(row, packageType);
	}
	var el = quotationGetHotelRowPackageSelect(row);
	if (!el) {
		return;
	}
	var $pkg = jQuery(el);
	if (!$pkg.find('option[value="' + packageType + '"]').length) {
		$pkg.append(new Option(packageType, packageType, true, true));
	}
	$pkg.val(packageType);
	if ($pkg.data('select2')) {
		$pkg.trigger('change.select2');
	}
}

/** Unique package types from checked hotel rows (save + update tables). */
function quotationGetCheckedHotelPackageTypes() {
	var table = document.getElementById('tbl_package_tour_quotation_dynamic_hotel_update')
		|| document.getElementById('tbl_package_tour_quotation_dynamic_hotel');
	var types = [];
	if (!table || !table.rows) {
		return types;
	}
	for (var i = 0; i < table.rows.length; i++) {
		var row = table.rows[i];
		if (!row.cells[0] || !row.cells[0].childNodes[0] || !row.cells[0].childNodes[0].checked) {
			continue;
		}
		var ptype = quotationGetHotelRowPackageType(row);
		if (ptype && ptype !== '*Package Type' && types.indexOf(ptype) === -1) {
			types.push(ptype);
		}
	}
	return types;
}

/**
 * Remove per-person / group costing blocks for package types with no checked hotels.
 * Used on update (and save) so deselected package types do not keep tariff/transfer costs.
 */
function quotationSyncCostingUiToHotelSelection() {
	var allowed = quotationGetCheckedHotelPackageTypes();
	// If no checked hotel package types were detected, keep existing costing rows.
	// Otherwise a transient empty selection wipes PP/group blocks and update saves zeros.
	if (!allowed.length) {
		return allowed;
	}
	var allowedMap = {};
	for (var a = 0; a < allowed.length; a++) {
		allowedMap[String(allowed[a])] = true;
	}

	function packageAllowed(pkg) {
		pkg = String(pkg || '').trim();
		// Keep unidentified / template rows. Wiping them leaves Tab4 empty and
		// save fails with "Please enter land costing details before saving."
		if (!pkg || pkg === 'NA') {
			return true;
		}
		return !!allowedMap[pkg];
	}

	var $ppContainer = $('#quotation_pp_costing_container');
	if ($ppContainer.length) {
		$ppContainer.find('.quotation-pp-costing-row').each(function () {
			var $row = $(this);
			var pkg = $row.attr('data-package-type')
				|| $row.find('[id^="ppackage_type1"]').first().val()
				|| '';
			if (!packageAllowed(pkg)) {
				var $prev = $row.prev('hr.quotation-package-costing-separator');
				var $next = $row.next('hr.quotation-package-costing-separator');
				if ($prev.length) {
					$prev.remove();
				} else if ($next.length) {
					$next.remove();
				}
				$row.remove();
			}
		});
		// Keep separators tidy between remaining rows
		$ppContainer.find('hr.quotation-package-costing-separator').each(function () {
			var $hr = $(this);
			if (!$hr.next('.quotation-pp-costing-row').length || !$hr.prev('.quotation-pp-costing-row').length) {
				$hr.remove();
			}
		});
		// Renumber suffixes for remaining PP rows
		var $ppRows = $ppContainer.find('.quotation-pp-costing-row');
		var ppTotal = $ppRows.length;
		$ppRows.each(function (idx) {
			var suffix = (typeof quotationPpCostingSuffix === 'function')
				? quotationPpCostingSuffix(idx, ppTotal)
				: (ppTotal <= 1 ? '' : ('-' + (idx + 1)));
			if (typeof quotationPpCostingRenumberRow === 'function') {
				quotationPpCostingRenumberRow($(this), suffix);
			}
		});
	}

	var $groupRoot = $('#tbl_package_tour_quotation_dynamic_costing');
	if ($groupRoot.length && $groupRoot.is('div')) {
		$groupRoot.find('.quotation-group-costing-row').each(function () {
			var $row = $(this);
			var pkg = $row.find('[id^="package_type-"]').first().val() || '';
			if (!packageAllowed(pkg)) {
				var $prev = $row.prev('hr.quotation-package-costing-separator');
				var $next = $row.next('hr.quotation-package-costing-separator');
				if ($prev.length) {
					$prev.remove();
				} else if ($next.length) {
					$next.remove();
				}
				$row.remove();
			}
		});
		$groupRoot.find('hr.quotation-package-costing-separator').each(function () {
			var $hr = $(this);
			if (!$hr.next('.quotation-group-costing-row').length || !$hr.prev('.quotation-group-costing-row').length) {
				$hr.remove();
			}
		});
	}

	return allowed;
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
	if (typeof quotationWritePpDefaultCurrencyAmount === 'function') {
		quotationWritePpDefaultCurrencyAmount($row.find('#tour_cost' + suffix), hotelCost);
		quotationWritePpDefaultCurrencyAmount($row.find('#excursion_cost' + suffix), excursionCost);
		var $tr = $row.find('#transport_cost1' + suffix);
		if (!$tr.length) {
			$tr = $row.find('#transport_cost' + suffix);
		}
		quotationWritePpDefaultCurrencyAmount($tr, transportCost);
	} else {
		$row.find('#tour_cost' + suffix).val(hotelCost);
		$row.find('#excursion_cost' + suffix).val(excursionCost);
		$row.find('#transport_cost1' + suffix).val(transportCost);
	}
	quotationSyncDerivedCostingAmount($row.find('#basic_amount' + suffix), basicTotal);
	quotationSyncDerivedCostingAmount($row.find('#total_tour_cost' + suffix), basicTotal);
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
		window.quotationSkipGetBusiness = true;
		try {
			if (typeof quotation_cost_calculate === 'function') {
				quotation_cost_calculate('tour_cost-');
			}
		} finally {
			window.quotationSkipGetBusiness = false;
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
		window.quotationSkipGetBusiness = true;
		try {
			if (typeof quotation_cost_calculate === 'function') {
				quotation_cost_calculate('tour_cost' + suffix);
			}
		} finally {
			window.quotationSkipGetBusiness = false;
		}
	}
	if (typeof quotationOnDefaultCostingAmountsWritten === 'function') {
		quotationOnDefaultCostingAmountsWritten({ scope: 'group' });
	}
	return true;
}

function quotationPpCostingSuffix(index, total) {
	if (total <= 1) {
		return '';
	}
	return '-' + (index + 1);
}

function quotationPpFieldId(baseId, suffix) {
	return baseId + (suffix || '');
}

/**
 * Activity tariff transfer is per vehicle. Empty No.Of Vehicles used to
 * multiply the tariff by 0 on Save, so transfer never reached costing.
 */
function quotationExcursionNeedsTransfer(transferOption) {
	var opt = String(transferOption || '').trim().toLowerCase();
	return opt !== '' && opt !== 'without transfer';
}

function quotationExcursionVehicleCount(transferOption, vehicles) {
	var n = parseInt(vehicles, 10);
	if (isNaN(n) || n < 0) {
		n = 0;
	}
	if (!quotationExcursionNeedsTransfer(transferOption)) {
		return 0;
	}
	return n < 1 ? 1 : n;
}

function quotationSetExcursionRowTransferCost(row, transferCost) {
	if (!row) {
		return;
	}
	transferCost = parseFloat(transferCost) || 0;
	row.setAttribute('data-transfer-cost', transferCost);
	if (row.cells[17] && row.cells[17].childNodes[0]) {
		var el = row.cells[17].childNodes[0];
		var key = String(el.id || el.name || '').toLowerCase();
		if (key.indexOf('transfer') >= 0) {
			el.value = transferCost;
		}
	}
}

function quotationRefreshCostingAfterActivityTariff(options) {
	options = options || {};
	var force = options.force !== false;
	if (typeof quotationRefreshPpCostingFromTravelStaySelections === 'function') {
		quotationRefreshPpCostingFromTravelStaySelections({
			force: force,
			preserveIfEmpty: true,
			recalcServiceCharge: true
		});
		return;
	}
	if (typeof quotationRefreshPpActivityFromExcursion === 'function') {
		quotationRefreshPpActivityFromExcursion({ force: force, preserveIfEmpty: true });
	}
	if (typeof calculateCostingCardsUpdate === 'function') {
		calculateCostingCardsUpdate({ recalcServiceCharge: true });
	} else if (typeof calculateCostingCards === 'function') {
		calculateCostingCards(force);
	}
}

/**
 * Activity vehicle transfer cost from an excursion table row.
 * Save stores it in transfer_total cell; update stores data-transfer-cost.
 */
function quotationGetExcursionRowTransferCost(row) {
	if (!row) return 0;
	var attr = parseFloat(row.getAttribute('data-transfer-cost'));
	if (!isNaN(attr)) return attr;
	if (row.cells[17] && row.cells[17].childNodes[0]) {
		var el = row.cells[17].childNodes[0];
		var key = String(el.id || el.name || '').toLowerCase();
		if (key.indexOf('transfer') >= 0) {
			return parseFloat(el.value) || 0;
		}
	}
	return 0;
}

/**
 * Per-person activity amounts from excursion table.
 * Transfer is split across Adult + CWEB + CWNB only — never added to Infant.
 * Infant activity = ticket cost only.
 */
function quotationCalcActivityPpFromExcursionTable() {
	var adult_count = parseInt($('#total_adult12').val(), 10) || parseInt($('#total_adult').val(), 10) || 0;
	var child_with_bed = parseInt($('#children_with_bed12').val(), 10) || parseInt($('#children_with_bed').val(), 10) || 0;
	var child_without_bed = parseInt($('#children_without_bed12').val(), 10) || parseInt($('#children_without_bed').val(), 10) || 0;
	var total_infant = parseInt($('#total_infant12').val(), 10) || parseInt($('#total_infant').val(), 10) || 0;

	var exc_adult_cost = 0;
	var exc_child_cot = 0;
	var exc_childwo_cot = 0;
	var exc_infant_cost = 0;
	var exc_transfer_cost = 0;

	var table = document.getElementById('tbl_package_tour_quotation_dynamic_excursion');
	if (table && table.rows) {
		for (var e = 0; e < table.rows.length; e++) {
			var row = table.rows[e];
			var checkbox = row.cells[0] && row.cells[0].querySelector
				? row.cells[0].querySelector('input[type="checkbox"]')
				: (row.cells[0] && row.cells[0].childNodes[0]);
			if (!checkbox || !checkbox.checked) continue;

			if (row.cells[11] && row.cells[11].childNodes[0]) {
				exc_adult_cost += parseFloat(row.cells[11].childNodes[0].value) || 0;
			}
			if (row.cells[12] && row.cells[12].childNodes[0]) {
				exc_child_cot += parseFloat(row.cells[12].childNodes[0].value) || 0;
			}
			if (row.cells[13] && row.cells[13].childNodes[0]) {
				exc_childwo_cot += parseFloat(row.cells[13].childNodes[0].value) || 0;
			}
			if (row.cells[14] && row.cells[14].childNodes[0]) {
				exc_infant_cost += parseFloat(row.cells[14].childNodes[0].value) || 0;
			}
			exc_transfer_cost += quotationGetExcursionRowTransferCost(row);
		}
	}

	var ticket_adult = (adult_count > 0) ? (exc_adult_cost / adult_count) : 0;
	var ticket_cweb = (child_with_bed > 0) ? (exc_child_cot / child_with_bed) : 0;
	var ticket_cwnb = (child_without_bed > 0) ? (exc_childwo_cot / child_without_bed) : 0;
	var ticket_infant = (total_infant > 0) ? (exc_infant_cost / total_infant) : 0;

	var activity_passengers = adult_count + child_with_bed + child_without_bed;
	if (activity_passengers < 1) activity_passengers = 1;
	var activity_transfer_pp = exc_transfer_cost / activity_passengers;
	if (isNaN(activity_transfer_pp)) activity_transfer_pp = 0;

	return {
		activity_adult: ticket_adult + ((adult_count > 0) ? activity_transfer_pp : 0),
		activity_cweb: ticket_cweb + ((child_with_bed > 0) ? activity_transfer_pp : 0),
		activity_cwnb: ticket_cwnb + ((child_without_bed > 0) ? activity_transfer_pp : 0),
		// Infant: ticket only — activity transfer is NOT shared with infants
		activity_infant: ticket_infant
	};
}

/**
 * Force-write activity PP amounts onto all package costing rows (save + update fields).
 */
function quotationApplyActivityPpToFields(activityPp, options) {
	options = options || {};
	var force = options.force !== false;
	activityPp = activityPp || {};
	var activityAdult = parseFloat(activityPp.activity_adult) || 0;
	var activityCweb = parseFloat(activityPp.activity_cweb) || 0;
	var activityCwnb = parseFloat(activityPp.activity_cwnb) || 0;
	var activityInfant = parseFloat(activityPp.activity_infant) || 0;

	function writeField($row, suffix, baseId, value) {
		var $el = $row && $row.length
			? $row.find('#' + quotationPpFieldId(baseId, suffix))
			: $('#' + quotationPpFieldId(baseId, suffix));
		if (!$el.length) {
			$el = $('#' + quotationPpFieldId(baseId, suffix));
		}
		if (!$el.length) return;
		if (typeof quotationIsManualAmountField === 'function' && quotationIsManualAmountField($el)) {
			return;
		}
		if (force) {
			if (typeof quotationWritePpDefaultCurrencyAmount === 'function') {
				quotationWritePpDefaultCurrencyAmount($el, value);
			} else {
				$el.val(Number(value).toFixed(2));
			}
			return;
		}
		var cur = $el.val();
		if (cur === '' || cur === null || typeof cur === 'undefined') {
			if (typeof quotationWritePpDefaultCurrencyAmount === 'function') {
				quotationWritePpDefaultCurrencyAmount($el, value);
			} else {
				$el.val(Number(value).toFixed(2));
			}
		}
	}

	var $rows = $('#quotation_pp_costing_container .quotation-pp-costing-row');
	if ($rows.length) {
		$rows.each(function () {
			var $row = $(this);
			var suffix = $row.attr('data-pp-suffix') || '';
			writeField($row, suffix, 'adult_activity_pp', activityAdult);
			writeField($row, suffix, 'cweb_activity_pp', activityCweb);
			writeField($row, suffix, 'cwnb_activity_pp', activityCwnb);
			writeField($row, suffix, 'infant_activity_pp', activityInfant);
			writeField($row, suffix, 'adult_activity_pp_update', activityAdult);
			writeField($row, suffix, 'cweb_activity_pp_update', activityCweb);
			writeField($row, suffix, 'cwnb_activity_pp_update', activityCwnb);
			writeField($row, suffix, 'infant_activity_pp_update', activityInfant);
		});
	} else {
		writeField(null, '', 'adult_activity_pp', activityAdult);
		writeField(null, '', 'cweb_activity_pp', activityCweb);
		writeField(null, '', 'cwnb_activity_pp', activityCwnb);
		writeField(null, '', 'infant_activity_pp', activityInfant);
		writeField(null, '', 'adult_activity_pp_update', activityAdult);
		writeField(null, '', 'cweb_activity_pp_update', activityCweb);
		writeField(null, '', 'cwnb_activity_pp_update', activityCwnb);
		writeField(null, '', 'infant_activity_pp_update', activityInfant);
	}
}

/**
 * Sum existing PP hotel/transfer/activity amounts (display value, default, or DB saved).
 * Used so update load does not wipe manually stored amounts when tariff/travel-stay is empty.
 */
function quotationGetPpComponentExistingTotal(component) {
	component = String(component || '');
	if (!component) return 0;
	var total = 0;
	var selector =
		'[id^="adult_' + component + '_pp"], [id^="cweb_' + component + '_pp"], ' +
		'[id^="cwnb_' + component + '_pp"], [id^="infant_' + component + '_pp"]';
	var $scope = $('#quotation_pp_costing_container');
	if (!$scope.length) {
		$scope = $(document);
	}
	$scope.find(selector).each(function () {
		var $el = $(this);
		var cur = parseFloat(String($el.val() || '').replace(/,/g, '')) || 0;
		var def = parseFloat(String($el.attr('data-default-amount') || '').replace(/,/g, '')) || 0;
		var saved = parseFloat(String($el.attr('data-saved-amount') || '').replace(/,/g, '')) || 0;
		total += Math.max(cur, def, saved);
	});
	return total;
}

/**
 * Restore PP money fields from data-saved-amount when display/default was wiped to 0.
 * Safe to call repeatedly on update quotation load / after tariff refresh.
 */
function quotationRestoreUpdatePpSavedAmounts() {
	var restored = false;
	var $scope = $('#quotation_pp_costing_container');
	if (!$scope.length) return false;
	$scope.find('input[data-saved-amount]').each(function () {
		var $el = $(this);
		var saved = parseFloat(String($el.attr('data-saved-amount')).replace(/,/g, ''));
		if (isNaN(saved) || saved === 0) {
			return;
		}
		var cur = parseFloat(String($el.val()).replace(/,/g, '')) || 0;
		var def = parseFloat(String($el.attr('data-default-amount')).replace(/,/g, '')) || 0;
		// Only put DB amount back when display was wiped to 0 (do not override live tariff)
		if (cur === 0) {
			$el.attr('data-default-amount', saved);
			$el.val(Number(saved).toFixed(2));
			restored = true;
		} else if (def === 0) {
			$el.attr('data-default-amount', cur);
		}
	});
	return restored;
}

/**
 * Recalculate activity PP from excursion table and push into costing cards.
 * When tariff/travel-stay does not supply activity amounts, keep DB/manual PP values.
 */
function quotationRefreshPpActivityFromExcursion(options) {
	options = options || {};
	if (typeof quotationCalcActivityPpFromExcursionTable !== 'function') return null;
	var activityPp = quotationCalcActivityPpFromExcursionTable();
	var force = options.force !== false;
	var preserveIfEmpty = options.preserveIfEmpty !== false;

	var hasCheckedExcursion = false;
	var table = document.getElementById('tbl_package_tour_quotation_dynamic_excursion');
	if (table && table.rows) {
		for (var e = 0; e < table.rows.length; e++) {
			var row = table.rows[e];
			var checkbox = row.cells[0] && row.cells[0].querySelector
				? row.cells[0].querySelector('input[type="checkbox"]')
				: (row.cells[0] && row.cells[0].childNodes[0]);
			if (checkbox && checkbox.checked) {
				hasCheckedExcursion = true;
				break;
			}
		}
	}

	var calcTotal =
		(parseFloat(activityPp.activity_adult) || 0) +
		(parseFloat(activityPp.activity_cweb) || 0) +
		(parseFloat(activityPp.activity_cwnb) || 0) +
		(parseFloat(activityPp.activity_infant) || 0);

	var existingTotal = typeof quotationGetPpComponentExistingTotal === 'function'
		? quotationGetPpComponentExistingTotal('activity')
		: 0;

	// No checked activity / empty calc → keep stored DB or manual amounts
	if ((!hasCheckedExcursion || calcTotal === 0) && existingTotal > 0
		&& (preserveIfEmpty || !hasCheckedExcursion)) {
		if (typeof quotationRestoreUpdatePpSavedAmounts === 'function') {
			quotationRestoreUpdatePpSavedAmounts();
		}
		return activityPp;
	}

	// No checked activity rows and nothing saved → clear PP activity
	if (!hasCheckedExcursion) {
		if (existingTotal <= 0 && typeof quotationApplyActivityPpToFields === 'function') {
			quotationApplyActivityPpToFields({
				activity_adult: 0,
				activity_cweb: 0,
				activity_cwnb: 0,
				activity_infant: 0
			}, { force: true });
		}
		return activityPp;
	}

	if (typeof quotationApplyActivityPpToFields === 'function') {
		quotationApplyActivityPpToFields(activityPp, { force: force });
	}
	return activityPp;
}

/**
 * Read transport cost input from a travel-stay transport row (save or update table).
 */
function quotationGetTransportRowCostEl(row) {
	if (!row) return null;
	var costEl = row.querySelector
		? row.querySelector('input[id^="transport_cost"]')
		: null;
	if (costEl) return costEl;
	if (row.cells && row.cells[9]) {
		costEl = row.cells[9].querySelector
			? row.cells[9].querySelector('input')
			: row.cells[9].childNodes[0];
		return costEl || null;
	}
	return null;
}

function quotationHasCheckedTransportRows() {
	var tableIds = [
		'tbl_package_tour_quotation_dynamic_transport',
		'tbl_package_tour_quotation_dynamic_transport_u'
	];
	for (var t = 0; t < tableIds.length; t++) {
		var table = document.getElementById(tableIds[t]);
		if (!table || !table.rows) continue;
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			var checkbox = row.cells[0] && row.cells[0].querySelector
				? row.cells[0].querySelector('input[type="checkbox"]')
				: (row.cells[0] && row.cells[0].childNodes[0]);
			if (checkbox && checkbox.checked) return true;
		}
	}
	return false;
}

/**
 * Sum checked transport row costs (save + update tables) and split PP
 * across Adult + CWEB + CWNB (infants excluded).
 */
function quotationCalcTransferPpFromTransportTables() {
	var transportTotal = 0;
	var tableIds = [
		'tbl_package_tour_quotation_dynamic_transport',
		'tbl_package_tour_quotation_dynamic_transport_u'
	];
	for (var t = 0; t < tableIds.length; t++) {
		var table = document.getElementById(tableIds[t]);
		if (!table || !table.rows) continue;
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			var checkbox = row.cells[0] && row.cells[0].querySelector
				? row.cells[0].querySelector('input[type="checkbox"]')
				: (row.cells[0] && row.cells[0].childNodes[0]);
			if (!checkbox || !checkbox.checked) continue;
			var costEl = quotationGetTransportRowCostEl(row);
			transportTotal += costEl ? (parseFloat(costEl.value) || 0) : 0;
		}
	}

	var adult_count = parseInt($('#total_adult12').val(), 10) || parseInt($('#total_adult').val(), 10) || 0;
	var child_with_bed = parseInt($('#children_with_bed12').val(), 10) || parseInt($('#children_with_bed').val(), 10) || 0;
	var child_without_bed = parseInt($('#children_without_bed12').val(), 10) || parseInt($('#children_without_bed').val(), 10) || 0;
	var passengers = adult_count + child_with_bed + child_without_bed;
	if (passengers < 1) passengers = 1;
	var transportPp = transportTotal / passengers;
	if (isNaN(transportPp)) transportPp = 0;

	return {
		transport_total: transportTotal,
		transport_pp: transportPp,
		transfer_adult: adult_count > 0 ? transportPp : 0,
		transfer_cweb: child_with_bed > 0 ? transportPp : 0,
		transfer_cwnb: child_without_bed > 0 ? transportPp : 0,
		transfer_infant: 0
	};
}

function quotationApplyTransferPpToFields(transferData, options) {
	options = options || {};
	var force = options.force !== false;
	transferData = transferData || {};
	var transferAdult = parseFloat(transferData.transfer_adult) || 0;
	var transferCweb = parseFloat(transferData.transfer_cweb) || 0;
	var transferCwnb = parseFloat(transferData.transfer_cwnb) || 0;
	var transferInfant = parseFloat(transferData.transfer_infant) || 0;

	function writeField($row, suffix, baseId, value) {
		var $el = $row && $row.length
			? $row.find('#' + quotationPpFieldId(baseId, suffix))
			: $('#' + quotationPpFieldId(baseId, suffix));
		if (!$el.length) {
			$el = $('#' + quotationPpFieldId(baseId, suffix));
		}
		if (!$el.length) return;
		if (typeof quotationIsManualAmountField === 'function' && quotationIsManualAmountField($el)) {
			return;
		}
		if (force) {
			if (typeof quotationWritePpDefaultCurrencyAmount === 'function') {
				quotationWritePpDefaultCurrencyAmount($el, value);
			} else {
				$el.val(Number(value).toFixed(2));
			}
			return;
		}
		var raw = $el.val();
		var cur = parseFloat(String(raw == null ? '' : raw).replace(/,/g, ''));
		if (raw === '' || raw === null || typeof raw === 'undefined' || isNaN(cur) || cur === 0) {
			if (typeof quotationWritePpDefaultCurrencyAmount === 'function') {
				quotationWritePpDefaultCurrencyAmount($el, value);
			} else {
				$el.val(Number(value).toFixed(2));
			}
		}
	}

	var $rows = $('#quotation_pp_costing_container .quotation-pp-costing-row');
	if ($rows.length) {
		$rows.each(function () {
			var $row = $(this);
			var suffix = $row.attr('data-pp-suffix') || '';
			writeField($row, suffix, 'adult_transfer_pp', transferAdult);
			writeField($row, suffix, 'cweb_transfer_pp', transferCweb);
			writeField($row, suffix, 'cwnb_transfer_pp', transferCwnb);
			writeField($row, suffix, 'infant_transfer_pp', transferInfant);
			writeField($row, suffix, 'adult_transfer_pp_update', transferAdult);
			writeField($row, suffix, 'cweb_transfer_pp_update', transferCweb);
			writeField($row, suffix, 'cwnb_transfer_pp_update', transferCwnb);
			writeField($row, suffix, 'infant_transfer_pp_update', transferInfant);
		});
	} else {
		writeField(null, '', 'adult_transfer_pp', transferAdult);
		writeField(null, '', 'cweb_transfer_pp', transferCweb);
		writeField(null, '', 'cwnb_transfer_pp', transferCwnb);
		writeField(null, '', 'infant_transfer_pp', transferInfant);
		writeField(null, '', 'adult_transfer_pp_update', transferAdult);
		writeField(null, '', 'cweb_transfer_pp_update', transferCweb);
		writeField(null, '', 'cwnb_transfer_pp_update', transferCwnb);
		writeField(null, '', 'infant_transfer_pp_update', transferInfant);
	}
}

/**
 * Recalculate transfer PP from transport tables.
 * When tariff/travel-stay does not supply transfer amounts, keep DB/manual PP values.
 */
function quotationRefreshPpTransferFromTransport(options) {
	options = options || {};
	var force = options.force !== false;
	var preserveIfEmpty = options.preserveIfEmpty !== false;
	var transfer = quotationCalcTransferPpFromTransportTables();

	try {
		$('#travel_pp_costing').val(JSON.stringify([{
			total_cost: transfer.transport_total,
			checked: transfer.transport_total > 0 || quotationHasCheckedTransportRows()
		}]));
	} catch (e) {}

	var hasCheckedTransport = quotationHasCheckedTransportRows();

	var calcTotal =
		(parseFloat(transfer.transfer_adult) || 0) +
		(parseFloat(transfer.transfer_cweb) || 0) +
		(parseFloat(transfer.transfer_cwnb) || 0) +
		(parseFloat(transfer.transfer_infant) || 0);

	var existingTotal = typeof quotationGetPpComponentExistingTotal === 'function'
		? quotationGetPpComponentExistingTotal('transfer')
		: 0;

	// No checked transport / empty calc → keep stored DB or manual amounts
	if ((!hasCheckedTransport || calcTotal === 0) && existingTotal > 0
		&& (preserveIfEmpty || !hasCheckedTransport)) {
		if (typeof quotationRestoreUpdatePpSavedAmounts === 'function') {
			quotationRestoreUpdatePpSavedAmounts();
		}
		return transfer;
	}

	// No checked transport and nothing saved → clear PP transfer
	if (!hasCheckedTransport) {
		if (existingTotal <= 0) {
			quotationApplyTransferPpToFields({
				transfer_adult: 0,
				transfer_cweb: 0,
				transfer_cwnb: 0,
				transfer_infant: 0
			}, { force: true });
		}
		return transfer;
	}

	quotationApplyTransferPpToFields(transfer, { force: force });
	return transfer;
}

function quotationHasCheckedPlaneRows() {
	var table = document.getElementById('tbl_package_tour_quotation_dynamic_plane');
	if (!table || !table.rows) return false;
	for (var i = 0; i < table.rows.length; i++) {
		var row = table.rows[i];
		var checkbox = row.cells[0] && row.cells[0].querySelector
			? row.cells[0].querySelector('input[type="checkbox"]')
			: (row.cells[0] && row.cells[0].childNodes[0]);
		if (checkbox && checkbox.checked) return true;
	}
	return false;
}

/**
 * When all flights are deselected, clear PP flight amounts (and group flight totals).
 * When at least one flight remains selected, leave existing flight amounts as-is.
 */
function quotationApplyFlightPpFromPlaneSelection(options) {
	options = options || {};
	if (quotationHasCheckedPlaneRows()) {
		return false;
	}
	var force = options.force !== false;
	function writeField($row, suffix, baseId, value) {
		var $el = $row && $row.length
			? $row.find('#' + quotationPpFieldId(baseId, suffix))
			: $('#' + quotationPpFieldId(baseId, suffix));
		if (!$el.length) {
			$el = $('#' + quotationPpFieldId(baseId, suffix));
		}
		if (!$el.length) return;
		if (force || $el.val() === '' || $el.val() === null) {
			$el.val(Number(value).toFixed(2));
		}
	}
	var $rows = $('#quotation_pp_costing_container .quotation-pp-costing-row');
	if ($rows.length) {
		$rows.each(function () {
			var $row = $(this);
			var suffix = $row.attr('data-pp-suffix') || '';
			['adult', 'cweb', 'cwnb', 'infant'].forEach(function (ptype) {
				writeField($row, suffix, ptype + '_flight_pp', 0);
				writeField($row, suffix, ptype + '_flight_pp_update', 0);
			});
		});
	} else {
		['adult', 'cweb', 'cwnb', 'infant'].forEach(function (ptype) {
			writeField(null, '', ptype + '_flight_pp', 0);
			writeField(null, '', ptype + '_flight_pp_update', 0);
		});
	}
	$('#flight_cost1, #flight_acost1, #flight_ccost1, #flight_icost1, #flight_acost, #flight_ccost, #flight_icost').each(function () {
		$(this).val('0');
	});
	return true;
}

/**
 * Push checked transport / activity totals into group costing land fields.
 * Totals from travel-stay are in company default currency.
 */
function quotationRefreshGroupCostingLandFromSelections(options) {
	options = options || {};
	var forceGroupLand = options.forceGroupLand === true;
	var preserveExisting = options.preserveExisting === true
		|| (typeof quotationIsPackageQuotationUpdate === 'function' && quotationIsPackageQuotationUpdate() && !forceGroupLand);
	var transfer = quotationCalcTransferPpFromTransportTables();
	var activityTotal = 0;
	var excTable = document.getElementById('tbl_package_tour_quotation_dynamic_excursion');
	if (excTable && excTable.rows) {
		for (var e = 0; e < excTable.rows.length; e++) {
			var row = excTable.rows[e];
			var checkbox = row.cells[0] && row.cells[0].querySelector
				? row.cells[0].querySelector('input[type="checkbox"]')
				: (row.cells[0] && row.cells[0].childNodes[0]);
			if (!checkbox || !checkbox.checked) continue;
			if (row.cells[10] && row.cells[10].childNodes[0]) {
				activityTotal += parseFloat(row.cells[10].childNodes[0].value) || 0;
			}
		}
	}

	function writeDefaultCurrencyField($el, tariffAmountInCompanyCurrency, overwriteManual) {
		if (!$el || !$el.length) {
			return;
		}
		// Update Group Costing Transfer/Activity: never replace saved or typed amounts
		// unless forceGroupLand is explicitly requested.
		if (!forceGroupLand && !overwriteManual && typeof quotationProtectUpdateGroupLandField === 'function'
			&& quotationProtectUpdateGroupLandField($el)) {
			return;
		}
		if (!forceGroupLand && !overwriteManual && typeof quotationIsManualAmountField === 'function' && quotationIsManualAmountField($el)) {
			return;
		}
		if (preserveExisting && !overwriteManual) {
			var cur = parseFloat(String($el.val() || '').replace(/,/g, '')) || 0;
			if (cur > 0) {
				return;
			}
		}
		if (forceGroupLand) {
			$el.removeAttr('data-amount-source');
		}
		if (typeof quotationWritePpDefaultCurrencyAmount === 'function') {
			quotationWritePpDefaultCurrencyAmount($el, tariffAmountInCompanyCurrency);
			return;
		}
		tariffAmountInCompanyCurrency = parseFloat(tariffAmountInCompanyCurrency) || 0;
		var factor = window.quotationCurrencyDisplayFactor || 1;
		var amount = Math.round(tariffAmountInCompanyCurrency * factor * 100) / 100;
		$el.attr('data-amount-source', 'tariff');
		$el.attr('data-tariff-company-amount', tariffAmountInCompanyCurrency);
		$el.attr('data-default-amount', amount);
		$el.val(Number(amount).toFixed(2));
	}

	var $groupRoot = $('#group_costing_tab, #tbl_package_tour_quotation_dynamic_costing');
	var hotelTotals = {};
	try {
		var hotelPp = JSON.parse($('#hotel_pp_costing').val() || '[]');
		if (Array.isArray(hotelPp)) {
			for (var h = 0; h < hotelPp.length; h++) {
				if (!hotelPp[h] || hotelPp[h].checked === false) {
					continue;
				}
				var pkgType = String(hotelPp[h].package_type || '');
				if (!pkgType) {
					continue;
				}
				hotelTotals[pkgType] = (hotelTotals[pkgType] || 0) + (parseFloat(hotelPp[h].hotel_cost) || 0);
			}
		}
	} catch (eHotel) {}
	$groupRoot.find('[id^="tour_cost"]').each(function () {
		if (/_u$/.test(this.id)) {
			return;
		}
		var suffix = (typeof quotationCostingFieldSuffix === 'function')
			? quotationCostingFieldSuffix(this.id)
			: this.id.replace('tour_cost', '');
		var pkg = $('#package_type' + suffix).val() || '';
		if (pkg && hotelTotals[pkg] != null) {
			writeDefaultCurrencyField($(this), hotelTotals[pkg], !preserveExisting);
		}
		if (typeof quotation_cost_calculate1 === 'function') {
			quotation_cost_calculate1(this.id);
		} else if (typeof quotation_cost_calculate === 'function') {
			quotation_cost_calculate(this.id);
		}
	});
	$groupRoot.find('[id^="transport_cost"]').each(function () {
		// Skip travel-stay transport row cost fields (…_u)
		if (/_u$/.test(this.id)) return;
		if (!forceGroupLand) {
			if (typeof quotationProtectUpdateGroupLandField === 'function' && quotationProtectUpdateGroupLandField($(this))) {
				if (typeof quotation_cost_calculate1 === 'function') {
					quotation_cost_calculate1(this.id);
				} else if (typeof quotation_cost_calculate === 'function') {
					quotation_cost_calculate(this.id);
				}
				return;
			}
		}
		writeDefaultCurrencyField($(this), transfer.transport_total || 0);
		if (typeof quotation_cost_calculate1 === 'function') {
			quotation_cost_calculate1(this.id);
		} else if (typeof quotation_cost_calculate === 'function') {
			quotation_cost_calculate(this.id);
		}
	});
	$groupRoot.find('[id^="excursion_cost"]').each(function () {
		if (/_u$/.test(this.id)) return;
		if (!forceGroupLand) {
			if (typeof quotationProtectUpdateGroupLandField === 'function' && quotationProtectUpdateGroupLandField($(this))) {
				if (typeof quotation_cost_calculate1 === 'function') {
					quotation_cost_calculate1(this.id);
				} else if (typeof quotation_cost_calculate === 'function') {
					quotation_cost_calculate(this.id);
				}
				return;
			}
		}
		writeDefaultCurrencyField($(this), activityTotal || 0);
		if (typeof quotation_cost_calculate1 === 'function') {
			quotation_cost_calculate1(this.id);
		} else if (typeof quotation_cost_calculate === 'function') {
			quotation_cost_calculate(this.id);
		}
	});
}

/**
 * Full refresh of PP (and group land) costs from currently checked
 * hotel / transport / flight / activity rows. Call after any deselection.
 */
function quotationRefreshPpCostingFromTravelStaySelections(options) {
	options = options || {};
	var force = options.force !== false;
	var preserveIfEmpty = options.preserveIfEmpty !== false;

	if (typeof quotationSyncCostingUiToHotelSelection === 'function') {
		quotationSyncCostingUiToHotelSelection();
	}

	if (typeof quotationRefreshPpTransferFromTransport === 'function') {
		quotationRefreshPpTransferFromTransport({ force: force, preserveIfEmpty: preserveIfEmpty });
	} else {
		var transfer = quotationCalcTransferPpFromTransportTables();
		try {
			$('#travel_pp_costing').val(JSON.stringify([{
				total_cost: transfer.transport_total,
				checked: transfer.transport_total > 0
			}]));
		} catch (e) {}
		quotationApplyTransferPpToFields(transfer, { force: force });
	}

	if (typeof quotationRefreshPpActivityFromExcursion === 'function') {
		quotationRefreshPpActivityFromExcursion({ force: force, preserveIfEmpty: preserveIfEmpty });
	}

	quotationApplyFlightPpFromPlaneSelection({ force: force });
	quotationRefreshGroupCostingLandFromSelections({
		preserveExisting: !force,
		forceGroupLand: options.forceGroupLand === true
	});

	if (typeof calculateCostingCardsUpdate === 'function') {
		calculateCostingCardsUpdate({
			recalcServiceCharge: options.recalcServiceCharge !== false,
			// force refresh writes tariff/travel amounts in company default currency
			fromDefaultCurrencyWrite: force
		});
	} else if (typeof calculateCostingCards === 'function') {
		if (force) {
			calculateCostingCards(true);
		} else {
			calculateCostingCards(false, {
				autoFill: false,
				recalcServiceCharge: options.recalcServiceCharge !== false
			});
		}
	} else if (force && typeof quotationOnDefaultCostingAmountsWritten === 'function') {
		quotationOnDefaultCostingAmountsWritten({ scope: 'pp' });
	}
}

/**
 * Parse per-person costing input ids, e.g.
 * adult_activity_pp, cweb_hotel_pp-2, adult_activity_pp_update-1
 */
function quotationParsePpCostingFieldId(id) {
	id = String(id || '');
	var match = id.match(/^(adult|cweb|cwnb|infant)_(hotel|transfer|activity|land_cost|service_charge)_pp(?:_update)?(-\d+)?$/);
	if (!match) {
		match = id.match(/^(adult|cweb|cwnb|infant)_.+_pp(?:_update)?(-\d+)?$/);
		if (!match) {
			return null;
		}
		return { paxType: match[1], suffix: match[2] || '' };
	}
	return { paxType: match[1], component: match[2], suffix: match[3] || '' };
}

/**
 * Tax dropdown value format (same as group costing):
 *   CGST:(6%):(ledger)+SGST:(6%):(ledger)
 * Computes total tax amount by applying each rate to taxBase (like get_auto_values).
 */
function quotationCalcMultiTaxAmount(taxBase, taxValueOrSelect) {
	var taxBaseNum = parseFloat(taxBase);
	if (isNaN(taxBaseNum)) taxBaseNum = 0;

	var taxValue = '';
	if (taxValueOrSelect && taxValueOrSelect.jquery) {
		taxValue = taxValueOrSelect.val() || '';
		if (!taxValue) {
			taxValue = taxValueOrSelect.find('option:selected').text() || '';
		}
	} else {
		taxValue = String(taxValueOrSelect || '');
	}
	taxValue = String(taxValue || '').trim();
	if (!taxValue || /^\*?Select Tax/i.test(taxValue) || taxBaseNum === 0) {
		return 0;
	}

	var total = 0;
	var parts = taxValue.split('+');
	for (var i = 0; i < parts.length; i++) {
		var part = String(parts[i] || '').trim();
		if (!part) continue;
		var pct = 0;
		var bits = part.split(':');
		if (bits.length > 1 && bits[1]) {
			var m = String(bits[1]).match(/(\d+(\.\d+)?)/);
			if (m) pct = parseFloat(m[1]) || 0;
		}
		if (!pct) {
			var m2 = part.match(/(\d+(\.\d+)?)\s*%/);
			if (m2) pct = parseFloat(m2[1]) || 0;
		}
		if (pct) {
			total += parseFloat(((taxBaseNum * pct) / 100).toFixed(2));
		}
	}
	if (isNaN(total) || !isFinite(total)) total = 0;
	return parseFloat(total.toFixed(2));
}

function quotationStripPpCostingRowSelect2($row) {
	$row.find('select').each(function () {
		var $select = $(this);
		if ($select.data('select2')) {
			try {
				$select.select2('destroy');
			} catch (e) {}
		}
	});
	$row.find('.select2-container').remove();
}

function quotationInitPpCostingSelect2($scope) {
	var $root = $scope && $scope.length ? $scope : $('#quotation_pp_costing_container');
	if (!$root.length) {
		$root = $(document);
	}
	var $selects = $root.find('select[id^="currency_code_pp"]')
		.add('#currency_code_pp, #currency_code, #currency_code1');
	$selects.each(function () {
		var $select = $(this);
		if (!$select.length || $select.data('select2')) {
			return;
		}
		$select.select2({ width: '100%' });
	});
}

function quotationPpCostingRenumberRow($row, newSuffix) {
	var idMap = {};
	$row.find('[id]').each(function () {
		var oldId = this.id;
		if (!oldId) {
			return;
		}
		var baseId = oldId.replace(/-\d+$/, '');
		var newId = baseId + (newSuffix || '');
		idMap[oldId] = newId;
		this.id = newId;
		if (this.name) {
			var baseName = String(this.name).replace(/-\d+$/, '');
			this.name = baseName + (newSuffix || '');
		}
	});
	$row.find('label[for]').each(function () {
		var target = $(this).attr('for');
		if (target && idMap[target]) {
			$(this).attr('for', idMap[target]);
		}
	});
	$row.attr('data-pp-suffix', newSuffix || '');
	// Only first block keeps the shared adult_child table id
	$row.find('table[id^="tbl_package_tour_quotation_adult_child"]').attr(
		'id',
		newSuffix ? ('tbl_package_tour_quotation_adult_child' + newSuffix) : 'tbl_package_tour_quotation_adult_child'
	);
}

function quotationPpCostingSetRowValues($row, suffix, data, options) {
	options = options || {};
	data = data || {};
	var force = !!options.force;
	var packageType = data.type || data.package_type || 'NA';
	var packageId = data.package_id || data.id || '';
	var adultHotel = parseFloat(data.adult_cost) || 0;
	var cwebHotel = parseFloat(data.cwb_cost != null ? data.cwb_cost : data.child_with_bed) || 0;
	var cwnbHotel = parseFloat(data.cwob_cost != null ? data.cwob_cost : data.child_without_bed) || 0;
	var infantHotel = parseFloat(data.infant_cost) || 0;
	var transportPp = parseFloat(options.transport_pp) || 0;
	var transferAdult = data.transfer_adult != null ? parseFloat(data.transfer_adult) || 0
		: (options.transfer_adult != null ? parseFloat(options.transfer_adult) || 0 : transportPp);
	var transferCweb = data.transfer_cweb != null ? parseFloat(data.transfer_cweb) || 0
		: (options.transfer_cweb != null ? parseFloat(options.transfer_cweb) || 0 : transportPp);
	var transferCwnb = data.transfer_cwnb != null ? parseFloat(data.transfer_cwnb) || 0
		: (options.transfer_cwnb != null ? parseFloat(options.transfer_cwnb) || 0 : transportPp);
	// Infants excluded from transfer passenger split (adult + cweb + cwnb)
	var transferInfant = data.transfer_infant != null ? parseFloat(data.transfer_infant) || 0
		: (options.transfer_infant != null ? parseFloat(options.transfer_infant) || 0 : 0);
	var activityAdult = data.activity_adult != null ? parseFloat(data.activity_adult) || 0 : (parseFloat(options.activity_adult) || 0);
	var activityCweb = data.activity_cweb != null ? parseFloat(data.activity_cweb) || 0 : (parseFloat(options.activity_cweb) || 0);
	var activityCwnb = data.activity_cwnb != null ? parseFloat(data.activity_cwnb) || 0 : (parseFloat(options.activity_cwnb) || 0);
	var activityInfant = data.activity_infant != null ? parseFloat(data.activity_infant) || 0 : (parseFloat(options.activity_infant) || 0);

	// One package-type row only per block
	$row.find('table[id*="tbl_package_tour_quotation_adult_child"] tr').slice(1).remove();

	var $pkg = $row.find('#' + quotationPpFieldId('ppackage_type1', suffix));
	if ($pkg.length) {
		$pkg.val(packageType);
	}
	var $pkgId = $row.find('#' + quotationPpFieldId('pacakge_id2', suffix));
	if ($pkgId.length) {
		$pkgId.val(packageId);
	}
	$row.find('#' + quotationPpFieldId('adult_cost', suffix)).val(adultHotel);
	$row.find('#' + quotationPpFieldId('child_with', suffix)).val(cwebHotel);
	$row.find('#' + quotationPpFieldId('child_without', suffix)).val(cwnbHotel);
	$row.find('#' + quotationPpFieldId('infant_cost', suffix)).val(infantHotel);

	function setVal(baseId, value) {
		var $el = $row.find('#' + quotationPpFieldId(baseId, suffix));
		if (!$el.length) {
			return;
		}
		if (force) {
			$el.val(value);
			return;
		}
		var cur = $el.val();
		if (cur === '' || cur === null || parseFloat(cur) === 0) {
			$el.val(value);
		}
	}

	// Hotel from tariff by pax type (adult / CWEB / CWNB / infant) — package-wise
	// Save: adult_hotel_pp ; Update: adult_hotel_pp_update
	setVal('adult_hotel_pp', adultHotel);
	setVal('cweb_hotel_pp', cwebHotel);
	setVal('cwnb_hotel_pp', cwnbHotel);
	setVal('infant_hotel_pp', infantHotel);
	setVal('adult_hotel_pp_update', adultHotel);
	setVal('cweb_hotel_pp_update', cwebHotel);
	setVal('cwnb_hotel_pp_update', cwnbHotel);
	setVal('infant_hotel_pp_update', infantHotel);

	setVal('adult_transfer_pp', Number(transferAdult).toFixed(2));
	setVal('cweb_transfer_pp', Number(transferCweb).toFixed(2));
	setVal('cwnb_transfer_pp', Number(transferCwnb).toFixed(2));
	setVal('infant_transfer_pp', Number(transferInfant).toFixed(2));
	setVal('adult_transfer_pp_update', Number(transferAdult).toFixed(2));
	setVal('cweb_transfer_pp_update', Number(transferCweb).toFixed(2));
	setVal('cwnb_transfer_pp_update', Number(transferCwnb).toFixed(2));
	setVal('infant_transfer_pp_update', Number(transferInfant).toFixed(2));

	setVal('adult_activity_pp', Number(activityAdult).toFixed(2));
	setVal('cweb_activity_pp', Number(activityCweb).toFixed(2));
	setVal('cwnb_activity_pp', Number(activityCwnb).toFixed(2));
	setVal('infant_activity_pp', Number(activityInfant).toFixed(2));
	setVal('adult_activity_pp_update', Number(activityAdult).toFixed(2));
	setVal('cweb_activity_pp_update', Number(activityCweb).toFixed(2));
	setVal('cwnb_activity_pp_update', Number(activityCwnb).toFixed(2));
	setVal('infant_activity_pp_update', Number(activityInfant).toFixed(2));
}

/**
 * After ROE conversion, adult hotel PP must still use the normal formula:
 *   adult_hotel_pp = adult_share_total / adult_count
 * adult_share_total = converted (room × rooms + extra beds), same as INR tariff case.
 */
function quotationHotelAdultPpFromShare(entry, adult_count) {
	adult_count = parseFloat(adult_count) || 0;
	if (adult_count <= 0) {
		return 0;
	}
	var share = parseFloat(entry && entry.adult_share_total);
	if (!isNaN(share) && share > 0) {
		return share / adult_count;
	}
	// Reconstruct share from group hotel cost when child unit tariffs are known (legacy pp_arr)
	var hotelCost = parseFloat(entry && entry.hotel_cost) || 0;
	if (hotelCost > 0) {
		var cwbCount = parseInt($('#children_with_bed12').val(), 10) || parseInt($('#children_with_bed').val(), 10) || 0;
		var cwobCount = parseInt($('#children_without_bed12').val(), 10) || parseInt($('#children_without_bed').val(), 10) || 0;
		var cwbUnit = parseFloat(entry.child_with_bed) || 0;
		var cwobUnit = parseFloat(entry.child_without_bed) || 0;
		var reconstructed = hotelCost - (cwbCount * cwbUnit) - (cwobCount * cwobUnit);
		if (reconstructed > 0) {
			return reconstructed / adult_count;
		}
	}
	// Fallback: stored adult_cost from PHP (same formula when adult_count matched at load)
	return parseFloat(entry && entry.adult_cost) || 0;
}

function quotationRefreshHotelCostingAfterPaxChange() {
	// Prefer full hotel reload so child tariff inclusion + adult_share stay in sync
	if (typeof get_hotel_cost === 'function') {
		get_hotel_cost();
		return;
	}
	// If hotel AJAX is unavailable, re-split existing converted share into PP fields
	if (typeof quotationBuildHotelPerPersonArrFromPpCosting === 'function'
		&& typeof quotationApplyHotelTariffToPpFields === 'function') {
		quotationApplyHotelTariffToPpFields(quotationBuildHotelPerPersonArrFromPpCosting(), { force: true });
	}
	if (typeof calculateCostingCards === 'function') {
		calculateCostingCards(true);
	}
	if (typeof calculateCostingCardsUpdate === 'function') {
		calculateCostingCardsUpdate({ recalcServiceCharge: true });
	}
}

function quotationBuildHotelPerPersonArrFromPpCosting() {
	var hotel_per_person_arr = [];
	var per_person_costing = [];
	try {
		var hotel_pp_val = $('#hotel_pp_costing').val();
		if (hotel_pp_val) {
			per_person_costing = JSON.parse(hotel_pp_val);
		}
	} catch (err) {
		per_person_costing = [];
	}
	if (!Array.isArray(per_person_costing) || !per_person_costing.length) {
		return hotel_per_person_arr;
	}

	var adult_count = parseInt($('#total_adult12').val(), 10) || parseInt($('#total_adult').val(), 10) || 0;
	var child_with_bed = parseInt($('#children_with_bed12').val(), 10) || parseInt($('#children_with_bed').val(), 10) || 0;
	var child_without_bed = parseInt($('#children_without_bed12').val(), 10) || parseInt($('#children_without_bed').val(), 10) || 0;
	// Only package types with at least one checked hotel row.
	// Unchecked package types must not appear in per-person costing (or get transfer/tariff).
	var unique_package_type_arr = [];
	for (var i = 0; i < per_person_costing.length; i++) {
		if (per_person_costing[i]['checked'] !== true) {
			continue;
		}
		var ptype = per_person_costing[i]['package_type'] || '';
		if (ptype && unique_package_type_arr.indexOf(ptype) === -1) {
			unique_package_type_arr.push(ptype);
		}
	}
	if (!unique_package_type_arr.length) {
		return hotel_per_person_arr;
	}

	for (var u = 0; u < unique_package_type_arr.length; u++) {
		var adult_cost_total1 = 0;
		var cwb_cost_total1 = 0;
		var cwob_cost_total1 = 0;
		var infant_cost_total1 = 0;
		var package_id = '';
		var has_checked_hotel = false;
		for (var k = 0; k < per_person_costing.length; k++) {
			if (per_person_costing[k]['checked'] !== true) {
				continue;
			}
			if (per_person_costing[k]['package_type'] != unique_package_type_arr[u]) {
				continue;
			}
			has_checked_hotel = true;
			// Always re-apply PP formula on current adult count (after ROE conversion)
			var adult_cost_total = quotationHotelAdultPpFromShare(per_person_costing[k], adult_count);
			var cwb_cost_total = (child_with_bed > 0) ? parseFloat(per_person_costing[k]['child_with_bed']) || 0 : 0;
			var cwob_cost_total = (child_without_bed > 0) ? parseFloat(per_person_costing[k]['child_without_bed']) || 0 : 0;
			var infant_cost_total = parseFloat(per_person_costing[k]['infant_cost']) || 0;
			adult_cost_total1 += adult_cost_total;
			cwb_cost_total1 += cwb_cost_total;
			cwob_cost_total1 += cwob_cost_total;
			infant_cost_total1 += infant_cost_total;
			if (!package_id && per_person_costing[k]['package_id']) {
				package_id = per_person_costing[k]['package_id'];
			}
		}
		if (!has_checked_hotel) {
			continue;
		}
		hotel_per_person_arr.push({
			package_id: package_id,
			adult_cost: adult_cost_total1,
			cwb_cost: cwb_cost_total1,
			cwob_cost: cwob_cost_total1,
			infant_cost: infant_cost_total1,
			type: unique_package_type_arr[u],
			checked: true
		});
	}
	return hotel_per_person_arr;
}

/**
 * Force-write hotel tariff PP amounts onto matching package-type costing rows.
 * Save fields: adult_hotel_pp ; Update fields: adult_hotel_pp_update
 * Only overwrites hotel fields by default (keeps transfer/activity unless provided).
 */
function quotationApplyHotelTariffToPpFields(hotel_per_person_arr, options) {
	options = options || {};
	var force = options.force !== false;
	var skipZero = options.skipZero === true;
	var applyTransfer = options.applyTransfer === true;
	var applyActivity = options.applyActivity === true;
	if (!hotel_per_person_arr || !hotel_per_person_arr.length) {
		return;
	}
	var byType = {};
	for (var i = 0; i < hotel_per_person_arr.length; i++) {
		var t = String(hotel_per_person_arr[i].type || hotel_per_person_arr[i].package_type || '');
		if (t) {
			byType[t] = hotel_per_person_arr[i];
		}
	}
	var $rows = $('#quotation_pp_costing_container .quotation-pp-costing-row');
	if (!$rows.length) {
		return;
	}

	function writeHotel($row, suffix, data) {
		var adultHotel = parseFloat(data.adult_cost) || 0;
		var cwebHotel = parseFloat(data.cwb_cost != null ? data.cwb_cost : data.child_with_bed) || 0;
		var cwnbHotel = parseFloat(data.cwob_cost != null ? data.cwob_cost : data.child_without_bed) || 0;
		var infantHotel = parseFloat(data.infant_cost) || 0;
		// Missing hotel tariff (common on local DBs) must not wipe saved PP hotel amounts
		if (skipZero && adultHotel <= 0 && cwebHotel <= 0 && cwnbHotel <= 0 && infantHotel <= 0) {
			return;
		}

		function setField(baseId, value) {
			var $el = $row.find('#' + quotationPpFieldId(baseId, suffix));
			if (!$el.length) {
				return;
			}
			if (skipZero && (parseFloat(value) || 0) <= 0) {
				var existingAmt = (typeof quotationGetFieldDefaultAmount === 'function')
					? quotationGetFieldDefaultAmount($el)
					: (parseFloat($el.val()) || 0);
				if (existingAmt > 0) {
					return;
				}
			}
			if (force) {
				if (typeof quotationWritePpDefaultCurrencyAmount === 'function') {
					quotationWritePpDefaultCurrencyAmount($el, value);
				} else {
					$el.val(value);
				}
				return;
			}
			var cur = $el.val();
			if (cur === '' || cur === null || parseFloat(cur) === 0) {
				if (typeof quotationWritePpDefaultCurrencyAmount === 'function') {
					quotationWritePpDefaultCurrencyAmount($el, value);
				} else {
					$el.val(value);
				}
			}
		}

		$row.find('#' + quotationPpFieldId('adult_cost', suffix)).val(adultHotel);
		$row.find('#' + quotationPpFieldId('child_with', suffix)).val(cwebHotel);
		$row.find('#' + quotationPpFieldId('child_without', suffix)).val(cwnbHotel);
		$row.find('#' + quotationPpFieldId('infant_cost', suffix)).val(infantHotel);

		setField('adult_hotel_pp', adultHotel);
		setField('cweb_hotel_pp', cwebHotel);
		setField('cwnb_hotel_pp', cwnbHotel);
		setField('infant_hotel_pp', infantHotel);
		setField('adult_hotel_pp_update', adultHotel);
		setField('cweb_hotel_pp_update', cwebHotel);
		setField('cwnb_hotel_pp_update', cwnbHotel);
		setField('infant_hotel_pp_update', infantHotel);

		if (applyTransfer) {
			var transportPp = parseFloat(options.transport_pp) || 0;
			var transferAdult = data.transfer_adult != null ? parseFloat(data.transfer_adult) || 0 : transportPp;
			var transferCweb = data.transfer_cweb != null ? parseFloat(data.transfer_cweb) || 0 : transportPp;
			var transferCwnb = data.transfer_cwnb != null ? parseFloat(data.transfer_cwnb) || 0 : transportPp;
			var transferInfant = data.transfer_infant != null ? parseFloat(data.transfer_infant) || 0 : 0;
			setField('adult_transfer_pp', Number(transferAdult).toFixed(2));
			setField('cweb_transfer_pp', Number(transferCweb).toFixed(2));
			setField('cwnb_transfer_pp', Number(transferCwnb).toFixed(2));
			setField('infant_transfer_pp', Number(transferInfant).toFixed(2));
			setField('adult_transfer_pp_update', Number(transferAdult).toFixed(2));
			setField('cweb_transfer_pp_update', Number(transferCweb).toFixed(2));
			setField('cwnb_transfer_pp_update', Number(transferCwnb).toFixed(2));
			setField('infant_transfer_pp_update', Number(transferInfant).toFixed(2));
		}

		if (applyActivity) {
			var activityAdult = data.activity_adult != null ? parseFloat(data.activity_adult) || 0 : (parseFloat(options.activity_adult) || 0);
			var activityCweb = data.activity_cweb != null ? parseFloat(data.activity_cweb) || 0 : (parseFloat(options.activity_cweb) || 0);
			var activityCwnb = data.activity_cwnb != null ? parseFloat(data.activity_cwnb) || 0 : (parseFloat(options.activity_cwnb) || 0);
			var activityInfant = data.activity_infant != null ? parseFloat(data.activity_infant) || 0 : (parseFloat(options.activity_infant) || 0);
			setField('adult_activity_pp', Number(activityAdult).toFixed(2));
			setField('cweb_activity_pp', Number(activityCweb).toFixed(2));
			setField('cwnb_activity_pp', Number(activityCwnb).toFixed(2));
			setField('infant_activity_pp', Number(activityInfant).toFixed(2));
			setField('adult_activity_pp_update', Number(activityAdult).toFixed(2));
			setField('cweb_activity_pp_update', Number(activityCweb).toFixed(2));
			setField('cwnb_activity_pp_update', Number(activityCwnb).toFixed(2));
			setField('infant_activity_pp_update', Number(activityInfant).toFixed(2));
		}
	}

	$rows.each(function () {
		var $row = $(this);
		var suffix = $row.attr('data-pp-suffix') || '';
		var pkg = String(
			$row.attr('data-package-type')
			|| $row.find('[id^="ppackage_type1"]').first().val()
			|| ''
		);
		var data = byType[pkg] || null;
		if (!data && $rows.length === 1 && hotel_per_person_arr.length === 1) {
			data = hotel_per_person_arr[0];
		}
		if (!data) {
			return;
		}
		writeHotel($row, suffix, data);
	});
}

function quotationPopulatePpCostingFromHotels(hotel_per_person_arr, options) {
	var $container = $('#quotation_pp_costing_container');
	if (!$container.length) {
		return false;
	}
	options = options || {};
	options.force = (options.force !== false); // default force when rebuilding from Tab3

	// Capture current activity values before rebuild (tab switch must not wipe them).
	// Use default-currency amounts so a prior display currency (e.g. AED) is not double-converted.
	var preservedActivity = null;
	if (options.preserveActivity !== false) {
		preservedActivity = {
			activity_adult: 0,
			activity_cweb: 0,
			activity_cwnb: 0,
			activity_infant: 0
		};
		var $first = $container.find('.quotation-pp-costing-row').first();
		if ($first.length) {
			var readAct = function (base) {
				var $el = $first.find('[id^="' + base + '"]').first();
				if (typeof quotationGetFieldDefaultAmount === 'function' && $el.length) {
					return quotationGetFieldDefaultAmount($el);
				}
				return parseFloat($el.val()) || 0;
			};
			preservedActivity.activity_adult = readAct('adult_activity_pp');
			preservedActivity.activity_cweb = readAct('cweb_activity_pp');
			preservedActivity.activity_cwnb = readAct('cwnb_activity_pp');
			preservedActivity.activity_infant = readAct('infant_activity_pp');
		}
	}

	// Capture current transfer values before rebuild (same as activity)
	var preservedTransfer = null;
	if (options.preserveTransfer !== false) {
		preservedTransfer = {
			transfer_adult: 0,
			transfer_cweb: 0,
			transfer_cwnb: 0,
			transfer_infant: 0
		};
		var $firstTr = $container.find('.quotation-pp-costing-row').first();
		if ($firstTr.length) {
			var readTr = function (base) {
				var $el = $firstTr.find('[id^="' + base + '"]').first();
				if (typeof quotationGetFieldDefaultAmount === 'function' && $el.length) {
					return quotationGetFieldDefaultAmount($el);
				}
				return parseFloat($el.val()) || 0;
			};
			preservedTransfer.transfer_adult = readTr('adult_transfer_pp');
			preservedTransfer.transfer_cweb = readTr('cweb_transfer_pp');
			preservedTransfer.transfer_cwnb = readTr('cwnb_transfer_pp');
			preservedTransfer.transfer_infant = readTr('infant_transfer_pp');
		}
	}

	// Prefer live transport calc; fall back to preserved values; then options
	var transferFromTables = (typeof quotationCalcTransferPpFromTransportTables === 'function')
		? quotationCalcTransferPpFromTransportTables()
		: null;
	var transferCalcTotal = transferFromTables
		? ((parseFloat(transferFromTables.transfer_adult) || 0) +
			(parseFloat(transferFromTables.transfer_cweb) || 0) +
			(parseFloat(transferFromTables.transfer_cwnb) || 0) +
			(parseFloat(transferFromTables.transfer_infant) || 0))
		: 0;
	var transferSource = null;
	if (transferCalcTotal > 0) {
		transferSource = transferFromTables;
	} else if (preservedTransfer && (
		preservedTransfer.transfer_adult ||
		preservedTransfer.transfer_cweb ||
		preservedTransfer.transfer_cwnb ||
		preservedTransfer.transfer_infant
	) && (typeof quotationHasCheckedTransportRows !== 'function' || quotationHasCheckedTransportRows())) {
		transferSource = preservedTransfer;
	}
	if (transferSource) {
		if (options.transport_pp == null && transferSource.transport_pp != null) {
			options.transport_pp = transferSource.transport_pp;
		}
		if (options.transfer_adult == null) options.transfer_adult = transferSource.transfer_adult;
		if (options.transfer_cweb == null) options.transfer_cweb = transferSource.transfer_cweb;
		if (options.transfer_cwnb == null) options.transfer_cwnb = transferSource.transfer_cwnb;
		if (options.transfer_infant == null) options.transfer_infant = transferSource.transfer_infant;
	}

	// Prefer live excursion calc; fall back to preserved values; then options
	var activityFromExc = (typeof quotationCalcActivityPpFromExcursionTable === 'function')
		? quotationCalcActivityPpFromExcursionTable()
		: null;
	var activityCalcTotal = activityFromExc
		? ((parseFloat(activityFromExc.activity_adult) || 0) +
			(parseFloat(activityFromExc.activity_cweb) || 0) +
			(parseFloat(activityFromExc.activity_cwnb) || 0) +
			(parseFloat(activityFromExc.activity_infant) || 0))
		: 0;
	var activitySource = null;
	if (activityCalcTotal > 0) {
		activitySource = activityFromExc;
	} else if (preservedActivity && (
		preservedActivity.activity_adult ||
		preservedActivity.activity_cweb ||
		preservedActivity.activity_cwnb ||
		preservedActivity.activity_infant
	)) {
		activitySource = preservedActivity;
	}
	if (activitySource) {
		if (options.activity_adult == null) options.activity_adult = activitySource.activity_adult;
		if (options.activity_cweb == null) options.activity_cweb = activitySource.activity_cweb;
		if (options.activity_cwnb == null) options.activity_cwnb = activitySource.activity_cwnb;
		if (options.activity_infant == null) options.activity_infant = activitySource.activity_infant;
	}

	var $template = $container.find('.quotation-pp-costing-row').first();
	if (!$template.length) {
		return false;
	}

	quotationStripPpCostingRowSelect2($template);
	// Ensure template has a single package-type row (never multi-row addRow leftovers)
	$template.find('table[id*="tbl_package_tour_quotation_adult_child"] tr').slice(1).remove();

	var $templateClone = $template.clone(false, false);
	quotationStripPpCostingRowSelect2($templateClone);
	$templateClone.find('input, select, textarea').each(function () {
		if (this.type === 'checkbox' || this.type === 'radio') {
			return;
		}
		if (this.tagName === 'SELECT') {
			var sid = String(this.id || this.name || '');
			if (/currency_code_pp/i.test(sid)) {
				var companyCur = $(this).attr('data-company-currency')
					|| (typeof quotationGetCompanyDefaultCurrencyId === 'function' ? quotationGetCompanyDefaultCurrencyId() : '')
					|| '';
				if (companyCur) {
					$(this).val(companyCur);
				} else {
					this.selectedIndex = 0;
				}
			} else {
				this.selectedIndex = 0;
			}
			return;
		}
		if ($(this).attr('id') && /hotel_pp|transfer_pp|activity_pp|land_cost_pp|service_charge_pp|discount_amount_pp|tax_amount_pp|tcs_amount_pp|total_amount_pp|_cost$|landcost/.test(this.id)) {
			$(this).val('0');
		}
	});
	$container.empty();

	if (!hotel_per_person_arr || !hotel_per_person_arr.length) {
		hotel_per_person_arr = [{ type: 'NA', adult_cost: 0, cwb_cost: 0, cwob_cost: 0, infant_cost: 0 }];
	}

	var total = hotel_per_person_arr.length;
	for (var i = 0; i < total; i++) {
		if (i > 0) {
			$container.append('<hr class="quotation-package-costing-separator">');
		}
		var suffix = quotationPpCostingSuffix(i, total);
		var $row = $templateClone.clone(false, false);
		quotationStripPpCostingRowSelect2($row);
		quotationPpCostingRenumberRow($row, suffix);
		$container.append($row);
		var rowData = hotel_per_person_arr[i] || {};
		if (transferSource) {
			if (rowData.transfer_adult == null) rowData.transfer_adult = transferSource.transfer_adult;
			if (rowData.transfer_cweb == null) rowData.transfer_cweb = transferSource.transfer_cweb;
			if (rowData.transfer_cwnb == null) rowData.transfer_cwnb = transferSource.transfer_cwnb;
			if (rowData.transfer_infant == null) rowData.transfer_infant = transferSource.transfer_infant;
		}
		if (activitySource) {
			if (rowData.activity_adult == null) rowData.activity_adult = activitySource.activity_adult;
			if (rowData.activity_cweb == null) rowData.activity_cweb = activitySource.activity_cweb;
			if (rowData.activity_cwnb == null) rowData.activity_cwnb = activitySource.activity_cwnb;
			if (rowData.activity_infant == null) rowData.activity_infant = activitySource.activity_infant;
		}
		quotationPpCostingSetRowValues($row, suffix, rowData, options);
	}

	// Re-apply session currency (Previous→Next) or fall back to company profile default
	var savedCurrency = (typeof quotationGetSavedTab4CurrencyCode === 'function')
		? quotationGetSavedTab4CurrencyCode()
		: '';
	if (!savedCurrency && typeof quotationGetCompanyDefaultCurrencyId === 'function') {
		savedCurrency = quotationGetCompanyDefaultCurrencyId();
	}
	if (!savedCurrency && typeof quotationGetSelectedCurrencyId === 'function') {
		savedCurrency = quotationGetSelectedCurrencyId();
	}
	if (savedCurrency && typeof quotationEnsureCostingCurrencySelection === 'function') {
		quotationEnsureCostingCurrencySelection(savedCurrency);
	}

	quotationInitPpCostingSelect2($container);

	if (savedCurrency && typeof quotationEnsureCostingCurrencySelection === 'function') {
		quotationEnsureCostingCurrencySelection(savedCurrency);
	}

	// Keep current ROE factor (for tariff→quotation conversion). Do not reset to 1 —
	// amounts written below go through quotationWritePpDefaultCurrencyAmount.

	if (typeof calculateCostingCards === 'function') {
		setTimeout(function () {
			calculateCostingCards(true);
		}, 0);
	}
	return true;
}

function quotationCollectPpCostingEntries(options) {
	options = options || {};
	// save: adult_hotel_pp / adult_tax_amount_pp
	// update: adult_hotel_pp_update / adult_tax_amt_pp_update
	var mode = options.mode === 'update' ? 'update' : 'save';
	var packages = [];
	var $rows = $('#quotation_pp_costing_container .quotation-pp-costing-row');

	function fieldIds(prefix) {
		if (mode === 'update') {
			return {
				hotel: prefix + '_hotel_pp_update',
				transfer: prefix + '_transfer_pp_update',
				activity: prefix + '_activity_pp_update',
				land_cost: prefix + '_land_cost_pp_update',
				service_charge: prefix + '_service_charge_pp_update',
				discount_in: prefix + '_discount_in_pp_update',
				discount_amount: prefix + '_discount_amount_pp_update',
				flight: prefix + '_flight_pp_update',
				train: prefix + '_train_pp_update',
				cruise: prefix + '_cruise_pp_update',
				visa: prefix + '_visa_pp_update',
				guide: prefix + '_guide_pp_update',
				misc: prefix + '_misc_pp_update',
				tax_apply_on: prefix + '_tax_apply_on_pp_update',
				tax_value: prefix + '_select_tax_pp_update',
				tax_amount: prefix + '_tax_amt_pp_update',
				tcs: prefix + '_select_tcs_pp_update',
				tcs_amount: prefix + '_tcs_amount_pp_update',
				total: prefix + '_total_amount_pp_update'
			};
		}
		return {
			hotel: prefix + '_hotel_pp',
			transfer: prefix + '_transfer_pp',
			activity: prefix + '_activity_pp',
			land_cost: prefix + '_land_cost_pp',
			service_charge: prefix + '_service_charge_pp',
			discount_in: prefix + '_discount_in_pp',
			discount_amount: prefix + '_discount_amount_pp',
			flight: prefix + '_flight_pp',
			train: prefix + '_train_pp',
			cruise: prefix + '_cruise_pp',
			visa: prefix + '_visa_pp',
			guide: prefix + '_guide_pp',
			misc: prefix + '_misc_pp',
			tax_apply_on: prefix + '_tax_apply_on_pp',
			tax_value: prefix + '_select_tax_pp',
			tax_amount: prefix + '_tax_amount_pp',
			tcs: prefix + '_select_tcs_pp',
			tcs_amount: prefix + '_tcs_amount_pp',
			total: prefix + '_total_amount_pp'
		};
	}

	function readAmt($el) {
		if (!$el || !$el.length) {
			return 0;
		}
		// Always persist the visible quotation-currency value. data-default-amount on
		// derived fields (tax/TCS/total) can stay stale after costing recalculation.
		return parseFloat(String($el.val() || '').replace(/,/g, '')) || 0;
	}

	function readPP($scope, prefix, suffix) {
		var ids = fieldIds(prefix);
		var g = function (base) {
			var sel = '#' + quotationPpFieldId(base, suffix);
			return $scope && $scope.length ? $scope.find(sel) : $(sel);
		};
		var discountIn = g(ids.discount_in).val() || '';
		var discountAmt = (String(discountIn) === '1')
			? (+g(ids.discount_amount).val() || 0)
			: readAmt(g(ids.discount_amount));
		return {
			type: prefix,
			hotel: readAmt(g(ids.hotel)),
			transfer: readAmt(g(ids.transfer)),
			activity: readAmt(g(ids.activity)),
			land_cost: readAmt(g(ids.land_cost)),
			service_charge: readAmt(g(ids.service_charge)),
			discount_in: discountIn,
			discount_amount: discountAmt,
			flight: readAmt(g(ids.flight)),
			train: readAmt(g(ids.train)),
			cruise: readAmt(g(ids.cruise)),
			visa: readAmt(g(ids.visa)),
			guide: readAmt(g(ids.guide)),
			misc: readAmt(g(ids.misc)),
			tax_apply_on: g(ids.tax_apply_on).val() || '',
			tax_value: g(ids.tax_value).val() || '',
			tax_amount: readAmt(g(ids.tax_amount)),
			tcs: g(ids.tcs).val() || '',
			tcs_amount: readAmt(g(ids.tcs_amount)),
			total: readAmt(g(ids.total))
		};
	}

	// Do not invent a zero-filled package when PP rows are missing — that would
	// wipe real DB totals on update. Caller/model treat empty as "keep existing".
	if (!$rows.length) {
		return [];
	}

	$rows.each(function () {
		var $row = $(this);
		var suffix = $row.attr('data-pp-suffix') || '';
		packages.push({
			package_type: $row.find('#' + quotationPpFieldId('ppackage_type1', suffix)).val() || ($row.attr('data-package-type') || ''),
			package_id: $row.find('#' + quotationPpFieldId('pacakge_id2', suffix)).val() || '',
			rows: [readPP($row, 'adult', suffix), readPP($row, 'cweb', suffix), readPP($row, 'cwnb', suffix), readPP($row, 'infant', suffix)]
		});
	});
	return packages;
}

function quotationResolveGroupTaxSubtotal(suffix) {
	var s = suffix || '-';
	if (s.charAt(0) !== '-') {
		s = '-' + s;
	}
	var stored = ($('#service_tax_subtotal' + s).val() || '').trim();
	if (stored && stored !== '0' && stored !== '0.00' && stored.indexOf(':') !== -1) {
		return stored;
	}
	if (stored && !isNaN(parseFloat(stored)) && parseFloat(stored) > 0 && stored.indexOf(':') === -1) {
		return stored;
	}
	var taxApply = $('#tax_apply_on' + s).val() || $('#atax_apply_on' + s).val() || '';
	var taxValue = $('#tax_value' + s).val() || $('#tax_value1' + s).val() || '';
	if (!taxApply || !taxValue) {
		return stored;
	}
	var basicAmount = parseFloat($('#basic_amount' + s).val()) || 0;
	var serviceCharge = parseFloat($('#service_charge' + s).val()) || 0;
	var discountIn = $('#discount_in' + s).val();
	var discountAmt = parseFloat($('#discount_amt' + s).val()) || 0;
	var discount = (discountIn === 'Percentage')
		? (serviceCharge * discountAmt / 100)
		: discountAmt;
	var serviceAfter = serviceCharge - discount;
	var taxOn = 0;
	if (String(taxApply) === '1') {
		taxOn = basicAmount;
	} else if (String(taxApply) === '2') {
		taxOn = serviceAfter;
	} else if (String(taxApply) === '3') {
		taxOn = basicAmount + serviceAfter;
	}
	var chunks = String(taxValue).split('+');
	var applied = [];
	for (var i = 0; i < chunks.length; i++) {
		var parts = chunks[i].split(':');
		if (!parts.length || !parts[1]) {
			continue;
		}
		var pct = parseFloat(String(parts[1]).replace('(', '').replace('%', '')) || 0;
		var amt = ((taxOn * pct) / 100).toFixed(2);
		applied.push(String(parts[0]).trim() + ':' + String(parts[1]).trim() + ':' + amt);
	}
	return applied.length ? applied.join(', ') : stored;
}

function quotationGroupCostingSuffixFromFieldId(fieldId) {
	if (typeof quotationCostingFieldSuffix === 'function') {
		return quotationCostingFieldSuffix(fieldId) || '-';
	}
	var id = String(fieldId || '');
	var dash = id.indexOf('-');
	return dash >= 0 ? id.slice(dash) : '-';
}

function quotationFindGroupCostingTourCostFields($root) {
	$root = ($root && $root.length) ? $root : $('#tbl_package_tour_quotation_dynamic_costing');
	function isTourCostInput(el) {
		var id = el && el.id ? el.id : '';
		if (!id || /_u$/.test(id) || /^total_tour_cost/.test(id)) {
			return false;
		}
		return id === 'tour_cost' || id === 'tour_cost-' || /^tour_cost-\d+$/.test(id);
	}
	var $fields = $();
	$root.find('.quotation-group-costing-row').each(function () {
		var $tour = $(this).find('input').filter(function () {
			return isTourCostInput(this);
		}).first();
		if ($tour.length) {
			$fields = $fields.add($tour);
		}
	});
	if ($fields.length) {
		return $fields;
	}
	return $root.find('input').filter(function () {
		return isTourCostInput(this);
	});
}

function quotationCollectGroupCostingEntries() {
	if (!isQuotationGroupCostingDiv()) {
		return null;
	}

	function readAmt($el) {
		if (typeof quotationGetFieldDefaultAmount === 'function') {
			return quotationGetFieldDefaultAmount($el);
		}
		return $el.val() || '';
	}
	function readAmtStr($el) {
		var v = readAmt($el);
		return (v === '' || v === null || typeof v === 'undefined') ? '' : String(v);
	}

	var entries = [];
	quotationFindGroupCostingTourCostFields().each(function () {
		var suffix = quotationGroupCostingSuffixFromFieldId(this.id);
		var discountIn = $('#discount_in' + suffix).val() || '';
		var discountVal = (String(discountIn) === '1')
			? ($('#discount_amt' + suffix).val() || '')
			: readAmtStr($('#discount_amt' + suffix));
		entries.push({
			package_type_c: $('#package_type' + suffix).val() || '',
			tour_cost: readAmtStr($('#tour_cost' + suffix)),
			transport_cost: readAmtStr($('#transport_cost1' + suffix).length ? $('#transport_cost1' + suffix) : $('#transport_cost' + suffix)),
			excursion_cost: readAmtStr($('#excursion_cost' + suffix)),
			basic_cost: $('#basic_amount' + suffix).val() || '',
			service_tax: readAmtStr($('#service_charge' + suffix)),
			discount_in: discountIn,
			discount: discountVal,
			tax_apply_on: $('#tax_apply_on' + suffix).val() || '',
			tax_value: $('#tax_value' + suffix).val() || '',
			service_tax_subtotal: (typeof quotationResolveGroupTaxSubtotal === 'function')
				? quotationResolveGroupTaxSubtotal(suffix)
				: ($('#service_tax_subtotal' + suffix).val() || ''),
			tcs: $('#tcs_tax' + suffix).val() || '',
			tcsvalue: $('#tcs1' + suffix).val() || '',
			tdsvalue: readAmt($('#tds' + suffix)) || 0,
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
	quotationFindGroupCostingTourCostFields().each(function () {
		var suffix = quotationGroupCostingSuffixFromFieldId(this.id);
		var inc = (typeof quotationGroupInclusiveShowAmount === 'function')
			? quotationGroupInclusiveShowAmount(suffix)
			: { basic: '', service: '' };
		bsmValues.push([{
			"basic": inc.basic,
			"service": inc.service,
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
	var preserve = typeof quotationIsPackageQuotationUpdate === 'function' && quotationIsPackageQuotationUpdate();
	$('#tbl_package_tour_quotation_dynamic_costing').find('[id^="excursion_cost-"]').each(function () {
		if (/_u$/.test(this.id)) {
			return;
		}
		var $el = $(this);
		if (preserve || (typeof quotationProtectUpdateGroupLandField === 'function' && quotationProtectUpdateGroupLandField($el))) {
			var cur = parseFloat(String($el.val() || '').replace(/,/g, '')) || 0;
			if (cur > 0 || (typeof quotationIsManualAmountField === 'function' && quotationIsManualAmountField($el))
				|| (typeof quotationProtectUpdateGroupLandField === 'function' && quotationProtectUpdateGroupLandField($el))) {
				return;
			}
		}
		$el.val(total_amount);
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

	var preserve = typeof quotationIsPackageQuotationUpdate === 'function' && quotationIsPackageQuotationUpdate();

	quotationFindGroupCostingTourCostFields().each(function () {
		var suffix = quotationGroupCostingSuffixFromFieldId(this.id);
		var $transport = $('#transport_cost1' + suffix);
		if (!$transport.length) {
			$transport = $('#transport_cost' + suffix);
		}
		if (preserve) {
			var cur = parseFloat(String($transport.val() || '').replace(/,/g, '')) || 0;
			if (cur > 0 || (typeof quotationIsManualAmountField === 'function' && quotationIsManualAmountField($transport))
				|| (typeof quotationProtectUpdateGroupLandField === 'function' && quotationProtectUpdateGroupLandField($transport))) {
				if (typeof quotation_cost_calculate1 === 'function') {
					quotation_cost_calculate1($transport.attr('id'));
				} else if (typeof quotation_cost_calculate === 'function') {
					quotation_cost_calculate('tour_cost' + suffix);
				}
				return;
			}
		}
		var hotel_cost = parseFloat($('#tour_cost' + suffix).val()) || 0;
		$transport.val(transport_cost);
		if (typeof quotation_cost_calculate === 'function') {
			quotation_cost_calculate('tour_cost' + suffix);
		} else {
			$('#total_tour_cost' + suffix).val(parseFloat(transport_cost) + hotel_cost);
			$('#basic_amount' + suffix).val(parseFloat(transport_cost) + hotel_cost);
		}
	});
	return true;
}

/** Company profile default currency id from costing dropdowns. */
function quotationGetCompanyDefaultCurrencyId() {
	var fromData = $('#currency_code').attr('data-company-currency')
		|| $('#currency_code1').attr('data-company-currency')
		|| $('[id^="currency_code_pp"]').first().attr('data-company-currency')
		|| '';
	if (fromData) {
		return String(fromData);
	}
	// Fallback: currently selected option on group/PP (should already be company default on create)
	return $('#currency_code').val() || $('#currency_code_pp').val() || '';
}

/** On create load: force all costing currency dropdowns to company profile default. */
function quotationInitCostingCurrencyToCompanyDefault() {
	var companyCur = quotationGetCompanyDefaultCurrencyId();
	if (!companyCur || companyCur === '0') {
		return;
	}
	quotationEnsureCostingCurrencySelection(companyCur);
	// Do not trigger full ROE change on init — amounts are still empty/default currency
}

/** Saved quotation/document currency from Tab4 session (group or per-person). */
function quotationGetSavedTab4CurrencyCode() {
	try {
		// Only reuse session currency after the user has visited Tab4 in this draft
		if (sessionStorage.getItem('quotation_tab4_costing_visited') !== '1') {
			return '';
		}
		var travelRaw = sessionStorage.getItem('quotation_tab4_travel_cost_state');
		if (travelRaw) {
			var travel = JSON.parse(travelRaw);
			if (travel && travel.currency_code) {
				return String(travel.currency_code);
			}
		}
	} catch (e) {}
	return '';
}

/** Keep group + per-person currency dropdowns on the same id (select2-safe). */
function quotationEnsureCostingCurrencySelection(currencyId) {
	if (currencyId === undefined || currencyId === null || currencyId === '') {
		return;
	}
	currencyId = String(currencyId);
	var $all = $('#currency_code, #currency_code1').add($('[id^="currency_code_pp"]'));
	$all.each(function () {
		var $el = $(this);
		if (String($el.val()) !== currencyId) {
			$el.val(currencyId);
		}
		if ($el.data('select2')) {
			$el.trigger('change.select2');
		}
	});
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
		var currencyCode = (typeof quotationGetSelectedCurrencyId === 'function')
			? quotationGetSelectedCurrencyId()
			: ($('#currency_code').val() || $('#currency_code_pp').val() || '');
		sessionStorage.setItem('quotation_tab4_travel_cost_state', JSON.stringify({
			flight_cost: $('#flight_cost').val() || '',
			train_cost: $('#train_cost').val() || '',
			cruise_cost: $('#cruise_cost').val() || '',
			visa_cost: $('#visa_cost').val() || '',
			guide_cost: $('#guide_cost').val() || '',
			misc_cost: $('#misc_cost').val() || '',
			misc_desc: $('#misc_desc').val() || $('#miscellaneous_desc').val() || '',
			currency_code: currencyCode || ''
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

	var $costInputs = quotationFindGroupCostingTourCostFields();
	$costInputs.each(function () {
		var suffix = quotationGroupCostingSuffixFromFieldId(this.id);
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
			if (travel.currency_code) {
				// Sync dropdowns only — do not convert yet. PP/group rebuild writes
				// default-currency amounts next; calculateCostingCards applies ROE.
				quotationEnsureCostingCurrencySelection(travel.currency_code);
			}
		} catch (e2) {}
	}
	return true;
}

/**
 * Quotation costing amounts are in the selected quotation currency.
 * Manual entry (USD selected → user types USD) is stored/displayed as-is — no ROE rewrite.
 * Tariff/travel amounts arrive in company currency, are tagged data-amount-source=tariff
 * with data-tariff-company-amount, and convert via ROE on write and on currency change.
 * ROE: amount_in_to = (from_rate / to_rate) * amount_in_from
 * Example INR(1)→USD(95): 95000 tariff → 1000 shown/saved in USD
 */
window.quotationCurrencyDisplayFactor = window.quotationCurrencyDisplayFactor || 1;
window.quotationApplyingCurrencyDisplay = false;
window.quotationWritingDefaultCurrencyAmounts = false;

function quotationGetSelectedCurrencyId() {
	var $pp = $('[id^="currency_code_pp"]').filter(function () {
		return $(this).val();
	}).first();
	return $('#currency_code').val()
		|| $('#currency_code1').val()
		|| ($pp.length ? $pp.val() : '')
		|| $('#currency_code_pp').val()
		|| '';
}

function quotationIsCurrencyConvertibleField($el) {
	if (!$el || !$el.length) {
		return false;
	}
	var tag = ($el.prop('tagName') || '').toLowerCase();
	if (tag === 'select' || tag === 'textarea') {
		return false;
	}
	var id = String($el.attr('id') || '');
	if (!id) {
		return false;
	}
	if (/discount_in|tax_apply_on|select_tax|select_tcs|package_type|package_name|package_id|currency|ppackage|pacakge|other_desc|misc_desc|login_id|upload_url/i.test(id)) {
		return false;
	}
	// Derived / non-currency text — always use live field values (do not convert)
	if (/total_tour_cost|basic_amount|service_tax_subtotal|^tcs1($|-)|atax_apply|tax_value/i.test(id)) {
		return false;
	}
	if (/land_cost_pp|tax_amount_pp|tax_amt_pp|tcs_amount_pp|total_amount_pp/i.test(id)) {
		return false;
	}
	// Percentage discount value is not a money amount
	if (/discount_amt|discount_amount/i.test(id)) {
		var discountInId = id
			.replace('discount_amt', 'discount_in')
			.replace('discount_amount_pp_update', 'discount_in_pp_update')
			.replace('discount_amount_pp', 'discount_in_pp');
		var $din = $('#' + discountInId);
		if ($din.length && String($din.val()) === '1') {
			return false;
		}
	}
	return $el.is('input[type="number"], input[type="text"], input:not([type])')
		&& !$el.is('[type="checkbox"], [type="radio"], [type="button"], [type="submit"], [type="hidden"]');
}

function quotationGetCostingConvertibleInputs(scope) {
	var sels;
	if (scope === 'pp') {
		sels = [
			'#quotation_pp_costing_container .costing-table input',
			'#quotation_pp_costing_container input[id*="_hotel_pp"]',
			'#quotation_pp_costing_container input[id*="_transfer_pp"]',
			'#quotation_pp_costing_container input[id*="_activity_pp"]',
			'#quotation_pp_costing_container input[id*="_land_cost_pp"]',
			'#quotation_pp_costing_container input[id*="_service_charge_pp"]',
			'#quotation_pp_costing_container input[id*="_total_amount_pp"]'
		];
	} else if (scope === 'group') {
		sels = [
			'#tbl_package_tour_quotation_dynamic_costing input',
			'#flight_cost, #train_cost, #cruise_cost, #visa_cost, #guide_cost, #misc_cost',
			'#flight_cost1, #train_cost1, #cruise_cost1, #visa_cost1, #guide_cost1, #misc_cost1'
		];
	} else {
		sels = [
			'#tbl_package_tour_quotation_dynamic_costing input',
			'#flight_cost, #train_cost, #cruise_cost, #visa_cost, #guide_cost, #misc_cost',
			'#flight_cost1, #train_cost1, #cruise_cost1, #visa_cost1, #guide_cost1, #misc_cost1',
			'#quotation_pp_costing_container .costing-table input',
			'#quotation_pp_costing_container input[id*="_hotel_pp"]',
			'#quotation_pp_costing_container input[id*="_transfer_pp"]',
			'#quotation_pp_costing_container input[id*="_activity_pp"]',
			'#quotation_pp_costing_container input[id*="_land_cost_pp"]',
			'#quotation_pp_costing_container input[id*="_service_charge_pp"]',
			'#quotation_pp_costing_container input[id*="_total_amount_pp"]'
		];
	}
	return $(sels.join(',')).filter(function () {
		return quotationIsCurrencyConvertibleField($(this));
	});
}

/**
 * Mark DB-loaded PP transfer/activity amounts as manual on update so travel-stay
 * tariff refresh does not wipe values entered directly in costing cards.
 */
function quotationMarkSavedPpTransferActivityAsManual() {
	var $scope = $('#quotation_pp_costing_container');
	if (!$scope.length) {
		return;
	}
	$scope.find('input[data-saved-amount]').each(function () {
		var $el = $(this);
		var id = String($el.attr('id') || '');
		if (!/(transfer|activity)_pp_update/.test(id)) {
			return;
		}
		var saved = parseFloat(String($el.attr('data-saved-amount')).replace(/,/g, '')) || 0;
		if (saved > 0 && typeof quotationMarkFieldAsManualAmount === 'function') {
			quotationMarkFieldAsManualAmount($el);
		}
	});
}

/** Amounts are stored in quotation currency — data-default-amount matches the visible value. */
function quotationGetFieldDefaultAmount($el) {
	if (!$el || !$el.length) {
		return 0;
	}
	var display = parseFloat(String($el.val()).replace(/,/g, '')) || 0;
	if (typeof quotationIsManualAmountField === 'function' && quotationIsManualAmountField($el)) {
		return display;
	}
	var raw = $el.attr('data-default-amount');
	if (raw !== undefined && raw !== null && raw !== '') {
		var parsed = parseFloat(raw);
		if (isNaN(parsed)) {
			parsed = 0;
		}
		// Stale lock from early empty snapshot while field now has a real amount
		if (parsed === 0 && display !== 0 && !window.quotationApplyingCurrencyDisplay) {
			return display;
		}
		return parsed;
	}
	return display;
}

/**
 * Convert a company-currency tariff amount into the selected quotation currency.
 */
function quotationConvertTariffAmountToQuotationCurrency(tariffAmount) {
	tariffAmount = parseFloat(tariffAmount);
	if (isNaN(tariffAmount)) {
		tariffAmount = 0;
	}
	var factor = window.quotationCurrencyDisplayFactor || 1;
	if (!factor || isNaN(factor) || Math.abs(factor - 1) < 0.0000001) {
		return tariffAmount;
	}
	return Math.round(tariffAmount * factor * 100) / 100;
}

/**
 * If a field shows a real amount but data-default-amount is missing/0 (locked from
 * an empty page-load snapshot), refresh the default from the current display.
 */
function quotationRepairStaleZeroDefaultAmounts(scope) {
	if (window.quotationApplyingCurrencyDisplay) {
		return;
	}
	quotationGetCostingConvertibleInputs(scope).each(function () {
		var $el = $(this);
		var display = parseFloat(String($el.val()).replace(/,/g, '')) || 0;
		if (display === 0) {
			return;
		}
		var raw = $el.attr('data-default-amount');
		var def = (raw !== undefined && raw !== null && raw !== '') ? (parseFloat(raw) || 0) : null;
		if (def === null || def === 0) {
			$el.attr('data-default-amount', display);
		}
	});
}

function quotationSnapshotDefaultCostingAmounts(force, scope) {
	quotationGetCostingConvertibleInputs(scope).each(function () {
		var $el = $(this);
		if (!force && $el.attr('data-default-amount') !== undefined && $el.attr('data-default-amount') !== '') {
			return;
		}
		var display = parseFloat(String($el.val()).replace(/,/g, ''));
		if (isNaN(display)) {
			display = 0;
		}
		var existingRaw = $el.attr('data-default-amount');
		var existingDef = (existingRaw !== undefined && existingRaw !== null && existingRaw !== '')
			? (parseFloat(existingRaw) || 0)
			: null;
		// Never lock empty fields as 0 — and never wipe a real saved default with 0
		if (display === 0) {
			if (force || window.quotationWritingDefaultCurrencyAmounts) {
				return;
			}
			if (existingDef !== null && existingDef > 0) {
				return;
			}
		}
		$el.attr('data-default-amount', display);
	});
}

function quotationCaptureDisplayAmountsAsDefault(scope) {
	if (window.quotationApplyingCurrencyDisplay || window.quotationWritingDefaultCurrencyAmounts) {
		return;
	}
	quotationGetCostingConvertibleInputs(scope).each(function () {
		var $el = $(this);
		var display = parseFloat(String($el.val()).replace(/,/g, ''));
		if (isNaN(display)) {
			display = 0;
		}
		$el.attr('data-default-amount', display);
	});
}

function quotationMarkFieldAsTariffAmount($el, companyAmount) {
	if (!$el || !$el.length) {
		return;
	}
	companyAmount = parseFloat(companyAmount);
	if (isNaN(companyAmount)) {
		companyAmount = 0;
	}
	$el.attr('data-amount-source', 'tariff');
	$el.attr('data-tariff-company-amount', companyAmount);
}

function quotationMarkFieldAsManualAmount($el) {
	if (!$el || !$el.length) {
		return;
	}
	$el.attr('data-amount-source', 'manual');
	$el.removeAttr('data-tariff-company-amount');
}

function quotationIsManualAmountField($el) {
	return !!( $el && $el.length && String($el.attr('data-amount-source') || '') === 'manual' );
}

function quotationIsTariffAmountField($el) {
	return !!( $el && $el.length && String($el.attr('data-amount-source') || '') === 'tariff' );
}

/**
 * Write auto service-charge (company currency) into display currency and mark as tariff-sourced.
 * Skips fields the user edited manually.
 */
function quotationWriteTariffServiceCharge($el, companyScAmount) {
	if (!$el || !$el.length) {
		return false;
	}
	if (quotationIsManualAmountField($el)) {
		return false;
	}
	companyScAmount = parseFloat(companyScAmount);
	if (isNaN(companyScAmount)) {
		companyScAmount = 0;
	}
	quotationMarkFieldAsTariffAmount($el, companyScAmount);
	var amount = quotationConvertTariffAmountToQuotationCurrency(companyScAmount);
	$el.attr('data-default-amount', amount);
	$el.val(Number(amount).toFixed(2));
	return true;
}

function quotationApplyCurrencyFactorToCostingFields(factor, scope) {
	factor = parseFloat(factor);
	if (!factor || isNaN(factor)) {
		factor = 1;
	}
	window.quotationCurrencyDisplayFactor = factor;
	// Only tariff-sourced fields re-convert; manual amounts stay as typed.
	quotationReconvertTariffSourcedFields(scope);
}

/**
 * Re-apply ROE to fields that still carry a company-currency tariff amount.
 * Manual / saved amounts (no data-amount-source=tariff) are left unchanged.
 */
function quotationReconvertTariffSourcedFields(scope) {
	var convertedAny = false;
	window.quotationApplyingCurrencyDisplay = true;
	try {
		quotationGetCostingConvertibleInputs(scope).each(function () {
			var $el = $(this);
			if (!quotationIsTariffAmountField($el)) {
				return;
			}
			var companyRaw = $el.attr('data-tariff-company-amount');
			if (companyRaw === undefined || companyRaw === null || companyRaw === '') {
				return;
			}
			var companyAmt = parseFloat(String(companyRaw).replace(/,/g, ''));
			if (isNaN(companyAmt)) {
				return;
			}
			quotationWritePpDefaultCurrencyAmount($el, companyAmt);
			convertedAny = true;
		});
	} finally {
		window.quotationApplyingCurrencyDisplay = false;
	}
	if (!convertedAny) {
		if (!scope || scope === 'pp') {
			quotationRecalcPpDerivedAmountsAfterCurrency({ recalcServiceCharge: false });
		}
		return convertedAny;
	}
	quotationRecalcPpDerivedAmountsAfterCurrency({ recalcServiceCharge: true });
	// Group costing rows: re-run row calculator after tariff land fields changed
	if (!scope || scope === 'group') {
		$('#tbl_package_tour_quotation_dynamic_costing')
			.find('input[id^="hotel_cost"], input[id^="transport_cost"], input[id^="excursion_cost"]')
			.filter(function () {
				return quotationIsTariffAmountField($(this)) && !/_u$/.test(this.id);
			})
			.each(function () {
				if (typeof quotation_cost_calculate1 === 'function') {
					quotation_cost_calculate1(this.id);
				} else if (typeof quotation_cost_calculate === 'function') {
					quotation_cost_calculate(this.id);
				}
			});
	}
	return convertedAny;
}

/** Recompute PP land/tax/TCS/total after tariff/currency factor updates. */
function quotationRecalcPpDerivedAmountsAfterCurrency(options) {
	options = options || {};
	var recalcSc = options.recalcServiceCharge === true;
	if (!$('#quotation_pp_costing_container').length) {
		return;
	}
	if (typeof calculateCostingCardsUpdate === 'function') {
		calculateCostingCardsUpdate({
			recalcServiceCharge: recalcSc,
			fromDefaultCurrencyWrite: false
		});
		return;
	}
	if (typeof calculateCostingCards === 'function') {
		calculateCostingCards(false, {
			autoFill: false,
			recalcServiceCharge: recalcSc
		});
	}
}

/**
 * Write a tariff amount (company currency) into a PP field as quotation-currency.
 * Keeps the company amount so a later currency change can re-convert tariff data only.
 */
function quotationWritePpDefaultCurrencyAmount($el, tariffAmountInCompanyCurrency) {
	if (!$el || !$el.length) {
		return;
	}
	if (typeof quotationProtectUpdateGroupLandField === 'function' && quotationProtectUpdateGroupLandField($el)) {
		return;
	}
	if (typeof quotationIsManualAmountField === 'function' && quotationIsManualAmountField($el)) {
		return;
	}
	tariffAmountInCompanyCurrency = parseFloat(tariffAmountInCompanyCurrency);
	if (isNaN(tariffAmountInCompanyCurrency)) {
		tariffAmountInCompanyCurrency = 0;
	}
	quotationMarkFieldAsTariffAmount($el, tariffAmountInCompanyCurrency);
	var amount = quotationConvertTariffAmountToQuotationCurrency(tariffAmountInCompanyCurrency);
	$el.attr('data-default-amount', amount);
	$el.val(Number(amount).toFixed(2));
}

function quotationGetDefaultCostingTotal() {
	var total = 0;
	var $groupTotals = $('input[id^="total_tour_cost"]');
	if ($groupTotals.length) {
		$groupTotals.each(function () {
			total += quotationGetFieldDefaultAmount($(this));
		});
		if (total > 0) {
			return total;
		}
	}
	var adult = quotationGetFieldDefaultAmount($('#adult_total_amount_pp').length ? $('#adult_total_amount_pp') : $('#adult_total_amount_pp_update'));
	var cweb = quotationGetFieldDefaultAmount($('#cweb_total_amount_pp').length ? $('#cweb_total_amount_pp') : $('#cweb_total_amount_pp_update'));
	var cwnb = quotationGetFieldDefaultAmount($('#cwnb_total_amount_pp').length ? $('#cwnb_total_amount_pp') : $('#cwnb_total_amount_pp_update'));
	var infant = quotationGetFieldDefaultAmount($('#infant_total_amount_pp').length ? $('#infant_total_amount_pp') : $('#infant_total_amount_pp_update'));
	var adultCount = parseInt($('#total_adult12').val(), 10) || parseInt($('#total_adult').val(), 10) || 0;
	var cwebCount = parseInt($('#children_with_bed12').val(), 10) || parseInt($('#children_with_bed').val(), 10) || 0;
	var cwnbCount = parseInt($('#children_without_bed12').val(), 10) || parseInt($('#children_without_bed').val(), 10) || 0;
	var infantCount = parseInt($('#total_infant12').val(), 10) || parseInt($('#total_infant').val(), 10) || 0;
	return (adult * adultCount) + (cweb * cwebCount) + (cwnb * cwnbCount) + (infant * infantCount);
}

function quotationRefreshCurrencyConversionPreview() {
	// Preview text removed — ROE applies only when writing tariff amounts.
}

/**
 * Load ROE factor for the selected currency.
 * - reconvertTariff:true → re-convert fields marked data-amount-source=tariff (manual untouched)
 * - rewriteFields:true → same as reconvertTariff (legacy alias)
 * - default / reconvertTariff:false → only refresh the ROE factor (initial page load)
 */
function quotationApplySelectedCurrencyToCostingFields(done, options) {
	options = options || {};
	var scope = options.scope || options.currencyScope || null;
	var reconvertTariff = options.reconvertTariff === true || options.rewriteFields === true;
	quotationRepairStaleZeroDefaultAmounts(scope);
	quotationSnapshotDefaultCostingAmounts(false, scope);
	var toCurrency = quotationGetSelectedCurrencyId();
	if (!toCurrency || toCurrency === '0') {
		window.quotationCurrencyDisplayFactor = 1;
		if (reconvertTariff) {
			quotationApplyCurrencyFactorToCostingFields(1, scope);
		}
		if (typeof done === 'function') {
			done(1);
		}
		return;
	}
	var base_url = $('#base_url').val() || '';
	$.post(base_url + 'view/package_booking/quotation/home/hotel/get_currency_conversion.php', {
		amount: 100,
		to_currency: toCurrency
	}, function (resp) {
		var data = resp;
		if (typeof resp === 'string') {
			try { data = JSON.parse(resp); } catch (e) { data = null; }
		}
		var factor = 1;
		if (data && data.factor != null) {
			factor = parseFloat(data.factor) || 1;
		}
		if (data && String(data.from_currency) === String(data.to_currency)) {
			factor = 1;
		}
		window.quotationCurrencyDisplayFactor = factor;
		if (reconvertTariff) {
			quotationApplyCurrencyFactorToCostingFields(factor, scope);
		}
		if (typeof done === 'function') {
			done(factor);
		}
	}).fail(function () {
		if (typeof done === 'function') {
			done(window.quotationCurrencyDisplayFactor || 1);
		}
	});
}

function quotationOnDefaultCostingAmountsWritten(options) {
	options = options || {};
	// Tariff writers store company amounts + convert with current factor.
	// Refresh ROE and re-apply so a late factor load still converts tariff fields.
	var scope = options.scope || options.currencyScope || null;
	window.quotationWritingDefaultCurrencyAmounts = true;
	try {
		quotationSnapshotDefaultCostingAmounts(true, scope);
	} finally {
		window.quotationWritingDefaultCurrencyAmounts = false;
	}
	quotationApplySelectedCurrencyToCostingFields(null, { scope: scope, reconvertTariff: true });
}

function quotationAfterCostingRecalc(options) {
	options = options || {};
	if (window.quotationApplyingCurrencyDisplay) {
		return;
	}
	if (options.fromDefaultCurrencyWrite) {
		quotationOnDefaultCostingAmountsWritten({
			scope: options.currencyScope || options.scope || 'pp'
		});
		return;
	}
	quotationCaptureDisplayAmountsAsDefault(options.currencyScope || options.scope || null);
}

function quotationSyncCurrencyDropdowns(sourceId, value) {
	var $all = $('#currency_code, #currency_code1').add($('[id^="currency_code_pp"]'));
	$all.each(function () {
		var $el = $(this);
		if (this.id === sourceId) {
			return;
		}
		if ($el.val() !== value) {
			$el.val(value);
			if ($el.data('select2')) {
				$el.trigger('change.select2');
			}
		}
	});
}

$(document).on('change', '#currency_code, #currency_code1, [id^="currency_code_pp"]', function () {
	var id = this.id;
	var val = $(this).val();
	quotationSyncCurrencyDropdowns(id, val);
	// Tariff-sourced amounts re-convert via ROE; manual/saved amounts stay as entered
	quotationApplySelectedCurrencyToCostingFields(function () {
		if (typeof quotationSaveTab4CostingState === 'function') {
			quotationSaveTab4CostingState();
		}
	}, { reconvertTariff: true });
});

$(document).on('change', 'input[id^="total_tour_cost"], input[id$="_total_amount_pp"], input[id$="_total_amount_pp_update"]', function () {
	if (window.quotationApplyingCurrencyDisplay) {
		return;
	}
	quotationCaptureDisplayAmountsAsDefault();
	quotationRefreshCurrencyConversionPreview();
});

$(document).on('change input', '#tbl_package_tour_quotation_dynamic_costing input, #flight_cost, #train_cost, #cruise_cost, #visa_cost, #guide_cost, #misc_cost, #flight_cost1, #train_cost1, #cruise_cost1, #visa_cost1, #guide_cost1, #misc_cost1, #quotation_pp_costing_container .costing-table input', function () {
	if (window.quotationApplyingCurrencyDisplay || window.quotationWritingDefaultCurrencyAmounts) {
		return;
	}
	var $el = $(this);
	if (!quotationIsCurrencyConvertibleField($el)) {
		return;
	}
	var display = parseFloat(String($el.val()).replace(/,/g, ''));
	if (isNaN(display)) {
		display = 0;
	}
	// Typed value is already in quotation currency — do not re-convert on currency change
	quotationMarkFieldAsManualAmount($el);
	$el.attr('data-default-amount', display);
});

function quotationInitFeatureEditors(root) {
	if (typeof $ === 'undefined' || typeof $.fn.wysiwyg !== 'function') {
		return;
	}
	var $root = root ? $(root) : $(document);
	$root.find('textarea.feature_editor').each(function () {
		var $el = $(this);
		if ($el.data('wysiwyg')) {
			return;
		}
		var existing = $el.val() || '';
		$el.wysiwyg({
			controls: 'bold,italic,|,undo,redo,image|h1,h2,h3,decreaseFontSize,highlight',
			initialContent: ''
		});
		if (existing && $el.data('wysiwyg')) {
			try {
				$el.wysiwyg('setContent', existing);
			} catch (e) {}
		}
	});
}

function quotationInclExclPlainLength(html) {
	return String(html || '')
		.replace(/<[^>]*>/g, ' ')
		.replace(/&nbsp;/gi, ' ')
		.replace(/\s+/g, ' ')
		.trim().length;
}

function getQuotationEditorContent(textareaId) {
	if (!textareaId) {
		return '';
	}
	var $target = $('#' + textareaId);
	if (!$target.length) {
		return '';
	}
	var html = '';
	try {
		var plugin = $target.data('wysiwyg');
		if (plugin) {
			if (typeof $target.wysiwyg === 'function') {
				$target.wysiwyg('save');
			}
			if (plugin.editorDoc && plugin.editorDoc.body) {
				html = plugin.editorDoc.body.innerHTML || '';
			}
			if (!html && typeof plugin.getContent === 'function') {
				html = plugin.getContent() || '';
			}
		}
	} catch (e) {}
	if (!html) {
		var iframe = document.getElementById(textareaId + '-wysiwyg-iframe');
		if (!iframe) {
			var $wrap = $target.prevAll('.wysiwyg').first().add($target.nextAll('.wysiwyg').first()).add($target.closest('.wysiwyg'));
			iframe = $wrap.find('iframe').get(0);
		}
		if (iframe && iframe.contentWindow && iframe.contentWindow.document && iframe.contentWindow.document.body) {
			html = iframe.contentWindow.document.body.innerHTML || '';
		}
	}
	if (!html) {
		html = $target.val() || '';
	}
	var plain = String(html)
		.replace(/<[^>]*>/g, ' ')
		.replace(/&nbsp;/gi, ' ')
		.replace(/\s+/g, ' ')
		.trim()
		.toLowerCase();
	if (plain === '' || plain === 'initial content') {
		return '';
	}
	try {
		$target.val(html);
	} catch (e2) {}
	return html;
}

function quotationCaptureInclExclFromEditors() {
	var pairs = [];
	var seen = {};
	function addPair(inclId, exclId) {
		if (!inclId || seen[inclId]) {
			return;
		}
		if (!$('#' + inclId).length && !(exclId && $('#' + exclId).length)) {
			return;
		}
		seen[inclId] = true;
		pairs.push([inclId, exclId || inclId.replace(/^inclusions/, 'exclusions')]);
	}
	$('input[name="custom_package"]:checked').each(function () {
		var pid = $(this).val();
		if (pid) {
			addPair('inclusions' + pid, 'exclusions' + pid);
		}
	});
	if (String($('#is_ai_quotation').val() || sessionStorage.getItem('is_ai_quotation') || '') === '1') {
		addPair('inclusions_ai', 'exclusions_ai');
	} else if ($('#inclusions_ai').length && pairs.length === 0) {
		addPair('inclusions_ai', 'exclusions_ai');
	}
	addPair('inclusions1', 'exclusions1');
	$('textarea[id^="inclusions"]').each(function () {
		if (/^inclusions(\d+|_ai)$/.test(this.id)) {
			addPair(this.id, this.id.replace(/^inclusions/, 'exclusions'));
		}
	});

	var best = { inclusions: '', exclusions: '' };
	for (var i = 0; i < pairs.length; i++) {
		var incl = getQuotationEditorContent(pairs[i][0]);
		var excl = getQuotationEditorContent(pairs[i][1]);
		if (quotationInclExclPlainLength(incl) + quotationInclExclPlainLength(excl) > 0) {
			best = { inclusions: incl, exclusions: excl };
			break;
		}
	}
	try {
		sessionStorage.setItem('quotation_incl_excl', JSON.stringify(best));
	} catch (e3) {}
	return best;
}

function getInclusionsExclusionsForQuotation() {
	var live = quotationCaptureInclExclFromEditors();
	if (quotationInclExclPlainLength(live.inclusions) + quotationInclExclPlainLength(live.exclusions) > 0) {
		return live;
	}
	try {
		var stored = JSON.parse(sessionStorage.getItem('quotation_incl_excl') || 'null');
		if (stored && (stored.inclusions || stored.exclusions)) {
			return stored;
		}
	} catch (e) {}
	return live;
}

function quotationGetTravelStartDatePart() {
	var dates = typeof quotationGetTravelDates === 'function' ? quotationGetTravelDates() : {};
	var fromDate = (dates && dates.from_date) || $('#from_date12').val() || $('#from_date').val() || '';
	return fromDate ? String(fromDate).split(' ')[0] : '';
}

function quotationFillNewExcursionRow(row) {
	if (!row) return;
	var $chk = $(row).find('input[type="checkbox"]').first();
	if ($chk.length) {
		$chk.prop('checked', true);
	}
	var $transfer = $(row).find('select[id^="transfer_option"]');
	if ($transfer.length) {
		if ($transfer.data('select2')) {
			$transfer.select2('destroy');
		}
		if (!$transfer.val()) {
			if ($transfer.find('option[value="Private Transfer"]').length) {
				$transfer.val('Private Transfer');
			} else if ($transfer.find('option').length) {
				$transfer.prop('selectedIndex', 0);
			}
		}
		$transfer.select2({ width: '150px', minimumResultsForSearch: 0 });
	}
	if (typeof quotationSyncExcursionPaxFromTab1 === 'function') {
		quotationSyncExcursionPaxFromTab1({ force: true });
	}
	var datePart = quotationGetTravelStartDatePart();
	if (datePart) {
		var $excDate = $(row).find('input[id^="exc_date"]');
		if ($excDate.length && !$excDate.val()) {
			$excDate.val(datePart + ' 00:00');
		}
	}
}

function quotationFillNewPlaneRow(row) {
	if (!row) return;
	var $chk = $(row).find('input[type="checkbox"]').first();
	if ($chk.length) {
		$chk.prop('checked', true);
	}
	var datePart = quotationGetTravelStartDatePart();
	if (!datePart) return;
	$(row).find('input[id^="txt_dapart"], input[id^="txt_arrval"]').each(function () {
		if (!this.value) {
			this.value = datePart + ' 00:00';
		}
	});
}

function quotationFillNewHotelRowPackageType(table, row) {
	if (!table || !row) return;
	var prevType = '';
	if (table.rows.length >= 2 && typeof quotationGetHotelRowPackageType === 'function') {
		prevType = quotationGetHotelRowPackageType(table.rows[table.rows.length - 2]);
	}
	if (!prevType) {
		prevType = typeof quotationGetDefaultPackageType === 'function' ? quotationGetDefaultPackageType() : 'ECONOMY';
	}
	if (typeof quotationInitEditablePackageTypeSelect === 'function') {
		quotationInitEditablePackageTypeSelect(row, prevType);
	}
	if (typeof quotationSetHotelRowPackageType === 'function') {
		quotationSetHotelRowPackageType(row, prevType);
	}
}

function quotationHideTravelStayRowCheckboxes() {
	if (document.getElementById('quotation-hide-row-checkboxes')) {
		return;
	}
	var css = document.createElement('style');
	css.id = 'quotation-hide-row-checkboxes';
	css.type = 'text/css';
	css.appendChild(document.createTextNode(
		'#tbl_package_tour_quotation_dynamic_hotel td:first-child,' +
		'#tbl_package_tour_quotation_dynamic_hotel_update td:first-child,' +
		'#tbl_package_tour_quotation_dynamic_plane td:first-child,' +
		'#tbl_package_tour_quotation_dynamic_plane_update td:first-child,' +
		'#tbl_package_tour_quotation_dynamic_excursion td:first-child,' +
		'#tbl_package_tour_quotation_dynamic_train td:first-child,' +
		'#tbl_dynamic_cruise_quotation td:first-child,' +
		'#tbl_package_tour_quotation_dynamic_transport td:first-child,' +
		'#tbl_package_tour_quotation_dynamic_transport_u td:first-child' +
		'{display:none !important;}'
	));
	document.head.appendChild(css);
	jQuery(
		'#tbl_package_tour_quotation_dynamic_hotel input[type="checkbox"],' +
		'#tbl_package_tour_quotation_dynamic_hotel_update input[type="checkbox"],' +
		'#tbl_package_tour_quotation_dynamic_plane input[type="checkbox"],' +
		'#tbl_package_tour_quotation_dynamic_excursion input[type="checkbox"],' +
		'#tbl_package_tour_quotation_dynamic_train input[type="checkbox"],' +
		'#tbl_dynamic_cruise_quotation input[type="checkbox"],' +
		'#tbl_package_tour_quotation_dynamic_transport input[type="checkbox"],' +
		'#tbl_package_tour_quotation_dynamic_transport_u input[type="checkbox"]'
	).prop('checked', true);
}

function quotationAfterDynamicRowAdd(tableID) {
	var table = document.getElementById(tableID);
	if (!table || !table.rows.length) {
		return;
	}
	var row = table.rows[table.rows.length - 1];
	$(row).find('input[type="checkbox"]').prop('checked', true);
	if (tableID === 'tbl_package_tour_quotation_dynamic_hotel' || tableID === 'tbl_package_tour_quotation_dynamic_hotel_update') {
		quotationFillNewHotelRowPackageType(table, row);
		setTimeout(function () {
			quotationFillNewHotelRowPackageType(table, row);
		}, 50);
	}
	if (tableID === 'tbl_package_tour_quotation_dynamic_excursion') {
		quotationFillNewExcursionRow(row);
		setTimeout(function () {
			quotationFillNewExcursionRow(row);
		}, 50);
	}
	if (tableID === 'tbl_package_tour_quotation_dynamic_plane' || tableID === 'tbl_package_tour_quotation_dynamic_plane_update') {
		quotationFillNewPlaneRow(row);
		if (typeof initPackageQuotationDateTimePicker === 'function') {
			initPackageQuotationDateTimePicker(row);
		}
		setTimeout(function () {
			quotationFillNewPlaneRow(row);
		}, 50);
	}
}

(function quotationHookAddRow() {
	function install() {
		if (typeof window.addRow !== 'function' || window.addRow._quotationHooked) {
			return;
		}
		var nativeAddRow = window.addRow;
		var hooked = function (tableID) {
			nativeAddRow.apply(this, arguments);
			if (typeof quotationAfterDynamicRowAdd === 'function') {
				quotationAfterDynamicRowAdd(tableID);
			}
		};
		hooked._quotationHooked = true;
		window.addRow = hooked;
	}
	install();
	if (typeof jQuery !== 'undefined') {
		jQuery(function () {
			install();
			quotationHideTravelStayRowCheckboxes();
		});
	}
})();


