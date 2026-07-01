<?php
include "../../../model/model.php";
?>
<form id="frm_branch_save">
    <div class="modal fade" id="branches_save_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document" style="width: 95% !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">New Branch</h4>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-sm-4 mg_bt_10">
                            <select name="locations_id" id="locations_id" style="width:100%">
                                <option value="">Location</option>
                                <?php
                                $sq_location = mysqlQuery("select * from locations where active_flag='Active'");
                                while ($row_location = mysqli_fetch_assoc($sq_location)) {
                                ?>
                                                <option value="<?= $row_location['location_id'] ?>">
                                                    <?= $row_location['location_name'] ?></option>
                                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-8 mg_bt_10">
                            <input type="text" id="branch_name" name="branch_name" placeholder="*Company Branch Name"
                                title="Company Branch Name">
                        </div>

                        <div class="col-sm-4 mg_bt_10 hidden">
                            <textarea name="branch_address" id="branch_address" onchange="validate_address(this.id);"
                                placeholder="*Branch Address" title="Branch Address" rows="1"></textarea>
                        </div>
                        <div class="col-sm-4 mg_bt_10">
                            <input type="text" id="contact_no" name="contact_no" onchange="mobile_validate(this.id);"
                                placeholder="*Contact No" title="Contact No">
                        </div>
                        <div class="col-sm-4 mg_bt_10">
                            <input type="text" id="email_id" name="email_id" placeholder="Email ID"
                                onchange="validate_email(this.id);" title="Email ID">
                        </div>
                        <div class="col-sm-4 mg_bt_10">
                            <input type="text" id="landline_no" name="landline_no" placeholder="Landline No"
                                onchange="mobile_validate(this.id);" title="Landline No">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mg_bt_10">
                            <textarea name="address1" id="address1" onchange="validate_address(this.id);"
                                placeholder="*Address1" title="Address1" rows="1"></textarea>
                        </div>
                        <div class="col-sm-4 mg_bt_10">
                            <textarea name="address2" id="address2" onchange="validate_address(this.id);"
                                placeholder="Address2" title="Address2" rows="1"></textarea>
                        </div>
                        <div class="col-sm-4 mg_bt_10">
                            <input type="text" id="city" name="city" onchange="validate_city(this.id);"
                                placeholder="*City" title="City">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mg_bt_10">
                            <input type="text" id="pincode" name="pincode" onchange="validate_PINCode(this.id);"
                                placeholder="Pincode" title="Pincode">
                        </div>
                        <div class="col-sm-4 mg_bt_10">
                            <select name="state" id="state" title="State/Country Name" class='form-control'
                                style='width:100%' required>
                                <?php get_states_dropdown() ?>
                            </select>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-sm-4 mg_bt_10">
                            <label for="qr_upload">Upload QR Code:</label>
                            <div class="div-upload">
                                <div id="qr_upload_btn" class="upload-button1"><span>Image</span></div>
                                <span id="qr_status"></span>
                                <ul id="files"></ul>
                                <input type="hidden" id="qr_upload_url" name="qr_upload_url" value="">
                            </div>
                            <button type="button" data-toggle="tooltip" class="btn btn-excel btn-sm" title="Image Size Should Be Less Than 100KB, Resolution : 900 X 900 and Format: Jpg/JPEG/Png"><i class="fa fa-question-circle"></i></button>
                            <button type="button" id="qr_preview_btn" data-toggle="tooltip" class="btn btn-info btn-sm hidden" onclick="preview_qr_image()" title="Preview QR Code"><i class="fa fa-eye"></i></button>
                        </div>
                        <div class="img_view" id="img_view"></div>
                        <div class="col-sm-4 mg_bt_10">
                            <label for="logo_upload">Upload Branch Logo:</label>
                            <div class="div-upload">
                                <div id="logo_upload_btn" class="upload-button1"><span>Image</span></div>
                                <span id="logo_status"></span>
                                <ul id="files"></ul>
                                <input type="hidden" id="logo_upload_url" name="logo_upload_url" value="">
                            </div>
                            <button type="button" data-toggle="tooltip" class="btn btn-excel btn-sm" title="Image Size Should Be Less Than 100KB, Dimension : 222 X 83 and Format: Jpg/JPEG/Png"><i class="fa fa-question-circle"></i></button>
                            <button type="button" id="logo_preview_btn" data-toggle="tooltip" class="btn btn-info btn-sm hidden" onclick="preview_logo_image()" title="Preview Logo"><i class="fa fa-eye"></i></button>
                        </div>
                        <div class="col-sm-4 mg_bt_10">
                            <input type="hidden" id="bank_name" name="bank_name" placeholder="Bank Name" title="Bank Name"
                                class="bank_suggest">
                        </div>
                        <div class="col-sm-4 mg_bt_10">
                            <input type="hidden" id="bank_account_name" name="bank_account_name"
                                placeholder="Bank Account Name" title="Bank Account Name" class="">
                        </div>

                        <div class="col-sm-4 mg_bt_10">
                            <select class="hidden" name="acc_name" id="acc_name" title="Account Type">
                                <option value="">Account Type</option>
                                <option value="Savings">Savings</option>
                                <option value="Current">Current</option>
                            </select>
                        </div>
                        <div class="col-sm-4 mg_bt_10">
                            <input type="hidden" id="bank_acc_no" name="bank_acc_no" placeholder="A/c No"
                                onchange="validate_accountNo(this.id)" title="A/c No">
                        </div>
                        <div class="col-sm-4 mg_bt_10">
                            <input type="hidden" id="bank_branch_name" onchange="validate_branch(this.id);"
                                name="bank_branch_name" placeholder="Branch Name" title="Branch Name">
                        </div>

                        <div class="col-sm-4 mg_bt_10">
                            <input type="hidden" id="bank_ifsc_code" onchange="validate_IFSC(this.id);"
                                name="bank_ifsc_code" placeholder="IFSC/SWIFT Code" title="IFSC/SWIFT Code"
                                style="text-transform: uppercase;">
                        </div>
                        <div class="col-sm-4 mg_bt_10">
                            <input type="hidden" id="branch_tax" name="branch_tax" placeholder="Branch Tax no."
                                title="Branch Tax no." style="text-transform: uppercase;">
                        </div>
                    </div>

                    <div class="row text-center mg_tp_10">
                        <div class="col-md-12">
                            <button class="btn btn-sm btn-success" id="branch_save"><i
                                    class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

<script>
$('#branches_save_modal').modal('show');
$('#locations_id,#state').select2();

$('#branches_save_modal').on('shown.bs.modal', function() {
    $('#locations_id').select2('open'); //focus after modal open 
});

// Below solution is specifically for this page only because of unncessary tooltip occuring bug
// $(".select2-container").tooltip({
//     title: "Select Location",
//     placement: "bottom"
// });
$(".select2-selection span").attr('title', '');
// till here


upload_qr_branch();

function upload_qr_branch() {
    var btnUpload = $('#qr_upload_btn');
    $(btnUpload).find('span').text('Image');

    $("#qr_upload_url").val('');
    new AjaxUpload(btnUpload, {
        action: 'branches/upload_qr.php',
        name: 'uploadfileQR',
        onSubmit: function(file, ext) {
            if (!(ext && /^(jpg|png|jpeg)$/.test(ext))) {
                error_msg_alert('Only JPG, PNG, JPEG files are allowed');
                return false;
            }
            $(btnUpload).find('span').text('Uploading...');
        },
        onComplete: function(file, response) {
            console.log('Upload response:', response); // Debug log
            
            if (response.indexOf('error') !== -1) {
                var errorMsg = response.split('--');
                if(errorMsg.length > 1){
                    error_msg_alert(errorMsg[1]);
                } else if (response == "error1") {
                    error_msg_alert('Maximum size exceeds (Should be less than 100KB)');
                } else {
                    error_msg_alert('File upload failed. Please try again.');
                }
                $(btnUpload).find('span').text('Image');
                return false;
            } else {
                $(btnUpload).find('span').text('Uploaded');
                $("#qr_upload_url").val(response);
                $('#qr_preview_btn').removeClass('hidden'); // Show preview button
                msg_alert('QR Code uploaded successfully');
            }
        }
    });
}

upload_logo_branch();

function upload_logo_branch() {
    var btnUpload = $('#logo_upload_btn');
    $(btnUpload).find('span').text('Image');

    $("#logo_upload_url").val('');
    new AjaxUpload(btnUpload, {
        action: 'branches/upload_logo.php',
        name: 'uploadfileLOGO',
        onSubmit: function(file, ext) {
            if (!(ext && /^(jpg|png|jpeg)$/.test(ext))) {
                error_msg_alert('Only JPG, PNG, JPEG files are allowed');
                return false;
            }
            $(btnUpload).find('span').text('Uploading...');
        },
        onComplete: function(file, response) {
            console.log('Logo Upload response:', response); // Debug log
            
            if (response.indexOf('error') !== -1) {
                var errorMsg = response.split('--');
                if(errorMsg.length > 1){
                    error_msg_alert(errorMsg[1]);
                } else if (response == "error1") {
                    error_msg_alert('Maximum size exceeds (Should be less than 100KB)');
                } else {
                    error_msg_alert('File upload failed. Please try again.');
                }
                $(btnUpload).find('span').text('Image');
                return false;
            } else {
                $(btnUpload).find('span').text('Uploaded');
                $("#logo_upload_url").val(response);
                $('#logo_preview_btn').removeClass('hidden'); // Show preview button
                msg_alert('Branch Logo uploaded successfully');
            }
        }
    });
}

function preview_qr_image() {
    var qr_path = $('#qr_upload_url').val();
    if(qr_path) {
        var img_url = qr_path.substring(9); // Remove '../../../' from path
        var full_url = $('#base_url').val() + img_url;
        
        var modalHtml = '<div class="modal fade" id="qr_preview_modal" role="dialog">' +
            '<div class="modal-dialog modal-lg">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
            '<h4 class="modal-title">QR Code Preview</h4>' +
            '</div>' +
            '<div class="modal-body text-center">' +
            '<img src="' + full_url + '" alt="QR Code" style="max-width: 100%; height: auto;">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        
        $('#img_view').html(modalHtml);
        $('#qr_preview_modal').modal('show');
    }
}

function preview_logo_image() {
    var logo_path = $('#logo_upload_url').val();
    if(logo_path) {
        var img_url = logo_path.substring(9); // Remove '../../../' from path
        var full_url = $('#base_url').val() + img_url;
        
        var modalHtml = '<div class="modal fade" id="logo_preview_modal" role="dialog">' +
            '<div class="modal-dialog modal-lg">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
            '<h4 class="modal-title">Branch Logo Preview</h4>' +
            '</div>' +
            '<div class="modal-body text-center">' +
            '<img src="' + full_url + '" alt="Branch Logo" style="max-width: 100%; height: auto;">' +
            '<p class="mg_tp_10"><small>Recommended Dimension: 222 X 83</small></p>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        
        $('#img_view').html(modalHtml);
        $('#logo_preview_modal').modal('show');
    }
}

$(function() {
    $('#frm_branch_save').validate({
        rules: {
            branch_name: {
                required: true
            },
            locations_id: {
                required: true
            },
            branch_address: {
                required: true
            },
            active_flag: {
                required: true
            },
            address1: {
                required: true
            },
            city: {
                required: true
            },
            contact_no: {
                required: true
            },
            bank_acc_no: {
                maxlength: 50
            }
            //email_id : {email: true},
        },
        errorPlacement: function(error, element) {
            if (element.attr('id') == "locations_id") {
                error.insertAfter('#select2-locations_id-container');
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            $('#branch_save').prop('disabled',true);
            var base_url = $('#base_url').val();
            $('#branch_save').button('loading');
            $.ajax({
                type: 'post',
                url: base_url + 'controller/branches_and_location/branch_save.php',
                data: $('#frm_branch_save').serialize(),
                success: function(result) {
                    var msg = result.split('--');
                    $('#branch_save').prop('disabled',false);
                    if (msg[0] == 'error') {
                        error_msg_alert(msg[1]);
                        $('#branch_save').button('reset');
                        return false;
                    } else {
                        msg_alert(result);
                        $('#branch_save').button('reset');
                        $('#branches_save_modal').modal('hide');
                        reset_form('frm_branch_save');
                        branches_list_reflect();
                    }
                }
            });
        }
    });
});
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>