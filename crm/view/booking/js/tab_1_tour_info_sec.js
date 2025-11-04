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

  /////////// Plane ////////////////
  $.ajax({
    type: 'post',
    url: '../inc/get_plane_info.php',
    data: { group_id: group_id },
    success: function (result) {

      var table = document.getElementById("tbl_plane_travel_details_dynamic_row");
      var plane_arr = JSON.parse(result);
      if (jQuery.isEmptyObject(plane_arr)) {
        var f_row = table.rows[0];
        f_row.cells[0].childNodes[0].removeAttribute('checked');
        document.getElementById('chk_plane_select_all').removeAttribute('checked');
      };
      if (table.rows.length == 1) {
          for (var k = 1; k < table.rows.length; k++) {
              document.getElementById("tbl_plane_travel_details_dynamic_row")
                  .deleteRow(k);
          }
      } else {
          while (table.rows.length > 1) {
              document.getElementById("tbl_plane_travel_details_dynamic_row")
                  .deleteRow(table.rows.length-1);
              table.rows.length--;
          }
      }
      var f_row = table.rows[0];
      f_row.cells[2].childNodes[0].value = curr_datetime;
      f_row.cells[3].childNodes[0].value = curr_datetime;
      f_row.cells[4].childNodes[0].value = '';
      document.getElementById(f_row.cells[6].childNodes[0].id).selectedIndex = 0;
      document.getElementById(f_row.cells[7].childNodes[0].id).selectedIndex = 0;
      f_row.cells[8].childNodes[0].value = '';
      if (table.rows.length != plane_arr.length) {
          for (var j = 0; j < plane_arr.length - 1; j++) {
              addRow('tbl_plane_travel_details_dynamic_row');
          }
      }
      for (var i = 0; i < plane_arr.length; i++) {

        var row = table.rows[i];
        row.cells[0].childNodes[0].setAttribute('checked', 'true');

        row.cells[2].childNodes[0].value = plane_arr[i]['dapart_time'];
        row.cells[3].childNodes[0].value = plane_arr[i]['arraval_time'];
        row.cells[4].childNodes[0].value = plane_arr[i]['from_city'] + ' - ' + plane_arr[i]['from_location'];
        row.cells[5].childNodes[0].value = plane_arr[i]['to_city'] + ' - ' + plane_arr[i]['to_location'];
        row.cells[6].childNodes[0].value = plane_arr[i]['airline_name'];
        row.cells[7].childNodes[0].value = plane_arr[i]['class'];
        $(row.cells[6].childNodes[0]).trigger('change');
        $(row.cells[7].childNodes[0]).trigger('change');
        row.cells[10].childNodes[0].value = plane_arr[i]['from_city_id'];
        row.cells[11].childNodes[0].value = plane_arr[i]['to_city_id'];

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
  var tour_id = $('#cmb_tour_name').val();  // Get tour master ID, not group ID
  $.ajax({
    type: 'post',
    url: 'tab_2/get_transport_info.php',
    data: { tour_id: tour_id },  // Send tour_id instead of group_id
    success: function (result) {
      var table = document.getElementById("tbl_booking_transport");
      var transport_arr = JSON.parse(result);
      
      if (jQuery.isEmptyObject(transport_arr)) {
        var f_row = table.rows[0];
        f_row.cells[0].childNodes[0].removeAttribute('checked');
      }
      
      // Clear existing rows except first
      while (table.rows.length > 1) {
        table.deleteRow(1);
      }
      
      // Get today's date in dd-mm-yyyy format
      var today = new Date();
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0');
      var yyyy = today.getFullYear();
      var todayDate = dd + '-' + mm + '-' + yyyy;
      
      // Add rows if needed
      if (table.rows.length < transport_arr.length) {
        for (var i = 1; i < transport_arr.length; i++) {
          addRow('tbl_booking_transport');
        }
      }
      
      // Wait for rows to be added to DOM
      setTimeout(function () {
        for (var i = 0; i < transport_arr.length; i++) {
          var row = table.rows[i];
          
          // Set vehicle using querySelector
          var vehicleSelect = row.cells[2].querySelector('select');
          if(vehicleSelect){
            vehicleSelect.value = transport_arr[i]['vehicle_id'];
            $(vehicleSelect).trigger('change');
          }
          
          // Set dates using querySelector
          var startDateInput = row.cells[3].querySelector('input');
          if(startDateInput) startDateInput.value = todayDate;
          
          var endDateInput = row.cells[4].querySelector('input');
          if(endDateInput) endDateInput.value = todayDate;
          
        // Set pickup location - Add pre-selected option then init with AJAX
        if (transport_arr[i]['pickup_value'] && transport_arr[i]['pickup_value'] != '') {
          var pickupSelect = row.cells[5].querySelector('select');
          if(pickupSelect){
            var $pickupSelect = $(pickupSelect);
            // Add the pre-selected option
            var pickupHtml = '<optgroup value="' + transport_arr[i]['pickup_type'] + '" label="' + ucfirst(transport_arr[i]['pickup_type']) + '">' +
              '<option value="' + transport_arr[i]['pickup_value'] + '" selected>' + transport_arr[i]['pickup_location'] + '</option>' +
              '</optgroup>';
            $pickupSelect.html(pickupHtml);
            // Initialize with AJAX so user can search for other options
            destinationLoading($pickupSelect, 'Pickup Location');
          }
        }
        
        // Set drop location - Add pre-selected option then init with AJAX
        if (transport_arr[i]['drop_value'] && transport_arr[i]['drop_value'] != '') {
          var dropSelect = row.cells[6].querySelector('select');
          if(dropSelect){
            var $dropSelect = $(dropSelect);
            // Add the pre-selected option
            var dropHtml = '<optgroup value="' + transport_arr[i]['drop_type'] + '" label="' + ucfirst(transport_arr[i]['drop_type']) + '">' +
              '<option value="' + transport_arr[i]['drop_value'] + '" selected>' + transport_arr[i]['drop_location'] + '</option>' +
              '</optgroup>';
            $dropSelect.html(dropHtml);
            // Initialize with AJAX so user can search for other options
            destinationLoading($dropSelect, 'Drop-off Location');
          }
        }
        
        // Set service duration from tour data
        if (transport_arr[i]['service_duration'] && transport_arr[i]['service_duration'] != '') {
          var durationSelect = row.cells[7].querySelector('select');
          if(durationSelect){
            var $durationSelect = $(durationSelect);
            // Add the option if it doesn't exist, then select it
            var durationValue = transport_arr[i]['service_duration'];
            if ($durationSelect.find('option:contains("' + durationValue + '")').length === 0) {
              $durationSelect.append('<option value="' + durationValue + '">' + durationValue + '</option>');
            }
            // Select by text match
            $durationSelect.find('option').filter(function() {
              return $(this).text() === durationValue;
            }).prop('selected', true);
          }
        }
        
        // Set vehicle count from tour data
        var vehicleCountInput = row.cells[8].querySelector('input');
        if(vehicleCountInput){
          vehicleCountInput.value = transport_arr[i]['vehicle_count'] || '';
        }
        
        // Check the checkbox
        var checkbox = row.cells[0].querySelector('input[type="checkbox"]');
        if(checkbox){
          checkbox.checked = true;
          checkbox.setAttribute('checked', 'checked');
        }
      }
      
      // DON'T call destinationLoading on ALL dropdowns - it will clear the pre-populated options!
      // Each dropdown was already initialized with AJAX in the loop above
      
      // Reinitialize datepicker for all transport date fields (including new rows)
      $('#tbl_booking_transport').find('.app_datepicker').datetimepicker({ 
        timepicker: false, 
        format: 'd-m-Y' 
      });
      
      // Reinitialize vehicle dropdowns (simple select2, not AJAX)
      $('#tbl_booking_transport').find('select[name^="transport_vehicle_name"]').each(function(){
        if(!$(this).hasClass('select2-hidden-accessible')){
          $(this).select2();
        }
      });
      
      }, 300);
    }
  });

}

// Helper function to capitalize first letter
function ucfirst(str) {
  if (!str) return '';
  return str.charAt(0).toUpperCase() + str.slice(1);
}