<?php
include "../../model/model.php";
$dest_id = isset($_POST['dest_id']) ? $_POST['dest_id'] : 0;
$spa = isset($_POST['spa']) ? $_POST['spa'] : '';
$dwp = isset($_POST['dwp']) ? $_POST['dwp'] : '';
$ovs = isset($_POST['ovs']) ? $_POST['ovs'] : '';
$dayp = isset($_POST['dayp']) ? $_POST['dayp'] : '';
$dest_id = mysqlREString($dest_id);
$sq_itinerary_c = mysqli_num_rows(mysqlQuery("select * from itinerary_master where dest_id='$dest_id'"));
$row_dest = null;
if ($dest_id !== '' && $dest_id !== '0') {
    $row_dest = mysqli_fetch_assoc(mysqlQuery("select * from destination_master where dest_id = '$dest_id'"));
}
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

        <h4 class="modal-title" id="myModalLabel">Add Itinerary for <?= htmlspecialchars($dayp, ENT_QUOTES) ?></h4>

      </div>

      <div class="modal-body">
      <input type="hidden" id="spa" value='<?= htmlspecialchars($spa, ENT_QUOTES) ?>'/>
      <input type="hidden" id="dwp" value='<?= htmlspecialchars($dwp, ENT_QUOTES) ?>'/>
      <input type="hidden" id="ovs" value='<?= htmlspecialchars($ovs, ENT_QUOTES) ?>'/>
      <input type="hidden" id="base_url" value="<?= BASE_URL ?>"/>
      <input type="hidden" id="itin_locked_dest_id" value="<?= htmlspecialchars((string)$dest_id, ENT_QUOTES) ?>"/>
        <div class="row">
          <div class="text-left col-md-3 col-sm-6">
            <?php if ($row_dest) { ?>
            <select id="dest_ids1" name="dest_names1" title="Destination" class="form-control" style="width:100%" disabled>
              <option value="<?php echo (int)$row_dest['dest_id']; ?>" selected><?php echo htmlspecialchars($row_dest['dest_name'], ENT_QUOTES); ?></option>
            </select>
           
            <?php } else { ?>
            <select id="dest_ids1" name="dest_names1" title="Select Destination" class="form-control" style="width:100%" required>
              <option value="">*Destination</option>
            </select>
            <?php } ?>
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
                    <td class="col-md-3 no-pad" style="padding-left: 5px !important;"><input type="text" id="itin_modal_special_attaraction<?= $count?>" onchange="validate_spaces(this.id);validate_spattration(this.id);" name="itin_modal_special_attaraction" class="form-control" placeholder="*Special Attraction" title="Special Attraction" value="<?= htmlspecialchars($row_itinerary['special_attraction'], ENT_QUOTES) ?>"></td>
                    <td class="col-md-5 no-pad" style="padding-left: 5px !important;"><textarea id="itin_modal_day_program<?= $count?>" name="itin_modal_day_program" class="form-control" rows="2" placeholder="*Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" title="Day-wise Program"><?= htmlspecialchars($row_itinerary['daywise_program'], ENT_QUOTES) ?></textarea></td>
                    <td class="col-md-2 no-pad" style="padding-left: 5px !important;"><input type="text" id="itin_modal_overnight_stay<?= $count?>" name="itin_modal_overnight_stay" onchange="validate_spaces(this.id);validate_onstay(this.id);" class="form-control" placeholder="*Overnight Stay" title="Overnight Stay" value="<?= htmlspecialchars($row_itinerary['overnight_stay'], ENT_QUOTES) ?>"></td>
                    <td class="col-md-1 no-pad" style="padding-left: 5px !important; width: 120px;">
                        <?php if (!empty($row_itinerary['itinerary_image']) && trim($row_itinerary['itinerary_image']) !== '' && trim($row_itinerary['itinerary_image']) !== 'NULL') { ?>
                            <div style="margin-top: 5px;">
                                <div style="height:80px; max-height: 80px; overflow:hidden; position: relative; width: 80px; border: 2px solid #28a745; border-radius: 8px; background-color: #f8f9fa;">
                                    <img src="<?php 
                                        $image_path = trim($row_itinerary['itinerary_image']);
                                        if (strpos($image_path, 'http') === 0) {
                                            echo htmlspecialchars($image_path, ENT_QUOTES);
                                        } else {
                                            $project_base_url = str_replace('/crm/', '/', BASE_URL);
                                            $project_base_url = rtrim($project_base_url, '/');
                                            $image_path = ltrim($image_path, '/');
                                            $final_url = $project_base_url . '/' . $image_path;
                                            echo htmlspecialchars($final_url, ENT_QUOTES);
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
                        <input type="hidden" id="itinerary_image_<?= $count?>" name="itinerary_image" value="<?= htmlspecialchars($row_itinerary['itinerary_image'] ?? '', ENT_QUOTES) ?>">
                    </td>
                    <td class="hidden"><input type="text" id="itin_modal_entry_id<?= $count?>" name="itin_modal_entry_id" class="form-control" value="<?= htmlspecialchars($row_itinerary['entry_id'], ENT_QUOTES) ?>"></td>
                      <td>
              <button type="button" class="btn btn-sm btn-success itin-modal-add-btn" id="btn_itin_add_<?= $count ?>" style="margin-top: 35px;"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</button>

                    </td>
                    </tr>
                    <?php
                } ?>
                </tbody>
            </table>
            </div>
        <?php }
        else{
          if($dest_id != '' && $dest_id != 0){ ?>
            <div class="col-md-12 col-sm-6 col-xs-12 mg_tp_10">
            <?php echo '<h4 class="no-pad">Itinerary not added for this destination! <a href="'.BASE_URL.'view/other_masters/index.php" target="_blank" title="Add Itinerary"><i class="fa fa-plus"></i>&nbsp;&nbsp;Itinerary</a></h4> '; ?>
            </div>
        <?php }?>
          <div class="col-md-12 col-sm-6 col-xs-12 mg_tp_10"></div>
        <?php }?>
        </div>

      </div>      

    </div>

  </div>

</div>

</form>

<script>
(function () {
    $('#itinerary_detail_modal').modal('show');
    if ($('#dest_ids1').length && !$('#dest_ids1').prop('disabled')) {
        $('#dest_ids1').select2();
    } else if ($('#dest_ids1').length) {
        $('#dest_ids1').select2({ disabled: true });
    }

    function applySelectedItineraryRow() {
        var sq_itinerary_c = <?= (int)$sq_itinerary_c ?>;
        if (sq_itinerary_c == 0) {
            var dynamicCount = $('#sq_itinerary_c1').val();
            if (dynamicCount !== undefined && dynamicCount !== null && dynamicCount !== '') {
                sq_itinerary_c = parseInt(dynamicCount, 10) || 0;
            }
        }
        if (sq_itinerary_c == 0) {
            error_msg_alert("You need to add itinerary for this destination first!");
            return false;
        }

        var dest_id = $('#itin_locked_dest_id').val() || $('#dest_ids1').val();
        var spa = $('#spa').val();
        var dwp = $('#dwp').val();
        var ovs = $('#ovs').val();

        if (dest_id == '' || dest_id == 0) {
            error_msg_alert("Please select destination!");
            return false;
        }

        var table = document.getElementById("default_program_list");
        if (!table) {
            error_msg_alert("You need to add itinerary for this destination first!");
            return false;
        }
        var rowCount = table.rows.length;
        var count = 0;
        for (var i = 0; i < rowCount; i++) {
            var checkbox = table.rows[i].querySelector('input[type="checkbox"]');
            if (checkbox && checkbox.checked) {
                count++;
            }
        }
        if (parseInt(count, 10) != 1) {
            error_msg_alert("Please select one day program!");
            return false;
        }

        for (var i = 0; i < rowCount; i++) {
            var row = table.rows[i];
            var checkbox = row.querySelector('input[type="checkbox"]');
            if (!checkbox || !checkbox.checked) {
                continue;
            }

            var spInput = row.querySelector('input[name="itin_modal_special_attaraction"], input[id^="itin_modal_special_attaraction"]');
            var dwpInput = row.querySelector('textarea[name="itin_modal_day_program"], textarea[id^="itin_modal_day_program"]');
            var osInput = row.querySelector('input[name="itin_modal_overnight_stay"], input[id^="itin_modal_overnight_stay"]');
            var imgInput = row.querySelector('input[id^="itinerary_image_"]');

            var sp = spInput ? spInput.value : '';
            var dwp1 = dwpInput ? dwpInput.value : '';
            var os1 = osInput ? osInput.value : '';
            var img = imgInput ? imgInput.value : '';

            // Write only into package day fields (never into other pages)
            if (spa) { $('#' + spa).val(sp); }
            if (dwp) { $('#' + dwp).val(dwp1); }
            if (ovs) { $('#' + ovs).val(os1); }

            var dayId = '';
            if (spa.indexOf('special_attaraction') !== -1) {
                dayId = spa.split('special_attaraction')[1];
                dayId = dayId.replace(/-u$/, '');
            } else if (spa.indexOf('special_attraction') !== -1) {
                dayId = spa.split('special_attraction')[1];
                dayId = dayId.replace(/-u$/, '');
            } else {
                var match = spa.match(/(\d+)(?:-u)?$/);
                dayId = match ? match[1] : '';
            }

            if (dayId && img) {
                var imageKey = typeof resolveItineraryImageKeyFromSpa === 'function'
                    ? resolveItineraryImageKeyFromSpa(spa)
                    : dayId;
                window.selectedItineraryImage = {
                    dayId: dayId,
                    imageKey: imageKey,
                    spa: spa,
                    img: img
                };
            }
        }

        if (typeof applySelectedItineraryImagePreview === 'function') {
            applySelectedItineraryImagePreview();
        }
        $('#itinerary_detail_modal').modal('hide');
        setTimeout(function () {
            $('#div_itinerary_modal').empty();
        }, 300);
        return false;
    }

    $('#itinerary_detail_frm').off('submit.itineraryModal').on('submit.itineraryModal', function (e) {
        e.preventDefault();
        return applySelectedItineraryRow();
    });

    $(document).off('click.itineraryModalAdd', '#itinerary_detail_modal .itin-modal-add-btn').on('click.itineraryModalAdd', '#itinerary_detail_modal .itin-modal-add-btn', function (e) {
        e.preventDefault();
        applySelectedItineraryRow();
    });
})();
</script>
