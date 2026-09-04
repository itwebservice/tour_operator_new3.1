<?php
include "../../../model/model.php";
include_once('../../layouts/fullwidth_app_header.php');
$login_id = $_SESSION['login_id'];
$role = $_SESSION['role'];
$emp_id = $_SESSION['emp_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$sq = mysqli_fetch_assoc(mysqlQuery("select branch_status from branch_assign where link='hotel_quotation/index.php'"));
$branch_status = $sq['branch_status'];
?>
<!-- Tab panes -->
<div id="site_alert"></div>
<div id="vi_confirm_box"></div>
<div id="markup_confirm"></div>
<input type="hidden" id="branch_status" name="branch_status" value="<?= htmlspecialchars($branch_status) ?>">
<div class="bk_tab_head bg_light">
    <ul>
        <li>
            <a href="javascript:void(0)" id="tab1_head" class="active">
                <span class="num" title="Enquiry">1<i class="fa fa-check"></i></span><br>
                <span class="text">Enquiry</span>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" id="tab2_head">
                <span class="num" title="Hotels">2<i class="fa fa-check"></i></span><br>
                <span class="text">Hotels</span>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" id="tab3_head">
                <span class="num" title="Costing">3<i class="fa fa-check"></i></span><br>
                <span class="text">Costing</span>
            </a>
        </li>
    </ul>
</div>

<div class="bk_tabs bg-white">
    <div id="tab1" class="bk_tab active">
        <?php include_once("tab1.php"); ?>
    </div>
    <div id="tab2" class="bk_tab">
        <?php include_once("tab2.php"); ?>
    </div>
    <div id="tab3" class="bk_tab">
        <?php include_once("tab3.php"); ?>
    </div>
</div>
<style>
.xdsoft_datetimepicker { z-index: 100000 !important; }
.select2-dropdown { z-index: 100000 !important; }
.ui-autocomplete { z-index: 100000 !important; }
#tab2 table select[id^="tour_type"] { width: 145px !important; min-width: 145px !important; }
#tab2 .table-responsive { position: relative; }
</style>
<script>
$('#enquiry_id, #currency_code').select2();

$('#from_date, #to_date, #quotation_date1').datetimepicker({
    timepicker: false,
    format: 'd-m-Y',
    formatDate: 'd-m-Y',
    minDate: new Date(),
    parentID: 'body',
    scrollInput: false,
    scrollMonth: false,
    validateOnBlur: false
});
$('#txt_arrval1,#txt_dapart1,#train_arrival_date,#train_departure_date').datetimepicker({
    format: 'd-m-Y H:i'
});

/**Hotel Name load start**/
if (typeof hotelSupplierQuickLoadUrl === 'undefined') {
    hotelSupplierQuickLoadUrl = $('#base_url').val() + 'view/package_booking/quotation/home/hotel/hotel_name_load.php';
}
function hotel_name_list_load(id) {
    var city_id = $("#" + id).val();
    if (!city_id) {
        return;
    }
    var count = id.substring(9);
    var $hotel = $("#hotel_name-" + count);
    if (typeof hotelDropdownLoadByCity === 'function') {
        hotelDropdownLoadByCity(city_id, $hotel);
        return;
    }
    var base_url = $('#base_url').val();
    $.get(base_url + "view/package_booking/quotation/home/hotel/hotel_name_load.php", {
        city_id: city_id
    }, function(data) {
        if ($hotel.data('select2')) {
            $hotel.select2('destroy');
        }
        $hotel.html(data);
        $hotel.select2({ width: '160px', minimumResultsForSearch: 0, dropdownParent: $(document.body) });
        if (typeof captureHotelSelect2Config === 'function') {
            captureHotelSelect2Config($hotel);
        }
        initHotelSelectAddNew($hotel);
    });
}
</script>
<script src="<?php echo BASE_URL ?>view/hotel_quotation/js/quotation.js"></script>
<script>
if (typeof hqInitHotelDatepicker === 'function') {
    hqInitHotelDatepicker($('#quotation_date1'));
}
if (typeof hqInitHotelRowWidgets === 'function') {
    hqInitHotelRowWidgets('#frm_tab2');
}
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<script src="<?php echo BASE_URL ?>js/app/field_validation.js"></script>
<script src="<?php echo BASE_URL ?>view/hotel_quotation/js/business_rule.js"></script>
<?php
include_once('../../layouts/fullwidth_app_footer.php');
?>