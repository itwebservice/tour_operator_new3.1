<style>
    
     
    .action-icon-btn {
        padding: 4px 8px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        background: #fff;
        color: #495057;
        font-size: 12px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        min-width: auto;
        white-space: nowrap;
    }
    
    .action-icon-btn:hover {
        border-color: #adb5bd;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .action-icon-btn:hover i {
        transform: scale(1.15);
        transition: all 0.2s ease;
        color: #ffffff !important;
    }
    
    /* Background becomes icon color, icon becomes white on hover */
    /* Background becomes icon color, icon becomes white on hover */
    .action-icon-btn:hover i {
        color: #ffffff !important;
    }
    
    .action-icon-btn[data-icon-type="copy"]:hover {
        background: #ffc107 !important;
    }
    
    .action-icon-btn[data-icon-type="edit"]:hover {
        background: #007bff !important;
    }
    
    .action-icon-btn[data-icon-type="view"]:hover {
        background: #17a2b8 !important;
    }
    
    .action-icon-btn[data-icon-type="hotel"]:hover {
        background: #fd7e14 !important;
    }
    
    .action-icon-btn[data-icon-type="hotel"]:hover {
        background: #fd7e14 !important;
    }
    
    .action-icon-btn[data-icon-type="backoffice"]:hover {
        background: #6c757d !important;
    }
    
    .action-icon-btn[data-icon-type="convert"]:hover {
        background: #6f42c1 !important;
    }
    
    .action-icon-btn[data-icon-type="pdf"]:hover {
        background: #dc3545 !important;
    }
    
    .action-icon-btn[data-icon-type="word"]:hover {
        background: #2b579a !important;
    }
    
    .action-icon-btn[data-icon-type="email"]:hover {
        background: #007bff !important;
    }
    
    .action-icon-btn[data-icon-type]:hover .action-label {
        color: #ffffff !important;
    }
    
    .action-icon-btn i {
        font-size: 14px;
    }
    
    .action-icon-btn .action-label {
        font-size: 11px;
        font-weight: 500;
    }
    
    /* Icon Colors */
    .action-icon-btn .copy-icon {
        color: #ffc107;
    }
    
    .action-icon-btn .edit-icon {
        color: #007bff;
    }
    
    .action-icon-btn .view-icon {
        color: #17a2b8;
    }
    
    .action-icon-btn .hotel-icon {
        color: #fd7e14;
    }
    
    .action-icon-btn .backoffice-icon {
        color: #6c757d;
    }
    
    .action-icon-btn .convert-icon {
        color: #6f42c1;
    }
    
    .action-icon-btn .pdf-icon {
        color: #dc3545;
    }
    
    .action-icon-btn .word-icon {
        color: #2b579a;
    }
    
    .action-icon-btn .email-icon {
        color: #007bff;
    }
    @media (max-width: 1600px) {
        .action-icon-btn .action-label {
            display: none;
        }
        .action-icon-btn {
            padding: 4px 6px;
            min-width: 28px;
        }
    }
    
    /* On very small screens, allow wrapping if needed */
    @media (max-width: 1200px) {
        .actions-buttons-container {
            flex-wrap: wrap;
            gap: 2px;
        }
        .action-icon-btn {
            padding: 3px 5px;
            min-width: 26px;
        }
    }
</style>


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
    } else if ($app_quot_format == 7) {
        $url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_7/fit_quotation_html.php?quotation_id=$quotation_id";
        $urldoc = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_7/fit_quotation_html.php?quotation_id=$quotation_id";
    } else if ($app_quot_format == 8) {
        $url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_8/fit_quotation_html.php?quotation_id=$quotation_id";
        $urldoc = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_8/fit_quotation_html.php?quotation_id=$quotation_id";
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
						<div class="table-responsive">
							<table class="table table-hover table-bordered no-marg" id="tbl_tour_list">
								<tr class="table-heading-row">
									<th></th>
									<th>Sr No.</th>
									<th>Quotation ID</th>
									<th>Quotation Cost</th>
									<th>Updated Date</th>
									<!-- <th>Actions</th> -->
                                    <th>Generate Quotation</th>
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
										<!-- <td> -->
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
    <!-- <div class="btn-group download-btn-group">
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
    </div> -->

    <!-- Actions Button -->
    <!-- <div class="btn-group actions-btn-group">
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
    </div> -->

<!-- </td> -->
<td>
    <div class="download-actions-container" style="display: inline-flex; gap: 6px; align-items: center;">
        <!-- PDF Button -->
        <button type="button" class="btn btn-sm action-icon-btn"  onclick="loadOtherPage('<?php echo $url1; ?>')"
                data-icon-type="pdf" 
                data-toggle="tooltip" data-placement="top" title="Download as PDF">
            <i class="fa fa-file-pdf-o pdf-icon"></i>
            <span class="action-label">PDF</span>
        </button>
        
        <!-- Word Button -->
        <button type="button" class="btn btn-sm action-icon-btn" onclick="exportHTML('<?php echo $urldoc; ?>')"
                data-icon-type="word"
                data-toggle="tooltip" data-placement="top" title="Download as Word">
            <i class="fa fa-file-word-o word-icon"></i>
            <span class="action-label">Word</span>
        </button>
        
        <!-- Email Button -->
        <button type="button" class="btn btn-sm action-icon-btn"  onclick="openEmailWhatsappModal({
                    quotation_id: <?php echo $row_tours['quotation_id']; ?>,
                    email_id: '<?php echo $row_tours['email_id']; ?>',
                    mobile_no: '<?php echo $row_tours['mobile_no']; ?>',
                    package_name: '<?php echo addslashes($sq_tours_package['package_name']); ?>',
                    customer_name: '<?php echo addslashes($row_tours['customer_name']); ?>',
                    clone_qtn_id: '<?php echo addslashes($quotation_id_display); ?>'
                })" 
                data-icon-type="email"
                data-toggle="tooltip" data-placement="top" title="Email to Customer">
            <i class="fa fa-envelope-o email-icon"></i>
            <span class="action-label">Email</span>
        </button>
    </div>
</td>

<td>
    <div class="actions-buttons-container">
        <button type="button" class="btn btn-sm action-icon-btn" 
                data-icon-type="copy"
                onclick="quotation_sub_copy(<?php echo $row_tours['quotation_id']; ?>)"
                data-toggle="tooltip" data-placement="top" title="Create Sub-Quotation Copy">
            <i class="fa fa-files-o copy-icon"></i>
            <span class="action-label">Copy</span>
        </button>
        <button type="button" class="btn btn-sm action-icon-btn" 
                data-icon-type="edit"
                onclick="editQuotationDirect(<?php echo $row_tours['quotation_id']; ?>, <?php echo $row_tours['package_id']; ?>)"
                data-toggle="tooltip" data-placement="top" title="Edit Quotation">
            <i class="fa fa-pencil-square-o edit-icon"></i>
            <span class="action-label">Edit</span>
        </button>
        <a href="quotation_view.php?quotation_id=<?php echo $row_tours['quotation_id']; ?>" 
           target="_BLANK" 
           class="btn btn-sm action-icon-btn" 
           data-icon-type="view"
           data-toggle="tooltip" data-placement="top" title="View Details" style="padding-top: 12px; padding-bottom: 12px;padding-left: 15px; padding-right: 15px;">
            <i class="fa fa-eye view-icon"></i>
            <span class="action-label">View</span>
        </a>  
        <button type="button" class="btn btn-sm action-icon-btn" 
            data-icon-type="hotel"
            onclick="view_request(<?php echo $row_tours['quotation_id']; ?>)"
            data-toggle="tooltip" data-placement="top" title="Hotel Availability">
            <i class="fa fa-bed hotel-icon"></i>
            <span class="action-label">Hotel</span>
        </button>
      
        <button type="button" class="btn btn-sm action-icon-btn" 
                data-icon-type="backoffice"
                onclick="quotation_email_send_backoffice_modal(<?php echo $row_tours['quotation_id']; ?>);btnDisableEnable('email_backoffice_btn-<?php echo $row_tours['quotation_id']; ?>')"
                data-toggle="tooltip" data-placement="top" title="Email Quotation to Backoffice">
                <i class="fa fa-paper-plane-o backoffice-icon"></i>
            <span class="action-label">Email</span>
        </button>
      
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
                <input type="hidden" id="modal_email_id" value="<?= $email_id ?>">
                <input type="hidden" id="modal_mobile_no" value="<?= $mobile_no ?>">
                <input type="hidden" id="modal_quotation_id" value="<?= $specific_quotation_id ?>">

                <div class="communication-toolbar d-flex align-items-center mb-3">
                    <ul class="nav nav-tabs customMailTabs" id="communicationTabs" role="tablist">
                        <li class="nav-item">
                            <div class="split-tab-group">
                                <a class="nav-link split-tab-label active" id="email-tab" data-toggle="tab" href="#email-content" role="tab" aria-controls="email-content" aria-selected="true">Email</a>
                                <button type="button" class="split-tab-arrow custom-dropdown-link" aria-label="Email options">
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="dropdown-list">
                                <div class="mail-tab-option"><label><input type="checkbox" class="email-option" name="emailOptions[]" value="price_structure" checked><span>Price Structure</span></label></div>
                                <div class="mail-tab-option"><label><input type="checkbox" class="email-option" name="emailOptions[]" value="inclusion_exclusion" checked><span>Inclusion/Exclusion</span></label></div>
                                <div class="mail-tab-option"><label><input type="checkbox" class="email-option" name="emailOptions[]" value="terms_conditions" checked><span>Terms & Conditions</span></label></div>
                                <div class="mail-tab-option"><label><input type="checkbox" class="email-option" name="emailOptions[]" value="itinerary" checked><span>Itinerary</span></label></div>
                            </div>
                        </li>

                        <li class="nav-item">
                            <div class="split-tab-group">
                                <a class="nav-link split-tab-label" id="whatsapp-tab" data-toggle="tab" href="#whatsapp-content" role="tab" aria-controls="whatsapp-content" aria-selected="false">WhatsApp</a>
                                <button type="button" class="split-tab-arrow custom-dropdown-link" aria-label="WhatsApp options">
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="dropdown-list">
                                <div class="mail-tab-option"><label><input type="checkbox" class="whatsapp-option" name="whatsappOptions[]" value="price_structure" checked><span>Price Structure</span></label></div>
                                <div class="mail-tab-option"><label><input type="checkbox" class="whatsapp-option" name="whatsappOptions[]" value="inclusion_exclusion" checked><span>Inclusion/Exclusion</span></label></div>
                                <div class="mail-tab-option"><label><input type="checkbox" class="whatsapp-option" name="whatsappOptions[]" value="terms_conditions" checked><span>Terms & Conditions</span></label></div>
                                <div class="mail-tab-option"><label><input type="checkbox" class="whatsapp-option" name="whatsappOptions[]" value="itinerary" checked><span>Itinerary</span></label></div>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="communicationTabContent">
                    <!-- Email Tab -->
                    <div class="tab-pane  show active" id="email-content" role="tabpanel" aria-labelledby="email-tab">
                        <div class="row mt-3">
                            <div class="col-md-12">
                               

                                <label class="font-weight-bold preview-label">Email Preview :</label>
                                <div class="form-group email-preview-wrapper">
                                    <button type="button" class="copyBtn" id="copyEmailBtn" title="Copy email">
                                        <i class="fa fa-clone"></i>
                                    </button>
                                    <div id="emailPreviewArea" >
                                        <div class="p-3 text-center text-muted">
                                            <i class="fa fa-envelope fa-2x mb-2"></i>
                                            <p>Email preview will appear here...</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="email-send-row">
                                    <button type="button" class="btn" style="background-color: #009898; color: #fff;" id="sendEmailBtn">
                                        <i class="fa fa-paper-plane"></i> Send Email
                                    </button>
                                </div>

                                <div class="form-group" style="display: none;">
                                    <div id="emailDraftArea"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Tab -->
                    <div class="tab-pane fade" id="whatsapp-content" role="tabpanel" aria-labelledby="whatsapp-tab">
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold preview-label">WhatsApp Preview:</label>
                                    <div class="whatsapp-preview-wrapper">
                                        <button type="button" class="copyBtn" id="copyWhatsappBtn" title="Copy WhatsApp">
                                            <i class="fa fa-clone"></i>
                                        </button>
                                        <div id="whatsappPreviewArea">
                                            <div class="p-3 text-center text-muted">
                                                <i class="fa fa-whatsapp fa-2x mb-2 text-success"></i>
                                                <p>WhatsApp preview will appear here...</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="whatsapp-send-row">
                                        <button type="button" class="btn btn-teal" id="sendWhatsappBtn">
                                            <i class="fa fa-paper-plane-o"></i> Send WhatsApp
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group" style="display: none;">
                                    <div id="whatsappDraftArea"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

		$('#email-tab').addClass('active').attr('aria-selected', 'true');
		$('#whatsapp-tab').removeClass('active').attr('aria-selected', 'false');
		$('#email-content').addClass('show active');
		$('#whatsapp-content').removeClass('show active');

		setTimeout(function() {
			loadEmailContent(quotationData.quotation_id);
		}, 300);
	}

	// Function to load email content
	function loadEmailContent(quotation_id) {
		// Show loading state
		$('#emailPreviewArea').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Loading email content...</div>');
		$('#emailDraftArea').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Loading email draft...</div>');

		var format = 'body';
		var emailOption = 'Email Body';
		var allOptions = ['price_structure', 'inclusion_exclusion', 'terms_conditions', 'itinerary'];

		$.post('get_email_body_content.php', {
			quotation_id: quotation_id,
			email_option: emailOption,
			options: allOptions,
			format: format,
			sectioned: '1'
		}, function(data) {
			if (data && data.trim() !== '') {
				$('#emailPreviewArea').html(data);
				togglePreviewSections('email');
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

		var allOptions = ['price_structure', 'inclusion_exclusion', 'terms_conditions', 'itinerary'];

		$.post('get_email_body_content.php', {
			quotation_id: quotation_id,
			email_option: 'WhatsApp',
			options: allOptions,
			sectioned: '1'
		}, function(data) {
			if (data && data.trim() !== '') {
				$('#whatsappPreviewArea').html(data);
				togglePreviewSections('whatsapp');
			} else {
				$('#whatsappPreviewArea').html('<div class="p-3"><h5>WhatsApp Content Preview</h5><p class="text-muted">WhatsApp content will be displayed here based on your selections.</p></div>');
				$('#whatsappDraftArea').html('<div class="p-3"><h5>WhatsApp Draft</h5><p class="text-muted">WhatsApp draft content will be displayed here.</p></div>');
			}
		}).fail(function() {
			$('#whatsappPreviewArea').html('<div class="p-3"><h5>WhatsApp Content Preview</h5><p class="text-muted">WhatsApp content will be displayed here based on your selections.</p></div>');
			$('#whatsappDraftArea').html('<div class="p-3"><h5>WhatsApp Draft</h5><p class="text-muted">WhatsApp draft content will be displayed here.</p></div>');
		});
	}

	// Toggle preview sections based on checkbox selections
	function togglePreviewSections(type) {
		var optionClass = type === 'email' ? '.email-option' : '.whatsapp-option';
		var previewArea = type === 'email' ? '#emailPreviewArea' : '#whatsappPreviewArea';
		var draftArea = type === 'email' ? '#emailDraftArea' : '#whatsappDraftArea';
		var selectedOptions = [];

		$(optionClass + ':checked').each(function() {
			selectedOptions.push($(this).val());
		});

		$(previewArea + ' .preview-section-block').each(function() {
			var sectionKey = $(this).data('section');
			if (sectionKey === 'header' || sectionKey === 'footer') {
				$(this).show();
				return;
			}
			if (selectedOptions.indexOf(sectionKey) !== -1) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});

		updateDraftFromPreview(previewArea, draftArea);
	}

	// Build draft text from visible preview sections
	function updateDraftFromPreview(previewArea, draftArea) {
		var draftText = '';
		$(previewArea + ' .preview-section-block:visible').each(function() {
			var sectionText = $(this).text();
			if (sectionText && sectionText.trim() !== '') {
				draftText += sectionText.trim() + '\n\n';
			}
		});

		if (draftText.trim() !== '') {
			var textDraft = '<div style="font-family: monospace; font-size: 12px; line-height: 1.6; background: #f8f9fa; padding: 15px; border: 1px solid #e9ecef; border-radius: 4px; white-space: pre-wrap;">';
			textDraft += draftText.trim();
			textDraft += '</div>';
			$(draftArea).html(textDraft);
		}

		var htmlDraft = buildVisiblePreviewHtml(previewArea);
		if (htmlDraft) {
			$(draftArea).data('htmlDraft', htmlDraft);
		}
	}

	function buildVisiblePreviewHtml(previewArea) {
		var $preview = $(previewArea);
		var $pdfStyle = $preview.find('.quotation-email-pdf-style').first();
		var innerHtml = '';

		if ($pdfStyle.length) {
			var $wrapper = $('<div class="quotation-email-pdf-style"></div>');
			$pdfStyle.children('link').each(function() {
				$wrapper.append($(this).clone());
			});
			$pdfStyle.find('.preview-section-block:visible').each(function() {
				$wrapper.append($(this).clone());
			});
			if ($wrapper.children('.preview-section-block').length === 0) {
				$wrapper.append($pdfStyle.children(':not(link)').clone());
			}
			innerHtml = $wrapper.prop('outerHTML');
		} else if ($preview.find('.preview-section-block').length) {
			var $wrapper = $('<div></div>');
			$preview.find('.preview-section-block:visible').each(function() {
				$wrapper.append($(this).clone());
			});
			innerHtml = $wrapper.html();
		} else {
			innerHtml = $preview.html();
		}

		if (!innerHtml) {
			return '';
		}
		var plainCheck = $('<div>').html(innerHtml).text().replace(/\s+/g, '');
		if (plainCheck === '') {
			return '';
		}

		return '<!DOCTYPE html><html><head><meta charset="utf-8">' +
			'<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">' +
			'</head><body style="margin:0;padding:12px;font-family:Inter,Arial,sans-serif;background:#f8f9fa;">' +
			innerHtml + '</body></html>';
	}

	function getVisiblePreviewPlainText(previewArea) {
		var text = '';
		$(previewArea).find('.preview-section-block:visible').each(function() {
			var sectionText = $(this).text();
			if (sectionText && sectionText.trim() !== '') {
				text += sectionText.trim() + '\n\n';
			}
		});
		if (!text.trim()) {
			text = $(previewArea).text();
		}
		return text.trim();
	}

	function copyRichHtmlToClipboard(html, plainText, successMessage) {
		successMessage = successMessage || 'Content copied to clipboard!';

		if (navigator.clipboard && window.ClipboardItem && window.isSecureContext) {
			var htmlBlob = new Blob([html], { type: 'text/html' });
			var textBlob = new Blob([plainText], { type: 'text/plain' });
			return navigator.clipboard.write([
				new ClipboardItem({
					'text/html': htmlBlob,
					'text/plain': textBlob
				})
			]).then(function() {
				msg_alert(successMessage);
			}).catch(function() {
				if (fallbackCopyHtmlToClipboard(html)) {
					msg_alert(successMessage);
				} else {
					fallbackCopyTextToClipboard(plainText);
				}
			});
		}

		if (fallbackCopyHtmlToClipboard(html)) {
			msg_alert(successMessage);
			return $.Deferred().resolve().promise();
		}
		fallbackCopyTextToClipboard(plainText);
		return $.Deferred().resolve().promise();
	}

	function fallbackCopyHtmlToClipboard(html) {
		var container = document.createElement('div');
		container.innerHTML = html;
		container.contentEditable = 'true';
		container.style.position = 'fixed';
		container.style.left = '-9999px';
		container.style.top = '0';
		document.body.appendChild(container);

		var range = document.createRange();
		range.selectNodeContents(container);
		var selection = window.getSelection();
		selection.removeAllRanges();
		selection.addRange(range);

		var copied = false;
		try {
			copied = document.execCommand('copy');
		} catch (e) {
			copied = false;
		}

		selection.removeAllRanges();
		document.body.removeChild(container);
		return copied;
	}

	// Function to update content based on checkbox selections (fallback for non-sectioned content)
	function updateContentPreview(type) {
		var previewArea = type === 'email' ? '#emailPreviewArea' : '#whatsappPreviewArea';
		if ($(previewArea + ' .preview-section-block').length) {
			togglePreviewSections(type);
			return;
		}

		var selectedOptions = [];
		var optionClass = type === 'email' ? '.email-option' : '.whatsapp-option';
		var draftArea = type === 'email' ? '#emailDraftArea' : '#whatsappDraftArea';
		
		$(optionClass + ':checked').each(function() {
			selectedOptions.push($(this).val());
		});
		
		if (window.currentQuotationData) {
			var emailOption = 'Email Body';
			var format = 'body';
			
			$.post('get_email_body_content.php', {
				quotation_id: window.currentQuotationData.quotation_id,
				email_option: type === 'email' ? emailOption : 'WhatsApp',
				options: selectedOptions,
				format: type === 'email' ? format : 'text'
			}, function(data) {
				if (data && data.trim() !== '') {
					var formattedContent = data.replace(/\n/g, '<br>');
					$(previewArea).html(formattedContent);
					
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

		// Copy Email button — copy styled HTML from visible preview sections
		$('#copyEmailBtn').click(function() {
			var htmlContent = buildVisiblePreviewHtml('#emailPreviewArea');
			var plainText = getVisiblePreviewPlainText('#emailPreviewArea');

			if (!htmlContent && !plainText) {
				htmlContent = $('#emailDraftArea').data('htmlDraft') || '';
				plainText = $('#emailDraftArea').text();
			}

			if (htmlContent && htmlContent.trim() !== '') {
				copyRichHtmlToClipboard(htmlContent, plainText, 'Email copied with formatting!');
			} else if (plainText && plainText.trim() !== '') {
				fallbackCopyTextToClipboard(plainText);
			} else {
				error_msg_alert('No email content to copy. Please wait for content to load.');
			}
		});

	// Send WhatsApp button
	$(document).on('click', '#sendWhatsappBtn', function() {
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

		function closeMailTabDropdowns() {
			$('#emailWhatsappModal .dropdown-list').removeClass('show');
			$('#emailWhatsappModal .customMailTabs .nav-item').removeClass('is-open');
		}

		function activateEmailTab() {
			$('#whatsapp-tab').removeClass('active').attr('aria-selected', 'false');
			$('#whatsapp-content').removeClass('show active');
			$('#email-tab').addClass('active').attr('aria-selected', 'true');
			$('#email-content').addClass('show active');
			if (window.currentQuotationData) {
				loadEmailContent(window.currentQuotationData.quotation_id);
			}
		}

		function activateWhatsappTab() {
			$('#email-tab').removeClass('active').attr('aria-selected', 'false');
			$('#email-content').removeClass('show active');
			$('#whatsapp-tab').addClass('active').attr('aria-selected', 'true');
			$('#whatsapp-content').addClass('show active');
			if (window.currentQuotationData) {
				loadWhatsappContent(window.currentQuotationData.quotation_id);
			}
		}

		$('#emailWhatsappModal').on('hidden.bs.modal', function() {
			closeMailTabDropdowns();
		});

		// Arrow click - switch tab and toggle dropdown
		$(document).off('click', '#emailWhatsappModal .custom-dropdown-link');
		$(document).on('click', '#emailWhatsappModal .custom-dropdown-link', function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $navItem = $(this).closest('.nav-item');
			var $dropdown = $navItem.find('.dropdown-list');
			var isOpen = $dropdown.hasClass('show');
			var isEmailTab = $navItem.find('#email-tab').length > 0;

			if (isEmailTab) {
				activateEmailTab();
			} else {
				activateWhatsappTab();
			}

			$('#emailWhatsappModal .dropdown-list').not($dropdown).removeClass('show');
			$('#emailWhatsappModal .customMailTabs .nav-item').not($navItem).removeClass('is-open');

			if (isOpen) {
				$dropdown.removeClass('show');
				$navItem.removeClass('is-open');
			} else {
				$dropdown.addClass('show');
				$navItem.addClass('is-open');
			}
		});

		$(document).off('click', '#emailWhatsappModal .dropdown-list');
		$(document).on('click', '#emailWhatsappModal .dropdown-list', function(e) {
			e.stopPropagation();
		});

		$(document).off('click.mailTabDropdown');
		$(document).on('click.mailTabDropdown', function(e) {
			if (!$(e.target).closest('#emailWhatsappModal .nav-item').length) {
				closeMailTabDropdowns();
			}
		});

		$(document).on('change', '.email-option', function(e) {
			e.stopPropagation();
			updateContentPreview('email');
		});

		$(document).on('change', '.whatsapp-option', function(e) {
			e.stopPropagation();
			updateContentPreview('whatsapp');
		});

		$('#email-tab').off('click').on('click', function(e) {
			e.preventDefault();
			closeMailTabDropdowns();
			activateEmailTab();
		});

		$('#whatsapp-tab').off('click').on('click', function(e) {
			e.preventDefault();
			closeMailTabDropdowns();
			activateWhatsappTab();
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
    #emailWhatsappModal .modal-dialog {
        max-width: 1400px;
        width: 95%;
        margin: 20px auto;
        overflow: visible;
    }

    #emailWhatsappModal .modal-xl {
        max-width: 1400px;
        width: 95%;
    }

    #emailWhatsappModal .modal-content {
        height: 90vh;
        display: flex;
        flex-direction: column;
        border: none;
        border-radius: 8px;
        overflow: visible;
    }

    #emailWhatsappModal .modal-body {
        flex: 1;
        overflow-y: auto;
        overflow-x: visible;
        padding: 20px;
        background: #fff;
    }

    #emailWhatsappModal .modal-body ul.nav.customMailTabs {
        display: flex !important;
        border-bottom: 0 !important;
        border-radius: 0 !important;
        justify-content: center;
    }

    #emailWhatsappModal .modal-body ul.nav.customMailTabs li a.split-tab-label {
        font-size: 15px !important;
        font-weight: 500 !important;
        transition: none !important;
    }

    #emailWhatsappModal .communication-toolbar {
        padding-bottom: 0;
        overflow: visible;
        position: relative;
        z-index: 50;
    }

    #emailWhatsappModal .customMailTabs {
        border-bottom: 1px solid #eaeaea;
        margin-bottom: 0;
        position: relative;
        z-index: 50;
        overflow: visible;
        display: flex;
        align-items: flex-end;
        gap: 10px;
        padding-bottom: 12px;
    }

    #emailWhatsappModal .customMailTabs .nav-item {
        position: relative;
        margin-right: 0;
        overflow: visible;
        list-style: none;
    }

    #emailWhatsappModal .customMailTabs .nav-item.is-open {
        z-index: 60;
    }

    #emailWhatsappModal .split-tab-group {
        display: inline-flex;
        align-items: stretch;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #d8d8d8;
        height: 40px;
        background: #fff;
    }

    #emailWhatsappModal .split-tab-label {
        padding: 0 22px;
        border: none !important;
        border-radius: 0 !important;
        margin: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fff !important;
        color: #444 !important;
        font-weight: 500;
        font-size: 15px;
        text-decoration: none !important;
        min-width: 90px;
        box-shadow: none !important;
    }

    #emailWhatsappModal .split-tab-label:before {
        display: none !important;
    }

    #emailWhatsappModal .split-tab-label.active {
        background: #e8f7f7 !important;
        color: #009898 !important;
    }

    #emailWhatsappModal .split-tab-label:focus {
        outline: none;
        box-shadow: none;
    }

    #emailWhatsappModal .split-tab-label.active + .split-tab-arrow,
    #emailWhatsappModal .nav-item.is-open .split-tab-label.active + .split-tab-arrow {
        background: #009898;
        color: #fff;
        border-left-color: #009898;
    }

    #emailWhatsappModal .split-tab-group:has(.split-tab-label.active) {
        border-color: #009898;
    }

    #emailWhatsappModal .split-tab-arrow {
        width: 38px;
        min-width: 38px;
        border: none;
        border-left: 1px solid #d8d8d8;
        background: #f3f3f3;
        color: #444;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        font-size: 12px;
        outline: none;
        transition: background 0.2s ease, color 0.2s ease;
    }

    #emailWhatsappModal .split-tab-arrow:hover {
        background: #e9ecef;
    }

    #emailWhatsappModal .split-tab-label.active + .split-tab-arrow:hover {
        background: #008080;
    }

    #emailWhatsappModal .split-tab-arrow i {
        pointer-events: none;
    }

    #emailWhatsappModal .customMailTabs .dropdown-list {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        min-width: 100%;
        width: max-content;
        min-width: 220px;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        padding: 8px 0;
        z-index: 2000;
    }

    #emailWhatsappModal .customMailTabs .dropdown-list.show {
        display: block !important;
    }

    #emailWhatsappModal .customMailTabs .mail-tab-option {
        padding: 10px 16px;
        background: transparent;
    }

    #emailWhatsappModal .customMailTabs .mail-tab-option:hover {
        background: #f8f9fa;
    }

    #emailWhatsappModal .customMailTabs .dropdown-list label {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        cursor: pointer;
        font-weight: 500;
        color: #333;
        font-size: 14px;
    }

    #emailWhatsappModal .customMailTabs .dropdown-list input[type="checkbox"] {
        accent-color: #009898;
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    #emailWhatsappModal .modal-header {
        background: #009898;
        color: white;
        border-bottom: none;
        padding: 15px 20px;
    }

    #emailWhatsappModal .modal-header .close {
        color: white;
        opacity: 0.9;
        text-shadow: none;
    }

    #emailWhatsappModal .modal-header .close:hover {
        opacity: 1;
    }

    #emailWhatsappModal .modal-title {
        font-weight: 600;
        font-size: 18px;
    }

    #emailWhatsappModal .email-format-toolbar {
        flex-wrap: wrap;
        gap: 24px;
        justify-content: center;
        margin: 30px 0 20px;
    }

    #emailWhatsappModal .Format-dropdown-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    #emailWhatsappModal .Format-items {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    #emailWhatsappModal .Format-items .format-item {
        padding: 0;
    }

    #emailWhatsappModal .Format-items label {
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0;
        cursor: pointer;
        font-weight: 500;
        color: #333;
    }

    #emailWhatsappModal .Format-items input[type="radio"] {
        accent-color: #009898;
        width: 16px;
        height: 16px;
    }

    #emailWhatsappModal .email-subject-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 320px;
    }

    #emailWhatsappModal .email-subject-input {
        flex: 1;
        min-width: 280px;
    }

    #emailWhatsappModal .email-subject-input .form-control {
        border-radius: 6px;
        border: 1px solid #ced4da;
        height: 42px;
    }

    #emailWhatsappModal .format-label,
    #emailWhatsappModal .subject-label,
    #emailWhatsappModal .preview-label {
        color: #000;
        margin-bottom: 0;
        white-space: nowrap;
    }

    #emailWhatsappModal .preview-label {
        display: block;
        margin-bottom: 10px;
    }

    #emailWhatsappModal .copyBtn {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        color: #009898;
        padding: 11px 15px;
        cursor: pointer;
        z-index: 5;
        color:#737373;
    }

    #emailWhatsappModal .email-preview-wrapper:focus-visible,
    #emailWhatsappModal #emailPreviewArea:focus-visible{
        outline:none;
    }


    #emailWhatsappModal .email-preview-wrapper,
    #emailWhatsappModal .whatsapp-preview-wrapper {
        position: relative;
    }

    #emailWhatsappModal .email-preview-wrapper .copyBtn,
    #emailWhatsappModal .whatsapp-preview-wrapper .copyBtn {
        position: absolute;
        right: 30px;
        top: 15px;
    }

    #emailWhatsappModal .copy-subject-btn {
        margin-bottom: 0;
    }

    #emailWhatsappModal #emailPreviewArea {
        border: 1px solid #D5D5D5;
        border-radius: 8px;
        min-height: 250px;
        max-height: 600px;
        overflow-y: auto;
        padding: 15px;
        box-shadow: 0 4px 12px rgba(0, 152, 152, 0.15);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: #333;
    }

    #emailWhatsappModal .quotation-email-styled-preview {
        background: #fff;
    }

    #emailWhatsappModal .quotation-email-pdf-style {
        background: #f8f9fa;
        padding: 4px;
        border-radius: 6px;
    }

    #emailWhatsappModal .quotation-email-pdf-style .preview-section-block {
        margin-bottom: 8px;
    }

    #emailWhatsappModal .quotation-email-pdf-style table {
        width: 100% !important;
        max-width: 100%;
    }

    #emailWhatsappModal .quotation-email-styled-preview .preview-section-block {
        margin-bottom: 4px;
    }

    #emailWhatsappModal .quotation-email-styled-preview table {
        width: 100% !important;
        max-width: 100%;
    }

    #emailWhatsappModal #whatsappPreviewArea {
        border: 1px solid #D5D5D5;
        border-radius: 8px;
        background-color: #fff;
        min-height: 250px;
        max-height: 600px;
        overflow-y: auto;
        padding: 15px;
        box-shadow: 0 4px 12px rgba(0, 152, 152, 0.15);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: #333;
    }

    #emailWhatsappModal #email-content,
    #emailWhatsappModal #whatsapp-content
     {
        padding-top: 20px;
    }

    #emailWhatsappModal .email-send-row,
    #emailWhatsappModal .whatsapp-send-row {
        display: flex;
        justify-content: flex-end;
        margin-top: 15px;
    }

    #emailWhatsappModal .email-send-row .btn {
        border: none;
        border-radius: 6px;
        padding: 10px 16px;
        font-weight: 500;
    }

    #emailWhatsappModal .email-send-row .btn:hover {
        background-color: #007777 !important;
        color: #fff;
    }

    #emailWhatsappModal .btn-teal {
        background-color: #009898;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 10px 16px;
        font-weight: 500;
    }

    #emailWhatsappModal .btn-teal:hover {
        background-color: #007777;
        color: #fff;
    }

    #emailWhatsappModal .form-group {
        margin-bottom: 0;
    }

    #emailWhatsappModal #emailPreviewArea::-webkit-scrollbar,
    #emailWhatsappModal #whatsappPreviewArea::-webkit-scrollbar {
        width: 8px;
    }

    #emailWhatsappModal #emailPreviewArea::-webkit-scrollbar-thumb,
    #emailWhatsappModal #whatsappPreviewArea::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    #emailWhatsappModal .fa-spinner {
        color: #009898;
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
    
    #emailDraftArea, #whatsappDraftArea {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        line-height: 1.4;
        background-color: #f8f9fa;
        color: #495057;
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