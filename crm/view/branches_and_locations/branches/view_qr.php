<?php

include "../../../model/model.php";

$branch_id = $_REQUEST['branch_id'];
$getUrl = mysqli_fetch_assoc(mysqlQuery("select qr_url from branches where branch_id='$branch_id'"));
$img = !empty($getUrl['qr_url']) ? BASE_URL.substr($getUrl['qr_url'],9) : '';
?>

<div class="modal fade profile_box_modal" id="qr_view_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">

    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">


        <div class="modal-body profile_box_padding">

        <ul class="nav nav-tabs" role="tablist">

<li role="presentation" class="active"><a href="#basic_information" aria-controls="home" role="tab" data-toggle="tab" class="tab_name">QR Code</a></li>

<li class="pull-right"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></li>

</ul>
        <div class="text-center">
        <?php if($img != '') { ?>
        <img src="<?= $img ?>" alt="QR Code" width="300">
        <?php } else { ?>
        <p>No QR Code uploaded</p>
        <?php } ?>
        </div>

            </div>

        </div>

    </div>

</div>



<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>

<script>
    $('#qr_view_modal').modal('show');
    
    // Prevent parent modal from closing when this modal closes
    $('#qr_view_modal').on('hidden.bs.modal', function (e) {
        e.stopPropagation();
        // Re-show parent modal if it was hidden
        if(!$('#branch_update_modal').hasClass('in')) {
            $('#branch_update_modal').modal('show');
        }
    });
</script>

