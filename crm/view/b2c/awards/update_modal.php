<?php
include_once("../../../model/model.php");
$entry_id = $_POST['entry_id'];
$query = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM `b2c_awards` where entry_id='$entry_id'"));
?>
<form id="section_blogs_form1">

    <div class="modal fade" id="award_update_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Awards</h4>
                </div>
                <div class="modal-body">
                    <?php
                        $url = $query['image'];
                        $title = $query['title'];
                       
                        ?>
                        <div class="row mg_bt_20">
                        <div class="col-md-12">
                            <input type="text" id="title1" name="title" title="Title" placeholder="*Title(Upto 100 chars)" class="form-control" value="<?= $title ?>" onchange="validate_char_size('title1',100);" required/>
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
            onclick="delete_award_image('<?php echo $entry_id; ?>')" 
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




// function delete_award_image(entry_id) {
//     var base_url = $("#base_url").val();
//     var image_url = $("#image_upload_url1").val();

//     if (image_url === '') {
//         error_msg_alert("No image to delete!");
//         return;
//     }

//     $.ajax({
//         type: 'POST',
//         url: base_url + 'view/b2c/awards/delete_award_img.php',
//         data: { entry_id: entry_id, image_url: image_url },
//         success: function (response) {
//             var data = response.split('--');
//             if (data[0] == "error") {
//                 error_msg_alert(data[1]);
//             } else {
//                 success_msg_alert("Image deleted successfully!");
                
//                 // Remove the image preview
//                 $("#uploaded_image_preview").html('');

//                 // Reset the upload button text
//                 $("#id_upload_btn span").text("Upload");

//                 // Clear the hidden input field
//                 $("#image_upload_url1").val('');
//             }
//         }
//     });
// }


function delete_award_image(entry_id){
    var base_url = $("#base_url").val();

    var image_url = $("#image_upload_url1").val();
    
    $("#vi_confirm_box").vi_confirm_box({
          callback: function(result){
            if(result=="yes"){
              $.ajax({
                    type:'post',
                    url: base_url + 'view/b2c/awards/delete_award_img.php',
                    data:{ entry_id: entry_id, image_url: image_url },
                    success:function(result)
                    {
                      msg_alert(result);
                    //   load_images(hotel_name);
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



$('#award_update_modal').modal('show');
upload_blog_image1();
function upload_blog_image1(){
    var btnUpload=$('#id_upload_btn');
    var up_url = $("#image_upload_url1").val();
    
    var label = (up_url=='') ? 'Image': 'Uploaded';
    $(btnUpload).find('span').text(label);

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
            // console.log("Upload response:", response); 
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
        var title = $("#title1").val();
        // var designation = $('#designation1').val();
        // var testm = $("#testm1").val();
        // var image = $("#image_upload_url_testm1").val();

        var image = $("#image_upload_url1").val();
        if(title==''){
            error_msg_alert("Enter title!");
            return false;
        }
        if(image === ''){
            error_msg_alert("Upload image!");
            return false;
        }

        var old_array = [];
        var flag1 = validate_char_size('title1',100);
        if(!flag1){
            return false;
        }
        old_array.push({
            'title':title,
            'image':image,
            'entry_id':entry_id
            
        });
        $('#btn_update1').button('loading');
        $.ajax({
        type:'post',
        url: base_url+'controller/b2c_settings/cms_save.php',
        data:{ section : '28', data : old_array},
            success: function(message){
                var data = message.split('--');
                if(data[0] == 'erorr'){
                    error_msg_alert(data[1]);
                    return false;
                }else{
                    success_msg_alert(data[1]);
                    $('#btn_update1').button('reset');
                    $('#award_update_modal').modal('hide');
                    list_reflect();
                    // update_b2c_cache();
                }
            }
        });
    }
});
});
</script>
