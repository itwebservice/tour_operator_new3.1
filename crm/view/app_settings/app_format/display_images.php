<?php
include "../../../model/model.php";
$format = $_POST['format'];
$destination = $_POST['destination'];
$basic_format = "Landscape-Standard-Creative";
if ($format == 2) {
  $count = 129;
  $dir = 'https://itourscloud.com/quotation_format_images/Landscape-Standard-Creative/';
  $basic_format = "Landscape-Standard";
} else if ($format == 3) {
  $count = 129;
  $dir = 'https://itourscloud.com/quotation_format_images/Landscape-Standard-Creative/';
  $basic_format = "Landscape-Creative";
  
}
else if($format == 1 || $format == 9){ $count = 10; $dir = 'https://itourscloud.com/quotation_format_images/Portrait-Standard/';
  $basic_format = "Portrait-Standard";
  
}
else if($format == 5 || $format == 10){ $count = 52; $dir = 'https://itourscloud.com/quotation_format_images/Portrait-Advanced/';
  $basic_format = "Portrait-Advanced";
} else if ($format == 6) {
  $count = 98;
  $dir = 'https://itourscloud.com/quotation_format_images/Landscape-Advanced/';
  $basic_format = "Landscape-Advanced";
} else { //Format : 4
  $count = 65;
  $dir = 'https://itourscloud.com/quotation_format_images/Portrait-Creative/';
  $basic_format = "Portrait-Creative";
}
$sq_saved = mysqli_fetch_assoc(mysqlQuery("select quot_format, quot_img_url from app_settings where setting_id='1'"));
$saved_img_url = isset($sq_saved['quot_img_url']) ? $sq_saved['quot_img_url'] : '';
$saved_format = isset($sq_saved['quot_format']) ? $sq_saved['quot_format'] : '';

// for($i = 1; $i<=$count; $i++){
//   $image_path = $dir.$i.'.jpg';
//   $sq_setting = mysqli_num_rows(mysqlQuery("select * from app_settings where quot_format='$format' and quot_img_url='$image_path'"));
//   if($sq_setting>0){
//     $checked = 'checked';
//   }else{
//     $checked = '';
//   }
?>
<!-- <div class="gallary-image">
    <div class="col-sm-3">
      <div class="gallary-single-image mg_bt_30 mg_bt_10_sm_xs" style="width: 100%;">
          <img src="<//?php echo $dir . $i . '.jpg'; ?>" id="image<//?php echo $i; ?>" alt="title" class="img-responsive">
          <span class="img-check-btn">
            <input type="radio" id="image_select<//?php echo $i; ?>" name="image_check" value="<//?php echo $dir . $i . '.jpg' ?>" <?= $checked ?>>
          </span>
          <div class="table-image-btns">
            <ul style="margin-left: -40%;">
              <span style="color: #fff; "><//?php echo $sq_gal['description']; ?></span>
            </ul>
          </div>
      </div>
    </div>
</div> -->

<?php
$query = "select * from format_image_master where type='$basic_format'";

if (!empty($destination)) {
  $query .= " and dest_id='$destination'";
} else {
  $query .= " and dest_id='0'";
}
$queryImg = mysqlQuery($query);
$count = 0;

              // Find default image position from Destination 1

             $default_position = 1;
              $pos = 0;

              $res = mysqlQuery("
SELECT id,is_selected
FROM format_image_master
WHERE type='$basic_format'
AND dest_id='1'
ORDER BY id
");

              while ($r = mysqli_fetch_assoc($res)) {
                $pos++;
                if ($r['is_selected'] == 1) {
                  $default_position = $pos;
                  break;
                }
              }


$app_setting = mysqli_fetch_assoc(mysqlQuery("
SELECT quot_format, quot_img_url
FROM app_settings
WHERE setting_id='1'
"));

$selected_img = $app_setting['quot_img_url'];
$saved_format = $app_setting['quot_format'];
$current_format_selected = ($saved_format == $format);
$position = 0;

while ($db = mysqli_fetch_array($queryImg)) {
  $position++;
  if ($current_format_selected) {
    $checked = ($selected_img == $db['img_url']) ? "checked" : "";
  } else {
    $checked = ($position == $default_position) ? "checked" : "";
  }
?>
  <div class="gallary-image">
    <div class="col-sm-3">
      <div class="gallary-single-image mg_bt_30 mg_bt_10_sm_xs" style="width: 100%;">
        <img src="<?php echo $db['img_url']; ?>" id="image<?php echo $count; ?>" alt="title" class="img-responsive">
        <span class="img-check-btn">
          <input
            type="radio"
            id="image_select<?php echo $count; ?>"
            name="image_check"
            value="<?php echo $db['img_url']; ?>"
            <?php echo $checked; ?>>
          <div class="table-image-btns">
            <ul style="margin-left: -40%;">
              <!-- <span style="color: #fff; "><//?php echo $sq_gal['description']; ?></span> -->
            </ul>
          </div>
      </div>
    </div>
  </div>
<?php
  $count++;
}
?>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>