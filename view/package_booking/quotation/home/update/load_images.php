<?php
include '../../../../../model/model.php';
$image_url = $_POST['image_url'];
$package_id = $_POST['package_id'];
$quotation_id=$_POST['quotation_id'];
$id = $_POST['id'];

$sq_package_program = mysqlQuery("select * from package_quotation_program where quotation_id='$quotation_id' ORDER BY id");
?>
<h4>Uploaded Images</h4><hr>
<?php
$day_count = 1;
while($row_itinerary = mysqli_fetch_assoc($sq_package_program)){
    // Use the day_image field from package_quotation_program table
    $daywise_image = 'http://itourscloud.com/quotation_format_images/dummy-image.jpg';
    
    if (!empty($row_itinerary['day_image']) && trim($row_itinerary['day_image']) !== '' && trim($row_itinerary['day_image']) !== 'NULL') {
        $image_path = trim($row_itinerary['day_image']);
        
        // Check if path already starts with http
        if (strpos($image_path, 'http') === 0) {
            $daywise_image = $image_path;
        } else {
            // For itinerary images, use project root URL
            $project_base_url = str_replace('/crm/', '/', BASE_URL);
            $project_base_url = rtrim($project_base_url, '/');
            $image_path = ltrim($image_path, '/');
            $daywise_image = $project_base_url . '/' . $image_path;
        }
    }
    ?>
    <div class="col-md-2">
        <div class="gallary-single-image mg_bt_10" style="height:100px;max-height: 100px;overflow:hidden;">
            <img src="<?= $daywise_image ?>" id="<?= $day_count ?>" width="100%" height="100%" 
                 onerror="console.log('Image failed to load:', this.src); this.src='http://itourscloud.com/quotation_format_images/dummy-image.jpg';"
                 onload="console.log('Image loaded successfully:', this.src);">
            <span class="img-check-btn"><button type="button" class="btn btn-danger btn-sm" onclick="delete_image(<?=$package_id ?>,<?=$day_count ?>,'<?=$daywise_image ?>',<?php echo $id; ?>)" title="Remove Image"><i class="fa fa-times" aria-hidden="true"></i></button></span>
        </div>
        <h5 class="text-center no-pad">Day-<?= $day_count ?></h5>
    </div>
    <?php
    $day_count++;
} ?>