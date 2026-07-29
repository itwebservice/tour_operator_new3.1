/////// Reflect how many seats are available /////////////////////////////////////////////////
function seats_availability_reflect() {
  var tour_id = $("#cmb_tour_name").val();
  var tour_group_id = $("#cmb_tour_group").val();

  if (tour_id == '' || tour_group_id == '') {
    document.getElementById("div_seats_availability").innerHTML = "";
    return false;
  }

  $.get('../inc/seats_availability_reflect.php', { tour_id: tour_id, tour_group_id: tour_group_id }, function (data) {
    $('#div_seats_availability').html(data);

  })
}


//////////////////Seats availability check start /////////////////////////////
function seats_availability_check() {
  var tour_id = $("#cmb_tour_name").val();
  var tour_group_id = $("#cmb_tour_group").val();

  $.get("../inc/seats_availability_check.php", { tour_id: tour_id, tour_group_id: tour_group_id }, function (data) {
    // data1 = data.trim();
    var tour_info_arr = JSON.parse(data);

    $('#txt_available_seats').val(tour_info_arr[0]['available_seats']);
    $('#txt_total_seats1').val(tour_info_arr[0]['total_seats']);
    $('#seats_booked').val(tour_info_arr[0]['seats_booked']);
    if (tour_info_arr[0]['available_seats'] == '0') {
      error_msg_alert("All the bookings are done in this tour.");
      return false;
      //window.location.href = '../index.php';
    }
    else {
      $('#txt_available_seats').val(tour_info_arr[0]['available_seats']);
      $('#txt_total_seats1').val(tour_info_arr[0]['total_seats']);
    }
  });




}
//////////////////Seats availability check end /////////////////////////////

//////////////////Due date reflect start/////////////////////////////
function due_date_reflect() {
  var text = $("#cmb_tour_group option:selected").text();
  var text_arr = text.split(' ');
  var start_date = text_arr[0].trim();
  var date_arr = start_date.split('-');

  var d = new Date();
  d.setDate(date_arr[0]);
  d.setMonth(date_arr[1] - 1);
  d.setFullYear(date_arr[2]);

  var yesterdayMs = d.getTime() - 1000 * 60 * 60 * 24; // Offset by one day;
  d.setTime(yesterdayMs);
  var month = d.getMonth() + 1;
  var day = d.getDate();
  if (day < 10) {
    day = '0' + day;
  }
  if (month < 10) {
    month = '0' + month;
  }

  var due_date = day + '-' + month + '-' + d.getFullYear();
  $('#txt_balance_due_date').val(due_date);
}

//////////////////Due date reflect end/////////////////////////////

//////////////////Tain and plane date reflect start/////////////////////////////

function tour_type_reflect(tour_id, offset = '') {
  var tour_id = $('#' + tour_id).val();
  $.post('../inc/tour_type_reflect.php', { tour_id: tour_id }, function (data) {

    if (data == "Domestic") {
      $('input[name="txt_m_passport_no"]').prop('disabled', true);
      $('input[name="txt_m_passport_issue_date"]').prop('disabled', true);
      $('input[name="txt_m_passport_expiry_date"]').prop('disabled', true);
    }

    else {
      $('input[name="txt_m_passport_no"]').prop('disabled', false);
      $('input[name="txt_m_passport_issue_date"]').prop('disabled', false);
      $('input[name="txt_m_passport_expiry_date"]').prop('disabled', false);
    }
    $('#tour_type_r').val(data);
  });
}

/////Traveling dates validation///////
function validate_travelingDates(id) {
  var group_id = $('#cmb_tour_group').val();
  var chk_date = $('#' + id).val();
  $.ajax({
    type: 'post',
    url: '../inc/get_tour_dates.php',
    data: { group_id: group_id, chk_date: chk_date },
    success: function (result) {
      if (result == 'Error') {
        error_msg_alert("Date should be in between tour dates");
        $('#' + id).css({ 'border': '1px solid red' });
        document.getElementById(id).value = "";
        // $('#' + id).focus();
        g_validate_status = false;
        return false;
      }
      else {
        $('#' + id).css({ 'border': '1px solid #ddd' });
        return (true);
      }
      console.log(result);
    }
  });
}
///////End Traveling dates validation//////////


function tour_details_reflect(cmb_tour_group) {

  var currentdate = new Date(); 
  var dd = currentdate.getDate();
  if (dd < 10) {
    dd = '0' + dd;
  }
  var mm = currentdate.getMonth();
  mm =  parseFloat(mm) + 1;
  if (mm < 10) {
    mm = '0' + mm;
  }
  var curr_datetime = dd + "-"
                + mm  + "-" 
                + currentdate.getFullYear() + " "  
                + currentdate.getHours() + ":"  
                + currentdate.getMinutes();

  var group_id = $('#' + cmb_tour_group).val();
  /////////////// Train ////////////////
  $.ajax({
    type: 'post',
    url: '../inc/get_train_info.php',
    data: { group_id: group_id },
    success: function (result) {

      // Train Info////
      var table = document.getElementById("tbl_train_travel_details_dynamic_row");
      var train_arr = JSON.parse(result);
      if (jQuery.isEmptyObject(train_arr)) {
        var f_row = table.rows[0];
        f_row.cells[0].childNodes[0].removeAttribute('checked');
        document.getElementById('chk_train_select_all').removeAttribute('checked');
      };
      if (table.rows.length == 1) {
          for (var k = 1; k < table.rows.length; k++) {
              document.getElementById("tbl_train_travel_details_dynamic_row")
                  .deleteRow(k);
          }
      } else {
          while (table.rows.length > 1) {
              document.getElementById("tbl_train_travel_details_dynamic_row")
                  .deleteRow(table.rows.length-1);
              table.rows.length--;
          }
      }
      var f_row = table.rows[0];
      f_row.cells[2].childNodes[0].value = curr_datetime;
      document.getElementById(f_row.cells[3].childNodes[0].id).selectedIndex = 0;
      document.getElementById(f_row.cells[4].childNodes[0].id).selectedIndex = 0;
      document.getElementById(f_row.cells[8].childNodes[0].id).selectedIndex = 0;
      if (table.rows.length != train_arr.length) {
          for (var j = 0; j < train_arr.length - 1; j++) {
              addRow('tbl_train_travel_details_dynamic_row');
          }
      }
      for (var i = 0; i < train_arr.length; i++) {
        var row = table.rows[i];
        
        row.cells[0].childNodes[0].setAttribute('checked', 'true');

        row.cells[2].childNodes[0].value = train_arr[i]['dapart_time'];

        var option = new Option(train_arr[i]['from_location'], train_arr[i]['from_location'], true, true);
				$('#'+row.cells[3].childNodes[0].id).append(option).trigger('change');

				var option = new Option(train_arr[i]['to_location'], train_arr[i]['to_location'], true, true);
				$('#'+row.cells[4].childNodes[0].id).append(option).trigger('change');
        
        row.cells[8].childNodes[0].value = train_arr[i]['class'];

        $(row.cells[8].childNodes[0]).trigger('change');

      }
        city_lzloading('.train_from', '*From', true);
        city_lzloading('.train_to', '*To', true);
    }
  });

  /////////// Plane (prefer Group Quotation flights for this Tour Date) ////////////////
  $.ajax({
    type: 'post',
    url: '../inc/get_plane_info.php',
    data: { group_id: group_id },
    success: function (result) {

      var table = document.getElementById("tbl_plane_travel_details_dynamic_row");
      if (!table) {
        return;
      }

      var plane_arr = [];
      try {
        plane_arr = (typeof result === 'string') ? JSON.parse(result) : result;
      } catch (e) {
        console.error('Group Tour: invalid plane info response', e, result);
        plane_arr = [];
      }
      if (!Array.isArray(plane_arr)) {
        plane_arr = [];
      }

      while (table.rows.length > 1) {
        table.deleteRow(1);
      }

      if (plane_arr.length === 0) {
        var empty_row = table.rows[0];
        var empty_chk = empty_row.cells[0].querySelector('input[type="checkbox"]');
        if (empty_chk) {
          empty_chk.checked = false;
        }
        var selectAll = document.getElementById('chk_plane_select_all');
        if (selectAll) {
          selectAll.checked = false;
        }
        return;
      }

      for (var r = 1; r < plane_arr.length; r++) {
        addRow('tbl_plane_travel_details_dynamic_row');
      }
      if (typeof event_airport === 'function') {
        event_airport('tbl_plane_travel_details_dynamic_row', 4, 5);
      }
      if (typeof initAllAirlineSelectAddNew === 'function') {
        initAllAirlineSelectAddNew('#tbl_plane_travel_details_dynamic_row');
      }

      for (var i = 0; i < plane_arr.length; i++) {
        var row = table.rows[i];
        var plane = plane_arr[i] || {};
        var fromLabel = plane.from_sector || $.trim((plane.from_city || '') + (plane.from_location ? ' - ' + plane.from_location : ''));
        var toLabel = plane.to_sector || $.trim((plane.to_city || '') + (plane.to_location ? ' - ' + plane.to_location : ''));

        var getCtrl = (typeof getCellFormControl === 'function') ? getCellFormControl : function (cell) {
          return cell ? cell.querySelector('select,input') : null;
        };
        var chkEl = row.cells[0].querySelector('input[type="checkbox"]');
        var depEl = getCtrl(row.cells[2]);
        var arrEl = getCtrl(row.cells[3]);
        var fromEl = getCtrl(row.cells[4]);
        var toEl = getCtrl(row.cells[5]);
        var airlineEl = getCtrl(row.cells[6]);
        var classEl = getCtrl(row.cells[7]);
        var fromCityEl = getCtrl(row.cells[10]);
        var toCityEl = getCtrl(row.cells[11]);

        if (chkEl) {
          chkEl.checked = true;
        }
        if (depEl) {
          depEl.value = plane.dapart_time || curr_datetime;
        }
        if (arrEl) {
          arrEl.value = plane.arraval_time || curr_datetime;
        }
        if (fromEl && fromLabel) {
          $(fromEl).empty().append(new Option(fromLabel, fromLabel, true, true)).trigger('change');
        }
        if (toEl && toLabel) {
          $(toEl).empty().append(new Option(toLabel, toLabel, true, true)).trigger('change');
        }
        if (airlineEl && plane.airline_name !== undefined && plane.airline_name !== null && String(plane.airline_name) !== '') {
          var $airline = $(airlineEl);
          var airlineVal = String(plane.airline_name);
          if ($airline.find('option').filter(function () { return String(this.value) === airlineVal; }).length === 0 && plane.airline_label) {
            $airline.append(new Option(plane.airline_label, airlineVal, true, true));
          }
          $airline.val(airlineVal).trigger('change');
        }
        if (classEl && plane.class) {
          $(classEl).val(plane.class).trigger('change');
        }
        if (fromCityEl) {
          fromCityEl.value = plane.from_city_id || '';
        }
        if (toCityEl) {
          toCityEl.value = plane.to_city_id || '';
        }
        if (depEl && depEl.id && typeof dynamic_datetime === 'function') {
          dynamic_datetime(depEl.id);
        }
        if (arrEl && arrEl.id && typeof dynamic_datetime === 'function') {
          dynamic_datetime(arrEl.id);
        }
      }
    }
  });

  /////////////// Cruise ////////////////
  $.ajax({
    type: 'post',
    url: '../inc/get_cruise_info.php',
    data: { group_id: group_id },
    success: function (result) {

      // Cruise Info////
      var table = document.getElementById("tbl_dynamic_cruise_package_booking");
      var cruise_arr = JSON.parse(result);
      if (jQuery.isEmptyObject(cruise_arr)) {
        var f_row = table.rows[0];
        f_row.cells[0].childNodes[0].removeAttribute('checked');
        document.getElementById('chk_cruise_select_all').removeAttribute('checked');
      };
      if (table.rows.length == 1) {
          for (var k = 1; k < table.rows.length; k++) {
              document.getElementById("tbl_dynamic_cruise_package_booking")
                  .deleteRow(k);
          }
      } else {
          while (table.rows.length > 1) {
              document.getElementById("tbl_dynamic_cruise_package_booking")
                  .deleteRow(table.rows.length-1);
              table.rows.length--;
          }
      }
      var f_row = table.rows[0];
      f_row.cells[2].childNodes[0].value = curr_datetime;
      f_row.cells[3].childNodes[0].value = curr_datetime;
      f_row.cells[4].childNodes[0].value = '';
      f_row.cells[5].childNodes[0].value = '';
      if (table.rows.length != cruise_arr.length) {
          for (var j = 0; j < cruise_arr.length - 1; j++) {
              addRow('tbl_dynamic_cruise_package_booking');
          }
      }

      for (var i = 0; i < cruise_arr.length; i++) {
        var row = table.rows[i];
        row.cells[0].childNodes[0].setAttribute('checked', 'true');

        row.cells[2].childNodes[0].value = cruise_arr[i]['dapart_time'];
        row.cells[3].childNodes[0].value = cruise_arr[i]['dapart_time'];
        row.cells[4].childNodes[0].value = cruise_arr[i]['route'];
        row.cells[5].childNodes[0].value = cruise_arr[i]['cabin'];

        $(row.cells[4].childNodes[0]).trigger('change');
        $(row.cells[5].childNodes[0]).trigger('change');

      }
    }
  });

  ////Hotel Reflecet///
  $.ajax({
    type: 'post',
    url: '../inc/get_hotel_info.php',
    data: { group_id: group_id },
    success: function (result) {

      var table = document.getElementById("tbl_package_hotel_master");

      var hotel_arr = JSON.parse(result);
      if (jQuery.isEmptyObject(hotel_arr)) {
        var f_row = table.rows[0];
        f_row.cells[0].childNodes[0].removeAttribute('checked');
      };
      if (table.rows.length == 1) {
          for (var k = 1; k < table.rows.length; k++) {
              document.getElementById("tbl_package_hotel_master")
                  .deleteRow(k);
          }
      } else {
          while (table.rows.length > 1) {
              document.getElementById("tbl_package_hotel_master")
                  .deleteRow(table.rows.length-1);
              table.rows.length--;
          }
      }
      var f_row = table.rows[0];
      f_row.cells[2].childNodes[0].value = '';
      f_row.cells[3].childNodes[0].value = '';
      f_row.cells[4].childNodes[0].value = '';
      f_row.cells[5].childNodes[0].value = '';

      if (table.rows.length != hotel_arr.length) {
        for (var i = 1; i < hotel_arr.length; i++) {
          addRow('tbl_package_hotel_master');
        }
      }
      for (var i = 0; i < hotel_arr.length; i++) {
        var row = table.rows[i];
        row.cells[2].childNodes[0].value = hotel_arr[i]['city_names'];
        row.cells[3].childNodes[0].value = hotel_arr[i]['hotel_names'];
        row.cells[4].childNodes[0].value = hotel_arr[i]['hotel_type'];
        row.cells[5].childNodes[0].value = hotel_arr[i]['total_nights'];

        row.cells[0].childNodes[0].setAttribute('disabled', 'disabled');
        $(row.cells[2].childNodes[0]).trigger('change');
        $(row.cells[3].childNodes[0]).trigger('change');
        $(row.cells[4].childNodes[0]).trigger('change');
        $(row.cells[5].childNodes[0]).trigger('change');
      }
    }
  });

  /////// Costing ////////////////
  $.ajax({
    type: 'post',
    url: '../inc/get_visa_info.php',
    data: { group_id: group_id },
    success: function (result) {
      var visa_arr = JSON.parse(result);
      $('#visa_country_name').val(visa_arr.visa_country_name);
      $('#insuarance_company_name').val(visa_arr.company_name);
    }
  });

  /////////////// Transport ////////////////
  var quotation_id = $('#quotation_id').val();
  var quotTransportLoaded = $('#group_quot_transport_loaded').val();
  // Prefer transport from selected group quotation (includes service duration & vehicle count)
  if (quotation_id && quotation_id != '0' && quotTransportLoaded == '1') {
    return;
  }
  var tour_id = $('#cmb_tour_name').val();  // Get tour master ID, not group ID
  $.ajax({
    type: 'post',
    url: 'tab_2/get_transport_info.php',
    data: { tour_id: tour_id },  // Send tour_id instead of group_id
    success: function (result) {
      fill_group_booking_transport_table(JSON.parse(result), false);
    }
  });

}

// Helper function to capitalize first letter
function ucfirst(str) {
  if (!str) return '';
  return str.charAt(0).toUpperCase() + str.slice(1);
}

function group_quotation_info_load() {
  var quotation_id = $('#quotation_id').val();
  $('#group_quot_transport_loaded').val('0');
  if (quotation_id === '' || quotation_id === null) {
    return;
  }
  // Sale without quotation — clear rates and fall back to tour master costing
  if (quotation_id === '0') {
    $('#quot_adult_rate').val(0);
    $('#quot_with_bed_rate').val(0);
    $('#quot_without_bed_rate').val(0);
    $('#quot_infant_rate').val(0);
    $('#quot_single_person_rate').val(0);
    payment_details_reflected_data('tbl_member_dynamic_row');
    return;
  }

  $.ajax({
    type: 'post',
    url: '../inc/group_quotation_info_load.php',
    data: { quotation_id: quotation_id },
    success: function (result) {
      var response;
      try {
        response = JSON.parse(result);
      } catch (e) {
        console.error('Invalid quotation response', result);
        return;
      }
      if (!response || response.status !== 'ok') {
        return;
      }

      // Store quotation per-person rates for costing
      $('#quot_adult_rate').val(response.adult_rate || 0);
      $('#quot_with_bed_rate').val(response.with_bed_rate || 0);
      $('#quot_without_bed_rate').val(response.without_bed_rate || 0);
      $('#quot_infant_rate').val(response.infant_rate || 0);
      $('#quot_single_person_rate').val(response.single_person_rate || 0);

      // Apply quotation category amounts immediately (do not wait for member-row counting)
      apply_group_quotation_costing(response);

      // Set tour name then tour date from quotation
      if (response.tour_id) {
        $('#cmb_tour_name').val(String(response.tour_id));
        if ($('#cmb_tour_name').hasClass('select2-hidden-accessible')) {
          $('#cmb_tour_name').trigger('change.select2');
        }
        tour_type_reflect('cmb_tour_name');
        tour_group_reflect('cmb_tour_name', false);
        setTimeout(function () {
          if (response.tour_group) {
            $('#cmb_tour_group').val(String(response.tour_group));
            seats_availability_reflect();
            seats_availability_check();
            due_date_reflect();
            // Load other travelling sections from tour master, but transport from quotation
            $('#group_quot_transport_loaded').val('1');
            tour_details_reflect('cmb_tour_group');
            // Fill transport after tour_details AJAX starts; delay so DOM/select2 are ready
            setTimeout(function () {
              fill_group_booking_transport_table(response.transport_info_arr || [], true);
            }, 400);
          }
        }, 600);
      } else {
        $('#group_quot_transport_loaded').val('1');
        fill_group_booking_transport_table(response.transport_info_arr || [], true);
      }

      if (response.booking_type) {
        $('#tour_type_r').val(response.booking_type);
      }
      if (response.currency_code) {
        $('#gcurrency_code').val(response.currency_code);
      }
      if (response.tax_apply_on) {
        $('#tax_apply_on').val(response.tax_apply_on);
      }
      if (response.tax_value) {
        $('#tax_value').val(response.tax_value);
      }
      if (response.tcsper !== undefined && response.tcsper !== null && response.tcsper !== '') {
        $('#tcs_tax-').val(String(response.tcsper));
      }
      if (response.tcsvalue !== undefined) {
        $('#tcs1').val(response.tcsvalue);
      }
    }
  });
}

function apply_group_quotation_costing(response) {
  if (!response) return;

  var adultSeats = parseInt(response.total_adult, 10) || 0;
  var cwbSeats = parseInt(response.children_with_bed, 10) || 0;
  var cwobSeats = parseInt(response.children_without_bed, 10) || 0;
  var infantSeats = parseInt(response.total_infant, 10) || 0;
  var singleSeats = parseInt(response.single_person, 10) || 0;

  $('#txt_adult_seats').val(adultSeats);
  $('#txt_child_b_seats').val(cwbSeats);
  $('#txt_child_wb_seats').val(cwobSeats);
  $('#txt_infant_seats').val(infantSeats);
  $('#txt_single_person_seats').val(singleSeats);

  $('#txt_adult_expense').val(parseFloat(response.adult_cost || 0).toFixed(2));
  $('#txt_child_bed_expense').val(parseFloat(response.with_bed_cost || 0).toFixed(2));
  $('#txt_child_wbed_expense').val(parseFloat(response.children_cost || 0).toFixed(2));
  $('#txt_infant_expense').val(parseFloat(response.infant_cost || 0).toFixed(2));
  $('#txt_single_person_expense').val(parseFloat(response.single_person_cost || 0).toFixed(2));

  var totalPass = adultSeats + cwbSeats + cwobSeats + infantSeats + singleSeats;
  $('#txt_total_seats').val(totalPass);
  $('#txt_stay_total_seats').val(totalPass || response.total_passangers || 0);

  if (typeof tour_cost_calculate === 'function') {
    tour_cost_calculate('');
  } else {
    var total =
      parseFloat(response.adult_cost || 0) +
      parseFloat(response.with_bed_cost || 0) +
      parseFloat(response.children_cost || 0) +
      parseFloat(response.infant_cost || 0) +
      parseFloat(response.single_person_cost || 0);
    $('#txt_total_expense').val(total.toFixed(2));
    $('#txt_tour_fee').val(total.toFixed(2));
    if (typeof calculate_total_discount === 'function') {
      calculate_total_discount('');
    }
  }
}

function fill_group_booking_transport_table(transport_arr, fromQuotation) {
  var table = document.getElementById('tbl_booking_transport');
  if (!table) {
    return;
  }
  transport_arr = transport_arr || [];

  if (!transport_arr.length) {
    var f_row = table.rows[0];
    if (f_row && f_row.cells[0]) {
      var chk = f_row.cells[0].querySelector('input[type="checkbox"]');
      if (chk) {
        chk.removeAttribute('checked');
        chk.checked = false;
      }
    }
    while (table.rows.length > 1) {
      table.deleteRow(1);
    }
    return;
  }

  while (table.rows.length > 1) {
    table.deleteRow(1);
  }
  while (table.rows.length < transport_arr.length) {
    addRow('tbl_booking_transport');
  }

  var today = new Date();
  var dd = String(today.getDate()).padStart(2, '0');
  var mm = String(today.getMonth() + 1).padStart(2, '0');
  var yyyy = today.getFullYear();
  var todayDate = dd + '-' + mm + '-' + yyyy;

  setTimeout(function () {
    for (var i = 0; i < transport_arr.length; i++) {
      var row = table.rows[i];
      if (!row) continue;

      var vehicleSelect = row.cells[2].querySelector('select');
      if (vehicleSelect) {
        vehicleSelect.value = transport_arr[i]['vehicle_id'];
        $(vehicleSelect).trigger('change');
      }

      var startDateInput = row.cells[3].querySelector('input');
      if (startDateInput) {
        startDateInput.value = (fromQuotation && transport_arr[i]['start_date'])
          ? transport_arr[i]['start_date']
          : todayDate;
      }
      var endDateInput = row.cells[4].querySelector('input');
      if (endDateInput) {
        endDateInput.value = (fromQuotation && transport_arr[i]['end_date'])
          ? transport_arr[i]['end_date']
          : todayDate;
      }

      if (transport_arr[i]['pickup_value'] && transport_arr[i]['pickup_value'] != '') {
        var pickupSelect = row.cells[5].querySelector('select');
        if (pickupSelect) {
          var $pickupSelect = $(pickupSelect);
          var pickupHtml =
            '<optgroup value="' +
            transport_arr[i]['pickup_type'] +
            '" label="' +
            ucfirst(transport_arr[i]['pickup_type']) +
            '">' +
            '<option value="' +
            transport_arr[i]['pickup_value'] +
            '" selected>' +
            transport_arr[i]['pickup_location'] +
            '</option>' +
            '</optgroup>';
          $pickupSelect.html(pickupHtml);
          destinationLoading($pickupSelect, 'Pickup Location');
        }
      }

      if (transport_arr[i]['drop_value'] && transport_arr[i]['drop_value'] != '') {
        var dropSelect = row.cells[6].querySelector('select');
        if (dropSelect) {
          var $dropSelect = $(dropSelect);
          var dropHtml =
            '<optgroup value="' +
            transport_arr[i]['drop_type'] +
            '" label="' +
            ucfirst(transport_arr[i]['drop_type']) +
            '">' +
            '<option value="' +
            transport_arr[i]['drop_value'] +
            '" selected>' +
            transport_arr[i]['drop_location'] +
            '</option>' +
            '</optgroup>';
          $dropSelect.html(dropHtml);
          destinationLoading($dropSelect, 'Drop-off Location');
        }
      }

      if (transport_arr[i]['service_duration'] && transport_arr[i]['service_duration'] != '') {
        var durationSelect = row.cells[7].querySelector('select');
        if (durationSelect) {
          var $durationSelect = $(durationSelect);
          var durationValue = String(transport_arr[i]['service_duration']).trim();
          var durationId = transport_arr[i]['s_duration_id']
            ? String(transport_arr[i]['s_duration_id'])
            : '';

          // Ensure option exists (dropdown values are entry_id, text is duration label)
          if (durationId && !$durationSelect.find('option[value="' + durationId + '"]').length) {
            $durationSelect.append(
              '<option value="' + durationId + '">' + durationValue + '</option>'
            );
          }
          if (!durationId) {
            var matched = $durationSelect.find('option').filter(function () {
              return $(this).text().trim() === durationValue;
            });
            if (matched.length) {
              durationId = matched.first().attr('value');
            } else {
              durationId = durationValue;
              $durationSelect.append(
                '<option value="' + durationId + '">' + durationValue + '</option>'
              );
            }
          }

          durationSelect.value = durationId;
          if ($durationSelect.data('select2')) {
            $durationSelect.val(durationId).trigger('change');
          } else {
            $durationSelect.select2({ width: '170px' });
            $durationSelect.val(durationId).trigger('change');
          }
        }
      }

      var vehicleCountInput = row.cells[8].querySelector('input');
      if (vehicleCountInput) {
        vehicleCountInput.value =
          transport_arr[i]['vehicle_count'] !== undefined &&
          transport_arr[i]['vehicle_count'] !== null
            ? transport_arr[i]['vehicle_count']
            : '';
      }

      var checkbox = row.cells[0].querySelector('input[type="checkbox"]');
      if (checkbox) {
        checkbox.checked = true;
        checkbox.setAttribute('checked', 'checked');
      }
    }

    $('#tbl_booking_transport').find('.app_datepicker').datetimepicker({
      timepicker: false,
      format: 'd-m-Y'
    });
    $('#tbl_booking_transport')
      .find('select[name^="transport_vehicle_name"]')
      .each(function () {
        if (!$(this).hasClass('select2-hidden-accessible')) {
          $(this).select2();
        }
      });
    // Re-assert duration after select2 inits (can wipe value on first paint)
    for (var d = 0; d < transport_arr.length; d++) {
      if (!transport_arr[d]['service_duration']) continue;
      var dRow = table.rows[d];
      if (!dRow) continue;
      var dSelect = dRow.cells[7].querySelector('select');
      if (!dSelect) continue;
      var dVal = transport_arr[d]['s_duration_id']
        ? String(transport_arr[d]['s_duration_id'])
        : '';
      var dText = String(transport_arr[d]['service_duration']).trim();
      if (!dVal) {
        var opt = $(dSelect)
          .find('option')
          .filter(function () {
            return $(this).text().trim() === dText;
          })
          .first();
        if (opt.length) dVal = opt.attr('value');
      }
      if (dVal) {
        $(dSelect).val(dVal).trigger('change');
      }
    }
  }, 500);
}