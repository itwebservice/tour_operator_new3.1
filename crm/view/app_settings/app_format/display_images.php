<?php
include "../../../model/model.php";
$format = isset($_POST['format']) ? $_POST['format'] : '';
$destination = isset($_POST['destination']) ? $_POST['destination'] : '';
// Default to Option-1 when no format selected
if ($format === '' || $format === null || (int) $format <= 0) {
  $format = 1;
}
$basic_format = "Portrait-Creative";
if ($format == 2) {
  $basic_format = "Landscape-Standard";
} else if ($format == 3) {
  $basic_format = "Landscape-Creative";
} else if ($format == 1 || $format == 9) {
  $basic_format = "Portrait-Standard";
} else if ($format == 5 || $format == 10) {
  $basic_format = "Portrait-Advanced";
} else if ($format == 6) {
  $basic_format = "Landscape-Advanced";
} else {
  // Format : 4 and others
  $basic_format = "Portrait-Creative";
}

$query = "select * from format_image_master where type='$basic_format'";
if ($destination !== '' && $destination !== null) {
  $query .= " and dest_id='$destination'";
} else {
  $query .= " and dest_id='0'";
}
$queryImg = mysqlQuery($query);

$app_setting = mysqli_fetch_assoc(mysqlQuery("
SELECT quot_format, quot_img_url, format_dest_id
FROM app_settings
WHERE setting_id='1'
"));

$selected_img = isset($app_setting['quot_img_url']) ? $app_setting['quot_img_url'] : '';
$saved_format = isset($app_setting['quot_format']) ? $app_setting['quot_format'] : '';
$saved_dest = isset($app_setting['format_dest_id']) ? $app_setting['format_dest_id'] : '';
$current_format_selected = ((string)$saved_format === (string)$format);
// Match saved dest: empty filter means dest 0
$filter_dest = ($destination !== '' && $destination !== null) ? (string)$destination : '0';
$current_dest_selected = ((string)$saved_dest === $filter_dest);

$count = 0;
$any_checked = false;
$rows = array();
while ($db = mysqli_fetch_array($queryImg)) {
  $rows[] = $db;
}

// Prefer per-destination is_selected for this format type
foreach ($rows as $db) {
  if (!empty($db['is_selected']) && (string)$db['is_selected'] === '1') {
    $any_checked = true;
    break;
  }
}

foreach ($rows as $db) {
  $checked = '';
  if (!empty($db['is_selected']) && (string)$db['is_selected'] === '1') {
    $checked = 'checked';
  } elseif (!$any_checked && $current_format_selected && $current_dest_selected && $selected_img !== '' && $selected_img == $db['img_url']) {
    // Fallback for older saves where is_selected was not set
    $checked = 'checked';
    $any_checked = true;
  }
?>
  <div class="gallary-image">
    <div class="col-sm-3">
      <div class="gallary-single-image mg_bt_30 mg_bt_10_sm_xs" style="width: 100%;">
        <img src="<?php echo htmlspecialchars($db['img_url'], ENT_QUOTES); ?>" id="image<?php echo $count; ?>" alt="title" class="img-responsive">
        <span class="img-check-btn">
          <input
            type="radio"
            id="image_select<?php echo $count; ?>"
            name="image_check"
            value="<?php echo htmlspecialchars($db['img_url'], ENT_QUOTES); ?>"
            <?php echo $checked; ?>>
        </span>
      </div>
    </div>
  </div>
<?php
  $count++;
}
?>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>
