$('#transport_agency_id, #transport_bus_id,#city_name,#txt_catagory1,#hotel_name1').select2();
if (typeof initAllRoomCategorySelectAddNew === 'function') {
  initAllRoomCategorySelectAddNew('#tbl_package_hotel_infomration');
}
if (typeof initBookingServiceDurationSelects === 'function') {
  initBookingServiceDurationSelects();
}
if (typeof initBookingTransportPickupDrop === 'function') {
  initBookingTransportPickupDrop();
}
if (typeof hotelSupplierQuickLoadUrl === 'undefined') {
  hotelSupplierQuickLoadUrl = $('#base_url').val() + 'view/package_booking/booking/inc/hotel_name_load.php';
}
$('#txt_tsp_from_date,#txt_tsp_end_date').datetimepicker({ timepicker:true, format:'d-m-Y H:i' });
$('#txt_tsp_to_date').datetimepicker({ timepicker:false, format:'d-m-Y' });
$('#txt_hotel_from_date1, #txt_hotel_to_date1,#exc_date-1').datetimepicker({  format:'d-m-Y H:i' });

function get_booking_hotel_select_id_from_city(citySelectId) {
  if (!citySelectId || citySelectId.indexOf('city_name') !== 0) {
    return 'hotel_name1';
  }
  var suffix = citySelectId.substring('city_name'.length) || '1';
  return 'hotel_name' + suffix;
}

function get_booking_room_category_id_from_hotel(hotelSelectId) {
  if (!hotelSelectId) {
    return 'txt_catagory1';
  }
  if (hotelSelectId.indexOf('hotel_name') === 0) {
    var suffix = hotelSelectId.substring('hotel_name'.length) || '1';
    var candidates = ['txt_catagory' + suffix, 'txt_catagory' + suffix + '_h'];
    for (var i = 0; i < candidates.length; i++) {
      if (document.getElementById(candidates[i])) {
        return candidates[i];
      }
    }
    return 'txt_catagory' + suffix;
  }
  return 'txt_catagory1';
}

function initBookingServiceDurationSelects(container) {
  var $scope = container ? $(container) : $('#tbl_package_transport_infomration');
  $scope.find('select[id^="duration"]').each(function () {
    var $el = $(this);
    if ($el.data('select2')) {
      $el.select2('destroy');
    }
    $el.select2({ width: '170px', minimumResultsForSearch: 0 });
  });
}

function initBookingTransportPickupDrop(container) {
  if (typeof destinationLoading !== 'function') {
    return;
  }
  var $scope = container ? $(container) : $('#tbl_package_transport_infomration');
  $scope.find('select[id^="pickup_from"]').each(function () {
    destinationLoading($(this), 'Pickup Location');
  });
  $scope.find('select[id^="drop_to"]').each(function () {
    destinationLoading($(this), 'Drop-off Location');
  });
}

$(document).on('click', '#tab_3_head', function () {
  setTimeout(function () {
    if (typeof refresh_booking_hotel_select2_display === 'function') {
      refresh_booking_hotel_select2_display();
    }
    if (typeof initAllRoomCategorySelectAddNew === 'function') {
      initAllRoomCategorySelectAddNew('#tbl_package_hotel_infomration');
    }
    if (typeof initBookingServiceDurationSelects === 'function') {
      initBookingServiceDurationSelects();
    }
    if (typeof initBookingTransportPickupDrop === 'function') {
      initBookingTransportPickupDrop();
    }
    // Fill Activity pax from selected quotation traveler counts (CRM quotation behavior)
    if (typeof apply_booking_activity_pax_from_quotation === 'function') {
      apply_booking_activity_pax_from_quotation();
    }
  }, 200);
});


function booking_should_calculate_hotel_tariff() {
	var quotation_id = $('#quotation_id').val();
	return quotation_id === '0' || quotation_id === 0;
}

function get_booking_passenger_counts() {
	var adult_count = 0;
	var child_with_bed = 0;
	var child_without_bed = 0;
	var table = document.getElementById('tbl_package_tour_member');
	if (!table) {
		return {
			adult_count: adult_count,
			child_with_bed: child_with_bed,
			child_without_bed: child_without_bed
		};
	}

	for (var i = 0; i < table.rows.length; i++) {
		var row = table.rows[i];
		var chk = row.cells[0] && row.cells[0].childNodes[0];
		if (!chk || !chk.checked) {
			continue;
		}
		var adolescence = '';
		if (row.cells[9] && row.cells[9].childNodes[0]) {
			adolescence = row.cells[9].childNodes[0].value || '';
		}
		if (adolescence === 'Adult') {
			adult_count++;
		} else if (adolescence === 'Children') {
			child_with_bed++;
		} else if (adolescence === 'Infant') {
			child_without_bed++;
		}
	}

	return {
		adult_count: adult_count,
		child_with_bed: child_with_bed,
		child_without_bed: child_without_bed
	};
}

function booking_apply_hotel_tariff_total(totalCost) {
	if (!booking_should_calculate_hotel_tariff()) {
		return;
	}
	totalCost = totalCost || 0;
	$('#txt_hotel_expenses').val(totalCost);
	$('#total_basic_amt').val(totalCost);
	if (typeof calculate_tour_cost === 'function') {
		calculate_tour_cost('txt_hotel_expenses');
	}
	if (typeof get_auto_values === 'function') {
		get_auto_values('txt_booking_date', 'total_basic_amt', 'payment_mode', 'service_charge', 'markup', 'save', 'true', 'basic');
	}
}

function get_booking_hotel_cost() {
	if (!booking_should_calculate_hotel_tariff()) {
		return;
	}

	var table = document.getElementById('tbl_package_hotel_infomration');
	if (!table || !table.rows.length) {
		booking_apply_hotel_tariff_total(0);
		return;
	}

	var hotel_id_arr = [];
	var room_cat_arr = [];
	var check_in_arr = [];
	var check_out_arr = [];
	var total_nights_arr = [];
	var total_rooms_arr = [];
	var extra_bed_arr = [];
	var meal_plan_arr = [];
	var checked_arr = [];
	var package_id_arr = [];
	var passengerCounts = get_booking_passenger_counts();
	var package_id = $('#package1').val() || $('#txt_package_package_id').val() || '';
	var rowCount = table.rows.length;

	for (var i = 0; i < rowCount; i++) {
		var row = table.rows[i];
		var getCellValue = function (cellIndex) {
			if (!row.cells[cellIndex]) {
				return '';
			}
			var el = (typeof booking_table_cell_control === 'function')
				? booking_table_cell_control(row.cells[cellIndex])
				: row.cells[cellIndex].childNodes[0];
			return el ? el.value : '';
		};

		hotel_id_arr.push(getCellValue(3));
		room_cat_arr.push(getCellValue(7));
		check_in_arr.push(getCellValue(4));
		check_out_arr.push(getCellValue(5));
		total_rooms_arr.push(getCellValue(6));
		meal_plan_arr.push(getCellValue(8));
		extra_bed_arr.push(getCellValue(9) || '0');
		package_id_arr.push(package_id);
		total_nights_arr.push('');
		checked_arr.push(row.cells[0] && row.cells[0].childNodes[0] && row.cells[0].childNodes[0].checked ? 'true' : 'false');
	}

	var base_url = $('#base_url').val();
	$.ajax({
		type: 'post',
		url: base_url + 'view/package_booking/quotation/home/hotel/get_hotel_cost.php',
		data: {
			hotel_id_arr: hotel_id_arr,
			check_in_arr: check_in_arr,
			check_out_arr: check_out_arr,
			room_cat_arr: room_cat_arr,
			total_nights_arr: total_nights_arr,
			total_rooms_arr: total_rooms_arr,
			extra_bed_arr: extra_bed_arr,
			child_with_bed: passengerCounts.child_with_bed,
			child_without_bed: passengerCounts.child_without_bed,
			adult_count: passengerCounts.adult_count,
			package_id_arr: package_id_arr,
			checked_arr: checked_arr,
			meal_plan_arr: meal_plan_arr
		},
		success: function (result) {
			var hotel_arr = [];
			try {
				hotel_arr = JSON.parse(result);
			} catch (e) {
				hotel_arr = [];
			}

			var totalCost = 0;
			for (var j = 0; j < hotel_arr.length; j++) {
				if (checked_arr[j] === 'true') {
					totalCost += parseFloat(hotel_arr[j].hotel_cost) || 0;
				}
			}
			booking_apply_hotel_tariff_total(totalCost);
		}
	});
}

$(document).on('change', '#tbl_package_hotel_infomration input, #tbl_package_hotel_infomration select', function () {
	if (typeof get_booking_hotel_cost === 'function') {
		get_booking_hotel_cost();
	}
});

function back_to_tab_2(){
	  $('#tab_3_head').removeClass('active');
    $('#tab_2_head').addClass('active');
    $('.bk_tab').removeClass('active');
    $('#tab_2').addClass('active');
    $('html, body').animate({scrollTop: $('.bk_tab_head').offset().top}, 200);
}

/**Hotel Name load start**/
function load_hotel_list(id){
  var city_id = $("#"+id).val();
  if (!city_id) {
    return;
  }
  var hotelSelectId = get_booking_hotel_select_id_from_city(id);
  var $hotel = $("#" + hotelSelectId);
  if (!$hotel.length) {
    $hotel = $("#hotel_name1");
  }
  if (typeof hotelDropdownLoadByCity === 'function') {
    hotelDropdownLoadByCity(city_id, $hotel);
    return;
  }
  var base_url = $('#base_url').val();
  $.get(base_url + 'view/package_booking/booking/inc/hotel_name_load.php', { city_id : city_id }, function ( data ) {
        if ($hotel.data('select2')) {
            $hotel.select2('destroy');
        }
        $hotel.html( data );
        $hotel.select2({ width: '170px', minimumResultsForSearch: 0 });
        if (typeof captureHotelSelect2Config === 'function') {
            captureHotelSelect2Config($hotel);
        }
        initHotelSelectAddNew($hotel);
  } ) ;   
}
//Room Category
//roomcategory load
function hotel_type_load_cate(id)
{
  var hotel_id = $("#"+id).val();
  if (!hotel_id) {
    return;
  }

  var quotation_id = $("#quotation_id").val() || 0;
  var categorySelectId = get_booking_room_category_id_from_hotel(id);
  var base_url = $('#base_url').val();
  $.get(base_url + "view/package_booking/booking/inc/hotel_category.php", { hotel_id : hotel_id, quotation_id: quotation_id }, function ( data ) {
        var $cat = $("#" + categorySelectId);
        if (!$cat.length) {
          return;
        }
        $cat.html(data);
        if (typeof refreshRoomCategorySelectAfterLoad === 'function') {
            refreshRoomCategorySelectAfterLoad($cat, { width: '140px' });
        } else if (!$cat.data('select2')) {
            $cat.select2({ width: '140px', minimumResultsForSearch: 0 });
        }
  }).fail(function () {
        error_msg_alert('Unable to load room categories for the selected hotel.');
  }).always(function () {
        if (typeof get_booking_hotel_cost === 'function') {
            get_booking_hotel_cost();
        }
  });
}
/////////////////////////////////////Package Tour hotel name list load end/////////////////////////////////////

$(function(){
	$('#frm_tab_3').validate({
    rules:{
            
    },
		submitHandler:function(form){

			var valid_state = package_tour_booking_tab3_validate();
			if(valid_state==false){ return false; }

      //** Validation for Transport
      var table = document.getElementById("tbl_package_transport_infomration");
      var rowCount = table.rows.length;
      for(var i=0; i<rowCount; i++)
      {
        var row = table.rows[i];
        if(row.cells[0].childNodes[0].checked)
        {
          if(row.cells[2].childNodes[0].value==""){ error_msg_alert("Transport Vehicle in row-"+(i+1)+" is required<br>"); return false; }
          if(row.cells[3].childNodes[0].value==""){ error_msg_alert("Transport Start Date in row-"+(i+1)+" is required<br>"); return false; }
          if(row.cells[4].childNodes[0].value==""){ error_msg_alert("Transport End Date in row-"+(i+1)+" is required<br>"); return false; }
          if(row.cells[5].childNodes[0].value==""){ error_msg_alert("Transport Pickup location in row-"+(i+1)+" is required<br>"); return false; }
          if(row.cells[6].childNodes[0].value==""){ error_msg_alert("Transport Drop location in row-"+(i+1)+" is required<br>"); return false; }
          if(row.cells[7].childNodes[0].value==""){ error_msg_alert("Service Duration in row-"+(i+1)+" is required<br>"); return false; }
          if(row.cells[8].childNodes[0].value==""){ error_msg_alert("Vehicle count in row-"+(i+1)+" is required<br>"); return false; }
          
          count++; 
        }
        
      }

      //** Validation for Activity
      var table = document.getElementById("tbl_package_exc_infomration");
      var rowCount = table.rows.length;
      for(var i=0; i<rowCount; i++)
      {
        var row = table.rows[i];
        var chkEl = (typeof booking_table_cell_control === 'function')
          ? booking_table_cell_control(row.cells[0])
          : row.cells[0].childNodes[0];
        if(chkEl && chkEl.checked)
        {
          var dateEl = booking_table_cell_control(row.cells[2]);
          var cityEl = booking_table_cell_control(row.cells[3]);
          var nameEl = booking_table_cell_control(row.cells[4]);
          var transferEl = booking_table_cell_control(row.cells[5]);
          if(!dateEl || dateEl.value==""){ error_msg_alert("Activity Date in row-"+(i+1)+" is required<br>"); return false; }
          if(!cityEl || cityEl.value==""){ error_msg_alert("Activity City in row-"+(i+1)+" is required<br>"); return false; }
          if(!nameEl || nameEl.value==""){ error_msg_alert("Activity in row-"+(i+1)+" is required<br>"); return false;}
          if(!transferEl || transferEl.value==""){ error_msg_alert("Transfer option in row-"+(i+1)+" is required<br>"); return false; }

          count++; 
        }
      }

			$('#tab_3_head').addClass('done');
			$('#tab_4_head').addClass('active');
			$('.bk_tab').removeClass('active');
			$('#tab_4').addClass('active');
			$('html, body').animate({scrollTop: $('.bk_tab_head').offset().top}, 200);
			if (typeof get_booking_hotel_cost === 'function') {
				get_booking_hotel_cost();
			}
			if (typeof refreshQuotationTcsOnCostingTab === 'function') {
				refreshQuotationTcsOnCostingTab();
			}

		}
	});
});


/////////////////////////////////////Package Tour Master Tab3 validate start/////////////////////////////////////
function package_tour_booking_tab3_validate()
{
  g_validate_status = true;
  var validate_message = "";

  var table = document.getElementById("tbl_package_hotel_infomration");
  var rowCount = table.rows.length;
  for(var i=0; i<rowCount; i++)
  {
    var row = table.rows[i];

    if(row.cells[0].childNodes[0].checked)
    {
      validate_dynamic_empty_select(row.cells[2].childNodes[0]);
      validate_dynamic_empty_select(row.cells[3].childNodes[0]);
      validate_dynamic_empty_fields(row.cells[4].childNodes[0]);
      validate_dynamic_empty_date(row.cells[5].childNodes[0]);
      validate_dynamic_empty_date(row.cells[6].childNodes[0]);
      validate_dynamic_empty_fields(row.cells[7].childNodes[0]);

      if(row.cells[2].childNodes[0].value==""){ validate_message += "City in row-"+(i+1)+" is required<br>"; }               
      if(row.cells[3].childNodes[0].value==""){ validate_message += "Hotel in row-"+(i+1)+" is required<br>"; }                
      if(row.cells[4].childNodes[0].value==""){ validate_message += "Check-In date in row-"+(i+1)+" is required<br>"; }               
      if(row.cells[5].childNodes[0].value==""){ validate_message += "Check-Out date in row-"+(i+1)+" is required<br>"; }               
      if(row.cells[6].childNodes[0].value==""){ validate_message += "Room(s) in row-"+(i+1)+" is required<br>"; }               
      if(row.cells[7].childNodes[0].value==""){ validate_message += "Category in row-"+(i+1)+" is required<br>"; }            
    }
  } 
  if(validate_message!=""){
            error_msg_alert(validate_message, 10000);
            return false;
          }

  if(g_validate_status == false) { return false; }  

}
/////////////////////////////////////Package Tour Master Tab3 validate end/////////////////////////////////////

window.initBookingServiceDurationSelects = initBookingServiceDurationSelects;
window.initBookingTransportPickupDrop = initBookingTransportPickupDrop;
window.hotel_type_load_cate = hotel_type_load_cate;
window.get_booking_room_category_id_from_hotel = get_booking_room_category_id_from_hotel;
window.get_booking_hotel_cost = get_booking_hotel_cost;
window.booking_should_calculate_hotel_tariff = booking_should_calculate_hotel_tariff;

