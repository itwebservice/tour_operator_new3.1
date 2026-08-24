<?php

include "../../../model/model.php";

$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
$img = isset($_REQUEST['img_url']) ? $_REQUEST['img_url'] : '';
if ($img == '') {
	$getUrl = mysqli_fetch_assoc(mysqlQuery('select qr_url,sign_url from app_settings limit 1'));
	$db_path = ($type == 'QR') ? $getUrl['qr_url'] : $getUrl['sign_url'];
	$img = ($db_path != '') ? BASE_URL.substr($db_path, 9) : '';
}
$img = htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
?>

<div class="modal fade profile_box_modal" id="dmc_view_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">

    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">


        <div class="modal-body profile_box_padding">

        <ul class="nav nav-tabs" role="tablist">

<li role="presentation" class="active"><a href="#basic_information" aria-controls="home" role="tab" data-toggle="tab" class="tab_name">Image</a></li>

<li class="pull-right"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></li>

</ul>
        <div class="text-center">
        <?php if ($img != '') { ?>
        <img src="<?= $img ?>" alt="Preview" style="max-width: 100%; height: auto;">
        <?php } else { ?>
        <p>No image uploaded</p>
        <?php } ?>
        </div>

            </div>

        </div>

    </div>

</div>



<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>

<script>
    $('#dmc_view_modal').modal('show');
</script>