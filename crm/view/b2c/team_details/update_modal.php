<?php
include_once("../../../model/model.php");
$entry_id = $_POST['entry_id'];
$query = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM `b2c_team_details` where entry_id='$entry_id'"));
?>
<form id="section_blogs_form1">

    <div class="modal fade" id="team_update_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Team Details</h4>
                </div>
                <div class="modal-body">
                    <?php
                        $url = $query['image'];
                        $tname = $query['tname'];
                        $designation = $query['designation'];
                        ?>
                        <div class="row mg_bt_20">
                            <div class="col-md-6">
                                <input type="text" name="bname1" placeholder="*Name" id="tname1" title="Name" class="form-control" value="<?php echo $tname; ?>" required/>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="designation1" placeholder="*Description" title="Designation" class="form-control feature_editor" id="designation1" rows="5" value="<?php echo $designation; ?>" required/>
                            </div>
                         

                            </div>
                    <div class="row mg_bt_10">
    <div class="col-md-4">
        <div class="div-upload">
            <div id="id_upload_btn" class="upload-button1"><span><?php echo ($url=='') ?  'Upload' : 'Uploaded' ?></span></div>
            <span id="id_proof_status"></span>
            <ul id="files"></ul>
            <input type="hidden" id="image_upload_url1" name="image_upload_url1" value="<?php echo $url; ?>">
        </div>

        <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note: Upload Image size should be less than 100KB, resolution: 200×200, Format: JPEG, JPG,PNG.">
            <i class="fa fa-question-circle"></i>
        </button>
    </div>


<?php
$newUrl1 = preg_replace('/(\/+)/', '/', $url);
$newUrl = BASE_URL . str_replace('../', '', $newUrl1);
?>

<div id="uploaded_image_preview" style="position: relative; display: inline-block;margin-top:10px;margin-left:20px;margin-right:20px;">
    <?php if ($url != ''): ?>
        <button type="button" class="btn btn-danger btn-sm" id="delete_image_btn" 
            onclick="delete_team_image('<?php echo $entry_id; ?>')" 
            style="position: absolute; top: 5px; right: 5px; z-index: 10;">
            <i class="fa fa-times"></i>
        </button>
    
    <img src="<?php echo $newUrl; ?>" class="img-responsive" id="team_image" style="display: block; max-width: 100%; height: auto;">
</div>
<?php endif; ?>



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







function delete_team_image(entry_id){
    var base_url = $("#base_url").val();

    var image_url = $("#image_upload_url1").val();
    
    $("#vi_confirm_box").vi_confirm_box({
          callback: function(result){
            if(result=="yes"){
              $.ajax({
                    type:'post',
                    url: base_url + 'view/b2c/team_details/delete_team_img.php',
                    data:{ entry_id: entry_id, image_url: image_url },
                    success:function(result)
                    {
                      msg_alert(result);
                    //   load_images(hotel_name);
                       // Remove the image preview
                $("#uploaded_image_preview").html('');

                // Reset the upload button text
                $("#id_upload_btn span").text("Upload");

                // Clear the hidden input field
                $("#image_upload_url1").val('');
                    }
              });    
            } }
    });
}


$('#team_update_modal').modal('show');
upload_blog_image1();
function upload_blog_image1(){
    var btnUpload=$('#id_upload_btn');
    var up_url = $("#image_upload_url1").val();
    var label = (up_url=='') ? 'Image': 'Uploaded';
    $(btnUpload).find('span').text(label);

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
                $(btnUpload).find('span').text('Upload');
            }
            else{
                $(btnUpload).find('span').text('Uploaded');
                $("#image_upload_url1").val(response);
            }
        }
    });
}
$(function(){
$('#section_blogs_form1').validate({
    rules:{
    },
    submitHandler:function(form){

        var base_url = $('#base_url').val();

        var entry_id = $("#entry_id1").val();

        var tname = $("#tname1").val();
        var image = $("#image_upload_url1").val();
        var active_flag = $('#active_flag').val();

        var designation = $('#designation1').val();
        var iframe = document.getElementById("desc1-wysiwyg-iframe");
      

var old_array = [];
        
        if(image === ''){
            error_msg_alert("Upload image!");
            return false;
        }
        old_array.push({
            'tname':tname,
            'image':image,
            'designation':designation,
            'entry_id':entry_id,
            'active_flag':active_flag
        });
        $('#btn_update1').button('loading');
        $.ajax({
        type:'post',
        url: base_url+'controller/b2c_settings/cms_save.php',
        data:{ section : '27', data : old_array},
            success: function(message){
                var data = message.split('--');
                if(data[0] == 'erorr'){
                    error_msg_alert(data[1]);
                    return false;
                }else{
                    success_msg_alert(data[1]);
                    $('#btn_update1').button('reset');
                    $('#team_update_modal').modal('hide');
                    list_reflect();
                    // update_b2c_cache();
                }
            }
        });
    }
});
});
</script>
