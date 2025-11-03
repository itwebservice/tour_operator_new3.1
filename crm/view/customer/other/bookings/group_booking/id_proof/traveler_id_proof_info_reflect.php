<?php
include_once('../../../../../../model/model.php');
$traveler_id = $_POST['traveler_id'];
$sq_traveler_info = mysqli_fetch_assoc(mysqlQuery("select * from travelers_details where traveler_id='$traveler_id'"));
$traveler_group_id = $sq_traveler_info['traveler_group_id'];
$sq_group_tour = mysqli_fetch_assoc(mysqlQuery("select tour_id from tourwise_traveler_details where traveler_group_id='$traveler_group_id'"));
$sq_tour = mysqli_fetch_assoc(mysqlQuery("select tour_type from tour_master where tour_id='$sq_group_tour[tour_id]'"));
$download_url = preg_replace('/(\/+)/','/',$sq_traveler_info['id_proof_url']);
$download_url = BASE_URL.str_replace('../', '', $download_url);
$pan_url= preg_replace('/(\/+)/','/',$sq_traveler_info['pan_card_url']);
$pan_url = BASE_URL.str_replace('../', '', $pan_url); 
$pan_url3= preg_replace('/(\/+)/','/',$sq_traveler_info['pan_card_url3']);
$pan_url3 = BASE_URL.str_replace('../', '', $pan_url3); 
$pan_url4= preg_replace('/(\/+)/','/',$sq_traveler_info['pan_card_url4']);
$pan_url4 = BASE_URL.str_replace('../', '', $pan_url4);
$driving_license_url= preg_replace('/(\/+)/','/',$sq_traveler_info['driving_license']);
$driving_license_url = BASE_URL.str_replace('../', '', $driving_license_url);

$bg = ($sq_traveler_info['status']=="Cancel") ? "danger" : "";
?>

<div class="mg_tp_20"></div>
<h3 class="editor_title">ID Proof Information</h3>
<div class="panel panel-default panel-body">
<form id="frm_save">
  <div class="row mg_tp_20">
      <input type="hidden" name="traveler_id" id="traveler_id" value="<?php echo $traveler_id; ?>">
      <input type="hidden" name="tour_type" id="tour_type" value="<?php echo $sq_tour['tour_type']; ?>">
      <div class="col-md-3 col-sm-4 mg_bt_10_xs">
        <input type="text" name="passport_no" onchange="validate_passport(this.id);" id="passport_no" class="form-control" value="<?= $sq_traveler_info['passport_no'] ?>" placeholder="Passport No" title="Passport No" style="text-transform: uppercase;">
      </div>
      <div class="col-md-4 col-sm-4 mg_bt_10_xs">
        <input type="text" name="issue_date" id="issue_date" title="Issue Date" class="form-control" value="<?= ($sq_traveler_info['passport_issue_date'] == "1970-01-01" || $sq_traveler_info['passport_issue_date'] == "0000-00-00") ? date('d-m-Y') : get_date_user($sq_traveler_info['passport_issue_date']) ?>" onchange="get_to_date(this.id,'expiry_date');" placeholder="Issue Date">
      </div>
      <div class="col-md-4 col-sm-4 mg_bt_10_xs">
        <input type="text" name="expiry_date" id="expiry_date" title="Expiry Date" class="form-control" value="<?= ($sq_traveler_info['passport_expiry_date'] == "1970-01-01" || $sq_traveler_info['passport_expiry_date'] == "0000-00-00") ? date('d-m-Y') : get_date_user($sq_traveler_info['passport_expiry_date']) ?>"  placeholder="Expiry Date" onchange="validate_validDate('issue_date','expiry_date');">
      </div>
      <div class="col-md-2 col-sm-6 mg_tp_20 text_left_xs" style="display:flex;align-items:flex-start;gap:5px;">
          <div class="div-upload" style="margin-bottom: 5px;flex:1;" id="div_upload_button">
              <div id="id_proof_upload_g" class="upload-button1" style="white-space:nowrap;overflow:hidden;"><span style="font-size:9px;">Passport Copy</span></div>
              <span id="id_proof_status1" ></span>
              <ul id="files" ></ul>
              <input type="hidden" id="txt_id_proof_upload_dir1" name="txt_id_proof_upload_dir1" value="<?= $sq_traveler_info['id_proof_url'] ?>">
          </div>
          <?php if($sq_traveler_info['id_proof_url']!=""){ ?>
          <a href="<?= $download_url ?>" class="btn btn-info ico_center" title="Download Passport Copy" style="padding: 8px 21px;border-radius:20px;background-color:<?= $theme_color ?>;" download><i class="fa fa-download"></i></a>
          <?php } ?>
      </div>   
      <div class="col-md-2 col-sm-6 mg_tp_20 text_left_xs" style="display:flex;align-items:flex-start;gap:5px;">
        <div class="div-upload" style="margin-bottom: 5px;flex:1;" id="div_upload_button">
            <div id="pan_card_upload_g" class="upload-button1" style="white-space:nowrap;overflow:hidden;"><span style="font-size:11px;">PAN Card</span></div>
            <span id="pan_card_status1" ></span>
            <ul id="files" ></ul>
            <input type="hidden" id="txt_pan_card_upload_dir1" name="txt_pan_card_upload_dir1" value="<?= $sq_traveler_info['pan_card_url'] ?>">
        </div>
        <?php if($sq_traveler_info['pan_card_url']!=""){ ?>
        <a href="<?= $pan_url ?>" class="btn btn-info ico_center" title="Download PAN Card" style="padding: 8px 21px;border-radius:20px;background-color:<?= $theme_color ?>;" download><i class="fa fa-download"></i></a>
        <?php } ?>
      </div> 
        <div class="col-md-2 col-sm-6 mg_tp_20 text_left_xs" style="display:flex;align-items:flex-start;gap:5px;">
          <div class="div-upload" style="margin-bottom: 5px;flex:1;" id="div_upload_button">
            <div id="pan_card_upload3" class="upload-button1" style="white-space:nowrap;overflow:hidden;"><span style="font-size:11px;">Voter Id</span></div>
              <span id="pan_card_status3"></span>
              <ul id="files" ></ul>
            <input type="hidden" id="txt_pan_card_upload_dir3" name="txt_pan_card_upload_dir3" value="<?= $sq_traveler_info['pan_card_url3'] ?>">
          </div>
          <?php if($sq_traveler_info['pan_card_url3']!=""): ?>
          <a href="<?= $pan_url3 ?>" class="btn btn-info ico_center" title="Download Voter Id" style="padding: 8px 21px;border-radius:20px;background-color:<?= $theme_color ?>;" download><i class="fa fa-download"></i></a>
          <?php endif; ?>
        </div>
        <div class="col-md-2 col-sm-6 mg_tp_20 text_left_xs" style="display:flex;align-items:flex-start;gap:5px;">
          <div class="div-upload" style="margin-bottom: 5px;flex:1;" id="div_upload_button">
            <div id="pan_card_upload4" class="upload-button1" style="white-space:nowrap;overflow:hidden;"><span style="font-size:9px;">Aadhar Card</span></div>
              <span id="pan_card_status4"></span>
              <ul id="files" ></ul>
            <input type="hidden" id="txt_pan_card_upload_dir4" name="txt_pan_card_upload_dir4" value="<?= $sq_traveler_info['pan_card_url4'] ?>">
          </div>
          <?php if($sq_traveler_info['pan_card_url4']!=""): ?>
          <a href="<?= $pan_url4 ?>" class="btn btn-info ico_center" title="Download Aadhar Card" style="padding: 8px 21px;border-radius:20px;background-color:<?= $theme_color ?>;" download><i class="fa fa-download"></i></a>
          <?php endif; ?>
        </div>
        <div class="col-md-2 col-sm-6 mg_tp_20 text_left_xs" style="display:flex;align-items:flex-start;gap:5px;">
          <div class="div-upload" style="margin-bottom: 5px;flex:1;" id="div_upload_button">
            <div id="driving_license_upload" class="upload-button1" style="white-space:nowrap;overflow:hidden;"><span style="font-size:9px;">Driving License</span></div>
              <span id="driving_license_status"></span>
              <ul id="files" ></ul>
            <input type="hidden" id="txt_driving_license_upload_dir" name="txt_driving_license_upload_dir" value="<?= $sq_traveler_info['driving_license'] ?>">
          </div>
          <?php if($sq_traveler_info['driving_license']!=""): ?>
          <a href="<?= $driving_license_url ?>" class="btn btn-info ico_center" title="Download Driving License" style="padding: 8px 21px;border-radius:20px;background-color:<?= $theme_color ?>;" download><i class="fa fa-download"></i></a>
          <?php endif; ?>
        </div>
  </div>
  <div class="row mg_tp_10">
    <div class="col-md-12">
			<button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note: Size upto 5MB. Only pdf, jpg, png files are allowed"><i class="fa fa-question-circle"></i></button>
    </div>
  </div>
  <div class="row mg_tp_20">
    <div class="col-md-12 text-center">
      <button id="btn_save" class="btn btn-sm btn-success"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
    </div>
  </div>

</form> 
</div>
<script type="text/javascript">
$('#issue_date,#expiry_date').datetimepicker({ timepicker:false, format:'d-m-Y' });

function upload_tour_id_proof(doc_name)
{
    var traveler_id = $('#traveler_id').val();
    var id_proof_url = $('#txt_id_proof_upload_dir1').val();

    if(traveler_id==""){
        error_msg_alert('Please select traveler to upload his id proof!');
        return false;
    }

    var base_url = $('#base_url').val();

    $.ajax({
        type:'post',
        url: base_url+'controller/id_proof/group_tour_id_proof_upload.php',
        data:{ traveler_id : traveler_id, id_proof_url : id_proof_url },
        success:function(result){
            msg_alert(doc_name + ' uploaded successfully!');
        }
    });
}

function upload_tour_pan_card(doc_name)
{
    var traveler_id = $('#traveler_id').val();
    var id_proof_url = $('#txt_pan_card_upload_dir1').val();
    var id_proof_url3 = $('#txt_pan_card_upload_dir3').val();
    var id_proof_url4 = $('#txt_pan_card_upload_dir4').val();
    var driving_license = $('#txt_driving_license_upload_dir').val();

    if(traveler_id==""){
        error_msg_alert('Please select traveler to upload his pan card!');
        return false;
    }

    var base_url = $('#base_url').val();

    $.ajax({
        type:'post',
        url: base_url+'controller/id_proof/group_tour_pan_card_upload.php',
        data:{ traveler_id : traveler_id, id_proof_url : id_proof_url,id_proof_url3:id_proof_url3,id_proof_url4:id_proof_url4,driving_license:driving_license },
        success:function(result){
            msg_alert(doc_name + ' uploaded successfully!');
            //traveler_id_proof_info_reflect();
        }
    });
}

pan_card_upload1();

function pan_card_upload1()
{
    var type="pan_card";
    var btnUpload=$('#pan_card_upload_g');
    $(btnUpload).find('span').text('PAN Card');
    var status=$('#pan_card_status1');
    new AjaxUpload(btnUpload, {
      action: 'bookings/group_booking/id_proof/upload_pan_card_file.php',
      name: 'uploadfile1',
      onSubmit: function(file, ext){

         var tour_id = $("#cmb_tour_id").val();
          var id_proof_url = $("#txt_pan_card_upload_dir1").val();
          
          if(tour_id=='')
          {
            error_msg_alert('Please select tour name.');
            return false;
          }



         if (! (ext && /^(jpg|png|jpeg|pdf)$/.test(ext))){ 
            // extension is not allowed 
            error_msg_alert('Only JPG, PNG or pdf files are allowed');
            return false;
        }
        $(btnUpload).find('span').text('Uploading...');
      },
      onComplete: function(file, response){
        //On completion clear the status
        status.text('');
        var response1 = response.split('--');
        //Add uploaded file to list
        if(response1[0]=="error"){     
          error_msg_alert("File size exceeds");    
          $(btnUpload).find('span').text('PAN Card');
          return false;         
        }else if(response1[0]=="success"){ 
          document.getElementById("txt_pan_card_upload_dir1").value = response1[1];
          $(btnUpload).find('span').text('Uploaded');
          upload_tour_pan_card('PAN Card');
        }else{
          error_msg_alert("File not uploaded");    
          $(btnUpload).find('span').text('PAN Card');
          return false;
        }
      }
    });

}
pan_card_upload3();
function pan_card_upload3()
{
    var type="pan_card";
    var btnUpload=$('#pan_card_upload3');
    $(btnUpload).find('span').text('Voter Id');
    var status=$('#pan_card_status3');
    new AjaxUpload(btnUpload, {
      action: 'bookings/group_booking/id_proof/upload_pan_card_file3.php',
      name: 'uploadfile1',
      onSubmit: function(file, ext){

        status.text('');
        var tour_id = $("#pcmb_traveler_id").val();
        var id_proof_url = $("#txt_pan_card_upload_dir3").val();
        if(tour_id=='')
        {
          error_msg_alert('Please select booking!');
          return false;
        }
        if (! (ext && /^(jpg|png|jpeg|pdf)$/.test(ext))){ 
            // extension is not allowed 
            error_msg_alert('Only JPG, PNG or PDF files are allowed');
            return false;
        }
        $(btnUpload).find('span').text('Uploading...');
      },
      onComplete: function(file, response){
        //On completion clear the status
        status.text('');
        var response1 = response.split('--');
        //Add uploaded file to list
        if(response1[0]=="error"){     
          error_msg_alert("File size exceeds");    
          $(btnUpload).find('span').text('Voter Id');
          return false;         
        }else if(response1[0]=="success"){ 
          document.getElementById("txt_pan_card_upload_dir3").value = response1[1];
          $(btnUpload).find('span').text('Uploaded');
          upload_tour_pan_card('Voter Id');
        }else{
          error_msg_alert("File not uploaded");    
          $(btnUpload).find('span').text('Voter Id');
          return false;
        }
      }
    });
}
pan_card_upload4();
function pan_card_upload4()
{
    var type="pan_card";
    var btnUpload=$('#pan_card_upload4');
    $(btnUpload).find('span').html('<span style="font-size:11px;">Aadhar Card</span>');
    var status=$('#pan_card_status4');
    new AjaxUpload(btnUpload, {
      action: 'bookings/group_booking/id_proof/upload_pan_card_file4.php',
      name: 'uploadfile1',
      onSubmit: function(file, ext){

        status.text('');
        var tour_id = $("#pcmb_traveler_id").val();
        var id_proof_url = $("#txt_pan_card_upload_dir4").val();
        if(tour_id=='')
        {
          error_msg_alert('Please select booking!');
          return false;
        }
        if (! (ext && /^(jpg|png|jpeg|pdf)$/.test(ext))){ 
            // extension is not allowed 
            error_msg_alert('Only JPG, PNG or PDF files are allowed');
            return false;
        }
        $(btnUpload).find('span').text('Uploading...');
      },
      onComplete: function(file, response){
        //On completion clear the status
        status.text('');
        var response1 = response.split('--');
        //Add uploaded file to list
        if(response1[0]=="error"){     
          error_msg_alert("File size exceeds");    
          $(btnUpload).find('span').html('<span style="font-size:11px;">Aadhar Card</span>');
          return false;         
        }else if(response1[0]=="success"){ 
          document.getElementById("txt_pan_card_upload_dir4").value = response1[1];
          $(btnUpload).find('span').text('Uploaded');
          upload_tour_pan_card('Aadhar Card');
        }else{
          error_msg_alert("File not uploaded");    
          $(btnUpload).find('span').html('<span style="font-size:11px;">Aadhar Card</span>');
          return false;
        }
      }
    });
}

driving_license_upload();
function driving_license_upload()
{
    var type="driving_license";
    var btnUpload=$('#driving_license_upload');
    $(btnUpload).find('span').html('<span style="font-size:11px;">Driving License</span>');
    var status=$('#driving_license_status');
    new AjaxUpload(btnUpload, {
      action: 'bookings/group_booking/id_proof/upload_driving_license.php',
      name: 'uploadfile1',
      onSubmit: function(file, ext){

        status.text('');
        var tour_id = $("#pcmb_traveler_id").val();
        var id_proof_url = $("#txt_driving_license_upload_dir").val();
        if(tour_id=='')
        {
          error_msg_alert('Please select booking!');
          return false;
        }
        if (! (ext && /^(jpg|png|jpeg|pdf)$/.test(ext))){ 
            // extension is not allowed 
            error_msg_alert('Only JPG, PNG or PDF files are allowed');
            return false;
        }
        $(btnUpload).find('span').text('Uploading...');
      },
      onComplete: function(file, response){
        //On completion clear the status
        status.text('');
        var response1 = response.split('--');
        //Add uploaded file to list
        if(response1[0]=="error"){     
          error_msg_alert("File size exceeds");    
          $(btnUpload).find('span').html('<span style="font-size:11px;">Driving License</span>');
          return false;         
        }else if(response1[0]=="success"){ 
          document.getElementById("txt_driving_license_upload_dir").value = response1[1];
          $(btnUpload).find('span').text('Uploaded');
          upload_tour_pan_card('Driving License');
        }else{
          error_msg_alert("File not uploaded");    
          $(btnUpload).find('span').html('<span style="font-size:11px;">Driving License</span>');
          return false;
        }
      }
    });
}

function id_proof_upload1()
{
    var type="id_proof";
    var btnUpload=$('#id_proof_upload_g');
      $(btnUpload).find('span').html('<span style="font-size:11px;">Passport Copy</span>');
    var status=$('#id_proof_status1');
    new AjaxUpload(btnUpload, {
      action: 'bookings/group_booking/id_proof/upload_id_proof_file.php',
      name: 'uploadfile',
      onSubmit: function(file, ext){

          var tour_id = $("#cmb_tour_id").val();
          var id_proof_url = $("#txt_id_proof_upload_dir1").val();
          
          if(tour_id==''){
            error_msg_alert('Please select tour name.');
            return false;
          }
          if (! (ext && /^(jpg|png|jpeg|pdf)$/.test(ext))){
            // extension is not allowed 
            error_msg_alert('Only JPG, PNG or PDF files are allowed');
            return false;
          }
          $(btnUpload).find('span').text('Uploading...');
      },
      onComplete: function(file, response){
        //On completion clear the status
        status.text('');
        var response1 = response.split('--');
        //Add uploaded file to list
        if(response1[0]=="error"){     
          error_msg_alert("File size exceeds");    
          $(btnUpload).find('span').html('<span style="font-size:11px;">Passport Copy</span>');
          return false;         
        }else if(response1[0]=="success"){ 
          document.getElementById("txt_id_proof_upload_dir1").value = response1[1];
          $(btnUpload).find('span').text('Uploaded');
          upload_tour_id_proof('Passport Copy');
        }else{
          error_msg_alert("File not uploaded");    
          $(btnUpload).find('span').html('<span style="font-size:11px;">Passport Copy</span>');
          return false;
        }
      }
    });
}
id_proof_upload1();

$('#frm_save').validate({
    rules:{
      },
    submitHandler:function(){
            
      var tour_type = $('#tour_type').val();
      var passport_no = $('#passport_no').val();
      var issue_date = $('#issue_date').val();
      var expiry_date = $('#expiry_date').val();
      var traveler_id = $('#traveler_id').val();

      $('#btn_save').button('loading');
      $.ajax({
        type: 'post',
        url: base_url()+'controller/passport_id_details/info_save.php',
        data:{ passport_no : passport_no, issue_date : issue_date, expiry_date : expiry_date, traveler_id : traveler_id },
        success: function(result){
          msg_alert(result);
          $('#btn_save').button('reset');
          traveler_id_proof_info_reflect();
        }
    });
  }
}); 
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>