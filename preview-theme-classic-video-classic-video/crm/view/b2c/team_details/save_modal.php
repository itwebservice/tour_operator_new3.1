<?php
include_once("../../../model/model.php");
?>
<form id="section_team">
    <div class="modal fade" id="team_save_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Team Details</h4>
                </div>

                <div class="modal-body">
                    <div class="row mg_bt_20">
                        <div class="col-md-6">
                            <input type="text" name="tname" placeholder="*Name" id="tname" title="Name" class="form-control" required/>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="designation" placeholder="*Designation " id="designation" title="Designation " class="form-control" required/>
                        </div>
                    </div>
                   
                    <div class="row mg_bt_10">
                        <div class="col-md-3">          
                            <div class="div-upload">
                                <div id="id_upload_btn" class="upload-button1"><span>Upload</span></div>
                                <span id="id_proof_status" ></span>
                                <ul id="files"></ul>
                                <input type="hidden" id="image_upload_url" name="image_upload_url">
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
$('#team_save_modal').modal('show');
upload_blog_image();
function upload_blog_image(){
    var btnUpload=$('#id_upload_btn');
    $(btnUpload).find('span').text('Image');

    new AjaxUpload(btnUpload, {
        action: 'team_details/upload_team_img.php',
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
                $("#image_upload_url").val(response);
            }
        }
    });
}
$(function(){
$('#section_team').validate({
    rules:{
    },
    submitHandler:function(form){

        var base_url = $('#base_url').val();

        var tname = $("#tname").val();

        var designation = $("#designation").val();
        var image = $("#image_upload_url").val();
        // var iframe = document.getElementById("desc-wysiwyg-iframe");
        // var description = iframe.contentWindow.document.body.innerHTML;
        // if(description==''||description=='<br>'){
        //     error_msg_alert("Enter description!");
        //     return false;
        // }
        // alert(designation );
        if(image === ''){
            error_msg_alert("Upload image!");
            return false;
        }
        var team_details = [];
        team_details.push({
            'tname':tname,
            'designation':designation,
            'image':image
            
            // 'entry_id':''
        });

        $('#btn_save1').button('loading');
        $.ajax({
        type:'post',
        url: base_url+'controller/b2c_settings/cms_save.php',
        data:{ section : '27', data : team_details},
            success: function(message){
                var data = message.split('--');
                if(data[0] == 'erorr'){
                    error_msg_alert(data[1]);
                    return false;
                }else{
                    success_msg_alert(data[1]);
                    $('#btn_save1').button('reset');
                    $('#team_save_modal').modal('hide');
                    list_reflect();
                    // update_b2c_cache();
                }
            }
        });
    }
});
});
</script>
