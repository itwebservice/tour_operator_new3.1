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

$query = "select *, 
	COALESCE(is_sub_quotation, '0') as is_sub_quotation,
	COALESCE(parent_quotation_id, '0') as parent_quotation_id,
	COALESCE(quotation_display_id, '') as quotation_display_id
	from package_tour_quotation_master where email_id = '$email_id'  and status='1'";
if ($role != 'Admin' && $role != 'Branch Admin') {
	$query .= " and emp_id='$emp_id'";
}
if ($branch_status == 'yes' && $role == 'Branch Admin') {
	$query .= " and branch_admin_id = '$branch_admin_id'";
}
if ($branch_admin_id != '' && $role == 'Branch Admin') {
	$query .= " and branch_admin_id = '$branch_admin_id'";
}
$query .= ' ORDER BY 
	CASE WHEN COALESCE(is_sub_quotation, "0") = "0" THEN quotation_id ELSE COALESCE(parent_quotation_id, quotation_id) END ASC,
	CASE WHEN COALESCE(is_sub_quotation, "0") = "1" THEN quotation_id ELSE 0 END ASC';
$sq_query = mysqlQuery($query);

// Debug: Log the query and check for errors
if (!$sq_query) {
    error_log("Modal Query Error: " . mysqli_error($GLOBALS['con']));
    error_log("Query: " . $query);
}

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

// Debug: Log the query and results
error_log("Modal query: " . $query);
$debug_count = 0;
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
								
								// Check if query was successful
								if ($sq_query) {
									while ($row_tours = mysqli_fetch_assoc($sq_query)) {
										$debug_count++;
										error_log("Quotation $debug_count: ID=" . $row_tours['quotation_id'] . ", Display ID=" . (isset($row_tours['quotation_display_id']) ? $row_tours['quotation_display_id'] : 'N/A') . ", Is Sub=" . (isset($row_tours['is_sub_quotation']) ? $row_tours['is_sub_quotation'] : 'N/A'));
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
									
									// Check if this is a sub-quotation (handle missing fields gracefully)
									$is_sub_quotation = false;
									$parent_quotation_id = null;
									
									// Check if the fields exist in the database result
									if (isset($row_tours['is_sub_quotation']) && $row_tours['is_sub_quotation'] == '1') {
										$is_sub_quotation = true;
										$parent_quotation_id = isset($row_tours['parent_quotation_id']) ? $row_tours['parent_quotation_id'] : null;
									}
									
									// Debug: Log sub-quotation detection
									if ($is_sub_quotation) {
										error_log("Sub-quotation detected: " . $row_tours['quotation_id'] . " -> " . $quotation_id_display);
									}
									
									// Get quotation display ID (prefer quotation_display_id if available)
									$quotation_id_display = '';
									$quotation_id_display_formatted = '';
									
									// Check if quotation_display_id exists and use it
									if (isset($row_tours['quotation_display_id']) && !empty($row_tours['quotation_display_id'])) {
										$quotation_id_display = $row_tours['quotation_display_id'];
									} else {
										// Fallback to generating from quotation_id
										$quotation_id_display = get_quotation_id($row_tours['quotation_id'], $year);
									}
									
									$quotation_id_display_formatted = $quotation_id_display;
									
									// Apply sub-quotation formatting if it's a sub-quotation
									if ($is_sub_quotation) {
										// Add indentation and styling for sub-quotations with enhanced visibility
										$quotation_id_display_formatted = '<span class="sub-quotation-id-display">' . $quotation_id_display . '</span>';
									} else {
										// Main quotation styling
										$quotation_id_display_formatted = '<span class="main-quotation-id-display">' . $quotation_id_display . '</span>';
									}
								?>
									<tr <?php echo $is_sub_quotation ? 'class="sub-quotation-row"' : ''; ?>>
										<td><input type="checkbox" value="<?php echo $row_tours['quotation_id']; ?>" id="<?php echo $row_tours['quotation_id']; ?>" name="custom_package" class="custom_package" /></td>
										<td><?php echo $count; ?></td>
										<td><?php echo $quotation_id_display_formatted; ?></td>
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
    
    <!-- Combined Download Button -->
    <div class="btn-group download-btn-group">
        <button type="button" class="btn btn-info btn-sm dropdown-toggle download-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Download Quotation">
            <i class="fa fa-download"></i>
        </button>
        <div class="dropdown-menu download-dropdown">
            <a class="dropdown-item download-option" href="javascript:void(0)" onclick="loadOtherPage('<?php echo $url1; ?>')">
                <i class="fa fa-file-pdf-o pdf-icon"></i>
                <span class="option-text">
                    <strong>Download as PDF</strong>
                    <small>Portable Document Format</small>
                </span>
            </a>
            <a class="dropdown-item download-option" href="javascript:void(0)" onclick="exportHTML('<?php echo $urldoc; ?>')">
                <i class="fa fa-file-word-o word-icon"></i>
                <span class="option-text">
                    <strong>Download as Word</strong>
                    <small>Microsoft Word Document</small>
                </span>
            </a>
        </div>
    </div>

    <!-- WhatsApp Quotation -->
    <!-- <button class="btn btn-info btn-sm" onclick="quotation_whatsapp(<?php echo $row_tours['quotation_id']; ?>)" title="What'sApp Quotation to customer" data-toggle="tooltip">
        <i class="fa fa-whatsapp"></i>
    </button> -->

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

// Edit button that creates a copy and opens edit screen
$update_btn = '
    <button data-toggle="tooltip" style="display:inline-block" class="btn btn-info btn-sm" onclick="editQuotationWithCopy(' . $row_tours['quotation_id'] . ')" title="Edit Quotation (Creates Copy)">
        <i class="fa fa-pencil-square-o"></i>
    </button>';

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
								} else {
									// Show error message if query failed
									echo '<tr><td colspan="6" class="text-center text-danger">Error loading quotations. Please try again.</td></tr>';
								}
								?>
							</table>
						</div>
					</div>
				</div>



				<div class="row " style="display: none;">
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
                                    <label class="font-weight-bold mb-3" >Select Content Options:</label>
                                    <div class="row email-options-row">
                                        <div class="col-md-3">
                                            <div class="form-check email-option-check">
                                                <input class="form-check-input email-option" type="checkbox" id="emailPriceStructure" name="emailOptions[]" value="price_structure" checked>
                                                <label class="form-check-label" for="emailPriceStructure">Price Structure</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check email-option-check">
                                                <input class="form-check-input email-option" type="checkbox" id="emailInclusionExclusion" name="emailOptions[]" value="inclusion_exclusion" checked>
                                                <label class="form-check-label" for="emailInclusionExclusion">Inclusion/Exclusion</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check email-option-check">
                                                <input class="form-check-input email-option" type="checkbox" id="emailTermsConditions" name="emailOptions[]" value="terms_conditions" checked>
                                                <label class="form-check-label" for="emailTermsConditions">Terms & Conditions</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check email-option-check">
                                                <input class="form-check-input email-option" type="checkbox" id="emailItinerary" name="emailOptions[]" value="itinerary" checked>
                                                <label class="form-check-label" for="emailItinerary">Itinerary</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="form-group">
                                    <div class="row align-items-end">
                                        <div class="col-md-12">
                                            <div class="email-action-buttons">
                                                <button type="button" class="btn" style="background-color: #009898;" id="sendEmailBtn">
                                                    <i class="fa fa-paper-plane"></i> Send Email
                                                </button>
                                                <button type="button" class="btn"  style="background-color: #009898;"  id="copyEmailBtn">
                                                    <i class="fa fa-copy"></i> Copy Email
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email Preview -->
                                <div class="form-group">
                                    <label class="font-weight-bold" style="color: #009898;">Email Preview:</label>
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
                                    <label class="font-weight-bold" style="color: #009898;">Email Draft (Email Body Format):</label>
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
                                    <button type="button" class="btn" style="background-color: #009898;" id="sendWhatsappBtn">Send WhatsApp</button>
                                    <button type="button" class="btn" style="background-color: #009898;" id="copyWhatsappBtn">Copy WhatsApp</button>
                                </div>

                                <!-- WhatsApp Preview -->
                                <div class="form-group">
                                    <label class="font-weight-bold" style="color: #009898;">WhatsApp Preview:</label>
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
                                    <label class="font-weight-bold" style="color: #009898;">WhatsApp Draft:</label>
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
                <button type="button" class="btn" style="background-color: #009898;" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
	// Modal will be shown by the calling JavaScript function
	$('#email_option').select2();
	
	// Simple table display without DataTables sorting

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
		
		// Debug: Check if checkboxes are available after modal is shown
		setTimeout(function() {
		}, 500);
		
		// Ensure proper tab state - Email tab active by default
        $('#email-tab').trigger('click').addClass('active').attr('aria-selected', 'true');

		$('#whatsapp-tab').removeClass('active').attr('aria-selected', 'false');
		
		// Debug: Check checkboxes when modal is fully shown
		$('#emailWhatsappModal').on('shown.bs.modal', function() {
		});
		$('#email-content').addClass('show active');
		$('#whatsapp-content').removeClass('show active');
		
		// Load email content after modal is shown
		setTimeout(function() {
			loadEmailContent(quotationData.quotation_id);
		}, 300);
		
		// Set default email format to Email Body (no HTML support)
		// $('#emailFormatSelect').val('body'); // Removed - only Email Body format supported
		
		// Add modal refresh on show event
		$('#quotation_send_modal').on('shown.bs.modal', function() {
			// Force reload the modal content
			if (window.currentQuotationData) {
				loadEmailContent(window.currentQuotationData.quotation_id);
			}
		});
		
		// Add event listeners for email option checkboxes
		$('.email-option').on('change', function() {
			updateContentPreview('email');
		});
		
		// Email format dropdown removed - only Email Body format supported
	}

	// Function to load email content
	function loadEmailContent(quotation_id) {
		// Show loading state
		$('#emailPreviewArea').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Loading email content...</div>');
		$('#emailDraftArea').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Loading email draft...</div>');

		// Always use Email Body format (HTML format removed)
		var format = 'body';
		var emailOption = 'Email Body';

		// Gather currently selected email options
		var selectedOptions = [];
		$('.email-option:checked').each(function() {
			selectedOptions.push($(this).val());
		});

		// Load email content (Email Body format only)
		$.post('get_email_body_content.php', {
			quotation_id: quotation_id,
			email_option: emailOption,
			options: selectedOptions,
			format: format
		}, function(data) {
			if (data && data.trim() !== '') {
					// For Email Body format, show text content
					var formattedContent = data.replace(/\n/g, '<br>');
					$('#emailPreviewArea').html(formattedContent);
					
					var textDraft = '<div style="font-family: monospace; font-size: 12px; line-height: 1.4; background: #f8f9fa; padding: 15px; border: 1px solid #e9ecef; border-radius: 4px; white-space: pre-wrap;">';
					textDraft += data;
					textDraft += '</div>';
					$('#emailDraftArea').html(textDraft);
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
				
				// Format the WhatsApp draft with proper line breaks and styling
				var draftContent = '<div style="font-family: monospace; font-size: 12px; line-height: 1.6; background: #f8f9fa; padding: 15px; border: 1px solid #e9ecef; border-radius: 4px; white-space: pre-wrap;">';
				draftContent += data;
				draftContent += '</div>';
				$('#whatsappDraftArea').html(draftContent);
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
			// Always use Email Body format (HTML format removed)
			var emailOption = 'Email Body';
			var format = 'body';
			
			$.post('get_email_body_content.php', {
				quotation_id: window.currentQuotationData.quotation_id,
				email_option: type === 'email' ? emailOption : 'WhatsApp',
				options: selectedOptions,
				format: type === 'email' ? format : 'text'
			}, function(data) {
				if (data && data.trim() !== '') {
						// For Email Body format or WhatsApp, show text content
						var formattedContent = data.replace(/\n/g, '<br>');
						$(previewArea).html(formattedContent);
						
					// Format draft with proper line breaks and styling
					var textDraft = '<div style="font-family: monospace; font-size: 12px; line-height: 1.6; background: #f8f9fa; padding: 15px; border: 1px solid #e9ecef; border-radius: 4px; white-space: pre-wrap;">';
						textDraft += data;
						textDraft += '</div>';
						$(draftArea).html(textDraft);
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

	// Function removed - only Email Body format supported now

	// Event handlers for the new modal
	$(document).ready(function() {
	// Send Email button - use event delegation
	$(document).on('click', '#sendEmailBtn', function() {
		var selectedOptions = [];
		$('input[name="emailOptions[]"]:checked').each(function() {
			selectedOptions.push($(this).val());
		});
		
		// Always use Email Body format (HTML format removed)
		var emailFormat = 'body';
		var emailOption = 'Email Body';
		
		
		// Call individual email send function
		if (window.currentQuotationData) {
			sendIndividualQuotationEmail(window.currentQuotationData.quotation_id, 
				window.currentQuotationData.email_id, selectedOptions, emailOption);
		}
	});

		// Copy Email button
		$('#copyEmailBtn').click(function() {
			var emailContent = $('#emailDraftArea').text();
			if (emailContent && emailContent.trim() !== '') {
				// Try modern clipboard API first
				if (navigator.clipboard && window.isSecureContext) {
					navigator.clipboard.writeText(emailContent).then(function() {
						msg_alert('Email content copied to clipboard!');
					}).catch(function(err) {
						// Fallback for older browsers
						fallbackCopyTextToClipboard(emailContent);
					});
				} else {
					// Fallback for older browsers
					fallbackCopyTextToClipboard(emailContent);
				}
			} else {
				error_msg_alert('No email content to copy. Please wait for content to load.');
			}
		});

	// Send WhatsApp button
	$('#sendWhatsappBtn').click(function() {
		var selectedOptions = [];
		$('input[name="whatsappOptions[]"]:checked').each(function() {
			selectedOptions.push($(this).val());
		});
		
		
		if (window.currentQuotationData) {
			sendIndividualQuotationWhatsApp(window.currentQuotationData.quotation_id, 
				window.currentQuotationData.mobile_no, selectedOptions);
		}
	});

		// Copy WhatsApp button
		$('#copyWhatsappBtn').click(function() {
			var whatsappContent = $('#whatsappDraftArea').text();
			if (whatsappContent && whatsappContent.trim() !== '') {
				// Try modern clipboard API first
				if (navigator.clipboard && window.isSecureContext) {
					navigator.clipboard.writeText(whatsappContent).then(function() {
						msg_alert('WhatsApp content copied to clipboard!');
					}).catch(function(err) {
						// Fallback for older browsers
						fallbackCopyTextToClipboard(whatsappContent);
					});
				} else {
					// Fallback for older browsers
					fallbackCopyTextToClipboard(whatsappContent);
				}
			} else {
				error_msg_alert('No WhatsApp content to copy. Please wait for content to load.');
			}
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
	function sendIndividualQuotationEmail(quotation_id, email_id, selectedOptions, emailOption) {
		var base_url = $('#base_url').val();
		if (!base_url) {
			base_url = window.location.origin + '/itoursdemo/';
		}
		$('#sendEmailBtn').button('loading');
		
		// Prepare form data
		var formData = {
			quotation_id: quotation_id,
			email_id: email_id,
			email_option: emailOption || 'Email Body'
		};
		
		$.ajax({
			type: 'post',
			url: base_url + 'controller/package_tour/quotation/quotation_email_send_individual.php',
			data: formData,
			processData: true,
			contentType: 'application/x-www-form-urlencoded',
			success: function(message) {
				msg_alert(message);
				$('#sendEmailBtn').button('reset');
				$('#emailWhatsappModal').modal('hide');
			},
			error: function(xhr, status, error) {
				error_msg_alert('Error sending email. Please try again.');
				$('#sendEmailBtn').button('reset');
			}
		});
	}

	// Function to send individual quotation WhatsApp
	function sendIndividualQuotationWhatsApp(quotation_id, mobile_no, selectedOptions) {
		var base_url = $('#base_url').val();
		$('#sendWhatsappBtn').button('loading');
		
		
		$.ajax({
			type: 'post',
			url: base_url + 'controller/package_tour/quotation/quotation_whatsapp_individual.php',
			data: {
				quotation_id: quotation_id,
				mobile_no: mobile_no,
				options: selectedOptions
			},
			success: function(link) {
				$('#sendWhatsappBtn').button('reset');
				window.open(link, '_blank');
				$('#emailWhatsappModal').modal('hide');
			},
			error: function() {
				error_msg_alert('Error sending WhatsApp. Please try again.');
				$('#sendWhatsappBtn').button('reset');
			}
		});
	}

	// Fallback function for copying text to clipboard (for older browsers)
	function fallbackCopyTextToClipboard(text) {
		var textArea = document.createElement("textarea");
		textArea.value = text;
		
		// Avoid scrolling to bottom
		textArea.style.top = "0";
		textArea.style.left = "0";
		textArea.style.position = "fixed";
		textArea.style.opacity = "0";
		
		document.body.appendChild(textArea);
		textArea.focus();
		textArea.select();
		
		try {
			var successful = document.execCommand('copy');
			if (successful) {
				msg_alert('Content copied to clipboard!');
			} else {
				error_msg_alert('Unable to copy content. Please try manually selecting and copying the text.');
			}
		} catch (err) {
			error_msg_alert('Unable to copy content. Please try manually selecting and copying the text.');
		}
		
		document.body.removeChild(textArea);
	}

	// Function to create sub-quotation with version numbering
	function createSubQuotation(quotation_id) {
		var base_url = $('#base_url').val();
		
		// Show confirmation dialog
		$('#vi_confirm_box').vi_confirm_box({
			callback: function(data1) {
				if (data1 == "yes") {
					// Show loading state
					msg_alert('Creating sub-quotation...');
					
					$.ajax({
						type: 'post',
						url: base_url + 'controller/package_tour/quotation/quotation_sub_create.php',
						data: {
							quotation_id: quotation_id
						},
						success: function(result) {
							try {
								var response = JSON.parse(result);
								if (response.status === 'success') {
									msg_alert(response.message);
									// Refresh the quotation list
									quotation_list_reflect();
									// Close the modal
									$('#quotation_send_modal').modal('hide');
								} else {
									error_msg_alert(response.message);
								}
							} catch (e) {
								// Fallback for non-JSON response
								msg_alert(result);
								quotation_list_reflect();
								$('#quotation_send_modal').modal('hide');
							}
						},
						error: function() {
							error_msg_alert('Error creating sub-quotation. Please try again.');
						}
					});
				}
			}
		});
	}

	// Function to edit quotation by creating a copy first
	function editQuotationWithCopy(quotation_id) {
		var base_url = $('#base_url').val();
		
		// Show confirmation dialog
		$('#vi_confirm_box').vi_confirm_box({
			callback: function(data1) {
				if (data1 == "yes") {
					// Show loading state
					msg_alert('Creating copy for editing...');
					
					$.ajax({
						type: 'post',
						url: base_url + 'controller/package_tour/quotation/quotation_sub_create.php',
						data: {
							quotation_id: quotation_id
						},
						success: function(result) {
							try {
								var response = JSON.parse(result);
								if (response.status === 'success') {
									// Close the modal first
									$('#quotation_send_modal').modal('hide');
									
									// Create and submit the update form with the new quotation ID
									var form = $('<form>', {
										'method': 'POST',
										'action': base_url + 'view/package_booking/quotation/home/update/index.php',
										'style': 'display: inline-block'
									});
									form.append($('<input>', {
										'type': 'hidden',
										'name': 'quotation_id',
										'value': response.quotation_id
									}));
									form.append($('<input>', {
										'type': 'hidden',
										'name': 'package_id',
										'value': window.currentQuotationData ? window.currentQuotationData.package_id : ''
									}));
									$('body').append(form);
									form.submit();
								} else {
									error_msg_alert(response.message);
								}
							} catch (e) {
								// Fallback for non-JSON response - try to extract ID from text
								var new_quotation_id = extractQuotationIdFromResult(result);
								if (new_quotation_id) {
									$('#quotation_send_modal').modal('hide');
									var form = $('<form>', {
										'method': 'POST',
										'action': base_url + 'view/package_booking/quotation/home/update/index.php',
										'style': 'display: inline-block'
									});
									form.append($('<input>', {
										'type': 'hidden',
										'name': 'quotation_id',
										'value': new_quotation_id
									}));
									form.append($('<input>', {
										'type': 'hidden',
										'name': 'package_id',
										'value': window.currentQuotationData ? window.currentQuotationData.package_id : ''
									}));
									$('body').append(form);
									form.submit();
								} else {
									error_msg_alert('Error: Could not extract new quotation ID. Please try again.');
								}
							}
						},
						error: function() {
							error_msg_alert('Error creating quotation copy. Please try again.');
						}
					});
				}
			}
		});
	}

	// Helper function to extract quotation ID from the result message
	function extractQuotationIdFromResult(result) {
		// Try to extract quotation ID from the result message
		// Look for patterns like "QTN/2025/12.1" or just the numeric ID
		var match = result.match(/ID:\s*([A-Z0-9\/\.]+)/i);
		if (match && match[1]) {
			return match[1];
		}
		
		// If that doesn't work, try to get the latest quotation ID from the database
		// This is a fallback method
		return null;
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
        background-color: #009898;
        color: white;
        border-color: #009898;
    }
    
    #emailWhatsappModal .nav-tabs .nav-link:hover {
        border-color: #009898;
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
        background: linear-gradient(135deg, #009898 0%, #009898 100%);
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
    
    /* Download dropdown styling */
    .download-btn-group {
        position: relative;
    }
    
    .download-btn {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(0,123,255,0.3);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .download-btn:hover {
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,123,255,0.4);
    }
    
    .download-btn:focus {
        box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
    }
    
    .download-btn .btn-text {
        font-size: 12px;
        font-weight: 500;
    }
    
    .download-dropdown {
        min-width: 200px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border: none;
        border-radius: 8px;
        padding: 8px 0;
        margin-top: 4px;
        background: white;
        overflow: hidden;
    }
    
    .download-option {
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s ease;
        border: none;
        text-decoration: none;
        color: #495057;
    }
    
    .download-option:hover {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #007bff;
        transform: translateX(4px);
    }
    
    .download-option .pdf-icon {
        color: #dc3545;
        font-size: 18px;
        width: 20px;
        text-align: center;
    }
    
    .download-option .word-icon {
        color: #2b579a;
        font-size: 18px;
        width: 20px;
        text-align: center;
    }
    
    .option-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .option-text strong {
        font-size: 13px;
        font-weight: 600;
        color: inherit;
        margin: 0;
    }
    
    .option-text small {
        font-size: 11px;
        color: #6c757d;
        margin: 0;
        line-height: 1.2;
    }
    
    .download-btn-group .dropdown-toggle::after {
        margin-left: 6px;
        border-top: 4px solid;
        border-right: 4px solid transparent;
        border-left: 4px solid transparent;
        vertical-align: middle;
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
        background: linear-gradient(135deg, #009898 0%, #009898 100%);
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
        color: #009898;
    }
    
    /* Email Format Dropdown removed - only Email Body format supported */

    /* Email Action Buttons Styling */
    .email-action-buttons {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .email-action-buttons .btn {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 8px 16px;
    }

    .email-action-buttons .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    /* Email Options Checkbox Styling */
    .email-options-row {
        margin-bottom: 0;
    }

    .email-option-check {
        margin-bottom: 8px;
        padding: 4px 0;
    }

    .email-option-check .form-check-input {
        margin-right: 6px;
    }

    .email-option-check .form-check-label {
        font-size: 14px;
        margin-bottom: 0;
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
    
    /* Simple Sub-quotation ID Display Styling */
    .sub-quotation-id-display {
        /* margin-left: 25px; */
        color: #000;
        /* font-style: italic; */
        font-size: 1.1em;
        font-weight: bold;
    }
    
    .main-quotation-id-display {
        font-weight: bold;
        font-size: 1.1em;
    }
    
    /* Simple table row styling for sub-quotations */
    .sub-quotation-row {
        background-color: #f8f9fa;
        border-left: 3px solid #007bff;
    }
    
    /* Fix table alignment and margins */
    #tbl_tour_list {
        margin: 0;
        border-collapse: collapse;
        width: 100%;
    }
    
    #tbl_tour_list th,
    #tbl_tour_list td {
        padding: 8px 12px;
        text-align: left;
        vertical-align: middle;
        border: 1px solid #ddd;
    }
    
    #tbl_tour_list th {
        background-color: #f5f5f5;
        font-weight: bold;
    }
    
    .table-responsive {
        overflow-x: auto;
        margin: 0;
    }
    
    /* Ensure proper alignment for action buttons */
    #tbl_tour_list td:last-child {
        text-align: center;
        white-space: nowrap;
    }
    
    /* Fix button group alignment */
    .btn-group {
        display: inline-block;
        vertical-align: middle;
    }
</style>