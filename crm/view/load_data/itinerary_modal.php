<?php
include "../../model/model.php";
$dest_id = isset($_POST['dest_id']) ? $_POST['dest_id'] : 0;
$spa = $_POST['spa'];
$dwp = $_POST['dwp'];
$ovs = $_POST['ovs'];
$dayp = $_POST['dayp'];
$sq_itinerary_c = mysqli_num_rows(mysqlQuery("select * from itinerary_master where dest_id='$dest_id'"));
?>
     <style>
textarea.form-control {
    height: 120px !important;
}
#itinerary_detail_modal input.form-control {
  margin-top: 35px;
}


#itinerary_detail_modal label {
  margin-top: 45px;
}

</style>
   
<form id="itinerary_detail_frm">

<div class="modal fade" id="itinerary_detail_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">

  <div class="modal-dialog modal-lg" role="document" style="width:95% !important;">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <h4 class="modal-title" id="myModalLabel">Add Itinerary for <?= $dayp ?></h4>

      </div>

      <div class="modal-body">
      <input type="hidden" id="spa" value='<?=$spa ?>'/>
      <input type="hidden" id="dwp" value='<?=$dwp ?>'/>
      <input type="hidden" id="ovs" value='<?=$ovs ?>'/>
      <input type="hidden" id="base_url" value="<?= BASE_URL ?>"/>
        <div class="row">
          <div class="text-left col-md-3 col-sm-6">
            <select id="dest_ids1"  name="dest_names1" title="Select Destination" class="form-control" style="width:100%" onchange="get_dest_itinerary(this.id)" required> 
              <?php
              if($dest_id !='' && $dest_id !='0'){
              $row_dest = mysqli_fetch_assoc(mysqlQuery("select * from destination_master where dest_id = '$dest_id'"));
              ?>
              <option value="<?php echo $row_dest['dest_id']; ?>"><?php echo $row_dest['dest_name']; ?></option>
              <?php } ?>
              <option value="">*Destination</option>
              <?php 
              $sq_query = mysqlQuery("select * from destination_master where status != 'Inactive'"); 
              while($row_dest = mysqli_fetch_assoc($sq_query)){ ?>
                  <option value="<?php echo $row_dest['dest_id']; ?>"><?php echo $row_dest['dest_name']; ?></option>
                  <?php } ?>
            </select>
          </div>
        </div>
          <h5></h5>
        <div class="row" id="itinerary_data">
        <?php
        if($sq_itinerary_c >0){
        ?>
            <div class="col-md-12 col-sm-6 col-xs-12 mg_bt_10">
            <table style="width:100%" id="default_program_list" name="default_program_list" class="table mg_bt_0 table-bordered">
                <tbody>
                <?php
                $count = 0;
                $sq_itinerary = mysqlQuery("select * from itinerary_master where dest_id='$dest_id'");
                while($row_itinerary = mysqli_fetch_assoc($sq_itinerary)){
                    $count++;
                    ?>
                    <tr>
                    <td width="27px;" style="padding-right: 10px !important;"><input class="css-checkbox labelauty" id="chk_programd<?=$count?>" type="checkbox" style="display: none;"><label for="chk_programd<?= $count?>"><span class="labelauty-unchecked-image"></span><span class="labelauty-checked-image"></span></label></td>
                    <td width="20px;"><input maxlength="15" value="<?= $count?>" type="text" name="username" placeholder="Sr. No." class="form-control" disabled=""></td>
                    <td class="col-md-3 no-pad" style="padding-left: 5px !important;"><input type="text" id="special_attaraction<?= $count?>" onchange="validate_spaces(this.id);validate_spattration(this.id);" name="special_attaraction" class="form-control" placeholder="*Special Attraction" title="Special Attraction" value="<?= $row_itinerary['special_attraction']?>"></td>
                    <td class="col-md-5 no-pad" style="padding-left: 5px !important;"><textarea id="day_program<?= $count?>" name="day_program" class="form-control" rows="2" placeholder="*Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" title="Day-wise Program"><?=$row_itinerary['daywise_program']?></textarea></td>
                    <td class="col-md-2 no-pad" style="padding-left: 5px !important;"><input type="text" id="overnight_stay<?= $count?>" name="overnight_stay" onchange="validate_spaces(this.id);validate_onstay(this.id);" class="form-control" placeholder="*Overnight Stay" title="Overnight Stay" value="<?=$row_itinerary['overnight_stay']?>"></td>
                    <td class="col-md-1 no-pad" style="padding-left: 5px !important; width: 120px;">
                        <!-- Display existing image if available -->
                        <?php if (!empty($row_itinerary['itinerary_image']) && trim($row_itinerary['itinerary_image']) !== '' && trim($row_itinerary['itinerary_image']) !== 'NULL') { ?>
                            <div style="margin-top: 5px;">
                                <div style="height:80px; max-height: 80px; overflow:hidden; position: relative; width: 80px; border: 2px solid #28a745; border-radius: 8px; background-color: #f8f9fa;">
                                    <img src="<?php 
                                        $image_path = trim($row_itinerary['itinerary_image']);
                                        // Check if path already starts with http
                                        if (strpos($image_path, 'http') === 0) {
                                            echo $image_path;
                                        } else {
                                            // For itinerary images, use project root URL
                                            $project_base_url = str_replace('/crm/', '/', BASE_URL);
                                            $project_base_url = rtrim($project_base_url, '/');
                                            $image_path = ltrim($image_path, '/');
                                            $final_url = $project_base_url . '/' . $image_path;
                                            echo $final_url;
                                        }
                                    ?>" alt="Itinerary Image" 
                                         style="width:100%; height:100%; object-fit: cover; border-radius: 6px;"
                                         onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'text-align:center; padding:20px; color:#999;\'>No Image</div>';">
                                </div>
                                <small class="text-success">✓ Has Image</small>
                            </div>
                        <?php } else { ?>
                            <div style="margin-top: 5px; text-align: center; padding: 20px; color: #999; border: 1px dashed #ddd; border-radius: 4px;">
                                <i class="fa fa-image" style="font-size: 20px; margin-bottom: 5px;"></i><br>
                                <small>No Image</small>
                            </div>
                        <?php } ?>
                        <input type="hidden" id="itinerary_image_<?= $count?>" name="itinerary_image" value="<?= $row_itinerary['itinerary_image'] ?? '' ?>">
                    </td>
                    <td class="hidden"><input type="text" id="entry_id<?= $count?>" name="entry_id" class="form-control" value="<?=$row_itinerary['entry_id']?>"></td>
                    </tr>
                    <?php
                } ?>
                </tbody>
            </table>
            </div>
        <?php }
        else{
          if($dest_id != '' || $dest_id != 0){ ?>
            <div class="col-md-12 col-sm-6 col-xs-12 mg_tp_10">
            <?php echo '<h4 class="no-pad">Itinerary not added for this destination! <a href="'.BASE_URL.'view/other_masters/index.php" target="_blank" title="Add Itinerary"><i class="fa fa-plus"></i>&nbsp;&nbsp;Itinerary</a></h4> '; ?>
            </div>
        <?php }?>
          <div class="col-md-12 col-sm-6 col-xs-12 mg_tp_10"></div>
        <?php }?>
        </div>
          <div class="row mg_tp_10">
            <div class="col-xs-12 text-center">
              <button class="btn btn-sm btn-success" id="btn_update"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</button>
            </div>
          </div>



      </div>      

    </div>

  </div>

</div>

</form>

<script>
$('#itinerary_detail_modal').modal('show');
$('#dest_ids1').select2();
$('#itinerary_detail_frm').validate({
    rules:{
      dest_names1 : {required:true}
    },
    submitHandler:function(form){
      
        var sq_itinerary_c = <?= $sq_itinerary_c ?>;
        console.log('ITINERARY MODAL: sq_itinerary_c =', sq_itinerary_c);
        if(sq_itinerary_c != 0){
          var dest_id = $('#dest_ids1').val();
          var spa = $('#spa').val();
          var dwp = $('#dwp').val();
          var ovs = $('#ovs').val();

          console.log('ITINERARY MODAL: dest_id =', dest_id, 'spa =', spa, 'dwp =', dwp, 'ovs =', ovs);

          if(dest_id == '' || dest_id == 0){
            error_msg_alert("Please select destination!");
            return false;
          }
          var table = document.getElementById("default_program_list");
          var rowCount = table.rows.length;
          console.log('ITINERARY MODAL: Table found, rowCount =', rowCount);
          var count = 0;
          for(var i=0; i<rowCount; i++){
              var row = table.rows[i];
              var checkbox = row.cells[0].childNodes[0];
              console.log('ITINERARY MODAL: Row', i, 'checkbox checked =', checkbox.checked);
              if(checkbox.checked){
                  count++;
              }
          }
          console.log('ITINERARY MODAL: Total checked items =', count);
          if(parseInt(count) != 1){
              error_msg_alert("Please select one day program!");
              return false;
          }
          for(var i=0; i<rowCount; i++){
              var row = table.rows[i];
              if(row.cells[0].childNodes[0].checked){
                  console.log('ITINERARY MODAL: Processing selected row', i);

                  var sp = row.cells[2].childNodes[0].value;
                  var dwp1 = row.cells[3].childNodes[0].value;
                  var os1 = row.cells[4].childNodes[0].value;
                  
                  console.log('ITINERARY MODAL: Values - sp:', sp, 'dwp1:', dwp1, 'os1:', os1);
                  
                  // Get image path from hidden input
                  var imgInput = row.querySelector('input[id^="itinerary_image_"]');
                  var img = imgInput ? imgInput.value : '';
                  console.log('ITINERARY MODAL: Image input found:', imgInput, 'value:', img);
                  console.log('ITINERARY MODAL: Row cells count:', row.cells.length);
                  console.log('ITINERARY MODAL: All inputs in row:', row.querySelectorAll('input'));
                  
                  $('#'+spa).val(sp);
                  $('#'+dwp).val(dwp1);
                  $('#'+ovs).val(os1);
                  
                  console.log('ITINERARY MODAL: Set form values - spa:', spa, 'dwp:', dwp, 'ovs:', ovs);
                  
                  // Also copy the image to the package form
                  // Extract day number from spa parameter - handle different formats
                  var dayId = '';
                  if (spa.includes('special_attaraction')) {
                      dayId = spa.split('special_attaraction')[1];
                      // Remove any suffix like '-u' for update modal
                      dayId = dayId.replace(/-u$/, '');
                  } else if (spa.includes('special_attraction')) {
                      dayId = spa.split('special_attraction')[1];
                      dayId = dayId.replace(/-u$/, '');
                  } else {
                      // Fallback: try to extract number from the end
                      var match = spa.match(/(\d+)(?:-u)?$/);
                      dayId = match ? match[1] : '';
                  }
                  console.log('ITINERARY MODAL: dayId extracted:', dayId, 'from spa:', spa);
                  console.log('ITINERARY MODAL: img value:', img);
                  console.log('ITINERARY MODAL: All available preview_img elements:', $('[id^="preview_img_"]').map(function() { return this.id; }).get());
                  console.log('ITINERARY MODAL: All available day_image_preview elements:', $('[id^="day_image_preview_"]').map(function() { return this.id; }).get());
                  
                  if (dayId && img) {
                      // Store the image data for later use after modal closes
                      window.selectedItineraryImage = {
                          dayId: dayId,
                          img: img
                      };
                      console.log('ITINERARY MODAL: Stored image data for later processing:', window.selectedItineraryImage);
                  } else {
                      console.log('ITINERARY MODAL: Missing dayId or img - dayId:', dayId, 'img:', img);
                  }
              }
          }
          console.log('ITINERARY MODAL: Closing modal');
          $('#itinerary_detail_modal').modal('hide');
        }
        else{
          error_msg_alert("You need to add itinerary for this destination first!");
          return false;
        }
        
    }
});
</script>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>