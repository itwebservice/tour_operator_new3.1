<?php
include_once("../../../model/model.php");
?>
<form id="section_testm">
    <div class="modal fade" id="award_save_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Awards</h4>
                </div>

                <div class="modal-body">
                    <div class="row mg_bt_20">
                        <div class="col-md-12">
                            <input type="text" id="title" name="title" title="Title" placeholder="*Title(Upto 100 chars)" class="form-control" onchange="validate_char_size('title',100);" required/>
                        </div>
                       
                    <div class="row mg_bt_10" >
                        <div class="col-md-4">          
                            <div class="div-upload" style="margin-top:10px !important;margin-left:20px;">
                                <div id="id_upload_btn" class="upload-button1"><span>Upload</span></div>
                                <span id="id_proof_status" ></span>
                                <ul id="files"></ul>
                                <input type="hidden" id="image_upload_url_testm" name="image_upload_url_testm">
                            </div>
                            <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note: Upload Image size should be less than 100KB, resolution: 200×200, Format: JPEG, JPG,PNG."><i class="fa fa-question-circle"></i></button>
                        </div>
                    </div>                        
                    <div class="row mg_tp_20">
                        <div class="col-xs-12 text-center">
                            <button class="btn btn-sm btn-success" id="btn_save1"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>
<script>
$('#award_save_modal').modal('show');
upload_testm_image();
function upload_testm_image(){
    var btnUpload=$('#id_upload_btn');
    $(btnUpload).find('span').text('Image');

    new AjaxUpload(btnUpload, {
        action: 'awards/upload_award_img.php',
        name: 'uploadfile',
        onSubmit: function(file, ext)
        {
            if (! (ext && /^(png|jpeg|jpg)$/.test(ext))){ 
            error_msg_alert('Only JPG,JPEG,PNG files are allowed');
            return false;
            }
            $(btnUpload).find('span').text('Uploading...');
        },
        onComplete: function(file, response){
            var response1 = response.split('--');
            if(response1[0]=="error"){
                error_msg_alert(response1[1]);
                $(btnUpload).find('span').text('Image');
            }
            else{
                $(btnUpload).find('span').text('Uploaded');
                $("#image_upload_url_testm").val(response);
            }
        }
    });
}
$(function(){
$('#section_testm').validate({
    rules:{
    },
    submitHandler:function(form){

        var base_url = $('#base_url').val();

        var title = $("#title").val();
        // var designation = $('#designation').val();
        // var testm = $("#testm").val();
        var image = $("#image_upload_url_testm").val();
        // if(testm==''){
        //     error_msg_alert("Enter testimonial!");
        //     return false;
        // }
        if(image === ''){
            error_msg_alert("Upload image!");
            return false;
        }
        var flag1 = validate_char_size('title',100);
        if(!flag1){
            return false;
        }

        var images_array = [];        
        images_array.push({
            'title':title,
            'image':image,
            'entry_id':''
           
        });
        $('#btn_save1').button('loading');
        $.ajax({
        type:'post',
        url: base_url+'controller/b2c_settings/cms_save.php',
        data:{ section : '28', data : images_array},
            success: function(message){
                var data = message.split('--');
                console.log(message);
                if(data[0] == 'erorr'){
                    error_msg_alert(data[1]);
                    return false;
                }else{
                    success_msg_alert(data[1]);
                    $('#btn_save1').button('reset');
                    $('#award_save_modal').modal('hide');
                    list_reflect();
                    // update_b2c_cache();
                }
            }
        });
    }
});
});
</script>
