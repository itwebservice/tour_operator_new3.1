<form id="frm_tab1">
	<div class="app_panel">
		<div class="">
			<div class="container-fluid">
				<div class="app_panel_content Filter-panel">
					<div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
						<legend>Customer Details</legend>
						<div class="row mg_bt_20">
							<div class="col-md-4 col-sm-6 col-xs-12">
								<select name="customer_id" id="customer_id" class="customer_dropdown" title="Select Customer Name" style="width:100%" onchange="customer_info_load();get_auto_values('booking_date','basic_cost','payment_mode','service_charge','markup','save','true','service_charge','discount');">
								<?php get_new_customer_dropdown($role,$branch_admin_id,$branch_status); ?>
								</select>
							</div>
							<div id="cust_details">
								<div class="col-md-4 col-sm-6 col-xs-12">
									<input type="text" id="mobile_no" name="mobile_no"  placeholder="Mobile No" title="Mobile No" readonly>
								</div>
								<div class="col-md-4 col-sm-6 col-xs-12">
									<input type="text" id="email_id" name="email_id" placeholder="Email ID" title="Email ID" readonly>
								</div>
								<div class="col-md-2 col-sm-6 col-xs-12 hidden" id="company_div">
									<input type="text" id="company_name" class="hidden" name="company_name" title="Company Name" placeholder="Company Name" title="Company Name" readonly>
								</div>
								<div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10 hidden" id="credit_div">
									<input type="text" id="credit_amount" class="hidden" name="credit_amount" placeholder="Credit Note Balance" title="Credit Note Balance" readonly>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3 col-sm-6 col-xs-12">
								<select name="tour_type" id="tour_type" title="Travelling Type" required>
									<option value="">*Travelling Type</option>
									<option value="Domestic">Domestic</option>
									<option value="International">International</option>
								</select>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12 mg_bt_10">
								<input type="text" id="guest_name"  name="guest_name" title="Guest Name and contact number" placeholder="Guest Name and contact number" >
							</div>
						</div>
						<div class="row mg_tp_20">
							<div class="col-md-3">
								<input id="copy_details1" name="copy_details1" type="checkbox" onClick="copy_details();">
								&nbsp;&nbsp;<label for="copy_details1">Passenger Details same as above</label>
							</div>
							<div class="col-md-3">
								<input id="reissue_check1" name="reissue_check1" type="checkbox" onClick="ticket_reissue();">
								&nbsp;&nbsp;<label for="reissue_check1">Reissue Ticket</label>
							</div>
<form method="POST" enctype="multipart/form-data">
<div class="form-row">
<!-- Portal Dropdown -->
<div class="form-group col-md-3">
  <select name="tour_type" id="flight_select" class="form-control" required>
  <option value="">-- Select Portal --</option>
  <!-- <option value="Digital">Digital</option>
  <option value="Fare-Boutique">Fare-Boutique</option>
  <option value="Travel-Boutique">Travel-Boutique</option> -->
  
  <option value="TBO-Train">TBO</option>
  <option value="Tripjack">Tripjack</option>
  <option value="Amadeus">Amadeus</option>
  <option value="Galileo">Galileo</option>
  </select>
</div>
<input type="hidden" id="selected_portal" name="selected_portal">


<!-- CSV Upload -->
<div class="form-group col-md-3">
  <div class="div-upload  mg_bt_20" id="div_upload_button">
  <div id="cust_csv_upload" class="upload-button1"><span>Upload CSV</span></div>
  <span id="cust_status" ></span>
  <ul id="files" ></ul>
  <input type="hidden" id="txt_cust_csv_upload_dir" name="txt_cust_csv_upload_dir">
  </div>
</div>
<!-- Submit Button -->
<!-- <div class="form-group col-md-2 align-self-end">
	<button type="submit" class="btn btn-primary">Upload</button>
	</div> -->
</div>
</form>
</div>
</div>
<div id="new_cust_div" class="mg_tp_10"></div>
<h3 class="editor_title">Passenger Details</h3>
<div class="panel panel-default panel-body app_panel_style">
<div class="row text-right mg_bt_10">
<div class="col-xs-12">
<!-- <div class="col-md-3 text-left no-pad">
	<button type="button" class="btn btn-info btn-sm ico_left pull-left" onclick="display_format_modal();" autocomplete="off" data-original-title="" title=""><i class="fa fa-download" aria-hidden="true"></i>&nbsp;&nbsp;CSV Format</button>&nbsp;
	    <div class="div-upload  mg_bt_20" id="div_upload_button">
	        <div id="cust_csv_upload" class="upload-button1"><span>CSV</span></div>
	        <span id="cust_status" ></span>
	        <ul id="files" ></ul>
	        <input type="hidden" id="txt_cust_csv_upload_dir" name="txt_cust_csv_upload_dir">
	    </div>
	</div> -->
<!-- <span style="color: red;line-height: 35px;" data-original-title="" title="" class="note d-none">Please add multiple seat number of multi trip using '/' in between like S1/D3/NA. And similar for Meal plan field.</span> -->
<button type="button" class="btn btn-excel" title="Add Row" onclick="addRow('tbl_dynamic_ticket_master','1')"><i class="fa fa-plus"></i></button>
<button type="button" class="btn btn-pdf btn-sm" title="Delete Row" onclick="deleteRow('tbl_dynamic_ticket_master');"><i class="fa fa-trash"></i></button>
</div>
</div>
<div class="row">
<div class="col-xs-12">
<div class="table-responsive">
<?php $offset = ""; ?>
<table id="tbl_dynamic_ticket_master" name="tbl_dynamic_ticket_master" class="table border_0 no-marg" style="padding-bottom: 0 !important;">
<?php
	include_once('ticket_master_tbl.php');
	?>
</table>
</div>
</div>
</div>
</div>
<div class="row text-center mg_tp_20">
<div class="col-xs-12">
<button class="btn btn-info btn-sm ico_right">Next&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
</div>
</div>
<div id="div_flightd_modal"></div>
</div>
</div>
</div>
</div>
</form>
<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>
<script>
	$('#quotation_id').select2();
	cust_csv_upload();
  function cust_csv_upload()
  {   
      var base_url = $('#base_url').val();
      var btnUpload = $('#cust_csv_upload');
      var status = $('#cust_status');

      new AjaxUpload(btnUpload, {
          action: base_url + 'view/visa_passport_ticket/ticket/home/save/upload_passenger_csv.php',
          name: 'uploadfile',
          onSubmit: function(file, ext){

              // ✅ Check if portal is selected
              var selectedPortal = $('#flight_select').val();
              if(selectedPortal == ''){
                  alert('Please select a portal first.');
                  return false; // Prevent upload
              }
			   $('#selected_portal').val(selectedPortal);

              if (!confirm('Do you want to import this file?')){
                  return false;
              }

              if (!(ext && /^(csv)$/.test(ext))){ 
                  status.text('Only CSV files are allowed');
                  return false;
              }

              status.text('Uploading...');
          },
          onComplete: function(file, response){
              status.text('');
              if(response === "error"){
                  alert("File is not uploaded.");
              } else {
                  $('#txt_cust_csv_upload_dir').val(response);
                  cust_csv_save();
              }
          }
      });
  }

	
  // harshit 03-06-2025 render data in the table
	function cust_csv_save(){
	    var cust_csv_dir = document.getElementById("txt_cust_csv_upload_dir").value;
		var selectedPortal = $('#selected_portal').val();
	    var base_url = $('#base_url').val();
		console.log("Selected Portal: " + selectedPortal);
	    $.ajax({
	        type:'post',
	        url: base_url+'controller/visa_passport_ticket/ticket/passenger_csv_save.php',
	        data:{cust_csv_dir : cust_csv_dir,selected_portal: selectedPortal },
	        success:function(result){
	          console.log("CSV Upload Successful");
	          console.log(result);
	            var table = document.getElementById("tbl_dynamic_ticket_master");
				var pass_arr = JSON.parse(result);
				if (pass_arr.length > 0) {
					if (pass_arr[0]['flight_type'] === 'Int') {
						$('#tour_type').val('International');
					} else if (pass_arr[0]['flight_type'] === 'Dom') {
						$('#tour_type').val('Domestic');
					}
				}
	            for (var i = 0; i < pass_arr.length; i++) {
					var row = table.rows[i];
					var fullName = pass_arr[i]['passenger_name'].trim();
					var nameParts = fullName.split(" ").filter(p => p !== "");

					var firstName = '';
					var middleName = '';
					var lastName = '';

					if (nameParts.length === 1) {
						firstName = nameParts[0];
					} else if (nameParts.length === 2) {
						firstName = nameParts[0];
						lastName = nameParts[1];
					} else if (nameParts.length >= 3) {
						firstName = nameParts[0];
						middleName = nameParts[1];
						lastName = nameParts[nameParts.length - 1];
					}

					if (selectedPortal === "Tripjack") {
						row.cells[2].childNodes[0].value = firstName;
						row.cells[3].childNodes[0].value = middleName;
						row.cells[4].childNodes[0].value = lastName;
						row.cells[8].childNodes[0].value = pass_arr[i]['gds_pnr'];
						row.cells[14].childNodes[0].value = pass_arr[i]['search_type'];
						row.cells[15].childNodes[0].value = pass_arr[i]['departure_or_arrival'];
						row.cells[16].childNodes[0].value = pass_arr[i]['flight_duration'];
						row.cells[17].childNodes[0].value = pass_arr[i]['total_fair_amount'] ?? ''; // Optional field
						row.cells[18].childNodes[0].value = pass_arr[i]['flight_travel_date'] ?? ''; // Optional field
						row.cells[21].childNodes[0].value = selectedPortal; 
						row.cells[23].childNodes[0].value = pass_arr[i]['total_fair_amount'];
					} else if (selectedPortal === "TBO-Train") {
						row.cells[2].childNodes[0].value = firstName;
						row.cells[3].childNodes[0].value = middleName;
						row.cells[4].childNodes[0].value = lastName;
						row.cells[7].childNodes[0].value = pass_arr[i]['ticket_no'];
						row.cells[8].childNodes[0].value = pass_arr[i]['flight_pnr'];
						row.cells[15].childNodes[0].value = '';
						row.cells[16].childNodes[0].value = '';
						row.cells[17].childNodes[0].value = '';
						row.cells[18].childNodes[0].value = pass_arr[i]['flight_date'];
						row.cells[19].childNodes[0].value = pass_arr[i]['flight_carrier'];
						row.cells[20].childNodes[0].value = pass_arr[i]['flight_sector'];
						row.cells[21].childNodes[0].value = selectedPortal; 
						row.cells[22].childNodes[0].value = pass_arr[i]['flight_no_with_operator']; 
						row.cells[23].childNodes[0].value = pass_arr[i]['fair_amount']; 
					}
					else if (selectedPortal === "Amadeus") {
						row.cells[2].childNodes[0].value = firstName;
						row.cells[3].childNodes[0].value = middleName;
						row.cells[4].childNodes[0].value = lastName;
						row.cells[7].childNodes[0].value = '';
						row.cells[8].childNodes[0].value = pass_arr[i]['flight_pnr_no'];
						row.cells[15].childNodes[0].value = '';
						row.cells[16].childNodes[0].value = '';
						row.cells[17].childNodes[0].value = '';
						row.cells[18].childNodes[0].value ='';
						row.cells[19].childNodes[0].value = '';
						row.cells[20].childNodes[0].value = '';
						row.cells[21].childNodes[0].value = selectedPortal; 
						row.cells[22].childNodes[0].value = pass_arr[i]['flight_no_with_operator'];
						row.cells[23].childNodes[0].value = pass_arr[i]['fair_amount']; 
						row.cells[24].childNodes[0].value = pass_arr[i]['airline_code'];
						row.cells[25].childNodes[0].value = pass_arr[i]['flight_from'];
						row.cells[26].childNodes[0].value = pass_arr[i]['flight_to'];
						row.cells[27].childNodes[0].value = pass_arr[i]['flight_d_date'];
						row.cells[28].childNodes[0].value = pass_arr[i]['flight_d_time'];
						row.cells[29].childNodes[0].value = pass_arr[i]['flight_a_date'];
						row.cells[30].childNodes[0].value = pass_arr[i]['flight_a_time'];
						row.cells[31].childNodes[0].value = pass_arr[i]['flight_status'];
					}
					else if (selectedPortal === "Galileo") {
						row.cells[2].childNodes[0].value = firstName;
						row.cells[3].childNodes[0].value = middleName;
						row.cells[4].childNodes[0].value = lastName;
						row.cells[7].childNodes[0].value = pass_arr[i]['ticket_no'];
						row.cells[8].childNodes[0].value = pass_arr[i]['flight_pnr_no'];
						row.cells[15].childNodes[0].value = '';
						row.cells[16].childNodes[0].value = '';
						row.cells[17].childNodes[0].value = '';
						row.cells[18].childNodes[0].value ='';
						row.cells[19].childNodes[0].value = '';
						row.cells[20].childNodes[0].value = '';
						row.cells[21].childNodes[0].value = selectedPortal; 
						row.cells[22].childNodes[0].value = pass_arr[i]['flight_no_with_operator'];
						row.cells[23].childNodes[0].value = pass_arr[i]['fair_amount']; 
						row.cells[24].childNodes[0].value =  pass_arr[i]['airline_code'];
						row.cells[25].childNodes[0].value = pass_arr[i]['flight_from'];
						row.cells[26].childNodes[0].value = pass_arr[i]['flight_to'];
						row.cells[27].childNodes[0].value = pass_arr[i]['flight_d_date'];
						row.cells[28].childNodes[0].value = pass_arr[i]['flight_d_time'];
						row.cells[29].childNodes[0].value = pass_arr[i]['flight_a_date'];
						row.cells[30].childNodes[0].value = pass_arr[i]['flight_a_time'];
						row.cells[31].childNodes[0].value = pass_arr[i]['flight_status'];
						row.cells[32].childNodes[0].value = pass_arr[i]['flight_class'];
					}

					console.log("Passenger Name for Row " + i + ":", fullName);
					console.log("Ticket No for Row " + i + ":", row.cells[6].childNodes[0].value);
					console.log("Fair Amount Row " + i + ":", row.cells[23].childNodes[0].value);
					if (i != pass_arr.length - 1) {
						if (table.rows[i + 1] == undefined) {
							addRow('tbl_dynamic_ticket_master');
						}
					}
                } //end of for loop

	        }
	    });
	}
	
	function display_format_modal(){
	    var base_url = $('#base_url').val();
	    window.location = base_url+"images/csv_format/flight_passenger_list.csv";
	}
	function business_rule_load(){
		get_auto_values('booking_date','basic_cost','payment_mode','service_charge','markup','save','true','service_charge','discount');;
	}
	function ticket_reissue(){
	
	  var checkedValue = document.getElementById("reissue_check1").checked
	  var main_ticket = document.getElementsByClassName("main_ticket");
	
	  if(checkedValue === false){
	    $('.main_ticket').attr('type','hidden');
	  }
	  else {
	    $('.main_ticket').attr('type','text');
	  }
	}


  // harshit 03-06-2025 render data in the modal
	function add_flight_details(passenger_id,type='save'){	 
	  console.log("Passenger ID:", passenger_id);
		var match = passenger_id.match(/\d+$/); // extract number at end
		var count = match ? match[0] : null;
		$('#add_trips'+count).prop('disabled',true);
		if (!count) {
			console.error("Could not determine count from ID:", passenger_id);
			return;
		}
		console.log("Passenger ID: " + passenger_id+ " Count: " + count);
		
		var base_url = $('#base_url').val();
		var first_name = $('#first_name' + count).val();
		var middle_name = $('#middle_name' + count).val();
		var last_name = $('#last_name' + count).val();
		var gds_pnr = $('#gds_pnr' + count).val();
		var journey_type = $('#journey_type' + count).val();
		//  || 'One Way';
		var flight_details = $('#flight_details'+count).html();
		var departure_or_arrival = $('#departure_or_arrival' + count).val();
		var flight_duration = $('#flight_duration' + count).val();
		var flight_fair_amount = $('#flight_fair_amount' + count).val();
		var flight_travel_date = $('#flight_travel_date' + count).val();
		var flight_carrier = $('#flight_carrier' + count).val();
		var flight_sector = $('#flight_sector' + count).val();
		var selected_portal = $('#selected_portal' + count).val();
		var flight_no_with_operator = $('#flight_no_with_operator' + count).val();
		var fair_amount = $('#fair_amount' + count).val();
		var airline_code = $('#airline_code' + count).val();
		var flight_from = $('#flight_from' + count).val();
		var flight_to = $('#flight_to' + count).val();
		var flight_d_date = $('#flight_d_date' + count).val();
		var flight_d_time = $('#flight_d_time' + count).val();
		var flight_a_date = $('#flight_a_date' + count).val();
		var flight_a_time = $('#flight_a_time' + count).val();
		var flight_status = $('#flight_status' + count).val();
		var flight_class = $('#flight_class' + count).val();

		console.log("First Name: " + first_name + " Middle Name: " + middle_name + " Last Name: " + last_name);
		console.log("Base Url Details: " + base_url);
		console.log("journey type Details: " + journey_type);
		console.log("departure_or_arrival Details: " + departure_or_arrival);
		console.log("Flight No Details: " + flight_no_with_operator);

	  if(first_name==''){
	    error_msg_alert("Enter passenger's First name!");
	    $('#add_trips'+count).prop('disabled',false);
	    return false;
	  }
	  $('#add_trips'+count).button('loading');
	  $.post(base_url+'view/visa_passport_ticket/ticket/home/add_flight_details.php', { first_name:first_name,  middle_name:middle_name, last_name:last_name, journey_type:journey_type, gds_pnr:gds_pnr, 
		departure_or_arrival:departure_or_arrival,flight_fair_amount:flight_fair_amount, flight_carrier:flight_carrier,
		flight_duration:flight_duration,flight_travel_date:flight_travel_date, flight_details:flight_details,selected_portal:selected_portal,
		flight_sector:flight_sector,flight_no_with_operator:flight_no_with_operator,flight_from:flight_from,flight_to:flight_to,
		fair_amount:fair_amount,airline_code:airline_code,flight_a_date:flight_a_date,flight_a_time:flight_a_time,
		flight_d_date:flight_d_date,flight_d_time:flight_d_time,count:count,flight_status:flight_status,flight_class:flight_class,
		type:type,entry_id:'' }, function(data){
			$('#div_flightd_modal').html(data);
			$('#add_trips'+count).prop('disabled',false);
			$('#add_trips'+count).button('reset');
		});
	}

	// 16-06-2025 @harshit code
	$('#frm_tab1').validate({
		rules: {
			// customer_id: {
			// 	required: {
			// 		depends: function () {
			// 			let portal = $('#flight_select option:selected').val();
			// 			return !['Tripjack', 'Galileo', 'Amadeus', 'TBO-Train'].includes(portal);
			// 		}
			// 	}
			// },

			customer_id: {
  required: true
},
			tour_type1: {
				required: {
					depends: function () {
						let portal = $('#flight_select option:selected').val();
						return !['Tripjack', 'Galileo', 'Amadeus', 'TBO-Train'].includes(portal);
					}
				}
			}
		},
		submitHandler:function(form, e){	
	        e.preventDefault();
	        var adults = 0;
	        var childrens = 0;
	        var infant = 0;
	        var msg = "";
	
	        var table = document.getElementById("tbl_dynamic_ticket_master");
	        var rowCount = table.rows.length;    
          	console.log("Row Count :"+rowCount);   
	        var adult_total = 0;
	        var child_total = 0;
	        var infant_total = 0;
	
	        let checkedRowCount = 0;
	        for(var i=0; i<rowCount; i++){
	          console.log("Row Count :"+rowCount);
	          var row = table.rows[i];
	          if(row.cells[0].childNodes[0].checked)
	          {
				var _first_name = row.cells[2].childNodes[0].value;
	            var first_name = row.cells[3].childNodes[0].value;
	            var middle_name = row.cells[4].childNodes[0].value;
	            var adolescence = row.cells[6].childNodes[0].value;
	            var ticket_no = row.cells[7].childNodes[0].value;
	            var gds_pnr = row.cells[8].childNodes[0].value;
	            var baggage_info = row.cells[9].childNodes[0].value;
	            var seat_no = row.cells[10].childNodes[0].value;
	            var meal_plan = row.cells[11].childNodes[0].value;
	            var main_ticket = row.cells[12].childNodes[0].value;
	            var trip_details = $('#flight_details'+(i+1)).html();	
	            var basic_fare_total = 0;
	            var basic_fare_arr = [];
	            var trip_data_check_arr = [];
	            
	            if(_first_name==""){ 
	              error_msg_alert("First name is required in row:"+(i+1));
	              return false;
	            }
	            if(trip_details==""||trip_details==null){ 
	              error_msg_alert("Flight Ticket Details required in row:"+(i+1));
	              return false;
	            }
	
				
				
	            // var fare_amount = parseFloat(row.cells[23].childNodes[0].value) || 0;
				// basic_fare_total = fare_amount;
	         
// basic fare calculation
				// var trip_details = $('#flight_details'+(i+1)).html();
basic_fare_total = 0;

if(trip_details !== '' && trip_details !== null) {
	try {
		let trip_json = JSON.parse(trip_details);
		let basic_fare_arr = trip_json[0].basic_fare_arr || [];
		let trip_data_check_arr = trip_json[0].trip_data_check_arr || [];

		for (let t = 0; t < basic_fare_arr.length; t++) {
			let fare = parseFloat(basic_fare_arr[t]) || 0;
			if(trip_data_check_arr[t]) {
				basic_fare_total += fare;
			}
		}
	} catch (e) {
		console.error("Invalid trip_details JSON for row "+(i+1), e);
	}
}

				
	            
	            if(adolescence==""){ 
	              error_msg_alert("Adolescence is required in row:"+(i+1));
	              return false;
	            }
	            // if(row.cells[12].childNodes[0].getAttribute('type')=="text" && main_ticket==""){ 
	            //   error_msg_alert("Main Ticket Number is required in row:"+(i+1));
	            //   return false;
	            // }
				if(adolescence=="Adult"){
	              adults = adults + 1;
	              adult_total += basic_fare_total;
	            }
	            if(adolescence=="Child"){
	              childrens = childrens + 1;
	              child_total += basic_fare_total;
	            }
	            if(adolescence=="Infant"){
	              infant = infant + 1;
	              infant_total += basic_fare_total;
	            }
	            checkedRowCount++;
	          }      
	
	        }
	        if (checkedRowCount < 1) {
	          error_msg_alert('Atleast one passenger is required!');
	          return false;
	        }
	        if(msg!=""){
	        	error_msg_alert(msg);
	        	return false;
	        }
			var adolescence1 = $('#adolescence1 option:selected').val();
			// alert(adolescence1);
	        $('#adults').val(adults);
	        $('#childrens').val(childrens);
	        $('#infant').val(infant);
	        $('#adult_fair').val(adult_total);
	        $('#children_fair').val(child_total);
	        $('#infant_fair').val(infant_total);
	
	        calculate_total_amount('abc');
	
	        $('#tab_1_head').addClass('done');
	        $('#tab_3_head').addClass('active');
	        $('.bk_tab').removeClass('active');
	        $('#tab3').addClass('active');
	        $('html, body').animate({ scrollTop: $('.bk_tab_head').offset().top }, 200);
	
		}
	
	});
	
</script>