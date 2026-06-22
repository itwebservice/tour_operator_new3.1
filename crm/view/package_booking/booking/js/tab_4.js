$('#txt_payment_date1, #txt_payment_date2, #txt_balance_due_date').datetimepicker({ timepicker: false, format: 'd-m-Y' });
$('#txt_booking_date').datetimepicker({ format: 'd-m-Y H:i' });

if ($('#booking_id').length && $('#frm_tab_4').length) {
	$(function () {
		if (typeof calculate_plane_expense === 'function') {
			calculate_plane_expense('tbl_plane_travel_details_dynamic_row', true);
		}
		if (typeof calculate_train_expense === 'function') {
			calculate_train_expense('tbl_train_travel_details_dynamic_row');
		}
		if (typeof calculate_cruise_expense === 'function') {
			calculate_cruise_expense('tbl_dynamic_cruise_package_booking');
		}
		if (typeof calculate_tour_cost === 'function') {
			calculate_tour_cost();
		}
		if (typeof get_auto_values === 'function') {
			get_auto_values('booking_date', 'total_basic_amt', 'payment_mode', 'service_charge', 'markup', 'update', 'false', 'service_charge', 'discount_amt');
		}
		if ($('#txt_tour_service_tax').val() && typeof generic_tax_reflect === 'function') {
			generic_tax_reflect('tour_taxation_id', 'txt_tour_service_tax', 'calculate_total_tour_cost');
		}
	});
} else {
	destinationLoading('select[name^=pickup_from]', 'Pickup Location');
	destinationLoading('select[name^=drop_to]', 'Drop-off Location');
}

/////////////////////////////////////Package Tour Master Tab4 validate start/////////////////////////////////////
function package_tour_booking_tab4_validate() {
  g_validate_status = true;

  var payment_mode1 = $("#cmb_payment_mode1").val();
  var payment_mode2 = $("#cmb_payment_mode2").val();

  if (g_validate_status == false) { return false; }

}
/////////////////////////////////////Package Tour Master Tab4 validate end/////////////////////////////////////

function back_to_tab_3() {
  $('#tab_4_head').removeClass('active');
  $('#tab_3_head').addClass('active');
  $('.bk_tab').removeClass('active');
  $('#tab_3').addClass('active');
  $('html, body').animate({ scrollTop: $('.bk_tab_head').offset().top }, 200);
}
