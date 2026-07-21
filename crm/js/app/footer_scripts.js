$(function () {
	$('form').attr('autocomplete', 'off');
	$('input').attr('autocomplete', 'off');
});

$(function () {
	$('.feature_editor').wysiwyg({
		controls: 'bold,italic,|,undo,redo,image|h1,h2,h3,decreaseFontSize,highlight',
		initialContent: ''
	});
});

//**Sidebar Scroll
(function ($) {
	$(window).on('load', function () {
		$('.sidebar_wrap').mCustomScrollbar();
	});
})(jQuery);

//**Site Tooltips
$(function () {
	$("[data-toggle='tooltip']").tooltip({ placement: 'bottom' });
	$("[data-toggle='tooltip']").click(function () {
		$('.tooltip').remove();
	});
});

//**Smart Dropdown Positioning
// Automatically position dropdowns above when near bottom of viewport
function adjustDropdownPosition() {
    $('.btn-group, .dropdown').each(function() {
        var $btnGroup = $(this);
        var $dropdown = $btnGroup.find('.dropdown-menu');
        
        if ($dropdown.length === 0) return;
        
        // Skip if dropdown is not visible or about to be shown
        if (!$btnGroup.hasClass('open') && !$btnGroup.hasClass('show')) return;
        
        // Reset classes
        $btnGroup.removeClass('dropup');
        
        // Get button position and viewport height
        var buttonOffset = $btnGroup.offset();
        if (!buttonOffset) return; // Element not visible
        
        var buttonHeight = $btnGroup.outerHeight();
        var dropdownHeight = $dropdown.outerHeight() || 200; // Estimate if not visible
        var viewportHeight = $(window).height();
        var scrollTop = $(window).scrollTop();
        
        // Calculate space below and above
        var spaceBelow = viewportHeight - (buttonOffset.top - scrollTop + buttonHeight);
        var spaceAbove = buttonOffset.top - scrollTop;
        
        // If not enough space below but enough space above, use dropup
        if (spaceBelow < dropdownHeight + 20 && spaceAbove > dropdownHeight + 20) {
            $btnGroup.addClass('dropup');
            console.log('Smart positioning: Dropdown positioned above for button at', buttonOffset.top);
        }
    });
}

// Apply smart positioning globally
$(document).ready(function() {
    // Adjust positioning when dropdown is about to be shown
    $(document).on('show.bs.dropdown', '.btn-group, .dropdown', function() {
        var $this = $(this);
        setTimeout(function() {
            adjustDropdownPosition();
        }, 10);
    });
    
    // Also adjust on window resize and scroll
    $(window).on('resize scroll', function() {
        if ($('.btn-group.open, .btn-group.show, .dropdown.open, .dropdown.show').length > 0) {
            adjustDropdownPosition();
        }
    });
});

// Add global CSS for smart dropdown positioning
if (!document.getElementById('smart-dropdown-css')) {
    var css = `
    <style id="smart-dropdown-css">
    /* Smart dropdown positioning */
    .btn-group .dropdown-menu {
        transition: all 0.2s ease;
    }
    
    /* Dropup positioning - show above when near bottom */
    .btn-group.dropup .dropdown-menu {
        top: auto !important;
        bottom: 100% !important;
        margin: 0 0 2px !important;
        box-shadow: 0 -6px 12px rgba(0,0,0,.175) !important;
    }
    
    /* Ensure dropdown arrow points in correct direction for dropup */
    .btn-group.dropup .dropdown-toggle::after {
        border-top: 0 !important;
        border-bottom: 4px solid !important;
        border-right: 4px solid transparent !important;
        border-left: 4px solid transparent !important;
    }
    </style>
    `;
    $('head').append(css);
}

$(function () {
	$('input[type="text"], input[type="number"], select, textarea').addClass('form-control');
	$('.no_form_control').removeClass('form-control');
});

//* round off values function *//

function round_off_value(amount) {
	var amount1 = parseFloat(amount).toFixed(2);
	return amount1;
}

//**Message alert

function msg_alert(message) {
	var msg = message.split('--');
	if (msg[0] == 'error') {
		error_msg_alert(msg[1]);
	}
	else {
		success_msg_alert(message);
	}
}
//branch reflect
function emp_branch_reflect() {
	var base_url = $('#base_url').val();
	var emp_id = $('#booker_id_filter').val();

	$.post(base_url + 'view/load_data/branch_reflect.php', { emp_id: emp_id }, function (data) {
		$('#branch_id_filter').html(data);
	});
}

//Customer branch reflect
function cust_branch_reflect() {
	var base_url = $('#base_url').val();
	var cust_id = $('#customer_filter').val();

	$.post(base_url + 'view/load_data/cust_branch_reflect.php', { cust_id: cust_id }, function (data) {
		$('#branch_id_filter').html(data);
	});
}
//**Error Message Alert

function error_msg_alert(message, delay = '6000') {
	$('#site_alert').empty(); // to only display one error message
	$('#site_alert').vialert({ type: 'error', title: 'Error', message: message, delay: delay });
}

//**Success Message Alert

function success_msg_alert(message) {
	$('#site_alert').empty(); // to only display one success message
	$('#site_alert').vialert({ message: message });
}

//**Message popup reload
function msg_popup_reload(message) {
	var msg = message.split('--');

	if (msg[0] == 'error') {
		error_msg_alert(msg[1]);
	}
	else {
		$('#vi_confirm_box').vi_confirm_box({
			false_btn: false,
			message: message,
			true_btn_text: 'Ok',
			callback: function (data1) {
				if (data1 == 'yes') {
					document.location.reload();
				}
			}
		});
	}
}

//**Reset Form
function reset_form(form_id) {
	$('#' + form_id).find('input[type="text"]').each(function () {
		$(this).val('');
	});

	$('#' + form_id).find('textarea').each(function () {
		$(this).val('');
	});

	$('#' + form_id).find('select').each(function () {
		$(this).prop('selected', function () {
			return this.defaultSelected;
		});
	});
	document.getElementById(form_id).reset();
	$("select").closest("form").on("reset", function (ev) { // for resetting Select2
		var targetJQForm = $(ev.target);
		setTimeout((function () {
			this.find("select").trigger("change");
		}).bind(targetJQForm), 0);
	});
	document.getElementById(form_id).reset();
	$('#basic_show').html('&nbsp;'); $('#basic_show1').html('&nbsp;');
	$('#service_show').html('&nbsp;'); $('#service_show1').html('&nbsp;');
	$('#markup_show').html('&nbsp;'); $('#markup_show1').html('&nbsp;');
	$('#discount_show').html('&nbsp;'); $('#discount_show1').html('&nbsp;');
}

//**Element count in array

function isInArray(value, array1) {
	for (var arr_count = 0; arr_count < array1.length; arr_count++) {
		if (array1[arr_count] == value) {
			return false;
		}
	}
	return true;
}

//**Generic Tooltip
/*$(function() {
	$('input, select, textarea, span, a').tooltip({placement: 'bottom'});
});*/
$(function () {
	$('input,textarea,span, a').tooltip({ placement: 'bottom' });
	$('input,textarea,span, a').focus(function () {
		$('input,textarea,span, a').tooltip('hide');
	});
});

//**Radio button and checkboxes
$(document).ready(function () {
	$("input[type='radio'], input[type='checkbox']").labelauty({ label: false, maximum_width: '20px' });
});

//**Dual button
$(function () {
	$('.app_dual_button input[type="checkbox"], .app_dual_button input[type="radio"]').change(function () {
		$(this).parent().siblings().removeClass('active');

		$(this).parent().addClass('active');
	});
});

//**First letter capital event start**//
$(function () {
	var exception_fields_arr = [
		'app_website',
		'sms_username',
		'sms_password',
		'server_username',
		'txt_username',
		'app_smtp_host',
		'app_smtp_port',
		'app_smtp_password',
		'app_smtp_method',
		'airport_code1',
		'check_in',
		'check_out',
		'check_in1',
		'check_out1',
		're_password',
		'new_password',
		'current_password',
		'app_name',
		'bank_name',
		'bank_ifsc_code',
		'bank_swift_code',
		'package_name',
		'package_code',
		'package_name1',
		'package_code1',
		'corpo_company_name'
	];

	$('input[type="text"]').change(function () {
		var str_arr = $(this).val();
		var id = $(this).attr('id');

		if (jQuery.inArray(id, exception_fields_arr) == -1) {
			// if (!id.includes('email')) {
			// 	$(this).val( toTitleCase(str_arr) );
			// }
		}
	});
});

//**First letter capital event end**//
function toTitleCase(str) {
	return str.replace(/\w\S*/g, function (txt) {
		return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
	});
}

//**App Base URL start**//
function base_url() {
	var base_url = $('#base_url').val();
	return base_url;
}

$.validator.addMethod('regex', function (value, element, param) {
	return this.optional(element) || param.test(value);
});

//**Bank List reflect autocomplete start**//
function bank_list_reflect() {
	var base_url = $('#base_url').val();

	$.post(base_url + 'view/load_data/bank_list_json_response.php', {}, function (data) {
		var data = jQuery.parseJSON(data);
		bank_name_autocomplete(data);
	});
}
bank_list_reflect();
function bank_name_autocomplete(data) {
	$('.bank_suggest').each(function () {
		$(this).autocomplete({ source: data });
	});
}
//**Bank List reflect autocomplete end**//

//**Route List reflect autocomplete start**//
function route_list_reflect() {
	var base_url = $('#base_url').val();

	$.post(base_url + 'view/load_data/route_list_json_response.php', {}, function (data) {
		var data = jQuery.parseJSON(data);
		route_name_autocomplete(data);
	});
}
route_list_reflect();
function route_name_autocomplete(data) {
	$('.route_suggest').each(function () {
		$(this).autocomplete({ source: data });
	});
}
//**Route List reflect autocomplete end**//


function today_date(id) {
	var today = new Date();
	var todaydate =
		String(today.getDate()).padStart(2, '0') +
		'-' +
		String(today.getMonth() + 1).padStart(2, '0') +
		'-' +
		today.getFullYear();
	$('#' + id).val(todaydate);
}
today_date('as_of_date');
//**Calculate age generic start**//
function calculate_age_generic(from, to) {
	var dateString1 = $('#' + from).val();
	var get_new = dateString1.split('-');

	var day = get_new[0];

	var month = get_new[1];

	var year = get_new[2];

	var dateString = month + '/' + day + '/' + year;

	var get_new = dateString1.split('-');

	var day = get_new[0];

	var month = get_new[1];

	var year = get_new[2];

	var dateString = month + '/' + day + '/' + year;

	tagText = dateString.replace(/-/g, '/');

	var today = new Date();

	var birthDate = new Date(tagText);

	var age = today.getFullYear() - birthDate.getFullYear();

	var m = today.getMonth() - birthDate.getMonth();

	if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
		age--;
	}

	$('#' + to).val(age);
}

//**Calculate age generic start**//

//**Generic Customer save start**//

function customer_save_modal(client_modal_type = 'other') {
	
	$('#customer_save_modal_add_btn-').prop('disabled',true);
	var base_url = $('#base_url').val();

	$('#customer_save_modal_add_btn').button('loading');
	$.post(base_url + 'view/customer_master/save_modal.php', { client_modal_type: client_modal_type }, function (data) {
		$('#div_customer_save_modal').html(data);
		$('#customer_save_modal_add_btn').button('reset');
		$('#customer_save_modal_add_btn-').prop('disabled',false);
	});
}

function customer_dropdown_reload(cust_id = '') {
	var base_url = $('#base_url').val();

	$('.customer_dropdown').each(function () {
		var cur_ele = $(this);

		$.post(base_url + 'view/customer_master/customer_dropdown_load.php', {}, function (data) {
			$(cur_ele).select2();

			$(cur_ele).css('width', '100%');

			$(cur_ele).html(data);

			if (cust_id != '') {
				$(cur_ele).val(cust_id);
			}

			$(cur_ele).trigger('change');
		});
	});
}

function hotel_save_modal() {
	var base_url = $('#base_url').val();
	var target = '_blank';
	window.open(base_url + 'view/hotels/master/index.php', target);
}
function city_ssave_modal() {
	var base_url = $('#base_url').val();
	var target = '_blank';
	window.open(base_url + 'view/other_masters/index.php', target);
}
function airport_airline_save_modal() {
	var base_url = $('#base_url').val();
	var target = '_blank';
	window.open(base_url + 'view/other_masters/index.php', target);
}
function activity_save_modal(){
	
	var base_url = $('#base_url').val();
	var target = '_blank';
	window.open(base_url + 'view/b2b_excursion/index.php', target);
}
function supplier_save_modal(type){
	
	var base_url = $('#base_url').val();
	var target = '_blank';
	if(type=='other'){
		var link = 'other_vendor/index.php';
	}
	window.open(base_url + 'view/'+link, target);
}
function hotel_dropdown_reload(hotel_id = '') {
	var base_url = $('#base_url').val();

	$('.hotel_dropdown').each(function () {
		var cur_ele = $(this);

		$.post(base_url + 'view/hotels/master/hotel/hotel_dropdown_load.php', {}, function (data) {
			$(cur_ele).select2();

			$(cur_ele).css('width', '100%');

			$(cur_ele).html(data);

			if (hotel_id != '') {
				$(cur_ele).val(hotel_id);
			}

			$(cur_ele).trigger('change');
		});
	});
}


function corporate_fields_reflect(type='') {
	var base_url = $('#base_url').val();

    if(type!='update') {
        var corporate_fields = 'corporate_fields';
        var cust_type_id = 'cust_type';
		var customer_id = '';
    }else{
        var cust_type_id = 'cust_type1';
        var corporate_fields = 'corporate_fields1';
		var customer_id = $('#customer_id_u').val();
    }
	var cust_type = $('#'+cust_type_id).val();


	$.post(
		base_url + 'view/customer_master/corporate_fields_reflect.php',
		{ cust_type: cust_type, customer_id: customer_id },
		function (data) {
			$('#'+corporate_fields).html(data);
		}
	);
}

//**Common inline city save (use initCityAddNewInline on any lazy city Select2)**//

function validateCityNameText(cityName) {
	cityName = (cityName || '').trim();
	if (!cityName.replace(/\s/g, '').length) {
		error_msg_alert('It Should not allow spaces.');
		return false;
	}
	if (!/^[a-zA-Z\s ]+$/.test(cityName)) {
		error_msg_alert('Please enter valid city');
		return false;
	}
	return true;
}

function getSelect2SearchTerm($select) {
	var $selectEl = $($select);
	var cached = $selectEl.data('cityInlineSearchTerm');
	if (cached) {
		return (cached + '').trim();
	}

	var term = '';
	var s2 = $selectEl.data('select2');
	if (s2) {
		if (s2.dropdown && s2.dropdown.$search) {
			term = s2.dropdown.$search.val();
		}
		if (!term && s2.$dropdown) {
			term = s2.$dropdown.find('.select2-search__field').val();
		}
	}
	if (!term) {
		var $container = $selectEl.next('.select2-container');
		if (!$container.length) {
			$container = $selectEl.parent().find('.select2-container').first();
		}
		term = $container.find('.select2-search__field').val();
	}
	if (!term) {
		var $openSearch = $('.select2-container--open .select2-search__field');
		if ($openSearch.length === 1) {
			term = $openSearch.val();
		}
	}
	return (term || '').trim();
}

function cacheCityInlineSearchTerm($select, term) {
	$($select).data('cityInlineSearchTerm', (term || '').trim());
}

function selectCityInLazyDropdown($select, cityId, cityName, options) {
	options = options || {};
	if (!$select.length || cityId === '' || cityId === null || typeof cityId === 'undefined') {
		return;
	}
	var cityIdStr = String(cityId);
	if ($select.find('option[value="' + cityIdStr + '"]').length === 0) {
		$select.append($('<option></option>').attr('value', cityIdStr).text(cityName || ''));
	}
	$select.val(cityIdStr);
	if (options.triggerChange !== false) {
		$select.trigger('change');
	} else if ($select.data('select2')) {
		$select.trigger('change.select2');
	}
}

/** Force Select2 placeholder text when empty (ajax city dropdowns in modals). */
function applyLazyCityPlaceholder($select, placeholder) {
	placeholder = placeholder || 'Select City';
	$select = $($select);
	if (!$select.length || !$select.data('select2')) {
		return;
	}
	$select.val(null);
	var s2 = $select.data('select2');
	s2.$container.find('.select2-selection__clear').remove();
	var $rendered = s2.$container.find('.select2-selection__rendered');
	$rendered.empty().removeAttr('title');
	$rendered.addClass('select2-selection__placeholder').text(placeholder);
}

/** Reset lazy-loaded city Select2 to show placeholder (quick-save modals). */
function resetLazyCitySelect(selector, placeholder) {
	placeholder = placeholder || 'Select City';
	var $select = $(selector);
	if (!$select.length) {
		return;
	}
	if ($select.data('select2')) {
		$select.empty();
		applyLazyCityPlaceholder($select, placeholder);
	} else {
		$select.html('').val('');
	}
}

/** Refresh city placeholder after modal is visible (Select2 skips it while hidden). */
function bindQuickModalCityPlaceholderRefresh($modal, selectSelector, placeholder) {
	placeholder = placeholder || 'Select City';
	$modal.off('shown.bs.modal.lazyCity').on('shown.bs.modal.lazyCity', function () {
		var $select = $(selectSelector);
		if ($select.length && !$select.val()) {
			applyLazyCityPlaceholder($select, placeholder);
		}
	});
}

/** Init lazy city dropdown inside a modal with consistent placeholder. */
function initQuickModalCitySelect(selector, placeholder) {
	placeholder = placeholder || 'Select City';
	var $select = $(selector);
	if (!$select.length) {
		return;
	}
	if ($select.data('select2')) {
		$select.select2('destroy');
	}
	$select.empty();

	var base_url = $('#base_url').val();
	var url = base_url + '/view/load_data/generic_city_loading.php';
	var $modal = $select.closest('.modal');
	var config = {
		placeholder: placeholder,
		width: '100%',
		allowClear: true,
		ajax: {
			url: url,
			dataType: 'json',
			type: 'GET',
			data: function (params) {
				return { term: params.term, page: params.page || 0, valueasText: false };
			},
			processResults: function (data) {
				return {
					results: data.results,
					pagination: {
						more: data.pagination.more
					}
				};
			}
		}
	};
	if ($modal.length) {
		config.dropdownParent = $modal;
	}
	$select.select2(config);
	applyLazyCityPlaceholder($select, placeholder);
	$select.attr('data-add-new-option', 'true');
	initCityAddNewInline($select);
}

/**
 * Save a single city via AJAX. Returns jQuery promise.
 * options: { activeFlag, silent, onSuccess(cityId, cityName, existing), onError(msg) }
 */
function saveCityMaster(cityName, options) {
	options = options || {};
	cityName = (cityName || '').trim();

	if (!validateCityNameText(cityName)) {
		if (options.onError) {
			options.onError('Invalid city name');
		}
		return $.Deferred().reject().promise();
	}

	var base_url = $('#base_url').val();

	return $.post(
		base_url + 'controller/group_tour/tour_cities/city_quick_save_c.php',
		{ city_name: cityName, active_flag: options.activeFlag || 'Active' },
		function (data) {
			var res;
			try {
				res = typeof data === 'object' ? data : JSON.parse(data);
			} catch (e) {
				if (options.onError) {
					options.onError('Unexpected response from server.');
				} else {
					error_msg_alert('Unexpected response from server.');
				}
				return;
			}

			if (res.status === 'error') {
				if (options.onError) {
					options.onError(res.message);
				} else {
					error_msg_alert(res.message);
				}
				return;
			}

			if (!options.silent && !res.existing) {
				success_msg_alert('City has been successfully saved.');
			}

			if (typeof update_b2c_cache === 'function') {
				update_b2c_cache();
			}

			if (options.onSuccess) {
				options.onSuccess(res.city_id, res.city_name, res.existing);
			}
		}
	).fail(function () {
		if (options.onError) {
			options.onError('Failed to save city. Please try again.');
		} else {
			error_msg_alert('Failed to save city. Please try again.');
		}
	});
}

/**
 * Bind Select2 "+ Add New" to saveCityMaster for lazy-loaded city dropdowns.
 * Call after city_lzloading(selector).
 */
function initCityAddNewInline(selector, options) {
	options = options || {};
	var $select = $(selector);
	if (!$select.length) {
		return;
	}

	var selectKey = $select.attr('id') || ('city_sel_' + Math.random().toString(36).slice(2));
	var ns = 'cityInlineSave_' + selectKey;

	$select.attr('data-add-new-option', 'true');

	function handleCityAddNew(presetTerm) {
		var typedCity = (presetTerm || getSelect2SearchTerm($select) || '').trim();
		if (!typedCity) {
			error_msg_alert('Type a city name in the search box, then click Add New.');
			return false;
		}

		if ($select.data('select2')) {
			$select.select2('close');
		}

		saveCityMaster(typedCity, {
			activeFlag: options.activeFlag || 'Active',
			silent: options.silent,
			onSuccess: function (cityId, cityNameSaved, existing) {
				cacheCityInlineSearchTerm($select, '');
				selectCityInLazyDropdown($select, cityId, cityNameSaved);
				if (options.onSaved) {
					options.onSaved(cityId, cityNameSaved, existing);
				}
			},
			onError: options.onError
		});
		return false;
	}

	function getSelect2ResultsEl() {
		var s2 = $select.data('select2');
		if (s2 && s2.$dropdown) {
			return s2.$dropdown.find('.select2-results');
		}
		return $select.next('.select2-container--open').find('.select2-results');
	}

	function appendAndBindAddNew() {
		var $results = getSelect2ResultsEl();
		if (!$results.length) {
			return;
		}

		var $btn = $results.children('.add-new-option');
		if (!$btn.length) {
			$btn = $('<div class="add-new-option" role="button" tabindex="0"></div>')
				.css({
					padding: '8px',
					cursor: 'pointer',
					background: '#f2f2f2',
					borderTop: '1px solid #ddd',
					position: 'relative',
					zIndex: 10
				})
				.html('<span style="color:#676bae;">+ Add New</span>');
			$results.append($btn);
		}

		$btn.off('mousedown.' + ns).on('mousedown.' + ns, function (e) {
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			var typedCity = getSelect2SearchTerm($select);
			handleCityAddNew(typedCity);
			return false;
		});
	}

	$select
		.off('select2:open.' + ns + ' select2:results:all.' + ns + ' select2:add_new.' + ns)
		.on('select2:open.' + ns, function () {
			cacheCityInlineSearchTerm($select, '');
			setTimeout(appendAndBindAddNew, 0);
			setTimeout(appendAndBindAddNew, 80);
		})
		.on('select2:results:all.' + ns, function () {
			setTimeout(appendAndBindAddNew, 0);
		})
		.on('select2:add_new.' + ns, function () {
			handleCityAddNew();
		});

	$(document)
		.off('mousedown.' + ns, '.add-new-option')
		.on('mousedown.' + ns, '.add-new-option', function (e) {
			var s2 = $select.data('select2');
			if (!s2 || !s2.isOpen() || !s2.$dropdown || !s2.$dropdown[0].contains(e.currentTarget)) {
				return;
			}
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			var typedCity = getSelect2SearchTerm($select);
			handleCityAddNew(typedCity);
			return false;
		});

	$(document)
		.off('input.' + ns + ' keyup.' + ns)
		.on('input.' + ns + ' keyup.' + ns, '.select2-container--open .select2-search__field', function () {
			var s2 = $select.data('select2');
			if (!s2 || !s2.isOpen()) {
				return;
			}
			if (s2.dropdown && s2.dropdown.$search && s2.dropdown.$search[0] !== this) {
				return;
			}
			cacheCityInlineSearchTerm($select, $(this).val());
			setTimeout(appendAndBindAddNew, 0);
		});
}

//**Common inline airline save (use initAirlineAddNewInline on airline Select2 dropdowns)**//

function isAirlineMasterSelect(el) {
	var id = (el && el.id) ? el.id : '';
	return (
		id.indexOf('airline_name') === 0 ||
		id.indexOf('airlines_name') === 0 ||
		id.indexOf('txt_plane_company') === 0
	);
}

function getAirlineSelectValueMode($select) {
	var id = ($select.attr('id') || '');
	return id.indexOf('airlines_name') === 0 ? 'label' : 'id';
}

function buildAirlineLabel(airlineName, airlineCode) {
	return (airlineName || '') + ' (' + (airlineCode || '') + ')';
}

function cacheAirlineInlineSearchTerm($select, term) {
	$($select).data('airlineInlineSearchTerm', (term || '').trim());
}

function getAirlineInlineSearchTerm($select) {
	var $selectEl = $($select);
	var cached = $selectEl.data('airlineInlineSearchTerm');
	if (cached) {
		return (cached + '').trim();
	}
	return getSelect2SearchTerm($selectEl);
}

function selectAirlineInDropdown($select, airlineId, label, options) {
	options = options || {};
	if (!$select.length || airlineId === '' || airlineId === null || typeof airlineId === 'undefined') {
		return;
	}

	var valueMode = options.valueMode || getAirlineSelectValueMode($select);
	var airlineIdStr = String(airlineId);
	var optionLabel = label || buildAirlineLabel('', airlineIdStr);
	var optionValue = valueMode === 'label' ? optionLabel : airlineIdStr;

	if (!$select.find('option').filter(function () { return this.value === optionValue; }).length) {
		$select.append($('<option></option>').attr('value', optionValue).text(optionLabel));
	}

	$select.val(optionValue);
	if (options.triggerChange !== false) {
		$select.trigger('change');
	} else if ($select.data('select2')) {
		$select.trigger('change.select2');
	}
}

/**
 * Save a single airline via AJAX. Returns jQuery promise.
 * options: { activeFlag, silent, onSuccess(airlineId, airlineName, airlineCode, label, existing), onError(msg) }
 */
function saveAirlineMaster(airlineInput, options) {
	options = options || {};
	airlineInput = (airlineInput || '').trim();

	if (!airlineInput.replace(/\s/g, '').length) {
		if (options.onError) {
			options.onError('Airline name is required.');
		} else {
			error_msg_alert('Airline name is required.');
		}
		return $.Deferred().reject().promise();
	}

	var base_url = $('#base_url').val();

	return $.post(
		base_url + 'controller/other_masters/airlines/airline_quick_save_c.php',
		{ airline_input: airlineInput, active_flag: options.activeFlag || 'Active' },
		function (data) {
			var res;
			try {
				res = typeof data === 'object' ? data : JSON.parse(data);
			} catch (e) {
				if (options.onError) {
					options.onError('Unexpected response from server.');
				} else {
					error_msg_alert('Unexpected response from server.');
				}
				return;
			}

			if (res.status === 'error') {
				if (options.onError) {
					options.onError(res.message);
				} else {
					error_msg_alert(res.message);
				}
				return;
			}

			if (!options.silent && !res.existing) {
				success_msg_alert('Airline has been successfully saved.');
			}

			if (typeof update_b2c_cache === 'function') {
				update_b2c_cache();
			}

			if (options.onSuccess) {
				options.onSuccess(res.airline_id, res.airline_name, res.airline_code, res.label, res.existing);
			}
		}
	).fail(function () {
		if (options.onError) {
			options.onError('Failed to save airline. Please try again.');
		} else {
			error_msg_alert('Failed to save airline. Please try again.');
		}
	});
}

/**
 * Bind Select2 "+ Add New" to saveAirlineMaster for airline dropdowns.
 */
function initAirlineAddNewInline(selector, options) {
	options = options || {};
	var $select = $(selector);
	if (!$select.length || $select.data('airlineInlineSaveBound')) {
		return;
	}

	$select.data('airlineInlineSaveBound', true);

	var selectKey = $select.attr('id') || ('airline_sel_' + Math.random().toString(36).slice(2));
	var ns = 'airlineInlineSave_' + selectKey;

	$select.attr('data-add-new-option', 'true');

	function handleAirlineAddNew(presetTerm) {
		var typedAirline = (presetTerm || getAirlineInlineSearchTerm($select) || '').trim();
		if (!typedAirline) {
			error_msg_alert('Type an airline name in the search box, then click Add New.');
			return false;
		}

		if ($select.data('select2')) {
			$select.select2('close');
		}

		saveAirlineMaster(typedAirline, {
			activeFlag: options.activeFlag || 'Active',
			silent: options.silent,
			onSuccess: function (airlineId, airlineName, airlineCode, label, existing) {
				cacheAirlineInlineSearchTerm($select, '');
				selectAirlineInDropdown($select, airlineId, label, {
					valueMode: getAirlineSelectValueMode($select)
				});
				if (options.onSaved) {
					options.onSaved(airlineId, airlineName, airlineCode, label, existing);
				}
			},
			onError: options.onError
		});
		return false;
	}

	function getSelect2ResultsEl() {
		var s2 = $select.data('select2');
		if (s2 && s2.$dropdown) {
			return s2.$dropdown.find('.select2-results');
		}
		return $select.next('.select2-container--open').find('.select2-results');
	}

	function appendAndBindAddNew() {
		var $results = getSelect2ResultsEl();
		if (!$results.length) {
			return;
		}

		var $btn = $results.children('.add-new-option');
		if (!$btn.length) {
			$btn = $('<div class="add-new-option" role="button" tabindex="0"></div>')
				.css({
					padding: '8px',
					cursor: 'pointer',
					background: '#f2f2f2',
					borderTop: '1px solid #ddd',
					position: 'relative',
					zIndex: 10
				})
				.html('<span style="color:#676bae;">+ Add New</span>');
			$results.append($btn);
		}

		$btn.off('mousedown.' + ns).on('mousedown.' + ns, function (e) {
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			handleAirlineAddNew(getAirlineInlineSearchTerm($select));
			return false;
		});
	}

	$select
		.off('select2:open.' + ns + ' select2:results:all.' + ns + ' select2:add_new.' + ns)
		.on('select2:open.' + ns, function () {
			cacheAirlineInlineSearchTerm($select, '');
			setTimeout(appendAndBindAddNew, 0);
			setTimeout(appendAndBindAddNew, 80);
		})
		.on('select2:results:all.' + ns, function () {
			setTimeout(appendAndBindAddNew, 0);
		})
		.on('select2:add_new.' + ns, function () {
			handleAirlineAddNew();
		});

	$(document)
		.off('mousedown.' + ns, '.add-new-option')
		.on('mousedown.' + ns, '.add-new-option', function (e) {
			var s2 = $select.data('select2');
			if (!s2 || !s2.isOpen() || !s2.$dropdown || !s2.$dropdown[0].contains(e.currentTarget)) {
				return;
			}
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			handleAirlineAddNew(getAirlineInlineSearchTerm($select));
			return false;
		});

	$(document)
		.off('input.' + ns + ' keyup.' + ns)
		.on('input.' + ns + ' keyup.' + ns, '.select2-container--open .select2-search__field', function () {
			var s2 = $select.data('select2');
			if (!s2 || !s2.isOpen()) {
				return;
			}
			if (s2.dropdown && s2.dropdown.$search && s2.dropdown.$search[0] !== this) {
				return;
			}
			cacheAirlineInlineSearchTerm($select, $(this).val());
			setTimeout(appendAndBindAddNew, 0);
		});
}

function initAllAirlineSelectAddNew(container) {
	var $scope = container ? $(container) : $(document);
	$scope.find('select[data-add-new-option="true"]').filter(function () {
		return isAirlineMasterSelect(this);
	}).each(function () {
		initAirlineAddNewInline(this);
	});
}

window.initAirlineAddNewInline = initAirlineAddNewInline;
window.initAllAirlineSelectAddNew = initAllAirlineSelectAddNew;
window.saveAirlineMaster = saveAirlineMaster;

//**Common inline destination save (use initDestinationAddNewInline on destination Select2)**//

function isDestinationMasterSelect(el) {
	var id = (el && el.id) ? el.id : '';
	if (!id) {
		return false;
	}
	if (id === 'dest_name' || id === 'dest_name_s') {
		return true;
	}
	if (/^dest_name-\d/.test(id)) {
		return true;
	}
	if (/^dest_name\d+$/.test(id)) {
		return true;
	}
	return false;
}

function cacheDestinationInlineSearchTerm($select, term) {
	$($select).data('destinationInlineSearchTerm', (term || '').trim());
}

function getDestinationInlineSearchTerm($select) {
	var $selectEl = $($select);
	var cached = $selectEl.data('destinationInlineSearchTerm');
	if (cached) {
		return (cached + '').trim();
	}
	return getSelect2SearchTerm($selectEl);
}

function selectDestinationInDropdown($select, destId, destName, options) {
	options = options || {};
	if (!$select.length || destId === '' || destId === null || typeof destId === 'undefined') {
		return;
	}
	var destIdStr = String(destId);
	var optionLabel = destName || '';
	if (!$select.find('option').filter(function () { return this.value === destIdStr; }).length) {
		$select.append($('<option></option>').attr('value', destIdStr).text(optionLabel));
	}
	$select.val(destIdStr);
	if (options.triggerChange !== false) {
		$select.trigger('change');
	} else if ($select.data('select2')) {
		$select.trigger('change.select2');
	}
}

function saveDestinationMaster(destName, options) {
	options = options || {};
	destName = (destName || '').trim();

	if (!destName.replace(/\s/g, '').length) {
		if (options.onError) {
			options.onError('Destination name is required.');
		} else {
			error_msg_alert('Destination name is required.');
		}
		return $.Deferred().reject().promise();
	}

	var base_url = $('#base_url').val();

	return $.post(
		base_url + 'controller/other_masters/destination/destination_quick_save_c.php',
		{ dest_name: destName, status: options.status || 'Active' },
		function (data) {
			var res;
			try {
				res = typeof data === 'object' ? data : JSON.parse(data);
			} catch (e) {
				if (options.onError) {
					options.onError('Unexpected response from server.');
				} else {
					error_msg_alert('Unexpected response from server.');
				}
				return;
			}

			if (res.status === 'error') {
				if (options.onError) {
					options.onError(res.message);
				} else {
					error_msg_alert(res.message);
				}
				return;
			}

			if (!options.silent && !res.existing) {
				success_msg_alert('Destination has been successfully saved.');
			}

			if (typeof update_b2c_cache === 'function') {
				update_b2c_cache();
			}

			if (options.onSuccess) {
				options.onSuccess(res.dest_id, res.dest_name, res.existing);
			}
		}
	).fail(function () {
		if (options.onError) {
			options.onError('Failed to save destination. Please try again.');
		} else {
			error_msg_alert('Failed to save destination. Please try again.');
		}
	});
}

function initDestinationAddNewInline(selector, options) {
	options = options || {};
	var $select = $(selector);
	if (!$select.length || !isDestinationMasterSelect($select[0])) {
		return;
	}
	if ($select.data('destinationInlineSaveBound')) {
		return;
	}
	$select.data('destinationInlineSaveBound', true);

	var selectKey = $select.attr('id') || ('dest_sel_' + Math.random().toString(36).slice(2));
	var ns = 'destinationInlineSave_' + selectKey;

	$select.attr('data-add-new-option', 'true');

	function handleDestinationAddNew(presetTerm) {
		var typedDest = (presetTerm || getDestinationInlineSearchTerm($select) || '').trim();
		if (!typedDest) {
			error_msg_alert('Type a destination name in the search box, then click Add New.');
			return false;
		}

		if ($select.data('select2')) {
			$select.select2('close');
		}

		saveDestinationMaster(typedDest, {
			status: options.status || 'Active',
			silent: options.silent,
			onSuccess: function (destId, destNameSaved, existing) {
				cacheDestinationInlineSearchTerm($select, '');
				selectDestinationInDropdown($select, destId, destNameSaved);
				if (options.onSaved) {
					options.onSaved(destId, destNameSaved, existing);
				}
			},
			onError: options.onError
		});
		return false;
	}

	function getSelect2ResultsEl() {
		var s2 = $select.data('select2');
		if (s2 && s2.$dropdown) {
			return s2.$dropdown.find('.select2-results');
		}
		return $select.next('.select2-container--open').find('.select2-results');
	}

	function appendAndBindAddNew() {
		var $results = getSelect2ResultsEl();
		if (!$results.length) {
			return;
		}

		var $btn = $results.children('.add-new-option');
		if (!$btn.length) {
			$btn = $('<div class="add-new-option" role="button" tabindex="0"></div>')
				.css({
					padding: '8px',
					cursor: 'pointer',
					background: '#f2f2f2',
					borderTop: '1px solid #ddd',
					position: 'relative',
					zIndex: 10
				})
				.html('<span style="color:#676bae;">+ Add New</span>');
			$results.append($btn);
		}

		$btn.off('mousedown.' + ns).on('mousedown.' + ns, function (e) {
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			handleDestinationAddNew(getDestinationInlineSearchTerm($select));
			return false;
		});
	}

	$select
		.off('select2:open.' + ns + ' select2:results:all.' + ns + ' select2:add_new.' + ns)
		.on('select2:open.' + ns, function () {
			cacheDestinationInlineSearchTerm($select, '');
			setTimeout(appendAndBindAddNew, 0);
			setTimeout(appendAndBindAddNew, 80);
		})
		.on('select2:results:all.' + ns, function () {
			setTimeout(appendAndBindAddNew, 0);
		})
		.on('select2:add_new.' + ns, function () {
			handleDestinationAddNew();
		});

	$(document)
		.off('mousedown.' + ns, '.add-new-option')
		.on('mousedown.' + ns, '.add-new-option', function (e) {
			var s2 = $select.data('select2');
			if (!s2 || !s2.isOpen() || !s2.$dropdown || !s2.$dropdown[0].contains(e.currentTarget)) {
				return;
			}
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			handleDestinationAddNew(getDestinationInlineSearchTerm($select));
			return false;
		});

	$(document)
		.off('input.' + ns + ' keyup.' + ns)
		.on('input.' + ns + ' keyup.' + ns, '.select2-container--open .select2-search__field', function () {
			var s2 = $select.data('select2');
			if (!s2 || !s2.isOpen()) {
				return;
			}
			if (s2.dropdown && s2.dropdown.$search && s2.dropdown.$search[0] !== this) {
				return;
			}
			cacheDestinationInlineSearchTerm($select, $(this).val());
			setTimeout(appendAndBindAddNew, 0);
		});
}

function initAllDestinationSelectAddNew(container) {
	var $scope = container ? $(container) : $(document);
	$scope.find('select[data-add-new-option="true"]').filter(function () {
		return isDestinationMasterSelect(this);
	}).each(function () {
		initDestinationAddNewInline(this);
	});
}

window.initDestinationAddNewInline = initDestinationAddNewInline;
window.initAllDestinationSelectAddNew = initAllDestinationSelectAddNew;
window.saveDestinationMaster = saveDestinationMaster;

//**Common inline vehicle save (use initVehicleAddNewInline on transport vehicle Select2)**//

function isVehicleMasterSelect(el) {
	var id = (el && el.id) ? el.id : '';
	if (!id) {
		return false;
	}
	if (id === 'vehicle_name1' || id.indexOf('vehicle_name1') === 0) {
		return true;
	}
	if (id.indexOf('transport_vehicle-') === 0) {
		return true;
	}
	if (id.indexOf('transport_vehicle_name') === 0) {
		return true;
	}
	return false;
}

function cacheVehicleInlineSearchTerm($select, term) {
	$($select).data('vehicleInlineSearchTerm', (term || '').trim());
}

function getVehicleInlineSearchTerm($select) {
	var $selectEl = $($select);
	var cached = $selectEl.data('vehicleInlineSearchTerm');
	if (cached) {
		return (cached + '').trim();
	}
	return getSelect2SearchTerm($selectEl);
}

function selectVehicleInDropdown($select, entryId, vehicleName, options) {
	options = options || {};
	if (!$select.length || entryId === '' || entryId === null || typeof entryId === 'undefined') {
		return;
	}
	var entryIdStr = String(entryId);
	var optionLabel = vehicleName || '';
	if (!$select.find('option').filter(function () { return this.value === entryIdStr; }).length) {
		$select.append($('<option></option>').attr('value', entryIdStr).text(optionLabel));
	}
	$select.val(entryIdStr);
	if (options.triggerChange !== false) {
		$select.trigger('change');
	} else if ($select.data('select2')) {
		$select.trigger('change.select2');
	}
}

function saveVehicleMaster(vehicleName, options) {
	options = options || {};
	vehicleName = (vehicleName || '').trim();

	if (!vehicleName.replace(/\s/g, '').length) {
		if (options.onError) {
			options.onError('Vehicle name is required.');
		} else {
			error_msg_alert('Vehicle name is required.');
		}
		return $.Deferred().reject().promise();
	}

	var base_url = $('#base_url').val();

	return $.post(
		base_url + 'controller/b2b_transfer/vehicle_quick_save_c.php',
		{
			vehicle_name: vehicleName,
			vehicle_type: options.vehicleType || 'Private Car',
			seating_capacity: options.seatingCapacity || '4',
			status: options.status || 'Active'
		},
		function (data) {
			var res;
			try {
				res = typeof data === 'object' ? data : JSON.parse(data);
			} catch (e) {
				if (options.onError) {
					options.onError('Unexpected response from server.');
				} else {
					error_msg_alert('Unexpected response from server.');
				}
				return;
			}

			if (res.status === 'error') {
				if (options.onError) {
					options.onError(res.message);
				} else {
					error_msg_alert(res.message);
				}
				return;
			}

			if (!options.silent && !res.existing) {
				success_msg_alert('Vehicle has been successfully saved.');
			}

			if (typeof update_b2c_cache === 'function') {
				update_b2c_cache();
			}

			if (options.onSuccess) {
				options.onSuccess(res.entry_id, res.vehicle_name, res.existing);
			}
		}
	).fail(function () {
		if (options.onError) {
			options.onError('Failed to save vehicle. Please try again.');
		} else {
			error_msg_alert('Failed to save vehicle. Please try again.');
		}
	});
}

function initVehicleAddNewInline(selector, options) {
	options = options || {};
	var $select = $(selector);
	if (!$select.length || !isVehicleMasterSelect($select[0])) {
		return;
	}
	if ($select.data('vehicleInlineSaveBound')) {
		return;
	}
	$select.data('vehicleInlineSaveBound', true);

	var selectKey = $select.attr('id') || ('vehicle_sel_' + Math.random().toString(36).slice(2));
	var ns = 'vehicleInlineSave_' + selectKey;

	$select.attr('data-add-new-option', 'true');

	function handleVehicleAddNew(presetTerm) {
		var typedVehicle = (presetTerm || getVehicleInlineSearchTerm($select) || '').trim();
		if (!typedVehicle) {
			error_msg_alert('Type a vehicle name in the search box, then click Add New.');
			return false;
		}

		if ($select.data('select2')) {
			$select.select2('close');
		}

		saveVehicleMaster(typedVehicle, {
			vehicleType: options.vehicleType || 'Private Car',
			seatingCapacity: options.seatingCapacity || '4',
			status: options.status || 'Active',
			silent: options.silent,
			onSuccess: function (entryId, vehicleNameSaved, existing) {
				cacheVehicleInlineSearchTerm($select, '');
				selectVehicleInDropdown($select, entryId, vehicleNameSaved);
				if (options.onSaved) {
					options.onSaved(entryId, vehicleNameSaved, existing);
				}
			},
			onError: options.onError
		});
		return false;
	}

	function getSelect2ResultsEl() {
		var s2 = $select.data('select2');
		if (s2 && s2.$dropdown) {
			return s2.$dropdown.find('.select2-results');
		}
		return $select.next('.select2-container--open').find('.select2-results');
	}

	function appendAndBindAddNew() {
		var $results = getSelect2ResultsEl();
		if (!$results.length) {
			return;
		}

		var $btn = $results.children('.add-new-option');
		if (!$btn.length) {
			$btn = $('<div class="add-new-option" role="button" tabindex="0"></div>')
				.css({
					padding: '8px',
					cursor: 'pointer',
					background: '#f2f2f2',
					borderTop: '1px solid #ddd',
					position: 'relative',
					zIndex: 10
				})
				.html('<span style="color:#676bae;">+ Add New</span>');
			$results.append($btn);
		}

		$btn.off('mousedown.' + ns).on('mousedown.' + ns, function (e) {
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			handleVehicleAddNew(getVehicleInlineSearchTerm($select));
			return false;
		});
	}

	$select
		.off('select2:open.' + ns + ' select2:results:all.' + ns + ' select2:add_new.' + ns)
		.on('select2:open.' + ns, function () {
			cacheVehicleInlineSearchTerm($select, '');
			setTimeout(appendAndBindAddNew, 0);
			setTimeout(appendAndBindAddNew, 80);
		})
		.on('select2:results:all.' + ns, function () {
			setTimeout(appendAndBindAddNew, 0);
		})
		.on('select2:add_new.' + ns, function () {
			handleVehicleAddNew();
		});

	$(document)
		.off('mousedown.' + ns, '.add-new-option')
		.on('mousedown.' + ns, '.add-new-option', function (e) {
			var s2 = $select.data('select2');
			if (!s2 || !s2.isOpen() || !s2.$dropdown || !s2.$dropdown[0].contains(e.currentTarget)) {
				return;
			}
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			handleVehicleAddNew(getVehicleInlineSearchTerm($select));
			return false;
		});

	$(document)
		.off('input.' + ns + ' keyup.' + ns)
		.on('input.' + ns + ' keyup.' + ns, '.select2-container--open .select2-search__field', function () {
			var s2 = $select.data('select2');
			if (!s2 || !s2.isOpen()) {
				return;
			}
			if (s2.dropdown && s2.dropdown.$search && s2.dropdown.$search[0] !== this) {
				return;
			}
			cacheVehicleInlineSearchTerm($select, $(this).val());
			setTimeout(appendAndBindAddNew, 0);
		});
}

function initAllVehicleSelectAddNew(container) {
	var $scope = container ? $(container) : $(document);
	$scope.find('select[data-add-new-option="true"]').filter(function () {
		return isVehicleMasterSelect(this);
	}).each(function () {
		initVehicleAddNewInline(this);
	});
}

window.initVehicleAddNewInline = initVehicleAddNewInline;
window.initAllVehicleSelectAddNew = initAllVehicleSelectAddNew;
window.saveVehicleMaster = saveVehicleMaster;

//**Common inline room category save (use initRoomCategoryAddNewInline on room category Select2)**//

function isRoomCategorySelect(el) {
	var id = (el && el.id) ? el.id : '';
	if (id.indexOf('room_cat') === 0) {
		return true;
	}
	if (id.indexOf('txt_catagory') === 0) {
		return true;
	}
	return $(el).hasClass('category_select2');
}

function cacheRoomCategoryInlineSearchTerm($select, term) {
	$($select).data('roomCategoryInlineSearchTerm', (term || '').trim());
}

function getRoomCategoryInlineSearchTerm($select) {
	var $selectEl = $($select);
	var cached = $selectEl.data('roomCategoryInlineSearchTerm');
	if (cached) {
		return (cached + '').trim();
	}
	return getSelect2SearchTerm($selectEl);
}

function selectRoomCategoryInDropdown($select, roomCategory, options) {
	options = options || {};
	if (!$select.length || roomCategory === '' || roomCategory === null || typeof roomCategory === 'undefined') {
		return;
	}
	var categoryStr = String(roomCategory);
	if (!$select.find('option').filter(function () { return this.value === categoryStr; }).length) {
		$select.append($('<option></option>').attr('value', categoryStr).text(categoryStr));
	}
	$select.val(categoryStr);
	if (options.triggerChange !== false) {
		$select.trigger('change');
	} else if ($select.data('select2')) {
		$select.trigger('change.select2');
	}
}

function saveRoomCategoryMaster(roomCategory, options) {
	options = options || {};
	roomCategory = (roomCategory || '').trim();

	if (!roomCategory.replace(/\s/g, '').length) {
		if (options.onError) {
			options.onError('Room category is required.');
		} else {
			error_msg_alert('Room category is required.');
		}
		return $.Deferred().reject().promise();
	}

	var base_url = $('#base_url').val();

	return $.post(
		base_url + 'controller/other_masters/room_category/room_category_quick_save_c.php',
		{ room_category: roomCategory, active_status: options.activeStatus || 'Active' },
		function (data) {
			var res;
			try {
				res = typeof data === 'object' ? data : JSON.parse(data);
			} catch (e) {
				if (options.onError) {
					options.onError('Unexpected response from server.');
				} else {
					error_msg_alert('Unexpected response from server.');
				}
				return;
			}

			if (res.status === 'error') {
				if (options.onError) {
					options.onError(res.message);
				} else {
					error_msg_alert(res.message);
				}
				return;
			}

			if (!options.silent && !res.existing) {
				success_msg_alert('Room Category has been successfully saved.');
			}

			if (typeof update_b2c_cache === 'function') {
				update_b2c_cache();
			}

			if (options.onSuccess) {
				options.onSuccess(res.entry_id, res.room_category, res.existing);
			}
		}
	).fail(function () {
		if (options.onError) {
			options.onError('Failed to save room category. Please try again.');
		} else {
			error_msg_alert('Failed to save room category. Please try again.');
		}
	});
}

function initRoomCategoryAddNewInline(selector, options) {
	options = options || {};
	var $select = $(selector);
	if (!$select.length || !isRoomCategorySelect($select[0])) {
		return;
	}
	if ($select.data('roomCategoryInlineSaveBound')) {
		return;
	}
	$select.data('roomCategoryInlineSaveBound', true);

	var selectKey = $select.attr('id') || ('room_cat_sel_' + Math.random().toString(36).slice(2));
	var ns = 'roomCategoryInlineSave_' + selectKey;

	$select.attr('data-add-new-option', 'true');

	function handleRoomCategoryAddNew(presetTerm) {
		var typedCategory = (presetTerm || getRoomCategoryInlineSearchTerm($select) || '').trim();
		if (!typedCategory) {
			error_msg_alert('Type a room category in the search box, then click Add New.');
			return false;
		}

		if ($select.data('select2')) {
			$select.select2('close');
		}

		saveRoomCategoryMaster(typedCategory, {
			activeStatus: options.activeStatus || 'Active',
			silent: options.silent,
			onSuccess: function (entryId, roomCategorySaved, existing) {
				cacheRoomCategoryInlineSearchTerm($select, '');
				selectRoomCategoryInDropdown($select, roomCategorySaved);
				if (options.onSaved) {
					options.onSaved(entryId, roomCategorySaved, existing);
				}
			},
			onError: options.onError
		});
		return false;
	}

	function getSelect2ResultsEl() {
		var s2 = $select.data('select2');
		if (s2 && s2.$dropdown) {
			return s2.$dropdown.find('.select2-results');
		}
		return $select.next('.select2-container--open').find('.select2-results');
	}

	function appendAndBindAddNew() {
		var $results = getSelect2ResultsEl();
		if (!$results.length) {
			return;
		}

		var $btn = $results.children('.add-new-option');
		if (!$btn.length) {
			$btn = $('<div class="add-new-option" role="button" tabindex="0"></div>')
				.css({
					padding: '8px',
					cursor: 'pointer',
					background: '#f2f2f2',
					borderTop: '1px solid #ddd',
					position: 'relative',
					zIndex: 10
				})
				.html('<span style="color:#676bae;">+ Add New</span>');
			$results.append($btn);
		}

		$btn.off('mousedown.' + ns).on('mousedown.' + ns, function (e) {
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			handleRoomCategoryAddNew(getRoomCategoryInlineSearchTerm($select));
			return false;
		});
	}

	$select
		.off('select2:open.' + ns + ' select2:results:all.' + ns + ' select2:add_new.' + ns)
		.on('select2:open.' + ns, function () {
			cacheRoomCategoryInlineSearchTerm($select, '');
			setTimeout(appendAndBindAddNew, 0);
			setTimeout(appendAndBindAddNew, 80);
		})
		.on('select2:results:all.' + ns, function () {
			setTimeout(appendAndBindAddNew, 0);
		})
		.on('select2:add_new.' + ns, function () {
			handleRoomCategoryAddNew();
		});

	$(document)
		.off('mousedown.' + ns, '.add-new-option')
		.on('mousedown.' + ns, '.add-new-option', function (e) {
			var s2 = $select.data('select2');
			if (!s2 || !s2.isOpen() || !s2.$dropdown || !s2.$dropdown[0].contains(e.currentTarget)) {
				return;
			}
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			handleRoomCategoryAddNew(getRoomCategoryInlineSearchTerm($select));
			return false;
		});

	$(document)
		.off('input.' + ns + ' keyup.' + ns)
		.on('input.' + ns + ' keyup.' + ns, '.select2-container--open .select2-search__field', function () {
			var s2 = $select.data('select2');
			if (!s2 || !s2.isOpen()) {
				return;
			}
			if (s2.dropdown && s2.dropdown.$search && s2.dropdown.$search[0] !== this) {
				return;
			}
			cacheRoomCategoryInlineSearchTerm($select, $(this).val());
			setTimeout(appendAndBindAddNew, 0);
		});
}

function refreshRoomCategorySelectAfterLoad(selector, config) {
	config = config || {};
	var $select = $(selector);
	if (!$select.length || !isRoomCategorySelect($select[0])) {
		return;
	}

	$select.removeData('roomCategoryInlineSaveBound');
	if ($select.data('select2')) {
		$select.select2('destroy');
	}

	var s2Config = {
		width: config.width || '100%',
		minimumResultsForSearch: 0
	};
	if (config.dropdownParent) {
		s2Config.dropdownParent = config.dropdownParent;
	} else {
		var $modal = $select.closest('.modal');
		if ($modal.length) {
			s2Config.dropdownParent = $modal;
		}
	}

	$select.select2(s2Config);
	initRoomCategoryAddNewInline($select, config);
}

function initAllRoomCategorySelectAddNew(container) {
	var $scope = container ? $(container) : $(document);
	$scope.find('select[data-add-new-option="true"]').filter(function () {
		return isRoomCategorySelect(this);
	}).each(function () {
		initRoomCategoryAddNewInline(this);
	});
}

window.initRoomCategoryAddNewInline = initRoomCategoryAddNewInline;
window.initAllRoomCategorySelectAddNew = initAllRoomCategorySelectAddNew;
window.refreshRoomCategorySelectAfterLoad = refreshRoomCategorySelectAfterLoad;
window.saveRoomCategoryMaster = saveRoomCategoryMaster;

//**Common Hotel Supplier quick-save modal (reuse in package tour & other screens)**//

var _hsqModalInitialized = false;

function ensureHotelSupplierQuickModal(callback) {
	if ($('#Hotelsupplierdetails_modal').length) {
		if (!_hsqModalInitialized) {
			initHotelSupplierQuickModalFields();
		}
		if (typeof callback === 'function') {
			callback();
		}
		return;
	}

	var base_url = $('#base_url').val();
	$.post(base_url + 'view/hotels/master/hotel/hotel_supplier_quick_modal.php', {}, function (html) {
		$('#div_hotel_supplier_quick_modal').html(html);
		initHotelSupplierQuickModalFields();
		if (typeof callback === 'function') {
			callback();
		}
	});
}

function hotelSupplierQuickCityChange(cityId) {
	if (!cityId) {
		return;
	}
	var base_url = $('#base_url').val();
	$.getJSON(base_url + 'view/hotels/master/hotel/city_state_suggest.php', { city_id: cityId }, function (res) {
		if (res.status === 'success' && res.state_id) {
			$('#state_filter_hotel').val(res.state_id).trigger('change');
		}
	});
}

function resetHotelSupplierQuickForm() {
	$('#hsq_hotel_name').val('');
	$('#hotel_Category').val('').trigger('change');
	resetLazyCitySelect('#city_filter_hotel', 'Select City');
	if ($('#state_filter_hotel').data('select2')) {
		$('#state_filter_hotel').val('').trigger('change');
	} else {
		$('#state_filter_hotel').val('');
	}
}

function initHotelSupplierQuickModalFields() {
	if (_hsqModalInitialized) {
		return;
	}
	_hsqModalInitialized = true;

	var $modal = $('#Hotelsupplierdetails_modal');

	initQuickModalCitySelect('#city_filter_hotel', 'Select City');
	bindQuickModalCityPlaceholderRefresh($modal, '#city_filter_hotel', 'Select City');

	$('#state_filter_hotel, #hotel_Category').select2({
		dropdownParent: $modal,
		width: '100%'
	});

	$('#city_filter_hotel').off('change.hsq').on('change.hsq', function () {
		hotelSupplierQuickCityChange($(this).val());
	});

	$('#btn_hsq_save').off('click.hsq').on('click.hsq', function () {
		hotelSupplierQuickSave();
	});

	$modal.off('hidden.bs.modal.hsq').on('hidden.bs.modal.hsq', function () {
		var $thisModal = $(this);
		var hotelSelectId = $thisModal.data('trigger-hotel-id') || '';
		var savedHotelId = $thisModal.data('saved-hotel-id');
		var savedHotelName = $thisModal.data('saved-hotel-name') || '';
		var savedCityId = $thisModal.data('saved-city-id') || '';
		var savedCityName = $thisModal.data('saved-city-name') || '';

		if (savedHotelId && hotelSelectId) {
			reloadHotelAfterQuickSave(hotelSelectId, savedHotelId, savedHotelName, savedCityId, savedCityName);
		}

		$thisModal.removeData('saved-hotel-id');
		$thisModal.removeData('saved-hotel-name');
		$thisModal.removeData('saved-city-id');
		$thisModal.removeData('saved-city-name');
	});
}

function openHotelSupplierQuickModal(options) {
	options = options || {};

	ensureHotelSupplierQuickModal(function () {
		var $modal = $('#Hotelsupplierdetails_modal');
		$modal.data('trigger-city-id', options.citySelectId || '');
		$modal.data('trigger-hotel-id', options.hotelSelectId || '');

		resetHotelSupplierQuickForm();

		if (options.cityId) {
			var $citySelect = $('#city_filter_hotel');
			selectCityInLazyDropdown($citySelect, options.cityId, options.cityName || '');
			hotelSupplierQuickCityChange(options.cityId);
		}

		$modal.modal('show');
	});
}

function hotelSupplierQuickSave() {
	var base_url = $('#base_url').val();
	var city_id = $('#city_filter_hotel').val();
	var hotel_name = ($('#hsq_hotel_name').val() || '').trim();
	var state_id = $('#state_filter_hotel').val();
	var rating_star = $('#hotel_Category').val();

	if (!city_id) {
		error_msg_alert('Select city name.');
		return false;
	}
	if (!hotel_name.replace(/\s/g, '').length) {
		error_msg_alert('Enter hotel name.');
		return false;
	}
	if (!state_id) {
		error_msg_alert('Select state/country name.');
		return false;
	}
	if (!rating_star) {
		error_msg_alert('Select hotel category.');
		return false;
	}

	$('#btn_hsq_save').button('loading');
	$.post(
		base_url + 'controller/hotel/hotel_quick_save_c.php',
		{
			city_id: city_id,
			hotel_name: hotel_name,
			state_id: state_id,
			rating_star: rating_star
		},
		function (data) {
			var res;
			try {
				res = typeof data === 'object' ? data : JSON.parse(data);
			} catch (e) {
				error_msg_alert('Unexpected response from server.');
				$('#btn_hsq_save').button('reset');
				return;
			}

			if (res.status === 'error') {
				error_msg_alert(res.message);
				$('#btn_hsq_save').button('reset');
				return;
			}

			success_msg_alert(res.message || 'Hotel supplier has been successfully saved.');
			if (typeof update_b2c_cache === 'function') {
				update_b2c_cache();
			}

			var $modal = $('#Hotelsupplierdetails_modal');
			var hotelSelectId = $modal.data('trigger-hotel-id') || '';
			var savedCityName = $('#city_filter_hotel').find('option:selected').text() || '';

			$modal
				.data('saved-hotel-id', res.hotel_id)
				.data('saved-hotel-name', res.hotel_name)
				.data('saved-city-id', city_id)
				.data('saved-city-name', savedCityName)
				.modal('hide');

			$('#btn_hsq_save').button('reset');
		}
	).fail(function () {
		error_msg_alert('Failed to save hotel. Please try again.');
		$('#btn_hsq_save').button('reset');
	});
}

function resolveHotelCitySelect($hotelSelect) {
	if (!$hotelSelect || !$hotelSelect.length) {
		return $();
	}

	var $row = $hotelSelect.closest('tr');
	if ($row.length) {
		var $cityInRow = $row.find('select[id^="city_name"], select[name^="city_name"], select.city_name, select.city_id, select[id^="city_id"]').first();
		if ($cityInRow.length) {
			return $cityInRow;
		}
	}

	var hotelId = $hotelSelect.attr('id') || '';
	var match;

	match = hotelId.match(/^hotel_name-(\d+)-(\d+)$/);
	if (match) {
		var $city = $('#city_name-' + match[1] + '-' + match[2]);
		if ($city.length) {
			return $city;
		}
	}

	match = hotelId.match(/^hotel_name-(\d+)$/);
	if (match) {
		var $cityNum = $('#city_name' + match[1]);
		if ($cityNum.length) {
			return $cityNum;
		}
	}

	match = hotelId.match(/^hotel_name(\d+)$/);
	if (match) {
		var $cityAlt = $('#city_name' + match[1]);
		if ($cityAlt.length) {
			return $cityAlt;
		}
	}

	match = hotelId.match(/^hotel_id(\d+)$/);
	if (match) {
		var $cityHotel = $('#city_id' + match[1]);
		if ($cityHotel.length) {
			return $cityHotel;
		}
	}

	if (hotelId.indexOf('hotel_name') === 0) {
		var suffix = hotelId.replace(/^hotel_name/, '');
		var $citySuffix = $('#city_name' + suffix);
		if ($citySuffix.length) {
			return $citySuffix;
		}
	}

	return $('#city_name').length ? $('#city_name') : $();
}

function parseQuotationHotelRowSuffix(fieldId) {
	if (!fieldId) {
		return '';
	}
	var match = String(fieldId).match(/(?:hotel_name|city_name)-?(\d+)$/);
	return match ? match[1] : '';
}

function resolveHotelSelectFromCity(cityRef) {
	var $city = cityRef instanceof jQuery ? cityRef : $('#' + cityRef);
	if (!$city.length) {
		return $();
	}

	var $row = $city.closest('tr');
	if ($row.length) {
		var $hotelInRow = $row.find('select[id^="hotel_name"], select[name^="hotel_name"], select[id^="hotel_id"]').first();
		if ($hotelInRow.length) {
			return $hotelInRow;
		}
	}

	var cityFieldId = $city.attr('id') || '';

	// Hotel B2B tariff screens: cmb_city_id1 -> hotel_id1
	var cmbCityMatch = cityFieldId.match(/^cmb_city_id(\d*)$/);
	if (cmbCityMatch) {
		var tariffHotelId = 'hotel_id' + (cmbCityMatch[1] || '1');
		var $tariffHotel = $('#' + tariffHotelId);
		if ($tariffHotel.length) {
			return $tariffHotel;
		}
	}

	// Hotel tariff list filter
	if (cityFieldId === 'city_id_filter') {
		var $filterHotel = $('#hotel_id_filter');
		if ($filterHotel.length) {
			return $filterHotel;
		}
	}

	var suffix = parseQuotationHotelRowSuffix(cityFieldId);
	if (suffix) {
		var $hotel = $('#hotel_name-' + suffix);
		if ($hotel.length) {
			return $hotel;
		}
		$hotel = $('#hotel_name' + suffix);
		if ($hotel.length) {
			return $hotel;
		}
	}

	if ($('#hotel_name').length) {
		return $('#hotel_name');
	}

	return $();
}

function hotel_name_list_load(id) {
	var $city = $('#' + id);
	var city_id = $city.val();
	if (!city_id) {
		return;
	}

	var $hotel = resolveHotelSelectFromCity($city);
	if (!$hotel.length) {
		console.warn('hotel_name_list_load: hotel select not found for city', id);
		return;
	}

	if (typeof hotelDropdownLoadByCity === 'function') {
		hotelDropdownLoadByCity(city_id, $hotel);
		return;
	}

	var base_url = $('#base_url').val();
	$.get(base_url + 'view/package_booking/quotation/home/hotel/hotel_name_load.php', {
		city_id: city_id
	}, function (data) {
		if ($hotel.data('select2')) {
			$hotel.select2('destroy');
		}
		$hotel.html(data);
		var config = typeof captureHotelSelect2Config === 'function'
			? captureHotelSelect2Config($hotel)
			: { width: '160px', minimumResultsForSearch: 0 };
		$hotel.select2(config);
		if (typeof initHotelSelectAddNew === 'function') {
			initHotelSelectAddNew($hotel);
		}
	});
}

function normalizeHotelLoadUrl(url) {
	var base = ($('#base_url').val() || '').replace(/\/?$/, '/');
	if (!url) {
		return base + 'view/custom_packages/master/package/hotel/hotel_name_load.php';
	}
	if (/^https?:\/\//i.test(url)) {
		return url;
	}
	if (url.indexOf('view/') === 0 || url.indexOf('inc/') === 0) {
		return base + url;
	}
	// Relative paths like hotel/hotel_name_load.php depend on the current page folder.
	return base + url;
}

function resolveHotelLoadUrl() {
	if (typeof hotelSupplierQuickLoadUrl !== 'undefined' && hotelSupplierQuickLoadUrl) {
		return normalizeHotelLoadUrl(hotelSupplierQuickLoadUrl);
	}
	if (typeof packageHotelLoadUrl !== 'undefined' && packageHotelLoadUrl) {
		return normalizeHotelLoadUrl(packageHotelLoadUrl);
	}
	return normalizeHotelLoadUrl('');
}

function captureHotelSelect2Config($select) {
	var config;
	if ($select.data('hsq-select2-config')) {
		config = $.extend({}, $select.data('hsq-select2-config'));
	} else {
		config = {
			width: '100%',
			minimumResultsForSearch: 0
		};
		var s2 = $select.data('select2');
		if (s2 && s2.options) {
			var opts = s2.options.options || s2.options;
			if (opts.width) {
				config.width = opts.width;
			}
			if (opts.dropdownParent) {
				config.dropdownParent = opts.dropdownParent;
			}
			if (opts.placeholder !== undefined) {
				config.placeholder = opts.placeholder;
			}
			if (opts.allowClear !== undefined) {
				config.allowClear = opts.allowClear;
			}
		}
	}
	if (!config.dropdownParent) {
		var $modal = $select.closest('.modal');
		if ($modal.length) {
			config.dropdownParent = $modal;
		}
	}
	$select.data('hsq-select2-config', config);
	return config;
}

function reinitHotelSelect2($select) {
	var config = $.extend({}, captureHotelSelect2Config($select));
	if ($select.data('select2')) {
		$select.select2('destroy');
	}
	$select.select2(config);
	initHotelSelectAddNew($select);
}

function getHotelSelect2ResultsEl($select) {
	var s2 = $select.data('select2');
	if (s2 && s2.$dropdown) {
		return s2.$dropdown.find('.select2-results');
	}
	var $container = $select.next('.select2-container');
	if ($container.hasClass('select2-container--open')) {
		return $container.find('.select2-results');
	}
	return $();
}

function appendHotelAddNewButton($select) {
	var $results = getHotelSelect2ResultsEl($select);
	if (!$results.length) {
		return;
	}

	var selectKey = $select.attr('id') || 'hotel';
	var ns = 'hotelInlineAddNew_' + selectKey;

	$results.find('.add-new-option-hotel').remove();

	var $btn = $('<div class="add-new-option add-new-option-hotel" role="button" tabindex="0"></div>')
		.css({
			padding: '10px',
			cursor: 'pointer',
			background: '#f2f2f2',
			borderTop: '1px solid #ddd',
			display: 'flex',
			alignItems: 'center',
			gap: '6px',
			position: 'relative',
			zIndex: 9999,
			pointerEvents: 'auto'
		})
		.html(
			'<svg xmlns="http://www.w3.org/2000/svg" height="12" width="10.5" viewBox="0 0 448 512">' +
			'<path fill="rgb(103, 107, 174)" d="M256 64c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 160-160 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l160 0 0 160c0 17.7 14.3 32 32 32s32-14.3 32-32l0-160 160 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-160 0 0-160z"/>' +
			'</svg><span>Add New</span>'
		);

	$results.append($btn);

	$btn.off('mousedown.' + ns + ' click.' + ns).on('mousedown.' + ns + ' click.' + ns, function (e) {
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
		handleHotelSupplierAddNew($select);
		return false;
	});
}

function hotelDropdownLoadByCity(cityId, $hotelSelect, callback) {
	if (!$hotelSelect || !$hotelSelect.length) {
		if (typeof callback === 'function') {
			callback(false, $hotelSelect);
		}
		return;
	}
	if (!cityId) {
		if (typeof callback === 'function') {
			callback(false, $hotelSelect);
		}
		return;
	}

	var config = captureHotelSelect2Config($hotelSelect);
	$.get(resolveHotelLoadUrl(), { city_id: cityId })
		.done(function (data) {
			if (!$hotelSelect.length) {
				return;
			}
			if ($hotelSelect.data('select2')) {
				$hotelSelect.select2('destroy');
			}
			$hotelSelect.html(data);
			$hotelSelect.select2(config);
			initHotelSelectAddNew($hotelSelect);
			if (typeof callback === 'function') {
				callback(true, $hotelSelect);
			}
		})
		.fail(function () {
			if (typeof callback === 'function') {
				callback(false, $hotelSelect);
			}
		});
}

function selectHotelInDropdown($hotelSelect, hotelId, hotelName) {
	if (!$hotelSelect || !$hotelSelect.length || hotelId === '' || hotelId === null || typeof hotelId === 'undefined') {
		return;
	}
	var id = String(hotelId);
	var $option = $hotelSelect.find('option').filter(function () { return String(this.value) === id; });
	if (!$option.length) {
		$hotelSelect.append($('<option></option>').attr('value', id).text(hotelName || ''));
	} else if (hotelName) {
		$option.text(hotelName);
	}
	$hotelSelect.val(id);
	if ($hotelSelect.data('select2')) {
		$hotelSelect.trigger('change');
	} else {
		$hotelSelect.trigger('change');
	}

	var selectId = $hotelSelect.attr('id') || '';
	if (selectId && typeof hotel_type_load === 'function') {
		hotel_type_load(selectId);
	} else if (selectId && typeof hotel_type_load_cate === 'function') {
		hotel_type_load_cate(selectId);
	}
}

function reloadHotelAfterQuickSave(hotelSelectId, savedHotelId, savedHotelName, savedCityId, savedCityName) {
	if (!hotelSelectId || !savedHotelId) {
		return;
	}

	var $hotel = $('#' + hotelSelectId);
	if (!$hotel.length) {
		return;
	}

	var $city = resolveHotelCitySelect($hotel);
	var cityId = savedCityId || ($city.length ? $city.val() : '');

	if ($city.length && savedCityId && String($city.val() || '') !== String(savedCityId)) {
		selectCityInLazyDropdown($city, savedCityId, savedCityName || '', { triggerChange: false });
		cityId = savedCityId;
	} else if ($city.length && savedCityId) {
		cityId = savedCityId;
	}

	captureHotelSelect2Config($hotel);

	function finishSelect() {
		selectHotelInDropdown($hotel, savedHotelId, savedHotelName);
	}

	if (cityId) {
		hotelDropdownLoadByCity(cityId, $hotel, function () {
			setTimeout(finishSelect, 0);
		});
	} else {
		setTimeout(finishSelect, 0);
	}
}

function initAllHotelSelectAddNew(container) {
	var $scope = container ? $(container) : $(document);
	$scope.find('select[id^="hotel_name"][data-add-new-option="true"], select[id^="hotel_id"][data-add-new-option="true"]').each(function () {
		initHotelSelectAddNew(this);
	});
}

function handleHotelSupplierAddNew($hotelSelect) {
	if (!$hotelSelect || !$hotelSelect.length) {
		return false;
	}

	var hotelId = $hotelSelect.attr('id') || '';
	var $citySelect = resolveHotelCitySelect($hotelSelect);
	var citySelectId = $citySelect.length ? ($citySelect.attr('id') || '') : 'city_name';
	var cityId = $citySelect.length ? $citySelect.val() : '';
	var cityName = $citySelect.length ? $citySelect.find('option:selected').text() : '';

	if ($hotelSelect.data('select2')) {
		$hotelSelect.select2('close');
	}

	openHotelSupplierQuickModal({
		citySelectId: citySelectId,
		hotelSelectId: hotelId,
		cityId: cityId,
		cityName: cityName
	});
	return false;
}

/**
 * Bind Select2 "+ Add New" on hotel dropdowns to open the Hotel Supplier quick modal.
 */
function initHotelSelectAddNew(selector) {
	var $select = $(selector);
	if (!$select.length) {
		return;
	}

	var selectKey = $select.attr('id') || ('hotel_sel_' + Math.random().toString(36).slice(2));
	var ns = 'hotelInlineAddNew_' + selectKey;

	$select.attr('data-add-new-option', 'true');

	if ($select.data('select2')) {
		captureHotelSelect2Config($select);
	}

	$select
		.off('select2:open.' + ns + ' select2:add_new.' + ns)
		.on('select2:open.' + ns, function () {
			setTimeout(function () { appendHotelAddNewButton($select); }, 0);
			setTimeout(function () { appendHotelAddNewButton($select); }, 80);
		})
		.on('select2:add_new.' + ns, function () {
			handleHotelSupplierAddNew($select);
		});
}

$(function () {
	$(document).on('select2:add_new.hotelSupplierQuickGlobal', 'select[id^="hotel_name"][data-add-new-option="true"], select[id^="hotel_id"][data-add-new-option="true"]', function () {
		handleHotelSupplierAddNew($(this));
	});

	$(document).on('select2:select', 'select.plane-airport-select, select[id^="from_sector-"], select[id^="to_sector-"]', function (e) {
		var cityId = e.params && e.params.data ? e.params.data.city_id : '';
		syncSectorCityHidden(this.id || '', cityId);
	});

	$(document).on('select2:clear', 'select.plane-airport-select, select[id^="from_sector-"], select[id^="to_sector-"]', function () {
		syncSectorCityHidden(this.id || '', '');
	});

	$(document).on('click', '.btn-airport-input-add', function () {
		var inputId = $(this).data('input-id') || '';
		var suffix = inputId.replace(/^airpf-?/, '').replace(/^airpt-?/, '');
		var cityId = '';
		if (inputId.indexOf('airpf') === 0) {
			cityId = $('#from_city-' + suffix).val() || '';
		} else if (inputId.indexOf('airpt') === 0) {
			cityId = $('#to_city-' + suffix).val() || '';
		}
		openAirportQuickModal({ targetInputId: inputId, cityId: cityId });
	});

	// Re-init airport sector Select2 when tab/accordion becomes visible (hidden tabs break AJAX lists)
	$(document).on('click', '.bk_tab_head a', function () {
		setTimeout(function () {
			refreshPlaneAirportSelect2In('.bk_tab.active');
		}, 250);
	});

	$(document).on('shown.bs.collapse', '.panel-collapse', function () {
		var $panel = $(this);
		if ($panel.find('select[id^="from_sector-"], select[id^="to_sector-"]').length) {
			setTimeout(function () {
				refreshPlaneAirportSelect2In($panel);
			}, 150);
		}
	});

	$(document).on('click', '[data-toggle="collapse"][href*="collapse"], [data-toggle="collapse"][data-target*="collapse"]', function () {
		setTimeout(function () {
			refreshPlaneAirportSelect2In('.bk_tab.active');
		}, 400);
	});

	setTimeout(function () {
		initAllHotelSelectAddNew();
		initAllAirlineSelectAddNew();
		initAllRoomCategorySelectAddNew();
		initAllDestinationSelectAddNew();
		initAllVehicleSelectAddNew();
	}, 300);
});

//**Generic City save modal start**//
function generic_city_save_modal(modal_type = '') {

	$('#btn_city_save_modal').button('loading');
	var base_url = $('#base_url').val();

	$.post(base_url + 'view/other_masters/cities/save_modal.php', { modal_type: modal_type }, function (data) {
		var msg = data.split('--');
		if(msg[0]=='error'){

			error_msg_alert(msg[1]);
			$('#btn_city_save_modal').button('reset');
			return false;
		}
		else{
			$('#btn_city_save_modal').button('reset');
			$('#div_city_save_modal').html(data);
		}
	});
}

function city_master_dropdown_reload() {
	var city_master_dropdown = 'city_master_dropdown';

	var base_url = $('#base_url').val();

	$('.city_master_dropdown').each(function () {
		var cur_ele = $(this);

		$.post(
			base_url + 'modal/app_settings/dropdown_master.php',
			{ city_master_dropdown: city_master_dropdown },
			function (data) {
				$(cur_ele).select2();

				$(cur_ele).css('width', '100%');

				$(cur_ele).html(data).trigger('change');
			}
		);
	});
}
//City Dropdown Lazy Loading
// function city_lzloading(element, placeholder = "City Name", valueasText = false) {
// 	var base_url = $("#base_url").val();
// 	url = base_url + '/view/load_data/generic_city_loading.php';
// 	$(element).append($("<option></option>").attr("value", "").text(placeholder));
// 	$(element).select2({
// 		placeholder: placeholder,
// 		dropdownParent: $("#" + dropdownParent),
// 		ajax: {
// 			url: url,
// 			dataType: 'json',
// 			type: 'GET',
// 			data: function (params) { return { term: params.term, page: params.page || 0, valueasText: valueasText } },
// 			processResults: function (data) {
// 				let more = data.pagination;
// 				return {
// 					results: data.results,
// 					pagination: {
// 						more: more.more,
// 					}
// 				};
// 			}
// 		}
// 	});

// }



// function city_lzloading(element, placeholder = "City Name", valueasText = false) {
// 	var base_url = $("#base_url").val();
// 	url = base_url + '/view/load_data/generic_city_loading.php';
// 	$(element).append($("<option></option>").attr("value", "").text(placeholder));
// 	var dropdownParent  = $(element).closest('.modal').attr('id');
	
// 	$(element).select2({
// 		placeholder: placeholder,
// 		dropdownParent: $("#" + dropdownParent),
// 		ajax: {
// 			url: url,
// 			dataType: 'json',
// 			type: 'GET',
// 			data: function (params) { return { term: params.term, page: params.page || 0, valueasText: valueasText } },
// 			processResults: function (data) {
// 				let more = data.pagination;
// 				return {
// 					results: data.results,
// 					pagination: {
// 						more: more.more,
// 					}
// 				};
// 			}
// 		}
// 	});

// }


function getModalSelect2Parent($element) {
	// Append dropdown to body so position stays correct inside scrollable modals/tables.
	return $(document.body);
}

function resolveAppSelect2Width($el) {
	var $select = $($el);
	var styleAttr = $select.attr('style') || '';
	var widthMatch = styleAttr.match(/(?:^|;)\s*width\s*:\s*([^;]+)/i);
	if (widthMatch && widthMatch[1]) {
		return $.trim(widthMatch[1]);
	}
	var outerWidth = $select.outerWidth();
	return outerWidth > 0 ? outerWidth : '100%';
}

function cleanClonedSelectElement(selectEl) {
	if (!selectEl || selectEl.tagName !== 'SELECT') {
		return selectEl;
	}
	selectEl.classList.remove('select2-hidden-accessible');
	selectEl.removeAttribute('tabindex');
	selectEl.removeAttribute('aria-hidden');
	selectEl.removeAttribute('data-select2-id');
	if (selectEl.style.display === 'none') {
		selectEl.style.display = '';
	}
	return selectEl;
}

function initAppSelect2Element(element, extraConfig) {
	var $select = $(element);
	if (!$select.length || !$select.is('select')) {
		return $select;
	}
	extraConfig = extraConfig || {};

	// Lazy AJAX dropdowns (city / airport / airline) manage their own Select2.
	// Do not destroy them on modal shown or search will show "No results found".
	if (
		$select.attr('data-add-new-option') === 'true' ||
		$select.attr('data-lazy-select') === 'true' ||
		$select.attr('data-lazy-city') === 'true'
	) {
		return $select;
	}
	var existing = $select.data('select2');
	if (existing && existing.options && typeof existing.options.get === 'function' && existing.options.get('ajax')) {
		return $select;
	}

	if ($select.data('select2')) {
		$select.select2('destroy');
	}

	var config = $.extend({
		width: resolveAppSelect2Width($select),
		minimumResultsForSearch: 0,
		dropdownParent: getModalSelect2Parent($select)
	}, extraConfig);

	$select.select2(config);
	return $select;
}

function initModalAppSelect2(scope, extraConfig) {
	var $scope = scope ? $(scope) : $(document);
	extraConfig = extraConfig || {};
	$scope.find('select.app_select2, select.form-contrl.app_select2').each(function () {
		initAppSelect2Element(this, extraConfig);
	});
}

window.getModalSelect2Parent = getModalSelect2Parent;
window.resolveAppSelect2Width = resolveAppSelect2Width;
window.cleanClonedSelectElement = cleanClonedSelectElement;
window.initAppSelect2Element = initAppSelect2Element;
window.initModalAppSelect2 = initModalAppSelect2;

$(document).on('shown.bs.modal', '.modal', function () {
	initModalAppSelect2(this);
});

$(document).on('scroll.appSelect2', '.modal, .modal .table-responsive', function () {
	$('select.app_select2, select.form-contrl.app_select2').each(function () {
		var s2 = $(this).data('select2');
		if (s2 && s2.isOpen()) {
			$(this).select2('close');
		}
	});
});

function city_lzloading(element, placeholder = "City Name", valueasText = false) {
	var base_url = ($("#base_url").val() || '').replace(/\/?$/, '/');
	var url = base_url + 'view/load_data/generic_city_loading.php';

	$(element).each(function () {
		var $el = $(this);
		$el.attr('data-lazy-city', 'true');
		$el.attr('data-lazy-select', 'true');

		if ($el.data('select2')) {
			$el.select2('destroy');
		}
		if (!$el.find('option[value=""]').length) {
			$el.prepend($("<option></option>").attr("value", "").text(placeholder));
		}

		var $modal = $el.closest('.modal');
		var config = {
			placeholder: placeholder,
			allowClear: true,
			minimumInputLength: 0,
			width: '100%',
			ajax: {
				url: url,
				dataType: 'json',
				delay: 250,
				type: 'GET',
				data: function (params) {
					return {
						term: params.term || '',
						page: params.page || 0,
						valueasText: valueasText
					};
				},
				processResults: function (data) {
					data = data || {};
					var pagination = data.pagination || { more: false };
					return {
						results: data.results || [],
						pagination: {
							more: !!pagination.more
						}
					};
				},
				cache: true
			}
		};
		if ($modal.length) {
			config.dropdownParent = $modal;
		}
		$el.select2(config);

		if ($el.attr('data-add-new-option') === 'true') {
			initCityAddNewInline($el);
		}
	});
}


function destinationLoading(element, placeholder = "Destination", valueasText = false) {
	var base_url = $("#base_url").val();
	url = base_url + '/view/load_data/generic_destination_loading.php';

	$(element).each(function () {
		var $el = $(this);

		// Store current selection before destroying (per element)
		var currentValue = $el.val();
		var currentText = $el.find('option:selected').text();
		var currentOptgroup = $el.find('option:selected').parent().attr('value');

		
		if ($el.hasClass("select2-hidden-accessible")) {
			$el.select2('destroy');
		}

		$el.select2({
			placeholder: placeholder,
			ajax: {
				url: url,
				dataType: 'json',
				type: 'GET',
				data: function (params) { return { term: params.term, page: params.page || 0, valueasText: valueasText } },
				processResults: function (data) {
					// If there was a pre-selected value, add it to the results
					if (currentValue && currentText) {
						var found = false;
						for (var i = 0; i < data.results.length; i++) {
							if (data.results[i].children) {
								for (var j = 0; j < data.results[i].children.length; j++) {
									if (data.results[i].children[j].id === currentValue) {
										found = true;
										break;
									}
								}
							}
						}
						if (!found && currentOptgroup) {
							var groupLabel = currentOptgroup.charAt(0).toUpperCase() + currentOptgroup.slice(1) + ' Name';
							var groupExists = false;
							for (var k = 0; k < data.results.length; k++) {
								if (data.results[k].text === groupLabel) {
									data.results[k].children.unshift({ id: currentValue, text: currentText });
									groupExists = true;
									break;
								}
							}
							if (!groupExists) {
								data.results.unshift({
									text: groupLabel,
									children: [{ id: currentValue, text: currentText }]
								});
							}
						}
					}

					let more = data.pagination;
					return {
						results: data.results,
						pagination: {
							more: more.more,
						}
					};
				}
			}
		});

		if (currentValue) {
			$el.val(currentValue).trigger('change');
		}
	});
}
//**Generic City save modal end**//

//**Generic PAyment fields toggle function start**//

function payment_master_toggles(payment_mode_id, bank_name_id, transaction_id_id, bank_id_id) {

	var payment_mode = $('#' + payment_mode_id).val();

	if (payment_mode == 'Cash' || payment_mode == '' || payment_mode == 'Credit Note' || payment_mode == 'Debit Note' || payment_mode == 'Credit Card' || payment_mode == 'Advance' || payment_mode == 'To Supplier') {

		$('#' + bank_name_id).prop({ disabled: 'disabled', readonly: 'readonly', value: '' });
		$('#' + transaction_id_id).prop({ disabled: 'disabled', readonly: 'readonly', value: '' });
		$('#' + bank_id_id).prop({ disabled: 'disabled', readonly: 'readonly', value: '' });
	}
	else {

		$('#' + bank_name_id).prop({ disabled: '', readonly: '' });
		$('#' + transaction_id_id).prop({ disabled: '', readonly: '' });
		$('#' + bank_id_id).prop({ disabled: '', readonly: '' });
	}
}

//automatic gender change

function changeGender(id) {
	var offset = id.substr(15);
	var val = $('#' + id).val();
	switch (val) {
		case "Mr":
		case "Infant":
		case "Mas": gender = "Male"; break;
		case "Mrs":
		case "Miss":
		case "Smt": gender = "Female"; break;
	}
	$('#cmb_m_gender' + offset).val(gender);
}

//**Generic PAyment fields toggle function end**//

//If payment amount 0 disable payment mode

function payment_amount_validate(payment_amount_id, payment_mode_id, transaction_id_id, bank_name_name, bank_id_id) {
	var payment_amt = $('#' + payment_amount_id).val();

	if (payment_amt == 0) {
		$('#' + payment_mode_id).prop({ disabled: 'disabled', value: '' });

		$('#' + transaction_id_id).prop({ disabled: 'disabled', value: '' });

		$('#' + bank_name_name).prop({ disabled: 'disabled', value: '' });

		$('#' + bank_id_id).prop({ disabled: 'disabled', value: '' });
	}
	else {
		$('#' + payment_mode_id).prop({ disabled: '' });
		var offset = payment_mode_id.replace('cmb_payment_mode', '');
		if (typeof payment_installment_enable_disable_fields === 'function') {
			payment_installment_enable_disable_fields(offset);
		}
	}
}

function getAirportListUrl() {
	var base = ($('#base_url').val() || '').replace(/\/?$/, '/');
	return base + 'view/visa_passport_ticket/ticket/home/airport_list.php';
}

//**Common Airport quick-save modal (reuse in flight sections)**//

var _aqModalInitialized = false;

function ensureAirportQuickModal(callback) {
	if ($('#AirportQuickSave_modal').length) {
		if (!_aqModalInitialized) {
			initAirportQuickModalFields();
		}
		if (typeof callback === 'function') {
			callback();
		}
		return;
	}

	var base_url = $('#base_url').val();
	$.post(base_url + 'view/other_masters/airports/airport_quick_modal.php', {}, function (html) {
		$('#div_airport_quick_modal').html(html);
		initAirportQuickModalFields();
		if (typeof callback === 'function') {
			callback();
		}
	});
}

function resetAirportQuickForm() {
	$('#aq_airport_name').val('');
	$('#aq_airport_code').val('');
	resetLazyCitySelect('#city_filter_airport', 'Select City');
}

function initAirportQuickModalFields() {
	if (_aqModalInitialized) {
		return;
	}
	_aqModalInitialized = true;

	var $modal = $('#AirportQuickSave_modal');

	initQuickModalCitySelect('#city_filter_airport', 'Select City');
	bindQuickModalCityPlaceholderRefresh($modal, '#city_filter_airport', 'Select City');

	$('#btn_aq_save').off('click.aq').on('click.aq', function () {
		airportQuickSave();
	});

	$modal.off('hidden.bs.modal.aq').on('hidden.bs.modal.aq', function () {
		var $thisModal = $(this);
		var sectorSelectId = $thisModal.data('trigger-sector-id') || '';
		var targetInputId = $thisModal.data('trigger-input-id') || '';
		var savedLabel = $thisModal.data('saved-label') || '';
		var savedCityId = $thisModal.data('saved-city-id') || '';

		if (savedLabel) {
			if (sectorSelectId) {
				reloadAirportAfterQuickSave(sectorSelectId, savedLabel, savedCityId);
			} else if (targetInputId) {
				applyAirportQuickSaveToInput(targetInputId, savedLabel, savedCityId);
			}
		}

		$thisModal.removeData('saved-label');
		$thisModal.removeData('saved-city-id');
	});
}

function openAirportQuickModal(options) {
	options = options || {};

	ensureAirportQuickModal(function () {
		var $modal = $('#AirportQuickSave_modal');
		$modal.data('trigger-sector-id', options.sectorSelectId || '');
		$modal.data('trigger-input-id', options.targetInputId || '');

		resetAirportQuickForm();

		if (options.cityId) {
			selectCityInLazyDropdown($('#city_filter_airport'), options.cityId, options.cityName || '');
		}

		$modal.modal('show');
	});
}

function airportQuickSave() {
	var base_url = $('#base_url').val();
	var city_id = $('#city_filter_airport').val();
	var airport_name = ($('#aq_airport_name').val() || '').trim();
	var airport_code = ($('#aq_airport_code').val() || '').trim().toUpperCase();

	if (!city_id) {
		error_msg_alert('Select city name.');
		return false;
	}
	if (!airport_name.replace(/\s/g, '').length) {
		error_msg_alert('Enter airport name.');
		return false;
	}
	if (!airport_code.replace(/\s/g, '').length) {
		error_msg_alert('Enter airport code.');
		return false;
	}

	$('#btn_aq_save').button('loading');
	$.post(
		base_url + 'controller/other_masters/airports/airport_quick_save_c.php',
		{
			city_id: city_id,
			airport_name: airport_name,
			airport_code: airport_code
		},
		function (data) {
			var res;
			try {
				res = typeof data === 'object' ? data : JSON.parse(data);
			} catch (e) {
				error_msg_alert('Unexpected response from server.');
				$('#btn_aq_save').button('reset');
				return;
			}

			if (res.status === 'error') {
				error_msg_alert(res.message);
				$('#btn_aq_save').button('reset');
				return;
			}

			success_msg_alert(res.message || 'Airport has been successfully saved.');
			if (typeof update_b2c_cache === 'function') {
				update_b2c_cache();
			}

			$('#AirportQuickSave_modal')
				.data('saved-label', res.label || '')
				.data('saved-city-id', res.city_id || '')
				.modal('hide');

			$('#btn_aq_save').button('reset');
		}
	).fail(function () {
		error_msg_alert('Failed to save airport. Please try again.');
		$('#btn_aq_save').button('reset');
	});
}

function getSectorRowSuffix(sectorId) {
	if (!sectorId) {
		return '';
	}
	var match = sectorId.match(/^from_sector-(.+)$/);
	if (match) {
		return match[1];
	}
	match = sectorId.match(/^to_sector-(.+)$/);
	if (match) {
		return match[1];
	}
	return sectorId.replace(/^from_sector/, '').replace(/^to_sector/, '');
}

function syncSectorCityHidden(sectorSelectId, cityId) {
	if (!sectorSelectId || !cityId) {
		return;
	}
	var suffix = getSectorRowSuffix(sectorSelectId);
	if (sectorSelectId.indexOf('from_sector') === 0) {
		$('#from_city-' + suffix).val(cityId);
	} else if (sectorSelectId.indexOf('to_sector') === 0) {
		$('#to_city-' + suffix).val(cityId);
	}
}

function selectAirportInSector($select, label, cityId) {
	if (!$select || !$select.length || !label) {
		return;
	}
	var option = new Option(label, label, true, true);
	$select.append(option).trigger('change');
	if (cityId) {
		syncSectorCityHidden($select.attr('id') || '', cityId);
	}
}

function reloadAirportAfterQuickSave(sectorSelectId, savedLabel, savedCityId) {
	if (!sectorSelectId || !savedLabel) {
		return;
	}
	var $select = $('#' + sectorSelectId);
	if (!$select.length) {
		return;
	}
	selectAirportInSector($select, savedLabel, savedCityId);
}

function applyAirportQuickSaveToInput(inputId, savedLabel, savedCityId) {
	if (!inputId || !savedLabel) {
		return;
	}
	var $input = $('#' + inputId);
	if (!$input.length) {
		return;
	}
	$input.val(savedLabel);

	var suffix = inputId.replace(/^airpf-?/, '').replace(/^airpt-?/, '');
	if (inputId.indexOf('airpf') === 0) {
		$('#from_city-' + suffix).val(savedCityId || '');
		var airportPart = savedLabel.split(' - ')[1] || savedLabel;
		$('#departure_city-' + suffix).val(airportPart);
	} else if (inputId.indexOf('airpt') === 0) {
		$('#to_city-' + suffix).val(savedCityId || '');
		var airportPartArr = savedLabel.split(' - ')[1] || savedLabel;
		$('#arrival_city-' + suffix).val(airportPartArr);
	}
}

function getPlaneAirportSelect2ResultsEl($select) {
	var s2 = $select.data('select2');
	if (s2 && s2.$dropdown) {
		return s2.$dropdown.find('.select2-results');
	}
	return $select.next('.select2-container').find('.select2-results');
}

function appendAirportSectorAddNewButton($select) {
	var $results = getPlaneAirportSelect2ResultsEl($select);
	if (!$results.length) {
		return;
	}

	var selectKey = $select.attr('id') || 'sector';
	var ns = 'airportSectorAddNew_' + selectKey;

	$results.find('.add-new-option-airport').remove();

	var $btn = $('<div class="add-new-option add-new-option-airport" role="button" tabindex="0"></div>')
		.css({
			padding: '10px',
			cursor: 'pointer',
			background: '#f2f2f2',
			borderTop: '1px solid #ddd',
			display: 'flex',
			alignItems: 'center',
			gap: '6px',
			position: 'relative',
			zIndex: 9999,
			pointerEvents: 'auto'
		})
		.html(
			'<svg xmlns="http://www.w3.org/2000/svg" height="12" width="10.5" viewBox="0 0 448 512">' +
			'<path fill="rgb(103, 107, 174)" d="M256 64c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 160-160 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l160 0 0 160c0 17.7 14.3 32 32 32s32-14.3 32-32l0-160 160 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-160 0 0-160z"/>' +
			'</svg><span>Add New</span>'
		);

	$results.append($btn);

	$btn.off('mousedown.' + ns + ' click.' + ns).on('mousedown.' + ns + ' click.' + ns, function (e) {
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
		handleAirportSectorAddNew($select);
		return false;
	});
}

function handleAirportSectorAddNew($select) {
	if (!$select || !$select.length) {
		return false;
	}

	var sectorId = $select.attr('id') || '';
	var suffix = getSectorRowSuffix(sectorId);
	var cityId = '';
	if (sectorId.indexOf('from_sector') === 0) {
		cityId = $('#from_city-' + suffix).val() || '';
	} else if (sectorId.indexOf('to_sector') === 0) {
		cityId = $('#to_city-' + suffix).val() || '';
	}

	if ($select.data('select2')) {
		$select.select2('close');
	}

	openAirportQuickModal({
		sectorSelectId: sectorId,
		cityId: cityId
	});
	return false;
}

function initAirportSectorAddNew(selector) {
	var $select = $(selector);
	if (!$select.length) {
		return;
	}

	var selectKey = $select.attr('id') || ('sector_sel_' + Math.random().toString(36).slice(2));
	var ns = 'airportSectorAddNew_' + selectKey;

	$select.attr('data-add-new-option', 'true');

	$select
		.off('select2:open.' + ns + ' select2:add_new.' + ns)
		.on('select2:open.' + ns, function () {
			setTimeout(function () { appendAirportSectorAddNewButton($select); }, 0);
			setTimeout(function () { appendAirportSectorAddNewButton($select); }, 80);
		})
		.on('select2:add_new.' + ns, function () {
			handleAirportSectorAddNew($select);
		});
}

function capturePlaneAirportSelect2Config($select, forceRefresh) {
	if (!forceRefresh && $select.data('pa-select2-config')) {
		return $.extend({}, $select.data('pa-select2-config'));
	}
	var config = {
		width: '100%',
		minimumInputLength: 1,
		placeholder: $select.data('sector-type') === 'from' ? '*From Sector' : '*To Sector',
		allowClear: true,
		minimumResultsForSearch: 0
	};
	var s2 = $select.data('select2');
	if (s2 && s2.options) {
		var opts = s2.options.options || s2.options;
		if (opts.width) {
			config.width = opts.width;
		}
		if (opts.dropdownParent) {
			config.dropdownParent = opts.dropdownParent;
		}
		if (opts.placeholder !== undefined) {
			config.placeholder = opts.placeholder;
		}
	}
	if ($select.attr('style') && $select.attr('style').indexOf('width') !== -1) {
		config.width = '100%';
	}
	if (!config.dropdownParent) {
		var $modal = $select.closest('.modal');
		config.dropdownParent = $modal.length ? $modal : $('body');
	}
	$select.data('pa-select2-config', $.extend({}, config));
	return config;
}

function buildPlaneAirportAjaxConfig() {
	return {
		url: getAirportListUrl(),
		dataType: 'json',
		type: 'GET',
		delay: 250,
		data: function (params) {
			return { request: params.term || '' };
		},
		processResults: function (data) {
			if (!data || !$.isArray(data)) {
				return { results: [] };
			}
			return {
				results: $.map(data, function (item) {
					return {
						id: item.value,
						text: item.value,
						city_id: item.city_id
					};
				})
			};
		},
		cache: true
	};
}

function preparePlaneAirportSelect($select) {
	$select.addClass('plane-airport-select');
	var currentVal = $select.val();
	var currentText = $select.find('option:selected').text();
	$select.empty();
	if (currentVal && currentText) {
		$select.append($('<option></option>').attr('value', currentVal).text(currentText));
	} else {
		$select.append($('<option></option>').attr('value', '').text(''));
	}
}

function initPlaneAirportSelect2(container) {
	var $selects;
	if (typeof container === 'string' && container) {
		$selects = $(container).find('select[id^="from_sector-"], select[id^="to_sector-"]');
	} else if (container && container.jquery) {
		$selects = container.find('select[id^="from_sector-"], select[id^="to_sector-"]');
	} else {
		$selects = $('select.plane-airport-select, select[id^="from_sector-"], select[id^="to_sector-"]');
	}

	$selects.each(function () {
		var $sel = $(this);
		preparePlaneAirportSelect($sel);

		if ($sel.data('select2')) {
			capturePlaneAirportSelect2Config($sel);
			$sel.select2('destroy');
		}

		var config = capturePlaneAirportSelect2Config($sel, true);
		config.ajax = buildPlaneAirportAjaxConfig();
		$sel.select2(config);
		initAirportSectorAddNew($sel);
	});
}

function refreshPlaneAirportSelect2In(container) {
	if (typeof initPlaneAirportSelect2 !== 'function') {
		return;
	}
	var $scope;
	if (container && container.jquery) {
		$scope = container;
	} else if (typeof container === 'string' && container) {
		$scope = $(container);
	} else {
		$scope = $(document);
	}
	if (!$scope.length) {
		return;
	}
	$scope.find('select[id^="from_sector-"], select[id^="to_sector-"]').each(function () {
		$(this).removeData('pa-select2-config');
	});
	initPlaneAirportSelect2($scope);
}

window.init_plane_airport_select2 = initPlaneAirportSelect2;

function isPlaneAirportSelectElement(el) {
	return el && el.nodeType === 1 && el.tagName === 'SELECT' && (/^from_sector/.test(el.id || '') || /^to_sector/.test(el.id || ''));
}

function getCellFormControl(cell) {
	if (!cell) {
		return null;
	}
	var $select = $(cell).find('select[id^="from_sector-"], select[id^="to_sector-"]').first();
	if ($select.length) {
		return $select[0];
	}
	$select = $(cell).find('select, input').first();
	if ($select.length) {
		return $select[0];
	}
	for (var i = 0; i < cell.childNodes.length; i++) {
		var node = cell.childNodes[i];
		if (node.nodeType === 1) {
			return node;
		}
	}
	return null;
}

function packageHotelCellControl(cell) {
	return getCellFormControl(cell);
}

function packageHotelCellValue(cell) {
	var el = packageHotelCellControl(cell);
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

function isPackageHotelRowChecked(row) {
	var chk = packageHotelCellControl(row.cells[0]);
	return !!(chk && chk.checked);
}

function getPackageHotelRowData(row) {
	return {
		city_name: packageHotelCellValue(row.cells[2]),
		hotel_name: packageHotelCellValue(row.cells[3]),
		hotel_type: packageHotelCellValue(row.cells[4]),
		total_days: packageHotelCellValue(row.cells[5]),
		hotel_entry_id: row.cells[6] ? packageHotelCellValue(row.cells[6]) : ''
	};
}

window.packageHotelCellControl = packageHotelCellControl;
window.packageHotelCellValue = packageHotelCellValue;
window.isPackageHotelRowChecked = isPackageHotelRowChecked;
window.getPackageHotelRowData = getPackageHotelRowData;

function event_airport_table_uses_select(tableId, fromCol, toCol) {
	var table = document.getElementById(tableId);
	if (!table || !table.rows.length) {
		return false;
	}
	var fromEl = getCellFormControl(table.rows[0].cells[fromCol]);
	return isPlaneAirportSelectElement(fromEl);
}

function event_airport(id, fromSectornum = 2, toSectornum = 3) { //driver function
	if (!id || !document.getElementById(id)) {
		return;
	}

	if (event_airport_table_uses_select(id, fromSectornum, toSectornum)) {
		initPlaneAirportSelect2('#' + id);
		return;
	}

	var table1 = document.getElementById(id);
	if (!table1 || !table1.rows) {
		return;
	}
	var rows = table1.rows;
	for (var i = 0; i < parseInt(rows.length); i++) {
		if (!rows[i].cells[fromSectornum] || !rows[i].cells[toSectornum]) {
			continue;
		}
		var fromEl = getCellFormControl(rows[i].cells[fromSectornum]);
		var toEl = getCellFormControl(rows[i].cells[toSectornum]);
		if (!fromEl || !toEl || typeof fromEl.getAttribute !== 'function' || typeof toEl.getAttribute !== 'function') {
			continue;
		}
		var id1 = fromEl.getAttribute('id');
		var id2 = toEl.getAttribute('id');
		if (!id1 || !id2) {
			continue;
		}

		var ids = [{ "dep": id1 }, { "arr": id2 }];
		try {
			airport_load_main(ids);
		}
		catch (e) {
			continue;
		}
	}
}
function airport_load_main(ids) {
	ids.forEach(function (id) {
		var object_id = Object.values(id)[0];
		var base_url = $('#base_url').val();
		$("#" + object_id).autocomplete({
			source: function (request, response) {
				$.ajax({
					method: 'get',
					url: base_url + '/view/visa_passport_ticket/ticket/home/airport_list.php',
					dataType: 'json',
					data: { request: request.term },
					success: function (data) {
						response(data);
					}
				});
			},
			select: function (event, ui) {
				// var substr_id =  object_id.substr(6);
				var substr_id = object_id.split('-')[1];
				if (Object.keys(id)[0] == 'dep') {
					$('#from_city-' + substr_id).val(ui.item.city_id);
				}
				else {
					$('#to_city-' + substr_id).val(ui.item.city_id);
				}
			},
			open: function (event, ui) {
				$(this).autocomplete("widget").css({
					"width": document.getElementById(object_id).offsetWidth
				});
			},
			minLength: 3,
			change: function (event, ui) {
				// Use suffix after first "-" so ids like from_sector-1_d map to from_city-1_d
				var substr_id = object_id.split('-').slice(1).join('-');
				var currentVal = $.trim($(this).val() || '');
				// Keep prefilled/saved airport values on blur when user did not pick from the list again
				if (!ui.item) {
					if (currentVal !== '') {
						return;
					}
					$(this).val('');
					if (Object.keys(id)[0] == 'dep') {
						$('#from_city-' + substr_id).val("");
					} else {
						$('#to_city-' + substr_id).val("");
					}
					error_msg_alert('Please select Airport from the list!!');
					$(this).css('border', '1px solid red;');
					return;
				}
				if (($('#' + ids[0].dep).val() == $("#" + ids[1].arr).val()) && $('#' + ids[0].dep).val() != '' && $('#' + ids[1].arr).val() != '') {
					$(this).val('');
					if (Object.keys(id)[0] == 'dep') {
						$('#from_city-' + substr_id).val("");
					} else {
						$('#to_city-' + substr_id).val("");
					}
					$(this).css('border', '1px solid red;');
					error_msg_alert('Same Arrival and Boarding Airport Not Allowed!!');
				}

			}
		}).data("ui-autocomplete")._renderItem = function (ul, item) {
			return $("<li disabled>")
				.append("<a>" + item.value.split(" -")[1] + "<br><b>" + item.value.split(" -")[0] + "<b></a>")
				.appendTo(ul);
		}
	});
}

function generic_tax_reflect_temp(src_id, desc_id, funct_call) {
	var offset = src_id.split('-');

	desc_id = desc_id + '' + offset[1];

	generic_tax_reflect(src_id, desc_id, funct_call, src_id);
}

//**Generic service tax reflect start**//

function generic_tax_reflect(src_id, desc_id, funct_call, offset = '', temp_data = '') {
	var taxation_id = $('#' + src_id).val();

	$.post(base_url() + 'view/load_data/generic_tax_reflect.php', { taxation_id: taxation_id }, function (data) {
		$('#' + desc_id).val(data);

		if (temp_data != '') {
			window[funct_call](offset, temp_data);
		}
		else {
			if (funct_call != '') {
				if (offset == '') {
					window[funct_call]();
				}
				else {
					window[funct_call](offset);
				}
			}
		}
	});
}

//**Generic service tax reflect end**//
//**PHP to Javascript date converter**//
function php_to_js_date_converter(dateString1) {
	var get_new = dateString1.split('-');

	var day = get_new[0];

	var month = get_new[1];

	var year = get_new[2];

	var dateString = month + '/' + day + '/' + year;

	tagText = dateString.replace(/-/g, '/');

	var new_date = new Date(tagText);

	return new_date;
}

//**Trim characters**//

String.prototype.trimChars = function (chars) {
	var l = 0;

	var r = this.length - 1;

	while (chars.indexOf(this[l]) >= 0 && l < r) l++;

	while (chars.indexOf(this[r]) >= 0 && r >= l) r--;

	return this.substring(l, r + 1);
};

function printdiv(printpage, tbl_id) {
	$('#' + tbl_id).dataTable().fnDestroy();

	var headstr = '<html><head><title></title></head><body>';

	var footstr = '</body>';

	var newstr = document.all.item(printpage).innerHTML;

	var oldstr = document.body.innerHTML;

	document.body.innerHTML = headstr + newstr + footstr;

	window.print();

	document.body.innerHTML = oldstr;

	$('#' + tbl_id).dataTable();

	return false;
}

function check_pdf_size(pdf_size, url, url1) {
	var pdf_size = $('#' + pdf_size).val();

	if (pdf_size == 'A4 Full Size') {
		window.open(url, '_blank');
	}
	else {
		window.open(url1, '_blank');
	}
}
//Print
function loadOtherPage(url) {
	$('<iframe>').hide().attr('src', url).appendTo('body');
	// window.href = url;
}

// function check_package_type(setup_package, module_name) {
// 	var base_url = $('#base_url').val();
// 	if (module_name == 'user') {
// 		$.ajax({
// 			type: 'POST',
// 			url: base_url + 'view/package_permission/user_permission.php',
// 			data: {},
// 			async: false,
// 			success: function (data1) {
// 				$('#user_count').val(data1);
// 			}
// 		});
// 	}
// 	if (module_name == 'branch') {
// 		$.ajax({
// 			type: 'POST',
// 			url: base_url + 'view/package_permission/branch_permission.php',
// 			data: {},
// 			async: false,
// 			success: function (data1) {
// 				$('#branch_count').val(data1);
// 			}
// 		});
// 	}
// }
// function remove_hidden_class() {
// 	$('#package_permission').addClass('hidden');
// }
// function display_description(type, entry_id) {
// 	var base_url = $('#base_url').val();
// 	$.post(base_url + 'view/load_data/module_description_modal.php', { entry_id: entry_id, type: type }, function (
// 		data
// 	) {
// 		$('#div_content_modal').html(data);
// 	});
// }




function exportHTML(url) {
    // Fetch the HTML content from the URL
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(htmlContent => {
            // Show the modal with fetched content
            document.getElementById('preview').innerHTML = htmlContent;
            //$('#contentModal').modal('show');

            // Add event listener for the download button
          
                var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' " +
                    "xmlns:w='urn:schemas-microsoft-com:office:word' " +
                    "xmlns='http://www.w3.org/TR/REC-html40'>" +
                    "<head><title>Export HTML to Word Document</title><meta charset='utf-8'><link href='https://fonts.googleapis.com/css?family=Noto+Sans' rel='stylesheet'><link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet'><link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'><link href='https://fonts.googleapis.com/css?family=Roboto:100,300,500' rel='stylesheet'><link rel='stylesheet' href='https://www.demo1.itoursdemo.co.in/crm/css/font-awesome-4.7.0/css/font-awesome.min.css'><link rel='stylesheet' type='text/css' href='https://www.demo1.itoursdemo.co.in/crm/css/bootstrap.min.css'><link rel='stylesheet' type='text/css' href='https://www.demo1.itoursdemo.co.in/crm/css/app/admin.php'><link rel='stylesheet' type='text/css' href='https://www.demo1.itoursdemo.co.in/crm/css/app/app.php'><link rel='stylesheet' media='print' href='https://www.demo1.itoursdemo.co.in/crm/css/print/quotationGeneric.php'><link rel='stylesheet' media='print' href='https://www.demo1.itoursdemo.co.in/crm/css/print/printQuotationfive/quotationPrint.php'><link rel='stylesheet' media='print' href='https://www.demo1.itoursdemo.co.in/crm/css/print/printQuotationfive/quotationPdf.css'><script src='https://www.demo1.itoursdemo.co.in/crm/js/jquery-3.1.0.min.js'></script><script src='https://www.demo1.itoursdemo.co.in/crm/js/jquery-ui.min.js'></script><script src='https://www.demo1.itoursdemo.co.in/crm/js/bootstrap.min.js'></script></head><body>";
                var footer = "</body></html>";
                var sourceHTML = header + htmlContent + footer;

                var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
                var fileDownload = document.createElement("a");
                document.body.appendChild(fileDownload);
                fileDownload.href = source;
                fileDownload.download = 'document.doc';
                fileDownload.click();
                document.body.removeChild(fileDownload);
            
        })
        .catch(error => {
            console.error('Error fetching the HTML:', error);
        });
}




function check_package_type(setup_package, module_name) {
	var base_url = $('#base_url').val();
	if (module_name == 'user') {
		$.ajax({
			type: 'POST',
			url: base_url + 'view/package_permission/user_permission.php',
			data: {},
			async: false,
			success: function (data1) {
				$('#user_count').val(data1);
			}
		});
	}
	if (module_name == 'branch') {
		$.ajax({
			type: 'POST',
			url: base_url + 'view/package_permission/branch_permission.php',
			data: {},
			async: false,
			success: function (data1) {
				$('#branch_count').val(data1);
			}
		});
	}
}
function remove_hidden_class() {
	$('#package_permission').addClass('hidden');
}
function display_description(type, entry_id) {
	var base_url = $('#base_url').val();
	$.post(base_url + 'view/load_data/module_description_modal.php', { entry_id: entry_id, type: type }, function (
		data
	) {
		$('#div_content_modal').html(data);
	});
}




function select_all_check(id, custom_package) {
	var checked = $('#' + id).is(':checked');
	// Select all
	if (checked) {
		$('.' + custom_package).each(function () {
			$(this).prop('checked', true);
		});
	}
	else {
		// Deselect All
		$('.' + custom_package).each(function () {
			$(this).prop('checked', false);
		});
	}
}

function show_password(password) {
	var x = document.getElementById(password);
	if (x.type === 'password') {
		x.type = 'text';
	}
	else {
		x.type = 'password';
	}
}

function pagination_load(
	dataset,
	columns,
	bg_stat = false,
	footer_string = false,
	pg_length = 20,
	table_id = 'tbl_list',
	desc_filter = false
) {
	//1. dataset,2.columns titles,3.if want bg color,4.if want footer,5.manual pagelength change
	var html = '';
	var dataset_main;
	if (typeof dataset === 'object' && dataset !== null) {
		dataset_main = Array.isArray(dataset) ? dataset : [];
	} else {
		if (!dataset || (typeof dataset === 'string' && !$.trim(dataset))) {
			dataset = '[]';
		}
		try {
			dataset_main = JSON.parse(dataset);
		} catch (e) {
			console.error('pagination_load: invalid JSON response', e, dataset);
			dataset_main = [];
		}
	}
	if (!Array.isArray(dataset_main)) {
		dataset_main = [];
	}
	if (bg_stat) {
		var table_data = [];
		var bg = [];
		$.each(dataset_main, function (i, item) {
			table_data.push(dataset_main[i].data); //+ keeping different arrays for data and background color
			bg.push(dataset_main[i].bg);
		});
		table_data = JSON.parse(JSON.stringify(table_data));
	}
	else {
		var table_data = dataset_main;
	}
	if (footer_string) {
		table_data.pop();
		if ($.trim($('#' + table_id + ' tfoot').html())) {
			document.getElementById(table_id).deleteTFoot();
		}
		for (var i = 0; i < parseInt(dataset_main[dataset_main.length - 1].footer_data['total_footers']); i++) {
			html +=
				'<th class=" ' +
				dataset_main[dataset_main.length - 1].footer_data['class' + i] +
				' " colspan=\'' +
				dataset_main[dataset_main.length - 1].footer_data['col' + i] +
				"'>" +
				dataset_main[dataset_main.length - 1].footer_data['foot' + i] +
				'</th>';
		}
		html = '<tfoot><tr>' + html + '</tr></tfoot>';
	}
	if ($.fn.DataTable.isDataTable('#' + table_id)) {
		$('#' + table_id).DataTable().clear().destroy(); // for managin error
	}
	if(desc_filter == true)
	{
		order_value = 'desc'; 
	}
	else
	{
		order_value = 'asc'; 

	}
	var table = $('#' + table_id).DataTable({
	
		data: table_data,
		pageLength: pg_length,
		columns: columns,
		searching: true,
		// "scrollX": true,
		order: [[0, order_value]],
		createdRow: function (row, data, dataIndex) {
			// adds bg color for every invalid point
			if (bg_stat) $(row).addClass(bg[dataIndex]);
			var cellNode = row.cells[1] && row.cells[1].childNodes[0];
			if (cellNode && cellNode.nodeName === 'INPUT') {
				$(cellNode).labelauty({ label: false, maximum_width: '20px' });
			}
		},
		initComplete: function (settings, json) {
			$("[data-toggle='tooltip']").tooltip({ placement: 'bottom' });
			$("[data-toggle='tooltip']").click(function () {
				$('.tooltip').remove();
			});
		},
		lengthMenu: [
			[
				10,
				20,
				30,
				-1
			],
			[
				'10',
				'20',
				'30',
				'Show all'
			]
		],
		buttons: [
			'pageLength'
		]
	});
	$('#' + table_id).append(html);
	return table;
}
function convert_date_to_db(date) {
	var parts = date.split('-');
	date = parts[2] + '-' + parts[1] + '-' + parts[0];
	return Date.parse(date);
}

function get_other_rules(travel_type, booking_date) {
	var cache_rules = JSON.parse($('#cache_data').val());

	var invoice_date = $('#' + booking_date).val();
	if(invoice_date!== undefined){
		invoice_date = convert_date_to_db(invoice_date);
	}

	var other_rules = cache_rules[0]['other_rules'];
	var service_charge_result = [];
	var markup_result = [];
	var commission_result = [];

	//Filter rules Eg. Rule for == 'Service charge', Travel type='Hotel&All', Validity='Permanent||Period(from-to date)' //Service charge rules
	service_charge_result = other_rules.filter((rule) => {
		var from_date = new Date(rule['from_date']).getTime();
		var to_date = new Date(rule['to_date']).getTime();
		return (
			rule['rule_for'] === '1' &&
			rule['status'] === 'Active' &&
			(rule['travel_type'] === travel_type || rule['travel_type'] === 'All') &&
			(rule['validity'] == 'Permanent' || (invoice_date >= from_date && invoice_date <= to_date))
		);
	});

	//Markup rules
	markup_result =
		other_rules &&
		other_rules.filter((rule) => {
			var from_date = new Date(rule['from_date']).getTime();
			var to_date = new Date(rule['to_date']).getTime();

			return (
				rule['rule_for'] === '2' &&
				rule['status'] === 'Active' &&
				(rule['travel_type'] === travel_type || rule['travel_type'] === 'All') &&
				(rule['validity'] == 'Permanent' || (invoice_date >= from_date && invoice_date <= to_date))
			);
		});

	//Commission rules
	commission_result = other_rules.filter((rule) => {
		var from_date = new Date(rule['from_date']).getTime();
		var to_date = new Date(rule['to_date']).getTime();

		return (
			rule['rule_for'] === '3' &&
			rule['status'] === 'Active' &&
			(rule['travel_type'] === travel_type || rule['travel_type'] === 'All') &&
			(rule['validity'] == 'Permanent' || (invoice_date >= from_date && invoice_date <= to_date))
		);
	});

	//Taxes
	var taxes = cache_rules[0]['taxes'];
	taxes = taxes.filter((tax) => {
		return tax['status'] === 'Active';
	});

	//Tax Rules
	var tax_rules = cache_rules[0]['tax_rules'];
	tax_rules = tax_rules.filter((rule) => {
		var from_date = new Date(rule['from_date']).getTime();
		var to_date = new Date(rule['to_date']).getTime();

		return (
			rule['status'] === 'Active' &&
			(rule['travel_type'] === travel_type || rule['travel_type'] === 'All') &&
			(rule['validity'] == 'Permanent' || (invoice_date >= from_date && invoice_date <= to_date))
		);
	});

	var result = service_charge_result.concat(markup_result, commission_result, taxes, tax_rules);
	return result;
}
function update_cache() {
	var base_url = $('#base_url').val();
	$.post(base_url + 'model/update_cache.php', {}, function (data) {
		$('#cache_data').val(data);
	});
}

function update_b2c_cache() {
	var b2c_flag = $('#b2c_flag').val();
	if (b2c_flag === '1') {
		var base_url = $('#base_url').val();
		$.post(base_url + 'model/update_b2c_cache.php', {}, function (data) {
			return false;
		});
	}
}
function get_identifier_block(identifier, payment_mode, credit_card_details, credit_charges) {

	var payment_mode = $('#' + payment_mode).val();
	if (payment_mode === 'Credit Card') {
		document.getElementById(identifier).classList.remove("hidden");
		document.getElementById("identifier").innerHTML = '';
		var select = document.getElementById("identifier");
		select.options[select.options.length] = new Option('Select Identifier', '');

		var cache_rules = JSON.parse($('#cache_data').val());
		var credit_card_company = cache_rules[0]['credit_card_data'];

		credit_card_company && credit_card_company.filter((data) => {

			var card_memberships = [];
			card_memberships = JSON.parse(JSON.parse(data['membership_details_arr']));
			card_memberships.forEach(function (membership_no) {

				var identifiers = membership_no['nos'];
				identifiers && identifiers.map((i) => {
					let i1 = i.substring(0, 4);
					select.options[select.options.length] = new Option(i1, i1);
				});
			});
		});
	}
	else {
		document.getElementById(identifier).classList.add("hidden");
		document.getElementById(credit_card_details).classList.add("hidden");
		document.getElementById(credit_charges).classList.add("hidden");
	}
	document.getElementById(identifier).value = '';
	document.getElementById(credit_card_details).value = '';
	document.getElementById(credit_charges).value = '';
}
function get_credit_card_data(identifier, payment_mode, credit_card_details) {

	var identifier = $('#' + identifier).val();
	var payment_mode = $('#' + payment_mode).val();
	var cache_rules = JSON.parse($('#cache_data').val());
	var credit_card_company = cache_rules[0]['credit_card_data'];

	var identifiers1 = '';
	credit_card_company && credit_card_company.filter((data) => {

		var card_memberships = [];
		card_memberships = JSON.parse(JSON.parse(data['membership_details_arr']));
		card_memberships.forEach(function (membership_no) {

			var identifiers = membership_no['nos'];
			identifiers && identifiers.map((i) => {
				let i1 = i.substring(0, 4);
				if (identifier === i1)
					identifiers1 = data['entry_id'] + '-' + data['company_name'] + ':' + membership_no['membership_no'] + ':' + i;
			});
		});
	});
	if (payment_mode === 'Credit Card') {

		if (identifiers1 !== '') {
			document.getElementById(credit_card_details).classList.remove("hidden");
			document.getElementById('credit_card_details').value = identifiers1;
		} else {
			document.getElementById(credit_card_details).value = '';
			document.getElementById(credit_card_details).classList.add("hidden");
		}
	} else {
		document.getElementById(credit_card_details).value = '';
		document.getElementById(credit_card_details).classList.add("hidden");
	}
}

function get_credit_card_charges(identifier, payment_mode, payment_amount, credit_card_details, credit_charges) {

	var credit_card_charges = $('#credit_card_charges').val();
	var payment_mode = $('#' + payment_mode).val();
	var payment_amount = $('#' + payment_amount).val();
	if (payment_mode === 'Credit Card') {
		var result = payment_amount * (credit_card_charges / 100);
		document.getElementById(credit_charges).classList.remove("hidden");
		// document.getElementById(credit_card_details).classList.add("form-control");
		result = parseFloat(result).toFixed(2);
		document.getElementById(credit_charges).value = result;
	} else {
		document.getElementById(credit_charges).value = '';
		document.getElementById(credit_charges).classList.add("hidden");
		document.getElementById(credit_card_details).value = '';
		document.getElementById(credit_card_details).classList.add("hidden");
		document.getElementById(identifier).value = '';
		document.getElementById(identifier).classList.add("hidden");
	}
}


function get_identifier_block1(identifier, payment_mode, credit_card_details, credit_charges,tax_credit_charges) {

	var payment_mode = $('#' + payment_mode).val();
	if (payment_mode === 'Credit Card') {
		document.getElementById(identifier).classList.remove("hidden");

		
		
		document.getElementById("identifier").innerHTML = '';
		var select = document.getElementById("identifier");
		select.options[select.options.length] = new Option('Select Identifier', '');

		var cache_rules = JSON.parse($('#cache_data').val());
		var credit_card_company = cache_rules[0]['credit_card_data'];

		credit_card_company && credit_card_company.filter((data) => {

			var card_memberships = [];
			card_memberships = JSON.parse(JSON.parse(data['membership_details_arr']));
			card_memberships.forEach(function (membership_no) {

				var identifiers = membership_no['nos'];
				identifiers && identifiers.map((i) => {
					let i1 = i.substring(0, 4);
					select.options[select.options.length] = new Option(i1, i1);
				});
			});
		});
	}
	else {
		document.getElementById(identifier).classList.add("hidden");
		document.getElementById(credit_card_details).classList.add("hidden");
		document.getElementById(credit_charges).classList.add("hidden");
        document.getElementById(tax_credit_charges).classList.add("hidden");
		
	}
	document.getElementById(identifier).value = '';
	document.getElementById(credit_card_details).value = '';
	document.getElementById(credit_charges).value = '';
	document.getElementById(tax_credit_charges).value='';
}
function get_credit_card_data1(identifier, payment_mode, credit_card_details,tax_credit_charges) {

	var identifier = $('#' + identifier).val();
	var payment_mode = $('#' + payment_mode).val();
	var cache_rules = JSON.parse($('#cache_data').val());
	var credit_card_company = cache_rules[0]['credit_card_data'];

	var identifiers1 = '';
	credit_card_company && credit_card_company.filter((data) => {

		var card_memberships = [];
		card_memberships = JSON.parse(JSON.parse(data['membership_details_arr']));
		card_memberships.forEach(function (membership_no) {

			var identifiers = membership_no['nos'];
			identifiers && identifiers.map((i) => {
				let i1 = i.substring(0, 4);
				if (identifier === i1)
					identifiers1 = data['entry_id'] + '-' + data['company_name'] + ':' + membership_no['membership_no'] + ':' + i;
			});
		});
	});
	if (payment_mode === 'Credit Card') {

		if (identifiers1 !== '') {
			document.getElementById(credit_card_details).classList.remove("hidden");
			document.getElementById('credit_card_details').value = identifiers1;

			 document.getElementById(tax_credit_charges).classList.remove("hidden");
		} else {
			document.getElementById(credit_card_details).value = '';
			document.getElementById(credit_card_details).classList.add("hidden");
		}
	} else {
		document.getElementById(credit_card_details).value = '';
		document.getElementById(credit_card_details).classList.add("hidden");
	}
}



function get_credit_card_charges1(identifier, payment_mode, payment_amount, credit_card_details, credit_charges, tax_credit_charges, credit_charges_amt) {

    var payment_mode_val = $('#' + payment_mode).val();
    var payment_amount_val = parseFloat($('#' + payment_amount).val()) || 0;
    var credit_card_charges_val = parseFloat($('#' + credit_charges).val()) || 0;

    if (payment_mode_val === 'Credit Card') {
        // Calculate charges = amount × %
        var result = (payment_amount_val * (credit_card_charges_val / 100)).toFixed(2);

        // Show fields
        document.getElementById(credit_charges).classList.remove("hidden");
        document.getElementById(tax_credit_charges).classList.remove("hidden");

        // Don’t overwrite user input
        // document.getElementById(credit_charges).value = '0.00'; ❌ removed
        // document.getElementById(tax_credit_charges).value = '0.00'; ❌ removed
// alert(result);
        // Set calculated charges
        document.getElementById(credit_charges_amt).value = result;
    } else {
        // Reset + hide
        document.getElementById(credit_charges).value = '';
        document.getElementById(credit_charges).classList.add("hidden");
        document.getElementById(credit_card_details).value = '';
        document.getElementById(credit_card_details).classList.add("hidden");
        document.getElementById(identifier).value = '';
        document.getElementById(identifier).classList.add("hidden");
        document.getElementById(tax_credit_charges).classList.add("hidden");
    }
}

function get_credit_card_charge_tax(credit_charges_amt,tax_credit_charges,tax_credit_charges_amt){

	var credit_charges_amt= parseFloat($('#'+credit_charges_amt).val())|| 0;

	var tax_credit_charges = parseFloat($('#'+tax_credit_charges).val())|| 0;

	var tax_amt= (credit_charges_amt *(tax_credit_charges /100)).toFixed(2);

	// var result = (payment_amount_val * (credit_card_charges_val / 100)).toFixed(2);

	// document.getElementById(tax_credit_charges_amt).classList.remove("hidden");
    
	// alert(tax_amt);
        document.getElementById(tax_credit_charges_amt).value = tax_amt;

}

function check_updated_amount(payment_old_value, payment_amount) {

	if (parseFloat(payment_old_value) !== parseFloat(payment_amount)) {
		if (payment_amount != 0) return false;
		else return true;
	} else {
		return true;
	}
}
function resolveItineraryImageKeyFromSpa(spaFieldId) {
	if (!spaFieldId) {
		return '';
	}

	var $spa = $('#' + spaFieldId);
	if ($spa.length) {
		var $row = $spa.closest('tr');
		if ($row.length) {
			var $preview = $row.find('img[id^="preview_img_"]').first();
			if ($preview.length) {
				return String($preview.attr('id') || '').replace(/^preview_img_/, '');
			}
			var $hidden = $row.find('input[id^="existing_image_path_"], input[id^="day_image_path_"]').first();
			if ($hidden.length) {
				return String($hidden.attr('id') || '')
					.replace(/^existing_image_path_/, '')
					.replace(/^day_image_path_/, '');
			}
			var $file = $row.find('input[type="file"][id^="day_image_"]').first();
			if ($file.length) {
				return String($file.attr('id') || '').replace(/^day_image_/, '');
			}
		}
	}

	var match = String(spaFieldId).match(/(?:special_attaraction|special_attraction)(.+?)(?:-u)?$/i);
	return match ? String(match[1]).replace(/-u$/, '') : '';
}

function buildItineraryImagePreviewUrl(imgPath) {
	if (!imgPath || imgPath === 'NULL') {
		return '';
	}
	var path = String(imgPath).trim();
	if (path.indexOf('http') === 0) {
		return path;
	}
	var base = ($('#base_url').val() || '').replace(/\/crm\/?$/, '').replace(/\/$/, '');
	if (!base && window.location.href.indexOf('/crm/') > -1) {
		base = window.location.href.substring(0, window.location.href.indexOf('/crm/'));
	}
	return base + '/' + path.replace(/^\//, '');
}

function applySelectedItineraryImagePreview() {
	var data = window.selectedItineraryImage;
	if (!data || !data.img) {
		return false;
	}

	var imageKey = data.imageKey || data.dayId || '';
	if (!imageKey) {
		return false;
	}

	var img = data.img;
	var imageUrl = buildItineraryImagePreviewUrl(img);

	$('#existing_image_path_' + imageKey + ', #day_image_path_' + imageKey).val(img);

	var previewImg = $('#preview_img_' + imageKey);
	var previewDiv = $('#day_image_preview_' + imageKey);
	var uploadContainer = $('#upload_btn_container_' + imageKey);

	if (!previewImg.length || !previewDiv.length) {
		console.warn('applySelectedItineraryImagePreview: preview not found for key', imageKey, 'spa:', data.spa);
		window.selectedItineraryImage = null;
		return false;
	}

	previewImg.attr('src', imageUrl).show();
	previewDiv.attr('style', 'display: block !important; margin-top: 5px;').show();
	if (uploadContainer.length) {
		uploadContainer.hide();
	}
	$('label[for="day_image_' + imageKey + '"]').hide();
	previewDiv.find('button[onclick*="removeDayImage"]').css('display', 'flex').show();

	window.selectedItineraryImage = null;
	return true;
}

window.resolveItineraryImageKeyFromSpa = resolveItineraryImageKeyFromSpa;
window.buildItineraryImagePreviewUrl = buildItineraryImagePreviewUrl;
window.applySelectedItineraryImagePreview = applySelectedItineraryImagePreview;

function resolveItineraryDestId(preferredFieldId) {
	if (preferredFieldId === 0 || preferredFieldId === '0') {
		return '0';
	}
	// Prefer explicit package/booking destination field only.
	// Do NOT fall back to list filter #dest_name (that mixes wrong destinations).
	var dest_id = preferredFieldId ? $('#' + preferredFieldId).val() : '';
	if (!dest_id || dest_id === '0') {
		dest_id = $('#dest_name_s').val();
	}
	if (!dest_id || dest_id === '0') {
		dest_id = $('#dest_name_u').val();
	}
	if (!dest_id || dest_id === '0') {
		dest_id = $('#dest_name2').val();
	}
	if (!dest_id || dest_id === '0') {
		dest_id = $('#booking_itinerary_dest_id').val();
	}
	return dest_id;
}

function sync_car_itinerary_count() {
	var count = $('#itinerary_data .sq_itinerary_count').first().val();
	if (count !== undefined && count !== '') {
		$('#sq_itinerary_c1').val(count);
	}
}

function get_dest_itinerary_car_rental(dest_id1) {
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

window.sync_car_itinerary_count = sync_car_itinerary_count;
window.get_dest_itinerary_car_rental = get_dest_itinerary_car_rental;
window.get_dest_itinerary_car_quotation = get_dest_itinerary_car_rental;
window.get_dest_itinerary_booking = get_dest_itinerary_car_rental;

function init_car_rental_itinerary_modal() {
	if (!$('#itinerary_detail_frm').length || !$('#itinerary_detail_modal').length) {
		return;
	}

	$.fn.modal.Constructor.prototype.enforceFocus = function() {};

	$('#itinerary_detail_frm').appendTo('body');

	if ($('#dest_ids1').length) {
		if (typeof initModalAppSelect2 === 'function') {
			initModalAppSelect2('#itinerary_detail_modal');
		} else {
			if ($('#dest_ids1').hasClass('select2-hidden-accessible')) {
				$('#dest_ids1').select2('destroy');
			}
			$('#dest_ids1').select2({
				dropdownParent: $('body'),
				width: '100%',
				minimumResultsForSearch: 0,
				placeholder: '*Destination',
				allowClear: true
			});
		}
	}

	if (typeof sync_car_itinerary_count === 'function') {
		sync_car_itinerary_count();
	} else {
		var loadedCount = $('#itinerary_data .sq_itinerary_count').first().val();
		if (loadedCount !== undefined && loadedCount !== '') {
			$('#sq_itinerary_c1').val(loadedCount);
		}
	}

	if ($('#default_program_list tbody tr').length && (!$('#sq_itinerary_c1').val() || $('#sq_itinerary_c1').val() === '0')) {
		$('#sq_itinerary_c1').val($('#default_program_list tbody tr').length);
	}

	$("input[type='checkbox']", '#itinerary_detail_modal').labelauty({ label: false, maximum_width: '20px' });

	var $form = $('#itinerary_detail_frm');
	if ($form.data('validator')) {
		$form.validate().destroy();
	}

	var initialCount = $form.data('sq-itinerary-c') || $('#sq_itinerary_c1').val() || 0;

	$form.validate({
		rules: {
			dest_names1: { required: true }
		},
		submitHandler: function () {
			var sq_itinerary_c = $('#sq_itinerary_c1').val() || initialCount;
			if (sq_itinerary_c != 0) {
				var dest_id = $('#dest_ids1').val();
				var spa = $('#spa').val();
				var dwp = $('#dwp').val();
				var ovs = $('#ovs').val();
				var meal = $('#meal').val();

				if (dest_id == '' || dest_id == 0) {
					error_msg_alert('Please select destination!');
					return false;
				}
				var table = document.getElementById('default_program_list');
				if (!table) {
					error_msg_alert('Please select destination and load itinerary!');
					return false;
				}
				var rowCount = table.rows.length;
				var count = 0;
				var selectedRow = null;
				for (var i = 0; i < rowCount; i++) {
					var row = table.rows[i];
					var checkbox = row.cells[0].querySelector('input[type="checkbox"]');
					if (checkbox && checkbox.checked) {
						count++;
						selectedRow = row;
					}
				}
				if (parseInt(count, 10) !== 1) {
					error_msg_alert('Please select one day program!');
					return false;
				}
				if (selectedRow) {
					var sp = selectedRow.cells[2].querySelector('input,textarea').value;
					var dwp1 = selectedRow.cells[3].querySelector('textarea,input').value;
					var os1 = selectedRow.cells[4].querySelector('input').value;
					var meal1 = selectedRow.cells[5].querySelector('select').value;
					$('#' + spa).val(sp);
					$('#' + dwp).val(dwp1);
					$('#' + ovs).val(os1);
					$('#' + meal).val(meal1);
				}
				$('#itinerary_detail_modal').modal('hide');
				if (typeof restore_car_quotation_parent_modal_scroll === 'function') {
					restore_car_quotation_parent_modal_scroll();
				} else if (typeof restore_car_rental_parent_modal_scroll === 'function') {
					restore_car_rental_parent_modal_scroll();
				}
			} else {
				error_msg_alert('You need to add itinerary for this destination first!');
				return false;
			}
		}
	});

	$('#btn_update').off('click.itinerarySubmit').on('click.itinerarySubmit', function (e) {
		e.preventDefault();
		$form.submit();
	});

	$('#itinerary_detail_modal').off('hidden.bs.modal.carRentalItinerary').on('hidden.bs.modal.carRentalItinerary', function () {
		if (typeof restore_car_quotation_parent_modal_scroll === 'function') {
			restore_car_quotation_parent_modal_scroll();
		} else if (typeof restore_car_rental_parent_modal_scroll === 'function') {
			restore_car_rental_parent_modal_scroll();
		}
	});

	$('#itinerary_detail_modal').modal('show');
}

window.init_car_rental_itinerary_modal = init_car_rental_itinerary_modal;

function add_itinerary(dest_id1, spa, dwp, ovs, dayp) {

	var day_id = dayp.split('-');
	var allowNoDest = (dest_id1 === 0 || dest_id1 === '0');

	$('#itinerary'+day_id[1]).prop('disabled',true);
	var base_url = $('#base_url').val();
	var dest_id = resolveItineraryDestId(dest_id1);
	if ((!dest_id || dest_id === '' || dest_id === '0') && !allowNoDest) {
		error_msg_alert('Please select destination!');
		$('#itinerary'+day_id[1]).prop('disabled',false);
		return false;
	}
	if (!dest_id || dest_id === '') {
		dest_id = 0;
	}
	$('#itinerary'+day_id[1]).button('loading');
	$.post(base_url + 'view/load_data/itinerary_modal.php', { dest_id: dest_id, spa: spa, dwp: dwp, ovs: ovs, dayp: dayp }, function (data) {
		$('#itinerary'+day_id[1]).button('reset');
		$('#itinerary'+day_id[1]).prop('disabled',false);
		$('#div_itinerary_modal').html(data);
	});
}
function get_dest_itinerary(dest_id1) {

	var base_url = $('#base_url').val();
	var dest_id = resolveItineraryDestId(dest_id1);
	if (!dest_id || dest_id === '' || dest_id === '0') {
		error_msg_alert('Please select destination!');
		$('#itinerary_data').html('');
		return false;
	}
	$.post(base_url + 'view/load_data/get_itinerary_data.php', { dest_id: dest_id }, function (data) {
		$('#itinerary_data').html(data);
	});
}
function vehicle_save_modal(vehicle_name1) {
	var base_url = $('#base_url').val();
	$.post(
		base_url + 'view/load_data/vehicle_save.php',
		{ vehicle_name_id: vehicle_name1 },
		function (data) {
			$('#vehicle_add_modal').html(data);
		}
	);
}
function customer_whatsapp_send(first_name, contact_no, email_id,company_name,cust_type) {

	var base_url = $('#base_url').val();
	$.post(base_url + 'controller/customer_master/whatsapp_send.php', { first_name: first_name, contact_no: contact_no, email_id: email_id ,company_name:company_name,cust_type:cust_type}, function (data) {
		window.open(data);
	});
}

function btnDisable(btnId)
{
	$('#'+btnId).attr('disabled',"disabled");
}
function btnEnable(btnId)
{
	
	$('#'+btnId).removeAttr('disabled');
}
function btnDisableEnable(id)
{
	
	btnDisable(id);
	setTimeout(function () {btnEnable(id)}, 1500);
	
}

