<?php
include "../../model/model.php";
/*======******Header******=======*/
require_once('../layouts/admin_header.php');
?>
<?= begin_panel('Online Leads Settings', '') ?>

<?php
if (isset($_SESSION['flash_msg'])) {
    echo "<script>alert('" . $_SESSION['flash_msg'] . "');</script>";
    unset($_SESSION['flash_msg']);
}
?>
<div class="div_left type-02">
    <ul class="nav nav-pills">
        <li role="presentation" class="dropdown active">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" onclick="reflect_data('1')">
                Facebook
            </a>
        </li>
    </ul>
</div>
<div class="div_right type-02">
    <div id="section_data_form" style="display: flex; gap: 9px;">
        <form action="https://itoursdemo.co.in/connect_facebook.php" method="GET">
                <input type="hidden" name="redirect" value="<?php echo BASE_URL ?>view/online_leads/facebook/fb_callback.php">
                <button type="submit" class="btn btn-success me-2">Connect Facebook</button>
                </form>
        <form action="facebook/fetch_leads.php" method="post" style="display: inline;">
            <input type="hidden" name="redirect_back" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <button type="submit" class="btn btn-success me-2">Fetch Leads</button>
        </form>
    </div>
</div>

<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<script src="<?php echo BASE_URL ?>js/app/field_validation.js"></script>
<?= end_panel() ?>
<?php
require_once('../layouts/admin_footer.php');
?>