<?php
include "../../../../model/model.php";

$email_id = $_POST['email_id'];
$mobile_no = $_POST['mobile_no'];
$specific_quotation_id = isset($_POST['quotation_id']) ? $_POST['quotation_id'] : null;


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

// Build query based on whether specific quotation_id is provided
if ($specific_quotation_id) {
    // Show only the specific quotation and its sub-quotations
    // First, find the root parent quotation
    $root_parent_query = "SELECT quotation_id FROM package_tour_quotation_master WHERE quotation_id = '$specific_quotation_id' AND is_sub_quotation = '0'";
    $root_parent_result = mysqlQuery($root_parent_query);
    $root_parent = mysqli_fetch_assoc($root_parent_result);
    
    if ($root_parent) {
        // If it's a parent quotation, show it and all its sub-quotations
        $root_quotation_id = $root_parent['quotation_id'];
    } else {
        // If it's a sub-quotation, find its root parent
        $parent_query = "SELECT quotation_id FROM package_tour_quotation_master WHERE quotation_id = '$specific_quotation_id'";
        $parent_result = mysqlQuery($parent_query);
        $parent_row = mysqli_fetch_assoc($parent_result);
        
        if ($parent_row && $parent_row['parent_quotation_id']) {
            // Find the root parent by traversing up the chain
            $current_id = $parent_row['parent_quotation_id'];
            while (true) {
                $check_query = "SELECT quotation_id, parent_quotation_id FROM package_tour_quotation_master WHERE quotation_id = '$current_id'";
                $check_result = mysqlQuery($check_query);
                $check_row = mysqli_fetch_assoc($check_result);
                
                if (!$check_row || !$check_row['parent_quotation_id']) {
                    $root_quotation_id = $current_id;
                    break;
                }
                $current_id = $check_row['parent_quotation_id'];
            }
        } else {
            $root_quotation_id = $specific_quotation_id;
        }
    }
    
    $query = "select *, 
        COALESCE(is_sub_quotation, '0') as is_sub_quotation,
        COALESCE(parent_quotation_id, '0') as parent_quotation_id,
        COALESCE(quotation_id_display, '') as quotation_display_id
        from package_tour_quotation_master 
        where (quotation_id = '$root_quotation_id' OR parent_quotation_id = '$root_quotation_id') 
        and status='1'";
} else {
    // Show all quotations for the email (original behavior)
    $query = "select *, 
        COALESCE(is_sub_quotation, '0') as is_sub_quotation,
        COALESCE(parent_quotation_id, '0') as parent_quotation_id,
        COALESCE(quotation_id_display, '') as quotation_display_id
        from package_tour_quotation_master where email_id = '$email_id'  and status='1'";
}
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
    error_log("Modal Query Error: " . mysqli_error($conn));
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
$quotation_count = mysqli_num_rows($sq_query);

?>
<input type="hidden" id="whatsapp_switch" value="<?= $whatsapp_switch ?>">
<div class="modal fade" id="quotation_send_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><?= $modal_title ?></h4>
			</div>
			<div class="modal-body">
				<!-- Hidden field for base URL -->
				<input type="hidden" id="base_url" value="<?php echo BASE_URL ?>">
				
				<div class="row">
					<div class="col-xs-12">
						<?php if ($specific_quotation_id) { ?>
							<div class="alert alert-info">
								<i class="fa fa-info-circle"></i> Displaying quotation ID <?= $specific_quotation_id ?> and its sub-quotations (<?= $quotation_count ?> total)
							</div>
						<?php } ?>
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
									
									// Initialize variables
									$is_sub_quotation = false;
									$parent_quotation_id = null;
									
									// Check if the fields exist in the database result
									if (isset($row_tours['is_sub_quotation']) && $row_tours['is_sub_quotation'] == '1') {
										$is_sub_quotation = true;
										$parent_quotation_id = isset($row_tours['parent_quotation_id']) ? $row_tours['parent_quotation_id'] : null;
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
										// Sub-quotation styling - same as parent, perfectly aligned
										$quotation_id_display_formatted = '<span class="sub-quotation-id-display" style="font-weight: bold; color: #000; font-size: 1em;">' . $quotation_id_display . '</span>';
									} else {
										// Main quotation styling
										$quotation_id_display_formatted = '<span class="main-quotation-id-display" style="font-weight: bold; color: #000; font-size: 1em;">' . $quotation_id_display . '</span>';
									}
								?>
									<tr <?php echo $is_sub_quotation ? 'class="sub-quotation-row"' : ''; ?>>
										<td><input type="checkbox" value="<?php echo $row_tours['quotation_id']; ?>" id="<?php echo $row_tours['quotation_id']; ?>" name="custom_package" class="custom_package" /></td>
										<td><?php echo $count; ?></td>
										<td><?php echo $quotation_id_display_formatted; ?></td>
										<td><?= $quotation_cost_1 ?></td>
										<td><?php 
											// Show updated_at if available, otherwise show quotation_date
											$display_date = '';
											if (!empty($row_tours['updated_at']) && $row_tours['updated_at'] != '0000-00-00 00:00:00') {
												$display_date = get_date_user($row_tours['updated_at']);
											} else if (!empty($row_tours['quotation_date']) && $row_tours['quotation_date'] != '0000-00-00') {
												$display_date = get_date_user($row_tours['quotation_date']);
											} else {
												$display_date = 'N/A';
											}
											echo $display_date;
										?></td>
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
    
    <!-- Combined Download Button with Email2 -->
    <div class="btn-group download-btn-group">
        <button type="button" class="btn btn-info btn-sm dropdown-toggle download-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Download & Email Quotation">
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
            <div class="dropdown-divider"></div>
            <a class="dropdown-item download-option" href="javascript:void(0)" 
               onclick="openEmailWhatsappModal({
                   quotation_id: <?php echo $row_tours['quotation_id']; ?>,
                   email_id: '<?php echo $row_tours['email_id']; ?>',
                   mobile_no: '<?php echo $row_tours['mobile_no']; ?>',
                   package_name: '<?php echo addslashes($sq_tours_package['package_name']); ?>',
                   customer_name: '<?php echo addslashes($row_tours['customer_name']); ?>'
               })" 
               title="<?php echo $whatsapp_tooltip_change; ?>">
                <i class="fa fa-envelope-o email-icon"></i>
                <span class="option-text">
                    <strong>Email to Customer</strong>
                    <small>Send via Email & WhatsApp</small>
                </span>
            </a>
        </div>
    </div>

    <!-- Actions Button -->
    <div class="btn-group actions-btn-group">
        <button type="button" class="btn btn-success btn-sm dropdown-toggle actions-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Actions">
            <span class="btn-text">Actions</span>
        </button>
        <div class="dropdown-menu actions-dropdown">
            <a class="dropdown-item action-option" href="javascript:void(0)" 
            onclick="quotation_sub_copy(<?php echo $row_tours['quotation_id']; ?>)" 
            title="Create Sub-Quotation Copy">
                <i class="fa fa-files-o copy-icon"></i>
                <span class="option-text">
                    <strong>Copy</strong>
                    <small>Create a sub-quotation copy</small>
                </span>
            </a>
            <a class="dropdown-item action-option" href="javascript:void(0)" 
               onclick="editQuotationDirect(<?php echo $row_tours['quotation_id']; ?>, <?php echo $row_tours['package_id']; ?>)" 
               title="Edit Quotation">
                <i class="fa fa-pencil-square-o edit-icon"></i>
                <span class="option-text">
                    <strong>Edit</strong>
                    <small>Edit this quotation</small>
                </span>
            </a>
            <a class="dropdown-item action-option" href="quotation_view.php?quotation_id=<?php echo $row_tours['quotation_id']; ?>" 
               target="_BLANK" title="View Details">
                <i class="fa fa-eye view-icon"></i>
                <span class="option-text">
                    <strong>View</strong>
                    <small>View quotation details</small>
                </span>
            </a>
            <a class="dropdown-item action-option" href="javascript:void(0)" 
               onclick="view_request(<?php echo $row_tours['quotation_id']; ?>)" 
               title="<?php echo $title; ?>">
                <i class="fa fa-bed hotel-icon"></i>
                <span class="option-text">
                    <strong>Hotel Availability</strong>
                    <small>Check hotel availability</small>
                </span>
            </a>
            <a class="dropdown-item action-option" href="javascript:void(0)" 
               onclick="quotation_email_send_backoffice_modal(<?php echo $row_tours['quotation_id']; ?>);btnDisableEnable('email_backoffice_btn-<?php echo $row_tours['quotation_id']; ?>')" 
               title="Email Quotation to Backoffice">
                <i class="fa fa-paper-plane-o backoffice-icon"></i>
                <span class="option-text">
                    <strong>Email to Backoffice</strong>
                    <small>Send to internal team</small>
                </span>
            </a>
            <a class="dropdown-item action-option" href="javascript:void(0)" 
               onclick="convertQuotation(<?php echo $row_tours['quotation_id']; ?>)" 
               title="Convert Quotation">
                <i class="fa fa-exchange convert-icon"></i>
                <span class="option-text">
                    <strong>Convert</strong>
                    <small>Convert quotation to booking</small>
                </span>
            </a>
        </div>
    </div>

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
                <!-- Hidden fields to store modal parameters for refresh -->
                <input type="hidden" id="modal_email_id" value="<?= $email_id ?>">
                <input type="hidden" id="modal_mobile_no" value="<?= $mobile_no ?>">
                <input type="hidden" id="modal_quotation_id" value="<?= $specific_quotation_id ?>">
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
                                <!-- Content Options Card -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Select Content Options</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row email-options-row">
                                                    <div class="col-md-6">
                                                        <div class="form-check email-option-check">
                                                            <input class="form-check-input email-option" type="checkbox" id="emailPriceStructure" name="emailOptions[]" value="price_structure" checked>
                                                            <label class="form-check-label" for="emailPriceStructure">Price Structure</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check email-option-check">
                                                            <input class="form-check-input email-option" type="checkbox" id="emailInclusionExclusion" name="emailOptions[]" value="inclusion_exclusion" checked>
                                                            <label class="form-check-label" for="emailInclusionExclusion">Inclusion/Exclusion</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check email-option-check">
                                                            <input class="form-check-input email-option" type="checkbox" id="emailTermsConditions" name="emailOptions[]" value="terms_conditions" checked>
                                                            <label class="form-check-label" for="emailTermsConditions">Terms & Conditions</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check email-option-check">
                                                            <input class="form-check-input email-option" type="checkbox" id="emailItinerary" name="emailOptions[]" value="itinerary" checked>
                                                            <label class="form-check-label" for="emailItinerary">Itinerary</label>
                                                        </div>
                                                    </div>
                                                </div>
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

                                <!-- Email Draft - Hidden -->
                                <div class="form-group" style="display: none;">
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
                                <!-- Content Options Card for WhatsApp -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Select Content Options</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input whatsapp-option" type="checkbox" id="whatsappPriceStructure" name="whatsappOptions[]" value="price_structure" checked>
                                                            <label class="form-check-label" for="whatsappPriceStructure">Price Structure</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input whatsapp-option" type="checkbox" id="whatsappInclusionExclusion" name="whatsappOptions[]" value="inclusion_exclusion" checked>
                                                            <label class="form-check-label" for="whatsappInclusionExclusion">Inclusion/Exclusion</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input whatsapp-option" type="checkbox" id="whatsappTermsConditions" name="whatsappOptions[]" value="terms_conditions" checked>
                                                            <label class="form-check-label" for="whatsappTermsConditions">Terms & Conditions</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input whatsapp-option" type="checkbox" id="whatsappItinerary" name="whatsappOptions[]" value="itinerary" checked>
                                                            <label class="form-check-label" for="whatsappItinerary">Itinerary</label>
                                                        </div>
                                                    </div>
                                                </div>
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

                                <!-- WhatsApp Draft - Hidden -->
                                <div class="form-group" style="display: none;">
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

$(document).off('click', '.actions-btn-group .dropdown-toggle'); // prevent duplicate binding
$(document).on('click', '.actions-btn-group .dropdown-toggle', function (e) {
    e.preventDefault();
    e.stopPropagation();

    var $btn = $(this);
    var $menu = $btn.siblings('.dropdown-menu');

    // Close all open dropdowns first
    $('.actions-dropdown').hide().each(function () {
        var $parent = $(this).data('original-parent');
        if ($parent && $parent.length) {
            $parent.append($(this));
        }
    });

    // Save original parent before appending to body
    if (!$menu.data('original-parent')) {
        $menu.data('original-parent', $menu.parent());
    }

    // Calculate positions
    var windowHeight = $(window).height();
    var scrollTop = $(window).scrollTop();
    var btnOffset = $btn.offset();
    var btnHeight = $btn.outerHeight();
    var menuHeight = $menu.outerHeight();
    var spaceBelow = windowHeight - (btnOffset.top - scrollTop + btnHeight);
    var spaceAbove = btnOffset.top - scrollTop;

    // Default open downward
    var top = btnOffset.top + btnHeight;
    var left = btnOffset.left;

    // Open upward if less space below
    if (spaceBelow < menuHeight && spaceAbove > menuHeight) {
        top = btnOffset.top - menuHeight;
    }

    // Move dropdown to body
    $('body').append($menu);

    $menu.css({
        position: 'absolute',
        top: top,
        left: left,
        display: 'block',
        zIndex: 9999
    });
});

// Close dropdown on outside click
$(document).on('click', function () {
    $('.actions-dropdown:visible').each(function () {
        var $parent = $(this).data('original-parent');
        if ($parent && $parent.length) {
            $parent.append($(this));
        }
        $(this).hide();
    });
});



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
		

        return false;
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
			email_option: emailOption || 'Email Body',
            'options[]': selectedOptions
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

	// Function to refresh the quotation list
	function quotation_list_reflect() {
		// Reload the page to show updated quotation list
		location.reload();
	}
	
	// Function to refresh the modal content to show new sub-quotations
	function refreshModalContent() {
		var base_url = $('#base_url').val();
		var email_id = $('#modal_email_id').val();
		var mobile_no = $('#modal_mobile_no').val();
		var quotation_id = $('#modal_quotation_id').val();
		
		
		// Show loading indicator in the div_quotation_form
		$('#div_quotation_form').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Refreshing...</div>');
		
		// Add timestamp to prevent caching
		var timestamp = new Date().getTime();
		
		$.ajax({
			type: 'post',
			url: base_url + 'view/package_booking/quotation/home/send_quotation.php',
			data: {
				email_id: email_id,
				mobile_no: mobile_no,
				quotation_id: quotation_id,
				_t: timestamp
			},
			success: function(result) {
				$('#div_quotation_form').html(result);
				// Show the modal again after refresh
				$('#quotation_send_modal').modal('show');
			},
			error: function(xhr, status, error) {
				$('#div_quotation_form').html('<div class="alert alert-danger">Error refreshing content. Please try again.</div>');
			}
		});
	}

	// Function to create sub-quotation copy
	function quotation_sub_copy(quotation_id) {
		var base_url = $('#base_url').val();
		
		// Show confirmation dialog
		$('#vi_confirm_box').vi_confirm_box({
			callback: function(data1) {
				if (data1 == "yes") {
					// Show loading state
					msg_alert('Creating sub-quotation copy...');
					
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
									// Show success message
									msg_alert('Sub-quotation created successfully with ID: ' + response.quotation_id_display);
									
									// Wait a moment for the database to be updated, then refresh
									setTimeout(function() {
										refreshModalContent();
									}, 500);
								} else {
									error_msg_alert(response.message);
								}
							} catch (e) {
								// Fallback for non-JSON response
								msg_alert('Sub-quotation created successfully');
								// Wait a moment for the database to be updated, then refresh
								setTimeout(function() {
									refreshModalContent();
								}, 500);
							}
						},
						// error: function(xhr, status, error) {
						// 	console.error('Error creating sub-quotation:', error);
						// 	error_msg_alert('Failed to create sub-quotation. Please try again.');
						// }
					});
				}
			}
		});
	}

	// Function to edit quotation directly (without creating a copy)
	function editQuotationDirect(quotation_id, package_id) {
		var base_url = $('#base_url').val();
		
		// Close the modal first
		$('#quotation_send_modal').modal('hide');
		
		// Create and submit the update form directly
		var form = $('<form>', {
			'method': 'POST',
			'action': base_url + 'view/package_booking/quotation/home/update/index.php',
			'style': 'display: inline-block'
		});
		form.append($('<input>', {
			'type': 'hidden',
			'name': 'quotation_id',
			'value': quotation_id
		}));
		form.append($('<input>', {
			'type': 'hidden',
			'name': 'package_id',
			'value': package_id
		}));
		$('body').append(form);
		form.submit();
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

	// Function to convert quotation to booking
	function convertQuotation(quotation_id) {
		var base_url = $('#base_url').val();
		
		// Show confirmation dialog
		$('#vi_confirm_box').vi_confirm_box({
			callback: function(data1) {
				if (data1 == "yes") {
					// Show loading state
					msg_alert('Converting quotation to booking...');
					
					$.ajax({
						type: 'post',
						url: base_url + 'controller/package_tour/quotation/quotation_convert_to_booking.php',
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
							error_msg_alert('Error converting quotation. Please try again.');
						}
					});
				}
			}
		});
	}

	// Smart dropdown positioning function
	function adjustDropdownPosition() {
		$('.btn-group').each(function() {
			var $btnGroup = $(this);
			var $dropdown = $btnGroup.find('.dropdown-menu');
			
			if ($dropdown.length === 0) return;
			
			// Reset classes and positioning
			$btnGroup.removeClass('dropup');
			$dropdown.css('position', 'absolute');
			
			// Get button position and viewport height
			var buttonOffset = $btnGroup.offset();
			if (!buttonOffset) return;
			
			var buttonHeight = $btnGroup.outerHeight();
			var dropdownHeight = $dropdown.outerHeight() || 200; // Estimate if not visible
			var viewportHeight = $(window).height();
			var scrollTop = $(window).scrollTop();
			
			// Calculate space below and above
			var spaceBelow = viewportHeight - (buttonOffset.top - scrollTop + buttonHeight);
			var spaceAbove = buttonOffset.top - scrollTop;
			
			// Check if we're inside a scrollable container
			var $scrollContainer = $btnGroup.closest('.table-responsive, .modal-body');
			var isInScrollContainer = $scrollContainer.length > 0;
			
			// If not enough space below, position above or use fixed positioning
			if (spaceBelow < dropdownHeight + 20) {
				if (spaceAbove > dropdownHeight + 20) {
					$btnGroup.addClass('dropup');
					console.log('SEND MODAL: Dropdown positioned above for button at', buttonOffset.top);
				} else if (isInScrollContainer && window.innerHeight > 600) {
					// Use fixed positioning if in scroll container and viewport is large enough
					$dropdown.css({
						'position': 'fixed',
						'top': Math.max(10, buttonOffset.top - dropdownHeight - 5) + 'px',
						'left': (buttonOffset.left - $dropdown.outerWidth() + $btnGroup.outerWidth()) + 'px',
						'z-index': '9999'
					});
					console.log('SEND MODAL: Dropdown positioned with fixed positioning');
				}
			}
		});
	}

	// Apply smart positioning on page load and when dropdowns are opened
	$(document).ready(function() {
		// Adjust positioning when dropdown is about to be shown
		$(document).on('show.bs.dropdown', '.btn-group', function() {
			var $this = $(this);
			setTimeout(function() {
				adjustDropdownPosition();
			}, 10);
		});
		
		// Also adjust on window resize and scroll
		$(window).on('resize scroll', function() {
			adjustDropdownPosition();
		});
		
		// Adjust when modal is shown
		$('#quotation_send_modal').on('shown.bs.modal', function() {
			setTimeout(function() {
				adjustDropdownPosition();
			}, 100);
		});
		
		// Initial adjustment
		adjustDropdownPosition();
	});
</script>
<script src="<?php echo BASE_URL ?>view/package_booking/quotation/js/quotation.js"></script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>

<style>
    /* Action buttons styling in modal */
    #tbl_tour_list td:last-child {
        white-space: nowrap;
        position: relative;
    }
    
    #tbl_tour_list .btn {
        margin: 1px 2px 1px 0;
        padding: 4px 8px;
        font-size: 12px;
    }
    
    #tbl_tour_list .btn-group .btn {
        margin-right: 2px;
        margin-bottom: 2px;
    }
    
    /* Modal table responsive */
    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
        overflow-x: visible;
    }
    
    /* Ensure dropdowns don't cause horizontal scroll */
    .table-responsive {
        position: relative;
        overflow-x: visible;
        overflow-y: visible;
    }
    
    /* Ensure table container doesn't clip dropdowns */
    .table-responsive table {
        overflow: visible;
    }
    
    /* If modal content is too tall, add scroll only to table container */
    @media (max-height: 800px) {
        #quotation_send_modal .table-responsive {
            max-height: 400px;
            overflow-y: auto;
            overflow-x: visible;
        }
        
        /* But still keep dropdowns visible outside the scrollable area */
        #quotation_send_modal .table-responsive .btn-group .dropdown-menu {
            position: fixed !important;
            z-index: 9999 !important;
        }
    }
    
    /* Modal height adjustments - Remove scroll for better dropdown visibility */
    #quotation_send_modal .modal-dialog {
        max-height: none;
        height: auto;
        margin: 20px auto;
        max-width: 95%;
    }
    
    #quotation_send_modal .modal-content {
        height: auto;
        max-height: none;
        display: flex;
        flex-direction: column;
    }
    
    #quotation_send_modal .modal-body {
        flex: 1;
        overflow-y: visible;
        overflow-x: visible;
        padding: 20px;
        max-height: none;
    }
    
    /* Fix dropdown positioning for last column */
    #tbl_tour_list td:last-child .btn-group {
        position: static;
    }
    
    /* Ensure dropdowns are always visible above other content */
    .btn-group .dropdown-menu {
        z-index: 9999 !important;
        position: absolute !important;
    }
    
    /* Prevent modal backdrop from interfering with dropdowns */
    .modal-backdrop {
        z-index: 1040;
    }
    
    /* Ensure dropdown menus are above modal backdrop */
    .btn-group.open .dropdown-menu,
    .btn-group.show .dropdown-menu {
        z-index: 1050 !important;
    }
    
    /* Action buttons container */
    .action-buttons-container {
        display: flex;
        flex-wrap: wrap;
        gap: 2px;
        justify-content: flex-start;
    }
    
    /* Move buttons to the left */
    #tbl_tour_list td:last-child .btn-group:first-child {
        margin-left: 0;
    }
    
    #tbl_tour_list td:last-child .btn-group:last-child {
        margin-right: 0;
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
    
    /* Card border to match email preview */
    .card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        width: 100%;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 12px 16px;
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
        white-space: nowrap;
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
        position: absolute;
        right: 0;
        left: auto;
        z-index: 1050;
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

    /* Actions Button Styling */
    .actions-btn-group {
        position: relative;
    }
    
    .actions-btn {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(40,167,69,0.3);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }
    
    .actions-btn .btn-text {
        font-size: 12px;
        font-weight: 500;
    }
    
    .actions-btn:hover {
        background: linear-gradient(135deg, #1e7e34 0%, #155724 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40,167,69,0.4);
    }
    
    .actions-btn:focus {
        box-shadow: 0 0 0 3px rgba(40,167,69,0.25);
    }
    
    .actions-dropdown {
        min-width: 220px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border: none;
        border-radius: 8px;
        padding: 8px 0;
        margin-top: 4px;
        background: white;
        overflow: hidden;
        position: absolute;
        right: 0;
        left: auto;
        z-index: 1050;
    }
    
    .action-option {
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s ease;
        border: none;
        text-decoration: none;
        color: #495057;
    }
    
    .action-option:hover {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #28a745;
        transform: translateX(4px);
    }
    
    .action-option .copy-icon {
        color: #ffc107;
        font-size: 18px;
        width: 20px;
        text-align: center;
    }
    
    .action-option .edit-icon {
        color: #007bff;
        font-size: 18px;
        width: 20px;
        text-align: center;
    }
    
    .action-option .view-icon {
        color: #17a2b8;
        font-size: 18px;
        width: 20px;
        text-align: center;
    }
    
    .action-option .hotel-icon {
        color: #fd7e14;
        font-size: 18px;
        width: 20px;
        text-align: center;
    }
    
    .action-option .convert-icon {
        color: #6f42c1;
        font-size: 18px;
        width: 20px;
        text-align: center;
    }
    
    .action-option .backoffice-icon {
        color: #6c757d;
        font-size: 18px;
        width: 20px;
        text-align: center;
    }
    
    .actions-btn-group .dropdown-toggle::after {
        margin-left: 6px;
        border-top: 4px solid;
        border-right: 4px solid transparent;
        border-left: 4px solid transparent;
        vertical-align: middle;
    }

    /* Additional icon styling for download dropdown */
    .download-option .email-icon {
        color: #007bff;
        font-size: 18px;
        width: 20px;
        text-align: center;
    }
    
    .download-option .backoffice-icon {
        color: #6c757d;
        font-size: 18px;
        width: 20px;
        text-align: center;
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

    /* Card Styling for Content Options */
    .card-title {
        color: #009898;
        font-weight: 600;
        font-size: 14px;
    }
    
    .card-body {
        padding: 16px;
    }
    
    /* Card width and spacing */
    .col-md-6 .card {
        margin-bottom: 20px;
    }

    /* Email Options Checkbox Styling */
    .email-options-row {
        margin-bottom: 0;
    }

    .email-option-check {
        margin-bottom: 4px;
        padding: 2px 0;
    }

    .email-option-check .form-check-input {
        margin-right: 6px;
    }

    .email-option-check .form-check-label {
        font-size: 13px;
        margin-bottom: 0;
        font-weight: 500;
    }
    
    /* WhatsApp Options Styling */
    .whatsapp-option {
        margin-bottom: 4px;
        padding: 2px 0;
    }
    
    .whatsapp-option .form-check-input {
        margin-right: 6px;
    }
    
    .whatsapp-option .form-check-label {
        font-size: 13px;
        margin-bottom: 0;
        font-weight: 500;
    }
    
    /* Reduce spacing between form groups */
    .form-group {
        margin-bottom: 15px;
    }
    
    /* Compact row spacing */
    .row {
        margin-bottom: 0;
    }
    
    .col-md-3 {
        padding: 0 8px;
    }
    
    /* Card internal column spacing */
    .card-body .col-md-6 {
        padding: 0 8px;
        margin-bottom: 8px;
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
        padding: 8px 6px;
        text-align: left;
        vertical-align: middle;
        border: 1px solid #ddd;
    }
    
    #tbl_tour_list th {
        background-color: #f5f5f5;
        font-weight: bold;
    }
    
    .table-responsive {
        overflow-x: visible;
        margin: 0;
    }
    
    /* Ensure proper alignment for action buttons */
    #tbl_tour_list td:last-child {
        text-align: left;
        white-space: nowrap;
        padding: 8px 4px 8px 2px;
    }
    
    /* Fix button group alignment */
    .btn-group {
        display: inline-block;
        vertical-align: middle;
        margin: 0 2px 0 0;
    }
    
    /* Ensure dropdowns are visible and properly positioned */
    .btn-group .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        left: auto;
        z-index: 1000;
        display: none;
        float: left;
        min-width: 160px;
        padding: 5px 0;
        margin: 2px 0 0;
        font-size: 14px;
        text-align: left;
        list-style: none;
        background-color: #fff;
        border: 1px solid #ccc;
        border: 1px solid rgba(0,0,0,.15);
        border-radius: 4px;
        box-shadow: 0 6px 12px rgba(0,0,0,.175);
        transition: all 0.2s ease;
    }
    
    /* Smart positioning - show above when near bottom */
    .btn-group.dropup .dropdown-menu {
        top: auto;
        bottom: 100%;
        margin: 0 0 2px;
        box-shadow: 0 -6px 12px rgba(0,0,0,.175);
    }
    
    .btn-group.open .dropdown-menu {
        display: block;
    }
    
    /* Sub-quotation styling */
    .sub-quotation-row {
        background-color: #f8f9fa;
        border-left: 3px solid #007bff;
    }
    
    .sub-quotation-row:hover {
        background-color: #e9ecef;
    }
    
    .sub-quotation-id-display {
        font-weight: bold;
        color: #000;
        font-size: 1em;
    }
    
    .main-quotation-id-display {
        font-weight: bold;
        color: #000;
        font-size: 1em;
    }

    .actions-dropdown {
  display: none;
  min-width: 200px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
  border-radius: 8px;
  z-index: 9999 !important;
}
</style>