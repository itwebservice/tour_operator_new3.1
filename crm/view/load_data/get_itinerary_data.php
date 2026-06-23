<?php
include "../../model/model.php";
$dest_id = $_POST['dest_id'];
$sq_itinerary_c = mysqli_num_rows(mysqlQuery("select * from itinerary_master where dest_id='$dest_id'"));
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
            <td width="27px;" style="padding-right: 10px !important;"><input class="css-checkbox labelauty" id="chk_programd1<?=$count?>" type="checkbox" style="display: none;"><label for="chk_programd1<?=$count?>"><span class="labelauty-unchecked-image"></span><span class="labelauty-checked-image"></span></label></td>
            <td width="20px;"><input maxlength="15" value="<?=$count?>" type="text" name="username" placeholder="Sr. No." class="form-control" disabled=""></td>
            <td class="col-md-3 no-pad" style="padding-left: 5px !important;"><input type="text" id="special_attaraction<?=$count?>" onchange="validate_spaces(this.id);validate_spattration(this.id);" name="special_attaraction" class="form-control" placeholder="*Special Attraction" title="Special Attraction" value="<?=$row_itinerary['special_attraction']?>"></td>
            <td class="col-md-5 no-pad" style="padding-left: 5px !important;"><textarea id="day_program<?=$count?>" name="day_program" class="form-control" rows="2" placeholder="*Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" title="Day-wise Program"><?=$row_itinerary['daywise_program']?></textarea></td>
            <td class="col-md-2 no-pad" style="padding-left: 5px !important;"><input type="text" id="overnight_stay<?=$count?>" name="overnight_stay" onchange="validate_spaces(this.id);validate_onstay(this.id);" class="form-control" placeholder="*Overnight Stay" title="Overnight Stay" value="<?=$row_itinerary['overnight_stay']?>"></td>
            <td class="col-md-1 no-pad" style="padding-left: 5px !important; width: 120px;">
                <?php if (!empty($row_itinerary['itinerary_image']) && trim($row_itinerary['itinerary_image']) !== '' && trim($row_itinerary['itinerary_image']) !== 'NULL') { ?>
                    <div style="margin-top: 5px;">
                        <div style="height:80px; max-height: 80px; overflow:hidden; position: relative; width: 80px; border: 2px solid #28a745; border-radius: 8px; background-color: #f8f9fa;">
                            <img src="<?php
                                $image_path = trim($row_itinerary['itinerary_image']);
                                if (strpos($image_path, 'http') === 0) {
                                    echo $image_path;
                                } else {
                                    $project_base_url = str_replace('/crm/', '/', BASE_URL);
                                    $project_base_url = rtrim($project_base_url, '/');
                                    echo $project_base_url . '/' . ltrim($image_path, '/');
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
<?php }
else{
    if($dest_id != '' || $dest_id != 0){ ?>
    <div class="col-md-12 col-sm-6 col-xs-12 mg_tp_10">
    <?php echo '<h4 class="no-pad">Itinerary not added for this destination! <a href="'.BASE_URL.'view/other_masters/index.php" target="_blank" title="Add Itinerary"><i class="fa fa-plus"></i>&nbsp;&nbsp;Itinerary</a></h4> '; ?>
    </div>
<?php } ?>
<div class="col-md-12 col-sm-6 col-xs-12 mg_tp_10"></div>
<?php }?>
<input type="hidden" id="sq_itinerary_c1" value="<?=$sq_itinerary_c?>"/>

<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>