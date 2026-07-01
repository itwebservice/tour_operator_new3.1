<?php
include "../../../model/model.php";
$booking_id = $_POST['booking_id'];
$sq_transport = mysqli_fetch_assoc( mysqlQuery("select * from car_rental_transport_voucher_entries where booking_id='$booking_id'") );


$sq_booking = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$booking_id' and delete_status='0'"));
?>
<form id="frm_service_voucher">
<div class="modal fade" id="voucher_modal1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel">Duty Slip Details</h4>
		</div>
		<div class="modal-body">
		<div class="panel panel-default panel-body">
		<input type="hidden" id="cmb_booking_id" value='<?= $booking_id  ?>'>
		
		<?php
		
            $sq_entry_n1 = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_transport_voucher_entries where booking_id='$booking_id'"));
        //    echo($sq_entry_n1 ['vehicle_no']);
			?>
		<div class="row">
			<div class="col-md-4 col-sm-6 mg_bt_10">
				<input type="hidden" id="vehicle_name" value='<?= $q_transport['entry_id']  ?>'>
				<input type="text" id="vehicle_names" title="Vehicle Name" name="vehicle_name" value="<?=$sq_booking['vehicle_name']?>" disabled>
			</div>
		</div>
		<div class="row">
            <div class="col-md-4 col-sm-6 mg_bt_10">
				<input type="text" id="vehicle_no" name="vehicle_no" placeholder=" Vehicle No"   title=" Vehicle No" value="<?= $sq_entry_n1['vehicle_no'] ?>">
			</div>
			<div class="col-md-4 col-sm-6 mg_bt_10">
				<input type="text" id="driver_name" name="driver_name" placeholder="Driver Name"   title="Driver Name" value="<?= $sq_entry_n1['driver_name'] ?>">
			</div>
			<div class="col-md-4 col-sm-6 mg_bt_10">
				<input type="number" id="mobile_no" name="mobile_no" placeholder="Mobile No"    title="Mobile No" value="<?= $sq_entry_n1['mobile_no'] ?>">
			</div>
			<div class="col-md-4 col-sm-6 mg_bt_10">
				<input type="text" id="type_array" name="type_array"  placeholder="Type" title="Type" value="<?= $sq_entry_n1['type_array'] ?>" />
			</div>
		</div>
		
		
		<div class="row text-center mg_tp_20">
			<div class="col-md-12">
				<button class="btn btn-sm btn-info ico_left" title="Print"><i class="fa fa-print"></i>&nbsp;&nbsp;Duty Slip</button>
			</div>
		</div>
	</div>


    </div>      

    </div>

</div>

</div>



</form>

<script>

$('#voucher_modal1').modal('show');

$(function(){
	$('#frm_service_voucher').validate({
		rules:{
		},
		submitHandler:function(form, event){
				event.preventDefault();
				var base_url = $('#base_url').val();
				//var count = $('#count').val();
				var booking_id = $('#cmb_booking_id').val();
				var vehicle_name_array = $('#vehicle_name').val();
				var vehicle_no_array = $('#vehicle_no').val();
				var driver_mobile_no_array = $('#mobile_no').val();
				var driver_name_array = $('#driver_name').val();
				var type_array = $('#type_array').val();;

				
				// console.log(vehicle_name_array);
				// console.log(vehicle_no_array);
				// console.log(driver_mobile_no_array);
				// console.log(driver_name_array);
				// console.log(type_array);

			
				$.ajax({
					type:'post',
                url:base_url+'controller/car_rental/service_voucher/transport_service_voucher_save.php',
					data:{ booking_id : booking_id,vehicle_name_array:vehicle_name_array,driver_name_array : driver_name_array,driver_mobile_no_array :driver_mobile_no_array, type_array:type_array,vehicle_no_array:vehicle_no_array  },
					success: function(message){
						console.log(message);
						var msg = message.split('--');
						if(msg[0]=="error"){
							error_msg_alert(msg[1]);
							return false;
						}
						else
						{
							if(msg!=''){
								$('#vi_confirm_box').vi_confirm_box({
								false_btn: false,
								message: 'Information Saved Successfully',
								true_btn_text:'Ok',
								callback: function(data1){
                                            booking_registration_pdf(booking_id);
											$('#voucher_modal1').modal('hide');
										
									}
								});
							}
						}
					}
				});
		}
	});
});
</script>

<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>