<?php
include_once('../../../../../../model/model.php');
$entry_id = $_POST['entry_id'];
$sq_traveler_info = mysqli_fetch_assoc(mysqlQuery("select * from visa_master_entries where entry_id='$entry_id'"));
$download_url = preg_replace('/(\/+)/','/',$sq_traveler_info['id_proof_url']);
$download_url = BASE_URL.str_replace('../', '', $download_url);
$download_urlpan_card = preg_replace('/(\/+)/','/',$sq_traveler_info['pan_card_url']);
$download_urlpan_card = BASE_URL.str_replace('../', '', $download_urlpan_card);
$download_urlpan_card3 = preg_replace('/(\/+)/','/',$sq_traveler_info['pan_card_url3']);
$download_urlpan_card3 = BASE_URL.str_replace('../', '', $download_urlpan_card3);
$download_urlpan_card4 = preg_replace('/(\/+)/','/',$sq_traveler_info['pan_card_url4']);
$download_urlpan_card4 = BASE_URL.str_replace('../', '', $download_urlpan_card4);
$driving_license_url = preg_replace('/(\/+)/','/',$sq_traveler_info['driving_license']);
$driving_license_url = BASE_URL.str_replace('../', '', $driving_license_url);
?>
<div class="mg_tp_20"></div>
<h3 class="editor_title">id proof Information</h3>
<div class="panel panel-default panel-body">
    <form id="frm_save">
    <div class="row mg_tp_20">
      <input type="hidden" name="traveler_id" id="traveler_id" value="<?php echo $entry_id; ?>">
      <div class="col-md-4">
        <input type="text" name="passport_no" id="passport_no" title="Passport No" class="form-control"  onchange="validate_passport(this.id);" value="<?= $sq_traveler_info['passport_id'] ?>" placeholder="Passport No" title="Passport No" style="text-transform: uppercase;">
      </div>
      <div class="col-md-4">
        <input type="text" name="issue_date" id="issue_date" title="Issue Date" class="form-control" value="<?= ($sq_traveler_info['passport_issue_date'] == "1970-01-01" || $sq_traveler_info['passport_issue_date'] == "0000-00-00") ? date('d-m-Y'): get_date_user($sq_traveler_info['issue_date']) ?>"  placeholder="Issue Date"  title="Issue Date" onchange="get_to_date(this.id,'expiry_date');">
      </div>
      <div class="col-md-4">
        <input type="text" name="expiry_date" id="expiry_date" title="Expiry Date" class="form-control" value="<?= ($sq_traveler_info['passport_expiry_date'] == "1970-01-01" || $sq_traveler_info['passport_expiry_date'] == "0000-00-00") ? date('d-m-Y'): get_date_user($sq_traveler_info['expiry_date']) ?>"  placeholder="Expiry Date" title="Expiry Date" onchange="validate_validDate('issue_date','expiry_date');">
        </div>
        <div class="col-md-2 col-sm-6 mg_tp_20 text_left_xs" style="display:flex;align-items:flex-start;gap:5px;">
            <div class="div-upload" style="margin-bottom: 5px;flex:1;" id="div_upload_button">
                <div id="id_proof_upload" class="upload-button1" style="white-space:nowrap;overflow:hidden;"><span style="font-size:9px;">Passport Copy</span></div>
                <span id="id_proof_status" ></span>
                <ul id="files" ></ul>
                <input type="hidden" id="txt_id_proof_upload_dir" name="txt_id_proof_upload_dir" value="<?= $sq_traveler_info['id_proof_url'] ?>">
            </div>
            <?php if($sq_traveler_info['id_proof_url']!=""): ?>
            <a href="<?= $download_url ?>" class="btn btn-info ico_center" title="Download Passport Copy" style="padding: 8px 21px;border-radius:20px;background-color:<?= $theme_color ?>;" download><i class="fa fa-download i-download"></i></a>
            <?php endif; ?>
        </div>
        
        <div class="col-md-2 col-sm-6 mg_tp_20 text_left_xs" style="display:flex;align-items:flex-start;gap:5px;">
          <div class="div-upload" style="margin-bottom: 5px;flex:1;" id="div_upload_button">
            <div id="pan_card_upload" class="upload-button1" style="white-space:nowrap;overflow:hidden;"><span style="font-size:11px;">PAN Card</span></div>
              <span id="pan_card_status" ></span>
              <ul id="files" ></ul>
            <input type="hidden" id="txt_pan_card_upload_dir" name="txt_pan_card_upload_dir" value="<?= $sq_traveler_info['pan_card_url'] ?>">
          </div>
          <?php if($sq_traveler_info['pan_card_url']!=""): ?>
          <a href="<?= $download_urlpan_card ?>" class="btn btn-info ico_center" title="Download PAN Card" style="padding: 8px 21px;border-radius:20px;background-color:<?= $theme_color ?>;" download><i class="fa fa-download i-download"></i></a>
          <?php endif; ?>
        </div>
        <div class="col-md-2 col-sm-6 mg_tp_20 text_left_xs" style="display:flex;align-items:flex-start;gap:5px;">
          <div class="div-upload" style="margin-bottom: 5px;flex:1;" id="div_upload_button">
            <div id="pan_card_upload3" class="upload-button1" style="white-space:nowrap;overflow:hidden;"><span style="font-size:11px;">Voter Id</span></div>
              <span id="pan_card_status3"></span>
              <ul id="files" ></ul>
            <input type="hidden" id="txt_pan_card_upload_dir3" name="txt_pan_card_upload_dir3" value="<?= $sq_traveler_info['pan_card_url3'] ?>">
          </div>
          <?php if($sq_traveler_info['pan_card_url3']!=""): ?>
          <a href="<?= $download_urlpan_card3 ?>" class="btn btn-info ico_center" title="Download Voter Id" style="padding: 8px 21px;border-radius:20px;background-color:<?= $theme_color ?>;" download><i class="fa fa-download i-download"></i></a>
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
          <a href="<?= $download_urlpan_card4 ?>" class="btn btn-info ico_center" title="Download Aadhar Card" style="padding: 8px 21px;border-radius:20px;background-color:<?= $theme_color ?>;" download><i class="fa fa-download i-download"></i></a>
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
          <a href="<?= $driving_license_url ?>" class="btn btn-info ico_center" title="Download Driving License" style="padding: 8px 21px;border-radius:20px;background-color:<?= $theme_color ?>;" download><i class="fa fa-download i-download"></i></a>
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
          <button id="btn_save2" class="btn btn-sm btn-success"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
        </div>
    </div>
  </form> 
</div>
<script type="text/javascript">
$('#issue_date,#expiry_date').datetimepicker({ timepicker:false, format:'d-m-Y' });
$('#frm_save').validate({
  rules:{
  },
  submitHandler:function(){

            var passport_no = $('#passport_no').val();
            var issue_date = $('#issue_date').val();
            var expiry_date = $('#expiry_date').val();
            var entry_id = $('#traveler_id').val();
            var base_url = $('#base_url').val();

            $('#btn_save2').button('loading');
            $.ajax({
              type: 'post',
              url: base_url+'controller/passport_id_details/visa_passport_details.php',
              data:{ passport_no : passport_no, issue_date : issue_date, expiry_date : expiry_date, entry_id : entry_id },
              success: function(result){                
                msg_alert(result);
                $('#btn_save2').button('reset');
                traveler_id_proof_info_reflect();
              }
          });

        }
}); 
id_proof_upload();
function id_proof_upload()
{
    var type="id_proof";
    var btnUpload=$('#id_proof_upload');
    $(btnUpload).find('span').html('<span style="font-size:11px;">Passport Copy</span>');
    var status=$('#id_proof_status');
    new AjaxUpload(btnUpload, {
      action: 'bookings/visa/id_proof/upload_id_proof_file.php',
      name: 'uploadfile',
      onSubmit: function(file, ext){

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
          document.getElementById("txt_id_proof_upload_dir").value = response1[1];
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

function upload_tour_id_proof(doc_name)
{
    var entry_id = $('#traveler_id').val();
    var id_proof_url = $('#txt_id_proof_upload_dir').val();
    var base_url = $('#base_url').val();

    $.ajax({
        type:'post',
        url: base_url+'controller/id_proof/visa_ticket_id_proof_upload.php',
        data:{ entry_id : entry_id, id_proof_url : id_proof_url },
        success:function(result){
            msg_alert(doc_name + ' uploaded successfully!');
        }
    });
}

pan_card_upload();
function pan_card_upload()
{
    var type="pan_card";
    var btnUpload=$('#pan_card_upload');
    $(btnUpload).find('span').text('PAN Card');
    var status=$('#pan_card_status');
    new AjaxUpload(btnUpload, {
      action: 'bookings/visa/id_proof/upload_pan_card_file.php',
      name: 'uploadfile1',
      onSubmit: function(file, ext){

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
          $(btnUpload).find('span').text('PAN Card');
          return false;         
        }else if(response1[0]=="success"){ 
          document.getElementById("txt_pan_card_upload_dir").value = response1[1];
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
      action: 'bookings/visa/id_proof/upload_pan_card_file3.php',
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
      action: 'bookings/visa/id_proof/upload_pan_card_file4.php',
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
      action: 'bookings/visa/id_proof/upload_driving_license.php',
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

function upload_tour_pan_card(doc_name)
{
    var entry_id = $('#traveler_id').val();
    var id_proof_url = $('#txt_pan_card_upload_dir').val();
    var id_proof_url3 = $('#txt_pan_card_upload_dir3').val();
    var id_proof_url4 = $('#txt_pan_card_upload_dir4').val();
    var driving_license = $('#txt_driving_license_upload_dir').val();
    var base_url = $('#base_url').val();

    $.ajax({
        type:'post',
        url: base_url+'controller/id_proof/visa_ticket_pan_card_upload.php',
        data:{ entry_id : entry_id, id_proof_url : id_proof_url,id_proof_url3:id_proof_url3,id_proof_url4:id_proof_url4,driving_license:driving_license },
        success:function(result){
            msg_alert(doc_name + ' uploaded successfully!');
        }
    });
}
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>