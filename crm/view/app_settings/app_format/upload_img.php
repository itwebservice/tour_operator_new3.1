<?php
include "../../../model/model.php";
include __DIR__ . '/quot_format_config.php';

$sq_settings = mysqli_fetch_assoc(mysqlQuery("select quot_format, format_dest_id from app_settings where setting_id='1' limit 1"));
$qf_val = (int) (isset($sq_settings['quot_format']) ? $sq_settings['quot_format'] : 0);
$quot_format_labels = quot_format_get_labels();
if ($qf_val <= 0 || !isset($quot_format_labels[$qf_val])) {
	$qf_val = 1;
}
$format_dest_id = isset($sq_settings['format_dest_id']) ? (int) $sq_settings['format_dest_id'] : 0;
?>



<div class="modal fade profile_box_modal" id="format_upload_modal" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="formatUploadModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" data-original-title="" title="">×</span></button>
                <h4 class="modal-title" id="formatUploadModalLabel">Upload Image</h4>
            </div>

            <div class="modal-body ">
                <div class="row">
                    <div class="col-md-6">
                        <select name="format_list_upload" id="format_list_upload" title="Quotation Format List" class="form-control">
                            <?php foreach ($quot_format_labels as $opt_val => $opt_label) {
                                $selected = ($qf_val === (int) $opt_val) ? 'selected' : '';
                            ?>
                                <option value="<?= (int) $opt_val ?>" <?= $selected ?>><?= htmlspecialchars($opt_label) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="" id="destination_format_filter_upload" title="Destination" class="form-control" style="width:250px;">

                            <?= get_destinations_option($format_dest_id) ?>
                        </select>
                    </div>
                    <div class="col-md-12 text-center mg_tp_10">
                        <!--  upload-->



                        <div class="div-upload">
                            <div id="qr_upload_btn" class="upload-button1"><span>Image</span></div>
                            <span id="photo_status"></span>
                            <ul id="files"></ul>
                            <input type="hidden" id="qr_upload_url_i" name="qr_upload_url_i">

                        </div>

                        <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Image Size Should Be Less Than 100KB, Resolution :
                   Option-1 / Portrait Standard: 807×300,
                   Option-4: 1240×1470,
                   Option-5 / Portrait Advanced: 535×1142,
                   Option-2: 1136×570,
                   Option-3: 1136×570,
                   Landscape Advanced: 1137×802
                   and Format: Jpg/JPEG/Png"><i class="fa fa-question-circle"></i></button>
                        <!--  upload End-->
                    </div>
                    <div class="col-md-12 mg_tp_10 text-center">
                        <button class="btn btn-sm btn-success" type="button" onclick="imgSave()" id="img_upload_save"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#format_upload_modal').modal('show');
    $('#destination_format_filter_upload').select2();

    // Sync with main Quotation Format dropdown when upload modal opens
    if ($('#format_list').length && $('#format_list').val()) {
        $('#format_list_upload').val($('#format_list').val());
    }
    if ($('#destination_format_filter').length && $('#destination_format_filter').val()) {
        $('#destination_format_filter_upload').val($('#destination_format_filter').val()).trigger('change.select2');
    }

    upload_qr_company();

    function upload_qr_company() {

        var base_url = $('#base_url').val();
        var btnUpload = $('#qr_upload_btn');
        $(btnUpload).find('span').text('Image');

        $("#qr_upload_url_i").val('');
        new AjaxUpload(btnUpload, {
            action: base_url + 'view/app_settings/app_format/upload_img_store.php',
            name: 'uploadfileQR',
            onSubmit: function(file, ext) {
                if (!(ext && /^(jpg|png|jpeg)$/.test(ext))) {
                    error_msg_alert('Only JPG, PNG files are allowed');
                    return false;
                }
                $(btnUpload).find('span').text('Uploading...');
            },
            onComplete: function(file, response) {
                if (response === "error") {
                    error_msg_alert("File is not uploaded.");
                    $(btnUpload).find('span').text('Upload');
                } else {
                    if (response == "error1") {
                        $(btnUpload).find('span').text('Upload Images');
                        error_msg_alert('Maximum size exceeds');
                        return false;
                    } else {
                        $(btnUpload).find('span').text('Uploaded');
                        $("#qr_upload_url_i").val(response);
                    }

                }
                if (typeof img_array !== 'undefined' && img_array.length > 1) {
                    error_msg_alert("You can upload only 3 images");
                    return false;
                }

            }
        });
    }


    function imgSave() {
        var format = $('#format_list_upload').val();
        var destination = $('#destination_format_filter_upload').val();
        var imgUrl = $('#qr_upload_url_i').val();
        var base_url = $('#base_url').val();
        if (destination == "") {
            error_msg_alert("Destination Is Required");
            return false;
        }
        if (imgUrl == "") {
            error_msg_alert("Image Is Required");
            return false;
        }
        $.post(base_url + 'controller/app_settings/quotationFormat.php', {
            format: format,
            destination: destination,
            imgUrl: imgUrl,
        }, function(data) {
            success_msg_alert(data);
            $('#format_upload_modal').modal('hide');
            display_images('format_list');
        });

    }
</script>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>
