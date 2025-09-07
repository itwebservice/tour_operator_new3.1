<?php
include "../../../../model/model.php";

$email_id = $_POST['email_id'];
$mobile_no = $_POST['mobile_no'];

$branch_admin_id = $_SESSION['branch_admin_id'];
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];

// Function to get quotation URLs for a given quotation_id
function getQuotationUrls($quotation_id) {
    global $app_quot_format;
    
    if ($app_quot_format == 2) {
        $url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_2/fit_quotation_html.php?quotation_id=$quotation_id";
        $urldoc = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_2/fit_quotation_html_doc.php?quotation_id=$quotation_id";
    } else if ($app_quot_format == 3) {
        $url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_3/fit_quotation_html.php?quotation_id=$quotation_id";
        $urldoc = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_3/fit_quotation_html_doc.php?quotation_id=$quotation_id";
    } else if ($app_quot_format == 4) {
        $url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_4/fit_quotation_html.php?quotation_id=$quotation_id";
        $urldoc = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_4/fit_quotation_html_doc.php?quotation_id=$quotation_id";
    } else if ($app_quot_format == 5) {
        $url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_5/fit_quotation_html.php?quotation_id=$quotation_id";
        $urldoc = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_5/fit_quotation_html_doc.php?quotation_id=$quotation_id";
    } else if ($app_quot_format == 6) {
        $url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_6/fit_quotation_html.php?quotation_id=$quotation_id";
        $urldoc = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_6/fit_quotation_html_doc.php?quotation_id=$quotation_id";
    } else {
        $url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_1/fit_quotation_html.php?quotation_id=$quotation_id";
        $urldoc = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_1/fit_quotation_html_doc.php?quotation_id=$quotation_id";
    }
    
    return array('pdf_url' => $url1, 'word_url' => $urldoc);
}



$whatsapp_tooltip_change = ($whatsapp_switch == "on") ? 'Email and What\'sApp Quotation to Customer' : "Email Quotation to Customer";

// Function to get hotel availability status for a quotation
function getHotelAvailabilityStatus($quotation_id) {
	$sq_h_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_hotel_entries where quotation_id='$quotation_id'"));
	$avail_count = 0;
	$not_avail_count = 0;
	$req_count = 0;
	$sq_hotel = mysqlQuery("select * from package_tour_quotation_hotel_entries where quotation_id='$quotation_id'");
	while ($row_hotel = mysqli_fetch_assoc($sq_hotel)) {
		if ($row_hotel['request_sent'] == '0') {
			$req_count++;
		} else {
			$avail = isset($row_hotel['availability']) ? json_decode($row_hotel['availability']) : [];
			if (isset($avail) && ($avail->availability == 'Available' || $avail->availability == 'NA')) {
				$avail_count++;
			} else {
				$hotel_options = !empty($avail->option_hotel_arr) && $avail->option_hotel_arr != "null" ? $avail->option_hotel_arr : [];
				if (!empty($hotel_options) && $hotel_options != "null") {
					for ($j = 0; $j < sizeof($hotel_options); $j++) {
						if ($hotel_options[$j]->availability == 'Available' || $hotel_options[$j]->availability == 'NA') {
							$avail_count++;
						} else {
							$not_avail_count++;
						}
					}
				} else {
					$not_avail_count++;
				}
			}
		}
	}
	
	if ($req_count > 0) {
		$req_btn_class = 'btn-info';
		$title = "Send Hotel Availability Request";
	} else if ($sq_h_count == $avail_count || $sq_h_count <= $avail_count) {
		$req_btn_class = 'btn-warning';
		$title = "Hotel Availability Request(All hotels are available)";
	} else {
		$req_btn_class = 'btn-danger';
		$title = "Hotel Availability Request(Request is in process)";
	}
	
	return array('class' => $req_btn_class, 'title' => $title);
}
$q = "select * from branch_assign where link='package_booking/quotation/home/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count > 0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';

$email_id = $_POST['email_id'];
$mobile_no = $_POST['mobile_no'];

$query = "select * from package_tour_quotation_master where email_id = '$email_id'  and status='1'";
if ($role != 'Admin' && $role != 'Branch Admin') {
	$query .= " and emp_id='$emp_id'";
}
if ($branch_status == 'yes' && $role == 'Branch Admin') {
	$query .= " and branch_admin_id = '$branch_admin_id'";
}
if ($branch_admin_id != '' && $role == 'Branch Admin') {
	$query .= " and branch_admin_id = '$branch_admin_id'";
}
$query .= ' ORDER BY `quotation_id` DESC';
$sq_query = mysqlQuery($query);

// Get the first quotation details for the modal title
$first_quotation = mysqli_fetch_assoc(mysqlQuery($query . ' LIMIT 1'));
$modal_title = "Send Quotation";
$main_quotation_id = "";
if ($first_quotation) {
    $sq_first_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '{$first_quotation['package_id']}'"));
    $quotation_date = $first_quotation['quotation_date'];
    $yr = explode("-", $quotation_date);
    $year = $yr[0];
    $quotation_id_display = get_quotation_id($first_quotation['quotation_id'], $year);
    $modal_title = $sq_first_package['package_name'] . " - " . $quotation_id_display;
    $main_quotation_id = $quotation_id_display;
}

// Reset the query for the main loop
$sq_query = mysqlQuery($query);
?>
<input type="hidden" id="whatsapp_switch" value="<?= $whatsapp_switch ?>">
<div class="modal fade" id="quotation_send_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><?= $modal_title ?></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<input type="checkbox" id="check_all" name="check_all" onClick="select_all_check(this.id,'custom_package')">&nbsp;&nbsp;&nbsp;<span style="text-transform: initial;">Check All</span>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="table-responsive">
							<table class="table table-hover table-bordered no-marg" id="tbl_tour_list">
								<tr class="table-heading-row">
									<th></th>
									<th>Sr No.</th>
									<th>Quotation ID</th>
									<th>Quotation Cost</th>
									<th>Updated Date</th>
									<th>Actions</th>
								</tr>
								<?php
								$quotation_cost = 0;
								$count  = 1;
								while ($row_tours = mysqli_fetch_assoc($sq_query)) {
									$sq_tours_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '$row_tours[package_id]'"));
									$sq_cost = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$row_tours[quotation_id]'"));

									$basic_cost = $sq_cost['basic_amount'];
									$service_charge = $sq_cost['service_charge'];
									$service_tax_amount = 0;
									$tax_show = '';
									// $bsmValues = json_decode($sq_cost['bsmValues']);
									$bsmValues = json_decode($sq_cost['bsmValues'], true);
									$discount_in = $sq_cost['discount_in'];
									$discount = $sq_cost['discount'];
									if ($discount_in == 'Percentage') {
										$act_discount = (float)($service_charge) * (float)($discount) / 100;
									} else {
										$act_discount = ($service_charge != 0) ? $discount : 0;
									}
									$service_charge = $service_charge - (float)($act_discount);
									$tour_cost = $basic_cost + $service_charge;
									$name = '';
									if ($sq_cost['service_tax_subtotal'] !== 0.00 && ($sq_cost['service_tax_subtotal']) !== '') {
										$service_tax_subtotal1 = explode(',', $sq_cost['service_tax_subtotal']);
										for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
											$service_tax = explode(':', $service_tax_subtotal1[$i]);
											$service_tax_amount = (float)($service_tax_amount) + (float)($service_tax[2]);
											$name .= $service_tax[0] . ' ';
											$percent = $service_tax[1];
										}
									}
									if ($bsmValues[0]->service != '') {   //inclusive service charge
										$newBasic = $tour_cost + $service_tax_amount;
										$tax_show = '';
									} else {
										$tax_show =  $name . $percent . ($service_tax_amount);
										$newBasic = $tour_cost;
									}
									////////////Basic Amount Rules
									if ($bsmValues[0]->basic != '') { //inclusive markup
										$newBasic = $tour_cost + $service_tax_amount;
										$tax_show = '';
									}



									if (isset($bsmValues[0]['tcsper']) && $bsmValues[0]['tcsper'] != 'NaN') {
										$tcsper = $bsmValues[0]['tcsper'];
										$tcsvalue = $bsmValues[0]['tcsvalue'];
									} else {
										$tcsper = 0;
										$tcsvalue = 0;
									}

									$basic_cost = $sq_cost['basic_amount'];
									$service_charge = $sq_cost['service_charge'];




									// $quotation_cost = $sq_cost['total_tour_cost']+ $row_tours['train_cost'] + $row_tours['cruise_cost']+ $row_tours['flight_cost'] + $row_tours['visa_cost'] + $row_tours['guide_cost'] + $row_tours['misc_cost'];
									// $quotation_cost = ceil($quotation_cost);

									$quotation_cost = $basic_cost + $service_charge + $service_tax_amount + $row_tours['train_cost'] + $row_tours['cruise_cost'] + $row_tours['flight_cost'] + $row_tours['visa_cost'] + $row_tours['guide_cost'] + $row_tours['misc_cost'] + (float)($tcsvalue) - $act_discount;

									$quotation_cost_1 = currency_conversion($currency, $row_tours['currency_code'], $quotation_cost);

									$quotation_date = $row_tours['quotation_date'];
									$yr = explode("-", $quotation_date);
									$year = $yr[0];
								?>
									<tr>
										<td><input type="checkbox" value="<?php echo $row_tours['quotation_id']; ?>" id="<?php echo $row_tours['quotation_id']; ?>" name="custom_package" class="custom_package" /></td>
										<td><?php echo $count; ?></td>
										<td><?php echo get_quotation_id($row_tours['quotation_id'], $year); ?></td>
										<td><?= $quotation_cost_1 ?></td>
										<td><?php echo get_date_user($row_tours['updated_at']); ?></td>
										<td>
    <?php
    // Get URLs for this specific quotation
    $urls = getQuotationUrls($row_tours['quotation_id']);
    $url1 = $urls['pdf_url'];
    $urldoc = $urls['word_url'];
    
    // Get hotel availability status for this quotation
    $hotel_status = getHotelAvailabilityStatus($row_tours['quotation_id']);
    $req_btn_class = $hotel_status['class'];
    $title = $hotel_status['title'];
    ?>
    
    <!-- PDF Download -->
    <a data-toggle="tooltip" onclick="loadOtherPage('<?php echo $url1; ?>')" class="btn btn-info btn-sm" title="Download Quotation PDF">
        <i class="fa fa-print"></i>
    </a>

    <!-- Word Download -->
    <a data-toggle="tooltip" onclick="exportHTML('<?php echo $urldoc; ?>')" class="btn btn-info btn-sm" title="Download Quotation Word">
        <i class="fa fa-file-word-o"></i>
    </a>

    <!-- WhatsApp Quotation -->
    <button class="btn btn-info btn-sm" onclick="quotation_whatsapp(<?php echo $row_tours['quotation_id']; ?>)" title="What'sApp Quotation to customer" data-toggle="tooltip">
        <i class="fa fa-whatsapp"></i>
    </button>

    <!-- Email to Customer -->
        <a data-toggle="tooltip" href="javascript:void(0)" 
       id="btn_email_<?php echo $count; ?>" 
       class="btn btn-info btn-sm" 
       onclick="openEmailWhatsappModal({
           quotation_id: <?php echo $row_tours['quotation_id']; ?>,
           email_id: '<?php echo $row_tours['email_id']; ?>',
           mobile_no: '<?php echo $row_tours['mobile_no']; ?>',
           package_name: '<?php echo addslashes($sq_tours_package['package_name']); ?>',
           customer_name: '<?php echo addslashes($row_tours['customer_name']); ?>'
       })" 
       title="<?php echo $whatsapp_tooltip_change; ?>">
        <i class="fa fa-envelope-o"></i>
    </a>

    <!-- Email to Backoffice -->
    <a href="javascript:void(0)" id="btn_email1_<?php echo $count; ?>" title="Email Quotation to Backoffice" class="btn btn-info btn-sm"
       onclick="quotation_email_send_backoffice_modal(<?php echo $row_tours['quotation_id']; ?>);btnDisableEnable(this.id)" 
       id="email_backoffice_btn-<?php echo $row_tours['quotation_id']; ?>">
        <i class="fa fa-paper-plane-o"></i>
    </a>

    <!-- Hotel Request -->
    <button data-toggle="tooltip" style="display:inline-block" class="btn <?php echo $req_btn_class; ?> btn-sm" 
            onclick="view_request(<?php echo $row_tours['quotation_id']; ?>)" 
            id="view_req<?php echo $row_tours['quotation_id']; ?>" 
            title="<?php echo $title; ?>">
        <i class="fa fa-paper-plane-o"></i>
    </button>

    <!-- Copy Quotation -->
    <button data-toggle="tooltip" style="display:inline-block" class="btn btn-warning btn-sm" 
            onclick="quotation_clone(<?php echo $row_tours['quotation_id']; ?>)" 
            title="Create Copy of this Quotation">
        <i class="fa fa-files-o"></i>
    </button>

    <!-- View Details -->
    <a data-toggle="tooltip" style="display:inline-block" href="quotation_view.php?quotation_id=<?php echo $row_tours['quotation_id']; ?>" target="_BLANK" class="btn btn-info btn-sm" title="View Details">
        <i class="fa fa-eye"></i>
    </a>

	<?php
$to_date = $row_tours['to_date'];
$today = date('Y-m-d');

// Default update button form
$update_btn = '
    <form style="display:inline-block" action="update/index.php" id="frm_booking_' . $count . '" method="POST">
        <input type="hidden" id="quotation_id" name="quotation_id" value="' . $row_tours['quotation_id'] . '">
        <input type="hidden" id="package_id" name="package_id" value="' . $row_tours['package_id'] . '">
        <button data-toggle="tooltip" style="display:inline-block" class="btn btn-info btn-sm" title="Update Details">
            <i class="fa fa-pencil-square-o"></i>
        </button>
    </form>';

// Hide if conditions not satisfied
if ($to_date < $today && $modify_entries_switch == 'No' && $role != 'Admin' && $role != 'Branch Admin') {
    $update_btn = '';
}	
?>

    <!-- Conditionally added Update button -->
    <?php echo $update_btn; ?>
</td>

									</tr>
								<?php $count++;
								}
								?>
							</table>
						</div>
					</div>
				</div>



				<div class="row ">
					<div class="col-md-12">
						<div class="col-md-4 mg_tp_20">
							<select name="email_option" id="email_option" class="form-control" style="width:100%">
								<option value="By HTML">By HTML</option>
								<option value="Email Body">Email Body</option>
							</select>
						</div>
						<div class="col-md-4 mg_tp_20">
							<button class="btn btn-sm btn-success" id="btn_quotation_send" onclick="multiple_quotation_mail();"><i class="fa fa-paper-plane-o"></i>&nbsp;&nbsp;<?php echo ($whatsapp_switch == "on") ? "Send on Email and What's App" : "Send on Email" ?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Email/WhatsApp Modal -->
<div class="modal fade" id="emailWhatsappModal" tabindex="-1" role="dialog" aria-labelledby="emailWhatsappModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailWhatsappModalLabel"><?= $modal_title ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="communicationTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="email-tab" data-toggle="tab" href="#email-content" role="tab" aria-controls="email-content" aria-selected="true">Email</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="whatsapp-tab" data-toggle="tab" href="#whatsapp-content" role="tab" aria-controls="whatsapp-content" aria-selected="false">WhatsApp</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="communicationTabContent">
                    <!-- Email Tab -->
                    <div class="tab-pane fade show active" id="email-content" role="tabpanel" aria-labelledby="email-tab">
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <!-- Checkbox Options -->
                                <div class="form-group">
                                    <label class="font-weight-bold">Select Content Options:</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input email-option" type="checkbox" id="emailPriceStructure" name="emailOptions[]" value="price_structure" checked>
                                                <label class="form-check-label" for="emailPriceStructure">Price Structure</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input email-option" type="checkbox" id="emailInclusionExclusion" name="emailOptions[]" value="inclusion_exclusion" checked>
                                                <label class="form-check-label" for="emailInclusionExclusion">Inclusion/Exclusion</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input email-option" type="checkbox" id="emailTermsConditions" name="emailOptions[]" value="terms_conditions" checked>
                                                <label class="form-check-label" for="emailTermsConditions">Terms & Conditions</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input email-option" type="checkbox" id="emailItinerary" name="emailOptions[]" value="itinerary" checked>
                                                <label class="form-check-label" for="emailItinerary">Itinerary</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary" id="sendEmailBtn">Send Mail</button>
                                    <button type="button" class="btn btn-info" id="copyEmailBtn">Copy Email</button>
                                </div>

                                <!-- Email Preview -->
                                <div class="form-group">
                                    <label class="font-weight-bold text-primary">Email Preview:</label>
                                    <div id="emailPreviewArea">
                                        <!-- Email preview will be loaded here -->
                                        <div class="p-3 text-center text-muted">
                                            <i class="fa fa-envelope fa-2x mb-2"></i>
                                            <p>Email preview will appear here...</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email Draft -->
                                <div class="form-group">
                                    <label class="font-weight-bold text-primary">Email Draft (Email Body Format):</label>
                                    <div id="emailDraftArea">
                                        <!-- Email draft will be loaded here -->
                                        <div class="p-3 text-center text-muted">
                                            <i class="fa fa-file-text fa-2x mb-2"></i>
                                            <p>Email draft will appear here...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Tab -->
                    <div class="tab-pane fade" id="whatsapp-content" role="tabpanel" aria-labelledby="whatsapp-tab">
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <!-- Checkbox Options for WhatsApp -->
                                <div class="form-group">
                                    <label class="font-weight-bold">Select Content Options:</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input whatsapp-option" type="checkbox" id="whatsappPriceStructure" name="whatsappOptions[]" value="price_structure" checked>
                                                <label class="form-check-label" for="whatsappPriceStructure">Price Structure</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input whatsapp-option" type="checkbox" id="whatsappInclusionExclusion" name="whatsappOptions[]" value="inclusion_exclusion" checked>
                                                <label class="form-check-label" for="whatsappInclusionExclusion">Inclusion/Exclusion</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input whatsapp-option" type="checkbox" id="whatsappTermsConditions" name="whatsappOptions[]" value="terms_conditions" checked>
                                                <label class="form-check-label" for="whatsappTermsConditions">Terms & Conditions</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input whatsapp-option" type="checkbox" id="whatsappItinerary" name="whatsappOptions[]" value="itinerary" checked>
                                                <label class="form-check-label" for="whatsappItinerary">Itinerary</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons for WhatsApp -->
                                <div class="form-group">
                                    <button type="button" class="btn btn-success" id="sendWhatsappBtn">Send WhatsApp</button>
                                    <button type="button" class="btn btn-info" id="copyWhatsappBtn">Copy WhatsApp</button>
                                </div>

                                <!-- WhatsApp Preview -->
                                <div class="form-group">
                                    <label class="font-weight-bold text-success">WhatsApp Preview:</label>
                                    <div id="whatsappPreviewArea">
                                        <!-- WhatsApp preview will be loaded here -->
                                        <div class="p-3 text-center text-muted">
                                            <i class="fa fa-whatsapp fa-2x mb-2 text-success"></i>
                                            <p>WhatsApp preview will appear here...</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- WhatsApp Draft -->
                                <div class="form-group">
                                    <label class="font-weight-bold text-success">WhatsApp Draft:</label>
                                    <div id="whatsappDraftArea">
                                        <!-- WhatsApp draft will be loaded here -->
                                        <div class="p-3 text-center text-muted">
                                            <i class="fa fa-file-text fa-2x mb-2 text-success"></i>
                                            <p>WhatsApp draft will appear here...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
	$('#quotation_send_modal').modal('show');
	$('#email_option').select2();

	function select_all_check(id, custom_package) {
		var checked = $('#' + id).is(':checked');
		// Select all
		if (checked) {
			$('.custom_package1').each(function() {
				$(this).prop("checked", true);
			});
		} else {
			// Deselect All
			$('.custom_package1').each(function() {
				$(this).prop("checked", false);
			});
		}
	}

	function multiple_quotation_mail() {
		var quotation_id_arr = new Array();
		var base_url = $('#base_url').val();
		var email_option = $('#email_option').val();
		$('input[name="custom_package"]:checked').each(function() {
			quotation_id_arr.push($(this).val());
		});
		if (email_option == '') {
			error_msg_alert('Please select Email Option!');
			return false;
		}
		if (quotation_id_arr.length == 0) {
			error_msg_alert('Please select at least one quotation!');
			return false;
		}
		if ($('#whatsapp_switch').val() == "on") sendOn_whatsapp(base_url, quotation_id_arr);

		$('#btn_quotation_send').button('loading');
		$.ajax({
			type: 'post',
			url: base_url + 'controller/package_tour/quotation/quotation_email_send.php',
			data: {
				quotation_id_arr: quotation_id_arr,
				email_option: email_option
			},
			success: function(message) {
				msg_alert(message);
				$('#btn_quotation_send').button('reset');
				$('#quotation_send_modal').modal('hide');
			}
		});
	}

	function sendOn_whatsapp(base_url, quotation_id_arr) {
		$.post(base_url + 'controller/package_tour/quotation/quotation_whatsapp.php', {
			quotation_id_arr: quotation_id_arr
		}, function(link) {
			$('#custom_package_msg').button('reset');
			window.open(link, '_blank');
		});
	}

	// Function to open Email/WhatsApp modal
	function openEmailWhatsappModal(quotationData) {
		// Store quotation data for use in modal
		window.currentQuotationData = quotationData;
		
		// Show modal
		$('#emailWhatsappModal').modal('show');
		
		// Ensure proper tab state - Email tab active by default
        $('#email-tab').trigger('click').addClass('active').attr('aria-selected', 'true');

		$('#whatsapp-tab').removeClass('active').attr('aria-selected', 'false');
		$('#email-content').addClass('show active');
		$('#whatsapp-content').removeClass('show active');
		
		// Load email content after modal is shown
		setTimeout(function() {
			loadEmailContent(quotationData.quotation_id);
		}, 300);
	}

	// Function to load email content
	function loadEmailContent(quotation_id) {
		// Show loading state
		$('#emailPreviewArea').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Loading email content...</div>');
		$('#emailDraftArea').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Loading email draft...</div>');

		// Gather currently selected email options
		var selectedOptions = [];
		$('.email-option:checked').each(function() {
			selectedOptions.push($(this).val());
		});

		// Load email body content (same as what gets sent)
		$.post('get_email_body_content.php', {
			quotation_id: quotation_id,
			email_option: 'Email Body',
			options: selectedOptions
		}, function(data) {
                console.log(data , 'emaillll');
			if (data && data.trim() !== '') {
				// Format the content for preview (with proper line breaks)
				var formattedContent = data.replace(/\n/g, '<br>');
				$('#emailPreviewArea').html(formattedContent);
				$('#emailDraftArea').html(data);
			} else {
				$('#emailPreviewArea').html('<div class="p-3"><h5>Email Content Preview</h5><p class="text-muted">Email content will be displayed here based on your selections.</p></div>');
				$('#emailDraftArea').html('<div class="p-3"><h5>Email Draft</h5><p class="text-muted">Email draft content will be displayed here.</p></div>');
			}
		}).fail(function() {
			$('#emailPreviewArea').html('<div class="p-3"><h5>Email Content Preview</h5><p class="text-muted">Email content will be displayed here based on your selections.</p></div>');
			$('#emailDraftArea').html('<div class="p-3"><h5>Email Draft</h5><p class="text-muted">Email draft content will be displayed here.</p></div>');
		});
	}

	// Function to load WhatsApp content
	function loadWhatsappContent(quotation_id) {
		// Show loading state
		$('#whatsappPreviewArea').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Loading WhatsApp content...</div>');
		$('#whatsappDraftArea').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Loading WhatsApp draft...</div>');

		// Gather currently selected WhatsApp options
		var selectedOptions = [];
		$('.whatsapp-option:checked').each(function() {
			selectedOptions.push($(this).val());
		});

		// Load WhatsApp content (similar to email but formatted for WhatsApp)
		$.post('get_email_body_content.php', {
			quotation_id: quotation_id,
			email_option: 'WhatsApp',
			options: selectedOptions
		}, function(data) {
			if (data && data.trim() !== '') {
				// Format the content for WhatsApp preview (with proper line breaks)
				var formattedContent = data.replace(/\n/g, '<br>');
				$('#whatsappPreviewArea').html(formattedContent);
				$('#whatsappDraftArea').html(data);
			} else {
				$('#whatsappPreviewArea').html('<div class="p-3"><h5>WhatsApp Content Preview</h5><p class="text-muted">WhatsApp content will be displayed here based on your selections.</p></div>');
				$('#whatsappDraftArea').html('<div class="p-3"><h5>WhatsApp Draft</h5><p class="text-muted">WhatsApp draft content will be displayed here.</p></div>');
			}
		}).fail(function() {
			$('#whatsappPreviewArea').html('<div class="p-3"><h5>WhatsApp Content Preview</h5><p class="text-muted">WhatsApp content will be displayed here based on your selections.</p></div>');
			$('#whatsappDraftArea').html('<div class="p-3"><h5>WhatsApp Draft</h5><p class="text-muted">WhatsApp draft content will be displayed here.</p></div>');
		});
	}

	// Function to update content based on checkbox selections
	function updateContentPreview(type) {
		var selectedOptions = [];
		var optionClass = type === 'email' ? '.email-option' : '.whatsapp-option';
		var previewArea = type === 'email' ? '#emailPreviewArea' : '#whatsappPreviewArea';
		var draftArea = type === 'email' ? '#emailDraftArea' : '#whatsappDraftArea';
		
		$(optionClass + ':checked').each(function() {
			selectedOptions.push($(this).val());
		});
		
		// Load content based on selected options
		if (window.currentQuotationData) {
			$.post('get_email_body_content.php', {
				quotation_id: window.currentQuotationData.quotation_id,
				email_option: type === 'email' ? 'Email Body' : 'WhatsApp',
				options: selectedOptions
			}, function(data) {
				if (data && data.trim() !== '') {
					// Format the content for preview (with proper line breaks)
					var formattedContent = data.replace(/\n/g, '<br>');
					$(previewArea).html(formattedContent);
					$(draftArea).html(data);
				} else {
					$(previewArea).html('<div class="p-3"><h5>Content Preview</h5><p class="text-muted">Content will be displayed here based on your selections.</p></div>');
					$(draftArea).html('<div class="p-3"><h5>Draft</h5><p class="text-muted">Draft content will be displayed here.</p></div>');
				}
			}).fail(function() {
				$(previewArea).html('<div class="p-3"><h5>Content Preview</h5><p class="text-muted">Content will be displayed here based on your selections.</p></div>');
				$(draftArea).html('<div class="p-3"><h5>Draft</h5><p class="text-muted">Draft content will be displayed here.</p></div>');
			});
		}
	}

	// Event handlers for the new modal
	$(document).ready(function() {
	// Send Email button
	$('#sendEmailBtn').click(function() {
		var selectedOptions = [];
		$('input[name="emailOptions[]"]:checked').each(function() {
			selectedOptions.push($(this).val());
		});
		
		console.log('Selected Email Options:', selectedOptions);
		
		// Call individual email send function
		if (window.currentQuotationData) {
			sendIndividualQuotationEmail(window.currentQuotationData.quotation_id, 
				window.currentQuotationData.email_id, selectedOptions);
		}
	});

		// Copy Email button
		$('#copyEmailBtn').click(function() {
			var emailContent = $('#emailDraftArea').text();
			navigator.clipboard.writeText(emailContent).then(function() {
				alert('Email content copied to clipboard!');
			});
		});

	// Send WhatsApp button
	$('#sendWhatsappBtn').click(function() {
		var selectedOptions = [];
		$('input[name="whatsappOptions[]"]:checked').each(function() {
			selectedOptions.push($(this).val());
		});
		
		console.log('Selected WhatsApp Options:', selectedOptions);
		
		if (window.currentQuotationData) {
			sendIndividualQuotationWhatsApp(window.currentQuotationData.quotation_id, 
				window.currentQuotationData.mobile_no, selectedOptions);
		}
	});

		// Copy WhatsApp button
		$('#copyWhatsappBtn').click(function() {
			var whatsappContent = $('#whatsappDraftArea').text();
			navigator.clipboard.writeText(whatsappContent).then(function() {
				alert('WhatsApp content copied to clipboard!');
			});
		});

		// Checkbox change events for Email
		$('.email-option').change(function() {
			updateContentPreview('email');
		});

		// Checkbox change events for WhatsApp
		$('.whatsapp-option').change(function() {
			updateContentPreview('whatsapp');
		});

		// Tab change events
		$('#email-tab').on('click', function() {
			// Remove active class from WhatsApp tab
			$('#whatsapp-tab').removeClass('active').attr('aria-selected', 'false');
			$('#whatsapp-content').removeClass('show active');
			
			// Add active class to Email tab
			$(this).addClass('active').attr('aria-selected', 'true');
			$('#email-content').addClass('show active');
			
			if (window.currentQuotationData) {
				loadEmailContent(window.currentQuotationData.quotation_id);
			}
		});
		
		$('#whatsapp-tab').on('click', function() {
			// Remove active class from Email tab
			$('#email-tab').removeClass('active').attr('aria-selected', 'false');
			$('#email-content').removeClass('show active');
			
			// Add active class to WhatsApp tab
			$(this).addClass('active').attr('aria-selected', 'true');
			$('#whatsapp-content').addClass('show active');
			
			if (window.currentQuotationData) {
				loadWhatsappContent(window.currentQuotationData.quotation_id);
			}
		});
	});

	// Function to send individual quotation email
	function sendIndividualQuotationEmail(quotation_id, email_id, selectedOptions) {
		var base_url = $('#base_url').val();
		$('#sendEmailBtn').button('loading');
		
		console.log('Sending email with options:', selectedOptions);
		
		$.ajax({
			type: 'post',
			url: base_url + 'controller/package_tour/quotation/quotation_email_send_individual.php',
			data: {
				quotation_id: quotation_id,
				email_id: email_id,
				email_option: 'Email Body',
				options: selectedOptions
			},
			success: function(message) {
				console.log('Email sent successfully');
				msg_alert(message);
				$('#sendEmailBtn').button('reset');
				$('#emailWhatsappModal').modal('hide');
			},
			error: function() {
				console.log('Email sending failed');
				error_msg_alert('Error sending email. Please try again.');
				$('#sendEmailBtn').button('reset');
			}
		});
	}

	// Function to send individual quotation WhatsApp
	function sendIndividualQuotationWhatsApp(quotation_id, mobile_no, selectedOptions) {
		var base_url = $('#base_url').val();
		$('#sendWhatsappBtn').button('loading');
		
		console.log('Sending WhatsApp with options:', selectedOptions);
		
		$.ajax({
			type: 'post',
			url: base_url + 'controller/package_tour/quotation/quotation_whatsapp_individual.php',
			data: {
				quotation_id: quotation_id,
				mobile_no: mobile_no,
				options: selectedOptions
			},
			success: function(link) {
				console.log('WhatsApp sent successfully');
				$('#sendWhatsappBtn').button('reset');
				window.open(link, '_blank');
				$('#emailWhatsappModal').modal('hide');
			},
			error: function() {
				console.log('WhatsApp sending failed');
				error_msg_alert('Error sending WhatsApp. Please try again.');
				$('#sendWhatsappBtn').button('reset');
			}
		});
	}
</script>
<script src="<?php echo BASE_URL ?>view/package_booking/quotation/js/quotation.js"></script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>

<style>
    /* Action buttons styling in modal */
    #tbl_tour_list td:last-child {
        white-space: nowrap;
    }
    
    #tbl_tour_list .btn {
        margin: 1px;
        padding: 4px 8px;
        font-size: 12px;
    }
    
    #tbl_tour_list .btn-group .btn {
        margin-right: 2px;
        margin-bottom: 2px;
    }
    
    /* Modal table responsive */
    .table-responsive {
        max-height: 400px;
        overflow-y: auto;
    }
    
    /* Action buttons container */
    .action-buttons-container {
        display: flex;
        flex-wrap: wrap;
        gap: 2px;
    }
    
    /* Tooltip styling */
    .btn[data-toggle="tooltip"] {
        cursor: pointer;
    }
    
    /* Email/WhatsApp Modal Styling */
    #emailWhatsappModal .nav-tabs .nav-link {
        border: 1px solid #dee2e6;
        border-bottom: none;
        border-radius: 0.25rem 0.25rem 0 0;
        margin-right: 2px;
    }
    
    #emailWhatsappModal .nav-tabs .nav-link.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    
    #emailWhatsappModal .nav-tabs .nav-link:hover {
        border-color: #007bff;
    }
    
    #emailWhatsappModal .form-check {
        margin-bottom: 0.5rem;
    }
    
    #emailWhatsappModal .btn {
        margin-right: 10px;
        margin-bottom: 5px;
    }
    
    #emailPreviewArea, #whatsappPreviewArea, #emailDraftArea, #whatsappDraftArea {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        min-height: 250px;
        max-height: 400px;
        overflow-y: auto;
        padding: 15px;
    }
    
    #emailPreviewArea, #whatsappPreviewArea {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: #333;
    }
    
    #emailDraftArea, #whatsappDraftArea {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        line-height: 1.4;
        background-color: #f8f9fa;
        color: #495057;
    }
    
    /* Remove excessive white space */
    #emailPreviewArea *, #whatsappPreviewArea *, #emailDraftArea *, #whatsappDraftArea * {
        margin: 0;
        padding: 0;
    }
    
    #emailPreviewArea p, #whatsappPreviewArea p, #emailDraftArea p, #whatsappDraftArea p {
        margin-bottom: 10px;
    }
    
    #emailPreviewArea h1, #whatsappPreviewArea h1, #emailDraftArea h1, #whatsappDraftArea h1,
    #emailPreviewArea h2, #whatsappPreviewArea h2, #emailDraftArea h2, #whatsappDraftArea h2,
    #emailPreviewArea h3, #whatsappPreviewArea h3, #emailDraftArea h3, #whatsappDraftArea h3 {
        margin-bottom: 15px;
        margin-top: 20px;
    }
    
    #emailPreviewArea h1:first-child, #whatsappPreviewArea h1:first-child, 
    #emailDraftArea h1:first-child, #whatsappDraftArea h1:first-child {
        margin-top: 0;
    }
    
    #emailWhatsappModal .modal-dialog {
        max-width: 1400px;
        width: 95%;
        margin: 20px auto;
    }
    
    #emailWhatsappModal .modal-xl {
        max-width: 1400px;
        width: 95%;
    }
    
    #emailWhatsappModal .modal-content {
        height: 90vh;
        display: flex;
        flex-direction: column;
    }
    
    #emailWhatsappModal .modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }
    
    #emailWhatsappModal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom: none;
        padding: 15px 20px;
    }
    
    #emailWhatsappModal .modal-header .close {
        color: white;
        opacity: 0.8;
        text-shadow: none;
    }
    
    #emailWhatsappModal .modal-header .close:hover {
        opacity: 1;
    }
    
    #emailWhatsappModal .modal-title {
        font-weight: 600;
        font-size: 18px;
    }
    
    #emailWhatsappModal .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }
    
    #emailWhatsappModal .form-check-input:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    /* Enhanced tab styling */
    #emailWhatsappModal .nav-tabs {
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 20px;
    }
    
    #emailWhatsappModal .nav-tabs .nav-link {
        border: none;
        border-radius: 8px 8px 0 0;
        margin-right: 5px;
        padding: 12px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    #emailWhatsappModal .nav-tabs .nav-link:hover {
        border: none;
        background-color: #f8f9fa;
        color: #495057;
    }
    
    #emailWhatsappModal .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        border: none;
        box-shadow: 0 2px 4px rgba(0,123,255,0.3);
    }
    
    /* Button styling */
    #emailWhatsappModal .btn {
        border-radius: 6px;
        font-weight: 500;
        padding: 8px 16px;
        transition: all 0.3s ease;
    }
    
    #emailWhatsappModal .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    /* Form group spacing */
    #emailWhatsappModal .form-group {
        margin-bottom: 25px;
    }
    
    /* Loading spinner styling */
    .fa-spinner {
        color: #007bff;
    }
    
    /* Content area scrollbar styling */
    #emailPreviewArea::-webkit-scrollbar,
    #whatsappPreviewArea::-webkit-scrollbar,
    #emailDraftArea::-webkit-scrollbar,
    #whatsappDraftArea::-webkit-scrollbar {
        width: 8px;
    }
    
    #emailPreviewArea::-webkit-scrollbar-track,
    #whatsappPreviewArea::-webkit-scrollbar-track,
    #emailDraftArea::-webkit-scrollbar-track,
    #whatsappDraftArea::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    #emailPreviewArea::-webkit-scrollbar-thumb,
    #whatsappPreviewArea::-webkit-scrollbar-thumb,
    #emailDraftArea::-webkit-scrollbar-thumb,
    #whatsappDraftArea::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    #emailPreviewArea::-webkit-scrollbar-thumb:hover,
    #whatsappPreviewArea::-webkit-scrollbar-thumb:hover,
    #emailDraftArea::-webkit-scrollbar-thumb:hover,
    #whatsappDraftArea::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>