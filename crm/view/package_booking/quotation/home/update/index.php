<?php
include "../../../../../model/model.php";
include_once('../../../../layouts/fullwidth_app_header.php');

global $package_flight_switch,$package_cruise_switch,$package_train_switch;
$hide_flight = ($package_flight_switch == 'Yes') ? 'hidden' : '';
$hide_cruise = ($package_cruise_switch == 'Yes') ? 'hidden' : '';
$hide_train = ($package_train_switch == 'Yes') ? 'hidden' : '';

$quotation_id = $_POST['quotation_id'];
$package_id = $_POST['package_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$emp_id = $_SESSION['emp_id'];
$q = "select * from branch_assign where link='package_booking/quotation/home/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';

$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id'"));
?>
<!-- Tab panes -->
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
                <span class="num" title="Package">2<i class="fa fa-check"></i></span><br>
                <span class="text">Package</span>
            </a>
        </li>
        <li style="display: none;">
            <a href="javascript:void(0)" id="tab_daywise_head">
                <span class="num" title="Daywise Gallery">3<i class="fa fa-check"></i></span><br>
                <span class="text">Daywise Gallery</span>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" id="tab3_head">
                <span class="num" title="Travel And Stay">3<i class="fa fa-check"></i></span><br>
                <span class="text">Travel And Stay</span>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" id="tab4_head">
                <span class="num" title="Costing">4<i class="fa fa-check"></i></span><br>
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
    <div id="tab_daywise" class="bk_tab" style="display: none;">
        <?php include_once("daywise_images.php"); ?>
    </div>
    <div id="tab3" class="bk_tab">
        <?php include_once("tab3.php"); ?>
    </div>
    <div id="tab4" class="bk_tab">
        <?php include_once("tab4.php"); ?>
    </div>
</div>

<!-- Itinerary Modal Container -->
<div id="div_itinerary_modal"></div>

<script src="<?php echo BASE_URL ?>view/package_booking/quotation/js/quotation.js"></script>
<script src="<?php echo BASE_URL ?>view/package_booking/quotation/js/calculation.js"></script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>

<script>
$('#enquiry_id, #currency_code, #transport_vehicle1').select2();
$('#from_date12, #to_date12, #quotation_date').datetimepicker({
    timepicker: false,
    format: 'd-m-Y',
    minDate: 0  // Disable past dates - 0 means today, -1 would allow yesterday
});
$('#txt_arrval1,#txt_dapart1, #train_arrival_date,#train_departure_date').datetimepicker({
    format: 'd-m-Y H:i'
});

/**Hotel Name load start**/
function hotel_name_list_load(id) {
    var city_id = $("#" + id).val();
    var count = id.substring(9);
    $.get("../hotel/hotel_name_load.php", {
        city_id: city_id
    }, function(data) {
        $("#hotel_name-" + count).html(data);
    });
}

function hotel_type_load(id) {
    var hotel_id = $("#" + id).val();
    var count = id.substring(10);
    $.get("../hotel/hotel_type_load.php", {
        hotel_id: hotel_id
    }, function(data) {
        $("#hotel_type" + count).val(data);
    });
    hotel_type_load_cate(id);
}
//roomcategory load
function hotel_type_load_cate(id)
{
  var hotel_id = $("#"+id).val();
  var count = id.substring(11);
  console.log("DEBUG: Loading room categories for hotel_id:", hotel_id, "count:", count);
  $.get( "../hotel/hotel_category.php" , { hotel_id : hotel_id } , function ( data ) {
        console.log("DEBUG: Room category data received:", data);
        $ ("#room_cat-"+count).html( data ) ;  
        
        // Check if Deluxe Room is available and select it
        setTimeout(function() {
            var deluxeOption = $("#room_cat-" + count + " option[value*='Deluxe']");
            if (deluxeOption.length > 0) {
                $("#room_cat-" + count).val(deluxeOption.first().val());
                $("#room_cat-" + count).trigger('change');
            }
        }, 100);
  } ) ;   
}
/**Excursion Name load**/
function get_excursion_list(id) {
    var city_id = $("#" + id).val();
    var base_url = $('#base_url').val();

    var count = id.substring(10);
    $.post(base_url + "view/package_booking/quotation/home/excursion_name_load.php", {
        city_id: city_id
    }, function(data) {
        $("#excursion-" + count).empty();
        $("#excursion-" + count).html(data);
    });
}

/**Excursion Amount load**/
function get_excursion_amount()
{
    
}
/**Excursion Amount load**/
function get_excursion_amount_update(eleid) {
    var base_url = $('#base_url').val();
    var total_adult = $('#total_adult12').val();
    var children_without_bed = $('#children_without_bed12').val();
    var children_with_bed = $('#children_with_bed12').val();
    var total_infant = $('#total_infant12').val();
    var exc_date_arr = new Array();
    var exc_arr = new Array();
    var transfer_arr = new Array();

    var id = eleid.split('-');

    total_adult = (total_adult == '') ? 0 : total_adult;
    children_without_bed = (children_without_bed == '') ? 0 : children_without_bed;
    children_with_bed = (children_with_bed == '') ? 0 : children_with_bed;
    total_infant = (total_infant == '') ? 0 : total_infant;

    exc_date_arr.push($('#exc_date-' + id[1]).val());
    exc_arr.push($('#excursion-' + id[1]).val());
    transfer_arr.push($('#transfer_option-' + id[1]).val());

    $.post(base_url + "view/package_booking/quotation/home/excursion_amount_load.php", {
        exc_date_arr: exc_date_arr,
        exc_arr: exc_arr,
        transfer_arr: transfer_arr,
        total_adult: total_adult,
        children_without_bed: children_without_bed,
        children_with_bed: children_with_bed,
        total_infant: total_infant
    }, function(data) {
        var amount_arr = JSON.parse(data);
        $('#excursion_amount-' + id[1]).val(amount_arr[0]['total_cost']);
    });
}

// Function to process selected itinerary image after modal closes
function processSelectedItineraryImageQuotation() {
    console.log("QUOTATION UPDATE: processSelectedItineraryImageQuotation called");
    console.log("QUOTATION UPDATE: window.selectedItineraryImage =", window.selectedItineraryImage);
    
    if (window.selectedItineraryImage) {
        var dayId = window.selectedItineraryImage.dayId;
        var img = window.selectedItineraryImage.img;
        
        console.log("QUOTATION UPDATE: Processing selected itinerary image for day:", dayId, "img:", img);
        
        // Set the image path in hidden input
        $('#existing_image_path_' + dayId).val(img);
        console.log("QUOTATION UPDATE: Set hidden input value for day", dayId);
        
        // Show image preview if image exists
        if (img && img !== '' && img !== 'NULL') {
            var imageUrl = img;
            
            // Check if path already starts with http
            if (img.indexOf('http') !== 0) {
                // For package images, use project root URL
                var project_base_url = $('#base_url').val().replace('/crm/', '/');
                project_base_url = project_base_url.replace(/\/$/, '');
                var image_path = img.replace(/^\//, '');
                imageUrl = project_base_url + '/' + image_path;
            }
            
            console.log("QUOTATION UPDATE: Final image URL:", imageUrl);
            
            // Update the image preview
            var previewImg = $('#preview_img_' + dayId);
            var previewDiv = $('#day_image_preview_' + dayId);
            
            console.log("QUOTATION UPDATE: Looking for elements - previewImg:", previewImg.length, "previewDiv:", previewDiv.length);
            console.log("QUOTATION UPDATE: Available elements with preview_img_ prefix:", $('[id^="preview_img_"]').length);
            console.log("QUOTATION UPDATE: Available elements with day_image_preview_ prefix:", $('[id^="day_image_preview_"]').length);
            console.log("QUOTATION UPDATE: All preview_img elements:", $('[id^="preview_img_"]').map(function() { return this.id; }).get());
            console.log("QUOTATION UPDATE: All day_image_preview elements:", $('[id^="day_image_preview_"]').map(function() { return this.id; }).get());
            
            if (previewImg.length && previewDiv.length) {
                previewImg.attr('src', imageUrl);
                previewDiv.show();
                
                // Show the remove button
                previewDiv.find('button[onclick*="removeDayImage"]').show();
                
                // Hide the upload button
                $('#day_image_' + dayId).parent().find('label').hide();
                
                console.log("QUOTATION UPDATE: Image preview updated for day", dayId);
            } else {
                console.log("QUOTATION UPDATE: Preview elements not found for day", dayId);
                console.log("QUOTATION UPDATE: Available preview elements:", $('[id*="preview"]').map(function() { return this.id; }).get());
            }
        } else {
            console.log("QUOTATION UPDATE: No valid image to process for day", dayId);
        }
        
        // Clear the stored data
        window.selectedItineraryImage = null;
        console.log("QUOTATION UPDATE: Image processing completed and data cleared");
    } else {
        console.log("QUOTATION UPDATE: No selectedItineraryImage data found");
    }
}

// Listen for modal close event and process selected image
$(document).ready(function() {
    console.log("QUOTATION UPDATE: Setting up modal event listeners");
    
    // Multiple event listeners to ensure we catch the modal close
    $(document).on('hidden.bs.modal', '#itinerary_detail_modal', function() {
        console.log("QUOTATION UPDATE: Modal closed (hidden.bs.modal), processing selected image");
        console.log("QUOTATION UPDATE: window.selectedItineraryImage =", window.selectedItineraryImage);
        setTimeout(function() {
            processSelectedItineraryImageQuotation();
        }, 100);
    });
    
    $(document).on('hide.bs.modal', '#itinerary_detail_modal', function() {
        console.log("QUOTATION UPDATE: Modal closing (hide.bs.modal), processing selected image");
        console.log("QUOTATION UPDATE: window.selectedItineraryImage =", window.selectedItineraryImage);
        setTimeout(function() {
            processSelectedItineraryImageQuotation();
        }, 200);
    });
    
    // Also check periodically if image data is available
    setInterval(function() {
        if (window.selectedItineraryImage) {
            console.log("QUOTATION UPDATE: Periodic check found selectedItineraryImage, processing...");
            processSelectedItineraryImageQuotation();
        }
    }, 1000);
});

</script>
<script src="<?php echo BASE_URL ?>js/app/field_validation.js"></script>
<?php
include_once('../../../../layouts/fullwidth_app_footer.php');
?>