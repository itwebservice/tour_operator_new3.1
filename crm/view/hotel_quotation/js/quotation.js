function hqPad(n) {
	return (n < 10 ? '0' : '') + n;
}
function hqTodayStr() {
	var d = new Date();
	return hqPad(d.getDate()) + '-' + hqPad(d.getMonth() + 1) + '-' + d.getFullYear();
}
function hqTomorrowStr() {
	var d = new Date();
	d.setDate(d.getDate() + 1);
	return hqPad(d.getDate()) + '-' + hqPad(d.getMonth() + 1) + '-' + d.getFullYear();
}
function hqParseDmy(val) {
	if (!val) {
		return null;
	}
	var parts = String(val).split(' ')[0].split('-');
	if (parts.length < 3) {
		return null;
	}
	var d = new Date(parseInt(parts[2], 10), parseInt(parts[1], 10) - 1, parseInt(parts[0], 10));
	d.setHours(0, 0, 0, 0);
	return isNaN(d.getTime()) ? null : d;
}
function hqValidateNotPast(id) {
	var $el = $('#' + id);
	var val = $el.val();
	if (!val) {
		return true;
	}
	var picked = hqParseDmy(val);
	if (!picked) {
		return true;
	}
	var today = new Date();
	today.setHours(0, 0, 0, 0);
	if (picked < today) {
		error_msg_alert('Date cannot be past date');
		$el.val('');
		$el.css({ border: '1px solid red' });
		return false;
	}
	$el.css({ border: '1px solid #ddd' });
	return true;
}
function hqInitHotelDatepicker($els) {
	if (!$els || !$els.length || typeof $els.datetimepicker !== 'function') {
		return;
	}
	$els.each(function () {
		var $el = $(this);
		if ($el.data('datepicker')) {
			try { $el.datepicker('destroy'); } catch (e) {}
		}
		if ($el.data('xdsoft_datetimepicker')) {
			try { $el.datetimepicker('destroy'); } catch (e) {}
		}
		$el.datetimepicker({
			timepicker: false,
			format: 'd-m-Y',
			formatDate: 'd-m-Y',
			minDate: new Date(),
			parentID: 'body',
			scrollInput: false,
			scrollMonth: false,
			validateOnBlur: false
		});
		$el.prop('readonly', true);
	});
}
function hqSelect2Body($el, width) {
	if (!$el || !$el.length) {
		return;
	}
	if ($el.data('select2')) {
		$el.select2('destroy');
	}
	$el.select2({
		width: width || '160px',
		minimumResultsForSearch: 0,
		dropdownParent: $(document.body)
	});
}
function hqCitySelectBody($el) {
	if (!$el || !$el.length) {
		return;
	}
	var selectedVal = $el.val();
	var selectedText = $el.find('option:selected').text();
	var base_url = ($("#base_url").val() || '').replace(/\/?$/, '/');
	var url = base_url + 'view/load_data/generic_city_loading.php';
	if ($el.data('select2')) {
		$el.select2('destroy');
	}
	if (!$el.find('option[value=""]').length) {
		$el.prepend($("<option></option>").attr("value", "").text("City Name"));
	}
	$el.attr('data-lazy-city', 'true');
	$el.attr('data-lazy-select', 'true');
	$el.select2({
		placeholder: 'City Name',
		allowClear: true,
		minimumInputLength: 0,
		width: '160px',
		dropdownParent: $(document.body),
		ajax: {
			url: url,
			dataType: 'json',
			delay: 250,
			type: 'GET',
			data: function (params) {
				return {
					term: params.term || '',
					page: params.page || 0,
					valueasText: false
				};
			},
			processResults: function (data) {
				data = data || {};
				var pagination = data.pagination || { more: false };
				return {
					results: data.results || [],
					pagination: { more: !!pagination.more }
				};
			},
			cache: true
		}
	});
	if (selectedVal) {
		if (!$el.find('option[value="' + selectedVal + '"]').length) {
			$el.append(new Option(selectedText, selectedVal, true, true));
		} else {
			$el.val(selectedVal);
		}
	}
	if ($el.attr('data-add-new-option') === 'true' && typeof initCityAddNewInline === 'function') {
		initCityAddNewInline($el);
	}
}
function hqInitHotelRowWidgets(scope) {
	var $scope = scope ? $(scope) : $(document);
	$scope.find('select.city_master_dropdown').each(function () {
		hqCitySelectBody($(this));
	});
	$scope.find('select[id^="tour_type"]').each(function () {
		hqSelect2Body($(this), '145px');
	});
	$scope.find('select[id^="hotel_name"]').each(function () {
		hqSelect2Body($(this), '160px');
		if (typeof initHotelSelectAddNew === 'function') {
			initHotelSelectAddNew(this);
		}
	});
	$scope.find('select[id^="room_cat"]').each(function () {
		hqSelect2Body($(this), '162px');
		if (typeof initRoomCategoryAddNewInline === 'function') {
			initRoomCategoryAddNewInline(this);
		}
	});
	$scope.find('select[id^="meal_plan"]').each(function () {
		hqSelect2Body($(this), '145px');
	});
	hqInitHotelDatepicker($scope.find('.app_datepicker'));
}
function hqAddHotelRow(tableID, optionIndex) {
	var table = document.getElementById(tableID);
	if (!table) {
		return;
	}
	var tbody = table.tBodies.length ? table.tBodies[0] : table;
	var src = tbody.rows[tbody.rows.length - 1];
	if (!src) {
		return;
	}
	var row = src.cloneNode(false);
	var rowNum = tbody.rows.length + 1;
	var option = optionIndex || '1';
	var offset = option + '-' + rowNum;
	var tableIdAttr = tableID;
	var srcHotel = src.querySelector('select[id^="hotel_name"]');
	var hotelOnchange = (srcHotel && String(srcHotel.id).indexOf('-u_') !== -1)
		? "hotel_type_load1(this.id);hotel_type_load_cate1(this.id);get_hotel_cost('" + tableIdAttr + "');"
		: "hotel_type_load(this.id);get_hotel_cost('" + tableIdAttr + "');";

	for (var c = 0; c < src.cells.length; c++) {
		var newcell = row.insertCell(c);
		var oldCell = src.cells[c];
		if (oldCell.getAttribute('style')) {
			newcell.setAttribute('style', oldCell.getAttribute('style'));
		}
		if (oldCell.className) {
			newcell.className = oldCell.className;
		}
		if (c === 0) {
			newcell.innerHTML = '<input class="css-checkbox mg_bt_10" id="chk_program-' + offset + '" type="checkbox" onclick="get_hotel_cost(\'' + tableIdAttr + '\');" checked><label class="css-label" for="chk_program-' + offset + '"></label>';
			continue;
		}
		if (c === 1) {
			newcell.innerHTML = '<input maxlength="15" value="' + rowNum + '" type="text" name="username" placeholder="Sr. No." class="form-control mg_bt_10" disabled />';
			continue;
		}
		if (c === 2) {
			newcell.innerHTML = '<select name="tour_type-' + offset + '" id="tour_type-' + offset + '" style="width:145px;" title="Tour Type" class="form-control app_select2">' +
				'<option value="Domestic" selected>Domestic</option>' +
				'<option value="International">International</option></select>';
			continue;
		}
		if (c === 3) {
			newcell.innerHTML = '<select id="city_name-' + offset + '" name="city_name-' + offset + '" class="city_master_dropdown form-control" style="width:160px" onchange="hotel_name_list_load(this.id);" title="Select City Name" data-add-new-option="true"></select>';
			continue;
		}
		if (c === 4) {
			newcell.innerHTML = '<select id="hotel_name-' + offset + '" name="hotel_name-' + offset + '" onchange="' + hotelOnchange + '" class="form-control app_select2" style="width:160px" title="Select Hotel Name" data-add-new-option="true"><option value="">Hotel Name</option></select>';
			continue;
		}
		if (c === 5) {
			var srcRoom = oldCell.querySelector('select');
			var roomHtml = srcRoom ? srcRoom.innerHTML : '<option value="">Room Category</option>';
			newcell.innerHTML = '<select name="room_cat-' + offset + '" id="room_cat-' + offset + '" style="width:162px;" title="Room Category" class="form-control app_select2" onchange="get_hotel_cost(\'' + tableIdAttr + '\');" data-add-new-option="true">' + roomHtml + '</select>';
			var roomSel = newcell.querySelector('select');
			if (roomSel) {
				roomSel.selectedIndex = 0;
			}
			continue;
		}
		if (c === 6) {
			var srcMeal = oldCell.querySelector('select');
			var mealHtml = srcMeal ? srcMeal.innerHTML : '<option value="">Meal Plan</option>';
			newcell.innerHTML = '<select name="meal_plan-' + offset + '" id="meal_plan-' + offset + '" style="width:145px;" title="Meal Plan" class="form-control app_select2" onchange="get_hotel_cost(\'' + tableIdAttr + '\');">' + mealHtml + '</select>';
			var mealSel = newcell.querySelector('select');
			if (mealSel) {
				mealSel.selectedIndex = 0;
			}
			continue;
		}
		if (c === 7) {
            newcell.innerHTML = '<input type="text" style="width:150px;" class="app_datepicker" readonly id="check_in-' + offset + '" name="check_in-' + offset + '" placeholder="Check-In Date" title="Check-In Date" value="' + hqTodayStr() + '" onchange="hqValidateNotPast(this.id);get_auto_to_date(this.id);get_hotel_cost(\'' + tableIdAttr + '\');">';
			continue;
		}
		if (c === 8) {
			newcell.innerHTML = '<input type="text" style="width:150px;" class="app_datepicker" readonly id="check_out-' + offset + '" name="check_out-' + offset + '" placeholder="Check-Out Date" title="Check-Out Date" value="' + hqTomorrowStr() + '" onchange="hqValidateNotPast(this.id);calculate_total_nights(this.id);validate_validDates(this.id);get_hotel_cost(\'' + tableIdAttr + '\');">';
			continue;
		}
		if (c === 9) {
			newcell.innerHTML = '<input type="text" id="hotel_type-' + offset + '" name="hotel_type-' + offset + '" placeholder="Hotel Category" title="Hotel Category" style="width:150px" readonly>';
			continue;
		}
		if (c === 10) {
			newcell.innerHTML = '<input type="text" id="hotel_stay_days-' + offset + '" title="Total Nights" name="hotel_stay_days-' + offset + '" placeholder="Total Nights" onchange="validate_balance(this.id);" style="width:150px;" value="1" readonly>';
			continue;
		}
		if (c === 11) {
			newcell.innerHTML = '<input type="text" id="no_of_rooms-' + offset + '" title="Total Rooms" name="no_of_rooms-' + offset + '" placeholder="*Total Rooms" onchange="validate_balance(this.id);get_hotel_cost(\'' + tableIdAttr + '\');" style="width:120px">';
			continue;
		}
		if (c === 12) {
			newcell.innerHTML = '<input type="text" id="extra_bed-' + offset + '" name="extra_bed-' + offset + '" title="Extra Bed" placeholder="Extra Bed" onchange="validate_balance(this.id);get_hotel_cost(\'' + tableIdAttr + '\');" style="width:100px">';
			continue;
		}
		if (c === 13) {
			newcell.innerHTML = '<input type="number" id="hotel_cost-' + offset + '" name="hotel_cost-' + offset + '" placeholder="Hotel Cost" title="Hotel Cost" onchange="validate_balance(this.id)" style="width:100px;">';
			continue;
		}
		newcell.innerHTML = '';
	}

	tbody.appendChild(row);
	hqSelect2Body($('#tour_type-' + offset), '145px');
	hqCitySelectBody($('#city_name-' + offset));
	hqSelect2Body($('#hotel_name-' + offset), '160px');
	if (typeof initHotelSelectAddNew === 'function') {
		initHotelSelectAddNew('#hotel_name-' + offset);
	}
	hqSelect2Body($('#room_cat-' + offset), '162px');
	if (typeof initRoomCategoryAddNewInline === 'function') {
		initRoomCategoryAddNewInline($('#room_cat-' + offset)[0]);
	}
	hqSelect2Body($('#meal_plan-' + offset), '145px');
	hqInitHotelDatepicker($(row).find('.app_datepicker'));
}

function hqParseCustData() {
	var raw = $('#cust_data').val() || '[]';
	if (raw.indexOf('&quot;') !== -1 || raw.indexOf('&#039;') !== -1 || raw.indexOf('&amp;') !== -1) {
		raw = $('<textarea/>').html(raw).text();
	}
	try {
		var parsed = JSON.parse(raw);
		return Array.isArray(parsed) ? parsed : [];
	} catch (e) {
		return [];
	}
}
function hqFilterCustomerHints(list, term) {
	var q = String(term || '').toLowerCase();
	var out = [];
	if (!list || !list.length) {
		return out;
	}
	for (var i = 0; i < list.length && out.length < 25; i++) {
		var label = String(list[i].label || list[i].value || '');
		if (!q || label.toLowerCase().indexOf(q) !== -1) {
			out.push(list[i]);
		}
	}
	return out;
}
function hqInitCustomerAutocomplete(inputId, countryId, whatsappId, emailId) {
	var $input = $('#' + (inputId || 'customer_name'));
	if (!$input.length || typeof $input.autocomplete !== 'function') {
		return;
	}
	if ($input.data('ui-autocomplete')) {
		$input.autocomplete('destroy');
	}
	var countrySel = countryId || (inputId === 'customer_name1' ? 'country_code1' : 'country_code');
	var whatsappSel = whatsappId || (inputId === 'customer_name1' ? 'whatsapp_no1' : 'whatsapp_no');
	var emailSel = emailId || (inputId === 'customer_name1' ? 'email_id1' : 'email_id');
	if (!window.hqCustomerHints) {
		window.hqCustomerHints = hqParseCustData();
	}
	$input.autocomplete({
		appendTo: 'body',
		minLength: 1,
		source: function (request, response) {
			var local = window.hqCustomerHints || [];
			if (local.length) {
				response(hqFilterCustomerHints(local, request.term));
				return;
			}
			var base_url = ($('#base_url').val() || '').replace(/\/?$/, '/');
			$.getJSON(base_url + 'view/hotel_quotation/customer_hint.php', {
				branch_status: $('#branch_status').val() || ''
			}, function (data) {
				window.hqCustomerHints = Array.isArray(data) ? data : [];
				response(hqFilterCustomerHints(window.hqCustomerHints, request.term));
			}).fail(function () {
				response([]);
			});
		},
		select: function (event, ui) {
			$input.val(ui.item.label);
			if (ui.item.country_id) {
				var newOption = $("<option selected='selected'></option>").val(ui.item.country_id).text(ui.item.country_code || ui.item.country_id);
				$('#' + countrySel).append(newOption).trigger('change.select2');
			}
			$('#' + whatsappSel).val(ui.item.contact_no);
			$('#' + emailSel).val(ui.item.email_id);
			return false;
		},
		open: function () {
			$(this).autocomplete('widget').css({
				width: this.offsetWidth,
				'z-index': 100000
			});
		}
	});
	if ($input.data('ui-autocomplete')) {
		$input.data('ui-autocomplete')._renderItem = function (ul, item) {
			return $('<li>')
				.append($('<div>').text(item.label))
				.appendTo(ul);
		};
	}
}
$(function () {
	hqInitCustomerAutocomplete('customer_name');
	hqInitCustomerAutocomplete('customer_name1');
});

function total_passangers_calculate(offset = '') {
	var total_adult = $('#total_adult' + offset).val();
	var children_with_bed = $('#children_with_bed' + offset).val();
	var children_without_bed = $('#children_without_bed' + offset).val();
	var total_infant = $('#total_infant' + offset).val();

	if (total_adult == '') total_adult = 0;
	if (children_with_bed == '') children_with_bed = 0;
	if (children_without_bed == '') children_without_bed = 0;
	if (total_infant == '') total_infant = 0;


	var total_members = parseFloat(total_adult) + parseFloat(total_infant) + parseFloat(children_with_bed) + parseFloat(children_without_bed);
	$('#total_members' + offset).val(total_members);
}

function get_hotelenquiry_details(offset = '') {
	var enquiry_id = $('#enquiry_id' + offset).val();
	var base_url = $('#base_url').val();
	if(enquiry_id == 0){
		$('#customer_name' + offset).val('');
		$('#email_id' + offset).val('');
		$('#mobile_no' + offset).val('');
		$('#total_adult' + offset).val('');
		$('#total_infant' + offset).val('');
		$('#total_adult' + offset).val('');
		$('#children_without_bed' + offset).val('');
		$('#children_with_bed' + offset).val('');
		$('#hotel_requirements' + offset).wysiwyg("destroy")
		$('#hotel_requirements' + offset).val('');
		$('#hotel_requirements' + offset).wysiwyg({
			controls: 'bold,italic,|,undo,redo,image|h1,h2,h3,decreaseFontSize,highlight',
			initialContent: ''
		});
		$('#whatsapp_no' + offset).val('');
		$('#country_code' + offset).val('');
		$('#country_code' + offset).trigger('change');

		$('#total_members' + offset).val(0);
	}else{
		$.ajax({
			type: 'post',
			url: base_url + 'view/hotel_quotation/get_enquiry_details.php',
			dataType: 'json',
			data: { enquiry_id: enquiry_id },
			success: function (result) {
				
				$('#customer_name' + offset).val(result.name);
				$('#email_id' + offset).val(result.email_id);
				$('#total_adult' + offset).val(result.total_adult);
				$('#total_infant' + offset).val(result.total_infant);
				$('#total_adult' + offset).val(result.total_adult);
				$('#children_without_bed' + offset).val(result.total_cwob);
				$('#children_with_bed' + offset).val(result.total_cwb);
				$('#hotel_requirements' + offset).wysiwyg("destroy")
				$('#hotel_requirements' + offset).val(result.hotel_requirements);
				$('#hotel_requirements' + offset).wysiwyg({
					controls: 'bold,italic,|,undo,redo,image|h1,h2,h3,decreaseFontSize,highlight',
					initialContent: ''
				});
				var whatsapp = result.landline_no;
				var country_code = result.country_code;
				var ret = whatsapp.replace(country_code,'');
				$('#whatsapp_no' + offset).val(ret);
				$('#country_code' + offset).val(result.country_code);
				$('#country_code').trigger('change');

				if (result.total_adult === undefined || result.total_adult === '') result.total_adult = 0;
				if (result.total_infant === undefined || result.total_infant === '') result.total_infant = 0;
				if (result.total_cwob === undefined || result.total_cwob === '') result.total_cwob = 0;
				if (result.total_cwb === undefined || result.total_cwb === '') result.total_cwb = 0;

				var total_pax = parseFloat(result.total_adult) + parseFloat(result.total_cwob) + parseFloat(result.total_cwb) + parseFloat(result.total_infant);
				if (total_pax == '') total_pax = 0;
				$('#total_members' + offset).val(total_pax);
			},
		});
	}
}
var resetFieldToDefault = function(offset) {
    var selectedOptions = document.getElementById('country_code' + offset).selectedOptions;
    for(var i = 0; i < selectedOptions.length; i++)
        selectedOptions[i].selected = false;
}
function options_dynamic_reflect(id) {
	var nofquotation = $('#' + id).val();
	var base_url = $('#base_url').val();
	$('#' + id).css('border','1px solid #e2e2e2');
	if(nofquotation == '' || Number(nofquotation) > 7 || Number(nofquotation) <= 0){
		error_msg_alert('Please Enter Valid Number of Quotations');
		$('#' + id).val('');
		$('#' + id).css('border','1px solid red');
		return false;
	}
	$.ajax({
		type: 'get',
		url: base_url + 'view/hotel_quotation/get_options.php',
		data: { nofquotation: nofquotation },
		success: function (result) {
			$('#options_div').html(result);
		}
	});
}

function hotel_name_list_load(id){
  var city_id = $("#"+id).val();
  if (!city_id) {
    return;
  }
  var count = id.substring(9);
  var $hotel = $("#hotel_name-" + count);
  if (!$hotel.length) {
    $hotel = $("#hotel_name" + count);
  }
  if (typeof hotelDropdownLoadByCity === 'function') {
    hotelDropdownLoadByCity(city_id, $hotel);
    return;
  }
  var base_url = $('#base_url').val();
  $.get( base_url + "view/package_booking/quotation/home/hotel/hotel_name_load.php" , { city_id : city_id } , function ( data ) {
        if ($hotel.data('select2')) {
            $hotel.select2('destroy');
        }
        $hotel.html( data );
        $hotel.select2({ width: '160px', minimumResultsForSearch: 0, dropdownParent: $(document.body) });
        if (typeof captureHotelSelect2Config === 'function') {
            captureHotelSelect2Config($hotel);
        }
        initHotelSelectAddNew($hotel);
  }) ;   
}

function get_auto_to_date(from_date) {
	if (typeof hqValidateNotPast === 'function' && !hqValidateNotPast(from_date)) {
		return;
	}
	var from_date1 = $('#' + from_date).val();
	var offset = from_date.substring(8);
	if (from_date1 != '') {
		var edate = from_date1.split('-');
		e_date = new Date(edate[2], edate[1] - 1, edate[0]).getTime();
		var currentDate = new Date(new Date(e_date).getTime() + 24 * 60 * 60 * 1000);
		var day = currentDate.getDate();
		var month = currentDate.getMonth() + 1;
		var year = currentDate.getFullYear();
		if (day < 10) {
			day = '0' + day;
		}
		if (month < 10) {
			month = '0' + month;
		}
		$('#check_out' + offset).val(day + '-' + month + '-' + year);
	}
	else {
		$('#check_out' + offset).val('');
	}
	calculate_total_nights('check_out' + offset);
}

function calculate_total_nights(to_date1) {

	var offset = to_date1.substring(9);
	var from_date = $('#check_in' + offset).val();
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
		$('#hotel_stay_days' + offset).val(total_days);
	}
	else {
		$('#hotel_stay_days' + offset).val(0);
	}
}

//Get Hotel Cost
function get_hotel_cost(table_id){
	var hotel_id_arr = new Array();
	var room_cat_arr = new Array();
	var check_in_arr = new Array();
	var check_out_arr = new Array();
	var total_nights_arr = new Array();
	var meal_plan_arr = new Array();
	var total_rooms_arr = new Array();
	var extra_bed_arr = new Array();
	var hotel_cost_arr=new Array();
	
	var checked_arr = [];
	var package_id_arr = [];
	var child_with_bed = $('#children_with_bed').val(); 
	var child_without_bed = $('#children_without_bed').val(); 
	var adult_count = $('#total_adult').val(); 
	
    adult_count = (adult_count == '') ? 0 : adult_count;
    child_without_bed = (child_without_bed == '') ? 0 : child_without_bed;
    child_with_bed = (child_with_bed == '') ? 0 : child_with_bed;

	var table = document.getElementById(table_id);
	var rowCount = table.rows.length;

	for(var i=0; i<rowCount; i++){

		var row = table.rows[i];
		var hotel_id = row.cells[4].childNodes[0].value;
		var room_category = row.cells[5].childNodes[0].value;
		var meal_plan = row.cells[6].childNodes[0].value;
		var check_in = row.cells[7].childNodes[0].value;
		var check_out = row.cells[8].childNodes[0].value;
		var total_nights = row.cells[10].childNodes[0].value;
		var total_rooms = row.cells[11].childNodes[0].value;
		var extra_bed = row.cells[12].childNodes[0].value;
		hotel_id_arr.push(hotel_id);
		room_cat_arr.push(room_category);
		meal_plan_arr.push(meal_plan);
		check_in_arr.push(check_in);
		check_out_arr.push(check_out);
		total_nights_arr.push(total_nights);
		total_rooms_arr.push(total_rooms);
		extra_bed_arr.push(extra_bed);
		checked_arr.push(row.cells[0].childNodes[0].checked);
		package_id_arr.push(0);
	}
	var base_url = $('#base_url').val();
	$.ajax({
		type:'post',
		url: base_url+'view/package_booking/quotation/home/hotel/get_hotel_cost1.php',
		data:{ hotel_id_arr : hotel_id_arr,check_in_arr : check_in_arr,check_out_arr:check_out_arr,room_cat_arr:room_cat_arr,meal_plan_arr:meal_plan_arr,total_nights_arr:total_nights_arr,total_rooms_arr:total_rooms_arr,extra_bed_arr:extra_bed_arr,child_with_bed:child_with_bed,child_without_bed:child_without_bed,adult_count:adult_count,package_id_arr:package_id_arr,checked_arr:checked_arr },
		success:function(result){
			var hotel_arr = JSON.parse(result);
            if(hotel_arr.length === 0){

                for(var i=0; i<rowCount; i++){

                    var row = table.rows[i];
                    // row.cells[13].childNodes[0].value = 0;
                }
            }else{
					for(var i=0; i<hotel_arr.length; i++){
						var row = table.rows[i];
						if(row.cells[0].childNodes[0].checked){
							row.cells[13].childNodes[0].value = hotel_arr[i]['hotel_cost'];
						}
						// else{
						// 	row.cells[13].childNodes[0].value = 0;
						// }
					}
				
			}
		}
	});
}

function hotel_type_load(id){
	var hotel_id = $("#"+id).val();
	var base_url = $('#base_url').val();

	var count = id.substring(10);
	$.get( base_url + "view/package_booking/quotation/home/hotel/hotel_type_load.php" , { hotel_id : hotel_id } , function ( data ) {
		$("#hotel_type"+count).val( data ) ;  
	} ) ;   
	hotel_type_load_cate(id);
}

//roomcategory load
function hotel_type_load_cate(id)
{
	var hotel_id = $("#"+id).val();
	var base_url = $('#base_url').val();

	var count = id.substring(11);
	$.get( base_url + "view/package_booking/quotation/home/hotel/hotel_category.php" , { hotel_id : hotel_id } , function ( data ) {
			$ ("#room_cat-"+count).html( data ) ;
			if (typeof refreshRoomCategorySelectAfterLoad === 'function') {
				refreshRoomCategorySelectAfterLoad("#room_cat-" + count, { width: '162px' });
			}
	} ) ;
}



//update room category
function hotel_type_load1(id){
	var hotel_id = $("#"+id).val();
	var base_url = $('#base_url').val();

	var count = id.substring(10);
	$.get( base_url + "view/package_booking/quotation/home/hotel/hotel_type_load.php" , { hotel_id : hotel_id } , function ( data ) {
		$("#hotel_type"+count).val( data ) ;  
	} ) ;   
	
}
function hotel_type_load_cate1(id)
{
  var hotel_id = $("#"+id).val();
  var base_url = $('#base_url').val();

  var count = id.substring(11);
  $.get( base_url + "view/package_booking/quotation/home/hotel/hotel_category.php" , { hotel_id : hotel_id } , function ( data ) {
        $ ("#room_cat-"+count).html( data ) ;
        if (typeof refreshRoomCategorySelectAfterLoad === 'function') {
            refreshRoomCategorySelectAfterLoad("#room_cat-" + count, { width: '162px' });
        }
  } ) ;
}
function validate_validDates(to) {

	var offset =  to.substring(9);
	var from_date = $('#check_in' + offset).val();
	var to_date1 = $('#' + to).val();

	var edate = from_date.split('-');
	e_date = new Date(edate[2], edate[1] - 1, edate[0]).getTime();
	var edate1 = to_date1.split('-');
	e_date1 = new Date(edate1[2], edate1[1] - 1, edate1[0]).getTime();

	var from_date_ms = new Date(e_date).getTime();
	var to_date_ms = new Date(e_date1).getTime();

	if (from_date_ms > to_date_ms) {
		error_msg_alert('Date should not be greater than valid to date');
		$('#check_in' + offset).css({ border: '1px solid red' });
		$('#check_in' + offset).focus();
		g_validate_status = false;
		return false;
	}
	else {
		$('#check_in' + offset).css({ border: '1px solid #ddd' });
		return true;
	}
}

function cost_calculate(id){
	var offset = id.split('-')[1];
    var service_tax_subtotal = $('#tax_amount-'+offset).val();
    var basic_cost = $('#basic_cost-'+offset).val();   
    var service_charge = $('#service_charge-'+offset).val();
    var markup = $('#markup_cost-'+offset).val();
    var service_tax_markup = $('#tax_markup-'+offset).val();

	if(basic_cost==""){ basic_cost = 0; }
    if(service_charge==""){ service_charge = 0; }
    if(markup==""){ markup = 0; }
    
	var service_tax_amount = 0;
    if(parseFloat(service_tax_subtotal) !== 0.00 && (service_tax_subtotal) !== ''){

      var service_tax_subtotal1 = service_tax_subtotal.split(",");
      for(var i=0;i<service_tax_subtotal1.length;i++){
        var service_tax = service_tax_subtotal1[i].split(':');
        service_tax_amount = parseFloat(service_tax_amount) + parseFloat(service_tax[2]);
      }
    }
    
    var markupservice_tax_amount = 0;
    if(parseFloat(service_tax_markup) !== 0.00 && (service_tax_markup) !== ""){
      var service_tax_markup1 = service_tax_markup.split(",");
      for(var i=0;i<service_tax_markup1.length;i++){
        var service_tax = service_tax_markup1[i].split(':');
        markupservice_tax_amount = parseFloat(markupservice_tax_amount) + parseFloat(service_tax[2]);
      }
    }

	basic_cost = ($('#basic_show-'+offset).html() == '&nbsp;') ? basic_cost : parseFloat($('#basic_show-'+offset).text().split(' : ')[1]);
    service_charge = ($('#service_show-'+offset).html() == '&nbsp;') ? service_charge : parseFloat($('#service_show-'+offset).text().split(' : ')[1]);
    markup = ($('#markup_show-'+offset).html() == '&nbsp;') ? markup : parseFloat($('#markup_show-'+offset).text().split(' : ')[1]);
    
    customTcsTax(offset);
    
    var tcs_amt = $('#tcs-' + offset).val();
    if(tcs_amt=='')
    {
        tcs_amt=0;
    }
	var total_amount = Number(basic_cost) + Number(service_tax_amount) + Number(markupservice_tax_amount) + Number(service_charge) + Number(markup) + Number(tcs_amt);

	var total=total_amount.toFixed(2);
    var roundoff = Math.round(total)-total;
    $('#roundoff-'+offset).val(roundoff.toFixed(2));
    $('#total_amount-'+offset).val(parseFloat(total)+parseFloat(roundoff));
}

        
$(document).on("change",".tcs_tax_calculation",function() {
    var optionid=$(this).data("optionid");
    customTcsTax(optionid);
});

function customTcsTax(optionid)
{
    var tcs_tax=$("#tcs_tax-"+optionid).val();
    if(tcs_tax!=='')
    {
       var  subtotal=$("#basic_cost-"+optionid).val();
       var  service_charge=$("#service_charge-"+optionid).val();
       var  markup_cost=$("#markup_cost-"+optionid).val();
       
       var  service_tax_amount=0;
       var  tax_subtotal=$("#tax_amount-"+optionid).val();
       var  service_tax_subtotal1 = tax_subtotal.split(',');
	   for (var i = 0; i < service_tax_subtotal1.length; i++) {
		    var service_tax = service_tax_subtotal1[i].split(':');
		    service_tax_amount = parseFloat(service_tax_amount) + parseFloat(service_tax[2]);
	   }
	   
	    var markupservice_tax_amount = 0;
	    var service_tax_markup = $('#tax_markup-'+optionid).val();
        if(parseFloat(service_tax_markup) !== 0.00 && (service_tax_markup) !== ""){
          var service_tax_markup1 = service_tax_markup.split(",");
          for(var i=0;i<service_tax_markup1.length;i++){
            var service_tax = service_tax_markup1[i].split(':');
            markupservice_tax_amount = parseFloat(markupservice_tax_amount) + parseFloat(service_tax[2]);
          }
        }
	   
	  var  txt_actual_tour_cost1=$("#total_amount-"+optionid).val();

	   

       var tcsamount=parseFloat(parseFloat(markup_cost)+parseFloat(service_tax_amount)+parseFloat(subtotal)+parseFloat(service_charge)+parseFloat(markupservice_tax_amount))*parseFloat(tcs_tax)/100;
       var totalTcs=$("#tcs-"+optionid).val();
       if(totalTcs=='')
       {
        totalTcs=0;   
       }
       $("#tcs-"+optionid).val(tcsamount.toFixed(2));

       var txt_actual_tour_cost1total=parseFloat(parseFloat(markup_cost)+parseFloat(tcsamount)+parseFloat(service_tax_amount)+parseFloat(subtotal)+parseFloat(service_charge)+parseFloat(markupservice_tax_amount));
       console.log(txt_actual_tour_cost1total);
       $("#total_amount-"+optionid).val(txt_actual_tour_cost1total.toFixed(2));
    }
    else
    {
        var totalTcs=$("#tcs-"+optionid).val();
        $("#tcs-"+optionid).val(0.00);
        var txt_actual_tour_cost1=$("#total_amount-"+optionid).val();
        var txt_actual_tour_cost1total=parseFloat(txt_actual_tour_cost1)-parseFloat(totalTcs);
        $("#total_amount-"+optionid).val(txt_actual_tour_cost1total.toFixed(2));
    }    

}