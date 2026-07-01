<form id="frm_tab21">
	
	<div class="row">
		<div class="col-md-12 app_accordion">
			<input type="hidden" value="" id="tour_group_id"/>
  			<div class="panel-group main_block" id="accordion" role="tablist" aria-multiselectable="true">

  				<!-- Flight Information -->
				<div class="accordion_content main_block mg_bt_10">
					<div class="panel panel-default main_block">
						<div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
					        <div class="Normal main_block" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="true" aria-controls="collapse1" id="collapsed1">       
					        	<div class="col-md-12"><span>Flight Information</span></div>
					        </div>
					    </div>
					    <div id="collapse1" class="panel-collapse collapse in main_block" role="tabpanel" aria-labelledby="heading1">
					        <div class="panel-body">
					        	<?php include_once('plane_tbl.php') ?>
					        </div>
					    </div>
					</div>
				</div>
			</div>
		</div>
	</div>	



	<div class="row text-center mg_tp_20">

		<div class="col-xs-12">

			<button class="btn btn-info btn-sm ico_left" type="button" onclick="switch_to_tab1()"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Previous</button>

			&nbsp;&nbsp;

			<button class="btn btn-info btn-sm ico_right" onclick="get_auto_values('quotation_date1','subtotal1','payment_mode','service_charge1','markup_cost1','update','true','service_charge');">Next&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>

		</div>

	</div>



</form>



<script>

$('#airline_name1_1').select2();

$.fn.modal.Constructor.prototype.enforceFocus = function() {};

$(function(){

	$('#frm_tab21').validate({

		rules:{

		},

		submitHandler:function(form){
 

			var table = document.getElementById("tbl_flight_quotation_dynamic_plane_update");

			  var rowCount = table.rows.length;
			  var selectedCount1 = 0;
			  

			  for(var i=0; i<rowCount; i++)

			  {

			    var row = table.rows[i];

			     

			    if(isFlightQuotationPlaneRowChecked(row))

			    {

					var planeRow = getFlightQuotationPlaneRowData(row);
					var from_sector = planeRow.from_sector;
		       		var to_sector = planeRow.to_sector;
			       var airline_name = planeRow.airline_name;

			       var plane_class = planeRow.plane_class;
				   var total_adult = planeRow.total_adult;
			   var total_child = planeRow.total_child;
			   var total_infant = planeRow.total_infant;
			       var dapart1 = planeRow.dapart;

				   var arraval1 = planeRow.arraval;
				   var from_city_id1 = planeRow.from_city_id;
			  		var to_city_id1 = planeRow.to_city_id;
					  selectedCount1++;
			       var plane_id = planeRow.plane_id || "";


			       if(from_sector=="")

			       {

			          error_msg_alert('Enter From Sector Details in row'+(i+1));
						  $('.accordion_content').removeClass("indicator");
	          	  	  $('#tbl_flight_quotation_dynamic_plane_update').parent('div').closest('.accordion_content').addClass("indicator");

			          return false;

			       }

			       if(to_sector=="")

				    {

				          error_msg_alert('Enter To Sector Details in row'+(i+1));
						  $('.accordion_content').removeClass("indicator");
	          	  		  $('#tbl_flight_quotation_dynamic_plane_update').parent('div').closest('.accordion_content').addClass("indicator");

				          return false;

				    }


					if(dapart1=="")

					{ 

						error_msg_alert("Departure Date time is required in row:"+(i+1)); 
						  $('.accordion_content').removeClass("indicator");
	          	  		  $('#tbl_flight_quotation_dynamic_plane_update').parent('div').closest('.accordion_content').addClass("indicator");

						 return false;

					}

			       

					if(arraval1=="")

					{ 

						error_msg_alert('Arrival Date time is required in row:'+(i+1));
						  $('.accordion_content').removeClass("indicator");
	          	  		  $('#tbl_flight_quotation_dynamic_plane_update').parent('div').closest('.accordion_content').addClass("indicator"); 

						 return false;

					}
					if(total_adult == ''){
						
						error_msg_alert('Total adult(s) is required in row:'+(i+1)); 
						$('.accordion_content').removeClass("indicator");
						$('#tbl_flight_quotation_dynamic_plane').parent('div').closest('.accordion_content').addClass("indicator");

						return false;
					}



 

			    }      
				if(!selectedCount1){
					error_msg_alert("Please select atleast one flight entry"); 
					$('.accordion_content').removeClass("indicator");
					$('#tbl_flight_quotation_dynamic_plane').parent('div').closest('.accordion_content').addClass("indicator");
					return false;
		  		}
			  }







		$('.accordion_content').removeClass("indicator");
		$('a[href="#tab_3"]').tab('show');		

		}

	});

});

function switch_to_tab1(){ $('a[href="#tab_1"]').tab('show'); }

</script>



