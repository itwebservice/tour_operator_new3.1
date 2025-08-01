<?php
include_once("../../../model/model.php");
$entry_id = $_POST['entry_id'];
$query = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM `b2c_career` where entry_id='$entry_id'"));
?>
<form id="section_career_form1">

    <div class="modal fade" id="career_update_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Career</h4>
                </div>
                <div class="modal-body">
                    <?php
                        // $url = $query['image'];
                        $position = $query['position'];
                        $location=$query['location'];
                        $job_type=$query['job_type'];
                        $job_description = $query['job_description'];
                        $skills=$query['skills'];
                        $benefits=$query['benefits'];
                        
                        ?>
                        <div class="row mg_bt_20">
                           


                        <div class="col-md-4">
                            
                            <textarea name="position" placeholder="*Position" id="position1" title="Position" class="form-control" onchange="validate_career_1(this.id);" rows="8" required style="height:50px;"><?php echo $position; ?></textarea>

                        </div>

                        <div class="col-md-4">
                        <textarea  name="location" placeholder="*Location" id="location1" title="Location" class="form-control" onchange="validate_career_1(this.id);" row='8' style="height:50px;"  required><?php echo $location; ?></textarea>
                        </div>

                        <div class="col-md-4">
                        <textarea  name="job_type" placeholder="*Job Type" id="job_type1" title="Job Type" class="form-control" style="height:50px;" onchange="validate_career_1(this.id);" row='15'   required><?php echo $job_type; ?></textarea>
                        </div>
                    </div>
                    <div class="row mg_bt_20">
                        <div class="col-md-12">
                            <h5>Job Description</h5>
                            <textarea name="job_description" placeholder="*Job Description" title=" Job Description" class="form-control feature_editor" id="job_description1" rows="5" onchange="validate_career(this.id);" required><?php echo $job_description; ?></textarea>
                        </div>
</div>
<div class="row mg_bt_20">
                        <div class="col-md-12">
                            <h5>Skills</h5>
                            <textarea name="skills" placeholder="*Skills" title="Skills" class="form-control feature_editor" onchange="validate_career(this.id);"  id="skills1" rows="5" required><?php echo $skills; ?></textarea>
                        </div>
</div>
<div class="row mg_bt_20">
                        <div class="col-md-12">
                            <h5>Benefits</h5>
                            <textarea name="benefits" placeholder="*Benefits" title="Benefits" class="form-control feature_editor" id="benefits1" rows="5" onchange="validate_career(this.id);"  required><?php echo $benefits; ?></textarea>
                        </div>
                    </div>

                            <div class="col-md-4">
                                <select class="form-control" id="active_flag" name="active_flag" title="Active status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                      
                        <div class="row mg_bt_10 hidden">
                            <div class="col-md-4">
                                <!-- <div class="div-upload hidden">
                                    <div id="id_upload_btn" class="upload-button1 hidden"><span><?php echo ($url=='') ?  'Upload' : 'Uploaded' ?></span></div>
                                    <span id="id_proof_status" ></span>
                                    <ul id="files"></ul>
                                    <input type="hidden" id="image_upload_url1" name="image_upload_url1" value="<?php echo $url; ?>">
                                </div> -->
                                <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note: Upload Image size below 1MB, resolution : 1800*700, Format : JPEG,JPG."><i class="fa fa-question-circle"></i></button>
                            </div>
                        </div>
                        <?php
                        $newUrl1 = preg_replace('/(\/+)/','/',$url); 
                        $newUrl = BASE_URL.str_replace('../', '', $newUrl1);
                        ?>
                    <img src="<?php echo $newUrl; ?>" class="img-responsive hidden">
                    <input type="hidden" id="entry_id1" value="<?=$entry_id?>"/>

                    <div class="row mg_tp_20">
                        <div class="col-xs-12 text-center">
                            <button class="btn btn-sm btn-success" id="btn_update1"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Update</button>
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
$('#career_update_modal').modal('show');
// upload_blog_image1();
// function upload_blog_image1(){
//     var btnUpload=$('#id_upload_btn');
//     var up_url = $("#image_upload_url1").val();
//     var label = (up_url=='') ? 'Image': 'Uploaded';
//     $(btnUpload).find('span').text(label);

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
//                 $(btnUpload).find('span').text('Upload');
//             }
//             else{
//                 $(btnUpload).find('span').text('Uploaded');
//                 $("#image_upload_url1").val(response);
//             }
//         }
//     });
// }
$(function(){
$('#section_career_form1').validate({
    rules:{
    },
    submitHandler:function(form){

        var base_url = $('#base_url').val();

        var entry_id = $("#entry_id1").val();

      
        var position=$('#position1').val();
       var location=$('#location1').val();
       var job_type=$('#job_type1').val();

       var job_description=$('#job_description1').val();

       var skills=$('#skills1').val();

    //    var skills = iframe.contentWindow.document.body.innerHTML;

       var benefits=$('#benefits1').val();




        var active_flag = $('#active_flag').val();
        // var iframe = document.getElementById("desc1-wysiwyg-iframe");
        // var description = iframe.contentWindow.document.body.innerHTML;
        var old_array = [];
        // if(description==''||description=='<br>'){
        //     error_msg_alert("Enter description!");
        //     return false;
        // }
        


        var flag1 = validate_career('job_description1');
        if (!flag1) {
          return false;
        }
        var flag2 = validate_career('skills1');
        if (!flag2) {
          return false;
        }

        var flag3 = validate_career('benefits1');
        if (!flag3) {
          return false;
        }


        var flag4 = validate_career_1('position1');
        if (!flag4) {
          return false;
        }

        var flag5 = validate_career_1('location1');
        if (!flag5) {
          return false;
        }

        var flag6 = validate_career_1('job_type1');
        if (!flag6) {
          return false;
        }




        old_array.push({
           'position':position,
            'location':location,
            'job_type':job_type,
            'job_description':job_description ,
            'skills':skills,
            'benefits':benefits,
            'entry_id':entry_id,
            'active_flag':active_flag
        });
        $('#btn_update1').button('loading');
        $.ajax({
        type:'post',
        url: base_url+'controller/b2c_settings/cms_save.php',
        data:{ section : '25', data : old_array},
            success: function(message){
                var data = message.split('--');
                if(data[0] == 'erorr'){
                    error_msg_alert(data[1]);
                    return false;
                }else{
                    success_msg_alert(data[1]);
                    $('#btn_update1').button('reset');
                    $('#career_update_modal').modal('hide');
                    list_reflect();
                    // update_b2c_cache();
                }
            }
        });
    }
});
});
</script>
