<?php

include "../../../model/model.php";

$branch_id = $_REQUEST['branch_id'];
$getUrl = mysqli_fetch_assoc(mysqlQuery("select logo_url from branches where branch_id='$branch_id'"));
$img = !empty($getUrl['logo_url']) ? BASE_URL.substr($getUrl['logo_url'],9) : '';
?>

<div class="modal fade profile_box_modal" id="logo_view_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">

    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">


        <div class="modal-body profile_box_padding">

        <ul class="nav nav-tabs" role="tablist">

<li role="presentation" class="active"><a href="#basic_information" aria-controls="home" role="tab" data-toggle="tab" class="tab_name">Branch Logo</a></li>

<li class="pull-right"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></li>

</ul>
        <div class="text-center">
        <?php if($img != '') { ?>
        <img src="<?= $img ?>" alt="Branch Logo" style="max-width: 100%; height: auto;">
        <?php } else { ?>
        <p>No Logo uploaded</p>
        <?php } ?>
        </div>

            </div>

        </div>

    </div>

</div>



<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>

<script>
    $('#logo_view_modal').modal('show');
    
    // Prevent parent modal from closing when this modal closes
    $('#logo_view_modal').on('hidden.bs.modal', function (e) {
        e.stopPropagation();
        // Re-show parent modal if it was hidden
        if(!$('#branch_update_modal').hasClass('in')) {
            $('#branch_update_modal').modal('show');
        }
    });
</script>

