<?php
include "../../../../model/model.php";
/*======******Header******=======*/
require_once('../../../layouts/admin_header.php');
include_once('../inc/quotation_hints_modal.php');
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$q = "select * from branch_assign where link='package_booking/quotation/home/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
$financial_year_id = $_SESSION['financial_year_id'];
?>
<input type="hidden" id="branch_status" name="branch_status" value="<?= $branch_status ?>">
<?= begin_panel('Package Tour Quotation', 40) ?>
<div class="app_panel_content">
    <div class="row">
        <div class="col-md-12">
            <div id="div_id_proof_content"> </div>
            <div class="row mg_bt_20">
                <div class="col-xs-12 text-right">
                    <form action="save/index.php" method="POST">
                        <button class="btn btn-info btn-sm ico_left" id="quot_save"><i class="fa fa-plus"></i>&nbsp;&nbsp;Quotation</button>
                    </form>
                </div>
            </div>

            <div class="app_panel_content Filter-panel">
                <div class="row">
                    <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                        <select name="quotation_id" id="quotation_id" title="Select Quotation" style="width:100%">
                            <option value="">Select Quotation</option>
                            <?php
                            $query = "select * from package_tour_quotation_master where 1 and financial_year_id='$financial_year_id' and status='1' ";
                            if ($role == 'B2b' || $role == 'Sales' || $role == 'Backoffice') {
                                $query .= " and emp_id='$emp_id'";
                            }
                            if ($branch_status == 'yes' && $role != 'Admin') {
                                $query .= " and branch_admin_id = '$branch_admin_id'";
                            }
                            if ($branch_status == 'yes' && $role == 'Branch Admin') {
                                $query .= " and branch_admin_id='$branch_admin_id'";
                            }
                            $query .= " order by quotation_id desc";
                            $sq_quotation = mysqlQuery($query);
                            while ($row_quotation = mysqli_fetch_assoc($sq_quotation)) {

                                $quotation_date = $row_quotation['quotation_date'];
                                $yr = explode("-", $quotation_date);
                                $year = $yr[0];
                            ?>
                                <option value="<?= $row_quotation['quotation_id'] ?>">
                                    <?= get_quotation_id($row_quotation['quotation_id'], $year) ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                        <input type="text" id="from_date_filter" name="from_date_filter" placeholder="From Date" title="From Date" onchange="get_to_date(this.id,'to_date_filter');">
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                        <input type="text" id="to_date_filter" name="to_date_filter" placeholder="To Date" title="To Date" onchange="validate_validDate('from_date_filter' , 'to_date_filter');">
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                        <select name="booking_type_filter" id="booking_type_filter" title="Tour Type" onchange="get_tour_typewise_packages(this.id);">
                            <option value="">Tour Type</option>
                            <option value="Domestic">Domestic</option>
                            <option value="International">International</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                        <select name="tour_name" id="tour_name_filter" title="Package Name" style="width:100%">
                            <option value="">Package Name</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <select name="status" id="status" title="Status" style="width:100%">
                            <option value="">Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="row mg_tp_10">
                    <?php if ($role == 'Admin') { ?>
                        <div class="col-md-2 col-sm-6 col-xs-12 mg_bt_10_xs">
                            <select name="branch_id_filter" id="branch_id_filter1" title="Branch Name" style="width: 100%">
                                <?php get_branch_dropdown($role, $branch_admin_id, $branch_status) ?>
                            </select>
                        </div>
                    <?php } ?>
                    <div class="col-md-3 col-sm-6 mg_bt_10_xs">
                        <select name="financial_year_id_filter" id="financial_year_id_filter" title="Select Financial Year">
                            <?php
                            $sq_fina = mysqli_fetch_assoc(mysqlQuery("select * from financial_year where financial_year_id='$financial_year_id'"));
                            $financial_year = get_date_user($sq_fina['from_date']).'&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;&nbsp;'.get_date_user($sq_fina['to_date']);
                            ?>
                            <option value="<?= $sq_fina['financial_year_id'] ?>"><?= $financial_year  ?></option>
                            <?php echo get_financial_year_dropdown_filter($financial_year_id); ?>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12 mg_tp_10">
                        <button class="btn btn-sm btn-info ico_right" onclick="quotation_list_reflect()">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <div id="div_quotation_list_reflect" class="main_block loader_parent">
                <div class="row mg_tp_20">
                    <div class="col-md-12 no-pad">
                        <div class="table-responsive">
                            <table id="package_table" class="table table-hover" style="width:100% !important;margin: 20px 0 !important;">
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="div_quotation_form"></div>
            <div id="div_quotation_save"></div>
            <div id="backoffice_mail"></div>
            <div id="view_request"></div>
            
            
<!-- Modal Structure -->
<div class="modal fade" id="contentModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">HTML Content Preview</h5>
                 <button id="download-button" class="btn btn-success">Download as Word Document</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            <div class="modal-body">
                <div id="preview"></div>
            </div>
            <div class="modal-footer">
              
            </div>
        </div>
    </div>
</div>

<!-- Actions Modal -->
<div class="modal fade" id="actionsModal" tabindex="-1" role="dialog" aria-labelledby="actionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="actionsModalLabel">Quotation Actions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="quotation-info mb-3">
                            <h6>Quotation Details:</h6>
                            <p><strong>Quotation ID:</strong> <span id="modal_quotation_id"></span></p>
                            <p><strong>Customer:</strong> <span id="modal_customer_name"></span></p>
                            <p><strong>Package:</strong> <span id="modal_package_name"></span></p>
                            <p><strong>Amount:</strong> <span id="modal_amount"></span></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h6>Available Actions:</h6>
                        <div class="action-buttons-container">
                            <!-- PDF Actions -->
                            <div class="action-group mb-3">
                                <h6 class="text-primary">Download Options</h6>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm" id="modal_pdf_download" title="Download Quotation PDF">
                                        <i class="fa fa-print"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" id="modal_word_download" title="Download Quotation Word">
                                        <i class="fa fa-file-word-o"></i> Word
                                    </button>
                                </div>
                            </div>

                            <!-- Communication Actions -->
                            <div class="action-group mb-3">
                                <h6 class="text-primary">Communication</h6>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm" id="modal_email_customer" title="Email Quotation to Customer">
                                        <i class="fa fa-envelope-o"></i> Email Customer
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" id="modal_whatsapp" title="WhatsApp Quotation to Customer">
                                        <i class="fa fa-whatsapp"></i> WhatsApp
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" id="modal_email_backoffice" title="Email Quotation to Backoffice">
                                        <i class="fa fa-paper-plane-o"></i> Email Backoffice
                                    </button>
                                </div>
                            </div>

                            <!-- Management Actions -->
                            <div class="action-group mb-3">
                                <h6 class="text-primary">Management</h6>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm" id="modal_update" title="Update Details">
                                        <i class="fa fa-pencil-square-o"></i> Update
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" id="modal_clone" title="Create Copy of this Quotation">
                                        <i class="fa fa-files-o"></i> Clone
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" id="modal_view" title="View Details" target="_blank">
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                </div>
                            </div>

                            <!-- Hotel Actions -->
                            <div class="action-group mb-3">
                                <h6 class="text-primary">Hotel Management</h6>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm" id="modal_hotel_request" title="Send Hotel Availability Request">
                                        <i class="fa fa-paper-plane-o"></i> Hotel Request
                                    </button>
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
        </div>
    </div>
</div>
</div>
<?= end_panel() ?>
<script src="<?= BASE_URL ?>js/app/field_validation.js"></script>
<script>
    $('#quotation_id,#tour_name_filter').select2();
    $('#from_date_filter, #to_date_filter').datetimepicker({
        timepicker: false,
        format: 'd-m-Y'
    });
    $('[data-toggle="tooltip"]').tooltip();

    var column = [{
            title: "S_No."
        },
        {
            title: "QUOTATION_ID"
        },
        {
            title: "Tour_Name"
        },
        {
            title: "Customer"
        },
        {
            title: "Quotation_Date"
        },
        {
            title: "Amount"
        },
        {
            title: "Created_by"
        },
        {
            title: "Actions",
            className: "text-center action_width"
        }
    ];

    function view_request(quot_id) {
        $('#view_req' + quot_id).prop('disabled', true);
        $('#view_req' + quot_id).button('loading');
        $.post('hotel_availability/index.php', {
            quot_id: quot_id
        }, function(data) {
            $('#view_request').html(data);
            $('#view_req' + quot_id).button('reset');
            $('#view_req' + quot_id).prop('disabled', false);
        });
    }

function view_request(quot_id){
	$('#view_req'+quot_id).prop('disabled',true);
	$('#view_req'+quot_id).button('loading');
	$.post('hotel_availability/index.php', {quot_id : quot_id }, function(data){
		$('#view_request').html(data);
		$('#view_req'+quot_id).button('reset');
        $('#view_req'+quot_id).prop('disabled',false);
	});
}
function quotation_list_reflect() {
    $('#div_quotation_list_reflect').append('<div class="loader"></div>');
    var from_date = $('#from_date_filter').val();
    var to_date = $('#to_date_filter').val();
    var booking_type = $('#booking_type_filter').val();
    var package_id = $('#tour_name_filter').val();
    var quotation_id = $('#quotation_id').val();
    var branch_status = $('#branch_status').val();
    var branch_id = $('#branch_id_filter1').val();
    var status = $('#status').val();
    var financial_year_id_filter = $('#financial_year_id_filter').val();

    $.post('quotation_list_reflect.php', {
        from_date: from_date,
        to_date: to_date,
        booking_type: booking_type,
        package_id: package_id,
        quotation_id: quotation_id,
        branch_status: branch_status,
        branch_id: branch_id,
        status: status,
        financial_year_id:financial_year_id_filter
    }, function(data) {
        pagination_load(data, column, true, false, 20, 'package_table');
        $('.loader').remove();
    })
}
quotation_list_reflect();

    function quotation_clone(quotation_id) {
        var base_url = $('#base_url').val();
        $('#vi_confirm_box').vi_confirm_box({
            callback: function(data1) {
                if (data1 == "yes") {
                    $.ajax({
                        type: 'post',
                        url: base_url + 'controller/package_tour/quotation/quotation_clone.php',
                        data: {
                            quotation_id: quotation_id
                        },
                        success: function(result) {
                            msg_alert(result);
                            console.log(result);
                            quotation_list_reflect();
                        }
                    });
                }
            }
        });
    }

    function quotation_email_send(btn_id, quotation_id, email_id, mobile_no) {
        $('#' + btn_id).button('loading');
        var base_url = $('#base_url').val();
        
        // Add timestamp to prevent caching
        var timestamp = new Date().getTime();
        
        console.log('Loading specific quotation:', quotation_id, 'for email:', email_id);
        
        $.post('send_quotation.php', {
            quotation_id: quotation_id,  // Pass specific quotation_id
            email_id: email_id,
            mobile_no: mobile_no,
            _t: timestamp
        }, function(data) {
            console.log('Modal response for quotation:', quotation_id);
            $('#div_quotation_form').html(data);
            $('#' + btn_id).button('reset');
            // Show the modal after content is loaded
            $('#quotation_send_modal').modal('show');
        }).fail(function(xhr, status, error) {
            console.error('Error loading modal for quotation:', quotation_id, error);
            console.error('Response text:', xhr.responseText);
            $('#' + btn_id).button('reset');
            error_msg_alert('Error loading quotation modal. Please try again.');
        });
    }

    function get_tour_typewise_packages(tour_type) {

        var tour_type = $('#' + tour_type).val();
        $.post('get_tour_typewise_packages.php', {
            tour_type: tour_type
        }, function(data) {
            $('#tour_name_filter').html(data);
        });
    }
    get_tour_typewise_packages('booking_type_filter');

function quotation_email_send_backoffice_modal(quotation_id) {
    
	$('#email_backoffice_btn-'+quotation_id).prop('disabled',true);
	$('#email_backoffice_btn-'+quotation_id).button('loading');
    $.post('backoffice_mail.php', {
        quotation_id: quotation_id
    }, function(data) {
        $('#backoffice_mail').html(data);
        $('#email_backoffice_btn-'+quotation_id).prop('disabled',false);
        $('#email_backoffice_btn-'+quotation_id).button('reset');
    });
}

function save_modal() {
    var branch_status = $('#branch_status').val();
    $('#quot_save').button('loading');
    $.post('save/index.php', {
        branch_status: branch_status
    }, function(data) {
        $('#div_quotation_save').html(data);
        $('#quot_save').button('reset');
    });
}

// Modal action functions
function openActionsModal(quotationData) {
    // Populate modal with quotation data
    $('#modal_quotation_id').text(quotationData.quotation_id);
    $('#modal_customer_name').text(quotationData.customer_name);
    $('#modal_package_name').text(quotationData.package_name);
    $('#modal_amount').text(quotationData.amount);
    
    // Store quotation data for use in modal actions
    window.currentQuotationData = quotationData;
    
    // Show modal
    $('#actionsModal').modal('show');
}

// Modal action handlers
$(document).ready(function() {
    // PDF Download
    $('#modal_pdf_download').click(function() {
        if (window.currentQuotationData) {
            loadOtherPage(window.currentQuotationData.pdf_url);
        }
    });
    
    // Word Download
    $('#modal_word_download').click(function() {
        if (window.currentQuotationData) {
            exportHTML(window.currentQuotationData.word_url);
        }
    });
    
    // Email Customer
    $('#modal_email_customer').click(function() {
        if (window.currentQuotationData) {
            quotation_email_send('modal_email_customer', window.currentQuotationData.quotation_id, window.currentQuotationData.email_id, window.currentQuotationData.mobile_no);
        }
    });
    
    // WhatsApp
    $('#modal_whatsapp').click(function() {
        if (window.currentQuotationData) {
            quotation_whatsapp(window.currentQuotationData.quotation_id);
        }
    });
    
    // Email Backoffice
    $('#modal_email_backoffice').click(function() {
        if (window.currentQuotationData) {
            quotation_email_send_backoffice_modal(window.currentQuotationData.quotation_id);
        }
    });
    
    // Update
    $('#modal_update').click(function() {
        if (window.currentQuotationData) {
            // Create and submit the update form
            var form = $('<form>', {
                'method': 'POST',
                'action': 'update/index.php',
                'style': 'display: inline-block'
            });
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'quotation_id',
                'value': window.currentQuotationData.quotation_id
            }));
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'package_id',
                'value': window.currentQuotationData.package_id
            }));
            $('body').append(form);
            form.submit();
        }
    });
    
    // Clone
    $('#modal_clone').click(function() {
        if (window.currentQuotationData) {
            quotation_clone(window.currentQuotationData.quotation_id);
            $('#actionsModal').modal('hide');
        }
    });
    
    // View
    $('#modal_view').click(function() {
        if (window.currentQuotationData) {
            window.open('quotation_view.php?quotation_id=' + window.currentQuotationData.quotation_id, '_blank');
        }
    });
    
    // Hotel Request
    $('#modal_hotel_request').click(function() {
        if (window.currentQuotationData) {
            view_request(window.currentQuotationData.quotation_id);
            $('#actionsModal').modal('hide');
        }
    });
});

// Function to add actions button to each row
function addActionsButton(quotationData) {
    return '<button class="btn btn-primary btn-sm" onclick="openActionsModal(' + JSON.stringify(quotationData).replace(/"/g, '&quot;') + ')" title="View All Actions"><i class="fa fa-cogs"></i> Actions</button>';
}

// Smart dropdown positioning function for Actions modal and other dropdowns
function adjustDropdownPosition() {
    $('.btn-group, .dropdown').each(function() {
        var $btnGroup = $(this);
        var $dropdown = $btnGroup.find('.dropdown-menu');
        
        if ($dropdown.length === 0) return;
        
        // Reset classes
        $btnGroup.removeClass('dropup');
        
        // Get button position and viewport height
        var buttonOffset = $btnGroup.offset();
        var buttonHeight = $btnGroup.outerHeight();
        var dropdownHeight = $dropdown.outerHeight() || 200; // Estimate if not visible
        var viewportHeight = $(window).height();
        var scrollTop = $(window).scrollTop();
        
        // Calculate space below and above
        var spaceBelow = viewportHeight - (buttonOffset.top - scrollTop + buttonHeight);
        var spaceAbove = buttonOffset.top - scrollTop;
        
        // If not enough space below but enough space above, use dropup
        if (spaceBelow < dropdownHeight + 20 && spaceAbove > dropdownHeight + 20) {
            $btnGroup.addClass('dropup');
            console.log('QUOTATION INDEX: Dropdown positioned above for button at', buttonOffset.top);
        }
    });
}

// Apply smart positioning for all dropdowns
$(document).ready(function() {
    // Adjust positioning when dropdown is about to be shown
    $(document).on('show.bs.dropdown', '.btn-group, .dropdown', function() {
        var $this = $(this);
        setTimeout(function() {
            adjustDropdownPosition();
        }, 10);
    });
    
    // Also adjust on window resize and scroll
    $(window).on('resize scroll', function() {
        adjustDropdownPosition();
    });
    
    // Initial adjustment
    adjustDropdownPosition();
});
</script>
<style>
    .action_width {
        width: 250px;
        display: flex;
    }

    tr.warning {
        background-color: #fcf8e3;
    }

    /* Smart dropdown positioning */
    .btn-group .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        left: auto;
        z-index: 1000;
        transition: all 0.2s ease;
    }
    
    /* Dropup positioning - show above when near bottom */
    .btn-group.dropup .dropdown-menu {
        top: auto;
        bottom: 100%;
        margin: 0 0 2px;
        box-shadow: 0 -6px 12px rgba(0,0,0,.175);
    }
    
    /* Ensure dropdown arrow points in correct direction for dropup */
    .btn-group.dropup .dropdown-toggle::after {
        border-top: 0;
        border-bottom: 4px solid;
        border-right: 4px solid transparent;
        border-left: 4px solid transparent;
    }

    .table-hover>tbody>tr.warning:hover {
        background-color: #faf2cc;
    }
    
    /* Modal Styles */
    .quotation-info {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border-left: 4px solid #007bff;
    }
    
    .quotation-info p {
        margin-bottom: 5px;
    }
    
    .action-group {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        background-color: #ffffff;
    }
    
    .action-group h6 {
        margin-bottom: 10px;
        font-weight: 600;
    }
    
    .btn-group .btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }
    
    .action-buttons-container {
        max-height: 400px;
        overflow-y: auto;
    }
    
    #actionsModal .modal-body {
        max-height: 500px;
        overflow-y: auto;
    }
</style>
<?php
/*======******Footer******=======*/
require_once('../../../layouts/admin_footer.php');
?>