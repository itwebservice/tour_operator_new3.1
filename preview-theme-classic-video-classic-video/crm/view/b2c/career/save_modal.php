<?php
include_once("../../../model/model.php");
?>
<form id="section_career">
    <div class="modal fade" id="career_save_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Career</h4>
                </div>

                <div class="modal-body">
                    <div class="row mg_bt_20">
                        <div class="col-md-4">
                            
                            <textarea name="position" placeholder="*Position" id="position" title="Position" class="form-control" onchange="validate_career_1(this.id);" rows="8" required style="height:50px;"></textarea>

                        </div>

                        <div class="col-md-4">
                        <textarea  name="location" placeholder="*Location" id="location" title="Location" class="form-control" onchange="validate_career_1(this.id);" row='8' style="height:50px;"  required></textarea>
                        </div>

                        <div class="col-md-4">
                        <textarea  name="job_type" placeholder="*Job Type" id="job_type" title="Job Type" class="form-control" style="height:50px;" onchange="validate_career_1(this.id);" row='15'   required></textarea>
                        </div>
                    </div>
                    <div class="row mg_bt_20">
                        <div class="col-md-12">
                            <h5>Job Description</h5>
                            <textarea name="job_description" placeholder="*Job Description" title=" Job Description" class="form-control feature_editor" id="job_description" rows="5" onchange="validate_career(this.id);" required></textarea>
                        </div>
</div>
                    <div class="row mg_bt_20">

                        <div class="col-md-12">
                            <h5>Skills</h5>
                            <textarea name="skills" placeholder="*Skills" title="Skills" class="form-control feature_editor" onchange="validate_career(this.id);"  id="skills" rows="5" required></textarea>
                        </div>
</div>
<div class="row mg_bt_20">

                        <div class="col-md-12">
                            <h5>Benefits</h5>
                            <textarea name="benefits" placeholder="*Benefits" title="Benefits" class="form-control feature_editor" id="benefits" rows="5" onchange="validate_career(this.id);"  required></textarea>
                        </div>
                    </div>

                    <div class="row mg_bt_10 hidden">
                        <div class="col-md-3">          
                            <div class="div-upload">
                                <div id="id_upload_btn" class="upload-button1"><span>Upload</span></div>
                                <span id="id_proof_status" ></span>
                                <ul id="files"></ul>
                                <input type="hidden" id="image_upload_url" name="image_upload_url">
                            </div>
                            <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note: Upload Image size below 1MB, resolution : 1800*700, Format : JPEG,JPG."><i class="fa fa-question-circle"></i></button>
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
$('#career_save_modal').modal('show');
// upload_blog_image();
// function upload_blog_image(){
//     var btnUpload=$('#id_upload_btn');
//     $(btnUpload).find('span').text('Image');

//     new AjaxUpload(btnUpload, {
//         action: 'blogs/upload_blog_img.php',
//         name: 'uploadfile',
//         onSubmit: function(file, ext)
//         {
//             if (! (ext && /^(png|jpeg|jpg)$/.test(ext))){ 
//             error_msg_alert('Only JPG,JPEG,PNG files are allowed');
//             return false;
//             }
//             $(btnUpload).find('span').text('Uploading...');
//         },
//         onComplete: function(file, response){
//             var response1 = response.split('--');
//             if(response1[0]=="error"){
//                 error_msg_alert(response1[1]);
//                 $(btnUpload).find('span').text('Image');
//             }
//             else{
//                 $(btnUpload).find('span').text('Uploaded');
//                 $("#image_upload_url").val(response);
//             }
//         }
//     });
// }
$(function(){
$('#section_career').validate({
    rules:{
    },
    submitHandler:function(form){

        var base_url = $('#base_url').val();

        // var btitle = $("#btitle").val();
       var position=$('#position').val();
       var location=$('#location').val();
       var job_type=$('#job_type').val();

       var job_description=$('#job_description').val();

       var skills=$('#skills').val();

    //    var skills = iframe.contentWindow.document.body.innerHTML;

       var benefits=$('#benefits').val();

    // var benefits = iframe.contentWindow.document.body.innerHTML;

        var iframe = document.getElementById("desc-wysiwyg-iframe");
        // var job_description = iframe.contentWindow.document.body.innerHTML;
        // if(description==''||description=='<br>' || ){
        //     error_msg_alert("Enter description!");
        //     return false;
        // }
        // if(image === ''){
        //     error_msg_alert("Upload image!");
        //     return false;
        // }
        var career = [];
        career.push({
            'position':position,
            'location':location,
            'job_type':job_type,
            'job_description':job_description ,
            'skills':skills,
            'benefits':benefits,
            'entry_id':''
        });

     
        var flag1 = validate_career('job_description');
        if (!flag1) {
          return false;
        }
        var flag2 = validate_career('skills');
        if (!flag2) {
          return false;
        }

        var flag3 = validate_career('benefits');
        if (!flag3) {
          return false;
        }


        var flag4 = validate_career_1('position');
        if (!flag4) {
          return false;
        }

        var flag5 = validate_career_1('location');
        if (!flag5) {
          return false;
        }

        var flag6 = validate_career_1('job_type');
        if (!flag6) {
          return false;
        }
      

        $('#btn_save1').button('loading');
        $.ajax({
        type:'post',
        url: base_url+'controller/b2c_settings/cms_save.php',
        data:{ section : '25', data : career},
            success: function(message){
                var data = message.split('--');
                if(data[0] == 'erorr'){
                    error_msg_alert(data[1]);
                    return false;
                }else{
                    success_msg_alert(data[1]);
                    $('#btn_save1').button('reset');
                    $('#career_save_modal').modal('hide');
                    list_reflect();
                    // update_b2c_cache();
                }
            }
        });
    }
});
});
</script>
