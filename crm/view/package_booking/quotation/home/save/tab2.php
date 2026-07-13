
<style>
  .app_dual_button input[type="radio"]{
display: none;
}
.app_dual_button{
padding: 6px 6px;
border: 1px solid <?= $theme_color ?>;
margin-right: -5px;
background: #fff;
margin-bottom: 0;
cursor: pointer;
font-weight: 300;
font-size: 14px;
}
.app_dual_button label{
margin:0;
}
.app_dual_button.active{
background: <?= $theme_color ?>;
color: #fff;
}
.app_dual_button:first-child{
border-top-left-radius:20px;
border-bottom-left-radius:20px;
}
.app_dual_button:last-child{
border-top-right-radius:20px;
border-bottom-right-radius:20px;
}
label input.labelauty:checked + label{
<!-- background-color: rgba(0, 0, 0, 0.42); -->
color: #ffffff;
}
input.labelauty + label, input.labelauty:checked + label, input.labelauty:checked:not([disabled]) + label:hover{
background-color:<?= $theme_color ?>;
}
</style>

<form id="frm_tab2">

    <div class="app_panel" style="padding-top: 30px;">

        <div class="container" style="width:100% !important;">

        <div class="row text-center text_left_sm_xs mg_bt_10">	
            <label for="standardPackage" class="app_dual_button mg_bt_10 active">
                <input type="radio" id="standardPackage" name="quotation_package" checked onchange="package_booking_reflect()">
                &nbsp;&nbsp;Standard Package
            </label>    
            <label for="aiBuilder" class="app_dual_button mg_bt_10">
                <input type="radio" id="aiBuilder" name="quotation_package" onchange="package_booking_reflect()">
                &nbsp;&nbsp;AI Builder
            </label>
        </div> 

            <div class="row" id="package_div_content">

                <div class="col-md-3 col-sm-4 col-xs-12 mg_bt_20" id="package_div">
                    <?php
                    $sq_tours = mysqlQuery("select * from custom_package_master where status !='Inactive'");
                    $quotation_refer_id_map = array();
                    $sq_refer = mysqlQuery("SELECT dest_id, MIN(package_id) AS package_id FROM custom_package_master WHERE status != 'Inactive' GROUP BY dest_id");
                    while ($row_refer = mysqli_fetch_assoc($sq_refer)) {
                        $quotation_refer_id_map[$row_refer['dest_id']] = intval($row_refer['package_id']);
                    }
                    ?>
                    <select name="dest_name" id="dest_name" title="Select Destination"
                        onchange="quotationOnDestinationChange(this)" style="width:100%" data-add-new-option="true">
                        <option value="">*Select Destination</option>
                        <?php
                        $sq_query = mysqlQuery("select * from destination_master where status != 'Inactive'");
                        while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>
                            <option value="<?php echo $row_dest['dest_id']; ?>"><?php echo $row_dest['dest_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <input type="hidden" id="destinations" name="destinations" value='<?= get_destinations() ?>'>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-12 mg_bt_20">
                    <select name="nights_filter" id="nights_filter" title="Filter by Nights"
                        onchange="filter_packages_by_nights()" style="width:100%">
                        <option value="">All Nights</option>
                        <?php
                        // Generate options for 1 to 30 nights
                        for ($i = 1; $i <= 30; $i++) {
                            echo "<option value='$i'>$i Night" . ($i > 1 ? 's' : '') . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6 col-sm-4 col-xs-12 mg_bt_20 text-right">
                    <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note : The Package is not available for this Destination.Please create here."><i class="fa fa-question-circle"></i></button>

                    <a href="../../../../custom_packages/master/index.php" target='_blank' class="btn btn-sm" style="
                      background: none;
                      background: none;
                      color: #fff;
                      padding: 6px 14px;
                      font-size: 16px;
                      color: #007bff;text-decoration:none; display:inline-block;"><i class="fa fa-plus"></i>&nbsp;&nbsp;Package Tour</a>
                </div>
                <div class="col-md-12 col-sm-8 col-xs-12 no-pad" id="package_name_div">
                </div>
            </div>

            <!-- ai chat container start -->
            <div class="ai-chat-container" id="ai_chat_container" style="display: none;">
                <!-- <button class="btn btn-info btn-sm ico_left" id="aiToggleBtn" type="button" aria-label="Toggle AI assistant">
                    <span class="ai-toggle-icon"><i class=""><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                       <path d="M11.6838 0L10.8879 2.38783L8.50006 3.18377V4.13245L10.8879 4.9284L11.6838 7.31623H12.6325L13.4285 4.9284L15.8163 4.13245V3.18377L13.4285 2.38783L12.6325 0H11.6838Z" fill="white"/>
                       <path d="M5.01289 8.51283L6.18383 5H7.13251L8.30346 8.51283L11.8163 9.68377V10.6325L8.30346 11.8034L7.13251 15.3162H6.18383L5.01289 11.8034L1.50006 10.6325V9.68377L5.01289 8.51283Z" fill="white"/>
                       <path d="M2.17582 1L1.63186 2.63186L0 3.17582V3.82416L1.63186 4.36811L2.17582 5.99997H2.82416L3.36811 4.36811L4.99997 3.82416V3.17582L3.36811 2.63186L2.82416 1H2.17582Z" fill="white"/>
                       </svg>
                       </i></span>
                    <span>AI</span>
                </button> -->
                        
                <div class="ai-chat-box" id="aiChatBox" aria-hidden="true">
                    <textarea id="aiMessageInput" placeholder="Type your message..."></textarea>

                    <button type="button" class="send-btn" id="btnAnalyseMessage" aria-label="Analyse message">
                        <!-- Send Icon -->
                        <svg viewBox="0 0 24 24">
                        <path d="M2 21L23 12L2 3V10L17 12L2 14V21Z"/>
                        </svg>
                    </button>
                </div>
                <div id="aiApiInfo"></div>

                <div id="aiItineraryContainer"></div>

                <div class="ai-inclusions-exclusions-row">
                <div class="row mg_tp_20 ">
                                    <div class="col-md-6">
                                        <legend>Inclusions</legend>
                                    </div>
                                    <div class="col-md-6">
                                        <legend>Exclusions</legend>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table style="width:100%" class="no-marg"
                                            id=""
                                            name="">
                                            <tr>
                                        
                                                <td class="col-md-6"><textarea class="feature_editor"
                                                        id="inclusions_ai" name="inclusions"
                                                        placeholder="Inclusions" title="Inclusions"
                                                        rows="4"></textarea></td>
                                                <td class="col-md-6"><textarea class="feature_editor"
                                                        id="exclusions_ai" name="exclusions"
                                                        placeholder="Exclusions" title="Exclusions"
                                                        rows="4"></textarea></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

            </div>
                </div>

                <script>
                    (function () {
                        const aiToggleBtn = document.getElementById("aiToggleBtn");
                        const aiChatBox = document.getElementById("aiChatBox");

                        if (!aiToggleBtn || !aiChatBox) {
                            return;
                        }

                        aiToggleBtn.addEventListener("click", function () {
                            aiChatBox.classList.toggle("show");
                            const isVisible = aiChatBox.classList.contains("show");
                            aiChatBox.setAttribute("aria-hidden", String(!isVisible));
                            document.querySelectorAll('#aiItineraryContainer .ai-itinerary-row').forEach(function(row) {
                                row.classList.toggle("show", isVisible);
                                row.setAttribute("aria-hidden", String(!isVisible));
                            });
                        });
                    })();
                </script>





            <!-- ai chat container end -->

            <div class="row text-center mg_tp_20">
                <div class="col-xs-12">
                    <button class="btn btn-info btn-sm ico_left" type="button" onclick="switch_to_tab1()"><i
                            class="fa fa-arrow-left"></i>&nbsp;&nbsp Previous</button>
                    &nbsp;&nbsp;
                    <button class="btn btn-info btn-sm ico_right">Next&nbsp;&nbsp;<i
                            class="fa fa-arrow-right"></i></button>
                </div>
            </div>


            <input type="hidden" id="pckg_daywise_url" name="pckg_daywise_url" />
            <input type="hidden" id="is_ai_quotation" name="is_ai_quotation" value="0" />
            <input type="hidden" id="quotation_dest_id" name="quotation_dest_id" value="" />
            <input type="hidden" id="quotation_refer_id" name="quotation_refer_id" value="0" />
            <input type="hidden" id="quotation_refer_id_map" value='<?= htmlspecialchars(json_encode($quotation_refer_id_map), ENT_QUOTES, 'UTF-8') ?>' />

</form>
<?= end_panel() ?>

<script>
$('#dest_name').select2();
if (typeof initAllDestinationSelectAddNew === 'function') {
    initAllDestinationSelectAddNew('#frm_tab2');
}

function quotationOnDestinationChange(selectEl) {
    var $select = $(selectEl);
    var destId = $select.val();
    var destName = $select.find('option:selected').text();
    if (destId) {
        sessionStorage.setItem('selected_destination_id', destId);
        sessionStorage.setItem('selected_destination_name', destName);
        var tourName = ($('#tour_name').val() || '').trim();
        if (!tourName || tourName !== destName) {
            $('#tour_name').val(destName);
        }
    } else {
        sessionStorage.removeItem('selected_destination_id');
        sessionStorage.removeItem('selected_destination_name');
    }
    if (typeof quotationResetPackageLoadCache === 'function') {
        quotationResetPackageLoadCache();
    }
    if (typeof clearQuotationPackageListUi === 'function') {
        clearQuotationPackageListUi();
    }
    if (typeof load_packages_with_filter === 'function') {
        load_packages_with_filter(true);
    }
}

// Force destination sync on page load
setTimeout(function() {
    console.log("Force sync - checking for destination from Tab1...");
    var tab1Destination = $('#tour_name').val();
    console.log("Force sync - Tab1 destination:", tab1Destination);
    
    if (tab1Destination) {
        // Try to find and set the destination
        var destinations = JSON.parse($('#destinations').val() || '[]');
        for (var i = 0; i < destinations.length; i++) {
            if (destinations[i].label === tab1Destination) {
                console.log("Force sync - Found matching destination:", destinations[i]);
                $('#dest_name').val(destinations[i].dest_id).trigger('change');
                sessionStorage.setItem('selected_destination_id', destinations[i].dest_id);
                sessionStorage.setItem('selected_destination_name', destinations[i].label);
                console.log("Force sync - Destination set to:", destinations[i].label);
                break;
            }
        }
    }
}, 500);

// Additional sync when Tab2 becomes visible
$(document).on('click', '#tab2_head', function() {
    console.log("Tab2 clicked - forcing destination sync...");
    setTimeout(function() {
        var tab1Destination = ($('#tour_name').val() || '').trim();
        var storedDestName = sessionStorage.getItem('selected_destination_name');
        if (tab1Destination && storedDestName && tab1Destination !== storedDestName) {
            sessionStorage.removeItem('selected_destination_id');
            sessionStorage.removeItem('selected_destination_name');
            if (typeof quotationResetPackageLoadCache === 'function') {
                quotationResetPackageLoadCache();
            }
            if (typeof clearQuotationPackageListUi === 'function') {
                clearQuotationPackageListUi();
            }
        }
        if (typeof syncDestinationFromTab1 === 'function') {
            syncDestinationFromTab1(true);
            return;
        }
        if (tab1Destination) {
            var destinations = JSON.parse($('#destinations').val() || '[]');
            for (var i = 0; i < destinations.length; i++) {
                if (destinations[i].label === tab1Destination) {
                    console.log("Tab2 clicked - Found matching destination:", destinations[i]);
                    $('#dest_name').val(destinations[i].dest_id).trigger('change');
                    sessionStorage.setItem('selected_destination_id', destinations[i].dest_id);
                    sessionStorage.setItem('selected_destination_name', destinations[i].label);
                    break;
                }
            }
        }
    }, 100);
});


    // Initialize nights filter when tab2 loads
    $(document).ready(function() {
        // Function to sync nights filter and destination
        function syncNightsFilter(force_sync = false) {
            // Get total days from tab1 first
            var total_days = $('#total_days').val();
            var selected_nights = sessionStorage.getItem('selected_nights');
            var destination_id = sessionStorage.getItem('selected_destination_id');
            var destination_name = sessionStorage.getItem('selected_destination_name');
            var current_nights_filter = $('#nights_filter').val();
            var user_modified_nights = sessionStorage.getItem('user_modified_nights');

            console.log('Tab2 ready - total_days from tab1:', total_days);
            console.log('Tab2 ready - selected_nights from sessionStorage:', selected_nights);
            console.log('Tab2 ready - destination_id:', destination_id, 'destination_name:', destination_name);
            console.log('Tab2 ready - current_nights_filter:', current_nights_filter, 'user_modified_nights:', user_modified_nights);

            // Use total_days from tab1 as primary source (only if user hasn't manually modified)
            if (total_days && total_days > 0 && (!user_modified_nights || force_sync)) {
                selected_nights = total_days;
                sessionStorage.setItem('selected_nights', selected_nights);
                console.log('Using total_days from tab1:', selected_nights);
            } else if (selected_nights) {
                console.log('Using stored nights from sessionStorage:', selected_nights);
            }

            // Sync nights filter (only if user hasn't manually changed it or if force_sync is true)
            if (selected_nights && selected_nights > 0 && (!user_modified_nights || force_sync)) {
                $('#nights_filter').val(selected_nights);
                console.log('Initialized nights filter with value:', selected_nights);
            }

            // Sync destination
            if (destination_id && destination_name) {
                $('#dest_name').val(destination_id);
                $('#dest_name').trigger('change');
                console.log('Initialized destination with:', destination_name);
            }

            // Trigger package filtering if both destination and nights are available
            if ((destination_id || $('#dest_name').val()) && (selected_nights || $('#nights_filter').val())) {
                if (typeof load_packages_with_filter === 'function') {
                    load_packages_with_filter();
                }
            }
        }

        // Initial sync
        syncNightsFilter();

        // Also sync when tab2 becomes visible (in case of dynamic loading)
        $(document).on('click', '#tab2_head', function() {
            // Check if user modification flag was reset (meaning user went back to Tab1)
            var user_modified_nights = sessionStorage.getItem('user_modified_nights');
            var force_sync = !user_modified_nights; // Force sync if flag was reset
            
            console.log('Tab2 clicked - user_modified_nights:', user_modified_nights, 'force_sync:', force_sync);
            
            // Force destination sync when clicking Tab2
            var destination_id = sessionStorage.getItem('selected_destination_id');
            var destination_name = sessionStorage.getItem('selected_destination_name');
            console.log('Tab2 clicked - forcing destination sync:', destination_id, destination_name);
            
            if (destination_id && destination_name) {
                $('#dest_name').val(destination_id);
                $('#dest_name').trigger('change');
                console.log('Tab2 clicked - destination synced to:', destination_name);
            }
            
            setTimeout(function() {
                syncNightsFilter(force_sync);
            }, 50);
        });

        // Reset user modification flag when clicking on Tab1 header
        $(document).on('click', '#tab1_head', function() {
            sessionStorage.removeItem('user_modified_nights');
            console.log('Reset user_modified_nights flag - clicked Tab1 header');
        });

        // Reduced frequency check - only check every 2 seconds to prevent excessive calls
        setInterval(function() {
            var current_total_days = $('#total_days').val();
            var current_nights_filter = $('#nights_filter').val();
            var current_destination_id = sessionStorage.getItem('selected_destination_id');
            var current_dest_name = $('#dest_name').val();
            var user_modified_nights = sessionStorage.getItem('user_modified_nights');

            var needs_sync = false;

            // Check if total_days changed (only if user hasn't manually modified nights)
            if (current_total_days && current_total_days > 0 && current_nights_filter != current_total_days && !user_modified_nights) {
                console.log('Detected total_days change, syncing nights filter:', current_total_days);
                needs_sync = true;
            }

            // Check if destination changed
            if (current_destination_id && current_dest_name != current_destination_id) {
                console.log('Detected destination change, syncing destination:', current_destination_id);
                needs_sync = true;
            }

            if (needs_sync) {
                syncNightsFilter();
            }
        }, 2000); // Increased from 500ms to 2000ms
    });

    // Debounce timer for package loading
    var packageLoadingTimer = null;
    var isLoadingPackages = false;
    var lastLoadedDestId = null;
    var lastLoadedNights = null;

    window.__quotationResetPackageCache = function () {
        lastLoadedDestId = null;
        lastLoadedNights = null;
        isLoadingPackages = false;
        if (packageLoadingTimer) {
            clearTimeout(packageLoadingTimer);
            packageLoadingTimer = null;
        }
    };

    // Function to load packages with both destination and nights filter
    function load_packages_with_filter(forceReload) {
        var dest_id = $('#dest_name').val();
        var total_nights = $('#nights_filter').val() || sessionStorage.getItem('selected_nights');

        console.log("load_packages_with_filter called - dest_id:", dest_id, "total_nights:", total_nights, "forceReload:", forceReload);

        // Prevent multiple simultaneous loads
        if (isLoadingPackages) {
            console.log("Package loading already in progress, skipping...");
            return;
        }

        // Prevent loading same data unless forced
        if (!forceReload && lastLoadedDestId === dest_id && lastLoadedNights === total_nights) {
            console.log("Same data already loaded, skipping...");
            return;
        }

        // Clear any existing timer
        if (packageLoadingTimer) {
            clearTimeout(packageLoadingTimer);
        }

        if (dest_id) {
            syncQuotationReferId();
            // Debounce the loading to prevent excessive calls
            packageLoadingTimer = setTimeout(function() {
                isLoadingPackages = true;

                // Show loading indicator
                $('#package_loading_indicator').show();
                $('.package_content').addClass('loading');

                // Update sessionStorage with current nights selection
                if (total_nights) {
                    sessionStorage.setItem('selected_nights', total_nights);
                }

                console.log("Calling package_dynamic_reflect_with_nights with dest_id:", dest_id, "nights:", total_nights);
                // Call the package loading function with nights parameter
                package_dynamic_reflect_with_nights('dest_name', total_nights);
            }, 800); // Increased to 800ms debounce
        } else {
            console.log("No destination selected, cannot load packages");
        }
    }

    // Function to filter packages by nights in tab2
    function filter_packages_by_nights() {
        var total_nights = $('#nights_filter').val();

        // Mark that user has manually modified the nights filter
        sessionStorage.setItem('user_modified_nights', 'true');
        console.log('User manually changed nights filter to:', total_nights);

        if (total_nights) {
            sessionStorage.setItem('selected_nights', total_nights);
        } else {
            sessionStorage.removeItem('selected_nights');
        }

        // Reload packages with the new filter
        load_packages_with_filter();
    }

    // Function to load packages with explicit nights parameter
    function package_dynamic_reflect_with_nights(dest_name, total_nights) {
        var dest_id = $('#' + dest_name).val();
        var base_url = $('#base_url').val();

        // Ensure total_nights is not null or undefined
        if (!total_nights) {
            total_nights = '';
        }

        var ajax_data = {
            dest_id: dest_id,
            total_nights: total_nights
        };

        $.ajax({
            type: 'post',
            url: base_url + 'view/package_booking/quotation/inc/get_packages.php?v=' + Date.now(),
            data: ajax_data,
            success: function(result) {
                $('#package_name_div').html(result);
                // Hide loading indicator and remove loading class
                $('#package_loading_indicator').hide();
                $('.package_content').removeClass('loading');
                isLoadingPackages = false;

                // Track loaded data to prevent reloading same data
                lastLoadedDestId = dest_id;
                lastLoadedNights = total_nights;
            },
            error: function(result) {
                console.log('Package loading error:', result.responseText);
                // Hide loading indicator even on error
                $('#package_loading_indicator').hide();
                $('.package_content').removeClass('loading');
                isLoadingPackages = false;

                // Reset loaded data tracking on error
                lastLoadedDestId = null;
                lastLoadedNights = null;
            }
        });
    }

    function switch_to_tab1() {
        // Reset user modification flag when going back to Tab1
        // This allows re-sync when returning to Tab2
        sessionStorage.removeItem('user_modified_nights');
        console.log('Reset user_modified_nights flag - returning to Tab1');

        $('#tab2_head').removeClass('active');
        $('#tab1_head').addClass('active');
        $('.bk_tab').removeClass('active');
        $('#tab1').addClass('active');
        $('html, body').animate({
            scrollTop: $('.bk_tab_head').offset().top
        }, 200);
    }

    // Function to save itinerary data immediately when tab2 is completed
    function getItineraryImageKey(row, packageId, rowIndex) {
        var fileInput = row.querySelector('input[id^="day_image_"]');
        if (fileInput && fileInput.id) {
            return fileInput.id.replace('day_image_', '');
        }
        return packageId + '_' + (rowIndex + 1);
    }

    function getItineraryDayNumber(row, rowIndex) {
        var srNoInput = row.querySelector('input[name="username"]');
        if (srNoInput && srNoInput.value) {
            return srNoInput.value;
        }
        return String(rowIndex + 1);
    }

    function saveItineraryData() {
        var attraction_arr = [];
        var program_arr = [];
        var stay_arr = [];
        var meal_plan_arr = [];
        var day_image_arr = [];
        var package_p_id_arr = [];
        var package_id_arr = [];

        if ((hasValidAiItinerary() || hasAiItineraryContent()) && $('input[name="custom_package"]:checked').length === 0) {
            var aiData = storeAiItinerarySession();
            console.log('Saving AI itinerary data:', aiData);
            return;
        }

        // Collect data from all selected packages
        console.log("Looking for selected packages...");
        var selectedPackages = $('input[name="custom_package"]:checked');
        console.log("Found " + selectedPackages.length + " selected packages");

        $('input[name="custom_package"]:checked').each(function() {
            var package_id = $(this).val();
            console.log("Processing package ID: " + package_id);
            package_id_arr.push(package_id);

            var table = document.getElementById("dynamic_table_list_p_" + package_id);
            console.log("Looking for table: dynamic_table_list_p_" + package_id);
            if (table) {
                console.log("Table found, rows: " + table.rows.length);
                var rowCount = table.rows.length;
                console.log("Processing " + rowCount + " rows in table");
                for (var i = 0; i < rowCount; i++) {
                    var row = table.rows[i];
                    console.log("Row " + i + " has " + row.cells.length + " cells");

                    // Check if checkbox exists and is checked
                    var checkbox = row.cells[0].childNodes[0];
                    var isChecked = checkbox && checkbox.checked;
                    console.log("Row " + i + " checkbox checked: " + isChecked);

                    if (isChecked) {
                        // Debug each cell
                        for (var cellIndex = 0; cellIndex < row.cells.length; cellIndex++) {
                            var cell = row.cells[cellIndex];
                            var input = cell.childNodes[0];
                            if (input && input.value !== undefined) {
                                console.log("Cell " + cellIndex + " has input with value: " + input.value);
                            } else {
                                console.log("Cell " + cellIndex + " has no input or input has no value");
                            }
                        }

                        var attraction = row.cells[2].childNodes[0] ? row.cells[2].childNodes[0].value : '';
                        var program = row.cells[3].childNodes[0] ? row.cells[3].childNodes[0].value : '';
                        var stay = row.cells[4].childNodes[0] ? row.cells[4].childNodes[0].value : '';
                        var meal_plan = row.cells[5].childNodes[0] ? row.cells[5].childNodes[0].value : '';
                        var package_p_id = row.cells[7].childNodes[0] ? row.cells[7].childNodes[0].value : '';

                        console.log("Extracted values - attraction: '" + attraction + "', program: '" + program + "', stay: '" + stay + "', meal_plan: '" + meal_plan + "'");

                        if (attraction && program && stay) {
                            console.log("Adding data to arrays");
                            attraction_arr.push(attraction);
                            program_arr.push(program);
                            stay_arr.push(stay);
                            meal_plan_arr.push(meal_plan);
                            package_p_id_arr.push(package_p_id);

                            // Get image data - check existing image path first
                            var img = '';
                            var existingImgInput = row.querySelector('input[id^="existing_image_path_"]');
                            if (existingImgInput) {
                                img = existingImgInput.value || '';
                            }

                            var imageKey = getItineraryImageKey(row, package_id, i);
                            var dayNumber = getItineraryDayNumber(row, i);

                            // Check if new image was uploaded
                            if (window.quotationImages && window.quotationImages[imageKey]) {
                                var imageData = window.quotationImages[imageKey];
                                if (imageData.image_url) {
                                    img = imageData.image_url;
                                }
                            }

                            day_image_arr.push(img);
                            console.log("Added image for row", i, "key", imageKey, ":", img);

                            // Store image data if an image is selected (using the global storage)
                            if (window.quotationImages && window.quotationImages[imageKey]) {
                                var imageData = {
                                    package_id: package_p_id || package_id,
                                    day_number: dayNumber,
                                    file: window.quotationImages[imageKey].file,
                                    offset: imageKey
                                };

                                // Store image for later upload
                                if (!window.itineraryImages) {
                                    window.itineraryImages = [];
                                }
                                window.itineraryImages.push(imageData);
                                console.log("Collected image for day " + dayNumber + ", package " + imageData.package_id + ", file: " + imageData.file.name);
                            } else {
                                console.log("No image found for key " + imageKey);
                            }
                        } else {
                            console.log("Skipping row - missing required data");
                        }
                    } else {
                        console.log("Row " + i + " checkbox not checked, skipping");
                    }
                }
            } else {
                console.log("Table not found for package: " + package_id);
                console.log("Available tables on page:");
                $('table[id*="dynamic_table_list_p_"]').each(function() {
                    console.log("- " + this.id);
                });
            }
        });

        // Save the data if we have any
        console.log("Collected itinerary data counts - attractions: " + attraction_arr.length + ", programs: " + program_arr.length + ", stays: " + stay_arr.length);

        if (attraction_arr.length > 0) {
            console.log("Saving itinerary data:", {
                attraction_arr: attraction_arr,
                program_arr: program_arr,
                stay_arr: stay_arr,
                meal_plan_arr: meal_plan_arr,
                day_image_arr: day_image_arr,
                package_p_id_arr: package_p_id_arr,
                package_id_arr: package_id_arr
            });

            // Store in sessionStorage for later use
            sessionStorage.setItem('itinerary_data', JSON.stringify({
                attraction_arr: attraction_arr,
                program_arr: program_arr,
                stay_arr: stay_arr,
                meal_plan_arr: meal_plan_arr,
                day_image_arr: day_image_arr,
                package_p_id_arr: package_p_id_arr,
                package_id_arr: package_id_arr
            }));

            // Store in sessionStorage for later use when quotation is actually saved
            console.log('Storing itinerary data in sessionStorage for later saving');

            // Debug: Show collected images
            console.log('Total images collected:', window.itineraryImages ? window.itineraryImages.length : 0);
            if (window.itineraryImages && window.itineraryImages.length > 0) {
                window.itineraryImages.forEach(function(img, idx) {
                    console.log('Image ' + idx + ':', img.file.name, 'Day:', img.day_number, 'Package:', img.package_id);
                });
            }
        } else {
            console.log('No itinerary data collected - arrays are empty');
            console.log('This might be because no packages are selected or no itinerary data is entered');
        }
    }

    function package_save_modal() {
        var base_url = $('#base_url').val();
        window.href = base_url + 'view/custom_packages/master/package/index.php';
    }

    function isAiQuotationMode() {
        return $('#aiBuilder').is(':checked')
            || $('#is_ai_quotation').val() === '1'
            || sessionStorage.getItem('is_ai_quotation') === '1';
    }

    function getQuotationDestinationId() {
        return $('#dest_name').val()
            || sessionStorage.getItem('selected_destination_id')
            || sessionStorage.getItem('quotation_dest_id')
            || $('#quotation_dest_id').val()
            || '';
    }

    function syncQuotationReferId() {
        var dest_id = getQuotationDestinationId();
        var referMap = {};
        try {
            referMap = JSON.parse($('#quotation_refer_id_map').val() || '{}');
        } catch (e) {
            referMap = {};
        }
        var refer_id = (dest_id && referMap[dest_id]) ? referMap[dest_id] : 0;
        $('#quotation_refer_id').val(refer_id);
        sessionStorage.setItem('quotation_refer_id', refer_id);
        return refer_id;
    }

    function getQuotationEditorContent(textareaId) {
        var $target = $('#' + textareaId);
        if (!$target.length) {
            return '';
        }
        if ($target.data('wysiwyg')) {
            return $target.wysiwyg('getContent') || '';
        }
        var iframe = document.getElementById(textareaId + '-wysiwyg-iframe');
        if (iframe && iframe.contentWindow && iframe.contentWindow.document && iframe.contentWindow.document.body) {
            return iframe.contentWindow.document.body.innerHTML || '';
        }
        return $target.val() || '';
    }

    function normalizeAiList(items) {
        if (Array.isArray(items)) {
            return items.filter(function(item) {
                return item !== null && item !== undefined && String(item).trim() !== '';
            });
        }
        if (typeof items === 'string' && items.trim() !== '') {
            return [items.trim()];
        }
        if (items && typeof items === 'object') {
            return Object.values(items).filter(function(item) {
                return item !== null && item !== undefined && String(item).trim() !== '';
            });
        }
        return [];
    }

    function buildAiListHtml(items) {
        var list = normalizeAiList(items);
        if (!list.length) {
            return '';
        }
        return '<ul>' + list.map(function(item) {
            return '<li>' + $('<div/>').text(item).html() + '</li>';
        }).join('') + '</ul>';
    }

    function setWysiwygContent(textareaId, items) {
        var html = buildAiListHtml(items);
        var list = normalizeAiList(items);
        var $target = $('#' + textareaId);
        if (!$target.length) {
            return false;
        }
        if ($target.data('wysiwyg')) {
            $target.wysiwyg('setContent', html);
        } else {
            var iframe = document.getElementById(textareaId + '-wysiwyg-iframe');
            if (iframe && iframe.contentWindow && iframe.contentWindow.document && iframe.contentWindow.document.body) {
                iframe.contentWindow.document.body.innerHTML = html;
            } else {
                $target.val(list.join('\n'));
            }
        }
        $target.trigger('change');
        return list.length > 0;
    }

    function fillAiInclusionsExclusions(itinerary) {
        if (!itinerary) {
            return;
        }
        setWysiwygContent('inclusions_ai', itinerary.inclusions);
        setWysiwygContent('exclusions_ai', itinerary.exclusions);
    }

    function collectAiItineraryArrays() {
        var attraction_arr = [], program_arr = [], stay_arr = [], meal_plan_arr = [];
        $('#aiItineraryContainer .ai-itinerary-row').each(function() {
            var $row = $(this);
            if (!$row.find('.ai-program-check').is(':checked')) {
                return;
            }
            var attraction = ($row.find('.ai-special').val() || '').trim();
            var program = ($row.find('.ai-day-program').val() || '').trim();
            var stay = ($row.find('.ai-stay').val() || '').trim();
            var meal_plan = $row.find('.ai-meal').val() || '';
            if (meal_plan === 'Meal Plan') {
                meal_plan = '';
            }
            if (!attraction && !program && !stay) {
                return;
            }
            if (attraction && program && stay) {
                attraction_arr.push(attraction);
                program_arr.push(program);
                stay_arr.push(stay);
                meal_plan_arr.push(meal_plan);
            }
        });
        return {
            attraction_arr: attraction_arr,
            program_arr: program_arr,
            stay_arr: stay_arr,
            meal_plan_arr: meal_plan_arr
        };
    }

    function hasAiItineraryContent() {
        var hasContent = false;
        $('#aiItineraryContainer .ai-itinerary-row').each(function() {
            if (!$(this).find('.ai-program-check').is(':checked')) {
                return;
            }
            var attraction = ($(this).find('.ai-special').val() || '').trim();
            var program = ($(this).find('.ai-day-program').val() || '').trim();
            var stay = ($(this).find('.ai-stay').val() || '').trim();
            if (attraction || program || stay) {
                hasContent = true;
                return false;
            }
        });
        return hasContent;
    }

    function hasValidAiItinerary() {
        var data = collectAiItineraryArrays();
        return data.program_arr.length > 0;
    }

    function storeAiItinerarySession() {
        var data = collectAiItineraryArrays();
        var dest_id = getQuotationDestinationId();
        var refer_id = syncQuotationReferId();
        var inclusions = getQuotationEditorContent('inclusions_ai');
        var exclusions = getQuotationEditorContent('exclusions_ai');
        sessionStorage.setItem('is_ai_quotation', '1');
        sessionStorage.setItem('quotation_dest_id', dest_id);
        $('#is_ai_quotation').val('1');
        $('#quotation_dest_id').val(dest_id);
        var packageIdsForTab3 = refer_id && String(refer_id) !== '0' ? [String(refer_id)] : ['0'];
        sessionStorage.setItem('selected_packages_tab3', JSON.stringify(packageIdsForTab3));
        sessionStorage.setItem('itinerary_data', JSON.stringify({
            attraction_arr: data.attraction_arr,
            program_arr: data.program_arr,
            stay_arr: data.stay_arr,
            meal_plan_arr: data.meal_plan_arr,
            day_image_arr: [],
            package_p_id_arr: [],
            package_id_arr: ['0'],
            inclusions: inclusions,
            exclusions: exclusions
        }));
        return data;
    }

    function proceedToTab3FromAi() {
        storeAiItinerarySession();
        saveItineraryData();
            if (typeof syncQuotationTravelStayDates === 'function') {
                syncQuotationTravelStayDates({ preserveHotelDates: true });
            }
        $('#tab2_head').addClass('done');
        $('#tab3_head').addClass('active');
        $('.bk_tab').removeClass('active');
        $('#tab3').addClass('active');
        $('html, body').animate({
            scrollTop: $('.bk_tab_head').offset().top
        }, 200);
    }

    var aiItineraryMealOptions = [
        { value: '', text: 'Meal Plan' },
        { value: 'Breakfast', text: 'Breakfast' },
        { value: 'Lunch', text: 'Lunch' },
        { value: 'Dinner', text: 'Dinner' },
        { value: 'B+L', text: 'B+L' },
        { value: 'B+D', text: 'B+D' },
        { value: 'L+D', text: 'L+D' },
        { value: 'B+L+D', text: 'B+L+D' },
        { value: 'Room Only', text: 'Room Only' },
        { value: 'No Meals', text: 'No Meals' },
        { value: 'All Inclusive', text: 'All Inclusive' }
    ];

    function buildAiMealSelect(selectedMeal) {
        var $meal = $('<select class="ai-field ai-meal"></select>');
        aiItineraryMealOptions.forEach(function(opt) {
            $meal.append($('<option></option>').val(opt.value).text(opt.text));
        });
        if (selectedMeal) {
            if ($meal.find('option').filter(function() { return $(this).val() === selectedMeal; }).length) {
                $meal.val(selectedMeal);
            } else {
                $meal.append($('<option></option>').val(selectedMeal).text(selectedMeal).prop('selected', true));
            }
        }
        return $meal;
    }

    function addAiItineraryRow(data) {
        data = data || {};
        if (typeof window.aiItineraryRowCounter === 'undefined') {
            window.aiItineraryRowCounter = 0;
        }
        window.aiItineraryRowCounter++;
        var rowIndex = window.aiItineraryRowCounter;
        var checkboxId = 'chk_ai_program_' + rowIndex;
        var isVisible = $('#aiBuilder').is(':checked') || $('#aiChatBox').hasClass('show');

        var $row = $('<div class="ai-itinerary-row"></div>')
            .attr('data-ai-row', rowIndex)
            .attr('aria-hidden', isVisible ? 'false' : 'true');
        if (isVisible) {
            $row.addClass('show');
        }

        var $checkbox = $('<input type="checkbox" class="css-checkbox ai-program-check">')
            .attr('id', checkboxId)
            .prop('checked', data.checked !== false);
        var $checkboxLabel = $('<label class="css-label"></label>').attr('for', checkboxId);
        var $checkWrap = $('<span class="ai-check-wrap"></span>').append($checkbox).append($checkboxLabel);

        var $special = $('<input type="text" class="ai-field ai-special" placeholder="*Special Attraction">')
            .val(data.attraction || '');
        var $program = $('<textarea class="ai-field ai-day-program" placeholder="*Day-wise Program"></textarea>')
            .val(data.program || '');
        var $stay = $('<input type="text" class="ai-field ai-stay" placeholder="*Overnight Stay">')
            .val(data.stay || '');
        var $meal = buildAiMealSelect(data.meal || '');
        var $plusBtn = $('<button type="button" class="ai-plus-btn" aria-label="Add itinerary">+</button>');

        $row.append($checkWrap, $special, $program, $stay, $meal, $plusBtn);
        $('#aiItineraryContainer').append($row);
        return $row;
    }

    function initAiItineraryContainer() {
        if ($('#aiItineraryContainer .ai-itinerary-row').length === 0) {
            window.aiItineraryRowCounter = 0;
            addAiItineraryRow({ checked: true });
        }
    }

    function cloneAiItineraryRow() {
        addAiItineraryRow({ checked: true });
    }

    function fillAiItineraryRows(programs) {
        if (!Array.isArray(programs) || !programs.length) {
            return;
        }
        $('#aiItineraryContainer').empty();
        window.aiItineraryRowCounter = 0;
        programs.forEach(function(item) {
            addAiItineraryRow({
                attraction: item.special_attraction || item.attraction || '',
                program: item.day_wise_program || item.program || '',
                stay: item.overnight_stay || item.stay || '',
                meal: item.meal_plan || '',
                checked: true
            });
        });
        $('#aiChatBox').addClass('show').attr('aria-hidden', 'false');
        $('#aiItineraryContainer .ai-itinerary-row').addClass('show').attr('aria-hidden', 'false');
    }

    $(document).on('click', '.ai-plus-btn', function(e) {
        e.preventDefault();
        cloneAiItineraryRow();
    });

    initAiItineraryContainer();

    $('#btnAnalyseMessage').on('click', function() {
        var message = ($('#aiMessageInput').val() || '').trim();
        var base_url = $('#base_url').val();
        if (!message) {
            $('#aiApiInfo').text('Please paste quotation or itinerary text.');
            return;
        }
        $('#aiApiInfo').text('Please Wait Analysing...');
        $('#btnAnalyseMessage').prop('disabled', true);
        $.ajax({
            type: 'post',
            url: base_url + 'controller/gemini/gemini.php',
            dataType: 'json',
            data: { text: message },
            success: function(response) {
                if (response && response.error) {
                    $('#aiApiInfo').text(response.error);
                    return;
                }
                if (!(response && response.status)) {
                    $('#aiApiInfo').text((response && (response.error || response.Error)) ? (response.error || response.Error) : 'Failed to analyse message.');
                    return;
                }
                var parsed = null;
                try {
                    parsed = JSON.parse(response.reply || '{}');
                } catch (e) {
                    parsed = response.raw || null;
                }
                if (!parsed || parsed.Error) {
                    $('#aiApiInfo').text((parsed && parsed.Error) ? parsed.Error : 'Invalid AI response.');
                    return;
                }
                var programs = parsed.itinerary && parsed.itinerary.detailed_program ? parsed.itinerary.detailed_program : [];
                fillAiItineraryRows(programs);
                fillAiInclusionsExclusions(parsed.itinerary);
                var hasInclExcl = normalizeAiList(parsed.itinerary && parsed.itinerary.inclusions).length ||
                    normalizeAiList(parsed.itinerary && parsed.itinerary.exclusions).length;
                var statusMsg = programs.length ? 'Itinerary generated successfully.' : 'No itinerary rows found in AI response.';
                if (hasInclExcl) {
                    statusMsg += ' Inclusions and exclusions populated.';
                }
                $('#aiApiInfo').text(statusMsg);
            },
            error: function(xhr) {
                $('#aiApiInfo').text((xhr && xhr.responseText) ? xhr.responseText : 'Request failed.');
            },
            complete: function() {
                $('#btnAnalyseMessage').prop('disabled', false);
            }
        });
    });

    $('#frm_tab2').validate({

        rules: {

        },

        submitHandler: function(form, e) {
            e.preventDefault();
            var base_url = $('#base_url').val();

            var incl_arr = new Array();
            var excl_arr = new Array();
            var package_id_arr = new Array();

            $('input[name="custom_package"]:checked').each(function() {

                package_id_arr.push($(this).val());
                var package_id = $(this).val();
                //Incl & Excl
                var table = document.getElementById("dynamic_table_incl" + package_id);
                var rowCount = table.rows.length;
                for (var i = 0; i < rowCount; i++) {
                    var row = table.rows[i];
                    var inclusion = $('#inclusions' + package_id).val();
                    var exclusion = $('#exclusions' + package_id).val();

                    incl_arr.push(inclusion);
                    excl_arr.push(exclusion);
                }

            });
            var isAiMode = isAiQuotationMode();

            if (isAiMode && package_id_arr.length == 0) {
                var aiRowError = '';
                $('#aiItineraryContainer .ai-itinerary-row').each(function(index) {
                    if (!$(this).find('.ai-program-check').is(':checked')) {
                        return;
                    }
                    var attraction = ($(this).find('.ai-special').val() || '').trim();
                    var program = ($(this).find('.ai-day-program').val() || '').trim();
                    var stay = ($(this).find('.ai-stay').val() || '').trim();
                    if (!attraction && !program && !stay) {
                        return;
                    }
                    if (!attraction || !program || !stay) {
                        aiRowError = 'Special Attraction, Daywise Program, and Overnight Stay are mandatory in AI row ' + (index + 1);
                        return false;
                    }
                });
                if (aiRowError) {
                    error_msg_alert(aiRowError);
                    return false;
                }
                var aiData = collectAiItineraryArrays();
                if (aiData.program_arr.length === 0) {
                    error_msg_alert('Please enter at least one complete AI itinerary row.');
                    return false;
                }
                var dest_id = getQuotationDestinationId();
                if (!dest_id) {
                    error_msg_alert('Please select destination for AI quotation.');
                    return false;
                }
                proceedToTab3FromAi();
                return false;
            }

            if (!isAiMode) {
                sessionStorage.removeItem('is_ai_quotation');
                sessionStorage.removeItem('quotation_dest_id');
                sessionStorage.removeItem('quotation_refer_id');
                $('#is_ai_quotation').val('0');
                $('#quotation_dest_id').val('');
                $('#quotation_refer_id').val('0');
            }

            if (package_id_arr.length == 0 && !isAiMode) {
                error_msg_alert('Please select at least one Package!');
                return false;
            }

            var attraction_arr = new Array();
            var program_arr = new Array();
            var stay_arr = new Array();
            var meal_plan_arr = new Array();
            var package_p_id_arr = new Array();
            var day_count_arr = new Array();
            var count = 0;

            for (var j = 0; j < package_id_arr.length; j++) {
                var table = document.getElementById("dynamic_table_list_p_" + package_id_arr[j]);
                var rowCount = table.rows.length;
                for (var i = 0; i < rowCount; i++) {
                    var row = table.rows[i];
                    if (row.cells[0].childNodes[0].checked) {

                        count++;
                        var attraction = row.cells[2].childNodes[0].value;
                        var program = row.cells[3].childNodes[0].value;
                        var stay = row.cells[4].childNodes[0].value;
                        var meal_plan = row.cells[5].childNodes[0].value;
                        var package_id1 = row.cells[7].childNodes[0].value;

                        if (attraction == "") {
                            error_msg_alert('Special Attraction is mandatory in row' + (i + 1));
                            return false;
                        }
                        if (program == "") {
                            error_msg_alert('Daywise program is mandatory in row' + (i + 1));
                            return false;
                        }
                        if (stay == "") {
                            error_msg_alert('Overnight Stay is mandatory in row' + (i + 1));
                            return false;
                        }

                        var flag1 = validate_spattration(row.cells[2].childNodes[0].id);
                        var flag2 = validate_dayprogram(row.cells[3].childNodes[0].id);
                        var flag3 = validate_onstay(row.cells[4].childNodes[0].id);
                        if (!flag1 || !flag2 || !flag3) {
                            return false;
                        }
                        attraction_arr.push(attraction);
                        program_arr.push(program);
                        stay_arr.push(stay);
                        meal_plan_arr.push(meal_plan);
                        package_p_id_arr.push(package_id1);
                    }
                }
                day_count_arr.push(count);
                count = 0;
            }

            var total_adult = $('#total_adult').val();
            var total_children = $('#total_children').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            var total_days = $('#total_days').val();


            sessionStorage.setItem('selected_packages_tab3', JSON.stringify(package_id_arr));
            sessionStorage.removeItem('hotel_table_state_tab3');
            window.quotationTab2LoadState = { hotelDone: false, transportDone: false };

            $.ajax({

                type: 'post',

                url: '../save/package_hotel_info.php',

                data: {
                    package_id_arr: package_id_arr,
                    from_date: from_date
                },

                success: function(result) {

                    var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
                    var hotel_arr = JSON.parse(result);
                    var defaultPackageType = typeof quotationPrepareHotelTableForTab2Reload === 'function'
                        ? quotationPrepareHotelTableForTab2Reload(table)
                        : 'ECONOMY';

                    window.quotationFreshPackageLoad = true;
                    window.quotationBatchPopulatingHotels = true;
                    while (table.rows.length < hotel_arr.length) {
                        addRow('tbl_package_tour_quotation_dynamic_hotel');
                    }
                    for (var ri = 0; ri < table.rows.length; ri++) {
                        table.rows[ri].cells[1].childNodes[0].value = ri + 1;
                    }

                    function afterTab2HotelPopulate() {
                        window.quotationFreshPackageLoad = false;
                        window.quotationBatchPopulatingHotels = false;
                        if (typeof hideHotelPackage === 'function') {
                            hideHotelPackage(defaultPackageType);
                        }
                        if (typeof quotationSyncTravelStaySectionsFromHotels === 'function') {
                            quotationSyncTravelStaySectionsFromHotels();
                        } else if (typeof syncQuotationTravelStayDates === 'function') {
                            syncQuotationTravelStayDates();
                        }
                        if (window.quotationTab2LoadState) {
                            window.quotationTab2LoadState.hotelDone = true;
                        }
                        if (typeof quotationFinishTab2ToTab3 === 'function') {
                            quotationFinishTab2ToTab3();
                        }
                    }

                    if (typeof populateQuotationHotelRowsSequential === 'function') {
                        populateQuotationHotelRowsSequential(table, hotel_arr, {
                            freshPackageLoad: true,
                            packageTypeToAdd: defaultPackageType,
                            onComplete: afterTab2HotelPopulate
                        });
                    } else {
                        for (var i = 0; i < hotel_arr.length; i++) {
                            var row = table.rows[i];
                            var hotelData = hotel_arr[i];
                            row.cells[1].childNodes[0].value = (i + 1);
                            var $hotelSelect = $(row.cells[4].childNodes[0]);
                            if (typeof setQuotationCitySelect === 'function') {
                                setQuotationCitySelect(row.cells[3].childNodes[0], hotelData['city_id'], hotelData['city_name']);
                            } else {
                                city_lzloading(row.cells[3].childNodes[0]);
                                var $citySelect = $(row.cells[3].childNodes[0]);
                                var newOption = $("<option selected='selected'></option>").val(hotelData['city_id']).text(hotelData['city_name']);
                                $citySelect.append(newOption).trigger('change.select2');
                            }
                            if (typeof loadQuotationHotelFromPackage === 'function') {
                                loadQuotationHotelFromPackage(hotelData, $hotelSelect);
                            } else if (hotelData['city_id'] && typeof hotelDropdownLoadByCity === 'function') {
                                (function (data, $hotel) {
                                    hotelDropdownLoadByCity(data['city_id'], $hotel, function (success) {
                                        if (success && data['hotel_id1']) {
                                            if (typeof selectHotelInDropdown === 'function') {
                                                selectHotelInDropdown($hotel, data['hotel_id1'], data['hotel_name']);
                                            } else {
                                                $hotel.val(data['hotel_id1']).trigger('change');
                                            }
                                        }
                                    });
                                })(hotelData, $hotelSelect);
                            } else {
                                $hotelSelect.html('<option value="' + hotelData['hotel_id1'] + '">' + hotelData['hotel_name'] + '</option>');
                                $('#' + row.cells[4].childNodes[0].id).select2().trigger("change");
                            }
                            row.cells[6].childNodes[0].value = hotelData['check_in_date'];
                            row.cells[7].childNodes[0].value = hotelData['check_out_date'];
                            row.cells[8].childNodes[0].value = hotelData['hotel_type'];
                            if (typeof quotationComputeNightsFromDates === 'function') {
                                var hotelNights = quotationComputeNightsFromDates(hotelData['check_in_date'], hotelData['check_out_date']);
                                row.cells[9].childNodes[0].value = hotelNights > 0 ? hotelNights : (hotelData['total_days'] || '');
                            } else {
                                row.cells[9].childNodes[0].value = hotelData['total_days'] || '';
                            }
                            row.cells[10].childNodes[0].value = '';
                            row.cells[12].childNodes[0].value = hotelData['package_name'];
                            row.cells[14].childNodes[0].value = hotelData['package_id'];

                            $(row.cells[2].childNodes[0]).val(defaultPackageType).trigger('change.select2');
                            document.getElementById(row.cells[5].childNodes[0].id).selectedIndex = 0;
                            $('#' + row.cells[5].childNodes[0].id).select2().trigger("change");
                            calculate_total_nights(row.cells[7].childNodes[0].id);
                        }
                        afterTab2HotelPopulate();
                    }
                }
            });

            //Transport Info
            $from_date = $('#from_date').val();
            $to_date = $('#to_date').val();
            $.ajax({
                type: 'post',
                url: '../save/package_transport_info.php',
                data: {
                    package_id_arr: package_id_arr,
                    from_date: from_date,
                    total_adult: total_adult
                },
                success: function(result) {
                    var table = document.getElementById(
                        "tbl_package_tour_quotation_dynamic_transport");
                    while (table.rows.length > 1) {
                        table.deleteRow(table.rows.length - 1);
                    }
                    var transport_arr = JSON.parse(result);
                    if (table.rows.length != transport_arr.length) {
                        for (var i = 0; i < transport_arr.length - 1; i++) {
                            addRow('tbl_package_tour_quotation_dynamic_transport');
                        }
                    }
                    for (var i = 0; i < transport_arr.length; i++) {

                        var row = table.rows[i];
                        row.cells[0].childNodes[0].checked = true;
                        row.cells[1].childNodes[0].value = (i + 1);
                        row.cells[2].childNodes[0].value = transport_arr[i]['bus_id'];

                        row.cells[3].childNodes[0].value = transport_arr[i]['start_date'] || $from_date;
                        row.cells[4].childNodes[0].value = transport_arr[i]['end_date'] || $to_date;
                        $(row.cells[5].childNodes[0]).prepend('<optgroup value=' + transport_arr[i][
                                'pickup_type'
                            ] + ' label="' + (transport_arr[i]['pickup_type']).charAt(0)
                            .toUpperCase() + (transport_arr[i]['pickup_type']).slice(1) +
                            ' Name"><option value="' + transport_arr[i]['pickup_type'] + '-' +
                            transport_arr[i]['pickup_id'] + '">' + transport_arr[i]['pickup'] +
                            '</option></optgroup>');
                        document.getElementById(row.cells[5].childNodes[0].id).value =
                            transport_arr[i]['pickup_type'] + '-' + transport_arr[i]['pickup_id'];

                        $(row.cells[6].childNodes[0]).prepend('<optgroup value=' + transport_arr[i][
                                'drop_type'
                            ] + ' label="' + (transport_arr[i]['drop_type']).charAt(0)
                            .toUpperCase() + (transport_arr[i]['drop_type']).slice(1) +
                            ' Name"><option value="' + transport_arr[i]['drop_type'] + '-' +
                            transport_arr[i]['drop_id'] + '">' + transport_arr[i]['drop'] +
                            '</option></optgroup>');
                        document.getElementById(row.cells[6].childNodes[0].id).value =
                            transport_arr[i]['drop_type'] + '-' + transport_arr[i]['drop_id'];
                        row.cells[8].childNodes[0].value = transport_arr[i]['total_vehicles'];
                        row.cells[9].childNodes[0].value = transport_arr[i]['total_cost'];
                        row.cells[10].childNodes[0].value = transport_arr[i]['package_name'];
                        row.cells[11].childNodes[0].value = transport_arr[i]['package_id'];
                        row.cells[12].childNodes[0].value = transport_arr[i]['pickup_type'];
                        row.cells[13].childNodes[0].value = transport_arr[i]['drop_type'];

                        $('#' + row.cells[2].childNodes[0].id).select2().trigger("change");
                        $('#' + row.cells[5].childNodes[0].id).select2().trigger("change");
                        $('#' + row.cells[6].childNodes[0].id).select2().trigger("change");
                        $('#' + row.cells[7].childNodes[0].id).select2().trigger("change");
                        destinationLoading($(row.cells[5].childNodes[0]), 'Pickup Location');
                        destinationLoading($(row.cells[6].childNodes[0]), 'Drop-off Location');
                    }
                    if (window.quotationTab2LoadState) {
                        window.quotationTab2LoadState.transportDone = true;
                    }
                    if (typeof quotationFinishTab2ToTab3 === 'function') {
                        quotationFinishTab2ToTab3();
                    }
                },
                error: function() {
                    if (window.quotationTab2LoadState) {
                        window.quotationTab2LoadState.transportDone = true;
                    }
                    if (typeof quotationFinishTab2ToTab3 === 'function') {
                        quotationFinishTab2ToTab3();
                    }
                }
            });
            //Activity auto fetch pax count
            var table = document.getElementById("tbl_package_tour_quotation_dynamic_excursion");
            var rowCount = table.rows.length;
            var children_with_bed = $('#children_with_bed').val();
            var children_without_bed = $('#children_without_bed').val();
            var total_infant = $('#total_infant').val();

            for (var i = 0; i < rowCount; i++) {
                var row = table.rows[i];
                row.cells[6].childNodes[0].value = total_adult;
                row.cells[7].childNodes[0].value = children_with_bed;
                row.cells[8].childNodes[0].value = children_without_bed;
                row.cells[9].childNodes[0].value = total_infant;
            }

            //Selected Packages days reflect
            var dest_id = $('#dest_name').val();
            $.ajax({
                type: 'post',
                url: '../../inc/get_packages_days.php',
                data: {
                    dest_id: dest_id,
                    day_count_arr: day_count_arr,
                    package_id_arr: package_id_arr
                },
                success: function(result) {
                    $('#daywise_image_select').html(result);
                },
                error: function(result) {
                    console.log(result.responseText);
                }
            });

        }

    });

    function quotationFinishTab2ToTab3() {
        var state = window.quotationTab2LoadState;
        if (!state || !state.hotelDone || !state.transportDone) {
            return;
        }
        window.quotationTab2LoadState = null;

        if (typeof get_hotel_cost === 'function') {
            get_hotel_cost();
        }
        if (typeof get_excursion_amount === 'function') {
            get_excursion_amount();
        }
        if (typeof get_transport_cost === 'function') {
            get_transport_cost();
        }
        if (typeof saveItineraryData === 'function') {
            saveItineraryData();
        }
        if (typeof syncQuotationTravelStayDates === 'function') {
            syncQuotationTravelStayDates();
        }

        $('#tab2_head').addClass('done');
        $('#tab3_head').addClass('active');
        $('.bk_tab').removeClass('active');
        $('#tab3').addClass('active');
        $('html, body').animate({
            scrollTop: $('.bk_tab_head').offset().top
        }, 200);
    }

        // Load stored values from Tab 1 when Tab 2 loads
    $(document).ready(function() {
        console.log("Tab 2 loading - checking for stored destination and nights...");
        
        var tab1Destination = ($('#tour_name').val() || '').trim();
        var storedDestId = sessionStorage.getItem('selected_destination_id');
        var storedDestName = sessionStorage.getItem('selected_destination_name');

        // Discard stale session data when Tab1 destination does not match stored destination
        if (tab1Destination && storedDestName && tab1Destination !== storedDestName) {
            console.log("Stale destination in sessionStorage - clearing:", storedDestName, "vs Tab1:", tab1Destination);
            sessionStorage.removeItem('selected_destination_id');
            sessionStorage.removeItem('selected_destination_name');
            storedDestId = null;
            storedDestName = null;
            if (typeof window.__quotationResetPackageCache === 'function') {
                window.__quotationResetPackageCache();
            }
            if (typeof clearQuotationPackageListUi === 'function') {
                clearQuotationPackageListUi();
            }
        }
        
        // Get total days from tab1 as primary source for nights
        var total_days = $('#total_days').val();
        var storedNights = total_days || sessionStorage.getItem('selected_nights');
        
        console.log("Total days from tab1:", total_days);
        console.log("Stored destination ID:", storedDestId);
        console.log("Stored destination name:", storedDestName);
        console.log("Stored nights:", storedNights);
        
        // Debug: Check all sessionStorage items
        console.log("All sessionStorage items:");
        for (var i = 0; i < sessionStorage.length; i++) {
            var key = sessionStorage.key(i);
            console.log(key + ":", sessionStorage.getItem(key));
        }
        
        // Try to get destination from Tab1 input field as fallback
        console.log("Tab1 destination input value:", tab1Destination);
        
        // If no stored destination but Tab1 has a value, try to find the destination ID
        if (!storedDestId && tab1Destination) {
            console.log("No stored destination ID, but Tab1 has destination name:", tab1Destination);
            // Try to find the destination ID by matching the name
            var destinations = JSON.parse($('#destinations').val() || '[]');
            console.log("Available destinations:", destinations);
            
            for (var i = 0; i < destinations.length; i++) {
                if (destinations[i].label === tab1Destination) {
                    storedDestId = destinations[i].dest_id;
                    storedDestName = destinations[i].label;
                    console.log("Found matching destination:", storedDestId, storedDestName);
                    // Store it for future use
                    sessionStorage.setItem('selected_destination_id', storedDestId);
                    sessionStorage.setItem('selected_destination_name', storedDestName);
                    break;
                }
            }
        }
        
        // Set destination if available
        if (storedDestId && storedDestName) {
            console.log("Setting destination dropdown to:", storedDestId, "(", storedDestName, ")");
            
            // Ensure select2 is initialized before setting value
            if ($('#dest_name').hasClass('select2-hidden-accessible')) {
                console.log("Select2 already initialized, setting value directly");
                $('#dest_name').val(storedDestId).trigger('change');
            } else {
                console.log("Select2 not initialized, initializing first");
                $('#dest_name').select2();
                setTimeout(function() {
                    $('#dest_name').val(storedDestId).trigger('change');
                    console.log("Destination set after select2 init. Current value:", $('#dest_name').val());
                }, 100);
            }
            
            console.log("Destination set successfully. Current value:", $('#dest_name').val());
        } else {
            console.log("No stored destination found in sessionStorage or Tab1 input");
        }
        
        // Set nights if available - prioritize total_days from tab1
        if (total_days && total_days > 0) {
            $('#nights_filter').val(total_days);
            sessionStorage.setItem('selected_nights', total_days);
            console.log("Set nights filter to total_days from tab1:", total_days);
        } else if (storedNights && storedNights > 0) {
            $('#nights_filter').val(storedNights);
            console.log("Set nights filter to stored value:", storedNights);
        }
        
        // Load packages with the stored filters
        if (storedDestId) {
            console.log("Loading packages with destination:", storedDestId, "and nights:", storedNights);
            setTimeout(function() {
                console.log("Calling load_packages_with_filter()...");
                if (typeof load_packages_with_filter === 'function') {
                    load_packages_with_filter(true);
                } else {
                    console.error("load_packages_with_filter function not found");
                }
            }, 1000); // Increased delay to ensure everything is loaded
        } else {
            console.log("No stored destination found, not loading packages automatically");
        }
    });
</script>

<!-- Image Zoom Functionality -->
<style>
    .image-zoom-container {
        position: relative;
        display: inline-block;
        overflow: hidden;
        border-radius: 8px;
        cursor: zoom-in;
    }
    
    .image-zoom-container img {
        transition: transform 0.3s ease;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .image-zoom-container:hover img {
        transform: scale(1.5);
    }
    
    .image-zoom-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
        cursor: zoom-out;
    }
    
    .image-zoom-overlay img {
        max-width: 90%;
        max-height: 90%;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }
    
    .image-zoom-close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 30px;
        cursor: pointer;
        z-index: 10000;
    }
    
    .image-zoom-close:hover {
        color: #ff6b6b;
    }
</style>

<script>
    // Image zoom functionality
    function initImageZoom() {
        // Add zoom functionality to all existing images
        $('.image-zoom-container img').off('click').on('click', function(e) {
            e.preventDefault();
            showImageZoom($(this).attr('src'));
        });
    }
    
    function showImageZoom(imageSrc) {
        // Create overlay
        var overlay = $('<div class="image-zoom-overlay">' +
            '<span class="image-zoom-close">&times;</span>' +
            '<img src="' + imageSrc + '" alt="Zoomed Image">' +
            '</div>');
        
        $('body').append(overlay);
        overlay.fadeIn(300);
        
        // Close on click
        overlay.on('click', function(e) {
            if (e.target === this || $(e.target).hasClass('image-zoom-close')) {
                overlay.fadeOut(300, function() {
                    overlay.remove();
                });
            }
        });
        
        // Close on escape key
        $(document).on('keyup.imageZoom', function(e) {
            if (e.keyCode === 27) { // Escape key
                overlay.fadeOut(300, function() {
                    overlay.remove();
                });
                $(document).off('keyup.imageZoom');
            }
        });
    }
    
    // Initialize zoom when packages are loaded
    $(document).ready(function() {
        // Initialize zoom for existing images
        initImageZoom();
        
        // Re-initialize zoom when new content is loaded
        $(document).on('DOMNodeInserted', function() {
            setTimeout(function() {
                initImageZoom();
            }, 100);
        });
    });
    
    // Function to wrap images with zoom container
    function wrapImagesWithZoom() {
        $('img[id^="preview_img_"]').each(function() {
            if (!$(this).parent().hasClass('image-zoom-container')) {
                $(this).wrap('<div class="image-zoom-container"></div>');
            }
        });
    }
    
    // Call wrapImagesWithZoom when packages are loaded
    $(document).ajaxComplete(function() {
        setTimeout(function() {
            wrapImagesWithZoom();
            initImageZoom();
        }, 500);
    });
    function package_booking_reflect() {
        $('.app_dual_button').removeClass('active');
        $('input[name="quotation_package"]:checked').closest('label').addClass('active');

        var id = $('input[name="quotation_package"]:checked').attr('id');
        var wasAiMode = $('#is_ai_quotation').val() === '1' || sessionStorage.getItem('is_ai_quotation') === '1';

        if (id == "standardPackage") {
            $('#package_div_content').show();
            $('#nights_filter').closest('.col-md-3').show();
            $('#package_name_div').show();
            $('#package_div_content > .col-md-6.text-right').show();
            $('#ai_chat_container').hide();
            $('#is_ai_quotation').val('0');
            sessionStorage.removeItem('is_ai_quotation');

            if (wasAiMode && $('#dest_name').val() && typeof load_packages_with_filter === 'function') {
                load_packages_with_filter(true);
            }
        }
        if (id == "aiBuilder") {
            if (typeof resetPackageSelectorRadios === 'function') {
                resetPackageSelectorRadios();
            }
            if (typeof clearQuotationPackageListUi === 'function') {
                clearQuotationPackageListUi();
            }
            if (typeof quotationResetPackageLoadCache === 'function') {
                quotationResetPackageLoadCache();
            }

            $('#package_div_content').show();
            $('#nights_filter').closest('.col-md-3').hide();
            $('#package_name_div').hide();
            $('#package_div_content > .col-md-6.text-right').hide();
            $('#ai_chat_container').show();
            $('#aiChatBox').addClass('show').attr('aria-hidden', 'false');
            $('#aiItineraryContainer .ai-itinerary-row').addClass('show').attr('aria-hidden', 'false');
            $('#is_ai_quotation').val('1');
            sessionStorage.setItem('is_ai_quotation', '1');
            if (!$('#dest_name').val() && typeof syncDestinationFromTab1 === 'function') {
                syncDestinationFromTab1(false);
            }
        }
    }
    package_booking_reflect();


</script>