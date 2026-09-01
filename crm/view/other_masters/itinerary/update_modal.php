<?php
include "../../../model/model.php";
$dest_id = $_POST['dest_id'];
?>

<style>
textarea.form-control {
    height: 120 !important;
}

</style>
<form id="itinerary_frm_update">

<div class="modal fade" id="itinerary_update_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" >

  <div class="modal-dialog modal-lg" role="document" style="width:95% !important;">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <h4 class="modal-title" id="myModalLabel">Update Itinerary</h4>

      </div>

      <div class="modal-body">
        <div class="row">
          <div class="text-left col-md-3 col-sm-6">
            <select id="dest_ids1"  name="dest_names1" title="Select Destination" class="form-control" onchange="check_dest_validation(this.id)" style="width:100%" disabled> 
              <?php
              $row_dest = mysqli_fetch_assoc(mysqlQuery("select * from destination_master where dest_id = '$dest_id'"));
              ?>
              <option value="<?php echo $row_dest['dest_id']; ?>"><?php echo $row_dest['dest_name']; ?></option>
            </select>
          </div>
<div class="text-left col-md-3 col-sm-6">
          <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Character limit for Special attraction is 85 characters, for Day-wise program is 2000 characters and for Overnight stay is 30 characters."><i class="fa fa-question-circle"></i></button> 
</div>
          <div class="col-xs-9 text-right text_center_xs">
              <button type="button" class="btn btn-excel btn-sm" title="Add row" onClick="addRow('default_program_list', '', 'itinerary')"><i class="fa fa-plus"></i></button>
          </div>
        </div>
        <div class="row mg_tp_10">
          <div class="col-sm-12">
		        
            <!-- <span style="color: red;" class="note" data-original-title="" title="">For saving daywise program keep checkbox selected!</span> -->
          </div>
        </div>
        <div class="row">
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
                      <td width="27px;" style="padding-right: 10px !important;"><input class="css-checkbox labelauty" id="chk_programd<?=$count?>" type="checkbox" checked style="display: none;"><label for="chk_programd1<?=$count?>" style="margin-top:55px;"><span class="labelauty-unchecked-image"></span><span class="labelauty-checked-image"></span></label></td>
                      <td width="20px;"><input maxlength="15" value="<?=$count?>" type="text" name="username" placeholder="Sr. No." class="form-control" disabled="" style="margin-top:35px;"></td>
                      <td class="col-md-3 no-pad" style="padding-left: 5px !important;"><input type="text" id="special_attaraction<?=$count?>-u" onchange="validate_spaces(this.id);validate_spattration(this.id);" name="special_attaraction" style="margin-top:35px;" class="form-control" placeholder="*Special Attraction" title="Special Attraction" value="<?=$row_itinerary['special_attraction']?>"></td>
                      <td class="col-md-5 no-pad" style="padding-left: 5px !important;max-width: 594px;overflow: hidden;position: relative;"><textarea id="day_program<?=$count?>-u" name="day_program" class="form-control day_program" rows="3" placeholder="*Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" title="Day-wise Program"  style="overflow:hidden;resize:none;height:900px;"  
    rows="1"><?=$row_itinerary['daywise_program']?></textarea><span class="style_text" style="position: absolute !important; right: 15px !important; display: flex !important; gap: 15px; background: #f5f5f5 !important; padding: 0px 14px !important; top: 0px !important;"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span></td>
                      <td class="col-md-2 no-pad" style="padding-left: 5px !important;"><input type="text" id="overnight_stay<?=$count?>-u" name="overnight_stay" onchange="validate_spaces(this.id);validate_onstay(this.id);"  style="margin-top:35px;"class="form-control" placeholder="*Overnight Stay" title="Overnight Stay" value="<?=$row_itinerary['overnight_stay']?>"></td>
                      <td class="col-md-2 no-pad" style="padding-left:5px !important;">
                        <!-- Debug: Image path = <?= $row_itinerary['itinerary_image'] ?? 'NULL' ?> -->
                        <!-- Debug: BASE_URL = <?= BASE_URL ?> -->
                        <!-- Debug: Project URL = <?= str_replace('/crm/', '/', BASE_URL) ?> -->
                        <div style="margin-top:35px;">
                          <label for="day_image_<?=$count?>" class="btn btn-sm btn-success" 
                                 style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500; <?= (!empty($row_itinerary['itinerary_image']) && trim($row_itinerary['itinerary_image']) !== '' && trim($row_itinerary['itinerary_image']) !== 'NULL') ? 'display:none;' : '' ?>">
                              Upload Image
                          </label>
                          <input type="file" id="day_image_<?=$count?>" 
                                 name="day_image_<?=$count?>" accept="image/*" 
                                 onchange="previewDayImage(this, '<?=$count?>')" 
                                 style="display: none;">
                          <div id="day_image_preview_<?=$count?>" style="<?= (!empty($row_itinerary['itinerary_image']) && trim($row_itinerary['itinerary_image']) !== '' && trim($row_itinerary['itinerary_image']) !== 'NULL') ? 'display:block;' : 'display:none;' ?> margin-top: 5px;">
                              <div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                                  <img id="preview_img_<?=$count?>" src="<?php 
                                        if (!empty($row_itinerary['itinerary_image'])) {
                                            $image_path = trim($row_itinerary['itinerary_image']);
                                            // Debug the actual path
                                            error_log("Image path from DB: " . $image_path);
                                            
                                            // Check if path is valid and not empty
                                            if ($image_path && $image_path !== '' && $image_path !== 'NULL') {
                                                // Check if path already starts with http
                                                if (strpos($image_path, 'http') === 0) {
                                                    echo $image_path;
                                                } else {
                                                    // For itinerary images, use project root URL instead of CRM BASE_URL
                                                    // BASE_URL is http://localhost/itoursdemo/crm/ but images are in http://localhost/itoursdemo/uploads/
                                                    $project_base_url = str_replace('/crm/', '/', BASE_URL);
                                                    $project_base_url = rtrim($project_base_url, '/');
                                                    $image_path = ltrim($image_path, '/');
                                                    $final_url = $project_base_url . '/' . $image_path;
                                                    error_log("Final image URL: " . $final_url);
                                                    echo $final_url;
                                                }
                                            } else {
                                                // Empty or invalid path, don't output anything
                                                echo '';
                                            }
                                        } else {
                                            echo '';
                                        }
                                    ?>" alt="Preview" 
                                       style="width:100%; height:100%; object-fit: cover; border-radius: 6px;"
                                       data-rel-path="<?= htmlspecialchars(trim($row_itinerary['itinerary_image'] ?? ''), ENT_QUOTES) ?>"
                                       onerror="if(typeof itineraryImageOnError==='function'){itineraryImageOnError(this);} else { this.style.display='none'; }"
                                       onload="this.style.display='block';">
                                  <button type="button" 
                                          onclick="removeDayImage('<?=$count?>', this)" 
                                          title="Remove Image" 
                                          style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); <?= (empty($row_itinerary['itinerary_image']) || trim($row_itinerary['itinerary_image']) === '' || trim($row_itinerary['itinerary_image']) === 'NULL') ? 'display:none;' : '' ?>">
                                      ×
                                  </button>
                              </div>
                          </div>
                          <input type="hidden" id="itinerary_image_path_<?=$count?>" name="itinerary_image_path_<?=$count?>" value="<?= $row_itinerary['itinerary_image'] ?? '' ?>" />
                        </div>
                      </td>
                      <td class="hidden"><input type="hidden" id="entry_id" name="entry_id" class="form-control" value="<?=$row_itinerary['entry_id']?>"></td>
                    </tr>
                    <?php 
                  } ?>
                </tbody>
            </table>
            </div>
        </div>
          <div class="row mg_tp_10">
            <div class="col-xs-12 text-center">
              <button class="btn btn-sm btn-success" id="btn_update"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Update</button>
            </div>
          </div>



      </div>      

    </div>

  </div>

</div>

</form>

<script>
window.itineraryImages = {};
$('#dest_ids1').select2();
$('#itinerary_update_modal').modal('show');



// data bold and underline


$(document).on("click", ".style_text_b, .style_text_u", function() {
        var wrapper = $(this).data("wrapper");

        var textarea = $(this).parents('.style_text').siblings('.day_program')[0];
        console.log(textarea);
        // Ensure textarea exists and selectionStart/selectionEnd are supported
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;

        // Get the selected text
        var selectedText = textarea.value.substring(start, end);

        // Wrap the selected text with the wrapper (e.g., ** for bold, __ for underline)
        var wrappedText = wrapper + selectedText + wrapper;

        // Insert the wrapped text back into the textarea
        textarea.value = textarea.value.substring(0, start) + wrappedText + textarea.value.substring(end);

        // Adjust the cursor position after wrapping
        textarea.selectionStart = start;
        textarea.selectionEnd = end + wrapper.length * 2;
        var text = textarea.value;
        var content = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');

        // Replace markdown-style underline (__text__) with <u> tags
        content = content.replace(/__(.*?)__/g, '<u>$1</u>');
        textarea.value = content;
        //console.log(content);    
    });

    // 

$('#itinerary_frm_update').validate({
    rules:{
           dest_names1 : { required : true }
    },
    submitHandler:function(form){
      itineraryWhenImagesReady(function(){

      var dest_id = $('#dest_ids1').val();
      var table = document.getElementById("default_program_list");
      var rowCount = table.rows.length;
      var sp_arr = [];
      var dwp_arr = [];
      var os_arr = [];
      var checked_arr = [];
      var entry_id_arr = [];
      var img_arr = [];
      //Atleast one row validation
      var count = 0;
      for(var i=0; i<rowCount; i++){
          var row = table.rows[i];
          if(row.cells[0].childNodes[0].checked){
              count++;
          }
      }
      if(parseInt(count) == 0){
          error_msg_alert("Please select atleast one day itinerary!");
          return false;
      }
			var checked_arr = new Array();
      var sp_arr = new Array();
      var dwp_arr = new Array();
      var os_arr = new Array();
			var entry_id_arr = new Array();
      for(var i=0; i<rowCount; i++){
        
        var row = table.rows[i];

        var status = row.cells[0].childNodes[0].checked;
        var sp = row.cells[2].childNodes[0].value;
        var dwp = row.cells[3].childNodes[0].value;
        var os = row.cells[4].childNodes[0].value;
				if(row.cells[6]){
					var entry_id = row.cells[6].childNodes[0].value;	
				}
				else{
					var entry_id = "";
				}
        if(row.cells[0].childNodes[0].checked){

          if(sp==""){
              error_msg_alert('Special attraction is mandatory in row'+(i+1));
              return false;
          }
          if(dwp==""){
              error_msg_alert('Daywise program is mandatory in row'+(i+1));
              return false;
          }
          if(os==""){
              error_msg_alert('Overnight stay is mandatory in row'+(i+1));
              return false;
          }
          var flag1 = validate_spattration(row.cells[2].childNodes[0].id);
          var flag2 = validate_dayprogram(row.cells[3].childNodes[0].id);
          var flag3 = validate_onstay(row.cells[4].childNodes[0].id);         
          if(!flag1 || !flag2 || !flag3){
              return false;
          }
        }
        checked_arr.push(status);
        sp_arr.push(sp);
        dwp_arr.push(dwp);
        os_arr.push(os);
        entry_id_arr.push(entry_id);
        
        var img = itineraryCollectRowImagePath(row);
        img_arr.push(img || '');
      }

      console.log("UPDATE MODAL: Final data being sent:");
      console.log("- dest_id:", dest_id);
      console.log("- sp_arr:", sp_arr);
      console.log("- dwp_arr:", dwp_arr);
      console.log("- os_arr:", os_arr);
      console.log("- checked_arr:", checked_arr);
      console.log("- entry_id_arr:", entry_id_arr);
      console.log("- img_arr:", img_arr);
      console.log("- window.itineraryImages:", window.itineraryImages);

      $('#btn_update').button('loading');
      $.ajax({
      type:'post',
      url:base_url()+'controller/other_masters/itinerary/itinerary_update.php',
      data:{ dest_id : dest_id, sp_arr : sp_arr, dwp_arr : dwp_arr, os_arr : os_arr,checked_arr:checked_arr,entry_id_arr:entry_id_arr, img_arr : JSON.stringify(img_arr)},
      success:function(result){

          $('#btn_update').button('reset');
          var msg = result.split('--');
          if(msg[0]!="error"){
            $('#itinerary_update_modal').modal('hide');
            msg_alert(result);
            list_reflect();
          }
          else{
            error_msg_alert(msg[1]);
            $('#btn_update').button('reset');
          }
      }
      });
      });
    }
});

function itineraryImageUploadUrl() {
    return (($('#base_url').val() || '')) + 'view/other_masters/itinerary/upload_itinerary_image.php';
}

function itineraryParseImageUploadResponse(response) {
    response = String(response || '').replace(/<[^>]*>/g, ' ').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
    if (!response || /^error/i.test(response)) {
        return '';
    }
    var match = response.match(/uploads\/itinerary_images\/[A-Za-z0-9._-]+/i);
    if (match) {
        return match[0];
    }
    if (response.indexOf('uploads/') === 0 && response.indexOf(' ') === -1) {
        return response;
    }
    return '';
}

function itineraryTableRoot() {
    return document.getElementById('default_program_list');
}

function itineraryRowFromControl(el) {
    if (!el) {
        return null;
    }
    var $row = $(el).closest('tr');
    return $row.length ? $row[0] : null;
}

function itineraryFindInRow(row, selector) {
    return row ? row.querySelector(selector) : null;
}

function itineraryRowHidden(row) {
    return itineraryFindInRow(row, 'input[name^="itinerary_image_path_"]');
}

function itinerarySetRowImagePathOnRow(row, path) {
    if (!row) {
        return;
    }
    var hidden = itineraryRowHidden(row);
    if (hidden) {
        hidden.value = path || '';
    }
    if (path) {
        row.setAttribute('data-itinerary-image', path);
    } else {
        row.removeAttribute('data-itinerary-image');
    }
}

function itineraryResetFileInputOnRow(row) {
    var input = itineraryFindInRow(row, 'input[type="file"][id^="day_image_"]');
    if (!input || !input.parentNode) {
        return;
    }
    var neu = input.cloneNode(true);
    neu.value = '';
    input.parentNode.replaceChild(neu, input);
}

function itineraryShowDayImagePreviewOnRow(row, src) {
    if (!row) {
        return;
    }
    var previewImg = itineraryFindInRow(row, 'img[id^="preview_img_"]');
    var previewDiv = itineraryFindInRow(row, 'div[id^="day_image_preview_"]');
    var fileInput = itineraryFindInRow(row, 'input[type="file"][id^="day_image_"]');
    if (previewImg && src) {
        previewImg.src = src;
        previewImg.style.display = 'block';
    }
    if (previewDiv) {
        previewDiv.style.display = 'block';
        var btn = previewDiv.querySelector('button');
        if (btn) {
            btn.style.display = 'flex';
        }
    }
    if (fileInput) {
        var label = row.querySelector('label[for="' + fileInput.id + '"]');
        if (label) {
            label.style.display = 'none';
        }
    }
}

function itineraryHideDayImagePreviewOnRow(row) {
    if (!row) {
        return;
    }
    var previewImg = itineraryFindInRow(row, 'img[id^="preview_img_"]');
    var previewDiv = itineraryFindInRow(row, 'div[id^="day_image_preview_"]');
    var fileInput = itineraryFindInRow(row, 'input[type="file"][id^="day_image_"]');
    if (previewDiv) {
        previewDiv.style.display = 'none';
    }
    if (previewImg) {
        previewImg.src = '';
    }
    if (fileInput) {
        var label = row.querySelector('label[for="' + fileInput.id + '"]');
        if (label) {
            label.style.display = 'inline-block';
        }
    }
}

function itineraryUploadImageFile(file) {
    var fd = new FormData();
    fd.append('uploadfile', file);
    return $.ajax({
        url: itineraryImageUploadUrl(),
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false
    });
}

function itineraryCollectRowImagePath(row) {
    if (!row) {
        return '';
    }
    var hidden = itineraryRowHidden(row);
    var img = hidden ? String(hidden.value || '').trim() : '';
    if (!img) {
        img = String(row.getAttribute('data-itinerary-image') || '').trim();
    }
    return img;
}

function itineraryWhenImagesReady(callback) {
    var tries = 0;
    (function tick() {
        if (!window.itineraryPendingUploads || tries > 40) {
            callback();
            return;
        }
        tries++;
        setTimeout(tick, 250);
    })();
}

window.itineraryImageOnError = function(img) {
    if (!img) {
        return;
    }
    var rel = img.getAttribute('data-rel-path') || '';
    var tried = img.getAttribute('data-fallback') || '';
    var base = ($('#base_url').val() || '').replace(/\/?$/, '/');
    if (!tried && rel) {
        img.setAttribute('data-fallback', '1');
        img.src = base + rel.replace(/^\//, '');
        return;
    }
    img.style.display = 'none';
    var wrap = img.parentElement ? img.parentElement.parentElement : null;
    if (wrap) {
        wrap.style.display = 'none';
    }
    var label = wrap && wrap.parentElement ? wrap.parentElement.querySelector('label') : null;
    if (label) {
        label.style.display = 'block';
    }
};

window.previewDayImage = function(input, rowIndex) {
    var row = itineraryRowFromControl(input);
    var file = input && input.files ? input.files[0] : null;
    if (!file || !row) {
        return;
    }
    var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (allowedTypes.indexOf(file.type) === -1) {
        error_msg_alert('Only JPG, JPEG, PNG, WEBP files are allowed');
        itineraryResetFileInputOnRow(row);
        return;
    }
    var reader = new FileReader();
    reader.onload = function(e) {
        itineraryShowDayImagePreviewOnRow(row, e.target.result);
    };
    reader.readAsDataURL(file);

    window.itineraryPendingUploads = (window.itineraryPendingUploads || 0) + 1;
    window.itineraryImageUploadSeq = window.itineraryImageUploadSeq || {};
    var rowKey = String(rowIndex || (input.id || '').replace('day_image_', '') || 'row');
    window.itineraryImageUploadSeq[rowKey] = (window.itineraryImageUploadSeq[rowKey] || 0) + 1;
    var uploadSeq = window.itineraryImageUploadSeq[rowKey];
    row.setAttribute('data-itinerary-upload-seq', uploadSeq);

    itineraryUploadImageFile(file).always(function() {
        window.itineraryPendingUploads = Math.max(0, (window.itineraryPendingUploads || 1) - 1);
    }).done(function(response) {
        if (String(row.getAttribute('data-itinerary-upload-seq') || '') !== String(uploadSeq)) {
            return;
        }
        var path = itineraryParseImageUploadResponse(response);
        if (!path) {
            error_msg_alert('Image upload failed. Please try again.');
            return;
        }
        itinerarySetRowImagePathOnRow(row, path);
    }).fail(function() {
        if (String(row.getAttribute('data-itinerary-upload-seq') || '') !== String(uploadSeq)) {
            return;
        }
        error_msg_alert('Image upload failed. Please try again.');
    });
};

window.removeDayImage = function(rowIndex, btn) {
    var row = itineraryRowFromControl(btn) || (function() {
        var table = itineraryTableRoot();
        var input = table ? table.querySelector('[id="day_image_' + rowIndex + '"]') : null;
        return itineraryRowFromControl(input);
    })();
    if (!row) {
        return;
    }
    window.itineraryImageUploadSeq = window.itineraryImageUploadSeq || {};
    var rowKey = String(rowIndex || '');
    window.itineraryImageUploadSeq[rowKey] = (window.itineraryImageUploadSeq[rowKey] || 0) + 1;
    row.setAttribute('data-itinerary-upload-seq', window.itineraryImageUploadSeq[rowKey]);
    itineraryHideDayImagePreviewOnRow(row);
    itinerarySetRowImagePathOnRow(row, '');
    itineraryResetFileInputOnRow(row);
};

// Function to check if image exists and handle accordingly
window.checkImageExists = function(img) {
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', img.src, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 404 || xhr.status === 403) {
                console.log('Image not found on server:', img.src);
                img.style.display = 'none';
                img.parentElement.parentElement.style.display = 'none';
                var label = img.parentElement.parentElement.parentElement.querySelector('label');
                if (label) label.style.display = 'block';
            } else if (xhr.status === 200) {
                console.log('Image exists on server:', img.src);
            }
        }
    };
    xhr.send();
};

// Initialize the static row counter for edit modal (start from max existing row + 1)
$(document).ready(function() {
    var table = document.getElementById("default_program_list");
    if (table) {
        var maxRowId = 0;
        for (var i = 1; i < table.rows.length; i++) {
            var fileInput = table.rows[i].cells[5] ? table.rows[i].cells[5].querySelector('input[type="file"]') : null;
            if (fileInput && fileInput.id) {
                var rowId = parseInt(fileInput.id.replace('day_image_', ''));
                if (rowId > maxRowId) {
                    maxRowId = rowId;
                }
            }
        }
        window.itineraryRowIdCounter = maxRowId + 1;
    }
});

</script>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>
<script src="<?= BASE_URL ?>js/app/field_validation.js"></script>