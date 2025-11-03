<?php
include_once('../../../model/model.php');

$branch_id = $_POST['branch_id'];

$sq_branch = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_id'"));

$location_id = $sq_branch['location_id'];

$sq_location = mysqli_fetch_assoc(mysqlQuery("select * from locations where location_id='$location_id'"));
?>

<form id="frm_branch_update">

    <input type="hidden" id="branch_id" name="branch_id" value="<?= $sq_branch['branch_id'] ?>">

    <div class="modal fade" id="branch_update_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document" style="width: 95% !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Branch</h4>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">

                            <div class="row">
                                <div class="col-sm-4 mg_bt_10">
                                    <select name="location_id" id="location_id" class="app_select2"
                                        title="Select Location" style="width:100%">
                                        <option value="<?= $sq_location['location_id'] ?>"><?= $sq_location['location_name'] ?></option>
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
                                <div class="col-sm-4 mg_bt_10">
                                    <input type="text" id="branch_name1" name="branch_name1" placeholder="Company Branch Name"
                                        class="form-control" title="Company Branch Name"
                                        value="<?= $sq_branch['branch_name'] ?>">
                                </div>
                                <div class="col-sm-4 mg_bt_10">
                                    <input type="text" id="contact_no" onchange="mobile_validate(this.id);"
                                        name="contact_no" placeholder="*Contact No" title="Contact No"
                                        value="<?= $sq_branch['contact_no'] ?>">
                                </div>
                                <div class="col-sm-4 mg_bt_10 hidden">
                                    <textarea name="branch_address" id="branch_address" placeholder="Branch Address"
                                        onchange="validate_address(this.id);"
                                        title="Branch Address"><?= $sq_branch['branch_address'] ?></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 mg_bt_10">
                                    <input type="text" id="email_id" name="email_id" placeholder="Email ID"
                                        title="Email ID" onchange="validate_email(this.id);"
                                        value='<?= $sq_branch['email_id'] ?>'>
                                </div>
                                <div class="col-sm-4 mg_bt_10">
                                    <input type="text" id="landline_no" name="landline_no" placeholder="Landline No"
                                        onchange="mobile_validate(this.id);" title="Landline No"
                                        value="<?= $sq_branch['landline_no'] ?>">
                                </div>
                                <div class="col-sm-4 mg_bt_10">
                                    <textarea name="address1" id="address1" onchange="validate_address(this.id);"
                                        placeholder="*Address1" title="Address1"
                                        rows="1"><?= $sq_branch['address1'] ?></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 mg_bt_10">
                                    <textarea name="address2" id="address2" placeholder="Address2"
                                        onchange="validate_address(this.id);" title="Address2"
                                        rows="1"><?= $sq_branch['address2'] ?></textarea>
                                </div>
                                <div class="col-sm-4 mg_bt_10">
                                    <input type="text" id="city" name="city" placeholder="*City" title="City"
                                        onchange="validate_city(this.id);" value="<?= $sq_branch['city'] ?>">
                                </div>
                                <div class="col-sm-4 mg_bt_10">
                                    <input type="text" id="pincode" onchange="validate_PINCode(this.id);" name="pincode"
                                        placeholder="Pincode" title="Pincode" value="<?= $sq_branch['pincode'] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 mg_bt_10">
                                    <select name="state" id="state" title="State/Country Name" class='form-control'
                                        style='width:100%' required>
                                        <?php
                                        if ($sq_branch['state'] != "") {
                                        $sq_state = mysqli_fetch_assoc(mysqlQuery("select * from state_master where id='$sq_branch[state]'")); ?>
                                        <option value="<?= $sq_state['id'] ?>"><?= $sq_state['state_name'] ?></option>
                                        <?php } ?>
                                        <?php get_states_dropdown() ?>
                                    </select>
                                </div>
                               
                                <div class="col-sm-4 mg_bt_10">
                                    <select name="active_flag" id="active_flag" title="Status">
                                        <option value="<?= $sq_branch['active_flag'] ?>">
                                            <?= $sq_branch['active_flag'] ?></option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-sm-4 mg_bt_10">
                                    <input type="hidden" id="bank_name" name="bank_name" placeholder="Bank Name"
                                        title="Bank Name" class="bank_suggest" value="<?= $sq_branch['bank_name'] ?>">
                                </div>
                                <div class="col-sm-4 mg_bt_10">
                                    <input type="hidden" id="bank_account_name" name="bank_account_name"
                                        placeholder="Bank Account Name" title="Bank Account Name" class=""
                                        value="<?= $sq_branch['bank_account_name'] ?>">
                                </div>
                                <div class="col-sm-4 mg_bt_10">
                                    <select class="hidden" name="acc_name" id="acc_name" title="Account Type">
                                        <?php if ($sq_branch['acc_name']) { ?>
                                        <option value="<?= $sq_branch['acc_name'] ?>"><?= $sq_branch['acc_name'] ?>
                                        </option>
                                        <?php } ?>
                                        <option value="">Account Type</option>
                                        <option value="Savings">Savings</option>
                                        <option value="Current">Current</option>
                                    </select>
                                </div>
                                 <div class="col-sm-4 mg_bt_10">
                                    <label for="qr_upload">Upload QR Code:</label>
                                    <div class="div-upload">
                                        <div id="qr_upload_btn" class="upload-button1"><span>Image</span></div>
                                        <span id="qr_status"></span>
                                        <ul id="files"></ul>
                                        <input type="hidden" id="qr_upload_url" name="qr_upload_url" value="<?= $sq_branch['qr_url'] ?>">
                                    </div>
                                    <button type="button" data-toggle="tooltip" class="btn btn-excel btn-sm" title="Image Size Should Be Less Than 100KB, Resolution : 900 X 900 and Format: Jpg/JPEG/Png"><i class="fa fa-question-circle"></i></button>
                                    <?php if (!empty($sq_branch['qr_url'])) { ?>
                                        <button type="button" data-toggle="tooltip" class="btn btn-info btn-sm" onclick="img_view_modal(<?= $branch_id ?>)" title="View QR Code"><i class="fa fa-eye"></i></button>
                                    <?php } ?>
                                </div>
                                <div class="img_view" id="img_view"></div>
                                <div class="col-sm-4 mg_bt_10">
                                    <label for="logo_upload">Upload Branch Logo:</label>
                                    <div class="div-upload">
                                        <div id="logo_upload_btn" class="upload-button1"><span>Image</span></div>
                                        <span id="logo_status"></span>
                                        <ul id="files"></ul>
                                        <input type="hidden" id="logo_upload_url" name="logo_upload_url" value="<?= $sq_branch['logo_url'] ?>">
                                    </div>
                                    <button type="button" data-toggle="tooltip" class="btn btn-excel btn-sm" title="Image Size Should Be Less Than 100KB, Dimension : 222 X 83 and Format: Jpg/JPEG/Png"><i class="fa fa-question-circle"></i></button>
                                    <?php if (!empty($sq_branch['logo_url'])) { ?>
                                        <button type="button" data-toggle="tooltip" class="btn btn-info btn-sm" onclick="img_view_logo_modal(<?= $branch_id ?>)" title="View Branch Logo"><i class="fa fa-eye"></i></button>
                                    <?php } ?>
                                </div>

                                <div class="col-sm-4 mg_bt_10">
                                    <input type="hidden" id="bank_acc_no" name="bank_acc_no"
                                        onchange="validate_accountNo(this.id)" placeholder="A/c No" title="A/c No"
                                        value="<?= $sq_branch['bank_acc_no'] ?>">
                                </div>
                                <div class="col-sm-4 mg_bt_10">
                                    <input type="hidden" id="bank_branch_name" name="bank_branch_name"
                                        placeholder="Branch Name" onchange="validate_branch(this.id);"
                                        title="Branch Name" value="<?= $sq_branch['bank_branch_name'] ?>">
                                </div>
                                <div class="col-sm-4 mg_bt_10">
                                    <input type="hidden" id="bank_ifsc_code" name="bank_ifsc_code"
                                        onchange="validate_IFSC(this.id);" placeholder="IFSC/SWIFT Code"
                                        title="IFSC/SWIFT Code" value="<?= $sq_branch['ifsc_code'] ?>"
                                        style="text-transform: uppercase;">
                                </div>

                                <div class="col-sm-4 mg_bt_10">
                                    <input type="hidden" id="branch_tax" name="branch_tax" placeholder="Branch Tax no."
                                        title="Branch Tax no." value="<?= $sq_branch['branch_tax'] ?>"
                                        style="text-transform: uppercase;">
                                </div>
                            </div>
                            <div class="row text-center mg_tp_10">
                                <div class="col-md-12">
                                    <button class="btn btn-sm btn-success" id="branch_update"><i
                                            class="fa fa-floppy-o"></i>&nbsp;&nbsp;Update</button>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

<script>
$('#branch_update_modal').modal('show');
$('#location_id,#state').select2();
$('#branch_update_modal').on('shown.bs.modal', function() {
    $('#branch_name1').focus();
}); //focus after modal open
upload_qr_branch();

function upload_qr_branch() {
    var btnUpload = $('#qr_upload_btn');
    $(btnUpload).find('span').text('Image');

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
                msg_alert('QR Code uploaded successfully');
            }
        }
    });
}

upload_logo_branch();

function upload_logo_branch() {
    var btnUpload = $('#logo_upload_btn');
    $(btnUpload).find('span').text('Image');

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
                msg_alert('Branch Logo uploaded successfully');
            }
        }
    });
}

function img_view_modal(branch_id) {
    $.post('branches/view_qr.php', {
        branch_id: branch_id
    }, function(data) {
        $('#img_view').html(data);
    });
}

function img_view_logo_modal(branch_id) {
    $.post('branches/view_logo.php', {
        branch_id: branch_id
    }, function(data) {
        $('#img_view').html(data);
    });
}

$(function() {
    $('#frm_branch_update').validate({
        rules: {
            branch_name1: {
                required: true
            },
            location_id: {
                required: true
            },
            branch_address: {
                required: true
            },
            active_flag: {
                required: true
            },
            contact_no: {
                required: true
            },
            address1: {
                required: true
            },
            city: {
                required: true
            },
            bank_acc_no: {
                maxlength: 50
            }
        },
        submitHandler: function(form) {
            $('#branch_update').button('loading');
            var base_url = $('#base_url').val();
            $.ajax({
                type: 'post',
                url: base_url + 'controller/branches_and_location/branch_update.php',
                data: $('#frm_branch_update').serialize(),
                success: function(result) {
                    var msg = result.split('--');
                    if (msg[0] == 'error') {
                        error_msg_alert(msg[1]);
                        $('#branch_update').button('reset');
                        return false;
                    } else {
                        $('#branch_update').button('reset');
                        $('#branch_update_modal').modal('hide');
                        msg_alert(result);
                        reset_form('frm_branch_update');
                        $('#branch_update_modal').on('hidden.bs.modal', function() {
                            branches_list_reflect();
                        });

                    }
                }
            });
        }
    });
});
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>